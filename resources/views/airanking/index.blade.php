@extends('layouts.app')

@section('title', 'AI Candidate Ranking & Leaderboard - ASystem Support System')

@section('content')
<div class="space-y-6">
    <!-- PAGE HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-primary mb-1">
                <i class="fa-solid fa-ranking-star text-amber-500"></i>
                <span>Fitur & Layanan &bull; AI Candidate Ranking</span>
            </div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight">AI Candidate Ranking & Leaderboard</h1>
            <p class="text-xs text-slate-500 mt-0.5">Peringkat pelamar terbaik hasil analisa AI CV Match Score berdasarkan kualifikasi dan kompetensi posisi.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('kandidatportal.index') }}" class="btn-att-secondary text-xs">
                <i class="fa-solid fa-globe text-primary"></i>
                <span>Kandidat Portal</span>
            </a>
            <a href="{{ route('job.input') }}" class="btn-att-secondary text-xs">
                <i class="fa-solid fa-briefcase text-slate-600"></i>
                <span>Input Job Requirement</span>
            </a>
        </div>
    </div>

    <!-- METRIC CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Total Dianalisa AI -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex items-center gap-4 hover:shadow-md transition-all">
            <div class="w-12 h-12 rounded-xl bg-blue-50 border border-blue-100 text-primary flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-brain"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Dianalisis AI</p>
                <p class="text-2xl font-black text-slate-800">{{ $totalAnalyzed }}</p>
                <span class="text-[11px] font-semibold text-blue-600">Pelamar Terverifikasi AI</span>
            </div>
        </div>

        <!-- 2. Top Match Candidate -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex items-center gap-4 hover:shadow-md transition-all">
            <div class="w-12 h-12 rounded-xl bg-amber-50 border border-amber-100 text-amber-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-trophy"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Top Match Score</p>
                <p class="text-2xl font-black text-amber-600">{{ $topCandidate ? $topCandidate->ai_score . '%' : '-' }}</p>
                <p class="text-[11px] font-bold text-slate-700 truncate" title="{{ $topCandidate?->full_name }}">
                    {{ $topCandidate ? $topCandidate->full_name : 'Belum ada data' }}
                </p>
            </div>
        </div>

        <!-- 3. Rata-rata Skor Match -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex items-center gap-4 hover:shadow-md transition-all">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-chart-line"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Rata-rata Skor Match</p>
                <p class="text-2xl font-black text-slate-800">{{ $avgScore }}%</p>
                <span class="text-[11px] font-semibold text-emerald-600">Standar Kualitas CV</span>
            </div>
        </div>

        <!-- 4. Highly Recommended (>= 85%) -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex items-center gap-4 hover:shadow-md transition-all">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-star"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Kandidat Hijau (≥85%)</p>
                <p class="text-2xl font-black text-indigo-600">{{ $countGreen }}</p>
                <span class="text-[11px] font-semibold text-indigo-600">Sangat Direkomendasikan</span>
            </div>
        </div>
    </div>

    @if($podiumCandidates->count() >= 3 && empty($search) && $candidates->currentPage() == 1)
    <!-- PODIUM TOP 3 CANDIDATES SHOWCASE -->
    <div class="bg-gradient-to-br from-slate-900 via-primary-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden border border-slate-800">
        <!-- Ambient Glow -->
        <div class="absolute -top-24 -left-24 w-72 h-72 bg-primary-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -right-24 w-72 h-72 bg-amber-500/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="text-center max-w-xl mx-auto mb-8 relative z-10">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/20 border border-amber-400/30 text-amber-300 text-xs font-bold mb-2">
                <i class="fa-solid fa-crown text-amber-400"></i>
                TOP CANDIDATE SHOWCASE
            </span>
            <h2 class="text-xl sm:text-2xl font-black tracking-tight">Pelamar Terbaik Pilihan AI</h2>
            <p class="text-xs text-slate-300 mt-1">
                {{ $selectedJob ? "Posisi: {$selectedJob}" : "Peringkat 3 besar pelamar dengan CV Match Score tertinggi di seluruh posisi." }}
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end max-w-4xl mx-auto relative z-10">
            <!-- RANK 2: SILVER (Left) -->
            @if(isset($podiumCandidates[1]))
            @php $c2 = $podiumCandidates[1]; @endphp
            <div class="order-2 md:order-1 bg-white/10 backdrop-blur-md border border-white/15 rounded-2xl p-5 text-center flex flex-col items-center hover:bg-white/15 transition-all">
                <div class="relative mb-3">
                    <img src="{{ $c2->photo_path ?: 'https://ui-avatars.com/api/?name='.urlencode($c2->full_name).'&background=64748B&color=fff' }}" 
                         alt="{{ $c2->full_name }}" 
                         class="w-16 h-16 rounded-2xl object-cover border-2 border-slate-300 shadow-md">
                    <div class="absolute -top-2 -right-2 w-7 h-7 rounded-full bg-gradient-to-tr from-slate-400 to-slate-200 text-slate-800 text-xs font-black flex items-center justify-center shadow-md border-2 border-slate-800">
                        🥈
                    </div>
                </div>
                <h3 class="font-bold text-sm text-white truncate max-w-full" title="{{ $c2->full_name }}">{{ $c2->full_name }}</h3>
                <p class="text-[11px] text-slate-300 truncate max-w-full mb-3">{{ $c2->applied_job }}</p>
                <div class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-slate-300/20 text-slate-200 text-xs font-extrabold border border-slate-300/30 mb-3">
                    <i class="fa-solid fa-star text-slate-300"></i>
                    <span>{{ $c2->ai_score }}% Match</span>
                </div>
                <a href="{{ route('kandidatportal.show', $c2->id) }}" class="w-full py-1.5 px-3 rounded-xl bg-white/15 hover:bg-white/25 text-white text-xs font-bold transition-all flex items-center justify-center gap-1.5 border border-white/20">
                    <span>Lihat Profil</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>
            @endif

            <!-- RANK 1: GOLD (Center, Taller) -->
            @if(isset($podiumCandidates[0]))
            @php $c1 = $podiumCandidates[0]; @endphp
            <div class="order-1 md:order-2 bg-gradient-to-b from-amber-500/20 to-amber-600/10 backdrop-blur-md border-2 border-amber-400/50 rounded-2xl p-6 text-center flex flex-col items-center hover:border-amber-400 transition-all shadow-2xl relative">
                <div class="absolute -top-3 left-1/2 -translate-x-1/2 px-3 py-0.5 rounded-full bg-gradient-to-r from-amber-500 to-yellow-400 text-slate-900 text-[10px] font-black uppercase tracking-wider shadow-md">
                    #1 HIGHEST MATCH
                </div>
                <div class="relative my-3">
                    <img src="{{ $c1->photo_path ?: 'https://ui-avatars.com/api/?name='.urlencode($c1->full_name).'&background=F59E0B&color=fff' }}" 
                         alt="{{ $c1->full_name }}" 
                         class="w-20 h-20 rounded-2xl object-cover border-2 border-amber-300 shadow-xl ring-4 ring-amber-400/20">
                    <div class="absolute -top-2.5 -right-2.5 w-8 h-8 rounded-full bg-gradient-to-tr from-amber-500 to-yellow-300 text-slate-900 text-sm font-black flex items-center justify-center shadow-lg border-2 border-slate-900">
                        🥇
                    </div>
                </div>
                <h3 class="font-extrabold text-base text-white truncate max-w-full" title="{{ $c1->full_name }}">{{ $c1->full_name }}</h3>
                <p class="text-xs text-amber-200/90 truncate max-w-full mb-3 font-medium">{{ $c1->applied_job }}</p>
                <div class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-amber-400 text-slate-900 text-xs font-black shadow-md mb-4">
                    <i class="fa-solid fa-crown text-slate-900"></i>
                    <span>{{ $c1->ai_score }}% Match</span>
                </div>
                <a href="{{ route('kandidatportal.show', $c1->id) }}" class="w-full py-2 px-3 rounded-xl bg-gradient-to-r from-amber-400 to-yellow-400 hover:from-amber-300 hover:to-yellow-300 text-slate-900 text-xs font-black transition-all flex items-center justify-center gap-1.5 shadow-md">
                    <span>Lihat Profil Utama</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>
            @endif

            <!-- RANK 3: BRONZE (Right) -->
            @if(isset($podiumCandidates[2]))
            @php $c3 = $podiumCandidates[2]; @endphp
            <div class="order-3 md:order-3 bg-white/10 backdrop-blur-md border border-white/15 rounded-2xl p-5 text-center flex flex-col items-center hover:bg-white/15 transition-all">
                <div class="relative mb-3">
                    <img src="{{ $c3->photo_path ?: 'https://ui-avatars.com/api/?name='.urlencode($c3->full_name).'&background=B45309&color=fff' }}" 
                         alt="{{ $c3->full_name }}" 
                         class="w-16 h-16 rounded-2xl object-cover border-2 border-amber-700/60 shadow-md">
                    <div class="absolute -top-2 -right-2 w-7 h-7 rounded-full bg-gradient-to-tr from-amber-700 to-amber-500 text-white text-xs font-black flex items-center justify-center shadow-md border-2 border-slate-800">
                        🥉
                    </div>
                </div>
                <h3 class="font-bold text-sm text-white truncate max-w-full" title="{{ $c3->full_name }}">{{ $c3->full_name }}</h3>
                <p class="text-[11px] text-slate-300 truncate max-w-full mb-3">{{ $c3->applied_job }}</p>
                <div class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-amber-700/30 text-amber-200 text-xs font-extrabold border border-amber-600/30 mb-3">
                    <i class="fa-solid fa-star text-amber-400"></i>
                    <span>{{ $c3->ai_score }}% Match</span>
                </div>
                <a href="{{ route('kandidatportal.show', $c3->id) }}" class="w-full py-1.5 px-3 rounded-xl bg-white/15 hover:bg-white/25 text-white text-xs font-bold transition-all flex items-center justify-center gap-1.5 border border-white/20">
                    <span>Lihat Profil</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- MAIN LEADERBOARD CONTAINER -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        
        <!-- FILTER & SEARCH BAR -->
        <div class="p-4 border-b border-slate-200/80 flex flex-col lg:flex-row items-center justify-between gap-4 bg-slate-50/60">
            <!-- Left: Filter by Jabatan & Tier -->
            <form action="{{ route('airanking.index') }}" method="GET" class="flex flex-wrap items-center gap-2.5 w-full lg:w-auto">
                @if($search)
                    <input type="hidden" name="search" value="{{ $search }}">
                @endif

                <!-- Jabatan Select -->
                <div class="w-full sm:w-64">
                    <select name="job" onchange="this.form.submit()" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs font-bold text-slate-800 focus:ring-2 focus:ring-primary-100 focus:border-primary outline-none">
                        <option value="">Semua Jabatan Dilamar ({{ $availableJobs->count() }} Posisi)</option>
                        @foreach($availableJobs as $j)
                            <option value="{{ $j }}" {{ $selectedJob === $j ? 'selected' : '' }}>
                                {{ $j }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Tier Filter Pills -->
                <div class="flex items-center gap-1.5">
                    <a href="{{ route('airanking.index', ['job' => $selectedJob, 'search' => $search]) }}" 
                       class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ empty($selectedTier) ? 'bg-primary text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                        Semua
                    </a>
                    <a href="{{ route('airanking.index', ['job' => $selectedJob, 'tier' => 'green', 'search' => $search]) }}" 
                       class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $selectedTier === 'green' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                        Hijau (≥85%)
                    </a>
                    <a href="{{ route('airanking.index', ['job' => $selectedJob, 'tier' => 'yellow', 'search' => $search]) }}" 
                       class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $selectedTier === 'yellow' ? 'bg-amber-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                        Kuning (60-84%)
                    </a>
                    <a href="{{ route('airanking.index', ['job' => $selectedJob, 'tier' => 'red', 'search' => $search]) }}" 
                       class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $selectedTier === 'red' ? 'bg-rose-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                        Merah (<60%)
                    </a>
                </div>
            </form>

            <!-- Search Form -->
            <form action="{{ route('airanking.index') }}" method="GET" class="flex items-center gap-2 w-full lg:w-72">
                @if($selectedJob)
                    <input type="hidden" name="job" value="{{ $selectedJob }}">
                @endif
                @if($selectedTier)
                    <input type="hidden" name="tier" value="{{ $selectedTier }}">
                @endif
                <div class="relative w-full">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" name="search" value="{{ $search }}" 
                           placeholder="Cari kandidat di ranking..." 
                           class="w-full pl-9 pr-8 py-2 text-xs rounded-xl border border-slate-300 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary-100 bg-white">
                    @if($search)
                        <a href="{{ route('airanking.index', ['job' => $selectedJob, 'tier' => $selectedTier]) }}" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                            <i class="fa-solid fa-xmark text-xs"></i>
                        </a>
                    @endif
                </div>
                <button type="submit" class="btn-att-primary text-xs px-3 py-2 shrink-0">
                    Cari
                </button>
            </form>
        </div>

        <!-- INFO NOTICE BANNER -->
        <div class="px-5 py-2.5 bg-blue-50/50 border-b border-blue-100 text-[11px] text-blue-700 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-info text-primary"></i>
                <span>Hanya kandidat yang telah dianalisa oleh AI yang akan muncul pada daftar peringkat ini.</span>
            </div>
            @if($selectedJob)
            <span class="font-bold">Filter Posisi: {{ $selectedJob }}</span>
            @endif
        </div>

        <!-- LEADERBOARD DATA TABLE -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-3.5 text-center w-14">Rank</th>
                        <th class="py-3 px-3.5">Nama Pelamar</th>
                        <th class="py-3 px-3.5">Jabatan Dilamar</th>
                        <th class="py-3 px-3.5 w-48">AI Match Score</th>
                        <th class="py-3 px-3.5">Key Strengths & Rekomendasi</th>
                        <th class="py-3 px-3.5 text-center w-28">Status</th>
                        <th class="py-3 px-3.5 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-xs">
                    @forelse($candidates as $index => $c)
                        @php
                            $globalRank = ($candidates->currentPage() - 1) * $candidates->perPage() + $index + 1;
                            $score = intval($c->ai_score ?? 0);
                            $badgeClass = match(true) {
                                $score >= 85 => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                $score >= 60 => 'bg-amber-50 text-amber-700 border-amber-200',
                                default => 'bg-rose-50 text-rose-700 border-rose-200'
                            };
                            $barColor = match(true) {
                                $score >= 85 => 'bg-emerald-500',
                                $score >= 60 => 'bg-amber-500',
                                default => 'bg-rose-500'
                            };
                            $aiData = $c->ai_data;
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors {{ $globalRank <= 3 ? 'bg-amber-50/20' : '' }}">
                            <!-- Rank Column -->
                            <td class="py-3.5 px-3.5 text-center whitespace-nowrap">
                                @if($globalRank == 1)
                                    <div class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-amber-100 text-amber-800 font-black text-sm shadow-xs border border-amber-200" title="Rank 1 (Gold)">
                                        🥇
                                    </div>
                                @elseif($globalRank == 2)
                                    <div class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-slate-200 text-slate-800 font-black text-sm shadow-xs border border-slate-300" title="Rank 2 (Silver)">
                                        🥈
                                    </div>
                                @elseif($globalRank == 3)
                                    <div class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-amber-200/80 text-amber-900 font-black text-sm shadow-xs border border-amber-300" title="Rank 3 (Bronze)">
                                        🥉
                                    </div>
                                @else
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-slate-100 text-slate-600 font-bold text-xs">
                                        #{{ $globalRank }}
                                    </span>
                                @endif
                            </td>

                            <!-- Candidate Profile Column -->
                            <td class="py-3.5 px-3.5">
                                <div class="flex items-center gap-2.5">
                                    <img src="{{ $c->photo_path ?: 'https://ui-avatars.com/api/?name='.urlencode($c->full_name).'&background=0F52BA&color=fff' }}" 
                                         alt="{{ $c->full_name }}" 
                                         class="w-9 h-9 rounded-xl object-cover border border-slate-200 shadow-2xs">
                                    <div class="min-w-0">
                                        <a href="{{ route('kandidatportal.show', $c->id) }}" class="font-bold text-slate-900 hover:text-primary transition-colors block truncate">
                                            {{ $c->full_name }}
                                        </a>
                                        <div class="flex items-center gap-2 text-[11px] text-slate-400 font-mono">
                                            <span>{{ $c->nik }}</span>
                                            <span>&bull;</span>
                                            <span>{{ $c->age }} Thn</span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Applied Job Column -->
                            <td class="py-3.5 px-3.5">
                                <span class="font-bold text-slate-800 block text-xs">{{ $c->applied_job }}</span>
                                <span class="text-[11px] text-slate-400">{{ $c->area ?: 'JAKARTA' }}</span>
                            </td>

                            <!-- AI Match Score Column -->
                            <td class="py-3.5 px-3.5">
                                <div class="space-y-1.5">
                                    <div class="flex items-center justify-between">
                                        <span class="px-2 py-0.5 rounded text-[11px] font-black border {{ $badgeClass }}">
                                            <i class="fa-solid fa-star text-[10px] mr-0.5"></i>
                                            {{ $score }}% Match
                                        </span>
                                        <span class="text-[10px] font-bold {{ $score >= 85 ? 'text-emerald-600' : ($score >= 60 ? 'text-amber-600' : 'text-rose-600') }}">
                                            {{ $score >= 85 ? 'Sangat Cocok' : ($score >= 60 ? 'Cukup Cocok' : 'Kurang Cocok') }}
                                        </span>
                                    </div>
                                    <!-- Visual Progress Bar -->
                                    <div class="w-full h-2 rounded-full bg-slate-100 overflow-hidden">
                                        <div class="h-full rounded-full {{ $barColor }} transition-all duration-500" style="width: {{ $score }}%;"></div>
                                    </div>
                                </div>
                            </td>

                            <!-- Key Strengths Column -->
                            <td class="py-3.5 px-3.5">
                                @if(!empty($aiData['key_strengths']) && is_array($aiData['key_strengths']))
                                    <div class="flex flex-wrap gap-1 max-w-sm">
                                        @foreach(array_slice($aiData['key_strengths'], 0, 3) as $st)
                                            <span class="text-[10px] bg-slate-100 text-slate-700 font-medium px-2 py-0.5 rounded border border-slate-200">
                                                {{ $st }}
                                            </span>
                                        @endforeach
                                    </div>
                                @elseif(!empty($aiData['executive_summary']))
                                    <p class="text-[11px] text-slate-600 line-clamp-2 leading-relaxed max-w-sm">
                                        {{ $aiData['executive_summary'] }}
                                    </p>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">Kualifikasi memenuhi kriteria dasar formasi</span>
                                @endif
                            </td>

                            <!-- Status Seleksi Column -->
                            <td class="py-3.5 px-3.5 text-center whitespace-nowrap">
                                <span class="badge-pill {{ match($c->status_kandidat ?? $c->status) {
                                    'Terima' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'Interview' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'Arsip' => 'bg-rose-50 text-rose-700 border-rose-200',
                                    default => 'bg-slate-100 text-slate-700 border-slate-200'
                                } }}">
                                    {{ $c->status_kandidat ?? $c->status ?? 'Baru' }}
                                </span>
                            </td>

                            <!-- Action Column -->
                            <td class="py-3.5 px-3.5 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="https://api.whatsapp.com/send?phone={{ $c->clean_whatsapp }}" target="_blank" 
                                       class="w-7 h-7 rounded-lg bg-emerald-50 hover:bg-emerald-600 text-emerald-600 hover:text-white border border-emerald-200 transition-colors flex items-center justify-center text-xs"
                                       title="Hubungi via WhatsApp">
                                        <i class="fa-brands fa-whatsapp"></i>
                                    </a>
                                    <a href="{{ route('kandidatportal.show', $c->id) }}" 
                                       class="btn-att-primary text-xs py-1 px-2.5 flex items-center gap-1">
                                        <span>Detail</span>
                                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fa-solid fa-trophy text-3xl mb-2 text-slate-300"></i>
                                    <p class="font-semibold text-slate-600 text-sm">Belum ada kandidat yang direkomendasikan AI untuk kriteria ini</p>
                                    <p class="text-xs text-slate-400 mt-1">Coba ubah filter jabatan atau analisa CV pelamar terlebih dahulu.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        @if($candidates->hasPages())
            <div class="p-4 border-t border-slate-200 bg-slate-50/50">
                {{ $candidates->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
