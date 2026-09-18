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
            <button onclick="openImportCandidateModal()" type="button" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-blue-50 hover:bg-blue-100 text-primary border border-blue-200 text-xs font-bold transition-all shadow-sm cursor-pointer">
                <i class="fa-solid fa-cloud-arrow-up text-primary"></i>
                <span>Import Data</span>
            </button>

            <!-- 4. Export Data Button -->
            <a href="{{ route('interview.export') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 text-xs font-bold transition-all shadow-sm">
                <i class="fa-solid fa-file-excel text-emerald-600"></i>
                <span>Export Data</span>
            </a>
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

    <!-- 3. TABLE 1: DATA KANDIDAT MILIK REKRUTOR -->
    <div class="table-card">
        <!-- Table Header & Search Bar (Clean Flex Layout - NO OVERLAPPING) -->
        <div class="px-6 py-4 border-b border-slate-200 flex flex-col lg:flex-row lg:items-center justify-between gap-3 bg-white">
            <div class="flex items-center gap-2.5 flex-wrap">
                <span class="w-2.5 h-2.5 rounded-full bg-primary animate-pulse"></span>
                <h2 class="text-sm font-bold text-slate-900">
                    @if($filterUser === 'all')
                        Data Kandidat &bull; Semua Rekruter (Nasional)
                    @else
                        Data Kandidat &bull; {{ $displayRecruiterName ?? $user->name }} ({{ strtoupper($displayRecruiterTitle ?? ($user->job_title ?? 'REKRUTMEN')) }} - {{ strtoupper($displayRecruiterArea ?? ($user->area ?? 'JAKARTA')) }})
                    @endif
                </h2>
                <span class="badge-pill bg-blue-50 text-primary border-blue-200">
                    <i class="fa-solid fa-user-check text-[10px] mr-1"></i> {{ number_format($myCandidates->total()) }} Kandidat
                </span>
            </div>

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full lg:w-auto">
                <!-- Dropdown Filter Rekruter (Untuk Admin) -->
                @if(Auth::check() && (Auth::user()->isAdmin() || Auth::user()->role === 'admin'))
                <form method="GET" action="{{ route('interview.index') }}" class="flex items-center gap-1.5 flex-shrink-0">
                    @if(request('search_my'))
                        <input type="hidden" name="search_my" value="{{ request('search_my') }}">
                    @endif
                    <div class="flex items-center gap-1 bg-slate-50 border border-slate-200 rounded-xl px-2.5 py-1.5">
                        <i class="fa-solid fa-user-gear text-primary text-xs"></i>
                        <select name="filter_user" onchange="this.form.submit()" class="bg-transparent text-xs font-bold text-slate-700 focus:outline-none cursor-pointer">
                            <option value="my" {{ (empty($filterUser) || $filterUser === 'my') ? 'selected' : '' }}>👤 Data Saya ({{ $user->name }})</option>
                            <option value="all" {{ $filterUser === 'all' ? 'selected' : '' }}>🌐 Semua Rekruter (Nasional)</option>
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
                    <div class="relative w-full sm:w-64">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" name="search_my" value="{{ request('search_my') }}" placeholder="Cari nama, NIK, jabatan..." 
                               class="w-full pl-9 pr-3 py-1.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary bg-slate-50">
                    </div>
                    <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-primary text-white text-xs font-bold hover:bg-primary-700 shadow-sm flex-shrink-0">
                        Cari
                    </button>
                    @if(request('search_my') || $filterUser)
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
                                @if(strtolower($candidate->gender ?? '') === 'perempuan')
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
                            <td colspan="12" class="text-center py-12">
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
                {{ $myCandidates->appends(['search_my' => $searchMy])->links() }}
            </div>
        </div>
    </div>

    @if(!$isAdmin || (!empty($filterUser) && $filterUser !== 'all'))
    <!-- 4. TABLE 2: DATA KANDIDAT REKAN SE-AREA -->
    <div class="table-card">
        <div class="px-6 py-4 border-b border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-3 bg-white">
            <div class="flex items-center gap-2.5">
                <span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span>
                <h2 class="text-sm font-bold text-slate-900">
                    Data Kandidat Rekan Se-Area &bull; {{ strtoupper($user->area ?? 'JAKARTA') }}
                </h2>
                <span class="badge-pill bg-purple-50 text-purple-700 border-purple-200">
                    {{ $areaCandidates->total() }} Kandidat
                </span>
            </div>

            <!-- Search Area Form -->
            <form method="GET" action="{{ route('interview.index') }}" class="flex items-center gap-2 w-full md:w-auto">
                <div class="relative w-full md:w-72">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" name="search_area" value="{{ request('search_area') }}" placeholder="Cari kandidat rekan area..." 
                           class="w-full pl-9 pr-3 py-1.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-600 bg-slate-50">
                </div>
                <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-purple-600 text-white text-xs font-bold hover:bg-purple-700 shadow-sm flex-shrink-0">
                    Cari
                </button>
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
                        <th class="text-center">PSIKOTES</th>
                        <th class="text-center">MATH</th>
                        <th class="text-center">COMPUTER</th>
                        <th>REKRUTOR</th>
                        <th class="text-center min-w-[130px]">ACTION</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($areaCandidates as $index => $candidate)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="text-center font-bold text-slate-400 text-xs">
                                {{ $areaCandidates->firstItem() + $index }}
                            </td>
                            <td>
                                <span class="font-mono text-xs font-bold text-slate-700">{{ $candidate->nik }}</span>
                            </td>
                            <td>
                                <div class="font-bold text-xs text-slate-900">{{ $candidate->full_name }}</div>
                                <div class="text-[10px] text-slate-400">Area: {{ $candidate->area }}</div>
                            </td>
                            <td>
                                @if(strtolower($candidate->gender ?? '') === 'perempuan')
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
                                <div class="text-[11px] text-slate-500">{{ $candidate->age }} Thn</div>
                            </td>
                            <td>
                                <div class="text-xs text-slate-800">{{ $candidate->education ?? '-' }}</div>
                            </td>
                            <td>
                                <div class="font-bold text-xs text-slate-900">{{ strtoupper($candidate->principle->name ?? '-') }}</div>
                                <div class="text-[11px] text-primary">{{ $candidate->applied_job ?? '-' }}</div>
                            </td>
                            @php
                                $areaPsikotes = $candidate->psikotes_score;
                                $areaMath = $candidate->math_score;
                                $areaComputer = $candidate->computer_score;
                            @endphp
                            <td class="text-center">
                                @if($candidate->is_psikotes_done)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 text-xs font-bold" title="Sudah Tes Psikotes"><i class="fa-solid fa-check"></i></span>
                                @else
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-rose-100 text-rose-500 text-xs font-bold" title="Belum Selesai"><i class="fa-solid fa-xmark"></i></span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($candidate->is_math_done)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 text-xs font-bold"><i class="fa-solid fa-check"></i></span>
                                @else
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-rose-100 text-rose-500 text-xs font-bold"><i class="fa-solid fa-xmark"></i></span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($candidate->is_komputer_done)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 text-xs font-bold"><i class="fa-solid fa-check"></i></span>
                                @else
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-rose-100 text-rose-500 text-xs font-bold"><i class="fa-solid fa-xmark"></i></span>
                                @endif
                            </td>
                            <td>
                                <div class="font-semibold text-xs text-slate-700">{{ $candidate->user_name_formatted ?? $candidate->user_display_name }}</div>
                                <div class="text-[10px] text-slate-400 uppercase font-semibold">{{ $candidate->user_subtitle_formatted ?? ('ARO ' . ($candidate->area ?? 'JAKARTA')) }}</div>
                            </td>
                            <td class="text-center">
                                <div class="inline-flex items-center gap-1.5 justify-center">
                                    <a href="{{ route('interview.show', $candidate->id) }}" class="w-8 h-8 rounded-lg bg-blue-50 text-primary hover:bg-primary hover:text-white flex items-center justify-center text-sm shadow-sm" title="Lihat Hasil">
                                        <i class="fa-solid fa-clipboard-check"></i>
                                    </a>
                                    <a href="{{ $candidate->wa_url }}" target="_blank" class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white flex items-center justify-center text-sm shadow-sm" title="Kirim WA">
                                        <i class="fa-brands fa-whatsapp text-base"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="text-center py-8 text-xs text-slate-400">
                                Belum ada kandidat lain di area {{ strtoupper($user->area ?? 'JAKARTA') }}.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

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

            <!-- Download Template Button -->
            <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-200">
                <div class="flex items-center gap-2.5">
                    <i class="fa-solid fa-download text-slate-400 text-sm"></i>
                    <div>
                        <div class="text-xs font-bold text-slate-800">Belum punya template?</div>
                        <div class="text-[10px] text-slate-400">Unduh format template resmi</div>
                    </div>
                </div>
                <a href="{{ route('interview.import.template') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-slate-300 hover:border-primary hover:text-primary text-xs font-bold text-slate-700 transition shadow-xs">
                    <i class="fa-solid fa-file-arrow-down text-emerald-600"></i>
                    <span>Unduh hr.applicant.xlsx</span>
                </a>
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
        }
    });
</script>
@endsection