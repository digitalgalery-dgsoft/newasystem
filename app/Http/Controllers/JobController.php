<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobSpec;
use App\Models\Principle;
use App\Models\User;
use App\Services\ActivityLogger;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
            'filterCreator'
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
}