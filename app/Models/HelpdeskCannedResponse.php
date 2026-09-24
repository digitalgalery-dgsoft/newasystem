<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HelpdeskCannedResponse extends Model
{
    use HasFactory;

    protected $table = 'helpdesk_canned_responses';

    protected $fillable = [
        'division_id',
        'title',
        'content',
        'created_by',
    ];

    public function division(): BelongsTo
    {
        return $this->belongsTo(HelpdeskDivision::class, 'division_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
