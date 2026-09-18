<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('candidates')) {
            if (!Schema::hasColumn('candidates', 'principle')) {
                Schema::table('candidates', function (Blueprint $table) {
                    $table->string('principle')->nullable()->after('principle_id');
                });
            }

            // Backfill principle string into candidates from principles table before principles is truncated
            if (Schema::hasTable('principles')) {
                DB::statement("
                    UPDATE candidates 
                    SET principle = (
                        SELECT name FROM principles WHERE principles.id = candidates.principle_id
                    )
                    WHERE candidates.principle_id IS NOT NULL 
                    AND (candidates.principle IS NULL OR candidates.principle = '')
                ");
            }

            // Also backfill from tb_kandidat if missing
            if (Schema::hasTable('tb_kandidat')) {
                DB::statement("
                    UPDATE candidates 
                    SET principle = (
                        SELECT principle FROM tb_kandidat WHERE tb_kandidat.id = candidates.id
                    )
                    WHERE (candidates.principle IS NULL OR candidates.principle = '')
                    AND EXISTS (
                        SELECT 1 FROM tb_kandidat 
                        WHERE tb_kandidat.id = candidates.id 
                        AND tb_kandidat.principle IS NOT NULL 
                        AND tb_kandidat.principle != ''
                    )
                ");
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('candidates') && Schema::hasColumn('candidates', 'principle')) {
            Schema::table('candidates', function (Blueprint $table) {
                $table->dropColumn('principle');
            });
        }
    }
};
