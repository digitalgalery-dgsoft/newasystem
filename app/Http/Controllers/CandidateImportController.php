<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Candidate;
use App\Models\Principle;
use App\Models\OdooEntity;
use App\Models\TestResult;
use App\Models\InterviewAssessment;
use App\Models\PrincipleApproval;
use App\Models\WorkExperience;
use App\Models\Employee;
use App\Services\CandidateImportService;
use App\Services\OdooSyncService;
use App\Services\OdooRecruitmentSyncService;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Exception;

class CandidateImportController extends Controller
{
    protected CandidateImportService $importService;

    public function __construct(CandidateImportService $importService)
    {
        $this->importService = $importService;
    }

    private function getCurrentUser(): ?User
    {
        return auth()->user();
    }

    /**
     * Unduh Template Excel hr.applicant.xlsx
     */
    public function downloadTemplate()
    {
        $templatePath = public_path('templates/template_import_kandidat.xlsx');

        if (!file_exists($templatePath)) {
            $templatePath = storage_path('app/templates/template_import_kandidat.xlsx');
        }

        if (!file_exists($templatePath)) {
            // Coba salin dari file master jika tersedia di sistem
            $masterSource = 'D:/Documents/Downloads/hr.applicant.xlsx';
            if (file_exists($masterSource)) {
                File::ensureDirectoryExists(dirname($templatePath));
                copy($masterSource, $templatePath);
            }
        }

        if (!file_exists($templatePath)) {
            return back()->with('error', 'File template import belum tersedia di server.');
        }

        return response()->download($templatePath, 'hr.applicant.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Upload file Excel sementara untuk diproses via Terminal Streaming
     */
    public function upload(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|max:30720', // Max 30MB
        ]);

        $file = $request->file('excel_file');
        $extension = strtolower($file->getClientOriginalExtension());

        if ($extension !== 'xlsx') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya file berformat .xlsx yang diperbolehkan!',
            ], 422);
        }

        $tempDir = storage_path('app/temp_imports');
        File::ensureDirectoryExists($tempDir);

        // Hapus file lama yang berusia lebih dari 2 jam
        foreach (glob("{$tempDir}/*") as $oldFile) {
            if (is_file($oldFile) && (time() - filemtime($oldFile) > 7200)) {
                @unlink($oldFile);
            }
        }

        $token = 'cand_imp_' . uniqid() . '_' . time();
        $fileName = "{$token}.xlsx";
        $file->move($tempDir, $fileName);

        return response()->json([
            'success'  => true,
            'token'    => $token,
            'filename' => $file->getClientOriginalName(),
            'stream_url' => route('interview.import.stream', ['token' => $token]),
        ]);
    }

    /**
     * SSE Streaming Terminal Endpoint
     */
    public function stream(Request $request)
    {
        $token = preg_replace('/[^a-zA-Z0-9_-]/', '', (string)$request->query('token'));
        $filePath = storage_path("app/temp_imports/{$token}.xlsx");

        $user = $this->getCurrentUser();
        $userEmail = $user ? $user->email : 'recruitment@asystem.co.id';
        $userId = $user ? $user->id : null;

        return response()->stream(function () use ($filePath, $userEmail, $userId) {
            // Nonaktifkan buffering output PHP & webserver
            if (function_exists('apache_setenv')) {
                @apache_setenv('no-gzip', '1');
            }
            @ini_set('zlib.output_compression', '0');
            @ini_set('implicit_flush', '1');
            while (ob_get_level() > 0) {
                @ob_end_flush();
            }
            @ob_implicit_flush(1);
            set_time_limit(0);

            $sendEvent = function(string $type, string $message, ?array $meta = null) {
                $payload = [
                    'time'    => date('H:i:s'),
                    'type'    => $type,
                    'message' => $message,
                    'meta'    => $meta,
                ];
                echo "data: " . json_encode($payload) . "\n\n";
                if (ob_get_level() > 0) {
                    @ob_flush();
                }
                @flush();
            };

            $sendEvent('init', "Memulai konsol terminal import kandidat walkin interview...");
            $sendEvent('info', "Rekruter penanggung jawab: {$userEmail}");

            if (!file_exists($filePath)) {
                $sendEvent('error', "File batch import tidak ditemukan atau sesi upload telah kadaluarsa.");
                $sendEvent('complete', "Import dibatalkan.", ['total' => 0, 'success' => 0, 'failed' => 0]);
                return;
            }

            try {
                $this->importService->import($filePath, $userEmail, $userId, $sendEvent);
            } catch (Exception $e) {
                $sendEvent('error', "Terjadi kesalahan sistem saat memproses file: " . $e->getMessage());
                $sendEvent('complete', "Import selesai dengan galat fatal.", ['error' => $e->getMessage()]);
            } finally {
                // Bersihkan file sementara
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }

        }, 200, [
            'Content-Type'      => 'text/event-stream',
            'Cache-Control'     => 'no-cache, no-transform',
            'Connection'        => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    /**
     * Cari Pelamar di Rekrutmen Odoo ERP berdasarkan NIK
     */
    public function lookupOdooByNik(Request $request)
    {
        $rawNik = trim((string)$request->input('nik'));
        $cleanNik = preg_replace('/\D/', '', $rawNik);

        if (empty($cleanNik) || strlen($cleanNik) !== 16) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor NIK / KTP harus terdiri dari 16 digit angka.',
            ], 200);
        }

        $entityCode = strtoupper(trim((string)$request->input('entity', 'all')));

        $query = OdooEntity::where('is_active', true);
        if ($entityCode !== 'ALL' && !empty($entityCode)) {
            $query->where('code', $entityCode);
        }
        $entities = $query->get();

        if ($entities->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada entitas Odoo ERP yang aktif.',
            ], 200);
        }

        $allMatchingApplicants = [];

        foreach ($entities as $entity) {
            if (!$entity->isConfigured()) {
                continue;
            }

            try {
                $service = OdooSyncService::fromEntity($entity);
                if (!$service) {
                    continue;
                }
                $uid = $service->authenticate();

                $applicantFields = [
                    'id', 'name', 'partner_name', 'no_ktp', 'no_kk', 'email_from',
                    'partner_phone', 'partner_mobile', 'birth', 'place_of_birth',
                    'ktp_address', 'gender', 'height', 'weight', 'religion',
                    'marital_status', 'type_id', 'job_id', 'principle_id',
                    'area_id', 'department_id', 'stage_id', 'user_id',
                    'write_date', 'create_date', 'active',
                ];

                // 1. Cari berdasarkan No. KTP di entitas ini
                $applicants = $service->xmlRpcCall('/xmlrpc/2/object', 'execute_kw', [
                    $entity->odoo_db, $uid, $entity->odoo_api_key,
                    'hr.applicant', 'search_read',
                    [[['no_ktp', '=', $cleanNik]]],
                    [
                        'fields' => $applicantFields,
                        'context' => ['active_test' => false],
                        'order' => 'create_date desc, id desc',
                    ]
                ]);

                if (is_array($applicants) && !empty($applicants)) {
                    foreach ($applicants as $app) {
                        $allMatchingApplicants[] = [
                            'app' => $app,
                            'entity' => $entity->code,
                            'found_via' => 'no_ktp',
                        ];
                    }
                }

                // 2. Jika tidak ditemukan via no_ktp di entitas ini, cari via No. KK
                if (empty($applicants)) {
                    $applicantsKk = $service->xmlRpcCall('/xmlrpc/2/object', 'execute_kw', [
                        $entity->odoo_db, $uid, $entity->odoo_api_key,
                        'hr.applicant', 'search_read',
                        [[['no_kk', '=', $cleanNik]]],
                        [
                            'fields' => $applicantFields,
                            'context' => ['active_test' => false],
                            'order' => 'create_date desc, id desc',
                        ]
                    ]);

                    if (is_array($applicantsKk) && !empty($applicantsKk)) {
                        foreach ($applicantsKk as $app) {
                            $allMatchingApplicants[] = [
                                'app' => $app,
                                'entity' => $entity->code,
                                'found_via' => 'no_kk',
                            ];
                        }
                    }
                }
            } catch (\Throwable $e) {
                continue;
            }
        }

        if (empty($allMatchingApplicants)) {
            return response()->json([
                'success' => false,
                'message' => "NIK / No. KK {$cleanNik} tidak ditemukan di modul Rekrutmen Odoo ERP (AMK, AKP, ATK, ABO, ATB). Pastikan pelamar sudah diinput di Odoo atau periksa kembali nomor NIK/KK.",
            ], 200);
        }

        // Gunakan pemeringkatan prioritas OdooRecruitmentSyncService:
        // Memprioritaskan tahap rekrutmen aktif (Data Pelamar, Interview) di atas Joined jika kandidat sudah RESIGN
        $syncService = app(OdooRecruitmentSyncService::class);
        $bestMatch = $syncService->selectBestOdooApplicant($allMatchingApplicants, $cleanNik);

        $foundApplicant = $bestMatch['app'];
        $foundEntity = $bestMatch['entity'];
        $foundVia = $bestMatch['found_via'] ?? 'no_ktp';

        // Format data yang ditemukan
        $rawFoundKtp = preg_replace('/\D/', '', (string)($foundApplicant['no_ktp'] ?? ''));
        $rawFoundKk = preg_replace('/\D/', '', (string)($foundApplicant['no_kk'] ?? ''));
        $targetNik = (strlen($rawFoundKtp) === 16) ? $rawFoundKtp : $cleanNik;
        $targetKk = (strlen($rawFoundKk) === 16) ? $rawFoundKk : null;

        $name = ucwords(strtolower(trim((string)($foundApplicant['partner_name'] ?: $foundApplicant['name']))));
        $job = is_array($foundApplicant['job_id']) ? $foundApplicant['job_id'][1] : (string)($foundApplicant['job_id'] ?? '-');
        $principle = is_array($foundApplicant['principle_id']) ? $foundApplicant['principle_id'][1] : (string)($foundApplicant['principle_id'] ?? '-');
        $area = is_array($foundApplicant['area_id']) ? $foundApplicant['area_id'][1] : (string)($foundApplicant['area_id'] ?? '-');
        $stage = is_array($foundApplicant['stage_id']) ? $foundApplicant['stage_id'][1] : (string)($foundApplicant['stage_id'] ?? 'Data Pelamar');
        $phone = preg_replace('/[^0-9]/', '', (string)($foundApplicant['partner_mobile'] ?: $foundApplicant['partner_phone']));
        if (str_starts_with($phone, '62')) {
            $phone = '0' . substr($phone, 2);
        }

        $birth = $foundApplicant['birth'] ?: null;
        $age = null;
        if ($birth) {
            $age = \Carbon\Carbon::parse($birth)->age;
        }

        // Cek status resign mantan karyawan
        $empStatus = OdooRecruitmentSyncService::checkEmployeeResignStatus($targetNik);
        $isResigned = ($empStatus['is_resigned'] && !$empStatus['has_active']);

        if (str_contains(strtolower($stage), 'joined') && $isResigned) {
            $stage = 'Joined (Resign)';
        }

        $allMatchNiks = array_values(array_unique(array_filter([$cleanNik, $targetNik, $targetKk])));
        $existingCandidates = Candidate::whereIn('nik', $allMatchNiks)->orderBy('id')->get();

        // Cek apakah kandidat dengan NIK ini sudah pernah ada di database
        $hasExisting = $existingCandidates->isNotEmpty();
        $activeCandidate = $existingCandidates->first(function($c) {
            return ($c->status !== 'Arsip' && $c->status_kandidat !== 'Arsip');
        });
        $lastExisting = $existingCandidates->last();

        $existingInfo = null;
        if ($lastExisting) {
            $existingInfo = [
                'id'         => $lastExisting->id,
                'name'       => $lastExisting->full_name,
                'nik'        => $lastExisting->nik,
                'status'     => $lastExisting->status,
                'is_active'  => ($lastExisting->status !== 'Arsip' && $lastExisting->status_kandidat !== 'Arsip'),
                'created_at' => $lastExisting->created_at ? $lastExisting->created_at->format('d/m/Y H:i') : null,
            ];
        }

        $rawGender = strtolower(trim((string)($foundApplicant['gender'] ?? '')));
        $gender = 'Laki-laki';
        if (in_array($rawGender, ['female', 'perempuan', 'wanita', 'p', 'f'])) {
            $gender = 'Perempuan';
        } elseif (in_array($rawGender, ['male', 'laki-laki', 'pria', 'l', 'm'])) {
            $gender = 'Laki-laki';
        } elseif (strlen($targetNik) >= 8) {
            $day = (int) substr($targetNik, 6, 2);
            if ($day > 40 && $day <= 71) {
                $gender = 'Perempuan';
            }
        }

        // Kumpulkan daftar riwayat lain jika ditemukan lebih dari 1 record di Odoo
        $otherRecords = [];
        foreach ($allMatchingApplicants as $match) {
            if ($match['app']['id'] !== $foundApplicant['id'] || $match['entity'] !== $foundEntity) {
                $otherStage = is_array($match['app']['stage_id'] ?? null) ? $match['app']['stage_id'][1] : ($match['app']['stage_id'] ?? '-');
                $otherJob = is_array($match['app']['job_id'] ?? null) ? $match['app']['job_id'][1] : ($match['app']['job_id'] ?? '-');
                $otherRecords[] = [
                    'id'          => $match['app']['id'],
                    'entity'      => $match['entity'],
                    'stage'       => $otherStage,
                    'job'         => $otherJob,
                    'create_date' => $match['app']['create_date'] ?? null,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Data pelamar ditemukan di Odoo [{$foundEntity}]!" . ($foundVia === 'no_kk' ? " (via No. KK)" : ""),
            'applicant' => [
                'odoo_id'         => $foundApplicant['id'],
                'entity'          => $foundEntity,
                'name'            => $name,
                'nik'             => $targetNik,
                'no_ktp'          => $targetNik,
                'no_kk'           => $targetKk,
                'searched_nik'    => $cleanNik,
                'found_via'       => $foundVia,
                'job'             => $job,
                'principle'       => $principle,
                'area'            => $area,
                'stage'           => $stage,
                'phone'           => $phone,
                'birth'           => $birth,
                'age'             => $age,
                'birth_place'     => $foundApplicant['place_of_birth'] ?? null,
                'address'         => $foundApplicant['ktp_address'] ?? null,
                'email'           => $foundApplicant['email_from'] ?? null,
                'gender'          => $gender,
                'is_resigned'     => $isResigned,
                'resigned_entity' => $empStatus['resigned_entity'],
            ],
            'is_blocked'          => false,
            'has_existing'        => $hasExisting,
            'is_active_existing'  => (bool)$activeCandidate,
            'existing_candidate'  => $existingInfo,
            'is_resigned'         => $isResigned,
            'resigned_entity'     => $empStatus['resigned_entity'],
            'total_odoo_records'  => count($allMatchingApplicants),
            'other_records'       => $otherRecords,
        ]);
    }

    /**
     * Tarik dan Simpan Pelamar dari Odoo ke ASystem & Siapkan Tes Online CBT
     */
    public function importOdooByNik(Request $request)
    {
        $rawNik = trim((string)$request->input('nik'));
        $cleanNik = preg_replace('/\D/', '', $rawNik);
        $rawSearched = trim((string)$request->input('searched_nik', ''));
        $cleanSearched = preg_replace('/\D/', '', $rawSearched);

        if (empty($cleanNik) || strlen($cleanNik) !== 16) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor NIK / KTP harus terdiri dari 16 digit angka.',
            ], 200);
        }

        $entityCode = strtoupper(trim((string)$request->input('entity', 'all')));

        $query = OdooEntity::where('is_active', true);
        if ($entityCode !== 'ALL' && !empty($entityCode)) {
            $query->where('code', $entityCode);
        }
        $entities = $query->get();

        $allMatchingApplicants = [];

        $searchNiks = array_values(array_unique(array_filter([$cleanNik, $cleanSearched])));

        $applicantFields = [
            'id', 'name', 'partner_name', 'no_ktp', 'no_kk', 'email_from',
            'partner_phone', 'partner_mobile', 'birth', 'place_of_birth',
            'ktp_address', 'gender', 'height', 'weight', 'religion',
            'marital_status', 'type_id', 'job_id', 'principle_id',
            'area_id', 'department_id', 'stage_id', 'user_id',
            'write_date', 'create_date', 'active',
        ];

        foreach ($entities as $entity) {
            if (!$entity->isConfigured()) {
                continue;
            }

            try {
                $service = OdooSyncService::fromEntity($entity);
                if (!$service) {
                    continue;
                }
                $uid = $service->authenticate();

                // 1. Cari berdasarkan no_ktp
                foreach ($searchNiks as $sNik) {
                    $applicants = $service->xmlRpcCall('/xmlrpc/2/object', 'execute_kw', [
                        $entity->odoo_db, $uid, $entity->odoo_api_key,
                        'hr.applicant', 'search_read',
                        [[['no_ktp', '=', $sNik]]],
                        [
                            'fields' => $applicantFields,
                            'context' => ['active_test' => false],
                            'order' => 'create_date desc, id desc',
                        ]
                    ]);
                    if (is_array($applicants) && !empty($applicants)) {
                        foreach ($applicants as $app) {
                            $allMatchingApplicants[] = [
                                'app' => $app,
                                'entity' => $entity->code,
                                'found_via' => 'no_ktp',
                            ];
                        }
                    }
                }

                // 2. Cari berdasarkan no_kk jika belum ada
                if (empty($applicants)) {
                    foreach ($searchNiks as $sNik) {
                        $applicantsKk = $service->xmlRpcCall('/xmlrpc/2/object', 'execute_kw', [
                            $entity->odoo_db, $uid, $entity->odoo_api_key,
                            'hr.applicant', 'search_read',
                            [[['no_kk', '=', $sNik]]],
                            [
                                'fields' => $applicantFields,
                                'context' => ['active_test' => false],
                                'order' => 'create_date desc, id desc',
                            ]
                        ]);
                        if (is_array($applicantsKk) && !empty($applicantsKk)) {
                            foreach ($applicantsKk as $app) {
                                $allMatchingApplicants[] = [
                                    'app' => $app,
                                    'entity' => $entity->code,
                                    'found_via' => 'no_kk',
                                ];
                            }
                        }
                    }
                }
            } catch (\Throwable $e) {
                continue;
            }
        }

        if (empty($allMatchingApplicants)) {
            return response()->json([
                'success' => false,
                'message' => "NIK {$cleanNik} tidak ditemukan di modul Rekrutmen Odoo ERP.",
            ], 200);
        }

        // Pilih record terbaik dengan prioritas tahap rekrutmen aktif
        $syncService = app(OdooRecruitmentSyncService::class);
        $bestMatch = $syncService->selectBestOdooApplicant($allMatchingApplicants, $cleanNik);

        $foundApplicant = $bestMatch['app'];
        $foundEntity = $bestMatch['entity'];

        $rawFoundKtp = preg_replace('/\D/', '', (string)($foundApplicant['no_ktp'] ?? ''));
        $rawFoundKk = preg_replace('/\D/', '', (string)($foundApplicant['no_kk'] ?? ''));
        $targetNik = (strlen($rawFoundKtp) === 16) ? $rawFoundKtp : $cleanNik;
        $targetKk = (strlen($rawFoundKk) === 16) ? $rawFoundKk : null;

        $name = ucwords(strtolower(trim((string)($foundApplicant['partner_name'] ?: $foundApplicant['name']))));
        if (empty($name)) {
            $name = 'Kandidat NIK ' . $targetNik;
        }

        $job = is_array($foundApplicant['job_id']) ? $foundApplicant['job_id'][1] : (string)($foundApplicant['job_id'] ?? 'Kandidat Odoo');
        $prinName = is_array($foundApplicant['principle_id']) ? $foundApplicant['principle_id'][1] : (string)($foundApplicant['principle_id'] ?? '');
        $area = is_array($foundApplicant['area_id']) ? $foundApplicant['area_id'][1] : (string)($foundApplicant['area_id'] ?? '');
        $stage = is_array($foundApplicant['stage_id']) ? $foundApplicant['stage_id'][1] : (string)($foundApplicant['stage_id'] ?? 'Data Pelamar');

        // Cek status resign mantan karyawan
        $empStatus = OdooRecruitmentSyncService::checkEmployeeResignStatus($targetNik);
        $isResigned = ($empStatus['is_resigned'] && !$empStatus['has_active']);

        $stageLower = strtolower($stage);
        if (str_contains($stageLower, 'joined')) {
            if ($isResigned) {
                $statusKandidat = 'Baru';
                $stage = 'Joined (Resign)';
            } else {
                $statusKandidat = 'Terima';
            }
        } elseif (
            str_contains($stageLower, 'interview') ||
            str_contains($stageLower, 'principal') ||
            str_contains($stageLower, 'learning') ||
            str_contains($stageLower, 'pkwt')
        ) {
            $statusKandidat = 'Interview';
        } elseif (str_contains($stageLower, 'refuse') || str_contains($stageLower, 'tolak')) {
            $statusKandidat = 'Arsip';
        } else {
            $statusKandidat = 'Baru';
        }

        // Cari principle_id di database lokal
        $principleId = null;
        if (!empty($prinName)) {
            $foundPrinciple = Principle::where('name', 'like', "%{$prinName}%")->first();
            if ($foundPrinciple) {
                $principleId = $foundPrinciple->id;
            }
        }

        // Normalisasi nomor telepon
        $phone = preg_replace('/[^0-9]/', '', (string)($foundApplicant['partner_mobile'] ?: $foundApplicant['partner_phone']));
        if (str_starts_with($phone, '62')) {
            $phone = '0' . substr($phone, 2);
        }

        // Tanggal lahir & password default
        $birthDate = !empty($foundApplicant['birth']) ? date('Y-m-d', strtotime($foundApplicant['birth'])) : null;
        $passwordPlain = $birthDate ? date('dmY', strtotime($birthDate)) : '12345678';
        $passwordHashed = bcrypt($passwordPlain);

        // Normalisasi jenis kelamin dari Odoo (female -> Perempuan, male -> Laki-laki)
        $rawGender = strtolower(trim((string)($foundApplicant['gender'] ?? '')));
        $gender = 'Laki-laki';
        if (in_array($rawGender, ['female', 'perempuan', 'wanita', 'p', 'f'])) {
            $gender = 'Perempuan';
        } elseif (in_array($rawGender, ['male', 'laki-laki', 'pria', 'l', 'm'])) {
            $gender = 'Laki-laki';
        } elseif (strlen($targetNik) >= 8) {
            $day = (int) substr($targetNik, 6, 2);
            if ($day > 40 && $day <= 71) {
                $gender = 'Perempuan';
            }
        }

        // Pengguna / Rekruter saat ini
        $user = $this->getCurrentUser();
        $userEmail = $user ? $user->email : 'recruitment@asystem.co.id';
        $userId = $user ? $user->id : null;

        try {
            DB::beginTransaction();

            $allPossibleNiks = array_values(array_unique(array_filter([$cleanNik, $cleanSearched, $targetNik, $targetKk])));
            $existingCandidates = Candidate::whereIn('nik', $allPossibleNiks)->orderBy('id')->get();
            $isReplaced = $existingCandidates->isNotEmpty();

            $hasTbKandidat = Schema::hasTable('tb_kandidat');
            $wasActiveArchived = false;

            $currentUserName = $user ? ($user->name ?? $user->email) : 'User';
            $currentUserEmail = $user ? $user->email : 'recruitment@asystem.co.id';
            $archiveExplanation = 'Otomatis diarsipkan (Auto Replace): Data kandidat baru ditambahkan oleh user ' . $currentUserName . ' (' . $currentUserEmail . ') pada ' . now()->format('d/m/Y H:i');

            // AUTO REPLACE: Setiap data kandidat yang ada sebelumnya dengan NIK ini yang belum berstatus Arsip otomatis diarsipkan
            if ($existingCandidates->isNotEmpty()) {
                foreach ($existingCandidates as $existingCand) {
                    if ($existingCand->status !== 'Arsip' || $existingCand->status_kandidat !== 'Arsip') {
                        $existingCand->status = 'Arsip';
                        if (Schema::hasColumn('candidates', 'status_kandidat')) {
                            $existingCand->status_kandidat = 'Arsip';
                        }
                        if (Schema::hasColumn('candidates', 'archive_reason')) {
                            $existingCand->archive_reason = $archiveExplanation;
                        }
                        if (Schema::hasColumn('candidates', 'status_replace')) {
                            $existingCand->status_replace = 'Replaced';
                        }
                        $existingCand->save();

                        if ($hasTbKandidat) {
                            $tbData = ['status' => 'Arsip'];
                            if (Schema::hasColumn('tb_kandidat', 'status_kandidat')) {
                                $tbData['status_kandidat'] = 'Arsip';
                            }
                            if (Schema::hasColumn('tb_kandidat', 'archive_reason')) {
                                $tbData['archive_reason'] = $archiveExplanation;
                            }
                            if (Schema::hasColumn('tb_kandidat', 'alasanarsip')) {
                                $tbData['alasanarsip'] = $archiveExplanation;
                            }
                            DB::table('tb_kandidat')
                                ->where('id', $existingCand->id)
                                ->orWhere('no_ktp', $existingCand->nik)
                                ->update($tbData);
                        }

                        ActivityLogger::log('ARCHIVE', 'Odoo Sync', "Mengarsipkan kandidat sebelumnya {$existingCand->full_name} ({$existingCand->id}) karena auto replace oleh user: {$currentUserName}.", $existingCand);
                        $wasActiveArchived = true;
                    }
                }
            }

            $rawEdu = is_array($foundApplicant['type_id']) ? $foundApplicant['type_id'][1] : ($foundApplicant['type_id'] ?? null);
            if ($rawEdu === false || $rawEdu === '0') {
                $rawEdu = null;
            }

            // Cari data existing candidate dengan NIK yang sama untuk mewarisi profil jika ada
            $existingProfileCand = Candidate::whereIn('nik', $allPossibleNiks)
                ->where(function ($q) {
                    $q->where('is_profile_complete', 1)
                      ->orWhereNotNull('mother_name')
                      ->orWhereNotNull('bank_name');
                })
                ->orderByDesc('is_profile_complete')
                ->orderByDesc('id')
                ->first();

            if (empty($rawEdu) && $existingProfileCand && !empty($existingProfileCand->education) && $existingProfileCand->education !== '0') {
                $rawEdu = $existingProfileCand->education;
            }

            $candidatePayload = [
                'nik'                      => $targetNik,
                'full_name'                => $name,
                'birth_place'              => $foundApplicant['place_of_birth'] ?? ($existingProfileCand?->birth_place ?? null),
                'birth_date'               => $birthDate ?: ($existingProfileCand?->birth_date ?? null),
                'address_ktp'              => $foundApplicant['ktp_address'] ?? ($existingProfileCand?->address_ktp ?? null),
                'address_domicile'         => $foundApplicant['ktp_address'] ?? ($existingProfileCand?->address_domicile ?? null),
                'gender'                   => $gender ?: ($existingProfileCand?->gender ?? null),
                'height'                   => (int)($foundApplicant['height'] ?? null) ?: ($existingProfileCand?->height ?? null),
                'weight'                   => (int)($foundApplicant['weight'] ?? null) ?: ($existingProfileCand?->weight ?? null),
                'religion'                 => $foundApplicant['religion'] ?? ($existingProfileCand?->religion ?? null),
                'marital_status'           => $foundApplicant['marital_status'] ?? ($existingProfileCand?->marital_status ?? null),
                'education'                => $rawEdu,
                'phone'                    => $phone ?: ($existingProfileCand?->phone ?? null),
                'whatsapp'                 => $phone ?: ($existingProfileCand?->whatsapp ?? null),
                'email'                    => $foundApplicant['email_from'] ?? ($existingProfileCand?->email ?? null),
                'area'                     => $area,
                'penempatan'               => $area,
                'principle'                => $prinName,
                'principle_id'             => $principleId,
                'applied_job'              => $job,
                'status'                   => 'Active',
                'status_kandidat'          => $statusKandidat,
                'jenis'                    => '', // Walkin / Inhouse Interview list
                'source_type'              => 'odoo_sync',
                'useras'                   => $userEmail,
                'recruiter_id'             => $userId,
                'password'                 => $passwordHashed,
                'odoo_applicant_id'        => $foundApplicant['id'],
                'odoo_stage_name'          => $stage,
                'odoo_entity'              => $foundEntity,
                'odoo_synced_at'           => now(),
                'odoo_applicant_data'      => $foundApplicant,
                'mother_name'              => $existingProfileCand?->mother_name,
                'emergency_contact_name'   => $existingProfileCand?->emergency_contact_name,
                'emergency_contact_phone'  => $existingProfileCand?->emergency_contact_phone,
                'emergency_contact_relation'=> $existingProfileCand?->emergency_contact_relation,
                'bank_name'                => $existingProfileCand?->bank_name,
                'bank_account_number'      => $existingProfileCand?->bank_account_number,
                'bank_account_holder'      => $existingProfileCand?->bank_account_holder,
                'npwp'                     => $existingProfileCand?->npwp,
                'work_motivation'          => $existingProfileCand?->work_motivation,
                'strengths'                => $existingProfileCand?->strengths,
                'weaknesses'               => $existingProfileCand?->weaknesses,
                'current_activity'         => $existingProfileCand?->current_activity,
                'vehicle'                  => $existingProfileCand?->vehicle,
                'driving_license'          => $existingProfileCand?->driving_license,
                'computer_skill'           => $existingProfileCand?->computer_skill,
                'english_skill'            => $existingProfileCand?->english_skill,
                'other_skills'             => $existingProfileCand?->other_skills,
                'photo_path'               => $existingProfileCand?->photo_path,
                'cv_path'                  => $existingProfileCand?->cv_path,
                'signature_path'           => $existingProfileCand?->signature_path,
                'statement_agreed'         => $existingProfileCand ? ($existingProfileCand->statement_agreed ? 1 : 0) : 0,
                'experience_summary'       => $existingProfileCand?->experience_summary,
                'is_profile_complete'      => $existingProfileCand ? ($existingProfileCand->is_profile_complete ? 1 : 0) : 0,

                // RESET DATA TES ONLINE & EVALUASI KE AWAL
                'tes_kepribadian'          => null,
                'tes_matematika'           => null,
                'tes_komputer'             => null,
                'tes_ke'                   => 1,
                'buktikomputer'            => null,
                'idprinsiple'              => null,
                'ttd_prinsiple'            => null,
                'time_prinsiple'           => null,
                'note_principle'           => null,
                'status_approval'          => null,
                'ai_score'                 => null,
                'ai_cv_analysis'           => null,
                'jadwal_interview'         => null,
                'status_interview'         => null,
                'catatan_interview'        => null,
                'hasil_interview'          => null,
                'interviewer'              => null,
                'created_at'               => now(),
                'updated_at'               => now(),
            ];

            $candidate = Candidate::create($candidatePayload);

            // Warisi riwayat kerja jika belum ada pada kandidat baru tapi ada di record lama
            if ($existingProfileCand && $existingProfileCand->workExperiences()->exists()) {
                foreach ($existingProfileCand->workExperiences as $exp) {
                    $candidate->workExperiences()->create([
                        'company_name'  => $exp->company_name,
                        'position'      => $exp->position,
                        'start_date'    => $exp->start_date,
                        'end_date'      => $exp->end_date,
                        'salary'        => $exp->salary,
                        'job_desc'      => $exp->job_desc,
                        'leave_reason'  => $exp->leave_reason,
                        'order'         => $exp->order,
                    ]);
                }
            }

            // 3. Simpan / Replace ke tb_kandidat jika tabel legacy tersedia
            if ($hasTbKandidat) {
                $tbKandidatData = [
                    'tanggal'             => date('Y-m-d'),
                    'no_ktp'              => $targetNik,
                    'applicants_name'     => $name,
                    'alamat_ktp'          => $foundApplicant['ktp_address'] ?? null,
                    'alamat_domisili'     => $foundApplicant['ktp_address'] ?? null,
                    'kota_lahir'          => $foundApplicant['place_of_birth'] ?? null,
                    'tanggal_lahir'       => $birthDate ?: '1970-01-01',
                    'height'              => (string)($foundApplicant['height'] ?? ''),
                    'weight'              => (string)($foundApplicant['weight'] ?? ''),
                    'religion'            => (string)($foundApplicant['religion'] ?? ''),
                    'pendidikan_terakhir' => $rawEdu ?: '',
                    'phone'               => $phone,
                    'mobile'              => $phone,
                    'area'                => $area,
                    'principle'           => $prinName,
                    'applied_job'         => $job,
                    'status_kawin'        => (string)($foundApplicant['marital_status'] ?? ''),
                    'password'            => $passwordHashed,
                    'useras'              => $userEmail,
                    'status'              => 'Active',
                    'status_kandidat'     => $statusKandidat,
                    'jenis'               => '',
                    'info'                => 'WhatsApp',
                    'undangan'            => 'WhatsApp',
                    'waktukirim'          => now(),
                    'tes_kepribadian'     => null,
                    'tes_matematika'      => null,
                    'tes_komputer'        => null,
                    'tes_ke'              => 1,
                    'idprinsiple'         => null,
                    'ttd_prinsiple'       => null,
                    'gender'              => $gender,
                ];

                $existingTb = DB::table('tb_kandidat')->whereIn('no_ktp', $allPossibleNiks)->first();
                if ($existingTb) {
                    DB::table('tb_kandidat')->where('id', $existingTb->id)->update($tbKandidatData);
                } else {
                    $tbKandidatData['id'] = $candidate->id;
                    try {
                        DB::table('tb_kandidat')->insert($tbKandidatData);
                    } catch (\Throwable $eTb) {
                        DB::table('tb_kandidat')->where('no_ktp', $targetNik)->update($tbKandidatData);
                    }
                }
            }

            DB::commit();

            // Link CBT Login & Undangan WhatsApp
            $cbtLoginUrl = route('cbt.login');
            $waPhone = $phone;
            if (str_starts_with($waPhone, '0')) {
                $waPhone = '62' . substr($waPhone, 1);
            }

            $waText = "Halo {$name},\n\nAnda telah terdaftar untuk mengikuti tahapan seleksi tes online di ASystem ESA Groups ({$prinName} - {$job}).\n\nSilakan login untuk mengerjakan tes online (Psikotes DISC, Matematika, dan Profil):\n🔗 *Link Tes Online*: {$cbtLoginUrl}\n🆔 *Username (NIK)*: {$targetNik}\n🔑 *Password*: {$passwordPlain}\n\nMohon segera menyelesaikan tes tersebut. Terima kasih.\n*Tim Rekrutmen ESA Groups*";
            $waLink = !empty($waPhone) ? "https://api.whatsapp.com/send?phone={$waPhone}&text=" . rawurlencode($waText) : null;

            $resignNote = $isResigned ? " (Perhatian: Kandidat terdata mantan karyawan RESIGN di Odoo [{$empStatus['resigned_entity']}])" : "";
            $successMsg = $wasActiveArchived
                ? "Kandidat {$name} berhasil ditarik ulang dari Odoo [{$foundEntity}]. Data aktif sebelumnya telah otomatis diarsipkan dan proses baru siap dimulai!{$resignNote}"
                : ($isReplaced
                    ? "Kandidat {$name} berhasil ditarik kembali dari Odoo [{$foundEntity}] dan siap diproses!{$resignNote}"
                    : "Kandidat {$name} berhasil ditarik dari Odoo [{$foundEntity}] dan siap diproses!{$resignNote}");

            return response()->json([
                'success'          => true,
                'message'          => $successMsg,
                'candidate_id'     => $candidate->id,
                'detail_url'       => route('interview.show', $candidate->id),
                'cbt_login_url'    => $cbtLoginUrl,
                'full_name'        => $name,
                'nik'              => $targetNik,
                'no_ktp'           => $targetNik,
                'no_kk'            => $targetKk,
                'phone'            => $phone,
                'job'              => $job,
                'principle'        => $prinName,
                'area'             => $area,
                'stage'            => $stage,
                'entity'           => $foundEntity,
                'default_password' => $passwordPlain,
                'wa_link'          => $waLink,
                'wa_phone'         => $waPhone,
                'wa_text'          => $waText,
                'is_resigned'      => $isResigned,
                'resigned_entity'  => $empStatus['resigned_entity'],
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan kandidat ke database: ' . $e->getMessage(),
            ], 200);
        }
    }
}
