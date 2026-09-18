@extends('layouts.app')

@section('title', 'Master Data Karyawan - Attendance Portal')

@section('content')
<div class="space-y-6">

    <!-- Page Header Card -->
    <div class="page-header-card flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-13 h-13 rounded-2xl bg-gradient-to-br from-emerald-600 to-teal-700 text-white flex items-center justify-center text-2xl shadow-lg shadow-emerald-600/25 flex-shrink-0">
                <i class="fa-solid fa-users-gear"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">Data Master Karyawan</h1>
                    <span class="badge-pill bg-emerald-50 text-emerald-700 border-emerald-200">
                        <i class="fa-solid fa-database text-[10px]"></i> MASTER DATA
                    </span>
                    <span class="badge-pill bg-blue-50 text-primary border-blue-200 font-bold">
                        Inhouse & RateCard
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    Database induk informasi seluruh pegawai aktif, verifikasi komponen gaji, hak akses login, dan status evaluasi kerja.
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <a href="{{ route('odoo.setting.index') }}" class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 text-xs font-bold hover:bg-blue-100 transition-all shadow-sm">
                <i class="fa-solid fa-arrows-rotate text-blue-600"></i>
                <span>Sync Odoo (5 Entitas)</span>
            </a>
            <button onclick="openModal('addEmployeeModal')" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary hover:bg-primary-700 text-white text-xs font-bold transition-all shadow-md shadow-primary-600/20">
                <i class="fa-solid fa-user-plus"></i>
                <span>Add Karyawan</span>
            </button>
            <a href="{{ route('master.karyawan.index', ['tipe' => 'RateCard', 'status' => $status]) }}" class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl bg-slate-100 border border-slate-200 text-slate-700 text-xs font-semibold hover:bg-slate-200 transition-all">
                <i class="fa-solid fa-briefcase"></i>
                <span>Distributor / RateCard</span>
            </a>
        </div>
    </div>

    <!-- Stats Metric Row (Clickable Filters) -->
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total -->
        <a href="{{ route('master.karyawan.index', ['status' => 'all']) }}" class="stat-box transition-all hover:scale-[1.02] {{ $status === 'all' ? 'ring-2 ring-slate-900 shadow-md' : '' }}" title="Klik untuk lihat Semua Karyawan">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Total Karyawan</div>
                    <div class="text-2xl font-black text-slate-900 mt-1">{{ $stats['total'] }}</div>
                    <div class="text-[10px] text-slate-500 font-medium mt-0.5">Seluruh Database</div>
                </div>
                <div class="stat-box-icon bg-slate-100 text-slate-700">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
        </a>

        <!-- Aktif -->
        <a href="{{ route('master.karyawan.index', ['status' => 'Aktiv']) }}" class="stat-box transition-all hover:scale-[1.02] {{ $status === 'Aktiv' ? 'ring-2 ring-emerald-500 shadow-md' : '' }}" title="Klik untuk filter Karyawan Aktif">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Karyawan Aktif</div>
                    <div class="text-2xl font-black text-emerald-600 mt-1">{{ $stats['aktif'] }}</div>
                    <div class="text-[10px] text-emerald-600 font-medium mt-0.5">Default Aktif Lapangan</div>
                </div>
                <div class="stat-box-icon bg-emerald-50 text-emerald-600">
                    <i class="fa-solid fa-user-check"></i>
                </div>
            </div>
        </a>

        <!-- Resign -->
        <a href="{{ route('master.karyawan.index', ['status' => 'Resign']) }}" class="stat-box transition-all hover:scale-[1.02] {{ $status === 'Resign' ? 'ring-2 ring-rose-500 shadow-md' : '' }}" title="Klik untuk filter Karyawan Resign">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Resign / Nonaktif</div>
                    <div class="text-2xl font-black text-rose-600 mt-1">{{ $stats['resign'] }}</div>
                    <div class="text-[10px] text-rose-500 font-medium mt-0.5">Status Pengunduran</div>
                </div>
                <div class="stat-box-icon bg-rose-50 text-rose-600">
                    <i class="fa-solid fa-user-xmark"></i>
                </div>
            </div>
        </a>

        <!-- Inhouse & RateCard -->
        <a href="{{ route('master.karyawan.index', ['tipe' => 'Inhouse', 'status' => $status]) }}" class="stat-box transition-all hover:scale-[1.02] {{ request('tipe') === 'Inhouse' ? 'ring-2 ring-primary shadow-md' : '' }}" title="Klik untuk filter Karyawan Inhouse">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Tipe Inhouse</div>
                    <div class="text-2xl font-black text-primary mt-1">{{ $stats['inhouse'] }}</div>
                    <div class="text-[10px] text-slate-500 font-medium mt-0.5">{{ $stats['ratecard'] }} RateCard</div>
                </div>
                <div class="stat-box-icon bg-blue-50 text-primary">
                    <i class="fa-solid fa-house-chimney"></i>
                </div>
            </div>
        </a>
    </div>

    <!-- Filter & Advance Search Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
        <form method="GET" action="{{ route('master.karyawan.index') }}" class="space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                <!-- Search Input -->
                <div class="lg:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Cari Karyawan</label>
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Nama, NIK, NIP, Pimpinan..." 
                               class="w-full pl-8 pr-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary bg-slate-50/50">
                    </div>
                </div>

                <!-- Prinsiple Filter -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Prinsiple</label>
                    <select name="prinsiple" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary bg-slate-50/50">
                        <option value="">Semua Prinsiple</option>
                        @foreach($distinctPrinciples as $prin)
                            <option value="{{ $prin->name }}" {{ request('prinsiple') == $prin->name ? 'selected' : '' }}>
                                {{ $prin->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Jabatan Filter -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Jabatan</label>
                    <select name="jabatan" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary bg-slate-50/50">
                        <option value="">Semua Jabatan</option>
                        @foreach($distinctJabatan as $jab)
                            <option value="{{ $jab }}" {{ request('jabatan') == $jab ? 'selected' : '' }}>
                                {{ strtoupper($jab) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Area Filter -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Area</label>
                    <select name="area" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary bg-slate-50/50">
                        <option value="">Semua Area</option>
                        @foreach($distinctArea as $ar)
                            <option value="{{ $ar }}" {{ request('area') == $ar ? 'selected' : '' }}>
                                {{ $ar }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Entitas Odoo Filter (5 Entitas) -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Entitas Odoo</label>
                    <select name="entity" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary bg-slate-50/50">
                        <option value="">Semua Entitas</option>
                        @foreach(['AMK', 'AKP', 'ATK', 'ABO', 'ATB'] as $eCode)
                            <option value="{{ $eCode }}" {{ request('entity') == $eCode ? 'selected' : '' }}>
                                {{ $eCode }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 pt-2 border-t border-slate-100">
                <!-- Status Filter (Active, Resign, All) -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Status Karyawan</label>
                    <select name="status" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary bg-slate-50/50 font-semibold">
                        <option value="Aktiv" {{ ($status === 'Aktiv') ? 'selected' : '' }}>Karyawan Aktif (Default)</option>
                        <option value="Resign" {{ ($status === 'Resign') ? 'selected' : '' }}>Karyawan Resign</option>
                        <option value="all" {{ ($status === 'all') ? 'selected' : '' }}>Semua Status</option>
                    </select>
                </div>

                <!-- Tipe Karyawan (Inhouse / RateCard) -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Tipe (Inhouse / RateCard)</label>
                    <select name="tipe" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary bg-slate-50/50 font-semibold">
                        <option value="">Semua Tipe</option>
                        <option value="Inhouse" {{ request('tipe') === 'Inhouse' ? 'selected' : '' }}>Inhouse (5 Entitas)</option>
                        <option value="RateCard" {{ request('tipe') === 'RateCard' ? 'selected' : '' }}>RateCard (Client Luar)</option>
                    </select>
                </div>

                <!-- Information Note -->
                <div class="sm:col-span-2 flex items-center justify-between pt-4">
                    <div class="flex items-center gap-2 text-xs text-slate-500">
                        <span class="inline-flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                            <span class="text-rose-600 font-semibold">Teks Merah</span>: Belum ada komponen gaji
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('master.karyawan.index') }}" class="px-3 py-1.5 rounded-xl border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-100 transition-all">
                            <i class="fa-solid fa-rotate-left mr-1"></i> Reset
                        </a>
                        <button type="submit" class="px-4 py-1.5 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition-all shadow-sm">
                            <i class="fa-solid fa-filter mr-1"></i> Terapkan Filter
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Employee Custom Table Card -->
    <div class="table-card">
        <div class="px-6 py-4 border-b border-slate-200 flex flex-col lg:flex-row lg:items-center justify-between gap-3">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-sm font-bold text-slate-900">Daftar Karyawan Inhouse & RateCard</h2>
                    @if($status === 'Aktiv')
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                            <i class="fa-solid fa-circle-check text-[9px] mr-1"></i>Aktif Saja
                        </span>
                    @elseif($status === 'Resign')
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-300">
                            <i class="fa-solid fa-user-xmark text-[9px] mr-1"></i>Resign Saja
                        </span>
                    @else
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-800 border border-slate-300">
                            Semua Status
                        </span>
                    @endif
                </div>
                <p class="text-[11px] text-slate-500 mt-0.5">Menampilkan {{ $employees->firstItem() ?? 0 }} - {{ $employees->lastItem() ?? 0 }} dari {{ $employees->total() }} data karyawan</p>
            </div>

            <!-- Interactive Quick Filter Tabs (Status & Tipe) -->
            <div class="flex items-center gap-3 flex-wrap">
                <!-- Status Filter Pills -->
                <div class="flex items-center gap-1.5 text-xs font-semibold">
                    <span class="text-slate-400">Status:</span>
                    <a href="{{ route('master.karyawan.index', array_merge(request()->except(['status', 'page']), ['status' => 'Aktiv'])) }}" 
                       class="px-2.5 py-1 rounded-lg transition-all {{ ($status === 'Aktiv') ? 'bg-emerald-600 text-white shadow-sm font-bold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                       <i class="fa-solid fa-user-check text-[10px] mr-1"></i>Aktif
                    </a>
                    <a href="{{ route('master.karyawan.index', array_merge(request()->except(['status', 'page']), ['status' => 'Resign'])) }}" 
                       class="px-2.5 py-1 rounded-lg transition-all {{ ($status === 'Resign') ? 'bg-rose-600 text-white shadow-sm font-bold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                       <i class="fa-solid fa-user-xmark text-[10px] mr-1"></i>Resign
                    </a>
                    <a href="{{ route('master.karyawan.index', array_merge(request()->except(['status', 'page']), ['status' => 'all'])) }}" 
                       class="px-2.5 py-1 rounded-lg transition-all {{ ($status === 'all') ? 'bg-slate-900 text-white shadow-sm font-bold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                       Semua
                    </a>
                </div>

                <!-- Tipe Filter Pills -->
                <div class="flex items-center gap-1.5 text-xs font-semibold border-l border-slate-200 pl-3">
                    <span class="text-slate-400">Tipe:</span>
                    <a href="{{ route('master.karyawan.index', array_merge(request()->except(['tipe', 'page']), ['status' => $status])) }}" 
                       class="px-2.5 py-1 rounded-lg transition-all {{ !request('tipe') ? 'bg-primary text-white shadow-sm font-bold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                       Semua
                    </a>
                    <a href="{{ route('master.karyawan.index', array_merge(request()->except(['tipe', 'page']), ['tipe' => 'Inhouse', 'status' => $status])) }}" 
                       class="px-2.5 py-1 rounded-lg transition-all {{ request('tipe') === 'Inhouse' ? 'bg-primary text-white shadow-sm font-bold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                       Inhouse
                    </a>
                    <a href="{{ route('master.karyawan.index', array_merge(request()->except(['tipe', 'page']), ['tipe' => 'RateCard', 'status' => $status])) }}" 
                       class="px-2.5 py-1 rounded-lg transition-all {{ request('tipe') === 'RateCard' ? 'bg-primary text-white shadow-sm font-bold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                       RateCard
                    </a>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left custom-table">
                <thead>
                    <tr>
                        <th class="w-12 text-center">NO</th>
                        <th>NIK (KTP)</th>
                        <th>NAMA KARYAWAN</th>
                        <th class="text-center">ENTITAS</th>
                        <th>JABATAN & AREA</th>
                        <th>PRINSIPLE & LOGIN</th>
                        <th>PIMPINAN</th>
                        <th>TGL. JOIN</th>
                        <th>5 TAHUN</th>
                        <th class="text-center">STATUS</th>
                        <th class="text-center min-w-[170px]">TOOLS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($employees as $index => $emp)
                        @php
                            $badge = $emp->status_badge;
                            $eBadge = $emp->entity_badge;
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <!-- Col 1: NO -->
                            <td class="text-center font-bold text-slate-400 text-xs">
                                {{ $employees->firstItem() + $index }}
                            </td>

                            <!-- Col 2: NIK (KTP) -->
                            <td>
                                <div class="font-mono text-xs font-semibold text-slate-700">{{ $emp->nik }}</div>
                                @if($emp->nip)
                                    <div class="text-[10px] text-slate-400 font-mono">{{ $emp->nip }}</div>
                                @endif
                            </td>

                            <!-- Col 3: NAMA KARYAWAN -->
                            <td>
                                <div class="font-bold text-sm {{ $emp->has_komponen ? 'text-slate-900' : 'text-rose-600 font-extrabold' }}">
                                    {{ $emp->nama_karyawan }}
                                </div>
                                <div class="text-[11px] text-slate-500 flex items-center gap-2 mt-0.5">
                                    <span><i class="fa-regular fa-envelope text-[10px]"></i> {{ $emp->email ?? '-' }}</span>
                                    <span>•</span>
                                    <span><i class="fa-brands fa-whatsapp text-[10px] text-emerald-600"></i> {{ $emp->telepon ?? '-' }}</span>
                                </div>
                            </td>

                            <!-- Col 4: ENTITAS -->
                            <td class="text-center">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold border {{ $eBadge['bg'] }}" title="Entitas Odoo: {{ $eBadge['label'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $eBadge['dot'] }}"></span>
                                    <span>{{ $eBadge['label'] }}</span>
                                </span>
                            </td>

                            <!-- Col 5: JABATAN & AREA -->
                            <td>
                                <div class="font-bold text-xs text-slate-800">{{ strtoupper($emp->jabatan) }}</div>
                                <div class="text-[11px] text-slate-500 flex items-center gap-1 mt-0.5">
                                    <i class="fa-solid fa-location-dot text-[10px] text-slate-400"></i>
                                    <span>{{ $emp->area }}</span>
                                    @if($emp->divisi)
                                        <span class="text-slate-300">•</span>
                                        <span class="text-[10px] uppercase text-slate-400">{{ $emp->divisi }}</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Col 6: PRINSIPLE & TIPE + LOGIN ACCESS BADGE -->
                            <td>
                                <span class="font-semibold text-xs text-slate-800 block">{{ $emp->prinsiple ?? '-' }}</span>
                                <div class="flex items-center gap-1 flex-wrap mt-1">
                                    @if($emp->tipe_karyawan === 'Inhouse')
                                        <span class="inline-flex items-center gap-1 text-[10px] px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 font-bold border border-blue-200 shadow-xs">
                                            <i class="fa-solid fa-house-chimney text-[9px]"></i> Inhouse
                                        </span>
                                        <span class="inline-flex items-center gap-1 text-[10px] px-1.5 py-0.5 rounded-md bg-emerald-50 text-emerald-700 font-bold border border-emerald-200" title="Karyawan Inhouse otomatis dapat login">
                                            <i class="fa-solid fa-lock-open text-[8px]"></i> Login
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-[10px] px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 font-semibold border border-slate-200">
                                            <i class="fa-solid fa-briefcase text-[9px]"></i> RateCard
                                        </span>
                                        @if($emp->akses_login)
                                            <span class="inline-flex items-center gap-1 text-[10px] px-1.5 py-0.5 rounded-md bg-emerald-50 text-emerald-700 font-bold border border-emerald-200" title="Akses Login Diberikan oleh HR">
                                                <i class="fa-solid fa-lock-open text-[8px]"></i> Login Diizinkan
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-[10px] px-1.5 py-0.5 rounded-md bg-slate-100 text-slate-400 font-medium border border-slate-200" title="Akses Login Belum Diberikan (Terkunci)">
                                                <i class="fa-solid fa-lock text-[8px]"></i> No Login
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </td>

                            <!-- Col 7: PIMPINAN -->
                            <td>
                                <div class="font-semibold text-xs text-slate-700">{{ $emp->pimpinan ?? '-' }}</div>
                                @if($emp->jabatan_pimpinan)
                                    <div class="text-[10px] font-bold text-primary">{{ strtoupper($emp->jabatan_pimpinan) }}</div>
                                @endif
                            </td>

                            <!-- Col 8: TGL. JOIN -->
                            <td>
                                <div class="text-xs font-semibold text-slate-700">{{ $emp->formatted_join_date }}</div>
                                <div class="text-[10px] text-slate-400 font-medium mt-0.5">
                                    {{ $emp->years_of_service }}
                                </div>
                            </td>

                            <!-- Col 9: 5 TAHUN -->
                            <td>
                                <div class="text-xs font-bold text-slate-800">{{ $emp->five_years_date }}</div>
                                <div class="text-[10px] text-slate-400">Masa Evaluasi</div>
                            </td>

                            <!-- Col 10: STATUS -->
                            <td class="text-center">
                                <span class="badge-pill {{ $badge['bg'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $badge['dot'] }}"></span>
                                    <span>{{ $badge['label'] }}</span>
                                </span>
                            </td>

                            <!-- Col 11: TOOLS -->
                            <td class="text-center">
                                <div class="inline-flex items-center gap-1">
                                    <!-- Detail button -->
                                    <button onclick="viewEmployeeDetail({{ json_encode($emp) }})" 
                                            class="w-7 h-7 rounded-lg bg-blue-50 text-primary hover:bg-primary hover:text-white flex items-center justify-center text-xs transition-all shadow-sm" title="Detail Profil & Kredensial Login">
                                        <i class="bx bx-list-ul text-sm"></i>
                                    </button>

                                    <!-- Edit button -->
                                    <button onclick="editEmployee({{ json_encode($emp) }})" 
                                            class="w-7 h-7 rounded-lg bg-amber-50 text-amber-700 hover:bg-amber-600 hover:text-white flex items-center justify-center text-xs transition-all shadow-sm" title="Edit Data & Hak Akses">
                                        <i class="bx bx-edit text-sm"></i>
                                    </button>

                                    <!-- Quick Toggle Akses Login button for RateCard -->
                                    @if($emp->tipe_karyawan === 'RateCard' && $emp->status !== 'Resign')
                                        <form action="{{ route('master.karyawan.toggle-login', $emp->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    onclick="return confirm('{{ $emp->akses_login ? 'Cabut izin akses login untuk ' . $emp->nama_karyawan . '?' : 'Beri izin akses login untuk ' . $emp->nama_karyawan . '?' }}');"
                                                    class="w-7 h-7 rounded-lg {{ $emp->akses_login ? 'bg-emerald-100 text-emerald-700 hover:bg-rose-100 hover:text-rose-700' : 'bg-slate-100 text-slate-400 hover:bg-emerald-600 hover:text-white' }} flex items-center justify-center text-xs transition-all shadow-sm" 
                                                    title="{{ $emp->akses_login ? 'Akses Login Aktif (Klik untuk cabut izin)' : 'Akses Login Terkunci (Klik untuk beri izin login)' }}">
                                                <i class="fa-solid {{ $emp->akses_login ? 'fa-lock-open text-emerald-600' : 'fa-lock text-slate-400' }} text-[11px]"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Resign button with confirmation -->
                                    @if($emp->status !== 'Resign')
                                        <a href="{{ route('master.karyawan.resign', $emp->id) }}" 
                                           onclick="return confirm('Karyawan Benar Sudah Resign? Konfirmasi perubahan status menjadi Resign.');"
                                           class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white flex items-center justify-center text-xs transition-all shadow-sm" title="Tandai Resign">
                                            <i class="bx bx-user-x text-sm"></i>
                                        </a>
                                    @endif

                                    <!-- Switch User simulator -->
                                    <a href="{{ route('master.karyawan.switch', $emp->nik) }}" 
                                       class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white flex items-center justify-center text-xs transition-all shadow-sm" title="Switch User Akun">
                                        <i class="ri-user-shared-line text-sm"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center py-12">
                                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3 text-lg">
                                    <i class="fa-solid fa-user-slash"></i>
                                </div>
                                <div class="text-sm font-bold text-slate-700">Tidak ada data karyawan ditemukan</div>
                                <div class="text-xs text-slate-400 mt-1">Coba sesuaikan kata kunci pencarian atau reset filter status.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="text-xs text-slate-500">
                Menampilkan halaman <span class="font-bold text-slate-800">{{ $employees->currentPage() }}</span> dari <span class="font-bold text-slate-800">{{ $employees->lastPage() }}</span>
            </div>
            <div>
                {{ $employees->links() }}
            </div>
        </div>
    </div>

</div>

<!-- ========================================== -->
<!-- MODAL: ADD KARYAWAN                        -->
<!-- ========================================== -->
<div id="addEmployeeModal" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl max-w-2xl w-full shadow-2xl border border-slate-200 overflow-hidden my-8">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-primary text-white flex items-center justify-center text-sm shadow-sm">
                    <i class="fa-solid fa-user-plus"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Add Karyawan Baru</h3>
                    <p class="text-[11px] text-slate-500">Isi data pegawai baru. Password default otomatis diset ke Tanggal Lahir (ddmmyyyy).</p>
                </div>
            </div>
            <button onclick="closeModal('addEmployeeModal')" class="text-slate-400 hover:text-slate-700">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="{{ route('master.karyawan.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nomor KTP (NIK) <span class="text-rose-500">*</span></label>
                    <input type="number" name="nik" required placeholder="Contoh: 3171011504950001" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary focus:outline-none bg-slate-50/50 font-mono">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_karyawan" required placeholder="Nama Lengkap Karyawan" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary focus:outline-none bg-slate-50/50">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Email Karyawan (Username Login) <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" required placeholder="karyawan@arina.co.id" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary focus:outline-none bg-slate-50/50">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nomor HP / WhatsApp <span class="text-rose-500">*</span></label>
                    <input type="text" name="telepon" required placeholder="0812xxxxxxx" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary focus:outline-none bg-slate-50/50">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Lahir <span class="text-rose-500">*</span></label>
                    <input type="date" name="tanggal_lahir" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary focus:outline-none bg-slate-50/50">
                    <p class="text-[10px] text-slate-400 mt-1">Format <strong>ddmmyyyy</strong> (contoh: 28051997) otomatis menjadi password login.</p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Join <span class="text-rose-500">*</span></label>
                    <input type="date" name="tanggal_join" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary focus:outline-none bg-slate-50/50">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Area <span class="text-rose-500">*</span></label>
                    <select name="area" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary bg-slate-50/50">
                        <option value="" disabled selected>Pilih Area</option>
                        @foreach($distinctArea as $ar)
                            <option value="{{ $ar }}">{{ $ar }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Jabatan <span class="text-rose-500">*</span></label>
                    <select name="jabatan" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary bg-slate-50/50">
                        <option value="" disabled selected>Pilih Jabatan</option>
                        @foreach($distinctJabatan as $jab)
                            <option value="{{ $jab }}">{{ strtoupper($jab) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Prinsiple <span class="text-rose-500">*</span></label>
                    <select name="prinsiple" id="add_prinsiple_select" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary bg-slate-50/50">
                        <option value="" disabled selected>Pilih Prinsiple</option>
                        <optgroup label="Entitas Arina Group (Inhouse)">
                            <option value="PT Arina Multi Karya">PT Arina Multi Karya (AMK)</option>
                            <option value="PT Alva Karya Perkasa">PT Alva Karya Perkasa (AKP)</option>
                            <option value="PT Anugrah Terpercaya Kerja">PT Anugrah Terpercaya Kerja (ATK)</option>
                            <option value="PT Arina Bintang Operasional">PT Arina Bintang Operasional (ABO)</option>
                            <option value="PT Anugrah Tri Berkah">PT Anugrah Tri Berkah (ATB)</option>
                        </optgroup>
                        <optgroup label="Client Luar / Distributor (RateCard)">
                            @foreach($distinctPrinciples as $prin)
                                @if(!in_array($prin->name, ['PT Arina Multi Karya', 'PT Alva Karya Perkasa', 'PT Anugrah Terpercaya Kerja', 'PT Arina Bintang Operasional', 'PT Anugrah Tri Berkah']))
                                    <option value="{{ $prin->name }}">{{ $prin->name }}</option>
                                @endif
                            @endforeach
                        </optgroup>
                    </select>
                    <p class="text-[10px] text-slate-400 mt-1">Jika memilih 5 entitas maka otomatis Inhouse, selain itu RateCard.</p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Divisi</label>
                    <select name="divisi" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary bg-slate-50/50">
                        <option value="OPERASIONAL" selected>OPERASIONAL</option>
                        <option value="SALES & MARKETING">SALES & MARKETING</option>
                        <option value="HRD">HRD</option>
                        <option value="FINANCE & GA">FINANCE & GA</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Pimpinan Langsung</label>
                <select name="pimpinan" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary bg-slate-50/50">
                    <option value="" selected>Pilih Pimpinan</option>
                    @foreach($distinctPimpinan as $pim)
                        <option value="{{ $pim->nama_karyawan }}">{{ $pim->nama_karyawan }} - {{ $pim->jabatan }} ({{ $pim->area }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Pengaturan Akses Login (Untuk RateCard) -->
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 space-y-1">
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="akses_login" value="1" class="rounded text-primary focus:ring-primary h-4 w-4">
                    <span class="text-xs font-bold text-slate-800">Beri Izin Akses Login Sistem (Untuk RateCard)</span>
                </label>
                <p class="text-[11px] text-slate-500 pl-6">
                    Karyawan <strong>Inhouse</strong> otomatis memiliki izin login. Untuk <strong>RateCard</strong>, centang opsi ini agar karyawan diberikan izin akses masuk ke aplikasi.
                </p>
            </div>

            <div class="pt-4 border-t border-slate-200 flex items-center justify-end gap-2">
                <button type="button" onclick="closeModal('addEmployeeModal')" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-100">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-primary text-white text-xs font-bold hover:bg-primary-700 shadow-md shadow-primary-600/20">
                    <i class="bx bx-save mr-1"></i> Simpan Karyawan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL: DETAIL KARYAWAN                     -->
<!-- ========================================== -->
<div id="detailEmployeeModal" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50">
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-id-card text-primary"></i> Detail Lengkap & Akses Login Karyawan
            </h3>
            <button onclick="closeModal('detailEmployeeModal')" class="text-slate-400 hover:text-slate-700">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="p-6 space-y-3 text-xs" id="detailModalBody">
            <!-- Dynamic JS injection -->
        </div>
        <div class="px-6 py-3 border-t border-slate-200 bg-slate-50 flex justify-end">
            <button onclick="closeModal('detailEmployeeModal')" class="px-4 py-1.5 rounded-xl bg-slate-200 text-slate-700 text-xs font-semibold hover:bg-slate-300">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL: EDIT KARYAWAN                       -->
<!-- ========================================== -->
<div id="editEmployeeModal" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-2xl w-full shadow-2xl border border-slate-200 overflow-hidden my-8">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50">
            <h3 class="text-sm font-bold text-slate-900">Edit Data & Pengaturan Akses Karyawan</h3>
            <button onclick="closeModal('editEmployeeModal')" class="text-slate-400 hover:text-slate-700">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form id="editEmployeeForm" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Karyawan</label>
                    <input type="text" id="edit_nama" name="nama_karyawan" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Jabatan</label>
                    <input type="text" id="edit_jabatan" name="jabatan" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Email (Username Login)</label>
                    <input type="email" id="edit_email" name="email" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Telepon / WA</label>
                    <input type="text" id="edit_telepon" name="telepon" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50">
                </div>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Area</label>
                    <input type="text" id="edit_area" name="area" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Prinsiple</label>
                    <input type="text" id="edit_prinsiple" name="prinsiple" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Entitas Odoo</label>
                    <select id="edit_entity" name="entity" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50">
                        <option value="">-- Pilih Entitas --</option>
                        <option value="AMK">AMK - PT Arina Multi Karya</option>
                        <option value="AKP">AKP - PT Alva Karya Perkasa</option>
                        <option value="ATK">ATK - PT Anugrah Terpercaya Kerja</option>
                        <option value="ABO">ABO - PT Arina Bintang Operasional</option>
                        <option value="ATB">ATB - PT Anugrah Tri Berkah</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Lahir (Default Password)</label>
                    <input type="date" id="edit_tanggal_lahir" name="tanggal_lahir" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50">
                    <p class="text-[10px] text-slate-400 mt-0.5">Password default dihitung dari format ddmmyyyy.</p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Status Karyawan</label>
                    <select id="edit_status" name="status" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50 font-semibold">
                        <option value="Aktiv">Aktiv (Aktif)</option>
                        <option value="Resign">Resign</option>
                    </select>
                </div>
            </div>

            <!-- Setting Akses Login RateCard -->
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 space-y-1">
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="edit_akses_login" name="akses_login" value="1" class="rounded text-primary focus:ring-primary h-4 w-4">
                    <span class="text-xs font-bold text-slate-800">Beri Izin Akses Login Sistem (Untuk RateCard)</span>
                </label>
                <p class="text-[11px] text-slate-500 pl-6">
                    Karyawan Inhouse otomatis memiliki hak login. Centang opsi ini jika karyawan RateCard ini diizinkan login ke aplikasi.
                </p>
            </div>

            <input type="hidden" id="edit_tanggal_join" name="tanggal_join">
            <div class="pt-4 border-t border-slate-200 flex justify-end gap-2">
                <button type="button" onclick="closeModal('editEmployeeModal')" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-xs font-semibold">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-primary text-white text-xs font-bold">Update Data</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }
    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    function viewEmployeeDetail(emp) {
        const body = document.getElementById('detailModalBody');
        const isEmployeeInhouse = emp.tipe_karyawan === 'Inhouse';
        const hasAccess = isEmployeeInhouse || Boolean(emp.akses_login);
        const defPassword = emp.default_password || 'ddmmyyyy';

        body.innerHTML = `
            <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl mb-3 border border-slate-100">
                <div class="w-10 h-10 rounded-xl bg-primary text-white flex items-center justify-center font-bold text-base shadow-sm">
                    ${emp.nama_karyawan.charAt(0)}
                </div>
                <div>
                    <div class="text-sm font-bold text-slate-900">${emp.nama_karyawan}</div>
                    <div class="text-slate-500 font-mono text-[11px]">${emp.nik} • ${emp.nip || 'N/A'}</div>
                </div>
            </div>

            <!-- Kredensial Login Card -->
            <div class="p-3.5 rounded-xl ${hasAccess ? 'bg-emerald-50/80 border border-emerald-200' : 'bg-slate-100 border border-slate-200'} space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold ${hasAccess ? 'text-emerald-900' : 'text-slate-700'} flex items-center gap-1.5">
                        <i class="fa-solid ${hasAccess ? 'fa-lock-open text-emerald-600' : 'fa-lock text-slate-500'}"></i> Status Akses Login
                    </span>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full ${hasAccess ? 'bg-emerald-200 text-emerald-800' : 'bg-slate-200 text-slate-600'}">
                        ${isEmployeeInhouse ? 'Aktif (Inhouse)' : (emp.akses_login ? 'Aktif (RateCard Berizin)' : 'Terkunci (RateCard)')}
                    </span>
                </div>
                <div class="grid grid-cols-2 gap-2 text-xs pt-1 border-t ${hasAccess ? 'border-emerald-200/60' : 'border-slate-200'}">
                    <div>
                        <span class="text-slate-500 text-[10px] uppercase font-bold block">Username (Email):</span>
                        <strong class="font-mono text-slate-900 text-xs">${emp.email || '-'}</strong>
                    </div>
                    <div>
                        <span class="text-slate-500 text-[10px] uppercase font-bold block">Default Password:</span>
                        <div class="inline-flex items-center gap-1 mt-0.5">
                            <code class="bg-white px-2 py-0.5 rounded border border-slate-300 font-bold text-primary text-xs">${defPassword}</code>
                            <span class="text-[10px] text-slate-400 font-medium">(ddmmyyyy)</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Info Grid -->
            <div class="grid grid-cols-2 gap-3 pt-2">
                <div>
                    <span class="text-slate-400 block text-[11px]">Entitas Odoo:</span> 
                    <span class="inline-block px-2 py-0.5 rounded text-xs font-bold bg-blue-50 text-primary border border-blue-200 mt-0.5">${emp.entity || 'AMK'}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Tipe Karyawan:</span> 
                    <span class="inline-block px-2 py-0.5 rounded text-xs font-bold ${isEmployeeInhouse ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-slate-100 text-slate-700 border border-slate-200'} mt-0.5">
                        ${emp.tipe_karyawan || 'RateCard'}
                    </span>
                </div>
                <div><span class="text-slate-400 block text-[11px]">Tanggal Lahir:</span> <strong class="text-slate-800">${emp.formatted_birth_date || emp.tanggal_lahir || '-'}</strong></div>
                <div><span class="text-slate-400 block text-[11px]">Tanggal Join:</span> <strong class="text-slate-800">${emp.formatted_join_date || emp.tanggal_join || '-'}</strong></div>
                <div><span class="text-slate-400 block text-[11px]">Jabatan:</span> <strong class="text-slate-800">${emp.jabatan}</strong></div>
                <div><span class="text-slate-400 block text-[11px]">Area:</span> <strong class="text-slate-800">${emp.area}</strong></div>
                <div><span class="text-slate-400 block text-[11px]">Prinsiple:</span> <strong class="text-slate-800">${emp.prinsiple || '-'}</strong></div>
                <div><span class="text-slate-400 block text-[11px]">Pimpinan:</span> <strong class="text-slate-800">${emp.pimpinan || '-'}</strong></div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Status Karyawan:</span> 
                    <span class="badge-pill bg-emerald-50 text-emerald-700 border-emerald-200 font-bold mt-0.5">${emp.status}</span>
                </div>
                <div><span class="text-slate-400 block text-[11px]">WhatsApp:</span> <strong class="text-emerald-700">${emp.telepon || '-'}</strong></div>
            </div>
        `;
        openModal('detailEmployeeModal');
    }

    function editEmployee(emp) {
        const form = document.getElementById('editEmployeeForm');
        form.action = `/master/karyawan/${emp.id}`;
        document.getElementById('edit_nama').value = emp.nama_karyawan;
        document.getElementById('edit_jabatan').value = emp.jabatan;
        document.getElementById('edit_email').value = emp.email || '';
        document.getElementById('edit_telepon').value = emp.telepon || '';
        document.getElementById('edit_area').value = emp.area;
        document.getElementById('edit_prinsiple').value = emp.prinsiple || '';
        document.getElementById('edit_status').value = emp.status;
        if (document.getElementById('edit_entity')) {
            document.getElementById('edit_entity').value = emp.entity || '';
        }
        if (document.getElementById('edit_tanggal_lahir')) {
            document.getElementById('edit_tanggal_lahir').value = emp.tanggal_lahir ? emp.tanggal_lahir.substring(0, 10) : '';
        }
        if (document.getElementById('edit_akses_login')) {
            document.getElementById('edit_akses_login').checked = Boolean(emp.akses_login);
        }
        document.getElementById('edit_tanggal_join').value = emp.tanggal_join;
        openModal('editEmployeeModal');
    }
</script>
@endsection
