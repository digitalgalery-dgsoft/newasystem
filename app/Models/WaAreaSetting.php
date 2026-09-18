<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaAreaSetting extends Model
{
    use HasFactory;

    protected $table = 'tb_wa_area_setting';
    protected $primaryKey = 'area';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $guarded = [];

    protected $casts = [
        'updated_at' => 'datetime',
    ];
}
