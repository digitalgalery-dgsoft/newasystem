@extends('layouts.app')

@section('title', 'Helpdesk Support & Ticketing')

@section('content')
<div class="space-y-6">
    <!-- HERO HEADER -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-blue-700 via-indigo-700 to-sky-600 p-7 text-white shadow-xl shadow-blue-900/20">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 backdrop-blur-md border border-white/20 text-xs font-semibold uppercase tracking-wider text-sky-200 mb-3">
                    <i class="fa-solid fa-headset"></i>
                    <span>Pusat Bantuan & Layanan Terpadu ASystem</span>
                </div>
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight">
                    @if($isRegularUser)
                        Pusat Bantuan & Pengajuan Kendala
                    @elseif($isDivisionUser)
                        Dashboard Tiket Divisi
                    @else
                        Helpdesk Support & Ticketing
                    @endif
                </h1>
                <p class="text-sm text-sky-100/90 mt-1.5 max-w-2xl leading-relaxed">
                    @if($isRegularUser)
                        Ajukan kendala atau permohonan ke divisi terkait dan pantau proses penanganannya secara transparan.
                    @elseif($isDivisionUser)
                        Pantau dan respon tiket kendala yang ditujukan ke divisi Anda. Respon akan otomatis tersinkronisasi ke <strong>Work Plan (Step Progress)</strong>.
                    @else
                        Ajukan kendala, keluhan, dan permohonan ke divisi terkait. Respon dari petugas akan otomatis tersinkronisasi ke <strong>Work Plan (Step Progress)</strong>.
                    @endif
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('helpdesk.tickets.create') }}" class="inline-flex items-center gap-2.5 px-5 py-3 rounded-2xl bg-white text-blue-800 hover:bg-sky-50 font-bold text-sm shadow-lg shadow-black/10 transition-all hover:scale-105 active:scale-95">
                    <i class="fa-solid fa-circle-plus text-base text-blue-600"></i>
                    <span>Buat Tiket Baru</span>
                </a>
                @if($isAdmin)
                <a href="{{ route('helpdesk.kanban') }}" class="inline-flex items-center gap-2 px-4 py-3 rounded-2xl bg-white/10 hover:bg-white/20 border border-white/25 text-white font-semibold text-sm backdrop-blur-md transition-all">
                    <i class="fa-solid fa-table-columns"></i>
                    <span>Kanban Helpdesk</span>
                </a>
                <a href="{{ route('helpdesk.divisions.index') }}" class="inline-flex items-center gap-2 px-4 py-3 rounded-2xl bg-white/10 hover:bg-white/20 border border-white/25 text-white font-semibold text-sm backdrop-blur-md transition-all">
                    <i class="fa-solid fa-sitemap"></i>
                    <span>Kelola Divisi</span>
                </a>
                @endif
            </div>
        </div>
        <!-- DECORATIVE ACCENTS -->
        <div class="absolute -right-10 -bottom-10 w-72 h-72 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute right-1/3 -top-12 w-60 h-60 bg-sky-400/20 rounded-full blur-2xl pointer-events-none"></div>
    </div>

    <!-- METRICS CARDS -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <!-- TOTAL TIKET -->
        <a href="{{ route('helpdesk.tickets.index') }}" class="group bg-white p-5 rounded-2xl border border-slate-200/90 shadow-sm hover:shadow-md transition-all hover:-translate-y-0.5">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">
                    @if($isRegularUser) Tiket Saya @elseif($isDivisionUser) Tiket Divisi @else Total Tiket @endif
                </span>
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-ticket text-base"></i>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-2xl md:text-3xl font-black text-slate-800">{{ number_format($totalTickets) }}</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-1">
                @if($isRegularUser) Diajukan oleh Anda @elseif($isDivisionUser) Ditujukan ke divisi Anda @else Seluruh tiket di sistem @endif
            </p>
        </a>

        <!-- BUTUH RESPON / OPEN -->
        <a href="{{ route('helpdesk.tickets.index', ['status' => 'open']) }}" class="group bg-white p-5 rounded-2xl border border-amber-200/90 shadow-sm hover:shadow-md transition-all hover:-translate-y-0.5 relative overflow-hidden">
            @if($openTickets > 0)
            <div class="absolute top-0 right-0 w-2.5 h-2.5 bg-amber-500 rounded-full animate-ping m-2"></div>
            @endif
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-amber-700 uppercase tracking-wider">Menunggu Respon</span>
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-clock text-base"></i>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-2xl md:text-3xl font-black text-amber-600">{{ number_format($openTickets) }}</span>
            </div>
            <p class="text-[11px] text-amber-700/80 mt-1">
                @if($isRegularUser) Belum direspon divisi @elseif($isDivisionUser) Perlu respon dari divisi @else Belum ditangani / open @endif
            </p>
        </a>

        <!-- SEDANG PROSES -->
        <a href="{{ route('helpdesk.tickets.index', ['status' => 'in_progress']) }}" class="group bg-white p-5 rounded-2xl border border-indigo-200/90 shadow-sm hover:shadow-md transition-all hover:-translate-y-0.5">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-indigo-700 uppercase tracking-wider">Sedang Diproses</span>
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-spinner text-base fa-spin-pulse"></i>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-2xl md:text-3xl font-black text-indigo-600">{{ number_format($inProgressTickets) }}</span>
            </div>
            <p class="text-[11px] text-indigo-700/80 mt-1">Aktif di Work Plan (Progress)</p>
        </a>

        <!-- SELESAI / CLOSED -->
        <a href="{{ route('helpdesk.tickets.index', ['status' => 'closed']) }}" class="group bg-white p-5 rounded-2xl border border-emerald-200/90 shadow-sm hover:shadow-md transition-all hover:-translate-y-0.5">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Selesai / Ditutup</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-circle-check text-base"></i>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-2xl md:text-3xl font-black text-emerald-600">{{ number_format($closedTickets) }}</span>
            </div>
            <p class="text-[11px] text-emerald-700/80 mt-1">Kendala terselesaikan</p>
        </a>
    </div>

    <!-- MAIN TWO-COLUMN CONTENT ACCORDING TO ROLE -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        @if($isRegularUser)
        {{-- =========================================================================
             1. TAMPILAN KHUSUS USER BIASA (Hanya Tiket Sendiri)
             ========================================================================= --}}
        <div class="lg:col-span-2 space-y-6">
            <!-- DAFTAR TIKET SAYA -->
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                            <i class="fa-solid fa-ticket"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-800">Daftar Tiket Kendala Saya</h2>
                            <p class="text-[11px] text-slate-400">Seluruh tiket permohonan yang telah Anda ajukan</p>
                        </div>
                    </div>
                    <a href="{{ route('helpdesk.tickets.create') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-primary text-white text-xs font-bold hover:bg-primary-600 transition-all shadow-xs">
                        <i class="fa-solid fa-plus"></i>
                        <span>Buat Tiket Baru</span>
                    </a>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($myTickets as $ticket)
                    <div class="p-4 hover:bg-slate-50/80 transition-colors flex flex-col md:flex-row md:items-center justify-between gap-3">
                        <div class="flex items-start gap-3 flex-1 min-w-0">
                            <div class="w-9 h-9 rounded-xl flex-shrink-0 flex items-center justify-center text-sm font-bold {{ $ticket->priority === 'Urgent' ? 'bg-rose-100 text-rose-700' : 'bg-blue-100 text-blue-700' }}">
                                <i class="{{ $ticket->priority === 'Urgent' ? 'fa-solid fa-triangle-exclamation' : 'fa-solid fa-ticket' }}"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-xs font-mono font-bold text-slate-500">{{ $ticket->ticket_number }}</span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border {{ $ticket->priority_badge }} uppercase">
                                        {{ $ticket->priority }}
                                    </span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border {{ $ticket->status_badge }}">
                                        {{ $ticket->status_label }}
                                    </span>
                                    @if($ticket->workplan_task_id)
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200 flex items-center gap-1">
                                        <i class="fa-solid fa-list-check"></i>
                                        <span>Diproses di Work Plan</span>
                                    </span>
                                    @endif
                                </div>
                                <h3 class="text-sm font-bold text-slate-800 truncate mt-1 hover:text-primary">
                                    <a href="{{ route('helpdesk.tickets.show', $ticket->id) }}">{{ $ticket->subject }}</a>
                                </h3>
                                <p class="text-xs text-slate-500 line-clamp-1 mt-0.5">{{ $ticket->description }}</p>
                                <div class="flex items-center gap-3 text-[11px] text-slate-400 mt-1 flex-wrap">
                                    <span><i class="fa-solid fa-layer-group mr-1 text-slate-400"></i>Divisi: <strong class="text-slate-600">{{ $ticket->division->name ?? '-' }}</strong></span>
                                    <span><i class="fa-regular fa-clock mr-1 text-slate-400"></i>{{ $ticket->created_at->diffForHumans() }}</span>
                                    @if($ticket->assignedAgent)
                                    <span><i class="fa-solid fa-user-check mr-1 text-emerald-500"></i>Petugas: <strong class="text-slate-700">{{ $ticket->assignedAgent->name }}</strong></span>
                                    @else
                                    <span class="text-amber-600 italic"><i class="fa-regular fa-hourglass-half mr-1"></i>Menunggu Respon Petugas</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0 self-end md:self-center">
                            <a href="{{ route('helpdesk.tickets.show', $ticket->id) }}" class="px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-primary hover:text-white text-slate-700 text-xs font-bold transition-all flex items-center gap-1.5">
                                <i class="fa-regular fa-comments"></i>
                                <span>Lihat Chat & Detail</span>
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="p-10 text-center text-slate-400">
                        <div class="w-16 h-16 rounded-3xl bg-blue-50 text-blue-500 flex items-center justify-center mx-auto mb-3 text-2xl">
                            <i class="fa-regular fa-folder-open"></i>
                        </div>
                        <h4 class="text-sm font-bold text-slate-700">Belum Ada Tiket yang Diajukan</h4>
                        <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">Jika Anda mengalami kendala teknis atau permohonan ke divisi lain, buat tiket baru sekarang.</p>
                        <a href="{{ route('helpdesk.tickets.create') }}" class="inline-flex items-center gap-2 px-4 py-2 mt-4 rounded-xl bg-primary text-white text-xs font-bold shadow-md shadow-primary/20 hover:bg-primary-600 transition-all">
                            <i class="fa-solid fa-plus"></i>
                            <span>Buat Tiket Kendala</span>
                        </a>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- PANDUAN PENGGUNAAN ALUR LAYANAN -->
            <div class="bg-gradient-to-br from-slate-50 to-blue-50/40 rounded-3xl border border-slate-200/90 p-6 space-y-4">
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-circle-info text-blue-600"></i>
                    <span>Alur & Mekanisme Penanganan Tiket</span>
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                    <div class="p-4 bg-white rounded-2xl border border-slate-200/80 space-y-1.5 shadow-2xs">
                        <div class="w-7 h-7 rounded-xl bg-blue-100 text-blue-700 font-black flex items-center justify-center text-xs">1</div>
                        <h4 class="font-bold text-slate-800">Ajukan Tiket Kendala</h4>
                        <p class="text-slate-500 text-[11px] leading-relaxed">Tuliskan kendala Anda dengan jelas dan pilih divisi tujuan serta prioritas yang sesuai.</p>
                    </div>
                    <div class="p-4 bg-white rounded-2xl border border-slate-200/80 space-y-1.5 shadow-2xs">
                        <div class="w-7 h-7 rounded-xl bg-indigo-100 text-indigo-700 font-black flex items-center justify-center text-xs">2</div>
                        <h4 class="font-bold text-slate-800">Otomasi Work Plan</h4>
                        <p class="text-slate-500 text-[11px] leading-relaxed">Saat agen divisi merespon tiket Anda, tiket otomatis masuk ke papan Kanban Work Plan petugas.</p>
                    </div>
                    <div class="p-4 bg-white rounded-2xl border border-slate-200/80 space-y-1.5 shadow-2xs">
                        <div class="w-7 h-7 rounded-xl bg-emerald-100 text-emerald-700 font-black flex items-center justify-center text-xs">3</div>
                        <h4 class="font-bold text-slate-800">Diskusi & Solusi</h4>
                        <p class="text-slate-500 text-[11px] leading-relaxed">Anda dapat berkomunikasi dua arah via ruang percakapan tiket hingga kendala terselesaikan.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT 1 COL: USER BIASA -->
        <div class="space-y-6">
            <!-- RIWAYAT AKTIVITAS SAYA -->
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-5">
                <h3 class="text-sm font-bold text-slate-800 pb-3 border-b border-slate-100 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-timeline text-blue-500"></i>
                    <span>Aktivitas Tiket Anda</span>
                </h3>

                <div class="space-y-3 text-xs">
                    @forelse($recentLogs as $log)
                    <div class="flex items-start gap-2.5">
                        <div class="w-6 h-6 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0 text-[10px] mt-0.5">
                            <i class="fa-solid fa-bolt"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-slate-700 leading-snug">
                                <strong class="text-slate-900">{{ $log->user->name ?? 'System' }}</strong>
                                <span class="text-slate-500">{{ $log->details }}</span>
                            </p>
                            <span class="text-[10px] text-slate-400">{{ $log->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-slate-400 text-center py-3">Belum ada aktivitas pada tiket Anda.</p>
                    @endforelse
                </div>
            </div>

            <!-- CARD TIPS PENGAJUAN -->
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-5 space-y-2">
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <i class="fa-regular fa-lightbulb text-amber-500"></i>
                    <span>Tips Penyelesaian Cepat</span>
                </h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Sertakan tangkapan layar (screenshot) atau berkas pendukung saat membuat tiket untuk mempercepat analisa petugas divisi terkait.
                </p>
            </div>
        </div>

        @elseif($isDivisionUser)
        {{-- =========================================================================
             2. TAMPILAN KHUSUS USER DIVISI (Hanya Tiket Ditujukan ke Divisinya)
             ========================================================================= --}}
        <div class="lg:col-span-2 space-y-6">
            <!-- ANTREAN TIKET DIVISI SAYA -->
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                            <i class="fa-solid fa-inbox"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-800">Antrean Tiket Masuk Divisi Anda</h2>
                            <p class="text-[11px] text-slate-400">Permohonan bantuan dan kendala yang dialamatkan ke divisi Anda</p>
                        </div>
                    </div>
                    <a href="{{ route('helpdesk.tickets.index') }}" class="text-xs font-semibold text-primary hover:underline flex items-center gap-1">
                        <span>Lihat Semua Tiket Divisi</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($urgentTickets as $ticket)
                    <div class="p-4 hover:bg-slate-50/80 transition-colors flex flex-col md:flex-row md:items-center justify-between gap-3">
                        <div class="flex items-start gap-3 flex-1 min-w-0">
                            <div class="w-9 h-9 rounded-xl flex-shrink-0 flex items-center justify-center text-sm font-bold {{ $ticket->priority === 'Urgent' ? 'bg-rose-100 text-rose-700' : 'bg-blue-100 text-blue-700' }}">
                                <i class="{{ $ticket->priority === 'Urgent' ? 'fa-solid fa-triangle-exclamation' : 'fa-solid fa-ticket' }}"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-xs font-mono font-bold text-slate-500">{{ $ticket->ticket_number }}</span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border {{ $ticket->priority_badge }} uppercase">
                                        {{ $ticket->priority }}
                                    </span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border {{ $ticket->status_badge }}">
                                        {{ $ticket->status_label }}
                                    </span>
                                    @if($ticket->workplan_task_id)
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200 flex items-center gap-1">
                                        <i class="fa-solid fa-list-check"></i>
                                        <span>Work Plan: In Progress</span>
                                    </span>
                                    @endif
                                </div>
                                <h3 class="text-sm font-bold text-slate-800 truncate mt-1 hover:text-primary">
                                    <a href="{{ route('helpdesk.tickets.show', $ticket->id) }}">{{ $ticket->subject }}</a>
                                </h3>
                                <p class="text-xs text-slate-500 line-clamp-1 mt-0.5">{{ $ticket->description }}</p>
                                <div class="flex items-center gap-3 text-[11px] text-slate-500 mt-1 flex-wrap">
                                    <span><i class="fa-regular fa-user mr-1 text-slate-400"></i>Pengaju: <strong class="text-slate-700">{{ $ticket->creator->name ?? 'User' }}</strong></span>
                                    <span><i class="fa-solid fa-layer-group mr-1 text-slate-400"></i>Divisi: {{ $ticket->division->name ?? '-' }}</span>
                                    <span><i class="fa-regular fa-clock mr-1 text-slate-400"></i>{{ $ticket->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0 self-end md:self-center">
                            @if(empty($ticket->assigned_to) && $ticket->canBeManagedBy($user))
                            <form method="POST" action="{{ route('helpdesk.tickets.claim', $ticket->id) }}">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-sm transition-all flex items-center gap-1.5" title="Ambil tiket dan masukkan otomatis ke Work Plan Anda">
                                    <i class="fa-solid fa-hand-holding-hand"></i>
                                    <span>Ambil Tiket</span>
                                </button>
                            </form>
                            @endif
                            <a href="{{ route('helpdesk.tickets.show', $ticket->id) }}" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-all">
                                Detail Tiket
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="p-8 text-center text-slate-400">
                        <i class="fa-solid fa-circle-check text-4xl text-emerald-400 mb-2"></i>
                        <p class="text-sm font-semibold">Tidak ada tiket aktif yang memerlukan penanganan saat ini.</p>
                        <p class="text-xs text-slate-400 mt-1">Seluruh tiket divisi Anda telah direspon atau diselesaikan dengan baik.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- STATISTIK KINERJA DIVISI SAYA -->
            @if($divisions->isNotEmpty())
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                            <i class="fa-solid fa-chart-pie"></i>
                        </div>
                        <h2 class="text-base font-bold text-slate-800">Statistik Divisi Anda</h2>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($divisions as $div)
                    <div class="p-4 rounded-2xl border border-slate-100 bg-slate-50/50 hover:bg-slate-50 transition-colors">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg {{ $div->color_badge }}">
                                    <i class="{{ $div->icon }}"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-800">{{ $div->name }}</h4>
                                    <p class="text-[11px] text-slate-500">Kode: <span class="font-mono font-bold">{{ $div->code }}</span> • Target SLA: {{ $div->sla_hours }} Jam</p>
                                </div>
                            </div>
                            <span class="text-xs font-extrabold px-2 py-0.5 rounded-full bg-slate-200/80 text-slate-700">
                                {{ $div->total_count }} Tiket
                            </span>
                        </div>
                        <div class="grid grid-cols-3 gap-2 mt-3 pt-3 border-t border-slate-200/60 text-center text-xs">
                            <div>
                                <span class="block font-black text-amber-600">{{ $div->open_count }}</span>
                                <span class="text-[10px] text-slate-400">Open</span>
                            </div>
                            <div>
                                <span class="block font-black text-indigo-600">{{ $div->progress_count }}</span>
                                <span class="text-[10px] text-slate-400">Progress</span>
                            </div>
                            <div>
                                <span class="block font-black text-emerald-600">{{ $div->closed_count }}</span>
                                <span class="text-[10px] text-slate-400">Done</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- RIGHT 1 COL: USER DIVISI -->
        <div class="space-y-6">
            <!-- TIKET DITUGASKAN KEPADA SAYA (ASSIGNED TO ME) -->
            @if($assignedToMeTickets->isNotEmpty())
            <div class="bg-white rounded-3xl border border-indigo-200/90 shadow-sm p-5">
                <div class="flex items-center justify-between pb-3 border-b border-indigo-100 mb-3">
                    <h3 class="text-sm font-bold text-indigo-900 flex items-center gap-2">
                        <i class="fa-solid fa-list-check text-indigo-600"></i>
                        <span>Ditugaskan ke Saya</span>
                    </h3>
                    <a href="{{ route('helpdesk.tickets.index', ['tab' => 'assigned_to_me']) }}" class="text-xs text-indigo-600 font-semibold hover:underline">
                        Semua
                    </a>
                </div>

                <div class="space-y-3">
                    @foreach($assignedToMeTickets as $assigned)
                    <a href="{{ route('helpdesk.tickets.show', $assigned->id) }}" class="block p-3 rounded-xl border border-indigo-100 bg-indigo-50/20 hover:bg-indigo-50/60 transition-all">
                        <div class="flex items-center justify-between text-[11px] mb-1">
                            <span class="font-mono font-bold text-indigo-700">{{ $assigned->ticket_number }}</span>
                            <span class="px-2 py-0.2 rounded-full border {{ $assigned->priority_badge }} text-[9px]">
                                {{ $assigned->priority }}
                            </span>
                        </div>
                        <h4 class="text-xs font-bold text-slate-800 line-clamp-1">{{ $assigned->subject }}</h4>
                        <div class="flex items-center justify-between text-[10px] text-slate-500 mt-1">
                            <span>Dari: {{ $assigned->creator->name ?? 'User' }}</span>
                            <span class="font-semibold {{ $assigned->isOverdue() ? 'text-rose-600 font-bold' : '' }}">
                                SLA: {{ $assigned->due_date ? $assigned->due_date->format('d M H:i') : '-' }}
                            </span>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- TIKET YANG DIAJUKAN OLEH SAYA SENDIRI (JIKA ADA) -->
            @if($myTickets->isNotEmpty())
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3">
                    <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                        <i class="fa-regular fa-user text-blue-500"></i>
                        <span>Tiket Diajukan Saya</span>
                    </h3>
                    <a href="{{ route('helpdesk.tickets.index', ['tab' => 'my_tickets']) }}" class="text-xs text-primary font-semibold hover:underline">
                        Semua
                    </a>
                </div>

                <div class="space-y-3">
                    @foreach($myTickets as $my)
                    <a href="{{ route('helpdesk.tickets.show', $my->id) }}" class="block p-3 rounded-xl border border-slate-100 hover:border-blue-200 hover:bg-blue-50/30 transition-all">
                        <div class="flex items-center justify-between text-[11px] text-slate-500 mb-1">
                            <span class="font-mono font-bold">{{ $my->ticket_number }}</span>
                            <span class="px-2 py-0.2 rounded-full border {{ $my->status_badge }} text-[9px] font-bold">
                                {{ $my->status_label }}
                            </span>
                        </div>
                        <h4 class="text-xs font-bold text-slate-800 line-clamp-1">{{ $my->subject }}</h4>
                        <div class="flex items-center justify-between text-[10px] text-slate-400 mt-1">
                            <span>Divisi: {{ $my->division->name ?? '-' }}</span>
                            <span>{{ $my->created_at->format('d M Y') }}</span>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- RIWAYAT AKTIVITAS TERBARU DIVISI -->
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-5">
                <h3 class="text-sm font-bold text-slate-800 pb-3 border-b border-slate-100 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-timeline text-slate-400"></i>
                    <span>Aktivitas Terkini Divisi</span>
                </h3>

                <div class="space-y-3 text-xs">
                    @forelse($recentLogs as $log)
                    <div class="flex items-start gap-2.5">
                        <div class="w-6 h-6 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center flex-shrink-0 text-[10px] mt-0.5">
                            <i class="fa-solid fa-bolt"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-slate-700 leading-snug">
                                <strong class="text-slate-900">{{ $log->user->name ?? 'System' }}</strong>
                                <span class="text-slate-500">{{ $log->details }}</span>
                            </p>
                            <span class="text-[10px] text-slate-400">{{ $log->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-slate-400 text-center py-3">Belum ada riwayat aktivitas.</p>
                    @endforelse
                </div>
            </div>
        </div>

        @else
        {{-- =========================================================================
             3. TAMPILAN LENGKAP KHUSUS ADMINISTRATOR
             ========================================================================= --}}
        <div class="lg:col-span-2 space-y-6">
            <!-- ANTREAN TIKET MEMERLUKAN TINDAKAN (GLOBAL) -->
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center font-bold">
                            <i class="fa-solid fa-fire-flame-curved"></i>
                        </div>
                        <h2 class="text-base font-bold text-slate-800">Antrean Tiket Perlu Respon (Sistem Global)</h2>
                    </div>
                    <a href="{{ route('helpdesk.tickets.index', ['status' => 'active']) }}" class="text-xs font-semibold text-primary hover:underline flex items-center gap-1">
                        <span>Lihat Semua</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($urgentTickets as $ticket)
                    <div class="p-4 hover:bg-slate-50/80 transition-colors flex flex-col md:flex-row md:items-center justify-between gap-3">
                        <div class="flex items-start gap-3 flex-1 min-w-0">
                            <div class="w-9 h-9 rounded-xl flex-shrink-0 flex items-center justify-center text-sm font-bold {{ $ticket->priority === 'Urgent' ? 'bg-rose-100 text-rose-700' : 'bg-blue-100 text-blue-700' }}">
                                <i class="{{ $ticket->priority === 'Urgent' ? 'fa-solid fa-triangle-exclamation' : 'fa-solid fa-ticket' }}"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-xs font-mono font-bold text-slate-500">{{ $ticket->ticket_number }}</span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border {{ $ticket->priority_badge }} uppercase">
                                        {{ $ticket->priority }}
                                    </span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border {{ $ticket->status_badge }}">
                                        {{ $ticket->status_label }}
                                    </span>
                                    @if($ticket->workplan_task_id)
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200 flex items-center gap-1">
                                        <i class="fa-solid fa-list-check"></i>
                                        <span>Work Plan: In Progress</span>
                                    </span>
                                    @endif
                                </div>
                                <h3 class="text-sm font-bold text-slate-800 truncate mt-1 hover:text-primary">
                                    <a href="{{ route('helpdesk.tickets.show', $ticket->id) }}">{{ $ticket->subject }}</a>
                                </h3>
                                <div class="flex items-center gap-3 text-[11px] text-slate-500 mt-1 flex-wrap">
                                    <span><i class="fa-regular fa-user mr-1 text-slate-400"></i>{{ $ticket->creator->name ?? 'User' }}</span>
                                    <span><i class="fa-solid fa-layer-group mr-1 text-slate-400"></i>{{ $ticket->division->name ?? '-' }}</span>
                                    <span><i class="fa-regular fa-clock mr-1 text-slate-400"></i>{{ $ticket->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0 self-end md:self-center">
                            @if(empty($ticket->assigned_to) && $ticket->canBeManagedBy($user))
                            <form method="POST" action="{{ route('helpdesk.tickets.claim', $ticket->id) }}">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-sm transition-all flex items-center gap-1.5" title="Ambil tiket dan masukkan otomatis ke Work Plan Anda">
                                    <i class="fa-solid fa-hand-holding-hand"></i>
                                    <span>Ambil Tiket</span>
                                </button>
                            </form>
                            @endif
                            <a href="{{ route('helpdesk.tickets.show', $ticket->id) }}" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-all">
                                Detail Tiket
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="p-8 text-center text-slate-400">
                        <i class="fa-solid fa-circle-check text-4xl text-emerald-400 mb-2"></i>
                        <p class="text-sm font-semibold">Tidak ada antrean tiket mendesak saat ini.</p>
                        <p class="text-xs text-slate-400 mt-1">Semua tiket telah ditangani atau terselesaikan dengan baik.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- DISTRIBUSI SELURUH DIVISI LAYANAN -->
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                            <i class="fa-solid fa-sitemap"></i>
                        </div>
                        <h2 class="text-base font-bold text-slate-800">Distribusi Seluruh Divisi Layanan</h2>
                    </div>
                    <a href="{{ route('helpdesk.divisions.index') }}" class="text-xs font-semibold text-primary hover:underline">
                        Kelola Agen & Divisi
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($divisions as $div)
                    <div class="p-4 rounded-2xl border border-slate-100 bg-slate-50/50 hover:bg-slate-50 transition-colors">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg {{ $div->color_badge }}">
                                    <i class="{{ $div->icon }}"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-800">{{ $div->name }}</h4>
                                    <p class="text-[11px] text-slate-500">Kode: <span class="font-mono font-bold">{{ $div->code }}</span> • SLA: {{ $div->sla_hours }} Jam</p>
                                </div>
                            </div>
                            <span class="text-xs font-extrabold px-2 py-0.5 rounded-full bg-slate-200/80 text-slate-700">
                                {{ $div->total_count }} Tiket
                            </span>
                        </div>
                        <div class="grid grid-cols-3 gap-2 mt-3 pt-3 border-t border-slate-200/60 text-center text-xs">
                            <div>
                                <span class="block font-black text-amber-600">{{ $div->open_count }}</span>
                                <span class="text-[10px] text-slate-400">Open</span>
                            </div>
                            <div>
                                <span class="block font-black text-indigo-600">{{ $div->progress_count }}</span>
                                <span class="text-[10px] text-slate-400">Progress</span>
                            </div>
                            <div>
                                <span class="block font-black text-emerald-600">{{ $div->closed_count }}</span>
                                <span class="text-[10px] text-slate-400">Done</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- RIGHT 1 COL: ADMIN -->
        <div class="space-y-6">
            <!-- TIKET SAYA -->
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3">
                    <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                        <i class="fa-regular fa-user text-blue-500"></i>
                        <span>Tiket Diajukan Saya</span>
                    </h3>
                    <a href="{{ route('helpdesk.tickets.index', ['tab' => 'my_tickets']) }}" class="text-xs text-primary font-semibold hover:underline">
                        Semua
                    </a>
                </div>

                <div class="space-y-3">
                    @forelse($myTickets as $my)
                    <a href="{{ route('helpdesk.tickets.show', $my->id) }}" class="block p-3 rounded-xl border border-slate-100 hover:border-blue-200 hover:bg-blue-50/30 transition-all">
                        <div class="flex items-center justify-between text-[11px] text-slate-500 mb-1">
                            <span class="font-mono font-bold">{{ $my->ticket_number }}</span>
                            <span class="px-2 py-0.2 rounded-full border {{ $my->status_badge }} text-[9px] font-bold">
                                {{ $my->status_label }}
                            </span>
                        </div>
                        <h4 class="text-xs font-bold text-slate-800 line-clamp-1">{{ $my->subject }}</h4>
                        <div class="flex items-center justify-between text-[10px] text-slate-400 mt-1">
                            <span>Divisi: {{ $my->division->name ?? '-' }}</span>
                            <span>{{ $my->created_at->format('d M Y') }}</span>
                        </div>
                    </a>
                    @empty
                    <div class="text-center py-6 text-slate-400">
                        <i class="fa-regular fa-folder-open text-3xl mb-1.5"></i>
                        <p class="text-xs">Belum ada tiket yang Anda ajukan.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- TIKET DITUGASKAN KEPADA SAYA -->
            @if($assignedToMeTickets->isNotEmpty())
            <div class="bg-white rounded-3xl border border-indigo-200/90 shadow-sm p-5">
                <div class="flex items-center justify-between pb-3 border-b border-indigo-100 mb-3">
                    <h3 class="text-sm font-bold text-indigo-900 flex items-center gap-2">
                        <i class="fa-solid fa-list-check text-indigo-600"></i>
                        <span>Ditugaskan ke Saya</span>
                    </h3>
                    <a href="{{ route('helpdesk.tickets.index', ['tab' => 'assigned_to_me']) }}" class="text-xs text-indigo-600 font-semibold hover:underline">
                        Semua
                    </a>
                </div>

                <div class="space-y-3">
                    @foreach($assignedToMeTickets as $assigned)
                    <a href="{{ route('helpdesk.tickets.show', $assigned->id) }}" class="block p-3 rounded-xl border border-indigo-100 bg-indigo-50/20 hover:bg-indigo-50/60 transition-all">
                        <div class="flex items-center justify-between text-[11px] mb-1">
                            <span class="font-mono font-bold text-indigo-700">{{ $assigned->ticket_number }}</span>
                            <span class="px-2 py-0.2 rounded-full border {{ $assigned->priority_badge }} text-[9px]">
                                {{ $assigned->priority }}
                            </span>
                        </div>
                        <h4 class="text-xs font-bold text-slate-800 line-clamp-1">{{ $assigned->subject }}</h4>
                        <div class="flex items-center justify-between text-[10px] text-slate-500 mt-1">
                            <span>Dari: {{ $assigned->creator->name ?? 'User' }}</span>
                            <span class="font-semibold {{ $assigned->isOverdue() ? 'text-rose-600 font-bold' : '' }}">
                                SLA: {{ $assigned->due_date ? $assigned->due_date->format('d M H:i') : '-' }}
                            </span>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- RIWAYAT AKTIVITAS TERBARU (GLOBAL) -->
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-5">
                <h3 class="text-sm font-bold text-slate-800 pb-3 border-b border-slate-100 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-timeline text-slate-400"></i>
                    <span>Riwayat Aktivitas Sistem</span>
                </h3>

                <div class="space-y-3 text-xs">
                    @forelse($recentLogs as $log)
                    <div class="flex items-start gap-2.5">
                        <div class="w-6 h-6 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center flex-shrink-0 text-[10px] mt-0.5">
                            <i class="fa-solid fa-bolt"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-slate-700 leading-snug">
                                <strong class="text-slate-900">{{ $log->user->name ?? 'System' }}</strong>
                                <span class="text-slate-500">{{ $log->details }}</span>
                            </p>
                            <span class="text-[10px] text-slate-400">{{ $log->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-slate-400 text-center py-3">Belum ada riwayat aktivitas.</p>
                    @endforelse
                </div>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection
