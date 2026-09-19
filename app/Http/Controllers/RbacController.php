<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

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

        // 1. Data Role & Permissions untuk Matriks
        $roles = Role::with('permissions')->orderBy('id', 'asc')->get();
        $permissions = Permission::orderBy('module', 'asc')->orderBy('id', 'asc')->get();
        $permissionsByModule = $permissions->groupBy('module');

        // 2. Data Pengguna (Users / Karyawan Login)
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

        // 3. Ringkasan Metrik
        $totalUsers = User::count();
        $totalRoles = Role::count();
        $totalPermissions = Permission::count();
        $activeUsers = User::where('is_active', 1)->count();

        return view('setting.rbac.index', compact(
            'roles',
            'permissions',
            'permissionsByModule',
            'users',
            'search',
            'roleFilter',
            'statusFilter',
            'totalUsers',
            'totalRoles',
            'totalPermissions',
            'activeUsers'
        ));
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
                // Jangan modifikasi admin agar tidak terkunci dari sistem (Admin selalu memiliki semua izin)
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
     * Perbarui role, status aktif, dan custom permission overrides untuk pengguna individual
     */
    public function updateUserAccess(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'role' => 'required|string|exists:roles,name',
            'is_active' => 'nullable|boolean',
        ]);

        $user->role = $request->role;
        $user->is_active = $request->has('is_active');
        $user->save();

        // Simpan permission overrides individual jika ada
        $customOverrides = $request->input('custom_permissions', []);
        $syncData = [];

        foreach ($customOverrides as $permId => $action) {
            if ($action === 'allow') {
                $syncData[$permId] = ['is_granted' => true];
            } elseif ($action === 'deny') {
                $syncData[$permId] = ['is_granted' => false];
            }
            // jika 'inherit' atau kosong, jangan masukkan ke pivot agar mewarisi role default
        }

        $user->customPermissions()->sync($syncData);

        return redirect()->route('setting.rbac.index', ['tab' => 'users'])
            ->with('success', "Hak akses dan role untuk {$user->name} berhasil diperbarui!");
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
