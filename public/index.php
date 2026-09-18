<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Auto-clean orphan / leftover config files that cause fatal errors when optional packages are not installed
$orphanFiles = ['octane.php', 'sanctum.php', 'telescope.php', 'horizon.php', 'pennant.php', 'reverb.php'];
foreach ($orphanFiles as $cfgFile) {
    $targetPath = __DIR__ . '/../config/' . $cfgFile;
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

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
