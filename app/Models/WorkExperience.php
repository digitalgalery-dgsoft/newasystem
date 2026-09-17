<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkExperience extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_id',
        'company_name',
        'position',
        'company_phone',
        'reason_for_leaving',
        'start_date',
        'end_date',
        'supervisor_name',
        'performance_notes',
        'discipline_notes',
        'responsibility_notes',
        'strengths',
        'weaknesses',
        'check_date',
        'proof_attachment_path',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'check_date' => 'date',
    ];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }
}