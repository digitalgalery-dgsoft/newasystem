<?php
@ini_set('display_errors', 1);
@error_reporting(E_ALL);

$token = $_GET['token'] ?? ($argv[1] ?? '');
if (php_sapi_name() !== 'cli' && $token !== 'dgsoft_rahasia_123') {
    http_response_code(403);
    die("Access denied. Gunakan ?token=dgsoft_rahasia_123");
}

echo "<pre style='font-family: monospace; background: #0f172a; color: #f8fafc; padding: 24px; border-radius: 12px; line-height: 1.6;'>\n";
echo "=== ASYSTEM SERVER CACHE & BINDING FIXER ===\n";
echo "Waktu: " . date('Y-m-d H:i:s') . "\n\n";

$baseDir = dirname(__DIR__);
$cacheDir = $baseDir . '/bootstrap/cache';

echo "[1] Membersihkan berkas cache di {$cacheDir}...\n";
if (is_dir($cacheDir)) {
    $files = glob($cacheDir . '/*.php');
    foreach ($files as $file) {
        $name = basename($file);
        if ($name === '.gitignore') continue;
        if (@unlink($file)) {
            echo "  ✓ Berhasil menghapus: {$name}\n";
        } else {
            echo "  ✗ Gagal menghapus: {$name}\n";
        }
    }
}

if (file_exists($baseDir . '/config/octane.php')) {
    if (@unlink($baseDir . '/config/octane.php')) {
        echo "  ✓ Berhasil menghapus file konflik: config/octane.php\n";
    }
}

echo "\n[2] Membersihkan view cache di storage/framework/views...\n";
$viewCacheDir = $baseDir . '/storage/framework/views';
if (is_dir($viewCacheDir)) {
    $viewFiles = glob($viewCacheDir . '/*.php');
    $count = 0;
    foreach ($viewFiles as $vf) {
        if (@unlink($vf)) $count++;
    }
    echo "  ✓ Berhasil membersihkan {$count} file view cache.\n";
}

echo "\n[3] Memastikan permission storage dan bootstrap/cache...\n";
@chmod($baseDir . '/storage', 0777);
@chmod($baseDir . '/bootstrap/cache', 0777);
echo "  ✓ Permission diperbarui.\n";

echo "\n[4] Menguji bootstrap Laravel dan resolusi 'view'...\n";
try {
    require $baseDir . '/vendor/autoload.php';
    $app = require $baseDir . '/bootstrap/app.php';
    
    // Test container make
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    
    $isViewBound = $app->bound('view');
    echo "  Status binding 'view': " . ($isViewBound ? "SUKSES (Bound)" : "GAGAL (Not Bound)") . "\n";
    
    if ($isViewBound) {
        $viewFactory = $app->make('view');
        echo "  Class View Factory: " . get_class($viewFactory) . "\n";
        echo "\n\033[1;32m🎉 SEMUA CACHE BERHASIL DIBERSIHKAN DAN 'VIEW' SUDAH AKTIF NORMAL!\033[0m\n";
    }
} catch (\Throwable $e) {
    echo "\n\033[1;31m[ERROR PADA BOOTSTRAP]\033[0m: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " (Line " . $e->getLine() . ")\n";
}

echo "\n============================================\n";
echo "Silakan refresh halaman web utama: <a href='/kandidatportal' style='color: #38bdf8;'>/kandidatportal</a>\n";
echo "</pre>";
