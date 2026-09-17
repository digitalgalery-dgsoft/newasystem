<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        return array_values(array_filter(array_map('trim', explode("\n", $this->gemini_keys))));
    }
}