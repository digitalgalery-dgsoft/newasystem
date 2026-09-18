<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\OdooEntity;
use App\Models\OdooSyncLog;
use App\Services\OdooSyncService;
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
     * Search and sync a single employee by NIK from Odoo.
     */
    public function syncByNik(Request $request)
    {
        $validated = $request->validate([
            'nik'         => 'required|string',
            'entity_code' => 'required|string',
        ]);

        $entity = OdooEntity::where('code', strtoupper($validated['entity_code']))->first();
        if (!$entity) {
            $msg = "Entitas {$validated['entity_code']} tidak ditemukan.";
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

        try {
            $service = OdooSyncService::fromEntity($entity);
            $result = $service->syncSingleEmployee($entity, $validated['nik']);

            if ($request->wantsJson()) {
                return response()->json($result, $result['success'] ? 200 : 404);
            }

            return redirect()
                ->route('odoo.setting.index', ['tab' => $entity->code])
                ->with($result['success'] ? 'success' : 'error', $result['message']);

        } catch (\Throwable $e) {
            $errMsg = 'Gagal sync NIK: ' . $e->getMessage();
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $errMsg], 500);
            }

            return redirect()
                ->route('odoo.setting.index', ['tab' => $entity->code])
                ->with('error', $errMsg);
        }
    }

    /**
     * Clean up duplicate employees in database based on NIK.
     */
    public function cleanupDuplicates(Request $request)
    {
        try {
            $cleaned = OdooSyncService::cleanupDuplicateEmployees();

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
