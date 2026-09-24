<div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-xs hover:shadow-md transition-all space-y-2.5">
    <div class="flex items-center justify-between gap-1 text-[11px]">
        <a href="{{ route('helpdesk.tickets.show', $ticket->id) }}" class="font-mono font-bold text-primary hover:underline">
            {{ $ticket->ticket_number }}
        </a>
        <span class="px-2 py-0.2 rounded-full border text-[9px] font-bold uppercase {{ $ticket->priority_badge }}">
            {{ $ticket->priority }}
        </span>
    </div>

    <h4 class="text-xs font-bold text-slate-800 line-clamp-2 leading-snug">
        <a href="{{ route('helpdesk.tickets.show', $ticket->id) }}" class="hover:text-primary transition-colors">
            {{ $ticket->subject }}
        </a>
    </h4>

    <div class="flex items-center justify-between text-[10px] text-slate-500 pt-1 border-t border-slate-100">
        <span class="inline-flex items-center gap-1 font-semibold text-slate-700">
            <i class="{{ $ticket->division->icon ?? 'fa-solid fa-headset' }} text-slate-400 text-[9px]"></i>
            <span>{{ $ticket->division->code ?? '-' }}</span>
        </span>
        <span class="{{ $ticket->isOverdue() ? 'text-rose-600 font-bold' : 'text-slate-400' }}">
            {{ $ticket->created_at->diffForHumans() }}
        </span>
    </div>

    @if($ticket->workplan_task_id)
    <div class="pt-1">
        <span class="inline-flex items-center gap-1 text-[9px] font-bold px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 border border-indigo-200">
            <i class="fa-solid fa-list-check text-[8px]"></i>
            <span>Work Plan: In Progress</span>
        </span>
    </div>
    @endif

    <div class="flex items-center justify-between pt-1">
        <div class="flex items-center gap-1.5 text-[11px]">
            <div class="w-5 h-5 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-[9px]" title="Pengaju: {{ $ticket->creator->name ?? 'User' }}">
                {{ strtoupper(substr($ticket->creator->name ?? 'U', 0, 1)) }}
            </div>
            <span class="text-[10px] text-slate-500 truncate max-w-[90px]">{{ $ticket->creator->name ?? 'User' }}</span>
        </div>

        @if($ticket->assignedAgent)
        <div class="w-5 h-5 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-[9px]" title="Petugas: {{ $ticket->assignedAgent->name }}">
            {{ strtoupper(substr($ticket->assignedAgent->name, 0, 1)) }}
        </div>
        @else
        <span class="text-[9px] text-slate-400 italic">Unassigned</span>
        @endif
    </div>
</div>
