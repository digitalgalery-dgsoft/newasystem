<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

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

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Activity Logs');

        // Header Laporan
        $sheet->setCellValue('A1', 'LAPORAN LOG AKTIVITAS & AUDIT TRAIL ASYSTEM');
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('0F52BA'));

        $sheet->setCellValue('A2', 'Waktu Unduh: ' . now()->translatedFormat('d F Y, H:i:s') . ' WIB | Oleh: ' . Auth::user()->name . ' (' . (Auth::user()->jabatan_display ?? 'Administrator') . ')');
        $sheet->mergeCells('A2:H2');
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('64748B'));

        // Table Header
        $headers = [
            'A4' => 'No',
            'B4' => 'Waktu (WIB)',
            'C4' => 'Pengguna / Pelaku',
            'D4' => 'Jabatan',
            'E4' => 'Aksi',
            'F4' => 'Modul',
            'G4' => 'Deskripsi Aktivitas',
            'H4' => 'IP Address & Device',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        $sheet->getStyle('A4:H4')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 10,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0F52BA'], // Sapphire Blue
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(4)->setRowHeight(26);

        // Isi Data
        $rowIdx = 5;
        foreach ($logs as $i => $item) {
            $sheet->setCellValue('A' . $rowIdx, $i + 1);
            $sheet->setCellValue('B' . $rowIdx, $item->created_at ? $item->created_at->format('Y-m-d H:i:s') : '-');
            $sheet->setCellValue('C' . $rowIdx, $item->user_name . ($item->user_email ? " ({$item->user_email})" : ''));
            $sheet->setCellValue('D' . $rowIdx, $item->user_jabatan ?? '-');
            $sheet->setCellValue('E' . $rowIdx, $item->action);
            $sheet->setCellValue('F' . $rowIdx, $item->module);
            $sheet->setCellValue('G' . $rowIdx, $item->description);
            $sheet->setCellValue('H' . $rowIdx, ($item->ip_address ?? '-') . ' / ' . $item->device);

            // Zebra styling baris genap
            if ($i % 2 === 1) {
                $sheet->getStyle("A{$rowIdx}:H{$rowIdx}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F8FAFC');
            }

            $rowIdx++;
        }

        $lastRow = max(5, $rowIdx - 1);

        // Border & Alignment
        $sheet->getStyle("A4:H{$lastRow}")->getBorders()->getAllBorders()->applyFromArray([
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => 'CBD5E1'],
        ]);

        $sheet->getStyle("A5:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("B5:B{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("E5:E{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("F5:F{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Auto Width
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $fileName = 'Activity_Logs_ASystem_' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
