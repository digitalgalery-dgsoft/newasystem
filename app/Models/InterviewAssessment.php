<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InterviewAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_id',
        'interviewer_id',
        'interview_date',
        'work_motivation',
        'appearance',
        'attitude',
        'comprehension',
        'other_notes',
        'recommendation',
        'offered_salary',
        'placement_area',
        'interviewer_signature_path',

        // Aliases allowed in fillable to prevent silent dropping by Eloquent
        'work_willingness',
        'work_motivation_score',
        'appearance_score',
        'attitude_score',
        'comprehension_score',
        'notes',
        'salary_offered',
        'interviewer_signature',
    ];

    protected $casts = [
        'interview_date' => 'date',
        'work_motivation' => 'integer',
        'appearance' => 'integer',
        'attitude' => 'integer',
        'comprehension' => 'integer',
        'offered_salary' => 'decimal:2',
    ];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function interviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'interviewer_id');
    }

    // Total interview score percentage
    public function getAverageScoreAttribute(): float
    {
        $sum = ($this->work_motivation ?? 3) + ($this->appearance ?? 3) + ($this->attitude ?? 3) + ($this->comprehension ?? 3);
        return round(($sum / 20) * 100, 1);
    }

    /**
     * Konversi nilai numerik 1-5 ke label teks standar
     */
    public static function scoreToLabel(?int $score): string
    {
        return match ((int)$score) {
            5 => 'Sangat Baik',
            4 => 'Baik',
            3 => 'Cukup',
            2, 1 => 'Kurang',
            default => 'Baik',
        };
    }

    /**
     * Konversi label teks atau angka ke skor numerik 1-5
     */
    public static function labelToScore($label): int
    {
        if (is_numeric($label)) {
            $n = (int)$label;
            return ($n >= 1 && $n <= 5) ? $n : 3;
        }

        return match (trim((string)$label)) {
            'Sangat Baik' => 5,
            'Baik'        => 4,
            'Cukup'       => 3,
            'Kurang'      => 2,
            default       => 3,
        };
    }

    // Accessors & Mutators for work_willingness <-> work_motivation
    public function getWorkWillingnessAttribute(): ?string
    {
        return self::scoreToLabel($this->work_motivation);
    }

    public function setWorkWillingnessAttribute($value): void
    {
        $this->attributes['work_motivation'] = self::labelToScore($value);
    }

    public function setWorkMotivationScoreAttribute($value): void
    {
        $this->attributes['work_motivation'] = self::labelToScore($value);
    }

    public function setAppearanceScoreAttribute($value): void
    {
        $this->attributes['appearance'] = self::labelToScore($value);
    }

    public function setAttitudeScoreAttribute($value): void
    {
        $this->attributes['attitude'] = self::labelToScore($value);
    }

    public function setComprehensionScoreAttribute($value): void
    {
        $this->attributes['comprehension'] = self::labelToScore($value);
    }

    public function setSalaryOfferedAttribute($value): void
    {
        $this->attributes['offered_salary'] = is_numeric($value) ? $value : null;
    }

    public function setInterviewerSignatureAttribute($value): void
    {
        if (!empty($value)) {
            $this->attributes['interviewer_signature_path'] = $value;
        }
    }

    public function setWorkMotivationAttribute($value): void
    {
        $this->attributes['work_motivation'] = self::labelToScore($value);
    }

    public function setAppearanceAttribute($value): void
    {
        $this->attributes['appearance'] = self::labelToScore($value);
    }

    public function setAttitudeAttribute($value): void
    {
        $this->attributes['attitude'] = self::labelToScore($value);
    }

    public function setComprehensionAttribute($value): void
    {
        $this->attributes['comprehension'] = self::labelToScore($value);
    }

    // Accessors & Mutators for notes <-> other_notes
    public function getNotesAttribute(): ?string
    {
        return $this->other_notes;
    }

    public function setNotesAttribute($value): void
    {
        $this->attributes['other_notes'] = $value;
    }
}