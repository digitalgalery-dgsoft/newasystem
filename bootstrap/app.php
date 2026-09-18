<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Auto-clean orphan / leftover config files that cause fatal errors when optional packages are not installed
$knownProblematicConfigs = [
    'octane.php' => 'Laravel\Octane\Octane',
    'sanctum.php' => 'Laravel\Sanctum\Sanctum',
    'telescope.php' => 'Laravel\Telescope\Telescope',
    'horizon.php' => 'Laravel\Horizon\Horizon',
    'pennant.php' => 'Laravel\Pennant\Feature',
    'reverb.php' => 'Laravel\Reverb\Application',
];

foreach ($knownProblematicConfigs as $cfgFile => $className) {
    $targetPath = dirname(__DIR__) . '/config/' . $cfgFile;
    if (file_exists($targetPath) && !class_exists($className)) {
        @unlink($targetPath);
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
