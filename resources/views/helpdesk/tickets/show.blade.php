@extends('layouts.app')

@section('title', 'Tiket ' . $ticket->ticket_number . ' - ' . $ticket->subject)

@section('content')
<div class="space-y-6">
    <!-- HEADER BAR -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-6">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 flex-wrap text-xs text-slate-500 mb-2">
                    <a href="{{ route('helpdesk.index') }}" class="hover:text-primary transition-colors">Helpdesk</a>
                    <span>/</span>
                    <a href="{{ route('helpdesk.tickets.index') }}" class="hover:text-primary transition-colors">Tiket</a>
                    <span>/</span>
                    <span class="font-mono font-bold text-slate-700">{{ $ticket->ticket_number }}</span>
                    <span>•</span>
                    <span class="px-2.5 py-0.5 rounded-full border text-[10px] font-bold {{ $ticket->priority_badge }} uppercase">
                        {{ $ticket->priority }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full border text-[10px] font-bold {{ $ticket->status_badge }}">
                        {{ $ticket->status_label }}
                    </span>
                </div>
                <h1 class="text-xl md:text-2xl font-black text-slate-800 tracking-tight leading-snug">
                    {{ $ticket->subject }}
                </h1>
                <div class="flex items-center gap-4 text-xs text-slate-500 mt-2 flex-wrap">
                    <span><i class="fa-regular fa-user mr-1 text-slate-400"></i>Pengaju: <strong class="text-slate-700">{{ $ticket->creator->name ?? 'User' }}</strong></span>
                    <span><i class="fa-solid fa-layer-group mr-1 text-slate-400"></i>Divisi: <strong class="text-slate-700">{{ $ticket->division->name ?? '-' }}</strong></span>
                    <span><i class="fa-regular fa-calendar mr-1 text-slate-400"></i>Dibuat: {{ $ticket->created_at->format('d M Y H:i') }} WIB</span>
                </div>
            </div>

            <!-- AKSI CEPAT / CLAIM / STATUS -->
            <div class="flex items-center gap-2.5 flex-wrap">
                <!-- LINK KE WORK PLAN JIKA SUDAH TERSINKRONISASI -->
                @if($ticket->workplan_task_id)
                <a href="{{ route('workplan.index') }}?search={{ urlencode($ticket->ticket_number) }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-indigo-50 border border-indigo-200 text-indigo-700 hover:bg-indigo-100 text-xs font-bold transition-all shadow-xs"
                   title="Buka kartu tugas di Work Plan">
                    <i class="fa-solid fa-list-check text-indigo-600"></i>
                    <span>Work Plan: In Progress</span>
                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                </a>
                @endif

                <!-- TOMBOL AMBIL TIKET (JIKA BELUM DI-ASSIGN) -->
                @if(empty($ticket->assigned_to) && $ticket->canBeManagedBy($user))
                <form method="POST" action="{{ route('helpdesk.tickets.claim', $ticket->id) }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-600/20 transition-all">
                        <i class="fa-solid fa-hand-holding-hand"></i>
                        <span>Ambil & Tangani Tiket</span>
                    </button>
                </form>
                @endif

                <!-- DROPDOWN UBAH STATUS (JIKA BERHAK MENGELOLA) -->
                @if($ticket->canBeManagedBy($user))
                <div x-data="{ openStatus: false }" class="relative">
                    <button @click="openStatus = !openStatus" type="button" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all">
                        <i class="fa-solid fa-sliders text-slate-500"></i>
                        <span>Ubah Status</span>
                        <i class="fa-solid fa-chevron-down text-[10px]"></i>
                    </button>
                    <div x-show="openStatus" @click.away="openStatus = false" x-cloak
                         class="absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-xl border border-slate-200/90 py-2 z-30 text-xs">
                        <form method="POST" action="{{ route('helpdesk.tickets.status', $ticket->id) }}">
                            @csrf
                            <button type="submit" name="status" value="open" class="w-full text-left px-4 py-2 hover:bg-slate-50 text-slate-700 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                <span>Menunggu Respon (Open)</span>
                            </button>
                            <button type="submit" name="status" value="in_progress" class="w-full text-left px-4 py-2 hover:bg-slate-50 text-slate-700 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                <span>Sedang Diproses</span>
                            </button>
                            <button type="submit" name="status" value="answered" class="w-full text-left px-4 py-2 hover:bg-slate-50 text-slate-700 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                                <span>Telah Dijawab</span>
                            </button>
                            <button type="submit" name="status" value="resolved" class="w-full text-left px-4 py-2 hover:bg-slate-50 text-slate-700 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                <span>Selesai (Resolved)</span>
                            </button>
                            <button type="submit" name="status" value="closed" class="w-full text-left px-4 py-2 hover:bg-slate-50 text-slate-700 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-slate-500"></span>
                                <span>Tutup Tiket (Closed)</span>
                            </button>
                        </form>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- MAIN TWO COLUMNS -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- LEFT 2 COLS: CHAT ROOM & CONVERSATION -->
        <div class="lg:col-span-2 space-y-6">
            <!-- 1. KENDALA UTAMA (FIRST POST BY CREATOR) -->
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-6">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-extrabold flex items-center justify-center flex-shrink-0 text-sm shadow-md shadow-blue-500/20">
                        {{ strtoupper(substr($ticket->creator->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between gap-2 flex-wrap">
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">{{ $ticket->creator->name ?? 'User' }}</h3>
                                <p class="text-[11px] text-slate-400">{{ $ticket->creator->email ?? '-' }}</p>
                            </div>
                            <span class="text-[11px] text-slate-400 font-medium">
                                {{ $ticket->created_at->format('d M Y, H:i') }} WIB
                            </span>
                        </div>

                        <!-- PESAN KENDALA -->
                        <div class="mt-4 text-xs text-slate-800 leading-relaxed whitespace-pre-line bg-slate-50/70 p-4 rounded-2xl border border-slate-100">
                            {{ $ticket->description }}
                        </div>

                        <!-- LAMPIRAN AWAL JIKA ADA -->
                        @if($ticket->attachment)
                        <div class="mt-4 pt-4 border-t border-slate-100">
                            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-2">
                                <i class="fa-solid fa-paperclip mr-1"></i>Lampiran Kendala:
                            </span>
                            @php
                                $ext = strtolower(pathinfo($ticket->attachment, PATHINFO_EXTENSION));
                                $isImg = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                            @endphp
                            @if($isImg)
                            <a href="{{ asset('storage/' . $ticket->attachment) }}" target="_blank" class="block max-w-sm rounded-xl overflow-hidden border border-slate-200 hover:opacity-90 transition-opacity">
                                <img src="{{ asset('storage/' . $ticket->attachment) }}" alt="Lampiran" class="w-full object-cover max-h-64">
                            </a>
                            @else
                            <a href="{{ asset('storage/' . $ticket->attachment) }}" target="_blank" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors">
                                <i class="fa-regular fa-file-lines text-slate-500"></i>
                                <span>Unduh Lampiran (.{{ $ext }})</span>
                                <i class="fa-solid fa-arrow-down text-[10px]"></i>
                            </a>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- 2. STREAM BALASAN / CHAT (MESSENGER STYLE) -->
            <div class="space-y-4">
                @foreach($ticket->replies as $reply)
                @php
                    $isReplyInternal = (bool)$reply->is_internal;
                    $isMe = ($reply->user_id === $user->id);
                @endphp

                <!-- HANYA TAMPILKAN CATATAN INTERNAL KEPADA AGEN / ADMIN -->
                @if(!$isReplyInternal || $ticket->canBeManagedBy($user))
                <div class="flex items-start gap-3.5 {{ $isReplyInternal ? 'p-4 rounded-3xl bg-amber-50/70 border border-amber-200/90' : 'bg-white p-5 rounded-3xl border border-slate-200/90 shadow-xs' }}">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-xs flex-shrink-0 {{ $isReplyInternal ? 'bg-amber-200 text-amber-800' : ($isMe ? 'bg-blue-600 text-white' : 'bg-slate-200 text-slate-700') }}">
                        @if($isReplyInternal)
                        <i class="fa-solid fa-lock text-[11px]"></i>
                        @else
                        {{ strtoupper(substr($reply->user->name ?? 'U', 0, 1)) }}
                        @endif
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between gap-2 flex-wrap mb-1.5">
                            <div class="flex items-center gap-2">
                                <h4 class="text-xs font-bold text-slate-900">{{ $reply->user->name ?? 'User' }}</h4>
                                @if($isReplyInternal)
                                <span class="px-2 py-0.2 rounded-full bg-amber-200 text-amber-900 text-[9px] font-extrabold flex items-center gap-1">
                                    <i class="fa-solid fa-lock text-[8px]"></i>
                                    <span>CATATAN INTERNAL TIM</span>
                                </span>
                                @elseif($reply->user_id === $ticket->assigned_to)
                                <span class="px-2 py-0.2 rounded-full bg-blue-100 text-blue-800 text-[9px] font-bold">
                                    Petugas Responder
                                </span>
                                @endif
                            </div>
                            <span class="text-[10px] text-slate-400">
                                {{ $reply->created_at->format('d M Y, H:i') }} WIB
                            </span>
                        </div>

                        <!-- ISI BALASAN -->
                        <div class="text-xs text-slate-800 leading-relaxed whitespace-pre-line">
                            {{ $reply->message }}
                        </div>

                        <!-- LAMPIRAN BALASAN -->
                        @if($reply->attachment)
                        <div class="mt-3 pt-3 border-t border-slate-100">
                            @php
                                $rExt = strtolower(pathinfo($reply->attachment, PATHINFO_EXTENSION));
                                $rIsImg = in_array($rExt, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                            @endphp
                            @if($rIsImg)
                            <a href="{{ asset('storage/' . $reply->attachment) }}" target="_blank" class="block max-w-xs rounded-xl overflow-hidden border border-slate-200 hover:opacity-90 transition-opacity">
                                <img src="{{ asset('storage/' . $reply->attachment) }}" alt="Lampiran" class="w-full object-cover max-h-48">
                            </a>
                            @else
                            <a href="{{ asset('storage/' . $reply->attachment) }}" target="_blank" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold">
                                <i class="fa-regular fa-file"></i>
                                <span>Lampiran ({{ $rExt }})</span>
                            </a>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
                @endif
                @endforeach
            </div>

            <!-- 3. KOTAK FORM BALASAN (REPLY BOX) -->
            @if(!$ticket->isClosed() || $user->isAdmin())
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-6" x-data="{ isInternal: false, messageText: '' }">
                <form method="POST" action="{{ route('helpdesk.tickets.reply', $ticket->id) }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <!-- HEADER REPLY BOX & TEMPLATE BALASAN CEPAT -->
                    <div class="flex items-center justify-between gap-3 flex-wrap">
                        <div class="flex items-center gap-2">
                            <!-- TAB BALASAN PUBLIK VS CATATAN INTERNAL -->
                            <button type="button" @click="isInternal = false"
                                    class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all"
                                    :class="!isInternal ? 'bg-primary text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'">
                                <i class="fa-solid fa-reply mr-1"></i>Balasan ke Pengaju
                            </button>
                            @if($ticket->canBeManagedBy($user))
                            <button type="button" @click="isInternal = true"
                                    class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all"
                                    :class="isInternal ? 'bg-amber-500 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'">
                                <i class="fa-solid fa-lock mr-1"></i>Catatan Internal Tim
                            </button>
                            @endif
                        </div>

                        <!-- TEMPLATE BALASAN CEPAT (CANNED RESPONSES) -->
                        @if($cannedResponses->isNotEmpty())
                        <div x-data="{ openCanned: false }" class="relative">
                            <button @click="openCanned = !openCanned" type="button" class="text-xs text-primary font-bold hover:underline flex items-center gap-1">
                                <i class="fa-solid fa-bolt text-amber-500"></i>
                                <span>Template Balasan Cepat</span>
                            </button>
                            <div x-show="openCanned" @click.away="openCanned = false" x-cloak
                                 class="absolute right-0 bottom-full mb-2 w-72 bg-white rounded-2xl shadow-xl border border-slate-200/90 p-2 z-30 max-h-56 overflow-y-auto">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block px-3 py-1">Pilih Balasan Cepat:</span>
                                @foreach($cannedResponses as $canned)
                                <button type="button" @click="messageText = '{{ addslashes($canned->content) }}'; openCanned = false;"
                                        class="w-full text-left px-3 py-2 rounded-lg hover:bg-slate-50 text-xs text-slate-800 transition-colors">
                                    <strong class="block text-slate-900">{{ $canned->title }}</strong>
                                    <span class="text-[11px] text-slate-500 line-clamp-1">{{ $canned->content }}</span>
                                </button>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>

                    <input type="hidden" name="is_internal" :value="isInternal ? '1' : '0'">

                    <!-- NOTIFIKASI CATATAN INTERNAL -->
                    <div x-show="isInternal" x-cloak class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-800 flex items-center gap-2">
                        <i class="fa-solid fa-lock text-amber-600"></i>
                        <span>Pesan ini hanya dapat dibaca oleh sesama rekan tim agen divisi dan Administrator. Pengaju tidak dapat melihat catatan ini.</span>
                    </div>

                    <!-- TEXTAREA PESAN -->
                    <div>
                        <textarea name="message" rows="4" required x-model="messageText"
                                  placeholder="Ketik balasan Anda atau penjelasan progres kendala..."
                                  class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-primary focus:bg-white transition-all leading-relaxed"></textarea>
                    </div>

                    <!-- BOTTOM BAR: ATTACHMENT & SUBMIT -->
                    <div class="flex items-center justify-between gap-3 flex-wrap pt-2">
                        <label class="cursor-pointer inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors">
                            <i class="fa-solid fa-paperclip text-slate-500"></i>
                            <span>Lampirkan Berkas</span>
                            <input type="file" name="attachment" class="hidden"
                                   accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.zip"
                                   onchange="document.getElementById('reply_file_name').innerText = this.files[0] ? this.files[0].name : ''">
                        </label>
                        <span id="reply_file_name" class="text-xs text-slate-500 font-semibold truncate max-w-xs"></span>

                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary hover:bg-primary-600 text-white font-bold text-xs shadow-md shadow-primary/20 transition-all flex items-center gap-2 ml-auto">
                            <i class="fa-solid fa-paper-plane"></i>
                            <span>Kirim Balasan</span>
                        </button>
                    </div>
                </form>
            </div>
            @else
            <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200 text-center text-slate-500 text-xs">
                <i class="fa-solid fa-circle-check text-2xl text-emerald-500 mb-1"></i>
                <p class="font-bold">Tiket ini telah berstatus Selesai / Ditutup.</p>
                <p class="text-slate-400 mt-0.5">Percakapan telah diarsipkan. Jika ada kendala baru, silakan ajukan tiket baru.</p>
            </div>
            @endif
        </div>

        <!-- RIGHT 1 COL: METADATA & SIDEBAR TRACKER -->
        <div class="space-y-6">
            <!-- INFO SLA & STATUS PENYELESAIAN -->
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-5 space-y-4">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Informasi Layanan</h3>

                <div class="space-y-3 text-xs">
                    <!-- STATUS -->
                    <div class="flex items-center justify-between pb-2.5 border-b border-slate-100">
                        <span class="text-slate-500">Status Tiket:</span>
                        <span class="px-2.5 py-0.5 rounded-full border text-[10px] font-bold {{ $ticket->status_badge }}">
                            {{ $ticket->status_label }}
                        </span>
                    </div>

                    <!-- PRIORITAS -->
                    <div class="flex items-center justify-between pb-2.5 border-b border-slate-100">
                        <span class="text-slate-500">Prioritas:</span>
                        <span class="px-2.5 py-0.5 rounded-full border text-[10px] font-bold {{ $ticket->priority_badge }} uppercase">
                            {{ $ticket->priority }}
                        </span>
                    </div>

                    <!-- SLA DUE DATE -->
                    <div class="flex items-center justify-between pb-2.5 border-b border-slate-100">
                        <span class="text-slate-500">Batas Waktu SLA:</span>
                        <span class="font-semibold {{ $ticket->isOverdue() ? 'text-rose-600 font-bold' : 'text-slate-800' }}">
                            {{ $ticket->due_date ? $ticket->due_date->format('d M Y H:i') : '-' }}
                        </span>
                    </div>

                    <!-- RESPON PERTAMA -->
                    <div class="flex items-center justify-between pb-2.5 border-b border-slate-100">
                        <span class="text-slate-500">Respon Pertama:</span>
                        <span class="font-semibold text-slate-800">
                            {{ $ticket->first_response_at ? $ticket->first_response_at->format('d M Y H:i') : 'Menunggu' }}
                        </span>
                    </div>

                    <!-- PETUGAS PENANGGUNG JAWAB -->
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Petugas Responder:</span>
                        <span class="font-bold text-slate-900">
                            {{ $ticket->assignedAgent->name ?? 'Belum Di-assign' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- KARTU INTEGRASI WORK PLAN -->
            <div class="bg-gradient-to-br from-indigo-50 to-blue-50/60 rounded-3xl border border-indigo-200/90 p-5 shadow-sm space-y-3">
                <div class="flex items-center gap-2 text-indigo-900 font-bold text-xs">
                    <div class="w-7 h-7 rounded-lg bg-indigo-600 text-white flex items-center justify-center text-xs">
                        <i class="fa-solid fa-list-check"></i>
                    </div>
                    <span>Integrasi Work Plan (Kanban)</span>
                </div>

                @if($ticket->workplanTask)
                <div class="space-y-2 text-xs">
                    <p class="text-slate-700">Tugas aktif di papan Kanban petugas:</p>
                    <div class="p-3 bg-white rounded-xl border border-indigo-100 space-y-1">
                        <div class="flex items-center justify-between text-[10px]">
                            <span class="font-mono font-bold text-indigo-600">Task #{{ $ticket->workplanTask->id }}</span>
                            <span class="px-2 py-0.2 rounded-full font-bold uppercase {{ $ticket->workplanTask->status === 'done' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ $ticket->workplanTask->status }}
                            </span>
                        </div>
                        <h4 class="text-xs font-bold text-slate-800 line-clamp-2">{{ $ticket->workplanTask->title }}</h4>
                        <p class="text-[10px] text-slate-500">Penanggung Jawab: <strong>{{ $ticket->workplanTask->assignee }}</strong></p>
                    </div>
                    <a href="{{ route('workplan.index') }}?search={{ urlencode($ticket->ticket_number) }}" class="inline-flex items-center gap-1.5 text-xs text-indigo-700 font-bold hover:underline mt-1">
                        <span>Buka di Kanban Board</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
                @else
                <p class="text-[11px] text-slate-600 leading-relaxed">
                    Saat petugas merespon tiket ini, kartu tugas akan otomatis dibuat di papan Kanban petugas pada kolom <strong>Progress</strong>.
                </p>
                @endif
            </div>

            <!-- AUDIT TRAIL TIMELINE -->
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-5 space-y-3">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Riwayat Tiket (Audit Trail)</h3>
                <div class="space-y-3 text-xs max-h-72 overflow-y-auto pr-1">
                    @forelse($ticket->logs as $log)
                    <div class="flex items-start gap-2.5 pb-2.5 border-b border-slate-100 last:border-b-0">
                        <div class="w-5 h-5 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center text-[9px] flex-shrink-0 mt-0.5">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-slate-800 font-semibold leading-tight">{{ $log->details }}</p>
                            <span class="text-[10px] text-slate-400">{{ $log->created_at->format('d M H:i') }}</span>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-slate-400">Belum ada riwayat.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
