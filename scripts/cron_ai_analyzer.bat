@echo off
REM scripts/cron_ai_analyzer.bat
REM Menjalankan cron AI CV analyzer di Windows Command Prompt / Task Scheduler
cd /d %~dp0\..
php artisan ai:cron-analyzer --limit=3 >> storage\logs\cron_ai.log 2>&1
