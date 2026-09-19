<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Align timestamps of candidates created during UTC application configuration to Asia/Jakarta (WIB).
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            DB::statement("
                UPDATE candidates 
                SET created_at = datetime(created_at, '+7 hours'),
                    updated_at = datetime(updated_at, '+7 hours')
                WHERE id >= 64765 AND created_at < '2026-09-19 21:00:00'
            ");

            DB::statement("
                UPDATE work_experiences 
                SET created_at = datetime(created_at, '+7 hours'),
                    updated_at = datetime(updated_at, '+7 hours')
                WHERE candidate_id >= 64765 AND created_at < '2026-09-19 21:00:00'
            ");
        } elseif ($driver === 'mysql') {
            DB::statement("
                UPDATE candidates 
                SET created_at = DATE_ADD(created_at, INTERVAL 7 HOUR),
                    updated_at = DATE_ADD(updated_at, INTERVAL 7 HOUR)
                WHERE id >= 64765 AND created_at < '2026-09-19 21:00:00'
            ");

            DB::statement("
                UPDATE work_experiences 
                SET created_at = DATE_ADD(created_at, INTERVAL 7 HOUR),
                    updated_at = DATE_ADD(updated_at, INTERVAL 7 HOUR)
                WHERE candidate_id >= 64765 AND created_at < '2026-09-19 21:00:00'
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            DB::statement("
                UPDATE candidates 
                SET created_at = datetime(created_at, '-7 hours'),
                    updated_at = datetime(updated_at, '-7 hours')
                WHERE id >= 64765
            ");

            DB::statement("
                UPDATE work_experiences 
                SET created_at = datetime(created_at, '-7 hours'),
                    updated_at = datetime(updated_at, '-7 hours')
                WHERE candidate_id >= 64765
            ");
        } elseif ($driver === 'mysql') {
            DB::statement("
                UPDATE candidates 
                SET created_at = DATE_SUB(created_at, INTERVAL 7 HOUR),
                    updated_at = DATE_SUB(updated_at, INTERVAL 7 HOUR)
                WHERE id >= 64765
            ");

            DB::statement("
                UPDATE work_experiences 
                SET created_at = DATE_SUB(created_at, INTERVAL 7 HOUR),
                    updated_at = DATE_SUB(updated_at, INTERVAL 7 HOUR)
                WHERE candidate_id >= 64765
            ");
        }
    }
};
