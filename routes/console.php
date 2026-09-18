<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Automated Odoo Synchronization (runs every 30 minutes, replicating att-admin-v12)
Schedule::command('odoo:sync --trigger=cron')
    ->everyThirtyMinutes()
    ->withoutOverlapping()
    ->runInBackground();

