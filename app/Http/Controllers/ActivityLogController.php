<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Services\ActivityLogXlsxExportService;

class ActivityLogController extends Controller
{
    /**
     * Halaman Utama Audit Trail & Log Aktivitas
     */
    public function index(Request $request)
    {
        // Proteksi: Hanya Administrator atau yang berizin
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk melihat log aktivitas sistem.');
        }

        $fModule    = $request->get('f_module');
        $fAction    = $request->get('f_action');
        $fUser      = $request->get('f_user');
        $fSearch    = $request->get('f_search');
        $fDateStart = $request->get('f_date_start');
        $fDateEnd   = $request->get('f_date_end');
        $perPage    = (int) $request->get('per_page', 25);
        if (!in_array($perPage, [15, 25, 50, 100])) {
            $perPage = 25;
        }

        // Kueri dasar
        $query = ActivityLog::query()
            ->filterModule($fModule)
            ->filterAction($fAction)
            ->filterUser($fUser)
            ->search($fSearch)
            ->dateRange($fDateStart, $fDateEnd)
            ->latest('id');

        // 4 Kartu Metrik Ringkasan
        $today = Carbon::today();
        $metrics = [
            'total'   => ActivityLog::count(),
            'today'   => ActivityLog::whereDate('created_at', $today)->count(),
            'logins'  => ActivityLog::where('action', 'LOGIN')->count(),
            'changes' => ActivityLog::whereIn('action', ['CREATE', 'UPDATE', 'DELETE'])->count(),
        ];

        // Daftar unik untuk opsi filter dropdown
        $listModules = ActivityLog::distinct()->whereNotNull('module')->pluck('module')->sort()->values();
        $listActions = ActivityLog::distinct()->whereNotNull('action')->pluck('action')->sort()->values();
        $listUsers   = User::orderBy('name')->get(['id', 'name', 'email']);

        // Data terpaginasi
        $logs = $query->paginate($perPage)->withQueryString();

        return view('activity_logs.index', compact(
            'logs',
            'metrics',
            'listModules',
            'listActions',
            'listUsers',
            'fModule',
            'fAction',
            'fUser',
            'fSearch',
            'fDateStart',
            'fDateEnd',
            'perPage'
        ));
    }

    /**
     * API Detail Log (untuk Modal Diff & JSON Payload)
     */
    public function show($id)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $log = ActivityLog::findOrFail($id);

        return response()->json([
            'id'           => $log->id,
            'user_name'    => $log->user_name,
            'user_email'   => $log->user_email,
            'user_jabatan' => $log->user_jabatan,
            'action'       => $log->action,
            'module'       => $log->module,
            'description'  => $log->description,
            'subject_type' => $log->subject_type,
            'subject_id'   => $log->subject_id,
            'properties'   => $log->properties,
            'ip_address'   => $log->ip_address,
            'user_agent'   => $log->user_agent,
            'device'       => $log->device,
            'url'          => $log->url,
            'method'       => $log->method,
            'created_at'   => $log->formatted_created_at,
            'diff_time'    => $log->diff_time,
        ]);
    }

    /**
     * Ekspor Laporan Log Aktivitas ke Format Microsoft Excel (.xlsx)
     */
    public function export(Request $request)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Akses Ditolak.');
        }

        $fModule    = $request->get('f_module');
        $fAction    = $request->get('f_action');
        $fUser      = $request->get('f_user');
        $fSearch    = $request->get('f_search');
        $fDateStart = $request->get('f_date_start');
        $fDateEnd   = $request->get('f_date_end');

        // Catat aktivitas ekspor ini
        ActivityLogger::export('Log Aktivitas', 'Mengekspor laporan log aktivitas sistem ke file Excel (.xlsx)', [
            'module' => $fModule,
            'action' => $fAction,
            'user'   => $fUser,
            'search' => $fSearch,
            'start'  => $fDateStart,
            'end'    => $fDateEnd,
        ]);

        $query = ActivityLog::query()
            ->filterModule($fModule)
            ->filterAction($fAction)
            ->filterUser($fUser)
            ->search($fSearch)
            ->dateRange($fDateStart, $fDateEnd)
            ->latest('id')
            ->limit(5000); // Batas aman unduhan

        $logs = $query->get();

        $meta = [
            'module' => $fModule,
            'action' => $fAction,
            'user' => $fUser,
            'search' => $fSearch,
            'start' => $fDateStart,
            'end' => $fDateEnd,
            'exporter_name' => Auth::user()->name ?? 'Administrator',
            'exporter_jabatan' => Auth::user()->jabatan_display ?? 'Administrator',
        ];

        $filePath = ActivityLogXlsxExportService::generateXlsx($logs, $meta);
        $fileName = 'Activity_Logs_ASystem_' . now()->format('Ymd_His') . '.xlsx';

        return response()->download($filePath, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
            'Pragma' => 'public',
        ])->deleteFileAfterSend(true);
    }

}
