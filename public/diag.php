<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$token = $_GET['token'] ?? '';
if ($token !== 'dgsoft_rahasia_123') {
    die("Access denied.");
}

echo "<pre style='font-family: monospace; background: #111; color: #eee; padding: 20px;'>\n";
echo "=== ASYSTEM SERVER 3 DIAGNOSTIC ===\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "Loaded extensions: " . implode(', ', get_loaded_extensions()) . "\n";

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

try {
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $request = Illuminate\Http\Request::create('/kandidatportal', 'GET');
    
    // We inspect the inner execution
    $app->instance('request', $request);
    Illuminate\Support\Facades\Request::clearResolvedInstance();
    
    echo "\nTesting application bootstrap...\n";
    $kernel->bootstrap();
    echo "Bootstrap completed successfully!\n";
    
    echo "Testing middleware and route execution...\n";
    $response = (new Illuminate\Routing\Pipeline($app))
        ->send($request)
        ->through([])
        ->then(function($req) use ($app) {
            return $app['router']->dispatch($req);
        });
        
    echo "Execution status: " . $response->getStatusCode() . "\n";
    echo "Response content preview: " . substr(strip_tags($response->getContent()), 0, 200) . "...\n";

} catch (\Throwable $e) {
    echo "\n\033[1;31m[ORIGINAL ROOT EXCEPTION CAUGHT!]\033[0m\n";
    echo "Type   : " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File   : " . $e->getFile() . "\n";
    echo "Line   : " . $e->getLine() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\nChecking recent storage/logs/laravel.log:\n";
$logFile = __DIR__ . '/../storage/logs/laravel.log';
if (file_exists($logFile)) {
    $lines = file($logFile);
    $lastLines = array_slice($lines, -50);
    echo implode("", $lastLines);
} else {
    echo "No laravel.log file found.\n";
}

echo "</pre>";
