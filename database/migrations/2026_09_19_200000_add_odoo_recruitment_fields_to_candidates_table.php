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
        Schema::table('candidates', function (Blueprint $table) {
            if (!Schema::hasColumn('candidates', 'odoo_applicant_id')) {
                $table->unsignedBigInteger('odoo_applicant_id')->nullable()->index()->after('notes');
            }
            if (!Schema::hasColumn('candidates', 'odoo_entity')) {
                $table->string('odoo_entity', 10)->nullable()->after('odoo_applicant_id');
            }
            if (!Schema::hasColumn('candidates', 'odoo_stage_id')) {
                $table->integer('odoo_stage_id')->nullable()->after('odoo_entity');
            }
            if (!Schema::hasColumn('candidates', 'odoo_stage_name')) {
                $table->string('odoo_stage_name', 100)->nullable()->after('odoo_stage_id');
            }
            if (!Schema::hasColumn('candidates', 'odoo_synced_at')) {
                $table->timestamp('odoo_synced_at')->nullable()->after('odoo_stage_name');
            }
            if (!Schema::hasColumn('candidates', 'odoo_applicant_data')) {
                $table->longText('odoo_applicant_data')->nullable()->after('odoo_synced_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            $cols = ['odoo_applicant_id', 'odoo_entity', 'odoo_stage_id', 'odoo_stage_name', 'odoo_synced_at', 'odoo_applicant_data'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('candidates', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
