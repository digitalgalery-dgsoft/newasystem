<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Principle;
use App\Models\Employee;
use App\Services\ActivityLogger;

class PrincipleController extends Controller
{
    public function index(Request $request)
    {
        $query = Principle::withCount(['candidates', 'employees']);

        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('parent_company', 'like', "%{$search}%")
                  ->orWhere('entity', 'like', "%{$search}%")
                  ->orWhere('pic_name', 'like', "%{$search}%");
            });
        }

        if ($entity = $request->input('entity')) {
            $query->where('entity', $entity);
        }

        if ($induk = $request->input('induk')) {
            $query->where('parent_company', $induk);
        }

        if ($request->has('status') && $request->input('status') !== '') {
            $query->where('is_active', $request->boolean('status'));
        }

        $principles = $query->orderBy('entity', 'asc')->orderBy('code', 'asc')->paginate(15)->withQueryString();

        $stats = [
            'total' => Principle::count(),
            'active' => Principle::where('is_active', true)->count(),
            'inactive' => Principle::where('is_active', false)->count(),
            'parents' => Principle::select('parent_company')->distinct()->whereNotNull('parent_company')->count(),
            'total_employees' => Employee::count(),
            'by_entity' => [
                'AMK' => Principle::where('entity', 'AMK')->count(),
                'AKP' => Principle::where('entity', 'AKP')->count(),
                'ATK' => Principle::where('entity', 'ATK')->count(),
                'ABO' => Principle::where('entity', 'ABO')->count(),
                'ATB' => Principle::where('entity', 'ATB')->count(),
            ]
        ];

        $distinctParents = Principle::select('parent_company')->distinct()->whereNotNull('parent_company')->pluck('parent_company');
        $availableEntities = ['AMK', 'AKP', 'ATK', 'ABO', 'ATB'];

        return view('master.prinsiple.index', compact('principles', 'stats', 'distinctParents', 'availableEntities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:principles,code',
            'name' => 'required|string',
            'entity' => 'required|string|in:AMK,AKP,ATK,ABO,ATB',
            'parent_company' => 'required|string',
            'pic_name' => 'nullable|string',
            'pic_email' => 'nullable|email',
            'pic_phone' => 'nullable|string',
        ]);

        $validated['is_active'] = true;
        $principle = Principle::create($validated);

        ActivityLogger::crud('CREATE', 'Master Prinsiple', "Menambahkan prinsiple baru: {$principle->name} ({$principle->code})", $principle, [], $principle->toArray());

        return redirect()->route('master.prinsiple.index')->with('success', 'Data Prinsiple berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $principle = Principle::findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|string|unique:principles,code,' . $id,
            'name' => 'required|string',
            'entity' => 'required|string|in:AMK,AKP,ATK,ABO,ATB',
            'parent_company' => 'required|string',
            'pic_name' => 'nullable|string',
            'pic_email' => 'nullable|email',
            'pic_phone' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        $oldValues = $principle->only(array_keys($validated));
        $principle->update($validated);

        ActivityLogger::crud('UPDATE', 'Master Prinsiple', "Memperbarui data prinsiple: {$principle->name} ({$principle->code})", $principle, $oldValues, $principle->only(array_keys($validated)));

        return redirect()->route('master.prinsiple.index')->with('success', 'Data Prinsiple berhasil diperbarui!');
    }

    public function toggleStatus($id)
    {
        $principle = Principle::findOrFail($id);
        $principle->update(['is_active' => !$principle->is_active]);

        $statusStr = $principle->is_active ? 'diaktifkan' : 'dinonaktifkan';

        ActivityLogger::log('UPDATE', 'Master Prinsiple', "Mengubah status aktif prinsiple {$principle->name} menjadi: " . ($principle->is_active ? 'Aktif' : 'Non-aktif'), $principle, [
            'is_active' => $principle->is_active
        ]);

        return redirect()->route('master.prinsiple.index')->with('info', "Prinsiple {$principle->name} berhasil {$statusStr}.");
    }

    public function destroy($id)
    {
        $principle = Principle::findOrFail($id);
        $oldData = $principle->toArray();
        $principle->delete();

        ActivityLogger::crud('DELETE', 'Master Prinsiple', "Menghapus data prinsiple: {$oldData['name']} ({$oldData['code']})", $principle, $oldData, []);

        return redirect()->route('master.prinsiple.index')->with('success', 'Data Prinsiple berhasil dihapus.');
    }

    public function reimportOfficial()
    {
        \Illuminate\Support\Facades\Artisan::call('asystem:import-principles');

        ActivityLogger::log('IMPORT', 'Master Prinsiple', "Menjalankan re-import official master prinsiple 5 entitas");

        return redirect()->route('master.prinsiple.index')->with('success', 'Data dummy berhasil dibersihkan dan 157 data master prinsiple resmi 5 entitas telah diimpor!');
    }
}