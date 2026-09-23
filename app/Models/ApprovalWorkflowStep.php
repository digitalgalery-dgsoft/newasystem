<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class ApprovalWorkflowStep extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'step_order' => 'integer',
        'skip_if_direksi' => 'boolean',
        'approval_rules' => 'array',
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
     * Mencari aturan pemetaan dinamis (Area + Prinsiple + Users) yang paling cocok untuk kandidat ini
     */
    public function getMatchingRuleForCandidate($candidate): ?array
    {
        $rules = $this->approval_rules;
        if (empty($rules) || !is_array($rules)) {
            return null;
        }

        $candArea = strtoupper(trim($candidate->area ?? ''));
        $isJakarta = str_contains($candArea, 'JAKARTA');

        $candPrin = '';
        if (is_object($candidate->principle)) {
            $candPrin = $candidate->principle->name ?? '';
        } elseif (is_string($candidate->principle)) {
            $candPrin = $candidate->principle;
        }
        $candEntityCode = Employee::getEntityCodeFromPrinciple($candPrin) ?: $candPrin;
        $candEntityCodeUpper = strtoupper(trim($candEntityCode));
        $candPrinUpper = strtoupper(trim($candPrin));

        $matchedRules = [];

        foreach ($rules as $rule) {
            $ruleArea = strtoupper(trim($rule['area'] ?? 'ALL'));
            $rulePrin = strtoupper(trim($rule['prinsiple'] ?? 'ALL'));

            // 1. Evaluasi Area
            $areaMatches = false;
            $areaScore = 0;
            if ($ruleArea === 'ALL' || empty($ruleArea)) {
                $areaMatches = true;
                $areaScore = 1;
            } elseif ($ruleArea === 'JAKARTA') {
                if ($isJakarta) {
                    $areaMatches = true;
                    $areaScore = 3;
                }
            } elseif ($ruleArea === 'OUTSIDE_JAKARTA') {
                if (!$isJakarta) {
                    $areaMatches = true;
                    $areaScore = 3;
                }
            } else {
                // Exact / partial area match
                if ($candArea === $ruleArea || str_contains($candArea, $ruleArea)) {
                    $areaMatches = true;
                    $areaScore = 4;
                }
            }

            if (!$areaMatches) {
                continue;
            }

            // 2. Evaluasi Prinsiple / Entitas
            $prinMatches = false;
            $prinScore = 0;
            if ($rulePrin === 'ALL' || empty($rulePrin)) {
                $prinMatches = true;
                $prinScore = 1;
            } elseif ($rulePrin === $candEntityCodeUpper || str_contains($candEntityCodeUpper, $rulePrin)) {
                $prinMatches = true;
                $prinScore = 3;
            } elseif (!empty($candPrinUpper) && (str_contains($candPrinUpper, $rulePrin) || str_contains($rulePrin, $candPrinUpper))) {
                $prinMatches = true;
                $prinScore = 4;
            }

            if (!$prinMatches) {
                continue;
            }

            // Total bobot kecocokan spesifik
            $totalScore = $areaScore + $prinScore;
            $matchedRules[] = [
                'score' => $totalScore,
                'rule' => $rule
            ];
        }

        if (empty($matchedRules)) {
            return null;
        }

        // Urutkan dari bobot tertinggi (paling spesifik)
        usort($matchedRules, fn($a, $b) => $b['score'] <=> $a['score']);

        return $matchedRules[0]['rule'];
    }

    /**
     * Dapatkan daftar approver spesifik untuk kandidat berdasarkan aturan yang cocok
     */
    public function getMatchingApproversForCandidate($candidate): Collection
    {
        if ($this->approver_type === 'user' && !empty($this->approval_rules)) {
            $matchingRule = $this->getMatchingRuleForCandidate($candidate);
            if ($matchingRule) {
                // Jika data users sudah terangkum di dalam array rule
                if (!empty($matchingRule['users']) && is_array($matchingRule['users'])) {
                    return collect($matchingRule['users']);
                }

                // Jika hanya ada user_ids
                if (!empty($matchingRule['user_ids']) && is_array($matchingRule['user_ids'])) {
                    return User::whereIn('id', $matchingRule['user_ids'])->get();
                }
            }
        }

        // Fallback ke relasi stepUsers
        return $this->stepUsers;
    }

    /**
     * Cek apakah step ini berlaku untuk kandidat tertentu (berdasarkan aturan dinamis atau filter area & entitas)
     */
    public function matchesCandidate($candidate): bool
    {
        // 1. Jika bertipe 'user' dan memiliki aturan dinamis (approval_rules)
        if ($this->approver_type === 'user' && !empty($this->approval_rules) && is_array($this->approval_rules)) {
            return $this->getMatchingRuleForCandidate($candidate) !== null;
        }

        // 2. Cek Filter Area Legacy
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

        // 3. Cek Filter Entitas Legacy
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
