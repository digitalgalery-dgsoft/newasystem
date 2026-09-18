@echo off
REM ==============================================================================
REM 🌙 ASYSTEM SUPPORT SYSTEM - WINDOWS CRON UPDATE & RESIGN (MIDNIGHT)
REM ==============================================================================

cd /d "%~dp0\.."
echo [%DATE% %TIME%] Menjalankan Cron Odoo Updates & Resigns (Midnight)...
php artisan odoo:sync-updates-resigns --silent
echo [%DATE% %TIME%] Cron Selesai.
