<?php

namespace App\Http\Controllers\Helpdesk;

use App\Http\Controllers\Controller;
use App\Models\HelpdeskDivision;
use App\Models\HelpdeskDivisionAgent;
use App\Models\HelpdeskTicket;
use App\Models\HelpdeskTicketLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HelpdeskDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $now = Carbon::now();

        // 1. Metrik Global
        $totalTickets = HelpdeskTicket::count();
        $openTickets = HelpdeskTicket::where('status', 'open')->count();
        $inProgressTickets = HelpdeskTicket::whereIn('status', ['in_progress', 'answered'])->count();
        $closedTickets = HelpdeskTicket::whereIn('status', ['resolved', 'closed'])->count();

        // Tiket Terlambat (Overdue)
        $overdueTickets = HelpdeskTicket::whereNotIn('status', ['resolved', 'closed'])
            ->whereNotNull('due_date')
            ->where('due_date', '<', $now)
            ->count();

        // 2. Divisi yang dikelola oleh user saat ini
        $myDivisionIds = HelpdeskDivisionAgent::where('user_id', $user->id)
            ->pluck('division_id')
            ->toArray();

        $isAgent = !empty($myDivisionIds) || $user->isAdmin();

        // 3. Statistik per Divisi
        $divisions = HelpdeskDivision::where('is_active', true)
            ->withCount([
                'tickets as total_count',
                'tickets as open_count' => function ($q) {
                    $q->where('status', 'open');
                },
                'tickets as progress_count' => function ($q) {
                    $q->whereIn('status', ['in_progress', 'answered']);
                },
                'tickets as closed_count' => function ($q) {
                    $q->whereIn('status', ['resolved', 'closed']);
                },
            ])
            ->get();

        // 4. Antrean Tiket Butuh Respon Segera (Open / Urgent)
        $urgentQuery = HelpdeskTicket::with(['creator', 'division', 'assignedAgent'])
            ->whereNotIn('status', ['resolved', 'closed']);

        if (!$user->isAdmin()) {
            // Jika agen divisi, utamakan divisi miliknya atau tiket miliknya
            if (!empty($myDivisionIds)) {
                $urgentQuery->where(function ($q) use ($user, $myDivisionIds) {
                    $q->whereIn('division_id', $myDivisionIds)
                        ->orWhere('user_id', $user->id)
                        ->orWhere('assigned_to', $user->id);
                });
            } else {
                $urgentQuery->where('user_id', $user->id);
            }
        }

        $urgentTickets = (clone $urgentQuery)
            ->orderByRaw("CASE WHEN priority = 'Urgent' THEN 1 WHEN priority = 'High' THEN 2 WHEN status = 'open' THEN 3 ELSE 4 END")
            ->orderBy('created_at', 'desc')
            ->limit(7)
            ->get();

        // 5. Tiket Saya (Yang diajukan oleh user)
        $myTickets = HelpdeskTicket::with(['division', 'assignedAgent'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // 6. Tiket Ditugaskan kepada Saya (Assigned to Me)
        $assignedToMeTickets = HelpdeskTicket::with(['creator', 'division'])
            ->where('assigned_to', $user->id)
            ->whereNotIn('status', ['resolved', 'closed'])
            ->orderBy('due_date', 'asc')
            ->limit(5)
            ->get();

        // 7. Riwayat Aktivitas Terbaru (Audit Logs)
        $recentLogs = HelpdeskTicketLog::with(['ticket', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('helpdesk.index', compact(
            'totalTickets',
            'openTickets',
            'inProgressTickets',
            'closedTickets',
            'overdueTickets',
            'divisions',
            'urgentTickets',
            'myTickets',
            'assignedToMeTickets',
            'recentLogs',
            'isAgent',
            'user'
        ));
    }
}
