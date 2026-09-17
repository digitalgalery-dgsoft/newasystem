@extends('layouts.app')

@section('title', 'Pusat Fitur & Modul Aplikasi - Attendance Portal')

@section('content')
<div class="space-y-6">

    <!-- Page Header Card -->
    <div class="page-header-card flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-13 h-13 rounded-2xl bg-gradient-to-br from-primary-700 via-primary-600 to-blue-500 text-white flex items-center justify-center text-2xl shadow-lg shadow-primary-500/25 flex-shrink-0">
                <i class="fa-solid fa-grid-2"></i>
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">Pusat Fitur & Modul Aplikasi</h1>
                    @if(Auth::check() && Auth::user()->isAdmin())
                        <span class="badge-pill bg-indigo-50 text-indigo-700 border-indigo-200">
                            <i class="fa-solid fa-shield-halved text-[10px]"></i> AKSES: ADMINISTRATOR
                        </span>
                    @else
                        <span class="badge-pill bg-emerald-50 text-emerald-700 border-emerald-200">
                            <i class="fa-solid fa-user-check text-[10px]"></i> AKSES: MODUL OPERASIONAL
                        </span>
                    @endif
                    <span class="badge-pill bg-blue-50 text-primary border-blue-200">
                        <i class="fa-solid fa-circle-check text-[10px]"></i> Cloud Active
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    @if(Auth::check() && Auth::user()->isAdmin())
                        Direktori lengkap fitur operasional HRD, rekrutmen kandidat interview, serta kontrol penuh Master Data karyawan & prinsiple.
                    @else
                        Direktori modul operasional, presensi GPS kehadiran, pengajuan izin & cuti, evaluasi kinerja KPI, dan portal lowongan rekrutmen.
                    @endif
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2 self-start md:self-auto">
            <a href="{{ route('interview.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary text-white text-xs font-bold hover:bg-primary-700 transition-all shadow-md shadow-primary-600/20">
                <i class="fa-solid fa-user-tie"></i>
                <span>Masuk Sub-Menu Interview</span>
            </a>
        </div>
    </div>

    <!-- Quick Stats Metric Row -->
    <div class="grid grid-cols-2 {{ (Auth::check() && Auth::user()->isAdmin()) ? 'lg:grid-cols-4' : 'lg:grid-cols-3' }} gap-4">
        <div class="stat-box">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-semibold tracking-wider text-slate-400 uppercase">Talent Pool Rekrutmen</div>
                    <div class="text-2xl font-black text-slate-900 mt-1">{{ $interviewStats['total'] }}</div>
                    <div class="text-[11px] text-primary font-semibold mt-0.5">
                        <i class="fa-solid fa-circle-check"></i> {{ $interviewStats['active'] }} Kandidat Aktif
                    </div>
                </div>
                <div class="stat-box-icon bg-blue-50 text-primary">
                    <i class="fa-solid fa-user-check"></i>
                </div>
            </div>
        </div>

        @if(Auth::check() && Auth::user()->isAdmin())
        <div class="stat-box">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-semibold tracking-wider text-slate-400 uppercase">Master Karyawan</div>
                    <div class="text-2xl font-black text-slate-900 mt-1">{{ $employeeStats['total'] }}</div>
                    <div class="text-[11px] text-emerald-600 font-semibold mt-0.5">
                        <i class="fa-solid fa-circle-dot"></i> {{ $employeeStats['aktif'] }} Karyawan Aktif
                    </div>
                </div>
                <div class="stat-box-icon bg-emerald-50 text-emerald-600">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
        </div>

        <div class="stat-box">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-semibold tracking-wider text-slate-400 uppercase">Master Prinsiple</div>
                    <div class="text-2xl font-black text-slate-900 mt-1">{{ $principleStats['total'] }}</div>
                    <div class="text-[11px] text-purple-600 font-semibold mt-0.5">
                        <i class="fa-solid fa-handshake"></i> {{ $principleStats['active'] }} Rekanan Aktif
                    </div>
                </div>
                <div class="stat-box-icon bg-purple-50 text-purple-600">
                    <i class="fa-solid fa-building"></i>
                </div>
            </div>
        </div>
        @else
        <div class="stat-box">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-semibold tracking-wider text-slate-400 uppercase">Layanan Kepegawaian</div>
                    <div class="text-2xl font-black text-emerald-600 mt-1">Aktif</div>
                    <div class="text-[11px] text-emerald-600 font-semibold mt-0.5">
                        <i class="fa-solid fa-shield-check"></i> Presensi, Cuti & KPI
                    </div>
                </div>
                <div class="stat-box-icon bg-emerald-50 text-emerald-600">
                    <i class="fa-solid fa-id-badge"></i>
                </div>
            </div>
        </div>
        @endif

        <div class="stat-box">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-semibold tracking-wider text-slate-400 uppercase">Total Modul Portal</div>
                    <div class="text-2xl font-black text-slate-900 mt-1">{{ (Auth::check() && Auth::user()->isAdmin()) ? '9 Modul' : '7 Modul' }}</div>
                    <div class="text-[11px] text-slate-500 font-medium mt-0.5">
                        Terhubung & Terintegrasi
                    </div>
                </div>
                <div class="stat-box-icon bg-amber-50 text-amber-600">
                    <i class="fa-solid fa-cubes"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Heading -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-base font-bold text-slate-900">Katalog Fitur & Modul Utama</h2>
            <p class="text-xs text-slate-500">Pilih modul di bawah ini untuk mengakses menu atau sub-menu terkait.</p>
        </div>
        <div class="text-xs text-slate-400 font-medium hidden sm:block">
            Attendance Enterprise Version 12.4
        </div>
    </div>

    <!-- Feature Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        <!-- CARD 1: KANDIDAT INTERVIEW (FEATURE HIGHLIGHT / SUB-MENU UTAMA) -->
        <div class="bg-white rounded-2xl border-2 border-primary shadow-lg shadow-primary-500/10 p-6 relative overflow-hidden flex flex-col justify-between transition-all hover:-translate-y-1">
            <div class="absolute top-0 right-0">
                <span class="bg-gradient-to-l from-primary to-blue-600 text-white text-[10px] font-extrabold px-3 py-1 rounded-bl-xl tracking-wider shadow-sm uppercase">
                    ⭐ SUB-MENU UTAMA AKTIF
                </span>
            </div>

            <div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-xl bg-primary text-white flex items-center justify-center text-xl shadow-md shadow-primary-500/25">
                        <i class="fa-solid fa-user-tie"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Kandidat Interview</h3>
                        <span class="text-[11px] text-primary font-semibold">Sub-Menu Rekrutmen & Seleksi</span>
                    </div>
                </div>

                <p class="text-xs text-slate-600 leading-relaxed mb-4">
                    Pusat seleksi wawancara kerja kandidat. Dilengkapi 14-kolom tabel interview, otomasi link WhatsApp, penilaian DISC, tes matematika & komputer, referensi cek, dan portal token approval prinsiple.
                </p>

                <!-- Mini Status Counters -->
                <div class="grid grid-cols-3 gap-2 p-2.5 rounded-xl bg-slate-50 border border-slate-200/80 mb-4 text-center">
                    <div>
                        <div class="text-[10px] font-semibold text-slate-400">Total</div>
                        <div class="text-sm font-bold text-slate-800">{{ $interviewStats['total'] }}</div>
                    </div>
                    <div>
                        <div class="text-[10px] font-semibold text-slate-400">Aktif</div>
                        <div class="text-sm font-bold text-primary">{{ $interviewStats['active'] }}</div>
                    </div>
                    <div>
                        <div class="text-[10px] font-semibold text-slate-400">Lulus</div>
                        <div class="text-sm font-bold text-emerald-600">{{ $interviewStats['done'] }}</div>
                    </div>
                </div>

                <!-- Submenu Links List -->
                <div class="space-y-1.5 mb-5">
                    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Sub-Menu Terkait:</div>
                    <div class="flex flex-wrap gap-1.5">
                        <a href="{{ route('interview.walk') }}" class="text-[11px] px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-primary-50 hover:text-primary font-medium text-slate-600 transition-all">
                            <i class="fa-solid fa-person-walking-arrow-right mr-1"></i> Walk-in Interview
                        </a>
                        <a href="{{ route('interview.done') }}" class="text-[11px] px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-primary-50 hover:text-primary font-medium text-slate-600 transition-all">
                            <i class="fa-solid fa-circle-check mr-1"></i> Interview Selesai
                        </a>
                        <a href="{{ route('interview.arsip') }}" class="text-[11px] px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-primary-50 hover:text-primary font-medium text-slate-600 transition-all">
                            <i class="fa-solid fa-box-archive mr-1"></i> Arsip
                        </a>
                    </div>
                </div>
            </div>

            <a href="{{ route('interview.index') }}" class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl bg-primary hover:bg-primary-700 text-white text-xs font-bold transition-all shadow-md shadow-primary-600/20">
                <span>Buka Menu Interview</span>
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        @if(Auth::check() && Auth::user()->isAdmin())
        <!-- CARD 2: MASTER KARYAWAN -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col justify-between hover:border-slate-300 hover:shadow-md transition-all">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl border border-emerald-100">
                            <i class="fa-solid fa-users-gear"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Master Karyawan</h3>
                            <span class="text-[11px] text-slate-400 font-medium">Database Induk Pegawai</span>
                        </div>
                    </div>
                    <span class="badge-pill bg-emerald-50 text-emerald-700 border-emerald-200">MASTER</span>
                </div>

                <p class="text-xs text-slate-600 leading-relaxed mb-4">
                    Manajemen data karyawan inhouse dan ratecard. Monitoring batas masa kerja 5 tahun, kelengkapan komponen penggajian, review karyawan baru, dan pengunduran diri (resign).
                </p>

                <!-- Mini Status Counters -->
                <div class="grid grid-cols-3 gap-2 p-2.5 rounded-xl bg-slate-50 border border-slate-200/80 mb-4 text-center">
                    <div>
                        <div class="text-[10px] font-semibold text-slate-400">Total</div>
                        <div class="text-sm font-bold text-slate-800">{{ $employeeStats['total'] }}</div>
                    </div>
                    <div>
                        <div class="text-[10px] font-semibold text-slate-400">Aktif</div>
                        <div class="text-sm font-bold text-emerald-600">{{ $employeeStats['aktif'] }}</div>
                    </div>
                    <div>
                        <div class="text-[10px] font-semibold text-slate-400">Review Baru</div>
                        <div class="text-sm font-bold text-amber-600">{{ $employeeStats['review'] }}</div>
                    </div>
                </div>

                <!-- Submenu Links List -->
                <div class="space-y-1.5 mb-5">
                    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Sub-Menu Terkait:</div>
                    <div class="flex flex-wrap gap-1.5">
                        <a href="{{ route('master.karyawan.index', ['tipe' => 'Inhouse']) }}" class="text-[11px] px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 font-medium text-slate-600 transition-all">
                            Karyawan Inhouse
                        </a>
                        <a href="{{ route('master.karyawan.index', ['tipe' => 'RateCard']) }}" class="text-[11px] px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 font-medium text-slate-600 transition-all">
                            RateCard
                        </a>
                        <a href="{{ route('master.karyawan.index', ['status' => 'Review']) }}" class="text-[11px] px-2.5 py-1 rounded-lg bg-amber-50 text-amber-700 hover:bg-amber-100 font-semibold transition-all">
                            Review Karyawan
                        </a>
                    </div>
                </div>
            </div>

            <a href="{{ route('master.karyawan.index') }}" class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-all shadow-sm">
                <span>Kelola Master Karyawan</span>
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <!-- CARD 3: MASTER PRINSIPLE -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col justify-between hover:border-slate-300 hover:shadow-md transition-all">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl border border-purple-100">
                            <i class="fa-solid fa-building-shield"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Master Prinsiple</h3>
                            <span class="text-[11px] text-slate-400 font-medium">Rekanan & Mitra Klien</span>
                        </div>
                    </div>
                    <span class="badge-pill bg-purple-50 text-purple-700 border-purple-200">MASTER</span>
                </div>

                <p class="text-xs text-slate-600 leading-relaxed mb-4">
                    Pengelolaan direktori mitra korporat (Samsung, L'Oreal, Unilever, Mayora, dll.) dan pengelompokan ke 4 entitas inhouse (Arina, Alva, Anugrah, Abadi) serta kontak person PIC.
                </p>

                <!-- Mini Status Counters -->
                <div class="grid grid-cols-2 gap-2 p-2.5 rounded-xl bg-slate-50 border border-slate-200/80 mb-4 text-center">
                    <div>
                        <div class="text-[10px] font-semibold text-slate-400">Total Rekanan</div>
                        <div class="text-sm font-bold text-slate-800">{{ $principleStats['total'] }} Prinsiple</div>
                    </div>
                    <div>
                        <div class="text-[10px] font-semibold text-slate-400">Status Aktif</div>
                        <div class="text-sm font-bold text-purple-600">{{ $principleStats['active'] }} Aktif</div>
                    </div>
                </div>

                <!-- Submenu Links List -->
                <div class="space-y-1.5 mb-5">
                    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Inhouse Terhubung:</div>
                    <div class="flex flex-wrap gap-1.5 text-[11px] text-slate-600">
                        <span class="px-2 py-0.5 rounded bg-slate-100 font-medium">PT Arina Multi Karya</span>
                        <span class="px-2 py-0.5 rounded bg-slate-100 font-medium">PT Alva Karya Perkasa</span>
                        <span class="px-2 py-0.5 rounded bg-slate-100 font-medium">PT Anugrah Terpercaya Kerja</span>
                    </div>
                </div>
            </div>

            <a href="{{ route('master.prinsiple.index') }}" class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold transition-all shadow-md shadow-purple-600/20">
                <span>Kelola Master Prinsiple</span>
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        @endif

        <!-- CARD 4: PRESENSI & ATTENDANCE GPS -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col justify-between hover:border-slate-300 hover:shadow-md transition-all">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center text-xl border border-sky-100">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Presensi & Kehadiran GPS</h3>
                            <span class="text-[11px] text-slate-400 font-medium">Att-Admin Engine</span>
                        </div>
                    </div>
                    <span class="badge-pill bg-sky-50 text-sky-700 border-sky-200">OPERASIONAL</span>
                </div>

                <p class="text-xs text-slate-600 leading-relaxed mb-4">
                    Sistem presensi mobile terintegrasi dengan validasi GPS geofencing radius outlet, deteksi biometrik selfie wajah, rekapitulasi kehadiran real-time, dan manajemen shift kerja.
                </p>

                <div class="p-3 rounded-xl bg-sky-50/50 border border-sky-100 mb-4 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-xs font-bold text-slate-800">Tingkat Kehadiran Hari Ini</span>
                    </div>
                    <span class="text-xs font-extrabold text-sky-700">96.4% Terverifikasi</span>
                </div>
            </div>

            <a href="{{ route('presensi.index') }}" class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-xs font-bold transition-all shadow-md shadow-sky-600/20">
                <span>Monitoring Presensi</span>
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <!-- CARD 5: PENGAJUAN CUTI & IZIN -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col justify-between hover:border-slate-300 hover:shadow-md transition-all">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl border border-amber-100">
                            <i class="fa-solid fa-calendar-days"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Pengajuan Cuti & Izin</h3>
                            <span class="text-[11px] text-slate-400 font-medium">Layanan Administrasi HR</span>
                        </div>
                    </div>
                    <span class="badge-pill bg-amber-50 text-amber-700 border-amber-200">HR SERVICE</span>
                </div>

                <p class="text-xs text-slate-600 leading-relaxed mb-4">
                    Alur persetujuan cuti tahunan, izin sakit bersurat dokter, cuti khusus (melahirkan/menikah), serta kalkulasi saldo kuota hak cuti karyawan otomatis.
                </p>

                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 mb-4 flex items-center justify-between text-xs">
                    <span class="text-slate-600 font-medium">Pengajuan Menunggu Approval:</span>
                    <span class="font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200">3 Menunggu</span>
                </div>
            </div>

            <a href="{{ route('cuti.index') }}" class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold transition-all shadow-md shadow-amber-600/20">
                <span>Layanan Cuti & Izin</span>
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <!-- CARD 6: EVALUASI KINERJA & KPI -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col justify-between hover:border-slate-300 hover:shadow-md transition-all">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl border border-indigo-100">
                            <i class="fa-solid fa-chart-pie"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Penilaian Kinerja & KPI</h3>
                            <span class="text-[11px] text-slate-400 font-medium">Performance Evaluation</span>
                        </div>
                    </div>
                    <span class="badge-pill bg-indigo-50 text-indigo-700 border-indigo-200">PERFORMANCE</span>
                </div>

                <p class="text-xs text-slate-600 leading-relaxed mb-4">
                    Formulir evaluasi performa kerja berkala staf promotor, SPV, dan sales lapangan dengan indikator capaian target, review kedisiplinan, dan perangkingan kinerja.
                </p>

                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 mb-4 flex items-center justify-between text-xs">
                    <span class="text-slate-600 font-medium">Periode Berjalan:</span>
                    <span class="font-bold text-indigo-600">{{ date('F Y') }}</span>
                </div>
            </div>

            <a href="{{ route('kpi.index') }}" class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition-all shadow-md shadow-indigo-600/20">
                <span>Evaluasi KPI</span>
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <!-- CARD 7: PKWT & KONTRAK KERJA -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col justify-between hover:border-slate-300 hover:shadow-md transition-all">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-xl border border-teal-100">
                            <i class="fa-solid fa-file-contract"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900">PKWT & Kontrak Kerja</h3>
                            <span class="text-[11px] text-slate-400 font-medium">Legal & Compliance</span>
                        </div>
                    </div>
                    <span class="badge-pill bg-teal-50 text-teal-700 border-teal-200">LEGAL HR</span>
                </div>

                <p class="text-xs text-slate-600 leading-relaxed mb-4">
                    Manajemen berkas perjanjian kerja waktu tertentu (PKWT), tanda tangan digital, dan notifikasi peringatan masa kadaluarsa kontrak 30 & 60 hari sebelum selesai.
                </p>

                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 mb-4 flex items-center justify-between text-xs">
                    <span class="text-slate-600 font-medium">Expired &lt; 30 Hari:</span>
                    <span class="font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded-full border border-rose-200">2 Kontrak</span>
                </div>
            </div>

            <button class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold transition-all shadow-md shadow-teal-600/20">
                <span>Kelola PKWT</span>
                <i class="fa-solid fa-arrow-right"></i>
            </button>
        </div>

        <!-- CARD 8: HELPDESK & TIKET IT -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col justify-between hover:border-slate-300 hover:shadow-md transition-all">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl border border-rose-100">
                            <i class="fa-solid fa-headset"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Helpdesk & IT Support</h3>
                            <span class="text-[11px] text-slate-400 font-medium">Layanan Bantuan Internal</span>
                        </div>
                    </div>
                    <span class="badge-pill bg-rose-50 text-rose-700 border-rose-200">SUPPORT</span>
                </div>

                <p class="text-xs text-slate-600 leading-relaxed mb-4">
                    Pusat tiket kendala sistem, permohonan reset password, kendala titik koordinat GPS presensi, dan pembaruan perangkat kerja staf operasional.
                </p>

                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 mb-4 flex items-center justify-between text-xs">
                    <span class="text-slate-600 font-medium">Status Tiket Internal:</span>
                    <span class="font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">Semua Teratasi</span>
                </div>
            </div>

            <a href="{{ route('helpdesk.index') }}" class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition-all shadow-md shadow-rose-600/20">
                <span>Pusat Bantuan Tiket</span>
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <!-- CARD 9: WHATSAPP BOT AUTOMATION -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col justify-between hover:border-slate-300 hover:shadow-md transition-all">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl border border-emerald-100">
                            <i class="fa-brands fa-whatsapp text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900">WhatsApp Gateway</h3>
                            <span class="text-[11px] text-slate-400 font-medium">Otomasi Notifikasi & Bot</span>
                        </div>
                    </div>
                    <span class="badge-pill bg-emerald-50 text-emerald-700 border-emerald-200">AUTOMATION</span>
                </div>

                <p class="text-xs text-slate-600 leading-relaxed mb-4">
                    Gateway perpesanan otomatis untuk blast undangan jadwal interview kandidat, reminder jam presensi harian, dan notifikasi kelulusan seleksi pelamar.
                </p>

                <div class="p-3 rounded-xl bg-emerald-50/50 border border-emerald-100 mb-4 flex items-center justify-between text-xs">
                    <span class="text-slate-600 font-medium">Status Gateway:</span>
                    <span class="font-bold text-emerald-700 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Terhubung (Online)
                    </span>
                </div>
            </div>

            <a href="{{ route('interview.index') }}" class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-all shadow-md shadow-emerald-600/20">
                <span>Konfigurasi WhatsApp Bot</span>
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

    </div>

</div>
@endsection