@extends('layouts.app')

@section('title', 'Beranda - ASystem Portal')

@section('content')
@php
    $hour = (int) date('H');
    $greeting = match(true) {
        $hour >= 4 && $hour < 11 => 'Selamat Pagi',
        $hour >= 11 && $hour < 15 => 'Selamat Siang',
        $hour >= 15 && $hour < 18 => 'Selamat Sore',
        default => 'Selamat Malam'
    };

    $currentUser = $user ?? Auth::user();
    $userName = $currentUser ? $currentUser->name : 'Pengguna';
    $userEmail = $currentUser ? $currentUser->email : '-';
    $userRole = $currentUser ? ($currentUser->role ?? 'admin') : 'admin';
    $isAdmin = $currentUser ? $currentUser->isAdmin() : false;

    $roleLabel = match($userRole) {
        'admin' => 'Administrator Sistem',
        'karyawan_inhouse' => 'Karyawan Inhouse',
        'karyawan_ratecard' => 'Karyawan RateCard',
        'recruiter' => 'Recruiter Team',
        'head_hr' => 'Head of HR',
        default => ucfirst(str_replace('_', ' ', $userRole))
    };

    $avatarColor = match($userRole) {
        'admin' => '0F52BA',
        'karyawan_inhouse' => '059669',
        'karyawan_ratecard' => 'D97706',
        default => '6366F1'
    };
@endphp

<div class="space-y-6 w-full">

    <!-- 1. HERO SAMBUTAN UTAMA -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-primary-950 to-primary-900 text-white p-6 sm:p-8 lg:p-8 shadow-xl shadow-primary-950/20 border border-slate-800">
        <!-- Background Ambient Glow & Patterns -->
        <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-primary-500/20 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-80 h-80 rounded-full bg-blue-500/15 blur-3xl pointer-events-none"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-start sm:items-center gap-5">
                <div class="relative flex-shrink-0">
                    <img src="{{ $currentUser ? $currentUser->avatar_url : "https://ui-avatars.com/api/?name=" . urlencode($userName) . "&background=" . $avatarColor . "&color=fff&size=128&bold=true" }}" 
                         alt="{{ $userName }}" 
                         class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl border-2 border-white/20 shadow-lg object-cover ring-4 ring-white/10">
                    <span class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-emerald-500 border-2 border-slate-900 flex items-center justify-center text-[10px]" title="Aktif Online">
                        <i class="fa-solid fa-check text-white"></i>
                    </span>
                </div>
                <div>
                    <div class="flex items-center gap-2.5 flex-wrap mb-1.5">
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold tracking-wide uppercase bg-white/10 text-white border border-white/15 backdrop-blur-sm">
                            {{ $greeting }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold tracking-wide uppercase {{ $isAdmin ? 'bg-indigo-500/25 text-indigo-200 border border-indigo-400/30' : ($userRole === 'karyawan_inhouse' ? 'bg-emerald-500/25 text-emerald-200 border border-emerald-400/30' : 'bg-amber-500/25 text-amber-200 border border-amber-400/30') }}">
                            <i class="fa-solid {{ $isAdmin ? 'fa-shield-halved' : 'fa-id-badge' }} mr-1"></i>
                            {{ $roleLabel }}
                        </span>
                        <a href="{{ route('profile.index') }}" class="px-2.5 py-0.5 rounded-full text-[11px] font-bold tracking-wide bg-amber-400/20 hover:bg-amber-400/30 text-amber-200 border border-amber-300/30 transition-all flex items-center gap-1.5">
                            <i class="fa-solid fa-user-pen text-[10px]"></i>
                            <span>Edit Profil</span>
                        </a>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                        {{ $userName }}
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl leading-relaxed">
                        @if($isAdmin)
                            Selamat datang di pusat kendali <strong>ASystem Cloud Enterprise</strong>. Anda memiliki hak akses penuh sebagai Administrator untuk mengelola Master Data, konfigurasi sinkronisasi, dan seluruh modul operasional.
                        @else
                            Selamat datang di portal layanan <strong>ASystem Cloud</strong> — Support System ESA Groups. Silakan gunakan menu di panel samping untuk mengakses modul dan layanan operasional Anda.
                        @endif
                    </p>
                </div>
            </div>

            <!-- Date & System Status Badge -->
            <div class="flex md:flex-col items-center md:items-end justify-between gap-2 border-t md:border-t-0 border-white/10 pt-4 md:pt-0">
                <div class="text-left md:text-right">
                    <div class="text-[11px] text-slate-400 font-medium uppercase tracking-wider">Tanggal & Waktu</div>
                    <div class="text-sm sm:text-base font-bold text-white flex items-center gap-2 mt-0.5">
                        <i class="fa-solid fa-calendar-day text-primary-400"></i>
                        <span>{{ date('d F Y') }}</span>
                    </div>
                </div>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white/10 border border-white/15 text-xs text-slate-200 backdrop-blur-sm">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="font-semibold">ASystem Cloud Active</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. INFORMASI IDENTITAS & PANDUAN -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- KARTU INFORMASI AKUN / PROFIL (2 KOLOM) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-7 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-primary flex items-center justify-center text-lg">
                            <i class="fa-solid fa-address-card"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-900">Informasi Akun & Pengguna</h2>
                            <p class="text-xs text-slate-500">Detail identitas Anda yang terdaftar pada sistem</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-700">
                        {{ $isAdmin ? 'Admin View' : 'User View' }}
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-y-5 gap-x-6 text-xs">
                    <div class="space-y-1">
                        <div class="text-slate-400 font-semibold uppercase tracking-wider text-[10px]">Nama Lengkap</div>
                        <div class="text-slate-800 font-bold text-sm">{{ $userName }}</div>
                    </div>

                    <div class="space-y-1">
                        <div class="text-slate-400 font-semibold uppercase tracking-wider text-[10px]">Alamat Email</div>
                        <div class="text-slate-800 font-medium flex items-center gap-1.5">
                            <i class="fa-regular fa-envelope text-slate-400"></i>
                            <span>{{ $userEmail }}</span>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <div class="text-slate-400 font-semibold uppercase tracking-wider text-[10px]">Hak Akses / Peran</div>
                        <div>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md font-bold text-[11px] {{ $isAdmin ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                                <i class="fa-solid {{ $isAdmin ? 'fa-shield-halved' : 'fa-user-check' }} text-[10px]"></i>
                                {{ $roleLabel }}
                            </span>
                        </div>
                    </div>

                    @if($employee)
                        <div class="space-y-1">
                            <div class="text-slate-400 font-semibold uppercase tracking-wider text-[10px]">Nomor Induk Karyawan (NIK)</div>
                            <div class="text-slate-800 font-bold font-mono tracking-wider">{{ $employee->nik }}</div>
                        </div>

                        <div class="space-y-1">
                            <div class="text-slate-400 font-semibold uppercase tracking-wider text-[10px]">Jabatan / Posisi</div>
                            <div class="text-slate-800 font-semibold">{{ $employee->jabatan ?: 'Staff Operasional' }}</div>
                        </div>

                        <div class="space-y-1">
                            <div class="text-slate-400 font-semibold uppercase tracking-wider text-[10px]">Penempatan Area</div>
                            <div class="text-slate-800 font-medium flex items-center gap-1.5">
                                <i class="fa-solid fa-location-dot text-slate-400"></i>
                                <span>{{ $employee->area ?: '-' }} {{ $employee->regional ? '('.$employee->regional.')' : '' }}</span>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <div class="text-slate-400 font-semibold uppercase tracking-wider text-[10px]">Prinsiple / Mitra</div>
                            <div class="text-slate-800 font-semibold flex items-center gap-1.5">
                                <i class="fa-solid fa-building text-slate-400"></i>
                                <span>{{ $employee->prinsiple ?: 'PT Arina Multikarya' }}</span>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <div class="text-slate-400 font-semibold uppercase tracking-wider text-[10px]">Tipe & Status Pegawai</div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $employee->tipe_karyawan === 'Inhouse' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                    {{ $employee->tipe_karyawan }}
                                </span>
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    {{ strtoupper($employee->status) }}
                                </span>
                            </div>
                        </div>
                    @else
                        <div class="space-y-1">
                            <div class="text-slate-400 font-semibold uppercase tracking-wider text-[10px]">Lingkup Sistem</div>
                            <div class="text-slate-800 font-medium">Enterprise Management, Talent Pool & Odoo ERP</div>
                        </div>

                        <div class="space-y-1">
                            <div class="text-slate-400 font-semibold uppercase tracking-wider text-[10px]">Entitas Operasional</div>
                            <div class="text-slate-800 font-medium">ESA Groups (AMK, AKP, ATK, ABO, ATB)</div>
                        </div>

                        <div class="space-y-1">
                            <div class="text-slate-400 font-semibold uppercase tracking-wider text-[10px]">Keamanan & Sesi</div>
                            <div class="text-emerald-700 font-semibold flex items-center gap-1">
                                <i class="fa-solid fa-shield-check"></i> Sesi Login Terproteksi
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500">
                <span class="flex items-center gap-1.5">
                    <i class="fa-solid fa-info-circle text-primary"></i>
                    <span>Informasi profil disinkronkan secara otomatis dari database ASystem.</span>
                </span>
                <div class="flex items-center gap-2">
                    <a href="{{ route('profile.index') }}" class="inline-flex items-center gap-1.5 text-primary hover:text-primary-700 font-bold py-1.5 px-3 rounded-xl bg-primary-50 hover:bg-primary-100 transition-all border border-primary-200 shadow-2xs">
                        <i class="fa-solid fa-user-pen text-xs"></i>
                        <span>Edit Profil / Password</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline m-0">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1.5 text-rose-600 hover:text-rose-700 font-semibold py-1.5 px-3 rounded-xl hover:bg-rose-50 transition-all">
                            <i class="fa-solid fa-power-off text-xs"></i>
                            <span>Keluar (Logout)</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- KARTU PANDUAN NAVIGASI (1 KOLOM) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-7 flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-3 pb-4 mb-4 border-b border-slate-100">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-compass"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Panduan Navigasi</h2>
                        <p class="text-xs text-slate-500">Cara menggunakan sistem</p>
                    </div>
                </div>

                <div class="space-y-3.5 text-xs text-slate-600">
                    <div class="flex items-start gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <div class="w-6 h-6 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5">
                            1
                        </div>
                        <div>
                            <div class="font-bold text-slate-800">Menu Navigasi Samping</div>
                            <p class="text-slate-500 mt-0.5 leading-relaxed">
                                Gunakan bilah menu di sebelah kiri untuk berpindah ke modul yang ingin Anda gunakan.
                            </p>
                        </div>
                    </div>

                    @if($isAdmin)
                    <div class="flex items-start gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <div class="w-6 h-6 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5">
                            2
                        </div>
                        <div>
                            <div class="font-bold text-slate-800">Menu Master Data</div>
                            <p class="text-slate-500 mt-0.5 leading-relaxed">
                                Menu Master Data hanya tampil dan dapat dikelola oleh akun dengan akses Administrator.
                            </p>
                        </div>
                    </div>
                    @endif

                    <div class="flex items-start gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <div class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5">
                            {{ $isAdmin ? '3' : '2' }}
                        </div>
                        <div>
                            <div class="font-bold text-slate-800">Bantuan & Kendala</div>
                            <p class="text-slate-500 mt-0.5 leading-relaxed">
                                Jika menemukan kendala akses atau data yang tidak sesuai, hubungi tim IT Support / HRD.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5 p-3 rounded-xl bg-blue-50/70 border border-blue-100 text-[11px] text-blue-800 flex items-center gap-2">
                <i class="fa-solid fa-lock text-blue-600 text-sm flex-shrink-0"></i>
                <span>Pastikan selalu logout setelah selesai jika menggunakan perangkat komputer bersama.</span>
            </div>
        </div>

    </div>

</div>
@endsection
