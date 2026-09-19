<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('candidates', 'tes_ke')) {
            Schema::table('candidates', function (Blueprint $table) {
                $table->integer('tes_ke')->default(1)->nullable()->after('tes_matematika');
            });
        }

        // Backfill tes_ke dari tb_kandidat jika tabelnya ada
        if (Schema::hasTable('tb_kandidat')) {
            try {
                DB::statement("
                    UPDATE candidates 
                    SET tes_ke = (
                        SELECT COALESCE(tk.tes_ke, 1) 
                        FROM tb_kandidat tk 
                        WHERE tk.id = candidates.id 
                        LIMIT 1
                    )
                    WHERE EXISTS (
                        SELECT 1 
                        FROM tb_kandidat tk 
                        WHERE tk.id = candidates.id AND tk.tes_ke IS NOT NULL AND tk.tes_ke > 0
                    )
                ");
            } catch (\Throwable $e) {
                // Ignore if query fails on certain sqlite setups
            }
        }

        // Pastikan tidak ada tes_ke yang NULL atau 0
        DB::table('candidates')
            ->whereNull('tes_ke')
            ->orWhere('tes_ke', 0)
            ->update(['tes_ke' => 1]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('candidates', 'tes_ke')) {
            Schema::table('candidates', function (Blueprint $table) {
                $table->dropColumn('tes_ke');
            });
        }
    }
};
