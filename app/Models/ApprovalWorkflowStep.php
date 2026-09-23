<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApprovalWorkflowStep extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'step_order' => 'integer',
        'skip_if_direksi' => 'boolean',
    ];

    /**
     * Relasi ke alur workflow induk
     */
    public function workflow(): BelongsTo
    {
        return $this->belongsTo(ApprovalWorkflow::class, 'workflow_id');
    }

    /**
     * Relasi ke daftar user/karyawan yang ditugaskan sebagai approver
     */
    public function stepUsers(): HasMany
    {
        return $this->hasMany(ApprovalWorkflowStepUser::class, 'step_id');
    }

    /**
     * Relasi ke riwayat approval inhouse
     */
    public function approvals(): HasMany
    {
        return $this->hasMany(InhouseApproval::class, 'step_id');
    }

    /**
     * Cek apakah step ini berlaku untuk kandidat tertentu (berdasarkan area & entitas)
     */
    public function matchesCandidate($candidate): bool
    {
        // 1. Cek Filter Area
        if ($this->area_scope && $this->area_scope !== 'ALL') {
            $candArea = strtoupper(trim($candidate->area ?? ''));
            $isJakarta = str_contains($candArea, 'JAKARTA');

            if ($this->area_scope === 'JAKARTA' && !$isJakarta) {
                return false;
            }

            if ($this->area_scope === 'OUTSIDE_JAKARTA' && $isJakarta) {
                return false;
            }

            // Jika area_scope berupa JSON array area spesifik
            if (str_starts_with($this->area_scope, '[') && str_ends_with($this->area_scope, ']')) {
                $allowedAreas = json_decode($this->area_scope, true) ?: [];
                $allowedAreas = array_map('strtoupper', array_map('trim', $allowedAreas));
                if (!empty($allowedAreas) && !in_array($candArea, $allowedAreas, true)) {
                    return false;
                }
            }
        }

        // 2. Cek Filter Entitas
        if ($this->entity_scope && $this->entity_scope !== 'ALL') {
            $candEntity = '';
            if (is_object($candidate->principle)) {
                $candEntity = Employee::getEntityCodeFromPrinciple($candidate->principle->name) ?: $candidate->principle->name;
            } elseif (is_string($candidate->principle)) {
                $candEntity = Employee::getEntityCodeFromPrinciple($candidate->principle) ?: $candidate->principle;
            }

            $candEntityUpper = strtoupper(trim($candEntity));

            if (str_starts_with($this->entity_scope, '[') && str_ends_with($this->entity_scope, ']')) {
                $allowedEntities = json_decode($this->entity_scope, true) ?: [];
                $allowedEntities = array_map('strtoupper', array_map('trim', $allowedEntities));
                if (!empty($allowedEntities) && !in_array($candEntityUpper, $allowedEntities, true)) {
                    return false;
                }
            } else {
                $targetEntityUpper = strtoupper(trim($this->entity_scope));
                if ($targetEntityUpper !== $candEntityUpper && !str_contains($candEntityUpper, $targetEntityUpper)) {
                    return false;
                }
            }
        }

        return true;
    }
}
