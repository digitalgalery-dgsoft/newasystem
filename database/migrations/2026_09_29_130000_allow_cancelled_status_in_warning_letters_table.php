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
        Schema::table('warning_letters', function (Blueprint $table) {
            $table->string('status', 50)->default('review_head')->change();
        });

        Schema::table('warning_letter_approvals', function (Blueprint $table) {
            $table->string('action', 50)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warning_letter_approvals', function (Blueprint $table) {
            $table->enum('action', ['approve', 'reject', 'revise'])->change();
        });

        Schema::table('warning_letters', function (Blueprint $table) {
            $table->string('status', 50)->default('review_hrd')->change();
        });
    }
};
