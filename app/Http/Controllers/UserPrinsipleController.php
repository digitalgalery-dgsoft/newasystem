<?php

namespace App\Http\Controllers;

use App\Models\Principle;
use App\Models\User;
use App\Models\UserPrinsiple;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class UserPrinsipleController extends Controller
{
    /**
     * Helper to normalize Indonesian phone numbers to canonical 08xxx format.
     */
    public static function canonicalPhone(?string $phone): string
    {
        if (empty($phone)) {
            return '';
        }
        $digits = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($digits, '62')) {
            $digits = '0' . substr($digits, 2);
        } elseif (str_starts_with($digits, '8')) {
            $digits = '0' . $digits;
        }
        return $digits;
    }

    /**
     * Display a listing of user prinsiples.
     */
    public function index(Request $request): View
    {
        $currentUser = Auth::user();
        $isAdmin = $currentUser && ($currentUser->isAdmin() || $currentUser->role === 'admin');

        $query = UserPrinsiple::query();

        // 1. Data Isolation: Non-admin users ONLY see master user principles added by themselves
        if (!$isAdmin) {
            $query->where('created_by', $currentUser->id);
        } else {
            // Admin filter by creator
            if ($creatorFilter = $request->input('created_by')) {
                if ($creatorFilter === 'mine') {
                    $query->where('created_by', $currentUser->id);
                } elseif ($creatorFilter === 'unassigned') {
                    $query->whereNull('created_by');
                } elseif (is_numeric($creatorFilter)) {
                    $query->where('created_by', $creatorFilter);
                }
            }
        }

        // 2. Search Keyword
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

        // 3. Filter Prinsiple
        if ($prinsiple = $request->input('prinsiple')) {
            $query->where('prinsiple', $prinsiple);
        }

        // 4. Filter Area
        if ($area = $request->input('area')) {
            $query->where('area', $area);
        }

        // 5. Filter Status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $users = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();

        // Metrics Summary Scoped to the viewable dataset
        $statsScope = UserPrinsiple::query();
        if (!$isAdmin) {
            $statsScope->where('created_by', $currentUser->id);
        } elseif ($request->filled('created_by')) {
            $cf = $request->input('created_by');
            if ($cf === 'mine') {
                $statsScope->where('created_by', $currentUser->id);
            } elseif ($cf === 'unassigned') {
                $statsScope->whereNull('created_by');
            } elseif (is_numeric($cf)) {
                $statsScope->where('created_by', $cf);
            }
        }

        $stats = [
            'total'               => (clone $statsScope)->count(),
            'aktif'               => (clone $statsScope)->where('status', 'Aktiv')->count(),
            'distinct_prinsiple'  => (clone $statsScope)->distinct('prinsiple')->count('prinsiple'),
            'distinct_area'       => (clone $statsScope)->whereNotNull('area')->where('area', '!=', '')->distinct('area')->count('area'),
        ];

        // Dropdown options
        $principlesList = Principle::orderBy('name')->get();
        $distinctAreas = (clone $statsScope)->whereNotNull('area')->where('area', '!=', '')->distinct()->pluck('area')->toArray();
        if (!in_array('Nasional', $distinctAreas)) {
            array_unshift($distinctAreas, 'Nasional');
        }

        // List of all recruiters/users for admin filtering
        $allCreators = $isAdmin ? User::orderBy('name')->get(['id', 'name', 'email', 'area', 'role']) : collect();

        return view('userprinsiple.index', compact(
            'users', 
            'stats', 
            'principlesList', 
            'distinctAreas', 
            'isAdmin', 
            'currentUser', 
            'allCreators'
        ));
    }

    /**
     * Store a newly created user prinsiple.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'jabatan'      => 'required|string|max:255',
            'prinsiple'    => 'required|string|max:255',
            'area'         => 'required|string|max:255',
            'email'        => 'required|email|max:255',
            'no_wa'        => 'required|string|max:30',
            'katakunci'    => 'nullable|string|max:50',
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'jabatan.required'      => 'Jabatan wajib diisi.',
            'prinsiple.required'    => 'Prinsiple wajib dipilih.',
            'area.required'         => 'Area wajib diisi.',
            'email.required'        => 'Email resmi wajib diisi.',
            'email.email'           => 'Format email tidak valid.',
            'no_wa.required'        => 'Nomor WhatsApp / No HP wajib diisi.',
        ]);

        $email = strtolower(trim($request->input('email')));
        $rawPhone = trim($request->input('no_wa'));
        $canonicalPhone = self::canonicalPhone($rawPhone);

        // 1. Anti-Duplicate Check: Email
        $existingEmail = UserPrinsiple::whereRaw('LOWER(TRIM(email)) = ?', [$email])->first();
        if ($existingEmail) {
            $owner = $existingEmail->nama_lengkap . ($existingEmail->prinsiple ? " ({$existingEmail->prinsiple})" : "");
            return back()->withInput()->withErrors([
                'email' => "Email '{$email}' sudah terdaftar pada data User Prinsiple an. {$owner}. Email tidak boleh ada duplikat."
            ]);
        }

        // 2. Anti-Duplicate Check: No HP / WhatsApp
        if (strlen($canonicalPhone) < 9) {
            return back()->withInput()->withErrors([
                'no_wa' => "Nomor WhatsApp / HP '{$rawPhone}' tidak valid. Minimal 9 digit angka."
            ]);
        }

        $coreDigits = ltrim($canonicalPhone, '0');
        $phoneMatches = UserPrinsiple::whereNotNull('no_wa')
            ->where('no_wa', '!=', '')
            ->where(function ($q) use ($canonicalPhone, $coreDigits) {
                $q->where('no_wa', 'like', "%{$coreDigits}%")
                  ->orWhere('no_wa', 'like', "%{$canonicalPhone}%");
            })
            ->get(['id', 'nama_lengkap', 'prinsiple', 'no_wa']);

        foreach ($phoneMatches as $item) {
            if (self::canonicalPhone($item->no_wa) === $canonicalPhone) {
                $owner = $item->nama_lengkap . ($item->prinsiple ? " ({$item->prinsiple})" : "");
                return back()->withInput()->withErrors([
                    'no_wa' => "Nomor WhatsApp / HP '{$rawPhone}' sudah terdaftar pada data User Prinsiple an. {$owner}. Nomor HP tidak boleh ada duplikat."
                ]);
            }
        }

        $validated = $request->only(['nama_lengkap', 'jabatan', 'prinsiple', 'area', 'katakunci']);
        $validated['email'] = $email;
        $validated['no_wa'] = $canonicalPhone;
        $validated['created_by'] = Auth::id();
        $validated['status'] = 'Aktiv';

        // Auto generate 6-digit PIN if empty
        if (empty($validated['katakunci'])) {
            $validated['katakunci'] = (string) rand(100000, 999999);
        }

        // Connect to Principle ID if exists
        $principleObj = Principle::where('name', $validated['prinsiple'])->first();
        if ($principleObj) {
            $validated['prinsiple_id'] = $principleObj->id;
        }

        $userPrinsiple = UserPrinsiple::create($validated);

        // Double-sync to legacy userprinsiple table if exists
        if (Schema::hasTable('userprinsiple')) {
            DB::table('userprinsiple')->updateOrInsert(
                ['id' => $userPrinsiple->id],
                [
                    'nama_lengkap' => $userPrinsiple->nama_lengkap,
                    'jabatan'      => $userPrinsiple->jabatan,
                    'prinsiple'    => $userPrinsiple->prinsiple,
                    'email'        => $userPrinsiple->email,
                    'no_wa'        => $userPrinsiple->no_wa,
                    'katakunci'    => $userPrinsiple->katakunci,
                    'area'         => $userPrinsiple->area,
                    'status'       => $userPrinsiple->status,
                    'created_by'   => $userPrinsiple->created_by,
                    'created_at'   => $userPrinsiple->created_at ?? now(),
                ]
            );
        }

        return redirect()
            ->route('userprinsiple.index')
            ->with('success', "Master User Prinsiple an. {$validated['nama_lengkap']} ({$validated['prinsiple']}) berhasil ditambahkan!");
    }

    /**
     * Update the specified user prinsiple.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $currentUser = Auth::user();
        $isAdmin = $currentUser && ($currentUser->isAdmin() || $currentUser->role === 'admin');

        // Isolation: non-admin can only update their own master user principles
        $userPrinsiple = $isAdmin
            ? UserPrinsiple::findOrFail($id)
            : UserPrinsiple::where('created_by', $currentUser->id)->findOrFail($id);

        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'jabatan'      => 'required|string|max:255',
            'prinsiple'    => 'required|string|max:255',
            'area'         => 'required|string|max:255',
            'email'        => 'required|email|max:255',
            'no_wa'        => 'required|string|max:30',
            'katakunci'    => 'nullable|string|max:50',
            'status'       => 'required|string|in:Aktiv,Nonaktif',
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'jabatan.required'      => 'Jabatan wajib diisi.',
            'prinsiple.required'    => 'Prinsiple wajib dipilih.',
            'area.required'         => 'Area wajib diisi.',
            'email.required'        => 'Email resmi wajib diisi.',
            'email.email'           => 'Format email tidak valid.',
            'no_wa.required'        => 'Nomor WhatsApp / HP wajib diisi.',
            'status.required'       => 'Status user wajib dipilih.',
        ]);

        $email = strtolower(trim($request->input('email')));
        $rawPhone = trim($request->input('no_wa'));
        $canonicalPhone = self::canonicalPhone($rawPhone);

        // Anti-Duplicate Check: Email (ignoring current id)
        $existingEmail = UserPrinsiple::where('id', '!=', $id)
            ->whereRaw('LOWER(TRIM(email)) = ?', [$email])
            ->first();
        if ($existingEmail) {
            $owner = $existingEmail->nama_lengkap . ($existingEmail->prinsiple ? " ({$existingEmail->prinsiple})" : "");
            return back()->withInput()->withErrors([
                'email' => "Email '{$email}' sudah digunakan oleh {$owner}. Email tidak boleh ada duplikat."
            ]);
        }

        // Anti-Duplicate Check: No HP (ignoring current id)
        if (strlen($canonicalPhone) < 9) {
            return back()->withInput()->withErrors([
                'no_wa' => "Nomor WhatsApp / HP '{$rawPhone}' tidak valid. Minimal 9 digit angka."
            ]);
        }

        $coreDigits = ltrim($canonicalPhone, '0');
        $phoneMatches = UserPrinsiple::where('id', '!=', $id)
            ->whereNotNull('no_wa')
            ->where('no_wa', '!=', '')
            ->where(function ($q) use ($canonicalPhone, $coreDigits) {
                $q->where('no_wa', 'like', "%{$coreDigits}%")
                  ->orWhere('no_wa', 'like', "%{$canonicalPhone}%");
            })
            ->get(['id', 'nama_lengkap', 'prinsiple', 'no_wa']);

        foreach ($phoneMatches as $item) {
            if (self::canonicalPhone($item->no_wa) === $canonicalPhone) {
                $owner = $item->nama_lengkap . ($item->prinsiple ? " ({$item->prinsiple})" : "");
                return back()->withInput()->withErrors([
                    'no_wa' => "Nomor WhatsApp / HP '{$rawPhone}' sudah terdaftar pada data User Prinsiple an. {$owner}. Nomor HP tidak boleh ada duplikat."
                ]);
            }
        }

        $validated = $request->only(['nama_lengkap', 'jabatan', 'prinsiple', 'area', 'status']);
        $validated['email'] = $email;
        $validated['no_wa'] = $canonicalPhone;

        if ($request->filled('katakunci')) {
            $validated['katakunci'] = $request->input('katakunci');
        }

        $principleObj = Principle::where('name', $validated['prinsiple'])->first();
        $validated['prinsiple_id'] = $principleObj ? $principleObj->id : null;

        $userPrinsiple->update($validated);

        if (Schema::hasTable('userprinsiple')) {
            $syncData = [
                'nama_lengkap' => $userPrinsiple->nama_lengkap,
                'jabatan'      => $userPrinsiple->jabatan,
                'prinsiple'    => $userPrinsiple->prinsiple,
                'email'        => $userPrinsiple->email,
                'no_wa'        => $userPrinsiple->no_wa,
                'area'         => $userPrinsiple->area,
                'status'       => $userPrinsiple->status,
            ];
            if (isset($validated['katakunci'])) {
                $syncData['katakunci'] = $validated['katakunci'];
            }
            DB::table('userprinsiple')->where('id', $id)->update($syncData);
        }

        return redirect()
            ->route('userprinsiple.index')
            ->with('success', "Data User Prinsiple an. {$userPrinsiple->nama_lengkap} berhasil diperbarui!");
    }

    /**
     * Remove the specified user prinsiple.
     */
    public function destroy(int $id): RedirectResponse
    {
        $currentUser = Auth::user();
        $isAdmin = $currentUser && ($currentUser->isAdmin() || $currentUser->role === 'admin');

        $userPrinsiple = $isAdmin
            ? UserPrinsiple::findOrFail($id)
            : UserPrinsiple::where('created_by', $currentUser->id)->findOrFail($id);

        $name = $userPrinsiple->nama_lengkap;
        $userPrinsiple->delete();

        if (Schema::hasTable('userprinsiple')) {
            DB::table('userprinsiple')->where('id', $id)->delete();
        }

        return redirect()
            ->route('userprinsiple.index')
            ->with('success', "User Prinsiple an. {$name} berhasil dihapus dari sistem.");
    }

    /**
     * Simulate sending credentials / notification to the user prinsiple.
     */
    public function sendAccess(Request $request, int $id): RedirectResponse
    {
        $currentUser = Auth::user();
        $isAdmin = $currentUser && ($currentUser->isAdmin() || $currentUser->role === 'admin');

        $userPrinsiple = $isAdmin
            ? UserPrinsiple::findOrFail($id)
            : UserPrinsiple::where('created_by', $currentUser->id)->findOrFail($id);

        return redirect()
            ->route('userprinsiple.index')
            ->with('success', "Notifikasi akses & kredensial login telah berhasil dikirimkan ke {$userPrinsiple->email} (" . ($userPrinsiple->no_wa ? "WhatsApp: {$userPrinsiple->no_wa}" : "Email") . ")!");
    }
}
