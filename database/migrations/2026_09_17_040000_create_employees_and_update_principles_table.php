<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Update principles table if code column doesn't exist
        if (Schema::hasTable('principles')) {
            Schema::table('principles', function (Blueprint $table) {
                if (!Schema::hasColumn('principles', 'code')) {
                    $table->string('code')->nullable()->after('id');
                }
            });
        }

        // Create employees table
        if (!Schema::hasTable('employees')) {
            Schema::create('employees', function (Blueprint $table) {
                $table->id();
                $table->string('nik')->unique();
                $table->string('nip')->nullable();
                $table->string('nama_karyawan');
                $table->string('email')->nullable();
                $table->string('telepon')->nullable();
                $table->date('tanggal_join');
                $table->string('area');
                $table->string('jabatan');
                $table->string('divisi')->nullable();
                $table->foreignId('principle_id')->nullable()->constrained('principles')->nullOnDelete();
                $table->string('prinsiple')->nullable();
                $table->string('pimpinan')->nullable();
                $table->string('jabatan_pimpinan')->nullable();
                $table->string('level')->default('STAFF');
                $table->string('tipe_karyawan')->default('Inhouse'); // Inhouse, RateCard
                $table->string('status')->default('Aktiv'); // Aktiv, Resign, Review
                $table->boolean('has_komponen')->default(true);
                $table->string('foto')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
        if (Schema::hasTable('principles')) {
            Schema::table('principles', function (Blueprint $table) {
                if (Schema::hasColumn('principles', 'code')) {
                    $table->dropColumn('code');
                }
            });
        }
    }
};