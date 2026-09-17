<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('principle_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained('candidates')->cascadeOnDelete();
            $table->foreignId('principle_id')->constrained('principles')->cascadeOnDelete();
            $table->string('approval_token', 64)->unique()->index();
            $table->string('status', 30)->default('pending')->index();
            $table->text('notes')->nullable();
            $table->string('signature_path')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('principle_approvals');
    }
};