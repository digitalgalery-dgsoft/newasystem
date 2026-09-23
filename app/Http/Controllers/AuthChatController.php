<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Employee;
use App\Models\User;
use App\Models\PasswordResetRequest;
use App\Models\PasswordResetChatMessage;
use App\Services\OdooSyncService;

class AuthChatController extends Controller
{
    // =========================================================================
    // SISI KARYAWAN (PUBLIK / LOGIN PAGE)
    // =========================================================================

    /**
     * Verifikasi NIK, Cek Odoo jika belum ada, buat atau lanjutkan tiket chat.
     */
    public function checkNik(Request $request)
    {
        $request->validate([
            'nik'     => 'required|string',
            'message' => 'nullable|string|max:1000',
        ]);

        $cleanNik = trim($request->input('nik'));
        $userMsg  = trim($request->input('message') ?: 'Halo Admin, saya lupa kata sandi login ASystem. Mohon bantuannya untuk kirim akses.');

        // 1. Cek di database lokal terlebih dahulu
        $employee = Employee::where('nik', $cleanNik)
            ->orWhere('nip', $cleanNik)
            ->first();

        $odooSynced = false;

        // Jika tidak ditemukan di lokal, atau di lokal berstatus Resign (siapa tahu sudah aktif di entitas baru)
        if (!$employee || $employee->status === 'Resign') {
            $odooCheck = OdooSyncService::findAndSyncByNik($cleanNik);

            if ($odooCheck['status'] === 'found_active') {
                $employee = $odooCheck['employee'];
                $odooSynced = true;
            } elseif ($odooCheck['status'] === 'found_resign') {
                return response()->json([
                    'success' => false,
                    'status'  => 'resigned',
                    'message' => "NIK '{$cleanNik}' terdaftar di Odoo ({$odooCheck['entity']}) namun berstatus Resign / Non-Aktif. Hubungi tim HRD untuk informasi lebih lanjut.",
                ], 422);
            } else {
                if (!$employee) {
                    return response()->json([
                        'success' => false,
                        'status'  => 'not_found',
                        'message' => "NIK '{$cleanNik}' tidak ditemukan di database lokal maupun di seluruh server Odoo ERP yang aktif (AMK, AKP, ATK, ABO, ATB).",
                    ], 404);
                }
                // Jika lokal ada tapi status Resign dan Odoo tidak menemukan aktif
                return response()->json([
                    'success' => false,
                    'status'  => 'resigned',
                    'message' => "Karyawan dengan NIK '{$cleanNik}' berstatus Resign / Non-Aktif dalam sistem.",
                ], 422);
            }
        }

        // Pastikan status karyawan aktif
        if ($employee->status !== 'Aktiv') {
            return response()->json([
                'success' => false,
                'status'  => 'inactive',
                'message' => "Status karyawan [{$employee->nama_karyawan}] saat ini adalah '{$employee->status}'. Hanya karyawan aktif yang dapat mengajukan permintaan reset kata sandi.",
            ], 422);
        }

        // 2. Cari tiket aktif yang belum selesai (pending / replied) dalam 24 jam terakhir
        $activeTicket = PasswordResetRequest::where('employee_id', $employee->id)
            ->whereIn('status', ['pending', 'replied'])
            ->where('created_at', '>=', now()->subHours(24))
            ->latest('id')
            ->first();

        if ($activeTicket) {
            $ticket = $activeTicket;
            // Jika ada pesan baru dari karyawan, simpan ke thread
            if (!empty($userMsg)) {
                PasswordResetChatMessage::create([
                    'request_id'  => $ticket->id,
                    'sender_type' => 'employee',
                    'sender_name' => $employee->nama_karyawan,
                    'message'     => $userMsg,
                    'is_read'     => false,
                ]);
                $ticket->update(['status' => 'pending']);
            }
        } else {
            // Buat tiket permintaan baru
            $ticket = PasswordResetRequest::create([
                'ticket_number'   => PasswordResetRequest::generateTicketNumber(),
                'session_token'   => Str::random(40),
                'employee_id'     => $employee->id,
                'nik'             => $employee->nik,
                'nama_karyawan'   => $employee->nama_karyawan,
                'email'           => $employee->email,
                'telepon'         => $employee->telepon,
                'jabatan'         => $employee->jabatan,
                'entitas'         => $employee->entity,
                'tipe_karyawan'   => $employee->tipe_karyawan,
                'status'          => 'pending',
                'odoo_synced'     => $odooSynced,
                'request_message' => $userMsg,
            ]);

            // Buat pesan pertama
            PasswordResetChatMessage::create([
                'request_id'  => $ticket->id,
                'sender_type' => 'employee',
                'sender_name' => $employee->nama_karyawan,
                'message'     => $userMsg,
                'is_read'     => false,
            ]);
        }

        // Ambil riwayat percakapan
        $messages = $ticket->messages()->get();

        return response()->json([
            'success'       => true,
            'ticket_id'     => $ticket->id,
            'ticket_number' => $ticket->ticket_number,
            'session_token' => $ticket->session_token,
            'status'        => $ticket->status,
            'odoo_synced'   => $odooSynced,
            'employee'      => [
                'id'            => $employee->id,
                'nama'          => $employee->nama_karyawan,
                'nik'           => $employee->nik,
                'nip'           => $employee->nip,
                'jabatan'       => $employee->jabatan,
                'entitas'       => $employee->entity,
                'tipe_karyawan' => $employee->tipe_karyawan,
                'email'         => $employee->email,
                'foto'          => $employee->foto,
            ],
            'messages'      => $messages,
        ], 200);
    }

    /**
     * Polling percakapan untuk karyawan berdasarkan session_token.
     */
    public function poll(Request $request)
    {
        $sessionToken = $request->query('session_token');
        if (empty($sessionToken)) {
            return response()->json(['success' => false, 'message' => 'Token sesi tidak ada.'], 400);
        }

        $ticket = PasswordResetRequest::where('session_token', $sessionToken)->first();
        if (!$ticket) {
            return response()->json(['success' => false, 'message' => 'Tiket tidak ditemukan.'], 404);
        }

        $lastMessageId = (int) $request->query('last_message_id', 0);

        $newMessages = $ticket->messages()
            ->where('id', '>', $lastMessageId)
            ->get();

        return response()->json([
            'success'       => true,
            'status'        => $ticket->status,
            'access_sent'   => !empty($ticket->access_sent_at),
            'messages'      => $newMessages,
        ], 200);
    }

    /**
     * Karyawan mengirim pesan tambahan pada sesi chat yang sedang berjalan.
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'session_token' => 'required|string',
            'message'       => 'required|string|max:1000',
        ]);

        $ticket = PasswordResetRequest::where('session_token', $request->input('session_token'))->first();
        if (!$ticket) {
            return response()->json(['success' => false, 'message' => 'Sesi chat tidak valid.'], 404);
        }

        $msg = PasswordResetChatMessage::create([
            'request_id'  => $ticket->id,
            'sender_type' => 'employee',
            'sender_name' => $ticket->nama_karyawan,
            'message'     => trim($request->input('message')),
            'is_read'     => false,
        ]);

        if ($ticket->status === 'replied') {
            $ticket->update(['status' => 'pending']);
        }

        return response()->json([
            'success' => true,
            'message' => $msg,
        ], 201);
    }

    // =========================================================================
    // SISI ADMINISTRATOR (DASHBOARD HELPDESK)
    // =========================================================================

    /**
     * Halaman Dashboard Helpdesk / Live Chat Bantuan Login.
     */
    public function adminIndex(Request $request)
    {
        $filterStatus = $request->query('status', 'all');
        $search       = trim($request->query('search', ''));
        $activeId     = $request->query('id');

        $query = PasswordResetRequest::with(['employee', 'latestMessage'])->latest('updated_at');

        if ($filterStatus === 'pending') {
            $query->where('status', 'pending');
        } elseif ($filterStatus === 'resolved') {
            $query->where('status', 'resolved');
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_karyawan', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('ticket_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $requests = $query->paginate(20)->withQueryString();

        // Tiket yang sedang dibuka di pane kanan
        $activeTicket = null;
        if ($activeId) {
            $activeTicket = PasswordResetRequest::with(['employee', 'messages', 'resolver'])->find($activeId);
        } elseif ($requests->isNotEmpty()) {
            $activeTicket = PasswordResetRequest::with(['employee', 'messages', 'resolver'])->find($requests->first()->id);
        }

        // Tandai pesan karyawan sebagai terbaca saat admin membuka tiket
        if ($activeTicket) {
            $activeTicket->messages()
                ->where('sender_type', 'employee')
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }

        $pendingCount  = PasswordResetRequest::where('status', 'pending')->count();
        $resolvedCount = PasswordResetRequest::where('status', 'resolved')->count();
        $totalCount    = PasswordResetRequest::count();

        return view('admin.auth_chat.index', compact(
            'requests',
            'activeTicket',
            'filterStatus',
            'search',
            'pendingCount',
            'resolvedCount',
            'totalCount'
        ));
    }

    /**
     * Polling data untuk admin (badge counter & pesan chat baru).
     */
    public function adminPoll(Request $request)
    {
        $pendingCount = PasswordResetRequest::where('status', 'pending')->count();
        $activeId     = (int) $request->query('active_id', 0);
        $lastMsgId    = (int) $request->query('last_message_id', 0);

        $newMessages = [];
        if ($activeId > 0) {
            $newMessages = PasswordResetChatMessage::where('request_id', $activeId)
                ->where('id', '>', $lastMsgId)
                ->get();

            // Tandai terbaca jika dari employee
            PasswordResetChatMessage::where('request_id', $activeId)
                ->where('sender_type', 'employee')
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }

        return response()->json([
            'success'       => true,
            'pending_count' => $pendingCount,
            'new_messages'  => $newMessages,
        ]);
    }

    /**
     * Admin membalas pesan secara manual.
     */
    public function adminReply(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $ticket = PasswordResetRequest::findOrFail($id);

        $msg = PasswordResetChatMessage::create([
            'request_id'  => $ticket->id,
            'sender_type' => 'admin',
            'sender_name' => Auth::user()->name ?: 'Administrator HR',
            'message'     => trim($request->input('message')),
            'is_read'     => true,
        ]);

        $ticket->update([
            'status'      => 'replied',
            'resolved_by' => Auth::id(),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }

        return redirect()->route('admin.auth-chat.index', ['id' => $ticket->id])
            ->with('success', 'Balasan pesan berhasil dikirim ke karyawan.');
    }

    /**
     * Tombol Utama "Kirim Akses" (Email & Password).
     * - Menyiapkan kredensial login (Email & Default Password ddmmyyyy atau password akun).
     * - Mengaktifkan izin login jika karyawan RateCard.
     * - Memastikan akun User ada di tabel users.
     * - Mengirim balasan chat resmi berformat kredensial.
     * - Mengubah status tiket menjadi 'resolved'.
     */
    public function adminSendAccess(Request $request, $id)
    {
        $ticket = PasswordResetRequest::with('employee')->findOrFail($id);
        $employee = $ticket->employee;

        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Data karyawan tidak ditemukan.'], 404);
        }

        // Tentukan email login
        $emailLogin = !empty($employee->email) ? $employee->email : ($employee->nik . '@asystem.co.id');
        
        // Tentukan kata sandi default (ddmmyyyy dari tanggal lahir)
        $defPassword = $employee->default_password ?: 'ddmmyyyy';
        $hashedDefault = Hash::make($defPassword);

        // Jika karyawan belum punya password tersimpan atau request reset, set password ke default
        $employee->password = $hashedDefault;

        // Jika karyawan RateCard dan belum punya izin login, AKTIFKAN OTOMATIS!
        $isRateCard = ($employee->tipe_karyawan !== 'Inhouse');
        $activatedNote = '';
        if ($isRateCard && !$employee->akses_login) {
            $employee->akses_login = true;
            $activatedNote = ' (Izin Akses Login RateCard telah diaktifkan otomatis oleh Admin)';
        }
        $employee->save();

        // Pastikan akun User terdaftar di tabel users
        $userRole = ($employee->tipe_karyawan === 'Inhouse') ? 'karyawan_inhouse' : 'karyawan_ratecard';
        $user = User::whereRaw('LOWER(email) = ?', [strtolower($emailLogin)])->first();
        if (!$user) {
            User::create([
                'name'      => $employee->nama_karyawan,
                'email'     => $emailLogin,
                'password'  => $hashedDefault,
                'role'      => $userRole,
                'area'      => $employee->area,
                'job_title' => $employee->jabatan,
                'phone'     => $employee->telepon,
                'is_active' => true,
            ]);
        } else {
            $user->update([
                'name'      => $employee->nama_karyawan,
                'password'  => $hashedDefault,
                'is_active' => true,
            ]);
        }

        $loginUrl = route('login');

        // Format pesan balasan resmi
        $formattedReply = "Halo {$employee->nama_karyawan},\n\nBerikut informasi akses login akun ASystem Cloud Anda:\n" .
            "• Email / NIK : {$emailLogin}\n" .
            "• Kata Sandi  : {$defPassword}\n" .
            "• Entitas     : {$employee->entity} ({$employee->tipe_karyawan})\n" .
            "• Tautan Masuk: {$loginUrl}\n\n" .
            "Silakan gunakan email/NIK dan kata sandi di atas untuk login. Harap segera perbarui kata sandi Anda setelah berhasil masuk.{$activatedNote}\n\nSalam,\nAdmin HR ASystem Support System";

        // Simpan pesan balasan ke chat thread
        $chatMsg = PasswordResetChatMessage::create([
            'request_id'  => $ticket->id,
            'sender_type' => 'admin',
            'sender_name' => Auth::user()->name ?: 'Administrator HR',
            'message'     => $formattedReply,
            'meta'        => [
                'is_credentials' => true,
                'email'          => $emailLogin,
                'password'       => $defPassword,
                'login_url'      => $loginUrl,
                'nama'           => $employee->nama_karyawan,
                'nik'            => $employee->nik,
                'entitas'        => $employee->entity,
                'tipe_karyawan'  => $employee->tipe_karyawan,
            ],
            'is_read'     => true,
        ]);

        // Update status tiket
        $ticket->update([
            'status'         => 'resolved',
            'access_sent_at' => now(),
            'resolved_by'    => Auth::id(),
        ]);

        // Buat URL WhatsApp untuk tombol sekunder
        $cleanPhone = preg_replace('/[^0-9]/', '', (string)$employee->telepon);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        }
        $waUrl = !empty($cleanPhone) ? "https://wa.me/{$cleanPhone}?text=" . urlencode($formattedReply) : null;

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'       => true,
                'message'       => 'Akses login berhasil dikirim ke karyawan via Live Chat!',
                'chat_message'  => $chatMsg,
                'wa_url'        => $waUrl,
                'credentials'   => [
                    'email'    => $emailLogin,
                    'password' => $defPassword,
                ],
            ]);
        }

        return redirect()->route('admin.auth-chat.index', ['id' => $ticket->id])
            ->with('success', "Akses login berhasil dikirimkan ke {$employee->nama_karyawan}!");
    }

    /**
     * Tandai tiket selesai secara manual tanpa kirim ulang kredensial.
     */
    public function adminResolve(Request $request, $id)
    {
        $ticket = PasswordResetRequest::findOrFail($id);
        $ticket->update([
            'status'      => 'resolved',
            'resolved_by' => Auth::id(),
        ]);

        return redirect()->route('admin.auth-chat.index', ['id' => $ticket->id])
            ->with('success', "Tiket {$ticket->ticket_number} telah ditandai Selesai.");
    }
}
