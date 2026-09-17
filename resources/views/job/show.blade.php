@extends('layouts.app')

@section('title', $job->job_title . ' - Detail Lowongan - ASystem Support System')

@section('content')
<div class="space-y-6">
    <!-- TOP NAV / BREADCRUMB -->
    <div class="flex items-center justify-between">
        <a href="{{ route('job.public') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white border border-slate-200/80 text-xs font-bold text-slate-700 hover:text-primary hover:border-primary/40 shadow-sm transition-all">
            <i class="fa-solid fa-arrow-left text-slate-400"></i>
            <span>Kembali ke Daftar Lowongan</span>
        </a>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Lowongan Dibuka</span>
            </span>
        </div>
    </div>

    <!-- HERO HEADER CARD -->
    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-sm relative overflow-hidden">
        <div class="absolute -right-8 -top-8 w-48 h-48 bg-blue-50/60 rounded-full blur-2xl pointer-events-none"></div>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
            <div class="flex items-start sm:items-center gap-4">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white flex items-center justify-center text-3xl shrink-0 shadow-lg shadow-blue-500/20">
                    <i class="fa-solid fa-briefcase"></i>
                </div>
                <div class="space-y-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-50 text-primary border border-blue-100">
                            {{ $job->job_prinsiple ?? 'ESA Groups' }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600">
                            <i class="fa-solid fa-location-dot text-rose-500 mr-1"></i>{{ $job->job_area ?? 'NASIONAL' }}
                        </span>
                        @if(!empty($job->city))
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600">
                                <i class="fa-solid fa-city text-slate-400 mr-1"></i>{{ $job->city }}
                            </span>
                        @endif
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-800 tracking-tight leading-snug">
                        {{ $job->job_title }}
                    </h1>
                    <div class="flex flex-wrap items-center gap-4 text-xs text-slate-400">
                        <span class="flex items-center gap-1.5">
                            <i class="fa-regular fa-calendar text-slate-400"></i>
                            <span>Diposting: {{ $job->created_at ? $job->created_at->translatedFormat('d F Y') : '-' }}</span>
                        </span>
                        <span class="flex items-center gap-1.5 text-primary font-semibold">
                            <i class="fa-solid fa-users text-xs"></i>
                            <span>{{ $totalApplicants > 0 ? $totalApplicants . ' Pelamar Terdaftar' : 'Jadilah Pelamar Pertama!' }}</span>
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap sm:flex-nowrap items-center gap-3 shrink-0">
                <button type="button" onclick="shareJob()" class="btn-att-secondary text-xs">
                    <i class="fa-solid fa-share-nodes text-slate-400"></i>
                    <span>Bagikan</span>
                </button>
                <a href="{{ route('job.apply', $job->id) }}" class="px-6 py-3 rounded-xl bg-primary hover:bg-primary/90 text-white font-bold text-xs sm:text-sm shadow-md shadow-primary/20 hover:shadow-lg transition-all flex items-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span>Lamar Sekarang</span>
                </a>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT TWO COLUMNS -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- LEFT COLUMN: DETAILS -->
        <div class="lg:col-span-8 space-y-6">
            <!-- 1. Deskripsi Pekerjaan -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-primary flex items-center justify-center text-sm">
                        <i class="fa-solid fa-file-lines"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-800">Deskripsi Pekerjaan</h2>
                        <p class="text-[11px] text-slate-400">Tugas utama, tanggung jawab, dan ruang lingkup posisi ini.</p>
                    </div>
                </div>

                <div class="text-xs sm:text-sm text-slate-600 leading-relaxed space-y-3 prose prose-sm max-w-none">
                    @if(!empty($job->job_desc))
                        {!! nl2br(e($job->job_desc)) !!}
                    @else
                        <p class="text-slate-400 italic">Deskripsi lengkap pekerjaan dapat dikonfirmasi saat proses interview berlangsung.</p>
                    @endif
                </div>
            </div>

            <!-- 2. Kualifikasi & Persyaratan -->
            @if(!empty($job->job_quals) || !empty($job->kualifikasi))
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-800">Kualifikasi & Persyaratan</h2>
                        <p class="text-[11px] text-slate-400">Latar belakang pendidikan dan kriteria kandidat yang dicari.</p>
                    </div>
                </div>

                <div class="text-xs sm:text-sm text-slate-600 leading-relaxed space-y-3 prose prose-sm max-w-none">
                    {!! nl2br(e($job->job_quals ?? $job->kualifikasi)) !!}
                </div>
            </div>
            @endif

            <!-- 3. Keterampilan yang Dibutuhkan -->
            @php
                $skills = array_filter(array_map('trim', explode(',', strip_tags($job->job_skills ?? ''))));
            @endphp
            @if(count($skills) > 0)
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-800">Keterampilan Utama (Required Skills)</h2>
                        <p class="text-[11px] text-slate-400">Kemampuan yang dinilai tinggi oleh AI Smart Matcher kami.</p>
                    </div>
                </div>

                <div class="flex flex-wrap gap-2 pt-1">
                    @foreach($skills as $sk)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-blue-50 text-primary border border-blue-100/80">
                            <i class="fa-solid fa-check text-[10px] text-emerald-500"></i>
                            <span>{{ $sk }}</span>
                        </span>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- 4. Bagikan Lowongan -->
            <div class="bg-gradient-to-r from-slate-50 to-blue-50/50 rounded-2xl border border-slate-200/80 p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h3 class="text-xs font-bold text-slate-800 mb-0.5">Kenal orang yang cocok dengan lowongan ini?</h3>
                    <p class="text-[11px] text-slate-500">Bagikan peluang karier ini kepada rekan, keluarga, atau jejaring profesional Anda.</p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <a href="https://api.whatsapp.com/send?text=Lowongan%20Kerja%20{{ urlencode($job->job_title) }}%20di%20ESA%20Groups.%20Daftar%20sekarang:%20{{ urlencode(request()->fullUrl()) }}" target="_blank" class="w-9 h-9 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-sm hover:bg-emerald-600 transition-colors shadow-sm" title="Bagikan ke WhatsApp">
                        <i class="fa-brands fa-whatsapp"></i>
                    </a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->fullUrl()) }}" target="_blank" class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center text-sm hover:bg-blue-700 transition-colors shadow-sm" title="Bagikan ke LinkedIn">
                        <i class="fa-brands fa-linkedin-in"></i>
                    </a>
                    <button type="button" onclick="copyLink()" class="px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 text-xs font-bold hover:bg-slate-50 transition-colors shadow-sm flex items-center gap-1.5">
                        <i class="fa-regular fa-copy text-slate-400"></i>
                        <span>Salin Link</span>
                    </button>
                </div>
            </div>

            <!-- 5. Rekomendasi Lowongan Lainnya -->
            @if($otherJobs->isNotEmpty())
            <div class="space-y-3 pt-2">
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-compass text-primary"></i>
                    <span>Lowongan Sejenis Lainnya</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    @foreach($otherJobs as $oj)
                        <a href="{{ route('job.detail', $oj->id) }}" class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm hover:border-primary/40 hover:shadow-md transition-all group block">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">
                                {{ $oj->job_area ?? 'NASIONAL' }}
                            </span>
                            <h4 class="text-xs font-black text-slate-800 group-hover:text-primary transition-colors line-clamp-1 mb-2">
                                {{ $oj->job_title }}
                            </h4>
                            <span class="text-[11px] font-bold text-primary flex items-center gap-1">
                                <span>Lihat Detail</span>
                                <i class="fa-solid fa-arrow-right text-[9px] group-hover:translate-x-1 transition-transform"></i>
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- RIGHT COLUMN: STICKY SIDEBAR OVERVIEW & GUIDE -->
        <div class="lg:col-span-4 space-y-6 lg:sticky lg:top-6">
            <!-- Job Overview Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Ringkasan Posisi</h3>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-primary">Overview</span>
                </div>

                <div class="space-y-3.5 text-xs">
                    <!-- Lokasi Area -->
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-slate-50 text-slate-500 border border-slate-200/60 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-location-dot text-rose-500"></i>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Lokasi & Area</span>
                            <span class="font-semibold text-slate-800">
                                {{ $job->job_area ?? 'Nasional' }}
                                @if(!empty($job->city))
                                    ({{ $job->city }})
                                @endif
                            </span>
                        </div>
                    </div>

                    <!-- Pengalaman Kerja -->
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-slate-50 text-slate-500 border border-slate-200/60 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-business-time text-primary"></i>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Pengalaman</span>
                            <span class="font-semibold text-slate-800">{{ strip_tags($job->job_exp ?? 'Fresh Graduate / Pengalaman Relevan') }}</span>
                        </div>
                    </div>

                    <!-- Prinsiple -->
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-slate-50 text-slate-500 border border-slate-200/60 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-building text-amber-500"></i>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Unit Bisnis / Mitra</span>
                            <span class="font-semibold text-slate-800">{{ $job->job_prinsiple ?? 'ESA Groups' }}</span>
                        </div>
                    </div>

                    <!-- Tanggal Posting -->
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-slate-50 text-slate-500 border border-slate-200/60 flex items-center justify-center shrink-0">
                            <i class="fa-regular fa-calendar-check text-emerald-500"></i>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Tanggal Tayang</span>
                            <span class="font-semibold text-slate-800">{{ $job->created_at ? $job->created_at->translatedFormat('d F Y') : '-' }}</span>
                        </div>
                    </div>

                    <!-- Contact PIC -->
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-slate-50 text-slate-500 border border-slate-200/60 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-headset text-purple-500"></i>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">PIC Rekrutmen</span>
                            <span class="font-semibold text-slate-800 block">{{ $job->created_by ?? 'Tim Rekrutmen ESA' }}</span>
                            <a href="https://wa.me/6283139797309?text=Halo%20Admin%20Rekrutmen,%20saya%20ingin%20bertanya%20mengenai%20posisi%20{{ urlencode($job->job_title) }}" target="_blank" class="text-[11px] font-semibold text-emerald-600 hover:underline inline-flex items-center gap-1 mt-0.5">
                                <i class="fa-brands fa-whatsapp"></i> Chat WhatsApp PIC
                            </a>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100">
                    <a href="{{ route('job.apply', $job->id) }}" class="w-full py-3 rounded-xl bg-primary hover:bg-primary/90 text-white font-bold text-xs sm:text-sm shadow-md shadow-primary/20 hover:shadow-lg transition-all flex items-center justify-center gap-2">
                        <i class="fa-solid fa-paper-plane"></i>
                        <span>Lamar Posisi Ini</span>
                    </a>
                    <p class="text-[10px] text-slate-400 text-center mt-2">
                        Proses pendaftaran gratis dan tidak dipungut biaya apapun.
                    </p>
                </div>
            </div>

            <!-- Tahapan Seleksi Guide Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider pb-2 border-b border-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-route text-primary"></i>
                    <span>Alur Tahapan Seleksi</span>
                </h3>

                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-blue-100 text-primary font-black text-xs flex items-center justify-center shrink-0">
                            1
                        </div>
                        <div class="text-xs">
                            <span class="font-bold text-slate-800 block">Kirim Berkas Lamaran</span>
                            <p class="text-slate-500 text-[11px]">Isi data diri dan unggah CV lengkap melalui formulir online.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-blue-100 text-primary font-black text-xs flex items-center justify-center shrink-0">
                            2
                        </div>
                        <div class="text-xs">
                            <span class="font-bold text-slate-800 block">AI Screening & Profiling</span>
                            <p class="text-slate-500 text-[11px]">Sistem AI secara otomatis mengevaluasi kecocokan profil Anda.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-blue-100 text-primary font-black text-xs flex items-center justify-center shrink-0">
                            3
                        </div>
                        <div class="text-xs">
                            <span class="font-bold text-slate-800 block">Tes Online & Wawancara</span>
                            <p class="text-slate-500 text-[11px]">Pelamar lolos diundang mengikuti tes psikotes & interview.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 font-black text-xs flex items-center justify-center shrink-0">
                            4
                        </div>
                        <div class="text-xs">
                            <span class="font-bold text-slate-800 block">Penawaran Kerja & Onboarding</span>
                            <p class="text-slate-500 text-[11px]">Kandidat terpilih bergabung bersama tim ESA Groups.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyLink() {
    navigator.clipboard.writeText(window.location.href).then(() => {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Tautan berhasil disalin!',
            showConfirmButton: false,
            timer: 2000
        });
    });
}

function shareJob() {
    if (navigator.share) {
        navigator.share({
            title: 'Lowongan: {{ $job->job_title }}',
            text: 'Informasi lowongan kerja {{ $job->job_title }} di ESA Groups.',
            url: window.location.href,
        }).catch(() => {});
    } else {
        copyLink();
    }
}
</script>
@endsection
