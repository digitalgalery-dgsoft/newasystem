<?php
/**
 * ==============================================================================
 * 🚀 ASYSTEM - PRODUCTION ONE-CLICK DEPLOYMENT RUNNER
 * ==============================================================================
 * Skrip ini memicu deployment otomatis ke server production live:
 * Server: Server 3 (38.103.170.224)
 * Target Directory: /www/wwwroot/new.asystem.co.id
 * URL Web: https://new.asystem.co.id
 *
 * Penggunaan:
 *   php scripts/deploy_production.php
 * ==============================================================================
 */

echo "==============================================================================\n";
echo "🚀 ASYSTEM PRODUCTION AUTO-DEPLOY (Server 3: new.asystem.co.id)\n";
echo "==============================================================================\n";
echo "Waktu Mulai: " . date('Y-m-d H:i:s') . "\n";

$cmd = "--version && cd /www/wwwroot/new.asystem.co.id && git fetch origin main && git reset --hard origin/main && php artisan migrate --force && php artisan wp:import-dump && php artisan optimize:clear && git log -1 --oneline";

$url = "https://appsend.my.id/deploy-production.php?token=dgsoft_rahasia_123&only_server=3&artisan_cmd=" . urlencode($cmd);

echo "Mengirim perintah deployment ke runner remote...\n";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 180);

$res = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($res === false || !empty($error)) {
    echo "❌ Terjadi kesalahan saat menghubungi server deployment:\n";
    echo $error . "\n";
    exit(1);
}

// Format output bersih dari tag HTML
$clean = strip_tags($res);
$clean = preg_replace('/[ \t]+/', ' ', $clean);
$clean = preg_replace('/\n\s*\n+/', "\n", $clean);

echo "\n--- HASIL EKSEKUSI SERVER ---\n";
echo trim($clean) . "\n";
echo "------------------------------\n";

if (str_contains($clean, 'DEPLOY_SUCCESS_FLAG') || str_contains($clean, 'Health Check Ping OK')) {
    echo "\n🎉 DEPLOYMENT BERHASIL! Aplikasi production new.asystem.co.id telah dimutakhirkan.\n";
    exit(0);
} else {
    echo "\n⚠️ Deployment selesai dengan catatan. Silakan periksa log di atas.\n";
    exit(0);
}
