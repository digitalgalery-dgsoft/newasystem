@extends('layouts.public')

@section('title', 'Lowongan Kerja - ASystem Career ESA Groups')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10 space-y-8">
    <!-- HERO BANNER -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-blue-950 via-indigo-950 to-slate-900 text-white p-6 sm:p-12 shadow-2xl border border-blue-800/40">
        <!-- Ambient background circles -->
        <div class="absolute -right-10 -bottom-10 w-80 h-80 bg-blue-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute right-1/3 -top-10 w-72 h-72 bg-indigo-500/20 rounded-full blur-2xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-8">
            <div class="max-w-2xl space-y-4">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-200 text-xs font-semibold backdrop-blur-md">
                    <i class="fa-solid fa-sparkles text-amber-400"></i>
                    <span>Official Career Portal &bull; ESA Groups</span>
                </div>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight leading-tight text-white">
                    Find Your Dream Job <br class="hidden sm:inline"><span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-sky-300 to-indigo-300">With Us Today</span>
                </h1>
                <p class="text-sm sm:text-base text-slate-300 font-normal leading-relaxed max-w-xl">
                    Temukan posisi impian yang sesuai dengan kualifikasi dan passion Anda di seluruh unit bisnis ESA Groups. Nikmati proses seleksi digital cepat dengan penilaian kecocokan profil AI.
                </p>
                <div class="flex flex-wrap items-center gap-3 pt-2">
                    <a href="https://whatsapp.com/channel/0029VbDqNdb60eBgrCxsnt07" target="_blank" class="inline-flex items-center gap-2.5 px-5 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs sm:text-sm shadow-lg shadow-emerald-900/30 transition-all hover:shadow-emerald-600/40 hover:-translate-y-0.5">
                        <i class="fa-brands fa-whatsapp text-lg"></i>
                        <span>Join Channel Info LowKer</span>
                    </a>
                    <a href="#searchSection" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-white/10 hover:bg-white/20 text-white font-semibold text-xs sm:text-sm backdrop-blur-md border border-white/20 transition-all hover:-translate-y-0.5">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <span>Cari Posisi</span>
                    </a>
                </div>
            </div>

            <!-- Stats Highlight Cards -->
            <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:w-80 shrink-0">
                <div class="bg-white/10 backdrop-blur-md border border-white/15 p-4 rounded-2xl text-center">
                    <div class="text-2xl sm:text-3xl font-black text-amber-400 mb-0.5">{{ $jobs->total() }}</div>
                    <div class="text-xs text-slate-300 font-medium">Lowongan Aktif</div>
                </div>
                <div class="bg-white/10 backdrop-blur-md border border-white/15 p-4 rounded-2xl text-center">
                    <div class="text-2xl sm:text-3xl font-black text-sky-400 mb-0.5">{{ count($distinctAreas) }}</div>
                    <div class="text-xs text-slate-300 font-medium">Area Penempatan</div>
                </div>
                <div class="bg-white/10 backdrop-blur-md border border-white/15 p-4 rounded-2xl text-center">
                    <div class="text-2xl sm:text-3xl font-black text-emerald-400 mb-0.5">100%</div>
                    <div class="text-xs text-slate-300 font-medium">Proses Digital</div>
                </div>
                <div class="bg-white/10 backdrop-blur-md border border-white/15 p-4 rounded-2xl text-center">
                    <div class="text-2xl sm:text-3xl font-black text-purple-400 mb-0.5">AI Ready</div>
                    <div class="text-xs text-slate-300 font-medium">Smart Matcher</div>
                </div>
            </div>
        </div>
    </div>

    <!-- SEARCH & FILTER SECTION -->
    <div id="searchSection" class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-base font-bold text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-filter text-primary"></i>
                <span>Filter & Pencarian Lowongan</span>
            </h2>
            @if(!empty($search) || !empty($selectedJob) || !empty($selectedArea) || !empty($selectedCity))
                <a href="{{ route('job.public') }}" class="text-xs font-semibold text-rose-600 hover:underline flex items-center gap-1">
                    <i class="fa-solid fa-rotate-left"></i> Reset Filter
                </a>
            @endif
        </div>

        <form action="{{ route('job.public') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3">
            <!-- Keyword Search -->
            <div class="lg:col-span-4 relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </div>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari posisi, skill, atau kata kunci..." class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
            </div>

            <!-- Jabatan Filter (Searchable Dropdown) -->
            <div class="lg:col-span-3" x-data="searchableSelect({
                name: 'job',
                placeholder: 'Semua Jabatan',
                searchPlaceholder: 'Ketik cari jabatan...',
                selected: '{{ addslashes($selectedJob ?? '') }}',
                options: {{ json_encode(collect($distinctJobs)->values()->all()) }}
            })">
                <input type="hidden" :name="name" :value="selectedValue">
                <div class="relative" @click.outside="open = false">
                    <button type="button" 
                            @click="toggle()" 
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all flex items-center justify-between text-left gap-1 cursor-pointer">
                        <span class="truncate" :class="selectedValue ? 'font-bold text-slate-900' : 'text-slate-500'" x-text="displayLabel"></span>
                        <div class="flex items-center gap-1 shrink-0">
                            <span x-show="selectedValue" @click="clear($event)" class="text-slate-400 hover:text-rose-500 p-0.5 rounded transition" title="Hapus pilihan">
                                <i class="fa-solid fa-xmark text-[10px]"></i>
                            </span>
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                        </div>
                    </button>

                    <!-- Dropdown Search Menu -->
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-1"
                         class="absolute left-0 right-0 top-full mt-1.5 z-50 bg-white rounded-xl shadow-2xl border border-slate-200 overflow-hidden min-w-[240px]" 
                         style="display: none;">
                        
                        <!-- Search Box in Dropdown -->
                        <div class="p-2 border-b border-slate-100 bg-slate-50/70">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400">
                                    <i class="fa-solid fa-magnifying-glass text-[11px]"></i>
                                </div>
                                <input type="text" 
                                       x-ref="searchInput" 
                                       x-model="searchQuery" 
                                       @keydown.escape="open = false" 
                                       :placeholder="searchPlaceholder" 
                                       class="w-full pl-7 pr-7 py-1.5 bg-white border border-slate-200 rounded-lg text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/30">
                                <button type="button" x-show="searchQuery" @click="searchQuery = ''; $refs.searchInput.focus()" class="absolute inset-y-0 right-0 pr-2 flex items-center text-slate-400 hover:text-slate-600">
                                    <i class="fa-solid fa-xmark text-[10px]"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Options List -->
                        <div class="max-h-56 overflow-y-auto p-1 text-xs space-y-0.5">
                            <button type="button" 
                                    @click="select('')" 
                                    class="w-full px-2.5 py-1.5 rounded-lg text-left transition flex items-center justify-between font-medium cursor-pointer"
                                    :class="!selectedValue ? 'bg-primary-50 text-primary font-bold' : 'text-slate-600 hover:bg-slate-50'">
                                <span>Semua Jabatan</span>
                                <i x-show="!selectedValue" class="fa-solid fa-check text-[10px] text-primary"></i>
                            </button>
                            
                            <template x-for="item in filteredOptions" :key="item">
                                <button type="button" 
                                        @click="select(item)" 
                                        class="w-full px-2.5 py-1.5 rounded-lg text-left transition flex items-center justify-between text-xs cursor-pointer"
                                        :class="selectedValue === item ? 'bg-primary/10 text-primary font-bold' : 'text-slate-700 hover:bg-slate-100'">
                                    <span class="truncate" x-text="item"></span>
                                    <i x-show="selectedValue === item" class="fa-solid fa-check text-[10px] text-primary shrink-0 ml-1"></i>
                                </button>
                            </template>

                            <div x-show="filteredOptions.length === 0" class="py-4 text-center text-slate-400 text-xs">
                                <i class="fa-solid fa-inbox text-sm mb-1 block opacity-60"></i>
                                <span>Tidak ada jabatan cocok</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Area Filter (Searchable Dropdown) -->
            <div class="lg:col-span-2" x-data="searchableSelect({
                name: 'area',
                placeholder: 'Semua Area',
                searchPlaceholder: 'Ketik cari area...',
                selected: '{{ addslashes($selectedArea ?? '') }}',
                options: {{ json_encode(collect($distinctAreas)->values()->all()) }}
            })">
                <input type="hidden" :name="name" :value="selectedValue">
                <div class="relative" @click.outside="open = false">
                    <button type="button" 
                            @click="toggle()" 
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all flex items-center justify-between text-left gap-1 cursor-pointer">
                        <span class="truncate" :class="selectedValue ? 'font-bold text-slate-900' : 'text-slate-500'" x-text="displayLabel"></span>
                        <div class="flex items-center gap-1 shrink-0">
                            <span x-show="selectedValue" @click="clear($event)" class="text-slate-400 hover:text-rose-500 p-0.5 rounded transition" title="Hapus pilihan">
                                <i class="fa-solid fa-xmark text-[10px]"></i>
                            </span>
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                        </div>
                    </button>

                    <!-- Dropdown Search Menu -->
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-1"
                         class="absolute left-0 right-0 top-full mt-1.5 z-50 bg-white rounded-xl shadow-2xl border border-slate-200 overflow-hidden min-w-[220px]" 
                         style="display: none;">
                        
                        <!-- Search Box in Dropdown -->
                        <div class="p-2 border-b border-slate-100 bg-slate-50/70">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400">
                                    <i class="fa-solid fa-magnifying-glass text-[11px]"></i>
                                </div>
                                <input type="text" 
                                       x-ref="searchInput" 
                                       x-model="searchQuery" 
                                       @keydown.escape="open = false" 
                                       :placeholder="searchPlaceholder" 
                                       class="w-full pl-7 pr-7 py-1.5 bg-white border border-slate-200 rounded-lg text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/30">
                                <button type="button" x-show="searchQuery" @click="searchQuery = ''; $refs.searchInput.focus()" class="absolute inset-y-0 right-0 pr-2 flex items-center text-slate-400 hover:text-slate-600">
                                    <i class="fa-solid fa-xmark text-[10px]"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Options List -->
                        <div class="max-h-56 overflow-y-auto p-1 text-xs space-y-0.5">
                            <button type="button" 
                                    @click="select('')" 
                                    class="w-full px-2.5 py-1.5 rounded-lg text-left transition flex items-center justify-between font-medium cursor-pointer"
                                    :class="!selectedValue ? 'bg-primary-50 text-primary font-bold' : 'text-slate-600 hover:bg-slate-50'">
                                <span>Semua Area</span>
                                <i x-show="!selectedValue" class="fa-solid fa-check text-[10px] text-primary"></i>
                            </button>
                            
                            <template x-for="item in filteredOptions" :key="item">
                                <button type="button" 
                                        @click="select(item)" 
                                        class="w-full px-2.5 py-1.5 rounded-lg text-left transition flex items-center justify-between text-xs cursor-pointer"
                                        :class="selectedValue === item ? 'bg-primary/10 text-primary font-bold' : 'text-slate-700 hover:bg-slate-100'">
                                    <span class="truncate" x-text="item"></span>
                                    <i x-show="selectedValue === item" class="fa-solid fa-check text-[10px] text-primary shrink-0 ml-1"></i>
                                </button>
                            </template>

                            <div x-show="filteredOptions.length === 0" class="py-4 text-center text-slate-400 text-xs">
                                <i class="fa-solid fa-inbox text-sm mb-1 block opacity-60"></i>
                                <span>Tidak ada area cocok</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kota Filter (Searchable Dropdown) -->
            <div class="lg:col-span-2" x-data="searchableSelect({
                name: 'city',
                placeholder: 'Semua Kota',
                searchPlaceholder: 'Ketik cari kota...',
                selected: '{{ addslashes($selectedCity ?? '') }}',
                options: {{ json_encode(collect($distinctCities)->values()->all()) }}
            })">
                <input type="hidden" :name="name" :value="selectedValue">
                <div class="relative" @click.outside="open = false">
                    <button type="button" 
                            @click="toggle()" 
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all flex items-center justify-between text-left gap-1 cursor-pointer">
                        <span class="truncate" :class="selectedValue ? 'font-bold text-slate-900' : 'text-slate-500'" x-text="displayLabel"></span>
                        <div class="flex items-center gap-1 shrink-0">
                            <span x-show="selectedValue" @click="clear($event)" class="text-slate-400 hover:text-rose-500 p-0.5 rounded transition" title="Hapus pilihan">
                                <i class="fa-solid fa-xmark text-[10px]"></i>
                            </span>
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                        </div>
                    </button>

                    <!-- Dropdown Search Menu -->
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-1"
                         class="absolute left-0 right-0 top-full mt-1.5 z-50 bg-white rounded-xl shadow-2xl border border-slate-200 overflow-hidden min-w-[220px]" 
                         style="display: none;">
                        
                        <!-- Search Box in Dropdown -->
                        <div class="p-2 border-b border-slate-100 bg-slate-50/70">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400">
                                    <i class="fa-solid fa-magnifying-glass text-[11px]"></i>
                                </div>
                                <input type="text" 
                                       x-ref="searchInput" 
                                       x-model="searchQuery" 
                                       @keydown.escape="open = false" 
                                       :placeholder="searchPlaceholder" 
                                       class="w-full pl-7 pr-7 py-1.5 bg-white border border-slate-200 rounded-lg text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/30">
                                <button type="button" x-show="searchQuery" @click="searchQuery = ''; $refs.searchInput.focus()" class="absolute inset-y-0 right-0 pr-2 flex items-center text-slate-400 hover:text-slate-600">
                                    <i class="fa-solid fa-xmark text-[10px]"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Options List -->
                        <div class="max-h-56 overflow-y-auto p-1 text-xs space-y-0.5">
                            <button type="button" 
                                    @click="select('')" 
                                    class="w-full px-2.5 py-1.5 rounded-lg text-left transition flex items-center justify-between font-medium cursor-pointer"
                                    :class="!selectedValue ? 'bg-primary-50 text-primary font-bold' : 'text-slate-600 hover:bg-slate-50'">
                                <span>Semua Kota</span>
                                <i x-show="!selectedValue" class="fa-solid fa-check text-[10px] text-primary"></i>
                            </button>
                            
                            <template x-for="item in filteredOptions" :key="item">
                                <button type="button" 
                                        @click="select(item)" 
                                        class="w-full px-2.5 py-1.5 rounded-lg text-left transition flex items-center justify-between text-xs cursor-pointer"
                                        :class="selectedValue === item ? 'bg-primary/10 text-primary font-bold' : 'text-slate-700 hover:bg-slate-100'">
                                    <span class="truncate" x-text="item"></span>
                                    <i x-show="selectedValue === item" class="fa-solid fa-check text-[10px] text-primary shrink-0 ml-1"></i>
                                </button>
                            </template>

                            <div x-show="filteredOptions.length === 0" class="py-4 text-center text-slate-400 text-xs">
                                <i class="fa-solid fa-inbox text-sm mb-1 block opacity-60"></i>
                                <span>Tidak ada kota cocok</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="lg:col-span-1">
                <button type="submit" class="w-full py-2.5 bg-primary hover:bg-primary/90 text-white rounded-xl text-xs font-bold shadow-md shadow-primary/20 transition-all flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-search"></i>
                    <span>Cari</span>
                </button>
            </div>
        </form>
    </div>

    <!-- JOB LISTINGS HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-1">
        <div>
            <h3 class="text-base font-bold text-slate-800">
                Daftar Lowongan Tersedia (<span class="text-primary font-black">{{ $jobs->total() }}</span>)
            </h3>
            <p class="text-xs text-slate-500">Pilih lowongan untuk membaca informasi lengkap dan kirimkan lamaran Anda.</p>
        </div>
        
        <div class="text-xs text-slate-400 font-medium">
            Menampilkan {{ $jobs->firstItem() ?? 0 }} - {{ $jobs->lastItem() ?? 0 }} dari {{ $jobs->total() }} posisi
        </div>
    </div>

    <!-- JOB CARDS GRID -->
    @if($jobs->isEmpty())
        <div class="bg-white rounded-2xl border border-slate-200/80 p-12 text-center shadow-sm">
            <div class="w-16 h-16 bg-blue-50 text-primary rounded-2xl flex items-center justify-center text-3xl mx-auto mb-3">
                <i class="fa-solid fa-briefcase"></i>
            </div>
            <h4 class="text-base font-bold text-slate-800 mb-1">Tidak Ada Lowongan Ditemukan</h4>
            <p class="text-xs text-slate-500 max-w-md mx-auto mb-4">
                Tidak ada lowongan yang cocok dengan kriteria pencarian Anda. Silakan coba atur ulang filter pencarian Anda.
            </p>
            <a href="{{ route('job.public') }}" class="btn-att-primary text-xs inline-flex items-center gap-2">
                <i class="fa-solid fa-rotate-left"></i>
                <span>Tampilkan Semua Lowongan</span>
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @php
                $colorThemes = [
                    ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-600', 'border' => 'border-indigo-100', 'icon' => 'fa-briefcase'],
                    ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'border' => 'border-emerald-100', 'icon' => 'fa-laptop-code'],
                    ['bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'border' => 'border-amber-100', 'icon' => 'fa-chart-line'],
                    ['bg' => 'bg-purple-50', 'text' => 'text-purple-600', 'border' => 'border-purple-100', 'icon' => 'fa-user-tie'],
                    ['bg' => 'bg-sky-50', 'text' => 'text-sky-600', 'border' => 'border-sky-100', 'icon' => 'fa-building-columns'],
                    ['bg' => 'bg-rose-50', 'text' => 'text-rose-600', 'border' => 'border-rose-100', 'icon' => 'fa-bullhorn'],
                ];
            @endphp

            @foreach($jobs as $index => $item)
                @php
                    $theme = $colorThemes[$index % count($colorThemes)];
                    $skills = $item->skills_array;
                    $cleanDesc = $item->snippet_desc;
                @endphp
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm hover:shadow-md hover:border-primary/40 transition-all duration-200 flex flex-col justify-between group">
                    <div>
                        <!-- Top Row: Icon + Bookmark + Area Tag -->
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div class="w-12 h-12 rounded-xl {{ $theme['bg'] }} {{ $theme['border'] }} {{ $theme['text'] }} border flex items-center justify-center text-xl shrink-0 group-hover:scale-105 transition-transform">
                                <i class="fa-solid {{ $theme['icon'] }}"></i>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200/60">
                                    <i class="fa-solid fa-location-dot text-rose-500 mr-1"></i>{{ $item->job_area ?? 'NASIONAL' }}
                                </span>
                                <button type="button" class="w-8 h-8 rounded-full bg-slate-50 hover:bg-slate-100 text-slate-400 hover:text-amber-500 transition-colors flex items-center justify-center text-xs" title="Simpan Lowongan">
                                    <i class="fa-regular fa-bookmark"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Job Title -->
                        <h4 class="text-base font-black text-slate-800 group-hover:text-primary transition-colors line-clamp-1 mb-1">
                            <a href="{{ route('job.detail', $item->id) }}">{{ $item->job_title }}</a>
                        </h4>

                        <!-- Meta: Principle & City -->
                        <div class="flex items-center flex-wrap gap-x-3 gap-y-1 text-xs text-slate-400 mb-3">
                            <span class="flex items-center gap-1">
                                <i class="fa-solid fa-building text-slate-400"></i>
                                <span class="text-slate-600 font-medium">{{ $item->job_prinsiple ?? 'ESA Groups' }}</span>
                            </span>
                            @if(!empty($item->city))
                                <span class="flex items-center gap-1">
                                    <i class="fa-solid fa-city text-slate-400"></i>
                                    <span class="text-slate-600 font-medium">{{ $item->city }}</span>
                                </span>
                            @endif
                        </div>

                        <!-- Description Snippet -->
                        <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed mb-4 min-h-[36px]">
                            {{ $cleanDesc }}
                        </p>

                        <!-- Pelamar Counter -->
                        <div class="flex items-center gap-1.5 text-xs font-semibold text-primary mb-3">
                            <i class="fa-solid fa-user-group text-xs"></i>
                            <span>
                                @if($item->applicant_count > 0)
                                    {{ $item->applicant_count }} Pelamar Terdaftar
                                @else
                                    <span class="text-emerald-600">Jadilah Pelamar Pertama!</span>
                                @endif
                            </span>
                        </div>

                        <!-- Skills Badges -->
                        <div class="flex flex-wrap gap-1 mb-4">
                            @if(count($skills) > 0)
                                @foreach(array_slice($skills, 0, 3) as $sk)
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-100 text-slate-700 border border-slate-200/50">
                                        {{ $sk }}
                                    </span>
                                @endforeach
                                @if(count($skills) > 3)
                                    <span class="px-1.5 py-0.5 rounded-md text-[10px] font-bold bg-slate-50 text-slate-500">
                                        +{{ count($skills) - 3 }}
                                    </span>
                                @endif
                            @else
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                    Full Time
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Bottom Actions -->
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2 mt-auto">
                        <a href="{{ route('job.detail', $item->id) }}" class="text-xs font-bold text-slate-600 hover:text-primary transition-colors flex items-center gap-1">
                            <span>Detail</span>
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>

                        <a href="{{ route('job.apply', $item->id) }}" class="px-3.5 py-1.5 rounded-xl bg-primary hover:bg-primary/90 text-white font-bold text-xs shadow-sm shadow-primary/20 hover:shadow-md transition-all flex items-center gap-1.5">
                            <i class="fa-solid fa-paper-plane text-[10px]"></i>
                            <span>Lamar</span>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="pt-4">
            {{ $jobs->links() }}
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    function searchableSelect(config) {
        return {
            open: false,
            name: config.name,
            placeholder: config.placeholder,
            searchPlaceholder: config.searchPlaceholder || 'Ketik untuk mencari...',
            selectedValue: config.selected || '',
            searchQuery: '',
            options: config.options || [],

            get displayLabel() {
                if (!this.selectedValue) return this.placeholder;
                return this.selectedValue;
            },

            get filteredOptions() {
                if (!this.searchQuery || !this.searchQuery.trim()) {
                    return this.options;
                }
                const q = this.searchQuery.toLowerCase();
                return this.options.filter(opt => (opt + '').toLowerCase().includes(q));
            },

            toggle() {
                this.open = !this.open;
                if (this.open) {
                    this.searchQuery = '';
                    this.$nextTick(() => {
                        if (this.$refs.searchInput) {
                            this.$refs.searchInput.focus();
                        }
                    });
                }
            },

            select(val) {
                this.selectedValue = val;
                this.open = false;
                this.searchQuery = '';
            },

            clear(e) {
                if (e) e.stopPropagation();
                this.selectedValue = '';
                this.searchQuery = '';
                this.open = false;
            }
        }
    }
</script>
@endsection
