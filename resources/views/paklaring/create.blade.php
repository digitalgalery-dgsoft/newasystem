@extends('layouts.app')

@section('title', 'Input Pengajuan Veklaring - Support System ESA Groups')

@section('content')
<div class="space-y-6" x-data="paklaringCreateForm()">
    
    <!-- Header Card -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-xs">
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-primary/10 text-primary border border-primary/20">
                    Modul Veklaring
                </span>
                <span class="text-xs text-slate-400 font-medium">&bull; Input Pengajuan Baru (Internal)</span>
            </div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-file-circle-plus text-primary text-lg"></i>
                <span>Input Pengajuan Surat Referensi Kerja (Veklaring)</span>
            </h1>
            <p class="text-xs text-slate-500">
                Gunakan form ini untuk mendaftarkan permohonan surat keterangan kerja karyawan rekanan secara manual oleh admin/staf area.
            </p>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('paklaring.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all flex items-center gap-2">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                <span>Kembali ke Daftar</span>
            </a>
        </div>
    </div>

    @if(session('error'))
    <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium flex items-center justify-between">
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-circle-exclamation text-rose-600 text-base"></i>
            <span>{{ session('error') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">
            <i class="fa-solid fa-xmark text-sm"></i>
        </button>
    </div>
    @endif

    <form action="{{ route('paklaring.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" @submit.prevent="handleSubmit($event)">
        @csrf

        <!-- Section 1: Identitas Karyawan -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-6">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100 font-black text-slate-800 text-sm">
                <span class="w-6 h-6 rounded-lg bg-blue-50 text-primary flex items-center justify-center text-xs">1</span>
                <span>Data Identitas Karyawan</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- NIK -->
                <div class="sm:col-span-2 space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">
                        Nomor Induk Kependudukan (NIK KTP) <span class="text-rose-500">*</span>
                    </label>
                    <div class="flex items-center gap-2">
                        <input type="text" 
                               name="nik" 
                               x-model="form.nik" 
                               @input="formatNik()"
                               maxlength="16" 
                               required
                               placeholder="16 digit NIK"
                               class="flex-1 px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                        <button type="button" 
                                @click="lookupNik()"
                                :disabled="isSearchingNik || form.nik.length < 8"
                                class="px-4 py-2.5 rounded-xl bg-primary hover:bg-primary-700 disabled:opacity-50 text-white text-xs font-bold transition-all shadow-xs flex items-center gap-1.5 shrink-0 cursor-pointer">
                            <i class="fa-solid" :class="isSearchingNik ? 'fa-spinner fa-spin' : 'fa-wand-magic-sparkles'"></i>
                            <span>Autofill NIK</span>
                        </button>
                    </div>
                </div>

                <!-- Nama Lengkap -->
                <div class="sm:col-span-2 space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Nama Lengkap Sesuai KTP <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_lengkap" x-model="form.nama_lengkap" required placeholder="Nama lengkap sesuai KTP" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-primary/20 focus:border-primary uppercase">
                </div>

                <!-- Tempat Lahir -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Tempat Lahir <span class="text-rose-500">*</span></label>
                    <input type="text" name="tempat_lahir" x-model="form.tempat_lahir" required placeholder="Kota kelahiran" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 capitalize">
                </div>

                <!-- Tanggal Lahir -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Tanggal Lahir <span class="text-rose-500">*</span></label>
                    <input type="date" name="tgl_lahir" x-model="form.tgl_lahir" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800">
                </div>

                <!-- Jenis Kelamin -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Jenis Kelamin <span class="text-rose-500">*</span></label>
                    <select name="jenis_kelamin" x-model="form.jenis_kelamin" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800">
                        <option value="">-- Pilih Jenis Kelamin --</option>
                        <option value="Laki-laki">Laki-laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                </div>

                <!-- No WhatsApp -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Nomor WhatsApp Aktif <span class="text-rose-500">*</span></label>
                    <input type="text" name="no_hp" x-model="form.no_hp" required placeholder="08xxxx / 62xxxx" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800">
                </div>

                <!-- Email -->
                <div class="sm:col-span-2 space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Alamat Email Aktif <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" x-model="form.email" required placeholder="karyawan@email.com" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 lowercase">
                </div>

                <!-- Alamat KTP -->
                <div class="sm:col-span-2 space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Alamat Lengkap Sesuai KTP <span class="text-rose-500">*</span></label>
                    <textarea name="alamat" x-model="form.alamat" rows="2" required placeholder="Alamat KTP" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800"></textarea>
                </div>
            </div>
        </div>

        <!-- Section 2: Pekerjaan & Penempatan -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-6">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100 font-black text-slate-800 text-sm">
                <span class="w-6 h-6 rounded-lg bg-indigo-50 text-indigo-700 flex items-center justify-center text-xs">2</span>
                <span>Data Pekerjaan &amp; Penempatan</span>
            </div>

            <!-- Hidden Default Alasan -->
            <input type="hidden" name="alasan" x-model="form.alasan">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Prinsiple (Searchable Dropdown) -->
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
                        Prinsiple Rekanan <span class="text-rose-500">*</span>
                    </label>
                    <input type="hidden" name="prinsiple" x-model="form.prinsiple" required>

                    <!-- Trigger Button -->
                    <button type="button" 
                            @click="open = !open; if(open) $nextTick(() => $refs.searchPrinAdmin.focus())"
                            class="w-full min-h-[42px] px-3.5 py-2.5 rounded-xl border text-left text-xs font-semibold flex items-center justify-between gap-2 transition-all bg-white cursor-pointer select-none active:scale-[0.99]"
                            :class="open ? 'border-primary ring-2 ring-primary/20 shadow-xs' : (form.prinsiple ? 'border-slate-300 text-slate-800' : 'border-slate-200 text-slate-400')">
                        <span class="truncate" x-text="form.prinsiple ? form.prinsiple : '-- Cari / Pilih Prinsiple --'"></span>
                        <div class="flex items-center gap-1.5 shrink-0">
                            <template x-if="form.prinsiple">
                                <span @click.stop="form.prinsiple = ''; search = ''" 
                                      title="Hapus Pilihan"
                                      class="w-5 h-5 rounded-full hover:bg-slate-100 text-slate-400 hover:text-rose-500 flex items-center justify-center transition-colors">
                                    <i class="fa-solid fa-xmark text-xs"></i>
                                </span>
                            </template>
                            <i class="fa-solid fa-chevron-down text-[11px] text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180 text-primary' : ''"></i>
                        </div>
                    </button>

                    <!-- Dropdown Panel -->
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1 scale-98"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-1 scale-98"
                         class="absolute z-50 left-0 right-0 mt-1.5 bg-white rounded-2xl border border-slate-200 shadow-2xl overflow-hidden"
                         style="display: none;">
                        
                        <div class="p-2.5 bg-slate-50/95 border-b border-slate-100">
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 pointer-events-none">
                                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                                </span>
                                <input type="text"
                                       x-ref="searchPrinAdmin"
                                       x-model="search"
                                       placeholder="Ketik nama prinsiple..."
                                       class="w-full pl-8 pr-8 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary bg-white">
                                <button type="button" 
                                        x-show="search" 
                                        @click="search = ''; $refs.searchPrinAdmin.focus()"
                                        class="absolute inset-y-0 right-0 flex items-center pr-2.5 text-slate-400 hover:text-slate-600">
                                    <i class="fa-solid fa-circle-xmark text-xs"></i>
                                </button>
                            </div>
                        </div>

                        <div class="max-h-60 sm:max-h-56 overflow-y-auto divide-y divide-slate-50 overscroll-contain">
                            <template x-for="p in filtered" :key="p.name">
                                <button type="button"
                                        @click="form.prinsiple = p.name; open = false; search = ''"
                                        class="w-full px-3.5 py-2.5 text-left text-xs font-medium flex items-center justify-between hover:bg-primary-50/70 hover:text-primary transition-colors cursor-pointer select-none active:bg-primary-100 min-h-[40px]"
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

                <!-- Cabang Area (Searchable Dropdown) -->
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
                        Cabang Area <span class="text-rose-500">*</span>
                    </label>
                    <input type="hidden" name="area" x-model="form.area" required>

                    <!-- Trigger Button -->
                    <button type="button" 
                            @click="open = !open; if(open) $nextTick(() => $refs.searchAreaAdmin.focus())"
                            class="w-full min-h-[42px] px-3.5 py-2.5 rounded-xl border text-left text-xs font-semibold flex items-center justify-between gap-2 transition-all bg-white cursor-pointer select-none active:scale-[0.99]"
                            :class="open ? 'border-primary ring-2 ring-primary/20 shadow-xs' : (form.area ? 'border-slate-300 text-slate-800' : 'border-slate-200 text-slate-400')">
                        <span class="truncate" x-text="form.area ? form.area : '-- Cari / Pilih Area --'"></span>
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

                    <!-- Dropdown Panel -->
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1 scale-98"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-1 scale-98"
                         class="absolute z-50 left-0 right-0 mt-1.5 bg-white rounded-2xl border border-slate-200 shadow-2xl overflow-hidden"
                         style="display: none;">
                        
                        <div class="p-2.5 bg-slate-50/95 border-b border-slate-100">
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 pointer-events-none">
                                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                                </span>
                                <input type="text"
                                       x-ref="searchAreaAdmin"
                                       x-model="search"
                                       placeholder="Ketik nama area..."
                                       class="w-full pl-8 pr-8 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary bg-white">
                                <button type="button" 
                                        x-show="search" 
                                        @click="search = ''; $refs.searchAreaAdmin.focus()"
                                        class="absolute inset-y-0 right-0 flex items-center pr-2.5 text-slate-400 hover:text-slate-600">
                                    <i class="fa-solid fa-circle-xmark text-xs"></i>
                                </button>
                            </div>
                        </div>

                        <div class="max-h-60 sm:max-h-56 overflow-y-auto divide-y divide-slate-50 overscroll-contain">
                            <template x-for="ar in filtered" :key="ar">
                                <button type="button"
                                        @click="form.area = ar; open = false; search = ''"
                                        class="w-full px-3.5 py-2.5 text-left text-xs font-medium flex items-center justify-between hover:bg-primary-50/70 hover:text-primary transition-colors cursor-pointer select-none active:bg-primary-100 min-h-[40px]"
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
                           placeholder="Jabatan terakhir" 
                           class="w-full min-h-[42px] px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all capitalize">
                </div>
            </div>
        </div>

        <!-- Section 3: Berkas Persyaratan -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-6">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100 font-black text-slate-800 text-sm">
                <span class="w-6 h-6 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center text-xs">3</span>
                <div>
                    <span>Unggah Berkas Lampiran Persyaratan</span>
                    <p class="text-[11px] font-normal text-slate-500 mt-0.5">Format: JPG, JPEG, PNG, WEBP, atau PDF (maks. 2MB per berkas). Gambar otomatis di-resize dan dikompres.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- 1. Foto KTP -->
                <div x-data="fileDropzone({ name: 'foto_ktp', isRequired: true, label: '1. Foto KTP Asli', icon: 'fa-id-card', badgeClass: 'bg-primary-50 text-primary border-primary-200' })"
                     class="p-4 rounded-xl bg-slate-50/70 border border-slate-200/90 shadow-2xs space-y-3">
                    <div class="flex items-center justify-between gap-2">
                        <label class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                            <span class="w-5 h-5 rounded-md flex items-center justify-center text-[10px] font-bold bg-primary-50 text-primary border border-primary-100">
                                <i class="fa-solid fa-id-card"></i>
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
                         :class="isDragging ? 'border-primary bg-primary-50/40 ring-2 ring-primary/20 scale-[1.01]' : 'border-slate-200 hover:border-primary/50 hover:bg-slate-100/60 bg-white'"
                         class="border-2 border-dashed rounded-xl p-4 text-center cursor-pointer transition-all flex flex-col items-center justify-center gap-1.5 select-none">
                        <div class="w-8 h-8 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-center text-primary text-sm">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                        </div>
                        <div class="text-xs text-slate-700">
                            <span class="font-bold text-primary hover:underline">Klik pilih</span> atau tarik file ke sini
                        </div>
                        <div class="flex items-center gap-1.5 text-[10px] text-slate-400">
                            <span>Maks. 2MB</span>
                            <span>&bull;</span>
                            <span class="text-emerald-600 font-medium"><i class="fa-solid fa-wand-magic-sparkles text-[9px] mr-0.5"></i>Auto Resize</span>
                        </div>
                    </div>

                    <div x-show="isProcessing" class="border-2 border-dashed border-amber-200 bg-amber-50/50 rounded-xl p-4 text-center flex flex-col items-center justify-center gap-1.5">
                        <i class="fa-solid fa-circle-notch fa-spin text-amber-600 text-lg"></i>
                        <span class="text-xs font-bold text-amber-900">Mengoptimasi berkas...</span>
                    </div>

                    <div x-show="hasFile && !isProcessing" class="p-2.5 rounded-xl bg-white border border-slate-200 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <template x-if="fileType === 'image'">
                                <div class="relative group cursor-pointer shrink-0" @click="previewFullImage()" title="Klik untuk perbesar">
                                    <img :src="fileThumb" class="w-11 h-11 rounded-lg object-cover border border-slate-200 shadow-2xs group-hover:opacity-90 transition-all">
                                    <div class="absolute inset-0 bg-black/30 rounded-lg opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-xs transition-opacity">
                                        <i class="fa-solid fa-magnifying-glass-plus"></i>
                                    </div>
                                </div>
                            </template>
                            <template x-if="fileType === 'pdf'">
                                <div class="w-11 h-11 rounded-lg bg-rose-50 border border-rose-200 text-rose-600 flex flex-col items-center justify-center shrink-0">
                                    <i class="fa-solid fa-file-pdf text-lg"></i>
                                    <span class="text-[7px] font-black tracking-wider uppercase">PDF</span>
                                </div>
                            </template>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-slate-800 truncate" x-text="fileName" :title="fileName"></p>
                                <div class="flex items-center gap-1.5 mt-0.5 flex-wrap">
                                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-100" x-text="fileSizeFormatted"></span>
                                    <span x-show="fileOriginalSizeFormatted" class="text-[10px] text-slate-400">
                                        (Asli: <s x-text="fileOriginalSizeFormatted"></s>)
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-1 shrink-0">
                            <button type="button" @click="triggerChoose()" title="Ganti Berkas" class="px-2 py-1 rounded-lg bg-slate-50 border border-slate-200 hover:bg-slate-100 text-slate-700 text-xs font-bold transition-all flex items-center gap-1 cursor-pointer">
                                <i class="fa-solid fa-arrows-rotate text-[10px] text-slate-500"></i>
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
                     class="p-4 rounded-xl bg-slate-50/70 border border-slate-200/90 shadow-2xs space-y-3">
                    <div class="flex items-center justify-between gap-2">
                        <label class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                            <span class="w-5 h-5 rounded-md flex items-center justify-center text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                <i class="fa-solid fa-file-pen"></i>
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
                         :class="isDragging ? 'border-indigo-500 bg-indigo-50/40 ring-2 ring-indigo-500/20 scale-[1.01]' : 'border-slate-200 hover:border-indigo-400 hover:bg-slate-100/60 bg-white'"
                         class="border-2 border-dashed rounded-xl p-4 text-center cursor-pointer transition-all flex flex-col items-center justify-center gap-1.5 select-none">
                        <div class="w-8 h-8 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-center text-indigo-600 text-sm">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                        </div>
                        <div class="text-xs text-slate-700">
                            <span class="font-bold text-indigo-600 hover:underline">Klik pilih</span> atau tarik file ke sini
                        </div>
                        <div class="flex items-center gap-1.5 text-[10px] text-slate-400">
                            <span>Maks. 2MB</span>
                            <span>&bull;</span>
                            <span class="text-emerald-600 font-medium"><i class="fa-solid fa-wand-magic-sparkles text-[9px] mr-0.5"></i>Auto Resize</span>
                        </div>
                    </div>

                    <div x-show="isProcessing" class="border-2 border-dashed border-amber-200 bg-amber-50/50 rounded-xl p-4 text-center flex flex-col items-center justify-center gap-1.5">
                        <i class="fa-solid fa-circle-notch fa-spin text-amber-600 text-lg"></i>
                        <span class="text-xs font-bold text-amber-900">Mengoptimasi berkas...</span>
                    </div>

                    <div x-show="hasFile && !isProcessing" class="p-2.5 rounded-xl bg-white border border-slate-200 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <template x-if="fileType === 'image'">
                                <div class="relative group cursor-pointer shrink-0" @click="previewFullImage()" title="Klik untuk perbesar">
                                    <img :src="fileThumb" class="w-11 h-11 rounded-lg object-cover border border-slate-200 shadow-2xs group-hover:opacity-90 transition-all">
                                    <div class="absolute inset-0 bg-black/30 rounded-lg opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-xs transition-opacity">
                                        <i class="fa-solid fa-magnifying-glass-plus"></i>
                                    </div>
                                </div>
                            </template>
                            <template x-if="fileType === 'pdf'">
                                <div class="w-11 h-11 rounded-lg bg-rose-50 border border-rose-200 text-rose-600 flex flex-col items-center justify-center shrink-0">
                                    <i class="fa-solid fa-file-pdf text-lg"></i>
                                    <span class="text-[7px] font-black tracking-wider uppercase">PDF</span>
                                </div>
                            </template>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-slate-800 truncate" x-text="fileName" :title="fileName"></p>
                                <div class="flex items-center gap-1.5 mt-0.5 flex-wrap">
                                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-100" x-text="fileSizeFormatted"></span>
                                    <span x-show="fileOriginalSizeFormatted" class="text-[10px] text-slate-400">
                                        (Asli: <s x-text="fileOriginalSizeFormatted"></s>)
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-1 shrink-0">
                            <button type="button" @click="triggerChoose()" title="Ganti Berkas" class="px-2 py-1 rounded-lg bg-slate-50 border border-slate-200 hover:bg-slate-100 text-slate-700 text-xs font-bold transition-all flex items-center gap-1 cursor-pointer">
                                <i class="fa-solid fa-arrows-rotate text-[10px] text-slate-500"></i>
                                <span class="hidden sm:inline">Ganti</span>
                            </button>
                            <button type="button" @click="clearFile()" title="Hapus Berkas" class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 border border-rose-100 text-rose-600 flex items-center justify-center transition-all cursor-pointer">
                                <i class="fa-solid fa-trash-can text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 3. Form Exit Clearance -->
                <div x-data="fileDropzone({ name: 'exit_cl', isRequired: true, label: '3. Form Exit Clearance', icon: 'fa-clipboard-check', badgeClass: 'bg-emerald-50 text-emerald-700 border-emerald-200' })"
                     class="p-4 rounded-xl bg-slate-50/70 border border-slate-200/90 shadow-2xs space-y-3">
                    <div class="flex items-center justify-between gap-2">
                        <label class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                            <span class="w-5 h-5 rounded-md flex items-center justify-center text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                <i class="fa-solid fa-clipboard-check"></i>
                            </span>
                            <span>3. Form Exit Clearance <span class="text-rose-500">*</span></span>
                        </label>
                        <span class="text-[10px] font-bold text-rose-500 bg-rose-50 px-2 py-0.5 rounded-full border border-rose-100">Wajib</span>
                    </div>
                    <input type="file" name="exit_cl" x-ref="fileInput" @change="handleFileChange($event)" accept="image/jpeg,image/png,image/jpg,image/webp,application/pdf" class="hidden">
                    
                    <div x-show="!hasFile && !isProcessing"
                         @click="triggerChoose()"
                         @dragover.prevent="handleDragOver($event)"
                         @dragleave.prevent="handleDragLeave($event)"
                         @drop.prevent="handleDrop($event)"
                         :class="isDragging ? 'border-emerald-500 bg-emerald-50/40 ring-2 ring-emerald-500/20 scale-[1.01]' : 'border-slate-200 hover:border-emerald-400 hover:bg-slate-100/60 bg-white'"
                         class="border-2 border-dashed rounded-xl p-4 text-center cursor-pointer transition-all flex flex-col items-center justify-center gap-1.5 select-none">
                        <div class="w-8 h-8 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-center text-emerald-600 text-sm">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                        </div>
                        <div class="text-xs text-slate-700">
                            <span class="font-bold text-emerald-600 hover:underline">Klik pilih</span> atau tarik file ke sini
                        </div>
                        <div class="flex items-center gap-1.5 text-[10px] text-slate-400">
                            <span>Maks. 2MB</span>
                            <span>&bull;</span>
                            <span class="text-emerald-600 font-medium"><i class="fa-solid fa-wand-magic-sparkles text-[9px] mr-0.5"></i>Auto Resize</span>
                        </div>
                    </div>

                    <div x-show="isProcessing" class="border-2 border-dashed border-amber-200 bg-amber-50/50 rounded-xl p-4 text-center flex flex-col items-center justify-center gap-1.5">
                        <i class="fa-solid fa-circle-notch fa-spin text-amber-600 text-lg"></i>
                        <span class="text-xs font-bold text-amber-900">Mengoptimasi berkas...</span>
                    </div>

                    <div x-show="hasFile && !isProcessing" class="p-2.5 rounded-xl bg-white border border-slate-200 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <template x-if="fileType === 'image'">
                                <div class="relative group cursor-pointer shrink-0" @click="previewFullImage()" title="Klik untuk perbesar">
                                    <img :src="fileThumb" class="w-11 h-11 rounded-lg object-cover border border-slate-200 shadow-2xs group-hover:opacity-90 transition-all">
                                    <div class="absolute inset-0 bg-black/30 rounded-lg opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-xs transition-opacity">
                                        <i class="fa-solid fa-magnifying-glass-plus"></i>
                                    </div>
                                </div>
                            </template>
                            <template x-if="fileType === 'pdf'">
                                <div class="w-11 h-11 rounded-lg bg-rose-50 border border-rose-200 text-rose-600 flex flex-col items-center justify-center shrink-0">
                                    <i class="fa-solid fa-file-pdf text-lg"></i>
                                    <span class="text-[7px] font-black tracking-wider uppercase">PDF</span>
                                </div>
                            </template>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-slate-800 truncate" x-text="fileName" :title="fileName"></p>
                                <div class="flex items-center gap-1.5 mt-0.5 flex-wrap">
                                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-100" x-text="fileSizeFormatted"></span>
                                    <span x-show="fileOriginalSizeFormatted" class="text-[10px] text-slate-400">
                                        (Asli: <s x-text="fileOriginalSizeFormatted"></s>)
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-1 shrink-0">
                            <button type="button" @click="triggerChoose()" title="Ganti Berkas" class="px-2 py-1 rounded-lg bg-slate-50 border border-slate-200 hover:bg-slate-100 text-slate-700 text-xs font-bold transition-all flex items-center gap-1 cursor-pointer">
                                <i class="fa-solid fa-arrows-rotate text-[10px] text-slate-500"></i>
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
                     class="p-4 rounded-xl bg-slate-50/70 border border-slate-200/90 shadow-2xs space-y-3">
                    <div class="flex items-center justify-between gap-2">
                        <label class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                            <span class="w-5 h-5 rounded-md flex items-center justify-center text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-100">
                                <i class="fa-solid fa-file-signature"></i>
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
                         :class="isDragging ? 'border-purple-500 bg-purple-50/40 ring-2 ring-purple-500/20 scale-[1.01]' : 'border-slate-200 hover:border-purple-400 hover:bg-slate-100/60 bg-white'"
                         class="border-2 border-dashed rounded-xl p-4 text-center cursor-pointer transition-all flex flex-col items-center justify-center gap-1.5 select-none">
                        <div class="w-8 h-8 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-center text-purple-600 text-sm">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                        </div>
                        <div class="text-xs text-slate-700">
                            <span class="font-bold text-purple-600 hover:underline">Klik pilih</span> atau tarik file ke sini
                        </div>
                        <div class="flex items-center gap-1.5 text-[10px] text-slate-400">
                            <span>Maks. 2MB</span>
                            <span>&bull;</span>
                            <span class="text-emerald-600 font-medium"><i class="fa-solid fa-wand-magic-sparkles text-[9px] mr-0.5"></i>Auto Resize</span>
                        </div>
                    </div>

                    <div x-show="isProcessing" class="border-2 border-dashed border-amber-200 bg-amber-50/50 rounded-xl p-4 text-center flex flex-col items-center justify-center gap-1.5">
                        <i class="fa-solid fa-circle-notch fa-spin text-amber-600 text-lg"></i>
                        <span class="text-xs font-bold text-amber-900">Mengoptimasi berkas...</span>
                    </div>

                    <div x-show="hasFile && !isProcessing" class="p-2.5 rounded-xl bg-white border border-slate-200 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <template x-if="fileType === 'image'">
                                <div class="relative group cursor-pointer shrink-0" @click="previewFullImage()" title="Klik untuk perbesar">
                                    <img :src="fileThumb" class="w-11 h-11 rounded-lg object-cover border border-slate-200 shadow-2xs group-hover:opacity-90 transition-all">
                                    <div class="absolute inset-0 bg-black/30 rounded-lg opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-xs transition-opacity">
                                        <i class="fa-solid fa-magnifying-glass-plus"></i>
                                    </div>
                                </div>
                            </template>
                            <template x-if="fileType === 'pdf'">
                                <div class="w-11 h-11 rounded-lg bg-rose-50 border border-rose-200 text-rose-600 flex flex-col items-center justify-center shrink-0">
                                    <i class="fa-solid fa-file-pdf text-lg"></i>
                                    <span class="text-[7px] font-black tracking-wider uppercase">PDF</span>
                                </div>
                            </template>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-slate-800 truncate" x-text="fileName" :title="fileName"></p>
                                <div class="flex items-center gap-1.5 mt-0.5 flex-wrap">
                                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-100" x-text="fileSizeFormatted"></span>
                                    <span x-show="fileOriginalSizeFormatted" class="text-[10px] text-slate-400">
                                        (Asli: <s x-text="fileOriginalSizeFormatted"></s>)
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-1 shrink-0">
                            <button type="button" @click="triggerChoose()" title="Ganti Berkas" class="px-2 py-1 rounded-lg bg-slate-50 border border-slate-200 hover:bg-slate-100 text-slate-700 text-xs font-bold transition-all flex items-center gap-1 cursor-pointer">
                                <i class="fa-solid fa-arrows-rotate text-[10px] text-slate-500"></i>
                                <span class="hidden sm:inline">Ganti</span>
                            </button>
                            <button type="button" @click="clearFile()" title="Hapus Berkas" class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 border border-rose-100 text-rose-600 flex items-center justify-center transition-all cursor-pointer">
                                <i class="fa-solid fa-trash-can text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 5. Kartu BPJS Ketenagakerjaan -->
                <div x-data="fileDropzone({ name: 'kartu_bpjs', isRequired: true, label: '5. Kartu BPJS Ketenagakerjaan', icon: 'fa-shield-halved', badgeClass: 'bg-cyan-50 text-cyan-700 border-cyan-200' })"
                     class="p-4 rounded-xl bg-slate-50/70 border border-slate-200/90 shadow-2xs space-y-3">
                    <div class="flex items-center justify-between gap-2">
                        <label class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                            <span class="w-5 h-5 rounded-md flex items-center justify-center text-[10px] font-bold bg-cyan-50 text-cyan-700 border border-cyan-100">
                                <i class="fa-solid fa-shield-halved"></i>
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
                         :class="isDragging ? 'border-cyan-500 bg-cyan-50/40 ring-2 ring-cyan-500/20 scale-[1.01]' : 'border-slate-200 hover:border-cyan-400 hover:bg-slate-100/60 bg-white'"
                         class="border-2 border-dashed rounded-xl p-4 text-center cursor-pointer transition-all flex flex-col items-center justify-center gap-1.5 select-none">
                        <div class="w-8 h-8 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-center text-cyan-600 text-sm">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                        </div>
                        <div class="text-xs text-slate-700">
                            <span class="font-bold text-cyan-600 hover:underline">Klik pilih</span> atau tarik file ke sini
                        </div>
                        <div class="flex items-center gap-1.5 text-[10px] text-slate-400">
                            <span>Maks. 2MB</span>
                            <span>&bull;</span>
                            <span class="text-emerald-600 font-medium"><i class="fa-solid fa-wand-magic-sparkles text-[9px] mr-0.5"></i>Auto Resize</span>
                        </div>
                    </div>

                    <div x-show="isProcessing" class="border-2 border-dashed border-amber-200 bg-amber-50/50 rounded-xl p-4 text-center flex flex-col items-center justify-center gap-1.5">
                        <i class="fa-solid fa-circle-notch fa-spin text-amber-600 text-lg"></i>
                        <span class="text-xs font-bold text-amber-900">Mengoptimasi berkas...</span>
                    </div>

                    <div x-show="hasFile && !isProcessing" class="p-2.5 rounded-xl bg-white border border-slate-200 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <template x-if="fileType === 'image'">
                                <div class="relative group cursor-pointer shrink-0" @click="previewFullImage()" title="Klik untuk perbesar">
                                    <img :src="fileThumb" class="w-11 h-11 rounded-lg object-cover border border-slate-200 shadow-2xs group-hover:opacity-90 transition-all">
                                    <div class="absolute inset-0 bg-black/30 rounded-lg opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-xs transition-opacity">
                                        <i class="fa-solid fa-magnifying-glass-plus"></i>
                                    </div>
                                </div>
                            </template>
                            <template x-if="fileType === 'pdf'">
                                <div class="w-11 h-11 rounded-lg bg-rose-50 border border-rose-200 text-rose-600 flex flex-col items-center justify-center shrink-0">
                                    <i class="fa-solid fa-file-pdf text-lg"></i>
                                    <span class="text-[7px] font-black tracking-wider uppercase">PDF</span>
                                </div>
                            </template>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-slate-800 truncate" x-text="fileName" :title="fileName"></p>
                                <div class="flex items-center gap-1.5 mt-0.5 flex-wrap">
                                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-100" x-text="fileSizeFormatted"></span>
                                    <span x-show="fileOriginalSizeFormatted" class="text-[10px] text-slate-400">
                                        (Asli: <s x-text="fileOriginalSizeFormatted"></s>)
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-1 shrink-0">
                            <button type="button" @click="triggerChoose()" title="Ganti Berkas" class="px-2 py-1 rounded-lg bg-slate-50 border border-slate-200 hover:bg-slate-100 text-slate-700 text-xs font-bold transition-all flex items-center gap-1 cursor-pointer">
                                <i class="fa-solid fa-arrows-rotate text-[10px] text-slate-500"></i>
                                <span class="hidden sm:inline">Ganti</span>
                            </button>
                            <button type="button" @click="clearFile()" title="Hapus Berkas" class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 border border-rose-100 text-rose-600 flex items-center justify-center transition-all cursor-pointer">
                                <i class="fa-solid fa-trash-can text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 6. Berita Acara Serah Terima Aset -->
                <div x-data="fileDropzone({ name: 'serah_terima', isRequired: true, label: '6. Berita Acara Serah Terima Aset', icon: 'fa-handshake', badgeClass: 'bg-teal-50 text-teal-700 border-teal-200' })"
                     class="p-4 rounded-xl bg-slate-50/70 border border-slate-200/90 shadow-2xs space-y-3">
                    <div class="flex items-center justify-between gap-2">
                        <label class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                            <span class="w-5 h-5 rounded-md flex items-center justify-center text-[10px] font-bold bg-teal-50 text-teal-700 border border-teal-100">
                                <i class="fa-solid fa-handshake"></i>
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
                         :class="isDragging ? 'border-teal-500 bg-teal-50/40 ring-2 ring-teal-500/20 scale-[1.01]' : 'border-slate-200 hover:border-teal-400 hover:bg-slate-100/60 bg-white'"
                         class="border-2 border-dashed rounded-xl p-4 text-center cursor-pointer transition-all flex flex-col items-center justify-center gap-1.5 select-none">
                        <div class="w-8 h-8 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-center text-teal-600 text-sm">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                        </div>
                        <div class="text-xs text-slate-700">
                            <span class="font-bold text-teal-600 hover:underline">Klik pilih</span> atau tarik file ke sini
                        </div>
                        <div class="flex items-center gap-1.5 text-[10px] text-slate-400">
                            <span>Maks. 2MB</span>
                            <span>&bull;</span>
                            <span class="text-emerald-600 font-medium"><i class="fa-solid fa-wand-magic-sparkles text-[9px] mr-0.5"></i>Auto Resize</span>
                        </div>
                    </div>

                    <div x-show="isProcessing" class="border-2 border-dashed border-amber-200 bg-amber-50/50 rounded-xl p-4 text-center flex flex-col items-center justify-center gap-1.5">
                        <i class="fa-solid fa-circle-notch fa-spin text-amber-600 text-lg"></i>
                        <span class="text-xs font-bold text-amber-900">Mengoptimasi berkas...</span>
                    </div>

                    <div x-show="hasFile && !isProcessing" class="p-2.5 rounded-xl bg-white border border-slate-200 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <template x-if="fileType === 'image'">
                                <div class="relative group cursor-pointer shrink-0" @click="previewFullImage()" title="Klik untuk perbesar">
                                    <img :src="fileThumb" class="w-11 h-11 rounded-lg object-cover border border-slate-200 shadow-2xs group-hover:opacity-90 transition-all">
                                    <div class="absolute inset-0 bg-black/30 rounded-lg opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-xs transition-opacity">
                                        <i class="fa-solid fa-magnifying-glass-plus"></i>
                                    </div>
                                </div>
                            </template>
                            <template x-if="fileType === 'pdf'">
                                <div class="w-11 h-11 rounded-lg bg-rose-50 border border-rose-200 text-rose-600 flex flex-col items-center justify-center shrink-0">
                                    <i class="fa-solid fa-file-pdf text-lg"></i>
                                    <span class="text-[7px] font-black tracking-wider uppercase">PDF</span>
                                </div>
                            </template>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-slate-800 truncate" x-text="fileName" :title="fileName"></p>
                                <div class="flex items-center gap-1.5 mt-0.5 flex-wrap">
                                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-100" x-text="fileSizeFormatted"></span>
                                    <span x-show="fileOriginalSizeFormatted" class="text-[10px] text-slate-400">
                                        (Asli: <s x-text="fileOriginalSizeFormatted"></s>)
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-1 shrink-0">
                            <button type="button" @click="triggerChoose()" title="Ganti Berkas" class="px-2 py-1 rounded-lg bg-slate-50 border border-slate-200 hover:bg-slate-100 text-slate-700 text-xs font-bold transition-all flex items-center gap-1 cursor-pointer">
                                <i class="fa-solid fa-arrows-rotate text-[10px] text-slate-500"></i>
                                <span class="hidden sm:inline">Ganti</span>
                            </button>
                            <button type="button" @click="clearFile()" title="Hapus Berkas" class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 border border-rose-100 text-rose-600 flex items-center justify-center transition-all cursor-pointer">
                                <i class="fa-solid fa-trash-can text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 7. Surat Pernyataan / Surat CL (Khusus Loreal / Opsional) -->
                <div x-data="fileDropzone({ name: 'surat_cl', isRequired: false, label: '7. Surat Pernyataan / Surat CL (Khusus Loreal)', icon: 'fa-file-circle-check', badgeClass: 'bg-rose-50 text-rose-700 border-rose-200' })"
                     class="p-4 rounded-xl bg-slate-50/70 border border-slate-200/90 shadow-2xs space-y-3 sm:col-span-2">
                    <div class="flex items-center justify-between gap-2">
                        <label class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                            <span class="w-5 h-5 rounded-md flex items-center justify-center text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-100">
                                <i class="fa-solid fa-file-circle-check"></i>
                            </span>
                            <span>7. Surat Pernyataan / Surat CL (Khusus Prinsiple PT Loreal Indonesia / Opsional)</span>
                        </label>
                        <span class="text-[10px] font-bold text-slate-500 bg-slate-200/70 px-2 py-0.5 rounded-full border border-slate-300/60">Opsional</span>
                    </div>
                    <input type="file" name="surat_cl" x-ref="fileInput" @change="handleFileChange($event)" accept="image/jpeg,image/png,image/jpg,image/webp,application/pdf" class="hidden">
                    
                    <div x-show="!hasFile && !isProcessing"
                         @click="triggerChoose()"
                         @dragover.prevent="handleDragOver($event)"
                         @dragleave.prevent="handleDragLeave($event)"
                         @drop.prevent="handleDrop($event)"
                         :class="isDragging ? 'border-rose-500 bg-rose-50/40 ring-2 ring-rose-500/20 scale-[1.01]' : 'border-slate-200 hover:border-rose-400 hover:bg-slate-100/60 bg-white'"
                         class="border-2 border-dashed rounded-xl p-4 text-center cursor-pointer transition-all flex flex-col items-center justify-center gap-1.5 select-none">
                        <div class="w-8 h-8 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-center text-rose-600 text-sm">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                        </div>
                        <div class="text-xs text-slate-700">
                            <span class="font-bold text-rose-600 hover:underline">Klik pilih</span> atau tarik file ke sini
                        </div>
                        <div class="flex items-center gap-1.5 text-[10px] text-slate-400">
                            <span>Maks. 2MB</span>
                            <span>&bull;</span>
                            <span class="text-emerald-600 font-medium"><i class="fa-solid fa-wand-magic-sparkles text-[9px] mr-0.5"></i>Auto Resize</span>
                        </div>
                    </div>

                    <div x-show="isProcessing" class="border-2 border-dashed border-amber-200 bg-amber-50/50 rounded-xl p-4 text-center flex flex-col items-center justify-center gap-1.5">
                        <i class="fa-solid fa-circle-notch fa-spin text-amber-600 text-lg"></i>
                        <span class="text-xs font-bold text-amber-900">Mengoptimasi berkas...</span>
                    </div>

                    <div x-show="hasFile && !isProcessing" class="p-2.5 rounded-xl bg-white border border-slate-200 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <template x-if="fileType === 'image'">
                                <div class="relative group cursor-pointer shrink-0" @click="previewFullImage()" title="Klik untuk perbesar">
                                    <img :src="fileThumb" class="w-11 h-11 rounded-lg object-cover border border-slate-200 shadow-2xs group-hover:opacity-90 transition-all">
                                    <div class="absolute inset-0 bg-black/30 rounded-lg opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-xs transition-opacity">
                                        <i class="fa-solid fa-magnifying-glass-plus"></i>
                                    </div>
                                </div>
                            </template>
                            <template x-if="fileType === 'pdf'">
                                <div class="w-11 h-11 rounded-lg bg-rose-50 border border-rose-200 text-rose-600 flex flex-col items-center justify-center shrink-0">
                                    <i class="fa-solid fa-file-pdf text-lg"></i>
                                    <span class="text-[7px] font-black tracking-wider uppercase">PDF</span>
                                </div>
                            </template>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-slate-800 truncate" x-text="fileName" :title="fileName"></p>
                                <div class="flex items-center gap-1.5 mt-0.5 flex-wrap">
                                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-100" x-text="fileSizeFormatted"></span>
                                    <span x-show="fileOriginalSizeFormatted" class="text-[10px] text-slate-400">
                                        (Asli: <s x-text="fileOriginalSizeFormatted"></s>)
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-1 shrink-0">
                            <button type="button" @click="triggerChoose()" title="Ganti Berkas" class="px-2 py-1 rounded-lg bg-slate-50 border border-slate-200 hover:bg-slate-100 text-slate-700 text-xs font-bold transition-all flex items-center gap-1 cursor-pointer">
                                <i class="fa-solid fa-arrows-rotate text-[10px] text-slate-500"></i>
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

        <div class="flex items-center justify-end gap-3 pt-4">
            <a href="{{ route('paklaring.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all">
                Batal
            </a>
            <button type="submit" 
                    :disabled="isSubmitting"
                    class="px-6 py-2.5 rounded-xl bg-primary hover:bg-primary-700 disabled:opacity-50 text-white text-xs font-bold transition-all shadow-md shadow-primary/20 flex items-center gap-2 cursor-pointer">
                <i class="fa-solid" :class="isSubmitting ? 'fa-spinner fa-spin' : 'fa-paper-plane'"></i>
                <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan &amp; Teruskan ke Area'"></span>
            </button>
        </div>
    </form>
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
                    // Auto-resize dan kompresi client-side (maks. 1600px, kualitas 0.82)
                    finalFile = await this.resizeAndCompressImage(rawFile, 1600, 1600, 0.82);

                    // Jika masih > 2MB, kompresi lebih agresif (maks. 1200px, kualitas 0.70)
                    if (finalFile.size > MAX_ALLOWED) {
                        finalFile = await this.resizeAndCompressImage(finalFile, 1200, 1200, 0.70);
                    }

                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.fileThumb = e.target.result;
                    };
                    reader.readAsDataURL(finalFile);
                } else {
                    this.fileType = 'pdf';
                    this.fileThumb = '';
                }

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

function paklaringCreateForm() {
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
                }
            } catch (e) {
                console.error(e);
            } finally {
                this.isSearchingNik = false;
            }
        },

        handleSubmit(e) {
            const requiredFiles = [
                { name: 'foto_ktp', label: '1. Foto KTP Asli' },
                { name: 'form_request', label: '2. Form Request Veklaring' },
                { name: 'exit_cl', label: '3. Form Exit Clearance' },
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
