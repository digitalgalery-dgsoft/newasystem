<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandidateLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_id',
        'candidate_name',
        'activity',
        'ip_address',
        'browser',
        'os',
    ];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }
}
