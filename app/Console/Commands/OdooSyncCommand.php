<?php

namespace App\Console\Commands;

use App\Models\OdooEntity;
use App\Models\OdooSyncLog;
use App\Services\OdooSyncService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class OdooSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'odoo:sync 
                            {--entity= : Sync specific Odoo Entity code (e.g. AMK, AKP, ATK, ABO, ATB)} 
                            {--category=all : Sync category: all, inhouse (AMK, AKP, ATK, ABO, ATB), or ratecard}
                            {--nik= : Check and sync a single employee by NIK}
                            {--trigger=cron : Trigger type (cron or manual)}
                            {--silent : Run without verbose itemized output}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automated synchronization of Employees & Principals from Odoo XML-RPC (Replikasi att-admin-v12)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $entityCode = $this->option('entity');
        $category   = strtolower($this->option('category') ?: 'all');
        $nik        = trim($this->option('nik') ?: '');
        $trigger    = $this->option('trigger') ?: 'cron';
        $isSilent   = (bool) $this->option('silent');
        $batchId    = 'SYNC-' . date('Ymd-His') . '-' . Str::random(6);

        $categoryLabel = match ($category) {
            'inhouse'  => 'Inhouse Saja (AMK, AKP, ATK, ABO, ATB)',
            'ratecard' => 'RateCard Saja',
            default    => 'Semua Kategori',
        };

        $this->info("================================================================================");
        $this->info("🚀 MEMULAI SINKRONISASI ODOO [Batch: {$batchId}, Trigger: {$trigger}]");
        $this->info("🎯 Mode: " . ($nik !== '' ? "Sync by NIK ({$nik})" : "Bulk Sync ({$categoryLabel})"));
        $this->info("📅 Waktu Eksekusi: " . date('Y-m-d H:i:s T') . " (Replikasi att-admin-v12)");
        $this->info("================================================================================");

        // Cari entitas yang akan disinkronkan
        $query = OdooEntity::query();
        if (!empty($entityCode)) {
            $query->where('code', strtoupper(trim($entityCode)));
        } else {
            $query->where('is_active', true);
        }

        $entities = $query->get();

        if ($entities->isEmpty()) {
            $this->warn("⚠️  Tidak ada entitas Odoo aktif yang ditemukan untuk disinkronkan.");
            return 0;
        }

        // =====================================================================
        // MODE 1: SYNC 1 EMPLOYEE BY NIK
        // =====================================================================
        if ($nik !== '') {
            $found = false;
            foreach ($entities as $entity) {
                if (!$entity->isConfigured()) {
                    continue;
                }

                $this->line("\n<fg=cyan;options=bold>▶ Mencari NIK '{$nik}' pada Entitas: {$entity->name} ({$entity->code})...</>");
                try {
                    $service = OdooSyncService::fromEntity($entity);
                    $result = $service->syncSingleEmployee($entity, $nik);

                    if ($result['success']) {
                        $emp = $result['employee'];
                        $this->info("  ✓ Berhasil Disinkronkan!");
                        $this->table(
                            ['Field', 'Value'],
                            [
                                ['NIK / NIP', $emp->nik],
                                ['Nama Lengkap', $emp->nama_karyawan],
                                ['Jabatan', $emp->jabatan ?? '-'],
                                ['Departemen', $emp->departemen ?? '-'],
                                ['Prinsiple Odoo', $emp->prinsiple ?? '-'],
                                ['Entitas Terpilih', $emp->entitas ?? '-'],
                                ['Tipe Karyawan', $emp->tipe_karyawan ?? '-'],
                                ['Status Aktif', $emp->status_aktif ? 'Aktif' : 'Non-Aktif'],
                                ['Action', $result['action'] ?? '-'],
                            ]
                        );
                        $found = true;
                        break;
                    } else {
                        $this->line("  <fg=yellow>ℹ {$result['message']}</>");
                    }
                } catch (\Throwable $e) {
                    $this->error("  ❌ Exception pada {$entity->code}: " . $e->getMessage());
                }
            }

            if (!$found) {
                $this->warn("\n⚠️  Karyawan dengan NIK '{$nik}' tidak ditemukan atau tidak aktif di entitas yang diperiksa.");
                return 1;
            }

            return 0;
        }

        // =====================================================================
        // MODE 2: BULK SYNC DENGAN FILTER KATEGORI (INHOUSE / RATECARD / ALL)
        // =====================================================================
        $totalGlobalCreated  = 0;
        $totalGlobalUpdated  = 0;
        $totalGlobalResigned = 0;
        $globalErrors        = [];

        foreach ($entities as $entity) {
            $this->line("\n<fg=cyan;options=bold>▶ Menghubungkan ke Entitas: {$entity->name} ({$entity->code})...</>");

            if (!$entity->isConfigured()) {
                $msg = "Kredensial Odoo untuk {$entity->name} belum lengkap. Melewati entitas ini.";
                $this->warn("  ⚠️  {$msg}");
                continue;
            }

            try {
                $service = OdooSyncService::fromEntity($entity);
                if (!$service) {
                    $this->error("  ❌ Gagal membuat instance OdooSyncService untuk {$entity->code}");
                    continue;
                }

                $timeStart = microtime(true);
                $res = $service->syncEmployees($entity, null, $category);
                $duration = round(microtime(true) - $timeStart, 2);

                $created  = $res['created'] ?? 0;
                $updated  = $res['updated'] ?? 0;
                $resigned = $res['resigned'] ?? 0;
                $errors   = $res['errors'] ?? [];

                $totalGlobalCreated  += $created;
                $totalGlobalUpdated  += $updated;
                $totalGlobalResigned += $resigned;

                if (!$isSilent) {
                    $this->line("  <fg=green>✓ Karyawan Baru: {$created}</>");
                    $this->line("  <fg=yellow>✓ Diperbarui: {$updated}</>");
                    $this->line("  <fg=magenta>✓ Resign / Non-Aktif: {$resigned}</>");
                    $this->line("  <fg=blue>✓ Durasi Eksekusi: {$duration} detik</>");
                }

                if (!empty($errors)) {
                    foreach ($errors as $err) {
                        $this->line("  <fg=red>✗ Error: {$err}</>");
                        $globalErrors[] = "[{$entity->code}] {$err}";
                    }
                }

                $this->info("  ✓ Selesai memproses {$entity->name} ({$entity->code})");

            } catch (\Throwable $e) {
                $errStr = "[{$entity->code}] " . $e->getMessage();
                $globalErrors[] = $errStr;
                $this->error("  ❌ Exception pada {$entity->code}: " . $e->getMessage());

                // Catat log kegagalan
                OdooSyncLog::create([
                    'entity_code' => $entity->code,
                    'status'      => 'failed',
                    'message'     => 'Cron exception: ' . $e->getMessage(),
                    'created_by'  => $trigger === 'cron' ? 'Cron Schedule' : 'Console User',
                ]);
            }
        }

        // Cleanup duplikasi jika diperlukan
        try {
            $cleaned = OdooSyncService::cleanupDuplicateEmployees();
            if ($cleaned > 0) {
                $this->info("🧹 Pembersihan Data: {$cleaned} record karyawan duplikat berhasil dirapikan.");
            }
        } catch (\Throwable $e) {
            // Ignore cleanup errors
        }

        $this->info("\n================================================================================");
        $this->info("📊 RINGKASAN SINKRONISASI ODOO ({$trigger})");
        $this->info("================================================================================");
        $this->line("<fg=green>Total Karyawan Baru  : {$totalGlobalCreated}</>");
        $this->line("<fg=yellow>Total Data Diperbarui: {$totalGlobalUpdated}</>");
        $this->line("<fg=magenta>Total Resign / Keluar: {$totalGlobalResigned}</>");
        $this->line("<fg=" . (count($globalErrors) > 0 ? "red" : "green") . ">Total Error Terjadi : " . count($globalErrors) . "</>");
        $this->info("================================================================================\n");

        return count($globalErrors) > 0 ? 1 : 0;
    }
}
