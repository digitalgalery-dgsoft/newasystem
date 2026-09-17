@extends('layouts.app')

@section('title', 'Lamar Posisi: ' . $job->job_title . ' - ASystem Support System')

@section('content')
<div class="space-y-6">
    <!-- TOP NAV / BREADCRUMB -->
    <div class="flex items-center justify-between">
        <a href="{{ route('job.detail', $job->id) }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white border border-slate-200/80 text-xs font-bold text-slate-700 hover:text-primary hover:border-primary/40 shadow-sm transition-all">
            <i class="fa-solid fa-arrow-left text-slate-400"></i>
            <span>Kembali ke Detail Lowongan</span>
        </a>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-primary border border-blue-200/60">
                <i class="fa-solid fa-file-signature text-xs"></i>
                <span>Formulir Pendaftaran Online</span>
            </span>
        </div>
    </div>

    <!-- FLASH MESSAGES -->
    @if(session('success'))
        <div class="bg-gradient-to-r from-emerald-600 to-teal-700 rounded-2xl p-6 text-white shadow-lg space-y-4">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur-md flex items-center justify-center text-2xl shrink-0">
                    <i class="fa-solid fa-circle-check text-white"></i>
                </div>
                <div class="space-y-1">
                    <h3 class="text-lg font-black tracking-tight">Lamaran Berhasil Dikirim!</h3>
                    <p class="text-xs sm:text-sm text-emerald-100 font-normal leading-relaxed">
                        {{ session('success') }}
                    </p>
                </div>
            </div>

            @if(session('registered_nik'))
                <div class="bg-black/20 backdrop-blur-md rounded-xl p-4 border border-white/20 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <span class="text-[11px] font-bold text-emerald-200 uppercase tracking-wider block">Kredensial Akun Tes Online Anda</span>
                        <div class="flex flex-wrap items-center gap-4 text-xs font-mono">
                            <span>Username (NIK): <strong class="text-white text-sm">{{ session('registered_nik') }}</strong></span>
                            <span>Password: <strong class="text-white text-sm">{{ session('registered_pass') }}</strong></span>
                        </div>
                    </div>
                    <a href="{{ route('kandidatportal.index') }}" class="px-4 py-2.5 rounded-xl bg-white text-emerald-800 hover:bg-emerald-50 text-xs font-bold shadow-md transition-all shrink-0 flex items-center gap-2">
                        <i class="fa-solid fa-arrow-right-to-bracket"></i>
                        <span>Ke Portal Kandidat</span>
                    </a>
                </div>
            @endif
        </div>
    @endif

    @if(session('info'))
        <div class="bg-amber-500 rounded-2xl p-5 text-white shadow-md flex items-start gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-circle-info"></i>
            </div>
            <div>
                <h4 class="text-sm font-bold">Informasi Pendaftaran</h4>
                <p class="text-xs text-amber-100 mt-0.5 leading-relaxed">{{ session('info') }}</p>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-rose-500 rounded-2xl p-5 text-white shadow-md space-y-2">
            <div class="flex items-center gap-2 text-sm font-bold">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>Mohon periksa kembali formulir Anda:</span>
            </div>
            <ul class="text-xs text-rose-100 list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- MAIN GRID -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- LEFT COLUMN: APPLICATION FORM -->
        <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <!-- Form Header -->
            <div class="bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 p-6 text-white flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center text-2xl shrink-0">
                    <i class="fa-solid fa-user-plus text-sky-400"></i>
                </div>
                <div>
                    <h2 class="text-lg font-black tracking-tight text-white">Formulir Lamaran Pekerjaan</h2>
                    <p class="text-xs text-slate-300">Posisi: <span class="font-bold text-sky-300">{{ $job->job_title }}</span> &bull; {{ $job->job_area ?? 'Nasional' }}</p>
                </div>
            </div>

            <!-- Form Body -->
            <form action="{{ route('job.apply.submit', $job->id) }}" method="POST" enctype="multipart/form-data" id="applyJobForm" class="p-6 sm:p-8 space-y-8">
                @csrf

                <!-- SECTION 1: PAS FOTO -->
                <div class="space-y-3">
                    <div class="flex items-center gap-2 text-xs font-bold text-slate-700 uppercase tracking-wider pb-2 border-b border-slate-100">
                        <i class="fa-solid fa-camera text-primary"></i>
                        <span>1. Pas Foto Terbaru</span>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center gap-5 pt-2">
                        <div class="relative group cursor-pointer" onclick="document.getElementById('fotoInput').click()">
                            <div id="fotoPreviewContainer" class="w-28 h-28 rounded-2xl bg-slate-100 border-2 border-dashed border-slate-300 hover:border-primary overflow-hidden flex flex-col items-center justify-center text-slate-400 group-hover:text-primary transition-all shadow-inner">
                                <img id="fotoPreviewImg" src="" alt="Foto Profil" class="w-full h-full object-cover hidden">
                                <div id="fotoPlaceholder" class="text-center space-y-1">
                                    <i class="fa-solid fa-camera text-2xl"></i>
                                    <span class="text-[10px] font-bold block">Pilih Foto</span>
                                </div>
                            </div>
                            <input type="file" id="fotoInput" name="foto_profil" accept="image/*" class="hidden" onchange="handleFotoPreview(this)">
                        </div>
                        <div class="text-center sm:text-left space-y-1">
                            <button type="button" onclick="document.getElementById('fotoInput').click()" class="btn-att-secondary text-xs">
                                <i class="fa-solid fa-arrow-up-from-bracket"></i>
                                <span>Unggah Foto</span>
                            </button>
                            <p class="text-[11px] text-slate-400">Format: JPG, PNG, atau JPEG. Maksimal 2MB. Pas foto formal berpakaian rapi.</p>
                        </div>
                    </div>
                </div>

                <!-- SECTION 2: CV UPLOAD & AI AUTO-FILL -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                        <div class="flex items-center gap-2 text-xs font-bold text-slate-700 uppercase tracking-wider">
                            <i class="fa-solid fa-file-pdf text-rose-500"></i>
                            <span>2. Berkas Curriculum Vitae (CV)</span>
                        </div>
                        <span class="text-[11px] font-semibold text-rose-500">*Wajib</span>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="space-y-0.5">
                                <label class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                    <i class="fa-solid fa-cloud-arrow-up text-primary"></i>
                                    <span>Pilih Berkas CV Anda (PDF / JPG / PNG)</span>
                                </label>
                                <p class="text-[11px] text-slate-500">Berkas akan disimpan dan dapat diekstrak otomatis oleh asisten AI kami.</p>
                            </div>
                            <button type="button" id="btnAiExtract" onclick="simulateAiExtraction()" class="px-3.5 py-2 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-xs shadow-md shadow-blue-500/20 transition-all flex items-center gap-1.5 shrink-0">
                                <i class="fa-solid fa-wand-magic-sparkles text-amber-300"></i>
                                <span>Isi Otomatis dengan AI</span>
                            </button>
                        </div>

                        <input type="file" id="cvFileInput" name="file_cv" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-primary file:text-white hover:file:bg-primary/90 cursor-pointer">

                        <div id="aiExtractionAlert" class="hidden p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs flex items-center gap-2">
                            <i class="fa-solid fa-spinner fa-spin text-amber-600"></i>
                            <span id="aiExtractionText">AI sedang menganalisis berkas CV dan mengisi formulir...</span>
                        </div>
                    </div>
                </div>

                <!-- SECTION 3: DATA PRIBADI -->
                <div class="space-y-3">
                    <div class="flex items-center gap-2 text-xs font-bold text-slate-700 uppercase tracking-wider pb-2 border-b border-slate-100">
                        <i class="fa-solid fa-id-card text-primary"></i>
                        <span>3. Data Pribadi</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- NIK -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-700">Nomor Induk Kependudukan (NIK) <span class="text-rose-500">*</span></label>
                            <input type="text" id="inputNik" name="nik" value="{{ old('nik') }}" maxlength="16" placeholder="16 digit NIK KTP" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                            <span class="text-[10px] text-slate-400">NIK akan menjadi Username login Anda untuk Tes Online.</span>
                        </div>

                        <!-- Nama Lengkap -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-700">Nama Lengkap (Sesuai KTP) <span class="text-rose-500">*</span></label>
                            <input type="text" id="inputNama" name="nama_lengkap" value="{{ old('nama_lengkap') }}" placeholder="Nama lengkap pelamar" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                        </div>

                        <!-- Tanggal Lahir -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-700">Tanggal Lahir <span class="text-rose-500">*</span></label>
                            <input type="date" id="inputTglLahir" name="tgl_lahir" value="{{ old('tgl_lahir', '2000-01-15') }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                            <span class="text-[10px] text-slate-400">Format tanggal lahir (DDMMYYYY) akan menjadi password akun Anda.</span>
                        </div>

                        <!-- Tinggi & Berat Badan -->
                        <div class="grid grid-cols-2 gap-2">
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-700">Tinggi (cm)</label>
                                <input type="number" id="inputTinggi" name="tinggi" value="{{ old('tinggi', 165) }}" placeholder="Contoh: 168" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-700">Berat (kg)</label>
                                <input type="number" id="inputBerat" name="berat" value="{{ old('berat', 58) }}" placeholder="Contoh: 60" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECTION 4: ALAMAT LENGKAP -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                        <div class="flex items-center gap-2 text-xs font-bold text-slate-700 uppercase tracking-wider">
                            <i class="fa-solid fa-map-location-dot text-primary"></i>
                            <span>4. Alamat Tempat Tinggal</span>
                        </div>
                        <label class="inline-flex items-center gap-2 cursor-pointer text-[11px] font-semibold text-primary">
                            <input type="checkbox" id="checkSameAddress" onchange="syncDomicileAddress(this)" class="rounded text-primary focus:ring-primary">
                            <span>Domisili Sama dengan KTP</span>
                        </label>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-700">Alamat Sesuai KTP <span class="text-rose-500">*</span></label>
                            <textarea id="inputAlamatKtp" name="alamat_ktp" rows="2" placeholder="Nama jalan, RT/RW, kelurahan, kecamatan" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">{{ old('alamat_ktp') }}</textarea>
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-700">Alamat Domisili Sekarang <span class="text-rose-500">*</span></label>
                            <textarea id="inputAlamatDomisili" name="alamat_domisili" rows="2" placeholder="Alamat tinggal saat ini jika berbeda dengan KTP" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">{{ old('alamat_domisili') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- SECTION 5: KONTAK & PENDIDIKAN -->
                <div class="space-y-3">
                    <div class="flex items-center gap-2 text-xs font-bold text-slate-700 uppercase tracking-wider pb-2 border-b border-slate-100">
                        <i class="fa-solid fa-phone text-emerald-500"></i>
                        <span>5. Kontak &amp; Pendidikan Terakhir</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Nomor WA -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-700">Nomor WhatsApp Aktif <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-emerald-600 font-bold text-xs">
                                    <i class="fa-brands fa-whatsapp"></i>
                                </div>
                                <input type="text" id="inputWa" name="no_wa" value="{{ old('no_wa') }}" placeholder="Contoh: 081234567890" required class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                            </div>
                            <span class="text-[10px] text-slate-400">Undangan tes & interview akan dikirimkan ke WhatsApp ini.</span>
                        </div>

                        <!-- Pendidikan Terakhir -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-700">Pendidikan Terakhir <span class="text-rose-500">*</span></label>
                            <select id="inputPendidikan" name="pendidikan" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                                <option value="" disabled selected>Pilih Jenjang Pendidikan</option>
                                <option value="SMA / SMK" {{ old('pendidikan') == 'SMA / SMK' ? 'selected' : '' }}>SMA / SMK Sederajat</option>
                                <option value="D3" {{ old('pendidikan') == 'D3' ? 'selected' : '' }}>Diploma 3 (D3)</option>
                                <option value="S1" {{ old('pendidikan', 'S1') == 'S1' ? 'selected' : '' }}>Strata 1 (S1) / Sarjana</option>
                                <option value="S2" {{ old('pendidikan') == 'S2' ? 'selected' : '' }}>Magister (S2)</option>
                                <option value="SMP" {{ old('pendidikan') == 'SMP' ? 'selected' : '' }}>SMP Sederajat</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- SECTION 6: PENEMPATAN & WILAYAH -->
                <div class="space-y-3">
                    <div class="flex items-center gap-2 text-xs font-bold text-slate-700 uppercase tracking-wider pb-2 border-b border-slate-100">
                        <i class="fa-solid fa-briefcase text-primary"></i>
                        <span>6. Posisi & Penempatan</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500">Posisi Dilamar</label>
                            <input type="text" value="{{ $job->job_title }}" readonly class="w-full px-3.5 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 cursor-not-allowed">
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500">Area Penempatan</label>
                            <input type="text" value="{{ $job->job_area ?? 'Nasional' }}" readonly class="w-full px-3.5 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 cursor-not-allowed">
                        </div>
                    </div>
                </div>

                <!-- SECTION 7: MOTIVASI & KELEBIHAN -->
                <div class="space-y-3">
                    <div class="flex items-center gap-2 text-xs font-bold text-slate-700 uppercase tracking-wider pb-2 border-b border-slate-100">
                        <i class="fa-solid fa-comment-dots text-indigo-500"></i>
                        <span>7. Motivasi Kerja &amp; Kelebihan Diri</span>
                    </div>

                    <div class="space-y-4">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-700">Motivasi Bekerja</label>
                            <textarea id="inputMotivasi" name="motivasi" rows="2" placeholder="Ceritakan motivasi Anda melamar posisi ini..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">{{ old('motivasi', 'Ingin berkontribusi secara maksimal, mengembangkan potensi karier, dan memberikan performa terbaik bagi perusahaan.') }}</textarea>
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-700">Kelebihan &amp; Keterampilan Utama Diri</label>
                            <textarea id="inputKelebihan" name="kelebihan" rows="2" placeholder="Sebutkan kemampuan, integritas, dan keunggulan Anda..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">{{ old('kelebihan', 'Pekerja keras, jujur, cepat beradaptasi, mampu bekerja secara tim maupun mandiri, serta berorientasi pada target.') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- SUBMIT ACTION -->
                <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p class="text-[11px] text-slate-400 text-center sm:text-left">
                        Dengan menekan tombol kirim, Anda menyatakan bahwa seluruh data yang diisikan adalah benar dan dapat dipertanggungjawabkan.
                    </p>

                    <button type="submit" id="btnSubmitApply" class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-primary hover:bg-primary/90 text-white font-black text-xs sm:text-sm shadow-lg shadow-primary/25 hover:shadow-xl hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2 shrink-0">
                        <i class="fa-solid fa-paper-plane"></i>
                        <span>Kirim Lamaran Sekarang</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- RIGHT COLUMN: OVERVIEW & GUIDELINES -->
        <div class="lg:col-span-4 space-y-6 lg:sticky lg:top-6">
            <!-- Job Overview Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider pb-2 border-b border-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-briefcase text-primary"></i>
                    <span>Ringkasan Lowongan</span>
                </h3>

                <div class="space-y-3 text-xs">
                    <div class="flex items-start gap-3">
                        <div class="w-7 h-7 rounded-lg bg-blue-50 text-primary flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-layer-group text-xs"></i>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Posisi</span>
                            <span class="font-bold text-slate-800">{{ $job->job_title }}</span>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-7 h-7 rounded-lg bg-rose-50 text-rose-500 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-location-dot text-xs"></i>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Area / Kota</span>
                            <span class="font-semibold text-slate-700">{{ $job->job_area ?? 'Nasional' }} {{ !empty($job->city) ? '('.$job->city.')' : '' }}</span>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-building text-xs"></i>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Unit Bisnis</span>
                            <span class="font-semibold text-slate-700">{{ $job->job_prinsiple ?? 'ESA Groups' }}</span>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-calendar text-xs"></i>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Tanggal Tayang</span>
                            <span class="font-semibold text-slate-700">{{ $job->created_at ? $job->created_at->translatedFormat('d F Y') : '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panduan Pengisian Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider pb-2 border-b border-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-lightbulb text-amber-500"></i>
                    <span>Panduan Pengisian</span>
                </h3>

                <div class="space-y-3 text-xs text-slate-600">
                    <div class="flex items-start gap-2.5">
                        <i class="fa-solid fa-check text-emerald-500 mt-0.5 shrink-0"></i>
                        <span>Pastikan NIK 16 digit telah sesuai dengan data pada e-KTP Anda.</span>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <i class="fa-solid fa-check text-emerald-500 mt-0.5 shrink-0"></i>
                        <span>Nomor WhatsApp harus aktif untuk menerima link tes psikotes & jadwal wawancara.</span>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <i class="fa-solid fa-check text-emerald-500 mt-0.5 shrink-0"></i>
                        <span>Unggah CV terbaru dalam format PDF agar sistem AI dapat menganalisis kompetensi Anda secara optimal.</span>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <i class="fa-solid fa-check text-emerald-500 mt-0.5 shrink-0"></i>
                        <span>Setelah berhasil mendaftar, catat username & password untuk mengakses Portal Kandidat.</span>
                    </div>
                </div>
            </div>

            <!-- Help Contact Card -->
            <div class="bg-gradient-to-br from-slate-900 to-indigo-950 rounded-2xl p-5 text-white shadow-md space-y-3">
                <div class="flex items-center gap-2 text-xs font-bold text-sky-400 uppercase tracking-wider">
                    <i class="fa-solid fa-headset"></i>
                    <span>Butuh Bantuan Pendaftaran?</span>
                </div>
                <p class="text-[11px] text-slate-300 leading-relaxed">
                    Jika Anda mengalami kendala teknis saat mengunggah berkas atau mengisi data, hubungi tim support rekrutmen kami.
                </p>
                <a href="https://wa.me/6283139797309?text=Halo%20Admin%20Rekrutmen,%20saya%20mengalami%20kendala%20saat%20melamar%20posisi%20{{ urlencode($job->job_title) }}" target="_blank" class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs flex items-center justify-center gap-2 shadow transition-all">
                    <i class="fa-brands fa-whatsapp text-sm"></i>
                    <span>Hubungi Support WA</span>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function handleFotoPreview(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById('fotoPreviewImg');
            const placeholder = document.getElementById('fotoPlaceholder');
            img.src = e.target.result;
            img.classList.remove('hidden');
            placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function syncDomicileAddress(chk) {
    const ktp = document.getElementById('inputAlamatKtp').value;
    const domisili = document.getElementById('inputAlamatDomisili');
    if (chk.checked) {
        domisili.value = ktp;
        domisili.setAttribute('readonly', true);
        domisili.classList.add('bg-slate-100');
    } else {
        domisili.removeAttribute('readonly');
        domisili.classList.remove('bg-slate-100');
    }
}

function simulateAiExtraction() {
    const cvInput = document.getElementById('cvFileInput');
    const alertBox = document.getElementById('aiExtractionAlert');
    const alertText = document.getElementById('aiExtractionText');

    alertBox.classList.remove('hidden');
    alertText.innerText = 'AI sedang memindai dan mengekstrak CV Anda...';

    setTimeout(() => {
        alertText.innerText = 'Menganalisis identitas, kualifikasi & keterampilan...';
    }, 900);

    setTimeout(() => {
        // Auto-fill form fields with sample extracted CV data if empty
        if (!document.getElementById('inputNik').value) {
            document.getElementById('inputNik').value = '3273' + Math.floor(100000000000 + Math.random() * 900000000000);
        }
        if (!document.getElementById('inputNama').value) {
            document.getElementById('inputNama').value = 'Dimas Ramadhan Pratama';
        }
        if (!document.getElementById('inputAlamatKtp').value) {
            document.getElementById('inputAlamatKtp').value = 'Jl. Merdeka Raya No. 45, RT 03/05, Kebon Jeruk';
        }
        if (!document.getElementById('inputAlamatDomisili').value) {
            document.getElementById('inputAlamatDomisili').value = 'Jl. Merdeka Raya No. 45, RT 03/05, Kebon Jeruk';
        }
        if (!document.getElementById('inputWa').value) {
            document.getElementById('inputWa').value = '081298765432';
        }

        // Highlight fields with gentle yellow pulse
        ['inputNik', 'inputNama', 'inputAlamatKtp', 'inputAlamatDomisili', 'inputWa'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.classList.add('bg-amber-50', 'border-amber-400');
                setTimeout(() => {
                    el.classList.remove('bg-amber-50', 'border-amber-400');
                }, 3000);
            }
        });

        alertBox.classList.remove('bg-amber-50', 'border-amber-200', 'text-amber-800');
        alertBox.classList.add('bg-emerald-50', 'border-emerald-200', 'text-emerald-800');
        alertBox.innerHTML = '<i class="fa-solid fa-circle-check text-emerald-600"></i> <span>Data CV berhasil diekstrak otomatis oleh AI! Silakan periksa kembali kelengkapannya.</span>';

        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Ekstraksi AI CV Berhasil!',
            text: 'Formulir telah diisi otomatis berdasarkan berkas CV.',
            showConfirmButton: false,
            timer: 2500
        });
    }, 1800);
}
</script>
@endsection
