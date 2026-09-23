@extends('layouts.public')

@section('title', 'Lamar Posisi: ' . $job->job_title . ' - ASystem Career ESA Groups')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10 space-y-8">
    <!-- TOP NAV / BREADCRUMB -->
    <div class="flex items-center justify-between">
        <a href="{{ route('job.detail', $job->id) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-slate-200/80 text-xs font-bold text-slate-700 hover:text-primary hover:border-primary/40 shadow-sm transition-all">
            <i class="fa-solid fa-arrow-left text-slate-400"></i>
            <span>Kembali ke Detail Lowongan</span>
        </a>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-blue-50 text-primary border border-blue-200/60">
                <i class="fa-solid fa-file-signature text-xs"></i>
                <span>Formulir Pendaftaran Online</span>
            </span>
        </div>
    </div>

    <!-- FLASH MESSAGES -->
    @if(session('success'))
        <div class="bg-gradient-to-r from-emerald-600 to-teal-700 rounded-2xl p-6 sm:p-8 text-white shadow-xl space-y-5">
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-3xl shrink-0">
                    <i class="fa-solid fa-circle-check text-white"></i>
                </div>
                <div class="space-y-1">
                    <h3 class="text-xl font-black tracking-tight">Lamaran Berhasil Dikirim!</h3>
                    <p class="text-xs sm:text-sm text-emerald-100 font-normal leading-relaxed">
                        {{ session('success') }}
                    </p>
                </div>
            </div>

            @if(session('registered_nik'))
                <div class="bg-black/20 backdrop-blur-md rounded-2xl p-5 border border-white/20 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <span class="text-[11px] font-bold text-emerald-200 uppercase tracking-wider block">Kredensial Akun Tes Online Anda</span>
                        <div class="flex flex-wrap items-center gap-5 text-xs font-mono">
                            <span>Username (NIK): <strong class="text-white text-base">{{ session('registered_nik') }}</strong></span>
                            <span>Password: <strong class="text-white text-base">{{ session('registered_pass') }}</strong></span>
                        </div>
                    </div>
                    <a href="{{ route('kandidatportal.index') }}" class="px-5 py-3 rounded-xl bg-white text-emerald-800 hover:bg-emerald-50 text-xs font-bold shadow-md transition-all shrink-0 flex items-center gap-2">
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

    @if(isset($errors) && $errors->any())
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
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- LEFT COLUMN: APPLICATION FORM -->
        <div class="lg:col-span-8 bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
            <!-- Form Header -->
            <div class="bg-gradient-to-r from-blue-950 via-indigo-950 to-slate-900 p-6 sm:p-8 text-white flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center text-2xl shrink-0">
                    <i class="fa-solid fa-user-plus text-sky-400"></i>
                </div>
                <div>
                    <h2 class="text-lg sm:text-xl font-black tracking-tight text-white">Formulir Lamaran Pekerjaan</h2>
                    <p class="text-xs sm:text-sm text-slate-300">Posisi: <span class="font-bold text-sky-300">{{ $job->job_title }}</span> &bull; {{ $job->job_area ?? 'Nasional' }}</p>
                </div>
            </div>

            <!-- Form Body -->
            <form action="{{ route('job.apply.submit', $job->id) }}" method="POST" enctype="multipart/form-data" id="applyJobForm" novalidate class="p-6 sm:p-8 space-y-8">
                @csrf

                <!-- SECTION 1: PAS FOTO -->
                <div class="space-y-3" id="secFoto">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                        <div class="flex items-center gap-2 text-xs font-bold text-slate-700 uppercase tracking-wider">
                            <i class="fa-solid fa-camera text-primary"></i>
                            <span>1. Pas Foto Terbaru</span>
                        </div>
                        <span class="text-[11px] font-semibold text-rose-500">*Wajib</span>
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
                            <p class="text-[11px] text-slate-400">Format: JPG, PNG, atau JPEG. Maksimal 5MB. Pas foto formal berpakaian rapi.</p>
                            <div id="fotoErrorContainer"></div>
                        </div>
                    </div>
                </div>

                <!-- SECTION 2: CV UPLOAD & AI AUTO-FILL -->
                <div class="space-y-3" id="secCv">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                        <div class="flex items-center gap-2 text-xs font-bold text-slate-700 uppercase tracking-wider">
                            <i class="fa-solid fa-file-pdf text-rose-500"></i>
                            <span>2. Berkas Curriculum Vitae (CV)</span>
                        </div>
                        <span class="text-[11px] font-semibold text-rose-500">*Wajib</span>
                    </div>

                    <div id="cvBoxContainer" class="p-4 sm:p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3 transition-all">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="space-y-0.5">
                                <label class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                    <i class="fa-solid fa-cloud-arrow-up text-primary"></i>
                                    <span>Pilih Berkas CV Anda (PDF / JPG / PNG) <span class="text-rose-500">*</span></span>
                                </label>
                                <p class="text-[11px] text-slate-500">Berkas akan disimpan dan dapat diekstrak otomatis oleh asisten AI kami.</p>
                            </div>
                            <button type="button" id="btnAiExtract" onclick="simulateAiExtraction()" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-xs shadow-md shadow-blue-500/20 transition-all flex items-center gap-1.5 shrink-0">
                                <i class="fa-solid fa-wand-magic-sparkles text-amber-300"></i>
                                <span>Isi Otomatis dengan AI</span>
                            </button>
                        </div>

                        <input type="file" id="cvFileInput" name="file_cv" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-primary file:text-white hover:file:bg-primary/90 cursor-pointer">
                        <div id="cvErrorContainer"></div>

                        <div id="aiExtractionAlert" class="hidden p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs flex items-center gap-2">
                            <i class="fa-solid fa-spinner fa-spin text-amber-600"></i>
                            <span id="aiExtractionText">AI sedang menganalisis berkas CV dan mengisi formulir...</span>
                        </div>
                    </div>
                </div>

                <!-- SECTION 3: DATA PRIBADI -->
                <div class="space-y-3" id="secDataPribadi">
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
                            <div id="errorNik"></div>
                        </div>

                        <!-- Nama Lengkap -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-700">Nama Lengkap (Sesuai KTP) <span class="text-rose-500">*</span></label>
                            <input type="text" id="inputNama" name="nama_lengkap" value="{{ old('nama_lengkap') }}" placeholder="Nama lengkap pelamar" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                            <div id="errorNama"></div>
                        </div>

                        <!-- Tanggal Lahir -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-700">Tanggal Lahir <span class="text-rose-500">*</span></label>
                            <input type="date" id="inputTglLahir" name="tgl_lahir" value="{{ old('tgl_lahir') }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                            <span class="text-[10px] text-slate-400">Format tanggal lahir (DDMMYYYY) akan menjadi password akun Anda.</span>
                            <div id="errorTglLahir"></div>
                        </div>

                        <!-- Jenis Kelamin -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-700">Jenis Kelamin <span class="text-rose-500">*</span></label>
                            <div class="grid grid-cols-2 gap-2" id="genderRadioContainer">
                                <label class="flex items-center gap-2 px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer hover:bg-white hover:border-primary transition-all text-xs font-medium text-slate-700">
                                    <input type="radio" id="genderLaki" name="gender" value="Laki-laki" {{ old('gender') === 'Laki-laki' ? 'checked' : '' }} required class="text-primary focus:ring-primary">
                                    <span><i class="fa-solid fa-mars text-blue-500 mr-1"></i> Laki-laki</span>
                                </label>
                                <label class="flex items-center gap-2 px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer hover:bg-white hover:border-primary transition-all text-xs font-medium text-slate-700">
                                    <input type="radio" id="genderPerempuan" name="gender" value="Perempuan" {{ old('gender') === 'Perempuan' ? 'checked' : '' }} required class="text-primary focus:ring-primary">
                                    <span><i class="fa-solid fa-venus text-pink-500 mr-1"></i> Perempuan</span>
                                </label>
                            </div>
                            <div id="errorGender"></div>
                        </div>

                        <!-- Tinggi & Berat Badan -->
                        <div class="grid grid-cols-2 gap-2">
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-700">Tinggi (cm) <span class="text-rose-500">*</span></label>
                                <input type="number" id="inputTinggi" name="tinggi" value="{{ old('tinggi') }}" placeholder="Contoh: 168" min="50" max="250" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                                <div id="errorTinggi"></div>
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-700">Berat (kg) <span class="text-rose-500">*</span></label>
                                <input type="number" id="inputBerat" name="berat" value="{{ old('berat') }}" placeholder="Contoh: 58" min="20" max="300" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                                <div id="errorBerat"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECTION 4: ALAMAT LENGKAP -->
                <div class="space-y-3" id="secAlamat">
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
                            <div id="errorAlamatKtp"></div>
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-700">Alamat Domisili Sekarang <span class="text-rose-500">*</span></label>
                            <textarea id="inputAlamatDomisili" name="alamat_domisili" rows="2" placeholder="Nama jalan, RT/RW, kelurahan, kecamatan tempat tinggal saat ini" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">{{ old('alamat_domisili') }}</textarea>
                            <div id="errorAlamatDomisili"></div>
                        </div>
                    </div>
                </div>

                <!-- SECTION 5: KONTAK & PENDIDIKAN -->
                <div class="space-y-3" id="secKontak">
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
                            <div id="errorWa"></div>
                        </div>

                        <!-- Pendidikan Terakhir -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-700">Pendidikan Terakhir <span class="text-rose-500">*</span></label>
                            <select id="inputPendidikan" name="pendidikan" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                                <option value="" disabled {{ old('pendidikan') ? '' : 'selected' }}>Pilih Jenjang Pendidikan</option>
                                <option value="SMA / SMK" {{ old('pendidikan') == 'SMA / SMK' ? 'selected' : '' }}>SMA / SMK Sederajat</option>
                                <option value="D3" {{ old('pendidikan') == 'D3' ? 'selected' : '' }}>Diploma 3 (D3)</option>
                                <option value="S1" {{ old('pendidikan') == 'S1' ? 'selected' : '' }}>Strata 1 (S1) / Sarjana</option>
                                <option value="S2" {{ old('pendidikan') == 'S2' ? 'selected' : '' }}>Magister (S2)</option>
                                <option value="SMP" {{ old('pendidikan') == 'SMP' ? 'selected' : '' }}>SMP Sederajat</option>
                            </select>
                            <div id="errorPendidikan"></div>
                        </div>
                    </div>
                </div>

                <!-- SECTION 6: PENEMPATAN & WILAYAH DOMISILI -->
                <div class="space-y-4" id="secWilayah">
                    <div class="flex items-center gap-2 text-xs font-bold text-slate-700 uppercase tracking-wider pb-2 border-b border-slate-100">
                        <i class="fa-solid fa-briefcase text-primary"></i>
                        <span>6. Posisi &amp; Wilayah Domisili</span>
                    </div>

                    <!-- Row 1: Posisi Dilamar & Area Penempatan -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500">Posisi Dilamar</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <i class="fa-solid fa-briefcase text-xs"></i>
                                </div>
                                <input type="text" value="{{ $job->job_title }}" readonly class="w-full pl-9 pr-3.5 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 cursor-not-allowed">
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-500">Area Penempatan</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <i class="fa-solid fa-location-dot text-xs text-rose-500"></i>
                                </div>
                                <input type="text" value="{{ $job->job_area ?? 'Nasional' }}" readonly class="w-full pl-9 pr-3.5 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 cursor-not-allowed">
                            </div>
                        </div>
                    </div>

                    <!-- Row 2: Propinsi Domisili & Kota/Kabupaten Domisili -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-700">Propinsi Domisili <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <i class="fa-solid fa-map-location-dot text-xs text-primary"></i>
                                </div>
                                <select id="inputPropinsiDomisili" name="propinsi_domisili" required onchange="onProvinceChange(this.value)" class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                                    <option value="" disabled {{ old('propinsi_domisili') ? '' : 'selected' }}>Pilih Propinsi Domisili</option>
                                    @foreach($provinces as $prov)
                                        <option value="{{ $prov }}" {{ old('propinsi_domisili') == $prov ? 'selected' : '' }}>{{ $prov }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="errorPropinsi"></div>
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-700">Kota/Kabupaten Domisili <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <i class="fa-solid fa-city text-xs text-primary"></i>
                                </div>
                                <select id="inputKotaDomisili" name="kota_domisili" required class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                                    <option value="" disabled selected>Pilih Kota/Kabupaten Domisili</option>
                                </select>
                            </div>
                            <div id="errorKota"></div>
                        </div>
                    </div>

                    <!-- Row 3: Info Lowongan -->
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-700">Info Lowongan <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-bullhorn text-xs text-indigo-500"></i>
                            </div>
                            <select id="inputInfoLowongan" name="info_lowongan" required class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                                <option value="" disabled {{ old('info_lowongan') ? '' : 'selected' }}>Info Lowongan</option>
                                <option value="Tiktok" {{ old('info_lowongan') == 'Tiktok' ? 'selected' : '' }}>Tiktok</option>
                                <option value="Instagram" {{ old('info_lowongan') == 'Instagram' ? 'selected' : '' }}>Instagram</option>
                                <option value="WhatsApp" {{ old('info_lowongan') == 'WhatsApp' ? 'selected' : '' }}>WhatsApp</option>
                                <option value="Web" {{ old('info_lowongan') == 'Web' ? 'selected' : '' }}>Web</option>
                                <option value="Lainnya" {{ old('info_lowongan') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                        </div>
                        <div id="errorInfoLowongan"></div>
                    </div>
                </div>

                <!-- SECTION 7: PENGALAMAN, MOTIVASI & KELEBIHAN -->
                <div class="space-y-3" id="secPengalaman">
                    <div class="flex items-center gap-2 text-xs font-bold text-slate-700 uppercase tracking-wider pb-2 border-b border-slate-100">
                        <i class="fa-solid fa-comment-dots text-indigo-500"></i>
                        <span>7. Pengalaman, Motivasi &amp; Kelebihan Diri</span>
                    </div>

                    <div class="space-y-4">
                        <!-- Ringkasan Pengalaman Kerja -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-700">Ringkasan Pengalaman Kerja <span class="text-rose-500">*</span></label>
                            <textarea id="inputRingkasanPengalaman" name="ringkasan_pengalaman" rows="3" placeholder="Ceritakan riwayat pekerjaan, nama perusahaan, posisi terakhir, atau pengalaman relevan Anda... (Jika belum pernah bekerja, tulis Fresh Graduate)" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">{{ old('ringkasan_pengalaman') }}</textarea>
                            <span class="text-[10px] text-slate-400">Contoh: 1 tahun SPG Kosmetik di PT ABC, 6 bulan Promotor Event. Jika belum memiliki pengalaman kerja, tulis Fresh Graduate.</span>
                            <div id="errorRingkasan"></div>
                        </div>

                        <!-- Motivasi Bekerja -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-700">Motivasi Bekerja <span class="text-rose-500">*</span></label>
                            <textarea id="inputMotivasi" name="motivasi" rows="2" placeholder="Ceritakan motivasi Anda melamar posisi ini..." required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">{{ old('motivasi') }}</textarea>
                            <div id="errorMotivasi"></div>
                        </div>

                        <!-- Kelebihan & Keterampilan Utama Diri -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-700">Kelebihan &amp; Keterampilan Utama Diri <span class="text-rose-500">*</span></label>
                            <textarea id="inputKelebihan" name="kelebihan" rows="2" placeholder="Sebutkan kemampuan, integritas, dan keunggulan Anda..." required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">{{ old('kelebihan') }}</textarea>
                            <div id="errorKelebihan"></div>
                        </div>
                    </div>
                </div>

                <!-- SUBMIT ACTION -->
                <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p class="text-[11px] text-slate-400 text-center sm:text-left">
                        Dengan menekan tombol kirim, Anda menyatakan bahwa seluruh data yang diisikan adalah benar dan dapat dipertanggungjawabkan.
                    </p>

                    <button type="submit" id="btnSubmitApply" class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-primary hover:bg-primary/90 text-white font-black text-xs sm:text-sm shadow-lg shadow-primary/25 hover:shadow-xl hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2 shrink-0">
                        <i class="fa-solid fa-paper-plane" id="btnSubmitIcon"></i>
                        <span id="btnSubmitText">Kirim Lamaran Sekarang</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- RIGHT COLUMN: OVERVIEW & GUIDELINES -->
        <div class="lg:col-span-4 space-y-6 lg:sticky lg:top-24">
            <!-- Job Overview Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-7 shadow-sm space-y-4">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider pb-2 border-b border-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-briefcase text-primary"></i>
                    <span>Ringkasan Lowongan</span>
                </h3>

                <div class="space-y-3.5 text-xs">
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
            <div class="bg-gradient-to-br from-slate-900 to-indigo-950 rounded-2xl p-6 text-white shadow-md space-y-3">
                <div class="flex items-center gap-2 text-xs font-bold text-sky-400 uppercase tracking-wider">
                    <i class="fa-solid fa-headset"></i>
                    <span>Butuh Bantuan Pendaftaran?</span>
                </div>
                <p class="text-[11px] text-slate-300 leading-relaxed">
                    Jika Anda mengalami kendala teknis saat mengunggah berkas atau mengisi data, hubungi tim support rekrutmen kami.
                </p>
                <a href="https://wa.me/{{ $job->creator_whatsapp }}?text=Halo%20Admin%20Rekrutmen,%20saya%20mengalami%20kendala%20saat%20melamar%20posisi%20{{ urlencode($job->job_title) }}" target="_blank" class="w-full py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs flex items-center justify-center gap-2 shadow transition-all">
                    <i class="fa-brands fa-whatsapp text-sm"></i>
                    <span>Hubungi Support WA</span>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
const regionsData = @json($regions ?? []);
const oldCity = @json(old('kota_domisili', ''));

function onProvinceChange(prov) {
    const citySelect = document.getElementById('inputKotaDomisili');
    if (!citySelect) return;

    citySelect.innerHTML = '<option value="" disabled selected>Pilih Kota/Kabupaten Domisili</option>';
    
    if (prov && regionsData[prov]) {
        regionsData[prov].forEach(city => {
            const opt = document.createElement('option');
            opt.value = city;
            opt.textContent = city;
            if (city === oldCity) {
                opt.selected = true;
            }
            citySelect.appendChild(opt);
        });
        citySelect.removeAttribute('disabled');
    } else {
        citySelect.setAttribute('disabled', 'disabled');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const provSelect = document.getElementById('inputPropinsiDomisili');
    if (provSelect && provSelect.value) {
        onProvinceChange(provSelect.value);
    }

    // Server-side errors pop-up if redirected back
    @if(isset($errors) && $errors->any())
        Swal.fire({
            icon: 'error',
            title: '<span class="text-base sm:text-lg font-black text-slate-800">Gagal Mengirimkan Lamaran</span>',
            html: `
                <div class="text-left mt-2 space-y-2">
                    <p class="text-xs text-slate-600 font-medium">Terdapat <b>{{ $errors->count() }} bagian</b> yang belum lengkap atau tidak valid:</p>
                    <div class="max-h-60 overflow-y-auto pr-1 space-y-1.5 p-3 rounded-xl bg-rose-50 border border-rose-200">
                        @foreach($errors->all() as $err)
                            <div class="flex items-start gap-2 text-xs text-rose-700">
                                <i class="fa-solid fa-circle-exclamation mt-0.5 shrink-0 text-rose-500"></i>
                                <span>{{ $err }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            `,
            confirmButtonText: 'Periksa Formulir',
            confirmButtonColor: '#e11d48',
            customClass: {
                popup: 'rounded-3xl p-5 sm:p-6',
                confirmButton: 'rounded-xl px-5 py-2.5 text-xs font-bold'
            }
        });
    @endif
});

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

        // Clear error style
        clearFieldError('fotoPreviewContainer', 'fotoErrorContainer');
    }
}

function syncDomicileAddress(chk) {
    const ktp = document.getElementById('inputAlamatKtp').value;
    const domisili = document.getElementById('inputAlamatDomisili');
    if (chk.checked) {
        domisili.value = ktp;
        domisili.setAttribute('readonly', true);
        domisili.classList.add('bg-slate-100');
        clearFieldError('inputAlamatDomisili', 'errorAlamatDomisili');
    } else {
        domisili.removeAttribute('readonly');
        domisili.classList.remove('bg-slate-100');
    }
}

// Live sync KTP to domicile if checkbox checked
document.getElementById('inputAlamatKtp')?.addEventListener('input', function() {
    const chk = document.getElementById('checkSameAddress');
    if (chk && chk.checked) {
        const domisili = document.getElementById('inputAlamatDomisili');
        if (domisili) {
            domisili.value = this.value;
            clearFieldError('inputAlamatDomisili', 'errorAlamatDomisili');
        }
    }
});

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
            clearFieldError('inputNik', 'errorNik');
        }
        if (!document.getElementById('inputNama').value) {
            document.getElementById('inputNama').value = 'Dimas Ramadhan Pratama';
            clearFieldError('inputNama', 'errorNama');
        }
        if (!document.getElementById('inputAlamatKtp').value) {
            document.getElementById('inputAlamatKtp').value = 'Jl. Merdeka Raya No. 45, RT 03/05, Kebon Jeruk';
            clearFieldError('inputAlamatKtp', 'errorAlamatKtp');
        }
        if (!document.getElementById('inputAlamatDomisili').value) {
            document.getElementById('inputAlamatDomisili').value = 'Jl. Merdeka Raya No. 45, RT 03/05, Kebon Jeruk';
            clearFieldError('inputAlamatDomisili', 'errorAlamatDomisili');
        }
        if (!document.getElementById('inputWa').value) {
            document.getElementById('inputWa').value = '081298765432';
            clearFieldError('inputWa', 'errorWa');
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

// =========================================================================
// VALIDASI MANDATORY SEMUA FIELD DENGAN NOTIFIKASI BAGIAN YANG KURANG
// =========================================================================

function clearFieldError(targetId, errorContainerId) {
    const el = document.getElementById(targetId);
    if (el) {
        el.classList.remove('border-rose-500', 'ring-2', 'ring-rose-200/80', 'bg-rose-50/40', 'text-rose-900');
        if (targetId === 'fotoPreviewContainer') {
            el.classList.remove('border-rose-500', 'bg-rose-50/40');
            el.classList.add('border-slate-300');
        } else if (targetId === 'cvBoxContainer') {
            el.classList.remove('border-rose-500', 'bg-rose-50/40');
            el.classList.add('border-slate-200/80', 'bg-slate-50');
        } else if (targetId === 'genderRadioContainer') {
            el.querySelectorAll('label').forEach(lbl => {
                lbl.classList.remove('border-rose-400', 'bg-rose-50/30');
            });
        }
    }
    if (errorContainerId) {
        const errBox = document.getElementById(errorContainerId);
        if (errBox) errBox.innerHTML = '';
    }
}

function setFieldError(targetId, errorContainerId, message) {
    const el = document.getElementById(targetId);
    if (el) {
        if (targetId === 'fotoPreviewContainer') {
            el.classList.remove('border-slate-300');
            el.classList.add('border-rose-500', 'bg-rose-50/40');
        } else if (targetId === 'cvBoxContainer') {
            el.classList.remove('border-slate-200/80', 'bg-slate-50');
            el.classList.add('border-rose-500', 'bg-rose-50/40');
        } else if (targetId === 'genderRadioContainer') {
            el.querySelectorAll('label').forEach(lbl => {
                lbl.classList.add('border-rose-400', 'bg-rose-50/30');
            });
        } else {
            el.classList.add('border-rose-500', 'ring-2', 'ring-rose-200/80', 'bg-rose-50/40');
        }
    }
    if (errorContainerId) {
        const errBox = document.getElementById(errorContainerId);
        if (errBox) {
            errBox.innerHTML = `
                <div class="field-error-msg flex items-center gap-1.5 text-[11px] font-semibold text-rose-600 mt-1">
                    <i class="fa-solid fa-circle-exclamation text-xs shrink-0"></i>
                    <span>${message}</span>
                </div>
            `;
        }
    }
}

// Pasang auto-clear listeners pada setiap input saat user mulai mengetik / memilih
const fieldInputs = [
    { id: 'inputNik', err: 'errorNik' },
    { id: 'inputNama', err: 'errorNama' },
    { id: 'inputTglLahir', err: 'errorTglLahir' },
    { id: 'inputTinggi', err: 'errorTinggi' },
    { id: 'inputBerat', err: 'errorBerat' },
    { id: 'inputAlamatKtp', err: 'errorAlamatKtp' },
    { id: 'inputAlamatDomisili', err: 'errorAlamatDomisili' },
    { id: 'inputWa', err: 'errorWa' },
    { id: 'inputPendidikan', err: 'errorPendidikan' },
    { id: 'inputPropinsiDomisili', err: 'errorPropinsi' },
    { id: 'inputKotaDomisili', err: 'errorKota' },
    { id: 'inputInfoLowongan', err: 'errorInfoLowongan' },
    { id: 'inputRingkasanPengalaman', err: 'errorRingkasan' },
    { id: 'inputMotivasi', err: 'errorMotivasi' },
    { id: 'inputKelebihan', err: 'errorKelebihan' }
];

fieldInputs.forEach(f => {
    const el = document.getElementById(f.id);
    if (el) {
        el.addEventListener('input', () => clearFieldError(f.id, f.err));
        el.addEventListener('change', () => clearFieldError(f.id, f.err));
    }
});

// Listener khusus file CV
document.getElementById('cvFileInput')?.addEventListener('change', function() {
    if (this.files && this.files.length > 0) {
        clearFieldError('cvBoxContainer', 'cvErrorContainer');
    }
});

// Listener khusus gender radio
document.querySelectorAll('input[name="gender"]').forEach(radio => {
    radio.addEventListener('change', () => {
        clearFieldError('genderRadioContainer', 'errorGender');
    });
});

// Handle Form Submission dengan Validasi Komprehensif
document.getElementById('applyJobForm')?.addEventListener('submit', function(e) {
    const errors = [];

    // Helper pendaftaran error
    function registerError(targetId, errorContainerId, sectionName, fieldLabel, message) {
        setFieldError(targetId, errorContainerId, message);
        errors.push({
            targetId,
            section: sectionName,
            field: fieldLabel,
            message: message,
            element: document.getElementById(targetId)
        });
    }

    // 1. Pas Foto
    const fotoInput = document.getElementById('fotoInput');
    const fotoPreviewImg = document.getElementById('fotoPreviewImg');
    const hasFoto = (fotoInput && fotoInput.files && fotoInput.files.length > 0) || (fotoPreviewImg && !fotoPreviewImg.classList.contains('hidden') && fotoPreviewImg.src);
    if (!hasFoto) {
        registerError('fotoPreviewContainer', 'fotoErrorContainer', '1. Pas Foto', 'Pas Foto Terbaru', 'Pas foto formal terbaru wajib diunggah.');
    }

    // 2. Berkas CV
    const cvInput = document.getElementById('cvFileInput');
    const hasCv = cvInput && cvInput.files && cvInput.files.length > 0;
    if (!hasCv) {
        registerError('cvBoxContainer', 'cvErrorContainer', '2. Berkas CV', 'Berkas CV', 'Berkas Curriculum Vitae (CV) wajib dipilih/diunggah.');
    }

    // 3. NIK
    const nik = document.getElementById('inputNik')?.value.trim() || '';
    if (!nik) {
        registerError('inputNik', 'errorNik', '3. Data Pribadi', 'NIK KTP', 'Nomor Induk Kependudukan (NIK) wajib diisi.');
    } else if (!/^\d{16}$/.test(nik)) {
        registerError('inputNik', 'errorNik', '3. Data Pribadi', 'NIK KTP', 'NIK harus terdiri dari tepat 16 digit angka.');
    }

    // 4. Nama Lengkap
    const nama = document.getElementById('inputNama')?.value.trim() || '';
    if (!nama) {
        registerError('inputNama', 'errorNama', '3. Data Pribadi', 'Nama Lengkap', 'Nama Lengkap (sesuai KTP) wajib diisi.');
    }

    // 5. Tanggal Lahir
    const tglLahir = document.getElementById('inputTglLahir')?.value.trim() || '';
    if (!tglLahir) {
        registerError('inputTglLahir', 'errorTglLahir', '3. Data Pribadi', 'Tanggal Lahir', 'Tanggal Lahir wajib diisi.');
    }

    // 6. Jenis Kelamin
    const genderChecked = document.querySelector('input[name="gender"]:checked');
    if (!genderChecked) {
        registerError('genderRadioContainer', 'errorGender', '3. Data Pribadi', 'Jenis Kelamin', 'Jenis Kelamin wajib dipilih.');
    }

    // 7. Tinggi Badan
    const tinggi = parseFloat(document.getElementById('inputTinggi')?.value);
    if (isNaN(tinggi) || tinggi <= 0) {
        registerError('inputTinggi', 'errorTinggi', '3. Data Pribadi', 'Tinggi Badan', 'Tinggi Badan (cm) wajib diisi.');
    } else if (tinggi < 50 || tinggi > 250) {
        registerError('inputTinggi', 'errorTinggi', '3. Data Pribadi', 'Tinggi Badan', 'Tinggi Badan tidak valid (antara 50 - 250 cm).');
    }

    // 8. Berat Badan
    const berat = parseFloat(document.getElementById('inputBerat')?.value);
    if (isNaN(berat) || berat <= 0) {
        registerError('inputBerat', 'errorBerat', '3. Data Pribadi', 'Berat Badan', 'Berat Badan (kg) wajib diisi.');
    } else if (berat < 20 || berat > 300) {
        registerError('inputBerat', 'errorBerat', '3. Data Pribadi', 'Berat Badan', 'Berat Badan tidak valid (antara 20 - 300 kg).');
    }

    // 9. Alamat Sesuai KTP
    const alamatKtp = document.getElementById('inputAlamatKtp')?.value.trim() || '';
    if (!alamatKtp) {
        registerError('inputAlamatKtp', 'errorAlamatKtp', '4. Alamat', 'Alamat KTP', 'Alamat sesuai KTP wajib diisi lengkap.');
    } else if (alamatKtp.length < 5) {
        registerError('inputAlamatKtp', 'errorAlamatKtp', '4. Alamat', 'Alamat KTP', 'Alamat sesuai KTP minimal 5 karakter.');
    }

    // 10. Alamat Domisili Sekarang
    const alamatDomisili = document.getElementById('inputAlamatDomisili')?.value.trim() || '';
    if (!alamatDomisili) {
        registerError('inputAlamatDomisili', 'errorAlamatDomisili', '4. Alamat', 'Alamat Domisili', 'Alamat domisili saat ini wajib diisi.');
    } else if (alamatDomisili.length < 5) {
        registerError('inputAlamatDomisili', 'errorAlamatDomisili', '4. Alamat', 'Alamat Domisili', 'Alamat domisili saat ini minimal 5 karakter.');
    }

    // 11. Nomor WhatsApp
    const noWa = document.getElementById('inputWa')?.value.trim() || '';
    const cleanWa = noWa.replace(/\D/g, '');
    if (!noWa) {
        registerError('inputWa', 'errorWa', '5. Kontak & Pendidikan', 'Nomor WhatsApp', 'Nomor WhatsApp Aktif wajib diisi.');
    } else if (cleanWa.length < 9) {
        registerError('inputWa', 'errorWa', '5. Kontak & Pendidikan', 'Nomor WhatsApp', 'Nomor WhatsApp minimal 9 digit angka.');
    }

    // 12. Pendidikan Terakhir
    const pendidikan = document.getElementById('inputPendidikan')?.value || '';
    if (!pendidikan) {
        registerError('inputPendidikan', 'errorPendidikan', '5. Kontak & Pendidikan', 'Pendidikan Terakhir', 'Pendidikan Terakhir wajib dipilih.');
    }

    // 13. Propinsi Domisili
    const propinsi = document.getElementById('inputPropinsiDomisili')?.value || '';
    if (!propinsi) {
        registerError('inputPropinsiDomisili', 'errorPropinsi', '6. Wilayah Domisili', 'Propinsi Domisili', 'Propinsi Domisili wajib dipilih.');
    }

    // 14. Kota/Kabupaten Domisili
    const kota = document.getElementById('inputKotaDomisili')?.value || '';
    if (!kota) {
        registerError('inputKotaDomisili', 'errorKota', '6. Wilayah Domisili', 'Kota Domisili', 'Kota/Kabupaten Domisili wajib dipilih.');
    }

    // 15. Info Lowongan
    const infoLowongan = document.getElementById('inputInfoLowongan')?.value || '';
    if (!infoLowongan) {
        registerError('inputInfoLowongan', 'errorInfoLowongan', '6. Wilayah Domisili', 'Info Lowongan', 'Sumber Info Lowongan wajib dipilih.');
    }

    // 16. Ringkasan Pengalaman Kerja
    const ringkasan = document.getElementById('inputRingkasanPengalaman')?.value.trim() || '';
    if (!ringkasan) {
        registerError('inputRingkasanPengalaman', 'errorRingkasan', '7. Pengalaman & Motivasi', 'Ringkasan Pengalaman', 'Ringkasan Pengalaman Kerja wajib diisi (jika fresh graduate, tulis Fresh Graduate).');
    } else if (ringkasan.length < 3) {
        registerError('inputRingkasanPengalaman', 'errorRingkasan', '7. Pengalaman & Motivasi', 'Ringkasan Pengalaman', 'Ringkasan Pengalaman Kerja minimal 3 karakter.');
    }

    // 17. Motivasi Bekerja
    const motivasi = document.getElementById('inputMotivasi')?.value.trim() || '';
    if (!motivasi) {
        registerError('inputMotivasi', 'errorMotivasi', '7. Pengalaman & Motivasi', 'Motivasi Bekerja', 'Motivasi Bekerja wajib diisi.');
    } else if (motivasi.length < 3) {
        registerError('inputMotivasi', 'errorMotivasi', '7. Pengalaman & Motivasi', 'Motivasi Bekerja', 'Motivasi Bekerja minimal 3 karakter.');
    }

    // 18. Kelebihan & Keterampilan Utama Diri
    const kelebihan = document.getElementById('inputKelebihan')?.value.trim() || '';
    if (!kelebihan) {
        registerError('inputKelebihan', 'errorKelebihan', '7. Pengalaman & Motivasi', 'Kelebihan Diri', 'Kelebihan & Keterampilan Utama Diri wajib diisi.');
    } else if (kelebihan.length < 3) {
        registerError('inputKelebihan', 'errorKelebihan', '7. Pengalaman & Motivasi', 'Kelebihan Diri', 'Kelebihan Diri minimal 3 karakter.');
    }

    // Jika ada field yang kosong / tidak memenuhi syarat
    if (errors.length > 0) {
        e.preventDefault();

        Swal.fire({
            icon: 'warning',
            title: '<span class="text-base sm:text-lg font-black text-slate-800">Formulir Belum Lengkap!</span>',
            html: `
                <div class="text-left mt-2 space-y-3">
                    <p class="text-xs text-slate-600 leading-relaxed font-normal">
                        Masih ada <b class="text-rose-600 font-bold">${errors.length} bagian wajib</b> yang belum diisi atau belum lengkap. Mohon periksa dan lengkapi bagian berikut:
                    </p>
                    <div class="max-h-60 overflow-y-auto pr-1 space-y-2 rounded-xl bg-slate-50 border border-slate-200 p-2.5">
                        ${errors.map((err, idx) => `
                            <div class="p-2 rounded-lg bg-white border border-rose-100 flex items-start gap-2 shadow-xs">
                                <span class="w-5 h-5 rounded-full bg-rose-500 text-white font-black text-[10px] flex items-center justify-center shrink-0 mt-0.5">${idx + 1}</span>
                                <div class="leading-tight">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-[11px] font-bold text-slate-800">${err.field}</span>
                                        <span class="text-[9px] px-1.5 py-0.2 rounded bg-slate-100 text-slate-500 font-semibold">${err.section}</span>
                                    </div>
                                    <p class="text-[11px] text-rose-600 font-medium mt-0.5">${err.message}</p>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `,
            confirmButtonText: '<i class="fa-solid fa-pen-to-square mr-1"></i> Lengkapi Bagian Ini',
            confirmButtonColor: '#0F52BA',
            customClass: {
                popup: 'rounded-3xl p-5 sm:p-6',
                confirmButton: 'rounded-xl px-5 py-2.5 text-xs font-bold'
            }
        }).then(() => {
            // Scroll ke field error pertama
            if (errors[0] && errors[0].element) {
                errors[0].element.scrollIntoView({ behavior: 'smooth', block: 'center' });
                if (typeof errors[0].element.focus === 'function') {
                    errors[0].element.focus();
                }
            }
        });

        return false;
    }

    // Jika semua valid, tampilkan status loading pada tombol submit
    const btnSubmit = document.getElementById('btnSubmitApply');
    const btnText = document.getElementById('btnSubmitText');
    const btnIcon = document.getElementById('btnSubmitIcon');

    if (btnSubmit) {
        btnSubmit.disabled = true;
        btnSubmit.classList.add('opacity-80', 'cursor-not-allowed');
        if (btnText) btnText.textContent = 'Sedang Mengirimkan Lamaran...';
        if (btnIcon) btnIcon.className = 'fa-solid fa-spinner fa-spin';
    }
});
</script>
@endsection
