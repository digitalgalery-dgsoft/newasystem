<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom ttd_prinsiple & time_prinsiple pada candidates jika belum ada
        Schema::table('candidates', function (Blueprint $table) {
            if (!Schema::hasColumn('candidates', 'ttd_prinsiple')) {
                $table->string('ttd_prinsiple')->nullable()->index();
            }
            if (!Schema::hasColumn('candidates', 'time_prinsiple')) {
                $table->string('time_prinsiple')->nullable();
            }
            if (!Schema::hasColumn('candidates', 'gender')) {
                $table->string('gender')->nullable()->index();
            }
        });

        // 2. Tambah kolom gender pada tb_kandidat jika belum ada
        Schema::table('tb_kandidat', function (Blueprint $table) {
            if (!Schema::hasColumn('tb_kandidat', 'gender')) {
                $table->string('gender')->nullable()->index();
            }
        });

        // 3. Sinkronisasi data ttd_prinsiple dari tb_kandidat ke candidates
        DB::statement("
            UPDATE candidates 
            SET ttd_prinsiple = (SELECT tb_kandidat.ttd_prinsiple FROM tb_kandidat WHERE tb_kandidat.id = candidates.id),
                time_prinsiple = (SELECT tb_kandidat.time_prinsiple FROM tb_kandidat WHERE tb_kandidat.id = candidates.id)
            WHERE EXISTS (
                SELECT 1 FROM tb_kandidat 
                WHERE tb_kandidat.id = candidates.id 
                  AND tb_kandidat.ttd_prinsiple IS NOT NULL 
                  AND tb_kandidat.ttd_prinsiple != ''
            )
        ");

        // 4. Sinkronisasi status Arsip
        DB::statement("
            UPDATE candidates 
            SET status = 'Arsip' 
            WHERE EXISTS (
                SELECT 1 FROM tb_kandidat 
                WHERE tb_kandidat.id = candidates.id 
                  AND tb_kandidat.status = 'Arsip'
            )
        ");

        // 5. Populate kolom gender berbasis NIK (Digit ke 7-8: >40 Perempuan, <=31 Laki-laki)
        DB::statement("
            UPDATE candidates 
            SET gender = CASE 
                WHEN CAST(SUBSTR(REPLACE(nik, ' ', ''), 7, 2) AS INTEGER) > 40 THEN 'Perempuan' 
                ELSE 'Laki-laki' 
            END 
            WHERE (gender IS NULL OR gender = '') 
              AND length(REPLACE(nik, ' ', '')) >= 8
        ");

        DB::statement("
            UPDATE tb_kandidat 
            SET gender = CASE 
                WHEN CAST(SUBSTR(REPLACE(no_ktp, ' ', ''), 7, 2) AS INTEGER) > 40 THEN 'Perempuan' 
                ELSE 'Laki-laki' 
            END 
            WHERE (gender IS NULL OR gender = '') 
              AND length(REPLACE(no_ktp, ' ', '')) >= 8
        ");
    }

    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            if (Schema::hasColumn('candidates', 'ttd_prinsiple')) {
                $table->dropColumn('ttd_prinsiple');
            }
            if (Schema::hasColumn('candidates', 'time_prinsiple')) {
                $table->dropColumn('time_prinsiple');
            }
        });

        Schema::table('tb_kandidat', function (Blueprint $table) {
            if (Schema::hasColumn('tb_kandidat', 'gender')) {
                $table->dropColumn('gender');
            }
        });
    }
};
