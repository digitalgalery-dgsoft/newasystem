@echo off
REM ==============================================================================
REM 🚀 ASYSTEM SUPPORT SYSTEM - WINDOWS SERVER INSTALLER & DEPLOYMENT SCRIPT
REM Replikasi pola deployment otomatis Attendance (att-admin-v12)
REM ==============================================================================

echo ================================================================
echo    ASYSTEM SUPPORT SYSTEM - WINDOWS PRODUCTION INSTALLER
echo ================================================================
echo Waktu Eksekusi: %DATE% %TIME%
echo.

echo [1/6] Memeriksa PHP...
php -v >nul 2>&1
if %ERRORLEVEL% NEQ 0 (
    echo [ERROR] PHP tidak ditemukan dalam PATH Windows!
    pause
    exit /b 1
)
echo   ✓ PHP terdeteksi.

echo [2/6] Menyiapkan File .env...
if not exist ".env" (
    if exist ".env.example" (
        copy .env.example .env
        echo   ✓ File .env dibuat dari .env.example
    ) else (
        type nul > .env
        echo   ✓ File .env baru dibuat.
    )
) else (
    echo   ✓ File .env sudah ada.
)

echo [3/6] Menginstall Dependensi Composer...
call composer install --no-dev --optimize-autoloader --no-interaction

echo [4/6] Menyiapkan Folder Storage...
if not exist "storage\framework\sessions" mkdir storage\framework\sessions
if not exist "storage\framework\views" mkdir storage\framework\views
if not exist "storage\framework\cache" mkdir storage\framework\cache
if not exist "storage\logs" mkdir storage\logs
if not exist "storage\app\temp-pdf" mkdir storage\app\temp-pdf
if not exist "bootstrap\cache" mkdir bootstrap\cache
echo   ✓ Folder storage siap.

echo [5/6] Generate Key dan Migrasi Database...
call php artisan key:generate --force
call php artisan migrate --force
call php artisan storage:link --force 2>nul
echo Installed on %DATE% %TIME% via install.bat > storage\app\.installed
echo   ✓ Database termigrasi dan .installed dibuat.

echo [6/6] Optimasi Cache Laravel...
call php artisan config:clear
call php artisan route:clear
call php artisan view:clear

echo.
echo ================================================================
echo   INSTALASI ASYSTEM BERHASIL DISELESAIKAN!
echo ================================================================
echo Silakan jalankan web server (misal: php artisan serve) atau buka
echo halaman login di browser Anda.
echo ================================================================
pause
