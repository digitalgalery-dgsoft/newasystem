@extends('layouts.app')

@section('title', 'Daftar Surat Peringatan (SP)')

@section('content')
<div class="space-y-6">

    <!-- Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-2xl shadow-xs border border-slate-200">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-rose-500 to-amber-500 text-white flex items-center justify-center font-bold text-xl shadow-xs flex-shrink-0">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <h1 class="text-lg font-bold text-slate-900 tracking-tight flex items-center gap-2">
                    <span>Surat Peringatan (SP)</span>
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 border border-slate-200">ESA Groups</span>
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">Pengajuan, review legalitas pasal HRD, penerbitan nomor resmi & pemantauan masa berlaku SP.</p>
            </div>
        </div>
        <div class="flex items-center gap-2.5 flex-wrap">
            @if($isHrdOrAdmin)
            <a href="{{ route('master.approval-workflow.index', ['module' => 'surat_peringatan']) }}" 
               class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-all border border-slate-200 shadow-2xs">
                <i class="fa-solid fa-sliders text-xs text-primary"></i>
                <span>Setting Approver HRD</span>
            </a>
            @endif
            <a href="{{ route('warning-letters.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-xs hover:shadow transition-all">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Ajukan SP Baru</span>
            </a>
        </div>
    </div>

    <!-- Alert Success / Error -->
    @if(session('success'))
    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2.5 shadow-2xs">
        <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if(session('warning'))
    <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-semibold flex items-center gap-2.5 shadow-2xs">
        <i class="fa-solid fa-triangle-exclamation text-amber-600 text-sm"></i>
        <span>{{ session('warning') }}</span>
    </div>
    @endif

    <!-- Metric Stat Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5">
        <!-- Total SP -->
        <a href="{{ route('warning-letters.index', ['tab' => 'all']) }}" 
           class="p-4 rounded-2xl bg-white border {{ $tab === 'all' ? 'border-primary ring-2 ring-primary/20' : 'border-slate-200 hover:border-slate-300' }} shadow-xs transition-all">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Total SP</span>
                <i class="fa-solid fa-layer-group text-slate-400"></i>
            </div>
            <div class="text-2xl font-extrabold text-slate-900">{{ number_format($stats['total']) }}</div>
            <div class="text-[10px] text-slate-400 mt-1">Seluruh arsip SP</div>
        </a>

        <!-- Menunggu Review Pimpinan (Tahap 1) -->
        <a href="{{ route('warning-letters.index', ['tab' => 'review_head']) }}" 
           class="p-4 rounded-2xl bg-white border {{ $tab === 'review_head' ? 'border-blue-500 ring-2 ring-blue-500/20' : 'border-slate-200 hover:border-blue-300' }} shadow-xs transition-all">
            <div class="flex items-center justify-between text-blue-500 mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-blue-700">Review Pimpinan</span>
                <i class="fa-solid fa-user-tie"></i>
            </div>
            <div class="text-2xl font-extrabold text-blue-600">{{ number_format($stats['review_head'] ?? 0) }}</div>
            <div class="text-[10px] text-blue-600/80 mt-1">Verifikasi Tahap 1</div>
        </a>

        <!-- Menunggu Review HRD (Tahap 2) -->
        <a href="{{ route('warning-letters.index', ['tab' => 'review_hrd']) }}" 
           class="p-4 rounded-2xl bg-white border {{ $tab === 'review_hrd' ? 'border-amber-500 ring-2 ring-amber-500/20' : 'border-slate-200 hover:border-amber-300' }} shadow-xs transition-all">
            <div class="flex items-center justify-between text-amber-500 mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-amber-700">Review HRD</span>
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <div class="text-2xl font-extrabold text-amber-600">{{ number_format($stats['review_hrd']) }}</div>
            <div class="text-[10px] text-amber-600/80 mt-1">Verifikasi Tahap 2 & Nomor</div>
        </a>

        <!-- Aktif / Disetujui -->
        <a href="{{ route('warning-letters.index', ['tab' => 'approved']) }}" 
           class="p-4 rounded-2xl bg-white border {{ $tab === 'approved' ? 'border-emerald-500 ring-2 ring-emerald-500/20' : 'border-slate-200 hover:border-emerald-300' }} shadow-xs transition-all">
            <div class="flex items-center justify-between text-emerald-500 mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-700">Aktif Berlaku</span>
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="text-2xl font-extrabold text-emerald-600">{{ number_format($stats['approved']) }}</div>
            <div class="text-[10px] text-emerald-600/80 mt-1">Dalam masa 6 bulan</div>
        </a>

        <!-- Scan TTD Belum Lengkap -->
        <a href="{{ route('warning-letters.index', ['tab' => 'missing_signed']) }}" 
           class="p-4 rounded-2xl bg-white border {{ $tab === 'missing_signed' ? 'border-rose-500 ring-2 ring-rose-500/20' : 'border-slate-200 hover:border-rose-300' }} shadow-xs transition-all">
            <div class="flex items-center justify-between text-rose-500 mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-rose-700">Belum Ada TTD</span>
                <i class="fa-solid fa-file-circle-exclamation"></i>
            </div>
            <div class="text-2xl font-extrabold text-rose-600">{{ number_format($stats['missing_signed']) }}</div>
            <div class="text-[10px] text-rose-600/80 mt-1">Scan fisik belum diunggah</div>
        </a>

        <!-- Expired / Kedaluwarsa -->
        <a href="{{ route('warning-letters.index', ['tab' => 'expired']) }}" 
           class="p-4 rounded-2xl bg-white border {{ $tab === 'expired' ? 'border-slate-400 ring-2 ring-slate-400/20' : 'border-slate-200 hover:border-slate-300' }} shadow-xs transition-all">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-600">Kedaluwarsa</span>
                <i class="fa-solid fa-calendar-xmark"></i>
            </div>
            <div class="text-2xl font-extrabold text-slate-600">{{ number_format($stats['expired']) }}</div>
            <div class="text-[10px] text-slate-400 mt-1">Masa berlaku habis</div>
        </a>
    </div>

    <!-- Main Content Table Container -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
        
        <!-- Filter Tabs & Search Controls -->
        <div class="p-4 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            
            <!-- Tab Links -->
            <div class="flex items-center gap-1.5 overflow-x-auto text-xs font-bold pb-1 md:pb-0">
                <a href="{{ route('warning-letters.index', array_merge(request()->query(), ['tab' => 'all'])) }}"
                   class="px-3 py-1.5 rounded-xl transition-all whitespace-nowrap {{ $tab === 'all' ? 'bg-slate-900 text-white shadow-2xs' : 'text-slate-600 hover:bg-slate-100' }}">
                    Semua ({{ $stats['total'] }})
                </a>
                <a href="{{ route('warning-letters.index', array_merge(request()->query(), ['tab' => 'review_head'])) }}"
                   class="px-3 py-1.5 rounded-xl transition-all flex items-center gap-1.5 whitespace-nowrap {{ $tab === 'review_head' ? 'bg-blue-600 text-white shadow-2xs' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-700' }}">
                    <span>Review Pimpinan</span>
                    @if(($stats['review_head'] ?? 0) > 0)
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $tab === 'review_head' ? 'bg-white text-blue-700' : 'bg-blue-100 text-blue-800' }}">{{ $stats['review_head'] }}</span>
                    @endif
                </a>
                <a href="{{ route('warning-letters.index', array_merge(request()->query(), ['tab' => 'review_hrd'])) }}"
                   class="px-3 py-1.5 rounded-xl transition-all flex items-center gap-1.5 whitespace-nowrap {{ $tab === 'review_hrd' ? 'bg-amber-600 text-white shadow-2xs' : 'text-slate-600 hover:bg-amber-50 hover:text-amber-700' }}">
                    <span>Review HRD</span>
                    @if($stats['review_hrd'] > 0)
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $tab === 'review_hrd' ? 'bg-white text-amber-700' : 'bg-amber-100 text-amber-800' }}">{{ $stats['review_hrd'] }}</span>
                    @endif
                </a>
                <a href="{{ route('warning-letters.index', array_merge(request()->query(), ['tab' => 'approved'])) }}"
                   class="px-3 py-1.5 rounded-xl transition-all whitespace-nowrap {{ $tab === 'approved' ? 'bg-emerald-600 text-white shadow-2xs' : 'text-slate-600 hover:bg-emerald-50 hover:text-emerald-700' }}">
                    Aktif ({{ $stats['approved'] }})
                </a>
                <a href="{{ route('warning-letters.index', array_merge(request()->query(), ['tab' => 'missing_signed'])) }}"
                   class="px-3 py-1.5 rounded-xl transition-all flex items-center gap-1.5 whitespace-nowrap {{ $tab === 'missing_signed' ? 'bg-rose-600 text-white shadow-2xs' : 'text-slate-600 hover:bg-rose-50 hover:text-rose-700' }}">
                    <span>Belum Ada TTD</span>
                    @if($stats['missing_signed'] > 0)
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $tab === 'missing_signed' ? 'bg-white text-rose-700' : 'bg-rose-100 text-rose-800' }}">{{ $stats['missing_signed'] }}</span>
                    @endif
                </a>
                <a href="{{ route('warning-letters.index', array_merge(request()->query(), ['tab' => 'expired'])) }}"
                   class="px-3 py-1.5 rounded-xl transition-all whitespace-nowrap {{ $tab === 'expired' ? 'bg-slate-600 text-white shadow-2xs' : 'text-slate-600 hover:bg-slate-100' }}">
                    Kedaluwarsa ({{ $stats['expired'] }})
                </a>
                <a href="{{ route('warning-letters.index', array_merge(request()->query(), ['tab' => 'rejected'])) }}"
                   class="px-3 py-1.5 rounded-xl transition-all whitespace-nowrap {{ $tab === 'rejected' ? 'bg-rose-800 text-white shadow-2xs' : 'text-slate-600 hover:bg-slate-100' }}">
                    Ditolak ({{ $stats['rejected'] }})
                </a>
                <a href="{{ route('warning-letters.index', array_merge(request()->query(), ['tab' => 'cancelled'])) }}"
                   class="px-3 py-1.5 rounded-xl transition-all whitespace-nowrap {{ $tab === 'cancelled' ? 'bg-rose-950 text-white shadow-2xs' : 'text-slate-600 hover:bg-slate-100' }}">
                    Dibatalkan ({{ $stats['cancelled'] }})
                </a>
            </div>

            <!-- Search Form -->
            <form method="GET" action="{{ route('warning-letters.index') }}" class="flex items-center gap-2">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <div class="relative w-full sm:w-64">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari No. Surat / NIK / Nama..."
                           class="w-full pl-8 pr-3 py-1.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                </div>
                <button type="submit" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors">
                    Filter
                </button>
                @if(request('q') || request('tingkat') || request('entity'))
                <a href="{{ route('warning-letters.index', ['tab' => $tab]) }}" class="px-2 py-1.5 text-xs text-rose-600 hover:underline" title="Reset filter">
                    Reset
                </a>
                @endif
            </form>
        </div>

        <!-- Table Listing -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600 divide-y divide-slate-100">
                <thead class="bg-slate-50/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4 w-12 text-center">No</th>
                        <th class="py-3.5 px-4">No. Surat & Jenis SP</th>
                        <th class="py-3.5 px-4">Karyawan</th>
                        <th class="py-3.5 px-4">Entitas & Area</th>
                        <th class="py-3.5 px-4">Butir Pelanggaran</th>
                        <th class="py-3.5 px-4">Masa Berlaku</th>
                        <th class="py-3.5 px-4">Status & Scan TTD</th>
                        <th class="py-3.5 px-4 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($letters as $index => $item)
                    @php
                        $badge = $item->status_badge;
                        $docBadge = $item->signed_doc_status_badge;
                    @endphp
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="py-3 px-4 text-center text-slate-400 font-mono">
                            {{ $letters->firstItem() + $index }}
                        </td>

                        <!-- Nomor Surat & SP -->
                        <td class="py-3 px-4">
                            @if($item->nomor_surat)
                                <div class="font-bold text-slate-900 font-mono text-[11px]">{{ $item->nomor_surat }}</div>
                            @else
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">
                                    <i class="fa-solid fa-clock text-[9px]"></i> Menunggu Penomoran
                                </span>
                            @endif
                            <div class="mt-1 flex items-center gap-1.5">
                                <span class="px-2 py-0.5 rounded-full font-black text-[10px] tracking-wide uppercase
                                    {{ $item->tingkat_sp === 'sp3' ? 'bg-rose-100 text-rose-800 border border-rose-300' : ($item->tingkat_sp === 'sp2' ? 'bg-amber-100 text-amber-800 border border-amber-300' : 'bg-blue-100 text-blue-800 border border-blue-300') }}">
                                    {{ $item->tingkat_sp_code_text }}
                                </span>
                                @if($item->tingkat_sp !== $item->tingkat_sp_diajukan)
                                    <span class="text-[9px] text-slate-400" title="Awal diajukan sebagai {{ strtoupper($item->tingkat_sp_diajukan) }}">
                                        (Adj dari {{ strtoupper($item->tingkat_sp_diajukan) }})
                                    </span>
                                @endif
                            </div>
                        </td>

                        <!-- Karyawan -->
                        <td class="py-3 px-4">
                            <div class="font-bold text-slate-900 hover:text-primary transition-colors">
                                <a href="{{ route('warning-letters.show', $item->id) }}">{{ $item->nama_karyawan }}</a>
                            </div>
                            <div class="text-[10px] text-slate-400 font-mono mt-0.5">
                                NIK: <strong class="text-slate-600">{{ $item->nik }}</strong>
                                @if($item->nip) &bull; NIP: {{ $item->nip }} @endif
                            </div>
                            <div class="text-[11px] text-slate-500 mt-0.5">{{ $item->jabatan ?: '-' }}</div>
                        </td>

                        <!-- Entitas & Area -->
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-1.5">
                                <span class="px-2 py-0.5 rounded font-black text-[10px] bg-slate-800 text-white">
                                    {{ $item->entity }}
                                </span>
                                <span class="font-semibold text-slate-700">{{ $item->area ?: '-' }}</span>
                            </div>
                            <div class="text-[10px] text-slate-400 mt-1 truncate max-w-[150px]" title="{{ $item->prinsiple }}">
                                Mitra: {{ $item->prinsiple ?: 'Internal' }}
                            </div>
                        </td>

                        <!-- Butir Pelanggaran Ringkas -->
                        <td class="py-3 px-4 max-w-xs">
                            <div class="text-xs font-semibold text-slate-800 line-clamp-1">
                                {{ $item->violations->first()?->pelanggaran ?: '-' }}
                            </div>
                            <div class="text-[10px] text-slate-400 mt-0.5">
                                {{ $item->violations->count() }} butir pelanggaran
                                @if($item->pasal_pelanggaran)
                                    &bull; <span class="text-emerald-700 font-semibold">Pasal Disahkan</span>
                                @else
                                    &bull; <span class="text-amber-600 italic">Pasal belum diisi</span>
                                @endif
                            </div>
                        </td>

                        <!-- Tanggal Surat & Expired -->
                        <td class="py-3 px-4 whitespace-nowrap">
                            @if($item->status === 'approved')
                                <div class="text-slate-800 font-semibold">{{ $item->tanggal_surat ? $item->tanggal_surat->format('d/m/Y') : '-' }}</div>
                                <div class="text-[10px] text-slate-400 mt-0.5">
                                    Exp: <span class="font-bold text-slate-600">{{ $item->tanggal_expired ? $item->tanggal_expired->format('d/m/Y') : '-' }}</span>
                                </div>
                            @else
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">
                                    <i class="fa-solid fa-clock text-[9px]"></i> Menunggu Rilis
                                </span>
                                <div class="text-[9px] text-slate-400 mt-0.5">
                                    Diajukan: {{ $item->created_at ? $item->created_at->format('d/m/Y') : '-' }}
                                </div>
                            @endif
                        </td>

                        <!-- Status & Scan TTD -->
                        <td class="py-3 px-4 whitespace-nowrap space-y-1">
                            <!-- Status Workflow -->
                            <div>
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $badge['bg'] }} {{ $badge['text'] }} {{ $badge['border'] }}">
                                    <i class="fa-solid {{ $badge['icon'] }} text-[9px]"></i>
                                    <span>{{ $badge['label'] }}</span>
                                </span>
                            </div>

                            <!-- Indikator Scan Fisik Bertandatangan: MERAH jika belum, HIJAU jika sudah -->
                            @if($item->status === 'approved')
                            <div>
                                @if($item->has_signed_doc)
                                    <a href="{{ route('warning-letters.download-signed', $item->id) }}" target="_blank"
                                       class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300 hover:bg-emerald-200 transition-colors"
                                       title="Lihat berkas fisik tanda tangan">
                                        <i class="fa-solid fa-file-circle-check text-[9px] text-emerald-600"></i>
                                        <span>Scan TTD Lengkap</span>
                                    </a>
                                @else
                                    <a href="{{ route('warning-letters.show', $item->id) }}#upload-ttd"
                                       class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-300 hover:bg-rose-200 transition-colors animate-pulse"
                                       title="Klik untuk mengunggah scan tanda tangan 3 pihak">
                                        <i class="fa-solid fa-triangle-exclamation text-[9px] text-rose-600"></i>
                                        <span>Scan TTD Belum Ada</span>
                                    </a>
                                @endif
                            </div>
                            @endif
                        </td>

                        <!-- Aksi -->
                        <td class="py-3 px-4 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <!-- Detail (Selalu tampil untuk semua) -->
                                <a href="{{ route('warning-letters.show', $item->id) }}" 
                                   class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-primary-50 text-slate-600 hover:text-primary flex items-center justify-center transition-colors"
                                   title="Lihat Detail & Kronologi">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                </a>

                                <!-- AKSI PIMPINAN: Hanya ketika stepnya review pimpinan -->
                                @if(!$isHrdOrAdmin && $item->status === 'review_head' && $item->canUserApproveHead(Auth::user()))
                                    <a href="{{ route('warning-letters.show', $item->id) }}" 
                                       class="w-7 h-7 rounded-lg bg-blue-600 hover:bg-blue-700 text-white flex items-center justify-center transition-colors shadow-2xs"
                                       title="Review & Setujui Pimpinan">
                                        <i class="fa-solid fa-user-check text-xs"></i>
                                    </a>
                                @endif

                                <!-- AKSI HRD: HRD bisa approve kapanpun (baik review_head maupun review_hrd) -->
                                @if($isHrdOrAdmin && in_array($item->status, ['review_head', 'review_hrd']))
                                    <a href="{{ route('warning-letters.review-hrd', $item->id) }}" 
                                       class="w-7 h-7 rounded-lg bg-amber-500 hover:bg-amber-600 text-white flex items-center justify-center transition-colors shadow-2xs"
                                       title="Review & Terbitkan Nomor Resmi (Khusus HRD)">
                                        <i class="fa-solid fa-stamp text-xs"></i>
                                    </a>
                                @endif

                                <!-- Cetak PDF Resmi (Jika approved) -->
                                @if($item->status === 'approved')
                                    <a href="{{ route('warning-letters.print-pdf', $item->id) }}" target="_blank"
                                       class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition-colors"
                                       title="Cetak PDF Resmi (Kop Surat & QR Code)">
                                        <i class="fa-solid fa-file-pdf text-xs"></i>
                                    </a>
                                @endif

                                <!-- AKSI CANCEL HRD: HRD bisa cancel data kapanpun (baik masih review ataupun sudah selesai) -->
                                @if($isHrdOrAdmin && $item->status !== 'cancelled')
                                    <button type="button" 
                                            onclick="openCancelModal({{ $item->id }}, '{{ $item->nomor_surat ?: ('SP #' . $item->id) }}')"
                                            class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-rose-100 text-slate-400 hover:text-rose-600 flex items-center justify-center transition-colors cursor-pointer"
                                            title="Batalkan Surat Peringatan (Khusus HRD)">
                                        <i class="fa-solid fa-ban text-xs"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-slate-400">
                            <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                            </div>
                            <div class="text-sm font-bold text-slate-700">Belum Ada Data Surat Peringatan</div>
                            <div class="text-xs text-slate-400 mt-1">Tidak ada data SP yang sesuai dengan kriteria filter saat ini.</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($letters->hasPages())
        <div class="p-4 border-t border-slate-100">
            {{ $letters->links() }}
        </div>
        @endif
    </div>

    <!-- Modal Batalkan SP untuk HRD -->
    @if($isHrdOrAdmin)
    <div id="indexCancelModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl border border-slate-200 overflow-hidden transform transition-all animate-in fade-in zoom-in duration-200">
            <div class="p-6">
                <div class="w-12 h-12 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-ban text-xl"></i>
                </div>
                <h3 class="text-base font-extrabold text-slate-800 text-center">Batalkan Surat Peringatan?</h3>
                <p class="text-xs text-slate-500 text-center mt-1">
                    Anda akan membatalkan surat <span id="cancelDocLabel" class="font-bold text-slate-800 font-mono"></span>. Data ini akan tercatat resmi sebagai Dibatalkan.
                </p>

                <form id="indexCancelForm" method="POST" action="" class="mt-4 space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Alasan Pembatalan (HRD) <span class="text-rose-500">*</span></label>
                        <textarea name="alasan_pembatalan" rows="3" required placeholder="Tuliskan alasan pembatalan resmi dari pihak HRD..."
                                  class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 outline-none"></textarea>
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" onclick="closeCancelModal()"
                                class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors cursor-pointer">
                            Kembali
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 transition-colors shadow-xs cursor-pointer">
                            Ya, Batalkan SP
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openCancelModal(id, label) {
            document.getElementById('cancelDocLabel').innerText = label;
            document.getElementById('indexCancelForm').action = "{{ url('warning-letters') }}/" + id + "/cancel";
            document.getElementById('indexCancelModal').classList.remove('hidden');
        }
        function closeCancelModal() {
            document.getElementById('indexCancelModal').classList.add('hidden');
        }
    </script>
    @endif

</div>
@endsection
