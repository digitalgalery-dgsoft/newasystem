<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Failed;
use App\Services\ActivityLogger;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Global helper function jika belum ada
        if (!function_exists('activity_log')) {
            require_once app_path('helpers.php');
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. Catat Sesi Login Otomatis
        Event::listen(Login::class, function (Login $event) {
            ActivityLogger::auth('LOGIN', "Pengguna {$event->user->name} berhasil masuk ke sistem.", $event->user);
        });

        // 2. Catat Sesi Logout Otomatis
        Event::listen(Logout::class, function (Logout $event) {
            if ($event->user) {
                ActivityLogger::auth('LOGOUT', "Pengguna {$event->user->name} keluar dari sistem.", $event->user);
            }
        });

        // 3. Catat Percobaan Login Gagal
        Event::listen(Failed::class, function (Failed $event) {
            $email = $event->credentials['email'] ?? ($event->credentials['username'] ?? 'unknown');
            ActivityLogger::log('LOGIN_FAILED', 'Auth & Akun', "Percobaan login gagal untuk akun: {$email}", null, [
                'attempted_email' => $email,
            ], $event->user);
        });
    }
}
