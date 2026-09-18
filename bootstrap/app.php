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
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
            'candidate.auth' => \App\Http\Middleware\EnsureCandidateAuthenticated::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
