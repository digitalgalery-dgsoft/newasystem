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

        $isAdmin = $user->isHelpdeskAdmin();
        $isDivisionUser = $user->isHelpdeskDivisionUser();
        $isRegularUser = $user->isHelpdeskRegularUser();

        $myDivisionIds = HelpdeskDivisionAgent::where('user_id', $user->id)
            ->pluck('division_id')
            ->toArray();

        // 1. Tentukan Base Query untuk Metrik Sesuai Hak Akses
        // User Biasa: hanya tiket yang diajukan sendiri
        // User Divisi: hanya tiket yang ditujukan ke divisinya
        // Administrator: melihat seluruh tiket di sistem
        if ($isAdmin) {
            $baseQuery = HelpdeskTicket::query();
        } elseif ($isDivisionUser) {
            $baseQuery = HelpdeskTicket::whereIn('division_id', $myDivisionIds);
        } else {
            $baseQuery = HelpdeskTicket::where('user_id', $user->id);
        }

        // Metrik KPI tersaring sesuai peran
        $totalTickets = (clone $baseQuery)->count();
        $openTickets = (clone $baseQuery)->where('status', 'open')->count();
        $inProgressTickets = (clone $baseQuery)->whereIn('status', ['in_progress', 'answered'])->count();
        $closedTickets = (clone $baseQuery)->whereIn('status', ['resolved', 'closed'])->count();

        $overdueTickets = (clone $baseQuery)->whereNotIn('status', ['resolved', 'closed'])
            ->whereNotNull('due_date')
            ->where('due_date', '<', $now)
            ->count();

        // 2. Statistik Distribusi Divisi
        if ($isAdmin) {
            $divisions = HelpdeskDivision::where('is_active', true)
                ->withCount([
                    'tickets as total_count',
                    'tickets as open_count' => fn($q) => $q->where('status', 'open'),
                    'tickets as progress_count' => fn($q) => $q->whereIn('status', ['in_progress', 'answered']),
                    'tickets as closed_count' => fn($q) => $q->whereIn('status', ['resolved', 'closed']),
                ])
                ->get();
        } elseif ($isDivisionUser) {
            $divisions = HelpdeskDivision::whereIn('id', $myDivisionIds)
                ->where('is_active', true)
                ->withCount([
                    'tickets as total_count',
                    'tickets as open_count' => fn($q) => $q->where('status', 'open'),
                    'tickets as progress_count' => fn($q) => $q->whereIn('status', ['in_progress', 'answered']),
                    'tickets as closed_count' => fn($q) => $q->whereIn('status', ['resolved', 'closed']),
                ])
                ->get();
        } else {
            $divisions = collect();
        }

        // 3. Antrean Tiket Butuh Respon
        if ($isAdmin) {
            $urgentTickets = HelpdeskTicket::with(['creator', 'division', 'assignedAgent'])
                ->whereNotIn('status', ['resolved', 'closed'])
                ->orderByRaw("CASE WHEN priority = 'Urgent' THEN 1 WHEN priority = 'High' THEN 2 WHEN status = 'open' THEN 3 ELSE 4 END")
                ->orderBy('created_at', 'desc')
                ->limit(7)
                ->get();
        } elseif ($isDivisionUser) {
            $urgentTickets = HelpdeskTicket::with(['creator', 'division', 'assignedAgent'])
                ->whereIn('division_id', $myDivisionIds)
                ->whereNotIn('status', ['resolved', 'closed'])
                ->orderByRaw("CASE WHEN priority = 'Urgent' THEN 1 WHEN priority = 'High' THEN 2 WHEN status = 'open' THEN 3 ELSE 4 END")
                ->orderBy('created_at', 'desc')
                ->limit(7)
                ->get();
        } else {
            // User Biasa: hanya tiket aktif yang diajukan sendiri
            $urgentTickets = HelpdeskTicket::with(['creator', 'division', 'assignedAgent'])
                ->where('user_id', $user->id)
                ->whereNotIn('status', ['resolved', 'closed'])
                ->orderBy('created_at', 'desc')
                ->limit(7)
                ->get();
        }

        // 4. Tiket Saya (Diajukan oleh User yang login)
        $myTickets = HelpdeskTicket::with(['division', 'assignedAgent'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // 5. Tiket Ditugaskan kepada Saya (Assigned to Me) - Khusus Admin & Agen Divisi
        $assignedToMeTickets = ($isAdmin || $isDivisionUser) ? HelpdeskTicket::with(['creator', 'division'])
            ->where('assigned_to', $user->id)
            ->whereNotIn('status', ['resolved', 'closed'])
            ->orderBy('due_date', 'asc')
            ->limit(5)
            ->get() : collect();

        // 6. Riwayat Aktivitas Terbaru
        $logsQuery = HelpdeskTicketLog::with(['ticket', 'user']);
        if ($isRegularUser) {
            $logsQuery->whereHas('ticket', fn($q) => $q->where('user_id', $user->id));
        } elseif ($isDivisionUser) {
            $logsQuery->whereHas('ticket', fn($q) => $q->whereIn('division_id', $myDivisionIds));
        }
        $recentLogs = $logsQuery->orderBy('created_at', 'desc')->limit(10)->get();

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
            'isAdmin',
            'isDivisionUser',
            'isRegularUser',
            'user'
        ));
    }
}
