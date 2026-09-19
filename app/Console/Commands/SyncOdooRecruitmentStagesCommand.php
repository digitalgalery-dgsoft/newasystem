<?php

namespace App\Console\Commands;

use App\Services\OdooRecruitmentSyncService;
use Illuminate\Console\Command;

class SyncOdooRecruitmentStagesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'odoo:sync-portal-stages 
                            {--limit=1000 : Batas jumlah kandidat yang diproses per eksekusi} 
                            {--all : Sertakan juga kandidat yang sudah berstatus arsip} 
                            {--silent : Jalankan tanpa output verbose}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi status tahapan seleksi kandidat portal dengan Odoo Recruitment (hr.applicant) berdasarkan NIK dan auto-archive kandidat tidak aktif > 14 hari';

    /**
     * Execute the console command.
     */
    public function handle(OdooRecruitmentSyncService $syncService): int
    {
        $limit = (int)$this->option('limit');
        $includeArchived = (bool)$this->option('all');
        $isSilent = (bool)$this->option('silent');

        if (!$isSilent) {
            $this->info("================================================================================");
            $this->info("🚀 SINKRONISASI TAHAPAN KANDIDAT PORTAL DENGAN ODOO RECRUITMENT");
            $this->info("📌 Mencocokkan NIK ke hr.applicant Odoo (AMK, AKP, ATK, ABO, ATB)");
            $this->info("📌 Tahapan Interview -> Status Interview, Joined -> Status Terima");
            $this->info("📌 Kandidat > 14 hari tanpa pembaruan -> Otomatis Pindah ke Arsip");
            $this->info("📅 Waktu Eksekusi: " . date('Y-m-d H:i:s T'));
            $this->info("================================================================================");
        }

        $progressCallback = function (string $type, string $message, array $meta = []) use ($isSilent) {
            if ($isSilent && in_array($type, ['info', 'batch'])) {
                return;
            }

            switch ($type) {
                case 'stage_change':
                    $this->info("  {$message}");
                    break;
                case 'auto_archive':
                    $this->warn("  {$message}");
                    break;
                case 'warning':
                    $this->warn("  ⚠️ {$message}");
                    break;
                case 'error':
                    $this->error("  ❌ {$message}");
                    break;
                case 'success':
                    $this->info("  ✅ {$message}");
                    break;
                default:
                    $this->line("  ℹ️ {$message}");
                    break;
            }
        };

        $result = $syncService->syncAllCandidates($progressCallback, $limit, $includeArchived);

        if (!$isSilent) {
            $this->newLine();
            $this->table(
                ['Metrik', 'Jumlah'],
                [
                    ['Total Kandidat Diperiksa', $result['total_checked'] ?? 0],
                    ['Cocok di Odoo', $result['matched'] ?? 0],
                    ['Beralih ke INTERVIEW', $result['moved_interview'] ?? 0],
                    ['Beralih ke TERIMA (Joined)', $result['moved_terima'] ?? 0],
                    ['Beralih ke ARSIP (Ditolak)', $result['moved_arsip'] ?? 0],
                    ['Auto-Arsip (> 14 Hari Tanpa Update)', $result['auto_archived'] ?? 0],
                ]
            );
            $this->info("================================================================================");
            $this->info("🏁 PROSES SINKRONISASI ODOO RECRUITMENT SELESAI");
            $this->info("================================================================================");
        }

        return 0;
    }
}
