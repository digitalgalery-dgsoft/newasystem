<?php

namespace App\Console\Commands;

use App\Models\OdooEntity;
use App\Models\OdooSyncLog;
use App\Services\OdooSyncService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class OdooSyncActiveCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'odoo:sync-active 
                            {--entity= : Sync specific Odoo Entity code (e.g. AMK, AKP, ATK, ABO, ATB)} 
                            {--category=all : Sync category: all, inhouse, or ratecard}
                            {--silent : Run without verbose itemized output}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hourly sync of active employees from Odoo (inserts new active employees only, skips existing NIKs)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $entityCode = $this->option('entity');
        $category   = strtolower($this->option('category') ?: 'all');
        $isSilent   = (bool) $this->option('silent');
        $batchId    = 'SYNC-ACTIVE-' . date('Ymd-His') . '-' . Str::random(6);

        $this->info("================================================================================");
        $this->info("🚀 MEMULAI CRON ODOO SYNC EMPLOYEE AKTIF (HOURLY) [Batch: {$batchId}]");
        $this->info("📌 Aturan: Hanya ambil employee aktif, NIK yang sudah ada TIDAK DIUPDATE (skip).");
        $this->info("📅 Waktu Eksekusi: " . date('Y-m-d H:i:s T'));
        $this->info("================================================================================");

        $query = OdooEntity::query();
        if (!empty($entityCode)) {
            $query->where('code', strtoupper(trim($entityCode)));
        } else {
            $query->where('is_active', true);
        }

        $entities = $query->get();

        if ($entities->isEmpty()) {
            $this->warn("⚠️ Tidak ada entitas Odoo aktif yang ditemukan.");
            return 0;
        }

        $totalGlobalCreated = 0;
        $totalGlobalUpdated = 0;
        $totalGlobalSkipped = 0;
        $globalErrors       = [];

        foreach ($entities as $entity) {
            $this->line("\n<fg=cyan;options=bold>▶ Menghubungkan ke Entitas: {$entity->name} ({$entity->code})...</>");

            if (!$entity->isConfigured()) {
                $this->warn("  ⚠️ Kredensial Odoo untuk {$entity->name} belum lengkap. Melewati entitas ini.");
                continue;
            }

            try {
                $service = OdooSyncService::fromEntity($entity);
                if (!$service) {
                    $this->error("  ❌ Gagal membuat instance OdooSyncService untuk {$entity->code}");
                    continue;
                }

                $timeStart = microtime(true);
                // updateExisting = false: NIK yang sudah masuk dan aktif di entitas sama tidak diupdate,
                // namun jika statusnya Resign atau berbeda entitas, OdooSyncService akan otomatis mengupdate (mutasi/reaktivasi)
                $res = $service->syncEmployees(
                    entity: $entity,
                    progressCallback: function (string $type, string $message, ?array $meta = null) use ($isSilent) {
                        if (!$isSilent) {
                            if ($type === 'item_create') {
                                $this->line("  <fg=green>{$message}</>");
                            } elseif ($type === 'item_update') {
                                $this->line("  <fg=cyan>{$message}</>");
                            } elseif ($type === 'error' || $type === 'item_error') {
                                $this->line("  <fg=red>{$message}</>");
                            }
                        }
                    },
                    category: $category,
                    updateExisting: false
                );
                $duration = round(microtime(true) - $timeStart, 2);

                $created = $res['created'] ?? 0;
                $updated = $res['updated'] ?? 0;
                $errors  = $res['errors'] ?? [];

                $totalGlobalCreated += $created;
                $totalGlobalUpdated += $updated;

                $this->line("  <fg=green>✓ Karyawan Baru Ditambahkan : {$created}</>");
                if ($updated > 0) {
                    $this->line("  <fg=cyan>✓ Karyawan Mutasi/Reaktivasi: {$updated}</>");
                }
                $this->line("  <fg=blue>✓ Durasi Eksekusi          : {$duration} detik</>");

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

                OdooSyncLog::create([
                    'entity_code' => $entity->code,
                    'status'      => 'failed',
                    'message'     => 'Hourly active cron exception: ' . $e->getMessage(),
                    'created_by'  => 'Cron Hourly Active',
                ]);
            }
        }

        $this->info("\n================================================================================");
        $this->info("📊 RINGKASAN CRON EMPLOYEE AKTIF (HOURLY)");
        $this->info("================================================================================");
        $this->line("<fg=green>Total Karyawan Baru Masuk  : {$totalGlobalCreated}</>");
        $this->line("<fg=cyan>Total Mutasi / Reaktivasi  : {$totalGlobalUpdated}</>");
        $this->line("<fg=" . (count($globalErrors) > 0 ? "red" : "green") . ">Total Error Terjadi        : " . count($globalErrors) . "</>");
        $this->info("================================================================================\n");

        return count($globalErrors) > 0 ? 1 : 0;
    }
}
