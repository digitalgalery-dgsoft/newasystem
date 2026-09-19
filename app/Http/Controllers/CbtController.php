<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use App\Models\Candidate;
use App\Models\CandidateLog;
use App\Models\WorkExperience;
use App\Models\TestResult;
use App\Services\CbtQuestionService;

class CbtController extends Controller
{
    // =========================================================================
    // 1. AUTENTIKASI KANDIDAT CBT
    // =========================================================================

    public function showLoginForm()
    {
        if (session()->has('cbt_candidate_id')) {
            return redirect()->route('cbt.dashboard');
        }

        return view('cbt.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'nik' => 'required|string',
            'password' => 'required|string',
        ]);

        $nik = trim($request->nik);
        $password = trim($request->password);

        $candidate = Candidate::where('nik', $nik)
            ->orderByRaw("CASE WHEN (status IS NULL OR status NOT IN ('Arsip', 'archived')) AND (jenis IS NULL OR jenis = '') THEN 0 ELSE 1 END")
            ->orderByDesc('id')
            ->first();

        if (!$candidate) {
            return back()->withInput()->with('error', 'NIK tidak terdaftar dalam database penerimaan kandidat.');
        }

        // Pengecekan jika kandidat sudah terikat/ditempatkan di prinsiple tertentu
        if (!empty($candidate->principle_id) && in_array($candidate->status, ['placed', 'approved_principle', 'Active_Employee'])) {
            return back()->withInput()->with('error', 'Akun Non Aktif untuk Test Online. Silahkan Hubungi AS Terkait.');
        }

        // Validasi Password
        $passwordMatches = false;

        // 1. Cek Hashed Password
        if (!empty($candidate->password) && Hash::check($password, $candidate->password)) {
            $passwordMatches = true;
        }

        // 2. Cek Default Tanggal Lahir (ddmmyyyy / dmY)
        if (!$passwordMatches && $candidate->birth_date) {
            $dobFormatted = $candidate->birth_date->format('dmY');
            $dobAlt = $candidate->birth_date->format('dmy');
            if ($password === $dobFormatted || $password === $dobAlt) {
                $passwordMatches = true;
                // Update password menjadi hash yang valid
                try {
                    $candidate->password = Hash::make($password);
                    $candidate->saveQuietly();
                } catch (\Throwable $e) {
                    // Abaikan jika database belum termigrasi
                }
            }
        }

        // 3. Fallback Password Default 'password' / '123456' untuk kemudahan pengujian
        if (!$passwordMatches && in_array($password, ['password', '123456', 'arina123'])) {
            $passwordMatches = true;
        }

        if (!$passwordMatches) {
            return back()->withInput()->with('error', 'Password yang Anda masukkan salah. Gunakan tanggal lahir format ddmmyyyy jika belum diubah.');
        }

        // Simpan Sesi Login Kandidat
        session([
            'cbt_candidate_id' => $candidate->id,
            'cbt_candidate_nik' => $candidate->nik,
            'cbt_candidate_name' => $candidate->full_name,
        ]);

        // Catat Log Aktivitas Login
        $this->logActivity($candidate, 'Login Portal Test Online CBT', $request);

        return redirect()->route('cbt.dashboard')->with('success', 'Login berhasil! Selamat datang di Portal CBT ESA Groups.');
    }

    public function logout(Request $request)
    {
        $candidateId = session('cbt_candidate_id');
        if ($candidateId) {
            $candidate = Candidate::find($candidateId);
            if ($candidate) {
                $this->logActivity($candidate, 'Logout dari Portal Test Online CBT', $request);
            }
        }

        session()->forget(['cbt_candidate_id', 'cbt_candidate_nik', 'cbt_candidate_name']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('cbt.login')->with('info', 'Anda telah berhasil keluar dari sistem CBT.');
    }

    // =========================================================================
    // 2. DASHBOARD CBT KANDIDAT
    // =========================================================================

    public function dashboard(Request $request)
    {
        $candidate = $this->getCurrentCandidate();
        $isProfileComplete = $candidate->checkProfileCompleteness();
        
        $logs = $candidate->candidateLogs()->take(10)->get();

        $psikotesResult = $candidate->testResults()->where('test_type', 'psychology')->latest()->first();
        $mathResult = $candidate->testResults()->where('test_type', 'math')->latest()->first();
        $komputerResult = $candidate->testResults()->where('test_type', 'computer')->latest()->first();

        return view('cbt.dashboard', compact(
            'candidate',
            'isProfileComplete',
            'logs',
            'psikotesResult',
            'mathResult',
            'komputerResult'
        ));
    }

    // =========================================================================
    // 3. KELENGKAPAN PROFIL KANDIDAT
    // =========================================================================

    public function profile()
    {
        $candidate = $this->getCurrentCandidate();
        $candidate->load('workExperiences');
        $isProfileComplete = $candidate->checkProfileCompleteness();

        return view('cbt.profile', compact('candidate', 'isProfileComplete'));
    }

    public function updateProfile(Request $request)
    {
        $candidate = $this->getCurrentCandidate();
        $tab = $request->input('tab', 'pribadi');

        // Pastikan direktori upload tersedia
        $lampiranPath = public_path('lampiran');
        if (!File::exists($lampiranPath)) {
            File::makeDirectory($lampiranPath, 0777, true, true);
        }

        if ($tab === 'pribadi') {
            $request->validate([
                'full_name' => 'required|string|max:255',
                'birth_place' => 'required|string|max:100',
                'birth_date' => 'required|date',
                'gender' => 'required|string',
                'religion' => 'required|string',
                'education' => 'required|string',
                'phone' => 'required|string|max:25',
                'address_ktp' => 'required|string',
                'address_domicile' => 'required|string',
                'height' => 'required|numeric',
                'weight' => 'required|numeric',
                'marital_status' => 'required|string',
            ]);

            $candidate->full_name = $request->full_name;
            $candidate->birth_place = $request->birth_place;
            $candidate->birth_date = $request->birth_date;
            $candidate->gender = $request->gender;
            $candidate->religion = $request->religion;
            $candidate->education = $request->education;
            $candidate->phone = $request->phone;
            $candidate->whatsapp = $request->phone;
            $candidate->address_ktp = $request->address_ktp;
            $candidate->address_domicile = $request->address_domicile;
            $candidate->height = $request->height;
            $candidate->weight = $request->weight;
            $candidate->marital_status = $request->marital_status;

            // Foto Profil (Upload File atau Base64 dari kamera web)
            if ($request->filled('fotoprofil_base64')) {
                $base64 = $request->fotoprofil_base64;
                if (preg_match('/^data:image\/(\w+);base64,/', $base64, $type)) {
                    $data = substr($base64, strpos($base64, ',') + 1);
                    $data = base64_decode($data);
                    $fileName = 'foto_' . $candidate->nik . '_' . time() . '.' . strtolower($type[1]);
                    File::put($lampiranPath . '/' . $fileName, $data);
                    $candidate->photo_path = $fileName;
                }
            } elseif ($request->hasFile('photo_file')) {
                $file = $request->file('photo_file');
                $fileName = 'foto_' . $candidate->nik . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move($lampiranPath, $fileName);
                $candidate->photo_path = $fileName;
            }

            // Upload CV Berkas
            if ($request->hasFile('cv_file')) {
                $file = $request->file('cv_file');
                $fileName = 'cv_' . $candidate->nik . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move($lampiranPath, $fileName);
                $candidate->cv_path = $fileName;
            }

            $candidate->save();
            $this->logActivity($candidate, 'Memperbarui Data Pribadi & Kontak', $request);

        } elseif ($tab === 'keluarga') {
            $candidate->spouse_name = $request->spouse_name;
            $candidate->spouse_job = $request->spouse_job;
            $candidate->children_count = $request->children_count ?? 0;
            $candidate->child_order = $request->child_order;
            $candidate->mother_name = $request->mother_name;
            $candidate->emergency_contact_name = $request->emergency_contact_name;
            $candidate->emergency_contact_phone = $request->emergency_contact_phone;
            $candidate->emergency_contact_relation = $request->emergency_contact_relation;

            $candidate->save();
            $this->logActivity($candidate, 'Memperbarui Data Keluarga & Kontak Darurat', $request);

        } elseif ($tab === 'keuangan') {
            $candidate->bank_name = $request->bank_name;
            $candidate->bank_account_number = $request->bank_account_number;
            $candidate->bank_account_holder = $request->bank_account_holder;
            $candidate->npwp = $request->npwp;
            $candidate->last_salary = $request->last_salary ?? 0;
            $candidate->expected_salary = $request->expected_salary ?? 0;

            $candidate->save();
            $this->logActivity($candidate, 'Memperbarui Data Keuangan & Rekening', $request);

        } elseif ($tab === 'tambahan') {
            $candidate->work_motivation = $request->work_motivation;
            $candidate->strengths = $request->strengths;
            $candidate->weaknesses = $request->weaknesses;
            $candidate->current_activity = $request->current_activity;
            $candidate->vehicle = $request->vehicle;
            $candidate->driving_license = $request->driving_license;
            $candidate->computer_skill = $request->computer_skill;
            $candidate->english_skill = $request->english_skill;
            $candidate->other_skills = $request->other_skills;
            $candidate->other_skills_level = $request->other_skills_level;

            $candidate->save();
            $this->logActivity($candidate, 'Memperbarui Data Tambahan & Keterampilan', $request);

        } elseif ($tab === 'ttd') {
            // Tanda tangan digital canvas (Base64 PNG)
            if ($request->filled('signature_base64')) {
                $base64 = $request->signature_base64;
                if (str_contains($base64, 'base64,')) {
                    $data = substr($base64, strpos($base64, ',') + 1);
                    $data = base64_decode($data);
                    $fileName = 'ttd_' . $candidate->nik . '_' . time() . '.png';
                    File::put($lampiranPath . '/' . $fileName, $data);
                    $candidate->signature_path = $fileName;
                }
            }

            if ($request->has('statement_agreed')) {
                $candidate->statement_agreed = true;
            }

            $candidate->save();
            $this->syncSignatureAcrossCandidates($candidate);
            $this->logActivity($candidate, 'Menyimpan Tanda Tangan Digital & Pernyataan Integritas', $request);
        }

        // Cek kembali kelengkapan profil
        $isComplete = $candidate->checkProfileCompleteness();

        return redirect()->route('cbt.profile', ['tab' => $tab])
            ->with('success', 'Data profil Anda berhasil disimpan.');
    }

    public function storeExperience(Request $request)
    {
        $candidate = $this->getCurrentCandidate();

        $request->validate([
            'company_name' => 'required|string|max:150',
            'position' => 'required|string|max:100',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'reason_for_leaving' => 'nullable|string',
        ]);

        WorkExperience::create([
            'candidate_id' => $candidate->id,
            'company_name' => $request->company_name,
            'position' => $request->position,
            'phone' => $request->phone ?? '-',
            'start_date' => $request->start_date ?? now()->subYear()->format('Y-m-d'),
            'end_date' => $request->end_date ?? now()->format('Y-m-d'),
            'reason_for_leaving' => $request->reason_for_leaving ?? '-',
        ]);

        $candidate->checkProfileCompleteness();
        $this->logActivity($candidate, 'Menambahkan Riwayat Pengalaman Kerja: ' . $request->company_name, $request);

        return redirect()->route('cbt.profile', ['tab' => 'pengalaman'])
            ->with('success', 'Pengalaman kerja berhasil ditambahkan.');
    }

    public function destroyExperience(Request $request, $id)
    {
        $candidate = $this->getCurrentCandidate();
        $experience = WorkExperience::where('candidate_id', $candidate->id)->where('id', $id)->firstOrFail();
        $company = $experience->company_name;
        $experience->delete();

        $candidate->checkProfileCompleteness();
        $this->logActivity($candidate, 'Menghapus Pengalaman Kerja: ' . $company, $request);

        return redirect()->route('cbt.profile', ['tab' => 'pengalaman'])
            ->with('success', 'Pengalaman kerja berhasil dihapus.');
    }

    // =========================================================================
    // 4. MODUL TES KEPRIBADIAN (DISC ASSESSMENT)
    // =========================================================================

    public function kepribadian(Request $request)
    {
        $candidate = $this->getCurrentCandidate();

        if (!$candidate->checkProfileCompleteness()) {
            return redirect()->route('cbt.dashboard')
                ->with('warning', 'Harap lengkapi profil Anda terlebih dahulu sebelum memulai Tes Kepribadian.');
        }

        $questions = CbtQuestionService::getPersonalityQuestions();

        $this->logActivity($candidate, 'Mulai Mengerjakan Tes Kepribadian', $request);

        return view('cbt.tests.kepribadian', compact('candidate', 'questions'));
    }

    public function submitKepribadian(Request $request)
    {
        $candidate = $this->getCurrentCandidate();

        $timeElapsed = intval($request->input('timeElapsed', 0));
        $formattedDuration = gmdate('H:i:s', $timeElapsed);

        $results = ['a' => 0, 'b' => 0, 'c' => 0, 'd' => 0];
        $answers = [];

        foreach ($request->all() as $key => $val) {
            if (preg_match('/^q\d+$/', $key)) {
                $val = strtolower($val);
                if (isset($results[$val])) {
                    $results[$val]++;
                }
                $answers[$key] = $val;
            }
        }

        // Tentukan Tipe Dominan (A: Melankolis, B: Sanguinis, C: Koleris, D: Plegmatis)
        arsort($results);
        $dominantCode = array_key_first($results);
        $traitMap = [
            'a' => 'Melankolis - Berbakat, Rapi & Analitis',
            'b' => 'Sanguinis - Ramah, Antusias & Komunikatif',
            'c' => 'Koleris - Tegas, Berani & Berorientasi Hasil',
            'd' => 'Plegmatis - Tenang, Damai & Sabar'
        ];

        $details = [
            'counts' => [
                'A' => $results['a'] ?? 0,
                'B' => $results['b'] ?? 0,
                'C' => $results['c'] ?? 0,
                'D' => $results['d'] ?? 0,
                'D_disc' => $results['c'] ?? 0, // Koleris
                'I_disc' => $results['b'] ?? 0, // Sanguinis
                'S_disc' => $results['d'] ?? 0, // Plegmatis
                'C_disc' => $results['a'] ?? 0, // Melankolis
            ],
            'dominant_code' => strtoupper($dominantCode),
            'dominant_trait' => $traitMap[$dominantCode] ?? 'Seimbang',
            'duration_formatted' => $formattedDuration,
            'answers' => $answers,
        ];

        // 1. Simpan ke TestResult
        TestResult::updateOrCreate(
            ['candidate_id' => $candidate->id, 'test_type' => 'psychology'],
            [
                'score' => 100.00,
                'duration_seconds' => $timeElapsed,
                'test_details' => $details,
            ]
        );

        // 2. Simpan butir 40 jawaban ke tb_hasilpsikotes jika tabel ada
        if (\Illuminate\Support\Facades\Schema::hasTable('tb_hasilpsikotes')) {
            try {
                \Illuminate\Support\Facades\DB::table('tb_hasilpsikotes')
                    ->where('id_kandidat', $candidate->id)
                    ->delete();

                $psikoRows = [];
                for ($i = 1; $i <= 40; $i++) {
                    $ans = $answers['q' . $i] ?? 'b';
                    $psikoRows[] = [
                        'id_kandidat' => $candidate->id,
                        'id_soal' => $i,
                        'jawaban' => strtolower($ans),
                        'waktu_pengerjaan' => $formattedDuration,
                        'created_at' => now(),
                    ];
                }
                \Illuminate\Support\Facades\DB::table('tb_hasilpsikotes')->insert($psikoRows);
            } catch (\Throwable $e) {
                // Abaikan jika terjadi kendala pada tabel legacy
            }
        }

        // 3. Update kandidat
        $candidate->tes_kepribadian = $formattedDuration;
        $candidate->saveQuietly();

        // 4. Update tabel legacy tb_kandidat jika ada
        if (\Illuminate\Support\Facades\Schema::hasTable('tb_kandidat')) {
            \Illuminate\Support\Facades\DB::table('tb_kandidat')
                ->where('id', $candidate->id)
                ->orWhere(function ($q) use ($candidate) {
                    if (!empty($candidate->nik)) {
                        $q->where('no_ktp', $candidate->nik);
                    }
                })
                ->update([
                    'tes_kepribadian' => $formattedDuration,
                ]);
        }

        $this->syncTestAcrossCandidates($candidate, 'psychology', $formattedDuration, $details);

        $this->logActivity($candidate, 'Selesai Mengerjakan Tes Kepribadian (' . $formattedDuration . ')', $request);

        return redirect()->route('cbt.kepribadian.result')
            ->with('success', 'Tes Kepribadian berhasil diselesaikan!');
    }

    public function kepribadianResult()
    {
        $candidate = $this->getCurrentCandidate();
        $testResult = $candidate->testResults()->where('test_type', 'psychology')->latest()->first();

        if (!$testResult) {
            return redirect()->route('cbt.dashboard')->with('warning', 'Anda belum mengerjakan Tes Kepribadian.');
        }

        $details = $testResult->test_details ?? [];

        return view('cbt.tests.kepribadian_result', compact('candidate', 'testResult', 'details'));
    }

    // =========================================================================
    // 5. MODUL TES MATEMATIKA (10 MENIT)
    // =========================================================================

    public function matematika(Request $request)
    {
        $candidate = $this->getCurrentCandidate();

        if (!$candidate->checkProfileCompleteness()) {
            return redirect()->route('cbt.dashboard')
                ->with('warning', 'Harap lengkapi profil Anda terlebih dahulu sebelum memulai Tes Matematika.');
        }

        $questions = CbtQuestionService::getMathQuestions();

        $this->logActivity($candidate, 'Mulai Mengerjakan Tes Matematika', $request);

        return view('cbt.tests.matematika', compact('candidate', 'questions'));
    }

    public function submitMatematika(Request $request)
    {
        $candidate = $this->getCurrentCandidate();

        $timeElapsed = intval($request->input('timeElapsed', 0));
        $formattedDuration = gmdate('H:i:s', $timeElapsed);
        $userAnswers = $request->input('answers', []);

        $questions = collect(CbtQuestionService::getMathQuestions())->keyBy('id');
        $correctCount = 0;
        $totalQuestions = $questions->count();
        $breakdown = [];

        foreach ($questions as $qId => $qData) {
            $userAns = isset($userAnswers[$qId]) ? trim($userAnswers[$qId]) : '';
            $correctAns = trim($qData['correct_answer']);

            $isCorrect = false;

            // 1. Direct case-insensitive match
            if (strtolower($userAns) === strtolower($correctAns)) {
                $isCorrect = true;
            }

            // 2. Alphanumeric normalization (e.g. 170.000 vs 170000, Rp 170.000 vs 170000)
            if (!$isCorrect) {
                $normUser = preg_replace('/[^0-9a-zA-Z]/', '', strtolower($userAns));
                $normKey = preg_replace('/[^0-9a-zA-Z]/', '', strtolower($correctAns));
                if ($normUser !== '' && $normUser === $normKey) {
                    $isCorrect = true;
                }
            }

            // 3. Decimal, comma, space, and percentage normalization (e.g. 71.43% vs 71.43, 8,4 vs 8.4 or 8, 4)
            if (!$isCorrect) {
                $cleanUser = trim(str_replace([' ', '%', '.'], ['', '', ','], strtolower($userAns)));
                $cleanKey = trim(str_replace([' ', '%', '.'], ['', '', ','], strtolower($correctAns)));
                if ($cleanUser !== '' && $cleanUser === $cleanKey) {
                    $isCorrect = true;
                }
            }

            if ($isCorrect) {
                $correctCount++;
            }

            $breakdown[$qId] = [
                'question_text' => $qData['question_text'],
                'user_answer' => $userAns,
                'correct_answer' => $correctAns,
                'is_correct' => $isCorrect,
                'explanation' => $qData['explanation'] ?? '',
            ];
        }

        $score = round(($correctCount / max(1, $totalQuestions)) * 100, 2);
        $currentTesKe = max(1, intval($candidate->tes_ke ?? 1));

        $details = [
            'score' => $score,
            'correct_count' => $correctCount,
            'total_questions' => $totalQuestions,
            'duration_formatted' => $formattedDuration,
            'tes_ke' => $currentTesKe,
            'breakdown' => $breakdown,
        ];

        // 1. Simpan ke TestResult
        TestResult::updateOrCreate(
            ['candidate_id' => $candidate->id, 'test_type' => 'math'],
            [
                'score' => $score,
                'duration_seconds' => $timeElapsed,
                'test_details' => $details,
            ]
        );

        // 2. Simpan butir jawaban ke tb_hasilmath jika tabel ada
        if (\Illuminate\Support\Facades\Schema::hasTable('tb_hasilmath')) {
            try {
                foreach ($userAnswers as $qId => $ans) {
                    \Illuminate\Support\Facades\DB::table('tb_hasilmath')->insert([
                        'id_kandidat' => $candidate->id,
                        'id_soal' => intval($qId),
                        'jawaban' => strval($ans),
                        'waktu_pengerjaan' => $formattedDuration,
                        'tes_ke' => $currentTesKe,
                        'created_at' => now(),
                    ]);
                }
            } catch (\Throwable $e) {
                // Abaikan jika ada kegagalan insert legacy
            }
        }

        // 3. Update candidate
        $candidate->tes_matematika = $formattedDuration;
        $candidate->tes_ke = $currentTesKe;
        $candidate->saveQuietly();

        // 4. Update tabel legacy tb_kandidat jika ada
        if (\Illuminate\Support\Facades\Schema::hasTable('tb_kandidat')) {
            \Illuminate\Support\Facades\DB::table('tb_kandidat')
                ->where('id', $candidate->id)
                ->orWhere(function ($q) use ($candidate) {
                    if (!empty($candidate->nik)) {
                        $q->where('no_ktp', $candidate->nik);
                    }
                })
                ->update([
                    'tes_matematika' => $formattedDuration,
                    'tes_ke' => $currentTesKe,
                ]);
        }

        $this->syncTestAcrossCandidates($candidate, 'math', $formattedDuration, $details);

        $this->logActivity($candidate, 'Selesai Mengerjakan Tes Matematika (Tes Ke - ' . $currentTesKe . ', Nilai: ' . $score . ')', $request);

        return redirect()->route('cbt.matematika.result')
            ->with('success', 'Tes Matematika berhasil diselesaikan!');
    }

    public function matematikaResult()
    {
        $candidate = $this->getCurrentCandidate();
        $testResult = $candidate->testResults()->where('test_type', 'math')->latest()->first();

        if (!$testResult) {
            return redirect()->route('cbt.dashboard')->with('warning', 'Anda belum mengerjakan Tes Matematika.');
        }

        $details = $testResult->test_details ?? [];

        return view('cbt.tests.matematika_result', compact('candidate', 'testResult', 'details'));
    }

    // =========================================================================
    // 6. MODUL TES KOMPUTER
    // =========================================================================

    public function komputer(Request $request)
    {
        $candidate = $this->getCurrentCandidate();

        if (!$candidate->checkProfileCompleteness()) {
            return redirect()->route('cbt.dashboard')
                ->with('warning', 'Harap lengkapi profil Anda terlebih dahulu sebelum memulai Tes Komputer.');
        }

        $this->logActivity($candidate, 'Mulai Mengerjakan Tes Komputer', $request);

        return view('cbt.tests.komputer', compact('candidate'));
    }

    public function submitKomputer(Request $request)
    {
        $candidate = $this->getCurrentCandidate();

        $request->validate([
            'bukti_file' => 'required|file|mimes:jpeg,jpg,png,pdf|max:5120',
            'elapsedTime' => 'required|numeric',
        ]);

        $timeElapsed = intval($request->elapsedTime);
        $formattedDuration = gmdate('H:i:s', $timeElapsed);

        $lampiranPath = public_path('lampiran');
        if (!File::exists($lampiranPath)) {
            File::makeDirectory($lampiranPath, 0777, true, true);
        }

        $file = $request->file('bukti_file');
        $fileName = 'bukti_kompt_' . $candidate->nik . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move($lampiranPath, $fileName);

        $details = [
            'file_name' => $fileName,
            'original_name' => $file->getClientOriginalName(),
            'duration_formatted' => $formattedDuration,
            'submitted_at' => now()->toDateTimeString(),
        ];

        TestResult::updateOrCreate(
            ['candidate_id' => $candidate->id, 'test_type' => 'computer'],
            [
                'score' => 100.00,
                'duration_seconds' => $timeElapsed,
                'test_details' => $details,
            ]
        );

        $candidate->tes_komputer = $formattedDuration;
        $candidate->buktikomputer = $fileName;
        $candidate->saveQuietly();
        $this->syncTestAcrossCandidates($candidate, 'computer', $formattedDuration, $details, $fileName);

        $this->logActivity($candidate, 'Selesai Mengerjakan Tes Komputer & Unggah Bukti (' . $formattedDuration . ')', $request);

        return redirect()->route('cbt.dashboard')
            ->with('success', 'Tes Komputer berhasil diselesaikan! Bukti pengerjaan telah berhasil dikirim.');
    }

    // =========================================================================
    // HELPER METHODS
    // =========================================================================

    private function getCurrentCandidate(): Candidate
    {
        $candidateId = session('cbt_candidate_id');
        return Candidate::findOrFail($candidateId);
    }

    private function logActivity(Candidate $candidate, string $activity, Request $request): void
    {
        try {
            CandidateLog::create([
                'candidate_id' => $candidate->id,
                'candidate_name' => $candidate->full_name,
                'activity' => $activity,
                'ip_address' => $request->ip(),
                'browser' => substr($request->userAgent() ?? '', 0, 100),
                'os' => substr($request->userAgent() ?? '', 0, 255),
            ]);
        } catch (\Exception $e) {
            // Abaikan kesalahan penulisan log agar tidak memutus alur
        }
    }

    private function syncTestAcrossCandidates(Candidate $candidate, string $testType, ?string $duration, array $details, ?string $file = null): void
    {
        if (empty($candidate->nik)) {
            return;
        }

        $otherCandidates = Candidate::where('nik', $candidate->nik)
            ->where('id', '!=', $candidate->id)
            ->get();

        foreach ($otherCandidates as $other) {
            if ($testType === 'psychology') {
                $other->tes_kepribadian = $duration;
                if (\Illuminate\Support\Facades\Schema::hasTable('tb_hasilpsikotes') && !empty($details['answers'])) {
                    try {
                        \Illuminate\Support\Facades\DB::table('tb_hasilpsikotes')
                            ->where('id_kandidat', $other->id)
                            ->delete();

                        $otherAnswers = $details['answers'];
                        $otherRows = [];
                        for ($i = 1; $i <= 40; $i++) {
                            $ans = $otherAnswers['q' . $i] ?? 'b';
                            $otherRows[] = [
                                'id_kandidat' => $other->id,
                                'id_soal' => $i,
                                'jawaban' => strtolower($ans),
                                'waktu_pengerjaan' => $duration ?? '00:04:20',
                                'created_at' => now(),
                            ];
                        }
                        \Illuminate\Support\Facades\DB::table('tb_hasilpsikotes')->insert($otherRows);
                    } catch (\Throwable $e) {
                        // Abaikan kegagalan replikasi
                    }
                }
            } elseif ($testType === 'math') {
                $other->tes_matematika = $duration;
                $other->tes_ke = $details['tes_ke'] ?? 1;
            } elseif ($testType === 'computer') {
                $other->tes_komputer = $duration;
                if ($file) {
                    $other->buktikomputer = $file;
                }
            }
            $other->saveQuietly();

            TestResult::updateOrCreate(
                ['candidate_id' => $other->id, 'test_type' => $testType],
                [
                    'score' => $details['score'] ?? 100.00,
                    'duration_seconds' => $details['duration_seconds'] ?? ($details['duration'] ?? 0),
                    'test_details' => $details,
                ]
            );

            $other->checkProfileCompleteness();
        }
    }

    private function syncSignatureAcrossCandidates(Candidate $candidate): void
    {
        if (empty($candidate->nik) || empty($candidate->signature_path)) {
            return;
        }

        Candidate::where('nik', $candidate->nik)
            ->where('id', '!=', $candidate->id)
            ->where(function ($q) {
                $q->whereNull('signature_path')->orWhere('signature_path', '');
            })
            ->update([
                'signature_path' => $candidate->signature_path,
                'statement_agreed' => true,
            ]);
    }
}
