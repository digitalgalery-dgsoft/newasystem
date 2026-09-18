@extends('layouts.cbt')

@section('title', 'Dashboard Test Online CBT | ESA Groups')

@section('content')
<div class="space-y-6">

    <!-- HERO WELCOME BANNER (FULL-WIDTH MOBILE RESPONSIVE) -->
    <div class="bg-gradient-to-r from-slate-900 via-primary-900 to-primary-700 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -right-16 -top-16 w-60 h-60 bg-blue-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-5">
            <div class="flex items-center gap-4">
                <!-- Avatar -->
                <div class="relative flex-shrink-0">
                    @if($candidate->photo_path)
                        <img src="{{ $candidate->photo_url }}" alt="Foto Profil" class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl object-cover ring-4 ring-white/20 shadow-lg" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($candidate->full_name) }}&background=0F52BA&color=fff&size=256';">
                    @else
                        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-white/10 backdrop-blur-md text-white flex items-center justify-center ring-4 ring-white/20 font-black text-2xl shadow-lg">
                            {{ substr($candidate->full_name, 0, 1) }}
                        </div>
                    @endif
                    <span class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 border-2 border-slate-900 rounded-full"></span>
                </div>

                <!-- Info -->
                <div>
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-blue-500/20 text-blue-200 text-[11px] font-semibold mb-1">
                        <i class="fa-solid fa-id-badge text-[10px]"></i>
                        <span>NIK: {{ $candidate->nik }}</span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-black tracking-tight text-white leading-tight">
                        Selamat Datang, {{ $candidate->full_name }}!
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-300 mt-0.5">
                        Posisi: <span class="font-bold text-white">{{ $candidate->applied_job ?? 'Kandidat Pelamar' }}</span> • Area: <span class="font-bold text-white">{{ $candidate->area ?? 'JAKARTA' }}</span>
                    </p>
                </div>
            </div>

            <!-- Profile Action / Status Pill -->
            <div class="flex sm:flex-col items-center sm:items-end justify-between w-full sm:w-auto pt-3 sm:pt-0 border-t sm:border-t-0 border-white/10 gap-2">
                @if($candidate->all_tests_completed)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 text-xs font-bold">
                        <i class="fa-solid fa-circle-check"></i>
                        Semua Tes Selesai
                    </span>
                @else
                    <a href="{{ route('cbt.profile') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white text-slate-900 hover:bg-blue-50 text-xs font-bold shadow-md transition-all">
                        <i class="fa-solid fa-user-pen text-primary"></i>
                        <span>{{ $isProfileComplete ? 'Lihat / Edit Profil' : 'Lengkapi Profil' }}</span>
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- PROFILE STATUS ALERT SECTION -->
    <section>
        @if($candidate->all_tests_completed)
            <div class="bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200 text-emerald-900 p-5 rounded-2xl shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-500 text-white flex items-center justify-center flex-shrink-0 shadow-md shadow-emerald-500/30">
                        <i class="fa-solid fa-award text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm sm:text-base text-emerald-950">Selamat! Seluruh Tes Telah Diselesaikan 🎉</h3>
                        <p class="text-xs text-emerald-800 mt-0.5">Jawaban dan bukti pengerjaan Anda telah tersimpan dengan aman. Tim HRD Rekrutmen akan meninjau hasil evaluasi Anda.</p>
                    </div>
                </div>
            </div>
        @elseif(!$isProfileComplete)
            <div class="bg-gradient-to-r from-rose-50 to-amber-50 border border-rose-200 text-rose-900 p-5 rounded-2xl shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-start sm:items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-rose-500 text-white flex items-center justify-center flex-shrink-0 shadow-md shadow-rose-500/30 mt-1 sm:mt-0">
                        <i class="fa-solid fa-triangle-exclamation text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm sm:text-base text-rose-950">Data Profil Belum Lengkap!</h3>
                        <p class="text-xs text-rose-700 mt-0.5">Sesuai ketentuan, Anda wajib melengkapi data pribadi, keluarga, kontak darurat, rekening, dan tanda tangan digital sebelum modul tes dapat dibuka.</p>
                    </div>
                </div>
                <a href="{{ route('cbt.profile') }}" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-md shadow-rose-600/20 text-center transition-all flex items-center justify-center gap-1.5 flex-shrink-0">
                    <i class="fa-solid fa-arrow-right"></i>
                    <span>Lengkapi Profil Sekarang</span>
                </a>
            </div>
        @else
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 text-blue-900 p-5 rounded-2xl shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-primary text-white flex items-center justify-center flex-shrink-0 shadow-md shadow-primary/30">
                        <i class="fa-solid fa-circle-check text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm sm:text-base text-blue-950">Profil Sudah Lengkap! ✨</h3>
                        <p class="text-xs text-blue-800 mt-0.5">Semua modul tes di bawah ini telah terbuka. Silakan kerjakan dengan fokus, teliti, dan pastikan koneksi internet Anda stabil.</p>
                    </div>
                </div>
            </div>
        @endif
    </section>

    <!-- 3 ONLINE TEST CARDS GRID -->
    <section>
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-black text-slate-800 tracking-tight">Modul Tes Online Rekrutmen</h2>
                <p class="text-xs text-slate-500">Pilih modul tes untuk memulai proses evaluasi kemampuan Anda.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- 1. TES KEPRIBADIAN (DISC) -->
            @php
                $psikoSelesai = $candidate->is_psikotes_done;
            @endphp
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-lg transition-all flex flex-col group">
                <!-- Card Header Art -->
                <div class="h-36 bg-gradient-to-br from-indigo-900 via-indigo-700 to-blue-600 p-6 flex items-center justify-between text-white relative overflow-hidden">
                    <div class="relative z-10">
                        <span class="text-[10px] font-bold tracking-widest uppercase text-indigo-200 block mb-1">Tahap 1</span>
                        <h3 class="text-lg font-black text-white">Tes Kepribadian</h3>
                        <p class="text-xs text-indigo-200">DISC Personality Assessment</p>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center text-white text-2xl shadow-inner relative z-10 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-brain"></i>
                    </div>
                    <div class="absolute -right-6 -bottom-6 w-28 h-28 bg-white/10 rounded-full blur-xl pointer-events-none"></div>
                </div>

                <!-- Card Body -->
                <div class="p-6 flex-1 flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500">Status Tes:</span>
                            @if($psikoSelesai)
                                <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold text-[11px] flex items-center gap-1">
                                    <i class="fa-solid fa-check text-[10px]"></i> Selesai
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200 font-bold text-[11px] flex items-center gap-1">
                                    <i class="fa-solid fa-clock text-[10px]"></i> Belum Dikerjakan
                                </span>
                            @endif
                        </div>

                        <div class="text-xs text-slate-600 leading-relaxed bg-slate-50 p-3 rounded-xl border border-slate-100">
                            Terdiri dari <strong>24 butir soal</strong> pilihan preferensi karakter diri Anda. Tidak ada jawaban salah.
                            @if($psikoSelesai && $psikotesResult)
                                <div class="mt-2 pt-2 border-t border-slate-200 text-primary font-bold text-[11px]">
                                    Waktu: {{ $psikotesResult->test_details['duration_formatted'] ?? '-' }} • Tipe: {{ $psikotesResult->test_details['dominant_code'] ?? 'DISC' }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Action Button -->
                    <div class="pt-5 mt-4 border-t border-slate-100">
                        @if(!$isProfileComplete)
                            <button disabled class="w-full py-3 px-4 rounded-xl font-bold text-xs bg-slate-100 text-slate-400 cursor-not-allowed flex items-center justify-center gap-2">
                                <i class="fa-solid fa-lock"></i>
                                <span>Terkunci (Lengkapi Profil)</span>
                            </button>
                        @elseif($psikoSelesai)
                            <a href="{{ route('cbt.kepribadian.result') }}" class="w-full py-3 px-4 rounded-xl font-bold text-xs bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 transition-all flex items-center justify-center gap-2">
                                <i class="fa-solid fa-chart-pie"></i>
                                <span>Lihat Hasil Evaluasi</span>
                            </a>
                        @else
                            <a href="{{ route('cbt.kepribadian') }}" class="w-full py-3 px-4 rounded-xl font-bold text-xs bg-primary hover:bg-primary-700 text-white shadow-md shadow-primary/20 transition-all flex items-center justify-center gap-2">
                                <span>Mulai Tes Sekarang</span>
                                <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- 2. TES MATEMATIKA -->
            @php
                $mathSelesai = $candidate->is_math_done;
            @endphp
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-lg transition-all flex flex-col group">
                <!-- Card Header Art -->
                <div class="h-36 bg-gradient-to-br from-blue-900 via-blue-700 to-cyan-600 p-6 flex items-center justify-between text-white relative overflow-hidden">
                    <div class="relative z-10">
                        <span class="text-[10px] font-bold tracking-widest uppercase text-blue-200 block mb-1">Tahap 2</span>
                        <h3 class="text-lg font-black text-white">Tes Matematika</h3>
                        <p class="text-xs text-blue-200">10 Soal Logika & Aritmetika (10 Menit)</p>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center text-white text-2xl shadow-inner relative z-10 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-calculator"></i>
                    </div>
                    <div class="absolute -right-6 -bottom-6 w-28 h-28 bg-white/10 rounded-full blur-xl pointer-events-none"></div>
                </div>

                <!-- Card Body -->
                <div class="p-6 flex-1 flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500">Status Tes:</span>
                            @if($mathSelesai)
                                <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold text-[11px] flex items-center gap-1">
                                    <i class="fa-solid fa-check text-[10px]"></i> Selesai
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200 font-bold text-[11px] flex items-center gap-1">
                                    <i class="fa-solid fa-clock text-[10px]"></i> Belum Dikerjakan
                                </span>
                            @endif
                        </div>

                        <div class="text-xs text-slate-600 leading-relaxed bg-slate-50 p-3 rounded-xl border border-slate-100">
                            Menguji ketelitian hitung dagang, persentase, dan deret angka. Batas waktu pengerjaan <strong>10:00 menit</strong>.
                            @if($mathSelesai && $mathResult)
                                <div class="mt-2 pt-2 border-t border-slate-200 text-blue-700 font-bold text-[11px]">
                                    Skor: <span class="text-emerald-600 text-sm font-black">{{ $mathResult->score }}</span> / 100 • Durasi: {{ $mathResult->test_details['duration_formatted'] ?? '-' }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Action Button -->
                    <div class="pt-5 mt-4 border-t border-slate-100">
                        @if(!$isProfileComplete)
                            <button disabled class="w-full py-3 px-4 rounded-xl font-bold text-xs bg-slate-100 text-slate-400 cursor-not-allowed flex items-center justify-center gap-2">
                                <i class="fa-solid fa-lock"></i>
                                <span>Terkunci (Lengkapi Profil)</span>
                            </button>
                        @elseif($mathSelesai)
                            <a href="{{ route('cbt.matematika.result') }}" class="w-full py-3 px-4 rounded-xl font-bold text-xs bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 transition-all flex items-center justify-center gap-2">
                                <i class="fa-solid fa-chart-simple"></i>
                                <span>Lihat Skor & Hasil</span>
                            </a>
                        @else
                            <a href="{{ route('cbt.matematika') }}" class="w-full py-3 px-4 rounded-xl font-bold text-xs bg-blue-600 hover:bg-blue-700 text-white shadow-md shadow-blue-600/20 transition-all flex items-center justify-center gap-2">
                                <span>Mulai Tes Sekarang</span>
                                <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- 3. TES KOMPUTER -->
            @php
                $komptSelesai = $candidate->is_komputer_done;
            @endphp
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-lg transition-all flex flex-col group">
                <!-- Card Header Art -->
                <div class="h-36 bg-gradient-to-br from-slate-900 via-slate-800 to-teal-700 p-6 flex items-center justify-between text-white relative overflow-hidden">
                    <div class="relative z-10">
                        <span class="text-[10px] font-bold tracking-widest uppercase text-teal-200 block mb-1">Tahap 3</span>
                        <h3 class="text-lg font-black text-white">Tes Komputer</h3>
                        <p class="text-xs text-teal-200">Praktik Spreadsheet / Office & Unggah Bukti</p>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center text-white text-2xl shadow-inner relative z-10 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-laptop-code"></i>
                    </div>
                    <div class="absolute -right-6 -bottom-6 w-28 h-28 bg-white/10 rounded-full blur-xl pointer-events-none"></div>
                </div>

                <!-- Card Body -->
                <div class="p-6 flex-1 flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500">Status Tes:</span>
                            @if($komptSelesai)
                                <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold text-[11px] flex items-center gap-1">
                                    <i class="fa-solid fa-check text-[10px]"></i> Selesai
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200 font-bold text-[11px] flex items-center gap-1">
                                    <i class="fa-solid fa-clock text-[10px]"></i> Belum Dikerjakan
                                </span>
                            @endif
                        </div>

                        <div class="text-xs text-slate-600 leading-relaxed bg-slate-50 p-3 rounded-xl border border-slate-100">
                            Praktik keterampilan komputer berbasis studi kasus operasional. Catat waktu pengerjaan dan unggah tangkapan layar/bukti file.
                            @if($komptSelesai && $komputerResult)
                                <div class="mt-2 pt-2 border-t border-slate-200 text-teal-700 font-bold text-[11px]">
                                    Waktu: {{ $komputerResult->test_details['duration_formatted'] ?? '-' }} • Bukti: Terkirim
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Action Button -->
                    <div class="pt-5 mt-4 border-t border-slate-100">
                        @if(!$isProfileComplete)
                            <button disabled class="w-full py-3 px-4 rounded-xl font-bold text-xs bg-slate-100 text-slate-400 cursor-not-allowed flex items-center justify-center gap-2">
                                <i class="fa-solid fa-lock"></i>
                                <span>Terkunci (Lengkapi Profil)</span>
                            </button>
                        @elseif($komptSelesai)
                            <button disabled class="w-full py-3 px-4 rounded-xl font-bold text-xs bg-slate-100 text-emerald-700 border border-emerald-200 cursor-default flex items-center justify-center gap-2">
                                <i class="fa-solid fa-circle-check"></i>
                                <span>Bukti Telah Terunggah</span>
                            </button>
                        @else
                            <a href="{{ route('cbt.komputer') }}" class="w-full py-3 px-4 rounded-xl font-bold text-xs bg-teal-600 hover:bg-teal-700 text-white shadow-md shadow-teal-600/20 transition-all flex items-center justify-center gap-2">
                                <span>Mulai Tes Sekarang</span>
                                <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- RIWAYAT LOG AKTIVITAS KANDIDAT -->
    <section class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-blue-50 text-primary flex items-center justify-center text-sm">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Riwayat Aktivitas Ujian</h3>
                    <p class="text-[11px] text-slate-400">Pencatatan rekam jejak aktivitas akun Anda</p>
                </div>
            </div>
            <span class="text-[11px] font-bold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-lg">
                {{ $logs->count() }} Aktivitas
            </span>
        </div>

        <div class="flow-root">
            @if($logs->count() > 0)
                <ul role="list" class="-mb-6">
                    @foreach($logs as $index => $log)
                        <li>
                            <div class="relative pb-6">
                                @if(!$loop->last)
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-slate-200" aria-hidden="true"></span>
                                @endif
                                <div class="relative flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-full bg-primary-50 border-2 border-primary-200 text-primary flex items-center justify-center flex-shrink-0 text-xs">
                                        <i class="fa-solid fa-check"></i>
                                    </div>
                                    <div class="min-w-0 flex-1 flex justify-between items-center space-x-4">
                                        <div>
                                            <p class="text-xs font-semibold text-slate-800">{{ $log->activity }}</p>
                                            <span class="text-[10px] text-slate-400">{{ $log->browser ?? 'Browser' }}</span>
                                        </div>
                                        <div class="text-right text-[11px] font-mono text-slate-400 whitespace-nowrap">
                                            {{ $log->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="text-center py-8 text-xs text-slate-400">
                    <i class="fa-solid fa-inbox text-3xl mb-2 text-slate-300 block"></i>
                    Belum ada aktivitas yang tercatat.
                </div>
            @endif
        </div>
    </section>

</div>
@endsection
