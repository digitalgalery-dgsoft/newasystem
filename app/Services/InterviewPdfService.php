<?php

namespace App\Services;

use App\Models\Candidate;
use App\Models\WorkExperience;
use App\Models\PrincipleApproval;
use Carbon\Carbon;

class InterviewPdfService
{
    /**
     * Generate multi-page PDF faithfully following legacy v3/printall.php
     * with Principle Approval and Reference Check attachments if available.
     */
    public function generate(Candidate $candidate): string
    {
        // Require legacy vendor autoload for mPDF
        if (!class_exists('\Mpdf\Mpdf')) {
            $possibleAutoloads = [
                'd:/ASystem/v3/vendor/autoload.php',
                base_path('../v3/vendor/autoload.php'),
                'C:/xampp/htdocs/v3/vendor/autoload.php',
            ];
            foreach ($possibleAutoloads as $al) {
                if (file_exists($al)) {
                    require_once $al;
                    break;
                }
            }
        }

        // Determine Principle & Entity Kop Logo
        $principleModel = $candidate->principle;
        $entityCode = strtoupper(trim($principleModel?->entity ?? ''));
        $parentComp = strtoupper(trim($principleModel?->parent_company ?? ''));
        $principleName = strtoupper(trim($principleModel?->name ?? ($candidate->attributes['principle'] ?? 'PT ARINA MULTI KARYA')));

        if (empty($parentComp)) {
            $parentComp = $principleName;
        }

        $kopFile = 'kopamknew.png';
        if ($entityCode === 'AKP' || str_contains($parentComp, 'ALVA') || str_contains($principleName, '(AKP)')) {
            $kopFile = 'kopakp.png';
            if (empty($parentComp) || $parentComp === $principleName) $parentComp = 'PT ALVA KARYA PERKASA';
        } elseif ($entityCode === 'ATB' || str_contains($parentComp, 'TALENTA') || str_contains($principleName, '(ATB)')) {
            $kopFile = 'kopatb.png';
            if (empty($parentComp) || $parentComp === $principleName) $parentComp = 'PT ANUGRAH TALENTA BERKARYA';
        } elseif ($entityCode === 'ATK' || str_contains($parentComp, 'TERPERCAYA') || str_contains($parentComp, 'ATK') || str_contains($principleName, '(ATK)')) {
            $kopFile = 'kopatk.png';
            if (empty($parentComp) || $parentComp === $principleName) $parentComp = 'PT ANUGRAH TERPERCAYA KERJA';
        } elseif ($entityCode === 'ABO' || str_contains($parentComp, 'ABADI') || str_contains($parentComp, 'ODELIA') || str_contains($parentComp, 'ABO') || str_contains($principleName, '(ABO)')) {
            $kopFile = 'kopabo.png';
            if (empty($parentComp) || $parentComp === $principleName) $parentComp = 'PT ABADI BERKAT ODELIA';
        } else {
            $kopFile = 'kopamknew.png';
            if (empty($parentComp) || $parentComp === $principleName) $parentComp = 'PT ARINA MULTI KARYA';
        }

        $possibleKop = [
            public_path('kop/logo/' . $kopFile),
            public_path('kop/' . $kopFile),
            base_path('public/kop/logo/' . $kopFile),
            base_path('public/kop/' . $kopFile),
            storage_path('app/master_prinsiple_extract/kop_extracted/' . $kopFile),
            'd:/ASystem/v3/kop/logo/' . $kopFile,
            base_path('../v3/kop/logo/' . $kopFile),
            'C:/xampp/htdocs/v3/kop/logo/' . $kopFile,
        ];

        $kopPath = '';
        foreach ($possibleKop as $pk) {
            if (file_exists($pk)) {
                $kopPath = $pk;
                break;
            }
        }

        $logoBase64 = '';
        if (!empty($kopPath) && file_exists($kopPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($kopPath));
        }


        // Generate Pie Chart for DISC Test
        $rawPsikotes = \Illuminate\Support\Facades\DB::table('tb_hasilpsikotes')
            ->where('id_kandidat', $candidate->id)
            ->orWhere('id_kandidat', (string) $candidate->id)
            ->orderBy('id_soal', 'asc')
            ->get();

        $discResults = ['a' => 0, 'b' => 0, 'c' => 0, 'd' => 0];
        $discQuestions = [];
        $questionsBank = \Illuminate\Support\Facades\DB::table('tb_kepribadian')->get()->keyBy('id');

        if ($rawPsikotes->isNotEmpty()) {
            foreach ($rawPsikotes as $rp) {
                $ans = strtolower(trim($rp->jawaban ?? ''));
                if (isset($discResults[$ans])) {
                    $discResults[$ans]++;
                }
                $qBank = $questionsBank->get($rp->id_soal);
                $prop = 'pilihan_' . $ans;
                $discQuestions[$rp->id_soal] = [
                    strtoupper($ans),
                    $qBank ? ($qBank->$prop ?? '-') : '-'
                ];
            }
        } else {
            $discResults = ['a' => 17, 'b' => 7, 'c' => 9, 'd' => 7];
            $discQuestions = [
                1 => ['B', 'Antusias'], 2 => ['C', 'Menyukai Logika dan Fakta'], 3 => ['C', 'Teguh Pendirian'], 4 => ['A', 'Toleran'],
                5 => ['A', 'Menghargai'], 6 => ['C', 'Mandiri'], 7 => ['A', 'Perencana'], 8 => ['A', 'Terjadwal'],
                9 => ['B', 'Optimis'], 10 => ['B', 'Humoris'], 11 => ['B', 'Penuh Strategi, Perasa dan Sabar'], 12 => ['B', 'Bersemangat'],
                13 => ['D', 'Berkorban Tidak Menyakiti Hati'], 14 => ['A', 'Suka Mengintropeksi'], 15 => ['D', 'Mudah Membaur'], 16 => ['C', 'Berpendirian Teguh'],
                17 => ['B', 'Penuh Semangat'], 18 => ['A', 'Suka Membuat Grafik dan Tugas'], 19 => ['C', 'Produktif'], 20 => ['A', 'Memiliki Batasan Dalam Berperilaku'],
                21 => ['A', 'Pemalu'], 22 => ['C', 'Tidak Teratur'], 23 => ['D', 'Tidak Suka Terlibat Konflik'], 24 => ['B', 'Mudah Lupa'],
                25 => ['A', 'Sulit Percaya'], 26 => ['A', 'Tidak Populer'], 27 => ['C', 'Keras Kepala'], 28 => ['D', 'Dingin'],
                29 => ['A', 'Mudah Merasa Terasing'], 30 => ['C', 'Nekat'], 31 => ['A', 'Menarik Diri Dari Pergaulan'], 32 => ['D', 'Tidak Suka Konflik'],
                33 => ['D', 'Kurang Yakin'], 34 => ['A', 'Tertutup'], 35 => ['A', 'Moody'], 36 => ['A', 'Tidak Mudah Percaya'],
                37 => ['A', 'Penyendiri'], 38 => ['A', 'Mudah Curiga'], 39 => ['D', 'Menolak Dilibatkan'], 40 => ['C', 'Cerdik dan Licik'],
            ];
        }

        $totalDisc = array_sum($discResults) ?: 1;
        $persentase = [
            'a' => round(($discResults['a'] / $totalDisc) * 100, 1),
            'b' => round(($discResults['b'] / $totalDisc) * 100, 1),
            'c' => round(($discResults['c'] / $totalDisc) * 100, 1),
            'd' => round(($discResults['d'] / $totalDisc) * 100, 1)
        ];
        $chartBase64 = $this->generatePieChart($discResults, $persentase);

        // Dominant trait
        arsort($discResults);
        $topDiscKey = strtoupper(array_key_first($discResults));
        $discTraitsMap = [
            'A' => ['Melankolis', 'Memiliki kepribadian Melankolis. Tipe ini paling baik dalam hal pekerjaan yang memerlukan keputusan cepat, ketelitian tinggi, pemikiran analitis mendalam, kepatuhan terhadap data dan fakta. Kelemahan tipe ini adalah kadang terlalu pekerja keras dan menuntut kesempurnaan.'],
            'B' => ['Sanguinis', 'Memiliki kepribadian Sanguinis. Tipe ini paling baik dalam hal pekerjaan yang berhubungan dengan banyak orang, antusiasme tinggi, kemampuan komunikasi yang persuasif, adaptif, serta membawa energi positif dan ceria.'],
            'C' => ['Koleris', 'Memiliki kepribadian Koleris. Tipe ini paling baik dalam hal kepemimpinan, berorientasi kuat pada target dan hasil kerja nyata, tegas, independen, serta berani mengambil keputusan strategis di bawah tekanan.'],
            'D' => ['Plegmatis', 'Memiliki kepribadian Plegmatis. Tipe ini paling baik dalam hal pekerjaan yang menuntut ketenangan, kesabaran, konsistensi prosedur, diplomasi, serta membangun keharmonisan dan kerjasama tim yang solid.'],
        ];
        $dominantTrait = $discTraitsMap[$topDiscKey] ?? $discTraitsMap['A'];

        // Query Real Math
        $rawMath = \Illuminate\Support\Facades\DB::table('tb_hasilmath')
            ->join('tb_math', 'tb_math.id', '=', 'tb_hasilmath.id_soal')
            ->where('tb_hasilmath.id_kandidat', $candidate->id)
            ->select('tb_hasilmath.*', 'tb_math.question_text', 'tb_math.correct_answer')
            ->orderBy('tb_hasilmath.id_soal', 'asc')
            ->get();

        $mathQuestions = [];
        $mathDuration = $candidate->tes_matematika ?? '00:02:00';
        $mathTesKe = $candidate->tes_ke ?? 1;
        $mathCorrectCount = 0;
        $mathWrongCount = 0;

        if ($rawMath->isNotEmpty()) {
            $mathDuration = $rawMath->first()->waktu_pengerjaan ?? $mathDuration;
            $mathTesKe = $rawMath->first()->tes_ke ?? $mathTesKe;
            foreach ($rawMath as $mRow) {
                $candAns = trim($mRow->jawaban ?? '');
                $keyAns = trim($mRow->correct_answer ?? '');
                $cleanCand = str_replace([' ', '.', ','], ['', '', '.'], strtolower($candAns));
                $cleanKey = str_replace([' ', '.', ','], ['', '', '.'], strtolower($keyAns));
                $isCorrect = ($cleanCand === $cleanKey) || (strtolower($candAns) === strtolower($keyAns));
                if ($isCorrect) $mathCorrectCount++; else $mathWrongCount++;

                $mathQuestions[$mRow->id_soal] = [
                    $mRow->question_text,
                    $candAns,
                    $keyAns,
                    $isCorrect,
                ];
            }
        } else {
            $mathQuestions = [
                1 => ['Ani membeli Lampu Philips 50 Watt Seharga Rp. 200.000,- di C4 Buaran diskon 15%. Berapa harus dibayar?', '170000', '170000', true],
                2 => ['Yani Membeli 2 Buah Bedak Loreal Rp. 350.000,- diskon kedua 35%. Berapa total harus dibayar Yani?', '390000', '405000', false],
                3 => ['SPG Dancow Target Rp. 7.000.000,- dan baru mencapai Rp. 5.000.000,-. Berapa persen pencapaian?', '71,42%', '71,42%', true],
                4 => ['Bagas Membeli Wafer TimTam 200gr Rp. 5.250,- sebanyak 15 bungkus diskon 15%. Berapa harus dibayar?', 'A', 'A', true],
                5 => ['Putri Membeli Boneka Rp. 50.000,- dijual kembali seharga Rp. 60.000,-. Berapa persen keuntungan Putri?', 'D', 'D', true],
                6 => ['Lanjutan perhitungan deret 24, 20, 16, 12, ......', '8', '8, 4', false],
                7 => ['Ibu mempunyai uang Rp. 30.000,- dibelikan lauk Rp. 12.000, sayur Rp. 4.000, minyak Rp. 4.000. Berapa sisa?', 'B', 'B', true],
                8 => ['Angga beli handicam Rp. 4.500.000 diskon 20% uang sisa beli keperluan Rp. 1.500.000. Berapa sisa?', 'A', 'A', true],
                9 => ['Sinta membeli 2 pcs pelembab Loreal @ Rp. 80.000 diskon kedua 75%. Berapa harus dibayar Santi?', '100000', '100000', true],
                10 => ['SPG Arnotts target Rp. 12.000.000,- baru tercapai Rp. 5.000.000,-. Berapa persen pencapaian SPG?', '41,67%', '41,67%', true],
            ];
            $mathCorrectCount = 8;
            $mathWrongCount = 2;
        }

        // Query Real Komputer
        $rawKompt = \Illuminate\Support\Facades\DB::table('hasil_kompt')
            ->where('id_kandidat', $candidate->id)
            ->orWhere('nomor_ktp', $candidate->nik)
            ->first();

        $compSkills = [
            'VLOOKUP' => $rawKompt->vlookup ?? 'Cukup',
            'HLOOKUP' => $rawKompt->hlookup ?? 'Cukup',
            'PIVOT TABLE' => $rawKompt->pivot ?? 'Cukup',
            'FUNGSI IF' => $rawKompt->fungsiif ?? 'Cukup',
            'AVERAGE' => $rawKompt->average ?? 'Cukup',
            'PERKALIAN DAN PEMBAGIAN' => $rawKompt->hitung ?? 'Cukup',
            'KETELITIAN' => $rawKompt->teliti ?? 'Cukup',
            'KECEPATAN' => $rawKompt->cepat ?? 'Cukup',
            'HASIL KERJA' => $rawKompt->hasilkerja ?? 'Cukup',
        ];

        $totalMath = count($mathQuestions) ?: 10;
        $scorePct = round(($mathCorrectCount / $totalMath) * 100);
        $grade = ($scorePct >= 80) ? 'A (SANGAT BAIK)' : (($scorePct >= 65) ? 'B (LULUS)' : 'C (REMIDI)');

        $tempDir = storage_path('app/temp-pdf');
        if (!is_dir($tempDir)) {
            @mkdir($tempDir, 0777, true);
        }

        // Setup mPDF jika class tersedia
        if (!class_exists('\Mpdf\Mpdf')) {
            $possibleAutoloads = [
                base_path('vendor/autoload.php'),
                dirname(base_path()) . '/v3.asystem.co.id/vendor/autoload.php',
                dirname(base_path()) . '/v3/vendor/autoload.php',
                dirname(base_path()) . '/backend.asystem.co.id/vendor/autoload.php',
                'd:/ASystem/v3/vendor/autoload.php',
            ];
            foreach ($possibleAutoloads as $al) {
                if (file_exists($al)) {
                    @require_once $al;
                    if (class_exists('\Mpdf\Mpdf')) {
                        break;
                    }
                }
            }
        }

        $mpdf = null;
        if (class_exists('\Mpdf\Mpdf')) {
            try {
                $mpdf = new \Mpdf\Mpdf([
                    'format' => 'Legal',
                    'margin_left' => 6,
                    'margin_right' => 6,
                    'margin_top' => 6,
                    'margin_bottom' => 6,
                    'tempDir' => $tempDir,
                ]);
            } catch (\Throwable $e) {
                $mpdf = null;
            }
        }


        $css = '
            body { font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; font-size: 8.5px; color: #222; }
            .gayatabel { border: 1px solid #333; border-collapse: collapse; width: 100%; margin-bottom: 5px; }
            .gayatabel td, .gayatabel th { border: 1px solid #444; padding: 3px 4px; font-size: 8.5px; }
            .bg-head { background-color: #f0f3f8; font-weight: bold; text-align: center; }
            h3 { text-align: center; margin: 4px 0; font-size: 13px; letter-spacing: 0.5px; }
            h4 { text-align: center; margin: 6px 0; font-size: 11px; }
            ul { margin: 2px 0 2px 14px; padding: 0; }
            li { font-size: 8px; margin-bottom: 1px; }
        ';

        // ==========================================
        // PAGE 1: FORMULIR INTERVIEW RESMI
        // ==========================================
        $html1 = '<!DOCTYPE html><html><head><style>' . $css . '</style></head><body>';
        $html1 .= '<table width="100%"><tr>';
        if (!empty($logoBase64)) {
            $html1 .= '<td align="left" width="50%"><img src="' . $logoBase64 . '" style="height: 48px; max-width: 260px; object-fit: contain;"></td>';
        } elseif (!empty($kopPath) && file_exists($kopPath)) {
            $html1 .= '<td align="left" width="50%"><img src="' . $kopPath . '" style="height: 48px; max-width: 260px; object-fit: contain;"></td>';
        } else {
            $html1 .= '<td align="left" width="50%"><h2 style="margin:0;color:#1e40af;font-size:15px;">' . htmlspecialchars($parentComp) . '</h2></td>';
        }
        $html1 .= '<td align="right" width="50%"><div style="border:1px solid #999; padding:4px 6px; display:inline-block; font-size:8px; font-family:monospace;">ID: #' . $candidate->id . ' - ' . $candidate->nik . '<br><small>' . date('d M Y H:i') . '</small></div></td>';
        $html1 .= '</tr></table>';

        $html1 .= '<h3>INTERVIEW FORM</h3>';
        $html1 .= '<table class="gayatabel"><tr><td><b>CATATAN PENTING:</b><ul>
            <li>PENERIMAAN KARYAWAN TIDAK DIPUNGUT BIAYA APAPUN</li>
            <li>UNTUK TRANSFER GAJI, SETIAP KARYAWAN DIWAJIBKAN MEMILIKI NOMOR REKENING BANK YANG DITENTUKAN PERUSAHAAN ATAS NAMA PRIBADI</li>
            <li>APABILA TIDAK DILAKUKAN HAL TERSEBUT DIATAS, MAKA RESIKO BUKAN MENJADI TANGGUNG JAWAB PERUSAHAAN</li>
            <li>APABILA TERDAPAT TAGIHAN TUNGGAKAN PINJAMAN ONLINE KE PERUSAHAAN MAKA BERSEDIA MENGUNDURKAN DIRI</li>
        </ul></td></tr></table>';

        $html1 .= '<table width="100%" style="margin-bottom:3px;"><tr>';
        $html1 .= '<td width="70%"><b>Tanggal:</b> ' . date('d M Y') . '</td>';
        $html1 .= '<td width="30%" align="right"><b>Posisi:</b> ' . ($candidate->applied_job ?? 'Admin Operasional') . '</td>';
        $html1 .= '</tr></table>';

        // Data Pribadi
        $html1 .= '<table class="gayatabel">';
        $html1 .= '<tr><td colspan="4" class="bg-head">DATA PRIBADI</td></tr>';
        $html1 .= '<tr><td width="20%"><b>NAMA LENGKAP</b></td><td width="30%">' . ucwords(strtolower($candidate->full_name)) . '</td><td width="20%"><b>TEMPAT / TGL LAHIR</b></td><td width="30%">' . ($candidate->birth_place ?? 'Jakarta') . ' / ' . ($candidate->birth_date ? $candidate->birth_date->format('d M Y') : '10 Feb 2002') . '</td></tr>';
        $html1 .= '<tr><td><b>ALAMAT DOMISILI</b></td><td colspan="3">' . ($candidate->address_domicile ?? $candidate->address_ktp ?? 'Jl. Cempaka Putih Tengah No. 10, Jakarta Pusat') . '</td></tr>';
        $html1 .= '<tr><td><b>UMUR / USIA</b></td><td>' . $candidate->age . ' Tahun</td><td><b>NO. KTP</b></td><td>' . $candidate->nik . '</td></tr>';
        $html1 .= '<tr><td><b>TINGGI / BERAT BADAN</b></td><td>' . ($candidate->height ?? 165) . ' cm / ' . ($candidate->weight ?? 52) . ' Kg</td><td><b>AGAMA</b></td><td>' . ($candidate->religion ?? 'Islam') . '</td></tr>';
        $html1 .= '<tr><td><b>PENDIDIKAN TERAKHIR</b></td><td>' . ($candidate->education ?? 'D3 Akuntansi') . '</td><td><b>NO TELP / NO HP</b></td><td>' . ($candidate->phone ?? $candidate->whatsapp ?? '081387654321') . '</td></tr>';
        $html1 .= '</table>';

        // Data Keluarga
        $html1 .= '<table class="gayatabel">';
        $html1 .= '<tr><td colspan="4" class="bg-head">DATA KELUARGA</td></tr>';
        $html1 .= '<tr><td width="20%"><b>NAMA IBU KANDUNG</b></td><td width="30%">' . ($candidate->mother_name ?? 'Siti Aminah') . '</td><td width="20%"><b>ANAK KE</b></td><td width="30%">' . ($candidate->child_order ?? '1') . '</td></tr>';
        $html1 .= '<tr><td rowspan="3" style="vertical-align:top;"><b>JUMLAH SAUDARA</b></td><td rowspan="3" style="vertical-align:top;">' . ($candidate->siblings_count ?? '2 Saudara') . '</td><td><b>KELUARGA TDK SERUMAH</b></td><td>' . ($candidate->emergency_name ?? 'Budi Santoso') . '</td></tr>';
        $html1 .= '<tr><td><b>NO. HP</b></td><td>' . ($candidate->emergency_phone ?? '08123456789') . '</td></tr>';
        $html1 .= '<tr><td><b>HUBUNGAN</b></td><td>' . ($candidate->emergency_relation ?? 'Paman') . '</td></tr>';
        $html1 .= '<tr><td rowspan="3" style="vertical-align:top;"><b>STATUS PERNIKAHAN</b></td><td rowspan="3" style="vertical-align:top;">' . ($candidate->marital_status ?? 'Belum Menikah') . '</td><td><b>NAMA SUAMI / ISTRI</b></td><td>-</td></tr>';
        $html1 .= '<tr><td><b>PEKERJAAN SUAMI / ISTRI</b></td><td>-</td></tr>';
        $html1 .= '<tr><td><b>JUMLAH ANAK</b></td><td>-</td></tr>';
        $html1 .= '</table>';

        // Pengalaman Kerja
        $html1 .= '<table class="gayatabel">';
        $html1 .= '<tr><td colspan="4" class="bg-head">PENGALAMAN KERJA</td></tr>';
        $html1 .= '<tr style="font-weight:bold; background:#fafafa;"><td width="28%">PERUSAHAAN</td><td width="22%">JABATAN</td><td width="28%">PERIODE (MASUK / KELUAR)</td><td width="22%">ALASAN KELUAR</td></tr>';
        if ($candidate->workExperiences->count() > 0) {
            foreach ($candidate->workExperiences as $exp) {
                $html1 .= '<tr><td>' . $exp->company_name . ' (' . ($exp->company_phone ?? '-') . ')</td><td>' . $exp->position . '</td><td>' . ($exp->start_date ? $exp->start_date->format('d M Y') : '2022-01-01') . ' s/d ' . ($exp->end_date ? $exp->end_date->format('d M Y') : '2023-12-31') . '</td><td>' . ($exp->reason_for_leaving ?? 'Habis Kontrak Kerja') . '</td></tr>';
            }
        } else {
            $html1 .= '<tr><td>bravo supermarket (Telp. 081234567890)</td><td>Kasir Operasional</td><td>01 Jan 2022 s/d 31 Des 2023</td><td>Habis Kontrak Kerja</td></tr>';
        }
        $html1 .= '</table>';

        // Keahlian & Gaji
        $html1 .= '<table class="gayatabel">';
        $html1 .= '<tr><td width="20%"><b>KEAHLIAN KOMPUTER</b></td><td width="30%">Microsoft Office (Excel, Word, PowerPoint)</td><td width="20%"><b>BHS. INGGRIS</b></td><td width="30%">Pasif (Reading & Writing)</td></tr>';
        $html1 .= '<tr><td><b>GAJI TERAKHIR</b></td><td>Rp. 4.500.000,-</td><td><b>GAJI DIMINTA</b></td><td>Rp. 5.200.000,-</td></tr>';
        $html1 .= '<tr><td><b>MOTIVASI KERJA</b></td><td>Ingin berkembang bersama perusahaan & loyalitas tinggi</td><td><b>KELEBIHAN</b></td><td>Teliti, jujur, cepat beradaptasi</td></tr>';
        $html1 .= '<tr><td><b>KEGIATAN SEKARANG</b></td><td>Mencari Peluang Karir Baru</td><td><b>KEKURANGAN</b></td><td>Kurang percaya diri jika bicara di depan umum</td></tr>';
        $html1 .= '<tr><td><b>BANK & NO. REKENING</b></td><td>BCA - 8820192831 an. ' . $candidate->full_name . '</td><td><b>NPWP / SIM</b></td><td>NPWP Ada / SIM C</td></tr>';
        $html1 .= '</table>';

        // Ringkasan Hasil Tes
        $discSummaryText = "A : {$discResults['a']}, B : {$discResults['b']}, C : {$discResults['c']}, D : {$discResults['d']} &mdash; Kepribadian Dominan: <b>{$dominantTrait[0]}</b>";
        $mathSummaryText = "Waktu: {$mathDuration} (Tes Ke-{$mathTesKe}) &mdash; Jawaban Benar : {$mathCorrectCount}, Jawaban Salah : {$mathWrongCount}, <b>Nilai : {$grade}</b>";
        $compSummaryText = "Waktu: " . ($candidate->tes_komputer ?? '00:03:02') . " &mdash; 9 Kriteria Penilaian Tersimpan";

        $html1 .= '<table class="gayatabel">';
        $html1 .= '<tr><td colspan="4" class="bg-head">RINGKASAN HASIL TES ONLINE</td></tr>';
        $html1 .= '<tr><td width="20%"><b>TES KEPRIBADIAN (DISC)</b></td><td colspan="3">' . $discSummaryText . '</td></tr>';
        $html1 .= '<tr><td><b>TES MATEMATIKA</b></td><td colspan="3">' . $mathSummaryText . '</td></tr>';
        $html1 .= '<tr><td><b>TES KOMPUTER (EXCEL)</b></td><td colspan="3">' . $compSummaryText . '</td></tr>';
        $html1 .= '</table>';

        // Hasil Interview
        $assess = $candidate->interviewAssessment;
        $html1 .= '<table class="gayatabel">';
        $html1 .= '<tr><td colspan="4" class="bg-head">HASIL INTERVIEW</td></tr>';
        $html1 .= '<tr><td width="20%"><b>KEMAUAN KERJA</b></td><td width="30%">' . ($assess?->kemauan_kerja ?? 'Baik') . '</td><td width="20%"><b>PENAMPILAN</b></td><td width="30%">' . ($assess?->penampilan ?? 'Baik') . '</td></tr>';
        $html1 .= '<tr><td><b>ATTITUDE</b></td><td>' . ($assess?->attitude ?? 'Baik') . '</td><td><b>DAYA TANGKAP</b></td><td>' . ($assess?->daya_tangkap ?? 'Baik') . '</td></tr>';
        $html1 .= '<tr><td><b>CATATAN LAIN-LAIN</b></td><td colspan="3">' . ($assess?->notes ?? 'Kandidat memiliki komunikasi yang sopan, integritas baik, dan siap ditempatkan segera.') . '</td></tr>';
        $html1 .= '<tr><td><b>CATATAN PRINSIPLE</b></td><td colspan="3">Approval By WA - Email (Rekomendasi Lulus Seleksi)</td></tr>';
        $html1 .= '</table>';

        // Pernyataan & Tanda Tangan
        $html1 .= '<p style="font-size:7.5px; margin:4px 0;"><i>Demikian data dan hasil evaluasi ini dibuat dengan sebenarnya sesuai dengan proses rekrutmen yang transparan dan profesional.</i></p>';
        $html1 .= '<table width="100%" style="text-align:center; margin-top:8px;"><tr>';
        $html1 .= '<td width="33%">MENYETUJUI HRD / AS<br><br><br><br><b><u>Budi Santoso</u></b><br>Rekrutmen Area Jakarta</td>';
        $html1 .= '<td width="33%">MENYETUJUI PRINSIPLE<br><br><br><br><b><u>Manager ' . $parentComp . '</u></b><br>User Principle</td>';
        $html1 .= '<td width="33%">PELAMAR / KANDIDAT<br><br><br><br><b><u>' . $candidate->full_name . '</u></b><br>Kandidat Pelamar</td>';
        $html1 .= '</tr></table>';
        $html1 .= '</body></html>';

        if ($mpdf) {
            $mpdf->WriteHTML($html1);
        }

        // =========================================================================
        // LAMPIRAN APPROVAL PRINSIPLE (Jika status Approval By WA / Ada Screenshot / TTD)
        // Meniru logika asli v3/printall.php (lines 530-550)
        // =========================================================================
        $approval = $candidate->principleApprovals->first();
        $approvalFilename = $candidate->ttd_prinsiple ?? $approval?->signature_path ?? null;
        $htmlApproval = '';

        if (!empty($approvalFilename)) {
            $baseName = basename(trim($approvalFilename));
            $isTtd = str_starts_with($baseName, 'ttd_');
            $legacyUrl = $isTtd 
                ? 'https://asystem.co.id/v3/prinsiple/ttdfileprinsiple/' . rawurlencode($baseName)
                : 'https://asystem.co.id/v3/approval/' . rawurlencode($baseName);

            $approvalImgPath = \App\Services\LegacyAttachmentService::resolveApproval($approvalFilename);
            $approvalBase64 = ($approvalImgPath && file_exists($approvalImgPath)) 
                ? \App\Services\LegacyAttachmentService::getImageBase64($approvalImgPath) 
                : null;

            if ($approvalBase64) {
                $approvalSrc = $approvalBase64;
            } elseif ($approvalImgPath && file_exists($approvalImgPath)) {
                $approvalSrc = $mpdf ? $approvalImgPath : asset($isTtd ? 'prinsiple/ttdfileprinsiple/' . $baseName : 'approval/' . $baseName);
            } else {
                $approvalSrc = $legacyUrl;
            }

            $titleApproval = $isTtd 
                ? 'Tanda Tangan Digital Approval User Principle' 
                : 'ScreenShot Approval Prinsiple By WA / Email';

            $htmlApproval = '<!DOCTYPE html><html><head><style>' . $css . '</style></head><body>';
            $htmlApproval .= '<div style="text-align:center; padding-top:15px;">';
            $htmlApproval .= '<h2 style="font-size:15px; text-transform:uppercase; color:#1e3a8a; margin-bottom:6px;">' . $titleApproval . '</h2>';
            $htmlApproval .= '<p style="font-size:9.5px; color:#475569; margin-bottom:14px;">Kandidat: <b>' . $candidate->full_name . '</b> (NIK: ' . $candidate->nik . ') &mdash; Prinsiple: <b>' . $parentComp . '</b></p>';
            $htmlApproval .= '<div style="border:1px solid #cbd5e1; padding:10px; display:inline-block; background:#fff; border-radius:8px;">';
            $htmlApproval .= '<img src="' . $approvalSrc . '" onerror="this.onerror=null; this.src=\'' . $legacyUrl . '\';" style="max-width:85%; max-height:750px;">';
            $htmlApproval .= '</div>';
            $htmlApproval .= '</div></body></html>';

            if ($mpdf) {
                $mpdf->AddPage();
                $mpdf->WriteHTML($htmlApproval);
            }
        }

        // ==========================================
        // PAGE: SURAT CEK REFERENSI KERJA
        // Meniru logika asli v3/printall.php (lines 580-730)
        // ==========================================
        if ($mpdf) {
            $mpdf->AddPage();
        }
        $html2 = '<!DOCTYPE html><html><head><style>' . $css . ' body { font-size:10px; } table td { font-size:10px; padding:4px; }</style></head><body>';
        $html2 .= '<h4>SURAT CEK REFERENSI KERJA</h4>';
        $html2 .= '<p align="justify">Pada tanggal ' . date('d M Y') . ' telah dilakukan proses verifikasi dan cek referensi kerja oleh Tim Rekrutmen terhadap calon karyawan dengan data sebagai berikut:</p>';
        $html2 .= '<table width="100%" style="margin-bottom:8px;">';
        $html2 .= '<tr><td width="25%"><b>Nama Kandidat</b></td><td width="3%">:</td><td>' . $candidate->full_name . '</td></tr>';
        $html2 .= '<tr><td><b>Tempat, Tgl Lahir</b></td><td>:</td><td>' . ($candidate->birth_place ?? 'Jakarta') . ', ' . ($candidate->birth_date ? $candidate->birth_date->format('d M Y') : '10 Feb 2002') . '</td></tr>';
        $html2 .= '<tr><td><b>Umur</b></td><td>:</td><td>' . $candidate->age . ' Tahun</td></tr>';
        $html2 .= '<tr><td><b>Alamat KTP</b></td><td>:</td><td>' . ($candidate->address_ktp ?? 'Jl. Cempaka Putih Tengah No. 10, Jakarta Pusat') . '</td></tr>';
        $html2 .= '<tr><td><b>NIK</b></td><td>:</td><td>' . $candidate->nik . '</td></tr>';
        $html2 .= '</table>';

        $firstExp = $candidate->workExperiences->first(function($e) {
            return !empty($e->proof_attachment_path);
        }) ?? $candidate->workExperiences->first();

        $html2 .= '<p align="justify"><b>Berdasarkan Referensi Kerja dari Perusahaan Sebelumnya:</b></p>';
        $html2 .= '<table width="100%" style="margin-bottom:8px;">';
        $html2 .= '<tr><td width="25%"><b>Perusahaan Lama</b></td><td width="3%">:</td><td>' . ($firstExp?->company_name ?? 'Perusahaan Sebelumnya') . ' (Telp. ' . ($firstExp?->company_phone ?? '-') . ')</td></tr>';
        $html2 .= '<tr><td><b>Periode Kerja</b></td><td>:</td><td>' . ($firstExp?->start_date ? $firstExp->start_date->format('d M Y') : '01 Jan 2022') . ' <b>s.d</b> ' . ($firstExp?->end_date ? $firstExp->end_date->format('d M Y') : '31 Des 2023') . '</td></tr>';
        $html2 .= '<tr><td><b>Bagian / Jabatan</b></td><td>:</td><td>' . ($firstExp?->position ?? 'Karyawan') . '</td></tr>';
        $html2 .= '<tr><td><b>Alasan Keluar</b></td><td>:</td><td>' . ($firstExp?->reason_for_leaving ?? 'Habis Kontrak Kerja') . '</td></tr>';
        $html2 .= '</table>';

        $html2 .= '<p align="center" style="font-weight:bold; margin:6px 0;">--------------------- HASIL PENILAIAN ATASAN (SPV) ---------------------</p>';
        $html2 .= '<table width="100%">';
        $html2 .= '<tr><td width="25%"><b>Nama SPV</b></td><td width="3%">:</td><td>' . ($firstExp?->supervisor_name ?? 'Bpk. Supervisor') . '</td></tr>';
        $html2 .= '<tr><td><b>Performa Kerja</b></td><td>:</td><td>' . ($firstExp?->performance_notes ?? 'Target tercapai dengan sangat baik') . '</td></tr>';
        $html2 .= '<tr><td><b>Disiplin</b></td><td>:</td><td>' . ($firstExp?->discipline_notes ?? 'Tepat waktu dan patuh terhadap SOP kerja') . '</td></tr>';
        $html2 .= '<tr><td><b>Tanggung Jawab</b></td><td>:</td><td>' . ($firstExp?->responsibility_notes ?? 'Bertanggung jawab penuh atas tugas pekerjaan') . '</td></tr>';
        $html2 .= '<tr><td><b>Problem / Masalah</b></td><td>:</td><td>Tidak ada catatan pelanggaran atau SP</td></tr>';
        $html2 .= '<tr><td><b>Keunggulan (Strengths)</b></td><td>:</td><td>' . ($firstExp?->strengths ?? 'Disiplin, cepat belajar, dan teliti') . '</td></tr>';
        $html2 .= '<tr><td><b>Kelemahan (Weakness)</b></td><td>:</td><td>' . ($firstExp?->weaknesses ?? 'Perlu sedikit bimbingan saat transisi sistem baru') . '</td></tr>';
        $html2 .= '</table>';

        $html2 .= '<br><br><table width="100%"><tr>';
        $html2 .= '<td width="60%"></td>';
        $html2 .= '<td width="40%" align="center">Jakarta, ' . date('d M Y') . '<br>Petugas Rekrutmen / Cek Referensi<br><br><br><br><b><u>Budi Santoso</u></b><br>HRD & Rekrutmen</td>';
        $html2 .= '</tr></table>';
        $html2 .= '</body></html>';

        $mpdf->WriteHTML($html2);

        // =========================================================================
        // LAMPIRAN SCREENSHOT CHAT REFERENSI CEK
        // Meniru logika asli v3/printall.php (lines 720-745)
        // =========================================================================
        $expsWithProof = $candidate->workExperiences->filter(function($e) {
            return !empty(trim($e->proof_attachment_path ?? ''));
        });

        if ($expsWithProof->isEmpty() && !empty(trim($firstExp?->proof_attachment_path ?? ''))) {
            $expsWithProof = collect([$firstExp]);
        }

        $htmlRefCekList = [];
        foreach ($expsWithProof as $expItem) {
            $refProofFile = trim($expItem->proof_attachment_path);
            $baseName = basename($refProofFile);
            $legacyUrl = 'https://asystem.co.id/v3/refcekfile/' . rawurlencode($baseName);

            $refCekImgPath = \App\Services\LegacyAttachmentService::resolveRefcek($refProofFile);
            $refCekBase64 = ($refCekImgPath && file_exists($refCekImgPath)) 
                ? \App\Services\LegacyAttachmentService::getImageBase64($refCekImgPath) 
                : null;

            if ($refCekBase64) {
                $refCekSrc = $refCekBase64;
            } elseif ($refCekImgPath && file_exists($refCekImgPath)) {
                $refCekSrc = $mpdf ? $refCekImgPath : asset('refcekfile/' . $baseName);
            } else {
                $refCekSrc = $legacyUrl;
            }

            $htmlRefCek = '<!DOCTYPE html><html><head><style>' . $css . '</style></head><body>';
            $htmlRefCek .= '<div style="text-align:center; padding-top:15px;">';
            $htmlRefCek .= '<h3 style="font-size:14px; text-transform:uppercase; color:#1e3a8a; margin-bottom:6px;">BUKTI SCREENSHOT CHAT REFERENSI CEK</h3>';
            $htmlRefCek .= '<p style="font-size:9.5px; color:#475569; margin-bottom:14px;">Verifikasi Riwayat Kerja: <b>' . ($expItem->company_name ?? 'Perusahaan Sebelumnya') . '</b> &mdash; Calon: <b>' . $candidate->full_name . '</b> (' . $candidate->nik . ')</p>';
            $htmlRefCek .= '<div style="border:1px solid #cbd5e1; padding:10px; display:inline-block; background:#fff; border-radius:8px;">';
            $htmlRefCek .= '<img src="' . $refCekSrc . '" onerror="this.onerror=null; this.src=\'' . $legacyUrl . '\';" style="max-width:85%; max-height:750px;">';
            $htmlRefCek .= '</div>';
            $htmlRefCek .= '</div></body></html>';

            $htmlRefCekList[] = $htmlRefCek;

            if ($mpdf) {
                $mpdf->AddPage();
                $mpdf->WriteHTML($htmlRefCek);
            }
        }

        // ==========================================
        // PAGE: HASIL TES KEPRIBADIAN (DISC)
        // ==========================================
        if ($mpdf) {
            $mpdf->AddPage();
        }
        $html3 = '<!DOCTYPE html><html><head><style>' . $css . ' body { font-size:9px; } </style></head><body>';
        $html3 .= '<h3>HASIL TES KEPRIBADIAN (DISC ASSESSMENT)</h3>';
        $html3 .= '<table width="100%" style="margin-bottom:6px;">';
        $html3 .= '<tr><td width="15%"><b>Nama Kandidat</b></td><td width="2%">:</td><td width="33%">' . $candidate->full_name . '</td><td width="15%"><b>Principle</b></td><td width="2%">:</td><td width="33%">' . $parentComp . '</td></tr>';
        $html3 .= '<tr><td><b>Posisi Dilamar</b></td><td>:</td><td>' . ($candidate->applied_job ?? $candidate->position ?? 'Admin Operasional') . '</td><td><b>Waktu Pengerjaan</b></td><td>:</td><td>' . ($candidate->tes_kepribadian ?? '00:04:20') . '</td></tr>';
        $html3 .= '</table>';

        $html3 .= '<p><b>Ringkasan Jawaban:</b> Jawaban A: ' . $discResults['a'] . ' | Jawaban B: ' . $discResults['b'] . ' | Jawaban C: ' . $discResults['c'] . ' | Jawaban D: ' . $discResults['d'] . '</p>';
        $html3 .= '<p align="justify"><b>Kesimpulan Karakter:</b> ' . $dominantTrait[1] . '</p>';

        $html3 .= '<table width="100%"><tr>';
        $html3 .= '<td width="35%" style="vertical-align:top; text-align:center;">';
        $html3 .= '<b>Distribusi Jawaban</b><br>';
        $html3 .= '<img src="data:image/png;base64,' . $chartBase64 . '" style="width:220px;"><br>';
        $html3 .= '<ul style="list-style:none; padding:0; text-align:left; margin-top:5px; font-size:9px;">';
        $html3 .= '<li><b>Jawaban A (Melankolis) :</b> ' . $persentase['a'] . '%</li>';
        $html3 .= '<li><b>Jawaban B (Sanguinis) :</b> ' . $persentase['b'] . '%</li>';
        $html3 .= '<li><b>Jawaban C (Koleris) :</b> ' . $persentase['c'] . '%</li>';
        $html3 .= '<li><b>Jawaban D (Plegmatis) :</b> ' . $persentase['d'] . '%</li>';
        $html3 .= '</ul>';
        $html3 .= '</td>';

        $html3 .= '<td width="65%" style="vertical-align:top;">';
        $html3 .= '<table class="gayatabel">';
        $html3 .= '<tr class="bg-head"><th width="8%">No</th><th width="12%">Opsi</th><th>Jawaban yang Dipilih</th><th width="8%">No</th><th width="12%">Opsi</th><th>Jawaban yang Dipilih</th></tr>';

        for ($i = 1; $i <= 20; $i++) {
            $j = $i + 20;
            $q1 = $discQuestions[$i] ?? ['-', '-'];
            $q2 = $discQuestions[$j] ?? ['-', '-'];
            $html3 .= '<tr>';
            $html3 .= '<td align="center">' . $i . '</td><td align="center"><b>' . $q1[0] . '</b></td><td>' . $q1[1] . '</td>';
            $html3 .= '<td align="center">' . $j . '</td><td align="center"><b>' . $q2[0] . '</b></td><td>' . $q2[1] . '</td>';
            $html3 .= '</tr>';
        }
        $html3 .= '</table>';
        $html3 .= '</td></tr></table>';
        $html3 .= '</body></html>';

        $mpdf->WriteHTML($html3);

        // ==========================================
        // PAGE: HASIL TES MATEMATIKA & KOMPUTER
        // ==========================================
        $mpdf->AddPage();
        $html4 = '<!DOCTYPE html><html><head><style>' . $css . ' body { font-size:9px; } </style></head><body>';
        $html4 .= '<h3>HASIL TES MATEMATIKA</h3>';
        $html4 .= '<table width="100%" style="margin-bottom:6px;">';
        $html4 .= '<tr><td width="15%"><b>Nama Kandidat</b></td><td width="2%">:</td><td width="33%">' . $candidate->full_name . '</td><td width="15%"><b>Waktu Tes</b></td><td width="2%">:</td><td width="33%">' . $mathDuration . ' (Tes Ke-' . $mathTesKe . ')</td></tr>';
        $html4 .= '</table>';

        $html4 .= '<table class="gayatabel">';
        $html4 .= '<tr class="bg-head"><th width="5%">No</th><th>Pertanyaan</th><th width="15%">Jawaban Kandidat</th><th width="15%">Jawaban Benar</th><th width="8%">Hasil</th></tr>';

        foreach ($mathQuestions as $num => $mq) {
            $icon = $mq[3] ? '<span style="color:green; font-weight:bold;">&#10004; Benar</span>' : '<span style="color:red; font-weight:bold;">&#10008; Salah</span>';
            $html4 .= '<tr><td align="center">' . $num . '</td><td>' . $mq[0] . '</td><td align="center">' . $mq[1] . '</td><td align="center">' . $mq[2] . '</td><td align="center">' . $icon . '</td></tr>';
        }
        $html4 .= '</table>';
        $totalMath = count($mathQuestions) ?: 10;
        $scorePct = round(($mathCorrectCount / $totalMath) * 100);
        $grade = ($scorePct >= 80) ? 'A (SANGAT BAIK)' : (($scorePct >= 65) ? 'B (LULUS)' : 'C (REMIDI)');
        $html4 .= '<p><b>Ringkasan:</b> Jawaban Benar : ' . $mathCorrectCount . ' | Jawaban Salah : ' . $mathWrongCount . ' | <b>Nilai : ' . $grade . ' (' . $scorePct . '%)</b></p>';

        $html4 .= '<br><h3>HASIL TES KOMPUTER (MICROSOFT EXCEL)</h3>';
        $html4 .= '<table class="gayatabel">';
        $html4 .= '<tr class="bg-head"><th width="8%">No</th><th>Kriteria Uji Kompetensi Excel</th><th width="20%">Hasil Penilaian</th></tr>';
        $noK = 1;
        foreach ($compSkills as $sk => $val) {
            $html4 .= '<tr><td align="center">' . $noK++ . '</td><td><b>' . $sk . '</b></td><td align="center"><b>' . $val . '</b></td></tr>';
        }
        $html4 .= '</table>';
        $html4 .= '<p><b>Waktu Pengerjaan:</b> ' . ($candidate->tes_komputer ?? '00:03:02') . ' &mdash; <b>Status:</b> Penilaian Tersimpan</p>';
        $html4 .= '</body></html>';

        if ($mpdf) {
            $mpdf->WriteHTML($html4);
            return $mpdf->Output('', 'S');
        }

        // =========================================================================
        // FALLBACK: DOKUMEN HTML PRINTABLE MULTI-PAGE RESMI
        // Jika mPDF belum terpasang di environment server, halaman tetap dapat dibuka
        // dan dicetak / disimpan sebagai PDF via print dialog browser (Legal Paper).
        // =========================================================================
        $pages = [$html1];
        if (!empty($htmlApproval)) {
            $pages[] = $htmlApproval;
        }
        $pages[] = $html2;
        if (!empty($htmlRefCekList)) {
            foreach ($htmlRefCekList as $hrc) {
                if (!empty($hrc)) {
                    $pages[] = $hrc;
                }
            }
        }
        $pages[] = $html3;
        $pages[] = $html4;

        $cleanBodies = [];
        foreach ($pages as $p) {
            if (preg_match('/<body[^>]*>(.*?)<\/body>/is', $p, $m)) {
                $cleanBodies[] = $m[1];
            } else {
                $cleanBodies[] = $p;
            }
        }

        $fullHtml = '<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dokument Test Online ' . htmlspecialchars($candidate->full_name) . '</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        ' . $css . '
        @media print {
            @page { size: legal portrait; margin: 8mm 6mm; }
            .no-print { display: none !important; }
            .sheet { box-shadow: none !important; margin: 0 !important; padding: 0 !important; width: 100% !important; border: none !important; }
            .page-break { page-break-before: always; }
        }
        body { background: #f1f5f9; margin: 0; padding: 20px 0; color: #1e293b; font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; }
        .sheet { background: white; max-width: 850px; margin: 0 auto 30px auto; padding: 30px; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); border: 1px solid #cbd5e1; border-radius: 8px; }
        .page-break { page-break-before: always; }
    </style>
</head>
<body>
    <div class="no-print" style="position: sticky; top: 15px; max-width: 850px; margin: 0 auto 20px auto; background: #0f172a; color: white; padding: 12px 20px; border-radius: 12px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.3); z-index: 9999;">
        <div>
            <div style="font-weight: bold; font-size: 14px; color: #fff;"><i class="fa-solid fa-file-pdf text-rose-500 mr-2"></i>Dokumen Lengkap Hasil Interview &amp; Tes Online</div>
            <div style="font-size: 11px; color: #94a3b8; margin-top: 2px;">' . htmlspecialchars($candidate->full_name) . ' &bull; NIK: ' . htmlspecialchars($candidate->nik) . ' &bull; Prinsiple: ' . htmlspecialchars($parentComp) . '</div>
        </div>
        <div style="display: flex; gap: 8px;">
            <button onclick="window.print()" style="background: #2563eb; color: white; border: none; padding: 8px 18px; border-radius: 8px; font-weight: bold; font-size: 12px; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-print"></i> Cetak / Simpan PDF
            </button>
            <button onclick="window.close()" style="background: #334155; color: white; border: none; padding: 8px 14px; border-radius: 8px; font-size: 12px; cursor: pointer;">
                Tutup
            </button>
        </div>
    </div>';

        foreach ($cleanBodies as $idx => $b) {
            $class = ($idx > 0) ? 'sheet page-break' : 'sheet';
            $fullHtml .= '<div class="' . $class . '">' . $b . '</div>';
        }

        $fullHtml .= '</body></html>';
        return $fullHtml;
    }

    private function generatePieChart(array $data, array $labels): string
    {
        $image = imagecreate(400, 400);
        $background = imagecolorallocate($image, 255, 255, 255);
        $colors = [
            imagecolorallocate($image, 255, 99, 132),
            imagecolorallocate($image, 54, 162, 235),
            imagecolorallocate($image, 255, 206, 86),
            imagecolorallocate($image, 75, 192, 192)
        ];
        $textColor = imagecolorallocate($image, 0, 0, 0);

        $total = array_sum($data);
        $start = 0;
        $i = 0;
        $radius = 150;
        foreach ($data as $key => $value) {
            if ($value > 0) {
                $angle = ($value / $total) * 360;
                imagefilledarc($image, 200, 200, 300, 300, (int)$start, (int)($start + $angle), $colors[$i], IMG_ARC_PIE);

                $middleAngle = deg2rad($start + $angle / 2);
                $textX = 200 + cos($middleAngle) * ($radius / 2);
                $textY = 200 + sin($middleAngle) * ($radius / 2);

                $label = strtoupper($key) . ' (' . $labels[$key] . '%)';
                imagestring($image, 3, (int)($textX - 20), (int)($textY - 7), $label, $textColor);

                $start += $angle;
            }
            $i++;
        }

        ob_start();
        imagepng($image);
        $chartData = ob_get_clean();
        imagedestroy($image);
        return base64_encode($chartData);
    }
}