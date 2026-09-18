@extends('layouts.cbt')

@section('title', 'Login Peserta Test Online CBT | ESA Groups')

@section('content')
<div class="min-h-[calc(100vh-12rem)] flex items-center justify-center py-6 px-4">
    <div class="w-full max-w-md bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
        
        <!-- Top Hero Header Card -->
        <div class="bg-gradient-to-br from-slate-900 via-primary-900 to-primary-700 text-white p-6 sm:p-8 text-center relative overflow-hidden">
            <!-- Background glow accents -->
            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-blue-500/20 rounded-full blur-2xl pointer-events-none"></div>
            <div class="absolute -left-10 -top-10 w-40 h-40 bg-indigo-500/20 rounded-full blur-2xl pointer-events-none"></div>

            <div class="relative z-10">
                <div class="w-16 h-16 bg-white/10 backdrop-blur-md rounded-2xl mx-auto flex items-center justify-center mb-3 shadow-inner border border-white/20">
                    <i class="fa-solid fa-laptop-code text-2xl text-blue-300"></i>
                </div>
                <h1 class="text-xl sm:text-2xl font-black tracking-tight text-white">ESA GROUPS CBT</h1>
                <p class="text-xs text-blue-200 mt-1 font-medium">Computer-Based Test Online for Candidate Selection</p>
                <div class="inline-flex items-center gap-1.5 mt-2 px-2.5 py-0.5 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 text-[10px] font-bold">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    Portal Aktif
                </div>
            </div>
        </div>

        <!-- Form Card Body -->
        <div class="p-6 sm:p-8">
            <div class="mb-6 text-center">
                <h2 class="text-lg font-bold text-slate-800">Masuk ke Ruang Tes</h2>
                <p class="text-xs text-slate-500 mt-1">Masukkan NIK KTP dan Password Anda untuk memulai.</p>
            </div>

            <form method="POST" action="{{ route('cbt.login.post') }}" class="space-y-4">
                @csrf

                <!-- NIK Input -->
                <div>
                    <label for="nik" class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">
                        NIK / Nomor KTP
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                            <i class="fa-solid fa-id-card text-sm"></i>
                        </span>
                        <input type="text" name="nik" id="nik" value="{{ old('nik') }}" required autofocus
                               class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white transition-all font-mono"
                               placeholder="Contoh: 3171012304950001">
                    </div>
                </div>

                <!-- Password Input -->
                <div>
                    <label for="password" class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">
                        Password
                    </label>
                    <div class="relative" x-data="{ show: false }">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                            <i class="fa-solid fa-lock text-sm"></i>
                        </span>
                        <input :type="show ? 'text' : 'password'" name="password" id="password" required
                               class="w-full pl-10 pr-11 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white transition-all"
                               placeholder="Ketik password Anda">
                        <button type="button" @click="show = !show"
                                class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-slate-600 focus:outline-none">
                            <i :class="show ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'" class="text-sm"></i>
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit"
                            class="w-full py-3.5 px-4 rounded-xl font-bold text-sm text-white bg-gradient-to-r from-primary-700 via-primary-600 to-blue-600 hover:from-primary-800 hover:to-blue-700 shadow-lg shadow-primary-500/25 active:scale-[0.99] transition-all flex items-center justify-center gap-2">
                        <span>Masuk Ujian Online</span>
                        <i class="fa-solid fa-arrow-right-to-bracket text-xs"></i>
                    </button>
                </div>
            </form>

            <!-- Panduan Kredensial Login & Akun Demo -->
            <div class="mt-6 bg-blue-50/80 border border-blue-100 rounded-2xl p-4 text-xs text-slate-600">
                <div class="flex items-start gap-2.5">
                    <i class="fa-solid fa-circle-info text-primary mt-0.5 text-sm flex-shrink-0"></i>
                    <div class="w-full">
                        <span class="font-bold text-slate-800 block mb-1">Panduan Login Peserta:</span>
                        <ul class="list-disc list-inside space-y-1 text-[11px] text-slate-600 leading-relaxed">
                            <li>Gunakan <strong>NIK KTP 16 Digit</strong> yang telah didaftarkan.</li>
                            <li>Password default adalah <strong>tanggal lahir (ddmmyyyy)</strong>.</li>
                            <li>Jika mengalami kendala akun, silakan hubungi tim Rekruter / Area Supervisor (AS) terkait.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Link Kembali ke Lowongan Publik -->
            <div class="mt-6 text-center">
                <a href="{{ route('job.public') }}" class="text-xs font-semibold text-slate-500 hover:text-primary transition-colors inline-flex items-center gap-1.5">
                    <i class="fa-solid fa-arrow-left text-[10px]"></i>
                    <span>Kembali ke Portal Karir & Lowongan</span>
                </a>
            </div>

        </div>
    </div>
</div>
@endsection
