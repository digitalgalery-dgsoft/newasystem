<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobSpec;
use App\Models\Candidate;
use App\Models\Principle;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PublicJobController extends Controller
{
    /**
     * Halaman Utama Lowongan Kerja Publik (Replikasi job.php)
     */
    public function index(Request $request)
    {
        $search = $request->query('search') ?? $request->query('q');
        $selectedJob = $request->query('job');
        $selectedArea = $request->query('area');
        $selectedCity = $request->query('city');

        // Dropdown distinct list
        $distinctJobs = JobSpec::where('status', 'active')->distinct()->orderBy('job_title')->pluck('job_title');
        $distinctAreas = JobSpec::where('status', 'active')->distinct()->orderBy('job_area')->pluck('job_area');
        $distinctCities = JobSpec::where('status', 'active')->whereNotNull('city')->where('city', '!=', '')->distinct()->orderBy('city')->pluck('city');

        // Base query
        $query = JobSpec::where('status', 'active');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('job_title', 'like', "%{$search}%")
                  ->orWhere('job_desc', 'like', "%{$search}%")
                  ->orWhere('job_skills', 'like', "%{$search}%")
                  ->orWhere('job_area', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        if (!empty($selectedJob)) {
            $query->where('job_title', $selectedJob);
        }

        if (!empty($selectedArea)) {
            $query->where('job_area', $selectedArea);
        }

        if (!empty($selectedCity)) {
            $query->where('city', $selectedCity);
        }

        $jobs = $query->orderBy('created_at', 'desc')->paginate(12)->withQueryString();

        // Hitung total pelamar untuk setiap lowongan
        foreach ($jobs as $j) {
            $j->applicant_count = Candidate::where('applied_job', $j->job_title)
                ->where(function ($q) use ($j) {
                    $q->where('area', $j->job_area)
                      ->orWhereNull('area');
                })->count();
        }

        return view('job.index', compact(
            'jobs',
            'distinctJobs',
            'distinctAreas',
            'distinctCities',
            'search',
            'selectedJob',
            'selectedArea',
            'selectedCity'
        ));
    }

    /**
     * Halaman Detail Lowongan Kerja (Replikasi job_detail.php)
     */
    public function show($id)
    {
        $job = JobSpec::findOrFail($id);

        // Hitung total pelamar
        $totalApplicants = Candidate::where('applied_job', $job->job_title)
            ->where(function ($q) use ($job) {
                $q->where('area', $job->job_area)
                  ->orWhereNull('area');
            })->count();

        // Rekomendasi lowongan sejenis lainnya
        $otherJobs = JobSpec::where('status', 'active')
            ->where('id', '!=', $job->id)
            ->where(function ($q) use ($job) {
                $q->where('job_area', $job->job_area)
                  ->orWhere('job_title', 'like', "%{$job->job_title}%");
            })
            ->take(3)
            ->get();

        return view('job.show', compact('job', 'totalApplicants', 'otherJobs'));
    }

    /**
     * Form Pendaftaran Lowongan Kerja (Replikasi job_apply.php)
     */
    public function applyForm($id)
    {
        $job = JobSpec::findOrFail($id);

        $provinces = [
            'DKI Jakarta', 'Jawa Barat', 'Jawa Tengah', 'Jawa Timur', 
            'Banten', 'DI Yogyakarta', 'Sumatera Utara', 'Sumatera Selatan',
            'Sulawesi Selatan', 'Bali', 'Kalimantan Timur'
        ];

        return view('job.apply', compact('job', 'provinces'));
    }

    /**
     * Submit Form Pendaftaran Pelamar (Replikasi proses_apply_job.php)
     */
    public function submitApply(Request $request, $id)
    {
        $job = JobSpec::findOrFail($id);

        $request->validate([
            'nik' => 'required|string|min:16|max:16',
            'nama_lengkap' => 'required|string|max:255',
            'tgl_lahir' => 'required|date',
            'no_wa' => 'required|string|max:20',
            'alamat_ktp' => 'required|string',
            'pendidikan' => 'required|string',
        ]);

        $nik = $request->input('nik');

        // Cari atau buat candidate
        $candidate = Candidate::where('nik', $nik)->first();
        if ($candidate && $candidate->status === 'Active' && $candidate->applied_job === $job->job_title) {
            return back()->with('info', "Anda sudah pernah mendaftar posisi {$job->job_title} dengan NIK {$nik}. Akun Anda telah aktif, silakan login ke portal tes online.");
        }

        // Tentukan principle jika ada
        $principle = Principle::where('name', 'like', "%{$job->job_prinsiple}%")->first();

        // AI Score matching simulation
        $simulatedAiScore = rand(82, 95);
        $kategoriKandidat = $simulatedAiScore >= 85 ? 'Green' : 'Yellow';

        $aiAnalysis = [
            'evaluation_match_score' => $simulatedAiScore,
            'executive_summary' => "Pelamar {$request->input('nama_lengkap')} memiliki latar belakang pendidikan {$request->input('pendidikan')} dan keterampilan yang sesuai dengan kualifikasi {$job->job_title}. Siap diproses untuk tahapan interview.",
            'key_strengths' => $job->skills_array ?: ['Komunikasi Efektif', 'Kedisiplinan', 'Kerjasama Tim'],
            'suitability_reason' => "Kualifikasi dan motivasi kerja selaras dengan deskripsi pekerjaan {$job->job_title}.",
            'rekomendasi' => 'Sangat Direkomendasikan untuk Seleksi Lanjutan'
        ];

        $birthDateFormatted = Carbon::parse($request->input('tgl_lahir'))->format('dmY');

        $candidateData = [
            'nik' => $nik,
            'full_name' => $request->input('nama_lengkap'),
            'birth_date' => $request->input('tgl_lahir'),
            'height' => $request->input('tinggi', 165),
            'weight' => $request->input('berat', 55),
            'address_ktp' => $request->input('alamat_ktp'),
            'address_domicile' => $request->input('alamat_domisili', $request->input('alamat_ktp')),
            'phone' => $request->input('no_wa'),
            'whatsapp' => $request->input('no_wa'),
            'education' => $request->input('pendidikan'),
            'applied_job' => $job->job_title,
            'area' => $job->job_area ?? 'JAKARTA',
            'principle_id' => $principle?->id ?? 1,
            'source_type' => 'Job Portal',
            'jenis' => 'Job Portal',
            'status' => 'Active',
            'status_kandidat' => 'Baru',
            'work_motivation' => $request->input('motivasi', 'Ingin berkarier dan mengembangkan potensi di perusahaan'),
            'strengths' => $request->input('kelebihan', 'Pekerja keras, teliti, dan bertanggung jawab'),
            'ai_score' => $simulatedAiScore,
            'kategori_kandidat' => $kategoriKandidat,
            'ai_cv_analysis' => json_encode($aiAnalysis),
            'is_profile_complete' => 1,
            'password' => Hash::make($birthDateFormatted),
            'useras' => $job->created_by ?? 'Publik',
        ];

        if ($candidate) {
            $candidate->update($candidateData);
        } else {
            $candidate = Candidate::create($candidateData);
        }

        $successMessage = "Selamat, berkas pendaftaran posisi {$job->job_title} berhasil dikirim! Akun Anda aktif. Gunakan Username: {$nik} dan Password: {$birthDateFormatted} untuk masuk ke Tes Online.";

        return redirect()->route('job.apply', $job->id)
            ->with('success', $successMessage)
            ->with('registered_nik', $nik)
            ->with('registered_pass', $birthDateFormatted);
    }
}
