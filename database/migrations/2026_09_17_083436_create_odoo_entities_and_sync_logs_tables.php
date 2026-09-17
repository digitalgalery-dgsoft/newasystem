<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('odoo_entities', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->string('odoo_url')->nullable();
            $table->string('odoo_db')->nullable();
            $table->string('odoo_username')->nullable();
            $table->text('odoo_api_key')->nullable();
            $table->boolean('is_active')->default(true);
            $table->dateTime('last_sync_at')->nullable();
            $table->string('last_sync_status', 30)->nullable();
            $table->text('last_sync_message')->nullable();
            $table->json('sync_counts')->nullable();
            $table->timestamps();
        });

        Schema::create('odoo_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->string('batch_id', 50)->index();
            $table->string('entity_code', 20)->index();
            $table->string('sync_type', 30)->default('employee');
            $table->string('trigger_type', 20)->default('manual');
            $table->string('status', 20)->default('pending');
            $table->integer('new_count')->default(0);
            $table->integer('update_count')->default(0);
            $table->integer('resign_count')->default(0);
            $table->integer('total_employee_count')->default(0);
            $table->json('details')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });

        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'odoo_id')) {
                $table->integer('odoo_id')->nullable()->index();
            }
            if (!Schema::hasColumn('employees', 'entity')) {
                $table->string('entity', 20)->nullable()->index();
            }
            if (!Schema::hasColumn('employees', 'last_sync_at')) {
                $table->dateTime('last_sync_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['odoo_id', 'entity', 'last_sync_at']);
        });
        Schema::dropIfExists('odoo_sync_logs');
        Schema::dropIfExists('odoo_entities');
    }
};
