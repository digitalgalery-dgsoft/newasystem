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
        Schema::table('candidates', function (Blueprint $table) {
            if (!Schema::hasColumn('candidates', 'idprinsiple')) {
                $table->string('idprinsiple')->nullable()->index();
            }
        });

        // Sinkronisasi idprinsiple dari tb_kandidat ke candidates
        if (Schema::hasTable('tb_kandidat')) {
            DB::statement("
                UPDATE candidates 
                SET idprinsiple = (
                    SELECT tb_kandidat.idprinsiple 
                    FROM tb_kandidat 
                    WHERE tb_kandidat.id = candidates.id
                )
                WHERE EXISTS (
                    SELECT 1 
                    FROM tb_kandidat 
                    WHERE tb_kandidat.id = candidates.id 
                      AND tb_kandidat.idprinsiple IS NOT NULL 
                      AND tb_kandidat.idprinsiple != ''
                )
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            if (Schema::hasColumn('candidates', 'idprinsiple')) {
                $table->dropColumn('idprinsiple');
            }
        });
    }
};
