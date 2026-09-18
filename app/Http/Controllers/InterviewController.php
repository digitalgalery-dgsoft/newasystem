<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Principle;
use App\Models\User;
use App\Models\UserPrinsiple;
use App\Models\Employee;
use App\Models\InterviewAssessment;
use App\Models\WorkExperience;
use App\Models\TestResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class InterviewController extends Controller
{
    private function getCurrentUser()
    {
        if (auth()->check()) {
            return auth()->user();
        }

        return User::where('email', 'jamil@asystem.co.id')->first()
            ?? User::where('name', 'like', '%abdur%')->first()
            ?? User::where('email', 'like', '%abdur%')->first()
            ?? User::where('role', '!=', 'admin')->first()
            ?? User::firstOrCreate(
                ['email' => 'jamil@asystem.co.id'],
                [
                    'name' => 'Abdurrahman Jamil',
                    'password' => bcrypt('password'),
                    'role' => 'karyawan_inhouse',
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

    public static function resolveCandidateAsName($candidate, $fallbackUser = null): string
    {
        if (!empty($candidate->user_name_formatted)) {
            return $candidate->user_name_formatted;
        }

        $useras = trim($candidate->useras ?? '');
        if (!empty($useras)) {
            $lowerEmail = strtolower($useras);
            $u = User::whereRaw('LOWER(email) = ?', [$lowerEmail])->orWhere('name', $useras)->first();
            if ($u) {
                return $u->name;
            }
            $emp = Employee::whereRaw('LOWER(email) = ?', [$lowerEmail])->orWhere('nama_karyawan', $useras)->first();
            if ($emp) {
                return $emp->nama_karyawan;
            }
            if (str_contains($useras, '@')) {
                $parts = explode('@', $useras)[0];
                $name = preg_replace('/[0-9_.-]+/', ' ', $parts);
                return ucwords(trim($name)) ?: $useras;
            }
            return ucwords(strtolower($useras));
        }

        if ($candidate->recruiter && !empty($candidate->recruiter->name)) {
            return $candidate->recruiter->name;
        }

        if ($fallbackUser && !empty($fallbackUser->name)) {
            return $fallbackUser->name;
        }

        return 'Admin Rekrutmen';
    }

    private function buildWaUrl($candidate, $user, $salam): string
    {
        $induk = $candidate->principle?->parent_company ?? $candidate->principle?->name ?? 'ESA Groups';
        $asName = $candidate->user_name_formatted ?? self::resolveCandidateAsName($candidate, $user);
        $pesan = "{$salam} sdr/sdr(i) {$candidate->full_name}\n\n*Tes Online {$induk}*\n\nBerikut Kode Akses Tes Online Kamu\n\nUsername : {$candidate->nik}\nPassword : _Gunakan Tanggal lahir dengan Format ddmmyyyy_\n\nAkses Melalui Link Berikut https://new.asystem.co.id/cbt/login\n\nTutorial Cara Login & Isi Data Profile : https://youtu.be/l3KW9-13z7c\n\n_Terima Kasih_\n\n_Regards_\n{$asName}";

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
        $filterUser = $request->query('filter_user');

        // Ambil daftar rekruter dengan jumlah kandidat aktif
        $allRecruiters = \Illuminate\Support\Facades\DB::table('candidates')
            ->select('useras', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->whereNotIn('status', ['Arsip', 'archived'])
            ->where(function ($q) {
                $q->whereNull('jenis')->orWhere('jenis', '');
            })
            ->where(function ($q) {
                $q->whereNull('ttd_prinsiple')->orWhere('ttd_prinsiple', '');
            })
            ->whereNotNull('useras')
            ->where('useras', '!=', '')
            ->groupBy('useras')
            ->orderByDesc('total')
            ->get();

        $recruiterEmails = $allRecruiters->pluck('useras')->filter(fn($u) => str_contains($u, '@'))->map(fn($e) => strtolower(trim($e)))->unique()->values()->all();
        $employeeLookup = [];
        if (!empty($recruiterEmails)) {
            $emps = Employee::whereIn(\Illuminate\Support\Facades\DB::raw('LOWER(email)'), $recruiterEmails)
                ->where('status', 'Aktiv')
                ->get(['email', 'nama_karyawan', 'area']);
            foreach ($emps as $e) {
                $employeeLookup[strtolower(trim($e->email))] = $e;
            }
        }
        foreach ($allRecruiters as $r) {
            $lower = strtolower(trim($r->useras));
            if (isset($employeeLookup[$lower])) {
                $r->display_name = $employeeLookup[$lower]->nama_karyawan;
                $r->area = $employeeLookup[$lower]->area;
            } elseif (str_contains($r->useras, '@')) {
                $parts = explode('@', $r->useras)[0];
                $name = preg_replace('/[0-9_.-]+/', ' ', $parts);
                $r->display_name = ucwords(trim($name)) ?: $r->useras;
                $r->area = '';
            } else {
                $r->display_name = ucwords(strtolower($r->useras));
                $r->area = '';
            }
        }

        // Query 1: Data Kandidat Milik Anda (Tampil Sesuai User / Karyawan yang Login)
        $myCandidatesQuery = Candidate::with(['principle', 'recruiter', 'testResults'])
            ->whereNotIn('status', ['Arsip', 'archived'])
            ->where(function ($q) {
                $q->whereNull('jenis')->orWhere('jenis', '');
            })
            ->where(function ($q) {
                $q->whereNull('ttd_prinsiple')->orWhere('ttd_prinsiple', '');
            });

        $isAdmin = $user->isAdmin() || $user->role === 'admin';
        $userIdentifiers = KandidatPortalController::resolveUserIdentifiers($user);
        $displayRecruiterName = $user->name;
        $displayRecruiterTitle = $user->job_title ?? 'REKRUTMEN';
        $displayRecruiterArea = $user->area ?? 'JAKARTA';

        if ($isAdmin) {
            if (!empty($filterUser) && $filterUser !== 'all' && $filterUser !== 'my') {
                // Filter rekruter terpilih dari selector
                $myCandidatesQuery->where(function ($q) use ($filterUser) {
                    $q->where('useras', $filterUser)
                      ->orWhereRaw('LOWER(TRIM(useras)) = ?', [strtolower(trim($filterUser))]);
                });
                $foundRec = $allRecruiters->firstWhere('useras', $filterUser);
                $displayRecruiterName = $foundRec ? $foundRec->display_name : $filterUser;
                $displayRecruiterArea = $foundRec ? ($foundRec->area ?: 'INDONESIA') : 'INDONESIA';
                $displayRecruiterTitle = 'REKRUTER TERPILIH';
            } else {
                // Default Admin: Tampilkan semua data kandidat nasional
                $displayRecruiterName = 'Semua Rekruter (Nasional)';
                $displayRecruiterTitle = 'SUPER ADMIN - All';
                $displayRecruiterArea = 'NASIONAL';
            }
        } else {
            // USER BIASA / REKRUTER: Tampilkan HANYA data milik user yang sedang login!
            $myCandidatesQuery->where(function ($q) use ($user, $userIdentifiers) {
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

        if ($searchMy) {
            $myCandidatesQuery->where(function ($q) use ($searchMy) {
                $q->where('full_name', 'like', "%{$searchMy}%")
                  ->orWhere('nik', 'like', "%{$searchMy}%")
                  ->orWhere('applied_job', 'like', "%{$searchMy}%");
            });
        }

        // Hitung metrik ringkasan akurat untuk filter aktif
        $statTotal = (clone $myCandidatesQuery)->count();
        $statProfileComplete = (clone $myCandidatesQuery)->where('is_profile_complete', true)->count();
        $statTestDone = (clone $myCandidatesQuery)->where(function ($q) {
            $q->where(function ($sq) {
                $sq->whereNotNull('tes_kepribadian')
                   ->where('tes_kepribadian', '!=', '')
                   ->where('tes_kepribadian', '!=', '00:00:00')
                   ->where('tes_kepribadian', '!=', '-');
            })->orWhere(function ($sq) {
                $sq->whereNotNull('tes_matematika')
                   ->where('tes_matematika', '!=', '')
                   ->where('tes_matematika', '!=', '00:00:00')
                   ->where('tes_matematika', '!=', '-');
            });
        })->count();

        $myCandidates = $myCandidatesQuery->orderBy('id', 'desc')->paginate(15, ['*'], 'page_my');
        self::attachInhouseEmployeeNames($myCandidates);

        // Tambahkan atribut wa_url untuk setiap kandidat
        $myCandidates->getCollection()->transform(function ($c) use ($user, $salam) {
            $c->wa_url = $this->buildWaUrl($c, $user, $salam);
            return $c;
        });

        // Query 2: Data Kandidat Area (Hanya dieksekusi jika bukan admin yang sedang melihat view nasional)
        if ($isAdmin && (empty($filterUser) || $filterUser === 'all')) {
            $areaCandidates = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15, 1, ['path' => $request->url(), 'pageName' => 'page_area']);
        } else {
            $areaCandidatesQuery = Candidate::with(['principle', 'recruiter', 'testResults'])
                ->whereNotIn('status', ['Arsip', 'archived'])
                ->where(function ($q) {
                    $q->whereNull('jenis')->orWhere('jenis', '');
                })
                ->where(function ($q) {
                    $q->whereNull('ttd_prinsiple')->orWhere('ttd_prinsiple', '');
                })
                ->where('area', $displayRecruiterArea ?? $user->area ?? 'JAKARTA')
                ->where(function ($q) use ($user, $userIdentifiers) {
                    if ($user && !empty($user->id)) {
                        $q->where('recruiter_id', '!=', $user->id)->orWhereNull('recruiter_id');
                    }
                    if (!empty($userIdentifiers)) {
                        $q->whereNotIn(DB::raw('LOWER(TRIM(useras))'), $userIdentifiers)->orWhereNull('useras');
                    }
                });

            if ($searchArea) {
                $areaCandidatesQuery->where(function ($q) use ($searchArea) {
                    $q->where('full_name', 'like', "%{$searchArea}%")
                      ->orWhere('nik', 'like', "%{$searchArea}%")
                      ->orWhere('applied_job', 'like', "%{$searchArea}%");
                });
            }

            $areaCandidates = $areaCandidatesQuery->orderBy('id', 'desc')->paginate(15, ['*'], 'page_area');
            self::attachInhouseEmployeeNames($areaCandidates);
            $areaCandidates->getCollection()->transform(function ($c) use ($user, $salam) {
                $c->wa_url = $this->buildWaUrl($c, $user, $salam);
                return $c;
            });
        }

        $principles = Principle::where('is_active', true)->orderBy('name')->get();

        return view('interview.index', compact(
            'user',
            'salam',
            'isAdmin',
            'myCandidates',
            'areaCandidates',
            'principles',
            'searchMy',
            'searchArea',
            'allRecruiters',
            'filterUser',
            'displayRecruiterName',
            'displayRecruiterTitle',
            'displayRecruiterArea',
            'statTotal',
            'statProfileComplete',
            'statTestDone'
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
            'approverPrinsiple',
            'recruiter',
            'workExperiences',
            'interviewAssessment',
            'testResults',
            'principleApprovals'
        ])->findOrFail($id);

        $principles = Principle::where('is_active', true)->orderBy('name')->get();
        $userPrinsiples = self::getUserPrinsipleOptions($candidate);
        $areas = [
            'JAKARTA', 'SURABAYA', 'BANDUNG', 'SEMARANG', 'MEDAN', 
            'MAKASSAR', 'DENPARAS', 'PALEMBANG', 'BALIKPAPAN', 'YOGYAKARTA',
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

        $aiData = $candidate->ai_data;
        $otherCandidates = Candidate::where('applied_job', $candidate->applied_job)
            ->where('id', '!=', $candidate->id)
            ->whereNotNull('ai_score')
            ->where('ai_score', '>', 0)
            ->orderByDesc('ai_score')
            ->limit(5)
            ->get();

        $evalData = \App\Services\CandidateEvaluationDataService::getEvaluationData($candidate);

        return view('interview.show', array_merge([
            'candidate' => $candidate,
            'user' => $user,
            'principles' => $principles,
            'userPrinsiples' => $userPrinsiples,
            'areas' => $areas,
            'aiData' => $aiData,
            'otherCandidates' => $otherCandidates,
        ], $evalData));
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
     * Simpan Referensi Cek (Sinkronisasi ke work_experiences dan tb_pengalaman)
     */
    public function storeRefcek(Request $request, $id)
    {
        $candidate = Candidate::findOrFail($id);

        $companyId = $request->input('company_id', $request->input('work_experience_id'));
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
        $performa = $request->input('performance_review', $request->input('performa', $request->input('performance', 'Baik')));
        $disiplin = $request->input('discipline_review', $request->input('disiplin', $request->input('discipline', 'Tepat Waktu')));
        $tanggungJawab = $request->input('responsibility_review', $request->input('tanggung_jawab', $request->input('responsibility', 'Bertanggung Jawab')));
        $strengths = $request->input('strengths_identified', $request->input('strengthness', $request->input('strengths', '')));
        $weaknesses = $request->input('weaknesses_identified', $request->input('weekness', $request->input('weaknesses', '')));
        $checkDate = $request->input('checked_date', $request->input('tgl', date('Y-m-d')));
        $problems = $request->input('problem_notes', $request->input('problem', $request->input('problems', '-')));
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
            // Abaikan
        }

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

        if (empty($candidate->tes_komputer)) {
            $candidate->updateQuietly(['tes_komputer' => '00:03:10']);
        }

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
        $asName = self::resolveCandidateAsName($candidate, $user);
        $pesan = "Halo {$candidate->full_name}\n\nNilai Matematika Kamu Belum Memuaskan. Silahkan Lakukan Test Ulang.\n\nAkses Melalui Link Berikut https://new.asystem.co.id/cbt/login\n\n_Terima Kasih_\n\n_Regards_\n{$asName}";

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
        self::attachInhouseEmployeeNames($candidates);

        return view('interview.walk', compact('candidates', 'user', 'startDate', 'endDate', 'search'));
    }

    /**
     * Halaman Done (Replikasi interviewdone.php)
     * Kriteria 1: Interview selesai jika kolom ttd_prinsiple sudah terisi
     */
    /**
     * Helper to summarize distinct recruiters for a given query
     */
    protected function buildRecruitersSummary($baseQuery): \Illuminate\Support\Collection
    {
        $allRecruiters = (clone $baseQuery)
            ->select('useras', DB::raw('count(*) as total'))
            ->whereNotNull('useras')
            ->where('useras', '!=', '')
            ->groupBy('useras')
            ->orderByDesc('total')
            ->take(50)
            ->get();

        $recruiterEmails = $allRecruiters->pluck('useras')->filter(fn($u) => str_contains($u, '@'))->map(fn($e) => strtolower(trim($e)))->unique()->values()->all();
        $employeeLookup = [];
        if (!empty($recruiterEmails)) {
            $emps = Employee::whereIn(DB::raw('LOWER(email)'), $recruiterEmails)
                ->where('status', 'Aktiv')
                ->get(['email', 'nama_karyawan', 'area']);
            foreach ($emps as $e) {
                $employeeLookup[strtolower(trim($e->email))] = $e;
            }
        }
        foreach ($allRecruiters as $r) {
            $lower = strtolower(trim($r->useras));
            if (isset($employeeLookup[$lower])) {
                $r->display_name = $employeeLookup[$lower]->nama_karyawan;
                $r->area = $employeeLookup[$lower]->area;
            } elseif (str_contains($r->useras, '@')) {
                $parts = explode('@', $r->useras)[0];
                $name = preg_replace('/[0-9_.-]+/', ' ', $parts);
                $r->display_name = ucwords(trim($name)) ?: $r->useras;
                $r->area = '';
            } else {
                $r->display_name = ucwords(strtolower($r->useras));
                $r->area = '';
            }
        }

        return $allRecruiters;
    }

    /**
     * Halaman Done (Replikasi table_done di dataint.php)
     * Kriteria 1: Kandidat aktif yang telah diterima/disetujui oleh prinsiple
     */
    public function done(Request $request)
    {
        $user = $this->getCurrentUser();
        $search = $request->query('search');
        $filterUser = $request->query('filter_user');

        $baseCondition = function ($q) {
            $q->whereNotIn('status', ['Arsip', 'archived'])
              ->where(function ($sq) {
                  $sq->where(function ($q2) {
                      $q2->whereNotNull('ttd_prinsiple')->where('ttd_prinsiple', '!=', '');
                  })->orWhere(function ($q2) {
                      $q2->whereNotNull('note_principle')->where('note_principle', '!=', '');
                  });
              });
        };

        $allRecruiters = $this->buildRecruitersSummary(DB::table('candidates')->where($baseCondition));

        $query = Candidate::with(['principle', 'recruiter', 'testResults'])
            ->where($baseCondition);

        $isAdmin = $user->isAdmin() || $user->role === 'admin';
        $userIdentifiers = KandidatPortalController::resolveUserIdentifiers($user);

        if ($isAdmin) {
            if (!empty($filterUser) && $filterUser !== 'all' && $filterUser !== 'my') {
                $query->where(function($q) use ($filterUser) {
                    $q->where('useras', $filterUser)
                      ->orWhereRaw('LOWER(TRIM(useras)) = ?', [strtolower(trim($filterUser))]);
                });
            }
            // Default Admin: Tampilkan semua data kandidat selesai nasional
        } else {
            $query->where(function($q) use ($user, $userIdentifiers) {
                if (!empty($userIdentifiers)) {
                    $q->whereIn(DB::raw('LOWER(TRIM(useras))'), $userIdentifiers);
                    $q->orWhere('useras', '');
                    $q->orWhereNull('useras');
                    if ($user && !empty($user->id)) $q->orWhere('recruiter_id', $user->id);
                } elseif ($user && !empty($user->id)) {
                    $q->where('recruiter_id', $user->id);
                } else {
                    $q->whereRaw('1 = 0');
                }
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $candidates = $query->orderBy('id', 'desc')->paginate(20);
        self::attachInhouseEmployeeNames($candidates);

        return view('interview.done', compact('candidates', 'user', 'search', 'allRecruiters', 'filterUser', 'isAdmin'));
    }

    /**
     * Halaman Arsip (Replikasi interviewarsip.php)
     * Kriteria 2: Arsip adalah data kandidat dengan status = Arsip / archived
     */
    public function arsip(Request $request)
    {
        $user = $this->getCurrentUser();
        $search = $request->query('search');
        $filterUser = $request->query('filter_user');

        $baseCondition = function ($q) {
            $q->where(function ($sq) {
                $sq->where('status', 'Arsip')
                   ->orWhere('status', 'archived')
                   ->orWhere('status_kandidat', 'Arsip');
            });
        };

        $allRecruiters = $this->buildRecruitersSummary(DB::table('candidates')->where($baseCondition));

        $query = Candidate::with(['principle', 'recruiter', 'testResults'])
            ->where($baseCondition);

        $isAdmin = $user->isAdmin() || $user->role === 'admin';
        $userIdentifiers = KandidatPortalController::resolveUserIdentifiers($user);

        if ($isAdmin) {
            if (!empty($filterUser) && $filterUser !== 'all' && $filterUser !== 'my') {
                $query->where(function($q) use ($filterUser) {
                    $q->where('useras', $filterUser)
                      ->orWhereRaw('LOWER(TRIM(useras)) = ?', [strtolower(trim($filterUser))]);
                });
            }
            // Default Admin: Tampilkan semua data kandidat arsip nasional
        } else {
            $query->where(function($q) use ($user, $userIdentifiers) {
                if (!empty($userIdentifiers)) {
                    $q->whereIn(DB::raw('LOWER(TRIM(useras))'), $userIdentifiers);
                    $q->orWhere('useras', '');
                    $q->orWhereNull('useras');
                    if ($user && !empty($user->id)) $q->orWhere('recruiter_id', $user->id);
                } elseif ($user && !empty($user->id)) {
                    $q->where('recruiter_id', $user->id);
                } else {
                    $q->whereRaw('1 = 0');
                }
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $candidates = $query->orderBy('id', 'desc')->paginate(20);
        self::attachInhouseEmployeeNames($candidates);

        return view('interview.arsip', compact('candidates', 'user', 'search', 'allRecruiters', 'filterUser', 'isAdmin'));
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
        $output = $pdfService->generate($candidate);

        if (str_starts_with($output, '%PDF-')) {
            $filename = 'Dokument Test Online ' . $candidate->full_name . '.pdf';
            return response($output, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $filename . '"',
                'Cache-Control' => 'private, max-age=0, must-revalidate',
                'Pragma' => 'public',
            ]);
        }

        return response($output, 200, [
            'Content-Type' => 'text/html; charset=utf-8',
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
        $userPrinsipleId = $request->input('userprinsiple');

        $approval = \App\Models\PrincipleApproval::firstOrNew(['candidate_id' => $candidate->id]);
        $approval->principle_id = $candidate->principle_id ?? 1;
        $approval->status = $status;
        $approval->notes = $notes;
        $approval->approved_at = now();

        if ($request->hasFile('approval_screenshot')) {
            $path = $request->file('approval_screenshot')->store('approvals', 'public');
            $approval->signature_path = $path;
            $candidate->ttd_prinsiple = $path;
        }
        $approval->save();

        if (!empty($userPrinsipleId)) {
            $candidate->idprinsiple = $userPrinsipleId;
        }
        $candidate->status_approval = $status === 'Approved' ? 'Approve' : 'Tolak';
        $candidate->note_principle = $notes;
        $candidate->time_prinsiple = now();
        $candidate->save();

        try {
            \Illuminate\Support\Facades\DB::table('tb_kandidat')->where('id', $candidate->id)->update([
                'idprinsiple' => $candidate->idprinsiple,
                'status_approve' => $candidate->status_approval,
                'note_prinsiple' => $notes,
                'ttd_prinsiple' => $candidate->ttd_prinsiple,
                'time_prinsiple' => $candidate->time_prinsiple,
            ]);
        } catch (\Exception $e) {}

        return redirect()->route('interview.show', $candidate->id)
            ->with('success', 'Status dan Bukti Approval User Principle berhasil disimpan!');
    }

    /**
     * Alihkan Kandidat ke Account Supervisor (AS) / Prinsiple
     */
    public function alihkanAS(Request $request, $id)
    {
        $candidate = Candidate::findOrFail($id);
        if ($request->filled('prinsiple_id')) {
            $candidate->principle_id = $request->prinsiple_id;
        }
        if ($request->filled('useras')) {
            $candidate->useras = $request->useras;
        }
        if ($request->filled('notes')) {
            $candidate->notes = $request->notes;
        }
        $candidate->status = 'Interview';
        $candidate->status_kandidat = 'Interview';
        $candidate->save();

        return redirect()->route('interview.show', $candidate->id)
            ->with('success', 'Data kandidat ' . $candidate->full_name . ' berhasil dialihkan.');
    }

    /**
     * Ganti Area Penempatan Kandidat
     */
    public function gantiArea(Request $request, $id)
    {
        $candidate = Candidate::findOrFail($id);
        $candidate->area = $request->area;
        $candidate->save();

        return redirect()->route('interview.show', $candidate->id)
            ->with('success', 'Area penempatan kandidat berhasil diubah menjadi: ' . $candidate->area);
    }

    /**
     * Bersihkan teks prinsiple dari entitas umum (PT, CV, Indonesia, TBK)
     */
    public static function cleanPrinsipleText(?string $text): string
    {
        $excluded_words = ['PT.', 'PT', 'Indonesia', 'TBK', 'CV'];
        $cleaned = strtolower($text ?? '');
        foreach ($excluded_words as $w) {
            $cleaned = str_replace(strtolower($w), '', $cleaned);
        }
        $cleaned = preg_replace('/[^a-z0-9]/', ' ', $cleaned);
        return trim(preg_replace('/\s+/', ' ', $cleaned));
    }

    /**
     * Ambil opsi Master User Prinsiple yang sesuai dengan prinsiple & area kandidat
     */
    public static function getUserPrinsipleOptions(Candidate $candidate)
    {
        $area = trim($candidate->area ?? '');
        $candPrinciple = $candidate->principle?->name ?? $candidate->principle_name ?? $candidate->prinsiple ?? '';
        $cleanCandPrinciple = self::cleanPrinsipleText($candPrinciple);

        $query = UserPrinsiple::query();
        if (!empty($area)) {
            $query->where(function ($q) use ($area) {
                $q->where('area', 'like', "%{$area}%")
                  ->orWhere('area', 'Nasional')
                  ->orWhereNull('area')
                  ->orWhere('area', '');
            });
        }

        $all = $query->orderBy('nama_lengkap')->get();
        $matched = collect();

        foreach ($all as $up) {
            $cleanUpPrinciple = self::cleanPrinsipleText($up->prinsiple);
            $percent = 0;
            if (!empty($cleanCandPrinciple) && !empty($cleanUpPrinciple)) {
                if (str_contains($cleanCandPrinciple, $cleanUpPrinciple) || str_contains($cleanUpPrinciple, $cleanCandPrinciple)) {
                    $percent = 100;
                } else {
                    similar_text($cleanCandPrinciple, $cleanUpPrinciple, $percent);
                }
            } elseif (empty($cleanCandPrinciple)) {
                $percent = 100;
            }

            if ($percent >= 50) {
                $matched->push($up);
            }
        }

        // Fallback without area filter if no match found
        if ($matched->isEmpty() && !empty($cleanCandPrinciple)) {
            $allNoArea = UserPrinsiple::orderBy('nama_lengkap')->get();
            foreach ($allNoArea as $up) {
                $cleanUpPrinciple = self::cleanPrinsipleText($up->prinsiple);
                $percent = 0;
                if (!empty($cleanUpPrinciple)) {
                    if (str_contains($cleanCandPrinciple, $cleanUpPrinciple) || str_contains($cleanUpPrinciple, $cleanCandPrinciple)) {
                        $percent = 100;
                    } else {
                        similar_text($cleanCandPrinciple, $cleanUpPrinciple, $percent);
                    }
                }
                if ($percent >= 50) {
                    $matched->push($up);
                }
            }
        }

        // Fallback if still empty: load user principles for candidate area / nasional
        if ($matched->isEmpty()) {
            $matched = UserPrinsiple::where(function ($q) use ($area) {
                if (!empty($area)) {
                    $q->where('area', 'like', "%{$area}%")->orWhere('area', 'Nasional');
                }
            })->orderBy('nama_lengkap')->get();
        }

        return $matched;
    }

    /**
     * Resolving batch nama karyawan inhouse dari field useras
     */
    public static function attachInhouseEmployeeNames($candidates)
    {
        $collection = $candidates instanceof \Illuminate\Pagination\LengthAwarePaginator 
            ? $candidates->getCollection() 
            : collect($candidates);

        $emails = $collection->pluck('useras')
            ->filter(fn($u) => !empty($u) && str_contains($u, '@'))
            ->map(fn($e) => strtolower(trim($e)))
            ->unique()
            ->values()
            ->all();

        $employeeMap = [];
        if (!empty($emails)) {
            $employees = Employee::whereIn(\Illuminate\Support\Facades\DB::raw('LOWER(email)'), $emails)
                ->where('status', 'Aktiv')
                ->get(['email', 'nama_karyawan', 'area', 'jabatan']);

            foreach ($employees as $emp) {
                $employeeMap[strtolower(trim($emp->email))] = $emp;
            }
        }

        $userMap = [];
        if (!empty($emails)) {
            $users = User::whereIn(\Illuminate\Support\Facades\DB::raw('LOWER(email)'), $emails)
                ->get(['email', 'name', 'area', 'job_title']);
            foreach ($users as $u) {
                $userMap[strtolower(trim($u->email))] = $u;
            }
        }

        $collection->transform(function ($c) use ($employeeMap, $userMap) {
            $useras = trim($c->useras ?? '');
            if (!empty($useras)) {
                $lowerEmail = strtolower($useras);
                if (isset($userMap[$lowerEmail])) {
                    $c->user_name_formatted = $userMap[$lowerEmail]->name;
                    $c->user_subtitle_formatted = ($userMap[$lowerEmail]->job_title ?? 'ARO') . ' ' . ($c->area ?? $userMap[$lowerEmail]->area ?? 'JAKARTA');
                } elseif (isset($employeeMap[$lowerEmail])) {
                    $c->user_name_formatted = $employeeMap[$lowerEmail]->nama_karyawan;
                    $c->user_subtitle_formatted = ($employeeMap[$lowerEmail]->jabatan ?? 'ARO') . ' ' . ($c->area ?? $employeeMap[$lowerEmail]->area ?? 'JAKARTA');
                } elseif (str_contains($useras, '@')) {
                    $parts = explode('@', $useras)[0];
                    $name = preg_replace('/[0-9_.-]+/', ' ', $parts);
                    $c->user_name_formatted = ucwords(trim($name)) ?: $useras;
                    $c->user_subtitle_formatted = 'ARO ' . ($c->area ?? 'JAKARTA');
                } else {
                    $c->user_name_formatted = ucwords(strtolower($useras));
                    $c->user_subtitle_formatted = 'ARO ' . ($c->area ?? 'JAKARTA');
                }
            } else {
                $c->user_name_formatted = $c->recruiter->name ?? 'Administrator HR';
                $c->user_subtitle_formatted = 'ARO ' . ($c->area ?? 'JAKARTA');
            }
            return $c;
        });

        return $candidates;
    }
}

