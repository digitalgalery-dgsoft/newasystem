<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Auto-clean orphan / leftover config files that cause fatal errors when optional packages are not installed
$orphanFiles = ['octane.php', 'sanctum.php', 'telescope.php', 'horizon.php', 'pennant.php', 'reverb.php'];
foreach ($orphanFiles as $cfgFile) {
    $targetPath = dirname(__DIR__) . '/config/' . $cfgFile;
    if (file_exists($targetPath)) {
        $content = @file_get_contents($targetPath);
        if ($content && (
            (str_contains($content, 'Octane::') && !class_exists('Laravel\Octane\Octane')) ||
            (str_contains($content, 'Sanctum::') && !class_exists('Laravel\Sanctum\Sanctum')) ||
            (str_contains($content, 'Telescope::') && !class_exists('Laravel\Telescope\Telescope')) ||
            (str_contains($content, 'Horizon::') && !class_exists('Laravel\Horizon\Horizon'))
        )) {
            @unlink($targetPath);
        }
    }
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withCommands([
        __DIR__.'/../app/Console/Commands',
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
            'candidate.auth' => \App\Http\Middleware\EnsureCandidateAuthenticated::class,
        ]);
        $middleware->append(\App\Http\Middleware\PreventIndexingMiddleware::class);
        $middleware->redirectGuestsTo(fn () => route('login'));
        $middleware->trustProxies(at: '*');
        $middleware->validateCsrfTokens(except: [
            'odoo-setting/sync-by-nik',
            'sync-by-nik',
            'master/karyawan/sync-by-nik',
            'interview/odoo/lookup-nik',
            'interview/odoo/import-nik',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Tangani kedaluwarsa sesi token CSRF (Status 419 & TokenMismatchException)
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, \Illuminate\Http\Request $request) {
            if ($e->getStatusCode() !== 419) {
                return null;
            }

            if ($request->expectsJson() || $request->isJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Sesi formulir atau token keamanan telah kedaluwarsa. Silakan muat ulang halaman.',
                ], 419);
            }

            // Jika error terjadi pada form login karyawan/admin
            if ($request->is('login') || $request->is('login/*')) {
                return redirect()->route('login')
                    ->withInput($request->except('password', '_token'))
                    ->with('warning', 'Sesi login telah diperbarui karena pembaruan sistem atau lama tidak aktif. Silakan masukkan kata sandi kembali.');
            }

            // Jika error terjadi pada form login CBT kandidat
            if ($request->is('cbt/login') || $request->is('cbt/*')) {
                return redirect()->route('cbt.login')
                    ->withInput($request->except('password', '_token'))
                    ->with('warning', 'Sesi portal CBT telah diperbarui. Silakan login kembali.');
            }

            // Untuk form lainnya di dalam sistem
            return redirect()->back(fallback: route('login'))
                ->withInput($request->except('password', '_token'))
                ->with('warning', 'Sesi halaman telah kedaluwarsa. Silakan muat ulang dan kirim kembali.');
        });

        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson() || $request->isJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Sesi formulir atau token keamanan telah kedaluwarsa. Silakan muat ulang halaman.',
                ], 419);
            }

            if ($request->is('login') || $request->is('login/*')) {
                return redirect()->route('login')
                    ->withInput($request->except('password', '_token'))
                    ->with('warning', 'Sesi login telah diperbarui karena pembaruan sistem atau lama tidak aktif. Silakan masukkan kata sandi kembali.');
            }

            if ($request->is('cbt/login') || $request->is('cbt/*')) {
                return redirect()->route('cbt.login')
                    ->withInput($request->except('password', '_token'))
                    ->with('warning', 'Sesi portal CBT telah diperbarui. Silakan login kembali.');
            }

            return redirect()->back(fallback: route('login'))
                ->withInput($request->except('password', '_token'))
                ->with('warning', 'Sesi halaman telah kedaluwarsa. Silakan muat ulang dan kirim kembali.');
        });
    })->create();
