<?php

namespace App\Services;

use App\Models\ApprovalWorkflow;
use App\Models\ApprovalWorkflowStep;
use App\Models\Candidate;
use App\Models\Employee;
use App\Models\InhouseApproval;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ApprovalWorkflowService
{
    /**
     * Ambil konfigurasi workflow aktif untuk modul tertentu
     */
    public static function getWorkflow(string $module = 'kandidat_inhouse'): ?ApprovalWorkflow
    {
        return ApprovalWorkflow::with(['steps.stepUsers.user', 'steps.stepUsers.employee'])
            ->where('module', $module)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Cek apakah Pimpinan / Head dari rekruter yang menangani kandidat berstatus Direksi / Direktur / BOD
     */
    public static function isPimpinanDireksi(Candidate $candidate): bool
    {
        // 1. Identifikasi identifier rekruter / AS kandidat
        $recruiterEmail = $candidate->recruiter?->email ?? (str_contains($candidate->useras ?? '', '@') ? $candidate->useras : null);
        $recruiterName = $candidate->recruiter?->name ?? $candidate->useras;

        $pimpinan = null;
        $jabatanPimpinan = null;

        if (!empty($recruiterEmail)) {
            $emp = Employee::whereRaw('LOWER(TRIM(email)) = ?', [strtolower(trim($recruiterEmail))])
                ->whereNotNull('pimpinan')
                ->where('pimpinan', '!=', '')
                ->first();
            if ($emp) {
                $pimpinan = $emp->pimpinan;
                $jabatanPimpinan = $emp->jabatan_pimpinan ?? '';
            }
        }

        if (empty($pimpinan) && !empty($recruiterName)) {
            $cleanName = trim(preg_replace('/\b(recruitment|recruiter|aro|jakarta|surabaya|bandung|amk|akp|atk|abo)\b/i', '', $recruiterName));
            $emp = Employee::where(function ($q) use ($cleanName, $recruiterName) {
                if (!empty($cleanName)) {
                    $q->orWhereRaw('LOWER(TRIM(nama_karyawan)) = ?', [strtolower($cleanName)])
                      ->orWhere('nama_karyawan', 'like', "%{$cleanName}%");
                }
                $q->orWhere('nama_karyawan', 'like', "%{$recruiterName}%");
            })
            ->whereNotNull('pimpinan')
            ->where('pimpinan', '!=', '')
            ->first();

            if ($emp) {
                $pimpinan = $emp->pimpinan;
                $jabatanPimpinan = $emp->jabatan_pimpinan ?? '';
            }
        }

        // Cek juga jika kandidat memiliki nama_approver yang eksplisit diset
        $approverString = ($pimpinan ?? '') . ' ' . ($jabatanPimpinan ?? '') . ' ' . ($candidate->nama_approver ?? '');

        // Kata kunci penanda Direksi / Board of Directors
        $direksiRegex = '/\b(direksi|direktur|director|bod|ceo|coo|cfo|presdir|president director|presiden direktur)\b/i';
        if (preg_match($direksiRegex, $approverString)) {
            return true;
        }

        // Cek apakah nama pimpinan terdaftar sebagai karyawan dengan jabatan Direksi
        if (!empty($pimpinan)) {
            $pimpinanEmp = Employee::where('nama_karyawan', $pimpinan)
                ->orWhere('nama_karyawan', 'like', "%{$pimpinan}%")
                ->first();
            if ($pimpinanEmp && !empty($pimpinanEmp->jabatan) && preg_match($direksiRegex, $pimpinanEmp->jabatan)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Ambil daftar step approval yang relevan / berlaku khusus untuk kandidat ini
     * (Menyaring aturan Direksi, Area Jakarta vs Selain Jakarta, dan Entitas)
     */
    public static function getApplicableStepsForCandidate(Candidate $candidate, ?ApprovalWorkflow $workflow = null): Collection
    {
        $wf = $workflow ?: self::getWorkflow('kandidat_inhouse');
        if (!$wf || $wf->steps->isEmpty()) {
            return collect();
        }

        $isDireksi = self::isPimpinanDireksi($candidate);

        return $wf->steps->filter(function ($step) use ($candidate, $isDireksi) {
            // 1. Filter Direksi: jika step diset skip_if_direksi dan pimpinan adalah Direksi, lewati step ini
            if ($step->skip_if_direksi && $isDireksi) {
                return false;
            }

            // 2. Filter Area & Entitas via model helper
            return $step->matchesCandidate($candidate);
        })->values();
    }

    /**
     * Dapatkan Step yang saat ini sedang aktif / menunggu persetujuan untuk kandidat
     */
    public static function getCurrentStep(Candidate $candidate, ?Collection $applicableSteps = null): ?ApprovalWorkflowStep
    {
        // Jika kandidat sudah selesai (Approve) atau Ditolak (Tolak)
        $statusAppr = trim($candidate->status_approval ?? '');
        if (in_array($statusAppr, ['Approve', 'Tolak'])) {
            return null;
        }

        $steps = $applicableSteps ?: self::getApplicableStepsForCandidate($candidate);
        if ($steps->isEmpty()) {
            return null;
        }

        // 1. Cek dari current_approval_step_id
        if (!empty($candidate->current_approval_step_id)) {
            $matched = $steps->firstWhere('id', (int)$candidate->current_approval_step_id);
            if ($matched) {
                return $matched;
            }
        }

        // 2. Cek dari current_step_order
        if (!empty($candidate->current_step_order)) {
            $matched = $steps->firstWhere('step_order', (int)$candidate->current_step_order);
            if ($matched) {
                return $matched;
            }
        }

        // 3. Fallback backward-compatibility berdasarkan string status_approval
        if ($statusAppr === 'Review Head') {
            $headStep = $steps->firstWhere('approver_type', 'head');
            if ($headStep) {
                return $headStep;
            }
        } elseif (in_array($statusAppr, ['Review HRD', 'Proses'])) {
            $userStep = $steps->firstWhere('approver_type', 'user');
            if ($userStep) {
                return $userStep;
            }
        }

        // 4. Default: kembalikan step pertama yang berlaku
        return $steps->first();
    }

    /**
     * Dapatkan step berikutnya setelah step saat ini selesai disetujui
     */
    public static function getNextStep(Candidate $candidate, ApprovalWorkflowStep $currentStep, ?Collection $applicableSteps = null): ?ApprovalWorkflowStep
    {
        $steps = $applicableSteps ?: self::getApplicableStepsForCandidate($candidate);
        if ($steps->isEmpty()) {
            return null;
        }

        $currentIndex = $steps->search(fn($s) => $s->id === $currentStep->id);
        if ($currentIndex === false) {
            // Jika tidak ditemukan berdasarkan ID, cari berdasarkan step_order
            $next = $steps->first(fn($s) => $s->step_order > $currentStep->step_order);
            return $next;
        }

        // Ambil step pada indeks setelahnya
        return $steps->get($currentIndex + 1);
    }

    /**
     * Cek apakah pengguna saat ini berhak memberikan persetujuan pada step aktif kandidat
     */
    public static function canUserApprove(Candidate $candidate, User $user, ?ApprovalWorkflowStep $currentStep = null): bool
    {
        // 1. Super Admin selalu berhak menyetujui semua step
        if ($user->isAdmin() || $user->role === 'admin') {
            return true;
        }

        $statusAppr = trim($candidate->status_approval ?? '');
        if (in_array($statusAppr, ['Approve', 'Tolak'])) {
            return false;
        }

        $step = $currentStep ?: self::getCurrentStep($candidate);
        if (!$step) {
            return false;
        }

        // 2. Step bertipe 'head': approver adalah Head/Pimpinan langsung dari rekruter kandidat
        if ($step->approver_type === 'head') {
            $userName = strtolower(trim($user->name));
            $candApprover = strtolower(trim($candidate->nama_approver ?? ''));

            // a. Jika nama user cocok dengan nama_approver yang dikunci kandidat
            if (!empty($candApprover) && !empty($userName) && (str_contains($candApprover, $userName) || str_contains($userName, $candApprover))) {
                return true;
            }

            // b. Cek pimpinan rekruter di master Employee
            $subIdentifiers = $user->getSubordinateRecruiterIdentifiers();
            $candUseras = strtolower(trim($candidate->useras ?? ''));
            if (!empty($candUseras) && in_array($candUseras, $subIdentifiers, true)) {
                return true;
            }

            if ($candidate->recruiter_id && $candidate->recruiter_id === $user->id) {
                return true;
            }

            // c. Area match untuk user bertitel Head / Manager di area yang sama
            if ($user->isHead()) {
                $candArea = strtoupper(trim($candidate->area ?? ''));
                $userAreas = array_map('strtoupper', $user->getEffectiveAreas());
                if (!empty($candArea) && in_array($candArea, $userAreas, true)) {
                    return true;
                }
            }

            return false;
        }

        // 3. Step bertipe 'user': approver adalah user yang terdaftar pada step tersebut (Multiple Users)
        if ($step->approver_type === 'user') {
            // Jika step memiliki dynamic rules (Area + Prinsiple + Users)
            if (!empty($step->approval_rules) && is_array($step->approval_rules)) {
                $matchingRule = $step->getMatchingRuleForCandidate($candidate);
                if ($matchingRule) {
                    $ruleUserIds = array_map('intval', $matchingRule['user_ids'] ?? []);
                    if (in_array((int)$user->id, $ruleUserIds, true)) {
                        return true;
                    }

                    // Cek jika ada objek users di dalam rule
                    if (!empty($matchingRule['users']) && is_array($matchingRule['users'])) {
                        foreach ($matchingRule['users'] as $uItem) {
                            $uid = is_array($uItem) ? ($uItem['id'] ?? null) : ($uItem->id ?? null);
                            if ($uid && (int)$uid === (int)$user->id) {
                                return true;
                            }
                            $uEmail = is_array($uItem) ? ($uItem['email'] ?? null) : ($uItem->email ?? null);
                            if ($uEmail && strtolower(trim($uEmail)) === strtolower(trim($user->email ?? ''))) {
                                return true;
                            }
                        }
                    }

                    return false;
                }
            }

            // Fallback legacy: jika tidak ada dynamic rules
            if ($user->isHrd()) {
                return true;
            }

            // Cek apakah user_id pengguna terdaftar di stepUsers
            $stepUsers = $step->stepUsers;
            if ($stepUsers->contains('user_id', $user->id)) {
                return true;
            }

            // Cek pencocokan email atau nama
            $userEmail = strtolower(trim($user->email ?? ''));
            $userName = strtolower(trim($user->name ?? ''));

            foreach ($stepUsers as $su) {
                if (!empty($su->user_email) && strtolower(trim($su->user_email)) === $userEmail) {
                    return true;
                }
                if (!empty($su->user_name) && (str_contains($userName, strtolower(trim($su->user_name))) || str_contains(strtolower(trim($su->user_name)), $userName))) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Dapatkan detail approver yang relevan untuk kandidat pada step tertentu
     * (Menyajikan nama PIC spesifik berdasarkan kombinasi Area & Prinsiple)
     */
    public static function getStepApproverDisplayInfo(Candidate $candidate, ApprovalWorkflowStep $step): array
    {
        if ($step->approver_type === 'head') {
            $pimpinan = $candidate->recruiter?->employee?->pimpinan 
                ?: ($candidate->nama_approver ?: 'Head / Pimpinan Rekruter');
            return [
                'type' => 'head',
                'title' => 'Head / Pimpinan',
                'label' => $pimpinan,
                'names' => [$pimpinan],
                'rule_condition' => 'Atasan Langsung Rekruter',
            ];
        }

        // Tipe 'user' dengan dynamic rules (approval_rules)
        if (!empty($step->approval_rules) && is_array($step->approval_rules)) {
            $matchedRule = $step->getMatchingRuleForCandidate($candidate);
            if ($matchedRule) {
                $names = [];
                if (!empty($matchedRule['users']) && is_array($matchedRule['users'])) {
                    foreach ($matchedRule['users'] as $u) {
                        $names[] = is_array($u) ? ($u['name'] ?? 'User') : ($u->name ?? 'User');
                    }
                } elseif (!empty($matchedRule['user_ids']) && is_array($matchedRule['user_ids'])) {
                    $names = User::whereIn('id', $matchedRule['user_ids'])->pluck('name')->toArray();
                }

                $ruleArea = $matchedRule['area'] ?? 'ALL';
                $rulePrin = $matchedRule['prinsiple'] ?? 'ALL';
                $condText = ($ruleArea === 'ALL' ? 'Semua Area' : $ruleArea) . ' • ' . ($rulePrin === 'ALL' ? 'Semua Entitas' : $rulePrin);

                return [
                    'type' => 'user',
                    'title' => $step->step_name,
                    'label' => !empty($names) ? implode(', ', $names) : 'Akun Approver',
                    'names' => $names,
                    'rule_condition' => $condText,
                    'matched_rule' => $matchedRule,
                ];
            }
        }

        // Fallback jika tidak ada aturan dinamis yang cocok atau legacy
        $names = $step->stepUsers->pluck('user_name')->filter()->values()->toArray();
        return [
            'type' => 'user',
            'title' => $step->step_name,
            'label' => !empty($names) ? implode(', ', $names) : 'Approver Inhouse',
            'names' => $names,
            'rule_condition' => ($step->area_scope ?: 'Semua Area') . ' • ' . ($step->entity_scope ?: 'Semua Entitas'),
        ];
    }

    /**
     * Inisialisasi step awal untuk kandidat inhouse baru
     */
    public static function initializeCandidateStep(Candidate $candidate): void
    {
        $applicableSteps = self::getApplicableStepsForCandidate($candidate);
        $firstStep = $applicableSteps->first();

        if ($firstStep) {
            $candidate->current_approval_step_id = $firstStep->id;
            $candidate->current_step_order = $firstStep->step_order;
            $candidate->status_approval = 'Review ' . $firstStep->step_name;
            $candidate->save();
        }
    }

    /**
     * Proses submit approval atau penolakan kandidat
     */
    public static function processApproval(Candidate $candidate, User $user, array $data): array
    {
        $catatan = trim($data['catatan'] ?? '');
        $decision = trim($data['approval'] ?? $data['status'] ?? 'Approve');
        $isApprove = in_array(strtolower($decision), ['approve', 'yes', 'setuju']);
        $finalDecision = $isApprove ? 'Approve' : 'Tolak';
        $signaturePath = $data['signature_path'] ?? null;

        $applicableSteps = self::getApplicableStepsForCandidate($candidate);
        $currentStep = self::getCurrentStep($candidate, $applicableSteps);

        $stepId = $currentStep?->id;
        $stepOrder = $currentStep?->step_order ?? 1;
        $stepName = $currentStep?->step_name ?? ($user->isHrd() ? 'Persetujuan HRD' : 'Persetujuan Head');

        // 1. Simpan catatan ke tabel inhouse_approvals
        $approvalLog = InhouseApproval::create([
            'candidate_id' => $candidate->id,
            'step_id' => $stepId,
            'step_order' => $stepOrder,
            'step_name' => $stepName,
            'user_id' => $user->id,
            'nama_approver' => $user->name,
            'jabatan_approver' => $user->job_title ?? ($currentStep?->approver_type === 'head' ? 'Head Approver' : 'Approver Inhouse'),
            'catatan_approver' => $catatan,
            'status' => $finalDecision,
            'ttd_approver' => $signaturePath,
            'time_approver' => Carbon::now('Asia/Jakarta'),
        ]);

        // 2. Sinkronisasi ke tb_catataninhouse (legacy support)
        try {
            if (Schema::hasTable('tb_catataninhouse')) {
                DB::table('tb_catataninhouse')->insert([
                    'id_kandidat' => $candidate->id,
                    'nama_approver' => $user->name,
                    'jabatan_aprover' => $user->job_title ?? ($currentStep?->approver_type === 'head' ? 'Head Approver' : 'Approver'),
                    'catatan_approver' => $catatan,
                    'status' => $isApprove ? 'Yes' : 'No',
                    'ttd_approver' => $signaturePath ? basename($signaturePath) : null,
                    'time_approver' => Carbon::now('Asia/Jakarta')->toDateTimeString(),
                ]);
            }
        } catch (\Throwable $e) {}

        // 3. Update Status Kandidat
        if (!$isApprove) {
            // Jika Ditolak: proses selesai dengan status Tolak
            $candidate->update([
                'status_approval' => 'Tolak',
            ]);

            return [
                'status' => 'success',
                'decision' => 'Tolak',
                'message' => "Keputusan penolakan pada tahap [{$stepName}] berhasil disimpan.",
                'is_completed' => true,
            ];
        }

        // Jika Disetujui: Cek apakah masih ada step berikutnya
        $nextStep = $currentStep ? self::getNextStep($candidate, $currentStep, $applicableSteps) : null;

        if ($nextStep) {
            // Maju ke step berikutnya
            $candidate->update([
                'current_approval_step_id' => $nextStep->id,
                'current_step_order' => $nextStep->step_order,
                'status_approval' => 'Review ' . $nextStep->step_name,
            ]);

            return [
                'status' => 'success',
                'decision' => 'Approve',
                'message' => "Persetujuan pada tahap [{$stepName}] berhasil disimpan! Kandidat kini diteruskan ke tahap berikutnya: [{$nextStep->step_name}].",
                'is_completed' => false,
                'next_step' => $nextStep,
            ];
        }

        // Jika ini adalah step terakhir: Kandidat Selesai Disetujui (Approved)
        $candidate->update([
            'status_approval' => 'Approve',
            'note_principle' => $catatan,
            'ttd_prinsiple' => $signaturePath ?? $candidate->ttd_prinsiple ?? $candidate->signature_path,
            'time_prinsiple' => Carbon::now('Asia/Jakarta'),
        ]);

        return [
            'status' => 'success',
            'decision' => 'Approve',
            'message' => "Persetujuan tahap akhir [{$stepName}] berhasil disimpan! Seluruh tahapan approval inhouse telah selesai dan kandidat disetujui (Approved).",
            'is_completed' => true,
        ];
    }
}
