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
                        <i class="fa-solid fa-location-dot text-[10px]"></i> Area: {{ strtoupper($user->area ?? 'JAKARTA') }}
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
            <a href="{{ url('/importcalontest') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-blue-50 hover:bg-blue-100 text-primary border border-blue-200 text-xs font-bold transition-all shadow-sm">
                <i class="fa-solid fa-cloud-arrow-up text-primary"></i>
                <span>Import Data</span>
            </a>

            <!-- 4. Export Data Button -->
            <a href="{{ route('interview.export') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 text-xs font-bold transition-all shadow-sm">
                <i class="fa-solid fa-file-excel text-emerald-600"></i>
                <span>Export Data</span>
            </a>
        </div>
    </div>

    <!-- 2. STATS METRIC ROW (Clean Responsive 4-Column Grid) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Stat 1: Kandidat Milik Anda -->
        <div class="stat-box">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Kandidat Milik Anda</div>
                    <div class="text-2xl font-black text-slate-900 mt-1">{{ $myCandidates->total() }}</div>
                    <div class="text-[11px] text-primary font-semibold mt-0.5 flex items-center gap-1">
                        <i class="fa-solid fa-user-check"></i>
                        <span>Rekrutor: {{ $user->name }}</span>
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
                    <div class="text-2xl font-black text-emerald-600 mt-1">{{ $myCandidates->where('is_profile_complete', true)->count() }}</div>
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
                    <div class="text-2xl font-black text-amber-600 mt-1">{{ $myCandidates->filter(function($c) { return ($c->psikotes_score?->score ?? 0) > 0 || ($c->math_score?->score ?? 0) > 0; })->count() }}</div>
                    <div class="text-[11px] text-amber-600 font-semibold mt-0.5 flex items-center gap-1">
                        <i class="fa-solid fa-circle-dot"></i>
                        <span>DISC & Matematika</span>
                    </div>
                </div>
                <div class="stat-box-icon bg-amber-50 text-amber-600">
                    <i class="fa-solid fa-file-signature"></i>
                </div>
            </div>
        </div>

        <!-- Stat 4: Kandidat Area JAKARTA -->
        <div class="stat-box">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Kandidat Area {{ strtoupper($user->area ?? 'JAKARTA') }}</div>
                    <div class="text-2xl font-black text-purple-600 mt-1">{{ $areaCandidates->total() }}</div>
                    <div class="text-[11px] text-purple-600 font-semibold mt-0.5 flex items-center gap-1">
                        <i class="fa-solid fa-map-pin"></i>
                        <span>Rekan Se-Wilayah</span>
                    </div>
                </div>
                <div class="stat-box-icon bg-purple-50 text-purple-600">
                    <i class="fa-solid fa-location-dot"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. TABLE 1: DATA KANDIDAT MILIK REKRUTOR -->
    <div class="table-card">
        <!-- Table Header & Search Bar (Clean Flex Layout - NO OVERLAPPING) -->
        <div class="px-6 py-4 border-b border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-3 bg-white">
            <div class="flex items-center gap-2.5 flex-wrap">
                <span class="w-2.5 h-2.5 rounded-full bg-primary animate-pulse"></span>
                <h2 class="text-sm font-bold text-slate-900">
                    Data Kandidat &bull; {{ $user->name }} ({{ strtoupper($user->job_title ?? 'REKRUTMEN') }} - {{ strtoupper($user->area ?? 'JAKARTA') }})
                </h2>
                <span class="badge-pill bg-blue-50 text-primary border-blue-200">
                    {{ $myCandidates->total() }} Kandidat
                </span>
            </div>

            <!-- Search Form -->
            <form method="GET" action="{{ route('interview.index') }}" class="flex items-center gap-2 w-full md:w-auto">
                <div class="relative w-full md:w-72">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" name="search_my" value="{{ request('search_my') }}" placeholder="Cari nama, NIK, jabatan..." 
                           class="w-full pl-9 pr-3 py-1.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary bg-slate-50">
                </div>
                <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-primary text-white text-xs font-bold hover:bg-primary-700 shadow-sm flex-shrink-0">
                    Cari
                </button>
                @if(request('search_my'))
                    <a href="{{ route('interview.index') }}" class="px-2.5 py-1.5 rounded-xl bg-slate-100 text-slate-600 text-xs font-semibold hover:bg-slate-200">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <!-- Responsive Custom Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left custom-table">
                <thead>
                    <tr>
                        <th class="w-12 text-center">NO</th>
                        <th class="w-36">NO. KTP</th>
                        <th>NAMA KANDIDAT</th>
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
                                    <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 font-bold text-xs flex items-center justify-center flex-shrink-0 border border-slate-200">
                                        {{ strtoupper(substr($candidate->full_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-xs text-slate-900 leading-tight">{{ $candidate->full_name }}</div>
                                        <div class="text-[10px] text-slate-400 mt-0.5">
                                            <span>WhatsApp: {{ $candidate->phone ?? '-' }}</span>
                                        </div>
                                    </div>
                                </div>
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
                                @if($psikotes && $psikotes->is_passed)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 text-xs shadow-sm" title="Lulus Psikotes">
                                        <i class="fa-solid fa-check"></i>
                                    </span>
                                @elseif($psikotes && $psikotes->score > 0)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-blue-100 text-primary text-xs font-bold" title="Score: {{ $psikotes->score }}">
                                        {{ $psikotes->score }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-rose-100 text-rose-500 text-xs" title="Belum Selesai">
                                        <i class="fa-solid fa-minus"></i>
                                    </span>
                                @endif
                            </td>

                            <!-- MATH -->
                            <td class="text-center">
                                @if($math && $math->is_passed)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 text-xs shadow-sm" title="Lulus Matematika: {{ $math->score }}">
                                        <i class="fa-solid fa-check"></i>
                                    </span>
                                @elseif($math && $math->score > 0)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-amber-100 text-amber-700 text-xs font-bold" title="Score: {{ $math->score }}">
                                        {{ $math->score }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-rose-100 text-rose-500 text-xs" title="Belum Selesai">
                                        <i class="fa-solid fa-minus"></i>
                                    </span>
                                @endif
                            </td>

                            <!-- COMPUTER -->
                            <td class="text-center">
                                @if($computer && $computer->is_passed)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 text-xs shadow-sm" title="Lulus Komputer">
                                        <i class="fa-solid fa-check"></i>
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-rose-100 text-rose-500 text-xs" title="Belum Selesai">
                                        <i class="fa-solid fa-minus"></i>
                                    </span>
                                @endif
                            </td>

                            <!-- USER / REKRUTOR -->
                            <td>
                                <div class="font-bold text-xs text-slate-800">{{ $candidate->recruiter->name ?? $user->name }}</div>
                                <div class="text-[10px] text-slate-400 uppercase font-semibold">ARO {{ $candidate->area ?? 'JAKARTA' }}</div>
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
                            <td colspan="11" class="text-center py-12">
                                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3 text-lg">
                                    <i class="fa-solid fa-user-slash"></i>
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
                                @if($areaPsikotes && ($areaPsikotes->is_passed || $areaPsikotes->score > 0))
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 text-xs"><i class="fa-solid fa-check"></i></span>
                                @else
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-rose-100 text-rose-500 text-xs"><i class="fa-solid fa-minus"></i></span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($areaMath && ($areaMath->is_passed || $areaMath->score > 0))
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 text-xs"><i class="fa-solid fa-check"></i></span>
                                @else
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-rose-100 text-rose-500 text-xs"><i class="fa-solid fa-minus"></i></span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-rose-100 text-rose-500 text-xs"><i class="fa-solid fa-minus"></i></span>
                            </td>
                            <td>
                                <div class="font-semibold text-xs text-slate-700">{{ $candidate->recruiter->name ?? 'Rekan Area' }}</div>
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
                            <td colspan="11" class="text-center py-8 text-xs text-slate-400">
                                Tidak ada data rekan area lain saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
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

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeTutorialModal();
            closeEditPrincipleModal();
        }
    });
</script>
@endsection