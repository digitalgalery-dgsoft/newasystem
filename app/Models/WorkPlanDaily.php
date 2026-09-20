<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkPlanDaily extends Model
{
    use HasFactory;

    protected $table = 'tb_workplan';
    protected $primaryKey = 'kode';
    public $timestamps = false;

    protected $fillable = [
        'kode',
        'tanggal',
        'aktivitas',
        'divisi',
        'kendala',
        'user',
        'waktu',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'waktu' => 'datetime',
    ];
}
