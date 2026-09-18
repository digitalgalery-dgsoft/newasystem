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
     * Terdiri dari soal perhitungan aritmetika, persentase, margin, logika angka, dan soal cerita kerja.
     */
    public static function getMathQuestions(): array
    {
        return [
            [
                'id' => 1,
                'question_text' => 'Sebuah toko memberikan diskon 25% untuk sebuah produk yang berharga normal Rp 200.000. Berapakah harga produk tersebut setelah didiskon?',
                'question_type' => 'multiple_choice',
                'choices' => [
                    'a' => 'Rp 140.000',
                    'b' => 'Rp 150.000',
                    'c' => 'Rp 160.000',
                    'd' => 'Rp 175.000'
                ],
                'correct_answer' => 'b',
                'explanation' => 'Diskon = 25% x 200.000 = 50.000. Harga akhir = 200.000 - 50.000 = 150.000.'
            ],
            [
                'id' => 2,
                'question_text' => 'Jika 5 orang promotor dapat menyelesaikan penataan display toko dalam waktu 6 jam, berapa jam yang dibutuhkan jika pekerjaan tersebut dikerjakan oleh 10 orang promotor?',
                'question_type' => 'fill_in_the_blank',
                'choices' => null,
                'correct_answer' => '3',
                'explanation' => 'Perbandingan berbalik nilai: (5 x 6) / 10 = 30 / 10 = 3 jam.'
            ],
            [
                'id' => 3,
                'question_text' => 'Seorang sales berhasil menjual 120 unit produk dalam 4 hari. Dengan rasio penjualan yang sama, berapakah total produk yang dapat terjual dalam waktu 14 hari?',
                'question_type' => 'multiple_choice',
                'choices' => [
                    'a' => '420 unit',
                    'b' => '400 unit',
                    'c' => '360 unit',
                    'd' => '480 unit'
                ],
                'correct_answer' => 'a',
                'explanation' => 'Per hari = 120 / 4 = 30 unit. Dalam 14 hari = 14 x 30 = 420 unit.'
            ],
            [
                'id' => 4,
                'question_text' => 'Perhatikan deret angka berikut: 4, 8, 16, 32, 64, ... Berapakah angka berikutnya?',
                'question_type' => 'fill_in_the_blank',
                'choices' => null,
                'correct_answer' => '128',
                'explanation' => 'Pola deret dikalikan 2: 64 x 2 = 128.'
            ],
            [
                'id' => 5,
                'question_text' => 'Sebuah produk dibeli dengan harga modal Rp 80.000 dan dijual dengan mengambil keuntungan (margin) sebesar 20% dari harga modal. Berapakah harga jual produk tersebut?',
                'question_type' => 'multiple_choice',
                'choices' => [
                    'a' => 'Rp 92.000',
                    'b' => 'Rp 96.000',
                    'c' => 'Rp 100.000',
                    'd' => 'Rp 104.000'
                ],
                'correct_answer' => 'b',
                'explanation' => 'Keuntungan = 20% x 80.000 = 16.000. Harga jual = 80.000 + 16.000 = 96.000.'
            ],
            [
                'id' => 6,
                'question_text' => 'Hitunglah nilai operasi hitung berikut: 150 + 25 x 4 - 80 = ... (Ketik angka saja)',
                'question_type' => 'fill_in_the_blank',
                'choices' => null,
                'correct_answer' => '170',
                'explanation' => 'Perkalian didahulukan: 25 x 4 = 100. Maka 150 + 100 - 80 = 250 - 80 = 170.'
            ],
            [
                'id' => 7,
                'question_text' => 'Perhatikan deret angka berikut: 3, 7, 12, 18, 25, ... Angka berapakah yang melengkapi deret tersebut?',
                'question_type' => 'multiple_choice',
                'choices' => [
                    'a' => '31',
                    'b' => '32',
                    'c' => '33',
                    'd' => '35'
                ],
                'correct_answer' => 'c',
                'explanation' => 'Selisih bertambah 1: +4, +5, +6, +7, maka berikutnya +8: 25 + 8 = 33.'
            ],
            [
                'id' => 8,
                'question_text' => 'Dari 200 pelamar kerja, sebanyak 40 orang dinyatakan lulus seleksi tahap pertama. Berapakah persentase kelulusan pelamar tersebut? (Ketik angka saja, contoh: 20)',
                'question_type' => 'fill_in_the_blank',
                'choices' => null,
                'correct_answer' => '20',
                'explanation' => '(40 / 200) x 100% = 20%.'
            ],
            [
                'id' => 9,
                'question_text' => 'Seorang kurir logistik menempuh jarak 180 km dengan kecepatan rata-rata 60 km/jam. Jika ia berangkat pukul 08.00 WIB, pada pukul berapakah ia akan tiba di tujuan?',
                'question_type' => 'multiple_choice',
                'choices' => [
                    'a' => 'Pukul 10.30 WIB',
                    'b' => 'Pukul 11.00 WIB',
                    'c' => 'Pukul 11.30 WIB',
                    'd' => 'Pukul 12.00 WIB'
                ],
                'correct_answer' => 'b',
                'explanation' => 'Waktu tempuh = 180 / 60 = 3 jam. 08.00 + 3 jam = 11.00 WIB.'
            ],
            [
                'id' => 10,
                'question_text' => 'Sebuah karton kemasan berisi 24 kaleng minuman. Jika sebuah minimarket memesan 15 karton, berapa total kaleng minuman yang diterima? (Ketik angka saja)',
                'question_type' => 'fill_in_the_blank',
                'choices' => null,
                'correct_answer' => '360',
                'explanation' => '24 x 15 = 360 kaleng.'
            ]
        ];
    }
}
