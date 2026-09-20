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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('user_name', 150)->nullable()->index();
            $table->string('user_email', 150)->nullable();
            $table->string('user_jabatan', 150)->nullable();
            $table->string('action', 50)->index(); // LOGIN, LOGOUT, CREATE, UPDATE, DELETE, EXPORT, SYNC, SWITCH_USER, PROFILE, dll.
            $table->string('module', 100)->index(); // Auth, Master Karyawan, Master Prinsiple, Talent Pool, Kandidat Portal, Job Portal, CBT Online, Odoo Sync, RBAC, Work Plan, Profile, dsb.
            $table->text('description'); // Penjelasan aktivitas yang ramah dibaca
            $table->string('subject_type', 150)->nullable()->index();
            $table->string('subject_id', 100)->nullable()->index();
            $table->json('properties')->nullable(); // Payload perubahan (diff old vs new values, parameter filter, dsb.)
            $table->string('ip_address', 50)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('url')->nullable();
            $table->string('method', 10)->nullable(); // GET, POST, PUT, DELETE
            $table->timestamps();

            // Index gabungan untuk kecepatan filter
            $table->index(['created_at', 'module']);
            $table->index(['created_at', 'action']);
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
