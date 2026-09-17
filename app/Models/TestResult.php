<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_id',
        'test_type',
        'score',
        'duration_seconds',
        'test_details',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'duration_seconds' => 'integer',
        'test_details' => 'array',
    ];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }
}