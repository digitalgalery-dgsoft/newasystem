<?php

namespace App\Services;

use App\Models\ApprovalWorkflow;
use App\Models\ApprovalWorkflowStep;
use App\Models\Paklaring;
use App\Models\PaklaringApproval;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaklaringApprovalService
{
    /**
     * Ambil workflow aktif untuk modul paklaring
     */
    public static function getWorkflow(): ?ApprovalWorkflow
    {
        return ApprovalWorkflow::with(['steps.stepUsers.user'])
            ->where('module', 'paklaring')
            ->where('is_active', true)
            ->first();
    }

    /**
     * Generate Kode Validasi Unik (8 Karakter Alfanumerik Uppercase)
     */
    public static function generateKodeValidasi(): string
    {
        $characters = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        $maxAttempts = 10;

        for ($i = 0; $i < $maxAttempts; $i++) {
            $code = '';
            for ($c = 0; $c < 8; $c++) {
                $code .= $characters[random_int(0, strlen($characters) - 1)];
            }

            if (!Paklaring::where('kode_validasi', $code)->exists()) {
                return $code;
            }
        }

        return strtoupper(Str::random(8));
    }

    /**
     * Dapatkan step saat ini berdasarkan posisi approval paklaring
     */
    public static function getCurrentStep(Paklaring $paklaring): ?ApprovalWorkflowStep
    {
        $wf = self::getWorkflow();
        if (!$wf || $wf->steps->isEmpty()) {
            return null;
        }

        $stepOrder = $paklaring->current_step_order ?: match ($paklaring->status_bagian) {
            'Area' => 1,
            'HRD' => 2,
            'DB' => 3,
            'BPJS' => 4,
            default => 1,
        };

        return $wf->steps->firstWhere('step_order', $stepOrder);
    }

    /**
     * Cek apakah pengguna saat ini berhak melakukan approval pada step paklaring
     */
    public static function canUserApprove(Paklaring $paklaring, User $user, ?ApprovalWorkflowStep $step = null): bool
    {
        // 1. Super Admin / Admin selalu berhak menyetujui semua step
        if ($user->isAdmin() || $user->role === 'admin') {
            return true;
        }

        // Jika status sudah final (Selesai / Tolak), tidak bisa di-approve lagi
        if (in_array($paklaring->status, ['Selesai', 'Tolak'])) {
            return false;
        }

        $currentStep = $step ?: self::getCurrentStep($paklaring);
        if (!$currentStep) {
            return false;
        }

        // 2. Evaluasi Dynamic Rules (Area + Prinsiple + Users)
        if (!empty($currentStep->approval_rules) && is_array($currentStep->approval_rules)) {
            $matchingRule = self::getMatchingRule($currentStep, $paklaring);
            if ($matchingRule) {
                $ruleUserIds = array_map('intval', $matchingRule['user_ids'] ?? []);
                if (in_array((int)$user->id, $ruleUserIds, true)) {
                    return true;
                }

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
            }
        }

        // 3. Evaluasi Tabel Pivot Step Users
        if ($currentStep->stepUsers->contains('user_id', $user->id)) {
            return true;
        }

        $userEmail = strtolower(trim($user->email ?? ''));
        $userName = strtolower(trim($user->name ?? ''));
        foreach ($currentStep->stepUsers as $su) {
            if (!empty($su->user_email) && strtolower(trim($su->user_email)) === $userEmail) {
                return true;
            }
            if (!empty($su->user_name) && (str_contains($userName, strtolower(trim($su->user_name))) || str_contains(strtolower(trim($su->user_name)), $userName))) {
                return true;
            }
        }

        // 4. Role & Job Title Fallback Logic berdasarkan Step Name / Bagian
        $stepOrder = $currentStep->step_order;
        $userRole = strtolower($user->role ?? '');
        $userJob = strtolower($user->job_title ?? '');
        $userArea = strtoupper(trim($user->area ?? ''));
        $paklaringArea = strtoupper(trim($paklaring->area ?? ''));

        if ($stepOrder === 1 || $paklaring->status_bagian === 'Area') {
            // Step Area: Staff Area / AS / Koordinator / ARO / Admin Ops di area yang sama
            if (str_contains($userRole, 'area') || str_contains($userRole, 'as') || str_contains($userJob, 'aro') || str_contains($userJob, 'operasional') || str_contains($userJob, 'admin')) {
                if (empty($userArea) || $userArea === 'ALL' || $userArea === $paklaringArea || str_contains($paklaringArea, $userArea) || str_contains($userArea, $paklaringArea)) {
                    return true;
                }
            }
        } elseif ($stepOrder === 2 || $paklaring->status_bagian === 'HRD') {
            // Step HRD: Tim HRD Management
            if ($user->isHrd() || str_contains($userRole, 'hr') || str_contains($userJob, 'hr') || str_contains($userJob, 'hrd')) {
                return true;
            }
        } elseif ($stepOrder === 3 || $paklaring->status_bagian === 'DB') {
            // Step DB: Tim Database
            if (str_contains($userRole, 'db') || str_contains($userJob, 'database') || str_contains($userJob, 'db') || $user->isHrd()) {
                return true;
            }
        } elseif ($stepOrder === 4 || $paklaring->status_bagian === 'BPJS') {
            // Step BPJS: Tim BPJS / HRD
            if (str_contains($userRole, 'bpjs') || str_contains($userJob, 'bpjs') || $user->isHrd()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Dapatkan default tab dashboard paklaring berdasarkan peran / step approval user (On Focus)
     */
    public static function getDefaultTabForUser(?User $user): string
    {
        if (!$user) {
            return 'area';
        }

        // 1. Akun Administrator selalu on focus ke tab 'all' (Semua)
        if ($user->isAdmin() || $user->role === 'admin') {
            return 'all';
        }

        $userEmail = strtolower(trim($user->email ?? ''));
        $userRole = strtolower($user->role ?? '');
        $userJob = strtolower($user->job_title ?? '');

        // 2. Prioritas Role / Job Title Spesifik (misal Tim BPJS atau Tim DB)
        if (str_contains($userRole, 'bpjs') || str_contains($userJob, 'bpjs')) {
            return 'bpjs';
        }

        if (str_contains($userRole, 'db') || str_contains($userJob, 'database') || str_contains($userJob, 'db')) {
            return 'db';
        }

        // 3. Evaluasi Penugasan di Master Approval Workflow (paklaring)
        $wf = self::getWorkflow();
        if ($wf && $wf->steps->isNotEmpty()) {
            foreach ($wf->steps->sortBy('step_order') as $step) {
                // Cek Pivot Step Users
                if ($step->stepUsers->contains('user_id', $user->id)) {
                    return self::mapStepOrderToTab($step->step_order);
                }
                foreach ($step->stepUsers as $su) {
                    if (!empty($su->user_email) && strtolower(trim($su->user_email)) === $userEmail) {
                        return self::mapStepOrderToTab($step->step_order);
                    }
                }

                // Cek Approval Rules
                if (!empty($step->approval_rules) && is_array($step->approval_rules)) {
                    foreach ($step->approval_rules as $rule) {
                        $ruleUserIds = array_map('intval', $rule['user_ids'] ?? []);
                        if (in_array((int)$user->id, $ruleUserIds, true)) {
                            return self::mapStepOrderToTab($step->step_order);
                        }
                        if (!empty($rule['users']) && is_array($rule['users'])) {
                            foreach ($rule['users'] as $uItem) {
                                $uid = is_array($uItem) ? ($uItem['id'] ?? null) : ($uItem->id ?? null);
                                if ($uid && (int)$uid === (int)$user->id) {
                                    return self::mapStepOrderToTab($step->step_order);
                                }
                                $uMail = is_array($uItem) ? ($uItem['email'] ?? null) : ($uItem->email ?? null);
                                if ($uMail && strtolower(trim($uMail)) === $userEmail) {
                                    return self::mapStepOrderToTab($step->step_order);
                                }
                            }
                        }
                    }
                }
            }
        }

        // 4. Fallback Role HRD
        if ($user->isHrd() || str_contains($userRole, 'hr') || str_contains($userJob, 'hr') || str_contains($userJob, 'hrd')) {
            return 'hrd';
        }

        // 5. Fallback Tim Area
        if (str_contains($userRole, 'area') || str_contains($userRole, 'as') || str_contains($userJob, 'aro') || str_contains($userJob, 'operasional') || !empty($user->area)) {
            return 'area';
        }

        return 'area';
    }

    /**
     * Map Step Order ke Nama Tab Dashboard
     */
    public static function mapStepOrderToTab(int $stepOrder): string
    {
        return match ($stepOrder) {
            1 => 'area',
            2 => 'hrd',
            3 => 'db',
            4 => 'bpjs',
            default => 'area',
        };
    }

    /**
     * Dapatkan detail approver yang relevan untuk paklaring pada step tertentu
     */
    public static function getStepApproverDisplayInfo(Paklaring $paklaring, ApprovalWorkflowStep $step): array
    {
        $names = [];

        if (!empty($step->approval_rules) && is_array($step->approval_rules)) {
            $matchingRule = self::getMatchingRule($step, $paklaring);
            if ($matchingRule) {
                if (!empty($matchingRule['users']) && is_array($matchingRule['users'])) {
                    foreach ($matchingRule['users'] as $u) {
                        $names[] = is_array($u) ? ($u['name'] ?? 'User') : ($u->name ?? 'User');
                    }
                } elseif (!empty($matchingRule['user_ids']) && is_array($matchingRule['user_ids'])) {
                    $names = User::whereIn('id', $matchingRule['user_ids'])->pluck('name')->toArray();
                }

                $ruleArea = $matchingRule['area'] ?? 'ALL';
                $rulePrin = $matchingRule['prinsiple'] ?? 'ALL';
                $condText = ($ruleArea === 'ALL' ? 'Semua Area' : $ruleArea) . ' • ' . ($rulePrin === 'ALL' ? 'Semua Entitas' : $rulePrin);

                return [
                    'title' => $step->step_name,
                    'label' => !empty($names) ? implode(', ', $names) : 'Akun Approver Ditugaskan',
                    'names' => $names,
                    'rule_condition' => $condText,
                ];
            }
        }

        if ($step->stepUsers->isNotEmpty()) {
            $names = $step->stepUsers->pluck('user_name')->filter()->toArray();
        }

        return [
            'title' => $step->step_name,
            'label' => !empty($names) ? implode(', ', $names) : 'Sesuai Penugasan & Wewenang Modul',
            'names' => $names,
            'rule_condition' => 'Aturan Default',
        ];
    }

    /**
     * Evaluasi dynamic rules step berdasarkan Area & Prinsiple
     */
    public static function getMatchingRule(ApprovalWorkflowStep $step, Paklaring $paklaring): ?array
    {
        $rules = $step->approval_rules;
        if (empty($rules) || !is_array($rules)) {
            return null;
        }

        $pArea = strtoupper(trim($paklaring->area ?? ''));
        $isJakarta = str_contains($pArea, 'JAKARTA');
        $pPrin = strtoupper(trim($paklaring->prinsiple ?? ''));
        $pKantor = strtoupper(trim($paklaring->kantor ?? ''));

        $bestRule = null;
        $highestScore = -1;

        foreach ($rules as $rule) {
            $ruleArea = strtoupper(trim($rule['area'] ?? 'ALL'));
            $rulePrin = strtoupper(trim($rule['prinsiple'] ?? 'ALL'));

            $areaScore = 0;
            if ($ruleArea === 'ALL' || empty($ruleArea)) {
                $areaScore = 1;
            } elseif ($ruleArea === 'JAKARTA' && $isJakarta) {
                $areaScore = 3;
            } elseif ($ruleArea === 'OUTSIDE_JAKARTA' && !$isJakarta) {
                $areaScore = 3;
            } elseif ($pArea === $ruleArea || str_contains($pArea, $ruleArea)) {
                $areaScore = 4;
            } else {
                continue;
            }

            $prinScore = 0;
            if ($rulePrin === 'ALL' || empty($rulePrin)) {
                $prinScore = 1;
            } elseif ($pPrin === $rulePrin || str_contains($pPrin, $rulePrin) || $pKantor === $rulePrin) {
                $prinScore = 4;
            } else {
                continue;
            }

            $totalScore = $areaScore + $prinScore;
            if ($totalScore > $highestScore) {
                $highestScore = $totalScore;
                $bestRule = $rule;
            }
        }

        return $bestRule;
    }

    // =========================================================================
    // EKSEKUSI TAHAPAN APPROVAL
    // =========================================================================

    /**
     * Step 1: Persetujuan Area (AS / Admin Operasional)
     */
    public static function approveArea(Paklaring $paklaring, User $user, array $data, ?string $ip = null): void
    {
        $now = Carbon::now('Asia/Jakarta');
        $startTime = $paklaring->waktu_input ?: $paklaring->created_at;
        $diff = $startTime ? $startTime->diff($now) : null;
        $durasi = $diff ? "{$diff->d} Hari, {$diff->h} Jam, {$diff->i} Menit" : '-';

        DB::transaction(function () use ($paklaring, $user, $data, $now, $durasi, $ip) {
            $changes = [
                'tgl_masuk' => $data['tgl_masuk'] ?? $paklaring->tgl_masuk,
                'tgl_keluar' => $data['tgl_keluar'] ?? $paklaring->tgl_keluar,
                'tgl_kirimsurat' => $data['tgl_kirimsurat'] ?? $paklaring->tgl_kirimsurat,
                'xpdc' => $data['xpdc'] ?? $paklaring->xpdc,
                'noresi' => $data['noresi'] ?? $paklaring->noresi,
                'catatan_aro' => $data['catatan'] ?? null,
                'lama_aro' => $durasi,
                'pengguna' => $user->name,
                'status_bagian' => 'HRD',
                'status' => 'Proses',
                'current_step_order' => 2,
            ];

            if (!empty($data['ttd_digital'])) {
                $changes['ttd_digital'] = $data['ttd_digital'];
            }

            $paklaring->update($changes);

            PaklaringApproval::create([
                'paklaring_id' => $paklaring->id,
                'step_order' => 1,
                'step_name' => 'Persetujuan Area (AS)',
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'user_role' => $user->role,
                'action' => 'approved',
                'action_to' => 'HRD',
                'notes' => $data['catatan'] ?? 'Disetujui oleh Area dan diteruskan ke HRD.',
                'data_changes' => [
                    'tgl_masuk' => $changes['tgl_masuk'],
                    'tgl_keluar' => $changes['tgl_keluar'],
                    'xpdc' => $changes['xpdc'],
                    'noresi' => $changes['noresi'],
                ],
                'ip_address' => $ip,
            ]);
        });
    }

    /**
     * Step 2: Persetujuan HRD (Bisa lanjut ke DB atau bypass langsung ke BPJS)
     */
    public static function approveHrd(Paklaring $paklaring, User $user, array $data, ?string $ip = null): void
    {
        $now = Carbon::now('Asia/Jakarta');
        $startTime = $paklaring->updated_at ?: $paklaring->created_at;
        $diff = $startTime ? $startTime->diff($now) : null;
        $durasi = $diff ? "{$diff->d} Hari, {$diff->h} Jam, {$diff->i} Menit" : '-';

        $actionTo = ($data['action_to'] ?? 'DB') === 'BPJS' ? 'BPJS' : 'DB';
        $nextStepOrder = ($actionTo === 'BPJS') ? 4 : 3;

        DB::transaction(function () use ($paklaring, $user, $data, $now, $durasi, $actionTo, $nextStepOrder, $ip) {
            $changes = [
                'catatan_hrd' => $data['catatan'] ?? null,
                'lama_hrd' => $durasi,
                'pengguna' => $user->name,
                'status_bagian' => $actionTo,
                'status' => 'Proses',
                'current_step_order' => $nextStepOrder,
            ];

            if (!empty($data['tgl_masuk'])) $changes['tgl_masuk'] = $data['tgl_masuk'];
            if (!empty($data['tgl_keluar'])) $changes['tgl_keluar'] = $data['tgl_keluar'];
            if (!empty($data['tgl_kirimsurat'])) $changes['tgl_kirimsurat'] = $data['tgl_kirimsurat'];
            if (!empty($data['xpdc'])) $changes['xpdc'] = $data['xpdc'];
            if (!empty($data['noresi'])) $changes['noresi'] = $data['noresi'];

            $paklaring->update($changes);

            PaklaringApproval::create([
                'paklaring_id' => $paklaring->id,
                'step_order' => 2,
                'step_name' => 'Persetujuan HRD',
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'user_role' => $user->role,
                'action' => 'approved',
                'action_to' => $actionTo,
                'notes' => $data['catatan'] ?? ("Disetujui HRD dan diteruskan ke " . ($actionTo === 'BPJS' ? 'Tim BPJS' : 'Tim DB')),
                'data_changes' => $changes,
                'ip_address' => $ip,
            ]);
        });
    }

    /**
     * Generate Nomor Referensi Surat Keterangan Kerja Otomatis / Manual
     */
    public static function generateNomorRef(Paklaring $paklaring, ?string $manualRef = null, ?int $manualUrut = null, ?Carbon $date = null): array
    {
        $now = $date ?: Carbon::now('Asia/Jakarta');
        $nomorRef = trim($manualRef ?? '');
        $nomorUrut = !empty($manualUrut) ? (int)$manualUrut : null;

        // Jika nomor ref tidak diisi manual, sistem membuat format otomatis standar
        if (empty($nomorRef)) {
            $kantor = $paklaring->kantor ?: 'AMK';
            $monthRoman = ['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'][(int)$now->format('n') - 1];
            $year = $now->format('Y');
            $lastUrut = $nomorUrut ?: ((Paklaring::whereYear('created_at', $year)->max('nomor_urutref') ?? 0) + 1);
            $nomorUrut = $lastUrut;
            $formattedUrut = str_pad($nomorUrut, 4, '0', STR_PAD_LEFT);
            $nomorRef = "{$formattedUrut}/SKK/{$kantor}/{$monthRoman}/{$year}";
        }

        return [
            'nomor_ref' => $nomorRef,
            'nomor_urutref' => $nomorUrut,
        ];
    }

    /**
     * Step 3: Persetujuan Tim DB (Database)
     * Mendukung pilihan: Lanjut ke BPJS atau Rilis Surat Langsung (Selesai) dengan penerbitan No. Ref
     */
    public static function approveDb(Paklaring $paklaring, User $user, array $data, ?string $ip = null): void
    {
        $now = Carbon::now('Asia/Jakarta');
        $startTime = $paklaring->updated_at ?: $paklaring->created_at;
        $diff = $startTime ? $startTime->diff($now) : null;
        $durasi = $diff ? "{$diff->d} Hari, {$diff->h} Jam, {$diff->i} Menit" : '-';

        $actionType = strtolower(trim($data['action_type'] ?? 'bpjs')); // 'bpjs' atau 'selesai'

        DB::transaction(function () use ($paklaring, $user, $data, $now, $durasi, $actionType, $ip) {
            $changes = [
                'catatan_db' => $data['catatan'] ?? null,
                'lama_db' => $durasi,
                'pengguna' => $user->name,
            ];

            if (!empty($data['tgl_masuk'])) $changes['tgl_masuk'] = $data['tgl_masuk'];
            if (!empty($data['tgl_keluar'])) $changes['tgl_keluar'] = $data['tgl_keluar'];

            if ($actionType === 'selesai') {
                // Rilis Surat Langsung dari Step DB
                $refData = self::generateNomorRef($paklaring, $data['nomor_ref'] ?? null, $data['nomor_urutref'] ?? null, $now);
                $nomorRef = $refData['nomor_ref'];
                $nomorUrut = $refData['nomor_urutref'];

                $changes['nomor_ref'] = $nomorRef;
                $changes['nomor_urutref'] = $nomorUrut;
                $changes['status_bagian'] = 'Selesai';
                $changes['status'] = 'Selesai';
                $changes['current_step_order'] = 5;

                $paklaring->update($changes);

                PaklaringApproval::create([
                    'paklaring_id' => $paklaring->id,
                    'step_order' => 3,
                    'step_name' => 'Persetujuan Tim DB',
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                    'user_role' => $user->role,
                    'action' => 'approved',
                    'action_to' => 'Selesai',
                    'notes' => $data['catatan'] ?? "Disetujui Tim DB dan Surat Referensi Kerja resmi langsung dirilis dengan nomor {$nomorRef}.",
                    'data_changes' => $changes,
                    'ip_address' => $ip,
                ]);
            } else {
                // Lanjut ke Step 4 (BPJS)
                if (!empty($data['nomor_ref'])) {
                    $changes['nomor_ref'] = trim($data['nomor_ref']);
                }
                if (!empty($data['nomor_urutref'])) {
                    $changes['nomor_urutref'] = (int)$data['nomor_urutref'];
                }
                $changes['status_bagian'] = 'BPJS';
                $changes['status'] = 'Proses';
                $changes['current_step_order'] = 4;

                $paklaring->update($changes);

                PaklaringApproval::create([
                    'paklaring_id' => $paklaring->id,
                    'step_order' => 3,
                    'step_name' => 'Persetujuan Tim DB',
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                    'user_role' => $user->role,
                    'action' => 'approved',
                    'action_to' => 'BPJS',
                    'notes' => $data['catatan'] ?? 'Data riwayat kerja diverifikasi Tim DB dan diteruskan ke Tim BPJS.',
                    'data_changes' => $changes,
                    'ip_address' => $ip,
                ]);
            }
        });
    }

    /**
     * Step 4: Persetujuan Tim BPJS (Finalisasi & Penerbitan No. Surat Referensi)
     */
    public static function approveBpjs(Paklaring $paklaring, User $user, array $data, ?string $ip = null): void
    {
        $now = Carbon::now('Asia/Jakarta');
        $startTime = $paklaring->updated_at ?: $paklaring->created_at;
        $diff = $startTime ? $startTime->diff($now) : null;
        $durasi = $diff ? "{$diff->d} Hari, {$diff->h} Jam, {$diff->i} Menit" : '-';

        DB::transaction(function () use ($paklaring, $user, $data, $now, $durasi, $ip) {
            $inputRef = !empty($data['nomor_ref']) ? $data['nomor_ref'] : ($paklaring->nomor_ref ?? null);
            $inputUrut = !empty($data['nomor_urutref']) ? (int)$data['nomor_urutref'] : ($paklaring->nomor_urutref ?? null);

            $refData = self::generateNomorRef($paklaring, $inputRef, $inputUrut, $now);
            $nomorRef = $refData['nomor_ref'];
            $nomorUrut = $refData['nomor_urutref'];

            $changes = [
                'nomor_ref' => $nomorRef,
                'nomor_urutref' => $nomorUrut,
                'catatan_bpjs' => $data['catatan'] ?? null,
                'lama_bpjs' => $durasi,
                'pengguna' => $user->name,
                'status_bagian' => 'Selesai',
                'status' => 'Selesai',
                'current_step_order' => 5,
            ];

            $paklaring->update($changes);

            PaklaringApproval::create([
                'paklaring_id' => $paklaring->id,
                'step_order' => 4,
                'step_name' => 'Persetujuan Tim BPJS',
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'user_role' => $user->role,
                'action' => 'approved',
                'action_to' => 'Selesai',
                'notes' => $data['catatan'] ?? "Surat referensi kerja resmi diterbitkan dengan nomor {$nomorRef}.",
                'data_changes' => [
                    'nomor_ref' => $nomorRef,
                    'nomor_urutref' => $nomorUrut,
                ],
                'ip_address' => $ip,
            ]);
        });
    }

    /**
     * Hold pengajuan paklaring
     */
    public static function hold(Paklaring $paklaring, User $user, string $notes, ?string $ip = null): void
    {
        DB::transaction(function () use ($paklaring, $user, $notes, $ip) {
            $currentStep = self::getCurrentStep($paklaring);
            $stepName = $currentStep ? $currentStep->step_name : $paklaring->status_bagian;

            $paklaring->update([
                'status' => 'HOLD',
                'pengguna' => $user->name,
            ]);

            PaklaringApproval::create([
                'paklaring_id' => $paklaring->id,
                'step_order' => $paklaring->current_step_order,
                'step_name' => $stepName,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'user_role' => $user->role,
                'action' => 'hold',
                'notes' => $notes,
                'ip_address' => $ip,
            ]);
        });
    }

    /**
     * Tolak pengajuan paklaring
     */
    public static function reject(Paklaring $paklaring, User $user, string $notes, ?string $ip = null): void
    {
        DB::transaction(function () use ($paklaring, $user, $notes, $ip) {
            $currentStep = self::getCurrentStep($paklaring);
            $stepName = $currentStep ? $currentStep->step_name : $paklaring->status_bagian;

            $paklaring->update([
                'status' => 'Tolak',
                'alasan_penolakan' => $notes,
                'pengguna' => $user->name,
            ]);

            PaklaringApproval::create([
                'paklaring_id' => $paklaring->id,
                'step_order' => $paklaring->current_step_order,
                'step_name' => $stepName,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'user_role' => $user->role,
                'action' => 'rejected',
                'notes' => $notes,
                'ip_address' => $ip,
            ]);
        });
    }

    /**
     * Kembalikan pengajuan (Return Back) ke Area atau HRD
     */
    public static function returnBack(Paklaring $paklaring, User $user, string $targetBagian, string $notes, ?string $ip = null): void
    {
        $targetStepOrder = ($targetBagian === 'Area') ? 1 : 2;

        DB::transaction(function () use ($paklaring, $user, $targetBagian, $targetStepOrder, $notes, $ip) {
            $currentStep = self::getCurrentStep($paklaring);
            $stepName = $currentStep ? $currentStep->step_name : $paklaring->status_bagian;

            $paklaring->update([
                'status_bagian' => $targetBagian,
                'status' => 'Proses',
                'current_step_order' => $targetStepOrder,
                'pengguna' => $user->name,
            ]);

            PaklaringApproval::create([
                'paklaring_id' => $paklaring->id,
                'step_order' => $paklaring->current_step_order,
                'step_name' => $stepName,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'user_role' => $user->role,
                'action' => 'returned',
                'action_to' => $targetBagian,
                'notes' => $notes,
                'ip_address' => $ip,
            ]);
        });
    }
}
