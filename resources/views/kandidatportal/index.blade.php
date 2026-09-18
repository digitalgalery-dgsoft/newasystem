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

                    @if(!empty($isAdmin) && !empty($allRecruiters) && count($allRecruiters) > 0)
                    <!-- Filter Rekruter (Khusus Administrator) -->
                    <select name="recruiter" onchange="this.form.submit()" class="px-2.5 py-1.5 rounded-xl border border-blue-200 text-xs font-bold text-blue-900 bg-blue-50/80 focus:ring-2 focus:ring-primary outline-none">
                        <option value="my" {{ ($filterRecruiter === 'my' || empty($filterRecruiter)) ? 'selected' : '' }}>👤 Data Saya ({{ auth()->user()->name ?? 'Admin' }})</option>
                        <option value="all" {{ $filterRecruiter === 'all' ? 'selected' : '' }}>🌐 Semua Rekruter (Nasional)</option>
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
                               placeholder="Cari nama, NIK, posisi..." 
                               class="w-full pl-8 pr-3 py-1.5 rounded-xl border border-slate-300 text-xs font-medium text-slate-700 focus:ring-2 focus:ring-primary-500 outline-none">
                        <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    </div>

                    <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-primary text-white text-xs font-bold hover:bg-primary-700 transition-all shadow-sm">
                        <i class="fa-solid fa-filter mr-1"></i> Filter
                    </button>

                    @if($kategori || $start || $end || $search || ($isAdmin && !empty($filterRecruiter)))
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
                                {{ $cand->created_at ? $cand->created_at->format('d M Y') : '-' }}
                            </div>
                            <div class="text-[10px] text-slate-400">
                                {{ $cand->created_at ? $cand->created_at->format('H:i') : '' }} WIB
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

                        <!-- AI Score -->
                        <td class="text-center">
                            @php
                                $score = intval($cand->ai_score ?? 0);
                            @endphp
                            @if($score > 0)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-extrabold border {{ $cand->ai_badge_class }}">
                                    <i class="fa-solid fa-bolt text-[9px]"></i>
                                    {{ $score }}%
                                </span>
                            @else
                                <span class="text-[10px] text-slate-400 italic">Pending</span>
                            @endif
                        </td>

                        <!-- Kategori AI -->
                        <td class="text-center">
                            @if($cand->kategori_kandidat === 'Green')
                                <span class="badge-pill bg-emerald-50 text-emerald-700 border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Green
                                </span>
                            @elseif($cand->kategori_kandidat === 'Yellow')
                                <span class="badge-pill bg-amber-50 text-amber-700 border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Yellow
                                </span>
                            @elseif($cand->kategori_kandidat === 'Red')
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
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                        <i class="fa-solid fa-file-excel"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-extrabold text-slate-900">Export Pelamar ke Excel</h4>
                        <p class="text-[11px] text-slate-500">Unduh data pelamar Job Portal dalam format file spreadsheet</p>
                    </div>
                </div>
                <button @click="openExportModal = false" class="text-slate-400 hover:text-slate-600 text-base p-1">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form action="{{ route('kandidatportal.export') }}" method="GET" class="space-y-4">
                <input type="hidden" name="recruiter" value="{{ $filterRecruiter }}">
                <input type="hidden" name="q" value="{{ $search }}">
                <input type="hidden" name="start" value="{{ $start }}">
                <input type="hidden" name="end" value="{{ $end }}">

                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Status Seleksi / Tab</label>
                        <select name="tab" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-medium text-slate-700 bg-white outline-none">
                            <option value="baru" {{ $tab === 'baru' ? 'selected' : '' }}>Baru / Semua</option>
                            <option value="interview" {{ $tab === 'interview' ? 'selected' : '' }}>Interview</option>
                            <option value="terima" {{ $tab === 'terima' ? 'selected' : '' }}>Terima</option>
                            <option value="arsip" {{ $tab === 'arsip' ? 'selected' : '' }}>Arsip</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Pilih Kategori AI</label>
                        <select name="kategori" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-medium text-slate-700 bg-white outline-none">
                            <option value="">Semua Kategori (Green, Yellow, Red)</option>
                            <option value="Green" {{ $kategori === 'Green' ? 'selected' : '' }}>Hanya Green</option>
                            <option value="Yellow" {{ $kategori === 'Yellow' ? 'selected' : '' }}>Hanya Yellow</option>
                            <option value="Red" {{ $kategori === 'Red' ? 'selected' : '' }}>Hanya Red</option>
                        </select>
                    </div>

                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-[11px] text-slate-600 flex items-center gap-2">
                        <i class="fa-solid fa-circle-info text-primary"></i>
                        <span>Data yang diexport otomatis sesuai rekruter: <strong>{{ $displayUserName }}</strong></span>
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" @click="openExportModal = false" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-all">
                        Tutup
                    </button>
                    <button type="submit" @click="openExportModal = false" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-all shadow-md shadow-emerald-600/20 flex items-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-download"></i>
                        <span>Download Excel (.csv)</span>
                    </button>
                </div>
            </form>
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