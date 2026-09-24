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
        if (Schema::hasTable('helpdesk_tickets')) {
            Schema::table('helpdesk_tickets', function (Blueprint $table) {
                $table->text('attachment')->nullable()->change();
            });
        }

        if (Schema::hasTable('helpdesk_ticket_replies')) {
            Schema::table('helpdesk_ticket_replies', function (Blueprint $table) {
                $table->text('attachment')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('helpdesk_tickets')) {
            Schema::table('helpdesk_tickets', function (Blueprint $table) {
                $table->string('attachment', 255)->nullable()->change();
            });
        }

        if (Schema::hasTable('helpdesk_ticket_replies')) {
            Schema::table('helpdesk_ticket_replies', function (Blueprint $table) {
                $table->string('attachment', 255)->nullable()->change();
            });
        }
    }
};
