<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportWpDumpCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wp:import-dump {file?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import tasks, subtasks, comments, activities, notifications, and tb_workplan from seed or sql dump';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $seedGz = database_path('data/workplan_seed.sql.gz');
        $seedSql = database_path('data/workplan_seed.sql');
        $legacySql = 'D:\Documents\Downloads\db wp tdp\db_wp.sql';

        $filePath = $this->argument('file');
        if (empty($filePath)) {
            if (file_exists($seedGz)) {
                $filePath = $seedGz;
            } elseif (file_exists($seedSql)) {
                $filePath = $seedSql;
            } elseif (file_exists($legacySql)) {
                $filePath = $legacySql;
            } else {
                $filePath = $seedGz;
            }
        }

        if (!file_exists($filePath)) {
            $this->error("Berkas dump/seed tidak ditemukan di: {$filePath}");
            return 1;
        }

        $isGz = str_ends_with(strtolower($filePath), '.gz');

        $this->info("==============================================================================");
        $this->info("🚀 MEMULAI IMPOR DATA HISTORIS WORK PLAN & TODOLIST");
        $this->info("==============================================================================");
        $this->info("Path file: {$filePath} (" . ($isGz ? 'GZIP Compressed' : 'Raw SQL') . ")");
        $this->info("Ukuran: " . round(filesize($filePath) / 1024 / 1024, 2) . " MB");

        $f = $isGz ? gzopen($filePath, 'r') : fopen($filePath, 'r');
        if (!$f) {
            $this->error("Gagal membuka berkas SQL.");
            return 1;
        }

        $targetTables = [
            'tasks',
            'task_subtasks',
            'task_comments',
            'task_activities',
            'task_notifications',
            'categories',
            'task_categories',
            'tb_workplan',
        ];

        $this->warn("Mengosongkan tabel target...");
        DB::statement('PRAGMA foreign_keys = OFF;');
        foreach (['tasks', 'task_subtasks', 'task_comments', 'task_activities', 'task_notifications', 'task_categories', 'tb_workplan'] as $tbl) {
            DB::table($tbl)->truncate();
        }

        $currentTable = null;
        $buffer = '';

        DB::beginTransaction();

        $batchCount = 0;
        $errorCount = 0;
        $startTime = microtime(true);

        $getLine = function () use ($f, $isGz) {
            return $isGz ? gzgets($f) : fgets($f);
        };

        while (($line = $getLine()) !== false) {
            if (preg_match('/^INSERT INTO `([^`]+)`/', $line, $m)) {
                $tbl = $m[1];
                if (in_array($tbl, $targetTables)) {
                    $currentTable = $tbl;
                    $buffer = $line;
                } else {
                    $currentTable = null;
                    $buffer = '';
                }
            } elseif ($currentTable) {
                $buffer .= $line;
            }

            if ($currentTable && str_ends_with(trim($line), ';')) {
                $stmt = $buffer;
                if ($currentTable === 'categories') {
                    $stmt = str_replace('INSERT INTO `categories`', 'INSERT INTO `task_categories`', $stmt);
                }

                // Konversi MySQL escaping ke SQLite compatibility
                $stmt = str_replace("\\'", "''", $stmt);
                $stmt = str_replace('\\"', '"', $stmt);

                try {
                    DB::unprepared($stmt);
                    $batchCount++;
                    if ($batchCount % 100 === 0) {
                        DB::commit();
                        DB::beginTransaction();
                    }
                } catch (\Throwable $e) {
                    $errorCount++;
                    if ($errorCount <= 5) {
                        $this->error("Error importing {$currentTable}: " . substr($e->getMessage(), 0, 150));
                    }
                }

                $buffer = '';
                $currentTable = null;
            }
        }

        DB::commit();
        if ($isGz) {
            gzclose($f);
        } else {
            fclose($f);
        }
        DB::statement('PRAGMA foreign_keys = ON;');

        $duration = round(microtime(true) - $startTime, 2);

        $this->newLine();
        $this->info("==============================================================================");
        $this->info("🎉 IMPOR SELESAI DALAM {$duration} DETIK! (Total Statements: {$batchCount}, Errors: {$errorCount})");
        $this->info("==============================================================================");
        $this->table(
            ['Tabel', 'Jumlah Record di Database'],
            [
                ['tasks', DB::table('tasks')->count()],
                ['task_subtasks', DB::table('task_subtasks')->count()],
                ['task_comments', DB::table('task_comments')->count()],
                ['task_activities', DB::table('task_activities')->count()],
                ['task_notifications', DB::table('task_notifications')->count()],
                ['task_categories', DB::table('task_categories')->count()],
                ['tb_workplan', DB::table('tb_workplan')->count()],
            ]
        );

        return 0;
    }
}
