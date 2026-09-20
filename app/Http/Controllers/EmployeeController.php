<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Principle;
use App\Models\OdooEntity;
use App\Models\OdooSyncLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Services\ActivityLogger;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::query();

        // Search by Nama Karyawan (multiple terms, case-insensitive, partial/multi-word), NIK, or NIP
        if ($searchRaw = $request->input('search')) {
            $rawTerms = is_array($searchRaw) ? $searchRaw : preg_split('/[,;\n\r|]+/', (string)$searchRaw);
            $searchTerms = [];
            foreach ($rawTerms as $t) {
                $clean = trim((string)$t);
                if ($clean !== '') {
                    $searchTerms[] = $clean;
                }
            }
            $searchTerms = array_unique($searchTerms);

            if (!empty($searchTerms)) {
                $query->where(function ($outerQ) use ($searchTerms) {
                    foreach ($searchTerms as $idx => $term) {
                        $termLower = strtolower($term);
                        $clause = function ($subQ) use ($term, $termLower) {
                            // Match against nama_karyawan (case-insensitive), NIK, or NIP
                            $subQ->whereRaw('LOWER(nama_karyawan) LIKE ?', ["%{$termLower}%"])
                                 ->orWhere('nik', 'like', "%{$term}%")
                                 ->orWhere('nip', 'like', "%{$term}%");

                            // Multi-word matching within a term (e.g. "ubaid maulana" matches "Ubaid Maulana Aliyuddin")
                            $words = array_filter(explode(' ', $termLower));
                            if (count($words) > 1) {
                                $subQ->orWhere(function ($wordQ) use ($words) {
                                    foreach ($words as $w) {
                                        $wordQ->whereRaw('LOWER(nama_karyawan) LIKE ?', ["%{$w}%"]);
                                    }
                                });
                            }
                        };

                        if ($idx === 0) {
                            $outerQ->where($clause);
                        } else {
                            $outerQ->orWhere($clause);
                        }
                    }
                });
            }
        }

        // Status filter: Default to 'Aktiv' only unless explicitly set otherwise
        $status = $request->input('status', 'Aktiv');
        if (!empty($status) && !in_array(strtolower($status), ['all', 'semua'])) {
            $query->where('status', $status);
        }

        // Tipe filter (Inhouse / RateCard)
        if ($tipe = $request->input('tipe')) {
            $query->where('tipe_karyawan', $tipe);
        }

        if ($prinsiple = $request->input('prinsiple')) {
            $query->where('prinsiple', $prinsiple);
        }

        if ($jabatan = $request->input('jabatan')) {
            $query->where('jabatan', $jabatan);
        }

        if ($area = $request->input('area')) {
            $query->where('area', $area);
        }

        if ($entity = $request->input('entity')) {
            $query->where('entity', $entity);
        }

        // Penerapan Scope Hak Akses Role / User (Prinsiple & Area Cover)
        $currentUser = Auth::user();
        if ($currentUser) {
            $currentUser->applyRoleScopeToEmployees($query);
        }

        // Sort order: default diurutkan berdasarkan join date (tanggal_join) terbaru
        $sortBy = $request->input('sort_by', 'tanggal_join');
        $sortDir = strtolower($request->input('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        if ($sortBy === 'tanggal_join') {
            $query->orderByRaw('CASE WHEN tanggal_join IS NOT NULL AND tanggal_join != "" THEN 0 ELSE 1 END, tanggal_join ' . $sortDir . ', id desc');
        } else {
            $query->orderBy($sortBy, $sortDir);
        }

        $employees = $query->paginate(10)->withQueryString();

        // Metric Statistics (Disesuaikan dengan Scope Role User Aktif)
        $statBase = Employee::query();
        if ($currentUser) {
            $currentUser->applyRoleScopeToEmployees($statBase);
        }

        $stats = [
            'total' => (clone $statBase)->count(),
            'aktif' => (clone $statBase)->where('status', 'Aktiv')->count(),
            'resign' => (clone $statBase)->where('status', 'Resign')->count(),
            'inhouse' => (clone $statBase)->where('tipe_karyawan', 'Inhouse')->count(),
            'ratecard' => (clone $statBase)->where('tipe_karyawan', 'RateCard')->count(),
        ];

        // Dropdown options (Distinct, exclude BUDGET, sorted naturally)
        $principleNamesFromTable = Principle::where('name', 'not like', '%BUDGET%')
            ->select('name')
            ->distinct()
            ->whereNotNull('name')
            ->where('name', '!=', '')
            ->pluck('name');

        $principleNamesFromEmployees = Employee::where('prinsiple', 'not like', '%BUDGET%')
            ->select('prinsiple')
            ->distinct()
            ->whereNotNull('prinsiple')
            ->where('prinsiple', '!=', '')
            ->pluck('prinsiple');

        $distinctPrinciples = $principleNamesFromTable->merge($principleNamesFromEmployees)
            ->unique()
            ->filter()
            ->sort(SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->map(fn($n) => (object)['name' => $n]);

        if ($currentUser && !$currentUser->handlesAllPrinciples()) {
            $allowedP = array_map('strtolower', array_map('trim', $currentUser->getEffectivePrinciples()));
            $distinctPrinciples = $distinctPrinciples->filter(function($p) use ($allowedP) {
                return in_array(strtolower(trim($p->name)), $allowedP, true);
            })->values();
        }

        $distinctJabatan = Employee::select('jabatan')->distinct()->whereNotNull('jabatan')->orderBy('jabatan')->pluck('jabatan');
        $distinctArea = Employee::select('area')->distinct()->whereNotNull('area')->orderBy('area')->pluck('area');

        if ($currentUser && !$currentUser->coversAllAreas()) {
            $allowedA = array_map('strtolower', array_map('trim', $currentUser->getEffectiveAreas()));
            $distinctArea = $distinctArea->filter(function($a) use ($allowedA) {
                return in_array(strtolower(trim($a)), $allowedA, true);
            })->values();
        }
        
        // Pimpinan suggestions for datalist
        $existingPimpinan = Employee::whereNotNull('pimpinan')
            ->where('pimpinan', '!=', '')
            ->distinct()
            ->pluck('pimpinan');

        $leadersByJabatan = Employee::where(function ($q) {
            $q->where('jabatan', 'like', '%SPV%')
              ->orWhere('jabatan', 'like', '%SUPERVISOR%')
              ->orWhere('jabatan', 'like', '%KOORDINATOR%')
              ->orWhere('jabatan', 'like', '%HEAD%')
              ->orWhere('jabatan', 'like', '%MANAGER%')
              ->orWhere('jabatan', 'like', '%AS %')
              ->orWhere('jabatan', 'like', '%LEAD%')
              ->orWhereIn('level', ['SPV', 'HEAD', 'TL']);
        })->distinct()->pluck('nama_karyawan');

        $pimpinanSuggestions = $existingPimpinan->merge($leadersByJabatan)
            ->unique()
            ->filter()
            ->sort(SORT_NATURAL | SORT_FLAG_CASE)
            ->values();

        $distinctPimpinan = Employee::select('nama_karyawan', 'jabatan', 'area')
            ->where(function ($q) {
                $q->whereIn('level', ['SPV', 'HEAD', 'TL'])
                  ->orWhere('jabatan', 'like', '%SPV%')
                  ->orWhere('jabatan', 'like', '%SUPERVISOR%')
                  ->orWhere('jabatan', 'like', '%KOORDINATOR%')
                  ->orWhere('jabatan', 'like', '%HEAD%')
                  ->orWhere('jabatan', 'like', '%MANAGER%')
                  ->orWhere('jabatan', 'like', '%AS %')
                  ->orWhere('jabatan', 'like', '%LEAD%');
            })
            ->orderBy('nama_karyawan')
            ->get();

        // Active Inhouse Employees grouped by Area for Pimpinan Searchable Dropdown
        $inhouseEmployees = Employee::where('tipe_karyawan', 'Inhouse')
            ->where('status', 'Aktiv')
            ->select('id', 'nama_karyawan', 'jabatan', 'area', 'entity')
            ->orderBy('area')
            ->orderBy('nama_karyawan')
            ->get();

        $inhouseLeadersGrouped = $inhouseEmployees
            ->groupBy(function ($emp) {
                return $emp->area ? trim($emp->area) : 'Pusat / Lainnya';
            })
            ->sortKeys(SORT_NATURAL | SORT_FLAG_CASE)
            ->map(function ($items, $area) {
                return [
                    'area' => $area,
                    'items' => $items->map(function ($emp) {
                        return [
                            'id' => $emp->id,
                            'name' => $emp->nama_karyawan,
                            'jabatan' => $emp->jabatan ?? '',
                            'area' => $emp->area ?? '',
                            'entity' => $emp->entity ?? '',
                        ];
                    })->values()->all(),
                ];
            })
            ->values()
            ->all();

        $entitiesList = OdooEntity::orderBy('code')->get();

        // 12-Hour Employee Growth Progress Chart Data (every 30 mins, 24 slots)
        $chartData = Cache::remember('emp_growth_chart_12h', 60, function () use ($stats) {
            $tz = 'Asia/Jakarta';
            $now = Carbon::now($tz);

            // Round to nearest 30-minute boundary for clean labels
            if ($now->minute < 30) {
                $now = $now->minute(0)->second(0);
            } else {
                $now = $now->minute(30)->second(0);
            }

            $slotMinutes = 30;
            $numSlots = 24;

            $chartLabels = [];
            $slots = [];

            for ($i = $numSlots - 1; $i >= 0; $i--) {
                $slotEnd = $now->copy()->subMinutes($i * $slotMinutes);
                $slotStart = $slotEnd->copy()->subMinutes($slotMinutes);
                $chartLabels[] = $slotEnd->format('H:i');
                $slots[] = [
                    'start' => $slotStart->copy()->setTimezone('UTC'),
                    'end' => $slotEnd->copy()->setTimezone('UTC'),
                ];
            }

            $windowStart = $slots[0]['start'];
            $windowEnd = $slots[$numSlots - 1]['end'];

            $newEmployees = Employee::whereBetween('created_at', [$windowStart, $windowEnd])
                ->select('id', 'created_at')
                ->get();

            $resignedEmployees = Employee::where('status', 'Resign')
                ->whereBetween('updated_at', [$windowStart, $windowEnd])
                ->select('id', 'updated_at')
                ->get();

            $syncLogs = OdooSyncLog::whereBetween('created_at', [$windowStart, $windowEnd])
                ->select('id', 'created_at', 'new_count', 'resign_count', 'total_employee_count')
                ->get();

            $newSeries = array_fill(0, $numSlots, 0);
            $resignSeries = array_fill(0, $numSlots, 0);

            foreach ($slots as $idx => $slot) {
                $slotNewEmp = $newEmployees->filter(function ($emp) use ($slot) {
                    return $emp->created_at >= $slot['start'] && $emp->created_at <= $slot['end'];
                })->count();

                $slotResignEmp = $resignedEmployees->filter(function ($emp) use ($slot) {
                    return $emp->updated_at >= $slot['start'] && $emp->updated_at <= $slot['end'];
                })->count();

                $slotLogs = $syncLogs->filter(function ($log) use ($slot) {
                    return $log->created_at >= $slot['start'] && $log->created_at <= $slot['end'];
                });
                $syncNew = $slotLogs->sum('new_count');
                $syncRes = $slotLogs->sum('resign_count');

                $newSeries[$idx] = max($slotNewEmp, (int)$syncNew);
                $resignSeries[$idx] = max($slotResignEmp, (int)$syncRes);
            }

            $currentActive = (int)($stats['aktif'] ?? 0);
            $activeSeries = array_fill(0, $numSlots, $currentActive);

            for ($i = $numSlots - 2; $i >= 0; $i--) {
                $diff = $newSeries[$i + 1] - $resignSeries[$i + 1];
                $val = $activeSeries[$i + 1] - $diff;
                if ($val < 0) {
                    $val = 0;
                }
                $activeSeries[$i] = $val;
            }

            $totalNew12h = array_sum($newSeries);
            $totalResign12h = array_sum($resignSeries);

            return [
                'labels' => $chartLabels,
                'active_series' => $activeSeries,
                'new_series' => $newSeries,
                'resign_series' => $resignSeries,
                'total_new_12h' => $totalNew12h,
                'total_resign_12h' => $totalResign12h,
                'min_active' => count($activeSeries) ? min($activeSeries) : 0,
                'max_active' => count($activeSeries) ? max($activeSeries) : 0,
                'max_change' => max(array_merge($newSeries, $resignSeries, [0])),
            ];
        });

        $chartSummary = [
            'total_active' => $stats['aktif'] ?? 0,
            'total_resign' => $stats['resign'] ?? 0,
            'new_employees_12h' => $chartData['total_new_12h'] ?? 0,
            'resigned_employees_12h' => $chartData['total_resign_12h'] ?? 0,
        ];

        return view('master.karyawan.index', compact('employees', 'stats', 'chartData', 'chartSummary', 'distinctPrinciples', 'distinctJabatan', 'distinctArea', 'distinctPimpinan', 'pimpinanSuggestions', 'inhouseLeadersGrouped', 'entitiesList', 'status', 'tipe'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nik' => 'required|string|unique:employees,nik',
            'nama_karyawan' => 'required|string|max:255',
            'email' => 'nullable|email',
            'telepon' => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
            'tanggal_join' => 'required|date',
            'area' => 'required|string',
            'prinsiple' => 'required|string',
            'jabatan' => 'required|string',
            'divisi' => 'nullable|string',
            'pimpinan' => 'nullable|string',
            'jabatan_pimpinan' => 'nullable|string',
            'tipe_karyawan' => 'nullable|string',
            'entity' => 'nullable|string',
            'akses_login' => 'nullable',
        ]);

        $prin = Principle::where('name', $validated['prinsiple'])->first();
        if ($prin) {
            $validated['principle_id'] = $prin->id;
        }

        // Automatic Inhouse / RateCard determination
        $validated['tipe_karyawan'] = Employee::determineTipeKaryawan($validated['prinsiple']);
        if (empty($validated['entity'])) {
            $validated['entity'] = Employee::getEntityCodeFromPrinciple($validated['prinsiple']) ?: 'AMK';
        }

        // Birth date & default password (ddmmyyyy)
        if (empty($validated['tanggal_lahir'])) {
            $validated['tanggal_lahir'] = Employee::extractBirthDateFromNik($validated['nik']);
        }
        $dt = Carbon::parse($validated['tanggal_lahir']);
        $defaultPassword = $dt->format('dmY');
        $validated['password'] = Hash::make($defaultPassword);

        // Login access rule: Inhouse always has access; RateCard requires explicit toggle
        if ($validated['tipe_karyawan'] === 'Inhouse') {
            $validated['akses_login'] = true;
        } else {
            $validated['akses_login'] = $request->has('akses_login') && in_array($request->input('akses_login'), ['1', 'on', 'true'], true);
        }

        $validated['status'] = 'Aktiv';
        $validated['has_komponen'] = true;
        $validated['level'] = (str_contains(strtoupper($validated['jabatan']), 'SPV') ? 'SPV' : (str_contains(strtoupper($validated['jabatan']), 'HEAD') ? 'HEAD' : 'STAFF'));

        $newEmp = Employee::create($validated);

        ActivityLogger::crud('CREATE', 'Master Karyawan', "Menambahkan karyawan baru: {$newEmp->nama_karyawan} (NIK: {$newEmp->nik}, Jabatan: {$newEmp->jabatan})", $newEmp, [], $validated);

        return redirect()->route('master.karyawan.index')->with('success', 'Data Karyawan berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $validated = $request->validate([
            'nama_karyawan' => 'required|string|max:255',
            'email' => 'nullable|email',
            'telepon' => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
            'tanggal_join' => 'required|date',
            'area' => 'required|string',
            'prinsiple' => 'required|string',
            'jabatan' => 'required|string',
            'divisi' => 'nullable|string',
            'pimpinan' => 'nullable|string',
            'jabatan_pimpinan' => 'nullable|string',
            'status' => 'required|string',
            'tipe_karyawan' => 'nullable|string',
            'entity' => 'nullable|string',
            'akses_login' => 'nullable',
        ]);

        // Automatic Inhouse / RateCard determination
        $validated['tipe_karyawan'] = Employee::determineTipeKaryawan($validated['prinsiple']);
        if (empty($validated['entity'])) {
            $validated['entity'] = Employee::getEntityCodeFromPrinciple($validated['prinsiple']) ?: ($employee->entity ?: 'AMK');
        }

        // Birth date & default password check
        if (!empty($validated['tanggal_lahir'])) {
            $dt = Carbon::parse($validated['tanggal_lahir']);
            $defaultPassword = $dt->format('dmY');
            // If password is empty or birthdate changed, update default password
            if (empty($employee->password) || $employee->tanggal_lahir?->format('Y-m-d') !== $validated['tanggal_lahir']) {
                $validated['password'] = Hash::make($defaultPassword);
            }
        }

        // Login access rule: Inhouse always has access; RateCard respects setting
        if ($validated['tipe_karyawan'] === 'Inhouse') {
            $validated['akses_login'] = true;
        } else {
            $validated['akses_login'] = $request->has('akses_login') && in_array($request->input('akses_login'), ['1', 'on', 'true'], true);
        }

        $oldValues = $employee->only(array_keys($validated));
        $employee->update($validated);

        ActivityLogger::crud('UPDATE', 'Master Karyawan', "Memperbarui data karyawan: {$employee->nama_karyawan} (NIK: {$employee->nik})", $employee, $oldValues, $validated);

        // Sync to User table if existing
        if ($employee->email) {
            $user = User::where('email', $employee->email)->first();
            if ($user) {
                $user->update([
                    'name' => $employee->nama_karyawan,
                    'area' => $employee->area,
                    'job_title' => $employee->jabatan,
                    'phone' => $employee->telepon,
                    'is_active' => $employee->hasLoginAccess() && $employee->status === 'Aktiv',
                ]);
            }
        }

        return redirect()->route('master.karyawan.index')->with('success', 'Data Karyawan berhasil diperbarui!');
    }

    /**
     * Toggle Akses Login untuk Karyawan RateCard
     */
    public function toggleLoginAccess($id)
    {
        $employee = Employee::findOrFail($id);

        if ($employee->tipe_karyawan === 'Inhouse') {
            return redirect()->back()->with('info', "Karyawan Inhouse ({$employee->nama_karyawan}) otomatis memiliki akses login.");
        }

        $employee->akses_login = !$employee->akses_login;
        $employee->save();

        ActivityLogger::crud('UPDATE', 'Master Karyawan', "Mengubah izin login karyawan: {$employee->nama_karyawan} menjadi " . ($employee->akses_login ? 'Aktif' : 'Nonaktif'), $employee);

        if ($employee->email) {
            $user = User::where('email', $employee->email)->first();
            if ($user) {
                $user->update(['is_active' => $employee->akses_login]);
            }
        }

        $statusText = $employee->akses_login ? 'diberikan izin akses login' : 'dicabut izin akses loginnya';
        return redirect()->back()->with('success', "Karyawan RateCard {$employee->nama_karyawan} berhasil {$statusText}.");
    }

    public function bulkUpdatePimpinan(Request $request)
    {
        $validated = $request->validate([
            'target_type' => 'required|in:selected,filter',
            'employee_ids' => 'nullable|array',
            'employee_ids.*' => 'integer',
            'filter_prinsiple' => 'nullable|string',
            'filter_area' => 'nullable|string',
            'filter_entity' => 'nullable|string',
            'filter_status' => 'nullable|string',
            'pimpinan' => 'required|string|max:255',
            'jabatan_pimpinan' => 'nullable|string|max:255',
            'only_empty' => 'nullable|boolean',
        ]);

        $pimpinan = trim($validated['pimpinan']);
        $jabatanPimpinan = !empty($validated['jabatan_pimpinan']) ? trim($validated['jabatan_pimpinan']) : null;
        $onlyEmpty = $request->boolean('only_empty');

        if ($validated['target_type'] === 'selected') {
            if (empty($validated['employee_ids'])) {
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Pilih minimal 1 karyawan yang ingin diperbarui.'], 422);
                }
                return redirect()->back()->with('error', 'Pilih minimal 1 karyawan yang ingin diperbarui.');
            }
            $query = Employee::whereIn('id', $validated['employee_ids']);
        } else {
            $query = Employee::query();
            if (!empty($validated['filter_prinsiple'])) {
                $query->where('prinsiple', $validated['filter_prinsiple']);
            }
            if (!empty($validated['filter_area'])) {
                $query->where('area', $validated['filter_area']);
            }
            if (!empty($validated['filter_entity'])) {
                $query->where('entity', $validated['filter_entity']);
            }
            if (!empty($validated['filter_status'])) {
                $query->where('status', $validated['filter_status']);
            }
        }

        if ($onlyEmpty) {
            $query->where(function ($q) {
                $q->whereNull('pimpinan')->orWhere('pimpinan', '');
            });
        }

        $updateData = ['pimpinan' => $pimpinan];
        if (!empty($jabatanPimpinan)) {
            $updateData['jabatan_pimpinan'] = $jabatanPimpinan;
        }

        $updatedCount = $query->update($updateData);

        ActivityLogger::crud('UPDATE', 'Master Karyawan', "Bulk update pimpinan ({$updatedCount} karyawan) menjadi {$pimpinan}" . ($jabatanPimpinan ? " ({$jabatanPimpinan})" : ''), null, [], [
            'pimpinan' => $pimpinan,
            'jabatan_pimpinan' => $jabatanPimpinan,
            'updated_count' => $updatedCount,
            'target_type' => $validated['target_type'],
        ]);

        $msg = "Berhasil menetapkan pimpinan '{$pimpinan}' untuk {$updatedCount} data karyawan.";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'affected' => $updatedCount,
            ]);
        }

        return redirect()->back()->with('success', $msg);
    }

    public function resign($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->update(['status' => 'Resign', 'akses_login' => false]);

        if ($employee->email) {
            $user = User::where('email', $employee->email)->first();
            if ($user) {
                $user->update(['is_active' => false]);
            }
        }

        return redirect()->route('master.karyawan.index')->with('info', "Karyawan {$employee->nama_karyawan} berhasil di-resignkan.");
    }

    public function switchUser($nik)
    {
        $currentAuthUser = Auth::user();

        // Simpan data akun asli sebelum switch (jika belum ada sesi impersonasi yang aktif)
        $impersonatorId = session('impersonator_id', $currentAuthUser?->id);
        $impersonatorName = session('impersonator_name', $currentAuthUser?->name ?? 'Administrator');
        $impersonatorEmail = session('impersonator_email', $currentAuthUser?->email ?? '');

        $employee = Employee::where('nik', $nik)->firstOrFail();
        $userRole = ($employee->tipe_karyawan === 'Inhouse') ? 'karyawan_inhouse' : 'karyawan_ratecard';
        $user = User::firstOrCreate(
            ['email' => $employee->email],
            [
                'name' => $employee->nama_karyawan,
                'password' => Hash::make('password'),
                'role' => $userRole,
                'area' => $employee->area,
                'job_title' => $employee->jabatan,
                'phone' => $employee->telepon,
                'is_active' => true,
            ]
        );
        $user->update([
            'name' => $employee->nama_karyawan,
            'role' => $userRole,
            'area' => $employee->area,
            'job_title' => $employee->jabatan,
            'is_active' => true,
        ]);

        Auth::login($user);

        ActivityLogger::log('SWITCH_USER', 'Auth & Akun', "{$impersonatorName} beralih akun (impersonate) sebagai {$employee->nama_karyawan} ({$employee->jabatan})", $employee, [
            'target_nik' => $employee->nik,
            'target_email' => $employee->email,
        ]);

        // Pertahankan sesi impersonator
        if ($impersonatorId && $impersonatorId !== $user->id) {
            session([
                'impersonator_id'    => $impersonatorId,
                'impersonator_name'  => $impersonatorName,
                'impersonator_email' => $impersonatorEmail,
            ]);
        }

        return redirect()->route('interview.index')->with('success', "Berhasil beralih akun dan login sebagai <strong>{$employee->nama_karyawan}</strong> ({$employee->jabatan} &bull; Area {$employee->area}). Anda dapat kembali ke akun asli kapan saja melalui tombol di bagian atas atau samping.");
    }

    /**
     * Kembali ke akses user utama / asli (Revert Switch User)
     */
    public function switchBack(Request $request)
    {
        $impersonatorId = session('impersonator_id');

        if (!$impersonatorId) {
            return redirect()->route('fitur.index')->with('info', 'Tidak ada sesi switch user yang sedang aktif.');
        }

        $originalUser = User::find($impersonatorId);

        if (!$originalUser) {
            session()->forget(['impersonator_id', 'impersonator_name', 'impersonator_email']);
            return redirect()->route('login')->with('error', 'Akun user asli tidak ditemukan.');
        }

        // Login kembali ke akun asli
        Auth::login($originalUser);
        session()->forget(['impersonator_id', 'impersonator_name', 'impersonator_email']);

        ActivityLogger::log('SWITCH_USER', 'Auth & Akun', "Pengguna kembali dari sesi impersonate ke akun asli: {$originalUser->name}", $originalUser);

        return redirect()->route('master.karyawan.index')->with('success', "Berhasil kembali ke akses akun utama: <strong>{$originalUser->name}</strong>.");
    }
}
