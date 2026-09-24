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
        // 1. Tabel Divisi Helpdesk
        if (!Schema::hasTable('helpdesk_divisions')) {
            Schema::create('helpdesk_divisions', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->unique();
                $table->text('description')->nullable();
                $table->string('icon')->default('fa-solid fa-headset');
                $table->string('color')->default('blue'); // blue, indigo, emerald, amber, rose, purple
                $table->string('wa_group_id')->nullable();
                $table->string('wa_group_link')->nullable();
                $table->integer('sla_hours')->default(24);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2. Tabel Petugas / Agen Divisi (Karyawan Inhouse)
        if (!Schema::hasTable('helpdesk_division_agents')) {
            Schema::create('helpdesk_division_agents', function (Blueprint $table) {
                $table->id();
                $table->foreignId('division_id')->constrained('helpdesk_divisions')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->boolean('is_lead')->default(false);
                $table->boolean('is_auto_assign')->default(true);
                $table->timestamps();

                $table->unique(['division_id', 'user_id']);
            });
        }

        // 3. Tabel Utama Tiket Helpdesk
        if (!Schema::hasTable('helpdesk_tickets')) {
            Schema::create('helpdesk_tickets', function (Blueprint $table) {
                $table->id();
                $table->string('ticket_number')->unique();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('division_id')->constrained('helpdesk_divisions')->onDelete('cascade');
                $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
                $table->string('subject');
                $table->longText('description');
                $table->string('priority')->default('Medium'); // Low, Medium, High, Urgent
                $table->string('status')->default('open'); // open, in_progress, answered, resolved, closed
                $table->string('category')->nullable();
                $table->string('attachment')->nullable();
                $table->dateTime('due_date')->nullable();
                $table->string('sentiment')->nullable()->default('Neutral');
                $table->unsignedBigInteger('workplan_task_id')->nullable()->index();
                $table->dateTime('first_response_at')->nullable();
                $table->dateTime('resolved_at')->nullable();
                $table->dateTime('closed_at')->nullable();
                $table->timestamps();

                $table->index(['status', 'division_id']);
                $table->index(['user_id', 'status']);
                $table->index(['assigned_to', 'status']);
            });
        }

        // 4. Tabel Percakapan / Balasan Tiket (Replies & Internal Notes)
        if (!Schema::hasTable('helpdesk_ticket_replies')) {
            Schema::create('helpdesk_ticket_replies', function (Blueprint $table) {
                $table->id();
                $table->foreignId('ticket_id')->constrained('helpdesk_tickets')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->longText('message');
                $table->string('attachment')->nullable();
                $table->boolean('is_internal')->default(false); // Catatan internal antar agen
                $table->timestamps();

                $table->index('ticket_id');
            });
        }

        // 5. Tabel Audit Log Tiket (Ticket Logs)
        if (!Schema::hasTable('helpdesk_ticket_logs')) {
            Schema::create('helpdesk_ticket_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('ticket_id')->constrained('helpdesk_tickets')->onDelete('cascade');
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('action'); // Created, Assigned, Responded, Status_Changed, Priority_Changed, Closed
                $table->text('details')->nullable();
                $table->timestamp('created_at')->useCurrent();

                $table->index('ticket_id');
            });
        }

        // 6. Tabel Template Balasan Cepat (Canned Responses)
        if (!Schema::hasTable('helpdesk_canned_responses')) {
            Schema::create('helpdesk_canned_responses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('division_id')->nullable()->constrained('helpdesk_divisions')->nullOnDelete();
                $table->string('title');
                $table->text('content');
                $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('helpdesk_canned_responses');
        Schema::dropIfExists('helpdesk_ticket_logs');
        Schema::dropIfExists('helpdesk_ticket_replies');
        Schema::dropIfExists('helpdesk_tickets');
        Schema::dropIfExists('helpdesk_division_agents');
        Schema::dropIfExists('helpdesk_divisions');
    }
};
