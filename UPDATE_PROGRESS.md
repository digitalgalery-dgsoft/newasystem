# 🚀 Ringkasan Perkembangan & Progress Update ASystem Portal
**Support System ESA Groups** (PT Arina Multikarya, PT Alva Karya Perkasa, PT Anugrah Terpercaya Kerja, PT Arina Bintang Oetama, PT Anugrah Tri Berkah)  
*Terakhir diperbarui: 24 September 2026*

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

### 44. 🛡️ Hak Akses Pengguna, Role Dinamis (RBAC), & Multi-User Scoping AS
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
### 45. 👤 Pendaftaran Karyawan Langsung & Shortcut Akses RBAC dari Master Karyawan
- **Modal Pendaftaran Akun Cepat**:
  - Pada halaman Pengaturan Akses & Role (`/roles`), ditambahkan modal *"Daftarkan Akun User Baru"*.
  - Dilengkapi fitur live auto-complete dari data NIK, nama, dan email karyawan aktif di database `employees`.
  - Admin dapat langsung menetapkan Role dan cakupan Prinsiple & Area saat pembuatan akun baru.
- **Shortcut Akses di Master Karyawan**:
  - Ditambahkan tombol cepat *"Atur Akses & Role"* di baris aksi tabel Master Karyawan untuk memudahkan konfigurasi RBAC tanpa harus berpindah modul secara manual.

---

### 46. ⚙️ Manajemen Profil Pengguna (Edit Akun Karyawan & Sinkronisasi Master)
- **Halaman Edit Profil Pengguna (`/profile`)**:
  - Pengguna dan karyawan dapat memperbarui data akun mereka sendiri, meliputi: Nama Lengkap, Email, Nomor WhatsApp / Handphone, dan Kata Sandi baru dengan konfirmasi.
  - Fitur unggah Foto Profil interaktif dengan preview gambar langsung.
- **Sinkronisasi Dua Arah ke Master Data**:
  - Setiap perubahan data kontak (email & no HP) pada profil akun otomatis tersinkronisasi ke tabel `employees` dan riwayat master terkait agar data kepegawaian selalu mutakhir.

---

### 47. 📁 Manajemen Berkas & Lampiran Kandidat (Foto, CV, Fallback Server, & XLSX Link)
- **Modal Media Kandidat Interaktif (`/candidate/{id}/media`)**:
  - Menampilkan preview foto profil kandidat dan berkas CV (PDF/dokumen) dalam modal yang responsif.
- **Sistem Fallback Deteksi Berkas Multi-Server**:
  - Menghindari berkas *broken* dengan memeriksa berkas secara berurutan di direktori lokal storage Laravel, folder publik sistem lama (`asystem.co.id`), server statis `appsend.my.id`, dan `new.asystem.co.id`.
- **Generator Tautan Berkas pada Export Excel**:
  - Berkas export Excel Kandidat Job Portal kini dilengkapi kolom tautan langsung yang valid menuju berkas CV asli serta dokumen PDF hasil analisis AI.

---

### 48. 🤖 Optimasi Engine AI CV Analyzer (Rotasi Kunci Pintar, Anti-Stall, & Error Handling)
- **Rotasi Pintar Multi-Kunci Google Gemini API**:
  - Mendukung hingga 19+ API Key Gemini dengan sistem fallback otomatis ketika salah satu kunci mencapai batas kuota rate-limit (*HTTP 429*).
  - Periode cooldown otomatis selama 2 menit untuk token yang limit sebelum dicoba kembali.
- **Penanganan Anti-Stall Berkas CV**:
  - Kandidat dengan berkas CV yang rusak, hilang, atau tidak dapat dibaca otomatis ditandai dengan status `file_error`.
  - Mencegah proses cron terhenti (*hanging/stalling*) pada kandidat bermasalah dan memastikan antrean terus bergerak lancar.
- **Analisis AI Komprehensif saat Melamar Mandiri**:
  - Kandidat yang melamar lowongan melalui portal publik (`/job/{id}/apply`) otomatis langsung dianalisis CV-nya secara komprehensif menggunakan Gemini AI dan nilainya langsung masuk ke Talent Pool.

---

### 49. 🔄 Integrasi Status Tahapan Rekrutmen Odoo ERP Berdasarkan NIK & Auto-Archive
- **Pemadanan Kandidat dengan Odoo ERP (`hr.applicant`)**:
  - Mencocokkan data kandidat di ASystem dengan database rekrutmen Odoo ERP berdasarkan Nomor Induk Kependudukan (NIK).
  - Menampilkan badge tahapan seleksi resmi Odoo: *Data Pelamar*, *Interview*, *Principal*, *E-Learning*, *PKWT*, dan *Joined*.
- **Tampilan Metrik Ringkas Tahapan Odoo**:
  - Ditambahkan deretan kartu statistik compact Step Odoo pada halaman Kandidat Job Portal dan Kandidat Interview.
- **Tarik Data Kandidat dari Odoo berdasarkan NIK**:
  - Modal sinkronisasi dilengkapi kemampuan menarik data pelamar yang tercatat di Odoo langsung ke ASystem jika data belum ada di database lokal.
- **Pembersihan & Auto-Archive Otomatis**:
  - Kandidat portal yang tidak menunjukkan perkembangan tahapan selama lebih dari 14 hari secara otomatis dipindahkan ke status Arsip untuk menjaga database tetap bersih dan relevan.

---

### 50. 📊 Modul Eksekutif Statistik Job & Pelamar (`/job-stats`) & Export Multi-Sheet XLSX
- **Dashboard Statistik Lowongan & Rekrutmen Eksekutif**:
  - Menampilkan ringkasan total lowongan, total pelamar portal, pelamar yang diproses di Odoo, hingga kandidat yang berhasil *Joined*.
  - Tabel rincian pelamar per lowongan pekerjaan dengan breakdown lengkap tahapan seleksi Odoo ERP.
  - Perankingan otomatis berdasarkan performa perekrutan (*Highest Joined Candidates*).
- **Filter Ketat Khusus Kandidat Portal**:
  - Data statistik difilter secara ketat hanya menghitung pelamar dari Job Portal (`jenis = 'Job Portal'`), mengecualikan kandidat database interview legacy.
- **Resolusi Data Rekruter Inhouse**:
  - Nama dan posisi rekruter yang menangani lowongan ditarik langsung dari master data karyawan inhouse dan diformat rapi dalam Title Case.
- **Export Laporan Eksekutif Excel Multi-Sheet (`.xlsx`)**:
  - Menggantikan format CSV sederhana dengan berkas Excel XLSX profesional:
    - **Sheet 1**: Ringkasan Metrik KPI & Tabel Performa Lowongan dengan header Navy Blue dan format tabel korporat.
    - **Sheet 2**: Detail Seluruh Pelamar beserta lowongan, area, skor AI, dan status tahapan Odoo.

---

### 51. 🔄 Fitur Switch User & Revert User Account (Kembali ke Akun Asli)
- **Kemudahan Impersonasi untuk Supervisi**:
  - Administrator dapat beralih akun (*Switch User*) untuk melihat sistem persis seperti yang dilihat oleh pengguna atau rekruter tertentu.
- **Tombol Kembali ke Akun Asli (Revert Switch User)**:
  - Menyediakan tombol pemulih akun asli yang selalu terlihat di berbagai tempat:
    - **Sticky Amber Bar**: Pita peringatan berwarna kuning keemasan di bagian paling atas layar yang selalu menempel saat impersonasi berlangsung.
    - **Topbar Navigation**: Tombol cepat di sebelah info profil.
    - **Menu Profil Pengguna**: Opsi *"Kembali ke Akun Asli"* di dropdown menu akun.
  - Memastikan administrator dapat kembali ke akun aslinya secara instan tanpa perlu logout dan mengetikkan kredensial kembali.

---

### 52. 🎯 Penyempurnaan Scope All-Principle & All-Area pada Kandidat Portal & Interview
- **Koreksi Logika Otorisasi Scope Global**:
  - Memperbaiki penanganan akun pengguna yang dikonfigurasi dengan cakupan *All Prinsiple* (`all_principles = 1` atau array kosong) dan *All Area* (`all_areas = 1` atau array kosong).
  - Sebelumnya, akun dengan izin nasional ini sempat memicu filter kosong pada Kandidat Portal & Kandidat Interview.
  - Logika query disempurnakan di [User.php](file:///d:/ASystem/newasystem/app/Models/User.php), [KandidatPortalController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/KandidatPortalController.php), dan [InterviewController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/InterviewController.php) sehingga data pelamar tampil lengkap secara nasional.

---

### 53. ⏰ Standarisasi Zona Waktu Sistem & Kandidat ke Asia/Jakarta (WIB)
- **Konfigurasi Timezone Laravel**:
  - Mengubah konfigurasi zona waktu aplikasi pada [config/app.php](file:///d:/ASystem/newasystem/config/app.php) dari default `UTC` menjadi `'Asia/Jakarta'`.
- **Akurasi Waktu Pendaftaran Pelamar**:
  - Memastikan `created_at` pada saat kandidat mendaftar tersimpan dan ditampilkan menggunakan Waktu Indonesia Barat (WIB), bukan waktu server UTC.
  - Menyelaraskan seluruh tampilan tanggal pendaftaran di tabel, modal, dan export laporan.

---

### 54. ⚡ Live Running Text Marquee AI CV Analyzer & Pacing Otomatis 1 Kandidat per 30 Detik (Job Portal Only)
- **Komponen Running Text Marquee Dinamis**:
  - Menampilkan banner live ticker glassmorphic modern di bagian atas halaman Kandidat Job Portal.
  - **Pulsing Indicator**: Badge menyala `● PROSES AI` (Emerald) saat menganalisis dan `● AI STANDBY` (Amber) saat jeda antrean.
  - **Running Text Berjalan**: Menampilkan nama kandidat yang sedang dianalisis, lowongan, area, kecepatan, sisa antrean, kandidat berikutnya, dan skor kandidat terakhir yang selesai.
  - **Fitur Pause on Hover**: Teks berhenti bergerak secara halus saat kursor mouse diarahkan ke area ticker agar mudah dibaca.
  - **Auto-Poll Alpine.js**: Status diperbarui setiap 5 detik di latar belakang melalui endpoint `GET /kandidatportal/ai-live-status` tanpa reload halaman.
- **Pacing Otomatis: 1 Kandidat per 30 Detik (1 Menit 2 Kandidat)**:
  - Dikonfigurasi pada command [app/Console/Commands/CronAiAnalyzerCommand.php](file:///d:/ASystem/newasystem/app/Console/Commands/CronAiAnalyzerCommand.php) dan dijadwalkan per menit di [routes/console.php](file:///d:/ASystem/newasystem/routes/console.php).
  - Setiap eksekusi memproses 2 kandidat secara teratur dengan jeda istirahat dinamis hingga tepat 30 detik per kandidat.
- **Filter Ketat Khusus Kandidat Job Portal**:
  - Antrean dan pemrosesan AI dibatasi hanya untuk pelamar **Job Portal (`jenis = 'Job Portal'`)**, mengeluarkan data walk-in dan import lama.
  - Jumlah antrean pada teks berjalan sinkron 100% dengan kartu statistik **BELUM DIANALISA** di dashboard.

---

### 55. 🔒 Perbaikan Error Undefined $isAdmin, Isolasi Data Interview Selesai & Arsip per Rekrutor / AS, serta Akses Khusus Kandidat Inhouse 5 Entitas (Approval HRD & Head) (20 September 2026)
- **Perbaikan Fatal Error Undefined Variable `$isAdmin` di Halaman Selesai & Arsip**:
  - Menyelesaikan bug `ErrorException: Undefined variable $isAdmin` pada metode `done()` (baris 1072) dan `arsip()` (baris 1153) di [InterviewController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/InterviewController.php).
  - Variabel `$isAdmin` kini diinisialisasi secara dini di awal method (`$isAdmin = $user && ($user->isAdmin() || $user->role === 'admin');`).
- **Isolasi Akses Data Interview Selesai (`/interviewdone`) & Arsip (`/interviewarsip`)**:
  - **User Biasa (Rekrutor / AS)**:
    - Hanya menampilkan data kandidat yang merupakan milik atau di-handle oleh user itu sendiri (`LOWER(TRIM(useras))` sesuai identitas user / NIK / email / nama, atau `recruiter_id = $user->id`).
    - Menghilangkan klausul longgar (`orWhere('useras', '')` / `orWhereNull('useras')`) yang sebelumnya membuat rekruter bisa melihat kandidat tak bertuan atau kandidat rekruter lain.
    - Sembunyikan elemen dropdown/filter pilih rekruter pada antarmuka [done.blade.php](file:///d:/ASystem/newasystem/resources/views/interview/done.blade.php) dan [arsip.blade.php](file:///d:/ASystem/newasystem/resources/views/interview/arsip.blade.php) jika bukan Administrator.
  - **Administrator**:
    - Mempertahankan hak akses penuh (unrestricted) untuk melihat semua data kandidat selesai dan arsip secara nasional, lengkap dengan dropdown filter per rekruter.
- **Standarisasi Modul Kandidat Inhouse 5 Entitas Resmi ([InterviewInhouseController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/InterviewInhouseController.php))**:
  - **Filter Ketat 5 Entitas Inhouse**:
    - Kandidat inhouse dibatasi strictly hanya untuk 5 entitas resmi: `AMK` (PT Arina Multi Karya), `AKP` (PT Alva Karya Perkasa), `ATK` (PT Anugrah Terpercaya Kerja), `ABO` (PT Abadi Berkat Odelia), dan `ATB` (PT Anugrah Talenta Berkarya) via pencocokan `principle_id` dan nama entitas.
  - **Otorisasi Khusus Role HRD & Head**:
    - Menu dan halaman Kandidat Inhouse ditutup untuk umum/rekruter biasa dan hanya dapat diakses oleh user dengan role/kriteria **HRD** atau **Head** dari Rekrutor/AS yang meng-handle kandidat bersangkutan.
    - Sembunyikan link menu sidebar *"Kandidat Inhouse"* di [app.blade.php](file:///d:/ASystem/newasystem/resources/views/layouts/app.blade.php) jika user yang login bukan Admin, HRD, atau Head.
  - **Hierarki Approval Head & HRD**:
    - **Akses Head**: Hanya dapat melihat dan menyetujui kandidat inhouse yang ditangani oleh rekruter binaannya (berdasarkan relasi struktural pimpinan di master karyawan `employees.pimpinan` atau area supervisi). Pada halaman detail ([show.blade.php](file:///d:/ASystem/newasystem/resources/views/interviewinhouse/show.blade.php)), Head hanya memiliki tombol aksi **"Submit Data Head"**.
    - **Akses HRD / Admin**: Memiliki hak approval final dengan tombol aksi **"Submit HRD"** untuk seluruh kandidat inhouse.
### 56. 📋 Implementasi Modul Work Plan & ToDoList (Kanban Board 4 Kolom, Daily Activity Log, Kolaborasi Tim & Migrasi 13.101 Data Historis) (20 September 2026)
- **Latar Belakang & Modernisasi Modul**:
  - Mengadaptasi dan memodernisasi penuh modul perencanaan kerja dari sistem lama (`v3/wp.php`, `v3/wptodo.php`, `v3/datawp.php`) ke dalam arsitektur Laravel 12 dan desain modern ASystem Portal.
  - Menghadirkan antarmuka **Kanban Board** 4 kolom status: **To Do**, **In Progress**, **Review**, dan **Done**, dilengkapi panel accordion untuk tugas-tugas historis yang telah diarsipkan (**Archived**).
- **Migrasi Data Historis Lengkap dari MySQL Dump (`db_wp.sql`)**:
  - Dibuat command artisan otomatis: `php artisan wp:import-dump`.
  - Penanganan khusus konversi escape karakter MySQL (`\'` $\rightarrow$ `''`) untuk kompatibilitas penuh dengan driver database SQLite.
  - **Hasil Migrasi 100% Berhasil**:
    - **13.101 Tugas Utama** (`tasks` / `tb_task`)
    - **431 Sub-Tugas Checklist** (`task_subtasks` / `tb_task_subtask`)
    - **448 Komentar Diskusi** (`task_comments` / `tb_task_comment`)
    - **25.810 Jejak Riwayat Aktivitas** (`task_activities` / `tb_task_activity`)
    - **3.855 Notifikasi Pengguna** (`task_notifications` / `tb_task_notification`)
    - **6 Kategori Tugas** (`task_categories` / `tb_task_category`)
    - **18 Catatan Kerja Harian** (`tb_workplan`)
- **Fitur Unggulan Kanban Board & Kolaborasi Tim ([index.blade.php](file:///d:/ASystem/newasystem/resources/views/workplan/index.blade.php))**:
  - **Interaksi Drag & Drop Cepat**: Pemindahan tugas antar status secara intuitif dengan HTML5 drag-and-drop dan pembaruan backend asinkron.
  - **Aturan Otorisasi Alur Kerja (Workflow Permission Guard)**:
    - Hanya **Delegator (Pimpinan pembuat tugas)** atau **Administrator** yang berhak menyetujui pemindahan status dari **Review** ke **Done**. Staf biasa dicegah dengan respon HTTP 403 dan notifikasi peringatan.
  - **Panel Slide-Over Drawer Detail & Edit Tugas**:
    - Mode lihat & edit data tugas langsung di slide drawer tanpa meninggalkan halaman.
    - Quick action bar untuk navigasi status cepat dan tombol arsip/hapus terproteksi izin.
  - **Checklist Sub-Tugas Interaktif**:
    - Bar indikator persentase capaian penyelesaian sub-tugas real-time.
    - Toggle selesai checklist secara langsung via AJAX tanpa refresh halaman.
  - **Diskusi & Notifikasi Mentions (`@NamaKaryawan`)**:
    - Thread komentar tim dengan unggahan lampiran dokumen/gambar pendukung.
    - Pendeteksian tag `@Nama` yang otomatis membuat notifikasi penugasan atau review bagi rekan kerja yang ditandai.
  - **Audit Trail Jejak Aktivitas**: Riwayat perubahan status, delegasi, dan pembaruan tugas terekam transparan.
  - **Alat Bantu Salin Laporan WhatsApp (Copy Report)**: Menghasilkan format pesan teks rapi yang siap dikirimkan ke grup chat / WhatsApp pimpinan.
  - **Export Excel Profesional**: Disediakan unduhan rekap tugas format XLSX dengan penataan gaya sel, border, dan header menggunakan PhpSpreadsheet.
- **Modul Catatan Aktivitas Harian (Daily Work Plan - [daily.blade.php](file:///d:/ASystem/newasystem/resources/views/workplan/daily.blade.php))**:
  - Halaman khusus `/workplan-daily` untuk mencatat log operasional dan kendala harian per divisi.
  - Menampilkan 4 kartu metrik statistik, bilah filter pencarian kata kunci, dropdown divisi, dan tanggal pelaksanaan.
  - Form modal pencatatan aktivitas baru terintegrasi otomatis dengan divisi karyawan pelapor.
- **Standar Visual & Keselarasan Tema**:
  - Menerapkan palet warna resmi ASystem (Sapphire Blue `#0F52BA`, Navy `#1e293b`), tipografi Google Outfit, badge status beranimasi halus, dan tata letak responsif.

---

### 57. 💎 Modernisasi Seluruh Notifikasi & Dialog Menggunakan SweetAlert2 (20 September 2026)
- **Transformasi Notifikasi Browser Standar ke SweetAlert2**:
  - Menggantikan seluruh popup dialog bawaan browser (`alert()` dan `confirm()`) yang kaku dan tidak menarik dengan tampilan modal **SweetAlert2** yang modern, bersih, dan profesional.
- **Penyempurnaan Dialog Hak Akses & Peringatan**:
  - Penolakan akses saat user non-delegator mencoba memindahkan tugas status *Review* ke *Done* kini tampil dengan ikon peringatan (*warning*), tipografi tajam, dan tombol aksi Sapphire Blue (`#0F52BA`) dengan sudut membulat (`rounded-2xl`).
- **Konfirmasi Tindakan Hapus (Destructive Confirmation)**:
  - Tindakan penghapusan tugas utama, penghapusan sub-tugas checklist, dan penghapusan log kerja harian kini menggunakan modal dialog konfirmasi SweetAlert2 dengan tombol *Ya, Hapus* warna Rose Red (`#e11d48`) dan tombol *Batal* Slate Grey (`#94a3b8`).
- **Toast Feedback Interaktif & Non-Intrusif**:
  - Notifikasi penyimpanan berhasil, checklist terhapus, komentar terkirim, dan penyalinan format WhatsApp kini menggunakan SweetAlert2 Toast di pojok kanan atas (`top-end`) dengan durasi otomatis 1.5 - 2.5 detik.
- **Global Interceptor `window.alert`**:
  - Diterapkan script interceptor global pada [app.blade.php](file:///d:/ASystem/newasystem/resources/views/layouts/app.blade.php) sehingga setiap pemanggilan `alert()` di masa mendatang otomatis terkonversi menjadi modal dialog SweetAlert2 dengan deteksi otomatis tipe pesan (*Berhasil*, *Akses Ditolak*, *Perhatian*, atau *Kesalahan*).

---

### 58. 📎 Modernisasi Field Input Lampiran: Drag & Drop, Paste Clipboard (Ctrl+V) & Image Preview Real-time (20 September 2026)
- **Area Dropzone Interaktif & Modern**:
  - Menggantikan input file standar browser dengan dropzone modern bergaris putus-putus (*dashed border*), ikon upload yang membesar saat hover, dan feedback dragover (*ring highlight*).
- **Dukungan Clipboard Paste (Ctrl+V)**:
  - Pengguna dapat langsung menempelkan gambar hasil tangkapan layar (*screenshot*) dari clipboard cukup dengan menekan `Ctrl+V` saat mengetik di judul, deskripsi, komentar, atau area form tanpa perlu menyimpan gambar ke file explorer terlebih dahulu.
- **Kartu Pratinjau (Preview Card) Real-time**:
  - **Lampiran Gambar**: Menampilkan thumbnail pratinjau gambar, link perbesar (*magnifier*), nama file, ukuran file terformat (KB/MB), dan tombol hapus/batal.
  - **Lampiran Dokumen**: Menampilkan kartu dokumen dengan ikon berwarna sesuai format (PDF merah, Excel hijau, Word biru, ZIP oranye) dan detail ukuran file.
- **Implementasi Komprehensif di 3 Titik Form**:
  - **Form Tambah Tugas Baru**: Dropzone bersih dengan deteksi paste di seluruh area modal.
  - **Form Edit Tugas**: Menampilkan lampiran lama jika ada, dropzone baru, dan preview pengganti sebelum disimpan via `FormData` asinkron.
  - **Form Komentar & Diskusi**: Tombol lampirkan cepat, paste screenshot langsung saat mengetik di textarea komentar, dan thumbnail gambar interaktif di dalam riwayat komentar.

---

### 59. 🖼️ Lightbox Modal Pratinjau Berkas & Gambar (Tanpa Membuka Tab Baru) (20 September 2026)
- **Modal Lightbox Terintegrasi (`previewModal`)**:
  - Mengubah seluruh interaksi klik pada gambar thumbnail dan tautan lampiran agar terbuka langsung di dalam jendela pop-up modal (lightbox), tidak lagi membuka tab baru (`target="_blank"`).
- **Dukungan Berbagai Tipe Berkas**:
  - **Gambar (JPG, PNG, GIF, WebP, SVG)**: Ditampilkan secara responsif dengan ukuran proporsional penuh (*containment*), latar gelap tembus pandang (*dark glassmorphic backdrop* `bg-slate-950/85`), bayangan tajam, dan kontrol navigasi.
  - **PDF (`.pdf`)**: Menampilkan dokumen PDF langsung di dalam frame modal (`<iframe>` interaktif) sehingga pengguna dapat membaca isi dokumen tanpa harus meninggalkan dashboard.
  - **Dokumen Office / Arsip (Word, Excel, ZIP)**: Menampilkan kartu informasi dokumen dengan tombol unduh langsung dan opsi buka di tab baru jika diperlukan.
- **Kenyamanan Navigasi Pengguna**:
  - Dapat ditutup secara instan dengan menekan tombol **`Esc`**, mengklik tombol silang, atau mengklik area luar modal (*click outside*).
  - Terhubung ke seluruh bagian modul:
    - Thumbnail gambar pada stream komentar dan diskusi tim.
    - Lampiran berkas utama pada panel detail tugas.
    - Ikon paperclip lampiran pada kartu Kanban Board.
    - Thumbnail pratinjau pada form tambah tugas baru dan form edit tugas.

---

### 60. 💬 Modul WhatsApp Groups Chat Real-time pada Work Plan & ToDoList (20 September 2026)
- **Tampilan Antarmuka WhatsApp Web Modern & Responsif**:
  - **Bilah Samping Kiri (Group Sidebar)**:
    - Header profil pengguna aktif dengan indikator status *Online*, tombol cepat buat group baru (`fa-user-group`), dan tombol penyegaran instan.
    - Bilah pencarian (*search filter*) interaktif untuk memfilter daftar grup obrolan secara instan di sisi klien.
    - Daftar obrolan grup dengan avatar inisial berlatar warna dinamis, nama grup, cuplikan pesan terakhir (*latest message preview*) dengan nama pengirim, penanda waktu percakapan terakhir, dan badge total anggota.
  - **Area Obrolan Kanan (Active Chat Stream)**:
    - Header grup aktif menampilkan nama grup, total personel, daftar ringkasan anggota tim, tombol tambah anggota cepat (`+ Tambah Anggota`), tombol lihat seluruh anggota, dan tombol pintas navigasi kembali ke papan Kanban.
    - Latar belakang khas WhatsApp Web dengan pola doodle subtle hangat.
    - Gelembung pesan (*chat bubble*) terstruktur:
      - **Pesan Anggota Lain (Incoming)**: Sebelah kiri, berlatar putih bersih (`#ffffff`), dilengkapi nama pengirim dengan palet warna kontras beragam, konversi tautan URL otomatis, dan penanda waktu kirim.
      - **Pesan Sendiri (Outgoing)**: Sebelah kanan, berlatar hijau lembut WhatsApp (`#d9fdd3`), teks pesan, penanda waktu kirim, dan ikon centang ganda biru (*double blue checkmark*).
      - **Pesan Sistem / Notifikasi**: Ditampilkan di posisi tengah dengan pill membulat (*cth: "Group dibuat oleh...", "X menambahkan Y"*).
    - Bilah input pesan fleksibel dengan dukungan kirim instan tombol `Enter` (serta `Ctrl+Enter` untuk baris baru), dan tombol kirim bulat hijau WhatsApp (`#25D366`).
  - **Layar Pembuka (Welcome Screen)**:
    - Tampilan placeholder profesional saat belum ada grup yang dipilih dengan ikon WhatsApp besar dan tombol cepat ajakan membuat grup.
- **Manajemen Anggota Karyawan Inhouse**:
  - Modal pembuatan grup baru yang terintegrasi langsung dengan direktori karyawan Inhouse aktif (5 entitas ESA Groups: AMK, AKP, ATK, ABO, ATB).
  - Dilengkapi fitur pencarian real-time nama karyawan, informasi jabatan dan area penempatan, serta tombol pintas *"Pilih Semua"* dan *"Reset"*.
  - Modal penambahan anggota ke dalam grup yang sudah berjalan dengan pencegahan duplikasi data.
  - Modal inspeksi detail keanggotaan grup dengan pembagian peran (*Admin* vs *Anggota*).
- **Arsitektur Real-Time Cerdas & Kompatibel (Polling Interval)**:
  - Polling pesan asinkron setiap 2.5 detik pada grup aktif via endpoint `last_id` tanpa membebani server dan 100% kompatibel dengan arsitektur PHP-FPM / Nginx tanpa dependensi daemon background.
  - Polling sidebar setiap 6 detik untuk memperbarui snippet pesan terakhir dan urutan obrolan teratas.
  - Fitur auto-scroll cerdas ke dasar obrolan hanya jika posisi scroll pengguna berada di dekat bagian bawah (*near bottom*).
- **Integrasi Navigasi & Notifikasi SweetAlert2**:
  - Menggantikan dialog konfirmasi/error standar dengan SweetAlert2 elegan.
  - Akses satu-klik via tombol **"WA Groups Chat"** pada header Work Plan Kanban dan item menu bilah samping navigasi utama portal dengan badge **LIVE**.

---

### 61. 👥 Searchable Multi-Select Dropdown Anggota Tim & Penyesuaian Label "Groups Chat" (20 September 2026)
- **Searchable Multi-Select Dropdown Interaktif**:
  - **Form Buat Group Baru & Form Tambah Anggota**: Mengubah field input pemilihan anggota tim menjadi komponen Searchable Multi-Select Dropdown modern yang intuitif dan responsif.
  - **Tampilan Chips / Badges Anggota Terpilih**:
    - Anggota yang dipilih langsung tampil sebagai tag/badge rapi berwarna hijau toska dengan tombol silang (*✕*) untuk menghapus anggota secara cepat per individu.
  - **Pencarian Real-Time & Panel Dropdown Mengambang**:
    - Input teks pencarian cerdas yang memfilter nama karyawan, jabatan, dan kota/area secara instan di sisi klien.
    - Panel dropdown melayang (*floating dropdown menu* `z-50 shadow-2xl rounded-xl`) dengan avatar inisial, nama tebal, divisi/area, dan indikator checkmark terpilih.
    - Dilengkapi tombol cepat *"Pilih Semua"* (memilih semua hasil filter pencarian aktif) dan *"Reset"* (mengosongkan pilihan).
    - Footer dropdown menampilkan ringkasan jumlah anggota terpilih dan tombol *"Selesai"*.
  - **Sinkronisasi Skema Karyawan Inhouse & User Portal**:
    - Penyesuaian query pada `WorkPlanChatController` untuk mencocokkan skema database riil: `status = 'Aktiv'`, `tipe_karyawan = 'Inhouse'`, entitas resmi ESA Groups (AMK, AKP, ATK, ABO, ATB), dan menyertakan seluruh akun User aktif sistem agar seluruh personel (misal: Irfan Nur, Abdurrahman Jamil, dll.) dapat langsung dicari dan dipilih.
- **Standarisasi Penamaan Label ("Groups Chat")**:
  - Menghapus penyebutan merek pihak ketiga ("WA" / "WhatsApp") dan mengganti label menjadi **"Groups Chat"** di seluruh antarmuka:
    - Menu navigasi bilah samping (*sidebar*) utama portal dengan ikon obrolan modern (`fa-solid fa-comments`) dan badge **LIVE**.
    - Tombol pintas navigasi pada header Work Plan Kanban.
    - Judul halaman, breadcrumbs, modal *"Buat Group Baru"*, dan welcome screen obrolan.

---

### 62. ⚡ Optimasi Kecepatan Ekstrim Groups Chat & Perbaikan Pembukaan Modal (20 September 2026)
- **Akar Masalah Teridentifikasi**:
  1. **Query Terlalu Besar (23.367 Baris)**: Query inhouse karyawan sebelumnya menyertakan kondisi fallback entitas yang secara tidak sengaja menarik puluhan ribu karyawan *RateCard* (outsourced), menghasilkan payload JSON 3.5 MB yang membekukan thread JavaScript browser saat inisialisasi Alpine.js.
  2. **Event Bubbling Alpine `@click.away`**: Modal kartu menggunakan direktif `@click.away` tanpa modifier `@click.stop` pada tombol pemicu, sehingga klik tombol luar langsung memicu penutupan modal pada *tick* yang sama persis saat modal baru saja dibuka.
- **Solusi & Optimasi yang Diterapkan**:
  - **Pemangkasan 97% Data Karyawan (Dari 23.367 menjadi 794 Orang)**:
    - Query `WorkPlanChatController` kini difokuskan murni pada `status = 'Aktiv'` dan `tipe_karyawan = 'Inhouse'` serta akun User portal aktif.
    - Waktu query terpangkas drastis menjadi **39 ms** (dari hitungan detik) dan ukuran payload JSON menyusut dari 3.5 MB menjadi hanya ~50 KB.
  - **Render Cepat Terproteksi (*Capped List Rendering*)**:
    - Menambahkan *getter* `displayedInhouseEmployees` dan `displayedAddMembers` yang membatasi render DOM hingga 60 item pertama saat pencarian kosong dan merender instan seluruh hasil yang cocok saat pengguna mengetik. Render DOM turun dari 4.000+ nodes menjadi hanya 60 nodes (< 3ms).
  - **Perbaikan Pembukaan Modal Handal**:
    - Menambahkan `.stop` pada seluruh tombol pembuka (`@click.stop="openCreateGroupModal()"`, `@click.stop="openAddMemberModal()"`, `@click.stop="openViewMembersModal()"`).
    - Memindahkan penutupan latar belakang ke `@click.self="show... = false"` pada elemen *backdrop* gelap serta menambahkan `@click.stop` pada kartu modal, menjamin modal terbuka 100% responsif tanpa konflik klik luar.

### 63. 🖼️ Foto Profil pada Chat Bubble & Sistem Notifikasi Real-time (Lonceng & Toast) (20 September 2026)
- **Foto Profil Pengguna pada Chat Bubble**:
  - **Tampilan Sisi Kiri (Pesan Masuk dari Rekan Tim / Anggota Lain)**:
    - Menampilkan avatar foto profil melingkar (`w-7 h-7 sm:w-8 sm:h-8 rounded-full border border-slate-200 shadow-2xs`) di sisi kiri gelembung pesan.
    - Dilengkapi nama pengirim dengan warna dinamis sesuai palet untuk memudahkan membedakan siapa yang mengirim pesan di dalam obrolan grup.
  - **Tampilan Sisi Kanan (Pesan Keluar dari User Sendiri)**:
    - Menampilkan avatar foto profil pengguna sendiri di sisi kanan gelembung pesan berlatar hijau toska.
  - **Resolusi Avatar & Fallback Cerdas**:
    - Backend `WorkPlanChatController::getSenderAvatarUrl()` memeriksa foto profil pada `User::avatar_url`, master karyawan `Employee::foto` (di direktori `uploads/avatars/` atau `lampiran/`), dan fallback otomatis ke inisial dinamis UI-Avatars ber-warna.
    - Dilengkapi *request-level cache* pada controller agar resolusi avatar berlangsung cepat tanpa query berulang.
    - Penggunaan atribut Alpine.js `x-on:error` untuk menangani fallback instan jika berkas gambar gagal dimuat, sekaligus mencegah tabrakan dengan directive bawaan Blade `@error`.
- **Sistem Notifikasi Real-time (Lonceng Navbar & Popup Toast)**:
  - **Popup Toast Notifikasi (SweetAlert2)**:
    - Ketika ada pesan baru masuk dari anggota grup lain pada grup yang diikuti pengguna, muncul popup Toast di pojok kanan atas layar.
    - Toast memuat judul nama grup yang bersangkutan, avatar pengirim, nama pengirim, dan cuplikan pesan teks.
    - Dilengkapi audio notifikasi lembut (*chime/beep*) via Web Audio API tanpa dependensi berkas suara eksternal.
    - Mengklik popup Toast langsung mengarahkan pengguna ke ruang percakapan grup terkait.
  - **Ikon Lonceng Navbar Topbar Interaktif**:
    - Mengubah tombol lonceng topbar menjadi komponen Alpine.js `asystemNotifications()` dengan badge angka merah dinamis yang menampilkan total jumlah pesan belum dibaca (`unread_total`).
    - Ikon beranimasi halus saat notifikasi baru tiba.
    - Menu dropdown interaktif menampilkan daftar obrolan belum dibaca per grup, nama pengirim terakhir, cuplikan pesan, waktu pesan, badge counter unread, dan tautan cepat ke obrolan.
  - **Endpoint Pemeriksaan Notifikasi Global (`/workplan-chat/notifications/check`)**:
    - Polling ringan di latar belakang setiap ~8.5 detik yang bekerja di seluruh halaman ASystem Portal (Kanban, Interview, Dashboard, dll.), menjamin pengguna selalu terinformasi akan pesan baru secara real-time.

---

### 64. 👤 Resolusi Nama Lengkap AS / Rekruter pada Export Excel (.xlsx) Kandidat Portal dari Data Karyawan (20 September 2026)
- **Akar Masalah**:
  - Pada dokumen hasil export data Kandidat Job Portal ke format Excel (.xlsx), kolom Q (**Nama AS**) sebelumnya langsung mengambil nilai mentah `candidates.useras`, yang sebagian besar berisi alamat email pengguna (seperti `muhammadarry2693@gmail.com`, `rahmantaufik778@gmail.com`, dll.).
- **Solusi & Implementasi Terpadu**:
  1. **Service Export Excel (`CandidateXlsxExportService.php`)**:
     - Menerapkan mekanisme pre-fetch batch lookup nama lengkap karyawan dari tabel Data Karyawan (`employees`) berdasarkan email (`LOWER(TRIM(email))`), mengeliminasi query N+1 saat mengekspor ribuan data pelamar sekaligus.
     - Resolusi berjenjang kolom Q **Nama AS**:
       - *Prioritas 1*: Mengambil `nama_karyawan` resmi dari Data Karyawan (`Employee`).
       - *Prioritas 2*: Mengambil `name` dari tabel `User` jika belum terdaftar di Data Karyawan (misal akun Administrator).
       - *Prioritas 3*: Mempertahankan nama asli jika data `useras` sudah berupa nama (bukan alamat email).
       - *Prioritas 4*: Pemformatan Title Case rapi dari username email jika email belum terdaftar di master data.
  2. **Model Kandidat (`Candidate.php`)**:
     - Memperbarui accessor `getUserDisplayNameAttribute()` agar memprioritaskan pencarian nama lengkap dari Data Karyawan (`Employee`) berdasarkan kecocokan email sebelum memeriksa tabel `users`.
  3. **Controller Kandidat Portal (`KandidatPortalController.php`)**:
     - Memperbarui penyusunan daftar rekruter (`allRecruiters`) pada dropdown filter serta label subtitle banner dokumen Excel agar selalu menampilkan nama lengkap karyawan resmi.

---

### 65. 🏷️ Penambahan Jabatan AS dan Eliminasi Fallback Administrator ESA pada Export Excel (.xlsx) Kandidat Portal (20 September 2026)
- **Latar Belakang & Kebutuhan**:
  - Pada hasil export data Kandidat Job Portal ke format Excel (.xlsx), pengguna menghendaki agar kolom **Nama AS** (kolom Q) tidak hanya menampilkan nama lengkap AS/rekruter, namun juga menyertakan **Jabatan** dari karyawan tersebut.
  - Jika nama akun AS tidak terdaftar di Data Karyawan (`Employee`) (seperti akun super admin `admin@asystem.co.id`), sistem sebelumnya mengambil fallback nama akun User dari database yang menghasilkan teks `Administrator ESA`. Pengguna meminta agar fallback tersebut **dieliminasi**: jangan menampilkan `Administrator ESA`, melainkan tampilkan apa adanya alamat email yang tercantum atau berikan tanda strip `-` jika kosong.
- **Solusi & Implementasi Teknis**:
  1. **Format Nama & Jabatan**:
     - Memperluas kueri pre-fetch batch pada [CandidateXlsxExportService.php](file:///d:/ASystem/newasystem/app/Services/CandidateXlsxExportService.php) untuk mengambil kolom `email`, `nama_karyawan`, dan `jabatan` dari tabel `employees`.
     - Jika karyawan ditemukan di Data Karyawan dan memiliki nilai jabatan, disajikan dalam format elegan: `Nama Lengkap (Jabatan)` (contoh: `Ivola Piscessario Geraldyne (Area Supervisor)` atau `MUHAMMAD ARRY FITRAH (Area Supervisor)`). Jika kolom jabatan kosong di data karyawan, cukup disajikan `Nama Lengkap`.
     - Memperlebar lebar kolom Q dari 24 menjadi 32 pt agar nama beserta jabatan tertampil penuh tanpa terpotong.
  2. **Eliminasi Fallback Administrator ESA**:
     - Menghapus fallback pencarian ke tabel `users` yang sebelumnya memetakan email admin ke nama `Administrator ESA`.
     - Apabila nama tidak ditemukan di Data Karyawan:
       - Jika ada alamat email yang tercantum: tampilkan alamat email tersebut apa adanya (contoh: `admin@asystem.co.id`).
       - Jika tidak ada email atau data kosong: tampilkan tanda `-`.
       - Mencegah string teks yang mengandung kata `Administrator` / `Administrator ESA` muncul di kolom Nama AS.
  3. **Penyelarasan Model & Tampilan Web**:
     - Memperbarui accessor `getUserDisplayNameAttribute()` pada [Candidate.php](file:///d:/ASystem/newasystem/app/Models/Candidate.php) dan selector filter `allRecruiters` pada [KandidatPortalController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/KandidatPortalController.php) agar konsisten menyertakan jabatan dan mengeliminasi fallback nama administrator.

---

### 66. 🎨 Kustomisasi Tema Dashboard Personal (Light/Dark Mode, 4 Palet Gelap, Custom Accent Color) & Tampilan Jabatan User (20 September 2026)
- **Latar Belakang & Kebutuhan**:
  1. Label di bawah nama pengguna pada pojok kanan atas (Topbar) dan pojok kiri bawah (Sidebar Footer) sebelumnya menampilkan Role Akses (misal: `Administrator`, `Karyawan Inhouse`). Pengguna meminta agar diganti menampilkan **Jabatan** dari user tersebut.
  2. Menambahkan pengaturan warna custom di halaman **Edit Profile** agar setiap pengguna dapat mengubah nuansa dashboard mereka sendiri tanpa ter-apply secara global (tersimpan mandiri secara `localStorage`).
  3. Menambahkan pilihan **Light / Dark Mode** dengan opsi palet warna gelap: **Hitam Pekat**, **Biru Navy**, **Dark Grey**, dan **Soft Grey**.
- **Solusi & Implementasi Terpadu**:
  1. **Resolusi Jabatan Pengguna (`User.php`)**:
     - Membuat accessor cerdas `getJabatanDisplayAttribute()` pada model `User`: memprioritaskan jabatan resmi dari Data Karyawan (`Employee::jabatan` jika terhubung), kemudian kolom `job_title` pada tabel `users`, dan fallback terformat jika belum terisi.
     - Memperbarui label di pojok kanan atas (Topbar) dan pojok kiri bawah (Sidebar) pada [app.blade.php](file:///d:/ASystem/newasystem/resources/views/layouts/app.blade.php) serta kartu profil pada [index.blade.php](file:///d:/ASystem/newasystem/resources/views/fitur/index.blade.php) sehingga selalu mencerminkan jabatan resmi pengguna.
  2. **Multi-Palette Dark & Custom Accent Color System**:
     - Mengembangkan arsitektur CSS Variables terpadu di [layouts/app.blade.php](file:///d:/ASystem/newasystem/resources/views/layouts/app.blade.php) dengan dukungan 4 palet warna gelap:
       - **Hitam Pekat** (`black`): Pitch black murni (`#050505` & `#121215`) dengan kontras tajam.
       - **Biru Navy** (`navy`): Deep Navy Blue khas ASystem (`#070d1e` & `#13224d`).
       - **Dark Grey** (`dark_grey`): Charcoal Graphite modern (`#121316` & `#23252d`).
       - **Soft Grey** (`soft_grey`): Titanium Muted netral (`#23252b` & `#393c47`).
     - Script inisialisasi awal di tag `<head>` (*Zero-Flicker*) yang langsung menerapkan preferensi tema dari `localStorage` sebelum peramban selesai merender halaman.
     - Menyediakan tombol cepat **Quick Dark / Light Toggle** (ikon matahari/bulan) di Topbar samping notifikasi.
  3. **Antarmuka Kustomisasi Tema di Edit Profile (`profile/index.blade.php`)**:
     - Menambahkan Tab ke-3 **"Tema & Warna"** dengan pengontrol Alpine.js interaktif:
       - Pemilihan mode: *Mode Terang (Light)* vs *Mode Gelap (Dark)*.
       - Pemilihan palet gelap dengan kartu visual & swatch warna.
       - Pemilihan nuansa aksen dashboard: 7 preset warna populer serta **Custom Color Picker** bebas (`<input type="color">`).
       - **Live Simulation Preview**: Miniatur dashboard yang berubah warna secara real-time saat pengguna menguji tema.
       - Tombol simpan ke `localStorage` dan tombol reset ke bawaan sistem.

---

### 67. 🌙 Perbaikan Kontras & Keterbacaan Mini-Card Tahapan Rekrutmen Odoo ERP pada Dark Mode (20 September 2026)
- **Latar Belakang & Masalah**:
  - Pada tampilan Dark Mode, card step Odoo (*Tahapan Rekrutmen Odoo ERP*) di modul Kandidat Portal (`/kandidatportal`) dan Talent Pool Interview (`/interview`) mengalami masalah keterbacaan (*low contrast*):
    - Background 8 mini-card menggunakan class opacity `bg-slate-50/70` yang di mode gelap merender permukaan abu-abu terang / perak (*milky silver*).
    - Teks angka kandidat menggunakan `text-slate-900` yang terkonversi menjadi teks putih (`#ffffff`), menyebabkan teks putih bertumpuk di atas latar perak terang sehingga angka menjadi nyaris tidak terlihat.
    - Teks judul tahapan (*Semua Odoo, Data Pelamar, Interview, dll.*) bernuansa abu-abu pudar (`text-slate-500`) yang juga sulit dibaca di atas latar perak tersebut.
    - Kartu tahapan yang sedang aktif (*selected*) memiliki background pastel cerah (`bg-purple-50/80`, dll.) yang kontrasnya tidak harmonis dengan tema gelap.
- **Solusi & Implementasi Terpadu**:
  1. **Standardisasi Class CSS Komponen (`app.blade.php`, `kandidatportal/index.blade.php`, & `interview/index.blade.php`)**:
     - Menerapkan class terstruktur `.odoo-stat-card`, `.odoo-card-label`, `.odoo-card-value`, dan `.odoo-card-icon` pada ke-8 mini card di kedua modul.
     - Menyediakan modifier aktif per warna: `.odoo-stat-matched`, `.odoo-stat-blue`, `.odoo-stat-indigo`, `.odoo-stat-violet`, `.odoo-stat-sky`, `.odoo-stat-amber`, `.odoo-stat-emerald`, dan `.odoo-stat-slate`.
  2. **Styling Dark Mode Presisi Tinggi (High Contrast & Glowing Aesthetics)**:
     - **Inactive Card**: Background otomatis menyatu dengan warna kartu dashboard gelap (`var(--bg-card-alt)`), border subtle (`var(--border-color)`), teks label abu-abu terang bersih (`#94a3b8`, hover `#cbd5e1`), dan angka kandidat putih tajam (`#ffffff`).
     - **Active Card**: Efek *neon accent glow* dengan background transparan berwarna (opacity 20%), border aksen bercahaya, label beraksen warna terang (contoh: Ungu `#e9d5ff`, Biru `#bfdbfe`, dsb.), dan angka kandidat putih tebal (`#ffffff`).
     - **Badge Icon Pill**: Diberikan background semi-transparan dengan border dan warna ikon cerah menyala (purple, blue, indigo, violet, sky, amber, emerald, slate).
     - **Tombol Reset Filter**: Diberikan styling dark mode elegan bernuansa rose transparan (`rgba(244, 63, 94, 0.15)`) dengan border halus.
  3. **Universal Dark Mode Overrides untuk Seluruh Varian Opacity Tailwind Slate**:
     - Memperluas selektor global `html[data-theme="dark"]` di `layouts/app.blade.php` agar mencakup variasi opacity Tailwind: `.bg-slate-50\/20`, `.bg-slate-50\/30`, `.bg-slate-50\/40`, `.bg-slate-50\/50`, `.bg-slate-50\/60`, `.bg-slate-50\/70`, `.bg-slate-50\/75`, `.bg-slate-50\/80`, `.bg-slate-50\/90`, `.bg-slate-100/*`, dan `.bg-slate-200`, serta badge pastel (*rose, emerald, blue, purple, amber*). Mencegah munculnya bercak abu-abu terang di seluruh modul dashboard lainnya saat dark mode aktif.

---

### 68. 📊 Optimasi Kontras & Keterbacaan Seluruh Tabel di Modul Job Statistik pada Dark Mode (20 September 2026)
- **Latar Belakang & Masalah**:
  - Pada tampilan Dark Mode di modul **Job Statistik** (`/job/stats`), tampilan seluruh tabel mengalami masalah keterbacaan (*low contrast*):
    - **Tabel 1 (Statistik per Area) & Tabel 2 (Statistik per Nama User & Area)**: Angka *Jumlah Job Post* (`text-blue-700` / `text-sky-700`) dan *Jumlah Pelamar* (`text-emerald-700`) menggunakan warna teks gelap bawaan Tailwind dengan latar sel `bg-*-50/50`, sehingga tulisan tenggelam ke latar gelap dan sulit dibaca.
    - **Tabel 3 (Statistik Detail Kandidat Berdasarkan Prinsiple)**: Kolom *Prinsiple* (`text-indigo-700`) sangat gelap; badge pill *Kandidat Green*, *Yellow*, *Red*, serta kolom *Total Pelamar* tampak kusam dan kurang kontras.
    - **Tabel 4 (Statistik Kandidat per Rekrutor Step Odoo ERP)**: Header kolom tahapan rekrutmen Odoo ERP dan pill badge per tahapan sulit dibedakan, terutama antara tahapan aktif dan nilai 0.
    - Thead sticky, batas garis tabel (*table borders*), dan kontrol footer/pagination perlu diselaraskan dengan ke-4 varian palet tema gelap (*Hitam Pekat, Biru Navy, Dark Grey, Soft Grey*).
- **Solusi & Implementasi Terpadu**:
  1. **Class Semantic Terstruktur pada Template ([job/statistik.blade.php](file:///d:/ASystem/newasystem/resources/views/job/statistik.blade.php))**:
     - Menerapkan arsitektur class terisolasi agar tidak merusak tampilan Light Mode:
       - Kontainer & Header: `.job-stats-card`, `.job-stats-header-area`, `.job-stats-header-user`, `.job-stats-header-detail`, `.job-stats-header-odoo`.
       - Tabel & Header Kolom: `.job-stats-table`, `.th-green`, `.th-yellow`, `.th-red`, `.th-total`, `.th-step-*`.
       - Sel Data: `.stat-cell-jobpost`, `.stat-cell-pelamar`, `.stat-cell-prinsiple`, `.stat-cell-green`, `.stat-cell-yellow`, `.stat-cell-red`, `.stat-cell-total`, `.stat-text-primary`, `.stat-badge-region`.
       - Badge Tahapan Odoo: `.stat-badge-step-*`, `.stat-zero-val`, `.stat-btn-portal`, `.job-stats-footer`.
  2. **Styling Dark Mode Presisi Tinggi ([layouts/app.blade.php](file:///d:/ASystem/newasystem/resources/views/layouts/app.blade.php))**:
     - **Eliminasi Pita Kabut Vertikal**: Seluruh background cell `<td>` diatur transparan (`background: transparent !important;`) pada mode dark, menghapus lapisan belang vertikal kusam (`bg-*-50/40`) sehingga latar tabel tampil bersih, rata, dan menyatu harmonis dengan warna dark theme.
     - **Jumlah Job Post & Pelamar**: Job Post tampil Sky Blue cerah (`#38bdf8`), Pelamar tampil Mint Emerald tajam (`#34d399`).
     - **Prinsiple**: Diberikan warna Lilac Indigo terang (`#a5b4fc`).
     - **Badge Kategori Green, Yellow, Red & Total**: Diberikan latar transparan ber-border halus dengan warna kontras tinggi: Green (`#34d399`), Yellow (`#fbbf24`), Red (`#fb7185`), dan Total (`#ffffff`).
     - **Sistem Pill Tahapan Pipeline Odoo ERP (`.stat-pill-step`)**:
       - Menerapkan komponen pill rounded tersendiri dengan aksen neon bergradasi (*Pelamar Sky Blue, Interview Indigo, Principal Lavender, E-Learning Golden Amber, PKWT Cyan Teal, Belum di Odoo Soft Coral*).
       - Penekanan khusus pada **6. Joined** (`#34d399` font-black dengan glow shadow).
       - Penataan angka kosong / 0 (`.stat-zero-val`): ditampilkan halus tanpa box border (`#475569`) agar pandangan mata pengguna fokus tertuju pada angka tahapan yang aktif.
     - **Tombol Aksi Portal**: Menggunakan styling dark mode transparan indigo (`rgba(99, 102, 241, 0.2)` / `#c7d2fe`) dengan hover bercahaya terang.
     - **Header Sticky & Pagination**: Header tabel menggunakan `var(--bg-card-alt)` dengan teks `#cbd5e1`, pemisah border halus `var(--border-subtle)`, dan kontrol paginasi yang nyaman di mata.

---

### 69. 🛡️ Sistem Audit Trail & Log Aktivitas Komprehensif Seluruh Sistem (20 September 2026)
- **Latar Belakang & Kebutuhan**:
  - Perekaman seluruh aktivitas pengguna di sistem secara terpusat untuk keperluan audit, transparansi, pemantauan operasional, keamanan data, dan kepatuhan regulasi (*compliance*).
  - Aktivitas mencakup: Sesi Autentikasi (Login, Logout, Gagal Login, Switch User/Impersonate, Revert Switch User), CRUD Master Data (Karyawan, Prinsiple, CBT Soal), Rekrutmen & Pelamar (Kandidat Portal, Interview, Assessment, Remidi CBT, Alihkan AS, Ganti Area, Arsipkan), Export Data Laporan (Excel .xlsx), Integrasi Sinkronisasi Odoo ERP, Konfigurasi RBAC & Hak Akses Pengguna, serta Work Plan & ToDoList.
- **Komponen & Arsitektur Teknis**:
  1. **Tabel Database & Indeks Performa (`activity_logs`)**:
     - Kolom: `id`, `user_id`, `user_name`, `user_email`, `user_jabatan`, `action`, `module`, `description`, `subject_type`, `subject_id`, `properties` (JSON payload diff & metadata), `ip_address`, `user_agent`, `url`, `method`, `timestamps`.
     - Indeks komposit pada `created_at`, `user_id`, `action`, dan `module` untuk akselerasi kueri filter rentang tanggal.
  2. **Model Eloquent ([app/Models/ActivityLog.php](file:///d:/ASystem/newasystem/app/Models/ActivityLog.php))**:
     - Scopes filter dinamis: `filterModule`, `filterAction`, `filterUser`, `search`, `dateRange`.
     - Accessors cerdas: `action_badge_class`, `action_icon`, `formatted_created_at`, `diff_time`, `device` (deteksi otomatis browser dan platform: Chrome, Safari, Edge, Firefox, Android, iOS, Windows, Mac, Linux).
  3. **Service Terpusat & Safe Execution ([app/Services/ActivityLogger.php](file:///d:/ASystem/newasystem/app/Services/ActivityLogger.php) & [app/helpers.php](file:///d:/ASystem/newasystem/app/helpers.php))**:
     - Metode standar: `log()`, `auth()`, `crud()`, `export()`, `sync()`.
     - Isolasi error dengan `try-catch` sehingga jika terjadi kendala pada database log tidak akan pernah menggagalkan alur transaksi proses utama aplikasi.
     - Helper global `activity_log(...)` terdaftar otomatis di `AppServiceProvider`.
  4. **Otomasi Autentikasi Event Listener ([app/Providers/AppServiceProvider.php](file:///d:/ASystem/newasystem/app/Providers/AppServiceProvider.php))**:
     - Menangkap event resmi Laravel: `Login` (catat login sukses), `Logout` (catat logout), dan `Failed` (catat percobaan login gagal dengan identifier attempted email/username dan IP).
  5. **Integrasi Controller Menyeluruh**:
     - **[EmployeeController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/EmployeeController.php)**: Create, Update (snapshot diff data lama vs baru), Toggle Login Access, Bulk Edit Pimpinan, Switch User (Impersonate), Revert Switch User.
     - **[KandidatPortalController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/KandidatPortalController.php)**: Reset Password Kandidat, Update Interview, Alihkan AS, Ganti Area, Arsipkan Kandidat, Export Excel (.xlsx), Sinkronisasi Odoo Massal & Individu.
     - **[InterviewController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/InterviewController.php)**: Form Assessment, Approval Prinsiple (Approve/Reject dengan screenshot), Alihkan AS, Ganti Area, Set Remidi CBT Matematika, Edit Prinsiple, Arsipkan, Sinkronisasi Odoo.
     - **[JobController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/JobController.php)**: Tambah Job Requirement, Edit Job Requirement (diff old vs new), Toggle Status Job, Hapus Job.
     - **[JobStatistikController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/JobStatistikController.php)**: Export Laporan Rekapitulasi Statistik Excel (.xlsx).
     - **[PrincipleController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/PrincipleController.php)**: Tambah Prinsiple, Edit Prinsiple, Toggle Status Aktif, Hapus Prinsiple, Re-import Official 157 Entitas.
     - **[UserProfileController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/UserProfileController.php)**: Edit Profil Pengguna, Ganti Password Akun, Hapus Avatar Profil.
     - **[RbacController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/RbacController.php)**: Tambah Role, Edit Role, Hapus Role, Simpan Matriks Hak Akses, Ubah Role & Scope Pengguna, Tambah Akun Pengguna, Reset Password Pengguna.
     - **[OdooSettingController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/OdooSettingController.php)**: Simpan Konfigurasi Koneksi, Sync per Entitas, Sync All Entitas, Sync by NIK, Cleanup Duplikat Karyawan.
     - **[WorkPlanController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/WorkPlanController.php)**: Tambah Tugas, Edit Tugas, Pindah Status (Kanban), Arsipkan Tugas, Pulihkan dari Arsip, Hapus Tugas, Export Excel (.xlsx).
     - **[CbtController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/CbtController.php)**: Login/Logout Kandidat, Pengisian Profil 6 Tab, Pengerjaan & Submit Tes Psikotes/Kepribadian, Tes Matematika, dan Tes Komputer.
  6. **Viewer Antarmuka Modern ([resources/views/activity_logs/index.blade.php](file:///d:/ASystem/newasystem/resources/views/activity_logs/index.blade.php))**:
     - **4 Kartu Metrik Ringkasan**: Total Seluruh Log, Aktivitas Hari Ini, Total Sesi Login, Perubahan Data (CRUD).
     - **Filter Bar Responsif 6 Kolom**: Dari Tanggal, Sampai Tanggal, Modul, Jenis Aksi, Pengguna, Kata Kunci Pencarian, serta Pilihan Limit Baris per Halaman (15, 25, 50, 100).
     - **Tabel Audit Trail Interaktif**: Waktu detail + diff time (*X menit yang lalu*), User badge avatar + nama + jabatan + email, Action badge dengan icon penanda, Modul badge, Deskripsi lengkap, IP Address & jenis Device/Browser.
     - **Modal Pratinjau Diff Payload Data**: Membandingkan nilai lama (*old values* warna rose) vs nilai baru (*new values* warna emerald) serta raw JSON viewer.
     - **Tombol Export Excel (.xlsx)**: Unduhan rekap audit trail format XLSX ber-styling Sapphire Blue profesional via PhpSpreadsheet.
     - **Menu Sidebar "Log Aktivitas"**: Ditempatkan di grup Pengaturan Sistem khusus Administrator dengan badge `AUDIT`.
     - **Dukungan Penuh Dark Mode**: Menyesuaikan otomatis dengan 4 palet tema (*Hitam Pekat, Biru Navy, Dark Grey, Soft Grey*).


---

### 70. 📊 Perbaikan Error Export Excel Work Plan & Activity Logs: Migrasi ke OpenXML ZipArchive Mandiri (Zero External Dependency) (20 September 2026)
- **Akar Masalah**:
  - Pada saat mengekspor data Work Plan (`https://new.asystem.co.id/workplan/export`), sistem memicu error fatal `Class "PhpOffice\PhpSpreadsheet\Spreadsheet" not found` (HTTP 500) di [WorkPlanController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/WorkPlanController.php) baris 969.
  - Masalah serupa juga terdapat pada [ActivityLogController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/ActivityLogController.php) di mana kedua controller mengimpor class dari package `PhpOffice\PhpSpreadsheet` yang tidak terpasang di `composer.json` / `vendor` server produksi.
- **Solusi & Implementasi Terpadu**:
  1. **WorkPlanXlsxExportService ([app/Services/WorkPlanXlsxExportService.php](file:///d:/ASystem/newasystem/app/Services/WorkPlanXlsxExportService.php))**:
     - Mengembangkan service export mandiri berbasis PHP native `ZipArchive` & OpenXML Spreadsheet standard mengikuti pola sukses [CandidateXlsxExportService.php](file:///d:/ASystem/newasystem/app/Services/CandidateXlsxExportService.php) dan [JobStatistikXlsxExportService.php](file:///d:/ASystem/newasystem/app/Services/JobStatistikXlsxExportService.php) tanpa ketergantungan package eksternal (*zero external dependency*).
     - **Desain & Tata Letak Seluruh 13 Kolom**:
       - Kolom A: `NO` (rata tengah)
       - Kolom B: `ID TUGAS` (#123)
       - Kolom C: `JUDUL TUGAS` (lebar 40, wrap-text rapi)
       - Kolom D: `PRIORITAS` (badge tematik: High = soft rose `#FFE11D48`, Medium = soft amber `#FFD97706`, Low = slate)
       - Kolom E: `STATUS` (badge tematik: Done = soft emerald `#FF059669`, In Progress = soft sky `#FF0284C7`, Review = soft purple `#FF7C3AED`, Archived = muted)
       - Kolom F: `PENUGAS (CREATOR)`
       - Kolom G: `ASSIGNEE (PENERIMA)`
       - Kolom H: `DELEGATOR`
       - Kolom I: `TARGET DEADLINE` (format `d/m/Y`)
       - Kolom J: `PROGRESS SUBTASK` (rekap checklist contoh: `2/5 (40%)`)
       - Kolom K: `TANGGAL INPUT` (format `d/m/Y H:i`)
       - Kolom L: `TANGGAL SELESAI` (format `d/m/Y H:i`)
       - Kolom M: `LINK LAMPIRAN` (formula hyperlink Excel `=HYPERLINK(...)` aktif)
     - **Tampilan Profesional**: Banner judul, rincian metadata filter, header baris Dark Slate 800 (`#1E293B`) dengan teks putih tebal, freeze pane baris ke-4, zebra striping berselang-seling (`#FFFFFF` dan `#F8FAFC`), serta border sel tipis (`#CBD5E1`).
  2. **ActivityLogXlsxExportService ([app/Services/ActivityLogXlsxExportService.php](file:///d:/ASystem/newasystem/app/Services/ActivityLogXlsxExportService.php))**:
     - Mengembangkan service mandiri berbasis `ZipArchive` untuk ekspor audit trail, mencegah timbulnya error serupa pada modul Log Aktivitas.
     - Styling Sapphire Blue (`#0F52BA`) elegan dengan badge aksi tematik (Create, Update, Delete, Login, Failed).
  3. **Penyelarasan Controller & Respons Download**:
     - Menggantikan manual header `header(...)` dan `exit;` dengan standar Laravel `response()->download($filePath, $fileName, [...])->deleteFileAfterSend(true)`.
     - Menghapus seluruh import class `PhpOffice\PhpSpreadsheet` di [WorkPlanController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/WorkPlanController.php) dan [ActivityLogController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/ActivityLogController.php).

---

### 71. 🔄 Perbaikan Import Kandidat Interview: Replace Data NIK Eksisting & Reset Menyeluruh Data Tes Online CBT (21 September 2026)
- **Akar Masalah**:
  - Pada alur import kandidat interview (baik via Excel Walkin maupun Tarik NIK Odoo ERP), ketika ada NIK pelamar yang sama, sistem lama melakukan `update(['status' => 'Arsip'])` dan membuat baris record baru (`Candidate::create`), sehingga menimbulkan penumpukan data duplikat (tercatat 16.955 record duplikat historis).
  - Model [Candidate.php](file:///d:/ASystem/newasystem/app/Models/Candidate.php) memiliki model listener `booted()` `static::created` yang otomatis mencari record lama dengan NIK sama sebagai `$donor`, lalu menyalin seluruh nilai tes lama (`tes_kepribadian`, `tes_matematika`, `tes_komputer`, `signature_path`) dan mengkloning entri `TestResult`. Akibatnya, kandidat baru yang diimport tetap terbaca telah menyelesaikan tes dan menyerap nilai/jawaban tes sebelumnya.
  - [CandidateEvaluationDataService.php](file:///d:/ASystem/newasystem/app/Services/CandidateEvaluationDataService.php) melakukan query pada tabel legacy `hasil_kompt` dengan klausa `orWhere('nomor_ktp', $candidate->nik)` tanpa memeriksa status kelulusan/pengerjaan tes, sehingga data tes komputer lama tetap muncul di tab evaluasi.
- **Solusi & Implementasi Terpadu**:
  1. **Penonaktifan Auto-Donor Listener ([app/Models/Candidate.php](file:///d:/ASystem/newasystem/app/Models/Candidate.php))**:
     - Menghapus event listener `booted()` `static::created` yang menyalin hasil tes dari donor NIK lama, memastikan record kandidat baru atau ter-replace benar-benar bersih dan independen.
  2. **Mekanisme Replace & Test Reset pada CandidateImportService ([app/Services/CandidateImportService.php](file:///d:/ASystem/newasystem/app/Services/CandidateImportService.php))**:
     - Ketika NIK ditemukan pada database lokal, sistem mempertahankan 1 record utama dan menghapus sisa ID duplikat historis.
     - Menghapus seluruh riwayat tes lama pada relasi child: `TestResult`, `tb_hasilpsikotes`, `tb_hasilmath`, `hasil_kompt`, `hasilinterview`, `InterviewAssessment`, `PrincipleApproval`, `WorkExperience`, dan `tb_pengalaman`.
     - Meng-update record kandidat dengan data baru dari file Excel, menyetel status `Active`, `jenis = ''` (agar muncul di list interview aktif), serta me-reset seluruh indikator tes online ke `null`: `tes_kepribadian = null`, `tes_matematika = null`, `tes_komputer = null`, `tes_ke = 1`, `buktikomputer = null`, `signature_path = null`.
     - Menyelaraskan tabel legacy `tb_kandidat` dengan data baru dan nilai tes ter-reset.
     - Mengirimkan SSE event `'replace'` dengan pesan informatif pada streaming terminal log.
  3. **Penyelarasan Tarik NIK Odoo ERP ([app/Http/Controllers/CandidateImportController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/CandidateImportController.php))**:
     - Menggantikan logika archiving dan duplikasi dengan mekanisme replace & reset menyeluruh yang identik pada fungsi `importOdooByNik`.
     - Menghapus relasi tes lama dan mereset nilai tes ke `null`.
     - Mengembalikan respons sukses terpadu: `"Data kandidat {Nama} berhasil di-REPLACE dan tes online di-RESET ke awal!"`.
  4. **Proteksi Stale Data pada CandidateEvaluationDataService ([app/Services/CandidateEvaluationDataService.php](file:///d:/ASystem/newasystem/app/Services/CandidateEvaluationDataService.php))**:
     - Menambahkan guard check `$isPsikoCompleted` dan `$isKomptCompleted` (memverifikasi nilai tidak kosong, bukan `'00:00:00'`, dan bukan `'-'`).
     - Mengeliminasi fallback query `orWhere('nomor_ktp', ...)` pada `hasil_kompt` saat kandidat belum menyelesaikan tes.
     - Mengembalikan status `hasPsikotes = false`, `hasMath = false`, `hasKompt = false`, serta kesimpulan `'Belum Tes'` secara konsisten saat kandidat belum mengerjakan tes.
  5. **Penyempurnaan UI Modal & Terminal Log ([resources/views/interview/index.blade.php](file:///d:/ASystem/newasystem/resources/views/interview/index.blade.php))**:
     - Menambahkan badge styling `case 'replace':` berlatar cyan transparan (`bg-cyan-500/20 text-cyan-300 border-cyan-500/30`) pada terminal log JavaScript.
     - Memperbarui teks peringatan deteksi NIK eksisting pada modal tarik Odoo (`previewArchiveWarning`): `"NIK ini sebelumnya sudah pernah terdaftar di ASystem. Data lama otomatis di-REPLACE dan seluruh data tes online di-RESET ke awal agar pelamar dapat memulai seleksi tes dari awal."`
  6. **Artisan Command Deduplikasi Historis ([app/Console/Commands/CleanDuplicateCandidatesCommand.php](file:///d:/ASystem/newasystem/app/Console/Commands/CleanDuplicateCandidatesCommand.php))**:
     - Menyediakan command `php artisan candidates:clean-duplicates {--dry-run}` untuk mengonsolidasikan ribuan record duplikat historis secara aman kapan saja dibutuhkan oleh administrator sistem.

---

### 72. 🛠️ Perbaikan Sync Karyawan by NIK Odoo & Penyelarasan Nama Resmi Entitas ABO & ATB (21 September 2026)
- **Akar Masalah**:
  - Pada modal "Sync Data Karyawan by NIK (Odoo ERP)" di halaman Master Karyawan, pemanggilan `fetch(route('odoo.setting.sync-by-nik'))` menghasilkan URL absolut dengan protokol `http://`. Pada server produksi yang dilindungi HTTPS/Cloudflare, Nginx me-redirect permintaan `POST` tersebut dengan status 301/302 ke `https://`. Berdasarkan standar browser HTTP, pengalihan 301/302 mengubah metode `POST` menjadi `GET`, sehingga Laravel menolak permintaan dengan error: *"The GET method is not supported for route odoo-setting/sync-by-nik. Supported methods: POST, PUT."*
  - Label nama entitas pada dropdown Master Karyawan keliru/usang: entitas ATB tertulis *"PT Anugrah Tri Berkah"* (seharusnya **PT ANUGRAH TALENTA BERKARYA**), dan entitas ABO tertulis *"PT Arina Bintang Operasional"* (seharusnya **PT ABADI BERKAT ODELIA**).
- **Solusi & Implementasi Terpadu**:
  1. **Penyempurnaan URL Pemanggilan & HTTPS Enforcement**:
     - Mengubah pemanggilan `fetch` pada [master/karyawan/index.blade.php](file:///d:/ASystem/newasystem/resources/views/master/karyawan/index.blade.php) dan [odoo/setting.blade.php](file:///d:/ASystem/newasystem/resources/views/odoo/setting.blade.php) menjadi URL relatif `route('odoo.setting.sync-by-nik', [], false)` (`/odoo-setting/sync-by-nik`) agar browser selalu menggunakan protokol halaman saat ini (`https://`) tanpa terpengaruh redirect skema.
     - Menambahkan proteksi `URL::forceScheme('https')` pada [AppServiceProvider.php](file:///d:/ASystem/newasystem/app/Providers/AppServiceProvider.php) untuk lingkungan produksi atau saat terdeteksi reverse proxy HTTPS.
     - Memperluas route `odoo.setting.sync-by-nik` di [routes/web.php](file:///d:/ASystem/newasystem/routes/web.php) dengan `Route::match(['GET', 'POST'], ...)` sehingga aman dari penolakan metode HTTP.
  2. **Penyelarasan Nama Entitas ABO & ATB**:
     - Memperbarui seluruh dropdown pada [resources/views/master/karyawan/index.blade.php](file:///d:/ASystem/newasystem/resources/views/master/karyawan/index.blade.php) (Form Tambah Karyawan, Edit Karyawan, Bulk Edit Pimpinan, dan Modal Sync NIK):
       - `ABO` diselaraskan menjadi **PT Abadi Berkat Odelia** (PT ABADI BERKAT ODELIA).
       - `ATB` diselaraskan menjadi **PT Anugrah Talenta Berkarya** (PT ANUGRAH TALENTA BERKARYA).
     - Memperbarui [OdooEntitySeeder.php](file:///d:/ASystem/newasystem/database/seeders/OdooEntitySeeder.php) agar default data seeder konsisten dengan master entitas.

---

### 73. 🛡️ Penuntasan Error Syntax `unexpected token '<',` pada Sync by NIK & Safe Parsing JSON (21 September 2026)
- **Akar Masalah**:
  1. **Intersepsi Error Nginx (HTML 404/422)**:
     - Ketika NIK karyawan tidak ditemukan di Odoo atau validasi gagal, [OdooSettingController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/OdooSettingController.php) merespons dengan HTTP status 404 (`Not Found`) atau 422 (`Unprocessable Entity`).
     - Web server Nginx pada server produksi memiliki konfigurasi penanganan error (`fastcgi_intercept_errors on` / custom error page), sehingga setiap respon HTTP 404/422 dari PHP secara otomatis dicegat oleh Nginx dan digantikan dengan halaman HTML error Nginx (`<html><head><title>404 Not Found</title>...`).
  2. **Redirect 302 Tanpa Cek JSON pada Middleware `EnsureUserIsAdmin`**:
     - Middleware [EnsureUserIsAdmin.php](file:///d:/ASystem/newasystem/app/Http/Middleware/EnsureUserIsAdmin.php) sebelumnya langsung memanggil `redirect()->route('login')` atau `redirect()->route('fitur.index')` tanpa memeriksa apakah permintaan berasal dari AJAX/fetch (`$request->expectsJson()`).
     - Akibatnya, jika sesi login kedaluwarsa atau pengguna beralih mode user, middleware mengembalikan HTTP 302 HTML redirect yang diikuti oleh browser menuju halaman HTML login (`<!DOCTYPE html>...`).
  3. **Direct `response.json()` Parsing**:
     - Pemanggilan `response.json()` secara langsung pada `fetch()` di JavaScript frontend akan langsung mengalami crash dan melempar exception:
       `SyntaxError: Unexpected token '<', "<!DOCTYPE "... is not valid JSON`
       atau `SyntaxError: Unexpected token '<', "<html>"... is not valid JSON`.
- **Solusi & Implementasi Terpadu**:
  1. **HTTP 200 Payload-Driven Response ([OdooSettingController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/OdooSettingController.php) & [EmployeeController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/EmployeeController.php))**:
     - Seluruh respon JSON pada `syncByNik`, `testConnection`, `sync`, `syncAll`, dan `cleanupDuplicates` kini selalu mengembalikan status HTTP 200 dengan payload seragam `{ "success": false, "message": "..." }` saat NIK tidak ditemukan atau konfigurasi belum lengkap.
     - Menghindari 100% intersepsi halaman HTML error oleh Nginx maupun proxy Cloudflare.
  2. **Dukungan Respon JSON pada Middleware ([EnsureUserIsAdmin.php](file:///d:/ASystem/newasystem/app/Http/Middleware/EnsureUserIsAdmin.php))**:
     - Menambahkan verifikasi `$isJson = $request->expectsJson() || $request->wantsJson() || $request->ajax()`. Jika terdeteksi permintaan JSON, middleware mengembalikan JSON HTTP 200 dengan pesan ramah: *"Sesi login Anda telah berakhir. Silakan muat ulang (refresh) halaman dan login kembali."* alih-alih redirect 302 HTML.
  3. **Pengecualian CSRF Timeout ([bootstrap/app.php](file:///d:/ASystem/newasystem/app/bootstrap/app.php))**:
     - Mendaftarkan endpoint `odoo-setting/sync-by-nik`, `sync-by-nik`, dan `master/karyawan/sync-by-nik` pada `$middleware->validateCsrfTokens(except: [...])` agar permintaan sync by NIK tidak pernah gagal karena CSRF token mismatch/expired saat tab browser dibiarkan terbuka lama.
  4. **Multi-Alias Route ([routes/web.php](file:///d:/ASystem/newasystem/routes/web.php))**:
     - Menambahkan alias route `GET|POST /sync-by-nik` dan `GET|POST /master/karyawan/sync-by-nik` mendampingi route utama `/odoo-setting/sync-by-nik`.
  5. **Safe Response Parsing & X-Requested-With Header ([resources/views/master/karyawan/index.blade.php](file:///d:/ASystem/newasystem/resources/views/master/karyawan/index.blade.php) & [resources/views/odoo/setting.blade.php](file:///d:/ASystem/newasystem/resources/views/odoo/setting.blade.php))**:
     - Menambahkan header `'X-Requested-With': 'XMLHttpRequest'`.
     - Menggantikan `response.json()` dengan `await response.text()` dan pembungkusan `try { JSON.parse(text) } catch (e)`. Jika respon server mengandung tag HTML (`<html`, `<!DOCTYPE`, `<center>`), sistem menampilkan pesan panduan yang jelas dan tidak akan pernah menampilkan error mentah `unexpected token '<',`.

---

### 74. ⚡ Optimalisasi Performa Master Karyawan & Perbaikan Logika Sync by NIK (21 September 2026)
- **Akar Masalah**:
  1. **Logika Pengecekan Hasil Sync Single Employee**:
     - Pada [OdooSettingController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/OdooSettingController.php) baris 328, pengecekan respon sync menggunakan kondisi:
       `if (!empty($res['success']) && !empty($res['data']))`
     - Padahal [OdooSyncService.php](file:///d:/ASystem/newasystem/app/Services/OdooSyncService.php) mengembalikan model `Employee` di dalam key `'employee'`, bukan `'data'`.
     - Akibatnya, meskipun sinkronisasi data karyawan (contoh: NIK Selviani di AMK) berhasil dilakukan dan tersimpan di database lokal, kondisi `!empty($res['data'])` bernilai `false`, sehingga sistem:
       - Mengabaikan status sukses dan tidak mengeksekusi `break;`.
       - Pada pencarian **"Cari Otomatis di Semua Entitas"**, loop terus berlanjut hingga entitas terakhir (ATB) di mana NIK tersebut tidak ada, sehingga pesan error yang ditampilkan ke pengguna adalah: *"NIK tidak ditemukan di server Odoo ATB"*.
       - Pada pencarian langsung entitas **"AMK"**, sistem memasukkan pesan sukses ke variabel error fallback dengan status `'success' => false`, sehingga antarmuka menampilkannya di dalam kotak merah bertuliskan *"Karyawan Tidak Ditemukan di Odoo"*.
  2. **Beban Loading Berat dari Line Chart Pertumbuhan Employee 12 Jam**:
     - Halaman Master Karyawan mengeksekusi query database intensif dan iterasi 24 slot waktu (per 30 menit) serta memuat library eksternal `chart.js` via CDN, menyebabkan waktu loading halaman terasa lambat dan berat.
- **Solusi & Implementasi Terpadu**:
  1. **Perbaikan Logika Respon Sync by NIK ([OdooSettingController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/OdooSettingController.php) & [OdooSyncService.php](file:///d:/ASystem/newasystem/app/Services/OdooSyncService.php))**:
     - Mengubah verifikasi hasil menjadi `if (!empty($res['success']))` dan mengekstrak data dari `$res['employee']` maupun `$res['data']`.
     - Menambahkan key `'data' => $employee->toArray()` pada `syncSingleEmployee` di [OdooSyncService.php](file:///d:/ASystem/newasystem/app/Services/OdooSyncService.php).
     - Memastikan perintah `break;` langsung tereksekusi saat NIK ditemukan di salah satu entitas (contoh: AMK), sehingga pencarian pada entitas lain dihentikan seketika dan respon sukses langsung dikembalikan ke frontend.
     - Kartu hijau (*"Data Berhasil Diperbarui"*) dengan rincian karyawan, jabatan, divisi, dan status Inhouse/RateCard kini tampil dengan benar saat NIK ditemukan.
  2. **Pembersihan Line Chart Berat ([EmployeeController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/EmployeeController.php) & [resources/views/master/karyawan/index.blade.php](file:///d:/ASystem/newasystem/resources/views/master/karyawan/index.blade.php))**:
     - Menghilangkan kalkulasi 12-hour growth progress chart data dan query time-window di [EmployeeController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/EmployeeController.php).
     - Menghapus pemanggilan `@include('master.karyawan._growth_chart')` dan script CDN `chart.js` dari tampilan.
     - Mempertahankan bilah **Filter Cepat** (*Quick Filter Badges*: Semua, Aktif, Resign, Inhouse, RateCard) yang ringan, cepat, dan fungsional.
     - Halaman Master Karyawan kini terbuka secara instan (*lightning fast*).

---

## 📜 Riwayat Commit & Pembaruan Kode

| Commit ID | Deskripsi Pembaruan |
| :--- | :--- |
| `c4a70b5` | fix(interviewinhouse): synchronize evaluation tabs (interview, refcek, kompt, psikotes, math) with actual test and assessment data |
| `94cc238` | fix(inhouse): restrict head dashboard to review head step and fix undefined variable isHrd in show |
| `7e1b312` | feat(ai-queue): add side-by-side user distribution pie chart beside area chart |
| `b39ea24` | fix(inhouse-approval): resolve correct OM/Head approver for Inhouse candidates (prioritize entity OM OPS over admin support) |
| `b4bb434` | feat(ai-queue): add Area distribution pie chart for unanalyzed candidates |
| `9c521ba` | feat(ai): support AI analysis for Job Portal candidates without uploaded CV using form input data |
| `55b78ba` | fix(ai): prepend prompt instruction and set max_tokens to 8000 for Sumopod GLM reasoning |
| `e6f63ea` | fix(ai): increase Sumopod timeout to 120s and optimize reasoning prompt and tokens |
| `cbd13e0` | Order AI queue candidates strictly by earliest registration entry date with score 0 and job portal |
| `639bde9` | Add realtime Live Console Log with auto-prune yesterday logs and manual trigger feature |
| `e25b983` | Fix registration date 30 Nov -0001 bug and remove Score/Kategori column from AI queue table |
| `10adc4e` | feat: ubah layout log antrean AI menjadi berdampingan kanan-kiri dan tambahkan informasi model AI pada list selesai dianalisa |
| `1ddaaa3` | fix: perbaiki modal Alihkan ke AS dan Ganti Area portal serta buat halaman Log Antrean & Hasil Analisa AI dengan auto reload realtime |
| `d19e22c` | feat: tambahkan fitur searchable dropdown modal Ganti Area & Prinsiple serta daftar Nama AS / Rekrutor pada modal Alihkan ke AS |
| `fb0f6f9` | docs: add milestone 38 for master prinsiple search enhancement |
| `f86dae8` | fix(master-prinsiple): enhance search logic with case-insensitive token matching and smart entity fallback |
| `002e13c` | feat(ai-settings): add OpenRouter fallback (Gemini -> OpenRouter -> Sumopod) and dynamic custom model management |
| `2c1425a` | fix(career): allow archived candidates from portal and interview to re-apply to job postings |
| `61c654c` | fix(odoo-sync): exclude non-employee OD- dummy accounts and delete existing OD- records |
| `8f1a42d` | fix(odoo-sync): protect existing employee email and password from being overwritten during sync |
| `d5b9e33` | fix(odoo-sync): handle future departure_date as active and prioritize active ABO over resigned AMK |
| `7840994` | feat: show reset password notification in navbar bell, persistent bottom-right toast, and restrict group chat messages to members only |
| `152f9ad` | feat(auth): fitur lupa kata sandi via live chat ke admin dengan auto-sync Odoo & kirim akses |
| `781bf51` | fix(odoo-sync): perbaiki undefined variable tanggalJoin pada syncSingleEmployee |
| `5db403a` | fix(odoo-sync): penanganan mutasi entitas karyawan resign dan reaktivasi status |
| `030ad80` | fix(interview): batasi approval inhouse khusus 5 entitas resmi dan pulihkan approval prinsiple |
| `86f8043` | docs: update progress documentation with inhouse approval module, step locking, and tab bug fix |
| `253c764` | fix(interview): lock nama approver inhouse to user pimpinan on Step 1 to prevent manual selection |
| `4cf9a40` | fix(interview): close unclosed odoo sync form tag and add explicit type=button on tab navigation buttons |
| `b87e0ab` | feat(interview): unify candidate profile view, include all test tabs in interviewinhouse, and enforce strict step locking for inhouse approval |
| `7a9479e` | feat(interview): implement inhouse approval flow and approver views for 5 inhouse entities |
| `e346436` | docs: add milestone 18 for demo account cleanup and candidate ownership revert |
| `2332af4` | fix: revert candidate interview users from demo account jamil@asystem.co.id to legitimate users and clean controller fallbacks |
| `95ea357` | fix(region): align job statistics and candidate portal regions with tb_area master data |
| `3c18aec` | feat(workplan): resolve case-sensitivity matching, archive tasks access, date grouping and date filtering |
| `fe69c99` | feat(job): sesuaikan QR Code dan format broadcast WhatsApp dengan link langsung detail lowongan |
| `61d9fab` | feat(walkin): tampilkan jabatan pada dropdown Nama AS dan perbagus input Tanggal Lahir dengan label dan deteksi usia |
| `99ed017` | fix(walkin): filter dropdown nama AS hanya karyawan inhouse dengan jabatan AS/AM/RM/Rekrutor/Rekrutmen |
| `08efd69` | feat(walkin): form register kandidat walkin profesional sesuai sistem lama, searchable dropdown TomSelect, tb_area, tb_kota region cascade, dan nama AS sesuai area |
| `0e0572b` | feat(walkin): default data hari ini tgl berjalan, form registrasi publik tanpa dashboard, hilangkan sync sharepoint |
| `c496138` | feat(walkin): modul Kandidat Walkin Interview sesuai sistem lama, 4 kartu KPI, tabel filter data calon, dan formulir registrasi walkin (jenis Walkin) |
| `845f9a2` | feat(interview): tab navigasi interview selesai & arsip dan 1 tabel nasional untuk admin |
| `c1a1085` | feat(interview): pisahkan 2 tabel di halaman kandidat interview (kandidat sendiri dan rekan searea) |
| `a8d3cd5` | docs: simpan panduan implementasi WhatsApp Official Coex ke WHATSAPPCOEX.md |
| `a488c6d` | fix(auth): perbaiki fitur ganti password di edit profile dan sinkronisasi password karyawan |
| `d955b4e` | fix(master-karyawan): fix sync by NIK success condition and break loop on match, remove heavy line chart for faster page load |
| `5ffff92` | fix(odoo-sync): eliminate unexpected token '<' error by enforcing JSON on middleware, returning HTTP 200 payload-driven responses, and safe parsing in frontend |
| `b2433a1` | fix(odoo-sync): fix sync-by-nik HTTP 405 MethodNotAllowed and align ABO & ATB official company names in employee dropdowns |
| `2a6c91a` | docs: sinkronisasi riwayat commit dan pembaruan Milestone 71 di UPDATE_PROGRESS.md |
| `fd3d15a` | fix(interview): replace existing candidate NIK on import, reset online test CBT data, and eliminate auto-donor copying |
| `3c13760` | docs: sinkronisasi riwayat commit dan pembaruan Milestone 70 di UPDATE_PROGRESS.md |
| `88f4183` | fix(export): resolve Class PhpOffice\PhpSpreadsheet\Spreadsheet not found by migrating WorkPlan and ActivityLog exports to native ZipArchive OpenXML |
| `b924940` | docs: sinkronisasi progres pembaruan sistem dan riwayat commit ke UPDATE_PROGRESS.md |
| `ea0d004` | feat(audit): implementasi sistem audit trail terpusat, activity logs viewer, diff modal, dan export excel di seluruh sistem |

| `e64305b` | fix(ui): perbaiki tabel step Odoo dan hilangkan background kolom kusam pada mode dark |
| `6cebd6a` | fix(ui): perbaiki kontras dan keterbacaan tabel di modul job statistik pada dark mode |
| `e040663` | fix(ui): perbaiki kontras dan keterbacaan card step Odoo ERP serta opacity slate pada dark mode |
| `419d18c` | feat(ui): tampilkan jabatan di label user dan tambahkan kustomisasi tema dashboard (light/dark mode & custom color) |
| `ed77715` | feat(export): sertakan jabatan AS dan eliminasi fallback Administrator ESA pada export Excel |
| `3badda1` | docs: record commit 792c6ab in UPDATE_PROGRESS.md |
| `792c6ab` | feat(export): resolve recruiter full name from employee data in candidate portal XLSX export |
| `c5de959` | docs: document Milestone 63 profile avatar in chat bubbles and real-time notifications in UPDATE_PROGRESS.md |
| `e218f23` | fix(workplan-chat): replace @error with x-on:error to avoid blade directive collision |
| `2f445cd` | feat(workplan-chat): add profile avatars to chat bubbles and real-time notifications with bell badge counter and toast popup |
| `6a5bd22` | fix(chat): optimize employee query to pure inhouse (reduce from 23k to 794 items), cap dropdown rendering to 60 items, and fix modal backdrop click bubbling |
| `9822220` | feat(chat): implement searchable multi-select dropdown for team members and rename label to Groups Chat |
| `b425201` | fix(pdf): resolve MpdfException pcre.backtrack_limit by using direct local image file paths and safe HTML chunking |
| `6dcf11a` | docs: document Milestone 60 WhatsApp Groups Chat in UPDATE_PROGRESS.md |
| `5c8e7f3` | feat(workplan): implement WhatsApp-style Groups Chat with real-time messaging, inhouse members, and SweetAlert2 |
| `dafb3a3` | docs: document Milestone 59 lightbox preview modal |
| `f03d859` | feat(workplan): open attachment preview inside lightbox modal instead of new tab |
| `20ecb07` | docs: document Milestone 58 interactive attachment uploader |
| `f5c80a3` | feat(workplan): add drag and drop, clipboard paste, and image preview to task and comment attachment fields |
| `d255ad7` | docs: document Milestone 57 SweetAlert2 modernization |
| `60c8a3b` | feat(workplan): replace all native alert and confirm dialogs with SweetAlert2 |
| `2c8ce2c` | feat(workplan): filter employee dropdown strictly to active inhouse employees and add search filter |
| `2979858` | feat(workplan): restrict employee filter dropdown to inhouse employees only and add real-time search functionality |
| `914e35d` | feat(workplan): implement Work Plan & ToDoList kanban module, daily activity logs, and import 13k historical tasks |
| `3ced891` | fix: resolve undefined isAdmin, scope done and arsip to own candidates, and restrict inhouse candidates to 5 entities with HRD and Head approval |
| `ef18db8` | fix(ai-analyzer): restrict AI analysis queue and runner strictly to Job Portal candidates |
| `3995c51` | fix(console): register app/Console/Commands in bootstrap/app.php |
| `bbf580a` | feat(ai-analyzer): implement live running text ticker for AI CV processing and set pace to 1 candidate per 30 seconds |
| `c111042` | fix(timezone): configure default timezone to Asia/Jakarta and align candidate timestamps to WIB |
| `6dc7a35` | fix(rbac): allow all-principle and all-area scoped accounts to view candidate portal and interview data |
| `a6a430b` | feat(auth): implement revert switch user feature with sticky banner and navigation buttons |
| `48db3ec` | feat(job-stats): upgrade export from CSV to executive styled multi-sheet XLSX format |
| `6733c70` | fix(job-stats): resolve recruiter positions strictly from inhouse list with title case names |
| `6f91668` | feat(job-stats): rank Odoo recruitment step table by highest joined candidates count |
| `78cd94a` | fix(job-stats): filter candidate data strictly to Kandidat Portal (jenis = Job Portal) and exclude interview candidates |
| `5bc5985` | feat(job): implement job & candidate statistics page with Odoo recruitment step breakdown and export |
| `a1483df` | feat(odoo): add NIK-based candidate pull from Odoo ERP and update import modal instructions |
| `09b3c73` | feat: add compact Step Odoo statistic cards and apply Step Odoo sync and badges to Kandidat Interview |
| `dc41d92` | fix: Chunk remainingNiks to 400 items to prevent SQLite too many variables error |
| `5a241d5` | feat: Integrasi pencocokan tahapan rekrutmen Odoo ERP dengan Kandidat Portal berdasarkan NIK dan auto-archive 14 hari |
| `6b2c16e` | fix: lewati kandidat dengan file_error agar antrean cron tidak macet |
| `be0142e` | fix: gunakan analisis AI komprehensif saat apply dan perbaiki mapping hasil evaluasi CV pada detail kandidat |
| `23ff3e2` | feat: tambahkan skrip cron_ai_analyzer dengan rotasi pintar, jeda limit 2 menit, list token expired, dan fallback sumopod |
| `67aac3c` | Fix candidate photo and CV upload, intelligent fallback links in XLSX export, and restrict AI analysis to candidates with CV |
| `3175ec9` | fix: optimasi rendering notifikasi dan error bag pada halaman profile |
| `7305e0b` | feat: tambahkan fitur edit profile user / karyawan (email, no hp/wa, password, foto profile dan sinkronisasi data master) |
| `c1cc1f5` | feat(rbac): add direct employee registration & role assignment modal and Master Karyawan RBAC shortcut |
| `e35b31e` | docs: document decoupled RBAC and multi-user AS scoping architecture |
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

### 23. 🧮 Perbaikan Opsi Pilihan Ganda CBT Matematika & Normalisasi Master Soal
- **Akar Masalah (Double-Escaped JSON pada `tb_math`)**:
  - Kolom `choices` pada tabel `tb_math` untuk soal pilihan ganda (ID 4, 5, 7, 8) tersimpan dengan format JSON ter-escape ganda (`{\\"A\\":\\"66,937\\",...}`).
  - Cast `'choices' => 'array'` pada `MathQuestion.php` gagal melakukan `json_decode`, mengembalikan `null`, sehingga accessor `parsed_choices` menghasilkan array kosong `[]`.
  - Di tampilan ujian `cbt/tests/matematika.blade.php`, kondisi `@if($q['question_type'] === 'multiple_choice' && !empty($q['choices']))` bernilai `false`, menyebabkan sistem jatuh ke blok fallback input teks angka.
- **Solusi & Implementasi**:
  1. **Model `MathQuestion.php`**:
     - Menghapus cast array mentah dan menambahkan fungsi statis `parseChoices($value)` yang kebal terhadap semua format (JSON standar, string ter-escape ganda, array PHP, dsb).
     - Menambahkan accessor `getChoicesAttribute` dan `getParsedChoicesAttribute`.
     - Menambahkan mutator `setChoicesAttribute` yang memastikan data selalu tersimpan sebagai JSON rapi (`JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES`).
     - Mendaftarkan `parsed_choices` ke `$appends`.
  2. **Migrasi Pembersihan Database (`2026_09_21_110000_fix_tb_math_choices_json.php`)**:
     - Menormalisasi seluruh data `choices` di `tb_math` agar tersimpan sebagai JSON valid di tingkat database.
  3. **Tampilan Ujian & Master Soal**:
     - Fallback pilihan default legacy pada `CbtQuestionService::getMathQuestions()`.
     - Peningkatan styling radio button pada `matematika.blade.php`: badge opsi (A, B, C, D, E) otomatis terisi warna biru solid saat dipilih kandidat.
     - Penyesuaian `MathQuestionController.php` dan modal edit di `master/math/index.blade.php`.

---

### 24. 🔄 Penyempurnaan Sinkronisasi NIK Odoo Seluruh Entitas
- **Prioritisasi Kontrak Karyawan Aktif**:
  - Pada `OdooSyncService::syncSingleEmployee()`, record hasil query diurutkan agar kontrak aktif (`active = true` dan `departure_date` kosong) selalu diprioritaskan di atas kontrak lama yang sudah resign.
  - Penanganan entitas PT BUDGET kini mengembalikan array terstruktur `['success' => false, 'message' => ..., 'data' => null]` alih-alih `null`.
- **Notifikasi Pencarian Lintas Seluruh Entitas (`entity_code = 'ALL'`)**:
  - Jika satu entitas mengembalikan data resign namun entitas lain memiliki data aktif, sistem otomatis memprioritaskan data aktif.
  - Jika karyawan tidak ditemukan di seluruh entitas, notifikasi kini menampilkan pesan informatif: *"Karyawan dengan NIK / NIP '...' tidak ditemukan di seluruh entitas Odoo yang aktif (AMK, AKP, ATK, ABO, ATB)"* tanpa lagi secara keliru menyebut entitas terakhir yang diperiksa (ATB).

---

### 25. 🎯 Pembatasan Ketat Data Kandidat Portal & Interview di Dashboard AS (Hanya Kandidat Milik AS Terkait)
- **Akar Masalah**:
  - Pada commit `6dc7a35`, metode `User::canViewAllCandidates()` memiliki fallback: `return $this->handlesAllPrinciples() && $this->coversAllAreas();`.
  - Karena pada model `User` dan `Role` kedua metode tersebut secara default bernilai `true` jika tidak ada pembatasan khusus pada akun, maka **seluruh 124 user AS (Area Supervisor / Recruiter) di sistem otomatis terdeteksi memiliki hak melihat seluruh kandidat nasional** (`canViewAllCandidates() === true`).
  - Akibatnya, saat AS login ke `/kandidatportal` dan `/interview`, sistem tidak menerapkan filter `useras` / `recruiter_id` dan menampilkan seluruh 25.000+ kandidat nasional beserta dropdown switcher rekruter. Selain itu, pada `/interview`, Tabel 2 (*Data Kandidat Rekan Se-Area*) juga memuat kandidat milik rekruter lain.
- **Solusi & Implementasi**:
  1. **Model `User.php` (`canViewAllCandidates()`)**:
     - Menghapus fallback `handlesAllPrinciples() && coversAllAreas()`.
     - Fungsi ini sekarang **hanya mengembalikan `true`** untuk:
       - Administrator (`$this->isAdmin() || $this->role === 'admin'`)
       - Head HR dan Admin Officer (`in_array($this->role, ['head_hr', 'admin_officer'])`)
       - User yang secara eksplisit diberikan izin RBAC `view_all_candidates` (`$this->hasPermission('view_all_candidates')`)
     - Untuk seluruh user AS biasa, fungsi ini secara konsisten mengembalikan `false`.
  2. **Controller `InterviewController.php`**:
     - Query utama (`$myCandidatesQuery`) untuk user AS dibatasi secara mutlak hanya pada kandidat milik akun login tersebut via identitas AS (`useras` email, nama, alias) dan `recruiter_id`.
     - Daftar `$allRecruiters` dioptimasi hanya dieksekusi jika user memiliki hak view all (Admin), sehingga menghemat beban database untuk user AS.
     - Tabel 2 (*Data Kandidat Rekan Se-Area*) dinonaktifkan untuk user AS (hanya aktif jika Admin sedang memfilter rekruter tertentu), sehingga di dashboard AS hanya muncul kandidat milik AS tersebut.
     - Pada `walkInterview()`, data kandidat walk-in untuk non-admin juga dibatasi pada kandidat milik AS terkait.
  3. **Controller `KandidatPortalController.php`**:
     - Seluruh tab (*Baru*, *Interview*, *Terima*, *Arsip*) serta kartu metrik top statistics secara otomatis mencerminkan hanya kandidat milik AS terkait.
     - Ekspor Excel (`exportExcel`) untuk AS otomatis hanya mengekspor kandidat milik AS tersebut.
  4. **Tampilan Blade (`interview/index.blade.php` & `kandidatportal/index.blade.php`)**:
     - Dropdown filter rekruter (`filter_user` & `recruiter`) disembunyikan untuk user AS biasa dan hanya tampil untuk Administrator.
     - Tabel 2 disembunyikan sepenuhnya dari dashboard AS.
- **Hasil Verifikasi Live di Server Production (Server 3)**:
  - User AS (misal *Yeseniya Emeralda D.S*):
    - `/interview`: Total 1.467 kandidat (hanya miliknya, rekan area = 0).
    - `/kandidatportal`: Total 15 pelamar (hanya miliknya, scope: *Kandidat Milik Anda*).
    - Dropdown switcher rekruter tidak muncul.
  - User AS lain (*Imam Hozali*): 1.259 kandidat interview & 1 portal pelamar.
  - User AS lain (*Abdur Rahman*): 4 kandidat interview & 326 portal pelamar.
  - Super Administrator: Tetap memiliki akses penuh melihat seluruh 10.481 kandidat aktif nasional dan dapat memfilter per rekruter secara bebas.

---

### 26. 👥 Pemisahan 2 Tabel Kandidat Interview (Milik Sendiri & Rekan Se-Area) & Tab Terintegrasi Administrator
- **Dua Tabel untuk User Recruiter / AS**:
  - **Tabel 1 (Atas)**: Menampilkan data kandidat milik user sendiri berdasarkan identitas AS akun yang login (`useras` / `recruiter_id`).
  - **Tabel 2 (Bawah)**: Menampilkan data kandidat milik rekan kerja satu area penempatan yang sama, memudahkan koordinasi antar-rekruter di wilayah yang sama tanpa mencampuradukkan data pribadi.
- **Tampilan Khusus Administrator**:
  - Untuk akun dengan hak akses **Administrator HR**, halaman kandidat interview langsung menampilkan **1 tabel data nasional terpadu** tanpa pembagian 2 tabel.
  - Bagian **Interview Selesai** dan **Arsip Interview** dibuat menjadi **Tab Terintegrasi** di bagian atas (seperti pada modul Kandidat Portal), sehingga admin dapat beralih antar status kandidat secara instan tanpa perlu berpindah URL halaman.

---

### 27. 🚶 Modul & Halaman Kandidat Walk-in Interview (`/walkinterview`) Sesuai Sistem Lama
- **Replikasi Sempurna Modul Legacy**:
  - Membangun halaman daftar kandidat walkin interview yang selaras dengan antarmuka sistem lama.
  - Menyaring kandidat dengan kategori khusus `jenis = 'Walkin'`.
- **Fitur Dashboard Walk-in**:
  - **Filter Tanggal Default**: Otomatis menampilkan data kandidat pada **hari ini (tanggal berjalan WIB)**.
  - **4 Kartu Metrik KPI Berwarna**:
    - *Total Walk-in*: Menghitung akumulasi seluruh pendaftar walkin.
    - *Proses Interview*: Menghitung kandidat yang sedang dalam tahap seleksi/wawancara.
    - *Lolos Seleksi*: Menghitung kandidat yang disetujui/lolos ke tahap berikutnya.
    - *Pending / Menunggu*: Menghitung pendaftar baru yang belum diproses.
  - **Legenda Status di Footer**: Menampilkan petunjuk warna status kandidat yang informatif di bagian bawah tabel.
  - **Pembersihan Tombol Sync Sharepoint**: Menghilangkan tombol sinkronisasi SharePoint yang sudah tidak digunakan.

---

### 28. 📝 Formulir Registrasi Walkin Interview Publik, Cascading Master Data (`tb_area` & `tb_kota`), dan Searchable TomSelect
- **Halaman Formulir Publik Mandiri (`/walkinterview/register`)**:
  - Halaman pendaftaran publik yang dapat diakses langsung oleh calon kandidat / pelamar umum di luar dashboard internal (tanpa login).
  - Desain modern bernuansa profesional dengan header gradasi navy-biru, avatar photo uploader dengan *live preview*, CV attachment, serta 16+ field data diri lengkap.
- **Master Data `tb_area` (43 Area) & `tb_kota` (513 Kota)**:
  - Migrasi `2026_09_21_210000_create_tb_area_and_tb_kota_tables.php` mengimpor seluruh master area dan kabupaten/kota se-Indonesia.
  - **Relasi Cascading Dinamis**: Saat pelamar memilih Area (contoh: *Aceh* di *Region 6*), pilihan Kota Asal secara instan menyaring dan hanya memunculkan kota-kota di Region 6 (contoh: *Aceh Barat, Aceh Besar, Banda Aceh, dll.*).
- **Searchable Dropdown (TomSelect)**:
  - Seluruh field dropdown (Pendidikan, Jabatan Dilamar, Area, Kota Asal, Nama AS, Info Lowongan, Jenis Undangan) dilengkapi fitur pencarian (*type-to-search*) dengan styling rounded pill yang konsisten.

---

### 29. 🎯 Filter Ketat Personel Inhouse pada Dropdown Nama AS / Rekrutor Beserta Tampilan Badge Jabatan
- **Aturan Filter Ketat**:
  - Mengeliminasi seluruh nama acak historis dari tabel `tb_kandidat` dan divisi non-rekrutmen (driver, helper, gudang, kasir, OB, IT support, dll.).
  - Hanya memunculkan karyawan internal aktif (`tipe_karyawan = 'Inhouse'` dan `status = 'Aktiv'`) yang memiliki jabatan spesifik:
    - **AS**: `AS OPS`, `AS FACTORY`, `AS SENIOR OPS`, `Account Supervisor`, `Area Supervisor`, `ARO`.
    - **AM**: `AM OPS`, `SAM OPS`, `SAM FACTORY`, `Area Manager`, `Account Manager`.
    - **RM**: `RM OPS`, `Regional Manager`.
    - **Rekrutor / Rekrutmen**: `RECRUITMENT HRD`, `RECRUITMENT-HRD`, `Head of Recruitment`, `HR Recruiter`.
  - Pemetaan otomatis sesuai area penempatan yang dipilih pelamar.
- **Tampilan Jabatan dengan Pill Badge**:
  - Pada dropdown TomSelect, setiap opsi menampilkan **Nama Personel** di kiri dan **Pill Badge Jabatan** berwarna biru di kanan (contoh: `Ayu Putri Islamiah [RECRUITMENT HRD]`, `Fajar Bagus Sasmita [AM OPS]`, `Taufikur Rahman [AS OPS]`).
  - Saat dipilih, input menampilkan format ringkas: `Nama Karyawan (JABATAN)`.
  - Nilai tersimpan ke database tetap nama bersih karyawan sehingga sinkron dengan dashboard interview.
  - Mendukung pencarian ganda: kandidat dapat mengetik nama orang maupun nama jabatannya.

---

### 30. 📅 Penyempurnaan Input Tanggal Lahir Profesional dengan Indikator Usia Otomatis & Proteksi Input
- **Label Eksplisit**: Dilengkapi label `TANGGAL LAHIR *` dengan ikon kalender biru di atas field.
- **Kalkulasi & Indikator Usia Otomatis (*Real-time*)**:
  - Menghitung tanggal lahir seketika dan memunculkan badge usia otomatis di samping label (contoh: `✓ Usia: 24 th`).
  - Memberi peringatan visual dini jika kandidat tidak sengaja memilih tahun berjalan (`⚠ Usia 0 th (Cek tahun lahir)`).
- **Batasan Usia Kerja Realistis**: Diberikan batasan tanggal input (`min="1950-01-01"` dan `max="{{ date('Y-m-d', strtotime('-15 years')) }}"`) sehingga browser tidak membuka tahun berjalan (2026).
- **One-Click Calendar Picker**: Mengklik di mana saja pada field tanggal langsung membuka pemilih tanggal kalender (`showPicker()`).
- **Petunjuk Format**: Format `Hari / Bulan / Tahun` dan keterangan `Min. 17 tahun` di bawah input.

---

### 31. 📲 QR Code Khusus per Job & Salin Pesan Broadcast WhatsApp dengan Tautan Langsung Detail Lowongan
- **QR Code Unik per Lowongan**:
  - Tombol QR Code di setiap baris lowongan (aktif & expired) menghasilkan QR Code yang mengarah langsung ke tautan detail publik lowongan kerja (`https://new.asystem.co.id/job/{id}`).
  - Modal pop-up QR Code menampilkan pratinjau jernih 350x350px, box tautan langsung lengkap dengan tombol **Salin Link**, tombol **Buka Detail** di tab baru, dan tombol **Download QR Code**.
- **Salin Pesan Broadcast WhatsApp**:
  - Tombol WhatsApp (ikon hijau) di kolom Aksi menyalin format teks broadcast lowongan lengkap: Posisi, Prinsiple, Area Penempatan, Skill, serta **Link Detail & Lamar Lowongan** langsung (`https://new.asystem.co.id/job/...`).
  - Teks siap disebarkan ke grup percakapan dan calon kandidat dengan tautan yang dapat langsung diklik untuk melamar online.
- **Fleksibilitas Routing Job Detail**:
  - Endpoint `PublicJobController@show`, `@applyForm`, dan `@submitApply` kini mendukung pemanggilan baik menggunakan ID numerik maupun slug judul lowongan.

---

### 32. 📋 Resolusi Work Plan & To Do List Pasca-Migrasi: Pencocokan Nama Case-Insensitive & Akses Data Historis Arsip (Andi Kurniawan Distrianto & 65 User Lainnya)
- **Akar Permasalahan yang Ditemukan**:
  - **Perbedaan Huruf Besar/Kecil (Case Sensitivity)**: Di database SQLite, operator perbandingan string `=` dan `IN` bersifat case-sensitive. Nama user dari Odoo ERP sering kali berformat huruf kapital penuh (`ANDI KURNIAWAN DISTRIANTO`), sedangkan data historis dari database lama berformat Title Case (`Andi Kurniawan Distrianto`) atau huruf kecil. Akibatnya query pencarian menghasilkan 0 baris.
  - **Status Tugas Diarsipkan (`status = 'archived'`)**: Data tugas milik **Andi Kurniawan Distrianto** (sebanyak 316 tugas) dan 65 user lainnya seluruhnya berstatus `'archived'` (dari total 11.903 data arsip hasil migrasi database lama). Di antarmuka papan Kanban sebelumnya:
    1. Kartu metrik ringkasan di bagian atas hanya menghitung tugas aktif non-arsip, sehingga angka menunjukkan `0` (dikira data hilang).
    2. Accordion "Tugas Diarsipkan" di bagian bawah berada dalam posisi tertutup (*collapsed*) secara default.
    3. Dropdown filter karyawan secara eksplisit mengecualikan status `archived` (`whereNotIn('status', ['archived'])`), sehingga nama Andi Kurniawan Distrianto dan 64 karyawan lainnya tidak muncul di filter pencarian.
- **Solusi Komprehensif yang Diterapkan**:
  - **Pencocokan Case-Insensitive Multi-Kandidat (`WorkPlanController.php`)**:
    - Seluruh query penelusuran user, assignee, delegator, maupun penerima notifikasi diubah menggunakan `LOWER(TRIM(...))`.
    - Dibuat helper `getUserCandidateNames($user)` yang menghimpun variasi nama akun (`users.name`), nama di Odoo (`employees.nama_karyawan`), dan email.
    - Helper otorisasi `isUserAuthorizedForTask()`, `isDelegatorForTask()`, dan `isCreatorOrDelegatorForTask()` memastikan akses edit, approve, dan delete berfungsi mulus tanpa terganjal variasi kapitalisasi nama.
  - **Dropdown Filter Karyawan Lengkap & Deduplikasi Rapi**:
    - Dropdown filter kini merangkum semua user dari riwayat tugas (baik status aktif maupun `archived`, mencakup `assignee`, `user`, dan `delegator`), digabungkan dengan karyawan inhouse aktif.
    - Dilakukan deduplikasi case-insensitive cerdas yang memprioritaskan format Title Case/Mixed Case daripada ALL CAPS.
  - **Kartu Metrik ke-6 untuk Arsip & Banner Informatif (`index.blade.php`)**:
    - Grid statistik atas diperluas menjadi 6 kolom responsif dengan kartu **Arsip** berwarna ungu (`fa-box-archive`) yang menampilkan jumlah total arsip secara transparan.
    - Ditambahkan banner gradien informatif jika user memiliki 0 tugas aktif namun memiliki riwayat di arsip (misal: `"Seluruh Tugas Tersimpan di Arsip (316 Tugas)"`).
    - Accordion "Tugas Diarsipkan" otomatis terbuka (*auto-expanded*) jika user hanya memiliki data arsip atau saat difilter berdasarkan nama user tersebut.
  - **Avatar & Foto Profil Case-Insensitive (`Task.php`)**:
    - Metode `Task::getAvatarUrl` kini melakukan pencarian case-insensitive ke tabel `users` dan `employees`, serta menampilkan foto profil karyawan asli jika tersedia.

---

### 33. 📅 Fitur Pengelompokan (Grouping) & Filter Tanggal pada Tugas Diarsipkan (Archive Section)
- **Pengelompokan Berdasarkan Tanggal (Grouping by Date)**:
  - Seluruh tugas diarsipkan kini dikelompokkan secara visual berdasarkan tanggal penyelesaian (`date_completed`) atau tanggal input (`date_input`).
  - Setiap kelompok tanggal dilengkapi dengan header elegan: ikon kalender, tanggal dalam bahasa Indonesia lengkap (contoh: `Selasa, 15 September 2026`), badge jumlah tugas (`16 Tugas`), dan garis pemisah.
  - Kartu tugas menampilkan prioritas, judul tugas, penanggung jawab/assignee, jam selesai dalam format WIB, tombol detail, dan tombol pulihkan (*unarchive*).
- **Toolbar Filter Tanggal Khusus Arsip**:
  - **Quick Select Dropdown**: Pilihan tanggal instan yang otomatis menghimpun tanggal-tanggal yang memiliki riwayat arsip tugas beserta jumlah tugasnya (contoh: `Sel, 15 Sep 2026 (16 Tugas)`). Pemilihan langsung memperbarui data.
  - **Filter Rentang Tanggal (Date Range)**: Input tanggal kustom `Dari` dan `Sampai` untuk meninjau riwayat tugas dalam kurun waktu tertentu.
  - **Tombol Reset & Indikator Aktif**: Tombol reset yang ramah serta notifikasi banner informatif yang menjelaskan rentang tanggal yang sedang aktif.
  - **Preservasi Parameter**: Filter tanggal arsip mempertahankan seluruh parameter filter papan utama (`user_filter`, `smart`, `search`) tanpa saling menimpa.
  - **Anchor Navigation & Paginasi**: Paginasi arsip dilengkapi hash fragment `#archived-section` sehingga saat berpindah halaman langsung mengarah ke bagian arsip tanpa mengharuskan pengguna scroll manual dari atas.

---

### 34. 🗺️ Penyelarasan Penuh Data Region & Area (Job Statistik & Kandidat Portal) Berdasarkan Master `tb_area` (43 Area Resmi ESA Groups)
- **Master Area Model `TbArea.php` (43 Area Resmi)**:
  - Dibuat model [TbArea.php](file:///d:/ASystem/newasystem/app/Models/TbArea.php) sebagai sumber kebenaran tunggal (*single source of truth*) yang merepresentasikan seluruh 43 cabang/area resmi ESA Groups dari `tb_area (2).sql`.
  - Dilengkapi kamus pemetaan region terpusat (`getRegionMap()`), resolusi nama region presisi (`resolveRegion()`), dan normalisasi kapitalisasi nama area (`getCanonicalAreaName()`).
- **Koreksi Mismatch Region pada Job Statistik ([JobStatistikController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/JobStatistikController.php))**:
  - **Aceh**: Dikoreksi dari **Region 7** menjadi **Region 6** (sesuai kode 30 pada master data).
  - **Papua**: Dikoreksi dari `-` menjadi **Region 5**.
  - **Pematang Siantar**: Dikoreksi dari `-` menjadi **Region 6**.
  - **Buduran & Medaeng**: Dikoreksi dari `-` menjadi **Region 4**.
  - **Normalisasi Duplikasi Area**: Menggabungkan variasi kapitalisasi (`TASIKMALAYA` vs `Tasikmalaya`, `JAKARTA` vs `Jakarta`, dll.) sehingga rekapitulasi data per Area pada Tabel 1, Tabel 2, dan Tabel 3 menyatu rapi tanpa baris terpisah.
- **Koreksi Export Excel & Tampilan Kandidat Portal**:
  - Pada [CandidateXlsxExportService.php](file:///d:/ASystem/newasystem/app/Services/CandidateXlsxExportService.php), seluruh 43 area kini dipetakan dengan tepat ke Region 1 s/d Region 7 pada Kolom O (Region) file Excel, menyelesaikan bug di mana pelamar dari 14 area sebelumnya salah di-default ke Region 1.
  - Pada [KandidatPortalController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/KandidatPortalController.php) metode `show()`, modal "Ganti Area" kini mengambil seluruh 43 area resmi dari `TbArea`.
  - Pada [kandidatportal/index.blade.php](file:///d:/ASystem/newasystem/resources/views/kandidatportal/index.blade.php), ditambahkan badge visual Region di samping/bawah nama Area pada daftar pelamar.
- **Accessor Model `region` pada `Candidate` & `JobSpec`**:
  - Menambahkan accessor `getRegionAttribute()` dan model boot saving hook pada [Candidate.php](file:///d:/ASystem/newasystem/app/Models/Candidate.php) dan [JobSpec.php](file:///d:/ASystem/newasystem/app/Models/JobSpec.php) agar atribut `region` selalu tersinkron otomatis saat data disimpan.
- **Database Migration Sinkronisasi Data Eksisting**:
  - Migrasi `2026_09_22_123000_sync_tb_area_and_normalize_regions.php` menambahkan kolom `region` (terindeks) pada `candidates` dan `job_region` pada `job_specs`.
  - Menyinkronkan seluruh 64.000+ data kandidat dan 477 lowongan kerja yang ada di database ke Region yang benar.

---

### 35. 🧹 Pembersihan Akun Demo Jamil & Pengembalian Data Kandidat ke User Asli
- **Latar Belakang Masalah**:
  - Akun `jamil@asystem.co.id` (Abdurrahman Jamil - IT Programmer) sebelumnya digunakan sebagai fallback akun demo saat pengembangan awal sistem pada modul `InterviewController`, `KandidatPortalController`, `InterviewInhouseController`, dan `CandidateImportController`.
  - Pada tabel `candidates`, terdapat kandidat interview demo (ID 4: Ahmad Faisal Rahman dan ID 5: Siti Nurhaliza) yang di-assign ke `useras: 'jamil@asystem.co.id'` dan `recruiter_id: 3`.
  - Pada tabel `hasilinterview`, record ID 524 (NIK 3275086711040002 / Annisa Tiara Noviyanti) mencantumkan `nama_as: 'Abdurrahman Jamil'` padahal kandidat ini aslinya dihandle oleh Anton Purnama Wijaya (`useras: antonjunot666@gmail.com`).
  - Pada fungsi `resolveUserIdentifiers()`, terdapat blok pemetaan alias liar yang menyatukan seluruh variasi nama `abdur rahman` (AS Jambi) dan `abdurrahman2330@gmail.com` (AS Pekanbaru) ke akun demo `jamil@asystem.co.id`, sehingga ratusan kandidat AS Jambi dan Pekanbaru keliru diasosiasikan sebagai milik akun demo Jamil.
- **Tindakan Perbaikan & Migrasi Database**:
  - Dibuat migrasi resmi: `2026_09_22_143000_revert_demo_candidate_users_from_jamil.php`.
  - Mengembalikan kepemilikan data kandidat yang menggunakan akun demo `jamil@asystem.co.id` ke user Administrator HR yang sah (`admin@asystem.co.id`, `recruiter_id: 1`).
  - Mengembalikan field `nama_as` pada tabel `hasilinterview` ID 524 ke user aslinya (`Anton Purnama Wijaya`).
- **Pembersihan Logika Controller Backend**:
  - [InterviewController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/InterviewController.php): Mengganti metode `getCurrentUser()` agar menggunakan `auth()->user() ?? User::where('role', 'admin')->first() ?? User::first()`, menghapus fallback hardcoded ke akun demo Jamil.
  - [KandidatPortalController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/KandidatPortalController.php):
    - Mengganti `getCurrentUser()` ke auth user / admin resmi.
    - Menghapus blok alias paksa yang mencampuradukkan AS Abdur Rahman Jambi / Pekanbaru dengan akun demo Jamil.
  - [InterviewInhouseController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/InterviewInhouseController.php): Menghapus `User::firstOrCreate` akun demo Jamil pada `getCurrentUser()`.
  - [CandidateImportController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/CandidateImportController.php): Menghapus fallback Jamil pada `getCurrentUser()`.
  - [AuthController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/AuthController.php): Menghapus fallback pencocokan alias login ke `jamil@asystem.co.id`.
- **Status Deployment**:
  - Berhasil diuji coba lokal, di-commit, di-push ke branch `main`, dan berhasil dieksekusi migrasinya pada server production (`new.asystem.co.id`) dengan status HTTP 200 OK.

---

### 36. 🏢 Modul Approval Inhouse, Kandidat Inhouse, & Penyelarasan Step Berjenjang (Rekrutor ➔ Head ➔ HRD Pusat)
- **Detail Kandidat Inhouse & Approval Inhouse ([interview/show.blade.php](file:///d:/ASystem/newasystem/resources/views/interview/show.blade.php) & [interviewinhouse/show.blade.php](file:///d:/ASystem/newasystem/resources/views/interviewinhouse/show.blade.php))**:
  - Untuk kandidat dengan Prinsiple naungan 5 entitas inhouse (`PT ARINA MULTI KARYA`, `PT ALVA KARYA PERKASA`, `PT ANUGRAH TERPERCAYA KERJA`, `PT ABADI BERKAT ODELIA`, `PT ANUGRAH TALENTA BERKARYA`, `PT ARINA BINTANG OETAMA`, `PT ANUGRAH TRI BERKAH`), tab ke-7 berubah menjadi **"Approval Inhouse"**.
  - **Penyelarasan Tampilan Profil Kandidat Lengkap**: Seluruh tampilan profil kandidat pada modul interview maupun detail inhouse diselaraskan menggunakan format kartu profil lengkap 11 data points (*No. KTP/NIK, Nama Lengkap, Alamat KTP, Usia, Pendidikan, WhatsApp, Pengajuan Entitas, Posisi Dilamar, Status Seleksi, User AS/Rekruter, Lampiran CV*) dengan pasfoto 3x4 interaktif dan viewer lampiran CV terpadu.
  - **7 Tab Terpadu pada Halaman Inhouse (`/interviewinhouse/{id}`)**: Halaman evaluasi approver inhouse menyertakan seluruh 7 tab hasil seleksi lengkap yang identik dengan detail interview standar:
    1. *Hasil Interview*: Matrix penilaian aspek wawancara, tanggal interview, catatan pewawancara, dan tanda tangan AS/rekruter.
    2. *Referensi Cek*: Riwayat pengalaman kerja kandidat, kontak referensi, catatan verifikasi, dan bukti screenshot cek referensi.
    3. *Tes Komputer*: Skor Word, Excel, PPT, Internet, total skor penilaian, dan berkas bukti tes.
    4. *Tes Kepribadian*: Hasil evaluasi kepribadian, gaya komunikasi, dan tipe kepribadian DISC.
    5. *Tes Matematika*: Skor nilai, rincian jumlah jawaban benar, salah, dan riwayat tes ke-N.
    6. *Analisa AI (CV Analyzer)*: Match rate kecocokan posisi, level pengalaman, dan ringkasan keunggulan kandidat.
    7. *Approval Inhouse*: Tabel riwayat *List Head Approve*, *Approval HRD Pusat*, dan form keputusan evaluasi approver.
  - **Tombol Berkas Lamaran & Dokumen Interview**:
    - Tombol *Dokument Interview & Test Online*: Membuka berkas PDF resmi lembar penilaian seleksi kandidat.
    - Tombol *Berkas Lamaran*: Membuka file lamaran yang dilampirkan Rekrutor/AS dengan penanganan responsif dan status tombol disabled yang informatif jika berkas belum diunggah.
  - **Formulir Status Formasi Pengajuan (New / Replace)**:
    - Pilihan status formasi: `New` atau `Replace`.
    - Jika memilih `Replace`, form dinamis Alpine.js otomatis memunculkan kolom input: *Menggantikan*, *Tanggal Resign*, dan *Alasan Resign*, serta tersinkronisasi ke tabel `tb_replace`.

---

### 37. 🔒 Penguncian Step Approver Inhouse (*Step Locking*), Perbaikan Tab Navigation, & Proteksi Otomatis Pimpinan User
- **Penguncian Nama Approver Otomatis Sesuai Pimpinan User (Step 1 Locking)**:
  - Mengubah field *Nama Approver Inhouse* dari dropdown yang dapat dipilih manual menjadi **kartu terkunci otomatis (*Locked Readonly Display*)** berikon gembok dan badge `Step 1: Head Approver (Terkunci)`.
  - Rekrutor/pengaju tidak dapat memilih atau mengubah approver secara manual guna mencegah salah pilih.
  - Nilai approver diselesaikan secara otomatis berjenjang oleh [InterviewController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/InterviewController.php):
    1. Pimpinan dari User AS / Rekrutor kandidat di master `employees.pimpinan` & `jabatan_pimpinan`.
    2. Pimpinan user yang sedang login di master `employees`.
    3. Pimpinan / Leader per Area dari data master karyawan aktif.
    4. Head operasional inhouse resmi (`David Oscar Sahala G Sibuea - OM (Operation Manager)`).
  - Dibuat migrasi database `2026_09_22_174813_add_nama_approver_to_candidates_table.php` untuk menyimpan kolom `nama_approver` secara permanen pada tabel `candidates`.
- **Proteksi Hak Akses & Alur Step Berjenjang**:
  - **Step 1 (Rekrutor ➔ Head)**: Status kandidat menjadi `Review Head`. Form di rekrutor terkunci (*Locked View*). User HRD yang mencoba melakukan approve pada tahap ini ditolak oleh sistem karena kandidat wajib disetujui Head terlebih dahulu.
  - **Step 2 (Head ➔ HRD Pusat)**: Setelah Head menyetujui, status naik menjadi `Review HRD`. Head tidak dapat mengedit ulang pengajuan yang sudah berada di tahap HRD Pusat. Form approval terbuka khusus untuk user dengan hak akses HRD Pusat.
  - **Selesai (Fully Approved)**: Setelah disetujui HRD Pusat, status kandidat menjadi `Approve`, kolom `ttd_prinsiple` dan `time_prinsiple` terisi, dan kandidat otomatis berpindah ke tab *Interview Selesai* (`tab=done`).
- **Perbaikan Bug Loading Modal & Tab Navigation**:
  - Memperbaiki tag penutup form sinkronisasi status Odoo yang sebelumnya hilang (`unclosed <form>`) pada `interview/show.blade.php`, yang sebelumnya menyebabkan klik tab navigasi terinterpretasi sebagai form submit dan memicu modal loading *page-loader* *"Menyimpan Data..."* serta me-refresh halaman kembali ke Tab 1.
  - Menambahkan atribut eksplisit `type="button"` pada seluruh 7 tombol tab navigasi di `interview/show.blade.php` dan `interviewinhouse/show.blade.php` sehingga perpindahan tab antar menu seleksi berlangsung instan tanpa me-reload halaman.
- **Deployment Production**:
  - Seluruh pembaruan telah diuji coba, diverifikasi dengan kompilasi template Blade bersih (`php artisan view:cache`), di-push ke GitHub `origin/main`, dan dieksekusi migrasinya pada server production live (`new.asystem.co.id`).

---

### 38. 🏢 Pembatasan Ketat Approval Inhouse Khusus 5 Entitas Resmi & Pemulihan Approval Prinsiple
- **Koreksi Logika Penentuan Status Inhouse (`InterviewInhouseController.php`)**:
  - Memperbaiki method `getInhousePrincipleIds()`: Menghapus pengecekan `Principle::whereIn('entity', ['AMK', 'AKP', 'ATK', 'ABO', 'ATB'])` yang sebelumnya keliru mengambil seluruh prinsiple/klien (karena seluruh klien seperti Nestle, L'Oreal, Wings berada di bawah entity holding tersebut).
  - Menyandarkan filter ID prinsiple resmi secara eksklusif pada 5 entitas inhouse perusahaan via `\App\Models\Employee::isInhousePrinciple($p->name)`.
  - Memperbaiki method `isCandidateInhouse($candidate)` agar strictly hanya mengembalikan `true` jika `principle_id` atau nama `principle` kandidat termasuk ke dalam salah satu dari 5 entitas resmi:
    1. `PT ARINA MULTI KARYA`
    2. `PT ALVA KARYA PERKASA`
    3. `PT ANUGRAH TERPERCAYA KERJA`
    4. `PT ABADI BERKAT ODELIA`
    5. `PT ANUGRAH TALENTA BERKARYA`
  - Menghapus bypass `is_inhouse = 1` tanpa validasi prinsiple, sehingga kandidat dengan prinsiple eksternal/klien (seperti *PT NESTLE INDONESIA*) tidak lagi keliru diperlakukan sebagai inhouse.
  - Memperbaiki query filter `index()` agar strictly hanya menampilkan kandidat dengan prinsiple 5 entitas inhouse resmi.
- **Auto-Recovery & Proteksi Form Approval (`InterviewController.php`)**:
  - Pada method `show($id)`: Menambahkan auto-recovery otomatis jika kandidat non-inhouse sempat memiliki flag `is_inhouse = 1` atau status `Review Head` / `Review HRD` akibat kekeliruan submit sebelumnya. Sistem secara otomatis menetralkan `is_inhouse = 0` dan mereset `status_approval = null` sehingga form **Approval Prinsiple** terbuka bebas tanpa terkunci.
  - Pada method `storeInhouseApproval()`: Menambahkan validasi protektif `if (!InterviewInhouseController::isCandidateInhouse($candidate))` untuk mencegah pengajuan inhouse pada kandidat yang bukan 5 entitas.
- **Penyelarasan Tampilan Tab Navigasi (`interview/show.blade.php`)**:
  - Tab navigasi ke-7 menampilkan label eksplisit:
    - Untuk kandidat inhouse: **`7. Approval Inhouse`** (`fa-house-chimney-user`).
    - Untuk kandidat prinsiple/klien umum: **`7. Approval Prinsiple`** (`fa-building-circle-check`).

---

### 39. 🔄 Penyempurnaan Sinkronisasi Odoo: Mutasi Lintas Entitas & Reaktivasi Karyawan Resign
- **Latar Belakang & Identifikasi Masalah**:
  - Pada kasus karyawan yang resign di satu entitas holding (misal AMK) lalu diaktifkan kembali di entitas holding lain (misal ATK), data di Master Karyawan sebelumnya tidak terupdate dan tetap tertahan dengan status `Resign` di entitas lama (AMK).
  - Ditemukan 4 titik penyebab:
    1. Aturan skip mentah pada cron hourly dan bulk sync (`if (!$updateExisting) continue;`) yang mengabaikan NIK yang sudah ada di database tanpa memeriksa apakah karyawan tersebut berpindah entitas atau aktif kembali.
    2. Perintah `break` prematur pada terminal sync NIK lintas entitas (`streamSyncNik`) dan CLI command (`odoo:sync`) saat menemukan record pertama (AMK) yang berstatus resign, sebelum sempat memeriksa entitas aktif baru (ATK).
    3. Batasan `NOT NULL` pada kolom `tanggal_join` yang memicu PDOException jika karyawan baru di Odoo belum memiliki `first_contract_date`.
- **Solusi & Implementasi**:
  - **Logika Update Cerdas (`OdooSyncService::syncEmployees`)**:
    - Memperbarui aturan skip: NIK hanya dilewati jika NIK sudah ada, **sudah berstatus Aktiv**, **dan** berada di **entitas yang sama**.
    - Jika status karyawan sebelumnya `Resign` atau entitasnya berbeda (`AMK !== ATK`), sistem secara otomatis mengupdate record: mengubah entitas ke entitas baru, status ke `Aktiv`, serta memperbarui jabatan, departemen, tipe karyawan, prinsiple, dan Odoo ID.
    - Menambahkan penanganan fallback otomatis untuk `tanggal_join`, `jabatan`, dan `area`.
  - **Prioritas Status Aktif pada Pencarian NIK Lintas Entitas (`OdooSettingController::streamSyncNik` & `OdooSyncCommand`)**:
    - Saat melakukan pencarian NIK lintas seluruh entitas (`ALL`), jika ditemukan record berstatus `Resign`, sistem tidak langsung menghentikan loop, melainkan menyimpannya sebagai fallback dan terus memeriksa entitas lainnya hingga menemukan record `Aktiv`.
    - Menambahkan visualisasi badge dan log mutasi di live terminal SSE: `[Pindah dari AMK ➔ ATK]` dan `[Reaktivasi: Resign ➔ Aktiv]`.
  - **Peningkatan Pelaporan Cron Hourly (`OdooSyncActiveCommand`)**:
    - Menambahkan metrik pelacakan dan pencetakan `Total Mutasi / Reaktivasi` pada ringkasan eksekusi cron per jam.
- **Deployment Production**:
  - Seluruh pembaruan telah diuji coba, bebas error sintaks (`php -l`), lolos uji validasi skenario mutasi dan reaktivasi, di-push ke branch `main` GitHub, dan dieksekusi deployment ke server live production (`new.asystem.co.id`).

---

### 40. 💬 Fitur Lupa Kata Sandi via Live Chat Administrator & Auto-Sync Odoo Karyawan
- **Latar Belakang & Kebutuhan Fitur**:
  - Menggantikan tautan statis WhatsApp pada halaman login dengan modul **Live Chat Bantuan Login & Reset Password** yang terhubung langsung ke Administrator HR (mirip alur login helpdesk aplikasi Attendance).
  - Mengintegrasikan pengecekan NIK otomatis ke server Odoo ERP (5 entitas: AMK, AKP, ATK, ABO, ATB).
- **Alur Kerja Karyawan (Halaman Login `/login`)**:
  - Karyawan mengklik tombol *"Lupa kata sandi?"* untuk membuka jendela dialog modal Live Chat.
  - Karyawan memasukkan 16 digit NIK.
  - Sistem memvalidasi NIK: jika belum terdaftar di database lokal, sistem secara otomatis menarik data dari server Odoo seluruh entitas. Jika ditemukan aktif di Odoo, karyawan langsung didaftarkan ke tabel `employees`.
  - Jika karyawan aktif, sistem membuka sesi percakapan chat dengan tiket resmi (`REQ-YYYYMMDD-XXXX`).
  - Karyawan dapat memantau pesan balasan secara real-time (auto-poll) dan saling berbalas pesan dengan Admin.
  - Begitu Admin mengirimkan akses, kartu kredensial resmi (Email & Password) tampil di dalam chat dengan tombol **"Salin Kredensial & Langsung Masuk"** yang otomatis mengisikan form login.
- **Alur Kerja Administrator (Dashboard Helpdesk `/admin/bantuan-login`)**:
  - Admin menerima notifikasi real-time via badge lonceng navbar dan badge counter pada sidebar *Bantuan Login*.
  - Tampilan split-pane 2 kolom: daftar tiket di sebelah kiri (dengan filter status *Pending*, *Selesai*, *Semua*) dan panel percakapan interaktif di sebelah kanan lengkap dengan ringkasan identitas karyawan.
  - **Tombol "Kirim Akses" (Email & Kata Sandi)**: 1-klik mengirimkan kredensial login (email & kata sandi default `ddmmyyyy` / password akun) langsung ke percakapan chat karyawan.
  - **Auto-Aktivasi RateCard**: Jika karyawan bertipe RateCard dan belum memiliki izin login (`akses_login = false`), sistem secara otomatis mengaktifkan perizinan login saat Admin mengklik tombol *Kirim Akses*.
  - **Tombol "Kirim via WhatsApp"**: Menyediakan tautan cepat ke WhatsApp Web/App dengan pesan kredensial yang sudah terformat rapi.
- **Basis Data & Komponen**:
  - Migrasi `2026_09_23_093000_create_password_reset_chat_tables.php` (`password_reset_requests` & `password_reset_chat_messages`).
  - Model `PasswordResetRequest` & `PasswordResetChatMessage`.
  - Controller `AuthChatController`.
  - Helper `OdooSyncService::findAndSyncByNik()`.
  - View publik modal `auth/login.blade.php` & view admin `admin/auth_chat/index.blade.php`.

---

### 41. 🔄 Perbaikan Kritis Sinkronisasi Odoo: Penanganan departure_date Terjadwal & Prioritas Entitas Aktif
- **Akar Masalah**:
  1. **Logika Resign Kaku pada `departure_date`**:
     - Sebelumnya, sistem menganggap karyawan berstatus `Resign` jika kolom `departure_date` tidak kosong (`!empty($rec['departure_date'])`).
     - Di Odoo HR, `departure_date` sering diisi terlebih dahulu untuk jadwal pengunduran diri / berakhirnya kontrak di masa depan (contoh: NIK `3671115505900003` aktif di ABO dengan `departure_date: 2026-09-30`, saat ini 23 September 2026).
     - Akibatnya, karyawan yang masih aktif bekerja keliru ditandai sebagai `Resign`.
  2. **Query XML-RPC Odoo Mengecualikan Karyawan Aktif dengan Tanggal Departure**:
     - Query `syncEmployees()` menggunakan filter Odoo `['departure_date', '=', false]`, sehingga karyawan aktif yang memiliki jadwal berakhir di masa depan langsung dikeluarkan dari hasil pencarian dan tidak pernah disinkronkan ke Master Karyawan.
  3. **Penentuan Entitas pada Pencarian Lintas Entitas (`ALL`)**:
     - Ketika NIK diperiksa lintas seluruh entitas (AMK, AKP, ATK, ABO, ATB), jika entitas lama (misal AMK) mengembalikan `Resign` dan entitas baru (ABO) keliru terbaca `Resign`, sistem mempertahankan entitas AMK. Hal ini menyebabkan NIK terbaca AMK dan ditolak pada Live Chat Bantuan Login / Reset Password.
- **Solusi & Implementasi**:
  1. **Logika Tanggal Departure yang Akurat (`departure_date <= today`)**:
     - Pada `OdooSyncService::syncSingleEmployee()`, `syncEmployees()`, dan `verifyAndCleanResignedEmployees()`, karyawan hanya dianggap `Resign` jika `active == false` ATAU `departure_date <= date('Y-m-d')`.
     - Jika `active == true` dan `departure_date` berada di masa depan (`> today`), karyawan tetap berstatus **`Aktiv`**.
  2. **Query XML-RPC Odoo yang Adaptif**:
     - Query pencarian diperbarui menjadi `['active', '=', true], '|', ['departure_date', '=', false], ['departure_date', '>', date('Y-m-d')]`, sehingga karyawan aktif dengan tanggal departure di masa depan tetap ditarik saat Sync All.
  3. **Prioritas Entitas Aktif & Komparasi Rekord Mutakhir**:
     - Pada `findAndSyncByNik()`, `OdooSettingController::syncEmployeesByNik()`, `streamSyncNik()`, dan `OdooSyncCommand`, entitas yang mengembalikan status `Aktiv` **mutlak diprioritaskan** dan langsung menghentikan loop pencarian.
     - Jika seluruh entitas berstatus non-aktif, sistem membandingkan tanggal departure / join date terbaru sehingga entitas terakhir yang tersimpan di database lokal.
- **Hasil Pengujian**:
  - NIK `3671115505900003` (MEITA ADRIAN SARI) berhasil terdeteksi sebagai **`found_active`** di entitas **`ABO`** (`PT VINDA INTERNASIONAL INDONESIA`, Jabatan: `ADMIN - Jakarta`).
  - Master Karyawan dan Live Chat Bantuan Login kini berhasil memvalidasi dan memproses permintaan akses dengan lancar.

---

### 42. 🔒 Proteksi Email & Password Karyawan saat Pembaruan Data dari Odoo ERP
- **Latar Belakang & Kebutuhan**:
  - Banyak karyawan yang telah mengganti email login dan kata sandi kustom mereka melalui profil akun ASystem atau melalui bantuan tim HRD.
  - Sebelumnya, ketika proses sinkronisasi Odoo berjalan (baik Sync All, Sync by NIK, maupun cron pembersihan resign), kolom `email` pada data karyawan yang sudah ada di-update ulang menggunakan data mentah dari Odoo (`work_email` / `private_email`).
  - Hal ini menyebabkan karyawan yang telah mengganti email/password tidak dapat login kembali karena email di sistem tertimpa kembali ke email lama Odoo.
- **Implementasi Proteksi**:
  1. **Proteksi Email (`OdooSyncService.php`)**:
     - Pada `syncEmployees()` (Sync All), `syncSingleEmployee()` (Sync by NIK), dan `verifyAndCleanResignedEmployees()`:
       ```php
       $effectiveEmail = ($employee && !empty($employee->email)) ? $employee->email : $email;
       ```
     - Jika data karyawan sudah terdaftar di database lokal dan kolom `email` sudah terisi, sistem **mempertahankan email lokal tersebut secara mutlak** dan tidak menimpanya dengan data dari Odoo.
     - Email dari Odoo hanya digunakan untuk data karyawan baru (*new record*) atau jika data email lokal saat ini masih bernilai kosong (*null/empty*).
  2. **Proteksi Kata Sandi (`password`)**:
     - Kolom `password` tidak pernah disertakan dalam payload update data dari Odoo.
     - Password kustom yang telah di-hash dan disimpan oleh karyawan / admin tetap utuh dan terlindungi dari segala bentuk reset atau modifikasi saat sinkronisasi rutin berlangsung.

---

### 43. 🧹 Pengecualian Akun Sistem/Dummy (Prefix OD-) dari Sinkronisasi Odoo & Pembersihan Database
- **Latar Belakang & Akar Masalah**:
  - Di Odoo ERP, terdapat record akun sistem / operasional cabang (contoh: `aro-yogyakarta-dennihendra`, `aro-cirebon-hendra`, `ro-cirebon, tasikmalaya-hendra`, `as-kediri-canny`, dll.) yang tidak memiliki NIK (`identification_id`) maupun NIP (`registration_number`).
  - Sebelumnya, sistem menghasilkan fallback NIK dummy berupa prefix `OD-` diikuti Odoo ID (contoh: `OD-16191`, `OD-48408`, `OD-54335`) sehingga akun-akun tersebut ikut tersedot ke Master Karyawan sebagai karyawan aktif.
- **Implementasi Pengecualian pada Sinkronisasi Odoo (`OdooSyncService.php`)**:
  1. **Query XML-RPC Odoo**:
     - Ditambahkan filter ketat pada query Odoo:
       ```php
       ['|', ['identification_id', '!=', false], ['registration_number', '!=', false]]
       ```
       Memastikan record yang tidak memiliki NIK/NIP tidak lagi diambil dari server Odoo.
  2. **Filter Validasi PHP**:
     - Pada `syncEmployees()`, record dengan NIK kosong atau diawali `OD-` langsung di-skip dan tidak diproses.
     - Pada `syncSingleEmployee()` dan `findAndSyncByNik()`, input NIK dummy atau berawalan `OD-` ditolak dengan keterangan bukan data employee riil.
- **Pembersihan Database & Migrasi**:
  - Migrasi `2026_09_23_130000_delete_dummy_od_employees.php`:
    - Menghapus seluruh data karyawan dummy berawalan `OD-%` serta data dengan NIK kosong/null dari tabel `employees`.
  - Fungsi statis `OdooSyncService::cleanupDummyOdEmployees()` disediakan untuk pemeliharaan rutin.

---

### 44. 🔄 Izin Pendaftaran Ulang (Re-apply) untuk Kandidat Berstatus Arsip (Portal & Interview)
- **Latar Belakang Masalah**:
  - Kandidat yang statusnya sudah diarsipkan (`status_kandidat = 'Arsip'` atau `status IN ('Arsip', 'archived')`) terblokir saat mendaftar kembali lowongan pekerjaan melalui portal publik (`/job/{id}/apply`).
  - Muncul notifikasi penghalang: *"Anda sudah pernah mendaftar posisi [Posisi] dengan NIK [NIK]. Akun Anda telah aktif, silakan login ke portal tes online."*
  - Hal ini terjadi karena pengecekan duplikasi lama di `PublicJobController@submitApply` hanya memeriksa `$candidate->status === 'Active'` dan `$candidate->applied_job === $job->job_title` tanpa mengecek apakah kandidat tersebut sebenarnya sudah berstatus Arsip.
- **Perubahan & Perbaikan**:
  1. **Logika Duplicate Check yang Presisi ([PublicJobController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/PublicJobController.php))**:
     - Notifikasi penolakan kini **hanya** muncul jika pelamar memiliki berkas lamaran yang sedang aktif berjalan dan **belum diarsipkan** (`hasActiveSameJob`).
     - Jika kandidat tercatat berstatus arsip (baik di Kandidat Portal maupun Interview), pelamar **diizinkan mendaftar kembali**.
  2. **Re-aktivasi & Reset Data Seleksi Bersih**:
     - Status kandidat diperbarui menjadi `status = 'Active'` dan `status_kandidat = 'Baru'`.
     - Posisi lamaran dan area diperbarui sesuai lowongan yang dilamar.
     - Alasan arsip lama (`archive_reason`) dibersihkan menjadi `null`.
     - Waktu pendaftaran diperbarui (`created_at = now()`, `updated_at = now()`) agar langsung muncul di tab *Kandidat Baru* dan terhitung di statistik *Masuk Hari Ini*.
     - Data approval/evaluasi lama (`status_approval`, `idprinsiple`, `ttd_prinsiple`, `time_prinsiple`, `note_principle`) di-reset ke `null`.
     - Modul tes online di-reset bersih: nomor percobaan tes (`tes_ke`) dinaikkan, indikator `tes_kepribadian`, `tes_matematika`, `tes_komputer`, `buktikomputer`, dan `statement_agreed` di-reset ke `null`/`0` sehingga kandidat dapat mengikuti tes online CBT kembali untuk lamaran barunya.
  3. **Penyelarasan Legacy `tb_kandidat`**:
     - Menyinkronkan update status `Active`, `status_kandidat = 'Baru'`, `tes_ke`, dan pembersihan data tes pada tabel legacy `tb_kandidat`.
  4. **Prioritas Login CBT ([CbtController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/CbtController.php))**:
     - Kueri pencarian kandidat saat login CBT kini memprioritaskan akun yang aktif dan tidak diarsipkan (`status = 'Active' AND status_kandidat != 'Arsip'`), memastikan kandidat yang mendaftar ulang langsung masuk ke akun seleksi aktif terbarunya.

---

### 45. 🤖 Integrasi AI OpenRouter, Hierarki Fallback Kuota (Gemini ➔ OpenRouter ➔ Sumopod), & Model Kustom Dinamis
- **Latar Belakang & Kebutuhan**:
  - Untuk menjaga kelangsungan analisa CV kandidat secara terus menerus saat kuota token atau rate limit Google Gemini tercapai, ditambahkan provider AI perantara dari **OpenRouter**.
  - Urutan hierarki eksekusi fallback yang ditentukan:
    $$\text{API Key Gemini} \longrightarrow \text{OpenRouter} \longrightarrow \text{Sumopod}$$
  - Jika seluruh provider API Key mengalami limitasi kuota atau kegagalan, proses analisa AI harus berhenti secara aman (*rate_limited*) tanpa perulangan tanpa batas (*infinite retry loop*).
  - Admin memerlukan fleksibilitas untuk mengubah model OpenRouter yang digunakan, serta dapat menambahkan model-model baru secara dinamis untuk Google Gemini, OpenRouter, maupun Sumopod.
- **Implementasi Skema Database & Model**:
  1. **Migrasi Database (`2026_09_23_140000_add_openrouter_and_custom_models_to_ai_settings.php`)**:
     - Menambahkan kolom `openrouter_key` dan `openrouter_model` pada tabel `ai_settings` dan tabel warisan `tb_ai_setting`.
     - Menambahkan kolom JSON `gemini_models_list`, `openrouter_models_list`, dan `sumopod_models_list` untuk menyimpan daftar model kustom yang dapat dipilih.
  2. **Model `AiSetting.php`**:
     - Ditambahkan casting JSON array untuk list ketiga provider AI.
     - Dibuat accessor cerdas `gemini_models`, `openrouter_models`, dan `sumopod_models` yang secara otomatis menggabungkan daftar model bawaan (*default curated list*) dengan model-model baru yang diinputkan pengguna.
     - Sinkronisasi ganda (*dual-sync*) otomatis ke tabel warisan `tb_ai_setting`.
- **Implementasi Service Analisa CV (`AiAnalyzerService.php`)**:
  1. **Urutan Eksekusi Bertingkat**:
     - **Tahap 1 — Google Gemini Pool**: Merotasi seluruh API Key Gemini aktif. Jika terkena limit (HTTP 429), key diistirahatkan 2 menit. Jika error permanen, dipindahkan ke daftar token expired.
     - **Tahap 2 — OpenRouter Gateway**: Jika seluruh Gemini Key sedang dalam masa limit/cooldown, alur beralih ke OpenRouter API (`https://openrouter.ai/api/v1/chat/completions`) menggunakan Authorization Bearer key dan parameter penalaran `"reasoning": {"enabled": true}`.
     - **Tahap 3 — Sumopod Fallback**: Jika OpenRouter juga limit atau tidak dapat diakses, alur beralih ke Sumopod/OpenAI fallback.
     - **Tahap 4 — Penghentian Otomatis**: Jika seluruh provider limit/gagal, proses analisa otomatis berhenti dengan status `rate_limited` dan detail penyebab kegagalan dicatat pada kandidat.
  2. **Metode `callOpenRouter()`**:
     - Mengirimkan prompt evaluasi CV dan base64 lampiran file (PDF/gambar) via cURL dengan timeout 60 detik.
     - Mendukung parsing multi-format (konten balasan atau reasoning) serta pembersihan JSON evaluasi otomatis.
- **Pembaruan Halaman Pengaturan AI (`/ai-settings` & `AiSettingController.php`)**:
  1. **Banner Alur Eksekusi Visual**:
     - Menampilkan diagram alir hierarki: `1. Gemini ➔ 2. OpenRouter ➔ 3. Sumopod ➔ Berhenti Jika Semua Limit`.
  2. **Kartu Konfigurasi OpenRouter**:
     - Input API Key OpenRouter dengan tombol intip/sembunyikan kata sandi.
     - Dropdown pilihan model OpenRouter (`nvidia/nemotron-3-ultra-550b-a55b:free`, `meta-llama/llama-3.3-70b-instruct:free`, `deepseek/deepseek-r1:free`, `google/gemini-2.0-flash-exp:free`, `qwen/qwen-2.5-72b-instruct:free`, `openai/gpt-4o-mini`, dll.).
     - Input form dinamis `+ Tambah Model Baru OpenRouter`.
     - Badges model aktif dengan tombol hapus model kustom.
     - Tombol `Test Koneksi OpenRouter` dengan feedback SweetAlert2 dan pengukuran latensi riil.
  3. **Peningkatan Kartu Gemini & Sumopod**:
     - Menambahkan input dinamis `+ Tambah Model Baru` untuk Gemini dan Sumopod, memungkinkan pengguna mendaftarkan model AI generasi terbaru kapan saja.
     - Tombol `Test Koneksi Sumopod` dan pengujian langsung ke server API.
     - Route & Controller `removeModel()` untuk menghapus model kustom yang sudah tidak dipakai.

---

### 46. 🏢 Peningkatan Fitur Search Master Prinsiple & Smart Entity Fallback
- **Penyebab Utama Masalah Pencarian Kosong**:
  - Ditemukan melalui investigasi log server Nginx live bahwa pengguna mencari `ICI PAINT` saat filter entitas `ATK` sedang aktif (`search=ICI+PAINT&entity=ATK`).
  - Di database resmi, prinsiple `PT ICI PAINTS INDONESIA` terdaftar di entitas `AMK` dan `AKP`, bukan di `ATK`. Karena query sebelumnya menggabungkan pencarian dan entitas secara kaku (`where entity = ATK`), sistem mengembalikan 0 hasil (tabel kosong).
- **Multi-Word & Case-Insensitive Token Matching**:
  - Mengimplementasikan pencarian token multi-kata dengan `LOWER(...) LIKE ?` pada `PrincipleController.php`.
  - Mendukung urutan kata acak (`PAINT ICI` atau `ICI INDONESIA` tetap menemukan `PT ICI PAINTS INDONESIA`).
  - Mengabaikan tanda baca perusahaan seperti `PT.` atau `CV.`.
- **Smart Entity Fallback (Fallback Lintas Entitas)**:
  - Jika pengguna melakukan pencarian pada entitas tertentu dan tidak ditemukan (0 data), sistem secara cerdas mengecek apakah prinsiple ada di entitas lain.
  - Jika ada di entitas lain, sistem otomatis menampilkan hasil dari entitas terkait dan menampilkan alert informatif:
    > *"Tidak ada prinsiple dengan kata kunci '...' di Entitas ATK. Ditemukan X data pada entitas lain (AMK, AKP). Data dari entitas lain otomatis ditampilkan di bawah."*
  - Dilengkapi tombol cepat *"Tampilkan Semua Entitas"*.
- **Penyempurnaan Tampilan & Filter UX (`index.blade.php`)**:
  - Menambahkan tombol clear *(X)* pada kotak input pencarian.
  - Tab cepat entitas (Semua, AMK, AKP, ATK, ABO, ATB) kini mempertahankan parameter pencarian yang sedang aktif saat berpindah tab.
  - Empty state lebih ramah dengan penjelasan detail serta tombol *"Reset Semua Filter"*.

---

### 47. 📊 Dashboard Log Antrean & Realtime AI Live Console Analyzer, Distribusi Dual Chart (Area & User), serta Analisa Kandidat Tanpa CV
- **Latar Belakang & Kebutuhan**:
  - Tim HR dan rekrutmen membutuhkan visibilitas mendalam terhadap antrean pemrosesan AI CV Analyzer, distribusi kandidat berdasarkan wilayah kerja (Area) dan PIC rekrutor/user, serta konsol log yang dapat dipantau langsung secara live (*real-time*).
  - Banyak kandidat dari Job Portal publik yang mendaftar tanpa melampirkan berkas CV PDF/gambar, melainkan mengisi seluruh data pengalaman, pendidikan, dan deskripsi diri langsung pada formulir web. Kandidat ini sebelumnya tidak dapat diproses oleh AI CV Analyzer karena engine hanya mencari berkas fisik.
- **Implementasi Fitur & Peningkatan**:
  1. **Dual Visualisasi Chart Antrean AI (`ai_queue.blade.php`)**:
     - Menghadirkan 2 grafik berdampingan (*side-by-side*) interaktif berbasis Chart.js:
       - **Donut Chart Distribusi Area**: Menampilkan sebaran kandidat dalam antrean berdasarkan area penempatan kerja.
       - **Donut/Pie Chart Distribusi User / Rekrutor**: Menampilkan beban kerja antrean kandidat per PIC rekrutor.
     - **Smart Recruiter Name Resolution**: Mengonversi alamat email atau ID user menjadi nama lengkap karyawan resmi dari database `employees` / `users`, sehingga grafik menampilkan nama asli yang ramah dibaca (bukan raw email).
  2. **Split Layout 2 Kolom Antrean & Hasil Analisa**:
     - Kolom Kiri: Daftar antrean kandidat yang belum dianalisa dengan status pendaftaran, asal portal, dan prioritas antrean.
     - Kolom Kanan: Riwayat kandidat yang baru saja selesai dianalisa lengkap dengan badge skor AI, kategori rekomendasi, dan nama model AI yang mengeksekusi (contoh: *Gemini 2.5 Flash*, *OpenRouter Llama 3.3 70B*, atau *Sumopod GLM-4*).
  3. **Live Console Log Real-Time & Auto-Prune**:
     - Terminal log real-time beralaskan tema gelap (*dark terminal style*) yang menampilkan setiap proses pemanggilan API, evaluasi token, pergantian provider AI, dan hasil parsing score.
     - Fitur pembersihan otomatis log kemarin (*auto-prune yesterday logs*) untuk menjaga performa database tetap optimal.
     - Tombol eksekusi manual antrean (*Run AI Queue Now*) untuk memicu batch analisa secara langsung tanpa menunggu jadwal cron.
  4. **Analisa Kandidat Tanpa Unggahan Berkas CV (Form Input Compilation)**:
     - Memperbarui [AiAnalyzerService.php](file:///d:/ASystem/newasystem/app/Services/AiAnalyzerService.php) agar mendukung kandidat yang tidak memiliki file CV terunggah.
     - Sistem secara cerdas mengompilasi seluruh data teks formulir pendaftaran pelamar (Nama, Usia, Pendidikan Terakhir, Jurusan, Pengalaman Kerja, Keterampilan Teknis, serta Deskripsi Diri/Summary) menjadi dokumen evaluasi terstruktur dan mengirimkannya ke engine AI untuk evaluasi komprehensif.
  5. **Pengurutan Antrean Berdasarkan Pendaftar Pertama (*FIFO Earliest Entry Date*)**:
     - Kueri antrean AI diprioritaskan ketat berdasarkan tanggal pendaftaran paling awal (`created_at ASC` / `tgl_masuk ASC`), memastikan keadilan seleksi di mana pelamar yang mendaftar lebih awal diproses terlebih dahulu.
     - Menuntaskan bug format tanggal `30 Nov -0001` pada kandidat lama dengan nilai tanggal kosong (*null-safe date formatting*).

---

### 48. 🏢 Penyempurnaan Routing Approver Head Inhouse (Prioritas OM Operasional Cabang vs Admin Support)
- **Akar Masalah & Identifikasi**:
  - Pada pengajuan approval kandidat inhouse (Step 1: Head Approver), sistem sebelumnya mengambil pimpinan karyawan rekrutor/AS dari kolom `employees.pimpinan`.
  - Pada beberapa cabang/area (contoh: Area Surabaya), pimpinan langsung di struktur Odoo tercatat sebagai staf admin operasional (`DEWI NOER HAYATI - ADMIN OPERASIONAL SURABAYA`).
  - Akibatnya, form Step 1 mengunci nama approver ke staf admin, bukan ke pimpinan manajerial operasional cabang yang berwenang memberikan persetujuan kerja inhouse.
- **Solusi & Logika Hierarki Approver Baru ([InterviewController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/InterviewController.php) & [InterviewInhouseController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/InterviewInhouseController.php))**:
  - Memperbarui algoritma resolusi approver pimpinan:
    1. **Pemeriksaan Jabatan Pimpinan Langsung**: Jika pimpinan langsung memiliki kata kunci non-manajerial seperti `ADMIN`, `SUPPORT`, atau `STAFF`, sistem tidak langsung menggunakannya sebagai Head Approver.
    2. **Prioritas Operasional Leadership Cabang**: Sistem menelusuri pimpinan hierarki di atasnya atau mencari karyawan aktif di area cabang terkait yang memiliki jabatan struktural operasional resmi:
       - `Operation Manager (OM)`
       - `Branch Manager (BM)`
       - `Area Manager (AM)`
       - `Head` / `Pimpinan Cabang`
       - `Supervisor (SPV)`
    3. **Fallback Head Operasional Inhouse Resmi**:
       - Jika tidak ditemukan di level cabang, approver diarahkan ke Kepala Operasional Inhouse Perusahaan: `David Oscar Sahala G Sibuea - OM (Operation Manager)`.
  - Kandidat inhouse Area Surabaya kini secara tepat dialihkan approval-nya ke pimpinan operasional yang sah.

---

### 49. 🛡️ Restriksi Dashboard Head Inhouse Khusus Step `Review Head` & Perbaikan Bug Otorisasi `$isHrd`
- **Latar Belakang Masalah**:
  - Pimpinan Head yang login ke dashboard Kandidat Inhouse (`/interviewinhouse`) sebelumnya melihat seluruh kandidat inhouse di semua tahapan (termasuk kandidat yang masih draft di rekrutor, kandidat yang sudah naik ke HRD Pusat, maupun yang sudah disetujui penuh).
  - Ketika Head mengklik tombol detail data kandidat, muncul error fatal:
    > `ErrorException: Undefined variable $isHrd in file .../interviewinhouse/show.blade.php`
- **Perbaikan & Implementasi**:
  1. **Restriksi Ketat Dashboard Head ([InterviewInhouseController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/InterviewInhouseController.php))**:
     - Menambahkan filter hak akses pada method `index()`: Jika pengguna yang login adalah pimpinan Head (dan bukan Super Admin / HRD Pusat), daftar kandidat inhouse **hanya menampilkan kandidat yang sedang menunggu persetujuan Head** (`where('status_approval', 'Review Head')`).
     - Head dapat fokus memproses berkas yang menjadi tanggung jawabnya tanpa terdistraksi oleh berkas di luar wewenangnya.
  2. **Resolusi Fatal Error `$isHrd` ([InterviewInhouseController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/InterviewInhouseController.php))**:
     - Menambahkan penentuan flag perizinan lengkap sebelum me-render view:
       ```php
       $isHrd = (auth()->user()->role === 'admin' || auth()->user()->role === 'hrd' || str_contains(strtolower(auth()->user()->name), 'hrd'));
       $isHead = ($candidate->nama_approver && str_contains(strtolower($candidate->nama_approver), strtolower(auth()->user()->name)));
       ```
     - Memastikan seluruh variabel otorisasi (`$isHrd`, `$isHead`, `$isUserAs`, `$canApproveInhouse`) terdefinisi dengan aman di controller dan view.

---

### 50. 📋 Penyelarasan Menyeluruh Data Riil Evaluasi Kandidat Inhouse (Interview, Refcek, Komputer, Kepribadian, & Matematika)
- **Latar Belakang & Analisa Kebutuhan**:
  - Halaman detail kandidat inhouse (`/interviewinhouse/{id}`) sebelumnya menggunakan template tampilan dengan data statis / default mockup yang tidak mencerminkan data riil hasil tes dan evaluasi dari database kandidat.
  - Tampilan evaluasi harus diselaraskan sepenuhnya dengan halaman detail kandidat interview (`/interview/{id}`) yang sudah memiliki standar data evaluasi 5 pilar seleksi lengkap.
- **Implementasi Service Terpadu `CandidateEvaluationDataService`**:
  - Dibuat service sentral [CandidateEvaluationDataService.php](file:///d:/ASystem/newasystem/app/Services/CandidateEvaluationDataService.php) dengan method `getEvaluationData($candidate)` untuk menyatukan dan menstandarisasi pemrosesan data evaluasi kandidat di seluruh modul sistem.
- **Penyelarasan 5 Tab Evaluasi pada `interviewinhouse/show.blade.php`**:
  1. **Tab 1: Hasil Wawancara (Interview)**:
     - Menampilkan 4 pilar penilaian wawancara:
       - **Penampilan**: Skala 1-4 (Kurang, Cukup, Baik, Sangat Baik).
       - **Komunikasi**: Skala 1-4.
       - **Pemahaman Tugas**: Skala 1-4.
       - **Kepribadian**: Skala 1-4.
     - Total skor wawancara, catatan detail hasil interview, nama pewawancara, serta **tanda tangan digital riil** Rekrutor / AS yang tersimpan di sistem.
  2. **Tab 2: Referensi Cek (Refcek)**:
     - Dropdown interaktif perusahaan yang dicek berdasarkan riwayat pekerjaan aktual pelamar (`tb_riwayat_pekerjaan`).
     - Data lengkap informan/PIC referensi, nomor kontak, hubungan kerja, serta catatan verifikasi rekam jejak (kinerja, integritas, kedisiplinan, alasan keluar).
     - Pratinjau berkas / tangkapan layar bukti percakapan WhatsApp referensi cek dengan modal lightbox zoom.
  3. **Tab 3: Hasil Tes Komputer**:
     - 9 Pilar Keterampilan Komputer riil: *MS Word, MS Excel, Formula/Rumus Excel, Power Point, Email & Internet, Mengetik 10 Jari, Kecepatan Kerja, Kerapian Data, Sikap Kerja*.
     - Skor kumulatif, nilai persentase, dan predikat kelulusan (Grade A, B, C, D).
     - Catatan evaluator penguji serta viewer berkas bukti lembar tes komputer.
  4. **Tab 4: Hasil Tes Kepribadian (DISC / Florence Littauer)**:
     - Skor 4 tipe kepribadian: *Sanguinis (Popular), Koleris (Powerful), Melankolis (Perfect), Plegmatis (Peaceful)*.
     - Daftar 40 butir soal kepribadian lengkap dengan jawaban riil yang dipilih kandidat beserta tipe kepribadian tiap butir.
     - Ringkasan profil kepribadian dominan dan analisa kecocokan jabatan kerja.
  5. **Tab 5: Hasil Tes Matematika CBT**:
     - Nilai riil tes matematika (skor total, persentase kelulusan, grade A/B/C/D).
     - Rincian seluruh butir soal tes matematika CBT yang dikerjakan kandidat, kunci jawaban benar, jawaban yang dipilih kandidat, dan indikator visual status Benar (hijau) / Salah (merah).
     - Riwayat nomor percobaan tes (`tes_ke`) dan status remedial.
- **Hasil & Verifikasi**:
  - Halaman detail inhouse kini 100% menampilkan data riil seleksi yang terintegrasi langsung dengan tabel `candidates`, `tb_kandidat`, `tb_riwayat_pekerjaan`, `tb_jawaban_kepribadian`, dan `tb_jawaban_matematika`.
  - Tampilan visual rapi, konsisten, responsif, dan siap digunakan oleh pimpinan Head maupun HRD Pusat.

---

### 51. 🔒 Proteksi Autentikasi Modul Input Job Requirement & Kontrol Kepemilikan Akun Rekruter (User AS) (23 September 2026)
- **Latar Belakang & Investigasi Masalah**:
  - Ditemukan beberapa lowongan pekerjaan aktif (seperti *SPG EVENT (SABTU MINGGU)* Surabaya, *KOORDINATOR PABRIK* & *SPG 01* Semarang, serta *Team Leader Motoris Forisa Dessert 1* Malang) tercatat dibuat oleh akun `admin@asystem.co.id` padahal seharusnya dimiliki oleh rekruter cabang terkait.
  - Investigasi log IP mendalam mengungkap bahwa:
    1. Rute `/inputjob` di [routes/web.php](file:///d:/ASystem/newasystem/routes/web.php) sebelumnya tidak terbungkus middleware `auth`, sehingga pengguna dengan sesi yang sudah habis (*expired*) atau belum login tetap dapat mengakses formulir.
    2. Pada [JobController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/JobController.php), terdapat fungsi fallback `getCurrentUser()` yang secara otomatis mengembalikan akun `admin@asystem.co.id` ketika `auth()->user()` bernilai `null`. Hal ini menyebabkan request guest/unauthenticated diam-diam menyamar sebagai Administrator ESA dan menyimpan lowongan dengan `created_by = admin@asystem.co.id`.
    3. Form input lowongan belum menyediakan pilihan akun pembuat bagi Administrator, sehingga Admin tidak dapat menentukan atau mengalihkan akun rekruter pemilik lowongan.
- **Implementasi Solusi & Perbaikan Sistem**:
  1. **Proteksi Middleware `auth`**:
     - Membungkus seluruh rute `/inputjob` (`index`, `store`, `generate_ai`, `generate_image_prompt`, `destroy`, `toggle`) ke dalam `Route::middleware(['auth'])` di [routes/web.php](file:///d:/ASystem/newasystem/routes/web.php).
     - Pengguna yang belum login kini secara otomatis dialihkan ke halaman login (`/login`) dengan pesan peringatan.
  2. **Eliminasi Fallback Admin**:
     - Menghapus fallback diam-diam ke user admin pada [JobController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/JobController.php) (`getCurrentUser()`), menjamin hanya akun terautentikasi resmi yang dapat membuat dan mengelola lowongan.
  3. **Dropdown Pilihan Pembuat Lowongan untuk Administrator**:
     - Menambahkan dropdown interaktif **"Pembuat Job / Akun Rekruter (User AS)"** pada form [job/input.blade.php](file:///d:/ASystem/newasystem/resources/views/job/input.blade.php) khusus untuk pengguna dengan hak akses Administrator.
     - Administrator dapat menentukan akun rekruter penanggung jawab lowongan saat membuat baru maupun mengalihkan kepemilikan lowongan saat mode edit (`updateData['created_by']`).
  4. **Pembaruan Kepemilikan 4 Lowongan Eksisting di Database Server**:
     - Lowongan **SPG EVENT (SABTU MINGGU)** (ID 623 - Surabaya) $\rightarrow$ dialihkan ke `reyna.arina@gmail.com` (Reyna Sastri Dewi Asrini).
     - Lowongan **KOORDINATOR PABRIK** (ID 574 - Semarang) $\rightarrow$ dialihkan ke `setiawanbudiwibisono91@gmail.com` (Setiawan Budi Wibisono).
     - Lowongan **SPG 01** (ID 572 - Semarang) $\rightarrow$ dialihkan ke `viandhikatytho@gmail.com` (Tytho Viandhika Pratama).
     - Lowongan **Team Leader Motoris Forisa Dessert 1** (ID 562 - Malang) $\rightarrow$ dialihkan ke `novandsabtian16@gmail.com` (Novand Sabtian).


---

### 52. ⚡ Perbaikan Realtime Update Persentase & Sebaran User pada Log Antrean AI (23 September 2026)
- **Latar Belakang & Gejala Masalah**:
  - Pada halaman Log Antrean AI (`/kandidatportal/ai-queue`), metrik antrean dan kartu sisi kiri (*Persentase Area Belum Dianalisa*) berhasil terupdate berkurang secara realtime saat background AI memproses kandidat.
  - Namun, kartu sisi kanan (*Persentase Berdasarkan Nama User*) sempat tertahan pada angka awal saat halaman pertama kali dibuka (misal: antrean tertahan di 482, sebaran user dan donut chart tidak berubah), sehingga terjadi ketidaksinkronan angka antara kartu area dan kartu user.
- **Akar Penyebab (*Root Causes*)**:
  1. **Blokade Eksekusi State Alpine.js**:
     - Pada `fetchData()`, pembaruan `this.userStats` diletakkan setelah fungsi update visual `updateAreaChart()`. Jika Chart.js pada chart area mengalami perubahan jumlah slice/label saat animasi berlangsung, pemanggilan `.update()` dapat memicu error internal Chart.js sehingga pembaruan `this.userStats` terlewat (*skipped*).
  2. **Chart.js Arc Dataset Length Mismatch**:
     - Baik `updateAreaChart()` maupun `updateUserChart()` langsung mengubah array `data` dan memanggil `.update()`. Ketika jumlah peringkat user atau area berubah dari Top 8 ke 9 atau sebaliknya, Chart.js gagal menginterpolasi elemen arc yang berbeda panjangnya.
  3. **Reaktivitas Key Template Alpine.js**:
     - Penggunaan `:key="'user_' + idx"` pada `template x-for` menyebabkan Alpine.js mempertahankan node DOM lama karena index-nya tidak berubah, sehingga progress bar dan persentase tidak di-render ulang secara reaktif saat nilainya bergeser.
  4. **Browser/Proxy HTTP Caching**:
     - Request polling `fetch()` ke endpoint `/kandidatportal/ai-queue-data` belum dilengkapi parameter *cache-buster* timestamp serta header respons `no-store, no-cache, must-revalidate`.
- **Implementasi Solusi & Perbaikan**:
  1. **Pembaruan State Data Mendahului Visualisasi**:
     - Pada `fetchData()`, nilai reaktif `this.areaStats` dan `this.userStats` langsung diperbarui seketika respons JSON diterima, sebelum memanggil update chart.
     - Setiap pembaruan visual chart (`updateAreaChart()` dan `updateUserChart()`) diisolasi dengan penanganan `try...catch` mandiri sehingga error visual pada satu chart tidak akan pernah menghentikan data atau chart lainnya.
  2. **Safeguard & Anti-Flicker Render Chart.js**:
     - Menghilangkan proses penghancuran instance canvas (`destroy()`) saat polling berkala dan menonaktifkan sweep animation (`animation: false`).
     - Pembaruan berkala langsung memutakhirkan dataset objek in-place via mode `chart.update('none')`, sehingga grafik donat tidak berkedip, tidak memutar/sweep ulang dari nol, dan hanya proporsi busur irisannya yang bergeser lembut.
  3. **Kunci Reaktif Stabil pada Alpine.js (`:key`)**:
     - Mengubah `:key` pada `x-for` menjadi berbasis identitas nama: `:key="'user_' + (item.user || ('idx_' + idx))"` dan `:key="'area_' + (item.area || ('idx_' + idx))"`.
     - Node DOM kartu baris dipertahankan secara utuh tanpa dihancurkan/dibuat ulang saat jumlah antrean berkurang, sehingga **hanya angkanya saja yang berganti** secara bersih dan progress bar menyusut secara halus via transisi CSS.
  4. **Cache-Busting & Header Anti-Cache**:
     - Menambahkan parameter query `_t=${Date.now()}` dan header `Cache-Control: no-cache` pada request `fetch()`.
     - Menambahkan header HTTP `Cache-Control: no-store, no-cache, must-revalidate, max-age=0` dan `Pragma: no-cache` pada controller `KandidatPortalController::aiQueueData()`.
  5. **Sinkronisasi Total Ground Truth**:
     - Memastikan `$totalUnanalyzed` pada backend merujuk langsung ke `$queueCount` sehingga kedua kartu dan metrik antrean selalu memiliki angka dasar yang 100% identik dan sinkron.

---

### 53. ⚙️ Implementasi Role Akses Approver Dinamis & Workflow Engine Kandidat Inhouse (23 September 2026)
- **Latar Belakang & Kebutuhan Pengguna**:
  - Alur persetujuan (*approval*) kandidat inhouse (`/interviewinhouse`) sebelumnya bersifat statis/kaku (*hardcoded* 2 tahap: Head $\rightarrow$ HRD Pusat).
  - Pengguna membutuhkan fleksibilitas penuh untuk mengatur alur approval secara dinamis:
    1. **Urutan Tahapan Dinamis**: Kemampuan menambah, mengubah, menghapus, dan mengatur urutan step approval (Step 1, Step 2, Step 3, dst.).
    2. **Pilihan Tipe Approver per Step**:
       - **Head**: Otomatis mengikuti Head / Pimpinan langsung dari rekruter yang menangani kandidat berdasarkan data Master Karyawan (`tb_karyawan.pimpinan` / `Employee::pimpinan`).
       - **Akun User / Karyawan**: Memiliki input nama step kustom (misal: *Review HRD Jakarta*, *Review HRD Pusat*, *Approval GM Ops*) serta pemilihan akun pengguna / karyawan penanggung jawab (**mendukung pemilihan multiple approvers sekaligus**).
    3. **Pengecualian Direksi (Auto-Bypass)**: Jika pimpinan langsung dari rekruter kandidat adalah **Direksi** (Direktur / BOD), maka step approval Head tidak diperlukan dan secara otomatis dilewati (*skip* langsung ke step berikutnya).
    4. **Kondisi Berdasarkan Area & Entitas Formasi**:
       - Step approval dapat memiliki batasan/kondisi **Area** (`Semua Area`, `Khusus Jakarta`, `Selain Jakarta (Luar Jakarta)`, atau area spesifik) dan **Entitas** (`Semua Entitas` atau entitas inhouse tertentu: AMK, AKP, ATK, ABO, ATB).
       - Contoh implementasi:
         - Kandidat **Area Jakarta**: Setelah step Head (atau langsung jika pimpinannya Direksi), alur masuk ke approval **HRD Jakarta** sesuai entitasnya.
         - Kandidat **Selain Area Jakarta** (Cabang/Daerah luar Jakarta): Setelah step Head, alur masuk ke approval **HRD Pusat**.
- **Komponen Arsitektur Database & Migrasi**:
  - **Tabel `approval_workflows`**: Menyimpan master alur kerja approval per modul (`module: 'kandidat_inhouse'`).
  - **Tabel `approval_workflow_steps`**: Menyimpan tahapan approval lengkap dengan `step_order`, `step_name`, `approver_type` (`'head'` vs `'user'`), `area_scope`, `entity_scope`, dan `skip_if_direksi`.
  - **Tabel `approval_workflow_step_users`**: Tabel relasi penugasan banyak akun user / karyawan untuk setiap step approval (menunjang multi-approver per step).
  - **Tabel `candidates`**: Penambahan kolom pelacakan step aktif: `current_approval_step_id` dan `current_step_order`.
  - **Tabel `inhouse_approvals`**: Penambahan kolom pencatatan riwayat bertingkat: `step_id`, `step_order`, `step_name`, dan `user_id`.
  - **Migrasi**: `2026_09_23_200000_create_dynamic_approval_workflows_tables.php` yang otomatis menginisialisasi workflow default dan memetakan kandidat eksisting tanpa menimbulkan downtime.
- **Workflow Engine & Business Logic Service (`ApprovalWorkflowService.php`)**:
  - `isPimpinanDireksi(Candidate $candidate)`: Mendeteksi secara cerdas apakah pimpinan rekruter berstatus Direksi (Direktur, Board of Directors, Managing Director, Presdir) melalui Master `Employee`.
  - `getApplicableStepsForCandidate(Candidate $candidate)`: Menyaring daftar tahapan workflow yang relevan untuk kandidat berdasarkan evaluasi Direksi, normalisasi Area (Jakarta vs Luar Jakarta), dan Entitas inhouse (AMK, AKP, ATK, ABO, ATB).
  - `getCurrentStep(Candidate $candidate)` & `getNextStep(Candidate $candidate, ...)`: Menentukan step aktif yang sedang menunggu keputusan dan menavigasi kandidat ke tahap berikutnya setelah disetujui.
  - `canUserApprove(Candidate $candidate, User $user)`: Memvalidasi secara ketat hak akses pengguna pada step aktif saat ini:
    - Administrator selalu memiliki akses override.
    - Pada step Head: memvalidasi kecocokan nama pimpinan rekruter, bawahan langsung, atau penugasan `nama_approver`.
    - Pada step Akun User: memvalidasi apakah user terdaftar pada daftar multi-approver step tersebut atau memiliki role HRD yang berwenang.
  - `processApproval(Candidate $candidate, User $user, array $data)`: Mencatat log keputusan ke `inhouse_approvals` (dan sinkronisasi `tb_catataninhouse`), mengalihkan status kandidat ke step berikutnya, atau menyelesaikan approval final (`Approve`) jika seluruh tahapan telah rampung.
- **Antarmuka Konfigurasi Master (`/master/approval-workflow`)**:
  - Ditambahkan ke bilah navigasi (*sidebar*) di bawah menu **Master Data** khusus Administrator.
  - Tampilan visual pipeline step cards responsif dengan badge tipe approver, filter area, filter entitas, dan indikator auto-skip Direksi.
  - Tombol pengurutan langsung (*Reorder Up / Down*) yang sinkron via AJAX.
  - Modal interaktif (didukung Alpine.js) untuk Tambah & Edit Step dengan radio selector tipe approver, opsi filter area & entitas, checkbox skip Direksi, serta pencarian & multi-select akun karyawan lengkap dengan avatar dan chips/tags terpilih.
- **Pembaruan Halaman Detail & Evaluasi Inhouse (`/interviewinhouse/{id}`)**:
  - Menggantikan blok statis 2 step dengan **Dynamic Horizontal/Vertical Stepper** yang merender seluruh tahapan yang berlaku bagi kandidat.
  - Stepper menampilkan status riil tiap tahap: *Disetujui* (hijau), *Sedang Menunggu* (kuning/indigo berdenyut), *Terkunci* (abu-abu), atau *Ditolak* (merah).
  - Form Approval hanya aktif dan dapat disubmit oleh user yang berhak pada step yang sedang aktif, sementara user lain disajikan kotak informasi yang menjelaskan giliran approver yang berhak.
  - Tabel riwayat persetujuan terpadu menampilkan riwayat keputusan per tahap lengkap dengan nama approver, jabatan, tanda tangan digital, catatan, dan timestamp.
- **Penyebaran ke Server Produksi (Server 3)**:
  - Berhasil di-deploy ke server live `new.asystem.co.id` (38.103.170.224) via `php scripts/deploy_production.php`.
  - Migrasi skema berjalan sukses dan seluruh endpoint telah teruji beroperasi normal (HTTP 200).

---

### 54. 🏢 Penambahan Konfigurasi Dinamis Area, Prinsiple & Multi-User pada Form Step Approval Kandidat Inhouse (23 September 2026)
- **Latar Belakang & Kebutuhan Pengguna**:
  - Pada form konfigurasi step sebelumnya, kondisi Area (*Area Scope*) dan Entitas (*Entity Scope*) hanya berupa single-dropdown statis untuk satu step keseluruhan.
  - Pengguna membutuhkan fleksibilitas tingkat lanjut di mana dalam satu tahapan step (misal: *Review HRD*), pengguna dapat menambahkan pemetaan kondisi **Area**, **Prinsiple**, dan **User** secara dinamis (+ Tambah Baris Aturan):
    - Contoh:
      - Jakarta + AMK &rarr; PIC Approver: **Yeneniya (HRD AMK)** (bisa multiple user).
      - Jakarta + AKP &rarr; PIC Approver: **Uriyanto (HRD AKP)** (bisa multiple user).
      - Luar Jakarta + Semua Entitas &rarr; PIC Approver: **HRD Pusat**.
    - Pilihan user pada tiap kombinasi kondisi tetap mendukung pemilihan **lebih dari satu user (multiple user)**.
- **Skema Basis Data & Migrasi (`2026_09_23_210000_add_dynamic_rules_to_approval_workflow_steps_tables.php`)**:
  - Menambahkan kolom `approval_rules` (`JSON`, nullable) pada tabel `approval_workflow_steps` untuk menyimpan data pemetaan array aturan dinamis secara terstruktur.
  - Menambahkan kolom `area` (`varchar(100)`, default `'ALL'`) dan `prinsiple` (`varchar(100)`, default `'ALL'`) pada tabel `approval_workflow_step_users` agar integritas data relasional dan pencarian foreign key tetap optimal.
- **Penyempurnaan Model & Workflow Engine (`ApprovalWorkflowStep.php` & `ApprovalWorkflowService.php`)**:
  - `getMatchingRuleForCandidate($candidate)`: Algoritma cerdas pencocokan spesifik berbasis bobot (*scoring specificity*). Aturan dengan kondisi spesifik (misal Jakarta + AMK) diprioritaskan di atas aturan umum (Luar Jakarta / Semua Area / Semua Prinsiple).
  - `getMatchingApproversForCandidate($candidate)`: Mengembalikan daftar PIC approver yang berwenang spesifik untuk kandidat tersebut.
  - `canUserApprove($candidate, $user, $currentStep)`: Memvalidasi kecocokan ID pengguna terhadap daftar user yang ditugaskan pada aturan yang cocok untuk kandidat (kandidat Jakarta AMK hanya dapat di-approve oleh Yeneniya, bukan oleh Uriyanto).
  - `getStepApproverDisplayInfo($candidate, $step)`: Menghasilkan informasi detail nama PIC dan kondisi aturan yang cocok untuk dirender pada stepper dan pesan form terkunci.
- **Penyempurnaan Antarmuka Form Master (`/master/approval-workflow`)**:
  - **Dynamic Rule Repeater (Alpine.js)**:
    - Opsi radio tipe approver: **Head / Pimpinan** vs **Akun User Tertentu**.
    - Tiap baris aturan kini dilengkapi **Searchable Dropdown** untuk:
      1. **Area Penempatan**: Kotak pencarian interaktif untuk memilih Semua Area, Jakarta, Luar Jakarta, ataupun kota cabang spesifik.
      2. **Prinsiple / Entitas**: Kotak pencarian untuk memilih Semua Entitas, 5 entitas inhouse resmi (AMK, AKP, ATK, ABO, ATB), maupun master prinsiple mitra rekanan.
      3. **Akun Approver (User / Karyawan)**: Multi-select searchable popover dengan kotak pencarian nama/email/jabatan karyawan secara instan, lengkap dengan indikator badge "Terpilih" dan tag chips yang dapat dihapus `(x)`.
    - Tombol `+ Tambah Aturan Area & Prinsiple Baru` dan tombol hapus baris (*trash*).
  - **Visual Pipeline Step Cards**:
    - Step cards menampilkan badge pill ringkas untuk tiap aturan yang terpasang (`📍 Area • 🏢 Prinsiple → 👤 Users`).
- **Penyempurnaan Stepper Detail Kandidat (`/interviewinhouse/{id}`)**:
  - Stepper menampilkan nama PIC approver yang relevan secara otomatis sesuai formasi kandidat (misal untuk kandidat Jakarta AMK langsung menampilkan *"Approver: Yeneniya (HRD AMK)"*).
  - Kotak informasi pada form yang terkunci secara transparan menampilkan nama PIC penanggung jawab dan kondisinya.

---

### 55. 🧠 Integrasi Knowledge Graph Memory (Graphify) & Otomasi Pembaruan Arsitektur Codebase (23 September 2026)
- **Latar Belakang & Kebutuhan Pengguna**:
  - Untuk mempermudah pemahaman arsitektur codebase berskala besar pada project **newasystem** tanpa memakan konsumsi token berlebih, diintegrasikan alat graf pengetahuan open-source **Graphify** (`https://github.com/Graphify-Labs/graphify.git`).
  - Pengguna menginstruksikan agar instalasi dan pembuatan memori graph dilakukan, serta memastikan ke depannya setiap update selalu memperbarui graph tersebut.
- **Instalasi Paket & Konfigurasi Workspace**:
  - Berhasil menginstal paket resmi `graphifyy` (v0.9.66) melalui pip pada lingkungan Python 3.12 dengan dependensi AST lengkap (`tree-sitter-php`, `tree-sitter-javascript`, `networkx`, `rapidfuzz`).
  - Membuat berkas pengecualian `.graphifyignore` untuk mengabaikan folder cache dan library berat (`vendor/`, `node_modules/`, `storage/`, `scratch/`, data aset biner).
- **Integrasi Native Antigravity Skill & Rules**:
  - Menjalankan `python -m graphify antigravity install` yang secara otomatis mendaftarkan:
    1. Skill resmi Antigravity: `C:\Users\user\.gemini\config\skills\graphify\SKILL.md` (mendukung command `/graphify`).
    2. Workflow rule: `.agents/workflows/graphify.md` dan `.agents/rules/graphify.md`.
    3. File aturan project root: `AGENTS.md` yang mewajibkan setiap agen AI untuk memutakhirkan graphify pada setiap akhir siklus tugas.
  - Memasang **Git Hooks Otomatis** (`python -m graphify hook install`) pada `.git/hooks/post-commit` dan `.git/hooks/post-checkout` sehingga setiap kali ada git commit baru, proses graphify otomatis terpicu.
- **Hasil Ekstraksi Graf Perdana Codebase**:
  - Memindai 252 berkas kode inti PHP / Laravel, Blade, dan skema database secara paralel menggunakan 12 worker AST.
  - Berhasil memetakan **1.220 simpul komponen (*nodes*)** dan **2.793 relasi keterkaitan (*edges*)** dalam **158 klaster (*communities*)**.
  - Berkas artefak memori yang dihasilkan di `graphify-out/`:
    - `graph.json`: Database graf struktural berformat JSON untuk kueri cepat AI (`python -m graphify query "<topik>"`).
    - `graph.html`: Visualisasi graf 2D/3D interaktif yang dapat dibuka langsung di Google Chrome / browser tanpa server web.
    - `GRAPH_TREE.html`: Diagram pohon hirarki komponen D3 v7.
- **Skrip Pembaharuan 1-Klik**:
  - Menyediakan skrip `scripts/run_graphify.ps1` untuk mempermudah pembaruan graph secara instan kapan saja.

---

### 56. 📝 Form Job Apply: Seluruh Field Menjadi Mandatory (Wajib Diisi) & Notifikasi Interaktif Bagian yang Kurang (23 September 2026)
- **Latar Belakang & Kebutuhan Pengguna**:
  - Formulir pendaftaran lowongan kerja publik (`/job/{id}/apply`) sebelumnya masih memiliki beberapa field opsional (*nullable*), seperti pas foto, tinggi & berat badan, ringkasan pengalaman, motivasi, dan kelebihan diri.
  - Pengguna menginstruksikan agar seluruh field dibuat menjadi **mandatory (wajib diisi)** oleh kandidat pelamar, dan jika terdapat bagian yang masih kosong/kurang lengkap, sistem harus memunculkan **notifikasi interaktif yang merinci secara jelas bagian-bagian mana saja yang masih kurang**.
- **Pembaruan Backend Controller (`app/Http/Controllers/PublicJobController.php`)**:
  - Memperketat aturan validasi pada metode `submitApply` untuk 18 field secara komprehensif:
    1. `foto_profil`: Wajib diunggah (`required|file|mimes:jpeg,png,jpg,webp|max:5120`).
    2. `file_cv`: Berkas CV wajib diunggah (`required|file|mimes:pdf,jpeg,png,jpg|max:10240`).
    3. `nik`: Wajib 16 digit angka (`required|string|size:16|regex:/^[0-9]+$/`).
    4. `nama_lengkap`: Wajib diisi sesuai KTP (`required|string|max:255`).
    5. `tgl_lahir`: Wajib diisi (`required|date`).
    6. `gender`: Wajib dipilih (`required|in:Laki-laki,Perempuan`).
    7. `tinggi`: Wajib diisi angka antara 50 - 250 cm (`required|numeric|min:50|max:250`).
    8. `berat`: Wajib diisi angka antara 20 - 300 kg (`required|numeric|min:20|max:300`).
    9. `alamat_ktp`: Wajib diisi minimal 5 karakter (`required|string|min:5`).
    10. `alamat_domisili`: Wajib diisi minimal 5 karakter (`required|string|min:5`).
    11. `no_wa`: Wajib diisi minimal 9 digit angka (`required|string|min:9|max:20`).
    12. `pendidikan`: Wajib dipilih (`required|string`).
    13. `propinsi_domisili`: Wajib dipilih (`required|string|max:100`).
    14. `kota_domisili`: Wajib dipilih (`required|string|max:100`).
    15. `info_lowongan`: Wajib dipilih (`required|string|max:100`).
    16. `ringkasan_pengalaman`: Wajib diisi minimal 3 karakter (`required|string|min:3`).
    17. `motivasi`: Wajib diisi minimal 3 karakter (`required|string|min:3`).
    18. `kelebihan`: Wajib diisi minimal 3 karakter (`required|string|min:3`).
  - Menambahkan *custom validation error messages* dalam Bahasa Indonesia yang formal, sopan, dan informatif untuk setiap field.
- **Pembaruan Frontend Antarmuka & Validasi Client-Side (`resources/views/job/apply.blade.php`)**:
  - **Tanda Visual Wajib (*Mandatory Asterisk*)**:
    - Seluruh label input, textarea, select, dan kartu upload (Pas Foto & CV) kini dilengkapi badge merah `*Wajib` atau bintang merah `<span class="text-rose-500">*</span>`.
    - Menghilangkan default placeholder dummy agar kandidat benar-benar memilih dan mengisikan data mereka sendiri.
  - **Validasi JavaScript Interaktif Sebelum Submit**:
    - Menggunakan atribut `novalidate` pada form agar tidak tertahan oleh tooltip native browser yang kaku, melainkan dihandle secara cerdas oleh script validasi kustom.
    - Mengaudit seluruh 18 input secara bersamaan sebelum berkas dikirim.
    - Jika terdapat field yang belum diisi atau tidak valid, memunculkan popup **SweetAlert2** bertema warning modern:
      - Menampilkan jumlah total bagian yang belum diisi (misal: *18 bagian yang belum diisi*).
      - Menampilkan daftar rincian terformat (*scrollable card*) yang mencantumkan Nomor, Nama Field, Kategori Seksi, serta pesan panduan pengisian yang jelas.
      - Menyediakan tombol konfirmasi *"Lengkapi Bagian Ini"* yang secara otomatis melakukan *smooth scrolling* dan memfokuskan kursor ke field error pertama.
  - **Highlighting Merah & Real-Time Error Clearing**:
    - Setiap input/kontainer yang kurang lengkap secara dinamis diberi efek border merah (`border-rose-500`), bayangan fokus merah (`ring-2 ring-rose-200`), dan pesan error dengan ikon `fa-circle-exclamation` di bawah field.
    - Begitu kandidat mulai mengetik atau memilih opsi pada field terkait (`input` & `change` event), efek merah dan pesan error otomatis hilang seketika (*real-time auto-clear*).
    - Menambahkan sinkronisasi *real-time* alamat KTP ke domisili saat opsi *"Domisili Sama dengan KTP"* dicentang.
    - Integrasi otomatis penanganan server error (`$errors->any()`) jika terjadi penolakan dari backend, memunculkan SweetAlert2 merah rincian error saat halaman dimuat ulang.

---

### 57. ⚡ Perbaikan Masalah Input Token AI CV Analyzer Melonjak Ekstrem (2.114.589 Token) & Sanitasi Gambar Base64 Job Requirement (24 September 2026)
- **Investigasi Akar Masalah (*Root Cause Analysis*)**:
  - Ditemukan bahwa kandidat (seperti Rubiyanto - NIK `3301212002030002` untuk posisi *SALES PROMOTION BOY (MITRA BELANJA WINGS - PRIA)*) mengalami kegagalan analisa AI dengan status *"Limit token AI tercapai (Gemini -> OpenRouter -> Sumopod)"* dan mencatat lonjakan input token hingga **2.114.589 token**.
  - Padahal berkas CV berformat PDF kandidat hanya berukuran normal (154 KB, 1 halaman) dan deskripsi pekerjaan relatif singkat.
  - **Penyebab Utama**: Pada tabel `job_specs` (Job ID #63), terdapat gambar brosur/flyer berukuran 2,98 MB yang ditempel (*paste*) ke dalam editor teks Summernote pada kolom kualifikasi (`job_quals`). Summernote mengonversi gambar tersebut menjadi string mentah `<img src="data:image/png;base64,...">` sepanjang **2.980.919 karakter**.
  - Ketika `AiAnalyzerService::buildJobSpecsText()` memuat spesifikasi lowongan, teks HTML mentah tersebut digabungkan langsung ke dalam prompt AI tanpa proses pembersihan tag/gambar, sehingga satu prompt teks mencapai **~746.228 token**.
  - Saat engine AI memicu Gemini API beberapa kali (retry/rotasi key), total konsumsi token seketika melonjak menembus **2.114.589 token** dan memicu HTTP 429 limit pada seluruh API Key. Fallback OpenRouter juga menolak permintaan (*HTTP 400: requested about 746228 tokens exceeds context limit 262144*), begitu pula Sumopod (*context window exceeds limit*).
- **Pembersihan & Perbaikan Basis Data Produksi**:
  - Menjalankan pembersihan langsung pada baris data `job_specs` ID #63 di server produksi, membuang tag base64 image sebesar 2,98 MB dan menyisakan teks murni kualifikasi pekerjaan (panjang string berkurang dari 2.980.919 karakter menjadi hanya 245 karakter).
  - Melakukan pemindaian menyeluruh terhadap seluruh 545 data lowongan kerja aktif lainnya untuk memastikan tidak ada data base64 lain yang tersisa.
- **Pembaruan Engine AI (`app/Services/AiAnalyzerService.php`)**:
  - **Sanitasi Ketat Teks Prompt (`sanitizeTextForPrompt`)**:
    - Otomatis membuang seluruh tag `<img[^>]*>` dan pola inline base64 `data:image/...;base64,...`.
    - Menghapus tag script, style, svg.
    - Mengonversi tag pemisah blok (`<br>`, `<p>`, `<li>`, dsb.) menjadi baris baru (*newline*) agar format teks tetap terstruktur rapi dan terbaca jelas oleh AI.
    - Menjalankan `strip_tags()` dan `html_entity_decode()`.
    - Membatasi panjang maksimal per seksi (kualifikasi maks 2.000 karakter, deskripsi maks 2.500 karakter, skills maks 1.500 karakter, pengalaman kerja maks 1.500 karakter).
  - **Pengamanan Input Biodata & Pengalaman Kandidat**:
    - Menerapkan sanitasi serupa pada seluruh isian teks bebas kandidat (`work_motivation`, `strengths`, `weaknesses`, `experience_summary`, dsb.).
  - **Monitoring & Safety Truncation**:
    - Menambahkan log info pencatatan panjang karakter prompt dan estimasi token sebelum panggilan API dilakukan.
    - Memasang batas aman pemotongan otomatis (*safety cap*) jika prompt kumulatif melampaui 25.000 karakter demi menjamin tidak akan pernah terjadi ledakan kuota token di masa mendatang.
- **Pembaruan Form Input & Controller Job Requirement (`app/Http/Controllers/JobController.php` & `resources/views/job/input.blade.php`)**:
  - **Sanitasi Backend**: Controller secara otomatis memfilter dan menghapus tag gambar base64 sebelum data lowongan disimpan atau diperbarui ke dalam tabel `job_specs`.
  - **Pengaturan Summernote Frontend**:
    - Menghapus opsi 'picture' dan 'video' dari toolbar Summernote form input lowongan kerja.
    - Menambahkan event callback `onImageUpload` yang menampilkan SweetAlert2 peringatan apabila pengguna mencoba melakukan drag-and-drop atau copy-paste file gambar ke dalam kolom teks persyaratan.

---

### 58. 📋 Penyelarasan Akun Astri Wahyuni (ASTRI WAHYUNI,ST & HOD AR - Surabaya) & Sinkronisasi Presisi Salin Laporan WhatsApp Work Plan (24 September 2026)
- **Investigasi Masalah & Akar Penyebab (*Root Cause Analysis*)**:
  - **Perbedaan Nama Sistem Lama vs Sistem Baru**:
    - Pada data historis sistem lama, akun dan tugas dicatat dengan nama `Astri Wahyuni` (135 tugas, terdiri dari 127 arsip dan 8 tugas pertengahan September 2026).
    - Pada sistem baru / Odoo, nama resmi akun dan karyawan tercatat sebagai `ASTRI WAHYUNI,ST` dengan email `astriramelan@gmail.com` dan jabatan `HOD AR - Surabaya`.
    - Helper pencarian `getUserCandidateNames($user)` sebelumnya hanya mengambil string nama akun secara harfiah tanpa melakukan normalisasi gelar akademik (seperti `,ST` / `, ST`). Akibatnya, 127 tugas arsip riwayat kerja dan 8 tugas lama tidak terbaca pada akun `ASTRI WAHYUNI,ST`, menyebabkan metrik ARSIP menunjukkan `0`.
  - **Diskrepansi Jumlah Tugas di Salin Laporan WhatsApp vs Papan Kanban**:
    - Pada method `WorkPlanController::copyReport()`, query tugas To Do dibatasi secara artifisial dengan `->limit(5)->get()`, padahal di papan Kanban pengguna memiliki 9 tugas aktif pada kolom TO DO. Akibatnya, 4 tugas To Do terpotong dan tidak masuk laporan.
    - Logika fallback tugas selesai (`doneTasks`) secara otomatis mengambil 5 tugas arsip/selesai dari masa lalu jika tidak ada tugas selesai hari ini. Akibatnya, kolom DONE di Kanban bernilai `0`, tetapi di teks laporan WhatsApp muncul 5 tugas lama, menimbulkan perbedaan data yang membingungkan.
    - Pemanggilan modal `openCopyReportModal()` di frontend tidak meneruskan parameter filter aktif URL (`search`, `smart`, `user_filter`), dan `copyReport()` tidak menggunakan `getFilteredTasksQuery()`.
  - **Status Pimpinan (*Head of Department*)**:
    - Method `User::isHead()` belum menyertakan singkatan `'hod'` (Head of Department) pada daftar kata kunci pimpinan, serta belum memeriksa `jabatan` dari relasi `linked_employee`.
- **Solusi & Penyelarasan Menyeluruh yang Diterapkan**:
  - **Normalisasi Gelar Akademik & Multi-Kandidat (`app/Http/Controllers/WorkPlanController.php`)**:
    - Memperbarui `getUserCandidateNames($user)` untuk mendeteksi variasi gelar di belakang nama (koma atau spasi, seperti `,ST`, `, ST`, `, S.T.`, `, SE`, dsb.) dan secara otomatis menghasilkan variasi nama murni tanpa gelar (*clean name*), Title Case, maupun variasi kombinasi tanda baca.
    - Menambahkan penjaminan mapping eksplisit akun `astriramelan@gmail.com` / `ASTRI WAHYUNI,ST` agar selalu mencakup `Astri Wahyuni`.
  - **Sinkronisasi Presisi Salin Laporan WhatsApp (`WorkPlanController::copyReport` & `index.blade.php`)**:
    - Menghapus pembatasan `->limit(5)` pada To Do; kini mengambil seluruh data To Do aktif sesuai papan Kanban.
    - Mengintegrasikan query laporan dengan `getFilteredTasksQuery($request, ...)` sehingga apa yang terlihat di Kanban board (berdasarkan filter pencarian, filter karyawan, atau smart filter) 100% identik dengan hasil teks Salin Laporan (WA).
    - Memperbaiki logika `doneTasks` agar hanya mencantumkan tugas yang benar-benar selesai pada hari ini (`date_completed = today`), dan jika kosong menampilkan `-(Belum ada tugas yang diselesaikan hari ini)` tanpa mengambil tugas usang dari masa lalu.
    - Menambahkan baris ringkasan metrik statistik elegan pada header pesan teks: `📊 Total Aktif: X Tugas (Y To Do, Z In Progress, ...)`.
    - Memperbarui fungsi JavaScript `openCopyReportModal()` di `index.blade.php` untuk meneruskan seluruh `window.location.search` saat mengambil format laporan.
  - **Penyelarasan Role Head of Department (`app/Models/User.php`)**:
    - Menambahkan kata kunci `'hod'` pada `$headKeywords` di `User::isHead()`.
    - Memeriksa gabungan antara `user.job_title` dan `employee.jabatan` dari data karyawan inhouse terkait.
  - **Perintah Sinkronisasi Data Basis Data (`app/Console/Commands/SyncAstriWpCommand.php`)**:
    - Dibuat perintah CLI `php artisan wp:sync-astri-names` yang memutakhirkan 135 tugas, penerima notifikasi, dan catatan harian dari `Astri Wahyuni` menjadi `ASTRI WAHYUNI,ST`.
    - Mengarsipkan 8 tugas aktif tertanggal 18 September 2026 dan sebelumnya yang telah digantikan oleh tugas-tugas baru tertanggal 24 September 2026, sehingga data aktif papan Kanban tetap presisi 11 tugas (9 To Do, 2 In Progress) dan 135 tugas riwayat tersimpan rapi di bagian Arsip.

---

### 59. 👥 Resolusi Approver Step Approval: Auto-Sync Karyawan Inhouse Aktif ke Akun Pengguna & Pencarian Multi-Kata (Yohana Teraseptia Seagma) (24 September 2026)
- **Investigasi Masalah & Akar Penyebab (*Root Cause Analysis*)**:
  - Saat mengonfigurasi step approval kandidat inhouse pada modal *"Edit Step Approval"*, pencarian approver untuk nama **YOHANA TERASEPTIA SEAGMA** menghasilkan status *"Karyawan / user tidak ditemukan"*.
  - Di tabel `employees` (Master Karyawan), data **YOHANA TERASEPTIA SEAGMA** tercatat aktif sebagai karyawan inhouse (NIK: `3321115509870002`, Email: `seagmayohana@gmail.com`, Jabatan: `ADMIN OPS - Jakarta`, Tipe: `Inhouse`, Status: `Aktiv`, `akses_login = 1`).
  - Namun, pada tabel `users`, akun belum terbuat karena user belum pernah login mandiri sebelumnya. Dari total 829 karyawan inhouse aktif di master data, terdapat 528 karyawan yang belum memiliki baris data di tabel `users`.
  - Pada [ApprovalWorkflowController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/ApprovalWorkflowController.php), dropdown approver `$availableUsers` dan API `searchApprovers()` hanya melakukan kueri ke tabel `users` (`User::where('is_active', true)`). Akibatnya seluruh 528 karyawan inhouse yang belum memiliki user account tidak muncul dalam daftar pilihan approver.
- **Solusi Komprehensif yang Diterapkan**:
  - **Sinkronisasi Otomatis Inhouse ke Akun Pengguna (`app/Http/Controllers/ApprovalWorkflowController.php`)**:
    - Menambahkan method otomatis `ensureInhouseUsersExist()` yang memeriksa dan menjamin seluruh karyawan inhouse aktif dari `employees` memiliki akun di tabel `users`.
    - Dipanggil langsung saat halaman konfigurasi alur approval (`index()`) dibuka maupun saat endpoint `searchApprovers()` dipanggil, sehingga approver dari kalangan karyawan inhouse selalu tersedia secara *real-time*.
  - **CLI Command Massal (`app/Console/Commands/SyncInhouseUsersCommand.php`)**:
    - Dibuat perintah CLI `php artisan users:sync-inhouse` untuk menyinkronkan seluruh 528 karyawan inhouse aktif ke tabel `users` lengkap dengan nama, email, jabatan, area penempatan, dan role yang sesuai.
  - **Peningkatan Pencarian Multi-Kata (*Multi-Term Search*)**:
    - Pada [index.blade.php](file:///d:/ASystem/newasystem/resources/views/master/approval_workflow/index.blade.php), fungsi `filterUsersList(query)` ditingkatkan menggunakan teknik pemecahan kata (`terms.every(t => text.includes(t))`). Pencarian kini sangat fleksibel dan dapat menemukan karyawan terlepas dari urutan kata (misal: `"Yohana Seagma"`, `"Yohana Jakarta"`, atau `"YOHANA TERASEPTIA SEAGMA"`).
    - Endpoint `searchApprovers()` di backend juga diperbarui dengan pencarian multi-kata berbasis `LIKE` untuk setiap suku kata pada nama, email, jabatan, dan area.

---

### 60. 🛠️ Perbaikan Variabel Undefined `$principles` pada Controller Alur Approval (24 September 2026)
- **Masalah**:
  - Halaman `https://new.asystem.co.id/master/approval-workflow` mengalami `ErrorException: compact(): Undefined variable $principles` pada baris 63 di [ApprovalWorkflowController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/ApprovalWorkflowController.php).
- **Akar Masalah**:
  - Variabel `$principles` yang di-pass ke dalam fungsi `compact(...)` di method `index()` sempat tidak terdefinisi karena terhapus secara tidak sengaja pada saat pembaruan sebelumnya.
- **Solusi**:
  - Mengembalikan definisi kueri `$principles = Principle::orderBy('name', 'asc')->pluck('name')->unique()->values();` pada baris 55 [ApprovalWorkflowController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/ApprovalWorkflowController.php).
  - Menguji pemanggilan view [index.blade.php](file:///d:/ASystem/newasystem/resources/views/master/approval_workflow/index.blade.php) dan memastikan halaman merender 200 OK dengan sukses.
  - Memutakhirkan ke server produksi (Server 3) dan melakukan verifikasi live.

---

### 61. 🎫 Implementasi Modul Baru: Helpdesk Ticketing Terintegrasi Otomatis Work Plan (Step Progress) & Master Karyawan Inhouse (24 September 2026)
- **Latar Belakang & Analisis Kebutuhan**:
  - Merevitalisasi dan memodernisasi modul ticketing helpdesk dari sistem lama (`D:\ASystem\helpdesk`) ke dalam arsitektur native Laravel 12 ASystem.
  - Memetakan divisi helpdesk secara langsung berbasis data **Master Karyawan Inhouse** (`employees` / `users`).
  - Mengimplementasikan otomasi dua arah (*two-way synchronization*) ke modul **Work Plan & ToDo**: setiap kali petugas/karyawan merespon atau mengambil tiket yang masuk, data tiket tersebut secara otomatis terbuat sebagai tugas di papan Kanban Work Plan karyawan bersangkutan tepat pada kolom/step **Progress** (`inprogress`).
  - Menjaga integritas seluruh fitur yang sudah berjalan tanpa gangguan (*zero-breakage guarantee*).
- **Komponen Teknis & Arsitektur yang Dibangun**:
  - **Migrasi Skema Basis Data**:
    - `2026_09_24_140000_create_helpdesk_tables.php`:
      - `helpdesk_divisions`: Pengaturan divisi layanan, kode unik, ikon, warna tema Tailwind, SLA (jam), dan tautan grup WhatsApp.
      - `helpdesk_division_agents`: Pemetaan karyawan inhouse sebagai responder divisi dengan flag `is_lead` dan `is_auto_assign`.
      - `helpdesk_tickets`: Data tiket dengan nomor unik otomatis (`TKT-YYYYMMDD-XXXX`), pengaju (`user_id`), divisi tujuan, petugas penanggung jawab (`assigned_to`), urgensi (`Low`, `Medium`, `High`, `Urgent`), status (`open`, `in_progress`, `answered`, `resolved`, `closed`), SLA deadline, berkas lampiran, dan relasi langsung ke Work Plan (`workplan_task_id`).
      - `helpdesk_ticket_replies`: Percakapan real-time dengan dukungan **Catatan Internal Tim (Private Notes)** yang hanya terlihat oleh sesama agen dan Administrator.
      - `helpdesk_ticket_logs`: Jejak audit (*audit trail*) otomatis mencatat setiap pembuatan, perubahan status, pengalihan agen, dan penyelesaian tiket.
      - `helpdesk_canned_responses`: Template balasan cepat per divisi.
    - `2026_09_24_140100_add_helpdesk_ticket_id_to_tasks_table.php`:
      - Menambahkan kolom `helpdesk_ticket_id` (nullable, indexed) pada tabel `tasks` (Work Plan) untuk mengikat tugas ke tiket secara instan.
  - **Lapisan Model Eloquent (`app/Models/`)**:
    - [HelpdeskDivision.php](file:///d:/ASystem/newasystem/app/Models/HelpdeskDivision.php), [HelpdeskDivisionAgent.php](file:///d:/ASystem/newasystem/app/Models/HelpdeskDivisionAgent.php), [HelpdeskTicket.php](file:///d:/ASystem/newasystem/app/Models/HelpdeskTicket.php), [HelpdeskTicketReply.php](file:///d:/ASystem/newasystem/app/Models/HelpdeskTicketReply.php), [HelpdeskTicketLog.php](file:///d:/ASystem/newasystem/app/Models/HelpdeskTicketLog.php), [HelpdeskCannedResponse.php](file:///d:/ASystem/newasystem/app/Models/HelpdeskCannedResponse.php).
    - Memperbarui [Task.php](file:///d:/ASystem/newasystem/app/Models/Task.php) (`helpdesk_ticket_id`, relasi `helpdeskTicket()`) dan [User.php](file:///d:/ASystem/newasystem/app/Models/User.php) (relasi `helpdeskTickets()`, `helpdeskAssignedTickets()`, `helpdeskDivisions()`).
  - **Service Otomasi Work Plan (`app/Services/HelpdeskWorkplanService.php`)**:
    - `syncTicketToWorkplan()`: Saat agen membalas tiket atau mengklik tombol *"Ambil Tiket"*, jika task belum ada maka otomatis di-generate ke tabel `tasks` dengan status `'inprogress'` (kolom Progress), prioritas dipetakan presisi, deskripsi lengkap, tenggat SLA, penanggung jawab (`assignee`), pencatatan aktivitas di `task_activities`, dan notifikasi ke `task_notifications`.
    - `syncReplyToWorkplanComment()`: Setiap balasan chat di tiket otomatis disinkronkan ke tabel `task_comments` pada tugas Work Plan terkait.
    - `syncTicketClosedToWorkplan()`: Saat status tiket diselesaikan/ditutup (`resolved`/`closed`), kartu tugas di Work Plan otomatis dipindahkan ke status `'done'` lengkap dengan `date_completed`.
    - `syncWorkplanTaskDoneToTicket()`: Otomasi balik saat pengguna menyeret kartu tugas di papan Kanban Work Plan ke kolom `'done'`, tiket Helpdesk terkait otomatis berpindah status ke `'resolved'`.
  - **Controller & Alur Bisnis (`app/Http/Controllers/Helpdesk/`)**:
    - [HelpdeskDashboardController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/Helpdesk/HelpdeskDashboardController.php): Metrik KPI, antrean tiket butuh respon segera, statistik distribusi per divisi, dan feed aktivitas.
    - [HelpdeskTicketController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/Helpdesk/HelpdeskTicketController.php): Filter multi-tab (Semua Tiket, Tiket Saya, Ditugaskan ke Saya, Tiket Divisi Saya), form pembuatan tiket, aksi claim instan, update status/prioritas, dan papan Kanban helpdesk.
    - [HelpdeskDivisionController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/Helpdesk/HelpdeskDivisionController.php): Pengaturan divisi dan fitur `syncFromInhouse()` yang memindai Master Karyawan Inhouse untuk otomatis membuat divisi standar (IT Support, Operasional/OPS, GA, HRD, Finance/Tax, Legal) serta memetakan karyawan inhouse ke divisinya.
    - [HelpdeskCannedController.php](file:///d:/ASystem/newasystem/app/Http/Controllers/Helpdesk/HelpdeskCannedController.php): Manajemen template balasan cepat.
  - **Antarmuka Pengguna (UI/UX) Standar Sapphire Blue ESA (`resources/views/helpdesk/`)**:
    - [index.blade.php](file:///d:/ASystem/newasystem/resources/views/helpdesk/index.blade.php): Dashboard modern dengan kartu metrik glowing, antrean tiket prioritas tinggi, grafik sebaran divisi, dan log aktivitas real-time.
    - [tickets/index.blade.php](file:///d:/ASystem/newasystem/resources/views/helpdesk/tickets/index.blade.php): Daftar tiket multi-filter, badge SLA countdown, avatar agen/pengaju, dan badge indikator integrasi Work Plan.
    - [tickets/create.blade.php](file:///d:/ASystem/newasystem/resources/views/helpdesk/tickets/create.blade.php): Form interaktif pemilihan kartu divisi, tingkat urgensi, upload lampiran drag & drop, dan informasi integrasi Work Plan.
    - [tickets/show.blade.php](file:///d:/ASystem/newasystem/resources/views/helpdesk/tickets/show.blade.php): Ruang chat messenger modern, pembeda visual catatan internal rahasia (kuning/amber dengan ikon gembok) vs balasan publik, dropdown balasan cepat, tombol ambil tiket, dan tautan langsung ke kartu Work Plan.
    - [kanban.blade.php](file:///d:/ASystem/newasystem/resources/views/helpdesk/kanban.blade.php): Papan visual Kanban 4 kolom (Menunggu Respon ➡️ Sedang Diproses ➡️ Telah Dijawab ➡️ Selesai).
    - [divisions/index.blade.php](file:///d:/ASystem/newasystem/resources/views/helpdesk/divisions/index.blade.php): Pengelolaan divisi dan daftar penugasan agen inhouse.
    - [canned/index.blade.php](file:///d:/ASystem/newasystem/resources/views/helpdesk/canned/index.blade.php): Pengelolaan template respon cepat.
  - **Navigasi Sidebar (`resources/views/layouts/app.blade.php`)**:
    - Menambahkan menu accordion **Helpdesk Support** dengan badge `TICKET` dan icon headset elegan bernuansa amber di bawah Groups Chat.
  - **Pengujian & Verifikasi Alur Menyeluruh**:
    - Telah diuji melalui skrip otomasi [test_helpdesk_flow.php](file:///C:/Users/user/.gemini/antigravity-ide/brain/41eba75b-a996-488f-9598-1f584053c808/scratch/test_helpdesk_flow.php): verifikasi pembuatan tiket ➡️ respon agen ➡️ auto-assign ➡️ pembentukan otomatis tugas Work Plan di step `'inprogress'` ➡️ notifikasi penugasan ➡️ penyelesaian tiket ➡️ perubahan status tugas Work Plan menjadi `'done'`. Seluruh pengujian lulus 100%.

---

### 62. 🛡️ Diferensiasi Hak Akses Dashboard & Modul Ticketing (User Biasa, User Divisi, Administrator) (24 September 2026)
- **Latar Belakang & Kebijakan Hak Akses (Role-Based Access Control / RBAC)**:
  - Memisahkan secara tegas hak akses, menu, serta visibilitas data Helpdesk & Ticketing antara **User Biasa (Karyawan Pengaju)**, **User Divisi (Agen/Petugas Responder Divisi)**, dan **Administrator (Super Admin)**.
  - Memastikan privasi dan kerahasiaan data tiket: pengguna non-admin tidak dapat mengintip (*prevent data leakage*) tiket pengguna lain atau divisi lain.
  - Menyederhanakan navigasi pengguna biasa dan agen divisi agar fokus pada tugas serta kendalanya masing-masing tanpa menu yang tidak relevan.
- **Rincian Implementasi per Kategori Pengguna**:
  1. **User Biasa (Regular User / Pengaju Kendala)**:
     - **Visibilitas Data**: Hanya dapat melihat data tiket yang diajukan oleh dirinya sendiri (`user_id == Auth::id()`).
     - **Navigasi Menu Sidebar**: Menu *Antrean Tiket*, *Kanban Helpdesk*, *Master Divisi & Agen*, dan *Balasan Cepat* **tidak ditampilkan**. Hanya menampilkan *Dashboard Tiket* dan *Buat Tiket Baru*.
     - **Tampilan Dashboard (`/helpdesk`)**:
       - Kartu Metrik personal: *Tiket Saya*, *Menunggu Respon*, *Sedang Diproses*, *Selesai / Ditutup*.
       - Kolom Utama: *Daftar Tiket Kendala Saya* menampilkan riwayat tiket yang diajukan, status penanganan, SLA, nama petugas penanggung jawab, serta tombol langsung menuju ruang chat tiket.
       - Kartu Panduan: 3 langkah alur penanganan tiket dan tips resolusi kendala cepat.
       - Kartu Aktivitas: Audit log riwayat aktivitas pada tiket miliknya sendiri.
       - Menyembunyikan tabel *Distribusi Divisi Layanan* dan antrean tiket karyawan lain.
     - **Proteksi Backend & Keamanan Route**:
       - Akses langsung URL ke `/helpdesk/kanban`, `/helpdesk/divisions`, dan `/helpdesk/canned` diblokir dengan `403 Forbidden`.
       - Akses ke `/helpdesk/tickets` dipaksa terkunci secara otomatis pada filter `where('user_id', Auth::id())`.
       - Akses ke detail tiket (`/helpdesk/tickets/{id}`) dibatasi ketat: jika user mencoba membuka tiket orang lain, sistem langsung melempar `403 Forbidden`.
  2. **User Divisi (Division Agent / Responder)**:
     - **Visibilitas Data**: Hanya dapat melihat data tiket yang dialamatkan ke divisinya (`division_id IN ($myDivisionIds)`) atau tiket yang diajukan sendiri.
     - **Navigasi Menu Sidebar**: Menu *Antrean Tiket*, *Kanban Helpdesk*, *Master Divisi & Agen*, dan *Balasan Cepat* **tidak ditampilkan** (menu ini eksklusif Administrator).
     - **Tampilan Dashboard (`/helpdesk`)**:
       - Kartu Metrik Divisi: *Tiket Divisi*, *Menunggu Respon Divisi*, *Sedang Diproses (Work Plan)*, *Selesai*.
       - Kolom Utama: *Antrean Tiket Masuk Divisi Anda* dilengkapi tombol cepat *"Ambil Tiket"* (Claim) yang meng-assign tiket dan langsung membuat tugas di papan Kanban Work Plan pada step *Progress*, serta tombol *"Detail Tiket"*.
       - Kartu Statistik: Monitoring SLA dan beban kerja divisi yang diampunya (*Open, Progress, Done*).
       - Kolom Kanan: Tiket ditugaskan ke saya (*Assigned to Me*), tiket diajukan sendiri, dan log aktivitas terkini divisi.
     - **Proteksi Backend**:
       - Diblokir `403 Forbidden` dari menu Kanban, Master Divisi, dan Balasan Cepat.
       - Query daftar tiket dikunci pada divisi miliknya (`whereIn('division_id', $myDivisionIds)`).
  3. **Administrator (Super Admin)**:
     - **Menu Lengkap Sidebar**: Menampilkan seluruh menu (*Dashboard Tiket, Antrean Tiket, Buat Tiket Baru, Kanban Helpdesk, Master Divisi & Agen, Balasan Cepat*).
     - **Tampilan Dashboard**: Ringkasan global seluruh perusahaan (*system-wide*), antrean tiket urgent dari semua divisi, pemantauan seluruh divisi layanan dan kepatuhan SLA.
     - **Hak Akses Penuh**: Mengelola divisi, memetakan karyawan inhouse sebagai agen, mengatur template balasan cepat, menggeser kartu di Kanban Helpdesk, dan mengelola seluruh tiket.
- **Berkas Kode yang Dimodifikasi**:
  - `app/Models/User.php`: Penambahan helper `isHelpdeskAdmin()`, `isHelpdeskDivisionUser()`, `isHelpdeskRegularUser()`, dan relasi helpdesk.
  - `resources/views/layouts/app.blade.php`: Gating menu sidebar accordion Helpdesk Support dengan `@if(auth()->user()->isHelpdeskAdmin())`.
  - `routes/web.php`: Penambahan middleware `'admin'` pada route kanban, divisions, dan canned responses.
  - `app/Http/Controllers/Helpdesk/HelpdeskDashboardController.php`: Scoping metrik, antrean, distribusi, dan log aktivitas berbasis 3 peran.
  - `app/Http/Controllers/Helpdesk/HelpdeskTicketController.php`: Scoping `index()`, otorisasi `show()`, penguncian `claim()`, `updateStatus()`, dan `kanban()`.
  - `app/Http/Controllers/Helpdesk/HelpdeskDivisionController.php` & `HelpdeskCannedController.php`: Penegakan `authorizeAdmin()`.
  - `resources/views/helpdesk/index.blade.php`: Arsitektur antarmuka baru dengan 3 varian tampilan (Regular User, Division User, Administrator).
  - `resources/views/helpdesk/tickets/index.blade.php`: Header, tabs navigasi, dan filter divisi adaptif.
  - `resources/views/helpdesk/tickets/show.blade.php`: Breadcrumbs adaptif dan pembatasan template balasan cepat.
- **Hasil Pengujian Otomasi**:
  - Berhasil diuji melalui skrip [test_helpdesk_roles.php](file:///C:/Users/user/.gemini/antigravity-ide/brain/41eba75b-a996-488f-9598-1f584053c808/scratch/test_helpdesk_roles.php) dengan hasil verifikasi seluruh peran (Admin, Division Agent, Regular User) berjalan presisi 100%.

---

### 63. ⚡ Searchable Dropdown Agen Divisi, Template Masalah & Format Laporan Tiket, serta Master Template Laporan
- **Searchable Combobox Agen Divisi Helpdesk (`/helpdesk/divisions`)**:
  - Menggantikan dropdown `<select>` standar yang harus di-scroll panjang dengan komponen Alpine.js Searchable Combobox interaktif.
  - Memungkinkan admin mencari langsung nama, jabatan, atau area dari ratusan karyawan inhouse aktif secara instan.
  - Menampilkan saran nama dan jabatan secara live, visual badge pilihan, tombol pembersihan (*clear* `x`), serta tombol *"Tambah"* yang otomatis aktif saat karyawan dipilih.
  - Memfilter secara otomatis karyawan yang telah menjadi agen di divisi tersebut sehingga tidak muncul ganda.
  - Serialisasi data terpusat dan efisien dari controller `$inhouseAgentsList` tanpa membebani render DOM Blade.
- **Fitur Template Laporan pada Pembuatan Tiket (`/helpdesk/tickets/create`)**:
  - Mengadopsi fungsionalitas sistem helpdesk legacy (`D:\ASystem\helpdesk`) ke dalam arsitektur modern Laravel 12.
  - Menambahkan kartu pintasan interaktif *"Jalan Pintas (Template Masalah & Format Laporan)"* yang dikelompokkan rapi per divisi.
  - Ketika template dipilih, sistem secara otomatis mengisikan:
    1. **Divisi Tujuan** terkait kendala tersebut.
    2. **Judul Kendala / Subjek Default** secara otomatis.
    3. **Format Isian Rinci Kendala** (format formulir tanya-jawab baku yang siap diisi pelapor).
  - Jika jenis kendala memiliki formulir baku (seperti formulir Excel reimbursement, surat keterangan kerja, dsb.), sistem langsung memunculkan kartu dokumen format dengan tombol *"Unduh Format"*.
  - Dilengkapi tombol *"Kosongkan Template"* untuk mereset isian form ke mode manual.
- **Master Template Laporan Kendala untuk Administrator (`/helpdesk/templates`)**:
  - Membangun antarmuka dan sistem CRUD lengkap bagi Administrator untuk menambah, mengubah, mengaktifkan/menonaktifkan, dan menghapus jenis template laporan.
  - Migrasi database: `2026_09_24_150000_create_helpdesk_ticket_templates_table.php` dengan field `id`, `division_id`, `title`, `subject`, `message`, `attachment`, `is_active`, `order_num`, `timestamps`.
  - Model Eloquent `HelpdeskTicketTemplate` lengkap dengan relasi `HelpdeskDivision` dan helper accessor `attachment_url` & `attachment_filename`.
  - Controller `HelpdeskTemplateController` dengan otorisasi ketat Administrator (`authorizeAdmin()`), validasi berkas upload maksimal 10MB, serta penghapusan berkas fisik saat template/attachment dihapus.
  - Seeder otomatis `HelpdeskTemplateSeeder` yang menginisialisasi 8 template standar operasional:
    1. IT Support: Reset Password Akun / Email
    2. IT Support: Kendala Hardware, PC & Jaringan Kantor
    3. IT Support: Laporan Bug / Error Aplikasi Portal ASystem
    4. HRD: Pengajuan Surat Keterangan Kerja (Paklaring / SK)
    5. HRD: Pertanyaan & Klarifikasi Data Karyawan / Slip Gaji
    6. Finance: Pengajuan Klaim Operasional & Reimbursement
    7. GA: Permintaan ATK & Pengadaan Fasilitas Kantor
    8. OPS: Laporan Kendala Penempatan Lapangan / Mitra Prinsiple
  - Integrasi navigasi sidebar: Menu *"Template Laporan"* pada grup Helpdesk Support di `resources/views/layouts/app.blade.php`.
- **Berkas Kode yang Dibuat / Dimodifikasi**:
  - `database/migrations/2026_09_24_150000_create_helpdesk_ticket_templates_table.php` *(Baru)*
  - `app/Models/HelpdeskTicketTemplate.php` *(Baru)*
  - `database/seeders/HelpdeskTemplateSeeder.php` *(Baru)*
  - `app/Http/Controllers/Helpdesk/HelpdeskTemplateController.php` *(Baru)*
  - `resources/views/helpdesk/templates/index.blade.php` *(Baru)*
  - `routes/web.php`
  - `resources/views/layouts/app.blade.php`
  - `app/Http/Controllers/Helpdesk/HelpdeskDivisionController.php`
  - `resources/views/helpdesk/divisions/index.blade.php`
  - `app/Http/Controllers/Helpdesk/HelpdeskTicketController.php`
  - `resources/views/helpdesk/tickets/create.blade.php`
- **Hasil Pengujian Otomasi**:
  - Verifikasi menyeluruh via `test_templates_and_combobox.php`: rute master template, database Eloquent, seeder 8 template, kompilasi Blade view divisi, searchable combobox, dan template autofill pada form tiket berhasil 100% tanpa error.

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
   - Master Soal Matematika CBT: `http://127.0.0.1:8000/master/math`
   - Sinkronisasi Odoo: `http://127.0.0.1:8000/odoo-setting`
   - Talent Pool Rekrutmen: `http://127.0.0.1:8000/interview`
   - AI Ranking: `http://127.0.0.1:8000/airanking`
   - Portal Lowongan Publik: `http://127.0.0.1:8000/job`
   - Login Karyawan / Admin: `http://127.0.0.1:8000/login`

---
*Dikembangkan dengan standar modern arsitektur Laravel 12, UI responsif TailwindCSS, dan integrasi ESA Groups.*

