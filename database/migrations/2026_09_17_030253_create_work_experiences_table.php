<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained('candidates')->cascadeOnDelete();
            $table->string('company_name');
            $table->string('position');
            $table->string('company_phone')->nullable();
            $table->text('reason_for_leaving')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            
            // Reference Check
            $table->string('supervisor_name')->nullable();
            $table->text('performance_notes')->nullable();
            $table->text('discipline_notes')->nullable();
            $table->text('responsibility_notes')->nullable();
            $table->text('strengths')->nullable();
            $table->text('weaknesses')->nullable();
            $table->date('check_date')->nullable();
            $table->string('proof_attachment_path')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_experiences');
    }
};