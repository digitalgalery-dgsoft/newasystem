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
     * Rule: Inhouse has login access by default unless revoked; RateCard only if granted (akses_login == true).
     */
    public function hasLoginAccess(): bool
    {
        if ($this->tipe_karyawan === 'Inhouse') {
            return $this->akses_login !== false && $this->akses_login !== 0 && $this->akses_login !== '0';
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
     * Hanya 5 Entitas Resmi Inhouse:
     * 1. PT ARINA MULTI KARYA (AMK)
     * 2. PT ALVA KARYA PERKASA (AKP)
     * 3. PT ANUGRAH TERPERCAYA KERJA (ATK)
     * 4. PT ABADI BERKAT ODELIA (ABO)
     * 5. PT ANUGRAH TALENTA BERKARYA (ATB)
     */
    public static function getEntityCodeFromPrinciple(?string $principleName): ?string
    {
        if (!$principleName) return null;
        $p = strtoupper(trim($principleName));
        
        // Buang teks dalam tanda kurung di akhir seperti (AMK), (AKP), (ATK), dll
        $clean = preg_replace('/\s*\([^)]*\)\s*$/', '', $p);
        // Buang prefiks legal PT / CV / TBK
        $clean = trim(preg_replace('/\b(PT\.?|CV\.?|TBK)\b/i', '', $clean));
        $clean = trim(preg_replace('/[^A-Z0-9\s]/', '', $clean));
        $clean = preg_replace('/\s+/', ' ', $clean);

        // 1. PT ARINA MULTI KARYA
        if ($clean === 'ARINA MULTI KARYA' || $clean === 'ARINA MULTIKARYA' || $clean === 'AMK' || $p === 'PT ARINA MULTI KARYA') {
            return 'AMK';
        }
        // 2. PT ALVA KARYA PERKASA
        if ($clean === 'ALVA KARYA PERKASA' || $clean === 'AKP' || $p === 'PT ALVA KARYA PERKASA') {
            return 'AKP';
        }
        // 3. PT ANUGRAH TERPERCAYA KERJA
        if ($clean === 'ANUGRAH TERPERCAYA KERJA' || $clean === 'ATK' || $p === 'PT ANUGRAH TERPERCAYA KERJA') {
            return 'ATK';
        }
        // 4. PT ABADI BERKAT ODELIA (atau PT ARINA BINTANG OETAMA / ABO)
        if ($clean === 'ABADI BERKAT ODELIA' || $clean === 'ARINA BINTANG OETAMA' || $clean === 'ARINA BINTANG OPERASIONAL' || $clean === 'ABO' || $p === 'PT ABADI BERKAT ODELIA' || $p === 'PT ARINA BINTANG OETAMA') {
            return 'ABO';
        }
        // 5. PT ANUGRAH TALENTA BERKARYA (atau PT ANUGRAH TRI BERKAH / ATB)
        if ($clean === 'ANUGRAH TALENTA BERKARYA' || $clean === 'ANUGRAH TRI BERKAH' || $clean === 'ATB' || $p === 'PT ANUGRAH TALENTA BERKARYA' || $p === 'PT ANUGRAH TRI BERKAH') {
            return 'ATB';
        }

        return null;
    }

    /**
     * Determine if an employee is Inhouse or RateCard based on principle name matching 5 entities.
     * Inhouse hanya terdiri dari 5:
     * - PT ARINA MULTI KARYA
     * - PT ALVA KARYA PERKASA
     * - PT ANUGRAH TERPERCAYA KERJA
     * - PT ABADI BERKAT ODELIA
     * - PT ANUGRAH TALENTA BERKARYA
     */
    public static function determineTipeKaryawan(?string $principleName): string
    {
        return self::isInhousePrinciple($principleName) ? 'Inhouse' : 'RateCard';
    }

    public static function isInhousePrinciple(?string $principleName): bool
    {
        return self::getEntityCodeFromPrinciple($principleName) !== null;
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
