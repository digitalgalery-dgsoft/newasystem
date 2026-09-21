<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobSpec;
use App\Models\Principle;
use App\Models\User;
use App\Models\Employee;
use App\Models\AiSetting;
use App\Services\ActivityLogger;
use App\Services\IndonesiaRegionService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class JobController extends Controller
{
    private function getCurrentUser()
    {
        return auth()->user() ?? User::where('role', 'admin')->first() ?? User::first();
    }

    /**
     * Halaman Input Job Requirement (Replikasi inputjob.php)
     */
    public function index(Request $request)
    {
        $user = auth()->user() ?? $this->getCurrentUser();
        $isAdmin = $user && ($user->role === 'admin' || (method_exists($user, 'isAdmin') && $user->isAdmin()));
        $today = Carbon::today()->format('Y-m-d');

        // Resolusi Area User (Area mengikuti area user penempatan)
        $userArea = $user?->area;
        if (empty($userArea) && $user?->email) {
            $emp = Employee::where('email', $user->email)->first();
            if ($emp && !empty($emp->area)) {
                $userArea = $emp->area;
            }
        }
        if (empty($userArea)) {
            $userArea = 'Jember';
        }

        // Data Provinsi dan Kota Se-Indonesia
        $provincesWithCities = IndonesiaRegionService::getProvincesWithCities();
        $provinces = IndonesiaRegionService::getProvinces();

        $userEmail = strtolower(trim($user?->email ?? ''));
        $userName = strtolower(trim($user?->name ?? ''));
        $filterCreator = $request->query('filter_creator');

        // Edit mode with ownership authorization check
        $editData = null;
        if ($request->has('edit') && is_numeric($request->query('edit'))) {
            $foundJob = JobSpec::find($request->query('edit'));
            if ($foundJob) {
                if (!$isAdmin) {
                    $creator = strtolower(trim($foundJob->created_by ?? ''));
                    if ($creator !== $userEmail && $creator !== $userName) {
                        return redirect()->route('job.input')->with('error', 'Akses ditolak! Anda hanya dapat mengedit lowongan yang Anda buat sendiri.');
                    }
                }
                $editData = $foundJob;
            }
        }

        // Active jobs query
        $search = $request->query('search');
        $activeQuery = JobSpec::where('status', 'active')
            ->where(function ($q) use ($today) {
                $q->whereNull('tgl_expired')->orWhere('tgl_expired', '>=', $today);
            });

        // Expired jobs query
        $expiredQuery = JobSpec::where('status', 'active')
            ->whereNotNull('tgl_expired')
            ->where('tgl_expired', '<', $today);

        // ACCESS FILTER: Only show jobs created by the logged-in user, EXCEPT for Administrator
        if ($isAdmin) {
            if ($filterCreator === 'my') {
                $activeQuery->where(function ($q) use ($userEmail, $userName) {
                    $q->whereRaw('LOWER(TRIM(created_by)) = ?', [$userEmail]);
                    if (!empty($userName)) {
                        $q->orWhereRaw('LOWER(TRIM(created_by)) = ?', [$userName]);
                    }
                });
                $expiredQuery->where(function ($q) use ($userEmail, $userName) {
                    $q->whereRaw('LOWER(TRIM(created_by)) = ?', [$userEmail]);
                    if (!empty($userName)) {
                        $q->orWhereRaw('LOWER(TRIM(created_by)) = ?', [$userName]);
                    }
                });
            } elseif (!empty($filterCreator) && $filterCreator !== 'all') {
                $activeQuery->whereRaw('LOWER(TRIM(created_by)) = ?', [strtolower(trim($filterCreator))]);
                $expiredQuery->whereRaw('LOWER(TRIM(created_by)) = ?', [strtolower(trim($filterCreator))]);
            }
            // If 'all' or empty, administrator sees all jobs across all creators
        } else {
            // Non-administrator: Strictly filter by the user's email / username
            $activeQuery->where(function ($q) use ($userEmail, $userName) {
                $q->whereRaw('LOWER(TRIM(created_by)) = ?', [$userEmail]);
                if (!empty($userName)) {
                    $q->orWhereRaw('LOWER(TRIM(created_by)) = ?', [$userName]);
                }
            });
            $expiredQuery->where(function ($q) use ($userEmail, $userName) {
                $q->whereRaw('LOWER(TRIM(created_by)) = ?', [$userEmail]);
                if (!empty($userName)) {
                    $q->orWhereRaw('LOWER(TRIM(created_by)) = ?', [$userName]);
                }
            });
        }

        // Search filter
        if ($search) {
            $searchClosure = function ($q) use ($search) {
                $q->where('job_title', 'like', "%{$search}%")
                  ->orWhere('job_prinsiple', 'like', "%{$search}%")
                  ->orWhere('job_area', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('province', 'like', "%{$search}%")
                  ->orWhere('job_skills', 'like', "%{$search}%")
                  ->orWhere('created_by', 'like', "%{$search}%");
            };
            $activeQuery->where($searchClosure);
            $expiredQuery->where($searchClosure);
        }

        $activeJobs = $activeQuery->orderBy('created_at', 'desc')->get();
        $expiredJobs = $expiredQuery->orderBy('tgl_expired', 'desc')->get();

        // Principles
        $principles = Principle::where('is_active', true)->orderBy('name')->get();

        // Dynamic Areas from imported JobSpec and standard lists
        $distinctJobAreas = JobSpec::whereNotNull('job_area')
            ->where('job_area', '!=', '')
            ->distinct()
            ->pluck('job_area')
            ->all();

        $standardAreas = [
            'Aceh', 'Balikpapan', 'Bandung', 'Banjarmasin', 'Batam', 'Bekasi', 'Bogor', 
            'Bojonegoro', 'Cirebon', 'Denpasar', 'Depok', 'Gorontalo', 'Jakarta', 'Jambi', 
            'Jember', 'Kediri', 'Kudus', 'Kupang', 'Lampung', 'Madiun', 'Makassar', 'Malang', 
            'Manado', 'Mataram', 'Medan', 'Palembang', 'Palu', 'Pasuruan', 'Pekanbaru', 
            'Pematang Siantar', 'Purwokerto', 'Samarinda', 'Semarang', 'Solo', 'Surabaya', 
            'Tangerang', 'Tasikmalaya', 'Tegal', 'Yogyakarta'
        ];

        $areas = collect(array_merge($standardAreas, $distinctJobAreas))
            ->map(fn($a) => trim($a))
            ->filter()
            ->unique(fn($a) => strtolower($a))
            ->sort(SORT_NATURAL | SORT_FLAG_CASE)
            ->values();

        // Creator list for admin filter dropdown
        $allCreators = collect();
        if ($isAdmin) {
            $allCreators = JobSpec::select('created_by', DB::raw('COUNT(*) as total'))
                ->whereNotNull('created_by')
                ->where('created_by', '!=', '')
                ->groupBy('created_by')
                ->orderByDesc('total')
                ->get();
        }

        // Preset templates
        $templates = [
            [
                'label' => 'Admin Operasional',
                'title' => 'Admin Operasional & Back Office',
                'area' => 'Jakarta',
                'quals' => "- Pendidikan minimal D3 / S1 semua jurusan\n- Usia maksimal 28 tahun\n- Berpenampilan rapi dan komunikatif\n- Teliti, disiplin, dan bertanggung jawab",
                'skills' => 'Microsoft Excel, VLOOKUP, HLOOKUP, Pivot Table, Administrasi Kantor, Filing',
                'exp' => 'Minimal 1 tahun pengalaman di bidang administrasi / operasional.',
                'desc' => "- Menginput dan memvalidasi data harian operasional cabang\n- Melakukan rekonsiliasi arsip dokumen dan pengarsipan digital\n- Berkoordinasi dengan tim lapangan dan principal terkait absensi dan laporan bulanan",
            ],
            [
                'label' => 'SPG / Sales Promotion',
                'title' => 'Sales Promotion Girl (SPG) Modern Trade',
                'area' => 'Jakarta',
                'quals' => "- Pendidikan minimal SMA / SMK Sederajat\n- Usia maksimal 26 tahun, tinggi minimal 158 cm\n- Berpenampilan menarik dan komunikatif\n- Bersedia bekerja target dan shifting",
                'skills' => 'Direct Selling, Customer Service, Komunikasi Aktif, Display Produk',
                'exp' => 'Pengalaman minimal 6 bulan sebagai SPG / Sales retail atau fresh graduate.',
                'desc' => "- Memperkenalkan dan menjual produk principal kepada pengunjung toko\n- Menata display produk agar selalu rapi dan menarik\n- Membuat laporan penjualan harian via WhatsApp / sistem",
            ],
            [
                'label' => 'Digital Marketing',
                'title' => 'Digital Marketing Specialist',
                'area' => 'Surabaya',
                'quals' => "- S1 Pemasaran / Komunikasi / Multimedia\n- Usia maksimal 30 tahun\n- Memiliki portofolio campaign yang pernah dikelola",
                'skills' => 'Meta Ads, Google Ads, TikTok Ads, SEO, Copywriting, Google Analytics',
                'exp' => 'Min 1-2 tahun mengelola anggaran iklan berbayar (Paid Ads).',
                'desc' => "- Merancang strategi pemasaran digital dan iklan berbayar\n- Mengoptimalkan konversi lead dan pelamar kerja\n- Menganalisis metriks ROI dan efektivitas campaign",
            ],
            [
                'label' => 'Software Engineer',
                'title' => 'Fullstack Web Developer (Laravel & Vue/React)',
                'area' => 'Surabaya',
                'quals' => "- S1 Teknik Informatika / Sistem Informasi atau setara\n- IPK minimal 3.00\n- Memiliki kemampuan problem solving yang baik",
                'skills' => 'PHP, Laravel, JavaScript, Vue.js/React, Tailwind CSS, MySQL, Git, REST API',
                'exp' => 'Minimal 2 tahun pengalaman membangun aplikasi web berbasis Laravel.',
                'desc' => "- Membangun, menguji, dan merilis fitur-fitur portal rekrutmen dan absensi\n- Menjaga kebersihan kode (clean code) dan optimasi database\n- Berkolaborasi dengan tim produk dan UI/UX",
            ],
        ];

        return view('job.input', compact(
            'activeJobs', 
            'expiredJobs', 
            'editData', 
            'principles', 
            'areas', 
            'templates', 
            'search',
            'user',
            'isAdmin',
            'allCreators',
            'filterCreator',
            'userArea',
            'provinces',
            'provincesWithCities'
        ));
    }

    /**
     * Simpan / Update Job Specification
     */
    public function store(Request $request)
    {
        $user = auth()->user() ?? $this->getCurrentUser();
        $isAdmin = $user && ($user->role === 'admin' || (method_exists($user, 'isAdmin') && $user->isAdmin()));
        $userEmail = strtolower(trim($user?->email ?? ''));
        $userName = strtolower(trim($user?->name ?? ''));

        // Resolusi Area User (Area mengikuti area user)
        $userArea = $user?->area;
        if (empty($userArea) && $user?->email) {
            $emp = Employee::where('email', $user->email)->first();
            if ($emp && !empty($emp->area)) {
                $userArea = $emp->area;
            }
        }
        if (empty($userArea)) {
            $userArea = 'Jember';
        }

        $validated = $request->validate([
            'job_title' => 'required|string|max:200',
            'job_prinsiple' => 'nullable|string|max:200',
            'job_area' => 'nullable|string|max:200',
            'province' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'job_quals' => 'nullable|string',
            'job_skills' => 'nullable|string',
            'job_exp' => 'nullable|string',
            'job_desc' => 'nullable|string',
            'additional_info' => 'nullable|string',
            'tgl_expired' => 'nullable|date',
            'edit_id' => 'nullable|integer',
        ]);

        // Ketentuan: Area mengikuti sesuai area user (jika non-admin atau jika job_area kosong)
        if (!$isAdmin || empty($validated['job_area'])) {
            $validated['job_area'] = $userArea;
        }

        $editId = $request->input('edit_id');
        $today = Carbon::today()->format('Y-m-d');

        // Check duplicate active job title (meniru logika v3/inputjob.php baris 80-109)
        $dupQuery = JobSpec::whereRaw('LOWER(TRIM(job_title)) = ?', [strtolower(trim($validated['job_title']))])
            ->where('status', 'active')
            ->where(function ($q) use ($today) {
                $q->whereNull('tgl_expired')->orWhere('tgl_expired', '>=', $today);
            });

        if ($editId > 0) {
            $dupQuery->where('id', '!=', $editId);
        }

        if ($dupQuery->exists()) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Gagal menyimpan! Nama Job '{$validated['job_title']}' sudah ada dan sedang aktif.");
        }

        if ($editId > 0) {
            $job = JobSpec::findOrFail($editId);
            if (!$isAdmin) {
                $creator = strtolower(trim($job->created_by ?? ''));
                if ($creator !== $userEmail && $creator !== $userName) {
                    return redirect()->route('job.input')->with('error', 'Akses ditolak! Anda hanya dapat mengubah lowongan yang Anda buat sendiri.');
                }
            }
            $oldValues = $job->only(array_keys($validated));
            $job->update($validated);
            ActivityLogger::crud('UPDATE', 'Job Requirement', "Memperbarui lowongan kerja: {$job->job_title}", $job, $oldValues, $job->only(array_keys($validated)));
            $msg = "Data job '{$job->job_title}' berhasil diperbarui.";
        } else {
            $validated['created_by'] = $user?->email ?? $user?->name ?? 'admin.pusat@arina.co.id';
            $validated['status'] = 'active';
            $job = JobSpec::create($validated);
            ActivityLogger::crud('CREATE', 'Job Requirement', "Menambahkan lowongan kerja baru: {$job->job_title} ({$job->job_area})", $job, [], $job->toArray());
            $msg = "Job '{$job->job_title}' berhasil disimpan!";
        }

        return redirect()->route('job.input')->with('success', $msg);
    }

    /**
     * Hapus Job
     */
    public function destroy($id)
    {
        $user = auth()->user() ?? $this->getCurrentUser();
        $isAdmin = $user && ($user->role === 'admin' || (method_exists($user, 'isAdmin') && $user->isAdmin()));
        $userEmail = strtolower(trim($user?->email ?? ''));
        $userName = strtolower(trim($user?->name ?? ''));

        $job = JobSpec::findOrFail($id);
        if (!$isAdmin) {
            $creator = strtolower(trim($job->created_by ?? ''));
            if ($creator !== $userEmail && $creator !== $userName) {
                return redirect()->route('job.input')->with('error', 'Akses ditolak! Anda hanya dapat menghapus lowongan yang Anda buat sendiri.');
            }
        }

        $title = $job->job_title;
        $oldData = $job->toArray();
        $job->delete();

        ActivityLogger::crud('DELETE', 'Job Requirement', "Menghapus lowongan kerja: {$title}", $job, $oldData, []);

        return redirect()->route('job.input')
            ->with('warning', "Data job '{$title}' berhasil dihapus.");
    }

    /**
     * Toggle Status Aktif / Non-aktif
     */
    public function toggleStatus($id)
    {
        $user = auth()->user() ?? $this->getCurrentUser();
        $isAdmin = $user && ($user->role === 'admin' || (method_exists($user, 'isAdmin') && $user->isAdmin()));
        $userEmail = strtolower(trim($user?->email ?? ''));
        $userName = strtolower(trim($user?->name ?? ''));

        $job = JobSpec::findOrFail($id);
        if (!$isAdmin) {
            $creator = strtolower(trim($job->created_by ?? ''));
            if ($creator !== $userEmail && $creator !== $userName) {
                return redirect()->route('job.input')->with('error', 'Akses ditolak! Anda tidak berhak mengubah status lowongan ini.');
            }
        }

        $job->status = $job->status === 'active' ? 'inactive' : 'active';
        $job->save();

        ActivityLogger::log('UPDATE', 'Job Requirement', "Mengubah status lowongan kerja '{$job->job_title}' menjadi " . strtoupper($job->status), $job, [
            'status' => $job->status
        ]);

        return redirect()->route('job.input')
            ->with('success', "Status job '{$job->job_title}' diubah menjadi: " . strtoupper($job->status));
    }

    /**
     * Generate Spesifikasi Job via AI (Replikasi ajax_generate_job.php)
     */
    public function generateJobAi(Request $request)
    {
        $jobTitle = trim($request->input('job_title', ''));
        if (empty($jobTitle)) {
            return response()->json(['status' => 'error', 'message' => 'Posisi / Nama Jabatan harus diisi terlebih dahulu!'], 422);
        }

        $aiSetting = AiSetting::first();
        $keys = $aiSetting?->keys_list ?? [];
        $geminiModel = $aiSetting?->gemini_model ?: 'gemini-2.5-flash';
        $sumopodKey = $aiSetting?->sumopod_key ?: '';
        $sumopodModel = $aiSetting?->sumopod_model ?: 'gpt-4o-mini';

        $prompt = "Buatkan detail lowongan kerja Profesional dalam bahasa Indonesia untuk posisi '{$jobTitle}'.\n" .
                  "PENTING: Dilarang keras menyebutkan kebutuhan batasan usia, tinggi badan, berat badan, serta menyebutkan nama brand apa pun di dalam bagian Kualifikasi Umum ('quals') maupun Deskripsi/Skills.\n" .
                  "Jika ada standar rekomendasi mengenai usia, tinggi/berat badan untuk posisi tersebut, masukkan informasi itu HANYA ke dalam bagian 'additional_info'.\n" .
                  "Output WAJIB hanya berupa JSON murni tanpa tag markdown (jangan gunakan ```json).\n" .
                  "JSON harus memiliki key persis seperti ini:\n" .
                  "- 'quals' (Pendidikan & Kualifikasi Umum, format list HTML <ul><li>)\n" .
                  "- 'skills' (Spesialisasi Keterampilan, teks biasa dipisah koma. CONTOH: Skill A, Skill B. DILARANG menggunakan list HTML!)\n" .
                  "- 'exp' (Ringkasan Pengalaman, format list HTML <ul><li>)\n" .
                  "- 'desc' (Deskripsi Tugas Pekerjaan, format list HTML <ul><li>)\n" .
                  "- 'additional_info' (Informasi tambahan internal seperti rekomendasi batasan usia/tinggi/berat badan, format paragraf plain text biasa).\n" .
                  "Pastikan keseluruhan response AI Anda adalah SATU json utuh dan valid, tanpa tambahan teks apapun di luar blok kurawal JSON.";

        $jsonResult = null;

        // Try Gemini keys
        foreach ($keys as $key) {
            $key = trim($key);
            if (empty($key)) continue;
            $res = $this->callGeminiApi($key, $geminiModel, $prompt);
            if ($res) {
                $cleaned = trim(preg_replace('/^```(?:json)?\s*|\s*```$/i', '', trim($res)));
                $parsed = json_decode($cleaned, true);
                if (is_array($parsed) && isset($parsed['quals'])) {
                    $jsonResult = $parsed;
                    break;
                }
            }
        }

        // Fallback to SumoPod
        if (!$jsonResult && !empty($sumopodKey)) {
            $res = $this->callSumopodApi($sumopodKey, $sumopodModel, $prompt);
            if ($res) {
                $cleaned = trim(preg_replace('/^```(?:json)?\s*|\s*```$/i', '', trim($res)));
                $parsed = json_decode($cleaned, true);
                if (is_array($parsed) && isset($parsed['quals'])) {
                    $jsonResult = $parsed;
                }
            }
        }

        // Fallback default template if AI is offline
        if (!$jsonResult) {
            $jsonResult = [
                'quals' => "<ul><li>Pendidikan minimal SMA/SMK/D3/S1 sesuai bidang pekerjaan</li><li>Memiliki komunikasi yang baik, ramah, dan berpenampilan rapi</li><li>Disiplin, jujur, teliti, dan bertanggung jawab terhadap tugas</li><li>Mampu bekerja secara mandiri maupun berkolaborasi dalam tim</li></ul>",
                'skills' => 'Komunikasi Efektif, Manajemen Waktu, Problem Solving, Administrasi Dasar, Kerjasama Tim',
                'exp' => '<ul><li>Minimal 1 tahun pengalaman pada posisi serupa atau terbuka untuk lulusan baru bertalenta</li><li>Memiliki pemahaman dasar terkait alur kerja operasional</li></ul>',
                'desc' => "<ul><li>Menjalankan tugas utama serta tanggung jawab harian posisi {$jobTitle}</li><li>Berkoordinasi aktif dengan supervisor dan rekan kerja terkait capaian target</li><li>Menyusun laporan aktivitas harian/mingguan secara berkala</li></ul>",
                'additional_info' => 'Kandidat diprioritaskan yang siap segera bergabung (immediate joiner).',
            ];
        }

        return response()->json([
            'status' => 'success',
            'data' => $jsonResult,
        ]);
    }

    /**
     * Generate Image Prompt untuk Midjourney / DALL-E (Replikasi ajax_generate_image_prompt.php)
     */
    public function generateImagePrompt(Request $request)
    {
        $jobTitle = trim($request->input('job_title', ''));
        $jobSkills = trim($request->input('job_skills', ''));
        $jobQuals = trim($request->input('job_quals', ''));

        if (empty($jobTitle)) {
            return response()->json(['status' => 'error', 'message' => 'Posisi / Nama Jabatan harus diisi terlebih dahulu!'], 422);
        }

        $user = auth()->user() ?? $this->getCurrentUser();
        $userName = $user?->name ?: 'HRD Recruitment';
        $userPhone = $user?->phone ?: '0812-3456-7890';

        $prompt = "Buatkan prompt JSON murni untuk Image Generator (Midjourney/DALL-E) guna pembuatan Poster Loker (aspek rasio 3:4).\n" .
                  "DILARANG memasukkan rekomendasi usia, tinggi, berat badan atau brand ke dalam prompt visual.\n" .
                  "Pastikan output HANYA JSON murni (jangan gunakan markdown ```json).\n" .
                  "Di area kanan bawah poster HARUS dikosongkan/diwarnai putih untuk penempelan QR Code (wajib disebutkan di visual_prompt).\n\n" .
                  "Struktur JSON harus PERSIS seperti ini:\n" .
                  "{\n" .
                  "  \"job_title\": \"{$jobTitle}\",\n" .
                  "  \"skills_required\": \"{$jobSkills}\",\n" .
                  "  \"general_qualifications\": \"{$jobQuals}\",\n" .
                  "  \"contact_person\": {\n" .
                  "    \"name\": \"{$userName}\",\n" .
                  "    \"phone\": \"{$userPhone}\"\n" .
                  "  },\n" .
                  "  \"aspect_ratio\": \"3:4\",\n" .
                  "  \"visual_prompt\": \"Prompt rinci dalam bahasa Inggris untuk Midjourney/DALL-E untuk membuat poster estetik recruitment flyer for {$jobTitle}. Clean corporate and vibrant style, modern typography space, professional illustration or photo. MUST INCLUDE instruction to leave a blank white square at the bottom right corner for a QR code.\"\n" .
                  "}\n\n" .
                  "Pastikan response Anda HANYA berupa JSON valid.";

        $aiSetting = AiSetting::first();
        $keys = $aiSetting?->keys_list ?? [];
        $geminiModel = $aiSetting?->gemini_model ?: 'gemini-2.5-flash';
        $sumopodKey = $aiSetting?->sumopod_key ?: '';
        $sumopodModel = $aiSetting?->sumopod_model ?: 'gpt-4o-mini';

        $jsonResult = null;
        foreach ($keys as $key) {
            $key = trim($key);
            if (empty($key)) continue;
            $res = $this->callGeminiApi($key, $geminiModel, $prompt);
            if ($res) {
                $cleaned = trim(preg_replace('/^```(?:json)?\s*|\s*```$/i', '', trim($res)));
                $parsed = json_decode($cleaned, true);
                if (is_array($parsed) && isset($parsed['visual_prompt'])) {
                    $jsonResult = $parsed;
                    break;
                }
            }
        }

        if (!$jsonResult && !empty($sumopodKey)) {
            $res = $this->callSumopodApi($sumopodKey, $sumopodModel, $prompt);
            if ($res) {
                $cleaned = trim(preg_replace('/^```(?:json)?\s*|\s*```$/i', '', trim($res)));
                $parsed = json_decode($cleaned, true);
                if (is_array($parsed) && isset($parsed['visual_prompt'])) {
                    $jsonResult = $parsed;
                }
            }
        }

        if (!$jsonResult) {
            $cleanQuals = strip_tags($jobQuals);
            $jsonResult = [
                'job_title' => $jobTitle,
                'skills_required' => $jobSkills,
                'general_qualifications' => $cleanQuals,
                'contact_person' => [
                    'name' => $userName,
                    'phone' => $userPhone,
                ],
                'aspect_ratio' => '3:4',
                'visual_prompt' => "A professional recruitment flyer poster for '{$jobTitle}', modern corporate graphic design, bold typography headline 'WE ARE HIRING: {$jobTitle}', clean layout with stylish badge highlights, high contrast aesthetic, ultra high definition, leave a clean white blank square box at the bottom right corner for QR code placement --ar 3:4",
            ];
        }

        return response()->json([
            'status' => 'success',
            'data' => $jsonResult,
        ]);
    }

    private function callGeminiApi(string $apiKey, string $model, string $prompt): ?string
    {
        try {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . trim($apiKey);
            $response = Http::timeout(25)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'responseMimeType' => 'application/json'
                    ]
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
            }
        } catch (\Throwable $e) {
            Log::warning('JobController Gemini API Error: ' . $e->getMessage());
        }
        return null;
    }

    private function callSumopodApi(string $apiKey, string $model, string $prompt): ?string
    {
        try {
            $url = "https://ai.sumopod.com/v1/chat/completions";
            $response = Http::timeout(25)
                ->withToken(trim($apiKey))
                ->post($url, [
                    'model' => $model,
                    'messages' => [
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'response_format' => ['type' => 'json_object']
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['choices'][0]['message']['content'] ?? null;
            }
        } catch (\Throwable $e) {
            Log::warning('JobController SumoPod API Error: ' . $e->getMessage());
        }
        return null;
    }
}