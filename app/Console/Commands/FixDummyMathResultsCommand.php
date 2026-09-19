<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Candidate;
use App\Models\TestResult;
use App\Services\CandidateEvaluationDataService;

class FixDummyMathResultsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'math:fix-dummy-results
                            {--dry-run : Only show candidates that would be fixed without making changes}
                            {--candidate_id= : Target a specific candidate ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix candidate math test results who took dummy questions to Grade B (70%) based on real candidate data, ensuring legacy data remains untouched.';

    /**
     * Template Jawaban Grade B (Skor 70%, 7 Benar, 3 Salah)
     * Diadopsi dari kandidat riil di database (contoh: Refy Nur Mariska / Luluk Nurwati)
     */
    protected array $templateAnswers = [
        1 => '170000',  // BENAR (Lampu Philips diskon 15% dari 200.000)
        2 => '247500',  // SALAH (Bedak Loreal: salah hitung diskon kedua, kunci 495000)
        3 => '71%',     // SALAH (Target SPG Dancow: pembulatan tanpa desimal, kunci 71.43%)
        4 => 'A',       // BENAR (Bagas TimTam: pilihan ganda A)
        5 => 'D',       // BENAR (Boneka Putri: keuntungan 60% = pilihan ganda D)
        6 => '8,4',     // BENAR (Deret 24, 20, 16, 12, ... = 8,4)
        7 => 'B',       // BENAR (Uang Ibu sisa 9.000 = pilihan ganda B)
        8 => 'A',       // BENAR (Handycam Angga sisa 1.000.000 = pilihan ganda A)
        9 => '100000',  // BENAR (Pelembab Loreal beli ke-2 diskon 75% = 100.000)
        10 => '42%',    // SALAH (Target SPG Arnots: pembulatan tanpa desimal, kunci 41.67%)
    ];

    public function handle()
    {
        $isDryRun = (bool) $this->option('dry-run');
        $targetCandidateId = $this->option('candidate_id');

        $this->info("===============================================================");
        $this->info("   SINKRONISASI HASIL TES MATEMATIKA DUMMY KE NILAI B (70%)    ");
        $this->info("===============================================================");
        if ($isDryRun) {
            $this->warn("MODE: DRY RUN (Tidak ada data yang akan diubah di database)");
        }

        // 1. Cari kandidat yang terlanjur mengerjakan soal dummy
        $detectedCandidateIds = [];

        // Deteksi dari tb_hasilmath dengan sidik jari jawaban dummy
        if (Schema::hasTable('tb_hasilmath')) {
            $dummyRows = DB::table('tb_hasilmath')
                ->where(function ($query) {
                    $query->where(function ($q) {
                        $q->where('id_soal', 4)->where('jawaban', '128');
                    })->orWhere(function ($q) {
                        $q->where('id_soal', 6)->where('jawaban', '170');
                    })->orWhere(function ($q) {
                        $q->where('id_soal', 10)->where('jawaban', '360');
                    });
                })
                ->pluck('id_kandidat')
                ->unique()
                ->toArray();

            $detectedCandidateIds = array_merge($detectedCandidateIds, $dummyRows);
        }

        // Deteksi dari test_results dengan soal deret dummy unik
        if (Schema::hasTable('test_results')) {
            $dummyTestResults = DB::table('test_results')
                ->where('test_type', 'math')
                ->where(function ($q) {
                    $q->where('test_details', 'LIKE', '%4, 8, 16, 32, 64%')
                      ->orWhere('test_details', 'LIKE', '%150 + 25 x 4 - 80%');
                })
                ->pluck('candidate_id')
                ->unique()
                ->toArray();

            $detectedCandidateIds = array_merge($detectedCandidateIds, $dummyTestResults);
        }

        $detectedCandidateIds = array_unique(array_filter($detectedCandidateIds));

        // Jika user menyertakan candidate_id secara eksplisit
        if ($targetCandidateId) {
            $targetCandidateId = intval($targetCandidateId);
            if (!in_array($targetCandidateId, $detectedCandidateIds)) {
                $candidateExists = Candidate::find($targetCandidateId);
                if (!$candidateExists) {
                    $this->error("Kandidat dengan ID {$targetCandidateId} tidak ditemukan!");
                    return 1;
                }
                $this->warn("Menambahkan kandidat ID {$targetCandidateId} sesuai argumen --candidate_id.");
                $detectedCandidateIds[] = $targetCandidateId;
            } else {
                $detectedCandidateIds = [$targetCandidateId];
            }
        }

        if (empty($detectedCandidateIds)) {
            $this->info("Tidak ditemukan kandidat dengan hasil tes matematika dummy.");
            $this->info("Semua data di sistem adalah data resmi yang sudah sesuai. Tidak ada yang diubah.");
            return 0;
        }

        $this->info("Ditemukan " . count($detectedCandidateIds) . " kandidat yang terlanjur mengerjakan soal dummy:");
        $evalService = new CandidateEvaluationDataService();

        // Ambil data soal master tb_math untuk breakdown CBT
        $mathQuestions = [];
        if (Schema::hasTable('tb_math')) {
            $mathQuestions = DB::table('tb_math')->orderBy('id')->get()->keyBy('id');
        }

        foreach ($detectedCandidateIds as $candId) {
            $candidate = Candidate::find($candId);
            if (!$candidate) {
                $this->warn("Kandidat ID {$candId} tidak ditemukan di tabel candidates. Melewati.");
                continue;
            }

            $evalBefore = $evalService->getEvaluationData($candidate);
            $mathAnswers = DB::table('tb_hasilmath')->where('id_kandidat', $candidate->id)->pluck('jawaban', 'id_soal')->toArray();

            $this->line("---------------------------------------------------------------");
            $this->line("Kandidat: [ID: {$candidate->id}] {$candidate->full_name} (NIK: {$candidate->nik})");
            $this->line("  Posisi: {$candidate->applied_job}");
            $this->line("  Status Saat Ini -> Grade: {$evalBefore['mathGrade']} | Skor: {$evalBefore['mathScorePercent']}% | Benar: {$evalBefore['mathCorrectCount']}/10");
            $this->line("  Jawaban: " . json_encode($mathAnswers));

            // Lewati jika kandidat sudah memiliki nilai A atau B (sudah sesuai)
            if ($evalBefore['mathScorePercent'] >= 70) {
                $this->comment("  [LEWATI] Kandidat sudah memiliki Nilai {$evalBefore['mathGrade']} ({$evalBefore['mathScorePercent']}%). Tidak diubah agar data yang sudah sesuai tetap aman.");
                continue;
            }

            if ($isDryRun) {
                $this->warn("  [DRY-RUN AKAN DIUBAH] -> Akan disinkronkan ke Grade B (70%, 7 Benar, 3 Salah)");
                continue;
            }

            // Ambil durasi dan tes_ke lama agar waktu pengerjaan kandidat tidak hilang
            $currentDuration = '00:04:54';
            $currentTesKe = max(1, intval($candidate->tes_ke ?? 1));

            if (Schema::hasTable('tb_hasilmath')) {
                $existingMath = DB::table('tb_hasilmath')
                    ->where('id_kandidat', $candidate->id)
                    ->orderBy('id', 'desc')
                    ->first();
                if ($existingMath && !empty($existingMath->waktu_pengerjaan)) {
                    $currentDuration = $existingMath->waktu_pengerjaan;
                }
                if ($existingMath && !empty($existingMath->tes_ke)) {
                    $currentTesKe = intval($existingMath->tes_ke);
                }
            }

            if (!empty($candidate->tes_matematika)) {
                $currentDuration = $candidate->tes_matematika;
            }

            // 1. Simpan ke tb_hasilmath (10 butir soal Grade B)
            if (Schema::hasTable('tb_hasilmath')) {
                // Hapus jawaban dummy lama untuk kandidat dan tes_ke ini
                DB::table('tb_hasilmath')
                    ->where('id_kandidat', $candidate->id)
                    ->where('tes_ke', $currentTesKe)
                    ->delete();

                foreach ($this->templateAnswers as $qId => $ans) {
                    DB::table('tb_hasilmath')->insert([
                        'id_kandidat' => $candidate->id,
                        'id_soal' => $qId,
                        'jawaban' => strval($ans),
                        'waktu_pengerjaan' => $currentDuration,
                        'tes_ke' => $currentTesKe,
                        'created_at' => now(),
                    ]);
                }
            }

            // 2. Susun breakdown untuk TestResult
            $breakdown = [];
            $correctKeys = [1, 4, 5, 6, 7, 8, 9]; // 7 butir benar
            foreach ($this->templateAnswers as $qId => $ans) {
                $mq = $mathQuestions->get($qId);
                $isCorrect = in_array($qId, $correctKeys);
                $breakdown[$qId] = [
                    'question' => $mq->question_text ?? ("Soal Matematika #" . $qId),
                    'user_answer' => $ans,
                    'correct_answer' => $mq->correct_answer ?? '',
                    'is_correct' => $isCorrect,
                ];
            }

            // Hitung detik durasi
            $parts = explode(':', $currentDuration);
            $durationSeconds = 294; // fallback ~5 menit
            if (count($parts) === 3) {
                $durationSeconds = ($parts[0] * 3600) + ($parts[1] * 60) + $parts[2];
            }

            // 3. Simpan ke TestResult
            TestResult::updateOrCreate(
                ['candidate_id' => $candidate->id, 'test_type' => 'math'],
                [
                    'score' => 70.0,
                    'duration_seconds' => $durationSeconds,
                    'test_details' => [
                        'score' => 70.0,
                        'correct_count' => 7,
                        'total_questions' => 10,
                        'duration_formatted' => $currentDuration,
                        'tes_ke' => $currentTesKe,
                        'breakdown' => $breakdown,
                    ],
                ]
            );

            // 4. Update tabel candidates
            $candidate->tes_matematika = $currentDuration;
            $candidate->tes_ke = $currentTesKe;
            $candidate->saveQuietly();

            // 5. Update tabel legacy tb_kandidat jika ada
            if (Schema::hasTable('tb_kandidat')) {
                DB::table('tb_kandidat')
                    ->where('id', $candidate->id)
                    ->orWhere(function ($q) use ($candidate) {
                        if (!empty($candidate->nik)) {
                            $q->where('no_ktp', $candidate->nik);
                        }
                    })
                    ->update([
                        'tes_matematika' => $currentDuration,
                        'tes_ke' => $currentTesKe,
                    ]);
            }

            // 6. Verifikasi evaluasi akhir
            $evalAfter = $evalService->getEvaluationData($candidate->fresh());
            $this->info("  ✔ BERHASIL DIUPDATE -> Grade: {$evalAfter['mathGrade']} | Skor: {$evalAfter['mathScorePercent']}% | Benar: {$evalAfter['mathCorrectCount']}/10 (Durasi: {$currentDuration})");
        }

        $this->line("---------------------------------------------------------------");
        if (!$isDryRun) {
            $this->info("Selesai. Seluruh kandidat dummy berhasil disesuaikan menjadi Nilai B (Grade B).");
            $this->info("Data kandidat lama yang sudah sesuai tetap aman 100% dan tidak mengalami perubahan.");
        }

        return 0;
    }
}
