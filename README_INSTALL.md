# 🚀 Panduan Instalasi & Deployment ASystem Portal

Paket ini sudah **100% siap pakai (Clean, Production-Ready, Plug & Play)**.
Seluruh dependensi PHP (`vendor/`), struktur database modern, serta **seluruh data migrasi database (64.118 kandidat, 1,81 juta jawaban tes, akun user, dan setting)** sudah disertakan di dalam paket file installer (`asystem_interview` & `database/database.sqlite`).

---

## 🔐 Akun Login Bawaan (Default Administrator)
Setelah instalasi selesai atau file diekstrak ke server, Anda dapat langsung login melalui halaman `/login`:
- **URL Login**: `https://domain-anda.com/login` (atau `http://IP-SERVER/login`)
- **Email**: `admin@asystem.co.id`
- **Password**: `password`
- **Akses**: `Administrator (SUPER ADMIN - All / Seluruh Indonesia)`

---

## 📦 Pilihan Metode Instalasi ke Server

### METODE 1: cPanel / Shared Hosting (Paling Cepat & Mudah)
1. **Upload File ZIP**:
   - Buka **File Manager** di cPanel Anda.
   - Upload file zip installer ini ke direktori domain Anda (misal `public_html` atau direktori subdomain).
2. **Ekstrak ZIP**:
   - Klik kanan file ZIP -> pilih **Extract**.
3. **Konfigurasi Document Root**:
   - Pastikan **Document Root** domain / subdomain Anda mengarah ke folder `public` (misal: `public_html/public`).
4. **Izin Akses File (Permissions)**:
   - Pastikan folder `storage/`, `bootstrap/cache/`, dan file database `asystem_interview` memiliki izin tulis (Permissions `775` untuk folder, `664` untuk file SQLite).
5. **Selesai!**
   - Buka browser dan akses website Anda. Aplikasi langsung berjalan dengan seluruh data kandidat nasional.

---

### METODE 2: Linux VPS / Cloud Server (Ubuntu / Debian / AlmaLinux)
1. Upload file ZIP ke server Anda (misal ke `/var/www/asystem`).
2. Masuk via SSH dan ekstrak paket:
   ```bash
   cd /var/www/asystem
   unzip asystem_production_installer.zip
   ```
3. Jalankan automated installer script:
   ```bash
   chmod +x install.sh
   ./install.sh
   ```
4. Script akan secara otomatis:
   - Memeriksa PHP & ekstensi yang dibutuhkan.
   - Menyiapkan izin direktori `storage`, `bootstrap/cache`, dan database SQLite.
   - Menghubungkan symlink storage (`php artisan storage:link`).
   - Melakukan optimasi cache produksi (`config:cache`, `route:cache`, `view:cache`).
5. Arahkan Nginx / Apache Document Root ke `/var/www/asystem/public`.

---

### METODE 3: Windows Server (IIS / XAMPP / Laragon)
1. Ekstrak file ZIP ke direktori web server Anda (misal `C:\xampp\htdocs\asystem` atau `C:\inetpub\wwwroot\asystem`).
2. Buka Command Prompt / PowerShell sebagai Administrator di folder tersebut.
3. Jalankan script installer:
   ```cmd
   install.bat
   ```
4. Arahkan Document Root IIS / Virtual Host Apache ke folder `public`.

---

### METODE 4: Web Wizard Installer (Antarmuka Grafis)
Jika Anda ingin mengatur URL baru atau menghubungkan ke database MySQL eksternal melalui antarmuka web interaktif:
1. Pastikan file flag `storage/app/.installed` belum ada (hapus jika ada).
2. Akses halaman:
   `https://domain-anda.com/install`
3. Ikuti langkah-langkah pada layar:
   - Pengecekan kesiapan server (PHP, PDO, Mbstring, OpenSSL, Storage writable).
   - Pilih koneksi: **SQLite** (bawaan) atau **MySQL** (jika ingin dialihkan ke MySQL).
   - Buat akun administrator baru.
4. Klik **Pasang Aplikasi Sekarang**. Sistem akan memproses dan langsung mengarahkan Anda ke dashboard.

---

## ⏰ Pengaturan Otomatisasi (Cron Job / Background Worker)
Untuk memastikan background job (seperti pengiriman WA otomatis atau sync terjadwal) berjalan lancar, tambahkan entri Cron Job berikut di server Anda (`crontab -e`):
```bash
* * * * * cd /path/ke/folder/asystem && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🛠️ Catatan Teknis File Database
- **SQLite Database Path**: File database utama berada di file `asystem_interview` (root) dan salinan identik di `database/database.sqlite`.
- **Integrasi Dokumen, Bukti Refcek & Approval Prinsiple**:
  Aplikasi telah dilengkapi fitur fallback otomatis. Jika berkas lama belum di-upload ke server baru, sistem secara otomatis mengambil tampilan berkas atau redirect dari server arsip live:
  1. **CV & Pasfoto Kandidat**: `https://asystem.co.id/interview/lampiran/{filename}`
  2. **Lampiran Referensi Cek**: `https://asystem.co.id/v3/refcekfile/{filename}`
  3. **Lampiran Approval Prinsiple**: `https://asystem.co.id/v3/approval/{filename}` (atau `https://asystem.co.id/v3/prinsiple/ttdfileprinsiple/{filename}`)
  4. **Aset Modul V3 Lainnya**: `https://asystem.co.id/v3/{path}`

---
&copy; 2026 PT Arina Multikarya - ASystem Portal ESA Groups. All rights reserved.

