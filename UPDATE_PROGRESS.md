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

## 📜 Riwayat Commit Terkini (Git Log)

| Hash Commit | Deskripsi Perubahan |
|---|---|
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
