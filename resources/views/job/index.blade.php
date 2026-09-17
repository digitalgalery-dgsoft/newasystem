@extends('layouts.app')

@section('title', 'Portal Lowongan Kerja - ASystem Support System')

@section('content')
<div class="space-y-6">
    <!-- HERO BANNER -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 text-white p-6 sm:p-10 shadow-xl border border-blue-800/40">
        <!-- Ambient background circles -->
        <div class="absolute -right-10 -bottom-10 w-72 h-72 bg-blue-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute right-1/3 -top-10 w-60 h-60 bg-indigo-500/20 rounded-full blur-2xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-8">
            <div class="max-w-2xl space-y-4">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-200 text-xs font-semibold backdrop-blur-md">
                    <i class="fa-solid fa-sparkles text-amber-400"></i>
                    <span>Official Career Portal &bull; ESA Groups Support System</span>
                </div>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight leading-tight text-white">
                    Find Your Dream Job <br class="hidden sm:inline"><span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-sky-300 to-indigo-300">With Us Today</span>
                </h1>
                <p class="text-sm sm:text-base text-slate-300 font-normal leading-relaxed max-w-xl">
                    Jelajahi berbagai peluang karier terbaik di seluruh cabang dan unit bisnis kami. Daftarkan diri Anda sekarang dan manfaatkan penilaian kesesuaian AI kami.
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
                    <div class="text-xs text-slate-300 font-medium">Smart Screening</div>
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

            <!-- Jabatan Filter -->
            <div class="lg:col-span-3">
                <select name="job" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                    <option value="">Semua Jabatan</option>
                    @foreach($distinctJobs as $jab)
                        <option value="{{ $jab }}" {{ $selectedJob == $jab ? 'selected' : '' }}>{{ $jab }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Area Filter -->
            <div class="lg:col-span-2">
                <select name="area" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                    <option value="">Semua Area</option>
                    @foreach($distinctAreas as $ar)
                        <option value="{{ $ar }}" {{ $selectedArea == $ar ? 'selected' : '' }}>{{ $ar }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Kota Filter -->
            <div class="lg:col-span-2">
                <select name="city" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                    <option value="">Semua Kota</option>
                    @foreach($distinctCities as $cty)
                        <option value="{{ $cty }}" {{ $selectedCity == $cty ? 'selected' : '' }}>{{ $cty }}</option>
                    @endforeach
                </select>
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
            <p class="text-xs text-slate-500">Pilih lowongan untuk membaca detail lengkap dan kirimkan lamaran Anda.</p>
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
                    $skills = array_filter(array_map('trim', explode(',', strip_tags($item->job_skills ?? ''))));
                    $cleanDesc = strip_tags($item->job_desc ?? ($item->kualifikasi ?? ''));
                    if (empty($cleanDesc)) {
                        $cleanDesc = 'Memiliki dedikasi kerja tinggi, mampu beradaptasi dan berkembang bersama perusahaan.';
                    }
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
