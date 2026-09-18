#!/usr/bin/env bash
# ==============================================================================
# 🚀 ASYSTEM SUPPORT SYSTEM - CRON SINKRONISASI EMPLOYEE AKTIF (HOURLY)
# Berjalan setiap jam (0 * * * *)
# Aturan: Hanya ambil employee yang aktif di Odoo. NIK yang sudah ada TIDAK diupdate.
# ==============================================================================
# Contoh konfigurasi crontab server (crontab -e):
# 0 * * * * /bin/bash /path/to/project/scripts/cron_odoo_active_hourly.sh >> /path/to/project/storage/logs/cron_odoo_active.log 2>&1
# ==============================================================================

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_DIR="$(dirname "$SCRIPT_DIR")"

cd "$PROJECT_DIR"

echo "================================================================"
echo "Memulai Cron Odoo Sync Active (Hourly) pada: $(date '+%Y-%m-%d %H:%M:%S')"
echo "================================================================"

php artisan odoo:sync-active --silent

echo "Selesai pada: $(date '+%Y-%m-%d %H:%M:%S')"
echo "================================================================"
