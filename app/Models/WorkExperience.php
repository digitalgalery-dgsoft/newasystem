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

    protected $appends = [
        'proof_url',
    ];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function getProofUrlAttribute(): ?string
    {
        $val = trim($this->proof_attachment_path ?? '');
        if (empty($val)) {
            return null;
        }

        $baseName = basename($val);

        if (file_exists(public_path('refcekfile/' . $baseName))) {
            return asset('refcekfile/' . $baseName);
        }
        if (file_exists(public_path('lampiran/' . $baseName))) {
            return asset('lampiran/' . $baseName);
        }
        if (file_exists(public_path('storage/' . $baseName))) {
            return asset('storage/' . $baseName);
        }

        return route('refcekfile.show', $baseName);
    }
}