<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('tb_math')) {
            Schema::table('tb_math', function (Blueprint $table) {
                if (!Schema::hasColumn('tb_math', 'is_active')) {
                    $table->boolean('is_active')->default(1)->after('correct_answer');
                }
                if (!Schema::hasColumn('tb_math', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable()->after('created_at');
                }
            });

            // Pastikan seluruh data lama di tb_math berstatus aktif
            try {
                DB::table('tb_math')->whereNull('is_active')->orWhere('is_active', 0)->update(['is_active' => 1]);
            } catch (\Throwable $e) {
                // Ignore if not applicable
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('tb_math')) {
            Schema::table('tb_math', function (Blueprint $table) {
                if (Schema::hasColumn('tb_math', 'is_active')) {
                    $table->dropColumn('is_active');
                }
                if (Schema::hasColumn('tb_math', 'updated_at')) {
                    $table->dropColumn('updated_at');
                }
            });
        }
    }
};
