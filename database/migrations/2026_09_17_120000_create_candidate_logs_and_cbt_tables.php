<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom profil & CBT pada tabel candidates jika belum ada
        Schema::table('candidates', function (Blueprint $table) {
            if (!Schema::hasColumn('candidates', 'password')) {
                $table->string('password')->nullable()->after('email');
            }
            if (!Schema::hasColumn('candidates', 'spouse_name')) {
                $table->string('spouse_name', 150)->nullable()->after('marital_status');
            }
            if (!Schema::hasColumn('candidates', 'spouse_job')) {
                $table->string('spouse_job', 100)->nullable()->after('spouse_name');
            }
            if (!Schema::hasColumn('candidates', 'children_count')) {
                $table->integer('children_count')->nullable()->default(0)->after('spouse_job');
            }
            if (!Schema::hasColumn('candidates', 'child_order')) {
                $table->integer('child_order')->nullable()->after('children_count');
            }
            if (!Schema::hasColumn('candidates', 'other_skills')) {
                $table->string('other_skills', 255)->nullable()->after('english_skill');
            }
            if (!Schema::hasColumn('candidates', 'other_skills_level')) {
                $table->string('other_skills_level', 50)->nullable()->after('other_skills');
            }
            if (!Schema::hasColumn('candidates', 'statement_agreed')) {
                $table->boolean('statement_agreed')->default(false)->after('signature_path');
            }
            if (!Schema::hasColumn('candidates', 'tes_kepribadian')) {
                $table->string('tes_kepribadian', 50)->nullable()->after('is_profile_complete');
            }
            if (!Schema::hasColumn('candidates', 'tes_matematika')) {
                $table->string('tes_matematika', 50)->nullable()->after('tes_kepribadian');
            }
            if (!Schema::hasColumn('candidates', 'tes_komputer')) {
                $table->string('tes_komputer', 50)->nullable()->after('tes_matematika');
            }
            if (!Schema::hasColumn('candidates', 'buktikomputer')) {
                $table->string('buktikomputer', 255)->nullable()->after('tes_komputer');
            }
            if (!Schema::hasColumn('candidates', 'is_komputer')) {
                $table->boolean('is_komputer')->default(true)->after('buktikomputer');
            }
        });

        // 2. Tabel candidate_logs untuk mencatat aktivitas tes & login
        if (!Schema::hasTable('candidate_logs')) {
            Schema::create('candidate_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('candidate_id')->constrained('candidates')->cascadeOnDelete();
                $table->string('candidate_name', 150)->nullable();
                $table->string('activity', 255);
                $table->string('ip_address', 50)->nullable();
                $table->string('browser', 100)->nullable();
                $table->string('os', 255)->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('candidate_logs');

        Schema::table('candidates', function (Blueprint $table) {
            $columns = [
                'spouse_name', 'spouse_job', 'children_count', 'child_order',
                'other_skills', 'other_skills_level', 'statement_agreed',
                'tes_kepribadian', 'tes_matematika', 'tes_komputer', 'buktikomputer', 'is_komputer'
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('candidates', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
