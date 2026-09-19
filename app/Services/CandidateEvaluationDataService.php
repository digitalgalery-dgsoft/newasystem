<?php

namespace App\Services;

use App\Models\Candidate;
use Illuminate\Support\Facades\DB;

class CandidateEvaluationDataService
{
    public static function getEvaluationData(Candidate $candidate): array
    {
        // 1. Data Tes Kepribadian (DISC)
        $rawPsikotes = DB::table('tb_hasilpsikotes')
            ->where('id_kandidat', $candidate->id)
            ->orWhere('id_kandidat', (string) $candidate->id)
            ->orderBy('id_soal', 'asc')
            ->get();

        $psikotesItems = [];
        $psikotesCounts = ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0];
        $psikotesDuration = '00:04:20';
        $hasPsikotes = false;

        if ($rawPsikotes->isNotEmpty()) {
            $hasPsikotes = true;
            $questionsBank = DB::table('tb_kepribadian')->get()->keyBy('id');
            $psikotesDuration = $rawPsikotes->first()->waktu_pengerjaan ?? $candidate->tes_kepribadian ?? '00:04:20';

            foreach ($rawPsikotes as $row) {
                $ansUpper = strtoupper(trim($row->jawaban ?? ''));
                $ansLower = strtolower(trim($row->jawaban ?? ''));
                if (isset($psikotesCounts[$ansUpper])) {
                    $psikotesCounts[$ansUpper]++;
                }
                $qBank = $questionsBank->get($row->id_soal);
                $prop = 'pilihan_' . $ansLower;
                $choiceText = $qBank ? ($qBank->$prop ?? '-') : '-';

                $psikotesItems[$row->id_soal] = [
                    'ans' => $ansUpper,
                    'text' => $choiceText,
                ];
            }
        } else {
            // Cek jika ada di testResults (dari modul CBT baru)
            $cbtPsychology = $candidate->testResults->firstWhere('test_type', 'psychology');
            if ($cbtPsychology && !empty($cbtPsychology->test_details)) {
                $hasPsikotes = true;
                $details = is_array($cbtPsychology->test_details) ? $cbtPsychology->test_details : json_decode($cbtPsychology->test_details, true);
                $psikotesDuration = $details['duration_formatted'] ?? gmdate('H:i:s', $cbtPsychology->duration_seconds ?? 0);
                $cbtCounts = $details['counts'] ?? [];
                $psikotesCounts['A'] = $cbtCounts['D'] ?? $cbtCounts['A'] ?? 0;
                $psikotesCounts['B'] = $cbtCounts['I'] ?? $cbtCounts['B'] ?? 0;
                $psikotesCounts['C'] = $cbtCounts['S'] ?? $cbtCounts['C'] ?? 0;
                $psikotesCounts['D'] = $cbtCounts['C'] ?? $cbtCounts['D'] ?? 0;

                $questionsBank = DB::table('tb_kepribadian')->get()->keyBy('id');
                $cbtAnswers = $details['answers'] ?? [];
                foreach ($questionsBank as $qId => $qBank) {
                    $ansKey = 'q' . $qId;
                    $ans = strtoupper($cbtAnswers[$ansKey] ?? 'A');
                    $ansLower = strtolower($ans);
                    $prop = 'pilihan_' . $ansLower;
                    $psikotesItems[$qId] = [
                        'ans' => $ans,
                        'text' => $qBank->$prop ?? '-',
                    ];
                }
            }
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
        $dominantDisc = $discConclusions[$dominantKey] ?? $discConclusions['A'];

        // 2. Data Tes Matematika
        $mathItems = [];
        $mathDuration = '-';
        $mathTesKe = max(1, intval($candidate->tes_ke ?? 1));
        $mathCorrectCount = 0;
        $mathWrongCount = 0;
        $mathScorePercent = 0;
        $mathGrade = '-';
        $hasMath = false;

        $isMathCompleted = !empty($candidate->tes_matematika) 
            && $candidate->tes_matematika !== '00:00:00' 
            && $candidate->tes_matematika !== '-';

        if ($isMathCompleted) {
            $targetTesKe = $mathTesKe;

            // Cari di tb_hasilmath sesuai tes_ke kandidat saat ini
            $rawMath = DB::table('tb_hasilmath')
                ->join('tb_math', 'tb_math.id', '=', 'tb_hasilmath.id_soal')
                ->where('tb_hasilmath.id_kandidat', $candidate->id)
                ->where('tb_hasilmath.tes_ke', $targetTesKe)
                ->select('tb_hasilmath.*', 'tb_math.question_text', 'tb_math.correct_answer')
                ->orderBy('tb_hasilmath.id_soal', 'asc')
                ->get();

            // Fallback jika tidak ditemukan dengan tes_ke spesifik
            if ($rawMath->isEmpty()) {
                $latestTesKe = DB::table('tb_hasilmath')->where('id_kandidat', $candidate->id)->max('tes_ke');
                if ($latestTesKe) {
                    $rawMath = DB::table('tb_hasilmath')
                        ->join('tb_math', 'tb_math.id', '=', 'tb_hasilmath.id_soal')
                        ->where('tb_hasilmath.id_kandidat', $candidate->id)
                        ->where('tb_hasilmath.tes_ke', $latestTesKe)
                        ->select('tb_hasilmath.*', 'tb_math.question_text', 'tb_math.correct_answer')
                        ->orderBy('tb_hasilmath.id_soal', 'asc')
                        ->get();
                    $mathTesKe = $latestTesKe;
                }
            }

            if ($rawMath->isNotEmpty()) {
                $hasMath = true;
                $mathDuration = $rawMath->first()->waktu_pengerjaan ?? $candidate->tes_matematika ?? '00:02:00';
                $mathTesKe = $rawMath->first()->tes_ke ?? $mathTesKe;

                foreach ($rawMath as $mRow) {
                    $candAns = trim($mRow->jawaban ?? '');
                    $keyAns = trim($mRow->correct_answer ?? '');
                    $cleanCand = str_replace([' ', '.', ','], ['', '', '.'], strtolower($candAns));
                    $cleanKey = str_replace([' ', '.', ','], ['', '', '.'], strtolower($keyAns));
                    $isCorrect = ($cleanCand === $cleanKey) || (strtolower($candAns) === strtolower($keyAns));

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
            } else {
                // Cek jika ada di testResults (CBT baru)
                $cbtMath = $candidate->testResults->firstWhere('test_type', 'math');
                if ($cbtMath && !empty($cbtMath->test_details)) {
                    $hasMath = true;
                    $mDetails = is_array($cbtMath->test_details) ? $cbtMath->test_details : json_decode($cbtMath->test_details, true);
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
            }
        }

        // 3. Data Tes Komputer
        $rawKompt = DB::table('hasil_kompt')
            ->where('id_kandidat', $candidate->id)
            ->orWhere('nomor_ktp', $candidate->nik)
            ->first();

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
        $komptDuration = $candidate->tes_komputer ?? '00:03:02';

        if ($rawKompt) {
            $hasKompt = true;
            foreach ($compSkills as $k => $label) {
                $savedComp[$k] = $rawKompt->$k ?? 'Cukup';
            }
        } else {
            $cTest = $candidate->testResults->firstWhere('test_type', 'computer');
            if ($cTest && !empty($cTest->test_details)) {
                $hasKompt = true;
                $savedComp = is_array($cTest->test_details) ? $cTest->test_details : json_decode($cTest->test_details, true);
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

        $psikotestNama = $dominantDisc['type'] ?? 'Melankolis';
        $isMelankolisOrPlegmatis = in_array(strtolower($psikotestNama), ['melankolis', 'plegmatis']);

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
