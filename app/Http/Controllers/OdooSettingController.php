<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\OdooEntity;
use App\Models\OdooSyncLog;
use App\Services\OdooSyncService;
use App\Services\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OdooSettingController extends Controller
{
    /**
     * Display Odoo Integration Settings for 5 entities (AMK, AKP, ATK, ABO, ATB).
     */
    public function index(Request $request): View
    {
        $entities = OdooEntity::orderBy('id')->get();

        // Ensure 5 default entities exist if database was freshly reset
        if ($entities->count() < 5) {
            $defaults = [
                ['code' => 'AMK', 'name' => 'PT Arina Multi Karya', 'odoo_url' => 'https://odoo.arinamultikarya.com', 'odoo_db' => 'AMK_LIVE'],
                ['code' => 'AKP', 'name' => 'PT Alva Karya Perkasa', 'odoo_url' => 'https://odoo.arinamultikarya.com', 'odoo_db' => 'AKP_LIVE'],
                ['code' => 'ATK', 'name' => 'PT Anugrah Terpercaya Kerja', 'odoo_url' => 'https://odoo.arinamultikarya.com', 'odoo_db' => 'ATK_LIVE'],
                ['code' => 'ABO', 'name' => 'PT Abadi Berkat Odelia', 'odoo_url' => 'https://odoo.arinamultikarya.com', 'odoo_db' => 'ABO_LIVE'],
                ['code' => 'ATB', 'name' => 'PT Anugrah Talenta Berkarya', 'odoo_url' => 'https://odoo.arinamultikarya.com', 'odoo_db' => 'ATB_LIVE'],
            ];
            foreach ($defaults as $def) {
                OdooEntity::firstOrCreate(['code' => $def['code']], $def);
            }
            $entities = OdooEntity::orderBy('id')->get();
        }

        $activeTab = $request->query('tab', 'AMK');
        $currentEntity = $entities->firstWhere('code', $activeTab) ?: $entities->first();

        // Stats summary across system
        $stats = [
            'total_karyawan'      => Employee::count(),
            'total_aktif'         => Employee::where('status', 'Aktiv')->count(),
            'total_resign'        => Employee::where('status', 'Resign')->count(),
            'total_inhouse'       => Employee::where('tipe_karyawan', 'Inhouse')->count(),
            'total_ratecard'      => Employee::where('tipe_karyawan', 'RateCard')->count(),
            'configured_entities' => $entities->filter->isConfigured()->count(),
            'active_entities'     => $entities->where('is_active', true)->count(),
        ];

        // Recent Sync Logs
        $recentLogs = OdooSyncLog::with('entity')
            ->orderBy('id', 'desc')
            ->take(15)
            ->get();

        return view('odoo.setting', compact('entities', 'currentEntity', 'activeTab', 'stats', 'recentLogs'));
    }

    /**
     * Update connection credentials for a specific entity.
     */
    public function update(Request $request, string $code): RedirectResponse
    {
        $entity = OdooEntity::where('code', $code)->firstOrFail();

        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'odoo_url'      => 'nullable|string|max:255',
            'odoo_db'       => 'nullable|string|max:255',
            'odoo_username' => 'nullable|string|max:255',
            'odoo_api_key'  => 'nullable|string',
            'is_active'     => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? (bool)$request->is_active : false;

        $entity->update($validated);

        ActivityLogger::log('UPDATE', 'Integrasi Odoo', "Memperbarui konfigurasi koneksi Odoo entitas {$entity->name} ({$code})", $entity);

        return redirect()
            ->route('odoo.setting.index', ['tab' => $code])
            ->with('success', "Konfigurasi koneksi Odoo untuk entitas {$entity->name} ({$code}) berhasil diperbarui.");
    }

    /**
     * Test XML-RPC connection to Odoo server.
     */
    public function testConnection(Request $request, string $code): JsonResponse
    {
        $entity = OdooEntity::where('code', $code)->first();
        if (!$entity) {
            return response()->json([
                'success' => false,
                'message' => "Entitas {$code} tidak ditemukan.",
            ], 404);
        }

        if (!$entity->isConfigured()) {
            return response()->json([
                'success' => false,
                'message' => "Kredensial Odoo untuk {$entity->name} belum lengkap. Mohon isi URL, DB, Username, dan API Key.",
            ], 422);
        }

        try {
            $service = OdooSyncService::fromEntity($entity);
            $result = $service->testConnection();

            return response()->json([
                'success' => true,
                'message' => "Koneksi Berhasil! Terhubung ke Odoo v{$result['server_version']} sebagai UID {$result['uid']} (Database: {$result['database']})",
                'data'    => $result,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Koneksi Gagal: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Execute sync for a specific entity.
     * Supports category filtering: 'all', 'inhouse', 'ratecard'.
     */
    public function sync(Request $request, string $code)
    {
        $entity = OdooEntity::where('code', $code)->first();
        if (!$entity) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => "Entitas {$code} tidak ditemukan."], 404);
            }
            return redirect()->back()->with('error', "Entitas {$code} tidak ditemukan.");
        }

        if (!$entity->isConfigured()) {
            $msg = "Kredensial Odoo untuk {$entity->name} belum lengkap. Silakan lengkapi pengaturan koneksi terlebih dahulu.";
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return redirect()->back()->with('error', $msg);
        }

        $category = $request->input('category', 'all');

        try {
            $service = OdooSyncService::fromEntity($entity);
            $result = $service->syncEmployees($entity, null, $category);

            ActivityLogger::sync("Odoo ERP ({$code})", "Sinkronisasi data karyawan Odoo entitas {$code} (Kategori: {$category}, Baru: " . ($result['created'] ?? 0) . ", Update: " . ($result['updated'] ?? 0) . ")", $result);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => $result['success'],
                    'message' => $result['message'],
                    'data'    => $result,
                ]);
            }

            return redirect()
                ->route('odoo.setting.index', ['tab' => $code])
                ->with($result['success'] ? 'success' : 'warning', $result['message']);

        } catch (\Throwable $e) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Gagal sinkronisasi: ' . $e->getMessage()], 500);
            }
            return redirect()
                ->route('odoo.setting.index', ['tab' => $code])
                ->with('error', 'Gagal sinkronisasi: ' . $e->getMessage());
        }
    }

    /**
     * Sync all configured & active entities sequentially.
     * Supports category filtering: 'all', 'inhouse', 'ratecard'.
     */
    public function syncAll(Request $request)
    {
        $activeEntities = OdooEntity::where('is_active', true)->get()->filter->isConfigured();

        if ($activeEntities->isEmpty()) {
            $msg = 'Tidak ada entitas aktif dengan kredensial Odoo yang lengkap.';
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return redirect()->back()->with('warning', $msg);
        }

        $category = $request->input('category', 'all');
        $categoryLabel = match($category) {
            'inhouse'  => 'Inhouse Saja',
            'ratecard' => 'RateCard Saja',
            default    => 'Semua Kategori',
        };

        $totalCreated  = 0;
        $totalUpdated  = 0;
        $allErrors     = [];
        $syncedCodes   = [];

        foreach ($activeEntities as $entity) {
            try {
                $service = OdooSyncService::fromEntity($entity);
                $res = $service->syncEmployees($entity, null, $category);
                $totalCreated  += $res['created'] ?? 0;
                $totalUpdated  += $res['updated'] ?? 0;
                $syncedCodes[]  = $entity->code;
                if (!empty($res['errors'])) {
                    $allErrors = array_merge($allErrors, $res['errors']);
                }
            } catch (\Throwable $e) {
                $allErrors[] = "[{$entity->code}] " . $e->getMessage();
            }
        }

        $entitiesStr = implode(', ', $syncedCodes);
        $summary = "Sinkronisasi selesai ({$categoryLabel}) untuk entitas ({$entitiesStr}). Total Baru: {$totalCreated} | Diperbarui: {$totalUpdated}" . (count($allErrors) > 0 ? " | Error: " . count($allErrors) : "");

        ActivityLogger::sync('Odoo ERP (Semua)', "Sinkronisasi massal seluruh entitas Odoo ({$entitiesStr}). Total Baru: {$totalCreated}, Diperbarui: {$totalUpdated}", [
            'category' => $category,
            'entities' => $entitiesStr,
            'created' => $totalCreated,
            'updated' => $totalUpdated,
            'errors' => count($allErrors),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => empty($allErrors),
                'message' => $summary,
                'data'    => [
                    'category' => $category,
                    'created'  => $totalCreated,
                    'updated'  => $totalUpdated,
                    'errors'   => $allErrors,
                ],
            ]);
        }

        return redirect()
            ->route('odoo.setting.index')
            ->with(empty($allErrors) ? 'success' : 'warning', $summary);
    }

    /**
     * Search and sync employee(s) by NIK from Odoo.
     * Supports single NIK or multiple NIKs (comma / newline separated),
     * and entity_code = 'ALL' (search across all active entities).
     */
    public function syncByNik(Request $request)
    {
        $rawNik = (string)$request->input('nik', '');
        // Split by commas, newlines, semicolons, or whitespace
        $niks = array_values(array_unique(array_filter(
            preg_split('/[\r\n,;]+/', trim($rawNik)),
            fn($val) => trim($val) !== ''
        )));

        if (empty($niks)) {
            $niks = array_values(array_unique(array_filter(
                preg_split('/\s+/', trim($rawNik)),
                fn($val) => trim($val) !== ''
            )));
        }

        $niks = array_map('trim', $niks);

        if (empty($niks)) {
            $msg = 'Mohon masukkan setidaknya satu Nomor Induk Karyawan (NIK / NIP).';
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return redirect()->back()->with('error', $msg);
        }

        $entityCode = strtoupper(trim((string)$request->input('entity_code', 'ALL')));

        if ($entityCode === 'ALL' || empty($entityCode)) {
            $entities = OdooEntity::where('is_active', true)->get()->filter->isConfigured();
            if ($entities->isEmpty()) {
                $msg = 'Tidak ada entitas Odoo aktif dengan kredensial lengkap yang siap disinkronkan. Mohon periksa konfigurasi Odoo ERP.';
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $msg], 422);
                }
                return redirect()->back()->with('error', $msg);
            }
        } else {
            $entity = OdooEntity::where('code', $entityCode)->first();
            if (!$entity) {
                $msg = "Entitas {$entityCode} tidak ditemukan.";
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $msg], 404);
                }
                return redirect()->back()->with('error', $msg);
            }
            if (!$entity->isConfigured()) {
                $msg = "Kredensial Odoo untuk entitas {$entity->name} ({$entity->code}) belum lengkap. Silakan lengkapi pengaturan koneksi terlebih dahulu.";
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $msg], 422);
                }
                return redirect()->back()->with('error', $msg);
            }
            $entities = collect([$entity]);
        }

        $results = [];
        $foundCount = 0;
        $notFoundCount = 0;

        foreach ($niks as $nik) {
            $nikFound = false;
            $lastMessage = '';

            foreach ($entities as $targetEntity) {
                try {
                    $service = OdooSyncService::fromEntity($targetEntity);
                    if (!$service) {
                        continue;
                    }

                    $res = $service->syncSingleEmployee($targetEntity, $nik);

                    if (!empty($res['success']) && !empty($res['data'])) {
                        $empData = $res['data'];
                        $employeePayload = array_merge($empData, [
                            'departemen' => $empData['divisi'] ?? '-',
                            'entitas'    => $empData['entity'] ?? $targetEntity->code,
                        ]);
                        $results[] = [
                            'nik'      => $nik,
                            'success'  => true,
                            'message'  => $res['message'],
                            'entity'   => $targetEntity->code,
                            'data'     => $empData,
                            'employee' => $employeePayload,
                            'action'   => ($empData['is_new'] ?? false) ? 'created' : 'updated',
                        ];
                        $nikFound = true;
                        $foundCount++;
                        break;
                    } else {
                        $lastMessage = $res['message'] ?? "NIK '{$nik}' tidak ditemukan di entitas {$targetEntity->code}.";
                    }
                } catch (\Throwable $e) {
                    $lastMessage = "[{$targetEntity->code}] " . $e->getMessage();
                }
            }

            if (!$nikFound) {
                $results[] = [
                    'nik'     => $nik,
                    'success' => false,
                    'message' => $lastMessage ?: "NIK '{$nik}' tidak ditemukan di " . ($entityCode === 'ALL' ? "semua entitas Odoo yang aktif." : "entitas {$entityCode}."),
                    'data'    => null,
                ];
                $notFoundCount++;
            }
        }

        // Single NIK: return exact schema expected by UI
        if (count($niks) === 1) {
            $single = $results[0];
            if ($request->wantsJson()) {
                return response()->json($single, $single['success'] ? 200 : 404);
            }

            return redirect()->back()->with($single['success'] ? 'success' : 'error', $single['message']);
        }

        // Multiple NIKs: return summary and items list
        $overallSuccess = ($foundCount > 0);
        $summaryMsg = "Sinkronisasi NIK selesai: {$foundCount} berhasil disinkronkan, {$notFoundCount} tidak ditemukan.";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => $overallSuccess,
                'message' => $summaryMsg,
                'summary' => [
                    'total'     => count($niks),
                    'found'     => $foundCount,
                    'not_found' => $notFoundCount,
                ],
                'results' => $results,
            ], $overallSuccess ? 200 : 404);
        }

        return redirect()->back()->with($overallSuccess ? 'success' : 'warning', $summaryMsg);
    }

    /**
     * Stream real-time terminal synchronization logs for a single entity.
     * Uses Server-Sent Events (SSE) to prevent HTTP timeouts.
     */
    public function streamSync(Request $request, string $code)
    {
        $category = $request->query('category', 'all');

        return response()->stream(function () use ($code, $category) {
            if (function_exists('apache_setenv')) {
                @apache_setenv('no-gzip', '1');
            }
            @ini_set('zlib.output_compression', '0');
            @ini_set('implicit_flush', '1');
            while (ob_get_level() > 0) {
                @ob_end_flush();
            }
            @ob_implicit_flush(1);
            set_time_limit(0);

            $sendEvent = function(string $type, string $message, ?array $meta = null) {
                $payload = [
                    'time'    => date('H:i:s'),
                    'type'    => $type,
                    'message' => $message,
                    'meta'    => $meta,
                ];
                echo "data: " . json_encode($payload) . "\n\n";
                if (ob_get_level() > 0) {
                    @ob_flush();
                }
                @flush();
            };

            $sendEvent('init', "Memulai konsol terminal sinkronisasi Odoo untuk entitas [{$code}]...");

            $entity = OdooEntity::where('code', $code)->first();
            if (!$entity) {
                $sendEvent('error', "Entitas [{$code}] tidak ditemukan dalam database.");
                $sendEvent('complete', "Proses dihentikan: Entitas tidak ditemukan.", ['success' => false]);
                return;
            }

            if (!$entity->isConfigured()) {
                $sendEvent('error', "Kredensial Odoo untuk {$entity->name} ({$code}) belum lengkap. Harap isi URL, DB, Username, dan API Key.");
                $sendEvent('complete', "Proses dihentikan: Konfigurasi belum lengkap.", ['success' => false]);
                return;
            }

            try {
                $sendEvent('info', "Menghubungkan ke server Odoo XML-RPC di {$entity->odoo_url} (Database: {$entity->odoo_db})...");
                $service = OdooSyncService::fromEntity($entity);
                $test = $service->testConnection();
                $sendEvent('success', "Autentikasi Odoo Berhasil! Terhubung sebagai UID {$test['uid']} (Odoo v{$test['server_version']}).");

                $result = $service->syncEmployees($entity, function(string $type, string $message, ?array $meta = null) use ($sendEvent) {
                    $sendEvent($type, $message, $meta);
                }, $category);

                $sendEvent('complete', $result['message'], [
                    'success'  => $result['success'],
                    'created'  => $result['created'] ?? 0,
                    'updated'  => $result['updated'] ?? 0,
                    'errors'   => count($result['errors'] ?? []),
                    'entity'   => $code,
                ]);

            } catch (\Throwable $e) {
                $sendEvent('error', "Terjadi kesalahan: " . $e->getMessage());
                $sendEvent('complete', "Gagal sinkronisasi: " . $e->getMessage(), ['success' => false]);
            }

        }, 200, [
            'Content-Type'      => 'text/event-stream',
            'Cache-Control'     => 'no-cache, no-transform',
            'Connection'        => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    /**
     * Stream real-time terminal synchronization logs across all active entities.
     */
    public function streamSyncAll(Request $request)
    {
        $category = $request->query('category', 'all');

        return response()->stream(function () use ($category) {
            if (function_exists('apache_setenv')) {
                @apache_setenv('no-gzip', '1');
            }
            @ini_set('zlib.output_compression', '0');
            @ini_set('implicit_flush', '1');
            while (ob_get_level() > 0) {
                @ob_end_flush();
            }
            @ob_implicit_flush(1);
            set_time_limit(0);

            $sendEvent = function(string $type, string $message, ?array $meta = null) {
                $payload = [
                    'time'    => date('H:i:s'),
                    'type'    => $type,
                    'message' => $message,
                    'meta'    => $meta,
                ];
                echo "data: " . json_encode($payload) . "\n\n";
                if (ob_get_level() > 0) {
                    @ob_flush();
                }
                @flush();
            };

            $categoryLabel = match($category) {
                'inhouse'  => 'Inhouse Saja',
                'ratecard' => 'RateCard Saja',
                default    => 'Semua Kategori (Inhouse + RateCard)',
            };

            $sendEvent('init', "Memulai konsol terminal sinkronisasi seluruh entitas Odoo ({$categoryLabel})...");

            $activeEntities = OdooEntity::where('is_active', true)->orderBy('id')->get()->filter->isConfigured()->values();

            if ($activeEntities->isEmpty()) {
                $sendEvent('error', "Tidak ada entitas aktif dengan konfigurasi Odoo yang lengkap.");
                $sendEvent('complete', "Proses dihentikan: Tidak ada entitas aktif.", ['success' => false]);
                return;
            }

            $totalEntities = $activeEntities->count();
            $sendEvent('info', "Ditemukan {$totalEntities} entitas aktif terkonfigurasi: [" . $activeEntities->pluck('code')->implode(', ') . "].");

            $grandCreated = 0;
            $grandUpdated = 0;
            $allErrors = [];

            foreach ($activeEntities as $idx => $entity) {
                $num = $idx + 1;
                $sendEvent('entity_start', "==================================================", ['entity' => $entity->code]);
                $sendEvent('entity_start', ">>> [{$num}/{$totalEntities}] MEMPROSES ENTITAS {$entity->code} ({$entity->name}) <<<", ['entity' => $entity->code]);
                $sendEvent('entity_start', "==================================================", ['entity' => $entity->code]);

                try {
                    $service = OdooSyncService::fromEntity($entity);
                    $test = $service->testConnection();
                    $sendEvent('success', "[{$entity->code}] Terhubung ke Odoo v{$test['server_version']} sebagai UID {$test['uid']}.");

                    $res = $service->syncEmployees($entity, function(string $type, string $message, ?array $meta = null) use ($sendEvent) {
                        $sendEvent($type, $message, $meta);
                    }, $category);

                    $created = $res['created'] ?? 0;
                    $updated = $res['updated'] ?? 0;
                    $grandCreated += $created;
                    $grandUpdated += $updated;

                    if (!empty($res['errors'])) {
                        $allErrors = array_merge($allErrors, $res['errors']);
                    }

                    $sendEvent('entity_end', "✅ [{$entity->code}] Selesai. Baru: {$created} | Diperbarui: {$updated}", [
                        'entity'        => $entity->code,
                        'grand_created' => $grandCreated,
                        'grand_updated' => $grandUpdated,
                    ]);

                } catch (\Throwable $e) {
                    $errMsg = "[{$entity->code}] Gagal: " . $e->getMessage();
                    $allErrors[] = $errMsg;
                    $sendEvent('error', "❌ {$errMsg}", ['entity' => $entity->code]);
                }
            }

            $summary = "Sinkronisasi seluruh entitas selesai! Total Karyawan Baru: {$grandCreated} | Diperbarui: {$grandUpdated}" . (count($allErrors) > 0 ? " | Error: " . count($allErrors) : "");
            $sendEvent('complete', $summary, [
                'success'       => empty($allErrors),
                'grand_created' => $grandCreated,
                'grand_updated' => $grandUpdated,
                'total_errors'  => count($allErrors),
            ]);

        }, 200, [
            'Content-Type'      => 'text/event-stream',
            'Cache-Control'     => 'no-cache, no-transform',
            'Connection'        => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    /**
     * Stream real-time synchronization by NIK.
     */
    public function streamSyncNik(Request $request)
    {
        $rawNik = (string)$request->input('nik', '');
        $entityCode = (string)$request->input('entity_code', 'ALL');

        return response()->stream(function () use ($rawNik, $entityCode) {
            if (function_exists('apache_setenv')) {
                @apache_setenv('no-gzip', '1');
            }
            @ini_set('zlib.output_compression', '0');
            @ini_set('implicit_flush', '1');
            while (ob_get_level() > 0) {
                @ob_end_flush();
            }
            @ob_implicit_flush(1);
            set_time_limit(0);

            $sendEvent = function(string $type, string $message, ?array $meta = null) {
                $payload = [
                    'time'    => date('H:i:s'),
                    'type'    => $type,
                    'message' => $message,
                    'meta'    => $meta,
                ];
                echo "data: " . json_encode($payload) . "\n\n";
                if (ob_get_level() > 0) {
                    @ob_flush();
                }
                @flush();
            };

            $niks = array_values(array_unique(array_filter(
                preg_split('/[\r\n,;]+/', trim($rawNik)),
                fn($val) => trim($val) !== ''
            )));

            if (empty($niks)) {
                $sendEvent('error', "Masukkan minimal satu NIK atau NIP yang valid.");
                $sendEvent('complete', "Proses dibatalkan: NIK kosong.", ['success' => false]);
                return;
            }

            $sendEvent('init', "Memulai pencarian & sinkronisasi untuk " . count($niks) . " NIK...");

            $entities = ($entityCode === 'ALL')
                ? OdooEntity::where('is_active', true)->get()->filter->isConfigured()->values()
                : OdooEntity::where('code', $entityCode)->get()->filter->isConfigured()->values();

            if ($entities->isEmpty()) {
                $sendEvent('error', "Tidak ada entitas aktif dengan kredensial Odoo yang lengkap.");
                $sendEvent('complete', "Gagal: Entitas tidak tersedia.", ['success' => false]);
                return;
            }

            $successCount = 0;
            $failCount = 0;

            foreach ($niks as $i => $singleNik) {
                $num = $i + 1;
                $found = false;
                $sendEvent('info', "[{$num}/" . count($niks) . "] Mencari NIK {$singleNik} di server Odoo...");

                foreach ($entities as $entity) {
                    try {
                        $service = OdooSyncService::fromEntity($entity);
                        $res = $service->syncSingleEmployee($entity, $singleNik);
                        if ($res['success']) {
                            $found = true;
                            $successCount++;
                            $actionLabel = $res['action'] === 'created' ? 'DIBUAT (BARU)' : 'DIPERBARUI';
                            $empName = $res['employee']->nama_karyawan ?? $singleNik;
                            $empJob = $res['employee']->jabatan ?? 'Staff';
                            $empType = $res['employee']->tipe_karyawan ?? 'Inhouse';
                            $sendEvent('item_create', "✅ [{$entity->code}] NIK {$singleNik} - {$empName} ({$empJob} | {$empType}) -> {$actionLabel}", [
                                'action'   => $res['action'],
                                'entity'   => $entity->code,
                                'nik'      => $singleNik,
                                'name'     => $empName,
                                'success'  => true,
                            ]);
                            break;
                        }
                    } catch (\Throwable $e) {
                        // continue to next entity
                    }
                }

                if (!$found) {
                    $failCount++;
                    $sendEvent('item_error', "⚠️ NIK {$singleNik} tidak ditemukan pada entitas aktif di Odoo.", [
                        'nik'     => $singleNik,
                        'success' => false,
                    ]);
                }
            }

            $summary = "Pencarian NIK Selesai. Ditemukan & Tersinkron: {$successCount} | Tidak Ditemukan: {$failCount}";
            $sendEvent('complete', $summary, [
                'success' => $successCount > 0,
                'created' => $successCount,
                'errors'  => $failCount,
            ]);

        }, 200, [
            'Content-Type'      => 'text/event-stream',
            'Cache-Control'     => 'no-cache, no-transform',
            'Connection'        => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    /**
     * Clean up duplicate employees in database based on NIK.
     */
    public function cleanupDuplicates(Request $request)
    {
        try {
            $cleaned = OdooSyncService::cleanupDuplicateEmployees();

            ActivityLogger::log('DELETE', 'Integrasi Odoo', "Membersihkan {$cleaned} data karyawan duplikat berdasarkan NIK", null, [
                'cleaned_count' => $cleaned
            ]);

            $msg = $cleaned > 0
                ? "Pembersihan berhasil! {$cleaned} data karyawan duplikat telah digabungkan dan dirapikan."
                : "Database rapi. Tidak ditemukan data karyawan duplikat berdasarkan NIK.";

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => $msg, 'cleaned' => $cleaned]);
            }

            return redirect()->route('odoo.setting.index')->with('success', $msg);

        } catch (\Throwable $e) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Gagal membersihkan duplikat: ' . $e->getMessage()], 500);
            }
            return redirect()->route('odoo.setting.index')->with('error', 'Gagal membersihkan duplikat: ' . $e->getMessage());
        }
    }
}
