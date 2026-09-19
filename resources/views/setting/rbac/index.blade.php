@extends('layouts.app')

@section('title', 'Hak Akses Pengguna & Role Dinamis (RBAC) - ASystem')

@section('content')
<div class="space-y-6" x-data="{
    activeTab: '{{ $activeTab ?? 'matrix' }}',
    newRoleModal: false,
    editRoleModal: false,
    editUserModal: false,
    resetPasswordModal: false,
    createUserModal: false,
    newUserData: {
        name: '',
        email: '',
        password: '',
        role: 'role_akses_as',
        job_title: '',
        area: '',
        scope_principle_type: 'all',
        allowed_principles: [],
        scope_area_type: 'all',
        allowed_areas: []
    },
    empSearchQuery: '',
    empSearchResults: [],
    empIsSearching: false,
    searchPrincipleNewUser: '',
    searchAreaNewUser: '',
    selectedRole: {
        id: null,
        name: '',
        display_name: '',
        description: '',
        is_system: false,
        permissions: []
    },
    selectedUser: {
        id: null,
        name: '',
        email: '',
        role: '',
        is_active: true,
        scope_principle_type: 'all',
        allowed_principles: [],
        scope_area_type: 'all',
        allowed_areas: []
    },
    allPrinciples: {{ json_encode($allPrinciples) }},
    allAreas: {{ json_encode($allAreas) }},
    searchPrincipleUser: '',
    searchAreaUser: '',

    searchEmployeesFromMaster() {
        if (this.empSearchQuery.length < 2) {
            this.empSearchResults = [];
            return;
        }
        this.empIsSearching = true;
        fetch('{{ route('setting.rbac.search-employees') }}?q=' + encodeURIComponent(this.empSearchQuery))
            .then(res => res.json())
            .then(data => {
                this.empSearchResults = data;
                this.empIsSearching = false;
            })
            .catch(() => {
                this.empIsSearching = false;
            });
    },

    selectEmployeeForUser(emp) {
        this.newUserData.name = emp.nama_karyawan;
        this.newUserData.email = emp.email || (emp.nik ? emp.nik + '@asystem.co.id' : '');
        this.newUserData.job_title = emp.jabatan || 'Karyawan';
        this.newUserData.area = emp.area || 'Semua Area';
        if (emp.prinsiple && this.allPrinciples.includes(emp.prinsiple)) {
            this.newUserData.scope_principle_type = 'specific';
            this.newUserData.allowed_principles = [emp.prinsiple];
        }
        if (emp.area && this.allAreas.includes(emp.area)) {
            this.newUserData.scope_area_type = 'specific';
            this.newUserData.allowed_areas = [emp.area];
        }
        this.empSearchResults = [];
        this.empSearchQuery = emp.nama_karyawan + ' (' + emp.nik + ')';
    },

    toggleAllNewPrinciples() {
        if (this.newUserData.allowed_principles.length === this.allPrinciples.length) {
            this.newUserData.allowed_principles = [];
        } else {
            this.newUserData.allowed_principles = [...this.allPrinciples];
        }
    },

    toggleAllNewAreas() {
        if (this.newUserData.allowed_areas.length === this.allAreas.length) {
            this.newUserData.allowed_areas = [];
        } else {
            this.newUserData.allowed_areas = [...this.allAreas];
        }
    },

    openEditRole(role) {
        this.selectedRole = {
            id: role.id,
            name: role.name,
            display_name: role.display_name,
            description: role.description || '',
            is_system: !!role.is_system,
            permissions: role.permissions ? role.permissions.map(p => p.id) : []
        };
        this.editRoleModal = true;
    },

    openEditUser(user) {
        const handleAll = (user.handle_all_principles === false || user.handle_all_principles === 0) ? 'specific' : 'all';
        const coverAll = (user.cover_all_areas === false || user.cover_all_areas === 0) ? 'specific' : 'all';
        this.selectedUser = {
            id: user.id,
            name: user.name,
            email: user.email,
            role: user.role,
            is_active: user.is_active !== undefined ? !!user.is_active : true,
            scope_principle_type: handleAll,
            allowed_principles: Array.isArray(user.allowed_principles) ? [...user.allowed_principles] : [],
            scope_area_type: coverAll,
            allowed_areas: Array.isArray(user.allowed_areas) ? [...user.allowed_areas] : []
        };
        this.searchPrincipleUser = '';
        this.searchAreaUser = '';
        this.editUserModal = true;
    },

    toggleAllUserPrinciples() {
        if (this.selectedUser.allowed_principles.length === this.allPrinciples.length) {
            this.selectedUser.allowed_principles = [];
        } else {
            this.selectedUser.allowed_principles = [...this.allPrinciples];
        }
    },

    toggleAllUserAreas() {
        if (this.selectedUser.allowed_areas.length === this.allAreas.length) {
            this.selectedUser.allowed_areas = [];
        } else {
            this.selectedUser.allowed_areas = [...this.allAreas];
        }
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
                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-bold text-[10px] border border-emerald-200">Scope Prinsiple & Area per User</span>
                </div>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Kelola peran akses (seperti <strong>Role Akses AS</strong>), matriks izin modul, serta cakupan prinsiple & area kerja masing-masing karyawan agar data tampil sesuai dengan tugasnya.
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
                <i class="fa-solid fa-id-card-clip"></i>
            </div>
            <div>
                <div class="text-xl font-black text-slate-900">{{ $totalRoles }}</div>
                <div class="text-[11px] font-semibold text-slate-500">Total Role Dinamis</div>
            </div>
        </div>

        <!-- 3. Total Permissions -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-lg flex-shrink-0 border border-emerald-100">
                <i class="fa-solid fa-shield-check"></i>
            </div>
            <div>
                <div class="text-xl font-black text-slate-900">{{ $totalPermissions }}</div>
                <div class="text-[11px] font-semibold text-slate-500">Total Izin Modul</div>
            </div>
        </div>

        <!-- 4. Active Users -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center text-lg flex-shrink-0 border border-blue-100">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <div class="text-xl font-black text-slate-900">{{ $activeUsers }}</div>
                <div class="text-[11px] font-semibold text-slate-500">Pengguna Aktif Login</div>
            </div>
        </div>
    </div>

    <!-- TAB NAVIGATION -->
    <div class="bg-white rounded-2xl p-1.5 border border-slate-200 shadow-sm inline-flex flex-wrap gap-1">
        <button type="button" 
                @click="activeTab = 'matrix'" 
                :class="activeTab === 'matrix' ? 'bg-primary text-white shadow-sm font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-semibold'"
                class="px-5 py-2 rounded-xl text-xs transition-all flex items-center gap-2">
            <i class="fa-solid fa-table-cells text-xs"></i>
            <span>Matriks Hak Akses Modul</span>
        </button>

        <button type="button" 
                @click="activeTab = 'users'" 
                :class="activeTab === 'users' ? 'bg-primary text-white shadow-sm font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-semibold'"
                class="px-5 py-2 rounded-xl text-xs transition-all flex items-center gap-2">
            <i class="fa-solid fa-users-gear text-xs"></i>
            <span>Pengaturan Pengguna & Scope AS</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-black" :class="activeTab === 'users' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700'">
                {{ $totalUsers }}
            </span>
        </button>

        <button type="button" 
                @click="activeTab = 'roles'" 
                :class="activeTab === 'roles' ? 'bg-primary text-white shadow-sm font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-semibold'"
                class="px-5 py-2 rounded-xl text-xs transition-all flex items-center gap-2">
            <i class="fa-solid fa-id-badge text-xs"></i>
            <span>Daftar & Detail Role</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-black" :class="activeTab === 'roles' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700'">
                {{ $totalRoles }}
            </span>
        </button>
    </div>

    <!-- TAB 1: MATRIKS PERIZINAN PER ROLE -->
    <div x-show="activeTab === 'matrix'" x-cloak class="space-y-4">
        <form action="{{ route('setting.rbac.matrix.update') }}" method="POST">
            @csrf
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/50">
                    <div>
                        <h3 class="text-sm font-black text-slate-900">Matriks Hak Akses Modul per Role</h3>
                        <p class="text-[11px] text-slate-500">Centang kotak untuk memberikan izin modul kepada masing-masing peran. Matriks ini berlaku umum untuk setiap pengguna yang memiliki role tersebut.</p>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary hover:bg-primary-700 text-white font-bold text-xs shadow-md shadow-primary/25 transition-all flex items-center gap-2">
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
                                                    title="Edit Info Role"
                                                    class="text-slate-400 hover:text-primary p-0.5">
                                                <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                                            </button>
                                        </div>
                                        <div class="mt-1 flex items-center justify-center gap-1 flex-wrap">
                                            <code class="text-[9px] font-mono text-slate-500 bg-slate-100 px-1 py-0.2 rounded">{{ $role->name }}</code>
                                            <span class="text-[9px] font-semibold text-slate-400">({{ $role->users()->count() }} User)</span>
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
                                                    <!-- Super Admin selalu diizinkan -->
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
                <div class="w-full sm:w-56">
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
            <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h3 class="text-sm font-black text-slate-900">Daftar Akun Pengguna & Penugasan AS</h3>
                    <p class="text-[11px] text-slate-500">Satu role (misal: <strong>Role Akses AS</strong>) dapat digunakan oleh banyak user dengan prinsiple handle dan cover area yang berbeda-beda.</p>
                </div>
                <button type="button" @click="createUserModal = true" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-primary to-indigo-600 hover:from-primary-700 hover:to-indigo-700 text-white font-bold text-xs shadow-md shadow-primary/25 transition-all flex items-center gap-2 self-start sm:self-auto">
                    <i class="fa-solid fa-user-plus text-xs"></i>
                    <span>Daftarkan Karyawan / Tambah User Baru</span>
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-[11px] font-black text-slate-500 uppercase tracking-wider">
                            <th class="py-3.5 px-4 w-12 text-center">ID</th>
                            <th class="py-3.5 px-4">Nama & Email Pengguna</th>
                            <th class="py-3.5 px-4">Jabatan & Asal</th>
                            <th class="py-3.5 px-3 text-center">Role Akses</th>
                            <th class="py-3.5 px-4">Prinsiple Dihandle</th>
                            <th class="py-3.5 px-4">Area Cover</th>
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
                                            'role_akses_as' => 'bg-indigo-100 text-indigo-800 border-indigo-200 font-black',
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
                                </td>

                                <!-- Prinsiple Dihandle Column -->
                                <td class="py-3.5 px-4">
                                    @if($u->isAdmin())
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <i class="fa-solid fa-crown text-[9px] text-amber-500"></i>
                                            <span>Semua Prinsiple (Admin)</span>
                                        </span>
                                    @elseif($u->handlesAllPrinciples())
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <i class="fa-solid fa-circle-check text-[9px]"></i>
                                            <span>Semua Prinsiple</span>
                                        </span>
                                    @else
                                        @php
                                            $uPrins = $u->getEffectivePrinciples();
                                            $uPrinsCount = count($uPrins);
                                        @endphp
                                        @if($uPrinsCount === 0)
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-lg bg-rose-50 text-rose-700 border border-rose-200">
                                                <i class="fa-solid fa-ban text-[9px]"></i>
                                                <span>Belum Ditugaskan</span>
                                            </span>
                                        @else
                                            <div class="space-y-0.5">
                                                <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-200">
                                                    <i class="fa-solid fa-building text-[9px]"></i>
                                                    <span>{{ $uPrinsCount }} Prinsiple</span>
                                                </span>
                                                <div class="text-[10px] text-slate-500 font-medium truncate max-w-[170px]" title="{{ implode(', ', $uPrins) }}">
                                                    {{ implode(', ', array_slice($uPrins, 0, 2)) }}{{ $uPrinsCount > 2 ? ', +' . ($uPrinsCount - 2) . ' lainnya' : '' }}
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                </td>

                                <!-- Area Cover Column -->
                                <td class="py-3.5 px-4">
                                    @if($u->isAdmin())
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-lg bg-blue-50 text-blue-700 border border-blue-200">
                                            <i class="fa-solid fa-crown text-[9px] text-amber-500"></i>
                                            <span>Semua Area (Admin)</span>
                                        </span>
                                    @elseif($u->coversAllAreas())
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-lg bg-blue-50 text-blue-700 border border-blue-200">
                                            <i class="fa-solid fa-circle-check text-[9px]"></i>
                                            <span>Semua Area (Nasional)</span>
                                        </span>
                                    @else
                                        @php
                                            $uAreas = $u->getEffectiveAreas();
                                            $uAreasCount = count($uAreas);
                                        @endphp
                                        @if($uAreasCount === 0)
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-lg bg-rose-50 text-rose-700 border border-rose-200">
                                                <i class="fa-solid fa-ban text-[9px]"></i>
                                                <span>Belum Ada Area</span>
                                            </span>
                                        @else
                                            <div class="space-y-0.5">
                                                <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-lg bg-purple-50 text-purple-700 border border-purple-200">
                                                    <i class="fa-solid fa-location-dot text-[9px]"></i>
                                                    <span>{{ $uAreasCount }} Area</span>
                                                </span>
                                                <div class="text-[10px] text-slate-500 font-medium truncate max-w-[170px]" title="{{ implode(', ', $uAreas) }}">
                                                    {{ implode(', ', array_slice($uAreas, 0, 2)) }}{{ $uAreasCount > 2 ? ', +' . ($uAreasCount - 2) . ' lainnya' : '' }}
                                                </div>
                                            </div>
                                        @endif
                                    @endif
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
                                <td colspan="8" class="py-12 text-center text-slate-400">
                                    <i class="fa-solid fa-user-slash text-3xl mb-2 text-slate-300"></i>
                                    <p class="text-xs font-semibold">Tidak ada pengguna yang cocok dengan kriteria pencarian.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $users->links() }}
            </div>
        </div>
    </div>

    <!-- TAB 3: DAFTAR & DETAIL ROLE DINAMIS -->
    <div x-show="activeTab === 'roles'" x-cloak class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm font-black text-slate-900">Katalog Role & Tanggung Jawab</h3>
                <p class="text-[11px] text-slate-500">Daftar peran sistem yang tersedia. Setiap role dapat ditugaskan ke banyak pengguna dengan cakupan kerja spesifik.</p>
            </div>

            <button type="button" @click="newRoleModal = true" class="px-4 py-2 rounded-xl bg-primary hover:bg-primary-700 text-white font-bold text-xs shadow-md shadow-primary/25 transition-all flex items-center gap-1.5">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Tambah Role Baru</span>
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($roles as $r)
                <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow">
                    <div>
                        <div class="flex items-start justify-between gap-2 mb-3">
                            <div>
                                <h4 class="font-extrabold text-slate-900 text-sm flex items-center gap-1.5">
                                    <span>{{ $r->display_name }}</span>
                                    @if($r->is_system)
                                        <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 font-bold text-[9px]" title="Role Bawaan Sistem">SISTEM</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 font-bold text-[9px] border border-indigo-200" title="Role Kustom">KUSTOM</span>
                                    @endif
                                </h4>
                                <code class="text-[10px] text-slate-400 font-mono">{{ $r->name }}</code>
                            </div>

                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                                {{ $r->users()->count() }} User
                            </span>
                        </div>

                        <p class="text-xs text-slate-500 leading-relaxed mb-4">
                            {{ $r->description ?: 'Tidak ada deskripsi khusus untuk role ini.' }}
                        </p>

                        <!-- Informative Scope Note -->
                        <div class="bg-indigo-50/50 rounded-2xl p-3 border border-indigo-100/80 mb-4 text-[11px] text-slate-600">
                            <div class="font-bold text-indigo-900 flex items-center gap-1.5 mb-1">
                                <i class="fa-solid fa-users-gear text-indigo-600 text-xs"></i>
                                <span>Cakupan Prinsiple & Area Fleksibel</span>
                            </div>
                            <p class="text-[10px] text-slate-500 leading-normal">
                                Role ini dapat digunakan oleh banyak user dengan tugas prinsiple & wilayah berbeda yang diatur di Tab <strong>Manajemen Pengguna</strong>.
                            </p>
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
                                <span>Edit Role</span>
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
        <div class="bg-white rounded-3xl max-w-xl w-full p-6 sm:p-7 shadow-2xl border border-slate-100 my-8 max-h-[90vh] flex flex-col">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-4 flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-indigo-50 text-indigo-700 flex items-center justify-center text-lg font-black">
                        <i class="fa-solid fa-plus"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-slate-900">Tambah Role Akses Baru</h3>
                        <p class="text-xs text-slate-500">Definisikan peran baru (contoh: <strong>Role Akses AS</strong>) dan modul yang diizinkan.</p>
                    </div>
                </div>
                <button type="button" @click="newRoleModal = false" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>

            <form action="{{ route('setting.rbac.role.store') }}" method="POST" class="overflow-y-auto pr-1 flex-1 space-y-4 text-xs">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Nama Tampilan Role <span class="text-rose-500">*</span></label>
                        <input type="text" name="display_name" required placeholder="Contoh: Role Akses AS" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:outline-none focus:border-primary">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Kode Identitas (Slug) <span class="text-slate-400 font-normal text-[11px]">(Opsional)</span></label>
                        <input type="text" name="name" placeholder="contoh: role_akses_as" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:outline-none focus:border-primary">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Deskripsi Tanggung Jawab Role</label>
                    <textarea name="description" rows="2" placeholder="Jelaskan ruang lingkup atau tanggung jawab peran ini..." class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:bg-white focus:outline-none focus:border-primary"></textarea>
                </div>

                <!-- INFO BANNER: PRINSIPLE & AREA DIATUR PER USER -->
                <div class="p-3.5 rounded-2xl bg-indigo-50/70 border border-indigo-100 flex items-start gap-3">
                    <div class="w-7 h-7 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="fa-solid fa-lightbulb text-xs"></i>
                    </div>
                    <div class="text-[11px] text-slate-600 leading-relaxed">
                        <strong class="text-indigo-950 block font-bold mb-0.5">Pengaturan Scope Fleksibel per User / AS:</strong>
                        Role ini berfungsi sebagai template hak akses fitur. Anda dapat menyettingkan <strong>Prinsiple yang Dihandle</strong> dan <strong>Area Cover</strong> yang berbeda-beda untuk masing-masing AS di tab <strong>Pengaturan Pengguna & Scope AS</strong>.
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
                                            <span class="text-xs text-slate-700 font-medium">{{ $p->display_name }}</span>
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
                        <i class="fa-solid fa-plus text-xs"></i>
                        <span>Simpan Role Baru</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: EDIT ROLE -->
    <div x-show="editRoleModal" x-cloak class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-3xl max-w-xl w-full p-6 sm:p-7 shadow-2xl border border-slate-100 my-8 max-h-[90vh] flex flex-col">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-4 flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-indigo-50 text-indigo-700 flex items-center justify-center text-lg font-black">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-slate-900">Edit Data Role Akses</h3>
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

                <div class="p-3.5 rounded-2xl bg-indigo-50/70 border border-indigo-100 flex items-start gap-3">
                    <div class="w-7 h-7 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="fa-solid fa-lightbulb text-xs"></i>
                    </div>
                    <div class="text-[11px] text-slate-600 leading-relaxed">
                        <strong class="text-indigo-950 block font-bold mb-0.5">Cakupan Kerja Karyawan:</strong>
                        Prinsiple yang dihandle dan area cover dapat diatur spesifik untuk setiap pengguna di tab <strong>Pengaturan Pengguna & Scope AS</strong>.
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" @click="editRoleModal = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-primary hover:bg-primary-700 text-white font-bold text-xs shadow-md shadow-primary/25 flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk text-xs"></i>
                        <span>Simpan Perubahan Role</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL BARU: DAFTARKAN KARYAWAN / BUAT USER BARU -->
    <div x-show="createUserModal" x-cloak class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-7 shadow-2xl border border-slate-100 my-8 max-h-[92vh] flex flex-col">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-4 flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-indigo-50 text-indigo-700 flex items-center justify-center text-lg font-black">
                        <i class="fa-solid fa-user-plus"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-slate-900">Daftarkan Karyawan & Berikan Role Akses</h3>
                        <p class="text-xs text-slate-500">Pilih karyawan dari Master Karyawan atau input manual, lalu tentukan Role Akses dan Scope kerjanya.</p>
                    </div>
                </div>
                <button type="button" @click="createUserModal = false" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>

            <form action="{{ route('setting.rbac.user.store') }}" method="POST" class="overflow-y-auto pr-1 flex-1 space-y-4 text-xs">
                @csrf

                <!-- AUTOCOMPLETE SEARCH FROM MASTER KARYAWAN -->
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 relative">
                    <label class="block font-bold text-slate-800 text-xs mb-1.5 flex items-center justify-between">
                        <span class="flex items-center gap-1.5">
                            <i class="fa-solid fa-address-book text-primary text-xs"></i>
                            <span>Cari Karyawan dari Master Karyawan (Opsional Auto-Fill)</span>
                        </span>
                        <span class="text-[10px] text-slate-400">Ketik min. 2 huruf</span>
                    </label>
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" 
                               x-model="empSearchQuery" 
                               @input.debounce.300ms="searchEmployeesFromMaster()" 
                               placeholder="Ketik Nama Karyawan, NIK, atau Email untuk auto-fill..." 
                               class="w-full pl-8 pr-8 py-2 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-primary">
                        <span x-show="empIsSearching" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs">
                            <i class="fa-solid fa-spinner fa-spin"></i>
                        </span>
                    </div>

                    <!-- DROPDOWN HASIL CARI KARYAWAN -->
                    <div x-show="empSearchResults.length > 0" 
                         @click.outside="empSearchResults = []"
                         class="absolute left-0 right-0 top-full mt-1 z-50 bg-white rounded-2xl shadow-xl border border-slate-200 p-1.5 max-h-48 overflow-y-auto divide-y divide-slate-100">
                        <template x-for="emp in empSearchResults" :key="emp.id">
                            <div @click="selectEmployeeForUser(emp)" class="p-2.5 hover:bg-indigo-50/70 rounded-xl cursor-pointer transition-colors flex items-center justify-between">
                                <div>
                                    <div class="font-bold text-slate-900 text-xs" x-text="emp.nama_karyawan"></div>
                                    <div class="text-[10px] text-slate-500 mt-0.5 flex items-center gap-2">
                                        <span>NIK: <strong x-text="emp.nik"></strong></span>
                                        <span>•</span>
                                        <span x-text="emp.jabatan || 'Karyawan'"></span>
                                        <span>•</span>
                                        <span x-text="emp.area || 'Semua Area'"></span>
                                    </div>
                                </div>
                                <span class="px-2 py-1 rounded-lg bg-indigo-100 text-indigo-700 text-[10px] font-bold">Pilih Karyawan</span>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- USER IDENTITAS FORM -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Nama Lengkap Pengguna <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" x-model="newUserData.name" required placeholder="Nama Karyawan" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:outline-none focus:border-primary">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Email Pengguna (Untuk Login) <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" x-model="newUserData.email" required placeholder="email@asystem.co.id" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:outline-none focus:border-primary">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Jabatan / Posisi</label>
                        <input type="text" name="job_title" x-model="newUserData.job_title" placeholder="Contoh: Account Specialist" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:outline-none focus:border-primary">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Area Asal</label>
                        <input type="text" name="area" x-model="newUserData.area" placeholder="Contoh: Surabaya" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:outline-none focus:border-primary">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Pilih Role Akses <span class="text-rose-500">*</span></label>
                        <select name="role" x-model="newUserData.role" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:outline-none focus:border-primary">
                            @foreach($roles as $r)
                                <option value="{{ $r->name }}">{{ $r->display_name }} ({{ $r->name }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Password Login <span class="text-slate-400 font-normal text-[10px]">(Opsional, default: password123)</span></label>
                        <input type="password" name="password" x-model="newUserData.password" placeholder="Minimal 6 karakter..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:outline-none focus:border-primary">
                    </div>
                </div>

                <!-- SCOPE 1: PRINSIPLE DIHANDLE -->
                <div class="p-4 rounded-2xl bg-indigo-50/40 border border-indigo-100 space-y-3" x-show="newUserData.role !== 'admin'">
                    <div class="flex items-center justify-between">
                        <div class="font-bold text-slate-900 text-xs flex items-center gap-2">
                            <i class="fa-solid fa-building text-indigo-600 text-sm"></i>
                            <span>Prinsiple yang Dihandle</span>
                        </div>
                        <span class="text-[10px] text-indigo-700 font-bold bg-white px-2 py-0.5 rounded-full border border-indigo-200" 
                              x-text="newUserData.scope_principle_type === 'all' ? 'Semua Prinsiple' : newUserData.allowed_principles.length + ' Dipilih'">
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <label class="p-2.5 rounded-xl border flex items-center gap-2 cursor-pointer transition-all" 
                               :class="newUserData.scope_principle_type === 'all' ? 'bg-indigo-600 text-white border-indigo-600 font-bold shadow-sm' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'">
                            <input type="radio" name="scope_principle_type" value="all" x-model="newUserData.scope_principle_type" class="sr-only">
                            <i class="fa-solid fa-earth-americas text-xs"></i>
                            <span>Semua Prinsiple</span>
                        </label>
                        <label class="p-2.5 rounded-xl border flex items-center gap-2 cursor-pointer transition-all" 
                               :class="newUserData.scope_principle_type === 'specific' ? 'bg-indigo-600 text-white border-indigo-600 font-bold shadow-sm' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'">
                            <input type="radio" name="scope_principle_type" value="specific" x-model="newUserData.scope_principle_type" class="sr-only">
                            <i class="fa-solid fa-list-check text-xs"></i>
                            <span>Pilih Prinsiple Tertentu</span>
                        </label>
                    </div>

                    <div x-show="newUserData.scope_principle_type === 'specific'" x-cloak class="space-y-2 pt-2 border-t border-indigo-100">
                        <div class="flex items-center gap-2">
                            <div class="relative flex-1">
                                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                <input type="text" x-model="searchPrincipleNewUser" placeholder="Cari nama prinsiple (Wings, Kalbe, dll)..." class="w-full pl-8 pr-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-medium focus:outline-none focus:border-indigo-500">
                            </div>
                            <button type="button" @click="toggleAllNewPrinciples()" class="px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 font-bold text-[11px] whitespace-nowrap shadow-sm">
                                <span x-text="newUserData.allowed_principles.length === allPrinciples.length ? 'Batal Semua' : 'Pilih Semua'"></span>
                            </button>
                        </div>

                        <div class="max-h-40 overflow-y-auto bg-white rounded-2xl border border-slate-200 p-2 divide-y divide-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-0.5">
                            <template x-for="p in allPrinciples.filter(item => item.toLowerCase().includes(searchPrincipleNewUser.toLowerCase()))" :key="p">
                                <label class="py-1.5 px-2 flex items-center gap-2 hover:bg-indigo-50/50 rounded-lg cursor-pointer">
                                    <input type="checkbox" name="allowed_principles[]" :value="p" x-model="newUserData.allowed_principles" class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                                    <span class="text-xs text-slate-800 font-medium truncate" x-text="p" :title="p"></span>
                                </label>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- SCOPE 2: AREA COVER -->
                <div class="p-4 rounded-2xl bg-blue-50/40 border border-blue-100 space-y-3" x-show="newUserData.role !== 'admin'">
                    <div class="flex items-center justify-between">
                        <div class="font-bold text-slate-900 text-xs flex items-center gap-2">
                            <i class="fa-solid fa-location-dot text-blue-600 text-sm"></i>
                            <span>Area Cover Penempatan</span>
                        </div>
                        <span class="text-[10px] text-blue-700 font-bold bg-white px-2 py-0.5 rounded-full border border-blue-200" 
                              x-text="newUserData.scope_area_type === 'all' ? 'Semua Area' : newUserData.allowed_areas.length + ' Dipilih'">
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <label class="p-2.5 rounded-xl border flex items-center gap-2 cursor-pointer transition-all" 
                               :class="newUserData.scope_area_type === 'all' ? 'bg-blue-600 text-white border-blue-600 font-bold shadow-sm' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'">
                            <input type="radio" name="scope_area_type" value="all" x-model="newUserData.scope_area_type" class="sr-only">
                            <i class="fa-solid fa-map text-xs"></i>
                            <span>Semua Area (Nasional)</span>
                        </label>
                        <label class="p-2.5 rounded-xl border flex items-center gap-2 cursor-pointer transition-all" 
                               :class="newUserData.scope_area_type === 'specific' ? 'bg-blue-600 text-white border-blue-600 font-bold shadow-sm' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'">
                            <input type="radio" name="scope_area_type" value="specific" x-model="newUserData.scope_area_type" class="sr-only">
                            <i class="fa-solid fa-location-crosshairs text-xs"></i>
                            <span>Pilih Area Tertentu</span>
                        </label>
                    </div>

                    <div x-show="newUserData.scope_area_type === 'specific'" x-cloak class="space-y-2 pt-2 border-t border-blue-100">
                        <div class="flex items-center gap-2">
                            <div class="relative flex-1">
                                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                <input type="text" x-model="searchAreaNewUser" placeholder="Cari nama kota/area..." class="w-full pl-8 pr-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-medium focus:outline-none focus:border-blue-500">
                            </div>
                            <button type="button" @click="toggleAllNewAreas()" class="px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 font-bold text-[11px] whitespace-nowrap shadow-sm">
                                <span x-text="newUserData.allowed_areas.length === allAreas.length ? 'Batal Semua' : 'Pilih Semua'"></span>
                            </button>
                        </div>

                        <div class="max-h-40 overflow-y-auto bg-white rounded-2xl border border-slate-200 p-2 divide-y divide-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-0.5">
                            <template x-for="a in allAreas.filter(item => item.toLowerCase().includes(searchAreaNewUser.toLowerCase()))" :key="a">
                                <label class="py-1 px-2 flex items-center gap-2 hover:bg-blue-50/50 rounded-lg cursor-pointer">
                                    <input type="checkbox" name="allowed_areas[]" :value="a" x-model="newUserData.allowed_areas" class="w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                                    <span class="text-xs text-slate-800 font-medium truncate" x-text="a" :title="a"></span>
                                </label>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" @click="createUserModal = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-primary hover:bg-primary-700 text-white font-bold text-xs shadow-md shadow-primary/25 flex items-center gap-2">
                        <i class="fa-solid fa-user-plus text-xs"></i>
                        <span>Daftarkan & Berikan Role Akses</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 3: ATUR HAK AKSES & SCOPE KERJA PENGGUNA -->
    <div x-show="editUserModal" x-cloak class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-7 shadow-2xl border border-slate-100 my-8 max-h-[92vh] flex flex-col">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-4 flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-indigo-50 text-indigo-700 flex items-center justify-center text-lg font-black">
                        <i class="fa-solid fa-user-pen"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-slate-900">Atur Hak Akses & Penugasan Scope Karyawan</h3>
                        <p class="text-xs text-slate-500" x-text="selectedUser.name + ' (' + selectedUser.email + ')'"></p>
                    </div>
                </div>
                <button type="button" @click="editUserModal = false" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>

            <form :action="'{{ url('setting/rbac/user') }}/' + selectedUser.id" method="POST" class="overflow-y-auto pr-1 flex-1 space-y-4 text-xs">
                @csrf
                @method('PUT')

                <!-- SECTION 1: ROLE SELECTION & LOGIN STATUS -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Pilih Role Akses <span class="text-rose-500">*</span></label>
                        <select name="role" x-model="selectedUser.role" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:outline-none focus:border-primary">
                            @foreach($roles as $r)
                                <option value="{{ $r->name }}">{{ $r->display_name }} ({{ $r->name }})</option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-slate-400 mt-1">Menentukan menu & modul sistem yang dapat dibuka karyawan.</p>
                    </div>

                    <div class="flex items-center">
                        <label class="p-3 rounded-2xl bg-slate-50 border border-slate-200 flex items-center gap-2.5 cursor-pointer w-full hover:bg-slate-100 transition-colors">
                            <input type="checkbox" name="is_active" value="1" :checked="selectedUser.is_active" class="w-4 h-4 text-primary rounded border-slate-300 focus:ring-primary">
                            <div>
                                <span class="font-bold text-slate-800 text-xs block">Izinkan Login ke Portal</span>
                                <span class="text-[10px] text-slate-400">Buka akses login untuk akun ini</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- SECTION 2: PRINSIPLE YANG DIHANDLE -->
                <div class="p-4 rounded-2xl bg-indigo-50/40 border border-indigo-100 space-y-3" x-show="selectedUser.role !== 'admin'">
                    <div class="flex items-center justify-between">
                        <div class="font-bold text-slate-900 text-xs flex items-center gap-2">
                            <i class="fa-solid fa-building text-indigo-600 text-sm"></i>
                            <span>Prinsiple yang Dihandle Pengguna Ini</span>
                        </div>
                        <span class="text-[10px] text-indigo-700 font-bold bg-white px-2 py-0.5 rounded-full border border-indigo-200" 
                              x-text="selectedUser.scope_principle_type === 'all' ? 'Semua Prinsiple' : selectedUser.allowed_principles.length + ' Dipilih'">
                        </span>
                    </div>

                    <!-- Type Radio Buttons -->
                    <div class="grid grid-cols-2 gap-2">
                        <label class="p-2.5 rounded-xl border flex items-center gap-2 cursor-pointer transition-all" 
                               :class="selectedUser.scope_principle_type === 'all' ? 'bg-indigo-600 text-white border-indigo-600 font-bold shadow-sm' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'">
                            <input type="radio" name="scope_principle_type" value="all" x-model="selectedUser.scope_principle_type" class="sr-only">
                            <i class="fa-solid fa-earth-americas text-xs"></i>
                            <span>Semua Prinsiple (Nasional)</span>
                        </label>
                        <label class="p-2.5 rounded-xl border flex items-center gap-2 cursor-pointer transition-all" 
                               :class="selectedUser.scope_principle_type === 'specific' ? 'bg-indigo-600 text-white border-indigo-600 font-bold shadow-sm' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'">
                            <input type="radio" name="scope_principle_type" value="specific" x-model="selectedUser.scope_principle_type" class="sr-only">
                            <i class="fa-solid fa-list-check text-xs"></i>
                            <span>Pilih Prinsiple Tertentu</span>
                        </label>
                    </div>

                    <!-- Specific Principle Checklist -->
                    <div x-show="selectedUser.scope_principle_type === 'specific'" x-cloak class="space-y-2 pt-2 border-t border-indigo-100">
                        <div class="flex items-center gap-2">
                            <div class="relative flex-1">
                                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                <input type="text" x-model="searchPrincipleUser" placeholder="Cari nama prinsiple (misal: Wings, Kalbe, Unilever)..." class="w-full pl-8 pr-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-medium focus:outline-none focus:border-indigo-500">
                            </div>
                            <button type="button" @click="toggleAllUserPrinciples()" class="px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 font-bold text-[11px] whitespace-nowrap shadow-sm">
                                <span x-text="selectedUser.allowed_principles.length === allPrinciples.length ? 'Batal Semua' : 'Pilih Semua'"></span>
                            </button>
                        </div>

                        <div class="max-h-44 overflow-y-auto bg-white rounded-2xl border border-slate-200 p-2 divide-y divide-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-0.5">
                            <template x-for="p in allPrinciples.filter(item => item.toLowerCase().includes(searchPrincipleUser.toLowerCase()))" :key="p">
                                <label class="py-1.5 px-2 flex items-center gap-2 hover:bg-indigo-50/50 rounded-lg cursor-pointer">
                                    <input type="checkbox" name="allowed_principles[]" :value="p" x-model="selectedUser.allowed_principles" class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                                    <span class="text-xs text-slate-800 font-medium truncate" x-text="p" :title="p"></span>
                                </label>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- SECTION 3: AREA COVER PENEMPATAN -->
                <div class="p-4 rounded-2xl bg-blue-50/40 border border-blue-100 space-y-3" x-show="selectedUser.role !== 'admin'">
                    <div class="flex items-center justify-between">
                        <div class="font-bold text-slate-900 text-xs flex items-center gap-2">
                            <i class="fa-solid fa-location-dot text-blue-600 text-sm"></i>
                            <span>Area Cover Penempatan Pengguna Ini</span>
                        </div>
                        <span class="text-[10px] text-blue-700 font-bold bg-white px-2 py-0.5 rounded-full border border-blue-200" 
                              x-text="selectedUser.scope_area_type === 'all' ? 'Semua Area' : selectedUser.allowed_areas.length + ' Dipilih'">
                        </span>
                    </div>

                    <!-- Type Radio Buttons -->
                    <div class="grid grid-cols-2 gap-2">
                        <label class="p-2.5 rounded-xl border flex items-center gap-2 cursor-pointer transition-all" 
                               :class="selectedUser.scope_area_type === 'all' ? 'bg-blue-600 text-white border-blue-600 font-bold shadow-sm' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'">
                            <input type="radio" name="scope_area_type" value="all" x-model="selectedUser.scope_area_type" class="sr-only">
                            <i class="fa-solid fa-map text-xs"></i>
                            <span>Semua Area (Nasional)</span>
                        </label>
                        <label class="p-2.5 rounded-xl border flex items-center gap-2 cursor-pointer transition-all" 
                               :class="selectedUser.scope_area_type === 'specific' ? 'bg-blue-600 text-white border-blue-600 font-bold shadow-sm' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'">
                            <input type="radio" name="scope_area_type" value="specific" x-model="selectedUser.scope_area_type" class="sr-only">
                            <i class="fa-solid fa-location-crosshairs text-xs"></i>
                            <span>Pilih Area Tertentu</span>
                        </label>
                    </div>

                    <!-- Specific Area Checklist -->
                    <div x-show="selectedUser.scope_area_type === 'specific'" x-cloak class="space-y-2 pt-2 border-t border-blue-100">
                        <div class="flex items-center gap-2">
                            <div class="relative flex-1">
                                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                <input type="text" x-model="searchAreaUser" placeholder="Cari nama kota/area (misal: Surabaya, Jakarta, Bandung)..." class="w-full pl-8 pr-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-medium focus:outline-none focus:border-blue-500">
                            </div>
                            <button type="button" @click="toggleAllUserAreas()" class="px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 font-bold text-[11px] whitespace-nowrap shadow-sm">
                                <span x-text="selectedUser.allowed_areas.length === allAreas.length ? 'Batal Semua' : 'Pilih Semua'"></span>
                            </button>
                        </div>

                        <div class="max-h-44 overflow-y-auto bg-white rounded-2xl border border-slate-200 p-2 divide-y divide-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-0.5">
                            <template x-for="a in allAreas.filter(item => item.toLowerCase().includes(searchAreaUser.toLowerCase()))" :key="a">
                                <label class="py-1 px-2 flex items-center gap-2 hover:bg-blue-50/50 rounded-lg cursor-pointer">
                                    <input type="checkbox" name="allowed_areas[]" :value="a" x-model="selectedUser.allowed_areas" class="w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                                    <span class="text-xs text-slate-800 font-medium truncate" x-text="a" :title="a"></span>
                                </label>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Admin Notice if role is admin -->
                <div x-show="selectedUser.role === 'admin'" class="p-4 rounded-2xl bg-purple-50 border border-purple-200 text-purple-800 text-xs flex items-center gap-3">
                    <i class="fa-solid fa-crown text-amber-500 text-lg flex-shrink-0"></i>
                    <div>
                        <strong class="block font-bold">Role Administrator HR Terpilih</strong>
                        <span>Akun Administrator otomatis memiliki akses tak terbatas ke semua modul, semua prinsiple, dan seluruh area penempatan.</span>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" @click="editUserModal = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-primary hover:bg-primary-700 text-white font-bold text-xs shadow-md shadow-primary/25 flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk text-xs"></i>
                        <span>Simpan Pengaturan Akses & Scope</span>
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
