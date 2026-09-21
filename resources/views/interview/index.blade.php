@extends('layouts.app')

@section('title', 'Kandidat Interview - Attendance Portal')

@section('content')
<div class="space-y-6">

    <!-- 1. PAGE HEADER CARD (Attendance Style) -->
    <div class="page-header-card flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-13 h-13 rounded-2xl bg-gradient-to-br from-primary-700 via-primary-600 to-blue-500 text-white flex items-center justify-center text-2xl shadow-lg shadow-primary-500/25 flex-shrink-0">
                <i class="fa-solid fa-clipboard-user"></i>
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">Kandidat Interview</h1>
                    <span class="badge-pill bg-blue-50 text-primary border-blue-200">
                        <i class="fa-solid fa-building-shield text-[10px]"></i> ESA Groups
                    </span>
                    <span class="badge-pill bg-emerald-50 text-emerald-700 border-emerald-200">
                        <i class="fa-solid fa-location-dot text-[10px]"></i> Area: {{ strtoupper($displayRecruiterArea ?? ($user->area ?? 'JAKARTA')) }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    Pipeline seleksi calon pegawai, tes psikotes DISC online, tes matematika, tes komputer, dan pengiriman undangan wawancara kerja.
                </p>
            </div>
        </div>

        <!-- 4 Top Action Buttons -->
        <div class="flex items-center flex-wrap gap-2.5">
            <!-- 1. Tutorial Button -->
            <button onclick="openTutorialModal()" type="button" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 text-xs font-bold transition-all shadow-sm">
                <i class="fa-solid fa-circle-play text-amber-500"></i>
                <span>Tutorial</span>
            </button>

            <!-- 2. Walk Interview Button -->
            <a href="{{ route('interview.walk') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-800 border border-rose-200 text-xs font-bold transition-all shadow-sm">
                <i class="fa-solid fa-person-walking text-rose-500"></i>
                <span>Walk Interview</span>
            </a>

            <!-- 3. Import Data Button -->
            <button onclick="openImportCandidateModal()" type="button" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-blue-50 hover:bg-blue-100 text-primary border border-blue-200 text-xs font-bold transition-all shadow-sm cursor-pointer" title="Import data kandidat dari file Excel format Odoo hr.applicant">
                <i class="fa-solid fa-cloud-arrow-up text-primary"></i>
                <span>Import Data</span>
            </button>

            <!-- 3b. Tarik dari Odoo (NIK) Button -->
            <button onclick="openOdooNikModal()" type="button" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-800 border border-indigo-200 text-xs font-bold transition-all shadow-sm cursor-pointer" title="Tarik data kandidat dari Odoo ERP secara instan berdasarkan NIK">
                <i class="fa-solid fa-id-card-clip text-indigo-600"></i>
                <span>Tarik dari Odoo (NIK)</span>
            </button>

            <!-- 4. Export Data Button -->
            <a href="{{ route('interview.export') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 text-xs font-bold transition-all shadow-sm">
                <i class="fa-solid fa-file-excel text-emerald-600"></i>
                <span>Export Data</span>
            </a>

            <!-- 5. Sync Step Odoo Button -->
            <button onclick="openSyncOdooModal()" type="button" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-800 border border-purple-200 text-xs font-bold transition-all shadow-sm cursor-pointer" title="Sinkronisasi Status Tahapan Seleksi Kandidat dengan Odoo ERP">
                <i class="fa-solid fa-arrows-rotate text-purple-600"></i>
                <span>Sync Step Odoo</span>
            </button>
        </div>
    </div>

    <!-- 2. STATS METRIC ROW (Clean Responsive 4-Column Grid) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Stat 1: Total Kandidat -->
        <div class="stat-box">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ ($filterUser === 'all') ? 'Total Kandidat Nasional' : 'Kandidat Milik Anda' }}</div>
                    <div class="text-2xl font-black text-slate-900 mt-1">{{ number_format($statTotal ?? $myCandidates->total()) }}</div>
                    <div class="text-[11px] text-primary font-semibold mt-0.5 flex items-center gap-1">
                        <i class="fa-solid fa-user-check"></i>
                        <span>Rekrutor: {{ $displayRecruiterName ?? $user->name }}</span>
                    </div>
                </div>
                <div class="stat-box-icon bg-blue-50 text-primary">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
        </div>

        <!-- Stat 2: Profil Lengkap -->
        <div class="stat-box">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Profil Lengkap</div>
                    <div class="text-2xl font-black text-emerald-600 mt-1">{{ number_format($statProfileComplete ?? 0) }}</div>
                    <div class="text-[11px] text-emerald-600 font-semibold mt-0.5 flex items-center gap-1">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Data & Berkas Terisi</span>
                    </div>
                </div>
                <div class="stat-box-icon bg-emerald-50 text-emerald-600">
                    <i class="fa-solid fa-id-card"></i>
                </div>
            </div>
        </div>

        <!-- Stat 3: Selesai Tes Online -->
        <div class="stat-box">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Selesai Tes Online</div>
                    <div class="text-2xl font-black text-amber-600 mt-1">{{ number_format($statTestDone ?? 0) }}</div>
                    <div class="text-[11px] text-amber-600 font-semibold mt-0.5 flex items-center gap-1">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                        <span>Psikotes / Math</span>
                    </div>
                </div>
                <div class="stat-box-icon bg-amber-50 text-amber-600">
                    <i class="fa-solid fa-square-poll-vertical"></i>
                </div>
            </div>
        </div>

        <!-- Stat 4: Area Rekruter -->
        <div class="stat-box">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Wilayah Operasional</div>
                    <div class="text-2xl font-black text-slate-900 mt-1">{{ strtoupper($displayRecruiterArea ?? ($user->area ?? 'JAKARTA')) }}</div>
                    <div class="text-[11px] text-slate-500 font-semibold mt-0.5 flex items-center gap-1">
                        <i class="fa-solid fa-map-pin text-rose-500"></i>
                        <span>Penempatan Kerja</span>
                    </div>
                </div>
                <div class="stat-box-icon bg-slate-100 text-slate-700">
                    <i class="fa-solid fa-earth-asia"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- COMPACT STATS MINI-CARDS: STEP ODOO ERP -->
    @if(isset($odooStats))
    <div class="bg-white border border-slate-200 rounded-2xl p-3 sm:p-4 shadow-sm">
        <div class="flex items-center justify-between gap-2 mb-2.5">
            <div class="flex items-center gap-2">
                <div class="odoo-icon-purple w-6 h-6 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center text-xs font-bold">
                    <i class="fa-solid fa-arrows-split-up-and-left"></i>
                </div>
                <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">Tahapan Rekrutmen Odoo ERP</h3>
                <span class="text-[10px] font-semibold text-slate-400 hidden sm:inline">&bull; Klik card untuk filter cepat per tahapan kandidat interview</span>
            </div>
            @if(!empty($odooStage))
                <a href="{{ route('interview.index', request()->except('odoo_stage')) }}" class="odoo-reset-btn inline-flex items-center gap-1 text-[11px] font-bold text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 px-2.5 py-1 rounded-lg transition-colors shadow-xs">
                    <i class="fa-solid fa-xmark text-[10px]"></i>
                    <span>Reset Filter Step</span>
                </a>
            @endif
        </div>
        
        <!-- Grid 8 Mini Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-2">
            <!-- 1. Semua di Odoo -->
            <a href="{{ route('interview.index', array_merge(request()->except(['page_my', 'page_area']), ['odoo_stage' => ($odooStage === 'matched' ? '' : 'matched')])) }}" 
               class="odoo-stat-card p-2 sm:p-2.5 rounded-xl border transition-all duration-150 flex flex-col justify-between {{ $odooStage === 'matched' ? 'odoo-stat-card-active odoo-stat-matched bg-purple-50/80 border-purple-400 ring-2 ring-purple-400/50 shadow-xs' : 'bg-slate-50/70 hover:bg-white border-slate-200 hover:border-purple-300 hover:shadow-xs' }}">
                <div class="flex items-center justify-between gap-1">
                    <span class="odoo-card-label text-[10px] font-bold uppercase tracking-wider {{ $odooStage === 'matched' ? 'text-purple-700' : 'text-slate-500' }}">Semua Odoo</span>
                    <span class="odoo-card-icon odoo-icon-purple w-5 h-5 rounded-md bg-purple-100 text-purple-700 flex items-center justify-center text-[10px]">
                        <i class="fa-solid fa-bolt"></i>
                    </span>
                </div>
                <div class="odoo-card-value text-base sm:text-lg font-black {{ $odooStage === 'matched' ? 'text-purple-800' : 'text-slate-900' }} mt-1">
                    {{ number_format($odooStats['total_odoo'] ?? 0) }}
                </div>
            </a>

            <!-- 2. Data Pelamar -->
            <a href="{{ route('interview.index', array_merge(request()->except(['page_my', 'page_area']), ['odoo_stage' => ($odooStage === 'Data Pelamar' ? '' : 'Data Pelamar')])) }}" 
               class="odoo-stat-card p-2 sm:p-2.5 rounded-xl border transition-all duration-150 flex flex-col justify-between {{ $odooStage === 'Data Pelamar' ? 'odoo-stat-card-active odoo-stat-blue bg-blue-50/80 border-blue-400 ring-2 ring-blue-400/50 shadow-xs' : 'bg-slate-50/70 hover:bg-white border-slate-200 hover:border-blue-300 hover:shadow-xs' }}">
                <div class="flex items-center justify-between gap-1">
                    <span class="odoo-card-label text-[10px] font-bold uppercase tracking-wider {{ $odooStage === 'Data Pelamar' ? 'text-blue-700' : 'text-slate-500' }}">Data Pelamar</span>
                    <span class="odoo-card-icon odoo-icon-blue w-5 h-5 rounded-md bg-blue-100 text-blue-700 flex items-center justify-center text-[10px]">
                        <i class="fa-solid fa-file-lines"></i>
                    </span>
                </div>
                <div class="odoo-card-value text-base sm:text-lg font-black {{ $odooStage === 'Data Pelamar' ? 'text-blue-800' : 'text-slate-900' }} mt-1">
                    {{ number_format($odooStats['data_pelamar'] ?? 0) }}
                </div>
            </a>

            <!-- 3. Interview -->
            <a href="{{ route('interview.index', array_merge(request()->except(['page_my', 'page_area']), ['odoo_stage' => ($odooStage === 'interview' ? '' : 'interview')])) }}" 
               class="odoo-stat-card p-2 sm:p-2.5 rounded-xl border transition-all duration-150 flex flex-col justify-between {{ $odooStage === 'interview' ? 'odoo-stat-card-active odoo-stat-indigo bg-indigo-50/80 border-indigo-400 ring-2 ring-indigo-400/50 shadow-xs' : 'bg-slate-50/70 hover:bg-white border-slate-200 hover:border-indigo-300 hover:shadow-xs' }}">
                <div class="flex items-center justify-between gap-1">
                    <span class="odoo-card-label text-[10px] font-bold uppercase tracking-wider {{ $odooStage === 'interview' ? 'text-indigo-700' : 'text-slate-500' }}">Interview</span>
                    <span class="odoo-card-icon odoo-icon-indigo w-5 h-5 rounded-md bg-indigo-100 text-indigo-700 flex items-center justify-center text-[10px]">
                        <i class="fa-solid fa-user-tie"></i>
                    </span>
                </div>
                <div class="odoo-card-value text-base sm:text-lg font-black {{ $odooStage === 'interview' ? 'text-indigo-800' : 'text-slate-900' }} mt-1">
                    {{ number_format($odooStats['interview'] ?? 0) }}
                </div>
            </a>

            <!-- 4. Principal -->
            <a href="{{ route('interview.index', array_merge(request()->except(['page_my', 'page_area']), ['odoo_stage' => ($odooStage === 'Principal' ? '' : 'Principal')])) }}" 
               class="odoo-stat-card p-2 sm:p-2.5 rounded-xl border transition-all duration-150 flex flex-col justify-between {{ $odooStage === 'Principal' ? 'odoo-stat-card-active odoo-stat-violet bg-violet-50/80 border-violet-400 ring-2 ring-violet-400/50 shadow-xs' : 'bg-slate-50/70 hover:bg-white border-slate-200 hover:border-violet-300 hover:shadow-xs' }}">
                <div class="flex items-center justify-between gap-1">
                    <span class="odoo-card-label text-[10px] font-bold uppercase tracking-wider {{ $odooStage === 'Principal' ? 'text-violet-700' : 'text-slate-500' }}">Principal</span>
                    <span class="odoo-card-icon odoo-icon-violet w-5 h-5 rounded-md bg-violet-100 text-violet-700 flex items-center justify-center text-[10px]">
                        <i class="fa-solid fa-user-shield"></i>
                    </span>
                </div>
                <div class="odoo-card-value text-base sm:text-lg font-black {{ $odooStage === 'Principal' ? 'text-violet-800' : 'text-slate-900' }} mt-1">
                    {{ number_format($odooStats['principal'] ?? 0) }}
                </div>
            </a>

            <!-- 5. E-Learning -->
            <a href="{{ route('interview.index', array_merge(request()->except(['page_my', 'page_area']), ['odoo_stage' => ($odooStage === 'elearning' ? '' : 'elearning')])) }}" 
               class="odoo-stat-card p-2 sm:p-2.5 rounded-xl border transition-all duration-150 flex flex-col justify-between {{ $odooStage === 'elearning' ? 'odoo-stat-card-active odoo-stat-sky bg-sky-50/80 border-sky-400 ring-2 ring-sky-400/50 shadow-xs' : 'bg-slate-50/70 hover:bg-white border-slate-200 hover:border-sky-300 hover:shadow-xs' }}">
                <div class="flex items-center justify-between gap-1">
                    <span class="odoo-card-label text-[10px] font-bold uppercase tracking-wider {{ $odooStage === 'elearning' ? 'text-sky-700' : 'text-slate-500' }}">E-Learning</span>
                    <span class="odoo-card-icon odoo-icon-sky w-5 h-5 rounded-md bg-sky-100 text-sky-700 flex items-center justify-center text-[10px]">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </span>
                </div>
                <div class="odoo-card-value text-base sm:text-lg font-black {{ $odooStage === 'elearning' ? 'text-sky-800' : 'text-slate-900' }} mt-1">
                    {{ number_format($odooStats['elearning'] ?? 0) }}
                </div>
            </a>

            <!-- 6. PKWT -->
            <a href="{{ route('interview.index', array_merge(request()->except(['page_my', 'page_area']), ['odoo_stage' => ($odooStage === 'pkwt' ? '' : 'pkwt')])) }}" 
               class="odoo-stat-card p-2 sm:p-2.5 rounded-xl border transition-all duration-150 flex flex-col justify-between {{ $odooStage === 'pkwt' ? 'odoo-stat-card-active odoo-stat-amber bg-amber-50/80 border-amber-400 ring-2 ring-amber-400/50 shadow-xs' : 'bg-slate-50/70 hover:bg-white border-slate-200 hover:border-amber-300 hover:shadow-xs' }}">
                <div class="flex items-center justify-between gap-1">
                    <span class="odoo-card-label text-[10px] font-bold uppercase tracking-wider {{ $odooStage === 'pkwt' ? 'text-amber-700' : 'text-slate-500' }}">PKWT</span>
                    <span class="odoo-card-icon odoo-icon-amber w-5 h-5 rounded-md bg-amber-100 text-amber-700 flex items-center justify-center text-[10px]">
                        <i class="fa-solid fa-file-signature"></i>
                    </span>
                </div>
                <div class="odoo-card-value text-base sm:text-lg font-black {{ $odooStage === 'pkwt' ? 'text-amber-800' : 'text-slate-900' }} mt-1">
                    {{ number_format($odooStats['pkwt'] ?? 0) }}
                </div>
            </a>

            <!-- 7. Joined -->
            <a href="{{ route('interview.index', array_merge(request()->except(['page_my', 'page_area']), ['odoo_stage' => ($odooStage === 'Joined' ? '' : 'Joined')])) }}" 
               class="odoo-stat-card p-2 sm:p-2.5 rounded-xl border transition-all duration-150 flex flex-col justify-between {{ $odooStage === 'Joined' ? 'odoo-stat-card-active odoo-stat-emerald bg-emerald-50/80 border-emerald-400 ring-2 ring-emerald-400/50 shadow-xs' : 'bg-slate-50/70 hover:bg-white border-slate-200 hover:border-emerald-300 hover:shadow-xs' }}">
                <div class="flex items-center justify-between gap-1">
                    <span class="odoo-card-label text-[10px] font-bold uppercase tracking-wider {{ $odooStage === 'Joined' ? 'text-emerald-700' : 'text-slate-500' }}">Joined</span>
                    <span class="odoo-card-icon odoo-icon-emerald w-5 h-5 rounded-md bg-emerald-100 text-emerald-700 flex items-center justify-center text-[10px]">
                        <i class="fa-solid fa-circle-check"></i>
                    </span>
                </div>
                <div class="odoo-card-value text-base sm:text-lg font-black {{ $odooStage === 'Joined' ? 'text-emerald-800' : 'text-slate-900' }} mt-1">
                    {{ number_format($odooStats['joined'] ?? 0) }}
                </div>
            </a>

            <!-- 8. Belum di Odoo -->
            <a href="{{ route('interview.index', array_merge(request()->except(['page_my', 'page_area']), ['odoo_stage' => ($odooStage === 'none' ? '' : 'none')])) }}" 
               class="odoo-stat-card p-2 sm:p-2.5 rounded-xl border transition-all duration-150 flex flex-col justify-between {{ $odooStage === 'none' ? 'odoo-stat-card-active odoo-stat-slate bg-slate-200 border-slate-400 ring-2 ring-slate-400/50 shadow-xs' : 'bg-slate-50/70 hover:bg-white border-slate-200 hover:border-slate-300 hover:shadow-xs' }}">
                <div class="flex items-center justify-between gap-1">
                    <span class="odoo-card-label text-[10px] font-bold uppercase tracking-wider {{ $odooStage === 'none' ? 'text-slate-800' : 'text-slate-500' }}">Belum di Odoo</span>
                    <span class="odoo-card-icon odoo-icon-slate w-5 h-5 rounded-md bg-slate-200 text-slate-600 flex items-center justify-center text-[10px]">
                        <i class="fa-regular fa-clock"></i>
                    </span>
                </div>
                <div class="odoo-card-value text-base sm:text-lg font-black {{ $odooStage === 'none' ? 'text-slate-900' : 'text-slate-600' }} mt-1">
                    {{ number_format($odooStats['belum_odoo'] ?? 0) }}
                </div>
            </a>
        </div>
    </div>
    @endif

    <!-- 3. TABLE 1: DATA KANDIDAT MILIK REKRUTOR -->
    <div class="table-card">
        <!-- Table Header & Search Bar (Clean Flex Layout - NO OVERLAPPING) -->
        <div class="px-6 py-4 border-b border-slate-200 flex flex-col lg:flex-row lg:items-center justify-between gap-3 bg-white">
            <div class="flex items-center gap-2.5 flex-wrap">
                <span class="w-2.5 h-2.5 rounded-full bg-primary animate-pulse"></span>
                <h2 class="text-sm font-bold text-slate-900">
                    @if($filterUser === 'all')
                        Data Kandidat &bull; Semua Rekruter (Nasional)
                    @elseif(!empty($filterUser) && $filterUser !== 'my')
                        Data Kandidat &bull; {{ $displayRecruiterName }} ({{ strtoupper($displayRecruiterTitle) }} - {{ strtoupper($displayRecruiterArea) }})
                    @else
                        Data Kandidat Milik Anda &bull; {{ $user->name }} ({{ strtoupper($displayRecruiterTitle ?? ($user->job_title ?? 'REKRUTMEN')) }} - {{ strtoupper($displayRecruiterArea ?? ($user->area ?? 'JAKARTA')) }})
                    @endif
                </h2>
                <span class="badge-pill bg-blue-50 text-primary border-blue-200">
                    <i class="fa-solid fa-user-check text-[10px] mr-1"></i> {{ number_format($myCandidates->total()) }} Kandidat Milik Anda
                </span>
            </div>

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full lg:w-auto">
                <!-- Dropdown Filter Rekruter (Untuk Admin & User All Scope) -->
                @if((!empty($isAdmin) || !empty($canViewAllRecruiters)) && isset($allRecruiters) && count($allRecruiters) > 0)
                <form method="GET" action="{{ route('interview.index') }}" class="flex items-center gap-1.5 flex-shrink-0">
                    @if(request('search_my'))
                        <input type="hidden" name="search_my" value="{{ request('search_my') }}">
                    @endif
                    @if(request('search_area'))
                        <input type="hidden" name="search_area" value="{{ request('search_area') }}">
                    @endif
                    <div class="flex items-center gap-1 bg-slate-50 border border-slate-200 rounded-xl px-2.5 py-1.5">
                        <i class="fa-solid fa-user-gear text-primary text-xs"></i>
                        <select name="filter_user" onchange="this.form.submit()" class="bg-transparent text-xs font-bold text-slate-700 focus:outline-none cursor-pointer">
                            <option value="all" {{ ($filterUser === 'all' || empty($filterUser)) ? 'selected' : '' }}>🌐 Semua Rekruter (Nasional)</option>
                            <option value="my" {{ $filterUser === 'my' ? 'selected' : '' }}>👤 Data Saya ({{ $user->name }})</option>
                            <optgroup label="Pilih Rekruter Tertentu:">
                                @foreach($allRecruiters as $rec)
                                    <option value="{{ $rec->useras }}" {{ $filterUser === $rec->useras ? 'selected' : '' }}>
                                        {{ $rec->display_name }} ({{ number_format($rec->total) }})
                                    </option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>
                </form>
                @endif

                <!-- Search Form -->
                <form method="GET" action="{{ route('interview.index') }}" class="flex items-center gap-2 flex-1 sm:flex-initial">
                    @if($filterUser)
                        <input type="hidden" name="filter_user" value="{{ $filterUser }}">
                    @endif
                    @if(request('search_area'))
                        <input type="hidden" name="search_area" value="{{ request('search_area') }}">
                    @endif
                    @if(request('page_area'))
                        <input type="hidden" name="page_area" value="{{ request('page_area') }}">
                    @endif

                    <!-- Filter Step Odoo -->
                    <div class="flex items-center gap-1 bg-slate-50 border border-slate-200 rounded-xl px-2.5 py-1.5">
                        <i class="fa-solid fa-arrows-split-up-and-left text-purple-600 text-xs"></i>
                        <select name="odoo_stage" onchange="this.form.submit()" class="bg-transparent text-xs font-bold text-slate-700 focus:outline-none cursor-pointer">
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
                    </div>

                    <div class="relative w-full sm:w-64">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" name="search_my" value="{{ request('search_my') }}" placeholder="Cari nama, NIK, jabatan..." 
                                class="w-full pl-9 pr-3 py-1.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary bg-slate-50">
                    </div>
                    <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-primary text-white text-xs font-bold hover:bg-primary-700 shadow-sm flex-shrink-0">
                        Cari
                    </button>
                    @if(request('search_my') || $filterUser || !empty($odooStage))
                        <a href="{{ route('interview.index') }}" class="px-2.5 py-1.5 rounded-xl bg-slate-100 text-slate-600 text-xs font-semibold hover:bg-slate-200">
                            Reset
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <!-- Responsive Custom Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left custom-table">
                <thead>
                    <tr>
                        <th class="w-12 text-center">NO</th>
                        <th class="w-36">NO. KTP</th>
                        <th>NAMA KANDIDAT</th>
                        <th>JENIS KELAMIN</th>
                        <th>TGL. LAHIR & USIA</th>
                        <th>PENDIDIKAN</th>
                        <th>PRINSIPLE & JABATAN</th>
                        <th class="text-center">STEP ODOO</th>
                        <th class="text-center">PSIKOTES</th>
                        <th class="text-center">MATH</th>
                        <th class="text-center">COMPUTER</th>
                        <th>USER</th>
                        <th class="text-center min-w-[130px]">ACTION</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($myCandidates as $index => $candidate)
                        @php
                            $psikotes = $candidate->psikotes_score;
                            $math = $candidate->math_score;
                            $computer = $candidate->computer_score;
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <!-- NO -->
                            <td class="text-center font-bold text-slate-400 text-xs">
                                {{ $myCandidates->firstItem() + $index }}
                            </td>

                            <!-- NIK -->
                            <td>
                                <span class="font-mono text-xs font-bold text-slate-700 tracking-tight">
                                    {{ $candidate->nik }}
                                </span>
                            </td>

                            <!-- NAMA KANDIDAT -->
                            <td>
                                <div class="flex items-center gap-2.5">
                                    @if($candidate->photo_path)
                                        <img src="{{ $candidate->photo_url }}" alt="{{ $candidate->full_name }}" class="w-8 h-8 rounded-lg object-cover flex-shrink-0 border border-slate-200" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($candidate->full_name) }}&background=0F52BA&color=fff';">
                                    @else
                                        <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 font-bold text-xs flex items-center justify-center flex-shrink-0 border border-slate-200">
                                            {{ strtoupper(substr($candidate->full_name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-bold text-xs text-slate-900 leading-tight">{{ $candidate->full_name }}</div>
                                        <div class="text-[10px] text-slate-400 mt-0.5">
                                            <span>WhatsApp: {{ $candidate->phone ?? '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- JENIS KELAMIN -->
                            <td>
                                @if(in_array(strtolower($candidate->gender ?? ''), ['perempuan', 'female']))
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-pink-50 text-pink-700 border border-pink-200">
                                        <i class="fa-solid fa-venus text-[10px]"></i> Perempuan
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                        <i class="fa-solid fa-mars text-[10px]"></i> Laki-laki
                                    </span>
                                @endif
                            </td>

                            <!-- TGL LAHIR & USIA -->
                            <td>
                                <div class="text-xs font-semibold text-slate-800">{{ $candidate->formatted_birth_date }}</div>
                                <div class="text-[11px] text-slate-500 font-medium">{{ $candidate->age }} Thn</div>
                            </td>

                            <!-- PENDIDIKAN -->
                            <td>
                                <div class="text-xs font-semibold text-slate-800">{{ $candidate->education ?? '-' }}</div>
                                @if($candidate->major)
                                    <div class="text-[10px] text-slate-400 truncate max-w-[120px]">{{ $candidate->major }}</div>
                                @endif
                            </td>

                            <!-- PRINSIPLE & JABATAN -->
                            <td>
                                <div class="font-bold text-xs text-slate-900">{{ strtoupper($candidate->principle->name ?? 'NON PRINSIPLE') }}</div>
                                <div class="text-[11px] text-primary font-semibold mt-0.5">{{ $candidate->applied_job ?? '-' }}</div>
                            </td>

                            <!-- STEP ODOO -->
                            <td class="text-center">
                                @php
                                    $odooBadge = $candidate->odoo_badge_info;
                                @endphp
                                @if($candidate->odoo_stage_name)
                                    <div class="inline-flex flex-col items-center gap-0.5">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] border {{ $odooBadge['class'] }}" title="Tahapan di Odoo ERP: {{ $candidate->odoo_stage_name }} ({{ $candidate->odoo_entity ?? 'Odoo' }})">
                                            <i class="{{ $odooBadge['icon'] }} text-[9px]"></i>
                                            <span>{{ $candidate->odoo_stage_name }}</span>
                                        </span>
                                        @if($candidate->odoo_entity)
                                            <span class="text-[9px] font-extrabold text-purple-600 bg-purple-50 px-1 rounded border border-purple-100">
                                                {{ $candidate->odoo_entity }}
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-[10px] text-slate-400 italic">Belum di Odoo</span>
                                @endif
                            </td>

                            <!-- PSIKOTES (DISC) -->
                            <td class="text-center">
                                @if($candidate->is_psikotes_done)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 text-xs font-bold shadow-sm" title="Sudah Tes Psikotes ({{ $candidate->tes_kepribadian ?? 'Selesai' }})">
                                        <i class="fa-solid fa-check"></i>
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-rose-100 text-rose-500 text-xs font-bold" title="Belum Selesai">
                                        <i class="fa-solid fa-xmark"></i>
                                    </span>
                                @endif
                            </td>

                            <!-- MATH -->
                            <td class="text-center">
                                @if($candidate->is_math_done)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 text-xs font-bold shadow-sm" title="Sudah Tes Matematika ({{ $math?->score ? 'Score: ' . $math->score : ($candidate->tes_matematika ?? 'Selesai') }})">
                                        <i class="fa-solid fa-check"></i>
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-rose-100 text-rose-500 text-xs font-bold" title="Belum Selesai">
                                        <i class="fa-solid fa-xmark"></i>
                                    </span>
                                @endif
                            </td>

                            <!-- COMPUTER -->
                            <td class="text-center">
                                @if($candidate->is_komputer_done)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 text-xs font-bold shadow-sm" title="Sudah Tes Komputer">
                                        <i class="fa-solid fa-check"></i>
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-rose-100 text-rose-500 text-xs font-bold" title="Belum Selesai">
                                        <i class="fa-solid fa-xmark"></i>
                                    </span>
                                @endif
                            </td>

                            <!-- USER / REKRUTOR -->
                            <td>
                                <div class="font-bold text-xs text-slate-800">{{ $candidate->user_name_formatted ?? $candidate->user_display_name }}</div>
                                <div class="text-[10px] text-slate-400 uppercase font-semibold">{{ $candidate->user_subtitle_formatted ?? ('ARO ' . ($candidate->area ?? 'JAKARTA')) }}</div>
                            </td>

                            <!-- ACTION TOOLS -->
                            <td class="text-center">
                                <div class="inline-flex items-center gap-1.5 justify-center">
                                    <!-- Detail / Hasil Interview -->
                                    <a href="{{ route('interview.show', $candidate->id) }}" 
                                       class="w-8 h-8 rounded-lg bg-blue-50 text-primary hover:bg-primary hover:text-white flex items-center justify-center text-sm transition-all shadow-sm" 
                                       title="Hasil Interview & Form Penilaian">
                                        <i class="fa-solid fa-clipboard-check"></i>
                                    </a>

                                    <!-- WhatsApp Automation Link -->
                                    <a href="{{ $candidate->wa_url }}" 
                                       target="_blank" 
                                       class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white flex items-center justify-center text-sm transition-all shadow-sm" 
                                       title="Kirim Pesan WhatsApp Undangan">
                                        <i class="fa-brands fa-whatsapp text-base"></i>
                                    </a>

                                    <!-- Edit Prinsiple Modal Trigger -->
                                    <button onclick="openEditPrincipleModal({{ $candidate->id }}, '{{ $candidate->full_name }}', '{{ $candidate->principle_id }}')" 
                                            class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 flex items-center justify-center text-xs transition-all shadow-sm" 
                                            title="Ubah Prinsiple">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13" class="text-center py-12">
                                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3 text-lg">
                                    <i class="fa-solid fa-inbox"></i>
                                </div>
                                <div class="text-sm font-bold text-slate-700">Tidak ada kandidat ditemukan</div>
                                <div class="text-xs text-slate-400 mt-1">Gunakan kata kunci pencarian lain atau import data baru.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Bar -->
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="text-xs text-slate-500">
                Menampilkan halaman <span class="font-bold text-slate-800">{{ $myCandidates->currentPage() }}</span> dari <span class="font-bold text-slate-800">{{ $myCandidates->lastPage() }}</span>
            </div>
            <div>
                {{ $myCandidates->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

    @if(isset($areaCandidates))
    <!-- 4. TABLE 2: DATA KANDIDAT REKAN SE-AREA -->
    <div class="table-card border-t-2 border-t-purple-500" id="table-rekan">
        <div class="px-6 py-4 border-b border-slate-200 flex flex-col lg:flex-row lg:items-center justify-between gap-3 bg-white">
            <div class="flex items-center gap-2.5 flex-wrap">
                <span class="w-2.5 h-2.5 rounded-full bg-purple-600 animate-pulse"></span>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-sm font-bold text-slate-900">
                            Data Kandidat Rekan Se-Area &bull; {{ strtoupper($targetArea ?? ($displayRecruiterArea ?? ($user->area ?? 'JAKARTA'))) }}
                        </h2>
                        <span class="badge-pill bg-purple-50 text-purple-700 border-purple-200">
                            <i class="fa-solid fa-users text-[10px] mr-1"></i> {{ number_format($areaCandidates->total()) }} Kandidat Rekan Area
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-0.5">
                        Menampilkan kandidat yang diproses oleh rekan kerja lain di wilayah operasional yang sama ({{ strtoupper($targetArea ?? ($displayRecruiterArea ?? ($user->area ?? 'JAKARTA'))) }}).
                    </p>
                </div>
            </div>

            <!-- Search Area Form -->
            <form method="GET" action="{{ route('interview.index') }}#table-rekan" class="flex items-center gap-2 w-full lg:w-auto">
                @if(request('search_my'))
                    <input type="hidden" name="search_my" value="{{ request('search_my') }}">
                @endif
                @if(request('filter_user'))
                    <input type="hidden" name="filter_user" value="{{ request('filter_user') }}">
                @endif
                @if(request('odoo_stage'))
                    <input type="hidden" name="odoo_stage" value="{{ request('odoo_stage') }}">
                @endif
                @if(request('page_my'))
                    <input type="hidden" name="page_my" value="{{ request('page_my') }}">
                @endif

                <div class="relative w-full sm:w-64">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" name="search_area" value="{{ request('search_area') }}" placeholder="Cari nama, NIK rekan area..." 
                           class="w-full pl-9 pr-3 py-1.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-600 bg-slate-50">
                </div>
                <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-purple-600 text-white text-xs font-bold hover:bg-purple-700 shadow-sm flex-shrink-0">
                    Cari
                </button>
                @if(request('search_area'))
                    <a href="{{ route('interview.index', request()->except('search_area')) }}#table-rekan" class="px-2.5 py-1.5 rounded-xl bg-slate-100 text-slate-600 text-xs font-semibold hover:bg-slate-200">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left custom-table">
                <thead>
                    <tr>
                        <th class="w-12 text-center">NO</th>
                        <th class="w-36">NO. KTP</th>
                        <th>NAMA KANDIDAT</th>
                        <th>JENIS KELAMIN</th>
                        <th>TGL. LAHIR & USIA</th>
                        <th>PENDIDIKAN</th>
                        <th>PRINSIPLE & JABATAN</th>
                        <th class="text-center">STEP ODOO</th>
                        <th class="text-center">PSIKOTES</th>
                        <th class="text-center">MATH</th>
                        <th class="text-center">COMPUTER</th>
                        <th>REKRUTOR</th>
                        <th class="text-center min-w-[130px]">ACTION</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($areaCandidates as $index => $candidate)
                        <tr class="hover:bg-purple-50/30 transition-colors">
                            <td class="text-center font-bold text-slate-400 text-xs">
                                {{ $areaCandidates->firstItem() + $index }}
                            </td>
                            <td>
                                <span class="font-mono text-xs font-bold text-slate-700">{{ $candidate->nik }}</span>
                            </td>
                            <td>
                                <div class="flex items-center gap-2.5">
                                    @if($candidate->photo_path)
                                        <img src="{{ $candidate->photo_url }}" alt="{{ $candidate->full_name }}" class="w-8 h-8 rounded-lg object-cover flex-shrink-0 border border-slate-200" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($candidate->full_name) }}&background=8B5CF6&color=fff';">
                                    @else
                                        <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-700 font-bold text-xs flex items-center justify-center flex-shrink-0 border border-purple-200">
                                            {{ strtoupper(substr($candidate->full_name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-bold text-xs text-slate-900 leading-tight">{{ $candidate->full_name }}</div>
                                        <div class="text-[10px] text-slate-400 mt-0.5">
                                            <span>WhatsApp: {{ $candidate->phone ?? '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if(in_array(strtolower($candidate->gender ?? ''), ['perempuan', 'female']))
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-pink-50 text-pink-700 border border-pink-200">
                                        <i class="fa-solid fa-venus text-[10px]"></i> Perempuan
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                        <i class="fa-solid fa-mars text-[10px]"></i> Laki-laki
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="text-xs font-semibold text-slate-800">{{ $candidate->formatted_birth_date }}</div>
                                <div class="text-[11px] text-slate-500 font-medium">{{ $candidate->age }} Thn</div>
                            </td>
                            <td>
                                <div class="text-xs font-semibold text-slate-800">{{ $candidate->education ?? '-' }}</div>
                                @if($candidate->major)
                                    <div class="text-[10px] text-slate-400 truncate max-w-[120px]">{{ $candidate->major }}</div>
                                @endif
                            </td>
                            <td>
                                <div class="font-bold text-xs text-slate-900">{{ strtoupper($candidate->principle->name ?? 'NON PRINSIPLE') }}</div>
                                <div class="text-[11px] text-primary font-semibold mt-0.5">{{ $candidate->applied_job ?? '-' }}</div>
                            </td>

                            <!-- STEP ODOO -->
                            <td class="text-center">
                                @php
                                    $areaOdooBadge = $candidate->odoo_badge_info;
                                @endphp
                                @if($candidate->odoo_stage_name)
                                    <div class="inline-flex flex-col items-center gap-0.5">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] border {{ $areaOdooBadge['class'] }}" title="Tahapan di Odoo ERP: {{ $candidate->odoo_stage_name }} ({{ $candidate->odoo_entity ?? 'Odoo' }})">
                                            <i class="{{ $areaOdooBadge['icon'] }} text-[9px]"></i>
                                            <span>{{ $candidate->odoo_stage_name }}</span>
                                        </span>
                                        @if($candidate->odoo_entity)
                                            <span class="text-[9px] font-extrabold text-purple-600 bg-purple-50 px-1 rounded border border-purple-100">
                                                {{ $candidate->odoo_entity }}
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-[10px] text-slate-400 italic">Belum di Odoo</span>
                                @endif
                            </td>
                            @php
                                $areaPsikotes = $candidate->psikotes_score;
                                $areaMath = $candidate->math_score;
                                $areaComputer = $candidate->computer_score;
                            @endphp
                            <td class="text-center">
                                @if($candidate->is_psikotes_done)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 text-xs font-bold shadow-sm" title="Sudah Tes Psikotes ({{ $candidate->tes_kepribadian ?? 'Selesai' }})"><i class="fa-solid fa-check"></i></span>
                                @else
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-rose-100 text-rose-500 text-xs font-bold" title="Belum Selesai"><i class="fa-solid fa-xmark"></i></span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($candidate->is_math_done)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 text-xs font-bold shadow-sm" title="Sudah Tes Matematika ({{ $areaMath?->score ? 'Score: ' . $areaMath->score : ($candidate->tes_matematika ?? 'Selesai') }})"><i class="fa-solid fa-check"></i></span>
                                @else
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-rose-100 text-rose-500 text-xs font-bold" title="Belum Selesai"><i class="fa-solid fa-xmark"></i></span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($candidate->is_komputer_done)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 text-xs font-bold shadow-sm" title="Sudah Tes Komputer"><i class="fa-solid fa-check"></i></span>
                                @else
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-rose-100 text-rose-500 text-xs font-bold" title="Belum Selesai"><i class="fa-solid fa-xmark"></i></span>
                                @endif
                            </td>
                            <td>
                                <div class="font-bold text-xs text-slate-800">{{ $candidate->user_name_formatted ?? $candidate->user_display_name }}</div>
                                <div class="text-[10px] text-slate-400 uppercase font-semibold">{{ $candidate->user_subtitle_formatted ?? ('ARO ' . ($candidate->area ?? 'JAKARTA')) }}</div>
                            </td>
                            <td class="text-center">
                                <div class="inline-flex items-center gap-1.5 justify-center">
                                    <a href="{{ route('interview.show', $candidate->id) }}" class="w-8 h-8 rounded-lg bg-blue-50 text-primary hover:bg-primary hover:text-white flex items-center justify-center text-sm transition-all shadow-sm" title="Hasil Interview & Form Penilaian">
                                        <i class="fa-solid fa-clipboard-check"></i>
                                    </a>
                                    <a href="{{ $candidate->wa_url }}" target="_blank" class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white flex items-center justify-center text-sm transition-all shadow-sm" title="Kirim Pesan WhatsApp">
                                        <i class="fa-brands fa-whatsapp text-base"></i>
                                    </a>
                                    <button onclick="openEditPrincipleModal({{ $candidate->id }}, '{{ $candidate->full_name }}', '{{ $candidate->principle_id }}')" 
                                            class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 flex items-center justify-center text-xs transition-all shadow-sm" 
                                            title="Ubah Prinsiple">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13" class="text-center py-10">
                                <div class="w-12 h-12 rounded-full bg-purple-50 text-purple-400 flex items-center justify-center mx-auto mb-2.5 text-lg">
                                    <i class="fa-solid fa-users-slash"></i>
                                </div>
                                <div class="text-sm font-bold text-slate-700">Belum ada kandidat rekan lain di area {{ strtoupper($targetArea ?? ($displayRecruiterArea ?? ($user->area ?? 'JAKARTA'))) }}</div>
                                <div class="text-xs text-slate-400 mt-1">Data kandidat milik rekan lain dalam satu area operasional akan otomatis muncul di sini.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Bar Table 2 -->
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="text-xs text-slate-500">
                Menampilkan halaman <span class="font-bold text-slate-800">{{ $areaCandidates->currentPage() }}</span> dari <span class="font-bold text-slate-800">{{ $areaCandidates->lastPage() }}</span> (Total <span class="font-bold text-slate-800">{{ number_format($areaCandidates->total()) }}</span> Kandidat Rekan)
            </div>
            <div>
                {{ $areaCandidates->appends(request()->query())->fragment('table-rekan')->links() }}
            </div>
        </div>
    </div>
    @endif

<!-- ========================================================================= -->
<!-- MODAL SYNC STEP ODOO ERP                                                  -->
<!-- ========================================================================= -->
<div id="syncOdooModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 transition-all duration-200" onclick="if(event.target === this) closeSyncOdooModal()">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 relative animate-scale-up" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between border-b border-slate-200 pb-3.5 mb-4">
            <h4 class="text-base font-extrabold text-slate-900 flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-arrows-rotate"></i>
                </div>
                <span>Sinkronisasi Tahapan Odoo ERP</span>
            </h4>
            <button onclick="closeSyncOdooModal()" type="button" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg hover:bg-slate-100 transition-all">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="{{ route('interview.sync_odoo') }}" method="POST" class="space-y-4">
            @csrf
            <div class="p-3.5 rounded-xl bg-purple-50/70 border border-purple-200/80 text-purple-900 text-xs leading-relaxed space-y-1.5">
                <div class="font-bold flex items-center gap-1.5 text-purple-950">
                    <i class="fa-solid fa-circle-info text-purple-600"></i>
                    <span>Informasi Pencocokan Tahapan</span>
                </div>
                <p>
                    Sistem akan mencocokkan nomor KTP/NIK kandidat interview dengan data pelamar di <b>Odoo ERP (hr.applicant)</b> pada entitas aktif (AMK, AKP, ATK, ABO, ATB).
                </p>
                <ul class="list-disc list-inside space-y-0.5 text-[11px] text-purple-800 pt-1">
                    <li>Kandidat tahap interview di Odoo akan otomatis diperbarui status dan stepnya.</li>
                    <li>Kandidat yang sudah <b>Joined</b> di Odoo akan disinkronkan ke tahap Terima.</li>
                </ul>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Maksimal Kandidat yang Diproses:</label>
                <select name="limit" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-purple-500 bg-white font-semibold text-slate-800">
                    <option value="500">500 Kandidat (Cepat - Rekomendasi)</option>
                    <option value="1000" selected>1.000 Kandidat (Standar)</option>
                    <option value="2000">2.000 Kandidat</option>
                    <option value="5000">5.000 Kandidat (Batch Besar)</option>
                </select>
                <p class="text-[10px] text-slate-400 mt-1">Kandidat diurutkan dari yang belum pernah dicek atau paling lama tidak disinkronkan.</p>
            </div>

            <div class="pt-3 border-t border-slate-200 flex items-center justify-end gap-2">
                <button type="button" onclick="closeSyncOdooModal()" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition-colors">
                    Batal
                </button>
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl text-xs font-bold text-white bg-purple-600 hover:bg-purple-700 shadow-md shadow-purple-600/20 transition-all">
                    <i class="fa-solid fa-arrows-rotate"></i>
                    <span>Mulai Sinkronisasi Sekarang</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL TUTORIAL (Attendance Portal Style)                                  -->
<!-- ========================================================================= -->
<div id="tutorialModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 transition-all duration-200" onclick="if(event.target === this) closeTutorialModal()">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 relative animate-scale-up" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between border-b border-slate-200 pb-3.5 mb-4">
            <h4 class="text-base font-extrabold text-slate-900 flex items-center gap-2.5">
                <i class="fa-solid fa-circle-play text-amber-500 text-lg"></i>
                <span>Materi & Tutorial Interview</span>
            </h4>
            <button onclick="closeTutorialModal()" type="button" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg hover:bg-slate-100 transition-all">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <div class="space-y-3">
            <a href="{{ url('/downloads/ONLINE_TEST.pptx') }}" target="_blank" class="flex items-center justify-between p-3.5 rounded-xl bg-amber-50/80 border border-amber-200/80 text-amber-900 font-bold text-xs hover:bg-amber-100 hover:border-amber-300 transition-all shadow-sm">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-file-powerpoint text-xl text-amber-600"></i>
                    <span>Download Materi PPT Online Test</span>
                </div>
                <i class="fa-solid fa-download text-amber-600"></i>
            </a>

            <a href="{{ url('/downloads/Approval_Kandidat_Inhouse.pptx') }}" target="_blank" class="flex items-center justify-between p-3.5 rounded-xl bg-amber-50/80 border border-amber-200/80 text-amber-900 font-bold text-xs hover:bg-amber-100 hover:border-amber-300 transition-all shadow-sm">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-file-powerpoint text-xl text-amber-600"></i>
                    <span>Materi Tutorial Kandidat Inhouse</span>
                </div>
                <i class="fa-solid fa-download text-amber-600"></i>
            </a>

            <a href="https://youtu.be/UFD9Lv265Ao" target="_blank" class="flex items-center justify-between p-3.5 rounded-xl bg-rose-50/80 border border-rose-200/80 text-rose-900 font-bold text-xs hover:bg-rose-100 hover:border-rose-300 transition-all shadow-sm">
                <div class="flex items-center gap-3">
                    <i class="fa-brands fa-youtube text-xl text-rose-600"></i>
                    <span>Video Tutorial Untuk Rekrutor / AS</span>
                </div>
                <i class="fa-solid fa-arrow-up-right-from-square text-xs text-rose-500"></i>
            </a>

            <a href="https://youtu.be/l3KW9-13z7c" target="_blank" class="flex items-center justify-between p-3.5 rounded-xl bg-rose-50/80 border border-rose-200/80 text-rose-900 font-bold text-xs hover:bg-rose-100 hover:border-rose-300 transition-all shadow-sm">
                <div class="flex items-center gap-3">
                    <i class="fa-brands fa-youtube text-xl text-rose-600"></i>
                    <span>Video Tutorial Untuk Kandidat</span>
                </div>
                <i class="fa-solid fa-arrow-up-right-from-square text-xs text-rose-500"></i>
            </a>
        </div>

        <div class="mt-5 pt-3.5 border-t border-slate-200 flex justify-end">
            <button onclick="closeTutorialModal()" type="button" class="px-5 py-2 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300 transition-all shadow-sm">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL EDIT PRINSIPLE                                                      -->
<!-- ========================================================================= -->
<div id="editPrincipleModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 transition-all duration-200">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 relative animate-scale-up">
        <div class="flex items-center justify-between border-b border-slate-200 pb-3.5 mb-4">
            <h4 class="text-base font-bold text-slate-900">Ubah Prinsiple Kandidat</h4>
            <button onclick="closeEditPrincipleModal()" class="text-slate-400 hover:text-slate-700">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form id="formEditPrinciple" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nama Kandidat</label>
                <input type="text" id="ep_candidate_name" readonly class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-slate-100 text-slate-600 font-semibold">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Pilih Prinsiple Baru</label>
                <select id="ep_principle_id" name="principle_id" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary bg-slate-50">
                    <option value="">Pilih Prinsiple</option>
                    @foreach($principles as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="pt-3 border-t border-slate-200 flex justify-end gap-2">
                <button type="button" onclick="closeEditPrincipleModal()" class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold bg-primary hover:bg-primary-700 text-white shadow-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================== -->
<!-- MODAL UPLOAD EXCEL KANDIDAT INTERVIEW -->
<!-- ============================================================== -->
<div id="modalImportCandidate" class="fixed inset-0 z-[999990] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden">
    <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-slate-200 overflow-hidden transform transition-all">
        <!-- Modal Header -->
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-blue-50/50 via-white to-indigo-50/30">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-100 text-primary flex items-center justify-center text-lg font-bold">
                    <i class="fa-solid fa-file-excel"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Import Kandidat Walkin</h3>
                    <p class="text-xs text-slate-500">Unggah file Excel format Odoo hr.applicant.xlsx</p>
                </div>
            </div>
            <button type="button" onclick="closeImportCandidateModal()" class="w-8 h-8 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600 flex items-center justify-center transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-5 space-y-4">
            <!-- Info Alert -->
            <div class="p-3.5 rounded-xl bg-blue-50/70 border border-blue-200 text-xs text-blue-900 space-y-1.5">
                <div class="flex items-center gap-2 font-bold text-blue-950">
                    <i class="fa-solid fa-circle-info text-primary"></i>
                    <span>Ketentuan Format Template</span>
                </div>
                <p class="text-[11px] leading-relaxed text-blue-800">
                    Template import mengacu pada file <span class="font-bold">hr.applicant.xlsx</span> (31 kolom). Jika NIK sudah terdaftar sebelumnya, data lama otomatis diarsipkan dan data baru dibuat dengan status aktif.
                </p>
            </div>

            <!-- Keterangan Template ASystem di Rekrutmen Odoo -->
            <div class="flex items-start gap-3 p-3.5 rounded-xl bg-amber-50/90 border border-amber-200/90 text-amber-950 shadow-xs">
                <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 text-sm mt-0.5">
                    <i class="fa-solid fa-file-lines"></i>
                </div>
                <div>
                    <div class="text-xs font-bold text-amber-900">Gunakan Template ASystem di Page Rekrutmen Odoo</div>
                    <p class="text-[11px] text-amber-800 mt-1 leading-relaxed">
                        Silakan export data pelamar langsung dari modul <b>Recruitment Odoo ERP</b> menggunakan tampilan / preset <b>"Template ASystem"</b>, kemudian unggah file <b>.xlsx</b> hasil export tersebut di bawah ini.
                    </p>
                </div>
            </div>

            <!-- Opsi Cepat: Tarik Langsung via NIK -->
            <div class="p-3 rounded-xl bg-indigo-50/80 border border-indigo-200 flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-bolt"></i>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-indigo-950">Ingin Lebih Praktis? Tarik via NIK</div>
                        <div class="text-[10px] text-indigo-600">Tarik kandidat perorangan instan tanpa perlu export-import Excel</div>
                    </div>
                </div>
                <button type="button" onclick="closeImportCandidateModal(); openOdooNikModal();" class="px-3 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition shadow-xs flex items-center gap-1.5">
                    <i class="fa-solid fa-id-card-clip"></i>
                    <span>Input NIK</span>
                </button>
            </div>

            <!-- File Upload Dropzone -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Pilih File Excel (.xlsx)</label>
                <div id="dropzoneImport" onclick="document.getElementById('fileExcelImport').click()" class="border-2 border-dashed border-slate-300 hover:border-primary/60 rounded-2xl p-6 text-center cursor-pointer bg-slate-50/50 hover:bg-blue-50/30 transition group">
                    <input type="file" id="fileExcelImport" accept=".xlsx" class="hidden" onchange="handleFileSelected(this)">
                    <div class="w-12 h-12 rounded-2xl bg-white shadow-sm border border-slate-200 flex items-center justify-center text-primary text-xl mx-auto mb-2.5 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-cloud-arrow-up text-primary"></i>
                    </div>
                    <div id="dropzoneText" class="space-y-1">
                        <div class="text-xs font-bold text-slate-800">Klik untuk memilih file atau drag & drop</div>
                        <div class="text-[11px] text-slate-400">Format yang didukung: <b>.xlsx</b> (Maksimal 30 MB)</div>
                    </div>
                    <div id="selectedFileInfo" class="hidden mt-2 p-2.5 rounded-xl bg-white border border-emerald-200 text-emerald-800 text-xs font-bold inline-flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-emerald-500"></i>
                        <span id="selectedFileName">file.xlsx</span>
                        <span id="selectedFileSize" class="text-[10px] font-normal text-slate-400"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="p-4 border-t border-slate-100 flex items-center justify-between bg-slate-50/50">
            <button type="button" onclick="closeImportCandidateModal()" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-200 rounded-xl transition">
                Batal
            </button>
            <button type="button" id="btnStartImport" onclick="submitImportFile()" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary hover:bg-primary-700 text-white text-xs font-bold shadow-md shadow-primary/20 transition hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="fa-solid fa-terminal"></i>
                <span>Mulai Import (Terminal Live)</span>
            </button>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- TERMINAL CONSOLE MODAL (LIVE STREAMING IMPORT ENGINE) -->
<!-- ============================================================== -->
<div id="terminalImportModal" class="fixed inset-0 z-[999995] flex items-center justify-center p-3 sm:p-5 bg-slate-950/85 backdrop-blur-md hidden transition-all duration-300">
    <div class="bg-[#0a0e14] border border-slate-700/80 rounded-2xl w-full max-w-4xl shadow-2xl overflow-hidden flex flex-col transition-all duration-300">
        <!-- Terminal Titlebar (macOS Style) -->
        <div class="px-4 py-3 bg-[#161b22] border-b border-slate-800 flex items-center justify-between shrink-0 select-none">
            <div class="flex items-center gap-2">
                <button type="button" onclick="closeTerminalImportModal()" class="w-3.5 h-3.5 rounded-full bg-rose-500 hover:bg-rose-600 transition shadow-xs flex items-center justify-center text-[9px] text-rose-950 font-black group" title="Tutup / Hentikan">
                    <span class="opacity-0 group-hover:opacity-100">✕</span>
                </button>
                <button type="button" onclick="clearTerminalImportLogs()" class="w-3.5 h-3.5 rounded-full bg-amber-400 hover:bg-amber-500 transition shadow-xs flex items-center justify-center text-[9px] text-amber-950 font-black group" title="Bersihkan Layar">
                    <span class="opacity-0 group-hover:opacity-100">−</span>
                </button>
                <button type="button" onclick="toggleTerminalImportFullscreen()" class="w-3.5 h-3.5 rounded-full bg-emerald-500 hover:bg-emerald-600 transition shadow-xs flex items-center justify-center text-[9px] text-emerald-950 font-black group" title="Layar Penuh">
                    <span class="opacity-0 group-hover:opacity-100">⤢</span>
                </button>
                <div class="h-4 w-[1px] bg-slate-700 mx-1.5"></div>
                <div class="flex items-center gap-2 text-xs font-mono text-slate-300 font-semibold">
                    <i class="fa-solid fa-terminal text-emerald-400"></i>
                    <span id="terminalImportTitle">ASystem HR Terminal - Import Kandidat Walkin</span>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <span id="terminalImportStatusBadge" class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span>
                    <span id="terminalImportStatusText">STREAMING ACTIVE</span>
                </span>
                <button type="button" onclick="copyTerminalImportLogs()" class="text-[11px] font-mono px-2 py-1 rounded bg-slate-800 text-slate-300 hover:text-white border border-slate-700 transition" title="Salin Seluruh Log">
                    <i class="fa-regular fa-copy"></i>
                </button>
                <button type="button" onclick="closeTerminalImportModal()" class="text-slate-400 hover:text-white transition p-1">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
        </div>

        <!-- Terminal Live Metrics Bar -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 p-3 bg-[#0d1117] border-b border-slate-800 text-slate-300 font-mono text-xs">
            <div class="bg-slate-900/80 border border-slate-800 rounded-lg p-2 flex items-center justify-between">
                <span class="text-slate-400 text-[11px]">Total Baris:</span>
                <span id="termMetricTotal" class="font-bold text-white text-sm">0</span>
            </div>
            <div class="bg-emerald-950/30 border border-emerald-800/40 rounded-lg p-2 flex items-center justify-between">
                <span class="text-emerald-400 text-[11px]">Sukses:</span>
                <span id="termMetricSuccess" class="font-bold text-emerald-300 text-sm">0</span>
            </div>
            <div class="bg-rose-950/30 border border-rose-800/40 rounded-lg p-2 flex items-center justify-between">
                <span class="text-rose-400 text-[11px]">Gagal / Skip:</span>
                <span id="termMetricFailed" class="font-bold text-rose-300 text-sm">0</span>
            </div>
            <div class="bg-amber-950/30 border border-amber-800/40 rounded-lg p-2 flex items-center justify-between">
                <span class="text-amber-400 text-[11px]">Pengalaman:</span>
                <span id="termMetricExp" class="font-bold text-amber-300 text-sm">0</span>
            </div>
        </div>

        <!-- Terminal Console Logs Body -->
        <div id="terminalImportConsoleBody" class="flex-1 min-h-[380px] max-h-[480px] overflow-y-auto p-4 font-mono text-[11px] sm:text-xs text-slate-200 bg-[#0a0e14] leading-relaxed select-text space-y-1">
            <div class="text-slate-500 pb-2 border-b border-slate-800/80 text-[10px] flex items-center justify-between">
                <span>[ASYSTEM IMPORT ENGINE v3.5 - ARCHIVE EXISTING & WALKIN ENROLLMENT]</span>
                <span id="termImportClock">00:00:00</span>
            </div>
            <div id="terminalImportLogList" class="space-y-0.5 pt-2">
                <!-- Log items rendered dynamically here -->
            </div>
            <!-- Blinking Terminal Prompt -->
            <div id="termImportPromptLine" class="flex items-center gap-1.5 text-emerald-400 pt-1 text-[11px]">
                <span class="text-sky-400">admin@asystem</span>:<span class="text-amber-400">~/interview-import</span>$
                <span class="inline-block w-2 h-3.5 bg-emerald-400 animate-pulse ml-0.5"></span>
            </div>
        </div>

        <!-- Terminal Footer Controls -->
        <div class="px-4 py-3 bg-[#161b22] border-t border-slate-800 flex items-center justify-between shrink-0">
            <div class="text-[11px] font-mono text-slate-400 flex items-center gap-2">
                <i class="fa-solid fa-circle-nodes text-emerald-400"></i>
                <span id="terminalImportFooterNote">Proses import sedang berjalan...</span>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" id="btnAbortImport" onclick="abortTerminalImport()" class="px-3 py-1.5 rounded-lg font-bold text-xs bg-rose-500/20 text-rose-300 border border-rose-500/30 hover:bg-rose-500/30 transition flex items-center gap-1.5">
                    <i class="fa-solid fa-stop text-[10px]"></i>
                    <span>Hentikan</span>
                </button>
                <button type="button" id="btnRefreshAfterImport" onclick="finishAndRefresh()" class="hidden px-4 py-1.5 rounded-lg font-bold text-xs bg-emerald-600 hover:bg-emerald-500 text-white shadow-md transition flex items-center gap-1.5">
                    <i class="fa-solid fa-rotate-right"></i>
                    <span>Selesai & Refresh Halaman</span>
                </button>
                <button type="button" onclick="closeTerminalImportModal()" class="btn-att-primary text-xs px-4 py-1.5">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- MODAL TARIK KANDIDAT DARI ODOO ERP VIA NIK -->
<!-- ============================================================== -->
<div id="modalOdooNik" class="fixed inset-0 z-[999990] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden" onclick="if(event.target === this) closeOdooNikModal()">
    <div class="bg-white rounded-3xl max-w-xl w-full shadow-2xl border border-slate-200 overflow-hidden transform transition-all animate-scale-up" onclick="event.stopPropagation()">
        <!-- Modal Header -->
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-indigo-50/80 via-purple-50/40 to-white">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl bg-indigo-600 text-white flex items-center justify-center text-xl shadow-md shadow-indigo-600/20 shrink-0">
                    <i class="fa-solid fa-id-card-clip"></i>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 tracking-tight">Tarik Kandidat dari Odoo ERP</h3>
                    <p class="text-xs text-slate-500">Input NIK untuk tarik profil otomatis & proses tes online tanpa export-import file</p>
                </div>
            </div>
            <button type="button" onclick="closeOdooNikModal()" class="w-8 h-8 rounded-xl hover:bg-slate-100 text-slate-400 hover:text-slate-600 flex items-center justify-center transition">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 space-y-5">
            <!-- Form Input NIK & Entitas -->
            <div class="space-y-3.5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center justify-between">
                        <span>Nomor Identitas (NIK KTP / No. KK)</span>
                        <span id="odooNikCounter" class="text-[11px] font-semibold text-slate-400 font-mono">0 / 16 Digit</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-address-card text-base"></i>
                        </div>
                        <input type="text" id="odooNikInput" maxlength="16" placeholder="Masukkan 16 digit NIK KTP atau No. KK pelamar..." 
                                oninput="handleNikInput(this)" 
                                onkeydown="if(event.key === 'Enter'){ event.preventDefault(); searchCandidateByNik(); }"
                                class="w-full pl-10 pr-24 py-3 rounded-2xl border-2 border-slate-200 focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100 text-base font-bold text-slate-800 tracking-wider font-mono outline-none transition placeholder:font-sans placeholder:font-normal placeholder:text-xs">
                        <button type="button" id="btnSearchOdoo" onclick="searchCandidateByNik()" 
                                class="absolute right-1.5 top-1.5 bottom-1.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-sm transition flex items-center gap-1.5 disabled:opacity-50 cursor-pointer">
                            <i class="fa-solid fa-magnifying-glass text-xs"></i>
                            <span>Cari</span>
                        </button>
                    </div>
                </div>

                <!-- Pilihan Entitas Odoo -->
                <div class="flex items-center gap-2">
                    <label class="text-[11px] font-bold text-slate-500 shrink-0">Entitas Odoo:</label>
                    <select id="odooNikEntity" class="flex-1 px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/60 focus:bg-white focus:ring-2 focus:ring-indigo-500 outline-none cursor-pointer">
                        <option value="all" selected>Semua Entitas Odoo ERP (AMK, AKP, ATK, ABO, ATB)</option>
                        <option value="AKP">AKP - PT Alva Karya Perkasa</option>
                        <option value="AMK">AMK - PT Arina Multi Karya</option>
                        <option value="ATK">ATK - PT Anugrah Terpercaya Kerja</option>
                        <option value="ABO">ABO - PT Abadi Berkat Odelia</option>
                        <option value="ATB">ATB - PT Anugrah Talenta Berkarya</option>
                    </select>
                </div>
            </div>

            <!-- STATE 1: LOADING SPINNER -->
            <div id="odooNikLoading" class="hidden p-6 rounded-2xl bg-indigo-50/50 border border-indigo-100 text-center space-y-3 animate-pulse">
                <div class="w-10 h-10 rounded-full border-3 border-indigo-600 border-t-transparent animate-spin mx-auto"></div>
                <div class="text-xs font-bold text-indigo-950">Memeriksa Database Rekrutmen Odoo ERP...</div>
                <p class="text-[11px] text-indigo-700">Mencari data pelamar berdasarkan No. KTP maupun No. KK pada model hr.applicant</p>
            </div>

            <!-- STATE 2: PREVIEW CARD (DITEMUKAN DI ODOO) -->
            <div id="odooNikPreview" class="hidden space-y-4">
                <div class="p-4 rounded-2xl bg-gradient-to-br from-indigo-50/80 via-white to-purple-50/50 border-2 border-indigo-200/80 shadow-sm space-y-3.5">
                    <div class="flex items-start justify-between gap-3 border-b border-indigo-100/80 pb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-700 font-extrabold flex items-center justify-center text-sm" id="previewInitial">
                                NY
                            </div>
                            <div>
                                <h4 class="text-sm font-extrabold text-slate-900 leading-tight" id="previewName">Nama Pelamar</h4>
                                <div class="text-[11px] font-mono text-slate-500 mt-0.5 flex flex-wrap items-center gap-1.5">
                                    <span id="previewNik">NIK: 1610065111980003</span>
                                    <span id="previewKk" class="text-slate-400"></span>
                                    <span id="previewFoundBadge" class="hidden text-[10px] font-sans font-bold px-2 py-0.5 rounded-md bg-amber-100 text-amber-800 border border-amber-300">Ditemukan via No. KK</span>
                                </div>
                            </div>
                        </div>
                        <span id="previewEntityBadge" class="px-2.5 py-1 rounded-lg text-xs font-black bg-indigo-600 text-white shadow-xs">
                            AKP
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-2.5 text-xs">
                        <div class="bg-white/80 p-2.5 rounded-xl border border-slate-200/70">
                            <span class="text-[10px] font-bold text-slate-400 block uppercase">Posisi / Job</span>
                            <span class="font-extrabold text-slate-800 line-clamp-1" id="previewJob">-</span>
                        </div>
                        <div class="bg-white/80 p-2.5 rounded-xl border border-slate-200/70">
                            <span class="text-[10px] font-bold text-slate-400 block uppercase">Prinsiple</span>
                            <span class="font-extrabold text-slate-800 line-clamp-1" id="previewPrinciple">-</span>
                        </div>
                        <div class="bg-white/80 p-2.5 rounded-xl border border-slate-200/70">
                            <span class="text-[10px] font-bold text-slate-400 block uppercase">Area / Penempatan</span>
                            <span class="font-extrabold text-slate-800" id="previewArea">-</span>
                        </div>
                        <div class="bg-white/80 p-2.5 rounded-xl border border-slate-200/70">
                            <span class="text-[10px] font-bold text-slate-400 block uppercase">Tahapan Odoo</span>
                            <span class="font-extrabold text-purple-700" id="previewStage">-</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-[11px] text-slate-600 px-1">
                        <span class="flex items-center gap-1.5">
                            <i class="fa-solid fa-phone text-indigo-500"></i>
                            <span id="previewPhone">-</span>
                        </span>
                        <span class="flex items-center gap-1.5">
                            <i class="fa-solid fa-calendar-day text-indigo-500"></i>
                            <span id="previewTtl">-</span>
                        </span>
                    </div>

                    <!-- Warning jika ada kandidat lama dengan NIK sama -->
                    <div id="previewArchiveWarning" class="hidden p-2.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-[11px] flex items-center gap-2">
                        <i class="fa-solid fa-circle-exclamation text-amber-500 text-xs shrink-0"></i>
                        <span>NIK ini sebelumnya sudah pernah terdaftar di ASystem. Data lama otomatis di-<strong>REPLACE</strong> dan seluruh data tes online di-<strong>RESET</strong> ke awal agar pelamar dapat memulai seleksi tes dari awal.</span>
                    </div>
                </div>

                <!-- Tombol Eksekusi Tarik & Simpan -->
                <div class="flex items-center justify-end gap-2.5 pt-1">
                    <button type="button" onclick="resetOdooNikModal()" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition">
                        Batal / Ganti NIK
                    </button>
                    <button type="button" id="btnSaveCandidate" onclick="saveCandidateFromOdoo()" 
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 transition hover:-translate-y-0.5 cursor-pointer">
                        <i class="fa-solid fa-cloud-arrow-down"></i>
                        <span>Tarik & Proses Kandidat ke ASystem</span>
                    </button>
                </div>
            </div>

            <!-- STATE 3: SUCCESS RESULT (SETELAH TERSIMPAN DI ASYSTEM) -->
            <div id="odooNikSuccess" class="hidden space-y-4">
                <div class="p-5 rounded-2xl bg-gradient-to-br from-emerald-50 via-white to-teal-50 border-2 border-emerald-300 shadow-sm space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-500 text-white flex items-center justify-center text-2xl shadow-md shadow-emerald-500/30 shrink-0">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                        <div>
                            <span class="text-[10px] font-black uppercase tracking-wider text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-md">BERHASIL DIPROSES</span>
                            <h4 class="text-base font-extrabold text-slate-900 mt-0.5" id="succCandidateName">Nama Pelamar</h4>
                            <p class="text-xs text-slate-500" id="succCandidateJob">Posisi & Prinsiple</p>
                        </div>
                    </div>

                    <!-- Kredensial Tes Online CBT -->
                    <div class="p-4 rounded-xl bg-white border border-emerald-200 shadow-xs space-y-2.5">
                        <div class="text-xs font-extrabold text-slate-800 flex items-center gap-2">
                            <i class="fa-solid fa-laptop-code text-emerald-600"></i>
                            <span>Akun Login Tes Online (CBT Peserta)</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs font-mono">
                            <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-200">
                                <span class="text-[10px] font-sans font-bold text-slate-400 block uppercase">Username (NIK)</span>
                                <span class="font-bold text-slate-900 select-all" id="succLoginNik">-</span>
                            </div>
                            <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-200">
                                <span class="text-[10px] font-sans font-bold text-slate-400 block uppercase">Password Default</span>
                                <span class="font-bold text-emerald-700 select-all" id="succLoginPass">-</span>
                            </div>
                        </div>
                        <div class="text-[11px] text-slate-500 flex items-center gap-1.5 pt-0.5">
                            <i class="fa-solid fa-link text-slate-400"></i>
                            <span>URL Tes: <b class="font-mono text-indigo-600 select-all" id="succLoginUrl">-</b></span>
                        </div>
                    </div>

                    <!-- Action Buttons for Success -->
                    <div class="flex flex-col sm:flex-row items-center gap-2.5 pt-1">
                        <a id="btnSendWaInvite" href="#" target="_blank" class="w-full sm:w-auto flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 transition hover:-translate-y-0.5">
                            <i class="fa-brands fa-whatsapp text-sm"></i>
                            <span>Kirim Undangan Tes via WA</span>
                        </a>
                        <a id="btnViewCandidateProfile" href="#" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition">
                            <i class="fa-solid fa-user text-xs"></i>
                            <span>Buka Profil</span>
                        </a>
                        <button type="button" onclick="resetOdooNikModal()" class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-bold transition cursor-pointer">
                            <i class="fa-solid fa-plus text-xs mr-1"></i> Tarik NIK Lain
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Footer (ketika state awal) -->
        <div id="odooNikFooter" class="p-4 border-t border-slate-100 flex items-center justify-between bg-slate-50/50">
            <span class="text-[11px] text-slate-500 flex items-center gap-1.5">
                <i class="fa-solid fa-shield-halved text-indigo-500"></i>
                <span>Terkoneksi langsung ke server Odoo XML-RPC</span>
            </span>
            <button type="button" onclick="closeOdooNikModal()" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-200 rounded-xl transition">
                Tutup
            </button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function openTutorialModal() {
        const m = document.getElementById('tutorialModal');
        if (m) {
            m.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
    }
    function closeTutorialModal() {
        const m = document.getElementById('tutorialModal');
        if (m) {
            m.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }

    function openEditPrincipleModal(candidateId, candidateName, principleId) {
        document.getElementById('formEditPrinciple').action = `/interview/${candidateId}/edit-principle`;
        document.getElementById('ep_candidate_name').value = candidateName;
        document.getElementById('ep_principle_id').value = principleId || '';
        document.getElementById('editPrincipleModal').classList.remove('hidden');
    }
    function closeEditPrincipleModal() {
        document.getElementById('editPrincipleModal').classList.add('hidden');
    }

    // ==============================================================
    // IMPORT KANDIDAT WALKIN WITH LIVE TERMINAL ENGINE
    // ==============================================================
    let currentEventSource = null;
    let importTotalCount = 0;
    let importSuccessCount = 0;
    let importFailedCount = 0;
    let importExpCount = 0;

    function openImportCandidateModal() {
        document.getElementById('modalImportCandidate').classList.remove('hidden');
    }

    function closeImportCandidateModal() {
        document.getElementById('modalImportCandidate').classList.add('hidden');
    }

    function handleFileSelected(input) {
        if (!input.files || input.files.length === 0) return;
        const file = input.files[0];
        document.getElementById('selectedFileName').textContent = file.name;
        document.getElementById('selectedFileSize').textContent = `(${(file.size / 1024).toFixed(1)} KB)`;
        document.getElementById('selectedFileInfo').classList.remove('hidden');
        document.getElementById('dropzoneText').classList.add('hidden');
    }

    // Drag & drop dropzone handlers
    const dropzone = document.getElementById('dropzoneImport');
    if (dropzone) {
        ['dragenter', 'dragover'].forEach(eventName => {
            dropzone.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.add('border-primary', 'bg-blue-50/50');
            }, false);
        });
        ['dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.remove('border-primary', 'bg-blue-50/50');
            }, false);
        });
        dropzone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files && files.length > 0) {
                const fileInput = document.getElementById('fileExcelImport');
                fileInput.files = files;
                handleFileSelected(fileInput);
            }
        });
    }

    async function submitImportFile() {
        const fileInput = document.getElementById('fileExcelImport');
        if (!fileInput.files || fileInput.files.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Pilih File Terlebih Dahulu',
                text: 'Silakan pilih file Excel (.xlsx) sebelum memulai proses import.',
            });
            return;
        }

        const file = fileInput.files[0];
        if (!file.name.toLowerCase().endsWith('.xlsx')) {
            Swal.fire({
                icon: 'error',
                title: 'Format File Tidak Didukung',
                text: 'Hanya file format .xlsx yang diperbolehkan.',
            });
            return;
        }

        const btn = document.getElementById('btnStartImport');
        btn.disabled = true;
        btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i><span>Mengunggah File...</span>`;

        const formData = new FormData();
        formData.append('excel_file', file);
        formData.append('_token', '{{ csrf_token() }}');

        try {
            const resp = await fetch("{{ route('interview.import.upload') }}", {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            });

            const data = await resp.json();

            if (!resp.ok || !data.success) {
                throw new Error(data.message || 'Gagal mengunggah file.');
            }

            // Tutup modal upload, buka terminal modal
            closeImportCandidateModal();
            openTerminalImportModal(file.name);

            // Sambungkan ke Live SSE Stream
            startTerminalStream(data.stream_url);

        } catch (err) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal Upload',
                text: err.message || 'Terjadi kesalahan saat mengunggah file.',
            });
        } finally {
            btn.disabled = false;
            btn.innerHTML = `<i class="fa-solid fa-terminal"></i><span>Mulai Import (Terminal Live)</span>`;
        }
    }

    function openTerminalImportModal(fileName) {
        importTotalCount = 0;
        importSuccessCount = 0;
        importFailedCount = 0;
        importExpCount = 0;
        updateTerminalImportMetrics();

        document.getElementById('terminalImportTitle').textContent = `ASystem HR Terminal - ${fileName}`;
        document.getElementById('terminalImportLogList').innerHTML = '';
        document.getElementById('terminalImportStatusBadge').className = 'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30';
        document.getElementById('terminalImportStatusText').textContent = 'STREAMING ACTIVE';
        document.getElementById('terminalImportFooterNote').textContent = 'Mempersiapkan proses import stream...';
        document.getElementById('btnAbortImport').classList.remove('hidden');
        document.getElementById('btnRefreshAfterImport').classList.add('hidden');

        document.getElementById('terminalImportModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeTerminalImportModal() {
        if (currentEventSource) {
            if (!confirm('Proses import sedang berjalan. Yakin ingin menutup terminal dan membatalkan pemantauan?')) {
                return;
            }
            abortTerminalImport();
        }
        document.getElementById('terminalImportModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function clearTerminalImportLogs() {
        document.getElementById('terminalImportLogList').innerHTML = '';
    }

    function toggleTerminalImportFullscreen() {
        const modalWin = document.querySelector('#terminalImportModal > div');
        if (modalWin.classList.contains('max-w-4xl')) {
            modalWin.classList.remove('max-w-4xl');
            modalWin.classList.add('max-w-[98vw]', 'h-[96vh]');
            document.getElementById('terminalImportConsoleBody').classList.remove('max-h-[480px]');
            document.getElementById('terminalImportConsoleBody').classList.add('max-h-[calc(96vh-180px)]');
        } else {
            modalWin.classList.add('max-w-4xl');
            modalWin.classList.remove('max-w-[98vw]', 'h-[96vh]');
            document.getElementById('terminalImportConsoleBody').classList.add('max-h-[480px]');
            document.getElementById('terminalImportConsoleBody').classList.remove('max-h-[calc(96vh-180px)]');
        }
    }

    function copyTerminalImportLogs() {
        const container = document.getElementById('terminalImportLogList');
        navigator.clipboard.writeText(container.innerText).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Tersalin',
                text: 'Seluruh log terminal berhasil disalin ke clipboard!',
                timer: 1500,
                showConfirmButton: false,
            });
        });
    }

    function updateTerminalImportMetrics() {
        document.getElementById('termMetricTotal').textContent = importTotalCount;
        document.getElementById('termMetricSuccess').textContent = importSuccessCount;
        document.getElementById('termMetricFailed').textContent = importFailedCount;
        document.getElementById('termMetricExp').textContent = importExpCount;
    }

    function appendTerminalLog(payload) {
        const list = document.getElementById('terminalImportLogList');
        const line = document.createElement('div');
        line.className = 'flex items-start gap-2 py-0.5 leading-snug';

        const timeSpan = `<span class="text-slate-500 shrink-0 select-none">[${payload.time || new Date().toLocaleTimeString('id-ID')}]</span>`;
        let badge = '';
        let msgColor = 'text-slate-200';

        switch (payload.type) {
            case 'init':
                badge = `<span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-sky-500/20 text-sky-400 border border-sky-500/30">INIT</span>`;
                msgColor = 'text-sky-300 font-semibold';
                break;
            case 'info':
                badge = `<span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30">INFO</span>`;
                msgColor = 'text-slate-300';
                break;
            case 'success':
                badge = `<span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">SUCCESS</span>`;
                msgColor = 'text-emerald-300';
                importSuccessCount++;
                break;
            case 'archive':
                badge = `<span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-purple-500/20 text-purple-300 border border-purple-500/30">ARCHIVE</span>`;
                msgColor = 'text-purple-200';
                break;
            case 'replace':
                badge = `<span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-cyan-500/20 text-cyan-300 border border-cyan-500/30">REPLACE</span>`;
                msgColor = 'text-cyan-200';
                importSuccessCount++;
                break;
            case 'experience':
                badge = `<span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">WORK-EXP</span>`;
                msgColor = 'text-amber-200';
                importExpCount++;
                break;
            case 'warning':
                badge = `<span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30">WARN</span>`;
                msgColor = 'text-amber-300';
                break;
            case 'error':
                badge = `<span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-rose-500/20 text-rose-400 border border-rose-500/30">FAILED</span>`;
                msgColor = 'text-rose-400 font-semibold';
                importFailedCount++;
                break;
            case 'complete':
                badge = `<span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-emerald-500/30 text-emerald-300 border border-emerald-400">DONE</span>`;
                msgColor = 'text-emerald-200 font-bold';
                break;
            default:
                badge = `<span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-slate-700 text-slate-300">LOG</span>`;
        }

        if (payload.meta && payload.meta.total_rows) {
            importTotalCount = payload.meta.total_rows;
        }

        updateTerminalImportMetrics();

        line.innerHTML = `${timeSpan} ${badge} <span class="${msgColor}">${payload.message}</span>`;
        list.appendChild(line);

        // Auto-scroll ke bawah
        const body = document.getElementById('terminalImportConsoleBody');
        body.scrollTop = body.scrollHeight;
    }

    function startTerminalStream(streamUrl) {
        if (currentEventSource) {
            currentEventSource.close();
        }

        currentEventSource = new EventSource(streamUrl);

        currentEventSource.onmessage = function(e) {
            try {
                const payload = JSON.parse(e.data);
                appendTerminalLog(payload);

                if (payload.type === 'complete') {
                    finishTerminalImport(payload);
                }
            } catch (err) {
                console.error("Gagal parse SSE payload:", err, e.data);
            }
        };

        currentEventSource.onerror = function(err) {
            console.warn("EventSource stream terputus:", err);
            appendTerminalLog({
                type: 'info',
                message: 'Koneksi stream ditutup oleh server (proses tuntas).',
            });
            if (currentEventSource) {
                currentEventSource.close();
                currentEventSource = null;
            }
            document.getElementById('terminalImportStatusBadge').className = 'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-slate-700 text-slate-300';
            document.getElementById('terminalImportStatusText').textContent = 'SELESAI';
            document.getElementById('terminalImportFooterNote').textContent = 'Import selesai diproses.';
            document.getElementById('btnAbortImport').classList.add('hidden');
            document.getElementById('btnRefreshAfterImport').classList.remove('hidden');
        };
    }

    function finishTerminalImport(payload) {
        if (currentEventSource) {
            currentEventSource.close();
            currentEventSource = null;
        }

        document.getElementById('terminalImportStatusBadge').className = 'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40';
        document.getElementById('terminalImportStatusText').textContent = 'SELESAI';
        document.getElementById('terminalImportFooterNote').textContent = 'Import telah selesai diproses sepenuhnya.';
        document.getElementById('btnAbortImport').classList.add('hidden');
        document.getElementById('btnRefreshAfterImport').classList.remove('hidden');

        Swal.fire({
            icon: importFailedCount > 0 ? 'warning' : 'success',
            title: 'Import Selesai',
            html: `<div style="text-align: left; font-size: 13px;">
                Sukses Diimport: <b>${importSuccessCount} kandidat</b><br>
                Gagal / Skip: <b>${importFailedCount} baris</b><br>
                Pengalaman Ditambahkan: <b>${importExpCount}</b>
            </div>`,
            confirmButtonText: 'Refresh Data',
        }).then((result) => {
            if (result.isConfirmed) {
                finishAndRefresh();
            }
        });
    }

    function abortTerminalImport() {
        if (currentEventSource) {
            currentEventSource.close();
            currentEventSource = null;
        }
        appendTerminalLog({
            type: 'warning',
            message: 'Proses import dibatalkan oleh pengguna.',
        });
        document.getElementById('terminalImportStatusBadge').className = 'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-rose-500/20 text-rose-300';
        document.getElementById('terminalImportStatusText').textContent = 'DIBATALKAN';
        document.getElementById('btnAbortImport').classList.add('hidden');
    }

    function finishAndRefresh() {
        window.location.href = "{{ route('interview.index') }}";
    }

    // Modal Sync Odoo
    function openSyncOdooModal() {
        const modal = document.getElementById('syncOdooModal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeSyncOdooModal() {
        const modal = document.getElementById('syncOdooModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    }

    // ==============================================================
    // FITUR TARIK KANDIDAT DARI ODOO ERP VIA NIK
    // ==============================================================
    let currentOdooApplicantData = null;

    function openOdooNikModal() {
        document.getElementById('modalOdooNik').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        setTimeout(() => {
            const inp = document.getElementById('odooNikInput');
            if (inp) {
                inp.focus();
                inp.select();
            }
        }, 150);
    }

    function closeOdooNikModal() {
        document.getElementById('modalOdooNik').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function handleNikInput(input) {
        input.value = input.value.replace(/\D/g, '').slice(0, 16);
        const len = input.value.length;
        const counter = document.getElementById('odooNikCounter');
        if (counter) {
            counter.textContent = `${len} / 16 Digit`;
            if (len === 16) {
                counter.className = 'text-[11px] font-bold text-emerald-600 font-mono';
            } else {
                counter.className = 'text-[11px] font-semibold text-slate-400 font-mono';
            }
        }
    }

    function resetOdooNikModal() {
        currentOdooApplicantData = null;
        document.getElementById('odooNikLoading').classList.add('hidden');
        document.getElementById('odooNikPreview').classList.add('hidden');
        document.getElementById('odooNikSuccess').classList.add('hidden');
        document.getElementById('odooNikFooter').classList.remove('hidden');
        const inp = document.getElementById('odooNikInput');
        if (inp) {
            inp.value = '';
            inp.focus();
        }
        const counter = document.getElementById('odooNikCounter');
        if (counter) {
            counter.textContent = '0 / 16 Digit';
            counter.className = 'text-[11px] font-semibold text-slate-400 font-mono';
        }
    }

    async function searchCandidateByNik() {
        const nikInput = document.getElementById('odooNikInput');
        const nik = (nikInput.value || '').trim();
        const entity = document.getElementById('odooNikEntity').value || 'all';

        if (nik.length !== 16) {
            Swal.fire({
                icon: 'warning',
                title: 'Format NIK Belum Sesuai',
                text: `NIK wajib 16 digit angka (saat ini ${nik.length} digit).`,
            });
            nikInput.focus();
            return;
        }

        // Tampilkan loading
        document.getElementById('odooNikLoading').classList.remove('hidden');
        document.getElementById('odooNikPreview').classList.add('hidden');
        document.getElementById('odooNikSuccess').classList.add('hidden');
        document.getElementById('btnSearchOdoo').disabled = true;

        try {
            const resp = await fetch("{{ route('interview.odoo.lookup_nik') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ nik: nik, entity: entity })
            });

            let data;
            try {
                data = await resp.json();
            } catch (e) {
                data = { success: false, message: 'Gagal memproses respon dari server (Status ' + resp.status + ').' };
            }

            document.getElementById('odooNikLoading').classList.add('hidden');
            document.getElementById('btnSearchOdoo').disabled = false;

            if (!data.success) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Tidak Ditemukan di Odoo',
                    text: data.message || 'Data pelamar tidak ditemukan di Odoo ERP.',
                });
                return;
            }

            // Render Preview
            currentOdooApplicantData = data.applicant;
            const app = data.applicant;

            document.getElementById('previewName').textContent = app.name || '-';
            document.getElementById('previewNik').textContent = `NIK: ${app.nik}`;
            const kkEl = document.getElementById('previewKk');
            if (kkEl) {
                kkEl.textContent = app.no_kk ? `• No. KK: ${app.no_kk}` : '';
            }
            const foundBadge = document.getElementById('previewFoundBadge');
            if (foundBadge) {
                if (app.found_via === 'no_kk') {
                    foundBadge.textContent = 'Ditemukan via No. KK';
                    foundBadge.classList.remove('hidden');
                } else {
                    foundBadge.classList.add('hidden');
                }
            }
            document.getElementById('previewInitial').textContent = (app.name || 'KD').split(' ').map(w => w[0]).slice(0, 2).join('').toUpperCase();
            document.getElementById('previewEntityBadge').textContent = app.entity || 'Odoo';
            document.getElementById('previewJob').textContent = app.job || '-';
            document.getElementById('previewPrinciple').textContent = app.principle || '-';
            document.getElementById('previewArea').textContent = app.area || '-';
            document.getElementById('previewStage').textContent = app.stage || 'Data Pelamar';
            document.getElementById('previewPhone').textContent = app.phone ? `0${app.phone.replace(/^0+/, '')}` : 'Tidak ada telepon';
            
            let ttlText = app.birth || '-';
            if (app.birth_place) ttlText = `${app.birth_place}, ${ttlText}`;
            if (app.age) ttlText += ` (${app.age} Thn)`;
            document.getElementById('previewTtl').textContent = ttlText;

            // Warning jika data lama ada
            const warnEl = document.getElementById('previewArchiveWarning');
            if (data.existing_candidate) {
                warnEl.classList.remove('hidden');
            } else {
                warnEl.classList.add('hidden');
            }

            document.getElementById('odooNikPreview').classList.remove('hidden');

        } catch (err) {
            document.getElementById('odooNikLoading').classList.add('hidden');
            document.getElementById('btnSearchOdoo').disabled = false;
            Swal.fire({
                icon: 'error',
                title: 'Koneksi Gagal',
                text: 'Terjadi gangguan saat menghubungi server Odoo: ' + err.message,
            });
        }
    }

    async function saveCandidateFromOdoo() {
        if (!currentOdooApplicantData) return;
        const nik = currentOdooApplicantData.nik;
        const searchedNik = currentOdooApplicantData.searched_nik || nik;
        const entity = currentOdooApplicantData.entity;

        const btnSave = document.getElementById('btnSaveCandidate');
        btnSave.disabled = true;
        btnSave.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> <span>Menyimpan ke ASystem...</span>`;

        try {
            const resp = await fetch("{{ route('interview.odoo.import_nik') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ nik: nik, searched_nik: searchedNik, entity: entity })
            });

            let data;
            try {
                data = await resp.json();
            } catch (e) {
                data = { success: false, message: 'Gagal memproses respon dari server (Status ' + resp.status + ').' };
            }

            btnSave.disabled = false;
            btnSave.innerHTML = `<i class="fa-solid fa-cloud-arrow-down"></i> <span>Tarik & Proses Kandidat ke ASystem</span>`;

            if (!data.success) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menyimpan',
                    text: data.message || 'Terjadi kesalahan saat menyimpan data kandidat.',
                });
                return;
            }

            // Sembunyikan form & preview, tampilkan kartu sukses
            document.getElementById('odooNikPreview').classList.add('hidden');
            document.getElementById('odooNikFooter').classList.add('hidden');

            document.getElementById('succCandidateName').textContent = data.full_name;
            document.getElementById('succCandidateJob').textContent = `${data.principle} • ${data.job} • Area: ${data.area}`;
            document.getElementById('succLoginNik').textContent = data.nik;
            document.getElementById('succLoginPass').textContent = data.default_password;
            document.getElementById('succLoginUrl').textContent = data.cbt_login_url;

            const btnWa = document.getElementById('btnSendWaInvite');
            if (data.wa_link) {
                btnWa.href = data.wa_link;
                btnWa.classList.remove('hidden');
            } else {
                btnWa.classList.add('hidden');
            }

            const btnProfile = document.getElementById('btnViewCandidateProfile');
            btnProfile.href = data.detail_url;

            document.getElementById('odooNikSuccess').classList.remove('hidden');

            // Notifikasi Toast
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true
            });
            Toast.fire({
                icon: 'success',
                title: `Kandidat ${data.full_name} berhasil disimpan!`
            });

        } catch (err) {
            btnSave.disabled = false;
            btnSave.innerHTML = `<i class="fa-solid fa-cloud-arrow-down"></i> <span>Tarik & Proses Kandidat ke ASystem</span>`;
            Swal.fire({
                icon: 'error',
                title: 'Galat Sistem',
                text: 'Terjadi kesalahan: ' + err.message,
            });
        }
    }

    // Auto open modal jika URL mengandung ?open_import=1
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('open_import') === '1') {
            openImportCandidateModal();
        }

        // Live clock di console
        setInterval(() => {
            const clockEl = document.getElementById('termImportClock');
            if (clockEl) {
                clockEl.textContent = new Date().toLocaleTimeString('id-ID');
            }
        }, 1000);
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeTutorialModal();
            closeEditPrincipleModal();
            closeImportCandidateModal();
            closeSyncOdooModal();
            closeOdooNikModal();
        }
    });
</script>
@endsection