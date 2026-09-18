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
    ];

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

    public static function getLampiranUrl(?string $file): ?string
    {
        if (empty($file)) {
            return null;
        }
        $baseName = basename(trim($file));
        if (file_exists(public_path('lampiran/' . $baseName))) {
            return asset('lampiran/' . $baseName);
        }
        if (file_exists(public_path('storage/' . $baseName))) {
            return asset('storage/' . $baseName);
        }
        if (file_exists(public_path($file))) {
            return asset($file);
        }
        return 'https://asystem.co.id/interview/lampiran/' . $baseName;
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
            if (file_exists(public_path('approval/' . $baseName))) {
                return asset('approval/' . $baseName);
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
        $score = intval($this->ai_score ?? 0);
        if ($score >= 85) return '#059669'; // emerald-600
        if ($score >= 60) return '#d97706'; // amber-600
        return '#e11d48'; // rose-600
    }

    public function getAiBadgeClassAttribute(): string
    {
        $score = intval($this->ai_score ?? 0);
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
        if (!empty($this->tes_matematika) && $this->tes_matematika !== '00:00:00' && $this->tes_matematika !== '-') {
            return true;
        }
        if ($this->relationLoaded('testResults')) {
            return $this->testResults->firstWhere('test_type', 'math') !== null;
        }
        return $this->testResults()->where('test_type', 'math')->exists();
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
            return $this->recruiter->name ?? 'Administrator HR';
        }

        if (str_contains($useras, '@')) {
            $userObj = User::whereRaw('LOWER(email) = ?', [strtolower($useras)])->first();
            if ($userObj) {
                return $userObj->name;
            }

            $employee = Employee::whereRaw('LOWER(email) = ?', [strtolower($useras)])
                ->where('tipe_karyawan', 'Inhouse')
                ->where('status', 'Aktiv')
                ->first();

            if ($employee) {
                return $employee->nama_karyawan;
            }

            $anyEmployee = Employee::whereRaw('LOWER(email) = ?', [strtolower($useras)])
                ->where('status', 'Aktiv')
                ->first();

            if ($anyEmployee) {
                return $anyEmployee->nama_karyawan;
            }

            $parts = explode('@', $useras)[0];
            $name = preg_replace('/[0-9_.-]+/', ' ', $parts);
            return ucwords(trim($name)) ?: $useras;
        }

        return ucwords(strtolower($useras));
    }

    public function getUserPrinsipleOptionsAttribute()
    {
        return \App\Http\Controllers\InterviewController::getUserPrinsipleOptions($this);
    }
}