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
            if (!Schema::hasColumn('candidates', 'info')) {
                $table->string('info')->nullable()->after('info_lowongan');
            }
            if (!Schema::hasColumn('candidates', 'undangan')) {
                $table->string('undangan')->nullable()->after('info');
            }
        });

        // Sinkronkan data info dan undangan yang ada dari tb_kandidat ke candidates
        try {
            if (Schema::hasTable('tb_kandidat')) {
                $driver = DB::getDriverName();
                if ($driver === 'mysql') {
                    DB::statement("
                        UPDATE candidates c
                        INNER JOIN tb_kandidat t ON c.nik = t.no_ktp
                        SET 
                            c.info = COALESCE(t.info, c.info),
                            c.undangan = COALESCE(t.undangan, c.undangan)
                        WHERE c.jenis = 'Walkin'
                    ");
                } else {
                    DB::statement("
                        UPDATE candidates 
                        SET 
                            info = (SELECT info FROM tb_kandidat WHERE tb_kandidat.no_ktp = candidates.nik LIMIT 1),
                            undangan = (SELECT undangan FROM tb_kandidat WHERE tb_kandidat.no_ktp = candidates.nik LIMIT 1)
                        WHERE jenis = 'Walkin'
                    ");
                }
            }
        } catch (\Throwable $e) {
            // Abaikan jika error
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            if (Schema::hasColumn('candidates', 'undangan')) {
                $table->dropColumn('undangan');
            }
            if (Schema::hasColumn('candidates', 'info')) {
                $table->dropColumn('info');
            }
        });
    }
};
