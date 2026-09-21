@extends('layouts.app')

@section('title', 'Kandidat Walkin Interview - ESA Groups')

@section('content')
<div class="space-y-5" x-data="walkinPage()">
    <!-- Top Action Bar & Title -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
        <div class="flex items-center gap-3">
            <a href="{{ route('interview.index') }}" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition-colors shadow-xs" title="Kembali ke Kandidat Interview">
                <i class="fa-solid fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h1 class="text-lg font-bold text-slate-900 uppercase tracking-wide flex items-center gap-2">
                    <span>Kandidat Interview</span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-rose-100 text-rose-700 font-bold tracking-normal normal-case">Walk-in</span>
                </h1>
                <p class="text-xs text-slate-500">Daftar kandidat walkin interview ESA Groups dan monitoring kelengkapan data</p>
            </div>
        </div>

        <div class="flex items-center flex-wrap gap-2">
            <!-- 1. Export Data Button -->
            <a href="{{ route('interview.walk.export', request()->query()) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition shadow-xs">
                <i class="fa-solid fa-file-excel text-xs"></i>
                <span>Export Data</span>
            </a>

            <!-- 2. Data Supply Kandidat (Buka Link Form Pendaftaran Publik) -->
            <a href="{{ route('interview.walk.register') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-white bg-[#1e40af] hover:bg-blue-800 transition shadow-xs" title="Buka Formulir Pendaftaran Walk-in untuk Kandidat / Publik">
                <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                <span>Data Supply Kandidat</span>
            </a>

            <!-- 3. Salin Link Form Pendaftaran untuk Kandidat -->
            <button type="button" @click="copyPublicRegisterLink()" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition shadow-xs cursor-pointer" title="Salin link form pendaftaran kandidat untuk dibagikan via WhatsApp">
                <i class="fa-regular fa-copy text-xs text-slate-500" x-show="!copiedLink"></i>
                <i class="fa-solid fa-check text-xs text-emerald-600" x-show="copiedLink"></i>
                <span x-text="copiedLink ? 'Link Tersalin!' : 'Salin Link Form'"></span>
            </button>
        </div>
    </div>

    <!-- Alert Success / Error -->
    @if(session('success'))
    <div class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-xs flex items-center justify-between gap-2 shadow-xs">
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    @endif

    <!-- Card Filter: Data Calon: -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 sm:p-5">
        <div class="flex items-center justify-between gap-2 mb-3.5 flex-wrap">
            <h2 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                <span>Data Calon:</span>
                @if($startDate === now()->toDateString() && $endDate === now()->toDateString())
                    <span class="text-[10px] px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 font-bold">
                        Hari Ini ({{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('d M Y') }})
                    </span>
                @elseif(!$startDate && !$endDate)
                    <span class="text-[10px] px-2 py-0.5 rounded-md bg-blue-100 text-blue-800 font-bold">
                        Semua Tanggal
                    </span>
                @endif
            </h2>

            <!-- Quick Filter Preset Links -->
            <div class="flex items-center gap-1.5 text-xs">
                <a href="{{ route('interview.walk') }}" 
                   class="px-2.5 py-1 rounded-lg font-semibold transition {{ ($startDate === now()->toDateString() && $endDate === now()->toDateString()) ? 'bg-primary text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Hari Ini
                </a>
                <a href="{{ route('interview.walk', ['all' => 1]) }}" 
                   class="px-2.5 py-1 rounded-lg font-semibold transition {{ (!$startDate && !$endDate) ? 'bg-primary text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Semua Data
                </a>
            </div>
        </div>

        <form method="GET" action="{{ route('interview.walk') }}" id="filterForm">
            <input type="hidden" name="per_page" value="{{ $perPage ?? 10 }}">

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5 items-end">
                <!-- Cari Data -->
                <div class="lg:col-span-1">
                    <label class="block text-xs font-medium text-slate-600 mb-1">Cari Data:</label>
                    <div class="relative">
                        <input type="text" 
                               name="search" 
                               value="{{ $search ?? '' }}" 
                               placeholder="Cari..." 
                               class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition">
                    </div>
                </div>

                <!-- Kategori -->
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Kategori:</label>
                    <select name="kategori" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition">
                        <option value="Semua" {{ ($kategori ?? 'Semua') === 'Semua' ? 'selected' : '' }}>Semua</option>
                        <option value="Green" {{ ($kategori ?? '') === 'Green' ? 'selected' : '' }}>Green</option>
                        <option value="Yellow" {{ ($kategori ?? '') === 'Yellow' ? 'selected' : '' }}>Yellow</option>
                        <option value="Red" {{ ($kategori ?? '') === 'Red' ? 'selected' : '' }}>Red</option>
                        <option value="Uncategorized" {{ ($kategori ?? '') === 'Uncategorized' ? 'selected' : '' }}>Uncategorized</option>
                    </select>
                </div>

                <!-- Dari Tanggal (Default: Hari Ini / tgl berjalan) -->
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Dari Tanggal:</label>
                    <input type="date" 
                           name="start_date" 
                           value="{{ $startDate ?? '' }}" 
                           class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition">
                </div>

                <!-- Sampai Tanggal (Default: Hari Ini / tgl berjalan) -->
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Sampai Tanggal:</label>
                    <input type="date" 
                           name="end_date" 
                           value="{{ $endDate ?? '' }}" 
                           class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition">
                </div>

                <!-- Filter & Refresh Buttons -->
                <div class="flex items-center gap-2">
                    <button type="submit" class="flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-[#334155] hover:bg-[#1e293b] transition shadow-xs cursor-pointer">
                        <span>Filter</span>
                    </button>
                    <a href="{{ route('interview.walk') }}" class="flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-[#2563eb] hover:bg-blue-700 transition shadow-xs text-center" title="Reset filter ke hari ini">
                        <span>Refresh</span>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- 4 Colored KPI Cards (Green, Yellow, Red, Uncategorized) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
        <!-- Card Green -->
        <a href="{{ route('interview.walk', array_merge(request()->except('kategori', 'page'), ['kategori' => 'Green', 'start_date' => $startDate, 'end_date' => $endDate])) }}" 
           class="p-4 rounded-2xl border transition-all duration-200 block shadow-xs hover:shadow-md cursor-pointer {{ ($kategori ?? '') === 'Green' ? 'ring-2 ring-emerald-500 bg-[#d1fae5] border-emerald-400' : 'bg-[#e6fbf2] border-[#a7f3d0] hover:bg-[#d8f9ea]' }}">
            <div class="flex items-start justify-between">
                <div>
                    <div class="text-[11px] font-bold text-[#047857]">
                        Green ({{ $pctGreen }}% / Total: {{ number_format($totalGreenAll) }})
                    </div>
                    <div class="text-2xl sm:text-3xl font-bold text-slate-900 mt-2">
                        {{ number_format($countGreenFiltered) }}
                    </div>
                </div>
                <div class="w-9 h-9 rounded-full border border-emerald-400 flex items-center justify-center text-emerald-600 bg-white/60">
                    <i class="fa-solid fa-check text-sm"></i>
                </div>
            </div>
        </a>

        <!-- Card Yellow -->
        <a href="{{ route('interview.walk', array_merge(request()->except('kategori', 'page'), ['kategori' => 'Yellow', 'start_date' => $startDate, 'end_date' => $endDate])) }}" 
           class="p-4 rounded-2xl border transition-all duration-200 block shadow-xs hover:shadow-md cursor-pointer {{ ($kategori ?? '') === 'Yellow' ? 'ring-2 ring-amber-500 bg-[#fef3c7] border-amber-400' : 'bg-[#fff8e6] border-[#fde68a] hover:bg-[#fef1cb]' }}">
            <div class="flex items-start justify-between">
                <div>
                    <div class="text-[11px] font-bold text-[#b45309]">
                        Yellow ({{ $pctYellow }}% / Total: {{ number_format($totalYellowAll) }})
                    </div>
                    <div class="text-2xl sm:text-3xl font-bold text-slate-900 mt-2">
                        {{ number_format($countYellowFiltered) }}
                    </div>
                </div>
                <div class="w-9 h-9 rounded-full border border-amber-400 flex items-center justify-center text-amber-600 bg-white/60">
                    <i class="fa-solid fa-exclamation text-sm"></i>
                </div>
            </div>
        </a>

        <!-- Card Red -->
        <a href="{{ route('interview.walk', array_merge(request()->except('kategori', 'page'), ['kategori' => 'Red', 'start_date' => $startDate, 'end_date' => $endDate])) }}" 
           class="p-4 rounded-2xl border transition-all duration-200 block shadow-xs hover:shadow-md cursor-pointer {{ ($kategori ?? '') === 'Red' ? 'ring-2 ring-rose-500 bg-[#fee2e2] border-rose-400' : 'bg-[#fff0f0] border-[#fecaca] hover:bg-[#fedcdc]' }}">
            <div class="flex items-start justify-between">
                <div>
                    <div class="text-[11px] font-bold text-[#be123c]">
                        Red ({{ $pctRed }}% / Total: {{ number_format($totalRedAll) }})
                    </div>
                    <div class="text-2xl sm:text-3xl font-bold text-slate-900 mt-2">
                        {{ number_format($countRedFiltered) }}
                    </div>
                </div>
                <div class="w-9 h-9 rounded-full border border-rose-400 flex items-center justify-center text-rose-600 bg-white/60">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </div>
            </div>
        </a>

        <!-- Card Uncategorized -->
        <a href="{{ route('interview.walk', array_merge(request()->except('kategori', 'page'), ['kategori' => 'Uncategorized', 'start_date' => $startDate, 'end_date' => $endDate])) }}" 
           class="p-4 rounded-2xl border transition-all duration-200 block shadow-xs hover:shadow-md cursor-pointer {{ ($kategori ?? '') === 'Uncategorized' ? 'ring-2 ring-slate-500 bg-[#e2e8f0] border-slate-400' : 'bg-[#e5e7eb] border-[#cbd5e1] hover:bg-[#dadde2]' }}">
            <div class="flex items-start justify-between">
                <div>
                    <div class="text-[11px] font-bold text-slate-700">
                        Uncategorized (Total: {{ number_format($totalUncatAll) }})
                    </div>
                    <div class="text-2xl sm:text-3xl font-bold text-slate-900 mt-2">
                        {{ number_format($countUncatFiltered) }}
                    </div>
                </div>
                <div class="w-9 h-9 rounded-full border border-slate-400 flex items-center justify-center text-slate-600 bg-white/60">
                    <i class="fa-solid fa-question text-sm"></i>
                </div>
            </div>
        </a>
    </div>

    <!-- Main Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <!-- Table Toolbar (Tampilkan X data & Search) -->
        <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-2 text-xs text-slate-600">
                <span>Tampilkan</span>
                <select onchange="changePerPage(this.value)" class="text-xs rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-1 text-slate-800 focus:outline-none focus:ring-1 focus:ring-primary">
                    <option value="10" {{ ($perPage ?? 10) == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ ($perPage ?? 10) == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ ($perPage ?? 10) == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ ($perPage ?? 10) == 100 ? 'selected' : '' }}>100</option>
                </select>
                <span>data</span>
            </div>

            <!-- Quick Table Search -->
            <form method="GET" action="{{ route('interview.walk') }}" class="flex items-center gap-2">
                <input type="hidden" name="kategori" value="{{ $kategori ?? 'Semua' }}">
                <input type="hidden" name="start_date" value="{{ $startDate ?? '' }}">
                <input type="hidden" name="end_date" value="{{ $endDate ?? '' }}">
                <input type="hidden" name="per_page" value="{{ $perPage ?? 10 }}">
                <div class="flex items-center gap-1.5 text-xs text-slate-600">
                    <label for="tableSearchInput" class="font-medium">Search:</label>
                    <input type="text" 
                           id="tableSearchInput"
                           name="search" 
                           value="{{ $search ?? '' }}" 
                           placeholder="" 
                           class="w-40 sm:w-56 text-xs rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-1 text-slate-800 focus:outline-none focus:ring-1 focus:ring-primary transition">
                </div>
            </form>
        </div>

        <!-- Responsive Table Container -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-slate-50/90 text-slate-700 font-bold border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-2.5 text-center w-10">No</th>
                        <th class="py-3 px-3 w-28 whitespace-nowrap">Tanggal</th>
                        <th class="py-3 px-3 w-36 whitespace-nowrap">No. KTP</th>
                        <th class="py-3 px-4 min-w-[170px]">Nama Kandidat</th>
                        <th class="py-3 px-3 w-28 whitespace-nowrap">Tgl. Lahir</th>
                        <th class="py-3 px-2.5 text-center w-20 whitespace-nowrap">Usia</th>
                        <th class="py-3 px-3 min-w-[110px]">Pendidikan Terakhir</th>
                        <th class="py-3 px-3 min-w-[130px]">Posisi Dilamar</th>
                        <th class="py-3 px-3 min-w-[100px]">Area</th>
                        <th class="py-3 px-3 min-w-[150px]">Rekrutor / AS</th>
                        <th class="py-3 px-3 min-w-[100px]">Info</th>
                        <th class="py-3 px-3 min-w-[110px]">Invite By</th>
                        <th class="py-3 px-3 text-center w-28 whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-800">
                    @forelse($candidates as $idx => $c)
                    <tr class="hover:bg-blue-50/30 transition-colors">
                        <!-- No -->
                        <td class="py-3 px-2.5 text-center text-slate-400 font-mono">
                            {{ $candidates->firstItem() + $idx }}
                        </td>

                        <!-- Tanggal -->
                        <td class="py-3 px-3 text-slate-600 font-medium whitespace-nowrap">
                            {{ $c->created_at ? $c->created_at->format('d M Y') : '-' }}
                        </td>

                        <!-- No. KTP -->
                        <td class="py-3 px-3 font-mono font-semibold text-slate-800 whitespace-nowrap">
                            {{ $c->nik }}
                        </td>

                        <!-- Nama Kandidat -->
                        <td class="py-3 px-4">
                            <span class="font-bold text-slate-900 block leading-tight">{{ $c->full_name }}</span>
                            @if($c->kategori_kandidat)
                                <span class="inline-block mt-0.5 text-[9px] px-1.5 py-0.2 rounded font-bold uppercase
                                    {{ $c->kategori_kandidat === 'Green' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                    {{ $c->kategori_kandidat === 'Yellow' ? 'bg-amber-100 text-amber-800' : '' }}
                                    {{ $c->kategori_kandidat === 'Red' ? 'bg-rose-100 text-rose-800' : '' }}
                                ">
                                    {{ $c->kategori_kandidat }}
                                </span>
                            @endif
                        </td>

                        <!-- Tgl. Lahir -->
                        <td class="py-3 px-3 text-slate-600 whitespace-nowrap">
                            {{ $c->birth_date ? \Carbon\Carbon::parse($c->birth_date)->format('d M Y') : '-' }}
                        </td>

                        <!-- Usia -->
                        <td class="py-3 px-2.5 text-center whitespace-nowrap font-medium text-slate-700">
                            {{ $c->birth_date ? \Carbon\Carbon::parse($c->birth_date)->age . ' Tahun' : '-' }}
                        </td>

                        <!-- Pendidikan Terakhir -->
                        <td class="py-3 px-3 text-slate-700">
                            {{ $c->education ?? '-' }}
                        </td>

                        <!-- Posisi Dilamar -->
                        <td class="py-3 px-3 font-medium text-slate-800">
                            {{ $c->applied_job ?? '-' }}
                        </td>

                        <!-- Area -->
                        <td class="py-3 px-3 text-slate-700">
                            {{ $c->area ?? '-' }}
                        </td>

                        <!-- Rekrutor / AS -->
                        <td class="py-3 px-3 text-slate-800">
                            <div class="font-medium text-slate-900 leading-tight">
                                {{ $c->user_name_formatted ?? ($c->useras ?: ($c->recruiter->name ?? 'Admin')) }}
                            </div>
                            @if(!empty($c->user_subtitle_formatted))
                                <div class="text-[10px] text-slate-400 mt-0.5 leading-tight">
                                    {{ $c->user_subtitle_formatted }}
                                </div>
                            @endif
                        </td>

                        <!-- Info -->
                        <td class="py-3 px-3 text-slate-600">
                            {{ $c->info ?? ($c->info_lowongan ?? '-') }}
                        </td>

                        <!-- Invite By -->
                        <td class="py-3 px-3 text-slate-600">
                            {{ $c->undangan ?? 'Walk interview' }}
                        </td>

                        <!-- Status & Aksi -->
                        <td class="py-3 px-3 text-center whitespace-nowrap">
                            <div class="inline-flex items-center gap-1.5">
                                <!-- Status Indicator Dot (Red = Belum Lengkap, Green = Sudah Lengkap) -->
                                @if($c->is_profile_complete)
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block shadow-xs" title="Data Sudah Lengkap"></span>
                                @else
                                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500 inline-block shadow-xs" title="Data Belum Lengkap"></span>
                                @endif

                                <!-- Tombol Centang Biru (Detail / Penilaian) -->
                                <a href="{{ route('interview.show', $c->id) }}" 
                                   class="w-6 h-6 rounded bg-[#0284c7] hover:bg-[#0369a1] text-white flex items-center justify-center transition shadow-xs" 
                                   title="Buka Form Interview / Detail">
                                    <i class="fa-solid fa-check text-[11px]"></i>
                                </a>

                                <!-- Tombol WhatsApp (jika nomor tersedia) -->
                                @if(!empty($c->whatsapp) || !empty($c->phone))
                                <a href="{{ $c->wa_url ?? ('https://wa.me/' . preg_replace('/[^0-9]/', '', $c->whatsapp ?: $c->phone)) }}" 
                                   target="_blank" 
                                   class="w-6 h-6 rounded bg-emerald-600 hover:bg-emerald-700 text-white flex items-center justify-center transition shadow-xs" 
                                   title="Kirim Pesan WhatsApp">
                                    <i class="fa-brands fa-whatsapp text-[12px]"></i>
                                </a>
                                @endif

                                <!-- Tombol Silang Merah (Arsip / Tolak) -->
                                <button type="button" 
                                        @click="openArchiveModal('{{ $c->id }}', '{{ addslashes($c->full_name) }}')"
                                        class="w-6 h-6 rounded bg-[#ef4444] hover:bg-[#dc2626] text-white flex items-center justify-center transition shadow-xs" 
                                        title="Arsipkan / Tolak Kandidat">
                                    <i class="fa-solid fa-xmark text-[11px]"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="13" class="py-12 text-center text-slate-400">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 text-xl">
                                    <i class="fa-solid fa-person-walking"></i>
                                </div>
                                <div class="font-medium text-slate-600 text-sm">Tidak ada data kandidat walk interview untuk filter tanggal ini.</div>
                                <p class="text-xs text-slate-400">Silakan sesuaikan filter tanggal atau klik tombol "Semua Data" di atas.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Table Pagination & Count Info -->
        <div class="p-4 border-t border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="text-xs text-slate-500">
                Menampilkan {{ $candidates->firstItem() ?? 0 }} hingga {{ $candidates->lastItem() ?? 0 }} dari {{ number_format($candidates->total()) }} data
            </div>
            <div>
                {{ $candidates->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

    <!-- Keterangan Legend at Bottom Left (sesuai gambar sistem lama) -->
    <div class="p-3 bg-white rounded-xl border border-slate-200/80 shadow-xs max-w-sm space-y-1 text-xs">
        <div class="font-bold text-slate-800 text-[11px] uppercase tracking-wider mb-1">Keterangan:</div>
        <div class="flex items-center gap-2 text-slate-700">
            <span class="w-2.5 h-2.5 rounded-full bg-rose-500 inline-block flex-shrink-0"></span>
            <span>: Data Belum Lengkap</span>
        </div>
        <div class="flex items-center gap-2 text-slate-700">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block flex-shrink-0"></span>
            <span>: Data Sudah Lengkap</span>
        </div>
    </div>

    <!-- Modal Archive Candidate -->
    <div x-show="openArchive" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div @click.away="openArchive = false" class="bg-white rounded-2xl shadow-xl max-w-sm w-full p-5 text-center">
            <div class="w-12 h-12 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto mb-3 text-xl">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <h4 class="font-bold text-slate-900 text-sm">Arsipkan Kandidat?</h4>
            <p class="text-xs text-slate-500 mt-1" x-text="'Kandidat ' + archiveName + ' akan diarsipkan dari daftar kandidat aktif.'"></p>
            
            <form :action="'{{ url('/interview/archive') }}/' + archiveId" method="POST" class="mt-4">
                @csrf
                <input type="hidden" name="alasanarsip" value="Diarsipkan dari Kandidat Walkin">
                <div class="flex items-center gap-2 justify-center">
                    <button type="button" @click="openArchive = false" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200">Batal</button>
                    <button type="submit" class="px-3.5 py-1.5 rounded-lg text-xs font-bold text-white bg-rose-600 hover:bg-rose-700">Ya, Arsipkan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function walkinPage() {
    return {
        openArchive: false,
        archiveId: '',
        archiveName: '',
        copiedLink: false,
        openArchiveModal(id, name) {
            this.archiveId = id;
            this.archiveName = name;
            this.openArchive = true;
        },
        copyPublicRegisterLink() {
            const link = "{{ route('interview.walk.register') }}";
            navigator.clipboard.writeText(link).then(() => {
                this.copiedLink = true;
                setTimeout(() => {
                    this.copiedLink = false;
                }, 3000);
            }).catch(() => {
                prompt("Salin link pendaftaran berikut:", link);
            });
        }
    }
}

function changePerPage(val) {
    const url = new URL(window.location.href);
    url.searchParams.set('per_page', val);
    url.searchParams.delete('page');
    window.location.href = url.toString();
}
</script>
@endsection
