<?php

namespace App\Console\Commands;

use App\Models\Candidate;
use App\Services\AiAnalyzerService;
use Illuminate\Console\Command;

class CronAiAnalyzerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai:cron-analyzer {--limit=3 : Jumlah kandidat yang diproses per eksekusi} {--candidate_id= : ID kandidat spesifik untuk dianalisis} {--force : Paksa analisa ulang meski sudah ada skor}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto Analisa CV Kandidat menggunakan AI Gemini (Smart Rotation & Cooldown) dan Sumopod Fallback';

    /**
     * Execute the console command.
     */
    public function handle(AiAnalyzerService $analyzerService)
    {
        $this->info("=================================================");
        $this->info("=== ASystem Cron AI CV Analyzer Started ===");
        $this->info("Time: " . now()->translatedFormat('d M Y H:i:s') . " WIB");
        $this->info("=================================================");

        $specificId = $this->option('candidate_id');
        $force = $this->option('force');
        $limit = intval($this->option('limit') ?: 3);

        if ($specificId) {
            $candidates = Candidate::where('id', $specificId)->get();
        } else {
            $query = Candidate::where(function ($q) {
                $q->whereNotNull('cv_path')
                  ->where('cv_path', '!=', '')
                  ->where('cv_path', '!=', '-');
            });

            if (!$force) {
                $query->where(function ($q) {
                    $q->whereNull('ai_score')
                      ->orWhere('ai_score', 0);
                })->where(function ($q) {
                    $q->whereNull('ai_cv_analysis')
                      ->orWhere('ai_cv_analysis', 'not like', '%file_error%');
                });
            }


            // Diurutkan dari yang paling awal masuk (oldest first)
            $candidates = $query->orderBy('id', 'asc')
                                ->limit($limit)
                                ->get();
        }

        if ($candidates->isEmpty()) {
            $this->comment("INFO: Tidak ada kandidat antrean dengan skor 0 yang membutuhkan analisa AI.");
            return 0;
        }

        $count = $candidates->count();
        $this->info("INFO: Ditemukan $count kandidat untuk diproses...");

        $successCount = 0;
        $failedCount = 0;

        foreach ($candidates as $candidate) {
            $this->line("-------------------------------------------------");
            $this->info("Memproses Kandidat ID #{$candidate->id} - {$candidate->full_name} ({$candidate->applied_job})");

            $res = $analyzerService->analyzeCandidate($candidate, function ($msg, $level) {
                if ($level === 'error') {
                    $this->error("  $msg");
                } elseif ($level === 'warning') {
                    $this->warn("  $msg");
                } elseif ($level === 'info') {
                    $this->line("  $msg");
                } else {
                    $this->info("  $msg");
                }
            });

            if ($res['success']) {
                $successCount++;
                $this->info("  -> Hasil: Score {$res['score']} ({$res['category']}) via {$res['provider']}");
            } else {
                $failedCount++;
                $this->warn("  -> Tertunda/Gagal: {$res['message']}");
            }
        }

        $this->line("=================================================");
        $this->info("=== Cron AI Selesai. Sukses: $successCount, Tertunda/Gagal: $failedCount ===");
        $this->line("=================================================");

        return 0;
    }
}
