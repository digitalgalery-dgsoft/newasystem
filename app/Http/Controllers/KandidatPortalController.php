<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Employee;
use App\Models\Candidate;
use App\Models\Principle;
use App\Models\InterviewAssessment;
use App\Models\WorkExperience;
use App\Services\AiAnalyzerService;
use App\Services\OdooRecruitmentSyncService;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
        return User::where('role', 'admin')->first()
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

            // Potongan nama jika lebih dari 1 kata
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
        $odooStage = $request->query('odoo_stage');

        $user = $this->getCurrentUser();
        $isAdmin = $user && ($user->role === 'admin' || (method_exists($user, 'isAdmin') && $user->isAdmin()));
        $canViewAllRecruiters = $isAdmin || ($user && method_exists($user, 'canViewAllCandidates') && $user->canViewAllCandidates());
        $userIdentifiers = $this->resolveUserIdentifiers($user);

        // Ambil daftar seluruh rekruter dari data kandidat portal (khusus untuk selector filter admin / user berhak semua scope)
        $allRecruiters = [];
        if ($canViewAllRecruiters) {
            $recQuery = DB::table('candidates')
                ->select('useras', DB::raw('count(*) as total'))
                ->where('jenis', 'Job Portal')
                ->whereNotNull('useras')
                ->where('useras', '!=', '');

            if ($user && !$isAdmin) {
                $user->applyRoleScopeToCandidates($recQuery);
            }

            $allRecruiters = $recQuery->groupBy('useras')
                ->orderByDesc('total')
                ->get();

            $recruiterEmails = $allRecruiters->pluck('useras')->filter(fn($u) => str_contains($u, '@'))->map(fn($e) => strtolower(trim($e)))->unique()->values()->all();
            $employeeLookup = [];
            if (!empty($recruiterEmails)) {
                $emps = Employee::whereIn(DB::raw('LOWER(TRIM(email))'), $recruiterEmails)
                    ->whereNotNull('nama_karyawan')
                    ->where('nama_karyawan', '!=', '')
                    ->orderByRaw("CASE WHEN status = 'Aktiv' THEN 0 ELSE 1 END")
                    ->get(['email', 'nama_karyawan', 'jabatan']);
                foreach ($emps as $e) {
                    $k = strtolower(trim($e->email));
                    $nama = trim($e->nama_karyawan);
                    $jab = trim($e->jabatan ?? '');
                    $employeeLookup[$k] = !empty($jab) ? "{$nama} ({$jab})" : $nama;
                }
            }

            foreach ($allRecruiters as $r) {
                $lower = strtolower(trim($r->useras));
                if (isset($employeeLookup[$lower])) {
                    $r->display_name = $employeeLookup[$lower];
                } elseif (str_contains($r->useras, '@')) {
                    $r->display_name = $r->useras;
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

        if ($canViewAllRecruiters) {
            if (!empty($filterRecruiter) && $filterRecruiter !== 'all' && $filterRecruiter !== 'my') {
                // Admin / All-scope memfilter rekruter terpilih
                $baseQuery->where(function ($q) use ($filterRecruiter) {
                    $q->where('useras', $filterRecruiter)
                      ->orWhereRaw('LOWER(TRIM(useras)) = ?', [strtolower(trim($filterRecruiter))]);
                });
                $displayUserName = $filterRecruiter;
                $scopeTitle = 'Rekruter: ' . $filterRecruiter;
            } elseif ($filterRecruiter === 'my') {
                $baseQuery->where(function ($q) use ($user, $userIdentifiers) {
                    if (!empty($userIdentifiers)) {
                        $q->whereIn(DB::raw('LOWER(TRIM(useras))'), $userIdentifiers);
                        if ($user && !empty($user->id)) {
                            $q->orWhere('recruiter_id', $user->id);
                        }
                    } elseif ($user && !empty($user->id)) {
                        $q->where('recruiter_id', $user->id);
                    }
                });
                $scopeTitle = 'Kandidat Milik Anda (' . $displayUserName . ')';
            } else {
                // Default Admin / All-Scope: Seluruh Lowongan (Nasional)
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

        // Terapkan Pembatasan Scope Role (Prinsiple & Area Cover)
        if ($user) {
            $user->applyRoleScopeToCandidates($baseQuery);
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

        // Statistik Ringkas Step Odoo
        $odooStatsRaw = (clone $baseQuery)->selectRaw("
            SUM(CASE WHEN odoo_stage_name = 'Data Pelamar' THEN 1 ELSE 0 END) as data_pelamar,
            SUM(CASE WHEN odoo_stage_name LIKE '%Interview%' THEN 1 ELSE 0 END) as interview,
            SUM(CASE WHEN odoo_stage_name = 'Principal' THEN 1 ELSE 0 END) as principal,
            SUM(CASE WHEN odoo_stage_name LIKE '%Learning%' THEN 1 ELSE 0 END) as elearning,
            SUM(CASE WHEN odoo_stage_name LIKE '%PKWT%' THEN 1 ELSE 0 END) as pkwt,
            SUM(CASE WHEN odoo_stage_name = 'Joined' THEN 1 ELSE 0 END) as joined,
            SUM(CASE WHEN odoo_stage_name IS NULL OR odoo_stage_name = '' THEN 1 ELSE 0 END) as belum_odoo,
            SUM(CASE WHEN odoo_stage_name IS NOT NULL AND odoo_stage_name != '' THEN 1 ELSE 0 END) as total_odoo
        ")->first();

        $odooStats = [
            'data_pelamar' => (int) ($odooStatsRaw->data_pelamar ?? 0),
            'interview'    => (int) ($odooStatsRaw->interview ?? 0),
            'principal'    => (int) ($odooStatsRaw->principal ?? 0),
            'elearning'    => (int) ($odooStatsRaw->elearning ?? 0),
            'pkwt'         => (int) ($odooStatsRaw->pkwt ?? 0),
            'joined'       => (int) ($odooStatsRaw->joined ?? 0),
            'belum_odoo'   => (int) ($odooStatsRaw->belum_odoo ?? 0),
            'total_odoo'   => (int) ($odooStatsRaw->total_odoo ?? 0),
        ];

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

        // Filter Step Odoo
        if (!empty($odooStage)) {
            if ($odooStage === 'none') {
                $tableQuery->where(function($q) {
                    $q->whereNull('odoo_stage_name')->orWhere('odoo_stage_name', '');
                });
            } elseif ($odooStage === 'matched') {
                $tableQuery->whereNotNull('odoo_stage_name')->where('odoo_stage_name', '!=', '');
            } elseif ($odooStage === 'interview') {
                $tableQuery->where('odoo_stage_name', 'like', '%Interview%');
            } elseif ($odooStage === 'elearning') {
                $tableQuery->where('odoo_stage_name', 'like', '%Learning%');
            } elseif ($odooStage === 'pkwt') {
                $tableQuery->where('odoo_stage_name', 'like', '%PKWT%');
            } else {
                $tableQuery->where('odoo_stage_name', $odooStage);
            }
        }

        // Filter Rentang Tanggal
        $parseDate = function ($d) {
            if (empty($d)) return null;
            $clean = str_replace('/', '-', trim($d));
            try {
                return Carbon::parse($clean);
            } catch (\Throwable $e) {
                return null;
            }
        };

        $startDate = $parseDate($start);
        $endDate = $parseDate($end);

        if ($startDate && $endDate) {
            $tableQuery->whereBetween('created_at', [
                $startDate->copy()->startOfDay(),
                $endDate->copy()->endOfDay()
            ]);
        } elseif ($startDate) {
            $tableQuery->where('created_at', '>=', $startDate->copy()->startOfDay());
        } elseif ($endDate) {
            $tableQuery->where('created_at', '<=', $endDate->copy()->endOfDay());
        }

        // Pencarian Teks
        if (!empty($search)) {
            $tableQuery->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('applied_job', 'like', "%{$search}%")
                  ->orWhere('area', 'like', "%{$search}%")
                  ->orWhere('odoo_stage_name', 'like', "%{$search}%");
            });
        }

        $candidates = $tableQuery->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        $distinctAreas = Candidate::where('jenis', 'Job Portal')
            ->whereNotNull('area')
            ->where('area', '!=', '')
            ->distinct()
            ->orderBy('area')
            ->pluck('area');

        $distinctOdooStages = Candidate::where('jenis', 'Job Portal')
            ->whereNotNull('odoo_stage_name')
            ->where('odoo_stage_name', '!=', '')
            ->distinct()
            ->orderBy('odoo_stage_name')
            ->pluck('odoo_stage_name');

        $aiLiveStatus = \App\Services\AiAnalyzerService::getLiveRunningStatus();

        return view('kandidatportal.index', compact(
            'candidates',
            'tab',
            'kategori',
            'odooStage',
            'distinctOdooStages',
            'start',
            'end',
            'search',
            'filterRecruiter',
            'displayUserName',
            'scopeTitle',
            'isAdmin',
            'canViewAllRecruiters',
            'allRecruiters',
            'distinctAreas',
            'totalPelamar',
            'masukHariIni',
            'kandidatGreen',
            'belumDianalisa',
            'countBaru',
            'countInterview',
            'countTerima',
            'countArsip',
            'odooStats',
            'aiLiveStatus'
        ));
    }

    /**
     * Endpoint API status live AI CV Analyzer untuk Running Text
     */
    public function aiLiveStatus()
    {
        return response()->json(\App\Services\AiAnalyzerService::getLiveRunningStatus());
    }

    /**
     * Helper untuk mengambil data log antrean dan riwayat analisa AI
     */
    protected function getAiQueueLogPayload(): array
    {
        $formatValidDate = function ($date, $fallbackDate = null, $format = 'd M Y, H:i') {
            $candidates = [$date, $fallbackDate];
            foreach ($candidates as $d) {
                if (empty($d)) {
                    continue;
                }
                $str = is_string($d) ? trim($d) : (string)$d;
                if (str_starts_with($str, '0000') || $str === '-' || $str === '0') {
                    continue;
                }
                try {
                    $carbon = $d instanceof \Carbon\Carbon ? $d : \Carbon\Carbon::parse($d);
                    if ($carbon->year > 1970) {
                        return $carbon->translatedFormat($format);
                    }
                } catch (\Throwable $e) {
                    // Abaikan kegagalan parse
                }
            }
            return '-';
        };

        $queueQuery = Candidate::whereRaw("LOWER(TRIM(jenis)) = 'job portal'")
            ->where(function ($q) {
                $q->whereNull('ai_score')->orWhere('ai_score', 0);
            });

        $queueCount = (clone $queueQuery)->count();

        $queueList = (clone $queueQuery)
            ->orderByRaw("CASE WHEN cv_path IS NOT NULL AND cv_path != '' AND cv_path != '-' THEN 0 ELSE 1 END, id ASC")
            ->limit(10)
            ->get(['id', 'full_name', 'applied_job', 'area', 'created_at', 'updated_at', 'cv_path', 'ai_score', 'kategori_kandidat', 'ai_cv_analysis'])
            ->map(function ($c, $idx) use ($formatValidDate) {
                return [
                    'id' => $c->id,
                    'queue_num' => $idx + 1,
                    'full_name' => $c->full_name,
                    'applied_job' => $c->applied_job ?? '-',
                    'area' => $c->area ?? 'JAKARTA',
                    'created_at_formatted' => $formatValidDate($c->created_at, $c->updated_at, 'd M Y, H:i'),
                    'has_cv' => $c->hasCv(),
                    'cv_name' => $c->cv_path ? basename($c->cv_path) : null,
                    'completed_at' => 'Dalam Antrean #' . ($idx + 1),
                    'detail_url' => route('kandidatportal.show', $c->id),
                ];
            });

        $completedQuery = Candidate::whereRaw("LOWER(TRIM(jenis)) = 'job portal'")
            ->whereNotNull('ai_score')
            ->where('ai_score', '>', 0);

        $completedCount = (clone $completedQuery)->count();
        $greenCount = (clone $completedQuery)->where('ai_score', '>=', 85)->count();
        $yellowCount = (clone $completedQuery)->whereBetween('ai_score', [60, 84])->count();
        $redCount = (clone $completedQuery)->where('ai_score', '<', 60)->count();

        $aiSetting = \App\Models\AiSetting::first();
        $defaultModel = $aiSetting?->gemini_model ?: 'gemini-2.5-flash';
        $lastCompletedCache = \Illuminate\Support\Facades\Cache::get('ai_analyzer_last_completed');

        $completedList = (clone $completedQuery)
            ->orderByDesc('updated_at')
            ->limit(10)
            ->get(['id', 'full_name', 'applied_job', 'area', 'ai_score', 'kategori_kandidat', 'ai_cv_analysis', 'created_at', 'updated_at', 'photo_path'])
            ->map(function ($c, $idx) use ($defaultModel, $lastCompletedCache, $formatValidDate) {
                $cat = $c->kategori_kandidat;
                if (empty($cat)) {
                    $score = intval($c->ai_score);
                    $cat = ($score >= 85) ? 'Green' : (($score >= 60) ? 'Yellow' : 'Red');
                }

                $modelName = null;
                $providerName = null;

                // 1. Coba dari JSON ai_cv_analysis
                if (!empty($c->ai_cv_analysis)) {
                    $parsed = json_decode($c->ai_cv_analysis, true);
                    if (is_array($parsed)) {
                        $modelName = $parsed['_model'] ?? $parsed['model'] ?? null;
                        $providerName = $parsed['_provider'] ?? $parsed['provider'] ?? null;
                    }
                }

                // 2. Coba dari cache last completed jika ID sama
                if (empty($modelName) && $lastCompletedCache && ($lastCompletedCache['candidate_id'] ?? null) == $c->id) {
                    $modelName = $lastCompletedCache['model'] ?? null;
                    $providerName = $lastCompletedCache['provider'] ?? null;
                }

                // 3. Fallback default model aktif
                if (empty($modelName)) {
                    $modelName = $defaultModel;
                    $providerName = 'Gemini';
                }

                $completedAtFormatted = '-';
                if ($c->updated_at && $c->updated_at->year > 1970) {
                    $completedAtFormatted = $c->updated_at->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i:s') . ' WIB';
                }

                return [
                    'id' => $c->id,
                    'num' => $idx + 1,
                    'full_name' => $c->full_name,
                    'applied_job' => $c->applied_job ?? '-',
                    'area' => $c->area ?? 'JAKARTA',
                    'created_at_formatted' => $formatValidDate($c->created_at, $c->updated_at, 'd M Y'),
                    'score' => intval($c->ai_score),
                    'category' => $cat,
                    'model' => $modelName,
                    'provider' => $providerName,
                    'completed_at' => $completedAtFormatted,
                    'detail_url' => route('kandidatportal.show', $c->id),
                ];
            });

        $liveStatus = \App\Services\AiAnalyzerService::getLiveRunningStatus();
        $processLogs = \App\Services\AiAnalyzerService::getTodayLogs(80);
        $logDate = now('Asia/Jakarta')->translatedFormat('d F Y');

        return [
            'queue_count' => $queueCount,
            'completed_count' => $completedCount,
            'green_count' => $greenCount,
            'yellow_count' => $yellowCount,
            'red_count' => $redCount,
            'queue_list' => $queueList,
            'completed_list' => $completedList,
            'process_logs' => $processLogs,
            'log_date' => $logDate,
            'live_status' => $liveStatus,
            'timestamp' => now('Asia/Jakarta')->format('H:i:s'),
        ];
    }

    /**
     * Halaman Log Antrean & Riwayat Hasil Analisa AI
     */
    public function aiQueueLog(Request $request)
    {
        $payload = $this->getAiQueueLogPayload();

        if ($request->wantsJson() || $request->query('format') === 'json') {
            return response()->json(array_merge(['success' => true], $payload));
        }

        return view('kandidatportal.ai_queue', array_merge([
            'user' => $this->getCurrentUser(),
        ], $payload));
    }

    /**
     * Endpoint API Data Log Antrean & Hasil Analisa AI untuk Auto-Reload Realtime
     */
    public function aiQueueData()
    {
        return response()->json(array_merge(['success' => true], $this->getAiQueueLogPayload()));
    }

    /**
     * Trigger analisa 1 kandidat terdepan secara langsung dari web UI
     */
    public function aiQueueTriggerProcess(Request $request)
    {
        $candidate = Candidate::whereRaw("LOWER(TRIM(jenis)) = 'job portal'")
            ->where(function ($q) {
                $q->whereNull('ai_score')->orWhere('ai_score', 0);
            })
            ->where(function ($q) {
                $q->whereNull('ai_cv_analysis')
                  ->orWhere('ai_cv_analysis', 'not like', '%file_error%');
            })
            ->whereNotNull('cv_path')
            ->where('cv_path', '!=', '')
            ->where('cv_path', '!=', '-')
            ->orderBy('id', 'asc')
            ->first();

        if (!$candidate) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada kandidat dalam antrean yang memiliki berkas CV valid untuk dianalisis.'
            ]);
        }

        $analyzer = app(\App\Services\AiAnalyzerService::class);
        $res = $analyzer->analyzeCandidate($candidate);

        return response()->json([
            'success' => $res['success'] ?? false,
            'candidate_id' => $candidate->id,
            'candidate_name' => $candidate->full_name,
            'score' => $res['score'] ?? null,
            'category' => $res['category'] ?? null,
            'message' => $res['message'] ?? 'Proses analisa selesai.',
            'error_type' => $res['error_type'] ?? null,
        ]);
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
        try {
            $areas = DB::table('tb_area')->orderBy('area')->pluck('area')->toArray();
        } catch (\Throwable $e) {
            $areas = [];
        }
        if (empty($areas)) {
            $areas = array_column(\App\Models\TbArea::getOfficialAreas(), 'area');
            sort($areas);
        }

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

        $user = $this->getCurrentUser();

        $asRecruiterOptions = \App\Http\Controllers\InterviewController::getAsRecruiterOptions();

        return view('kandidatportal.show', array_merge([
            'candidate' => $candidate,
            'user' => $user,
            'principles' => $principles,
            'userPrinsiples' => $userPrinsiples,
            'areas' => $areas,
            'asRecruiterOptions' => $asRecruiterOptions,
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

        if (!$candidate->hasCv() || empty($candidate->ai_score)) {
            return back()->with('error', 'Kandidat ' . $candidate->full_name . ' belum memiliki berkas CV atau belum dianalisis oleh AI. Unggah berkas CV terlebih dahulu.');
        }

        $pdfContent = $pdfService->generate($candidate);
        $filename = 'AI_Analysis_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $candidate->full_name) . '.pdf';

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    /**
     * Unggah / Perbarui Berkas Foto Profil & CV oleh Admin / Rekruter
     */
    public function uploadAttachments(Request $request, $id)
    {
        $candidate = Candidate::findOrFail($id);

        $request->validate([
            'foto_profil' => 'nullable|file|mimes:jpeg,png,jpg,webp|max:5120',
            'file_cv' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:10240',
        ]);

        $lampiranPath = public_path('lampiran');
        if (!\Illuminate\Support\Facades\File::exists($lampiranPath)) {
            \Illuminate\Support\Facades\File::makeDirectory($lampiranPath, 0777, true, true);
        }

        $nik = $candidate->nik ?: time();
        $updatedFiles = [];
        $newCvUploaded = false;

        // 1. Upload Foto Profil
        if ($request->hasFile('foto_profil')) {
            $foto = $request->file('foto_profil');
            $fotoName = 'foto_' . $nik . '_' . time() . '.' . $foto->getClientOriginalExtension();
            $foto->move($lampiranPath, $fotoName);
            $candidate->photo_path = $fotoName;
            $updatedFiles[] = 'Foto Profil';
        }

        // 2. Upload Berkas CV
        if ($request->hasFile('file_cv')) {
            $cv = $request->file('file_cv');
            $cvName = 'cv_' . $nik . '_' . time() . '.' . $cv->getClientOriginalExtension();
            $cv->move($lampiranPath, $cvName);
            $candidate->cv_path = $cvName;
            $newCvUploaded = true;
            $updatedFiles[] = 'Berkas CV';
        }

        // 3. Jika CV baru diunggah dan belum ada Analisis AI, jalankan Analisis AI otomatis
        if ($newCvUploaded || ($candidate->hasCv() && empty($candidate->ai_score))) {
            $candidate->save();
            $analyzer = app(AiAnalyzerService::class);
            $res = $analyzer->analyzeCandidate($candidate);
            if ($res['success']) {
                $updatedFiles[] = 'Analisis AI CV (Skor Match: ' . $res['score'] . ')';
            } else {
                $updatedFiles[] = 'Berkas CV (Analisis AI antrean/tertunda)';
            }
        } else {
            $candidate->save();
        }

        // Sinkronkan ke tb_kandidat jika ada
        if (\Illuminate\Support\Facades\Schema::hasTable('tb_kandidat')) {
            $tbData = [];
            if (!empty($candidate->photo_path)) $tbData['fotoprofil'] = $candidate->photo_path;
            if (!empty($candidate->cv_path)) $tbData['filecv'] = $candidate->cv_path;
            if (!empty($tbData)) {
                \Illuminate\Support\Facades\DB::table('tb_kandidat')->where('no_ktp', $candidate->nik)->update($tbData);
            }
        }

        if (empty($updatedFiles)) {
            return back()->with('info', 'Tidak ada berkas baru yang dipilih untuk diunggah.');
        }

        return back()->with('success', 'Berhasil memperbarui: ' . implode(', ', $updatedFiles) . '.');
    }

    /**
     * Analisis Ulang CV Kandidat via AI (dengan Rotasi Cerdas & Fallback Sumopod)
     */
    public function analyzeCv($id)
    {
        $candidate = Candidate::findOrFail($id);

        if (!$candidate->hasCv()) {
            return back()->with('error', 'Kandidat ' . $candidate->full_name . ' belum memiliki berkas CV. Silakan unggah berkas CV terlebih dahulu.');
        }

        $analyzer = app(AiAnalyzerService::class);
        $res = $analyzer->analyzeCandidate($candidate);

        if ($res['success']) {
            return back()->with('success', 'Analisis AI berkas CV kandidat ' . $candidate->full_name . ' berhasil diselesaikan! Skor Match: ' . $res['score'] . ' (' . $res['category'] . ') via ' . $res['provider'] . '.');
        }

        if (($res['error_type'] ?? '') === 'rate_limited') {
            return back()->with('warning', 'Analisis AI tertunda: Seluruh API Key AI Gemini & Sumopod saat ini sedang limit/jeda kuota (2 menit). Sistem cron akan mengulang otomatis, atau silakan klik coba lagi dalam beberapa saat.');
        }

        return back()->with('error', 'Analisis AI belum berhasil: ' . ($res['message'] ?? 'Terjadi kendala API'));
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

        ActivityLogger::log('RESET_PASSWORD', 'Kandidat Portal', "Reset password akun kandidat: {$candidate->full_name} ({$candidate->nik})", $candidate);

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

        $user = $this->getCurrentUser();
        $userSigFile = 'signatures/user_' . $user->id . '.png';
        $sigData = $validated['assessor_signature'] ?? null;

        $savedSigPath = null;
        if (!empty($sigData)) {
            if (str_contains($sigData, 'base64')) {
                $imageData = explode(',', $sigData)[1];
                $decoded = base64_decode($imageData);
                \Illuminate\Support\Facades\Storage::disk('public')->put($userSigFile, $decoded);
                $user->update(['signature_path' => $userSigFile]);
                $savedSigPath = $userSigFile;
            } else {
                $savedSigPath = $sigData;
            }
        }

        // Update assessment
        InterviewAssessment::updateOrCreate(
            ['candidate_id' => $candidate->id],
            [
                'interviewer_id' => $user->id,
                'work_willingness' => $validated['work_willingness'],
                'appearance' => $validated['appearance'],
                'attitude' => $validated['attitude'],
                'comprehension' => $validated['comprehension'],
                'notes' => $validated['notes'] ?? null,
                'interview_date' => $validated['interview_date'],
                'interviewer_signature_path' => $savedSigPath,
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

        ActivityLogger::log('UPDATE', 'Kandidat Portal', "Memperbarui hasil interview kandidat {$candidate->full_name} (Status: {$candidate->status_kandidat})", $candidate, [
            'status_kandidat' => $candidate->status_kandidat,
            'kategori_kandidat' => $candidate->kategori_kandidat,
            'kategori_industri' => $candidate->kategori_industri,
        ]);

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

        $oldAs = $candidate->useras;
        $candidate->principle_id = $validated['prinsiple_id'] ?? $candidate->principle_id;
        $candidate->useras = $validated['useras'];
        if (!empty($validated['notes'])) {
            $candidate->notes = $validated['notes'];
        }
        $candidate->status_kandidat = 'Interview';
        $candidate->save();

        ActivityLogger::log('UPDATE', 'Kandidat Portal', "Mengalihkan AS kandidat {$candidate->full_name} dari '{$oldAs}' ke '{$validated['useras']}'", $candidate, [
            'old_useras' => $oldAs,
            'new_useras' => $validated['useras'],
            'principle_id' => $candidate->principle_id,
        ]);

        return redirect()->route('kandidatportal.show', $candidate->id)
            ->with('success', 'Data kandidat ' . $candidate->full_name . ' berhasil dialihkan ke AS: ' . $validated['useras']);
    }

    /**
     * Ganti Area & Prinsiple Penempatan Kandidat
     */
    public function gantiArea(Request $request, $id)
    {
        $candidate = Candidate::findOrFail($id);
        $oldArea = $candidate->area;
        $oldPrincipleName = $candidate->principle?->name ?? $candidate->principle ?? '-';

        $updates = [];
        if ($request->filled('area')) {
            $candidate->area = \App\Models\TbArea::getCanonicalAreaName(trim($request->area));
            $candidate->region = \App\Models\TbArea::resolveRegion($candidate->area);
            $updates['area'] = $candidate->area;
        }

        if ($request->filled('principle_id')) {
            $principle = Principle::find($request->principle_id);
            if ($principle) {
                $candidate->principle_id = $principle->id;
                $candidate->principle = $principle->name;
                $updates['principle'] = $principle->name;
            }
        }

        $candidate->save();

        if (Schema::hasTable('tb_kandidat')) {
            DB::table('tb_kandidat')
                ->where('no_ktp', $candidate->nik)
                ->update($updates);
        }

        $newPrincipleName = $candidate->principle?->name ?? $candidate->principle ?? '-';
        ActivityLogger::log('UPDATE', 'Kandidat Portal', "Mengubah area/prinsiple kandidat {$candidate->full_name}. Area: '{$oldArea}' -> '{$candidate->area}', Prinsiple: '{$oldPrincipleName}' -> '{$newPrincipleName}'", $candidate, [
            'old_area' => $oldArea,
            'new_area' => $candidate->area,
            'old_principle' => $oldPrincipleName,
            'new_principle' => $newPrincipleName,
        ]);

        return redirect()->back()
            ->with('success', 'Area dan Prinsiple kandidat ' . $candidate->full_name . ' berhasil diperbarui.');
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

        ActivityLogger::log('ARCHIVE', 'Kandidat Portal', "Mengarsipkan kandidat {$candidate->full_name}. Alasan: {$validated['alasan']}", $candidate, [
            'alasan' => $validated['alasan'],
        ]);

        return redirect()->route('kandidatportal.index', ['tab' => 'arsip'])
            ->with('warning', 'Kandidat ' . $candidate->full_name . ' berhasil diarsipkan.');
    }

    /**
     * Mengaktifkan kembali kandidat yang diarsipkan (Un-Archive)
     */
    public function unarchive(Request $request, $id)
    {
        $candidate = Candidate::findOrFail($id);

        $candidate->status = 'Active';
        $candidate->status_kandidat = 'Interview';
        $oldReason = $candidate->archive_reason;
        $candidate->archive_reason = null;
        $candidate->save();

        if (\Illuminate\Support\Facades\Schema::hasTable('tb_kandidat')) {
            $tbData = ['status' => 'Active'];
            if (\Illuminate\Support\Facades\Schema::hasColumn('tb_kandidat', 'status_kandidat')) {
                $tbData['status_kandidat'] = 'Interview';
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('tb_kandidat', 'archive_reason')) {
                $tbData['archive_reason'] = null;
            }
            \Illuminate\Support\Facades\DB::table('tb_kandidat')
                ->where('id', $candidate->id)
                ->orWhere('no_ktp', $candidate->nik)
                ->update($tbData);
        }

        ActivityLogger::log('UNARCHIVE', 'Kandidat Portal', "Mengaktifkan kembali kandidat {$candidate->full_name} ({$candidate->id}) dari arsip.", $candidate, [
            'alasan_arsip_sebelumnya' => $oldReason,
        ]);

        return redirect()->back()
            ->with('success', "Kandidat {$candidate->full_name} berhasil diaktifkan kembali!");
    }

    /**
     * Export Data Pelamar Job Portal ke XLSX Profesional (Filtered by User, Tanggal, Kategori, Status, dan Area)
     */
    public function exportExcel(Request $request)
    {
        $status_kandidat = $request->query('status_kandidat') ?? $request->query('tab');
        $kategori = $request->query('kategori');
        $area = $request->query('area');
        $start = $request->query('start');
        $end = $request->query('end');
        $search = $request->query('q') ?? $request->query('search');
        $filterRecruiter = $request->query('recruiter');

        $user = $this->getCurrentUser();
        $isAdmin = $user && ($user->role === 'admin' || (method_exists($user, 'isAdmin') && $user->isAdmin()));
        $canViewAllRecruiters = $isAdmin || ($user && method_exists($user, 'canViewAllCandidates') && $user->canViewAllCandidates());
        $userIdentifiers = $this->resolveUserIdentifiers($user);

        $baseQuery = Candidate::where('jenis', 'Job Portal');

        if ($canViewAllRecruiters) {
            if (!empty($filterRecruiter) && !in_array(strtolower($filterRecruiter), ['all', 'my', 'semua', ''])) {
                // Admin / All-scope memfilter rekruter terpilih
                $baseQuery->where(function ($q) use ($filterRecruiter) {
                    $q->where('useras', $filterRecruiter)
                      ->orWhereRaw('LOWER(TRIM(useras)) = ?', [strtolower(trim($filterRecruiter))]);
                });
            } elseif ($filterRecruiter === 'my') {
                $baseQuery->where(function ($q) use ($user, $userIdentifiers) {
                    if (!empty($userIdentifiers)) {
                        $q->whereIn(DB::raw('LOWER(TRIM(useras))'), $userIdentifiers);
                        if ($user && !empty($user->id)) {
                            $q->orWhere('recruiter_id', $user->id);
                        }
                    } elseif ($user && !empty($user->id)) {
                        $q->where('recruiter_id', $user->id);
                    }
                });
            }
            // Jika $filterRecruiter bernilai 'all' atau kosong -> Export semua kandidat sesuai scope
        } else {
            // User biasa / AS / Rekruter: Hanya kandidat miliknya
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

        // Terapkan Pembatasan Scope Role (Prinsiple & Area Cover)
        if ($user) {
            $user->applyRoleScopeToCandidates($baseQuery);
        }

        // Filter Status Kandidat
        if (!empty($status_kandidat) && !in_array(strtolower($status_kandidat), ['all', 'semua', ''])) {
            if (strcasecmp($status_kandidat, 'interview') === 0) {
                $baseQuery->whereNotIn('status', ['Arsip', 'archived'])
                    ->where(function ($q) {
                        $q->whereNull('ttd_prinsiple')->orWhere('ttd_prinsiple', '');
                    })
                    ->where('status_kandidat', 'Interview');
            } elseif (strcasecmp($status_kandidat, 'terima') === 0) {
                $baseQuery->whereNotIn('status', ['Arsip', 'archived'])
                    ->where(function ($q) {
                        $q->where(function ($sub) {
                            $sub->whereNotNull('ttd_prinsiple')->where('ttd_prinsiple', '!=', '');
                        })->orWhere('status_kandidat', 'Terima');
                    });
            } elseif (strcasecmp($status_kandidat, 'arsip') === 0) {
                $baseQuery->where(function ($q) {
                    $q->where('status', 'Arsip')
                      ->orWhere('status', 'archived')
                      ->orWhere('status_kandidat', 'Arsip');
                });
            } elseif (strcasecmp($status_kandidat, 'baru') === 0) {
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
        }

        // Filter Area Penempatan
        if (!empty($area) && !in_array(strtolower($area), ['all', 'semua', ''])) {
            $baseQuery->where('area', $area);
        }

        // Filter Kategori AI
        if (!empty($kategori) && !in_array(strtolower($kategori), ['all', 'semua', ''])) {
            if (in_array(strtolower($kategori), ['pending', 'belum', 'belum dianalisa'])) {
                $baseQuery->where(function ($q) {
                    $q->whereNull('ai_score')->orWhere('ai_score', 0);
                });
            } else {
                $baseQuery->where('kategori_kandidat', $kategori);
            }
        }

        // Filter Rentang Tanggal Daftar (Aman untuk format dd/mm/yyyy, dd-mm-yyyy, dan yyyy-mm-dd)
        $parseDate = function ($d) {
            if (empty($d)) return null;
            $clean = str_replace('/', '-', trim($d));
            try {
                return Carbon::parse($clean);
            } catch (\Throwable $e) {
                return null;
            }
        };

        $startDate = $parseDate($start);
        $endDate = $parseDate($end);

        if ($startDate && $endDate) {
            $baseQuery->whereBetween('created_at', [
                $startDate->copy()->startOfDay(),
                $endDate->copy()->endOfDay()
            ]);
        } elseif ($startDate) {
            $baseQuery->where('created_at', '>=', $startDate->copy()->startOfDay());
        } elseif ($endDate) {
            $baseQuery->where('created_at', '<=', $endDate->copy()->endOfDay());
        }

        // Filter Search Keyword
        if (!empty($search)) {
            $baseQuery->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('applied_job', 'like', "%{$search}%")
                  ->orWhere('area', 'like', "%{$search}%");
            });
        }

        $candidates = $baseQuery->with(['workExperiences'])
            ->orderBy('created_at', 'desc')
            ->get();

        $recruiterLabel = 'Semua Rekruter (Nasional)';
        if ($isAdmin || $canViewAllRecruiters) {
            if (!empty($filterRecruiter) && !in_array(strtolower($filterRecruiter), ['all', 'my', 'semua', ''])) {
                if (str_contains($filterRecruiter, '@')) {
                    $emp = Employee::whereRaw('LOWER(TRIM(email)) = ?', [strtolower(trim($filterRecruiter))])
                        ->whereNotNull('nama_karyawan')
                        ->where('nama_karyawan', '!=', '')
                        ->first(['nama_karyawan', 'jabatan']);
                    if ($emp) {
                        $nama = trim($emp->nama_karyawan);
                        $jab = trim($emp->jabatan ?? '');
                        $recruiterLabel = !empty($jab) ? "{$nama} ({$jab})" : $nama;
                    } else {
                        $recruiterLabel = $filterRecruiter;
                    }
                } else {
                    $recruiterLabel = $filterRecruiter;
                }
            }
        } else {
            $recruiterLabel = $user ? $user->name : 'User';
        }

        $baseUrl = request()->getSchemeAndHttpHost();
        if (empty($baseUrl) || str_contains($baseUrl, 'localhost') || str_contains($baseUrl, '127.0.0.1')) {
            $baseUrl = 'https://new.asystem.co.id';
        }

        $meta = [
            'start' => $startDate ? $startDate->format('Y-m-d') : null,
            'end' => $endDate ? $endDate->format('Y-m-d') : null,
            'kategori' => $kategori,
            'status_kandidat' => $status_kandidat,
            'area' => $area,
            'recruiter_name' => $recruiterLabel,
            'base_url' => $baseUrl,
        ];

        $filePath = \App\Services\CandidateXlsxExportService::generateXlsx($candidates, $meta);
        $fileName = 'Data_Kandidat_Job_Portal_' . date('Ymd_His') . '.xlsx';

        ActivityLogger::export('Kandidat Portal', "Mengekspor data pelamar job portal ke Excel (" . count($candidates) . " kandidat)", [
            'total_rows' => count($candidates),
            'status' => $status_kandidat,
            'kategori' => $kategori,
            'area' => $area,
            'recruiter' => $filterRecruiter,
            'start' => $startDate ? $startDate->format('Y-m-d') : null,
            'end' => $endDate ? $endDate->format('Y-m-d') : null,
        ]);

        return response()->download($filePath, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
            'Pragma' => 'public',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Sinkronisasi massal data kandidat portal dengan Odoo Recruitment ERP
     */
    public function syncOdooRecruitment(Request $request, OdooRecruitmentSyncService $syncService)
    {
        $limit = (int)($request->input('limit', 1000));
        $includeArchived = $request->boolean('all', false);

        $result = $syncService->syncAllCandidates(null, $limit, $includeArchived);

        ActivityLogger::sync('Odoo Recruitment', "Sinkronisasi massal kandidat portal dengan Odoo ERP (Cocok: {$result['matched']})", $result);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($result);
        }

        $msg = "Sinkronisasi Odoo berhasil! {$result['matched']} pelamar cocok di Odoo ({$result['moved_interview']} ke Interview, {$result['moved_terima']} ke Terima, {$result['moved_arsip']} ke Arsip). ";
        if ($result['auto_archived'] > 0) {
            $msg .= "{$result['auto_archived']} kandidat > 14 hari tanpa update otomatis dipindahkan ke Arsip.";
        }

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Sinkronisasi status Odoo untuk 1 kandidat spesifik
     */
    public function syncSingleOdoo(Request $request, $id, OdooRecruitmentSyncService $syncService)
    {
        $candidate = Candidate::findOrFail($id);
        $result = $syncService->syncSingleCandidate($candidate);

        ActivityLogger::sync('Odoo Recruitment', "Sinkronisasi status Odoo untuk kandidat: {$candidate->full_name} ({$candidate->nik})", $result, $candidate);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($result);
        }

        if ($result['found'] ?? false) {
            $msg = "Status Odoo berhasil diperbarui: [{$result['entity']}] {$result['odoo_stage']}. Status kandidat saat ini: {$result['status_kandidat']}.";
            return redirect()->back()->with('success', $msg);
        }

        return redirect()->back()->with('warning', $result['message'] ?? 'NIK kandidat tidak ditemukan di Odoo.');
    }
}