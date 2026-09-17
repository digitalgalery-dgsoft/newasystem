<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Candidate;
use App\Models\Principle;
use App\Models\InterviewAssessment;
use App\Models\WorkExperience;
use App\Models\TestResult;
use Carbon\Carbon;

class KandidatPortalController extends Controller
{
    /**
     * Halaman Utama Pelamar Job Portal (Replikasi kandidatportal.php)
     */
    public function index(Request $request)
    {
        $today = Carbon::today()->format('Y-m-d');
        $tab = $request->query('tab', 'baru');
        $kategori = $request->query('kategori');
        $start = $request->query('start');
        $end = $request->query('end');
        $search = $request->query('q') ?? $request->query('search');

        // Base Query: Pelamar dari Job Portal
        $baseQuery = Candidate::where(function ($q) {
            $q->where('jenis', 'Job Portal')
              ->orWhere('source_type', 'Job Portal')
              ->orWhereNull('source_type');
        });

        // Top Statistics
        $totalPelamar = (clone $baseQuery)->count();
        $masukHariIni = (clone $baseQuery)->whereDate('created_at', $today)->count();
        $kandidatGreen = (clone $baseQuery)->where('kategori_kandidat', 'Green')->count();
        $belumDianalisa = (clone $baseQuery)->where(function ($q) {
            $q->whereNull('ai_score')->orWhere('ai_score', 0);
        })->count();

        // Tab Badges Counters
        $countBaru = (clone $baseQuery)->where(function ($q) {
            $q->where('status_kandidat', 'Baru')
              ->orWhereNull('status_kandidat')
              ->orWhereNotIn('status_kandidat', ['Interview', 'Terima', 'Arsip']);
        })->count();

        $countInterview = (clone $baseQuery)->where('status_kandidat', 'Interview')->count();
        $countTerima = (clone $baseQuery)->where('status_kandidat', 'Terima')->count();
        $countArsip = (clone $baseQuery)->where('status_kandidat', 'Arsip')->count();

        // Filter Table Query based on active tab
        $tableQuery = clone $baseQuery;

        if ($tab === 'interview') {
            $tableQuery->where('status_kandidat', 'Interview');
        } elseif ($tab === 'terima') {
            $tableQuery->where('status_kandidat', 'Terima');
        } elseif ($tab === 'arsip') {
            $tableQuery->where('status_kandidat', 'Arsip');
        } else {
            // Tab 'baru' / default
            $tableQuery->where(function ($q) {
                $q->where('status_kandidat', 'Baru')
                  ->orWhereNull('status_kandidat')
                  ->orWhereNotIn('status_kandidat', ['Interview', 'Terima', 'Arsip']);
            });
        }

        // Filter Kategori
        if (!empty($kategori)) {
            $tableQuery->where('kategori_kandidat', $kategori);
        }

        // Filter Rentang Tanggal
        if (!empty($start) && !empty($end)) {
            $tableQuery->whereBetween('created_at', [
                Carbon::parse($start)->startOfDay(),
                Carbon::parse($end)->endOfDay()
            ]);
        }

        // Pencarian Teks
        if (!empty($search)) {
            $tableQuery->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('applied_job', 'like', "%{$search}%")
                  ->orWhere('area', 'like', "%{$search}%");
            });
        }

        $candidates = $tableQuery->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('kandidatportal.index', compact(
            'candidates',
            'tab',
            'kategori',
            'start',
            'end',
            'search',
            'totalPelamar',
            'masukHariIni',
            'kandidatGreen',
            'belumDianalisa',
            'countBaru',
            'countInterview',
            'countTerima',
            'countArsip'
        ));
    }

    /**
     * Halaman Detail & Evaluasi Kandidat Portal (Replikasi hasilportal.php & detailkandidat.php)
     */
    public function show($id)
    {
        $candidate = Candidate::with([
            'principle',
            'recruiter',
            'workExperiences',
            'interviewAssessment',
            'principleApprovals',
            'testResults'
        ])->findOrFail($id);

        $principles = Principle::where('is_active', true)->orderBy('name')->get();
        $areas = [
            'JAKARTA', 'SURABAYA', 'BANDUNG', 'SEMARANG', 'MEDAN', 
            'MAKASSAR', 'DENPASAR', 'PALEMBANG', 'BALIKPAPAN', 'YOGYAKARTA',
            'MALANG', 'BOGOR', 'BEKASI', 'TANGERANG', 'DEPOK'
        ];

        // Psychological DISC test processing
        $discTest = $candidate->testResults->firstWhere('test_type', 'psychology');
        $mathTest = $candidate->testResults->firstWhere('test_type', 'math');
        $compTest = $candidate->testResults->firstWhere('test_type', 'computer');

        $aiData = $candidate->ai_data;

        return view('kandidatportal.show', compact(
            'candidate',
            'principles',
            'areas',
            'discTest',
            'mathTest',
            'compTest',
            'aiData'
        ));
    }

    /**
     * Reset Password Kandidat ke Tanggal Lahir (ddmmyyyy)
     */
    public function resetPassword(Request $request, $id)
    {
        $candidate = Candidate::findOrFail($id);

        if (!$candidate->birth_date) {
            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal: Tanggal lahir kandidat belum diisi atau format tidak valid.'
                ], 422);
            }
            return back()->with('error', 'Gagal: Tanggal lahir kandidat belum diisi.');
        }

        $plainPassword = $candidate->birth_date->format('dmY');
        $candidate->password = password_hash($plainPassword, PASSWORD_DEFAULT);
        $candidate->save();

        $msg = "Password kandidat <strong>{$candidate->full_name}</strong> berhasil di-reset menjadi: <code>{$plainPassword}</code> (Format ddmmyyyy dari tgl lahir " . $candidate->birth_date->format('d-m-Y') . ").";

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => $msg,
                'plain_password' => $plainPassword
            ]);
        }

        return back()->with('success', $msg);
    }

    /**
     * Simpan / Perbarui Hasil Interview
     */
    public function updateInterview(Request $request, $id)
    {
        $candidate = Candidate::findOrFail($id);

        $validated = $request->validate([
            'work_willingness' => 'required|string',
            'appearance' => 'required|string',
            'attitude' => 'required|string',
            'comprehension' => 'required|string',
            'notes' => 'nullable|string',
            'interview_date' => 'required|date',
            'kategori_kandidat' => 'nullable|string',
            'kategori_industri' => 'nullable|string',
            'status_kandidat' => 'nullable|string',
            'assessor_signature' => 'nullable|string',
        ]);

        // Update assessment
        InterviewAssessment::updateOrCreate(
            ['candidate_id' => $candidate->id],
            [
                'work_willingness' => $validated['work_willingness'],
                'appearance' => $validated['appearance'],
                'attitude' => $validated['attitude'],
                'comprehension' => $validated['comprehension'],
                'notes' => $validated['notes'] ?? null,
                'interview_date' => $validated['interview_date'],
                'assessor_signature' => $validated['assessor_signature'] ?? null,
            ]
        );

        // Update candidate status & categories
        if (!empty($validated['kategori_kandidat'])) {
            $candidate->kategori_kandidat = $validated['kategori_kandidat'];
        }
        if (!empty($validated['kategori_industri'])) {
            $candidate->kategori_industri = $validated['kategori_industri'];
        }
        if (!empty($validated['status_kandidat'])) {
            $candidate->status_kandidat = $validated['status_kandidat'];
        }
        $candidate->save();

        return redirect()->route('kandidatportal.show', $candidate->id)
            ->with('success', 'Hasil Interview kandidat ' . $candidate->full_name . ' berhasil disimpan!');
    }

    /**
     * Alihkan Kandidat ke Account Supervisor (AS) / Prinsiple
     */
    public function alihkanAS(Request $request, $id)
    {
        $candidate = Candidate::findOrFail($id);

        $validated = $request->validate([
            'prinsiple_id' => 'nullable|integer',
            'useras' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $candidate->principle_id = $validated['prinsiple_id'] ?? $candidate->principle_id;
        $candidate->useras = $validated['useras'];
        if (!empty($validated['notes'])) {
            $candidate->notes = $validated['notes'];
        }
        $candidate->status_kandidat = 'Interview';
        $candidate->save();

        return redirect()->route('kandidatportal.show', $candidate->id)
            ->with('success', 'Data kandidat ' . $candidate->full_name . ' berhasil dialihkan ke AS: ' . $validated['useras']);
    }

    /**
     * Ganti Area Penempatan Kandidat
     */
    public function gantiArea(Request $request, $id)
    {
        $candidate = Candidate::findOrFail($id);

        $validated = $request->validate([
            'area' => 'required|string',
        ]);

        $candidate->area = $validated['area'];
        $candidate->save();

        return redirect()->route('kandidatportal.show', $candidate->id)
            ->with('success', 'Area penempatan kandidat ' . $candidate->full_name . ' berhasil diubah menjadi: ' . $validated['area']);
    }

    /**
     * Arsipkan Kandidat
     */
    public function arsipkan(Request $request, $id)
    {
        $candidate = Candidate::findOrFail($id);

        $validated = $request->validate([
            'alasan' => 'required|string',
        ]);

        $candidate->status_kandidat = 'Arsip';
        $candidate->archive_reason = $validated['alasan'];
        $candidate->save();

        return redirect()->route('kandidatportal.index', ['tab' => 'arsip'])
            ->with('warning', 'Kandidat ' . $candidate->full_name . ' berhasil diarsipkan.');
    }
}