@extends('layouts.app')

@section('title', 'Statistik Job & Kandidat Pelamar - ASystem')

@section('content')
<div class="space-y-6 pb-12" x-data="jobStatsApp()">

    <!-- PAGE HEADER CARD -->
    <div class="page-header-card flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-primary-50 border border-primary-100 flex items-center justify-center text-primary text-2xl font-black shadow-inner">
                <i class="fa-solid fa-chart-pie"></i>
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-xl md:text-2xl font-extrabold text-slate-900 tracking-tight">Statistik Job & Kandidat Pelamar</h1>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-50 text-blue-700 border border-blue-200">
                        <i class="fa-solid fa-sync fa-spin text-[9px]"></i> Live Analytics
                    </span>
                </div>
                <p class="text-xs md:text-sm text-slate-500 font-medium mt-0.5">
                    Monitoring persebaran Job Requirement, performa pelamar per Area, Prinsiple, serta rincian Step Odoo ERP per Rekrutor / AS (Khusus Pelamar Kandidat Portal).
                </p>
            </div>
        </div>

        <!-- QUICK ACTIONS -->
        <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
            <a href="{{ route('job.input') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-all">
                <i class="fa-solid fa-briefcase text-slate-500"></i>
                <span>Kelola Job</span>
            </a>
            <a href="{{ route('kandidatportal.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-all">
                <i class="fa-solid fa-globe text-slate-500"></i>
                <span>Kandidat Portal</span>
            </a>
            <a href="{{ route('job.statistik.export', request()->all()) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition-all shadow-sm shadow-emerald-600/20">
                <i class="fa-solid fa-file-excel"></i>
                <span>Export CSV/Excel</span>
            </a>
        </div>
    </div>

    <!-- 4 METRIC STAT CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Total Job Posts -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm flex items-center gap-4 relative overflow-hidden group hover:border-blue-300 transition-all">
            <div class="w-12 h-12 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 text-xl font-bold flex-shrink-0 group-hover:scale-105 transition-transform">
                <i class="fa-solid fa-briefcase"></i>
            </div>
            <div class="min-w-0 flex-1">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Job Requirement</div>
                <div class="text-2xl font-black text-slate-900 leading-tight mt-0.5">{{ number_format($totalJobPosts) }}</div>
                <div class="text-[11px] font-semibold text-blue-600 flex items-center gap-1 mt-0.5">
                    <i class="fa-solid fa-layer-group text-[9px]"></i> Formasi terdata
                </div>
            </div>
        </div>

        <!-- 2. Total Pelamar -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm flex items-center gap-4 relative overflow-hidden group hover:border-indigo-300 transition-all">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 text-xl font-bold flex-shrink-0 group-hover:scale-105 transition-transform">
                <i class="fa-solid fa-users"></i>
            </div>
            <div class="min-w-0 flex-1">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Seluruh Pelamar</div>
                <div class="text-2xl font-black text-slate-900 leading-tight mt-0.5">{{ number_format($totalPelamar) }}</div>
                <div class="text-[11px] font-semibold text-indigo-600 flex items-center gap-1 mt-0.5">
                    <i class="fa-solid fa-user-plus text-[9px]"></i> Kandidat masuk
                </div>
            </div>
        </div>

        <!-- 3. Total Green Candidates -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm flex items-center gap-4 relative overflow-hidden group hover:border-emerald-300 transition-all">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 text-xl font-bold flex-shrink-0 group-hover:scale-105 transition-transform">
                <i class="fa-solid fa-user-check"></i>
            </div>
            <div class="min-w-0 flex-1">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Kandidat Grade Green</div>
                <div class="text-2xl font-black text-emerald-700 leading-tight mt-0.5">{{ number_format($totalGreen) }}</div>
                <div class="text-[11px] font-semibold text-emerald-600 flex items-center gap-1 mt-0.5">
                    <i class="fa-solid fa-circle-check text-[9px]"></i> 
                    {{ $totalPelamar > 0 ? round(($totalGreen / $totalPelamar) * 100, 1) : 0 }}% Lolos Kualifikasi
                </div>
            </div>
        </div>

        <!-- 4. Total Odoo Synced -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm flex items-center gap-4 relative overflow-hidden group hover:border-purple-300 transition-all">
            <div class="w-12 h-12 rounded-xl bg-purple-50 border border-purple-100 flex items-center justify-center text-purple-600 text-xl font-bold flex-shrink-0 group-hover:scale-105 transition-transform">
                <i class="fa-solid fa-cloud-arrow-up"></i>
            </div>
            <div class="min-w-0 flex-1">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Kandidat di Odoo ERP</div>
                <div class="text-2xl font-black text-purple-700 leading-tight mt-0.5">{{ number_format($totalOdoo) }}</div>
                <div class="text-[11px] font-semibold text-purple-600 flex items-center gap-1 mt-0.5">
                    <i class="fa-solid fa-link text-[9px]"></i>
                    {{ $totalPelamar > 0 ? round(($totalOdoo / $totalPelamar) * 100, 1) : 0 }}% Sinkronisasi Odoo
                </div>
            </div>
        </div>
    </div>

    <!-- FILTER BAR (SEPERTI SISTEM LAMA DENGAN DESAIN MODERN) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5">
        <form method="GET" action="{{ route('job.statistik') }}" class="space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-sliders text-primary"></i>
                    <span class="text-sm font-bold text-slate-800">Filter Data Statistik</span>
                </div>
                @if(!empty(array_filter($filters)))
                    <span class="text-xs bg-amber-50 text-amber-700 font-bold px-2 py-0.5 rounded-md border border-amber-200">
                        Filter Aktif Diterapkan
                    </span>
                @endif
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
                <!-- 1. Filter Region -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Region</label>
                    <select name="f_region" class="w-full text-xs font-semibold text-slate-700 bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 focus:bg-white focus:border-primary focus:ring-1 focus:ring-primary transition-all">
                        <option value="">Semua Region</option>
                        @foreach($listRegions as $reg)
                            <option value="{{ $reg }}" {{ $filters['region'] === $reg ? 'selected' : '' }}>{{ $reg }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- 2. Filter Area -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Area / Kota</label>
                    <select name="f_area" class="w-full text-xs font-semibold text-slate-700 bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 focus:bg-white focus:border-primary focus:ring-1 focus:ring-primary transition-all">
                        <option value="">Semua Area</option>
                        @foreach($listAreas as $ar)
                            <option value="{{ $ar }}" {{ $filters['area'] === $ar ? 'selected' : '' }}>{{ $ar }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- 3. Filter Prinsiple -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Prinsiple</label>
                    <select name="f_prinsiple" class="w-full text-xs font-semibold text-slate-700 bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 focus:bg-white focus:border-primary focus:ring-1 focus:ring-primary transition-all">
                        <option value="">Semua Prinsiple</option>
                        @foreach($listPrinsiple as $pr)
                            <option value="{{ $pr }}" {{ $filters['prinsiple'] === $pr ? 'selected' : '' }}>{{ $pr }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- 4. Filter Nama User / Rekrutor -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Nama User / AS</label>
                    <select name="f_user" class="w-full text-xs font-semibold text-slate-700 bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 focus:bg-white focus:border-primary focus:ring-1 focus:ring-primary transition-all">
                        <option value="">Semua User / Rekrutor</option>
                        @foreach($listUsers as $uVal => $uLabel)
                            <option value="{{ $uVal }}" {{ (strtolower($filters['user']) === strtolower($uVal) || strtolower($filters['user']) === strtolower($uLabel)) ? 'selected' : '' }}>
                                {{ $uLabel }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 5. Filter Info Lowongan -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Info Lowongan</label>
                    <select name="f_info" class="w-full text-xs font-semibold text-slate-700 bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 focus:bg-white focus:border-primary focus:ring-1 focus:ring-primary transition-all">
                        <option value="">Semua Info Lowongan</option>
                        @foreach($listInfo as $inf)
                            <option value="{{ $inf }}" {{ strtolower($filters['info']) === strtolower($inf) ? 'selected' : '' }}>{{ $inf }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- BUTTON ACTIONS -->
            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100 flex-wrap">
                <a href="{{ route('job.statistik') }}" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-all flex items-center gap-1.5">
                    <i class="fa-solid fa-rotate-left text-slate-400"></i>
                    <span>Reset Filter</span>
                </a>
                <a href="{{ route('job.statistik.export', request()->all()) }}" class="px-4 py-2 rounded-xl text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition-all flex items-center gap-1.5">
                    <i class="fa-solid fa-file-csv text-emerald-600"></i>
                    <span>Export Data</span>
                </a>
                <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-primary hover:bg-primary-700 transition-all shadow-sm shadow-primary-500/20 flex items-center gap-1.5">
                    <i class="fa-solid fa-filter"></i>
                    <span>Terapkan Filter</span>
                </button>
            </div>
        </form>
    </div>

    <!-- DUA KOLOM ATAS: TABEL 1 (AREA) & TABEL 2 (USER & AREA) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- TABEL 1: STATISTIK PER AREA -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col" x-data="tableFilterArea()">
            <!-- HEADER -->
            <div class="bg-slate-900 text-white px-4 py-3 flex items-center justify-between flex-wrap gap-2">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-map-location-dot text-cyan-400 text-sm"></i>
                    <h2 class="text-sm font-bold tracking-tight">Statistik per Area</h2>
                    <span class="text-[10px] bg-slate-800 text-cyan-300 font-extrabold px-2 py-0.5 rounded border border-slate-700" x-text="filteredRows.length + ' Area'"></span>
                </div>
                <!-- Mini Search Input -->
                <div class="relative">
                    <input type="text" x-model="searchQuery" placeholder="Cari area / region..." 
                           class="text-xs bg-slate-800 border border-slate-700 text-white placeholder-slate-400 rounded-lg pl-7 pr-2.5 py-1 focus:outline-none focus:border-cyan-400 w-36 sm:w-44 transition-all">
                    <i class="fa-solid fa-search text-[10px] text-slate-400 absolute left-2.5 top-2"></i>
                </div>
            </div>

            <!-- TABLE CONTENT -->
            <div class="overflow-x-auto flex-1 max-h-[480px]">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-slate-100 text-slate-600 font-bold sticky top-0 z-10 shadow-sm">
                        <tr>
                            <th class="py-2.5 px-3 border-b border-slate-200 w-10 text-center">No</th>
                            <th class="py-2.5 px-3 border-b border-slate-200">Region</th>
                            <th class="py-2.5 px-3 border-b border-slate-200">Area</th>
                            <th class="py-2.5 px-3 border-b border-slate-200 text-center">Jumlah Job Post</th>
                            <th class="py-2.5 px-3 border-b border-slate-200 text-center">Jumlah Pelamar</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        <template x-for="(row, idx) in paginatedRows" :key="'area-' + idx">
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-2 px-3 text-center text-slate-400" x-text="(page - 1) * perPage + idx + 1"></td>
                                <td class="py-2 px-3">
                                    <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200" x-text="row.region"></span>
                                </td>
                                <td class="py-2 px-3 font-semibold text-slate-900" x-text="row.area"></td>
                                <td class="py-2 px-3 text-center font-bold text-blue-700 bg-blue-50/50" x-text="Number(row.job_post).toLocaleString()"></td>
                                <td class="py-2 px-3 text-center font-bold text-emerald-700 bg-emerald-50/50" x-text="Number(row.pelamar).toLocaleString()"></td>
                            </tr>
                        </template>
                        <tr x-show="filteredRows.length === 0">
                            <td colspan="5" class="text-center py-8 text-slate-400 font-medium">
                                Tidak ada data statistik area yang sesuai filter.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- FOOTER & PAGINATION -->
            <div class="p-3 bg-slate-50 border-t border-slate-200 flex items-center justify-between text-xs text-slate-500 font-medium">
                <div>
                    Total: <strong class="text-slate-800" x-text="totalJobPosts.toLocaleString()"></strong> Job Post, 
                    <strong class="text-slate-800" x-text="totalPelamar.toLocaleString()"></strong> Pelamar
                </div>
                <div class="flex items-center gap-2" x-show="totalPages > 1">
                    <button @click="page = Math.max(1, page - 1)" :disabled="page <= 1" class="px-2 py-1 rounded bg-white border border-slate-200 disabled:opacity-40 text-xs">
                        <i class="fa-solid fa-chevron-left text-[10px]"></i>
                    </button>
                    <span class="text-[11px]">Hal <strong x-text="page"></strong> / <span x-text="totalPages"></span></span>
                    <button @click="page = Math.min(totalPages, page + 1)" :disabled="page >= totalPages" class="px-2 py-1 rounded bg-white border border-slate-200 disabled:opacity-40 text-xs">
                        <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- TABEL 2: STATISTIK PER NAMA USER & AREA -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col" x-data="tableFilterUserArea()">
            <!-- HEADER -->
            <div class="bg-sky-700 text-white px-4 py-3 flex items-center justify-between flex-wrap gap-2">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-user-gear text-cyan-200 text-sm"></i>
                    <h2 class="text-sm font-bold tracking-tight">Statistik per Nama User & Area</h2>
                    <span class="text-[10px] bg-sky-800 text-cyan-200 font-extrabold px-2 py-0.5 rounded border border-sky-600" x-text="filteredRows.length + ' User'"></span>
                </div>
                <!-- Mini Search Input -->
                <div class="relative">
                    <input type="text" x-model="searchQuery" placeholder="Cari nama user / area..." 
                           class="text-xs bg-sky-800 border border-sky-600 text-white placeholder-sky-200 rounded-lg pl-7 pr-2.5 py-1 focus:outline-none focus:border-cyan-300 w-36 sm:w-44 transition-all">
                    <i class="fa-solid fa-search text-[10px] text-sky-300 absolute left-2.5 top-2"></i>
                </div>
            </div>

            <!-- TABLE CONTENT -->
            <div class="overflow-x-auto flex-1 max-h-[480px]">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-slate-100 text-slate-600 font-bold sticky top-0 z-10 shadow-sm">
                        <tr>
                            <th class="py-2.5 px-3 border-b border-slate-200 w-10 text-center">No</th>
                            <th class="py-2.5 px-3 border-b border-slate-200">Nama User</th>
                            <th class="py-2.5 px-3 border-b border-slate-200">Region</th>
                            <th class="py-2.5 px-3 border-b border-slate-200">Area</th>
                            <th class="py-2.5 px-3 border-b border-slate-200 text-center">Jumlah Job Post</th>
                            <th class="py-2.5 px-3 border-b border-slate-200 text-center">Jumlah Pelamar</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        <template x-for="(row, idx) in paginatedRows" :key="'userarea-' + idx">
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-2 px-3 text-center text-slate-400" x-text="(page - 1) * perPage + idx + 1"></td>
                                <td class="py-2 px-3 font-semibold text-slate-900" x-text="row.user"></td>
                                <td class="py-2 px-3">
                                    <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200" x-text="row.region"></span>
                                </td>
                                <td class="py-2 px-3" x-text="row.area"></td>
                                <td class="py-2 px-3 text-center font-bold text-sky-700 bg-sky-50/50" x-text="Number(row.job_post).toLocaleString()"></td>
                                <td class="py-2 px-3 text-center font-bold text-emerald-700 bg-emerald-50/50" x-text="Number(row.pelamar).toLocaleString()"></td>
                            </tr>
                        </template>
                        <tr x-show="filteredRows.length === 0">
                            <td colspan="6" class="text-center py-8 text-slate-400 font-medium">
                                Tidak ada data statistik user & area yang sesuai filter.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- FOOTER & PAGINATION -->
            <div class="p-3 bg-slate-50 border-t border-slate-200 flex items-center justify-between text-xs text-slate-500 font-medium">
                <div>
                    Menampilkan <span class="font-bold text-slate-800" x-text="filteredRows.length"></span> baris
                </div>
                <div class="flex items-center gap-2" x-show="totalPages > 1">
                    <button @click="page = Math.max(1, page - 1)" :disabled="page <= 1" class="px-2 py-1 rounded bg-white border border-slate-200 disabled:opacity-40 text-xs">
                        <i class="fa-solid fa-chevron-left text-[10px]"></i>
                    </button>
                    <span class="text-[11px]">Hal <strong x-text="page"></strong> / <span x-text="totalPages"></span></span>
                    <button @click="page = Math.min(totalPages, page + 1)" :disabled="page >= totalPages" class="px-2 py-1 rounded bg-white border border-slate-200 disabled:opacity-40 text-xs">
                        <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    </button>
                </div>
            </div>
        </div>

    </div>

    <!-- TABEL 3: STATISTIK DETAIL KANDIDAT BERDASARKAN PRINSIPLE -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden" x-data="tableFilterDetail()">
        <!-- HEADER -->
        <div class="bg-slate-900 text-white px-5 py-3.5 flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-xs border border-emerald-500/30">
                    <i class="fa-solid fa-building-columns"></i>
                </div>
                <div>
                    <h2 class="text-sm md:text-base font-bold tracking-tight">Statistik Detail Kandidat Berdasarkan Prinsiple</h2>
                    <p class="text-[11px] text-slate-400">Rincian sebaran per user, area, prinsiple, lowongan pekerjaan, dan perolehan kategori Green / Yellow / Red</p>
                </div>
            </div>

            <!-- Controls -->
            <div class="flex items-center gap-2.5 flex-wrap sm:flex-nowrap">
                <div class="relative">
                    <input type="text" x-model="searchQuery" placeholder="Cari posisi, prinsiple, user..." 
                           class="text-xs bg-slate-800 border border-slate-700 text-white placeholder-slate-400 rounded-xl pl-8 pr-3 py-1.5 focus:outline-none focus:border-emerald-400 w-48 sm:w-64 transition-all">
                    <i class="fa-solid fa-search text-xs text-slate-400 absolute left-3 top-2.5"></i>
                </div>
                <select x-model.number="perPage" class="text-xs bg-slate-800 border border-slate-700 text-white rounded-xl px-2.5 py-1.5 focus:outline-none">
                    <option value="15">15 Baris</option>
                    <option value="30">30 Baris</option>
                    <option value="50">50 Baris</option>
                    <option value="100">100 Baris</option>
                </select>
            </div>
        </div>

        <!-- TABLE CONTENT -->
        <div class="overflow-x-auto max-h-[600px]">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-slate-100 text-slate-600 font-bold sticky top-0 z-10 shadow-sm">
                    <tr>
                        <th class="py-3 px-3.5 border-b border-slate-200 w-10 text-center">No</th>
                        <th class="py-3 px-3.5 border-b border-slate-200">Nama User</th>
                        <th class="py-3 px-3.5 border-b border-slate-200">Region</th>
                        <th class="py-3 px-3.5 border-b border-slate-200">Area</th>
                        <th class="py-3 px-3.5 border-b border-slate-200">Prinsiple</th>
                        <th class="py-3 px-3.5 border-b border-slate-200">Nama Job / Posisi</th>
                        <th class="py-3 px-3.5 border-b border-slate-200 text-center bg-emerald-50 text-emerald-800">Kandidat Green</th>
                        <th class="py-3 px-3.5 border-b border-slate-200 text-center bg-amber-50 text-amber-800">Kandidat Yellow</th>
                        <th class="py-3 px-3.5 border-b border-slate-200 text-center bg-rose-50 text-rose-800">Kandidat Red</th>
                        <th class="py-3 px-3.5 border-b border-slate-200 text-center bg-slate-200 text-slate-900">Total Pelamar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    <template x-for="(row, idx) in paginatedRows" :key="'detail-' + idx">
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-2.5 px-3.5 text-center text-slate-400" x-text="(page - 1) * perPage + idx + 1"></td>
                            <td class="py-2.5 px-3.5 font-semibold text-slate-900" x-text="row.user"></td>
                            <td class="py-2.5 px-3.5">
                                <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200" x-text="row.region"></span>
                            </td>
                            <td class="py-2.5 px-3.5" x-text="row.area"></td>
                            <td class="py-2.5 px-3.5 font-semibold text-indigo-700" x-text="row.prinsiple"></td>
                            <td class="py-2.5 px-3.5 font-bold text-slate-900" x-text="row.job_title"></td>
                            
                            <!-- Green Badge -->
                            <td class="py-2.5 px-3.5 text-center bg-emerald-50/40">
                                <span class="inline-flex items-center justify-center min-w-[28px] px-2 py-0.5 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-200" x-text="Number(row.green).toLocaleString()"></span>
                            </td>

                            <!-- Yellow Badge -->
                            <td class="py-2.5 px-3.5 text-center bg-amber-50/40">
                                <span class="inline-flex items-center justify-center min-w-[28px] px-2 py-0.5 rounded-full text-xs font-black bg-amber-100 text-amber-800 border border-amber-200" x-text="Number(row.yello).toLocaleString()"></span>
                            </td>

                            <!-- Red Badge -->
                            <td class="py-2.5 px-3.5 text-center bg-rose-50/40">
                                <span class="inline-flex items-center justify-center min-w-[28px] px-2 py-0.5 rounded-full text-xs font-black bg-rose-100 text-rose-800 border border-rose-200" x-text="Number(row.red).toLocaleString()"></span>
                            </td>

                            <!-- Total -->
                            <td class="py-2.5 px-3.5 text-center font-black text-slate-900 bg-slate-100/70" x-text="Number(row.total_pelamar).toLocaleString()"></td>
                        </tr>
                    </template>
                    <tr x-show="filteredRows.length === 0">
                        <td colspan="10" class="text-center py-10 text-slate-400 font-medium">
                            Tidak ada data detail statistik yang sesuai kriteria pencarian.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- FOOTER & PAGINATION -->
        <div class="p-4 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs text-slate-500 font-medium">
            <div>
                Menampilkan <strong class="text-slate-800" x-text="filteredRows.length"></strong> posisi lowongan terpilih
            </div>
            <div class="flex items-center gap-2" x-show="totalPages > 1">
                <button @click="page = 1" :disabled="page <= 1" class="px-2 py-1 rounded bg-white border border-slate-200 disabled:opacity-40 text-xs font-bold" title="Halaman Pertama">
                    <i class="fa-solid fa-angles-left text-[10px]"></i>
                </button>
                <button @click="page = Math.max(1, page - 1)" :disabled="page <= 1" class="px-2.5 py-1 rounded bg-white border border-slate-200 disabled:opacity-40 text-xs">
                    <i class="fa-solid fa-chevron-left text-[10px]"></i>
                </button>
                <span class="text-xs">Halaman <strong class="text-slate-800" x-text="page"></strong> dari <strong class="text-slate-800" x-text="totalPages"></strong></span>
                <button @click="page = Math.min(totalPages, page + 1)" :disabled="page >= totalPages" class="px-2.5 py-1 rounded bg-white border border-slate-200 disabled:opacity-40 text-xs">
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </button>
                <button @click="page = totalPages" :disabled="page >= totalPages" class="px-2 py-1 rounded bg-white border border-slate-200 disabled:opacity-40 text-xs font-bold" title="Halaman Terakhir">
                    <i class="fa-solid fa-angles-right text-[10px]"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- TABEL 4: STATISTIK KANDIDAT PER REKRUTOR / AS BERDASARKAN STEP ODOO ERP -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden" x-data="tableFilterOdooRecruiter()">
        <!-- HEADER -->
        <div class="bg-indigo-900 text-white px-5 py-4 flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-indigo-500/30 text-indigo-200 flex items-center justify-center font-bold text-sm border border-indigo-400/30 shadow-inner">
                    <i class="fa-solid fa-users-gear"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-sm md:text-base font-bold tracking-tight">Statistik Kandidat per Rekrutor / AS Berdasarkan Step Odoo ERP</h2>
                        <span class="text-[10px] bg-indigo-800 text-indigo-200 font-extrabold px-2 py-0.5 rounded border border-indigo-700">
                            ERP Integration
                        </span>
                        <span class="text-[10px] bg-emerald-500/30 text-emerald-300 font-bold px-2 py-0.5 rounded border border-emerald-400/30">
                            <i class="fa-solid fa-arrow-down-wide-short text-[9px]"></i> Rank Joined
                        </span>
                    </div>
                    <p class="text-[11px] text-indigo-200/80 mt-0.5">
                        Rekapitulasi progres kandidat di masing-masing tahapan pipeline Odoo Recruitment (Diurutkan berdasarkan peringkat jumlah Joined terbanyak).
                    </p>
                </div>
            </div>

            <!-- Controls -->
            <div class="flex items-center gap-2.5 flex-wrap sm:flex-nowrap">
                <div class="relative">
                    <input type="text" x-model="searchQuery" placeholder="Cari nama rekrutor / area..." 
                           class="text-xs bg-indigo-800/80 border border-indigo-700 text-white placeholder-indigo-300 rounded-xl pl-8 pr-3 py-1.5 focus:outline-none focus:border-cyan-300 w-48 sm:w-64 transition-all">
                    <i class="fa-solid fa-search text-xs text-indigo-300 absolute left-3 top-2.5"></i>
                </div>
                <select x-model.number="perPage" class="text-xs bg-indigo-800/80 border border-indigo-700 text-white rounded-xl px-2.5 py-1.5 focus:outline-none">
                    <option value="15">15 Baris</option>
                    <option value="30">30 Baris</option>
                    <option value="50">50 Baris</option>
                    <option value="100">100 Baris</option>
                </select>
            </div>
        </div>

        <!-- TABLE CONTENT -->
        <div class="overflow-x-auto max-h-[600px]">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-slate-100 text-slate-600 font-bold sticky top-0 z-10 shadow-sm">
                    <tr>
                        <th class="py-3 px-3.5 border-b border-slate-200 w-10 text-center">No</th>
                        <th class="py-3 px-3.5 border-b border-slate-200">Nama Rekrutor / AS</th>
                        <th class="py-3 px-3.5 border-b border-slate-200">Region & Area</th>
                        <th class="py-3 px-3.5 border-b border-slate-200 text-center bg-slate-200/80 text-slate-900">Total</th>
                        <th class="py-3 px-3.5 border-b border-slate-200 text-center bg-sky-50 text-sky-800">1. Pelamar</th>
                        <th class="py-3 px-3.5 border-b border-slate-200 text-center bg-indigo-50 text-indigo-800">2. Interview</th>
                        <th class="py-3 px-3.5 border-b border-slate-200 text-center bg-purple-50 text-purple-800">3. Principal</th>
                        <th class="py-3 px-3.5 border-b border-slate-200 text-center bg-amber-50 text-amber-800">4. E-Learning</th>
                        <th class="py-3 px-3.5 border-b border-slate-200 text-center bg-teal-50 text-teal-800">5. PKWT</th>
                        <th class="py-3 px-3.5 border-b border-slate-200 text-center bg-emerald-100 text-emerald-900 font-black border-b-2 border-emerald-500">
                            <div class="flex items-center justify-center gap-1">
                                <span>6. Joined</span>
                                <i class="fa-solid fa-arrow-down-wide-short text-[10px] text-emerald-700"></i>
                            </div>
                        </th>
                        <th class="py-3 px-3.5 border-b border-slate-200 text-center bg-rose-50 text-rose-800">Belum di Odoo</th>
                        <th class="py-3 px-3.5 border-b border-slate-200 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    <template x-for="(row, idx) in paginatedRows" :key="'odoo-rec-' + idx">
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-2.5 px-3.5 text-center text-slate-400" x-text="(page - 1) * perPage + idx + 1"></td>
                            <td class="py-2.5 px-3.5">
                                <div class="font-bold text-slate-900" x-text="row.user_display"></div>
                                <div class="text-[10px] text-slate-400 font-mono" x-text="row.user_email"></div>
                            </td>
                            <td class="py-2.5 px-3.5">
                                <div class="font-semibold text-slate-800" x-text="row.area"></div>
                                <div class="text-[10px] text-slate-400" x-text="row.region"></div>
                            </td>
                            <!-- Total -->
                            <td class="py-2.5 px-3.5 text-center font-black text-slate-900 bg-slate-100/80">
                                <span class="inline-flex items-center justify-center min-w-[28px] px-2 py-0.5 rounded-full text-xs font-black bg-slate-800 text-white" x-text="Number(row.total).toLocaleString()"></span>
                            </td>

                            <!-- 1. Pelamar -->
                            <td class="py-2.5 px-3.5 text-center bg-sky-50/40">
                                <span class="inline-flex items-center justify-center min-w-[24px] px-1.5 py-0.5 rounded-md text-xs font-bold" 
                                      :class="row.data_pelamar > 0 ? 'bg-sky-100 text-sky-800 font-black' : 'text-slate-300'"
                                      x-text="Number(row.data_pelamar).toLocaleString()"></span>
                            </td>

                            <!-- 2. Interview -->
                            <td class="py-2.5 px-3.5 text-center bg-indigo-50/40">
                                <span class="inline-flex items-center justify-center min-w-[24px] px-1.5 py-0.5 rounded-md text-xs font-bold" 
                                      :class="row.interview > 0 ? 'bg-indigo-100 text-indigo-800 font-black' : 'text-slate-300'"
                                      x-text="Number(row.interview).toLocaleString()"></span>
                            </td>

                            <!-- 3. Principal -->
                            <td class="py-2.5 px-3.5 text-center bg-purple-50/40">
                                <span class="inline-flex items-center justify-center min-w-[24px] px-1.5 py-0.5 rounded-md text-xs font-bold" 
                                      :class="row.principal > 0 ? 'bg-purple-100 text-purple-800 font-black' : 'text-slate-300'"
                                      x-text="Number(row.principal).toLocaleString()"></span>
                            </td>

                            <!-- 4. E-Learning -->
                            <td class="py-2.5 px-3.5 text-center bg-amber-50/40">
                                <span class="inline-flex items-center justify-center min-w-[24px] px-1.5 py-0.5 rounded-md text-xs font-bold" 
                                      :class="row.elearning > 0 ? 'bg-amber-100 text-amber-800 font-black' : 'text-slate-300'"
                                      x-text="Number(row.elearning).toLocaleString()"></span>
                            </td>

                            <!-- 5. PKWT -->
                            <td class="py-2.5 px-3.5 text-center bg-teal-50/40">
                                <span class="inline-flex items-center justify-center min-w-[24px] px-1.5 py-0.5 rounded-md text-xs font-bold" 
                                      :class="row.pkwt > 0 ? 'bg-teal-100 text-teal-800 font-black' : 'text-slate-300'"
                                      x-text="Number(row.pkwt).toLocaleString()"></span>
                            </td>

                            <!-- 6. Joined -->
                            <td class="py-2.5 px-3.5 text-center bg-emerald-50/40">
                                <span class="inline-flex items-center justify-center min-w-[24px] px-1.5 py-0.5 rounded-md text-xs font-bold" 
                                      :class="row.joined > 0 ? 'bg-emerald-100 text-emerald-800 font-black' : 'text-slate-300'"
                                      x-text="Number(row.joined).toLocaleString()"></span>
                            </td>

                            <!-- Belum di Odoo -->
                            <td class="py-2.5 px-3.5 text-center bg-rose-50/40">
                                <span class="inline-flex items-center justify-center min-w-[24px] px-1.5 py-0.5 rounded-md text-xs font-bold" 
                                      :class="row.belum_di_odoo > 0 ? 'bg-rose-100 text-rose-800 font-black' : 'text-slate-300'"
                                      x-text="Number(row.belum_di_odoo).toLocaleString()"></span>
                            </td>

                            <!-- Aksi -->
                            <td class="py-2.5 px-3.5 text-center">
                                <a :href="'{{ route('kandidatportal.index') }}?recruiter=' + encodeURIComponent(row.user_email)" 
                                   title="Lihat Kandidat Portal Rekrutor Ini"
                                   class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-[10px] font-bold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 border border-indigo-200 transition-all">
                                    <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>
                                    <span>Portal</span>
                                </a>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filteredRows.length === 0">
                        <td colspan="12" class="text-center py-10 text-slate-400 font-medium">
                            Tidak ada data rekrutor yang sesuai kriteria pencarian.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- FOOTER & PAGINATION -->
        <div class="p-4 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs text-slate-500 font-medium">
            <div>
                Menampilkan <strong class="text-slate-800" x-text="filteredRows.length"></strong> Rekrutor / AS terdaftar
            </div>
            <div class="flex items-center gap-2" x-show="totalPages > 1">
                <button @click="page = 1" :disabled="page <= 1" class="px-2 py-1 rounded bg-white border border-slate-200 disabled:opacity-40 text-xs font-bold" title="Halaman Pertama">
                    <i class="fa-solid fa-angles-left text-[10px]"></i>
                </button>
                <button @click="page = Math.max(1, page - 1)" :disabled="page <= 1" class="px-2.5 py-1 rounded bg-white border border-slate-200 disabled:opacity-40 text-xs">
                    <i class="fa-solid fa-chevron-left text-[10px]"></i>
                </button>
                <span class="text-xs">Halaman <strong class="text-slate-800" x-text="page"></strong> dari <strong class="text-slate-800" x-text="totalPages"></strong></span>
                <button @click="page = Math.min(totalPages, page + 1)" :disabled="page >= totalPages" class="px-2.5 py-1 rounded bg-white border border-slate-200 disabled:opacity-40 text-xs">
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </button>
                <button @click="page = totalPages" :disabled="page >= totalPages" class="px-2 py-1 rounded bg-white border border-slate-200 disabled:opacity-40 text-xs font-bold" title="Halaman Terakhir">
                    <i class="fa-solid fa-angles-right text-[10px]"></i>
                </button>
            </div>
        </div>
    </div>

</div>

<!-- DATA EMBEDDED DARI CONTROLLER UNTUK ALPINES -->
<script>
    const RAW_STATS_AREA = @json($statsArea);
    const RAW_STATS_USER_AREA = @json($statsUserArea);
    const RAW_STATS_DETAIL = @json($statsDetail);
    const RAW_STATS_ODOO_RECRUITER = @json($statsOdooRecruiter);

    function jobStatsApp() {
        return {
            init() {
                // Initialize app level helpers if needed
            }
        };
    }

    function tableFilterArea() {
        return {
            searchQuery: '',
            page: 1,
            perPage: 15,
            rows: RAW_STATS_AREA,
            get filteredRows() {
                const q = this.searchQuery.toLowerCase().trim();
                if (!q) return this.rows;
                return this.rows.filter(r => 
                    (r.area && r.area.toLowerCase().includes(q)) ||
                    (r.region && r.region.toLowerCase().includes(q))
                );
            },
            get paginatedRows() {
                const start = (this.page - 1) * this.perPage;
                return this.filteredRows.slice(start, start + this.perPage);
            },
            get totalPages() {
                return Math.ceil(this.filteredRows.length / this.perPage) || 1;
            },
            get totalJobPosts() {
                return this.rows.reduce((sum, r) => sum + Number(r.job_post || 0), 0);
            },
            get totalPelamar() {
                return this.rows.reduce((sum, r) => sum + Number(r.pelamar || 0), 0);
            }
        };
    }

    function tableFilterUserArea() {
        return {
            searchQuery: '',
            page: 1,
            perPage: 15,
            rows: RAW_STATS_USER_AREA,
            get filteredRows() {
                const q = this.searchQuery.toLowerCase().trim();
                if (!q) return this.rows;
                return this.rows.filter(r => 
                    (r.user && r.user.toLowerCase().includes(q)) ||
                    (r.area && r.area.toLowerCase().includes(q)) ||
                    (r.region && r.region.toLowerCase().includes(q))
                );
            },
            get paginatedRows() {
                const start = (this.page - 1) * this.perPage;
                return this.filteredRows.slice(start, start + this.perPage);
            },
            get totalPages() {
                return Math.ceil(this.filteredRows.length / this.perPage) || 1;
            }
        };
    }

    function tableFilterDetail() {
        return {
            searchQuery: '',
            page: 1,
            perPage: 15,
            rows: RAW_STATS_DETAIL,
            get filteredRows() {
                const q = this.searchQuery.toLowerCase().trim();
                if (!q) return this.rows;
                return this.rows.filter(r => 
                    (r.user && r.user.toLowerCase().includes(q)) ||
                    (r.area && r.area.toLowerCase().includes(q)) ||
                    (r.region && r.region.toLowerCase().includes(q)) ||
                    (r.prinsiple && r.prinsiple.toLowerCase().includes(q)) ||
                    (r.job_title && r.job_title.toLowerCase().includes(q))
                );
            },
            get paginatedRows() {
                const start = (this.page - 1) * this.perPage;
                return this.filteredRows.slice(start, start + this.perPage);
            },
            get totalPages() {
                return Math.ceil(this.filteredRows.length / this.perPage) || 1;
            }
        };
    }

    function tableFilterOdooRecruiter() {
        return {
            searchQuery: '',
            page: 1,
            perPage: 15,
            rows: RAW_STATS_ODOO_RECRUITER,
            get filteredRows() {
                const q = this.searchQuery.toLowerCase().trim();
                if (!q) return this.rows;
                return this.rows.filter(r => 
                    (r.user_display && r.user_display.toLowerCase().includes(q)) ||
                    (r.user_email && r.user_email.toLowerCase().includes(q)) ||
                    (r.area && r.area.toLowerCase().includes(q)) ||
                    (r.region && r.region.toLowerCase().includes(q))
                );
            },
            get paginatedRows() {
                const start = (this.page - 1) * this.perPage;
                return this.filteredRows.slice(start, start + this.perPage);
            },
            get totalPages() {
                return Math.ceil(this.filteredRows.length / this.perPage) || 1;
            }
        };
    }
</script>
@endsection
