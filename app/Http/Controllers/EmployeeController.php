<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Principle;
use App\Models\OdooEntity;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::query();

        // Search
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('nama_karyawan', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('area', 'like', "%{$search}%")
                  ->orWhere('prinsiple', 'like', "%{$search}%")
                  ->orWhere('jabatan', 'like', "%{$search}%")
                  ->orWhere('pimpinan', 'like', "%{$search}%");
            });
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

        // Sort order
        $sortBy = $request->input('sort_by', 'id');
        $sortDir = $request->input('sort_dir', 'desc');
        $query->orderBy($sortBy, $sortDir);

        $employees = $query->paginate(10)->withQueryString();

        // Metric Statistics (Global overview)
        $stats = [
            'total' => Employee::count(),
            'aktif' => Employee::where('status', 'Aktiv')->count(),
            'review' => Employee::where('status', 'Review')->count(),
            'resign' => Employee::where('status', 'Resign')->count(),
            'inhouse' => Employee::where('tipe_karyawan', 'Inhouse')->count(),
            'ratecard' => Employee::where('tipe_karyawan', 'RateCard')->count(),
        ];

        // Dropdown options
        $distinctPrinciples = Principle::orderBy('name')->get();
        $distinctJabatan = Employee::select('jabatan')->distinct()->whereNotNull('jabatan')->orderBy('jabatan')->pluck('jabatan');
        $distinctArea = Employee::select('area')->distinct()->whereNotNull('area')->orderBy('area')->pluck('area');
        $distinctPimpinan = Employee::select('nama_karyawan', 'jabatan', 'area')->whereIn('level', ['SPV', 'HEAD', 'TL'])->orderBy('nama_karyawan')->get();

        $entitiesList = OdooEntity::orderBy('code')->get();
        return view('master.karyawan.index', compact('employees', 'stats', 'distinctPrinciples', 'distinctJabatan', 'distinctArea', 'distinctPimpinan', 'entitiesList', 'status', 'tipe'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nik' => 'required|string|unique:employees,nik',
            'nama_karyawan' => 'required|string|max:255',
            'email' => 'nullable|email',
            'telepon' => 'nullable|string',
            'tanggal_join' => 'required|date',
            'area' => 'required|string',
            'prinsiple' => 'required|string',
            'jabatan' => 'required|string',
            'divisi' => 'nullable|string',
            'pimpinan' => 'nullable|string',
            'tipe_karyawan' => 'nullable|string',
            'entity' => 'nullable|string',
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
            'tanggal_join' => 'required|date',
            'area' => 'required|string',
            'prinsiple' => 'required|string',
            'jabatan' => 'required|string',
            'divisi' => 'nullable|string',
            'pimpinan' => 'nullable|string',
            'status' => 'required|string',
            'tipe_karyawan' => 'nullable|string',
            'entity' => 'nullable|string',
        ]);

        // Automatic Inhouse / RateCard determination
        $validated['tipe_karyawan'] = Employee::determineTipeKaryawan($validated['prinsiple']);
        if (empty($validated['entity'])) {
            $validated['entity'] = Employee::getEntityCodeFromPrinciple($validated['prinsiple']) ?: ($employee->entity ?: 'AMK');
        }

        $employee->update($validated);

        return redirect()->route('master.karyawan.index')->with('success', 'Data Karyawan berhasil diperbarui!');
    }

    public function resign($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->update(['status' => 'Resign']);

        return redirect()->route('master.karyawan.index')->with('info', "Karyawan {$employee->nama_karyawan} berhasil di-resignkan.");
    }

    public function switchUser($nik)
    {
        $employee = Employee::where('nik', $nik)->firstOrFail();
        return redirect()->back()->with('success', "Simulasi Switch Akun aktif sebagai {$employee->nama_karyawan} ({$employee->jabatan}).");
    }
}
