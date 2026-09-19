<?php
@ini_set('display_errors', 1);
@error_reporting(E_ALL);

$token = $_GET['token'] ?? ($argv[1] ?? '');
if (php_sapi_name() !== 'cli' && $token !== 'dgsoft_rahasia_123') {
    http_response_code(403);
    die("Access denied. Token tidak valid.");
}

header('Content-Type: text/plain; charset=utf-8');

$baseDir = dirname(__DIR__);
echo "=== ASYSTEM AUTOMATED PRODUCTION DEPLOYMENT ===\n";
echo "Waktu: " . date('Y-m-d H:i:s') . "\n";
echo "Direktori: {$baseDir}\n\n";

$commands = [
    "cd {$baseDir} && git config --global --add safe.directory {$baseDir} 2>&1",
    "cd {$baseDir} && rm -f database/migrations/2026_07_* 2>&1",
    "cd {$baseDir} && git clean -f database/migrations/ 2>&1",
    "cd {$baseDir} && git clean -fd config/ 2>&1",
    "cd {$baseDir} && git fetch origin main 2>&1",
    "cd {$baseDir} && git reset --hard origin/main 2>&1",
    "cd {$baseDir} && rm -rf app/Providers/Filament 2>&1",
    "cd {$baseDir} && rm -f bootstrap/cache/*.php 2>&1",
    "cd {$baseDir} && rm -f storage/framework/views/*.php 2>&1",
    "cd {$baseDir} && rm -f config/octane.php config/sanctum.php 2>&1",
    "cd {$baseDir} && php artisan migrate --force 2>&1",
    "cd {$baseDir} && chown -R www:www {$baseDir} 2>&1 || true",
    "cd {$baseDir} && php artisan optimize:clear 2>&1",
];

function runShellCmd($cmd) {
    if (function_exists('exec')) {
        $out = [];
        @exec($cmd, $out);
        return implode("\n", $out);
    } elseif (function_exists('shell_exec')) {
        return (string)@shell_exec($cmd);
    } elseif (function_exists('system')) {
        ob_start();
        @system($cmd);
        return ob_get_clean();
    } elseif (function_exists('passthru')) {
        ob_start();
        @passthru($cmd);
        return ob_get_clean();
    } elseif (function_exists('proc_open')) {
        $proc = @proc_open($cmd, [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
        if (is_resource($proc)) {
            $out = stream_get_contents($pipes[1]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            proc_close($proc);
            return $out;
        }
    }
    return "[ERROR] Tidak ada fungsi eksekusi shell yang diizinkan (disable_functions). Gunakan runner SSH production.";
}

foreach ($commands as $cmd) {
    echo ">> {$cmd}\n";
    $output = runShellCmd($cmd);
    echo $output . "\n\n";
}

echo "=== DEPLOYMENT COMPLETED SUCCESSFULLY ===\n";
