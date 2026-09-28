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
                $this->line("Total akses detail kandidat terdeteksi di log (sample {$limit}): <fg=yellow>{$totalMatched}</> riwayat.");

                if ($totalMatched > 0) {
                    $directHits200 = [];
                    $blocked302 = [];
                    $otherHits = [];
                    $ipCounts = [];
                    $visitedCandidates = [];

                    foreach ($matchedLines as $rawLine) {
                        if (preg_match('/^(\S+) \S+ \S+ \[(.*?)\] "(GET|POST) (\S+) \S+" (\d{3}) \S+ "(.*?)" "(.*?)"/', $rawLine, $m)) {
                            $ip = $m[1];
                            $waktu = $m[2];
                            $method = $m[3];
                            $endpoint = $m[4];
                            $status = $m[5];
                            $referer = $m[6] ?: '-';
                            $ua = $m[7];

                            $ipCounts[$ip] = ($ipCounts[$ip] ?? 0) + 1;

                            // Ekstrak ID kandidat
                            if (preg_match('#/(?:kandidatportal|interview|interviewinhouse)/(\d+)#', $endpoint, $cid)) {
                                $visitedCandidates[$cid[1]] = ($visitedCandidates[$cid[1]] ?? 0) + 1;
                            }

                            $isInternalReferer = str_contains($referer, 'new.asystem.co.id') && (
                                str_contains($referer, '/kandidatportal') ||
                                str_contains($referer, '/interview') ||
                                str_contains($referer, '/fitur')
                            );

                            $entry = [
                                'waktu'    => $waktu,
                                'ip'       => $ip,
                                'url'      => "$method $endpoint",
                                'status'   => $status,
                                'referer'  => $referer,
                                'ua'       => \Illuminate\Support\Str::limit($ua, 45),
                                'is_bot'   => (bool) preg_match('/bot|crawl|spider|slurp|semrush|google|bing/i', $ua),
                            ];

                            if ($status == '200' && (!$isInternalReferer || $entry['is_bot'])) {
                                $directHits200[] = $entry;
                            } elseif ($status == '302') {
                                $blocked302[] = $entry;
                            } else {
                                $otherHits[] = $entry;
                            }
                        }
                    }

                    // 1. Tampilkan Akses Direct Hit / Guest yang Sempat Berhasil (HTTP 200)
                    $this->newLine();
                    $this->warn("⚠️ [A] Akses Langsung / Tanpa Referer Internal yang Terbuka (HTTP 200) [Sebelum Pengetatan]: " . count($directHits200) . " riwayat");
                    if (!empty($directHits200)) {
                        $rows = array_map(fn($e) => [$e['waktu'], $e['ip'], $e['url'], $e['referer'], $e['ua']], array_slice($directHits200, 0, 20));
                        $this->table(['Waktu Server', 'IP Address', 'URL Detail Kandidat', 'Referer', 'User Agent / Perangkat'], $rows);
                    } else {
                        $this->info("✔ Tidak ditemukan akses direct hit berstatus 200.");
                    }

                    // 2. Tampilkan Akses yang Berhasil Diblokir (HTTP 302 -> Redirect Login)
                    $this->newLine();
                    $this->info("🛡️ [B] Akses Tamu/Bot yang Berhasil DIBLOKIR & DIALIHKAN KE LOGIN (HTTP 302) [Setelah Pengetatan]: " . count($blocked302) . " riwayat");
                    if (!empty($blocked302)) {
                        $rows = array_map(fn($e) => [$e['waktu'], $e['ip'], $e['url'], $e['status'] . ' (Redirect Login)', $e['ua']], array_slice($blocked302, 0, 15));
                        $this->table(['Waktu Server', 'IP Address', 'URL Target', 'Status', 'User Agent / Perangkat'], $rows);
                    }

                    // 3. Ringkasan Top IP Pengakses
                    $this->newLine();
                    $this->line("📊 [C] Top IP Address Pengakses Detail Kandidat:");
                    arsort($ipCounts);
                    $ipRows = [];
                    foreach (array_slice($ipCounts, 0, 8, true) as $ip => $cnt) {
                        $ipRows[] = [$ip, $cnt . " kali request"];
                    }
                    $this->table(['IP Address', 'Frekuensi Akses'], $ipRows);

                    // 4. Ringkasan ID Kandidat yang Sering Diakses
                    if (!empty($visitedCandidates)) {
                        arsort($visitedCandidates);
                        $this->line("🎯 [D] Sample ID Kandidat yang Dituju:");
                        $candIds = array_keys(array_slice($visitedCandidates, 0, 10, true));
                        $this->line("  ↳ ID: " . implode(', ', $candIds));
                    }
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
