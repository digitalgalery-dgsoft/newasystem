<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Principle;
use App\Models\User;
use App\Models\InterviewAssessment;
use App\Models\WorkExperience;
use App\Models\TestResult;
use Illuminate\Http\Request;
use Carbon\Carbon;

class InterviewController extends Controller
{
    private function getCurrentUser()
    {
        return auth()->user() ?? User::firstOrCreate(
            ['email' => 'jamil@asystem.co.id'],
            [
                'name' => 'Jamil',
                'password' => bcrypt('password'),
                'role' => 'recruiter',
                'job_title' => 'REKRUTMEN',
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
        $induk = $candidate->principle?->parent_company ?? $candidate->principle?->name ?? 'ESA Groups';
        $jobTitle = strtoupper($user->job_title ?? 'REKRUTMEN');
        $area = strtoupper($user->area ?? 'JAKARTA');
        $pesan = "{$salam} sdr/sdr(i) {$candidate->full_name}\n\n*Tes Online {$induk}*\n\nBerikut Kode Akses Tes Online Kamu\n\nUsername : {$candidate->nik}\nPassword : _Gunakan Tanggal lahir dengan Format ddmmyyyy_\n\nAkses Melalui Link Berikut https://asystem.co.id/interview\n\nTutorial Cara Login & Isi Data Profile : https://youtu.be/l3KW9-13z7c\n\n_Terima Kasih_\n\n_Regards_\n" . ucwords(strtolower($user->name)) . " - {$jobTitle} {$area}";

        $phone = $candidate->clean_whatsapp;
        return "https://web.whatsapp.com/send?phone={$phone}&text=" . urlencode($pesan);
    }

    /**
     * Halaman Utama: Replikasi interview.php
     */
    public function index(Request $request)
    {
        $user = $this->getCurrentUser();
        $salam = $this->getSalam();
        $searchMy = $request->query('search_my');
        $searchArea = $request->query('search_area');

        // Query 1: Data Kandidat Milik Sendiri (atau semua jika admin/rekrutmen)
        $myCandidatesQuery = Candidate::with(['principle', 'recruiter', 'testResults'])
            ->where('status', 'Active')
            ->where(function ($q) {
                $q->whereNull('note_principle')->orWhere('note_principle', '');
            });

        if ($user->role !== 'admin' && $user->role !== 'recruiter') {
            $myCandidatesQuery->where(function ($q) use ($user) {
                $q->where('recruiter_id', $user->id)
                  ->orWhere('useras', $user->email);
            });
        }

        if ($searchMy) {
            $myCandidatesQuery->where(function ($q) use ($searchMy) {
                $q->where('full_name', 'like', "%{$searchMy}%")
                  ->orWhere('nik', 'like', "%{$searchMy}%")
                  ->orWhere('applied_job', 'like', "%{$searchMy}%");
            });
        }

        $myCandidates = $myCandidatesQuery->orderBy('id', 'desc')->paginate(15, ['*'], 'page_my');

        // Tambahkan atribut wa_url untuk setiap kandidat
        $myCandidates->getCollection()->transform(function ($c) use ($user, $salam) {
            $c->wa_url = $this->buildWaUrl($c, $user, $salam);
            return $c;
        });

        // Query 2: Data Kandidat Area (Rekan se-area)
        $areaCandidatesQuery = Candidate::with(['principle', 'recruiter', 'testResults'])
            ->where('status', 'Active')
            ->where(function ($q) {
                $q->whereNull('note_principle')->orWhere('note_principle', '');
            })
            ->where('area', $user->area ?? 'JAKARTA')
            ->where(function ($q) use ($user) {
                $q->where('recruiter_id', '!=', $user->id)
                  ->orWhereNull('recruiter_id');
            });

        if ($searchArea) {
            $areaCandidatesQuery->where(function ($q) use ($searchArea) {
                $q->where('full_name', 'like', "%{$searchArea}%")
                  ->orWhere('nik', 'like', "%{$searchArea}%")
                  ->orWhere('applied_job', 'like', "%{$searchArea}%");
            });
        }

        $areaCandidates = $areaCandidatesQuery->orderBy('id', 'desc')->paginate(15, ['*'], 'page_area');
        $areaCandidates->getCollection()->transform(function ($c) use ($user, $salam) {
            $c->wa_url = $this->buildWaUrl($c, $user, $salam);
            return $c;
        });

        $principles = Principle::where('is_active', true)->orderBy('name')->get();

        return view('interview.index', compact(
            'user',
            'salam',
            'myCandidates',
            'areaCandidates',
            'principles',
            'searchMy',
            'searchArea'
        ));
    }

    /**
     * Halaman Detail & Evaluasi: Replikasi hasil.php
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
            'principleApprovals'
        ])->findOrFail($id);

        $principles = Principle::where('is_active', true)->orderBy('name')->get();

        return view('interview.show', compact('candidate', 'user', 'principles'));
    }

    /**
     * Simpan Hasil Interview (Form Matrix 1-5)
     */
    public function storeAssessment(Request $request, $id)
    {
        $candidate = Candidate::findOrFail($id);
        $user = $this->getCurrentUser();

        $km = $request->input("kemauan_kerja", $request->input("work_motivation_score", "Cukup"));
        $pen = $request->input("penampilan", $request->input("appearance_score", "Cukup"));
        $att = $request->input("attitude", $request->input("attitude_score", "Cukup"));
        $dt = $request->input("daya_tangkap", $request->input("comprehension_score", "Cukup"));
        $notes = $request->input("catatan", $request->input("notes", ""));
        $date = $request->input("tgl_interview", $request->input("interview_date", date("Y-m-d")));
        $salary = $request->input("salary_offered", 5500000);
        $area = $request->input("placement_area", $candidate->area ?? "JAKARTA");

        $scoreMap = [
            "Sangat Baik" => 5,
            "Baik" => 4,
            "Cukup" => 3,
            "Kurang" => 2,
            5 => 5, 4 => 4, 3 => 3, 2 => 2, 1 => 1
        ];

        InterviewAssessment::updateOrCreate(
            ["candidate_id" => $candidate->id],
            [
                "interviewer_id" => $user->id,
                "interview_date" => $date,
                "work_motivation" => $scoreMap[$km] ?? 3,
                "appearance" => $scoreMap[$pen] ?? 3,
                "attitude" => $scoreMap[$att] ?? 3,
                "comprehension" => $scoreMap[$dt] ?? 3,
                "other_notes" => $notes,
                "recommendation" => "recommended",
                "offered_salary" => $salary,
                "placement_area" => $area,
            ]
        );

        if ($request->has("signature_data") && !empty($request->input("signature_data"))) {
            $sigData = $request->input("signature_data");
            $candidate->update(["signature_path" => $sigData]);
        }

        return redirect()->route("interview.show", $candidate->id)
            ->with("success", "Data Hasil Interview Berhasil Disimpan!");
    }

    public function old_storeAssessment(Request $request, $id)
    {
        $candidate = Candidate::findOrFail($id);
        $user = $this->getCurrentUser();

        $validated = $request->validate([
            'work_motivation_score' => 'required|integer|between:1,5',
            'appearance_score' => 'required|integer|between:1,5',
            'attitude_score' => 'required|integer|between:1,5',
            'comprehension_score' => 'required|integer|between:1,5',
            'notes' => 'nullable|string',
            'salary_offered' => 'nullable|numeric',
            'placement_area' => 'nullable|string',
            'interview_date' => 'required|date',
            'interviewer_signature' => 'nullable|string',
        ]);

        InterviewAssessment::updateOrCreate(
            ['candidate_id' => $candidate->id],
            array_merge($validated, [
                'interviewer_id' => $user->id,
            ])
        );

        return redirect()->route('interview.show', $candidate->id)
            ->with('success', 'Data Hasil Interview Berhasil Disimpan!');
    }

    /**
     * Simpan Referensi Cek
     */
    public function storeRefcek(Request $request, $id)
    {
        $candidate = Candidate::findOrFail($id);

        $validated = $request->validate([
            'work_experience_id' => 'required|exists:work_experiences,id',
            'supervisor_name' => 'required|string',
            'performance' => 'required|string',
            'discipline' => 'required|string',
            'responsibility' => 'required|string',
            'problems' => 'nullable|string',
            'strengths' => 'nullable|string',
            'weaknesses' => 'nullable|string',
            'checked_date' => 'required|date',
            'proof_file' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:4096',
        ]);

        $exp = WorkExperience::where('candidate_id', $candidate->id)
            ->where('id', $validated['work_experience_id'])
            ->firstOrFail();

        $data = [
            'supervisor_name' => $validated['supervisor_name'],
            'supervisor_evaluation' => json_encode([
                'performance' => $validated['performance'],
                'discipline' => $validated['discipline'],
                'responsibility' => $validated['responsibility'],
                'problems' => $validated['problems'] ?? '',
                'strengths' => $validated['strengths'] ?? '',
                'weaknesses' => $validated['weaknesses'] ?? '',
                'checked_date' => $validated['checked_date'],
            ]),
        ];

        if ($request->hasFile('proof_file')) {
            $path = $request->file('proof_file')->store('refcek_proofs', 'public');
            $data['proof_attachment_path'] = $path;
        }

        $exp->update($data);

        return redirect()->route('interview.show', $candidate->id)
            ->with('success', 'Data Referensi Cek Berhasil Disimpan!');
    }

    /**
     * Simpan Hasil Tes Komputer
     */
    public function storeComputerTest(Request $request, $id)
    {
        $candidate = Candidate::findOrFail($id);

        $scores = [
            'vlookup' => $request->input('vlookup'),
            'hlookup' => $request->input('hlookup'),
            'pivot' => $request->input('pivot'),
            'fungsiif' => $request->input('fungsiif'),
            'average' => $request->input('average'),
            'hitung' => $request->input('hitung'),
            'teliti' => $request->input('teliti'),
            'cepat' => $request->input('cepat'),
            'hasilkerja' => $request->input('hasilkerja'),
        ];

        $validItems = count(array_filter($scores, fn($v) => !empty($v)));
        $totalScore = round(($validItems / 9) * 100);

        TestResult::updateOrCreate(
            [
                'candidate_id' => $candidate->id,
                'test_type' => 'computer',
            ],
            [
                'score' => $totalScore,
                'details' => $scores,
                'passed' => $totalScore >= 60,
            ]
        );

        return redirect()->route('interview.show', $candidate->id)
            ->with('success', 'Data Penilaian Tes Komputer Berhasil Disimpan!');
    }

    /**
     * Set Remidi Tes Matematika
     */
    public function setRemidi(Request $request, $id)
    {
        $candidate = Candidate::findOrFail($id);
        $user = $this->getCurrentUser();

        TestResult::where('candidate_id', $candidate->id)
            ->where('test_type', 'math')
            ->delete();

        $salam = $this->getSalam();
        $pesan = "Halo {$candidate->full_name}\n\nNilai Matematika Kamu Belum Memuaskan. Silahkan Lakukan Test Ulang.\n\nAkses Melalui Link Berikut https://asystem.co.id/interview\n\n_Terima Kasih_\n\n_Regards_\n" . ucwords(strtolower($user->name)) . " - " . strtoupper($user->job_title ?? 'REKRUTMEN') . " " . ($user->area ?? 'JAKARTA');

        $waUrl = "https://web.whatsapp.com/send?phone={$candidate->clean_whatsapp}&text=" . urlencode($pesan);

        return redirect()->route('interview.show', $candidate->id)
            ->with('success', 'Remidi Berhasil Diset! Silakan hubungi kandidat untuk mengulang tes.')
            ->with('remidi_wa_url', $waUrl);
    }

    /**
     * Edit Prinsiple Kandidat
     */
    public function editPrinciple(Request $request, $id)
    {
        $candidate = Candidate::findOrFail($id);
        $request->validate(['principle_id' => 'required|exists:principles,id']);

        $candidate->update(['principle_id' => $request->principle_id]);

        return redirect()->route('interview.show', $candidate->id)
            ->with('success', 'Prinsiple Kandidat Berhasil Diperbarui!');
    }

    /**
     * Arsipkan Kandidat
     */
    public function archive(Request $request, $id)
    {
        $candidate = Candidate::findOrFail($id);
        $request->validate(['archive_reason' => 'required|string']);

        $candidate->update([
            'status' => 'Arsip',
            'archive_reason' => $request->archive_reason,
        ]);

        return redirect()->route('interview.index')
            ->with('success', "Kandidat {$candidate->full_name} Berhasil Diarsipkan!");
    }

    /**
     * Halaman Walk Interview (Replikasi walkinterview.php)
     */
    public function walkInterview(Request $request)
    {
        $user = $this->getCurrentUser();
        $startDate = $request->query('start_date', Carbon::today()->toDateString());
        $endDate = $request->query('end_date', Carbon::today()->toDateString());
        $search = $request->query('search');

        $query = Candidate::with(['principle', 'recruiter', 'testResults'])
            ->where('status', 'Active')
            ->where('source_type', 'walk_in');

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);
        }

        if ($user->role !== 'admin') {
            $query->where('area', $user->area ?? 'JAKARTA');
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $candidates = $query->orderBy('id', 'desc')->paginate(20);

        return view('interview.walk', compact('candidates', 'user', 'startDate', 'endDate', 'search'));
    }

    /**
     * Halaman Done (Replikasi interviewdone.php)
     */
    public function done(Request $request)
    {
        $user = $this->getCurrentUser();
        $search = $request->query('search');

        $query = Candidate::with(['principle', 'recruiter', 'testResults'])
            ->where('status', 'Active')
            ->whereNotNull('note_principle')
            ->where('note_principle', '!=', '');

        if ($user->role !== 'admin' && $user->role !== 'recruiter') {
            $query->where('recruiter_id', $user->id);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $candidates = $query->orderBy('id', 'desc')->paginate(20);

        return view('interview.done', compact('candidates', 'user', 'search'));
    }

    /**
     * Halaman Arsip (Replikasi interviewarsip.php)
     */
    public function arsip(Request $request)
    {
        $user = $this->getCurrentUser();
        $search = $request->query('search');

        $query = Candidate::with(['principle', 'recruiter', 'testResults'])
            ->where('status', 'Arsip');

        if ($user->role !== 'admin' && $user->role !== 'recruiter') {
            $query->where('recruiter_id', $user->id);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $candidates = $query->orderBy('id', 'desc')->paginate(20);

        return view('interview.arsip', compact('candidates', 'user', 'search'));
    }
    /**
     * Download / Stream Document Lengkap dalam format PDF (Replikasi printall.php)
     */
    public function downloadPdf($id)
    {
        $candidate = Candidate::with([
            'principle',
            'recruiter',
            'workExperiences',
            'interviewAssessment',
            'testResults',
            'principleApprovals'
        ])->findOrFail($id);

        $pdfService = new \App\Services\InterviewPdfService();
        $pdfBinary = $pdfService->generate($candidate);

        $filename = 'Dokument Test Online ' . $candidate->full_name . '.pdf';

        return response($pdfBinary, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
            'Cache-Control' => 'private, max-age=0, must-revalidate',
            'Pragma' => 'public',
        ]);
    }
    /**
     * Simpan / Update Approval User Principle dengan Upload Bukti Screenshot
     */
    public function storePrincipleApproval(Request $request, $id)
    {
        $candidate = Candidate::findOrFail($id);

        $status = $request->input('statusapprove') === 'Yes' ? 'Approved' : 'Rejected';
        $notes = $request->input('notes') ?? 'Approval By WA - Email';

        $approval = \App\Models\PrincipleApproval::firstOrNew(['candidate_id' => $candidate->id]);
        $approval->principle_id = $request->input('userprinsiple') ?? $candidate->principle_id ?? 1;
        $approval->status = $status;
        $approval->notes = $notes;
        $approval->approved_at = now();

        if ($request->hasFile('approval_screenshot')) {
            $path = $request->file('approval_screenshot')->store('approvals', 'public');
            $approval->signature_path = $path;
        }
        $approval->save();

        $candidate->status_approval = $status === 'Approved' ? 'Approve' : 'Tolak';
        $candidate->note_principle = $notes;
        $candidate->save();

        return redirect()->route('interview.show', $candidate->id)
            ->with('success', 'Status dan Bukti Approval User Principle berhasil disimpan!');
    }
}