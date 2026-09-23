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
    protected $signature = 'ai:cron-analyzer {--limit=2 : Jumlah kandidat yang diproses per eksekusi (default: 2)} {--interval=30 : Jeda target per kandidat dalam detik (default: 30)} {--candidate_id= : ID kandidat spesifik untuk dianalisis} {--force : Paksa analisa ulang meski sudah ada skor}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto Analisa CV Kandidat menggunakan AI Gemini (1 kandidat per 30 detik / 2 per menit)';

    /**
     * Execute the console command.
     */
    public function handle(AiAnalyzerService $analyzerService)
    {
        $this->info("=================================================");
        $this->info("=== ASystem Cron AI CV Analyzer Started ===");
        $this->info("Time: " . now('Asia/Jakarta')->translatedFormat('d M Y H:i:s') . " WIB");
        $this->info("Pace: 1 kandidat per 30 detik (1 menit 2 kandidat)");
        $this->info("=================================================");

        $specificId = $this->option('candidate_id');
        $force = $this->option('force');
        $limit = intval($this->option('limit') ?: 2);
        $interval = intval($this->option('interval') ?: 30);

        if ($specificId) {
            $candidates = Candidate::where('id', $specificId)->get();
        } else {
            $query = Candidate::whereRaw("LOWER(TRIM(jenis)) = 'job portal'")
                ->where(function ($q) {
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

            // Urutkan kandidat yang pertama masuk (paling awal mendaftar)
            $candidates = $query->orderByRaw("CASE 
                WHEN created_at IS NOT NULL AND created_at > '1970-01-01' THEN created_at 
                WHEN updated_at IS NOT NULL AND updated_at > '1970-01-01' THEN updated_at 
                ELSE '9999-12-31' 
            END ASC, id ASC")
            ->limit($limit)
            ->get();
        }

        if ($candidates->isEmpty()) {
            $this->comment("INFO: Tidak ada kandidat Job Portal dengan skor 0 yang membutuhkan analisa AI.");
            \Illuminate\Support\Facades\Cache::put('ai_analyzer_current_status', [
                'is_processing' => false,
                'status_text'   => 'Standby (Semua antrean CV kandidat Job Portal telah selesai dianalisis)',
            ], 180);
            return 0;
        }

        $count = $candidates->count();
        $this->info("INFO: Ditemukan $count kandidat untuk diproses (Target ritme: 1 kandidat per $interval detik)...");

        $successCount = 0;
        $failedCount = 0;

        foreach ($candidates as $index => $candidate) {
            $startTime = microtime(true);
            $this->line("-------------------------------------------------");
            $this->info("Memproses Kandidat [" . ($index + 1) . "/{$count}] ID #{$candidate->id} - {$candidate->full_name} ({$candidate->applied_job})");

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

            $elapsed = microtime(true) - $startTime;
            $sleepTime = max(0, $interval - $elapsed);

            // Jeda antar kandidat jika masih ada antrean berikutnya dalam batch ini
            if ($index < $count - 1 && $sleepTime > 0) {
                $sec = (int) ceil($sleepTime);
                $this->comment("  -> Memberikan jeda {$sec}s menuju kandidat berikutnya (Kecepatan: 1 kandidat / $interval detik)...");
                \Illuminate\Support\Facades\Cache::put('ai_analyzer_current_status', [
                    'is_processing' => false,
                    'status_text'   => "Jeda {$sec}s sebelum memproses kandidat berikutnya (Kecepatan: 1/30 detik)...",
                    'cooldown_sec'  => $sec,
                ], 120);
                sleep($sec);
            }
        }

        $this->line("=================================================");
        $this->info("=== Cron AI Selesai. Sukses: $successCount, Tertunda/Gagal: $failedCount ===");
        $this->line("=================================================");

        return 0;
    }
}
