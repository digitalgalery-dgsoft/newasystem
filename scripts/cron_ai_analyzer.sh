#!/bin/bash
# scripts/cron_ai_analyzer.sh
# Menjalankan cron AI CV analyzer di Linux crontab
# * * * * * /bin/bash /path/to/scripts/cron_ai_analyzer.sh > /dev/null 2>&1

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )"
PROJECT_DIR="$SCRIPT_DIR/.."

cd "$PROJECT_DIR"
/usr/bin/php artisan ai:cron-analyzer --limit=3 >> storage/logs/cron_ai.log 2>&1
