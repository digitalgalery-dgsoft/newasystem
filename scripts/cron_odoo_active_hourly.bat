@echo off
REM ==============================================================================
REM 🚀 ASYSTEM SUPPORT SYSTEM - WINDOWS CRON SINKRONISASI EMPLOYEE AKTIF (HOURLY)
REM Aturan: Hanya ambil employee yang aktif di Odoo. NIK yang sudah ada TIDAK diupdate.
REM ==============================================================================

cd /d "%~dp0\.."
echo [%DATE% %TIME%] Menjalankan Cron Odoo Sync Active (Hourly)...
php artisan odoo:sync-active --silent
echo [%DATE% %TIME%] Cron Selesai.
