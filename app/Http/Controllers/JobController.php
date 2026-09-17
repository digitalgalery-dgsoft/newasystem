<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobSpec;
use App\Models\Principle;
use Carbon\Carbon;

class JobController extends Controller
{
    /**
     * Halaman Input Job Requirement (Replikasi inputjob.php)
     */
    public function index(Request $request)
    {
        $today = Carbon::today()->format('Y-m-d');

        // Edit mode
        $editData = null;
        if ($request->has('edit') && is_numeric($request->query('edit'))) {
            $editData = JobSpec::find($request->query('edit'));
        }

        // Active jobs (status = active and tgl_expired >= today or null)
        $search = $request->query('search');
        $activeQuery = JobSpec::where('status', 'active')
            ->where(function ($q) use ($today) {
                $q->whereNull('tgl_expired')->orWhere('tgl_expired', '>=', $today);
            });

        if ($search) {
            $activeQuery->where(function ($q) use ($search) {
                $q->where('job_title', 'like', "%{$search}%")
                  ->orWhere('job_prinsiple', 'like', "%{$search}%")
                  ->orWhere('job_area', 'like', "%{$search}%")
                  ->orWhere('job_skills', 'like', "%{$search}%");
            });
        }

        $activeJobs = $activeQuery->orderBy('created_at', 'desc')->get();

        // Expired jobs
        $expiredJobs = JobSpec::where('status', 'active')
            ->whereNotNull('tgl_expired')
            ->where('tgl_expired', '<', $today)
            ->orderBy('tgl_expired', 'desc')
            ->get();

        // Principles & Areas
        $principles = Principle::where('is_active', true)->orderBy('name')->get();
        $areas = [
            'JAKARTA', 'SURABAYA', 'BANDUNG', 'SEMARANG', 'MEDAN', 
            'MAKASSAR', 'DENPASAR', 'PALEMBANG', 'BALIKPAPAN', 'YOGYAKARTA',
            'MALANG', 'BOGOR', 'BEKASI', 'TANGERANG', 'DEPOK'
        ];

        // Preset templates
        $templates = [
            [
                'label' => 'Admin Operasional',
                'title' => 'Admin Operasional & Back Office',
                'area' => 'JAKARTA',
                'quals' => "- Pendidikan minimal D3 / S1 semua jurusan\n- Usia maksimal 28 tahun\n- Berpenampilan rapi dan komunikatif\n- Teliti, disiplin, dan bertanggung jawab",
                'skills' => 'Microsoft Excel, VLOOKUP, HLOOKUP, Pivot Table, Administrasi Kantor, Filing',
                'exp' => 'Minimal 1 tahun pengalaman di bidang administrasi / operasional.',
                'desc' => "- Menginput dan memvalidasi data harian operasional cabang\n- Melakukan rekonsiliasi arsip dokumen dan pengarsipan digital\n- Berkoordinasi dengan tim lapangan dan principal terkait absensi dan laporan bulanan",
            ],
            [
                'label' => 'SPG / Sales Promotion',
                'title' => 'Sales Promotion Girl (SPG) Modern Trade',
                'area' => 'JAKARTA',
                'quals' => "- Pendidikan minimal SMA / SMK Sederajat\n- Usia maksimal 26 tahun, tinggi minimal 158 cm\n- Berpenampilan menarik dan komunikatif\n- Bersedia bekerja target dan shifting",
                'skills' => 'Direct Selling, Customer Service, Komunikasi Aktif, Display Produk',
                'exp' => 'Pengalaman minimal 6 bulan sebagai SPG / Sales retail atau fresh graduate.',
                'desc' => "- Memperkenalkan dan menjual produk principal kepada pengunjung toko\n- Menata display produk agar selalu rapi dan menarik\n- Membuat laporan penjualan harian via WhatsApp / sistem",
            ],
            [
                'label' => 'Digital Marketing',
                'title' => 'Digital Marketing Specialist',
                'area' => 'SURABAYA',
                'quals' => "- S1 Pemasaran / Komunikasi / Multimedia\n- Usia maksimal 30 tahun\n- Memiliki portofolio campaign yang pernah dikelola",
                'skills' => 'Meta Ads, Google Ads, TikTok Ads, SEO, Copywriting, Google Analytics',
                'exp' => 'Min 1-2 tahun mengelola anggaran iklan berbayar (Paid Ads).',
                'desc' => "- Merancang strategi pemasaran digital dan iklan berbayar\n- Mengoptimalkan konversi lead dan pelamar kerja\n- Menganalisis metriks ROI dan efektivitas campaign",
            ],
            [
                'label' => 'Software Engineer',
                'title' => 'Fullstack Web Developer (Laravel & Vue/React)',
                'area' => 'JAKARTA',
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
            'search'
        ));
    }

    /**
     * Simpan / Update Job Specification
     */
    public function store(Request $request)
    {
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
            $job->update($validated);
            $msg = "Data job '{$job->job_title}' berhasil diperbarui.";
        } else {
            $validated['created_by'] = 'admin.pusat@arina.co.id';
            $validated['status'] = 'active';
            $job = JobSpec::create($validated);
            $msg = "Job '{$job->job_title}' berhasil disimpan!";
        }

        return redirect()->route('job.input')->with('success', $msg);
    }

    /**
     * Hapus Job
     */
    public function destroy($id)
    {
        $job = JobSpec::findOrFail($id);
        $title = $job->job_title;
        $job->delete();

        return redirect()->route('job.input')
            ->with('warning', "Data job '{$title}' berhasil dihapus.");
    }

    /**
     * Toggle Status Aktif / Non-aktif
     */
    public function toggleStatus($id)
    {
        $job = JobSpec::findOrFail($id);
        $job->status = $job->status === 'active' ? 'inactive' : 'active';
        $job->save();

        return redirect()->route('job.input')
            ->with('success', "Status job '{$job->job_title}' diubah menjadi: " . strtoupper($job->status));
    }
}