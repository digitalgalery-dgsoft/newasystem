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
        if (Schema::hasTable('ai_settings')) {
            Schema::table('ai_settings', function (Blueprint $table) {
                if (!Schema::hasColumn('ai_settings', 'gemini_expired_keys')) {
                    $table->longText('gemini_expired_keys')->nullable()->after('gemini_keys');
                }
            });
        }

        if (Schema::hasTable('tb_ai_setting')) {
            Schema::table('tb_ai_setting', function (Blueprint $table) {
                if (!Schema::hasColumn('tb_ai_setting', 'gemini_expired_keys')) {
                    $table->longText('gemini_expired_keys')->nullable()->after('gemini_keys');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('ai_settings')) {
            Schema::table('ai_settings', function (Blueprint $table) {
                if (Schema::hasColumn('ai_settings', 'gemini_expired_keys')) {
                    $table->dropColumn('gemini_expired_keys');
                }
            });
        }

        if (Schema::hasTable('tb_ai_setting')) {
            Schema::table('tb_ai_setting', function (Blueprint $table) {
                if (Schema::hasColumn('tb_ai_setting', 'gemini_expired_keys')) {
                    $table->dropColumn('gemini_expired_keys');
                }
            });
        }
    }
};
