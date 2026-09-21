@extends('layouts.app')

@section('title', 'Work Plan & ToDoList - Support System')

@section('content')
<div class="space-y-6" x-data="kanbanBoard()">

    <!-- Page Header Card -->
    <div class="page-header-card flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-13 h-13 rounded-2xl bg-gradient-to-br from-indigo-600 via-primary-600 to-blue-700 text-white flex items-center justify-center text-2xl shadow-lg shadow-indigo-600/25 flex-shrink-0">
                <i class="fa-solid fa-list-check"></i>
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">Work Plan & ToDoList</h1>
                    <span class="badge-pill bg-indigo-50 text-indigo-700 border-indigo-200">
                        <i class="fa-solid fa-table-columns text-[10px]"></i> KANBAN BOARD
                    </span>
                    <span class="badge-pill bg-emerald-50 text-emerald-700 border-emerald-200 font-semibold">
                        <i class="fa-solid fa-users text-[10px]"></i> Kolaborasi Tim
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    Kelola perencanaan kerja, delegasi tugas harian, checklist sub-tugas, dan evaluasi hasil kerja tim secara terstruktur dan real-time.
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <button type="button" @click="openCreateModal()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-primary-600 to-indigo-600 hover:from-primary-700 hover:to-indigo-700 text-white text-xs font-bold transition-all shadow-md shadow-primary-600/20">
                <i class="fa-solid fa-plus text-sm"></i>
                <span>Tambah Tugas Baru</span>
            </button>
            <a href="{{ route('workplan.chat') }}" class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-xs font-bold transition-all shadow-md shadow-emerald-600/20">
                <i class="fa-solid fa-comments text-sm"></i>
                <span>Groups Chat</span>
            </a>
            <button type="button" @click="openCopyReportModal()" class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl bg-purple-50 border border-purple-200 text-purple-700 hover:bg-purple-100 text-xs font-bold transition-all shadow-sm">
                <i class="fa-solid fa-clipboard-list text-purple-600"></i>
                <span>Salin Laporan (WA)</span>
            </button>
            <a href="{{ route('workplan.export', request()->query()) }}" class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 hover:bg-emerald-100 text-xs font-bold transition-all shadow-sm">
                <i class="fa-solid fa-file-excel text-emerald-600"></i>
                <span>Export Excel</span>
            </a>
            <a href="{{ route('workplan.daily') }}" class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl bg-slate-100 border border-slate-200 text-slate-700 hover:bg-slate-200 text-xs font-semibold transition-all">
                <i class="fa-solid fa-book-bookmark text-slate-600"></i>
                <span>Catatan Divisi (Daily)</span>
            </a>
        </div>
    </div>

    <!-- 5 STATISTIC METRIC CARDS -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3.5">
        <!-- 1. Total Tugas Aktif -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex items-center gap-3.5 hover:shadow-md transition-all">
            <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg flex-shrink-0">
                <i class="fa-solid fa-bars-progress"></i>
            </div>
            <div class="min-w-0 flex-1">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider truncate">Total Aktif</div>
                <div class="text-xl font-extrabold text-slate-900 tracking-tight">{{ number_format($statsTotal) }}</div>
            </div>
        </div>

        <!-- 2. To Do -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex items-center gap-3.5 hover:shadow-md transition-all">
            <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-lg flex-shrink-0">
                <i class="fa-regular fa-clock"></i>
            </div>
            <div class="min-w-0 flex-1">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider truncate">To Do</div>
                <div class="text-xl font-extrabold text-slate-900 tracking-tight">{{ number_format($statsTodo) }}</div>
            </div>
        </div>

        <!-- 3. In Progress -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex items-center gap-3.5 hover:shadow-md transition-all">
            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg flex-shrink-0">
                <i class="fa-solid fa-spinner fa-spin-pulse"></i>
            </div>
            <div class="min-w-0 flex-1">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider truncate">In Progress</div>
                <div class="text-xl font-extrabold text-amber-600 tracking-tight">{{ number_format($statsInProgress) }}</div>
            </div>
        </div>

        <!-- 4. Review Dibutuhkan -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex items-center gap-3.5 hover:shadow-md transition-all">
            <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg flex-shrink-0">
                <i class="fa-solid fa-magnifying-glass-chart"></i>
            </div>
            <div class="min-w-0 flex-1">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider truncate">Review</div>
                <div class="text-xl font-extrabold text-indigo-600 tracking-tight">{{ number_format($statsReview) }}</div>
            </div>
        </div>

        <!-- 5. Selesai (Done) & Overdue -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between gap-2 col-span-2 md:col-span-1 hover:shadow-md transition-all">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg flex-shrink-0">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider truncate">Done</div>
                    <div class="text-lg font-extrabold text-emerald-600 tracking-tight leading-none">{{ number_format($statsDone) }}</div>
                </div>
            </div>
            @if($statsOverdue > 0)
            <div class="text-right pl-2 border-l border-slate-100">
                <span class="text-[10px] font-bold text-rose-500 uppercase tracking-wider block">Overdue</span>
                <span class="text-sm font-black text-rose-600">{{ number_format($statsOverdue) }}</span>
            </div>
            @endif
        </div>
    </div>

    <!-- FILTER & SEARCH TOOLBAR -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('workplan.index') }}" class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
            
            <!-- Search Keyword Input -->
            <div class="flex-1 relative min-w-[240px]">
                <i class="fa-solid fa-magnifying-glass text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 text-sm"></i>
                <input type="text" 
                       name="search" 
                       value="{{ $search }}"
                       placeholder="Cari judul tugas, deskripsi, atau tags..." 
                       class="w-full pl-10 pr-4 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                @if(!empty($search))
                <a href="{{ route('workplan.index', array_merge(request()->except('search'))) }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs">
                    <i class="fa-solid fa-xmark"></i>
                </a>
                @endif
            </div>

            <!-- Filter Karyawan / Assignee Inhouse dengan Fitur Search (Admin & Head) -->
            @if($isAdmin || ($isHead && count($usersInView) > 1))
            <div class="w-full lg:w-72 relative" 
                 x-data="searchableUserFilter({
                     selected: '{{ $filterUser }}',
                     users: {{ json_encode($usersInView) }}
                 })"
                 @click.outside="open = false">
                
                <input type="hidden" name="user_filter" :value="selectedValue" id="userFilterInput">

                <!-- Trigger Button -->
                <button type="button" 
                        @click="toggleDropdown()" 
                        class="w-full px-3.5 py-2 text-xs bg-slate-50 hover:bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary flex items-center justify-between gap-2 transition-all shadow-xs cursor-pointer text-left">
                    <div class="flex items-center gap-2 min-w-0 flex-1">
                        <i class="fa-solid fa-user-tie text-slate-400 text-xs shrink-0" :class="{ 'text-primary': selectedValue && selectedValue !== 'all' }"></i>
                        <span class="truncate" 
                              :class="selectedValue && selectedValue !== 'all' ? 'font-extrabold text-slate-900' : 'font-medium text-slate-600'" 
                              x-text="displayLabel">
                        </span>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0">
                        <span x-show="selectedValue && selectedValue !== 'all'" 
                              @click.stop="selectUser('all')" 
                              class="w-4 h-4 rounded-full bg-slate-200 hover:bg-rose-100 hover:text-rose-600 text-slate-500 flex items-center justify-center text-[10px] transition-colors" 
                              title="Reset Filter Karyawan">
                            <i class="fa-solid fa-xmark"></i>
                        </span>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200" 
                           :class="{ 'rotate-180': open }"></i>
                    </div>
                </button>

                <!-- Dropdown Menu dengan Kotak Search -->
                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-1 scale-98"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-1 scale-98"
                     class="absolute left-0 top-full mt-1.5 z-50 bg-white rounded-2xl shadow-xl border border-slate-200/90 overflow-hidden w-full min-w-[280px]" 
                     style="display: none;">
                    
                    <!-- Search Input Box -->
                    <div class="p-2.5 border-b border-slate-100 bg-slate-50/80">
                        <div class="relative">
                            <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                            <input type="text" 
                                   x-ref="searchInput" 
                                   x-model="search" 
                                   @keydown.escape="open = false" 
                                   @keydown.enter.prevent="if(filteredUsers.length > 0) selectUser(filteredUsers[0])"
                                   placeholder="Cari nama karyawan inhouse..." 
                                   class="w-full pl-8 pr-7 py-1.5 bg-white border border-slate-200 rounded-lg text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20 transition-all font-medium">
                            <button type="button" 
                                    x-show="search" 
                                    @click="search = ''; $refs.searchInput.focus()" 
                                    class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-[11px]">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Options List -->
                    <div class="max-h-60 overflow-y-auto p-1.5 space-y-0.5 text-xs">
                        <!-- Option: Semua Karyawan Inhouse -->
                        <button type="button" 
                                @click="selectUser('all')" 
                                class="w-full px-3 py-2 rounded-xl text-left transition-all flex items-center justify-between font-medium cursor-pointer"
                                :class="!selectedValue || selectedValue === 'all' ? 'bg-primary-50 text-primary font-bold shadow-xs' : 'text-slate-700 hover:bg-slate-50'">
                            <span class="flex items-center gap-2">
                                <i class="fa-solid fa-users text-xs text-primary"></i>
                                <span>Semua Karyawan Inhouse ({{ count($usersInView) }})</span>
                            </span>
                            <i x-show="!selectedValue || selectedValue === 'all'" class="fa-solid fa-check text-xs text-primary"></i>
                        </button>

                        <div class="border-t border-slate-100 my-1"></div>

                        <!-- Filtered Inhouse Employees -->
                        <template x-for="name in filteredUsers" :key="name">
                            <button type="button" 
                                    @click="selectUser(name)" 
                                    class="w-full px-3 py-2 rounded-xl text-left transition-all flex items-center justify-between font-medium cursor-pointer"
                                    :class="selectedValue === name ? 'bg-primary-50 text-primary font-bold shadow-xs' : 'text-slate-700 hover:bg-slate-50'">
                                <span class="flex items-center gap-2 truncate">
                                    <i class="fa-solid fa-user-circle text-slate-400 text-xs shrink-0" :class="{ 'text-primary': selectedValue === name }"></i>
                                    <span class="truncate" x-text="name"></span>
                                </span>
                                <i x-show="selectedValue === name" class="fa-solid fa-check text-xs text-primary shrink-0 ml-2"></i>
                            </button>
                        </template>

                        <!-- Empty State -->
                        <div x-show="filteredUsers.length === 0" class="py-6 text-center text-slate-400 text-xs">
                            <i class="fa-solid fa-user-slash text-base text-slate-300 block mb-1"></i>
                            <span>Tidak ada karyawan inhouse yang cocok</span>
                        </div>
                    </div>

                    <!-- Footer Info -->
                    <div class="px-3 py-2 bg-slate-50 border-t border-slate-100 text-[10px] text-slate-400 flex items-center justify-between">
                        <span>Hanya Karyawan Inhouse</span>
                        <span x-text="`${filteredUsers.length} karyawan ditemukan`"></span>
                    </div>
                </div>

            </div>
            @endif

            <!-- Smart Filter Pills -->
            <div class="flex items-center gap-1.5 flex-wrap">
                <input type="hidden" name="smart" id="smartFilterInput" value="{{ $smartFilter }}">

                <button type="button" 
                        @click="setSmartFilter('all')" 
                        class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $smartFilter === 'all' ? 'bg-primary text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Semua
                </button>
                <button type="button" 
                        @click="setSmartFilter('my')" 
                        class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $smartFilter === 'my' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    <i class="fa-solid fa-user text-[10px] mr-1"></i> Tugas Saya
                </button>
                <button type="button" 
                        @click="setSmartFilter('high')" 
                        class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $smartFilter === 'high' ? 'bg-rose-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    <i class="fa-solid fa-fire text-[10px] mr-1"></i> Prioritas Tinggi
                </button>
                <button type="button" 
                        @click="setSmartFilter('overdue')" 
                        class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $smartFilter === 'overdue' ? 'bg-amber-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    <i class="fa-solid fa-triangle-exclamation text-[10px] mr-1"></i> Terlambat
                </button>
                <button type="submit" class="px-3 py-1.5 bg-slate-800 text-white rounded-lg text-xs font-bold hover:bg-slate-900 transition-all">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- ============================================================================== -->
    <!-- KANBAN BOARD (4 KOLOM STATUS)                                                  -->
    <!-- ============================================================================== -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 items-start">
        
        <!-- KOLOM 1: TO DO -->
        <div class="kanban-col-wrapper bg-slate-50/80 rounded-2xl border border-slate-200/80 p-3.5 flex flex-col min-h-[520px]">
            <!-- Header Kolom -->
            <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-200">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-slate-400"></span>
                    <h3 class="text-xs font-black tracking-wider text-slate-800 uppercase">To Do</h3>
                    <span class="px-2 py-0.5 rounded-full bg-slate-200/80 text-slate-700 text-[10px] font-extrabold" id="col-count-todo" title="Total Seluruh Tugas To Do">
                        {{ number_format($statsTodo) }}
                    </span>
                </div>
                <button type="button" @click="openCreateModal('todo')" class="w-6 h-6 rounded-lg bg-white border border-slate-200 hover:bg-slate-100 text-slate-500 flex items-center justify-center text-xs transition-all" title="Tambah ke To Do">
                    <i class="fa-solid fa-plus"></i>
                </button>
            </div>

            <!-- Drop Zone / Cards List -->
            <div class="kanban-dropzone flex-1 space-y-3 min-h-[420px]" 
                 id="col-todo" 
                 data-status="todo"
                 @dragover.prevent="handleDragOver($event)"
                 @dragleave="handleDragLeave($event)"
                 @drop="handleDrop($event, 'todo')">
                @forelse($tasksTodo as $task)
                    @include('workplan._card', ['task' => $task, 'column' => 'todo'])
                @empty
                    <div class="h-40 flex flex-col items-center justify-center text-slate-400 text-center p-4 border border-dashed border-slate-200 rounded-xl pointer-events-none empty-state">
                        <i class="fa-solid fa-inbox text-2xl mb-1 text-slate-300"></i>
                        <span class="text-xs font-medium">Tidak ada tugas To Do</span>
                    </div>
                @endforelse
            </div>

            <!-- Tombol Muat Lebih Banyak (Load More) -->
            @if($statsTodo > $tasksTodo->count())
            <div class="pt-3 border-t border-slate-200/60 mt-3" id="load-more-wrapper-todo">
                <button type="button" 
                        @click="loadMoreColumn('todo')" 
                        :disabled="loadingColumns.todo"
                        class="w-full py-2 px-3 text-[11px] font-bold text-slate-600 hover:text-primary hover:bg-white bg-slate-100/80 border border-slate-200 rounded-xl transition-all shadow-xs flex items-center justify-center gap-1.5 cursor-pointer disabled:opacity-50">
                    <template x-if="loadingColumns.todo">
                        <i class="fa-solid fa-spinner fa-spin text-xs"></i>
                    </template>
                    <template x-if="!loadingColumns.todo">
                        <i class="fa-solid fa-angles-down text-[10px]"></i>
                    </template>
                    <span>Muat 25 Tugas Lagi (<span id="remaining-count-todo">{{ number_format($statsTodo - $tasksTodo->count()) }}</span> tersisa)</span>
                </button>
            </div>
            @endif
        </div>

        <!-- KOLOM 2: IN PROGRESS -->
        <div class="kanban-col-wrapper bg-amber-50/40 rounded-2xl border border-amber-200/60 p-3.5 flex flex-col min-h-[520px]">
            <!-- Header Kolom -->
            <div class="flex items-center justify-between pb-3 mb-3 border-b border-amber-200/60">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                    <h3 class="text-xs font-black tracking-wider text-amber-900 uppercase">In Progress</h3>
                    <span class="px-2 py-0.5 rounded-full bg-amber-200/80 text-amber-800 text-[10px] font-extrabold" id="col-count-inprogress" title="Total Seluruh Tugas In Progress">
                        {{ number_format($statsInProgress) }}
                    </span>
                </div>
                <button type="button" @click="openCreateModal('inprogress')" class="w-6 h-6 rounded-lg bg-white border border-amber-200 hover:bg-amber-100 text-amber-700 flex items-center justify-center text-xs transition-all" title="Tambah ke In Progress">
                    <i class="fa-solid fa-plus"></i>
                </button>
            </div>

            <!-- Drop Zone / Cards List -->
            <div class="kanban-dropzone flex-1 space-y-3 min-h-[420px]" 
                 id="col-inprogress" 
                 data-status="inprogress"
                 @dragover.prevent="handleDragOver($event)"
                 @dragleave="handleDragLeave($event)"
                 @drop="handleDrop($event, 'inprogress')">
                @forelse($tasksInProgress as $task)
                    @include('workplan._card', ['task' => $task, 'column' => 'inprogress'])
                @empty
                    <div class="h-40 flex flex-col items-center justify-center text-amber-400 text-center p-4 border border-dashed border-amber-200 rounded-xl pointer-events-none empty-state">
                        <i class="fa-solid fa-spinner text-2xl mb-1 text-amber-300"></i>
                        <span class="text-xs font-medium">Tidak ada tugas sedang dikerjakan</span>
                    </div>
                @endforelse
            </div>

            <!-- Tombol Muat Lebih Banyak (Load More) -->
            @if($statsInProgress > $tasksInProgress->count())
            <div class="pt-3 border-t border-amber-200/60 mt-3" id="load-more-wrapper-inprogress">
                <button type="button" 
                        @click="loadMoreColumn('inprogress')" 
                        :disabled="loadingColumns.inprogress"
                        class="w-full py-2 px-3 text-[11px] font-bold text-amber-800 hover:text-primary hover:bg-white bg-amber-100/60 border border-amber-200 rounded-xl transition-all shadow-xs flex items-center justify-center gap-1.5 cursor-pointer disabled:opacity-50">
                    <template x-if="loadingColumns.inprogress">
                        <i class="fa-solid fa-spinner fa-spin text-xs"></i>
                    </template>
                    <template x-if="!loadingColumns.inprogress">
                        <i class="fa-solid fa-angles-down text-[10px]"></i>
                    </template>
                    <span>Muat 25 Tugas Lagi (<span id="remaining-count-inprogress">{{ number_format($statsInProgress - $tasksInProgress->count()) }}</span> tersisa)</span>
                </button>
            </div>
            @endif
        </div>

        <!-- KOLOM 3: REVIEW -->
        <div class="kanban-col-wrapper bg-indigo-50/40 rounded-2xl border border-indigo-200/60 p-3.5 flex flex-col min-h-[520px]">
            <!-- Header Kolom -->
            <div class="flex items-center justify-between pb-3 mb-3 border-b border-indigo-200/60">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                    <h3 class="text-xs font-black tracking-wider text-indigo-900 uppercase">Review</h3>
                    <span class="px-2 py-0.5 rounded-full bg-indigo-200/80 text-indigo-800 text-[10px] font-extrabold" id="col-count-review" title="Total Seluruh Tugas Menunggu Review">
                        {{ number_format($statsReview) }}
                    </span>
                </div>
                <span class="text-[10px] font-bold text-indigo-600 bg-indigo-100/80 px-2 py-0.5 rounded-md" title="Persetujuan Delegator/Pimpinan">
                    APPROVAL
                </span>
            </div>

            <!-- Drop Zone / Cards List -->
            <div class="kanban-dropzone flex-1 space-y-3 min-h-[420px]" 
                 id="col-review" 
                 data-status="review"
                 @dragover.prevent="handleDragOver($event)"
                 @dragleave="handleDragLeave($event)"
                 @drop="handleDrop($event, 'review')">
                @forelse($tasksReview as $task)
                    @include('workplan._card', ['task' => $task, 'column' => 'review'])
                @empty
                    <div class="h-40 flex flex-col items-center justify-center text-indigo-400 text-center p-4 border border-dashed border-indigo-200 rounded-xl pointer-events-none empty-state">
                        <i class="fa-solid fa-check-double text-2xl mb-1 text-indigo-300"></i>
                        <span class="text-xs font-medium">Tidak ada tugas menunggu review</span>
                    </div>
                @endforelse
            </div>

            <!-- Tombol Muat Lebih Banyak (Load More) -->
            @if($statsReview > $tasksReview->count())
            <div class="pt-3 border-t border-indigo-200/60 mt-3" id="load-more-wrapper-review">
                <button type="button" 
                        @click="loadMoreColumn('review')" 
                        :disabled="loadingColumns.review"
                        class="w-full py-2 px-3 text-[11px] font-bold text-indigo-800 hover:text-primary hover:bg-white bg-indigo-100/60 border border-indigo-200 rounded-xl transition-all shadow-xs flex items-center justify-center gap-1.5 cursor-pointer disabled:opacity-50">
                    <template x-if="loadingColumns.review">
                        <i class="fa-solid fa-spinner fa-spin text-xs"></i>
                    </template>
                    <template x-if="!loadingColumns.review">
                        <i class="fa-solid fa-angles-down text-[10px]"></i>
                    </template>
                    <span>Muat 25 Tugas Lagi (<span id="remaining-count-review">{{ number_format($statsReview - $tasksReview->count()) }}</span> tersisa)</span>
                </button>
            </div>
            @endif
        </div>

        <!-- KOLOM 4: DONE -->
        <div class="kanban-col-wrapper bg-emerald-50/40 rounded-2xl border border-emerald-200/60 p-3.5 flex flex-col min-h-[520px]">
            <!-- Header Kolom -->
            <div class="flex items-center justify-between pb-3 mb-3 border-b border-emerald-200/60">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    <h3 class="text-xs font-black tracking-wider text-emerald-900 uppercase">Done</h3>
                    <span class="px-2 py-0.5 rounded-full bg-emerald-200/80 text-emerald-800 text-[10px] font-extrabold" id="col-count-done" title="Total Seluruh Tugas Selesai">
                        {{ number_format($statsDone) }}
                    </span>
                </div>
                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100/80 px-2 py-0.5 rounded-md">
                    SELESAI
                </span>
            </div>

            <!-- Drop Zone / Cards List -->
            <div class="kanban-dropzone flex-1 space-y-3 min-h-[420px]" 
                 id="col-done" 
                 data-status="done"
                 @dragover.prevent="handleDragOver($event)"
                 @dragleave="handleDragLeave($event)"
                 @drop="handleDrop($event, 'done')">
                @forelse($tasksDone as $task)
                    @include('workplan._card', ['task' => $task, 'column' => 'done'])
                @empty
                    <div class="h-40 flex flex-col items-center justify-center text-emerald-400 text-center p-4 border border-dashed border-emerald-200 rounded-xl pointer-events-none empty-state">
                        <i class="fa-solid fa-award text-2xl mb-1 text-emerald-300"></i>
                        <span class="text-xs font-medium">Belum ada tugas selesai</span>
                    </div>
                @endforelse
            </div>

            <!-- Tombol Muat Lebih Banyak (Load More) -->
            @if($statsDone > $tasksDone->count())
            <div class="pt-3 border-t border-emerald-200/60 mt-3" id="load-more-wrapper-done">
                <button type="button" 
                        @click="loadMoreColumn('done')" 
                        :disabled="loadingColumns.done"
                        class="w-full py-2 px-3 text-[11px] font-bold text-emerald-800 hover:text-primary hover:bg-white bg-emerald-100/60 border border-emerald-200 rounded-xl transition-all shadow-xs flex items-center justify-center gap-1.5 cursor-pointer disabled:opacity-50">
                    <template x-if="loadingColumns.done">
                        <i class="fa-solid fa-spinner fa-spin text-xs"></i>
                    </template>
                    <template x-if="!loadingColumns.done">
                        <i class="fa-solid fa-angles-down text-[10px]"></i>
                    </template>
                    <span>Muat 25 Tugas Lagi (<span id="remaining-count-done">{{ number_format($statsDone - $tasksDone->count()) }}</span> tersisa)</span>
                </button>
            </div>
            @endif
        </div>

    </div>

    <!-- ============================================================================== -->
    <!-- DAFTAR TUGAS DIARSIPKAN (ARCHIVED ACCORDION)                                    -->
    <!-- ============================================================================== -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden" x-data="{ openArchive: false }">
        <button type="button" 
                @click="openArchive = !openArchive"
                class="w-full px-5 py-4 flex items-center justify-between hover:bg-slate-50 transition-all">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-box-archive"></i>
                </div>
                <div class="text-left">
                    <h4 class="text-sm font-bold text-slate-800">Tugas Diarsipkan (Archived)</h4>
                    <p class="text-[11px] text-slate-400">Daftar tugas historis yang telah diselesaikan dan disimpan rapi.</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-bold">
                    {{ $tasksArchived->total() }} Arsip
                </span>
                <i class="fa-solid fa-chevron-down text-xs text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': openArchive }"></i>
            </div>
        </button>

        <div x-show="openArchive" x-collapse class="border-t border-slate-200 p-5 bg-slate-50/40">
            @if($tasksArchived->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3.5">
                @foreach($tasksArchived as $archivedTask)
                <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between gap-2">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-1.5">
                            <span class="text-[10px] font-bold text-slate-400">#{{ $archivedTask->id }}</span>
                            <span class="text-[10px] px-2 py-0.5 rounded-full font-bold bg-slate-100 text-slate-600">
                                {{ $archivedTask->priority }}
                            </span>
                        </div>
                        <h5 class="text-xs font-bold text-slate-800 line-clamp-2">{{ $archivedTask->title }}</h5>
                    </div>
                    <div class="flex items-center justify-between pt-2 border-t border-slate-100 text-[11px] text-slate-400">
                        <span>{{ $archivedTask->date_completed ? $archivedTask->date_completed->format('d M Y') : '-' }}</span>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="openTaskDetail({{ $archivedTask->id }})" class="text-primary hover:underline font-semibold">
                                Detail
                            </button>
                            <form method="POST" action="{{ route('workplan.unarchive', $archivedTask->id) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-emerald-600 hover:underline font-semibold" title="Pulihkan ke Done">
                                    Pulihkan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-4">
                {{ $tasksArchived->links() }}
            </div>
            @else
            <div class="text-center py-8 text-slate-400 text-xs">
                Belum ada tugas yang diarsipkan.
            </div>
            @endif
        </div>
    </div>

    <!-- ============================================================================== -->
    <!-- MODALS: TAMBAH TUGAS, DETAIL TUGAS, SALIN LAPORAN                              -->
    <!-- ============================================================================== -->
    @include('workplan._modals')

</div>
@endsection

@push('scripts')
<script>
function kanbanBoard() {
    return {
        draggedTaskId: null,
        detailTaskId: null,
        activeTab: 'detail',
        currentTask: null,
        subtasks: [],
        comments: [],
        activities: [],
        canEdit: false,
        canDelete: false,
        canApprove: false,
        isEditingTask: false,
        isLoadingDetail: false,
        newCommentText: '',
        newSubtaskText: '',
        submittingComment: false,
        createAttachment: null,
        isDraggingCreate: false,
        editAttachment: null,
        isDraggingEdit: false,
        commentAttachment: null,
        isDraggingComment: false,
        loadingColumns: {
            todo: false,
            inprogress: false,
            review: false,
            done: false
        },
        columnOffsets: {
            todo: {{ $tasksTodo->count() }},
            inprogress: {{ $tasksInProgress->count() }},
            review: {{ $tasksReview->count() }},
            done: {{ $tasksDone->count() }}
        },
        editForm: {
            title: '',
            description: '',
            priority: 'Medium',
            due_date: '',
            assignee: ''
        },

        loadMoreColumn(column) {
            if (this.loadingColumns[column]) return;
            this.loadingColumns[column] = true;

            const offset = this.columnOffsets[column] || 0;
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('column', column);
            urlParams.set('offset', offset);
            urlParams.set('limit', 25);

            fetch(`{{ route('workplan.load_more') }}?${urlParams.toString()}`)
                .then(res => res.json())
                .then(data => {
                    this.loadingColumns[column] = false;
                    if (data.success && data.html) {
                        const dropzone = document.getElementById(`col-${column}`);
                        if (dropzone) {
                            const emptyState = dropzone.querySelector('.empty-state');
                            if (emptyState) emptyState.remove();

                            const temp = document.createElement('div');
                            temp.innerHTML = data.html;
                            const cards = Array.from(temp.children);
                            cards.forEach(card => {
                                dropzone.appendChild(card);
                                if (window.Alpine) {
                                    Alpine.initTree(card);
                                }
                            });
                        }

                        this.columnOffsets[column] = data.loaded;

                        const wrapper = document.getElementById(`load-more-wrapper-${column}`);
                        if (wrapper) {
                            if (!data.has_more) {
                                wrapper.remove();
                            } else {
                                const remEl = document.getElementById(`remaining-count-${column}`);
                                if (remEl) remEl.innerText = Number(data.remaining).toLocaleString('id-ID');
                            }
                        }
                    } else if (!data.has_more) {
                        const wrapper = document.getElementById(`load-more-wrapper-${column}`);
                        if (wrapper) wrapper.remove();
                    }
                })
                .catch(err => {
                    this.loadingColumns[column] = false;
                    console.error('Gagal memuat tugas:', err);
                });
        },
        previewModal: {
            open: false,
            url: '',
            name: '',
            isImage: false,
            isPdf: false
        },

        openPreviewModal(url, name = '') {
            if (!url) return;
            const cleanUrl = url.split('?')[0];
            const isImg = Boolean(cleanUrl.match(/\.(jpg|jpeg|png|gif|webp|svg)$/i) || url.startsWith('blob:') || url.startsWith('data:image/'));
            const isPdf = Boolean(cleanUrl.match(/\.pdf$/i));
            const fileName = name || url.split('/').pop() || 'Berkas Lampiran';

            this.previewModal = {
                open: true,
                url: url,
                name: decodeURIComponent(fileName),
                isImage: isImg,
                isPdf: isPdf
            };
        },

        closePreviewModal() {
            this.previewModal.open = false;
            this.previewModal.url = '';
        },

        formatBytes(bytes) {
            if (!bytes || bytes === 0) return '0 B';
            const k = 1024;
            const sizes = ['B', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
        },

        setAttachmentFile(file, target) {
            if (!file) return;
            if (file.size > 10 * 1024 * 1024) {
                Swal.fire({
                    icon: 'warning',
                    title: 'File Terlalu Besar',
                    text: 'Ukuran berkas lampiran maksimal 10 MB.',
                    confirmButtonColor: '#0F52BA',
                    customClass: { popup: 'rounded-2xl' }
                });
                return;
            }

            const isImage = file.type.startsWith('image/');
            let previewUrl = null;
            if (isImage) {
                previewUrl = URL.createObjectURL(file);
            }

            const ext = file.name && file.name.includes('.') ? file.name.split('.').pop().toLowerCase() : (isImage ? 'png' : 'file');

            const item = {
                file: file,
                name: file.name || 'lampiran',
                size: this.formatBytes(file.size),
                isImage: isImage,
                previewUrl: previewUrl,
                extension: ext
            };

            if (target === 'create') {
                if (this.createAttachment && this.createAttachment.previewUrl) {
                    URL.revokeObjectURL(this.createAttachment.previewUrl);
                }
                this.createAttachment = item;
                const fileInput = document.getElementById('createTaskAttachmentInput');
                if (fileInput) {
                    try {
                        const dt = new DataTransfer();
                        dt.items.add(file);
                        fileInput.files = dt.files;
                    } catch(e) {}
                }
            } else if (target === 'edit') {
                if (this.editAttachment && this.editAttachment.previewUrl) {
                    URL.revokeObjectURL(this.editAttachment.previewUrl);
                }
                this.editAttachment = item;
            } else if (target === 'comment') {
                if (this.commentAttachment && this.commentAttachment.previewUrl) {
                    URL.revokeObjectURL(this.commentAttachment.previewUrl);
                }
                this.commentAttachment = item;
            }
        },

        removeAttachment(target) {
            if (target === 'create') {
                if (this.createAttachment && this.createAttachment.previewUrl) {
                    URL.revokeObjectURL(this.createAttachment.previewUrl);
                }
                this.createAttachment = null;
                const fileInput = document.getElementById('createTaskAttachmentInput');
                if (fileInput) fileInput.value = '';
            } else if (target === 'edit') {
                if (this.editAttachment && this.editAttachment.previewUrl) {
                    URL.revokeObjectURL(this.editAttachment.previewUrl);
                }
                this.editAttachment = null;
                const fileInput = document.getElementById('editTaskAttachmentInput');
                if (fileInput) fileInput.value = '';
            } else if (target === 'comment') {
                if (this.commentAttachment && this.commentAttachment.previewUrl) {
                    URL.revokeObjectURL(this.commentAttachment.previewUrl);
                }
                this.commentAttachment = null;
                const fileInput = document.getElementById('commentAttachmentInput');
                if (fileInput) fileInput.value = '';
            }
        },

        handleFileSelect(e, target) {
            if (e.target.files && e.target.files.length > 0) {
                this.setAttachmentFile(e.target.files[0], target);
            }
        },

        handleFileDrop(e, target) {
            e.preventDefault();
            if (target === 'create') this.isDraggingCreate = false;
            if (target === 'edit') this.isDraggingEdit = false;
            if (target === 'comment') this.isDraggingComment = false;

            if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length > 0) {
                this.setAttachmentFile(e.dataTransfer.files[0], target);
            }
        },

        handleClipboardPaste(e, target) {
            const clipboard = e.clipboardData || window.clipboardData;
            if (!clipboard || !clipboard.items) return;

            for (let i = 0; i < clipboard.items.length; i++) {
                const item = clipboard.items[i];
                if (item.kind === 'file') {
                    const blob = item.getAsFile();
                    if (blob) {
                        let fileName = blob.name;
                        if (!fileName || fileName === 'image.png' || fileName === 'blob') {
                            const now = new Date();
                            const timeStr = now.getHours().toString().padStart(2,'0') + now.getMinutes().toString().padStart(2,'0') + now.getSeconds().toString().padStart(2,'0');
                            fileName = `screenshot_${timeStr}.png`;
                        }
                        const file = new File([blob], fileName, { type: blob.type || 'image/png' });
                        this.setAttachmentFile(file, target);
                        e.preventDefault();
                        Swal.fire({
                            icon: 'success',
                            title: 'Screenshot Ditempel!',
                            text: `File '${fileName}' berhasil dilampirkan dari clipboard.`,
                            timer: 1500,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });
                        break;
                    }
                }
            }
        },

        setSmartFilter(val) {
            document.getElementById('smartFilterInput').value = val;
            document.getElementById('smartFilterInput').form.submit();
        },

        openCreateModal(defaultStatus = 'todo') {
            document.getElementById('createTaskForm').reset();
            this.removeAttachment('create');
            document.getElementById('createTaskStatus').value = defaultStatus;
            document.getElementById('createTaskModal').classList.remove('hidden');
        },

        closeCreateModal() {
            this.removeAttachment('create');
            document.getElementById('createTaskModal').classList.add('hidden');
        },

        openCopyReportModal() {
            fetch("{{ route('workplan.copy_report') }}")
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('copyReportTextarea').value = data.report;
                        document.getElementById('copyReportModal').classList.remove('hidden');
                    }
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Gagal memuat format laporan.',
                        confirmButtonColor: '#0F52BA',
                        customClass: { popup: 'rounded-2xl' }
                    });
                });
        },

        closeCopyReportModal() {
            document.getElementById('copyReportModal').classList.add('hidden');
        },

        copyReportToClipboard() {
            const textarea = document.getElementById('copyReportTextarea');
            textarea.select();
            document.execCommand('copy');
            if (navigator.clipboard) {
                navigator.clipboard.writeText(textarea.value);
            }
            this.closeCopyReportModal();
            Swal.fire({
                icon: 'success',
                title: 'Berhasil Disalin!',
                text: 'Laporan tugas siap ditempel ke WhatsApp / Chat tim.',
                timer: 2500,
                showConfirmButton: false,
                toast: true,
                position: 'top-end',
                timerProgressBar: true
            });
        },

        // Drag and drop
        handleDragStart(event, taskId) {
            this.draggedTaskId = taskId;
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/plain', taskId);
            event.target.classList.add('opacity-50', 'scale-95');
        },

        handleDragEnd(event) {
            event.target.classList.remove('opacity-50', 'scale-95');
            document.querySelectorAll('.kanban-dropzone').forEach(el => el.classList.remove('bg-primary-50/50', 'border-primary'));
        },

        handleDragOver(event) {
            event.currentTarget.classList.add('bg-primary-50/50', 'border-primary');
        },

        handleDragLeave(event) {
            event.currentTarget.classList.remove('bg-primary-50/50', 'border-primary');
        },

        handleDrop(event, targetStatus) {
            event.currentTarget.classList.remove('bg-primary-50/50', 'border-primary');
            const taskId = this.draggedTaskId || event.dataTransfer.getData('text/plain');
            if (!taskId) return;

            this.moveTaskStatus(taskId, targetStatus);
        },

        moveTaskStatus(taskId, targetStatus) {
            if (this.currentTask && this.currentTask.status === 'review' && targetStatus === 'done' && !this.canApprove) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Akses Ditolak!',
                    html: `Hanya <b>Delegator/Pimpinan (${this.currentTask.delegator})</b> atau <b>Administrator</b> yang berhak menyetujui tugas dari status <b>Review</b> menjadi <b>Done</b>.`,
                    confirmButtonText: 'Mengerti',
                    confirmButtonColor: '#0F52BA',
                    customClass: {
                        popup: 'rounded-2xl shadow-2xl',
                        confirmButton: 'rounded-xl font-bold px-5 py-2.5 text-xs'
                    }
                });
                return;
            }

            fetch(`/workplan/${taskId}/move`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status: targetStatus })
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    window.location.reload();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Akses Ditolak!',
                        html: res.error || 'Gagal memindahkan status tugas.',
                        confirmButtonColor: '#0F52BA',
                        customClass: {
                            popup: 'rounded-2xl shadow-2xl',
                            confirmButton: 'rounded-xl font-bold px-5 py-2.5 text-xs'
                        }
                    });
                }
            })
            .catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Jaringan',
                    text: 'Tidak dapat menghubungi server. Periksa koneksi Anda.',
                    confirmButtonColor: '#0F52BA',
                    customClass: { popup: 'rounded-2xl' }
                });
            });
        },

        // Task detail slide-over
        openTaskDetail(taskId) {
            this.detailTaskId = taskId;
            this.activeTab = 'detail';
            this.isLoadingDetail = true;
            this.isEditingTask = false;
            this.removeAttachment('edit');
            this.removeAttachment('comment');
            document.getElementById('taskDetailDrawer').classList.remove('hidden');

            fetch(`/workplan/${taskId}/details`)
                .then(r => r.json())
                .then(res => {
                    this.isLoadingDetail = false;
                    if (res.success) {
                        this.currentTask = res.task;
                        this.subtasks = res.task.subtasks || [];
                        this.canEdit = res.can_edit;
                        this.canDelete = res.can_delete;
                        this.canApprove = res.can_approve;
                        this.editForm = {
                            title: res.task.title,
                            description: res.task.description,
                            priority: res.task.priority,
                            due_date: res.task.due_date,
                            assignee: res.task.assignee,
                        };
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: res.error || 'Gagal memuat rincian tugas.',
                            confirmButtonColor: '#0F52BA',
                            customClass: { popup: 'rounded-2xl' }
                        });
                    }
                })
                .catch(() => {
                    this.isLoadingDetail = false;
                    Swal.fire({
                        icon: 'error',
                        title: 'Kesalahan Jaringan',
                        text: 'Tidak dapat memuat detail tugas.',
                        confirmButtonColor: '#0F52BA',
                        customClass: { popup: 'rounded-2xl' }
                    });
                });

            this.loadComments(taskId);
            this.loadActivities(taskId);
        },

        closeTaskDetail() {
            this.removeAttachment('edit');
            this.removeAttachment('comment');
            this.isEditingTask = false;
            document.getElementById('taskDetailDrawer').classList.add('hidden');
        },

        addSubtask() {
            if (!this.newSubtaskText.trim()) return;
            const text = this.newSubtaskText.trim();
            this.newSubtaskText = '';

            fetch(`/workplan/${this.detailTaskId}/subtasks`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ subtask_text: text })
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    this.subtasks.push({
                        id: res.subtask.id,
                        subtask_text: res.subtask.subtask_text,
                        is_completed: false
                    });
                    this.loadActivities(this.detailTaskId);
                    Swal.fire({
                        icon: 'success',
                        title: 'Ditambahkan',
                        text: 'Sub-tugas berhasil ditambahkan.',
                        timer: 1500,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Menambah Sub-Tugas',
                        text: res.error || 'Terjadi kesalahan.',
                        confirmButtonColor: '#0F52BA',
                        customClass: { popup: 'rounded-2xl' }
                    });
                }
            });
        },

        toggleSubtaskItem(subtaskId) {
            const item = this.subtasks.find(s => s.id === subtaskId);
            if (!item) return;
            item.is_completed = !item.is_completed;

            fetch(`/workplan/subtasks/${subtaskId}/toggle`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ is_completed: item.is_completed })
            })
            .then(r => r.json())
            .then(res => {
                if (!res.success) {
                    item.is_completed = !item.is_completed; // rollback
                } else {
                    this.loadActivities(this.detailTaskId);
                }
            });
        },

        deleteSubtaskItem(subtaskId) {
            Swal.fire({
                title: 'Hapus Sub-Tugas?',
                text: 'Item checklist ini akan dihapus dari daftar.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-2xl shadow-2xl',
                    confirmButton: 'rounded-xl font-bold px-4 py-2 text-xs',
                    cancelButton: 'rounded-xl font-bold px-4 py-2 text-xs'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/workplan/subtasks/${subtaskId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(r => r.json())
                    .then(res => {
                        if (res.success) {
                            this.subtasks = this.subtasks.filter(s => s.id !== subtaskId);
                            this.loadActivities(this.detailTaskId);
                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus',
                                text: 'Item checklist berhasil dihapus.',
                                timer: 1500,
                                showConfirmButton: false,
                                toast: true,
                                position: 'top-end'
                            });
                        }
                    });
                }
            });
        },

        saveTaskEdit() {
            const formData = new FormData();
            formData.append('_method', 'PUT');
            formData.append('title', this.editForm.title);
            formData.append('description', this.editForm.description || '');
            formData.append('priority', this.editForm.priority || 'Medium');
            formData.append('due_date', this.editForm.due_date || '');
            if (this.editForm.assignee) {
                formData.append('assignee', this.editForm.assignee);
            }
            if (this.editAttachment && this.editAttachment.file) {
                formData.append('attachment', this.editAttachment.file);
            }

            fetch(`/workplan/${this.detailTaskId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    this.currentTask.title = this.editForm.title;
                    this.currentTask.description = this.editForm.description;
                    this.currentTask.priority = this.editForm.priority;
                    this.currentTask.due_date = this.editForm.due_date;
                    this.currentTask.assignee = this.editForm.assignee;
                    if (res.task && res.task.attachment_url) {
                        this.currentTask.attachment_url = res.task.attachment_url;
                    }
                    this.removeAttachment('edit');
                    this.isEditingTask = false;
                    this.loadActivities(this.detailTaskId);
                    const cardTitle = document.querySelector(`#task-card-${this.detailTaskId} h4`);
                    if (cardTitle) cardTitle.innerText = this.editForm.title;
                    Swal.fire({
                        icon: 'success',
                        title: 'Tersimpan!',
                        text: 'Perubahan tugas berhasil disimpan.',
                        timer: 1800,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: res.error || 'Gagal menyimpan perubahan.',
                        confirmButtonColor: '#0F52BA',
                        customClass: { popup: 'rounded-2xl' }
                    });
                }
            });
        },

        deleteCurrentTask() {
            Swal.fire({
                title: 'Hapus Tugas Permanen?',
                text: 'Tugas ini beserta seluruh sub-tugas dan diskusinya akan dihapus permanen.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Hapus Permanen',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-2xl shadow-2xl',
                    confirmButton: 'rounded-xl font-bold px-4 py-2 text-xs',
                    cancelButton: 'rounded-xl font-bold px-4 py-2 text-xs'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/workplan/${this.detailTaskId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(() => {
                        window.location.reload();
                    });
                }
            });
        },

        loadComments(taskId) {
            fetch(`/workplan/${taskId}/comments`)
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        this.comments = data.comments;
                    }
                });
        },

        loadActivities(taskId) {
            fetch(`/workplan/${taskId}/activities`)
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        this.activities = data.activities;
                    }
                });
        },

        submitComment() {
            if (!this.newCommentText.trim() && !this.commentAttachment) return;
            this.submittingComment = true;

            const form = new FormData();
            form.append('comment_text', this.newCommentText || '');
            if (this.commentAttachment && this.commentAttachment.file) {
                form.append('attachment', this.commentAttachment.file);
            }

            fetch(`/workplan/${this.detailTaskId}/comments`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: form
            })
            .then(r => r.json())
            .then(data => {
                this.submittingComment = false;
                if (data.success) {
                    this.comments.push(data.comment);
                    this.newCommentText = '';
                    this.removeAttachment('comment');
                    this.loadActivities(this.detailTaskId);
                    Swal.fire({
                        icon: 'success',
                        title: 'Terkirim',
                        text: 'Komentar berhasil dipublikasikan.',
                        timer: 1500,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.error || 'Gagal mengirim komentar.',
                        confirmButtonColor: '#0F52BA',
                        customClass: { popup: 'rounded-2xl' }
                    });
                }
            })
            .catch(() => {
                this.submittingComment = false;
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Jaringan',
                    text: 'Gagal mengirim komentar.',
                    confirmButtonColor: '#0F52BA',
                    customClass: { popup: 'rounded-2xl' }
                });
            });
        }
    }
}

function confirmDeleteWorkplan(event, message) {
    event.preventDefault();
    const form = event.target.closest('form');
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: message || 'Apakah Anda yakin ingin menghapus data ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e11d48',
        cancelButtonColor: '#94a3b8',
        confirmButtonText: 'Ya, Hapus Permanen',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        customClass: {
            popup: 'rounded-2xl shadow-2xl',
            confirmButton: 'rounded-xl font-bold px-4 py-2 text-xs',
            cancelButton: 'rounded-xl font-bold px-4 py-2 text-xs'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
    return false;
}

@if(session('success'))
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: @json(session('success')),
        timer: 2500,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
    });
});
@endif

@if(session('error'))
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: 'error',
        title: 'Perhatian!',
        text: @json(session('error')),
        confirmButtonColor: '#0F52BA',
        customClass: { popup: 'rounded-2xl' }
    });
});
@endif

function searchableUserFilter(config) {
    return {
        open: false,
        selectedValue: config.selected || 'all',
        search: '',
        users: config.users || [],

        get displayLabel() {
            if (!this.selectedValue || this.selectedValue === 'all') {
                return `Semua Karyawan Inhouse (${this.users.length})`;
            }
            return this.selectedValue;
        },

        get filteredUsers() {
            if (!this.search || !this.search.trim()) {
                return this.users;
            }
            const q = this.search.toLowerCase().trim();
            return this.users.filter(u => (u + '').toLowerCase().includes(q));
        },

        toggleDropdown() {
            this.open = !this.open;
            if (this.open) {
                this.search = '';
                this.$nextTick(() => {
                    if (this.$refs.searchInput) {
                        this.$refs.searchInput.focus();
                    }
                });
            }
        },

        selectUser(val) {
            this.selectedValue = val;
            this.open = false;
            this.search = '';
            document.getElementById('userFilterInput').value = val;
            document.getElementById('userFilterInput').form.submit();
        }
    }
}
</script>
@endpush
