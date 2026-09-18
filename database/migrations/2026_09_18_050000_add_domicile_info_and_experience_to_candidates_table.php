<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('candidates')) {
            Schema::table('candidates', function (Blueprint $table) {
                if (!Schema::hasColumn('candidates', 'province_domicile')) {
                    $table->string('province_domicile')->nullable()->after('address_domicile');
                }
                if (!Schema::hasColumn('candidates', 'city_domicile')) {
                    $table->string('city_domicile')->nullable()->after('province_domicile');
                }
                if (!Schema::hasColumn('candidates', 'info_lowongan')) {
                    $table->string('info_lowongan')->nullable()->after('source_type');
                }
                if (!Schema::hasColumn('candidates', 'experience_summary')) {
                    $table->text('experience_summary')->nullable()->after('info_lowongan');
                }
            });
        }

        if (Schema::hasTable('tb_kandidat')) {
            Schema::table('tb_kandidat', function (Blueprint $table) {
                if (!Schema::hasColumn('tb_kandidat', 'province_domicile')) {
                    $table->string('province_domicile')->nullable()->after('alamat_domisili');
                }
                if (!Schema::hasColumn('tb_kandidat', 'city_domicile')) {
                    $table->string('city_domicile')->nullable()->after('province_domicile');
                }
                if (!Schema::hasColumn('tb_kandidat', 'info_lowongan')) {
                    $table->string('info_lowongan')->nullable()->after('info');
                }
                if (!Schema::hasColumn('tb_kandidat', 'experience_summary')) {
                    $table->text('experience_summary')->nullable()->after('info_lowongan');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('candidates')) {
            Schema::table('candidates', function (Blueprint $table) {
                $cols = ['province_domicile', 'city_domicile', 'info_lowongan', 'experience_summary'];
                foreach ($cols as $c) {
                    if (Schema::hasColumn('candidates', $c)) {
                        $table->dropColumn($c);
                    }
                }
            });
        }

        if (Schema::hasTable('tb_kandidat')) {
            Schema::table('tb_kandidat', function (Blueprint $table) {
                $cols = ['province_domicile', 'city_domicile', 'info_lowongan', 'experience_summary'];
                foreach ($cols as $c) {
                    if (Schema::hasColumn('tb_kandidat', $c)) {
                        $table->dropColumn($c);
                    }
                }
            });
        }
    }
};
