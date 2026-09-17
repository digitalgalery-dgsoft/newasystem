<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 16)->unique()->index();
            $table->string('full_name')->index();
            $table->date('birth_date')->nullable();
            $table->string('birth_place')->nullable();
            $table->string('gender', 15)->nullable();
            $table->string('religion', 30)->nullable();
            $table->string('education', 50)->nullable();
            $table->string('phone', 25)->nullable();
            $table->string('whatsapp', 25)->nullable();
            $table->string('email', 100)->nullable()->index();
            $table->text('address_ktp')->nullable();
            $table->text('address_domicile')->nullable();
            $table->string('marital_status', 30)->nullable();
            $table->decimal('expected_salary', 14, 2)->default(0);
            $table->decimal('last_salary', 14, 2)->default(0);
            $table->string('work_motivation')->nullable();
            $table->text('strengths')->nullable();
            $table->text('weaknesses')->nullable();
            $table->string('current_activity')->nullable();
            $table->string('vehicle')->nullable();
            $table->string('driving_license')->nullable();
            $table->string('computer_skill')->nullable();
            $table->string('english_skill')->nullable();
            
            // Workflow Status & Classification
            $table->string('status', 40)->default('new')->index();
            $table->string('source_type', 40)->default('walk_in')->index();
            $table->string('applied_job', 150)->index();
            $table->string('area', 100)->index();
            
            $table->foreignId('principle_id')->nullable()->constrained('principles')->nullOnDelete();
            $table->foreignId('recruiter_id')->nullable()->constrained('users')->nullOnDelete();
            
            // Files & Signatures
            $table->string('photo_path')->nullable();
            $table->string('cv_path')->nullable();
            $table->string('signature_path')->nullable();
            
            // AI Analysis & Completeness
            $table->decimal('ai_score', 5, 2)->nullable();
            $table->json('ai_analysis')->nullable();
            $table->boolean('is_profile_complete')->default(false)->index();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};