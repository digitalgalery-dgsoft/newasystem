<?php
// ==============================================================================
// 🚀 ASYSTEM PORTAL - PRODUCTION AUTO-DEPLOYMENT WEB CONSOLE
// URL: https://new.asystem.co.id/deploy.php?token=dgsoft_rahasia_123
// ==============================================================================

@ini_set('display_errors', 1);
@error_reporting(E_ALL);
@set_time_limit(300);
@ini_set('memory_limit', '256M');

$secretToken = getenv('DEPLOY_SECRET_TOKEN') ?: "dgsoft_rahasia_123";
$inputToken = (string)($_GET['token'] ?? $_POST['token'] ?? '');

if (empty($inputToken) || !hash_equals($secretToken, $inputToken)) {
    http_response_code(403);
    die("Akses Ditolak: Token tidak valid.");
}

$baseDir = dirname(__DIR__);

// Jika request adalah POST, jalankan proses deploy (streaming output)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    
    header('Content-Type: application/octet-stream');
    header('Cache-Control: no-cache');
    header('X-Accel-Buffering: no');
    
    echo "<!--" . str_repeat(' ', 4096) . "-->\n";
    @flush();

    $commands = [
        "git config --global --add safe.directory {$baseDir}",
        "cd {$baseDir} && git fetch origin main",
        "cd {$baseDir} && git reset --hard origin/main",
        "cd {$baseDir} && php artisan migrate --force",
        "cd {$baseDir} && php artisan optimize:clear",
        "chmod -R 777 {$baseDir}/storage {$baseDir}/bootstrap/cache",
    ];

    echo "<span style=\"color: #00bcd4; font-weight: bold;\">=== MEMULAI PROSES DEPLOYMENT ASYSTEM SERVER 3 (ATK) ===</span>\n";
    echo "Direktori: {$baseDir}\n";
    echo "Waktu    : " . date('Y-m-d H:i:s') . "\n\n";

    foreach ($commands as $cmd) {
        echo "<span style=\"color: #fbc02d; font-weight: bold;\">▶ Eksekusi: {$cmd}</span>\n";
        @flush();

        $process = @proc_open($cmd . " 2>&1", [
            1 => ["pipe", "w"]
        ], $pipes);

        if (is_resource($process)) {
            while (!feof($pipes[1])) {
                $line = fgets($pipes[1]);
                if ($line !== false) {
                    echo htmlspecialchars($line);
                    @flush();
                }
            }
            fclose($pipes[1]);
            $returnCode = proc_close($process);

            if ($returnCode !== 0) {
                echo "<span style=\"color: #ff5252; font-weight: bold;\">❌ Gagal dengan kode exit {$returnCode}</span>\n\n";
            } else {
                echo "<span style=\"color: #4caf50;\">✓ Sukses</span>\n\n";
            }
        }
    }

    echo "<span style=\"color: #4caf50; font-weight: bold;\">🎉 DEPLOYMENT SELESAI DENGAN SUKSES!</span>\n";
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ASystem Auto-Deploy Console (Server 3 ATK)</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen p-4 sm:p-8 flex items-center justify-center">
    <div class="w-full max-w-3xl bg-slate-800/90 border border-slate-700 rounded-2xl shadow-2xl overflow-hidden backdrop-blur-md">
        <!-- HEADER -->
        <div class="bg-gradient-to-r from-blue-700 to-indigo-700 p-6 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center text-2xl border border-white/20">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                </div>
                <div>
                    <h1 class="text-xl font-black">ASystem Production Deployer</h1>
                    <p class="text-xs text-blue-200">Server 3: PT Anugrah Talenta Berkarya (ATK) &bull; 38.103.170.224</p>
                </div>
            </div>
            <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-xs font-bold">
                Online
            </span>
        </div>

        <!-- BODY -->
        <div class="p-6 space-y-5">
            <div class="p-4 rounded-xl bg-slate-900/60 border border-slate-700 text-xs space-y-2">
                <div class="flex justify-between">
                    <span class="text-slate-400">Target Path:</span>
                    <span class="font-mono text-indigo-300"><?= htmlspecialchars($baseDir) ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">GitHub Repo:</span>
                    <span class="font-mono text-emerald-300">digitalgalery-dgsoft/newasystem (main)</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Database:</span>
                    <span class="font-mono text-amber-300">SQLite (asystem_interview)</span>
                </div>
            </div>

            <form id="deployForm" onsubmit="runDeploy(event)" class="flex justify-center">
                <button type="submit" id="btnDeploy" class="px-8 py-3.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-black text-sm shadow-lg shadow-indigo-600/30 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-rocket"></i>
                    <span>Tarik Update Terbaru (Git Pull &amp; Migrate)</span>
                </button>
            </form>

            <!-- TERMINAL STREAMING CONSOLE -->
            <div id="consoleWrapper" class="hidden space-y-2">
                <div class="flex items-center justify-between text-xs text-slate-400">
                    <span class="font-bold flex items-center gap-2">
                        <i class="fa-solid fa-terminal"></i> Terminal Output
                    </span>
                    <span id="deployStatus" class="font-mono text-amber-400">Memproses...</span>
                </div>
                <pre id="outputTerminal" class="p-4 rounded-xl bg-black/90 text-emerald-400 font-mono text-xs overflow-x-auto max-h-96 whitespace-pre-wrap leading-relaxed border border-slate-700"></pre>
            </div>
        </div>
    </div>

    <script>
    async function runDeploy(e) {
        e.preventDefault();
        const btn = document.getElementById('btnDeploy');
        const wrapper = document.getElementById('consoleWrapper');
        const terminal = document.getElementById('outputTerminal');
        const status = document.getElementById('deployStatus');

        btn.disabled = true;
        btn.classList.add('opacity-50', 'cursor-not-allowed');
        wrapper.classList.remove('hidden');
        terminal.textContent = '';
        status.textContent = 'Menghubungkan ke GitHub...';

        try {
            const response = await fetch(window.location.href, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            const reader = response.body.getReader();
            const decoder = new TextDecoder('utf-8');

            while (true) {
                const { done, value } = await reader.read();
                if (done) break;
                const chunk = decoder.decode(value, { stream: true });
                terminal.textContent += chunk;
                terminal.scrollTop = terminal.scrollHeight;
            }

            status.textContent = 'Selesai!';
            status.className = 'font-mono text-emerald-400 font-bold';
        } catch (err) {
            terminal.textContent += '\n\nError: ' + err.message;
            status.textContent = 'Gagal';
            status.className = 'font-mono text-rose-400 font-bold';
        } finally {
            btn.disabled = false;
            btn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    }
    </script>
</body>
</html>
