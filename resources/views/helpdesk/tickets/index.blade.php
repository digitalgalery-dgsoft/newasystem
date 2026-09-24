@extends('layouts.app')

@section('title', 'Daftar Antrean Tiket Helpdesk')

@section('content')
<div class="space-y-6">
    <!-- HEADER -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-1">
                <a href="{{ route('helpdesk.index') }}" class="hover:text-primary transition-colors">Helpdesk</a>
                <span>/</span>
                <span class="text-slate-800">
                    @if($isRegularUser)
                        Tiket Saya
                    @elseif($isDivisionUser)
                        Tiket Divisi
                    @else
                        Antrean Tiket
                    @endif
                </span>
            </div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight">
                @if($isRegularUser)
                    Tiket Yang Saya Ajukan
                @elseif($isDivisionUser)
                    Antrean & Tiket Divisi
                @else
                    Antrean & Riwayat Seluruh Tiket
                @endif
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                @if($isRegularUser)
                    Pantau status, penanganan, dan respon atas kendala yang Anda ajukan.
                @elseif($isDivisionUser)
                    Kelola dan respon tiket kendala yang ditujukan ke divisi Anda. Respon otomatis masuk ke Work Plan.
                @else
                    Kelola, respon, dan pantau status permohonan kendala antar divisi secara global.
                @endif
            </p>
        </div>
        <div class="flex items-center gap-2.5">
            @if($isAdmin)
            <a href="{{ route('helpdesk.kanban') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-bold shadow-xs transition-all">
                <i class="fa-solid fa-table-columns text-slate-500"></i>
                <span>Tampilan Kanban</span>
            </a>
            @endif
            <a href="{{ route('helpdesk.tickets.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary hover:bg-primary-600 text-white text-xs font-bold shadow-md shadow-primary/20 transition-all">
                <i class="fa-solid fa-plus"></i>
                <span>Buat Tiket Baru</span>
            </a>
        </div>
    </div>

    <!-- TABS NAVIGASI -->
    <div class="flex items-center gap-2 border-b border-slate-200 overflow-x-auto pb-1 text-xs font-bold">
        @if($isAdmin)
        <a href="{{ route('helpdesk.tickets.index', array_merge(request()->except('tab'), ['tab' => 'all'])) }}"
           class="px-4 py-2.5 rounded-t-xl transition-all flex items-center gap-2 whitespace-nowrap {{ $tab === 'all' ? 'border-b-2 border-primary text-primary bg-primary-50/50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
            <i class="fa-solid fa-inbox"></i>
            <span>Semua Tiket</span>
            <span class="px-1.5 py-0.2 rounded-md bg-slate-200 text-slate-700 text-[10px]">{{ $counts['all'] }}</span>
        </a>
        @endif

        @if($isAdmin || $isDivisionUser)
        <a href="{{ route('helpdesk.tickets.index', array_merge(request()->except('tab'), ['tab' => 'my_division'])) }}"
           class="px-4 py-2.5 rounded-t-xl transition-all flex items-center gap-2 whitespace-nowrap {{ $tab === 'my_division' ? 'border-b-2 border-emerald-600 text-emerald-700 bg-emerald-50/50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
            <i class="fa-solid fa-building-user"></i>
            <span>Tiket Divisi {{ $isDivisionUser ? 'Saya' : '' }}</span>
            <span class="px-1.5 py-0.2 rounded-md bg-emerald-100 text-emerald-700 text-[10px]">{{ $counts['my_division'] }}</span>
        </a>

        <a href="{{ route('helpdesk.tickets.index', array_merge(request()->except('tab'), ['tab' => 'assigned_to_me'])) }}"
           class="px-4 py-2.5 rounded-t-xl transition-all flex items-center gap-2 whitespace-nowrap {{ $tab === 'assigned_to_me' ? 'border-b-2 border-indigo-600 text-indigo-700 bg-indigo-50/50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
            <i class="fa-solid fa-list-check"></i>
            <span>Ditugaskan ke Saya</span>
            <span class="px-1.5 py-0.2 rounded-md bg-indigo-100 text-indigo-700 text-[10px]">{{ $counts['assigned_to_me'] }}</span>
        </a>
        @endif

        <a href="{{ route('helpdesk.tickets.index', array_merge(request()->except('tab'), ['tab' => 'my_tickets'])) }}"
           class="px-4 py-2.5 rounded-t-xl transition-all flex items-center gap-2 whitespace-nowrap {{ $tab === 'my_tickets' ? 'border-b-2 border-primary text-primary bg-primary-50/50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
            <i class="fa-regular fa-user"></i>
            <span>Tiket Diajukan Saya</span>
            <span class="px-1.5 py-0.2 rounded-md bg-slate-200 text-slate-700 text-[10px]">{{ $counts['my_tickets'] }}</span>
        </a>
    </div>

    <!-- FILTER BAR -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-xs">
        <form method="GET" action="{{ route('helpdesk.tickets.index') }}" class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-5 gap-3">
            <input type="hidden" name="tab" value="{{ $tab }}">

            <!-- PENCARIAN -->
            <div class="lg:col-span-2 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor tiket, judul, atau nama pengaju..."
                       class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-primary focus:bg-white transition-all">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                @if(request('search'))
                <a href="{{ route('helpdesk.tickets.index', request()->except('search')) }}" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 text-xs">
                    <i class="fa-solid fa-xmark"></i>
                </a>
                @endif
            </div>

            <!-- FILTER DIVISI -->
            @if($isAdmin || ($isDivisionUser && $divisions->count() > 1))
            <div>
                <select name="division_id" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-primary focus:bg-white">
                    <option value="">Semua Divisi {{ $isDivisionUser ? 'Saya' : '' }}</option>
                    @foreach($divisions as $d)
                    <option value="{{ $d->id }}" {{ request('division_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <!-- FILTER STATUS -->
            <div>
                <select name="status" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-primary focus:bg-white">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif (Belum Selesai)</option>
                    <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open (Menunggu Respon)</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress (Sedang Dikerjakan)</option>
                    <option value="answered" {{ request('status') === 'answered' ? 'selected' : '' }}>Answered (Dijawab)</option>
                    <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved (Selesai)</option>
                    <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed (Ditutup)</option>
                </select>
            </div>

            <!-- FILTER PRIORITAS & RESET -->
            <div class="flex items-center gap-2">
                <select name="priority" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-primary focus:bg-white">
                    <option value="">Semua Prioritas</option>
                    <option value="Urgent" {{ request('priority') === 'Urgent' ? 'selected' : '' }}>Urgent</option>
                    <option value="High" {{ request('priority') === 'High' ? 'selected' : '' }}>High</option>
                    <option value="Medium" {{ request('priority') === 'Medium' ? 'selected' : '' }}>Medium</option>
                    <option value="Low" {{ request('priority') === 'Low' ? 'selected' : '' }}>Low</option>
                </select>
                @if(request()->hasAny(['search', 'division_id', 'status', 'priority']))
                <a href="{{ route('helpdesk.tickets.index', ['tab' => $tab]) }}" class="px-2.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold transition-colors" title="Reset Filter">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- TABEL TIKET -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-4">Nomor & Prioritas</th>
                        <th class="py-3.5 px-4">Judul Kendala & Pengaju</th>
                        <th class="py-3.5 px-4">Divisi Tujuan</th>
                        <th class="py-3.5 px-4">Status & Work Plan</th>
                        <th class="py-3.5 px-4">Petugas (Assignee)</th>
                        <th class="py-3.5 px-4">SLA Deadline</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    @forelse($tickets as $t)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <!-- NOMOR & PRIORITAS -->
                        <td class="py-3.5 px-4 align-top">
                            <a href="{{ route('helpdesk.tickets.show', $t->id) }}" class="font-mono font-bold text-primary hover:underline block">
                                {{ $t->ticket_number }}
                            </a>
                            <span class="inline-block mt-1 px-2 py-0.5 rounded-full border {{ $t->priority_badge }} text-[9px] uppercase font-bold">
                                {{ $t->priority }}
                            </span>
                        </td>

                        <!-- JUDUL & PENGAJU -->
                        <td class="py-3.5 px-4 align-top max-w-xs">
                            <a href="{{ route('helpdesk.tickets.show', $t->id) }}" class="font-bold text-slate-800 hover:text-primary transition-colors line-clamp-1">
                                {{ $t->subject }}
                            </a>
                            <p class="text-[11px] text-slate-400 line-clamp-1 mt-0.5">{{ $t->description }}</p>
                            <div class="flex items-center gap-1.5 text-[10px] text-slate-500 mt-1">
                                <i class="fa-regular fa-user text-slate-400"></i>
                                <span>{{ $t->creator->name ?? 'User' }}</span>
                                <span>•</span>
                                <span>{{ $t->created_at->diffForHumans() }}</span>
                            </div>
                        </td>

                        <!-- DIVISI TUJUAN -->
                        <td class="py-3.5 px-4 align-top">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl border text-[11px] font-semibold {{ $t->division->color_badge ?? 'bg-slate-50 text-slate-700 border-slate-200' }}">
                                <i class="{{ $t->division->icon ?? 'fa-solid fa-headset' }} text-[10px]"></i>
                                <span>{{ $t->division->name ?? '-' }}</span>
                            </span>
                        </td>

                        <!-- STATUS & WORK PLAN -->
                        <td class="py-3.5 px-4 align-top space-y-1">
                            <span class="inline-block px-2.5 py-0.5 rounded-full border text-[10px] font-bold {{ $t->status_badge }}">
                                {{ $t->status_label }}
                            </span>
                            @if($t->workplan_task_id)
                            <a href="{{ route('workplan.index') }}?search={{ urlencode($t->ticket_number) }}" 
                               class="flex items-center gap-1 text-[10px] text-indigo-700 font-bold bg-indigo-50 border border-indigo-200 px-2 py-0.5 rounded-md hover:bg-indigo-100 transition-colors"
                               title="Tiket ini telah tersinkronisasi di Work Plan (Status: Progress)">
                                <i class="fa-solid fa-list-check text-[9px]"></i>
                                <span>Work Plan: In Progress</span>
                            </a>
                            @endif
                        </td>

                        <!-- ASSIGNEE -->
                        <td class="py-3.5 px-4 align-top">
                            @if($t->assignedAgent)
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-blue-100 text-blue-700 font-bold flex items-center justify-center text-[10px]">
                                    {{ strtoupper(substr($t->assignedAgent->name, 0, 1)) }}
                                </div>
                                <span class="font-semibold text-slate-800">{{ $t->assignedAgent->name }}</span>
                            </div>
                            @else
                                @if($t->canBeManagedBy($user))
                                <form method="POST" action="{{ route('helpdesk.tickets.claim', $t->id) }}">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 rounded-lg bg-blue-50 border border-blue-200 text-blue-700 hover:bg-blue-600 hover:text-white text-[11px] font-bold transition-all shadow-xs flex items-center gap-1" title="Ambil tiket ini dan otomatis masukkan ke Work Plan Anda">
                                        <i class="fa-solid fa-hand-holding-hand"></i>
                                        <span>Ambil Tiket</span>
                                    </button>
                                </form>
                                @else
                                <span class="text-slate-400 italic text-[11px]">Belum Ditangani</span>
                                @endif
                            @endif
                        </td>

                        <!-- SLA DEADLINE -->
                        <td class="py-3.5 px-4 align-top text-[11px]">
                            @if($t->due_date)
                            <span class="{{ $t->isOverdue() ? 'text-rose-600 font-bold flex items-center gap-1' : 'text-slate-600' }}">
                                @if($t->isOverdue())
                                <i class="fa-solid fa-triangle-exclamation text-rose-500"></i>
                                @endif
                                <span>{{ $t->due_date->format('d M Y H:i') }}</span>
                            </span>
                            @else
                            <span class="text-slate-400">-</span>
                            @endif
                        </td>

                        <!-- AKSI -->
                        <td class="py-3.5 px-4 align-top text-center whitespace-nowrap">
                            <div class="inline-flex items-center gap-1.5 justify-center">
                                <a href="{{ route('helpdesk.tickets.show', $t->id) }}" class="inline-flex items-center gap-1 px-3 py-1 rounded-xl bg-slate-100 hover:bg-primary hover:text-white text-slate-700 font-bold transition-all text-[11px]">
                                    <i class="fa-regular fa-eye"></i>
                                    <span>Detail</span>
                                </a>
                                @if($t->user_id === $user->id && $t->status !== 'closed')
                                <form method="POST" action="{{ route('helpdesk.tickets.close', $t->id) }}" onsubmit="return confirm('Apakah kendala pada tiket #{{ $t->ticket_number }} sudah terselesaikan dan Anda ingin menutupnya?')" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-emerald-50 hover:bg-emerald-600 hover:text-white text-emerald-700 border border-emerald-200 hover:border-emerald-600 font-bold transition-all text-[11px]" title="Tutup Tiket">
                                        <i class="fa-solid fa-circle-check"></i>
                                        <span class="hidden sm:inline">Tutup</span>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-400">
                            <i class="fa-regular fa-folder-open text-4xl mb-2 text-slate-300"></i>
                            <p class="font-bold text-sm text-slate-500">Tidak ada tiket yang sesuai dengan kriteria filter.</p>
                            <p class="text-xs text-slate-400 mt-1">Coba ubah filter status atau kata kunci pencarian Anda.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tickets->hasPages())
        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            {{ $tickets->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
