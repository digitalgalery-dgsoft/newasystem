@extends('layouts.app')

@section('title', 'Formulir Registrasi Walkin Interview - ESA Groups')

@section('content')
<div class="min-h-[80vh] flex flex-col items-center justify-center py-6 px-4">
    <!-- Back to Walkin list -->
    <div class="w-full max-w-md mb-3 flex items-center justify-between">
        <a href="{{ route('interview.walk') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-primary transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Daftar Walkin</span>
        </a>
    </div>

    <!-- Alert Success / Error -->
    @if(session('success'))
    <div class="w-full max-w-md mb-4 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-800 text-xs flex items-center justify-between gap-2 shadow-xs">
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    @endif

    @if(isset($errors) && $errors->any())
    <div class="w-full max-w-md mb-4 p-4 bg-rose-50 border border-rose-200 rounded-2xl text-rose-800 text-xs space-y-1 shadow-xs">
        <div class="font-bold flex items-center gap-1.5">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span>Terdapat kesalahan pengisian formulir:</span>
        </div>
        <ul class="list-disc list-inside space-y-0.5 text-[11px] text-rose-700">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Card Form Sesuai Gambar 2 Sistem Lama -->
    <div class="bg-white rounded-3xl shadow-xl max-w-md w-full p-6 sm:p-8 border border-slate-100">
        <!-- Subtitle -->
        <div class="text-center mb-6">
            <h2 class="text-base font-semibold text-slate-700 tracking-tight">
                Isi formulir untuk mendaftar.
            </h2>
        </div>

        <form action="{{ route('interview.walk.store') }}" method="POST" class="space-y-4">
            @csrf

            <!-- 1. NIK / No. KTP -->
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-id-card text-sm"></i>
                </div>
                <input type="text" 
                       name="nik" 
                       value="{{ old('nik') }}"
                       required 
                       maxlength="16" 
                       minlength="16" 
                       pattern="[0-9]{16}"
                       placeholder="NIK / No. KTP" 
                       class="w-full pl-11 pr-4 py-3.5 bg-[#f1f5f9]/90 border-0 rounded-2xl text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition"
                       title="Masukkan 16 digit NIK sesuai KTP">
            </div>

            <!-- 2. Nama Lengkap -->
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-user text-sm"></i>
                </div>
                <input type="text" 
                       name="full_name" 
                       value="{{ old('full_name') }}"
                       required 
                       placeholder="Nama Lengkap" 
                       class="w-full pl-11 pr-4 py-3.5 bg-[#f1f5f9]/90 border-0 rounded-2xl text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
            </div>

            <!-- 3. Tanggal Lahir (dd/mm/tttt) -->
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-calendar-days text-sm"></i>
                </div>
                <input type="date" 
                       name="birth_date" 
                       value="{{ old('birth_date') }}"
                       required 
                       class="w-full pl-11 pr-4 py-3.5 bg-[#f1f5f9]/90 border-0 rounded-2xl text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
            </div>

            <!-- 4. Pendidikan Terakhir -->
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-graduation-cap text-sm"></i>
                </div>
                <select name="education" required class="w-full pl-11 pr-10 py-3.5 bg-[#f1f5f9]/90 border-0 rounded-2xl text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition appearance-none">
                    <option value="" disabled {{ old('education') ? '' : 'selected' }}>Pendidikan Terakhir</option>
                    @foreach($dropdownEducation as $edu)
                        <option value="{{ $edu }}" {{ old('education') == $edu ? 'selected' : '' }}>{{ $edu }}</option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-chevron-down text-xs"></i>
                </div>
            </div>

            <!-- 5. Jabatan Dilamar -->
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-briefcase text-sm"></i>
                </div>
                <select name="applied_job" required class="w-full pl-11 pr-10 py-3.5 bg-[#f1f5f9]/90 border-0 rounded-2xl text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition appearance-none">
                    <option value="" disabled {{ old('applied_job') ? '' : 'selected' }}>Jabatan Dilamar</option>
                    @foreach($dropdownJobs as $job)
                        <option value="{{ $job }}" {{ old('applied_job') == $job ? 'selected' : '' }}>{{ $job }}</option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-chevron-down text-xs"></i>
                </div>
            </div>

            <!-- 6. Area Interview -->
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-map-location-dot text-sm"></i>
                </div>
                <select name="area" required class="w-full pl-11 pr-10 py-3.5 bg-[#f1f5f9]/90 border-0 rounded-2xl text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition appearance-none">
                    <option value="" disabled {{ old('area') ? '' : 'selected' }}>Area Interview</option>
                    @foreach($dropdownAreas as $area)
                        <option value="{{ $area }}" {{ old('area') == $area ? 'selected' : '' }}>{{ $area }}</option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-chevron-down text-xs"></i>
                </div>
            </div>

            <!-- 7. Informasi Lowongan -->
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-bullhorn text-sm"></i>
                </div>
                <select name="info" required class="w-full pl-11 pr-10 py-3.5 bg-[#f1f5f9]/90 border-0 rounded-2xl text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition appearance-none">
                    <option value="" disabled {{ old('info') ? '' : 'selected' }}>Informasi Lowongan</option>
                    @foreach($dropdownInfo as $inf)
                        <option value="{{ $inf }}" {{ old('info') == $inf ? 'selected' : '' }}>{{ $inf }}</option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-chevron-down text-xs"></i>
                </div>
            </div>

            <!-- 8. Jenis Undangan Interview -->
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-envelope text-sm"></i>
                </div>
                <select name="undangan" required class="w-full pl-11 pr-10 py-3.5 bg-[#f1f5f9]/90 border-0 rounded-2xl text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition appearance-none">
                    <option value="" disabled {{ old('undangan') ? '' : 'selected' }}>Jenis Undangan Interview</option>
                    @foreach($dropdownUndangan as $und)
                        <option value="{{ $und }}" {{ old('undangan') == $und ? 'selected' : '' }}>{{ $und }}</option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-chevron-down text-xs"></i>
                </div>
            </div>

            <!-- Submit Button: Register -->
            <div class="pt-3">
                <button type="submit" class="w-full py-3.5 px-6 rounded-full bg-[#2563eb] hover:bg-blue-700 text-white font-bold text-sm tracking-wide shadow-md shadow-blue-500/25 transition cursor-pointer">
                    Register
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
