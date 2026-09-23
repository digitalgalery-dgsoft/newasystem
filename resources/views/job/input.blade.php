@extends('layouts.app')

@section('title', 'Input Job Requirement - Attendance Admin Portal')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<style>
    .note-editor.note-frame {
        border: 1px solid #cbd5e1 !important;
        border-radius: 0.75rem !important;
        overflow: hidden;
        background: #ffffff;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.04);
    }
    .note-toolbar {
        background-color: #f8fafc !important;
        border-bottom: 1px solid #e2e8f0 !important;
        padding: 5px 8px !important;
    }
    .note-btn {
        border-radius: 0.375rem !important;
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        color: #334155 !important;
        padding: 3px 8px !important;
        font-size: 11px !important;
    }
    .note-btn:hover {
        background: #f1f5f9 !important;
        color: #0f172a !important;
    }
    .note-editable {
        font-size: 12px !important;
        line-height: 1.6 !important;
        color: #1e293b !important;
        min-height: 90px !important;
        font-family: inherit !important;
        background: #ffffff;
    }
    .note-placeholder {
        font-size: 12px !important;
        color: #94a3b8 !important;
    }
</style>

<div class="space-y-6" x-data="jobManager()">
    
    <!-- PAGE TITLE & HEADER CARD -->
    <div class="page-header-card flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-xl bg-primary-50 border border-primary-200 flex items-center justify-center text-primary text-xl font-bold">
                    <i class="fa-solid fa-briefcase"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Input Job Requirement</h1>
                        @if($isAdmin)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-100 text-blue-800 border border-blue-200">
                                <i class="fa-solid fa-shield-halved text-[9px]"></i> Akses Administrator
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                <i class="fa-solid fa-user-check text-[9px]"></i> Pembuat: {{ $user?->name ?? $user?->email }}
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">
                        @if($isAdmin)
                            Kelola seluruh posisi lowongan pekerjaan dari semua pembuat di sistem
                        @else
                            Kelola posisi lowongan pekerjaan yang Anda buat untuk kampanye rekrutmen Anda
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('interview.index') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition-all shadow-sm">
                <i class="fa-solid fa-users text-primary"></i>
                <span>Data Kandidat</span>
            </a>
            <button @click="openPresetModal()" type="button" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold text-white bg-primary hover:bg-primary-700 transition-all shadow-sm shadow-primary-500/20">
                <i class="fa-solid fa-wand-magic-sparkles"></i>
                <span>Preset & AI Helper</span>
            </button>
        </div>
    </div>

    <!-- STATS ROW -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-box flex items-center gap-4">
            <div class="stat-box-icon bg-blue-50 text-primary border border-blue-100">
                <i class="fa-solid fa-briefcase"></i>
            </div>
            <div>
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Job Aktif</div>
                <div class="text-2xl font-black text-slate-900 leading-tight">{{ $activeJobs->count() }}</div>
                <div class="text-[11px] font-semibold text-emerald-600 flex items-center gap-1 mt-0.5">
                    <i class="fa-solid fa-circle-check text-[9px]"></i> Siap melamar
                </div>
            </div>
        </div>

        <div class="stat-box flex items-center gap-4">
            <div class="stat-box-icon bg-rose-50 text-rose-600 border border-rose-100">
                <i class="fa-solid fa-business-time"></i>
            </div>
            <div>
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Job Expired</div>
                <div class="text-2xl font-black text-slate-900 leading-tight">{{ $expiredJobs->count() }}</div>
                <div class="text-[11px] font-semibold text-rose-600 flex items-center gap-1 mt-0.5">
                    <i class="fa-solid fa-clock-rotate-left text-[9px]"></i> Melewati deadline
                </div>
            </div>
        </div>

        <div class="stat-box flex items-center gap-4">
            <div class="stat-box-icon bg-amber-50 text-amber-600 border border-amber-100">
                <i class="fa-solid fa-building"></i>
            </div>
            <div>
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Prinsiple Terdaftar</div>
                <div class="text-2xl font-black text-slate-900 leading-tight">{{ $principles->count() }}</div>
                <div class="text-[11px] font-semibold text-slate-500 mt-0.5">Client & Partner</div>
            </div>
        </div>

        <div class="stat-box flex items-center gap-4">
            <div class="stat-box-icon bg-emerald-50 text-emerald-600 border border-emerald-100">
                <i class="fa-solid fa-map-location-dot"></i>
            </div>
            <div>
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Cakupan Area</div>
                <div class="text-2xl font-black text-slate-900 leading-tight">{{ count($areas) }}</div>
                <div class="text-[11px] font-semibold text-emerald-600 mt-0.5">Seluruh Indonesia</div>
            </div>
        </div>
    </div>

    <!-- MAIN TWO COLUMNS -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- ============================================== -->
        <!-- LEFT COLUMN: FORM INPUT / EDIT JOB (5 COLS)     -->
        <!-- ============================================== -->
        <div class="lg:col-span-5 space-y-4">
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                
                <!-- Card Header -->
                <div class="px-5 py-4 border-b border-slate-200 bg-slate-50/70 flex flex-wrap items-center justify-between gap-2.5">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg {{ $editData ? 'bg-amber-100 text-amber-700' : 'bg-primary-100 text-primary' }} flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid {{ $editData ? 'fa-pen-to-square' : 'fa-plus' }}"></i>
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-slate-800">{{ $editData ? 'Edit Spesifikasi Job' : 'Tambah Job Baru' }}</h2>
                            <p class="text-[11px] text-slate-500">
                                @if($editData)
                                    Dibuat oleh: <span class="font-semibold text-slate-700">{{ $editData->created_by ?: 'System' }}</span>
                                @else
                                    Dibuat sebagai: <span class="font-semibold text-primary">{{ $user?->email ?? $user?->name }}</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" 
                                id="btnGenerateAI" 
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-white bg-[#1e293b] hover:bg-slate-900 border border-slate-700 shadow-sm transition-all">
                            <i class="fa-solid fa-wand-magic-sparkles text-amber-400"></i>
                            <span>Generate Job by AI</span>
                        </button>

                        <button type="button" 
                                id="btnCopyPrompt" 
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-white bg-[#2563eb] hover:bg-blue-700 border border-blue-600 shadow-sm transition-all">
                            <i class="fa-regular fa-image"></i>
                            <span>Generate Image Prompt</span>
                        </button>

                        @if($editData)
                        <a href="{{ route('job.input') }}" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl text-xs font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition-all">
                            <i class="fa-solid fa-xmark"></i> Batal Edit
                        </a>
                        @endif
                    </div>
                </div>

                <!-- Fast Template Pills -->
                <div class="px-5 pt-3 pb-2 border-b border-slate-100 bg-slate-50/30">
                    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                        <i class="fa-solid fa-bolt text-amber-500"></i>
                        <span>Pilih Template Cepat:</span>
                    </div>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($templates as $tmpl)
                        <button type="button" 
                                @click="applyTemplate({{ json_encode($tmpl) }})"
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-white text-slate-700 border border-slate-200 hover:border-primary-500 hover:text-primary hover:bg-primary-50 transition-all">
                            <span>{{ $tmpl['label'] }}</span>
                        </button>
                        @endforeach
                    </div>
                </div>

                <!-- Form Body -->
                <form action="{{ route('job.store') }}" method="POST" id="jobForm" class="p-5 space-y-4">
                    @csrf
                    <input type="hidden" name="edit_id" value="{{ $editData ? $editData->id : 0 }}">

                    <!-- Akun Pembuat / Rekruter (Khusus Akses Administrator) -->
                    @if($isAdmin && isset($availableRecruiters) && $availableRecruiters->isNotEmpty())
                    <div class="bg-blue-50/50 border border-blue-200/70 rounded-xl p-3">
                        <label for="created_by" class="block text-xs font-bold text-slate-800 mb-1 flex items-center justify-between">
                            <span class="flex items-center gap-1.5">
                                <i class="fa-solid fa-user-pen text-primary"></i>
                                Pembuat Job / Akun Rekruter (User AS)
                            </span>
                            <span class="text-[10px] font-semibold text-blue-700 bg-blue-100/80 px-2 py-0.5 rounded-full">
                                Kontrol Admin
                            </span>
                        </label>
                        <select id="created_by" 
                                name="created_by" 
                                class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs font-semibold text-slate-800 bg-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                            @php
                                $selectedCreator = old('created_by', $editData ? $editData->created_by : ($user?->email ?? ''));
                            @endphp
                            @if($editData && $editData->created_by && !$availableRecruiters->contains('email', $editData->created_by))
                                <option value="{{ $editData->created_by }}" selected>
                                    {{ $editData->created_by }} (Kustom / Sebelumnya)
                                </option>
                            @endif
                            @foreach($availableRecruiters as $rec)
                                <option value="{{ $rec->email }}" {{ strcasecmp($selectedCreator, $rec->email) === 0 ? 'selected' : '' }}>
                                    {{ $rec->name }} ({{ $rec->email }}) {{ $rec->area ? '— Area ' . $rec->area : '' }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-slate-500 mt-1">
                            Pilih akun rekruter yang bertanggung jawab atas lowongan ini.
                        </p>
                    </div>
                    @endif

                    <!-- Posisi / Nama Jabatan -->
                    <div>
                        <label for="job_title" class="block text-xs font-bold text-slate-700 mb-1">
                            Posisi / Nama Jabatan <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" 
                                   id="job_title" 
                                   name="job_title" 
                                   required 
                                   value="{{ old('job_title', $editData->job_title ?? '') }}"
                                   placeholder="Contoh: Senior Fullstack Developer" 
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all placeholder:text-slate-400 font-semibold text-slate-800">
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">Nama posisi harus unik untuk lowongan yang sedang aktif.</p>
                    </div>

                    <!-- Prinsiple -->
                    <div>
                        <label for="job_prinsiple" class="block text-xs font-bold text-slate-700 mb-1">Prinsiple</label>
                        <select id="job_prinsiple" name="job_prinsiple" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs font-medium text-slate-700 bg-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                            <option value="">Pilih Prinsiple</option>
                            @if($editData && $editData->job_prinsiple && !$principles->contains('name', $editData->job_prinsiple))
                                <option value="{{ $editData->job_prinsiple }}" selected>{{ $editData->job_prinsiple }}</option>
                            @endif
                            @foreach($principles as $prin)
                                <option value="{{ $prin->name }}" {{ old('job_prinsiple', $editData->job_prinsiple ?? '') == $prin->name ? 'selected' : '' }}>
                                    {{ $prin->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Area (Mengikuti Sesuai Area User) -->
                    <div>
                        <label for="job_area" class="block text-xs font-bold text-slate-700 mb-1">Area</label>
                        @if($isAdmin)
                            <select id="job_area" name="job_area" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs font-medium text-slate-700 bg-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                                <option value="">Pilih Area</option>
                                @foreach($areas as $ar)
                                    <option value="{{ $ar }}" {{ strcasecmp(old('job_area', $editData->job_area ?? $userArea), $ar) === 0 ? 'selected' : '' }}>
                                        {{ $ar }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <input type="text" 
                                   id="job_area" 
                                   name="job_area" 
                                   value="{{ old('job_area', $editData->job_area ?? $userArea) }}" 
                                   readonly 
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 bg-slate-100/90 text-xs font-bold text-slate-800 cursor-not-allowed outline-none shadow-inner"
                                   title="Area mengikuti profil penempatan Anda">
                        @endif
                    </div>

                    <!-- Provinsi Penempatan & Kota Penempatan (Pilihan Se-Indonesia) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="province" class="block text-xs font-bold text-slate-700 mb-1">Provinsi Penempatan</label>
                            <select id="province" 
                                    name="province" 
                                    class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs font-medium text-slate-700 bg-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                                <option value="">Pilih Provinsi</option>
                                @foreach($provinces as $prov)
                                    <option value="{{ $prov }}" {{ old('province', $editData->province ?? '') == $prov ? 'selected' : '' }}>
                                        {{ $prov }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="city" class="block text-xs font-bold text-slate-700 mb-1">Kota Penempatan</label>
                            <select id="city" 
                                    name="city" 
                                    class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs font-medium text-slate-700 bg-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                                <option value="">Pilih Kota</option>
                            </select>
                        </div>
                    </div>

                    <!-- Tanggal Expired -->
                    <div>
                        <label for="tgl_expired" class="block text-xs font-bold text-slate-700 mb-1">
                            Tanggal Expired
                        </label>
                        <div class="relative">
                            <input type="date" 
                                   id="tgl_expired" 
                                   name="tgl_expired" 
                                   value="{{ old('tgl_expired', $editData && $editData->tgl_expired ? $editData->tgl_expired->format('Y-m-d') : '') }}"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 focus:ring-2 focus:ring-primary-500 outline-none">
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">Kosongkan jika lowongan berlaku terus tanpa batas waktu.</p>
                    </div>

                    <!-- Pendidikan & Kualifikasi Umum -->
                    <div>
                        <label for="job_quals" class="block text-xs font-bold text-slate-700 mb-1">Pendidikan &amp; Kualifikasi Umum</label>
                        <textarea id="job_quals" 
                                  name="job_quals" 
                                  class="summernote"
                                  placeholder="Contoh: S1 Teknik Informatika, IPK min 3.25, Usia Maks 30 Tahun">{!! old('job_quals', $editData ? $editData->job_quals : '') !!}</textarea>
                    </div>

                    <!-- Spesialisasi Keterampilan (Skills) -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label for="job_skills" class="block text-xs font-bold text-slate-700">Spesialisasi Keterampilan (Skills)</label>
                            <span class="text-[10px] text-primary font-semibold">Pisahkan dengan koma</span>
                        </div>
                        <textarea id="job_skills" 
                                  name="job_skills" 
                                  rows="2" 
                                  placeholder="Contoh: React, Node.js, REST API, MySQL, Unit Testing, AWS"
                                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-medium text-slate-700 focus:ring-2 focus:ring-primary-500 outline-none">{{ old('job_skills', $editData ? $editData->plain_skills : '') }}</textarea>
                    </div>

                    <!-- Ringkasan Pengalaman yang Dibutuhkan -->
                    <div>
                        <label for="job_exp" class="block text-xs font-bold text-slate-700 mb-1">Ringkasan Pengalaman yang Dibutuhkan</label>
                        <textarea id="job_exp" 
                                  name="job_exp" 
                                  class="summernote"
                                  placeholder="Contoh: Minimal 3 tahun memimpin tim tech developer atau sejenis.">{!! old('job_exp', $editData ? $editData->job_exp : '') !!}</textarea>
                    </div>

                    <!-- Deskripsi Tugas Pekerjaan (Job Description) -->
                    <div>
                        <label for="job_desc" class="block text-xs font-bold text-slate-700 mb-1">Deskripsi Tugas Pekerjaan (Job Description)</label>
                        <textarea id="job_desc" 
                                  name="job_desc" 
                                  class="summernote"
                                  placeholder="Tuliskan detail tanggung jawab harian serta target KPI jika ada...">{!! old('job_desc', $editData ? $editData->job_desc : '') !!}</textarea>
                    </div>

                    <!-- Informasi Tambahan (Internal HR Only) -->
                    <div>
                        <label for="additional_info" class="block text-xs font-bold text-slate-700 mb-1">
                            Informasi Tambahan <span class="text-[10px] text-slate-400 font-normal">(Internal HR Only)</span>
                        </label>
                        <textarea id="additional_info" 
                                  name="additional_info" 
                                  rows="2" 
                                  placeholder="Catatan penempatan internal, rentang gaji, atau kebutuhan mendesak."
                                  class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-medium text-slate-700 focus:ring-2 focus:ring-primary-500 outline-none">{{ old('additional_info', $editData ? $editData->plain_additional_info : '') }}</textarea>
                    </div>

                    <!-- Tips Penulisan -->
                    <div class="p-3 rounded-xl bg-blue-50/80 border border-blue-200/80 text-[11px] text-blue-800 space-y-1">
                        <div class="font-bold flex items-center gap-1.5 text-blue-900">
                            <i class="fa-solid fa-circle-info text-xs text-primary"></i>
                            <span>Tips Format Penulisan:</span>
                        </div>
                        <p class="leading-relaxed">Gunakan tanda strip (<code>- </code>) di awal setiap baris kualifikasi dan job desc agar otomatis terformat rapi menjadi poin-poin saat dibagikan ke WhatsApp dan portal pelamar.</p>
                    </div>

                    <!-- Submit & Reset Buttons -->
                    <div class="pt-2 flex items-center justify-end gap-2.5">
                        <a href="{{ route('job.input') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-100 transition-all">
                            <i class="fa-solid fa-arrow-rotate-left mr-1"></i> Reset
                        </a>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary hover:bg-primary-700 text-white text-xs font-bold shadow-md shadow-primary-500/20 transition-all flex items-center gap-2">
                            <i class="fa-solid {{ $editData ? 'fa-floppy-disk' : 'fa-check' }}"></i>
                            <span>{{ $editData ? 'Update Job Requirement' : 'Simpan Job Requirement' }}</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>

        <!-- ============================================== -->
        <!-- RIGHT COLUMN: ACTIVE & EXPIRED JOBS (7 COLS)   -->
        <!-- ============================================== -->
        <div class="lg:col-span-7 space-y-6">
            
            <!-- ACTIVE JOBS CARD -->
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <!-- Header with Search & Creator Filter -->
                <div class="p-4 sm:p-5 border-b border-slate-200 flex flex-col lg:flex-row lg:items-center justify-between gap-3 bg-slate-50/50">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-list-check"></i>
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2 flex-wrap">
                                <span>{{ $isAdmin ? 'Daftar Job Aktif' : 'Daftar Job Aktif Anda' }}</span>
                                <span class="bg-emerald-100 text-emerald-800 text-[10px] font-extrabold px-2 py-0.5 rounded-full">{{ $activeJobs->count() }} Lowongan</span>
                            </h2>
                            <p class="text-[11px] text-slate-500">
                                @if($isAdmin)
                                    Seluruh lowongan aktif dari semua rekruter / pembuat
                                @else
                                    Lowongan aktif yang Anda buat untuk proses seleksi rekrutmen
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                        <!-- Dropdown Filter Pembuat (Khusus Akses Administrator) -->
                        @if($isAdmin && isset($allCreators) && $allCreators->isNotEmpty())
                        <form method="GET" action="{{ route('job.input') }}" class="flex items-center gap-1.5 flex-shrink-0">
                            @if(request('search'))
                                <input type="hidden" name="search" value="{{ request('search') }}">
                            @endif
                            <div class="flex items-center gap-1 bg-white border border-slate-200 rounded-xl px-2.5 py-1.5 shadow-sm">
                                <i class="fa-solid fa-user-gear text-primary text-xs"></i>
                                <select name="filter_creator" onchange="this.form.submit()" class="bg-transparent text-xs font-bold text-slate-700 focus:outline-none cursor-pointer">
                                    <option value="" {{ empty($filterCreator) || $filterCreator === 'all' ? 'selected' : '' }}>Semua Pembuat ({{ $allCreators->sum('total') }})</option>
                                    <option value="my" {{ $filterCreator === 'my' ? 'selected' : '' }}>Akun Saya ({{ $user?->name ?? 'Admin' }})</option>
                                    @foreach($allCreators as $cr)
                                        <option value="{{ $cr->created_by }}" {{ $filterCreator === $cr->created_by ? 'selected' : '' }}>
                                            {{ $cr->created_by }} ({{ $cr->total }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                        @endif

                        <!-- Search Input -->
                        <form action="{{ route('job.input') }}" method="GET" class="relative w-full sm:w-56">
                            @if(!empty($filterCreator))
                                <input type="hidden" name="filter_creator" value="{{ $filterCreator }}">
                            @endif
                            <input type="text" 
                                   name="search" 
                                   value="{{ $search ?? '' }}"
                                   placeholder="Cari jabatan, skill, area..." 
                                   class="w-full pl-8 pr-3 py-1.5 rounded-xl border border-slate-300 text-xs font-medium focus:ring-2 focus:ring-primary-500 outline-none">
                            <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            @if($search)
                                <a href="{{ route('job.input', array_filter(['filter_creator' => $filterCreator])) }}" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs">✕</a>
                            @endif
                        </form>
                    </div>
                </div>

                <!-- Table Content -->
                @if($activeJobs->isEmpty())
                <div class="p-10 text-center">
                    <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3 text-2xl">
                        <i class="fa-solid fa-briefcase"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-700">Belum Ada Lowongan Aktif</h3>
                    <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                        @if($isAdmin)
                            Tidak ada data lowongan aktif yang cocok dengan kriteria pencarian/filter.
                        @else
                            Anda belum membuat lowongan aktif. Gunakan formulir di sebelah kiri untuk menambah lowongan baru.
                        @endif
                    </p>
                </div>
                @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs custom-table">
                        <thead>
                            <tr>
                                <th class="w-10 text-center">No</th>
                                <th>Posisi / Jabatan</th>
                                <th>Area & Prinsiple</th>
                                <th>Skills yang Dibutuhkan</th>
                                <th class="text-center w-28">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($activeJobs as $index => $job)
                            @php
                                $canManage = $isAdmin || (strtolower(trim($job->created_by ?? '')) === strtolower(trim($user?->email ?? '')) || strtolower(trim($job->created_by ?? '')) === strtolower(trim($user?->name ?? '')));
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="text-center font-bold text-slate-400">{{ $index + 1 }}</td>
                                <td>
                                    <div class="flex items-start gap-2.5">
                                        <!-- QR Code Trigger -->
                                        <button type="button" 
                                                @click="showQR('{{ addslashes($job->job_title) }}', '{{ route('job.detail', $job->id) }}')"
                                                class="w-9 h-9 rounded-lg border border-slate-200 bg-slate-50 hover:bg-primary-50 hover:border-primary-300 text-slate-600 hover:text-primary flex items-center justify-center flex-shrink-0 transition-all text-sm"
                                                title="Lihat & Unduh QR Code Lowongan">
                                            <i class="fa-solid fa-qrcode"></i>
                                        </button>
                                        <div class="min-w-0">
                                            <div class="font-bold text-slate-900 text-xs leading-snug">
                                                {{ $job->job_title }}
                                            </div>
                                            <div class="flex items-center flex-wrap gap-1.5 mt-1">
                                                @if($job->tgl_expired)
                                                    <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-rose-600 bg-rose-50 px-1.5 py-0.5 rounded">
                                                        <i class="fa-regular fa-clock text-[9px]"></i>
                                                        Exp: {{ $job->tgl_expired->format('d M Y') }}
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded">
                                                        <i class="fa-solid fa-infinity text-[8px]"></i>
                                                        Tanpa Batas
                                                    </span>
                                                @endif
                                                <span class="inline-flex items-center gap-1 text-[10px] font-medium text-slate-600 bg-slate-100 border border-slate-200 px-1.5 py-0.5 rounded" title="Dibuat oleh">
                                                    <i class="fa-solid fa-user-pen text-[9px] text-primary"></i>
                                                    {{ $job->created_by ?: 'System' }}
                                                </span>
                                                <span class="text-[10px] text-slate-400">• {{ $job->created_at ? $job->created_at->diffForHumans() : '-' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="space-y-1">
                                        @if($job->job_area)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                            <i class="fa-solid fa-location-dot text-[9px]"></i>
                                            {{ $job->job_area }}
                                        </span>
                                        @endif
                                        @if($job->city || $job->province)
                                        <div class="text-[10px] text-slate-500 font-medium flex items-center gap-1">
                                            <i class="fa-solid fa-map-pin text-[9px] text-slate-400"></i>
                                            {{ implode(', ', array_filter([$job->city, $job->province])) }}
                                        </div>
                                        @endif
                                        @if($job->job_prinsiple)
                                        <span class="block text-[11px] font-bold text-slate-700">
                                            <i class="fa-solid fa-building-circle-check text-[10px] text-slate-400 mr-1"></i>
                                            {{ $job->job_prinsiple }}
                                        </span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="flex flex-wrap gap-1 max-w-[200px]">
                                        @php
                                            $skills = $job->skills_array;
                                            $showSkills = array_slice($skills, 0, 3);
                                            $remainCount = count($skills) - 3;
                                        @endphp
                                        @forelse($showSkills as $sk)
                                            <span class="inline-block px-2 py-0.5 bg-slate-100 text-slate-700 border border-slate-200 rounded text-[10px] font-medium">
                                                {{ $sk }}
                                            </span>
                                        @empty
                                            <span class="text-[10px] text-slate-400 italic">-</span>
                                        @endforelse
                                        @if($remainCount > 0)
                                            <span class="inline-block px-1.5 py-0.5 bg-slate-200 text-slate-600 rounded text-[9px] font-bold">
                                                +{{ $remainCount }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        @if($canManage)
                                        <a href="{{ route('job.input', ['edit' => $job->id]) }}" 
                                           class="w-7 h-7 rounded-lg bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200 flex items-center justify-center transition-all"
                                           title="Edit Spesifikasi Job">
                                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                                        </a>
                                        @endif

                                        <button type="button" 
                                                @click="copyJobShareLink('{{ addslashes($job->job_title) }}', '{{ addslashes($job->job_area) }}', '{{ addslashes($job->job_prinsiple) }}', '{{ route('job.detail', $job->id) }}', '{{ addslashes(implode(', ', $job->skills_array)) }}')"
                                                class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 flex items-center justify-center transition-all"
                                                title="Copy Broadcast Lowongan untuk WhatsApp">
                                            <i class="fa-brands fa-whatsapp text-xs"></i>
                                        </button>

                                        @if($canManage)
                                        <form action="{{ route('job.destroy', $job->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus lowongan {{ addslashes($job->job_title) }}?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="w-7 h-7 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 flex items-center justify-center transition-all"
                                                    title="Hapus Job">
                                                <i class="fa-solid fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>

            <!-- EXPIRED JOBS CARD (COLLAPSIBLE / ACCORDION) -->
            @if($expiredJobs->isNotEmpty())
            <div class="bg-white border border-rose-200 rounded-2xl shadow-sm overflow-hidden" x-data="{ openExpired: true }">
                <div class="p-4 border-b border-rose-100 bg-rose-50/50 flex items-center justify-between cursor-pointer" @click="openExpired = !openExpired">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-business-time"></i>
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-rose-900 flex items-center gap-2 flex-wrap">
                                <span>{{ $isAdmin ? 'Daftar Lowongan Expired' : 'Daftar Lowongan Expired Anda' }}</span>
                                <span class="bg-rose-200 text-rose-800 text-[10px] font-extrabold px-2 py-0.5 rounded-full">{{ $expiredJobs->count() }} Job</span>
                            </h2>
                            <p class="text-[11px] text-rose-600">Lowongan telah melewati tanggal expired namun tetap tersimpan dalam sistem</p>
                        </div>
                    </div>
                    <i class="fa-solid fa-chevron-down text-rose-400 transition-transform duration-200" :class="{ 'rotate-180': openExpired }"></i>
                </div>

                <div x-show="openExpired" class="overflow-x-auto">
                    <table class="w-full text-left text-xs custom-table">
                        <thead>
                            <tr>
                                <th class="w-10 text-center">No</th>
                                <th>Posisi / Jabatan</th>
                                <th>Area & Prinsiple</th>
                                <th>Tanggal Expired</th>
                                <th class="text-center w-28">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($expiredJobs as $index => $exp)
                            @php
                                $canManageExp = $isAdmin || (strtolower(trim($exp->created_by ?? '')) === strtolower(trim($user?->email ?? '')) || strtolower(trim($exp->created_by ?? '')) === strtolower(trim($user?->name ?? '')));
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors opacity-80">
                                <td class="text-center font-bold text-slate-400">{{ $index + 1 }}</td>
                                <td>
                                    <div class="flex items-start gap-2.5">
                                        <!-- QR Code Trigger -->
                                        <button type="button" 
                                                @click="showQR('{{ addslashes($exp->job_title) }}', '{{ route('job.detail', $exp->id) }}')"
                                                class="w-8 h-8 rounded-lg border border-slate-200 bg-slate-50 hover:bg-primary-50 hover:border-primary-300 text-slate-500 hover:text-primary flex items-center justify-center flex-shrink-0 transition-all text-xs"
                                                title="Lihat & Unduh QR Code Lowongan">
                                            <i class="fa-solid fa-qrcode"></i>
                                        </button>
                                        <div class="min-w-0">
                                            <div class="font-bold text-slate-600 line-through">
                                                {{ $exp->job_title }}
                                            </div>
                                            <div class="flex items-center gap-1.5 mt-1">
                                                <span class="inline-flex items-center gap-1 text-[10px] font-medium text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded" title="Dibuat oleh">
                                                    <i class="fa-solid fa-user-pen text-[9px] text-slate-400"></i>
                                                    {{ $exp->created_by ?: 'System' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="space-y-0.5">
                                        <span class="text-xs font-semibold text-slate-600">{{ $exp->job_area ?? '-' }}</span>
                                        @if($exp->city || $exp->province)
                                        <div class="text-[10px] text-slate-400">
                                            {{ implode(', ', array_filter([$exp->city, $exp->province])) }}
                                        </div>
                                        @endif
                                        <span class="block text-[10px] text-slate-400">{{ $exp->job_prinsiple ?? '-' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-rose-700 bg-rose-100 px-2 py-0.5 rounded-full">
                                        <i class="fa-solid fa-calendar-xmark text-[10px]"></i>
                                        {{ $exp->tgl_expired ? $exp->tgl_expired->format('d M Y') : '-' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        @if($canManageExp)
                                        <a href="{{ route('job.input', ['edit' => $exp->id]) }}" 
                                           class="w-7 h-7 rounded-lg bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200 flex items-center justify-center transition-all"
                                           title="Perpanjang / Edit Tanggal Expired">
                                            <i class="fa-solid fa-clock-rotate-left text-xs"></i>
                                        </a>

                                        <button type="button" 
                                                @click="copyJobShareLink('{{ addslashes($exp->job_title) }}', '{{ addslashes($exp->job_area) }}', '{{ addslashes($exp->job_prinsiple) }}', '{{ route('job.detail', $exp->id) }}')"
                                                class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 flex items-center justify-center transition-all"
                                                title="Copy Broadcast Lowongan untuk WhatsApp">
                                            <i class="fa-brands fa-whatsapp text-xs"></i>
                                        </button>

                                        <form action="{{ route('job.destroy', $exp->id) }}" method="POST" onsubmit="return confirm('Hapus lowongan expired ini secara permanen?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="w-7 h-7 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 flex items-center justify-center transition-all"
                                                    title="Hapus Job">
                                                <i class="fa-solid fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
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

    <!-- QR CODE MODAL POPUP -->
    <div x-show="qrModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6 text-center border border-slate-200 space-y-4"
             @click.away="qrModalOpen = false">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="text-left">
                    <h4 class="text-sm font-extrabold text-slate-900">QR Code Lowongan Kerja</h4>
                    <p class="text-[11px] text-slate-500 truncate max-w-[220px]" x-text="qrJobTitle"></p>
                </div>
                <button @click="qrModalOpen = false" class="text-slate-400 hover:text-slate-600 text-base p-1">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- QR Code Container -->
            <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 flex flex-col items-center justify-center">
                <img :src="qrImageUrl" alt="Job QR Code" class="w-48 h-48 rounded-lg shadow-sm">
                <p class="text-[11px] text-slate-400 mt-2">Scan QR untuk membuka detail lowongan langsung</p>
            </div>

            <!-- Tautan Langsung Job Detail -->
            <div class="bg-slate-50 rounded-xl p-3 border border-slate-200 text-left space-y-1.5">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider flex items-center gap-1">
                        <i class="fa-solid fa-link text-primary text-[10px]"></i>
                        <span>Link Langsung Lowongan</span>
                    </span>
                    <button type="button" @click="copyJobUrl()" class="text-[11px] font-bold text-primary hover:text-blue-700 flex items-center gap-1 transition">
                        <i class="fa-regular fa-copy"></i> Salin Link
                    </button>
                </div>
                <div class="bg-white px-2.5 py-1.5 rounded-lg border border-slate-200 flex items-center justify-between gap-2">
                    <a :href="qrJobUrl" target="_blank" class="text-xs font-semibold text-primary hover:underline truncate max-w-[240px]" x-text="qrJobUrl"></a>
                    <a :href="qrJobUrl" target="_blank" class="text-slate-400 hover:text-primary transition shrink-0 p-1" title="Buka di tab baru">
                        <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                    </a>
                </div>
            </div>

            <div class="space-y-2 pt-1">
                <div class="grid grid-cols-2 gap-2">
                    <a :href="qrImageUrl" download="qrcode-job.png" target="_blank" class="w-full inline-flex items-center justify-center gap-1.5 py-2.5 rounded-xl bg-primary text-white text-xs font-bold hover:bg-blue-700 transition-all shadow-sm">
                        <i class="fa-solid fa-download text-xs"></i>
                        <span>Download QR</span>
                    </a>
                    <button type="button" @click="copyJobUrl()" class="w-full inline-flex items-center justify-center gap-1.5 py-2.5 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 text-xs font-bold transition-all">
                        <i class="fa-regular fa-copy text-xs"></i>
                        <span>Salin Link</span>
                    </button>
                </div>
                <button @click="qrModalOpen = false" type="button" class="w-full py-2 rounded-xl text-xs font-semibold text-slate-500 hover:bg-slate-100 transition-all">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- AI & PRESET GENERATOR MODAL -->
    <div x-show="presetModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 border border-slate-200 space-y-4"
             @click.away="presetModalOpen = false">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-primary to-blue-500 text-white flex items-center justify-center">
                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-extrabold text-slate-900">AI Job Description Assistant</h4>
                        <p class="text-[11px] text-slate-500">Pilih template standar perusahaan untuk mempercepat input data</p>
                    </div>
                </div>
                <button @click="presetModalOpen = false" class="text-slate-400 hover:text-slate-600 text-base p-1">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="space-y-2.5 max-h-96 overflow-y-auto pr-1">
                @foreach($templates as $tmpl)
                <div class="p-3.5 rounded-xl border border-slate-200 hover:border-primary-400 hover:bg-primary-50/40 transition-all cursor-pointer"
                     @click="applyTemplate({{ json_encode($tmpl) }}); presetModalOpen = false;">
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-bold text-xs text-slate-900">{{ $tmpl['title'] }}</span>
                        <span class="text-[10px] font-bold text-primary bg-primary-50 px-2 py-0.5 rounded-full border border-primary-200">{{ $tmpl['label'] }}</span>
                    </div>
                    <p class="text-[11px] text-slate-500 line-clamp-2 leading-relaxed">{{ $tmpl['desc'] }}</p>
                    <div class="mt-2 text-[10px] text-slate-400 flex items-center gap-2">
                        <span><i class="fa-solid fa-map-pin mr-1 text-slate-400"></i>{{ $tmpl['area'] }}</span>
                        <span>•</span>
                        <span class="truncate max-w-[240px]"><i class="fa-solid fa-bolt mr-1 text-amber-500"></i>{{ $tmpl['skills'] }}</span>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="pt-2 border-t border-slate-100 flex items-center justify-end">
                <button @click="presetModalOpen = false" type="button" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-all">
                    Tutup
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

<script>
    const provincesWithCities = @json($provincesWithCities ?? []);
    const initialProvince = @json(old('province', $editData->province ?? ''));
    const initialCity = @json(old('city', $editData->city ?? ''));

    function populateCities(provName, preselectedCity = '') {
        const citySelect = document.getElementById('city');
        if (!citySelect) return;

        citySelect.innerHTML = '<option value="">Pilih Kota</option>';

        if (!provName || !provincesWithCities[provName]) {
            citySelect.setAttribute('disabled', 'disabled');
            citySelect.classList.add('bg-slate-50');
            return;
        }

        citySelect.removeAttribute('disabled');
        citySelect.classList.remove('bg-slate-50');

        const cities = provincesWithCities[provName] || [];
        let matched = false;

        cities.forEach(city => {
            const opt = document.createElement('option');
            opt.value = city;
            opt.textContent = city;
            if (preselectedCity && (city.toLowerCase() === preselectedCity.toLowerCase())) {
                opt.selected = true;
                matched = true;
            }
            citySelect.appendChild(opt);
        });

        if (preselectedCity && !matched) {
            const customOpt = document.createElement('option');
            customOpt.value = preselectedCity;
            customOpt.textContent = preselectedCity;
            customOpt.selected = true;
            citySelect.appendChild(customOpt);
        }
    }

    $(document).ready(function() {
        // Init Summernote WYSIWYG
        const summernoteConfig = {
            height: 130,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link', 'table']],
                ['misc', ['undo', 'redo']]
            ],
            callbacks: {
                onImageUpload: function(files) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Gambar Tidak Diizinkan',
                        text: 'Penyisipan gambar ke dalam formulir lowongan kerja tidak diizinkan. Mohon masukkan rincian dalam format teks atau poin.',
                        confirmButtonColor: '#4f46e5'
                    });
                }
            }
        };

        $('#job_quals').summernote($.extend({}, summernoteConfig, {
            placeholder: 'Contoh: S1 Teknik Informatika, IPK min 3.25, Usia Maks 30 Tahun'
        }));

        $('#job_exp').summernote($.extend({}, summernoteConfig, {
            placeholder: 'Contoh: Minimal 3 tahun memimpin tim tech developer atau sejenis.'
        }));

        $('#job_desc').summernote($.extend({}, summernoteConfig, {
            placeholder: 'Tuliskan detail tanggung jawab harian serta target KPI jika ada...'
        }));

        // Dependent dropdown Provinsi -> Kota
        const provSelect = document.getElementById('province');
        if (provSelect) {
            provSelect.addEventListener('change', function() {
                populateCities(this.value);
            });
        }

        if (initialProvince) {
            populateCities(initialProvince, initialCity);
        } else {
            populateCities('');
        }

        // Sinkronisasi textarea Summernote sebelum form submit
        const jobForm = document.getElementById('jobForm');
        if (jobForm) {
            jobForm.addEventListener('submit', function() {
                if ($('#job_quals').length) $('#job_quals').val($('#job_quals').summernote('code'));
                if ($('#job_exp').length) $('#job_exp').val($('#job_exp').summernote('code'));
                if ($('#job_desc').length) $('#job_desc').val($('#job_desc').summernote('code'));
            });
        }

        // Handler Generate Job by AI
        const btnAi = document.getElementById('btnGenerateAI');
        if (btnAi) {
            btnAi.addEventListener('click', function() {
                const titleInput = document.getElementById('job_title');
                const jobTitle = titleInput ? titleInput.value.trim() : '';
                if (!jobTitle) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Posisi Belum Diisi',
                        text: 'Silakan isi Posisi / Nama Jabatan terlebih dahulu untuk digenerate oleh AI.',
                        confirmButtonColor: '#0F52BA'
                    });
                    return;
                }

                const origHtml = btnAi.innerHTML;
                btnAi.disabled = true;
                btnAi.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>Generating AI...</span>';

                fetch('{{ route("job.generate_ai") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ job_title: jobTitle })
                })
                .then(res => res.json())
                .then(res => {
                    btnAi.disabled = false;
                    btnAi.innerHTML = origHtml;

                    if (res.status === 'success' && res.data) {
                        if (res.data.quals) {
                            $('#job_quals').summernote('code', res.data.quals);
                        }
                        if (res.data.skills && document.getElementById('job_skills')) {
                            document.getElementById('job_skills').value = res.data.skills;
                        }
                        if (res.data.exp) {
                            $('#job_exp').summernote('code', res.data.exp);
                        }
                        if (res.data.desc) {
                            $('#job_desc').summernote('code', res.data.desc);
                        }
                        if (res.data.additional_info && document.getElementById('additional_info')) {
                            document.getElementById('additional_info').value = res.data.additional_info;
                        }

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil Digenerate!',
                            text: 'Kualifikasi, skills, pengalaman, dan job description berhasil diisi otomatis oleh AI.',
                            timer: 2500,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Generate',
                            text: res.message || 'Terjadi kesalahan saat memproses generator AI.'
                        });
                    }
                })
                .catch(err => {
                    btnAi.disabled = false;
                    btnAi.innerHTML = origHtml;
                    Swal.fire({
                        icon: 'error',
                        title: 'Kesalahan Sistem',
                        text: 'Gagal menghubungi server generator AI.'
                    });
                });
            });
        }

        // Handler Generate Image Prompt
        let generatedPromptData = null;
        const btnPrompt = document.getElementById('btnCopyPrompt');
        if (btnPrompt) {
            btnPrompt.addEventListener('click', function() {
                if (generatedPromptData) {
                    showPromptModal(generatedPromptData);
                    return;
                }

                const titleInput = document.getElementById('job_title');
                const jobTitle = titleInput ? titleInput.value.trim() : '';
                if (!jobTitle) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Posisi Belum Diisi',
                        text: 'Silakan isi Posisi / Nama Jabatan terlebih dahulu untuk generate image prompt.',
                        confirmButtonColor: '#0F52BA'
                    });
                    return;
                }

                const skillsData = document.getElementById('job_skills')?.value.trim() || '';
                const qualsData = $('#job_quals').length ? $('#job_quals').summernote('code') : (document.getElementById('job_quals')?.value || '');

                const origHtml = btnPrompt.innerHTML;
                btnPrompt.disabled = true;
                btnPrompt.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>Generating...</span>';

                fetch('{{ route("job.generate_image_prompt") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        job_title: jobTitle,
                        job_skills: skillsData,
                        job_quals: qualsData
                    })
                })
                .then(res => res.json())
                .then(res => {
                    btnPrompt.disabled = false;
                    btnPrompt.innerHTML = origHtml;

                    if (res.status === 'success' && res.data) {
                        generatedPromptData = JSON.stringify(res.data, null, 2);
                        btnPrompt.innerHTML = '<i class="fa-regular fa-copy"></i> <span>Copy Prompt</span>';
                        btnPrompt.classList.remove('bg-[#2563eb]', 'hover:bg-blue-700');
                        btnPrompt.classList.add('bg-emerald-600', 'hover:bg-emerald-700');
                        showPromptModal(generatedPromptData);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: res.message || 'Terjadi kesalahan saat generate image prompt.'
                        });
                    }
                })
                .catch(err => {
                    btnPrompt.disabled = false;
                    btnPrompt.innerHTML = origHtml;
                    Swal.fire({
                        icon: 'error',
                        title: 'Kesalahan Sistem',
                        text: 'Gagal menghubungi server generator prompt.'
                    });
                });
            });
        }

        function showPromptModal(promptText) {
            Swal.fire({
                title: 'Prompt Berhasil Digenerate!',
                html: `
                    <p class="text-xs text-slate-500 text-left mb-2">Prompt JSON siap digunakan untuk Image Generator (Midjourney / DALL-E):</p>
                    <textarea id="swal-prompt-text" class="w-full p-3 rounded-xl border border-slate-300 font-mono text-xs text-slate-800 bg-slate-50" rows="10" readonly>${promptText}</textarea>
                `,
                icon: 'success',
                showCancelButton: true,
                confirmButtonText: '<i class="fa-regular fa-copy mr-1"></i> Copy Prompt',
                cancelButtonText: 'Tutup',
                confirmButtonColor: '#0F52BA',
                cancelButtonColor: '#64748b',
                width: '600px'
            }).then((result) => {
                if (result.isConfirmed) {
                    navigator.clipboard.writeText(promptText).then(() => {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Prompt berhasil disalin ke clipboard!',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    }).catch(() => {
                        alert('Gagal menyalin ke clipboard.');
                    });
                }
            });
        }
    });

    function jobManager() {
        return {
            qrModalOpen: false,
            presetModalOpen: false,
            qrJobTitle: '',
            qrJobUrl: '',
            qrImageUrl: '',

            showQR(title, jobUrl) {
                this.qrJobTitle = title;
                this.qrJobUrl = jobUrl;
                const encodedUrl = encodeURIComponent(jobUrl);
                this.qrImageUrl = `https://api.qrserver.com/v1/create-qr-code/?size=350x350&data=${encodedUrl}&color=0F52BA`;
                this.qrModalOpen = true;
            },

            copyJobUrl() {
                if (!this.qrJobUrl) return;
                navigator.clipboard.writeText(this.qrJobUrl).then(() => {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Tautan langsung lowongan berhasil disalin!',
                        showConfirmButton: false,
                        timer: 2500
                    });
                }).catch(() => {
                    prompt('Salin link lowongan berikut:', this.qrJobUrl);
                });
            },

            openPresetModal() {
                this.presetModalOpen = true;
            },

            applyTemplate(tmpl) {
                if (document.getElementById('job_title')) {
                    document.getElementById('job_title').value = tmpl.title || '';
                }
                if ($('#job_quals').length) {
                    $('#job_quals').summernote('code', tmpl.quals ? tmpl.quals.replace(/\n/g, '<br>') : '');
                } else if (document.getElementById('job_quals')) {
                    document.getElementById('job_quals').value = tmpl.quals || '';
                }
                if (document.getElementById('job_skills')) {
                    document.getElementById('job_skills').value = tmpl.skills || '';
                }
                if ($('#job_exp').length) {
                    $('#job_exp').summernote('code', tmpl.exp ? tmpl.exp.replace(/\n/g, '<br>') : '');
                } else if (document.getElementById('job_exp')) {
                    document.getElementById('job_exp').value = tmpl.exp || '';
                }
                if ($('#job_desc').length) {
                    $('#job_desc').summernote('code', tmpl.desc ? tmpl.desc.replace(/\n/g, '<br>') : '');
                } else if (document.getElementById('job_desc')) {
                    document.getElementById('job_desc').value = tmpl.desc || '';
                }
            },

            copyJobShareLink(title, area, prinsiple, jobUrl, skills = '') {
                let text = `📢 *LOWONGAN KERJA TERBARU*\n\n` +
                           `💼 *Posisi:* ${title}\n` +
                           `🏢 *Prinsiple / Perusahaan:* ${prinsiple || '-'}\n` +
                           `📍 *Area Penempatan:* ${area || '-'}\n`;

                if (skills && skills.trim()) {
                    text += `⚡ *Keahlian / Skill:* ${skills}\n`;
                }

                text += `\n🔗 *Link Detail & Lamar Lowongan:*\n` +
                        `${jobUrl}\n\n` +
                        `📲 _Silakan klik link di atas untuk melihat detail lengkap dan langsung melamar online._`;

                navigator.clipboard.writeText(text).then(() => {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Pesan WhatsApp & Link Job berhasil disalin!',
                        text: 'Siap di-paste ke WhatsApp',
                        showConfirmButton: false,
                        timer: 3000
                    });
                }).catch(() => {
                    prompt('Salin pesan broadcast lowongan berikut:', text);
                });
            }
        };
    }
</script>
@endsection