<?php

namespace App\Console\Commands;

use App\Models\OdooEntity;
use App\Models\OdooSyncLog;
use App\Services\OdooSyncService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class OdooSyncUpdatesResignsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'odoo:sync-updates-resigns 
                            {--entity= : Sync specific Odoo Entity code (e.g. AMK, AKP, ATK, ABO, ATB)} 
                            {--silent : Run without verbose itemized output}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Midnight sync to check active employee data updates and mark resigned employees from Odoo';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $entityCode = $this->option('entity');
        $isSilent   = (bool) $this->option('silent');
        $batchId    = 'SYNC-MIDNIGHT-' . date('Ymd-His') . '-' . Str::random(6);

        $this->info("================================================================================");
        $this->info("🌙 MEMULAI CRON ODOO SYNC UPDATE & RESIGN (MIDNIGHT) [Batch: {$batchId}]");
        $this->info("📌 Aturan: Periksa data karyawan aktif lokal, perbarui perubahan & tandai Resign jika non-aktif di Odoo.");
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

        $totalGlobalUpdated  = 0;
        $totalGlobalResigned = 0;
        $globalErrors        = [];

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
                $res = $service->syncUpdatesAndResigns(
                    entity: $entity,
                    progressCallback: function (string $type, string $message, ?array $meta = null) use ($isSilent) {
                        if (!$isSilent) {
                            if ($type === 'item_resign') {
                                $this->line("  <fg=yellow>{$message}</>");
                            } elseif ($type === 'item_update') {
                                $this->line("  <fg=cyan>{$message}</>");
                            } elseif ($type === 'error' || $type === 'item_error') {
                                $this->line("  <fg=red>{$message}</>");
                            }
                        }
                    }
                );
                $duration = round(microtime(true) - $timeStart, 2);

                $updated  = $res['updated'] ?? 0;
                $resigned = $res['resigned'] ?? 0;
                $errors   = $res['errors'] ?? [];

                $totalGlobalUpdated  += $updated;
                $totalGlobalResigned += $resigned;

                $this->line("  <fg=cyan>✓ Data Karyawan Diperbarui : {$updated}</>");
                $this->line("  <fg=yellow>✓ Karyawan Ditandai Resign : {$resigned}</>");
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
                    'message'     => 'Midnight updates/resigns cron exception: ' . $e->getMessage(),
                    'created_by'  => 'Cron Midnight Updates Resigns',
                ]);
            }
        }

        $this->info("\n================================================================================");
        $this->info("📊 RINGKASAN CRON UPDATE & RESIGN (MIDNIGHT)");
        $this->info("================================================================================");
        $this->line("<fg=cyan>Total Data Karyawan Diperbarui : {$totalGlobalUpdated}</>");
        $this->line("<fg=yellow>Total Karyawan Ditandai Resign : {$totalGlobalResigned}</>");
        $this->line("<fg=" . (count($globalErrors) > 0 ? "red" : "green") . ">Total Error Terjadi            : " . count($globalErrors) . "</>");
        $this->info("================================================================================\n");

        return count($globalErrors) > 0 ? 1 : 0;
    }
}
