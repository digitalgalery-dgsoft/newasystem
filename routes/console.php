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

