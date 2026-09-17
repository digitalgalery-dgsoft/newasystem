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

    // --- ACCESSORS & ALIASES (Matching Legacy tb_kandidat & Modern schema) ---

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

    public function getAlamatKtpAttribute()
    {
        return $this->address_ktp;
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
}