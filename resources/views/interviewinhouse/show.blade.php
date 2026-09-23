@extends('layouts.app')

@section('title', 'Evaluasi Inhouse - ' . $candidate->full_name)
@section('page_title', 'DETAIL EVALUASI & APPROVAL KANDIDAT INHOUSE')
@section('breadcrumb_active', 'Detail Inhouse')

@section('content')
<div class="space-y-6" x-data="{ 
    activeTab: '{{ request('tab', 'approval') }}',
    mediaModalOpen: false,
    mediaModalType: 'image',
    mediaModalUrl: '',
    mediaModalTitle: ''
}">

    <!-- TOP NAVIGATION & STATUS BAR -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <a href="{{ route('interviewinhouse.index') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 shadow-sm transition">
            <i class="fa-solid fa-arrow-left text-xs"></i>
            <span>Kembali ke Kandidat Inhouse</span>
        </a>

        <div class="flex items-center gap-2 flex-wrap">
            <span class="text-xs text-slate-500 font-medium">Role Approver Login:</span>
            @if($isHrd)
                <span class="px-2.5 py-1 rounded-lg text-xs font-black bg-purple-50 text-purple-700 border border-purple-200">
                    <i class="fa-solid fa-user-shield mr-1"></i> HRD Pusat
                </span>
            @else
                <span class="px-2.5 py-1 rounded-lg text-xs font-black bg-blue-50 text-blue-700 border border-blue-200">
                    <i class="fa-solid fa-user-tie mr-1"></i> Head Approver
                </span>
            @endif

            <span class="px-2.5 py-1 rounded-lg text-xs font-bold border {{ $statusBadge['class'] }}">
                {{ $statusBadge['text'] }}
            </span>
        </div>
    </div>

    <!-- ACTION BUTTONS: DOKUMENT INTERVIEW & BERKAS LAMARAN (Persis Gambar 3) -->
    <div class="space-y-2">
        <div class="flex flex-wrap items-center gap-2.5">
            <!-- 1. Tombol Dokument Interview & Test Online (Biru Tua) -->
            <a href="{{ route('interview.pdf', $candidate->id) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-slate-800 hover:bg-slate-900 shadow-sm transition">
                <i class="fa-solid fa-file-pdf text-rose-400"></i>
                <span>Dokument Interview & Test Online</span>
            </a>

            <!-- 2. Tombol Berkas Lamaran -->
            @if(!empty($candidate->berkas_lamaran))
                <a href="{{ route('interviewinhouse.berkas', $candidate->id) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-slate-700 hover:bg-slate-800 shadow-sm transition">
                    <i class="fa-solid fa-file-lines text-sky-300"></i>
                    <span>Berkas Lamaran</span>
                </a>
            @else
                <button type="button" disabled class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold bg-slate-200 text-slate-400 cursor-not-allowed border border-slate-300 shadow-none">
                    <i class="fa-solid fa-file-lines text-slate-400"></i>
                    <span>Berkas Lamaran</span>
                </button>
            @endif
        </div>

        <p class="text-[11px] text-slate-500 italic flex items-center gap-1.5 pt-0.5">
            <i class="fa-solid fa-circle-info text-slate-400 text-xs"></i>
            <span>Jika Tombol Berkas Lamaran Berwarna Abu-abu & Tidak Bisa di Klik, Artinya User Belum Melampirkan Berkas Lamaran Kandidat.</span>
        </p>
    </div>

    <!-- PROFIL KANDIDAT CARD LENGKAP (11 DATA POINTS + FOTO 3X4 + CV - SAMAKAN SEPERTI DETAIL INTERVIEW) -->
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

                    <!-- 3. Alamat KTP -->
                    <div class="flex items-start gap-2 md:col-span-2">
                        <span class="w-32 text-slate-400 font-medium flex-shrink-0">Alamat KTP:</span>
                        <span class="text-slate-700 leading-relaxed">{{ $candidate->address_ktp ?? '-' }}</span>
                    </div>

                    <!-- 4. Usia -->
                    <div class="flex items-start gap-2">
                        <span class="w-32 text-slate-400 font-medium flex-shrink-0">Usia:</span>
                        <span class="text-slate-700 font-semibold">{{ $candidate->age }} Tahun</span>
                    </div>

                    <!-- 5. Pendidikan Terakhir -->
                    <div class="flex items-start gap-2">
                        <span class="w-32 text-slate-400 font-medium flex-shrink-0">Pendidikan Terakhir:</span>
                        <span class="text-slate-700">{{ $candidate->education ?? '-' }}</span>
                    </div>

                    <!-- 6. Nomor Handphone -->
                    <div class="flex items-start gap-2">
                        <span class="w-32 text-slate-400 font-medium flex-shrink-0">Mobile / WhatsApp:</span>
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
                        <span class="px-2.5 py-0.5 rounded-full text-[10.5px] font-extrabold bg-blue-50 text-blue-700 border border-blue-200">
                            {{ $candidate->status_approval ?? $candidate->status_kandidat ?? 'Review Inhouse' }}
                        </span>
                    </div>

                    <!-- 10. Pengaju (User Request) -->
                    <div class="flex items-start gap-2">
                        <span class="w-32 text-slate-400 font-medium flex-shrink-0">User Request:</span>
                        <span class="text-slate-800 font-bold bg-slate-100 px-2 py-0.5 rounded border border-slate-200">
                            {{ $candidate->user_request ?? ($candidate->recruiter?->name ?? $candidate->user_display_name) }}
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
                        <div class="space-y-1.5 flex-1">
                            <span class="text-slate-600 italic block">{{ $candidate->notes ?? 'Tidak ada catatan tambahan.' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Foto Profil 3x4 & Lampiran CV -->
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

    <!-- 7 TABS NAVIGATION BAR (TETAP DISAMAKAN DENGAN DETAIL INTERVIEW) -->
    <div class="bg-slate-100/80 border border-slate-200 rounded-2xl p-1.5 shadow-inner flex items-center gap-1.5 overflow-x-auto scrollbar-thin">
        <button type="button" 
                @click="activeTab = 'interview'" 
                :class="activeTab === 'interview' ? 'bg-primary-600 text-white shadow-md shadow-primary-500/25 font-bold' : 'text-slate-600 hover:text-primary-600 hover:bg-white font-semibold'" 
                class="px-4 py-2.5 rounded-xl text-xs md:text-sm transition-all duration-150 flex items-center gap-2 whitespace-nowrap">
            <i class="fa-solid fa-clipboard-check text-xs"></i>
            <span>1. Hasil Interview</span>
        </button>

        <button type="button" 
                @click="activeTab = 'refcek'" 
                :class="activeTab === 'refcek' ? 'bg-primary-600 text-white shadow-md shadow-primary-500/25 font-bold' : 'text-slate-600 hover:text-primary-600 hover:bg-white font-semibold'" 
                class="px-4 py-2.5 rounded-xl text-xs md:text-sm transition-all duration-150 flex items-center gap-2 whitespace-nowrap">
            <i class="fa-solid fa-phone-volume text-xs"></i>
            <span>2. Referensi Cek</span>
        </button>

        <button type="button" 
                @click="activeTab = 'kompt'" 
                :class="activeTab === 'kompt' ? 'bg-primary-600 text-white shadow-md shadow-primary-500/25 font-bold' : 'text-slate-600 hover:text-primary-600 hover:bg-white font-semibold'" 
                class="px-4 py-2.5 rounded-xl text-xs md:text-sm transition-all duration-150 flex items-center gap-2 whitespace-nowrap">
            <i class="fa-solid fa-laptop-code text-xs"></i>
            <span>3. Tes Komputer</span>
        </button>

        <button type="button" 
                @click="activeTab = 'kepribadian'" 
                :class="activeTab === 'kepribadian' ? 'bg-primary-600 text-white shadow-md shadow-primary-500/25 font-bold' : 'text-slate-600 hover:text-primary-600 hover:bg-white font-semibold'" 
                class="px-4 py-2.5 rounded-xl text-xs md:text-sm transition-all duration-150 flex items-center gap-2 whitespace-nowrap">
            <i class="fa-solid fa-brain text-xs"></i>
            <span>4. Tes Kepribadian</span>
        </button>

        <button type="button" 
                @click="activeTab = 'matematika'" 
                :class="activeTab === 'matematika' ? 'bg-primary-600 text-white shadow-md shadow-primary-500/25 font-bold' : 'text-slate-600 hover:text-primary-600 hover:bg-white font-semibold'" 
                class="px-4 py-2.5 rounded-xl text-xs md:text-sm transition-all duration-150 flex items-center gap-2 whitespace-nowrap">
            <i class="fa-solid fa-calculator text-xs"></i>
            <span>5. Tes Matematika</span>
        </button>

        <button type="button" 
                @click="activeTab = 'ai'" 
                :class="activeTab === 'ai' ? 'bg-primary-600 text-white shadow-md shadow-primary-500/25 font-bold' : 'text-slate-600 hover:text-primary-600 hover:bg-white font-semibold'" 
                class="px-4 py-2.5 rounded-xl text-xs md:text-sm transition-all duration-150 flex items-center gap-2 whitespace-nowrap">
            <i class="fa-solid fa-wand-magic-sparkles text-amber-500 text-xs"></i>
            <span>6. Analisa AI (CV Analyzer)</span>
        </button>

        <!-- TAB 7: APPROVAL INHOUSE (DEFAULT APPROVER TAB) -->
        <button type="button" 
                @click="activeTab = 'approval'" 
                :class="activeTab === 'approval' ? 'bg-primary-600 text-white shadow-md shadow-primary-500/25 font-bold' : 'text-slate-600 hover:text-primary-600 hover:bg-white font-semibold'" 
                class="px-4 py-2.5 rounded-xl text-xs md:text-sm transition-all duration-150 flex items-center gap-2 whitespace-nowrap">
            <i class="fa-solid fa-house-chimney-user text-xs"></i>
            <span>7. Approval Inhouse</span>
        </button>
    </div>

    <!-- TAB CONTENTS CONTAINER -->
    <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm">

        <!-- ============================================================= -->
        <!-- TAB 1: HASIL INTERVIEW -->
        <!-- ============================================================= -->
        <div x-show="activeTab === 'interview'" class="space-y-6">
            @php
                $assess = $candidate->interviewAssessment;
            @endphp
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- Left: 4 Pillars Radio Table (Styled Modern Matrix) -->
                <div class="lg:col-span-8 space-y-5">
                    
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
                                        <label class="inline-flex items-center gap-1.5 cursor-default">
                                            <input type="radio" disabled {{ ($p['val'] == 5 || $p['val'] === 'Sangat Baik') ? 'checked' : '' }} class="w-4 h-4 text-primary-600 border-slate-300">
                                            <span class="{{ ($p['val'] == 5 || $p['val'] === 'Sangat Baik') ? 'text-primary-700 font-bold' : 'text-slate-500' }}">Sangat Baik</span>
                                        </label>
                                    </td>
                                    <td class="py-3.5 px-3 text-center">
                                        <label class="inline-flex items-center gap-1.5 cursor-default">
                                            <input type="radio" disabled {{ ($p['val'] == 4 || $p['val'] === 'Baik') ? 'checked' : '' }} class="w-4 h-4 text-primary-600 border-slate-300">
                                            <span class="{{ ($p['val'] == 4 || $p['val'] === 'Baik') ? 'text-primary-700 font-bold' : 'text-slate-500' }}">Baik</span>
                                        </label>
                                    </td>
                                    <td class="py-3.5 px-3 text-center">
                                        <label class="inline-flex items-center gap-1.5 cursor-default">
                                            <input type="radio" disabled {{ ($p['val'] == 3 || $p['val'] == 2 || $p['val'] === 'Cukup' || empty($p['val'])) ? 'checked' : '' }} class="w-4 h-4 text-primary-600 border-slate-300">
                                            <span class="{{ ($p['val'] == 3 || $p['val'] == 2 || $p['val'] === 'Cukup' || empty($p['val'])) ? 'text-primary-700 font-bold' : 'text-slate-500' }}">Cukup</span>
                                        </label>
                                    </td>
                                    <td class="py-3.5 px-3 text-center">
                                        <label class="inline-flex items-center gap-1.5 cursor-default">
                                            <input type="radio" disabled {{ ($p['val'] == 1 || $p['val'] === 'Kurang') ? 'checked' : '' }} class="w-4 h-4 text-primary-600 border-slate-300">
                                            <span class="{{ ($p['val'] == 1 || $p['val'] === 'Kurang') ? 'text-rose-600 font-bold' : 'text-slate-500' }}">Kurang</span>
                                        </label>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Catatan Interview -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Catatan Lain-lain dari Rekrutor:</label>
                        <div class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3.5 text-xs text-slate-800 font-medium leading-relaxed italic">
                            "{{ $assess?->other_notes ?: 'Tidak ada catatan wawancara khusus.' }}"
                        </div>
                    </div>

                    <!-- Tanggal Interview -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggal Wawancara:</label>
                        <div class="relative max-w-xs">
                            <span class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-800">
                                <i class="fa-regular fa-calendar-days text-primary"></i>
                                <span>{{ $assess?->interview_date ? \Carbon\Carbon::parse($assess->interview_date)->format('d F Y') : '-' }}</span>
                            </span>
                        </div>
                    </div>

                </div>

                <!-- Right: Tanda Tangan Pewawancara (AS / Rekrutor) -->
                <div class="lg:col-span-4 space-y-3">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <div>
                            <label class="block text-xs font-bold text-slate-800">Tanda Tangan Pewawancara</label>
                            <span class="text-[10px] text-slate-500 font-medium">User AS / Rekrutor: <b class="text-slate-700">{{ $candidateAsName }}</b></span>
                        </div>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $initialInterviewerSig ? 'bg-emerald-50 border border-emerald-200 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                            <i class="fa-solid {{ $initialInterviewerSig ? 'fa-circle-check text-emerald-600' : 'fa-pen-nib text-slate-400' }}"></i>
                            <span>{{ $initialInterviewerSig ? 'TTD Terverifikasi' : 'Belum Ada TTD' }}</span>
                        </span>
                    </div>

                    <div class="border-2 border-dashed border-slate-200 rounded-2xl p-4 bg-slate-50 flex items-center justify-center min-h-[180px]">
                        @if(!empty($initialInterviewerSig))
                            <img src="{{ $initialInterviewerSig }}" alt="TTD Pewawancara" class="max-h-32 object-contain mx-auto" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                            <span class="text-xs text-slate-400 italic hidden">Format TTD tidak dapat dimuat</span>
                        @else
                            <div class="text-center text-slate-400 space-y-1">
                                <i class="fa-solid fa-signature text-2xl text-slate-300"></i>
                                <span class="text-xs italic block">Belum dibubuhi tanda tangan pewawancara</span>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>

        <!-- ============================================================= -->
        <!-- TAB 2: REFERENSI CEK -->
        <!-- ============================================================= -->
        <div x-show="activeTab === 'refcek'" class="space-y-6">
            @php
                $firstExp = $candidate->workExperiences->first();
            @endphp

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- Left Column (Perusahaan, Tanggal, SPV, Performa, Disiplin, Tanggung Jawab, Problem, Keunggulan, Kelemahan) -->
                <div class="lg:col-span-7 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Perusahaan Sebelumnya</label>
                        <select name="company_id" id="companySelect" onchange="handleCompanySelect(this.value)" class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 font-semibold focus:ring-4 focus:ring-primary-100 focus:border-primary-600 outline-none shadow-xs">
                            @if($candidate->workExperiences->count() > 0)
                                @foreach($candidate->workExperiences as $w)
                                    <option value="{{ $w->id }}" {{ $loop->first ? 'selected' : '' }}>
                                        {{ $w->company_name }} @if(!empty($w->position)) ({{ $w->position }}) @endif
                                    </option>
                                @endforeach
                            @else
                                <option value="" selected disabled>Belum ada riwayat pengalaman kerja</option>
                            @endif
                        </select>
                        <p class="text-[10px] text-slate-400 mt-1">Pilih dari data riwayat pekerjaan yang diinputkan dibagian Pengalaman Kerja kandidat</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggal Cek Referensi</label>
                        <input type="date" readonly id="refcek_date" value="{{ $firstExp?->check_date ? \Carbon\Carbon::parse($firstExp->check_date)->format('Y-m-d') : date('Y-m-d') }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama SPV</label>
                        <input type="text" readonly id="refcek_spv" value="{{ $firstExp?->supervisor_name ?? '-' }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none font-medium">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Performa</label>
                        <textarea readonly id="refcek_performance" rows="2" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs text-slate-800 focus:outline-none">{{ $firstExp?->performance_notes ?? 'Baik' }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Disiplin</label>
                        <textarea readonly id="refcek_discipline" rows="2" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs text-slate-800 focus:outline-none">{{ $firstExp?->discipline_notes ?? 'Tepat Waktu' }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggung Jawab</label>
                        <textarea readonly id="refcek_responsibility" rows="2" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs text-slate-800 focus:outline-none">{{ $firstExp?->responsibility_notes ?? 'Bertanggung Jawab' }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Problem / Masalah</label>
                        <textarea readonly id="refcek_problem" rows="2" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs text-slate-800 focus:outline-none">-</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Keunggulan</label>
                        <textarea readonly id="refcek_strengths" rows="2" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs text-slate-800 focus:outline-none">{{ $firstExp?->strengths ?? '-' }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Kelemahan</label>
                        <textarea readonly id="refcek_weaknesses" rows="2" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs text-slate-800 focus:outline-none">{{ $firstExp?->weaknesses ?? '-' }}</textarea>
                    </div>
                </div>

                <!-- Right Column (Telp Perusahaan, Tgl Masuk, Tgl Keluar, Alasan Keluar, Proof Box) -->
                <div class="lg:col-span-5 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Telp Perusahaan</label>
                        <input type="text" readonly id="refcek_phone" value="{{ $firstExp?->company_phone ?? '-' }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Tgl. Masuk</label>
                        <input type="text" readonly id="refcek_start_date" value="{{ $firstExp?->start_date ? \Carbon\Carbon::parse($firstExp->start_date)->format('Y-m-d') : '-' }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Tgl. Keluar</label>
                        <input type="text" readonly id="refcek_end_date" value="{{ $firstExp?->end_date ? \Carbon\Carbon::parse($firstExp->end_date)->format('Y-m-d') : '-' }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Alasan Keluar</label>
                        <textarea readonly id="refcek_reason" rows="2" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs text-slate-800 focus:outline-none">{{ $firstExp?->reason_for_leaving ?? '-' }}</textarea>
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
                                        <span class="text-[10px] text-emerald-600 font-semibold block">Bukti Verifikasi Terlampir</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1.5 flex-shrink-0">
                                    <button type="button" onclick="previewCurrentRefcek()" class="px-3 py-1.5 bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 rounded-xl text-xs font-bold transition-colors inline-flex items-center gap-1 cursor-pointer">
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

                </div>

            </div>

            <!-- Tabel Riwayat Semua Pengalaman Kerja -->
            @if($candidate->workExperiences->count() > 0)
                <div class="mt-6 border-t border-slate-100 pt-6 space-y-3">
                    <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Daftar Semua Riwayat Pengalaman Kerja Kandidat:</h4>
                    <div class="border border-slate-200 rounded-2xl overflow-hidden shadow-2xs">
                        <table class="w-full text-xs text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider text-[11px]">
                                    <th class="py-3 px-4">Nama Perusahaan</th>
                                    <th class="py-3 px-3">Jabatan</th>
                                    <th class="py-3 px-3">Periode</th>
                                    <th class="py-3 px-3">No. Telp Perusahaan</th>
                                    <th class="py-3 px-3">Nama SPV</th>
                                    <th class="py-3 px-3 text-center">Bukti Cek</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($candidate->workExperiences as $exp)
                                    <tr class="hover:bg-slate-50/70 transition-colors">
                                        <td class="py-3 px-4 font-bold text-slate-900">{{ $exp->company_name }}</td>
                                        <td class="py-3 px-3 text-slate-700">{{ $exp->position ?? '-' }}</td>
                                        <td class="py-3 px-3 text-slate-500 whitespace-nowrap">{{ $exp->period_label }}</td>
                                        <td class="py-3 px-3 text-slate-600">{{ $exp->company_phone ?? '-' }}</td>
                                        <td class="py-3 px-3 text-slate-700">{{ $exp->supervisor_name ?? '-' }}</td>
                                        <td class="py-3 px-3 text-center">
                                            @if($exp->proof_url)
                                                <button type="button" onclick="openCandidateMedia('image', '{{ $exp->proof_url }}', 'Bukti Referensi Cek: {{ addslashes($exp->company_name) }}')" class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 font-bold text-[11px] border border-emerald-200 hover:bg-emerald-100 transition cursor-pointer">
                                                    <i class="fa-solid fa-image mr-1"></i> Bukti
                                                </button>
                                            @else
                                                <span class="text-slate-400 text-[11px] italic">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <!-- ============================================================= -->
        <!-- TAB 3: TES KOMPUTER -->
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
                <span>Data penilaian awal tes komputer belum tersimpan / belum dinilai.</span>
            </div>
            @endif

            <div class="border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
                <table class="w-full text-xs text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase tracking-wider text-[11px]">
                            <th class="py-3 px-4 font-bold">Aspek Keahlian Komputer</th>
                            <th class="py-3 px-3 text-center font-bold w-32">Baik</th>
                            <th class="py-3 px-3 text-center font-bold w-32">Cukup</th>
                            <th class="py-3 px-3 text-center font-bold w-32">Kurang</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($compSkills as $k => $label)
                        @php
                            $val = $savedComp[$k] ?? 'Cukup';
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3 px-4 font-bold text-slate-800 uppercase tracking-wider text-xs">{{ $label }}</td>
                            <td class="py-3 px-3 text-center">
                                <label class="inline-flex items-center gap-2 cursor-default">
                                    <input type="radio" disabled {{ strtolower($val) === 'baik' ? 'checked' : '' }} class="w-4 h-4 text-primary-600 border-slate-300">
                                    <span class="{{ strtolower($val) === 'baik' ? 'text-primary-700 font-bold' : 'text-slate-500' }}">Baik</span>
                                </label>
                            </td>
                            <td class="py-3 px-3 text-center">
                                <label class="inline-flex items-center gap-2 cursor-default">
                                    <input type="radio" disabled {{ (strtolower($val) === 'cukup' || empty($val)) ? 'checked' : '' }} class="w-4 h-4 text-primary-600 border-slate-300">
                                    <span class="{{ (strtolower($val) === 'cukup' || empty($val)) ? 'text-slate-900 font-bold' : 'text-slate-500' }}">Cukup</span>
                                </label>
                            </td>
                            <td class="py-3 px-3 text-center">
                                <label class="inline-flex items-center gap-2 cursor-default">
                                    <input type="radio" disabled {{ strtolower($val) === 'kurang' ? 'checked' : '' }} class="w-4 h-4 text-primary-600 border-slate-300">
                                    <span class="{{ strtolower($val) === 'kurang' ? 'text-rose-600 font-bold' : 'text-slate-500' }}">Kurang</span>
                                </label>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ============================================================= -->
        <!-- TAB 4: TES KEPRIBADIAN -->
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
                    <p class="text-slate-700 leading-relaxed">
                        {{ $dominantDisc['summary'] }}
                    </p>
                </div>
            </div>

            <!-- 4-Column Table of Questions (Exact layout from interview.show) -->
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
        <!-- TAB 5: TES MATEMATIKA -->
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
        <!-- TAB 6: ANALISA AI (CV ANALYZER) -->
        <!-- ============================================================= -->
        <div x-show="activeTab === 'ai'" class="space-y-6">
            <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-wand-magic-sparkles text-amber-500"></i>
                <span>Analisa Otomatis AI (CV Analyzer)</span>
            </h3>

            @if(!empty($aiData))
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 space-y-1">
                        <span class="text-[11px] font-bold text-slate-500 uppercase">Match Rate Posisi</span>
                        <div class="text-2xl font-black text-primary">{{ $candidate->ai_score ?? '-' }}%</div>
                    </div>
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 space-y-1">
                        <span class="text-[11px] font-bold text-slate-500 uppercase">Tingkat Pengalaman</span>
                        <div class="text-base font-extrabold text-slate-800">{{ $aiData['experience_level'] ?? 'Mid Level' }}</div>
                    </div>
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 space-y-1">
                        <span class="text-[11px] font-bold text-slate-500 uppercase">Rekomendasi AI</span>
                        <div class="text-base font-extrabold text-emerald-600">{{ $aiData['recommendation'] ?? 'Direkomendasikan' }}</div>
                    </div>
                </div>

                <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-2">
                    <span class="text-xs font-bold text-slate-700 block">Ringkasan Evaluasi AI:</span>
                    <p class="text-xs text-slate-700 leading-relaxed">
                        {{ $aiData['summary'] ?? 'Analisa AI menunjukkan kandidat memiliki kualifikasi yang relevan dengan spesifikasi formasi lowongan.' }}
                    </p>
                </div>
            @else
                <div class="p-8 text-center bg-slate-50 rounded-2xl border border-slate-200 text-slate-400 text-xs italic">
                    Belum ada data analisa AI untuk kandidat ini.
                </div>
            @endif
        </div>

        <!-- ============================================================= -->
        <!-- TAB 7: APPROVAL INHOUSE (FORM KEPUTUSAN APPROVER & RIWAYAT) -->
        <!-- ============================================================= -->
        <div x-show="activeTab === 'approval'" class="space-y-6">
            
            <!-- STEP INDICATOR STATUS -->
            @php
                $statusAppr = $candidate->status_approval ?? 'Proses';
                $isApprovedHead = in_array($statusAppr, ['Review HRD', 'Approve']);
                $isApprovedHrd = ($statusAppr === 'Approve');
            @endphp

            <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl space-y-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block">Status Alur Approval:</span>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                    <div class="p-3 rounded-xl border {{ $isApprovedHead ? 'bg-emerald-50 border-emerald-300 text-emerald-900' : ($statusAppr === 'Review Head' ? 'bg-amber-50 border-amber-300 text-amber-900' : 'bg-white border-slate-200 text-slate-500') }}">
                        <div class="text-[10.5px] font-bold">STEP 1: PERSETUJUAN HEAD</div>
                        <div class="text-xs font-black flex items-center gap-1.5 mt-1">
                            @if($isApprovedHead)
                                <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i> Disetujui Head
                            @elseif($statusAppr === 'Review Head')
                                <i class="fa-solid fa-clock text-amber-500 text-sm"></i> Sedang Menunggu Head
                            @elseif($statusAppr === 'Tolak')
                                <i class="fa-solid fa-circle-xmark text-rose-500 text-sm"></i> Ditolak
                            @else
                                <i class="fa-solid fa-circle-dot text-slate-400 text-sm"></i> Belum Diajukan
                            @endif
                        </div>
                    </div>

                    <div class="p-3 rounded-xl border {{ $isApprovedHrd ? 'bg-emerald-50 border-emerald-300 text-emerald-900' : ($statusAppr === 'Review HRD' ? 'bg-amber-50 border-amber-300 text-amber-900' : 'bg-white border-slate-200 text-slate-400') }}">
                        <div class="text-[10.5px] font-bold">STEP 2: PERSETUJUAN HRD PUSAT</div>
                        <div class="text-xs font-black flex items-center gap-1.5 mt-1">
                            @if($isApprovedHrd)
                                <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i> Selesai (Disetujui Penuh)
                            @elseif($statusAppr === 'Review HRD')
                                <i class="fa-solid fa-clock text-amber-500 text-sm"></i> Sedang Menunggu HRD
                            @else
                                <i class="fa-solid fa-lock text-slate-400 text-sm"></i> Terkunci (Menunggu Head)
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- MAIN APPROVER GRID (Persis Gambar 3) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- KOLOM KIRI: Form Keputusan & Tanda Tangan Digital -->
                <div class="lg:col-span-6 bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
                    <div class="pb-2 border-b border-slate-100 flex items-center justify-between">
                        <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                            <i class="fa-solid fa-signature text-primary"></i>
                            <span>Form Approval {{ $isHrd ? 'HRD Pusat' : 'Head Approver' }}</span>
                        </h3>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $isHrd ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ $isHrd ? 'Step HRD' : 'Step Head' }}
                        </span>
                    </div>

                    <!-- STEP LOCKING VALIDATION CHECK -->
                    @if($isHrd && $statusAppr === 'Review Head')
                        <!-- HRD melihat kandidat yang masih menunggu persetujuan Head -->
                        <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs space-y-2">
                            <div class="flex items-center gap-2 font-bold text-sm text-amber-950">
                                <i class="fa-solid fa-lock text-amber-600"></i>
                                <span>Menunggu Persetujuan Head Approver (Step 1)</span>
                            </div>
                            <p class="text-[11.5px] leading-relaxed text-amber-800">
                                Kandidat ini masih dalam antrean evaluasi Head Approver. Form persetujuan HRD Pusat akan otomatis aktif setelah Head memberikan keputusan persetujuan.
                            </p>
                        </div>
                    @elseif(!$isHrd && in_array($statusAppr, ['Review HRD', 'Approve']))
                        <!-- Head melihat kandidat yang sudah diapprove oleh Head -->
                        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs space-y-2">
                            <div class="flex items-center gap-2 font-bold text-sm text-emerald-950">
                                <i class="fa-solid fa-circle-check text-emerald-600"></i>
                                <span>Persetujuan Head Telah Selesai Disubmit</span>
                            </div>
                            <p class="text-[11.5px] leading-relaxed text-emerald-800">
                                Anda telah menyetujui kandidat ini. Berkas saat ini sedang dalam proses evaluasi oleh HRD Pusat.
                            </p>
                        </div>
                    @elseif($statusAppr === 'Approve')
                        <!-- Selesai Disetujui HRD -->
                        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs space-y-2">
                            <div class="flex items-center gap-2 font-bold text-sm text-emerald-950">
                                <i class="fa-solid fa-circle-check text-emerald-600"></i>
                                <span>Kandidat Telah Selesai Disetujui (Approved)</span>
                            </div>
                            <p class="text-[11.5px] leading-relaxed text-emerald-800">
                                Proses persetujuan inhouse telah selesai. Kandidat telah dipindahkan ke tab Selesai.
                            </p>
                        </div>
                    @else
                        <!-- FORM AKTIF UNTUK APPROVER -->
                        <form action="{{ route('interviewinhouse.approval', $candidate->id) }}" method="POST" id="inhouseApprovalForm" class="space-y-4">
                            @csrf
                            <input type="hidden" name="submit_type" value="{{ $isHrd ? 'hrd' : 'head' }}">
                            <input type="hidden" name="signature_data" id="signatureDataInput" value="">

                            <!-- 1. Keputusan -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Keputusan</label>
                                <select name="approval" class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:ring-4 focus:ring-primary-100 focus:border-primary outline-none cursor-pointer" required>
                                    <option value="" disabled selected>Hasil Keputusan</option>
                                    <option value="Approve">Approve</option>
                                    <option value="Tolak">Tolak</option>
                                </select>
                            </div>

                            <!-- 2. Catatan -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Catatan</label>
                                <textarea name="catatan" rows="3" class="w-full bg-white border border-slate-200 rounded-xl p-3 text-xs text-slate-800 focus:ring-4 focus:ring-primary-100 focus:border-primary outline-none" placeholder="Masukkan catatan hasil evaluasi dan rekomendasi..." required></textarea>
                            </div>

                            <!-- 3. Tanda Tangan Canvas -->
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <label class="text-xs font-bold text-slate-700">Tanda Tangan</label>
                                    <div class="flex items-center gap-2">
                                        @if(!empty($user->signature_path))
                                            <button type="button" onclick="pasteMySavedSig()" class="text-[11px] font-bold text-emerald-700 hover:text-emerald-800 underline flex items-center gap-1 cursor-pointer">
                                                <i class="fa-solid fa-stamp text-[10px]"></i> Tempel TTD Saya
                                            </button>
                                        @endif
                                        <button type="button" onclick="clearSigCanvas()" class="text-[11px] font-bold text-rose-600 hover:text-rose-700 underline flex items-center gap-1 cursor-pointer">
                                            <i class="fa-solid fa-rotate-left text-[10px]"></i> Bersihkan
                                        </button>
                                    </div>
                                </div>

                                <div class="border-2 border-dashed border-slate-300 rounded-2xl p-1 bg-slate-50 relative overflow-hidden">
                                    <canvas id="sigCanvas" class="w-full h-44 bg-white rounded-xl touch-none cursor-crosshair border border-slate-100 shadow-inner"></canvas>
                                    <div id="sigPlaceholder" class="absolute inset-0 flex items-center justify-center pointer-events-none text-slate-300 text-xs font-semibold">
                                        <span>Gambar tanda tangan digital di sini</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Tombol Submit -->
                            <div class="pt-2">
                                <button type="submit" onclick="syncSigDataBeforeSubmit()" class="w-full py-3 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 shadow-md shadow-rose-600/20 transition-all flex items-center justify-center gap-2 cursor-pointer">
                                    <i class="fa-solid fa-check-double text-xs"></i>
                                    <span>Simpan & Submit Keputusan {{ $isHrd ? 'HRD Pusat' : 'Head' }}</span>
                                </button>
                            </div>
                        </form>
                    @endif
                </div>

                <!-- KOLOM KANAN: Kandidat Info & List Head Approve (Persis Gambar 3) -->
                <div class="lg:col-span-6 space-y-6">
                    
                    <!-- 1. Ringkasan Status Kandidat -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm space-y-3">
                        <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                            <i class="fa-solid fa-user-check text-primary"></i>
                            <span>Kandidat</span>
                        </h3>

                        <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-3.5 space-y-2 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500 font-medium">Status Formasi:</span>
                                <span class="font-bold text-slate-800">
                                    {{ $candidate->status_replace === 'Replace' ? 'Replace (Penggantian)' : 'New (Formasi Baru)' }}
                                </span>
                            </div>

                            @if($candidate->status_replace === 'Replace')
                                <div class="flex items-center justify-between border-t border-slate-200/60 pt-2">
                                    <span class="text-slate-500 font-medium">Menggantikan:</span>
                                    <span class="font-bold text-slate-900">{{ $candidate->menggantikan ?: '-' }}</span>
                                </div>
                                <div class="flex items-center justify-between border-t border-slate-200/60 pt-2">
                                    <span class="text-slate-500 font-medium">Tanggal Resign:</span>
                                    <span class="font-bold text-slate-800">
                                        {{ $candidate->tgl_resign ? \Carbon\Carbon::parse($candidate->tgl_resign)->format('d/m/Y') : '-' }}
                                    </span>
                                </div>
                                <div class="flex items-start justify-between border-t border-slate-200/60 pt-2">
                                    <span class="text-slate-500 font-medium shrink-0 w-28">Alasan Resign:</span>
                                    <span class="text-slate-700 font-medium text-right">{{ $candidate->alasan_resign ?: '-' }}</span>
                                </div>
                            @endif

                            <div class="flex items-center justify-between border-t border-slate-200/60 pt-2">
                                <span class="text-slate-500 font-medium">Pengaju (User Request):</span>
                                <span class="font-bold text-primary">{{ $candidate->user_request ?: ($candidate->user_display_name ?: '-') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- 2. List Head Approve (Tabel Persis Gambar 3) -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm space-y-3">
                        <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                            <i class="fa-solid fa-list-check text-primary"></i>
                            <span>List Head Approve</span>
                        </h3>

                        <div class="border border-slate-200 rounded-xl overflow-hidden">
                            <table class="w-full text-xs text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider text-[10.5px]">
                                        <th class="py-2.5 px-3">Nama</th>
                                        <th class="py-2.5 px-3">Catatan</th>
                                        <th class="py-2.5 px-2.5 text-center">Hasil Keputusan</th>
                                        <th class="py-2.5 px-2.5 text-center">Tanda Tangan</th>
                                        <th class="py-2.5 px-3 text-center">Waktu Submit</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @php
                                        $headApprovals = $candidate->inhouseApprovals->filter(function($appr) {
                                            $job = strtolower($appr->jabatan_approver ?? '');
                                            return !str_contains($job, 'admin hrd') && !str_contains($job, 'hr lead');
                                        });
                                    @endphp
                                    @forelse($headApprovals as $hAppr)
                                        <tr class="hover:bg-slate-50/70 transition-colors">
                                            <td class="py-3 px-3 font-bold text-slate-800">
                                                <div>{{ $hAppr->nama_approver }}</div>
                                                <div class="text-[9.5px] text-slate-400 font-normal">{{ $hAppr->jabatan_approver ?: 'Head' }}</div>
                                            </td>
                                            <td class="py-3 px-3 text-slate-600 leading-relaxed max-w-[150px]">
                                                {{ $hAppr->catatan_approver ?: '-' }}
                                            </td>
                                            <td class="py-3 px-2.5 text-center">
                                                @if(in_array(strtolower($hAppr->status ?? ''), ['approve', 'yes']))
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">Approve</span>
                                                @else
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-800 border border-rose-200">Tolak</span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-2.5 text-center">
                                                @if($hAppr->ttd_approver)
                                                    @php
                                                        $sigSrc = $hAppr->ttd_approver;
                                                        if (!str_starts_with($sigSrc, 'data:image') && !str_starts_with($sigSrc, 'http')) {
                                                            $sigSrc = asset($sigSrc);
                                                        }
                                                    @endphp
                                                    <img src="{{ $sigSrc }}" alt="TTD Head" class="h-8 max-w-[80px] mx-auto object-contain" onerror="this.src='/lampiran/{{ basename($hAppr->ttd_approver) }}';">
                                                @else
                                                    <span class="text-[9.5px] text-slate-400 italic">Belum TTD</span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-3 text-center text-[10.5px] text-slate-500 whitespace-nowrap">
                                                {{ $hAppr->time_approver ? \Carbon\Carbon::parse($hAppr->time_approver)->format('d/m/Y H:i') : '-' }}
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

                    <!-- 3. List Approval HRD jika sudah diapprove HRD -->
                    @php
                        $hrdApprovals = $candidate->inhouseApprovals->filter(function($appr) {
                            $job = strtolower($appr->jabatan_approver ?? '');
                            return str_contains($job, 'admin hrd') || str_contains($job, 'hr lead') || str_contains($job, 'hrd');
                        });
                    @endphp
                    @if($hrdApprovals->isNotEmpty())
                    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm space-y-3">
                        <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                            <i class="fa-solid fa-stamp text-emerald-600"></i>
                            <span>Approval HRD Pusat</span>
                        </h3>

                        <div class="border border-slate-200 rounded-xl overflow-hidden">
                            <table class="w-full text-xs text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider text-[10.5px]">
                                        <th class="py-2.5 px-3">Nama</th>
                                        <th class="py-2.5 px-3">Catatan</th>
                                        <th class="py-2.5 px-2.5 text-center">Hasil</th>
                                        <th class="py-2.5 px-2.5 text-center">Tanda Tangan</th>
                                        <th class="py-2.5 px-3 text-center">Waktu</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($hrdApprovals as $hrdAppr)
                                        <tr class="hover:bg-slate-50/70 transition-colors">
                                            <td class="py-3 px-3 font-bold text-slate-800">
                                                <div>{{ $hrdAppr->nama_approver }}</div>
                                                <div class="text-[9.5px] text-slate-400 font-normal">{{ $hrdAppr->jabatan_approver ?: 'ADMIN HRD' }}</div>
                                            </td>
                                            <td class="py-3 px-3 text-slate-600 leading-relaxed max-w-[150px]">
                                                {{ $hrdAppr->catatan_approver ?: '-' }}
                                            </td>
                                            <td class="py-3 px-2.5 text-center">
                                                @if(in_array(strtolower($hrdAppr->status ?? ''), ['approve', 'yes']))
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">Approve</span>
                                                @else
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-800 border border-rose-200">Tolak</span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-2.5 text-center">
                                                @if($hrdAppr->ttd_approver)
                                                    @php
                                                        $sigSrcHrd = $hrdAppr->ttd_approver;
                                                        if (!str_starts_with($sigSrcHrd, 'data:image') && !str_starts_with($sigSrcHrd, 'http')) {
                                                            $sigSrcHrd = asset($sigSrcHrd);
                                                        }
                                                    @endphp
                                                    <img src="{{ $sigSrcHrd }}" alt="TTD HRD" class="h-8 max-w-[80px] mx-auto object-contain" onerror="this.src='/lampiran/{{ basename($hrdAppr->ttd_approver) }}';">
                                                @else
                                                    <span class="text-[9.5px] text-slate-400 italic">Belum TTD</span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-3 text-center text-[10.5px] text-slate-500 whitespace-nowrap">
                                                {{ $hrdAppr->time_approver ? \Carbon\Carbon::parse($hrdAppr->time_approver)->format('d/m/Y H:i') : '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                </div>

            </div>
        </div>

    </div>

    <!-- PREVIEW MEDIA MODAL (POPUP PREVIEW FOTO / CV / DOKUMEN) -->
    <div x-show="mediaModalOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/80 backdrop-blur-xs flex items-center justify-center p-4"
         style="display: none;">
        <div @click.away="mediaModalOpen = false" class="bg-white rounded-3xl max-w-4xl w-full overflow-hidden shadow-2xl border border-slate-200">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-800 truncate pr-4" x-text="mediaModalTitle"></h3>
                <button @click="mediaModalOpen = false" class="w-8 h-8 rounded-full bg-slate-200 hover:bg-slate-300 text-slate-600 flex items-center justify-center transition">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
            <div class="p-6 flex items-center justify-center bg-slate-900/5 min-h-[300px]">
                <template x-if="mediaModalType === 'image'">
                    <img :src="mediaModalUrl" class="max-h-[75vh] max-w-full rounded-xl object-contain shadow-md">
                </template>
                <template x-if="mediaModalType === 'pdf'">
                    <iframe :src="mediaModalUrl" class="w-full h-[75vh] rounded-xl border border-slate-200"></iframe>
                </template>
            </div>
        </div>
    </div>

</div>

<!-- SCRIPT CANVAS TANDA TANGAN DIGITAL UNTUK APPROVER -->
<script>
    let sigCanvas, sigCtx, drawing = false, hasDrawnOnCanvas = false;
    const userSavedSigUrl = @json($user->getSignatureBase64() ?? null);

    function initApproverSigCanvas() {
        sigCanvas = document.getElementById('sigCanvas');
        if (!sigCanvas) return;
        sigCtx = sigCanvas.getContext('2d');
        if (!sigCtx) return;

        const rect = sigCanvas.getBoundingClientRect();
        sigCanvas.width = rect.width > 0 ? Math.round(rect.width) : 400;
        sigCanvas.height = 176;

        sigCtx.lineWidth = 2.5;
        sigCtx.lineCap = 'round';
        sigCtx.lineJoin = 'round';
        sigCtx.strokeStyle = '#0f172a';

        function getPointerPos(e) {
            const r = sigCanvas.getBoundingClientRect();
            const scaleX = sigCanvas.width / (r.width || 1);
            const scaleY = sigCanvas.height / (r.height || 1);
            let clientX, clientY;
            if (e.touches && e.touches.length > 0) {
                clientX = e.touches[0].clientX;
                clientY = e.touches[0].clientY;
            } else {
                clientX = e.clientX;
                clientY = e.clientY;
            }
            return {
                x: (clientX - r.left) * scaleX,
                y: (clientY - r.top) * scaleY
            };
        }

        function onStart(e) {
            drawing = true;
            hasDrawnOnCanvas = true;
            const ph = document.getElementById('sigPlaceholder');
            if (ph) ph.style.display = 'none';
            const pos = getPointerPos(e);
            sigCtx.beginPath();
            sigCtx.moveTo(pos.x, pos.y);
            sigCtx.lineTo(pos.x, pos.y);
            sigCtx.stroke();
            if (e.cancelable && e.type.startsWith('touch')) e.preventDefault();
        }

        function onMove(e) {
            if (!drawing) return;
            const pos = getPointerPos(e);
            sigCtx.lineTo(pos.x, pos.y);
            sigCtx.stroke();
            if (e.cancelable && e.type.startsWith('touch')) e.preventDefault();
        }

        function onEnd() {
            if (drawing) {
                drawing = false;
                sigCtx.closePath();
                syncSigDataBeforeSubmit();
            }
        }

        if (window.PointerEvent) {
            sigCanvas.addEventListener('pointerdown', function(e) {
                try { sigCanvas.setPointerCapture(e.pointerId); } catch(_) {}
                onStart(e);
            });
            sigCanvas.addEventListener('pointermove', onMove);
            sigCanvas.addEventListener('pointerup', function(e) {
                onEnd();
                try { sigCanvas.releasePointerCapture(e.pointerId); } catch(_) {}
            });
            sigCanvas.addEventListener('pointercancel', onEnd);
        } else {
            sigCanvas.addEventListener('mousedown', onStart);
            sigCanvas.addEventListener('mousemove', onMove);
            window.addEventListener('mouseup', onEnd);
            sigCanvas.addEventListener('touchstart', onStart, { passive: false });
            sigCanvas.addEventListener('touchmove', onMove, { passive: false });
            window.addEventListener('touchend', onEnd);
        }
    }

    function clearSigCanvas() {
        if (!sigCanvas || !sigCtx) return;
        sigCtx.clearRect(0, 0, sigCanvas.width, sigCanvas.height);
        hasDrawnOnCanvas = false;
        const ph = document.getElementById('sigPlaceholder');
        if (ph) ph.style.display = 'flex';
        const input = document.getElementById('signatureDataInput');
        if (input) input.value = '';
    }

    function pasteMySavedSig() {
        if (!userSavedSigUrl) {
            alert('Anda belum memiliki tanda tangan profil tersimpan.');
            return;
        }
        if (!sigCanvas || !sigCtx) return;
        const img = new Image();
        img.onload = function() {
            sigCtx.clearRect(0, 0, sigCanvas.width, sigCanvas.height);
            sigCtx.drawImage(img, 0, 0, sigCanvas.width, sigCanvas.height);
            hasDrawnOnCanvas = true;
            const ph = document.getElementById('sigPlaceholder');
            if (ph) ph.style.display = 'none';
            syncSigDataBeforeSubmit();
        };
        img.src = userSavedSigUrl;
    }

    function syncSigDataBeforeSubmit() {
        if (!sigCanvas) return;
        const input = document.getElementById('signatureDataInput');
        if (input && hasDrawnOnCanvas) {
            input.value = sigCanvas.toDataURL('image/png');
        }
    }

    function openCandidateMedia(type, url, title) {
        const root = document.querySelector('[x-data]');
        if (root && root._x_dataStack) {
            const data = root._x_dataStack[0];
            data.mediaModalType = type;
            data.mediaModalUrl = url;
            data.mediaModalTitle = title;
            data.mediaModalOpen = true;
        } else {
            window.open(url, '_blank');
        }
    }

    // --- REFERENSI CEK DROPDOWN & PROOF HANDLER ---
    const candidateWorkExps = @json($candidate->workExperiences);
    let currentExpProofUrl = '{{ $firstExp?->proof_url ?? "" }}';
    let currentExpCompanyName = '{{ addslashes($firstExp?->company_name ?? "") }}';

    function handleCompanySelect(val) {
        const exp = candidateWorkExps.find(e => e.id == val);
        if (exp) {
            if (document.getElementById('refcek_spv')) document.getElementById('refcek_spv').value = exp.supervisor_name || '';
            if (document.getElementById('refcek_phone')) document.getElementById('refcek_phone').value = exp.company_phone || '';
            if (document.getElementById('refcek_performance')) document.getElementById('refcek_performance').value = exp.performance_notes || exp.performa || 'Baik';
            if (document.getElementById('refcek_discipline')) document.getElementById('refcek_discipline').value = exp.discipline_notes || exp.disiplin || 'Tepat Waktu';
            if (document.getElementById('refcek_responsibility')) document.getElementById('refcek_responsibility').value = exp.responsibility_notes || exp.tanggungjawab || 'Bertanggung Jawab';
            if (document.getElementById('refcek_strengths')) document.getElementById('refcek_strengths').value = exp.strengths || exp.streng || '';
            if (document.getElementById('refcek_weaknesses')) document.getElementById('refcek_weaknesses').value = exp.weaknesses || exp.week || '';
            if (document.getElementById('refcek_reason')) document.getElementById('refcek_reason').value = exp.reason_for_leaving || exp.alasan_keluar || '';
            if (document.getElementById('refcek_start_date')) document.getElementById('refcek_start_date').value = exp.start_date ? exp.start_date.substring(0, 10) : '';
            if (document.getElementById('refcek_end_date')) document.getElementById('refcek_end_date').value = exp.end_date ? exp.end_date.substring(0, 10) : '';
            if (document.getElementById('refcek_date')) document.getElementById('refcek_date').value = exp.check_date ? exp.check_date.substring(0, 10) : '{{ date("Y-m-d") }}';
            updateRefcekProofUI(exp);
        } else {
            updateRefcekProofUI(null);
        }
    }

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

    document.addEventListener('DOMContentLoaded', initApproverSigCanvas);
</script>
@endsection