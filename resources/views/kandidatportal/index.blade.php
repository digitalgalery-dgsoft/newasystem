@extends('layouts.app')

@section('title', 'Kandidat Job Portal - Attendance Admin Portal')

@section('content')
<div class="space-y-6" x-data="kandidatPortalManager()">

    <!-- PAGE HEADER CARD -->
    <div class="page-header-card flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-200 flex items-center justify-center text-primary text-xl font-bold">
                    <i class="fa-solid fa-globe"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Kandidat Job Portal</h1>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg bg-blue-50 border border-blue-200 text-blue-800 text-xs font-bold">
                            <i class="fa-solid fa-user-check text-primary"></i>
                            <span>{{ $displayUserName }}</span>
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">Monitoring pelamar lowongan kerja daring, hasil pemindaian AI CV Analyzer, dan tahapan seleksi</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('airanking.index') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold text-amber-700 bg-amber-50 border border-amber-200 hover:bg-amber-100 transition-all shadow-sm">
                <i class="fa-solid fa-ranking-star text-amber-500"></i>
                <span>AI Ranking Leaderboard</span>
            </a>
            <a href="{{ route('job.input') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition-all shadow-sm">
                <i class="fa-solid fa-briefcase text-primary"></i>
                <span>Kelola Lowongan Job</span>
            </a>
            <button @click="openExportModal = true" type="button" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition-all shadow-sm shadow-emerald-600/20">
                <i class="fa-solid fa-file-excel"></i>
                <span>Export Data Excel</span>
            </button>
            <button @click="openSyncOdooModal = true" type="button" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold text-white bg-purple-600 hover:bg-purple-700 transition-all shadow-sm shadow-purple-600/20">
                <i class="fa-solid fa-arrows-rotate"></i>
                <span>Sync Step Odoo</span>
            </button>
        </div>
    </div>

    <!-- 4 STAT CARDS -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Total Pelamar -->
        <div class="stat-box flex items-center gap-4">
            <div class="stat-box-icon bg-blue-50 text-primary border border-blue-100">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Pelamar</div>
                <div class="text-2xl font-black text-slate-900 leading-tight">{{ $totalPelamar }}</div>
                <div class="text-[11px] font-semibold text-primary mt-0.5 truncate" title="{{ $scopeTitle }}">{{ $scopeTitle }}</div>
            </div>
        </div>

        <!-- 2. Masuk Hari Ini -->
        <div class="stat-box flex items-center gap-4">
            <div class="stat-box-icon bg-sky-50 text-sky-600 border border-sky-100">
                <i class="fa-solid fa-calendar-day"></i>
            </div>
            <div>
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Masuk Hari Ini</div>
                <div class="text-2xl font-black text-slate-900 leading-tight">{{ $masukHariIni }}</div>
                <div class="text-[11px] font-semibold text-emerald-600 flex items-center gap-1 mt-0.5">
                    <i class="fa-solid fa-bolt text-[9px]"></i> Pendaftar Baru
                </div>
            </div>
        </div>

        <!-- 3. Kandidat Green -->
        <div class="stat-box flex items-center gap-4">
            <div class="stat-box-icon bg-emerald-50 text-emerald-600 border border-emerald-100">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Kandidat Green</div>
                <div class="text-2xl font-black text-slate-900 leading-tight">{{ $kandidatGreen }}</div>
                <div class="text-[11px] font-semibold text-emerald-600 mt-0.5">Sangat Direkomendasikan AI</div>
            </div>
        </div>

        <!-- 4. Belum Dianalisa -->
        <div class="stat-box flex items-center gap-4">
            <div class="stat-box-icon bg-rose-50 text-rose-600 border border-rose-100">
                <i class="fa-solid fa-microchip"></i>
            </div>
            <div>
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Belum Dianalisa</div>
                <div class="text-2xl font-black text-slate-900 leading-tight">{{ $belumDianalisa }}</div>
                <div class="text-[11px] font-semibold text-rose-600 mt-0.5">Menunggu Screening</div>
            </div>
        </div>
    </div>

    <!-- COMPACT STATS MINI-CARDS: STEP ODOO ERP -->
    @if(isset($odooStats))
    <div class="bg-white border border-slate-200 rounded-2xl p-3 sm:p-4 shadow-sm">
        <div class="flex items-center justify-between gap-2 mb-2.5">
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center text-xs font-bold">
                    <i class="fa-solid fa-arrows-split-up-and-left"></i>
                </div>
                <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">Tahapan Rekrutmen Odoo ERP</h3>
                <span class="text-[10px] font-semibold text-slate-400 hidden sm:inline">&bull; Klik card untuk filter cepat per tahapan</span>
            </div>
            @if(!empty($odooStage))
                <a href="{{ route('kandidatportal.index', request()->except('odoo_stage')) }}" class="inline-flex items-center gap-1 text-[11px] font-bold text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 px-2.5 py-1 rounded-lg transition-colors shadow-xs">
                    <i class="fa-solid fa-xmark text-[10px]"></i>
                    <span>Reset Filter Step</span>
                </a>
            @endif
        </div>
        
        <!-- Grid 8 Mini Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-2">
            <!-- 1. Semua di Odoo -->
            <a href="{{ route('kandidatportal.index', array_merge(request()->except('page'), ['odoo_stage' => ($odooStage === 'matched' ? '' : 'matched')])) }}" 
               class="p-2 sm:p-2.5 rounded-xl border transition-all duration-150 flex flex-col justify-between {{ $odooStage === 'matched' ? 'bg-purple-50/80 border-purple-400 ring-2 ring-purple-400/50 shadow-xs' : 'bg-slate-50/70 hover:bg-white border-slate-200 hover:border-purple-300 hover:shadow-xs' }}">
                <div class="flex items-center justify-between gap-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider {{ $odooStage === 'matched' ? 'text-purple-700' : 'text-slate-500' }}">Semua Odoo</span>
                    <span class="w-5 h-5 rounded-md bg-purple-100 text-purple-700 flex items-center justify-center text-[10px]">
                        <i class="fa-solid fa-bolt"></i>
                    </span>
                </div>
                <div class="text-base sm:text-lg font-black {{ $odooStage === 'matched' ? 'text-purple-800' : 'text-slate-900' }} mt-1">
                    {{ number_format($odooStats['total_odoo'] ?? 0) }}
                </div>
            </a>

            <!-- 2. Data Pelamar -->
            <a href="{{ route('kandidatportal.index', array_merge(request()->except('page'), ['odoo_stage' => ($odooStage === 'Data Pelamar' ? '' : 'Data Pelamar')])) }}" 
               class="p-2 sm:p-2.5 rounded-xl border transition-all duration-150 flex flex-col justify-between {{ $odooStage === 'Data Pelamar' ? 'bg-blue-50/80 border-blue-400 ring-2 ring-blue-400/50 shadow-xs' : 'bg-slate-50/70 hover:bg-white border-slate-200 hover:border-blue-300 hover:shadow-xs' }}">
                <div class="flex items-center justify-between gap-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider {{ $odooStage === 'Data Pelamar' ? 'text-blue-700' : 'text-slate-500' }}">Data Pelamar</span>
                    <span class="w-5 h-5 rounded-md bg-blue-100 text-blue-700 flex items-center justify-center text-[10px]">
                        <i class="fa-solid fa-file-lines"></i>
                    </span>
                </div>
                <div class="text-base sm:text-lg font-black {{ $odooStage === 'Data Pelamar' ? 'text-blue-800' : 'text-slate-900' }} mt-1">
                    {{ number_format($odooStats['data_pelamar'] ?? 0) }}
                </div>
            </a>

            <!-- 3. Interview -->
            <a href="{{ route('kandidatportal.index', array_merge(request()->except('page'), ['odoo_stage' => ($odooStage === 'interview' ? '' : 'interview')])) }}" 
               class="p-2 sm:p-2.5 rounded-xl border transition-all duration-150 flex flex-col justify-between {{ $odooStage === 'interview' ? 'bg-indigo-50/80 border-indigo-400 ring-2 ring-indigo-400/50 shadow-xs' : 'bg-slate-50/70 hover:bg-white border-slate-200 hover:border-indigo-300 hover:shadow-xs' }}">
                <div class="flex items-center justify-between gap-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider {{ $odooStage === 'interview' ? 'text-indigo-700' : 'text-slate-500' }}">Interview</span>
                    <span class="w-5 h-5 rounded-md bg-indigo-100 text-indigo-700 flex items-center justify-center text-[10px]">
                        <i class="fa-solid fa-user-tie"></i>
                    </span>
                </div>
                <div class="text-base sm:text-lg font-black {{ $odooStage === 'interview' ? 'text-indigo-800' : 'text-slate-900' }} mt-1">
                    {{ number_format($odooStats['interview'] ?? 0) }}
                </div>
            </a>

            <!-- 4. Principal -->
            <a href="{{ route('kandidatportal.index', array_merge(request()->except('page'), ['odoo_stage' => ($odooStage === 'Principal' ? '' : 'Principal')])) }}" 
               class="p-2 sm:p-2.5 rounded-xl border transition-all duration-150 flex flex-col justify-between {{ $odooStage === 'Principal' ? 'bg-violet-50/80 border-violet-400 ring-2 ring-violet-400/50 shadow-xs' : 'bg-slate-50/70 hover:bg-white border-slate-200 hover:border-violet-300 hover:shadow-xs' }}">
                <div class="flex items-center justify-between gap-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider {{ $odooStage === 'Principal' ? 'text-violet-700' : 'text-slate-500' }}">Principal</span>
                    <span class="w-5 h-5 rounded-md bg-violet-100 text-violet-700 flex items-center justify-center text-[10px]">
                        <i class="fa-solid fa-user-shield"></i>
                    </span>
                </div>
                <div class="text-base sm:text-lg font-black {{ $odooStage === 'Principal' ? 'text-violet-800' : 'text-slate-900' }} mt-1">
                    {{ number_format($odooStats['principal'] ?? 0) }}
                </div>
            </a>

            <!-- 5. E-Learning -->
            <a href="{{ route('kandidatportal.index', array_merge(request()->except('page'), ['odoo_stage' => ($odooStage === 'elearning' ? '' : 'elearning')])) }}" 
               class="p-2 sm:p-2.5 rounded-xl border transition-all duration-150 flex flex-col justify-between {{ $odooStage === 'elearning' ? 'bg-sky-50/80 border-sky-400 ring-2 ring-sky-400/50 shadow-xs' : 'bg-slate-50/70 hover:bg-white border-slate-200 hover:border-sky-300 hover:shadow-xs' }}">
                <div class="flex items-center justify-between gap-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider {{ $odooStage === 'elearning' ? 'text-sky-700' : 'text-slate-500' }}">E-Learning</span>
                    <span class="w-5 h-5 rounded-md bg-sky-100 text-sky-700 flex items-center justify-center text-[10px]">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </span>
                </div>
                <div class="text-base sm:text-lg font-black {{ $odooStage === 'elearning' ? 'text-sky-800' : 'text-slate-900' }} mt-1">
                    {{ number_format($odooStats['elearning'] ?? 0) }}
                </div>
            </a>

            <!-- 6. PKWT -->
            <a href="{{ route('kandidatportal.index', array_merge(request()->except('page'), ['odoo_stage' => ($odooStage === 'pkwt' ? '' : 'pkwt')])) }}" 
               class="p-2 sm:p-2.5 rounded-xl border transition-all duration-150 flex flex-col justify-between {{ $odooStage === 'pkwt' ? 'bg-amber-50/80 border-amber-400 ring-2 ring-amber-400/50 shadow-xs' : 'bg-slate-50/70 hover:bg-white border-slate-200 hover:border-amber-300 hover:shadow-xs' }}">
                <div class="flex items-center justify-between gap-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider {{ $odooStage === 'pkwt' ? 'text-amber-700' : 'text-slate-500' }}">PKWT</span>
                    <span class="w-5 h-5 rounded-md bg-amber-100 text-amber-700 flex items-center justify-center text-[10px]">
                        <i class="fa-solid fa-file-signature"></i>
                    </span>
                </div>
                <div class="text-base sm:text-lg font-black {{ $odooStage === 'pkwt' ? 'text-amber-800' : 'text-slate-900' }} mt-1">
                    {{ number_format($odooStats['pkwt'] ?? 0) }}
                </div>
            </a>

            <!-- 7. Joined -->
            <a href="{{ route('kandidatportal.index', array_merge(request()->except('page'), ['odoo_stage' => ($odooStage === 'Joined' ? '' : 'Joined')])) }}" 
               class="p-2 sm:p-2.5 rounded-xl border transition-all duration-150 flex flex-col justify-between {{ $odooStage === 'Joined' ? 'bg-emerald-50/80 border-emerald-400 ring-2 ring-emerald-400/50 shadow-xs' : 'bg-slate-50/70 hover:bg-white border-slate-200 hover:border-emerald-300 hover:shadow-xs' }}">
                <div class="flex items-center justify-between gap-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider {{ $odooStage === 'Joined' ? 'text-emerald-700' : 'text-slate-500' }}">Joined</span>
                    <span class="w-5 h-5 rounded-md bg-emerald-100 text-emerald-700 flex items-center justify-center text-[10px]">
                        <i class="fa-solid fa-circle-check"></i>
                    </span>
                </div>
                <div class="text-base sm:text-lg font-black {{ $odooStage === 'Joined' ? 'text-emerald-800' : 'text-slate-900' }} mt-1">
                    {{ number_format($odooStats['joined'] ?? 0) }}
                </div>
            </a>

            <!-- 8. Belum di Odoo -->
            <a href="{{ route('kandidatportal.index', array_merge(request()->except('page'), ['odoo_stage' => ($odooStage === 'none' ? '' : 'none')])) }}" 
               class="p-2 sm:p-2.5 rounded-xl border transition-all duration-150 flex flex-col justify-between {{ $odooStage === 'none' ? 'bg-slate-200 border-slate-400 ring-2 ring-slate-400/50 shadow-xs' : 'bg-slate-50/70 hover:bg-white border-slate-200 hover:border-slate-300 hover:shadow-xs' }}">
                <div class="flex items-center justify-between gap-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider {{ $odooStage === 'none' ? 'text-slate-800' : 'text-slate-500' }}">Belum di Odoo</span>
                    <span class="w-5 h-5 rounded-md bg-slate-200 text-slate-600 flex items-center justify-center text-[10px]">
                        <i class="fa-regular fa-clock"></i>
                    </span>
                </div>
                <div class="text-base sm:text-lg font-black {{ $odooStage === 'none' ? 'text-slate-900' : 'text-slate-600' }} mt-1">
                    {{ number_format($odooStats['belum_odoo'] ?? 0) }}
                </div>
            </a>
        </div>
    </div>
    @endif

    <!-- MAIN TABLE CONTAINER CARD -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        
        <!-- Filter Header Bar -->
        <div class="p-4 sm:p-5 border-b border-slate-200 bg-slate-50/50 space-y-4">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                
                <!-- Left: Title & Active Filter Summary -->
                <div>
                    <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-list-check text-primary"></i>
                        <span>Data Pelamar Job Portal</span>
                        <span class="bg-blue-100 text-blue-800 text-[10px] font-extrabold px-2 py-0.5 rounded-full">{{ $candidates->total() }} Data Ditemukan</span>
                    </h2>
                    <p class="text-[11px] text-slate-500 mt-0.5">Pilih tab tahapan dan gunakan filter kategori AI untuk menyaring profil terbaik</p>
                </div>

                <!-- Right: Search & Date / Category Filter Form -->
                <form action="{{ route('kandidatportal.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
                    <input type="hidden" name="tab" value="{{ $tab }}">

                    @if((!empty($isAdmin) || !empty($canViewAllRecruiters)) && !empty($allRecruiters) && count($allRecruiters) > 0)
                    <!-- Filter Rekruter (Administrator & User All Scope) -->
                    <select name="recruiter" onchange="this.form.submit()" class="px-2.5 py-1.5 rounded-xl border border-blue-200 text-xs font-bold text-blue-900 bg-blue-50/80 focus:ring-2 focus:ring-primary outline-none">
                        <option value="all" {{ ($filterRecruiter === 'all' || empty($filterRecruiter)) ? 'selected' : '' }}>🌐 Semua Rekruter (Nasional)</option>
                        <option value="my" {{ $filterRecruiter === 'my' ? 'selected' : '' }}>👤 Data Saya ({{ auth()->user()->name ?? 'User' }})</option>
                        <optgroup label="Pilih Rekruter Spesifik:">
                            @foreach($allRecruiters as $r)
                                <option value="{{ $r->useras }}" {{ $filterRecruiter === $r->useras ? 'selected' : '' }}>
                                    {{ $r->display_name }} ({{ $r->total }} pelamar)
                                </option>
                            @endforeach
                        </optgroup>
                    </select>
                    @endif

                    <!-- Kategori AI Filter -->
                    <select name="kategori" class="px-3 py-1.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 bg-white focus:ring-2 focus:ring-primary-500 outline-none">
                        <option value="">Semua Kategori AI</option>
                        <option value="Green" {{ $kategori === 'Green' ? 'selected' : '' }}>🟢 Green (Score &ge; 85%)</option>
                        <option value="Yellow" {{ $kategori === 'Yellow' ? 'selected' : '' }}>🟡 Yellow (60% - 84%)</option>
                        <option value="Red" {{ $kategori === 'Red' ? 'selected' : '' }}>🔴 Red (&lt; 60%)</option>
                    </select>

                    <!-- Filter Step Odoo -->
                    <select name="odoo_stage" class="px-3 py-1.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 bg-white focus:ring-2 focus:ring-primary-500 outline-none">
                        <option value="">Semua Step Odoo</option>
                        <option value="matched" {{ ($odooStage ?? '') === 'matched' ? 'selected' : '' }}>⚡ Terdaftar di Odoo</option>
                        <option value="none" {{ ($odooStage ?? '') === 'none' ? 'selected' : '' }}>⚪ Belum di Odoo</option>
                        @if(!empty($distinctOdooStages) && count($distinctOdooStages) > 0)
                            <optgroup label="Tahapan Spesifik:">
                                @foreach($distinctOdooStages as $stg)
                                    <option value="{{ $stg }}" {{ ($odooStage ?? '') === $stg ? 'selected' : '' }}>{{ $stg }}</option>
                                @endforeach
                            </optgroup>
                        @endif
                    </select>

                    <!-- Tanggal Dari -->
                    <input type="date" 
                           name="start" 
                           value="{{ $start }}" 
                           placeholder="Dari Tanggal" 
                           class="px-2.5 py-1.5 rounded-xl border border-slate-300 text-xs font-medium text-slate-700 bg-white focus:ring-2 focus:ring-primary-500 outline-none">

                    <!-- Tanggal Sampai -->
                    <input type="date" 
                           name="end" 
                           value="{{ $end }}" 
                           placeholder="Sampai Tanggal" 
                           class="px-2.5 py-1.5 rounded-xl border border-slate-300 text-xs font-medium text-slate-700 bg-white focus:ring-2 focus:ring-primary-500 outline-none">

                    <!-- Search Box -->
                    <div class="relative w-44 sm:w-56">
                        <input type="text" 
                               name="q" 
                               value="{{ $search }}" 
                               placeholder="Cari nama, NIK, posisi, step..." 
                               class="w-full pl-8 pr-3 py-1.5 rounded-xl border border-slate-300 text-xs font-medium text-slate-700 focus:ring-2 focus:ring-primary-500 outline-none">
                        <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    </div>

                    <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-primary text-white text-xs font-bold hover:bg-primary-700 transition-all shadow-sm">
                        <i class="fa-solid fa-filter mr-1"></i> Filter
                    </button>

                    @if($kategori || !empty($odooStage) || $start || $end || $search || ($isAdmin && !empty($filterRecruiter)))
                    <a href="{{ route('kandidatportal.index', ['tab' => $tab]) }}" class="px-3 py-1.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-all">
                        Reset
                    </a>
                    @endif
                </form>

            </div>

            <!-- TABS NAVIGATION (Baru, Interview, Terima, Arsip) -->
            <div class="flex items-center gap-2 border-b border-slate-200/80 pb-1">
                <!-- 1. Baru / Semua -->
                <a href="{{ route('kandidatportal.index', array_merge(request()->except('tab', 'page'), ['tab' => 'baru'])) }}" 
                   class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ $tab === 'baru' ? 'bg-primary text-white shadow-md shadow-primary-500/20' : 'text-slate-600 hover:text-primary hover:bg-slate-100' }}">
                    <i class="fa-solid fa-inbox text-xs"></i>
                    <span>Baru / Semua</span>
                    <span class="px-1.5 py-0.5 rounded-md text-[10px] font-extrabold {{ $tab === 'baru' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700' }}">
                        {{ $countBaru }}
                    </span>
                </a>

                <!-- 2. Interview -->
                <a href="{{ route('kandidatportal.index', array_merge(request()->except('tab', 'page'), ['tab' => 'interview'])) }}" 
                   class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ $tab === 'interview' ? 'bg-primary text-white shadow-md shadow-primary-500/20' : 'text-slate-600 hover:text-primary hover:bg-slate-100' }}">
                    <i class="fa-solid fa-user-tie text-xs"></i>
                    <span>Interview</span>
                    <span class="px-1.5 py-0.5 rounded-md text-[10px] font-extrabold {{ $tab === 'interview' ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-800' }}">
                        {{ $countInterview }}
                    </span>
                </a>

                <!-- 3. Terima -->
                <a href="{{ route('kandidatportal.index', array_merge(request()->except('tab', 'page'), ['tab' => 'terima'])) }}" 
                   class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ $tab === 'terima' ? 'bg-primary text-white shadow-md shadow-primary-500/20' : 'text-slate-600 hover:text-primary hover:bg-slate-100' }}">
                    <i class="fa-solid fa-circle-check text-xs"></i>
                    <span>Terima</span>
                    <span class="px-1.5 py-0.5 rounded-md text-[10px] font-extrabold {{ $tab === 'terima' ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-800' }}">
                        {{ $countTerima }}
                    </span>
                </a>

                <!-- 4. Arsip -->
                <a href="{{ route('kandidatportal.index', array_merge(request()->except('tab', 'page'), ['tab' => 'arsip'])) }}" 
                   class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ $tab === 'arsip' ? 'bg-primary text-white shadow-md shadow-primary-500/20' : 'text-slate-600 hover:text-primary hover:bg-slate-100' }}">
                    <i class="fa-solid fa-box-archive text-xs"></i>
                    <span>Arsip</span>
                    <span class="px-1.5 py-0.5 rounded-md text-[10px] font-extrabold {{ $tab === 'arsip' ? 'bg-white/20 text-white' : 'bg-rose-100 text-rose-800' }}">
                        {{ $countArsip }}
                    </span>
                </a>
            </div>

        </div>

        <!-- TABLE CONTENT -->
        @if($candidates->isEmpty())
        <div class="p-12 text-center">
            <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3 text-2xl">
                <i class="fa-solid fa-user-slash"></i>
            </div>
            <h3 class="text-sm font-bold text-slate-800">Tidak Ada Data Pelamar</h3>
            <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">Tidak ada kandidat pelamar yang cocok dengan kriteria filter pada tab <strong>{{ strtoupper($tab) }}</strong>.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs custom-table">
                <thead>
                    <tr>
                        <th class="w-10 text-center">No</th>
                        <th>Tgl Daftar</th>
                        <th>Foto</th>
                        <th>No. KTP / NIK</th>
                        <th>Nama Kandidat</th>
                        <th>Jenis Kelamin</th>
                        <th>Tgl Lahir / Usia</th>
                        <th>Pendidikan</th>
                        <th>Posisi Dilamar</th>
                        <th>Area</th>
                        <th class="text-center">Step Odoo</th>
                        <th class="text-center">AI Match</th>
                        <th class="text-center">Kategori</th>
                        <th class="text-center">CV</th>
                        <th class="text-center w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($candidates as $index => $cand)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="text-center font-bold text-slate-400">{{ $candidates->firstItem() + $index }}</td>
                        
                        <!-- Tgl Daftar -->
                        <td>
                            <div class="font-semibold text-slate-800 text-[11px]">
                                {{ $cand->created_at ? $cand->created_at->timezone('Asia/Jakarta')->format('d M Y') : '-' }}
                            </div>
                            <div class="text-[10px] text-slate-400">
                                {{ $cand->created_at ? $cand->created_at->timezone('Asia/Jakarta')->format('H:i') : '' }} WIB
                            </div>
                        </td>

                        <!-- Foto Profil -->
                        <td class="text-center">
                            @if($cand->photo_path)
                                <img src="{{ $cand->photo_url }}" 
                                     alt="Avatar" class="w-8 h-8 rounded-full border border-slate-200 mx-auto object-cover" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($cand->full_name) }}&background=0F52BA&color=fff';">
                            @else
                                <div class="w-8 h-8 rounded-full bg-blue-50 border border-blue-200 text-primary flex items-center justify-center font-extrabold mx-auto text-xs">
                                    {{ strtoupper(substr($cand->full_name, 0, 1)) }}
                                </div>
                            @endif
                        </td>

                        <!-- No KTP -->
                        <td>
                            <code class="text-[11px] font-mono text-slate-700 bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200">
                                {{ $cand->nik }}
                            </code>
                        </td>

                        <!-- Nama Kandidat -->
                        <td>
                            <a href="{{ route('kandidatportal.show', $cand->id) }}" class="font-bold text-slate-900 hover:text-primary transition-colors text-xs flex items-center gap-1.5">
                                <span>{{ $cand->full_name }}</span>
                                <i class="fa-solid fa-arrow-up-right-from-square text-[9px] text-slate-300"></i>
                            </a>
                            <div class="text-[10px] text-slate-500 font-medium mt-0.5 flex items-center gap-1">
                                <i class="fa-brands fa-whatsapp text-emerald-500"></i>
                                <span>{{ $cand->phone ?? '-' }}</span>
                            </div>
                        </td>

                        <!-- Jenis Kelamin -->
                        <td>
                            @if(strtolower($cand->gender ?? '') === 'perempuan')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-pink-50 text-pink-700 border border-pink-200">
                                    <i class="fa-solid fa-venus text-[10px]"></i> Perempuan
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                    <i class="fa-solid fa-mars text-[10px]"></i> Laki-laki
                                </span>
                            @endif
                        </td>

                        <!-- Tgl Lahir / Usia -->
                        <td>
                            <div class="text-slate-700 font-medium">
                                {{ $cand->formatted_birth_date }}
                            </div>
                            <span class="inline-block text-[10px] font-bold text-slate-500 bg-slate-100 px-1.5 py-0.2 rounded">
                                {{ $cand->age }} Tahun
                            </span>
                        </td>

                        <!-- Pendidikan -->
                        <td>
                            <span class="text-slate-700 font-semibold">{{ $cand->education ?? '-' }}</span>
                        </td>

                        <!-- Posisi Dilamar -->
                        <td>
                            <div class="font-bold text-slate-900 text-xs leading-snug">
                                {{ $cand->applied_job ?? '-' }}
                            </div>
                        </td>

                        <!-- Area -->
                        <td>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                <i class="fa-solid fa-location-dot text-[9px]"></i>
                                {{ $cand->area ?? 'JAKARTA' }}
                            </span>
                        </td>

                        <!-- Step Odoo -->
                        <td class="text-center">
                            @php
                                $odooBadge = $cand->odoo_badge_info;
                            @endphp
                            @if($cand->odoo_stage_name)
                                <div class="inline-flex flex-col items-center gap-0.5">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] border {{ $odooBadge['class'] }}" title="Tahapan di Odoo ERP: {{ $cand->odoo_stage_name }} ({{ $cand->odoo_entity ?? 'Odoo' }})">
                                        <i class="{{ $odooBadge['icon'] }} text-[9px]"></i>
                                        <span>{{ $cand->odoo_stage_name }}</span>
                                    </span>
                                    @if($cand->odoo_entity)
                                        <span class="text-[9px] font-extrabold text-slate-400">
                                            {{ $cand->odoo_entity }}
                                        </span>
                                    @endif
                                </div>
                            @else
                                <span class="text-[10px] text-slate-400 italic">Belum di Odoo</span>
                            @endif
                        </td>

                        <!-- AI Score -->
                        <td class="text-center">
                            @php
                                $hasCv = $cand->hasCv();
                                $score = ($hasCv && $cand->ai_score !== null) ? intval($cand->ai_score) : 0;
                            @endphp
                            @if($hasCv && $score > 0)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-extrabold border {{ $cand->ai_badge_class }}">
                                    <i class="fa-solid fa-bolt text-[9px]"></i>
                                    {{ $score }}%
                                </span>
                            @else
                                <span class="text-[10px] text-slate-400 italic">-</span>
                            @endif
                        </td>

                        <!-- Kategori AI -->
                        <td class="text-center">
                            @if($hasCv && $cand->kategori_kandidat === 'Green')
                                <span class="badge-pill bg-emerald-50 text-emerald-700 border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Green
                                </span>
                            @elseif($hasCv && $cand->kategori_kandidat === 'Yellow')
                                <span class="badge-pill bg-amber-50 text-amber-700 border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Yellow
                                </span>
                            @elseif($hasCv && $cand->kategori_kandidat === 'Red')
                                <span class="badge-pill bg-rose-50 text-rose-700 border-rose-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Red
                                </span>
                            @else
                                <span class="badge-pill bg-slate-100 text-slate-600 border-slate-200">-</span>
                            @endif
                        </td>

                        <!-- File CV -->
                        <td class="text-center">
                            @if($cand->cv_path)
                                <a href="{{ $cand->cv_url }}" target="_blank" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 transition-colors shadow-sm" title="Buka File CV">
                                    <i class="fa-solid fa-file-pdf"></i> Ada
                                    <i class="fa-solid fa-arrow-up-right-from-square text-[8px]"></i>
                                </a>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-medium bg-slate-100 text-slate-400">
                                    Tidak Ada
                                </span>
                            @endif
                        </td>

                        <!-- Aksi Buttons -->
                        <td class="text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <!-- 1. Evaluasi / Detail -->
                                <a href="{{ route('kandidatportal.show', $cand->id) }}" 
                                   class="w-7 h-7 rounded-lg bg-primary-50 text-primary hover:bg-primary-100 border border-primary-200 flex items-center justify-center transition-all"
                                   title="Buka Lembar Evaluasi & Hasil Test">
                                    <i class="fa-solid fa-clipboard-check text-xs"></i>
                                </a>

                                <!-- 2. Reset Password Button -->
                                <button type="button" 
                                        @click="openResetPasswordModal('{{ $cand->id }}', '{{ $cand->full_name }}', '{{ $cand->birth_date ? $cand->birth_date->format('d-m-Y') : '' }}', '{{ $cand->birth_date ? $cand->birth_date->format('dmY') : '' }}')"
                                        class="w-7 h-7 rounded-lg bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200 flex items-center justify-center transition-all"
                                        title="Reset Password Kandidat ke Tanggal Lahir (ddmmyyyy)">
                                    <i class="fa-solid fa-key text-xs"></i>
                                </button>

                                <!-- 3. WhatsApp Direct Broadcast -->
                                <button type="button" 
                                        @click="sendWhatsAppMessage('{{ $cand->clean_whatsapp }}', '{{ $cand->full_name }}', '{{ $cand->applied_job }}', '{{ $cand->area }}')"
                                        class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 flex items-center justify-center transition-all"
                                        title="Kirim Undangan / Info via WhatsApp">
                                    <i class="fa-brands fa-whatsapp text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        <div class="p-4 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3 bg-slate-50/50">
            <div class="text-xs text-slate-500 font-medium">
                Menampilkan <b>{{ $candidates->firstItem() }}</b> - <b>{{ $candidates->lastItem() }}</b> dari <b>{{ $candidates->total() }}</b> pelamar
            </div>
            <div>
                {{ $candidates->links() }}
            </div>
        </div>
        @endif

    </div>

    <!-- MODAL RESET PASSWORD -->
    <div x-show="resetModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 border border-slate-200 space-y-4"
             @click.away="resetModalOpen = false">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center">
                        <i class="fa-solid fa-key"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-extrabold text-slate-900">Reset Password Kandidat</h4>
                        <p class="text-[11px] text-slate-500">Konfirmasi pengaturan ulang kata sandi login pelamar</p>
                    </div>
                </div>
                <button @click="resetModalOpen = false" class="text-slate-400 hover:text-slate-600 text-base p-1">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="space-y-3">
                <p class="text-xs text-slate-600 leading-relaxed">
                    Apakah Anda yakin ingin mereset kata sandi akun pelamar untuk kandidat:
                </p>
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs space-y-1">
                    <div class="font-bold text-slate-800" x-text="resetCandidateName"></div>
                    <div class="text-[11px] text-slate-500">
                        Tanggal Lahir: <span class="font-semibold text-slate-700" x-text="resetCandidateBirth"></span>
                    </div>
                </div>

                <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-amber-900 text-xs space-y-1">
                    <div class="font-bold flex items-center gap-1.5">
                        <i class="fa-solid fa-shield-halved text-amber-600"></i>
                        <span>Password Baru Otomatis:</span>
                    </div>
                    <p class="text-[11px] leading-relaxed">
                        Kata sandi baru akan otomatis diatur menjadi <code class="bg-amber-200/70 font-mono px-1.5 py-0.5 rounded font-bold" x-text="resetCandidatePlain"></code> (Format <b>ddmmyyyy</b> dari tanggal lahir kandidat) dan di-hash dengan aman di sistem.
                    </p>
                </div>
            </div>

            <form :action="'/kandidatportal/' + resetCandidateId + '/reset-password'" method="POST" class="pt-2 flex items-center justify-end gap-2">
                @csrf
                <button type="button" @click="resetModalOpen = false" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-all">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold transition-all shadow-md shadow-amber-600/20 flex items-center gap-1.5">
                    <i class="fa-solid fa-key"></i>
                    <span>Ya, Reset Password</span>
                </button>
            </form>
        </div>
    </div>

    <!-- MODAL EXPORT EXCEL -->
    <div x-show="openExportModal" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 border border-slate-200 space-y-4"
             @click.away="openExportModal = false">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center text-base">
                        <i class="fa-solid fa-file-excel"></i>
                    </div>
                    <h4 class="text-sm font-extrabold text-slate-800 tracking-tight">Export Data Kandidat Job Portal</h4>
                </div>
                <button type="button" @click="openExportModal = false" class="text-slate-400 hover:text-slate-600 text-lg p-1 transition-colors">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Modal Form -->
            <form action="{{ route('kandidatportal.export') }}" method="GET" class="space-y-3.5 text-xs">
                <input type="hidden" name="recruiter" value="{{ $filterRecruiter }}">
                <input type="hidden" name="q" value="{{ $search }}">

                <!-- 1. Dari Tanggal (Tanggal Daftar) -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Dari Tanggal (Tanggal Daftar)</label>
                    <input type="date" name="start" value="{{ $start }}" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-medium text-slate-700 bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all">
                    <p class="text-[11px] text-slate-400 mt-1">Kosongkan untuk mengexport semua tanggal.</p>
                </div>

                <!-- 2. Sampai Tanggal (Tanggal Daftar) -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Sampai Tanggal (Tanggal Daftar)</label>
                    <input type="date" name="end" value="{{ $end }}" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-medium text-slate-700 bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all">
                </div>

                <!-- 3. Kategori AI -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kategori AI</label>
                    <select name="kategori" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-medium text-slate-700 bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all cursor-pointer">
                        <option value="">Semua Kategori</option>
                        <option value="Green" {{ $kategori === 'Green' ? 'selected' : '' }}>Green (Sangat Direkomendasikan)</option>
                        <option value="Yellow" {{ $kategori === 'Yellow' ? 'selected' : '' }}>Yellow (Pertimbangan)</option>
                        <option value="Red" {{ $kategori === 'Red' ? 'selected' : '' }}>Red (Kurang Sesuai)</option>
                        <option value="pending" {{ $kategori === 'pending' ? 'selected' : '' }}>Belum Dianalisa AI</option>
                    </select>
                </div>

                <!-- 4. Status Kandidat -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Status Kandidat</label>
                    <select name="status_kandidat" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-medium text-slate-700 bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all cursor-pointer">
                        <option value="" {{ empty(request('status_kandidat')) ? 'selected' : '' }}>Semua Status</option>
                        <option value="Baru" {{ request('status_kandidat') === 'Baru' ? 'selected' : '' }}>Baru</option>
                        <option value="Interview" {{ request('status_kandidat') === 'Interview' ? 'selected' : '' }}>Interview</option>
                        <option value="Terima" {{ request('status_kandidat') === 'Terima' ? 'selected' : '' }}>Terima</option>
                        <option value="Arsip" {{ request('status_kandidat') === 'Arsip' ? 'selected' : '' }}>Arsip</option>
                    </select>
                </div>

                <!-- 5. Area Penempatan (Filter by Area Baru) -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Area Penempatan</label>
                    <select name="area" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-medium text-slate-700 bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all cursor-pointer">
                        <option value="">Semua Area</option>
                        @if(!empty($distinctAreas))
                            @foreach($distinctAreas as $areaOpt)
                                <option value="{{ $areaOpt }}">{{ $areaOpt }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <!-- Footer Buttons -->
                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                    <button type="button" @click="openExportModal = false" class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-all cursor-pointer">
                        Tutup
                    </button>
                    <button type="submit" @click="setTimeout(() => openExportModal = false, 500)" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-all shadow-md shadow-emerald-600/20 flex items-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-download"></i>
                        <span>Download Excel</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL SYNC STATUS ODOO -->
    <div x-show="openSyncOdooModal" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 border border-slate-200 space-y-4"
             @click.away="!isSyncingOdoo && (openSyncOdooModal = false)">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-arrows-rotate"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-extrabold text-slate-900">Sinkronisasi Rekrutmen Odoo ERP</h4>
                        <p class="text-[11px] text-slate-500">Pencocokan NIK & Pembaruan Tahapan Seleksi Otomatis</p>
                    </div>
                </div>
                <button type="button" :disabled="isSyncingOdoo" @click="openSyncOdooModal = false" class="text-slate-400 hover:text-slate-600 text-base p-1">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Description / Rules -->
            <div class="space-y-2.5 text-xs text-slate-600">
                <div class="p-3 bg-purple-50/70 border border-purple-200 rounded-xl space-y-1.5 text-purple-900">
                    <div class="font-bold flex items-center gap-1.5">
                        <i class="fa-solid fa-circle-info text-purple-600"></i>
                        <span>Aturan Sinkronisasi Otomatis:</span>
                    </div>
                    <ul class="list-disc pl-4 space-y-1 text-[11px] leading-relaxed text-purple-800">
                        <li><b>First/Second Interview & Tahap Seleksi</b> &rarr; Kandidat dialihkan ke tab <b>Interview</b>.</li>
                        <li><b>Joined</b> &rarr; Kandidat dialihkan ke tab <b>Terima</b>.</li>
                        <li><b>Kandidat > 14 Hari Tanpa Update</b> (masih di step Baru) &rarr; Otomatis dialihkan ke tab <b>Arsip</b>.</li>
                        <li>Pencocokan dilakukan otomatis lintas 5 entitas aktif (AMK, AKP, ATK, ABO, ATB).</li>
                    </ul>
                </div>

                <!-- Result Card (shown when finished) -->
                <div x-show="syncResult" x-cloak class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl space-y-2 text-emerald-900">
                    <div class="font-bold flex items-center gap-1.5 text-xs">
                        <i class="fa-solid fa-circle-check text-emerald-600"></i>
                        <span>Hasil Sinkronisasi Terkini:</span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-[11px]">
                        <div class="bg-white p-2 rounded-lg border border-emerald-100">
                            <span class="text-slate-500 block">Total Diperiksa</span>
                            <b class="text-slate-800 text-sm" x-text="syncResult ? syncResult.total_checked : 0"></b>
                        </div>
                        <div class="bg-white p-2 rounded-lg border border-emerald-100">
                            <span class="text-slate-500 block">Cocok di Odoo</span>
                            <b class="text-purple-700 text-sm" x-text="syncResult ? syncResult.matched : 0"></b>
                        </div>
                        <div class="bg-white p-2 rounded-lg border border-emerald-100">
                            <span class="text-slate-500 block">Pindah ke Interview</span>
                            <b class="text-indigo-600 text-sm" x-text="syncResult ? syncResult.moved_interview : 0"></b>
                        </div>
                        <div class="bg-white p-2 rounded-lg border border-emerald-100">
                            <span class="text-slate-500 block">Pindah ke Terima</span>
                            <b class="text-emerald-600 text-sm" x-text="syncResult ? syncResult.moved_terima : 0"></b>
                        </div>
                        <div class="col-span-2 bg-white p-2 rounded-lg border border-emerald-100 flex items-center justify-between">
                            <span class="text-slate-500">Auto-Arsip (> 14 Hari):</span>
                            <b class="text-rose-600 text-sm" x-text="syncResult ? syncResult.auto_archived : 0"></b>
                        </div>
                    </div>
                </div>

                <!-- Loading State -->
                <div x-show="isSyncingOdoo" class="py-6 text-center space-y-2">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-purple-500 border-t-transparent"></div>
                    <div class="text-xs font-bold text-slate-700">Sedang mencocokkan NIK ke Odoo ERP...</div>
                    <div class="text-[11px] text-slate-400">Harap tunggu sebentar, sistem sedang memeriksa ribuan data pelamar.</div>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" :disabled="isSyncingOdoo" @click="openSyncOdooModal = false" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-all">
                    Tutup
                </button>
                <template x-if="!syncResult">
                    <button type="button" :disabled="isSyncingOdoo" @click="runOdooSync()" class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold transition-all shadow-md shadow-purple-600/20 flex items-center gap-1.5 cursor-pointer disabled:opacity-50">
                        <i class="fa-solid fa-play"></i>
                        <span>Mulai Sinkronisasi Sekarang</span>
                    </button>
                </template>
                <template x-if="syncResult">
                    <button type="button" @click="window.location.reload()" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-all shadow-md shadow-emerald-600/20 flex items-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-rotate-right"></i>
                        <span>Muat Ulang Halaman</span>
                    </button>
                </template>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    function kandidatPortalManager() {
        return {
            resetModalOpen: false,
            openExportModal: false,
            openSyncOdooModal: false,
            isSyncingOdoo: false,
            syncResult: null,

            resetCandidateId: '',
            resetCandidateName: '',
            resetCandidateBirth: '',
            resetCandidatePlain: '',

            openResetPasswordModal(id, name, birthDateFmt, plainPwd) {
                this.resetCandidateId = id;
                this.resetCandidateName = name;
                this.resetCandidateBirth = birthDateFmt || 'Belum diisi';
                this.resetCandidatePlain = plainPwd || 'ddmmyyyy';
                this.resetModalOpen = true;
            },

            async runOdooSync() {
                this.isSyncingOdoo = true;
                this.syncResult = null;
                try {
                    const res = await fetch('{{ route('kandidatportal.sync_odoo') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ limit: 1500 })
                    });
                    const data = await res.json();
                    this.syncResult = data;
                } catch (e) {
                    alert('Gagal menjalankan sinkronisasi Odoo: ' + e.message);
                } finally {
                    this.isSyncingOdoo = false;
                }
            },

            sendWhatsAppMessage(phone, name, job, area) {
                if (!phone) {
                    alert('Nomor WhatsApp kandidat tidak tersedia.');
                    return;
                }
                const msg = `Halo Sdr/i *${name}*,\n\nTerima kasih telah melamar posisi *${job || 'Pekerjaan'}* penempatan *${area || 'Cabang'}* melalui Job Portal PT Arina Multi Karya.\n\nKami ingin mengonfirmasi kelengkapan data berkas Anda untuk tahapan seleksi selanjutnya.`;
                const url = `https://api.whatsapp.com/send?phone=${phone}&text=${encodeURIComponent(msg)}`;
                window.open(url, '_blank');
            }
        };
    }
</script>
@endsection