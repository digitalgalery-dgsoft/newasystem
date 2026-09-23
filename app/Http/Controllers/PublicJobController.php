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
        $job = is_numeric($id) 
            ? JobSpec::find($id) 
            : JobSpec::where('slug', $id)->first();

        if (!$job) {
            $job = JobSpec::findOrFail($id);
        }

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
        $job = is_numeric($id) 
            ? JobSpec::find($id) 
            : JobSpec::where('slug', $id)->first();

        if (!$job) {
            $job = JobSpec::findOrFail($id);
        }

        $provinces = \App\Services\IndonesiaRegionService::getProvinces();
        $regions = \App\Services\IndonesiaRegionService::getProvincesWithCities();

        return view('job.apply', compact('job', 'provinces', 'regions'));
    }

    /**
     * Submit Form Pendaftaran Pelamar (Replikasi proses_apply_job.php)
     */
    public function submitApply(Request $request, $id)
    {
        $job = is_numeric($id) 
            ? JobSpec::find($id) 
            : JobSpec::where('slug', $id)->first();

        if (!$job) {
            $job = JobSpec::findOrFail($id);
        }

        $request->validate([
            'foto_profil' => 'required|file|mimes:jpeg,png,jpg,webp|max:5120',
            'file_cv' => 'required|file|mimes:pdf,jpeg,png,jpg|max:10240',
            'nik' => 'required|string|size:16|regex:/^[0-9]+$/',
            'nama_lengkap' => 'required|string|max:255',
            'tgl_lahir' => 'required|date',
            'gender' => 'required|in:Laki-laki,Perempuan',
            'tinggi' => 'required|numeric|min:50|max:250',
            'berat' => 'required|numeric|min:20|max:300',
            'alamat_ktp' => 'required|string|min:5',
            'alamat_domisili' => 'required|string|min:5',
            'no_wa' => 'required|string|min:9|max:20',
            'pendidikan' => 'required|string',
            'propinsi_domisili' => 'required|string|max:100',
            'kota_domisili' => 'required|string|max:100',
            'info_lowongan' => 'required|string|max:100',
            'ringkasan_pengalaman' => 'required|string|min:3',
            'motivasi' => 'required|string|min:3',
            'kelebihan' => 'required|string|min:3',
        ], [
            'foto_profil.required' => 'Pas Foto Terbaru wajib diunggah.',
            'foto_profil.file' => 'Berkas Pas Foto tidak valid.',
            'foto_profil.mimes' => 'Format Pas Foto harus berupa JPG, JPEG, PNG, atau WEBP.',
            'foto_profil.max' => 'Ukuran Pas Foto maksimal 5MB.',
            'file_cv.required' => 'Berkas Curriculum Vitae (CV) wajib diunggah.',
            'file_cv.file' => 'Berkas CV tidak valid.',
            'file_cv.mimes' => 'Format Berkas CV harus berupa PDF, JPG, JPEG, atau PNG.',
            'file_cv.max' => 'Ukuran Berkas CV maksimal 10MB.',
            'nik.required' => 'Nomor Induk Kependudukan (NIK) wajib diisi.',
            'nik.size' => 'NIK harus berjumlah tepat 16 digit.',
            'nik.regex' => 'NIK hanya boleh berupa 16 digit angka.',
            'nama_lengkap.required' => 'Nama Lengkap (sesuai KTP) wajib diisi.',
            'tgl_lahir.required' => 'Tanggal Lahir wajib diisi.',
            'tgl_lahir.date' => 'Format Tanggal Lahir tidak valid.',
            'gender.required' => 'Jenis Kelamin wajib dipilih.',
            'gender.in' => 'Pilihan Jenis Kelamin tidak valid.',
            'tinggi.required' => 'Tinggi Badan (cm) wajib diisi.',
            'tinggi.numeric' => 'Tinggi Badan harus berupa angka.',
            'tinggi.min' => 'Tinggi Badan tidak valid (minimal 50 cm).',
            'tinggi.max' => 'Tinggi Badan tidak valid (maksimal 250 cm).',
            'berat.required' => 'Berat Badan (kg) wajib diisi.',
            'berat.numeric' => 'Berat Badan harus berupa angka.',
            'berat.min' => 'Berat Badan tidak valid (minimal 20 kg).',
            'berat.max' => 'Berat Badan tidak valid (maksimal 300 kg).',
            'alamat_ktp.required' => 'Alamat Sesuai KTP wajib diisi.',
            'alamat_ktp.min' => 'Alamat Sesuai KTP minimal 5 karakter.',
            'alamat_domisili.required' => 'Alamat Domisili Sekarang wajib diisi.',
            'alamat_domisili.min' => 'Alamat Domisili Sekarang minimal 5 karakter.',
            'no_wa.required' => 'Nomor WhatsApp Aktif wajib diisi.',
            'no_wa.min' => 'Nomor WhatsApp minimal 9 digit.',
            'pendidikan.required' => 'Pendidikan Terakhir wajib dipilih.',
            'propinsi_domisili.required' => 'Propinsi Domisili wajib dipilih.',
            'kota_domisili.required' => 'Kota/Kabupaten Domisili wajib dipilih.',
            'info_lowongan.required' => 'Sumber Info Lowongan wajib dipilih.',
            'ringkasan_pengalaman.required' => 'Ringkasan Pengalaman Kerja wajib diisi (jika fresh graduate, tulis Fresh Graduate).',
            'ringkasan_pengalaman.min' => 'Ringkasan Pengalaman Kerja minimal 3 karakter.',
            'motivasi.required' => 'Motivasi Bekerja wajib diisi.',
            'motivasi.min' => 'Motivasi Bekerja minimal 3 karakter.',
            'kelebihan.required' => 'Kelebihan & Keterampilan Utama Diri wajib diisi.',
            'kelebihan.min' => 'Kelebihan Diri minimal 3 karakter.',
        ]);

        $nik = $request->input('nik');

        // Cari record kandidat yang sudah ada berdasarkan NIK (paling baru)
        $candidate = Candidate::where('nik', $nik)->orderByDesc('id')->first();

        // Cek apakah kandidat berstatus arsip (baik di portal maupun interview)
        $isArchived = $candidate && (
            in_array(strtolower($candidate->status ?? ''), ['arsip', 'archived'])
            || strtolower($candidate->status_kandidat ?? '') === 'arsip'
        );

        // Cek apakah ada berkas lamaran aktif yang SEDANG berjalan dan BELUM diarsipkan untuk posisi yang sama
        $hasActiveSameJob = Candidate::where('nik', $nik)
            ->where('applied_job', $job->job_title)
            ->where('status', 'Active')
            ->where(function ($q) {
                $q->whereNull('status_kandidat')
                  ->orWhere(function ($sq) {
                      $sq->whereRaw('LOWER(status_kandidat) != ?', ['arsip'])
                         ->whereRaw('LOWER(status) NOT IN (?, ?)', ['arsip', 'archived']);
                  });
            })
            ->exists();

        if ($hasActiveSameJob) {
            return back()->with('info', "Anda sudah pernah mendaftar posisi {$job->job_title} dengan NIK {$nik}. Akun Anda telah aktif, silakan login ke portal tes online.");
        }

        // Tentukan principle jika ada
        $principle = Principle::where('name', 'like', "%{$job->job_prinsiple}%")->first();

        // Pastikan direktori upload lampiran tersedia
        $lampiranPath = public_path('lampiran');
        if (!\Illuminate\Support\Facades\File::exists($lampiranPath)) {
            \Illuminate\Support\Facades\File::makeDirectory($lampiranPath, 0777, true, true);
        }

        // 1. Upload Pas Foto Profil
        $photoPath = $candidate?->photo_path;
        if ($request->hasFile('foto_profil')) {
            $fotoFile = $request->file('foto_profil');
            $photoName = 'foto_' . $nik . '_' . time() . '.' . $fotoFile->getClientOriginalExtension();
            $fotoFile->move($lampiranPath, $photoName);
            $photoPath = $photoName;
        } elseif ($request->filled('fotoprofil_base64')) {
            $base64 = $request->fotoprofil_base64;
            if (preg_match('/^data:image\/(\w+);base64,/', $base64, $type)) {
                $data = substr($base64, strpos($base64, ',') + 1);
                $data = base64_decode($data);
                $photoName = 'foto_' . $nik . '_' . time() . '.' . strtolower($type[1]);
                \Illuminate\Support\Facades\File::put($lampiranPath . '/' . $photoName, $data);
                $photoPath = $photoName;
            }
        }

        // 2. Upload Berkas CV
        $cvPath = $candidate?->cv_path;
        if ($request->hasFile('file_cv')) {
            $cvFile = $request->file('file_cv');
            $cvName = 'cv_' . $nik . '_' . time() . '.' . $cvFile->getClientOriginalExtension();
            $cvFile->move($lampiranPath, $cvName);
            $cvPath = $cvName;
        }

        // 3. AI Score matching - HANYA DILAKUKAN JIKA ADA BERKAS CV VALID
        $hasCv = !empty($cvPath) && $cvPath !== '-';

        $birthDateFormatted = Carbon::parse($request->input('tgl_lahir'))->format('dmY');

        $candidateData = [
            'nik' => $nik,
            'full_name' => $request->input('nama_lengkap'),
            'gender' => $request->input('gender'),
            'birth_date' => $request->input('tgl_lahir'),
            'height' => $request->input('tinggi', 165),
            'weight' => $request->input('berat', 55),
            'address_ktp' => $request->input('alamat_ktp'),
            'address_domicile' => $request->input('alamat_domisili', $request->input('alamat_ktp')),
            'province_domicile' => $request->input('propinsi_domisili'),
            'city_domicile' => $request->input('kota_domisili'),
            'secondary_city' => $request->input('kota_domisili'),
            'phone' => $request->input('no_wa'),
            'whatsapp' => $request->input('no_wa'),
            'education' => $request->input('pendidikan'),
            'applied_job' => $job->job_title,
            'area' => $job->job_area ?? 'JAKARTA',
            'principle_id' => $principle?->id ?? 1,
            'info_lowongan' => $request->input('info_lowongan'),
            'info' => $request->input('info_lowongan'),
            'source_type' => $request->input('info_lowongan', 'Job Portal'),
            'jenis' => 'Job Portal',
            'status' => 'Active',
            'status_kandidat' => 'Baru',
            'archive_reason' => null,
            'photo_path' => $photoPath,
            'cv_path' => $cvPath,
            'experience_summary' => $request->input('ringkasan_pengalaman'),
            'work_motivation' => $request->input('motivasi'),
            'strengths' => $request->input('kelebihan'),
            'ai_score' => null,
            'kategori_kandidat' => null,
            'ai_cv_analysis' => null,
            'is_profile_complete' => 1,
            'password' => Hash::make($birthDateFormatted),
            'useras' => $job->created_by ?? 'Publik',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // Jika kandidat sebelumnya dalam status arsip, reset approval, evaluasi, dan modul tes untuk seleksi baru
        if ($isArchived) {
            $candidateData['status_approval'] = null;
            $candidateData['idprinsiple'] = null;
            $candidateData['ttd_prinsiple'] = null;
            $candidateData['time_prinsiple'] = null;
            $candidateData['note_principle'] = null;
            $candidateData['tes_ke'] = max(1, intval($candidate->tes_ke ?? 1)) + 1;
            $candidateData['tes_kepribadian'] = null;
            $candidateData['tes_matematika'] = null;
            $candidateData['tes_komputer'] = null;
            $candidateData['buktikomputer'] = null;
            $candidateData['statement_agreed'] = 0;
            $candidateData['odoo_stage_name'] = null;
            $candidateData['odoo_applicant_id'] = null;
        }

        if ($candidate) {
            $candidate->update($candidateData);
        } else {
            $candidate = Candidate::create($candidateData);
        }

        // Jalankan analisa AI otomatis secara nyata jika ada berkas CV
        if ($hasCv) {
            try {
                app(\App\Services\AiAnalyzerService::class)->analyzeCandidate($candidate);
            } catch (\Throwable $e) {
                // Jika sedang jeda limit atau ada kendala koneksi, skor tetap null dan cron job latar belakang akan menganalisisnya
            }
        }


        // Simpan Ringkasan Pengalaman ke tabel work_experiences jika diisi
        if ($request->filled('ringkasan_pengalaman')) {
            $candidate->workExperiences()->create([
                'company_name' => 'Pengalaman Kerja Pelamar',
                'position' => $job->job_title,
                'responsibility_notes' => $request->input('ringkasan_pengalaman'),
                'performance_notes' => 'Diinput mandiri pada form pendaftaran',
            ]);
        }

        // Sinkronisasi ke legacy tb_kandidat jika tabel tersedia
        if (\Illuminate\Support\Facades\Schema::hasTable('tb_kandidat')) {
            $tbData = [
                'applicants_name' => $candidateData['full_name'],
                'gender' => $candidateData['gender'],
                'tanggal_lahir' => $candidateData['birth_date'],
                'height' => $candidateData['height'],
                'weight' => $candidateData['weight'],
                'alamat_ktp' => $candidateData['address_ktp'],
                'alamat_domisili' => $candidateData['address_domicile'],
                'province_domicile' => $candidateData['province_domicile'],
                'city_domicile' => $candidateData['city_domicile'],
                'secondary_city' => $candidateData['city_domicile'],
                'phone' => $candidateData['phone'],
                'mobile' => $candidateData['whatsapp'],
                'pendidikan_terakhir' => $candidateData['education'],
                'applied_job' => $candidateData['applied_job'],
                'area' => $candidateData['area'],
                'info' => $candidateData['info_lowongan'],
                'info_lowongan' => $candidateData['info_lowongan'],
                'experience_summary' => $candidateData['experience_summary'],
                'motivasi_kerja' => $candidateData['work_motivation'],
                'kelebihan' => $candidateData['strengths'],
                'status' => 'Active',
                'status_kandidat' => 'Baru',
                'useras' => $candidateData['useras'],
                'fotoprofil' => $photoPath,
                'filecv' => $cvPath,
                'waktukirim' => now(),
                'password' => $birthDateFormatted,
            ];

            if ($isArchived) {
                if (\Illuminate\Support\Facades\Schema::hasColumn('tb_kandidat', 'tes_ke')) {
                    $tbData['tes_ke'] = $candidateData['tes_ke'];
                }
                if (\Illuminate\Support\Facades\Schema::hasColumn('tb_kandidat', 'tes_kepribadian')) {
                    $tbData['tes_kepribadian'] = null;
                }
                if (\Illuminate\Support\Facades\Schema::hasColumn('tb_kandidat', 'tes_matematika')) {
                    $tbData['tes_matematika'] = null;
                }
            }

            \Illuminate\Support\Facades\DB::table('tb_kandidat')->updateOrInsert(
                ['no_ktp' => $nik],
                $tbData
            );
        }

        $successMessage = "Selamat, berkas pendaftaran posisi {$job->job_title} berhasil dikirim! Akun Anda aktif. Gunakan Username: {$nik} dan Password: {$birthDateFormatted} untuk masuk ke Tes Online.";

        return redirect()->route('job.apply', $job->id)
            ->with('success', $successMessage)
            ->with('registered_nik', $nik)
            ->with('registered_pass', $birthDateFormatted);
    }
}
