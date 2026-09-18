@echo off
REM ==============================================================================
REM 🚀 ASYSTEM SUPPORT SYSTEM - WINDOWS CRON SINKRONISASI ODOO OTOMATIS
REM ==============================================================================

cd /d "%~dp0\.."
echo [%DATE% %TIME%] Menjalankan Sinkronisasi Odoo Otomatis...
php artisan odoo:sync --trigger=cron --silent
echo [%DATE% %TIME%] Sinkronisasi Selesai.
