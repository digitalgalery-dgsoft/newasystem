@extends('layouts.app')

@section('title', 'Hasil Test - ' . $candidate->full_name)
@section('page_title', 'HASIL TEST KANDIDAT')
@section('breadcrumb_active', 'Detail Evaluasi')

@section('content')
<div x-data="{ 
    activeTab: 'interview', 
    editPrincipleModal: false, 
    archiveModal: false,
    computerEnabled: true,
    photoUploadModal: false
}" class="space-y-6">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white border border-slate-200/80 rounded-2xl p-4 shadow-sm">
        <div class="flex items-center gap-3">
            <a href="{{ route('interview.index') }}" 
               class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors shadow-sm"
               title="Kembali ke Daftar Interview">
                <i class="fa-solid fa-arrow-left text-sm"></i>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-primary-600 bg-primary-50 px-2 py-0.5 rounded-md border border-primary-100">
                        Evaluasi Kandidat
                    </span>
                    <span class="text-xs text-slate-400">&bull;</span>
                    <span class="text-xs text-slate-500 font-medium">ID #{{ $candidate->id }}</span>
                </div>
                <h1 class="text-lg font-black text-slate-800 tracking-tight">HASIL TEST KANDIDAT</h1>
            </div>
        </div>

        <div class="flex items-center flex-wrap gap-2.5">
            <!-- Toggle Tes Komputer (From Legacy App) -->
            <button @click="computerEnabled = !computerEnabled" 
                    type="button" 
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold transition-all shadow-sm"
                    :class="computerEnabled ? 'bg-amber-500 hover:bg-amber-600 text-white shadow-amber-500/20' : 'bg-slate-600 hover:bg-slate-700 text-white shadow-slate-600/20'">
                <i class="fa-solid fa-laptop-code text-xs"></i>
                <span x-text="computerEnabled ? 'Tes Komputer: Set OFF (Disable)' : 'Tes Komputer: Set ON (Enable)'">Tes Komputer: Set OFF (Disable)</span>
            </button>

            <!-- Back Button -->
            <a href="{{ route('interview.index') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold bg-primary-600 hover:bg-primary-700 text-white shadow-sm shadow-primary-500/20 transition-all">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                <span>Back</span>
            </a>
        </div>
    </div>

    <!-- Profil Kandidat Card (Executive Layout with 11 Authentic Rows) -->
    <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm">
        <div class="flex items-center justify-between pb-3.5 mb-4 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-primary-50 text-primary-600 flex items-center justify-center font-bold text-sm">
                    <i class="fa-solid fa-address-card"></i>
                </div>
                <h2 class="text-base font-bold text-slate-800">Profil Kandidat</h2>
            </div>

            <!-- 2 Action Buttons on Top Right of Table (Edit Prinsiple & Arsip) -->
            <div class="flex items-center gap-2">
                <button @click="editPrincipleModal = true" 
                        type="button" 
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-sky-50 text-sky-700 border border-sky-200 hover:bg-sky-100 transition-all shadow-sm">
                    <i class="fa-solid fa-pen-to-square text-sky-600 text-[11px]"></i>
                    <span>Edit Prinsiple</span>
                </button>

                <button @click="archiveModal = true" 
                        type="button" 
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100 transition-all shadow-sm">
                    <i class="fa-solid fa-box-archive text-rose-600 text-[11px]"></i>
                    <span>Arsipkan Kandidat</span>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left Side: Clean Professional Table with 11 Authentic Rows -->
            <div class="lg:col-span-9 overflow-x-auto">
                <table class="w-full text-xs border-collapse">
                    <tbody class="divide-y divide-slate-100">
                        <tr class="hover:bg-slate-50/50">
                            <th class="py-2.5 px-3 text-left font-bold text-slate-700 w-44">No. KTP</th>
                            <td class="py-2.5 px-3 text-slate-800">: <strong class="font-mono bg-slate-100 px-2 py-0.5 rounded text-slate-900 border border-slate-200">{{ $candidate->nik }}</strong></td>
                        </tr>
                        <tr class="bg-slate-50/60 hover:bg-slate-50">
                            <th class="py-2.5 px-3 text-left font-bold text-slate-700">Nama Kandidat</th>
                            <td class="py-2.5 px-3 text-slate-900 font-bold text-sm">: {{ $candidate->full_name }}</td>
                        </tr>
                        <tr class="hover:bg-slate-50/50">
                            <th class="py-2.5 px-3 text-left font-bold text-slate-700">Alamat KTP</th>
                            <td class="py-2.5 px-3 text-slate-700 leading-relaxed">: {{ $candidate->address_ktp ?? '-' }}</td>
                        </tr>
                        <tr class="bg-slate-50/60 hover:bg-slate-50">
                            <th class="py-2.5 px-3 text-left font-bold text-slate-700">Usia</th>
                            <td class="py-2.5 px-3 text-slate-800">: <span class="font-semibold">{{ $candidate->age }} Tahun</span></td>
                        </tr>
                        <tr class="hover:bg-slate-50/50">
                            <th class="py-2.5 px-3 text-left font-bold text-slate-700">Pendidikan Terakhir</th>
                            <td class="py-2.5 px-3 text-slate-800">: <span class="font-semibold">{{ $candidate->education ?? '-' }}</span></td>
                        </tr>
                        <tr class="bg-slate-50/60 hover:bg-slate-50">
                            <th class="py-2.5 px-3 text-left font-bold text-slate-700">Mobile</th>
                            <td class="py-2.5 px-3 text-slate-800">
                                : <a href="https://wa.me/{{ $candidate->clean_whatsapp }}" target="_blank" class="text-emerald-700 bg-emerald-50 border border-emerald-200 hover:bg-emerald-100 px-2.5 py-1 rounded-md font-semibold inline-flex items-center gap-1.5 transition-colors">
                                    <i class="fa-brands fa-whatsapp text-emerald-600 text-xs"></i>
                                    <span>{{ $candidate->phone ?? '-' }}</span>
                                </a>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-50/50">
                            <th class="py-2.5 px-3 text-left font-bold text-slate-700">Prinsiple</th>
                            <td class="py-2.5 px-3 text-slate-800 font-semibold">: <strong class="text-primary-700">{{ $candidate->principle->name ?? 'PT ARINA MULTI KARYA' }}</strong> - {{ $candidate->area ?? 'JAKARTA' }}</td>
                        </tr>
                        <tr class="bg-slate-50/60 hover:bg-slate-50">
                            <th class="py-2.5 px-3 text-left font-bold text-slate-700">Jabatan</th>
                            <td class="py-2.5 px-3 text-slate-800 font-semibold">: {{ $candidate->applied_job ?? 'Principal' }}</td>
                        </tr>
                        <tr class="hover:bg-slate-50/50">
                            <th class="py-2.5 px-3 text-left font-bold text-slate-700">Status</th>
                            <td class="py-2.5 px-3">
                                : <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                    {{ $candidate->status_approval ?? 'Proses' }}
                                </span>
                            </td>
                        </tr>
                        <tr class="bg-slate-50/60 hover:bg-slate-50">
                            <th class="py-2.5 px-3 text-left font-bold text-slate-700">Nama AS</th>
                            <td class="py-2.5 px-3 text-slate-800 font-mono">: {{ $candidate->useras ?? $candidate->recruiter->email ?? 'susanti162021@gmail.com' }}</td>
                        </tr>
                        <tr class="hover:bg-slate-50/50">
                            <th class="py-2.5 px-3 text-left font-bold text-slate-700">Catatan</th>
                            <td class="py-2.5 px-3 text-slate-700 italic">: {{ $candidate->notes ?? '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Right Side: Highly Attractive Photo Box with Live Upload/Dropzone -->
            <div class="lg:col-span-3 flex flex-col items-center justify-center">
                <div class="relative group w-full max-w-[190px]">
                    <!-- Photo Container Frame -->
                    <div class="w-full h-[220px] rounded-2xl bg-gradient-to-b from-slate-100 to-slate-200 border-2 border-dashed border-slate-300 group-hover:border-primary-500 transition-all flex flex-col items-center justify-center p-3 text-center shadow-inner overflow-hidden relative">
                        
                        @if(!empty($candidate->photo_path))
                            <img id="candidatePhotoPreview" 
                                 src="{{ asset('storage/' . $candidate->photo_path) }}" 
                                 alt="Foto {{ $candidate->full_name }}" 
                                 class="w-full h-full object-cover rounded-xl shadow-sm">
                        @else
                            <div id="candidatePhotoPlaceholder" class="flex flex-col items-center justify-center space-y-2">
                                <div class="w-14 h-14 rounded-full bg-white text-slate-400 flex items-center justify-center shadow-sm group-hover:text-primary-600 transition-colors">
                                    <i class="fa-regular fa-image text-2xl"></i>
                                </div>
                                <div>
                                    <span class="text-xs font-bold text-slate-700 block">Foto Tidak Ada</span>
                                    <span class="text-[10px] text-slate-400">Klik untuk upload foto</span>
                                </div>
                            </div>
                            <img id="candidatePhotoPreview" class="hidden w-full h-full object-cover rounded-xl shadow-sm">
                        @endif

                        <!-- Hover Overlay to Upload / Change Photo -->
                        <label for="candidatePhotoInput" 
                               class="absolute inset-0 bg-slate-900/60 backdrop-blur-xs opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center text-white cursor-pointer p-3 text-center">
                            <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center mb-1 text-base">
                                <i class="fa-solid fa-camera"></i>
                            </div>
                            <span class="text-xs font-bold">Ganti / Upload Foto</span>
                            <span class="text-[9px] text-slate-300 mt-0.5">JPG / PNG maks 2MB</span>
                        </label>
                    </div>

                    <!-- Hidden File Input for Direct Photo Upload -->
                    <form action="#" method="POST" enctype="multipart/form-data" id="photoUploadForm">
                        @csrf
                        <input type="file" 
                               id="candidatePhotoInput" 
                               name="photo" 
                               accept="image/jpeg,image/png,image/jpg" 
                               class="hidden" 
                               onchange="handleCandidatePhotoPreview(this)">
                    </form>

                    <div class="text-center mt-2">
                        <span class="text-[11px] text-slate-400 font-medium">Foto Profil Kandidat (3x4)</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Download All Document Button & Subtext -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 bg-slate-50 border border-slate-200/80 rounded-2xl p-4 shadow-xs">
        <div class="flex items-center gap-3">
            <a href="{{ route('interview.pdf', $candidate->id) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold bg-amber-500 hover:bg-amber-600 text-slate-900 shadow-md shadow-amber-500/20 transition-all cursor-pointer">
                <i class="fa-solid fa-file-pdf text-amber-400 text-sm"></i>
                <span>Download All Document</span>
            </a>
            <span class="text-xs italic text-blue-600 font-semibold flex items-center gap-1.5">
                <i class="fa-solid fa-circle-info text-[11px]"></i>
                Dokument Bisa di Download Jika Sudah Approve Prinsiple
            </span>
        </div>

        <span class="text-xs font-mono text-slate-500 font-medium">Status: Standar Rekrutmen Terverifikasi</span>
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

        <button @click="activeTab = 'userprinsiple'" 
                :class="activeTab === 'userprinsiple' ? 'bg-primary-600 text-white shadow-md shadow-primary-500/25 font-bold' : 'text-slate-600 hover:text-primary-600 hover:bg-white font-semibold'" 
                class="px-4 py-2.5 rounded-xl text-xs md:text-sm transition-all duration-150 flex items-center gap-2 whitespace-nowrap">
            <i class="fa-solid fa-building-circle-check text-xs"></i>
            <span>User Principle</span>
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

                        <!-- Plus Button at bottom right -->
                        <div class="flex justify-end pt-8">
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
            <!-- Header Bar with Timer & 4 Answer Badges -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3 border-b border-slate-100">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-100 text-slate-800 text-xs font-semibold">
                    <i class="fa-solid fa-clock text-slate-500"></i>
                    <span>Waktu Pengerjaan : <strong>00:04:20</strong></span>
                </div>

                <div class="flex items-center flex-wrap gap-2 text-xs font-bold">
                    <span class="px-3 py-1 rounded-lg bg-rose-50 text-rose-700 border border-rose-200">Jawaban A : 17</span>
                    <span class="px-3 py-1 rounded-lg bg-amber-50 text-amber-700 border border-amber-200">Jawaban B : 7</span>
                    <span class="px-3 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200">Jawaban C : 9</span>
                    <span class="px-3 py-1 rounded-lg bg-blue-50 text-blue-700 border border-blue-200">Jawaban D : 7</span>
                </div>
            </div>

            <!-- Kesimpulan Box (Exact text from screenshot) -->
            <div class="bg-gradient-to-r from-amber-50/80 to-amber-100/50 border border-amber-200/80 rounded-2xl p-5 text-xs text-slate-800 leading-relaxed shadow-xs flex items-start gap-3.5">
                <div class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center flex-shrink-0 text-sm shadow-sm">
                    <i class="fa-solid fa-lightbulb"></i>
                </div>
                <div>
                    <h4 class="font-bold text-amber-900 text-sm mb-1">Kesimpulan Karakter:</h4>
                    <p class="text-slate-700">
                        Memiliki kepribadian <strong class="text-slate-900 font-bold">Melankolis</strong>. Tipe ini paling baik dalam hal pekerjaan yang memerlukan keputusan cepat; persoalan yang memerlukan tindakan dan pencapaian seketika; bidang-bidang yang menuntut kontrol dan wewenang yang kuat. Kelemahan tipe ini adalah tidak tahu bagaimana cara menangani orang lain; sulit mengakui kesalahan; sulit bersikap sabar; terlalu pekerja keras.
                    </p>
                </div>
            </div>

            <!-- 4-Column Table of 40 Questions (Exact screenshot layout) -->
            @php
                $qData = [
                    1 => ['ans' => 'B', 'text' => 'Antusias'],
                    2 => ['ans' => 'C', 'text' => 'Menyukai Logika dan Fakta'],
                    3 => ['ans' => 'C', 'text' => 'Teguh Pendirian'],
                    4 => ['ans' => 'A', 'text' => 'Toleran'],
                    5 => ['ans' => 'A', 'text' => 'Menghargai'],
                    6 => ['ans' => 'C', 'text' => 'Mandiri'],
                    7 => ['ans' => 'A', 'text' => 'Perencana'],
                    8 => ['ans' => 'A', 'text' => 'Terjadwal'],
                    9 => ['ans' => 'B', 'text' => 'Optimis'],
                    10 => ['ans' => 'B', 'text' => 'Humoris'],
                    11 => ['ans' => 'B', 'text' => 'Penuh Strategi, Perasa dan Sabar'],
                    12 => ['ans' => 'B', 'text' => 'Bersemangat'],
                    13 => ['ans' => 'D', 'text' => 'Berkorban Tidak Menyakiti Hati Orang Lain'],
                    14 => ['ans' => 'A', 'text' => 'Suka Mengintropeksi'],
                    15 => ['ans' => 'D', 'text' => 'Mudah Membaur'],
                    16 => ['ans' => 'C', 'text' => 'Berpendirian Teguh'],
                    17 => ['ans' => 'B', 'text' => 'Penuh Semangat'],
                    18 => ['ans' => 'A', 'text' => 'Suka Membuat Grafik dan Daftar Tugas'],
                    19 => ['ans' => 'C', 'text' => 'Produktif'],
                    20 => ['ans' => 'A', 'text' => 'Memiliki Batasan Dalam Berperilaku'],
                    21 => ['ans' => 'A', 'text' => 'Pemalu'],
                    22 => ['ans' => 'C', 'text' => 'Tidak Teratur'],
                    23 => ['ans' => 'D', 'text' => 'Tidak Suka Terlibat Dalam Masalah Polik'],
                    24 => ['ans' => 'B', 'text' => 'Mudah Lupa'],
                    25 => ['ans' => 'A', 'text' => 'Sulit Percaya'],
                    26 => ['ans' => 'A', 'text' => 'Tidak Populer'],
                    27 => ['ans' => 'C', 'text' => 'Keras Kepala'],
                    28 => ['ans' => 'D', 'text' => 'Dingin'],
                    29 => ['ans' => 'A', 'text' => 'Mudah Merasa Terasing'],
                    30 => ['ans' => 'C', 'text' => 'Nekat'],
                    31 => ['ans' => 'A', 'text' => 'Menarik Diri Dari Pergaulan'],
                    32 => ['ans' => 'D', 'text' => 'Tidak Suka Konflik'],
                    33 => ['ans' => 'D', 'text' => 'Kurang Yakin'],
                    34 => ['ans' => 'A', 'text' => 'Tertutup'],
                    35 => ['ans' => 'A', 'text' => 'Moody'],
                    36 => ['ans' => 'A', 'text' => 'Tidak Mudah Percaya'],
                    37 => ['ans' => 'A', 'text' => 'Penyendiri'],
                    38 => ['ans' => 'A', 'text' => 'Mudah Curiga'],
                    39 => ['ans' => 'D', 'text' => 'Menolak Dilibatkan'],
                    40 => ['ans' => 'C', 'text' => 'Cerdik dan Licik'],
                ];
            @endphp

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
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-2 px-2.5 text-center font-bold text-slate-400">{{ $i }}</td>
                                <td class="py-2 px-2 text-center font-mono font-bold text-primary-700 bg-slate-50/50">{{ $qData[$i]['ans'] }}</td>
                                <td class="py-2 px-2.5 text-slate-700 leading-tight">{{ $qData[$i]['text'] }}</td>
                            </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
                @endfor
            </div>
        </div>

        <!-- ============================================================= -->
        <!-- TAB 5: TES MATEMATIKA (Matching Image 5) -->
        <!-- ============================================================= -->
        <div x-show="activeTab === 'matematika'" class="space-y-5">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-100 text-slate-800 text-xs font-semibold">
                        <i class="fa-solid fa-stopwatch text-slate-500"></i>
                        <span>Waktu Pengerjaan : <strong>00:02:00</strong></span>
                    </div>
                    <span class="text-xs font-bold text-slate-700 bg-slate-50 px-2.5 py-1 rounded-md border border-slate-200">
                        Tes Ke - 1
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

            <!-- Table of 10 Math Questions (Exact from screenshot) -->
            @php
                $mathItems = [
                    1 => [
                        'q' => 'Ani membeli Lampu Philips 50 Watt Seharga Rp. 200.000,- di C4 Buaran, dan di C4 Sedang ada Promo Diskon 15% Untuk Pembelian Lampu Philips. Berapa Rupiah Yang Harus Dibayar Ani?',
                        'cand' => '170000', 'key' => '170000', 'correct' => true
                    ],
                    2 => [
                        'q' => 'Yani Membeli 2 Buah Bedak Loreal Seharga Rp. 350.000,- di Matahari Departemen Store Pejaten dan Sedang Ada Promo Untuk Pembelian Kedua Diskon 35%. Berapa Rupiah Yang Harus Dibayar Yani?',
                        'cand' => '390000', 'key' => '405000', 'correct' => false
                    ],
                    3 => [
                        'q' => 'SPG Dancow di C4 Cempaka Mas Mempunyai Target Sebanyak Rp. 7.000.000,- dan Baru Mencapai Target Sebanyak Rp. 5.000.000,-. Sudah Berapa Persen Pencapaian SPG Tersebut?',
                        'cand' => '71,42%', 'key' => '71,42%', 'correct' => true
                    ],
                    4 => [
                        'q' => 'Bagas Membeli Wafer TimTam 200gr Seharga Rp. 5.250,- sebanyak 15 Bungkus di Lotte Kelapa Gading dan Sedang Ada Promo Diskon 15%. Berapa Rupiah Yang Harus Dibayar Bagas?',
                        'cand' => 'A', 'key' => 'A', 'correct' => true
                    ],
                    5 => [
                        'q' => 'Putri Membeli Boneka Rp. 50.000,- Kemudian Boneka itu Dijual kembali dengan Harga Rp. 60.000. Berapa persen Keuntungan Putri?',
                        'cand' => 'D', 'key' => 'D', 'correct' => true
                    ],
                    6 => [
                        'q' => 'Lanjutan perhitungan berikut 24, 20, 16, 12, ......',
                        'cand' => '8', 'key' => '8,4', 'correct' => false
                    ],
                    7 => [
                        'q' => 'Ibu mempunyai uang sebesar Rp. 30.000,- Uang itu dibelikan lauk pauk Rp. 12.000,- Sayuran Rp. 4.000,- dan Minyak Goreng Rp. 4.000,- Berapa Sisa uang ibu?',
                        'cand' => 'B', 'key' => 'B', 'correct' => true
                    ],
                    8 => [
                        'q' => 'Angga mempunyai uang sebesar Rp. 4.500.000,- dan ia berniat membeli sebuah handicam seharga Rp. 2.500.000,- sebelum diskon. harga handycam tersebut adalah 20% setelah itu Angga juga membelanjakan uangnya untuk keperluan lain sebesar Rp. 1.500.000,-. Berapa sisa uang Angga Saat ini?',
                        'cand' => 'A', 'key' => 'A', 'correct' => true
                    ],
                    9 => [
                        'q' => 'Sinta membeli 2 pcs pelembab Loreal seharga Rp. 80.000,- untuk satu pelembab dan di MDS Pejaten sedang ada promosi untuk pembelian kedua diskon 75%. Berapa Rupiah yang harus dibayar santi?',
                        'cand' => '100000', 'key' => '100000', 'correct' => true
                    ],
                    10 => [
                        'q' => 'SPG Arnotts di C4 KLL mempunyai target sebanyak Rp. 12.000.000,- dan baru mencapai target sebanyak Rp. 5.000.000,-. Sudah berapa persen pencapaian SPG tersebut?',
                        'cand' => '41,67%', 'key' => '41,67%', 'correct' => true
                    ],
                ];
            @endphp

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
                    <span class="text-xl font-black text-emerald-900">8</span>
                </div>
                <div class="bg-rose-50 border border-rose-200 rounded-xl p-3 text-center">
                    <span class="text-[11px] font-semibold text-rose-700 block">Jawaban Salah</span>
                    <span class="text-xl font-black text-rose-900">2</span>
                </div>
                <div class="bg-primary-50 border border-primary-200 rounded-xl p-3 text-center">
                    <span class="text-[11px] font-semibold text-primary-700 block">Nilai Akhir</span>
                    <span class="text-xl font-black text-primary-900">B</span>
                </div>
            </div>
        </div>

        <!-- ============================================================= -->
        <!-- TAB 6: USER PRINCIPLE (Matching Legacy App) -->
        <!-- ============================================================= -->
        <div x-show="activeTab === 'userprinsiple'" class="space-y-6">
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
                            <strong class="text-slate-900">{{ $candidate->principle->name ?? 'PT ARINA MULTI KARYA' }} (Manager)</strong>
                        </div>
                        <div class="flex items-center justify-between border-t border-slate-200/60 pt-2">
                            <span class="text-slate-500 font-medium">Status Approval:</span>
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                Menunggu Approval
                            </span>
                        </div>
                    </div>

                    <div class="pt-2">
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Note Prinsiple :</label>
                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 text-xs text-slate-500 italic leading-relaxed">
                            Belum ada catatan persetujuan dari User Prinsiple.
                        </div>
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
                                @foreach($principles as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }} - Manager Area</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Yes / No</label>
                            <select name="statusapprove" class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:ring-4 focus:ring-primary-100 focus:border-primary-600 outline-none" required>
                                <option value="" selected disabled>Pilih Hasil Approval</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
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
                            <button type="button" class="w-full py-3 rounded-xl text-xs font-bold bg-rose-600 hover:bg-rose-700 text-white shadow-md shadow-rose-600/25 transition-all flex items-center justify-center gap-2">
                                <i class="fa-solid fa-paper-plane text-xs"></i>
                                <span>Set & Send To User Prinsiple</span>
                            </button>
                        </div>
                    </form>
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
@endsection