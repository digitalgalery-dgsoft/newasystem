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

        // Halaman kandidat inhouse HANYA untuk user HRD dan Head dari user (Rekrutor / AS)
        if (!$user->isHrdOrHead()) {
            return redirect()->route('interview.index')->with('error', 'Akses Ditolak! Halaman Kandidat Inhouse hanya dapat diakses oleh HRD dan Head / Pimpinan untuk proses approval.');
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

        $baseQuery = Candidate::with(['principle', 'recruiter', 'testResults', 'inhouseApprovals'])
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

        // 2. Hak Akses: HRD melihat semua inhouse, Head HANYA melihat kandidat yang dihandle rekruter/AS binaannya
        if ($user->isHrd()) {
            // HRD / Super Admin: Melihat seluruh kandidat inhouse (dibatasi scope bila diset)
            $user->applyRoleScopeToCandidates($baseQuery);
        } else {
            // Head dari user (Rekrutor / AS) yang handle kandidat tersebut
            $subIdentifiers = $user->getSubordinateRecruiterIdentifiers();
            $headArea = !empty($user->area) ? strtoupper(trim($user->area)) : null;
            $allowedAreas = array_map('strtoupper', $user->getEffectiveAreas());

            $baseQuery->where(function ($q) use ($subIdentifiers, $headArea, $allowedAreas, $user) {
                $hasCondition = false;
                if (!empty($subIdentifiers)) {
                    $q->whereIn(\Illuminate\Support\Facades\DB::raw('LOWER(TRIM(useras))'), $subIdentifiers);
                    $hasCondition = true;
                }
                if (!empty($allowedAreas)) {
                    if ($hasCondition) {
                        $q->orWhereIn(\Illuminate\Support\Facades\DB::raw('UPPER(TRIM(area))'), $allowedAreas);
                    } else {
                        $q->whereIn(\Illuminate\Support\Facades\DB::raw('UPPER(TRIM(area))'), $allowedAreas);
                        $hasCondition = true;
                    }
                } elseif (!empty($headArea)) {
                    if ($hasCondition) {
                        $q->orWhere(\Illuminate\Support\Facades\DB::raw('UPPER(TRIM(area))'), $headArea);
                    } else {
                        $q->where(\Illuminate\Support\Facades\DB::raw('UPPER(TRIM(area))'), $headArea);
                        $hasCondition = true;
                    }
                }
                $q->orWhere('recruiter_id', $user->id);
            });
        }

        // Hitung Statistik
        // Hitung Statistik
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

        // Navigasi Tabs: 'process' (default) vs 'done'
        $tab = $request->query('tab', 'process');
        if (!in_array($tab, ['process', 'done'])) {
            $tab = 'process';
        }

        // Filter tabel
        $query = clone $baseQuery;

        if ($tab === 'done') {
            $query->where('status_approval', 'Approve');
        } else {
            $query->where(function ($q) {
                $q->whereNull('status_approval')
                  ->orWhere('status_approval', '!=', 'Approve');
            });
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
                $query->where(function ($q) {
                    $q->whereNull('status_approval')
                      ->orWhere('status_approval', '')
                      ->orWhereIn('status_approval', ['Review HRD', 'Review Head', 'Proses']);
                });
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

        if (!$user->isHrdOrHead()) {
            return redirect()->route('interview.index')->with('error', 'Akses Ditolak! Halaman Detail Inhouse hanya dapat diakses oleh HRD dan Head / Pimpinan.');
        }

        $candidate = Candidate::with([
            'principle',
            'recruiter',
            'workExperiences',
            'interviewAssessment',
            'testResults',
            'inhouseApprovals'
        ])->findOrFail($id);

        if (!self::isCandidateInhouse($candidate)) {
            return redirect()->route('interview.index')->with('error', 'Akses Ditolak! Kandidat ini bukan kandidat inhouse 5 entitas.');
        }

        // Jika user adalah Head (dan bukan HRD), periksa apakah kandidat ini dihandle oleh timnya
        if (!$user->isHrd()) {
            $subIdentifiers = $user->getSubordinateRecruiterIdentifiers();
            $candUseras = strtolower(trim($candidate->useras ?? ''));
            $isSub = in_array($candUseras, $subIdentifiers) || ($candidate->recruiter_id == $user->id);
            $headArea = !empty($user->area) ? strtoupper(trim($user->area)) : null;
            $candArea = strtoupper(trim($candidate->area ?? ''));
            $allowedAreas = array_map('strtoupper', $user->getEffectiveAreas());
            $isAreaMatch = (!empty($allowedAreas) && in_array($candArea, $allowedAreas)) || (!empty($headArea) && $candArea === $headArea);

            if (!$isSub && !$isAreaMatch) {
                return redirect()->route('interviewinhouse.index')->with('error', 'Akses Ditolak! Anda bukan Head yang menangani kandidat inhouse ini.');
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
            default => ['text' => 'Proses Review', 'class' => 'bg-amber-50 text-amber-700 border-amber-200']
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
        ], $evalData));
    }


    /**
     * Simpan Approval & Tanda Tangan Digital Inhouse (Replikasi savettdhrd.php & savettdhead.php)
     */
    public function storeApproval(Request $request, $id)
    {
        $candidate = Candidate::findOrFail($id);
        $user = $this->getCurrentUser();
        $isJson = $request->isJson() || $request->wantsJson() || $request->header('Content-Type') === 'application/json';

        if (!$user || !$user->isHrdOrHead()) {
            if ($isJson) {
                return response()->json(['status' => 'error', 'message' => 'Akses ditolak! Anda bukan HRD atau Head approver.'], 403);
            }
            return back()->with('error', 'Akses ditolak! Anda bukan HRD atau Head approver.');
        }

        if (!self::isCandidateInhouse($candidate)) {
            if ($isJson) {
                return response()->json(['status' => 'error', 'message' => 'Kandidat ini bukan kandidat inhouse 5 entitas.'], 422);
            }
            return back()->with('error', 'Kandidat ini bukan kandidat inhouse 5 entitas.');
        }

        $catatan = $request->input('catatan');
        $approval = $request->input('approval') ?? $request->input('status') ?? 'Approve';
        $imageData = $request->input('image') ?? $request->input('signature_data');
        $submitType = $request->input('submit_type', 'head'); // 'hrd' atau 'head'

        if (empty($catatan) || empty($approval)) {
            if ($isJson) {
                return response()->json(['status' => 'error', 'message' => 'Catatan dan hasil keputusan wajib diisi!'], 422);
            }
            return back()->with('error', 'Catatan dan hasil keputusan wajib diisi!');
        }

        // Proteksi Step Locking
        if ($submitType === 'hrd') {
            if (!$user->isHrd()) {
                $msg = 'Akses ditolak! Hanya role HRD yang berhak melakukan submit persetujuan HRD Pusat.';
                return $isJson ? response()->json(['status' => 'error', 'message' => $msg], 403) : back()->with('error', $msg);
            }
            if ($candidate->status_approval === 'Review Head') {
                $msg = 'Akses ditolak! Kandidat belum disetujui oleh Head Approver (Step 1). Menunggu persetujuan Head terlebih dahulu.';
                return $isJson ? response()->json(['status' => 'error', 'message' => $msg], 422) : back()->with('error', $msg);
            }
        } elseif ($submitType === 'head') {
            if (in_array($candidate->status_approval, ['Review HRD', 'Approve'])) {
                $msg = 'Kandidat ini sudah disetujui oleh Head Approver dan telah berada di tahap HRD Pusat.';
                return $isJson ? response()->json(['status' => 'error', 'message' => $msg], 422) : back()->with('error', $msg);
            }
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

        if ($submitType === 'hrd') {
            // Replikasi savettdhrd.php
            $finalStatus = ($approval === 'Yes' || $approval === 'Approve') ? 'Approve' : 'Tolak';
            
            $candidate->update([
                'status_approval' => $finalStatus,
                'note_principle' => $catatan,
                'ttd_prinsiple' => $fileName ?? $candidate->ttd_prinsiple ?? $candidate->signature_path,
                'time_prinsiple' => Carbon::now('Asia/Jakarta'),
            ]);

            // Catat juga ke inhouse_approvals
            InhouseApproval::create([
                'candidate_id' => $candidate->id,
                'nama_approver' => $user->name,
                'jabatan_approver' => $user->job_title ?? 'ADMIN HRD Jakarta',
                'catatan_approver' => $catatan,
                'status' => $finalStatus,
                'ttd_approver' => $fileName,
                'time_approver' => Carbon::now('Asia/Jakarta'),
            ]);

            // Sinkronisasi ke tb_catataninhouse
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('tb_catataninhouse')) {
                    \Illuminate\Support\Facades\DB::table('tb_catataninhouse')->insert([
                        'id_kandidat' => $candidate->id,
                        'nama_approver' => $user->name,
                        'jabatan_aprover' => $user->job_title ?? 'ADMIN HRD',
                        'catatan_approver' => $catatan,
                        'status' => ($approval === 'Yes' || $approval === 'Approve') ? 'Yes' : 'No',
                        'ttd_approver' => $fileName ? basename($fileName) : null,
                        'time_approver' => Carbon::now('Asia/Jakarta')->toDateTimeString(),
                    ]);
                }
            } catch (\Throwable $e) {}

            $msg = 'Persetujuan & Tanda Tangan HRD Pusat berhasil disimpan! Kandidat selesai diproses.';
        } else {
            // Replikasi savettdhead.php
            $finalStatus = ($approval === 'Yes' || $approval === 'Approve') ? 'Approve' : 'Tolak';

            InhouseApproval::create([
                'candidate_id' => $candidate->id,
                'nama_approver' => $user->name,
                'jabatan_approver' => $user->job_title ?? 'Head Approver',
                'catatan_approver' => $catatan,
                'status' => $finalStatus,
                'ttd_approver' => $fileName,
                'time_approver' => Carbon::now('Asia/Jakarta'),
            ]);

            // Update candidate status ke Review HRD
            $candidate->update([
                'status_approval' => ($approval === 'Yes' || $approval === 'Approve') ? 'Review HRD' : 'Tolak',
            ]);

            // Sinkronisasi ke tb_catataninhouse
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('tb_catataninhouse')) {
                    \Illuminate\Support\Facades\DB::table('tb_catataninhouse')->insert([
                        'id_kandidat' => $candidate->id,
                        'nama_approver' => $user->name,
                        'jabatan_aprover' => $user->job_title ?? 'OM',
                        'catatan_approver' => $catatan,
                        'status' => ($approval === 'Yes' || $approval === 'Approve') ? 'Yes' : 'No',
                        'ttd_approver' => $fileName ? basename($fileName) : null,
                        'time_approver' => Carbon::now('Asia/Jakarta')->toDateTimeString(),
                    ]);
                }
            } catch (\Throwable $e) {}

            $msg = 'Persetujuan Head Approver berhasil disubmit! Kandidat kini diteruskan ke HRD Pusat.';
        }

        if ($isJson) {
            return response()->json([
                'status' => 'success',
                'message' => $msg,
                'redirect' => route('interviewinhouse.show', $candidate->id)
            ]);
        }

        return redirect()->route('interviewinhouse.show', $candidate->id)->with('success', $msg);
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
