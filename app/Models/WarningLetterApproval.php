<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WarningLetterApproval extends Model
{
    use HasFactory;

    protected $table = 'warning_letter_approvals';

    protected $fillable = [
        'warning_letter_id',
        'stage',
        'user_id',
        'action',
        'perubahan_tingkat',
        'catatan',
    ];

    public function warningLetter(): BelongsTo
    {
        return $this->belongsTo(WarningLetter::class, 'warning_letter_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
