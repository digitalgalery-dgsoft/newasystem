#!/usr/bin/env bash
# ==============================================================================
# 🌙 ASYSTEM SUPPORT SYSTEM - CRON PENGECEKAN UPDATE & RESIGN (MIDNIGHT)
# Berjalan setiap tengah malam (0 0 * * *)
# Aturan: Periksa data karyawan aktif lokal, perbarui perubahan data & tandai Resign jika di Odoo sudah non-aktif / keluar.
# ==============================================================================
# Contoh konfigurasi crontab server (crontab -e):
# 0 0 * * * /bin/bash /path/to/project/scripts/cron_odoo_updates_resigns_midnight.sh >> /path/to/project/storage/logs/cron_odoo_midnight.log 2>&1
# ==============================================================================

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_DIR="$(dirname "$SCRIPT_DIR")"

cd "$PROJECT_DIR"

echo "================================================================"
echo "Memulai Cron Odoo Sync Updates & Resigns (Midnight) pada: $(date '+%Y-%m-%d %H:%M:%S')"
echo "================================================================"

php artisan odoo:sync-updates-resigns --silent

echo "Selesai pada: $(date '+%Y-%m-%d %H:%M:%S')"
echo "================================================================"
