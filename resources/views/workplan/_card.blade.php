@php
    $isOverdue = $task->isOverdue();
    $totalSub = $task->subtasks->count();
    $completedSub = $task->subtasks->where('is_completed', true)->count();
    $progress = $task->progressPercentage();
    $priorityClass = match($task->priority) {
        'High' => 'bg-rose-50 text-rose-700 border-rose-200',
        'Medium' => 'bg-amber-50 text-amber-700 border-amber-200',
        default => 'bg-emerald-50 text-emerald-700 border-emerald-200',
    };
    $priorityIcon = match($task->priority) {
        'High' => 'fa-solid fa-fire text-rose-500',
        'Medium' => 'fa-solid fa-bolt text-amber-500',
        default => 'fa-solid fa-feather text-emerald-500',
    };
    $cleanDesc = trim(strip_tags($task->description ?? ''));
@endphp

<div class="task-card bg-white rounded-xl p-3.5 border {{ $isOverdue ? 'border-rose-400 bg-rose-50/20' : 'border-slate-200/90' }} shadow-sm hover:shadow-md hover:border-primary/50 transition-all duration-200 cursor-grab active:cursor-grabbing group relative"
     id="task-card-{{ $task->id }}"
     data-task-id="{{ $task->id }}"
     draggable="true"
     @dragstart="handleDragStart($event, {{ $task->id }})"
     @dragend="handleDragEnd($event)">

    <!-- Top Row: Priority, Overdue, Task ID, Quick Menu -->
    <div class="flex items-center justify-between gap-2 mb-2">
        <div class="flex items-center gap-1.5 flex-wrap">
            <span class="inline-flex items-center gap-1 text-[10px] font-black px-2 py-0.5 rounded-full border {{ $priorityClass }}">
                <i class="{{ $priorityIcon }} text-[9px]"></i>
                {{ strtoupper($task->priority) }}
            </span>
            @if($isOverdue)
            <span class="inline-flex items-center gap-1 text-[9px] font-extrabold px-1.5 py-0.2 rounded bg-rose-600 text-white uppercase tracking-wider animate-pulse">
                Terlambat
            </span>
            @endif
        </div>

        <div class="flex items-center gap-1">
            <span class="text-[10px] font-bold text-slate-400">#{{ $task->id }}</span>
            <div class="relative" x-data="{ menuOpen: false }">
                <button type="button" @click.stop="menuOpen = !menuOpen" class="w-6 h-6 rounded-md hover:bg-slate-100 text-slate-400 hover:text-slate-600 flex items-center justify-center text-xs transition-all">
                    <i class="fa-solid fa-ellipsis-vertical"></i>
                </button>
                <div x-show="menuOpen" 
                     @click.away="menuOpen = false" 
                     x-cloak
                     class="absolute right-0 top-full mt-1 w-36 bg-white rounded-xl shadow-lg border border-slate-200 py-1 z-20 text-xs font-semibold">
                    <button type="button" @click.stop="openTaskDetail({{ $task->id }}); menuOpen = false" class="w-full px-3 py-1.5 text-left text-slate-700 hover:bg-slate-50 flex items-center gap-2">
                        <i class="fa-solid fa-eye text-primary w-4"></i> Detail
                    </button>
                    @if($column === 'done')
                    <form method="POST" action="{{ route('workplan.archive', $task->id) }}" class="block">
                        @csrf
                        <button type="submit" class="w-full px-3 py-1.5 text-left text-slate-700 hover:bg-slate-50 flex items-center gap-2">
                            <i class="fa-solid fa-box-archive text-amber-600 w-4"></i> Arsipkan
                        </button>
                    </form>
                    @endif
                    @if(Auth::user()->isAdmin() || $task->user === Auth::user()->name || $task->delegator === Auth::user()->name)
                    <form method="POST" action="{{ route('workplan.destroy', $task->id) }}" onsubmit="return confirmDeleteWorkplan(event, 'Hapus tugas #{{ $task->id }} secara permanen?')" class="block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-3 py-1.5 text-left text-rose-600 hover:bg-rose-50 flex items-center gap-2 border-t border-slate-100">
                            <i class="fa-solid fa-trash text-rose-500 w-4"></i> Hapus
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Title & Description (Clickable to open Detail) -->
    <div class="cursor-pointer" @click="openTaskDetail({{ $task->id }})">
        <h4 class="text-xs font-bold text-slate-900 group-hover:text-primary transition-colors line-clamp-2 leading-snug">
            {{ $task->title }}
        </h4>

        @if(!empty($cleanDesc))
        <p class="text-[11px] text-slate-500 mt-1 line-clamp-2 leading-relaxed">
            {{ Str::limit($cleanDesc, 90) }}
        </p>
        @endif
    </div>

    <!-- Subtasks Progress Bar (if exists) -->
    @if($totalSub > 0)
    <div class="mt-2.5 pt-2 border-t border-slate-100 cursor-pointer" @click="openTaskDetail({{ $task->id }})">
        <div class="flex items-center justify-between text-[10px] font-bold mb-1">
            <span class="text-slate-500 flex items-center gap-1">
                <i class="fa-solid fa-list-check text-slate-400"></i> Checklist
            </span>
            <span class="{{ $completedSub === $totalSub ? 'text-emerald-600' : 'text-slate-600' }}">
                {{ $completedSub }}/{{ $totalSub }}
            </span>
        </div>
        <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
            <div class="h-full bg-emerald-500 rounded-full transition-all duration-300" style="width: {{ $progress }}%"></div>
        </div>
    </div>
    @endif

    <!-- Card Footer: Assignee, Due Date, Comments, Attachment -->
    <div class="flex items-center justify-between gap-2 mt-3 pt-2.5 border-t border-slate-100 text-[11px]">
        
        <!-- Assignee User Info -->
        <div class="flex items-center gap-1.5 min-w-0" title="Penerima Tugas: {{ $task->assignee ?: '-' }}">
            <img src="{{ App\Models\Task::getAvatarUrl($task->assignee) }}" 
                 alt="{{ $task->assignee }}"
                 class="w-5 h-5 rounded-full object-cover ring-1 ring-slate-200 bg-slate-100 flex-shrink-0"
                 onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?background=6366f1&color=fff&name={{ urlencode($task->assignee ?: 'User') }}';">
            <span class="text-[11px] font-semibold text-slate-700 truncate max-w-[90px]">
                {{ $task->assignee ?: '-' }}
            </span>
        </div>

        <!-- Meta Indicators: Due Date & Comments -->
        <div class="flex items-center gap-2 flex-shrink-0">
            @if(!empty($task->attachment_url))
            <a href="{{ $task->attachment_url }}" target="_blank" @click.stop class="text-slate-400 hover:text-primary transition-colors" title="Lihat Lampiran">
                <i class="fa-solid fa-paperclip text-[10px]"></i>
            </a>
            @endif

            @if($task->comments->count() > 0)
            <span class="flex items-center gap-1 text-[10px] font-bold text-slate-500">
                <i class="fa-regular fa-comment text-slate-400"></i>
                {{ $task->comments->count() }}
            </span>
            @endif

            @if($task->due_date)
            <span class="inline-flex items-center gap-1 text-[10px] font-bold px-1.5 py-0.5 rounded {{ $isOverdue ? 'bg-rose-100 text-rose-700 font-extrabold' : 'bg-slate-100 text-slate-600' }}" title="Target Deadline">
                <i class="fa-regular fa-calendar text-[9px]"></i>
                {{ $task->due_date->format('d M') }}
            </span>
            @endif
        </div>

    </div>

</div>
