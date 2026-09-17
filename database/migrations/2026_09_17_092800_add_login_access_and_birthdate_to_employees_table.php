<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'tanggal_lahir')) {
                $table->date('tanggal_lahir')->nullable()->after('telepon');
            }
            if (!Schema::hasColumn('employees', 'akses_login')) {
                $table->boolean('akses_login')->default(false)->after('tipe_karyawan');
            }
            if (!Schema::hasColumn('employees', 'password')) {
                $table->string('password')->nullable()->after('akses_login');
            }
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['tanggal_lahir', 'akses_login', 'password']);
        });
    }
};
