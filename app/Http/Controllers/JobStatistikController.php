<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Employee;
use App\Models\JobSpec;
use App\Models\Principle;
use App\Models\TbArea;
use App\Services\JobStatistikXlsxExportService;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class JobStatistikController extends Controller
{
    /**
     * Kamus pemetaan Area / Kota ke Region 1 s/d Region 7 (ESA Groups).
     */
    public static function getRegionMap(): array
    {
        return TbArea::getRegionMap();
    }

    /**
     * Resolusi nama region dari area.
     */
    public static function resolveRegion(?string $area): string
    {
        return TbArea::resolveRegion($area);
    }


    /**
     * Data Master Karyawan Map (email / name -> formatted display name).
     * HANYA dari list karyawan inhouse ESA Groups dan tabel users internal.
     * Penulisan nama diseragamkan dengan format Title Case (huruf awal kapital).
     */
    private function getEmployeeMaps(): array
    {
        $inhousePrinciples = [
            'PT ARINA MULTI KARYA',
            'PT ALVA KARYA PERKASA',
            'PT ANUGRAH TALENTA BERKARYA',
            'PT ANUGRAH TERPERCAYA KERJA',
            'PT ABADI BERKAT ODELIA',
        ];

        $emailMap = [];
        $rawNameMap = [];

        // 1. Prioritas Utama: Tabel Users (Akun Resmi Sistem: Rekrutmen, AS, Admin, Inhouse)
        $users = \App\Models\User::select('email', 'name', 'job_title', 'area')->get();
        foreach ($users as $u) {
            $eMail = strtolower(trim((string)$u->email));
            $rawName = trim((string)$u->name);
            $cleanName = ucwords(strtolower($rawName));
            $jab = trim((string)$u->job_title);
            $area = trim((string)$u->area);

            $display = $cleanName;
            if (!empty($jab)) {
                $display .= " ({$jab})";
            }

            $info = [
                'name'          => $cleanName,
                'email'         => $eMail,
                'jabatan'       => $jab,
                'display'       => $display,
                'area'          => $area,
                'canonical_key' => !empty($eMail) ? $eMail : strtolower($cleanName),
            ];

            if (!empty($eMail)) {
                $emailMap[$eMail] = $info;
            }
            if (!empty($cleanName)) {
                $rawNameMap[strtolower($cleanName)] = $info;
            }
        }

        // 2. Prioritas Kedua: Tabel Employees KHUSUS INHOUSE ESA GROUPS SAJA
        $inhouseEmployees = Employee::whereIn('prinsiple', $inhousePrinciples)
            ->select('email', 'nama_karyawan', 'jabatan', 'area')
            ->get();

        foreach ($inhouseEmployees as $emp) {
            $eMail = strtolower(trim((string)$emp->email));
            $rawName = trim((string)$emp->nama_karyawan);
            $cleanName = ucwords(strtolower($rawName));
            $jab = trim((string)$emp->jabatan);
            $area = trim((string)$emp->area);

            $display = $cleanName;
            if (!empty($jab)) {
                $display .= " ({$jab})";
            }

            $info = [
                'name'          => $cleanName,
                'email'         => $eMail,
                'jabatan'       => $jab,
                'display'       => $display,
                'area'          => $area,
                'canonical_key' => !empty($eMail) ? $eMail : strtolower($cleanName),
            ];

            if (!empty($eMail) && !isset($emailMap[$eMail])) {
                $emailMap[$eMail] = $info;
            }
            if (!empty($cleanName) && !isset($rawNameMap[strtolower($cleanName)])) {
                $rawNameMap[strtolower($cleanName)] = $info;
            }
        }

        return [$emailMap, $rawNameMap];
    }

    /**
     * Halaman Utama Statistik Job Post & Pelamar.
     */
    public function index(Request $request)
    {
        $filters = [
            'region'    => trim((string)$request->input('f_region', '')),
            'area'      => trim((string)$request->input('f_area', '')),
            'prinsiple' => trim((string)$request->input('f_prinsiple', '')),
            'user'      => trim((string)$request->input('f_user', '')),
            'info'      => trim((string)$request->input('f_info', '')),
        ];

        $data = $this->calculateStatistics($filters);

        return view('job.statistik', array_merge($data, [
            'filters' => $filters,
        ]));
    }

    /**
     * Export Excel (.xlsx) Statistik multi-sheet dengan format profesional.
     */
    public function export(Request $request): BinaryFileResponse
    {
        $filters = [
            'region'    => trim((string)$request->input('f_region', '')),
            'area'      => trim((string)$request->input('f_area', '')),
            'prinsiple' => trim((string)$request->input('f_prinsiple', '')),
            'user'      => trim((string)$request->input('f_user', '')),
            'info'      => trim((string)$request->input('f_info', '')),
        ];

        $data = $this->calculateStatistics($filters);
        
        $filePath = JobStatistikXlsxExportService::generateXlsx($data, $filters);
        $fileName = 'Statistik_Job_Kandidat_Portal_' . date('Ymd_His') . '.xlsx';

        ActivityLogger::export('Job Statistik', "Mengekspor laporan rekapitulasi statistik job & kandidat portal ke Excel", [
            'region' => $filters['region'] ?: 'Semua',
            'area' => $filters['area'] ?: 'Semua',
            'prinsiple' => $filters['prinsiple'] ?: 'Semua',
            'user' => $filters['user'] ?: 'Semua',
            'info' => $filters['info'] ?: 'Semua',
        ]);

        return response()->download($filePath, $fileName, [
            'Content-Type'  => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'       => '0',
            'Pragma'        => 'public',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Mesin Kalkulasi Statistik Utama.
     */
    private function calculateStatistics(array $filters): array
    {
        [$emailMap, $rawNameMap] = $this->getEmployeeMaps();

        // 1. Fetch Job Specs
        $jobs = JobSpec::select('job_title', 'job_area', 'job_prinsiple', 'created_by')->get();

        // 2. Fetch Candidates: HANYA kandidat dari Kandidat Portal (jenis = 'Job Portal'), kandidat interview tidak termasuk
        $candidateQuery = Candidate::where('jenis', 'Job Portal')
            ->select('applied_job', 'useras', 'kategori_kandidat', 'info', 'area', 'principle', 'odoo_stage_name', 'status');
        if (!empty($filters['info'])) {
            $candidateQuery->whereRaw('LOWER(TRIM(info)) = ?', [strtolower($filters['info'])]);
        }
        $candidates = $candidateQuery->get();

        // Distinct filter lists
        $listRegions = ['Region 1', 'Region 2', 'Region 3', 'Region 4', 'Region 5', 'Region 6', 'Region 7'];
        $listAreas = [];
        $listPrinsiple = [];
        $listUsers = [];
        $listInfo = [];

        // Maps data structures
        $statsArea = [];
        $statsUserArea = [];
        $statsDetail = [];
        $jobMap = [];

        // Inisialisasi proses Job Specs
        foreach ($jobs as $job) {
            $rawArea = trim((string)$job->job_area);
            $area = !empty($rawArea) && $rawArea !== '-' ? TbArea::getCanonicalAreaName($rawArea) : '-';
            $region = self::resolveRegion($area);
            $prinsiple = !empty(trim((string)$job->job_prinsiple)) ? trim((string)$job->job_prinsiple) : '-';
            $creator = trim((string)$job->created_by);
            $creatorEmail = strtolower($creator);

            // Tentukan display user name
            $userDisplay = $creator;
            if (isset($emailMap[$creatorEmail])) {
                $userDisplay = $emailMap[$creatorEmail]['display'];
            } elseif (isset($rawNameMap[strtolower($creator)])) {
                $userDisplay = $rawNameMap[strtolower($creator)]['display'];
            }

            // Populate filter options
            if ($area !== '-' && !in_array($area, $listAreas)) $listAreas[] = $area;
            if ($prinsiple !== '-' && !in_array($prinsiple, $listPrinsiple)) $listPrinsiple[] = $prinsiple;
            if (!empty($creator) && !isset($listUsers[$creatorEmail])) $listUsers[$creatorEmail] = $userDisplay;

            // Terapkan filter pada job specs
            if (!empty($filters['region']) && strtolower($region) !== strtolower($filters['region'])) continue;
            if (!empty($filters['area']) && strtolower($area) !== strtolower($filters['area'])) continue;
            if (!empty($filters['prinsiple']) && strtolower($prinsiple) !== strtolower($filters['prinsiple'])) continue;
            if (!empty($filters['user']) && $creatorEmail !== strtolower($filters['user']) && strtolower($userDisplay) !== strtolower($filters['user'])) continue;

            $titleRaw = trim((string)$job->job_title);
            $titleLow = strtolower($titleRaw);

            // Table 1: Area
            if (!isset($statsArea[$area])) {
                $statsArea[$area] = [
                    'region'   => $region,
                    'area'     => $area,
                    'job_post' => 0,
                    'pelamar'  => 0,
                ];
            }
            $statsArea[$area]['job_post']++;

            // Table 2: User & Area
            $keyUserArea = $userDisplay . '|' . $area;
            if (!isset($statsUserArea[$keyUserArea])) {
                $statsUserArea[$keyUserArea] = [
                    'user'     => $userDisplay,
                    'region'   => $region,
                    'area'     => $area,
                    'job_post' => 0,
                    'pelamar'  => 0,
                ];
            }
            $statsUserArea[$keyUserArea]['job_post']++;

            // Table 3: Detail
            $keyDetail = $userDisplay . '|' . $area . '|' . $prinsiple . '|' . $titleRaw;
            if (!isset($statsDetail[$keyDetail])) {
                $statsDetail[$keyDetail] = [
                    'user'          => $userDisplay,
                    'region'        => $region,
                    'area'          => $area,
                    'prinsiple'     => $prinsiple,
                    'job_title'     => $titleRaw,
                    'job_post'      => 0,
                    'green'         => 0,
                    'yello'         => 0,
                    'red'           => 0,
                    'total_pelamar' => 0,
                ];
            }
            $statsDetail[$keyDetail]['job_post']++;

            // Map untuk pencocokan pelamar
            $creatorRawName = isset($emailMap[$creatorEmail]) ? $emailMap[$creatorEmail]['name'] : $creator;
            $mapKey = $titleLow . '|' . strtolower($creatorRawName);
            $jobItemInfo = [
                'keyDetail' => $keyDetail,
                'area'      => $area,
                'region'    => $region,
                'prinsiple' => $prinsiple,
                'job_title' => $titleRaw,
                'user'      => $userDisplay,
            ];
            if (!isset($jobMap[$mapKey])) {
                $jobMap[$mapKey] = [];
            }
            $jobMap[$mapKey][] = $jobItemInfo;

            if (!isset($jobTitleMap[$titleLow])) {
                $jobTitleMap[$titleLow] = [];
            }
            $jobTitleMap[$titleLow][] = $jobItemInfo;
        }

        // Inisialisasi struktur Rekap Step Odoo per Rekrutor
        $statsOdooRecruiter = [];

        // Hitung Kandidat / Pelamar
        $totalPelamarCount = 0;
        $totalGreenCount = 0;
        $totalOdooCount = 0;

        foreach ($candidates as $c) {
            $cInf = trim((string)$c->info);
            if (!empty($cInf) && !in_array($cInf, $listInfo)) {
                $listInfo[] = $cInf;
            }

            if (!empty($filters['info']) && strtolower($cInf) !== strtolower($filters['info'])) {
                continue;
            }

            $appJob = strtolower(trim((string)$c->applied_job));
            $useras = trim((string)$c->useras);
            $userasLower = strtolower($useras);
            $cRawArea = trim((string)$c->area);
            $cArea = !empty($cRawArea) && $cRawArea !== '-' && strtolower($cRawArea) !== 'null' ? TbArea::getCanonicalAreaName($cRawArea) : '-';
            $rawPrin = $c->getRawOriginal('principle');
            if (empty($rawPrin) && is_object($c->principle)) {
                $rawPrin = $c->principle->name ?? '-';
            }
            $cPrinciple = trim((string)$rawPrin) ?: '-';
            $cRegion = self::resolveRegion($cArea);

            // Resolusi nama rekruter kandidat: cek berdasarkan email dulu, lalu inhouse name map
            $recruiterDisplay = null;
            $matchedCanonicalKey = null;
            $matchedEmail = null;

            if (!empty($userasLower) && isset($emailMap[$userasLower])) {
                $recruiterDisplay = $emailMap[$userasLower]['display'];
                $matchedCanonicalKey = $emailMap[$userasLower]['canonical_key'];
                $matchedEmail = $emailMap[$userasLower]['email'];
                if (($cArea === '-' || strtolower($cArea) === 'nasional') && !empty($emailMap[$userasLower]['area'])) {
                    $cArea = TbArea::getCanonicalAreaName($emailMap[$userasLower]['area']);
                    $cRegion = self::resolveRegion($cArea);
                }
            } elseif (!empty($userasLower) && isset($rawNameMap[$userasLower])) {
                $recruiterDisplay = $rawNameMap[$userasLower]['display'];
                $matchedCanonicalKey = $rawNameMap[$userasLower]['canonical_key'];
                $matchedEmail = $rawNameMap[$userasLower]['email'];
                if (($cArea === '-' || strtolower($cArea) === 'nasional') && !empty($rawNameMap[$userasLower]['area'])) {
                    $cArea = TbArea::getCanonicalAreaName($rawNameMap[$userasLower]['area']);
                    $cRegion = self::resolveRegion($cArea);
                }
            } else {
                // Penulisan Nama dibuat seragam hanya huruf awal yang kapital
                if (str_contains($useras, '@')) {
                    $prefix = explode('@', $useras)[0];
                    $clean = ucwords(strtolower(trim(preg_replace('/[0-9_.-]+/', ' ', $prefix))));
                    $recruiterDisplay = $clean ?: $useras;
                } else {
                    $recruiterDisplay = ucwords(strtolower($useras));
                }
                $matchedCanonicalKey = $userasLower;
                $matchedEmail = $useras;
            }

            // Dropdown list
            if (!empty($useras)) {
                $userKey = !empty($matchedEmail) ? strtolower($matchedEmail) : $userasLower;
                if (!isset($listUsers[$userKey])) {
                    $listUsers[$userKey] = $recruiterDisplay;
                }
            }
            if ($cArea !== '-' && !in_array($cArea, $listAreas)) {
                $listAreas[] = $cArea;
            }

            // Filter Kandidat
            if (!empty($filters['region']) && strtolower($cRegion) !== strtolower($filters['region'])) continue;
            if (!empty($filters['area']) && strtolower($cArea) !== strtolower($filters['area'])) continue;
            if (!empty($filters['prinsiple']) && strtolower($cPrinciple) !== strtolower($filters['prinsiple'])) continue;
            if (!empty($filters['user']) && $userasLower !== strtolower($filters['user']) && strtolower($recruiterDisplay) !== strtolower($filters['user']) && strtolower($matchedEmail ?? '') !== strtolower($filters['user'])) continue;

            $totalPelamarCount++;
            $kat = strtolower(trim((string)$c->kategori_kandidat));
            if (str_contains($kat, 'green') || str_contains($kat, 'hijau')) {
                $totalGreenCount++;
            }
            if (!empty($c->odoo_stage_name)) {
                $totalOdooCount++;
            }

            // Pencocokan ke Table 1, Table 2, Table 3 via jobMap atau jobTitleMap
            $mapKey = $appJob . '|' . $userasLower;
            $matchedArea = $cArea;
            $matchedUser = $recruiterDisplay;
            $matchedRegion = $cRegion;
            $matchedPrin = $cPrinciple;
            $matchedTitle = trim((string)$c->applied_job);
            $matchedDetailKey = null;

            if (isset($jobMap[$mapKey]) && !empty($jobMap[$mapKey])) {
                $jMatch = $jobMap[$mapKey][0];
                $matchedArea = $jMatch['area'];
                $matchedRegion = $jMatch['region'];
                $matchedPrin = $jMatch['prinsiple'];
                $matchedTitle = $jMatch['job_title'];
                $matchedUser = $jMatch['user'];
                $matchedDetailKey = $jMatch['keyDetail'];
            } elseif (isset($jobTitleMap[$appJob]) && !empty($jobTitleMap[$appJob])) {
                $chosen = $jobTitleMap[$appJob][0];
                foreach ($jobTitleMap[$appJob] as $candidateJob) {
                    if (strtolower($candidateJob['area']) === strtolower($cArea)) {
                        $chosen = $candidateJob;
                        break;
                    }
                }
                $matchedArea = $chosen['area'];
                $matchedRegion = $chosen['region'];
                $matchedPrin = $chosen['prinsiple'];
                $matchedTitle = $chosen['job_title'];
                $matchedUser = $chosen['user'];
                $matchedDetailKey = $chosen['keyDetail'];
            }

            // Update Table 1: Area
            if (!isset($statsArea[$matchedArea])) {
                $statsArea[$matchedArea] = [
                    'region'   => $matchedRegion,
                    'area'     => $matchedArea,
                    'job_post' => 0,
                    'pelamar'  => 0,
                ];
            }
            $statsArea[$matchedArea]['pelamar']++;

            // Update Table 2: User & Area
            $kUA = $matchedUser . '|' . $matchedArea;
            if (!isset($statsUserArea[$kUA])) {
                $statsUserArea[$kUA] = [
                    'user'     => $matchedUser,
                    'region'   => $matchedRegion,
                    'area'     => $matchedArea,
                    'job_post' => 0,
                    'pelamar'  => 0,
                ];
            }
            $statsUserArea[$kUA]['pelamar']++;

            // Update Table 3: Detail Kandidat per Prinsiple (Job Specs)
            if ($matchedDetailKey && isset($statsDetail[$matchedDetailKey])) {
                $statsDetail[$matchedDetailKey]['total_pelamar']++;
                if (str_contains($kat, 'green') || str_contains($kat, 'hijau')) {
                    $statsDetail[$matchedDetailKey]['green']++;
                } elseif (str_contains($kat, 'yello') || str_contains($kat, 'kuning')) {
                    $statsDetail[$matchedDetailKey]['yello']++;
                } elseif (str_contains($kat, 'red') || str_contains($kat, 'merah')) {
                    $statsDetail[$matchedDetailKey]['red']++;
                }
            }

            // Update Table 4: Rekap Step Odoo per Rekrutor
            $recKey = !empty($matchedCanonicalKey) ? $matchedCanonicalKey : (!empty($userasLower) ? $userasLower : 'unassigned');
            if (!isset($statsOdooRecruiter[$recKey])) {
                $statsOdooRecruiter[$recKey] = [
                    'user_email'    => !empty($matchedEmail) ? $matchedEmail : ($useras ?: 'Tidak Terdata'),
                    'user_display'  => !empty($useras) ? $recruiterDisplay : 'Belum Ditugaskan / Walkin Bebas',
                    'region'        => $matchedRegion,
                    'area'          => $matchedArea,
                    'total'         => 0,
                    'data_pelamar'  => 0,
                    'interview'     => 0,
                    'principal'     => 0,
                    'elearning'     => 0,
                    'pkwt'          => 0,
                    'joined'        => 0,
                    'belum_di_odoo' => 0,
                ];
            }
            $statsOdooRecruiter[$recKey]['total']++;

            $stg = strtolower(trim((string)$c->odoo_stage_name));
            if (empty($stg)) {
                $statsOdooRecruiter[$recKey]['belum_di_odoo']++;
            } elseif (str_contains($stg, 'joined')) {
                $statsOdooRecruiter[$recKey]['joined']++;
            } elseif (str_contains($stg, 'pkwt')) {
                $statsOdooRecruiter[$recKey]['pkwt']++;
            } elseif (str_contains($stg, 'learning') || str_contains($stg, 'elearning')) {
                $statsOdooRecruiter[$recKey]['elearning']++;
            } elseif (str_contains($stg, 'principal')) {
                $statsOdooRecruiter[$recKey]['principal']++;
            } elseif (str_contains($stg, 'interview')) {
                $statsOdooRecruiter[$recKey]['interview']++;
            } elseif (str_contains($stg, 'pelamar') || str_contains($stg, 'initial')) {
                $statsOdooRecruiter[$recKey]['data_pelamar']++;
            } else {
                $statsOdooRecruiter[$recKey]['data_pelamar']++;
            }
        }

        // Sorting
        sort($listAreas);
        sort($listPrinsiple);
        asort($listUsers);
        sort($listInfo);

        // Urutkan Tabel 1 berdasarkan jumlah pelamar & post terbanyak
        uasort($statsArea, fn($a, $b) => ($b['pelamar'] + $b['job_post']) <=> ($a['pelamar'] + $a['job_post']));

        // Urutkan Tabel 2 berdasarkan jumlah pelamar terbanyak
        uasort($statsUserArea, fn($a, $b) => $b['pelamar'] <=> $a['pelamar']);

        // Urutkan Tabel 3 berdasarkan total pelamar terbanyak
        uasort($statsDetail, fn($a, $b) => $b['total_pelamar'] <=> $a['total_pelamar']);

        // Urutkan Tabel 4 berdasarkan peringkat jumlah JOINED terbanyak, lalu pkwt, lalu total
        uasort($statsOdooRecruiter, function ($a, $b) {
            if ($b['joined'] !== $a['joined']) {
                return $b['joined'] <=> $a['joined'];
            }
            if ($b['pkwt'] !== $a['pkwt']) {
                return $b['pkwt'] <=> $a['pkwt'];
            }
            return $b['total'] <=> $a['total'];
        });

        // Hitung total job post
        $totalJobPosts = 0;
        foreach ($statsArea as $sa) {
            $totalJobPosts += $sa['job_post'];
        }

        return [
            'listRegions'        => $listRegions,
            'listAreas'          => $listAreas,
            'listPrinsiple'      => $listPrinsiple,
            'listUsers'          => $listUsers,
            'listInfo'           => $listInfo,
            'statsArea'          => array_values($statsArea),
            'statsUserArea'      => array_values($statsUserArea),
            'statsDetail'        => array_values($statsDetail),
            'statsOdooRecruiter' => array_values($statsOdooRecruiter),
            'totalJobPosts'      => $totalJobPosts,
            'totalPelamar'       => $totalPelamarCount,
            'totalGreen'         => $totalGreenCount,
            'totalOdoo'          => $totalOdooCount,
        ];
    }
}
