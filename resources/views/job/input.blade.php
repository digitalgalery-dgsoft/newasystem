@extends('layouts.app')

@section('title', 'Input Job Requirement - Attendance Admin Portal')

@section('content')
<div class="space-y-6" x-data="jobManager()">
    
    <!-- PAGE TITLE & HEADER CARD -->
    <div class="page-header-card flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-xl bg-primary-50 border border-primary-200 flex items-center justify-center text-primary text-xl font-bold">
                    <i class="fa-solid fa-briefcase"></i>
                </div>
                <div>
                    <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Input Job Requirement</h1>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">Kelola posisi lowongan pekerjaan, kualifikasi kandidat, dan masa berlaku kampanye rekrutmen</p>
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
                <div class="px-5 py-4 border-b border-slate-200 bg-slate-50/70 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg {{ $editData ? 'bg-amber-100 text-amber-700' : 'bg-primary-100 text-primary' }} flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid {{ $editData ? 'fa-pen-to-square' : 'fa-plus' }}"></i>
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-slate-800">{{ $editData ? 'Edit Spesifikasi Job' : 'Tambah Job Baru' }}</h2>
                            <p class="text-[11px] text-slate-500">{{ $editData ? 'Perbarui informasi posisi pekerjaan terpilih' : 'Lengkapi data posisi & kualifikasi' }}</p>
                        </div>
                    </div>

                    @if($editData)
                    <a href="{{ route('job.input') }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition-all">
                        <i class="fa-solid fa-xmark"></i> Batal Edit
                    </a>
                    @endif
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
                                   placeholder="Contoh: Admin Operasional & Back Office" 
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all placeholder:text-slate-400 font-semibold text-slate-800">
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">Nama posisi harus unik untuk lowongan yang sedang aktif.</p>
                    </div>

                    <!-- Prinsiple & Area (Grid 2 cols) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="job_prinsiple" class="block text-xs font-bold text-slate-700 mb-1">Prinsiple</label>
                            <select id="job_prinsiple" name="job_prinsiple" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-medium text-slate-700 bg-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                                <option value="">-- Pilih Prinsiple --</option>
                                @foreach($principles as $prin)
                                    <option value="{{ $prin->name }}" {{ old('job_prinsiple', $editData->job_prinsiple ?? '') == $prin->name ? 'selected' : '' }}>
                                        {{ $prin->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="job_area" class="block text-xs font-bold text-slate-700 mb-1">Area Penempatan</label>
                            <select id="job_area" name="job_area" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-medium text-slate-700 bg-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                                <option value="">-- Pilih Area --</option>
                                @foreach($areas as $ar)
                                    <option value="{{ $ar }}" {{ old('job_area', $editData->job_area ?? '') == $ar ? 'selected' : '' }}>
                                        {{ $ar }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Provinsi & Kota -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="province" class="block text-xs font-bold text-slate-700 mb-1">Provinsi</label>
                            <input type="text" 
                                   id="province" 
                                   name="province" 
                                   value="{{ old('province', $editData->province ?? '') }}"
                                   placeholder="Contoh: DKI Jakarta" 
                                   class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-medium text-slate-700 focus:ring-2 focus:ring-primary-500 outline-none">
                        </div>

                        <div>
                            <label for="city" class="block text-xs font-bold text-slate-700 mb-1">Kota / Kabupaten</label>
                            <input type="text" 
                                   id="city" 
                                   name="city" 
                                   value="{{ old('city', $editData->city ?? '') }}"
                                   placeholder="Contoh: Jakarta Pusat" 
                                   class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-medium text-slate-700 focus:ring-2 focus:ring-primary-500 outline-none">
                        </div>
                    </div>

                    <!-- Tanggal Expired -->
                    <div>
                        <label for="tgl_expired" class="block text-xs font-bold text-slate-700 mb-1">
                            Tanggal Expired (Deadline Lowongan)
                        </label>
                        <div class="relative">
                            <input type="date" 
                                   id="tgl_expired" 
                                   name="tgl_expired" 
                                   value="{{ old('tgl_expired', $editData && $editData->tgl_expired ? $editData->tgl_expired->format('Y-m-d') : '') }}"
                                   class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 focus:ring-2 focus:ring-primary-500 outline-none">
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">Kosongkan jika lowongan berlaku terus tanpa batas waktu.</p>
                    </div>

                    <!-- Pendidikan & Kualifikasi Umum -->
                    <div>
                        <label for="job_quals" class="block text-xs font-bold text-slate-700 mb-1">Pendidikan & Kualifikasi Umum</label>
                        <textarea id="job_quals" 
                                  name="job_quals" 
                                  rows="3" 
                                  placeholder="- Pendidikan minimal S1 Akuntansi / Manajemen&#10;- Usia maksimal 28 tahun&#10;- Berpenampilan rapi dan komunikatif"
                                  class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-medium text-slate-700 focus:ring-2 focus:ring-primary-500 outline-none leading-relaxed">{{ old('job_quals', $editData->job_quals ?? '') }}</textarea>
                    </div>

                    <!-- Spesialisasi Keterampilan (Skills) -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label for="job_skills" class="block text-xs font-bold text-slate-700">Keterampilan (Skills)</label>
                            <span class="text-[10px] text-primary font-semibold">Pisahkan dengan koma</span>
                        </div>
                        <textarea id="job_skills" 
                                  name="job_skills" 
                                  rows="2" 
                                  placeholder="Microsoft Excel, VLOOKUP, Administrasi Kantor, Typing Speed"
                                  class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-medium text-slate-700 focus:ring-2 focus:ring-primary-500 outline-none">{{ old('job_skills', $editData->job_skills ?? '') }}</textarea>
                    </div>

                    <!-- Pengalaman Kerja -->
                    <div>
                        <label for="job_exp" class="block text-xs font-bold text-slate-700 mb-1">Pengalaman yang Dibutuhkan</label>
                        <textarea id="job_exp" 
                                  name="job_exp" 
                                  rows="2" 
                                  placeholder="Minimal 1 tahun pengalaman di bidang administrasi perkantoran / retail."
                                  class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-medium text-slate-700 focus:ring-2 focus:ring-primary-500 outline-none">{{ old('job_exp', $editData->job_exp ?? '') }}</textarea>
                    </div>

                    <!-- Deskripsi Pekerjaan (Job Desc) -->
                    <div>
                        <label for="job_desc" class="block text-xs font-bold text-slate-700 mb-1">Deskripsi Tugas (Job Description)</label>
                        <textarea id="job_desc" 
                                  name="job_desc" 
                                  rows="3" 
                                  placeholder="- Menginput data harian operasional cabang&#10;- Melakukan rekonsiliasi arsip dokumen&#10;- Menyusun laporan berkala ke manajemen"
                                  class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-medium text-slate-700 focus:ring-2 focus:ring-primary-500 outline-none leading-relaxed">{{ old('job_desc', $editData->job_desc ?? '') }}</textarea>
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
                                  class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-medium text-slate-700 focus:ring-2 focus:ring-primary-500 outline-none">{{ old('additional_info', $editData->additional_info ?? '') }}</textarea>
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
                <!-- Header with Search -->
                <div class="p-4 sm:p-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/50">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-list-check"></i>
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                <span>Daftar Job Aktif</span>
                                <span class="bg-emerald-100 text-emerald-800 text-[10px] font-extrabold px-2 py-0.5 rounded-full">{{ $activeJobs->count() }} Lowongan</span>
                            </h2>
                            <p class="text-[11px] text-slate-500">Lowongan yang sedang dibuka untuk proses seleksi & rekrutmen</p>
                        </div>
                    </div>

                    <!-- Search Input -->
                    <form action="{{ route('job.input') }}" method="GET" class="relative w-full sm:w-64">
                        <input type="text" 
                               name="search" 
                               value="{{ $search ?? '' }}"
                               placeholder="Cari jabatan, skill, area..." 
                               class="w-full pl-8 pr-3 py-1.5 rounded-xl border border-slate-300 text-xs font-medium focus:ring-2 focus:ring-primary-500 outline-none">
                        <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        @if($search)
                            <a href="{{ route('job.input') }}" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs">✕</a>
                        @endif
                    </form>
                </div>

                <!-- Table Content -->
                @if($activeJobs->isEmpty())
                <div class="p-10 text-center">
                    <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3 text-2xl">
                        <i class="fa-solid fa-briefcase"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-700">Belum Ada Lowongan Aktif</h3>
                    <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">Gunakan formulir di sebelah kiri untuk membuat spesifikasi pekerjaan baru atau gunakan template cepat.</p>
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
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="text-center font-bold text-slate-400">{{ $index + 1 }}</td>
                                <td>
                                    <div class="flex items-start gap-2.5">
                                        <!-- QR Code Trigger -->
                                        <button type="button" 
                                                @click="showQR('{{ $job->job_title }}', '{{ $job->slug }}')"
                                                class="w-9 h-9 rounded-lg border border-slate-200 bg-slate-50 hover:bg-primary-50 hover:border-primary-300 text-slate-600 hover:text-primary flex items-center justify-center flex-shrink-0 transition-all text-sm"
                                                title="Lihat & Unduh QR Code Lowongan">
                                            <i class="fa-solid fa-qrcode"></i>
                                        </button>
                                        <div class="min-w-0">
                                            <div class="font-bold text-slate-900 text-xs leading-snug">
                                                {{ $job->job_title }}
                                            </div>
                                            <div class="flex items-center gap-2 mt-1">
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
                                                <span class="text-[10px] text-slate-400">•</span>
                                                <span class="text-[10px] text-slate-500 font-medium">{{ $job->created_at->diffForHumans() }}</span>
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
                                        <a href="{{ route('job.input', ['edit' => $job->id]) }}" 
                                           class="w-7 h-7 rounded-lg bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200 flex items-center justify-center transition-all"
                                           title="Edit Spesifikasi Job">
                                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                                        </a>

                                        <button type="button" 
                                                @click="copyJobShareLink('{{ $job->job_title }}', '{{ $job->job_area }}', '{{ $job->job_prinsiple }}')"
                                                class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 flex items-center justify-center transition-all"
                                                title="Copy Broadcast Lowongan untuk WhatsApp">
                                            <i class="fa-brands fa-whatsapp text-xs"></i>
                                        </button>

                                        <form action="{{ route('job.destroy', $job->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus lowongan {{ $job->job_title }}?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="w-7 h-7 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 flex items-center justify-center transition-all"
                                                    title="Hapus Job">
                                                <i class="fa-solid fa-trash text-xs"></i>
                                            </button>
                                        </form>
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
                            <h2 class="text-sm font-bold text-rose-900 flex items-center gap-2">
                                <span>Daftar Lowongan Expired</span>
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
                            <tr class="hover:bg-slate-50/80 transition-colors opacity-80">
                                <td class="text-center font-bold text-slate-400">{{ $index + 1 }}</td>
                                <td>
                                    <div class="font-bold text-slate-600 line-through">
                                        {{ $exp->job_title }}
                                    </div>
                                    <span class="text-[10px] text-slate-400">{{ $exp->created_by }}</span>
                                </td>
                                <td>
                                    <div class="space-y-0.5">
                                        <span class="text-xs font-semibold text-slate-600">{{ $exp->job_area ?? '-' }}</span>
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
                                        <a href="{{ route('job.input', ['edit' => $exp->id]) }}" 
                                           class="w-7 h-7 rounded-lg bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200 flex items-center justify-center transition-all"
                                           title="Perpanjang / Edit Tanggal Expired">
                                            <i class="fa-solid fa-clock-rotate-left text-xs"></i>
                                        </a>

                                        <form action="{{ route('job.destroy', $exp->id) }}" method="POST" onsubmit="return confirm('Hapus lowongan expired ini secara permanen?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="w-7 h-7 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 flex items-center justify-center transition-all"
                                                    title="Hapus Job">
                                                <i class="fa-solid fa-trash text-xs"></i>
                                            </button>
                                        </form>
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
                    <h4 class="text-sm font-extrabold text-slate-900">QR Code Lowongan</h4>
                    <p class="text-[11px] text-slate-500 truncate max-w-[220px]" x-text="qrJobTitle"></p>
                </div>
                <button @click="qrModalOpen = false" class="text-slate-400 hover:text-slate-600 text-base p-1">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- QR Code Container -->
            <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-center">
                <img :src="qrImageUrl" alt="Job QR Code" class="w-48 h-48 rounded-lg shadow-sm">
            </div>

            <div class="space-y-2">
                <a :href="qrImageUrl" download="qrcode-job.png" target="_blank" class="w-full inline-flex items-center justify-center gap-2 py-2.5 rounded-xl bg-primary text-white text-xs font-bold hover:bg-primary-700 transition-all shadow-md shadow-primary-500/20">
                    <i class="fa-solid fa-download"></i>
                    <span>Download QR Code</span>
                </a>
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
<script>
    function jobManager() {
        return {
            qrModalOpen: false,
            presetModalOpen: false,
            qrJobTitle: '',
            qrImageUrl: '',

            showQR(title, slug) {
                this.qrJobTitle = title;
                const encodedUrl = encodeURIComponent('https://asystem.co.id/v3/job_detail.php?slug=' + slug);
                this.qrImageUrl = `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodedUrl}&color=0F52BA`;
                this.qrModalOpen = true;
            },

            openPresetModal() {
                this.presetModalOpen = true;
            },

            applyTemplate(tmpl) {
                if (document.getElementById('job_title')) {
                    document.getElementById('job_title').value = tmpl.title || '';
                }
                if (document.getElementById('job_area') && tmpl.area) {
                    const areaSelect = document.getElementById('job_area');
                    for (let i = 0; i < areaSelect.options.length; i++) {
                        if (tmpl.area.toUpperCase().includes(areaSelect.options[i].value)) {
                            areaSelect.selectedIndex = i;
                            break;
                        }
                    }
                }
                if (document.getElementById('job_quals')) {
                    document.getElementById('job_quals').value = tmpl.quals || '';
                }
                if (document.getElementById('job_skills')) {
                    document.getElementById('job_skills').value = tmpl.skills || '';
                }
                if (document.getElementById('job_exp')) {
                    document.getElementById('job_exp').value = tmpl.exp || '';
                }
                if (document.getElementById('job_desc')) {
                    document.getElementById('job_desc').value = tmpl.desc || '';
                }
            },

            copyJobShareLink(title, area, prinsiple) {
                const text = `*LOWONGAN KERJA TERBARU*\nPosisi: *${title}*\nArea: ${area || '-'}\nPrinsiple: ${prinsiple || '-'}\n\nSilakan apply melalui portal resmi PT Arina Multi Karya.`;
                navigator.clipboard.writeText(text).then(() => {
                    alert('Format pesan lowongan kerja berhasil disalin ke clipboard! Siap di-paste ke WhatsApp.');
                });
            }
        };
    }
</script>
@endsection