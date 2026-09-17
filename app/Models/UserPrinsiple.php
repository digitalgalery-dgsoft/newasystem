<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPrinsiple extends Model
{
    use HasFactory;

    protected $table = 'user_prinsiples';

    protected $fillable = [
        'nama_lengkap',
        'jabatan',
        'prinsiple_id',
        'prinsiple',
        'area',
        'email',
        'no_wa',
        'katakunci',
        'status',
    ];

    public function principle(): BelongsTo
    {
        return $this->belongsTo(Principle::class, 'prinsiple_id');
    }

    public function getStatusBadgeAttribute(): array
    {
        return match(strtolower($this->status ?? '')) {
            'aktiv', 'active' => [
                'bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'dot' => 'bg-emerald-500',
                'label' => 'AKTIF',
            ],
            default => [
                'bg' => 'bg-slate-100 text-slate-600 border-slate-200',
                'dot' => 'bg-slate-400',
                'label' => strtoupper($this->status ?: 'NONAKTIF'),
            ]
        };
    }
}
