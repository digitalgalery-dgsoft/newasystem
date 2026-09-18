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
            if (!Schema::hasColumn('candidates', 'jenis')) {
                $table->string('jenis', 100)->nullable()->index();
            }
            if (!Schema::hasColumn('candidates', 'status_kandidat')) {
                $table->string('status_kandidat', 100)->nullable()->index();
            }
            if (!Schema::hasColumn('candidates', 'kategori_kandidat')) {
                $table->string('kategori_kandidat', 100)->nullable();
            }
            if (!Schema::hasColumn('candidates', 'ai_score')) {
                $table->integer('ai_score')->nullable();
            }
        });

        // Use raw statements for composite indexes to control column ordering and SQLite compatibility
        DB::statement('CREATE INDEX IF NOT EXISTS idx_cand_portal ON candidates(jenis, status, status_kandidat, id DESC)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_cand_done ON candidates(ttd_prinsiple, status, id DESC)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_cand_arsip ON candidates(status, status_kandidat, id DESC)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_cand_area ON candidates(area, jenis, status, id DESC)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_cand_filter_interview ON candidates(status, jenis, ttd_prinsiple, id DESC)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS idx_cand_portal');
        DB::statement('DROP INDEX IF EXISTS idx_cand_done');
        DB::statement('DROP INDEX IF EXISTS idx_cand_arsip');
        DB::statement('DROP INDEX IF EXISTS idx_cand_area');
        DB::statement('DROP INDEX IF EXISTS idx_cand_filter_interview');
    }
};
