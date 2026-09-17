<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'nik',
        'nip',
        'nama_karyawan',
        'email',
        'telepon',
        'tanggal_join',
        'area',
        'jabatan',
        'divisi',
        'principle_id',
        'prinsiple',
        'pimpinan',
        'jabatan_pimpinan',
        'level',
        'tipe_karyawan',
        'status',
        'has_komponen',
        'foto',
    ];

    protected $casts = [
        'tanggal_join' => 'date',
        'has_komponen' => 'boolean',
    ];

    public function principle(): BelongsTo
    {
        return $this->belongsTo(Principle::class);
    }

    public function getFormattedJoinDateAttribute(): string
    {
        return $this->tanggal_join ? $this->tanggal_join->format('d M Y') : '-';
    }

    public function getFiveYearsDateAttribute(): string
    {
        return $this->tanggal_join ? $this->tanggal_join->copy()->addYears(5)->format('d M Y') : '-';
    }

    public function getYearsOfServiceAttribute(): string
    {
        if (!$this->tanggal_join) return '-';
        $diff = $this->tanggal_join->diff(Carbon::now());
        if ($diff->y > 0) {
            return $diff->y . ' Thn ' . $diff->m . ' Bln';
        }
        return $diff->m . ' Bln ' . $diff->d . ' Hari';
    }

    public function getStatusBadgeAttribute(): array
    {
        return match(strtolower($this->status)) {
            'aktiv', 'active' => [
                'bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'dot' => 'bg-emerald-500',
                'label' => 'AKTIF',
            ],
            'resign' => [
                'bg' => 'bg-rose-50 text-rose-700 border-rose-200',
                'dot' => 'bg-rose-500',
                'label' => 'RESIGN',
            ],
            'review' => [
                'bg' => 'bg-amber-50 text-amber-700 border-amber-200',
                'dot' => 'bg-amber-500',
                'label' => 'REVIEW',
            ],
            default => [
                'bg' => 'bg-slate-100 text-slate-700 border-slate-200',
                'dot' => 'bg-slate-400',
                'label' => strtoupper($this->status),
            ]
        };
    }
}