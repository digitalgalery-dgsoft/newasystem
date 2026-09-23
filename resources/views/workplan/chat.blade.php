@extends('layouts.app')

@section('title', 'Groups Chat - Work Plan & ToDoList')

@section('content')
<div class="space-y-4" x-data="wpGroupChat()" x-init="initChat()">

    <!-- Top Breadcrumb & Actions Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white px-5 py-3.5 rounded-2xl border border-slate-200/80 shadow-sm">
        <div class="flex items-center gap-3">
            <a href="{{ route('workplan.index') }}" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition-colors" title="Kembali ke Kanban Board">
                <i class="fa-solid fa-arrow-left text-sm"></i>
            </a>
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white flex items-center justify-center text-xl shadow-md shadow-emerald-500/25 flex-shrink-0">
                <i class="fa-solid fa-comments"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-base font-bold text-slate-900 tracking-tight">Groups Chat</h1>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        REAL-TIME
                    </span>
                </div>
                <p class="text-[11px] text-slate-500">
                    Kolaborasi pesan grup langsung antar personil inhouse ESA Groups.
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" @click.stop="openCreateGroupModal()" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-xs font-bold transition-all shadow-md shadow-emerald-600/20">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Buat Group Baru</span>
            </button>
            <a href="{{ route('workplan.index') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-all">
                <i class="fa-solid fa-table-columns text-xs"></i>
                <span>Kanban Board</span>
            </a>
        </div>
    </div>

    <!-- MAIN WHATSAPP WEB CONTAINER -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xl overflow-hidden flex flex-col md:flex-row h-[calc(100vh-165px)] min-h-[580px]">

        <!-- ================================================================= -->
        <!-- LEFT SIDEBAR: DAFTAR GROUP CHAT -->
        <!-- ================================================================= -->
        <div class="w-full md:w-[360px] lg:w-[400px] border-r border-slate-200/90 flex flex-col flex-shrink-0 bg-slate-50/50">
            <!-- Sidebar Header -->
            <div class="p-3.5 bg-slate-100/90 border-b border-slate-200 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-600 to-primary-600 text-white font-black text-xs flex items-center justify-center shadow-sm">
                        {{ strtoupper(substr($userName, 0, 2)) }}
                    </div>
                    <div class="min-w-0">
                        <div class="text-xs font-bold text-slate-800 truncate">{{ $userName }}</div>
                        <div class="text-[10px] text-emerald-600 font-medium flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Online
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-1">
                    <button type="button" @click.stop="openCreateGroupModal()" class="w-8 h-8 rounded-lg hover:bg-slate-200 text-slate-600 flex items-center justify-center transition-colors" title="Buat Group Baru">
                        <i class="fa-solid fa-user-group text-xs"></i>
                    </button>
                    <button type="button" @click="pollGroups(true)" class="w-8 h-8 rounded-lg hover:bg-slate-200 text-slate-600 flex items-center justify-center transition-colors" title="Refresh Daftar Group">
                        <i class="fa-solid fa-arrows-rotate text-xs" :class="{'animate-spin': isRefreshingGroups}"></i>
                    </button>
                </div>
            </div>

            <!-- Search Group Bar -->
            <div class="p-2.5 bg-white border-b border-slate-200">
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" x-model="groupSearch" placeholder="Cari atau mulai chat baru..." class="w-full pl-9 pr-8 py-1.5 text-xs bg-slate-100 hover:bg-slate-200/70 focus:bg-white rounded-xl border border-transparent focus:border-emerald-500 focus:outline-none transition-all placeholder:text-slate-400">
                    <button x-show="groupSearch" @click="groupSearch = ''" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>

            <!-- Group List Stream -->
            <div class="flex-1 overflow-y-auto divide-y divide-slate-100 bg-white" id="groupListContainer">
                <template x-for="g in filteredGroups" :key="g.id">
                    <div @click="selectGroup(g.id)"
                         class="flex items-center gap-3 px-3.5 py-3 cursor-pointer transition-all hover:bg-slate-50 relative"
                         :class="activeGroupId == g.id ? 'bg-emerald-50/80 border-l-4 border-emerald-600' : ''">
                        <!-- Group Avatar -->
                        <div class="w-11 h-11 rounded-full text-white font-extrabold text-xs flex items-center justify-center shadow-sm flex-shrink-0"
                             :style="'background-color: ' + (g.avatar_color || '#10b981')">
                            <span x-text="g.initials"></span>
                        </div>

                        <!-- Info -->
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between gap-1 mb-0.5">
                                <span class="text-xs font-bold text-slate-900 truncate" x-text="g.name"></span>
                                <span class="text-[10px] text-slate-400 flex-shrink-0" x-text="g.latest_message ? g.latest_message.time : ''"></span>
                            </div>
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-[11px] text-slate-500 truncate" x-html="formatLastMessageSnippet(g)"></p>
                                <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-slate-100 text-slate-500 font-semibold flex-shrink-0" title="Jumlah Anggota">
                                    <i class="fa-solid fa-user text-[9px] mr-0.5"></i><span x-text="g.member_count"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Empty State jika tidak ada grup -->
                <div x-show="filteredGroups.length === 0" class="p-8 text-center">
                    <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-3 text-xl">
                        <i class="fa-regular fa-comments"></i>
                    </div>
                    <div class="text-xs font-bold text-slate-700">Tidak ada group ditemukan</div>
                    <p class="text-[11px] text-slate-400 mt-1 max-w-xs mx-auto">
                        Klik tombol di atas untuk membuat group baru dan menambahkan anggota tim.
                    </p>
                    <button type="button" @click.stop="openCreateGroupModal()" class="mt-3.5 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-bold shadow-sm">
                        <i class="fa-solid fa-plus text-[10px]"></i>
                        <span>Buat Group Pertama</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- ================================================================= -->
        <!-- RIGHT PANEL: CHAT ACTIVE GROUP / WELCOME SCREEN -->
        <!-- ================================================================= -->
        <div class="flex-1 flex flex-col bg-[#efeae2] relative min-w-0">

            @if($activeGroup)
            <!-- 1. CHAT HEADER -->
            <div class="p-3 bg-white/95 backdrop-blur border-b border-slate-200/90 flex items-center justify-between gap-3 shadow-xs z-10 flex-shrink-0">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-full text-white font-extrabold text-xs flex items-center justify-center shadow-sm flex-shrink-0"
                         style="background-color: {{ $activeGroup->avatar_color ?: '#10b981' }}">
                        {{ $activeGroup->initials }}
                    </div>
                    <div class="min-w-0 cursor-pointer" @click.stop="openViewMembersModal()">
                        <div class="text-xs md:text-sm font-bold text-slate-900 truncate hover:text-emerald-700 transition-colors flex items-center gap-1.5">
                            <span>{{ $activeGroup->name }}</span>
                            <i class="fa-solid fa-circle-info text-[11px] text-slate-400"></i>
                        </div>
                        <div class="text-[10px] md:text-[11px] text-slate-500 truncate" title="{{ $activeGroup->members->pluck('user_name')->implode(', ') }}">
                            <span class="font-semibold text-emerald-700">{{ $activeGroup->members->count() }} Anggota:</span>
                            {{ $activeGroup->members->take(5)->pluck('user_name')->implode(', ') }}
                            @if($activeGroup->members->count() > 5)
                                <span class="text-slate-400 font-medium">+{{ $activeGroup->members->count() - 5 }} lainnya</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 flex-shrink-0">
                    <button type="button" @click.stop="openAddMemberModal()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-emerald-800 text-xs font-bold transition-all shadow-xs" title="Tambah Personel ke Group Ini">
                        <i class="fa-solid fa-user-plus text-[11px] text-emerald-600"></i>
                        <span class="hidden sm:inline">Tambah Anggota</span>
                    </button>
                    <button type="button" @click.stop="openViewMembersModal()" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition-colors" title="Lihat Daftar Anggota">
                        <i class="fa-solid fa-users text-xs"></i>
                    </button>
                </div>
            </div>

            @if($isGroupMember)
            <!-- 2. CHAT STREAM (WHATSAPP BACKGROUND & BUBBLES) -->
            <div class="flex-1 overflow-y-auto p-4 space-y-3 relative" id="chatMessageStream" style="background-color: #efeae2; background-image: radial-gradient(#d1d5db 0.75px, transparent 0.75px); background-size: 16px 16px;">

                <!-- Notice WA E2E Encryption Style -->
                <div class="flex justify-center my-1">
                    <div class="px-3.5 py-1.5 rounded-xl bg-amber-100/80 border border-amber-200 text-[10px] text-amber-800 text-center max-w-md shadow-2xs leading-relaxed">
                        <i class="fa-solid fa-lock text-[9px] mr-1"></i> Pesan antar personil terenkripsi internal dan tersimpan secara real-time di sistem Work Plan ESA Groups.
                    </div>
                </div>

                <!-- Loop Messages -->
                <template x-for="(msg, index) in messages" :key="msg.id">
                    <div>
                        <!-- System Message Pill -->
                        <div x-show="msg.is_system" class="flex justify-center my-1.5">
                            <div class="px-3 py-1 rounded-lg bg-slate-200/90 text-slate-700 text-[10px] font-medium shadow-2xs text-center max-w-sm" x-text="msg.message_text"></div>
                        </div>

                        <!-- Chat Bubble (User vs Others) with Profile Avatar -->
                        <div x-show="!msg.is_system" class="flex items-end gap-2 my-1" :class="msg.is_me ? 'justify-end' : 'justify-start'">
                            
                            <!-- Avatar Pengirim (Pesan Masuk / Orang Lain - Kiri) -->
                            <template x-if="!msg.is_me">
                                <div class="flex-shrink-0 mb-0.5" :title="msg.user_sender">
                                    <img :src="msg.sender_avatar || ('https://ui-avatars.com/api/?name=' + encodeURIComponent(msg.user_sender) + '&background=0F52BA&color=fff&size=64&bold=true')"
                                         :alt="msg.user_sender"
                                         class="w-7 h-7 sm:w-8 sm:h-8 rounded-full object-cover border border-slate-200 shadow-2xs ring-1 ring-white/80"
                                         x-on:error="$event.target.src = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(msg.user_sender) + '&background=0F52BA&color=fff&size=64&bold=true'">
                                </div>
                            </template>

                            <div class="max-w-[85%] sm:max-w-[70%] rounded-2xl px-3.5 py-2 shadow-xs relative text-slate-800 text-xs leading-relaxed break-words"
                                 :class="msg.is_me ? 'bg-[#d9fdd3] rounded-tr-xs border border-emerald-200/60' : 'bg-white rounded-tl-xs border border-slate-200/80'">
                                
                                <!-- Sender Name (Only for others) -->
                                <div x-show="!msg.is_me" class="text-[11px] font-extrabold mb-0.5"
                                     :style="'color: ' + getSenderColor(msg.user_sender)"
                                     x-text="msg.user_sender">
                                </div>

                                <!-- Text Message -->
                                <div class="whitespace-pre-wrap select-text text-slate-800 text-xs" x-html="formatMessageText(msg.message_text)"></div>

                                <!-- Time & Checkmark -->
                                <div class="flex items-center justify-end gap-1 mt-1 text-[9px] text-slate-400 select-none">
                                    <span x-text="msg.time"></span>
                                    <span x-show="msg.is_me" class="text-sky-600 font-bold ml-0.5" title="Terkirim">
                                        <i class="fa-solid fa-check-double text-[10px]"></i>
                                    </span>
                                </div>
                            </div>

                            <!-- Avatar Pengirim (Pesan Keluar / User Sendiri - Kanan) -->
                            <template x-if="msg.is_me">
                                <div class="flex-shrink-0 mb-0.5" title="Anda">
                                    <img :src="msg.sender_avatar || ('https://ui-avatars.com/api/?name=' + encodeURIComponent(userName) + '&background=059669&color=fff&size=64&bold=true')"
                                         :alt="userName"
                                         class="w-7 h-7 sm:w-8 sm:h-8 rounded-full object-cover border border-emerald-300 shadow-2xs ring-1 ring-white/80"
                                         x-on:error="$event.target.src = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(userName) + '&background=059669&color=fff&size=64&bold=true'">
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                <!-- Indicator saat pesan kosong -->
                <div x-show="messages.length === 0" class="flex flex-col items-center justify-center h-48 text-slate-400">
                    <div class="w-12 h-12 rounded-full bg-white/80 flex items-center justify-center text-slate-400 mb-2 shadow-xs">
                        <i class="fa-regular fa-comment-dots text-xl"></i>
                    </div>
                    <div class="text-xs font-semibold text-slate-600">Belum ada pesan di group ini</div>
                    <div class="text-[11px] text-slate-400">Jadilah yang pertama mengirimkan sapaan ke tim!</div>
                </div>

                <div id="streamBottomAnchor"></div>
            </div>

            <!-- 3. CHAT FOOTER / INPUT BAR -->
            <div class="p-3 bg-[#f0f2f5] border-t border-slate-300/80 flex items-end gap-2 flex-shrink-0">
                <div class="flex-1 relative">
                    <textarea x-model="inputMessage"
                              @keydown.enter.exact.prevent="sendChatMessage()"
                              @keydown.ctrl.enter="inputMessage += '\n'"
                              rows="1"
                              placeholder="Ketik pesan... (Tekan Enter untuk kirim, Ctrl+Enter untuk baris baru)"
                              class="w-full px-4 py-2.5 rounded-2xl bg-white border border-slate-200 focus:border-emerald-500 focus:outline-none text-xs text-slate-800 placeholder:text-slate-400 resize-none shadow-xs transition-all max-h-32 leading-relaxed"
                              :disabled="isSendingMessage"></textarea>
                </div>

                <button type="button"
                        @click="sendChatMessage()"
                        :disabled="isSendingMessage || !inputMessage.trim()"
                        class="w-10 h-10 rounded-full bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 disabled:opacity-50 disabled:cursor-not-allowed text-white flex items-center justify-center shadow-md shadow-emerald-600/30 transition-all flex-shrink-0"
                        title="Kirim Pesan">
                    <i class="fa-solid fa-paper-plane text-xs" :class="{'animate-pulse': isSendingMessage}"></i>
                </button>
            </div>
            @else
            <!-- 2B. ENCRYPTED / MEMBERS-ONLY RESTRICTION SCREEN (ADMIN / NON-MEMBER) -->
            <div class="flex-1 flex flex-col items-center justify-center p-6 text-center bg-[#efeae2]" style="background-image: radial-gradient(#d1d5db 0.75px, transparent 0.75px); background-size: 16px 16px;">
                <div class="max-w-md w-full bg-white/95 backdrop-blur-md rounded-3xl p-7 border border-slate-200/90 shadow-2xl space-y-4">
                    <div class="w-16 h-16 rounded-2xl bg-amber-50 border border-amber-200 text-amber-600 flex items-center justify-center text-3xl mx-auto shadow-sm">
                        <i class="fa-solid fa-lock"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-800 tracking-tight">Percakapan Khusus Anggota Group</h3>
                        <div class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full bg-amber-50 border border-amber-200 text-amber-800 text-[11px] font-bold mt-2">
                            <i class="fa-solid fa-shield-halved text-amber-600 text-xs"></i>
                            <span>Privasi & Kerahasiaan Tim</span>
                        </div>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Isi percakapan dan pesan di dalam group <b>{{ $activeGroup->name }}</b> hanya dapat dibaca dan dikirim oleh anggota resmi group.
                        @if($isAdmin)
                        <span class="block mt-2 text-slate-500 text-[11px]">Sebagai Administrator, Anda dapat memantau daftar group dan melihat personil anggotanya melalui tombol di bawah.</span>
                        @endif
                    </p>
                    <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-2">
                        <button type="button" @click.stop="openViewMembersModal()" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold transition-all shadow-md">
                            <i class="fa-solid fa-users text-xs"></i>
                            <span>Lihat Anggota ({{ $activeGroup->members->count() }})</span>
                        </button>
                        <button type="button" @click.stop="openAddMemberModal()" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-all shadow-md shadow-emerald-600/20">
                            <i class="fa-solid fa-user-plus text-xs"></i>
                            <span>Kelola Anggota</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- 3B. FOOTER TERKUNCI -->
            <div class="p-3.5 bg-slate-100/90 border-t border-slate-200 text-center text-xs text-slate-500 flex items-center justify-center gap-2 flex-shrink-0">
                <i class="fa-solid fa-lock text-slate-400 text-xs"></i>
                <span class="font-medium text-slate-600">Anda bukan anggota group ini. Akses pesan dan pengiriman dinonaktifkan.</span>
            </div>
            @endif

            @else
            <!-- EMPTY STATE: WELCOME SCREEN (WA WEB STYLE) -->
            <div class="flex-1 flex flex-col items-center justify-center p-8 text-center bg-slate-50/70">
                <div class="w-20 h-20 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-4xl shadow-inner mb-4">
                    <i class="fa-solid fa-comments"></i>
                </div>
                <h2 class="text-xl font-black text-slate-800 tracking-tight">Work Plan Groups Chat</h2>
                <p class="text-xs text-slate-500 mt-2 max-w-md leading-relaxed">
                    Kirim dan terima pesan real-time antar karyawan inhouse. Diskusikan tugas, koordinasikan proyek harian, dan pantau progres kerja tim tanpa perlu membuka aplikasi luar.
                </p>
                <div class="mt-6 flex items-center gap-3">
                    <button type="button" @click.stop="openCreateGroupModal()" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 transition-all">
                        <i class="fa-solid fa-plus text-xs"></i>
                        <span>Buat Group Chat Baru</span>
                    </button>
                </div>
                <div class="mt-8 text-[11px] text-slate-400 flex items-center gap-1.5">
                    <i class="fa-solid fa-lock text-[10px]"></i> Dilindungi protokol database internal ESA Groups
                </div>
            </div>
            @endif

        </div>

    </div>

    <!-- ================================================================= -->
    <!-- MODAL: BUAT GROUP BARU -->
    <!-- ================================================================= -->
    <div x-show="showCreateGroupModal"
         @click.self="showCreateGroupModal = false"
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;">
        
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-lg w-full overflow-visible relative"
             @click.stop>
            
            <div class="px-6 py-4 bg-gradient-to-r from-emerald-600 to-teal-700 text-white flex items-center justify-between rounded-t-2xl">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center text-white">
                        <i class="fa-solid fa-user-group text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold">Buat Group Baru</h3>
                        <p class="text-[10px] text-emerald-100">Kolaborasi tugas dan koordinasi tim</p>
                    </div>
                </div>
                <button type="button" @click="showCreateGroupModal = false" class="text-emerald-100 hover:text-white transition-colors">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <form @submit.prevent="submitCreateGroup()" class="p-6 space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Nama Group <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" x-model="newGroupForm.name" required maxlength="120"
                           placeholder="cth: Tim Lapangan Surabaya / Project IT Q3"
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Deskripsi / Tujuan Group (Opsional)
                    </label>
                    <textarea x-model="newGroupForm.description" rows="2" maxlength="500"
                              placeholder="Deskripsi singkat mengenai tujuan group koordinasi ini..."
                              class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all resize-none"></textarea>
                </div>

                <!-- Pilih Anggota Tim (Searchable Multi-Select Dropdown) -->
                <div class="relative" @click.away="inhouseDropdownOpen = false">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-bold text-slate-700">
                            Pilih Anggota Tim (Inhouse)
                            <span class="text-emerald-700 font-semibold ml-1" x-text="'(' + newGroupForm.members.length + ' dipilih)'"></span>
                        </label>
                        <div class="flex items-center gap-2 text-[11px]">
                            <button type="button" @click="selectAllFilteredInhouse()" class="text-emerald-600 hover:text-emerald-800 hover:underline font-semibold">Pilih Semua</button>
                            <span class="text-slate-300">•</span>
                            <button type="button" @click="clearSelectedMembers()" class="text-rose-500 hover:text-rose-700 hover:underline font-semibold">Reset</button>
                        </div>
                    </div>

                    <!-- Selected Chips / Badges Display -->
                    <div x-show="newGroupForm.members.length > 0" class="flex flex-wrap gap-1.5 mb-2 max-h-24 overflow-y-auto p-1.5 bg-emerald-50/60 rounded-xl border border-emerald-200/80">
                        <template x-for="name in newGroupForm.members" :key="name">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white border border-emerald-300 text-emerald-800 text-[11px] font-bold shadow-xs">
                                <span x-text="name"></span>
                                <button type="button" @click.stop="removeMember(name)" class="w-4 h-4 rounded-full bg-emerald-100 hover:bg-emerald-200 text-emerald-700 inline-flex items-center justify-center transition-colors" title="Hapus">
                                    <i class="fa-solid fa-xmark text-[9px]"></i>
                                </button>
                            </span>
                        </template>
                    </div>

                    <!-- Dropdown Trigger & Search Input -->
                    <div class="relative">
                        <div class="relative flex items-center cursor-pointer" @click="inhouseDropdownOpen = true">
                            <i class="fa-solid fa-magnifying-glass absolute left-3.5 text-slate-400 text-xs pointer-events-none"></i>
                            <input type="text" 
                                   x-model="inhouseSearch" 
                                   @focus="inhouseDropdownOpen = true"
                                   @click="inhouseDropdownOpen = true"
                                   placeholder="Ketik untuk mencari nama karyawan..."
                                   class="w-full pl-9 pr-10 py-2.5 text-xs bg-white rounded-xl border border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all placeholder:text-slate-400 font-medium">
                            <button type="button" 
                                    @click.stop="inhouseDropdownOpen = !inhouseDropdownOpen" 
                                    class="absolute right-2.5 w-7 h-7 flex items-center justify-center text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition-colors">
                                <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200" :class="{'rotate-180 text-emerald-600': inhouseDropdownOpen}"></i>
                            </button>
                        </div>

                        <!-- Floating Dropdown Panel -->
                        <div x-show="inhouseDropdownOpen" 
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 translate-y-1"
                             class="absolute left-0 right-0 top-full mt-1.5 bg-white border border-slate-200 rounded-xl shadow-2xl z-50 overflow-hidden divide-y divide-slate-100"
                             style="display: none;">
                            
                            <div class="px-3 py-1.5 bg-slate-50 text-[10px] text-slate-500 flex justify-between items-center font-medium">
                                <span>Pilih nama karyawan di bawah:</span>
                                <span class="font-bold text-slate-700" x-text="filteredInhouseEmployees.length + ' personil' + (filteredInhouseEmployees.length > 60 ? ' (menampilkan 60)' : '')"></span>
                            </div>

                            <div class="max-h-48 overflow-y-auto divide-y divide-slate-100">
                                <template x-for="emp in displayedInhouseEmployees" :key="emp.id">
                                    <div @click="toggleMember(emp.nama_karyawan)"
                                         class="flex items-center justify-between px-3.5 py-2 hover:bg-emerald-50/60 cursor-pointer transition-colors select-none"
                                         :class="isMemberSelected(emp.nama_karyawan) ? 'bg-emerald-50/80' : ''">
                                        <div class="flex items-center gap-2.5 min-w-0">
                                            <div class="w-7 h-7 rounded-lg text-white font-bold text-[10px] flex items-center justify-center flex-shrink-0"
                                                 :class="isMemberSelected(emp.nama_karyawan) ? 'bg-emerald-600 shadow-xs' : 'bg-slate-400'">
                                                <span x-text="(emp.nama_karyawan || '').substring(0, 2).toUpperCase()"></span>
                                            </div>
                                            <div class="min-w-0">
                                                <div class="text-xs text-slate-800 truncate" :class="isMemberSelected(emp.nama_karyawan) ? 'font-extrabold text-emerald-900' : 'font-semibold'" x-text="emp.nama_karyawan"></div>
                                                <div class="text-[10px] text-slate-400 truncate">
                                                    <span x-text="emp.jabatan_db || emp.jabatan || 'Karyawan'"></span> • <span x-text="emp.area || 'Pusat'"></span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="w-5 h-5 rounded-md border flex items-center justify-center transition-all flex-shrink-0"
                                             :class="isMemberSelected(emp.nama_karyawan) ? 'bg-emerald-600 border-emerald-600 text-white shadow-xs' : 'border-slate-300 bg-white'">
                                            <i class="fa-solid fa-check text-[10px]" x-show="isMemberSelected(emp.nama_karyawan)"></i>
                                        </div>
                                    </div>
                                </template>

                                <div x-show="filteredInhouseEmployees.length === 0" class="p-6 text-center text-xs text-slate-400">
                                    <i class="fa-solid fa-user-slash text-base mb-1 block text-slate-300"></i>
                                    Tidak ada karyawan yang cocok dengan pencarian "<span class="font-semibold text-slate-600" x-text="inhouseSearch"></span>"
                                </div>
                            </div>

                            <div class="p-2 bg-slate-50 flex items-center justify-between border-t border-slate-100">
                                <span class="text-[11px] text-emerald-700 font-bold" x-text="newGroupForm.members.length + ' anggota dipilih'"></span>
                                <button type="button" @click="inhouseDropdownOpen = false" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[11px] font-bold shadow-xs transition-colors">
                                    Selesai
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-2 flex items-center justify-end gap-2 border-t border-slate-100">
                    <button type="button" @click="showCreateGroupModal = false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors">
                        Batal
                    </button>
                    <button type="submit" :disabled="isSubmittingGroup" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 disabled:opacity-50 transition-all inline-flex items-center gap-2">
                        <i class="fa-solid fa-check text-xs"></i>
                        <span>Buat Group</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ================================================================= -->
    <!-- MODAL: TAMBAH ANGGOTA BARU KE GROUP AKTIF -->
    <!-- ================================================================= -->
    @if($activeGroup)
    <div x-show="showAddMemberModal"
         @click.self="showAddMemberModal = false"
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;">
        
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-lg w-full overflow-visible relative"
             @click.stop>
            
            <div class="px-6 py-4 bg-emerald-600 text-white flex items-center justify-between rounded-t-2xl">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center text-white">
                        <i class="fa-solid fa-user-plus text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold">Tambah Anggota ke Group</h3>
                        <p class="text-[10px] text-emerald-100">{{ $activeGroup->name }}</p>
                    </div>
                </div>
                <button type="button" @click="showAddMemberModal = false" class="text-emerald-100 hover:text-white transition-colors">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <form @submit.prevent="submitAddMembers()" class="p-6 space-y-4">
                <!-- Pilih Karyawan Inhouse (Searchable Multi-Select Dropdown) -->
                <div class="relative" @click.away="addMemberDropdownOpen = false">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-bold text-slate-700">
                            Pilih Karyawan Inhouse
                            <span class="text-emerald-700 font-semibold ml-1" x-text="'(' + selectedAddMembers.length + ' dipilih)'"></span>
                        </label>
                        <div class="flex items-center gap-2 text-[11px]">
                            <button type="button" @click="selectAllFilteredAddMembers()" class="text-emerald-600 hover:text-emerald-800 hover:underline font-semibold">Pilih Semua</button>
                            <span class="text-slate-300">•</span>
                            <button type="button" @click="clearAddMembers()" class="text-rose-500 hover:text-rose-700 hover:underline font-semibold">Reset</button>
                        </div>
                    </div>

                    <!-- Selected Chips / Badges Container -->
                    <div x-show="selectedAddMembers.length > 0" class="flex flex-wrap gap-1.5 mb-2 max-h-24 overflow-y-auto p-1.5 bg-emerald-50/60 rounded-xl border border-emerald-200/80">
                        <template x-for="name in selectedAddMembers" :key="name">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white border border-emerald-300 text-emerald-800 text-[11px] font-bold shadow-xs">
                                <span x-text="name"></span>
                                <button type="button" @click.stop="removeAddMember(name)" class="w-4 h-4 rounded-full bg-emerald-100 hover:bg-emerald-200 text-emerald-700 inline-flex items-center justify-center transition-colors" title="Hapus">
                                    <i class="fa-solid fa-xmark text-[9px]"></i>
                                </button>
                            </span>
                        </template>
                    </div>

                    <!-- Search Input & Dropdown Toggle Box -->
                    <div class="relative">
                        <div class="relative flex items-center cursor-pointer" @click="addMemberDropdownOpen = true">
                            <i class="fa-solid fa-magnifying-glass absolute left-3.5 text-slate-400 text-xs pointer-events-none"></i>
                            <input type="text" 
                                   x-model="addMemberSearch" 
                                   @focus="addMemberDropdownOpen = true"
                                   @click="addMemberDropdownOpen = true"
                                   placeholder="Ketik untuk mencari nama karyawan..."
                                   class="w-full pl-9 pr-10 py-2.5 text-xs bg-white rounded-xl border border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all placeholder:text-slate-400 font-medium">
                            <button type="button" 
                                    @click.stop="addMemberDropdownOpen = !addMemberDropdownOpen" 
                                    class="absolute right-2.5 w-7 h-7 flex items-center justify-center text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition-colors">
                                <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200" :class="{'rotate-180 text-emerald-600': addMemberDropdownOpen}"></i>
                            </button>
                        </div>

                        <!-- Floating Dropdown List Panel -->
                        <div x-show="addMemberDropdownOpen" 
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 translate-y-1"
                             class="absolute left-0 right-0 top-full mt-1.5 bg-white border border-slate-200 rounded-xl shadow-2xl z-50 overflow-hidden divide-y divide-slate-100"
                             style="display: none;">
                            
                            <!-- Search Status & Quick Count -->
                            <div class="px-3 py-1.5 bg-slate-50 text-[10px] text-slate-500 flex justify-between items-center font-medium">
                                <span>Pilih calon anggota yang akan ditambahkan:</span>
                                <span class="font-bold text-slate-700" x-text="nonMemberCandidates.length + ' personil' + (nonMemberCandidates.length > 60 ? ' (menampilkan 60)' : '')"></span>
                            </div>

                            <!-- List of Non-Member Candidates -->
                            <div class="max-h-48 overflow-y-auto divide-y divide-slate-100">
                                <template x-for="emp in displayedAddMembers" :key="emp.id">
                                    <div @click="toggleAddMember(emp.nama_karyawan)"
                                         class="flex items-center justify-between px-3.5 py-2 hover:bg-emerald-50/60 cursor-pointer transition-colors select-none"
                                         :class="isAddMemberSelected(emp.nama_karyawan) ? 'bg-emerald-50/80' : ''">
                                        
                                        <div class="flex items-center gap-2.5 min-w-0">
                                            <div class="w-7 h-7 rounded-lg text-white font-bold text-[10px] flex items-center justify-center flex-shrink-0"
                                                 :class="isAddMemberSelected(emp.nama_karyawan) ? 'bg-emerald-600 shadow-xs' : 'bg-slate-400'">
                                                <span x-text="(emp.nama_karyawan || '').substring(0, 2).toUpperCase()"></span>
                                            </div>
                                            <div class="min-w-0">
                                                <div class="text-xs text-slate-800 truncate" :class="isAddMemberSelected(emp.nama_karyawan) ? 'font-extrabold text-emerald-900' : 'font-semibold'" x-text="emp.nama_karyawan"></div>
                                                <div class="text-[10px] text-slate-400 truncate">
                                                    <span x-text="emp.jabatan_db || emp.jabatan || 'Karyawan'"></span> • <span x-text="emp.area || 'Pusat'"></span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Checkbox Pill -->
                                        <div class="w-5 h-5 rounded-md border flex items-center justify-center transition-all flex-shrink-0"
                                             :class="isAddMemberSelected(emp.nama_karyawan) ? 'bg-emerald-600 border-emerald-600 text-white shadow-xs' : 'border-slate-300 bg-white'">
                                            <i class="fa-solid fa-check text-[10px]" x-show="isAddMemberSelected(emp.nama_karyawan)"></i>
                                        </div>
                                    </div>
                                </template>

                                <div x-show="nonMemberCandidates.length === 0" class="p-6 text-center text-xs text-slate-400">
                                    <i class="fa-solid fa-circle-info text-base mb-1 block text-slate-300"></i>
                                    Semua karyawan inhouse sudah menjadi anggota group ini atau tidak ada hasil pencarian.
                                </div>
                            </div>

                            <!-- Dropdown Footer -->
                            <div class="p-2 bg-slate-50 flex items-center justify-between border-t border-slate-100">
                                <span class="text-[11px] text-emerald-700 font-bold" x-text="selectedAddMembers.length + ' anggota dipilih'"></span>
                                <button type="button" @click="addMemberDropdownOpen = false" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[11px] font-bold shadow-xs transition-colors">
                                    Selesai
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-2 flex items-center justify-end gap-2 border-t border-slate-100">
                    <button type="button" @click="showAddMemberModal = false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors">
                        Batal
                    </button>
                    <button type="submit" :disabled="isSubmittingMembers || selectedAddMembers.length === 0" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 disabled:opacity-50 transition-all inline-flex items-center gap-2">
                        <i class="fa-solid fa-user-plus text-xs"></i>
                        <span>Tambahkan Anggota</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ================================================================= -->
    <!-- MODAL: LIHAT SEMUA ANGGOTA GROUP -->
    <!-- ================================================================= -->
    <div x-show="showViewMembersModal"
         @click.self="showViewMembersModal = false"
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;">
        
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-md w-full overflow-hidden"
             @click.stop>
            
            <div class="px-5 py-4 bg-slate-900 text-white flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center text-white">
                        <i class="fa-solid fa-users text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold">Anggota Group</h3>
                        <p class="text-[10px] text-slate-300">{{ $activeGroup->name }} ({{ $activeGroup->members->count() }} orang)</p>
                    </div>
                </div>
                <button type="button" @click="showViewMembersModal = false" class="text-slate-400 hover:text-white transition-colors">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <div class="p-4 max-h-80 overflow-y-auto divide-y divide-slate-100">
                @foreach($activeGroup->members as $member)
                <div class="flex items-center justify-between py-2.5">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-8 h-8 rounded-full bg-slate-200 text-slate-700 font-bold text-xs flex items-center justify-center flex-shrink-0">
                            {{ strtoupper(substr($member->user_name, 0, 2)) }}
                        </div>
                        <div class="min-w-0">
                            <div class="text-xs font-bold text-slate-800 truncate flex items-center gap-1.5">
                                <span>{{ $member->user_name }}</span>
                                @if(trim(strtolower($member->user_name)) === trim(strtolower($userName)))
                                    <span class="text-[9px] font-semibold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">Anda</span>
                                @endif
                            </div>
                            <div class="text-[10px] text-slate-400">Bergabung: {{ $member->joined_at ? $member->joined_at->format('d/m/Y') : '-' }}</div>
                        </div>
                    </div>
                    <div>
                        @if($member->role === 'admin')
                            <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 border border-amber-300">
                                Admin
                            </span>
                        @else
                            <span class="text-[10px] font-medium px-2 py-0.5 rounded-full bg-slate-100 text-slate-600">
                                Anggota
                            </span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            <div class="p-3.5 bg-slate-50 border-t border-slate-200 flex justify-end">
                <button type="button" @click="showViewMembersModal = false" class="px-4 py-1.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-semibold">
                    Tutup
                </button>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection

@section('scripts')
<script>
function wpGroupChat() {
    return {
        userName: @json($userName),
        activeGroupId: @json($activeGroup ? $activeGroup->id : null),
        activeGroupName: @json($activeGroup ? $activeGroup->name : 'Groups Chat'),
        isGroupMember: @json($isGroupMember ?? false),
        csrfToken: '{{ csrf_token() }}',
        
        // Data Groups
        groups: {!! json_encode($groupsJson) !!},
        groupSearch: '',
        isRefreshingGroups: false,
        lastKnownGroupMessages: {},

        // Data Pesan
        messages: {!! json_encode($messagesJson) !!},
        lastMessageId: 0,
        inputMessage: '',
        isSendingMessage: false,
        messagePollTimer: null,
        groupsPollTimer: null,

        // Inhouse Employees
        inhouseEmployees: @json($inhouseEmployees),
        inhouseSearch: '',

        // Modals state
        showCreateGroupModal: false,
        isSubmittingGroup: false,
        inhouseDropdownOpen: false,
        newGroupForm: {
            name: '',
            description: '',
            members: []
        },

        showAddMemberModal: false,
        isSubmittingMembers: false,
        addMemberDropdownOpen: false,
        addMemberSearch: '',
        selectedAddMembers: [],

        showViewMembersModal: false,
        activeGroupMembers: @json($activeGroup ? $activeGroup->members->pluck('user_name')->toArray() : []),

        // Palet warna pengirim chat orang lain
        colorPalette: ['#0284c7', '#7c3aed', '#d97706', '#059669', '#dc2626', '#4f46e5', '#0891b2', '#c026d3'],

        initChat() {
            if (this.messages.length > 0) {
                this.lastMessageId = this.messages[this.messages.length - 1].id;
            }

            // Catat pesan terakhir dari masing-masing grup untuk deteksi chat baru via sidebar
            this.groups.forEach(g => {
                if (g.latest_message) {
                    this.lastKnownGroupMessages[g.id] = g.latest_message.sender + ':' + g.latest_message.text;
                }
            });

            this.scrollToBottom(false);

            // Jalankan polling real-time pesan HANYA jika ada group aktif dan user adalah ANGGOTA group
            if (this.activeGroupId && this.isGroupMember) {
                this.startMessagePolling();
            }

            // Jalankan polling daftar group
            this.startGroupsPolling();
        },

        scrollToBottom(smooth = true) {
            this.$nextTick(() => {
                const container = document.getElementById('chatMessageStream');
                if (container) {
                    if (smooth) {
                        container.scrollTo({ top: container.scrollHeight, behavior: 'smooth' });
                    } else {
                        container.scrollTop = container.scrollHeight;
                    }
                }
            });
        },

        get filteredGroups() {
            if (!this.groupSearch.trim()) return this.groups;
            const q = this.groupSearch.toLowerCase();
            return this.groups.filter(g => g.name.toLowerCase().includes(q));
        },

        get filteredInhouseEmployees() {
            if (!this.inhouseSearch.trim()) return this.inhouseEmployees;
            const q = this.inhouseSearch.toLowerCase();
            return this.inhouseEmployees.filter(e => 
                (e.nama_karyawan && e.nama_karyawan.toLowerCase().includes(q)) ||
                (e.jabatan_db && e.jabatan_db.toLowerCase().includes(q)) ||
                (e.jabatan && e.jabatan.toLowerCase().includes(q)) ||
                (e.area && e.area.toLowerCase().includes(q))
            );
        },

        get nonMemberCandidates() {
            const currentMembersLower = this.activeGroupMembers.map(m => (m || '').toLowerCase().trim());
            return this.inhouseEmployees.filter(e => {
                const name = (e.nama_karyawan || '').toLowerCase().trim();
                const isMember = currentMembersLower.includes(name);
                if (isMember) return false;
                if (!this.addMemberSearch.trim()) return true;
                const q = this.addMemberSearch.toLowerCase();
                return name.includes(q) || 
                       ((e.jabatan_db || e.jabatan || '').toLowerCase().includes(q)) ||
                       ((e.area || '').toLowerCase().includes(q));
            });
        },

        get displayedInhouseEmployees() {
            return this.filteredInhouseEmployees.slice(0, 60);
        },

        get displayedAddMembers() {
            return this.nonMemberCandidates.slice(0, 60);
        },

        // Searchable Multi-Select Dropdown methods (Create Group)
        toggleMember(name) {
            if (!name) return;
            const idx = this.newGroupForm.members.indexOf(name);
            if (idx > -1) {
                this.newGroupForm.members.splice(idx, 1);
            } else {
                this.newGroupForm.members.push(name);
            }
        },

        removeMember(name) {
            this.newGroupForm.members = this.newGroupForm.members.filter(n => n !== name);
        },

        isMemberSelected(name) {
            return this.newGroupForm.members.includes(name);
        },

        selectAllFilteredInhouse() {
            const names = this.filteredInhouseEmployees.map(e => e.nama_karyawan).filter(Boolean);
            names.forEach(name => {
                if (!this.newGroupForm.members.includes(name)) {
                    this.newGroupForm.members.push(name);
                }
            });
        },

        clearSelectedMembers() {
            this.newGroupForm.members = [];
        },

        // Searchable Multi-Select Dropdown methods (Add Members)
        toggleAddMember(name) {
            if (!name) return;
            const idx = this.selectedAddMembers.indexOf(name);
            if (idx > -1) {
                this.selectedAddMembers.splice(idx, 1);
            } else {
                this.selectedAddMembers.push(name);
            }
        },

        removeAddMember(name) {
            this.selectedAddMembers = this.selectedAddMembers.filter(n => n !== name);
        },

        isAddMemberSelected(name) {
            return this.selectedAddMembers.includes(name);
        },

        selectAllFilteredAddMembers() {
            const names = this.nonMemberCandidates.map(e => e.nama_karyawan).filter(Boolean);
            names.forEach(name => {
                if (!this.selectedAddMembers.includes(name)) {
                    this.selectedAddMembers.push(name);
                }
            });
        },

        clearAddMembers() {
            this.selectedAddMembers = [];
        },

        selectGroup(id) {
            if (id == this.activeGroupId) return;
            window.location.href = "{{ route('workplan.chat') }}?group_id=" + id;
        },

        getSenderColor(name) {
            if (!name) return '#0284c7';
            let hash = 0;
            for (let i = 0; i < name.length; i++) {
                hash = name.charCodeAt(i) + ((hash << 5) - hash);
            }
            const index = Math.abs(hash) % this.colorPalette.length;
            return this.colorPalette[index];
        },

        formatLastMessageSnippet(g) {
            if (g.is_member === false) {
                return '<span class="italic text-slate-400 font-medium"><i class="fa-solid fa-lock text-[9px] mr-1"></i>Pesan khusus anggota grup</span>';
            }
            if (!g.latest_message) return '<span class="italic text-slate-400">Belum ada pesan</span>';
            const sender = g.latest_message.sender === this.userName ? 'Anda: ' : `${g.latest_message.sender}: `;
            return `<span class="font-medium text-slate-600">${this.escapeHtml(sender)}</span>${this.escapeHtml(g.latest_message.text)}`;
        },

        formatMessageText(text) {
            if (!text) return '';
            // Auto link detection and HTML escape
            const escaped = this.escapeHtml(text);
            const urlPattern = /(\b(https?|ftp):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/gim;
            return escaped.replace(urlPattern, '<a href="$1" target="_blank" class="text-emerald-700 underline hover:text-emerald-900 break-all font-semibold">$1</a>');
        },

        escapeHtml(string) {
            const entityMap = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;',
                '/': '&#x2F;'
            };
            return String(string).replace(/[&<>"'\/]/g, s => entityMap[s]);
        },

        // Suara Notifikasi Chat (Web Audio API sintetis, jernih & tanpa file luar)
        playNotificationSound() {
            try {
                const AudioContext = window.AudioContext || window.webkitAudioContext;
                if (!AudioContext) return;
                const ctx = new AudioContext();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(587.33, ctx.currentTime); // D5
                osc.frequency.setValueAtTime(880, ctx.currentTime + 0.08); // A5
                gain.gain.setValueAtTime(0.12, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.35);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + 0.35);
            } catch (e) {
                // Ignore audio policy restriction
            }
        },

        // Trigger Toast SweetAlert2 untuk Chat Baru Masuk
        triggerChatToast(notif) {
            this.playNotificationSound();

            // Beritahu layout navbar agar lonceng ikut terupdate
            window.dispatchEvent(new CustomEvent('asystem-chat-notif', { detail: notif }));

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    showCloseButton: true,
                    timer: 5000,
                    timerProgressBar: true,
                    iconHtml: `<div class="w-7 h-7 rounded-full flex items-center justify-center text-white text-xs font-bold shadow-xs bg-emerald-600">
                                 <i class="fa-solid fa-comments text-xs"></i>
                               </div>`,
                    customClass: {
                        popup: 'rounded-2xl shadow-2xl border border-emerald-300 bg-white/95 backdrop-blur-md cursor-pointer hover:shadow-2xl text-left',
                        title: 'text-xs font-extrabold text-slate-800 m-0 text-left',
                        htmlContainer: 'text-xs text-slate-600 m-0 mt-1 text-left'
                    },
                    title: `<div class="flex items-center gap-1.5 text-emerald-800 font-extrabold text-xs">
                              <i class="fa-solid fa-users text-[10px] text-emerald-600"></i>
                              <span>${this.escapeHtml(notif.group_name)}</span>
                            </div>`,
                    html: `
                        <div class="flex items-start gap-2.5 pt-1">
                            <img src="${notif.sender_avatar || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(notif.user_sender) + '&background=059669&color=fff'}" 
                                 class="w-7 h-7 rounded-full object-cover border border-slate-200 flex-shrink-0 mt-0.5 shadow-2xs">
                            <div class="min-w-0 flex-1">
                                <div class="text-[11px] font-bold text-slate-800 truncate">${this.escapeHtml(notif.user_sender)}</div>
                                <div class="text-xs text-slate-600 line-clamp-2 leading-relaxed">${this.escapeHtml(notif.message_text)}</div>
                            </div>
                        </div>
                    `,
                    didOpen: (toast) => {
                        toast.addEventListener('click', (e) => {
                            if (!e.target.closest('.swal2-close')) {
                                if (notif.group_id && notif.group_id != this.activeGroupId) {
                                    window.location.href = `{{ url('/workplan-chat') }}?group_id=${notif.group_id}`;
                                }
                            }
                        });
                    }
                });
            }
        },

        // Real-time Polling Pesan
        startMessagePolling() {
            if (this.messagePollTimer) clearInterval(this.messagePollTimer);
            this.messagePollTimer = setInterval(() => {
                this.fetchNewMessages();
            }, 2500);
        },

        async fetchNewMessages() {
            if (!this.activeGroupId) return;
            try {
                const response = await fetch(`{{ url('/workplan-chat/groups') }}/${this.activeGroupId}/messages?last_id=${this.lastMessageId}`);
                if (!response.ok) return;
                const res = await response.json();
                if (res.success && res.messages && res.messages.length > 0) {
                    const stream = document.getElementById('chatMessageStream');
                    const isNearBottom = stream ? (stream.scrollHeight - stream.scrollTop - stream.clientHeight < 120) : true;

                    res.messages.forEach(msg => {
                        if (!this.messages.some(m => m.id === msg.id)) {
                            this.messages.push(msg);
                            if (msg.id > this.lastMessageId) {
                                this.lastMessageId = msg.id;
                            }

                            // Jika pesan baru bukan dari user sendiri dan bukan sistem, bunyikan dan tampilkan toast!
                            if (!msg.is_me && !msg.is_system) {
                                this.triggerChatToast({
                                    group_id: this.activeGroupId,
                                    group_name: this.activeGroupName,
                                    user_sender: msg.user_sender,
                                    sender_avatar: msg.sender_avatar,
                                    message_text: msg.message_text
                                });
                            }
                        }
                    });

                    if (isNearBottom) {
                        this.scrollToBottom(true);
                    }
                }
            } catch (e) {
                console.error('Message polling error:', e);
            }
        },

        // Polling update sidebar group
        startGroupsPolling() {
            if (this.groupsPollTimer) clearInterval(this.groupsPollTimer);
            this.groupsPollTimer = setInterval(() => {
                this.pollGroups(false);
            }, 5000);
        },

        async pollGroups(manual = false) {
            if (manual) this.isRefreshingGroups = true;
            try {
                const response = await fetch("{{ route('workplan.chat.groups.poll') }}");
                if (response.ok) {
                    const res = await response.json();
                    if (res.success && res.groups) {
                        // Deteksi jika ada pesan baru di group selain activeGroup (HANYA untuk group tempat user menjadi anggota resmi)
                        res.groups.forEach(g => {
                            if (g.is_member && g.latest_message && g.id != this.activeGroupId) {
                                const sig = g.latest_message.sender + ':' + g.latest_message.text;
                                const prevSig = this.lastKnownGroupMessages[g.id];
                                if (prevSig && prevSig !== sig && g.latest_message.sender !== this.userName) {
                                    // Munculkan toast notifikasi untuk grup lain yang ada chat baru!
                                    this.triggerChatToast({
                                        group_id: g.id,
                                        group_name: g.name,
                                        user_sender: g.latest_message.sender,
                                        message_text: g.latest_message.text
                                    });
                                }
                                this.lastKnownGroupMessages[g.id] = sig;
                            }
                        });

                        this.groups = res.groups;
                    }
                }
            } catch (e) {
                console.error('Groups poll error:', e);
            } finally {
                if (manual) this.isRefreshingGroups = false;
            }
        },

        // Kirim Pesan Teks
        async sendChatMessage() {
            const text = this.inputMessage.trim();
            if (!text || !this.activeGroupId || this.isSendingMessage) return;

            this.isSendingMessage = true;
            this.inputMessage = '';

            try {
                const response = await fetch(`{{ url('/workplan-chat/groups') }}/${this.activeGroupId}/messages`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ message_text: text })
                });

                const res = await response.json();
                if (res.success && res.message) {
                    this.messages.push(res.message);
                    this.lastMessageId = res.message.id;
                    this.scrollToBottom(true);
                    this.pollGroups(false);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Mengirim',
                        text: res.error || 'Terjadi kesalahan saat mengirim pesan.',
                        confirmButtonColor: '#10b981'
                    });
                    this.inputMessage = text;
                }
            } catch (e) {
                console.error('Send message error:', e);
                Swal.fire({
                    icon: 'error',
                    title: 'Koneksi Terputus',
                    text: 'Tidak dapat menghubungi server. Silakan coba sesaat lagi.',
                    confirmButtonColor: '#10b981'
                });
                this.inputMessage = text;
            } finally {
                this.isSendingMessage = false;
            }
        },

        // Modal Handlers
        openCreateGroupModal() {
            this.newGroupForm = {
                name: '',
                description: '',
                members: []
            };
            this.inhouseSearch = '';
            this.inhouseDropdownOpen = false;
            this.showCreateGroupModal = true;
        },

        async submitCreateGroup() {
            if (!this.newGroupForm.name.trim()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Nama group wajib diisi.',
                    confirmButtonColor: '#10b981'
                });
                return;
            }

            this.isSubmittingGroup = true;
            try {
                const response = await fetch("{{ route('workplan.chat.groups.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.newGroupForm)
                });

                const res = await response.json();
                if (res.success && res.redirect) {
                    this.showCreateGroupModal = false;
                    Swal.fire({
                        icon: 'success',
                        title: 'Group Berhasil Dibuat!',
                        text: `Group ${this.newGroupForm.name} siap digunakan.`,
                        showConfirmButton: false,
                        timer: 1200
                    }).then(() => {
                        window.location.href = res.redirect;
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Membuat Group',
                        text: res.message || 'Terjadi kesalahan pada input data.',
                        confirmButtonColor: '#10b981'
                    });
                }
            } catch (e) {
                console.error('Submit group error:', e);
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Server',
                    text: 'Gagal menyimpan group baru.',
                    confirmButtonColor: '#10b981'
                });
            } finally {
                this.isSubmittingGroup = false;
            }
        },

        openAddMemberModal() {
            this.selectedAddMembers = [];
            this.addMemberSearch = '';
            this.addMemberDropdownOpen = false;
            this.showAddMemberModal = true;
        },

        async submitAddMembers() {
            if (this.selectedAddMembers.length === 0 || !this.activeGroupId) return;

            this.isSubmittingMembers = true;
            try {
                const response = await fetch(`{{ url('/workplan-chat/groups') }}/${this.activeGroupId}/members`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ members: this.selectedAddMembers })
                });

                const res = await response.json();
                if (res.success) {
                    this.showAddMemberModal = false;
                    // Update list anggota lokal
                    this.activeGroupMembers = [...this.activeGroupMembers, ...this.selectedAddMembers];
                    Swal.fire({
                        icon: 'success',
                        title: 'Anggota Ditambahkan!',
                        text: res.message,
                        confirmButtonColor: '#10b981'
                    }).then(() => {
                        this.fetchNewMessages();
                        this.pollGroups(false);
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Menambah Anggota',
                        text: res.message || 'Terjadi kesalahan.',
                        confirmButtonColor: '#10b981'
                    });
                }
            } catch (e) {
                console.error('Add members error:', e);
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Server',
                    text: 'Gagal menambahkan anggota ke group.',
                    confirmButtonColor: '#10b981'
                });
            } finally {
                this.isSubmittingMembers = false;
            }
        },

        openViewMembersModal() {
            this.showViewMembersModal = true;
        }
    };
}
</script>
@endsection
