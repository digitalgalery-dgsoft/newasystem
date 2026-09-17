<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Principle;
use App\Models\User;
use Illuminate\Http\Request;

class CandidateController extends Controller
{
    public function index(Request $request)
    {
        $query = Candidate::with(['principle', 'recruiter', 'assessment', 'testResults'])
            ->latest('id');

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('area')) {
            $query->where('area', $request->area);
        }
        if ($request->filled('principle_id')) {
            $query->where('principle_id', $request->principle_id);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'ilike', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('applied_job', 'ilike', "%{$search}%");
            });
        }

        $candidates = $query->paginate(15)->withQueryString();

        // Status Counts
        $statusCounts = [
            'total' => Candidate::count(),
            'new' => Candidate::where('status', 'new')->count(),
            'interview_process' => Candidate::where('status', 'interview_process')->count(),
            'review_principle' => Candidate::where('status', 'review_principle')->count(),
            'passed' => Candidate::where('status', 'passed')->count(),
            'archived' => Candidate::where('status', 'archived')->count(),
        ];

        $principles = Principle::where('is_active', true)->orderBy('name')->get();
        $distinctAreas = Candidate::select('area')->distinct()->whereNotNull('area')->pluck('area');

        return view('candidates.index', compact('candidates', 'statusCounts', 'principles', 'distinctAreas'));
    }

    public function create()
    {
        $principles = Principle::where('is_active', true)->orderBy('name')->get();
        $recruiters = User::where('role', 'recruiter')->orWhere('role', 'admin')->get();

        return view('candidates.create', compact('principles', 'recruiters'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nik' => 'required|digits:16|unique:candidates,nik',
            'full_name' => 'required|string|max:150',
            'birth_date' => 'required|date',
            'gender' => 'required|string',
            'education' => 'required|string',
            'phone' => 'required|string|max:20',
            'applied_job' => 'required|string|max:150',
            'area' => 'required|string|max:100',
            'principle_id' => 'nullable|exists:principles,id',
            'expected_salary' => 'nullable|numeric',
        ]);

        $candidate = Candidate::create($validated + [
            'status' => 'new',
            'source_type' => 'walk_in',
            'whatsapp' => $validated['phone'],
            'is_profile_complete' => false,
        ]);

        return redirect()->route('candidates.index')->with('success', "Kandidat {$candidate->full_name} berhasil didaftarkan.");
    }

    public function show(Candidate $candidate)
    {
        $candidate->load(['principle', 'recruiter', 'workExperiences', 'assessment', 'testResults', 'principleApproval']);
        return view('candidates.show', compact('candidate'));
    }

    public function getWhatsAppMessage(Candidate $candidate)
    {
        $password = $candidate->birth_date ? $candidate->birth_date->format('dmY') : '12345678';
        $clientName = $candidate->principle ? $candidate->principle->name : 'ASystem Arina';

        $text = "Halo {$candidate->full_name},\n\n"
              . "*Undangan Tes Online & Interview - {$clientName}*\n\n"
              . "Berikut akun akses tes online Anda:\n"
              . "• Username (NIK): *{$candidate->nik}*\n"
              . "• Password: *{$password}* (format tanggal lahir: ddmmyyyy)\n"
              . "• Posisi: *{$candidate->applied_job}*\n"
              . "• Area: *{$candidate->area}*\n\n"
              . "Silakan login dan lengkapi data profil sebelum sesi wawancara.\n\n"
              . "Terima kasih.\nHR Recruitment";

        $cleanPhone = preg_replace('/[^0-9]/', '', $candidate->phone ?? $candidate->whatsapp);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        }

        $waUrl = "https://api.whatsapp.com/send?phone={$cleanPhone}&text=" . urlencode($text);

        return response()->json([
            'phone' => $cleanPhone,
            'message' => $text,
            'url' => $waUrl,
        ]);
    }
}