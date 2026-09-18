#!/usr/bin/env bash
# ==============================================================================
# 🚀 ASYSTEM SUPPORT SYSTEM - ONE-CLICK PRODUCTION DEPLOY SCRIPT
# ==============================================================================

set -e

GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
CYAN='\033[0;36m'
NC='\033[0m'

BASE_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$BASE_DIR"

echo -e "${CYAN}================================================================${NC}"
echo -e "${CYAN}   🚀 ASYSTEM SUPPORT SYSTEM - ONE-CLICK AUTO DEPLOY            ${NC}"
echo -e "${CYAN}================================================================${NC}"
echo -e "Waktu:     ${YELLOW}$(date '+%Y-%m-%d %H:%M:%S')${NC}"
echo -e "Direktori: ${YELLOW}${BASE_DIR}${NC}\n"

# 1. Safe directory config
echo -e "${BLUE}[1/7] Menyetel Git Safe Directory...${NC}"
git config --global --add safe.directory "$BASE_DIR" 2>/dev/null || true

# 2. Hapus berkas migrasi sisa lama & berkas orphan
echo -e "${BLUE}[2/7] Membersihkan berkas migrasi sisa & orphan lama...${NC}"
rm -f database/migrations/2026_07_* 2>/dev/null || true
git clean -f database/migrations/ 2>/dev/null || true
rm -rf app/Providers/Filament 2>/dev/null || true
rm -f bootstrap/cache/*.php 2>/dev/null || true
rm -f storage/framework/views/*.php 2>/dev/null || true
rm -f config/octane.php config/sanctum.php 2>/dev/null || true

# 3. Tarik update terbaru dari GitHub
echo -e "${BLUE}[3/7] Mengambil update terbaru dari GitHub (origin main)...${NC}"
git fetch origin main
git reset --hard origin/main

# Re-clean after reset
rm -f database/migrations/2026_07_* 2>/dev/null || true
git clean -f database/migrations/ 2>/dev/null || true
rm -rf app/Providers/Filament 2>/dev/null || true

# 4. Pastikan direktori storage, database, & izin siap
echo -e "${BLUE}[4/7] Menyiapkan struktur direktori storage & database...${NC}"
mkdir -p storage/framework/{sessions,views,cache}
mkdir -p storage/app/public/signatures
mkdir -p storage/logs
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# 5. Jalankan Migrasi Skema Database
echo -e "${BLUE}[5/7] Menjalankan Migrasi Skema Database...${NC}"
php artisan migrate --force

# 6. Set Kepemilikan Web Server (www:www)
echo -e "${BLUE}[6/7] Mengatur izin hak akses web server (www:www)...${NC}"
chown -R www:www "$BASE_DIR" 2>/dev/null || true
chmod 664 asystem_interview database/database.sqlite 2>/dev/null || true

# 7. Bersihkan & Optimasi Cache Laravel
echo -e "${BLUE}[7/7] Membersihkan & Menyegarkan Cache Laravel...${NC}"
php artisan optimize:clear

echo -e "\n${GREEN}================================================================${NC}"
echo -e "${GREEN}  🎉 DEPLOYMENT SELESAI & BERHASIL DITERAPKAN KE SERVER!        ${NC}"
echo -e "${GREEN}================================================================${NC}\n"
