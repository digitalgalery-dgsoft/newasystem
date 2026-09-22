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
                $asDetails = \App\Http\Controllers\InterviewController::resolveCandidateAsDetails($candidate, $user);
                $candidateAsName = $asDetails['name'] ?? 'User AS';
                $assessSigPath = $assess?->interviewer_signature_path;
            @endphp
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- Matrix Assessment Table -->
                <div class="lg:col-span-8 space-y-5">
                    <div class="border border-slate-200 rounded-2xl overflow-hidden shadow-2xs">
                        <table class="w-full text-xs text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase tracking-wider text-[11px]">
                                    <th class="py-3 px-4 w-40 font-bold">Aspek Penilaian</th>
                                    <th class="py-3 px-3 text-center font-bold">Hasil Penilaian Rekrutor / AS</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @php
                                    $pillars = [
                                        ['name' => 'kemauan_kerja', 'label' => 'Kemauan Kerja', 'val' => $assess?->work_motivation ?? 'Cukup'],
                                        ['name' => 'penampilan', 'label' => 'Penampilan', 'val' => $assess?->appearance ?? 'Cukup'],
                                        ['name' => 'attitude', 'label' => 'Attitude', 'val' => $assess?->attitude ?? 'Cukup'],
                                        ['name' => 'daya_tangkap', 'label' => 'Daya Tangkap', 'val' => $assess?->comprehension ?? 'Cukup'],
                                    ];
                                @endphp
                                @foreach($pillars as $p)
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <td class="py-3.5 px-4 font-bold text-slate-800 text-xs">{{ $p['label'] }}</td>
                                    <td class="py-3.5 px-3 text-center">
                                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-primary-50 text-primary-700 border border-primary-200">
                                            {{ $p['val'] }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Catatan Interview -->
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700">Catatan Lain-lain dari Rekrutor:</label>
                        <p class="text-xs text-slate-800 leading-relaxed italic">
                            "{{ $assess?->other_notes ?? 'Tidak ada catatan wawancara khusus.' }}"
                        </p>
                    </div>

                    <!-- Tanggal Interview -->
                    <div class="text-xs text-slate-600">
                        <span class="font-medium text-slate-400">Tanggal Wawancara:</span>
                        <strong class="text-slate-800 ml-1">{{ $assess?->interview_date ? \Carbon\Carbon::parse($assess->interview_date)->format('d F Y') : '-' }}</strong>
                    </div>
                </div>

                <!-- Right: Tanda Tangan Interviewer / AS -->
                <div class="lg:col-span-4 space-y-3">
                    <label class="block text-xs font-bold text-slate-800">Tanda Tangan Pewawancara (AS / Rekrutor)</label>
                    <span class="text-[10px] text-slate-500 font-medium block">Pewawancara: <b class="text-slate-700">{{ $candidateAsName }}</b></span>
                    
                    <div class="border-2 border-dashed border-slate-200 rounded-2xl p-2 bg-slate-50 flex items-center justify-center min-h-[160px]">
                        @if(!empty($assessSigPath))
                            @php
                                $asSigSrc = $assessSigPath;
                                if (!str_starts_with($asSigSrc, 'data:image') && !str_starts_with($asSigSrc, 'http')) {
                                    $asSigSrc = asset($asSigSrc);
                                }
                            @endphp
                            <img src="{{ $asSigSrc }}" alt="TTD Pewawancara" class="max-h-32 object-contain" onerror="this.src='/lampiran/{{ basename($assessSigPath) }}';">
                        @else
                            <span class="text-xs text-slate-400 italic">Belum dibubuhi tanda tangan pewawancara</span>
                        @endif
                    </div>
                </div>

            </div>
        </div>

        <!-- ============================================================= -->
        <!-- TAB 2: REFERENSI CEK -->
        <!-- ============================================================= -->
        <div x-show="activeTab === 'refcek'" class="space-y-6">
            <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-phone-volume text-primary"></i>
                <span>Data Pengalaman Kerja & Referensi Cek</span>
            </h3>

            <div class="border border-slate-200 rounded-2xl overflow-hidden shadow-2xs">
                <table class="w-full text-xs text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider text-[11px]">
                            <th class="py-3 px-4">Nama Perusahaan</th>
                            <th class="py-3 px-3">Jabatan</th>
                            <th class="py-3 px-3">Periode</th>
                            <th class="py-3 px-3">Kontak Referensi</th>
                            <th class="py-3 px-4">Catatan Referensi Cek</th>
                            <th class="py-3 px-3 text-center">Bukti SS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($candidate->workExperiences as $exp)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3.5 px-4 font-bold text-slate-900">{{ $exp->company_name }}</td>
                                <td class="py-3.5 px-3 text-slate-700">{{ $exp->position ?? '-' }}</td>
                                <td class="py-3.5 px-3 text-slate-500 whitespace-nowrap">{{ $exp->period_label }}</td>
                                <td class="py-3.5 px-3 text-slate-600">
                                    {{ $exp->contact_person ? "{$exp->contact_person} ({$exp->contact_number})" : '-' }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-700 italic">
                                    {{ $exp->notes ?: 'Tidak ada catatan referensi cek.' }}
                                </td>
                                <td class="py-3.5 px-3 text-center">
                                    @if($exp->proof_url)
                                        <button type="button" onclick="openCandidateMedia('image', '{{ $exp->proof_url }}', 'Bukti Referensi Cek: {{ addslashes($exp->company_name) }}')" class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 font-bold text-[11px] border border-emerald-200 hover:bg-emerald-100 transition">
                                            <i class="fa-solid fa-image mr-1"></i> Bukti
                                        </button>
                                    @else
                                        <span class="text-slate-400 text-[11px] italic">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-xs text-slate-400 italic bg-slate-50/50">
                                    Belum ada data pengalaman kerja / referensi cek yang tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ============================================================= -->
        <!-- TAB 3: TES KOMPUTER -->
        <!-- ============================================================= -->
        <div x-show="activeTab === 'kompt'" class="space-y-6">
            <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-laptop-code text-primary"></i>
                <span>Hasil Tes Komputer</span>
            </h3>

            @php $comp = $candidate->computer_test; @endphp
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 text-center space-y-1">
                    <span class="text-[11px] font-bold text-slate-500 uppercase">MS Word</span>
                    <div class="text-lg font-black text-slate-800">{{ $comp['word'] ?? '-' }}</div>
                </div>
                <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 text-center space-y-1">
                    <span class="text-[11px] font-bold text-slate-500 uppercase">MS Excel</span>
                    <div class="text-lg font-black text-slate-800">{{ $comp['excel'] ?? '-' }}</div>
                </div>
                <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 text-center space-y-1">
                    <span class="text-[11px] font-bold text-slate-500 uppercase">MS PowerPoint</span>
                    <div class="text-lg font-black text-slate-800">{{ $comp['ppt'] ?? '-' }}</div>
                </div>
                <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 text-center space-y-1">
                    <span class="text-[11px] font-bold text-slate-500 uppercase">Internet & Email</span>
                    <div class="text-lg font-black text-slate-800">{{ $comp['internet'] ?? '-' }}</div>
                </div>
            </div>

            <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-700 block">Total Skor Tes Komputer:</span>
                    <span class="text-sm font-black text-primary">{{ $comp['score'] ?? '-' }}</span>
                </div>
                @if(!empty($comp['proof_url']))
                    <button type="button" onclick="openCandidateMedia('image', '{{ $comp['proof_url'] }}', 'Bukti Tes Komputer: {{ addslashes($candidate->full_name) }}')" class="px-3.5 py-2 rounded-xl bg-white border border-slate-200 shadow-sm text-xs font-bold text-slate-700 hover:bg-slate-50 transition flex items-center gap-2">
                        <i class="fa-solid fa-file-image text-primary"></i>
                        <span>Lihat Bukti Tes</span>
                    </button>
                @endif
            </div>
        </div>

        <!-- ============================================================= -->
        <!-- TAB 4: TES KEPRIBADIAN -->
        <!-- ============================================================= -->
        <div x-show="activeTab === 'kepribadian'" class="space-y-6">
            <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-brain text-primary"></i>
                <span>Evaluasi Tes Kepribadian</span>
            </h3>

            @php $personality = $candidate->personality_test; @endphp
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 space-y-1">
                    <span class="text-[11px] font-bold text-slate-500 uppercase">Tipe Kepribadian (DISC)</span>
                    <div class="text-base font-extrabold text-primary">{{ $personality['disc_type'] ?? ($personality['type'] ?? 'Belum Dinilai') }}</div>
                </div>
                <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 space-y-1">
                    <span class="text-[11px] font-bold text-slate-500 uppercase">Gaya Komunikasi</span>
                    <div class="text-base font-extrabold text-slate-800">{{ $personality['communication_style'] ?? '-' }}</div>
                </div>
                <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 space-y-1">
                    <span class="text-[11px] font-bold text-slate-500 uppercase">Respon Tekanan Kerja</span>
                    <div class="text-base font-extrabold text-slate-800">{{ $personality['stress_handling'] ?? '-' }}</div>
                </div>
            </div>

            <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Analisa Kepribadian & Rekomendasi Karakter:</label>
                <p class="text-xs text-slate-700 leading-relaxed italic">
                    {{ $personality['summary'] ?? ($personality['notes'] ?? 'Belum ada catatan deskriptif hasil tes kepribadian.') }}
                </p>
            </div>
        </div>

        <!-- ============================================================= -->
        <!-- TAB 5: TES MATEMATIKA -->
        <!-- ============================================================= -->
        <div x-show="activeTab === 'matematika'" class="space-y-6">
            <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-calculator text-primary"></i>
                <span>Hasil Tes Matematika Dasar</span>
            </h3>

            @php $math = $candidate->math_test; @endphp
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 text-center space-y-1">
                    <span class="text-[11px] font-bold text-slate-500 uppercase">Jawaban Benar</span>
                    <div class="text-xl font-black text-emerald-600">{{ $math['correct'] ?? '-' }}</div>
                </div>
                <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 text-center space-y-1">
                    <span class="text-[11px] font-bold text-slate-500 uppercase">Jawaban Salah</span>
                    <div class="text-xl font-black text-rose-600">{{ $math['wrong'] ?? '-' }}</div>
                </div>
                <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 text-center space-y-1">
                    <span class="text-[11px] font-bold text-slate-500 uppercase">Skor Akhir Matematika</span>
                    <div class="text-xl font-black text-primary">{{ $math['score'] ?? '-' }}</div>
                </div>
            </div>
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

    document.addEventListener('DOMContentLoaded', initApproverSigCanvas);
</script>
@endsection