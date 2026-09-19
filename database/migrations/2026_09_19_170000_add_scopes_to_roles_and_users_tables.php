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
        // 1. Tambah scope field pada tabel roles
        if (Schema::hasTable('roles')) {
            Schema::table('roles', function (Blueprint $table) {
                if (!Schema::hasColumn('roles', 'handle_all_principles')) {
                    $table->boolean('handle_all_principles')->default(true)->after('is_system');
                }
                if (!Schema::hasColumn('roles', 'allowed_principles')) {
                    $table->text('allowed_principles')->nullable()->after('handle_all_principles');
                }
                if (!Schema::hasColumn('roles', 'cover_all_areas')) {
                    $table->boolean('cover_all_areas')->default(true)->after('allowed_principles');
                }
                if (!Schema::hasColumn('roles', 'allowed_areas')) {
                    $table->text('allowed_areas')->nullable()->after('cover_all_areas');
                }
            });
        }

        // 2. Tambah scope field pada tabel users (override per-user)
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'scope_override')) {
                    $table->boolean('scope_override')->default(false)->after('is_active');
                }
                if (!Schema::hasColumn('users', 'handle_all_principles')) {
                    $table->boolean('handle_all_principles')->default(true)->after('scope_override');
                }
                if (!Schema::hasColumn('users', 'allowed_principles')) {
                    $table->text('allowed_principles')->nullable()->after('handle_all_principles');
                }
                if (!Schema::hasColumn('users', 'cover_all_areas')) {
                    $table->boolean('cover_all_areas')->default(true)->after('allowed_principles');
                }
                if (!Schema::hasColumn('users', 'allowed_areas')) {
                    $table->text('allowed_areas')->nullable()->after('cover_all_areas');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('roles')) {
            Schema::table('roles', function (Blueprint $table) {
                $cols = [];
                if (Schema::hasColumn('roles', 'handle_all_principles')) $cols[] = 'handle_all_principles';
                if (Schema::hasColumn('roles', 'allowed_principles')) $cols[] = 'allowed_principles';
                if (Schema::hasColumn('roles', 'cover_all_areas')) $cols[] = 'cover_all_areas';
                if (Schema::hasColumn('roles', 'allowed_areas')) $cols[] = 'allowed_areas';
                if (!empty($cols)) {
                    $table->dropColumn($cols);
                }
            });
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $cols = [];
                if (Schema::hasColumn('users', 'scope_override')) $cols[] = 'scope_override';
                if (Schema::hasColumn('users', 'handle_all_principles')) $cols[] = 'handle_all_principles';
                if (Schema::hasColumn('users', 'allowed_principles')) $cols[] = 'allowed_principles';
                if (Schema::hasColumn('users', 'cover_all_areas')) $cols[] = 'cover_all_areas';
                if (Schema::hasColumn('users', 'allowed_areas')) $cols[] = 'allowed_areas';
                if (!empty($cols)) {
                    $table->dropColumn($cols);
                }
            });
        }
    }
};
