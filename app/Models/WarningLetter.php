<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WarningLetter extends Model
{
    use HasFactory;

    protected $table = 'warning_letters';

    protected $fillable = [
        'nomor_surat',
        'nomor_urut',
        'employee_id',
        'nip',
        'nik',
        'nama_karyawan',
        'jabatan',
        'area',
        'singkatan_area',
        'prinsiple',
        'entity',
        'tingkat_sp',
        'tingkat_sp_diajukan',
        'tanggal_surat',
        'tanggal_expired',
        'pasal_pelanggaran',
        'tindakan_perbaikan',
        'status',
        'posisi_approval',
        'file_pendukung',
        'file_ttd_karyawan',
        'tgl_upload_ttd',
        'alasan_penolakan',
        'created_by',
        'pimpinan_pembuat',
        'head_approved_by',
        'head_approved_at',
        'head_notes',
        'file_pdf_surat',
        'approved_by',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_surat' => 'date',
            'tanggal_expired' => 'date',
            'file_pendukung' => 'array',
            'tgl_upload_ttd' => 'datetime',
            'head_approved_at' => 'datetime',
            'approved_at' => 'datetime',
            'nomor_urut' => 'integer',
        ];
    }

    // Relationships
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function headApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'head_approved_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function violations(): HasMany
    {
        return $this->hasMany(WarningLetterViolation::class, 'warning_letter_id');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(WarningLetterApproval::class, 'warning_letter_id')->latest();
    }

    // Accessors & Helper Methods
    public function getTingkatSpLabelAttribute(): string
    {
        return match (strtolower($this->tingkat_sp)) {
            'sp1' => 'Surat Peringatan I (Satu)',
            'sp2' => 'Surat Peringatan II (Dua)',
            'sp3' => 'Surat Peringatan III (Tiga / Terakhir)',
            default => strtoupper($this->tingkat_sp),
        };
    }

    public function getTingkatSpCodeTextAttribute(): string
    {
        return match (strtolower($this->tingkat_sp)) {
            'sp1' => 'SP 1',
            'sp2' => 'SP 2',
            'sp3' => 'SP 3',
            default => strtoupper($this->tingkat_sp),
        };
    }

    public function getTingkatSpRomanAttribute(): string
    {
        return match (strtolower($this->tingkat_sp)) {
            'sp1' => 'SPI',
            'sp2' => 'SPII',
            'sp3' => 'SPIII',
            default => 'SPI',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->status === 'approved' && $this->tanggal_expired && Carbon::now()->greaterThan($this->tanggal_expired)) {
            return 'Kedaluwarsa (Expired)';
        }

        return match ($this->status) {
            'draft' => 'Draft',
            'review_head' => 'Menunggu Review Pimpinan',
            'review_hrd' => 'Menunggu Approval HRD',
            'approved' => 'Disetujui (Aktif)',
            'rejected' => 'Ditolak',
            'cancelled' => 'Dibatalkan',
            'expired' => 'Kedaluwarsa (Expired)',
            default => ucfirst($this->status),
        };
    }

    public function getStatusBadgeAttribute(): array
    {
        if ($this->status === 'approved' && $this->tanggal_expired && Carbon::now()->greaterThan($this->tanggal_expired)) {
            return [
                'bg' => 'bg-slate-100',
                'text' => 'text-slate-700',
                'border' => 'border-slate-300',
                'label' => 'Masa Berlaku Habis (Expired)',
                'icon' => 'fa-clock-rotate-left',
            ];
        }

        return match ($this->status) {
            'draft' => [
                'bg' => 'bg-slate-100',
                'text' => 'text-slate-700',
                'border' => 'border-slate-200',
                'label' => 'Draft',
                'icon' => 'fa-file-lines',
            ],
            'review_head' => [
                'bg' => 'bg-blue-50',
                'text' => 'text-blue-700',
                'border' => 'border-blue-200',
                'label' => 'Review Pimpinan',
                'icon' => 'fa-user-tie',
            ],
            'review_hrd' => [
                'bg' => 'bg-amber-50',
                'text' => 'text-amber-700',
                'border' => 'border-amber-200',
                'label' => 'Review HRD',
                'icon' => 'fa-clock',
            ],
            'approved' => [
                'bg' => 'bg-emerald-50',
                'text' => 'text-emerald-700',
                'border' => 'border-emerald-200',
                'label' => 'Aktif (Disetujui)',
                'icon' => 'fa-circle-check',
            ],
            'rejected' => [
                'bg' => 'bg-rose-50',
                'text' => 'text-rose-700',
                'border' => 'border-rose-200',
                'label' => 'Ditolak',
                'icon' => 'fa-circle-xmark',
            ],
            'cancelled' => [
                'bg' => 'bg-rose-100',
                'text' => 'text-rose-800',
                'border' => 'border-rose-300',
                'label' => 'Dibatalkan',
                'icon' => 'fa-ban',
            ],
            'expired' => [
                'bg' => 'bg-slate-100',
                'text' => 'text-slate-600',
                'border' => 'border-slate-300',
                'label' => 'Expired',
                'icon' => 'fa-clock-rotate-left',
            ],
            default => [
                'bg' => 'bg-slate-50',
                'text' => 'text-slate-700',
                'border' => 'border-slate-200',
                'label' => ucfirst($this->status),
                'icon' => 'fa-info',
            ],
        };
    }

    public function getHasSignedDocAttribute(): bool
    {
        return !empty($this->file_ttd_karyawan);
    }

    public function getSignedDocStatusBadgeAttribute(): array
    {
        if ($this->status !== 'approved') {
            return [
                'bg' => 'bg-slate-100',
                'text' => 'text-slate-500',
                'label' => 'Belum Terbit',
                'icon' => 'fa-minus',
            ];
        }

        if ($this->has_signed_doc) {
            return [
                'bg' => 'bg-emerald-100',
                'text' => 'text-emerald-800',
                'border' => 'border-emerald-300',
                'label' => 'Lengkap (Scan TTD Diunggah)',
                'icon' => 'fa-file-circle-check',
                'color' => 'green',
            ];
        }

        return [
            'bg' => 'bg-rose-100',
            'text' => 'text-rose-800',
            'border' => 'border-rose-300',
            'label' => 'Belum Diunggah (Scan TTD)',
            'icon' => 'fa-triangle-exclamation',
            'color' => 'red',
        ];
    }

    public function getEntityFullNameAttribute(): string
    {
        return match (strtoupper($this->entity)) {
            'AMK' => 'PT Arina Multikarya',
            'AKP' => 'PT Alva Karya Perkasa',
            'ATK' => 'PT Anugrah Terpercaya Kerja',
            'ABO' => 'PT Arina Bintang Oetama',
            'ATB' => 'PT Anugrah Tri Berkah',
            default => 'ESA Groups',
        };
    }

    /**
     * Cek apakah user berhak memberikan persetujuan Tahap 1 (Pimpinan Pembuat SP)
     */
    public function canUserApproveHead(?User $user): bool
    {
        if (!$user || $this->status !== 'review_head') {
            return false;
        }

        // Admin & Super Admin selalu memiliki otoritas override
        if ($user->isAdmin() || in_array($user->role, ['admin', 'superadmin'])) {
            return true;
        }

        $userName = strtolower(trim($user->name ?? ''));
        $pimpinan = strtolower(trim($this->pimpinan_pembuat ?? ''));

        // 1. Kecocokan eksplisit dengan nama pimpinan pembuat
        if (!empty($pimpinan) && !empty($userName)) {
            if ($pimpinan === $userName || str_contains($pimpinan, $userName) || str_contains($userName, $pimpinan)) {
                return true;
            }
        }

        // 2. Cek apakah user terdaftar sebagai pimpinan dari user pembuat di tabel employees
        if ($this->creator) {
            $creatorEmp = Employee::where('email', $this->creator->email)
                ->orWhere('nik', $this->creator->nik)
                ->first();

            if ($creatorEmp && !empty($creatorEmp->pimpinan)) {
                $empPimpinan = strtolower(trim($creatorEmp->pimpinan));
                if ($empPimpinan === $userName || str_contains($empPimpinan, $userName) || str_contains($userName, $empPimpinan)) {
                    return true;
                }
            }
        }

        // 3. User berstatus Head/Supervisor/Manager di area/cabang pembuat
        if ($user->isHead()) {
            $creatorArea = strtoupper(trim($this->creator?->area ?? $this->area ?? ''));
            $userAreas = array_map('strtoupper', $user->getEffectiveAreas());
            if (!empty($creatorArea) && in_array($creatorArea, $userAreas, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Cek apakah user berhak memberikan persetujuan Tahap 2 (HRD Approval)
     * Menggunakan aturan dinamis ApprovalWorkflow jika tersedia, atau fallback ke role HRD
     */
    public function canUserApproveHrd(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        // HRD berwenang menyetujui surat baik yang berada di tahap review_hrd maupun review_head (override)
        if (!in_array($this->status, ['review_hrd', 'review_head'])) {
            return false;
        }

        // Admin & Super Admin selalu memiliki akses approval
        if ($user->isAdmin() || in_array($user->role, ['admin', 'superadmin'])) {
            return true;
        }

        // Evaluasi konfigurasi dinamis ApprovalWorkflow modul 'surat_peringatan'
        $workflow = ApprovalWorkflow::with(['steps.stepUsers.user'])
            ->where('module', 'surat_peringatan')
            ->where('is_active', true)
            ->first();

        if ($workflow && $workflow->steps->isNotEmpty()) {
            // Cari step approval HRD (biasanya order >= 2 atau approver_type = 'user')
            $hrdStep = $workflow->steps->first(fn($s) => $s->step_order >= 2 || $s->approver_type === 'user');

            if ($hrdStep) {
                // a. Evaluasi aturan dinamis (Area + Prinsiple/Entitas)
                $rule = $hrdStep->getMatchingRuleForWarningLetter($this);
                if ($rule) {
                    $userIds = array_map('intval', $rule['user_ids'] ?? []);
                    if (in_array((int)$user->id, $userIds, true)) {
                        return true;
                    }
                }

                // b. Evaluasi direct step users
                $assignedIds = $hrdStep->stepUsers->pluck('user_id')->map(fn($id) => (int)$id)->toArray();
                if (in_array((int)$user->id, $assignedIds, true)) {
                    return true;
                }
            }
        }

        // Fallback default jika alur dinamis belum dikonfigurasi: seluruh akun berkewenangan HRD
        return $user->isHrd();
    }

    /**
     * Cek apakah user berhak membatalkan (cancel) Surat Peringatan kapanpun
     * Bagian HRD / Admin berhak membatalkan data kapanpun, baik masih review ataupun sudah selesai
     */
    public function canUserCancel(?User $user): bool
    {
        if (!$user || $this->status === 'cancelled') {
            return false;
        }

        return $user->isAdmin() || $user->isHrd() || in_array($user->role, ['admin', 'superadmin']);
    }
}

