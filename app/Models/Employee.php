<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'nik',
        'nip',
        'nama_karyawan',
        'email',
        'telepon',
        'tanggal_lahir',
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
        'akses_login',
        'password',
        'status',
        'has_komponen',
        'foto',
        'odoo_id',
        'entity',
        'last_sync_at',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_join' => 'date',
        'akses_login' => 'boolean',
        'has_komponen' => 'boolean',
        'last_sync_at' => 'datetime',
        'odoo_id' => 'integer',
    ];

    protected $appends = [
        'default_password',
        'formatted_join_date',
        'formatted_birth_date',
        'years_of_service',
        'five_years_date',
        'entity_badge',
        'status_badge',
        'login_access_badge',
    ];

    /**
     * Boot model events:
     * - automatically compute Inhouse vs RateCard based on principle matching 5 entities.
     * - Inhouse employees are automatically granted login access.
     * - Hash default password from birth date (ddmmyyyy) if password is empty.
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

            // Inhouse employees always have login access
            if ($employee->tipe_karyawan === 'Inhouse') {
                $employee->akses_login = true;
            }

            // Auto-extract birthdate from NIK if empty
            if (empty($employee->tanggal_lahir) && !empty($employee->nik)) {
                $employee->tanggal_lahir = self::extractBirthDateFromNik($employee->nik);
            }

            // If password is not set, initialize default password (ddmmyyyy)
            if (empty($employee->password)) {
                $defPwd = $employee->default_password;
                $employee->password = Hash::make($defPwd);
            }
        });
    }

    /**
     * Extract birth date (YYYY-MM-DD) from 16-digit Indonesian NIK.
     */
    public static function extractBirthDateFromNik(?string $nik): string
    {
        $clean = preg_replace('/\D/', '', (string)$nik);
        if (strlen($clean) >= 12) {
            $d = (int)substr($clean, 6, 2);
            if ($d > 40) {
                $d -= 40; // female day offset
            }
            $m = (int)substr($clean, 8, 2);
            $yShort = (int)substr($clean, 10, 2);
            $y = ($yShort > 30) ? (1900 + $yShort) : (2000 + $yShort);
            if ($d >= 1 && $d <= 31 && $m >= 1 && $m <= 12) {
                return sprintf('%04d-%02d-%02d', $y, $m, $d);
            }
        }
        return '1998-01-01';
    }

    /**
     * Check if employee has system login access.
     * Rule: Inhouse always has login access; RateCard only if granted (akses_login == true).
     */
    public function hasLoginAccess(): bool
    {
        if ($this->tipe_karyawan === 'Inhouse') {
            return true;
        }
        return (bool) $this->akses_login;
    }

    /**
     * Default password formatted as ddmmyyyy of birth date.
     */
    public function getDefaultPasswordAttribute(): string
    {
        if ($this->tanggal_lahir) {
            return Carbon::parse($this->tanggal_lahir)->format('dmY');
        }
        $nikBdate = self::extractBirthDateFromNik($this->nik);
        return Carbon::parse($nikBdate)->format('dmY');
    }

    /**
     * Badge array for login access status.
     */
    public function getLoginAccessBadgeAttribute(): array
    {
        if ($this->hasLoginAccess()) {
            return [
                'status' => true,
                'label' => $this->tipe_karyawan === 'Inhouse' ? 'Aktif (Inhouse)' : 'Aktif (Diberi Izin)',
                'bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'dot' => 'bg-emerald-500',
                'icon' => 'fa-lock-open',
            ];
        }
        return [
            'status' => false,
            'label' => 'Terkunci (RateCard)',
            'bg' => 'bg-slate-100 text-slate-500 border-slate-200',
            'dot' => 'bg-slate-400',
            'icon' => 'fa-lock',
        ];
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

    public function getFormattedBirthDateAttribute(): string
    {
        return $this->tanggal_lahir ? $this->tanggal_lahir->format('d M Y') : '-';
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
