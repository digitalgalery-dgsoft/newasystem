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

    protected $casts = [
        'gemini_expired_keys' => 'array',
        'wa_use_pusat' => 'integer',
    ];

    /**
     * Get active Gemini keys (excluding expired / invalid keys)
     */
    public function getKeysListAttribute(): array
    {
        if (empty($this->gemini_keys)) {
            return [];
        }
        $keys = preg_split('/[\r\n\s]+/', $this->gemini_keys);
        $keys = array_values(array_filter(array_map('trim', $keys)));

        $expiredKeyStrings = array_map(function ($item) {
            return is_array($item) ? trim($item['key'] ?? '') : trim($item);
        }, $this->expired_keys_list);

        return array_values(array_filter($keys, function ($k) use ($expiredKeyStrings) {
            return !empty($k) && !in_array($k, $expiredKeyStrings, true);
        }));
    }

    /**
     * Get structured list of expired / invalid keys
     */
    public function getExpiredKeysListAttribute(): array
    {
        $raw = $this->gemini_expired_keys;
        if (empty($raw)) {
            return [];
        }
        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            return is_array($decoded) ? $decoded : [];
        }
        return is_array($raw) ? $raw : [];
    }

    /**
     * Mark a Gemini API Key as expired / invalid (not due to temporary rate limit)
     */
    public function markKeyExpired(string $key, string $errorReason = 'API Key Error'): void
    {
        $cleanKey = trim($key);
        if (empty($cleanKey)) {
            return;
        }

        $currentExpired = $this->expired_keys_list;
        $exists = false;
        foreach ($currentExpired as $item) {
            $itemKey = is_array($item) ? ($item['key'] ?? '') : $item;
            if ($itemKey === $cleanKey) {
                $exists = true;
                break;
            }
        }

        if (!$exists) {
            $currentExpired[] = [
                'key' => $cleanKey,
                'error' => $errorReason,
                'detected_at' => now()->translatedFormat('d M Y H:i:s') . ' WIB',
            ];
            $this->gemini_expired_keys = $currentExpired;
        }

        // Also remove from active gemini_keys pool so it is immediately excluded
        $keys = preg_split('/[\r\n\s]+/', (string) $this->gemini_keys);
        $keys = array_filter(array_map('trim', $keys), function ($k) use ($cleanKey) {
            return !empty($k) && $k !== $cleanKey;
        });
        $this->gemini_keys = implode("\n", array_values($keys));

        $this->save();
    }

    /**
     * Remove key from expired list (e.g. if user resolved or wants to restore)
     */
    public function removeExpiredKey(string $key): void
    {
        $cleanKey = trim($key);
        $currentExpired = $this->expired_keys_list;
        $filtered = array_values(array_filter($currentExpired, function ($item) use ($cleanKey) {
            $itemKey = is_array($item) ? ($item['key'] ?? '') : $item;
            return $itemKey !== $cleanKey;
        }));

        $this->gemini_expired_keys = $filtered;
        $this->save();
    }

    protected static function booted()
    {
        static::saved(function ($model) {
            // Dual sync to legacy tb_ai_setting
            if (Schema::hasTable('tb_ai_setting')) {
                $payload = [
                    'gemini_keys' => $model->gemini_keys,
                    'gemini_model' => $model->gemini_model,
                    'sumopod_key' => $model->sumopod_key,
                    'sumopod_model' => $model->sumopod_model,
                    'wa_api_key' => $model->wa_api_key,
                    'wa_device' => $model->wa_device,
                    'wa_template' => $model->wa_template,
                ];
                if (Schema::hasColumn('tb_ai_setting', 'gemini_expired_keys')) {
                    $payload['gemini_expired_keys'] = is_array($model->gemini_expired_keys) 
                        ? json_encode($model->gemini_expired_keys) 
                        : $model->gemini_expired_keys;
                }
                DB::table('tb_ai_setting')->updateOrInsert(
                    ['id' => 1],
                    $payload
                );
            }
        });
    }
}