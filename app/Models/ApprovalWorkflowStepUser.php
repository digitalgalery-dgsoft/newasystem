<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovalWorkflowStepUser extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Relasi ke step approval
     */
    public function step(): BelongsTo
    {
        return $this->belongsTo(ApprovalWorkflowStep::class, 'step_id');
    }

    /**
     * Relasi ke akun User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke data Employee
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
