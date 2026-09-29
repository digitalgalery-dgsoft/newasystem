<?php

namespace App\Http\Controllers;

use App\Models\ApprovalWorkflow;
use App\Models\Employee;
use App\Models\TbArea;
use App\Models\WarningLetter;
use App\Models\WarningLetterApproval;
use App\Models\WarningLetterCounter;
use App\Models\WarningLetterViolation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Mpdf\Mpdf;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class WarningLetterController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth',
            'admin',
        ];
    }

    /**
     * Daftar Surat Peringatan & Ringkasan Metrik
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        $isHrdOrAdmin = ($user->isAdmin() || $user->isHrd());
        $userName = strtolower(trim($user->name ?? ''));

        $query = WarningLetter::with(['employee', 'creator', 'approver', 'headApprover', 'violations']);

        // Data Scoping Sesuai Peran:
        // 1. HRD / Admin: Menampilkan seluruh data nasional yang pernah diajukan
        // 2. Pimpinan: Menampilkan data yang diajukan timnya (bawahan / pimpinan_pembuat / area pimpinan) + data dirinya sendiri
        // 3. User Pembuat biasa: Menampilkan hanya data yang dibuatnya sendiri
        if (!$isHrdOrAdmin) {
            $subordinateEmails = Employee::whereRaw('LOWER(TRIM(pimpinan)) = ?', [$userName])
                ->orWhere('pimpinan', 'like', "%{$user->name}%")
                ->whereNotNull('email')
                ->pluck('email')
                ->toArray();

            $subordinateUserIds = !empty($subordinateEmails)
                ? User::whereIn('email', $subordinateEmails)->pluck('id')->toArray()
                : [];

            $effectiveAreas = $user->isHead() ? array_filter(array_map('trim', $user->getEffectiveAreas())) : [];

            $query->where(function ($q) use ($user, $userName, $subordinateUserIds, $effectiveAreas) {
                // a. Data yang dibuat sendiri oleh user pembuat
                $q->where('created_by', $user->id)
                  // b. Data di mana user tercatat sebagai pimpinan pembuat
                  ->orWhereRaw('LOWER(TRIM(pimpinan_pembuat)) = ?', [$userName]);

                // c. Data yang diajukan oleh anggota tim/bawahan pimpinan
                if (!empty($subordinateUserIds)) {
                    $q->orWhereIn('created_by', $subordinateUserIds);
                }

                // d. Data tim di area pimpinan (jika user adalah Head/Supervisor area)
                if (!empty($effectiveAreas)) {
                    $q->orWhereIn('area', $effectiveAreas);
                }
            });
        }

        // Filter Tab Status
        $tab = $request->query('tab', 'all');
        $now = Carbon::now();

        if ($tab === 'review_head') {
            $query->where('status', 'review_head');
        } elseif ($tab === 'review_hrd') {
            $query->where('status', 'review_hrd');
        } elseif ($tab === 'approved') {
            $query->where('status', 'approved')->where('tanggal_expired', '>=', $now->toDateString());
        } elseif ($tab === 'expired') {
            $query->where(function ($q) use ($now) {
                $q->where('status', 'expired')
                  ->orWhere(function ($sub) use ($now) {
                      $sub->where('status', 'approved')
                          ->where('tanggal_expired', '<', $now->toDateString());
                  });
            });
        } elseif ($tab === 'rejected') {
            $query->where('status', 'rejected');
        } elseif ($tab === 'cancelled') {
            $query->where('status', 'cancelled');
        } elseif ($tab === 'missing_signed') {
            $query->where('status', 'approved')->whereNull('file_ttd_karyawan');
        }

        // Filter Pencarian Teks
        if ($search = trim($request->query('q', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_surat', 'like', "%{$search}%")
                  ->orWhere('nama_karyawan', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('area', 'like', "%{$search}%")
                  ->orWhere('prinsiple', 'like', "%{$search}%");
            });
        }

        // Filter Tingkat SP
        if ($tingkat = $request->query('tingkat')) {
            $query->where('tingkat_sp', strtolower($tingkat));
        }

        // Filter Entitas
        if ($entity = $request->query('entity')) {
            $query->where('entity', strtoupper($entity));
        }

        // Hitung Statistik Kartu
        $statsBase = WarningLetter::query();
        if (!$isHrdOrAdmin) {
            $statsBase->where(function ($q) use ($user, $userName, $subordinateUserIds, $effectiveAreas) {
                $q->where('created_by', $user->id)
                  ->orWhereRaw('LOWER(TRIM(pimpinan_pembuat)) = ?', [$userName]);
                if (!empty($subordinateUserIds)) {
                    $q->orWhereIn('created_by', $subordinateUserIds);
                }
                if (!empty($effectiveAreas)) {
                    $q->orWhereIn('area', $effectiveAreas);
                }
            });
        }

        $stats = [
            'total' => (clone $statsBase)->count(),
            'review_head' => (clone $statsBase)->where('status', 'review_head')->count(),
            'review_hrd' => (clone $statsBase)->where('status', 'review_hrd')->count(),
            'approved' => (clone $statsBase)->where('status', 'approved')->where('tanggal_expired', '>=', $now->toDateString())->count(),
            'expired' => (clone $statsBase)->where(function ($q) use ($now) {
                $q->where('status', 'expired')
                  ->orWhere(function ($sub) use ($now) {
                      $sub->where('status', 'approved')->where('tanggal_expired', '<', $now->toDateString());
                  });
            })->count(),
            'rejected' => (clone $statsBase)->where('status', 'rejected')->count(),
            'cancelled' => (clone $statsBase)->where('status', 'cancelled')->count(),
            'missing_signed' => (clone $statsBase)->where('status', 'approved')->whereNull('file_ttd_karyawan')->count(),
        ];

        $letters = $query->latest('id')->paginate(15)->withQueryString();

        return view('warning_letters.index', compact('letters', 'stats', 'tab', 'isHrdOrAdmin'));
    }

    /**
     * Form Pengajuan Surat Peringatan (Sisi Requester / Atasan)
     * - Tanggal surat otomatis hari ini
     * - Tidak input pasal
     * - Mendeteksi pimpinan pembuat otomatis
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        $preselectedEmployee = null;
        if ($empId = $request->query('employee_id')) {
            $preselectedEmployee = Employee::find($empId);
        }

        // Deteksi pimpinan pembuat secara otomatis dari master karyawan (employees)
        $cleanName = trim(preg_replace('/\b(recruitment|recruiter|aro|jakarta|surabaya|bandung|amk|akp|atk|abo|admin|hrd|staff)\b/i', '', $user->name));
        $creatorEmp = Employee::whereRaw('LOWER(TRIM(email)) = ?', [strtolower(trim($user->email))])
            ->orWhere('nik', $user->nik ?? '')
            ->orWhereRaw('LOWER(TRIM(nama_karyawan)) = ?', [strtolower(trim($user->name))])
            ->orWhere('nama_karyawan', 'like', "%{$cleanName}%")
            ->first();
        $creatorPimpinan = $creatorEmp?->pimpinan ?? '';
        $creatorPimpinanJabatan = $creatorEmp?->jabatan_pimpinan ?? '';

        // Fallback jika belum terisi di profil: cek pimpinan berdasarkan area akun pembuat di master karyawan
        if (empty($creatorPimpinan)) {
            $area = $creatorEmp?->area ?? $user->area ?? '';
            if (!empty($area)) {
                $leader = Employee::where('area', $area)
                    ->where(function($q) {
                        $q->where('status', 'Aktiv')->orWhereNull('status');
                    })
                    ->where(function($q) {
                        $q->where('jabatan', 'like', '%AM%')
                          ->orWhere('jabatan', 'like', '%Manager%')
                          ->orWhere('jabatan', 'like', '%Supervisor%')
                          ->orWhere('jabatan', 'like', '%AS%')
                          ->orWhere('jabatan', 'like', '%Head%')
                          ->orWhere('jabatan', 'like', '%Lead%');
                    })
                    ->first();
                if ($leader) {
                    $creatorPimpinan = $leader->nama_karyawan;
                    $creatorPimpinanJabatan = $leader->jabatan;
                }
            }
        }

        // Ambil daftar pimpinan/atasan aktif dari Master Karyawan untuk dropdown jika belum terpetakan otomatis
        $masterLeaders = Employee::where('status', 'Aktiv')
            ->where(function($q) {
                $q->where('jabatan', 'like', '%AM%')
                  ->orWhere('jabatan', 'like', '%Manager%')
                  ->orWhere('jabatan', 'like', '%Supervisor%')
                  ->orWhere('jabatan', 'like', '%Head%')
                  ->orWhere('jabatan', 'like', '%Lead%')
                  ->orWhere('jabatan', 'like', '%AS%')
                  ->orWhere('jabatan', 'like', '%Direktur%');
            })
            ->orderBy('nama_karyawan')
            ->get(['id', 'nama_karyawan', 'jabatan', 'area']);

        return view('warning_letters.create', compact('preselectedEmployee', 'creatorPimpinan', 'creatorPimpinanJabatan', 'masterLeaders'));
    }

    /**
     * Simpan Pengajuan Surat Peringatan
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'tingkat_sp' => 'required|in:sp1,sp2,sp3',
            'tindakan_perbaikan' => 'nullable|string',
            'pimpinan_pembuat' => 'nullable|string|max:150',
            'violations' => 'required|array|min:1',
            'violations.*.tanggal_pelanggaran' => 'required|date',
            'violations.*.pelanggaran' => 'required|string',
            'violations.*.kronologi' => 'nullable|string',
            'file_pendukung' => 'nullable|array',
            'file_pendukung.*' => 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png,doc,docx',
        ], [
            'employee_id.required' => 'Karyawan yang diajukan wajib dipilih.',
            'employee_id.exists' => 'Data karyawan tidak ditemukan dalam sistem.',
            'tingkat_sp.required' => 'Usulan tingkat SP wajib dipilih.',
            'violations.required' => 'Minimal harus terdapat 1 butir pelanggaran.',
            'violations.*.tanggal_pelanggaran.required' => 'Tanggal pelanggaran wajib diisi.',
            'violations.*.pelanggaran.required' => 'Uraian pokok pelanggaran wajib diisi.',
            'file_pendukung.*.max' => 'Ukuran berkas lampiran maksimal 10MB per file.',
        ]);

        $employee = Employee::findOrFail($request->employee_id);

        // Ambil singkatan area resmi dari master area
        $singkatanArea = TbArea::getSingkatanByArea($employee->area);

        // Tanggal surat dibuat OTOMATIS hari ini
        $tglSurat = Carbon::now();
        $tglExpired = (clone $tglSurat)->addMonths(6);

        // Pimpinan pembuat OTOMATIS ditentukan dari Master Karyawan (employees)
        $cleanName = trim(preg_replace('/\b(recruitment|recruiter|aro|jakarta|surabaya|bandung|amk|akp|atk|abo|admin|hrd|staff)\b/i', '', $user->name));
        $creatorEmp = Employee::whereRaw('LOWER(TRIM(email)) = ?', [strtolower(trim($user->email))])
            ->orWhere('nik', $user->nik ?? '')
            ->orWhereRaw('LOWER(TRIM(nama_karyawan)) = ?', [strtolower(trim($user->name))])
            ->orWhere('nama_karyawan', 'like', "%{$cleanName}%")
            ->first();
        $pimpinanPembuat = $creatorEmp?->pimpinan ?? '';

        if (empty($pimpinanPembuat)) {
            $area = $creatorEmp?->area ?? $user->area ?? '';
            if (!empty($area)) {
                $leader = Employee::where('area', $area)
                    ->where(function($q) {
                        $q->where('status', 'Aktiv')->orWhereNull('status');
                    })
                    ->where(function($q) {
                        $q->where('jabatan', 'like', '%AM%')
                          ->orWhere('jabatan', 'like', '%Manager%')
                          ->orWhere('jabatan', 'like', '%Supervisor%')
                          ->orWhere('jabatan', 'like', '%AS%')
                          ->orWhere('jabatan', 'like', '%Head%')
                          ->orWhere('jabatan', 'like', '%Lead%');
                    })
                    ->first();
                if ($leader) {
                    $pimpinanPembuat = $leader->nama_karyawan;
                }
            }
        }

        // Fallback jika ada input eksplisit
        if (empty($pimpinanPembuat) && $request->filled('pimpinan_pembuat')) {
            $pimpinanPembuat = trim($request->pimpinan_pembuat);
        }

        // Tentukan status awal:
        // Jika pemohon memiliki pimpinan, masuk ke 'review_head'
        // Jika pemohon adalah Admin / HRD tanpa pimpinan khusus, langsung ke 'review_hrd'
        $initialStatus = 'review_head';
        $posisiApproval = !empty($pimpinanPembuat) ? "Pimpinan ({$pimpinanPembuat})" : "Pimpinan Pembuat SP";

        if (empty($pimpinanPembuat) && $user->isHrd()) {
            $initialStatus = 'review_hrd';
            $posisiApproval = 'Tim HRD Management';
        }

        // Upload lampiran pendukung
        $uploadedFiles = [];
        if ($request->hasFile('file_pendukung')) {
            foreach ($request->file('file_pendukung') as $file) {
                if ($file && $file->isValid()) {
                    $path = $file->store('warning_letters/attachments', 'public');
                    $uploadedFiles[] = [
                        'name' => $file->getClientOriginalName(),
                        'path' => $path,
                        'size' => $file->getSize(),
                        'uploaded_at' => Carbon::now()->toDateTimeString(),
                    ];
                }
            }
        }

        DB::beginTransaction();
        try {
            $letter = WarningLetter::create([
                'employee_id' => $employee->id,
                'nip' => $employee->nip,
                'nik' => $employee->nik,
                'nama_karyawan' => $employee->nama_karyawan,
                'jabatan' => $employee->jabatan,
                'area' => $employee->area,
                'singkatan_area' => $singkatanArea,
                'prinsiple' => $employee->prinsiple,
                'entity' => strtoupper($employee->entity ?: 'AMK'),
                'tingkat_sp' => $request->tingkat_sp,
                'tingkat_sp_diajukan' => $request->tingkat_sp,
                'tanggal_surat' => $tglSurat->toDateString(),
                'tanggal_expired' => $tglExpired->toDateString(),
                'pasal_pelanggaran' => null, // Dikosongkan, diisi oleh HRD saat approval
                'tindakan_perbaikan' => trim($request->tindakan_perbaikan ?? ''),
                'status' => $initialStatus,
                'posisi_approval' => $posisiApproval,
                'pimpinan_pembuat' => $pimpinanPembuat,
                'file_pendukung' => !empty($uploadedFiles) ? $uploadedFiles : null,
                'created_by' => $user->id,
            ]);

            // Simpan butir-butir pelanggaran
            foreach ($request->violations as $v) {
                if (!empty($v['pelanggaran'])) {
                    WarningLetterViolation::create([
                        'warning_letter_id' => $letter->id,
                        'tanggal_pelanggaran' => $v['tanggal_pelanggaran'],
                        'pelanggaran' => trim($v['pelanggaran']),
                        'kronologi' => trim($v['kronologi'] ?? ''),
                    ]);
                }
            }

            // Catat log approval awal
            WarningLetterApproval::create([
                'warning_letter_id' => $letter->id,
                'stage' => 'pengajuan',
                'user_id' => $user->id,
                'action' => 'approve',
                'perubahan_tingkat' => null,
                'catatan' => 'Pengajuan usulan Surat Peringatan dibuat oleh ' . $user->name . ' (Atasan: ' . ($pimpinanPembuat ?: '-') . ').',
            ]);

            DB::commit();

            $msg = ($initialStatus === 'review_head')
                ? 'Usulan Surat Peringatan berhasil diajukan dan sedang menunggu Review Tahap 1 dari Pimpinan (' . ($pimpinanPembuat ?: 'Atasan') . ').'
                : 'Usulan Surat Peringatan berhasil diajukan dan sedang menunggu Review Verifikasi dari Tim HRD.';

            return redirect()->route('warning-letters.show', $letter->id)
                ->with('success', $msg);
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menyimpan pengajuan Surat Peringatan: ' . $e->getMessage());
        }
    }

    /**
     * Tampilan Detail Lengkap Surat Peringatan & Timeline
     */
    public function show($id)
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        $letter = WarningLetter::with(['employee', 'creator', 'approver', 'headApprover', 'violations', 'approvals.user'])->findOrFail($id);

        $isHrdOrAdmin = ($user->isAdmin() || $user->isHrd());
        $canApproveHead = $letter->canUserApproveHead($user);
        $canApproveHrd = $letter->canUserApproveHrd($user);

        return view('warning_letters.show', compact('letter', 'isHrdOrAdmin', 'canApproveHead', 'canApproveHrd'));
    }

    /**
     * Persetujuan Tahap 1: Pimpinan dari User Pembuat
     */
    public function approveHead(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        $letter = WarningLetter::findOrFail($id);

        if ($letter->status !== 'review_head') {
            return back()->with('error', 'Status surat ini bukan menunggu review pimpinan.');
        }

        if (!$letter->canUserApproveHead($user)) {
            return back()->with('error', 'Akses ditolak! Anda tidak memiliki wewenang menyetujui tahap review pimpinan untuk pengajuan ini.');
        }

        $notes = trim($request->input('notes', ''));

        DB::beginTransaction();
        try {
            $letter->update([
                'head_approved_by' => $user->id,
                'head_approved_at' => Carbon::now(),
                'head_notes' => $notes,
                'status' => 'review_hrd',
                'posisi_approval' => 'Tim HRD Management',
            ]);

            WarningLetterApproval::create([
                'warning_letter_id' => $letter->id,
                'stage' => 'pimpinan',
                'user_id' => $user->id,
                'action' => 'approve',
                'catatan' => $notes ?: 'Persetujuan Tahap 1 oleh Pimpinan / Atasan Pembuat SP disetujui.',
            ]);

            DB::commit();

            return redirect()->route('warning-letters.show', $letter->id)
                ->with('success', 'Persetujuan Tahap 1 oleh Pimpinan berhasil disimpan. Berkas diteruskan ke HRD untuk pemeriksaan pasal dan penerbitan nomor surat.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses persetujuan pimpinan: ' . $e->getMessage());
        }
    }

    /**
     * Halaman Khusus Review & Approval HRD
     * Memungkinkan HRD menginput rujukan pasal, menyesuaikan jenis SP, dan mengedit redaksi pelanggaran/kronologi
     */
    public function reviewHrd($id)
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        $letter = WarningLetter::with(['employee', 'creator', 'violations'])->findOrFail($id);

        if (!$letter->canUserApproveHrd($user)) {
            return redirect()->route('warning-letters.show', $id)
                ->with('error', 'Akses ditolak! Anda tidak memiliki wewenang untuk mereview dan menyetujui Surat Peringatan ini.');
        }

        return view('warning_letters.review_hrd', compact('letter'));
    }

    /**
     * Eksekusi Approval HRD
     * - HRD menginput rujukan pasal PP/PKB
     * - HRD dapat mengubah tingkat SP, butir pelanggaran, dan kronologi
     * - Otomatis generate Nomor Surat Resmi secara atomic
     * - Otomatis simpan berkas PDF resmi ke storage
     */
    public function approveHrd(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        $letter = WarningLetter::with(['employee', 'creator', 'violations'])->findOrFail($id);

        if (!$letter->canUserApproveHrd($user)) {
            return redirect()->route('warning-letters.show', $id)
                ->with('error', 'Akses ditolak! Anda tidak memiliki wewenang untuk menyetujui Surat Peringatan.');
        }

        $validated = $request->validate([
            'tingkat_sp' => 'required|in:sp1,sp2,sp3',
            'pasal_pelanggaran' => 'required|string',
            'tindakan_perbaikan' => 'nullable|string',
            'catatan_approval' => 'nullable|string',
            'violations' => 'required|array|min:1',
            'violations.*.id' => 'nullable|integer',
            'violations.*.tanggal_pelanggaran' => 'required|date',
            'violations.*.pelanggaran' => 'required|string',
            'violations.*.kronologi' => 'nullable|string',
        ], [
            'pasal_pelanggaran.required' => 'Rujukan Pasal Peraturan Perusahaan / PKB wajib diinputkan oleh HRD sebagai dasar hukum.',
            'tingkat_sp.required' => 'Jenis/Tingkat SP wajib ditentukan.',
            'violations.required' => 'Minimal harus terdapat 1 butir pelanggaran.',
        ]);

        DB::beginTransaction();
        try {
            // Tanggal surat diambil dari waktu surat rilis / Approve HRD dan waktu 6 bulan dimulai dari tanggal tersebut
            $releaseDate = Carbon::now();
            $expiredDate = (clone $releaseDate)->addMonths(6);

            // Generate nomor surat resmi jika belum memiliki nomor (berdasarkan tanggal rilis)
            $nomorSurat = $letter->nomor_surat;
            $nomorUrut = $letter->nomor_urut;

            if (empty($nomorSurat)) {
                $gen = WarningLetterCounter::generateNextNomorSurat(
                    $letter->entity,
                    $letter->singkatan_area,
                    $request->tingkat_sp,
                    $releaseDate
                );
                $nomorUrut = $gen['nomor_urut'];
                $nomorSurat = $gen['nomor_surat'];
            }

            $perubahanTingkat = null;
            if ($request->tingkat_sp !== $letter->tingkat_sp_diajukan) {
                $perubahanTingkat = strtoupper($letter->tingkat_sp_diajukan) . ' -> ' . strtoupper($request->tingkat_sp);
            }

            // Update data surat resmi: tanggal surat ditetapkan saat rilis HRD & masa berlaku 6 bulan dihitung dari tanggal tersebut
            $letter->update([
                'nomor_surat' => $nomorSurat,
                'nomor_urut' => $nomorUrut,
                'tingkat_sp' => $request->tingkat_sp,
                'tanggal_surat' => $releaseDate->toDateString(),
                'tanggal_expired' => $expiredDate->toDateString(),
                'pasal_pelanggaran' => trim($request->pasal_pelanggaran),
                'tindakan_perbaikan' => trim($request->tindakan_perbaikan ?? ''),
                'status' => 'approved',
                'posisi_approval' => 'Selesai (Disetujui)',
                'approved_by' => $user->id,
                'approved_at' => $releaseDate,
            ]);

            // Sinkronisasi pembaruan butir-butir pelanggaran
            $existingViolationIds = $letter->violations->pluck('id')->toArray();
            $keptIds = [];

            foreach ($request->violations as $vData) {
                if (!empty($vData['id']) && in_array($vData['id'], $existingViolationIds)) {
                    $violation = WarningLetterViolation::find($vData['id']);
                    if ($violation) {
                        $violation->update([
                            'tanggal_pelanggaran' => $vData['tanggal_pelanggaran'],
                            'pelanggaran' => trim($vData['pelanggaran']),
                            'kronologi' => trim($vData['kronologi'] ?? ''),
                        ]);
                        $keptIds[] = $violation->id;
                    }
                } else {
                    $newV = WarningLetterViolation::create([
                        'warning_letter_id' => $letter->id,
                        'tanggal_pelanggaran' => $vData['tanggal_pelanggaran'],
                        'pelanggaran' => trim($vData['pelanggaran']),
                        'kronologi' => trim($vData['kronologi'] ?? ''),
                    ]);
                    $keptIds[] = $newV->id;
                }
            }

            // Hapus butir yang dihapus oleh HRD
            WarningLetterViolation::where('warning_letter_id', $letter->id)
                ->whereNotIn('id', $keptIds)
                ->delete();

            // Catat log approval
            WarningLetterApproval::create([
                'warning_letter_id' => $letter->id,
                'stage' => 'hrd',
                'user_id' => $user->id,
                'action' => 'approve',
                'perubahan_tingkat' => $perubahanTingkat,
                'catatan' => $request->catatan_approval ?: 'Surat Peringatan disetujui & nomor surat resmi diterbitkan.',
            ]);

            // Otomatis render berkas PDF ke storage
            $letter->refresh();
            $letter->load(['employee', 'creator', 'approver', 'headApprover', 'violations']);
            $pdfString = $this->generatePdfContent($letter);
            $safeFileName = 'sp_documents/SP_' . preg_replace('/[^a-zA-Z0-9_\-]/', '_', $letter->nomor_surat) . '.pdf';
            Storage::disk('public')->put($safeFileName, $pdfString);

            $letter->update(['file_pdf_surat' => $safeFileName]);

            DB::commit();

            return redirect()->route('warning-letters.show', $letter->id)
                ->with('success', "Surat Peringatan berhasil disetujui dan diterbitkan secara resmi dengan Nomor: {$nomorSurat}");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memproses persetujuan HRD: ' . $e->getMessage());
        }
    }

    /**
     * Penolakan Pengajuan oleh Pimpinan / HRD
     */
    public function reject(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        $letter = WarningLetter::findOrFail($id);

        $isHrdOrAdmin = ($user->isAdmin() || $user->isHrd());
        $canReject = false;
        $rejectStage = 'hrd';

        if ($letter->status === 'review_head' && $letter->canUserApproveHead($user)) {
            $canReject = true;
            $rejectStage = 'pimpinan';
        } elseif ($isHrdOrAdmin && in_array($letter->status, ['review_head', 'review_hrd'])) {
            $canReject = true;
            $rejectStage = 'hrd';
        } elseif ($letter->status === 'review_hrd' && $letter->canUserApproveHrd($user)) {
            $canReject = true;
            $rejectStage = 'hrd';
        }

        if (!$canReject) {
            return back()->with('error', 'Akses ditolak! Anda tidak memiliki wewenang menolak pengajuan ini.');
        }

        $request->validate([
            'alasan_penolakan' => 'required|string|min:5',
        ], [
            'alasan_penolakan.required' => 'Alasan penolakan pengajuan wajib diisi.',
            'alasan_penolakan.min' => 'Alasan penolakan minimal 5 karakter.',
        ]);

        $letter->update([
            'status' => 'rejected',
            'posisi_approval' => ($rejectStage === 'pimpinan') ? 'Ditolak Pimpinan' : 'Ditolak HRD',
            'alasan_penolakan' => trim($request->alasan_penolakan),
        ]);

        WarningLetterApproval::create([
            'warning_letter_id' => $letter->id,
            'stage' => $rejectStage,
            'user_id' => $user->id,
            'action' => 'reject',
            'catatan' => trim($request->alasan_penolakan),
        ]);

        return redirect()->route('warning-letters.show', $letter->id)
            ->with('info', 'Pengajuan Surat Peringatan telah ditolak.');
    }

    /**
     * Pembatalan Surat Peringatan oleh HRD / Administrator Kapanpun
     * (Bisa membatalkan SP yang masih dalam tahap review maupun yang sudah selesai/approved)
     */
    public function cancel(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        $letter = WarningLetter::findOrFail($id);
        $isHrdOrAdmin = ($user->isAdmin() || $user->isHrd());

        if (!$isHrdOrAdmin) {
            return back()->with('error', 'Akses ditolak! Hanya bagian HRD / Administrator yang berwenang membatalkan Surat Peringatan.');
        }

        if ($letter->status === 'cancelled') {
            return back()->with('info', 'Surat Peringatan ini sudah dalam status dibatalkan.');
        }

        $request->validate([
            'alasan_pembatalan' => 'required|string|min:5',
        ], [
            'alasan_pembatalan.required' => 'Alasan pembatalan Surat Peringatan wajib diisi.',
            'alasan_pembatalan.min' => 'Alasan pembatalan minimal 5 karakter.',
        ]);

        $reason = trim($request->alasan_pembatalan);

        DB::beginTransaction();
        try {
            $oldStatus = $letter->status;
            $letter->update([
                'status' => 'cancelled',
                'posisi_approval' => 'Dibatalkan HRD',
                'alasan_penolakan' => $reason,
            ]);

            WarningLetterApproval::create([
                'warning_letter_id' => $letter->id,
                'stage' => 'hrd',
                'user_id' => $user->id,
                'action' => 'cancel',
                'catatan' => "Surat Peringatan dibatalkan oleh HRD ({$user->name}) dari status [{$oldStatus}]. Alasan: {$reason}",
            ]);

            DB::commit();

            return redirect()->route('warning-letters.show', $letter->id)
                ->with('success', 'Surat Peringatan #' . ($letter->nomor_surat ?: $letter->id) . ' berhasil dibatalkan oleh HRD.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membatalkan Surat Peringatan: ' . $e->getMessage());
        }
    }

    /**
     * Unggah Berkas Dokumen Fisik yang Telah Ditandatangani Basah oleh 3 Pihak
     */
    public function uploadSignedDoc(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        $letter = WarningLetter::findOrFail($id);

        if ($letter->status !== 'approved') {
            return back()->with('error', 'Hanya Surat Peringatan yang telah disetujui yang dapat diunggah dokumen fisik bertandatangan.');
        }

        $request->validate([
            'file_ttd' => 'required|file|max:15360|mimes:pdf,jpg,jpeg,png',
        ], [
            'file_ttd.required' => 'Berkas scan tanda tangan wajib dipilih.',
            'file_ttd.max' => 'Ukuran berkas scan tanda tangan maksimal 15MB.',
        ]);

        if ($request->hasFile('file_ttd')) {
            $file = $request->file('file_ttd');
            $path = $file->store('warning_letters/signed', 'public');

            $letter->update([
                'file_ttd_karyawan' => $path,
                'tgl_upload_ttd' => Carbon::now(),
            ]);

            WarningLetterApproval::create([
                'warning_letter_id' => $letter->id,
                'stage' => 'upload_ttd',
                'user_id' => $user->id,
                'action' => 'upload_ttd',
                'catatan' => 'Berkas fisik bertandatangan lengkap (Karyawan, Atasan, HRD) berhasil diunggah oleh ' . $user->name . '.',
            ]);

            return back()->with('success', 'Dokumen fisik bertandatangan berhasil diunggah! Status kelengkapan dokumen kini Terarsip Lengkap.');
        }

        return back()->with('error', 'Gagal memproses berkas unggahan.');
    }

    /**
     * Unduh Berkas Dokumen Fisik Tertandatangani
     */
    public function downloadSignedDoc($id)
    {
        $letter = WarningLetter::findOrFail($id);

        if (!$letter->file_ttd_karyawan || !Storage::disk('public')->exists($letter->file_ttd_karyawan)) {
            abort(404, 'Berkas tanda tangan fisik belum tersedia atau tidak ditemukan di server.');
        }

        return Storage::disk('public')->download($letter->file_ttd_karyawan);
    }

    /**
     * Cetak PDF Dokumen Resmi Surat Peringatan
     * Menggunakan layout standar berkop resmi entitas ESA Groups & QR Code
     */
    public function printPdf($id)
    {
        $letter = WarningLetter::with(['employee', 'creator', 'approver', 'headApprover', 'violations'])->findOrFail($id);

        $pdfString = $this->generatePdfContent($letter);
        $filename = 'SP_' . preg_replace('/[^a-zA-Z0-9_\-]/', '_', $letter->nomor_surat ?: ('ID_' . $letter->id)) . '.pdf';

        return response($pdfString, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "inline; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Helper Render PDF String via mPDF
     */
    public function generatePdfContent(WarningLetter $letter): string
    {
        // Resolusi file kop surat resmi sesuai entitas dari D:\ASystem\KOP ENTITAS (public/kop/)
        $kopMap = [
            'AMK' => [public_path('kop/KopAMK.png'), public_path('kop/kopamknew.png')],
            'AKP' => [public_path('kop/KopAKP.png'), public_path('kop/kopakp.png')],
            'ATK' => [public_path('kop/KopATK.png'), public_path('kop/kopatk.png')],
            'ABO' => [public_path('kop/KopABO.png'), public_path('kop/kopabo.png')],
            'ATB' => [public_path('kop/KopATB.png'), public_path('kop/KopATB.jpg'), public_path('kop/kopatb.png')],
        ];
        $entityCode = strtoupper($letter->entity ?: 'AMK');
        $possiblePaths = $kopMap[$entityCode] ?? [public_path('kop/KopAMK.png'), public_path('kop/kopamknew.png')];
        $kopPath = null;
        foreach ($possiblePaths as $p) {
            if (file_exists($p)) {
                $kopPath = $p;
                break;
            }
        }

        $kopBase64 = null;
        if ($kopPath && file_exists($kopPath)) {
            $ext = strtolower(pathinfo($kopPath, PATHINFO_EXTENSION));
            $mime = ($ext === 'jpg' || $ext === 'jpeg') ? 'image/jpeg' : 'image/png';
            $kopBase64 = "data:{$mime};base64," . base64_encode(file_get_contents($kopPath));
        }

        // URL verifikasi QR code
        $verifyUrl = route('warning-letters.show', $letter->id);

        $html = view('warning_letters.pdf', compact('letter', 'kopBase64', 'verifyUrl', 'entityCode'))->render();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 18,
            'margin_right' => 18,
            'margin_top' => 12,
            'margin_bottom' => 22,
            'default_font' => 'helvetica',
        ]);

        $mpdf->SetTitle("Surat Peringatan - {$letter->nama_karyawan} ({$letter->nomor_surat})");
        $mpdf->SetAuthor('ESA Groups HRD Management');
        $mpdf->WriteHTML($html);

        $filename = 'SP_' . preg_replace('/[^a-zA-Z0-9_\-]/', '_', $letter->nomor_surat ?: ('ID_' . $letter->id)) . '.pdf';

        return $mpdf->Output($filename, 'S');
    }

    /**
     * AJAX Live Search Karyawan untuk Form Pengajuan
     */
    public function searchEmployees(Request $request)
    {
        $q = trim($request->query('q', ''));
        if (strlen($q) < 2) {
            return response()->json(['results' => []]);
        }

        $employees = Employee::where(function ($sub) use ($q) {
            $sub->where('nama_karyawan', 'like', "%{$q}%")
                ->orWhere('nik', 'like', "%{$q}%")
                ->orWhere('nip', 'like', "%{$q}%");
        })
        ->take(15)
        ->get(['id', 'nama_karyawan', 'nik', 'nip', 'jabatan', 'area', 'prinsiple', 'entity', 'status', 'foto']);

        $formatted = $employees->map(function ($emp) {
            return [
                'id' => $emp->id,
                'text' => "{$emp->nama_karyawan} (NIK: {$emp->nik}) - {$emp->jabatan}",
                'nama' => $emp->nama_karyawan,
                'nik' => $emp->nik,
                'nip' => $emp->nip ?: '-',
                'jabatan' => $emp->jabatan ?: '-',
                'area' => $emp->area ?: '-',
                'singkatan_area' => TbArea::getSingkatanByArea($emp->area),
                'prinsiple' => $emp->prinsiple ?: '-',
                'entity' => strtoupper($emp->entity ?: 'AMK'),
                'status' => $emp->status ?: 'Aktif',
            ];
        });

        return response()->json(['results' => $formatted]);
    }
}
