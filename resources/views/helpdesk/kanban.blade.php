@extends('layouts.app')

@section('title', 'Kanban Board Helpdesk')

@section('content')
<div class="space-y-6">
    <!-- HEADER -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-1">
                <a href="{{ route('helpdesk.index') }}" class="hover:text-primary transition-colors">Helpdesk</a>
                <span>/</span>
                <span class="text-slate-800">Kanban Board</span>
            </div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight">Papan Kanban Tiket Helpdesk</h1>
            <p class="text-xs text-slate-500 mt-0.5">Visualisasi alur pengerjaan dan status tiket antar divisi secara real-time.</p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('helpdesk.tickets.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-bold shadow-xs transition-all">
                <i class="fa-solid fa-list text-slate-500"></i>
                <span>Tampilan Daftar</span>
            </a>
            <a href="{{ route('helpdesk.tickets.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary hover:bg-primary-600 text-white text-xs font-bold shadow-md shadow-primary/20 transition-all">
                <i class="fa-solid fa-plus"></i>
                <span>Buat Tiket Baru</span>
            </a>
        </div>
    </div>

    <!-- FILTER BAR -->
    <div class="bg-white p-3.5 rounded-2xl border border-slate-200/90 shadow-xs flex items-center justify-between gap-3 flex-wrap">
        <form method="GET" action="{{ route('helpdesk.kanban') }}" class="flex items-center gap-3 flex-wrap">
            <select name="division_id" onchange="this.form.submit()" class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-primary">
                <option value="">Semua Divisi</option>
                @foreach($divisions as $d)
                <option value="{{ $d->id }}" {{ request('division_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                @endforeach
            </select>

            <select name="priority" onchange="this.form.submit()" class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-primary">
                <option value="">Semua Prioritas</option>
                <option value="Urgent" {{ request('priority') === 'Urgent' ? 'selected' : '' }}>Urgent</option>
                <option value="High" {{ request('priority') === 'High' ? 'selected' : '' }}>High</option>
                <option value="Medium" {{ request('priority') === 'Medium' ? 'selected' : '' }}>Medium</option>
                <option value="Low" {{ request('priority') === 'Low' ? 'selected' : '' }}>Low</option>
            </select>

            @if(request()->hasAny(['division_id', 'priority']))
            <a href="{{ route('helpdesk.kanban') }}" class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition-colors">
                <i class="fa-solid fa-rotate-left mr-1"></i>Reset
            </a>
            @endif
        </form>

        <span class="text-xs text-slate-400 font-semibold">
            Total {{ $openTickets->count() + $inProgressTickets->count() + $answeredTickets->count() + $closedTickets->count() }} Tiket
        </span>
    </div>

    <!-- 4 KANBAN COLUMNS -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-start">
        <!-- 1. OPEN (MENUNGGU RESPON) -->
        <div class="bg-slate-100/80 rounded-3xl p-3.5 border border-slate-200/80 space-y-3">
            <div class="flex items-center justify-between px-2">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                    <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider">Menunggu Respon</h3>
                </div>
                <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-black">
                    {{ $openTickets->count() }}
                </span>
            </div>

            <div class="space-y-2.5 min-h-[300px]">
                @forelse($openTickets as $ticket)
                @include('helpdesk._kanban_card', ['ticket' => $ticket])
                @empty
                <div class="py-8 text-center text-slate-400 text-xs">
                    <p>Tidak ada tiket menunggu.</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- 2. IN PROGRESS (SEDANG DIPROSES) -->
        <div class="bg-slate-100/80 rounded-3xl p-3.5 border border-slate-200/80 space-y-3">
            <div class="flex items-center justify-between px-2">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-blue-600"></span>
                    <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider">Sedang Diproses</h3>
                </div>
                <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 text-[10px] font-black">
                    {{ $inProgressTickets->count() }}
                </span>
            </div>

            <div class="space-y-2.5 min-h-[300px]">
                @forelse($inProgressTickets as $ticket)
                @include('helpdesk._kanban_card', ['ticket' => $ticket])
                @empty
                <div class="py-8 text-center text-slate-400 text-xs">
                    <p>Tidak ada tiket diproses.</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- 3. ANSWERED (DIJAWAB) -->
        <div class="bg-slate-100/80 rounded-3xl p-3.5 border border-slate-200/80 space-y-3">
            <div class="flex items-center justify-between px-2">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-purple-500"></span>
                    <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider">Telah Dijawab</h3>
                </div>
                <span class="px-2 py-0.5 rounded-full bg-purple-100 text-purple-800 text-[10px] font-black">
                    {{ $answeredTickets->count() }}
                </span>
            </div>

            <div class="space-y-2.5 min-h-[300px]">
                @forelse($answeredTickets as $ticket)
                @include('helpdesk._kanban_card', ['ticket' => $ticket])
                @empty
                <div class="py-8 text-center text-slate-400 text-xs">
                    <p>Tidak ada tiket dijawab.</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- 4. CLOSED / RESOLVED (SELESAI) -->
        <div class="bg-slate-100/80 rounded-3xl p-3.5 border border-slate-200/80 space-y-3">
            <div class="flex items-center justify-between px-2">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                    <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider">Selesai / Closed</h3>
                </div>
                <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-black">
                    {{ $closedTickets->count() }}
                </span>
            </div>

            <div class="space-y-2.5 min-h-[300px]">
                @forelse($closedTickets as $ticket)
                @include('helpdesk._kanban_card', ['ticket' => $ticket])
                @empty
                <div class="py-8 text-center text-slate-400 text-xs">
                    <p>Tidak ada tiket selesai.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
