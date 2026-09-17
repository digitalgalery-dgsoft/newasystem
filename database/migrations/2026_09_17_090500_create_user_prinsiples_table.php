<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_prinsiples', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lengkap');
            $table->string('jabatan')->nullable();
            $table->unsignedBigInteger('prinsiple_id')->nullable()->index();
            $table->string('prinsiple')->index();
            $table->string('area')->default('Nasional')->index();
            $table->string('email')->index();
            $table->string('no_wa')->nullable();
            $table->string('katakunci')->nullable();
            $table->string('status', 30)->default('Aktiv')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_prinsiples');
    }
};
