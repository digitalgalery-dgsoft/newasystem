<?php

namespace App\Services;

class CbtQuestionService
{
    /**
     * Mengembalikan 24 Butir Soal Tes Kepribadian DISC Standar ESA Groups CBT.
     * Masing-masing opsi:
     * A = Dominan (D) -> Tegas, Berani, Berorientasi Hasil, Memimpin
     * B = Intensif/Influencing (I) -> Ramah, Antusias, Komunikatif, Persuasif
     * C = Stabil/Steadiness (S) -> Sabar, Pendengar Baik, Setia, Mendukung Tim
     * D = Cermat/Conscientiousness (C) -> Teliti, Rapi, Analitis, Taat Aturan
     */
    public static function getPersonalityQuestions(): array
    {
        return [
            [
                'id' => 1,
                'prompt' => 'Saya adalah pribadi yang...',
                'a' => 'Bersemangat, berani mengambil risiko, dan suka tantangan baru.',
                'b' => 'Mudah bergaul, ramah, dan senang berada di tengah banyak orang.',
                'c' => 'Tenang, sabar, dan lebih suka suasana yang damai serta teratur.',
                'd' => 'Teliti, menyukai detail, dan selalu berusaha melakukan hal dengan benar.'
            ],
            [
                'id' => 2,
                'prompt' => 'Ketika menghadapi suatu masalah, saya cenderung...',
                'a' => 'Mengambil keputusan cepat dan langsung fokus pada solusi konkret.',
                'b' => 'Mendiskusikannya secara terbuka dan memotivasi rekan kerja.',
                'c' => 'Mendengarkan saran semua pihak dan menjaga keharmonisan tim.',
                'd' => 'Menganalisis data, fakta, dan akar penyebab secara mendalam.'
            ],
            [
                'id' => 3,
                'prompt' => 'Dalam lingkungan kerja, orang lain melihat saya sebagai sosok yang...',
                'a' => 'Tegas, independen, dan berorientasi pada pencapaian target.',
                'b' => 'Ceria, antusias, komunikatif, dan membawa energi positif.',
                'c' => 'Dapat diandalkan, loyal, penuh pengertian, dan tidak banyak menuntut.',
                'd' => 'Sistematis, terstruktur, disiplin, dan standar kerjanya tinggi.'
            ],
            [
                'id' => 4,
                'prompt' => 'Sikap saya terhadap perubahan atau hal-hal baru adalah...',
                'a' => 'Melihatnya sebagai kesempatan besar untuk maju dan memimpin.',
                'b' => 'Sangat bersemangat dan tidak sabar ingin segera mencobanya.',
                'c' => 'Membutuhkan waktu adaptasi namun tetap berusaha mendukung kelancaran tim.',
                'd' => 'Memeriksa prosedur, risiko, dan memastikan langkah-langkahnya jelas.'
            ],
            [
                'id' => 5,
                'prompt' => 'Gaya komunikasi yang paling mencerminkan diri saya adalah...',
                'a' => 'Langsung ke pokok persoalan (to the point) tanpa bertele-tele.',
                'b' => 'Ekspresif, persuasif, dan senang bercerita atau berinteraksi.',
                'c' => 'Santun, penuh empati, dan menjadi pendengar yang baik.',
                'd' => 'Akurat, formal, berbasis fakta, dan penuh pertimbangan.'
            ],
            [
                'id' => 6,
                'prompt' => 'Ketika bekerja di dalam tim, peran yang paling saya nikmati adalah...',
                'a' => 'Menjadi pemimpin penggerak yang mengarahkan pencapaian hasil.',
                'b' => 'Membangun semangat kekeluargaan dan mempromosikan ide-ide kreatif.',
                'c' => 'Mendukung pelaksanaan tugas di balik layar dengan konsisten.',
                'd' => 'Memastikan kualitas pekerjaan sesuai standar mutu dan bebas kesalahan.'
            ],
            [
                'id' => 7,
                'prompt' => 'Hal yang paling membuat saya bersemangat dalam bekerja adalah...',
                'a' => 'Meraih kemenangan, prestasi nyata, dan mengatasi rintangan sulit.',
                'b' => 'Apresiasi sosial, pengakuan, dan hubungan kerja yang akrab.',
                'c' => 'Kestabilan, rasa aman, dan kerjasama yang solid tanpa konflik.',
                'd' => 'Pekerjaan yang rapi, sempurna, dan terselesaikan secara efisien.'
            ],
            [
                'id' => 8,
                'prompt' => 'Jika terjadi silang pendapat atau perselisihan, saya akan...',
                'a' => 'Mempertahankan argumen saya dengan tegas jika saya yakin itu benar.',
                'b' => 'Mencari jalan kompromi santai agar suasana tidak menjadi tegang.',
                'c' => 'Mengalah atau meredam situasi demi menjaga hubungan baik antar rekan.',
                'd' => 'Menunjukkan data valid atau SOP untuk menyelesaikan perdebatan.'
            ],
            [
                'id' => 9,
                'prompt' => 'Saya paling tidak nyaman apabila berada dalam situasi...',
                'a' => 'Diperlakukan lambat, tidak berdaya, atau dikendalikan orang lain.',
                'b' => 'Ditolak secara sosial, diabaikan, atau bekerja dalam isolasi.',
                'c' => 'Perubahan mendadak yang tidak menentu dan penuh perselisihan.',
                'd' => 'Pekerjaan yang ceroboh, tidak teratur, atau tanpa aturan jelas.'
            ],
            [
                'id' => 10,
                'prompt' => 'Dalam menyelesaikan tugas harian, prioritas utama saya adalah...',
                'a' => 'Kecepatan dan ketercapaian target kuantitas.',
                'b' => 'Interaksi yang menyenangkan dan hasil yang membanggakan bersama.',
                'c' => 'Ketekunan dan konsistensi hingga semua pekerjaan tuntas.',
                'd' => 'Ketepatan, presisi perhitungan, dan kualitas dokumen yang prima.'
            ],
            [
                'id' => 11,
                'prompt' => 'Ketika menerima kritik dari atasan atau rekan kerja, reaksi saya...',
                'a' => 'Menerimanya sebagai tantangan untuk membuktikan kemampuan lebih.',
                'b' => 'Mungkin sempat sedih sebentar, tapi segera bangkit dengan senyuman.',
                'c' => 'Merenungkannya dalam hati dan memperbaikinya dengan tenang.',
                'd' => 'Mengevaluasi apakah kritik tersebut objektif dan didukung bukti nyata.'
            ],
            [
                'id' => 12,
                'prompt' => 'Di mata teman-teman terdekat, saya dikenal sebagai orang yang...',
                'a' => 'Pantang menyerah, kuat pendirian, dan percaya diri tinggi.',
                'b' => 'Humoris, luwes, mudah mencairkan suasana, dan berjiwa sosial.',
                'c' => 'Setia kawan, jujur, pengertian, dan selalu bersedia menolong.',
                'd' => 'Kritis, bijaksana, tertata rapi, dan dapat dipercaya dalam hal rahasia.'
            ],
            [
                'id' => 13,
                'prompt' => 'Pola kerja yang paling cocok dengan gaya saya adalah...',
                'a' => 'Memiliki wewenang mandiri dan ruang mengambil inisiatif.',
                'b' => 'Dinamis, banyak berinteraksi dengan orang/klien, tidak monoton.',
                'c' => 'Rutinitas yang jelas, teratur, dan lingkungan kerja yang kondusif.',
                'd' => 'Instruksi yang spesifik, target kualitas terukur, dan lingkungan tenang.'
            ],
            [
                'id' => 14,
                'prompt' => 'Dalam merencanakan sesuatu, saya biasanya...',
                'a' => 'Menetapkan sasaran utama dan langsung bertindak mengeksekusinya.',
                'b' => 'Membuat konsep kreatif dan mengajak orang lain bergabung.',
                'c' => 'Memikirkan kesiapan rekan tim dan langkah-langkah yang nyaman.',
                'd' => 'Menyusun checklist terperinci, jadwal teratur, dan antisipasi kendala.'
            ],
            [
                'id' => 15,
                'prompt' => 'Kelebihan utama yang paling menonjol dari diri saya adalah...',
                'a' => 'Keberanian bersikap dan kecepatan mengambil inisiatif.',
                'b' => 'Kemampuan mempengaruhi dan memotivasi orang di sekitar saya.',
                'c' => 'Kesabaran tinggi dan komitmen menjaga keutuhan tim.',
                'd' => 'Ketelitian memeriksa hal-hal kecil yang sering terlewat orang lain.'
            ],
            [
                'id' => 16,
                'prompt' => 'Hal yang paling saya hindari saat beraktivitas adalah...',
                'a' => 'Membuang-buang waktu dengan pembicaraan yang tidak produktif.',
                'b' => 'Kehilangan antusiasme dan suasana kerja yang dingin/kaku.',
                'c' => 'Konflik terbuka, teriakan, atau ketegangan antar individu.',
                'd' => 'Melakukan kesalahan fatal akibat tergesa-gesa tanpa perhitungan.'
            ],
            [
                'id' => 17,
                'prompt' => 'Dalam hal mematuhi peraturan dan instruksi kerja...',
                'a' => 'Saya mematuhinya, namun fleksibel jika ada jalan pintas yang lebih cepat.',
                'b' => 'Saya mengikutinya selama tidak membatasi kreativitas dan interaksi.',
                'c' => 'Saya selalu mematuhi instruksi atasan demi kelancaran operasional.',
                'd' => 'Saya sangat patuh pada SOP baku karena aturan menjaga keselamatan kerja.'
            ],
            [
                'id' => 18,
                'prompt' => 'Saat memimpin suatu proyek atau aktivitas bersama, saya...',
                'a' => 'Mendelegasikan tugas dengan tegas dan menuntut hasil maksimal.',
                'b' => 'Memberi inspirasi, menyemangati tim, dan merayakan tiap capaian.',
                'c' => 'Mendampingi rekan yang kesulitan dan memastikan tidak ada yang tertinggal.',
                'd' => 'Membuat matriks tugas, mengawasi kepatuhan, dan meneliti laporan berkala.'
            ],
            [
                'id' => 19,
                'prompt' => 'Ketika menghadapi tekanan batas waktu (deadline) yang ketat...',
                'a' => 'Adrenalin saya meningkat dan saya terpacu untuk menyelesaikannya lebih cepat.',
                'b' => 'Saya mengajak tim saling menguatkan agar tetap santai namun fokus.',
                'c' => 'Saya tetap tenang, fokus bekerja langkah demi langkah tanpa panik.',
                'd' => 'Saya memprioritaskan tugas terpenting secara berurutan dan terorganisir.'
            ],
            [
                'id' => 20,
                'prompt' => 'Dalam percakapan sehari-hari, saya cenderung lebih sering...',
                'a' => 'Menyampaikan instruksi, pendapat tegas, atau gagasan utama.',
                'b' => 'Menceritakan pengalaman, memuji, atau melontarkan lelucon segar.',
                'c' => 'Mendengarkan keluh kesah teman dan memberikan dukungan moril.',
                'd' => 'Menanyakan detail teknis, kebenaran informasi, atau fakta pendukung.'
            ],
            [
                'id' => 21,
                'prompt' => 'Prinsip hidup yang paling mendekati pandangan saya adalah...',
                'a' => '"Jika kamu ingin sesuatu terlaksana, lakukan sekarang juga dengan berani."',
                'b' => '"Hidup itu indah bila dinikmati bersama teman dan saling menginspirasi."',
                'c' => '"Kebaikan, ketulusan, dan kesetiaan adalah fondasi kedamaian."',
                'd' => '"Kebenaran, ketertiban, dan disiplin adalah kunci keberhasilan sejati."'
            ],
            [
                'id' => 22,
                'prompt' => 'Ketika membeli barang atau memilih suatu layanan, saya lebih memperhatikan...',
                'a' => 'Fungsi utama, prestise, dan seberapa cepat barang itu bisa saya dapatkan.',
                'b' => 'Daya tarik visual, rekomendasi teman, dan kesan tren terkini.',
                'c' => 'Kepercayaan terhadap penjual, kenyamanan penggunaan, dan keamanan.',
                'd' => 'Spesifikasi detail, perbandingan harga, keaslian, dan ulasan teknis.'
            ],
            [
                'id' => 23,
                'prompt' => 'Bagi saya, keberhasilan suatu organisasi paling ditentukan oleh...',
                'a' => 'Ketegasan pemimpin dan keberanian mengejar target pasar agresif.',
                'b' => 'Kekompakan budaya kerja yang ceria dan komunikasi pelanggan yang hebat.',
                'c' => 'Loyalitas karyawan, keharmonisan lingkungan, dan rasa saling percaya.',
                'd' => 'Sistem manajemen mutu yang rapi, audit transparan, dan SOP ketat.'
            ],
            [
                'id' => 24,
                'prompt' => 'Secara umum, kata sifat yang paling tepat menggambarkan karakter saya adalah...',
                'a' => 'Pemberani, Kompetitif, Penentu, Berorientasi Hasil (Dominan).',
                'b' => 'Antusias, Ramah, Percaya Diri, Komunikatif (Intensif / Influencing).',
                'c' => 'Penyabar, Setia, Rukun, Pendukung Terpercaya (Stabil / Steadiness).',
                'd' => 'Teliti, Analitis, Sistematis, Tertata Rapi (Cermat / Conscientiousness).'
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
                    return $dbQuestions->map(function ($q) {
                        return [
                            'id' => $q->id,
                            'question_text' => $q->question_text,
                            'question_type' => $q->question_type,
                            'choices' => $q->parsed_choices,
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
