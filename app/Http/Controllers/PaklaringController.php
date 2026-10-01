<?php

namespace App\Http\Controllers;

use App\Models\ApprovalWorkflow;
use App\Models\ApprovalWorkflowStep;
use App\Models\Candidate;
use App\Models\Employee;
use App\Models\Paklaring;
use App\Models\PaklaringApproval;
use App\Models\Principle;
use App\Models\TbArea;
use App\Models\User;
use App\Services\PaklaringApprovalService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mpdf\Mpdf;

class PaklaringController extends Controller
{
    /**
     * Dashboard & Daftar Pengajuan Paklaring (Internal)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user && ($user->isAdmin() || $user->role === 'admin');

        // Tentukan tab aktif (On Focus):
        // Jika query 'tab' kosong, atau jika akun non-admin mencoba request 'all', arahkan ke step approval user
        $requestedTab = $request->query('tab');
        if (empty($requestedTab) || (!$isAdmin && $requestedTab === 'all')) {
            $tab = PaklaringApprovalService::getDefaultTabForUser($user);
        } else {
            $tab = $requestedTab;
        }

        $search = trim($request->query('search', ''));
        $areaFilter = $request->query('area', '');
        $prinsipleFilter = $request->query('prinsiple', '');

        // Query dasar
        $baseQuery = Paklaring::query();

        // Scope jika user adalah rekruter / staf area (kecuali admin/HRD pusat/DB/BPJS)
        if ($user && !$user->isAdmin() && !$user->isHrd() && !str_contains(strtolower($user->job_title ?? ''), 'pusat') && !str_contains(strtolower($user->role ?? ''), 'db') && !str_contains(strtolower($user->role ?? ''), 'bpjs')) {
            $effectiveAreas = $user->getEffectiveAreas();
            if (!empty($effectiveAreas) && !in_array('ALL', array_map('strtoupper', $effectiveAreas))) {
                $baseQuery->whereIn('area', $effectiveAreas);
            }
        }

        // Hitung badge counter untuk masing-masing tab
        $counts = [
            'all' => (clone $baseQuery)->count(),
            'area' => (clone $baseQuery)->waitingArea()->count(),
            'hrd' => (clone $baseQuery)->waitingHrd()->count(),
            'db' => (clone $baseQuery)->waitingDb()->count(),
            'bpjs' => (clone $baseQuery)->waitingBpjs()->count(),
            'selesai' => (clone $baseQuery)->completed()->count(),
            'tolak_hold' => (clone $baseQuery)->rejectedOrHold()->count(),
        ];

        // Terapkan filter tab
        $query = clone $baseQuery;
        match ($tab) {
            'area' => $query->waitingArea(),
            'hrd' => $query->waitingHrd(),
            'db' => $query->waitingDb(),
            'bpjs' => $query->waitingBpjs(),
            'selesai' => $query->completed(),
            'tolak_hold' => $query->rejectedOrHold(),
            default => null,
        };

        // Filter Area
        if (!empty($areaFilter)) {
            $query->where('area', $areaFilter);
        }

        // Filter Prinsiple
        if (!empty($prinsipleFilter)) {
            $query->where('prinsiple', $prinsipleFilter);
        }

        // Filter Pencarian (Nama, NIK, Kode Validasi, Jabatan)
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('kode_validasi', 'like', "%{$search}%")
                  ->orWhere('jabatan', 'like', "%{$search}%")
                  ->orWhere('nomor_ref', 'like', "%{$search}%");
            });
        }

        $paklarings = $query->latest('id')->paginate(15)->withQueryString();

        // Master pilihan untuk dropdown filter
        $areas = TbArea::orderBy('area', 'asc')->pluck('area')->filter()->unique()->values();
        if ($areas->isEmpty()) {
            $areas = collect(TbArea::getOfficialAreas())->pluck('area')->filter()->unique()->values();
        }

        $principles = Principle::orderBy('name', 'asc')->pluck('name')->filter()->unique()->values();

        // Alur workflow paklaring untuk referensi
        $workflow = PaklaringApprovalService::getWorkflow();

        return view('paklaring.index', compact(
            'paklarings',
            'counts',
            'tab',
            'search',
            'areaFilter',
            'prinsipleFilter',
            'areas',
            'principles',
            'workflow'
        ));
    }

    /**
     * Form Pengajuan Paklaring (Internal User)
     */
    public function create()
    {
        $areas = TbArea::orderBy('area', 'asc')->pluck('area')->filter()->unique()->values();
        if ($areas->isEmpty()) {
            $areas = collect(TbArea::getOfficialAreas())->pluck('area')->filter()->unique()->values();
        }

        $principles = Principle::where('is_active', true)->orderBy('name', 'asc')->get();

        return view('paklaring.create', compact('areas', 'principles'));
    }

    /**
     * Form Pengajuan Paklaring Publik (Tanpa Login)
     */
    public function createPublic()
    {
        $areas = TbArea::orderBy('area', 'asc')->pluck('area')->filter()->unique()->values();
        if ($areas->isEmpty()) {
            $areas = collect(TbArea::getOfficialAreas())->pluck('area')->filter()->unique()->values();
        }

        $principles = Principle::where('is_active', true)->orderBy('name', 'asc')->get();

        return view('paklaring.public_form', compact('areas', 'principles'));
    }

    /**
     * API Lookup Data Karyawan / Kandidat Berdasarkan NIK (Autofill Form)
     */
    public function lookupNik(Request $request)
    {
        $nik = trim($request->query('nik', ''));
        if (empty($nik) || strlen($nik) < 8) {
            return response()->json(['found' => false]);
        }

        // 1. Cek di Master Employee
        $emp = Employee::where('nik', $nik)->first();
        if ($emp) {
            return response()->json([
                'found' => true,
                'source' => 'employee',
                'nama' => $emp->nama_karyawan,
                'tempat_lahir' => $emp->tempat_lahir,
                'tgl_lahir' => $emp->tgl_lahir ? Carbon::parse($emp->tgl_lahir)->format('Y-m-d') : null,
                'jenis_kelamin' => $emp->jenis_kelamin,
                'alamat' => $emp->alamat,
                'no_hp' => $emp->no_hp,
                'email' => $emp->email,
                'jabatan' => $emp->jabatan,
                'prinsiple' => $emp->prinsiple,
                'area' => $emp->area,
                'kantor' => $emp->entity,
                'tgl_masuk' => $emp->tgl_masuk ? Carbon::parse($emp->tgl_masuk)->format('Y-m-d') : null,
                'tgl_keluar' => $emp->tgl_keluar ? Carbon::parse($emp->tgl_keluar)->format('Y-m-d') : null,
            ]);
        }

        // 2. Cek di Candidate
        $cand = Candidate::where('nik', $nik)->latest('id')->first();
        if ($cand) {
            $prinName = is_object($cand->principle) ? ($cand->principle->name ?? '') : (string)$cand->principle;
            return response()->json([
                'found' => true,
                'source' => 'candidate',
                'nama' => $cand->nama_lengkap,
                'tempat_lahir' => $cand->tempat_lahir,
                'tgl_lahir' => $cand->tgl_lahir ? Carbon::parse($cand->tgl_lahir)->format('Y-m-d') : null,
                'jenis_kelamin' => $cand->jenis_kelamin,
                'alamat' => $cand->alamat,
                'no_hp' => $cand->no_hp,
                'email' => $cand->email,
                'jabatan' => $cand->jabatan,
                'prinsiple' => $prinName,
                'area' => $cand->area,
                'kantor' => $cand->kantor,
            ]);
        }

        return response()->json(['found' => false]);
    }

    /**
     * Simpan Pengajuan Paklaring Baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required|string|size:16',
            'nama_lengkap' => 'required|string|max:150',
            'tempat_lahir' => 'nullable|string|max:100',
            'tgl_lahir' => 'nullable|date',
            'jenis_kelamin' => 'required|string',
            'alamat' => 'required|string',
            'prinsiple' => 'required|string',
            'area' => 'required|string',
            'jabatan' => 'required|string|max:150',
            'no_hp' => 'required|string|max:30',
            'email' => 'required|email|max:150',
            'alasan' => 'nullable|string|max:255',
            'tgl_masuk' => 'nullable|date',
            'tgl_keluar' => 'nullable|date',
            'foto_ktp' => 'required|file|mimes:jpeg,jpg,png,webp,pdf|max:2048',
            'form_request' => 'required|file|mimes:jpeg,jpg,png,webp,pdf|max:2048',
            'exit_cl' => 'required|file|mimes:jpeg,jpg,png,webp,pdf|max:2048',
            'pengunduran_diri' => 'required|file|mimes:jpeg,jpg,png,webp,pdf|max:2048',
            'kartu_bpjs' => 'required|file|mimes:jpeg,jpg,png,webp,pdf|max:2048',
            'serah_terima' => 'required|file|mimes:jpeg,jpg,png,webp,pdf|max:2048',
            'surat_cl' => 'nullable|file|mimes:jpeg,jpg,png,webp,pdf|max:2048',
            'status_deposit' => 'nullable|string',
            // Field Rekening Pemohon (Legacy & New Form)
            'nomorrekening' => 'nullable|string|max:50',
            'nomor_rekening' => 'nullable|string|max:50',
            'bank' => 'nullable|string|max:50',
            'namarekening' => 'nullable|string|max:150',
            'nama_rekening' => 'nullable|string|max:150',
            'jmldeposit' => 'nullable|string|max:50',
            'jml_deposit' => 'nullable',
            'bukutabungan' => 'nullable|file|mimes:jpeg,jpg,png,webp,pdf|max:2048',
            'buku_tabungan' => 'nullable|file|mimes:jpeg,jpg,png,webp,pdf|max:2048',
            // Field Bank Deposit Awal (Sistem Lama)
            'kebank' => 'nullable|string|max:50',
            'kerekening' => 'nullable|string|max:50',
            'tanggaldeposit' => 'nullable|date',
            'buktideposit' => 'nullable|file|mimes:jpeg,jpg,png,webp,pdf|max:2048',
            'bukti_deposit' => 'nullable|file|mimes:jpeg,jpg,png,webp,pdf|max:2048',
        ], [
            'foto_ktp.max' => 'Ukuran berkas Foto KTP Asli tidak boleh melebihi 2MB.',
            'form_request.max' => 'Ukuran berkas Form Request Veklaring tidak boleh melebihi 2MB.',
            'exit_cl.max' => 'Ukuran berkas Form Exit Clearance tidak boleh melebihi 2MB.',
            'pengunduran_diri.max' => 'Ukuran berkas Surat Pengunduran Diri tidak boleh melebihi 2MB.',
            'kartu_bpjs.max' => 'Ukuran berkas Kartu BPJS Ketenagakerjaan tidak boleh melebihi 2MB.',
            'serah_terima.max' => 'Ukuran berkas Berita Acara Serah Terima Aset tidak boleh melebihi 2MB.',
            'surat_cl.max' => 'Ukuran berkas Surat Pernyataan / Surat CL tidak boleh melebihi 2MB.',
            '*.max' => 'Ukuran berkas yang diunggah tidak boleh melebihi 2MB.',
            '*.mimes' => 'Format berkas lampiran harus berupa JPG, JPEG, PNG, WEBP, atau PDF.',
        ]);

        $nik = trim($request->nik);
        $prinsiple = trim($request->prinsiple);

        // 1. Cek Duplikasi Pengajuan yang Masih Berproses
        $existing = Paklaring::where('nik', $nik)
            ->where('prinsiple', $prinsiple)
            ->where('status', 'Proses')
            ->first();

        if ($existing) {
            return back()
                ->withInput()
                ->with('error', "Pengajuan untuk NIK {$nik} dan Prinsiple {$prinsiple} saat ini masih dalam proses (ID Permohonan: {$existing->kode_validasi}).");
        }

        // 2. Generate Unique Validation Codes
        $kodeValidasi = PaklaringApprovalService::generateKodeValidasi();
        $kodeValidasiRef = PaklaringApprovalService::generateKodeValidasi();

        // 3. Upload File Lampiran dengan Optimasi / Auto-Resize
        $uploadedFiles = [];
        $fileKeys = [
            'foto_ktp', 'form_request', 'exit_cl', 'pengunduran_diri', 
            'kartu_bpjs', 'serah_terima', 'surat_cl'
        ];

        $storageFolder = "paklaring/{$kodeValidasi}";
        foreach ($fileKeys as $fileKey) {
            if ($request->hasFile($fileKey)) {
                $file = $request->file($fileKey);
                $ext = $file->getClientOriginalExtension();
                $filename = strtoupper($fileKey) . "_{$kodeValidasi}." . $ext;
                $uploadedFiles[$fileKey] = $this->storeOptimizedFile($file, $storageFolder, $filename);
            } else {
                $uploadedFiles[$fileKey] = null;
            }
        }

        // Upload Buku Tabungan (dukung nama input bukutabungan atau buku_tabungan)
        if ($request->hasFile('bukutabungan')) {
            $file = $request->file('bukutabungan');
            $filename = "BUKU_TABUNGAN_{$kodeValidasi}." . $file->getClientOriginalExtension();
            $file->storeAs($storageFolder, $filename, 'public');
            $uploadedFiles['buku_tabungan'] = "{$storageFolder}/{$filename}";
        } elseif ($request->hasFile('buku_tabungan')) {
            $file = $request->file('buku_tabungan');
            $filename = "BUKU_TABUNGAN_{$kodeValidasi}." . $file->getClientOriginalExtension();
            $file->storeAs($storageFolder, $filename, 'public');
            $uploadedFiles['buku_tabungan'] = "{$storageFolder}/{$filename}";
        } else {
            $uploadedFiles['buku_tabungan'] = null;
        }

        // Upload Bukti Deposit (dukung nama input buktideposit atau bukti_deposit)
        if ($request->hasFile('buktideposit')) {
            $file = $request->file('buktideposit');
            $filename = "BUKTI_DEPOSIT_{$kodeValidasi}." . $file->getClientOriginalExtension();
            $file->storeAs($storageFolder, $filename, 'public');
            $uploadedFiles['bukti_deposit'] = "{$storageFolder}/{$filename}";
        } elseif ($request->hasFile('bukti_deposit')) {
            $file = $request->file('bukti_deposit');
            $filename = "BUKTI_DEPOSIT_{$kodeValidasi}." . $file->getClientOriginalExtension();
            $file->storeAs($storageFolder, $filename, 'public');
            $uploadedFiles['bukti_deposit'] = "{$storageFolder}/{$filename}";
        } else {
            $uploadedFiles['bukti_deposit'] = null;
        }

        // 4. Resolve Induk Kantor (Entitas Inhouse) & Region
        $kantor = null;
        $prinObj = Principle::where('name', $prinsiple)->orWhere('code', $prinsiple)->first();
        if ($prinObj) {
            $kantor = $prinObj->entity ?: $prinObj->parent_company;
        }
        if (empty($kantor)) {
            $kantor = Employee::getEntityCodeFromPrinciple($prinsiple) ?: 'AMK';
        }

        $region = null;
        $areaObj = TbArea::where('area', $request->area)->first();
        if ($areaObj) {
            $region = $areaObj->region;
        }

        // Format No HP (menjadikan format 62...)
        $noHp = trim($request->no_hp);
        if (str_starts_with($noHp, '0')) {
            $noHp = '62' . substr($noHp, 1);
        }

        $now = Carbon::now('Asia/Jakarta');

        // Resolve data pencairan deposit
        $nomorRekening = trim($request->input('nomorrekening') ?? $request->input('nomor_rekening') ?? '');
        $namaRekening = trim($request->input('namarekening') ?? $request->input('nama_rekening') ?? '');
        $rawJml = $request->input('jmldeposit') ?? $request->input('jml_deposit') ?? null;
        $cleanJml = null;
        if (!empty($rawJml)) {
            $cleanStr = preg_replace('/[^0-9.]/', '', str_replace(',', '.', (string)$rawJml));
            $cleanJml = is_numeric($cleanStr) ? (float)$cleanStr : null;
        }

        $kebank = trim($request->input('kebank') ?? '');
        $kerekening = trim($request->input('kerekening') ?? '');
        $tglDeposit = $request->input('tanggaldeposit') ?: null;

        $hasDepositInput = ($request->filled('status_deposit') || !empty($nomorRekening) || !empty($kebank) || !empty($cleanJml));
        $isDeposit = $hasDepositInput ? 'Ya' : null;

        // 5. Simpan ke Database
        $paklaring = Paklaring::create([
            'kode_validasi' => $kodeValidasi,
            'kode_validasiref' => $kodeValidasiRef,
            'nik' => $nik,
            'nama_lengkap' => strtoupper(trim($request->nama_lengkap)),
            'tempat_lahir' => ucwords(trim($request->tempat_lahir ?? '')),
            'tgl_lahir' => $request->tgl_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'alamat' => trim($request->alamat),
            'kantor' => $kantor,
            'prinsiple' => $prinsiple,
            'area' => trim($request->area),
            'region' => $region,
            'jabatan' => strtoupper(trim($request->jabatan)),
            'no_hp' => $noHp,
            'email' => strtolower(trim($request->email)),
            'alasan' => trim($request->alasan ?? 'Mengundurkan Diri'),
            'tgl_masuk' => $request->tgl_masuk,
            'tgl_keluar' => $request->tgl_keluar,
            'status_deposit' => $isDeposit,
            'nomor_rekening' => $nomorRekening ?: null,
            'bank' => strtoupper(trim($request->bank ?? '')),
            'nama_rekening' => strtoupper($namaRekening),
            'jml_deposit' => $cleanJml,
            'kebank' => $kebank ?: null,
            'kerekening' => $kerekening ?: null,
            'tanggaldeposit' => $tglDeposit,
            'foto_ktp' => $uploadedFiles['foto_ktp'],
            'form_request' => $uploadedFiles['form_request'],
            'exit_cl' => $uploadedFiles['exit_cl'],
            'pengunduran_diri' => $uploadedFiles['pengunduran_diri'],
            'kartu_bpjs' => $uploadedFiles['kartu_bpjs'],
            'serah_terima' => $uploadedFiles['serah_terima'],
            'surat_cl' => $uploadedFiles['surat_cl'],
            'buku_tabungan' => $uploadedFiles['buku_tabungan'],
            'bukti_deposit' => $uploadedFiles['bukti_deposit'],
            'status_bagian' => 'Area',
            'status' => 'Proses',
            'current_step_order' => 1,
            'created_by' => Auth::id(),
            'waktu_input' => $now,
            'pengguna' => Auth::check() ? Auth::user()->name : "Karyawan an. {$request->nama_lengkap}",
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Simpan log approval pembuatan awal
        PaklaringApproval::create([
            'paklaring_id' => $paklaring->id,
            'step_order' => 1,
            'step_name' => 'Pengajuan Masuk',
            'user_id' => Auth::id(),
            'user_name' => Auth::check() ? Auth::user()->name : $paklaring->nama_lengkap,
            'user_email' => Auth::check() ? Auth::user()->email : $paklaring->email,
            'user_role' => Auth::check() ? Auth::user()->role : 'pemohon',
            'action' => 'submitted',
            'action_to' => 'Area',
            'notes' => 'Pengajuan surat referensi kerja / veklaring berhasil diinput dan diteruskan ke Tim Area.',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('paklaring.success', ['kode' => $kodeValidasi]);
    }

    /**
     * Halaman Sukses Setelah Pengajuan Berhasil Diinput
     */
    public function success($kode)
    {
        $paklaring = Paklaring::where('kode_validasi', $kode)->firstOrFail();
        return view('paklaring.success', compact('paklaring'));
    }

    /**
     * Cek Status Pengajuan Paklaring (Publik & Internal)
     */
    public function checkStatus(Request $request)
    {
        $search = trim($request->query('q', ''));
        $paklarings = collect();

        if (!empty($search)) {
            $paklarings = Paklaring::with('approvals')
                ->where('kode_validasi', $search)
                ->orWhere('nik', $search)
                ->latest('id')
                ->get();
        }

        return view('paklaring.check_status', compact('search', 'paklarings'));
    }

    /**
     * Unduh / Cetak Dokumen Surat Keterangan Kerja Resmi Publik (PDF)
     * Dapat diakses dari halaman cek status atau tautan langsung bila permohonan telah selesai / terbit
     */
    public function downloadPublicPdf($kode)
    {
        $paklaring = Paklaring::with('approvals')
            ->where('kode_validasi', $kode)
            ->orWhere('id', is_numeric($kode) ? (int)$kode : 0)
            ->orWhere('nomor_ref', $kode)
            ->first();

        if (!$paklaring) {
            return redirect()->route('paklaring.public.check')
                ->with('error', 'Dokumen surat keterangan kerja tidak ditemukan.');
        }

        // Pastikan status surat sudah selesai atau nomor referensi telah terisi
        if ($paklaring->status !== 'Selesai' && empty($paklaring->nomor_ref)) {
            return redirect()->route('paklaring.public.check', ['q' => $paklaring->kode_validasi])
                ->with('warning', 'Surat keterangan kerja ini masih dalam proses verifikasi approval dan belum resmi terbit.');
        }

        $pdfString = $this->generatePdfContent($paklaring);
        $safeName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $paklaring->kode_validasi . '_' . $paklaring->nama_lengkap);
        $filename = "Veklaring_{$safeName}.pdf";

        return response($pdfString, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "inline; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Halaman Validasi & Verifikasi Keabsahan Dokumen Surat Keterangan Kerja (Veklaring)
     * Ditampilkan otomatis ketika QR Code pada fisik surat dicetak dan di-scan melalui kamera ponsel
     */
    public function verifyQrCode($kode)
    {
        $paklaring = Paklaring::with(['approvals' => function ($q) {
            $q->orderBy('step_order', 'asc');
        }])
        ->where('kode_validasi', $kode)
        ->orWhere('nomor_ref', $kode)
        ->orWhere('id', is_numeric($kode) ? (int)$kode : 0)
        ->first();

        // Resolusi kode entitas resmi (AMK, AKP, ATK, ABO, ATB)
        $entityCode = 'AMK';
        $entityTitle = 'PT. Arina Multikarya';
        $entityUpper = 'PT ARINA MULTI KARYA';
        $kopUrl = null;

        if ($paklaring) {
            $entityCode = strtoupper($paklaring->kantor ?: 'AMK');
            if (!in_array($entityCode, ['AMK', 'AKP', 'ATK', 'ABO', 'ATB'])) {
                $resolved = Employee::getEntityCodeFromPrinciple($paklaring->kantor ?: $paklaring->prinsiple);
                if ($resolved) {
                    $entityCode = $resolved;
                }
            }

            $entityConfig = [
                'AMK' => ['upper' => 'PT ARINA MULTI KARYA', 'title' => 'PT. Arina Multikarya', 'files' => ['kop/kopamknew.png', 'kop/KopAMK.png']],
                'AKP' => ['upper' => 'PT ALVA KARYA PERKASA', 'title' => 'PT. Alva Karya Perkasa', 'files' => ['kop/kopakp.png', 'kop/KopAKP.png']],
                'ATK' => ['upper' => 'PT ANUGRAH TERPERCAYA KERJA', 'title' => 'PT. Anugrah Terpercaya Kerja', 'files' => ['kop/kopatk.png', 'kop/KopATK.png']],
                'ABO' => ['upper' => 'PT ARINA BINTANG OETAMA', 'title' => 'PT. Arina Bintang Oetama', 'files' => ['kop/kopabo.png', 'kop/KopABO.png']],
                'ATB' => ['upper' => 'PT ANUGRAH TALENTA BERKARYA', 'title' => 'PT. Anugrah Talenta Berkarya', 'files' => ['kop/kopatb.png', 'kop/KopATB.png']],
            ];

            $entData = $entityConfig[$entityCode] ?? $entityConfig['AMK'];
            $entityUpper = $entData['upper'];
            $entityTitle = $entData['title'];

            foreach ($entData['files'] as $f) {
                if (file_exists(public_path($f))) {
                    $kopUrl = asset($f);
                    break;
                }
            }
        }

        // Helper format tanggal Indonesia
        $formatTanggalIndo = function ($date) {
            if (!$date) return '-';
            $c = Carbon::parse($date);
            $bulan = [
                1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];
            return $c->format('d') . ' ' . ($bulan[(int)$c->format('m')] ?? '') . ' ' . $c->format('Y');
        };

        return view('paklaring.verify', compact(
            'paklaring',
            'kode',
            'entityCode',
            'entityTitle',
            'entityUpper',
            'kopUrl',
            'formatTanggalIndo'
        ));
    }

    /**
     * Detail Pengajuan & Panel Aksi Approval
     */
    public function show($id)
    {
        $paklaring = Paklaring::with(['approvals' => function ($q) {
            $q->orderBy('created_at', 'desc');
        }, 'creator'])->findOrFail($id);

        $currentUser = Auth::user();
        $workflow = PaklaringApprovalService::getWorkflow();
        $currentStep = PaklaringApprovalService::getCurrentStep($paklaring);
        $canApprove = $currentUser ? PaklaringApprovalService::canUserApprove($paklaring, $currentUser, $currentStep) : false;

        // Info PIC approver per step
        $stepsInfo = [];
        if ($workflow && $workflow->steps->isNotEmpty()) {
            foreach ($workflow->steps as $s) {
                $stepsInfo[$s->step_order] = PaklaringApprovalService::getStepApproverDisplayInfo($paklaring, $s);
            }
        }

        return view('paklaring.show', compact(
            'paklaring',
            'workflow',
            'currentStep',
            'canApprove',
            'stepsInfo'
        ));
    }

    /**
     * Step 1: Approve dari Tim Area
     */
    public function approveArea(Request $request, $id)
    {
        $paklaring = Paklaring::findOrFail($id);
        $user = Auth::user();

        if (!PaklaringApprovalService::canUserApprove($paklaring, $user)) {
            return back()->with('error', 'Anda tidak memiliki hak wewenang untuk menyetujui tahap Area pada pengajuan ini.');
        }

        $request->validate([
            'tgl_masuk' => 'required|date',
            'tgl_keluar' => 'required|date|after_or_equal:tgl_masuk',
            'tgl_kirimsurat' => 'required|date',
            'xpdc' => 'required|string|max:100',
            'noresi' => 'required|string|max:100',
            'catatan' => 'required|string|max:1000',
            'tandatangan_base64' => 'nullable|string',
        ]);

        $ttdFilename = null;
        if ($request->filled('tandatangan_base64')) {
            $base64 = $request->tandatangan_base64;
            $base64 = str_replace(['data:image/png;base64,', ' '], ['', '+'], $base64);
            $decoded = base64_decode($base64);
            $ttdFilename = "paklaring/{$paklaring->kode_validasi}/TTD_AREA_{$paklaring->kode_validasi}.png";
            Storage::disk('public')->put($ttdFilename, $decoded);
        }

        PaklaringApprovalService::approveArea($paklaring, $user, [
            'tgl_masuk' => $request->tgl_masuk,
            'tgl_keluar' => $request->tgl_keluar,
            'tgl_kirimsurat' => $request->tgl_kirimsurat,
            'xpdc' => trim($request->xpdc),
            'noresi' => trim($request->noresi),
            'catatan' => trim($request->catatan),
            'ttd_digital' => $ttdFilename,
        ], $request->ip());

        return redirect()->route('paklaring.show', $paklaring->id)
            ->with('success', 'Persetujuan Area berhasil disimpan dan diteruskan ke tahap HRD.');
    }

    /**
     * Step 2: Approve dari HRD (Pilihan ke DB atau langsung BPJS)
     */
    public function approveHrd(Request $request, $id)
    {
        $paklaring = Paklaring::findOrFail($id);
        $user = Auth::user();

        if (!PaklaringApprovalService::canUserApprove($paklaring, $user)) {
            return back()->with('error', 'Anda tidak memiliki hak wewenang untuk menyetujui tahap HRD pada pengajuan ini.');
        }

        $request->validate([
            'catatan' => 'required|string|max:1000',
            'action_to' => 'required|in:DB,BPJS',
            'tgl_masuk' => 'nullable|date',
            'tgl_keluar' => 'nullable|date',
            'tgl_kirimsurat' => 'nullable|date',
            'xpdc' => 'nullable|string|max:100',
            'noresi' => 'nullable|string|max:100',
        ]);

        PaklaringApprovalService::approveHrd($paklaring, $user, [
            'catatan' => trim($request->catatan),
            'action_to' => $request->action_to,
            'tgl_masuk' => $request->tgl_masuk,
            'tgl_keluar' => $request->tgl_keluar,
            'tgl_kirimsurat' => $request->tgl_kirimsurat,
            'xpdc' => $request->xpdc,
            'noresi' => $request->noresi,
        ], $request->ip());

        $target = $request->action_to === 'BPJS' ? 'Tim BPJS' : 'Tim DB';
        return redirect()->route('paklaring.show', $paklaring->id)
            ->with('success', "Persetujuan HRD berhasil disimpan dan diteruskan ke {$target}.");
    }

    /**
     * Step 3: Approve dari Tim DB (Database)
     * Pilihan: Kembalikan ke Area (jika persyaratan belum lengkap), Approve (Surat Rilis), atau Lanjut ke Step BPJS
     */
    public function approveDb(Request $request, $id)
    {
        $paklaring = Paklaring::findOrFail($id);
        $user = Auth::user();

        if (!PaklaringApprovalService::canUserApprove($paklaring, $user)) {
            return back()->with('error', 'Anda tidak memiliki hak wewenang untuk menyetujui tahap DB pada pengajuan ini.');
        }

        $request->validate([
            'action_type' => 'required|in:bpjs,selesai,return_area',
            'catatan' => 'required|string|max:1000',
            'nomor_ref' => 'nullable|string|max:100',
            'nomor_urutref' => 'nullable|integer',
            'tgl_masuk' => 'nullable|date',
            'tgl_keluar' => 'nullable|date',
        ]);

        if ($request->action_type === 'return_area') {
            PaklaringApprovalService::returnBack(
                $paklaring,
                $user,
                'Area',
                trim($request->catatan),
                $request->ip()
            );

            return redirect()->route('paklaring.show', $paklaring->id)
                ->with('warning', 'Pengajuan veklaring telah dikembalikan ke Tim Area karena persyaratan belum lengkap.');
        }

        PaklaringApprovalService::approveDb($paklaring, $user, [
            'action_type' => $request->action_type,
            'catatan' => trim($request->catatan),
            'nomor_ref' => $request->nomor_ref,
            'nomor_urutref' => $request->nomor_urutref,
            'tgl_masuk' => $request->tgl_masuk,
            'tgl_keluar' => $request->tgl_keluar,
        ], $request->ip());

        if ($request->action_type === 'selesai') {
            return redirect()->route('paklaring.show', $paklaring->id)
                ->with('success', 'Persetujuan Tim DB selesai! Surat Referensi Kerja (Veklaring) resmi telah diterbitkan.');
        }

        return redirect()->route('paklaring.show', $paklaring->id)
            ->with('success', 'Verifikasi Tim DB berhasil disimpan dan diteruskan ke Tim BPJS.');
    }

    /**
     * Step 4: Approve dari Tim BPJS (Finalisasi & Penomoran Resmi)
     */
    public function approveBpjs(Request $request, $id)
    {
        $paklaring = Paklaring::findOrFail($id);
        $user = Auth::user();

        if (!PaklaringApprovalService::canUserApprove($paklaring, $user)) {
            return back()->with('error', 'Anda tidak memiliki hak wewenang untuk menyetujui tahap BPJS pada pengajuan ini.');
        }

        $request->validate([
            'catatan' => 'nullable|string|max:1000',
            'nomor_ref' => 'nullable|string|max:100',
            'nomor_urutref' => 'nullable|integer',
        ]);

        PaklaringApprovalService::approveBpjs($paklaring, $user, [
            'catatan' => trim($request->catatan ?? ''),
            'nomor_ref' => $request->nomor_ref,
            'nomor_urutref' => $request->nomor_urutref,
        ], $request->ip());

        return redirect()->route('paklaring.show', $paklaring->id)
            ->with('success', 'Persetujuan Tim BPJS selesai! Surat Referensi Kerja resmi telah diterbitkan.');
    }

    /**
     * Hold Pengajuan
     */
    public function hold(Request $request, $id)
    {
        $paklaring = Paklaring::findOrFail($id);
        $user = Auth::user();

        if (!PaklaringApprovalService::canUserApprove($paklaring, $user)) {
            return back()->with('error', 'Anda tidak memiliki wewenang untuk menahan pengajuan ini.');
        }

        $request->validate([
            'catatan_hold' => 'required|string|max:1000',
        ]);

        PaklaringApprovalService::hold($paklaring, $user, trim($request->catatan_hold), $request->ip());

        return redirect()->route('paklaring.show', $paklaring->id)
            ->with('warning', 'Pengajuan veklaring berhasil di-Hold.');
    }

    /**
     * Tolak Pengajuan
     */
    public function reject(Request $request, $id)
    {
        $paklaring = Paklaring::findOrFail($id);
        $user = Auth::user();

        if (!PaklaringApprovalService::canUserApprove($paklaring, $user)) {
            return back()->with('error', 'Anda tidak memiliki wewenang untuk menolak pengajuan ini.');
        }

        $request->validate([
            'alasan_penolakan' => 'required|string|max:1000',
        ]);

        PaklaringApprovalService::reject($paklaring, $user, trim($request->alasan_penolakan), $request->ip());

        return redirect()->route('paklaring.show', $paklaring->id)
            ->with('error', 'Pengajuan veklaring telah ditolak.');
    }

    /**
     * Kembalikan Pengajuan ke Tahap Sebelumnya (Return Back)
     */
    public function returnBack(Request $request, $id)
    {
        $paklaring = Paklaring::findOrFail($id);
        $user = Auth::user();

        if (!PaklaringApprovalService::canUserApprove($paklaring, $user)) {
            return back()->with('error', 'Anda tidak memiliki wewenang untuk mengembalikan pengajuan ini.');
        }

        $request->validate([
            'target_bagian' => 'required|in:Area,HRD',
            'catatan_kembali' => 'required|string|max:1000',
        ]);

        PaklaringApprovalService::returnBack(
            $paklaring,
            $user,
            $request->target_bagian,
            trim($request->catatan_kembali),
            $request->ip()
        );

        return redirect()->route('paklaring.show', $paklaring->id)
            ->with('info', "Pengajuan veklaring dikembalikan ke tahap {$request->target_bagian}.");
    }

    /**
     * Cetak Dokumen Resmi Surat Keterangan Kerja (Veklaring) Langsung Berbentuk PDF
     * Selaras dengan implementasi Surat Peringatan (mPDF inline stream response)
     */
    public function printPdf($id)
    {
        $paklaring = Paklaring::with('approvals')->findOrFail($id);

        if ($paklaring->status !== 'Selesai') {
            return back()->with('error', 'Surat referensi kerja hanya dapat dicetak setelah semua tahapan approval selesai.');
        }

        $pdfString = $this->generatePdfContent($paklaring);
        $safeName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $paklaring->kode_validasi . '_' . $paklaring->nama_lengkap);
        $filename = "Veklaring_{$safeName}.pdf";

        return response($pdfString, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "inline; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Helper Render PDF String Dokumen Veklaring via mPDF
     */
    public function generatePdfContent(Paklaring $paklaring): string
    {
        // Resolusi kode entitas resmi (AMK, AKP, ATK, ABO, ATB)
        $entityCode = strtoupper($paklaring->kantor ?: 'AMK');
        if (!in_array($entityCode, ['AMK', 'AKP', 'ATK', 'ABO', 'ATB'])) {
            $resolved = Employee::getEntityCodeFromPrinciple($paklaring->kantor ?: $paklaring->prinsiple);
            if ($resolved) {
                $entityCode = $resolved;
            }
        }

        // Konfigurasi nama dan berkas kop entitas resmi (selaras dengan Surat Peringatan)
        $entityConfig = [
            'AMK' => [
                'upper' => 'PT ARINA MULTI KARYA',
                'title' => 'PT. Arina Multikarya',
                'files' => ['kop/kopamknew.png', 'kop/KopAMK.png'],
            ],
            'AKP' => [
                'upper' => 'PT ALVA KARYA PERKASA',
                'title' => 'PT. Alva Karya Perkasa',
                'files' => ['kop/kopakp.png', 'kop/KopAKP.png'],
            ],
            'ATK' => [
                'upper' => 'PT ANUGRAH TERPERCAYA KERJA',
                'title' => 'PT. Anugrah Terpercaya Kerja',
                'files' => ['kop/kopatk.png', 'kop/KopATK.png'],
            ],
            'ABO' => [
                'upper' => 'PT ARINA BINTANG OETAMA',
                'title' => 'PT. Arina Bintang Oetama',
                'files' => ['kop/kopabo.png', 'kop/KopABO.png'],
            ],
            'ATB' => [
                'upper' => 'PT ANUGRAH TALENTA BERKARYA',
                'title' => 'PT. Anugrah Talenta Berkarya',
                'files' => ['kop/kopatb.png', 'kop/KopATB.png', 'kop/KopATB.jpg'],
            ],
        ];

        $entData = $entityConfig[$entityCode] ?? $entityConfig['AMK'];
        $entityUpper = $entData['upper'];
        $entityTitle = $entData['title'];

        // Resolusi berkas kop resmi
        $kopBase64 = null;
        $kopUrl = null;
        foreach ($entData['files'] as $f) {
            $fullPath = public_path($f);
            if (file_exists($fullPath)) {
                $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
                $mime = ($ext === 'jpg' || $ext === 'jpeg') ? 'image/jpeg' : 'image/png';
                $kopBase64 = "data:{$mime};base64," . base64_encode(file_get_contents($fullPath));
                $kopUrl = asset($f);
                break;
            }
        }

        // Helper format tanggal bahasa Indonesia
        $formatTanggalIndo = function ($date) {
            if (!$date) return '-';
            $c = Carbon::parse($date);
            $bulan = [
                1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];
            return $c->format('d') . ' ' . ($bulan[(int)$c->format('m')] ?? '') . ' ' . $c->format('Y');
        };

        $tglMasukStr = $formatTanggalIndo($paklaring->tgl_masuk);
        $tglKeluarStr = $formatTanggalIndo($paklaring->tgl_keluar);

        // Tanggal persetujuan BPJS / rilis surat
        $bpjsApproval = $paklaring->approvals->firstWhere('step_order', 4);
        $tanggalTerbitStr = $formatTanggalIndo($bpjsApproval ? $bpjsApproval->created_at : ($paklaring->updated_at ?? now()));

        // QR Code URL verifikasi keaslian surat online (Membuka Halaman Validasi Resmi)
        $qrVerifyUrl = route('paklaring.public.verify', ['kode' => $paklaring->kode_validasi]);

        // Cache gambar QR Code di lokal agar mPDF dapat membaca langsung tanpa latency HTTP
        $qrDir = storage_path('app/public/qrcodes');
        if (!file_exists($qrDir)) {
            @mkdir($qrDir, 0755, true);
        }
        $qrFile = "{$qrDir}/QR_VERIFY_{$paklaring->kode_validasi}.png";
        if (!file_exists($qrFile)) {
            $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=" . urlencode($qrVerifyUrl);
            $qrData = @file_get_contents($qrUrl);
            if ($qrData) {
                @file_put_contents($qrFile, $qrData);
            }
        }

        $qrImageSrc = file_exists($qrFile)
            ? ('data:image/png;base64,' . base64_encode(file_get_contents($qrFile)))
            : ("https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=" . urlencode($qrVerifyUrl));

        $html = view('paklaring.pdf', compact(
            'paklaring',
            'entityCode',
            'entityUpper',
            'entityTitle',
            'kopBase64',
            'kopUrl',
            'tglMasukStr',
            'tglKeluarStr',
            'tanggalTerbitStr',
            'qrVerifyUrl',
            'qrImageSrc'
        ))->render();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 20,
            'margin_right' => 20,
            'margin_top' => 14,
            'margin_bottom' => 16,
            'default_font' => 'times',
        ]);

        // Watermark kode validasi diagonal di tengah surat (Native mPDF)
        $mpdf->SetWatermarkText($paklaring->kode_validasi, 0.08);
        $mpdf->showWatermarkText = true;

        $mpdf->SetTitle("Surat Keterangan Kerja - {$paklaring->nama_lengkap} ({$paklaring->kode_validasi})");
        $mpdf->SetAuthor("{$entityTitle} - HRD Management");
        $mpdf->WriteHTML($html);

        $safeName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $paklaring->kode_validasi . '_' . $paklaring->nama_lengkap);
        $filename = "Veklaring_{$safeName}.pdf";

        return $mpdf->Output($filename, 'S');
    }

    /**
     * Simpan file lampiran dengan auto-resize gambar otomatis bila dimensi melebihi 1600px
     */
    private function storeOptimizedFile($file, string $storageFolder, string $filename): string
    {
        $ext = strtolower($file->getClientOriginalExtension());
        $imageExtensions = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($ext, $imageExtensions, true) && function_exists('imagecreatefromstring')) {
            try {
                $imageContent = file_get_contents($file->getRealPath());
                $src = @imagecreatefromstring($imageContent);

                if ($src !== false) {
                    $width = imagesx($src);
                    $height = imagesy($src);
                    $maxWidth = 1600;
                    $maxHeight = 1600;

                    if ($width > $maxWidth || $height > $maxHeight) {
                        $ratio = min($maxWidth / $width, $maxHeight / $height);
                        $newWidth = (int)round($width * $ratio);
                        $newHeight = (int)round($height * $ratio);

                        $dst = imagecreatetruecolor($newWidth, $newHeight);
                        if ($ext === 'png') {
                            imagealphablending($dst, false);
                            imagesavealpha($dst, true);
                        }
                        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                        ob_start();
                        if ($ext === 'png') {
                            imagepng($dst, null, 7);
                        } else {
                            imagejpeg($dst, null, 82);
                        }
                        $optimizedContent = ob_get_clean();

                        imagedestroy($src);
                        imagedestroy($dst);

                        $path = "{$storageFolder}/{$filename}";
                        Storage::disk('public')->put($path, $optimizedContent);
                        return $path;
                    }
                    imagedestroy($src);
                }
            } catch (\Throwable $e) {
                // Fallback jika GD gagal
            }
        }

        $file->storeAs($storageFolder, $filename, 'public');
        return "{$storageFolder}/{$filename}";
    }
}
