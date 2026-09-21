<?php

namespace App\Services;

class CbtQuestionService
{
    /**
     * Mengembalikan 40 Butir Soal Tes Kepribadian Master Sistem Lama (tb_kepribadian).
     * Florence Littauer Personality Profile (DISC/Temperamen):
     * A = Melankolis (Analitis, Teratur, Rapi, Taat Aturan)
     * B = Sanguinis (Ramah, Antusias, Komunikatif, Ceria)
     * C = Koleris (Tegas, Berani, Berorientasi Target & Hasil, Memimpin)
     * D = Plegmatis (Tenang, Sabar, Rukun, Pendengar Baik)
     */
    public static function getPersonalityQuestions(): array
    {
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('tb_kepribadian')) {
                $dbQuestions = \Illuminate\Support\Facades\DB::table('tb_kepribadian')->orderBy('id')->get();
                if ($dbQuestions->isNotEmpty()) {
                    return $dbQuestions->map(function ($q) {
                        return [
                            'id' => intval($q->id),
                            'prompt' => 'Pilih salah satu kata/sifat yang paling menggambarkan diri Anda:',
                            'a' => trim($q->pilihan_a ?? ''),
                            'b' => trim($q->pilihan_b ?? ''),
                            'c' => trim($q->pilihan_c ?? ''),
                            'd' => trim($q->pilihan_d ?? ''),
                        ];
                    })->toArray();
                }
            }
        } catch (\Throwable $e) {
            // Fallback ke array default jika query gagal
        }

        return self::getDefaultLegacyPersonalityQuestions();
    }

    /**
     * Fallback 40 butir soal tes kepribadian master sistem lama ESA Groups (tb_kepribadian)
     */
    public static function getDefaultLegacyPersonalityQuestions(): array
    {
        return [
            [
                'id' => 1,
                'prompt' => 'Pilih salah satu kata/sifat yang paling menggambarkan diri Anda:',
                'a' => 'Logis',
                'b' => 'Antusias',
                'c' => 'Berani',
                'd' => 'Mudah Menyesuaikan Diri',
            ],
            [
                'id' => 2,
                'prompt' => 'Pilih salah satu kata/sifat yang paling menggambarkan diri Anda:',
                'a' => 'Tuntas',
                'b' => 'Ceria',
                'c' => 'Menyukai Logika dan Fakta',
                'd' => 'Tenang, Tidak Mudah Terusik',
            ],
            [
                'id' => 3,
                'prompt' => 'Pilih salah satu kata/sifat yang paling menggambarkan diri Anda:',
                'a' => 'Rela Berkorban',
                'b' => 'Mudah Bergaul',
                'c' => 'Teguh Pendirian',
                'd' => 'Mudah Menerima',
            ],
            [
                'id' => 4,
                'prompt' => 'Pilih salah satu kata/sifat yang paling menggambarkan diri Anda:',
                'a' => 'Toleran',
                'b' => 'Mempesona',
                'c' => 'Suka Bersaing',
                'd' => 'Emosi Terkontrol',
            ],
            [
                'id' => 5,
                'prompt' => 'Pilih salah satu kata/sifat yang paling menggambarkan diri Anda:',
                'a' => 'Menghargai',
                'b' => 'Menyenangkan Orang Lain',
                'c' => 'Gesit Dalam Segala Situasi',
                'd' => 'Menahan Diri',
            ],
            [
                'id' => 6,
                'prompt' => 'Pilih salah satu kata/sifat yang paling menggambarkan diri Anda:',
                'a' => 'Sensitif Terhadap Orang Lain',
                'b' => 'Penuh Gairah Hidup',
                'c' => 'Mandiri',
                'd' => 'Mudah Puas',
            ],
            [
                'id' => 7,
                'prompt' => 'Pilih salah satu kata/sifat yang paling menggambarkan diri Anda:',
                'a' => 'Perencana',
                'b' => 'Mendorong Orang Lain',
                'c' => 'Berpikir Positif',
                'd' => 'Penyabar',
            ],
            [
                'id' => 8,
                'prompt' => 'Pilih salah satu kata/sifat yang paling menggambarkan diri Anda:',
                'a' => 'Terjadwal',
                'b' => 'Impulsif, Tidak Suka Pikir Panjang',
                'c' => 'Penuh Keyakinan',
                'd' => 'Pendiam',
            ],
            [
                'id' => 9,
                'prompt' => 'Pilih salah satu kata/sifat yang paling menggambarkan diri Anda:',
                'a' => 'Teratur',
                'b' => 'Optimis',
                'c' => 'Berbicara Terang-terangan',
                'd' => 'Menerima Apa Saja',
            ],
            [
                'id' => 10,
                'prompt' => 'Pilih salah satu kata/sifat yang paling menggambarkan diri Anda:',
                'a' => 'Konsisten',
                'b' => 'Humoris',
                'c' => 'Suka Mendominasi',
                'd' => 'Merespon Apa Saja',
            ],
            [
                'id' => 11,
                'prompt' => 'Pilih salah satu kata/sifat yang paling menggambarkan diri Anda:',
                'a' => 'Runtut',
                'b' => 'Menyenangkan Sebagai Teman',
                'c' => 'Bersedia Mengambil Resiko',
                'd' => 'Penuh Strategi, Perasa dan Sabar',
            ],
            [
                'id' => 12,
                'prompt' => 'Pilih salah satu kata/sifat yang paling menggambarkan diri Anda:',
                'a' => 'Menyukai Seni',
                'b' => 'Bersemangat',
                'c' => 'Percaya Diri',
                'd' => 'Seimbang dan Konsisten',
            ],
            [
                'id' => 13,
                'prompt' => 'Pilih salah satu kata/sifat yang paling menggambarkan diri Anda:',
                'a' => 'Menyukai Kesempurnaan',
                'b' => 'Memberi Inspirasi',
                'c' => 'Dapat Bekerja Sendiri',
                'd' => 'Bertahan Tidak Menyakiti Hati Orang Lain',
            ],
            [
                'id' => 14,
                'prompt' => 'Pilih salah satu kata/sifat yang paling menggambarkan diri Anda:',
                'a' => 'Suka Menginstropeksi',
                'b' => 'Menyatakan Perasaan Terang-terangan',
                'c' => 'Mudah Mengambil Keputusan',
                'd' => 'Sarkastis',
            ],
            [
                'id' => 15,
                'prompt' => 'Pilih salah satu kata/sifat yang paling menggambarkan diri Anda:',
                'a' => 'Memiliki Apresiasi Musik',
                'b' => 'Mudah Membaur',
                'c' => 'Memimpin Orang Lain',
                'd' => 'Mendamaikan Pertikaian',
            ],
            [
                'id' => 16,
                'prompt' => 'Pilih salah satu kata/sifat yang paling menggambarkan diri Anda:',
                'a' => 'Cepat Tanggap',
                'b' => 'Suka Bicara',
                'c' => 'Berpendirian Teguh',
                'd' => 'Toleran',
            ],
            [
                'id' => 17,
                'prompt' => 'Pilih salah satu kata/sifat yang paling menggambarkan diri Anda:',
                'a' => 'Loyal',
                'b' => 'Penuh Semangat',
                'c' => 'Mengarahkan Orang Lain',
                'd' => 'Senang Mendengarkan',
            ],
            [
                'id' => 18,
                'prompt' => 'Pilih salah satu kata/sifat yang paling menggambarkan diri Anda:',
                'a' => 'Suka Membuat Grafik dan Daftar Tugas',
                'b' => 'Dicintai',
                'c' => 'Dapat Memimpin',
                'd' => 'Jarang Iri Hati',
            ],
            [
                'id' => 19,
                'prompt' => 'Pilih salah satu kata/sifat yang paling menggambarkan diri Anda:',
                'a' => 'Perfeksionis',
                'b' => 'Populer',
                'c' => 'Produktif',
                'd' => 'Terbuka',
            ],
            [
                'id' => 20,
                'prompt' => 'Pilih salah satu kata/sifat yang paling menggambarkan diri Anda:',
                'a' => 'Memiliki Batasan Dalam Berperilaku',
                'b' => 'Bergejolak Dengan Semangat Hidup',
                'c' => 'Tidak Kenal Takut',
                'd' => 'Stabil, Tidak Mudah terpengaruh Situasi',
            ],
            [
                'id' => 21,
                'prompt' => 'Pilih salah satu kata/sifat yang paling menggambarkan diri Anda:',
                'a' => 'Pemalu',
                'b' => 'Suka Pamer',
                'c' => 'Suka Memerintah',
                'd' => 'Tanpa Ekspresi',
            ],
            [
                'id' => 22,
                'prompt' => 'Pilih salah satu kata/sifat yang paling menggambarkan diri Anda:',
                'a' => 'Sulit Memaafkan',
                'b' => 'Kurang Teratur',
                'c' => 'Tidak Sensitif',
                'd' => 'Tidak Antusias',
            ],
            [
                'id' => 23,
                'prompt' => 'Pilih salah satu kata/sifat yang paling menggambarkan diri Anda:',
                'a' => 'Pendendam',
                'b' => 'Suka Bercerita Berulang-ulang',
                'c' => 'Melawan Cara Orang Lain',
                'd' => 'Tidak Suka Terlibat Dalam Masalah Pelik',
            ],
            [
                'id' => 24,
                'prompt' => 'Pilih salah satu kata/sifat yang paling menggambarkan diri Anda:',
                'a' => 'Cerewet',
                'b' => 'Mudah Lupa',
                'c' => 'Blak-blakan',
                'd' => 'Penakut',
            ],
            [
                'id' => 25,
                'prompt' => 'Pilih salah satu kata/sifat yang paling menggambarkan diri Anda:',
                'a' => 'Sulit Percaya',
                'b' => 'Lebih Banyak Bicara Daripada Mendengarkan',
                'c' => 'Tidak Sabar',
                'd' => 'Sulit Memutuskan',
            ],
            [
                'id' => 26,
                'prompt' => 'Pilih salah satu kata/sifat yang paling menggambarkan diri Anda:',
                'a' => 'Tidak Populer',
                'b' => 'Mudah Berubah-ubah Emosinya',
                'c' => 'Kurang Memberikan Kasih Sayang',
                'd' => 'Tidak Tertarik dengan Kelompok',
            ],
            [
                'id' => 27,
                'prompt' => 'Pilih salah satu kata/sifat yang paling menggambarkan diri Anda:',
                'a' => 'Sulit Dipuaskan',
                'b' => 'Tidak Konsisten',
                'c' => 'Keras Kepala',
                'd' => 'Ragu-ragu',
            ],
            [
                'id' => 28,
                'prompt' => 'Pilih salah satu kata/sifat yang paling menggambarkan diri Anda:',
                'a' => 'Pesimis',
                'b' => 'Membiarkan Orang Lain Bertindak Sesukanya',
                'c' => 'Memiliki Harga Diri Tinggi',
                'd' => 'Dingin',
            ],
            [
                'id' => 29,
                'prompt' => 'Pilih salah satu kata/sifat yang paling menggambarkan diri Anda:',
                'a' => 'Mudah Merasa Terasing',
                'b' => 'Mudah Marah',
                'c' => 'Suka Berdebat',
                'd' => 'Tidak Punya Tujuan',
            ],
            [
                'id' => 30,
                'prompt' => 'Pilih salah satu kata/sifat yang paling menggambarkan diri Anda:',
                'a' => 'Suka Melihat Sisi Buruk dari Pergaulan',
                'b' => 'Naif',
                'c' => 'Nekat',
                'd' => 'Masa Bodoh Terhadap Lingkungan',
            ],
            [
                'id' => 31,
                'prompt' => 'Pilih salah satu kata/sifat yang paling menggambarkan diri Anda:',
                'a' => 'Menarik Diri Dari Pergaulan',
                'b' => 'Suka Dipuji',
                'c' => 'Workaholik',
                'd' => 'Mudah Resah',
            ],
            [
                'id' => 32,
                'prompt' => 'Pilih salah satu kata/sifat yang paling menggambarkan diri Anda:',
                'a' => 'Terlalu Perasa',
                'b' => 'Mendominasi Pembicaraan',
                'c' => 'Mudah Menyinggung Perasaan Orang',
                'd' => 'Tidak Suka Konflik',
            ],
            [
                'id' => 33,
                'prompt' => 'Pilih salah satu kata/sifat yang paling menggambarkan diri Anda:',
                'a' => 'Mudah Tertekan',
                'b' => 'Tidak Rapi',
                'c' => 'Memaksa Mengambil Kontrol',
                'd' => 'Kurang Yakin',
            ],
            [
                'id' => 34,
                'prompt' => 'Pilih salah satu kata/sifat yang paling menggambarkan diri Anda:',
                'a' => 'Tertutup',
                'b' => 'Bertindak Tidak Berdasarkan Logika',
                'c' => 'Tidak Toleran',
                'd' => 'Tidak Pedulian',
            ],
            [
                'id' => 35,
                'prompt' => 'Pilih salah satu kata/sifat yang paling menggambarkan diri Anda:',
                'a' => 'Moody',
                'b' => 'Berantakan',
                'c' => 'Mempengaruhi Orang Lain dengan Lihai',
                'd' => 'Bicaranya Pelan Kalau Didesak',
            ],
            [
                'id' => 36,
                'prompt' => 'Pilih salah satu kata/sifat yang paling menggambarkan diri Anda:',
                'a' => 'Tidak Mudah Percaya',
                'b' => 'Suka Menjadi Pusat Perhatian',
                'c' => 'Tidak Mudah Dibujuk',
                'd' => 'Lambat / Lelet',
            ],
            [
                'id' => 37,
                'prompt' => 'Pilih salah satu kata/sifat yang paling menggambarkan diri Anda:',
                'a' => 'Penyendiri',
                'b' => 'Jika Berbicara / Tertawa Sangat Keras',
                'c' => 'Memperlihatkan Kekuatan Saya',
                'd' => 'Malas',
            ],
            [
                'id' => 38,
                'prompt' => 'Pilih salah satu kata/sifat yang paling menggambarkan diri Anda:',
                'a' => 'Mudah Curiga',
                'b' => 'Sulit Berkonsentrasi',
                'c' => 'Sering Marah',
                'd' => 'Tidak Termotivasi Untuk Bekerja',
            ],
            [
                'id' => 39,
                'prompt' => 'Pilih salah satu kata/sifat yang paling menggambarkan diri Anda:',
                'a' => 'Suka Membalas Dendam',
                'b' => 'Mudah Bosan',
                'c' => 'Tergesa-gesa',
                'd' => 'Menolak Dilibatkan',
            ],
            [
                'id' => 40,
                'prompt' => 'Pilih salah satu kata/sifat yang paling menggambarkan diri Anda:',
                'a' => 'Suka Mengkritik',
                'b' => 'Mudah Berubah Pemikirannya',
                'c' => 'Cerdik dan Lihai',
                'd' => 'Mudah Mengalah',
            ],
        ];
    }

    /**
     * Mengembalikan Bank Soal Matematika Standar CBT Recruitment ESA Groups (10 Menit).
     * Membaca langsung dari Master Soal tb_math yang aktif.
     */
    public static function getMathQuestions(): array
    {
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('tb_math')) {
                $dbQuestions = \App\Models\MathQuestion::active()->orderBy('id')->get();
                if ($dbQuestions->isNotEmpty()) {
                    $legacyDefaults = collect(self::getDefaultLegacyMathQuestions())->keyBy('id');

                    return $dbQuestions->map(function ($q) use ($legacyDefaults) {
                        $choices = $q->parsed_choices;
                        // Jika soal bertipe multiple_choice tapi choices kosong, fallback ke pilihan default legacy
                        if ($q->question_type === 'multiple_choice' && empty($choices) && isset($legacyDefaults[$q->id])) {
                            $choices = $legacyDefaults[$q->id]['choices'] ?? [];
                        }

                        return [
                            'id' => $q->id,
                            'question_text' => $q->question_text,
                            'question_type' => $q->question_type,
                            'choices' => $choices,
                            'correct_answer' => $q->correct_answer,
                            'explanation' => '',
                        ];
                    })->toArray();
                }
            }
        } catch (\Throwable $e) {
            // Fallback ke array default jika query gagal
        }

        return self::getDefaultLegacyMathQuestions();
    }

    /**
     * Fallback 10 butir soal matematika master sistem lama ESA Groups (tb_math)
     */
    public static function getDefaultLegacyMathQuestions(): array
    {
        return [
            [
                'id' => 1,
                'question_text' => 'Ani membeli Lampu Philips 50 Watt Seharga Rp. 200,000,- di C4 Buaran, dan di All C4 Sedang ada Promo Diskon 15% Untuk Pembelian Lampu Philips. Berapa Rupiah Yang Harus Dibayar Ani?',
                'question_type' => 'fill_in_the_blank',
                'choices' => null,
                'correct_answer' => '170000',
                'explanation' => 'Diskon = 15% x 200.000 = 30.000. Harus dibayar = 200.000 - 30.000 = 170.000.'
            ],
            [
                'id' => 2,
                'question_text' => 'Yani Membeli 2 Buah Bedak Loreal Seharga Rp. 300,000,- di Matahari Departemen Store Pejaten dan Sedang Ada Promo Untuk Pembelian Kedua Diskon 35%. Berapa Rupiah Yang Harus Dibayar Yani?',
                'question_type' => 'fill_in_the_blank',
                'choices' => null,
                'correct_answer' => '495000',
                'explanation' => 'Barang 1 = 300.000. Barang 2 diskon 35% = 300.000 - 105.000 = 195.000. Total = 495.000.'
            ],
            [
                'id' => 3,
                'question_text' => 'SPG Dancow di C4 Cempaka Mas Mempunyai Target Sebanyak Rp. 7,000,000,- dan Baru Mencapai Target Sebanyak Rp. 5,000,000,-. Sudah Berapa Persen Pencapaian SPG Tersebut',
                'question_type' => 'fill_in_the_blank',
                'choices' => null,
                'correct_answer' => '71.43%',
                'explanation' => '(5.000.000 / 7.000.000) x 100% = 71.43%.'
            ],
            [
                'id' => 4,
                'question_text' => 'Bagas Membeli Wafer TimTam 200Gr Seharga Rp. 5,250,- sebanyak 15 Bungkus di Lotte Kelapa Gading dan Sedang Ada Promo Diskon 15%. Berapa Rupiah Yang Harus Dibayar Bagas?',
                'question_type' => 'multiple_choice',
                'choices' => [
                    'A' => '66,937',
                    'B' => '67,250',
                    'C' => '64,500',
                    'D' => '69,500',
                    'E' => '70,500'
                ],
                'correct_answer' => 'A',
                'explanation' => '15 x 5.250 = 78.750. Diskon 15% = 11.812,5. Bayar = 66.937,5 (dibulatkan 66.937).'
            ],
            [
                'id' => 5,
                'question_text' => 'Putri Membeli Boneka Rp. 50,000,- Kemudian Boneka Itu Dijual kembali dengan Harga Rp. 80,000. Berapa persen Keuntungan Putri?',
                'question_type' => 'multiple_choice',
                'choices' => [
                    'A' => '30%',
                    'B' => '40%',
                    'C' => '50%',
                    'D' => '60%',
                    'E' => '70%'
                ],
                'correct_answer' => 'D',
                'explanation' => 'Untung = 80.000 - 50.000 = 30.000. Persen = (30.000 / 50.000) x 100% = 60%.'
            ],
            [
                'id' => 6,
                'question_text' => 'Lanjutkan perhitungan berikut 24, 20, 16, 12, ......, .......',
                'question_type' => 'fill_in_the_blank',
                'choices' => null,
                'correct_answer' => '8,4',
                'explanation' => 'Pola deret berkurang 4: 12 - 4 = 8, 8 - 4 = 4.'
            ],
            [
                'id' => 7,
                'question_text' => 'Ibu mempunyai uang sebesar Rp. 30,000,- uang itu dibelikan lauk pauk Rp. 12,000,- Sayuran Rp. 5,000,- dan Minyak Goreng Rp. 4,000,-. Berapa Sisa uang Ibu?',
                'question_type' => 'multiple_choice',
                'choices' => [
                    'A' => '10,000',
                    'B' => '9,000',
                    'C' => '8,000',
                    'D' => '7,000',
                    'E' => '6,000'
                ],
                'correct_answer' => 'B',
                'explanation' => 'Total belanja = 12.000 + 5.000 + 4.000 = 21.000. Sisa = 30.000 - 21.000 = 9.000.'
            ],
            [
                'id' => 8,
                'question_text' => 'Angga mempunyai uang sebesar Rp. 4,500,000,- dan ia berniat membeli sebuah handicam seharga Rp. 2,500,000,- sebelum diskon, harga handycam tersebut adalah 20% setelah itu Angga juga membelanjakan uangnya untuk keperluan lain sebesar Rp. 1,500,000,-. Berapa sisa uang Angga Saat Ini?',
                'question_type' => 'multiple_choice',
                'choices' => [
                    'A' => '1,000,000',
                    'B' => '1,200,000',
                    'C' => '1,300,000',
                    'D' => '1,400,000',
                    'E' => '1,500,000'
                ],
                'correct_answer' => 'A',
                'explanation' => 'Harga handicam setelah diskon 20% = 2.000.000. Total belanja = 2.000.000 + 1.500.000 = 3.500.000. Sisa = 4.500.000 - 3.500.000 = 1.000.000.'
            ],
            [
                'id' => 9,
                'question_text' => 'Sinta membeli 2 pcs pelembab Loreal seharga Rp. 80,000,- untuk satu pelembab dan di MDS Pejaten sedang ada promosi untuk pembelian kedua diskon 75%. Berapa Rupiah yang harus dibayar santi?',
                'question_type' => 'fill_in_the_blank',
                'choices' => null,
                'correct_answer' => '100000',
                'explanation' => 'Pcs 1 = 80.000. Pcs 2 diskon 75% = 20.000. Total = 100.000.'
            ],
            [
                'id' => 10,
                'question_text' => 'SPG Arnots di C4 KJL mempunyai target sebanyak Rp. 12,000,000,- dan baru mencapai target sebanyak Rp. 5,000,000,-. Sudah berapa persen pencapaian SPG tersebut?',
                'question_type' => 'fill_in_the_blank',
                'choices' => null,
                'correct_answer' => '41.67%',
                'explanation' => '(5.000.000 / 12.000.000) x 100% = 41.67%.'
            ],
        ];
    }
}
