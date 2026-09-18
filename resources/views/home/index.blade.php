@extends('layouts.public')

@section('title', 'ASystem - Integrated Support System ESA Groups')

@section('content')
<div class="space-y-16 sm:space-y-24 py-6 sm:py-10">
    <!-- 1. HERO SECTION -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-950 via-blue-950 to-indigo-950 text-white p-8 sm:p-14 lg:p-20 shadow-2xl border border-blue-800/30">
            <!-- Background Ambient Glow -->
            <div class="absolute -right-20 -top-20 w-96 h-96 bg-blue-500/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -left-20 -bottom-20 w-96 h-96 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 max-w-3xl space-y-6">
                <!-- Badge -->
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-200 text-xs font-semibold backdrop-blur-md">
                    <i class="fa-solid fa-sparkles text-amber-400"></i>
                    <span>Official Integrated System for ESA Groups</span>
                </div>

                <!-- Main Title -->
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-[1.15] text-white">
                    Sistem Terpadu <br class="hidden sm:inline">
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-sky-300 to-indigo-300">Rekrutmen &amp; SDM Cerdas</span>
                </h1>

                <!-- Subtitle -->
                <p class="text-base sm:text-lg text-slate-300 font-normal leading-relaxed max-w-2xl">
                    Platform karier dan seleksi tenaga kerja modern berbasis kecerdasan buatan (AI) untuk seluruh unit bisnis dan mitra kerja ESA Groups. Cepat, transparan, dan terstandarisasi.
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-wrap items-center gap-3 pt-4">
                    <a href="{{ route('job.public') }}" class="px-6 py-3.5 rounded-2xl bg-gradient-to-r from-primary to-blue-600 hover:from-blue-600 hover:to-primary text-white font-black text-xs sm:text-sm shadow-xl shadow-blue-900/40 hover:shadow-primary/50 hover:-translate-y-0.5 transition-all flex items-center gap-2">
                        <i class="fa-solid fa-briefcase text-sm"></i>
                        <span>Jelajahi Lowongan Kerja</span>
                    </a>
                    <a href="{{ route('login') }}" class="px-5 py-3.5 rounded-2xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs sm:text-sm backdrop-blur-md border border-white/20 transition-all hover:-translate-y-0.5 flex items-center gap-2 shadow-sm group">
                        <i class="fa-solid fa-users-viewfinder text-sky-400 group-hover:scale-110 transition-transform"></i>
                        <span>Talent Pool / Rekrutmen - Login Disini</span>
                    </a>
                    <a href="https://asystem.co.id/v3/login" target="_blank" class="px-5 py-3.5 rounded-2xl bg-indigo-500/20 hover:bg-indigo-500/35 text-indigo-100 font-bold text-xs sm:text-sm backdrop-blur-md border border-indigo-400/30 transition-all hover:-translate-y-0.5 flex items-center gap-2 shadow-sm group">
                        <i class="fa-solid fa-arrow-up-right-from-square text-indigo-300 group-hover:scale-110 transition-transform"></i>
                        <span>Fitur Lain - Akses ASystem V3</span>
                    </a>
                    <a href="https://whatsapp.com/channel/0029VbDqNdb60eBgrCxsnt07" target="_blank" class="px-4 py-3.5 rounded-2xl bg-emerald-600/90 hover:bg-emerald-500 text-white font-bold text-xs sm:text-sm transition-all hover:-translate-y-0.5 flex items-center gap-2 shadow-sm">
                        <i class="fa-brands fa-whatsapp text-base"></i>
                        <span class="hidden lg:inline">Info LowKer</span>
                    </a>
                </div>

                <!-- Trust Micro Features -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-8 border-t border-white/10">
                    <div>
                        <p class="text-2xl sm:text-3xl font-black text-amber-400">{{ $totalJobs }}</p>
                        <p class="text-xs text-slate-400 font-medium">Lowongan Aktif</p>
                    </div>
                    <div>
                        <p class="text-2xl sm:text-3xl font-black text-sky-400">{{ $totalCandidates }}+</p>
                        <p class="text-xs text-slate-400 font-medium">Pelamar Terdaftar</p>
                    </div>
                    <div>
                        <p class="text-2xl sm:text-3xl font-black text-emerald-400">{{ $totalPrinciples }}</p>
                        <p class="text-xs text-slate-400 font-medium">Unit Bisnis Mitra</p>
                    </div>
                    <div>
                        <p class="text-2xl sm:text-3xl font-black text-purple-400">{{ $distinctAreasCount }}+</p>
                        <p class="text-xs text-slate-400 font-medium">Area Penempatan</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 1.5 DEDICATED PORTAL GATEWAY CARDS SECTION -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-6 sm:-mt-10 relative z-20">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Card 1: Talent Pool / Rekrutmen -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xl hover:shadow-2xl hover:border-primary/50 transition-all duration-300 flex flex-col justify-between group relative overflow-hidden">
                <div class="absolute top-0 right-0 w-36 h-36 bg-blue-50/70 rounded-bl-full -z-0 pointer-events-none group-hover:scale-110 transition-transform"></div>
                
                <div class="space-y-4 relative z-10">
                    <div class="flex items-center justify-between">
                        <div class="w-12 h-12 rounded-2xl bg-blue-50 text-primary border border-blue-100 flex items-center justify-center text-xl group-hover:bg-primary group-hover:text-white transition-all shadow-sm">
                            <i class="fa-solid fa-users-viewfinder"></i>
                        </div>
                        <span class="px-3 py-1 rounded-full text-[10px] font-extrabold bg-blue-50 text-primary border border-blue-200 uppercase tracking-wider">
                            <i class="fa-solid fa-circle-check text-[9px] mr-1"></i>Portal Karyawan / HR
                        </span>
                    </div>

                    <div>
                        <h3 class="text-xl font-black text-slate-900 group-hover:text-primary transition-colors tracking-tight">
                            Talent Pool / Rekrutmen
                        </h3>
                        <p class="text-xs font-bold text-primary mt-1 flex items-center gap-1.5">
                            <span>Login Disini</span>
                            <i class="fa-solid fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                        </p>
                    </div>

                    <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                        Akses modul seleksi dan rekrutmen terpadu untuk Admin HR, Recruiter, dan User Prinsiple. Mengelola data pelamar, evaluasi AI, jadwal wawancara, tes psikotes online, dan persetujuan digital.
                    </p>
                </div>

                <div class="pt-5 mt-6 border-t border-slate-100 relative z-10">
                    <a href="{{ route('login') }}" class="w-full py-3.5 px-5 rounded-2xl bg-primary hover:bg-primary-700 text-white font-bold text-xs sm:text-sm shadow-md shadow-primary/20 hover:shadow-lg transition-all flex items-center justify-center gap-2">
                        <i class="fa-solid fa-arrow-right-to-bracket"></i>
                        <span>Talent Pool / Rekrutmen - Login Disini</span>
                    </a>
                </div>
            </div>

            <!-- Card 2: Fitur Lain - Akses ASystem V3 -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xl hover:shadow-2xl hover:border-indigo-400 transition-all duration-300 flex flex-col justify-between group relative overflow-hidden">
                <div class="absolute top-0 right-0 w-36 h-36 bg-indigo-50/70 rounded-bl-full -z-0 pointer-events-none group-hover:scale-110 transition-transform"></div>

                <div class="space-y-4 relative z-10">
                    <div class="flex items-center justify-between">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 border border-indigo-100 flex items-center justify-center text-xl group-hover:bg-indigo-600 group-hover:text-white transition-all shadow-sm">
                            <i class="fa-solid fa-layer-group"></i>
                        </div>
                        <span class="px-3 py-1 rounded-full text-[10px] font-extrabold bg-indigo-50 text-indigo-700 border border-indigo-200 uppercase tracking-wider">
                            <i class="fa-solid fa-up-right-from-square text-[9px] mr-1"></i>Sistem Pusat
                        </span>
                    </div>

                    <div>
                        <h3 class="text-xl font-black text-slate-900 group-hover:text-indigo-600 transition-colors tracking-tight">
                            Fitur Lain
                        </h3>
                        <p class="text-xs font-bold text-indigo-600 mt-1 flex items-center gap-1.5">
                            <span>Akses ASystem V3</span>
                            <i class="fa-solid fa-arrow-up-right-from-square text-[10px] group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform"></i>
                        </p>
                    </div>

                    <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                        Akses modul operasional lengkap ASystem V3 pada server pusat (<strong class="text-slate-700">asystem.co.id</strong>), mencakup administrasi historis, kontrol sistem data, dan layanan operasional lainnya.
                    </p>
                </div>

                <div class="pt-5 mt-6 border-t border-slate-100 relative z-10">
                    <a href="https://asystem.co.id/v3/login" target="_blank" class="w-full py-3.5 px-5 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs sm:text-sm shadow-md transition-all flex items-center justify-center gap-2">
                        <i class="fa-solid fa-arrow-up-right-from-square text-xs text-sky-400"></i>
                        <span>Fitur Lain - Akses ASystem V3</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. FEATURED JOBS SECTION -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 text-xs font-bold text-primary uppercase tracking-wider mb-1">
                    <i class="fa-solid fa-fire text-amber-500"></i>
                    <span>Lowongan Unggulan</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Peluang Karier Terbaru di ESA Groups</h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">Daftarkan diri Anda sekarang dan bergabunglah dengan ribuan talenta hebat kami.</p>
            </div>

            <a href="{{ route('job.public') }}" class="btn-att-secondary text-xs sm:text-sm shrink-0">
                <span>Lihat Semua Lowongan ({{ $totalJobs }})</span>
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        @if($featuredJobs->isEmpty())
            <div class="bg-white rounded-3xl border border-slate-200/80 p-12 text-center shadow-sm">
                <p class="text-sm text-slate-500">Belum ada lowongan baru saat ini.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($featuredJobs as $index => $item)
                    @php
                        $skills = $item->skills_array;
                        $colors = ['indigo', 'emerald', 'amber', 'purple', 'sky', 'rose'];
                        $theme = $colors[$index % count($colors)];
                    @endphp
                    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm hover:shadow-xl hover:border-primary/40 hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between group">
                        <div>
                            <!-- Header Row -->
                            <div class="flex items-start justify-between gap-3 mb-4">
                                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-primary border border-blue-100 flex items-center justify-center text-xl shrink-0 group-hover:scale-105 transition-transform">
                                    <i class="fa-solid fa-briefcase"></i>
                                </div>
                                <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600">
                                    <i class="fa-solid fa-location-dot text-rose-500 mr-1"></i>{{ $item->job_area ?? 'NASIONAL' }}
                                </span>
                            </div>

                            <!-- Title -->
                            <h3 class="text-base sm:text-lg font-black text-slate-900 group-hover:text-primary transition-colors line-clamp-1 mb-1">
                                <a href="{{ route('job.detail', $item->id) }}">{{ $item->job_title }}</a>
                            </h3>

                            <!-- Principle & City -->
                            <p class="text-xs text-slate-400 font-medium mb-3">
                                <i class="fa-solid fa-building text-slate-400 mr-1"></i>{{ $item->job_prinsiple ?? 'ESA Groups' }}
                                @if(!empty($item->city))
                                    &bull; <i class="fa-solid fa-city text-slate-400 mr-0.5"></i>{{ $item->city }}
                                @endif
                            </p>

                            <!-- Desc Snippet -->
                            <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed mb-4">
                                {{ $item->snippet_desc }}
                            </p>

                            <!-- Pelamar Counter -->
                            <div class="flex items-center gap-1.5 text-xs font-semibold text-primary mb-4">
                                <i class="fa-solid fa-users text-xs"></i>
                                <span>{{ $item->applicant_count > 0 ? $item->applicant_count . ' Pelamar' : 'Jadilah Pelamar Pertama!' }}</span>
                            </div>

                            <!-- Skills -->
                            <div class="flex flex-wrap gap-1.5 mb-6">
                                @if(count($skills) > 0)
                                    @foreach(array_slice($skills, 0, 3) as $sk)
                                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-semibold bg-slate-100 text-slate-700">
                                            {{ $sk }}
                                        </span>
                                    @endforeach
                                @else
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-semibold bg-emerald-50 text-emerald-700">
                                        Full-Time
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-3 mt-auto">
                            <a href="{{ route('job.detail', $item->id) }}" class="text-xs font-bold text-slate-600 hover:text-primary transition-colors flex items-center gap-1">
                                <span>Lihat Detail</span>
                                <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            </a>
                            <a href="{{ route('job.apply', $item->id) }}" class="px-4 py-2 rounded-xl bg-primary hover:bg-primary/90 text-white font-bold text-xs shadow-md shadow-primary/20 transition-all flex items-center gap-1.5">
                                <i class="fa-solid fa-paper-plane text-[10px]"></i>
                                <span>Lamar</span>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    <!-- 3. PLATFORM CAPABILITIES SECTION -->
    <section id="features" class="bg-white border-y border-slate-200/80 py-16 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            <div class="text-center max-w-2xl mx-auto space-y-3">
                <div class="inline-flex items-center gap-2 text-xs font-bold text-primary uppercase tracking-wider">
                    <i class="fa-solid fa-cubes text-primary"></i>
                    <span>Ekosistem ASystem</span>
                </div>
                <h2 class="text-2xl sm:text-4xl font-black text-slate-900 tracking-tight">Teknologi Cerdas untuk Pengelolaan SDM</h2>
                <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                    ASystem mengintegrasikan setiap tahapan siklus kerja mulai dari rekrutmen berbasis AI hingga penilaian performa kerja yang transparan.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                <!-- Feature 1 -->
                <div class="bg-slate-50 rounded-3xl p-7 border border-slate-200/60 hover:border-primary/40 hover:bg-white hover:shadow-xl transition-all duration-200 space-y-4 group">
                    <div class="w-14 h-14 rounded-2xl bg-blue-100 text-primary flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-brain"></i>
                    </div>
                    <h3 class="text-base sm:text-lg font-black text-slate-900">AI CV Screening & Profiling</h3>
                    <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                        Sistem AI cerdas membaca file CV, mengekstrak data diri pelamar secara otomatis, dan memberikan skor kecocokan profil secara instan.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-slate-50 rounded-3xl p-7 border border-slate-200/60 hover:border-emerald-400 hover:bg-white hover:shadow-xl transition-all duration-200 space-y-4 group">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                        <i class="fa-brands fa-whatsapp"></i>
                    </div>
                    <h3 class="text-base sm:text-lg font-black text-slate-900">WhatsApp Gateway Otomatis</h3>
                    <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                        Notifikasi status lamaran, undangan tes online, serta jadwal interview langsung terkirim secara otomatis ke WhatsApp pelamar.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-slate-50 rounded-3xl p-7 border border-slate-200/60 hover:border-purple-400 hover:bg-white hover:shadow-xl transition-all duration-200 space-y-4 group">
                    <div class="w-14 h-14 rounded-2xl bg-purple-100 text-purple-600 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-users-viewfinder"></i>
                    </div>
                    <h3 class="text-base sm:text-lg font-black text-slate-900">Portal Wawancara & Evaluasi</h3>
                    <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                        Pencatatan form wawancara terstandarisasi, tes kepribadian, tes matematika, serta referensi cek tersimpan dalam satu dokumen PDF siap unduh.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="bg-slate-50 rounded-3xl p-7 border border-slate-200/60 hover:border-amber-400 hover:bg-white hover:shadow-xl transition-all duration-200 space-y-4 group">
                    <div class="w-14 h-14 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-ranking-star"></i>
                    </div>
                    <h3 class="text-base sm:text-lg font-black text-slate-900">AI Ranking & Leaderboard</h3>
                    <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                        Leaderboard peringkat kandidat berbasis skor kecocokan AI (Tier Hijau ≥85%, Kuning, Merah) untuk membantu rekruter mengambil keputusan terbaik.
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="bg-slate-50 rounded-3xl p-7 border border-slate-200/60 hover:border-sky-400 hover:bg-white hover:shadow-xl transition-all duration-200 space-y-4 group">
                    <div class="w-14 h-14 rounded-2xl bg-sky-100 text-sky-600 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-stamp"></i>
                    </div>
                    <h3 class="text-base sm:text-lg font-black text-slate-900">Digital Principle Approval</h3>
                    <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                        Otorisasi dan persetujuan kandidat oleh mitra kerja/user prinsiple dilakukan secara online tanpa berkas fisik.
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="bg-slate-50 rounded-3xl p-7 border border-slate-200/60 hover:border-indigo-400 hover:bg-white hover:shadow-xl transition-all duration-200 space-y-4 group">
                    <div class="w-14 h-14 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <h3 class="text-base sm:text-lg font-black text-slate-900">Aman & Terpusat</h3>
                    <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                        Seluruh data pelamar dan master karyawan tersimpan rapi dengan hak akses berjenjang (Admin HR, Recruiter, dan User Prinsiple).
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. ALUR SELEKSI 4 TAHAP -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        <div class="text-center max-w-2xl mx-auto space-y-3">
            <div class="inline-flex items-center gap-2 text-xs font-bold text-primary uppercase tracking-wider">
                <i class="fa-solid fa-route text-primary"></i>
                <span>Proses Seleksi</span>
            </div>
            <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">4 Langkah Mudah Bergabung Bersama Kami</h2>
            <p class="text-xs sm:text-sm text-slate-500">Alur pendaftaran transparan dari awal hingga onboarding.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm relative space-y-3">
                <span class="w-10 h-10 rounded-2xl bg-blue-100 text-primary font-black text-base flex items-center justify-center">1</span>
                <h4 class="text-base font-bold text-slate-900">Isi Form Online</h4>
                <p class="text-xs text-slate-500 leading-relaxed">Pilih lowongan yang sesuai, isi biodata diri, dan unggah berkas CV Anda.</p>
            </div>

            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm relative space-y-3">
                <span class="w-10 h-10 rounded-2xl bg-blue-100 text-primary font-black text-base flex items-center justify-center">2</span>
                <h4 class="text-base font-bold text-slate-900">AI Screening</h4>
                <p class="text-xs text-slate-500 leading-relaxed">Sistem menganalisis kesesuaian profil dan mengarahkan ke tahapan tes.</p>
            </div>

            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm relative space-y-3">
                <span class="w-10 h-10 rounded-2xl bg-blue-100 text-primary font-black text-base flex items-center justify-center">3</span>
                <h4 class="text-base font-bold text-slate-900">Tes & Wawancara</h4>
                <p class="text-xs text-slate-500 leading-relaxed">Akses tes online melalui portal dan ikuti sesi wawancara bersama tim rekrutmen.</p>
            </div>

            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm relative space-y-3">
                <span class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-700 font-black text-base flex items-center justify-center">4</span>
                <h4 class="text-base font-bold text-slate-900">Onboarding Kerja</h4>
                <p class="text-xs text-slate-500 leading-relaxed">Kandidat terpilih menerima penawaran resmi dan mulai bergabung berkarya.</p>
            </div>
        </div>
    </section>

    <!-- 5. ABOUT SECTION -->
    <section id="about" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-blue-950 rounded-3xl text-white p-8 sm:p-14 shadow-xl border border-slate-800 flex flex-col lg:flex-row items-center justify-between gap-10">
            <div class="max-w-2xl space-y-4">
                <span class="text-xs font-bold text-sky-400 uppercase tracking-wider">Tentang Kami</span>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black text-white tracking-tight leading-tight">
                    Membangun Masa Depan Bersama Ekosistem ESA Groups
                </h2>
                <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                    ESA Groups adalah grup korporasi yang menaungi berbagai unit bisnis terkemuka di bidang distribusi, logistik, operasional, dan layanan profesional di seluruh Indonesia. ASystem hadir sebagai tulang punggung digital dalam memastikan pengelolaan SDM berjalan secara profesional, transparan, dan terdepan.
                </p>
                <div class="pt-2">
                    <a href="{{ route('job.public') }}" class="btn-att-primary text-xs">
                        <span>Lihat Lowongan Kerja Aktif</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="w-full lg:w-96 bg-white/10 backdrop-blur-md p-6 rounded-2xl border border-white/15 space-y-4 shrink-0">
                <h4 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-emerald-400"></i>
                    <span>Nilai Unggulan Kami</span>
                </h4>
                <ul class="text-xs text-slate-300 space-y-2.5">
                    <li class="flex items-start gap-2">
                        <i class="fa-solid fa-check text-sky-400 mt-0.5"></i>
                        <span>Integritas tinggi dalam seluruh proses seleksi.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fa-solid fa-check text-sky-400 mt-0.5"></i>
                        <span>Kesempatan pengembangan karier yang luas.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fa-solid fa-check text-sky-400 mt-0.5"></i>
                        <span>Lingkungan kerja kolaboratif dan adaptif.</span>
                    </li>
                </ul>
            </div>
        </div>
    </section>

    <!-- 6. CONTACT SECTION -->
    <section id="contact" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-3xl border border-slate-200/80 p-8 sm:p-12 shadow-sm space-y-8">
            <div class="max-w-xl space-y-2">
                <span class="text-xs font-bold text-primary uppercase tracking-wider">Kontak &amp; Bantuan</span>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Hubungi Tim Rekrutmen Kami</h2>
                <p class="text-xs sm:text-sm text-slate-500">Ada pertanyaan seputar lowongan pekerjaan atau kendala aplikasi? Tim kami siap membantu Anda.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-2">
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/60 space-y-2">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-primary flex items-center justify-center text-lg">
                        <i class="fa-solid fa-location-dot text-rose-500"></i>
                    </div>
                    <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Kantor Pusat</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">Jl. Rajawali No.18-20, Krembangan Selatan, Kec. Krembangan, Surabaya, Jawa Timur 60175</p>
                </div>

                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/60 space-y-2">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-lg">
                        <i class="fa-brands fa-whatsapp text-emerald-600"></i>
                    </div>
                    <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">WhatsApp Support</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">Hubungi admin rekrutmen via WhatsApp: <br><a href="https://wa.me/6283139797309" target="_blank" class="font-bold text-emerald-600 hover:underline">+62 831-3979-7309</a></p>
                </div>

                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/60 space-y-2">
                    <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-envelope text-purple-600"></i>
                    </div>
                    <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Email Layanan</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">Kirimkan berkas atau pertanyaan resmi ke: <br><strong class="text-slate-700">itsupport@arina.co.id</strong></p>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
