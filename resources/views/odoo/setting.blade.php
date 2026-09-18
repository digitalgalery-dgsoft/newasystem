@extends('layouts.app')

@section('title', 'Setting Sync Odoo ERP (5 Entitas) - ASystem ESA Groups')

@section('content')
<div class="space-y-6">
    <!-- PAGE HEADER -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-primary mb-1">
                <i class="fa-solid fa-arrows-rotate text-primary animate-spin-slow"></i>
                <span>Master Data &bull; Integrasi Odoo ERP XML-RPC</span>
            </div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight flex items-center gap-3">
                <span>Setting Sinkronisasi Odoo ERP</span>
                <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-200">5 Entitas Perusahaan</span>
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Kelola kredensial koneksi XML-RPC & sinkronisasi data karyawan (<code>hr.employee</code>) untuk entitas AMK, AKP, ATK, ABO, dan ATB.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Tombol Bersihkan Duplikat NIK -->
            <form action="{{ route('odoo.setting.cleanup-duplicates') }}" method="POST" onsubmit="return confirm('Sistem akan memindai dan merapikan seluruh data NIK ganda dengan aman. Lanjutkan?')">
                @csrf
                <button type="submit" class="btn-att-secondary text-xs hover:border-amber-300 hover:text-amber-700" title="Bersihkan dan gabungkan data NIK duplikat">
                    <i class="fa-solid fa-wand-magic-sparkles text-amber-500"></i>
                    <span>Bersihkan Duplikat NIK</span>
                </button>
            </form>

            <!-- Tombol Sync Semua Entitas -->
            <button type="button" onclick="document.getElementById('modalSyncAll').classList.remove('hidden')" class="btn-att-primary text-xs bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 shadow-md flex items-center gap-2">
                <i class="fa-solid fa-cloud-arrow-down text-sm"></i>
                <span>Sync Semua Entitas...</span>
            </button>

            <!-- Link ke Master Karyawan -->
            <a href="{{ route('master.karyawan.index') }}" class="btn-att-secondary text-xs">
                <i class="fa-solid fa-users-gear text-slate-500"></i>
                <span>Data Karyawan</span>
            </a>
        </div>
    </div>

    <!-- FLASH MESSAGES -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3.5 rounded-2xl flex items-start gap-3 text-xs shadow-sm">
            <i class="fa-solid fa-circle-check text-emerald-600 text-base mt-0.5 shrink-0"></i>
            <div class="flex-1 font-medium leading-relaxed">
                {{ session('success') }}
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    @if(session('warning'))
        <div class="bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3.5 rounded-2xl flex items-start gap-3 text-xs shadow-sm">
            <i class="fa-solid fa-triangle-exclamation text-amber-600 text-base mt-0.5 shrink-0"></i>
            <div class="flex-1 font-medium leading-relaxed">
                {{ session('warning') }}
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-amber-500 hover:text-amber-700">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3.5 rounded-2xl flex items-start gap-3 text-xs shadow-sm">
            <i class="fa-solid fa-circle-xmark text-rose-600 text-base mt-0.5 shrink-0"></i>
            <div class="flex-1 font-medium leading-relaxed">
                {{ session('error') }}
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    <!-- METRIC SUMMARY CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Total Karyawan -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 border border-blue-100 text-primary flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-id-card-clip"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Karyawan</p>
                <p class="text-2xl font-black text-slate-800">{{ number_format($stats['total_karyawan']) }}</p>
                <span class="text-[11px] font-semibold text-blue-600">Terdaftar di Database</span>
            </div>
        </div>

        <!-- 2. Karyawan Aktif -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-user-check"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Karyawan Aktif</p>
                <p class="text-2xl font-black text-slate-800">{{ number_format($stats['total_aktif']) }}</p>
                <div class="flex items-center gap-1.5 mt-0.5 text-[10px] font-bold">
                    <span class="text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200" title="Karyawan Inhouse (5 Entitas)">Inhouse: {{ number_format($stats['total_inhouse'] ?? 0) }}</span>
                    <span class="text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-200" title="Karyawan RateCard">RateCard: {{ number_format($stats['total_ratecard'] ?? 0) }}</span>
                </div>
            </div>
        </div>

        <!-- 3. Entitas Terkonfigurasi -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-50 border border-purple-100 text-purple-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-server"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Odoo Terkonfigurasi</p>
                <p class="text-2xl font-black text-slate-800">{{ $stats['configured_entities'] }} <span class="text-sm font-normal text-slate-400">/ 5 Entitas</span></p>
                <span class="text-[11px] font-semibold text-purple-600">Kredensial Lengkap</span>
            </div>
        </div>

        <!-- 4. Entitas Aktif -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-cyan-50 border border-cyan-100 text-cyan-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-network-wired"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Entitas Aktif</p>
                <p class="text-2xl font-black text-slate-800">{{ $stats['active_entities'] }} <span class="text-sm font-normal text-slate-400">/ 5 Entitas</span></p>
                <span class="text-[11px] font-semibold text-cyan-600">Siap Sinkronisasi</span>
            </div>
        </div>
    </div>

    <!-- BANNER ATURAN INHOUSE VS RATECARD -->
    <div class="bg-gradient-to-r from-blue-50/80 via-indigo-50/50 to-slate-50 p-4 rounded-2xl border border-blue-200/70 shadow-xs flex flex-col md:flex-row items-start md:items-center justify-between gap-3 text-xs">
        <div class="flex items-start gap-3">
            <div class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center text-base shrink-0 shadow-xs">
                <i class="fa-solid fa-building-shield"></i>
            </div>
            <div class="space-y-0.5">
                <div class="flex items-center gap-2">
                    <span class="font-extrabold text-slate-800">Ketentuan Kategori: Inhouse vs RateCard</span>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">Hanya Employee Aktif</span>
                </div>
                <p class="text-slate-600 text-[11px] leading-relaxed">
                    Daftar Entitas Inhouse: 
                    <strong class="text-blue-700">PT ARINA MULTI KARYA</strong>, 
                    <strong class="text-emerald-700">PT ALVA KARYA PERKASA</strong>, 
                    <strong class="text-purple-700">PT ANUGRAH TERPERCAYA KERJA</strong>, 
                    <strong class="text-amber-700">PT ABADI BERKAT ODELIA</strong>, 
                    <strong class="text-cyan-700">PT ANUGRAH TALENTA BERKARYA</strong>.
                    Jika nilai <strong>Prinsiple</strong> pada data Odoo sama dengan entitas tersebut, maka otomatis dikategorikan sebagai <span class="px-1.5 py-0.5 font-bold rounded bg-emerald-100 text-emerald-800 border border-emerald-200">Inhouse</span>. Jika selain itu, dikategorikan sebagai <span class="px-1.5 py-0.5 font-bold rounded bg-blue-100 text-blue-800 border border-blue-200">RateCard</span>.
                </p>
            </div>
        </div>
    </div>

    <!-- TAB NAVIGATION: 5 ENTITAS (AMK, AKP, ATK, ABO, ATB) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="border-b border-slate-200/80 bg-slate-50/50 p-2 sm:p-3">
            <div class="flex flex-wrap items-center gap-2">
                @foreach($entities as $ent)
                    @php
                        $isSelected = ($currentEntity->code === $ent->code);
                        $badgeColor = match($ent->code) {
                            'AMK' => 'blue',
                            'AKP' => 'emerald',
                            'ATK' => 'purple',
                            'ABO' => 'amber',
                            'ATB' => 'cyan',
                            default => 'slate'
                        };
                    @endphp
                    <a href="{{ route('odoo.setting.index', ['tab' => $ent->code]) }}" 
                       class="flex-1 min-w-[170px] sm:min-w-[190px] p-3 rounded-xl border transition-all duration-200 text-left {{ $isSelected ? 'bg-white border-primary shadow-sm ring-2 ring-primary/10' : 'bg-white/60 border-slate-200/70 hover:bg-white hover:border-slate-300' }}">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <span class="text-xs font-black px-2 py-0.5 rounded-md uppercase tracking-wider bg-{{ $badgeColor }}-50 text-{{ $badgeColor }}-700 border border-{{ $badgeColor }}-200">
                                {{ $ent->code }}
                            </span>
                            @if($ent->isConfigured())
                                <span class="flex items-center gap-1 text-[10px] font-bold text-emerald-600">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    <span>Terkonfigurasi</span>
                                </span>
                            @else
                                <span class="flex items-center gap-1 text-[10px] font-medium text-slate-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                                    <span>Belum Diisi</span>
                                </span>
                            @endif
                        </div>
                        <p class="text-xs font-bold text-slate-800 truncate">{{ $ent->name }}</p>
                        <div class="flex items-center justify-between text-[11px] text-slate-500 mt-1">
                            <span>Karyawan: <strong>{{ number_format($ent->active_employees_count) }}</strong></span>
                            @if($ent->last_sync_at)
                                <span class="text-[10px] text-slate-400 truncate">{{ $ent->last_sync_at->diffForHumans() }}</span>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- CURRENT ENTITY DETAIL & CONFIGURATION PANEL -->
        <div class="p-5 sm:p-6 space-y-6">
            <!-- ENTITY TOP BANNER -->
            @php
                $entColor = match($currentEntity->code) {
                    'AMK' => 'blue',
                    'AKP' => 'emerald',
                    'ATK' => 'purple',
                    'ABO' => 'amber',
                    'ATB' => 'cyan',
                    default => 'slate'
                };
            @endphp
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 p-4 rounded-2xl bg-gradient-to-r from-slate-50 via-white to-slate-50 border border-slate-200/80">
                <div class="flex items-center gap-3.5">
                    <div class="w-14 h-14 rounded-2xl bg-{{ $entColor }}-500 text-white flex items-center justify-center font-black text-xl shadow-md shrink-0">
                        {{ $currentEntity->code }}
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2 mb-0.5">
                            <h2 class="text-lg font-black text-slate-800">{{ $currentEntity->name }}</h2>
                            <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-md bg-{{ $entColor }}-50 text-{{ $entColor }}-700 border border-{{ $entColor }}-200">
                                ENTITAS {{ $currentEntity->code }}
                            </span>
                            @if($currentEntity->is_active)
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    <span>Aktif</span>
                                </span>
                            @else
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 border border-slate-200">
                                    Nonaktif
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-500">
                            Sinkronisasi data otomatis dengan database Odoo ERP entitas <strong>{{ $currentEntity->name }}</strong> via protokol XML-RPC.
                        </p>
                    </div>
                </div>

                <!-- Last Sync Tag -->
                <div class="flex items-center gap-3 bg-white p-2.5 px-4 rounded-xl border border-slate-200 shadow-sm shrink-0">
                    <div class="w-9 h-9 rounded-lg bg-blue-50 text-primary flex items-center justify-center text-sm">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Terakhir Sinkron</p>
                        @if($currentEntity->last_sync_at)
                            <p class="text-xs font-bold text-slate-800">{{ $currentEntity->last_sync_at->format('d M Y, H:i') }}</p>
                            <span class="text-[10px] font-semibold text-emerald-600">{{ $currentEntity->last_sync_status ? strtoupper($currentEntity->last_sync_status) : 'SELESAI' }}</span>
                        @else
                            <p class="text-xs font-medium text-slate-400">Belum pernah disinkron</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- TWO COLUMN LAYOUT: CREDENTIALS FORM & ACTIONS HUB -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                <!-- FORM CONFIGURATION (7 COLS) -->
                <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm">
                    <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-key text-primary text-sm"></i>
                            <h3 class="text-sm font-black text-slate-800 uppercase tracking-wider">Kredensial Koneksi Odoo XML-RPC</h3>
                        </div>
                        <span class="text-[11px] text-slate-400">Protokol: <code>/xmlrpc/2/common</code> &amp; <code>/xmlrpc/2/object</code></span>
                    </div>

                    <form action="{{ route('odoo.setting.update', $currentEntity->code) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <!-- 1. Nama Entitas / Perusahaan -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Nama Entitas Perusahaan <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name', $currentEntity->name) }}" required
                                   class="w-full text-xs font-semibold px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                                   placeholder="Contoh: PT Arina Multi Karya">
                        </div>

                        <!-- 2. Odoo Server URL -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Odoo Server URL <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-xs">
                                    <i class="fa-solid fa-globe"></i>
                                </span>
                                <input type="url" name="odoo_url" value="{{ old('odoo_url', $currentEntity->odoo_url) }}" required
                                       class="w-full text-xs font-semibold pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all font-mono"
                                       placeholder="https://odoo.arinamultikarya.com atau http://192.168.1.100:8069">
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1">Gunakan URL lengkap dengan protokol <code>https://</code> atau <code>http://</code>.</p>
                        </div>

                        <!-- 3. Database Name -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Nama Database Odoo <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-xs">
                                    <i class="fa-solid fa-database"></i>
                                </span>
                                <input type="text" name="odoo_db" value="{{ old('odoo_db', $currentEntity->odoo_db) }}" required
                                       class="w-full text-xs font-semibold pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all font-mono"
                                       placeholder="Contoh: AMK_LIVE, odoo_amk, arina_prod">
                            </div>
                        </div>

                        <!-- 4. Username / Email -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Username / Email Login Odoo <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-xs">
                                    <i class="fa-solid fa-envelope"></i>
                                </span>
                                <input type="text" name="odoo_username" value="{{ old('odoo_username', $currentEntity->odoo_username) }}" required
                                       class="w-full text-xs font-semibold pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                                       placeholder="Contoh: admin@arinamultikarya.com">
                            </div>
                        </div>

                        <!-- 5. API Key / Password -->
                        <div x-data="{ showKey: false }">
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Odoo API Key / Token / Password <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-xs">
                                    <i class="fa-solid fa-lock"></i>
                                </span>
                                <input :type="showKey ? 'text' : 'password'" name="odoo_api_key" value="{{ old('odoo_api_key', $currentEntity->odoo_api_key) }}" required
                                       class="w-full text-xs font-semibold pl-9 pr-10 py-2.5 rounded-xl border border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all font-mono"
                                       placeholder="Masukkan Odoo API Key atau password user">
                                <button type="button" @click="showKey = !showKey" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 text-xs">
                                    <i :class="showKey ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'"></i>
                                </button>
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1">Disarankan menggunakan <strong>API Key</strong> yang dibuat melalui menu Profil User Odoo &gt; Account Security.</p>
                        </div>

                        <!-- 6. Status Switch (Active / Inactive) -->
                        <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                            <div>
                                <span class="text-xs font-bold text-slate-800">Status Entitas Aktif</span>
                                <p class="text-[11px] text-slate-400">Jika dinonaktifkan, entitas ini akan dilewati saat Sync Semua Entitas.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ $currentEntity->is_active ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                            </label>
                        </div>

                        <!-- SUBMIT & TEST BUTTONS -->
                        <div class="pt-4 flex flex-wrap items-center justify-between gap-3 border-t border-slate-100">
                            <button type="submit" class="btn-att-primary text-xs px-5 py-2.5">
                                <i class="fa-solid fa-floppy-disk"></i>
                                <span>Simpan Pengaturan {{ $currentEntity->code }}</span>
                            </button>

                            <button type="button" id="btnTestConn" onclick="testOdooConnection('{{ $currentEntity->code }}')" 
                                    class="btn-att-secondary text-xs hover:border-blue-300 hover:text-blue-700">
                                <i class="fa-solid fa-signal text-blue-500"></i>
                                <span>Test Koneksi XML-RPC</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- ACTIONS HUB & STATS (5 COLS) -->
                <div class="lg:col-span-5 space-y-4">
                    <!-- SYNC TRIGGER CARD -->
                    <div class="bg-gradient-to-br from-blue-600 via-primary to-indigo-700 text-white rounded-2xl p-5 shadow-lg relative overflow-hidden">
                        <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-white/10 rounded-full blur-xl pointer-events-none"></div>

                        <div class="flex items-center justify-between gap-2 mb-3">
                            <span class="text-[10px] font-extrabold uppercase tracking-wider px-2.5 py-1 rounded-md bg-white/20 backdrop-blur-sm">
                                Eksekusi Sinkronisasi
                            </span>
                            <span class="text-xs font-bold bg-white/10 px-2 py-0.5 rounded-full">Entitas {{ $currentEntity->code }}</span>
                        </div>

                        <h4 class="text-lg font-black tracking-tight mb-1">Sinkronisasi Data Karyawan</h4>
                        <p class="text-xs text-blue-100 leading-relaxed mb-3">
                            Tarik data terbaru <code>hr.employee</code> dari Odoo untuk entitas <strong>{{ $currentEntity->code }}</strong>.
                        </p>

                        <form action="{{ route('odoo.setting.sync', $currentEntity->code) }}" method="POST" onsubmit="return confirm('Mulai proses sinkronisasi data karyawan untuk entitas {{ $currentEntity->code }}?')">
                            @csrf
                            
                            <!-- Category Filter Selection -->
                            <div class="mb-3.5 bg-white/10 p-2.5 rounded-xl border border-white/15 backdrop-blur-xs">
                                <label class="block text-[10px] font-extrabold text-blue-100 uppercase tracking-wider mb-1.5">
                                    Pilih Kategori Karyawan:
                                </label>
                                <div class="grid grid-cols-3 gap-1.5 text-xs">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="category" value="inhouse" checked class="peer sr-only">
                                        <div class="py-1.5 px-2 text-center rounded-lg bg-white/15 border border-white/20 peer-checked:bg-white peer-checked:text-primary peer-checked:font-black transition-all text-[11px]">
                                            Inhouse
                                        </div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="category" value="ratecard" class="peer sr-only">
                                        <div class="py-1.5 px-2 text-center rounded-lg bg-white/15 border border-white/20 peer-checked:bg-white peer-checked:text-primary peer-checked:font-black transition-all text-[11px]">
                                            RateCard
                                        </div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="category" value="all" class="peer sr-only">
                                        <div class="py-1.5 px-2 text-center rounded-lg bg-white/15 border border-white/20 peer-checked:bg-white peer-checked:text-primary peer-checked:font-black transition-all text-[11px]">
                                            Semua
                                        </div>
                                    </label>
                                </div>
                                <div class="flex items-center gap-1.5 mt-2 text-[10px] text-blue-100">
                                    <i class="fa-solid fa-circle-check text-emerald-300"></i>
                                    <span>Hanya employee berstatus <strong>aktif</strong> yang disinkronkan.</span>
                                </div>
                            </div>

                            <button type="submit" class="w-full py-3 px-4 rounded-xl bg-white text-primary font-black text-xs hover:bg-blue-50 transition-all shadow-md flex items-center justify-center gap-2 group">
                                <i class="fa-solid fa-arrows-rotate text-sm group-hover:rotate-180 transition-transform duration-500"></i>
                                <span>SINKRONISASI KARYAWAN {{ $currentEntity->code }}</span>
                            </button>
                        </form>

                        @if($currentEntity->last_sync_message)
                            <div class="mt-3.5 p-2.5 rounded-xl bg-white/10 text-[11px] leading-relaxed backdrop-blur-sm border border-white/15">
                                <span class="font-bold opacity-80">Catatan Terakhir:</span>
                                <span class="opacity-95">{{ $currentEntity->last_sync_message }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- ENTITY EMPLOYEE STATS -->
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm space-y-3">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-users text-primary text-sm"></i>
                                <h4 class="text-xs font-black text-slate-800 uppercase tracking-wider">Statistik Karyawan [{{ $currentEntity->code }}]</h4>
                            </div>
                            <a href="{{ route('master.karyawan.index', ['entity' => $currentEntity->code]) }}" class="text-[11px] font-bold text-primary hover:underline">
                                Buka List &rarr;
                            </a>
                        </div>

                        <div class="grid grid-cols-3 gap-2 text-center">
                            <div class="p-3 rounded-xl bg-emerald-50/60 border border-emerald-100">
                                <p class="text-[10px] font-bold text-emerald-600 uppercase">Aktif</p>
                                <p class="text-xl font-black text-emerald-700">{{ number_format($currentEntity->active_employees_count) }}</p>
                            </div>

                            <div class="p-3 rounded-xl bg-rose-50/60 border border-rose-100">
                                <p class="text-[10px] font-bold text-rose-600 uppercase">Resign</p>
                                <p class="text-xl font-black text-rose-700">{{ number_format($currentEntity->resigned_employees_count) }}</p>
                            </div>

                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                                <p class="text-[10px] font-bold text-slate-500 uppercase">Total</p>
                                <p class="text-xl font-black text-slate-800">{{ number_format($currentEntity->total_employees_count) }}</p>
                            </div>
                        </div>

                        <!-- INFO ALERT -->
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/80 text-[11px] text-slate-600 leading-relaxed flex items-start gap-2.5">
                            <i class="fa-solid fa-circle-info text-blue-500 text-sm mt-0.5 shrink-0"></i>
                            <div>
                                <strong class="text-slate-800">Metode Sinkronisasi:</strong>
                                Menggunakan pencarian berbasis NIK (<code>identification_id</code>) atau NIP (<code>registration_number</code>). Jika data sudah ada, sistem akan memperbarui jabatan &amp; status secara cerdas tanpa menghapus riwayat kandidat.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FITUR SINKRONISASI & CEK 1 DATA KARYAWAN BY NIK -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-user-tag"></i>
                </div>
                <div>
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-wider">Cek &amp; Sinkronisasi 1 Data Karyawan (By NIK)</h3>
                    <p class="text-[11px] text-slate-500">Cek status atau tarik 1 karyawan spesifik langsung dari Odoo berdasarkan NIK / NIP tanpa perlu proses sinkronisasi massal.</p>
                </div>
            </div>
            <div class="flex items-center gap-1.5 text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200 self-start sm:self-auto">
                <i class="fa-solid fa-circle-check"></i>
                <span>Filter Otomatis Karyawan Aktif</span>
            </div>
        </div>

        <form id="formSyncByNik" onsubmit="handleSyncByNik(event)" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
            @csrf
            <!-- Input NIK -->
            <div class="md:col-span-5 space-y-1">
                <label class="block text-xs font-bold text-slate-700">
                    Nomor Induk Karyawan (NIK / NIP) <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-id-card"></i>
                    </div>
                    <input type="text" id="nikInput" name="nik" required placeholder="Contoh: 202400123 / 357801..." 
                           class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                </div>
            </div>

            <!-- Pilih Entitas Odoo -->
            <div class="md:col-span-4 space-y-1">
                <label class="block text-xs font-bold text-slate-700">
                    Entitas Odoo <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-building"></i>
                    </div>
                    <select id="entityCodeInput" name="entity_code" required
                            class="w-full pl-9 pr-8 py-2.5 rounded-xl border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all appearance-none bg-white">
                        @foreach($entities as $ent)
                            <option value="{{ $ent->code }}" {{ $currentEntity->code === $ent->code ? 'selected' : '' }}>
                                {{ $ent->code }} &bull; {{ $ent->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400 text-xs">
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>
                </div>
            </div>

            <!-- Tombol Cek & Sync -->
            <div class="md:col-span-3">
                <button type="submit" id="btnSyncByNik" class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white font-bold text-xs shadow-md transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <span>Cek &amp; Sync NIK</span>
                </button>
            </div>
        </form>

        <!-- HASIL PENGECEKAN / SINKRONISASI 1 DATA (AJAX PREVIEW) -->
        <div id="nikResultContainer" class="hidden mt-3 pt-4 border-t border-slate-100">
            <!-- Dynamic Result Area -->
        </div>
    </div>

    <!-- MODAL SYNC SEMUA ENTITAS DENGAN PILIHAN KATEGORI -->
    <div id="modalSyncAll" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs hidden p-4">
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-md overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-blue-100 text-primary flex items-center justify-center text-base">
                        <i class="fa-solid fa-cloud-arrow-down"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-800">Sync Semua Entitas Odoo</h3>
                        <p class="text-[11px] text-slate-500">Proses sinkronisasi seluruh 5 entitas aktif</p>
                    </div>
                </div>
                <button type="button" onclick="document.getElementById('modalSyncAll').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form action="{{ route('odoo.setting.sync-all') }}" method="POST" class="p-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">
                        Pilih Kategori Sinkronisasi:
                    </label>
                    <div class="space-y-2">
                        <!-- Option 1: Inhouse Saja -->
                        <label class="flex items-start gap-3 p-3 rounded-xl border border-slate-200 hover:border-primary/50 cursor-pointer transition-all has-[:checked]:border-primary has-[:checked]:bg-blue-50/40">
                            <input type="radio" name="category" value="inhouse" checked class="mt-0.5 text-primary focus:ring-primary">
                            <div class="flex-1 text-xs">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-slate-800">Inhouse Saja (5 Entitas)</span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-700">Rekomendasi</span>
                                </div>
                                <p class="text-[11px] text-slate-500 mt-0.5">Prinsiple sama dengan 5 entitas: <strong>AMK, AKP, ATK, ABO, ATB</strong>. Beban proses lebih ringan.</p>
                            </div>
                        </label>

                        <!-- Option 2: RateCard Saja -->
                        <label class="flex items-start gap-3 p-3 rounded-xl border border-slate-200 hover:border-primary/50 cursor-pointer transition-all has-[:checked]:border-primary has-[:checked]:bg-blue-50/40">
                            <input type="radio" name="category" value="ratecard" class="mt-0.5 text-primary focus:ring-primary">
                            <div class="flex-1 text-xs">
                                <span class="font-bold text-slate-800">RateCard Saja</span>
                                <p class="text-[11px] text-slate-500 mt-0.5">Karyawan dengan Prinsiple selain 5 entitas inhouse.</p>
                            </div>
                        </label>

                        <!-- Option 3: Semua Kategori -->
                        <label class="flex items-start gap-3 p-3 rounded-xl border border-slate-200 hover:border-primary/50 cursor-pointer transition-all has-[:checked]:border-primary has-[:checked]:bg-blue-50/40">
                            <input type="radio" name="category" value="all" class="mt-0.5 text-primary focus:ring-primary">
                            <div class="flex-1 text-xs">
                                <span class="font-bold text-slate-800">Semua Kategori (Inhouse + RateCard)</span>
                                <p class="text-[11px] text-slate-500 mt-0.5">Tarik seluruh data karyawan aktif tanpa filter kategori.</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Filter Status Aktif Info -->
                <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200/80 text-[11px] text-emerald-800 flex items-start gap-2.5">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-sm mt-0.5 shrink-0"></i>
                    <div>
                        <span class="font-bold">Hanya Karyawan Aktif:</span>
                        Sistem hanya akan memproses karyawan yang berstatus aktif (tanpa tanggal resign) di Odoo.
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-2">
                    <button type="button" onclick="document.getElementById('modalSyncAll').classList.add('hidden')" class="btn-att-secondary text-xs px-4 py-2">
                        Batal
                    </button>
                    <button type="submit" class="btn-att-primary text-xs px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600">
                        <i class="fa-solid fa-cloud-arrow-down"></i>
                        <span>Mulai Sinkronisasi</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- LOG RIWAYAT SINKRONISASI (RECENT LOGS TABLE) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-primary flex items-center justify-center text-sm">
                    <i class="fa-solid fa-list-check"></i>
                </div>
                <div>
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-wider">Riwayat Log Sinkronisasi Odoo</h3>
                    <p class="text-[11px] text-slate-500">Mencatat 15 batch sinkronisasi data karyawan terakhir dari seluruh entitas.</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/80 text-slate-500 uppercase font-bold text-[10px] tracking-wider border-y border-slate-200/80">
                        <th class="py-3 px-3.5">Waktu Eksekusi</th>
                        <th class="py-3 px-3.5">Entitas</th>
                        <th class="py-3 px-3.5">Batch ID</th>
                        <th class="py-3 px-3.5">Pemicu</th>
                        <th class="py-3 px-3.5">Status</th>
                        <th class="py-3 px-3.5 text-center">Data Baru</th>
                        <th class="py-3 px-3.5 text-center">Diperbarui</th>
                        <th class="py-3 px-3.5 text-center">Resign</th>
                        <th class="py-3 px-3.5">Detail / Pesan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentLogs as $log)
                        @php
                            $badgeStatus = match($log->status) {
                                'success' => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'label' => 'SUKSES'],
                                'partial' => ['bg' => 'bg-amber-50 text-amber-700 border-amber-200', 'label' => 'SEBAGIAN'],
                                default   => ['bg' => 'bg-rose-50 text-rose-700 border-rose-200', 'label' => 'GAGAL']
                            };
                            $entBadge = match($log->entity_code) {
                                'AMK' => 'bg-blue-50 text-blue-700 border-blue-200',
                                'AKP' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                'ATK' => 'bg-purple-50 text-purple-700 border-purple-200',
                                'ABO' => 'bg-amber-50 text-amber-700 border-amber-200',
                                'ATB' => 'bg-cyan-50 text-cyan-700 border-cyan-200',
                                default => 'bg-slate-50 text-slate-700 border-slate-200'
                            };
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3 px-3.5 font-medium text-slate-700 whitespace-nowrap">
                                {{ $log->created_at->format('d M Y, H:i:s') }}
                                <span class="block text-[10px] text-slate-400">{{ $log->created_at->diffForHumans() }}</span>
                            </td>
                            <td class="py-3 px-3.5 whitespace-nowrap">
                                <span class="text-[10px] font-black px-2 py-0.5 rounded-md border uppercase {{ $entBadge }}">
                                    {{ $log->entity_code }}
                                </span>
                            </td>
                            <td class="py-3 px-3.5 font-mono text-[11px] text-slate-500 whitespace-nowrap">
                                {{ $log->batch_id }}
                            </td>
                            <td class="py-3 px-3.5 capitalize font-medium text-slate-600">
                                {{ $log->trigger_type }}
                            </td>
                            <td class="py-3 px-3.5 whitespace-nowrap">
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-md border {{ $badgeStatus['bg'] }}">
                                    {{ $badgeStatus['label'] }}
                                </span>
                            </td>
                            <td class="py-3 px-3.5 text-center font-bold text-emerald-600">
                                +{{ number_format($log->new_count) }}
                            </td>
                            <td class="py-3 px-3.5 text-center font-bold text-blue-600">
                                {{ number_format($log->update_count) }}
                            </td>
                            <td class="py-3 px-3.5 text-center font-bold text-rose-600">
                                {{ number_format($log->resign_count) }}
                            </td>
                            <td class="py-3 px-3.5 text-slate-600 text-[11px] max-w-xs truncate" title="{{ $log->error_message ?: 'Proses berjalan lancar.' }}">
                                {{ $log->error_message ?: 'Proses sinkronisasi berhasil tanpa kendala.' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-slate-400">
                                <i class="fa-solid fa-clock-rotate-left text-2xl mb-1.5 opacity-40 block"></i>
                                <span>Belum ada riwayat proses sinkronisasi Odoo. Klik tombol "SINKRONISASI KARYAWAN" di atas untuk memulai.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- JAVASCRIPT FOR TEST CONNECTION & SYNC BY NIK -->
<script>
function testOdooConnection(entityCode) {
    const btn = document.getElementById('btnTestConn');
    const originalHtml = btn.innerHTML;

    btn.disabled = true;
    btn.innerHTML = `<i class="fa-solid fa-circle-notch fa-spin text-blue-500"></i><span>Menguji Koneksi...</span>`;

    fetch(`{{ url('odoo-setting') }}/${entityCode}/test`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json().then(data => ({ status: response.status, body: data })))
    .then(({ status, body }) => {
        btn.disabled = false;
        btn.innerHTML = originalHtml;

        if (body.success) {
            alert('BERHASIL!\n\n' + body.message);
        } else {
            alert('KONEKSI GAGAL!\n\n' + (body.message || 'Terjadi kesalahan saat menguji koneksi ke server Odoo.'));
        }
    })
    .catch(error => {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
        alert('ERROR KONEKSI:\n\nTidak dapat menghubungi server lokal aplikasi: ' + error.message);
    });
}

function handleSyncByNik(event) {
    event.preventDefault();
    const nikInput = document.getElementById('nikInput');
    const nik = nikInput.value.trim();
    const entityCode = document.getElementById('entityCodeInput').value;
    const btn = document.getElementById('btnSyncByNik');
    const resultContainer = document.getElementById('nikResultContainer');

    if (!nik) {
        alert('Mohon masukkan NIK / NIP terlebih dahulu.');
        nikInput.focus();
        return;
    }

    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = `<i class="fa-solid fa-circle-notch fa-spin text-white"></i><span>Mencari di Odoo...</span>`;

    resultContainer.classList.remove('hidden');
    resultContainer.innerHTML = `
        <div class="p-6 text-center text-slate-500 bg-slate-50 rounded-2xl border border-slate-200/80 animate-pulse">
            <i class="fa-solid fa-arrows-rotate fa-spin text-2xl text-indigo-600 mb-2"></i>
            <p class="text-xs font-bold text-slate-700">Menghubungkan ke Odoo XML-RPC (${entityCode})...</p>
            <p class="text-[11px] text-slate-400 mt-0.5">Mencari data karyawan NIK '${nik}' dan memverifikasi status aktif...</p>
        </div>
    `;

    fetch(`{{ route('odoo.setting.sync-by-nik') }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            nik: nik,
            entity_code: entityCode
        })
    })
    .then(response => response.json().then(data => ({ status: response.status, body: data })))
    .then(({ status, body }) => {
        btn.disabled = false;
        btn.innerHTML = originalHtml;

        if (body.success && body.employee) {
            const emp = body.employee;
            const isInhouse = (emp.tipe_karyawan === 'Inhouse');
            const typeBadge = isInhouse
                ? `<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-300 shadow-xs"><i class="fa-solid fa-building-user text-emerald-600"></i>INHOUSE</span>`
                : `<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-blue-100 text-blue-800 border border-blue-300 shadow-xs"><i class="fa-solid fa-handshake text-blue-600"></i>RATECARD</span>`;

            const actionBadge = body.action === 'created'
                ? `<span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-emerald-200/80 text-emerald-900 border border-emerald-300">DATA BARU DIBUAT</span>`
                : `<span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-blue-200/80 text-blue-900 border border-blue-300">DATA DIPERBARUI</span>`;

            resultContainer.innerHTML = `
                <div class="p-5 bg-gradient-to-r from-emerald-50/60 via-teal-50/40 to-white rounded-2xl border border-emerald-200/90 shadow-sm space-y-4 animate-in fade-in duration-200">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-emerald-200/70">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-lg shadow-sm shrink-0">
                                <i class="fa-solid fa-user-check"></i>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-black uppercase text-emerald-900 tracking-wide">Karyawan Berhasil Disinkronkan</span>
                                    ${actionBadge}
                                </div>
                                <p class="text-xs text-slate-600 mt-0.5">${body.message}</p>
                            </div>
                        </div>
                        <div>
                            ${typeBadge}
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
                        <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-xs">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">NIK / NIP</span>
                            <p class="font-black text-slate-800 text-sm mt-0.5 font-mono">${emp.nik || '-'}</p>
                        </div>
                        <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-xs">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Nama Lengkap</span>
                            <p class="font-black text-slate-800 text-sm mt-0.5">${emp.nama_karyawan || '-'}</p>
                        </div>
                        <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-xs">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Jabatan / Divisi</span>
                            <p class="font-bold text-slate-700 mt-0.5 truncate">${emp.jabatan || '-'}</p>
                            <span class="text-[10px] text-slate-400">${emp.departemen || '-'}</span>
                        </div>
                        <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-xs">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Prinsiple &amp; Entitas</span>
                            <p class="font-bold text-slate-700 mt-0.5 truncate">${emp.prinsiple || '-'}</p>
                            <span class="text-[10px] text-primary font-bold">Entitas: ${emp.entitas || entityCode}</span>
                        </div>
                    </div>

                    <div class="pt-2 flex flex-wrap items-center justify-between gap-3 text-xs border-t border-emerald-100">
                        <div class="flex items-center gap-2 text-[11px] text-slate-600">
                            <span class="inline-flex items-center gap-1 text-emerald-700 font-bold">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Status: Aktif Bekerja
                            </span>
                            <span>&bull;</span>
                            <span>Kategori: <strong>${isInhouse ? 'Inhouse (Prinsiple Termasuk 5 Entitas)' : 'RateCard (Prinsiple Eksternal)'}</strong></span>
                        </div>
                        <a href="{{ route('master.karyawan.index') }}?search=${encodeURIComponent(emp.nik)}" class="btn-att-primary text-xs px-3.5 py-1.5 shadow-xs">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                            <span>Buka di Data Karyawan</span>
                        </a>
                    </div>
                </div>
            `;
        } else {
            resultContainer.innerHTML = `
                <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-xs text-rose-800 space-y-2 animate-in fade-in duration-200">
                    <div class="flex items-start gap-2.5">
                        <i class="fa-solid fa-circle-exclamation text-rose-600 text-lg mt-0.5 shrink-0"></i>
                        <div class="flex-1">
                            <p class="font-black text-rose-900 text-sm">Karyawan Tidak Ditemukan atau Non-Aktif di Odoo</p>
                            <p class="text-xs text-rose-700 mt-0.5">${body.message || 'Data tidak ditemukan pada server Odoo entitas ' + entityCode}</p>
                        </div>
                    </div>
                    <div class="p-2.5 bg-white/60 rounded-xl border border-rose-200 text-[11px] text-rose-700">
                        <strong>Aturan Filter:</strong> Hanya karyawan yang berstatus <em>aktif</em> (field <code>active=True</code> dan tanggal resign/<code>departure_date</code> kosong) yang diizinkan untuk disinkronkan.
                    </div>
                </div>
            `;
        }
    })
    .catch(error => {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
        resultContainer.innerHTML = `
            <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-xs text-rose-800 flex items-start gap-2.5">
                <i class="fa-solid fa-triangle-exclamation text-rose-600 text-lg mt-0.5 shrink-0"></i>
                <div>
                    <p class="font-black text-rose-900">Gagal Menghubungi Server</p>
                    <p class="text-xs text-rose-700 mt-0.5">${error.message}</p>
                </div>
            </div>
        `;
    });
}
</script>
@endsection
