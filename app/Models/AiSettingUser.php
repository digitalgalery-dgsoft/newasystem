<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiSettingUser extends Model
{
    use HasFactory;

    protected $table = 'tb_ai_setting_user';
    protected $primaryKey = 'email';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $guarded = [];

    protected $casts = [
        'wa_use_pusat' => 'boolean',
    ];
}
