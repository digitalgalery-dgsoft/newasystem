@extends('layouts.app')

@section('title', 'Hak Akses Pengguna & Role Dinamis (RBAC) - ASystem')

@section('content')
<div class="space-y-6" x-data="{
    activeTab: '{{ $activeTab ?? 'matrix' }}',
    newRoleModal: false,
    editRoleModal: false,
    editUserModal: false,
    resetPasswordModal: false,
    selectedRole: {
        id: null,
        name: '',
        display_name: '',
        description: '',
        is_system: false,
        scope_principle_type: 'all',
        allowed_principles: [],
        scope_area_type: 'all',
        allowed_areas: []
    },
    selectedUser: {
        id: null,
        name: '',
        email: '',
        role: '',
        is_active: true,
        scope_override: false,
        user_scope_principle_type: 'all',
        user_allowed_principles: [],
        user_scope_area_type: 'all',
        user_allowed_areas: []
    },
    allPrinciples: {{ json_encode($allPrinciples) }},
    allAreas: {{ json_encode($allAreas) }},
    searchPrincipleNew: '',
    searchAreaNew: '',
    searchPrincipleEdit: '',
    searchAreaEdit: '',
    searchPrincipleUser: '',
    searchAreaUser: '',

    openEditRole(role) {
        this.selectedRole = {
            id: role.id,
            name: role.name,
            display_name: role.display_name,
            description: role.description || '',
            is_system: !!role.is_system,
            scope_principle_type: role.handle_all_principles ? 'all' : 'specific',
            allowed_principles: Array.isArray(role.allowed_principles) ? [...role.allowed_principles] : [],
            scope_area_type: role.cover_all_areas ? 'all' : 'specific',
            allowed_areas: Array.isArray(role.allowed_areas) ? [...role.allowed_areas] : []
        };
        this.searchPrincipleEdit = '';
        this.searchAreaEdit = '';
        this.editRoleModal = true;
    },

    openEditUser(user) {
        this.selectedUser = {
            id: user.id,
            name: user.name,
            email: user.email,
            role: user.role,
            is_active: !!user.is_active,
            scope_override: !!user.scope_override,
            user_scope_principle_type: user.handle_all_principles ? 'all' : 'specific',
            user_allowed_principles: Array.isArray(user.allowed_principles) ? [...user.allowed_principles] : [],
            user_scope_area_type: user.cover_all_areas ? 'all' : 'specific',
            user_allowed_areas: Array.isArray(user.allowed_areas) ? [...user.allowed_areas] : []
        };
        this.searchPrincipleUser = '';
        this.searchAreaUser = '';
        this.editUserModal = true;
    },

    openResetPassword(user) {
        this.selectedUser = {
            id: user.id,
            name: user.name,
            email: user.email
        };
        this.resetPasswordModal = true;
    }
}">

    <!-- ALERT MESSAGES -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2.5">
                <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    @if(session('info'))
        <div class="p-4 rounded-2xl bg-blue-50 border border-blue-200 text-blue-800 text-xs font-semibold flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2.5">
                <i class="fa-solid fa-circle-info text-blue-600 text-base"></i>
                <span>{{ session('info') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-blue-500 hover:text-blue-700">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    @if(session('error') || (isset($errors) && $errors->any()))
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2.5">
                <i class="fa-solid fa-triangle-exclamation text-rose-600 text-base"></i>
                <div>
                    <span>{{ session('error') ?? 'Terjadi kesalahan saat memproses data:' }}</span>
                    @if(isset($errors) && $errors->any())
                        <ul class="list-disc pl-5 mt-1 text-[11px] font-normal">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    <!-- PAGE HEADER -->
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-slate-900 via-indigo-900 to-primary text-white flex items-center justify-center text-2xl shadow-lg shadow-indigo-500/20 flex-shrink-0">
                <i class="fa-solid fa-user-shield"></i>
            </div>
            <div>
                <div class="flex items-center gap-2 mb-1 flex-wrap">
                    <h1 class="text-xl font-black text-slate-900 tracking-tight">Hak Akses Pengguna & Role Dinamis (RBAC)</h1>
                    <span class="px-2.5 py-0.5 rounded-full bg-indigo-50 text-indigo-700 font-bold text-[10px] border border-indigo-200">Role-Based Access Control</span>
                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-bold text-[10px] border border-emerald-200">Scope Prinsiple & Area</span>
                </div>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Kelola peran dinamis, matriks izin modul, serta pengaturan cakupan prinsiple dan area kerja karyawan agar data tampil sesuai dengan otorisasi rolenya.
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2.5 self-end md:self-auto flex-wrap">
            <button type="button" @click="newRoleModal = true" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-primary to-indigo-600 hover:from-primary-700 hover:to-indigo-700 text-white font-bold text-xs shadow-md shadow-primary/25 transition-all flex items-center gap-2">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Tambah Role Baru</span>
            </button>
            <a href="{{ route('odoo.setting.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs transition-all flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-arrows-rotate text-primary"></i>
                <span>Setting Sync Odoo</span>
            </a>
        </div>
    </div>

    <!-- 4 STAT METRIC CARDS -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Total Users -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center text-lg flex-shrink-0 border border-indigo-100">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <div class="text-xl font-black text-slate-900">{{ $totalUsers }}</div>
                <div class="text-[11px] font-semibold text-slate-500">Total Akun Pengguna</div>
            </div>
        </div>

        <!-- 2. Total Roles -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center text-lg flex-shrink-0 border border-purple-100">
                <i class="fa-solid fa-id-badge"></i>
            </div>
            <div>
                <div class="text-xl font-black text-purple-700">{{ $totalRoles }}</div>
                <div class="text-[11px] font-semibold text-slate-500">Role Terkonfigurasi</div>
            </div>
        </div>

        <!-- 3. Total Permissions -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center text-lg flex-shrink-0 border border-blue-100">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div>
                <div class="text-xl font-black text-blue-700">{{ $totalPermissions }}</div>
                <div class="text-[11px] font-semibold text-slate-500">Modul Perizinan</div>
            </div>
        </div>

        <!-- 4. Active Logins -->
        <div class="bg-white rounded-2xl p-4 border border-emerald-200/80 bg-emerald-50/20 shadow-sm flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-lg flex-shrink-0 border border-emerald-200">
                <i class="fa-solid fa-user-check"></i>
            </div>
            <div>
                <div class="text-xl font-black text-emerald-700">{{ $activeUsers }}</div>
                <div class="text-[11px] font-semibold text-emerald-600">Pengguna Aktif Login</div>
            </div>
        </div>
    </div>

    <!-- TAB NAVIGATION HEADER -->
    <div class="bg-white rounded-2xl p-1.5 border border-slate-200 shadow-sm flex items-center justify-between gap-1.5 overflow-x-auto">
        <div class="flex items-center gap-1.5">
            <button type="button" 
                    @click="activeTab = 'matrix'" 
                    :class="activeTab === 'matrix' ? 'bg-slate-900 text-white shadow-md' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
                    class="px-5 py-2.5 rounded-xl font-bold text-xs transition-all flex items-center gap-2 whitespace-nowrap">
                <i class="fa-solid fa-table-cells text-xs"></i>
                <span>Matriks Hak Akses Role</span>
            </button>

            <button type="button" 
                    @click="activeTab = 'users'" 
                    :class="activeTab === 'users' ? 'bg-slate-900 text-white shadow-md' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
                    class="px-5 py-2.5 rounded-xl font-bold text-xs transition-all flex items-center gap-2 whitespace-nowrap">
                <i class="fa-solid fa-users-gear text-xs"></i>
                <span>Manajemen Pengguna & Karyawan</span>
                <span class="px-1.5 py-0.5 rounded-md text-[10px] font-black" :class="activeTab === 'users' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700'">{{ $totalUsers }}</span>
            </button>

            <button type="button" 
                    @click="activeTab = 'roles'" 
                    :class="activeTab === 'roles' ? 'bg-slate-900 text-white shadow-md' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
                    class="px-5 py-2.5 rounded-xl font-bold text-xs transition-all flex items-center gap-2 whitespace-nowrap">
                <i class="fa-solid fa-tags text-xs"></i>
                <span>Katalog Role & Scope</span>
                <span class="px-1.5 py-0.5 rounded-md text-[10px] font-black" :class="activeTab === 'roles' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700'">{{ $totalRoles }}</span>
            </button>
        </div>

        <button type="button" @click="newRoleModal = true" class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-primary hover:bg-primary-50 transition-all mr-1">
            <i class="fa-solid fa-plus text-[10px]"></i>
            <span>Tambah Role</span>
        </button>
    </div>

    <!-- TAB 1: MATRIKS HAK AKSES PER ROLE -->
    <div x-show="activeTab === 'matrix'" x-cloak class="space-y-4">
        <form method="POST" action="{{ route('setting.rbac.matrix.update') }}">
            @csrf

            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                    <div>
                        <h3 class="text-sm font-black text-slate-900">Matriks Hak Akses Modul per Role</h3>
                        <p class="text-[11px] text-slate-500">Centang kotak untuk memberikan izin akses modul kepada role terkait. Perubahan akan berlaku secara instan.</p>
                    </div>

                    <div class="flex items-center gap-2.5">
                        <button type="button" @click="newRoleModal = true" class="px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs transition-all flex items-center gap-1.5">
                            <i class="fa-solid fa-plus text-xs"></i>
                            <span>Role Baru</span>
                        </button>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-primary hover:bg-primary-700 text-white font-bold text-xs shadow-md shadow-primary/25 transition-all flex items-center gap-2">
                            <i class="fa-solid fa-floppy-disk text-xs"></i>
                            <span>Simpan Perubahan Matriks</span>
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/90 border-b border-slate-200 text-[11px] font-black text-slate-600 uppercase tracking-wider">
                                <th class="py-4 px-4 w-1/3 min-w-[280px]">Modul & Nama Perizinan</th>
                                @foreach($roles as $role)
                                    <th class="py-3 px-3 text-center min-w-[150px] border-l border-slate-100">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <span class="font-bold text-slate-900 text-xs truncate max-w-[130px]">{{ $role->display_name }}</span>
                                            <button type="button" 
                                                    @click="openEditRole({{ json_encode($role) }})" 
                                                    title="Edit Scope & Info Role"
                                                    class="text-slate-400 hover:text-primary p-0.5">
                                                <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                                            </button>
                                        </div>
                                        <div class="text-[10px] text-slate-400 font-mono">({{ $role->name }})</div>
                                        
                                        <!-- Scope Badges -->
                                        <div class="flex flex-col items-center gap-0.5 mt-1.5">
                                            @if($role->handlesAllPrinciples())
                                                <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-emerald-50 text-emerald-700 border border-emerald-200">Semua Prinsiple</span>
                                            @else
                                                <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-indigo-50 text-indigo-700 border border-indigo-200" title="{{ implode(', ', $role->getEffectivePrinciples()) }}">
                                                    {{ count($role->getEffectivePrinciples()) }} Prinsiple
                                                </span>
                                            @endif

                                            @if($role->coversAllAreas())
                                                <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-blue-50 text-blue-700 border border-blue-200">Semua Area</span>
                                            @else
                                                <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-amber-50 text-amber-700 border border-amber-200" title="{{ implode(', ', $role->getEffectiveAreas()) }}">
                                                    {{ count($role->getEffectiveAreas()) }} Area
                                                </span>
                                            @endif
                                        </div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs">
                            @foreach($permissionsByModule as $moduleName => $perms)
                                <!-- Module Header Row -->
                                <tr class="bg-slate-100/70">
                                    <td colspan="{{ count($roles) + 1 }}" class="py-2 px-4 font-black text-[11px] text-slate-700 uppercase tracking-wider flex items-center gap-2">
                                        <i class="fa-solid fa-folder-open text-primary text-xs"></i>
                                        <span>Modul: {{ $moduleName }}</span>
                                    </td>
                                </tr>

                                @foreach($perms as $p)
                                    <tr class="hover:bg-slate-50/70 transition-colors">
                                        <td class="py-3 px-4">
                                            <div class="font-bold text-slate-900 text-xs">{{ $p->display_name }}</div>
                                            <div class="text-[11px] text-slate-400 mt-0.5">{{ $p->description }}</div>
                                            <code class="text-[10px] text-slate-500 font-mono bg-slate-100 px-1.5 py-0.2 rounded mt-1 inline-block">{{ $p->name }}</code>
                                        </td>

                                        @foreach($roles as $role)
                                            <td class="py-3 px-3 text-center align-middle border-l border-slate-100">
                                                @php
                                                    $isAssigned = $role->permissions->contains('id', $p->id);
                                                    $isAdminRole = ($role->name === 'admin');
                                                @endphp

                                                @if($isAdminRole)
                                                    <!-- Super Admin selalu diizinkan (Readonly checkmark) -->
                                                    <div class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700" title="Super Admin memiliki akses penuh">
                                                        <i class="fa-solid fa-check text-xs font-black"></i>
                                                    </div>
                                                @else
                                                    <label class="inline-flex items-center cursor-pointer p-1">
                                                        <input type="checkbox" 
                                                               name="matrix[{{ $role->id }}][{{ $p->id }}]" 
                                                               value="1"
                                                               {{ $isAssigned ? 'checked' : '' }}
                                                               class="w-5 h-5 text-primary rounded-lg border-slate-300 focus:ring-primary transition-all">
                                                    </label>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end">
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-primary hover:bg-primary-700 text-white font-bold text-xs shadow-md shadow-primary/25 transition-all flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk text-xs"></i>
                        <span>Simpan Perubahan Matriks</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- TAB 2: MANAJEMEN PENGGUNA & KARYAWAN -->
    <div x-show="activeTab === 'users'" x-cloak class="space-y-4">
        <!-- FILTER BAR -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm">
            <form method="GET" action="{{ route('setting.rbac.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
                <input type="hidden" name="tab" value="users">

                <!-- Search -->
                <div class="relative flex-1 w-full">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" 
                           name="search" 
                           value="{{ $search }}" 
                           placeholder="Cari pengguna berdasarkan nama, email, jabatan, atau area..." 
                           class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:bg-white focus:outline-none focus:border-primary transition-all">
                </div>

                <!-- Role Filter -->
                <div class="w-full sm:w-52">
                    <select name="role" 
                            onchange="this.form.submit()"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:bg-white focus:outline-none focus:border-primary">
                        <option value="all" {{ $roleFilter === 'all' ? 'selected' : '' }}>Semua Role</option>
                        @foreach($roles as $r)
                            <option value="{{ $r->name }}" {{ $roleFilter === $r->name ? 'selected' : '' }}>{{ $r->display_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div class="w-full sm:w-44">
                    <select name="status" 
                            onchange="this.form.submit()"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:bg-white focus:outline-none focus:border-primary">
                        <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="1" {{ $statusFilter === '1' ? 'selected' : '' }}>Aktif Login</option>
                        <option value="0" {{ $statusFilter === '0' ? 'selected' : '' }}>Non-Aktif</option>
                    </select>
                </div>

                <button type="submit" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-filter text-xs"></i>
                    <span>Filter</span>
                </button>

                @if(!empty($search) || $roleFilter !== 'all' || $statusFilter !== 'all')
                    <a href="{{ route('setting.rbac.index', ['tab' => 'users']) }}" class="w-full sm:w-auto px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs transition-all flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-rotate-left text-xs"></i>
                        <span>Reset</span>
                    </a>
                @endif
            </form>
        </div>

        <!-- USERS TABLE -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-black text-slate-900">Daftar Akun Pengguna & Karyawan</h3>
                    <p class="text-[11px] text-slate-500">Kelola role, cakupan prinsiple/area, dan perizinan spesifik untuk setiap user.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-[11px] font-black text-slate-500 uppercase tracking-wider">
                            <th class="py-3.5 px-4 w-12 text-center">ID</th>
                            <th class="py-3.5 px-4">Nama & Email Pengguna</th>
                            <th class="py-3.5 px-4">Jabatan & Area Asal</th>
                            <th class="py-3.5 px-3 text-center">Role Akses</th>
                            <th class="py-3.5 px-4">Scope Kerja (Prinsiple & Area)</th>
                            <th class="py-3.5 px-3 text-center">Status Login</th>
                            <th class="py-3.5 px-4 text-center w-36">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @forelse($users as $u)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3.5 px-4 text-center font-mono text-slate-400 font-bold">
                                    {{ $u->id }}
                                </td>

                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900">{{ $u->name }}</div>
                                    <div class="text-[11px] text-slate-400 flex items-center gap-1.5 mt-0.5">
                                        <i class="fa-regular fa-envelope text-[10px]"></i>
                                        <span>{{ $u->email }}</span>
                                    </div>
                                </td>

                                <td class="py-3.5 px-4">
                                    <div class="font-semibold text-slate-800">{{ $u->job_title ?: 'Karyawan' }}</div>
                                    <div class="text-[11px] text-slate-400 mt-0.5 flex items-center gap-1">
                                        <i class="fa-solid fa-location-dot text-[10px]"></i>
                                        <span>{{ $u->area ?: 'Semua Area' }}</span>
                                    </div>
                                </td>

                                <td class="py-3.5 px-3 text-center">
                                    @php
                                        $roleBadgeClasses = [
                                            'admin' => 'bg-purple-100 text-purple-800 border-purple-200',
                                            'recruiter' => 'bg-blue-100 text-blue-800 border-blue-200',
                                            'head_hr' => 'bg-amber-100 text-amber-800 border-amber-200',
                                            'karyawan_inhouse' => 'bg-slate-100 text-slate-800 border-slate-200',
                                            'karyawan_ratecard' => 'bg-zinc-100 text-zinc-700 border-zinc-200',
                                        ];
                                        $badgeCls = $roleBadgeClasses[$u->role] ?? 'bg-indigo-100 text-indigo-800 border-indigo-200';
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $badgeCls }}">
                                        {{ $u->roleModel->display_name ?? strtoupper($u->role) }}
                                    </span>

                                    @if($u->customPermissions->count() > 0)
                                        <div class="text-[9px] font-bold text-primary mt-1">
                                            +{{ $u->customPermissions->count() }} Izin Kustom
                                        </div>
                                    @endif
                                </td>

                                <td class="py-3.5 px-4">
                                    <div class="space-y-1">
                                        <!-- Prinsiple Badge -->
                                        <div class="flex items-center gap-1">
                                            <span class="text-[10px] text-slate-400 font-semibold w-14">Prinsiple:</span>
                                            @if($u->handlesAllPrinciples())
                                                <span class="text-[10px] font-bold px-1.5 py-0.2 rounded bg-emerald-50 text-emerald-700 border border-emerald-200">Semua Prinsiple</span>
                                            @else
                                                <span class="text-[10px] font-bold px-1.5 py-0.2 rounded bg-indigo-50 text-indigo-700 border border-indigo-200" title="{{ implode(', ', $u->getEffectivePrinciples()) }}">
                                                    {{ count($u->getEffectivePrinciples()) }} Prinsiple
                                                </span>
                                            @endif
                                            @if($u->scope_override)
                                                <span class="text-[9px] text-amber-600 font-bold bg-amber-50 px-1 rounded border border-amber-200">Kustom</span>
                                            @endif
                                        </div>

                                        <!-- Area Badge -->
                                        <div class="flex items-center gap-1">
                                            <span class="text-[10px] text-slate-400 font-semibold w-14">Area:</span>
                                            @if($u->coversAllAreas())
                                                <span class="text-[10px] font-bold px-1.5 py-0.2 rounded bg-blue-50 text-blue-700 border border-blue-200">Semua Area</span>
                                            @else
                                                <span class="text-[10px] font-bold px-1.5 py-0.2 rounded bg-purple-50 text-purple-700 border border-purple-200" title="{{ implode(', ', $u->getEffectiveAreas()) }}">
                                                    {{ count($u->getEffectiveAreas()) }} Area
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <td class="py-3.5 px-3 text-center">
                                    @if($u->is_active)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            <span>Aktif</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                            <span>Non-Aktif</span>
                                        </span>
                                    @endif
                                </td>

                                <td class="py-3.5 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button type="button" 
                                                @click="openEditUser({{ json_encode($u) }})"
                                                class="px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-primary-50 text-slate-700 hover:text-primary font-bold text-[11px] transition-all flex items-center gap-1">
                                            <i class="fa-solid fa-user-pen text-[10px]"></i>
                                            <span>Atur Akses</span>
                                        </button>

                                        <button type="button" 
                                                @click="openResetPassword({{ json_encode($u->only(['id', 'name', 'email'])) }})"
                                                title="Reset Password"
                                                class="w-7 h-7 rounded-xl bg-slate-100 hover:bg-amber-50 text-slate-500 hover:text-amber-600 transition-all inline-flex items-center justify-center">
                                            <i class="fa-solid fa-key text-[10px]"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400">
                                    <i class="fa-solid fa-user-slash text-3xl mb-2 block"></i>
                                    <p class="text-xs font-semibold">Tidak ada data pengguna yang cocok dengan filter pencarian.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- TAB 3: KATALOG ROLE & SCOPE -->
    <div x-show="activeTab === 'roles'" x-cloak class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm font-black text-slate-900">Katalog Role & Pengaturan Scope Cakupan Kerja</h3>
                <p class="text-[11px] text-slate-500">Daftar seluruh role pengguna berserta cakupan prinsiple dan area penempatan kerja.</p>
            </div>
            <button type="button" @click="newRoleModal = true" class="px-4 py-2 rounded-xl bg-primary hover:bg-primary-700 text-white font-bold text-xs shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Tambah Role Baru</span>
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($roles as $r)
                <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow">
                    <div>
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div class="flex items-center gap-2.5">
                                <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-700 flex items-center justify-center text-lg font-black flex-shrink-0">
                                    <i class="fa-solid fa-id-card-clip"></i>
                                </div>
                                <div>
                                    <h4 class="font-black text-slate-900 text-sm">{{ $r->display_name }}</h4>
                                    <div class="flex items-center gap-1.5 mt-0.5">
                                        <code class="text-[10px] text-slate-400 font-mono">{{ $r->name }}</code>
                                        @if($r->is_system)
                                            <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-slate-100 text-slate-600">Sistem</span>
                                        @else
                                            <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-purple-50 text-purple-700">Kustom</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                                {{ $r->users()->count() }} User
                            </span>
                        </div>

                        <p class="text-xs text-slate-500 leading-relaxed mb-4">
                            {{ $r->description ?: 'Tidak ada deskripsi khusus untuk role ini.' }}
                        </p>

                        <!-- Scope Summary -->
                        <div class="bg-slate-50 rounded-2xl p-3 border border-slate-100 space-y-2 mb-4">
                            <div>
                                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1 flex items-center gap-1">
                                    <i class="fa-solid fa-building text-primary text-[10px]"></i>
                                    <span>Prinsiple Dihandle:</span>
                                </div>
                                @if($r->handlesAllPrinciples())
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800">
                                        Semua Prinsiple (Tanpa Batasan)
                                    </span>
                                @else
                                    <div class="flex flex-wrap gap-1">
                                        @foreach(array_slice($r->getEffectivePrinciples(), 0, 4) as $pName)
                                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-md bg-white border border-slate-200 text-slate-700">
                                                {{ $pName }}
                                            </span>
                                        @endforeach
                                        @if(count($r->getEffectivePrinciples()) > 4)
                                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-md bg-indigo-50 text-indigo-700">
                                                +{{ count($r->getEffectivePrinciples()) - 4 }} Lainnya
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <div class="pt-2 border-t border-slate-200/60">
                                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1 flex items-center gap-1">
                                    <i class="fa-solid fa-location-dot text-rose-500 text-[10px]"></i>
                                    <span>Area Cover:</span>
                                </div>
                                @if($r->coversAllAreas())
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-blue-100 text-blue-800">
                                        Semua Area (Nasional)
                                    </span>
                                @else
                                    <div class="flex flex-wrap gap-1">
                                        @foreach(array_slice($r->getEffectiveAreas(), 0, 5) as $aName)
                                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-md bg-white border border-slate-200 text-slate-700">
                                                {{ $aName }}
                                            </span>
                                        @endforeach
                                        @if(count($r->getEffectiveAreas()) > 5)
                                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-md bg-purple-50 text-purple-700">
                                                +{{ count($r->getEffectiveAreas()) - 5 }} Lainnya
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                        <div class="text-[11px] font-semibold text-slate-600">
                            <span>Izin: </span>
                            <span class="font-black text-primary">{{ $r->permissions->count() }} Modul</span>
                        </div>

                        <div class="flex items-center gap-1.5">
                            <button type="button" 
                                    @click="openEditRole({{ json_encode($r) }})"
                                    class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-primary-50 text-slate-700 hover:text-primary font-bold text-[11px] transition-all flex items-center gap-1">
                                <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                                <span>Edit Scope</span>
                            </button>

                            @if(!$r->is_system && $r->name !== 'admin')
                                <form action="{{ route('setting.rbac.role.destroy', $r->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus role \'{{ $r->display_name }}\'? Role tidak dapat dihapus jika sedang digunakan user.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Hapus Role" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-rose-50 text-slate-400 hover:text-rose-600 transition-all inline-flex items-center justify-center">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- MODAL 1: TAMBAH ROLE BARU -->
    <div x-show="newRoleModal" x-cloak class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-7 shadow-2xl border border-slate-100 my-8 max-h-[90vh] flex flex-col">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-4 flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-indigo-50 text-indigo-700 flex items-center justify-center text-lg font-black">
                        <i class="fa-solid fa-plus"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-slate-900">Tambah Role Akses Baru</h3>
                        <p class="text-xs text-slate-500">Definisikan peran baru beserta prinsiple yang dihandle dan area cover-nya.</p>
                    </div>
                </div>
                <button type="button" @click="newRoleModal = false" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>

            <form action="{{ route('setting.rbac.role.store') }}" method="POST" class="overflow-y-auto pr-1 flex-1 space-y-4 text-xs" x-data="{
                newScopePrinciple: 'all',
                newScopeArea: 'all',
                selectedPrinciples: [],
                selectedAreas: [],
                toggleAllPrinciples() {
                    if (this.selectedPrinciples.length === allPrinciples.length) {
                        this.selectedPrinciples = [];
                    } else {
                        this.selectedPrinciples = [...allPrinciples];
                    }
                },
                toggleAllAreas() {
                    if (this.selectedAreas.length === allAreas.length) {
                        this.selectedAreas = [];
                    } else {
                        this.selectedAreas = [...allAreas];
                    }
                }
            }">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Nama Tampilan Role <span class="text-rose-500">*</span></label>
                        <input type="text" name="display_name" required placeholder="Contoh: Recruiter Area Surabaya" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:outline-none focus:border-primary">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Kode Identitas (Slug) <span class="text-slate-400 font-normal text-[11px]">(Opsional)</span></label>
                        <input type="text" name="name" placeholder="contoh: recruiter_surabaya" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:outline-none focus:border-primary">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Deskripsi Tanggung Jawab Role</label>
                    <textarea name="description" rows="2" placeholder="Jelaskan ruang lingkup atau tanggung jawab peran ini..." class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:bg-white focus:outline-none focus:border-primary"></textarea>
                </div>

                <!-- SCOPE 1: PENGATURAN PRINSIPLE DIHANDLE -->
                <div class="p-4 rounded-2xl bg-indigo-50/40 border border-indigo-100 space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="font-bold text-slate-800 text-xs flex items-center gap-1.5">
                            <i class="fa-solid fa-building text-indigo-600"></i>
                            <span>Prinsiple yang Dihandle</span>
                        </div>
                        <span class="text-[10px] text-indigo-700 font-bold" x-text="newScopePrinciple === 'all' ? 'Semua Prinsiple' : selectedPrinciples.length + ' Dipilih'"></span>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <label class="p-2.5 rounded-xl border flex items-center gap-2 cursor-pointer transition-all" :class="newScopePrinciple === 'all' ? 'bg-indigo-600 text-white border-indigo-600 font-bold' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'">
                            <input type="radio" name="scope_principle_type" value="all" x-model="newScopePrinciple" class="sr-only">
                            <i class="fa-solid fa-earth-americas text-xs"></i>
                            <span>Semua Prinsiple</span>
                        </label>
                        <label class="p-2.5 rounded-xl border flex items-center gap-2 cursor-pointer transition-all" :class="newScopePrinciple === 'specific' ? 'bg-indigo-600 text-white border-indigo-600 font-bold' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'">
                            <input type="radio" name="scope_principle_type" value="specific" x-model="newScopePrinciple" class="sr-only">
                            <i class="fa-solid fa-list-check text-xs"></i>
                            <span>Pilih Prinsiple Tertentu</span>
                        </label>
                    </div>

                    <!-- Specific Principle Checklist -->
                    <div x-show="newScopePrinciple === 'specific'" x-cloak class="space-y-2 pt-2 border-t border-indigo-100">
                        <div class="flex items-center gap-2">
                            <input type="text" x-model="searchPrincipleNew" placeholder="Cari nama prinsiple..." class="flex-1 px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-medium focus:outline-none focus:border-indigo-500">
                            <button type="button" @click="toggleAllPrinciples()" class="px-2.5 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 font-bold text-[11px] whitespace-nowrap">
                                <span x-text="selectedPrinciples.length === allPrinciples.length ? 'Batal Semua' : 'Pilih Semua'"></span>
                            </button>
                        </div>

                        <div class="max-h-40 overflow-y-auto bg-white rounded-xl border border-slate-200 p-2 divide-y divide-slate-100">
                            <template x-for="p in allPrinciples.filter(item => item.toLowerCase().includes(searchPrincipleNew.toLowerCase()))" :key="p">
                                <label class="py-1.5 px-2 flex items-center gap-2.5 hover:bg-slate-50 rounded-lg cursor-pointer">
                                    <input type="checkbox" name="allowed_principles[]" :value="p" x-model="selectedPrinciples" class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                                    <span class="text-xs text-slate-800 font-medium" x-text="p"></span>
                                </label>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- SCOPE 2: PENGATURAN AREA COVER -->
                <div class="p-4 rounded-2xl bg-blue-50/40 border border-blue-100 space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="font-bold text-slate-800 text-xs flex items-center gap-1.5">
                            <i class="fa-solid fa-location-dot text-blue-600"></i>
                            <span>Area Cover Penempatan</span>
                        </div>
                        <span class="text-[10px] text-blue-700 font-bold" x-text="newScopeArea === 'all' ? 'Semua Area' : selectedAreas.length + ' Dipilih'"></span>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <label class="p-2.5 rounded-xl border flex items-center gap-2 cursor-pointer transition-all" :class="newScopeArea === 'all' ? 'bg-blue-600 text-white border-blue-600 font-bold' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'">
                            <input type="radio" name="scope_area_type" value="all" x-model="newScopeArea" class="sr-only">
                            <i class="fa-solid fa-map text-xs"></i>
                            <span>Semua Area (Nasional)</span>
                        </label>
                        <label class="p-2.5 rounded-xl border flex items-center gap-2 cursor-pointer transition-all" :class="newScopeArea === 'specific' ? 'bg-blue-600 text-white border-blue-600 font-bold' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'">
                            <input type="radio" name="scope_area_type" value="specific" x-model="newScopeArea" class="sr-only">
                            <i class="fa-solid fa-location-crosshairs text-xs"></i>
                            <span>Pilih Area Tertentu</span>
                        </label>
                    </div>

                    <!-- Specific Area Checklist -->
                    <div x-show="newScopeArea === 'specific'" x-cloak class="space-y-2 pt-2 border-t border-blue-100">
                        <div class="flex items-center gap-2">
                            <input type="text" x-model="searchAreaNew" placeholder="Cari nama kota/area..." class="flex-1 px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-medium focus:outline-none focus:border-blue-500">
                            <button type="button" @click="toggleAllAreas()" class="px-2.5 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 font-bold text-[11px] whitespace-nowrap">
                                <span x-text="selectedAreas.length === allAreas.length ? 'Batal Semua' : 'Pilih Semua'"></span>
                            </button>
                        </div>

                        <div class="max-h-40 overflow-y-auto bg-white rounded-xl border border-slate-200 p-2 divide-y divide-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-1">
                            <template x-for="a in allAreas.filter(item => item.toLowerCase().includes(searchAreaNew.toLowerCase()))" :key="a">
                                <label class="py-1 px-2 flex items-center gap-2 hover:bg-slate-50 rounded-lg cursor-pointer">
                                    <input type="checkbox" name="allowed_areas[]" :value="a" x-model="selectedAreas" class="w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                                    <span class="text-xs text-slate-800 font-medium" x-text="a"></span>
                                </label>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- INITIAL PERMISSIONS -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                    <div class="font-bold text-slate-800 text-xs flex items-center gap-1.5">
                        <i class="fa-solid fa-shield-halved text-slate-600"></i>
                        <span>Inisialisasi Izin Akses Modul</span>
                    </div>
                    <p class="text-[11px] text-slate-400">Pilih modul yang diizinkan untuk role ini (dapat diubah kapan saja pada tab Matriks).</p>

                    <div class="max-h-48 overflow-y-auto bg-white rounded-xl border border-slate-200 p-3 space-y-3">
                        @foreach($permissionsByModule as $moduleName => $perms)
                            <div>
                                <div class="font-black text-[10px] text-slate-500 uppercase tracking-wider mb-1">{{ $moduleName }}</div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5">
                                    @foreach($perms as $p)
                                        <label class="flex items-center gap-2 p-1.5 hover:bg-slate-50 rounded-lg cursor-pointer">
                                            <input type="checkbox" name="permissions[]" value="{{ $p->id }}" class="w-4 h-4 text-primary rounded border-slate-300 focus:ring-primary">
                                            <span class="text-[11px] text-slate-800 font-medium leading-tight">{{ $p->display_name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" @click="newRoleModal = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-primary hover:bg-primary-700 text-white font-bold text-xs shadow-md shadow-primary/25 flex items-center gap-2">
                        <i class="fa-solid fa-check text-xs"></i>
                        <span>Simpan Role Baru</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: EDIT ROLE & SCOPE -->
    <div x-show="editRoleModal" x-cloak class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-7 shadow-2xl border border-slate-100 my-8 max-h-[90vh] flex flex-col">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-4 flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-indigo-50 text-indigo-700 flex items-center justify-center text-lg font-black">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-slate-900">Edit Data Role & Pengaturan Scope</h3>
                        <p class="text-xs text-slate-500" x-text="selectedRole.display_name"></p>
                    </div>
                </div>
                <button type="button" @click="editRoleModal = false" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>

            <form :action="'{{ url('setting/rbac/roles') }}/' + selectedRole.id" method="POST" class="overflow-y-auto pr-1 flex-1 space-y-4 text-xs">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Nama Tampilan Role <span class="text-rose-500">*</span></label>
                        <input type="text" name="display_name" x-model="selectedRole.display_name" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:outline-none focus:border-primary">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Kode Identitas (Slug)</label>
                        <input type="text" name="name" x-model="selectedRole.name" :disabled="selectedRole.is_system || selectedRole.name === 'admin'" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:outline-none focus:border-primary disabled:bg-slate-100 disabled:text-slate-400">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Deskripsi Peran</label>
                    <textarea name="description" x-model="selectedRole.description" rows="2" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:bg-white focus:outline-none focus:border-primary"></textarea>
                </div>

                <!-- SCOPE 1: PENGATURAN PRINSIPLE DIHANDLE -->
                <div class="p-4 rounded-2xl bg-indigo-50/40 border border-indigo-100 space-y-3" x-show="selectedRole.name !== 'admin'">
                    <div class="flex items-center justify-between">
                        <div class="font-bold text-slate-800 text-xs flex items-center gap-1.5">
                            <i class="fa-solid fa-building text-indigo-600"></i>
                            <span>Prinsiple yang Dihandle</span>
                        </div>
                        <span class="text-[10px] text-indigo-700 font-bold" x-text="selectedRole.scope_principle_type === 'all' ? 'Semua Prinsiple' : selectedRole.allowed_principles.length + ' Dipilih'"></span>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <label class="p-2.5 rounded-xl border flex items-center gap-2 cursor-pointer transition-all" :class="selectedRole.scope_principle_type === 'all' ? 'bg-indigo-600 text-white border-indigo-600 font-bold' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'">
                            <input type="radio" name="scope_principle_type" value="all" x-model="selectedRole.scope_principle_type" class="sr-only">
                            <i class="fa-solid fa-earth-americas text-xs"></i>
                            <span>Semua Prinsiple</span>
                        </label>
                        <label class="p-2.5 rounded-xl border flex items-center gap-2 cursor-pointer transition-all" :class="selectedRole.scope_principle_type === 'specific' ? 'bg-indigo-600 text-white border-indigo-600 font-bold' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'">
                            <input type="radio" name="scope_principle_type" value="specific" x-model="selectedRole.scope_principle_type" class="sr-only">
                            <i class="fa-solid fa-list-check text-xs"></i>
                            <span>Pilih Prinsiple Tertentu</span>
                        </label>
                    </div>

                    <!-- Specific Principle Checklist -->
                    <div x-show="selectedRole.scope_principle_type === 'specific'" x-cloak class="space-y-2 pt-2 border-t border-indigo-100">
                        <div class="flex items-center gap-2">
                            <input type="text" x-model="searchPrincipleEdit" placeholder="Cari nama prinsiple..." class="flex-1 px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-medium focus:outline-none focus:border-indigo-500">
                            <button type="button" @click="selectedRole.allowed_principles = selectedRole.allowed_principles.length === allPrinciples.length ? [] : [...allPrinciples]" class="px-2.5 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 font-bold text-[11px] whitespace-nowrap">
                                <span x-text="selectedRole.allowed_principles.length === allPrinciples.length ? 'Batal Semua' : 'Pilih Semua'"></span>
                            </button>
                        </div>

                        <div class="max-h-40 overflow-y-auto bg-white rounded-xl border border-slate-200 p-2 divide-y divide-slate-100">
                            <template x-for="p in allPrinciples.filter(item => item.toLowerCase().includes(searchPrincipleEdit.toLowerCase()))" :key="p">
                                <label class="py-1.5 px-2 flex items-center gap-2.5 hover:bg-slate-50 rounded-lg cursor-pointer">
                                    <input type="checkbox" name="allowed_principles[]" :value="p" x-model="selectedRole.allowed_principles" class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                                    <span class="text-xs text-slate-800 font-medium" x-text="p"></span>
                                </label>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- SCOPE 2: PENGATURAN AREA COVER -->
                <div class="p-4 rounded-2xl bg-blue-50/40 border border-blue-100 space-y-3" x-show="selectedRole.name !== 'admin'">
                    <div class="flex items-center justify-between">
                        <div class="font-bold text-slate-800 text-xs flex items-center gap-1.5">
                            <i class="fa-solid fa-location-dot text-blue-600"></i>
                            <span>Area Cover Penempatan</span>
                        </div>
                        <span class="text-[10px] text-blue-700 font-bold" x-text="selectedRole.scope_area_type === 'all' ? 'Semua Area' : selectedRole.allowed_areas.length + ' Dipilih'"></span>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <label class="p-2.5 rounded-xl border flex items-center gap-2 cursor-pointer transition-all" :class="selectedRole.scope_area_type === 'all' ? 'bg-blue-600 text-white border-blue-600 font-bold' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'">
                            <input type="radio" name="scope_area_type" value="all" x-model="selectedRole.scope_area_type" class="sr-only">
                            <i class="fa-solid fa-map text-xs"></i>
                            <span>Semua Area (Nasional)</span>
                        </label>
                        <label class="p-2.5 rounded-xl border flex items-center gap-2 cursor-pointer transition-all" :class="selectedRole.scope_area_type === 'specific' ? 'bg-blue-600 text-white border-blue-600 font-bold' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'">
                            <input type="radio" name="scope_area_type" value="specific" x-model="selectedRole.scope_area_type" class="sr-only">
                            <i class="fa-solid fa-location-crosshairs text-xs"></i>
                            <span>Pilih Area Tertentu</span>
                        </label>
                    </div>

                    <!-- Specific Area Checklist -->
                    <div x-show="selectedRole.scope_area_type === 'specific'" x-cloak class="space-y-2 pt-2 border-t border-blue-100">
                        <div class="flex items-center gap-2">
                            <input type="text" x-model="searchAreaEdit" placeholder="Cari nama kota/area..." class="flex-1 px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-medium focus:outline-none focus:border-blue-500">
                            <button type="button" @click="selectedRole.allowed_areas = selectedRole.allowed_areas.length === allAreas.length ? [] : [...allAreas]" class="px-2.5 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 font-bold text-[11px] whitespace-nowrap">
                                <span x-text="selectedRole.allowed_areas.length === allAreas.length ? 'Batal Semua' : 'Pilih Semua'"></span>
                            </button>
                        </div>

                        <div class="max-h-40 overflow-y-auto bg-white rounded-xl border border-slate-200 p-2 divide-y divide-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-1">
                            <template x-for="a in allAreas.filter(item => item.toLowerCase().includes(searchAreaEdit.toLowerCase()))" :key="a">
                                <label class="py-1 px-2 flex items-center gap-2 hover:bg-slate-50 rounded-lg cursor-pointer">
                                    <input type="checkbox" name="allowed_areas[]" :value="a" x-model="selectedRole.allowed_areas" class="w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                                    <span class="text-xs text-slate-800 font-medium" x-text="a"></span>
                                </label>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" @click="editRoleModal = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-primary hover:bg-primary-700 text-white font-bold text-xs shadow-md shadow-primary/25 flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk text-xs"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 3: EDIT USER ACCESS & CUSTOM SCOPE OVERRIDE -->
    <div x-show="editUserModal" x-cloak class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-3xl max-w-xl w-full p-6 sm:p-7 shadow-2xl border border-slate-100 my-8 max-h-[90vh] flex flex-col">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-4 flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-indigo-50 text-indigo-700 flex items-center justify-center text-lg font-black">
                        <i class="fa-solid fa-user-pen"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-slate-900">Atur Hak Akses & Scope Pengguna</h3>
                        <p class="text-xs text-slate-500" x-text="selectedUser.name"></p>
                    </div>
                </div>
                <button type="button" @click="editUserModal = false" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>

            <form :action="'{{ url('setting/rbac/user') }}/' + selectedUser.id" method="POST" class="overflow-y-auto pr-1 flex-1 space-y-4 text-xs">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Nama Pengguna</label>
                        <input type="text" :value="selectedUser.name" disabled class="w-full px-3.5 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-xs font-semibold text-slate-600">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Email Pengguna</label>
                        <input type="email" :value="selectedUser.email" disabled class="w-full px-3.5 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-xs font-semibold text-slate-600">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Pilih Role Akses <span class="text-rose-500">*</span></label>
                    <select name="role" x-model="selectedUser.role" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:outline-none focus:border-primary">
                        @foreach($roles as $r)
                            <option value="{{ $r->name }}">{{ $r->display_name }} ({{ $r->name }})</option>
                        @endforeach
                    </select>
                    <p class="text-[10px] text-slate-400 mt-1">Secara bawaan, pengguna akan mewarisi izin modul serta scope prinsiple & area dari role ini.</p>
                </div>

                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" :checked="selectedUser.is_active" class="w-4 h-4 text-primary rounded border-slate-300 focus:ring-primary">
                        <div>
                            <span class="font-bold text-slate-800 text-xs block">Izinkan Login ke Portal ASYSTEM</span>
                            <span class="text-[11px] text-slate-400">Jika dinonaktifkan, akun pengguna akan diblokir dari login ke aplikasi.</span>
                        </div>
                    </label>
                </div>

                <!-- PENGATURAN SCOPE OVERRIDE INDIVIDUAL -->
                <div class="p-4 rounded-2xl bg-amber-50/50 border border-amber-200/80 space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="scope_override" value="1" x-model="selectedUser.scope_override" class="w-4 h-4 text-amber-600 rounded border-slate-300 focus:ring-amber-500">
                            <span class="font-black text-slate-900 text-xs">Kustomisasi Scope Khusus Pengguna Ini (Override Role)</span>
                        </label>
                    </div>
                    <p class="text-[11px] text-slate-500">
                        Aktifkan jika karyawan ini menangani prinsiple atau meng-cover area yang berbeda dari pengaturan default rolenya.
                    </p>

                    <div x-show="selectedUser.scope_override" x-cloak class="space-y-3 pt-3 border-t border-amber-200/60">
                        <!-- User Prinsiple Scope -->
                        <div>
                            <div class="font-bold text-slate-800 text-[11px] mb-1.5 flex items-center justify-between">
                                <span>Prinsiple yang Dihandle:</span>
                                <span class="text-[10px] text-amber-700 font-bold" x-text="selectedUser.user_scope_principle_type === 'all' ? 'Semua Prinsiple' : selectedUser.user_allowed_principles.length + ' Dipilih'"></span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 mb-2">
                                <label class="p-2 rounded-xl border flex items-center gap-1.5 cursor-pointer text-[11px]" :class="selectedUser.user_scope_principle_type === 'all' ? 'bg-amber-600 text-white border-amber-600 font-bold' : 'bg-white text-slate-700 border-slate-200'">
                                    <input type="radio" name="user_scope_principle_type" value="all" x-model="selectedUser.user_scope_principle_type" class="sr-only">
                                    <span>Semua Prinsiple</span>
                                </label>
                                <label class="p-2 rounded-xl border flex items-center gap-1.5 cursor-pointer text-[11px]" :class="selectedUser.user_scope_principle_type === 'specific' ? 'bg-amber-600 text-white border-amber-600 font-bold' : 'bg-white text-slate-700 border-slate-200'">
                                    <input type="radio" name="user_scope_principle_type" value="specific" x-model="selectedUser.user_scope_principle_type" class="sr-only">
                                    <span>Pilih Prinsiple Tertentu</span>
                                </label>
                            </div>

                            <div x-show="selectedUser.user_scope_principle_type === 'specific'" class="space-y-1.5">
                                <input type="text" x-model="searchPrincipleUser" placeholder="Cari prinsiple..." class="w-full px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-[11px]">
                                <div class="max-h-32 overflow-y-auto bg-white rounded-xl border border-slate-200 p-2 divide-y divide-slate-100">
                                    <template x-for="p in allPrinciples.filter(item => item.toLowerCase().includes(searchPrincipleUser.toLowerCase()))" :key="p">
                                        <label class="py-1 px-1.5 flex items-center gap-2 hover:bg-slate-50 rounded cursor-pointer">
                                            <input type="checkbox" name="user_allowed_principles[]" :value="p" x-model="selectedUser.user_allowed_principles" class="w-3.5 h-3.5 text-amber-600 rounded">
                                            <span class="text-[11px] text-slate-800" x-text="p"></span>
                                        </label>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- User Area Scope -->
                        <div class="pt-2 border-t border-amber-200/60">
                            <div class="font-bold text-slate-800 text-[11px] mb-1.5 flex items-center justify-between">
                                <span>Area Penempatan Cover:</span>
                                <span class="text-[10px] text-amber-700 font-bold" x-text="selectedUser.user_scope_area_type === 'all' ? 'Semua Area' : selectedUser.user_allowed_areas.length + ' Dipilih'"></span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 mb-2">
                                <label class="p-2 rounded-xl border flex items-center gap-1.5 cursor-pointer text-[11px]" :class="selectedUser.user_scope_area_type === 'all' ? 'bg-amber-600 text-white border-amber-600 font-bold' : 'bg-white text-slate-700 border-slate-200'">
                                    <input type="radio" name="user_scope_area_type" value="all" x-model="selectedUser.user_scope_area_type" class="sr-only">
                                    <span>Semua Area</span>
                                </label>
                                <label class="p-2 rounded-xl border flex items-center gap-1.5 cursor-pointer text-[11px]" :class="selectedUser.user_scope_area_type === 'specific' ? 'bg-amber-600 text-white border-amber-600 font-bold' : 'bg-white text-slate-700 border-slate-200'">
                                    <input type="radio" name="user_scope_area_type" value="specific" x-model="selectedUser.user_scope_area_type" class="sr-only">
                                    <span>Pilih Area Tertentu</span>
                                </label>
                            </div>

                            <div x-show="selectedUser.user_scope_area_type === 'specific'" class="space-y-1.5">
                                <input type="text" x-model="searchAreaUser" placeholder="Cari area..." class="w-full px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-[11px]">
                                <div class="max-h-32 overflow-y-auto bg-white rounded-xl border border-slate-200 p-2 divide-y divide-slate-100 grid grid-cols-2 gap-1">
                                    <template x-for="a in allAreas.filter(item => item.toLowerCase().includes(searchAreaUser.toLowerCase()))" :key="a">
                                        <label class="py-1 px-1.5 flex items-center gap-2 hover:bg-slate-50 rounded cursor-pointer">
                                            <input type="checkbox" name="user_allowed_areas[]" :value="a" x-model="selectedUser.user_allowed_areas" class="w-3.5 h-3.5 text-amber-600 rounded">
                                            <span class="text-[11px] text-slate-800" x-text="a"></span>
                                        </label>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" @click="editUserModal = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-primary hover:bg-primary-700 text-white font-bold text-xs shadow-md shadow-primary/25 flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk text-xs"></i>
                        <span>Simpan Perubahan Akses</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 4: RESET PASSWORD -->
    <div x-show="resetPasswordModal" x-cloak class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl border border-slate-100 my-8">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg font-black">
                        <i class="fa-solid fa-key"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-900">Reset Password</h3>
                        <p class="text-[11px] text-slate-400" x-text="selectedUser.name"></p>
                    </div>
                </div>
                <button type="button" @click="resetPasswordModal = false" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>

            <form :action="'{{ url('setting/rbac/user') }}/' + selectedUser.id + '/reset-password'" method="POST">
                @csrf

                <div class="space-y-4 text-xs">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Password Baru <span class="text-rose-500">*</span></label>
                        <input type="password" name="password" required minlength="6" placeholder="Minimal 6 karakter" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:outline-none focus:border-primary">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Konfirmasi Password Baru <span class="text-rose-500">*</span></label>
                        <input type="password" name="password_confirmation" required minlength="6" placeholder="Ulangi password baru" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:outline-none focus:border-primary">
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" @click="resetPasswordModal = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-md shadow-amber-600/25 flex items-center gap-2">
                        <i class="fa-solid fa-lock text-xs"></i>
                        <span>Reset Password</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
