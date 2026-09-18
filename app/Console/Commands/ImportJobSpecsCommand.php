<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportJobSpecsCommand extends Command
{
    protected $signature = 'job:import-specs {--file= : Path to tb_job_specs.sql}';
    protected $description = 'Import real job specs from MySQL dump into job_specs table';

    public function handle()
    {
        $filePath = $this->option('file') ?: 'D:/Documents/Downloads/tb_job_specs.sql';

        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        $this->info("Reading SQL dump from {$filePath}...");
        $sql = file_get_contents($filePath);

        // Replace MySQL escaped quotes with SQLite compatible single quotes
        $converted = str_replace(["\\'", "\\\""], ["''", '"'], $sql);
        // Replace MySQL escaped newlines with actual newlines
        $converted = str_replace(["\\r\\n", "\\r", "\\n"], ["\n", "\n", "\n"], $converted);

        $this->info("Clearing existing dummy data from job_specs table...");
        DB::statement('DELETE FROM job_specs');

        $lines = explode("\n", $converted);
        $stmtBuffer = '';
        $insertCount = 0;
        $rowCount = 0;

        DB::beginTransaction();
        try {
            foreach ($lines as $line) {
                $trimmed = trim($line);
                if (str_starts_with($trimmed, 'INSERT INTO') || $stmtBuffer !== '') {
                    $stmtBuffer .= $line . "\n";
                    if (str_ends_with($trimmed, ';')) {
                        // Rename table name to job_specs
                        $stmtBuffer = str_replace('`tb_job_specs`', '`job_specs`', $stmtBuffer);
                        $stmtBuffer = str_replace('INSERT INTO', 'INSERT OR REPLACE INTO', $stmtBuffer);
                        
                        DB::statement($stmtBuffer);
                        $insertCount++;
                        $stmtBuffer = '';
                    }
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error("Import failed: " . $e->getMessage());
            return 1;
        }

        $totalCount = DB::table('job_specs')->count();
        $this->info("Successfully imported {$totalCount} job specs across {$insertCount} insert blocks!");

        return 0;
    }
}
