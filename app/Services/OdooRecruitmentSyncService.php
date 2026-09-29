<?php

namespace App\Services;

use App\Models\Candidate;
use App\Models\OdooEntity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class OdooRecruitmentSyncService
{
    /**
     * Sinkronisasi seluruh kandidat portal dengan tahapan rekrutmen Odoo.
     *
     * @param callable|null $progressCallback Callback progres (type, message, meta)
     * @param int $limit Maksimal kandidat yang diproses (0 = tanpa limit)
     * @param bool $includeArchived Apakah memeriksa kandidat yang sudah diarsipkan
     */
    public function syncAllCandidates(?callable $progressCallback = null, int $limit = 1000, bool $includeArchived = false, string $scope = 'all'): array
    {
        $log = function(string $type, string $message, array $meta = []) use ($progressCallback) {
            if ($progressCallback && is_callable($progressCallback)) {
                call_user_func($progressCallback, $type, $message, $meta);
            }
        };

        $scopeLabel = $scope === 'interview' ? 'Kandidat Interview (Inhouse)' : ($scope === 'portal' ? 'Kandidat Job Portal' : 'Semua Kandidat (Portal & Interview)');
        $log('info', "Memulai sinkronisasi tahapan rekrutmen Odoo ERP dengan {$scopeLabel}...");

        $activeEntities = OdooEntity::where('is_active', true)->get();
        if ($activeEntities->isEmpty()) {
            $log('error', 'Tidak ada entitas Odoo yang aktif.');
            return [
                'success' => false,
                'message' => 'Tidak ada entitas Odoo yang aktif untuk sinkronisasi.',
                'stats' => [],
            ];
        }

        // Ambil kandidat yang memiliki NIK
        $query = Candidate::whereNotNull('nik')
            ->where('nik', '!=', '')
            ->whereRaw('LENGTH(TRIM(nik)) >= 10');

        if ($scope === 'portal') {
            $query->where('jenis', 'Job Portal');
        } elseif ($scope === 'interview') {
            $query->where(function ($q) {
                $q->whereNull('jenis')->orWhere('jenis', '');
            });
        }

        if (!$includeArchived) {
            $query->where(function ($q) {
                $q->whereNull('status_kandidat')
                  ->orWhereNotIn('status_kandidat', ['Arsip']);
            })->whereNotIn('status', ['Arsip', 'archived']);
        }

        // Urutkan yang belum pernah disinkronkan atau paling lama disinkronkan
        $candidates = $query->orderByRaw('odoo_synced_at IS NULL DESC, odoo_synced_at ASC, id DESC')
            ->when($limit > 0, fn($q) => $q->take($limit))
            ->get();

        $totalCandidates = $candidates->count();
        $log('info', "Ditemukan {$totalCandidates} {$scopeLabel} untuk dicocokkan ke Odoo.");

        if ($totalCandidates === 0) {
            // Jalankan auto-archive meskipun tidak ada kandidat yang perlu dicocokkan
            $autoArchivedCount = $this->runAutoArchiveRule($log);
            return [
                'success' => true,
                'total' => 0,
                'matched' => 0,
                'moved_interview' => 0,
                'moved_terima' => 0,
                'auto_archived' => $autoArchivedCount,
            ];
        }

        // Buat mapping NIK -> Collection Candidate
        $nikMap = [];
        foreach ($candidates as $cand) {
            $cleanNik = trim($cand->nik);
            if (!isset($nikMap[$cleanNik])) {
                $nikMap[$cleanNik] = [];
            }
            $nikMap[$cleanNik][] = $cand;
        }

        $allNiks = array_keys($nikMap);
        $nikChunks = array_chunk($allNiks, 150);

        $matchedCount = 0;
        $movedInterviewCount = 0;
        $movedTerimaCount = 0;
        $movedArsipCount = 0;
        $unmodifiedCount = 0;

        // Kumpulkan semua kecocokan pelamar Odoo dari seluruh entitas
        $allApplicantMatches = [];

        // Iterasi entitas Odoo (AMK, AKP, ATK, ABO, ATB)
        foreach ($activeEntities as $entity) {
            if (!$entity->isConfigured()) {
                continue;
            }

            try {
                $service = OdooSyncService::fromEntity($entity);
                if (!$service) {
                    continue;
                }
                $uid = $service->authenticate();
            } catch (\Throwable $e) {
                $log('warning', "Gagal terhubung ke Odoo {$entity->code}: " . $e->getMessage());
                continue;
            }

            $log('info', "Memeriksa kecocokan NIK di Odoo [{$entity->code}] {$entity->name}...");

            foreach ($nikChunks as $chunkIndex => $chunk) {
                try {
                    // Query batch NIK ke hr.applicant
                    $applicants = $service->xmlRpcCall('/xmlrpc/2/object', 'execute_kw', [
                        $entity->odoo_db, $uid, $entity->odoo_api_key,
                        'hr.applicant', 'search_read',
                        [[
                            ['no_ktp', 'in', $chunk],
                        ]],
                        [
                            'fields' => [
                                'id', 'name', 'partner_name', 'no_ktp', 'stage_id',
                                'job_id', 'department_id', 'user_id', 'write_date', 'create_date',
                                'active',
                            ],
                            'context' => ['active_test' => false], // Ambil juga yang inactive/refused
                            'order' => 'write_date desc, id desc',
                        ]
                    ]);

                    if (!is_array($applicants) || empty($applicants)) {
                        continue;
                    }

                    foreach ($applicants as $app) {
                        $appKtp = trim((string)($app['no_ktp'] ?? ''));
                        if (empty($appKtp) || !isset($nikMap[$appKtp])) {
                            continue;
                        }

                        $allApplicantMatches[$appKtp][] = [
                            'app' => $app,
                            'entity' => $entity->code,
                        ];
                    }

                } catch (\Throwable $e) {
                    $log('warning', "Gagal memeriksa batch chunk #{$chunkIndex} di {$entity->code}: " . $e->getMessage());
                }
            }
        }

        // Sekarang proses setiap kandidat dengan record Odoo terbaik yang diprioritaskan
        foreach ($nikMap as $appKtp => $targetCandidates) {
            if (!empty($allApplicantMatches[$appKtp])) {
                $bestMatch = $this->selectBestOdooApplicant($allApplicantMatches[$appKtp], $appKtp);
                if ($bestMatch) {
                    foreach ($targetCandidates as $candidate) {
                        $res = $this->applyOdooApplicantData($candidate, $bestMatch['app'], $bestMatch['entity']);
                        $matchedCount++;

                        if ($res['status_changed']) {
                            if ($res['new_status'] === 'Interview') {
                                $movedInterviewCount++;
                                $log('stage_change', "👉 [{$bestMatch['entity']}] {$candidate->full_name} (NIK: {$candidate->nik}) beralih ke INTERVIEW (Odoo: {$res['odoo_stage']})");
                            } elseif ($res['new_status'] === 'Terima') {
                                $movedTerimaCount++;
                                $log('stage_change', "🎉 [{$bestMatch['entity']}] {$candidate->full_name} (NIK: {$candidate->nik}) beralih ke TERIMA (Odoo: {$res['odoo_stage']})");
                            } elseif ($res['new_status'] === 'Arsip') {
                                $movedArsipCount++;
                                $log('stage_change', "📦 [{$bestMatch['entity']}] {$candidate->full_name} (NIK: {$candidate->nik}) beralih ke ARSIP (Odoo: {$res['odoo_stage']})");
                            }
                        } else {
                            $unmodifiedCount++;
                        }
                    }

                    // Hapus dari nikMap setelah berhasil diproses agar tidak ditandai sbg belum pernah sinkron
                    unset($nikMap[$appKtp]);
                }
            }
        }

        // Tandai timestamp sinkronisasi untuk kandidat yang belum cocok di Odoo
        $remainingNiks = array_keys($nikMap);
        if (!empty($remainingNiks)) {
            foreach (array_chunk($remainingNiks, 400) as $chunkRemaining) {
                $remQuery = Candidate::whereIn('nik', $chunkRemaining)
                    ->whereNull('odoo_synced_at');
                if ($scope === 'portal') {
                    $remQuery->where('jenis', 'Job Portal');
                } elseif ($scope === 'interview') {
                    $remQuery->where(function ($q) {
                        $q->whereNull('jenis')->orWhere('jenis', '');
                    });
                }
                $remQuery->update(['odoo_synced_at' => now()]);
            }
        }

        // Jalankan aturan Auto-Archive 14 Hari untuk kandidat Job Portal yang tidak ada update
        $autoArchivedCount = 0;
        if ($scope === 'portal' || $scope === 'all') {
            $autoArchivedCount = $this->runAutoArchiveRule($log);
        }

        $log('success', "Sinkronisasi selesai! Total Cocok di Odoo: {$matchedCount}, Pindah Interview: {$movedInterviewCount}, Pindah Terima: {$movedTerimaCount}, Auto-Arsip (>14 hari): {$autoArchivedCount}");

        return [
            'success'         => true,
            'total_checked'   => $totalCandidates,
            'matched'         => $matchedCount,
            'moved_interview' => $movedInterviewCount,
            'moved_terima'    => $movedTerimaCount,
            'moved_arsip'     => $movedArsipCount,
            'auto_archived'   => $autoArchivedCount,
        ];
    }

    /**
     * Sinkronisasi status Odoo untuk satu kandidat tertentu.
     */
    public function syncSingleCandidate(Candidate $candidate): array
    {
        $nik = trim($candidate->nik ?? '');
        if (empty($nik)) {
            return [
                'success' => false,
                'message' => 'Kandidat tidak memiliki NIK / No. KTP.',
            ];
        }

        $activeEntities = OdooEntity::where('is_active', true)->get();
        if ($activeEntities->isEmpty()) {
            return [
                'success' => false,
                'message' => 'Tidak ada entitas Odoo yang aktif.',
            ];
        }

        $allMatches = [];

        foreach ($activeEntities as $entity) {
            if (!$entity->isConfigured()) {
                continue;
            }

            try {
                $service = OdooSyncService::fromEntity($entity);
                if (!$service) {
                    continue;
                }
                $uid = $service->authenticate();

                $applicants = $service->xmlRpcCall('/xmlrpc/2/object', 'execute_kw', [
                    $entity->odoo_db, $uid, $entity->odoo_api_key,
                    'hr.applicant', 'search_read',
                    [[
                        ['no_ktp', '=', $nik],
                    ]],
                    [
                        'fields' => [
                            'id', 'name', 'partner_name', 'no_ktp', 'stage_id',
                            'job_id', 'department_id', 'user_id', 'write_date', 'create_date',
                            'active',
                        ],
                        'context' => ['active_test' => false],
                        'order' => 'write_date desc, id desc',
                    ]
                ]);

                if (is_array($applicants) && !empty($applicants)) {
                    foreach ($applicants as $app) {
                        $allMatches[] = [
                            'app' => $app,
                            'entity' => $entity->code,
                        ];
                    }
                }
            } catch (\Throwable $e) {
                Log::warning("Gagal query Odoo {$entity->code} untuk NIK {$nik}: " . $e->getMessage());
            }
        }

        if (!empty($allMatches)) {
            $bestMatch = $this->selectBestOdooApplicant($allMatches, $nik);
            if ($bestMatch) {
                $res = $this->applyOdooApplicantData($candidate, $bestMatch['app'], $bestMatch['entity']);
                $resignSuffix = ($res['is_resigned'] ?? false) ? ' (Status Karyawan: Resign)' : '';
                return [
                    'success'        => true,
                    'found'          => true,
                    'entity'         => $bestMatch['entity'],
                    'odoo_stage'     => $candidate->odoo_stage_name,
                    'status_kandidat'=> $candidate->status_kandidat,
                    'status_changed' => $res['status_changed'],
                    'is_resigned'    => $res['is_resigned'] ?? false,
                    'message'        => "Kandidat cocok dengan data Odoo [{$bestMatch['entity']}]. Tahapan: {$candidate->odoo_stage_name}.{$resignSuffix}",
                ];
            }
        }

        // Jika tidak ditemukan di Odoo, perbarui tanggal cek
        $candidate->odoo_synced_at = now();
        $candidate->saveQuietly();

        return [
            'success' => true,
            'found'   => false,
            'message' => 'NIK tidak ditemukan pada data rekrutmen Odoo di seluruh entitas aktif.',
        ];
    }

    /**
     * Memetakan data pelamar Odoo ke kandidat dan memperbarui status_kandidat.
     */
    public function applyOdooApplicantData(Candidate $candidate, array $odooApp, string $entityCode): array
    {
        $oldStatus = $candidate->status_kandidat;
        $applicantId = $odooApp['id'] ?? null;
        $isActive = (bool)($odooApp['active'] ?? true);

        // Ambil stage Odoo
        $stageId = null;
        $stageName = 'Data Pelamar';
        if (isset($odooApp['stage_id']) && is_array($odooApp['stage_id'])) {
            $stageId = (int)$odooApp['stage_id'][0];
            $stageName = trim((string)$odooApp['stage_id'][1]);
        }

        $newStatus = $oldStatus;
        $stageLower = strtolower($stageName);

        // Cek status resign karyawan untuk NIK ini
        $empStatus = self::checkEmployeeResignStatus($candidate->nik ?? '');
        $isEmpResigned = ($empStatus['is_resigned'] && !$empStatus['has_active']);

        // Periksa apakah data pelamar Odoo ini adalah berkas historis masa lalu (bukan untuk lamaran saat ini)
        $appCreateDate = !empty($odooApp['create_date']) ? Carbon::parse($odooApp['create_date']) : null;
        $candidateApplyDate = $candidate->created_at ? Carbon::parse($candidate->created_at) : null;

        $isHistoricalPastRecord = false;
        if ($appCreateDate && $candidateApplyDate) {
            // Jika record pelamar di Odoo dibuat jauh sebelum pelamar mendaftar ke lowongan ini (> 30 hari)
            if ($appCreateDate->lt($candidateApplyDate->copy()->subDays(30))) {
                $isHistoricalPastRecord = true;
            }
        }

        // ATURAN 1: Refused / Inactive di Odoo -> Pindah ke Arsip (hanya jika bukan berkas historis lama saat kandidat mendaftar baru)
        if (!$isActive || str_contains($stageLower, 'refuse') || str_contains($stageLower, 'tolak')) {
            if (!$isHistoricalPastRecord || $oldStatus !== 'Baru') {
                $newStatus = 'Arsip';
                $candidate->archive_reason = 'Ditolak / Di-arsip pada sistem Odoo Recruitment';
                $candidate->status = 'Arsip';
            }
        }
        // ATURAN 2: Joined -> Pindah ke Terima KECUALI jika karyawan di master employee sudah RESIGN
        elseif (str_contains($stageLower, 'joined')) {
            if ($isEmpResigned) {
                // Karyawan sudah resign! Jangan pindahkan ke 'Terima'
                // Pertahankan status rekrutmen saat ini (Baru atau Interview)
                if (in_array($oldStatus, ['Baru', 'Interview'])) {
                    $newStatus = $oldStatus;
                } else {
                    $newStatus = 'Baru';
                }
                $stageName = 'Joined (Resign)';
                if (empty($candidate->status) || $candidate->status === 'Arsip' || $candidate->status === 'archived') {
                    $candidate->status = 'Active';
                }
            } else {
                $newStatus = 'Terima';
                if (empty($candidate->status) || $candidate->status === 'Arsip' || $candidate->status === 'archived') {
                    $candidate->status = 'Active';
                }
            }
        }
        // ATURAN 3: First Interview, Second Interview, Principal, E-Learning, Pembuatan PKWT, Pending PKWT -> Pindah ke Interview
        elseif (
            str_contains($stageLower, 'interview') ||
            str_contains($stageLower, 'principal') ||
            str_contains($stageLower, 'learning') ||
            str_contains($stageLower, 'elearning') ||
            str_contains($stageLower, 'pkwt')
        ) {
            // Jika saat ini masih Baru (atau belum ditentukan), naikkan ke Interview
            if ($oldStatus === 'Baru' || empty($oldStatus)) {
                $newStatus = 'Interview';
            }
            // Jika sudah Terima di lokal tapi di Odoo belum Joined (misal masih PKWT), pertahankan Terima
            elseif ($oldStatus === 'Terima') {
                // Biarkan tetap Terima
            } else {
                $newStatus = 'Interview';
            }
            if ($candidate->status === 'Arsip' || $candidate->status === 'archived') {
                $candidate->status = 'Active';
            }
        }
        // ATURAN 4: Data Pelamar / Initial Qualification -> Tetap di Baru jika belum interview
        elseif (str_contains($stageLower, 'pelamar') || str_contains($stageLower, 'initial')) {
            if (empty($oldStatus)) {
                $newStatus = 'Baru';
            }
        }

        // Simpan data Odoo ke record kandidat (SELALU simpan agar UI menampilkan tahapan di Odoo)
        $candidate->odoo_applicant_id = $applicantId;
        $candidate->odoo_entity = $entityCode;
        $candidate->odoo_stage_id = $stageId;
        $candidate->odoo_stage_name = $stageName;
        $candidate->odoo_synced_at = now();
        $candidate->odoo_applicant_data = [
            'is_historical'          => $isHistoricalPastRecord,
            'is_employee_resigned'  => $isEmpResigned,
            'employee_status'       => $isEmpResigned ? 'Resign' : ($empStatus['has_active'] ? 'Aktiv' : null),
            'employee_entity'       => $empStatus['resigned_entity'] ?? $empStatus['active_entity'] ?? null,
            'job'                   => isset($odooApp['job_id']) && is_array($odooApp['job_id']) ? $odooApp['job_id'][1] : null,
            'department'            => isset($odooApp['department_id']) && is_array($odooApp['department_id']) ? $odooApp['department_id'][1] : null,
            'recruiter'             => isset($odooApp['user_id']) && is_array($odooApp['user_id']) ? $odooApp['user_id'][1] : null,
            'create_date'           => $odooApp['create_date'] ?? null,
            'write_date'            => $odooApp['write_date'] ?? null,
            'active'                => $isActive,
        ];

        $statusChanged = ($newStatus !== $oldStatus);
        $candidate->status_kandidat = $newStatus;
        $candidate->saveQuietly();

        return [
            'status_changed' => $statusChanged,
            'old_status'     => $oldStatus,
            'new_status'     => $newStatus,
            'odoo_stage'     => $stageName,
            'is_historical'  => $isHistoricalPastRecord,
            'is_resigned'    => $isEmpResigned,
        ];
    }

    /**
     * Memeriksa apakah NIK ini terdaftar sebagai karyawan dengan status Resign.
     */
    public static function checkEmployeeResignStatus(?string $nik): array
    {
        $cleanNik = trim($nik ?? '');
        if (empty($cleanNik)) {
            return ['is_resigned' => false, 'has_active' => false, 'resigned_entity' => null, 'active_entity' => null];
        }

        $employees = \App\Models\Employee::where('nik', $cleanNik)->get();
        if ($employees->isEmpty()) {
            return ['is_resigned' => false, 'has_active' => false, 'resigned_entity' => null, 'active_entity' => null];
        }

        $activeEmp = $employees->first(function($e) {
            return in_array(strtolower(trim($e->status ?? '')), ['aktiv', 'active']);
        });

        $resignedEmp = $employees->first(function($e) {
            return in_array(strtolower(trim($e->status ?? '')), ['resign', 'non-aktif', 'inactive']);
        });

        return [
            'is_resigned'     => ($resignedEmp && !$activeEmp),
            'has_active'      => (bool)$activeEmp,
            'resigned_entity' => $resignedEmp?->entity,
            'active_entity'   => $activeEmp?->entity,
        ];
    }

    /**
     * Memilih record pelamar Odoo terbaik dari berbagai entitas:
     * - Memprioritaskan tahapan rekrutmen aktif (Interview, Data Pelamar, PKWT, dll)
     * - Menurunkan prioritas Joined jika kandidat sudah berstatus RESIGN di data karyawan
     * - Memprioritaskan tanggal terbaru (create_date / id)
     */
    public function selectBestOdooApplicant(array $matches, string $nik): ?array
    {
        if (empty($matches)) {
            return null;
        }

        $empStatus = self::checkEmployeeResignStatus($nik);

        usort($matches, function ($a, $b) use ($empStatus) {
            $scoreA = $this->scoreApplicant($a['app'], $a['entity'], $empStatus);
            $scoreB = $this->scoreApplicant($b['app'], $b['entity'], $empStatus);

            if ($scoreA === $scoreB) {
                $createA = $a['app']['create_date'] ?? '';
                $createB = $b['app']['create_date'] ?? '';
                if ($createA !== $createB) {
                    return strcmp($createB, $createA);
                }
                return ($b['app']['id'] ?? 0) <=> ($a['app']['id'] ?? 0);
            }

            return $scoreB <=> $scoreA;
        });

        return $matches[0];
    }

    /**
     * Memberikan skor prioritas pada record pelamar Odoo.
     */
    public function scoreApplicant(array $app, string $entityCode, array $empStatus): int
    {
        $stageName = is_array($app['stage_id'] ?? null) ? ($app['stage_id'][1] ?? '') : (string)($app['stage_id'] ?? '');
        $stageLower = strtolower(trim($stageName));
        $isActive = (bool)($app['active'] ?? true);

        // Tahap aktif dalam proses rekrutmen: Interview, Psikotes, PKWT, Learning, Data Pelamar
        $isRecruitmentPipeline = (
            str_contains($stageLower, 'interview') ||
            str_contains($stageLower, 'principal') ||
            str_contains($stageLower, 'learning') ||
            str_contains($stageLower, 'elearning') ||
            str_contains($stageLower, 'pkwt') ||
            str_contains($stageLower, 'pelamar') ||
            str_contains($stageLower, 'initial') ||
            str_contains($stageLower, 'qualification') ||
            str_contains($stageLower, 'screening')
        );

        if ($isRecruitmentPipeline && $isActive) {
            if (str_contains($stageLower, 'interview') || str_contains($stageLower, 'principal') || str_contains($stageLower, 'pkwt')) {
                return 1200;
            }
            return 1000;
        }

        // Tahap Joined:
        if (str_contains($stageLower, 'joined')) {
            // Jika employee sudah resign, ini adalah berkas lama mantan karyawan
            if ($empStatus['is_resigned'] && !$empStatus['has_active']) {
                return 200; // Prioritas jauh lebih rendah dibanding data pelamar / rekrutmen aktif
            }
            // Jika employee aktif, Joined adalah status valid
            return 800;
        }

        // Tahap Ditolak / Refused / Inaktif
        if (!$isActive || str_contains($stageLower, 'refuse') || str_contains($stageLower, 'tolak')) {
            return 100;
        }

        return 300;
    }

    /**
     * Menerapkan Aturan Auto-Archive:
     * Jika dalam rentang 2 minggu (14 hari) dari data masuk kandidat belum ada update (masih di step Baru)
     * maka datanya akan otomatis pindah ke Arsip.
     */
    public function runAutoArchiveRule(?callable $logCallback = null): int
    {
        $log = function(string $type, string $message, array $meta = []) use ($logCallback) {
            if ($logCallback && is_callable($logCallback)) {
                call_user_func($logCallback, $type, $message, $meta);
            }
        };

        $cutoffDate = Carbon::now()->subDays(14);

        // Ambil kandidat dengan jenis 'Job Portal', status 'Baru' (atau null),
        // terdaftar lebih dari 14 hari yang lalu, dan tidak sedang dalam tahapan Interview/Terima
        $expiredCandidates = Candidate::where('jenis', 'Job Portal')
            ->where(function ($q) {
                $q->where('status_kandidat', 'Baru')
                  ->orWhereNull('status_kandidat');
            })
            ->where('created_at', '<=', $cutoffDate)
            ->where(function ($q) {
                // Belum ada progres interview/terima di Odoo
                $q->whereNull('odoo_stage_name')
                  ->orWhere('odoo_stage_name', 'Data Pelamar')
                  ->orWhere('odoo_stage_name', 'Initial Qualification');
            })
            ->get();

        $count = $expiredCandidates->count();
        if ($count > 0) {
            $log('info', "Menjalankan aturan auto-archive: {$count} kandidat terdaftar lebih dari 14 hari tanpa pembaruan tahap seleksi.");

            foreach ($expiredCandidates as $cand) {
                $cand->status_kandidat = 'Arsip';
                $cand->status = 'Arsip';
                $cand->archive_reason = 'Otomatis diarsipkan: Tidak ada update tahapan seleksi dalam 14 hari sejak pendaftaran';
                $cand->saveQuietly();

                // Selaraskan duplikat record dengan NIK yang sama agar tidak tertinggal status Active
                if (!empty($cand->nik)) {
                    Candidate::where('nik', $cand->nik)
                        ->where('id', '!=', $cand->id)
                        ->update([
                            'status' => 'Arsip',
                            'status_kandidat' => 'Arsip',
                            'archive_reason' => 'Otomatis diarsipkan: Tidak ada update tahapan seleksi dalam 14 hari sejak pendaftaran',
                        ]);
                }
            }

            $log('auto_archive', "📦 Berhasil mengarsipkan {$count} kandidat yang tidak aktif selama lebih dari 14 hari.");
        }

        return $count;
    }
}
