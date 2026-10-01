@extends('layouts.public')

@section('title', 'Form Pengajuan Surat Referensi Kerja (Veklaring) - ESA Groups')

@section('content')
<div class="min-h-screen py-10 px-4 sm:px-6 lg:px-8 bg-slate-50/60" x-data="paklaringPublicForm()">
    <div class="max-w-4xl mx-auto space-y-6">

        <!-- Top Brand Header Card -->
        <div class="bg-gradient-to-r from-primary-700 via-primary-600 to-blue-600 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-primary-500/10 relative overflow-hidden">
            <div class="absolute -right-10 -bottom-10 w-56 h-56 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-2">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 backdrop-blur-md text-xs font-bold tracking-wide uppercase">
                        <i class="fa-solid fa-file-contract text-cyan-300"></i>
                        <span>Layanan Surat Keterangan Kerja Online</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight">Form Pengajuan Veklaring</h1>
                    <p class="text-xs sm:text-sm text-blue-100 max-w-xl leading-relaxed">
                        Permohonan Surat Keterangan &amp; Referensi Kerja resmi ESA Groups (PT Arina Multi Karya, PT Alva Karya Perkasa, PT Anugrah Terpercaya Kerja, PT Abadi Berkat Odelia, PT Anugrah Talenta Berkarya).
                    </p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('paklaring.public.check') }}" class="px-4 py-2.5 rounded-xl bg-white/15 hover:bg-white/25 text-white text-xs font-bold border border-white/20 transition-all flex items-center gap-2 backdrop-blur-md shadow-xs">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                        <span>Cek Status Permohonan</span>
                    </a>
                </div>
            </div>
        </div>

        @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2.5">
                <i class="fa-solid fa-triangle-exclamation text-rose-600 text-base"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
        @endif

        @if(isset($errors) && $errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold shadow-xs">
            <div class="flex items-center gap-2 mb-2 font-black">
                <i class="fa-solid fa-circle-exclamation text-rose-600 text-base"></i>
                <span>Mohon periksa kembali isian form Anda:</span>
            </div>
            <ul class="list-disc list-inside space-y-1 text-slate-700 font-medium pl-2">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('paklaring.public.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" @submit.prevent="handleSubmit($event)">
            @csrf

            <!-- Section 1: Data Identitas Diri -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xs space-y-6">
                <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                    <div class="w-10 h-10 rounded-2xl bg-blue-50 text-primary flex items-center justify-center font-black text-sm border border-blue-100">
                        1
                    </div>
                    <div>
                        <h2 class="text-base font-black text-slate-800 tracking-tight">Data Identitas Karyawan</h2>
                        <p class="text-xs text-slate-500">Pastikan NIK dan nama lengkap sesuai dengan Kartu Tanda Penduduk (KTP).</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- NIK -->
                    <div class="sm:col-span-2 space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700">
                            Nomor Induk Kependudukan (NIK KTP) <span class="text-rose-500">*</span>
                        </label>
                        <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                            <div class="relative flex-1">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                    <i class="fa-solid fa-id-card text-xs"></i>
                                </span>
                                <input type="text" 
                                       name="nik" 
                                       id="nik"
                                       x-model="form.nik" 
                                       @input="formatNik()"
                                       maxlength="16" 
                                       required
                                       placeholder="16 digit NIK sesuai KTP"
                                       class="w-full min-h-[44px] pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                            </div>
                            <button type="button" 
                                    @click="lookupNik()"
                                    :disabled="isSearchingNik || form.nik.length < 8"
                                    class="w-full sm:w-auto min-h-[44px] px-5 py-2.5 rounded-xl bg-primary hover:bg-primary-700 disabled:opacity-50 text-white text-xs font-bold transition-all shadow-xs flex items-center justify-center gap-1.5 shrink-0 cursor-pointer">
                                <i class="fa-solid" :class="isSearchingNik ? 'fa-spinner fa-spin' : 'fa-wand-magic-sparkles'"></i>
                                <span>Cek Data NIK</span>
                            </button>
                        </div>
                        <p class="text-[11px] text-slate-400">
                            Tips: Klik <strong>Cek Data NIK</strong> agar sistem mengisi otomatis riwayat kerja Anda dari database.
                        </p>
                    </div>

                    <!-- Nama Lengkap -->
                    <div class="space-y-1.5 sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700">
                            Nama Lengkap Sesuai KTP <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               name="nama_lengkap" 
                               x-model="form.nama_lengkap" 
                               required
                               placeholder="Nama lengkap sesuai KTP"
                               class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all uppercase">
                    </div>

                    <!-- Tempat Lahir -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700">
                            Tempat Lahir <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               name="tempat_lahir" 
                               x-model="form.tempat_lahir" 
                               required
                               placeholder="Contoh: Surabaya"
                               class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all capitalize">
                    </div>

                    <!-- Tanggal Lahir -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700">
                            Tanggal Lahir <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" 
                               name="tgl_lahir" 
                               x-model="form.tgl_lahir" 
                               required
                               class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                    </div>

                    <!-- Jenis Kelamin -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700">
                            Jenis Kelamin <span class="text-rose-500">*</span>
                        </label>
                        <select name="jenis_kelamin" 
                                x-model="form.jenis_kelamin" 
                                required
                                class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>

                    <!-- Nomor WhatsApp Aktif -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700">
                            Nomor WhatsApp Aktif <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 text-xs font-bold">
                                +62 / 0
                            </span>
                            <input type="text" 
                                   name="no_hp" 
                                   x-model="form.no_hp" 
                                   required
                                   placeholder="81234567890"
                                   class="w-full min-h-[44px] pl-16 pr-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                        </div>
                    </div>

                    <!-- Email Aktif -->
                    <div class="space-y-1.5 sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700">
                            Alamat Email Aktif <span class="text-rose-500">*</span>
                        </label>
                        <input type="email" 
                               name="email" 
                               x-model="form.email" 
                               required
                               placeholder="nama@email.com"
                               class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all lowercase">
                    </div>

                    <!-- Alamat KTP -->
                    <div class="sm:col-span-2 space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700">
                            Alamat Lengkap Sesuai KTP <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="alamat" 
                                  x-model="form.alamat" 
                                  rows="2" 
                                  required
                                  placeholder="Jalan, RT/RW, Kelurahan, Kecamatan, Kota/Kabupaten, Provinsi"
                                  class="w-full min-h-[60px] px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"></textarea>
                    </div>
                </div>
            </div>

            <!-- Section 2: Data Riwayat Pekerjaan & Penempatan -->
            <div class="bg-white rounded-2xl sm:rounded-3xl p-5 sm:p-8 border border-slate-200 shadow-xs space-y-5 sm:space-y-6">
                <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-indigo-50 text-indigo-700 flex items-center justify-center font-black text-sm border border-indigo-100 shrink-0">
                        2
                    </div>
                    <div>
                        <h2 class="text-sm sm:text-base font-black text-slate-800 tracking-tight">Riwayat Penempatan &amp; Pekerjaan</h2>
                        <p class="text-[11px] sm:text-xs text-slate-500">Pilih prinsiple rekanan dan cabang area penempatan terakhir Anda.</p>
                    </div>
                </div>

                <!-- Hidden Default Alasan -->
                <input type="hidden" name="alasan" x-model="form.alasan">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                    <!-- Prinsiple / Mitra Rekanan (Searchable Dropdown) -->
                    <div class="space-y-1.5 relative" 
                         x-data="{
                             open: false,
                             search: '',
                             get filtered() {
                                 if (!this.search.trim()) return allPrinciples;
                                 const q = this.search.toLowerCase().trim();
                                 return allPrinciples.filter(p => p.label.toLowerCase().includes(q) || p.name.toLowerCase().includes(q));
                             }
                         }" 
                         @click.outside="open = false"
                         @keydown.escape.window="open = false">
                        <label class="block text-xs font-bold text-slate-700">
                            Prinsiple / Mitra Rekanan Terakhir <span class="text-rose-500">*</span>
                        </label>
                        <input type="hidden" name="prinsiple" x-model="form.prinsiple" required>

                        <!-- Trigger Button (Touch-Friendly Mobile min-h-[44px]) -->
                        <button type="button" 
                                @click="open = !open; if(open) $nextTick(() => $refs.searchPrin.focus())"
                                class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border text-left text-xs font-semibold flex items-center justify-between gap-2 transition-all bg-white cursor-pointer select-none active:scale-[0.99]"
                                :class="open ? 'border-primary ring-2 ring-primary/20 shadow-xs' : (form.prinsiple ? 'border-slate-300 text-slate-800' : 'border-slate-200 text-slate-400')">
                            <span class="truncate" x-text="form.prinsiple ? form.prinsiple : '-- Cari / Pilih Prinsiple Rekanan --'"></span>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <template x-if="form.prinsiple">
                                    <span @click.stop="form.prinsiple = ''; search = ''" 
                                          title="Hapus Pilihan"
                                          class="w-6 h-6 rounded-full hover:bg-slate-100 text-slate-400 hover:text-rose-500 flex items-center justify-center transition-colors">
                                        <i class="fa-solid fa-xmark text-xs"></i>
                                    </span>
                                </template>
                                <i class="fa-solid fa-chevron-down text-[11px] text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180 text-primary' : ''"></i>
                            </div>
                        </button>

                        <!-- Dropdown Panel (Responsive Pop-up with Search) -->
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-1 scale-98"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 translate-y-1 scale-98"
                             class="absolute z-50 left-0 right-0 mt-1.5 bg-white rounded-2xl border border-slate-200 shadow-2xl overflow-hidden"
                             style="display: none;">
                            
                            <!-- Search Bar -->
                            <div class="p-2.5 bg-slate-50/95 border-b border-slate-100">
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 pointer-events-none">
                                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                                    </span>
                                    <input type="text"
                                           x-ref="searchPrin"
                                           x-model="search"
                                           placeholder="Ketik nama prinsiple..."
                                           class="w-full pl-8 pr-8 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary bg-white">
                                    <button type="button" 
                                            x-show="search" 
                                            @click="search = ''; $refs.searchPrin.focus()"
                                            class="absolute inset-y-0 right-0 flex items-center pr-2.5 text-slate-400 hover:text-slate-600">
                                        <i class="fa-solid fa-circle-xmark text-xs"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- List Options (Touch-Friendly min-h-[42px]) -->
                            <div class="max-h-60 sm:max-h-56 overflow-y-auto divide-y divide-slate-50 overscroll-contain">
                                <template x-for="p in filtered" :key="p.name">
                                    <button type="button"
                                            @click="form.prinsiple = p.name; open = false; search = ''"
                                            class="w-full px-3.5 py-3 sm:py-2.5 text-left text-xs font-medium flex items-center justify-between hover:bg-primary-50/70 hover:text-primary transition-colors cursor-pointer select-none active:bg-primary-100 min-h-[42px]"
                                            :class="form.prinsiple === p.name ? 'bg-primary-50 text-primary font-bold' : 'text-slate-700'">
                                        <span class="truncate mr-2" x-text="p.label"></span>
                                        <i x-show="form.prinsiple === p.name" class="fa-solid fa-check text-xs text-primary shrink-0"></i>
                                    </button>
                                </template>
                                <div x-show="filtered.length === 0" class="py-5 px-3 text-center text-xs text-slate-400">
                                    <i class="fa-solid fa-circle-exclamation mr-1 text-slate-300"></i>
                                    Prinsiple tidak ditemukan
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Area / Cabang Penempatan (Searchable Dropdown) -->
                    <div class="space-y-1.5 relative" 
                         x-data="{
                             open: false,
                             search: '',
                             get filtered() {
                                 if (!this.search.trim()) return allAreas;
                                 const q = this.search.toLowerCase().trim();
                                 return allAreas.filter(a => a.toLowerCase().includes(q));
                             }
                         }" 
                         @click.outside="open = false"
                         @keydown.escape.window="open = false">
                        <label class="block text-xs font-bold text-slate-700">
                            Area / Cabang Penempatan Terakhir <span class="text-rose-500">*</span>
                        </label>
                        <input type="hidden" name="area" x-model="form.area" required>

                        <!-- Trigger Button (Touch-Friendly Mobile min-h-[44px]) -->
                        <button type="button" 
                                @click="open = !open; if(open) $nextTick(() => $refs.searchArea.focus())"
                                class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border text-left text-xs font-semibold flex items-center justify-between gap-2 transition-all bg-white cursor-pointer select-none active:scale-[0.99]"
                                :class="open ? 'border-primary ring-2 ring-primary/20 shadow-xs' : (form.area ? 'border-slate-300 text-slate-800' : 'border-slate-200 text-slate-400')">
                            <span class="truncate" x-text="form.area ? form.area : '-- Cari / Pilih Cabang Area --'"></span>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <template x-if="form.area">
                                    <span @click.stop="form.area = ''; search = ''" 
                                          title="Hapus Pilihan"
                                          class="w-6 h-6 rounded-full hover:bg-slate-100 text-slate-400 hover:text-rose-500 flex items-center justify-center transition-colors">
                                        <i class="fa-solid fa-xmark text-xs"></i>
                                    </span>
                                </template>
                                <i class="fa-solid fa-chevron-down text-[11px] text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180 text-primary' : ''"></i>
                            </div>
                        </button>

                        <!-- Dropdown Panel (Responsive Pop-up with Search) -->
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-1 scale-98"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 translate-y-1 scale-98"
                             class="absolute z-50 left-0 right-0 mt-1.5 bg-white rounded-2xl border border-slate-200 shadow-2xl overflow-hidden"
                             style="display: none;">
                            
                            <!-- Search Bar -->
                            <div class="p-2.5 bg-slate-50/95 border-b border-slate-100">
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 pointer-events-none">
                                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                                    </span>
                                    <input type="text"
                                           x-ref="searchArea"
                                           x-model="search"
                                           placeholder="Ketik nama kota / cabang area..."
                                           class="w-full pl-8 pr-8 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary bg-white">
                                    <button type="button" 
                                            x-show="search" 
                                            @click="search = ''; $refs.searchArea.focus()"
                                            class="absolute inset-y-0 right-0 flex items-center pr-2.5 text-slate-400 hover:text-slate-600">
                                        <i class="fa-solid fa-circle-xmark text-xs"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- List Options (Touch-Friendly min-h-[42px]) -->
                            <div class="max-h-60 sm:max-h-56 overflow-y-auto divide-y divide-slate-50 overscroll-contain">
                                <template x-for="ar in filtered" :key="ar">
                                    <button type="button"
                                            @click="form.area = ar; open = false; search = ''"
                                            class="w-full px-3.5 py-3 sm:py-2.5 text-left text-xs font-medium flex items-center justify-between hover:bg-primary-50/70 hover:text-primary transition-colors cursor-pointer select-none active:bg-primary-100 min-h-[42px]"
                                            :class="form.area === ar ? 'bg-primary-50 text-primary font-bold' : 'text-slate-700'">
                                        <span class="truncate mr-2" x-text="ar"></span>
                                        <i x-show="form.area === ar" class="fa-solid fa-check text-xs text-primary shrink-0"></i>
                                    </button>
                                </template>
                                <div x-show="filtered.length === 0" class="py-5 px-3 text-center text-xs text-slate-400">
                                    <i class="fa-solid fa-circle-exclamation mr-1 text-slate-300"></i>
                                    Area tidak ditemukan
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Jabatan Terakhir -->
                    <div class="space-y-1.5 sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700">
                            Jabatan Terakhir <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               name="jabatan" 
                               x-model="form.jabatan" 
                               required
                               placeholder="Contoh: Sales Promotion Girl (SPG), Admin, MD, dll."
                               class="w-full min-h-[44px] px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all capitalize">
                    </div>
                </div>
            </div>

            <!-- Section 3: Unggah Berkas & Lampiran Wajib -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xs space-y-6">
                <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                    <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-700 flex items-center justify-center font-black text-sm border border-amber-100">
                        3
                    </div>
                    <div>
                        <h2 class="text-base font-black text-slate-800 tracking-tight">Dokumen Lampiran Persyaratan</h2>
                        <p class="text-xs text-slate-500">Unggah foto / scan dokumen dalam format JPG, JPEG, PNG, WEBP, atau PDF (maks. 2MB per berkas).</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- 1. Foto KTP -->
                    <div x-data="fileDropzone({ name: 'foto_ktp', isRequired: true, label: '1. Foto KTP Asli', icon: 'fa-id-card', badgeClass: 'bg-primary-50 text-primary border-primary-200' })"
                         class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-2xs space-y-3">
                        <div class="flex items-center justify-between gap-2">
                            <label class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold bg-primary-50 text-primary border border-primary-100">
                                    <i class="fa-solid fa-id-card text-[11px]"></i>
                                </span>
                                <span>1. Foto KTP Asli <span class="text-rose-500">*</span></span>
                            </label>
                            <span class="text-[10px] font-bold text-rose-500 bg-rose-50 px-2 py-0.5 rounded-full border border-rose-100">Wajib</span>
                        </div>
                        <input type="file" name="foto_ktp" x-ref="fileInput" @change="handleFileChange($event)" accept="image/jpeg,image/png,image/jpg,image/webp,application/pdf" class="hidden">
                        
                        <div x-show="!hasFile && !isProcessing"
                             @click="triggerChoose()"
                             @dragover.prevent="handleDragOver($event)"
                             @dragleave.prevent="handleDragLeave($event)"
                             @drop.prevent="handleDrop($event)"
                             :class="isDragging ? 'border-primary bg-primary-50/40 ring-2 ring-primary/20 scale-[1.01]' : 'border-slate-200 hover:border-primary/50 hover:bg-slate-50/60 bg-slate-50/40'"
                             class="border-2 border-dashed rounded-xl p-4 sm:p-5 text-center cursor-pointer transition-all flex flex-col items-center justify-center gap-1.5 select-none">
                            <div class="w-10 h-10 rounded-2xl bg-white border border-slate-200 shadow-2xs flex items-center justify-center text-primary text-base">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                            </div>
                            <div class="text-xs text-slate-700">
                                <span class="font-bold text-primary hover:underline">Klik untuk pilih</span> atau tarik file ke sini
                            </div>
                            <div class="flex items-center gap-1.5 text-[10px] text-slate-400">
                                <span>Maks. 2MB</span>
                                <span>&bull;</span>
                                <span class="text-emerald-600 font-medium"><i class="fa-solid fa-wand-magic-sparkles text-[9px] mr-0.5"></i>Auto Resize</span>
                            </div>
                        </div>

                        <div x-show="isProcessing" class="border-2 border-dashed border-amber-200 bg-amber-50/50 rounded-xl p-5 text-center flex flex-col items-center justify-center gap-2">
                            <i class="fa-solid fa-circle-notch fa-spin text-amber-600 text-xl"></i>
                            <span class="text-xs font-bold text-amber-900">Mengoptimasi berkas...</span>
                        </div>

                        <div x-show="hasFile && !isProcessing" class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <template x-if="fileType === 'image'">
                                    <div class="relative group cursor-pointer shrink-0" @click="previewFullImage()" title="Klik untuk perbesar">
                                        <img :src="fileThumb" class="w-12 h-12 rounded-lg object-cover border border-slate-200 shadow-2xs group-hover:opacity-90 transition-all">
                                        <div class="absolute inset-0 bg-black/30 rounded-lg opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-xs transition-opacity">
                                            <i class="fa-solid fa-magnifying-glass-plus"></i>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="fileType === 'pdf'">
                                    <div class="w-12 h-12 rounded-lg bg-rose-50 border border-rose-200 text-rose-600 flex flex-col items-center justify-center shrink-0">
                                        <i class="fa-solid fa-file-pdf text-xl"></i>
                                        <span class="text-[8px] font-black tracking-wider uppercase mt-0.5">PDF</span>
                                    </div>
                                </template>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-slate-800 truncate" x-text="fileName" :title="fileName"></p>
                                    <div class="flex items-center gap-1.5 mt-1 flex-wrap">
                                        <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-100" x-text="fileSizeFormatted"></span>
                                        <span x-show="fileOriginalSizeFormatted" class="text-[10px] text-slate-400">
                                            (Asli: <s x-text="fileOriginalSizeFormatted"></s>)
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <button type="button" @click="triggerChoose()" title="Ganti Berkas" class="px-2.5 py-1.5 rounded-lg bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 text-xs font-bold transition-all flex items-center gap-1 cursor-pointer">
                                    <i class="fa-solid fa-arrows-rotate text-[11px] text-slate-500"></i>
                                    <span class="hidden sm:inline">Ganti</span>
                                </button>
                                <button type="button" @click="clearFile()" title="Hapus Berkas" class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 border border-rose-100 text-rose-600 flex items-center justify-center transition-all cursor-pointer">
                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Form Request -->
                    <div x-data="fileDropzone({ name: 'form_request', isRequired: true, label: '2. Form Request Veklaring', icon: 'fa-file-pen', badgeClass: 'bg-indigo-50 text-indigo-700 border-indigo-200' })"
                         class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-2xs space-y-3">
                        <div class="flex items-center justify-between gap-2">
                            <label class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                    <i class="fa-solid fa-file-pen text-[11px]"></i>
                                </span>
                                <span>2. Form Request Veklaring <span class="text-rose-500">*</span></span>
                            </label>
                            <span class="text-[10px] font-bold text-rose-500 bg-rose-50 px-2 py-0.5 rounded-full border border-rose-100">Wajib</span>
                        </div>
                        <input type="file" name="form_request" x-ref="fileInput" @change="handleFileChange($event)" accept="image/jpeg,image/png,image/jpg,image/webp,application/pdf" class="hidden">
                        
                        <div x-show="!hasFile && !isProcessing"
                             @click="triggerChoose()"
                             @dragover.prevent="handleDragOver($event)"
                             @dragleave.prevent="handleDragLeave($event)"
                             @drop.prevent="handleDrop($event)"
                             :class="isDragging ? 'border-indigo-500 bg-indigo-50/40 ring-2 ring-indigo-500/20 scale-[1.01]' : 'border-slate-200 hover:border-indigo-400 hover:bg-slate-50/60 bg-slate-50/40'"
                             class="border-2 border-dashed rounded-xl p-4 sm:p-5 text-center cursor-pointer transition-all flex flex-col items-center justify-center gap-1.5 select-none">
                            <div class="w-10 h-10 rounded-2xl bg-white border border-slate-200 shadow-2xs flex items-center justify-center text-indigo-600 text-base">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                            </div>
                            <div class="text-xs text-slate-700">
                                <span class="font-bold text-indigo-600 hover:underline">Klik untuk pilih</span> atau tarik file ke sini
                            </div>
                            <div class="flex items-center gap-1.5 text-[10px] text-slate-400">
                                <span>Maks. 2MB</span>
                                <span>&bull;</span>
                                <span class="text-emerald-600 font-medium"><i class="fa-solid fa-wand-magic-sparkles text-[9px] mr-0.5"></i>Auto Resize</span>
                            </div>
                        </div>

                        <div x-show="isProcessing" class="border-2 border-dashed border-amber-200 bg-amber-50/50 rounded-xl p-5 text-center flex flex-col items-center justify-center gap-2">
                            <i class="fa-solid fa-circle-notch fa-spin text-amber-600 text-xl"></i>
                            <span class="text-xs font-bold text-amber-900">Mengoptimasi berkas...</span>
                        </div>

                        <div x-show="hasFile && !isProcessing" class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <template x-if="fileType === 'image'">
                                    <div class="relative group cursor-pointer shrink-0" @click="previewFullImage()" title="Klik untuk perbesar">
                                        <img :src="fileThumb" class="w-12 h-12 rounded-lg object-cover border border-slate-200 shadow-2xs group-hover:opacity-90 transition-all">
                                        <div class="absolute inset-0 bg-black/30 rounded-lg opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-xs transition-opacity">
                                            <i class="fa-solid fa-magnifying-glass-plus"></i>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="fileType === 'pdf'">
                                    <div class="w-12 h-12 rounded-lg bg-rose-50 border border-rose-200 text-rose-600 flex flex-col items-center justify-center shrink-0">
                                        <i class="fa-solid fa-file-pdf text-xl"></i>
                                        <span class="text-[8px] font-black tracking-wider uppercase mt-0.5">PDF</span>
                                    </div>
                                </template>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-slate-800 truncate" x-text="fileName" :title="fileName"></p>
                                    <div class="flex items-center gap-1.5 mt-1 flex-wrap">
                                        <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-100" x-text="fileSizeFormatted"></span>
                                        <span x-show="fileOriginalSizeFormatted" class="text-[10px] text-slate-400">
                                            (Asli: <s x-text="fileOriginalSizeFormatted"></s>)
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <button type="button" @click="triggerChoose()" title="Ganti Berkas" class="px-2.5 py-1.5 rounded-lg bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 text-xs font-bold transition-all flex items-center gap-1 cursor-pointer">
                                    <i class="fa-solid fa-arrows-rotate text-[11px] text-slate-500"></i>
                                    <span class="hidden sm:inline">Ganti</span>
                                </button>
                                <button type="button" @click="clearFile()" title="Hapus Berkas" class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 border border-rose-100 text-rose-600 flex items-center justify-center transition-all cursor-pointer">
                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Exit Clearance -->
                    <div x-data="fileDropzone({ name: 'exit_cl', isRequired: true, label: '3. Exit Clearance', icon: 'fa-clipboard-check', badgeClass: 'bg-emerald-50 text-emerald-700 border-emerald-200' })"
                         class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-2xs space-y-3">
                        <div class="flex items-center justify-between gap-2">
                            <label class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                    <i class="fa-solid fa-clipboard-check text-[11px]"></i>
                                </span>
                                <span>3. Exit Clearance <span class="text-rose-500">*</span></span>
                            </label>
                            <span class="text-[10px] font-bold text-rose-500 bg-rose-50 px-2 py-0.5 rounded-full border border-rose-100">Wajib</span>
                        </div>
                        <input type="file" name="exit_cl" x-ref="fileInput" @change="handleFileChange($event)" accept="image/jpeg,image/png,image/jpg,image/webp,application/pdf" class="hidden">
                        
                        <div x-show="!hasFile && !isProcessing"
                             @click="triggerChoose()"
                             @dragover.prevent="handleDragOver($event)"
                             @dragleave.prevent="handleDragLeave($event)"
                             @drop.prevent="handleDrop($event)"
                             :class="isDragging ? 'border-emerald-500 bg-emerald-50/40 ring-2 ring-emerald-500/20 scale-[1.01]' : 'border-slate-200 hover:border-emerald-400 hover:bg-slate-50/60 bg-slate-50/40'"
                             class="border-2 border-dashed rounded-xl p-4 sm:p-5 text-center cursor-pointer transition-all flex flex-col items-center justify-center gap-1.5 select-none">
                            <div class="w-10 h-10 rounded-2xl bg-white border border-slate-200 shadow-2xs flex items-center justify-center text-emerald-600 text-base">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                            </div>
                            <div class="text-xs text-slate-700">
                                <span class="font-bold text-emerald-600 hover:underline">Klik untuk pilih</span> atau tarik file ke sini
                            </div>
                            <div class="flex items-center gap-1.5 text-[10px] text-slate-400">
                                <span>Maks. 2MB</span>
                                <span>&bull;</span>
                                <span class="text-emerald-600 font-medium"><i class="fa-solid fa-wand-magic-sparkles text-[9px] mr-0.5"></i>Auto Resize</span>
                            </div>
                        </div>

                        <div x-show="isProcessing" class="border-2 border-dashed border-amber-200 bg-amber-50/50 rounded-xl p-5 text-center flex flex-col items-center justify-center gap-2">
                            <i class="fa-solid fa-circle-notch fa-spin text-amber-600 text-xl"></i>
                            <span class="text-xs font-bold text-amber-900">Mengoptimasi berkas...</span>
                        </div>

                        <div x-show="hasFile && !isProcessing" class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <template x-if="fileType === 'image'">
                                    <div class="relative group cursor-pointer shrink-0" @click="previewFullImage()" title="Klik untuk perbesar">
                                        <img :src="fileThumb" class="w-12 h-12 rounded-lg object-cover border border-slate-200 shadow-2xs group-hover:opacity-90 transition-all">
                                        <div class="absolute inset-0 bg-black/30 rounded-lg opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-xs transition-opacity">
                                            <i class="fa-solid fa-magnifying-glass-plus"></i>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="fileType === 'pdf'">
                                    <div class="w-12 h-12 rounded-lg bg-rose-50 border border-rose-200 text-rose-600 flex flex-col items-center justify-center shrink-0">
                                        <i class="fa-solid fa-file-pdf text-xl"></i>
                                        <span class="text-[8px] font-black tracking-wider uppercase mt-0.5">PDF</span>
                                    </div>
                                </template>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-slate-800 truncate" x-text="fileName" :title="fileName"></p>
                                    <div class="flex items-center gap-1.5 mt-1 flex-wrap">
                                        <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-100" x-text="fileSizeFormatted"></span>
                                        <span x-show="fileOriginalSizeFormatted" class="text-[10px] text-slate-400">
                                            (Asli: <s x-text="fileOriginalSizeFormatted"></s>)
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <button type="button" @click="triggerChoose()" title="Ganti Berkas" class="px-2.5 py-1.5 rounded-lg bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 text-xs font-bold transition-all flex items-center gap-1 cursor-pointer">
                                    <i class="fa-solid fa-arrows-rotate text-[11px] text-slate-500"></i>
                                    <span class="hidden sm:inline">Ganti</span>
                                </button>
                                <button type="button" @click="clearFile()" title="Hapus Berkas" class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 border border-rose-100 text-rose-600 flex items-center justify-center transition-all cursor-pointer">
                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Surat Pengunduran Diri -->
                    <div x-data="fileDropzone({ name: 'pengunduran_diri', isRequired: true, label: '4. Surat Pengunduran Diri', icon: 'fa-file-signature', badgeClass: 'bg-purple-50 text-purple-700 border-purple-200' })"
                         class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-2xs space-y-3">
                        <div class="flex items-center justify-between gap-2">
                            <label class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold bg-purple-50 text-purple-700 border border-purple-100">
                                    <i class="fa-solid fa-file-signature text-[11px]"></i>
                                </span>
                                <span>4. Surat Pengunduran Diri <span class="text-rose-500">*</span></span>
                            </label>
                            <span class="text-[10px] font-bold text-rose-500 bg-rose-50 px-2 py-0.5 rounded-full border border-rose-100">Wajib</span>
                        </div>
                        <input type="file" name="pengunduran_diri" x-ref="fileInput" @change="handleFileChange($event)" accept="image/jpeg,image/png,image/jpg,image/webp,application/pdf" class="hidden">
                        
                        <div x-show="!hasFile && !isProcessing"
                             @click="triggerChoose()"
                             @dragover.prevent="handleDragOver($event)"
                             @dragleave.prevent="handleDragLeave($event)"
                             @drop.prevent="handleDrop($event)"
                             :class="isDragging ? 'border-purple-500 bg-purple-50/40 ring-2 ring-purple-500/20 scale-[1.01]' : 'border-slate-200 hover:border-purple-400 hover:bg-slate-50/60 bg-slate-50/40'"
                             class="border-2 border-dashed rounded-xl p-4 sm:p-5 text-center cursor-pointer transition-all flex flex-col items-center justify-center gap-1.5 select-none">
                            <div class="w-10 h-10 rounded-2xl bg-white border border-slate-200 shadow-2xs flex items-center justify-center text-purple-600 text-base">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                            </div>
                            <div class="text-xs text-slate-700">
                                <span class="font-bold text-purple-600 hover:underline">Klik untuk pilih</span> atau tarik file ke sini
                            </div>
                            <div class="flex items-center gap-1.5 text-[10px] text-slate-400">
                                <span>Maks. 2MB</span>
                                <span>&bull;</span>
                                <span class="text-emerald-600 font-medium"><i class="fa-solid fa-wand-magic-sparkles text-[9px] mr-0.5"></i>Auto Resize</span>
                            </div>
                        </div>

                        <div x-show="isProcessing" class="border-2 border-dashed border-amber-200 bg-amber-50/50 rounded-xl p-5 text-center flex flex-col items-center justify-center gap-2">
                            <i class="fa-solid fa-circle-notch fa-spin text-amber-600 text-xl"></i>
                            <span class="text-xs font-bold text-amber-900">Mengoptimasi berkas...</span>
                        </div>

                        <div x-show="hasFile && !isProcessing" class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <template x-if="fileType === 'image'">
                                    <div class="relative group cursor-pointer shrink-0" @click="previewFullImage()" title="Klik untuk perbesar">
                                        <img :src="fileThumb" class="w-12 h-12 rounded-lg object-cover border border-slate-200 shadow-2xs group-hover:opacity-90 transition-all">
                                        <div class="absolute inset-0 bg-black/30 rounded-lg opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-xs transition-opacity">
                                            <i class="fa-solid fa-magnifying-glass-plus"></i>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="fileType === 'pdf'">
                                    <div class="w-12 h-12 rounded-lg bg-rose-50 border border-rose-200 text-rose-600 flex flex-col items-center justify-center shrink-0">
                                        <i class="fa-solid fa-file-pdf text-xl"></i>
                                        <span class="text-[8px] font-black tracking-wider uppercase mt-0.5">PDF</span>
                                    </div>
                                </template>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-slate-800 truncate" x-text="fileName" :title="fileName"></p>
                                    <div class="flex items-center gap-1.5 mt-1 flex-wrap">
                                        <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-100" x-text="fileSizeFormatted"></span>
                                        <span x-show="fileOriginalSizeFormatted" class="text-[10px] text-slate-400">
                                            (Asli: <s x-text="fileOriginalSizeFormatted"></s>)
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <button type="button" @click="triggerChoose()" title="Ganti Berkas" class="px-2.5 py-1.5 rounded-lg bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 text-xs font-bold transition-all flex items-center gap-1 cursor-pointer">
                                    <i class="fa-solid fa-arrows-rotate text-[11px] text-slate-500"></i>
                                    <span class="hidden sm:inline">Ganti</span>
                                </button>
                                <button type="button" @click="clearFile()" title="Hapus Berkas" class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 border border-rose-100 text-rose-600 flex items-center justify-center transition-all cursor-pointer">
                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- 5. Kartu BPJS -->
                    <div x-data="fileDropzone({ name: 'kartu_bpjs', isRequired: true, label: '5. Kartu BPJS Ketenagakerjaan', icon: 'fa-shield-halved', badgeClass: 'bg-cyan-50 text-cyan-700 border-cyan-200' })"
                         class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-2xs space-y-3">
                        <div class="flex items-center justify-between gap-2">
                            <label class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold bg-cyan-50 text-cyan-700 border border-cyan-100">
                                    <i class="fa-solid fa-shield-halved text-[11px]"></i>
                                </span>
                                <span>5. Kartu BPJS Ketenagakerjaan <span class="text-rose-500">*</span></span>
                            </label>
                            <span class="text-[10px] font-bold text-rose-500 bg-rose-50 px-2 py-0.5 rounded-full border border-rose-100">Wajib</span>
                        </div>
                        <input type="file" name="kartu_bpjs" x-ref="fileInput" @change="handleFileChange($event)" accept="image/jpeg,image/png,image/jpg,image/webp,application/pdf" class="hidden">
                        
                        <div x-show="!hasFile && !isProcessing"
                             @click="triggerChoose()"
                             @dragover.prevent="handleDragOver($event)"
                             @dragleave.prevent="handleDragLeave($event)"
                             @drop.prevent="handleDrop($event)"
                             :class="isDragging ? 'border-cyan-500 bg-cyan-50/40 ring-2 ring-cyan-500/20 scale-[1.01]' : 'border-slate-200 hover:border-cyan-400 hover:bg-slate-50/60 bg-slate-50/40'"
                             class="border-2 border-dashed rounded-xl p-4 sm:p-5 text-center cursor-pointer transition-all flex flex-col items-center justify-center gap-1.5 select-none">
                            <div class="w-10 h-10 rounded-2xl bg-white border border-slate-200 shadow-2xs flex items-center justify-center text-cyan-600 text-base">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                            </div>
                            <div class="text-xs text-slate-700">
                                <span class="font-bold text-cyan-600 hover:underline">Klik untuk pilih</span> atau tarik file ke sini
                            </div>
                            <div class="flex items-center gap-1.5 text-[10px] text-slate-400">
                                <span>Maks. 2MB</span>
                                <span>&bull;</span>
                                <span class="text-emerald-600 font-medium"><i class="fa-solid fa-wand-magic-sparkles text-[9px] mr-0.5"></i>Auto Resize</span>
                            </div>
                        </div>

                        <div x-show="isProcessing" class="border-2 border-dashed border-amber-200 bg-amber-50/50 rounded-xl p-5 text-center flex flex-col items-center justify-center gap-2">
                            <i class="fa-solid fa-circle-notch fa-spin text-amber-600 text-xl"></i>
                            <span class="text-xs font-bold text-amber-900">Mengoptimasi berkas...</span>
                        </div>

                        <div x-show="hasFile && !isProcessing" class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <template x-if="fileType === 'image'">
                                    <div class="relative group cursor-pointer shrink-0" @click="previewFullImage()" title="Klik untuk perbesar">
                                        <img :src="fileThumb" class="w-12 h-12 rounded-lg object-cover border border-slate-200 shadow-2xs group-hover:opacity-90 transition-all">
                                        <div class="absolute inset-0 bg-black/30 rounded-lg opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-xs transition-opacity">
                                            <i class="fa-solid fa-magnifying-glass-plus"></i>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="fileType === 'pdf'">
                                    <div class="w-12 h-12 rounded-lg bg-rose-50 border border-rose-200 text-rose-600 flex flex-col items-center justify-center shrink-0">
                                        <i class="fa-solid fa-file-pdf text-xl"></i>
                                        <span class="text-[8px] font-black tracking-wider uppercase mt-0.5">PDF</span>
                                    </div>
                                </template>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-slate-800 truncate" x-text="fileName" :title="fileName"></p>
                                    <div class="flex items-center gap-1.5 mt-1 flex-wrap">
                                        <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-100" x-text="fileSizeFormatted"></span>
                                        <span x-show="fileOriginalSizeFormatted" class="text-[10px] text-slate-400">
                                            (Asli: <s x-text="fileOriginalSizeFormatted"></s>)
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <button type="button" @click="triggerChoose()" title="Ganti Berkas" class="px-2.5 py-1.5 rounded-lg bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 text-xs font-bold transition-all flex items-center gap-1 cursor-pointer">
                                    <i class="fa-solid fa-arrows-rotate text-[11px] text-slate-500"></i>
                                    <span class="hidden sm:inline">Ganti</span>
                                </button>
                                <button type="button" @click="clearFile()" title="Hapus Berkas" class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 border border-rose-100 text-rose-600 flex items-center justify-center transition-all cursor-pointer">
                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- 6. Berita Acara Serah Terima -->
                    <div x-data="fileDropzone({ name: 'serah_terima', isRequired: true, label: '6. Berita Acara Serah Terima Aset', icon: 'fa-handshake', badgeClass: 'bg-teal-50 text-teal-700 border-teal-200' })"
                         class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-2xs space-y-3">
                        <div class="flex items-center justify-between gap-2">
                            <label class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold bg-teal-50 text-teal-700 border border-teal-100">
                                    <i class="fa-solid fa-handshake text-[11px]"></i>
                                </span>
                                <span>6. Berita Acara Serah Terima Aset <span class="text-rose-500">*</span></span>
                            </label>
                            <span class="text-[10px] font-bold text-rose-500 bg-rose-50 px-2 py-0.5 rounded-full border border-rose-100">Wajib</span>
                        </div>
                        <input type="file" name="serah_terima" x-ref="fileInput" @change="handleFileChange($event)" accept="image/jpeg,image/png,image/jpg,image/webp,application/pdf" class="hidden">
                        
                        <div x-show="!hasFile && !isProcessing"
                             @click="triggerChoose()"
                             @dragover.prevent="handleDragOver($event)"
                             @dragleave.prevent="handleDragLeave($event)"
                             @drop.prevent="handleDrop($event)"
                             :class="isDragging ? 'border-teal-500 bg-teal-50/40 ring-2 ring-teal-500/20 scale-[1.01]' : 'border-slate-200 hover:border-teal-400 hover:bg-slate-50/60 bg-slate-50/40'"
                             class="border-2 border-dashed rounded-xl p-4 sm:p-5 text-center cursor-pointer transition-all flex flex-col items-center justify-center gap-1.5 select-none">
                            <div class="w-10 h-10 rounded-2xl bg-white border border-slate-200 shadow-2xs flex items-center justify-center text-teal-600 text-base">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                            </div>
                            <div class="text-xs text-slate-700">
                                <span class="font-bold text-teal-600 hover:underline">Klik untuk pilih</span> atau tarik file ke sini
                            </div>
                            <div class="flex items-center gap-1.5 text-[10px] text-slate-400">
                                <span>Maks. 2MB</span>
                                <span>&bull;</span>
                                <span class="text-emerald-600 font-medium"><i class="fa-solid fa-wand-magic-sparkles text-[9px] mr-0.5"></i>Auto Resize</span>
                            </div>
                        </div>

                        <div x-show="isProcessing" class="border-2 border-dashed border-amber-200 bg-amber-50/50 rounded-xl p-5 text-center flex flex-col items-center justify-center gap-2">
                            <i class="fa-solid fa-circle-notch fa-spin text-amber-600 text-xl"></i>
                            <span class="text-xs font-bold text-amber-900">Mengoptimasi berkas...</span>
                        </div>

                        <div x-show="hasFile && !isProcessing" class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <template x-if="fileType === 'image'">
                                    <div class="relative group cursor-pointer shrink-0" @click="previewFullImage()" title="Klik untuk perbesar">
                                        <img :src="fileThumb" class="w-12 h-12 rounded-lg object-cover border border-slate-200 shadow-2xs group-hover:opacity-90 transition-all">
                                        <div class="absolute inset-0 bg-black/30 rounded-lg opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-xs transition-opacity">
                                            <i class="fa-solid fa-magnifying-glass-plus"></i>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="fileType === 'pdf'">
                                    <div class="w-12 h-12 rounded-lg bg-rose-50 border border-rose-200 text-rose-600 flex flex-col items-center justify-center shrink-0">
                                        <i class="fa-solid fa-file-pdf text-xl"></i>
                                        <span class="text-[8px] font-black tracking-wider uppercase mt-0.5">PDF</span>
                                    </div>
                                </template>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-slate-800 truncate" x-text="fileName" :title="fileName"></p>
                                    <div class="flex items-center gap-1.5 mt-1 flex-wrap">
                                        <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-100" x-text="fileSizeFormatted"></span>
                                        <span x-show="fileOriginalSizeFormatted" class="text-[10px] text-slate-400">
                                            (Asli: <s x-text="fileOriginalSizeFormatted"></s>)
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <button type="button" @click="triggerChoose()" title="Ganti Berkas" class="px-2.5 py-1.5 rounded-lg bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 text-xs font-bold transition-all flex items-center gap-1 cursor-pointer">
                                    <i class="fa-solid fa-arrows-rotate text-[11px] text-slate-500"></i>
                                    <span class="hidden sm:inline">Ganti</span>
                                </button>
                                <button type="button" @click="clearFile()" title="Hapus Berkas" class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 border border-rose-100 text-rose-600 flex items-center justify-center transition-all cursor-pointer">
                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- 7. Surat Pernyataan / Surat CL (Khusus Loreal / Opsional) -->
                    <div x-data="fileDropzone({ name: 'surat_cl', isRequired: false, label: '7. Surat Pernyataan / Surat CL (Khusus Prinsiple PT Loreal Indonesia)', icon: 'fa-file-circle-check', badgeClass: 'bg-rose-50 text-rose-700 border-rose-200' })"
                         class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-2xs space-y-3 sm:col-span-2"
                         x-show="form.prinsiple.toUpperCase().includes('LOREAL') || showSuratCl"
                         x-transition>
                        <div class="flex items-center justify-between gap-2">
                            <label class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold bg-rose-50 text-rose-700 border border-rose-100">
                                    <i class="fa-solid fa-file-circle-check text-[11px]"></i>
                                </span>
                                <span>7. Surat Pernyataan / Surat CL (Khusus Prinsiple PT Loreal Indonesia)</span>
                            </label>
                            <span class="text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full border border-slate-200">Khusus Loreal</span>
                        </div>
                        <input type="file" name="surat_cl" x-ref="fileInput" @change="handleFileChange($event)" accept="image/jpeg,image/png,image/jpg,image/webp,application/pdf" class="hidden">
                        
                        <div x-show="!hasFile && !isProcessing"
                             @click="triggerChoose()"
                             @dragover.prevent="handleDragOver($event)"
                             @dragleave.prevent="handleDragLeave($event)"
                             @drop.prevent="handleDrop($event)"
                             :class="isDragging ? 'border-rose-500 bg-rose-50/40 ring-2 ring-rose-500/20 scale-[1.01]' : 'border-slate-200 hover:border-rose-400 hover:bg-slate-50/60 bg-slate-50/40'"
                             class="border-2 border-dashed rounded-xl p-4 sm:p-5 text-center cursor-pointer transition-all flex flex-col items-center justify-center gap-1.5 select-none">
                            <div class="w-10 h-10 rounded-2xl bg-white border border-slate-200 shadow-2xs flex items-center justify-center text-rose-600 text-base">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                            </div>
                            <div class="text-xs text-slate-700">
                                <span class="font-bold text-rose-600 hover:underline">Klik untuk pilih</span> atau tarik file ke sini
                            </div>
                            <div class="flex items-center gap-1.5 text-[10px] text-slate-400">
                                <span>Maks. 2MB</span>
                                <span>&bull;</span>
                                <span class="text-emerald-600 font-medium"><i class="fa-solid fa-wand-magic-sparkles text-[9px] mr-0.5"></i>Auto Resize</span>
                            </div>
                        </div>

                        <div x-show="isProcessing" class="border-2 border-dashed border-amber-200 bg-amber-50/50 rounded-xl p-5 text-center flex flex-col items-center justify-center gap-2">
                            <i class="fa-solid fa-circle-notch fa-spin text-amber-600 text-xl"></i>
                            <span class="text-xs font-bold text-amber-900">Mengoptimasi berkas...</span>
                        </div>

                        <div x-show="hasFile && !isProcessing" class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <template x-if="fileType === 'image'">
                                    <div class="relative group cursor-pointer shrink-0" @click="previewFullImage()" title="Klik untuk perbesar">
                                        <img :src="fileThumb" class="w-12 h-12 rounded-lg object-cover border border-slate-200 shadow-2xs group-hover:opacity-90 transition-all">
                                        <div class="absolute inset-0 bg-black/30 rounded-lg opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-xs transition-opacity">
                                            <i class="fa-solid fa-magnifying-glass-plus"></i>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="fileType === 'pdf'">
                                    <div class="w-12 h-12 rounded-lg bg-rose-50 border border-rose-200 text-rose-600 flex flex-col items-center justify-center shrink-0">
                                        <i class="fa-solid fa-file-pdf text-xl"></i>
                                        <span class="text-[8px] font-black tracking-wider uppercase mt-0.5">PDF</span>
                                    </div>
                                </template>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-slate-800 truncate" x-text="fileName" :title="fileName"></p>
                                    <div class="flex items-center gap-1.5 mt-1 flex-wrap">
                                        <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-100" x-text="fileSizeFormatted"></span>
                                        <span x-show="fileOriginalSizeFormatted" class="text-[10px] text-slate-400">
                                            (Asli: <s x-text="fileOriginalSizeFormatted"></s>)
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <button type="button" @click="triggerChoose()" title="Ganti Berkas" class="px-2.5 py-1.5 rounded-lg bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 text-xs font-bold transition-all flex items-center gap-1 cursor-pointer">
                                    <i class="fa-solid fa-arrows-rotate text-[11px] text-slate-500"></i>
                                    <span class="hidden sm:inline">Ganti</span>
                                </button>
                                <button type="button" @click="clearFile()" title="Hapus Berkas" class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 border border-rose-100 text-rose-600 flex items-center justify-center transition-all cursor-pointer">
                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 4: Pernyataan & Tombol Kirim -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xs space-y-6">
                <div class="flex items-start gap-3 p-4 rounded-2xl bg-blue-50/60 border border-blue-200/80">
                    <input type="checkbox" id="agreement" required class="w-4 h-4 rounded text-primary focus:ring-primary border-slate-300 mt-0.5 cursor-pointer">
                    <label for="agreement" class="text-xs text-slate-700 leading-relaxed cursor-pointer font-medium select-none">
                        Saya menyatakan dengan sebenarnya bahwa data identitas diri, riwayat masa kerja, dan berkas lampiran yang saya unggah adalah <strong>benar, sah, dan dapat dipertanggungjawabkan</strong>. Saya bersedia mengikuti seluruh prosedur verifikasi bertingkat di ESA Groups.
                    </label>
                </div>

                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-2">
                    <a href="{{ route('paklaring.public.check') }}" class="w-full sm:w-auto px-5 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all text-center">
                        <i class="fa-solid fa-arrow-left text-xs mr-1"></i>
                        <span>Cek Status / Batal</span>
                    </a>

                    <button type="submit" 
                            :disabled="isSubmitting"
                            class="w-full sm:w-auto px-8 py-3.5 rounded-2xl bg-primary hover:bg-primary-700 disabled:opacity-50 text-white text-xs font-black tracking-wide shadow-lg shadow-primary-500/25 transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <i class="fa-solid" :class="isSubmitting ? 'fa-spinner fa-spin' : 'fa-paper-plane'"></i>
                        <span x-text="isSubmitting ? 'Mengunggah &amp; Memproses...' : 'Kirim Permohonan Veklaring'"></span>
                    </button>
                </div>
            </div>
        </form>

    </div>
</div>

<script>
const allPrinciples = @json($principles->map(fn($p) => [
    'name' => $p->name,
    'entity' => $p->entity,
    'label' => $p->name . ($p->entity ? ' (' . $p->entity . ')' : '')
]));
const allAreas = @json($areas);

function fileDropzone(config) {
    return {
        name: config.name,
        isRequired: config.isRequired || false,
        label: config.label,
        icon: config.icon || 'fa-file',
        badgeClass: config.badgeClass || 'bg-primary-50 text-primary border-primary-200',
        isDragging: false,
        isProcessing: false,
        hasFile: false,
        fileName: '',
        fileSizeFormatted: '',
        fileOriginalSizeFormatted: '',
        fileType: '', // 'image' | 'pdf'
        fileThumb: '',

        triggerChoose() {
            if (this.$refs.fileInput) {
                this.$refs.fileInput.click();
            }
        },

        handleDragOver(e) {
            e.preventDefault();
            this.isDragging = true;
        },

        handleDragLeave(e) {
            e.preventDefault();
            this.isDragging = false;
        },

        handleDrop(e) {
            e.preventDefault();
            this.isDragging = false;
            const files = e.dataTransfer.files;
            if (files && files.length > 0) {
                this.processFile(files[0]);
            }
        },

        handleFileChange(e) {
            const files = e.target.files;
            if (files && files.length > 0) {
                this.processFile(files[0]);
            }
        },

        clearFile() {
            this.hasFile = false;
            this.fileName = '';
            this.fileSizeFormatted = '';
            this.fileOriginalSizeFormatted = '';
            this.fileType = '';
            this.fileThumb = '';
            this.isProcessing = false;
            if (this.$refs.fileInput) {
                this.$refs.fileInput.value = '';
            }
        },

        previewFullImage() {
            if (this.fileThumb) {
                Swal.fire({
                    title: this.label,
                    text: this.fileName,
                    imageUrl: this.fileThumb,
                    imageAlt: this.fileName,
                    imageWidth: 650,
                    imageHeight: 'auto',
                    confirmButtonText: 'Tutup',
                    confirmButtonColor: '#0F52BA'
                });
            }
        },

        formatBytes(bytes) {
            if (!bytes || bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        },

        async processFile(rawFile) {
            const MAX_ALLOWED = 2 * 1024 * 1024; // 2 MB
            const ext = rawFile.name.split('.').pop().toLowerCase();
            const allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'pdf'];
            const isImage = rawFile.type.startsWith('image/') || ['jpg', 'jpeg', 'png', 'webp'].includes(ext);
            const isPdf = rawFile.type === 'application/pdf' || ext === 'pdf';

            if (!isImage && !isPdf) {
                Swal.fire({
                    icon: 'error',
                    title: 'Format Berkas Tidak Didukung',
                    html: `Berkas <b>${rawFile.name}</b> memiliki ekstensi yang tidak diizinkan.<br><small class="text-slate-500">Format yang diperbolehkan hanya: JPG, JPEG, PNG, WEBP, atau PDF.</small>`,
                    confirmButtonColor: '#e11d48',
                    confirmButtonText: 'Tutup'
                });
                this.clearFile();
                return;
            }

            this.isProcessing = true;
            const originalSize = rawFile.size;

            try {
                let finalFile = rawFile;

                if (isImage) {
                    this.fileType = 'image';
                    // Auto-resize dan kompresi client-side (maks. resolusi 1600px, kualitas 0.82)
                    finalFile = await this.resizeAndCompressImage(rawFile, 1600, 1600, 0.82);

                    // Jika masih > 2MB, kompresi lebih agresif (maks. 1200px, kualitas 0.70)
                    if (finalFile.size > MAX_ALLOWED) {
                        finalFile = await this.resizeAndCompressImage(finalFile, 1200, 1200, 0.70);
                    }

                    // Buat Data URL untuk preview thumbnail
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.fileThumb = e.target.result;
                    };
                    reader.readAsDataURL(finalFile);
                } else {
                    this.fileType = 'pdf';
                    this.fileThumb = '';
                }

                // Validasi ukuran berkas maksimal 2MB
                if (finalFile.size > MAX_ALLOWED) {
                    const sizeMB = (finalFile.size / (1024 * 1024)).toFixed(2);
                    Swal.fire({
                        icon: 'warning',
                        title: 'Ukuran Berkas Terlalu Besar!',
                        html: `
                            <div class="text-left text-xs text-slate-600 space-y-2.5 mt-2">
                                <p>Berkas <b>${rawFile.name}</b> berukuran <b><span class="text-rose-600">${sizeMB} MB</span></b>.</p>
                                <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700">
                                    <i class="fa-solid fa-triangle-exclamation mr-1.5"></i>
                                    Maksimal ukuran berkas yang diperbolehkan adalah <b>2.00 MB</b>.
                                </div>
                                <p class="text-slate-500">
                                    ${isPdf ? 'Silakan kompres dokumen PDF Anda terlebih dahulu sebelum diunggah.' : 'Silakan pilih gambar dengan resolusi yang lebih proporsional.'}
                                </p>
                            </div>
                        `,
                        confirmButtonColor: '#e11d48',
                        confirmButtonText: 'Pilih Berkas Lain'
                    });
                    this.clearFile();
                    return;
                }

                // Pasang file hasil optimasi ke input native via DataTransfer
                const dt = new DataTransfer();
                dt.items.add(finalFile);
                this.$refs.fileInput.files = dt.files;

                this.hasFile = true;
                this.fileName = finalFile.name;
                this.fileSizeFormatted = this.formatBytes(finalFile.size);
                if (originalSize > finalFile.size) {
                    this.fileOriginalSizeFormatted = this.formatBytes(originalSize);
                } else {
                    this.fileOriginalSizeFormatted = '';
                }

            } catch (err) {
                console.error('Error memproses berkas:', err);
                if (rawFile.size > MAX_ALLOWED) {
                    const sizeMB = (rawFile.size / (1024 * 1024)).toFixed(2);
                    Swal.fire({
                        icon: 'warning',
                        title: 'Ukuran Berkas Terlalu Besar!',
                        html: `Berkas <b>${rawFile.name}</b> berukuran <b>${sizeMB} MB</b>.<br><span class="text-rose-600 font-semibold text-xs">Maksimal ukuran berkas adalah 2.00 MB.</span>`,
                        confirmButtonColor: '#e11d48',
                        confirmButtonText: 'Tutup'
                    });
                    this.clearFile();
                    return;
                }
                const dt = new DataTransfer();
                dt.items.add(rawFile);
                this.$refs.fileInput.files = dt.files;
                this.hasFile = true;
                this.fileName = rawFile.name;
                this.fileSizeFormatted = this.formatBytes(rawFile.size);
            } finally {
                this.isProcessing = false;
            }
        },

        resizeAndCompressImage(file, maxW, maxH, quality) {
            return new Promise((resolve) => {
                const img = new Image();
                const objUrl = URL.createObjectURL(file);
                img.onload = () => {
                    URL.revokeObjectURL(objUrl);
                    let w = img.width;
                    let h = img.height;

                    if (w > maxW || h > maxH) {
                        if (w / h > maxW / maxH) {
                            h = Math.round((h * maxW) / w);
                            w = maxW;
                        } else {
                            w = Math.round((w * maxH) / h);
                            h = maxH;
                        }
                    }

                    const canvas = document.createElement('canvas');
                    canvas.width = w;
                    canvas.height = h;
                    const ctx = canvas.getContext('2d');
                    
                    // Background putih untuk dokumen
                    ctx.fillStyle = '#FFFFFF';
                    ctx.fillRect(0, 0, w, h);
                    ctx.drawImage(img, 0, 0, w, h);

                    const mimeType = 'image/jpeg';
                    canvas.toBlob((blob) => {
                        if (!blob || (blob.size >= file.size && file.size <= 2 * 1024 * 1024 && (file.type === 'image/jpeg' || file.type === 'image/png'))) {
                            resolve(file);
                            return;
                        }
                        const newName = file.name.replace(/\.[^/.]+$/, "") + '.jpg';
                        const newFile = new File([blob], newName, { type: 'image/jpeg', lastModified: Date.now() });
                        resolve(newFile);
                    }, mimeType, quality);
                };
                img.onerror = () => resolve(file);
                img.src = objUrl;
            });
        }
    };
}

function paklaringPublicForm() {
    return {
        form: {
            nik: '{{ old('nik', '') }}',
            nama_lengkap: '{{ old('nama_lengkap', '') }}',
            tempat_lahir: '{{ old('tempat_lahir', '') }}',
            tgl_lahir: '{{ old('tgl_lahir', '') }}',
            jenis_kelamin: '{{ old('jenis_kelamin', '') }}',
            alamat: '{{ old('alamat', '') }}',
            no_hp: '{{ old('no_hp', '') }}',
            email: '{{ old('email', '') }}',
            prinsiple: '{{ old('prinsiple', '') }}',
            area: '{{ old('area', '') }}',
            jabatan: '{{ old('jabatan', '') }}',
            alasan: '{{ old('alasan', 'Mengundurkan Diri') }}',
            tgl_masuk: '{{ old('tgl_masuk', '') }}',
            tgl_keluar: '{{ old('tgl_keluar', '') }}',
        },
        showSuratCl: false,
        isSearchingNik: false,
        isSubmitting: false,

        formatNik() {
            this.form.nik = this.form.nik.replace(/\D/g, '').slice(0, 16);
            if (this.form.nik.length === 16 && !this.form.nama_lengkap) {
                this.lookupNik();
            }
        },

        async lookupNik() {
            if (this.form.nik.length < 8) return;
            this.isSearchingNik = true;
            try {
                const res = await fetch(`{{ route('paklaring.lookup-nik') }}?nik=${encodeURIComponent(this.form.nik)}`);
                const data = await res.json();
                if (data.found) {
                    if (data.nama) this.form.nama_lengkap = data.nama;
                    if (data.tempat_lahir) this.form.tempat_lahir = data.tempat_lahir;
                    if (data.tgl_lahir) this.form.tgl_lahir = data.tgl_lahir;
                    if (data.jenis_kelamin) this.form.jenis_kelamin = data.jenis_kelamin;
                    if (data.alamat) this.form.alamat = data.alamat;
                    if (data.no_hp) this.form.no_hp = data.no_hp;
                    if (data.email) this.form.email = data.email;
                    if (data.jabatan) this.form.jabatan = data.jabatan;
                    if (data.prinsiple) this.form.prinsiple = data.prinsiple;
                    if (data.area) this.form.area = data.area;
                    if (data.tgl_masuk) this.form.tgl_masuk = data.tgl_masuk;
                    if (data.tgl_keluar) this.form.tgl_keluar = data.tgl_keluar;

                    Swal.fire({
                        icon: 'success',
                        title: 'Data Ditemukan!',
                        text: `Data riwayat untuk NIK ${this.form.nik} berhasil dimuat otomatis.`,
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'info',
                        title: 'Data Belum Terdaftar',
                        text: 'NIK belum ditemukan di master riwayat, silakan isi data secara manual.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            } catch (e) {
                console.error('Error lookup NIK:', e);
            } finally {
                this.isSearchingNik = false;
            }
        },

        handleSubmit(e) {
            const requiredFiles = [
                { name: 'foto_ktp', label: '1. Foto KTP Asli' },
                { name: 'form_request', label: '2. Form Request Veklaring' },
                { name: 'exit_cl', label: '3. Exit Clearance' },
                { name: 'pengunduran_diri', label: '4. Surat Pengunduran Diri' },
                { name: 'kartu_bpjs', label: '5. Kartu BPJS Ketenagakerjaan' },
                { name: 'serah_terima', label: '6. Berita Acara Serah Terima Aset' }
            ];

            for (const req of requiredFiles) {
                const inputEl = document.querySelector(`input[name="${req.name}"]`);
                if (!inputEl || !inputEl.files || inputEl.files.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Dokumen Persyaratan Belum Lengkap',
                        html: `Dokumen <b>${req.label}</b> belum diunggah.<br><small class="text-slate-500">Seluruh dokumen persyaratan wajib (1 s/d 6) harus diunggah.</small>`,
                        confirmButtonColor: '#0F52BA',
                        confirmButtonText: 'Lengkapi Berkas'
                    });
                    return;
                }
            }

            this.isSubmitting = true;
            e.target.submit();
        }
    };
}
</script>
@endsection
