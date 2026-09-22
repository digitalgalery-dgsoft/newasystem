@extends('layouts.app')

@section('title', 'Hasil Test - ' . $candidate->full_name)
@section('page_title', 'HASIL TEST KANDIDAT')
@section('breadcrumb_active', 'Detail Evaluasi')

@section('content')
<div x-data="{ 
    activeTab: 'interview', 
    alihkanModalOpen: false,
    gantiAreaModalOpen: false,
    editPrincipleModal: false, 
    archiveModal: false,
    computerEnabled: true,
    photoUploadModal: false
}" class="space-y-6">

    <!-- TOP BAR / BREADCRUMB & HEADER (Identik dengan Detail Kandidat Portal) -->
    <div class="page-header-card flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('interview.index') }}" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition-colors shadow-xs">
                    <i class="fa-solid fa-arrow-left text-sm"></i>
                </a>
                <h1 class="text-xl font-bold text-slate-900 tracking-tight">{{ $candidate->full_name }}</h1>
                <span class="badge-pill bg-blue-50 text-blue-700 border-blue-200">
                    {{ $candidate->status_kandidat ?? $candidate->status ?? 'Interview' }}
                </span>
            </div>
            <div class="text-xs text-slate-500 mt-1 flex items-center gap-2">
                <span>Posisi: <b class="text-slate-800">{{ $candidate->applied_job ?? $candidate->position ?? '-' }}</b></span>
                <span>•</span>
                <span>Area: <b class="text-slate-800">{{ $candidate->area ?? 'JAKARTA' }}</b></span>
            </div>
        </div>

        <!-- ACTION BUTTONS -->
        <div class="flex items-center flex-wrap gap-2">
            <!-- Toggle Tes Komputer -->
            <button @click="computerEnabled = !computerEnabled" 
                    type="button" 
                    class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-bold transition-all shadow-sm"
                    :class="computerEnabled ? 'bg-amber-500 hover:bg-amber-600 text-white shadow-amber-500/20' : 'bg-slate-600 hover:bg-slate-700 text-white shadow-slate-600/20'">
                <i class="fa-solid fa-laptop-code text-xs"></i>
                <span x-text="computerEnabled ? 'Tes Komputer: Set OFF (Disable)' : 'Tes Komputer: Set ON (Enable)'">Tes Komputer: Set OFF (Disable)</span>
            </button>

            <!-- Alihkan ke AS -->
            <button @click="alihkanModalOpen = true" type="button" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 shadow-sm transition-all">
                <i class="fa-solid fa-user-plus text-primary"></i>
                <span>Alihkan ke AS</span>
            </button>

            <!-- Cek Status Odoo -->
            <form action="{{ route('interview.sync_single_odoo', $candidate->id) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-purple-700 bg-purple-50 hover:bg-purple-100 border border-purple-200 shadow-sm transition-all" title="Cek Status Seleksi di Odoo ERP">
                    <i class="fa-solid fa-arrows-rotate text-purple-600"></i>
                    <span>Cek Status Odoo</span>
                </button>
            </form>

            <!-- Ganti Area / Prinsiple -->
            <button @click="gantiAreaModalOpen = true" type="button" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200 shadow-sm transition-all" title="Ganti Area & Prinsiple Penempatan">
                <i class="fa-solid fa-location-dot text-amber-600"></i>
                <span>Ganti Area / Prinsiple</span>
            </button>

            <!-- Arsipkan / Aktifkan Kembali -->
            @if(in_array($candidate->status, ['Arsip', 'archived']) || $candidate->status_kandidat === 'Arsip')
                <form action="{{ route('interview.unarchive', $candidate->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin mengaktifkan kembali kandidat {{ addslashes($candidate->full_name) }} ke daftar interview?');">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-300 shadow-sm transition-all" title="Aktifkan kembali ke daftar interview aktif">
                        <i class="fa-solid fa-box-open text-emerald-600"></i>
                        <span>Aktifkan Kembali</span>
                    </button>
                </form>
            @else
                <button @click="archiveModal = true" type="button" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 shadow-sm transition-all">
                    <i class="fa-solid fa-box-archive"></i>
                    <span>Arsipkan</span>
                </button>
            @endif

            <!-- Download PDF (Blue Button) -->
            @if(!empty($isUserPrinsipleDisabled))
                <button type="button" 
                        disabled 
                        title="{{ implode(' &#10; ', $userPrinsipleDisableReasons ?? ['Download dinonaktifkan: kandidat tidak memenuhi syarat kelulusan']) }}"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-slate-400 bg-slate-100 border border-dashed border-slate-300 cursor-not-allowed opacity-60 shadow-none">
                    <i class="fa-solid fa-file-pdf text-rose-400"></i>
                    <span>Download All Document</span>
                    <span class="px-1.5 py-0.5 text-[9px] font-bold rounded-md bg-rose-100 text-rose-700 border border-rose-200 uppercase tracking-tight">Disabled</span>
                </button>
            @else
                <a href="{{ route('interview.pdf', $candidate->id) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white bg-primary hover:bg-primary-700 shadow-md shadow-primary-500/20 transition-all">
                    <i class="fa-solid fa-file-pdf"></i>
                    <span>Download All Document</span>
                </a>
            @endif
        </div>
    </div>

    <!-- NOTIFIKASI KANDIDAT BERSTATUS ARSIP -->
    @if(in_array($candidate->status, ['Arsip', 'archived']) || $candidate->status_kandidat === 'Arsip')
    <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 text-xs text-amber-900 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center text-lg flex-shrink-0 shadow-sm">
                <i class="fa-solid fa-box-archive"></i>
            </div>
            <div>
                <h4 class="font-bold text-sm text-amber-950">Kandidat Ini Sedang Berada di Arsip</h4>
                <p class="text-[11px] text-amber-800 mt-0.5">
                    Alasan Diarsipkan: <span class="font-semibold italic text-rose-700">{{ $candidate->archive_reason ?? 'Tidak ada catatan alasan' }}</span>
                </p>
            </div>
        </div>

        <form action="{{ route('interview.unarchive', $candidate->id) }}" method="POST" onsubmit="return confirm('Aktifkan kembali kandidat {{ addslashes($candidate->full_name) }} ke daftar interview aktif?');">
            @csrf
            <button type="submit" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold transition flex items-center gap-2 shadow-md shadow-emerald-600/20 whitespace-nowrap">
                <i class="fa-solid fa-box-open text-xs"></i>
                <span>Aktifkan Kembali ke Interview</span>
            </button>
        </form>
    </div>
    @endif

    <!-- STATUS REKRUTMEN ODOO ERP BANNER -->
    @php $odooBadge = $candidate->odoo_badge_info; @endphp
    <div class="bg-gradient-to-r {{ $candidate->odoo_stage_name ? 'from-purple-50 via-indigo-50 to-blue-50 border-purple-200' : 'from-slate-50 to-slate-100 border-slate-200' }} border rounded-2xl p-4 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl {{ $candidate->odoo_stage_name ? 'bg-purple-600 text-white shadow-md shadow-purple-600/20' : 'bg-slate-200 text-slate-500' }} flex items-center justify-center text-lg font-bold flex-shrink-0">
                <i class="fa-solid fa-arrows-split-up-and-left"></i>
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Status Rekrutmen Odoo ERP:</span>
                    @if($candidate->odoo_stage_name)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-black border {{ $odooBadge['class'] }}">
                            <i class="{{ $odooBadge['icon'] }}"></i>
                            <span>{{ $candidate->odoo_stage_name }}</span>
                        </span>
                        @if($candidate->odoo_entity)
                            <span class="px-2 py-0.5 rounded-md bg-purple-100 text-purple-800 text-[10px] font-extrabold border border-purple-200">
                                {{ $candidate->odoo_entity }}
                            </span>
                        @endif
                    @else
                        <span class="px-2.5 py-0.5 rounded-lg bg-slate-200 text-slate-600 text-xs font-semibold">
                            Belum Terdaftar di Odoo
                        </span>
                    @endif
                </div>
                <div class="text-[11px] text-slate-500 mt-1 flex items-center gap-3 flex-wrap">
                    @if($candidate->odoo_applicant_id)
                        <span><i class="fa-solid fa-hashtag text-slate-400"></i> Applicant ID: <b>#{{ $candidate->odoo_applicant_id }}</b></span>
                    @endif
                    @if($candidate->odoo_synced_at)
                        <span><i class="fa-regular fa-clock text-slate-400"></i> Terakhir Dicek: <b>{{ $candidate->odoo_synced_at->diffForHumans() }}</b> ({{ $candidate->odoo_synced_at->format('d/m/Y H:i') }})</span>
                    @endif
                    <span><i class="fa-solid fa-id-badge text-slate-400"></i> NIK: <b class="font-mono">{{ $candidate->nik }}</b></span>
                </div>
            </div>
        </div>

        <form action="{{ route('interview.sync_single_odoo', $candidate->id) }}" method="POST">
            @csrf
            <button type="submit" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold text-purple-700 bg-white hover:bg-purple-50 border border-purple-200 shadow-sm transition-all whitespace-nowrap">
                <i class="fa-solid fa-arrows-rotate text-purple-600"></i>
                <span>Cek Status Terkini</span>
            </button>
    </div>

    <!-- PROFIL KANDIDAT CARD REGULER (11 DATA POINTS + FOTO DROPZONE) -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
        <div class="flex flex-col lg:flex-row gap-6">
            
            <!-- Left: 11 Data Points Table -->
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-7 h-7 rounded-lg bg-blue-50 text-primary flex items-center justify-center font-bold text-xs">
                        <i class="fa-solid fa-id-card"></i>
                    </div>
                    <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Profil Lengkap Pelamar</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-3 text-xs">
                    <!-- 1. No KTP -->
                    <div class="flex items-start gap-2">
                        <span class="w-32 text-slate-400 font-medium flex-shrink-0">No. KTP / NIK:</span>
                        <code class="font-mono font-bold text-slate-800 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">{{ $candidate->nik }}</code>
                    </div>

                    <!-- 2. Nama Lengkap -->
                    <div class="flex items-start gap-2">
                        <span class="w-32 text-slate-400 font-medium flex-shrink-0">Nama Lengkap:</span>
                        <span class="font-bold text-slate-900">{{ $candidate->full_name }}</span>
                    </div>

                    <!-- Jenis Kelamin -->
                    <div class="flex items-start gap-2">
                        <span class="w-32 text-slate-400 font-medium flex-shrink-0">Jenis Kelamin:</span>
                        <span class="font-bold text-slate-800">
                            @if(in_array(strtolower($candidate->gender ?? ''), ['perempuan', 'female']))
                                <span class="inline-flex items-center gap-1 text-pink-600 font-bold"><i class="fa-solid fa-venus"></i> Perempuan</span>
                            @else
                                <span class="inline-flex items-center gap-1 text-blue-600 font-bold"><i class="fa-solid fa-mars"></i> Laki-laki</span>
                            @endif
                        </span>
                    </div>

                    <!-- 3. Alamat KTP -->
                    <div class="flex items-start gap-2 md:col-span-2">
                        <span class="w-32 text-slate-400 font-medium flex-shrink-0">Alamat KTP:</span>
                        <span class="text-slate-700 font-medium leading-relaxed">{{ $candidate->address_ktp ?? '-' }}</span>
                    </div>

                    <!-- 4. Usia & Tgl Lahir -->
                    <div class="flex items-start gap-2">
                        <span class="w-32 text-slate-400 font-medium flex-shrink-0">Tgl. Lahir & Usia:</span>
                        <span class="text-slate-800 font-semibold">{{ $candidate->formatted_birth_date }} (<b>{{ $candidate->age }} Tahun</b>)</span>
                    </div>

                    <!-- 5. Pendidikan Terakhir -->
                    <div class="flex items-start gap-2">
                        <span class="w-32 text-slate-400 font-medium flex-shrink-0">Pendidikan:</span>
                        <span class="font-bold text-slate-800">{{ $candidate->education ?? '-' }}</span>
                    </div>

                    <!-- 6. Mobile / WhatsApp -->
                    <div class="flex items-start gap-2">
                        <span class="w-32 text-slate-400 font-medium flex-shrink-0">Nomor WhatsApp:</span>
                        <a href="https://api.whatsapp.com/send?phone={{ $candidate->clean_whatsapp }}" target="_blank" class="text-emerald-700 font-bold hover:underline flex items-center gap-1">
                            <i class="fa-brands fa-whatsapp text-emerald-600"></i>
                            <span>{{ $candidate->phone ?? '-' }}</span>
                        </a>
                    </div>

                    <!-- 7. Prinsiple & Area -->
                    <div class="flex items-start gap-2">
                        <span class="w-32 text-slate-400 font-medium flex-shrink-0">Prinsiple & Area:</span>
                        <span class="font-bold text-primary">{{ $candidate->principle ? $candidate->principle->name : 'PT ARINA MULTI KARYA' }}</span>
                        <span class="text-slate-400"> - {{ $candidate->area ?? 'JAKARTA' }}</span>
                    </div>

                    <!-- 8. Posisi Dilamar -->
                    <div class="flex items-start gap-2">
                        <span class="w-32 text-slate-400 font-medium flex-shrink-0">Posisi Dilamar:</span>
                        <span class="font-bold text-slate-900">{{ $candidate->applied_job ?? $candidate->position ?? '-' }}</span>
                    </div>

                    <!-- 9. Status Seleksi -->
                    <div class="flex items-start gap-2">
                        <span class="w-32 text-slate-400 font-medium flex-shrink-0">Status Seleksi:</span>
                        <span class="badge-pill bg-blue-50 text-blue-700 border-blue-200">
                            {{ $candidate->status_kandidat ?? $candidate->status ?? 'Interview' }}
                        </span>
                    </div>

                    <!-- 10. Nama User / AS -->
                    <div class="flex items-start gap-2">
                        <span class="w-32 text-slate-400 font-medium flex-shrink-0">User AS / Rekruter:</span>
                        <span class="text-slate-700 font-medium">{{ $candidate->user_display_name }}</span>
                    </div>

                    <!-- 11. Lampiran CV -->
                    <div class="flex items-start gap-2">
                        <span class="w-32 text-slate-400 font-medium flex-shrink-0">Lampiran CV:</span>
                        @if($candidate->cv_path)
                            @php
                                $cvExt = strtolower(pathinfo($candidate->cv_path, PATHINFO_EXTENSION));
                                $isCvImage = in_array($cvExt, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                            @endphp
                            <a href="{{ $candidate->cv_url }}" target="_blank" 
                               onclick="openCandidateMedia('{{ $isCvImage ? 'image' : 'pdf' }}', '{{ $candidate->cv_url }}', 'Lampiran CV: {{ addslashes($candidate->full_name) }}'); return false;"
                               class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 font-bold text-xs border border-emerald-200 transition-colors shadow-sm cursor-pointer" title="Lihat Berkas CV">
                                <i class="fa-solid fa-file-pdf text-emerald-600"></i>
                                <span>Lihat File CV</span>
                                <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>
                            </a>
                        @else
                            <span class="text-slate-400 text-xs italic">Belum ada lampiran CV</span>
                        @endif
                    </div>

                    <!-- 12. Catatan Khusus -->
                    <div class="flex items-start gap-2 md:col-span-2">
                        <span class="w-32 text-slate-400 font-medium flex-shrink-0">Catatan Khusus:</span>
                        <div class="space-y-1.5 flex-1">
                            @if(!empty($catatanRekomendasi))
                                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold shadow-2xs">
                                    <i class="fa-solid fa-triangle-exclamation text-rose-500 text-xs"></i>
                                    <span>{{ $catatanRekomendasi }}</span>
                                </div>
                            @endif
                            <span class="text-slate-600 italic block">{{ $candidate->notes ?? (!empty($catatanRekomendasi) ? '' : 'Tidak ada catatan tambahan.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Foto Profil 3x4 & Lampiran CV (Side-by-Side dengan Preview Modal) -->
            @php
                $cvUrl = $candidate->cv_url;
                $cvExt = $candidate->cv_path ? strtolower(pathinfo($candidate->cv_path, PATHINFO_EXTENSION)) : '';
                $isCvImage = in_array($cvExt, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
            @endphp
            <div class="w-full lg:w-auto flex flex-row flex-wrap sm:flex-nowrap items-stretch justify-center gap-3.5 p-3.5 bg-slate-50/90 rounded-2xl border border-slate-200 shadow-2xs">
                
                <!-- 1. FOTO RESMI PELAMAR (3x4) -->
                <div class="w-36 p-2 rounded-xl bg-white border border-slate-200/90 shadow-2xs hover:border-primary-400 hover:shadow-md transition-all flex flex-col items-center justify-between group cursor-pointer"
                     onclick="openCandidateMedia('image', '{{ $candidate->photo_url }}', 'Foto Resmi: {{ addslashes($candidate->full_name) }}')"
                     title="Klik untuk melihat preview foto">
                    <div class="relative w-32 h-40 rounded-lg overflow-hidden border border-slate-200 bg-slate-100 flex flex-col items-center justify-center shadow-inner">
                        @if($candidate->photo_path)
                            <img id="photoPreview" src="{{ $candidate->photo_url }}" alt="Foto {{ $candidate->full_name }}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($candidate->full_name) }}&background=0F52BA&color=fff&size=256';">
                        @else
                            <div id="photoPlaceholder" class="flex flex-col items-center text-slate-400 p-2">
                                <i class="fa-solid fa-camera text-2xl mb-1 text-slate-300 group-hover:text-primary transition-colors"></i>
                                <span class="text-[11px] font-bold">Pasfoto 3x4</span>
                                <span class="text-[9px] text-slate-400 mt-0.5">JPG / PNG</span>
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 flex flex-col items-center justify-center text-white text-xs font-semibold transition-opacity gap-1 backdrop-blur-[1px]">
                            <i class="fa-solid fa-magnifying-glass-plus text-base"></i>
                            <span class="text-[10px] font-bold tracking-wider uppercase">Preview</span>
                        </div>
                    </div>
                    <div class="mt-2 text-center">
                        <span class="text-[11px] font-bold text-slate-700 block leading-tight">Foto Resmi Pelamar</span>
                        <span class="text-[9px] text-slate-400 block mt-0.5">Ukuran 3x4 • Klik Preview</span>
                    </div>
                </div>

                <!-- 2. LAMPIRAN BERKAS CV -->
                @if($candidate->cv_path)
                    <div class="w-36 p-2 rounded-xl bg-white border border-slate-200/90 shadow-2xs hover:border-emerald-500 hover:shadow-md transition-all flex flex-col items-center justify-between group cursor-pointer"
                         onclick="openCandidateMedia('{{ $isCvImage ? 'image' : 'pdf' }}', '{{ $cvUrl }}', 'Lampiran CV: {{ addslashes($candidate->full_name) }}')"
                         title="Klik untuk membuka preview CV">
                        <div class="relative w-32 h-40 rounded-lg overflow-hidden border border-slate-200 bg-slate-50 flex flex-col items-center justify-center p-2 text-center group-hover:bg-emerald-50/30 transition-colors shadow-inner">
                            @if($isCvImage)
                                <img src="{{ $cvUrl }}" alt="CV {{ $candidate->full_name }}" class="w-full h-full object-cover rounded transition-transform duration-300 group-hover:scale-105">
                            @else
                                <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 border border-rose-100 flex items-center justify-center text-2xl mb-1.5 shadow-2xs group-hover:scale-110 transition-transform">
                                    <i class="fa-solid fa-file-pdf"></i>
                                </div>
                                <span class="text-[10px] font-bold text-slate-800 line-clamp-2 px-1 break-all leading-snug">{{ basename($candidate->cv_path) }}</span>
                                <span class="inline-flex items-center gap-1 text-[8.5px] font-extrabold text-emerald-700 bg-emerald-100/90 px-1.5 py-0.5 rounded-full mt-1.5">
                                    <i class="fa-solid fa-circle-check text-[7.5px]"></i> Berkas CV
                                </span>
                            @endif
                            <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 flex flex-col items-center justify-center text-white text-xs font-semibold transition-opacity gap-1 backdrop-blur-[1px]">
                                <i class="fa-solid fa-eye text-base"></i>
                                <span class="text-[10px] font-bold tracking-wider uppercase">Preview CV</span>
                            </div>
                        </div>
                        <div class="mt-2 text-center">
                            <span class="text-[11px] font-bold text-slate-700 block leading-tight">Lampiran Berkas CV</span>
                            <span class="text-[9px] text-emerald-600 font-bold flex items-center justify-center gap-1 mt-0.5">
                                <i class="fa-solid fa-file-lines text-[8px]"></i> Klik Buka CV
                            </span>
                        </div>
                    </div>
                @else
                    <div class="w-36 p-2 rounded-xl bg-white/70 border border-dashed border-slate-300 opacity-80 flex flex-col items-center justify-between text-center cursor-not-allowed"
                         onclick="alert('Kandidat ini belum mengunggah berkas lampiran CV.')"
                         title="Belum ada lampiran CV">
                        <div class="w-32 h-40 rounded-lg bg-slate-50 border border-slate-100 flex flex-col items-center justify-center p-2 text-slate-400">
                            <i class="fa-regular fa-file-pdf text-2xl mb-1.5 text-slate-300"></i>
                            <span class="text-[11px] font-bold text-slate-500">Belum Ada CV</span>
                            <span class="text-[9px] text-slate-400 mt-0.5">Tidak terlampir</span>
                        </div>
                        <div class="mt-2 text-center">
                            <span class="text-[11px] font-bold text-slate-400 block leading-tight">Lampiran Berkas CV</span>
                            <span class="text-[9px] text-slate-400 block mt-0.5">Belum Diunggah</span>
                        </div>
                    </div>
                @endif

            </div>

        </div>
    </div>

    <!-- 7 Tabs Navigation Bar (Modern Attendance Tabs) -->
    <div class="bg-slate-100/80 border border-slate-200 rounded-2xl p-1.5 shadow-inner flex items-center gap-1.5 overflow-x-auto scrollbar-thin">
        <button @click="activeTab = 'interview'" 
                :class="activeTab === 'interview' ? 'bg-primary-600 text-white shadow-md shadow-primary-500/25 font-bold' : 'text-slate-600 hover:text-primary-600 hover:bg-white font-semibold'" 
                class="px-4 py-2.5 rounded-xl text-xs md:text-sm transition-all duration-150 flex items-center gap-2 whitespace-nowrap">
            <i class="fa-solid fa-clipboard-check text-xs"></i>
            <span>1. Hasil Interview</span>
        </button>

        <button @click="activeTab = 'refcek'" 
                :class="activeTab === 'refcek' ? 'bg-primary-600 text-white shadow-md shadow-primary-500/25 font-bold' : 'text-slate-600 hover:text-primary-600 hover:bg-white font-semibold'" 
                class="px-4 py-2.5 rounded-xl text-xs md:text-sm transition-all duration-150 flex items-center gap-2 whitespace-nowrap">
            <i class="fa-solid fa-phone-volume text-xs"></i>
            <span>2. Referensi Cek</span>
        </button>

        <button @click="activeTab = 'kompt'" 
                :class="activeTab === 'kompt' ? 'bg-primary-600 text-white shadow-md shadow-primary-500/25 font-bold' : 'text-slate-600 hover:text-primary-600 hover:bg-white font-semibold'" 
                class="px-4 py-2.5 rounded-xl text-xs md:text-sm transition-all duration-150 flex items-center gap-2 whitespace-nowrap">
            <i class="fa-solid fa-laptop-code text-xs"></i>
            <span>3. Tes Komputer</span>
        </button>

        <button @click="activeTab = 'kepribadian'" 
                :class="activeTab === 'kepribadian' ? 'bg-primary-600 text-white shadow-md shadow-primary-500/25 font-bold' : 'text-slate-600 hover:text-primary-600 hover:bg-white font-semibold'" 
                class="px-4 py-2.5 rounded-xl text-xs md:text-sm transition-all duration-150 flex items-center gap-2 whitespace-nowrap">
            <i class="fa-solid fa-brain text-xs"></i>
            <span>4. Tes Kepribadian</span>
        </button>

        <button @click="activeTab = 'matematika'" 
                :class="activeTab === 'matematika' ? 'bg-primary-600 text-white shadow-md shadow-primary-500/25 font-bold' : 'text-slate-600 hover:text-primary-600 hover:bg-white font-semibold'" 
                class="px-4 py-2.5 rounded-xl text-xs md:text-sm transition-all duration-150 flex items-center gap-2 whitespace-nowrap">
            <i class="fa-solid fa-calculator text-xs"></i>
            <span>5. Tes Matematika</span>
        </button>

        <!-- 6. Analisa AI (CV Analyzer) (Tab No. 6 Sesuai Permintaan User!) -->
        <button @click="activeTab = 'ai'" 
                :class="activeTab === 'ai' ? 'bg-primary-600 text-white shadow-md shadow-primary-500/25 font-bold' : 'text-slate-600 hover:text-primary-600 hover:bg-white font-semibold'" 
                class="px-4 py-2.5 rounded-xl text-xs md:text-sm transition-all duration-150 flex items-center gap-2 whitespace-nowrap">
            <i class="fa-solid fa-wand-magic-sparkles text-amber-500 text-xs"></i>
            <span>6. Analisa AI (CV Analyzer)</span>
        </button>

        <!-- 7. User Principle / Approver Inhouse -->
        @if(!empty($isInhouseCandidate))
            <button @click="activeTab = 'userprinsiple'" 
                    :class="activeTab === 'userprinsiple' ? 'bg-primary-600 text-white shadow-md shadow-primary-500/25 font-bold' : 'text-slate-600 hover:text-primary-600 hover:bg-white font-semibold'" 
                    class="px-4 py-2.5 rounded-xl text-xs md:text-sm transition-all duration-150 flex items-center gap-2 whitespace-nowrap">
                <i class="fa-solid fa-house-chimney-user text-xs"></i>
                <span>Approval Inhouse</span>
            </button>
        @elseif(!empty($isUserPrinsipleDisabled))
            <button type="button" 
                    disabled 
                    title="{{ implode(' &#10; ', $userPrinsipleDisableReasons ?? []) }}"
                    class="px-4 py-2.5 rounded-xl text-xs md:text-sm transition-all duration-150 flex items-center gap-2 whitespace-nowrap bg-slate-100 text-slate-400 opacity-60 cursor-not-allowed border border-dashed border-slate-300">
                <i class="fa-solid fa-ban text-rose-500 text-xs"></i>
                <span>7. User Principle</span>
                <span class="px-1.5 py-0.5 text-[9px] font-bold rounded-md bg-rose-100 text-rose-700 border border-rose-200 uppercase tracking-tight">Disabled</span>
            </button>
        @else
            <button @click="activeTab = 'userprinsiple'" 
                    :class="activeTab === 'userprinsiple' ? 'bg-primary-600 text-white shadow-md shadow-primary-500/25 font-bold' : 'text-slate-600 hover:text-primary-600 hover:bg-white font-semibold'" 
                    class="px-4 py-2.5 rounded-xl text-xs md:text-sm transition-all duration-150 flex items-center gap-2 whitespace-nowrap">
                <i class="fa-solid fa-building-circle-check text-xs"></i>
                <span>7. User Principle</span>
            </button>
        @endif
    </div>

    <!-- TAB CONTENTS CONTAINER -->
    <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm">

        <!-- ============================================================= -->
        <!-- TAB 1: HASIL INTERVIEW (Matching Image 1) -->
        <!-- ============================================================= -->
        <div x-show="activeTab === 'interview'" class="space-y-6">
            @php
                $assess = $candidate->interviewAssessment;
                $currentUser = $user ?? auth()->user();
                $currentUserId = $currentUser?->id;
                $currentUserName = $currentUser?->name ?? 'User AS';

                // 1. Tanda tangan tersimpan milik User AS yang sedang login (untuk tombol "Tempel TTD Saya")
                $mySavedSigUrl = null;
                if ($currentUser && !empty($currentUser->signature_path)) {
                    $mySavedSigUrl = $currentUser->getSignatureBase64();
                }

                // 2. Resolve AS yang ditugaskan untuk kandidat ini ("AS Sendiri")
                $asDetails = \App\Http\Controllers\InterviewController::resolveCandidateAsDetails($candidate, $currentUser);
                $candidateAsUser = $asDetails['user'] ?? null;
                $candidateAsName = $asDetails['name'] ?? $currentUserName;
                $candidateAsSigUrl = null;
                if ($candidateAsUser && !empty($candidateAsUser->signature_path)) {
                    $candidateAsSigUrl = $candidateAsUser->getSignatureBase64();
                }

                // 3. Tanda tangan yang sudah tersimpan pada penilaian kandidat ini (jika sudah dinilai sebelumnya)
                $assessSigUrl = null;
                $assessSigPath = $assess?->interviewer_signature_path;
                if (!empty($assessSigPath)) {
                    if (str_starts_with($assessSigPath, 'data:image')) {
                        $assessSigUrl = $assessSigPath;
                    } elseif (\Illuminate\Support\Facades\Storage::disk('public')->exists($assessSigPath)) {
                        $assessSigUrl = 'data:image/png;base64,' . base64_encode(\Illuminate\Support\Facades\Storage::disk('public')->get($assessSigPath));
                    } elseif (file_exists(public_path($assessSigPath))) {
                        $assessSigUrl = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path($assessSigPath)));
                    } elseif (file_exists(storage_path('app/public/' . $assessSigPath))) {
                        $assessSigUrl = 'data:image/png;base64,' . base64_encode(file_get_contents(storage_path('app/public/' . $assessSigPath)));
                    }
                }

                // 4. Prioritas default:
                // - Jika sudah ada TTD penilaian kandidat -> gunakan TTD penilaian ($assessSigUrl)
                // - Jika belum ada, dan AS sendiri milik kandidat punya TTD -> gunakan TTD AS sendiri ($candidateAsSigUrl)
                // - Jika memang belum ada -> KOSONG (null)
                $initialSigUrl = $assessSigUrl ?: $candidateAsSigUrl;
            @endphp
            <form action="{{ route('interview.assess', $candidate->id) }}" method="POST" id="interviewForm" class="space-y-6">
                @csrf
                <input type="hidden" name="signature_data" id="signatureDataInput" value="{{ $initialSigUrl ?? '' }}">

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    
                    <!-- Left: Matrix Table & Form Fields -->
                    <div class="lg:col-span-8 space-y-5">
                        
                        <!-- 4 Pillars Radio Table (Styled Modern Matrix) -->
                        <div class="border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
                            <table class="w-full text-xs text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase tracking-wider text-[11px]">
                                        <th class="py-3 px-4 w-40 font-bold">Aspek Penilaian</th>
                                        <th class="py-3 px-3 text-center font-bold">Sangat Baik</th>
                                        <th class="py-3 px-3 text-center font-bold">Baik</th>
                                        <th class="py-3 px-3 text-center font-bold">Cukup</th>
                                        <th class="py-3 px-3 text-center font-bold">Kurang</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @php
                                        $assess = $candidate->interviewAssessment;
                                        $pillars = [
                                            ['name' => 'kemauan_kerja', 'label' => 'Kemauan Kerja', 'val' => $assess?->work_motivation ?? 3],
                                            ['name' => 'penampilan', 'label' => 'Penampilan', 'val' => $assess?->appearance ?? 3],
                                            ['name' => 'attitude', 'label' => 'Attitude', 'val' => $assess?->attitude ?? 3],
                                            ['name' => 'daya_tangkap', 'label' => 'Daya Tangkap', 'val' => $assess?->comprehension ?? 3],
                                        ];
                                    @endphp

                                    @foreach($pillars as $p)
                                    <tr class="hover:bg-slate-50/70 transition-colors">
                                        <td class="py-3.5 px-4 font-bold text-slate-800 text-xs">{{ $p['label'] }}</td>
                                        <td class="py-3.5 px-3 text-center">
                                            <label class="inline-flex items-center gap-1.5 cursor-pointer group">
                                                <input type="radio" name="{{ $p['name'] }}" value="Sangat Baik" {{ ($p['val'] == 5 || $p['val'] === 'Sangat Baik') ? 'checked' : '' }} class="w-4 h-4 text-primary-600 focus:ring-primary-500 border-slate-300">
                                                <span class="text-slate-600 font-medium group-hover:text-primary-600">Sangat Baik</span>
                                            </label>
                                        </td>
                                        <td class="py-3.5 px-3 text-center">
                                            <label class="inline-flex items-center gap-1.5 cursor-pointer group">
                                                <input type="radio" name="{{ $p['name'] }}" value="Baik" {{ ($p['val'] == 4 || $p['val'] === 'Baik') ? 'checked' : '' }} class="w-4 h-4 text-primary-600 focus:ring-primary-500 border-slate-300">
                                                <span class="text-slate-600 font-medium group-hover:text-primary-600">Baik</span>
                                            </label>
                                        </td>
                                        <td class="py-3.5 px-3 text-center">
                                            <label class="inline-flex items-center gap-1.5 cursor-pointer group">
                                                <input type="radio" name="{{ $p['name'] }}" value="Cukup" {{ ($p['val'] == 3 || $p['val'] == 2 || $p['val'] === 'Cukup' || empty($p['val'])) ? 'checked' : '' }} class="w-4 h-4 text-primary-600 focus:ring-primary-500 border-slate-300">
                                                <span class="text-slate-800 font-bold group-hover:text-primary-600">Cukup</span>
                                            </label>
                                        </td>
                                        <td class="py-3.5 px-3 text-center">
                                            <label class="inline-flex items-center gap-1.5 cursor-pointer group">
                                                <input type="radio" name="{{ $p['name'] }}" value="Kurang" {{ ($p['val'] == 1 || $p['val'] === 'Kurang') ? 'checked' : '' }} class="w-4 h-4 text-primary-600 focus:ring-primary-500 border-slate-300">
                                                <span class="text-slate-600 font-medium group-hover:text-primary-600">Kurang</span>
                                            </label>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Catatan Lain-lain -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Catatan Lain-lain</label>
                            <textarea name="catatan" 
                                      rows="3" 
                                      class="w-full bg-white border border-slate-200 rounded-xl p-3.5 text-xs text-slate-800 focus:ring-4 focus:ring-primary-100 focus:border-primary-600 outline-none transition-all placeholder:text-slate-400" 
                                      placeholder="Tuliskan catatan khusus atau kesimpulan wawancara...">{{ $assess?->other_notes ?? 'ok' }}</textarea>
                        </div>

                        <!-- Tanggal Interview -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggal Interview</label>
                            <div class="relative max-w-xs">
                                <input type="date" 
                                       name="tgl_interview" 
                                       value="{{ $assess?->interview_date?->format('Y-m-d') ?? date('Y-m-d') }}" 
                                       class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-semibold text-slate-800 focus:ring-4 focus:ring-primary-100 focus:border-primary-600 outline-none">
                            </div>
                        </div>

                    </div>

                    <!-- Right: Tanda Tangan Canvas / Signature Pad -->
                    <div class="lg:col-span-4 space-y-3">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <div>
                                <label class="block text-xs font-bold text-slate-800">Tanda Tangan Pewawancara</label>
                                <span class="text-[10px] text-slate-500 font-medium">User AS: <b class="text-slate-700">{{ $candidateAsName }}</b></span>
                            </div>
                            
                            <span id="sigBadge" class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $assessSigUrl ? 'bg-emerald-50 border border-emerald-200 text-emerald-700' : ($candidateAsSigUrl ? 'bg-blue-50 border border-blue-200 text-blue-700' : 'bg-slate-100 text-slate-500') }}">
                                <i id="sigBadgeIcon" class="fa-solid {{ $assessSigUrl ? 'fa-circle-check text-emerald-600' : ($candidateAsSigUrl ? 'fa-stamp text-blue-600' : 'fa-pen-nib text-slate-400') }}"></i>
                                <span id="sigBadgeText">
                                    @if($assessSigUrl)
                                        TTD Interview Tersimpan
                                    @elseif($candidateAsSigUrl)
                                        TTD AS (Siap)
                                    @else
                                        Belum Ada TTD
                                    @endif
                                </span>
                            </span>
                        </div>

                        <!-- Toolbar Opsi: Tempel TTD Tersimpan ATAU Gambar TTD Baru -->
                        <div class="flex items-center gap-2 p-1.5 bg-slate-100/90 rounded-xl border border-slate-200 shadow-2xs">
                            @if(!empty($mySavedSigUrl))
                                <button type="button" 
                                        onclick="pasteMySavedSignature()" 
                                        class="flex-1 py-1.5 px-2.5 rounded-lg bg-white hover:bg-emerald-50 text-slate-700 hover:text-emerald-700 border border-slate-200 hover:border-emerald-300 text-[11px] font-bold transition-all flex items-center justify-center gap-1.5 shadow-2xs group"
                                        title="Tempel tanda tangan profil tersimpan milik Anda ({{ $currentUserName }})">
                                    <i class="fa-solid fa-stamp text-emerald-600 group-hover:scale-110 transition-transform"></i>
                                    <span>Tempel TTD Saya</span>
                                </button>
                            @endif

                            <button type="button" 
                                    onclick="startNewSignature()" 
                                    class="flex-1 py-1.5 px-2.5 rounded-lg bg-white hover:bg-blue-50 text-slate-700 hover:text-blue-700 border border-slate-200 hover:border-blue-300 text-[11px] font-bold transition-all flex items-center justify-center gap-1.5 shadow-2xs group"
                                    title="Gambar tanda tangan baru (akan menggantikan file TTD Anda)">
                                <i class="fa-solid fa-pen-nib text-blue-600 group-hover:scale-110 transition-transform"></i>
                                <span>{{ !empty($mySavedSigUrl) ? 'Gambar TTD Baru' : 'Buat TTD Baru' }}</span>
                            </button>

                            <button type="button" 
                                    onclick="clearSignatureCanvas()" 
                                    class="py-1.5 px-2.5 rounded-lg bg-white hover:bg-rose-50 text-slate-500 hover:text-rose-600 border border-slate-200 hover:border-rose-300 text-[11px] font-semibold transition-all flex items-center justify-center gap-1 shadow-2xs"
                                    title="Hapus / Bersihkan Tanda Tangan">
                                <i class="fa-solid fa-rotate-left text-rose-500"></i>
                                <span>Reset</span>
                            </button>
                        </div>
                        
                        <div class="border border-slate-200 rounded-2xl bg-white p-4 shadow-sm text-center">
                            <!-- Canvas Drawing Pad -->
                            <div class="relative">
                                <canvas id="signatureCanvas" 
                                        width="400" 
                                        height="240" 
                                        style="touch-action: none;"
                                        class="w-full h-52 bg-slate-50/50 rounded-xl border border-dashed border-slate-300 cursor-crosshair touch-none shadow-inner"></canvas>
                                
                                <div class="absolute bottom-2 left-3 pointer-events-none text-[10px] text-slate-400 font-medium">
                                    <span>Tanda Tangan Pewawancara</span>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between pt-2.5 text-xs">
                                <span class="text-[10px] text-slate-400 italic">
                                    * TTD baru akan menggantikan file tanda tangan tersimpan Anda
                                </span>
                                <span id="sigStatusLabel" class="text-[10px] text-primary-600 font-bold not-italic"></span>
                            </div>
                        </div>

                        <!-- Submit Button Right Below Signature (Matching Image 1) -->
                        <div class="flex justify-end pt-2">
                            <button type="button" 
                                    onclick="submitInterviewForm()" 
                                    class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-primary-600 hover:bg-primary-700 text-white shadow-lg shadow-primary-500/25 transition-all"
                                    title="Simpan Hasil Interview">
                                <i class="fa-solid fa-check text-base font-black"></i>
                            </button>
                        </div>
                    </div>

                </div>
            </form>
        </div>

        <!-- ============================================================= -->
        <!-- TAB 2: REFERENSI CEK (Matching Image 2) -->
        <!-- ============================================================= -->
        <div x-show="activeTab === 'refcek'" class="space-y-6">
            @php
                $firstExp = $candidate->workExperiences->first();
            @endphp

            <form action="{{ route('interview.refcek', $candidate->id) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    
                    <!-- Left Column (Perusahaan, Tanggal, SPV, Performa, Disiplin, Tanggung Jawab, Problem, Keunggulan, Kelemahan) -->
                    <div class="lg:col-span-7 space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Perusahaan Sebelumnya <span class="text-rose-500">*</span></label>
                            <select name="company_id" id="companySelect" onchange="handleCompanySelect(this.value)" class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 font-semibold focus:ring-4 focus:ring-primary-100 focus:border-primary-600 outline-none shadow-xs">
                                @if($candidate->workExperiences->count() > 0)
                                    @foreach($candidate->workExperiences as $w)
                                        <option value="{{ $w->id }}" {{ $loop->first ? 'selected' : '' }}>
                                            {{ $w->company_name }} @if(!empty($w->position)) ({{ $w->position }}) @endif
                                        </option>
                                    @endforeach
                                    <option value="new">+ Tambah Riwayat Perusahaan Baru</option>
                                @else
                                    <option value="new" selected>+ Input Nama Perusahaan Pengalaman Kerja</option>
                                @endif
                            </select>
                            <p class="text-[10px] text-slate-400 mt-1">Pilih dari data riwayat pekerjaan yang diinputkan dibagian Pengalaman Kerja kandidat</p>
                        </div>

                        <!-- Manual Company Name input if 'new' is selected or no experiences exist -->
                        <div id="customCompanyWrapper" style="{{ $candidate->workExperiences->count() > 0 ? 'display: none;' : '' }}">
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Perusahaan Baru</label>
                            <input type="text" name="company_name" id="customCompanyName" placeholder="Masukkan nama perusahaan..." value="{{ $firstExp?->company_name ?? '' }}" class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:ring-4 focus:ring-primary-100 focus:border-primary-600 outline-none font-semibold">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggal Cek Referensi</label>
                            <input type="date" name="checked_date" id="refcek_date" value="{{ date('Y-m-d') }}" class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:ring-4 focus:ring-primary-100 focus:border-primary-600 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama SPV <span class="text-rose-500">*</span></label>
                            <input type="text" name="supervisor_name" id="refcek_spv" value="{{ $firstExp?->supervisor_name ?? '-' }}" required class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:ring-4 focus:ring-primary-100 focus:border-primary-600 outline-none font-medium" placeholder="Bpk. Bambang">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Performa</label>
                            <textarea name="performance_review" id="refcek_performance" rows="2" class="w-full bg-white border border-slate-200 rounded-xl p-3 text-xs text-slate-800 focus:ring-4 focus:ring-primary-100 focus:border-primary-600 outline-none">{{ $firstExp?->performance_notes ?? 'Baik' }}</textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Disiplin</label>
                            <textarea name="discipline_review" id="refcek_discipline" rows="2" class="w-full bg-white border border-slate-200 rounded-xl p-3 text-xs text-slate-800 focus:ring-4 focus:ring-primary-100 focus:border-primary-600 outline-none">{{ $firstExp?->discipline_notes ?? 'Tepat Waktu' }}</textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggung Jawab</label>
                            <textarea name="responsibility_review" id="refcek_responsibility" rows="2" class="w-full bg-white border border-slate-200 rounded-xl p-3 text-xs text-slate-800 focus:ring-4 focus:ring-primary-100 focus:border-primary-600 outline-none">{{ $firstExp?->responsibility_notes ?? 'Bertanggung Jawab' }}</textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Problem / Masalah</label>
                            <textarea name="problem_notes" id="refcek_problem" rows="2" class="w-full bg-white border border-slate-200 rounded-xl p-3 text-xs text-slate-800 focus:ring-4 focus:ring-primary-100 focus:border-primary-600 outline-none">-</textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Keunggulan</label>
                            <textarea name="strengths_identified" id="refcek_strengths" rows="2" class="w-full bg-white border border-slate-200 rounded-xl p-3 text-xs text-slate-800 focus:ring-4 focus:ring-primary-100 focus:border-primary-600 outline-none">{{ $firstExp?->strengths ?? 'Problem solving cepat dan komunikatif.' }}</textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Kelemahan</label>
                            <textarea name="weaknesses_identified" id="refcek_weaknesses" rows="2" class="w-full bg-white border border-slate-200 rounded-xl p-3 text-xs text-slate-800 focus:ring-4 focus:ring-primary-100 focus:border-primary-600 outline-none">{{ $firstExp?->weaknesses ?? 'Terkadang terlalu detail.' }}</textarea>
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="px-6 py-2.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white text-xs font-bold transition-all shadow-md shadow-primary-500/20 flex items-center gap-2">
                                <i class="fa-solid fa-floppy-disk"></i>
                                <span>Simpan Referensi Cek</span>
                            </button>
                        </div>
                    </div>

                    <!-- Right Column (Telp Perusahaan, Tgl Masuk, Tgl Keluar, Alasan Keluar, Ultra Attractive Upload Box) -->
                    <div class="lg:col-span-5 space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Telp Perusahaan</label>
                            <input type="text" name="company_phone" id="refcek_phone" value="{{ $firstExp?->company_phone ?? '081234567890' }}" class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:ring-4 focus:ring-primary-100 focus:border-primary-600 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Tgl. Masuk</label>
                            <input type="text" name="tgl_masuk" value="{{ $firstExp?->start_date?->format('Y-m-d') ?? '2022-01-01' }}" class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:ring-4 focus:ring-primary-100 focus:border-primary-600 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Tgl. Keluar</label>
                            <input type="text" name="tgl_keluar" value="{{ $firstExp?->end_date?->format('Y-m-d') ?? '2023-12-31' }}" class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:ring-4 focus:ring-primary-100 focus:border-primary-600 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Alasan Keluar</label>
                            <textarea name="reason_for_leaving" rows="2" class="w-full bg-white border border-slate-200 rounded-xl p-3 text-xs text-slate-800 focus:ring-4 focus:ring-primary-100 focus:border-primary-600 outline-none">{{ $firstExp?->reason_for_leaving ?? 'Habis Kontrak Kerja' }}</textarea>
                        </div>

                        <!-- Highly Attractive File Upload Dropzone (Screenshot WA Chat) -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Screenshot WA Chat</label>
                            
                            <div class="relative border-2 border-dashed border-slate-300 hover:border-primary-500 rounded-2xl bg-slate-50/60 p-5 text-center transition-all group">
                                <input type="file" 
                                       name="proof_file" 
                                       id="waChatProofInput" 
                                       accept="image/jpeg,image/png,image/jpg" 
                                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                       onchange="handleWaProofPreview(this)">

                                <div id="waProofUploadPrompt" class="space-y-2">
                                    <div class="w-12 h-12 rounded-xl bg-white text-primary-600 flex items-center justify-center mx-auto shadow-sm group-hover:scale-105 transition-transform">
                                        <i class="fa-solid fa-cloud-arrow-up text-xl"></i>
                                    </div>
                                    <div>
                                        <span class="text-xs font-bold text-slate-700 block">Pilih File Screenshot</span>
                                        <span class="text-[11px] text-slate-400">atau tarik dan letakkan file gambar di sini</span>
                                    </div>
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-200 text-slate-600">
                                        Format file: jpg/jpeg, png
                                    </span>
                                </div>

                                <!-- Live Preview Container -->
                                <div id="waProofPreviewContainer" class="hidden flex flex-col items-center space-y-2">
                                    <img id="waProofPreviewImage" class="max-h-36 rounded-lg object-contain shadow-sm border border-slate-200">
                                    <span id="waProofFileName" class="text-xs font-mono text-slate-700 font-bold"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Current Uploaded Proof Preview (Refcek) -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Lampiran Bukti Referensi Cek Tersimpan :</label>
                            <div id="refcek_current_proof" class="p-3 bg-white rounded-2xl border border-slate-200 shadow-2xs">
                                <div id="refcek_has_proof" style="{{ ($firstExp && $firstExp->proof_url) ? '' : 'display:none;' }}" class="flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <div class="w-12 h-12 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden flex-shrink-0 cursor-pointer shadow-xs group" onclick="previewCurrentRefcek()" title="Klik untuk preview lampiran">
                                            <img id="refcek_proof_thumb" src="{{ $firstExp?->proof_url ?? '' }}" alt="Bukti Refcek" class="w-full h-full object-cover transition-transform group-hover:scale-105" onerror="this.onerror=null; this.src='{{ $firstExp?->proof_legacy_url ?? '' }}';">
                                        </div>
                                        <div class="min-w-0">
                                            <div class="text-xs font-bold text-slate-800 truncate" id="refcek_proof_filename">{{ $firstExp ? basename($firstExp->proof_attachment_path) : '' }}</div>
                                            <span class="text-[10px] text-emerald-600 font-semibold block">Bukti Verifikasi Terlampir (Server / Fallback V3)</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1.5 flex-shrink-0">
                                        <button type="button" onclick="previewCurrentRefcek()" class="px-3 py-1.5 bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 rounded-xl text-xs font-bold transition-colors inline-flex items-center gap-1">
                                            <i class="fa-solid fa-eye text-xs"></i> Preview
                                        </button>
                                        <a href="{{ $firstExp?->proof_url ?? '#' }}" id="refcek_proof_link" target="_blank" class="px-3 py-1.5 bg-white border border-slate-300 rounded-xl text-xs font-bold text-primary hover:bg-slate-100 transition-colors inline-flex items-center gap-1 shadow-2xs">
                                            <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i> Buka
                                        </a>
                                    </div>
                                </div>
                                <div id="refcek_no_proof" style="{{ ($firstExp && $firstExp->proof_url) ? 'display:none;' : '' }}" class="text-xs text-slate-400 italic text-center py-2">
                                    <i class="fa-regular fa-image text-slate-300 mr-1"></i> Belum ada lampiran screenshot verifikasi untuk perusahaan ini
                                </div>
                            </div>
                        </div>

                        <!-- Plus Button at bottom right -->
                        <div class="flex justify-end pt-4">
                            <button type="submit" class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-rose-500 hover:bg-rose-600 text-white shadow-lg shadow-rose-500/25 transition-all" title="Tambah / Simpan Pengalaman">
                                <i class="fa-solid fa-plus text-base font-black"></i>
                            </button>
                        </div>
                    </div>

                </div>
            </form>
        </div>

        <!-- ============================================================= -->
        <!-- ============================================================= -->
        <!-- TAB 3: TES KOMPUTER (Matching Image 3) -->
        <!-- ============================================================= -->
        <div x-show="activeTab === 'kompt'" class="space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-100 text-slate-800 text-xs font-semibold">
                    <i class="fa-solid fa-stopwatch text-slate-500"></i>
                    <span>Waktu Pengerjaan : <strong>{{ $komptDuration ?? '00:03:02' }}</strong></span>
                </div>
                <span class="text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-md">
                    Skor: {{ $komptSummaryLabel ?? 'Cukup (75%)' }}
                </span>
            </div>

            @if(!$hasKompt)
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-3.5 flex items-center gap-3 text-xs text-amber-800">
                <i class="fa-solid fa-circle-info text-amber-500 text-base flex-shrink-0"></i>
                <span>Data penilaian awal tes komputer belum tersimpan. Anda dapat menilai langsung indikator di bawah dan menekan tombol <strong>Submit</strong>.</span>
            </div>
            @endif

            <form action="{{ route('interview.kompt', $candidate->id) }}" method="POST" class="space-y-5">
                @csrf

                <div class="border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
                    <table class="w-full text-xs text-left border-collapse">
                        <tbody class="divide-y divide-slate-100">
                            @foreach($compSkills as $k => $label)
                            @php
                                $val = $savedComp[$k] ?? 'Cukup';
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3 px-4 font-bold text-slate-800 w-72 uppercase tracking-wider text-xs">{{ $label }}</td>
                                <td class="py-3 px-3">
                                    <label class="inline-flex items-center gap-2 cursor-pointer group">
                                        <input type="radio" name="{{ $k }}" value="Baik" {{ strtolower($val) === 'baik' ? 'checked' : '' }} class="w-4 h-4 text-primary-600 focus:ring-primary-500 border-slate-300">
                                        <span class="text-slate-700 group-hover:text-primary-600">Baik</span>
                                    </label>
                                </td>
                                <td class="py-3 px-3">
                                    <label class="inline-flex items-center gap-2 cursor-pointer group">
                                        <input type="radio" name="{{ $k }}" value="Cukup" {{ (strtolower($val) === 'cukup' || empty($val)) ? 'checked' : '' }} class="w-4 h-4 text-primary-600 focus:ring-primary-500 border-slate-300">
                                        <span class="text-slate-900 font-bold group-hover:text-primary-600">Cukup</span>
                                    </label>
                                </td>
                                <td class="py-3 px-3">
                                    <label class="inline-flex items-center gap-2 cursor-pointer group">
                                        <input type="radio" name="{{ $k }}" value="Kurang" {{ strtolower($val) === 'kurang' ? 'checked' : '' }} class="w-4 h-4 text-primary-600 focus:ring-primary-500 border-slate-300">
                                        <span class="text-slate-700 group-hover:text-primary-600">Kurang</span>
                                    </label>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="pt-2">
                    <button type="submit" class="px-6 py-2.5 rounded-xl text-xs font-bold bg-slate-800 hover:bg-slate-900 text-white shadow-sm transition-all">
                        Submit
                    </button>
                </div>
            </form>
        </div>

        <!-- ============================================================= -->
        <!-- TAB 4: TES KEPRIBADIAN (Matching Image 4) -->
        <!-- ============================================================= -->
        <div x-show="activeTab === 'kepribadian'" class="space-y-5">
            @if($hasPsikotes)
            <!-- Header Bar with Timer & 4 Answer Badges -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3 border-b border-slate-100">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-100 text-slate-800 text-xs font-semibold">
                    <i class="fa-solid fa-clock text-slate-500"></i>
                    <span>Waktu Pengerjaan : <strong>{{ $psikotesDuration }}</strong></span>
                </div>

                <div class="flex items-center flex-wrap gap-2 text-xs font-bold">
                    <span class="px-3 py-1 rounded-lg bg-rose-50 text-rose-700 border border-rose-200">Jawaban A : {{ $psikotesCounts['A'] ?? 0 }}</span>
                    <span class="px-3 py-1 rounded-lg bg-amber-50 text-amber-700 border border-amber-200">Jawaban B : {{ $psikotesCounts['B'] ?? 0 }}</span>
                    <span class="px-3 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200">Jawaban C : {{ $psikotesCounts['C'] ?? 0 }}</span>
                    <span class="px-3 py-1 rounded-lg bg-blue-50 text-blue-700 border border-blue-200">Jawaban D : {{ $psikotesCounts['D'] ?? 0 }}</span>
                </div>
            </div>

            <!-- Kesimpulan Box -->
            <div class="bg-gradient-to-r from-amber-50/80 to-amber-100/50 border border-amber-200/80 rounded-2xl p-5 text-xs text-slate-800 leading-relaxed shadow-xs flex items-start gap-3.5">
                <div class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center flex-shrink-0 text-sm shadow-sm">
                    <i class="fa-solid fa-lightbulb"></i>
                </div>
                <div>
                    <h4 class="font-bold text-amber-900 text-sm mb-1">Kesimpulan Karakter:</h4>
                    <p class="text-slate-700">
                        {{ $dominantDisc['summary'] }}
                    </p>
                </div>
            </div>

            <!-- 4-Column Table of Questions (Exact screenshot layout) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @for($col = 0; $col < 4; $col++)
                <div class="border border-slate-200 rounded-2xl overflow-hidden shadow-xs bg-white">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-bold text-[11px]">
                            <tr>
                                <th class="py-2.5 px-2.5 w-8 text-center">No.</th>
                                <th class="py-2.5 px-2 w-14 text-center">Jawaban</th>
                                <th class="py-2.5 px-2.5">Jawaban yang Dipilih</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @for($i = ($col * 10) + 1; $i <= ($col * 10) + 10; $i++)
                            @php
                                $item = $psikotesItems[$i] ?? ['ans' => '-', 'text' => '-'];
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-2 px-2.5 text-center font-bold text-slate-400">{{ $i }}</td>
                                <td class="py-2 px-2 text-center font-mono font-bold text-primary-700 bg-slate-50/50">{{ $item['ans'] }}</td>
                                <td class="py-2 px-2.5 text-slate-700 leading-tight">{{ $item['text'] }}</td>
                            </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
                @endfor
            </div>
            @else
            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-8 text-center space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center mx-auto text-xl">
                    <i class="fa-solid fa-brain"></i>
                </div>
                <h4 class="text-sm font-bold text-slate-800">Kandidat Belum Mengikuti Tes Kepribadian</h4>
                <p class="text-xs text-slate-500 max-w-md mx-auto">
                    Kandidat belum menyelesaikan Tes Kepribadian (DISC). Hasil tes dan rincian 40 butir jawaban akan otomatis tersinkronisasi di sini setelah kandidat menyelesaikan tes online.
                </p>
            </div>
            @endif
        </div>

        <!-- ============================================================= -->
        <!-- TAB 5: TES MATEMATIKA (Matching Image 5) -->
        <!-- ============================================================= -->
        <div x-show="activeTab === 'matematika'" class="space-y-5">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-100 text-slate-800 text-xs font-semibold">
                        <i class="fa-solid fa-stopwatch text-slate-500"></i>
                        <span>Waktu Pengerjaan : <strong>{{ $mathDuration }}</strong></span>
                    </div>
                    <span class="text-xs font-bold text-slate-700 bg-slate-50 px-2.5 py-1 rounded-md border border-slate-200">
                        Tes Ke - {{ $mathTesKe }}
                    </span>
                </div>

                <form action="{{ route('interview.remidi', $candidate->id) }}" method="POST">
                    @csrf
                    <button type="submit" 
                            onclick="return confirm('Atur remidi tes matematika untuk kandidat ini? Tautan tes online baru akan dibuatkan.')" 
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold bg-rose-600 hover:bg-rose-700 text-white shadow-sm shadow-rose-600/20 transition-all">
                        <i class="fa-solid fa-arrows-rotate text-xs"></i>
                        <span>Send Remidi</span>
                    </button>
                </form>
            </div>

            @if($hasMath && count($mathItems) > 0)
            <!-- Table of Math Questions -->
            <div class="border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-bold text-[11px]">
                        <tr>
                            <th class="py-2.5 px-3 w-10 text-center">#</th>
                            <th class="py-2.5 px-3">Pertanyaan</th>
                            <th class="py-2.5 px-3 w-36">Jawaban Kandidat</th>
                            <th class="py-2.5 px-3 w-36">Jawaban Benar</th>
                            <th class="py-2.5 px-3 w-16 text-center">Hasil</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($mathItems as $idx => $m)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-2.5 px-3 text-center font-bold text-slate-400">{{ $idx }}</td>
                            <td class="py-2.5 px-3 text-slate-800 leading-snug">{{ $m['q'] }}</td>
                            <td class="py-2.5 px-3 font-mono font-bold {{ $m['correct'] ? 'text-slate-800' : 'text-rose-600 bg-rose-50/50 rounded px-1.5' }}">{{ $m['cand'] }}</td>
                            <td class="py-2.5 px-3 font-mono font-bold text-slate-800">{{ $m['key'] }}</td>
                            <td class="py-2.5 px-3 text-center text-sm font-bold">
                                @if($m['correct'])
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 text-xs">&#10004;</span>
                                @else
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-rose-100 text-rose-700 text-xs">&#10008;</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Score Summary Cards -->
            <div class="grid grid-cols-3 gap-4 pt-2">
                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-3 text-center">
                    <span class="text-[11px] font-semibold text-emerald-700 block">Jawaban Benar</span>
                    <span class="text-xl font-black text-emerald-900">{{ $mathCorrectCount }}</span>
                </div>
                <div class="bg-rose-50 border border-rose-200 rounded-xl p-3 text-center">
                    <span class="text-[11px] font-semibold text-rose-700 block">Jawaban Salah</span>
                    <span class="text-xl font-black text-rose-900">{{ $mathWrongCount }}</span>
                </div>
                <div class="bg-primary-50 border border-primary-200 rounded-xl p-3 text-center">
                    <span class="text-[11px] font-semibold text-primary-700 block">Nilai Akhir</span>
                    <span class="text-xl font-black text-primary-900">{{ $mathGrade }} ({{ $mathScorePercent }}%)</span>
                </div>
            </div>
            @else
            <div class="bg-amber-50/60 border border-amber-200 rounded-2xl p-8 text-center space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center mx-auto text-xl">
                    <i class="fa-solid fa-calculator"></i>
                </div>
                @if($mathTesKe > 1)
                    <h4 class="text-sm font-bold text-slate-800">Status Remidi Aktif (Tes Ke - {{ $mathTesKe }})</h4>
                    <p class="text-xs text-slate-600 max-w-md mx-auto">
                        Kandidat telah dijadwalkan untuk melakukan <strong>Remidi Tes Matematika (Tes Ke - {{ $mathTesKe }})</strong>. Status tes saat ini belum selesai (silang merah). Nilai dan butir jawaban akan otomatis diperbarui setelah kandidat mengerjakan ulang tes di portal CBT.
                    </p>
                @else
                    <h4 class="text-sm font-bold text-slate-800">Kandidat Belum Mengikuti Tes Matematika</h4>
                    <p class="text-xs text-slate-500 max-w-md mx-auto">
                        Kandidat belum menyelesaikan Tes Matematika. Data nilai, rincian benar/salah, dan pembahasan butir soal akan otomatis terisi setelah kandidat menyelesaikan ujian.
                    </p>
                @endif
            </div>
            @endif
        </div>

        <!-- ============================================================= -->
        <!-- TAB 6: ANALISA AI (CV ANALYZER RESULT) - Sesuai Skrip Asli & Tab 6 -->
        <!-- ============================================================= -->
        <div x-show="activeTab === 'ai'" class="space-y-6">
            @php
                $matchScore = intval($aiData['evaluation_match_score'] ?? ($candidate->ai_score ?? 0));
                $scoreColorHex = '#059669'; // emerald-600
                $scoreBadgeBg = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                if ($matchScore < 60) {
                    $scoreColorHex = '#e11d48'; // rose-600
                    $scoreBadgeBg = 'bg-rose-50 text-rose-700 border-rose-200';
                } elseif ($matchScore < 85) {
                    $scoreColorHex = '#d97706'; // amber-600
                    $scoreBadgeBg = 'bg-amber-50 text-amber-700 border-amber-200';
                }
            @endphp

            <!-- Top Header Banner with Download PDF & Re-Analyze Buttons -->
            <div class="p-4 bg-gradient-to-r from-blue-50/80 via-indigo-50/50 to-slate-50 rounded-2xl border border-blue-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-primary-600 to-indigo-600 text-white flex items-center justify-center text-xl shadow-md shadow-primary-500/20 flex-shrink-0">
                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900">AI CV Analyzer Recommendation</h3>
                        <p class="text-[11px] text-slate-500">Hasil evaluasi komprehensif profil kandidat & kesesuaian requirement lowongan</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 flex-wrap">
                    <!-- Download PDF Button (Sesuai Skrip Aslinya!) -->
                    <a href="{{ route('interview.cetak-ai', $candidate->id) }}" target="_blank" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition-all shadow-sm shadow-rose-600/20 flex items-center gap-2">
                        <i class="fa-solid fa-file-pdf text-sm"></i>
                        <span>Download PDF</span>
                    </a>

                    <button type="button" @click="alert('Memulai evaluasi ulang berkas CV kandidat...')" class="px-3.5 py-2 rounded-xl bg-white border border-slate-300 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-all shadow-sm flex items-center gap-1.5">
                        <i class="fa-solid fa-arrows-rotate text-primary-600"></i>
                        <span>Analisis Ulang CV</span>
                    </button>
                </div>
            </div>

            @if(!empty($aiData))
            <!-- Section 1: Radial Gauge Match & Candidate Biodata -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-stretch">
                <!-- Match Gauge Box -->
                <div class="md:col-span-4 bg-slate-50 rounded-2xl border border-slate-200 p-6 flex flex-col items-center justify-center text-center">
                    <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Evaluation Match Score</div>
                    
                    <!-- Circular Gauge -->
                    <div class="relative w-36 h-36 rounded-full flex items-center justify-center shadow-sm"
                         style="background: conic-gradient({{ $scoreColorHex }} {{ $matchScore }}%, #e2e8f0 0);">
                            <div class="w-28 h-28 bg-white rounded-full flex flex-col items-center justify-center shadow-inner">
                                <span class="text-3xl font-black text-slate-900 leading-none">{{ $matchScore }}%</span>
                                <span class="text-[10px] font-extrabold uppercase mt-1" style="color: {{ $scoreColorHex }}">
                                    {{ $aiData['specification_fit'] ?? 'Specification Fit' }}
                                </span>
                            </div>
                    </div>

                    <p class="text-[11px] text-slate-500 mt-4 leading-relaxed max-w-xs">
                        Kandidat memiliki tingkat kecocokan <b>{{ $matchScore }}%</b> terhadap kualifikasi posisi <b>{{ $candidate->applied_job }}</b>.
                    </p>
                </div>

                <!-- Candidate Biodata Card -->
                <div class="md:col-span-8 bg-white rounded-2xl border border-slate-200 p-6 flex flex-col justify-between">
                    <div>
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Candidate Biodata</div>
                        <h4 class="text-base font-extrabold text-primary-600 mb-3">
                            {{ $aiData['candidate_biodata']['name'] ?? $candidate->full_name }}
                        </h4>
                        <table class="w-full text-xs">
                            <tr class="border-b border-slate-100">
                                <td class="py-2 text-slate-400 font-medium w-28">Contact</td>
                                <td class="py-2 text-slate-800 font-bold">: {{ $aiData['candidate_biodata']['contact'] ?? ($candidate->whatsapp ?: $candidate->phone ?: '-') }}</td>
                            </tr>
                            <tr class="border-b border-slate-100">
                                <td class="py-2 text-slate-400 font-medium">Education</td>
                                <td class="py-2 text-slate-800 font-bold">: {{ $aiData['candidate_biodata']['education'] ?? ($candidate->education ?: '-') }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-slate-400 font-medium">Position Applied</td>
                                <td class="py-2 text-slate-800 font-bold">: {{ $candidate->applied_job }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                        <span>Status Seleksi: <strong class="text-slate-700">{{ $candidate->status_kandidat ?? 'Aktif' }}</strong></span>
                        <span>Area: <strong class="text-slate-700">{{ $candidate->area ?? 'JAKARTA' }}</strong></span>
                    </div>
                </div>
            </div>

            <!-- Section 2: Core Strengths & Weaknesses / Missing Gaps -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Core Strengths -->
                <div class="bg-white rounded-2xl border border-emerald-200 overflow-hidden shadow-xs">
                    <div class="px-5 py-3 bg-emerald-50/70 border-b border-emerald-100 flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-emerald-600"></i>
                        <h5 class="text-xs font-bold text-emerald-900 uppercase tracking-wider">Core Strengths</h5>
                    </div>
                    <div class="p-5">
                        <ul class="space-y-2 text-xs text-slate-700">
                            @forelse((array)($aiData['core_strengths'] ?? []) as $str)
                            <li class="flex items-start gap-2">
                                <i class="fa-solid fa-check text-emerald-600 text-xs mt-0.5 flex-shrink-0"></i>
                                <span class="leading-relaxed">{{ $str }}</span>
                            </li>
                            @empty
                            <li class="text-slate-400 italic">Tidak ada catatan keunggulan khusus.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                <!-- Weaknesses / Missing Gaps -->
                <div class="bg-white rounded-2xl border border-rose-200 overflow-hidden shadow-xs">
                    <div class="px-5 py-3 bg-rose-50/70 border-b border-rose-100 flex items-center gap-2">
                        <i class="fa-solid fa-circle-xmark text-rose-600"></i>
                        <h5 class="text-xs font-bold text-rose-900 uppercase tracking-wider">Weaknesses / Missing Gaps</h5>
                    </div>
                    <div class="p-5">
                        <ul class="space-y-2 text-xs text-slate-700">
                            @forelse((array)($aiData['weaknesses'] ?? []) as $weak)
                            <li class="flex items-start gap-2">
                                <i class="fa-solid fa-xmark text-rose-500 text-xs mt-0.5 flex-shrink-0"></i>
                                <span class="leading-relaxed">{{ $weak }}</span>
                            </li>
                            @empty
                            <li class="text-slate-400 italic">Tidak ada catatan kelemahan signifikan.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Section 3: Psychological Traits & Core Skills & Other Candidates -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Psychological Traits & Culture Fit -->
                <div class="bg-white rounded-2xl border border-indigo-200 overflow-hidden shadow-xs space-y-4 p-5">
                    <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
                        <i class="fa-solid fa-brain text-indigo-600"></i>
                        <h5 class="text-xs font-bold text-indigo-900 uppercase tracking-wider">Psychological Traits & Culture Fit</h5>
                    </div>

                    <div>
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Personality Traits</span>
                        <div class="flex flex-wrap gap-1.5">
                            @forelse((array)($aiData['psychological_traits']['personality'] ?? []) as $trait)
                            <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-indigo-50 text-indigo-800 border border-indigo-200">
                                {{ $trait }}
                            </span>
                            @empty
                            <span class="text-xs text-slate-400 italic">-</span>
                            @endforelse
                        </div>
                    </div>

                    <div>
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Work Style</span>
                        <p class="text-xs text-slate-700 bg-slate-50 p-3 rounded-xl border border-slate-200 leading-relaxed">
                            {{ $aiData['psychological_traits']['work_style'] ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Cultural Fit</span>
                        <p class="text-xs text-slate-700 bg-slate-50 p-3 rounded-xl border border-slate-200 leading-relaxed">
                            {{ $aiData['psychological_traits']['cultural_fit'] ?? '-' }}
                        </p>
                    </div>
                </div>

                <!-- Core Skills Evaluation & Other Candidates Comparison -->
                <div class="space-y-6">
                    <!-- Core Skills -->
                    <div class="bg-white rounded-2xl border border-sky-200 overflow-hidden shadow-xs p-5">
                        <div class="flex items-center gap-2 pb-2 border-b border-slate-100 mb-3">
                            <i class="fa-solid fa-screwdriver-wrench text-sky-600"></i>
                            <h5 class="text-xs font-bold text-sky-900 uppercase tracking-wider">Core Skills Evaluation</h5>
                        </div>
                        <div class="flex flex-wrap gap-1.5">
                            @forelse((array)($aiData['core_skills'] ?? []) as $skill)
                            <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-sky-50 text-sky-800 border border-sky-200">
                                {{ $skill }}
                            </span>
                            @empty
                            <span class="text-xs text-slate-400 italic">Tidak ada daftar core skills khusus.</span>
                            @endforelse
                        </div>
                    </div>

                    <!-- Other Candidates Comparison -->
                    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
                        <div class="px-5 py-3 bg-slate-50 border-b border-slate-200 flex items-center gap-2">
                            <i class="fa-solid fa-users text-slate-500"></i>
                            <h5 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Other Candidates Comparison</h5>
                        </div>
                        <div class="divide-y divide-slate-100">
                            @forelse($otherCandidates as $oc)
                            @php
                                $ocScore = intval($oc->ai_score ?? 0);
                                $ocBadgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                if ($ocScore < 60) {
                                    $ocBadgeClass = 'bg-rose-50 text-rose-700 border-rose-200';
                                } elseif ($ocScore < 85) {
                                    $ocBadgeClass = 'bg-amber-50 text-amber-700 border-amber-200';
                                }
                            @endphp
                            <div class="px-4 py-2.5 flex items-center justify-between text-xs hover:bg-slate-50 transition-colors">
                                <div class="font-bold text-slate-800">{{ $oc->full_name }}</div>
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold border {{ $ocBadgeClass }}">
                                    {{ $ocScore }}% Fit
                                </span>
                            </div>
                            @empty
                            <div class="p-4 text-center text-xs text-slate-400 italic">
                                Belum ada kandidat lain yang dianalisa pada posisi {{ $candidate->applied_job }}.
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 4: Work History & Experience -->
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
                <div class="px-5 py-3 bg-slate-50 border-b border-slate-200 flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-slate-600"></i>
                    <h5 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Work History & Experience</h5>
                </div>
                <div class="p-5">
                    <ul class="space-y-2.5 text-xs text-slate-700">
                        @forelse((array)($aiData['work_history'] ?? []) as $wh)
                        <li class="flex items-start gap-2.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-primary-600 mt-1.5 flex-shrink-0"></span>
                            <span class="leading-relaxed">{{ $wh }}</span>
                        </li>
                        @empty
                        <li class="text-slate-400 italic">Belum ada riwayat pengalaman kerja tercatat di berkas CV.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <!-- Section 5: Data Verification (CV vs Form Input) if exists -->
            @if(!empty($aiData['data_discrepancy']))
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 shadow-xs">
                <div class="flex items-center gap-2 mb-2 text-amber-900 font-bold text-xs uppercase tracking-wider">
                    <i class="fa-solid fa-triangle-exclamation text-amber-600"></i>
                    <span>Data Verification (CV vs Form Input)</span>
                </div>
                <p class="text-xs text-amber-950 leading-relaxed font-medium">
                    {!! nl2br(e($aiData['data_discrepancy'])) !!}
                </p>
            </div>
            @endif

            <!-- Section 6: Final Recruiter Recommendation -->
            <div class="bg-gradient-to-r from-emerald-50/90 to-teal-50/70 border border-emerald-200 rounded-2xl p-5 shadow-xs">
                <div class="flex items-center gap-2 mb-2 text-emerald-900 font-bold text-xs uppercase tracking-wider">
                    <i class="fa-solid fa-medal text-emerald-600"></i>
                    <span>Final Recruiter Recommendation</span>
                </div>
                <p class="text-xs text-slate-800 leading-relaxed font-medium">
                    {!! nl2br(e($aiData['recommendation'] ?? ($aiData['ai_verdict'] ?? 'Kandidat memiliki rekam jejak kerja yang relevan dan kualifikasi yang sesuai untuk tahapan rekrutmen.'))) !!}
                </p>
            </div>

            @else
            <!-- Empty State -->
            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-10 text-center space-y-4">
                <div class="w-16 h-16 rounded-3xl bg-indigo-100 text-indigo-600 flex items-center justify-center mx-auto text-2xl shadow-inner">
                    <i class="fa-solid fa-robot"></i>
                </div>
                <div>
                    <h4 class="text-base font-bold text-slate-800">Belum Ada Analisa AI</h4>
                    <p class="text-xs text-slate-500 max-w-md mx-auto mt-1">
                        Berkas CV kandidat belum dianalisis secara otomatis oleh AI CV Analyzer. Klik tombol di bawah untuk memulai analisa kecocokan kualifikasi.
                    </p>
                </div>
                <button type="button" @click="alert('Memulai analisa AI CV...')" class="px-5 py-2.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white text-xs font-bold transition-all shadow-md shadow-primary-500/20 inline-flex items-center gap-2">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                    <span>Analisa CV Sekarang</span>
                </button>
            </div>
            @endif
        </div>

        <!-- ============================================================= -->
        <!-- TAB 7: USER PRINCIPLE / APPROVER INHOUSE -->
        <!-- ============================================================= -->
        <div x-show="activeTab === 'userprinsiple'" class="space-y-6">
            @if(!empty($isInhouseCandidate))
                <!-- KHUSUS KANDIDAT INHOUSE (Matching Gambar 1 & 2) -->
                <div x-data="{ inhouseStatus: '{{ old('status_replace', $candidate->status_replace ?? '') }}' }" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    
                    <!-- KIRI: List Head Approve & Approval HRD -->
                    <div class="lg:col-span-7 space-y-6">
                        <!-- 1. List Head Approve -->
                        <div class="space-y-3">
                            <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                                <i class="fa-solid fa-user-tie text-primary"></i>
                                <span>List Head Approve</span>
                            </h3>
                            <div class="border border-slate-200 rounded-2xl overflow-hidden shadow-2xs bg-white">
                                <table class="w-full text-xs text-left border-collapse">
                                    <thead>
                                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider text-[11px]">
                                            <th class="py-3 px-3.5">Nama</th>
                                            <th class="py-3 px-3.5">Catatan</th>
                                            <th class="py-3 px-3 text-center">Hasil Keputusan</th>
                                            <th class="py-3 px-3 text-center">Tanda Tangan</th>
                                            <th class="py-3 px-3.5 text-center">Waktu Submit</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @php
                                            $headList = $candidate->inhouseApprovals->filter(function($appr) {
                                                $job = strtolower($appr->jabatan_approver ?? '');
                                                return !str_contains($job, 'admin hrd') && !str_contains($job, 'hr lead');
                                            });
                                        @endphp
                                        @forelse($headList as $headAppr)
                                            <tr class="hover:bg-slate-50/70 transition-colors">
                                                <td class="py-3 px-3.5 font-bold text-slate-800">
                                                    <div>{{ $headAppr->nama_approver }}</div>
                                                    <div class="text-[10px] text-slate-400 font-normal">{{ $headAppr->jabatan_approver ?: 'Head' }}</div>
                                                </td>
                                                <td class="py-3 px-3.5 text-slate-600 leading-relaxed max-w-[200px]">
                                                    {{ $headAppr->catatan_approver ?: '-' }}
                                                </td>
                                                <td class="py-3 px-3 text-center">
                                                    @if(in_array(strtolower($headAppr->status ?? ''), ['approve', 'yes']))
                                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">Approve</span>
                                                    @else
                                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-800 border border-rose-200">Tolak</span>
                                                    @endif
                                                </td>
                                                <td class="py-3 px-3 text-center">
                                                    @if($headAppr->ttd_approver)
                                                        @php
                                                            $sigSrc = $headAppr->ttd_approver;
                                                            if (!str_starts_with($sigSrc, 'data:image') && !str_starts_with($sigSrc, 'http')) {
                                                                $sigSrc = asset($sigSrc);
                                                            }
                                                        @endphp
                                                        <img src="{{ $sigSrc }}" alt="TTD Head" class="h-9 max-w-[90px] mx-auto object-contain" onerror="this.src='/lampiran/{{ basename($headAppr->ttd_approver) }}';">
                                                    @else
                                                        <span class="text-[10px] text-slate-400 italic">Belum TTD</span>
                                                    @endif
                                                </td>
                                                <td class="py-3 px-3.5 text-center text-[11px] text-slate-500 whitespace-nowrap">
                                                    {{ $headAppr->time_approver ? \Carbon\Carbon::parse($headAppr->time_approver)->format('d/m/Y H:i') : '-' }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="py-6 text-center text-xs text-slate-400 italic bg-slate-50/50">
                                                    Belum ada approval dari Head
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- 2. Approval HRD -->
                        <div class="space-y-3">
                            <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                                <i class="fa-solid fa-stamp text-primary"></i>
                                <span>Approval HRD</span>
                            </h3>
                            <div class="border border-slate-200 rounded-2xl overflow-hidden shadow-2xs bg-white">
                                <table class="w-full text-xs text-left border-collapse">
                                    <thead>
                                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider text-[11px]">
                                            <th class="py-3 px-3.5">Nama</th>
                                            <th class="py-3 px-3.5">Catatan</th>
                                            <th class="py-3 px-3 text-center">Hasil Keputusan</th>
                                            <th class="py-3 px-3 text-center">Tanda Tangan</th>
                                            <th class="py-3 px-3.5 text-center">Waktu Submit</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @php
                                            $hrdList = $candidate->inhouseApprovals->filter(function($appr) {
                                                $job = strtolower($appr->jabatan_approver ?? '');
                                                return str_contains($job, 'admin hrd') || str_contains($job, 'hr lead') || str_contains($job, 'hrd');
                                            });
                                        @endphp
                                        @forelse($hrdList as $hrdAppr)
                                            <tr class="hover:bg-slate-50/70 transition-colors">
                                                <td class="py-3 px-3.5 font-bold text-slate-800">
                                                    <div>{{ $hrdAppr->nama_approver }}</div>
                                                    <div class="text-[10px] text-slate-400 font-normal">{{ $hrdAppr->jabatan_approver ?: 'HRD Pusat' }}</div>
                                                </td>
                                                <td class="py-3 px-3.5 text-slate-600 leading-relaxed max-w-[200px]">
                                                    {{ $hrdAppr->catatan_approver ?: '-' }}
                                                </td>
                                                <td class="py-3 px-3 text-center">
                                                    @if(in_array(strtolower($hrdAppr->status ?? ''), ['approve', 'yes']))
                                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">Approve</span>
                                                    @else
                                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-800 border border-rose-200">Tolak</span>
                                                    @endif
                                                </td>
                                                <td class="py-3 px-3 text-center">
                                                    @if($hrdAppr->ttd_approver)
                                                        @php
                                                            $sigSrcHrd = $hrdAppr->ttd_approver;
                                                            if (!str_starts_with($sigSrcHrd, 'data:image') && !str_starts_with($sigSrcHrd, 'http')) {
                                                                $sigSrcHrd = asset($sigSrcHrd);
                                                            }
                                                        @endphp
                                                        <img src="{{ $sigSrcHrd }}" alt="TTD HRD" class="h-9 max-w-[90px] mx-auto object-contain" onerror="this.src='/lampiran/{{ basename($hrdAppr->ttd_approver) }}';">
                                                    @else
                                                        <span class="text-[10px] text-slate-400 italic">Belum TTD</span>
                                                    @endif
                                                </td>
                                                <td class="py-3 px-3.5 text-center text-[11px] text-slate-500 whitespace-nowrap">
                                                    {{ $hrdAppr->time_approver ? \Carbon\Carbon::parse($hrdAppr->time_approver)->format('d/m/Y H:i') : '-' }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="py-6 text-center text-xs text-slate-400 italic bg-slate-50/50">
                                                    Belum ada approval dari HRD Pusat
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    @php
                        $inhouseStatusApproval = $candidate->status_approval ?? 'Proses';
                        $isLocked = in_array($inhouseStatusApproval, ['Review Head', 'Review HRD', 'Approve']);
                    @endphp

                    <!-- KANAN: Form Set & Send Approval / Locked Status sesuai Step -->
                    <div class="lg:col-span-5 bg-slate-50/70 border border-slate-200 rounded-2xl p-5 space-y-4">
                        
                        <!-- STEP INDICATOR BAR -->
                        <div class="p-3 bg-white border border-slate-200 rounded-xl space-y-2">
                            <span class="text-[10.5px] font-bold uppercase tracking-wider text-slate-500 block">Alur Approval Inhouse:</span>
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <!-- Step 1 Box -->
                                <div class="p-2 rounded-lg border {{ in_array($inhouseStatusApproval, ['Review Head']) ? 'bg-amber-50 border-amber-300 text-amber-900' : (in_array($inhouseStatusApproval, ['Review HRD', 'Approve']) ? 'bg-emerald-50 border-emerald-300 text-emerald-900' : 'bg-blue-50 border-blue-200 text-blue-900') }}">
                                    <div class="text-[10px] font-bold">STEP 1: HEAD</div>
                                    <div class="text-[11px] font-extrabold flex items-center gap-1 mt-0.5">
                                        @if(in_array($inhouseStatusApproval, ['Review HRD', 'Approve']))
                                            <i class="fa-solid fa-circle-check text-emerald-600 text-xs"></i> Disetujui Head
                                        @elseif($inhouseStatusApproval === 'Review Head')
                                            <i class="fa-solid fa-clock text-amber-500 text-xs"></i> Menunggu Head
                                        @elseif($inhouseStatusApproval === 'Tolak')
                                            <i class="fa-solid fa-circle-xmark text-rose-500 text-xs"></i> Ditolak
                                        @else
                                            <i class="fa-solid fa-arrow-right text-blue-500 text-xs"></i> Siap Dikirim
                                        @endif
                                    </div>
                                </div>

                                <!-- Step 2 Box -->
                                <div class="p-2 rounded-lg border {{ $inhouseStatusApproval === 'Review HRD' ? 'bg-amber-50 border-amber-300 text-amber-900' : ($inhouseStatusApproval === 'Approve' ? 'bg-emerald-50 border-emerald-300 text-emerald-900' : 'bg-slate-100 border-slate-200 text-slate-400') }}">
                                    <div class="text-[10px] font-bold">STEP 2: HRD PUSAT</div>
                                    <div class="text-[11px] font-extrabold flex items-center gap-1 mt-0.5">
                                        @if($inhouseStatusApproval === 'Approve')
                                            <i class="fa-solid fa-circle-check text-emerald-600 text-xs"></i> Selesai (Approved)
                                        @elseif($inhouseStatusApproval === 'Review HRD')
                                            <i class="fa-solid fa-clock text-amber-500 text-xs"></i> Menunggu HRD
                                        @else
                                            <i class="fa-solid fa-lock text-slate-400 text-xs"></i> Terkunci
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($isLocked)
                            <!-- TAMPILAN TERKUNCI (LOCKED VIEW SESUAI STEP) -->
                            <div class="space-y-4">
                                @if($inhouseStatusApproval === 'Review Head')
                                    <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs flex items-start gap-2.5">
                                        <i class="fa-solid fa-lock text-amber-600 mt-0.5 text-sm"></i>
                                        <div>
                                            <strong class="block font-bold">Form Terkunci (Sedang Menunggu Review Head)</strong>
                                            <span class="text-[11px] text-amber-800 leading-relaxed block mt-0.5">Pengajuan telah dikirimkan ke Head Approver. Form dikunci agar tidak terjadi kesalahan perubahan approver.</span>
                                        </div>
                                    </div>
                                @elseif($inhouseStatusApproval === 'Review HRD')
                                    <div class="p-3.5 rounded-xl bg-sky-50 border border-sky-200 text-sky-900 text-xs flex items-start gap-2.5">
                                        <i class="fa-solid fa-lock text-sky-600 mt-0.5 text-sm"></i>
                                        <div>
                                            <strong class="block font-bold">Form Terkunci (Sedang Menunggu Review HRD Pusat)</strong>
                                            <span class="text-[11px] text-sky-800 leading-relaxed block mt-0.5">Head telah menyetujui kandidat ini. Saat ini berkas sedang dalam proses review akhir oleh HRD Pusat.</span>
                                        </div>
                                    </div>
                                @elseif($inhouseStatusApproval === 'Approve')
                                    <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs flex items-start gap-2.5">
                                        <i class="fa-solid fa-circle-check text-emerald-600 mt-0.5 text-sm"></i>
                                        <div>
                                            <strong class="block font-bold">Proses Approval Selesai (Approved)</strong>
                                            <span class="text-[11px] text-emerald-800 leading-relaxed block mt-0.5">Kandidat telah disetujui penuh oleh Head & HRD Pusat dan dipindahkan ke riwayat Selesai.</span>
                                        </div>
                                    </div>
                                @endif

                                <!-- Ringkasan Data Pengajuan yang Terkunci -->
                                <div class="bg-white border border-slate-200 rounded-xl p-3.5 space-y-2.5 text-xs text-slate-700">
                                    <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                        <span class="text-slate-500 font-medium">Status Pengajuan:</span>
                                        <span class="px-2.5 py-0.5 rounded-full text-[10.5px] font-extrabold {{ $inhouseStatusApproval === 'Approve' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                            {{ $inhouseStatusApproval }}
                                        </span>
                                    </div>

                                    <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                        <span class="text-slate-500 font-medium">Pengaju (User Request):</span>
                                        <span class="font-bold text-slate-900">{{ $candidate->user_request ?: '-' }}</span>
                                    </div>

                                    <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                        <span class="text-slate-500 font-medium">Berkas Lamaran:</span>
                                        @if($candidate->berkas_lamaran)
                                            <a href="{{ route('interviewinhouse.berkas', $candidate->id) }}" target="_blank" class="font-bold underline text-primary hover:text-primary-700 inline-flex items-center gap-1">
                                                <i class="fa-solid fa-file-lines text-xs"></i> Lihat Berkas
                                            </a>
                                        @else
                                            <span class="text-slate-400 italic">Tidak ada berkas</span>
                                        @endif
                                    </div>

                                    <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                        <span class="text-slate-500 font-medium">Status Formasi:</span>
                                        <span class="font-bold text-slate-900">{{ $candidate->status_replace === 'Replace' ? 'Replace' : 'New' }}</span>
                                    </div>

                                    @if($candidate->status_replace === 'Replace')
                                        <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                            <span class="text-slate-500 font-medium">Menggantikan:</span>
                                            <span class="font-bold text-slate-900">{{ $candidate->menggantikan ?: '-' }}</span>
                                        </div>
                                        <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                            <span class="text-slate-500 font-medium">Tanggal Resign:</span>
                                            <span class="font-bold text-slate-900">{{ $candidate->tgl_resign ? \Carbon\Carbon::parse($candidate->tgl_resign)->format('d/m/Y') : '-' }}</span>
                                        </div>
                                        <div class="flex items-start justify-between">
                                            <span class="text-slate-500 font-medium shrink-0 w-28">Alasan Resign:</span>
                                            <span class="font-medium text-slate-800 text-right">{{ $candidate->alasan_resign ?: '-' }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @else
                            <!-- FORM AKTIF STEP 1: PENGIRIMAN KE HEAD APPROVER -->
                            <form action="{{ route('interview.inhouse_approval', $candidate->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                @csrf

                                <!-- Nama Approver Inhouse (Step 1: Pilihan Head / Pimpinan) -->
                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <label class="block text-xs font-bold text-slate-700">Nama Approver Inhouse</label>
                                        <span class="px-2 py-0.5 text-[9.5px] font-extrabold bg-blue-100 text-blue-800 rounded-md">Step 1: Head Approver</span>
                                    </div>
                                    <select name="nama_approver" class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:ring-4 focus:ring-primary-100 focus:border-primary outline-none cursor-pointer" required>
                                        <option value="" disabled selected>-- Pilih Head Approver (Pimpinan) --</option>
                                        @foreach($inhouseApproverOptions as $opt)
                                            <option value="{{ $opt }}">{{ $opt }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-[10.5px] text-slate-500 mt-1 block italic">* Terkunci pada Step 1 (Pimpinan / Head). Pilihan HRD Pusat akan otomatis aktif di Step 2 setelah Head menyetujui.</span>
                                </div>

                                <!-- Berkas Lamaran Kandidat -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Berkas Lamaran Kandidat</label>
                                    <div class="bg-white border border-slate-200 rounded-xl p-2.5">
                                        <input type="file" name="berkas_lamaran" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="w-full text-xs text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                                    </div>
                                    @if($candidate->berkas_lamaran)
                                        <div class="mt-2 flex items-center justify-between px-3 py-1.5 bg-sky-50 rounded-xl border border-sky-200 text-[11px] text-sky-800">
                                            <span class="truncate max-w-[200px] font-medium"><i class="fa-solid fa-file-lines text-sky-600 mr-1"></i> {{ basename($candidate->berkas_lamaran) }}</span>
                                            <a href="{{ route('interviewinhouse.berkas', $candidate->id) }}" target="_blank" class="font-bold underline text-sky-700 hover:text-sky-900 shrink-0">Lihat File</a>
                                        </div>
                                    @endif
                                </div>

                                <!-- Status (New / Replace) -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Status</label>
                                    <select name="status_replace" x-model="inhouseStatus" class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:ring-4 focus:ring-primary-100 focus:border-primary outline-none cursor-pointer" required>
                                        <option value="">-- Pilih Status --</option>
                                        <option value="New">New</option>
                                        <option value="Replace">Replace</option>
                                    </select>
                                </div>

                                <!-- Form Tambahan Khusus Status Replace (Persis Gambar 2) -->
                                <div x-show="inhouseStatus === 'Replace'" x-transition class="space-y-4 pt-1 border-t border-slate-200/80">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Menggantikan</label>
                                        <input type="text" name="menggantikan" value="{{ old('menggantikan', $candidate->menggantikan) }}" placeholder="Nama yang digantikan" class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:ring-4 focus:ring-primary-100 focus:border-primary outline-none">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggal Resign</label>
                                        <input type="date" name="tgl_resign" value="{{ old('tgl_resign', $candidate->tgl_resign) }}" class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:ring-4 focus:ring-primary-100 focus:border-primary outline-none">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Alasan Resign</label>
                                        <input type="text" name="alasan_resign" value="{{ old('alasan_resign', $candidate->alasan_resign) }}" placeholder="Alasan pengunduran diri" class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:ring-4 focus:ring-primary-100 focus:border-primary outline-none">
                                    </div>
                                </div>

                                <!-- Tombol Submit Set & Send Approval -->
                                <div class="pt-2">
                                    <button type="submit" class="w-full py-3 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 border border-rose-600 shadow-md shadow-rose-600/20 transition-all flex items-center justify-center gap-2 cursor-pointer">
                                        <i class="fa-solid fa-paper-plane text-xs"></i>
                                        <span>Set & Kirim Approval ke Head (Step 1)</span>
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>

                </div>
            @else
                <!-- REGULAR USER PRINCIPLE FORM -->
                @if(!empty($isUserPrinsipleDisabled))
                    <!-- Warning / Disqualification Banner -->
                    <div class="p-4 rounded-2xl bg-rose-50 border-2 border-rose-200 text-rose-900 shadow-sm flex items-start gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 border border-rose-200 flex items-center justify-center flex-shrink-0 text-lg">
                            <i class="fa-solid fa-ban"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <h4 class="text-sm font-bold text-rose-900">Tab User Principle Dinonaktifkan</h4>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-200 text-rose-800 uppercase">Tidak Memenuhi Kriteria</span>
                            </div>
                            <div class="mt-2 space-y-1.5">
                                @foreach($userPrinsipleDisableReasons ?? [] as $reason)
                                    <div class="text-xs font-semibold text-rose-800 flex items-start gap-2">
                                        <i class="fa-solid fa-circle-xmark text-rose-500 mt-0.5 flex-shrink-0"></i>
                                        <span>{{ $reason }}</span>
                                    </div>
                                @endforeach
                            </div>
                            <p class="text-[11px] text-rose-600 font-medium mt-2.5 border-t border-rose-200/60 pt-2">
                                * Kandidat tidak dapat diajukan / dikirim ke User Prinsiple karena tidak memenuhi syarat kelulusan seleksi.
                            </p>
                        </div>
                    </div>
                @endif
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    
                    <!-- Left: Approval Info & Notes -->
                    <div class="lg:col-span-7 space-y-4">
                        <h3 class="text-sm font-bold text-slate-800 pb-2 border-b border-slate-100 flex items-center gap-2">
                            <i class="fa-solid fa-stamp text-primary-600"></i>
                            di Approve Oleh Prinsiple :
                        </h3>

                        <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-4 space-y-2 text-xs text-slate-700">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500 font-medium">Nama Approver:</span>
                                <strong class="text-slate-900">
                                    @if($candidate->approverPrinsiple)
                                        {{ $candidate->approverPrinsiple->nama_lengkap }} ({{ $candidate->approverPrinsiple->jabatan ?? 'Manager' }})
                                    @elseif($candidate->principle)
                                        {{ $candidate->principle->name }} (Manager)
                                    @else
                                        PT ARINA MULTI KARYA (Manager)
                                    @endif
                                </strong>
                            </div>
                            <div class="flex items-center justify-between border-t border-slate-200/60 pt-2">
                                <span class="text-slate-500 font-medium">Status Approval:</span>
                                @if(in_array(strtolower($candidate->status_approval ?? ''), ['approve', 'approved']))
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        Approved
                                    </span>
                                @elseif(in_array(strtolower($candidate->status_approval ?? ''), ['tolak', 'rejected']))
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                        Rejected
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                        Menunggu Approval
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="pt-2">
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Note Prinsiple :</label>
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 text-xs {{ !empty($candidate->note_principle) ? 'text-slate-700 font-medium' : 'text-slate-500 italic' }} leading-relaxed">
                                {{ $candidate->note_principle ?: 'Belum ada catatan persetujuan dari User Prinsiple.' }}
                            </div>
                        </div>

                        <!-- Lampiran Bukti Approval Prinsiple (Screenshot WA / TTD Digital) -->
                        <div class="pt-2">
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Lampiran Bukti Approval Prinsiple :</label>
                            @if($candidate->approval_proof_url)
                                <div class="p-3.5 bg-white border border-slate-200 rounded-2xl flex items-center justify-between gap-3 shadow-xs">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-12 h-12 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden flex-shrink-0 cursor-pointer group relative" onclick="openCandidateMedia('image', '{{ $candidate->approval_proof_url }}', 'Bukti Approval Prinsiple: {{ addslashes($candidate->full_name) }}')">
                                            <img src="{{ $candidate->approval_proof_url }}" alt="Approval Proof" class="w-full h-full object-cover transition-transform group-hover:scale-105" onerror="this.onerror=null; this.src='{{ $candidate->approval_legacy_url }}';">
                                        </div>
                                        <div class="min-w-0">
                                            <div class="text-xs font-bold text-slate-900 truncate">{{ basename($candidate->ttd_prinsiple ?? $candidate->principleApprovals->first()?->signature_path ?? '') }}</div>
                                            <div class="text-[10px] text-emerald-600 font-semibold flex items-center gap-1 mt-0.5">
                                                <i class="fa-solid fa-circle-check text-[9px]"></i> Berkas Tersimpan / Fallback Live V3
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1.5 flex-shrink-0">
                                        <button type="button" onclick="openCandidateMedia('image', '{{ $candidate->approval_proof_url }}', 'Bukti Approval Prinsiple: {{ addslashes($candidate->full_name) }}')" class="px-3 py-1.5 rounded-xl bg-primary-50 text-primary-700 hover:bg-primary-100 text-xs font-bold transition-colors inline-flex items-center gap-1.5">
                                            <i class="fa-solid fa-eye text-xs"></i>
                                            <span>Preview</span>
                                        </button>
                                        <a href="{{ $candidate->approval_proof_url }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 text-xs font-bold transition-colors inline-flex items-center gap-1.5">
                                            <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                                            <span>Buka</span>
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5 text-xs text-slate-400 italic">
                                    Belum ada berkas lampiran bukti approval dari User Prinsiple.
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Right: Form to Set & Send to Principle with Ultra Attractive File Upload -->
                    <div class="lg:col-span-5 bg-slate-50/70 border border-slate-200 rounded-2xl p-5 space-y-4">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-2">
                            <i class="fa-solid fa-user-check text-primary-600"></i>
                            Set Approval Prinsiple
                        </h4>
                        
                        <form action="{{ route('interview.principleApproval', $candidate->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Approver Prinsiple</label>
                                <select name="userprinsiple" class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:ring-4 focus:ring-primary-100 focus:border-primary-600 outline-none" required>
                                    <option value="" disabled {{ empty($candidate->idprinsiple) ? 'selected' : '' }}>-- Pilih User Prinsiple ({{ $candidate->area ?? 'Area' }}) --</option>
                                    @forelse($userPrinsiples ?? $candidate->user_prinsiple_options as $up)
                                        <option value="{{ $up->id }}" {{ (old('userprinsiple', $candidate->idprinsiple) == $up->id) ? 'selected' : '' }}>
                                            {{ $up->nama_lengkap }} - {{ $up->jabatan }} ({{ $up->prinsiple }} - {{ $up->area }})
                                        </option>
                                    @empty
                                        <option value="" disabled>Tidak ada User Prinsiple yang cocok di area ini</option>
                                    @endforelse
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Yes / No</label>
                                <select name="statusapprove" class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:ring-4 focus:ring-primary-100 focus:border-primary-600 outline-none" required>
                                    <option value="" selected disabled>Pilih Hasil Approval</option>
                                    <option value="Yes" {{ in_array(strtolower($candidate->status_approval ?? ''), ['approve', 'approved']) ? 'selected' : '' }}>Yes</option>
                                    <option value="No" {{ in_array(strtolower($candidate->status_approval ?? ''), ['tolak', 'rejected']) ? 'selected' : '' }}>No</option>
                                </select>
                            </div>

                            <!-- Highly Attractive File Upload for Approval Screenshot -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Upload Screenshot Approval</label>
                                
                                <div class="relative border-2 border-dashed border-slate-300 hover:border-primary-500 rounded-2xl bg-white p-4 text-center transition-all group">
                                    <input type="file" 
                                           name="approval_screenshot" 
                                           id="approvalScreenshotInput" 
                                           accept="image/jpeg,image/png,image/jpg" 
                                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                           onchange="handleApprovalProofPreview(this)">

                                    <div id="approvalUploadPrompt" class="space-y-1.5">
                                        <div class="w-10 h-10 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center mx-auto shadow-xs group-hover:scale-105 transition-transform">
                                            <i class="fa-solid fa-file-arrow-up text-lg"></i>
                                        </div>
                                        <span class="text-xs font-bold text-slate-700 block">Pilih Screenshot Bukti</span>
                                        <span class="text-[10px] text-primary-600 italic block">Upload SS Jika Approval Via Email / Whatsapp</span>
                                    </div>

                                    <!-- Live Preview Container -->
                                    <div id="approvalPreviewContainer" class="hidden flex flex-col items-center space-y-1.5">
                                        <img id="approvalPreviewImage" class="max-h-28 rounded-lg object-contain shadow-xs border border-slate-200">
                                        <span id="approvalFileName" class="text-[11px] font-mono text-slate-700 font-bold"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-2">
                                @if(!empty($isUserPrinsipleDisabled))
                                    <button type="button" disabled class="w-full py-3 rounded-xl text-xs font-bold bg-slate-200 text-slate-400 cursor-not-allowed border border-slate-300 flex items-center justify-center gap-2 shadow-none">
                                        <i class="fa-solid fa-ban text-xs text-rose-500"></i>
                                        <span>Pengajuan Dinonaktifkan (Tidak Memenuhi Syarat)</span>
                                    </button>
                                @else
                                    <button type="submit" class="w-full py-3 rounded-xl text-xs font-bold bg-rose-600 hover:bg-rose-700 text-white shadow-md shadow-rose-600/25 transition-all flex items-center justify-center gap-2">
                                        <i class="fa-solid fa-paper-plane text-xs"></i>
                                        <span>Set & Send To User Prinsiple</span>
                                    </button>
                                @endif
                            </div>
                        </form>
                    </div>

                </div>
            @endif
        </div>

    </div>

    <!-- MODAL EDIT PRINSIPLE -->
    <div x-show="editPrincipleModal" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="editPrincipleModal = false" 
             class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h4 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-pencil text-primary-600"></i>
                    Edit Prinsiple Kandidat
                </h4>
                <button @click="editPrincipleModal = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <form action="{{ route('interview.editPrinciple', $candidate->id) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Pilih Prinsiple</label>
                    <select name="principle_id" class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-semibold text-slate-800 focus:ring-4 focus:ring-primary-100 focus:border-primary-600 outline-none" required>
                        @foreach($principles as $p)
                            <option value="{{ $p->id }}" {{ ($candidate->principle_id == $p->id) ? 'selected' : '' }}>
                                {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button @click="editPrincipleModal = false" type="button" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">
                        Close
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold bg-primary-600 hover:bg-primary-700 text-white shadow-sm">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL ARSIPKAN KANDIDAT -->
    <div x-show="archiveModal" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="archiveModal = false" 
             class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h4 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-box-archive text-rose-600"></i>
                    Arsipkan Kandidat
                </h4>
                <button @click="archiveModal = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <form action="{{ route('interview.archive', $candidate->id) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Alasan Arsip</label>
                    <textarea name="alasan" rows="3" class="w-full bg-white border border-slate-200 rounded-xl p-3 text-xs text-slate-800 focus:ring-4 focus:ring-rose-100 focus:border-rose-600 outline-none" placeholder="Masukkan alasan pengarsipan kandidat..."></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button @click="archiveModal = false" type="button" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">
                        Close
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold bg-rose-600 hover:bg-rose-700 text-white shadow-sm">
                        Arsipkan
                    </button>
                </div>
            </form>
        </div>
    </div>



    <!-- MODAL ALIKAN KE AS -->
    <div x-show="alihkanModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="alihkanModalOpen = false" 
             class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h4 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-user-plus text-primary"></i>
                    Alihkan Kandidat ke AS Lain
                </h4>
                <button @click="alihkanModalOpen = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <form action="{{ route('interview.alihkan', $candidate->id) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Pilih Prinsiple</label>
                    <select name="prinsiple_id" class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:ring-4 focus:ring-primary-100 focus:border-primary-600 outline-none">
                        @foreach($principles as $p)
                            <option value="{{ $p->id }}" {{ ($candidate->principle_id == $p->id) ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">User AS / Rekruter Tujuan</label>
                    <input type="email" name="useras" value="{{ $candidate->useras ?? 'admin.pusat@arina.co.id' }}" required class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:ring-4 focus:ring-primary-100 focus:border-primary-600 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Catatan Pengalihan (Opsional)</label>
                    <textarea name="notes" rows="2" class="w-full bg-white border border-slate-200 rounded-xl p-3 text-xs text-slate-800 focus:ring-4 focus:ring-primary-100 focus:border-primary-600 outline-none" placeholder="Alasan pengalihan kandidat..."></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button @click="alihkanModalOpen = false" type="button" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold bg-primary hover:bg-primary-700 text-white shadow-sm">
                        Simpan & Alihkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL GANTI AREA & PRINSIPLE -->
    <div x-show="gantiAreaModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="gantiAreaModalOpen = false" 
             class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h4 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-location-dot text-amber-600"></i>
                    Ganti Area & Prinsiple Penempatan
                </h4>
                <button @click="gantiAreaModalOpen = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <form action="{{ route('interview.ganti-area', $candidate->id) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-map-pin text-amber-600"></i>
                        <span>Area Penempatan</span>
                    </label>
                    <select name="area" class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:ring-4 focus:ring-amber-100 focus:border-amber-500 outline-none cursor-pointer" required>
                        @php
                            $allAreas = $areas ?? [
                                'JAKARTA', 'SURABAYA', 'BANDUNG', 'SEMARANG', 'MEDAN', 
                                'MAKASSAR', 'DENPASAR', 'PALEMBANG', 'BALIKPAPAN', 'YOGYAKARTA',
                                'MALANG', 'BOGOR', 'BEKASI', 'TANGERANG', 'DEPOK'
                            ];
                            if (!empty($candidate->area) && !in_array($candidate->area, $allAreas)) {
                                $allAreas[] = $candidate->area;
                            }
                        @endphp
                        @foreach($allAreas as $ar)
                            <option value="{{ $ar }}" {{ ($candidate->area == $ar) ? 'selected' : '' }}>{{ $ar }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-building text-amber-600"></i>
                        <span>Prinsiple Penempatan</span>
                    </label>
                    <select name="principle_id" class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:ring-4 focus:ring-amber-100 focus:border-amber-500 outline-none cursor-pointer" required>
                        <option value="">-- Pilih Prinsiple --</option>
                        @foreach($principles as $p)
                            @php
                                $isSelected = ($candidate->principle_id == $p->id)
                                    || ($candidate->principle && is_object($candidate->principle) && $candidate->principle->id == $p->id)
                                    || (is_string($candidate->principle) && strtolower(trim($candidate->principle)) === strtolower(trim($p->name)));
                            @endphp
                            <option value="{{ $p->id }}" {{ $isSelected ? 'selected' : '' }}>
                                {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button @click="gantiAreaModalOpen = false" type="button" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold bg-amber-600 hover:bg-amber-700 text-white shadow-sm transition cursor-pointer">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- CANDIDATE MEDIA PREVIEW MODAL -->
    @include('partials.candidate-media-modal')
</div>

@push('scripts')
<script>
    // Live Image Preview Functions
    function handleCandidatePhotoPreview(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('candidatePhotoPreview');
                const placeholder = document.getElementById('candidatePhotoPlaceholder');
                if (preview) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                }
                if (placeholder) {
                    placeholder.classList.add('hidden');
                }
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // --- REFERENSI CEK DROPDOWN & PROOF HANDLER ---
    const candidateWorkExps = @json($candidate->workExperiences);

    function handleCompanySelect(val) {
        const customWrapper = document.getElementById('customCompanyWrapper');
        const customInput = document.getElementById('customCompanyName');

        if (val === 'new') {
            if (customWrapper) customWrapper.style.display = 'block';
            if (customInput) {
                customInput.value = '';
                customInput.focus();
            }
            if (document.getElementById('refcek_spv')) document.getElementById('refcek_spv').value = '';
            if (document.getElementById('refcek_phone')) document.getElementById('refcek_phone').value = '';
            if (document.getElementById('refcek_performance')) document.getElementById('refcek_performance').value = 'Baik';
            if (document.getElementById('refcek_discipline')) document.getElementById('refcek_discipline').value = 'Tepat Waktu';
            if (document.getElementById('refcek_responsibility')) document.getElementById('refcek_responsibility').value = 'Bertanggung Jawab';
            if (document.getElementById('refcek_strengths')) document.getElementById('refcek_strengths').value = '';
            if (document.getElementById('refcek_weaknesses')) document.getElementById('refcek_weaknesses').value = '';
            return;
        }

        if (customWrapper) customWrapper.style.display = 'none';

        const exp = candidateWorkExps.find(e => e.id == val);
        if (exp) {
            if (document.getElementById('refcek_spv')) document.getElementById('refcek_spv').value = exp.supervisor_name || '';
            if (document.getElementById('refcek_phone')) document.getElementById('refcek_phone').value = exp.company_phone || '';
            if (document.getElementById('refcek_performance')) document.getElementById('refcek_performance').value = exp.performance_notes || exp.performa || 'Baik';
            if (document.getElementById('refcek_discipline')) document.getElementById('refcek_discipline').value = exp.discipline_notes || exp.disiplin || 'Tepat Waktu';
            if (document.getElementById('refcek_responsibility')) document.getElementById('refcek_responsibility').value = exp.responsibility_notes || exp.tanggungjawab || 'Bertanggung Jawab';
            if (document.getElementById('refcek_strengths')) document.getElementById('refcek_strengths').value = exp.strengths || exp.streng || '';
            if (document.getElementById('refcek_weaknesses')) document.getElementById('refcek_weaknesses').value = exp.weaknesses || exp.week || '';
            updateRefcekProofUI(exp);
        } else {
            updateRefcekProofUI(null);
        }
    }

    let currentExpProofUrl = '{{ $firstExp?->proof_url ?? "" }}';
    let currentExpCompanyName = '{{ addslashes($firstExp?->company_name ?? "") }}';

    function updateRefcekProofUI(exp) {
        const hasProofBox = document.getElementById('refcek_has_proof');
        const noProofBox = document.getElementById('refcek_no_proof');
        const filenameEl = document.getElementById('refcek_proof_filename');
        const linkEl = document.getElementById('refcek_proof_link');

        if (exp && exp.proof_url) {
            currentExpProofUrl = exp.proof_url;
            currentExpCompanyName = exp.company_name || '';
            const fname = (exp.proof_attachment_path || '').split('/').pop().split('\\').pop();
            if (filenameEl) filenameEl.textContent = fname;
            if (linkEl) linkEl.href = exp.proof_url;
            const thumbEl = document.getElementById('refcek_proof_thumb');
            if (thumbEl) {
                thumbEl.src = exp.proof_url;
                thumbEl.onerror = () => { thumbEl.src = exp.proof_legacy_url || ''; };
            }
            if (hasProofBox) hasProofBox.style.display = 'flex';
            if (noProofBox) noProofBox.style.display = 'none';
        } else {
            currentExpProofUrl = '';
            currentExpCompanyName = '';
            if (hasProofBox) hasProofBox.style.display = 'none';
            if (noProofBox) noProofBox.style.display = 'block';
        }
    }

    function previewCurrentRefcek() {
        if (currentExpProofUrl) {
            openCandidateMedia('image', currentExpProofUrl, 'Bukti Referensi Cek: ' + currentExpCompanyName);
        }
    }

    function handleWaProofPreview(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const container = document.getElementById('waProofPreviewContainer');
                const prompt = document.getElementById('waProofUploadPrompt');
                const img = document.getElementById('waProofPreviewImage');
                const name = document.getElementById('waProofFileName');
                
                if (img) img.src = e.target.result;
                if (name) name.textContent = input.files[0].name;
                if (container) container.classList.remove('hidden');
                if (prompt) prompt.classList.add('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function handleApprovalProofPreview(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const container = document.getElementById('approvalPreviewContainer');
                const prompt = document.getElementById('approvalUploadPrompt');
                const img = document.getElementById('approvalPreviewImage');
                const name = document.getElementById('approvalFileName');
                
                if (img) img.src = e.target.result;
                if (name) name.textContent = input.files[0].name;
                if (container) container.classList.remove('hidden');
                if (prompt) prompt.classList.add('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Signature Pad logic using HTML5 Canvas
    const mySavedSigUrl = @json($mySavedSigUrl);
    const initialSigUrl = @json($initialSigUrl);
    let canvas, ctx, isDrawing = false, hasDrawn = false;

    function initSignatureCanvas() {
        canvas = document.getElementById('signatureCanvas');
        if (!canvas) return;

        ctx = canvas.getContext('2d');
        if (!ctx) return;

        // Ensure canvas width & height match client dimensions for 1:1 pixel accuracy
        const rect = canvas.getBoundingClientRect();
        if (rect.width > 0 && rect.height > 0) {
            canvas.width = Math.round(rect.width);
            canvas.height = Math.round(rect.height);
        } else {
            canvas.width = 400;
            canvas.height = 240;
        }

        ctx.lineWidth = 2.5;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
        ctx.strokeStyle = '#0f172a';

        if (initialSigUrl) {
            loadSigImageToCanvas(initialSigUrl);
        }

        function getPos(e) {
            const r = canvas.getBoundingClientRect();
            const scaleX = canvas.width / (r.width || 1);
            const scaleY = canvas.height / (r.height || 1);
            let clientX, clientY;
            if (e.touches && e.touches.length > 0) {
                clientX = e.touches[0].clientX;
                clientY = e.touches[0].clientY;
            } else if (e.changedTouches && e.changedTouches.length > 0) {
                clientX = e.changedTouches[0].clientX;
                clientY = e.changedTouches[0].clientY;
            } else {
                clientX = e.clientX;
                clientY = e.clientY;
            }
            return {
                x: (clientX - r.left) * scaleX,
                y: (clientY - r.top) * scaleY
            };
        }

        function startDraw(e) {
            isDrawing = true;
            hasDrawn = true;
            const pos = getPos(e);
            ctx.beginPath();
            ctx.moveTo(pos.x, pos.y);
            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();
            if (e.cancelable && e.type && e.type.startsWith('touch')) e.preventDefault();
        }

        function draw(e) {
            if (!isDrawing) return;
            const pos = getPos(e);
            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();
            if (e.cancelable && e.type && e.type.startsWith('touch')) e.preventDefault();
        }

        function stopDraw(e) {
            if (isDrawing) {
                isDrawing = false;
                ctx.closePath();
            }
        }

        // Pointer Events (supports stylus, finger touch, and mouse seamlessly)
        if (window.PointerEvent) {
            canvas.addEventListener('pointerdown', function(e) {
                try { canvas.setPointerCapture(e.pointerId); } catch(_) {}
                startDraw(e);
            });
            canvas.addEventListener('pointermove', draw);
            canvas.addEventListener('pointerup', function(e) {
                stopDraw(e);
                try { canvas.releasePointerCapture(e.pointerId); } catch(_) {}
            });
            canvas.addEventListener('pointercancel', function(e) {
                stopDraw(e);
                try { canvas.releasePointerCapture(e.pointerId); } catch(_) {}
            });
        } else {
            // Fallback for older browsers
            canvas.addEventListener('mousedown', startDraw);
            canvas.addEventListener('mousemove', draw);
            window.addEventListener('mouseup', stopDraw);

            canvas.addEventListener('touchstart', startDraw, { passive: false });
            canvas.addEventListener('touchmove', draw, { passive: false });
            window.addEventListener('touchend', stopDraw);
            window.addEventListener('touchcancel', stopDraw);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSignatureCanvas);
    } else {
        initSignatureCanvas();
    }

    // Re-check size on resize if user has not started drawing yet
    window.addEventListener('resize', function() {
        if (!hasDrawn && canvas) {
            const r = canvas.getBoundingClientRect();
            if (r.width > 0 && r.height > 0) {
                canvas.width = Math.round(r.width);
                canvas.height = Math.round(r.height);
                ctx.lineWidth = 2.5;
                ctx.lineCap = 'round';
                ctx.lineJoin = 'round';
                ctx.strokeStyle = '#0f172a';
            }
        }
    });

    function loadSigImageToCanvas(dataUrl) {
        if (!canvas || !ctx || !dataUrl) return;
        const img = new Image();
        img.onload = function() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
            hasDrawn = true;
            const input = document.getElementById('signatureDataInput');
            if (input) input.value = dataUrl;
        };
        img.src = dataUrl;
    }

    function pasteMySavedSignature() {
        if (!mySavedSigUrl) {
            alert('Anda belum memiliki tanda tangan tersimpan. Silakan gambar tanda tangan baru terlebih dahulu.');
            return;
        }
        loadSigImageToCanvas(mySavedSigUrl);
        updateSigBadge('TTD Saya Ditempel', 'emerald');
    }

    function startNewSignature() {
        clearSignatureCanvas();
        updateSigBadge('Mode Gambar TTD Baru', 'blue');
    }

    function clearSignatureCanvas() {
        if (!ctx || !canvas) return;
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        hasDrawn = false;
        const input = document.getElementById('signatureDataInput');
        if (input) input.value = '';
        updateSigBadge('Belum Ada TTD', 'slate');
    }

    function updateSigBadge(text, color) {
        const badge = document.getElementById('sigBadge');
        const badgeText = document.getElementById('sigBadgeText');
        const badgeIcon = document.getElementById('sigBadgeIcon');
        if (!badge || !badgeText) return;

        badgeText.textContent = text;
        badge.className = 'inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold';
        if (color === 'emerald') {
            badge.classList.add('bg-emerald-50', 'border', 'border-emerald-200', 'text-emerald-700');
            if (badgeIcon) badgeIcon.className = 'fa-solid fa-circle-check text-emerald-600';
        } else if (color === 'blue') {
            badge.classList.add('bg-blue-50', 'border', 'border-blue-200', 'text-blue-700');
            if (badgeIcon) badgeIcon.className = 'fa-solid fa-pen-nib text-blue-600';
        } else {
            badge.classList.add('bg-slate-100', 'text-slate-500');
            if (badgeIcon) badgeIcon.className = 'fa-solid fa-eraser text-slate-400';
        }
    }

    function syncSignatureData() {
        const input = document.getElementById('signatureDataInput');
        if (canvas && hasDrawn && input) {
            try {
                // If it's a freshly drawn signature, export canvas to dataUrl
                input.value = canvas.toDataURL('image/png');
            } catch (e) {
                console.warn('Signature canvas export note:', e);
            }
        }
    }

    function submitInterviewForm() {
        const form = document.getElementById('interviewForm');
        if (!form) return;
        syncSignatureData();
        form.submit();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('interviewForm');
        if (form) {
            form.addEventListener('submit', function() {
                syncSignatureData();
            });
        }
    });
</script>
@endpush
@endsection