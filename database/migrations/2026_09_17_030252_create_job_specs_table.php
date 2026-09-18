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
        if (!Schema::hasTable('job_specs')) {
            Schema::create('job_specs', function (Blueprint $table) {
                $table->id();
                $table->string('job_title', 200);
                $table->string('job_prinsiple', 200)->nullable();
                $table->string('job_area', 200)->nullable();
                $table->string('province', 100)->nullable();
                $table->string('city', 100)->nullable();
                $table->text('job_quals')->nullable();
                $table->text('job_skills')->nullable();
                $table->text('job_exp')->nullable();
                $table->text('job_desc')->nullable();
                $table->text('additional_info')->nullable();
                $table->date('tgl_expired')->nullable();
                $table->string('created_by', 100)->nullable();
                $table->string('status', 20)->default('active')->index();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_specs');
    }
};
