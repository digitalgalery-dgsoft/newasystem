<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApprovalWorkflow extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relasi ke tahapan approval
     */
    public function steps(): HasMany
    {
        return $this->hasMany(ApprovalWorkflowStep::class, 'workflow_id')->orderBy('step_order', 'asc');
    }

    /**
     * Scope untuk alur yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
