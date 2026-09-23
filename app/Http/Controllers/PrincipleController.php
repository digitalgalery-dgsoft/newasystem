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
        $search = $request->input('search');
        $entity = $request->input('entity');
        $induk = $request->input('induk');
        $status = $request->input('status');

        $applySearch = function($q, $searchStr) {
            $rawLower = strtolower(trim($searchStr));
            $cleanForWords = preg_replace('/[^\p{L}\p{N}]+/u', ' ', $rawLower);
            $words = array_values(array_filter(explode(' ', $cleanForWords), function($w) {
                return strlen($w) >= 2;
            }));
            if (empty($words)) {
                $words = array_values(array_filter(explode(' ', $rawLower)));
            }

            $q->where(function($outer) use ($rawLower, $searchStr, $words) {
                // Direct match on code, name, pic_name, pic_email
                $outer->where(function($exactQ) use ($rawLower, $searchStr) {
                    $exactQ->whereRaw('LOWER(name) LIKE ?', ["%{$rawLower}%"])
                           ->orWhere('code', 'like', "%{$searchStr}%")
                           ->orWhereRaw('LOWER(pic_name) LIKE ?', ["%{$rawLower}%"])
                           ->orWhereRaw('LOWER(pic_email) LIKE ?', ["%{$rawLower}%"]);
                });

                // Multi-word match: each word must match name, code, or pic_name
                if (count($words) > 1) {
                    $outer->orWhere(function($multiQ) use ($words) {
                        foreach ($words as $word) {
                            $multiQ->where(function($wordQ) use ($word) {
                                $wordQ->whereRaw('LOWER(name) LIKE ?', ["%{$word}%"])
                                      ->orWhereRaw('LOWER(code) LIKE ?', ["%{$word}%"])
                                      ->orWhereRaw('LOWER(pic_name) LIKE ?', ["%{$word}%"]);
                            });
                        }
                    });
                }
            });
        };

        $buildBaseQuery = function($withEntity = true) use ($applySearch, $search, $entity, $induk, $status) {
            $q = Principle::withCount(['candidates', 'employees']);

            if (!empty($search)) {
                $applySearch($q, $search);
            }

            if ($withEntity && !empty($entity)) {
                $q->where('entity', $entity);
            }

            if (!empty($induk)) {
                $q->where('parent_company', $induk);
            }

            if ($status !== null && $status !== '') {
                $q->where('is_active', (bool)$status);
            }

            return $q;
        };

        $query = $buildBaseQuery(true);

        $allEntitiesFallback = false;
        $foundInEntities = [];
        $searchedEntity = null;

        // Smart Entity Fallback: If search was provided with a specific entity filter but 0 results were found,
        // automatically search across other entities and present them with a helpful notice banner.
        if (!empty($search) && !empty($entity)) {
            $filteredCount = (clone $query)->count();
            if ($filteredCount === 0) {
                $fallbackQuery = $buildBaseQuery(false);
                $fallbackCount = (clone $fallbackQuery)->count();
                if ($fallbackCount > 0) {
                    $allEntitiesFallback = true;
                    $searchedEntity = $entity;
                    $foundInEntities = array_values(array_unique((clone $fallbackQuery)->pluck('entity')->toArray()));
                    $query = $fallbackQuery;
                }
            }
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

        return view('master.prinsiple.index', compact(
            'principles', 
            'stats', 
            'distinctParents', 
            'availableEntities',
            'allEntitiesFallback',
            'foundInEntities',
            'searchedEntity'
        ));
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