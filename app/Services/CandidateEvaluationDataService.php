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
        $rawMath = DB::table('tb_hasilmath')
            ->join('tb_math', 'tb_math.id', '=', 'tb_hasilmath.id_soal')
            ->where('tb_hasilmath.id_kandidat', $candidate->id)
            ->select('tb_hasilmath.*', 'tb_math.question_text', 'tb_math.correct_answer')
            ->orderBy('tb_hasilmath.id_soal', 'asc')
            ->get();

        $mathItems = [];
        $mathDuration = '00:02:00';
        $mathTesKe = 1;
        $mathCorrectCount = 0;
        $mathWrongCount = 0;
        $hasMath = false;

        if ($rawMath->isNotEmpty()) {
            $hasMath = true;
            $mathDuration = $rawMath->first()->waktu_pengerjaan ?? $candidate->tes_matematika ?? '00:02:00';
            $mathTesKe = $rawMath->first()->tes_ke ?? $candidate->tes_ke ?? 1;

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
                $mathCorrectCount = $mDetails['correct_answers'] ?? round(($cbtMath->score / 100) * 10);
                $mathWrongCount = 10 - $mathCorrectCount;
                $mathTesKe = 1;
                $mBreakdown = $mDetails['breakdown'] ?? [];
                foreach ($mBreakdown as $idx => $b) {
                    $mathItems[$idx] = [
                        'q' => $b['question'] ?? ('Pertanyaan Soal #' . $idx),
                        'cand' => $b['user_answer'] ?? '-',
                        'key' => $b['correct_answer'] ?? '-',
                        'correct' => (bool) ($b['is_correct'] ?? false),
                    ];
                }
            }
        }

        $mathTotalQuestions = count($mathItems) > 0 ? count($mathItems) : 10;
        $mathScorePercent = $mathTotalQuestions > 0 ? round(($mathCorrectCount / $mathTotalQuestions) * 100) : 0;
        $mathGrade = ($mathScorePercent >= 85) ? 'A' : (($mathScorePercent >= 70) ? 'B' : (($mathScorePercent >= 55) ? 'C' : 'D'));

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

        $baikCount = count(array_filter($savedComp, fn($v) => strtolower($v ?? '') === 'baik'));
        $cukupCount = count(array_filter($savedComp, fn($v) => strtolower($v ?? '') === 'cukup'));
        $komptPercentage = count($compSkills) > 0 ? round((($baikCount + ($cukupCount * 0.5)) / count($compSkills)) * 100) : 75;
        $komptSummaryLabel = ($komptPercentage >= 80) ? 'Baik (' . $komptPercentage . '%)' : (($komptPercentage >= 60) ? 'Cukup (' . $komptPercentage . '%)' : 'Kurang (' . $komptPercentage . '%)');

        // 4. Validasi Kelayakan Tab User Prinsiple
        $job = trim($candidate->applied_job ?? $candidate->position ?? '');
        $isSalesRelated = (bool) preg_match('/\b(spg|spb|dc|sales|salest promotion girl|sales promotion girl|sales promotion boy|ba|beauty advisor|bc|beauty consultant|dulux consultant|promotor|promoter|canvasser|md|smd|merchandiser)\b/i', $job);

        $dominantType = strtolower($dominantDisc['type'] ?? '');
        $isMelankolisOrPlegmatis = in_array($dominantType, ['melankolis', 'plegmatis', 'pragmatis']);
        $psikotestNama = ($dominantType === 'melankolis') ? 'Melankolis' : (($dominantType === 'plegmatis' || $dominantType === 'pragmatis') ? 'Plegmatis' : ucfirst($dominantType));

        // Syarat 1: Nilai Matematika tidak boleh C / D (minimal B untuk lolos)
        $isMathFailed = $hasMath && !in_array(strtoupper($mathGrade ?? ''), ['A', 'B']);

        // Syarat 2: Psikotes tidak boleh Melankolis / Plegmatis untuk posisi penjualan/sales
        $isPsikotestFailed = $hasPsikotes && $isSalesRelated && $isMelankolisOrPlegmatis;

        $isUserPrinsipleDisabled = $isMathFailed || $isPsikotestFailed;

        $userPrinsipleDisableReasons = [];
        if ($isPsikotestFailed) {
            $userPrinsipleDisableReasons[] = "Hasil Psikotest {$psikotestNama} Tidak Disarankan Untuk Jabatan " . ($job ?: 'Sales') . ". Harap Cari Kandidat Lain (Disarankan Mencari Kandidat Baru / Yang Lain).";
        }
        if ($isMathFailed) {
            $userPrinsipleDisableReasons[] = "Nilai Matematika kandidat adalah {$mathGrade}. Untuk lolos, minimal nilai Matematika adalah B.";
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
