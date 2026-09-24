@extends('layouts.app')

@section('title', 'Tiket ' . $ticket->ticket_number . ' - ' . $ticket->subject)

@section('content')
<div class="space-y-6" x-data="{
    previewModalOpen: false,
    previewItem: {
        url: '',
        downloadUrl: '',
        filename: '',
        ext: '',
        isImage: false,
        isPdf: false,
        isDoc: false,
        icon: '',
        badgeColor: ''
    },
    openPreview(item) {
        this.previewItem = item;
        this.previewModalOpen = true;
    },
    closePreview() {
        this.previewModalOpen = false;
    }
}" @keydown.escape.window="closePreview()">
    <!-- HEADER BAR -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-6">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 flex-wrap text-xs text-slate-500 mb-2">
                    <a href="{{ route('helpdesk.index') }}" class="hover:text-primary transition-colors">Helpdesk</a>
                    <span>/</span>
                    <a href="{{ route('helpdesk.tickets.index') }}" class="hover:text-primary transition-colors">
                        @if(auth()->check() && auth()->user()->isHelpdeskRegularUser())
                            Tiket Saya
                        @elseif(auth()->check() && auth()->user()->isHelpdeskDivisionUser())
                            Tiket Divisi
                        @else
                            Tiket
                        @endif
                    </a>
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

                        <!-- LAMPIRAN AWAL (THUMBNAIL KECIL DENGAN MODAL PREVIEW) -->
                        @if($ticket->has_attachments)
                        <div class="mt-4 pt-4 border-t border-slate-100">
                            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-2.5">
                                <i class="fa-solid fa-paperclip mr-1"></i>Lampiran Kendala ({{ count($ticket->attachments_details) }} Berkas):
                            </span>
                            <div class="flex flex-wrap items-center gap-3">
                                @foreach($ticket->attachments_details as $att)
                                <div class="group relative flex flex-col p-2.5 rounded-2xl border border-slate-200 bg-slate-50/70 hover:bg-white hover:border-primary/50 hover:shadow-md transition-all w-32 sm:w-36 text-center">
                                    <!-- THUMBNAIL BOX -->
                                    <div class="relative w-full h-20 rounded-xl overflow-hidden bg-slate-100 flex items-center justify-center border border-slate-200/60 mb-2 cursor-pointer"
                                         @click="openPreview({
                                             url: '{{ $att['url'] }}',
                                             downloadUrl: '{{ $att['download_url'] }}',
                                             filename: '{{ addslashes($att['filename']) }}',
                                             ext: '{{ $att['ext'] }}',
                                             isImage: {{ $att['is_image'] ? 'true' : 'false' }},
                                             isPdf: {{ $att['is_pdf'] ? 'true' : 'false' }},
                                             isDoc: {{ $att['is_doc'] ? 'true' : 'false' }},
                                             icon: '{{ $att['icon'] }}',
                                             badgeColor: '{{ $att['badge_color'] }}'
                                         })">
                                        @if($att['is_image'])
                                        <img src="{{ $att['url'] }}" alt="{{ $att['filename'] }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                                        <div class="absolute inset-0 bg-slate-900/30 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity text-white text-xs">
                                            <i class="fa-solid fa-magnifying-glass-plus text-sm"></i>
                                        </div>
                                        @else
                                        <div class="flex flex-col items-center justify-center gap-1">
                                            <i class="{{ $att['icon'] }} text-2xl"></i>
                                            <span class="text-[9px] font-mono font-bold uppercase text-slate-500">.{{ $att['ext'] }}</span>
                                        </div>
                                        <div class="absolute inset-0 bg-slate-900/30 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity text-white text-xs">
                                            <i class="fa-solid fa-eye text-sm"></i>
                                        </div>
                                        @endif
                                    </div>

                                    <!-- NAMA FILE -->
                                    <p class="text-[11px] font-bold text-slate-800 truncate w-full" title="{{ $att['filename'] }}">
                                        {{ $att['filename'] }}
                                    </p>

                                    <!-- TOMBOL PREVIEW & UNDUH -->
                                    <div class="flex items-center justify-center gap-1.5 mt-1.5 pt-1.5 border-t border-slate-100 text-[10px]">
                                        <button type="button" 
                                                @click="openPreview({
                                                    url: '{{ $att['url'] }}',
                                                    downloadUrl: '{{ $att['download_url'] }}',
                                                    filename: '{{ addslashes($att['filename']) }}',
                                                    ext: '{{ $att['ext'] }}',
                                                    isImage: {{ $att['is_image'] ? 'true' : 'false' }},
                                                    isPdf: {{ $att['is_pdf'] ? 'true' : 'false' }},
                                                    isDoc: {{ $att['is_doc'] ? 'true' : 'false' }},
                                                    icon: '{{ $att['icon'] }}',
                                                    badgeColor: '{{ $att['badge_color'] }}'
                                                })"
                                                class="px-2 py-0.5 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white font-bold transition-all flex items-center gap-1">
                                            <i class="fa-solid fa-eye text-[9px]"></i>
                                            <span>Preview</span>
                                        </button>
                                        <a href="{{ $att['download_url'] }}" class="p-1 rounded-lg text-slate-400 hover:text-slate-700 transition-colors" title="Unduh Berkas">
                                            <i class="fa-solid fa-download text-[10px]"></i>
                                        </a>
                                    </div>
                                </div>
                                @endforeach
                            </div>
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

                        <!-- LAMPIRAN BALASAN (THUMBNAIL KECIL DENGAN MODAL PREVIEW) -->
                        @if($reply->has_attachments)
                        <div class="mt-3 pt-3 border-t border-slate-100">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">
                                <i class="fa-solid fa-paperclip mr-1"></i>Lampiran ({{ count($reply->attachments_details) }}):
                            </span>
                            <div class="flex flex-wrap items-center gap-2.5">
                                @foreach($reply->attachments_details as $rAtt)
                                <div class="group relative flex flex-col p-2 rounded-xl border border-slate-200 bg-slate-50/70 hover:bg-white hover:border-primary/50 hover:shadow-sm transition-all w-28 text-center">
                                    <div class="relative w-full h-16 rounded-lg overflow-hidden bg-slate-100 flex items-center justify-center border border-slate-200/60 mb-1.5 cursor-pointer"
                                         @click="openPreview({
                                             url: '{{ $rAtt['url'] }}',
                                             downloadUrl: '{{ $rAtt['download_url'] }}',
                                             filename: '{{ addslashes($rAtt['filename']) }}',
                                             ext: '{{ $rAtt['ext'] }}',
                                             isImage: {{ $rAtt['is_image'] ? 'true' : 'false' }},
                                             isPdf: {{ $rAtt['is_pdf'] ? 'true' : 'false' }},
                                             isDoc: {{ $rAtt['is_doc'] ? 'true' : 'false' }},
                                             icon: '{{ $rAtt['icon'] }}',
                                             badgeColor: '{{ $rAtt['badge_color'] }}'
                                         })">
                                        @if($rAtt['is_image'])
                                        <img src="{{ $rAtt['url'] }}" alt="{{ $rAtt['filename'] }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                                        <div class="absolute inset-0 bg-slate-900/30 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity text-white text-xs">
                                            <i class="fa-solid fa-magnifying-glass-plus text-xs"></i>
                                        </div>
                                        @else
                                        <div class="flex flex-col items-center justify-center gap-0.5">
                                            <i class="{{ $rAtt['icon'] }} text-lg"></i>
                                            <span class="text-[8px] font-mono font-bold uppercase text-slate-500">.{{ $rAtt['ext'] }}</span>
                                        </div>
                                        <div class="absolute inset-0 bg-slate-900/30 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity text-white text-xs">
                                            <i class="fa-solid fa-eye text-xs"></i>
                                        </div>
                                        @endif
                                    </div>

                                    <p class="text-[10px] font-bold text-slate-800 truncate w-full" title="{{ $rAtt['filename'] }}">
                                        {{ $rAtt['filename'] }}
                                    </p>

                                    <div class="flex items-center justify-center gap-1 mt-1 pt-1 border-t border-slate-100 text-[9px]">
                                        <button type="button" 
                                                @click="openPreview({
                                                    url: '{{ $rAtt['url'] }}',
                                                    downloadUrl: '{{ $rAtt['download_url'] }}',
                                                    filename: '{{ addslashes($rAtt['filename']) }}',
                                                    ext: '{{ $rAtt['ext'] }}',
                                                    isImage: {{ $rAtt['is_image'] ? 'true' : 'false' }},
                                                    isPdf: {{ $rAtt['is_pdf'] ? 'true' : 'false' }},
                                                    isDoc: {{ $rAtt['is_doc'] ? 'true' : 'false' }},
                                                    icon: '{{ $rAtt['icon'] }}',
                                                    badgeColor: '{{ $rAtt['badge_color'] }}'
                                                })"
                                                class="px-1.5 py-0.5 rounded bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white font-bold transition-all flex items-center gap-1">
                                            <i class="fa-solid fa-eye text-[8px]"></i>
                                            <span>Preview</span>
                                        </button>
                                        <a href="{{ $rAtt['download_url'] }}" class="p-0.5 rounded text-slate-400 hover:text-slate-700 transition-colors" title="Unduh Berkas">
                                            <i class="fa-solid fa-download text-[9px]"></i>
                                        </a>
                                    </div>
                                </div>
                                @endforeach
                            </div>
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
                        @if($ticket->canBeManagedBy($user) && $cannedResponses->isNotEmpty())
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
                    <div class="space-y-2 pt-2" x-data="{
                        replyFiles: [],
                        handleReplyFiles(e) {
                            const selected = Array.from(e.target.files);
                            selected.forEach(file => {
                                if (!this.replyFiles.some(f => f.name === file.name && f.size === file.size)) {
                                    this.replyFiles.push(file);
                                }
                            });
                            this.syncReplyInput();
                        },
                        removeReplyFile(idx) {
                            this.replyFiles.splice(idx, 1);
                            this.syncReplyInput();
                        },
                        syncReplyInput() {
                            const dt = new DataTransfer();
                            this.replyFiles.forEach(f => dt.items.add(f));
                            document.getElementById('reply_attachments_input').files = dt.files;
                        }
                    }">
                        <div class="flex items-center justify-between gap-3 flex-wrap">
                            <label class="cursor-pointer inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors">
                                <i class="fa-solid fa-paperclip text-slate-500"></i>
                                <span>Lampirkan Berkas (Multiple)</span>
                                <input type="file" name="attachments[]" id="reply_attachments_input" class="hidden" multiple
                                       accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip"
                                       @change="handleReplyFiles($event)">
                            </label>

                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary hover:bg-primary-600 text-white font-bold text-xs shadow-md shadow-primary/20 transition-all flex items-center gap-2 ml-auto">
                                <i class="fa-solid fa-paper-plane"></i>
                                <span>Kirim Balasan</span>
                            </button>
                        </div>

                        <!-- DAFTAR BERKAS TERPILIH UNTUK BALASAN -->
                        <div x-show="replyFiles.length > 0" x-cloak class="flex flex-wrap gap-1.5 pt-1">
                            <template x-for="(rf, rIdx) in replyFiles" :key="rIdx">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-blue-50 border border-blue-200 text-blue-800 text-[11px] font-medium">
                                    <i class="fa-solid fa-paperclip text-[10px] text-blue-500"></i>
                                    <span class="truncate max-w-[160px]" x-text="rf.name"></span>
                                    <button type="button" @click="removeReplyFile(rIdx)" class="text-blue-400 hover:text-rose-600" title="Hapus">
                                        <i class="fa-solid fa-xmark text-xs"></i>
                                    </button>
                                </span>
                            </template>
                        </div>
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

    <!-- ========================================== -->
    <!-- PREVIEW MODAL ATTACHMENT (IN-PAGE DIALOG) -->
    <!-- ========================================== -->
    <div x-cloak
         x-show="previewModalOpen"
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true"
         @keydown.escape.window="closePreview()">
        
        <!-- BACKDROP -->
        <div x-show="previewModalOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm transition-opacity"
             @click="closePreview()"></div>

        <!-- MODAL DIALOG CONTAINER -->
        <div class="min-h-full flex items-center justify-center p-3 sm:p-6 text-center">
            <div x-show="previewModalOpen"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 @click.stop
                 class="relative bg-white rounded-3xl text-left overflow-hidden shadow-2xl border border-slate-200/80 transform transition-all w-full max-w-4xl flex flex-col max-h-[92vh]">
                
                <!-- MODAL HEADER -->
                <div class="px-5 py-3.5 bg-slate-50/90 border-b border-slate-100 flex items-center justify-between gap-3 flex-shrink-0">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center text-sm shadow-sm flex-shrink-0" :class="previewItem.badgeColor || 'bg-slate-200 text-slate-700'">
                            <i :class="previewItem.icon || 'fa-solid fa-file'"></i>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-xs sm:text-sm font-bold text-slate-800 truncate" id="modal-title" x-text="previewItem.filename || 'Lampiran Berkas'"></h3>
                            <div class="flex items-center gap-2 text-[10px] text-slate-400">
                                <span>Format: <strong class="uppercase text-slate-600 font-semibold" x-text="previewItem.ext"></strong></span>
                                <span>&bull;</span>
                                <span>Preview Lampiran</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 flex-shrink-0">
                        <!-- TOMBOL UNDUH -->
                        <a :href="previewItem.downloadUrl" 
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-200/80 hover:bg-slate-300 text-slate-700 text-xs font-semibold transition" 
                           title="Unduh Berkas Ini">
                            <i class="fa-solid fa-download text-xs"></i>
                            <span class="hidden sm:inline">Unduh</span>
                        </a>

                        <!-- BUKA DI TAB BARU (OPSIONAL) -->
                        <a :href="previewItem.url" 
                           target="_blank" 
                           class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-slate-200/80 hover:bg-slate-300 text-slate-700 text-xs font-semibold transition" 
                           title="Buka File di Tab Baru">
                            <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                        </a>

                        <!-- TOMBOL TUTUP -->
                        <button type="button" 
                                @click="closePreview()" 
                                class="w-8 h-8 rounded-xl bg-slate-200/80 hover:bg-rose-100 hover:text-rose-600 text-slate-600 flex items-center justify-center transition" 
                                title="Tutup Preview (Esc)">
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>
                </div>

                <!-- MODAL BODY -->
                <div class="flex-1 overflow-auto bg-slate-900/5 relative flex items-center justify-center p-3 sm:p-5">
                    
                    <!-- 1. TAMPILAN GAMBAR -->
                    <template x-if="previewItem.isImage">
                        <div class="flex flex-col items-center justify-center w-full h-full min-h-[300px] max-h-[76vh]">
                            <img :src="previewItem.url" 
                                 :alt="previewItem.filename" 
                                 class="max-h-[72vh] max-w-full rounded-2xl object-contain shadow-lg border border-slate-200/60 bg-white" />
                        </div>
                    </template>

                    <!-- 2. TAMPILAN PDF -->
                    <template x-if="previewItem.isPdf">
                        <div class="w-full h-[74vh] rounded-2xl overflow-hidden shadow-inner border border-slate-200 bg-white">
                            <iframe :src="previewItem.url" class="w-full h-full border-0"></iframe>
                        </div>
                    </template>

                    <!-- 3. TAMPILAN DOKUMEN OFFICE / ZIP / LAINNYA -->
                    <template x-if="!previewItem.isImage && !previewItem.isPdf">
                        <div class="w-full max-w-lg py-12 px-6 flex flex-col items-center justify-center text-center bg-white rounded-3xl shadow-sm border border-slate-200/80 my-4">
                            <div class="w-20 h-20 rounded-3xl flex items-center justify-center text-4xl mb-4 shadow-sm" :class="previewItem.badgeColor || 'bg-slate-100 text-slate-600'">
                                <i :class="previewItem.icon || 'fa-solid fa-file-lines'"></i>
                            </div>
                            <h4 class="text-sm sm:text-base font-bold text-slate-900 mb-1.5 break-all max-w-full" x-text="previewItem.filename"></h4>
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-slate-100 text-[10px] font-mono font-bold text-slate-600 uppercase mb-4">
                                Ekstensi .<span x-text="previewItem.ext"></span>
                            </div>
                            <p class="text-xs text-slate-500 max-w-sm mb-6 leading-relaxed">
                                Dokumen <strong class="text-slate-700" x-text="previewItem.filename"></strong> tidak dapat dirender secara interaktif langsung di browser. Silakan unduh dokumen untuk membuka di aplikasi terkait (Word / Excel / PPT / Reader).
                            </p>
                            <a :href="previewItem.downloadUrl" 
                               class="inline-flex items-center gap-2 px-6 py-2.5 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-lg shadow-indigo-200 transition-all hover:scale-[1.02] active:scale-[0.98]">
                                <i class="fa-solid fa-cloud-arrow-down text-sm"></i>
                                <span>Unduh Dokumen Sekarang</span>
                            </a>
                        </div>
                    </template>
                </div>

                <!-- MODAL FOOTER -->
                <div class="px-5 py-2.5 bg-white border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400 flex-shrink-0">
                    <span>Tekan tombol <strong>Esc</strong> atau klik di luar kotak untuk menutup preview.</span>
                    <button type="button" @click="closePreview()" class="text-slate-600 hover:text-slate-900 font-semibold">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
