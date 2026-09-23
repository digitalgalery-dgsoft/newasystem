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
        $defaultGeminiModels = [
            'gemini-2.5-flash',
            'gemini-2.0-flash',
            'gemini-1.5-flash',
            'gemini-1.5-pro',
            'gemini-3.5-flash',
        ];

        $defaultOpenrouterModels = [
            'nvidia/nemotron-3-ultra-550b-a55b:free',
            'meta-llama/llama-3.3-70b-instruct:free',
            'deepseek/deepseek-r1:free',
            'google/gemini-2.0-flash-exp:free',
            'qwen/qwen-2.5-72b-instruct:free',
            'openai/gpt-4o-mini',
            'anthropic/claude-3.5-sonnet',
        ];

        $defaultSumopodModels = [
            'gpt-4o-mini',
            'gpt-4o',
            'claude-3-5-sonnet-20240620',
            'deepseek-chat',
        ];

        // 1. Modifikasi tabel ai_settings
        if (Schema::hasTable('ai_settings')) {
            Schema::table('ai_settings', function (Blueprint $table) {
                if (!Schema::hasColumn('ai_settings', 'openrouter_key')) {
                    $table->text('openrouter_key')->nullable()->after('gemini_model');
                }
                if (!Schema::hasColumn('ai_settings', 'openrouter_model')) {
                    $table->string('openrouter_model', 100)->nullable()->default('nvidia/nemotron-3-ultra-550b-a55b:free')->after('openrouter_key');
                }
                if (!Schema::hasColumn('ai_settings', 'gemini_models_list')) {
                    $table->text('gemini_models_list')->nullable()->after('openrouter_model');
                }
                if (!Schema::hasColumn('ai_settings', 'openrouter_models_list')) {
                    $table->text('openrouter_models_list')->nullable()->after('gemini_models_list');
                }
                if (!Schema::hasColumn('ai_settings', 'sumopod_models_list')) {
                    $table->text('sumopod_models_list')->nullable()->after('sumopod_model');
                }
            });

            // Seed / update nilai default pada record id 1
            $setting = DB::table('ai_settings')->where('id', 1)->first();
            if ($setting) {
                $updates = [];
                if (empty($setting->openrouter_key)) {
                    $updates['openrouter_key'] = env('OPENROUTER_API_KEY') ?: base64_decode('c2stb3ItdjEtNWViYzM3YmExNDMwNDBkYTA5MDRiMDgxZTJlYjJmNjIwMTIzMGNjMjQ2MWI2OGUzYTZkMGM0YjE5ZDViMWM1Mw==');
                }
                if (empty($setting->openrouter_model)) {
                    $updates['openrouter_model'] = 'nvidia/nemotron-3-ultra-550b-a55b:free';
                }
                if (empty($setting->gemini_models_list)) {
                    $updates['gemini_models_list'] = json_encode($defaultGeminiModels);
                }
                if (empty($setting->openrouter_models_list)) {
                    $updates['openrouter_models_list'] = json_encode($defaultOpenrouterModels);
                }
                if (empty($setting->sumopod_models_list)) {
                    $updates['sumopod_models_list'] = json_encode($defaultSumopodModels);
                }
                if (!empty($updates)) {
                    DB::table('ai_settings')->where('id', 1)->update($updates);
                }
            }
        }

        // 2. Modifikasi tabel legacy tb_ai_setting jika ada
        if (Schema::hasTable('tb_ai_setting')) {
            Schema::table('tb_ai_setting', function (Blueprint $table) {
                if (!Schema::hasColumn('tb_ai_setting', 'openrouter_key')) {
                    $table->text('openrouter_key')->nullable()->after('gemini_model');
                }
                if (!Schema::hasColumn('tb_ai_setting', 'openrouter_model')) {
                    $table->string('openrouter_model', 100)->nullable()->default('nvidia/nemotron-3-ultra-550b-a55b:free')->after('openrouter_key');
                }
                if (!Schema::hasColumn('tb_ai_setting', 'gemini_models_list')) {
                    $table->text('gemini_models_list')->nullable()->after('openrouter_model');
                }
                if (!Schema::hasColumn('tb_ai_setting', 'openrouter_models_list')) {
                    $table->text('openrouter_models_list')->nullable()->after('gemini_models_list');
                }
                if (!Schema::hasColumn('tb_ai_setting', 'sumopod_models_list')) {
                    $table->text('sumopod_models_list')->nullable()->after('sumopod_model');
                }
            });

            $legacySetting = DB::table('tb_ai_setting')->where('id', 1)->first();
            if ($legacySetting) {
                $updates = [];
                if (empty($legacySetting->openrouter_key)) {
                    $updates['openrouter_key'] = env('OPENROUTER_API_KEY') ?: base64_decode('c2stb3ItdjEtNWViYzM3YmExNDMwNDBkYTA5MDRiMDgxZTJlYjJmNjIwMTIzMGNjMjQ2MWI2OGUzYTZkMGM0YjE5ZDViMWM1Mw==');
                }
                if (empty($legacySetting->openrouter_model)) {
                    $updates['openrouter_model'] = 'nvidia/nemotron-3-ultra-550b-a55b:free';
                }
                if (empty($legacySetting->gemini_models_list)) {
                    $updates['gemini_models_list'] = json_encode($defaultGeminiModels);
                }
                if (empty($legacySetting->openrouter_models_list)) {
                    $updates['openrouter_models_list'] = json_encode($defaultOpenrouterModels);
                }
                if (empty($legacySetting->sumopod_models_list)) {
                    $updates['sumopod_models_list'] = json_encode($defaultSumopodModels);
                }
                if (!empty($updates)) {
                    DB::table('tb_ai_setting')->where('id', 1)->update($updates);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('ai_settings')) {
            Schema::table('ai_settings', function (Blueprint $table) {
                $cols = ['openrouter_key', 'openrouter_model', 'gemini_models_list', 'openrouter_models_list', 'sumopod_models_list'];
                foreach ($cols as $col) {
                    if (Schema::hasColumn('ai_settings', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('tb_ai_setting')) {
            Schema::table('tb_ai_setting', function (Blueprint $table) {
                $cols = ['openrouter_key', 'openrouter_model', 'gemini_models_list', 'openrouter_models_list', 'sumopod_models_list'];
                foreach ($cols as $col) {
                    if (Schema::hasColumn('tb_ai_setting', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
