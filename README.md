# ASystem - Support System ESA Groups

Platform modern manajemen rekrutmen, interview, evaluasi kandidat, dan portal lowongan kerja berbasis **Laravel 12** dan **Attendance HR Enterprise Design System (`att-admin-v12`)**.

---

## Fitur Utama

1. **Pusat Fitur (Hub Dashboard)**:
   - Akses terpusat ke seluruh modul dan layanan HR operasional.
   
2. **Master Data**:
   - **Master Karyawan**: Data pegawai internal/inhouse dengan filter area dan jabatan.
   - **Master Prinsiple**: Manajemen mitra prinsiple dan integrasi area.

3. **Input Job Requirement (`/inputjob`)**:
   - Pembuatan spesifikasi lowongan kerja dengan integrasi AI Job Assistant.
   - Quick preset posisi populer (Admin, SPG, Digital Marketing, Software Engineer).
   - Generasi otomatis QR Code lowongan beresolusi tinggi untuk disebarkan ke pelamar.

4. **Kandidat Portal (`/kandidatportal`)**:
   - Manajemen pelamar dari portal job publik.
   - Filter tahapan (*Pelamar Baru, Tahap Interview, Diterima, Arsip*).
   - Klasifikasi kategori AI (*Green ≥ 80%, Yellow 60-79%, Red < 60%*).
   - Fitur Reset Password akun pelamar instan ke format tanggal lahir (`ddmmyyyy`).

5. **Detail & Evaluasi Kandidat Portal (`/kandidatportal/{id}`)**:
   - **Profil Kandidat**: 11 data pokok identitas + dropzone foto profil 3x4 dengan instant live preview.
   - **Download All Document**: Ekspor multi-page PDF berukuran kertas Legal dengan kop resmi perusahaan, QR Code, dan lampiran bukti persetujuan / chat WA.
   - **7 Tab Evaluasi Komprehensif**:
     - **Tab 1: Hasil Interview**: Matriks penilaian 4 aspek (*Kemauan Kerja, Penampilan, Attitude, Daya Tangkap*) + Canvas Tanda Tangan Digital.
     - **Tab 2: Referensi Cek**: Form evaluasi atasan terdahulu 2 kolom + dropzone upload screenshot chat WhatsApp.
     - **Tab 3: Tes Komputer**: Matriks 9 rumus dan keahlian fungsi Microsoft Excel.
     - **Tab 4: Analisa AI (CV Analyzer)**: Radial score gauge (0-100%), badge kesesuaian, pemetaan matched vs missing skills, dan rekomendasi pertanyaan interview.
     - **Tab 5: Tes Kepribadian (DISC)**: Durasi pengerjaan, skor 4 tipe (A, B, C, D), kesimpulan watak dominan (Melankolis), dan 40 butir pertanyaan-jawaban.
     - **Tab 6: Tes Matematika**: 10 butir soal matematika kerja dengan koreksi otomatis benar/salah, tombol kirim remidi, dan kalkulasi nilai akhir.
     - **Tab 7: User Prinsiple**: Status persetujuan prinsiple, catatan, dan formulir persetujuan dengan upload bukti screenshot approval.

6. **Kandidat Interview (`/interview`)**:
   - Manajemen kandidat proses wawancara tatap muka, interview selesai, dan arsip.

---

## Persyaratan Sistem

- PHP >= 8.2 (Disarankan PHP 8.3)
- Composer
- SQLite 3 (Database default) atau MySQL
- Ekstensi PHP: `pdo_sqlite`, `gd`, `curl`, `mbstring`, `openssl`

---

## Panduan Instalasi & Menjalankan

1. **Clone Repository**:
   ```bash
   git clone https://github.com/digitalgalery-dgsoft/newasystem.git
   cd newasystem
   ```

2. **Instal Dependensi**:
   ```bash
   composer install
   ```

3. **Konfigurasi Environment**:
   Salin berkas `.env.example` ke `.env`:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database**:
   Aplikasi secara default telah menyertakan database SQLite `asystem_interview`. Anda juga dapat menjalankan migrasi fresh jika diperlukan:
   ```bash
   php artisan migrate
   ```

5. **Jalankan Server**:
   ```bash
   php artisan serve
   ```
   Akses aplikasi pada: `http://127.0.0.1:8000`

---

&copy; PT Arina Multikarya - Support System ESA Groups.