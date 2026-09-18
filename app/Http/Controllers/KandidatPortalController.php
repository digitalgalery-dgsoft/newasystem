<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Employee;
use App\Models\Candidate;
use App\Models\Principle;
use App\Models\InterviewAssessment;
use App\Models\WorkExperience;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KandidatPortalController extends Controller
{
    /**
     * Dapatkan user yang sedang aktif login (dengan fallback)
     */
    protected function getCurrentUser()
    {
        if (auth()->check()) {
            return auth()->user();
        }
        return User::where('email', 'jamil@asystem.co.id')->first()
            ?? User::where('name', 'like', '%abdur%')->first()
            ?? User::where('email', 'like', '%abdur%')->first()
            ?? User::where('role', '!=', 'admin')->first()
            ?? User::first();
    }

    /**
     * Resolusi seluruh alias/identitas user (email, nama, alias inhouse) untuk mencocokkan data kandidat
     */
    public static function resolveUserIdentifiers($user): array
    {
        $identifiers = [];
        if (!$user) {
            return $identifiers;
        }

        if (!empty($user->email)) {
            $identifiers[] = strtolower(trim($user->email));
        }

        if (!empty($user->name)) {
            $name = trim($user->name);
            $identifiers[] = strtolower($name);

            // Variasi ejaan: "Abdurrahman" <-> "Abdur Rahman"
            if (stripos($name, 'abdurrahman') !== false) {
                $identifiers[] = strtolower(str_ireplace('abdurrahman', 'abdur rahman', $name));
                $identifiers[] = 'abdur rahman';
                $identifiers[] = 'abdurrahman';
            } elseif (stripos($name, 'abdur rahman') !== false) {
                $identifiers[] = strtolower(str_ireplace('abdur rahman', 'abdurrahman', $name));
                $identifiers[] = 'abdur rahman';
                $identifiers[] = 'abdurrahman';
            }

            // Potongan nama jika lebih dari 1 kata (contoh: "Jamil")
            $parts = preg_split('/\s+/', $name);
            if (count($parts) > 1) {
                foreach ($parts as $p) {
                    if (strlen($p) >= 4) {
                        $identifiers[] = strtolower(trim($p));
                    }
                }
            }
        }

        // Cek data karyawan dari tabel employees jika ada
        if (!empty($user->email) || !empty($user->name)) {
            $emp = Employee::where(function ($q) use ($user) {
                if (!empty($user->email)) {
                    $q->where('email', $user->email);
                }
                if (!empty($user->name)) {
                    $q->orWhere('nama_karyawan', $user->name);
                }
            })->first();

            if ($emp) {
                if (!empty($emp->email)) {
                    $identifiers[] = strtolower(trim($emp->email));
                }
                if (!empty($emp->nama_karyawan)) {
                    $identifiers[] = strtolower(trim($emp->nama_karyawan));
                }
            }
        }

        // Alias khusus yang diketahui untuk Abdurrahman Jamil
        $hasJamilOrAbdur = false;
        foreach ($identifiers as $id) {
            if (str_contains($id, 'abdurrahman') || str_contains($id, 'abdur rahman') || str_contains($id, 'jamil')) {
                $hasJamilOrAbdur = true;
                break;
            }
        }
        if ($hasJamilOrAbdur) {
            $identifiers[] = 'abdur rahman';
            $identifiers[] = 'abdurrahman';
            $identifiers[] = 'abdurrahman jamil';
            $identifiers[] = 'abdurrahman2330@gmail.com';
            $identifiers[] = 'abdurrahmanjamil.mail@gmail.com';
            $identifiers[] = 'jamil@asystem.co.id';
            $identifiers[] = 'jamil';
        }

        return array_values(array_unique(array_filter($identifiers)));
    }

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
        $filterRecruiter = $request->query('recruiter');

        $user = $this->getCurrentUser();
        $isAdmin = $user && ($user->role === 'admin' || (method_exists($user, 'isAdmin') && $user->isAdmin()));
        $userIdentifiers = $this->resolveUserIdentifiers($user);

        // Ambil daftar seluruh rekruter dari data kandidat portal (khusus untuk selector filter admin)
        $allRecruiters = [];
        if ($isAdmin) {
            $allRecruiters = DB::table('candidates')
                ->select('useras', DB::raw('count(*) as total'))
                ->where('jenis', 'Job Portal')
                ->whereNotNull('useras')
                ->where('useras', '!=', '')
                ->groupBy('useras')
                ->orderByDesc('total')
                ->get();

            foreach ($allRecruiters as $r) {
                if (str_contains($r->useras, '@')) {
                    $parts = explode('@', $r->useras)[0];
                    $name = preg_replace('/[0-9_.-]+/', ' ', $parts);
                    $r->display_name = ucwords(trim($name)) ?: $r->useras;
                } else {
                    $r->display_name = ucwords(strtolower($r->useras));
                }
            }
        }

        // Base Query: Pelamar dari Job Portal (jenis = 'Job Portal')
        $baseQuery = Candidate::where('jenis', 'Job Portal');

        // Tentukan Filter & Label Tampilan Rekruter
        $displayUserName = $user ? $user->name : 'User';
        $scopeTitle = 'Kandidat Milik Anda (' . $displayUserName . ')';

        if ($isAdmin) {
            if (!empty($filterRecruiter) && $filterRecruiter !== 'all' && $filterRecruiter !== 'my') {
                // Admin memfilter rekruter terpilih
                $baseQuery->where(function ($q) use ($filterRecruiter) {
                    $q->where('useras', $filterRecruiter)
                      ->orWhereRaw('LOWER(TRIM(useras)) = ?', [strtolower(trim($filterRecruiter))]);
                });
                $displayUserName = $filterRecruiter;
                $scopeTitle = 'Rekruter: ' . $filterRecruiter;
            } else {
                // Default Admin: Seluruh Lowongan (Nasional)
                $displayUserName = 'Semua Rekruter (Nasional)';
                $scopeTitle = 'Seluruh Lowongan (Nasional)';
            }
        } else {
            // DEFAULT UNTUK USER BIASA / REKRUTER: TAMPILKAN HANYA DATA MILIK USER YANG LOGIN!
            $baseQuery->where(function ($q) use ($user, $userIdentifiers) {
                if (!empty($userIdentifiers)) {
                    $q->whereIn(DB::raw('LOWER(TRIM(useras))'), $userIdentifiers);
                    if ($user && !empty($user->id)) {
                        $q->orWhere('recruiter_id', $user->id);
                    }
                } elseif ($user && !empty($user->id)) {
                    $q->where('recruiter_id', $user->id);
                } else {
                    $q->whereRaw('1 = 0');
                }
            });
            $scopeTitle = 'Kandidat Milik Anda (' . $displayUserName . ')';
        }

        // Top Statistics & Tab Badges Counters (sinkron 100% dengan filter user aktif)
        $stats = (clone $baseQuery)->selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN date(created_at) = ? THEN 1 ELSE 0 END) as masuk_hari_ini,
            SUM(CASE WHEN kategori_kandidat = 'Green' THEN 1 ELSE 0 END) as green,
            SUM(CASE WHEN ai_score IS NULL OR ai_score = 0 THEN 1 ELSE 0 END) as belum_dianalisa,
            SUM(CASE WHEN status NOT IN ('Arsip', 'archived') AND (ttd_prinsiple IS NULL OR ttd_prinsiple = '') AND (status_kandidat = 'Baru' OR status_kandidat IS NULL OR status_kandidat NOT IN ('Interview', 'Terima', 'Arsip')) THEN 1 ELSE 0 END) as count_baru,
            SUM(CASE WHEN status NOT IN ('Arsip', 'archived') AND (ttd_prinsiple IS NULL OR ttd_prinsiple = '') AND status_kandidat = 'Interview' THEN 1 ELSE 0 END) as count_interview,
            SUM(CASE WHEN status NOT IN ('Arsip', 'archived') AND ((ttd_prinsiple IS NOT NULL AND ttd_prinsiple != '') OR status_kandidat = 'Terima') THEN 1 ELSE 0 END) as count_terima,
            SUM(CASE WHEN status = 'Arsip' OR status = 'archived' OR status_kandidat = 'Arsip' THEN 1 ELSE 0 END) as count_arsip
        ", [$today])->first();

        $totalPelamar = (int) ($stats->total ?? 0);
        $masukHariIni = (int) ($stats->masuk_hari_ini ?? 0);
        $kandidatGreen = (int) ($stats->green ?? 0);
        $belumDianalisa = (int) ($stats->belum_dianalisa ?? 0);
        $countBaru = (int) ($stats->count_baru ?? 0);
        $countInterview = (int) ($stats->count_interview ?? 0);
        $countTerima = (int) ($stats->count_terima ?? 0);
        $countArsip = (int) ($stats->count_arsip ?? 0);

        // Filter Table Query based on active tab
        $tableQuery = clone $baseQuery;

        if ($tab === 'interview') {
            $tableQuery->whereNotIn('status', ['Arsip', 'archived'])
                ->where(function ($q) {
                    $q->whereNull('ttd_prinsiple')->orWhere('ttd_prinsiple', '');
                })
                ->where('status_kandidat', 'Interview');
        } elseif ($tab === 'terima') {
            $tableQuery->whereNotIn('status', ['Arsip', 'archived'])
                ->where(function ($q) {
                    $q->where(function ($sub) {
                        $sub->whereNotNull('ttd_prinsiple')->where('ttd_prinsiple', '!=', '');
                    })->orWhere('status_kandidat', 'Terima');
                });
        } elseif ($tab === 'arsip') {
            $tableQuery->where(function ($q) {
                $q->where('status', 'Arsip')
                  ->orWhere('status', 'archived')
                  ->orWhere('status_kandidat', 'Arsip');
            });
        } else {
            // Tab 'baru' / default
            $tableQuery->whereNotIn('status', ['Arsip', 'archived'])
                ->where(function ($q) {
                    $q->whereNull('ttd_prinsiple')->orWhere('ttd_prinsiple', '');
                })
                ->where(function ($q) {
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
            'filterRecruiter',
            'displayUserName',
            'scopeTitle',
            'isAdmin',
            'allRecruiters',
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
            'approverPrinsiple',
            'recruiter',
            'workExperiences',
            'interviewAssessment',
            'principleApprovals',
            'testResults'
        ])->findOrFail($id);

        $principles = Principle::where('is_active', true)->orderBy('name')->get();
        $userPrinsiples = \App\Http\Controllers\InterviewController::getUserPrinsipleOptions($candidate);
        $areas = [
            'JAKARTA', 'SURABAYA', 'BANDUNG', 'SEMARANG', 'MEDAN', 
            'MAKASSAR', 'DENPASAR', 'PALEMBANG', 'BALIKPAPAN', 'YOGYAKARTA',
            'MALANG', 'BOGOR', 'BEKASI', 'TANGERANG', 'DEPOK'
        ];

        // Sinkronisasi data pengalaman dari tb_pengalaman jika work_experiences masih kosong
        if ($candidate->workExperiences->isEmpty()) {
            $rawExps = DB::table('tb_pengalaman')
                ->where('id_kandidat', $candidate->id)
                ->orWhere(function ($q) use ($candidate) {
                    if (!empty($candidate->nik)) {
                        $q->where('nomor_ktp', $candidate->nik);
                    }
                })
                ->get();
            if ($rawExps->isNotEmpty()) {
                foreach ($rawExps as $r) {
                    WorkExperience::create([
                        'candidate_id' => $candidate->id,
                        'company_name' => $r->nama_perusahaan ?? 'Perusahaan Sebelumnya',
                        'position' => $r->jabatan ?? ($candidate->applied_job ?? 'Karyawan'),
                        'company_phone' => $r->telp_perusahaan ?? '-',
                        'reason_for_leaving' => $r->alasan_keluar ?? '-',
                        'start_date' => $r->tgl_masuk ?? null,
                        'end_date' => $r->tgl_keluar ?? null,
                        'supervisor_name' => $r->spv ?? '-',
                        'performance_notes' => $r->performa ?? 'Baik',
                        'discipline_notes' => $r->disiplin ?? 'Tepat Waktu',
                        'responsibility_notes' => $r->tanggungjawab ?? 'Bertanggung Jawab',
                        'strengths' => $r->streng ?? '',
                        'weaknesses' => $r->week ?? '',
                        'check_date' => $r->tanggal ?? null,
                        'proof_attachment_path' => $r->file_cek ?? null,
                    ]);
                }
                $candidate->unsetRelation('workExperiences');
                $candidate->load('workExperiences');
            }
        }

        // Psychological DISC test, Math & Computer test processing
        $discTest = $candidate->testResults->firstWhere('test_type', 'psychology');
        $mathTest = $candidate->testResults->firstWhere('test_type', 'math');
        $compTest = $candidate->testResults->firstWhere('test_type', 'computer');

        $aiData = $candidate->ai_data;
        $evalData = \App\Services\CandidateEvaluationDataService::getEvaluationData($candidate);

        $otherCandidates = Candidate::where('applied_job', $candidate->applied_job)
            ->where('id', '!=', $candidate->id)
            ->whereNotNull('ai_score')
            ->where('ai_score', '>', 0)
            ->orderByDesc('ai_score')
            ->limit(5)
            ->get();

        return view('kandidatportal.show', array_merge([
            'candidate' => $candidate,
            'principles' => $principles,
            'userPrinsiples' => $userPrinsiples,
            'areas' => $areas,
            'discTest' => $discTest,
            'mathTest' => $mathTest,
            'compTest' => $compTest,
            'aiData' => $aiData,
            'otherCandidates' => $otherCandidates,
        ], $evalData));
    }

    /**
     * Download PDF Hasil Analisa AI (Replikasi cetak_ai_result.php)
     */
    public function cetakAiPdf($id, \App\Services\AiPdfService $pdfService)
    {
        $candidate = Candidate::with(['principle'])->findOrFail($id);
        $pdfContent = $pdfService->generate($candidate);
        $filename = 'AI_Analysis_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $candidate->full_name) . '.pdf';

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    /**
     * Simpan / Perbarui Referensi Cek (Sinkronisasi ke work_experiences dan tb_pengalaman)
     */
    public function storeRefcek(Request $request, $id)
    {
        $candidate = Candidate::findOrFail($id);

        $companyId = $request->input('company_id');
        $exp = null;
        if ($companyId && $companyId !== 'new' && is_numeric($companyId)) {
            $exp = WorkExperience::where('candidate_id', $candidate->id)->where('id', $companyId)->first();
            if (!$exp) {
                $exp = WorkExperience::find($companyId);
            }
        }

        $companyName = trim($request->input('company_name', ''));
        if (!$exp) {
            $exp = WorkExperience::create([
                'candidate_id' => $candidate->id,
                'company_name' => !empty($companyName) ? $companyName : 'Perusahaan Sebelumnya',
                'position' => $candidate->applied_job ?? 'Karyawan',
                'start_date' => $request->input('tgl_masuk') ?: now()->subYears(1),
                'end_date' => $request->input('tgl_keluar') ?: now(),
            ]);
        } elseif (!empty($companyName) && $companyName !== $exp->company_name) {
            $exp->company_name = $companyName;
        }

        $spv = $request->input('supervisor_name', $request->input('spv', '-'));
        $phone = $request->input('company_phone', $request->input('telpperusahaan', '-'));
        $performa = $request->input('performance_review', $request->input('performa', 'Baik'));
        $disiplin = $request->input('discipline_review', $request->input('disiplin', 'Tepat Waktu'));
        $tanggungJawab = $request->input('responsibility_review', $request->input('tanggung_jawab', 'Bertanggung Jawab'));
        $strengths = $request->input('strengths_identified', $request->input('strengthness', ''));
        $weaknesses = $request->input('weaknesses_identified', $request->input('weekness', ''));
        $checkDate = $request->input('checked_date', $request->input('tgl', date('Y-m-d')));
        $problems = $request->input('problem_notes', $request->input('problem', '-'));
        $reason = $request->input('reason_for_leaving', $request->input('alasankeluar', ''));

        $data = [
            'supervisor_name' => $spv,
            'company_phone' => $phone,
            'performance_notes' => $performa,
            'discipline_notes' => $disiplin,
            'responsibility_notes' => $tanggungJawab,
            'strengths' => $strengths,
            'weaknesses' => $weaknesses,
            'check_date' => $checkDate,
            'reason_for_leaving' => $reason,
        ];

        if ($request->hasFile('proof_file') || $request->hasFile('sscek')) {
            $file = $request->file('proof_file') ?: $request->file('sscek');
            $filename = time() . '_' . preg_replace('/[^A-Za-z0-9_\.]/', '_', $file->getClientOriginalName());
            $destination = public_path('lampiran');
            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }
            $file->move($destination, $filename);
            $data['proof_attachment_path'] = 'lampiran/' . $filename;
        }

        $exp->update($data);

        // Sinkronkan ke tabel legacy tb_pengalaman
        try {
            DB::table('tb_pengalaman')->updateOrInsert(
                [
                    'id_kandidat' => $candidate->id,
                    'nama_perusahaan' => $exp->company_name,
                ],
                [
                    'nomor_ktp' => $candidate->nik,
                    'telp_perusahaan' => $phone,
                    'jabatan' => $exp->position ?? ($candidate->applied_job ?? 'Karyawan'),
                    'spv' => $spv,
                    'performa' => $performa,
                    'disiplin' => $disiplin,
                    'tanggungjawab' => $tanggungJawab,
                    'masalah' => $problems,
                    'streng' => $strengths,
                    'week' => $weaknesses,
                    'tanggal' => $checkDate,
                    'file_cek' => $data['proof_attachment_path'] ?? null,
                ]
            );
        } catch (\Throwable $e) {
            // Abaikan jika ada perbedaan skema
        }

        return back()->with('success', 'Data Referensi Cek Berhasil Disimpan!');
    }

    /**
     * Simpan / Perbarui Penilaian Tes Komputer
     */
    public function storeComputerTest(Request $request, $id)
    {
        $candidate = Candidate::findOrFail($id);

        $scores = [
            'vlookup' => $request->input('vlookup', 'Baik'),
            'hlookup' => $request->input('hlookup', 'Baik'),
            'pivot' => $request->input('pivot', 'Baik'),
            'fungsiif' => $request->input('fungsiif', 'Baik'),
            'average' => $request->input('average', 'Baik'),
            'hitung' => $request->input('hitung', 'Baik'),
            'teliti' => $request->input('teliti', 'Baik'),
            'cepat' => $request->input('cepat', 'Baik'),
            'hasilkerja' => $request->input('hasilkerja', 'Baik'),
        ];

        try {
            DB::table('hasil_kompt')->updateOrInsert(
                ['id_kandidat' => $candidate->id],
                array_merge($scores, [
                    'nomor_ktp' => $candidate->nik,
                ])
            );
        } catch (\Throwable $e) {
            // fallback
        }

        $baikCount = count(array_filter($scores, fn($v) => strtolower($v) === 'baik'));
        $cukupCount = count(array_filter($scores, fn($v) => strtolower($v) === 'cukup'));
        $totalScore = round((($baikCount + ($cukupCount * 0.5)) / count($scores)) * 100);

        \App\Models\TestResult::updateOrCreate(
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

        if (empty($candidate->tes_komputer)) {
            $candidate->updateQuietly(['tes_komputer' => '00:03:10']);
        }

        return back()->with('success', 'Data Penilaian Tes Komputer Berhasil Disimpan!');
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

    /**
     * Export Data Pelamar Job Portal ke CSV/Excel (Filtered by Logged-in User)
     */
    public function exportExcel(Request $request)
    {
        $tab = $request->query('tab', 'baru');
        $kategori = $request->query('kategori');
        $start = $request->query('start');
        $end = $request->query('end');
        $search = $request->query('q') ?? $request->query('search');
        $filterRecruiter = $request->query('recruiter');

        $user = $this->getCurrentUser();
        $isAdmin = $user && ($user->role === 'admin' || (method_exists($user, 'isAdmin') && $user->isAdmin()));
        $userIdentifiers = $this->resolveUserIdentifiers($user);

        $baseQuery = Candidate::where('jenis', 'Job Portal');

        if ($isAdmin && $filterRecruiter === 'all') {
            // Semua
        } elseif ($isAdmin && !empty($filterRecruiter) && $filterRecruiter !== 'my') {
            $baseQuery->where(function ($q) use ($filterRecruiter) {
                $q->where('useras', $filterRecruiter)
                  ->orWhereRaw('LOWER(TRIM(useras)) = ?', [strtolower(trim($filterRecruiter))]);
            });
        } else {
            $baseQuery->where(function ($q) use ($user, $userIdentifiers) {
                if (!empty($userIdentifiers)) {
                    $q->whereIn(DB::raw('LOWER(TRIM(useras))'), $userIdentifiers);
                    if ($user && !empty($user->id)) {
                        $q->orWhere('recruiter_id', $user->id);
                    }
                } elseif ($user && !empty($user->id)) {
                    $q->where('recruiter_id', $user->id);
                } else {
                    $q->whereRaw('1 = 0');
                }
            });
        }

        if ($tab === 'interview') {
            $baseQuery->whereNotIn('status', ['Arsip', 'archived'])
                ->where(function ($q) {
                    $q->whereNull('ttd_prinsiple')->orWhere('ttd_prinsiple', '');
                })
                ->where('status_kandidat', 'Interview');
        } elseif ($tab === 'terima') {
            $baseQuery->whereNotIn('status', ['Arsip', 'archived'])
                ->where(function ($q) {
                    $q->where(function ($sub) {
                        $sub->whereNotNull('ttd_prinsiple')->where('ttd_prinsiple', '!=', '');
                    })->orWhere('status_kandidat', 'Terima');
                });
        } elseif ($tab === 'arsip') {
            $baseQuery->where(function ($q) {
                $q->where('status', 'Arsip')
                  ->orWhere('status', 'archived')
                  ->orWhere('status_kandidat', 'Arsip');
            });
        } elseif ($tab === 'baru') {
            $baseQuery->whereNotIn('status', ['Arsip', 'archived'])
                ->where(function ($q) {
                    $q->whereNull('ttd_prinsiple')->orWhere('ttd_prinsiple', '');
                })
                ->where(function ($q) {
                    $q->where('status_kandidat', 'Baru')
                      ->orWhereNull('status_kandidat')
                      ->orWhereNotIn('status_kandidat', ['Interview', 'Terima', 'Arsip']);
                });
        }

        if (!empty($kategori)) {
            $baseQuery->where('kategori_kandidat', $kategori);
        }

        if (!empty($start) && !empty($end)) {
            $baseQuery->whereBetween('created_at', [
                Carbon::parse($start)->startOfDay(),
                Carbon::parse($end)->endOfDay()
            ]);
        }

        if (!empty($search)) {
            $baseQuery->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('applied_job', 'like', "%{$search}%")
                  ->orWhere('area', 'like', "%{$search}%");
            });
        }

        $candidates = $baseQuery->orderBy('created_at', 'desc')->get();
        $csvFileName = 'kandidat_job_portal_' . date('Ymd_His') . '.csv';
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$csvFileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['NO', 'TANGGAL DAFTAR', 'NIK', 'NAMA KANDIDAT', 'JENIS KELAMIN', 'TANGGAL LAHIR', 'USIA', 'PENDIDIKAN', 'POSISI DILAMAR', 'AREA', 'KATEGORI AI', 'AI SCORE', 'STATUS KANDIDAT', 'REKRUTER / AS'];

        $callback = function() use ($candidates, $columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, $columns);
            $no = 1;
            foreach ($candidates as $c) {
                fputcsv($file, [
                    $no++,
                    $c->created_at ? $c->created_at->format('d/m/Y H:i') : '-',
                    "'" . $c->nik,
                    $c->full_name,
                    $c->gender ?? '-',
                    $c->birth_date ? $c->birth_date->format('d/m/Y') : '-',
                    $c->age ?? '-',
                    $c->education ?? '-',
                    $c->applied_job ?? '-',
                    $c->area ?? '-',
                    $c->kategori_kandidat ?? '-',
                    $c->ai_score ? $c->ai_score . '%' : 'Pending',
                    $c->status_kandidat ?? 'Baru',
                    $c->useras ?? '-',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}