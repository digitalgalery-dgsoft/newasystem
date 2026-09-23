@extends('layouts.app')

@section('title', 'Log Antrean & Hasil Analisa AI - Kandidat Portal')

@section('content')
<style>
    @keyframes tickerAnimation {
        0% { transform: translate3d(0, 0, 0); }
        100% { transform: translate3d(-50%, 0, 0); }
    }
    .animate-ticker-track {
        display: inline-flex;
        animation: tickerAnimation 35s linear infinite;
    }
    .animate-ticker-track:hover {
        animation-play-state: paused;
    }
    .mask-ticker {
        mask-image: linear-gradient(to right, transparent, black 15px, black calc(100% - 15px), transparent);
        -webkit-mask-image: linear-gradient(to right, transparent, black 15px, black calc(100% - 15px), transparent);
    }
</style>

<div class="space-y-6" 
     x-data="aiQueueLogManager({
         queueCount: {{ $queue_count ?? 0 }},
         completedCount: {{ $completed_count ?? 0 }},
         greenCount: {{ $green_count ?? 0 }},
         yellowCount: {{ $yellow_count ?? 0 }},
         redCount: {{ $red_count ?? 0 }},
         queueList: @js($queue_list ?? []),
         completedList: @js($completed_list ?? []),
         liveStatus: @js($live_status ?? [])
     })"
     x-init="init()">

    <!-- TOP BAR / BREADCRUMB & HEADER -->
    <div class="page-header-card flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('kandidatportal.index') }}" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition-all shadow-xs" title="Kembali ke Kandidat Portal">
                    <i class="fa-solid fa-arrow-left text-sm"></i>
                </a>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                            <i class="fa-solid fa-brain text-indigo-600"></i>
                            Log Antrean & Hasil Analisa AI
                        </h1>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span>
                            </span>
                            Live Realtime
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">
                        Monitoring antrean proses analisa CV otomatis dan riwayat kandidat yang baru selesai dinilai oleh AI.
                    </p>
                </div>
            </div>
        </div>

        <!-- ACTION BUTTONS & AUTO-RELOAD CONTROLS -->
        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Auto Reload Status & Timer -->
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-100 border border-slate-200 text-xs">
                <button type="button" 
                        @click="toggleAutoReload()" 
                        class="flex items-center gap-1.5 font-bold transition cursor-pointer"
                        :class="autoReload ? 'text-emerald-700' : 'text-slate-500'">
                    <span class="relative flex h-2 w-2">
                        <span x-show="autoReload" class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2" :class="autoReload ? 'bg-emerald-500' : 'bg-slate-400'"></span>
                    </span>
                    <span x-text="autoReload ? 'Auto Reload: ON' : 'Auto Reload: OFF'"></span>
                </button>
                <span class="text-slate-300">|</span>
                <span class="text-[11px] font-mono text-slate-600 flex items-center gap-1" title="Hitung mundur refresh realtime berikutnya">
                    <i class="fa-regular fa-clock text-[10px] text-slate-400"></i>
                    <span x-text="autoReload ? countdown + 's' : 'Jeda'"></span>
                </span>
            </div>

            <!-- Tombol Manual Refresh -->
            <button type="button" 
                    @click="manualRefresh()" 
                    :disabled="isLoading"
                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 shadow-sm transition-all cursor-pointer disabled:opacity-50">
                <i class="fa-solid fa-arrows-rotate text-indigo-600" :class="{ 'fa-spin': isLoading }"></i>
                <span x-text="isLoading ? 'Memperbarui...' : 'Refresh Sekarang'"></span>
            </button>

            <!-- Link Navigasi Cepat -->
            <a href="{{ route('kandidatportal.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 shadow-sm transition-all">
                <i class="fa-solid fa-globe text-primary"></i>
                <span>Kandidat Portal</span>
            </a>

            <a href="{{ route('airanking.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200 shadow-sm transition-all">
                <i class="fa-solid fa-ranking-star text-amber-600"></i>
                <span>AI Leaderboard</span>
            </a>
        </div>
    </div>

    <!-- AI LIVE RUNNING TEXT / REALTIME TICKER BAR -->
    <div class="relative overflow-hidden rounded-2xl border border-indigo-200/80 bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white shadow-md transition-all select-none"
         @mouseenter="paused = true"
         @mouseleave="paused = false">
        
        <div class="flex items-center">
            <!-- Badge Kiri (Live Indicator) -->
            <div class="flex items-center gap-2 px-3.5 py-2.5 bg-indigo-950/95 border-r border-indigo-500/30 flex-shrink-0 z-10 shadow-sm backdrop-blur-sm">
                <span class="relative flex h-2.5 w-2.5">
                    <span :class="isProcessing ? 'animate-ping bg-emerald-400 opacity-75' : 'bg-slate-400 opacity-20'" class="absolute inline-flex h-full w-full rounded-full"></span>
                    <span :class="isProcessing ? 'bg-emerald-500 shadow-sm shadow-emerald-400/50' : 'bg-amber-400'" class="relative inline-flex rounded-full h-2.5 w-2.5"></span>
                </span>
                <div class="flex items-center gap-1.5">
                    <i class="fa-solid fa-bolt text-indigo-300 text-xs"></i>
                    <span class="text-[11px] font-black tracking-wider uppercase bg-gradient-to-r from-indigo-200 via-white to-indigo-100 bg-clip-text text-transparent">AI Live Status</span>
                </div>
                <span :class="isProcessing ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40 shadow-xs shadow-emerald-500/20' : 'bg-amber-500/20 text-amber-300 border-amber-500/40'" 
                      class="text-[9px] font-black uppercase px-2 py-0.5 rounded-full border tracking-wider flex items-center gap-1">
                    <i class="fa-solid fa-spinner fa-spin text-[8px]" x-show="isProcessing"></i>
                    <span x-text="isProcessing ? 'PROSES' : 'STANDBY'"></span>
                </span>
            </div>

            <!-- Tengah: Running Text (Marquee Ticker) -->
            <div class="flex-1 overflow-hidden py-2.5 px-2 relative mask-ticker">
                <div class="inline-flex whitespace-nowrap animate-ticker-track" :style="paused ? 'animation-play-state: paused;' : ''">
                    <!-- Loop dua kali untuk efek infinite loop tanpa jeda -->
                    <template x-for="copy in 2" :key="copy">
                        <div class="inline-flex items-center gap-6 text-xs font-medium text-slate-200 pr-8">
                            
                            <!-- Item 1: Sedang Dianalisis -->
                            <template x-if="isProcessing && current">
                                <div class="inline-flex items-center gap-2 bg-gradient-to-r from-amber-500/25 to-orange-500/25 px-3 py-1 rounded-xl border border-amber-500/50 text-amber-200 shadow-xs">
                                    <i class="fa-solid fa-spinner fa-spin text-amber-400 text-xs"></i>
                                    <span class="font-bold text-amber-300 uppercase tracking-wide text-[10px]">Sedang Dianalisis:</span>
                                    <strong class="text-white font-extrabold" x-text="current.candidate_name"></strong>
                                    <span class="text-amber-100/90 text-[11px]" x-text="'(' + (current.applied_job || '-') + ' • ' + (current.area || '-') + ')'"></span>
                                    <span class="text-[10px] text-amber-300 font-mono" x-text="'[Mulai: ' + (current.formatted_time || '') + ']'"></span>
                                </div>
                            </template>

                            <template x-if="!isProcessing">
                                <div class="inline-flex items-center gap-2 bg-indigo-500/15 px-3 py-1 rounded-xl border border-indigo-500/30 text-indigo-200">
                                    <i class="fa-solid fa-circle-check text-indigo-400 text-xs"></i>
                                    <span class="text-slate-300 font-semibold text-[11px]" x-text="current?.status_text || 'Sistem siap memproses antrean berikutnya'"></span>
                                </div>
                            </template>

                            <span class="text-indigo-400/40">•</span>

                            <!-- Item 2: Kecepatan Proses -->
                            <div class="inline-flex items-center gap-1.5 text-cyan-300 bg-cyan-500/10 px-2.5 py-1 rounded-xl border border-cyan-500/20">
                                <i class="fa-solid fa-gauge-high text-cyan-400 text-xs"></i>
                                <span class="text-slate-300">Kecepatan:</span>
                                <strong class="text-cyan-200 font-bold">1 kandidat per 30 detik (1 menit 2 kandidat)</strong>
                            </div>

                            <span class="text-indigo-400/40">•</span>

                            <!-- Item 3: Total Antrean Menunggu -->
                            <div class="inline-flex items-center gap-1.5 text-purple-300 bg-purple-500/10 px-2.5 py-1 rounded-xl border border-purple-500/20">
                                <i class="fa-solid fa-clock-rotate-left text-purple-400 text-xs"></i>
                                <span class="text-slate-300">Antrean Menunggu:</span>
                                <strong class="text-purple-200 font-black" x-text="queueCount + ' kandidat'"></strong>
                            </div>

                            <span class="text-indigo-400/40">•</span>

                            <!-- Item 4: Kandidat Berikutnya -->
                            <template x-if="nextCandidate">
                                <div class="inline-flex items-center gap-1.5 text-blue-300 bg-blue-500/10 px-2.5 py-1 rounded-xl border border-blue-500/20">
                                    <i class="fa-solid fa-forward text-blue-400 text-xs"></i>
                                    <span class="text-slate-300">Berikutnya:</span>
                                    <strong class="text-blue-100 font-bold" x-text="nextCandidate.full_name"></strong>
                                    <span class="text-slate-400 text-[11px]" x-text="'(' + (nextCandidate.applied_job || '-') + ')'"></span>
                                </div>
                            </template>

                            <template x-if="nextCandidate">
                                <span class="text-indigo-400/40">•</span>
                            </template>

                            <!-- Item 5: Baru Selesai -->
                            <template x-if="lastCompleted && lastCompleted.candidate_name">
                                <div class="inline-flex items-center gap-2 bg-emerald-500/15 px-3 py-1 rounded-xl border border-emerald-500/30 text-emerald-200">
                                    <i class="fa-solid fa-circle-check text-emerald-400 text-xs"></i>
                                    <span class="font-bold text-emerald-300 uppercase tracking-wide text-[10px]">Baru Selesai:</span>
                                    <strong class="text-white font-bold" x-text="lastCompleted.candidate_name"></strong>
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-black uppercase"
                                          :class="lastCompleted.category === 'Green' ? 'bg-emerald-500 text-white' : (lastCompleted.category === 'Yellow' ? 'bg-amber-500 text-white' : 'bg-red-500 text-white')"
                                          x-text="'Score ' + (lastCompleted.score ?? 0) + '% (' + (lastCompleted.category || '-') + ')'"></span>
                                    <span class="text-slate-400 text-[10px]" x-text="'[' + (lastCompleted.formatted_time || '') + ']'"></span>
                                </div>
                            </template>

                            <span class="text-indigo-400/40">•</span>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Badge Kanan: Waktu Terakhir Diperbarui -->
            <div class="hidden md:flex items-center gap-2 px-3.5 py-2.5 bg-indigo-950/95 border-l border-indigo-500/30 flex-shrink-0 text-right z-10 backdrop-blur-sm">
                <div class="text-right">
                    <div class="text-[9px] font-bold text-indigo-300 uppercase tracking-wider">Update Terakhir</div>
                    <div class="text-xs font-mono font-bold text-white" x-text="lastUpdatedTime"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- 4 STAT CARDS -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Total Antrean Menunggu -->
        <div class="stat-box flex items-center gap-4 bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 border border-purple-100 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <div class="min-w-0">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Antrean</div>
                <div class="text-2xl font-black text-slate-900 leading-tight" x-text="queueCount"></div>
                <div class="text-[11px] font-semibold text-purple-600 mt-0.5 truncate flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-purple-500 animate-pulse"></span>
                    Menunggu Analisa
                </div>
            </div>
        </div>

        <!-- 2. Selesai Dianalisa -->
        <div class="stat-box flex items-center gap-4 bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="min-w-0">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Selesai Dianalisa</div>
                <div class="text-2xl font-black text-slate-900 leading-tight" x-text="completedCount"></div>
                <div class="text-[11px] font-semibold text-emerald-600 mt-0.5 truncate">
                    Kandidat Teruji AI
                </div>
            </div>
        </div>

        <!-- 3. Rekomendasi Hijau (Match >= 85%) -->
        <div class="stat-box flex items-center gap-4 bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-award"></i>
            </div>
            <div class="min-w-0">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">High Match (Green)</div>
                <div class="text-2xl font-black text-slate-900 leading-tight" x-text="greenCount"></div>
                <div class="text-[11px] font-semibold text-blue-600 mt-0.5 truncate">
                    Skor Cocok &ge; 85%
                </div>
            </div>
        </div>

        <!-- 4. Kecepatan & Engine -->
        <div class="stat-box flex items-center gap-4 bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-microchip"></i>
            </div>
            <div class="min-w-0">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Engine AI</div>
                <div class="text-lg font-black text-slate-900 leading-tight truncate">Multi-Provider</div>
                <div class="text-[11px] font-semibold text-amber-600 mt-0.5 truncate">
                    Gemini &bull; OpenRouter &bull; Sumo
                </div>
            </div>
        </div>
    </div>

    <!-- TABEL 1: 10 KANDIDAT DALAM ANTREAN ANALISA AI -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <!-- Header Tabel Antrean -->
        <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-gradient-to-r from-slate-50 to-amber-50/30">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center text-lg font-bold">
                    <i class="fa-solid fa-hourglass-half"></i>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                        <span>List Kandidat Sedang dalam Antrean Analisa AI</span>
                        <span class="text-xs px-2 py-0.5 rounded-full font-bold bg-amber-100 text-amber-800" x-text="'10 Terdepan dari ' + queueCount"></span>
                    </h3>
                    <p class="text-xs text-slate-500">
                        Urutan prioritas antrean evaluasi berkas CV oleh background AI worker.
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 text-xs text-slate-500 bg-white px-3 py-1.5 rounded-xl border border-slate-200">
                    <i class="fa-solid fa-gauge text-slate-400"></i>
                    <span>Estimasi: <b>~30 detik / kandidat</b></span>
                </span>
            </div>
        </div>

        <!-- Tabel Antrean -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/70 text-slate-500 font-bold uppercase text-[10px] tracking-wider">
                        <th class="py-3 px-4 text-center w-16">Antrean</th>
                        <th class="py-3 px-4">Tgl Daftar</th>
                        <th class="py-3 px-4">Nama Kandidat</th>
                        <th class="py-3 px-4">Jabatan Dilamar</th>
                        <th class="py-3 px-4">Area</th>
                        <th class="py-3 px-4 text-center">Score</th>
                        <th class="py-3 px-4 text-center">Kategori</th>
                        <th class="py-3 px-4">Tgl & Jam Selesai Analisa</th>
                        <th class="py-3 px-4 text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <template x-for="cand in queueList" :key="'queue_' + cand.id">
                        <tr class="hover:bg-amber-50/40 transition-colors">
                            <!-- Posisi Antrean -->
                            <td class="py-3 px-4 text-center font-black">
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-black shadow-2xs"
                                      :class="cand.queue_num === 1 ? 'bg-amber-500 text-white ring-2 ring-amber-300' : 'bg-slate-100 text-slate-700'"
                                      x-text="'#' + cand.queue_num">
                                </span>
                            </td>

                            <!-- Tgl Daftar -->
                            <td class="py-3 px-4 font-semibold text-slate-600 whitespace-nowrap" x-text="cand.created_at_formatted"></td>

                            <!-- Nama Kandidat -->
                            <td class="py-3 px-4">
                                <a :href="cand.detail_url" class="font-bold text-slate-900 hover:text-primary transition flex items-center gap-1.5">
                                    <span x-text="cand.full_name"></span>
                                    <i class="fa-solid fa-arrow-up-right-from-square text-[9px] text-slate-400"></i>
                                </a>
                                <div class="text-[10px] text-slate-400 mt-0.5 flex items-center gap-1" x-show="cand.has_cv">
                                    <i class="fa-regular fa-file-pdf text-rose-500"></i>
                                    <span class="truncate max-w-[200px]" x-text="cand.cv_name || 'Berkas CV Terlampir'"></span>
                                </div>
                            </td>

                            <!-- Jabatan -->
                            <td class="py-3 px-4 font-medium text-slate-700" x-text="cand.applied_job || '-'"></td>

                            <!-- Area -->
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[11px] font-semibold bg-slate-100 text-slate-700">
                                    <i class="fa-solid fa-location-dot text-slate-400 text-[9px]"></i>
                                    <span x-text="cand.area || 'JAKARTA'"></span>
                                </span>
                            </td>

                            <!-- Score -->
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-500">
                                    <i class="fa-solid fa-minus text-[9px]"></i>
                                    <span>Belum Dinilai</span>
                                </span>
                            </td>

                            <!-- Kategori -->
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                    <i class="fa-solid fa-clock text-[9px]"></i>
                                    <span>Menunggu Antrean</span>
                                </span>
                            </td>

                            <!-- Tgl & Jam Selesai Analisa -->
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 text-xs text-amber-800 font-semibold bg-amber-50 px-2.5 py-1 rounded-xl border border-amber-200/70">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    <span x-text="cand.queue_num === 1 ? 'Diproses Berikutnya' : 'Antrean #' + cand.queue_num"></span>
                                </span>
                            </td>

                            <!-- Aksi -->
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <a :href="cand.detail_url" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold text-primary bg-primary/10 hover:bg-primary/20 transition">
                                    <span>Detail</span>
                                    <i class="fa-solid fa-chevron-right text-[9px]"></i>
                                </a>
                            </td>
                        </tr>
                    </template>

                    <!-- Empty State Antrean -->
                    <tr x-show="queueList.length === 0">
                        <td colspan="9" class="py-10 text-center text-slate-400">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                                    <i class="fa-solid fa-circle-check"></i>
                                </div>
                                <span class="font-bold text-slate-700 text-sm">Semua Berkas Selesai Dianalisa</span>
                                <span class="text-xs text-slate-500">Saat ini tidak ada kandidat dalam antrean menunggu analisa AI.</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- TABEL 2: 10 KANDIDAT YANG BARU SELESAI DI ANALISA -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <!-- Header Tabel Selesai -->
        <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-gradient-to-r from-slate-50 to-emerald-50/30">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center text-lg font-bold">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                        <span>List Kandidat yang Baru Selesai di Analisa</span>
                        <span class="text-xs px-2 py-0.5 rounded-full font-bold bg-emerald-100 text-emerald-800" x-text="'10 Terakhir dari ' + completedCount"></span>
                    </h3>
                    <p class="text-xs text-slate-500">
                        Data kandidat yang baru selesai dinilai oleh engine AI secara otomatis.
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('airanking.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-800 bg-white hover:bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-300 shadow-2xs transition">
                    <i class="fa-solid fa-ranking-star text-amber-500"></i>
                    <span>Buka AI Leaderboard Lengkap</span>
                </a>
            </div>
        </div>

        <!-- Tabel Selesai -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/70 text-slate-500 font-bold uppercase text-[10px] tracking-wider">
                        <th class="py-3 px-4 text-center w-12">No</th>
                        <th class="py-3 px-4">Tgl Daftar</th>
                        <th class="py-3 px-4">Nama Kandidat</th>
                        <th class="py-3 px-4">Jabatan Dilamar</th>
                        <th class="py-3 px-4">Area</th>
                        <th class="py-3 px-4 text-center">Score</th>
                        <th class="py-3 px-4 text-center">Kategori</th>
                        <th class="py-3 px-4">Tgl & Jam Selesai Analisa</th>
                        <th class="py-3 px-4 text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <template x-for="cand in completedList" :key="'comp_' + cand.id">
                        <tr class="hover:bg-emerald-50/30 transition-colors">
                            <!-- No -->
                            <td class="py-3 px-4 text-center font-bold text-slate-500" x-text="cand.num"></td>

                            <!-- Tgl Daftar -->
                            <td class="py-3 px-4 font-semibold text-slate-600 whitespace-nowrap" x-text="cand.created_at_formatted"></td>

                            <!-- Nama Kandidat -->
                            <td class="py-3 px-4 font-bold text-slate-900">
                                <a :href="cand.detail_url" class="hover:text-primary transition flex items-center gap-1.5">
                                    <span x-text="cand.full_name"></span>
                                    <i class="fa-solid fa-arrow-up-right-from-square text-[9px] text-slate-400"></i>
                                </a>
                            </td>

                            <!-- Jabatan -->
                            <td class="py-3 px-4 font-medium text-slate-700" x-text="cand.applied_job || '-'"></td>

                            <!-- Area -->
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[11px] font-semibold bg-slate-100 text-slate-700">
                                    <i class="fa-solid fa-location-dot text-slate-400 text-[9px]"></i>
                                    <span x-text="cand.area || 'JAKARTA'"></span>
                                </span>
                            </td>

                            <!-- Score -->
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-black shadow-2xs"
                                      :class="cand.score >= 85 ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : (cand.score >= 60 ? 'bg-amber-100 text-amber-800 border border-amber-300' : 'bg-rose-100 text-rose-800 border border-rose-300')">
                                    <i class="fa-solid fa-bolt text-[9px]"></i>
                                    <span x-text="cand.score + '%'"></span>
                                </span>
                            </td>

                            <!-- Kategori -->
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold"
                                      :class="cand.category === 'Green' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : (cand.category === 'Yellow' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-rose-50 text-rose-700 border border-rose-200')">
                                    <span x-text="cand.category === 'Green' ? '🟢 Green' : (cand.category === 'Yellow' ? '🟡 Yellow' : '🔴 Red')"></span>
                                </span>
                            </td>

                            <!-- Tgl & Jam Selesai Analisa -->
                            <td class="py-3 px-4 whitespace-nowrap font-mono text-xs font-bold text-slate-700 flex items-center gap-1.5 pt-3.5">
                                <i class="fa-regular fa-clock text-slate-400 text-[10px]"></i>
                                <span x-text="cand.completed_at"></span>
                            </td>

                            <!-- Aksi -->
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <a :href="cand.detail_url" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition">
                                    <span>Hasil AI</span>
                                    <i class="fa-solid fa-chevron-right text-[9px]"></i>
                                </a>
                            </td>
                        </tr>
                    </template>

                    <!-- Empty State Selesai -->
                    <tr x-show="completedList.length === 0">
                        <td colspan="9" class="py-8 text-center text-slate-400">
                            Belum ada riwayat hasil analisa AI.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function aiQueueLogManager(initData) {
        return {
            queueCount: initData?.queueCount || 0,
            completedCount: initData?.completedCount || 0,
            greenCount: initData?.greenCount || 0,
            yellowCount: initData?.yellowCount || 0,
            redCount: initData?.redCount || 0,
            queueList: initData?.queueList || [],
            completedList: initData?.completedList || [],
            
            // Live Status Ticker
            isProcessing: initData?.liveStatus?.is_processing || false,
            current: initData?.liveStatus?.current || null,
            lastCompleted: initData?.liveStatus?.last_completed || null,
            nextCandidate: initData?.liveStatus?.next_candidate || null,
            
            // Auto reload state
            autoReload: true,
            countdown: 5,
            countdownTimer: null,
            paused: false,
            isLoading: false,
            lastUpdatedTime: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) + ' WIB',

            init() {
                this.startCountdown();
            },

            startCountdown() {
                if (this.countdownTimer) clearInterval(this.countdownTimer);
                this.countdownTimer = setInterval(() => {
                    if (!this.autoReload) return;
                    this.countdown--;
                    if (this.countdown <= 0) {
                        this.countdown = 5;
                        this.fetchData();
                    }
                }, 1000);
            },

            toggleAutoReload() {
                this.autoReload = !this.autoReload;
                if (this.autoReload) {
                    this.countdown = 5;
                }
            },

            async manualRefresh() {
                this.countdown = 5;
                await this.fetchData();
            },

            async fetchData() {
                if (this.isLoading) return;
                this.isLoading = true;
                try {
                    const res = await fetch('{{ route("kandidatportal.ai_queue_data") }}');
                    if (res.ok) {
                        const data = await res.json();
                        if (data.success) {
                            this.queueCount = data.queue_count;
                            this.completedCount = data.completed_count;
                            this.greenCount = data.green_count;
                            this.yellowCount = data.yellow_count;
                            this.redCount = data.red_count;
                            this.queueList = data.queue_list;
                            this.completedList = data.completed_list;
                            
                            if (data.live_status) {
                                this.isProcessing = data.live_status.is_processing;
                                this.current = data.live_status.current;
                                this.lastCompleted = data.live_status.last_completed;
                                this.nextCandidate = data.live_status.next_candidate;
                            }

                            this.lastUpdatedTime = (data.timestamp || new Date().toLocaleTimeString('id-ID')) + ' WIB';
                        }
                    }
                } catch (err) {
                    console.error("Gagal memperbarui data live:", err);
                } finally {
                    this.isLoading = false;
                }
            }
        };
    }
</script>
@endsection
