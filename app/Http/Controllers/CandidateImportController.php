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
use App\Services\CandidateImportService;
use App\Services\OdooSyncService;
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

    private function getCurrentUser()
    {
        if (auth()->check()) {
            return auth()->user();
        }

        return User::where('role', 'admin')->first()
            ?? User::first();
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

        $foundApplicant = null;
        $foundEntity = null;

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

                // 1. Cari berdasarkan No. KTP
                $applicants = $service->xmlRpcCall('/xmlrpc/2/object', 'execute_kw', [
                    $entity->odoo_db, $uid, $entity->odoo_api_key,
                    'hr.applicant', 'search_read',
                    [[['no_ktp', '=', $cleanNik]]],
                    [
                        'fields' => $applicantFields,
                        'context' => ['active_test' => false],
                        'order' => 'write_date desc, id desc',
                        'limit' => 1,
                    ]
                ]);

                // 2. Jika tidak ditemukan, cari berdasarkan No. KK
                if (!is_array($applicants) || empty($applicants)) {
                    $applicants = $service->xmlRpcCall('/xmlrpc/2/object', 'execute_kw', [
                        $entity->odoo_db, $uid, $entity->odoo_api_key,
                        'hr.applicant', 'search_read',
                        [[['no_kk', '=', $cleanNik]]],
                        [
                            'fields' => $applicantFields,
                            'context' => ['active_test' => false],
                            'order' => 'write_date desc, id desc',
                            'limit' => 1,
                        ]
                    ]);
                }

                if (is_array($applicants) && !empty($applicants)) {
                    $foundApplicant = $applicants[0];
                    $foundEntity = $entity->code;
                    break;
                }
            } catch (\Throwable $e) {
                continue;
            }
        }

        if (!$foundApplicant) {
            return response()->json([
                'success' => false,
                'message' => "NIK / No. KK {$cleanNik} tidak ditemukan di modul Rekrutmen Odoo ERP (AMK, AKP, ATK, ABO, ATB). Pastikan pelamar sudah diinput di Odoo atau periksa kembali nomor NIK/KK.",
            ], 200);
        }

        // Format data yang ditemukan
        $rawFoundKtp = preg_replace('/\D/', '', (string)($foundApplicant['no_ktp'] ?? ''));
        $rawFoundKk = preg_replace('/\D/', '', (string)($foundApplicant['no_kk'] ?? ''));
        $targetNik = (strlen($rawFoundKtp) === 16) ? $rawFoundKtp : $cleanNik;
        $targetKk = (strlen($rawFoundKk) === 16) ? $rawFoundKk : null;
        $foundVia = ($cleanNik === $rawFoundKk && $cleanNik !== $rawFoundKtp) ? 'no_kk' : 'no_ktp';

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

        $allMatchNiks = array_values(array_unique(array_filter([$cleanNik, $targetNik, $targetKk])));
        $existingCandidates = Candidate::whereIn('nik', $allMatchNiks)->orderBy('id')->get();

        // Cek apakah ada kandidat dengan NIK ini yang saat ini sedang AKTIF (bukan status Arsip)
        $activeCandidate = $existingCandidates->first(function($c) {
            return $c->status !== 'Arsip' && $c->status_kandidat !== 'Arsip';
        });

        $isBlocked = false;
        $blockedData = null;

        if ($activeCandidate) {
            $isBlocked = true;
            $asName = $activeCandidate->user_display_name ?: $activeCandidate->useras ?: 'Rekruter Terkait';
            $asEmail = $activeCandidate->useras ?: ($activeCandidate->recruiter->email ?? '-');

            $testsStatus = [];
            if ($activeCandidate->is_psikotes_done || !empty($activeCandidate->tes_kepribadian)) {
                $testsStatus[] = 'Tes Kepribadian (' . ($activeCandidate->tes_kepribadian ?: 'Selesai') . ')';
            }
            if ($activeCandidate->is_math_done || !empty($activeCandidate->tes_matematika)) {
                $mRes = $activeCandidate->testResults()->where('test_type', 'math')->first();
                $mScore = $mRes ? $mRes->score : null;
                $testsStatus[] = 'Tes Matematika (' . ($mScore !== null ? 'Nilai: ' . $mScore : 'Selesai') . ')';
            }
            if ($activeCandidate->is_computer_done || !empty($activeCandidate->tes_komputer)) {
                $testsStatus[] = 'Tes Komputer (Selesai)';
            }
            $testsText = !empty($testsStatus) ? implode(', ', $testsStatus) : 'Proses seleksi sedang berjalan';
            $profComplete = ($activeCandidate->is_profile_complete || $activeCandidate->checkProfileCompleteness());

            $activePrinName = is_object($activeCandidate->principle) ? ($activeCandidate->principle->name ?? '-') : ($activeCandidate->principle ?? '-');

            $blockedData = [
                'id' => $activeCandidate->id,
                'name' => $activeCandidate->full_name,
                'nik' => $activeCandidate->nik,
                'status' => $activeCandidate->status,
                'as_name' => $asName,
                'as_email' => $asEmail,
                'area' => $activeCandidate->area ?? '-',
                'principle' => $activePrinName,
                'job' => $activeCandidate->applied_job ?? '-',
                'tests_text' => $testsText,
                'is_profile_complete' => $profComplete,
                'profile_status_text' => $profComplete ? 'Lengkap' : 'Belum Lengkap',
                'created_at' => $activeCandidate->created_at ? $activeCandidate->created_at->format('d/m/Y H:i') : null,
            ];
        }

        $lastExisting = $existingCandidates->last();

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

        return response()->json([
            'success' => true,
            'message' => "Data pelamar ditemukan di Odoo [{$foundEntity}]!" . ($foundVia === 'no_kk' ? " (via No. KK)" : ""),
            'applicant' => [
                'odoo_id'      => $foundApplicant['id'],
                'entity'       => $foundEntity,
                'name'         => $name,
                'nik'          => $targetNik,
                'no_ktp'       => $targetNik,
                'no_kk'        => $targetKk,
                'searched_nik' => $cleanNik,
                'found_via'    => $foundVia,
                'job'          => $job,
                'principle'    => $principle,
                'area'         => $area,
                'stage'        => $stage,
                'phone'        => $phone,
                'birth'        => $birth,
                'age'          => $age,
                'birth_place'  => $foundApplicant['place_of_birth'] ?? null,
                'address'      => $foundApplicant['ktp_address'] ?? null,
                'email'        => $foundApplicant['email_from'] ?? null,
                'gender'       => $gender,
            ],
            'is_blocked' => $isBlocked,
            'blocked_data' => $blockedData,
            'existing_candidate' => $lastExisting ? [
                'id'         => $lastExisting->id,
                'name'       => $lastExisting->full_name,
                'status'     => $lastExisting->status,
                'is_active'  => ($lastExisting->status !== 'Arsip' && $lastExisting->status_kandidat !== 'Arsip'),
                'created_at' => $lastExisting->created_at ? $lastExisting->created_at->format('d/m/Y H:i') : null,
            ] : null,
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

        $foundApplicant = null;
        $foundEntity = null;

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
                            'order' => 'write_date desc, id desc',
                            'limit' => 1,
                        ]
                    ]);
                    if (is_array($applicants) && !empty($applicants)) {
                        $foundApplicant = $applicants[0];
                        $foundEntity = $entity->code;
                        break 2;
                    }
                }

                // 2. Cari berdasarkan no_kk
                foreach ($searchNiks as $sNik) {
                    $applicants = $service->xmlRpcCall('/xmlrpc/2/object', 'execute_kw', [
                        $entity->odoo_db, $uid, $entity->odoo_api_key,
                        'hr.applicant', 'search_read',
                        [[['no_kk', '=', $sNik]]],
                        [
                            'fields' => $applicantFields,
                            'context' => ['active_test' => false],
                            'order' => 'write_date desc, id desc',
                            'limit' => 1,
                        ]
                    ]);
                    if (is_array($applicants) && !empty($applicants)) {
                        $foundApplicant = $applicants[0];
                        $foundEntity = $entity->code;
                        break 2;
                    }
                }
            } catch (\Throwable $e) {
                continue;
            }
        }

        if (!$foundApplicant) {
            return response()->json([
                'success' => false,
                'message' => "NIK {$cleanNik} tidak ditemukan di modul Rekrutmen Odoo ERP.",
            ], 200);
        }

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

            // VALIDASI PROTEKSI KANDIDAT AKTIF:
            // Sesuai aturan sistem, jika kandidat berstatus AKTIF (bukan Arsip):
            // Data TIDAK BISA di-replace atau diimpor ulang sampai data yang aktif diarsipkan.
            // Data baru hanya bisa me-replace data yang berstatus Arsip.
            $activeCandidate = $existingCandidates->first(function($c) {
                return $c->status !== 'Arsip' && $c->status_kandidat !== 'Arsip';
            });

            if ($activeCandidate) {
                DB::rollBack();
                $asName = $activeCandidate->user_display_name ?: $activeCandidate->useras ?: 'Rekruter Terkait';
                $asEmail = $activeCandidate->useras ?: ($activeCandidate->recruiter->email ?? '-');

                $testsStatus = [];
                if ($activeCandidate->is_psikotes_done || !empty($activeCandidate->tes_kepribadian)) {
                    $testsStatus[] = 'Tes Kepribadian (' . ($activeCandidate->tes_kepribadian ?: 'Selesai') . ')';
                }
                if ($activeCandidate->is_math_done || !empty($activeCandidate->tes_matematika)) {
                    $mRes = $activeCandidate->testResults()->where('test_type', 'math')->first();
                    $mScore = $mRes ? $mRes->score : null;
                    $testsStatus[] = 'Tes Matematika (' . ($mScore !== null ? 'Nilai: ' . $mScore : 'Selesai') . ')';
                }
                if ($activeCandidate->is_computer_done || !empty($activeCandidate->tes_komputer)) {
                    $testsStatus[] = 'Tes Komputer (Selesai)';
                }
                $testsText = !empty($testsStatus) ? implode(', ', $testsStatus) : 'Proses seleksi sedang berjalan';
                $profText = ($activeCandidate->is_profile_complete || $activeCandidate->checkProfileCompleteness()) ? 'Lengkap' : 'Belum Lengkap';
                $activePrinName = is_object($activeCandidate->principle) ? ($activeCandidate->principle->name ?? '-') : ($activeCandidate->principle ?? '-');

                return response()->json([
                    'success' => false,
                    'is_blocked' => true,
                    'title' => 'Kandidat Aktif Tidak Dapat Di-replace!',
                    'message' => "Kandidat dengan NIK {$targetNik} ({$activeCandidate->full_name}) sudah terdaftar di ASystem dan saat ini berstatus AKTIF.\n\n"
                               . "• Status Profil: {$profText}\n"
                               . "• Progres Tes Online: {$testsText}\n"
                               . "• Terdaftar under AS: {$asName} ({$asEmail})\n"
                               . "• Area / Posisi: {$activeCandidate->applied_job} • Area {$activeCandidate->area} ({$activePrinName})\n\n"
                               . "Sesuai SOP, data kandidat aktif tidak dapat di-replace. Data baru hanya bisa masuk/me-replace jika data aktif diarsipkan terlebih dahulu. Harap berkoordinasi dengan AS terkait ({$asName} - {$asEmail}).",
                    'candidate' => [
                        'id' => $activeCandidate->id,
                        'name' => $activeCandidate->full_name,
                        'status' => $activeCandidate->status,
                        'as_name' => $asName,
                        'as_email' => $asEmail,
                        'tests_status' => $testsText,
                        'profile_status' => $profText,
                    ]
                ], 200);
            }

            $candidatePayload = [
                'nik'                      => $targetNik,
                'full_name'                => $name,
                'birth_place'              => $foundApplicant['place_of_birth'] ?? null,
                'birth_date'               => $birthDate,
                'address_ktp'              => $foundApplicant['ktp_address'] ?? null,
                'address_domicile'         => $foundApplicant['ktp_address'] ?? null,
                'gender'                   => $gender,
                'height'                   => (int)($foundApplicant['height'] ?? null) ?: null,
                'weight'                   => (int)($foundApplicant['weight'] ?? null) ?: null,
                'religion'                 => $foundApplicant['religion'] ?? null,
                'marital_status'           => $foundApplicant['marital_status'] ?? null,
                'education'                => is_array($foundApplicant['type_id']) ? $foundApplicant['type_id'][1] : ($foundApplicant['type_id'] ?? null),
                'phone'                    => $phone,
                'whatsapp'                 => $phone,
                'email'                    => $foundApplicant['email_from'] ?? null,
                'area'                     => $area,
                'penempatan'               => $area,
                'principle'                => $prinName,
                'principle_id'             => $principleId,
                'applied_job'              => $job,
                'status'                   => 'Active',
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
                'is_profile_complete'      => false,

                // RESET DATA TES ONLINE & EVALUASI KE AWAL
                'tes_kepribadian'          => null,
                'tes_matematika'           => null,
                'tes_komputer'             => null,
                'tes_ke'                   => 1,
                'buktikomputer'            => null,
                'signature_path'           => null,
                'statement_agreed'         => false,
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
                'updated_at'               => now(),
            ];

            if ($isReplaced) {
                $candidate = $existingCandidates->last();
                $allCandIds = $existingCandidates->pluck('id')->all();

                // Bersihkan duplikat record jika ada dari import terdahulu, migrasikan child records ke kandidat utama
                $duplicateIds = array_diff($allCandIds, [$candidate->id]);
                if (!empty($duplicateIds)) {
                    TestResult::whereIn('candidate_id', $duplicateIds)->update(['candidate_id' => $candidate->id]);
                    WorkExperience::whereIn('candidate_id', $duplicateIds)->update(['candidate_id' => $candidate->id]);
                    Candidate::whereIn('id', $duplicateIds)->delete();
                }

                // JANGAN PERNAH menghapus TestResult, tb_hasilpsikotes, tb_hasilmath, hasil_kompt, WorkExperience,
                // ataupun mereset hasil tes online dan tanda tangan digital yang sudah dikerjakan kandidat!

                // Hanya perbarui data dari Odoo tanpa merusak hasil tes dan data profil portal yang sudah diisi
                $updateData = [
                    'odoo_applicant_id'   => $foundApplicant['id'],
                    'odoo_stage_name'     => $stage,
                    'odoo_entity'         => $foundEntity,
                    'odoo_synced_at'      => now(),
                    'odoo_applicant_data' => $foundApplicant,
                    'updated_at'          => now(),
                ];

                if (!empty($job)) $updateData['applied_job'] = $job;
                if (!empty($prinName)) {
                    $updateData['principle'] = $prinName;
                    if ($principleId) $updateData['principle_id'] = $principleId;
                }
                if (!empty($area)) {
                    $updateData['area'] = $area;
                    $updateData['penempatan'] = $area;
                }
                if ($userEmail && empty($candidate->useras)) $updateData['useras'] = $userEmail;
                if ($userId && empty($candidate->recruiter_id)) $updateData['recruiter_id'] = $userId;

                // Update data demografi dasar dari Odoo hanya jika data lokal masih kosong
                if (!empty($name) && empty($candidate->full_name)) $updateData['full_name'] = $name;
                if (!empty($phone) && empty($candidate->phone)) {
                    $updateData['phone'] = $phone;
                    $updateData['whatsapp'] = $phone;
                }
                if (!empty($birthDate) && empty($candidate->birth_date)) $updateData['birth_date'] = $birthDate;
                if (!empty($foundApplicant['place_of_birth']) && empty($candidate->birth_place)) {
                    $updateData['birth_place'] = $foundApplicant['place_of_birth'];
                }
                if (!empty($foundApplicant['ktp_address']) && empty($candidate->address_ktp)) {
                    $updateData['address_ktp'] = $foundApplicant['ktp_address'];
                    $updateData['address_domicile'] = $foundApplicant['ktp_address'];
                }
                $odooEducation = is_array($foundApplicant['type_id']) ? $foundApplicant['type_id'][1] : ($foundApplicant['type_id'] ?? null);
                if (!empty($odooEducation) && empty($candidate->education)) {
                    $updateData['education'] = $odooEducation;
                }

                $candidate->update($updateData);
                $candidate->checkProfileCompleteness();
            } else {
                $candidatePayload['created_at'] = now();
                $candidate = Candidate::create($candidatePayload);
            }

            // 3. Simpan / Replace ke tb_kandidat jika tabel legacy tersedia
            if (Schema::hasTable('tb_kandidat')) {
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
                    'pendidikan_terakhir' => is_array($foundApplicant['type_id']) ? $foundApplicant['type_id'][1] : (string)($foundApplicant['type_id'] ?? ''),
                    'phone'               => $phone,
                    'mobile'              => $phone,
                    'area'                => $area,
                    'principle'           => $prinName,
                    'applied_job'         => $job,
                    'status_kawin'        => (string)($foundApplicant['marital_status'] ?? ''),
                    'password'            => $passwordHashed,
                    'useras'              => $userEmail,
                    'status'              => 'Active',
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
                    // Pertahankan progres tes jika sudah ada di tb_kandidat atau candidate
                    if (!empty($existingTb->tes_kepribadian) || !empty($candidate->tes_kepribadian)) {
                        unset($tbKandidatData['tes_kepribadian']);
                    }
                    if (!empty($existingTb->tes_matematika) || !empty($candidate->tes_matematika)) {
                        unset($tbKandidatData['tes_matematika']);
                    }
                    if (!empty($existingTb->tes_komputer) || !empty($candidate->tes_komputer)) {
                        unset($tbKandidatData['tes_komputer']);
                    }
                    if (!empty($existingTb->tes_ke) || !empty($candidate->tes_ke)) {
                        unset($tbKandidatData['tes_ke']);
                    }
                    if (!empty($existingTb->password)) {
                        unset($tbKandidatData['password']);
                    }
                    if (empty($tbKandidatData['pendidikan_terakhir']) && !empty($existingTb->pendidikan_terakhir)) {
                        unset($tbKandidatData['pendidikan_terakhir']);
                    }
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

            $successMsg = $isReplaced
                ? "Data kandidat {$name} berhasil di-REPLACE dan tes online di-RESET ke awal!"
                : "Kandidat {$name} berhasil ditarik dari Odoo [{$foundEntity}] dan siap diproses!";

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
