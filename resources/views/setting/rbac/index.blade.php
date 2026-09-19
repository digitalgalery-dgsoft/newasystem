@extends('layouts.app')

@section('title', 'Hak Akses Pengguna & RBAC - ASYSTEM Support System')

@section('content')
<div class="space-y-6" x-data="{ 
    activeTab: '{{ request('tab', 'matrix') }}',
    editUserModal: false,
    resetPasswordModal: false,
    selectedUser: {},
    openEditUser(user) {
        this.selectedUser = user;
        this.editUserModal = true;
    },
    openResetPassword(user) {
        this.selectedUser = user;
        this.resetPasswordModal = true;
    }
}">

    <!-- BREADCRUMB -->
    <div class="flex items-center gap-2 text-xs text-slate-500">
        <a href="{{ route('fitur.index') }}" class="hover:text-primary transition-colors flex items-center gap-1.5">
            <i class="fa-solid fa-house"></i>
            <span>Beranda</span>
        </a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
        <span>System Setting</span>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
        <span class="font-bold text-slate-800">Hak Akses (RBAC)</span>
    </div>

    <!-- FLASH MESSAGES -->
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
                <div class="flex items-center gap-2 mb-1">
                    <h1 class="text-xl font-black text-slate-900 tracking-tight">Hak Akses Pengguna (RBAC)</h1>
                    <span class="px-2.5 py-0.5 rounded-full bg-indigo-50 text-indigo-700 font-bold text-[10px] border border-indigo-200">Role-Based Access Control</span>
                </div>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Kelola matriks hak akses modul per role dan otorisasi akses individual untuk seluruh karyawan dan pengguna sistem ESA Groups.
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2 self-end md:self-auto">
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
    <div class="bg-white rounded-2xl p-1.5 border border-slate-200 shadow-sm flex items-center gap-1.5 overflow-x-auto">
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
            <span>Katalog Role & Tanggung Jawab</span>
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

                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-primary hover:bg-primary-700 text-white font-bold text-xs shadow-md shadow-primary/25 transition-all flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk text-xs"></i>
                        <span>Simpan Perubahan Matriks</span>
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/90 border-b border-slate-200 text-[11px] font-black text-slate-600 uppercase tracking-wider">
                                <th class="py-3.5 px-4 w-1/3 min-w-[280px]">Modul & Nama Perizinan</th>
                                @foreach($roles as $role)
                                    <th class="py-3.5 px-3 text-center min-w-[130px]">
                                        <div class="font-bold text-slate-800 text-xs">{{ $role->display_name }}</div>
                                        <span class="text-[10px] font-semibold text-slate-400 font-mono">({{ $role->name }})</span>
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
                                            <td class="py-3 px-3 text-center align-middle">
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
                    <p class="text-[11px] text-slate-500">Kelola role dan perizinan spesifik untuk setiap user yang terdaftar dalam sistem.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-[11px] font-black text-slate-500 uppercase tracking-wider">
                            <th class="py-3.5 px-4 w-12 text-center">ID</th>
                            <th class="py-3.5 px-4">Nama & Email Pengguna</th>
                            <th class="py-3.5 px-4">Jabatan & Area</th>
                            <th class="py-3.5 px-3 text-center">Role Akses</th>
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
                                        $badgeCls = $roleBadgeClasses[$u->role] ?? 'bg-slate-100 text-slate-700 border-slate-200';
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
                                                @click="openEditUser({{ json_encode($u->only(['id', 'name', 'email', 'role', 'is_active'])) }})"
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
                                <td colspan="6" class="py-12 text-center text-slate-400">
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

    <!-- TAB 3: KATALOG ROLE & TANGGUNG JAWAB -->
    <div x-show="activeTab === 'roles'" x-cloak class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($roles as $r)
                <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center text-base font-black flex-shrink-0">
                                    <i class="fa-solid fa-id-card-clip"></i>
                                </div>
                                <div>
                                    <h4 class="font-black text-slate-900 text-sm">{{ $r->display_name }}</h4>
                                    <code class="text-[10px] text-slate-400 font-mono">{{ $r->name }}</code>
                                </div>
                            </div>

                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                                {{ $r->users()->count() }} User
                            </span>
                        </div>

                        <p class="text-xs text-slate-500 leading-relaxed mb-4">
                            {{ $r->description ?: 'Tidak ada deskripsi khusus untuk role ini.' }}
                        </p>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] font-semibold text-slate-600">
                        <span>Total Izin Akses:</span>
                        <span class="font-black text-primary">{{ $r->permissions->count() }} Modul</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- MODAL EDIT USER ACCESS -->
    <div x-show="editUserModal" x-cloak class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 my-8">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center text-lg font-black">
                        <i class="fa-solid fa-user-pen"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-900">Atur Hak Akses Pengguna</h3>
                        <p class="text-[11px] text-slate-400" x-text="selectedUser.name"></p>
                    </div>
                </div>
                <button type="button" @click="editUserModal = false" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>

            <form :action="'{{ url('setting/rbac/user') }}/' + selectedUser.id" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-4 text-xs">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Nama Pengguna</label>
                        <input type="text" :value="selectedUser.name" disabled class="w-full px-3.5 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-xs font-semibold text-slate-600">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Email Pengguna</label>
                        <input type="email" :value="selectedUser.email" disabled class="w-full px-3.5 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-xs font-semibold text-slate-600">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Pilih Role Akses <span class="text-rose-500">*</span></label>
                        <select name="role" x-model="selectedUser.role" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:outline-none focus:border-primary">
                            @foreach($roles as $r)
                                <option value="{{ $r->name }}">{{ $r->display_name }} ({{ $r->name }})</option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-slate-400 mt-1">Hak akses modul akan otomatis menyesuaikan dengan matriks perizinan role terpilih.</p>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                        <label class="flex items-center gap-2.5 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" :checked="selectedUser.is_active" class="w-4 h-4 text-primary rounded border-slate-300 focus:ring-primary">
                            <div>
                                <span class="font-bold text-slate-800 text-xs block">Izinkan Login ke Portal ASYSTEM</span>
                                <span class="text-[11px] text-slate-400">Jika dinonaktifkan, pengguna tidak akan dapat login ke sistem.</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" @click="editUserModal = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-primary hover:bg-primary-700 text-white font-bold text-xs shadow-md shadow-primary/25 flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk text-xs"></i>
                        <span>Simpan Akses</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL RESET PASSWORD -->
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
