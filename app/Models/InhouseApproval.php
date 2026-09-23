<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InhouseApproval extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'time_approver' => 'datetime',
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    public function step()
    {
        return $this->belongsTo(ApprovalWorkflowStep::class, 'step_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}