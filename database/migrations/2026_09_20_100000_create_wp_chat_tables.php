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
        // 1. Tabel Group Chat (WhatsApp Style)
        if (!Schema::hasTable('wp_chat_groups')) {
            Schema::create('wp_chat_groups', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('avatar_color', 30)->default('#10b981');
                $table->string('created_by')->nullable();
                $table->unsignedBigInteger('created_by_id')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index('created_by');
                $table->index('is_active');
            });
        }

        // 2. Tabel Anggota Group Chat
        if (!Schema::hasTable('wp_chat_group_members')) {
            Schema::create('wp_chat_group_members', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('group_id');
                $table->string('user_name');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('role', 20)->default('member'); // admin, member
                $table->dateTime('joined_at')->nullable();
                $table->dateTime('last_read_at')->nullable();
                $table->timestamps();

                $table->index('group_id');
                $table->index('user_name');
                $table->index(['group_id', 'user_name']);
            });
        }

        // 3. Tabel Pesan Chat
        if (!Schema::hasTable('wp_chat_messages')) {
            Schema::create('wp_chat_messages', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('group_id');
                $table->string('user_sender');
                $table->unsignedBigInteger('sender_id')->nullable();
                $table->text('message_text');
                $table->timestamps();

                $table->index('group_id');
                $table->index('created_at');
                $table->index(['group_id', 'created_at']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wp_chat_messages');
        Schema::dropIfExists('wp_chat_group_members');
        Schema::dropIfExists('wp_chat_groups');
    }
};
