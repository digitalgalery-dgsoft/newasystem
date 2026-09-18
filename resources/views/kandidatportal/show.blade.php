@extends('layouts.app')

@section('title', 'Detail & Evaluasi Kandidat - ' . $candidate->full_name)

@section('content')
<div class="space-y-6" x-data="kandidatDetailManager()">

    <!-- TOP BAR / BREADCRUMB & HEADER -->
    <div class="page-header-card flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('kandidatportal.index') }}" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition-all">
                    <i class="fa-solid fa-arrow-left text-sm"></i>
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">{{ $candidate->full_name }}</h1>
                        <span class="badge-pill {{ $candidate->ai_badge_class }}">
                            <i class="fa-solid fa-bolt text-[9px]"></i> AI Match: {{ $candidate->ai_score ?? 0 }}%
                        </span>
                        @if($candidate->kategori_kandidat === 'Green')
                            <span class="badge-pill bg-emerald-50 text-emerald-700 border-emerald-200">🟢 Green</span>
                        @elseif($candidate->kategori_kandidat === 'Yellow')
                            <span class="badge-pill bg-amber-50 text-amber-700 border-amber-200">🟡 Yellow</span>
                        @elseif($candidate->kategori_kandidat === 'Red')
                            <span class="badge-pill bg-rose-50 text-rose-700 border-rose-200">🔴 Red</span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">
                        Posisi: <span class="font-bold text-slate-700">{{ $candidate->applied_job ?? 'Belum Ditentukan' }}</span> • Area: <span class="font-bold text-slate-700">{{ $candidate->area ?? 'JAKARTA' }}</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- ACTION BUTTONS -->
        <div class="flex flex-wrap items-center gap-2">
            <!-- Alihkan ke AS -->
            <button @click="alihkanModalOpen = true" type="button" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 shadow-sm transition-all">
                <i class="fa-solid fa-user-gear text-primary"></i>
                <span>Alihkan ke AS</span>
            </button>

            <!-- Ganti Area -->
            <button @click="gantiAreaModalOpen = true" type="button" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 shadow-sm transition-all">
                <i class="fa-solid fa-location-dot text-amber-600"></i>
                <span>Ganti Area</span>
            </button>

            <!-- Arsipkan -->
            <button @click="arsipModalOpen = true" type="button" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 shadow-sm transition-all">
                <i class="fa-solid fa-box-archive"></i>
                <span>Arsipkan</span>
            </button>

            <!-- Download PDF -->
            <a href="{{ route('interview.pdf', $candidate->id) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white bg-primary hover:bg-primary-700 shadow-md shadow-primary-500/20 transition-all">
                <i class="fa-solid fa-file-pdf"></i>
                <span>Download All Document</span>
            </a>
        </div>
    </div>

    <!-- PROFIL KANDIDAT CARD (11 DATA POINTS + FOTO DROPZONE) -->
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
                        <span class="font-bold text-slate-900">{{ $candidate->applied_job ?? '-' }}</span>
                    </div>

                    <!-- 9. Status Seleksi -->
                    <div class="flex items-start gap-2">
                        <span class="w-32 text-slate-400 font-medium flex-shrink-0">Status Seleksi:</span>
                        <span class="badge-pill bg-blue-50 text-blue-700 border-blue-200">
                            {{ $candidate->status_kandidat ?? 'Baru' }}
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

                    <!-- 12. Catatan / Gaji -->
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

    <!-- 7 NAVIGATION TABS CONTAINER -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden" x-data="{ activeTab: 'interview' }">
        
        <!-- Tab Navigation Bar -->
        <div class="border-b border-slate-200 bg-slate-50/70 px-4 sm:px-6 pt-3 flex items-center gap-2 overflow-x-auto">
            <!-- 1. Hasil Interview -->
            <button @click="activeTab = 'interview'" 
                    type="button" 
                    class="px-4 py-2.5 text-xs font-bold border-b-2 transition-all flex items-center gap-2 whitespace-nowrap"
                    :class="activeTab === 'interview' ? 'border-primary text-primary bg-white rounded-t-xl' : 'border-transparent text-slate-500 hover:text-slate-800'">
                <i class="fa-solid fa-clipboard-user"></i>
                <span>1. Hasil Interview</span>
            </button>

            <!-- 2. Referensi Cek -->
            <button @click="activeTab = 'refcek'" 
                    type="button" 
                    class="px-4 py-2.5 text-xs font-bold border-b-2 transition-all flex items-center gap-2 whitespace-nowrap"
                    :class="activeTab === 'refcek' ? 'border-primary text-primary bg-white rounded-t-xl' : 'border-transparent text-slate-500 hover:text-slate-800'">
                <i class="fa-solid fa-building-user"></i>
                <span>2. Referensi Cek</span>
            </button>

            <!-- 3. Tes Komputer -->
            <button @click="activeTab = 'komputer'" 
                    type="button" 
                    class="px-4 py-2.5 text-xs font-bold border-b-2 transition-all flex items-center gap-2 whitespace-nowrap"
                    :class="activeTab === 'komputer' ? 'border-primary text-primary bg-white rounded-t-xl' : 'border-transparent text-slate-500 hover:text-slate-800'">
                <i class="fa-solid fa-laptop-code"></i>
                <span>3. Tes Komputer</span>
            </button>

            <!-- 4. Tes Kepribadian -->
            <button @click="activeTab = 'kepribadian'" 
                    type="button" 
                    class="px-4 py-2.5 text-xs font-bold border-b-2 transition-all flex items-center gap-2 whitespace-nowrap"
                    :class="activeTab === 'kepribadian' ? 'border-primary text-primary bg-white rounded-t-xl' : 'border-transparent text-slate-500 hover:text-slate-800'">
                <i class="fa-solid fa-brain"></i>
                <span>4. Tes Kepribadian (DISC)</span>
            </button>

            <!-- 5. Tes Matematika -->
            <button @click="activeTab = 'matematika'" 
                    type="button" 
                    class="px-4 py-2.5 text-xs font-bold border-b-2 transition-all flex items-center gap-2 whitespace-nowrap"
                    :class="activeTab === 'matematika' ? 'border-primary text-primary bg-white rounded-t-xl' : 'border-transparent text-slate-500 hover:text-slate-800'">
                <i class="fa-solid fa-calculator"></i>
                <span>5. Tes Matematika</span>
            </button>

            <!-- 6. Analisa AI (CV Analyzer) (Tab No. 6 Sesuai Permintaan User!) -->
            <button @click="activeTab = 'ai'" 
                    type="button" 
                    class="px-4 py-2.5 text-xs font-bold border-b-2 transition-all flex items-center gap-2 whitespace-nowrap"
                    :class="activeTab === 'ai' ? 'border-primary text-primary bg-white rounded-t-xl' : 'border-transparent text-primary/80 hover:text-primary'">
                <i class="fa-solid fa-wand-magic-sparkles text-amber-500"></i>
                <span>6. Analisa AI (CV Analyzer)</span>
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            </button>

            <!-- 7. User Prinsiple -->
            <button @click="activeTab = 'userprinsiple'" 
                    type="button" 
                    class="px-4 py-2.5 text-xs font-bold border-b-2 transition-all flex items-center gap-2 whitespace-nowrap"
                    :class="(activeTab === 'userprinsiple' || activeTab === 'prinsiple') ? 'border-primary text-primary bg-white rounded-t-xl' : 'border-transparent text-slate-500 hover:text-slate-800'">
                <i class="fa-solid fa-building-shield"></i>
                <span>7. User Prinsiple</span>
            </button>
        </div>

        <!-- Tab Content Panes -->
        <div class="p-6">
            
            <!-- ============================================== -->
            <!-- TAB 1: HASIL INTERVIEW                         -->
            <!-- ============================================== -->
            <div x-show="activeTab === 'interview'" class="space-y-6">
                <form action="{{ route('kandidatportal.interview', $candidate->id) }}" method="POST" id="interviewForm" class="space-y-6">
                    @csrf
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs custom-table">
                            <thead>
                                <tr>
                                    <th class="w-64">Kriteria Penilaian</th>
                                    <th class="text-center w-36">Sangat Baik</th>
                                    <th class="text-center w-36">Baik</th>
                                    <th class="text-center w-36">Cukup</th>
                                    <th class="text-center w-36">Kurang</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @php
                                    $assess = $candidate->interviewAssessment;
                                    $criteria = [
                                        'work_willingness' => 'Kemauan Kerja',
                                        'appearance' => 'Penampilan',
                                        'attitude' => 'Attitude / Sikap',
                                        'comprehension' => 'Daya Tangkap',
                                    ];
                                @endphp
                                @foreach($criteria as $field => $label)
                                <tr>
                                    <td class="font-bold text-slate-800">{{ $label }}</td>
                                    @foreach(['Sangat Baik', 'Baik', 'Cukup', 'Kurang'] as $opt)
                                    <td class="text-center">
                                        <label class="inline-flex items-center justify-center p-1.5 cursor-pointer">
                                            <input type="radio" name="{{ $field }}" value="{{ $opt }}" 
                                                   {{ ($assess && $assess->$field === $opt) || (!$assess && $opt === 'Baik') ? 'checked' : '' }}
                                                   class="w-4 h-4 text-primary focus:ring-primary-500">
                                        </label>
                                    </td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Additional Details -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Wawancara</label>
                            <input type="date" name="interview_date" value="{{ $assess && $assess->interview_date ? $assess->interview_date->format('Y-m-d') : date('Y-m-d') }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-medium text-slate-700">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Kategori AI / Rekomendasi</label>
                            <select name="kategori_kandidat" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-medium text-slate-700 bg-white">
                                <option value="Green" {{ $candidate->kategori_kandidat === 'Green' ? 'selected' : '' }}>🟢 Green</option>
                                <option value="Yellow" {{ $candidate->kategori_kandidat === 'Yellow' ? 'selected' : '' }}>🟡 Yellow</option>
                                <option value="Red" {{ $candidate->kategori_kandidat === 'Red' ? 'selected' : '' }}>🔴 Red</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Status Tahapan Kandidat</label>
                            <select name="status_kandidat" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-medium text-slate-700 bg-white">
                                <option value="Baru" {{ $candidate->status_kandidat === 'Baru' ? 'selected' : '' }}>Baru</option>
                                <option value="Interview" {{ $candidate->status_kandidat === 'Interview' ? 'selected' : '' }}>Interview</option>
                                <option value="Terima" {{ $candidate->status_kandidat === 'Terima' ? 'selected' : '' }}>Terima</option>
                                <option value="Arsip" {{ $candidate->status_kandidat === 'Arsip' ? 'selected' : '' }}>Arsip</option>
                            </select>
                        </div>

                        <div class="md:col-span-3">
                            <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Lain-lain</label>
                            <textarea name="notes" rows="3" placeholder="Catatan hasil wawancara tatap muka..." class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-medium text-slate-700">{{ $assess ? $assess->notes : '' }}</textarea>
                        </div>
                    </div>

                    <!-- Canvas Tanda Tangan -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tanda Tangan Asessor / HRD</label>
                        <div class="border-2 border-dashed border-slate-300 rounded-xl p-2 bg-slate-50/50 inline-block">
                            <canvas id="signaturePad" width="360" height="130" class="bg-white rounded-lg border border-slate-200 shadow-inner"></canvas>
                            <div class="flex items-center justify-between mt-2 px-1">
                                <button type="button" onclick="clearSignature()" class="text-[11px] font-bold text-rose-600 hover:text-rose-700">
                                    <i class="fa-solid fa-eraser mr-1"></i> Hapus Tanda Tangan
                                </button>
                                <span class="text-[10px] text-slate-400">Gunakan mouse atau touchscreen</span>
                            </div>
                        </div>
                        <input type="hidden" name="assessor_signature" id="signatureData" value="{{ $assess ? $assess->assessor_signature : '' }}">
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100">
                        <button type="submit" onclick="saveSignatureBeforeSubmit()" class="px-5 py-2.5 rounded-xl bg-primary hover:bg-primary-700 text-white text-xs font-bold transition-all shadow-md shadow-primary-500/20 flex items-center gap-2">
                            <i class="fa-solid fa-floppy-disk"></i>
                            <span>Simpan Hasil Wawancara</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- ============================================== -->
            <!-- TAB 2: REFERENSI CEK                           -->
            <!-- ============================================== -->
            <div x-show="activeTab === 'refcek'" class="space-y-6">
                <form action="{{ route('kandidatportal.refcek', $candidate->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    
                    @php
                        $firstExp = $candidate->workExperiences->first();
                    @endphp

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Left: Form Data SPV & Perilaku -->
                        <div class="space-y-4">
                            <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                                <i class="fa-solid fa-user-check text-primary"></i>
                                <span>Penilaian Supervisor / Atasan Sebelumnya</span>
                            </h4>

                            <div class="space-y-3 text-xs">
                                <!-- Perusahaan Sebelumnya Dropdown (Sesuai Permintaan User!) -->
                                <div>
                                    <label class="block text-slate-700 font-bold mb-1">
                                        Perusahaan Sebelumnya <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <select name="company_id" id="companySelect" onchange="handleCompanySelect(this.value)" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 font-semibold text-slate-800 focus:ring-2 focus:ring-primary focus:border-transparent bg-white shadow-xs">
                                            @if($candidate->workExperiences->count() > 0)
                                                @foreach($candidate->workExperiences as $we)
                                                    <option value="{{ $we->id }}" {{ $loop->first ? 'selected' : '' }}>
                                                        {{ $we->company_name }} @if(!empty($we->position)) ({{ $we->position }}) @endif
                                                    </option>
                                                @endforeach
                                                <option value="new">+ Tambah Riwayat Perusahaan Baru</option>
                                            @else
                                                <option value="new" selected>+ Input Nama Perusahaan Pengalaman Kerja</option>
                                            @endif
                                        </select>
                                    </div>
                                    <p class="text-[10px] text-slate-400 mt-1">Pilih dari data riwayat pekerjaan yang diinputkan dibagian Pengalaman Kerja kandidat</p>
                                </div>

                                <!-- Manual Company Name input if 'new' is selected or no experiences exist -->
                                <div id="customCompanyWrapper" style="{{ $candidate->workExperiences->count() > 0 ? 'display: none;' : '' }}">
                                    <label class="block text-slate-700 font-bold mb-1">Nama Perusahaan Baru</label>
                                    <input type="text" name="company_name" id="customCompanyName" placeholder="Masukkan nama perusahaan..." value="{{ $firstExp?->company_name ?? '' }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 font-semibold text-slate-800 focus:ring-2 focus:ring-primary focus:border-transparent">
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-slate-600 font-bold mb-1">Nama SPV / Referensi <span class="text-rose-500">*</span></label>
                                        <input type="text" name="supervisor_name" id="refcek_spv" value="{{ $firstExp?->supervisor_name ?? '-' }}" required class="w-full px-3 py-2 rounded-xl border border-slate-300 font-medium text-slate-800 focus:ring-2 focus:ring-primary focus:border-transparent">
                                    </div>
                                    <div>
                                        <label class="block text-slate-600 font-bold mb-1">Telp Perusahaan / SPV <span class="text-rose-500">*</span></label>
                                        <input type="text" name="company_phone" id="refcek_phone" value="{{ $firstExp?->company_phone ?? '-' }}" required class="w-full px-3 py-2 rounded-xl border border-slate-300 font-medium text-slate-800 focus:ring-2 focus:ring-primary focus:border-transparent">
                                    </div>
                                </div>

                                <div class="grid grid-cols-3 gap-2">
                                    <div>
                                        <label class="block text-slate-600 font-bold mb-1">Performa</label>
                                        <input type="text" name="performance_review" id="refcek_performance" value="{{ $firstExp?->performance_notes ?? 'Baik' }}" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 text-xs font-medium text-slate-800">
                                    </div>
                                    <div>
                                        <label class="block text-slate-600 font-bold mb-1">Disiplin</label>
                                        <input type="text" name="discipline_review" id="refcek_discipline" value="{{ $firstExp?->discipline_notes ?? 'Tepat Waktu' }}" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 text-xs font-medium text-slate-800">
                                    </div>
                                    <div>
                                        <label class="block text-slate-600 font-bold mb-1">Tanggung Jawab</label>
                                        <input type="text" name="responsibility_review" id="refcek_responsibility" value="{{ $firstExp?->responsibility_notes ?? 'Bertanggung Jawab' }}" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 text-xs font-medium text-slate-800">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-slate-600 font-bold mb-1">Keunggulan Utama (Strengths)</label>
                                    <textarea name="strengths_identified" id="refcek_strengths" rows="2" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-800 focus:ring-2 focus:ring-primary focus:border-transparent">{{ $firstExp?->strengths ?? 'Problem solving cepat dan komunikatif.' }}</textarea>
                                </div>

                                <div>
                                    <label class="block text-slate-600 font-bold mb-1">Kelemahan (Weaknesses)</label>
                                    <textarea name="weaknesses_identified" id="refcek_weaknesses" rows="2" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-800 focus:ring-2 focus:ring-primary focus:border-transparent">{{ $firstExp?->weaknesses ?? 'Terkadang terlalu detail.' }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Right: Bukti Chat WhatsApp Verifikasi -->
                        <div class="space-y-4">
                            <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                                <i class="fa-brands fa-whatsapp text-emerald-600"></i>
                                <span>Bukti Screenshot Chat Verifikasi (WA)</span>
                            </h4>

                            <div class="p-6 rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 text-center flex flex-col items-center justify-center hover:border-emerald-400 transition-all">
                                <i class="fa-solid fa-cloud-arrow-up text-3xl text-emerald-600 mb-2"></i>
                                <span class="text-xs font-bold text-slate-800">Unggah Tangkapan Layar Chat Verifikasi</span>
                                <span class="text-[11px] text-slate-400 mt-1">Format: JPG, PNG maksimal 3MB</span>
                                
                                <label class="mt-3 cursor-pointer px-4 py-2 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold hover:bg-emerald-100 transition-colors inline-flex items-center gap-1.5">
                                    <i class="fa-solid fa-paperclip"></i>
                                    <span>Pilih File Screenshot</span>
                                    <input type="file" name="proof_file" accept="image/*,.pdf" class="hidden" onchange="previewRefcekProof(this)">
                                </label>
                                <span id="refcek_filename" class="text-[11px] font-mono text-emerald-700 mt-2"></span>
                            </div>

                            <!-- Current Uploaded Proof Preview -->
                            <div id="refcek_current_proof" class="p-3 bg-white rounded-xl border border-slate-200 flex items-center justify-between shadow-2xs">
                                @if($firstExp && ($firstExp->proof_attachment_path || $firstExp->proof_url))
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <div class="w-12 h-12 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden flex-shrink-0 cursor-pointer shadow-xs group" onclick="openCandidateMedia('image', '{{ $firstExp->proof_url }}', 'Bukti Referensi Cek: {{ addslashes($firstExp->company_name) }}')">
                                            <img src="{{ $firstExp->proof_url }}" alt="Bukti Refcek" class="w-full h-full object-cover transition-transform group-hover:scale-105" onerror="this.onerror=null; this.src='{{ $firstExp->proof_legacy_url }}';">
                                        </div>
                                        <div class="min-w-0">
                                            <span class="text-xs font-bold text-slate-800 truncate block">{{ basename($firstExp->proof_attachment_path) }}</span>
                                            <span class="text-[10px] text-emerald-600 font-semibold block">Bukti Verifikasi Terunggah (Live Fallback V3)</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1.5 flex-shrink-0">
                                        <button type="button" onclick="openCandidateMedia('image', '{{ $firstExp->proof_url }}', 'Bukti Referensi Cek: {{ addslashes($firstExp->company_name) }}')" class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 rounded-lg text-[11px] font-bold transition-colors inline-flex items-center gap-1">
                                            <i class="fa-solid fa-eye text-[10px]"></i> Preview
                                        </button>
                                        <a href="{{ $firstExp->proof_url }}" target="_blank" class="px-2.5 py-1 bg-white border border-slate-300 rounded-lg text-[11px] font-bold text-primary hover:bg-slate-100 transition-colors inline-flex items-center gap-1">
                                            <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i> Buka
                                        </a>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 italic">Belum ada tangkapan layar verifikasi yang disimpan</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- SUBMIT BUTTON FOR REFERENSI CEK -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-primary hover:bg-primary-700 text-white text-xs font-bold transition-all shadow-md shadow-primary-500/20 flex items-center gap-2">
                            <i class="fa-solid fa-floppy-disk"></i>
                            <span>Simpan Referensi Cek</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- ============================================== -->
            <!-- TAB 3: TES KOMPUTER                            -->
            <!-- ============================================== -->
            <div x-show="activeTab === 'komputer'" class="space-y-6">
                <form action="{{ route('kandidatportal.kompt', $candidate->id) }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Hasil Uji Kompetensi Spreadsheet / Excel</h4>
                            <p class="text-[11px] text-slate-500 mt-0.5">
                                Waktu Pengerjaan: <span class="font-bold text-slate-800">{{ $komptDuration ?? '00:03:10' }}</span> • 
                                Status: <span class="font-bold text-emerald-600">{{ $komptSummaryLabel ?? 'Telah Diisi' }}</span>
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                        <!-- Left: Table of skills -->
                        <div class="lg:col-span-8 overflow-x-auto">
                            <table class="w-full text-xs custom-table">
                                <thead>
                                    <tr>
                                        <th>Keahlian Komputer</th>
                                        <th class="text-center w-28">Baik</th>
                                        <th class="text-center w-28">Cukup</th>
                                        <th class="text-center w-28">Kurang</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @php
                                        $compSkillsMap = $compSkills ?? [
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
                                    @endphp
                                    @foreach($compSkillsMap as $sk => $label)
                                    @php
                                        $val = $savedComp[$sk] ?? 'Baik';
                                    @endphp
                                    <tr>
                                        <td class="font-bold text-slate-800">{{ $label }}</td>
                                        @foreach(['Baik', 'Cukup', 'Kurang'] as $v)
                                        <td class="text-center">
                                            <label class="inline-flex items-center justify-center cursor-pointer p-1">
                                                <input type="radio" name="{{ $sk }}" value="{{ $v }}" {{ strtolower($val) === strtolower($v) ? 'checked' : '' }} class="w-4 h-4 text-primary focus:ring-primary">
                                            </label>
                                        </td>
                                        @endforeach
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Right: Bukti Pengerjaan -->
                        <div class="lg:col-span-4 bg-slate-50 p-4 rounded-2xl border border-slate-200 text-center space-y-3">
                            <h5 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Bukti Pengerjaan Komputer</h5>
                            @php
                                $buktiKompt = $candidate->buktikomputer;
                                $buktiPath = $buktiKompt ? public_path('lampiran/' . $buktiKompt) : null;
                                $buktiUrl = $buktiKompt ? asset('lampiran/' . $buktiKompt) : null;
                            @endphp
                            @if(!empty($buktiKompt) && file_exists($buktiPath))
                                <a href="{{ $buktiUrl }}" target="_blank" class="block group">
                                    <img src="{{ $buktiUrl }}" alt="Bukti Tes Komputer" class="w-full max-h-48 object-contain rounded-xl border border-slate-300 shadow-sm group-hover:opacity-90 transition-opacity">
                                    <span class="text-[10px] text-primary font-bold mt-1.5 inline-block">Klik untuk memperbesar</span>
                                </a>
                            @else
                                <div class="h-36 flex flex-col items-center justify-center rounded-xl border border-dashed border-slate-300 bg-white text-slate-400 p-4">
                                    <i class="fa-solid fa-file-excel text-2xl mb-1 text-slate-300"></i>
                                    <span class="text-[11px] font-semibold">Bukti Belum Diunggah</span>
                                    <span class="text-[9px] text-slate-400 mt-0.5">Kandidat belum mengunggah file hasil tes</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- SUBMIT BUTTON FOR TES KOMPUTER -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-primary hover:bg-primary-700 text-white text-xs font-bold transition-all shadow-md shadow-primary-500/20 flex items-center gap-2">
                            <i class="fa-solid fa-floppy-disk"></i>
                            <span>Simpan Nilai Tes Komputer</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- ============================================== -->
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
            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-8 text-center space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-sky-100 text-sky-600 flex items-center justify-center mx-auto text-xl">
                    <i class="fa-solid fa-calculator"></i>
                </div>
                <h4 class="text-sm font-bold text-slate-800">Kandidat Belum Mengikuti Tes Matematika</h4>
                <p class="text-xs text-slate-500 max-w-md mx-auto">
                    Kandidat belum menyelesaikan Tes Matematika. Data nilai, rincian benar/salah, dan pembahasan butir soal akan otomatis terisi setelah kandidat menyelesaikan ujian.
                </p>
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
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-primary to-indigo-600 text-white flex items-center justify-center text-xl shadow-md shadow-primary-500/20 flex-shrink-0">
                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900">AI CV Analyzer Recommendation</h3>
                        <p class="text-[11px] text-slate-500">Hasil evaluasi komprehensif profil kandidat & kesesuaian requirement lowongan</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 flex-wrap">
                    <!-- Download PDF Button (Sesuai Skrip Aslinya!) -->
                    <a href="{{ route('kandidatportal.cetak-ai', $candidate->id) }}" target="_blank" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition-all shadow-sm shadow-rose-600/20 flex items-center gap-2">
                        <i class="fa-solid fa-file-pdf text-sm"></i>
                        <span>Download PDF</span>
                    </a>

                    <button type="button" @click="alert('Memulai evaluasi ulang berkas CV kandidat...')" class="px-3.5 py-2 rounded-xl bg-white border border-slate-300 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-all shadow-sm flex items-center gap-1.5">
                        <i class="fa-solid fa-arrows-rotate text-primary"></i>
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
                        <h4 class="text-base font-extrabold text-primary mb-3">
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
                        <span>Status Seleksi: <strong class="text-slate-700">{{ $candidate->status_kandidat ?? 'Job Portal' }}</strong></span>
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
                            <span class="w-1.5 h-1.5 rounded-full bg-primary mt-1.5 flex-shrink-0"></span>
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
                <button type="button" @click="alert('Memulai analisa AI CV...')" class="px-5 py-2.5 rounded-xl bg-primary hover:bg-primary-700 text-white text-xs font-bold transition-all shadow-md shadow-primary-500/20 inline-flex items-center gap-2">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                    <span>Analisa CV Sekarang</span>
                </button>
            </div>
            @endif
        </div>

        <!-- ============================================================= -->
        <!-- TAB 7: USER PRINCIPLE (Matching Legacy App)                   -->
        <!-- ============================================================= -->
        <div x-show="activeTab === 'userprinsiple' || activeTab === 'prinsiple'" class="space-y-6">
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
                            <button type="submit" class="w-full py-3 rounded-xl text-xs font-bold bg-rose-600 hover:bg-rose-700 text-white shadow-md shadow-rose-600/25 transition-all flex items-center justify-center gap-2">
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

    <!-- MODAL ALIKAN KE AS -->
    <div x-show="alihkanModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 border border-slate-200 space-y-4" @click.away="alihkanModalOpen = false">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h4 class="text-sm font-extrabold text-slate-900">Alihkan Data Kandidat ke AS</h4>
                <button @click="alihkanModalOpen = false" class="text-slate-400 hover:text-slate-600 text-base">✕</button>
            </div>
            <form action="{{ route('kandidatportal.alihkan', $candidate->id) }}" method="POST" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block text-slate-700 font-bold mb-1">Prinsiple</label>
                    <select name="prinsiple_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white text-xs">
                        @foreach($principles as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-slate-700 font-bold mb-1">Nama AS / AE</label>
                    <input type="text" name="useras" value="admin.pusat@arina.co.id" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold">
                </div>
                <div>
                    <label class="block text-slate-700 font-bold mb-1">Catatan untuk AS</label>
                    <textarea name="notes" rows="3" placeholder="Tuliskan catatan alokasi..." class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs"></textarea>
                </div>
                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="alihkanModalOpen = false" class="px-3 py-2 rounded-xl border border-slate-300 font-semibold">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-primary text-white font-bold">Kirim Pengalihan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL GANTI AREA -->
    <div x-show="gantiAreaModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6 border border-slate-200 space-y-4" @click.away="gantiAreaModalOpen = false">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h4 class="text-sm font-extrabold text-slate-900">Ganti Area Penempatan</h4>
                <button @click="gantiAreaModalOpen = false" class="text-slate-400 hover:text-slate-600 text-base">✕</button>
            </div>
            <form action="{{ route('kandidatportal.ganti_area', $candidate->id) }}" method="POST" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block text-slate-700 font-bold mb-1">Pilih Area Baru</label>
                    <select name="area" class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white text-xs">
                        @foreach($areas as $a)
                            <option value="{{ $a }}" {{ ($candidate->area ?? '') === $a ? 'selected' : '' }}>{{ $a }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="gantiAreaModalOpen = false" class="px-3 py-2 rounded-xl border border-slate-300 font-semibold">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-amber-600 text-white font-bold">Simpan Perubahan Area</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL ARSIP -->
    <div x-show="arsipModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6 border border-slate-200 space-y-4" @click.away="arsipModalOpen = false">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h4 class="text-sm font-extrabold text-rose-700">Arsipkan Kandidat</h4>
                <button @click="arsipModalOpen = false" class="text-slate-400 hover:text-slate-600 text-base">✕</button>
            </div>
            <form action="{{ route('kandidatportal.arsipkan', $candidate->id) }}" method="POST" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block text-slate-700 font-bold mb-1">Alasan Pengarsipan</label>
                    <textarea name="alasan" required rows="3" placeholder="Misal: Tidak hadir interview / Kualifikasi tidak sesuai..." class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs"></textarea>
                </div>
                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="arsipModalOpen = false" class="px-3 py-2 rounded-xl border border-slate-300 font-semibold">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 text-white font-bold">Ya, Arsipkan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- CANDIDATE MEDIA PREVIEW MODAL -->
    @include('partials.candidate-media-modal')

</div>
@endsection

@section('scripts')
<script>
    function kandidatDetailManager() {
        return {
            alihkanModalOpen: false,
            gantiAreaModalOpen: false,
            arsipModalOpen: false,
        };
    }

    // HTML5 Canvas Signature Pad
    let canvas = document.getElementById('signaturePad');
    let ctx = canvas ? canvas.getContext('2d') : null;
    let isDrawing = false;
    let lastX = 0;
    let lastY = 0;

    if (canvas && ctx) {
        // Init background
        ctx.strokeStyle = '#0F52BA';
        ctx.lineWidth = 2.5;
        ctx.lineCap = 'round';

        const getPos = (e) => {
            const rect = canvas.getBoundingClientRect();
            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const clientY = e.touches ? e.touches[0].clientY : e.clientY;
            return { x: clientX - rect.left, y: clientY - rect.top };
        };

        const startDraw = (e) => {
            isDrawing = true;
            const p = getPos(e);
            lastX = p.x;
            lastY = p.y;
            if (e.touches) e.preventDefault();
        };

        const draw = (e) => {
            if (!isDrawing) return;
            const p = getPos(e);
            ctx.beginPath();
            ctx.moveTo(lastX, lastY);
            ctx.lineTo(p.x, p.y);
            ctx.stroke();
            lastX = p.x;
            lastY = p.y;
            if (e.touches) e.preventDefault();
        };

        const stopDraw = () => { isDrawing = false; };

        canvas.addEventListener('mousedown', startDraw);
        canvas.addEventListener('mousemove', draw);
        window.addEventListener('mouseup', stopDraw);

        canvas.addEventListener('touchstart', startDraw, { passive: false });
        canvas.addEventListener('touchmove', draw, { passive: false });
        window.addEventListener('touchend', stopDraw);
    }

    function clearSignature() {
        if (canvas && ctx) {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            document.getElementById('signatureData').value = '';
        }
    }

    function saveSignatureBeforeSubmit() {
        if (canvas) {
            const dataUrl = canvas.toDataURL('image/png');
            document.getElementById('signatureData').value = dataUrl;
        }
    }

    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('photoPreview');
                const placeholder = document.getElementById('photoPlaceholder');
                if (img) {
                    img.src = e.target.result;
                } else if (placeholder) {
                    placeholder.parentElement.innerHTML = `<img id="photoPreview" src="${e.target.result}" alt="Foto Baru" class="w-full h-full object-cover">`;
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
            document.getElementById('refcek_spv').value = '';
            document.getElementById('refcek_phone').value = '';
            document.getElementById('refcek_performance').value = 'Baik';
            document.getElementById('refcek_discipline').value = 'Tepat Waktu';
            document.getElementById('refcek_responsibility').value = 'Bertanggung Jawab';
            document.getElementById('refcek_strengths').value = '';
            document.getElementById('refcek_weaknesses').value = '';
            updateProofPreview(null);
            return;
        }

        if (customWrapper) customWrapper.style.display = 'none';

        const exp = candidateWorkExps.find(e => e.id == val);
        if (exp) {
            document.getElementById('refcek_spv').value = exp.supervisor_name || '';
            document.getElementById('refcek_phone').value = exp.company_phone || '';
            document.getElementById('refcek_performance').value = exp.performance_notes || exp.performa || 'Baik';
            document.getElementById('refcek_discipline').value = exp.discipline_notes || exp.disiplin || 'Tepat Waktu';
            document.getElementById('refcek_responsibility').value = exp.responsibility_notes || exp.tanggungjawab || 'Bertanggung Jawab';
            document.getElementById('refcek_strengths').value = exp.strengths || exp.streng || '';
            document.getElementById('refcek_weaknesses').value = exp.weaknesses || exp.week || '';
            updateProofPreview(exp);
        } else {
            updateProofPreview(null);
        }
    }

    function updateProofPreview(exp) {
        const container = document.getElementById('refcek_current_proof');
        if (!container) return;
        if (exp && exp.proof_url) {
            const fname = (exp.proof_attachment_path || '').split('/').pop().split('\\').pop();
            const safeName = (exp.company_name || '').replace(/'/g, "\\'");
            container.innerHTML = `
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-12 h-12 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden flex-shrink-0 cursor-pointer shadow-xs group" onclick="openCandidateMedia('image', '${exp.proof_url}', 'Bukti Referensi Cek: ${safeName}')">
                        <img src="${exp.proof_url}" alt="Bukti Refcek" class="w-full h-full object-cover transition-transform group-hover:scale-105" onerror="this.onerror=null; this.src='${exp.proof_legacy_url || ''}';">
                    </div>
                    <div class="min-w-0">
                        <span class="text-xs font-bold text-slate-800 truncate block">${fname}</span>
                        <span class="text-[10px] text-emerald-600 font-semibold block">Bukti Verifikasi Terunggah (Live Fallback V3)</span>
                    </div>
                </div>
                <div class="flex items-center gap-1.5 flex-shrink-0">
                    <button type="button" onclick="openCandidateMedia('image', '${exp.proof_url}', 'Bukti Referensi Cek: ${safeName}')" class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 rounded-lg text-[11px] font-bold transition-colors inline-flex items-center gap-1">
                        <i class="fa-solid fa-eye text-[10px]"></i> Preview
                    </button>
                    <a href="${exp.proof_url}" target="_blank" class="px-2.5 py-1 bg-white border border-slate-300 rounded-lg text-[11px] font-bold text-primary hover:bg-slate-100 transition-colors inline-flex items-center gap-1">
                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i> Buka
                    </a>
                </div>
            `;
        } else {
            container.innerHTML = `<span class="text-xs text-slate-400 italic">Belum ada tangkapan layar verifikasi yang disimpan</span>`;
        }
    }

    function previewRefcekProof(input) {
        if (input.files && input.files[0]) {
            const label = document.getElementById('refcek_filename');
            if (label) {
                label.textContent = 'File dipilih: ' + input.files[0].name;
            }
        }
    }
</script>
@endsection