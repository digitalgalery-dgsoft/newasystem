@extends('layouts.app')

@section('title', 'Profil Akun Saya - ASystem Cloud')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto" x-data="{ activeTab: '{{ $errors->has('current_password') || $errors->has('password') || $errors->has('password_confirmation') || session('tab') === 'security' ? 'security' : (old('_tab', 'info')) }}', showCurrentPass: false, showNewPass: false, showConfirmPass: false }">

    <!-- 1. HEADER & BREADCRUMBS -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/90 shadow-sm relative overflow-hidden">
        <div class="absolute -right-16 -top-16 w-56 h-56 bg-primary-50 rounded-full blur-2xl pointer-events-none -z-0"></div>
        
        <div class="relative z-10">
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-1.5">
                <a href="{{ route('fitur.index') }}" class="hover:text-primary transition-colors">Beranda</a>
                <span>/</span>
                <span class="text-slate-800 font-bold">Profil Akun</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="fa-solid fa-id-card text-primary"></i>
                <span>Profil Akun Saya</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Kelola data akun, informasi kontak, foto profil, dan keamanan password Anda secara mandiri.
            </p>
        </div>

        <div class="relative z-10 flex items-center gap-3">
            <a href="{{ route('fitur.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-all flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali ke Menu</span>
            </a>
        </div>
    </div>

    @if(isset($errors) && $errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm font-semibold flex items-start gap-3 shadow-sm">
            <div class="w-8 h-8 rounded-xl bg-rose-500 text-white flex items-center justify-center flex-shrink-0 text-sm shadow mt-0.5">
                <i class="fa-solid fa-circle-exclamation"></i>
            </div>
            <div class="flex-1">
                <div class="font-bold">Terjadi Kesalahan Validasi:</div>
                <ul class="list-disc list-inside mt-1 space-y-0.5 text-xs">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- 2. GRID KONTEN UTAMA -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- KOLOM KIRI: KARTU PROFIL & DATA IDENTITAS (4 Kolom) -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- Kartu User Profile Summary -->
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-6 text-center relative overflow-hidden">
                <div class="absolute top-0 left-0 right-0 h-28 bg-gradient-to-r from-primary-600 via-indigo-600 to-primary-800"></div>

                <!-- Avatar dengan Border & Status -->
                <div class="relative inline-block mt-8 mb-4">
                    <div class="relative group">
                        <img id="profileSummaryAvatar" 
                             src="{{ $user->avatar_url }}" 
                             alt="{{ $user->name }}" 
                             class="w-28 h-28 rounded-3xl object-cover ring-4 ring-white shadow-xl bg-white border border-slate-200 mx-auto">
                        <label for="avatarInputDirect" 
                               class="absolute inset-0 bg-black/40 rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center text-white text-xs font-bold cursor-pointer backdrop-blur-[2px]">
                            <i class="fa-solid fa-camera text-xl mb-1"></i>
                            <span>Ganti Foto</span>
                        </label>
                    </div>
                    <span class="absolute bottom-1 right-1 w-5 h-5 rounded-full bg-emerald-500 ring-2 ring-white" title="Akun Aktif"></span>
                </div>

                <h2 class="text-xl font-black text-slate-900 tracking-tight">{{ $user->name }}</h2>
                <p class="text-xs text-slate-500 font-medium mt-0.5 truncate">{{ $user->email }}</p>

                <!-- Role Badge -->
                <div class="mt-3 flex items-center justify-center gap-2 flex-wrap">
                    @php
                        $roleName = match($user->role) {
                            'admin' => 'Administrator Sistem',
                            'karyawan_inhouse' => 'Karyawan Inhouse',
                            'karyawan_ratecard' => 'Karyawan RateCard',
                            'recruiter' => 'Recruiter HR',
                            'head_hr' => 'Head of HR',
                            default => ucfirst(str_replace('_', ' ', $user->role ?? 'User'))
                        };
                    @endphp
                    <span class="px-3 py-1 rounded-full text-[11px] font-extrabold uppercase tracking-wider bg-primary-50 text-primary-700 border border-primary-200 shadow-xs">
                        <i class="fa-solid fa-shield-halved mr-1"></i> {{ $roleName }}
                    </span>
                    @if($user->area)
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                            <i class="fa-solid fa-location-dot text-rose-500 mr-1"></i> {{ $user->area }}
                        </span>
                    @endif
                </div>

                <!-- Progress Kelengkapan Profil -->
                <div class="mt-6 pt-5 border-t border-slate-100 text-left">
                    <div class="flex items-center justify-between text-xs font-bold mb-1.5">
                        <span class="text-slate-600">Kelengkapan Akun</span>
                        <span class="text-primary">{{ $completeness }}%</span>
                    </div>
                    <div class="w-full h-2 rounded-full bg-slate-100 overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-primary to-indigo-600 rounded-full transition-all duration-500" style="width: {{ $completeness }}%"></div>
                    </div>
                </div>

                <!-- Informasi Singkat Detail Akun -->
                <div class="mt-5 pt-4 border-t border-slate-100 space-y-2.5 text-xs text-left">
                    <div class="flex items-center justify-between py-1">
                        <span class="text-slate-400 font-medium flex items-center gap-1.5">
                            <i class="fa-solid fa-phone text-slate-400 text-[11px]"></i> No. WhatsApp
                        </span>
                        <span class="font-bold text-slate-800">{{ $user->phone ?: '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between py-1">
                        <span class="text-slate-400 font-medium flex items-center gap-1.5">
                            <i class="fa-solid fa-briefcase text-slate-400 text-[11px]"></i> Jabatan
                        </span>
                        <span class="font-bold text-slate-800">{{ $user->job_title ?: ($employee?->jabatan ?: '-') }}</span>
                    </div>
                    <div class="flex items-center justify-between py-1">
                        <span class="text-slate-400 font-medium flex items-center gap-1.5">
                            <i class="fa-solid fa-calendar-check text-slate-400 text-[11px]"></i> Terdaftar
                        </span>
                        <span class="font-bold text-slate-800">{{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}</span>
                    </div>
                </div>

                @if($user->avatar)
                    <div class="mt-5 pt-4 border-t border-slate-100">
                        <form action="{{ route('profile.avatar.destroy') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus foto profil dan kembali ke avatar bawaan?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full py-2 px-3 rounded-xl border border-rose-200 text-rose-600 hover:bg-rose-50 text-xs font-bold transition-all flex items-center justify-center gap-1.5">
                                <i class="fa-regular fa-trash-can"></i>
                                <span>Hapus Foto Profil</span>
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            <!-- Kartu Info Sinkronisasi Karyawan (Jika Terhubung) -->
            @if($employee)
                <div class="bg-gradient-to-br from-indigo-900 to-slate-900 text-white rounded-3xl p-6 shadow-md relative overflow-hidden">
                    <div class="absolute -right-8 -bottom-8 w-32 h-32 bg-indigo-500/20 rounded-full blur-xl pointer-events-none"></div>

                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <span class="w-8 h-8 rounded-xl bg-indigo-500/30 border border-indigo-400/30 flex items-center justify-center text-indigo-300 text-xs">
                                <i class="fa-solid fa-link"></i>
                            </span>
                            <div>
                                <h3 class="text-sm font-bold tracking-tight">Data Karyawan Terhubung</h3>
                                <p class="text-[10px] text-indigo-200">Sinkron Master Karyawan</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                            {{ $employee->status ?? 'Aktiv' }}
                        </span>
                    </div>

                    <div class="space-y-2 text-xs divide-y divide-white/10">
                        <div class="flex items-center justify-between pt-2">
                            <span class="text-indigo-200">NIK / NIP</span>
                            <span class="font-bold text-white">{{ $employee->nik ?: ($employee->nip ?: '-') }}</span>
                        </div>
                        <div class="flex items-center justify-between pt-2">
                            <span class="text-indigo-200">Jabatan</span>
                            <span class="font-bold text-white">{{ $employee->jabatan ?: '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between pt-2">
                            <span class="text-indigo-200">Prinsiple</span>
                            <span class="font-bold text-white truncate max-w-[150px]" title="{{ $employee->prinsiple }}">{{ $employee->prinsiple ?: '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between pt-2">
                            <span class="text-indigo-200">Area Cover</span>
                            <span class="font-bold text-white">{{ $employee->area ?: '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between pt-2">
                            <span class="text-indigo-200">Tipe Karyawan</span>
                            <span class="font-bold text-white">{{ $employee->tipe_karyawan ?: '-' }}</span>
                        </div>
                    </div>
                </div>
            @endif

        </div>

        <!-- KOLOM KANAN: FORM UPDATE PROFIL & PASSWORD (8 Kolom) -->
        <div class="lg:col-span-8 space-y-6">

            <!-- TAB SELECTOR -->
            <div class="bg-white rounded-2xl p-1.5 border border-slate-200/90 shadow-sm flex items-center gap-1">
                <button @click="activeTab = 'info'" 
                        type="button"
                        class="flex-1 py-3 px-3 sm:px-4 rounded-xl font-bold text-xs sm:text-sm transition-all flex items-center justify-center gap-1.5 sm:gap-2"
                        :class="activeTab === 'info' ? 'bg-primary text-white shadow-md shadow-primary/25' : 'text-slate-600 hover:bg-slate-50'">
                    <i class="fa-solid fa-user-pen"></i>
                    <span>Informasi Akun</span>
                </button>
                <button @click="activeTab = 'security'" 
                        type="button"
                        class="flex-1 py-3 px-3 sm:px-4 rounded-xl font-bold text-xs sm:text-sm transition-all flex items-center justify-center gap-1.5 sm:gap-2"
                        :class="activeTab === 'security' ? 'bg-primary text-white shadow-md shadow-primary/25' : 'text-slate-600 hover:bg-slate-50'">
                    <i class="fa-solid fa-lock"></i>
                    <span>Keamanan & Password</span>
                </button>
                <button @click="activeTab = 'theme'" 
                        type="button"
                        class="flex-1 py-3 px-3 sm:px-4 rounded-xl font-bold text-xs sm:text-sm transition-all flex items-center justify-center gap-1.5 sm:gap-2"
                        :class="activeTab === 'theme' ? 'bg-primary text-white shadow-md shadow-primary/25' : 'text-slate-600 hover:bg-slate-50'">
                    <i class="fa-solid fa-palette"></i>
                    <span>Tema & Warna</span>
                </button>
            </div>

            <!-- TAB 1: FORM INFORMASI AKUN & KONTAK -->
            <div x-show="activeTab === 'info'" class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-6 sm:p-8 space-y-6">
                <div>
                    <h3 class="text-lg font-black text-slate-900 tracking-tight flex items-center gap-2">
                        <i class="fa-solid fa-address-card text-primary"></i>
                        <span>Perbarui Data Akun & Kontak</span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-1">
                        Perubahan email, nama, dan no WhatsApp akan otomatis disinkronkan ke Master Karyawan.
                    </p>
                </div>

                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_tab" value="info">

                    <!-- UPLOAD FOTO PROFIL DENGAN LIVE PREVIEW -->
                    <div class="p-5 rounded-2xl bg-slate-50/80 border border-slate-200 space-y-4">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Foto Profil Akun
                        </label>

                        <div class="flex flex-col sm:flex-row items-center gap-5">
                            <!-- Preview Box -->
                            <div class="relative flex-shrink-0">
                                <img id="avatarLivePreview" 
                                     src="{{ $user->avatar_url }}" 
                                     alt="Preview Foto" 
                                     class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl object-cover ring-2 ring-primary/20 shadow-md bg-white border border-slate-200">
                                <span id="previewChangedBadge" class="hidden absolute -top-2 -right-2 px-2 py-0.5 rounded-full bg-amber-500 text-white text-[9px] font-black shadow uppercase">
                                    Baru
                                </span>
                            </div>

                            <!-- Input & Guidance -->
                            <div class="flex-1 w-full space-y-2">
                                <input type="file" 
                                       id="avatarInputDirect" 
                                       name="avatar" 
                                       accept="image/jpeg,image/png,image/jpg,image/webp" 
                                       class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-primary-50 file:text-primary hover:file:bg-primary-100 cursor-pointer border border-slate-200 rounded-xl bg-white focus:outline-none">
                                <p class="text-[11px] text-slate-400">
                                    <i class="fa-solid fa-circle-info text-primary mr-1"></i>
                                    Format: <strong>JPG, PNG, WEBP</strong>. Maksimal <strong>2 MB</strong>. Foto persegi disarankan untuk hasil terbaik.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- INPUT FIELDS -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        
                        <!-- Nama Lengkap -->
                        <div class="sm:col-span-2 space-y-1.5">
                            <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Nama Lengkap <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <i class="fa-solid fa-user"></i>
                                </span>
                                <input type="text" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $user->name) }}" 
                                       required
                                       class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 text-xs sm:text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all bg-white" 
                                       placeholder="Nama Lengkap Anda">
                            </div>
                            @error('name')
                                <p class="text-rose-500 text-[11px] font-semibold mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="space-y-1.5">
                            <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Alamat Email (Login) <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <i class="fa-solid fa-envelope"></i>
                                </span>
                                <input type="email" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email', $user->email) }}" 
                                       required
                                       class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 text-xs sm:text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all bg-white" 
                                       placeholder="nama@perusahaan.com">
                            </div>
                            <p class="text-[10px] text-slate-400">Digunakan sebagai username saat login.</p>
                            @error('email')
                                <p class="text-rose-500 text-[11px] font-semibold mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- No HP / WhatsApp -->
                        <div class="space-y-1.5">
                            <label for="phone" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                No. HP / WhatsApp <span class="text-slate-400 font-normal">(Opsional)</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <i class="fa-brands fa-whatsapp text-emerald-500 text-base"></i>
                                </span>
                                <input type="tel" 
                                       id="phone" 
                                       name="phone" 
                                       value="{{ old('phone', $user->phone) }}" 
                                       class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 text-xs sm:text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all bg-white" 
                                       placeholder="Contoh: 081234567890">
                            </div>
                            <p class="text-[10px] text-slate-400">Nomor aktif untuk kontak dan notifikasi.</p>
                            @error('phone')
                                <p class="text-rose-500 text-[11px] font-semibold mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    <!-- READ-ONLY ROLE & AREA BADGES -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-wrap items-center justify-between gap-3 text-xs">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-lock text-slate-400"></i>
                            <span class="text-slate-500">Pengaturan Hak Akses:</span>
                            <span class="font-bold text-slate-700">{{ $roleName }}</span>
                        </div>
                        <div class="text-[11px] text-slate-400">
                            *Untuk perubahan role akses dan area cover, hubungi Administrator HRD.
                        </div>
                    </div>

                    <!-- TOMBOL SIMPAN -->
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                        <a href="{{ route('fitur.index') }}" class="px-5 py-3 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-all">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-3 rounded-xl bg-primary hover:bg-primary-700 text-white text-xs sm:text-sm font-bold shadow-md shadow-primary/25 hover:shadow-lg transition-all flex items-center gap-2">
                            <i class="fa-solid fa-floppy-disk"></i>
                            <span>Simpan Perubahan Profil</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- TAB 2: FORM KEAMANAN & GANTI PASSWORD -->
            <div x-show="activeTab === 'security'" x-cloak class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-6 sm:p-8 space-y-6">
                <div>
                    <h3 class="text-lg font-black text-slate-900 tracking-tight flex items-center gap-2">
                        <i class="fa-solid fa-shield-halved text-indigo-600"></i>
                        <span>Keamanan & Ganti Password</span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-1">
                        Gunakan password yang kuat dan unik untuk melindungi akun ASystem Anda.
                    </p>
                </div>

                <!-- Info Box Tips Keamanan -->
                <div class="p-4 rounded-2xl bg-indigo-50/70 border border-indigo-100 text-indigo-950 text-xs space-y-1">
                    <div class="font-bold flex items-center gap-2 text-indigo-800">
                        <i class="fa-solid fa-lightbulb"></i>
                        <span>Tips Password Aman</span>
                    </div>
                    <ul class="list-disc list-inside text-[11px] text-indigo-700 space-y-0.5 ml-1">
                        <li>Minimal 6 karakter (kombinasi huruf besar, kecil, angka lebih disarankan).</li>
                        <li>Jangan gunakan informasi yang mudah ditebak seperti tanggal lahir atau nama sendiri.</li>
                        <li>Pastikan Anda mengingat password baru untuk proses login berikutnya.</li>
                    </ul>
                </div>

                <!-- Success Alert Khusus Tab Keamanan -->
                @if(session('success') && session('tab') === 'security')
                    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold flex items-center gap-3 shadow-xs">
                        <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center flex-shrink-0 text-sm shadow">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                        <div>
                            <div class="font-bold text-emerald-900">Password Berhasil Diperbarui!</div>
                            <div class="text-xs text-emerald-700 mt-0.5">{{ session('success') }}</div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('profile.password') }}" method="POST" class="space-y-5">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_tab" value="security">

                    <!-- Password Saat Ini -->
                    <div class="space-y-1.5">
                        <label for="current_password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Password Saat Ini <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-key"></i>
                            </span>
                            <input :type="showCurrentPass ? 'text' : 'password'" 
                                   id="current_password" 
                                   name="current_password" 
                                   required
                                   class="w-full pl-10 pr-11 py-3 rounded-xl border border-slate-200 text-xs sm:text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all bg-white" 
                                   placeholder="Masukkan password Anda saat ini">
                            <button type="button" 
                                    @click="showCurrentPass = !showCurrentPass" 
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition-colors">
                                <i class="fa-solid" :class="showCurrentPass ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                        @error('current_password')
                            <p class="text-rose-500 text-[11px] font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="h-px bg-slate-100 my-2"></div>

                    <!-- Password Baru -->
                    <div class="space-y-1.5">
                        <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Password Baru <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-lock"></i>
                            </span>
                            <input :type="showNewPass ? 'text' : 'password'" 
                                   id="password" 
                                   name="password" 
                                   required
                                   minlength="6"
                                   class="w-full pl-10 pr-11 py-3 rounded-xl border border-slate-200 text-xs sm:text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all bg-white" 
                                   placeholder="Minimal 6 karakter">
                            <button type="button" 
                                    @click="showNewPass = !showNewPass" 
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition-colors">
                                <i class="fa-solid" :class="showNewPass ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-rose-500 text-[11px] font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Konfirmasi Password Baru -->
                    <div class="space-y-1.5">
                        <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Konfirmasi Password Baru <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-lock-open"></i>
                            </span>
                            <input :type="showConfirmPass ? 'text' : 'password'" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   required
                                   minlength="6"
                                   class="w-full pl-10 pr-11 py-3 rounded-xl border border-slate-200 text-xs sm:text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all bg-white" 
                                   placeholder="Ulangi password baru persis sama">
                            <button type="button" 
                                    @click="showConfirmPass = !showConfirmPass" 
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition-colors">
                                <i class="fa-solid" :class="showConfirmPass ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>

                    <!-- TOMBOL PERBARUI PASSWORD -->
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                        <button type="reset" class="px-5 py-3 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-all">
                            Reset
                        </button>
                        <button type="submit" class="px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs sm:text-sm font-bold shadow-md shadow-indigo-600/25 hover:shadow-lg transition-all flex items-center gap-2">
                            <i class="fa-solid fa-key"></i>
                            <span>Perbarui Password</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- TAB 3: PENGATURAN TEMA & WARNA CUSTOM DASHBOARD -->
            <div x-show="activeTab === 'theme'" x-cloak class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-6 sm:p-8 space-y-8" x-data="themePageController()" x-init="initThemeController()">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-5 border-b border-slate-100">
                    <div>
                        <h3 class="text-lg font-black text-slate-900 tracking-tight flex items-center gap-2">
                            <i class="fa-solid fa-palette text-primary"></i>
                            <span>Kustomisasi Nuansa Tema Dashboard</span>
                        </h3>
                        <p class="text-xs text-slate-500 mt-1">
                            Personalisasi gaya visual, mode tampilan, dan warna nuansa dashboard Anda secara mandiri.
                        </p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200 self-start">
                        <i class="fa-solid fa-laptop-code text-xs"></i>
                        <span>Tersimpan di Browser Anda (LocalStorage)</span>
                    </span>
                </div>

                <!-- 1. PILIHAN MODE TAMPILAN (LIGHT / DARK) -->
                <div class="space-y-3">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-circle-half-stroke text-primary"></i>
                        <span>1. Mode Tampilan Sistem</span>
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Mode Terang (Light) -->
                        <button type="button" 
                                @click="setThemeMode('light')"
                                class="p-4 rounded-2xl border-2 text-left transition-all relative overflow-hidden flex items-center gap-4 group"
                                :class="themeMode === 'light' ? 'border-primary bg-primary-50/40 ring-2 ring-primary/20' : 'border-slate-200 hover:border-slate-300 bg-white'">
                            <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center text-xl flex-shrink-0 shadow-xs group-hover:scale-105 transition-transform">
                                <i class="fa-solid fa-sun"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-extrabold text-sm text-slate-900 flex items-center justify-between">
                                    <span>Mode Terang (Light)</span>
                                    <i x-show="themeMode === 'light'" class="fa-solid fa-circle-check text-primary text-base"></i>
                                </div>
                                <p class="text-[11px] text-slate-500 mt-0.5">Latar bersih cerah, optimal untuk aktivitas siang hari.</p>
                            </div>
                        </button>

                        <!-- Mode Gelap (Dark) -->
                        <button type="button" 
                                @click="setThemeMode('dark')"
                                class="p-4 rounded-2xl border-2 text-left transition-all relative overflow-hidden flex items-center gap-4 group"
                                :class="themeMode === 'dark' ? 'border-primary bg-slate-900/10 ring-2 ring-primary/20' : 'border-slate-200 hover:border-slate-300 bg-white'">
                            <div class="w-12 h-12 rounded-xl bg-slate-900 text-indigo-400 flex items-center justify-center text-xl flex-shrink-0 shadow-xs group-hover:scale-105 transition-transform">
                                <i class="fa-solid fa-moon"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-extrabold text-sm text-slate-900 flex items-center justify-between">
                                    <span>Mode Gelap (Dark)</span>
                                    <i x-show="themeMode === 'dark'" class="fa-solid fa-circle-check text-primary text-base"></i>
                                </div>
                                <p class="text-[11px] text-slate-500 mt-0.5">Latar gelap teduh, nyaman untuk mata dan hemat daya.</p>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- 2. PILIHAN WARNA TEMA GELAP (HITAM PEKAT, BIRU NAVY, DARK GREY, SOFT GREY) -->
                <div class="space-y-3 pt-2">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                            <i class="fa-solid fa-paint-roller text-primary"></i>
                            <span>2. Pilihan Warna Tema Gelap</span>
                        </label>
                        <span class="text-[11px] text-slate-400 italic" x-show="themeMode === 'light'">(Pilih palet di bawah untuk mengaktifkan)</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                        <!-- Hitam Pekat -->
                        <button type="button" 
                                @click="setPalette('black')"
                                class="p-4 rounded-2xl border-2 text-left transition-all flex flex-col justify-between relative group"
                                :class="themePalette === 'black' ? 'border-primary bg-slate-50 ring-2 ring-primary/20' : 'border-slate-200 hover:border-slate-300 bg-white'">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-1.5">
                                    <div class="w-6 h-6 rounded-lg bg-[#050505] border border-slate-700 shadow-inner"></div>
                                    <div class="w-6 h-6 rounded-lg bg-[#18181f] border border-slate-700 shadow-inner -ml-3"></div>
                                </div>
                                <i x-show="themePalette === 'black'" class="fa-solid fa-circle-check text-primary text-sm"></i>
                            </div>
                            <div>
                                <div class="font-bold text-xs text-slate-900">Hitam Pekat</div>
                                <div class="text-[10px] text-slate-500 mt-0.5 leading-snug">Pitch Black (#050505) pekat & kontras tajam.</div>
                            </div>
                        </button>

                        <!-- Biru Navy -->
                        <button type="button" 
                                @click="setPalette('navy')"
                                class="p-4 rounded-2xl border-2 text-left transition-all flex flex-col justify-between relative group"
                                :class="themePalette === 'navy' ? 'border-primary bg-slate-50 ring-2 ring-primary/20' : 'border-slate-200 hover:border-slate-300 bg-white'">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-1.5">
                                    <div class="w-6 h-6 rounded-lg bg-[#070d1e] border border-blue-900 shadow-inner"></div>
                                    <div class="w-6 h-6 rounded-lg bg-[#13224d] border border-blue-800 shadow-inner -ml-3"></div>
                                </div>
                                <i x-show="themePalette === 'navy'" class="fa-solid fa-circle-check text-primary text-sm"></i>
                            </div>
                            <div>
                                <div class="font-bold text-xs text-slate-900">Biru Navy</div>
                                <div class="text-[10px] text-slate-500 mt-0.5 leading-snug">Deep Navy (#070d1e) nuansa korporat ESA.</div>
                            </div>
                        </button>

                        <!-- Dark Grey -->
                        <button type="button" 
                                @click="setPalette('dark_grey')"
                                class="p-4 rounded-2xl border-2 text-left transition-all flex flex-col justify-between relative group"
                                :class="themePalette === 'dark_grey' ? 'border-primary bg-slate-50 ring-2 ring-primary/20' : 'border-slate-200 hover:border-slate-300 bg-white'">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-1.5">
                                    <div class="w-6 h-6 rounded-lg bg-[#121316] border border-slate-700 shadow-inner"></div>
                                    <div class="w-6 h-6 rounded-lg bg-[#23252d] border border-slate-600 shadow-inner -ml-3"></div>
                                </div>
                                <i x-show="themePalette === 'dark_grey'" class="fa-solid fa-circle-check text-primary text-sm"></i>
                            </div>
                            <div>
                                <div class="font-bold text-xs text-slate-900">Dark Grey</div>
                                <div class="text-[10px] text-slate-500 mt-0.5 leading-snug">Charcoal (#121316) graphite modern.</div>
                            </div>
                        </button>

                        <!-- Soft Grey -->
                        <button type="button" 
                                @click="setPalette('soft_grey')"
                                class="p-4 rounded-2xl border-2 text-left transition-all flex flex-col justify-between relative group"
                                :class="themePalette === 'soft_grey' ? 'border-primary bg-slate-50 ring-2 ring-primary/20' : 'border-slate-200 hover:border-slate-300 bg-white'">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-1.5">
                                    <div class="w-6 h-6 rounded-lg bg-[#23252b] border border-slate-600 shadow-inner"></div>
                                    <div class="w-6 h-6 rounded-lg bg-[#393c47] border border-slate-500 shadow-inner -ml-3"></div>
                                </div>
                                <i x-show="themePalette === 'soft_grey'" class="fa-solid fa-circle-check text-primary text-sm"></i>
                            </div>
                            <div>
                                <div class="font-bold text-xs text-slate-900">Soft Grey</div>
                                <div class="text-[10px] text-slate-500 mt-0.5 leading-snug">Titanium (#23252b) abu-abu teduh lembut.</div>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- 3. PENGATURAN WARNA CUSTOM DASHBOARD (ACCENT COLOR) -->
                <div class="space-y-4 pt-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-eye-dropper text-primary"></i>
                        <span>3. Pengaturan Nuansa Warna Custom Dashboard</span>
                    </label>

                    <div class="p-5 rounded-2xl bg-slate-50/80 border border-slate-200 space-y-4">
                        <!-- Preset Cepat Warna Populer -->
                        <div>
                            <span class="text-[11px] font-bold text-slate-600 block mb-2">Preset Warna Nuansa Pilihan:</span>
                            <div class="flex items-center gap-2.5 flex-wrap">
                                <template x-for="c in colorPresets" :key="c.hex">
                                    <button type="button" 
                                            @click="setPrimaryColor(c.hex)"
                                            class="flex items-center gap-2 px-3 py-1.5 rounded-xl border transition-all text-xs font-bold shadow-2xs"
                                            :class="primaryColor.toLowerCase() === c.hex.toLowerCase() ? 'border-slate-900 bg-white ring-2 ring-slate-400 shadow-sm' : 'border-slate-200 bg-white hover:border-slate-300'">
                                        <span class="w-4 h-4 rounded-full flex-shrink-0 shadow-xs" :style="'background-color: ' + c.hex"></span>
                                        <span class="text-slate-700" x-text="c.name"></span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <!-- Color Picker Bebas -->
                        <div class="pt-3 border-t border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div class="relative">
                                    <input type="color" 
                                           x-model="primaryColor" 
                                           @input="applyThemeToDOM()"
                                           class="w-12 h-12 rounded-xl cursor-pointer border-2 border-slate-300 p-0.5 bg-white shadow-sm focus:outline-none">
                                </div>
                                <div>
                                    <div class="text-xs font-bold text-slate-800">Pilih Warna Bebas (Color Picker Custom)</div>
                                    <div class="text-[11px] text-slate-500 font-mono" x-text="'Kode Hex: ' + primaryColor.toUpperCase()"></div>
                                </div>
                            </div>
                            <div class="text-[11px] text-slate-400 italic">
                                *Warna diterapkan pada tombol, menu aktif, badge, dan sorotan navigasi dashboard Anda.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. PRATINJAU INTERAKTIF (LIVE PREVIEW) -->
                <div class="space-y-3 pt-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-display text-primary"></i>
                        <span>4. Pratinjau Tampilan Dashboard (Live Interactive Preview)</span>
                    </label>

                    <div class="rounded-2xl border p-4 sm:p-5 transition-all duration-300"
                         :style="previewCardStyle">
                        <div class="flex items-center justify-between pb-3 border-b border-white/10 mb-4">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full bg-rose-500"></div>
                                <div class="w-3 h-3 rounded-full bg-amber-500"></div>
                                <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                                <span class="text-xs font-bold ml-2 opacity-90" x-text="'Simulasi Dashboard (' + (themeMode === 'dark' ? 'Mode Gelap - ' + getPaletteLabel() : 'Mode Terang') + ')'"></span>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold text-white shadow-xs"
                                  :style="'background-color: ' + primaryColor" x-text="primaryColor.toUpperCase()"></span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div class="p-3 rounded-xl border border-white/10" :style="previewInnerBoxStyle">
                                <div class="text-[10px] opacity-60 font-semibold mb-1">Menu Navigasi Aktif</div>
                                <div class="py-1.5 px-2.5 rounded-lg text-white font-bold text-xs flex items-center justify-between shadow-xs"
                                     :style="'background-color: ' + primaryColor">
                                    <span class="flex items-center gap-1.5"><i class="fa-solid fa-house text-[10px]"></i> Beranda</span>
                                    <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                </div>
                            </div>
                            <div class="p-3 rounded-xl border border-white/10" :style="previewInnerBoxStyle">
                                <div class="text-[10px] opacity-60 font-semibold mb-1">Tombol Aksi Utama</div>
                                <button type="button" class="w-full py-1.5 px-3 rounded-lg text-white font-bold text-xs shadow-xs"
                                        :style="'background-color: ' + primaryColor">
                                    <i class="fa-solid fa-plus-circle mr-1"></i> Simpan Data
                                </button>
                            </div>
                            <div class="p-3 rounded-xl border border-white/10" :style="previewInnerBoxStyle">
                                <div class="text-[10px] opacity-60 font-semibold mb-1">Badge & Indikator</div>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="w-2.5 h-2.5 rounded-full" :style="'background-color: ' + primaryColor"></span>
                                    <span class="text-xs font-bold opacity-90">ASystem Cloud Active</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 5. TOMBOL AKSI SIMPAN & RESET -->
                <div class="pt-5 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
                    <button type="button" 
                            @click="resetToDefaultTheme()"
                            class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-all flex items-center gap-2">
                        <i class="fa-solid fa-rotate-left"></i>
                        <span>Reset ke Bawaan Sistem</span>
                    </button>

                    <button type="button" 
                            @click="saveThemeSettings()"
                            class="px-6 py-3 rounded-xl text-white text-xs sm:text-sm font-bold shadow-md hover:shadow-lg transition-all flex items-center gap-2"
                            :style="'background-color: ' + primaryColor">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Terapkan & Simpan Tema</span>
                    </button>
                </div>
            </div>

        </div>

    </div>

</div>

<!-- JAVASCRIPT UNTUK LIVE PREVIEW FOTO PROFIL & CONTROLLER TEMA -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const avatarInput = document.getElementById('avatarInputDirect');
        const livePreview = document.getElementById('avatarLivePreview');
        const summaryAvatar = document.getElementById('profileSummaryAvatar');
        const changedBadge = document.getElementById('previewChangedBadge');

        if (avatarInput) {
            avatarInput.addEventListener('change', function (e) {
                const file = e.target.files[0];
                if (file) {
                    if (file.size > 2 * 1024 * 1024) {
                        alert('Ukuran file gambar terlalu besar! Maksimal 2 MB.');
                        avatarInput.value = '';
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = function (event) {
                        if (livePreview) livePreview.src = event.target.result;
                        if (summaryAvatar) summaryAvatar.src = event.target.result;
                        if (changedBadge) changedBadge.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    });

    function themePageController() {
        return {
            themeMode: localStorage.getItem('asystem_theme_mode') || 'light',
            themePalette: localStorage.getItem('asystem_theme_palette') || 'navy',
            primaryColor: localStorage.getItem('asystem_primary_color') || '#0F52BA',
            colorPresets: [
                { name: 'Biru ESA', hex: '#0F52BA' },
                { name: 'Indigo', hex: '#4F46E5' },
                { name: 'Emerald', hex: '#059669' },
                { name: 'Purple', hex: '#7C3AED' },
                { name: 'Ruby Rose', hex: '#E11D48' },
                { name: 'Amber Gold', hex: '#D97706' },
                { name: 'Teal Ocean', hex: '#0891B2' },
            ],

            initThemeController() {
                this.themeMode = localStorage.getItem('asystem_theme_mode') || 'light';
                this.themePalette = localStorage.getItem('asystem_theme_palette') || 'navy';
                this.primaryColor = localStorage.getItem('asystem_primary_color') || '#0F52BA';

                window.addEventListener('asystemThemeChanged', (e) => {
                    if (e.detail) {
                        this.themeMode = e.detail.theme;
                        this.themePalette = e.detail.palette;
                        this.primaryColor = e.detail.primary;
                    }
                });
            },

            setThemeMode(mode) {
                this.themeMode = mode;
                this.applyThemeToDOM();
            },

            setPalette(pal) {
                this.themePalette = pal;
                if (this.themeMode !== 'dark') {
                    this.themeMode = 'dark';
                }
                this.applyThemeToDOM();
            },

            setPrimaryColor(hex) {
                this.primaryColor = hex;
                this.applyThemeToDOM();
            },

            getPaletteLabel() {
                const labels = {
                    'black': 'Hitam Pekat',
                    'navy': 'Biru Navy',
                    'dark_grey': 'Dark Grey',
                    'soft_grey': 'Soft Grey'
                };
                return labels[this.themePalette] || 'Biru Navy';
            },

            get previewCardStyle() {
                if (this.themeMode === 'light') {
                    return 'background-color: #f8fafc; color: #1e293b; border-color: #e2e8f0;';
                }
                const bgMap = {
                    'black': 'background-color: #09090b; color: #f8fafc; border-color: #27272a;',
                    'navy': 'background-color: #070d1e; color: #f1f5f9; border-color: #213775;',
                    'dark_grey': 'background-color: #121316; color: #f1f5f9; border-color: #383b48;',
                    'soft_grey': 'background-color: #23252b; color: #f8fafc; border-color: #525666;'
                };
                return bgMap[this.themePalette] || bgMap['navy'];
            },

            get previewInnerBoxStyle() {
                if (this.themeMode === 'light') {
                    return 'background-color: #ffffff; border-color: #e2e8f0;';
                }
                const boxMap = {
                    'black': 'background-color: #121215; border-color: #26262e;',
                    'navy': 'background-color: #13224d; border-color: #213775;',
                    'dark_grey': 'background-color: #23252d; border-color: #383b48;',
                    'soft_grey': 'background-color: #393c47; border-color: #525666;'
                };
                return boxMap[this.themePalette] || boxMap['navy'];
            },

            applyThemeToDOM() {
                document.documentElement.setAttribute('data-theme', this.themeMode);
                document.documentElement.setAttribute('data-palette', this.themePalette);
                document.documentElement.style.setProperty('--color-primary', this.primaryColor);
                if (this.themeMode === 'dark') {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
                if (typeof updateThemeUI === 'function') {
                    updateThemeUI();
                }
            },

            saveThemeSettings() {
                localStorage.setItem('asystem_theme_mode', this.themeMode);
                localStorage.setItem('asystem_theme_palette', this.themePalette);
                localStorage.setItem('asystem_primary_color', this.primaryColor);
                this.applyThemeToDOM();

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Tema Dashboard Disimpan!',
                        text: 'Nuansa tema berhasil diterapkan secara personal.',
                        showConfirmButton: false,
                        timer: 2500,
                        timerProgressBar: true
                    });
                } else {
                    alert('Tema Dashboard berhasil disimpan!');
                }
            },

            resetToDefaultTheme() {
                this.themeMode = 'light';
                this.themePalette = 'navy';
                this.primaryColor = '#0F52BA';
                this.saveThemeSettings();
            }
        };
    }
</script>
@endsection
