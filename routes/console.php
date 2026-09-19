<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// 1. Cron Job: Sync Employee Aktif Baru Setiap Jam (Hanya karyawan aktif, NIK yang sudah ada TIDAK diupdate)
Schedule::command('odoo:sync-active --silent')
    ->hourly()
    ->withoutOverlapping()
    ->runInBackground();

// 2. Cron Job: Pengecekan Update Data & Resign Karyawan Setiap Tengah Malam (Pukul 00:00)
Schedule::command('odoo:sync-updates-resigns --silent')
    ->dailyAt('00:00')
    ->withoutOverlapping()
    ->runInBackground();

// 3. Cron Job: AI CV Analyzer Otomatis Setiap Menit (1 kandidat per 30 detik / 1 menit 2 kandidat)
Schedule::command('ai:cron-analyzer --limit=2 --interval=30')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();

// 4. Cron Job: Sync Tahapan Kandidat Portal dengan Odoo Recruitment & Auto-Archive > 14 hari
Schedule::command('odoo:sync-portal-stages --limit=500 --silent')
    ->hourly()
    ->withoutOverlapping()
    ->runInBackground();


