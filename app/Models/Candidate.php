<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Candidate extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'birth_date' => 'date',
        'is_profile_complete' => 'boolean',
        'ai_analysis' => 'array',
        'expected_salary' => 'decimal:2',
        'last_salary' => 'decimal:2',
        'tes_ke' => 'integer',
        'odoo_synced_at' => 'datetime',
        'odoo_applicant_data' => 'array',
    ];

    protected static function booted()
    {
        static::created(function (Candidate $candidate) {
            if (empty($candidate->nik)) {
                return;
            }

            // Cek apakah ada record lain dengan NIK sama yang sudah memiliki hasil tes
            $donor = static::where('nik', $candidate->nik)
                ->where('id', '!=', $candidate->id)
                ->whereNotNull('tes_kepribadian')
                ->where('tes_kepribadian', '!=', '')
                ->where('tes_kepribadian', '!=', '-')
                ->where('tes_kepribadian', '!=', '00:00:00')
                ->orderByDesc('id')
                ->first();

            if ($donor) {
                $candidate->tes_kepribadian = $donor->tes_kepribadian;
                if (empty($candidate->tes_matematika) || $candidate->tes_matematika === '00:00:00' || $candidate->tes_matematika === '-') {
                    $candidate->tes_matematika = $donor->tes_matematika;
                    $candidate->tes_ke = $donor->tes_ke ?? 1;
                }
                if (empty($candidate->tes_komputer) || $candidate->tes_komputer === '00:00:00' || $candidate->tes_komputer === '-') {
                    $candidate->tes_komputer = $donor->tes_komputer;
                }
                if (empty($candidate->buktikomputer)) {
                    $candidate->buktikomputer = $donor->buktikomputer;
                }
                if (empty($candidate->signature_path)) {
                    $candidate->signature_path = $donor->signature_path;
                    $candidate->statement_agreed = $donor->statement_agreed;
                }
                $candidate->saveQuietly();

                $donorResults = TestResult::where('candidate_id', $donor->id)->get();
                foreach ($donorResults as $dr) {
                    TestResult::firstOrCreate(
                        ['candidate_id' => $candidate->id, 'test_type' => $dr->test_type],
                        [
                            'score' => $dr->score,
                            'duration_seconds' => $dr->duration_seconds,
                            'test_details' => $dr->test_details,
                        ]
                    );
                }

                $candidate->checkProfileCompleteness();
            }
        });
    }

    public function principle()
    {
        return $this->belongsTo(Principle::class);
    }

    public function getPrincipleAttribute()
    {
        if ($this->relationLoaded('principle') && $this->getRelation('principle') !== null) {
            return $this->getRelation('principle');
        }

        if (!empty($this->principle_id)) {
            $p = Principle::find($this->principle_id);
            if ($p) {
                return $p;
            }
        }

        $prinName = $this->attributes['principle'] ?? null;
        if (!empty($prinName)) {
            return (object) [
                'id' => $this->principle_id ?? 0,
                'name' => $prinName,
                'code' => null,
                'parent_company' => null,
            ];
        }

        return null;
    }

    public function approverPrinsiple()
    {
        return $this->belongsTo(UserPrinsiple::class, 'idprinsiple');
    }

    public function recruiter()
    {
        return $this->belongsTo(User::class, 'recruiter_id');
    }

    public function workExperiences()
    {
        return $this->hasMany(WorkExperience::class);
    }

    public function interviewAssessment()
    {
        return $this->hasOne(InterviewAssessment::class);
    }

    public function principleApprovals()
    {
        return $this->hasMany(PrincipleApproval::class);
    }

    public function testResults()
    {
        return $this->hasMany(TestResult::class);
    }

    
    public function inhouseApprovals()
    {
        return $this->hasMany(InhouseApproval::class)->orderBy('id', 'desc');
    }

    public function getUserAsNameAttribute(): string
    {
        return $this->user_display_name;
    }

    public static function getLampiranUrl(?string $file, ?string $customBaseUrl = null): ?string
    {
        if (empty($file) || trim($file) === '-') {
            return null;
        }
        $file = trim($file);
        $baseName = basename($file);

        // Resolusi baseUrl yang handal (termasuk saat dipanggil dari CLI)
        $baseUrl = $customBaseUrl;
        if (empty($baseUrl)) {
            if (app()->runningInConsole()) {
                $baseUrl = 'https://new.asystem.co.id';
            } else {
                try {
                    $baseUrl = request()->getSchemeAndHttpHost();
                } catch (\Throwable $e) {
                    $baseUrl = 'https://new.asystem.co.id';
                }
                if (empty($baseUrl) || str_contains($baseUrl, 'localhost') || str_contains($baseUrl, '127.0.0.1')) {
                    $baseUrl = 'https://new.asystem.co.id';
                }
            }
        }
        $baseUrl = rtrim($baseUrl, '/');

        if (file_exists(public_path('lampiran/' . $baseName))) {
            return $baseUrl . '/lampiran/' . rawurlencode($baseName);
        }
        if (file_exists(public_path('storage/' . $baseName))) {
            return $baseUrl . '/storage/' . rawurlencode($baseName);
        }
        if (file_exists(public_path($file))) {
            return $baseUrl . '/' . ltrim($file, '/');
        }
        return 'https://asystem.co.id/interview/lampiran/' . rawurlencode($baseName);
    }

    public function hasCv(): bool
    {
        $cv = trim($this->cv_path ?? '');
        return !empty($cv) && $cv !== '-';
    }

    public function getPhotoUrlAttribute(): string
    {
        $file = trim($this->photo_path ?? '');
        if (empty($file)) {
            return 'https://ui-avatars.com/api/?name=' . urlencode($this->full_name ?? 'User') . '&background=0F52BA&color=fff&size=256';
        }
        return self::getLampiranUrl($file) ?? 'https://ui-avatars.com/api/?name=' . urlencode($this->full_name ?? 'User') . '&background=0F52BA&color=fff&size=256';
    }

    public function getCvUrlAttribute(): ?string
    {
        return self::getLampiranUrl($this->cv_path);
    }

    public function getApprovalProofUrlAttribute(): ?string
    {
        $val = trim($this->ttd_prinsiple ?? '');
        if (empty($val)) {
            $appr = $this->principleApprovals->first();
            if ($appr && !empty($appr->signature_path)) {
                $val = trim($appr->signature_path);
            }
        }
        if (empty($val)) {
            return null;
        }

        if (str_starts_with($val, 'approvals/') || str_starts_with($val, 'storage/approvals/')) {
            $base = basename($val);
            if (file_exists(public_path('storage/approvals/' . $base))) {
                return asset('storage/approvals/' . $base);
            }
        }

        $baseName = basename($val);

        if (str_starts_with($baseName, 'ttd_')) {
            if (file_exists(public_path('prinsiple/ttdfileprinsiple/' . $baseName))) {
                return asset('prinsiple/ttdfileprinsiple/' . $baseName);
            }
            return route('prinsiple.ttd.show', $baseName);
        }

        if (file_exists(public_path('approval/' . $baseName))) {
            return asset('approval/' . $baseName);
        }
        if (file_exists(public_path('storage/approvals/' . $baseName))) {
            return asset('storage/approvals/' . $baseName);
        }
        if (file_exists(public_path('lampiran/' . $baseName))) {
            return asset('lampiran/' . $baseName);
        }

        return route('approval.show', $baseName);
    }

    public function getApprovalLegacyUrlAttribute(): ?string
    {
        $val = trim($this->ttd_prinsiple ?? '');
        if (empty($val)) {
            $appr = $this->principleApprovals->first();
            if ($appr && !empty($appr->signature_path)) {
                $val = trim($appr->signature_path);
            }
        }
        if (empty($val)) {
            return null;
        }

        $baseName = basename($val);
        if (str_starts_with($baseName, 'ttd_')) {
            return 'https://asystem.co.id/v3/prinsiple/ttdfileprinsiple/' . rawurlencode($baseName);
        }

        return 'https://asystem.co.id/v3/approval/' . rawurlencode($baseName);
    }

    public function getApplicantsNameAttribute()
    {
        return $this->full_name;
    }

    public function setApplicantsNameAttribute($value)
    {
        $this->attributes['full_name'] = $value;
    }

    public function getNoKtpAttribute()
    {
        return $this->nik;
    }

    public function setNoKtpAttribute($value)
    {
        $this->attributes['nik'] = $value;
    }

    public function getTanggalLahirAttribute()
    {
        return $this->birth_date;
    }

    public function getPendidikanTerakhirAttribute()
    {
        return $this->education;
    }

    public function getGenderAttribute($value)
    {
        if (!empty($value)) {
            return $value;
        }
        $cleanNik = preg_replace('/\D/', '', $this->nik ?? '');
        if (strlen($cleanNik) >= 8) {
            $day = (int) substr($cleanNik, 6, 2);
            if ($day > 40 && $day <= 71) {
                return 'Perempuan';
            } elseif ($day >= 1 && $day <= 31) {
                return 'Laki-laki';
            }
        }
        return 'Laki-laki';
    }

    public function getJenisKelaminAttribute()
    {
        return $this->gender;
    }

    public function getAlamatKtpAttribute()
    {
        return $this->address_ktp;
    }

    public function getPropinsiDomisiliAttribute()
    {
        return $this->province_domicile;
    }

    public function getKotaDomisiliAttribute()
    {
        return $this->city_domicile ?? $this->secondary_city;
    }

    public function getInfoLowonganAttribute()
    {
        return $this->info_lowongan ?? $this->info ?? $this->source_type;
    }

    public function getRingkasanPengalamanAttribute()
    {
        return $this->experience_summary;
    }

    public function getMobileAttribute()
    {
        return $this->phone ?? $this->whatsapp;
    }

    public function getFotoprofilAttribute()
    {
        return $this->photo_path;
    }

    public function getFilecvAttribute()
    {
        return $this->cv_path;
    }

    public function getWaktukirimAttribute()
    {
        return $this->created_at;
    }

    public function getTanggalAttribute()
    {
        return $this->created_at ? $this->created_at->format('Y-m-d') : null;
    }

    public function getAgeAttribute(): int
    {
        return $this->birth_date ? $this->birth_date->age : 0;
    }

    public function getFormattedBirthDateAttribute(): string
    {
        return $this->birth_date ? $this->birth_date->translatedFormat('d M Y') : '-';
    }

    public function getPsikotesScoreAttribute()
    {
        return $this->testResults->firstWhere('test_type', 'psychology');
    }

    public function getMathScoreAttribute()
    {
        return $this->testResults->firstWhere('test_type', 'math');
    }

    public function getComputerScoreAttribute()
    {
        return $this->testResults->firstWhere('test_type', 'computer');
    }

    public function getCleanWhatsappAttribute(): string
    {
        $num = preg_replace('/[^0-9]/', '', $this->phone ?? $this->whatsapp ?? '');
        if (str_starts_with($num, '0')) {
            $num = '62' . substr($num, 1);
        }
        return $num;
    }

    public function getAiDataAttribute(): array
    {
        if (!$this->hasCv()) {
            return [];
        }
        if (!empty($this->ai_cv_analysis)) {
            $data = json_decode($this->ai_cv_analysis, true);
            if (!is_array($data) && is_string($this->ai_cv_analysis)) {
                $data = json_decode(stripslashes($this->ai_cv_analysis), true);
            }
            if (is_array($data)) return $data;
        }
        if (is_array($this->ai_analysis)) {
            return $this->ai_analysis;
        }
        return [];
    }

    public function getAiScoreColorHexAttribute(): string
    {
        if (!$this->hasCv() || empty($this->ai_score)) {
            return '#94a3b8'; // slate-400
        }
        $score = intval($this->ai_score);
        if ($score >= 85) return '#059669'; // emerald-600
        if ($score >= 60) return '#d97706'; // amber-600
        return '#e11d48'; // rose-600
    }

    public function getAiBadgeClassAttribute(): string
    {
        if (!$this->hasCv() || empty($this->ai_score)) {
            return 'bg-slate-50 text-slate-500 border-slate-200';
        }
        $score = intval($this->ai_score);
        if ($score >= 85) return 'bg-emerald-50 text-emerald-700 border-emerald-200';
        if ($score >= 60) return 'bg-amber-50 text-amber-700 border-amber-200';
        return 'bg-rose-50 text-rose-700 border-rose-200';
    }

    public function candidateLogs()
    {
        return $this->hasMany(CandidateLog::class)->orderBy('created_at', 'desc');
    }

    /**
     * Memeriksa kelengkapan profil kandidat sesuai standar interview ESA Groups.
     */
    public function checkProfileCompleteness(): bool
    {
        $hasRequiredFields = !empty($this->full_name) &&
            !empty($this->nik) &&
            !empty($this->birth_place) &&
            !empty($this->birth_date) &&
            !empty($this->address_ktp) &&
            !empty($this->address_domicile) &&
            !empty($this->education) &&
            (!empty($this->phone) || !empty($this->whatsapp)) &&
            !empty($this->religion) &&
            !empty($this->height) &&
            !empty($this->weight) &&
            !empty($this->marital_status) &&
            !empty($this->mother_name) &&
            !empty($this->emergency_contact_name) &&
            !empty($this->emergency_contact_phone) &&
            !empty($this->emergency_contact_relation) &&
            !empty($this->bank_name) &&
            !empty($this->bank_account_number) &&
            !empty($this->bank_account_holder) &&
            !empty($this->work_motivation) &&
            !empty($this->strengths) &&
            !empty($this->weaknesses) &&
            !empty($this->current_activity) &&
            !empty($this->vehicle) &&
            !empty($this->driving_license) &&
            !empty($this->signature_path) &&
            ($this->statement_agreed == 1 || $this->statement_agreed === true);

        // Jika pengalaman kerja wajib ada minimal 1
        $hasExperience = $this->workExperiences()->count() > 0;

        $isComplete = $hasRequiredFields && $hasExperience;

        if ($this->is_profile_complete !== $isComplete) {
            $this->updateQuietly(['is_profile_complete' => $isComplete]);
        }

        return $isComplete;
    }

    public function getIsPsikotesDoneAttribute(): bool
    {
        if (!empty($this->tes_kepribadian) && $this->tes_kepribadian !== '00:00:00' && $this->tes_kepribadian !== '-') {
            return true;
        }
        if ($this->relationLoaded('testResults')) {
            return $this->testResults->firstWhere('test_type', 'psychology') !== null;
        }
        return $this->testResults()->where('test_type', 'psychology')->exists();
    }

    public function getIsMathDoneAttribute(): bool
    {
        if (empty($this->tes_matematika) || $this->tes_matematika === '00:00:00' || $this->tes_matematika === '-') {
            return false;
        }
        return true;
    }

    public function getIsKomputerDoneAttribute(): bool
    {
        if (!empty($this->tes_komputer) && $this->tes_komputer !== '00:00:00' && $this->tes_komputer !== '-') {
            return true;
        }
        if ($this->relationLoaded('testResults')) {
            return $this->testResults->firstWhere('test_type', 'computer') !== null;
        }
        return $this->testResults()->where('test_type', 'computer')->exists();
    }

    public function getAllTestsCompletedAttribute(): bool
    {
        $requiresComputer = $this->is_komputer ?? true;
        if ($requiresComputer) {
            return $this->is_psikotes_done && $this->is_math_done && $this->is_komputer_done;
        }
        return $this->is_psikotes_done && $this->is_math_done;
    }

    public function getUserDisplayNameAttribute(): string
    {
        $useras = trim($this->useras ?? '');
        if (empty($useras)) {
            if ($this->recruiter && !empty($this->recruiter->email)) {
                $useras = trim($this->recruiter->email);
            } else {
                return '-';
            }
        }

        if (str_contains($useras, '@')) {
            $cleanEmail = strtolower($useras);

            // 1. Prioritas Utama: Ambil Nama Lengkap & Jabatan dari Data Karyawan (Employee) berdasarkan email
            $employee = Employee::whereRaw('LOWER(TRIM(email)) = ?', [$cleanEmail])
                ->whereNotNull('nama_karyawan')
                ->where('nama_karyawan', '!=', '')
                ->orderByRaw("CASE WHEN status = 'Aktiv' THEN 0 ELSE 1 END")
                ->orderBy('id', 'desc')
                ->first(['nama_karyawan', 'jabatan']);

            if ($employee && !empty($employee->nama_karyawan)) {
                $empName = trim($employee->nama_karyawan);
                $empJabatan = trim($employee->jabatan ?? '');
                return !empty($empJabatan) ? "{$empName} ({$empJabatan})" : $empName;
            }

            // 2. Jika tidak ditemukan di Data Karyawan:
            // JANGAN fallback ke Administrator ESA / Administrator HR
            // Tampilkan apa adanya email yang tercantum
            return $useras;
        }

        if (stripos($useras, 'administrator') !== false || stripos($useras, 'admin esa') !== false) {
            return '-';
        }

        return ucwords(strtolower($useras));
    }

    public function getUserPrinsipleOptionsAttribute()
    {
        return \App\Http\Controllers\InterviewController::getUserPrinsipleOptions($this);
    }

    public function hasOdooRecruitment(): bool
    {
        return !empty($this->odoo_applicant_id) || !empty($this->odoo_stage_name);
    }

    public function getOdooBadgeInfoAttribute(): array
    {
        $stage = trim($this->odoo_stage_name ?? '');
        if (empty($stage)) {
            return [
                'label' => 'Belum di Odoo',
                'class' => 'bg-slate-100 text-slate-500 border-slate-200',
                'icon'  => 'fa-regular fa-clock',
                'stage' => null,
            ];
        }

        $lower = strtolower($stage);
        if (str_contains($lower, 'joined')) {
            return [
                'label' => 'Joined',
                'class' => 'bg-emerald-50 text-emerald-700 border-emerald-300 font-bold',
                'icon'  => 'fa-solid fa-circle-check',
                'stage' => $stage,
            ];
        }
        if (str_contains($lower, 'pkwt')) {
            return [
                'label' => $stage,
                'class' => 'bg-amber-50 text-amber-800 border-amber-300 font-bold',
                'icon'  => 'fa-solid fa-file-signature',
                'stage' => $stage,
            ];
        }
        if (str_contains($lower, 'learning') || str_contains($lower, 'elearning')) {
            return [
                'label' => 'E-Learning',
                'class' => 'bg-teal-50 text-teal-700 border-teal-300 font-bold',
                'icon'  => 'fa-solid fa-graduation-cap',
                'stage' => $stage,
            ];
        }
        if (str_contains($lower, 'principal')) {
            return [
                'label' => 'Principal',
                'class' => 'bg-sky-50 text-sky-700 border-sky-300 font-bold',
                'icon'  => 'fa-solid fa-building-user',
                'stage' => $stage,
            ];
        }
        if (str_contains($lower, 'interview')) {
            return [
                'label' => $stage,
                'class' => 'bg-purple-50 text-purple-700 border-purple-300 font-bold',
                'icon'  => 'fa-solid fa-user-tie',
                'stage' => $stage,
            ];
        }
        if (str_contains($lower, 'pelamar') || str_contains($lower, 'initial') || str_contains($lower, 'kualifikasi')) {
            return [
                'label' => 'Data Pelamar',
                'class' => 'bg-blue-50 text-blue-700 border-blue-200 font-medium',
                'icon'  => 'fa-solid fa-inbox',
                'stage' => $stage,
            ];
        }
        if (str_contains($lower, 'refuse') || str_contains($lower, 'tolak') || str_contains($lower, 'arsip')) {
            return [
                'label' => 'Ditolak / Arsip',
                'class' => 'bg-rose-50 text-rose-700 border-rose-200 font-medium',
                'icon'  => 'fa-solid fa-ban',
                'stage' => $stage,
            ];
        }

        return [
            'label' => $stage,
            'class' => 'bg-indigo-50 text-indigo-700 border-indigo-200 font-medium',
            'icon'  => 'fa-solid fa-arrow-right-to-bracket',
            'stage' => $stage,
        ];
    }
}