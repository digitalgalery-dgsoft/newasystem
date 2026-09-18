#!/usr/bin/env bash
# ==============================================================================
# 🚀 ASYSTEM SUPPORT SYSTEM - CRON SINKRONISASI ODOO OTOMATIS
# Replikasi pola cron otomatis Attendance (att-admin-v12)
# ==============================================================================
# Contoh konfigurasi crontab server (crontab -e):
# */30 * * * * /bin/bash /path/to/project/scripts/cron_odoo_sync.sh >> /path/to/project/storage/logs/cron_odoo.log 2>&1
# ==============================================================================

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_DIR="$(dirname "$SCRIPT_DIR")"

cd "$PROJECT_DIR"

echo "================================================================"
echo "Memulai Cron Odoo Sync pada: $(date '+%Y-%m-%d %H:%M:%S')"
echo "================================================================"

php artisan odoo:sync --trigger=cron --silent

echo "Selesai pada: $(date '+%Y-%m-%d %H:%M:%S')"
echo "================================================================"
