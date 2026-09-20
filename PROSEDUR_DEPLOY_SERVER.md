# 🚀 Panduan & Prosedur Deployment Server Production ASystem

Dokumen ini berisi panduan resmi, konfigurasi infrastruktur, dan langkah-langkah operasional untuk melakukan deployment aplikasi **ASystem Support System** ke server live production (`new.asystem.co.id`).

---

## 🌐 1. Informasi Infrastruktur & Server

| Parameter | Konfigurasi Production |
| :--- | :--- |
| **Domain Utama** | [https://new.asystem.co.id](https://new.asystem.co.id) |
| **Target Server** | Server 3: PT Anugrah Talenta Berkarya / ATK (`38.103.170.224`) |
| **Direktori Web Root** | `/www/wwwroot/new.asystem.co.id` |
| **Repository GitHub** | `https://github.com/digitalgalery-dgsoft/newasystem.git` |
| **Branch Production** | `main` |
| **Web Server & User** | Nginx / OpenResty (`www:www`) |
| **PHP Runtime** | PHP 8.3 CLI & PHP-FPM (`/www/server/php/83/bin/php`) |
| **Database** | SQLite (`database/database.sqlite` & `asystem_interview`) |
| **Security Token** | `dgsoft_rahasia_123` |

---

## ⚡ 2. Cara Cepat Deploy (Metode Utama: 1-Click Remote Runner)

Anda dapat langsung melakukan deployment dari lingkungan lokal (laptop/komputer pengembang) ke server production secara instan tanpa perlu membuka SSH secara manual.

### Perintah Eksekusi:
Jalankan perintah berikut di terminal/PowerShell direktori project:
```bash
php scripts/deploy_production.php
```

### Mekanisme Kerja Skrip:
1. Skrip menghubungi deployment runner remote di `https://appsend.my.id/deploy-production.php`.
2. Runner mengirimkan instruksi ke Server 3 (`new.asystem.co.id`) untuk:
   - Masuk ke direktori web root: `cd /www/wwwroot/new.asystem.co.id`
   - Mengambil commit terbaru dari GitHub: `git fetch origin main`
   - Menerapkan branch terbaru secara aman: `git reset --hard origin/main`
   - Menjalankan migrasi database jika ada: `php artisan migrate --force`
   - Membersihkan seluruh cache Laravel: `php artisan optimize:clear`
   - Menampilkan hash commit terakhir yang sedang aktif: `git log -1 --oneline`
3. Menjalankan HTTP Health Check Ping (`HTTP 200`) untuk memastikan aplikasi live tanpa error.

---

## 🖥️ 3. Metode Alternatif (Deploy Langsung via SSH Terminal)

Jika Anda sedang terhubung langsung ke terminal SSH server (`38.103.170.224`), gunakan salah satu prosedur berikut:

### Opsi A: Menggunakan Shell Script Resmi
```bash
cd /www/wwwroot/new.asystem.co.id
bash deploy.sh
```

### Opsi B: Perintah Manual Langkah demi Langkah
```bash
cd /www/wwwroot/new.asystem.co.id

# 1. Pastikan safe directory git
git config --global --add safe.directory /www/wwwroot/new.asystem.co.id

# 2. Bersihkan file migrasi legacy/orphan (jika ada)
rm -f database/migrations/2026_07_* 2>/dev/null || true
git clean -f database/migrations/ 2>/dev/null || true
rm -rf app/Providers/Filament 2>/dev/null || true

# 3. Tarik update terbaru dari branch main
git fetch origin main
git reset --hard origin/main

# 4. Jalankan migrasi database
php artisan migrate --force

# 5. Set hak akses web server
chown -R www:www /www/wwwroot/new.asystem.co.id
chmod 664 asystem_interview database/database.sqlite 2>/dev/null || true

# 6. Bersihkan cache bootstrap, config, route, dan view
php artisan optimize:clear
```

---

## 📋 4. Standar Operasional Prosedur (SOP) Sebelum & Sesudah Deploy

Untuk memastikan deployment berjalan aman, selalu ikuti urutan langkah berikut:

### Sebelum Deploy (Pre-Deploy Checklist):
1. **Validasi Sintaks & Rute Lokal**:
   ```bash
   php artisan route:list
   ```
   Pastikan tidak ada sintaks error PHP atau route/controller yang hilang.
2. **Periksa Perubahan Berkas**:
   ```bash
   git status
   git diff
   ```
3. **Catat Perubahan pada `UPDATE_PROGRESS.md`**:
   Dokumentasikan milestone pembaruan dan commit hash terkait.
4. **Commit & Push ke GitHub**:
   ```bash
   git add .
   git commit -m "feat/fix: deskripsi perubahan ringkas dan padat"
   git push origin main
   ```

### Eksekusi Deploy:
```bash
php scripts/deploy_production.php
```

### Setelah Deploy (Post-Deploy Verification):
1. Buka browser dan periksa endpoint yang baru saja diubah, misalnya:
   - Halaman Interview Selesai: `https://new.asystem.co.id/interviewdone`
   - Halaman Arsip Interview: `https://new.asystem.co.id/interviewarsip`
   - Halaman Kandidat Inhouse: `https://new.asystem.co.id/interviewinhouse`
   - Halaman Job Portal: `https://new.asystem.co.id/kandidatportal`
2. Pastikan tidak ada pesan error `500 Server Error` atau halaman blank.
3. Cek log server jika diperlukan:
   - Path log aplikasi: `/www/wwwroot/new.asystem.co.id/storage/logs/laravel.log`

---

## ⏱️ 5. Layanan Latar Belakang (Cron & Worker) di Production

Aplikasi ASystem memanfaatkan crontab server untuk beberapa tugas otomatis:

1. **Sinkronisasi Data Employee Odoo ERP (Hourly)**:
   - Jam kerja: Setiap jam pukul 08:00 - 18:00 WIB.
   - Perintah: `php artisan odoo:sync --active-only`
2. **Sinkronisasi Resign & Update Odoo (Midnight)**:
   - Tengah malam: Pukul 00:00 WIB.
   - Perintah: `php artisan odoo:sync`
3. **AI CV Analyzer Runner (Tiap Menit)**:
   - Perintah: `php artisan schedule:run` atau `php artisan ai:cv-analyzer` (memproses 2 kandidat per menit dengan interval 30 detik).

---

*Dokumen ini diperbarui secara berkala mengikuti arsitektur sistem terbaru ASystem.*
