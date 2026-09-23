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
        // 1. Tambah kolom approval_rules (JSON) ke approval_workflow_steps jika belum ada
        if (Schema::hasTable('approval_workflow_steps')) {
            Schema::table('approval_workflow_steps', function (Blueprint $table) {
                if (!Schema::hasColumn('approval_workflow_steps', 'approval_rules')) {
                    $table->json('approval_rules')->nullable()->after('entity_scope');
                }
            });
        }

        // 2. Tambah kolom area dan prinsiple ke approval_workflow_step_users jika belum ada
        if (Schema::hasTable('approval_workflow_step_users')) {
            Schema::table('approval_workflow_step_users', function (Blueprint $table) {
                if (!Schema::hasColumn('approval_workflow_step_users', 'area')) {
                    $table->string('area', 100)->default('ALL')->after('employee_id');
                }
                if (!Schema::hasColumn('approval_workflow_step_users', 'prinsiple')) {
                    $table->string('prinsiple', 100)->default('ALL')->after('area');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('approval_workflow_steps')) {
            Schema::table('approval_workflow_steps', function (Blueprint $table) {
                if (Schema::hasColumn('approval_workflow_steps', 'approval_rules')) {
                    $table->dropColumn('approval_rules');
                }
            });
        }

        if (Schema::hasTable('approval_workflow_step_users')) {
            Schema::table('approval_workflow_step_users', function (Blueprint $table) {
                if (Schema::hasColumn('approval_workflow_step_users', 'area')) {
                    $table->dropColumn('area');
                }
                if (Schema::hasColumn('approval_workflow_step_users', 'prinsiple')) {
                    $table->dropColumn('prinsiple');
                }
            });
        }
    }
};
