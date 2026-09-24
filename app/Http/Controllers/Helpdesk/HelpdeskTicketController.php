<?php

namespace App\Http\Controllers\Helpdesk;

use App\Http\Controllers\Controller;
use App\Models\HelpdeskCannedResponse;
use App\Models\HelpdeskDivision;
use App\Models\HelpdeskDivisionAgent;
use App\Models\HelpdeskTicket;
use App\Models\HelpdeskTicketLog;
use App\Models\HelpdeskTicketReply;
use App\Models\HelpdeskTicketTemplate;
use App\Models\User;
use App\Services\HelpdeskWorkplanService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HelpdeskTicketController extends Controller
{
    /**
     * Daftar Antrean Tiket (List & Filter)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        $isAdmin = $user->isHelpdeskAdmin();
        $isDivisionUser = $user->isHelpdeskDivisionUser();
        $isRegularUser = $user->isHelpdeskRegularUser();

        $myDivisionIds = HelpdeskDivisionAgent::where('user_id', $user->id)->pluck('division_id')->toArray();

        $query = HelpdeskTicket::with(['creator', 'division', 'assignedAgent', 'workplanTask']);

        // Tab Filter Sesuai Hak Akses
        $tab = $request->query('tab');

        if ($isRegularUser) {
            // User biasa HANYA bisa melihat tiket yang diajukan sendiri
            $tab = 'my_tickets';
            $query->where('user_id', $user->id);
        } elseif ($isDivisionUser) {
            // User divisi HANYA bisa melihat tiket yang ditujukan ke divisinya
            if (!$tab || !in_array($tab, ['my_division', 'assigned_to_me', 'my_tickets'])) {
                $tab = 'my_division';
            }

            if ($tab === 'assigned_to_me') {
                $query->where('assigned_to', $user->id);
            } elseif ($tab === 'my_tickets') {
                $query->where('user_id', $user->id);
            } else {
                // Default divisi saya
                $query->whereIn('division_id', $myDivisionIds);
            }
        } else {
            // Administrator: Memiliki akses penuh ke seluruh tiket
            if (!$tab) {
                $tab = 'all';
            }

            if ($tab === 'my_tickets') {
                $query->where('user_id', $user->id);
            } elseif ($tab === 'assigned_to_me') {
                $query->where('assigned_to', $user->id);
            } elseif ($tab === 'my_division') {
                $query->whereIn('division_id', $myDivisionIds);
            }
        }

        // Filter Pencarian (Nomor Tiket, Subject, Nama Pengaju)
        if ($search = $request->query('search')) {
            $search = trim($search);
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('creator', function ($qc) use ($search) {
                        $qc->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Filter Divisi
        if ($divisionId = $request->query('division_id')) {
            if ($isDivisionUser) {
                if (in_array($divisionId, $myDivisionIds)) {
                    $query->where('division_id', $divisionId);
                }
            } elseif ($isAdmin) {
                $query->where('division_id', $divisionId);
            }
        }

        // Filter Status
        if ($status = $request->query('status')) {
            if ($status === 'active') {
                $query->whereNotIn('status', ['resolved', 'closed']);
            } else {
                $query->where('status', $status);
            }
        }

        // Filter Prioritas
        if ($priority = $request->query('priority')) {
            $query->where('priority', $priority);
        }

        // Urutan: Prioritas Urgent & Tiket Baru di atas
        $tickets = $query->orderByRaw("CASE WHEN status = 'open' THEN 1 WHEN status = 'in_progress' THEN 2 ELSE 3 END")
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Daftar Divisi untuk filter dropdown
        if ($isAdmin) {
            $divisions = HelpdeskDivision::where('is_active', true)->orderBy('name', 'asc')->get();
        } elseif ($isDivisionUser) {
            $divisions = HelpdeskDivision::whereIn('id', $myDivisionIds)->where('is_active', true)->orderBy('name', 'asc')->get();
        } else {
            $divisions = collect();
        }

        // Hitungan per tab sesuai hak akses
        $counts = [
            'all' => $isAdmin ? HelpdeskTicket::count() : 0,
            'my_tickets' => HelpdeskTicket::where('user_id', $user->id)->count(),
            'assigned_to_me' => ($isAdmin || $isDivisionUser) ? HelpdeskTicket::where('assigned_to', $user->id)->count() : 0,
            'my_division' => !empty($myDivisionIds) ? HelpdeskTicket::whereIn('division_id', $myDivisionIds)->count() : 0,
        ];

        return view('helpdesk.tickets.index', compact('tickets', 'divisions', 'tab', 'counts', 'user', 'isAdmin', 'isDivisionUser', 'isRegularUser'));
    }

    /**
     * Form Buat Tiket Baru
     */
    public function create()
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        $divisions = HelpdeskDivision::where('is_active', true)->orderBy('name', 'asc')->get();

        $templates = HelpdeskTicketTemplate::where('is_active', true)
            ->with('division')
            ->orderBy('order_num', 'asc')
            ->orderBy('title', 'asc')
            ->get();

        $templatesGrouped = [];
        foreach ($templates as $t) {
            $group = $t->division ? $t->division->name : 'Umum / Semua Divisi';
            $templatesGrouped[$group][] = $t;
        }

        return view('helpdesk.tickets.create', compact('divisions', 'templates', 'templatesGrouped', 'user'));
    }

    /**
     * Simpan Tiket Baru
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        $request->validate([
            'division_id' => 'required|exists:helpdesk_divisions,id',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:Low,Medium,High,Urgent',
            'attachment' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,zip',
        ], [
            'division_id.required' => 'Divisi tujuan wajib dipilih.',
            'subject.required' => 'Judul kendala wajib diisi.',
            'description.required' => 'Deskripsi kendala wajib diisi.',
            'attachment.max' => 'Ukuran berkas lampiran maksimal 10MB.',
        ]);

        $division = HelpdeskDivision::findOrFail($request->input('division_id'));

        // Unggah Lampiran jika ada
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('helpdesk_attachments', 'public');
        }

        // Tentukan batas waktu SLA (due_date)
        $slaHours = $division->sla_hours ?: 24;
        $dueDate = Carbon::now()->addHours($slaHours);

        $ticket = HelpdeskTicket::create([
            'user_id' => $user->id,
            'division_id' => $division->id,
            'subject' => trim($request->input('subject')),
            'description' => trim($request->input('description')),
            'priority' => $request->input('priority', 'Medium'),
            'status' => 'open',
            'category' => $request->input('category'),
            'attachment' => $attachmentPath,
            'due_date' => $dueDate,
            'sentiment' => ($request->input('priority') === 'Urgent') ? 'Urgent' : 'Neutral',
        ]);

        // Catat Audit Log
        HelpdeskTicketLog::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'action' => 'Created',
            'details' => "Tiket dibuat oleh {$user->name} dengan prioritas {$ticket->priority}.",
            'created_at' => Carbon::now(),
        ]);

        return redirect()->route('helpdesk.tickets.show', $ticket->id)
            ->with('success', "Tiket #{$ticket->ticket_number} berhasil dibuat! Tim divisi {$division->name} akan segera merespon.");
    }

    /**
     * Halaman Detail Tiket & Ruang Chat Percakapan
     */
    public function show($id)
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        $ticket = HelpdeskTicket::with([
            'creator',
            'division',
            'assignedAgent',
            'workplanTask',
            'replies.user',
            'logs.user'
        ])->findOrFail($id);

        $myDivisionIds = HelpdeskDivisionAgent::where('user_id', $user->id)->pluck('division_id')->toArray();
        $isAgentOfDivision = in_array($ticket->division_id, $myDivisionIds);
        $isAssignedAgent = ($ticket->assigned_to === $user->id);
        $isCreator = ($ticket->user_id === $user->id);
        $isAdmin = $user->isHelpdeskAdmin();

        // Validasi Hak Akses Melihat Tiket Sesuai Peran:
        // - Admin: Boleh melihat seluruh tiket
        // - User Divisi: Boleh melihat tiket yang ditujukan ke divisinya ATAU tiket yang diajukan sendiri
        // - User Biasa: HANYA boleh melihat tiket yang diajukan sendiri
        if (!$isAdmin) {
            if ($user->isHelpdeskDivisionUser()) {
                if (!$isAgentOfDivision && !$isCreator) {
                    abort(403, 'Anda hanya dapat melihat tiket yang ditujukan ke divisi Anda atau tiket yang Anda ajukan sendiri.');
                }
            } else {
                if (!$isCreator) {
                    abort(403, 'Anda hanya dapat melihat tiket yang Anda ajukan sendiri.');
                }
            }
        }

        // Canned responses untuk divisi ini
        $cannedResponses = HelpdeskCannedResponse::where('division_id', $ticket->division_id)
            ->orWhereNull('division_id')
            ->orderBy('title', 'asc')
            ->get();

        // Daftar Agen Divisi untuk opsi transfer / delegasi
        $divisionAgents = $ticket->division->agents()->where('is_active', true)->get();

        return view('helpdesk.tickets.show', compact(
            'ticket',
            'cannedResponses',
            'divisionAgents',
            'isAgentOfDivision',
            'isAssignedAgent',
            'isCreator',
            'isAdmin',
            'user'
        ));
    }

    /**
     * Kirim Balasan / Catatan Internal Tiket
     * OTOMASI: Saat Karyawan / Agen merespon, tiket otomatis masuk ke Work Plan pada Step Progress!
     */
    public function reply(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        $ticket = HelpdeskTicket::with(['creator', 'division'])->findOrFail($id);

        $request->validate([
            'message' => 'required|string',
            'attachment' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,zip',
            'is_internal' => 'nullable|boolean',
        ], [
            'message.required' => 'Pesan balasan wajib diisi.',
            'attachment.max' => 'Ukuran berkas lampiran maksimal 10MB.',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('helpdesk_attachments', 'public');
        }

        $isInternal = (bool)$request->input('is_internal', false);

        // Hanya agen / admin yang boleh membuat catatan internal
        if ($isInternal && !$ticket->canBeManagedBy($user)) {
            $isInternal = false;
        }

        $reply = HelpdeskTicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => trim($request->input('message')),
            'attachment' => $attachmentPath,
            'is_internal' => $isInternal,
        ]);

        $isFirstResponse = empty($ticket->first_response_at);
        $now = Carbon::now();

        // Logika Respon oleh Agen / Karyawan
        $isAgentResponding = ($user->id !== $ticket->user_id);

        if ($isAgentResponding) {
            $ticketUpdates = [];

            // Rekam waktu first response
            if ($isFirstResponse) {
                $ticketUpdates['first_response_at'] = $now;
            }

            // Jika tiket belum di-assign, auto-assign ke agen pertama yang merespon
            if (empty($ticket->assigned_to)) {
                $ticketUpdates['assigned_to'] = $user->id;
                HelpdeskTicketLog::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $user->id,
                    'action' => 'Assigned',
                    'details' => "Tiket otomatis di-assign ke {$user->name} karena merespon tiket.",
                    'created_at' => $now,
                ]);
            }

            // Update status tiket
            if ($ticket->status === 'open') {
                $ticketUpdates['status'] = 'in_progress';
            } elseif ($ticket->status === 'in_progress' && !$isInternal) {
                $ticketUpdates['status'] = 'answered';
            }

            if (!empty($ticketUpdates)) {
                $ticket->update($ticketUpdates);
            }

            // =========================================================================
            // 🚀 OTOMASI WORK PLAN: MASUKKAN TIKET KE WORKPLAN STEP PROGRESS (IN PROGRESS)
            // =========================================================================
            $targetAgent = $ticket->assigned_to ? User::find($ticket->assigned_to) : $user;
            if ($targetAgent) {
                HelpdeskWorkplanService::syncTicketToWorkplan($ticket, $targetAgent);
            }

        } else {
            // Jika Pembuat Tiket (User) yang membalas kembali
            if (in_array($ticket->status, ['answered', 'resolved'])) {
                $ticket->update(['status' => 'in_progress']);
            }
        }

        // Sinkronkan balasan ke komentar Work Plan (jika ada task terkait)
        HelpdeskWorkplanService::syncReplyToWorkplanComment($ticket, $reply);

        // Catat Audit Log Respon
        HelpdeskTicketLog::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'action' => $isInternal ? 'Internal_Note' : 'Replied',
            'details' => $isInternal ? "Menambahkan catatan internal." : "Mengirimkan balasan kepada pengaju.",
            'created_at' => $now,
        ]);

        $msgSuccess = "Balasan berhasil dikirim.";
        if ($isAgentResponding && empty($ticket->workplan_task_id)) {
            $msgSuccess .= " Data tiket telah otomatis masuk ke Work Plan Anda pada kolom Progress!";
        }

        return redirect()->route('helpdesk.tickets.show', $ticket->id)->with('success', $msgSuccess);
    }

    /**
     * Tombol Pintas "Ambil & Tangani Tiket" (Claim Ticket)
     * Langsung meng-assign tiket dan memasukkannya ke Work Plan pada kolom Progress!
     */
    public function claim($id)
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        $ticket = HelpdeskTicket::with(['division'])->findOrFail($id);

        // Hanya Admin atau Agen dari divisi tiket yang boleh mengklaim tiket
        $myDivisionIds = HelpdeskDivisionAgent::where('user_id', $user->id)->pluck('division_id')->toArray();
        if (!$user->isHelpdeskAdmin() && !in_array($ticket->division_id, $myDivisionIds)) {
            abort(403, 'Anda bukan merupakan agen dari divisi yang dituju oleh tiket ini.');
        }

        if ($ticket->isClosed()) {
            return redirect()->back()->with('error', 'Tiket ini sudah ditutup.');
        }

        $now = Carbon::now();

        $ticket->update([
            'assigned_to' => $user->id,
            'status' => 'in_progress',
            'first_response_at' => $ticket->first_response_at ?: $now,
        ]);

        // Catat Audit Log
        HelpdeskTicketLog::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'action' => 'Assigned',
            'details' => "Tiket diambil dan ditangani langsung oleh {$user->name}.",
            'created_at' => $now,
        ]);

        // =========================================================================
        // 🚀 OTOMASI WORK PLAN: BUAT TUGAS DI STEP IN PROGRESS
        // =========================================================================
        $task = HelpdeskWorkplanService::syncTicketToWorkplan($ticket, $user);

        return redirect()->route('helpdesk.tickets.show', $ticket->id)
            ->with('success', "Tiket berhasil diambil! Tugas baru telah otomatis ditambahkan ke Work Plan Anda di kolom Progress.");
    }

    /**
     * Perbarui Status Tiket (Open, In Progress, Answered, Resolved, Closed)
     */
    public function updateStatus(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) return response()->json(['success' => false, 'error' => 'Unauthorized'], 401);

        $ticket = HelpdeskTicket::findOrFail($id);

        if (!$ticket->canBeManagedBy($user)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'error' => 'Forbidden'], 403);
            }
            abort(403, 'Anda tidak memiliki wewenang untuk mengubah status tiket ini.');
        }

        $request->validate([
            'status' => 'required|in:open,in_progress,answered,resolved,closed',
        ]);

        $oldStatus = $ticket->status;
        $newStatus = $request->input('status');
        $now = Carbon::now();

        $updates = ['status' => $newStatus];
        if (in_array($newStatus, ['resolved', 'closed'])) {
            if ($newStatus === 'resolved') $updates['resolved_at'] = $now;
            if ($newStatus === 'closed') $updates['closed_at'] = $now;
        }

        $ticket->update($updates);

        // Catat Audit Log
        HelpdeskTicketLog::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'action' => 'Status_Changed',
            'details' => "Status tiket diubah dari {$oldStatus} menjadi {$newStatus} oleh {$user->name}.",
            'created_at' => $now,
        ]);

        // Sinkronisasi ke Work Plan: Jika closed/resolved, set task menjadi 'done'
        if (in_array($newStatus, ['resolved', 'closed'])) {
            HelpdeskWorkplanService::syncTicketClosedToWorkplan($ticket);
        } elseif ($newStatus === 'in_progress' && $ticket->assigned_to) {
            $assignedUser = User::find($ticket->assigned_to);
            if ($assignedUser) {
                HelpdeskWorkplanService::syncTicketToWorkplan($ticket, $assignedUser);
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Status tiket berhasil diubah menjadi {$ticket->status_label}.",
                'status' => $newStatus,
                'status_label' => $ticket->status_label,
                'status_badge' => $ticket->status_badge,
            ]);
        }

        return redirect()->back()->with('success', "Status tiket berhasil diubah menjadi {$ticket->status_label}.");
    }

    /**
     * Papan Kanban Tiket Helpdesk
     */
    public function kanban(Request $request)
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        // Kanban Helpdesk hanya untuk Administrator
        if (!$user->isHelpdeskAdmin()) {
            abort(403, 'Menu Kanban Helpdesk hanya dapat diakses oleh Administrator.');
        }

        $query = HelpdeskTicket::with(['creator', 'division', 'assignedAgent', 'workplanTask']);

        if ($divisionId = $request->query('division_id')) {
            $query->where('division_id', $divisionId);
        }

        if ($priority = $request->query('priority')) {
            $query->where('priority', $priority);
        }

        $tickets = $query->orderBy('created_at', 'desc')->get();

        $openTickets = $tickets->where('status', 'open');
        $inProgressTickets = $tickets->where('status', 'in_progress');
        $answeredTickets = $tickets->where('status', 'answered');
        $closedTickets = $tickets->whereIn('status', ['resolved', 'closed']);

        $divisions = HelpdeskDivision::where('is_active', true)->orderBy('name', 'asc')->get();

        return view('helpdesk.kanban', compact(
            'openTickets',
            'inProgressTickets',
            'answeredTickets',
            'closedTickets',
            'divisions',
            'user'
        ));
    }

    /**
     * Tampilkan / Unduh Berkas Lampiran Tiket
     */
    public function downloadAttachment($id)
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        $ticket = HelpdeskTicket::findOrFail($id);

        // Otorisasi akses tiket
        if (!$user->isAdmin()) {
            $myDivisionIds = $user->helpdeskDivisions()->pluck('helpdesk_divisions.id')->toArray();
            $isAgentOfDivision = in_array($ticket->division_id, $myDivisionIds);
            $isCreator = ($ticket->user_id === $user->id);
            if (!$isAgentOfDivision && !$isCreator) {
                abort(403, 'Anda tidak memiliki akses ke berkas lampiran tiket ini.');
            }
        }

        if (!$ticket->attachment) {
            abort(404, 'Tiket ini tidak memiliki berkas lampiran.');
        }

        $path = storage_path('app/public/' . $ticket->attachment);
        if (!file_exists($path)) {
            $altPath = public_path('storage/' . $ticket->attachment);
            if (file_exists($altPath)) {
                $path = $altPath;
            } else {
                $altPath2 = public_path($ticket->attachment);
                if (file_exists($altPath2)) {
                    $path = $altPath2;
                } else {
                    abort(404, 'Berkas lampiran fisik tidak ditemukan di server.');
                }
            }
        }

        if (request()->query('download')) {
            return response()->download($path, basename($ticket->attachment));
        }

        return response()->file($path);
    }

    /**
     * Tampilkan / Unduh Berkas Lampiran Balasan Tiket
     */
    public function downloadReplyAttachment($ticketId, $replyId)
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        $ticket = HelpdeskTicket::findOrFail($ticketId);

        // Otorisasi akses tiket
        if (!$user->isAdmin()) {
            $myDivisionIds = $user->helpdeskDivisions()->pluck('helpdesk_divisions.id')->toArray();
            $isAgentOfDivision = in_array($ticket->division_id, $myDivisionIds);
            $isCreator = ($ticket->user_id === $user->id);
            if (!$isAgentOfDivision && !$isCreator) {
                abort(403, 'Anda tidak memiliki akses ke berkas lampiran balasan ini.');
            }
        }

        $reply = HelpdeskTicketReply::where('ticket_id', $ticket->id)->findOrFail($replyId);

        if (!$reply->attachment) {
            abort(404, 'Balasan ini tidak memiliki berkas lampiran.');
        }

        $path = storage_path('app/public/' . $reply->attachment);
        if (!file_exists($path)) {
            $altPath = public_path('storage/' . $reply->attachment);
            if (file_exists($altPath)) {
                $path = $altPath;
            } else {
                $altPath2 = public_path($reply->attachment);
                if (file_exists($altPath2)) {
                    $path = $altPath2;
                } else {
                    abort(404, 'Berkas lampiran fisik tidak ditemukan di server.');
                }
            }
        }

        if (request()->query('download')) {
            return response()->download($path, basename($reply->attachment));
        }

        return response()->file($path);
    }
}
