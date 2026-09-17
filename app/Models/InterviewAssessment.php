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
        $sum = $this->work_motivation + $this->appearance + $this->attitude + $this->comprehension;
        return round(($sum / 20) * 100, 1);
    }
}