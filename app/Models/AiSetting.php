<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AiSetting extends Model
{
    use HasFactory;

    protected $table = 'ai_settings';
    protected $guarded = ['id'];

    public function getKeysListAttribute(): array
    {
        if (empty($this->gemini_keys)) {
            return [];
        }
        $keys = preg_split('/[\r\n\s]+/', $this->gemini_keys);
        return array_values(array_filter(array_map('trim', $keys)));
    }

    protected static function booted()
    {
        static::saved(function ($model) {
            // Dual sync to legacy tb_ai_setting
            if (Schema::hasTable('tb_ai_setting')) {
                DB::table('tb_ai_setting')->updateOrInsert(
                    ['id' => 1],
                    [
                        'gemini_keys' => $model->gemini_keys,
                        'gemini_model' => $model->gemini_model,
                        'sumopod_key' => $model->sumopod_key,
                        'sumopod_model' => $model->sumopod_model,
                        'wa_api_key' => $model->wa_api_key,
                        'wa_device' => $model->wa_device,
                        'wa_template' => $model->wa_template,
                    ]
                );
            }
        });
    }
}