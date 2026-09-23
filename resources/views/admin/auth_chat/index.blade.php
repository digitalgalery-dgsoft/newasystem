@extends('layouts.app')

@section('title', 'Bantuan Login & Reset Kata Sandi - ASystem')

@section('content')
<div class="space-y-5 pb-10" id="authChatAdminApp">

    <!-- PAGE HEADER CARD -->
    <div class="page-header-card flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-primary to-indigo-700 flex items-center justify-center text-white text-2xl font-black shadow-md shadow-primary/20">
                <i class="fa-solid fa-headset"></i>
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-xl md:text-2xl font-extrabold text-slate-900 tracking-tight">Bantuan Login &amp; Reset Password</h1>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-50 text-primary border border-blue-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"></span>
                        Live Helpdesk Karyawan
                    </span>
                    @if($pendingCount > 0)
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-50 text-rose-700 border border-rose-200 animate-bounce">
                        {{ $pendingCount }} Permintaan Baru
                    </span>
                    @endif
                </div>
                <p class="text-xs md:text-sm text-slate-500 font-medium mt-0.5">
                    Permintaan reset kata sandi via Live Chat dari karyawan di halaman login. Verifikasi Odoo otomatis &amp; 1-klik kirim kredensial.
                </p>
            </div>
        </div>

        <!-- Quick Metrics -->
        <div class="flex items-center gap-2">
            <div class="px-3.5 py-2 rounded-xl bg-slate-50 border border-slate-200/80 text-center min-w-[80px]">
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Total</span>
                <strong class="text-base font-black text-slate-800">{{ $totalCount }}</strong>
            </div>
            <div class="px-3.5 py-2 rounded-xl bg-amber-50 border border-amber-200 text-center min-w-[80px]">
                <span class="text-[10px] uppercase font-bold text-amber-600 block">Pending</span>
                <strong class="text-base font-black text-amber-700" id="badgePendingCount">{{ $pendingCount }}</strong>
            </div>
            <div class="px-3.5 py-2 rounded-xl bg-emerald-50 border border-emerald-200 text-center min-w-[80px]">
                <span class="text-[10px] uppercase font-bold text-emerald-600 block">Selesai</span>
                <strong class="text-base font-black text-emerald-700">{{ $resolvedCount }}</strong>
            </div>
        </div>
    </div>

    <!-- MAIN 2-COLUMN SPLIT PANE -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
        
        <!-- LEFT COLUMN: TICKET LIST (4 COLS) -->
        <div class="lg:col-span-4 bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden flex flex-col min-h-[580px] max-h-[800px]">
            
            <!-- Filter & Search Toolbar -->
            <div class="p-3.5 border-b border-slate-100 space-y-3 bg-slate-50/70">
                <!-- Search input -->
                <form method="GET" action="{{ route('admin.auth-chat.index') }}" class="relative">
                    <input type="hidden" name="status" value="{{ $filterStatus }}">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 text-xs">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </div>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari NIK, Nama, No Tiket..." class="w-full pl-8 pr-3 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20 transition-all">
                </form>

                <!-- Status Filter Tabs -->
                <div class="flex items-center gap-1 p-1 bg-slate-200/60 rounded-xl text-xs font-bold">
                    <a href="{{ route('admin.auth-chat.index', ['status' => 'all', 'search' => $search]) }}" class="flex-1 py-1.5 text-center rounded-lg transition-all {{ $filterStatus === 'all' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                        Semua
                    </a>
                    <a href="{{ route('admin.auth-chat.index', ['status' => 'pending', 'search' => $search]) }}" class="flex-1 py-1.5 text-center rounded-lg transition-all {{ $filterStatus === 'pending' ? 'bg-white text-amber-700 shadow-xs' : 'text-slate-600 hover:text-amber-700' }}">
                        Pending @if($pendingCount > 0) <span class="ml-1 px-1.5 py-0.2 rounded-full text-[9px] bg-rose-500 text-white font-mono">{{ $pendingCount }}</span> @endif
                    </a>
                    <a href="{{ route('admin.auth-chat.index', ['status' => 'resolved', 'search' => $search]) }}" class="flex-1 py-1.5 text-center rounded-lg transition-all {{ $filterStatus === 'resolved' ? 'bg-white text-emerald-700 shadow-xs' : 'text-slate-600 hover:text-emerald-700' }}">
                        Selesai
                    </a>
                </div>
            </div>

            <!-- Ticket Items Scroll List -->
            <div class="flex-1 overflow-y-auto divide-y divide-slate-100">
                @forelse($requests as $req)
                @php
                    $isSelected = $activeTicket && $activeTicket->id === $req->id;
                    $emp = $req->employee;
                @endphp
                <a href="{{ route('admin.auth-chat.index', ['id' => $req->id, 'status' => $filterStatus, 'search' => $search]) }}" 
                   class="block p-3.5 transition-all hover:bg-slate-50 relative {{ $isSelected ? 'bg-blue-50/80 border-l-4 border-primary' : '' }}">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br {{ $isSelected ? 'from-primary to-indigo-600 text-white' : 'bg-slate-100 text-slate-700' }} flex items-center justify-center font-bold text-sm shrink-0 shadow-xs border border-slate-200/50">
                            {{ strtoupper(substr($req->nama_karyawan, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-1 mb-1">
                                <h4 class="text-xs font-bold text-slate-900 truncate {{ $isSelected ? 'text-primary' : '' }}">
                                    {{ $req->nama_karyawan }}
                                </h4>
                                <span class="text-[10px] text-slate-400 font-mono shrink-0">
                                    {{ $req->created_at->diffForHumans() }}
                                </span>
                            </div>

                            <div class="flex items-center gap-1.5 text-[11px] text-slate-500 font-mono mb-1.5 flex-wrap">
                                <span class="font-bold text-slate-700">{{ $req->nik }}</span>
                                <span>&bull;</span>
                                <span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">{{ $req->entitas ?: 'AMK' }}</span>
                                <span class="px-1.5 py-0.2 rounded text-[10px] font-semibold {{ $req->tipe_karyawan === 'Inhouse' ? 'bg-blue-50 text-blue-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $req->tipe_karyawan ?: 'Inhouse' }}
                                </span>
                            </div>

                            <p class="text-[11px] text-slate-600 line-clamp-1 italic">
                                &ldquo;{{ $req->latestMessage ? $req->latestMessage->message : $req->request_message }}&rdquo;
                            </p>

                            <div class="mt-2 flex items-center justify-between">
                                <span class="text-[9px] font-mono text-slate-400">{{ $req->ticket_number }}</span>
                                {!! $req->status_badge !!}
                            </div>
                        </div>
                    </div>
                </a>
                @empty
                <div class="p-8 text-center text-slate-400 space-y-2">
                    <i class="fa-regular fa-folder-open text-3xl text-slate-300"></i>
                    <p class="text-xs font-bold text-slate-600">Tidak ada tiket ditemukan</p>
                    <p class="text-[11px] text-slate-400">Belum ada permintaan reset password pada filter ini.</p>
                </div>
                @endforelse
            </div>

            <!-- Pagination Footer -->
            @if($requests->hasPages())
            <div class="p-3 border-t border-slate-100 bg-slate-50/50 text-xs">
                {{ $requests->links() }}
            </div>
            @endif
        </div>

        <!-- RIGHT COLUMN: CHAT & DETAIL PANE (8 COLS) -->
        <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden flex flex-col min-h-[580px] max-h-[800px]">
            @if($activeTicket)
            @php
                $activeEmp = $activeTicket->employee;
                $emailLogin = $activeEmp?->email ?: ($activeTicket->nik . '@asystem.co.id');
                $defPass = $activeEmp?->default_password ?: 'ddmmyyyy';
                $isRateCard = ($activeTicket->tipe_karyawan !== 'Inhouse');
                $hasAccess = $activeEmp ? $activeEmp->hasLoginAccess() : false;

                // WhatsApp URL Helper
                $phoneClean = preg_replace('/[^0-9]/', '', (string)$activeTicket->telepon);
                if (str_starts_with($phoneClean, '0')) {
                    $phoneClean = '62' . substr($phoneClean, 1);
                }
                $waText = "Halo {$activeTicket->nama_karyawan},\n\nBerikut akses login ASystem Support System Anda:\n• Email / NIK: {$emailLogin}\n• Kata Sandi: {$defPass}\n• Tautan Masuk: " . route('login') . "\n\nSalam,\nAdmin HR ASystem";
                $waUrl = !empty($phoneClean) ? "https://wa.me/{$phoneClean}?text=" . urlencode($waText) : null;
            @endphp

            <!-- Top Header & Employee Profile Card -->
            <div class="p-4 border-b border-slate-100 bg-gradient-to-r from-slate-50 via-white to-blue-50/30">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-primary text-white flex items-center justify-center font-black text-base shadow-sm">
                            {{ strtoupper(substr($activeTicket->nama_karyawan, 0, 1)) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 class="text-sm font-extrabold text-slate-900">{{ $activeTicket->nama_karyawan }}</h3>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-200">{{ $activeTicket->entitas ?: 'AMK' }}</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $isRateCard ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-emerald-100 text-emerald-800 border border-emerald-200' }}">
                                    {{ $activeTicket->tipe_karyawan ?: 'Inhouse' }}
                                </span>
                                @if($activeTicket->odoo_synced)
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-purple-100 text-purple-800 border border-purple-200">
                                    <i class="fa-solid fa-arrows-rotate text-[8px]"></i> Baru di-sync dari Odoo
                                </span>
                                @endif
                            </div>
                            <div class="text-[11px] text-slate-500 font-mono mt-0.5">
                                NIK: <strong class="text-slate-800">{{ $activeTicket->nik }}</strong> 
                                @if($activeEmp?->nip) &bull; NIP: {{ $activeEmp->nip }} @endif
                                &bull; Jabatan: <strong class="text-slate-700 font-sans">{{ $activeTicket->jabatan ?: 'Staff' }}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        {!! $activeTicket->status_badge !!}
                    </div>
                </div>

                <!-- Credential Summary & Action Ribbon -->
                <div class="mt-3.5 pt-3 border-t border-slate-200/60 grid grid-cols-1 sm:grid-cols-3 gap-2.5 text-xs">
                    <div class="p-2.5 rounded-xl bg-white border border-slate-200/80 shadow-2xs">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Username / Email:</span>
                        <strong class="font-mono text-slate-900 text-xs truncate block" title="{{ $emailLogin }}">{{ $emailLogin }}</strong>
                    </div>
                    <div class="p-2.5 rounded-xl bg-white border border-slate-200/80 shadow-2xs">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Password Default (DDMMYYYY):</span>
                        <div class="flex items-center justify-between">
                            <code class="font-mono text-xs font-bold text-primary">{{ $defPass }}</code>
                            <span class="text-[10px] text-slate-400">Tgl Lahir</span>
                        </div>
                    </div>
                    <div class="p-2.5 rounded-xl bg-white border border-slate-200/80 shadow-2xs">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Status Izin Akses Login:</span>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="w-2 h-2 rounded-full {{ $hasAccess ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                            <strong class="text-xs {{ $hasAccess ? 'text-emerald-700' : 'text-rose-700' }}">
                                {{ $hasAccess ? 'Diizinkan (Aktif)' : 'Terkunci (RateCard)' }}
                            </strong>
                        </div>
                    </div>
                </div>

                <!-- Action Button Toolbar -->
                <div class="mt-3.5 flex flex-wrap items-center gap-2">
                    <!-- Tombol Utama: Kirim Akses (Email & Password) -->
                    <form method="POST" action="{{ route('admin.auth-chat.send-access', $activeTicket->id) }}" class="inline" onsubmit="return confirm('Kirimkan akses login (email & password) ke karyawan {{ $activeTicket->nama_karyawan }} via Live Chat?')">
                        @csrf
                        <button type="submit" class="px-4 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-xs font-bold shadow-sm shadow-emerald-600/20 flex items-center gap-2 transition-all">
                            <i class="fa-solid fa-key"></i>
                            <span>Kirim Akses (Email &amp; Kata Sandi)</span>
                        </button>
                    </form>

                    <!-- Tombol Sekunder: Kirim via WhatsApp -->
                    @if($waUrl)
                    <a href="{{ $waUrl }}" target="_blank" class="px-3.5 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-300 text-xs font-bold flex items-center gap-2 transition-all" title="Buka WhatsApp Web / App">
                        <i class="fa-brands fa-whatsapp text-sm text-emerald-600"></i>
                        <span>Kirim via WA</span>
                    </a>
                    @endif

                    <!-- Tombol Tandai Selesai -->
                    @if($activeTicket->status !== 'resolved')
                    <form method="POST" action="{{ route('admin.auth-chat.resolve', $activeTicket->id) }}" class="inline">
                        @csrf
                        <button type="submit" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold flex items-center gap-1.5 transition-all">
                            <i class="fa-solid fa-check"></i>
                            <span>Tandai Selesai</span>
                        </button>
                    </form>
                    @endif

                    <span class="text-[11px] text-slate-400 ml-auto font-mono">
                        Tiket: {{ $activeTicket->ticket_number }}
                    </span>
                </div>
            </div>

            <!-- Chat Message Scroll Container -->
            <div id="adminChatMessagesList" class="flex-1 p-4 sm:p-5 overflow-y-auto space-y-3 bg-slate-50/60">
                @foreach($activeTicket->messages as $msg)
                @php
                    $isEmp = ($msg->sender_type === 'employee');
                    $time = $msg->created_at->format('H:i');
                @endphp

                @if(!$isEmp && $msg->meta && !empty($msg->meta['is_credentials']))
                <!-- Admin Credential Card Bubble -->
                <div class="flex items-start justify-end gap-2.5 ml-auto max-w-[92%] sm:max-w-[80%]">
                    <div class="space-y-1.5">
                        <div class="p-3.5 rounded-2xl rounded-tr-none bg-white border-2 border-emerald-500/50 shadow-sm text-xs space-y-2">
                            <div class="flex items-center justify-between border-b border-emerald-100 pb-1.5">
                                <span class="font-bold text-emerald-900 flex items-center gap-1.5">
                                    <i class="fa-solid fa-key text-emerald-600"></i> Kredensial Akses Dikirim
                                </span>
                                <span class="text-[10px] text-slate-400">{{ $time }}</span>
                            </div>
                            <div class="p-2.5 bg-emerald-50/80 rounded-xl border border-emerald-200 text-xs space-y-1 font-mono">
                                <div><strong>Email:</strong> {{ $msg->meta['email'] }}</div>
                                <div><strong>Password:</strong> <span class="font-bold text-emerald-700 bg-white px-1.5 py-0.5 rounded border border-emerald-300">{{ $msg->meta['password'] }}</span></div>
                            </div>
                            <p class="text-[11px] text-slate-600 whitespace-pre-line leading-relaxed">
                                {{ $msg->message }}
                            </p>
                        </div>
                        <span class="text-[9px] text-slate-400 block text-right">Dikirim oleh {{ $msg->sender_name }}</span>
                    </div>
                    <div class="w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold text-xs shrink-0 mt-0.5 shadow-xs">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                </div>

                @elseif($isEmp)
                <!-- Employee Message Bubble -->
                <div class="flex items-start gap-2.5 max-w-[85%]">
                    <div class="w-8 h-8 rounded-lg bg-primary-50 text-primary border border-primary-200 flex items-center justify-center font-bold text-xs shrink-0 mt-0.5 shadow-2xs">
                        {{ strtoupper(substr($msg->sender_name, 0, 1)) }}
                    </div>
                    <div class="space-y-0.5">
                        <span class="text-[10px] font-bold text-slate-600 ml-1">{{ $msg->sender_name }} (Karyawan)</span>
                        <div class="p-3 rounded-2xl rounded-tl-none bg-white border border-slate-200/90 text-slate-800 text-xs shadow-2xs leading-relaxed whitespace-pre-line">
                            {{ $msg->message }}
                            <span class="text-[9px] text-slate-400 block text-right mt-1 font-mono">{{ $time }}</span>
                        </div>
                    </div>
                </div>

                @else
                <!-- Regular Admin Message Bubble -->
                <div class="flex items-start justify-end gap-2.5 ml-auto max-w-[85%]">
                    <div class="space-y-0.5 text-right">
                        <span class="text-[10px] font-bold text-primary mr-1">{{ $msg->sender_name }} (Admin)</span>
                        <div class="p-3 rounded-2xl rounded-tr-none bg-primary text-white text-xs shadow-xs text-left leading-relaxed whitespace-pre-line">
                            {{ $msg->message }}
                            <span class="text-[9px] text-blue-200 block text-right mt-1 font-mono">{{ $time }}</span>
                        </div>
                    </div>
                    <div class="w-8 h-8 rounded-lg bg-primary text-white flex items-center justify-center font-bold text-xs shrink-0 mt-0.5 shadow-xs">
                        <i class="fa-solid fa-user-shield"></i>
                    </div>
                </div>
                @endif
                @endforeach
            </div>

            <!-- Admin Reply Input Form Footer -->
            <div class="p-3.5 border-t border-slate-200 bg-white">
                <form method="POST" action="{{ route('admin.auth-chat.reply', $activeTicket->id) }}" class="flex items-center gap-2">
                    @csrf
                    <input type="text" name="message" required placeholder="Ketik balasan chat manual ke {{ $activeTicket->nama_karyawan }}..." class="flex-1 px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:border-primary focus:ring-1 focus:ring-primary/20 outline-none transition-all">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary hover:bg-primary/95 text-white font-bold text-xs shadow-sm flex items-center gap-1.5 transition-all">
                        <i class="fa-solid fa-paper-plane"></i>
                        <span>Kirim Balasan</span>
                    </button>
                </form>
            </div>

            @else
            <!-- Empty State when No Ticket Selected -->
            <div class="flex-1 flex flex-col items-center justify-center p-12 text-center text-slate-400">
                <div class="w-16 h-16 rounded-3xl bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400 text-2xl mb-3 shadow-inner">
                    <i class="fa-solid fa-comments"></i>
                </div>
                <h3 class="text-base font-extrabold text-slate-800">Pilih Tiket Permintaan</h3>
                <p class="text-xs text-slate-500 max-w-sm mt-1">
                    Silakan pilih salah satu tiket percakapan di kolom kiri untuk melihat riwayat pesan dan mengirimkan kredensial login ke karyawan.
                </p>
            </div>
            @endif
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const chatContainer = document.getElementById('adminChatMessagesList');
        if (chatContainer) {
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }

        // Auto polling admin
        const activeTicketId = {{ $activeTicket ? $activeTicket->id : 0 }};
        let lastMsgId = {{ $activeTicket && $activeTicket->messages->isNotEmpty() ? $activeTicket->messages->last()->id : 0 }};

        setInterval(async () => {
            try {
                const resp = await fetch(`{{ route('admin.auth-chat.poll') }}?active_id=${activeTicketId}&last_message_id=${lastMsgId}`);
                if (!resp.ok) return;
                const data = await resp.json();

                if (data.pending_count !== undefined) {
                    const badge = document.getElementById('badgePendingCount');
                    if (badge) badge.innerText = data.pending_count;
                }

                if (data.new_messages && data.new_messages.length > 0) {
                    location.reload(); // Muat ulang pesan terbaru
                }
            } catch (e) {
                // silent catch
            }
        }, 5000);
    });
</script>
@endsection
