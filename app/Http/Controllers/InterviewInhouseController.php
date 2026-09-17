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
        return auth()->user() ?? User::firstOrCreate(
            ['email' => 'jamil@asystem.co.id'],
            [
                'name' => 'Jamil Abdurrahman',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'job_title' => 'HRD & IT Support Lead',
                'area' => 'JAKARTA',
                'phone' => '081234567890',
            ]
        );
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
        $jobTitle = strtoupper($user->job_title ?? 'REKRUTMEN');
        $area = strtoupper($user->area ?? 'JAKARTA');
        $pesan = "{$salam} sdr/sdr(i) {$candidate->full_name}\n\n*Tes Online Inhouse {$induk}*\n\nBerikut Kode Akses Tes Online Kamu\n\nUsername : {$candidate->nik}\nPassword : _Gunakan Tanggal lahir dengan Format ddmmyyyy_\n\nAkses Melalui Link Berikut https://asystem.co.id/interview\n\nTutorial Cara Login & Isi Data Profile : https://youtu.be/l3KW9-13z7c\n\n_Terima Kasih_\n\n_Regards_\n" . ucwords(strtolower($user->name)) . " - {$jobTitle} {$area}";

        $phone = $candidate->clean_whatsapp;
        return "https://web.whatsapp.com/send?phone={$phone}&text=" . urlencode($pesan);
    }

    /**
     * Halaman Utama Kandidat Inhouse: Replikasi interviewinhouse.php
     */
    public function index(Request $request)
    {
        $user = $this->getCurrentUser();
        $salam = $this->getSalam();
        $search = $request->query('search') ?? $request->query('q');
        $statusReplace = $request->query('status_replace');
        $statusApproval = $request->query('status_approval');

        // Base Query: Kandidat Inhouse Aktif
        $inhousePrinciples = [
            'PT ARINA MULTI KARYA', 'PT Arina Multikarya',
            'PT ALVA KARYA PERKASA', 'PT Alva Karya Perkasa',
            'PT ANUGRAH TERPERCAYA KERJA', 'PT Anugrah Terpercaya Kerja',
            'PT ABADI BERKAT ODELIA', 'PT Abadi Berkat Odelia',
            'PT ABADAI BERKAT ODELIA'
        ];

        $baseQuery = Candidate::with(['principle', 'recruiter', 'testResults', 'inhouseApprovals'])
            ->where('status', 'Active')
            ->where(function ($q) use ($inhousePrinciples) {
                $q->where('is_inhouse', 1)
                  ->orWhereHas('principle', function ($pq) use ($inhousePrinciples) {
                      $pq->whereIn('name', $inhousePrinciples);
                  });
            });

        // Hitung Statistik
        $totalInhouse = (clone $baseQuery)->count();
        $countBaru = (clone $baseQuery)->where(function ($q) {
            $q->where('status_replace', 'Baru')
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

        // Filter tabel
        $query = clone $baseQuery;

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
            'totalInhouse',
            'countBaru',
            'countReplace',
            'countPending',
            'countApproved',
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
        $candidate = Candidate::with([
            'principle',
            'recruiter',
            'workExperiences',
            'interviewAssessment',
            'testResults',
            'inhouseApprovals'
        ])->findOrFail($id);

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

        return view('interviewinhouse.show', compact('candidate', 'user', 'principles', 'areas', 'statusBadge'));
    }

    /**
     * Simpan Approval & Tanda Tangan Digital Inhouse (Replikasi savettdhrd.php & savettdhead.php)
     */
    public function storeApproval(Request $request, $id)
    {
        $candidate = Candidate::findOrFail($id);
        $user = $this->getCurrentUser();

        // Handle JSON or Form Request
        $isJson = $request->isJson() || $request->wantsJson() || $request->header('Content-Type') === 'application/json';
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

        // Tanda tangan image processing
        $fileName = null;
        if (!empty($imageData)) {
            $fileName = $imageData; // simpan data URL atau path
        }

        if ($submitType === 'hrd') {
            // Replikasi savettdhrd.php
            $candidate->update([
                'status_approval' => $approval === 'Yes' || $approval === 'Approve' ? 'Approve' : 'Tolak',
                'note_principle' => $catatan,
                'signature_path' => $fileName ?? $candidate->signature_path,
            ]);

            // Catat juga ke inhouse_approvals
            InhouseApproval::create([
                'candidate_id' => $candidate->id,
                'nama_approver' => $user->name,
                'jabatan_approver' => $user->job_title ?? 'HRD Lead',
                'catatan_approver' => $catatan,
                'status' => $approval === 'Yes' || $approval === 'Approve' ? 'Approve' : 'Tolak',
                'ttd_approver' => $fileName,
                'time_approver' => Carbon::now('Asia/Jakarta'),
            ]);

            $msg = 'Tanda tangan & Keputusan HRD berhasil disimpan!';
        } else {
            // Replikasi savettdhead.php
            InhouseApproval::create([
                'candidate_id' => $candidate->id,
                'nama_approver' => $user->name,
                'jabatan_approver' => $user->job_title ?? 'Head Approver',
                'catatan_approver' => $catatan,
                'status' => $approval === 'Yes' || $approval === 'Approve' ? 'Approve' : 'Tolak',
                'ttd_approver' => $fileName,
                'time_approver' => Carbon::now('Asia/Jakarta'),
            ]);

            // Update candidate status
            $candidate->update([
                'status_approval' => $approval === 'Yes' || $approval === 'Approve' ? 'Review HRD' : 'Tolak',
            ]);

            $msg = 'Tanda tangan & Keputusan Head Approver berhasil disubmit!';
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
     * Download Berkas Lamaran
     */
    public function downloadBerkas($id)
    {
        $candidate = Candidate::findOrFail($id);
        if (empty($candidate->berkas_lamaran)) {
            return back()->with('warning', 'Berkas lamaran belum diunggah oleh kandidat.');
        }

        // Return direct PDF printout of candidate if uploaded file not physically present
        return redirect()->route('interview.pdf', $candidate->id);
    }
}
