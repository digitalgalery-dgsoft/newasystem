<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Principle;
use App\Models\User;
use App\Models\InterviewAssessment;
use App\Models\WorkExperience;
use App\Models\TestResult;
use App\Models\InhouseApproval;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class InterviewInhouseController extends Controller
{
    private function getCurrentUser()
    {
        return auth()->user() ?? User::where('role', 'admin')->first() ?? User::first();
    }

    private function getSalam(): string
    {
        $jam = Carbon::now('Asia/Jakarta')->format('H:i');
        if ($jam > '04:00' && $jam < '11:00') {
            return 'Selamat Pagi';
        } elseif ($jam >= '11:00' && $jam < '15:00') {
            return 'Selamat Siang';
        } elseif ($jam > '15:00' && $jam < '18:00') {
            return 'Selamat Sore';
        } else {
            return 'Selamat Malam';
        }
    }

    private function buildWaUrl($candidate, $user, $salam): string
    {
        $induk = $candidate->principle?->parent_company ?? $candidate->principle?->name ?? 'Inhouse ESA Groups';
        $asName = InterviewController::resolveCandidateAsName($candidate, $user);
        $pesan = "{$salam} sdr/sdr(i) {$candidate->full_name}\n\n*Tes Online Inhouse {$induk}*\n\nBerikut Kode Akses Tes Online Kamu\n\nUsername : {$candidate->nik}\nPassword : _Gunakan Tanggal lahir dengan Format ddmmyyyy_\n\nAkses Melalui Link Berikut https://new.asystem.co.id/cbt/login\n\nTutorial Cara Login & Isi Data Profile : https://youtu.be/l3KW9-13z7c\n\n_Terima Kasih_\n\n_Regards_\n{$asName}";

        $phone = $candidate->clean_whatsapp;
        return "https://web.whatsapp.com/send?phone={$phone}&text=" . urlencode($pesan);
    }

    /**
     * Halaman Utama Kandidat Inhouse: Replikasi interviewinhouse.php
     */
    /**
     * Ambil daftar ID prinsiple resmi 5 entitas inhouse
     */
    public static function getInhousePrincipleIds(): array
    {
        return Principle::all()->filter(function ($p) {
            return \App\Models\Employee::isInhousePrinciple($p->name);
        })->pluck('id')->toArray();
    }

    /**
     * Cek apakah kandidat berstatus inhouse 5 entitas resmi:
     * - PT ARINA MULTI KARYA
     * - PT ALVA KARYA PERKASA
     * - PT ANUGRAH TERPERCAYA KERJA
     * - PT ABADI BERKAT ODELIA
     * - PT ANUGRAH TALENTA BERKARYA
     */
    public static function isCandidateInhouse($candidate): bool
    {
        if (!$candidate) {
            return false;
        }

        // 1. Cek dari ID Prinsiple resmi (hanya ID dari 5 entitas inhouse)
        if (!empty($candidate->principle_id)) {
            $inhouseIds = self::getInhousePrincipleIds();
            if (in_array((int)$candidate->principle_id, $inhouseIds, true)) {
                return true;
            }
        }

        // 2. Cek dari nama prinsiple (baik dari model relation ataupun kolom string)
        $prinName = '';
        if (is_object($candidate->principle) && !empty($candidate->principle->name)) {
            $prinName = $candidate->principle->name;
        } elseif (is_string($candidate->principle)) {
            $prinName = $candidate->principle;
        }

        if (!empty($prinName) && \App\Models\Employee::isInhousePrinciple($prinName)) {
            return true;
        }

        return false;
    }

    /**
     * Halaman Utama Kandidat Inhouse: Replikasi interviewinhouse.php
     * Hanya tampil untuk HRD dan Head dari user (Rekrutor / AS) yang handle kandidat
     */
    public function index(Request $request)
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return redirect()->route('login');
        }

        // Halaman kandidat inhouse untuk HRD, Head, dan Approver yang ditugaskan pada workflow
        $isStepUser = \App\Models\ApprovalWorkflowStepUser::where('user_id', $user->id)
            ->orWhere('user_email', $user->email)
            ->exists();

        if (!$user->isHrdOrHead() && !$isStepUser && !$user->isAdmin()) {
            return redirect()->route('interview.index')->with('error', 'Akses Ditolak! Halaman Kandidat Inhouse hanya dapat diakses oleh HRD, Head, dan Approver yang ditugaskan.');
        }

        $salam = $this->getSalam();
        $search = $request->query('search') ?? $request->query('q');
        $statusReplace = $request->query('status_replace');
        $statusApproval = $request->query('status_approval');

        // 1. Filter: HANYA tampil kandidat dengan prinsiple 5 entitas inhouse resmi
        $inhouseIds = self::getInhousePrincipleIds();
        $inhouseNames = [
            'ARINA MULTI KARYA', 'ALVA KARYA PERKASA', 'ANUGRAH TERPERCAYA KERJA',
            'ABADI BERKAT ODELIA', 'ARINA BINTANG OETAMA', 'ANUGRAH TALENTA BERKARYA', 'ANUGRAH TRI BERKAH'
        ];

        $baseQuery = Candidate::with(['principle', 'recruiter', 'testResults', 'inhouseApprovals', 'currentApprovalStep'])
            ->whereNotIn('status', ['Arsip', 'archived'])
            ->where(function ($q) use ($inhouseIds, $inhouseNames) {
                if (!empty($inhouseIds)) {
                    $q->whereIn('principle_id', $inhouseIds);
                }
                $q->orWhere(function ($eq) use ($inhouseNames) {
                    foreach ($inhouseNames as $n) {
                        $eq->orWhere('principle', 'like', "%{$n}%");
                    }
                });
            });

        // 2. Hak Akses: HRD / Admin melihat semua inhouse; Head & Step Approver melihat yang ditugaskan kepada mereka
        if ($user->isHrd() || $user->isAdmin()) {
            $user->applyRoleScopeToCandidates($baseQuery);
        } else {
            $userName = trim($user->name);
            $userEmail = trim($user->email);
            $subIdentifiers = $user->getSubordinateRecruiterIdentifiers();

            // Ambil ID step approval yang menugaskan user ini
            $assignedStepIds = \App\Models\ApprovalWorkflowStepUser::where('user_id', $user->id)
                ->orWhere('user_email', $userEmail)
                ->pluck('step_id')
                ->toArray();

            $baseQuery->where(function ($q) use ($userName, $subIdentifiers, $user, $assignedStepIds) {
                $hasCondition = false;
                if (!empty($userName)) {
                    $q->where('nama_approver', 'like', "%{$userName}%");
                    $hasCondition = true;
                }
                if (!empty($subIdentifiers)) {
                    if ($hasCondition) {
                        $q->orWhereIn(\Illuminate\Support\Facades\DB::raw('LOWER(TRIM(useras))'), $subIdentifiers);
                    } else {
                        $q->whereIn(\Illuminate\Support\Facades\DB::raw('LOWER(TRIM(useras))'), $subIdentifiers);
                        $hasCondition = true;
                    }
                }
                if (!empty($assignedStepIds)) {
                    $q->orWhereIn('current_approval_step_id', $assignedStepIds);
                }
                $q->orWhere('recruiter_id', $user->id);
            });

            // Di dashboard non-HRD: kandidat yang masuk proses approval
            $baseQuery->where(function ($q) {
                $q->where('status_approval', '!=', 'Arsip')
                  ->whereNotNull('status_approval');
            });
        }

        // Hitung Statistik
        if ($user->isHrd()) {
            $totalInhouse = (clone $baseQuery)->count();
            $countBaru = (clone $baseQuery)->where(function ($q) {
                $q->where('status_replace', 'Baru')
                  ->orWhere('status_replace', 'New')
                  ->orWhereNull('status_replace')
                  ->orWhere('status_replace', '');
            })->count();
            $countReplace = (clone $baseQuery)->where('status_replace', 'Replace')->count();
            $countPending = (clone $baseQuery)->where(function ($q) {
                $q->whereNull('status_approval')
                  ->orWhere('status_approval', '')
                  ->orWhereIn('status_approval', ['Review HRD', 'Review Head', 'Proses']);
            })->count();
            $countApproved = (clone $baseQuery)->where('status_approval', 'Approve')->count();
            $countProcess = $totalInhouse - $countApproved;
        } else {
            // Khusus Head: statistik disesuaikan dengan alur approval Head
            $totalInhouse = (clone $baseQuery)->count();
            $countBaru = (clone $baseQuery)->where(function ($q) {
                $q->where('status_replace', 'Baru')
                  ->orWhere('status_replace', 'New')
                  ->orWhereNull('status_replace')
                  ->orWhere('status_replace', '');
            })->count();
            $countReplace = (clone $baseQuery)->where('status_replace', 'Replace')->count();
            // Menunggu Approval Head: yang saat ini sedang di step 'Review Head'
            $countPending = (clone $baseQuery)->where('status_approval', 'Review Head')->count();
            // Selesai oleh Head: yang sudah diapprove oleh Head (Review HRD atau Approve)
            $countApproved = (clone $baseQuery)->whereIn('status_approval', ['Review HRD', 'Approve'])->count();
            $countProcess = $countPending;
        }

        // Navigasi Tabs: 'process' (default) vs 'done'
        $tab = $request->query('tab', 'process');
        if (!in_array($tab, ['process', 'done'])) {
            $tab = 'process';
        }

        // Filter tabel
        $query = clone $baseQuery;

        if ($tab === 'done') {
            if ($user->isHrd()) {
                $query->where('status_approval', 'Approve');
            } else {
                // Untuk Head: tab done adalah kandidat yang sudah disetujui Head (diteruskan ke HRD atau sudah Approve)
                $query->whereIn('status_approval', ['Review HRD', 'Approve']);
            }
        } else {
            if ($user->isHrd()) {
                $query->where(function ($q) {
                    $q->whereNull('status_approval')
                      ->orWhere('status_approval', '!=', 'Approve');
                });
            } else {
                // Untuk Head: hanya tampil kandidat yang sedang di step approval head
                $query->where('status_approval', 'Review Head');
            }
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('applied_job', 'like', "%{$search}%")
                  ->orWhere('user_request', 'like', "%{$search}%");
            });
        }

        if ($statusReplace) {
            if ($statusReplace === 'Baru') {
                $query->where(function ($q) {
                    $q->where('status_replace', 'Baru')
                      ->orWhere('status_replace', 'New')
                      ->orWhereNull('status_replace')
                      ->orWhere('status_replace', '');
                });
            } else {
                $query->where('status_replace', $statusReplace);
            }
        }

        if ($statusApproval) {
            if ($statusApproval === 'Pending') {
                if ($user->isHrd()) {
                    $query->where(function ($q) {
                        $q->whereNull('status_approval')
                          ->orWhere('status_approval', '')
                          ->orWhereIn('status_approval', ['Review HRD', 'Review Head', 'Proses']);
                    });
                } else {
                    $query->where('status_approval', 'Review Head');
                }
            } else {
                $query->where('status_approval', $statusApproval);
            }
        }

        $candidates = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();

        // Tambahkan atribut wa_url untuk setiap kandidat
        $candidates->getCollection()->transform(function ($c) use ($user, $salam) {
            $c->wa_url = $this->buildWaUrl($c, $user, $salam);
            return $c;
        });

        return view('interviewinhouse.index', compact(
            'candidates',
            'user',
            'salam',
            'tab',
            'totalInhouse',
            'countBaru',
            'countReplace',
            'countPending',
            'countApproved',
            'countProcess',
            'search',
            'statusReplace',
            'statusApproval'
        ));
    }

    /**
     * Halaman Detail Kandidat Inhouse: Replikasi hasilinhouse.php dengan Tampilan Modern
     */
    public function show($id)
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return redirect()->route('login');
        }

        $isStepUser = \App\Models\ApprovalWorkflowStepUser::where('user_id', $user->id)
            ->orWhere('user_email', $user->email)
            ->exists();

        if (!$user->isHrdOrHead() && !$isStepUser && !$user->isAdmin()) {
            return redirect()->route('interview.index')->with('error', 'Akses Ditolak! Halaman Detail Inhouse hanya dapat diakses oleh HRD, Head / Pimpinan, atau Approver yang ditugaskan.');
        }

        $candidate = Candidate::with([
            'principle',
            'recruiter',
            'workExperiences',
            'interviewAssessment',
            'testResults',
            'inhouseApprovals',
            'currentApprovalStep'
        ])->findOrFail($id);

        if (!self::isCandidateInhouse($candidate)) {
            return redirect()->route('interview.index')->with('error', 'Akses Ditolak! Kandidat ini bukan kandidat inhouse 5 entitas.');
        }

        $isHrd = $user->isHrd();
        $isHead = $user->isHead();

        // Alur Approval Dinamis
        $applicableSteps = \App\Services\ApprovalWorkflowService::getApplicableStepsForCandidate($candidate);
        $currentStep = \App\Services\ApprovalWorkflowService::getCurrentStep($candidate, $applicableSteps);
        $canApproveCurrentStep = \App\Services\ApprovalWorkflowService::canUserApprove($candidate, $user, $currentStep);
        $isDireksi = \App\Services\ApprovalWorkflowService::isPimpinanDireksi($candidate);

        // Jika user adalah non-HRD & non-Admin, pastikan memiliki keterkaitan (bawahan, ditugaskan ke Head, atau approver aktif)
        if (!$isHrd && !$user->isAdmin()) {
            $userName = trim($user->name);
            $isApproverMatch = !empty($candidate->nama_approver) && !empty($userName) && str_contains(strtolower($candidate->nama_approver), strtolower($userName));
            $subIdentifiers = $user->getSubordinateRecruiterIdentifiers();
            $candUseras = strtolower(trim($candidate->useras ?? ''));
            $isSub = in_array($candUseras, $subIdentifiers) || ($candidate->recruiter_id == $user->id);
            $headArea = !empty($user->area) ? strtoupper(trim($user->area)) : null;
            $candArea = strtoupper(trim($candidate->area ?? ''));
            $allowedAreas = array_map('strtoupper', $user->getEffectiveAreas());
            $isAreaMatch = (!empty($allowedAreas) && in_array($candArea, $allowedAreas)) || (!empty($headArea) && $candArea === $headArea);

            if (!$isApproverMatch && !$isSub && !$isAreaMatch && !$canApproveCurrentStep && !$isStepUser) {
                return redirect()->route('interviewinhouse.index')->with('error', 'Akses Ditolak! Anda bukan Approver yang menangani kandidat inhouse ini.');
            }
        }

        // Sinkronisasi data pengalaman dari tb_pengalaman jika work_experiences masih kosong
        if ($candidate->workExperiences->isEmpty()) {
            $rawExps = DB::table('tb_pengalaman')
                ->where('id_kandidat', $candidate->id)
                ->orWhere(function ($q) use ($candidate) {
                    if (!empty($candidate->nik)) {
                        $q->where('nomor_ktp', $candidate->nik);
                    }
                })
                ->get();
            if ($rawExps->isNotEmpty()) {
                foreach ($rawExps as $r) {
                    WorkExperience::create([
                        'candidate_id' => $candidate->id,
                        'company_name' => $r->nama_perusahaan ?? 'Perusahaan Sebelumnya',
                        'position' => $r->jabatan ?? ($candidate->applied_job ?? 'Karyawan'),
                        'company_phone' => $r->telp_perusahaan ?? '-',
                        'reason_for_leaving' => $r->alasan_keluar ?? '-',
                        'start_date' => $r->tgl_masuk ?? null,
                        'end_date' => $r->tgl_keluar ?? null,
                        'supervisor_name' => $r->spv ?? '-',
                        'performance_notes' => $r->performa ?? 'Baik',
                        'discipline_notes' => $r->disiplin ?? 'Tepat Waktu',
                        'responsibility_notes' => $r->tanggungjawab ?? 'Bertanggung Jawab',
                        'strengths' => $r->streng ?? '',
                        'weaknesses' => $r->week ?? '',
                        'check_date' => $r->tanggal ?? null,
                        'proof_attachment_path' => $r->file_cek ?? null,
                    ]);
                }
                $candidate->unsetRelation('workExperiences');
                $candidate->load('workExperiences');
            }
        }

        $principles = Principle::where('is_active', true)->orderBy('name')->get();
        $areas = [
            'JAKARTA', 'SURABAYA', 'BANDUNG', 'SEMARANG', 'MEDAN', 
            'MAKASSAR', 'DENPASAR', 'PALEMBANG', 'BALIKPAPAN', 'YOGYAKARTA',
            'MALANG', 'BOGOR', 'BEKASI', 'TANGERANG', 'DEPOK'
        ];

        // Status Approval Display
        $statusApproval = $candidate->status_approval ?? 'Proses';
        $statusBadge = match($statusApproval) {
            'Approve' => ['text' => 'Approve', 'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
            'Tolak' => ['text' => 'Tolak', 'class' => 'bg-rose-50 text-rose-700 border-rose-200'],
            'Review HRD' => ['text' => 'Review HRD', 'class' => 'bg-sky-50 text-sky-700 border-sky-200'],
            'Review Head' => ['text' => 'Review Head', 'class' => 'bg-indigo-50 text-indigo-700 border-indigo-200'],
            default => ['text' => $statusApproval, 'class' => 'bg-amber-50 text-amber-700 border-amber-200']
        };

        $evalData = \App\Services\CandidateEvaluationDataService::getEvaluationData($candidate);

        $aiData = $candidate->ai_data;
        $otherCandidates = Candidate::where('applied_job', $candidate->applied_job)
            ->where('id', '!=', $candidate->id)
            ->whereNotNull('ai_score')
            ->where('ai_score', '>', 0)
            ->orderByDesc('ai_score')
            ->limit(5)
            ->get();
        $userPrinsiples = InterviewController::getUserPrinsipleOptions($candidate);

        // Resolve Interviewer / Recruiter details and signature
        $asDetails = InterviewController::resolveCandidateAsDetails($candidate, $user);
        $candidateAsUser = $asDetails['user'] ?? null;
        $candidateAsName = $asDetails['name'] ?? ($candidate->user_display_name ?: 'User AS');
        $candidateAsSigUrl = null;
        if ($candidateAsUser && !empty($candidateAsUser->signature_path)) {
            $candidateAsSigUrl = $candidateAsUser->getSignatureBase64();
        }

        $assess = $candidate->interviewAssessment;
        $assessSigUrl = null;
        $assessSigPath = $assess?->interviewer_signature_path;
        if (!empty($assessSigPath)) {
            if (str_starts_with($assessSigPath, 'data:image')) {
                $assessSigUrl = $assessSigPath;
            } elseif (Storage::disk('public')->exists($assessSigPath)) {
                $assessSigUrl = 'data:image/png;base64,' . base64_encode(Storage::disk('public')->get($assessSigPath));
            } elseif (file_exists(public_path($assessSigPath))) {
                $assessSigUrl = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path($assessSigPath)));
            } elseif (file_exists(storage_path('app/public/' . $assessSigPath))) {
                $assessSigUrl = 'data:image/png;base64,' . base64_encode(file_get_contents(storage_path('app/public/' . $assessSigPath)));
            }
        }

        $initialInterviewerSig = $assessSigUrl ?: $candidateAsSigUrl;
        if (!$initialInterviewerSig && !empty($candidate->signature_path)) {
            $candSig = $candidate->signature_path;
            if (file_exists(public_path('lampiran/' . $candSig))) {
                $initialInterviewerSig = asset('lampiran/' . $candSig);
            } elseif (file_exists(public_path('storage/' . $candSig))) {
                $initialInterviewerSig = asset('storage/' . $candSig);
            }
        }

        return view('interviewinhouse.show', array_merge([
            'candidate' => $candidate,
            'user' => $user,
            'principles' => $principles,
            'userPrinsiples' => $userPrinsiples,
            'areas' => $areas,
            'aiData' => $aiData,
            'otherCandidates' => $otherCandidates,
            'statusBadge' => $statusBadge,
            'isHrd' => $isHrd,
            'isHead' => $isHead,
            'isInhouseCandidate' => true,
            'asDetails' => $asDetails,
            'candidateAsName' => $candidateAsName,
            'initialInterviewerSig' => $initialInterviewerSig,
            'applicableSteps' => $applicableSteps,
            'currentStep' => $currentStep,
            'canApproveCurrentStep' => $canApproveCurrentStep,
            'isDireksi' => $isDireksi,
        ], $evalData));
    }


    /**
     * Simpan Approval & Tanda Tangan Digital Inhouse (Alur Dinamis)
     */
    public function storeApproval(Request $request, $id)
    {
        $candidate = Candidate::findOrFail($id);
        $user = $this->getCurrentUser();
        $isJson = $request->isJson() || $request->wantsJson() || $request->header('Content-Type') === 'application/json';

        if (!$user) {
            return $isJson ? response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401) : redirect()->route('login');
        }

        if (!self::isCandidateInhouse($candidate)) {
            if ($isJson) {
                return response()->json(['status' => 'error', 'message' => 'Kandidat ini bukan kandidat inhouse 5 entitas.'], 422);
            }
            return back()->with('error', 'Kandidat ini bukan kandidat inhouse 5 entitas.');
        }

        $applicableSteps = \App\Services\ApprovalWorkflowService::getApplicableStepsForCandidate($candidate);
        $currentStep = \App\Services\ApprovalWorkflowService::getCurrentStep($candidate, $applicableSteps);

        if (!$currentStep) {
            $msg = 'Kandidat ini sudah tidak memiliki tahapan approval yang aktif (telah selesai atau ditolak).';
            return $isJson ? response()->json(['status' => 'error', 'message' => $msg], 422) : back()->with('error', $msg);
        }

        if (!\App\Services\ApprovalWorkflowService::canUserApprove($candidate, $user, $currentStep)) {
            $msg = "Akses ditolak! Anda tidak memiliki hak akses approver untuk tahap [{$currentStep->step_name}].";
            return $isJson ? response()->json(['status' => 'error', 'message' => $msg], 403) : back()->with('error', $msg);
        }

        $catatan = $request->input('catatan');
        $approval = $request->input('approval') ?? $request->input('status') ?? 'Approve';
        $imageData = $request->input('image') ?? $request->input('signature_data');

        if (empty($catatan) || empty($approval)) {
            $msg = 'Catatan dan hasil keputusan wajib diisi!';
            return $isJson ? response()->json(['status' => 'error', 'message' => $msg], 422) : back()->with('error', $msg);
        }

        // Tanda tangan image processing (Simpan file PNG jika data URL)
        $fileName = null;
        if (!empty($imageData)) {
            if (str_starts_with($imageData, 'data:image')) {
                $dir = public_path('lampiran');
                if (!file_exists($dir)) {
                    mkdir($dir, 0777, true);
                }
                $ttdFilename = 'ttd_' . time() . '_' . $candidate->id . '.png';
                $binary = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imageData));
                file_put_contents($dir . '/' . $ttdFilename, $binary);
                $fileName = 'lampiran/' . $ttdFilename;
            } else {
                $fileName = $imageData;
            }
        }

        $result = \App\Services\ApprovalWorkflowService::processApproval($candidate, $user, [
            'catatan' => $catatan,
            'approval' => $approval,
            'signature_path' => $fileName,
        ]);

        if ($isJson) {
            return response()->json([
                'status' => 'success',
                'message' => $result['message'],
                'redirect' => route('interviewinhouse.show', $candidate->id)
            ]);
        }

        return redirect()->route('interviewinhouse.show', $candidate->id)->with('success', $result['message']);
    }

    /**
     * Download Berkas Lamaran yang diupload Rekrutor / AS
     */
    public function downloadBerkas($id)
    {
        $candidate = Candidate::findOrFail($id);
        if (empty($candidate->berkas_lamaran)) {
            return back()->with('warning', 'Berkas lamaran belum diunggah oleh Rekrutor / AS.');
        }

        $berkas = $candidate->berkas_lamaran;
        $pathsToCheck = [
            public_path($berkas),
            public_path('lampiran/' . basename($berkas)),
            public_path('lampiran/berkas_lamaran/' . basename($berkas)),
            storage_path('app/public/' . $berkas),
            storage_path('app/public/berkas_lamaran/' . basename($berkas)),
        ];

        foreach ($pathsToCheck as $p) {
            if (file_exists($p) && !is_dir($p)) {
                return response()->file($p);
            }
        }

        // Jika path berupa URL eksternal
        if (str_starts_with($berkas, 'http://') || str_starts_with($berkas, 'https://')) {
            return redirect($berkas);
        }

        // Fallback: Jika file fisik belum ada di server lokal, arahkan ke legacy asystem
        return redirect('https://asystem.co.id/interview/lampiran/' . rawurlencode(basename($berkas)), 302);
    }
}
