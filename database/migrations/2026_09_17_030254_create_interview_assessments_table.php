<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interview_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained('candidates')->cascadeOnDelete();
            $table->foreignId('interviewer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('interview_date');
            
            // 1-5 Scoring Metrics
            $table->unsignedTinyInteger('work_motivation')->default(3);
            $table->unsignedTinyInteger('appearance')->default(3);
            $table->unsignedTinyInteger('attitude')->default(3);
            $table->unsignedTinyInteger('comprehension')->default(3);
            $table->text('other_notes')->nullable();
            $table->string('recommendation', 30)->default('recommended')->index();
            $table->decimal('offered_salary', 14, 2)->nullable();
            $table->string('placement_area')->nullable();
            $table->string('interviewer_signature_path')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interview_assessments');
    }
};