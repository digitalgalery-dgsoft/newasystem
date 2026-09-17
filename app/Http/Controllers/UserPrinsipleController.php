<?php

namespace App\Http\Controllers;

use App\Models\Principle;
use App\Models\UserPrinsiple;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserPrinsipleController extends Controller
{
    /**
     * Display a listing of user prinsiples.
     */
    public function index(Request $request): View
    {
        $query = UserPrinsiple::query();

        // Search Keyword
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('jabatan', 'like', "%{$search}%")
                  ->orWhere('prinsiple', 'like', "%{$search}%")
                  ->orWhere('area', 'like', "%{$search}%")
                  ->orWhere('no_wa', 'like', "%{$search}%");
            });
        }

        // Filter Prinsiple
        if ($prinsiple = $request->input('prinsiple')) {
            $query->where('prinsiple', $prinsiple);
        }

        // Filter Area
        if ($area = $request->input('area')) {
            $query->where('area', $area);
        }

        // Filter Status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $users = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        // Metrics Summary
        $stats = [
            'total'               => UserPrinsiple::count(),
            'aktif'               => UserPrinsiple::where('status', 'Aktiv')->count(),
            'distinct_prinsiple'  => UserPrinsiple::distinct('prinsiple')->count('prinsiple'),
            'distinct_area'       => UserPrinsiple::distinct('area')->count('area'),
        ];

        // Dropdown options
        $principlesList = Principle::orderBy('name')->get();
        $distinctAreas = UserPrinsiple::whereNotNull('area')->distinct()->pluck('area')->toArray();
        if (!in_array('Nasional', $distinctAreas)) {
            array_unshift($distinctAreas, 'Nasional');
        }

        return view('userprinsiple.index', compact('users', 'stats', 'principlesList', 'distinctAreas'));
    }

    /**
     * Store a newly created user prinsiple.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'jabatan'      => 'required|string|max:255',
            'prinsiple'    => 'required|string|max:255',
            'area'         => 'required|string|max:255',
            'email'        => 'required|email|unique:user_prinsiples,email',
            'no_wa'        => 'nullable|string|max:30',
            'katakunci'    => 'nullable|string|max:50',
        ]);

        // Auto generate 6-digit PIN if empty
        if (empty($validated['katakunci'])) {
            $validated['katakunci'] = (string) rand(100000, 999999);
        }

        $validated['status'] = 'Aktiv';

        // Connect to Principle ID if exists
        $principleObj = Principle::where('name', $validated['prinsiple'])->first();
        if ($principleObj) {
            $validated['prinsiple_id'] = $principleObj->id;
        }

        UserPrinsiple::create($validated);

        return redirect()
            ->route('userprinsiple.index')
            ->with('success', "User Prinsiple an. {$validated['nama_lengkap']} ({$validated['prinsiple']}) berhasil ditambahkan!");
    }

    /**
     * Update the specified user prinsiple.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $user = UserPrinsiple::findOrFail($id);

        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'jabatan'      => 'required|string|max:255',
            'prinsiple'    => 'required|string|max:255',
            'area'         => 'required|string|max:255',
            'email'        => 'required|email|unique:user_prinsiples,email,' . $id,
            'no_wa'        => 'nullable|string|max:30',
            'katakunci'    => 'nullable|string|max:50',
            'status'       => 'required|string|in:Aktiv,Nonaktif',
        ]);

        // If password is left blank, keep previous
        if (empty($validated['katakunci'])) {
            unset($validated['katakunci']);
        }

        // Update principle_id
        $principleObj = Principle::where('name', $validated['prinsiple'])->first();
        $validated['prinsiple_id'] = $principleObj ? $principleObj->id : null;

        $user->update($validated);

        return redirect()
            ->route('userprinsiple.index')
            ->with('success', "Data User Prinsiple an. {$user->nama_lengkap} berhasil diperbarui!");
    }

    /**
     * Remove the specified user prinsiple.
     */
    public function destroy(int $id): RedirectResponse
    {
        $user = UserPrinsiple::findOrFail($id);
        $name = $user->nama_lengkap;
        $user->delete();

        return redirect()
            ->route('userprinsiple.index')
            ->with('success', "User Prinsiple an. {$name} berhasil dihapus dari sistem.");
    }

    /**
     * Simulate sending credentials / notification to the user prinsiple.
     */
    public function sendAccess(Request $request, int $id): RedirectResponse
    {
        $user = UserPrinsiple::findOrFail($id);

        return redirect()
            ->route('userprinsiple.index')
            ->with('success', "Notifikasi akses & kredensial login telah berhasil dikirimkan ke {$user->email} (" . ($user->no_wa ? "WhatsApp: {$user->no_wa}" : "Email") . ")!");
    }
}
