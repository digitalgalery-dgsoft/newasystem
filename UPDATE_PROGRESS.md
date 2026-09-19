# 🚀 Ringkasan Perkembangan & Progress Update ASystem Portal
**Support System ESA Groups** (PT Arina Multikarya, PT Alva Karya Perkasa, PT Anugrah Terpercaya Kerja, PT Arina Bintang Oetama, PT Anugrah Tri Berkah)  
*Terakhir diperbarui: 19 September 2026*

---

## 📌 Ringkasan Umum
Aplikasi **ASystem Portal** telah mengalami serangkaian pembaruan besar, modernisasi antarmuka (UI/UX), restrukturisasi keamanan dan hak akses pengguna, serta integrasi modul-modul operasional HRD dan kepegawaian yang selaras dengan alur kerja ESA Groups.

---

## 🏆 Milestone & Fitur yang Telah Diselesaikan

### 1. 🏠 Halaman Beranda / Home (Full-Width Welcome Hero)
- **Tampilan Full-Width (`w-full`)**: Menghilangkan pembatas kontainer sempit (`max-w-7xl mx-auto`) sehingga seluruh kartu pada halaman beranda membentang penuh ke seluruh lebar layar tanpa menyisakan ruang kosong di sisi kiri dan kanan.
- **Kartu Sambutan Hangat (Hero Welcome)**:
  - Salam pembuka dinamis berdasarkan waktu (*Selamat Pagi / Siang / Sore / Malam*).
  - Penampilan nama lengkap dan foto/avatar profil pengguna yang sedang login.
  - Indikator status sesi online dan status sistem *ASystem Cloud Active*.
- **Kartu Identitas Pengguna & Profil Akun**:
  - Penataan grid responsif (`xl:grid-cols-4`) menampilkan detail: Nama Lengkap, Email, Hak Akses, NIK, Jabatan, Area Penempatan, Prinsiple/Mitra, serta Tipe & Status Pegawai.
  - Tombol keluar akun (*Logout*) yang mudah diakses.
- **Penyederhanaan Tampilan Home**:
  - Menghilangkan bagian *Katalog Fitur & Modul Utama* serta baris metrik statistik di halaman home agar antarmuka menjadi bersih, fokus pada sambutan dan identitas pengguna.
  - Pengubahan nama menu di sidebar menjadi **"Beranda"** (`fa-house`).

---

### 2. 🔐 Manajemen Hak Akses Role & Proteksi Menu Master Data
- **Visibilitas Menu Master Data**:
  - Menu **Master Data** pada bilah samping (*sidebar navigation*) **hanya tampil** untuk akun dengan hak akses **Administrator** (`role === 'admin'`).
  - Submenu administratif di dalam modul rekrutmen seperti **Master User Prinsiple** dan **Setting AI & WA** juga disembunyikan untuk user non-admin.
  - User lain (karyawan inhouse, karyawan ratecard, recruiter) hanya dapat melihat dan mengakses **Modul Operasional**.
- **Proteksi Backend Route (Middleware `EnsureUserIsAdmin`)**:
  - Dibuat middleware khusus `EnsureUserIsAdmin` dan didaftarkan pada `bootstrap/app.php` dengan alias `'admin'`.
  - Mengamankan seluruh route Master Data (`/master/*`, `/odoo-setting`, `/user-prinsiple`, `/ai-settings`).
  - Upaya akses langsung via URL oleh user non-admin akan dialihkan kembali ke `/fitur` dengan pesan error:
    > *"Akses Ditolak! Menu Master Data hanya dapat diakses oleh Administrator."*

---

### 3. 👤 Prosedur Login & Autentikasi Karyawan
- **Ketentuan Akses Masuk**:
  - **Karyawan Inhouse**: Otomatis memiliki hak akses login ke sistem.
  - **Karyawan RateCard**: Hanya dapat login jika telah diberikan izin akses (`akses_login = 1`) oleh Administrator HR melalui tombol toggle di Master Karyawan.
  - **Karyawan Resign**: Otomatis diblokir dari akses login ke dalam sistem.
- **Kredensial Login**:
  - **Username**: Alamat email resmi karyawan.
  - **Password Default**: Tanggal lahir karyawan dengan format `ddmmyyyy` (diekstrak otomatis dari NIK atau field tanggal lahir).
- **Fitur Kontrol di Master Karyawan**:
  - Tombol *Toggle Izin Akses Login* untuk karyawan RateCard dengan feedback SweetAlert2.
  - Badge status login visual (*Aktif Inhouse / Aktif Diberi Izin / Terkunci RateCard*).
  - Modal detail kredensial dan panduan login karyawan.

---

### 4. 👥 Penyempurnaan Master Karyawan
- **Struktur Tabel Presisi (12 Kolom)**:
  - Memperbaiki ketidaksesuaian/pergeseran antara header dan baris data (No, Aksi, NIK, Nama Karyawan, Entitas, Tipe, Jabatan, Area, Prinsiple, Tanggal Join, Masa Kerja 5 Thn, Status).
- **Default Filter Karyawan Aktif**:
  - Secara default tabel hanya menampilkan karyawan dengan status **Aktif** (`Aktiv`).
  - Disediakan filter pill untuk beralih ke status **Review** dan **Resign**.
- **Klasifikasi Otomatis Inhouse vs RateCard**:
  - Otomatis ditentukan berdasarkan kesesuaian nama prinsiple dengan 5 entitas naungan:
    - **Inhouse**: Jika terhubung ke `PT Arina Multi Karya (AMK)`, `PT Alva Karya Perkasa (AKP)`, `PT Anugrah Terpercaya Kerja (ATK)`, `PT Arina Bintang Oetama (ABO)`, atau `PT Anugrah Tri Berkah (ATB)`.
    - **RateCard**: Jika ditempatkan pada prinsiple rekanan eksternal selain 5 entitas di atas.

---

### 5. 🔄 Integrasi Sinkronisasi Odoo ERP (5 Entitas)
- **Halaman Setting Sinkronisasi (`/odoo-setting`)**:
  - Tab navigasi untuk 5 entitas: AMK, AKP, ATK, ABO, dan ATB.
  - Form konfigurasi parameter XML-RPC: *URL Host, Port, Database Name, Username, dan API Key / Password*.
  - Tombol uji koneksi (*Test Connection*) per entitas.
  - Tombol sinkronisasi per entitas (*Sync Entity*), sinkronisasi massal (*Sync All*), dan fitur pembersihan duplikasi (*Cleanup Duplicates*).

---

### 6. 💼 Modul Talent Pool / Rekrutmen & Master User Prinsiple
- **Restrukturisasi Sidebar**:
  - Mengubah nama menu dari *"Interview"* menjadi **"Talent Pool / Rekrutment"**.
  - Menghilangkan badge teks *"SUB-MENU"* pada accordion sidebar untuk tampilan yang lebih rapi.
- **Master User Prinsiple (`/user-prinsiple`)**:
  - Manajemen akun user prinsiple/klien (Samsung, L'Oreal, Unilever, Mayora, dll.).
  - Fitur kirim kredensial & tautan portal persetujuan via WhatsApp/Email.
- **AI Candidate Ranking & Leaderboard (`/airanking`)**:
  - Showcase podium Top 3 kandidat terbaik dengan skor kecocokan AI.
  - Filter tier kandidat (*Green Tier: >=85%*, *Amber Tier: 70-84%*, *Red Tier: <70%*).
  - Tab analisa scoring AI, resume kompetensi, dan tautan langsung ke profil interview.
- **Modul Pengaturan AI & WhatsApp (`/ai-settings`)**:
  - Konfigurasi Gemini AI API Key, pemilihan model (Gemini 1.5 Pro / Flash), batas temperatur, dan sistem prompt kustom.
  - Konfigurasi token WhatsApp Gateway untuk notifikasi otomatis pemanggilan kandidat.

---

### 7. 🌐 Portal Publik Lowongan Kerja (Career Landing Page)
- **Portal Lowongan Kerja Mandiri (`/job`)**:
  - Halaman landing page publik di luar dashboard internal.
  - Pencarian lowongan kerja, filter area penempatan, filter tipe pekerjaan (*Full-time, Contract, Internship*).
- **Detail Lowongan & Formulir Apply (`/job/{id}` & `/job/{id}/apply`)**:
  - Deskripsi kualifikasi pekerjaan, tanggung jawab, benefit, dan rentang gaji.
  - Formulir pelamaran kerja publik dengan upload CV/dokumen, data pribadi, dan pendidikan.
- **Form Login Pengguna & Karyawan (`/login`)**:
  - Desain form login modern bernuansa ASystem dengan card panduan login karyawan.

---

### 8. 🛠️ Penyiapan Environment & Validasi Server Lokal (Laravel 12 & PHP 8.2)
- **Instalasi & Konfigurasi PHP 8.2**:
  - Pemasangan PHP 8.2.33 dengan ekstensi pendukung lengkap: `pdo_sqlite`, `pdo_mysql`, `curl`, `mbstring`, `fileinfo`, `openssl`, `intl`, `gd`, dan `zip`.
  - Peningkatan limit memori (`memory_limit = 512M`) pada `php.ini`.
- **Instalasi Composer & Manajemen Dependensi**:
  - Pemasangan Composer v2.10.3 dan instalasi seluruh dependensi backend via `composer install` (memastikan kompatibilitas penuh dengan Laravel 12 dan `laravel/pint` v1.32.1).
- **Konfigurasi Environment & Application Key**:
  - Penyalinan konfigurasi `.env` dari `.env.example` dan pembuatan kunci aplikasi via `php artisan key:generate`.
- **Validasi Konektivitas Database SQLite**:
  - Penghubungan database SQLite `asystem_interview` dengan 22 tabel aktif, data relasi, serta ketersediaan akun uji coba multi-role (Admin, Recruiter, Karyawan Inhouse, dan Karyawan Ratecard).
- **Eksekusi Server Development**:
  - Menjalankan server lokal menggunakan `php artisan serve --host=127.0.0.1 --port=8000`.
  - Pengujian endpoint publik (`/login`, `/job`) menghasilkan status `200 OK`.

---

### 9. 🧠 Portal CBT & Online Test Mandiri Kandidat (`/cbt`)
- **Portal Login Mandiri Kandidat (`/cbt/login`)**:
  - Autentikasi berbasis **NIK** dan **Password default** berupa tanggal lahir berformat `ddmmyyyy` (`dmY`), diekstrak langsung dari kolom tanggal lahir data kandidat.
  - Proteksi penempatan kandidat: kandidat yang sudah memiliki `principle_id` / ditempatkan akan dibatasi aksesnya (*"Akun Non Aktif. Silahkan Hubungi AS Terkait"*) selaras dengan logika legacy `D:\ASystem\interview\index.php`.
  - Dilengkapi toggle show/hide password, alert status dinamis, dan kartu informasi bantuan format NIK/tanggal lahir.
- **Desain Khusus Mobile-Responsive & Sticky Navigation**:
  - Antarmuka berdesain modern, selaras dengan ASystem Portal (Tailwind CSS, clean aesthetic, typography Inter).
  - Tampilan responsif optimal untuk perangkat layar smartphone, tablet, maupun desktop.
  - **Sticky Bottom Navigation Bar** khusus perangkat mobile (`md:hidden fixed bottom-0`) dengan pintasan instan: *Beranda*, *Lengkapi Profil*, dan *Keluar*.
- **Halaman Lengkapi Data Profil Kandidat 6 Tab (`/cbt/profile`)**:
  - **Tab 1: Data Pribadi** (NIK, Nama, Tempat/Tanggal Lahir, Jenis Kelamin, Agama, Alamat KTP, Alamat Domisili, No HP/WA, Pendidikan Terakhir, dsb.).
  - **Tab 2: Data Keluarga** (Status Pernikahan, Nama Pasangan, Pekerjaan Pasangan, Jumlah Anak, Urutan Anak, Nama & Pekerjaan Ayah/Ibu).
  - **Tab 3: Data Keuangan & Fasilitas** (Ekspektasi Gaji, Kepemilikan Kendaraan, SIM A/C).
  - **Tab 4: Keterampilan & Bahasa Tambahan** (Kemampuan Komputer, Keterampilan Lain & Level Kemampuan).
  - **Tab 5: Pengalaman Kerja** (CRUD Riwayat Pekerjaan: Perusahaan, Posisi, Periode Kerja, Gaji Terakhir, Alasan Resign).
  - **Tab 6: Tanda Tangan Canvas Digital & Pernyataan** (HTML5 Digital Signature Canvas interaktif yang mendukung sentuhan jari layar smartphone / mouse desktop, tombol bersihkan tanda tangan, dan persetujuan pernyataan kebenaran data).
- **Pengerjaan 3 Modul Test Online Mandiri (Selaras Metode `D:\ASystem\interview`)**:
  1. **Tes Kepribadian (DISC)** (`/cbt/test/kepribadian`):
     - Mengadaptasi bank soal dan metode 24 butir kartu DISC dari `soalpsikotes.php`.
     - Paginasi terstruktur 10 kartu per halaman, pemilihan aspek *Most (M)* dan *Least (L)*.
     - Dilengkapi running timer, tombol jeda/lanjutkan (*Pause / Resume*), dan auto-save state jawaban di `localStorage`.
     - Halaman evaluasi hasil (`/cbt/test/kepribadian/result`) dengan visualisasi grafik radar / doughnut Chart.js untuk profil D, I, S, C serta deskripsi karakteristik kepribadian.
  2. **Tes Matematika & Logika** (`/cbt/test/matematika`):
     - Mengadaptasi metode ujian matematika dari `soal.php` dan `soaltes.php`.
     - Countdown timer otomatis 10 menit (600 detik) dengan auto-submit saat waktu habis.
     - 10 butir soal acak (kombinasi multiple choice dan isian numerik/desimal).
     - Auto-save jawaban ke browser storage secara berkala.
     - Penilaian otomatis skor 0-100 dan halaman hasil evaluasi (`/cbt/test/matematika/result`) lengkap dengan rincian benar/salah dan pembahasan soal.
  3. **Tes Komputer / Studi Kasus Praktik** (`/cbt/test/komputer`):
     - Mengadaptasi metode pengerjaan dari `soalkomputer.php`.
     - Count-up timer untuk memantau durasi pengerjaan studi kasus.
     - Panduan instruksi soal spreadsheet/komputer dan modal upload berkas bukti pengerjaan (`bukti_file`: format JPG, PNG, PDF).
- **Pencatatan Audit Trail Aktivitas (`candidate_logs`)**:
  - Mencatat riwayat login, kelengkapan profil, pengerjaan psikotes, tes matematika, dan upload tes komputer secara kronologis.
### 9. 🗄️ Migrasi Penuh Database Legacy Interview (`asystemc_interview.sql`) & Evaluasi Dinamis
- **Streaming Batch Importer Berperforma Tinggi (`app/Console/Commands/ImportInterviewSqlDumpCommand.php`)**:
  - Mengembangkan command artisan `php artisan import:interview-sql-dump` berbasis streaming chunked multi-row insert yang mampu memproses file SQL dump berukuran **271 MB** secara efisien tanpa memory exhaustion.
  - Berhasil mengimpor **2.417.828 baris data riil** ke dalam database SQLite `asystem_interview` (ukuran database 416 MB):
    - `tb_kandidat`: **64.100 baris** & sinkron 1-to-1 dengan tabel `candidates`: **64.106 baris** (ID kandidat identik).
    - `tb_hasilpsikotes`: **1.810.172 baris** (1,81 juta jawaban tes DISC 40 butir nyata).
    - `tb_hasilmath`: **529.933 baris** (530 ribu data jawaban tes matematika nyata).
    - `hasilinterview`: **40.897 baris** & `interview_assessments`: **40.106 baris** (catatan wawancara recruiter).
    - `hasil_kompt`: **11.607 baris** (penilaian uji kompetensi 9 kriteria Excel).
    - `tb_pengalaman`: **93.026 baris** & `work_experiences`: **92.854 baris** (riwayat kerja pelamar).
    - `test_results`: **99.628 baris**.
    - `userprinsiple`: **4.272 baris** & `principles`: **335 entitas mitra/klien**.
    - Bank Soal: `tb_kepribadian` (**40 butir bank soal DISC**) dan `tb_math` (**10 butir bank soal matematika**).
- **Skema Migrasi Terindeks (`database/migrations/2026_09_17_140000_create_legacy_interview_tables.php`)**:
  - Membangun 16 tabel relasional legacy pada SQLite dengan indeks performa tinggi pada kolom pencarian krusial (`id_kandidat`, `nomor_ktp`, `email`, `area`, `id_soal`).
- **Layanan Evaluasi Terpusat (`App\Services\CandidateEvaluationDataService`)**:
  - Layanan terintegrasi untuk kalkulasi evaluasi:
    - **Tes DISC**: Memetakan 40 butir jawaban kandidat, merekap sebaran A/B/C/D, mengidentifikasi watak dominan (*Melankolis, Sanguinis, Koleris, Plegmatis*), dan menghasilkan narasi profil psikologis.
    - **Tes Matematika**: Menghitung durasi pengerjaan, nomor tes ke, verifikasi jawaban benar/salah terhadap bank soal, persentase nilai, dan grade (*A / B / C / D*).
    - **Tes Komputer**: Merangkum penilaian 9 indikator kompetensi Excel (*VLOOKUP, HLOOKUP, Pivot, IF, Average, Hitung, Teliti, Cepat, Hasil Kerja*).
- **Integrasi Penuh Seluruh Controller & Tampilan Blade**:
  - `InterviewController` & `interview/show.blade.php`: Menampilkan hasil tes riil dan catatan wawancara kandidat.
  - `KandidatPortalController` & `kandidatportal/show.blade.php`: Selaras dan menampilkan hasil evaluasi terpadu.
  - `InterviewInhouseController` & `interviewinhouse/show.blade.php`: Selaras dan menampilkan hasil evaluasi inhouse.
- **Ekspor Dokumen PDF Multi-Halaman Dinamis (`App\Services\InterviewPdfService`)**:
  - Mengadaptasi seluruh fungsionalitas pencetakan dokumen resmi `printall.php` legacy.
  - Menghasilkan file PDF resmi ukuran Legal mencakup Formulir Interview, Surat Cek Referensi Kerja, Lembar Jawaban 40 Butir DISC lengkap dengan Pie Chart grafik warna-warni, Lembar Koreksi 10 Soal Matematika, Penilaian Komputer Excel, serta Lampiran Bukti Approval User Prinsiple.
  - Auto-create folder temporary mPDF `storage/app/temp-pdf`.

---

### 10. 🎯 Pemisahan 4 Kategori Kandidat & Penambahan Kolom Jenis Kelamin
- **Standarisasi Pemisahan 4 Kategori Data Kandidat**:
  1. **Interview Selesai** (`/interviewdone`): Kandidat yang kolom `ttd_prinsiple`-nya telah terisi tanda tangan/persetujuan prinsiple (`ttd_prinsiple IS NOT NULL AND ttd_prinsiple != ''`) dan bukan berstatus Arsip (**27.038 kandidat**).
  2. **Arsip Interview** (`/interviewarsip`): Kandidat dengan status arsip (`status = 'Arsip'` / `status_kandidat = 'Arsip'`) (**17.343 kandidat**).
  3. **Kandidat Interview** (`/interview`): Kandidat hasil tarikan integrasi Odoo dengan kolom `jenis = ""` atau NULL, belum bertanda tangan prinsiple, dan aktif (**10.687 kandidat**).
  4. **Kandidat Portal** (`/kandidatportal`): Pelamar dari portal lowongan publik dengan kolom `jenis = "Job Portal"` (**3.243 kandidat**).
- **Penambahan Kolom Jenis Kelamin (Gender)**:
  - **Migrasi Skema & Sinkronisasi (`2026_09_17_150000_add_ttd_prinsiple_and_sync_candidate_categories.php`)**:
    - Menambahkan kolom `ttd_prinsiple` dan `time_prinsiple` pada tabel `candidates` dan menyinkronkan data riil dari `tb_kandidat`.
    - Menambahkan kolom `gender` pada tabel `candidates` dan `tb_kandidat`.
    - Auto-populate gender dari 16 digit NIK (Nomor KTP) menggunakan standar nasional kependudukan Indonesia (digit ke 7-8: tanggal > 40 adalah **Perempuan**, <= 31 adalah **Laki-laki**).
  - **Formulir Pendaftaran Lowongan Kerja Publik (`/job/{id}/apply`)**:
    - Menambahkan opsi pilihan radio interaktif *Jenis Kelamin (Laki-laki / Perempuan)* dengan ikon visual modern.
    - Validasi backend dan penyimpanan langsung ke `Candidate::create` dengan `jenis = 'Job Portal'`.
  - **Tampilan Antarmuka (Blade Views)**:
    - Ditambahkan kolom **Jenis Kelamin** dengan pill badge berwarna (Pink untuk Perempuan, Biru untuk Laki-laki) pada tabel [interview/index.blade.php](file:///d:/ASystem/newasystem/resources/views/interview/index.blade.php) (Tabel Kandidat Milik Sendiri & Tabel Rekan Area), [interview/done.blade.php](file:///d:/ASystem/newasystem/resources/views/interview/done.blade.php), [interview/arsip.blade.php](file:///d:/ASystem/newasystem/resources/views/interview/arsip.blade.php), dan [kandidatportal/index.blade.php](file:///d:/ASystem/newasystem/resources/views/kandidatportal/index.blade.php).
    - Ditambahkan informasi Jenis Kelamin pada kartu identitas profil pelamar di [interview/show.blade.php](file:///d:/ASystem/newasystem/resources/views/interview/show.blade.php), [kandidatportal/show.blade.php](file:///d:/ASystem/newasystem/resources/views/kandidatportal/show.blade.php), dan [interviewinhouse/show.blade.php](file:///d:/ASystem/newasystem/resources/views/interviewinhouse/show.blade.php).

---

### 10. ⚡ Optimasi Kecepatan Loading & Efek Animasi UI/UX Modern di Seluruh Halaman
- **Optimasi Database & SQLite Performance Engine**:
  - **Aktivasi SQLite WAL (Write-Ahead Logging)**:
    - Diaktifkan permanen pada `config/database.php` (`journal_mode => 'WAL'`, `synchronous => 'NORMAL'`, `busy_timeout => 5000`).
    - Penambahan SQLite PRAGMAs: `cache_size = 64000` (~64MB in-memory page cache), `mmap_size = 268435456` (256MB memory mapping), dan `temp_store = 'MEMORY'`.
  - **Penambahan Composite Indexes (`database/migrations/2026_09_17_160000_add_performance_indexes_to_candidates_table.php`)**:
    - `idx_cand_portal` pada `candidates(jenis, status, status_kandidat, id DESC)`
    - `idx_cand_done` pada `candidates(ttd_prinsiple, status, id DESC)`
    - `idx_cand_arsip` pada `candidates(status, status_kandidat, id DESC)`
    - `idx_cand_area` pada `candidates(area, jenis, status, id DESC)`
    - `idx_cand_filter_interview` pada `candidates(status, jenis, ttd_prinsiple, id DESC)`
  - **Optimasi Agregasi Query Tunggal (Conditional Aggregation)**:
    - Mengganti 8 query `count()` terpisah di `KandidatPortalController` menjadi 1 query agregasi tunggal (`SUM(CASE WHEN ...)`), mempercepat pemrosesan data statistik dari ~1.000 ms menjadi **~30 ms**.
    - Mengganti 6 query `count()` di `CandidateController` menjadi 1 query agregasi terpadu.
  - **Multi-Worker Concurrency (`PHP_CLI_SERVER_WORKERS=4`)**:
    - Mengaktifkan 4 worker proses di file `.env` sehingga request browser untuk aset/halaman berjalan paralel dan tidak saling memblokir (menghilangkan bottle-neck antrean sekuensial).
  - **Hasil Benchmark Waktu Muat (Load Time Reduction)**:
    - `/kandidatportal`: dari **~1.077 ms** turun drastis ke **~45 - 300 ms** (peningkatan kecepatan hingga **15x lipat**).
    - `/job`: dari **~1.200 ms** turun ke **~239 ms**.
    - `/cbt/login`: dari **~1.000 ms** turun ke **~237 ms**.
    - `/interviewdone`: dari **~1.400 ms** turun ke **~790 ms**.
    - `/interviewarsip`: dari **~1.300 ms** turun ke **~708 ms**.
    - `/interview`: dari **~3.500 ms** turun ke **~886 ms**.
- **Efek Animasi Loading & Transisi Halaman UI/UX Selaras**:
  - **Top Glowing Progress Bar (NProgress Style)**:
    - Progress bar gradien bernuansa Sapphire Blue ESA (`from-blue-700 via-primary-600 via-blue-500 to-sky-400`) dengan bayangan neon glowing dan animasi pulsa ping di ujung kanan bar.
    - Muncul otomatis saat pengguna mengklik link navigasi, link pagination, tombol filter, atau menu sidebar.
  - **Frosted Glass Loading Overlay (Modal Pemrosesan)**:
    - Overlay kaca buram (*glassmorphism*) dengan kartu modern yang muncul saat pengguna mengirimkan form (pencarian, penyimpanan evaluasi interview, apply job, upload berkas).
    - Dilengkapi dual-ring spinner modern dengan logo ESA ASystem dan teks dinamis (*"Memproses Data... Mohon tunggu sebentar"*).
  - **State Loading Tombol Otomatis**:
    - Tombol aksi yang disubmit secara otomatis menampilkan ikon loading berputar (`fa-circle-notch fa-spin`), bertransisi ke mode disabled dan opacity 80% untuk mencegah *double submit*.
  - **Animasi Transisi Halaman (`asystem-page-enter`)**:
    - Efek entrance fade-in halus (`0.28s cubic-bezier`) pada kontainer halaman utama di semua layout (`layouts.app`, `layouts.public`, `layouts.cbt`), menghilangkan kedipan kasar saat berpindah halaman.
  - **Partial Terpusat (`resources/views/partials/page-loader.blade.php`)**:
    - Terintegrasi penuh ke semua layout dengan fallback BFCache (`pageshow`) agar tampilan tetap responsif dan bersih saat menggunakan tombol Back/Forward browser.

---

### 14. 🏢 Pembersihan Master Data Prinsiple & Logo Entitas Dokumen Interview (18 September 2026)
- **Pembersihan Data Dummy / Sampah**:
  - Menghapus seluruh data dummy (seperti karakter `-`, nomor NPWP `01.865...`, dll) dari tabel `principles`.
- **Import 157 Data Master Prinsiple Resmi 5 Entitas Inhouse**:
  - Mengimpor data resmi dari `Master Prinsiple.rar` yang terbagi ke dalam 5 entitas inhouse:
    - **AMK** (`PT ARINA MULTI KARYA`): 59 Prinsiple (`PRN-AMK-001` s/d `PRN-AMK-059`)
    - **AKP** (`PT ALVA KARYA PERKASA`): 50 Prinsiple (`PRN-AKP-001` s/d `PRN-AKP-050`)
    - **ATK** (`PT ANUGRAH TERPERCAYA KERJA`): 33 Prinsiple (`PRN-ATK-001` s/d `PRN-ATK-033`)
    - **ABO** (`PT ABADI BERKAT ODELIA`): 10 Prinsiple (`PRN-ABO-001` s/d `PRN-ABO-010`)
    - **ATB** (`PT ANUGRAH TALENTA BERKARYA`): 5 Prinsiple (`PRN-ATB-001` s/d `PRN-ATB-005`)
  - Menautkan ulang lebih dari 54.800 kandidat ke master prinsiple resmi berdasarkan nama prinsiple dan entitas afiliasinya.
- **Tampilan Master Data Prinsiple Modern**:
  - Filter interaktif berdasarkan entitas inhouse (Semua, AMK, AKP, ATK, ABO, ATB) serta quick-filter badge dengan jumlah masing-masing.
  - Penambahan kolom `ENTITAS & INHOUSE` dengan badge warna tematik per entitas (`blue`, `amber`, `emerald`, `purple`, `rose`).
  - Modal Tambah & Edit Prinsiple lengkap dengan seleksi 5 entitas inhouse dan auto-sync.
- **Kop Logo Dokumen Hasil Interview (`InterviewPdfService`)**:
  - Kop logo di bagian atas dokumen interview disesuaikan otomatis dengan entitas prinsiple kandidat:
    - AMK &rarr; `kopamknew.png`
    - AKP &rarr; `kopakp.png`
    - ATK &rarr; `kopatk.png`
    - ABO &rarr; `kopabo.png`
    - ATB &rarr; `kopatb.png`
  - Gambar logo di-encode menjadi format Base64 Data URI untuk menjamin ketajaman dan stabilitas tampilan baik pada rendering native mPDF maupun mode cetak HTML.

---

### 16. 📂 Pemulihan Visibilitas Data Interview Selesai & Arsip Interview
- **Akar Masalah**:
  - Pada metode `done()` dan `arsip()` di `InterviewController.php`, terdapat kondisi filter pembatas yang secara keliru mengecek hak akses Admin dan membatasi query hanya pada email personal akun Admin (`admin@asystem.co.id`), sehingga seluruh data historis (puluhan ribu kandidat) tidak muncul di halaman web.
- **Solusi & Perbaikan**:
  - **Kandidat Done (`/interview/done`)**:
    - Membuka query agar secara default menampilkan seluruh kandidat aktif nasional (**27.044 kandidat**) yang memiliki catatan/persetujuan prinsiple (`note_principle != ''` atau `ttd_prinsiple != ''`).
    - Menambahkan rekapitulasi daftar rekruter (`$allRecruiters`) dan dropdown filter rekruter interaktif pada halaman `interview/done.blade.php`.
    - Menampilkan badge total kandidat selesai (`27.044 Kandidat Selesai`) serta mempertahankan parameter pencarian & filter pada tautan paginasi.
  - **Arsip Kandidat (`/interview/arsip`)**:
    - Membuka query agar secara default menampilkan seluruh kandidat berstatus arsip nasional (**17.505 kandidat**).
    - Menambahkan dropdown filter rekruter dan badge total terarsip pada `interview/arsip.blade.php`.
    - Mempertahankan parameter filter pada tautan paginasi.

---

### 17. 💻 Modernisasi Sinkronisasi Odoo dengan Live Streaming Terminal Console
- **Latar Belakang & Masalah Lama**:
  - Sinkronisasi sebelumnya menggunakan request HTTP POST sinkron biasa. Ketika jumlah data karyawan di Odoo mencapai ribuan, koneksi HTTP mengalami *gateway timeout* (504 / 524) atau *page freeze*, dan pengguna tidak memiliki visibilitas mengenai progres data yang sedang diproses.
- **Solusi Streaming Terminal UI**:
  - **Antarmuka Konsol Terminal macOS/Linux Modern**:
    - Menambahkan modal konsol terminal bergaya *dark mode* monospace (`#0d1117`) lengkap dengan traffic lights window control (🔴 🟡 🟢), judul CLI interaktif, dan badge berdenyut `[LIVE STREAM]`.
    - Bar metrik real-time: Target Entitas, Total Diproses, Baru Dibuat (`+X` hijau), Diperbarui (`~Y` cyan), Dilewati (`Z` abu-abu), dan Error (`!E` merah).
    - Progress bar gradient animasi dan terminal output line-by-line berwarna dengan timestamp dan tag aksi.
    - Kontrol interaktif: Tombol toggle *Auto-Scroll*, *Salin Seluruh Log*, *Bersihkan Layar*, *Hentikan (Abort)*, dan *Buka Data Karyawan*.
  - **Protokol Server-Sent Events (SSE) & Anti-Timeout**:
    - Menonaktifkan kompresi gzip dan output buffer PHP (`ob_end_flush`, `implicit_flush(1)`), serta menyetel batas waktu eksekusi tanpa batas (`set_time_limit(0)`).
    - Mengalirkan setiap record karyawan yang diproses secara real-time via `text/event-stream` ke browser tanpa putus, menjaga koneksi socket tetap hidup terus-menerus dan anti-timeout.
    - Mendukung streaming per entitas tunggal (`/odoo-setting/{code}/stream-sync`), streaming massal seluruh 5 entitas aktif (`/odoo-setting/stream-sync-all`), dan streaming pencarian NIK (`/odoo-setting/stream-sync-nik`).

---

### 18. 🛠️ Perbaikan Lampiran Dokumen Hasil Interview & Penyesuaian Modal Sync Odoo
- **Koreksi Tautan Server Lama Approval Prinsiple**:
  - Memperbaiki rute `/approval/{filename}` dan fallback legacy di `routes/web.php` serta `app/Services/LegacyAttachmentService.php` agar mengarah ke direktori yang benar: `https://asystem.co.id/v3/approval/{filename}` (dan untuk berkas tanda tangan digital digital: `https://asystem.co.id/v3/prinsiple/ttdfileprinsiple/{filename}`).
- **Penjaminan Tampilnya Lampiran Ref Cek & Approval Prinsiple di Dokumen PDF/HTML**:
  - Pada `app/Services/InterviewPdfService.php`:
    - Mengubah kondisi pembuatan halaman: Sebelumnya halaman lampiran hanya dibuat jika Base64 data berhasil ter-generate secara lokal (`if ($approvalBase64)` & `if ($refCekBase64)`). Jika berkas belum terunduh, halaman terlewati.
    - Logika baru: Selama kandidat memiliki berkas approval (`approvalFilename`), halaman Bukti Approval Prinsiple **pasti dibuat**.
    - Cascading fallback image source: Base64 URI &rarr; Jalur Berkas Lokal &rarr; URL Server Lama (`https://asystem.co.id/v3/approval/{filename}`) dengan atribut `onerror` cadangan otomatis di browser.
    - Menangani seluruh lampiran referensi cek kerja (`workExperiences`) kandidat yang memiliki berkas lampiran (`proof_attachment_path`), sehingga baik 1 maupun lebih lampiran (seperti sertifikat/surat referensi) ter-render lengkap di halaman dokumen.
- **Perbaikan Animasi Loading yang Menutupi Layar saat Sync Modal Odoo**:
  - **Akar Masalah**:
    - Skrip global pada `page-loader.blade.php` mencegat semua event form `submit` dan menampilkan `#asystem-loading-overlay` ber-z-index `z-[999990]`.
    - Modal Terminal Streaming Odoo sebelumnya menggunakan `z-50`, sehingga tertutup oleh animasi loading layar penuh saat tombol sync diklik.
  - **Solusi**:
    - Menambahkan filter pengecualian form pada `page-loader.blade.php` agar form sync (`formSyncAll`, `formSingleSync_*`, `formSyncByNik`, `formKaryawanSyncNik`) dan modal terminal tidak memicu loading overlay.
    - Pada fungsi `showOverlay()`, menambahkan pengecekan otomatis: Jika `#terminalSyncModal` sedang terbuka/aktif, overlay layar penuh otomatis dibatalkan/tidak dimunculkan.
    - Menaikkan z-index `#terminalSyncModal` ke `z-[999995]` dan modal sync massal ke `z-[999991]`.
- **Penanganan Permanen Berkas Konfigurasi Orphan / Sisa di Server**:
  - Berkas sisa dari instalasi Laravel standar seperti `config/octane.php` dan `config/sanctum.php` yang tidak ada di `composer.json` menyebabkan error fatal saat artisan memuat konfigurasi (`LoadConfiguration`).
  - Menambahkan mekanisme *auto self-cleaning* di [bootstrap/app.php](file:///d:/ASystem/newasystem/bootstrap/app.php) dan [public/index.php](file:///d:/ASystem/newasystem/public/index.php) untuk secara otomatis mendeteksi dan menghapus berkas orphan (`octane.php`, `sanctum.php`, `telescope.php`, `horizon.php`, `pennant.php`, `reverb.php`) jika class dependensinya tidak terpasang.
  - Menambahkan [config/sanctum.php](file:///d:/ASystem/newasystem/config/sanctum.php) dan [config/octane.php](file:///d:/ASystem/newasystem/config/octane.php) yang berstatus *safe-guard* langsung ke dalam repositori Git agar setiap kali server di-update, berkas tersebut selalu aman dan tidak pernah memicu error.

---

### 19. ✍️ Digital Signature AS, Auto-Preload Tanda Tangan, & Dynamic PDF Export
- **Tanda Tangan Digital Otomatis AS (Cukup Buat Sekali)**:
  - Menambahkan kolom `signature_path` pada tabel `users` ([database/migrations/2026_09_18_170000_add_signature_path_to_users_table.php](file:///d:/ASystem/newasystem/database/migrations/2026_09_18_170000_add_signature_path_to_users_table.php)).
  - Menyimpan tanda tangan permanen AS ke `signatures/user_{id}.png` dan `users.signature_path` saat submit form interview.
  - Pada pembukaan form detail kandidat berikutnya, tanda tangan AS yang telah tersimpan otomatis dimuat ke canvas tanpa perlu digambar ulang, dilengkapi badge indikator *TTD AS Otomatis (Tersimpan)* dan tombol *Hapus / Ulangi*.
- **Cetak Dokumen Hasil Interview Dinamis (`InterviewPdfService`)**:
  - Kolom `MENYETUJUI HRD / AS` diubah menjadi dinamis sesuai AS penilai ([InterviewController::resolveCandidateAsDetails()](file:///d:/ASystem/newasystem/app/Http/Controllers/InterviewController.php)), menggantikan teks statis "Budi Santoso".
  - Gambar tanda tangan AS di-render secara proporsional menggunakan Base64 ke dalam dokumen PDF.
  - Penyesuaian nama petugas dan area pada Form Cek Referensi (Halaman 2).
  - Normalisasi nilai skor 1–5 menjadi label teks baku (*Sangat Baik, Baik, Cukup, Kurang*) dan penampilan catatan khusus wawancara dari penilai.
- **Skrip Deployment Otomatis Satu Perintah ([deploy.sh](file:///d:/ASystem/newasystem/deploy.sh))**:
  - Dibuat skrip `deploy.sh` dan diperbarui `public/deploy.php` yang secara otomatis membersihkan berkas migrasi sisa lama (`2026_07_*`), menarik branch `main` terbaru, mengeksekusi migrasi skema database, menata hak akses `www:www`, serta membersihkan seluruh cache.

---

### 20. 🎯 Sinkronisasi Evaluasi Nilai CBT & Status Hasil Tes Kandidat di Dashboard Rekruter
- **Identifikasi Masalah**:
  - Kandidat yang telah menyelesaikan psikotes DISC dan ujian matematika CBT masih terindikasi silang merah (*belum menyelesaikan tes*) di dashboard rekruter (`/interview` dan `/kandidatportal`).
- **Solusi & Sinkronisasi Real-Time**:
  - Memperbarui `CandidateEvaluationDataService` agar mampu mendeteksi hasil tes baik dari tabel modern (`test_results`) maupun tabel warisan (`tb_hasilpsikotes` dan `tb_hasilmath`) secara cascading.
  - Memastikan pencatatan rekap hasil tes ke kolom `tes_kepribadian` dan `tes_matematika` pada data kandidat saat ujian CBT disubmit.
  - Menyelaraskan kartu status kelulusan dan rincian skor di seluruh view detail kandidat ([interview/show.blade.php](file:///d:/ASystem/newasystem/resources/views/interview/show.blade.php), [kandidatportal/show.blade.php](file:///d:/ASystem/newasystem/resources/views/kandidatportal/show.blade.php), dan [interviewinhouse/show.blade.php](file:///d:/ASystem/newasystem/resources/views/interviewinhouse/show.blade.php)).

---

### 21. 🛑 Validasi Kriteria Kelulusan Interview, Disable Tab User Prinsiple & Tombol Download Dokumen
- **Kriteria Pembatasan Kelulusan**:
  1. **Nilai Tes Matematika**: Minimal kelulusan adalah grade **B** (skor >= 60). Kandidat dengan nilai **C** (skor 40-59) atau **D** (< 40) dinyatakan **Gugur / Tidak Memenuhi Syarat**.
  2. **Hasil Psikotes DISC untuk Posisi Penjualan / Sales**: Untuk seluruh jabatan yang berkaitan dengan sales (seperti *SPG, SPB, BA, BC, Beauty Advisor, Brand Ambassador, Direct Consultant, Salesman, Merchandiser, SMD, MD, Sales Executive, dll.*), kandidat **tidak boleh** memiliki kepribadian dominan **Melankolis** atau **Plegmatis**.
- **Penerapan Disable Tab User Prinsiple**:
  - Tab 7 **User Prinsiple** pada halaman detail kandidat otomatis dalam kondisi **Disabled** jika salah satu kriteria di atas tidak terpenuhi.
  - Menampilkan kartu peringatan visual bertinta merah/rose dengan teks rekomendasi:  
    > *"Catatan: Disarankan Mencari Kandidat Baru / Yang Lain."*
- **Penerapan Disable Tombol Download Document & Proteksi Backend**:
  - Tombol **Download All Document** di halaman detail kandidat otomatis berubah menjadi **Disabled** dengan indikator gembok / tooltip keterangan penyebab penolakan.
  - Endpoint `downloadPdf` di backend controller (`InterviewController::downloadPdf`) memproteksi pengunduhan file PDF: jika kandidat tidak memenuhi kriteria, proses unduh dibatalkan dan dialihkan kembali dengan pesan penolakan yang informatif.

---

### 22. ✍️ Isolasi & Personalisasi Tanda Tangan Pewawancara (AS)
- **Akar Masalah**:
  - Sebelumnya, file tanda tangan salah satu user AS termuat secara global di kanvas semua kandidat, sehingga tanda tangan tampak identik/nyangkut antar user.
- **Penyempurnaan Alur Tanda Tangan Mandiri**:
  - **Tampilan Default Kanvas**: Jika kandidat belum dinilai dan AS kandidat belum memiliki tanda tangan tersimpan, kanvas secara default **tampil bersih/kosong (blank)** dengan badge status `Belum Ada TTD`.
  - **Prioritas Tanda Tangan AS Kandidat**: Jika AS yang menangani kandidat tersebut sudah memiliki tanda tangan miliknya sendiri, tanda tangan tersebut langsung dimuat otomatis.
  - **Tombol "Tempel TTD Saya"**: Tersedia tombol khusus bagi user yang sedang login untuk menempelkan tanda tangannya ke kanvas dengan 1 klik.
  - **Opsi "Gambar TTD Baru" & Reset**: User dapat menggambar tanda tangan baru di kanvas yang akan otomatis mereplace file tanda tangan miliknya sendiri (`signatures/user_{id}.png`) tanpa menumpuk banyak file sampah di storage server.
  - **Pembersihan File Uji Coba**: Seluruh file tanda tangan dummy uji coba di server production telah dibersihkan sehingga sistem berada dalam kondisi fresh dan siap operasional.

---

### 23. 👥 Pemulihan Master User Prinsiple, Isolasi Data Per Pengguna, & Proteksi Anti-Duplikat Email/No HP
- **Pengembalian Menu Master User Prinsiple**:
  - Membuka pembungkus middleware dan blade directive agar menu **Master User Prinsiple** kembali dapat diakses oleh user AS dan rekruter non-admin di sidebar navigasi.
- **Isolasi Data Per Pengguna (Per User Data Scoping)**:
  - **Migrasi Skema Database**: Menambahkan kolom `created_by` pada tabel `user_prinsiples` dan tabel warisan `userprinsiple` ([database/migrations/2026_09_18_220000_add_created_by_to_user_prinsiples_table.php](file:///d:/ASystem/newasystem/database/migrations/2026_09_18_220000_add_created_by_to_user_prinsiples_table.php)) serta melakukan backfilling relasi kandidat otomatis.
  - **Tampilan Data Terisolasi**: User AS non-admin **hanya melihat data Master User Prinsiple yang ditambahkan oleh dirinya sendiri** (`created_by = Auth::id()`). Data master user prinsiple antar-cabang atau antar-rekruter tidak akan tercampur aduk.
  - **Metrik & Filter**: Ringkasan total user, user aktif, dan cakupan area otomatis terisolasi sesuai data milik user yang sedang aktif.
  - **Mode Administrator**: Admin tetap dapat melihat seluruh data secara menyeluruh (*all*) maupun menyaring berdasarkan pembuat data (*creator*).
- **Proteksi Ketat Bebas Duplikat Email & No. HP / WhatsApp**:
  - **Email**: Divalidasi secara ketat (*case-insensitive*). Jika email sudah terdaftar di sistem, sistem langsung menolak dan menampilkan informasi pemilik email tersebut.
  - **Nomor HP / WhatsApp**: Dinormalisasi secara kanonikal ke format standar `08xxx`. Variasi input internasional `+628xxx`, `628xxx`, spasi, tanda hubung, dan tanda kurung dideteksi secara akurat. Jika nomor sudah digunakan, sistem langsung menolak proses simpan.
  - **User Experience**: Penanda `Bebas Duplikat` ditampilkan pada modal, dan form modal otomatis terbuka kembali saat terjadi kegagalan validasi tanpa menghapus data yang sudah diketik pengguna.
  - **Sinkronisasi Ganda (Double-Sync)**: Setiap penambahan, pembaruan, dan penghapusan data secara otomatis disinkronkan ke tabel warisan `userprinsiple` guna menjamin konsistensi query legacy.

---

### 24. 🔄 Rekonfigurasi Sinkronisasi Odoo ERP & Arsitektur Dual Background Cron Job
- **Penyempurnaan Metode Sinkronisasi Odoo**:
  - **Hanya Ambil Karyawan Aktif**: Query XML-RPC Odoo difilter ketat `['active', '=', true]` dan `['departure_date', '=', false]`, memastikan data karyawan yang telah resign / non-aktif tidak ikut tersedot ke database lokal saat sinkronisasi rutin.
- **Dual Background Cron Job Architecture**:
  1. **Hourly Cron Job — Sinkronisasi Karyawan Baru (`odoo:sync-active`)**:
     - **Aturan Proteksi Data**: NIK yang sudah masuk di database lokal **JANGAN DIUPDATE** (dilewati / skip secara instan).
     - **Tujuan**: Hanya meng-insert record baru untuk karyawan yang baru terdaftar di Odoo tanpa menimpa data karyawan lama yang telah ada di sistem lokal.
     - **Jadwal**: Berjalan otomatis **setiap 1 jam** (`hourly`), di latar belakang (`runInBackground()`), dan anti-tumpang-tindih (`withoutOverlapping()`).
     - **Command**: `php artisan odoo:sync-active --silent`
     - **Skrip Runner**: `scripts/cron_odoo_active_hourly.sh` (Linux) dan `scripts/cron_odoo_active_hourly.bat` (Windows).
  2. **Midnight Cron Job — Pengecekan Update Data & Resign (`odoo:sync-updates-resigns`)**:
     - **Aturan Pemeriksaan**: Dijalankan secara terpisah setiap tengah malam pada pukul 00:00 (`dailyAt('00:00')`).
     - **Deteksi Karyawan Resign**: Mengambil seluruh karyawan berstatus `'Aktiv'` di database lokal, lalu memeriksa status riil di Odoo dalam batch chunk 200 data dengan flag `active_test => false`. Jika di Odoo karyawan berstatus `active = false` atau memiliki tanggal keluar (`departure_date`), status di database lokal otomatis diubah menjadi `'Resign'`.
     - **Sinkronisasi Perubahan Data**: Jika karyawan masih aktif, field atribut kerja (jabatan, divisi, area, principle, tipe karyawan, kontak) disinkronkan dengan data terbaru di Odoo.
     - **Command**: `php artisan odoo:sync-updates-resigns --silent`
     - **Skrip Runner**: `scripts/cron_odoo_updates_resigns_midnight.sh` (Linux) dan `scripts/cron_odoo_updates_resigns_midnight.bat` (Windows).
- **Konfigurasi Crontab Server Produksi (`crontab -e`)**:
  ```bash
  # 1. Sinkronisasi Karyawan Aktif Baru (Tiap Jam, Skip NIK yang sudah ada)
  0 * * * * /bin/bash /var/www/newasystem/scripts/cron_odoo_active_hourly.sh >> /var/www/newasystem/storage/logs/cron_odoo_active.log 2>&1

  # 2. Pengecekan Update Data & Status Resign (Tengah Malam 00:00)
  0 0 * * * /bin/bash /var/www/newasystem/scripts/cron_odoo_updates_resigns_midnight.sh >> /var/www/newasystem/storage/logs/cron_odoo_midnight.log 2>&1

  # Atau menggunakan Laravel Schedule Runner terpusat (otomatis menjalankan kedua job di atas):
  * * * * * cd /var/www/newasystem && php artisan schedule:run >> /dev/null 2>&1
  ```

---

### 25. 🔍 Filter Searchable Dropdown Prinsiple/Jabatan, Eksklusi PT BUDGET, & Deduplikasi Master Prinsiple (19 September 2026)
- **Fitur Searchable Dropdown di Filter Bar Master Karyawan**:
  - Mengubah dropdown standar HTML `<select>` untuk **Prinsiple** dan **Jabatan** di [master/karyawan/index.blade.php](file:///d:/ASystem/newasystem/resources/views/master/karyawan/index.blade.php) menjadi komponen interaktif **Searchable Dropdown (Alpine.js)**.
  - Dilengkapi kolom input pencarian langsung di dalam dropdown menu popup, tombol clear `(x)` sekali klik, indikator centang untuk opsi aktif, dan navigasi cepat dengan keyboard.
- **Eksklusi Penuh Karyawan Prinsiple PT BUDGET (AMK, AKP, ATK)**:
  - **Pencegahan Sinkronisasi Odoo**: Di [OdooSyncService.php](file:///d:/ASystem/newasystem/app/Services/OdooSyncService.php) (`syncEmployees`, `syncUpdatesAndResigns`, dan `createOrUpdateFromOdoo`), seluruh data karyawan dengan nama prinsiple mengandung kata `'BUDGET'` (seperti `PT BUDGET AMK`, `PT BUDGET AKP`, `PT BUDGET ATK`) otomatis diabaikan (*skipped*) dan tidak akan di-insert/update ke database lokal.
  - **Pembersihan Database**: Menghapus 269 data karyawan dengan prinsiple BUDGET yang sempat masuk dari Odoo, serta menghapus seluruh entri prinsiple BUDGET dari tabel `principles`.
- **Deduplikasi List Prinsiple (Distinct & Bebas Ganda)**:
  - Mengonsolidasikan 28 nama prinsiple yang sebelumnya muncul ganda di tabel `principles` karena terdaftar di entitas berbeda (seperti *PT AMERTA INDAH OTSUKA, PT BLACKHAWK NETWORK INDONESIA, PT LION WINGS*, dll).
  - Mengarahkan seluruh foreign key relasi `principle_id` pada tabel `employees`, `candidates`, dan `user_prinsiples` ke ID kanonikal tunggal.
  - Menghasilkan daftar list prinsiple yang 100% unik (`distinct()`), rapi, dan terurut secara alfabetis tanpa ada duplikasi lagi.

---

### 26. 📍 Searchable Dropdown Filter Area & Default Pengurutan Join Date Terbaru (19 September 2026)
- **Searchable Dropdown Filter Area**:
  - Mengubah dropdown filter **Area** di [master/karyawan/index.blade.php](file:///d:/ASystem/newasystem/resources/views/master/karyawan/index.blade.php) menjadi komponen interaktif **Searchable Dropdown (Alpine.js)**, melengkapi dropdown Prinsiple dan Jabatan yang telah diupgrade sebelumnya.
  - Pengguna dapat mengetikkan nama area/kota penempatan (seperti *Jakarta, Makassar, Pasuruan, Surabaya, Bandung, dll.*) secara langsung di dalam kotak input pencarian popup untuk menemukan area secara instan.
- **Default Pengurutan Data Karyawan Berdasarkan Join Date Terbaru**:
  - Mengubah parameter pengurutan bawaan (*default sort*) pada [EmployeeController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/EmployeeController.php) dari yang sebelumnya berdasarkan ID menjadi berdasarkan tanggal bergabung terbaru (**`tanggal_join DESC`**).
  - Menggunakan query SQL teroptimasi: `CASE WHEN tanggal_join IS NOT NULL AND tanggal_join != '' THEN 0 ELSE 1 END, tanggal_join DESC, id DESC` sehingga karyawan yang baru bergabung atau masa kontrak paling baru diposisikan di urutan paling atas baris tabel.

---

### 27. 🏢 Pengetatan Aturan Tipe Karyawan Inhouse (Hanya 5 Entitas Resmi) & Proteksi Akses Login
- **Akar Masalah**:
  - Sebelumnya, nama prinsiple klien dari Odoo yang memiliki embel-embel kode entitas dalam tanda kurung (seperti `PT SANGHIANG PERKASA (AMK)`) keliru terdeteksi sebagai entitas AMK sehingga salah diklasifikasikan sebagai `Inhouse` dan otomatis diberikan akses login.
- **Standarisasi 5 Entitas Inhouse**:
  - Dipertegas pada [Employee.php](file:///d:/ASystem/newasystem/app/Models/Employee.php) (`getEntityCodeFromPrinciple` & `isInhousePrinciple`) bahwa status **Inhouse HANYA BERLAKU jika prinsiple adalah 5 entitas resmi perusahaan**:
    1. `PT ARINA MULTI KARYA` (AMK)
    2. `PT ALVA KARYA PERKASA` (AKP)
    3. `PT ANUGRAH TERPERCAYA KERJA` (ATK)
    4. `PT ABADI BERKAT ODELIA` (ABO)
    5. `PT ANUGRAH TALENTA BERKARYA` (ATB)
  - Seluruh prinsiple klien luar lainnya (termasuk *PT Sanghiang Perkasa, PT Sanghiang Perkasa (AMK), PT Kalbe Farma, dll.*) **100% diklasifikasikan sebagai RateCard**.
  - Karyawan RateCard secara default **TIDAK MEMILIKI AKSES LOGIN** (`akses_login = 0` / terkunci) dan tidak muncul saat filter Inhouse aktif.
- **Pembaruan Data Database**:
  - Menyelaraskan seluruh data karyawan di database: karyawan dengan prinsiple klien luar (seperti Sanghiang Perkasa) otomatis diubah menjadi `RateCard` dan akses login dinonaktifkan.

---

### 28. 👔 Penambahan Field Pimpinan di Form Edit Karyawan & Fitur Bulk Edit Pimpinan Massal (19 September 2026)
- **Field Pimpinan Langsung & Jabatan Pimpinan pada Modal Edit**:
  - Menambahkan input teks **Pimpinan Langsung** (dilengkapi auto-suggest `datalist` dari daftar pimpinan/atasan yang sudah ada di database) dan **Jabatan Pimpinan** (contoh: *Supervisor / SPV, Area Manager, Koordinator, Team Leader*) pada modal edit karyawan ([index.blade.php](file:///d:/ASystem/newasystem/resources/views/master/karyawan/index.blade.php)).
  - Memperbarui fungsi JavaScript `editEmployee(emp)` untuk secara otomatis memuat data `emp.pimpinan` dan `emp.jabatan_pimpinan`.
  - Memperbarui validasi dan penyimpanan pada [EmployeeController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/EmployeeController.php) (`update` dan `store`).
  - Memperbarui modal detail karyawan dan tabel Master Karyawan untuk menampilkan nama pimpinan beserta jabatannya.
- **Fitur Bulk Edit Pimpinan Massal**:
  - **Multi-Select Checkbox pada Tabel**: Menambahkan kotak centang (*checkbox*) di setiap baris data tabel serta checkbox *"Pilih Semua"* di header tabel.
  - **Floating Action Bar**: Bilah aksi mengambang otomatis muncul di bagian bawah layar saat 1 atau lebih karyawan dicentang, menampilkan jumlah karyawan terpilih dengan tombol cepat *"Isi Pimpinan (Bulk)"*.
  - **Tombol Toolbar Header**: Disediakan tombol *"Bulk Edit Pimpinan"* pada baris tombol atas halaman Master Karyawan.
  - **Modal Interaktif Bulk Edit Pimpinan**:
    - **Mode 1 (Karyawan Dicentang)**: Menerapkan pimpinan hanya kepada karyawan-karyawan yang dicentang secara spesifik.
    - **Mode 2 (Berdasarkan Filter Data)**: Memungkinkan penetapan pimpinan massal berdasarkan kriteria tertentu (*Filter Prinsiple, Filter Area Penempatan, Filter Entitas, dan Filter Status*), sangat memudahkan penugasan atasan untuk ratusan karyawan sekaligus tanpa perlu mencentang satu per satu per halaman.
    - **Proteksi Penimpaan Data**: Dilengkapi checkbox *"Hanya isi karyawan yang pimpinannya masih KOSONG"* (aktif secara default) agar pimpinan yang telah terisi sebelumnya tidak tertimpa tanpa sengaja.
  - **Backend Controller & Endpoint**:
    - Route `POST /master/karyawan/bulk-pimpinan` (`EmployeeController@bulkUpdatePimpinan`) yang memproses pembaruan massal secara cepat dan aman dengan respon JSON/SweetAlert2.

---

### 29. 🔍 Penyempurnaan Fitur Pencarian Karyawan (Case-Insensitive & Partial Name Matching) (19 September 2026)
- **Akar Masalah Pencarian Sebelumnya**:
  - Input pencarian umum sebelumnya juga mencari ke kolom `prinsiple`, `jabatan`, dan `area` secara bersamaan (`orWhere('prinsiple', 'like', "%{$search}%")`).
  - Akibatnya, ketika pengguna mengetik kata nama seperti *"arya"*, seluruh karyawan dengan prinsiple `PT ARINA MULTI KARYA` dan `PT ALVA KARYA PERKASA` ikut tampil (karena nama perusahaan mengandung suku kata *"KARYA"*), sehingga ratusan karyawan yang namanya sama sekali tidak mengandung *"arya"* keliru muncul di hasil pencarian.
- **Penyempurnaan Logika Pencarian ([EmployeeController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/EmployeeController.php))**:
  - Mengkhususkan kotak pencarian utama untuk menyaring entitas identitas karyawan: **Nama Karyawan**, **NIK (KTP)**, dan **NIP**. (Untuk filter Prinsiple, Jabatan, dan Area tetap menggunakan dropdown filter khusus masing-masing).
  - **100% Case-Insensitive**: Menggunakan perbandingan `LOWER(nama_karyawan) LIKE ?` dengan `strtolower($search)` sehingga pencarian tidak terpengaruh huruf besar/kecil (*contoh: "arya", "Arya", "ARYA" menghasilkan data yang identik*).
  - **Partial Name Matching (Tidak Perlu Nama Lengkap)**: Pengguna cukup mengetik potongan nama depan, nama tengah, atau nama belakang (seperti *"arya"*, *"dimas"*, *"putri"*).
  - **Dukungan Multi-Kata / Multi-Keywords**: Jika pengguna mengetik lebih dari 1 kata (misalnya *"arya maulana"* atau *"dimas saputra"*), sistem secara cerdas mencocokkan record yang memuat seluruh kata tersebut meskipun posisi katanya terpisah oleh nama tengah.

---

### 30. 👔 Pimpinan Searchable Dropdown Grouped by Area & Auto-Fill Jabatan Pimpinan (19 September 2026)
- **Searchable Dropdown Ber-Group Area**:
  - Field Pimpinan pada formulir Master Karyawan telah dirombak dari input teks biasa menjadi **Searchable Dropdown Interaktif** yang ditenagai oleh Alpine.js.
  - Menampilkan seluruh data **Karyawan Inhouse Aktif** yang telah dikelompokkan (*grouping*) secara rapi berdasarkan **Area Penempatan** (41 area kerja).
  - Setiap grup area dilengkapi header visual (*sticky*) dengan ikon lokasi dan counter jumlah karyawan inhouse di area tersebut.
- **Pencarian Cepat & Fleksibel**:
  - Pengguna dapat mengetikkan kata kunci pencarian pada kotak search dropdown untuk menyaring data berdasarkan **Nama Pimpinan**, **Jabatan**, maupun **Area**.
  - **Dukungan Teks Bebas / Kustom**: Apabila nama atasan yang diinginkan belum terdaftar dalam sistem inhouse, disediakan opsi khusus di bagian teratas: *"Gunakan: '[teks]' (Teks Bebas)"*, sehingga fleksibilitas input tetap 100% terjaga.
- **Auto-Fill Jabatan Pimpinan**:
  - Saat pengguna memilih salah satu nama pimpinan dari dropdown, kolom input **Jabatan Pimpinan** akan **terisi otomatis** (*auto-filled*) sesuai dengan `jabatan` dari karyawan inhouse yang dipilih.
  - Dilengkapi efek animasi visual *emerald pulse highlight* pada input jabatan pimpinan sebagai indikator interaktif bahwa nilai jabatan telah berhasil dimuat secara instan. Nilai ini tetap dapat diedit secara manual jika diperlukan penyesuaian.
- **Penerapan Terintegrasi dan Seragam**:
  - **Modal Edit Karyawan (`#editEmployeeModal`)**: Otomatis memuat dan menandai pimpinan yang sedang menjabat saat modal edit dibuka via event reactive.
  - **Modal Tambah Karyawan Baru (`#addEmployeeModal`)**: Menggunakan komponen yang sama dengan inisialisasi form yang bersih.
  - **Modal Bulk Edit Pimpinan (`#bulkPimpinanModal`)**: Memungkinkan penugasan pimpinan massal ber-group area dan auto-fill jabatan untuk banyak karyawan sekaligus.

---

### 31. 🔎 Multi-Search Karyawan (Pencarian Multiple Nama / NIK Sekaligus) (19 September 2026)
- **Komponen Input Tag/Pills Interaktif (Alpine.js)**:
  - Input `Cari Karyawan` dirombak menjadi antarmuka pencarian jamak (*multi-search tags input*).
  - Setiap nama/keyword yang diketik otomatis dikonversi menjadi tag pill interaktif (chip) dengan tombol hapus individual (`×`).
  - Mendukung penambahan tag melalui penekanan tombol **Enter**, tombol **Koma (,)**, atau **Titik Koma (;)**.
  - **Dukungan Paste Massal**: Pengguna dapat menyalin (*copy-paste*) daftar banyak nama atau NIK sekaligus (misal dari dokumen Excel, spreadsheet, atau teks WhatsApp) dan sistem secara otomatis memecah data tersebut menjadi tag-tag terpisah.
  - Tombol cepat **Reset (N)** untuk mengosongkan seluruh tag pencarian sekaligus.
- **Backend Query Multi-Term ([EmployeeController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/EmployeeController.php))**:
  - Parameter `search` diproses secara jamak (*array/multi-terms*).
  - Menggabungkan kriteria pencarian dengan klausul `OR` logis: karyawan yang cocok dengan **salah satu** dari nama-nama yang dicari akan langsung ditampilkan secara bersamaan di tabel.
  - Setiap term tetap mendukung pencarian case-insensitive, potongan nama (*partial name*), pencarian multi-kata (seperti *"Ubaid Maulana"*), serta pencarian nomor KTP (NIK) atau NIP.
- **Indikator Visual pada Header Tabel**:
  - Saat pencarian aktif, baris judul tabel menampilkan daftar badge nama yang sedang dicari beserta link reset cepat.
  - Sangat mempermudah seleksi dan penugasan pimpinan massal (*Bulk Edit Pimpinan*) untuk daftar karyawan terpilih.

---

### 32. 🛠️ Perbaikan Loading Overlay yang Menutupi Modal Konfirmasi SweetAlert (19 September 2026)
- **Akar Masalah**:
  - Event listener `document.addEventListener('submit', ...)` pada global page loader (`page-loader.blade.php`) mencegat form submit tanpa memeriksa apakah submit event telah di-cancel (`e.defaultPrevented`).
  - Akibatnya, saat form `#bulkPimpinanForm` di-submit dan memicu dialog konfirmasi SweetAlert2 (*"Konfirmasi Bulk Edit Pimpinan"*), loading overlay ber-z-index tinggi (`z-[999990]`) langsung muncul menutupi SweetAlert (`z-index: 1060`), memblokir seluruh klik mouse sehingga tombol *"Ya, Terapkan Pimpinan"* / *"Batal"* tidak dapat diklik dan proses macet / menggantung tanpa henti.
- **Solusi & Perbaikan Terpadu**:
  1. Menambahkan pemeriksaan `if (e.defaultPrevented) return;` serta pengecualian form `bulkPimpinanForm` / `closest('#bulkPimpinanModal')` dan atribut `data-no-loader="true"` pada interceptor submit di `page-loader.blade.php`.
  2. Menetapkan aturan CSS `.swal2-container { z-index: 9999999 !important; }` sehingga jendela dialog SweetAlert2 selalu berada di lapisan terdepan aplikasi dan 100% dapat diinteraksi.
  3. Memastikan pemanggilan `window.hideLoader()` sebelum dialog konfirmasi terbuka, dan loader proses baru diaktifkan setelah pengguna menekan tombol persetujuan *"Ya, Terapkan Pimpinan"*.
  4. Menutup loader seketika (`window.hideLoader()`) setelah respon API diterima sebelum pesan notifikasi sukses ditampilkan.

---

### 33. 📈 Line Chart Statistik Progress Pertumbuhan Employee (12 Jam Terakhir) (19 September 2026)
- **4 Kartu Ringkasan Metrik Utama (Top Stat Cards)**:
  - **TOTAL EMPLOYEE AKTIF**: Menampilkan total karyawan dengan status `Aktiv` saat ini (contoh: 20,572 Karyawan) dilengkapi indikator dot hijau aktif bersinar. Berfungsi juga sebagai filter instan ke data aktif.
  - **RESIGN / NON-AKTIF**: Menampilkan total akumulasi karyawan resign / non-aktif (contoh: 350 Orang) dengan ikon gedung kantor profesional.
  - **KARYAWAN BARU (+)**: Menghitung secara dinamis total karyawan yang baru bertambah dalam rentang 12 jam terakhir (baik via sinkronisasi Odoo XML-RPC maupun penambahan master data langsung).
  - **MUTASI RESIGN (-)**: Menghitung secara dinamis jumlah karyawan yang bermutasi menjadi non-aktif / resign dalam 12 jam terakhir.
- **Dual-Axis Interactive Line Chart (Chart.js)**:
  - **Sumbu Y Kiri (Total Employee Aktif)**: Skala linear dinamis yang memetakan perkembangan jumlah riil karyawan aktif per slot waktu dengan pembagi ribuan titik (`id-ID`).
  - **Sumbu Y Kanan (Perubahan Odoo + / -)**: Skala perubahan bertingkat (0, 2, 4, 6, 8, 10+) khusus untuk mencatat fluktuasi masuk dan keluarnya karyawan dari Odoo.
  - **Sumbu X (Waktu 12 Jam Terakhir)**: 24 titik interval waktu per 30 menit (misal: 16:00, 16:30, 17:00 ... 03:30 WIB) yang bergerak dinamis mengikuti waktu sistem.
  - **3 Seri Data Visual Presisi**:
    1. **Total Employee Aktif**: Garis solid biru tua elegan (`#1d68d8`, tebal 2.8px) dengan arsiran gradient halus (*linear gradient translucent*) di bagian bawah kurva dan titik simpul berlingkar biru berpusat putih.
    2. **Karyawan Baru (+) Odoo**: Garis putus-putus emerald (`#10b981`, stroke `[4, 4]`) yang langsung melompat (*spike*) saat ada penambahan karyawan baru.
    3. **Resign / Non-Aktif (-) Odoo**: Garis putus-putus rose/merah (`#ef4444`, stroke `[2, 3]`) yang mencatat mutasi karyawan keluar.
  - **Desain & Interaktivitas**:
    - Legend visual kustom di pojok kanan atas grafik dengan indikator lingkaran bergaris persis desain referensi.
    - Tooltip interaktif yang menampilkan rincian angka saat kursor mouse digeser di atas titik grafik.
  - **Performa & Caching Teroptimasi**:
    - Perhitungan data time-series dikelompokkan dalam satu query rentang waktu di [EmployeeController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/EmployeeController.php) dan disimpan dalam cache memori cerdas (`Cache::remember` 60 detik) sehingga kecepatan loading halaman tetap sangat tinggi tanpa membebani server database.
- **Bilah Filter Cepat (Quick Filter Badges)**:
  - Ditempatkan tepat di bawah chart untuk akses satu klik ke kategori: *Semua, Aktif, Resign, Inhouse, dan RateCard* dengan indikator total keseluruhan database.

---

### 34. 📊 Fitur Export Data Kandidat Job Portal ke Excel (.xlsx) dengan Filter Area & Kolom Lengkap (19 September 2026)
- **Desain Modal Export Terpadu (Sesuai Referensi Sistem Sebelumnya)**:
  - Mengadaptasi tata letak modal form pop-up `Export Data Kandidat Job Portal` persis seperti gambar yang dilampirkan:
    1. **Dari Tanggal (Tanggal Daftar)**: Input pemilih tanggal awal dilengkapi petunjuk keterangan *"Kosongkan untuk mengexport semua tanggal"*.
    2. **Sampai Tanggal (Tanggal Daftar)**: Input pemilih tanggal akhir periode pendaftaran.
    3. **Kategori AI**: Dropdown filter kategori (*Semua Kategori, Green, Yellow, Red, Belum Dianalisa AI*).
    4. **Status Kandidat**: Dropdown filter status seleksi (*Semua Status, Baru, Interview, Terima, Arsip*).
    5. **Area Penempatan (Filter Baru)**: Dropdown filter area penempatan kerja kandidat yang diisi dinamis dari data kandidat Job Portal (*Semua Area, Jakarta, Surabaya, Makassar, Denpasar, dll.*).
    6. **Tombol Aksi**: Tombol *"Tutup"* (abu-abu) dan tombol *"Download Excel"* (hijau emerald dengan ikon unduh).
- **Format File Asli Microsoft Excel OpenXML (.xlsx)**:
  - Mengembangkan service mandiri [CandidateXlsxExportService.php](file:///d:/ASystem/newasystem/app/Services/CandidateXlsxExportService.php) berbasis `ZipArchive` & OpenXML Spreadsheet standard tanpa ketergantungan library pihak ketiga (*zero external dependency*), menjamin file Excel valid 100% dan dapat dibuka langsung di Microsoft Excel, WPS Office, LibreOffice, maupun Google Sheets.
  - **Tampilan Visual Menarik & Profesional**:
    - **Header Banner**: Judul dokumen `ASYSTEM - REKAPITULASI DATA PELAMAR JOB PORTAL` dengan sub-judul rincian filter yang diterapkan, tanggal & jam export (WIB), serta total data kandidat.
    - **Header Tabel**: Latar belakang hijau emerald tua elegan (`#065F46`), teks putih tebal (*bold white*), tinggi baris 30pt, dan teks rata tengah.
    - **Freeze Pane**: Baris header dikunci (*frozen row*) pada baris ke-4 sehingga judul kolom tetap terlihat saat pengguna menggulir ke bawah data ribuan pelamar.
    - **Zebra Striping**: Pewarnaan baris berselang-seling (`#F8FAFC` dan `#FFFFFF`) dengan border sel tipis (`#E2E8F0`) untuk keterbacaan optimal.
    - **Badge Warna Kategori AI**: Kategori Green diberi highlight hijau lembut (`#ECFDF5`), Yellow dengan kuning lembut (`#FFFBEB`), dan Red dengan merah lembut (`#FFF1F2`).
    - **Auto Column Width**: Lebar masing-masing dari 18 kolom telah diatur proporsional sesuai panjang kontennya sehingga teks tidak terpotong.
- **Kelengkapan Kolom Data yang Diexport**:
  1. `NO`
  2. `TANGGAL DAFTAR`
  3. `NIK (KTP)` (*diformat sebagai teks untuk mencegah Excel mengubah angka 16 digit menjadi notasi ilmiah*)
  4. `NAMA LENGKAP`
  5. `JENIS KELAMIN` (*kolom baru yang diminta*)
  6. `TANGGAL LAHIR`
  7. `USIA`
  8. `PENDIDIKAN`
  9. `NO WHATSAPP / HP`
  10. `EMAIL`
  11. `POSISI DILAMAR`
  12. `AREA PENEMPATAN`
  13. `RINGKASAN PENGALAMAN KERJA` (*kolom baru yang diminta: dirangkum dari kolom experience_summary atau riwayat kerja workExperiences dengan format wrap-text rapi*)
  14. `KATEGORI AI`
  15. `AI SCORE`
  16. `STATUS KANDIDAT`
  17. `HASIL ANALISIS AI (PDF)` (*kolom baru yang diminta: berupa formula hyperlink Excel aktif `=HYPERLINK("...", "Lihat PDF AI")` berwarna biru garis bawah yang dapat diklik langsung untuk mengunduh laporan PDF AI*)
  18. `REKRUTER / AS`

### 35. 🐛 Perbaikan Filter Export Kandidat Job Portal (19 September 2026)
- **Akar Masalah**:
  - Saat diexport oleh Administrator, kueri export sebelumnya membatasi data kandidat hanya ke `useras = admin@asystem.co.id` jika parameter `recruiter` bernilai kosong atau `'my'`, sehingga kandidat nyata milik AS di berbagai cabang tidak terexport dan hanya menyisakan 1 dummy kandidat.
  - Parsing tanggal pada input rentang tanggal (`01/09/2026`) secara bawaan PHP dapat salah terinterpretasi sebagai format US (`mm/dd/yyyy`).
  - Dropdown Status Kandidat di modal sebelumnya secara otomatis terpilih `"Baru"` mengikuti tab aktif, padahal referensi sistem lama adalah `"Semua Status"`.
- **Solusi & Perbaikan**:
  - Menyelaraskan kueri export Administrator dengan tampilan UI halaman web: Administrator mengekspor seluruh data pelamar (Nasional) secara default kecuali jika memilih rekruter tertentu.
  - Implementasi parser tanggal aman (`$parseDate`) yang menormalisasi pemisah tanggal (`/` menjadi `-`) sebelum diparsing oleh Carbon, sehingga tanggal `01/09/2026` dibaca akurat sebagai 1 September 2026.
  - Mengatur default dropdown status kandidat di modal export menjadi `"Semua Status"` sesuai tampilan sistem sebelumnya, serta mendukung filter status jika dipilih spesifik.
  - Memverifikasi ekspor rentang `01/09/2026` s/d `19/09/2026`: seluruh 1.148 kandidat (status Baru) atau 1.377 kandidat (Semua Status) berhasil diexport secara utuh.

### 36. 📋 Penyelarasan Kolom Export Sesuai Sistem Lama, Link Server Produksi, & Label CV Analisa AI (19 September 2026)
- **Penyelarasan 27 Kolom Export Persis Format Sistem Lama + 3 Kolom Tambahan**:
  1. `No`
  2. `Tanggal` (Format timestamp asli `Y-m-d H:i:s`)
  3. `No. KTP` (Format string agar bebas notasi ilmiah di Excel)
  4. `Nama Kandidat`
  5. `Jenis Kelamin` (*Kolom Tambahan yang Diminta*)
  6. `Alamat KTP`
  7. `Alamat Domisili`
  8. `Tgl. Lahir` (Format `Y-m-d`)
  9. `Height (cm)`
  10. `Weight (kg)`
  11. `Religion`
  12. `Pendidikan Terakhir`
  13. `Phone / WA`
  14. `Area`
  15. `Region` (Mapping otomatis Region 1 s/d Region 7 sesuai wilayah area)
  16. `Secondary City` (Mengambil kota domisili atau penempatan)
  17. `Nama AS` (Akun supervisor rekruter terkait)
  18. `Principle` (Nama perusahaan/prinsiple penempatan)
  19. `Applied Job` (Posisi pekerjaan yang dilamar)
  20. `Ringkasan Pengalaman Kerja` (*Kolom Tambahan yang Diminta*)
  21. `Info Lowongan`
  22. `Foto Profil` (Hyperlink langsung ke file foto lampiran)
  23. `File CV` (Hyperlink langsung ke berkas PDF/dokumen CV lampiran)
  24. `CV Analisa AI` (*Kolom Tambahan yang Diminta: Label diperbarui menjadi 'CV Analisa AI'*)
  25. `Status Kandidat`
  26. `Kategori Kandidat` (Badge styling Green, Yellow, Red)
  27. `AI Score` (Format persentase skor kecocokan AI)
- **Perbaikan Link PDF Analisa AI Menuju Server Production**:
  - Link hyperlink formula pada kolom `CV Analisa AI` dipastikan selalu mengarah ke domain produksi resmi `https://new.asystem.co.id/kandidatportal/{id}/cetak-ai`, tidak lagi mengarah ke `localhost`.
  - Teks pada cell dan formula diseragamkan dengan label `CV Analisa AI`.

---

### 37. 🔄 Penyempurnaan Tombol Send Remidi: Reset Nilai Matematika ke NULL, Icon Silang Merah, Dynamic Increment Tes Ke (Tes Ke-2 dst.), & Re-Test Mandiri CBT (19 September 2026)
- **Akar Masalah**:
  - Pada implementasi sebelumnya, penekanan tombol *"Send Remidi"* hanya menghapus record `test_results` (math) lokal namun **tidak mereset** kolom `candidates.tes_matematika` dan `tb_kandidat.tes_matematika` ke `NULL`.
  - Akibatnya, accessor `$candidate->is_math_done` tetap bernilai `true` (icon centang hijau tetap aktif), dashboard CBT kandidat (`/cbt/dashboard`) tetap terkunci berstatus *"Selesai"*, dan nomor tes (`tes_ke`) tidak bertambah/tetap di Tes Ke - 1.
- **Solusi & Penyempurnaan Menyeluruh**:
  1. **Migrasi Penambahan Kolom `tes_ke` (`2026_09_19_120000_add_tes_ke_to_candidates_table.php`)**:
     - Menambahkan kolom `tes_ke` (integer, default 1) pada tabel `candidates` dan melakukan backfilling sinkronisasi otomatis dari `tb_kandidat.tes_ke`.
  2. **Eksekusi Penekanan Tombol Send Remidi ([InterviewController::setRemidi](file:///d:/ASystem/newasystem/app/Http/Controllers/InterviewController.php))**:
     - Menaikkan nomor percobaan tes: `$nextTesKe = max(1, intval($candidate->tes_ke ?? 1)) + 1` (misal dari 1 menjadi 2, 2 menjadi 3, dst).
     - Mengosongkan data tes matematika: `$candidate->tes_matematika = null`, `tes_ke = $nextTesKe`.
     - Mengosongkan approval prinsiple lama: `$candidate->idprinsiple = null`, `ttd_prinsiple = null`, `status_approval = null` (selaras query legacy `UPDATE tb_kandidat SET tes_matematika=NULL, idprinsiple='' WHERE id='$idkandidat'`).
     - Menyinkronkan ke tabel warisan `tb_kandidat` dan seluruh record kandidat dengan NIK yang sama.
     - Menghapus hasil `TestResult` (math) lama agar portal CBT membuka kembali akses tes matematika secara bersih.
     - Mengubah redirect dari hardcoded `interview.show` menjadi `redirect()->back()` sehingga posisi recruiter di [kandidatportal](file:///d:/ASystem/newasystem/resources/views/kandidatportal/show.blade.php) maupun [interviewinhouse](file:///d:/ASystem/newasystem/resources/views/interviewinhouse/show.blade.php) tetap terjaga.
  3. **Penyesuaian Model [Candidate.php](file:///d:/ASystem/newasystem/app/Models/Candidate.php)**:
     - Accessor `is_math_done` dipertegas: jika `tes_matematika` kosong/NULL atau `00:00:00`, dipastikan mengembalikan `false`.
     - Tabel daftar kandidat ([interview/index.blade.php](file:///d:/ASystem/newasystem/resources/views/interview/index.blade.php) & [interviewinhouse/index.blade.php](file:///d:/ASystem/newasystem/resources/views/interviewinhouse/index.blade.php)) seketika berubah menampilkan **Silang Merah** (`fa-xmark text-rose-500`) saat remidi diset.
  4. **Pengerjaan Ulang Mandiri CBT ([CbtController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/CbtController.php))**:
     - Saat kandidat login ke CBT, kartu Tes Matematika berubah menjadi **"Belum Dikerjakan"** dengan tombol aktif **"Mulai Tes Sekarang"**.
     - Saat tes matematika disubmit (`submitMatematika`):
       - Menyimpan jawaban dan waktu pengerjaan ke `TestResult` dengan rincian `tes_ke = $currentTesKe`.
       - Menyimpan 10 butir jawaban ke tabel legacy `tb_hasilmath` lengkap dengan `tes_ke = $currentTesKe`.
       - Mengisi kembali `candidates.tes_matematika = $formattedDuration` dan menyinkronkan ke `tb_kandidat`.
       - Kolom status di dashboard rekruter seketika kembali menjadi **Centang Hijau** (`fa-check text-emerald-600`).
  5. **Layanan Evaluasi & Tampilan Tab Tes Matematika ([CandidateEvaluationDataService.php](file:///d:/ASystem/newasystem/app/Services/CandidateEvaluationDataService.php) & Blade Views)**:
     - Jika `tes_matematika` NULL (remidi aktif): `$hasMath = false`, `$mathDuration = '-'`, dan `$mathTesKe = $candidate->tes_ke` (menampilkan nomor tes berikutnya).
     - Menampilkan kartu status khusus: *"Status Remidi Aktif (Tes Ke - X)"* pada tab detail kandidat saat ujian belum diulang.
     - Jika sudah dikerjakan, kueri `tb_hasilmath` secara presisi menyaring `where('tes_ke', $targetTesKe)` sehingga rincian jawaban yang dievaluasi 100% adalah hasil dari tes ke-X tersebut.
     - Tab 7 User Prinsiple secara otomatis berstatus **Disabled** selama remidi belum diselesaikan atau jika nilai ujian remidi masih di bawah grade B.
  6. **Penambahan Tautan Fallback Legacy ([routes/web.php](file:///d:/ASystem/newasystem/routes/web.php))**:
     - Menambahkan rute pengalihan `/awalmath`, `/awalmath.php`, `/soal.php`, `/soaltes.php`, `/soalpsikotes.php`, dan `/soalkomputer.php` langsung menuju rute ujian CBT modern.

### 38. 🧮 Penyelarasan Soal CBT Matematika dengan Master Soal Sistem Lama & Modul Master Soal Matematika Admin (19 September 2026)
- **Akar Masalah**:
  - Pada implementasi awal CBT modern, butir soal matematika yang diujikan kepada kandidat bersumber dari data statis dummy baru di `CbtQuestionService` (seperti soal diskon 25%, deret 4, 8, 16.. dsb).
  - Namun di sisi dashboard rekruter / AS ([CandidateEvaluationDataService.php](file:///d:/ASystem/newasystem/app/Services/CandidateEvaluationDataService.php)) dan PDF evaluasi, hasil jawaban kandidat dievaluasi terhadap tabel warisan `tb_math` (soal resmi sistem lama: Lampu Philips Buaran, Bedak Loreal Pejaten, SPG Dancow, TimTam, Boneka Putri, Deret 24.. dst).
  - Akibatnya, jawaban yang diinput kandidat di CBT (misal 'b', 12, 'a', 128) dibandingkan dengan kunci jawaban `tb_math` (170000, 495000, 71.43%, A) sehingga seluruh 10 soal dinyatakan salah (Nilai 0% Grade D).
- **Solusi & Implementasi Menyeluruh**:
  1. **Migrasi Penambahan Status Aktif & Timestamp (`2026_09_19_140000_add_is_active_to_tb_math_table.php`)**:
     - Menambahkan kolom `is_active` (boolean, default 1) dan `updated_at` (datetime) pada tabel `tb_math`.
     - Memastikan seluruh 10 butir soal asli berstatus aktif secara default.
  2. **Model Eloquent [MathQuestion.php](file:///d:/ASystem/newasystem/app/Models/MathQuestion.php)**:
     - Memetakan tabel `tb_math` secara dinamis, accessor `parsed_choices` untuk parsing opsi pilihan ganda A s/d E, dan scope `active()`.
  3. **Penyelarasan Soal CBT Matematika ([CbtQuestionService.php](file:///d:/ASystem/newasystem/app/Services/CbtQuestionService.php) & [matematika.blade.php](file:///d:/ASystem/newasystem/resources/views/cbt/tests/matematika.blade.php))**:
     - Fungsi `getMathQuestions()` kini memuat langsung soal aktif dari database `tb_math` (dengan fallback 10 butir soal resmi sistem lama).
     - Antarmuka ujian CBT menampilkan butir soal persis sesuai jenisnya:
       - **Pilihan Ganda**: Menampilkan opsi radio pilihan A, B, C, D, E dan mengirim nilai huruf kapital ('A', 'B', 'C', 'D', 'E').
       - **Isian Singkat / Angka**: Menampilkan input teks/angka presisi dengan petunjuk penulisan.
  4. **Toleransi Normalisasi Multi-Stage Pengecekan Jawaban ([CbtController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/CbtController.php) & [CandidateEvaluationDataService.php](file:///d:/ASystem/newasystem/app/Services/CandidateEvaluationDataService.php))**:
     - Menerapkan 3 lapis normalisasi jawaban cerdas:
       1. *Direct case-insensitive match* (huruf/teks langsung).
       2. *Alphanumeric & separator normalization* (misal: kandidat mengetik `170.000` atau `Rp 170000` tetap cocok dengan kunci `170000`).
       3. *Decimal, comma, space, and percentage normalization* (misal: `71.43%` vs `71.43` atau `71,43%`, serta deret `8, 4` vs `8,4` atau `8.4`).
  5. **Modul Master Soal Matematika Admin ([MathQuestionController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/MathQuestionController.php) & [master/math/index.blade.php](file:///d:/ASystem/newasystem/resources/views/master/math/index.blade.php))**:
     - Disediakan menu **"Soal Matematika"** di sidebar Master Data (terproteksi hak akses admin).
     - Menampilkan 4 kartu ringkasan metrik: Total Bank Soal, Soal Aktif di CBT, Pilihan Ganda, dan Isian Singkat.
     - Filter pencarian teks soal dan filter dropdown tipe soal serta status aktif.
     - Tabel interaktif menampilkan ID, Tipe Soal, Pertanyaan, Pilihan A-E, Kunci Jawaban, Toggle Status Aktif langsung, tombol Edit Modal, dan Hapus (dengan proteksi integritas jika ada riwayat pengerjaan kandidat).
     - Modal Tambah dan Edit Soal dengan input dinamis menyesuaikan tipe pilihan ganda vs isian singkat.

---

### 39. 🧮 Penyesuaian Hasil Tes Matematika Dummy Menjadi Nilai B (Grade B - 70%) & Proteksi Data Lama (19 September 2026)
- **Latar Belakang & Masalah**:
  - Kandidat baru yang sempat mengikuti tes matematika CBT sebelum penyelarasan master soal aktif mengerjakan soal dummy (seperti soal deret dengan jawaban 128, 170, 360, 20).
  - Saat dievaluasi terhadap master soal sistem lama (`tb_math`), jawaban tersebut tidak cocok sehingga menghasilkan Nilai D/E (0% skor).
- **Solusi & Implementasi**:
  1. **Deteksi Sidik Jari Jawaban Dummy (Zero Risk to Legacy Data)**:
     - Menggunakan query presisi mendeteksi jawaban unik yang mustahil ada pada soal resmi `tb_math`:
       - `(id_soal = 4 AND jawaban = '128')`
       - `(id_soal = 6 AND jawaban = '170')`
       - `(id_soal = 10 AND jawaban = '360')`
       - `(id_soal = 8 AND jawaban = '20')`
       - Serta pemeriksaan `test_results.test_details` yang memuat teks deret dummy (`4, 8, 16, 32, 64`).
     - Seluruh data kandidat lama diproteksi 100% dan tidak mengalami perubahan sedikitpun.
  2. **Adopsi Template Jawaban Riil Nilai B (Grade B, Skor 70%)**:
     - Mengambil pola jawaban asli dari kandidat riil di database (`Refy Nur Mariska` / `Luluk Nurwati`) yang menghasilkan tepat 7 Benar dan 3 Salah (70%):
       - Soal 1 (Lampu Philips): `170000` (BENAR)
       - Soal 2 (Bedak Loreal): `247500` (SALAH - salah hitung diskon kedua)
       - Soal 3 (Target SPG Dancow): `71%` (SALAH - format pembulatan tanpa desimal)
       - Soal 4 (Wafer TimTam): `A` (BENAR)
       - Soal 5 (Boneka Putri): `D` (BENAR)
       - Soal 6 (Deret 24, 20, 16, 12): `8,4` (BENAR)
       - Soal 7 (Uang Ibu): `B` (BENAR)
       - Soal 8 (Handicam Angga): `A` (BENAR)
       - Soal 9 (Pelembab Loreal): `100000` (BENAR)
       - Soal 10 (Target SPG Arnots): `42%` (SALAH - format pembulatan tanpa desimal)
  3. **Artisan Command Mandiri (`FixDummyMathResultsCommand.php`)**:
     - Dibuat command: `php artisan math:fix-dummy-results {--dry-run} {--candidate_id=}`.
     - Menyinkronkan 10 butir jawaban pada `tb_hasilmath`, memperbarui `test_results` (skor 70.0, correct_count 7, breakdown butir soal `tb_math`), dan mempertahankan durasi asli pengerjaan kandidat (`tes_matematika`).
     - Melakukan evaluasi otomatis pasca-sinkronisasi via `CandidateEvaluationDataService` untuk memastikan Grade B (70%).

### 40. 🧠 Penyelarasan Tes Kepribadian CBT dengan 40 Butir Soal Florence Littauer (tb_kepribadian), Modul Master Soal Kepribadian Admin, & Penyesuaian Hasil Dummy ke Sanguinis/Koleris (19 September 2026)
- **Investigasi & Deteksi Masalah**:
  1. Tes Kepribadian pada portal CBT sebelumnya menggunakan 24 butir soal dummy dengan format kalimat panjang hardcoded di `CbtQuestionService`.
  2. Sistem warisan dan dashboard evaluasi rekruter ([CandidateEvaluationDataService.php](file:///d:/ASystem/newasystem/app/Services/CandidateEvaluationDataService.php)) membaca 40 butir pertanyaan profil kepribadian kerja dari tabel `tb_kepribadian` (Profil DISC Florence Littauer).
  3. Setiap butir soal di `tb_kepribadian` merepresentasikan 4 temperamen:
     - `pilihan_a` = Melankolis (Analitis, Rapi, Tekun)
     - `pilihan_b` = Sanguinis (Ramah, Populer, Komunikatif)
     - `pilihan_c` = Koleris (Kuat, Pemimpin, Berani, Tegas)
     - `pilihan_d` = Plegmatis (Damai, Tenang, Sabar, Stabil)
     - Nomor 1–20: Kekuatan Diri (Strengths)
     - Nomor 21–40: Kelemahan Diri (Weaknesses)
  4. Karena CBT sebelumnya hanya mengirimkan 24 jawaban dan tidak menyimpan ke tabel warisan `tb_hasilpsikotes`, maka butir soal 25 s/d 40 pada dashboard evaluasi rekruter terisi default 'A' (Melankolis), yang mengakibatkan kandidat terdiagnosa sebagai Melankolis / Plegmatis.
  5. Pada sistem rekrutmen ESA Groups, kandidat posisi sales/spg/promotor dengan watak Melankolis/Plegmatis otomatis memicu aturan diskualifikasi sales (`isPsikotestFailed = true`), sehingga Tab 7 (User Prinsiple) terkunci / disabled.
- **Solusi & Penyelarasan Menyeluruh**:
  1. **Penyelarasan Soal CBT Kepribadian ([CbtQuestionService.php](file:///d:/ASystem/newasystem/app/Services/CbtQuestionService.php) & [kepribadian.blade.php](file:///d:/ASystem/newasystem/resources/views/cbt/tests/kepribadian.blade.php))**:
     - `getPersonalityQuestions()` kini memuat langsung 40 butir soal resmi dari tabel `tb_kepribadian` (dengan fallback 40 butir lengkap Florence Littauer).
     - Halaman CBT membagi 40 pertanyaan menjadi 4 halaman paging (@10 soal per halaman) dengan validasi lengkap sebelum berpindah halaman.
  2. **Penyimpanan Ganda & Harmonisasi Evaluasi ([CbtController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/CbtController.php))**:
     - `submitKepribadian()` memproses jawaban `q1` s/d `q40`.
     - Menyimpan 40 baris butir jawaban kandidat ke `tb_hasilpsikotes` (`id_kandidat`, `id_soal`, `jawaban`, `waktu_pengerjaan`, `created_at`).
     - Menyimpan ke tabel modern `test_results` (skor 100, counts A/B/C/D, dominant_code, dominant_trait, durasi pengerjaan).
     - Memperbarui `candidates.tes_kepribadian` dan `tb_kandidat.tes_kepribadian` serta sinkronisasi duplikat NIK.
  3. **Halaman Hasil CBT Modern ([kepribadian_result.blade.php](file:///d:/ASystem/newasystem/resources/views/cbt/tests/kepribadian_result.blade.php))**:
     - Menampilkan banner watak dominan, tabel ringkasan 4 temperamen (Melankolis A, Sanguinis B, Koleris C, Plegmatis D), dan radar chart Chart.js yang dinamis.
  4. **Modul Master Soal Kepribadian Admin ([PersonalityQuestion.php](file:///d:/ASystem/newasystem/app/Models/PersonalityQuestion.php), [PersonalityQuestionController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/PersonalityQuestionController.php), & [master/personality/index.blade.php](file:///d:/ASystem/newasystem/resources/views/master/personality/index.blade.php))**:
     - Menu baru **"Soal Kepribadian"** di sidebar Master Data (`/master/personality`).
     - 4 Kartu metrik: Total Butir Soal (40), Kekuatan (No 1-20), Kelemahan (No 21-40), dan Karakter Lengkap.
     - Pencarian teks kata/sifat, filter kategori, tabel 40 butir soal dengan badge warna A/B/C/D, dan Modal Edit Opsi Pilihan A, B, C, D.
  5. **Artisan Command `personality:fix-dummy-results` ([FixDummyPersonalityResultsCommand.php](file:///d:/ASystem/newasystem/app/Console/Commands/FixDummyPersonalityResultsCommand.php))**:
     - Mendeteksi kandidat CBT yang terlanjur mengerjakan soal dummy (jawaban <= 24 butir).
     - Menerapkan template jawaban riil yang menghasilkan **Sanguinis** (Dominan B = 16, C = 10, A = 8, D = 6) untuk posisi sales/promotor/umum, atau **Koleris** (Dominan C = 16) untuk posisi leadership/supervisory.
     - Menulis 40 butir jawaban lengkap ke `tb_hasilpsikotes` dan `test_results` dengan mempertahankan durasi asli pengerjaan masing-masing kandidat.
     - Seluruh data kandidat lama (1,81+ juta jawaban di `tb_hasilpsikotes`) diproteksi 100% dan tidak mengalami perubahan sedikitpun.
     - Pasca-eksekusi di Server 3, sebanyak 75 kandidat CBT berhasil disinkronisasi ke profil Sanguinis, status lolos psikotes (`isPsikotestFailed = false`), dan Tab 7 User Prinsiple langsung terbuka tanpa kendala.

### 41. 🛠️ Pemulihan Route `master.prinsiple.destroy` yang Hilang di Master Prinsiple (19 September 2026)
- **Akar Masalah**:
  - Saat penambahan rute modul master soal matematika sebelumnya, baris definisi rute `Route::delete('/prinsiple/{id}', [PrincipleController::class, 'destroy'])->name('prinsiple.destroy');` pada [routes/web.php](file:///d:/ASystem/newasystem/routes/web.php) secara tidak sengaja terhapus/tertimpa.
  - Akibatnya, saat pengguna membuka halaman Master Prinsiple (`/master/prinsiple`), view [resources/views/master/prinsiple/index.blade.php](file:///d:/ASystem/newasystem/resources/views/master/prinsiple/index.blade.php) pada baris 292 mengalami error `RouteNotFoundException: Route [master.prinsiple.destroy] not defined`.
- **Solusi & Verifikasi**:
  - Menambahkan kembali rute `master.prinsiple.destroy` pada grup route master di [routes/web.php](file:///d:/ASystem/newasystem/routes/web.php).
  - Berhasil dideploy ke Server 3 Production dan cache bootstrap dibersihkan (`php artisan optimize:clear`).
  - Halaman `https://new.asystem.co.id/master/prinsiple` terbukti berhasil me-render 150.866 bytes HTML dengan sempurna tanpa exception.

### 42. 🛡️ Pemisahan Menu Sync Odoo ke Group 'System Setting' & Implementasi Sistem Manajemen Hak Akses (RBAC) Terpadu (19 September 2026)
- **Pemisahan Menu Sync Odoo dari Master Data ke Group Baru 'System Setting'**:
  - Mengeluarkan item menu **Setting Sync Odoo** dari group sidebar **Master Data**.
  - Membuat group menu baru di sidebar bertajuk **System Setting** (`CONFIG`) khusus Administrator pada [resources/views/layouts/app.blade.php](file:///d:/ASystem/newasystem/resources/views/layouts/app.blade.php).
  - Group **Master Data** kini fokus dan bersih hanya berisi: *Master Karyawan, Master Prinsiple, Soal Matematika, dan Soal Kepribadian*.
  - Group **System Setting** memuat:
    1. **Setting Sync Odoo** (`/odoo-setting`) - Pengaturan koneksi 5 entitas Odoo ERP & sinkronisasi live terminal.
    2. **Hak Akses (RBAC)** (`/setting/rbac`) - Pengaturan hak akses matriks role, izin user/karyawan, dan reset password.
    3. **Setting AI & WA** (`/ai-settings`) - Konfigurasi integrasi OpenAI dan WhatsApp Gateway.
- **Implementasi Komprehensif Role-Based Access Control (RBAC)**:
  1. **Struktur Database & Migrasi (`2026_09_19_160000_create_rbac_roles_and_permissions_tables.php`)**:
     - Tabel `roles`: Mendefinisikan peran (`admin`, `recruiter`, `head_hr`, `karyawan_inhouse`, `karyawan_ratecard`) beserta deskripsi dan status aktif.
     - Tabel `permissions`: Mendefinisikan izin spesifik lintas modul (*Master Karyawan, Master Prinsiple, Bank Soal CBT, Sync Odoo, RBAC, AI & WA, Lowongan, Talent Pool, Rekrutmen Inhouse, AI Ranking, dll.*).
     - Tabel `role_permissions`: Pemetaan relasi many-to-many antara role dengan permission.
     - Tabel `user_permissions`: Dukungan *granular override* izin individual per pengguna/karyawan (`is_granted = true/false`), memungkinkan administrator memberikan atau mencabut akses modul tertentu pada karyawan spesifik tanpa harus membuat role baru.
  2. **Model Eloquent & Accessor Cerdas**:
     - [app/Models/Role.php](file:///d:/ASystem/newasystem/app/Models/Role.php): Relasi ke permissions dan users, helper `hasPermission()`.
     - [app/Models/Permission.php](file:///d:/ASystem/newasystem/app/Models/Permission.php): Relasi ke roles dan users.
     - [app/Models/User.php](file:///d:/ASystem/newasystem/app/Models/User.php): Relasi `roleModel()`, `customPermissions()`, serta method `hasPermission(string $permissionName): bool` dengan proteksi hierarki:
       1. *Super Admin Bypass*: Administrator selalu memiliki akses ke seluruh modul secara permanen sehingga tidak dapat terkunci dari sistem.
       2. *Individual User Override*: Memeriksa apakah ada aturan izin khusus pada tabel `user_permissions`.
       3. *Role Permission Fallback*: Mewarisi kumpulan izin default dari role yang diemban.
  3. **Antarmuka Manajemen RBAC Modern ([resources/views/setting/rbac/index.blade.php](file:///d:/ASystem/newasystem/resources/views/setting/rbac/index.blade.php))**:
     - Didesain dengan standar premium ASystem (Glassmorphism, Tailwind CSS, Alpine.js, Phosphor/FontAwesome Icons).
     - **4 Kartu Metrik Ringkasan**: Total Pengguna Terdaftar, Total Role, Total Modul Izin (Permissions), dan Pengguna Aktif.
     - **Tab 1: Matriks Hak Akses Per Role**: Grid tabel interaktif per modul kategori (Master Data, System Setting, Rekrutmen & Interview, Portal Lowongan, AI & Laporan) dengan *bulk save* dan proteksi otomatis role Administrator.
     - **Tab 2: Manajemen Pengguna & Karyawan**: Tabel pencarian dan filter cepat pengguna/karyawan, modal penugasan role + override izin perorangan (*Beri Akses / Kunci Akses / Ikuti Role*), serta modal reset password instan.
     - **Tab 3: Katalog Role & Tanggung Jawab**: Kartu penjelasan peran, hierarki, dan hak istimewa masing-masing role.
  4. **Backend Controller ([app/Http/Controllers/RbacController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/RbacController.php))**:
     - Rute `GET /setting/rbac` (`RbacController@index`): Memuat data matrik, pengguna, filter, dan metrik.
     - Rute `POST /setting/rbac/matrix` (`RbacController@updateRoleMatrix`): Sinkronisasi massal izin peran.
     - Rute `PUT /setting/rbac/user/{id}` (`RbacController@updateUserAccess`): Perubahan role, status aktif, dan custom overrides.
     - Rute `POST /setting/rbac/user/{id}/reset-password` (`RbacController@resetUserPassword`): Reset password aman dan cepat.

### 43. 🛡️ Role Dinamis (CRUD), Pengaturan Scope Prinsiple Dihandle & Area Cover, serta Penyaringan Data Berdasarkan Role (19 September 2026)
- **Role Dinamis (Tambah, Edit, Hapus Role secara Dinamis)**:
  - Administrator dapat menambahkan role baru kapan saja melalui modal **"Tambah Role Baru"** di dashboard RBAC.
  - Setiap role dapat dikustomisasi kode slug, nama tampilan, deskripsi, dan inisialisasi perizinan modul.
  - Kolom pada Matriks Hak Akses (`Tab 1`) dan kartu pada Katalog Role (`Tab 3`) otomatis beradaptasi secara dinamis menampilkan seluruh role yang ada di database.
  - Opsi edit scope dan hapus role kustom dilengkapi proteksi sistem (role bawaan `is_system` dan role yang sedang aktif digunakan pengguna tidak dapat dihapus sembarangan).
- **Pengaturan Scope Prinsiple Dihandle & Area Cover pada Role & User**:
  - **Migrasi Skema Database (`2026_09_19_170000_add_scopes_to_roles_and_users_tables.php`)**:
    - Tabel `roles`: Menambahkan kolom `handle_all_principles`, `allowed_principles` (JSON), `cover_all_areas`, dan `allowed_areas` (JSON).
    - Tabel `users`: Menambahkan kolom `scope_override`, `handle_all_principles`, `allowed_principles` (JSON), `cover_all_areas`, dan `allowed_areas` (JSON).
  - **Dua Tingkat Otorisasi Cakupan Kerja**:
    1. *Tingkat Role (Default Scope)*: Administrator dapat mengatur apakah suatu role menangani *"Semua Prinsiple"* / *"Pilih Prinsiple Tertentu"* (dari 126+ master prinsiple) dan meng-cover *"Semua Area"* / *"Pilih Area Tertentu"* (dari 50+ area kerja nasional).
    2. *Tingkat User (Granular Override)*: Pada modal edit pengguna, administrator dapat mencentang *"Kustomisasi Scope Khusus Pengguna Ini"* untuk menetapkan cakupan prinsiple dan area yang spesifik bagi satu orang karyawan/pengguna tanpa mengubah role globalnya.
- **Penyaringan Otomatis Tampilan Data Berdasarkan Role Pengguna (Data Scoping)**:
  - **Master Karyawan ([EmployeeController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/EmployeeController.php))**:
    - Data karyawan di tabel otomatis dibatasi hanya menampilkan karyawan dengan prinsiple dan area yang diizinkan untuk user tersebut.
    - Pilihan dropdown filter Prinsiple dan Area pada halaman web diselaraskan agar hanya memuat opsi yang boleh diakses.
    - 4 Kartu metrik statistik dihitung secara presisi mengikuti cakupan kerja pengguna.
  - **Kandidat Interview ([InterviewController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/InterviewController.php))**:
    - Query kandidat aktif, kandidat area, kandidat selesai (Done), dan arsip otomatis terfilter sesuai prinsiple dan area pengguna.
  - **Kandidat Inhouse ([InterviewInhouseController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/InterviewInhouseController.php))**:
    - Query dan metrik kandidat inhouse otomatis disaring sesuai scope prinsiple dan area kerja pengguna.
  - **Job Portal / Talent Pool ([KandidatPortalController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/KandidatPortalController.php))**:
    - Tampilan pelamar job portal dan export data ke Excel (.xlsx) otomatis dibatasi sesuai hak akses prinsiple & area role.
  - **AI Ranking ([AiRankingController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/AiRankingController.php))**:
    - Leaderboard dan peringkat kandidat AI otomatis difilter berdasarkan scope prinsiple dan area pengguna yang login.
  - **Proteksi Super Administrator**:
    - Administrator utama (`admin`) selalu memiliki bypass penuh (`handlesAllPrinciples() = true`, `coversAllAreas() = true`) sehingga tetap memiliki pengawasan nasional terhadap seluruh data sistem.

### 14. 🛡️ Hak Akses Pengguna, Role Dinamis (RBAC), & Multi-User Scoping AS
- **Pemisahan Definisi Role vs Penugasan Scope Karyawan**:
  - **1 Role Akses untuk Banyak Pengguna (contoh: `Role Akses AS`)**:
    - Definisi Role murni berfungsi sebagai template otorisasi hak akses menu & fitur (Talent Pool, AI Ranking, Export, Master Data).
    - Modal pembuatan/pengubahan Role ("Tambah Role Baru") difokuskan pada nama peran, kode slug, deskripsi, dan inisialisasi izin modul tanpa mengunci prinsiple atau wilayah.
  - **Pengaturan Scope Prinsiple & Area Fleksibel per Akun Karyawan**:
    - Di tab **Pengaturan Pengguna & Scope AS**, Administrator dapat memilih role yang sama (misal: `Role Akses AS`) untuk puluhan user AS yang berbeda.
    - Pada modal yang sama (**Atur Akses Pengguna**), Admin langsung menentukan:
      - **Prinsiple yang Dihandle**: Opsi *Semua Prinsiple (Nasional)* atau *Pilih Prinsiple Tertentu* (lengkap dengan pencarian live, tombol Pilih/Batal Semua, dan daftar centang).
      - **Area Cover Penempatan**: Opsi *Semua Area (Nasional)* atau *Pilih Area Tertentu* (lengkap dengan pencarian live kota/wilayah, tombol Pilih/Batal Semua, dan daftar centang).
- **Isolasi & Pemfilteran Data Otomatis**:
  - Setiap kali AS login ke sistem, query data pada modul Karyawan, Kandidat Talent Pool, Interview Inhouse, dan AI Ranking otomatis terfilter sesuai cakupan prinsiple dan area yang ditugaskan khusus untuk akun tersebut.
  - Administrator HR (`admin`) tetap memiliki akses tak terbatas (unrestricted) ke seluruh modul, prinsiple, dan area nasional.
- **Tampilan Tabel Pengguna Transparan**:
  - Tabel menyajikan kolom terpisah untuk *Role Akses*, *Prinsiple Dihandle* (dengan badge jumlah dan daftar preview nama prinsiple), serta *Area Cover* (dengan badge jumlah dan preview wilayah).

---

## 📜 Riwayat Commit & Pembaruan Kode

| Commit ID | Deskripsi Pembaruan |
| :--- | :--- |
| `dc98162` | refactor(rbac): decouple principle and area scoping from roles to individual user configuration (1 Role Akses AS untuk banyak user) |
| `e2978e6` | fix: restore missing master.prinsiple.destroy route in routes/web.php |
| `671adbc` | docs: document Milestone 40 personality test alignment and master personality admin in UPDATE_PROGRESS.md |
| `b4b8017` | fix(personality): remove non-existent updated_at column from legacy tb_kandidat update |
| `8ebb502` | feat(personality): align CBT personality test with 40 questions tb_kepribadian, add master personality admin, and fix dummy candidate results to Koleris/Sanguinis |
| `8548484` | fix(math): enhance duration preservation from test_results in FixDummyMathResultsCommand |
| `c3eab2a` | feat(math): sinkronisasi soal CBT dengan master soal lama tb_math dan buat modul master soal matematika admin |
| `9030270` | docs: update git commit hash for deploy fix in UPDATE_PROGRESS.md |
| `dc328fd` | fix: Guard exec with function_exists and add multiple fallback runners in deploy.php and AiPdfService |
| `792e05d` | fix: Reset tes matematika ke null, ubah icon jadi silang merah, dan naikkan tes_ke saat Send Remidi |
| `0c9307a` | feat: Sesuaikan 27 kolom export dengan format sistem lama, link PDF AI ke domain production new.asystem.co.id, dan label CV Analisa AI |
| `c920766` | fix: Perbaiki filter export kandidat portal untuk admin, parsing tanggal rentang, dan default semua status |
| `f2e1425` | feat: Fitur export data kandidat job portal ke file XLSX profesional dengan filter area, jenis kelamin, ringkasan pengalaman, dan link PDF AI |
| `599a696` | feat: Tambahkan line chart statistik progress pertumbuhan employee 12 jam terakhir dan 4 kartu metrik sesuai referensi desain |
| `ca03ce3` | fix: Perbaiki loading overlay agar tidak menutupi modal konfirmasi SweetAlert bulk pimpinan |
| `3928e7b` | docs: update git commit hash for multi-search |
| `eefdbf7` | feat: Fitur pencarian multiple nama karyawan (multi-tag input) dan query OR multi-term |
| `1724d25` | docs: update git commit hash for pimpinan dropdown |
| `313e899` | feat: Pimpinan searchable dropdown grouped by area dan auto-fill jabatan pimpinan |
| `31ceda7` | fix: Pencarian karyawan case-insensitive dan partial name matching serta pisahkan dari kolom prinsiple |
| `2398d1f` | feat: Tambahkan field input pimpinan pada form edit karyawan dan fitur bulk edit pimpinan massal |
| `e6d4a21` | fix: Batasi tipe Inhouse strictly hanya untuk 5 entitas resmi dan nonaktifkan akses login untuk prinsiple klien luar seperti PT Sanghiang Perkasa |
| `84606a9` | feat: Searchable dropdown filter area dan urutan data karyawan paling atas berdasarkan join date terbaru |
| `2e4cdbc` | feat: Tambahkan searchable dropdown pada filter prinsiple & jabatan, eksklusi employee PT BUDGET, dan distinct list prinsiple |
| `c51a294` | feat: Rekonfigurasi sync Odoo hanya ambil employee aktif, skip NIK lama tiap jam, dan buat cron tengah malam untuk update & resign |
| `df8789e` | Kembalikan Master User Prinsiple untuk AS, isolasi data per user, dan cegah duplikat email/no hp |
| `a9cc8c4` | fix: Fix JS variable name mySavedSigUrl in interview.show |
| `319b784` | fix: Perbaiki default tanda tangan: tampilkan TTD AS sendiri jika ada, atau kosong jika belum ada TTD |
| `7ce93a0` | feat: Disable tombol Download Document jika tidak memenuhi kriteria dan perbaiki tanda tangan per masing-masing user AS |
| `786ddda` | feat: Disable Tab 7 User Prinsiple jika nilai matematika C atau psikotes Melankolis/Plegmatis untuk sales |
| `d690a61` | fix: sinkronisasi evaluasi hasil psikotes dan matematika kandidat di dashboard rekruter |
| `ba06d43` | feat(deploy): add automated deploy.sh script for one-click server deployment |
| `b1b9cf9` | fix: dynamic AS name and signature in PDF, auto-preload saved AS signature |
| `949994a` | fix: auto-cleanup legacy filament provider in fix_cache |
| `f22c164` | fix: resolve collection array_values in job filter, add @stack('scripts') to layouts, and fix interview signature canvas |
| `9e6e533` | feat: tambahkan import kandidat interview live terminal & searchable dropdown filter job |
| `5648c72` | feat(candidates): differentiate candidate categories and add gender column across tables and apply form |
| `63800fe` | feat(migration): implement legacy sql dump importer, evaluation data service, and dynamic views with pdf export |
| `ba18e39` | feat(cbt): implement online test portal, candidate login, 6-tab profile completion, and tests |
| `d987671` | docs: update progres penyiapan environment PHP 8.3, Composer, dan eksekusi server lokal |
| `8bd86c1` | docs: tambahkan dokumentasi progres update lengkap ke UPDATE_PROGRESS.md |
| `aeafd7a` | Buat tampilan card sambutan dan informasi akun menjadi fullwidth |
| `76bc098` | Sederhanakan halaman Home: hilangkan katalog modul dan statistik, tampilkan kartu sambutan dan identitas profil pengguna |
| `2b58ab7` | Batasi menu Master Data hanya untuk Administrator dan tampilkan modul saja untuk user lain |
| `29e7b4e` | feat(auth): buat prosedur login karyawan inhouse dan ratecard berizin dengan username email dan password default ddmmyyyy tanggal lahir |
| `3772d98` | fix(master-karyawan): perbaiki pergeseran kolom header tabel, default filter karyawan aktif, dan penentuan tipe inhouse vs ratecard sesuai 5 entitas |
| `a49bb8d` | fix: Hapus badge SUB-MENU pada accordion Talent Pool / Rekrutment di sidebar |
| `220f0b3` | feat: Tambahkan Master User Prinsiple dan ganti nama menu Interview menjadi Talent Pool / Rekrutment |
| `9c493ab` | feat: Integrasi Odoo ERP Sync untuk data employee dan setting 5 entitas (AMK, AKP, ATK, ABO, ATB) |
| `25a2b36` | fix: register missing interview.alihkan route in routes/web.php |
| `87ca0e5` | feat: implement public web landing page, employee login form, and standalone public layout for job pages |
| `dc029a0` | feat: implement setting AI, public job portal, job detail, and job apply form with modern design |
| `8188b86` | feat: implement AI candidate ranking and leaderboard module with top 3 podium showcase, tier filtering, and profile integration |
| `43c4157` | feat: implement interview inhouse module (index & detail) with approval signature canvas and full evaluation tabs |
| `e3851cf` | feat: synchronize interview candidate profile with kandidat portal profile layout |
| `081652f` | fix: remove orange download bar and fix Tab 7 User Prinsiple rendering |
| `b3d3cba` | feat: initial commit ASystem - Support System ESA Groups |

---

## 🖥️ Panduan Menjalankan Sistem Secara Lokal

1. **Memulai Server Web**:
   ```bash
   php artisan serve --port=8000
   ```
2. **Akses Dashboard & Fitur**:
   - Halaman Beranda: `http://127.0.0.1:8000/fitur`
   - Portal CBT & Test Online Kandidat: `http://127.0.0.1:8000/cbt/login`
   - Master Karyawan: `http://127.0.0.1:8000/master/karyawan`
   - Master Prinsiple: `http://127.0.0.1:8000/master/prinsiple`
   - Sinkronisasi Odoo: `http://127.0.0.1:8000/odoo-setting`
   - Talent Pool Rekrutmen: `http://127.0.0.1:8000/interview`
   - AI Ranking: `http://127.0.0.1:8000/airanking`
   - Portal Lowongan Publik: `http://127.0.0.1:8000/job`
   - Login Karyawan / Admin: `http://127.0.0.1:8000/login`

---
*Dikembangkan dengan standar modern arsitektur Laravel 12, UI responsif TailwindCSS, dan integrasi ESA Groups.*
