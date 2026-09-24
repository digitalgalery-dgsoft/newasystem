<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HelpdeskDivisionAgent extends Model
{
    use HasFactory;

    protected $table = 'helpdesk_division_agents';

    protected $fillable = [
        'division_id',
        'user_id',
        'is_lead',
        'is_auto_assign',
    ];

    protected $casts = [
        'is_lead' => 'boolean',
        'is_auto_assign' => 'boolean',
    ];

    public function division(): BelongsTo
    {
        return $this->belongsTo(HelpdeskDivision::class, 'division_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
