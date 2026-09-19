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
                <a href="{{ route('interviewinhouse.index') }}" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition-colors shadow-xs">
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

            <!-- Ganti Area -->
            <button @click="gantiAreaModalOpen = true" type="button" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200 shadow-sm transition-all">
                <i class="fa-solid fa-location-dot text-amber-600"></i>
                <span>Ganti Area</span>
            </button>

            <!-- Arsipkan -->
            <button @click="archiveModal = true" type="button" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 shadow-sm transition-all">
                <i class="fa-solid fa-box-archive"></i>
                <span>Arsipkan</span>
            </button>

            <!-- Download PDF (Blue Button) -->
            <a href="{{ route('interview.pdf', $candidate->id) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white bg-primary hover:bg-primary-700 shadow-md shadow-primary-500/20 transition-all">
                <i class="fa-solid fa-file-pdf"></i>
                <span>Download All Document</span>
            </a>

            <!-- Berkas Lamaran Button -->
            <a href="{{ route('interviewinhouse.berkas', $candidate->id) }}" target="_blank" 
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all shadow-sm {{ empty($candidate->berkas_lamaran) ? 'bg-slate-100 text-slate-400 border border-slate-200 cursor-not-allowed opacity-60' : 'bg-sky-50 text-sky-700 hover:bg-sky-100 border border-sky-200' }}"
               style="{{ empty($candidate->berkas_lamaran) ? 'pointer-events:none' : '' }}"
               title="{{ empty($candidate->berkas_lamaran) ? 'User belum melampirkan berkas lamaran' : 'Unduh Berkas Lamaran' }}">
                <i class="fa-solid fa-file-lines {{ empty($candidate->berkas_lamaran) ? 'text-slate-400' : 'text-sky-600' }}"></i>
                <span>Berkas Lamaran</span>
            </a>
        </div>
    </div>

    
    @if(empty($candidate->berkas_lamaran))
    <div class="px-4 py-2 rounded-xl bg-slate-50 border border-slate-200/80 text-[11px] text-slate-500 italic flex items-center gap-2">
        <i class="fa-solid fa-circle-info text-slate-400"></i>
        <span>Jika Tombol Berkas Lamaran Berwarna Abu-abu & Tidak Bisa di Klik, Artinya User Belum Melampirkan Berkas Lamaran Kandidat.</span>
    </div>
    @endif

    <!-- PROFIL KANDIDAT CARD (11 DATA POINTS + FOTO DROPZONE - Identik dengan Detail Kandidat Portal) -->
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
                            @if(strtolower($candidate->gender ?? '') === 'perempuan')
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

                    <!-- 8. User Request -->
                    <div class="flex items-start gap-2">
                        <span class="w-32 text-slate-400 font-medium flex-shrink-0">User Request:</span>
                        <span class="font-bold text-slate-800">{{ $candidate->user_request ?: ($candidate->useras ?: 'Admin Operasional') }}</span>
                    </div>

                    <!-- 9. Status Pengajuan -->
                    <div class="flex items-start gap-2">
                        <span class="w-32 text-slate-400 font-medium flex-shrink-0">Status Pengajuan:</span>
                        <span class="badge-pill {{ $candidate->status_replace === 'Replace' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200' }}">
                            Kandidat {{ $candidate->status_replace ?: 'Baru' }}
                        </span>
                    </div>

                    <!-- 10. Status Approval -->
                    <div class="flex items-start gap-2">
                        <span class="w-32 text-slate-400 font-medium flex-shrink-0">Status Approval:</span>
                        <span class="badge-pill {{ match($candidate->status_approval) {
                            'Approve' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'Tolak' => 'bg-rose-50 text-rose-700 border-rose-200',
                            'Review HRD' => 'bg-sky-50 text-sky-700 border-sky-200',
                            'Review Head' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                            default => 'bg-slate-100 text-slate-600 border-slate-200'
                        } }}">
                            {{ $candidate->status_approval ?: 'Review Head' }}
                        </span>
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
                        <span class="text-slate-600 italic">{{ $candidate->notes ?? 'Tidak ada catatan tambahan.' }}</span>
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

    
    <!-- FORMASI STATUS PENGAJUAN CARD (Baru vs Replace) -->
    <div class="p-5 rounded-2xl border {{ $candidate->status_replace === 'Replace' ? 'bg-amber-50/70 border-amber-200' : 'bg-emerald-50/70 border-emerald-200' }} shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl {{ $candidate->status_replace === 'Replace' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' }} flex items-center justify-center text-lg font-bold">
                    <i class="fa-solid {{ $candidate->status_replace === 'Replace' ? 'fa-user-gear' : 'fa-user-plus' }}"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold {{ $candidate->status_replace === 'Replace' ? 'text-amber-900' : 'text-emerald-900' }}">
                        Status Formasi Inhouse: Kandidat {{ $candidate->status_replace ?: 'Baru' }}
                    </h3>
                    <p class="text-xs {{ $candidate->status_replace === 'Replace' ? 'text-amber-700' : 'text-emerald-700' }}">
                        {{ $candidate->status_replace === 'Replace' ? 'Pengajuan penggantian karyawan yang telah resign' : 'Pengajuan penambahan tenaga kerja baru internal' }}
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-500 font-medium">Diajukan Oleh:</span>
                <span class="text-xs font-bold text-slate-800 bg-white px-3 py-1.5 rounded-lg border border-slate-200 shadow-2xs">
                    {{ $candidate->user_request ?: 'Admin Operasional' }}
                </span>
            </div>
        </div>

        @if($candidate->status_replace === 'Replace')
        <div class="mt-4 pt-3 border-t border-amber-200/80 grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
            <div class="bg-white/80 p-3 rounded-xl border border-amber-100">
                <span class="text-slate-400 font-medium block text-[11px] mb-0.5">Menggantikan:</span>
                <span class="font-bold text-slate-800 text-sm">{{ $candidate->menggantikan ?: '-' }}</span>
            </div>
            <div class="bg-white/80 p-3 rounded-xl border border-amber-100">
                <span class="text-slate-400 font-medium block text-[11px] mb-0.5">Tgl. Resign:</span>
                <span class="font-bold text-slate-800 text-sm">{{ $candidate->tgl_resign ? \Carbon\Carbon::parse($candidate->tgl_resign)->translatedFormat('d M Y') : '-' }}</span>
            </div>
            <div class="bg-white/80 p-3 rounded-xl border border-amber-100">
                <span class="text-slate-400 font-medium block text-[11px] mb-0.5">Alasan Resign:</span>
                <span class="font-semibold text-slate-700">{{ $candidate->alasan_resign ?: '-' }}</span>
            </div>
        </div>
        @endif
    </div>

    <!-- 6 Tabs Navigation Bar (Modern Attendance Tabs) -->
    <div class="bg-slate-100/80 border border-slate-200 rounded-2xl p-1.5 shadow-inner flex items-center gap-1.5 overflow-x-auto scrollbar-thin">
        <button @click="activeTab = 'interview'" 
                :class="activeTab === 'interview' ? 'bg-primary-600 text-white shadow-md shadow-primary-500/25 font-bold' : 'text-slate-600 hover:text-primary-600 hover:bg-white font-semibold'" 
                class="px-4 py-2.5 rounded-xl text-xs md:text-sm transition-all duration-150 flex items-center gap-2 whitespace-nowrap">
            <i class="fa-solid fa-clipboard-check text-xs"></i>
            <span>Hasil Interview</span>
        </button>

        <button @click="activeTab = 'refcek'" 
                :class="activeTab === 'refcek' ? 'bg-primary-600 text-white shadow-md shadow-primary-500/25 font-bold' : 'text-slate-600 hover:text-primary-600 hover:bg-white font-semibold'" 
                class="px-4 py-2.5 rounded-xl text-xs md:text-sm transition-all duration-150 flex items-center gap-2 whitespace-nowrap">
            <i class="fa-solid fa-phone-volume text-xs"></i>
            <span>Referensi Cek</span>
        </button>

        <button @click="activeTab = 'kompt'" 
                :class="activeTab === 'kompt' ? 'bg-primary-600 text-white shadow-md shadow-primary-500/25 font-bold' : 'text-slate-600 hover:text-primary-600 hover:bg-white font-semibold'" 
                class="px-4 py-2.5 rounded-xl text-xs md:text-sm transition-all duration-150 flex items-center gap-2 whitespace-nowrap">
            <i class="fa-solid fa-laptop-code text-xs"></i>
            <span>Tes Komputer</span>
        </button>

        <button @click="activeTab = 'kepribadian'" 
                :class="activeTab === 'kepribadian' ? 'bg-primary-600 text-white shadow-md shadow-primary-500/25 font-bold' : 'text-slate-600 hover:text-primary-600 hover:bg-white font-semibold'" 
                class="px-4 py-2.5 rounded-xl text-xs md:text-sm transition-all duration-150 flex items-center gap-2 whitespace-nowrap">
            <i class="fa-solid fa-brain text-xs"></i>
            <span>Tes Kepribadian</span>
        </button>

        <button @click="activeTab = 'matematika'" 
                :class="activeTab === 'matematika' ? 'bg-primary-600 text-white shadow-md shadow-primary-500/25 font-bold' : 'text-slate-600 hover:text-primary-600 hover:bg-white font-semibold'" 
                class="px-4 py-2.5 rounded-xl text-xs md:text-sm transition-all duration-150 flex items-center gap-2 whitespace-nowrap">
            <i class="fa-solid fa-calculator text-xs"></i>
            <span>Tes Matematika</span>
        </button>

        <button @click="activeTab = 'inhouse'" 
                :class="activeTab === 'inhouse' ? 'bg-primary-600 text-white shadow-md shadow-primary-500/25 font-bold' : 'text-slate-600 hover:text-primary-600 hover:bg-white font-semibold'" 
                class="px-4 py-2.5 rounded-xl text-xs md:text-sm transition-all duration-150 flex items-center gap-2 whitespace-nowrap">
            <i class="fa-solid fa-signature text-xs"></i>
            <span>Approval & TTD Inhouse</span>
        </button>
    </div>

    <!-- TAB CONTENTS CONTAINER -->
    <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm">

        <!-- ============================================================= -->
        <!-- TAB 1: HASIL INTERVIEW (Matching Image 1) -->
        <!-- ============================================================= -->
        <div x-show="activeTab === 'interview'" class="space-y-6">
            <form action="{{ route('interview.assess', $candidate->id) }}" method="POST" id="interviewForm" class="space-y-6">
                @csrf
                <input type="hidden" name="signature_data" id="signatureDataInput">

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
                        <label class="block text-xs font-bold text-slate-700">Tanda Tangan</label>
                        
                        <div class="border border-slate-200 rounded-2xl bg-white p-4 shadow-sm text-center">
                            <!-- Canvas Drawing Pad -->
                            <div class="relative">
                                <canvas id="signatureCanvas" 
                                        width="340" 
                                        height="210" 
                                        class="w-full h-52 bg-slate-50/50 rounded-xl border border-dashed border-slate-300 cursor-crosshair touch-none shadow-inner"></canvas>
                                
                                <div class="absolute bottom-2 left-3 pointer-events-none text-[10px] text-slate-400">
                                    <span>Tanda Tangan Pewawancara</span>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between pt-2.5 text-xs">
                                <button type="button" 
                                        onclick="clearSignatureCanvas()" 
                                        class="text-slate-500 hover:text-rose-600 font-semibold text-[11px] flex items-center gap-1 transition-colors">
                                    <i class="fa-solid fa-rotate-left"></i>
                                    <span>Hapus / Ulangi</span>
                                </button>
                                <span class="text-[10px] text-slate-400 italic">Gambar TTD di area kotak</span>
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
                <input type="hidden" name="work_experience_id" value="{{ $firstExp?->id ?? 1 }}">

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    
                    <!-- Left Column (Perusahaan, Tanggal, SPV, Performa, Disiplin, Tanggung Jawab, Problem, Keunggulan, Kelemahan) -->
                    <div class="lg:col-span-7 space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Perusahaan</label>
                            <select name="company_name" class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:ring-4 focus:ring-primary-100 focus:border-primary-600 outline-none">
                                @if($candidate->workExperiences->count() > 0)
                                    @foreach($candidate->workExperiences as $w)
                                        <option value="{{ $w->company_name }}">{{ $w->company_name }}</option>
                                    @endforeach
                                @else
                                    <option value="bravo supermarket">bravo supermarket</option>
                                @endif
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggal Cek Referensi</label>
                            <input type="date" name="checked_date" value="{{ date('Y-m-d') }}" class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:ring-4 focus:ring-primary-100 focus:border-primary-600 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama SPV</label>
                            <input type="text" name="supervisor_name" value="{{ $firstExp?->supervisor_name ?? 'Bpk. Bambang' }}" class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:ring-4 focus:ring-primary-100 focus:border-primary-600 outline-none" placeholder="Bpk. Bambang">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Performa</label>
                            <textarea name="performance" rows="2" class="w-full bg-white border border-slate-200 rounded-xl p-3 text-xs text-slate-800 focus:ring-4 focus:ring-primary-100 focus:border-primary-600 outline-none">{{ $firstExp?->performance_notes ?? 'Target tercapai dengan sangat baik' }}</textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">disiplin</label>
                            <textarea name="discipline" rows="2" class="w-full bg-white border border-slate-200 rounded-xl p-3 text-xs text-slate-800 focus:ring-4 focus:ring-primary-100 focus:border-primary-600 outline-none">{{ $firstExp?->discipline_notes ?? 'Tepat waktu dan patuh SOP' }}</textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggung Jawab</label>
                            <textarea name="responsibility" rows="2" class="w-full bg-white border border-slate-200 rounded-xl p-3 text-xs text-slate-800 focus:ring-4 focus:ring-primary-100 focus:border-primary-600 outline-none">{{ $firstExp?->responsibility_notes ?? 'Bertanggung jawab penuh atas kasir' }}</textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Problem / Masalah</label>
                            <textarea name="problems" rows="2" class="w-full bg-white border border-slate-200 rounded-xl p-3 text-xs text-slate-800 focus:ring-4 focus:ring-primary-100 focus:border-primary-600 outline-none">-</textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Keunggulan</label>
                            <textarea name="strengths" rows="2" class="w-full bg-white border border-slate-200 rounded-xl p-3 text-xs text-slate-800 focus:ring-4 focus:ring-primary-100 focus:border-primary-600 outline-none">{{ $firstExp?->strengths ?? 'Cepat menghitung dan ramah pada konsumen' }}</textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Kelemahan</label>
                            <textarea name="weaknesses" rows="2" class="w-full bg-white border border-slate-200 rounded-xl p-3 text-xs text-slate-800 focus:ring-4 focus:ring-primary-100 focus:border-primary-600 outline-none">{{ $firstExp?->weaknesses ?? 'Kadang kurang sabar saat antrean sangat panjang' }}</textarea>
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="px-6 py-2.5 rounded-xl text-xs font-bold bg-slate-800 hover:bg-slate-900 text-white shadow-sm transition-all">
                                Submit
                            </button>
                        </div>
                    </div>

                    <!-- Right Column (Telp Perusahaan, Tgl Masuk, Tgl Keluar, Alasan Keluar, Ultra Attractive Upload Box) -->
                    <div class="lg:col-span-5 space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Telp Perusahaan</label>
                            <input type="text" name="company_phone" value="{{ $firstExp?->company_phone ?? '081234567890' }}" class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:ring-4 focus:ring-primary-100 focus:border-primary-600 outline-none">
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
                            <div class="p-3 bg-white rounded-2xl border border-slate-200 shadow-2xs">
                                @if($firstExp && ($firstExp->proof_attachment_path || $firstExp->proof_url))
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="flex items-center gap-2.5 min-w-0">
                                            <div class="w-12 h-12 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden flex-shrink-0 cursor-pointer shadow-xs group" onclick="openCandidateMedia('image', '{{ $firstExp->proof_url }}', 'Bukti Referensi Cek: {{ addslashes($firstExp->company_name) }}')" title="Klik untuk preview lampiran">
                                                <img src="{{ $firstExp->proof_url }}" alt="Bukti Refcek" class="w-full h-full object-cover transition-transform group-hover:scale-105" onerror="this.onerror=null; this.src='{{ $firstExp->proof_legacy_url }}';">
                                            </div>
                                            <div class="min-w-0">
                                                <div class="text-xs font-bold text-slate-800 truncate">{{ basename($firstExp->proof_attachment_path) }}</div>
                                                <span class="text-[10px] text-emerald-600 font-semibold block">Bukti Verifikasi Terlampir (Server / Fallback V3)</span>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-1.5 flex-shrink-0">
                                            <button type="button" onclick="openCandidateMedia('image', '{{ $firstExp->proof_url }}', 'Bukti Referensi Cek: {{ addslashes($firstExp->company_name) }}')" class="px-3 py-1.5 bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 rounded-xl text-xs font-bold transition-colors inline-flex items-center gap-1">
                                                <i class="fa-solid fa-eye text-xs"></i> Preview
                                            </button>
                                            <a href="{{ $firstExp->proof_url }}" target="_blank" class="px-3 py-1.5 bg-white border border-slate-300 rounded-xl text-xs font-bold text-primary hover:bg-slate-100 transition-colors inline-flex items-center gap-1 shadow-2xs">
                                                <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i> Buka
                                            </a>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-xs text-slate-400 italic text-center py-2">
                                        <i class="fa-regular fa-image text-slate-300 mr-1"></i> Belum ada lampiran screenshot verifikasi untuk perusahaan ini
                                    </div>
                                @endif
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
        <!-- TAB 3: TES KOMPUTER (Matching Image 3) -->
        <!-- ============================================================= -->
        <div x-show="activeTab === 'kompt'" class="space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-100 text-slate-800 text-xs font-semibold">
                    <i class="fa-solid fa-stopwatch text-slate-500"></i>
                    <span>Waktu Pengerjaan : <strong>00:03:02</strong></span>
                </div>
                <span class="text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-md">
                    Skor: Cukup (75%)
                </span>
            </div>

            <form action="{{ route('interview.kompt', $candidate->id) }}" method="POST" class="space-y-5">
                @csrf

                @php
                    $compSkills = [
                        'vlookup' => 'VLOOKUP',
                        'hlookup' => 'HLOOKUP',
                        'pivot' => 'PIVOT TABLE',
                        'fungsiif' => 'FUNGSI IF',
                        'average' => 'AVERAGE',
                        'hitung' => 'PERKALIAN & PEMBAGIAN',
                        'teliti' => 'KETELITIAN',
                        'cepat' => 'KECEPATAN',
                        'hasilkerja' => 'HASIL KERJA',
                    ];
                    $savedComp = json_decode($candidate->testResults->firstWhere('test_type', 'computer')?->test_details ?? '{}', true) ?: [];
                @endphp

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
                                        <input type="radio" name="{{ $k }}" value="Baik" {{ $val === 'Baik' ? 'checked' : '' }} class="w-4 h-4 text-primary-600 focus:ring-primary-500 border-slate-300">
                                        <span class="text-slate-700 group-hover:text-primary-600">Baik</span>
                                    </label>
                                </td>
                                <td class="py-3 px-3">
                                    <label class="inline-flex items-center gap-2 cursor-pointer group">
                                        <input type="radio" name="{{ $k }}" value="Cukup" {{ ($val === 'Cukup' || empty($val)) ? 'checked' : '' }} class="w-4 h-4 text-primary-600 focus:ring-primary-500 border-slate-300">
                                        <span class="text-slate-900 font-bold group-hover:text-primary-600">Cukup</span>
                                    </label>
                                </td>
                                <td class="py-3 px-3">
                                    <label class="inline-flex items-center gap-2 cursor-pointer group">
                                        <input type="radio" name="{{ $k }}" value="Kurang" {{ $val === 'Kurang' ? 'checked' : '' }} class="w-4 h-4 text-primary-600 focus:ring-primary-500 border-slate-300">
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

            <!-- 4-Column Table of Questions -->
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
        <!-- TAB 6: APPROVAL INHOUSE & TANDA TANGAN DIGITAL (Replikasi hasilinhouse.php) -->
        <!-- ============================================================= -->
        <div x-show="activeTab === 'inhouse'" class="space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                
                <!-- Left Column: Form Keputusan & Canvas TTD Digital -->
                <div class="lg:col-span-6 bg-slate-50/60 p-5 rounded-2xl border border-slate-200 space-y-4">
                    <div class="flex items-center gap-2 pb-3 border-b border-slate-200">
                        <div class="w-8 h-8 rounded-lg bg-primary-50 text-primary flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-pen-nib"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-800">Form Keputusan & Tanda Tangan</h3>
                            <p class="text-[11px] text-slate-500">Pilih hasil keputusan dan goreskan tanda tangan digital Anda.</p>
                        </div>
                    </div>

                    <!-- Keputusan Select -->
                    <div>
                        <label for="approvalChoice" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Hasil Keputusan <span class="text-rose-500">*</span>
                        </label>
                        <select id="approvalChoice" class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-800 focus:ring-4 focus:ring-primary-100 focus:border-primary-600 outline-none">
                            <option value="Approve" selected>Approve (Disetujui)</option>
                            <option value="Tolak">Tolak (Ditolak)</option>
                        </select>
                    </div>

                    <!-- Digital Signature Canvas Pad -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-bold text-slate-700">
                                Goreskan Tanda Tangan Digital <span class="text-rose-500">*</span>
                            </label>
                            <button type="button" onclick="clearInhouseCanvas()" class="text-[11px] font-bold text-rose-600 hover:text-rose-700 flex items-center gap-1">
                                <i class="fa-solid fa-rotate-left"></i>
                                <span>Hapus Tanda Tangan</span>
                            </button>
                        </div>
                        <div class="border-2 border-dashed border-slate-300 rounded-xl bg-white overflow-hidden shadow-xs hover:border-primary-400 transition-colors">
                            <canvas id="inhouseSigCanvas" width="480" height="220" class="w-full bg-white touch-none cursor-crosshair block"></canvas>
                        </div>
                        <span class="text-[10px] text-slate-400 mt-1 block italic">* Gunakan mouse atau sentuhan layar pada kotak canvas di atas.</span>
                    </div>

                    <!-- Catatan Approver -->
                    <div>
                        <label for="catatanApprover" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Catatan Approver <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="catatanApprover" rows="4" 
                                  class="w-full bg-white border border-slate-300 rounded-xl p-3 text-xs text-slate-800 focus:ring-4 focus:ring-primary-100 focus:border-primary-600 outline-none placeholder:text-slate-400"
                                  placeholder="Tuliskan catatan pertimbangan approval, penempatan, atau alasan penerimaan/penolakan..."></textarea>
                    </div>

                    <!-- Submit Buttons (Matching legacy Submit HRD & Submit Data Head) -->
                    <div class="pt-3 border-t border-slate-200 flex flex-wrap items-center gap-2.5">
                        <button type="button" onclick="submitInhouseDecision('head')" 
                                class="flex-1 btn-att-secondary text-xs font-bold py-2.5 flex items-center justify-center gap-2 border-primary-300 text-primary hover:bg-primary-50">
                            <i class="fa-solid fa-paper-plane"></i>
                            <span>Submit Data Head</span>
                        </button>

                        <button type="button" onclick="submitInhouseDecision('hrd')" 
                                class="flex-1 btn-att-primary text-xs font-bold py-2.5 flex items-center justify-center gap-2">
                            <i class="fa-solid fa-check-double"></i>
                            <span>Submit HRD</span>
                        </button>
                    </div>
                </div>

                <!-- Right Column: List Head Approve History Table -->
                <div class="lg:col-span-6 bg-white p-5 rounded-2xl border border-slate-200 space-y-4 shadow-xs">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm">
                                <i class="fa-solid fa-list-check"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-800">List Head Approve</h3>
                                <p class="text-[11px] text-slate-500">Riwayat persetujuan berjenjang kandidat inhouse.</p>
                            </div>
                        </div>
                        <span class="text-xs font-bold px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 border border-slate-200">
                            {{ $candidate->inhouseApprovals->count() }} Riwayat
                        </span>
                    </div>

                    @if($candidate->inhouseApprovals->isEmpty())
                        <div class="py-12 text-center text-slate-400">
                            <i class="fa-solid fa-file-signature text-3xl mb-2 text-slate-300"></i>
                            <p class="font-semibold text-slate-600 text-sm">Belum ada approval dari Head</p>
                            <p class="text-xs text-slate-400 mt-1">Silakan berikan tanda tangan dan catatan pada form di sebelah kiri.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase tracking-wider text-[11px]">
                                        <th class="py-2.5 px-3 font-bold">Approver</th>
                                        <th class="py-2.5 px-3 font-bold">Catatan</th>
                                        <th class="py-2.5 px-2.5 text-center font-bold">Keputusan</th>
                                        <th class="py-2.5 px-3 text-center font-bold">Tanda Tangan</th>
                                        <th class="py-2.5 px-3 font-bold">Waktu Submit</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($candidate->inhouseApprovals as $app)
                                    <tr class="hover:bg-slate-50/60 transition-colors">
                                        <td class="py-3 px-3">
                                            <div class="font-bold text-slate-800">{{ $app->nama_approver }}</div>
                                            <div class="text-[11px] text-slate-400">{{ $app->jabatan_approver }}</div>
                                        </td>
                                        <td class="py-3 px-3 text-slate-600 max-w-xs leading-relaxed">
                                            {{ $app->catatan_approver ?: '-' }}
                                        </td>
                                        <td class="py-3 px-2.5 text-center">
                                            <span class="badge-pill {{ $app->status === 'Approve' || $app->status === 'Yes' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200' }}">
                                                {{ $app->status }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-3 text-center">
                                            @if($app->ttd_approver)
                                                <div class="w-24 h-12 border border-slate-200 rounded-lg bg-slate-50 p-1 flex items-center justify-center mx-auto overflow-hidden">
                                                    @if(str_starts_with($app->ttd_approver, 'data:') || str_starts_with($app->ttd_approver, 'http') || str_starts_with($app->ttd_approver, '/'))
                                                        <img src="{{ $app->ttd_approver }}" alt="TTD" class="max-h-full max-w-full object-contain">
                                                    @else
                                                        <img src="/ttdfileas/{{ $app->ttd_approver }}" alt="TTD" class="max-h-full max-w-full object-contain" onerror="this.src='https://ui-avatars.com/api/?name=TTD&background=f1f5f9&color=64748b'">
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-slate-400 italic text-[11px]">Tidak ada</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-3 text-slate-500 font-mono text-[11px] whitespace-nowrap">
                                            {{ $app->created_at ? $app->created_at->format('d M Y H:i') : ($app->time_approver ? \Carbon\Carbon::parse($app->time_approver)->format('d M Y H:i') : '-') }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

            </div>
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

    <!-- MODAL GANTI AREA -->
    <div x-show="gantiAreaModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="gantiAreaModalOpen = false" 
             class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl border border-slate-200 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h4 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-location-dot text-amber-600"></i>
                    Ganti Area Penempatan
                </h4>
                <button @click="gantiAreaModalOpen = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <form action="{{ route('interview.ganti-area', $candidate->id) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Pilih Area Baru</label>
                    <select name="area" class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:ring-4 focus:ring-amber-100 focus:border-amber-500 outline-none" required>
                        @php
                            $allAreas = $areas ?? [
                                'JAKARTA', 'SURABAYA', 'BANDUNG', 'SEMARANG', 'MEDAN', 
                                'MAKASSAR', 'DENPASAR', 'PALEMBANG', 'BALIKPAPAN', 'YOGYAKARTA',
                                'MALANG', 'BOGOR', 'BEKASI', 'TANGERANG', 'DEPOK'
                            ];
                        @endphp
                        @foreach($allAreas as $ar)
                            <option value="{{ $ar }}" {{ ($candidate->area == $ar) ? 'selected' : '' }}>{{ $ar }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button @click="gantiAreaModalOpen = false" type="button" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold bg-amber-600 hover:bg-amber-700 text-white shadow-sm">
                        Perbarui Area
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
    let canvas, ctx, isDrawing = false;

    document.addEventListener('DOMContentLoaded', function() {
        canvas = document.getElementById('signatureCanvas');
        if (canvas) {
            ctx = canvas.getContext('2d');
            ctx.lineWidth = 2.5;
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';
            ctx.strokeStyle = '#0f172a';

            // Draw a pre-existing sample curve if no signature is recorded yet
            drawSampleSignature();

            function getPos(e) {
                const rect = canvas.getBoundingClientRect();
                const scaleX = canvas.width / rect.width;
                const scaleY = canvas.height / rect.height;
                const clientX = e.touches ? e.touches[0].clientX : e.clientX;
                const clientY = e.touches ? e.touches[0].clientY : e.clientY;
                return {
                    x: (clientX - rect.left) * scaleX,
                    y: (clientY - rect.top) * scaleY
                };
            }

            function startDraw(e) {
                isDrawing = true;
                const pos = getPos(e);
                ctx.beginPath();
                ctx.moveTo(pos.x, pos.y);
                if (e.touches) e.preventDefault();
            }

            function draw(e) {
                if (!isDrawing) return;
                const pos = getPos(e);
                ctx.lineTo(pos.x, pos.y);
                ctx.stroke();
                if (e.touches) e.preventDefault();
            }

            function stopDraw() {
                isDrawing = false;
            }

            canvas.addEventListener('mousedown', startDraw);
            canvas.addEventListener('mousemove', draw);
            window.addEventListener('mouseup', stopDraw);

            canvas.addEventListener('touchstart', startDraw, { passive: false });
            canvas.addEventListener('touchmove', draw, { passive: false });
            window.addEventListener('touchend', stopDraw);
        }
    });

    function drawSampleSignature() {
        if (!ctx) return;
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        // Draw the sample cursive signature loop from Image 1
        ctx.beginPath();
        ctx.moveTo(80, 160);
        ctx.bezierCurveTo(90, 80, 140, 60, 150, 110);
        ctx.bezierCurveTo(160, 150, 70, 140, 110, 170);
        ctx.stroke();
    }

    function clearSignatureCanvas() {
        if (!ctx) return;
        ctx.clearRect(0, 0, canvas.width, canvas.height);
    }

    function submitInterviewForm() {
        const form = document.getElementById('interviewForm');
        if (canvas) {
            document.getElementById('signatureDataInput').value = canvas.toDataURL('image/png');
        }
        form.submit();
    }
</script>
@endpush
<script>
// Canvas Digital Signature Handler for Inhouse
let inhouseCanvas = null;
let inhouseCtx = null;
let isDrawing = false;
let lastX = 0;
let lastY = 0;

document.addEventListener('DOMContentLoaded', function() {
    inhouseCanvas = document.getElementById('inhouseSigCanvas');
    if (inhouseCanvas) {
        inhouseCtx = inhouseCanvas.getContext('2d');
        inhouseCtx.strokeStyle = '#0F52BA';
        inhouseCtx.lineWidth = 2.5;
        inhouseCtx.lineCap = 'round';
        inhouseCtx.lineJoin = 'round';

        function getPos(e) {
            const rect = inhouseCanvas.getBoundingClientRect();
            const scaleX = inhouseCanvas.width / rect.width;
            const scaleY = inhouseCanvas.height / rect.height;
            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const clientY = e.touches ? e.touches[0].clientY : e.clientY;
            return {
                x: (clientX - rect.left) * scaleX,
                y: (clientY - rect.top) * scaleY
            };
        }

        function start(e) {
            isDrawing = true;
            const pos = getPos(e);
            lastX = pos.x;
            lastY = pos.y;
            inhouseCtx.beginPath();
            inhouseCtx.moveTo(lastX, lastY);
            if (e.touches) e.preventDefault();
        }

        function move(e) {
            if (!isDrawing) return;
            const pos = getPos(e);
            inhouseCtx.lineTo(pos.x, pos.y);
            inhouseCtx.stroke();
            lastX = pos.x;
            lastY = pos.y;
            if (e.touches) e.preventDefault();
        }

        function stop() {
            if (isDrawing) {
                isDrawing = false;
                inhouseCtx.closePath();
            }
        }

        inhouseCanvas.addEventListener('mousedown', start);
        inhouseCanvas.addEventListener('mousemove', move);
        window.addEventListener('mouseup', stop);

        inhouseCanvas.addEventListener('touchstart', start, { passive: false });
        inhouseCanvas.addEventListener('touchmove', move, { passive: false });
        window.addEventListener('touchend', stop);
    }
});

function clearInhouseCanvas() {
    if (inhouseCanvas && inhouseCtx) {
        inhouseCtx.clearRect(0, 0, inhouseCanvas.width, inhouseCanvas.height);
    }
}

function submitInhouseDecision(submitType) {
    const approval = document.getElementById('approvalChoice').value;
    const catatan = document.getElementById('catatanApprover').value.trim();

    if (!catatan) {
        Swal.fire({
            icon: 'warning',
            title: 'Catatan Belum Diisi',
            text: 'Silakan isi catatan evaluasi approver terlebih dahulu.',
            confirmButtonColor: '#0F52BA'
        });
        return;
    }

    let signatureData = null;
    if (inhouseCanvas) {
        signatureData = inhouseCanvas.toDataURL('image/png');
    }

    Swal.fire({
        title: 'Konfirmasi ' + (submitType === 'hrd' ? 'Submit HRD' : 'Submit Head'),
        text: 'Simpan keputusan ' + approval + ' untuk kandidat inhouse ini?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#0F52BA',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Simpan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.showLoading();
            fetch("{{ route('interviewinhouse.approval', $candidate->id) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    approval: approval,
                    catatan: catatan,
                    image: signatureData,
                    submit_type: submitType
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        confirmButtonColor: '#0F52BA'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.message || 'Terjadi kesalahan saat menyimpan approval.',
                        confirmButtonColor: '#0F52BA'
                    });
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Sistem',
                    text: 'Tidak dapat terhubung ke server.',
                    confirmButtonColor: '#0F52BA'
                });
            });
        }
    });
}
</script>
@endsection