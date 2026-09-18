<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

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
    $targetPath = __DIR__ . '/../config/' . $cfgFile;
    if (file_exists($targetPath) && !class_exists($className)) {
        @unlink($targetPath);
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
