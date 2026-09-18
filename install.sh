#!/usr/bin/env bash
# ==============================================================================
# 🚀 ASYSTEM SUPPORT SYSTEM - AUTOMATED SERVER INSTALLER & DEPLOYMENT SCRIPT
# Replikasi pola deployment otomatis Attendance (att-admin-v12)
# ==============================================================================

set -e

# Warna Terminal
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

echo -e "${CYAN}================================================================${NC}"
echo -e "${CYAN}   🚀 ASYSTEM SUPPORT SYSTEM - PRODUCTION SERVER INSTALLER      ${NC}"
echo -e "${CYAN}================================================================${NC}"
echo -e "Waktu Eksekusi: ${YELLOW}$(date '+%Y-%m-%d %H:%M:%S')${NC}"
echo -e "Host Server:    ${YELLOW}$(hostname) ($(curl -s ifconfig.me 2>/dev/null || echo 'local'))${NC}\n"

# 1. Periksa Kebutuhan PHP
echo -e "${BLUE}[1/7] Memeriksa Lingkungan PHP...${NC}"
if ! command -v php &> /dev/null; then
    echo -e "${RED}Error: PHP belum terpasang di server!${NC}"
    exit 1
fi

PHP_VER=$(php -r "echo PHP_VERSION;")
echo -e "  ✓ PHP Terpasang: ${GREEN}${PHP_VER}${NC}"

# 2. Periksa Composer
echo -e "${BLUE}[2/7] Memeriksa Composer...${NC}"
if ! command -v composer &> /dev/null; then
    if [ -d "vendor" ]; then
        echo -e "  ✓ Composer CLI tidak ditemukan, namun direktori ${GREEN}vendor/${NC} sudah disertakan dalam paket."
    else
        echo -e "${RED}Error: Composer belum terpasang di server dan folder vendor tidak ditemukan!${NC}"
        exit 1
    fi
else
    echo -e "  ✓ Composer terdeteksi."
fi

# 3. Setup File Konfigurasi .env
echo -e "${BLUE}[3/7] Menyiapkan Konfigurasi .env...${NC}"
if [ ! -f ".env" ]; then
    if [ -f ".env.example" ]; then
        cp .env.example .env
        echo -e "  ✓ Berkas .env berhasil dibuat dari .env.example"
    else
        touch .env
        echo -e "  ✓ Berkas .env baru dibuat"
    fi
else
    echo -e "  ✓ Berkas .env sudah tersedia."
fi

# 4. Install Dependencies via Composer (jika composer tersedia)
if command -v composer &> /dev/null; then
    echo -e "${BLUE}[4/7] Mengoptimalkan Dependensi Composer (Production Mode)...${NC}"
    composer install --no-dev --optimize-autoloader --no-interaction
else
    echo -e "${BLUE}[4/7] Melompati instalasi Composer (menggunakan paket vendor bawaan)...${NC}"
fi

# 5. Atur Hak Akses Direktori (Permissions)
echo -e "${BLUE}[5/7] Mengatur Hak Akses Direktori Storage, Database & Cache...${NC}"
mkdir -p storage/framework/{sessions,views,cache}
mkdir -p storage/logs
mkdir -p storage/app/temp-pdf
mkdir -p bootstrap/cache
mkdir -p database
chmod -R 775 storage bootstrap/cache 2>/dev/null || true
chmod 775 database 2>/dev/null || true
chmod 664 asystem_interview database/database.sqlite 2>/dev/null || true
echo -e "  ✓ Direktori storage, database & cache siap dengan izin tulis."

# 6. Generate APP_KEY & Migrasi Database
echo -e "${BLUE}[6/7] Menyiapkan Application Key & Migrasi Skema Database...${NC}"
php artisan key:generate --force || true
php artisan migrate --force
php artisan storage:link --force 2>/dev/null || true

# Tandai status terinstal
mkdir -p storage/app
echo "Installed via install.sh on $(date '+%Y-%m-%d %H:%M:%S')" > storage/app/.installed
echo -e "  ✓ Migrasi database selesai & tanda .installed berhasil ditulis."

# 7. Optimasi Cache Laravel untuk Kecepatan Tinggi
echo -e "${BLUE}[7/7] Melakukan Optimasi Cache Laravel...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo -e "\n${GREEN}================================================================${NC}"
echo -e "${GREEN}  🎉 INSTALASI ASYSTEM SUPPORT SYSTEM BERHASIL DISELESAIKAN!     ${NC}"
echo -e "${GREEN}================================================================${NC}"
echo -e "Aplikasi Anda sekarang siap melayani pengguna di server produksi."
echo -e "Silakan tambahkan Cron Job berikut di server Anda (${CYAN}crontab -e${NC}):"
echo -e "${YELLOW}* * * * * cd $(pwd) && php artisan schedule:run >> /dev/null 2>&1${NC}"
echo -e "${GREEN}================================================================${NC}\n"
