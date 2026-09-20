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
        // 1. Tabel Tasks (Kanban / ToDoList)
        if (!Schema::hasTable('tasks')) {
            Schema::create('tasks', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('attachment_url')->nullable();
                $table->string('priority', 20)->default('Medium'); // Low, Medium, High
                $table->string('tags')->nullable();
                $table->date('due_date')->nullable();
                $table->string('status', 30)->default('todo'); // todo, inprogress, review, done, archived
                $table->string('user')->nullable(); // Pembuat tugas (nama karyawan / user)
                $table->string('assignee')->nullable(); // Penerima tugas
                $table->string('delegator')->nullable(); // Pendelegasi tugas
                $table->dateTime('date_input')->nullable();
                $table->dateTime('date_completed')->nullable();
                $table->timestamps();

                // Indeks untuk performa query filter & board
                $table->index('status');
                $table->index('user');
                $table->index('assignee');
                $table->index('delegator');
                $table->index('due_date');
                $table->index('priority');
            });
        }

        // 2. Tabel Subtasks (Checklist Item per Tugas)
        if (!Schema::hasTable('task_subtasks')) {
            Schema::create('task_subtasks', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('task_id');
                $table->text('subtask_text');
                $table->boolean('is_completed')->default(false);
                $table->dateTime('created_at')->nullable();
                $table->dateTime('updated_at')->nullable();

                $table->index('task_id');
                $table->index('is_completed');
            });
        }

        // 3. Tabel Comments (Komentar & Diskusi Tugas)
        if (!Schema::hasTable('task_comments')) {
            Schema::create('task_comments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('task_id');
                $table->string('user_comment');
                $table->text('comment_text');
                $table->dateTime('comment_date')->nullable();
                $table->string('attachment_url')->nullable();
                $table->timestamps();

                $table->index('task_id');
                $table->index('user_comment');
            });
        }

        // 4. Tabel Activities (Jejak Riwayat Aktivitas & Perubahan Tugas)
        if (!Schema::hasTable('task_activities')) {
            Schema::create('task_activities', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('task_id');
                $table->string('user_actor', 100);
                $table->string('action_type', 50); // status_change, task_edited, subtask_added, subtask_toggle, etc.
                $table->text('detail_new')->nullable();
                $table->text('detail_old')->nullable();
                $table->dateTime('created_at')->nullable();

                $table->index('task_id');
                $table->index('user_actor');
                $table->index('action_type');
            });
        }

        // 5. Tabel Notifications (Notifikasi Penugasan, Review, & Mention)
        if (!Schema::hasTable('task_notifications')) {
            Schema::create('task_notifications', function (Blueprint $table) {
                $table->id();
                $table->string('user_recipient', 100);
                $table->unsignedBigInteger('task_id');
                $table->string('notification_type', 50); // ASSIGNED, REVIEW_REQUEST, MENTION, COMMENT
                $table->text('message');
                $table->boolean('is_read')->default(false);
                $table->dateTime('created_at')->nullable();

                $table->index('user_recipient');
                $table->index('task_id');
                $table->index('is_read');
            });
        }

        // 6. Tabel Categories
        if (!Schema::hasTable('task_categories')) {
            Schema::create('task_categories', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('name', 100);
                $table->timestamps();
            });
        }

        // 7. Tabel Daily Workplan (tb_workplan legacy log aktivitas divisi)
        if (!Schema::hasTable('tb_workplan')) {
            Schema::create('tb_workplan', function (Blueprint $table) {
                $table->increments('kode');
                $table->date('tanggal');
                $table->string('aktivitas', 500);
                $table->string('divisi')->nullable();
                $table->string('kendala', 500)->nullable();
                $table->string('user')->nullable();
                $table->dateTime('waktu')->nullable();

                $table->index('tanggal');
                $table->index('divisi');
                $table->index('user');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_workplan');
        Schema::dropIfExists('task_categories');
        Schema::dropIfExists('task_notifications');
        Schema::dropIfExists('task_activities');
        Schema::dropIfExists('task_comments');
        Schema::dropIfExists('task_subtasks');
        Schema::dropIfExists('tasks');
    }
};
