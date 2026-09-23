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
        Schema::create('password_reset_requests', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->string('session_token', 64)->index();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->string('nik')->index();
            $table->string('nama_karyawan');
            $table->string('email')->nullable();
            $table->string('telepon')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('entitas')->nullable();
            $table->string('tipe_karyawan')->nullable();
            $table->string('status')->default('pending'); // pending, replied, resolved, rejected
            $table->boolean('odoo_synced')->default(false);
            $table->text('request_message')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('access_sent_at')->nullable();
            $table->timestamps();
        });

        Schema::create('password_reset_chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('password_reset_requests')->cascadeOnDelete();
            $table->string('sender_type'); // employee, admin, system
            $table->string('sender_name');
            $table->text('message');
            $table->json('meta')->nullable(); // credentials payload if any
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_reset_chat_messages');
        Schema::dropIfExists('password_reset_requests');
    }
};
