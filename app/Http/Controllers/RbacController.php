<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Principle;
use App\Models\Employee;
use App\Models\Candidate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RbacController extends Controller
{
    /**
     * Tampilkan halaman utama RBAC (Matriks Role, Manajemen User, dan Daftar Role)
     */
    public function index(Request $request)
    {
        $search = trim($request->query('search', ''));
        $roleFilter = $request->query('role', 'all');
        $statusFilter = $request->query('status', 'all');
        $activeTab = $request->query('tab', 'matrix');

        // 1. Data Role & Permissions untuk Matriks
        $roles = Role::with(['permissions', 'users'])->orderBy('id', 'asc')->get();
        $permissions = Permission::orderBy('module', 'asc')->orderBy('id', 'asc')->get();
        $permissionsByModule = $permissions->groupBy('module');

        // 2. Master Prinsiple & Master Area untuk Picker Scope
        $allPrinciples = Principle::where('name', 'not like', '%BUDGET%')
            ->whereNotNull('name')
            ->where('name', '!=', '')
            ->distinct()
            ->orderBy('name', 'asc')
            ->pluck('name')
            ->toArray();

        // Gabungkan area unik dari employees & candidates
        $empAreas = Employee::whereNotNull('area')->where('area', '!=', '')->distinct()->pluck('area')->toArray();
        $candAreas = Candidate::whereNotNull('area')->where('area', '!=', '')->distinct()->pluck('area')->toArray();
        $allAreas = array_values(array_unique(array_filter(array_merge($empAreas, $candAreas))));
        sort($allAreas, SORT_STRING | SORT_FLAG_CASE);

        // 3. Data Pengguna (Users / Karyawan Login)
        $userQuery = User::with(['roleModel', 'customPermissions']);

        if (!empty($search)) {
            $userQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('area', 'like', "%{$search}%")
                  ->orWhere('job_title', 'like', "%{$search}%");
            });
        }

        if ($roleFilter !== 'all') {
            $userQuery->where('role', $roleFilter);
        }

        if ($statusFilter !== 'all') {
            $userQuery->where('is_active', $statusFilter === '1' ? 1 : 0);
        }

        $users = $userQuery->orderBy('role', 'asc')->orderBy('name', 'asc')->paginate(15)->withQueryString();

        // 4. Ringkasan Metrik
        $totalUsers = User::count();
        $totalRoles = Role::count();
        $totalPermissions = Permission::count();
        $activeUsers = User::where('is_active', 1)->count();

        return view('setting.rbac.index', compact(
            'roles',
            'permissions',
            'permissionsByModule',
            'users',
            'allPrinciples',
            'allAreas',
            'search',
            'roleFilter',
            'statusFilter',
            'activeTab',
            'totalUsers',
            'totalRoles',
            'totalPermissions',
            'activeUsers'
        ));
    }

    /**
     * Tambah Role Baru Secara Dinamis
     */
    public function storeRole(Request $request)
    {
        $request->validate([
            'display_name' => 'required|string|max:100',
            'name' => 'nullable|string|max:50|unique:roles,name',
            'description' => 'nullable|string|max:255',
        ]);

        $name = $request->input('name');
        if (empty($name)) {
            $name = Str::slug($request->display_name, '_');
        } else {
            $name = Str::slug($name, '_');
        }

        // Pastikan unique
        $baseName = $name;
        $counter = 1;
        while (Role::where('name', $name)->exists()) {
            $name = "{$baseName}_{$counter}";
            $counter++;
        }

        $role = Role::create([
            'name' => $name,
            'display_name' => $request->display_name,
            'description' => $request->description,
            'is_system' => false,
            'handle_all_principles' => true,
            'allowed_principles' => null,
            'cover_all_areas' => true,
            'allowed_areas' => null,
        ]);

        // Simpan permission terpilih jika ada
        if ($request->has('permissions') && is_array($request->permissions)) {
            $role->permissions()->sync($request->permissions);
        }

        return redirect()->route('setting.rbac.index', ['tab' => 'roles'])
            ->with('success', "Role baru '{$role->display_name}' ({$role->name}) berhasil ditambahkan! Anda dapat langsung mengatur penugasan user beserta cakupan prinsiple & areanya di Tab Manajemen Pengguna.");
    }

    /**
     * Perbarui Data Role, Deskripsi, dan Hak Akses Modul
     */
    public function updateRole(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $rules = [
            'display_name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
        ];

        // Jika bukan system role, boleh edit kode nama
        if (!$role->is_system && $role->name !== 'admin') {
            $rules['name'] = 'required|string|max:50|unique:roles,name,' . $role->id;
        }

        $request->validate($rules);

        if (!$role->is_system && $role->name !== 'admin' && !empty($request->name)) {
            $oldName = $role->name;
            $newName = Str::slug($request->name, '_');
            $role->name = $newName;

            // Update user yang menggunakan role ini
            if ($oldName !== $newName) {
                User::where('role', $oldName)->update(['role' => $newName]);
            }
        }

        $role->display_name = $request->display_name;
        $role->description = $request->description;
        $role->save();

        if ($request->has('permissions') && is_array($request->permissions)) {
            if ($role->name === 'admin') {
                $role->permissions()->sync(Permission::pluck('id')->toArray());
            } else {
                $role->permissions()->sync($request->permissions);
            }
        }

        return redirect()->route('setting.rbac.index', ['tab' => 'roles'])
            ->with('success', "Data role '{$role->display_name}' berhasil diperbarui!");
    }

    /**
     * Hapus Role Kustom
     */
    public function destroyRole($id)
    {
        $role = Role::findOrFail($id);

        if ($role->is_system || $role->name === 'admin') {
            return redirect()->route('setting.rbac.index', ['tab' => 'roles'])
                ->with('error', "Role bawaan sistem '{$role->display_name}' tidak dapat dihapus!");
        }

        $userCount = User::where('role', $role->name)->count();
        if ($userCount > 0) {
            return redirect()->route('setting.rbac.index', ['tab' => 'roles'])
                ->with('error', "Role '{$role->display_name}' tidak dapat dihapus karena sedang digunakan oleh {$userCount} pengguna aktif. Harap ubah role pengguna tersebut terlebih dahulu.");
        }

        $roleName = $role->display_name;
        $role->permissions()->detach();
        $role->delete();

        return redirect()->route('setting.rbac.index', ['tab' => 'roles'])
            ->with('success', "Role '{$roleName}' berhasil dihapus dari sistem!");
    }

    /**
     * Simpan pembaruan matriks hak akses per role secara massal
     */
    public function updateRoleMatrix(Request $request)
    {
        $matrix = $request->input('matrix', []);
        $allRoles = Role::all();

        DB::transaction(function () use ($allRoles, $matrix) {
            foreach ($allRoles as $role) {
                // Admin selalu memiliki semua izin
                if ($role->name === 'admin') {
                    $allPermIds = Permission::pluck('id')->toArray();
                    $role->permissions()->sync($allPermIds);
                    continue;
                }

                $assignedPermIds = isset($matrix[$role->id]) ? array_keys($matrix[$role->id]) : [];
                $role->permissions()->sync($assignedPermIds);
            }
        });

        return redirect()->route('setting.rbac.index', ['tab' => 'matrix'])
            ->with('success', 'Matriks perizinan hak akses per role berhasil disimpan!');
    }

    /**
     * Perbarui role, status aktif, custom scope, dan permission overrides untuk pengguna individual
     */
    public function updateUserAccess(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'role' => 'required|string|exists:roles,name',
            'is_active' => 'nullable',
            'scope_principle_type' => 'nullable|string|in:all,specific',
            'scope_area_type' => 'nullable|string|in:all,specific',
            'allowed_principles' => 'nullable|array',
            'allowed_areas' => 'nullable|array',
        ]);

        $user->role = $request->role;
        $user->is_active = $request->has('is_active');

        // Pengaturan Scope Prinsiple & Area langsung per Karyawan / Pengguna
        $principleType = $request->input('scope_principle_type') ?? $request->input('user_scope_principle_type', 'all');
        $handleAllPrinciples = ($principleType === 'all');
        $principlesInput = $request->input('allowed_principles') ?? $request->input('user_allowed_principles', []);
        
        $user->handle_all_principles = $handleAllPrinciples;
        $user->allowed_principles = $handleAllPrinciples ? null : array_values(array_filter((array) $principlesInput));

        $areaType = $request->input('scope_area_type') ?? $request->input('user_scope_area_type', 'all');
        $coverAllAreas = ($areaType === 'all');
        $areasInput = $request->input('allowed_areas') ?? $request->input('user_allowed_areas', []);

        $user->cover_all_areas = $coverAllAreas;
        $user->allowed_areas = $coverAllAreas ? null : array_values(array_filter((array) $areasInput));
        $user->scope_override = true;

        $user->save();

        // Simpan permission overrides individual jika ada
        $customOverrides = $request->input('custom_permissions', []);
        if (is_array($customOverrides)) {
            $syncData = [];
            foreach ($customOverrides as $permId => $action) {
                if ($action === 'allow') {
                    $syncData[$permId] = ['is_granted' => true];
                } elseif ($action === 'deny') {
                    $syncData[$permId] = ['is_granted' => false];
                }
            }
            $user->customPermissions()->sync($syncData);
        }

        $principleSummary = $handleAllPrinciples ? 'Semua Prinsiple' : count((array) $user->allowed_principles) . ' Prinsiple';
        $areaSummary = $coverAllAreas ? 'Semua Area' : count((array) $user->allowed_areas) . ' Area';

        return redirect()->route('setting.rbac.index', ['tab' => 'users'])
            ->with('success', "Pengaturan akses untuk {$user->name} berhasil diperbarui (Role: {$user->role}, Prinsiple: {$principleSummary}, Area: {$areaSummary})!");
    }

    /**
     * Reset password cepat untuk user / karyawan
     */
    public function resetUserPassword(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('setting.rbac.index', ['tab' => 'users'])
            ->with('success', "Password untuk pengguna {$user->name} berhasil diperbarui!");
    }
}
