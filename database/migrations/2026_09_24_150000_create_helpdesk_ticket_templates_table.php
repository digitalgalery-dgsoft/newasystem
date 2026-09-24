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
        if (!Schema::hasTable('helpdesk_ticket_templates')) {
            Schema::create('helpdesk_ticket_templates', function (Blueprint $table) {
                $table->id();
                $table->foreignId('division_id')->nullable()->constrained('helpdesk_divisions')->nullOnDelete();
                $table->string('title');
                $table->string('subject')->nullable();
                $table->text('message');
                $table->string('attachment')->nullable();
                $table->boolean('is_active')->default(true);
                $table->integer('order_num')->default(0);
                $table->timestamps();

                $table->index(['division_id', 'is_active']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('helpdesk_ticket_templates');
    }
};
