@extends('layouts.cbt')

@section('title', 'Lengkapi Data Profil Kandidat | ESA Groups CBT')

@section('content')
<div class="space-y-6" x-data="{ 
    activeTab: '{{ request('tab', 'pribadi') }}',
    showExpModal: false 
}">

    <!-- TOP HEADER / BACK NAVIGATION & PROGRESS -->
    <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-sm border border-slate-200">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('cbt.dashboard') }}" class="w-10 h-10 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition-all flex-shrink-0">
                    <i class="fa-solid fa-arrow-left text-sm"></i>
                </a>
                <div>
                    <h1 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight">Formulir Kelengkapan Data Kandidat</h1>
                    <p class="text-xs text-slate-500">Lengkapi 6 bagian data di bawah ini untuk membuka akses pengerjaan tes online.</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                @if($isProfileComplete)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold">
                        <i class="fa-solid fa-circle-check"></i>
                        Profil 100% Lengkap
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        Profil Belum Lengkap
                    </span>
                @endif
            </div>
        </div>

        <!-- HORIZONTAL TAB NAVIGATION (MOBILE SCROLLABLE) -->
        <div class="mt-6 border-b border-slate-200 overflow-x-auto no-scrollbar">
            <div class="flex space-x-2 min-w-max pb-2">
                <button type="button" @click="activeTab = 'pribadi'"
                        :class="activeTab === 'pribadi' ? 'border-primary text-primary bg-blue-50/70' : 'border-transparent text-slate-500 hover:text-slate-800 hover:bg-slate-50'"
                        class="px-4 py-2.5 rounded-xl border-b-2 font-bold text-xs flex items-center gap-2 transition-all">
                    <i class="fa-solid fa-user"></i>
                    <span>1. Data Pribadi</span>
                </button>

                <button type="button" @click="activeTab = 'keluarga'"
                        :class="activeTab === 'keluarga' ? 'border-primary text-primary bg-blue-50/70' : 'border-transparent text-slate-500 hover:text-slate-800 hover:bg-slate-50'"
                        class="px-4 py-2.5 rounded-xl border-b-2 font-bold text-xs flex items-center gap-2 transition-all">
                    <i class="fa-solid fa-people-roof"></i>
                    <span>2. Data Keluarga</span>
                </button>

                <button type="button" @click="activeTab = 'keuangan'"
                        :class="activeTab === 'keuangan' ? 'border-primary text-primary bg-blue-50/70' : 'border-transparent text-slate-500 hover:text-slate-800 hover:bg-slate-50'"
                        class="px-4 py-2.5 rounded-xl border-b-2 font-bold text-xs flex items-center gap-2 transition-all">
                    <i class="fa-solid fa-wallet"></i>
                    <span>3. Data Keuangan</span>
                </button>

                <button type="button" @click="activeTab = 'tambahan'"
                        :class="activeTab === 'tambahan' ? 'border-primary text-primary bg-blue-50/70' : 'border-transparent text-slate-500 hover:text-slate-800 hover:bg-slate-50'"
                        class="px-4 py-2.5 rounded-xl border-b-2 font-bold text-xs flex items-center gap-2 transition-all">
                    <i class="fa-solid fa-sliders"></i>
                    <span>4. Keterampilan</span>
                </button>

                <button type="button" @click="activeTab = 'pengalaman'"
                        :class="activeTab === 'pengalaman' ? 'border-primary text-primary bg-blue-50/70' : 'border-transparent text-slate-500 hover:text-slate-800 hover:bg-slate-50'"
                        class="px-4 py-2.5 rounded-xl border-b-2 font-bold text-xs flex items-center gap-2 transition-all">
                    <i class="fa-solid fa-briefcase"></i>
                    <span>5. Pengalaman Kerja</span>
                    <span class="px-1.5 py-0.5 rounded-full bg-slate-200 text-slate-700 text-[10px] font-bold">{{ $candidate->workExperiences->count() }}</span>
                </button>

                <button type="button" @click="activeTab = 'ttd'"
                        :class="activeTab === 'ttd' ? 'border-primary text-primary bg-blue-50/70' : 'border-transparent text-slate-500 hover:text-slate-800 hover:bg-slate-50'"
                        class="px-4 py-2.5 rounded-xl border-b-2 font-bold text-xs flex items-center gap-2 transition-all">
                    <i class="fa-solid fa-signature"></i>
                    <span>6. Tanda Tangan Digital</span>
                </button>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 1: DATA PRIBADI & KONTAK -->
    <!-- ========================================================================= -->
    <div x-show="activeTab === 'pribadi'" class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200">
        <div class="mb-6 pb-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900">Bagian 1: Data Identitas Diri & Kontak</h2>
                <p class="text-xs text-slate-500">Pastikan NIK, nama lengkap, dan kontak WhatsApp aktif Anda sudah tepat.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('cbt.profile.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <input type="hidden" name="tab" value="pribadi">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-xs">
                <!-- NIK (Readonly) -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5 uppercase">NIK / No. KTP</label>
                    <input type="text" value="{{ $candidate->nik }}" readonly class="w-full px-3.5 py-2.5 bg-slate-100 border border-slate-200 rounded-xl font-mono font-bold text-slate-600 cursor-not-allowed">
                </div>

                <!-- Nama Lengkap -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5 uppercase">Nama Lengkap Sesuai KTP <span class="text-rose-500">*</span></label>
                    <input type="text" name="full_name" value="{{ old('full_name', $candidate->full_name) }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary focus:bg-white font-medium">
                </div>

                <!-- Tempat Lahir -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5 uppercase">Kota / Tempat Lahir <span class="text-rose-500">*</span></label>
                    <input type="text" name="birth_place" value="{{ old('birth_place', $candidate->birth_place) }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary focus:bg-white font-medium">
                </div>

                <!-- Tanggal Lahir -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5 uppercase">Tanggal Lahir <span class="text-rose-500">*</span></label>
                    <input type="date" name="birth_date" value="{{ old('birth_date', $candidate->birth_date ? $candidate->birth_date->format('Y-m-d') : '') }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary focus:bg-white font-medium">
                </div>

                <!-- Jenis Kelamin -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5 uppercase">Jenis Kelamin <span class="text-rose-500">*</span></label>
                    <select name="gender" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary focus:bg-white font-medium">
                        <option value="">-- Pilih Jenis Kelamin --</option>
                        <option value="Laki-laki" {{ in_array(strtolower(old('gender', $candidate->gender ?? '')), ['laki-laki', 'male', 'l', 'pria']) ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ in_array(strtolower(old('gender', $candidate->gender ?? '')), ['perempuan', 'female', 'p', 'wanita']) ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <!-- Agama -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5 uppercase">Agama <span class="text-rose-500">*</span></label>
                    <select name="religion" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary focus:bg-white font-medium">
                        <option value="">-- Pilih Agama --</option>
                        @foreach(['Islam', 'Kristen Protestan', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $agm)
                            <option value="{{ $agm }}" {{ old('religion', $candidate->religion) == $agm ? 'selected' : '' }}>{{ $agm }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Pendidikan Terakhir -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5 uppercase">Pendidikan Terakhir <span class="text-rose-500">*</span></label>
                    <select name="education" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary focus:bg-white font-medium">
                        <option value="">-- Pilih Pendidikan --</option>
                        @foreach(['SMA / SMK', 'D1', 'D2', 'D3', 'S1 / D4', 'S2'] as $pnd)
                            <option value="{{ $pnd }}" {{ old('education', $candidate->education) == $pnd ? 'selected' : '' }}>{{ $pnd }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Nomor HP / WhatsApp -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5 uppercase">Nomor WhatsApp Aktif <span class="text-rose-500">*</span></label>
                    <input type="text" name="phone" value="{{ old('phone', $candidate->phone ?? $candidate->whatsapp) }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary focus:bg-white font-medium" placeholder="08xxxxxxxxxx">
                </div>

                <!-- Tinggi Badan & Berat Badan -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5 uppercase">Tinggi (cm) <span class="text-rose-500">*</span></label>
                        <input type="number" name="height" value="{{ old('height', $candidate->height) }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary focus:bg-white font-medium" placeholder="165">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5 uppercase">Berat (kg) <span class="text-rose-500">*</span></label>
                        <input type="number" name="weight" value="{{ old('weight', $candidate->weight) }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary focus:bg-white font-medium" placeholder="55">
                    </div>
                </div>

                <!-- Status Perkawinan -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5 uppercase">Status Pernikahan <span class="text-rose-500">*</span></label>
                    <select name="marital_status" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary focus:bg-white font-medium">
                        <option value="">-- Pilih Status --</option>
                        <option value="Belum Menikah" {{ old('marital_status', $candidate->marital_status) == 'Belum Menikah' ? 'selected' : '' }}>Belum Menikah (Lajang)</option>
                        <option value="Menikah" {{ old('marital_status', $candidate->marital_status) == 'Menikah' ? 'selected' : '' }}>Menikah</option>
                        <option value="Cerai Hidup" {{ old('marital_status', $candidate->marital_status) == 'Cerai Hidup' ? 'selected' : '' }}>Cerai Hidup</option>
                        <option value="Cerai Mati" {{ old('marital_status', $candidate->marital_status) == 'Cerai Mati' ? 'selected' : '' }}>Cerai Mati</option>
                    </select>
                </div>

                <!-- Alamat KTP -->
                <div class="md:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1.5 uppercase">Alamat Lengkap Sesuai KTP <span class="text-rose-500">*</span></label>
                    <textarea name="address_ktp" rows="2" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary focus:bg-white font-medium leading-relaxed">{{ old('address_ktp', $candidate->address_ktp) }}</textarea>
                </div>

                <!-- Alamat Domisili -->
                <div class="md:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1.5 uppercase">Alamat Domisili Saat Ini <span class="text-rose-500">*</span></label>
                    <textarea name="address_domicile" rows="2" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary focus:bg-white font-medium leading-relaxed">{{ old('address_domicile', $candidate->address_domicile) }}</textarea>
                </div>

                <!-- Upload Foto Profil -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5 uppercase">Foto Profil Formal</label>
                    <input type="file" name="photo_file" accept="image/*" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-primary hover:file:bg-blue-100">
                    @if($candidate->photo_path)
                        <div class="mt-2 flex items-center gap-2">
                            <span class="text-[11px] text-emerald-600 font-semibold"><i class="fa-solid fa-circle-check"></i> Foto terpasang:</span>
                            <a href="{{ $candidate->photo_url }}" target="_blank" class="text-[11px] text-primary hover:underline font-mono inline-flex items-center gap-1" title="Lihat Foto">
                                <span>{{ $candidate->photo_path }}</span>
                                <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Upload CV -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5 uppercase">Berkas CV / Riwayat Hidup (PDF/DOC)</label>
                    <input type="file" name="cv_file" accept=".pdf,.doc,.docx" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-primary hover:file:bg-blue-100">
                    @if($candidate->cv_path)
                        <div class="mt-2 flex items-center gap-2">
                            <span class="text-[11px] text-emerald-600 font-semibold"><i class="fa-solid fa-circle-check"></i> File CV tersimpan:</span>
                            <a href="{{ $candidate->cv_url }}" target="_blank" class="text-[11px] text-primary hover:underline font-mono inline-flex items-center gap-1" title="Lihat Berkas CV">
                                <span>{{ $candidate->cv_path }}</span>
                                <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end">
                <button type="submit" class="px-6 py-3 rounded-xl bg-primary hover:bg-primary-700 text-white text-xs font-bold shadow-md shadow-primary/20 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Bagian 1 & Lanjut</span>
                </button>
            </div>
        </form>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 2: DATA KELUARGA & KONTAK DARURAT -->
    <!-- ========================================================================= -->
    <div x-show="activeTab === 'keluarga'" class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200">
        <div class="mb-6 pb-4 border-b border-slate-100">
            <h2 class="text-base font-bold text-slate-900">Bagian 2: Data Keluarga & Kontak Darurat</h2>
            <p class="text-xs text-slate-500">Informasi keluarga dan kontak yang dapat dihubungi dalam kondisi darurat.</p>
        </div>

        <form method="POST" action="{{ route('cbt.profile.update') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="tab" value="keluarga">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-xs">
                <!-- Nama Pasangan -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5 uppercase">Nama Suami / Istri (Bila Ada)</label>
                    <input type="text" name="spouse_name" value="{{ old('spouse_name', $candidate->spouse_name) }}" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary focus:bg-white font-medium" placeholder="Isi '-' jika belum menikah">
                </div>

                <!-- Pekerjaan Pasangan -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5 uppercase">Pekerjaan Suami / Istri</label>
                    <input type="text" name="spouse_job" value="{{ old('spouse_job', $candidate->spouse_job) }}" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary focus:bg-white font-medium" placeholder="Isi '-' jika belum menikah">
                </div>

                <!-- Jumlah Anak & Anak Ke -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5 uppercase">Jumlah Anak</label>
                        <input type="number" name="children_count" value="{{ old('children_count', $candidate->children_count ?? 0) }}" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary focus:bg-white font-medium">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5 uppercase">Anak Ke-</label>
                        <input type="number" name="child_order" value="{{ old('child_order', $candidate->child_order ?? 1) }}" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary focus:bg-white font-medium">
                    </div>
                </div>

                <!-- Nama Ibu Kandung -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5 uppercase">Nama Ibu Kandung <span class="text-rose-500">*</span></label>
                    <input type="text" name="mother_name" value="{{ old('mother_name', $candidate->mother_name) }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary focus:bg-white font-medium">
                </div>

                <!-- Kontak Darurat: Nama -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5 uppercase">Nama Kontak Darurat <span class="text-rose-500">*</span></label>
                    <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name', $candidate->emergency_contact_name) }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary focus:bg-white font-medium" placeholder="Orang tua / Saudara kandung">
                </div>

                <!-- Kontak Darurat: Nomor Telepon -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5 uppercase">Nomor HP Kontak Darurat <span class="text-rose-500">*</span></label>
                    <input type="text" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $candidate->emergency_contact_phone) }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary focus:bg-white font-medium" placeholder="08xxxxxxxxxx">
                </div>

                <!-- Kontak Darurat: Hubungan -->
                <div class="md:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1.5 uppercase">Hubungan Kontak Darurat <span class="text-rose-500">*</span></label>
                    <select name="emergency_contact_relation" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary focus:bg-white font-medium">
                        <option value="">-- Pilih Hubungan --</option>
                        @foreach(['Orang Tua', 'Suami / Istri', 'Kakak / Adik Kandung', 'Paman / Bibi', 'Wali / Kerabat'] as $rel)
                            <option value="{{ $rel }}" {{ old('emergency_contact_relation', $candidate->emergency_contact_relation) == $rel ? 'selected' : '' }}>{{ $rel }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end">
                <button type="submit" class="px-6 py-3 rounded-xl bg-primary hover:bg-primary-700 text-white text-xs font-bold shadow-md shadow-primary/20 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Bagian 2 & Lanjut</span>
                </button>
            </div>
        </form>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 3: DATA KEUANGAN & REKENING -->
    <!-- ========================================================================= -->
    <div x-show="activeTab === 'keuangan'" class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200">
        <div class="mb-6 pb-4 border-b border-slate-100">
            <h2 class="text-base font-bold text-slate-900">Bagian 3: Data Rekening Bank & Ekspektasi Gaji</h2>
            <p class="text-xs text-slate-500">Diperlukan untuk data administrasi payroll dan kesesuaian remunerasi.</p>
        </div>

        <form method="POST" action="{{ route('cbt.profile.update') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="tab" value="keuangan">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-xs">
                <!-- Nama Bank -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5 uppercase">Nama Bank <span class="text-rose-500">*</span></label>
                    <select name="bank_name" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary focus:bg-white font-medium">
                        <option value="">-- Pilih Bank --</option>
                        @foreach(['BCA', 'Bank Mandiri', 'BRI', 'BNI', 'BSI', 'CIMB Niaga', 'Permata Bank', 'Bank Danamon'] as $bnk)
                            <option value="{{ $bnk }}" {{ old('bank_name', $candidate->bank_name) == $bnk ? 'selected' : '' }}>{{ $bnk }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Nomor Rekening -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5 uppercase">Nomor Rekening Bank <span class="text-rose-500">*</span></label>
                    <input type="text" name="bank_account_number" value="{{ old('bank_account_number', $candidate->bank_account_number) }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary focus:bg-white font-mono font-bold" placeholder="1234567890">
                </div>

                <!-- Atas Nama Rekening -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5 uppercase">Atas Nama Pemilik Rekening <span class="text-rose-500">*</span></label>
                    <input type="text" name="bank_account_holder" value="{{ old('bank_account_holder', $candidate->bank_account_holder) }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary focus:bg-white font-medium" placeholder="Harus sesuai nama di buku tabungan">
                </div>

                <!-- NPWP -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5 uppercase">Nomor Pokok Wajib Pajak (NPWP)</label>
                    <input type="text" name="npwp" value="{{ old('npwp', $candidate->npwp) }}" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary focus:bg-white font-mono" placeholder="Isi '-' jika belum memiliki NPWP">
                </div>

                <!-- Gaji Terakhir -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5 uppercase">Gaji Terakhir (Rp)</label>
                    <input type="number" name="last_salary" value="{{ old('last_salary', intval($candidate->last_salary)) }}" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary focus:bg-white font-medium" placeholder="4500000">
                </div>

                <!-- Gaji Diminta -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5 uppercase">Gaji yang Diharapkan (Rp)</label>
                    <input type="number" name="expected_salary" value="{{ old('expected_salary', intval($candidate->expected_salary)) }}" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary focus:bg-white font-medium" placeholder="5000000">
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end">
                <button type="submit" class="px-6 py-3 rounded-xl bg-primary hover:bg-primary-700 text-white text-xs font-bold shadow-md shadow-primary/20 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Bagian 3 & Lanjut</span>
                </button>
            </div>
        </form>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 4: DATA TAMBAHAN & KETERAMPILAN -->
    <!-- ========================================================================= -->
    <div x-show="activeTab === 'tambahan'" class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200">
        <div class="mb-6 pb-4 border-b border-slate-100">
            <h2 class="text-base font-bold text-slate-900">Bagian 4: Keterampilan & Karakter Kerja</h2>
            <p class="text-xs text-slate-500">Informasi motivasi, keahlian pendukung, dan kesiapan operasional lapangan.</p>
        </div>

        <form method="POST" action="{{ route('cbt.profile.update') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="tab" value="tambahan">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-xs">
                <!-- Motivasi Kerja -->
                <div class="md:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1.5 uppercase">Motivasi Bekerja di ESA Groups <span class="text-rose-500">*</span></label>
                    <textarea name="work_motivation" rows="2" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary focus:bg-white font-medium">{{ old('work_motivation', $candidate->work_motivation) }}</textarea>
                </div>

                <!-- Kelebihan -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5 uppercase">Kelebihan / Kekuatan Utama Anda <span class="text-rose-500">*</span></label>
                    <textarea name="strengths" rows="2" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary focus:bg-white font-medium">{{ old('strengths', $candidate->strengths) }}</textarea>
                </div>

                <!-- Kekurangan -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5 uppercase">Kekurangan & Cara Mengatasinya <span class="text-rose-500">*</span></label>
                    <textarea name="weaknesses" rows="2" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary focus:bg-white font-medium">{{ old('weaknesses', $candidate->weaknesses) }}</textarea>
                </div>

                <!-- Kegiatan Sekarang -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5 uppercase">Kegiatan Saat Ini <span class="text-rose-500">*</span></label>
                    <input type="text" name="current_activity" value="{{ old('current_activity', $candidate->current_activity) }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary focus:bg-white font-medium" placeholder="Mencari kerja / Kuliah malam / Freelance">
                </div>

                <!-- Kepemilikan Kendaraan -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5 uppercase">Kendaraan yang Dimiliki <span class="text-rose-500">*</span></label>
                    <select name="vehicle" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary focus:bg-white font-medium">
                        <option value="">-- Pilih Kendaraan --</option>
                        <option value="Sepeda Motor Pribadi" {{ old('vehicle', $candidate->vehicle) == 'Sepeda Motor Pribadi' ? 'selected' : '' }}>Sepeda Motor Pribadi</option>
                        <option value="Mobil Pribadi" {{ old('vehicle', $candidate->vehicle) == 'Mobil Pribadi' ? 'selected' : '' }}>Mobil Pribadi</option>
                        <option value="Tidak Memiliki" {{ old('vehicle', $candidate->vehicle) == 'Tidak Memiliki' ? 'selected' : '' }}>Tidak Memiliki (Transportasi Umum)</option>
                    </select>
                </div>

                <!-- Kepemilikan SIM -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5 uppercase">Kepemilikan SIM <span class="text-rose-500">*</span></label>
                    <select name="driving_license" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary focus:bg-white font-medium">
                        <option value="">-- Pilih Kepemilikan SIM --</option>
                        <option value="SIM C" {{ old('driving_license', $candidate->driving_license) == 'SIM C' ? 'selected' : '' }}>SIM C (Motor)</option>
                        <option value="SIM A" {{ old('driving_license', $candidate->driving_license) == 'SIM A' ? 'selected' : '' }}>SIM A (Mobil)</option>
                        <option value="SIM A & C" {{ old('driving_license', $candidate->driving_license) == 'SIM A & C' ? 'selected' : '' }}>SIM A & C</option>
                        <option value="Tidak Memiliki SIM" {{ old('driving_license', $candidate->driving_license) == 'Tidak Memiliki SIM' ? 'selected' : '' }}>Tidak Memiliki SIM</option>
                    </select>
                </div>

                <!-- Keahlian Komputer -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5 uppercase">Keahlian Komputer</label>
                    <input type="text" name="computer_skill" value="{{ old('computer_skill', $candidate->computer_skill) }}" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary focus:bg-white font-medium" placeholder="MS Excel, Word, Google Workspace, POS">
                </div>

                <!-- Bahasa Inggris -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5 uppercase">Keahlian Bahasa Inggris</label>
                    <select name="english_skill" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary focus:bg-white font-medium">
                        <option value="Pasif" {{ old('english_skill', $candidate->english_skill) == 'Pasif' ? 'selected' : '' }}>Pasif (Membaca dasar)</option>
                        <option value="Cukup" {{ old('english_skill', $candidate->english_skill) == 'Cukup' ? 'selected' : '' }}>Cukup (Percakapan sehari-hari)</option>
                        <option value="Aktif / Fasih" {{ old('english_skill', $candidate->english_skill) == 'Aktif / Fasih' ? 'selected' : '' }}>Aktif / Fasih</option>
                    </select>
                </div>

                <!-- Keahlian Lain & Tingkat -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5 uppercase">Keahlian Lain</label>
                        <input type="text" name="other_skills" value="{{ old('other_skills', $candidate->other_skills) }}" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary focus:bg-white font-medium" placeholder="Komunikasi, Negosiasi">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5 uppercase">Tingkat Kemahiran</label>
                        <select name="other_skills_level" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary focus:bg-white font-medium">
                            <option value="Menengah" {{ old('other_skills_level', $candidate->other_skills_level) == 'Menengah' ? 'selected' : '' }}>Menengah</option>
                            <option value="Dasar" {{ old('other_skills_level', $candidate->other_skills_level) == 'Dasar' ? 'selected' : '' }}>Dasar</option>
                            <option value="Mahir" {{ old('other_skills_level', $candidate->other_skills_level) == 'Mahir' ? 'selected' : '' }}>Mahir</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end">
                <button type="submit" class="px-6 py-3 rounded-xl bg-primary hover:bg-primary-700 text-white text-xs font-bold shadow-md shadow-primary/20 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Bagian 4 & Lanjut</span>
                </button>
            </div>
        </form>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 5: PENGALAMAN KERJA -->
    <!-- ========================================================================= -->
    <div x-show="activeTab === 'pengalaman'" class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200">
        <div class="mb-6 pb-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-base font-bold text-slate-900">Bagian 5: Riwayat Pengalaman Kerja</h2>
                <p class="text-xs text-slate-500">Wajib mencantumkan minimal 1 riwayat pekerjaan sebelumnya atau magang.</p>
            </div>
            <button type="button" @click="showExpModal = true" class="px-4 py-2 rounded-xl bg-primary hover:bg-primary-700 text-white text-xs font-bold shadow-sm transition-all flex items-center gap-1.5 self-start sm:self-auto">
                <i class="fa-solid fa-plus"></i>
                <span>Tambah Pengalaman</span>
            </button>
        </div>

        @if($candidate->workExperiences->count() > 0)
            <div class="space-y-4">
                @foreach($candidate->workExperiences as $exp)
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="flex items-start gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 text-primary flex items-center justify-center text-base flex-shrink-0 shadow-sm">
                                <i class="fa-solid fa-building"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900">{{ $exp->company_name }}</h4>
                                <p class="text-xs font-semibold text-primary mt-0.5">{{ $exp->position }}</p>
                                <p class="text-[11px] text-slate-500 mt-1">
                                    <i class="fa-regular fa-calendar text-[10px]"></i> {{ $exp->start_date ? $exp->start_date->format('M Y') : '-' }} s/d {{ $exp->end_date ? $exp->end_date->format('M Y') : 'Sekarang' }}
                                    • Alasan Keluar: <span class="italic">{{ $exp->reason_for_leaving ?? '-' }}</span>
                                </p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('cbt.experience.destroy', $exp->id) }}" onsubmit="return confirm('Hapus riwayat pengalaman ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-rose-600 hover:bg-rose-50 border border-rose-200 transition-all flex items-center gap-1">
                                <i class="fa-solid fa-trash-can text-[10px]"></i>
                                <span>Hapus</span>
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12 border-2 border-dashed border-slate-200 rounded-2xl">
                <i class="fa-solid fa-briefcase text-4xl text-slate-300 mb-3 block"></i>
                <p class="text-xs font-bold text-slate-700">Belum Ada Pengalaman Kerja Terdaftar</p>
                <p class="text-[11px] text-slate-400 mt-1 max-w-sm mx-auto">Klik tombol di bawah ini untuk menambahkan riwayat kerja atau pengalaman magang/PKL Anda.</p>
                <button type="button" @click="showExpModal = true" class="mt-4 px-4 py-2 rounded-xl bg-primary text-white text-xs font-bold shadow-md hover:bg-primary-700 transition-all">
                    + Tambah Pengalaman Kerja
                </button>
            </div>
        @endif

        <div class="pt-6 mt-6 border-t border-slate-100 flex justify-end">
            <button type="button" @click="activeTab = 'ttd'" class="px-6 py-3 rounded-xl bg-primary hover:bg-primary-700 text-white text-xs font-bold shadow-md shadow-primary/20 transition-all flex items-center gap-2">
                <span>Lanjut ke Tanda Tangan Digital</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </button>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 6: TANDA TANGAN DIGITAL & PERNYATAAN INTEGRITAS -->
    <!-- ========================================================================= -->
    <div x-show="activeTab === 'ttd'" class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200">
        <div class="mb-6 pb-4 border-b border-slate-100">
            <h2 class="text-base font-bold text-slate-900">Bagian 6: Tanda Tangan Digital & Pernyataan Integritas</h2>
            <p class="text-xs text-slate-500">Tanda tangani formulir lamaran ini secara digital dan konfirmasikan keabsahan data Anda.</p>
        </div>

        <form method="POST" action="{{ route('cbt.profile.update') }}" id="signatureForm" class="space-y-6">
            @csrf
            <input type="hidden" name="tab" value="ttd">
            <input type="hidden" name="signature_base64" id="signatureBase64">

            <!-- Pernyataan Integritas Box -->
            <div class="bg-blue-50/80 border border-blue-200 rounded-2xl p-5 text-xs text-slate-700 leading-relaxed">
                <h4 class="font-bold text-primary mb-2 flex items-center gap-2">
                    <i class="fa-solid fa-shield-halved"></i>
                    <span>Surat Pernyataan Kebenaran Data Pelamar</span>
                </h4>
                <p>
                    Dengan ini saya menyatakan dengan sesungguhnya bahwa seluruh data, riwayat hidup, pengalaman kerja, serta berkas yang saya berikan dalam formulir rekrutmen ini adalah <strong>benar, sah, dan dapat dipertanggungjawabkan</strong>. Apabila di kemudian hari terbukti ada keterangan atau data yang tidak benar atau dipalsukan, saya bersedia menerima sanksi pembatalan hasil seleksi hingga pemutusan hubungan kerja sesuai ketentuan yang berlaku di ESA Groups.
                </p>
                <div class="mt-4 pt-3 border-t border-blue-200/60 flex items-center gap-2.5">
                    <input type="checkbox" name="statement_agreed" id="statement_agreed" value="1" {{ $candidate->statement_agreed ? 'checked' : '' }} required class="w-4 h-4 text-primary rounded border-slate-300 focus:ring-primary">
                    <label for="statement_agreed" class="font-bold text-slate-900 cursor-pointer">
                        Saya menyetujui dan menandatangani pernyataan di atas secara sadar tanpa paksaan. <span class="text-rose-500">*</span>
                    </label>
                </div>
            </div>

            <!-- Signature Pad Canvas -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block font-bold text-slate-700 text-xs uppercase tracking-wider">
                        Tanda Tangan Digital Anda (Gunakan Jari di Smartphone / Mouse di PC) <span class="text-rose-500">*</span>
                    </label>
                    <button type="button" id="clearCanvasBtn" class="text-xs text-rose-600 hover:text-rose-800 font-bold flex items-center gap-1">
                        <i class="fa-solid fa-eraser text-[11px]"></i>
                        <span>Bersihkan Tanda Tangan</span>
                    </button>
                </div>

                <div class="border-2 border-dashed border-slate-300 rounded-2xl p-2 bg-slate-50 flex flex-col items-center justify-center">
                    <canvas id="signaturePad" width="500" height="200" class="bg-white rounded-xl shadow-inner w-full max-w-lg cursor-crosshair border border-slate-200 touch-none"></canvas>
                    <span class="text-[11px] text-slate-400 mt-2">Area Tanda Tangan (Touchscreen Friendly)</span>
                </div>

                @if($candidate->signature_path)
                    <div class="mt-4 p-3 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center gap-3">
                        <i class="fa-solid fa-circle-check text-emerald-600"></i>
                        <span class="text-xs text-emerald-800 font-semibold">Tanda tangan digital Anda telah tersimpan sebelumnya. Anda dapat menandatangani ulang di atas jika ingin mengganti.</span>
                    </div>
                @endif
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end">
                <button type="submit" id="saveSignatureBtn" class="px-8 py-3.5 rounded-xl bg-gradient-to-r from-primary-700 to-blue-600 hover:from-primary-800 hover:to-blue-700 text-white text-xs font-bold shadow-lg shadow-primary/25 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>Simpan & Selesaikan Profil</span>
                </button>
            </div>
        </form>
    </div>

    <!-- MODAL TAMBAH PENGALAMAN KERJA -->
    <div x-show="showExpModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-100" @click.away="showExpModal = false">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-900">Tambah Pengalaman Kerja</h3>
                <button type="button" @click="showExpModal = false" class="text-slate-400 hover:text-slate-600 text-lg">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('cbt.experience.store') }}" class="space-y-4 mt-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Perusahaan / Instansi <span class="text-rose-500">*</span></label>
                    <input type="text" name="company_name" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary focus:bg-white font-medium" placeholder="PT Contoh Sukses">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Jabatan / Posisi <span class="text-rose-500">*</span></label>
                    <input type="text" name="position" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary focus:bg-white font-medium" placeholder="Sales Promotor / Admin / Staff">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Tanggal Masuk</label>
                        <input type="date" name="start_date" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary focus:bg-white font-medium">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Tanggal Keluar</label>
                        <input type="date" name="end_date" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary focus:bg-white font-medium">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Alasan Keluar</label>
                    <textarea name="reason_for_leaving" rows="2" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary focus:bg-white font-medium" placeholder="Kontrak selesai / Mengembangkan karir"></textarea>
                </div>

                <div class="pt-3 flex justify-end gap-2">
                    <button type="button" @click="showExpModal = false" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold hover:bg-slate-50">Batal</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary text-white font-bold hover:bg-primary-700 shadow-md">Simpan Pengalaman</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // DIGITAL SIGNATURE CANVAS SCRIPT
    const canvas = document.getElementById('signaturePad');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    const clearBtn = document.getElementById('clearCanvasBtn');
    const form = document.getElementById('signatureForm');
    const base64Input = document.getElementById('signatureBase64');

    let isDrawing = false;
    let hasDrawn = false;

    // Set line style
    ctx.lineWidth = 2.5;
    ctx.lineCap = 'round';
    ctx.strokeStyle = '#0F52BA';

    function getPos(e) {
        const rect = canvas.getBoundingClientRect();
        const scaleX = canvas.width / rect.width;
        const scaleY = canvas.height / rect.height;

        let clientX = e.clientX;
        let clientY = e.clientY;

        if (e.touches && e.touches.length > 0) {
            clientX = e.touches[0].clientX;
            clientY = e.touches[0].clientY;
        }

        return {
            x: (clientX - rect.left) * scaleX,
            y: (clientY - rect.top) * scaleY
        };
    }

    function startDraw(e) {
        isDrawing = true;
        hasDrawn = true;
        const pos = getPos(e);
        ctx.beginPath();
        ctx.moveTo(pos.x, pos.y);
        e.preventDefault();
    }

    function draw(e) {
        if (!isDrawing) return;
        const pos = getPos(e);
        ctx.lineTo(pos.x, pos.y);
        ctx.stroke();
        e.preventDefault();
    }

    function stopDraw(e) {
        if (isDrawing) {
            isDrawing = false;
        }
    }

    // Mouse Events
    canvas.addEventListener('mousedown', startDraw);
    canvas.addEventListener('mousemove', draw);
    window.addEventListener('mouseup', stopDraw);

    // Touch Events (Mobile)
    canvas.addEventListener('touchstart', startDraw, { passive: false });
    canvas.addEventListener('touchmove', draw, { passive: false });
    window.addEventListener('touchend', stopDraw);

    // Clear Button
    clearBtn.addEventListener('click', function() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        hasDrawn = false;
        base64Input.value = '';
    });

    // Form Submit Hook
    form.addEventListener('submit', function(e) {
        if (hasDrawn) {
            base64Input.value = canvas.toDataURL('image/png');
        }
    });
});
</script>
@endpush
