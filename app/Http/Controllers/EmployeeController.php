<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Principle;
use App\Models\OdooEntity;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

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

        // Sort order: default diurutkan berdasarkan join date (tanggal_join) terbaru
        $sortBy = $request->input('sort_by', 'tanggal_join');
        $sortDir = strtolower($request->input('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        if ($sortBy === 'tanggal_join') {
            $query->orderByRaw('CASE WHEN tanggal_join IS NOT NULL AND tanggal_join != "" THEN 0 ELSE 1 END, tanggal_join ' . $sortDir . ', id desc');
        } else {
            $query->orderBy($sortBy, $sortDir);
        }

        $employees = $query->paginate(10)->withQueryString();

        // Metric Statistics (Global overview)
        $stats = [
            'total' => Employee::count(),
            'aktif' => Employee::where('status', 'Aktiv')->count(),
            'resign' => Employee::where('status', 'Resign')->count(),
            'inhouse' => Employee::where('tipe_karyawan', 'Inhouse')->count(),
            'ratecard' => Employee::where('tipe_karyawan', 'RateCard')->count(),
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
        $distinctJabatan = Employee::select('jabatan')->distinct()->whereNotNull('jabatan')->orderBy('jabatan')->pluck('jabatan');
        $distinctArea = Employee::select('area')->distinct()->whereNotNull('area')->orderBy('area')->pluck('area');
        
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
        return view('master.karyawan.index', compact('employees', 'stats', 'distinctPrinciples', 'distinctJabatan', 'distinctArea', 'distinctPimpinan', 'pimpinanSuggestions', 'inhouseLeadersGrouped', 'entitiesList', 'status', 'tipe'));
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

        Employee::create($validated);

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

        $employee->update($validated);

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

        $affected = $query->update($updateData);

        $msg = "Berhasil menetapkan pimpinan '{$pimpinan}' untuk {$affected} data karyawan.";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'affected' => $affected,
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
        return redirect()->route('interview.index')->with('success', "Berhasil beralih akun dan login sebagai <strong>{$employee->nama_karyawan}</strong> ({$employee->jabatan} &bull; Area {$employee->area}). Data kandidat kini tampil sesuai akun ini.");
    }
}
