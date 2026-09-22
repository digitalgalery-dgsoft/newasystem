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
use App\Services\OdooRecruitmentSyncService;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class InterviewController extends Controller
{
    private function getCurrentUser()
    {
        if (auth()->check()) {
            return auth()->user();
        }

        return User::where('role', 'admin')->first()
            ?? User::first();
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

    public static function resolveCandidateAsDetails($candidate, $fallbackUser = null): array
    {
        $assess = $candidate->interviewAssessment ?? null;
        if ($assess && $assess->interviewer) {
            $u = $assess->interviewer;
            return [
                'name' => $u->name,
                'title' => $u->job_title ?: 'Area Supervisor',
                'area' => $u->area ?: ($candidate->area ?: 'Jakarta'),
                'signature_path' => $assess->interviewer_signature_path ?: $u->signature_path,
                'user' => $u,
            ];
        }

        if ($candidate->recruiter) {
            $u = $candidate->recruiter;
            return [
                'name' => $u->name,
                'title' => $u->job_title ?: 'Area Supervisor',
                'area' => $u->area ?: ($candidate->area ?: 'Jakarta'),
                'signature_path' => $assess?->interviewer_signature_path ?: $u->signature_path,
                'user' => $u,
            ];
        }

        $useras = trim($candidate->useras ?? '');
        if (!empty($useras)) {
            $lowerEmail = strtolower($useras);
            $u = User::whereRaw('LOWER(email) = ?', [$lowerEmail])->orWhere('name', $useras)->first();
            if ($u) {
                return [
                    'name' => $u->name,
                    'title' => $u->job_title ?: 'Area Supervisor',
                    'area' => $u->area ?: ($candidate->area ?: 'Jakarta'),
                    'signature_path' => $assess?->interviewer_signature_path ?: $u->signature_path,
                    'user' => $u,
                ];
            }

            $emp = Employee::whereRaw('LOWER(email) = ?', [$lowerEmail])->orWhere('nama_karyawan', $useras)->first();
            if ($emp) {
                return [
                    'name' => $emp->nama_karyawan,
                    'title' => $emp->jabatan ?: 'Area Supervisor',
                    'area' => $emp->penempatan ?: ($candidate->area ?: 'Jakarta'),
                    'signature_path' => $assess?->interviewer_signature_path,
                    'user' => null,
                ];
            }

            return [
                'name' => $candidate->user_display_name ?: $useras,
                'title' => 'Rekrutmen',
                'area' => $candidate->area ?: 'Jakarta',
                'signature_path' => $assess?->interviewer_signature_path,
                'user' => null,
            ];
        }

        if ($fallbackUser) {
            return [
                'name' => $fallbackUser->name,
                'title' => $fallbackUser->job_title ?: 'Area Supervisor',
                'area' => $fallbackUser->area ?: ($candidate->area ?: 'Jakarta'),
                'signature_path' => $assess?->interviewer_signature_path ?: $fallbackUser->signature_path,
                'user' => $fallbackUser,
            ];
        }

        return [
            'name' => 'Admin Rekrutmen',
            'title' => 'Rekrutmen',
            'area' => $candidate->area ?: 'Jakarta',
            'signature_path' => $assess?->interviewer_signature_path,
            'user' => null,
        ];
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
     * Halaman Utama: Replikasi interview.php dengan Navigasi Tabs (Interview, Selesai, Arsip)
     */
    public function index(Request $request)
    {
        $user = $this->getCurrentUser();
        $salam = $this->getSalam();
        $searchMy = $request->query('search_my', $request->query('search'));
        $searchArea = $request->query('search_area');
        $filterUser = $request->query('filter_user');
        $tab = $request->query('tab', 'interview');
        if (!in_array($tab, ['interview', 'done', 'arsip'])) {
            $tab = 'interview';
        }

        $isAdmin = $user && ($user->isAdmin() || $user->role === 'admin');
        $canViewAllRecruiters = $isAdmin || ($user && method_exists($user, 'canViewAllCandidates') && $user->canViewAllCandidates());
        $userIdentifiers = KandidatPortalController::resolveUserIdentifiers($user);
        $displayRecruiterName = $user ? $user->name : 'User';
        $displayRecruiterTitle = $user->job_title ?? 'REKRUTMEN';
        $displayRecruiterArea = $user->area ?? null;
        if (empty($displayRecruiterArea) && $user) {
            $emp = Employee::where('email', $user->email)->orWhere('nama_karyawan', $user->name)->first();
            if ($emp && !empty($emp->area)) {
                $displayRecruiterArea = $emp->area;
            }
        }
        if (empty($displayRecruiterArea)) {
            $displayRecruiterArea = 'JAKARTA';
        }

        // Ambil daftar rekruter dengan jumlah kandidat aktif (Hanya untuk Admin / All-Scope switcher)
        $allRecruiters = collect();
        if ($canViewAllRecruiters) {
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
        }

        // Hitung Tab Counters (Active, Done, Arsip)
        $countActiveQuery = Candidate::whereNotIn('status', ['Arsip', 'archived'])
            ->where(function ($q) {
                $q->whereNull('jenis')->orWhere('jenis', '');
            })
            ->where(function ($q) {
                $q->whereNull('ttd_prinsiple')->orWhere('ttd_prinsiple', '');
            });

        $countDoneQuery = Candidate::whereNotIn('status', ['Arsip', 'archived'])
            ->where(function ($sq) {
                $sq->where(function ($q2) {
                    $q2->whereNotNull('ttd_prinsiple')->where('ttd_prinsiple', '!=', '');
                })->orWhere(function ($q2) {
                    $q2->whereNotNull('note_principle')->where('note_principle', '!=', '');
                });
            });

        $countArsipQuery = Candidate::where(function ($sq) {
            $sq->where('status', 'Arsip')
               ->orWhere('status', 'archived')
               ->orWhere('status_kandidat', 'Arsip');
        });

        if ($canViewAllRecruiters) {
            if (!empty($filterUser) && $filterUser !== 'all' && $filterUser !== 'my') {
                $filterCond = function ($q) use ($filterUser) {
                    $q->where('useras', $filterUser)
                      ->orWhereRaw('LOWER(TRIM(useras)) = ?', [strtolower(trim($filterUser))]);
                };
                $countActiveQuery->where($filterCond);
                $countDoneQuery->where($filterCond);
                $countArsipQuery->where($filterCond);
            } elseif ($filterUser === 'my') {
                $myFilter = function ($q) use ($user, $userIdentifiers) {
                    if (!empty($userIdentifiers)) {
                        $q->whereIn(DB::raw('LOWER(TRIM(useras))'), $userIdentifiers);
                        if ($user && !empty($user->id)) $q->orWhere('recruiter_id', $user->id);
                    } elseif ($user && !empty($user->id)) {
                        $q->where('recruiter_id', $user->id);
                    }
                };
                $countActiveQuery->where($myFilter);
                $countDoneQuery->where($myFilter);
                $countArsipQuery->where($myFilter);
            }
        } else {
            $userFilter = function ($q) use ($user, $userIdentifiers) {
                if (!empty($userIdentifiers)) {
                    $q->whereIn(DB::raw('LOWER(TRIM(useras))'), $userIdentifiers);
                    if ($user && !empty($user->id)) $q->orWhere('recruiter_id', $user->id);
                } elseif ($user && !empty($user->id)) {
                    $q->where('recruiter_id', $user->id);
                } else {
                    $q->whereRaw('1 = 0');
                }
            };
            $countActiveQuery->where($userFilter);
            $countDoneQuery->where($userFilter);
            $countArsipQuery->where($userFilter);
        }

        if ($user) {
            $user->applyRoleScopeToCandidates($countActiveQuery);
            $user->applyRoleScopeToCandidates($countDoneQuery);
            $user->applyRoleScopeToCandidates($countArsipQuery);
        }

        $countActive = $countActiveQuery->count();
        $countDone = $countDoneQuery->count();
        $countArsip = $countArsipQuery->count();

        // Variabel inisialisasi untuk tiap tab
        $myCandidates = null;
        $areaCandidates = null;
        $doneCandidates = null;
        $arsipCandidates = null;
        $targetArea = null;
        $statTotal = 0;
        $statProfileComplete = 0;
        $statTestDone = 0;
        $odooStats = null;
        $distinctOdooStages = [];
        $odooStage = $request->query('odoo_stage');

        if ($tab === 'interview') {
            // Query 1: Data Kandidat Milik Anda / Rekruter Terpilih / Nasional (Tabel 1)
            $myCandidatesQuery = Candidate::with(['principle', 'recruiter', 'testResults'])
                ->whereNotIn('status', ['Arsip', 'archived'])
                ->where(function ($q) {
                    $q->whereNull('jenis')->orWhere('jenis', '');
                })
                ->where(function ($q) {
                    $q->whereNull('ttd_prinsiple')->orWhere('ttd_prinsiple', '');
                });

            $excludedRecruiterForArea = null;
            $excludedIdentifiersForArea = $userIdentifiers;

            if ($canViewAllRecruiters) {
                if (!empty($filterUser) && $filterUser !== 'all' && $filterUser !== 'my') {
                    // Filter rekruter terpilih dari selector
                    $myCandidatesQuery->where(function ($q) use ($filterUser) {
                        $q->where('useras', $filterUser)
                          ->orWhereRaw('LOWER(TRIM(useras)) = ?', [strtolower(trim($filterUser))]);
                    });
                    $foundRec = $allRecruiters->firstWhere('useras', $filterUser);
                    $displayRecruiterName = $foundRec ? $foundRec->display_name : $filterUser;
                    $displayRecruiterArea = $foundRec ? ($foundRec->area ?: ($user->area ?? 'JAKARTA')) : ($user->area ?? 'JAKARTA');
                    $displayRecruiterTitle = 'REKRUTER TERPILIH';

                    $excludedRecruiterForArea = $filterUser;
                    $excludedIdentifiersForArea = [strtolower(trim($filterUser))];
                    if ($foundRec && !empty($foundRec->display_name)) {
                        $excludedIdentifiersForArea[] = strtolower(trim($foundRec->display_name));
                    }
                } elseif ($filterUser === 'my') {
                    $myCandidatesQuery->where(function ($q) use ($user, $userIdentifiers) {
                        if (!empty($userIdentifiers)) {
                            $q->whereIn(DB::raw('LOWER(TRIM(useras))'), $userIdentifiers);
                            if ($user && !empty($user->id)) {
                                $q->orWhere('recruiter_id', $user->id);
                            }
                        } elseif ($user && !empty($user->id)) {
                            $q->where('recruiter_id', $user->id);
                        }
                    });
                    $displayRecruiterName = $user->name;
                    $displayRecruiterTitle = $user->job_title ?? 'REKRUTMEN';
                    $displayRecruiterArea = $user->area ?? 'JAKARTA';
                    $excludedIdentifiersForArea = $userIdentifiers;
                } else {
                    // Default Admin / All-Scope: Tampilkan semua data kandidat nasional langsung
                    $filterUser = 'all';
                    $displayRecruiterName = 'Semua Rekruter (Nasional)';
                    $displayRecruiterTitle = $isAdmin ? 'SUPER ADMIN - All' : 'ALL PRINCIPLE & AREA';
                    $displayRecruiterArea = 'NASIONAL';
                    $excludedIdentifiersForArea = $userIdentifiers;
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
                $excludedIdentifiersForArea = $userIdentifiers;
            }

            // Terapkan Pembatasan Scope Role (Prinsiple & Area Cover)
            if ($user) {
                $user->applyRoleScopeToCandidates($myCandidatesQuery);
            }

            // Hitung Statistik Ringkas Step Odoo ERP
            $odooBaseQuery = clone $myCandidatesQuery;
            $odooStatsRaw = (clone $odooBaseQuery)->selectRaw("
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

            $distinctOdooStages = (clone $odooBaseQuery)
                ->whereNotNull('odoo_stage_name')
                ->where('odoo_stage_name', '!=', '')
                ->distinct()
                ->pluck('odoo_stage_name')
                ->sort()
                ->values()
                ->all();

            // Terapkan Filter Step Odoo pada Query 1
            if (!empty($odooStage)) {
                if ($odooStage === 'none') {
                    $myCandidatesQuery->where(function($q) {
                        $q->whereNull('odoo_stage_name')->orWhere('odoo_stage_name', '');
                    });
                } elseif ($odooStage === 'matched') {
                    $myCandidatesQuery->whereNotNull('odoo_stage_name')->where('odoo_stage_name', '!=', '');
                } elseif ($odooStage === 'interview') {
                    $myCandidatesQuery->where('odoo_stage_name', 'like', '%Interview%');
                } elseif ($odooStage === 'elearning') {
                    $myCandidatesQuery->where('odoo_stage_name', 'like', '%Learning%');
                } elseif ($odooStage === 'pkwt') {
                    $myCandidatesQuery->where('odoo_stage_name', 'like', '%PKWT%');
                } else {
                    $myCandidatesQuery->where('odoo_stage_name', $odooStage);
                }
            }

            if ($searchMy) {
                $myCandidatesQuery->where(function ($q) use ($searchMy) {
                    $q->where('full_name', 'like', "%{$searchMy}%")
                      ->orWhere('nik', 'like', "%{$searchMy}%")
                      ->orWhere('applied_job', 'like', "%{$searchMy}%");
                });
            }

            // Hitung metrik ringkasan
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
            $myCandidates->getCollection()->transform(function ($c) use ($user, $salam) {
                $c->wa_url = $this->buildWaUrl($c, $user, $salam);
                return $c;
            });

            // =========================================================================
            // QUERY 2: DATA KANDIDAT REKAN SEAREA (TABEL 2)
            // Khusus Non-Admin: Administrator HANYA menampilkan 1 tabel saja (langsung nasional)!
            // =========================================================================
            if (!$isAdmin) {
                $targetArea = ($displayRecruiterArea && strtoupper($displayRecruiterArea) !== 'NASIONAL')
                    ? $displayRecruiterArea
                    : ($user->area ?? 'JAKARTA');

                if (empty($targetArea) || strtoupper($targetArea) === 'NASIONAL' || $targetArea === '-') {
                    $targetArea = 'JAKARTA';
                }

                $areaCandidatesQuery = Candidate::with(['principle', 'recruiter', 'testResults'])
                    ->whereNotIn('status', ['Arsip', 'archived'])
                    ->where(function ($q) {
                        $q->whereNull('jenis')->orWhere('jenis', '');
                    })
                    ->where(function ($q) {
                        $q->whereNull('ttd_prinsiple')->orWhere('ttd_prinsiple', '');
                    })
                    ->whereRaw('LOWER(TRIM(area)) = ?', [strtolower(trim($targetArea))])
                    ->where(function ($q) use ($excludedIdentifiersForArea) {
                        if (!empty($excludedIdentifiersForArea)) {
                            $q->whereNotIn(DB::raw('LOWER(TRIM(useras))'), $excludedIdentifiersForArea)
                              ->orWhereNull('useras');
                        }
                    });

                if ($user && !empty($user->id) && empty($excludedRecruiterForArea)) {
                    $areaCandidatesQuery->where(function ($q) use ($user) {
                        $q->where('recruiter_id', '!=', $user->id)->orWhereNull('recruiter_id');
                    });
                }

                // Terapkan Pembatasan Scope Role
                if ($user) {
                    $user->applyRoleScopeToCandidates($areaCandidatesQuery);
                }

                // Terapkan Filter Step Odoo pada Area Candidates
                if (!empty($odooStage)) {
                    if ($odooStage === 'none') {
                        $areaCandidatesQuery->where(function($q) {
                            $q->whereNull('odoo_stage_name')->orWhere('odoo_stage_name', '');
                        });
                    } elseif ($odooStage === 'matched') {
                        $areaCandidatesQuery->whereNotNull('odoo_stage_name')->where('odoo_stage_name', '!=', '');
                    } elseif ($odooStage === 'interview') {
                        $areaCandidatesQuery->where('odoo_stage_name', 'like', '%Interview%');
                    } elseif ($odooStage === 'elearning') {
                        $areaCandidatesQuery->where('odoo_stage_name', 'like', '%Learning%');
                    } elseif ($odooStage === 'pkwt') {
                        $areaCandidatesQuery->where('odoo_stage_name', 'like', '%PKWT%');
                    } else {
                        $areaCandidatesQuery->where('odoo_stage_name', $odooStage);
                    }
                }

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
            } else {
                $areaCandidates = null;
            }
        } elseif ($tab === 'done') {
            // TAB 2: INTERVIEW SELESAI
            $doneCandidatesQuery = Candidate::with(['principle', 'recruiter', 'testResults'])
                ->whereNotIn('status', ['Arsip', 'archived'])
                ->where(function ($sq) {
                    $sq->where(function ($q2) {
                        $q2->whereNotNull('ttd_prinsiple')->where('ttd_prinsiple', '!=', '');
                    })->orWhere(function ($q2) {
                        $q2->whereNotNull('note_principle')->where('note_principle', '!=', '');
                    });
                });

            if ($canViewAllRecruiters) {
                if (!empty($filterUser) && $filterUser !== 'all' && $filterUser !== 'my') {
                    $doneCandidatesQuery->where(function($q) use ($filterUser) {
                        $q->where('useras', $filterUser)
                          ->orWhereRaw('LOWER(TRIM(useras)) = ?', [strtolower(trim($filterUser))]);
                    });
                } elseif ($filterUser === 'my') {
                    $doneCandidatesQuery->where(function($q) use ($user, $userIdentifiers) {
                        if (!empty($userIdentifiers)) {
                            $q->whereIn(DB::raw('LOWER(TRIM(useras))'), $userIdentifiers);
                            if ($user && !empty($user->id)) $q->orWhere('recruiter_id', $user->id);
                        } elseif ($user && !empty($user->id)) {
                            $q->where('recruiter_id', $user->id);
                        }
                    });
                }
            } else {
                $doneCandidatesQuery->where(function($q) use ($user, $userIdentifiers) {
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

            if ($user) {
                $user->applyRoleScopeToCandidates($doneCandidatesQuery);
            }

            if ($searchMy) {
                $doneCandidatesQuery->where(function ($q) use ($searchMy) {
                    $q->where('full_name', 'like', "%{$searchMy}%")
                      ->orWhere('nik', 'like', "%{$searchMy}%")
                      ->orWhere('applied_job', 'like', "%{$searchMy}%");
                });
            }

            $doneCandidates = $doneCandidatesQuery->orderBy('id', 'desc')->paginate(20, ['*'], 'page_done');
            self::attachInhouseEmployeeNames($doneCandidates);
            $doneCandidates->getCollection()->transform(function ($c) use ($user, $salam) {
                $c->wa_url = $this->buildWaUrl($c, $user, $salam);
                return $c;
            });
        } elseif ($tab === 'arsip') {
            // TAB 3: ARSIP INTERVIEW
            $arsipCandidatesQuery = Candidate::with(['principle', 'recruiter', 'testResults'])
                ->where(function ($sq) {
                    $sq->where('status', 'Arsip')
                       ->orWhere('status', 'archived')
                       ->orWhere('status_kandidat', 'Arsip');
                });

            if ($canViewAllRecruiters) {
                if (!empty($filterUser) && $filterUser !== 'all' && $filterUser !== 'my') {
                    $arsipCandidatesQuery->where(function($q) use ($filterUser) {
                        $q->where('useras', $filterUser)
                          ->orWhereRaw('LOWER(TRIM(useras)) = ?', [strtolower(trim($filterUser))]);
                    });
                } elseif ($filterUser === 'my') {
                    $arsipCandidatesQuery->where(function($q) use ($user, $userIdentifiers) {
                        if (!empty($userIdentifiers)) {
                            $q->whereIn(DB::raw('LOWER(TRIM(useras))'), $userIdentifiers);
                            if ($user && !empty($user->id)) $q->orWhere('recruiter_id', $user->id);
                        } elseif ($user && !empty($user->id)) {
                            $q->where('recruiter_id', $user->id);
                        }
                    });
                }
            } else {
                $arsipCandidatesQuery->where(function($q) use ($user, $userIdentifiers) {
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

            if ($user) {
                $user->applyRoleScopeToCandidates($arsipCandidatesQuery);
            }

            if ($searchMy) {
                $arsipCandidatesQuery->where(function ($q) use ($searchMy) {
                    $q->where('full_name', 'like', "%{$searchMy}%")
                      ->orWhere('nik', 'like', "%{$searchMy}%")
                      ->orWhere('applied_job', 'like', "%{$searchMy}%");
                });
            }

            $arsipCandidates = $arsipCandidatesQuery->orderBy('id', 'desc')->paginate(20, ['*'], 'page_arsip');
            self::attachInhouseEmployeeNames($arsipCandidates);
            $arsipCandidates->getCollection()->transform(function ($c) use ($user, $salam) {
                $c->wa_url = $this->buildWaUrl($c, $user, $salam);
                return $c;
            });
        }

        $principles = Principle::where('is_active', true)->orderBy('name')->get();

        return view('interview.index', compact(
            'user',
            'salam',
            'isAdmin',
            'canViewAllRecruiters',
            'tab',
            'countActive',
            'countDone',
            'countArsip',
            'myCandidates',
            'areaCandidates',
            'doneCandidates',
            'arsipCandidates',
            'principles',
            'searchMy',
            'searchArea',
            'allRecruiters',
            'filterUser',
            'displayRecruiterName',
            'displayRecruiterTitle',
            'displayRecruiterArea',
            'targetArea',
            'statTotal',
            'statProfileComplete',
            'statTestDone',
            'odooStats',
            'distinctOdooStages',
            'odooStage'
        ));
    }

    /**
     * Sinkronisasi Tahapan Rekrutmen Odoo untuk Seluruh Kandidat Interview
     */
    public function syncOdoo(Request $request, OdooRecruitmentSyncService $syncService)
    {
        $limit = (int) $request->input('limit', 1000);
        $result = $syncService->syncAllCandidates(null, $limit, false, 'interview');

        if ($result['success']) {
            ActivityLogger::sync('Odoo Recruitment', "Sinkronisasi tahapan Odoo kandidat interview ({$result['matched']} cocok, {$result['moved_interview']} ke Interview, {$result['moved_terima']} ke Terima)", $result);
            $msg = "Sinkronisasi Odoo Kandidat Interview selesai! {$result['matched']} kandidat cocok di Odoo ({$result['moved_interview']} tahap Interview, {$result['moved_terima']} tahap Terima/Joined).";
            return back()->with('success', $msg);
        }

        return back()->with('error', 'Sinkronisasi Odoo gagal: ' . ($result['message'] ?? 'Error tidak diketahui'));
    }

    /**
     * Sinkronisasi Tahapan Rekrutmen Odoo untuk 1 Kandidat Interview Spesifik
     */
    public function syncSingleOdoo(Request $request, $id, OdooRecruitmentSyncService $syncService)
    {
        $candidate = Candidate::findOrFail($id);
        $result = $syncService->syncSingleCandidate($candidate);

        if ($result['success']) {
            $stageName = $result['stage_name'] ?? 'Belum terdaftar di Odoo';
            $entity = $result['entity'] ?? '-';
            ActivityLogger::sync('Odoo Recruitment', "Sinkronisasi status Odoo kandidat {$candidate->full_name} ({$candidate->nik}): {$stageName} ({$entity})", $result, $candidate);
            return back()->with('success', "Status Odoo {$candidate->full_name} diperbarui: {$stageName} (Entitas: {$entity})");
        }

        return back()->with('error', 'Pengecekan Odoo gagal: ' . ($result['message'] ?? 'Data tidak ditemukan di Odoo'));
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
            'principleApprovals',
            'inhouseApprovals'
        ])->findOrFail($id);

        $isInhouseCandidate = InterviewInhouseController::isCandidateInhouse($candidate);

        // Ambil daftar Approver Inhouse sesuai alur (Step 1: Rekrutor ke Head, Step 2: Head ke HRD)
        $inhouseApproverOptions = [];
        $currentApprovalStatus = $candidate->status_approval ?? 'Proses';
        $isStepHrd = in_array($currentApprovalStatus, ['Review HRD']);

        if ($isStepHrd) {
            // Step 2: Pilihan HRD Pusat
            $inhouseApproverOptions = [
                'Uriyanto - ADMIN HRD Jakarta',
                'Administrator HR - Head of Recruitment',
            ];
            $hrdUsers = User::where(function($q) {
                $q->where('role', 'admin')
                  ->orWhere('role', 'hrd')
                  ->orWhere('role', 'head_hr')
                  ->orWhere('job_title', 'like', '%HRD%')
                  ->orWhere('job_title', 'like', '%HR%');
            })->get();
            foreach ($hrdUsers as $hu) {
                $lbl = $hu->name . ' - ' . ($hu->job_title ?: 'HRD Pusat');
                if (!in_array($lbl, $inhouseApproverOptions)) {
                    $inhouseApproverOptions[] = $lbl;
                }
            }
        } else {
            // Step 1: Pilihan Head (Pimpinan User Rekrutor)
            // Cek pimpinan langsung user login
            $userPimpinan = null;
            if ($user) {
                $emp = Employee::where('email', $user->email)->orWhere('nama_karyawan', $user->name)->first();
                if ($emp && !empty($emp->pimpinan)) {
                    $userPimpinan = $emp->pimpinan;
                }
            }
            if (!empty($userPimpinan)) {
                $inhouseApproverOptions[] = $userPimpinan;
            }

            // Tambahkan daftar Head standar operasional
            $defaultHeads = [
                'Nurul Yuliastuti - Head HR',
                'David Oscar Sahala G Sibuea - OM',
                'Firmanto Setia Budi - AM',
                'Arief Denny Priambodo - RM',
                'Santy Christina Manurung - RM',
                'Rini Widia Anwar Suwarha - SAM',
                'Marinus Gulo - AM',
                'Administrator HR - Head of Recruitment'
            ];
            foreach ($defaultHeads as $dh) {
                if (!in_array($dh, $inhouseApproverOptions)) {
                    $inhouseApproverOptions[] = $dh;
                }
            }

            $headUsers = User::all()->filter(fn($u) => $u->isHead());
            foreach ($headUsers as $hu) {
                $lbl = $hu->name . ' - ' . ($hu->job_title ?: 'Head Approver');
                if (!in_array($lbl, $inhouseApproverOptions)) {
                    $inhouseApproverOptions[] = $lbl;
                }
            }
        }

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
            'isInhouseCandidate' => $isInhouseCandidate,
            'inhouseApproverOptions' => $inhouseApproverOptions,
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

        $assessment = InterviewAssessment::updateOrCreate(
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

        $sigData = $request->input("signature_data");
        $userSigFile = 'signatures/user_' . $user->id . '.png';

        if (!empty($sigData)) {
            if (str_contains($sigData, 'base64')) {
                $imageData = explode(',', $sigData)[1];
                $decoded = base64_decode($imageData);

                // Replace / timpa file tanda tangan permanen AS user (1 file per AS agar tidak menumpuk)
                \Illuminate\Support\Facades\Storage::disk('public')->put($userSigFile, $decoded);
                $user->update(['signature_path' => $userSigFile]);

                $assessment->update([
                    'interviewer_id' => $user->id,
                    'interviewer_signature_path' => $userSigFile,
                ]);
            } else {
                // Menggunakan TTD tersimpan
                $assessment->update([
                    'interviewer_id' => $user->id,
                    'interviewer_signature_path' => $sigData,
                ]);
            }
        } else {
            // Jika kosong (misal di-reset / belum bertanda tangan), biarkan kosong
            if ($request->has('signature_data')) {
                $assessment->update([
                    'interviewer_signature_path' => null,
                ]);
            }
        }

        ActivityLogger::log('UPDATE', 'Interview', "Menyimpan form assessment evaluasi interview untuk kandidat: {$candidate->full_name} ({$candidate->id})", $candidate, [
            'kemauan_kerja' => $km,
            'penampilan' => $pen,
            'attitude' => $att,
            'daya_tangkap' => $dt,
            'placement_area' => $area,
            'salary_offered' => $salary,
        ]);

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

        // 1. Hitung tes_ke berikutnya (naik 1 tingkat: misal dari 1 jadi 2, dst)
        $currentTesKe = max(1, intval($candidate->tes_ke ?? 1));
        $nextTesKe = $currentTesKe + 1;

        // 2. Reset status tes matematika & persetujuan pada kandidat
        $candidate->tes_matematika = null;
        $candidate->tes_ke = $nextTesKe;
        $candidate->idprinsiple = null;
        $candidate->ttd_prinsiple = null;
        $candidate->time_prinsiple = null;
        $candidate->status_approval = null;
        $candidate->save();

        // 3. Sinkronkan ke tabel legacy tb_kandidat jika ada
        if (\Illuminate\Support\Facades\Schema::hasTable('tb_kandidat')) {
            \Illuminate\Support\Facades\DB::table('tb_kandidat')
                ->where('id', $candidate->id)
                ->orWhere(function ($q) use ($candidate) {
                    if (!empty($candidate->nik)) {
                        $q->where('no_ktp', $candidate->nik);
                    }
                })
                ->update([
                    'tes_matematika' => null,
                    'tes_ke' => $nextTesKe,
                    'idprinsiple' => '',
                    'ttd_prinsiple' => null,
                ]);
        }

        // 4. Sinkronkan juga record kandidat lain dengan NIK yang sama jika ada
        if (!empty($candidate->nik)) {
            Candidate::where('nik', $candidate->nik)
                ->where('id', '!=', $candidate->id)
                ->update([
                    'tes_matematika' => null,
                    'tes_ke' => $nextTesKe,
                    'idprinsiple' => null,
                    'ttd_prinsiple' => null,
                    'time_prinsiple' => null,
                    'status_approval' => null,
                ]);
        }

        // 5. Hapus hasil TestResult math lama dari CBT agar kandidat dapat mengulang tes di CBT
        $allCandIds = Candidate::where('nik', $candidate->nik)->pluck('id')->push($candidate->id)->unique();
        TestResult::whereIn('candidate_id', $allCandIds)
            ->where('test_type', 'math')
            ->delete();

        $salam = $this->getSalam();
        $asName = self::resolveCandidateAsName($candidate, $user);
        $pesan = "Halo {$candidate->full_name}\n\nNilai Matematika Kamu Belum Memuaskan. Silahkan Lakukan Test Ulang (Tes Ke - {$nextTesKe}).\n\nAkses Melalui Link Berikut https://new.asystem.co.id/cbt/login\n\n_Terima Kasih_\n\n_Regards_\n{$asName}";

        $waUrl = "https://web.whatsapp.com/send?phone={$candidate->clean_whatsapp}&text=" . urlencode($pesan);

        ActivityLogger::log('REMIDI', 'Interview / CBT', "Set Remidi Tes Matematika (Ke-{$nextTesKe}) untuk kandidat: {$candidate->full_name} ({$candidate->id})", $candidate, [
            'tes_ke' => $nextTesKe,
            'whatsapp' => $candidate->clean_whatsapp,
        ]);

        return redirect()->back()
            ->with('success', "Remidi Berhasil Diset! Tes Matematika telah direset ke Tes Ke - {$nextTesKe}. Silakan hubungi kandidat untuk mengulang tes.")
            ->with('remidi_wa_url', $waUrl);
    }

    /**
     * Edit Prinsiple Kandidat
     */
    public function editPrinciple(Request $request, $id)
    {
        $candidate = Candidate::findOrFail($id);
        $request->validate(['principle_id' => 'required|exists:principles,id']);

        $oldPrincipleId = $candidate->principle_id;
        $candidate->update(['principle_id' => $request->principle_id]);

        ActivityLogger::log('UPDATE', 'Interview', "Memperbarui Prinsiple kandidat {$candidate->full_name} dari ID: {$oldPrincipleId} ke ID: {$request->principle_id}", $candidate, [
            'old_principle_id' => $oldPrincipleId,
            'new_principle_id' => $request->principle_id,
        ]);

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

        ActivityLogger::log('ARCHIVE', 'Interview', "Mengarsipkan kandidat {$candidate->full_name} ({$candidate->id}). Alasan: {$request->archive_reason}", $candidate, [
            'alasan' => $request->archive_reason,
        ]);

        return redirect()->route('interview.index')
            ->with('success', "Kandidat {$candidate->full_name} Berhasil Diarsipkan!");
    }

    /**
     * Mengaktifkan kembali kandidat yang diarsipkan (Un-Archive) ke daftar interview
     */
    public function unarchive(Request $request, $id)
    {
        $candidate = Candidate::findOrFail($id);

        $candidate->status = 'Active';
        if ($candidate->status_kandidat === 'Arsip') {
            $candidate->status_kandidat = 'Interview';
        }
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

        ActivityLogger::log('UNARCHIVE', 'Interview', "Mengaktifkan kembali kandidat {$candidate->full_name} ({$candidate->id}) dari arsip ke daftar interview aktif.", $candidate, [
            'alasan_arsip_sebelumnya' => $oldReason,
        ]);

        return redirect()->back()
            ->with('success', "Kandidat {$candidate->full_name} berhasil diaktifkan kembali ke daftar Interview!");
    }

    /**
     * Mengaktifkan kembali kandidat secara massal (Bulk Un-Archive)
     */
    public function bulkUnarchive(Request $request)
    {
        $ids = $request->input('candidate_ids', []);
        if (empty($ids) || !is_array($ids)) {
            return redirect()->back()->with('error', 'Silakan pilih minimal 1 kandidat yang ingin diaktifkan kembali.');
        }

        $candidates = Candidate::whereIn('id', $ids)->get();
        $count = 0;

        foreach ($candidates as $cand) {
            $cand->status = 'Active';
            if ($cand->status_kandidat === 'Arsip') {
                $cand->status_kandidat = 'Interview';
            }
            $cand->archive_reason = null;
            $cand->save();

            if (\Illuminate\Support\Facades\Schema::hasTable('tb_kandidat')) {
                $tbData = ['status' => 'Active'];
                if (\Illuminate\Support\Facades\Schema::hasColumn('tb_kandidat', 'status_kandidat')) {
                    $tbData['status_kandidat'] = 'Interview';
                }
                if (\Illuminate\Support\Facades\Schema::hasColumn('tb_kandidat', 'archive_reason')) {
                    $tbData['archive_reason'] = null;
                }
                \Illuminate\Support\Facades\DB::table('tb_kandidat')
                    ->where('id', $cand->id)
                    ->orWhere('no_ktp', $cand->nik)
                    ->update($tbData);
            }

            ActivityLogger::log('UNARCHIVE', 'Interview', "Mengaktifkan kembali kandidat {$cand->full_name} ({$cand->id}) dari arsip ke daftar interview via multi-select.", $cand);
            $count++;
        }

        return redirect()->back()
            ->with('success', "Berhasil mengaktifkan kembali {$count} kandidat ke daftar Interview!");
    }

    /**
     * Halaman Walk Interview (Replikasi walkinterview.php sesuai gambar sistem lama)
     */
    public function walkInterview(Request $request)
    {
        $user = $this->getCurrentUser();
        $isAdmin = $user && ($user->isAdmin() || $user->role === 'admin');
        $salam = $this->getSalam();

        $search = $request->query('search');
        $kategori = $request->query('kategori');
        $perPage = (int) $request->query('per_page', 10);
        if (!in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        $today = Carbon::now('Asia/Jakarta')->toDateString();

        // Default data yang tampil adalah data hari ini (tanggal berjalan) sesuai permintaan
        if ($request->has('start_date') || $request->has('end_date')) {
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date');
        } elseif ($request->query('all') == '1') {
            $startDate = null;
            $endDate = null;
        } else {
            $startDate = $today;
            $endDate = $today;
        }

        // Base Walkin Query (data kandidat dengan jenis 'Walkin' sesuai sistem lama)
        $baseWalkinQuery = Candidate::where('jenis', 'Walkin');

        if (!$isAdmin && $user) {
            $userIdentifiers = KandidatPortalController::resolveUserIdentifiers($user);
            $baseWalkinQuery->where(function ($q) use ($user, $userIdentifiers) {
                if (!empty($userIdentifiers)) {
                    $q->whereIn(DB::raw('LOWER(TRIM(useras))'), $userIdentifiers);
                    if (!empty($user->id)) {
                        $q->orWhere('recruiter_id', $user->id);
                    }
                } elseif (!empty($user->id)) {
                    $q->where('recruiter_id', $user->id);
                }
            });
            $user->applyRoleScopeToCandidates($baseWalkinQuery);
        }

        // Hitung Total All-Time per Kategori (untuk label kartu KPI: Total: X)
        $totalGreenAll = (clone $baseWalkinQuery)->where('kategori_kandidat', 'Green')->count();
        $totalYellowAll = (clone $baseWalkinQuery)->where('kategori_kandidat', 'Yellow')->count();
        $totalRedAll = (clone $baseWalkinQuery)->where('kategori_kandidat', 'Red')->count();
        $totalUncatAll = (clone $baseWalkinQuery)->where(function ($q) {
            $q->whereNull('kategori_kandidat')->orWhere('kategori_kandidat', '')->orWhere('kategori_kandidat', 'Uncategorized');
        })->count();

        // Terapkan Filter Tanggal
        $query = clone $baseWalkinQuery;
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);
        } elseif ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        } elseif ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        // Hitung Count pada Filter Tanggal Aktif (angka besar di kartu KPI)
        $dateFilteredBase = clone $query;
        $totalDateFiltered = (clone $dateFilteredBase)->count();
        $countGreenFiltered = (clone $dateFilteredBase)->where('kategori_kandidat', 'Green')->count();
        $countYellowFiltered = (clone $dateFilteredBase)->where('kategori_kandidat', 'Yellow')->count();
        $countRedFiltered = (clone $dateFilteredBase)->where('kategori_kandidat', 'Red')->count();
        $countUncatFiltered = (clone $dateFilteredBase)->where(function ($q) {
            $q->whereNull('kategori_kandidat')->orWhere('kategori_kandidat', '')->orWhere('kategori_kandidat', 'Uncategorized');
        })->count();

        $pctGreen = $totalDateFiltered > 0 ? round(($countGreenFiltered / $totalDateFiltered) * 100) : 0;
        $pctYellow = $totalDateFiltered > 0 ? round(($countYellowFiltered / $totalDateFiltered) * 100) : 0;
        $pctRed = $totalDateFiltered > 0 ? round(($countRedFiltered / $totalDateFiltered) * 100) : 0;

        // Terapkan Filter Kategori
        if (!empty($kategori) && $kategori !== 'Semua') {
            if ($kategori === 'Uncategorized') {
                $query->where(function ($q) {
                    $q->whereNull('kategori_kandidat')->orWhere('kategori_kandidat', '')->orWhere('kategori_kandidat', 'Uncategorized');
                });
            } else {
                $query->where('kategori_kandidat', $kategori);
            }
        }

        // Terapkan Filter Pencarian
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('applied_job', 'like', "%{$search}%")
                  ->orWhere('area', 'like', "%{$search}%")
                  ->orWhere('useras', 'like', "%{$search}%")
                  ->orWhere('info', 'like', "%{$search}%")
                  ->orWhere('undangan', 'like', "%{$search}%");
            });
        }

        $candidates = $query->orderBy('id', 'desc')->paginate($perPage);
        self::attachInhouseEmployeeNames($candidates);
        $candidates->getCollection()->transform(function ($c) use ($user, $salam) {
            $c->wa_url = $this->buildWaUrl($c, $user, $salam);
            return $c;
        });

        // Daftar Pilihan Dropdown untuk Form Registrasi Walkin
        $dropdownJobs = [
            'SPG/SPB', 'Beauty Advisor', 'MD', 'Administrasi', 'Team Leader',
            'Produksi', 'Sales', 'Promotor', 'Kasir', 'Helper', 'Driver', 'Store Supervisor'
        ];
        $dropdownAreas = [
            'Surabaya', 'Denpasar', 'Jakarta', 'Bandung', 'Malang', 'Banyuwangi',
            'Jember', 'Kediri', 'Madiun', 'Bojonegoro', 'TASIKMALAYA', 'Yogyakarta',
            'Semarang', 'Medan', 'Makassar'
        ];
        $dropdownInfo = [
            'WhatsApp', 'Teman', 'Teman / Relasi', 'Instagram', 'WA Group Lowker',
            'TikTok', 'Telegram', 'LinkedIn', 'Jobstreet', 'Website', 'Walk in Langsung'
        ];
        $dropdownUndangan = [
            'Walk Interview', 'WhatsApp', 'Email', 'Telepon', 'SMS'
        ];
        $dropdownEducation = [
            'SMA / SMK', 'SMA', 'SMK', 'D3', 'S1', 'S2', 'SMP', 'Lainnya'
        ];

        return view('interview.walk', compact(
            'candidates', 'user', 'isAdmin', 'startDate', 'endDate', 'search',
            'kategori', 'perPage', 'totalGreenAll', 'totalYellowAll', 'totalRedAll',
            'totalUncatAll', 'countGreenFiltered', 'countYellowFiltered', 'countRedFiltered',
            'countUncatFiltered', 'totalDateFiltered', 'pctGreen', 'pctYellow', 'pctRed',
            'dropdownJobs', 'dropdownAreas', 'dropdownInfo', 'dropdownUndangan', 'dropdownEducation'
        ));
    }

    /**
     * Helper to resolve AS / Rekrutor names for each area
     * Hanya karyawan inhouse yang jabatannya AS / AM / RM / Rekrutor / Rekrutmen
     */
    public static function getAsListByArea(): array
    {
        $areas = DB::table('tb_area')->orderBy('area')->pluck('area');
        $result = [];

        // Regex jabatan: hanya AS, AM, SAM, RM, ARO, Rekrutor, Rekrutmen, Recruiter, Recruitment
        $pattern = '/\b(AS|AM|SAM|RM|ARO|Rekrutor|Rekrutmen|Recruiter|Recruitment|Area Supervisor|Account Supervisor|Area Manager|Account Manager|Regional Manager)\b/i';

        // 1. Karyawan Inhouse Aktif dengan jabatan AS/AM/RM/Rekrutor/Rekrutmen
        $employees = Employee::where('tipe_karyawan', 'Inhouse')
            ->where(function($q) {
                $q->where('status', 'Aktiv')
                  ->orWhere('status', 'Aktif');
            })
            ->get(['nama_karyawan', 'jabatan', 'area'])
            ->filter(fn($e) => preg_match($pattern, $e->jabatan ?? ''));

        // 2. Users internal rekrutmen / inhouse dengan jabatan terkait rekrutmen/AS
        $users = User::whereIn('role', ['karyawan_inhouse', 'recruiter', 'admin'])
            ->get(['name', 'job_title', 'area', 'role'])
            ->filter(fn($u) => preg_match($pattern, ($u->job_title ?? '') . ' ' . ($u->role ?? '')));

        foreach ($areas as $a) {
            $areaLower = strtolower(trim($a));
            $matched = collect();

            // Match Karyawan Inhouse berdasarkan area atau teks jabatan (contoh: "AS OPS - Surabaya")
            foreach ($employees as $e) {
                $eArea = strtolower(trim($e->area ?? ''));
                $eJab = strtolower(trim($e->jabatan ?? ''));
                if (
                    strcasecmp($eArea, $areaLower) === 0 ||
                    str_contains($eArea, $areaLower) ||
                    str_contains($areaLower, $eArea) ||
                    str_contains($eJab, ' - ' . $areaLower)
                ) {
                    $cleanName = ucwords(strtolower(trim($e->nama_karyawan)));
                    $jabCore = trim(explode(' - ', $e->jabatan ?? '')[0]);
                    if (empty($jabCore)) $jabCore = 'AS OPS';

                    if (!empty($cleanName) && strlen($cleanName) > 2) {
                        $matched->push([
                            'name' => $cleanName,
                            'jabatan' => $jabCore,
                            'display' => $cleanName . ' (' . $jabCore . ')',
                        ]);
                    }
                }
            }

            // Match Users rekrutmen / AS se-area
            foreach ($users as $u) {
                $uArea = strtolower(trim($u->area ?? ''));
                $uJob = strtolower(trim($u->job_title ?? ''));
                if (
                    strcasecmp($uArea, $areaLower) === 0 ||
                    str_contains($uArea, $areaLower) ||
                    str_contains($areaLower, $uArea) ||
                    str_contains($uJob, ' - ' . $areaLower)
                ) {
                    $cleanName = ucwords(strtolower(trim($u->name)));
                    $jabCore = trim($u->job_title ?: ($u->role === 'recruiter' ? 'Recruiter' : 'Inhouse'));
                    if (!empty($cleanName) && strlen($cleanName) > 2) {
                        $matched->push([
                            'name' => $cleanName,
                            'jabatan' => $jabCore,
                            'display' => $cleanName . ' (' . $jabCore . ')',
                        ]);
                    }
                }
            }

            // Area khusus sub-wilayah Sidoarjo / Surabaya (seperti Buduran, Medaeng)
            if ($matched->isEmpty() && in_array($a, ['Buduran', 'Medaeng'])) {
                foreach ($employees as $e) {
                    $eArea = strtolower(trim($e->area ?? ''));
                    if (str_contains($eArea, 'surabaya')) {
                        $cleanName = ucwords(strtolower(trim($e->nama_karyawan)));
                        $jabCore = trim(explode(' - ', $e->jabatan ?? '')[0]);
                        if (empty($jabCore)) $jabCore = 'AS OPS';
                        if (!empty($cleanName) && strlen($cleanName) > 2) {
                            $matched->push([
                                'name' => $cleanName,
                                'jabatan' => $jabCore,
                                'display' => $cleanName . ' (' . $jabCore . ')',
                            ]);
                        }
                    }
                }
            }

            // Fallback jika tidak ada data lokal spesifik di area tersebut
            if ($matched->isEmpty()) {
                $matched->push([
                    'name' => 'ARO ' . strtoupper($a),
                    'jabatan' => 'ARO',
                    'display' => 'ARO ' . strtoupper($a) . ' (ARO)',
                ]);
            }

            $uniqueList = $matched
                ->filter(fn($item) => !empty($item['name']) && strlen($item['name']) > 2 && !in_array(strtolower($item['name']), ['publik', 'online', 'admin', '-']))
                ->unique('name')
                ->sortBy('name')
                ->values()
                ->all();

            $result[$a] = $uniqueList;
        }

        return $result;
    }

    /**
     * Halaman Form Registrasi Walkin Interview (Standalone)
     */
    public function createWalkInterview()
    {
        $areas = DB::table('tb_area')->orderBy('area')->get();
        $areaRegions = $areas->pluck('region', 'area')->toArray();

        $cities = DB::table('tb_kota')->orderBy('kota')->get();
        $citiesByRegion = $cities->groupBy('region')->map(fn($g) => $g->pluck('kota')->values())->toArray();

        $asListByArea = self::getAsListByArea();

        $dropdownJobs = [
            'SPG/SPB', 'Beauty Advisor', 'MD', 'Administrasi', 'Team Leader',
            'Produksi', 'Sales', 'Promotor', 'Kasir', 'Helper', 'Driver', 'Store Supervisor'
        ];
        $dropdownInfo = [
            'WhatsApp', 'Teman', 'Teman / Relasi', 'Instagram', 'WA Group Lowker',
            'TikTok', 'Telegram', 'LinkedIn', 'Jobstreet', 'Website', 'Walk in Langsung'
        ];
        $dropdownUndangan = [
            'Walk Interview', 'WhatsApp', 'Email', 'Telepon', 'SMS'
        ];
        $dropdownEducation = [
            'SMA / SMK', 'SMA', 'SMK', 'D3', 'S1', 'S2', 'SMP', 'Lainnya'
        ];

        return view('interview.walk_create', compact(
            'areas', 'areaRegions', 'cities', 'citiesByRegion', 'asListByArea',
            'dropdownJobs', 'dropdownInfo', 'dropdownUndangan', 'dropdownEducation'
        ));
    }

    /**
     * Simpan Data Formulir Pendaftaran Walkin Interview (Tersimpan sebagai jenis 'Walkin')
     */
    public function storeWalkInterview(Request $request)
    {
        $request->validate([
            'nik' => 'required|numeric|digits:16',
            'full_name' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'height' => 'nullable|numeric|min:50|max:250',
            'weight' => 'nullable|numeric|min:20|max:250',
            'address_ktp' => 'nullable|string|max:500',
            'address_domicile' => 'nullable|string|max:500',
            'whatsapp' => 'required|string|max:30',
            'education' => 'required|string',
            'applied_job' => 'required|string',
            'area' => 'required|string',
            'kota_asal' => 'nullable|string',
            'nama_as' => 'nullable|string',
            'work_motivation' => 'nullable|string|max:1000',
            'strengths' => 'nullable|string|max:1000',
            'info' => 'nullable|string',
            'undangan' => 'nullable|string',
            'foto_profil' => 'nullable|file|mimes:jpeg,png,jpg,webp|max:5120',
            'file_cv' => 'nullable|file|mimes:pdf,jpeg,png,jpg,webp|max:10240',
        ]);

        $authUser = auth()->user();
        $userAsName = $request->nama_as;
        if (empty($userAsName)) {
            if ($authUser) {
                $userAsName = $authUser->name;
            } else {
                $userAsName = 'ARO ' . strtoupper($request->area);
            }
        }
        $userEmail = $authUser ? $authUser->email : 'walkin@asystem.co.id';
        $recruiterId = $authUser ? $authUser->id : null;

        // Folder lampiran
        $lampiranPath = public_path('lampiran');
        if (!\Illuminate\Support\Facades\File::exists($lampiranPath)) {
            \Illuminate\Support\Facades\File::makeDirectory($lampiranPath, 0777, true, true);
        }

        $photoPath = null;
        if ($request->hasFile('foto_profil')) {
            $foto = $request->file('foto_profil');
            $fotoName = 'foto_' . $request->nik . '_' . time() . '.' . $foto->getClientOriginalExtension();
            $foto->move($lampiranPath, $fotoName);
            $photoPath = $fotoName;
        }

        $cvPath = null;
        if ($request->hasFile('file_cv')) {
            $cv = $request->file('file_cv');
            $cvName = 'cv_' . $request->nik . '_' . time() . '.' . $cv->getClientOriginalExtension();
            $cv->move($lampiranPath, $cvName);
            $cvPath = $cvName;
        }

        $candidatePayload = [
            'full_name' => trim($request->full_name),
            'birth_date' => $request->birth_date,
            'height' => $request->height,
            'weight' => $request->weight,
            'address_ktp' => $request->address_ktp,
            'address_domicile' => $request->address_domicile ?: $request->address_ktp,
            'phone' => $request->whatsapp,
            'whatsapp' => $request->whatsapp,
            'education' => $request->education,
            'applied_job' => $request->applied_job,
            'area' => $request->area,
            'city_domicile' => $request->kota_asal,
            'useras' => $userAsName,
            'work_motivation' => $request->work_motivation,
            'strengths' => $request->strengths,
            'info' => $request->info ?: 'Walk in Langsung',
            'info_lowongan' => $request->info ?: 'walk_in',
            'undangan' => $request->undangan ?: 'Walk Interview',
            'jenis' => 'Walkin', // Tersimpan eksplisit sebagai jenis 'Walkin'
            'source_type' => 'walk_in',
            'status' => 'Active',
            'status_kandidat' => 'Baru',
            'recruiter_id' => $recruiterId,
            'is_profile_complete' => true,
        ];

        if ($photoPath) {
            $candidatePayload['photo_path'] = $photoPath;
        }
        if ($cvPath) {
            $candidatePayload['cv_path'] = $cvPath;
        }

        $candidate = Candidate::updateOrCreate(
            ['nik' => $request->nik],
            $candidatePayload
        );

        // Sinkronkan ke tabel legacy tb_kandidat jika ada
        try {
            if (Schema::hasTable('tb_kandidat')) {
                $tbData = [
                    'tanggal' => now()->toDateString(),
                    'applicants_name' => $candidate->full_name,
                    'tanggal_lahir' => $candidate->birth_date ? Carbon::parse($candidate->birth_date)->format('Y-m-d') : null,
                    'height' => $candidate->height,
                    'weight' => $candidate->weight,
                    'alamat_ktp' => $candidate->address_ktp,
                    'alamat_domisili' => $candidate->address_domicile,
                    'phone' => $candidate->phone,
                    'mobile' => $candidate->whatsapp,
                    'pendidikan_terakhir' => $candidate->education,
                    'applied_job' => $candidate->applied_job,
                    'area' => $candidate->area,
                    'city_domicile' => $candidate->city_domicile,
                    'secondary_city' => $candidate->city_domicile,
                    'nama_as' => $userAsName,
                    'useras' => $userEmail,
                    'motivasi_kerja' => $candidate->work_motivation,
                    'kelebihan' => $candidate->strengths,
                    'info' => $candidate->info,
                    'undangan' => $candidate->undangan,
                    'jenis' => 'Walkin',
                    'status' => 'Active',
                    'status_kandidat' => 'Baru',
                ];

                if ($photoPath) {
                    $tbData['fotoprofil'] = $photoPath;
                }
                if ($cvPath) {
                    $tbData['filecv'] = $cvPath;
                }

                DB::table('tb_kandidat')->updateOrInsert(
                    ['no_ktp' => $candidate->nik],
                    $tbData
                );
            }
        } catch (\Throwable $e) {
            // Abaikan error sinkronisasi
        }

        ActivityLogger::log('CREATE', 'Walk Interview', "Pendaftaran kandidat walkin interview baru: {$candidate->full_name} ({$candidate->nik})", $candidate, [
            'nik' => $candidate->nik,
            'job' => $candidate->applied_job,
            'area' => $candidate->area,
            'jenis' => 'Walkin',
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Pendaftaran berhasil! Data Anda telah tersimpan sebagai kandidat Walk-in Interview.",
                'candidate' => $candidate
            ]);
        }

        if (!auth()->check()) {
            return redirect()->route('interview.walk.create')
                ->with('success', "Pendaftaran Berhasil! Terima kasih {$candidate->full_name}, data formulir Anda telah tersimpan. Silakan konfirmasi kehadiran Anda kepada petugas HRD / ARO di lokasi interview.");
        }

        return redirect()->route('interview.walk')
            ->with('success', "Kandidat Walkin Interview '{$candidate->full_name}' ({$candidate->nik}) berhasil didaftarkan dengan jenis Walkin!");
    }

    /**
     * Export Data Walkin Interview ke format CSV
     */
    public function exportWalkInterview(Request $request)
    {
        $search = $request->query('search');
        $kategori = $request->query('kategori');
        $today = Carbon::now('Asia/Jakarta')->toDateString();

        if ($request->has('start_date') || $request->has('end_date')) {
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date');
        } elseif ($request->query('all') == '1') {
            $startDate = null;
            $endDate = null;
        } else {
            $startDate = $today;
            $endDate = $today;
        }

        $query = Candidate::where('jenis', 'Walkin');

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);
        } elseif ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        } elseif ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        if (!empty($kategori) && $kategori !== 'Semua') {
            if ($kategori === 'Uncategorized') {
                $query->where(function ($q) {
                    $q->whereNull('kategori_kandidat')->orWhere('kategori_kandidat', '')->orWhere('kategori_kandidat', 'Uncategorized');
                });
            } else {
                $query->where('kategori_kandidat', $kategori);
            }
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('applied_job', 'like', "%{$search}%")
                  ->orWhere('area', 'like', "%{$search}%");
            });
        }

        $candidates = $query->orderBy('id', 'desc')->get();
        $fileName = 'kandidat_walkin_' . date('Ymd_His') . '.csv';

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$fileName}",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['NO', 'TANGGAL', 'NO KTP', 'NAMA KANDIDAT', 'TGL LAHIR', 'USIA', 'PENDIDIKAN', 'POSISI DILAMAR', 'AREA', 'REKRUTOR', 'INFO', 'INVITE BY', 'STATUS DATA'];

        $callback = function() use ($candidates, $columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8
            fputcsv($file, $columns);
            $no = 1;
            foreach ($candidates as $c) {
                fputcsv($file, [
                    $no++,
                    $c->created_at ? $c->created_at->format('d M Y') : '-',
                    "'" . $c->nik,
                    $c->full_name,
                    $c->formatted_birth_date,
                    $c->age . ' Tahun',
                    $c->education ?? '-',
                    $c->applied_job,
                    $c->area,
                    $c->user_name_formatted ?? $c->useras ?? '-',
                    $c->info ?? '-',
                    $c->undangan ?? 'Walk interview',
                    $c->is_profile_complete ? 'Lengkap' : 'Belum Lengkap',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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
     * Sekarang dialihkan ke tab 'done' pada interview.index
     */
    public function done(Request $request)
    {
        return redirect()->route('interview.index', array_merge(['tab' => 'done'], $request->all()));
    }

    /**
     * Halaman Arsip (Replikasi interviewarsip.php)
     * Sekarang dialihkan ke tab 'arsip' pada interview.index
     */
    public function arsip(Request $request)
    {
        return redirect()->route('interview.index', array_merge(['tab' => 'arsip'], $request->all()));
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
            'interviewAssessment.interviewer',
            'testResults',
            'principleApprovals'
        ])->findOrFail($id);

        $evalData = \App\Services\CandidateEvaluationDataService::getEvaluationData($candidate);
        if ($evalData['isUserPrinsipleDisabled'] ?? false) {
            $msg = 'Download Dokumen Dinonaktifkan: ' . implode(' | ', $evalData['userPrinsipleDisableReasons'] ?? ['Kandidat tidak memenuhi kriteria kelulusan.']);
            if (url()->previous() && url()->previous() !== url()->current()) {
                return redirect()->back()->with('error', $msg);
            }
            return redirect()->route('interview.show', $candidate->id)->with('error', $msg);
        }

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

        $evalData = \App\Services\CandidateEvaluationDataService::getEvaluationData($candidate);
        if ($evalData['isUserPrinsipleDisabled'] ?? false) {
            $reasonText = implode(' | ', $evalData['userPrinsipleDisableReasons'] ?? ['Kandidat tidak memenuhi kriteria kelulusan.']);
            return back()->with('error', 'Gagal: Tab User Principle dinonaktifkan untuk kandidat ini. ' . $reasonText);
        }

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

        ActivityLogger::log($status === 'Approved' ? 'APPROVE' : 'REJECT', 'Interview / Approval Prinsiple', "Approval Prinsiple untuk kandidat {$candidate->full_name} ({$candidate->id}): {$status}", $candidate, [
            'status' => $status,
            'notes' => $notes,
            'userprinsiple_id' => $userPrinsipleId,
        ]);

        return redirect()->route('interview.show', $candidate->id)
            ->with('success', 'Status dan Bukti Approval User Principle berhasil disimpan!');
    }

    /**
     * Simpan Pengajuan Approval Inhouse dari Rekrutor / AS (Set & Send Approval)
     */
    public function storeInhouseApproval(Request $request, $id)
    {
        $candidate = Candidate::findOrFail($id);
        $user = $this->getCurrentUser();

        if (in_array($candidate->status_approval, ['Review Head', 'Review HRD', 'Approve'])) {
            return back()->with('error', 'Pengajuan sedang dalam proses evaluasi (' . $candidate->status_approval . ') dan telah dikunci sesuai stepnya.');
        }

        $request->validate([
            'nama_approver' => 'required|string',
            'status_replace' => 'required|string',
            'berkas_lamaran' => 'nullable|file|mimes:pdf,doc,docx,jpeg,png,jpg|max:10240',
            'menggantikan' => 'nullable|string|max:255',
            'tgl_resign' => 'nullable|date',
            'alasan_resign' => 'nullable|string|max:1000',
        ]);

        $statusReplace = $request->status_replace === 'Baru' ? 'New' : $request->status_replace;

        // Upload berkas lamaran jika ada
        if ($request->hasFile('berkas_lamaran')) {
            $file = $request->file('berkas_lamaran');
            $filename = time() . '_' . preg_replace('/[^A-Za-z0-9_\.]/', '_', $file->getClientOriginalName());
            $destination = public_path('lampiran/berkas_lamaran');
            if (!\Illuminate\Support\Facades\File::exists($destination)) {
                \Illuminate\Support\Facades\File::makeDirectory($destination, 0777, true, true);
            }
            $file->move($destination, $filename);
            $candidate->berkas_lamaran = 'lampiran/berkas_lamaran/' . $filename;
        }

        $userRequest = null;
        if ($user) {
            $userTitle = $user->job_title ?: 'REKRUTMEN';
            $userArea = $user->area ?: 'Jakarta';
            $userRequest = "{$user->name} - {$userTitle} {$userArea}";
        }

        $candidate->update([
            'is_inhouse' => 1,
            'status_replace' => $statusReplace,
            'menggantikan' => $statusReplace === 'Replace' ? $request->menggantikan : null,
            'tgl_resign' => $statusReplace === 'Replace' ? $request->tgl_resign : null,
            'alasan_resign' => $statusReplace === 'Replace' ? $request->alasan_resign : null,
            'status_approval' => 'Review Head',
            'user_request' => $userRequest ?? $candidate->user_request,
        ]);

        // Sinkronisasi ke tb_replace jika ada
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('tb_replace')) {
                \Illuminate\Support\Facades\DB::table('tb_replace')->updateOrInsert(
                    ['id_kandidat' => $candidate->id],
                    [
                        'status' => $statusReplace,
                        'menggantikan' => $statusReplace === 'Replace' ? ($request->menggantikan ?? '') : '',
                        'tgl_resign' => $statusReplace === 'Replace' ? ($request->tgl_resign ?? '0000-00-00') : '0000-00-00',
                        'alasan_resign' => $statusReplace === 'Replace' ? ($request->alasan_resign ?? '') : '',
                    ]
                );
            }
        } catch (\Throwable $e) {}

        ActivityLogger::log('UPDATE', 'Interview / Inhouse Approval', "Pengajuan approval inhouse untuk kandidat {$candidate->full_name} ({$candidate->id}) dikirim ke {$request->nama_approver}", $candidate, [
            'status_replace' => $statusReplace,
            'nama_approver' => $request->nama_approver,
            'menggantikan' => $request->menggantikan,
        ]);

        return redirect()->route('interview.show', $candidate->id)
            ->with('success', "Pengajuan Approval Inhouse berhasil dikirim ke Approver: {$request->nama_approver}!");
    }

    /**
     * Alihkan Kandidat ke Account Supervisor (AS) / Prinsiple
     */
    public function alihkanAS(Request $request, $id)
    {
        $candidate = Candidate::findOrFail($id);
        $oldAs = $candidate->useras;
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

        ActivityLogger::log('UPDATE', 'Interview', "Mengalihkan AS kandidat {$candidate->full_name} dari '{$oldAs}' ke '{$candidate->useras}'", $candidate, [
            'old_useras' => $oldAs,
            'new_useras' => $candidate->useras,
            'principle_id' => $candidate->principle_id,
        ]);

        return redirect()->route('interview.show', $candidate->id)
            ->with('success', 'Data kandidat ' . $candidate->full_name . ' berhasil dialihkan.');
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
            $candidate->area = trim($request->area);
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
        ActivityLogger::log('UPDATE', 'Interview', "Mengubah area/prinsiple kandidat {$candidate->full_name}. Area: '{$oldArea}' -> '{$candidate->area}', Prinsiple: '{$oldPrincipleName}' -> '{$newPrincipleName}'", $candidate, [
            'old_area' => $oldArea,
            'new_area' => $candidate->area,
            'old_principle' => $oldPrincipleName,
            'new_principle' => $newPrincipleName,
        ]);

        return redirect()->back()
            ->with('success', 'Area dan Prinsiple kandidat ' . $candidate->full_name . ' berhasil diperbarui.');
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

