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
        'odoo_id',
        'entity',
        'last_sync_at',
    ];

    protected $casts = [
        'tanggal_join' => 'date',
        'has_komponen' => 'boolean',
        'last_sync_at' => 'datetime',
        'odoo_id' => 'integer',
    ];

    /**
     * Boot model events: automatically compute Inhouse vs RateCard
     * based on whether principle matches any of 5 entities: AMK, AKP, ATK, ABO, ATB.
     */
    protected static function booted()
    {
        static::saving(function ($employee) {
            $employee->tipe_karyawan = self::determineTipeKaryawan($employee->prinsiple);
            if (empty($employee->entity)) {
                $ent = self::getEntityCodeFromPrinciple($employee->prinsiple);
                if ($ent) {
                    $employee->entity = $ent;
                }
            }
        });
    }

    /**
     * Resolve Entity Code from principle name.
     * Matches 5 entities: AMK, AKP, ATK, ABO, ATB.
     */
    public static function getEntityCodeFromPrinciple(?string $principleName): ?string
    {
        if (!$principleName) return null;
        $p = strtoupper(trim($principleName));
        $normalized = trim(preg_replace('/\b(PT\.?|CV\.?|TBK)\b/i', '', $p));
        $normalized = trim(preg_replace('/[^A-Z0-9\s]/', '', $normalized));
        $words = preg_split('/\s+/', $normalized);

        if (in_array($p, ['AMK', 'AKP', 'ATK', 'ABO', 'ATB'], true)) {
            return $p;
        }
        if (in_array($normalized, ['AMK', 'AKP', 'ATK', 'ABO', 'ATB'], true)) {
            return $normalized;
        }

        if (str_contains($p, 'ARINA MULTI') || str_contains($p, 'ARINA MULTIKARYA') || in_array('AMK', $words, true)) {
            return 'AMK';
        }
        if (str_contains($p, 'ALVA KARYA') || in_array('AKP', $words, true)) {
            return 'AKP';
        }
        if (str_contains($p, 'ANUGRAH TERPERCAYA') || in_array('ATK', $words, true)) {
            return 'ATK';
        }
        if (str_contains($p, 'ARINA BINTANG') || str_contains($p, 'ABADI BERKAT') || in_array('ABO', $words, true)) {
            return 'ABO';
        }
        if (str_contains($p, 'ANUGRAH TRI BERKAH') || in_array('ATB', $words, true)) {
            return 'ATB';
        }

        return null;
    }

    /**
     * Determine if an employee is Inhouse or RateCard based on principle name matching 5 entities.
     */
    public static function determineTipeKaryawan(?string $principleName): string
    {
        return self::getEntityCodeFromPrinciple($principleName) !== null ? 'Inhouse' : 'RateCard';
    }

    public function principle(): BelongsTo
    {
        return $this->belongsTo(Principle::class);
    }

    public function entityModel(): BelongsTo
    {
        return $this->belongsTo(OdooEntity::class, 'entity', 'code');
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

    public function getEffectiveEntityAttribute(): string
    {
        if (!empty($this->entity)) {
            return strtoupper($this->entity);
        }
        $fromP = self::getEntityCodeFromPrinciple($this->prinsiple);
        return $fromP ?: 'AMK';
    }

    public function getEntityBadgeAttribute(): array
    {
        $code = $this->effective_entity;
        return match($code) {
            'AMK' => [
                'bg' => 'bg-blue-50 text-blue-700 border-blue-200',
                'dot' => 'bg-blue-600',
                'label' => 'AMK',
            ],
            'AKP' => [
                'bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'dot' => 'bg-emerald-600',
                'label' => 'AKP',
            ],
            'ATK' => [
                'bg' => 'bg-purple-50 text-purple-700 border-purple-200',
                'dot' => 'bg-purple-600',
                'label' => 'ATK',
            ],
            'ABO' => [
                'bg' => 'bg-amber-50 text-amber-700 border-amber-200',
                'dot' => 'bg-amber-600',
                'label' => 'ABO',
            ],
            'ATB' => [
                'bg' => 'bg-cyan-50 text-cyan-700 border-cyan-200',
                'dot' => 'bg-cyan-600',
                'label' => 'ATB',
            ],
            default => [
                'bg' => 'bg-slate-100 text-slate-600 border-slate-200',
                'dot' => 'bg-slate-400',
                'label' => $code ?: '-',
            ]
        };
    }

    public function getStatusBadgeAttribute(): array
    {
        return match(strtolower($this->status ?? '')) {
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
                'label' => strtoupper($this->status ?: 'UNKNOWN'),
            ]
        };
    }
}
