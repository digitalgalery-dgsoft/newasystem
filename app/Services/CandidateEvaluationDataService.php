<?php

namespace App\Services;

use App\Models\Candidate;
use App\Models\TestResult;
use App\Services\CbtQuestionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CandidateEvaluationDataService
{
    public static function getEvaluationData(Candidate $candidate): array
    {
        // Kumpulkan semua candidate ID dengan NIK yang sama (agar sinkron antar kandidat portal & CBT)
        $siblingIds = [$candidate->id];
        if (!empty($candidate->nik)) {
            $siblingIds = Candidate::where('nik', $candidate->nik)->pluck('id')->all();
            if (empty($siblingIds)) {
                $siblingIds = [$candidate->id];
            }
        }

        // 1. Data Tes Kepribadian (DISC)
        $rawPsikotes = collect();
        if (Schema::hasTable('tb_hasilpsikotes')) {
            $rawPsikotes = DB::table('tb_hasilpsikotes')
                ->whereIn('id_kandidat', $siblingIds)
                ->orWhere(function ($q) use ($candidate) {
                    if (!empty($candidate->nik)) {
                        $q->where('id_kandidat', (string) $candidate->nik);
                    }
                })
                ->orderBy('id_soal', 'asc')
                ->get();
        }

        $psikotesItems = [];
        $psikotesCounts = ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0];
        $psikotesDuration = (!empty($candidate->tes_kepribadian) && $candidate->tes_kepribadian !== '00:00:00' && $candidate->tes_kepribadian !== '-') 
            ? $candidate->tes_kepribadian 
            : null;
        $hasPsikotes = false;

        if ($rawPsikotes->isNotEmpty()) {
            $hasPsikotes = true;
            $questionsBank = Schema::hasTable('tb_kepribadian') ? DB::table('tb_kepribadian')->get()->keyBy('id') : collect();
            $defaultQuestions = collect(CbtQuestionService::getDefaultLegacyPersonalityQuestions())->keyBy('id');
            $psikotesDuration = $rawPsikotes->first()->waktu_pengerjaan ?? $psikotesDuration ?? '00:04:20';

            foreach ($rawPsikotes as $row) {
                $ansUpper = strtoupper(trim($row->jawaban ?? ''));
                $ansLower = strtolower(trim($row->jawaban ?? ''));
                if (isset($psikotesCounts[$ansUpper])) {
                    $psikotesCounts[$ansUpper]++;
                }
                $qBank = $questionsBank->get($row->id_soal);
                $prop = 'pilihan_' . $ansLower;
                $choiceText = $qBank ? ($qBank->$prop ?? '-') : '-';

                if ($choiceText === '-' || empty($choiceText)) {
                    $dq = $defaultQuestions->get($row->id_soal);
                    if ($dq && isset($dq[$ansLower])) {
                        $choiceText = $dq[$ansLower];
                    }
                }

                $psikotesItems[$row->id_soal] = [
                    'ans' => $ansUpper,
                    'text' => $choiceText,
                ];
            }
        }

        // Cek jika ada di testResults (dari modul CBT baru)
        $cbtPsychology = $candidate->testResults ? $candidate->testResults->firstWhere('test_type', 'psychology') : null;
        if (!$cbtPsychology) {
            $cbtPsychology = TestResult::whereIn('candidate_id', $siblingIds)->where('test_type', 'psychology')->latest()->first();
        }

        if ($cbtPsychology && !empty($cbtPsychology->test_details)) {
            $details = is_array($cbtPsychology->test_details) ? $cbtPsychology->test_details : json_decode($cbtPsychology->test_details, true);
            if (!$hasPsikotes || empty($psikotesItems)) {
                $hasPsikotes = true;
                $psikotesDuration = $details['duration_formatted'] ?? gmdate('H:i:s', $cbtPsychology->duration_seconds ?? 0);
                $cbtCounts = $details['counts'] ?? [];
                // Direct A, B, C, D mapping (A: Melankolis, B: Sanguinis, C: Koleris, D: Plegmatis)
                $psikotesCounts['A'] = $cbtCounts['A'] ?? $cbtCounts['C_disc'] ?? 0;
                $psikotesCounts['B'] = $cbtCounts['B'] ?? $cbtCounts['I_disc'] ?? 0;
                $psikotesCounts['C'] = $cbtCounts['C'] ?? $cbtCounts['D_disc'] ?? 0;
                $psikotesCounts['D'] = $cbtCounts['D'] ?? $cbtCounts['S_disc'] ?? 0;

                $questionsBank = Schema::hasTable('tb_kepribadian') ? DB::table('tb_kepribadian')->get()->keyBy('id') : collect();
                $defaultQuestions = collect(CbtQuestionService::getDefaultLegacyPersonalityQuestions())->keyBy('id');
                $cbtAnswers = $details['answers'] ?? [];
                for ($qId = 1; $qId <= 40; $qId++) {
                    $ansKey = 'q' . $qId;
                    $ans = strtoupper($cbtAnswers[$ansKey] ?? 'A');
                    $ansLower = strtolower($ans);
                    $prop = 'pilihan_' . $ansLower;
                    $qBank = $questionsBank->get($qId);
                    $choiceText = $qBank ? ($qBank->$prop ?? '-') : '-';
                    if ($choiceText === '-' || empty($choiceText)) {
                        $dq = $defaultQuestions->get($qId);
                        if ($dq && isset($dq[$ansLower])) {
                            $choiceText = $dq[$ansLower];
                        }
                    }
                    $psikotesItems[$qId] = [
                        'ans' => $ans,
                        'text' => $choiceText,
                    ];
                }
            }
        }

        // Pastikan kolom tes_kepribadian tersinkronisasi jika tes sudah selesai
        if ($hasPsikotes && (empty($candidate->tes_kepribadian) || $candidate->tes_kepribadian === '00:00:00' || $candidate->tes_kepribadian === '-')) {
            $candidate->tes_kepribadian = $psikotesDuration ?: '00:04:20';
            try {
                $candidate->saveQuietly();
                if (!empty($candidate->nik)) {
                    Candidate::where('nik', $candidate->nik)->where(function ($q) {
                        $q->whereNull('tes_kepribadian')->orWhere('tes_kepribadian', '')->orWhere('tes_kepribadian', '00:00:00');
                    })->update(['tes_kepribadian' => $candidate->tes_kepribadian]);

                    if (Schema::hasTable('tb_kandidat')) {
                        DB::table('tb_kandidat')->where('no_ktp', $candidate->nik)->update(['tes_kepribadian' => $candidate->tes_kepribadian]);
                    }
                }
            } catch (\Throwable $e) {}
        }

        // Hitung watak dominan & kesimpulan DISC
        $dominantKey = 'A';
        $highestCount = -1;
        foreach ($psikotesCounts as $k => $cnt) {
            if ($cnt > $highestCount) {
                $highestCount = $cnt;
                $dominantKey = $k;
            }
        }

        $discConclusions = [
            'A' => [
                'type' => 'Melankolis',
                'summary' => 'Memiliki kepribadian Melankolis. Tipe ini paling baik dalam hal pekerjaan yang memerlukan keputusan cepat, ketelitian tinggi, pemikiran analitis mendalam, kepatuhan terhadap data dan fakta. Kelemahan tipe ini adalah tidak tahu bagaimana cara menangani orang lain; sulit mengakui kesalahan; sulit bersikap sabar; terlalu pekerja keras.'
            ],
            'B' => [
                'type' => 'Sanguinis',
                'summary' => 'Memiliki kepribadian Sanguinis. Tipe ini paling baik dalam hal pekerjaan yang berhubungan dengan banyak orang, antusiasme tinggi, kemampuan komunikasi yang persuasif, adaptif, serta membawa energi positif dan ceria. Kelemahan tipe ini adalah kurang disiplin waktu, mudah terdistraksi, dan cenderung emosional.'
            ],
            'C' => [
                'type' => 'Koleris',
                'summary' => 'Memiliki kepribadian Koleris. Tipe ini paling baik dalam hal kepemimpinan, berorientasi kuat pada target dan hasil kerja nyata, tegas, independen, serta berani mengambil keputusan strategis di bawah tekanan. Kelemahan tipe ini adalah cenderung dominan, tidak sabaran terhadap detail kecil, dan terkadang kurang peka.'
            ],
            'D' => [
                'type' => 'Plegmatis',
                'summary' => 'Memiliki kepribadian Plegmatis. Tipe ini paling baik dalam hal pekerjaan yang menuntut ketenangan, kesabaran, konsistensi prosedur, diplomasi, serta membangun keharmonisan dan kerjasama tim yang solid. Kelemahan tipe ini adalah lambat dalam mengambil inisiatif mandiri, cenderung menghindari konflik, dan kurang menyukai perubahan mendadak.'
            ],
        ];
        $dominantDisc = $hasPsikotes 
            ? ($discConclusions[$dominantKey] ?? $discConclusions['A']) 
            : ['type' => 'Belum Tes', 'summary' => 'Kandidat belum mengikuti Tes Kepribadian (DISC).'];

        // 2. Data Tes Matematika
        $mathItems = [];
        $mathDuration = (!empty($candidate->tes_matematika) && $candidate->tes_matematika !== '00:00:00' && $candidate->tes_matematika !== '-')
            ? $candidate->tes_matematika
            : '-';
        $mathTesKe = max(1, intval($candidate->tes_ke ?? 1));
        $mathCorrectCount = 0;
        $mathWrongCount = 0;
        $mathScorePercent = 0;
        $mathGrade = '-';
        $hasMath = false;

        $targetTesKe = $mathTesKe;
        $rawMath = collect();
        if (Schema::hasTable('tb_hasilmath')) {
            $rawMath = DB::table('tb_hasilmath')
                ->join('tb_math', 'tb_math.id', '=', 'tb_hasilmath.id_soal')
                ->whereIn('tb_hasilmath.id_kandidat', $siblingIds)
                ->where('tb_hasilmath.tes_ke', $targetTesKe)
                ->select('tb_hasilmath.*', 'tb_math.question_text', 'tb_math.correct_answer')
                ->orderBy('tb_hasilmath.id_soal', 'asc')
                ->get();

            // Fallback jika tidak ditemukan dengan tes_ke spesifik
            if ($rawMath->isEmpty()) {
                $latestTesKe = DB::table('tb_hasilmath')->whereIn('id_kandidat', $siblingIds)->max('tes_ke');
                if ($latestTesKe) {
                    $rawMath = DB::table('tb_hasilmath')
                        ->join('tb_math', 'tb_math.id', '=', 'tb_hasilmath.id_soal')
                        ->whereIn('tb_hasilmath.id_kandidat', $siblingIds)
                        ->where('tb_hasilmath.tes_ke', $latestTesKe)
                        ->select('tb_hasilmath.*', 'tb_math.question_text', 'tb_math.correct_answer')
                        ->orderBy('tb_hasilmath.id_soal', 'asc')
                        ->get();
                    $mathTesKe = $latestTesKe;
                }
            }
        }

        if ($rawMath->isNotEmpty()) {
            $hasMath = true;
            $mathDuration = $rawMath->first()->waktu_pengerjaan ?? ($candidate->tes_matematika ?: '00:02:00');
            $mathTesKe = $rawMath->first()->tes_ke ?? $mathTesKe;

            foreach ($rawMath as $mRow) {
                $candAns = trim($mRow->jawaban ?? '');
                $keyAns = trim($mRow->correct_answer ?? '');
                $isCorrect = false;

                if (strtolower($candAns) === strtolower($keyAns)) {
                    $isCorrect = true;
                }
                if (!$isCorrect) {
                    $normCand = preg_replace('/[^0-9a-zA-Z]/', '', strtolower($candAns));
                    $normKey = preg_replace('/[^0-9a-zA-Z]/', '', strtolower($keyAns));
                    if ($normCand !== '' && $normCand === $normKey) {
                        $isCorrect = true;
                    }
                }
                if (!$isCorrect) {
                    $cleanCand = trim(str_replace([' ', '%', '.'], ['', '', ','], strtolower($candAns)));
                    $cleanKey = trim(str_replace([' ', '%', '.'], ['', '', ','], strtolower($keyAns)));
                    if ($cleanCand !== '' && $cleanKey !== '' && $cleanCand === $cleanKey) {
                        $isCorrect = true;
                    }
                }

                if ($isCorrect) {
                    $mathCorrectCount++;
                } else {
                    $mathWrongCount++;
                }

                $mathItems[$mRow->id_soal] = [
                    'q' => $mRow->question_text,
                    'cand' => $candAns,
                    'key' => $keyAns,
                    'correct' => $isCorrect,
                ];
            }
        }

        // Cek jika ada di testResults (CBT baru)
        $cbtMath = $candidate->testResults ? $candidate->testResults->firstWhere('test_type', 'math') : null;
        if (!$cbtMath) {
            $cbtMath = TestResult::whereIn('candidate_id', $siblingIds)->where('test_type', 'math')->latest()->first();
        }

        if ($cbtMath && !empty($cbtMath->test_details)) {
            $mDetails = is_array($cbtMath->test_details) ? $cbtMath->test_details : json_decode($cbtMath->test_details, true);
            if (!$hasMath || empty($mathItems)) {
                $hasMath = true;
                $mathDuration = $mDetails['duration_formatted'] ?? gmdate('H:i:s', $cbtMath->duration_seconds ?? 0);
                $mathCorrectCount = $mDetails['correct_answers'] ?? $mDetails['correct_count'] ?? round(($cbtMath->score / 100) * 10);
                $mathWrongCount = 10 - $mathCorrectCount;
                $mathTesKe = $mDetails['tes_ke'] ?? $candidate->tes_ke ?? 1;
                $mBreakdown = $mDetails['breakdown'] ?? [];
                foreach ($mBreakdown as $idx => $b) {
                    $mathItems[$idx] = [
                        'q' => $b['question'] ?? $b['question_text'] ?? ('Pertanyaan Soal #' . $idx),
                        'cand' => $b['user_answer'] ?? '-',
                        'key' => $b['correct_answer'] ?? '-',
                        'correct' => (bool) ($b['is_correct'] ?? false),
                    ];
                }
            }
        }

        if ($hasMath) {
            $mathTotalQuestions = count($mathItems) > 0 ? count($mathItems) : 10;
            $mathScorePercent = $mathTotalQuestions > 0 ? round(($mathCorrectCount / $mathTotalQuestions) * 100) : 0;
            $mathGrade = ($mathScorePercent >= 85) ? 'A' : (($mathScorePercent >= 70) ? 'B' : (($mathScorePercent >= 55) ? 'C' : 'D'));

            // Auto-sync jika kolom kandidat belum terisi
            if (empty($candidate->tes_matematika) || $candidate->tes_matematika === '00:00:00' || $candidate->tes_matematika === '-') {
                $candidate->tes_matematika = $mathDuration;
                $candidate->tes_ke = $mathTesKe;
                try {
                    $candidate->saveQuietly();
                    if (!empty($candidate->nik)) {
                        Candidate::where('nik', $candidate->nik)->where(function ($q) {
                            $q->whereNull('tes_matematika')->orWhere('tes_matematika', '')->orWhere('tes_matematika', '00:00:00');
                        })->update(['tes_matematika' => $mathDuration, 'tes_ke' => $mathTesKe]);
                    }
                } catch (\Throwable $e) {}
            }
        }

        // 3. Data Tes Komputer
        $rawKompt = null;
        if (Schema::hasTable('hasil_kompt')) {
            $rawKompt = DB::table('hasil_kompt')
                ->whereIn('id_kandidat', $siblingIds)
                ->first();
        }

        $compSkills = [
            'vlookup' => 'VLOOKUP',
            'hlookup' => 'HLOOKUP',
            'pivot' => 'PIVOT TABLE',
            'fungsiif' => 'FUNGSI IF',
            'average' => 'AVERAGE',
            'hitung' => 'PERKALIAN & PEMBAGIAN',
            'teliti' => 'KETELITIAN',
            'cepat' => 'KECEPATAN',
            'hasilkerja' => 'HASIL KERJA',
        ];

        $savedComp = [];
        $hasKompt = false;
        $komptDuration = (!empty($candidate->tes_komputer) && $candidate->tes_komputer !== '00:00:00' && $candidate->tes_komputer !== '-') 
            ? $candidate->tes_komputer 
            : '-';

        if ($rawKompt) {
            $hasKompt = true;
            foreach ($compSkills as $k => $label) {
                $savedComp[$k] = $rawKompt->$k ?? 'Cukup';
            }
        }

        $cTest = $candidate->testResults ? $candidate->testResults->firstWhere('test_type', 'computer') : null;
        if (!$cTest) {
            $cTest = TestResult::whereIn('candidate_id', $siblingIds)->where('test_type', 'computer')->latest()->first();
        }
        if ($cTest && !empty($cTest->test_details)) {
            $hasKompt = true;
            $savedComp = is_array($cTest->test_details) ? $cTest->test_details : json_decode($cTest->test_details, true);
            if ($komptDuration === '-') {
                $komptDuration = gmdate('H:i:s', $cTest->duration_seconds ?? 0);
            }
        }

        $komptSummaryLabel = 'Cukup (75%)';
        if ($hasKompt && !empty($savedComp)) {
            $totalPoints = 0;
            $pointMap = ['Sangat Baik' => 100, 'Baik' => 85, 'Cukup' => 70, 'Kurang' => 50];
            foreach ($savedComp as $val) {
                $totalPoints += $pointMap[$val] ?? 70;
            }
            $avgPoints = round($totalPoints / max(1, count($savedComp)));
            $label = $avgPoints >= 85 ? 'Baik' : ($avgPoints >= 70 ? 'Cukup' : 'Kurang');
            $komptSummaryLabel = "{$label} ({$avgPoints}%)";
        }

        // Validasi Posisi Sales & Hasil Psikotest DISC
        $job = strtolower(trim($candidate->applied_job ?? ''));
        $salesKeywords = [
            'spg', 'spb', 'ba', 'bc', 'beauty advisor', 'brand ambassador',
            'direct consultant', 'sales', 'merchandiser', 'md', 'smd',
            'salesman', 'canvasser', 'motoris', 'promotor', 'frontliner'
        ];

        $isSalesRelated = false;
        foreach ($salesKeywords as $kw) {
            if (str_contains($job, $kw)) {
                $isSalesRelated = true;
                break;
            }
        }

        $psikotestNama = $hasPsikotes ? ($dominantDisc['type'] ?? 'Belum Tes') : 'Belum Tes';
        $isMelankolisOrPlegmatis = $hasPsikotes && in_array(strtolower($psikotestNama), ['melankolis', 'plegmatis']);

        // Syarat 1: Nilai Matematika tidak boleh C / D (minimal B untuk lolos)
        $isMathFailed = $hasMath && !in_array(strtoupper($mathGrade ?? ''), ['A', 'B']);

        // Syarat 2: Psikotes tidak boleh Melankolis / Plegmatis untuk posisi penjualan/sales
        $isPsikotestFailed = $hasPsikotes && $isSalesRelated && $isMelankolisOrPlegmatis;

        $isUserPrinsipleDisabled = $isMathFailed || $isPsikotestFailed || !$hasMath;

        $userPrinsipleDisableReasons = [];
        if ($isPsikotestFailed) {
            $userPrinsipleDisableReasons[] = "Hasil Psikotest {$psikotestNama} Tidak Disarankan Untuk Jabatan " . ($job ?: 'Sales') . ". Harap Cari Kandidat Lain (Disarankan Mencari Kandidat Baru / Yang Lain).";
        }
        if ($isMathFailed) {
            $userPrinsipleDisableReasons[] = "Nilai Matematika kandidat adalah {$mathGrade}. Untuk lolos, minimal nilai Matematika adalah B.";
        } elseif (!$hasMath) {
            $userPrinsipleDisableReasons[] = "Kandidat belum menyelesaikan Tes Matematika (atau sedang dalam status Remidi Tes Ke - {$mathTesKe}).";
        }

        $catatanRekomendasi = !empty($userPrinsipleDisableReasons) ? implode(' | ', $userPrinsipleDisableReasons) : null;

        return [
            'hasPsikotes' => $hasPsikotes,
            'psikotesItems' => $psikotesItems,
            'psikotesCounts' => $psikotesCounts,
            'psikotesDuration' => $psikotesDuration,
            'dominantDisc' => $dominantDisc,
            'hasMath' => $hasMath,
            'mathItems' => $mathItems,
            'mathDuration' => $mathDuration,
            'mathTesKe' => $mathTesKe,
            'mathCorrectCount' => $mathCorrectCount,
            'mathWrongCount' => $mathWrongCount,
            'mathScorePercent' => $mathScorePercent,
            'mathGrade' => $mathGrade,
            'hasKompt' => $hasKompt,
            'savedComp' => $savedComp,
            'compSkills' => $compSkills,
            'komptDuration' => $komptDuration,
            'komptSummaryLabel' => $komptSummaryLabel,
            'isSalesRelated' => $isSalesRelated,
            'psikotestNama' => $psikotestNama,
            'isPsikotestFailed' => $isPsikotestFailed,
            'isMathFailed' => $isMathFailed,
            'isUserPrinsipleDisabled' => $isUserPrinsipleDisabled,
            'userPrinsipleDisableReasons' => $userPrinsipleDisableReasons,
            'catatanRekomendasi' => $catatanRekomendasi,
        ];
    }
}
