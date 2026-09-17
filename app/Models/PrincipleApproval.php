<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class PrincipleApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_id',
        'principle_id',
        'approval_token',
        'status',
        'notes',
        'signature_path',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->approval_token)) {
                $model->approval_token = Str::random(48);
            }
        });
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function principle(): BelongsTo
    {
        return $this->belongsTo(Principle::class);
    }
}