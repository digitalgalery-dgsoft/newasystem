<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ActivityLog;

class AuditTraceGuestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'audit:trace-guest {--limit=50 : Jumlah baris log yang ditampilkan}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Melacak audit trail & riwayat akses detail kandidat yang dilakukan tanpa login (Guest)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = (int) $this->option('limit') ?: 50;

        $this->info("================================================================================");
        $this->info("🔍 AUDIT TRAIL TRACING: AKSES DATA KANDIDAT TANPA LOGIN (GUEST)");
        $this->info("Waktu Pemeriksaan: " . date('Y-m-d H:i:s'));
        $this->info("================================================================================\n");

        // -----------------------------------------------------------------------------
        // BAGIAN 1: PEMERIKSAAN DARI TABEL ACTIVITY_LOGS (DATABASE APLIKASI)
        // -----------------------------------------------------------------------------
        $this->comment("--- [1] Riwayat dari Tabel Database (activity_logs) ---");

        try {
            $guestCandidateLogs = ActivityLog::where(function ($q) {
                $q->where('module', 'like', '%Kandidat%')
                  ->orWhere('module', 'like', '%Interview%')
                  ->orWhere('description', 'like', '%kandidat%');
            })->where(function ($q) {
                $q->whereNull('user_id')
                  ->orWhere('user_name', 'like', '%Guest%')
                  ->orWhere('user_name', 'like', '%Sistem%');
            })->orderBy('id', 'desc')->get();

            $totalGuestLogs = $guestCandidateLogs->count();
            $this->line("Total aktivitas kandidat oleh Guest/Sistem di database: <fg=yellow>{$totalGuestLogs}</> rekaman.");

            if ($totalGuestLogs > 0) {
                $headers = ['Waktu (WIB)', 'Aksi', 'Modul', 'User/Nama', 'IP Address', 'Deskripsi Ringkas'];
                $rows = [];
                foreach ($guestCandidateLogs->take($limit) as $log) {
                    $rows[] = [
                        $log->created_at ? $log->created_at->format('Y-m-d H:i:s') : '-',
                        $log->action,
                        $log->module,
                        $log->user_name ?: 'Guest / Sistem',
                        $log->ip_address ?: '-',
                        \Illuminate\Support\Str::limit($log->description, 55),
                    ];
                }
                $this->table($headers, $rows);
            } else {
                $this->info("✔ Tidak ditemukan aktivitas modifikasi/transaksi kandidat oleh Guest di database.");
            }
        } catch (\Throwable $e) {
            $this->error("Gagal memeriksa tabel activity_logs: " . $e->getMessage());
        }

        $this->newLine();

        // -----------------------------------------------------------------------------
        // BAGIAN 2: PEMERIKSAAN WEB SERVER ACCESS LOGS (NGINX / APACHE)
        // -----------------------------------------------------------------------------
        $this->comment("--- [2] Riwayat Akses HTTP Web Server (Nginx / Apache Logs) ---");

        $possibleLogFiles = [
            '/www/wwwlogs/new.asystem.co.id.log',
            '/www/wwwlogs/asystem.co.id.log',
            '/www/wwwlogs/access.log',
            '/var/log/nginx/access.log',
            '/var/log/nginx/new.asystem.co.id.access.log',
            '/var/log/apache2/access.log',
            '/var/log/httpd/access_log',
        ];

        $foundLogs = [];
        foreach ($possibleLogFiles as $file) {
            if (file_exists($file) && is_readable($file)) {
                $foundLogs[] = $file;
            }
        }

        if (empty($foundLogs) && is_dir('/www/wwwlogs')) {
            $files = glob('/www/wwwlogs/*asystem*.log');
            if (!empty($files)) {
                $foundLogs = array_merge($foundLogs, $files);
            }
        }

        if (empty($foundLogs)) {
            $this->warn("⚠️ File log web server tidak dapat diakses atau berada di direktori dengan izin khusus root.");
        } else {
            $this->info("Menemukan file log web server:");
            foreach ($foundLogs as $lf) {
                $size = round(filesize($lf) / 1024 / 1024, 2);
                $this->line("  ↳ <fg=cyan>{$lf}</> ({$size} MB)");
            }

            // Pattern akses detail kandidat
            $patterns = [
                'kandidatportal/[0-9]+',
                'interview/[0-9]+',
                'interviewinhouse/[0-9]+',
                'hasilinhouse\.php\?id=',
                'printall\?id=',
            ];

            foreach ($foundLogs as $lf) {
                $this->newLine();
                $this->line("Memeriksa log: <fg=cyan>{$lf}</>");

                $matchedLines = [];
                if (function_exists('exec')) {
                    $cmd = "grep -E 'GET /(" . implode('|', $patterns) . ")' " . escapeshellarg($lf) . " | tail -n " . $limit;
                    @exec($cmd, $matchedLines);
                }

                if (empty($matchedLines) && file_exists($lf)) {
                    $lines = @file($lf, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                    if ($lines) {
                        $reversed = array_reverse($lines);
                        foreach ($reversed as $line) {
                            foreach ($patterns as $pat) {
                                if (preg_match('#' . $pat . '#', $line)) {
                                    $matchedLines[] = $line;
                                    break;
                                }
                            }
                            if (count($matchedLines) >= $limit) break;
                        }
                    }
                }

                $totalMatched = count($matchedLines);
                $this->line("Total akses detail kandidat terdeteksi di log: <fg=yellow>{$totalMatched}</> riwayat.");

                if ($totalMatched > 0) {
                    $logRows = [];
                    foreach (array_slice($matchedLines, 0, 30) as $rawLine) {
                        if (preg_match('/^(\S+) \S+ \S+ \[(.*?)\] "(GET|POST) (\S+) \S+" (\d{3}) \S+ "(.*?)" "(.*?)"/', $rawLine, $m)) {
                            $ip = $m[1];
                            $waktu = $m[2];
                            $method = $m[3];
                            $endpoint = $m[4];
                            $status = $m[5];
                            $referer = $m[6] ?: '-';
                            $ua = \Illuminate\Support\Str::limit($m[7], 40);

                            $logRows[] = [$waktu, $ip, "$method $endpoint", $status, $referer, $ua];
                        } else {
                            $logRows[] = ['-', '-', \Illuminate\Support\Str::limit($rawLine, 80), '-', '-', '-'];
                        }
                    }
                    $this->table(['Waktu Server', 'IP Address', 'Request URL', 'Status HTTP', 'Referer', 'User Agent'], $logRows);
                } else {
                    $this->info("✔ Tidak ditemukan rekaman akses pada file log ini.");
                }
            }
        }

        $this->newLine();
        $this->info("================================================================================");
        $this->info("🏁 PEMERIKSAAN AUDIT SELESAI");
        $this->info("================================================================================");

        return 0;
    }
}
