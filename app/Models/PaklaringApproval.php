<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaklaringApproval extends Model
{
    use HasFactory;

    protected $table = 'paklaring_approvals';

    protected $guarded = ['id'];

    protected $casts = [
        'step_order' => 'integer',
        'data_changes' => 'array',
    ];

    public function paklaring(): BelongsTo
    {
        return $this->belongsTo(Paklaring::class, 'paklaring_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function step(): BelongsTo
    {
        return $this->belongsTo(ApprovalWorkflowStep::class, 'step_id');
    }
}
