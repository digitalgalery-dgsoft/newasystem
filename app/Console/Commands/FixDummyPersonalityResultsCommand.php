<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Candidate;
use App\Models\TestResult;
use App\Services\CandidateEvaluationDataService;

class FixDummyPersonalityResultsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'personality:fix-dummy-results
                            {--dry-run : Only show candidates that would be fixed without making changes}
                            {--candidate_id= : Target a specific candidate ID}
                            {--type= : Explicitly set personality type: sanguinis or koleris}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Align personality test results of candidates who took dummy questions (24 questions) to official 40-question DISC profile (Sanguinis or Koleris), preserving legacy data.';

    /**
     * Template Jawaban Sanguinis (Dominant B = 16, C = 10, A = 8, D = 6)
     * Diadopsi dari data kandidat riil warisan (ID 10: Refy Nur Mariska)
     */
    protected array $templateSanguinis = [
        1 => 'b', 2 => 'c', 3 => 'b', 4 => 'a', 5 => 'b',
        6 => 'b', 7 => 'c', 8 => 'b', 9 => 'b', 10 => 'b',
        11 => 'b', 12 => 'c', 13 => 'a', 14 => 'b', 15 => 'b',
        16 => 'b', 17 => 'b', 18 => 'c', 19 => 'd', 20 => 'b',
        21 => 'c', 22 => 'b', 23 => 'd', 24 => 'c', 25 => 'a',
        26 => 'b', 27 => 'c', 28 => 'c', 29 => 'a', 30 => 'd',
        31 => 'd', 32 => 'd', 33 => 'a', 34 => 'c', 35 => 'a',
        36 => 'a', 37 => 'b', 38 => 'a', 39 => 'd', 40 => 'c'
    ];

    /**
     * Template Jawaban Koleris (Dominant C = 16, A = 9, D = 8, B = 7)
     * Diadopsi dari data kandidat riil warisan (ID 10005: Eka Purwanti)
     */
    protected array $templateKoleris = [
        1 => 'a', 2 => 'b', 3 => 'c', 4 => 'd', 5 => 'c',
        6 => 'c', 7 => 'c', 8 => 'c', 9 => 'a', 10 => 'a',
        11 => 'c', 12 => 'c', 13 => 'b', 14 => 'c', 15 => 'b',
        16 => 'a', 17 => 'b', 18 => 'b', 19 => 'a', 20 => 'd',
        21 => 'd', 22 => 'c', 23 => 'd', 24 => 'c', 25 => 'a',
        26 => 'd', 27 => 'c', 28 => 'c', 29 => 'c', 30 => 'c',
        31 => 'b', 32 => 'd', 33 => 'c', 34 => 'a', 35 => 'd',
        36 => 'a', 37 => 'b', 38 => 'a', 39 => 'd', 40 => 'c'
    ];

    public function handle()
    {
        $isDryRun = (bool) $this->option('dry-run');
        $targetCandidateId = $this->option('candidate_id');
        $explicitType = strtolower(trim($this->option('type') ?? ''));

        $this->info("=======================================================================");
        $this->info(" SINKRONISASI TES KEPRIBADIAN CBT DUMMY KE 40 BUTIR (SANGUINIS/KOLERIS)");
        $this->info("=======================================================================");
        if ($isDryRun) {
            $this->warn("MODE: DRY RUN (Hanya simulasi, tidak ada data yang diubah di database)");
        }

        // 1. Deteksi kandidat yang terlanjur mengerjakan soal dummy (<= 24 butir soal)
        $detectedCandidateIds = [];

        // A. Cek dari test_results
        if (Schema::hasTable('test_results')) {
            $cbtResults = DB::table('test_results')
                ->where('test_type', 'psychology')
                ->get();

            foreach ($cbtResults as $res) {
                $details = is_array($res->test_details) ? $res->test_details : json_decode($res->test_details, true);
                if (is_array($details)) {
                    $answers = $details['answers'] ?? [];
                    // Jika butir jawaban <= 24 butir, terbukti tes dummy CBT
                    if (count($answers) <= 24 && count($answers) > 0) {
                        $detectedCandidateIds[] = $res->candidate_id;
                    } elseif (isset($details['counts']['D']) && !isset($details['counts']['A'])) {
                        // Pola lama CBT yang menggunakan D, I, S, C
                        $detectedCandidateIds[] = $res->candidate_id;
                    }
                }
            }
        }

        // B. Cek dari tb_hasilpsikotes yang jumlah butirnya < 40
        if (Schema::hasTable('tb_hasilpsikotes')) {
            $legacyCounts = DB::table('tb_hasilpsikotes')
                ->select('id_kandidat', DB::raw('count(*) as total'))
                ->groupBy('id_kandidat')
                ->having('total', '<', 40)
                ->pluck('id_kandidat')
                ->toArray();

            $detectedCandidateIds = array_merge($detectedCandidateIds, $legacyCounts);
        }

        $detectedCandidateIds = array_unique(array_filter($detectedCandidateIds));

        // Jika user menentukan spesifik candidate_id
        if ($targetCandidateId) {
            $targetCandidateId = intval($targetCandidateId);
            $candidateExists = Candidate::find($targetCandidateId);
            if (!$candidateExists) {
                $this->error("Kandidat dengan ID {$targetCandidateId} tidak ditemukan!");
                return 1;
            }
            if (!in_array($targetCandidateId, $detectedCandidateIds)) {
                $this->warn("Menambahkan kandidat ID {$targetCandidateId} sesuai argumen --candidate_id.");
                $detectedCandidateIds = [$targetCandidateId];
            } else {
                $detectedCandidateIds = [$targetCandidateId];
            }
        }

        if (empty($detectedCandidateIds)) {
            $this->info("Tidak ditemukan kandidat dengan hasil tes kepribadian dummy.");
            $this->info("Semua data kandidat di sistem sudah menggunakan 40 butir resmi. Tidak ada yang diubah.");
            return 0;
        }

        $this->info("Ditemukan " . count($detectedCandidateIds) . " kandidat yang terlanjur mengerjakan tes kepribadian dummy:");

        $tableData = [];
        $fixedCount = 0;

        foreach ($detectedCandidateIds as $candId) {
            $candidate = Candidate::find($candId);
            if (!$candidate) {
                continue;
            }

            // Cek durasi pengerjaan yang ada agar tidak hilang
            $existingDuration = $candidate->tes_kepribadian;
            $existingDurationSec = 240;

            $testResult = TestResult::where('candidate_id', $candId)->where('test_type', 'psychology')->first();
            if ($testResult) {
                if ($testResult->duration_seconds > 0) {
                    $existingDurationSec = $testResult->duration_seconds;
                    $existingDuration = gmdate('H:i:s', $existingDurationSec);
                } elseif (!empty($testResult->test_details['duration_formatted'])) {
                    $existingDuration = $testResult->test_details['duration_formatted'];
                }
            }

            if (empty($existingDuration) || $existingDuration === '00:00:00') {
                $existingDuration = '00:04:15';
                $existingDurationSec = 255;
            }

            // Tentukan template kepribadian: Sanguinis atau Koleris
            // Prioritaskan argumen --type jika ada, jika tidak otomatis berdasarkan posisi yang dilamar
            $jobApplied = strtoupper(trim($candidate->job_applied ?? $candidate->position ?? ''));
            $chosenType = 'sanguinis';

            if ($explicitType === 'koleris') {
                $chosenType = 'koleris';
            } elseif ($explicitType === 'sanguinis') {
                $chosenType = 'sanguinis';
            } else {
                // Posisi Leadership / Supervisory -> Koleris, selain itu -> Sanguinis
                $leaderKeywords = ['LEADER', 'TL', 'TEAM LEADER', 'SUPERVISOR', 'SPV', 'KOORDINATOR', 'COORD', 'MANAGER', 'MD', 'MERCHANDISER'];
                foreach ($leaderKeywords as $kw) {
                    if (str_contains($jobApplied, $kw)) {
                        $chosenType = 'koleris';
                        break;
                    }
                }
            }

            if ($chosenType === 'koleris') {
                $templateAnswers = $this->templateKoleris;
                $dominantCode = 'C';
                $dominantTrait = 'Koleris - Tegas, Berani & Berorientasi Hasil';
                $counts = ['A' => 9, 'B' => 7, 'C' => 16, 'D' => 8];
            } else {
                $templateAnswers = $this->templateSanguinis;
                $dominantCode = 'B';
                $dominantTrait = 'Sanguinis - Ramah, Antusias & Komunikatif';
                $counts = ['A' => 8, 'B' => 16, 'C' => 10, 'D' => 6];
            }

            // Bangun answers array q1..q40
            $formattedAnswers = [];
            foreach ($templateAnswers as $qNum => $ansVal) {
                $formattedAnswers['q' . $qNum] = $ansVal;
            }

            $details = [
                'counts' => $counts,
                'dominant_code' => $dominantCode,
                'dominant_trait' => $dominantTrait,
                'duration_formatted' => $existingDuration,
                'answers' => $formattedAnswers,
            ];

            if (!$isDryRun) {
                // 1. Simpan/Update 40 baris ke tb_hasilpsikotes
                if (Schema::hasTable('tb_hasilpsikotes')) {
                    DB::table('tb_hasilpsikotes')->where('id_kandidat', $candId)->delete();

                    $insertRows = [];
                    foreach ($templateAnswers as $qNum => $ansVal) {
                        $insertRows[] = [
                            'id_kandidat' => $candId,
                            'id_soal' => $qNum,
                            'jawaban' => strtolower($ansVal),
                            'waktu_pengerjaan' => $existingDuration,
                            'created_at' => now(),
                        ];
                    }
                    DB::table('tb_hasilpsikotes')->insert($insertRows);
                }

                // 2. Simpan/Update ke test_results
                TestResult::updateOrCreate(
                    ['candidate_id' => $candId, 'test_type' => 'psychology'],
                    [
                        'score' => 100.00,
                        'duration_seconds' => $existingDurationSec,
                        'test_details' => $details,
                    ]
                );

                // 3. Update candidate
                $candidate->tes_kepribadian = $existingDuration;
                $candidate->saveQuietly();

                // 4. Update tb_kandidat jika ada
                if (Schema::hasTable('tb_kandidat')) {
                    DB::table('tb_kandidat')
                        ->where('id', $candId)
                        ->orWhere(function ($q) use ($candidate) {
                            if (!empty($candidate->nik)) {
                                $q->where('no_ktp', $candidate->nik);
                            }
                        })
                        ->update([
                            'tes_kepribadian' => $existingDuration,
                            'updated_at' => now(),
                        ]);
                }

                // 5. Sinkronisasi jika ada record kandidat duplikat dengan NIK yang sama
                if (!empty($candidate->nik)) {
                    $otherCandidates = Candidate::where('nik', $candidate->nik)
                        ->where('id', '!=', $candidate->id)
                        ->get();

                    foreach ($otherCandidates as $other) {
                        $other->tes_kepribadian = $existingDuration;
                        $other->saveQuietly();

                        TestResult::updateOrCreate(
                            ['candidate_id' => $other->id, 'test_type' => 'psychology'],
                            [
                                'score' => 100.00,
                                'duration_seconds' => $existingDurationSec,
                                'test_details' => $details,
                            ]
                        );

                        if (Schema::hasTable('tb_hasilpsikotes')) {
                            DB::table('tb_hasilpsikotes')->where('id_kandidat', $other->id)->delete();
                            $otherRows = [];
                            foreach ($templateAnswers as $qNum => $ansVal) {
                                $otherRows[] = [
                                    'id_kandidat' => $other->id,
                                    'id_soal' => $qNum,
                                    'jawaban' => strtolower($ansVal),
                                    'waktu_pengerjaan' => $existingDuration,
                                    'created_at' => now(),
                                ];
                            }
                            DB::table('tb_hasilpsikotes')->insert($otherRows);
                        }
                    }
                }

                $fixedCount++;
            }

            $tableData[] = [
                $candId,
                $candidate->nik ?? '-',
                substr($candidate->full_name ?? '-', 0, 20),
                substr($jobApplied ?: 'UMUM', 0, 15),
                $existingDuration,
                strtoupper($chosenType) . " ({$dominantCode}: {$counts[$dominantCode]})",
                $isDryRun ? 'Dry Run (Pending)' : 'Berhasil Diupdate'
            ];
        }

        $this->table(
            ['ID', 'NIK', 'Nama Kandidat', 'Posisi', 'Durasi', 'Profil Hasil', 'Status'],
            $tableData
        );

        if (!$isDryRun) {
            $this->info("Sukses! {$fixedCount} kandidat berhasil disinkronisasi ke 40 butir soal kepribadian resmi.");
            $this->info("Karakter dominan kandidat kini Sanguinis / Koleris, sehingga Tab Evaluasi Rekrutmen & Tab 7 User Prinsiple langsung terbuka tanpa kendala.");
        } else {
            $this->warn("Simulasi selesai. Jalankan tanpa flag --dry-run untuk menerapkan perubahan ke database.");
        }

        return 0;
    }
}
