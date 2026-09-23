<!DOCTYPE html>
<html lang="id" class="min-h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Karyawan - ASystem Support System ESA Groups</title>
    
    <!-- Google Fonts: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Font Awesome 6 Pro & Boxicons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css">

    <!-- Tailwind CSS Play CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            DEFAULT: '#0F52BA', // Sapphire Blue
                            50: '#eef6ff',
                            100: '#d9ebff',
                            200: '#bce0fd',
                            500: '#2563eb',
                            600: '#0F52BA',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-screen font-sans antialiased text-slate-800 bg-gradient-to-br from-slate-950 via-blue-950 to-slate-900 flex flex-col justify-center items-center py-10 sm:py-16 px-4 relative overflow-x-hidden">

    <!-- Ambient Glowing Orbs -->
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-blue-600/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-indigo-600/20 rounded-full blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-md relative z-10 my-auto space-y-6">
        <!-- Top Brand Header -->
        <div class="text-center space-y-2">
            <a href="{{ route('home.index') }}" class="inline-flex items-center gap-3 group">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 via-primary to-indigo-600 text-white flex items-center justify-center font-black text-2xl shadow-xl shadow-primary/30 group-hover:scale-105 transition-transform">
                    A
                </div>
            </a>
            <h1 class="text-2xl font-black text-white tracking-tight">ASystem Support System</h1>
            <p class="text-xs text-slate-400 font-medium">Integrated Recruitment &amp; HR System for ESA Groups</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-3xl p-7 sm:p-9 shadow-2xl border border-slate-200/20 space-y-6">
            <div class="space-y-1 text-center sm:text-left">
                <h2 class="text-lg sm:text-xl font-black text-slate-800">Masuk ke Akun Anda</h2>
                <p class="text-xs text-slate-400">Silakan masukkan email dan kata sandi Anda untuk mengakses dashboard.</p>
            </div>

            <!-- Flash Error Alerts -->
            @if($errors->any())
                <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs flex items-center gap-2.5">
                    <i class="fa-solid fa-triangle-exclamation text-rose-500 shrink-0 text-sm"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            @if(session('info'))
                <div class="p-3.5 rounded-2xl bg-blue-50 border border-blue-200 text-primary text-xs flex items-center gap-2.5">
                    <i class="fa-solid fa-circle-info text-primary shrink-0 text-sm"></i>
                    <span>{{ session('info') }}</span>
                </div>
            @endif

            @if(session('success'))
                <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs flex items-center gap-2.5">
                    <i class="fa-solid fa-circle-check text-emerald-600 shrink-0 text-sm"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Employee Login Guidance Card -->
            <div class="p-3.5 rounded-2xl bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200/80 text-blue-950 text-xs flex items-start gap-2.5 shadow-sm">
                <i class="fa-solid fa-circle-info text-primary shrink-0 text-sm mt-0.5"></i>
                <div class="space-y-0.5">
                    <div class="font-bold text-xs text-blue-900 flex items-center gap-1.5">
                        <span>Akses Login Karyawan</span>
                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-blue-200/80 text-blue-800">Inhouse &amp; RateCard</span>
                    </div>
                    <p class="text-[11px] text-slate-600 leading-relaxed">
                        Gunakan <strong>Email atau NIK Karyawan</strong> terdaftar dengan kata sandi default <strong>Tanggal Lahir (DDMMYYYY)</strong>. Karyawan RateCard harus memiliki izin aktif dari Admin HR.
                    </p>
                </div>
            </div>

            <!-- Form -->
            <form action="{{ route('login.post') }}" method="POST" class="space-y-4" id="loginForm">
                @csrf

                <!-- Email or NIK Input -->
                <div class="space-y-1.5">
                    <label for="email" class="text-xs font-bold text-slate-700 block">Email atau NIK Pengguna</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-regular fa-user text-xs"></i>
                        </div>
                        <input type="text" id="email" name="email" value="{{ old('email') }}" placeholder="Email (contoh: ithelpdesk@arina.co.id) atau NIK" required autofocus class="w-full pl-9 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                    </div>
                </div>

                <!-- Password Input -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label for="password" class="text-xs font-bold text-slate-700">Kata Sandi</label>
                        <button type="button" onclick="openForgotPasswordChat()" class="text-[11px] font-bold text-primary hover:text-primary-700 hover:underline inline-flex items-center gap-1 cursor-pointer transition-colors">
                            <i class="fa-solid fa-headset text-xs text-primary"></i>
                            <span>Lupa kata sandi?</span>
                        </button>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-key text-xs"></i>
                        </div>
                        <input type="password" id="password" name="password" placeholder="Tanggal lahir (DDMMYYYY) atau kata sandi" required class="w-full pl-9 pr-10 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                        <button type="button" onclick="togglePasswordVisibility()" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition-colors">
                            <i class="fa-regular fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between pt-1">
                    <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-medium text-slate-600">
                        <input type="checkbox" name="remember" class="rounded text-primary focus:ring-primary">
                        <span>Ingat Saya</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" id="btnSubmitLogin" class="w-full py-3.5 rounded-xl bg-primary hover:bg-primary/90 text-white font-black text-xs sm:text-sm shadow-lg shadow-primary/25 hover:shadow-xl hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                        <i class="fa-solid fa-arrow-right-to-bracket"></i>
                        <span>Masuk ke Sistem</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Link ke Portal CBT Peserta Ujian -->
        <div class="text-center">
            <a href="{{ route('cbt.login') }}" class="inline-flex items-center gap-2 text-xs font-bold text-blue-200 hover:text-white bg-white/10 hover:bg-white/20 px-4 py-2.5 rounded-xl border border-white/20 transition-all shadow-sm">
                <i class="fa-solid fa-laptop-code text-blue-300"></i>
                <span>Peserta Ujian? Masuk ke Portal CBT Online &rarr;</span>
            </a>
        </div>

        <!-- Back to Home Link -->
        <div class="text-center">
            <a href="{{ route('home.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-400 hover:text-white transition-colors">
                <i class="fa-solid fa-arrow-left text-[10px]"></i>
                <span>Kembali ke Halaman Depan Web</span>
            </a>
        </div>
    </div>

    <!-- MODAL LIVE CHAT BANTUAN LOGIN & RESET PASSWORD KE ADMINISTRATOR -->
    <div id="forgotPasswordModal" class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-950/75 backdrop-blur-sm hidden transition-all">
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl border border-slate-200/50 flex flex-col overflow-hidden max-h-[92vh] sm:max-h-[85vh] animate-in fade-in zoom-in-95 duration-200">
            
            <!-- Modal Header -->
            <div class="p-4 sm:p-5 bg-gradient-to-r from-blue-900 via-primary to-indigo-900 text-white flex items-center justify-between shadow-md">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-white/15 backdrop-blur-md flex items-center justify-center text-lg shadow-sm border border-white/20">
                        <i class="fa-solid fa-headset text-blue-200"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-sm font-bold tracking-tight">Bantuan Login &amp; Reset Sandi</h3>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span> Live Admin
                            </span>
                        </div>
                        <p class="text-[11px] text-blue-200">Kirim permintaan akses langsung ke Administrator HR</p>
                    </div>
                </div>
                <button type="button" onclick="closeForgotPasswordChat()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-all">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Modal Body (STEP 1: INPUT NIK & VERIFIKASI ODOO) -->
            <div id="chatStepInput" class="p-6 space-y-4 overflow-y-auto">
                <div class="p-3.5 rounded-2xl bg-blue-50 border border-blue-200/80 text-blue-900 text-xs flex items-start gap-2.5">
                    <i class="fa-solid fa-circle-info text-primary shrink-0 text-sm mt-0.5"></i>
                    <div class="space-y-0.5">
                        <span class="font-bold block text-blue-950">Verifikasi Otomatis Odoo ERP</span>
                        <p class="text-[11px] text-slate-600 leading-relaxed">
                            Masukkan NIK Anda. Sistem akan memverifikasi keaktifan data di server Odoo seluruh entitas (AMK, AKP, ATK, ABO, ATB). Jika aktif dan belum terdaftar, akun Anda akan otomatis terintegrasi.
                        </p>
                    </div>
                </div>

                <form id="formCheckNik" onsubmit="handleCheckNik(event)" class="space-y-3.5">
                    <div class="space-y-1.5">
                        <label for="chatNikInput" class="text-xs font-bold text-slate-700 block">
                            Nomor Induk Karyawan (NIK) <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-regular fa-id-card text-xs"></i>
                            </div>
                            <input type="text" id="chatNikInput" required placeholder="Masukkan NIK 16 digit Anda..." class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono font-semibold text-slate-800 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label for="chatNikMessage" class="text-xs font-bold text-slate-700 block">
                            Pesan Tambahan (Opsional)
                        </label>
                        <textarea id="chatNikMessage" rows="2" placeholder="Contoh: Halo Admin, saya lupa kata sandi login ASystem..." class="w-full px-3.5 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all resize-none"></textarea>
                    </div>

                    <!-- Error Alert -->
                    <div id="chatNikError" class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs hidden flex items-start gap-2">
                        <i class="fa-solid fa-triangle-exclamation text-rose-500 shrink-0 text-sm mt-0.5"></i>
                        <span id="chatNikErrorText"></span>
                    </div>

                    <button type="submit" id="btnSubmitCheckNik" class="w-full py-3 rounded-xl bg-gradient-to-r from-primary to-indigo-600 hover:from-primary/95 hover:to-indigo-700 text-white font-bold text-xs shadow-md shadow-primary/25 flex items-center justify-center gap-2 transition-all">
                        <i class="fa-solid fa-paper-plane"></i>
                        <span>Mulai Chat &amp; Minta Akses</span>
                    </button>
                </form>
            </div>

            <!-- Modal Body (STEP 2: ACTIVE LIVE CHAT ROOM) -->
            <div id="chatStepRoom" class="hidden flex-1 flex flex-col min-h-0 bg-slate-50/50">
                
                <!-- Employee Summary Header -->
                <div class="px-4 py-2.5 bg-white border-b border-slate-200 flex items-center justify-between gap-3 text-xs shadow-xs">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-8 h-8 rounded-lg bg-primary-50 text-primary flex items-center justify-center font-bold text-xs shrink-0 border border-primary-200">
                            <span id="chatRoomInitials">K</span>
                        </div>
                        <div class="truncate">
                            <div class="font-bold text-slate-800 truncate" id="chatRoomName">-</div>
                            <div class="text-[10px] text-slate-500 font-mono truncate" id="chatRoomMeta">-</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-1 shrink-0">
                        <span id="chatRoomStatusBadge" class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-800">
                            Menunggu Balasan
                        </span>
                    </div>
                </div>

                <!-- Chat Messages Scroll Container -->
                <div id="chatMessagesList" class="flex-1 p-4 overflow-y-auto space-y-3 min-h-[260px] max-h-[400px]">
                    <!-- Dynamically populated bubbles -->
                </div>

                <!-- Message Input Footer -->
                <div class="p-3 bg-white border-t border-slate-200">
                    <form id="formSendChatReply" onsubmit="handleSendChatReply(event)" class="flex items-center gap-2">
                        <input type="text" id="chatReplyInput" placeholder="Ketik pesan ke admin..." required class="flex-1 px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                        <button type="submit" id="btnSendChatReply" class="px-4 py-2.5 rounded-xl bg-primary hover:bg-primary/90 text-white font-bold text-xs shadow-sm flex items-center gap-1.5 transition-all">
                            <i class="fa-solid fa-paper-plane"></i>
                            <span class="hidden sm:inline">Kirim</span>
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <script>
    function togglePasswordVisibility() {
        const pass = document.getElementById('password');
        const icon = document.getElementById('eyeIcon');
        if (pass.type === 'password') {
            pass.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            pass.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // =========================================================================
    // LIVE CHAT BANTUAN LOGIN & FORGOT PASSWORD LOGIC
    // =========================================================================
    let chatSessionToken = null;
    let chatLastMessageId = 0;
    let chatPollInterval = null;

    function openForgotPasswordChat() {
        document.getElementById('forgotPasswordModal').classList.remove('hidden');
        document.getElementById('chatNikInput').focus();
        
        // Cek apakah ada sesi chat tersimpan di sessionStorage
        const savedToken = sessionStorage.getItem('asystem_auth_chat_token');
        if (savedToken && !chatSessionToken) {
            chatSessionToken = savedToken;
            switchToChatRoom();
            pollChatMessages();
            startPolling();
        }
    }

    function closeForgotPasswordChat() {
        document.getElementById('forgotPasswordModal').classList.add('hidden');
        stopPolling();
    }

    function startPolling() {
        stopPolling();
        chatPollInterval = setInterval(pollChatMessages, 3000);
    }

    function stopPolling() {
        if (chatPollInterval) {
            clearInterval(chatPollInterval);
            chatPollInterval = null;
        }
    }

    async function handleCheckNik(event) {
        event.preventDefault();
        const nikInput = document.getElementById('chatNikInput');
        const msgInput = document.getElementById('chatNikMessage');
        const btn = document.getElementById('btnSubmitCheckNik');
        const errBox = document.getElementById('chatNikError');
        const errText = document.getElementById('chatNikErrorText');

        const nik = nikInput.value.trim();
        const msg = msgInput.value.trim();

        if (!nik) return;

        errBox.classList.add('hidden');
        const origBtnText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = `<i class="fa-solid fa-circle-notch fa-spin text-white"></i><span>Mengecek di Odoo ERP...</span>`;

        try {
            const resp = await fetch("{{ route('auth.chat.check-nik') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ nik: nik, message: msg })
            });

            const data = await resp.json();

            if (!resp.ok || !data.success) {
                errBox.classList.remove('hidden');
                errText.innerText = data.message || 'Terjadi kesalahan saat memverifikasi NIK.';
                btn.disabled = false;
                btn.innerHTML = origBtnText;
                return;
            }

            // Sesi chat berhasil dibuat / dilanjutkan
            chatSessionToken = data.session_token;
            sessionStorage.setItem('asystem_auth_chat_token', chatSessionToken);

            // Set info karyawan
            setupRoomHeader(data.employee, data.status);
            renderMessages(data.messages || []);

            switchToChatRoom();
            startPolling();

        } catch (e) {
            errBox.classList.remove('hidden');
            errText.innerText = 'Koneksi ke server gagal. Pastikan jaringan internet Anda aktif.';
            btn.disabled = false;
            btn.innerHTML = origBtnText;
        }
    }

    function switchToChatRoom() {
        document.getElementById('chatStepInput').classList.add('hidden');
        document.getElementById('chatStepRoom').classList.remove('hidden');
    }

    function setupRoomHeader(emp, status) {
        if (!emp) return;
        document.getElementById('chatRoomName').innerText = emp.nama || 'Karyawan';
        document.getElementById('chatRoomMeta').innerText = `${emp.nik} • ${emp.entitas || 'ASystem'} (${emp.tipe_karyawan || 'Inhouse'})`;
        document.getElementById('chatRoomInitials').innerText = (emp.nama || 'K').charAt(0).toUpperCase();

        const badge = document.getElementById('chatRoomStatusBadge');
        if (status === 'resolved') {
            badge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800';
            badge.innerHTML = '<i class="fa-solid fa-check text-[9px]"></i> Akses Terkirim';
        } else if (status === 'replied') {
            badge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-100 text-blue-800';
            badge.innerHTML = '<i class="fa-solid fa-reply text-[9px]"></i> Dibalas Admin';
        } else {
            badge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 animate-pulse';
            badge.innerHTML = '<i class="fa-solid fa-clock text-[9px]"></i> Menunggu Balasan';
        }
    }

    function renderMessages(messages) {
        const container = document.getElementById('chatMessagesList');
        container.innerHTML = '';

        if (!messages || messages.length === 0) {
            container.innerHTML = `
                <div class="text-center py-8 text-slate-400 text-xs">
                    <i class="fa-regular fa-comments text-2xl text-slate-300 mb-1.5 block"></i>
                    Belum ada percakapan. Ketik pesan Anda di bawah.
                </div>
            `;
            return;
        }

        messages.forEach(msg => {
            appendMessageBubble(msg);
        });

        scrollChatToBottom();
    }

    function appendMessageBubble(msg) {
        const container = document.getElementById('chatMessagesList');
        const isEmployee = (msg.sender_type === 'employee');

        chatLastMessageId = Math.max(chatLastMessageId, msg.id);

        let bubbleHtml = '';
        const timeStr = msg.created_at ? new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : '';

        // Jika pesan admin mengandung kredensial
        if (!isEmployee && msg.meta && msg.meta.is_credentials) {
            const meta = msg.meta;
            bubbleHtml = `
                <div class="flex items-start gap-2.5 max-w-[92%] sm:max-w-[85%]">
                    <div class="w-7 h-7 rounded-lg bg-primary text-white flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div class="space-y-1.5">
                        <div class="p-3.5 rounded-2xl rounded-tl-none bg-white border-2 border-emerald-500/40 shadow-sm text-xs space-y-2.5">
                            <div class="flex items-center justify-between border-b border-emerald-100 pb-1.5">
                                <span class="font-bold text-emerald-900 flex items-center gap-1.5">
                                    <i class="fa-solid fa-key text-emerald-600"></i> Kredensial Login Resmi
                                </span>
                                <span class="text-[10px] text-slate-400">${timeStr}</span>
                            </div>

                            <div class="p-2.5 bg-emerald-50/70 rounded-xl border border-emerald-200/80 space-y-1.5 text-xs">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] uppercase font-bold text-slate-500">Email / NIK:</span>
                                    <div class="flex items-center gap-1">
                                        <code class="font-mono font-bold text-slate-900 text-xs">${meta.email}</code>
                                        <button type="button" onclick="copyToClipboard('${meta.email}', 'Email')" class="text-slate-400 hover:text-emerald-700 p-0.5" title="Salin Email">
                                            <i class="fa-regular fa-copy text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] uppercase font-bold text-slate-500">Kata Sandi:</span>
                                    <div class="flex items-center gap-1">
                                        <code class="font-mono font-bold text-emerald-700 bg-white px-1.5 py-0.5 rounded border border-emerald-300 text-xs">${meta.password}</code>
                                        <button type="button" onclick="copyToClipboard('${meta.password}', 'Password')" class="text-slate-400 hover:text-emerald-700 p-0.5" title="Salin Kata Sandi">
                                            <i class="fa-regular fa-copy text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <button type="button" onclick="useCredentialsAndLogin('${meta.email}', '${meta.password}')" class="w-full py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-xs shadow-sm flex items-center justify-center gap-1.5 transition-all">
                                <i class="fa-solid fa-arrow-right-to-bracket"></i>
                                <span>Salin Kredensial &amp; Langsung Masuk</span>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        } else if (isEmployee) {
            bubbleHtml = `
                <div class="flex items-start justify-end gap-2.5 ml-auto max-w-[85%]">
                    <div class="p-3 rounded-2xl rounded-tr-none bg-primary text-white text-xs shadow-sm leading-relaxed whitespace-pre-line">
                        ${escapeHtml(msg.message)}
                        <span class="text-[9px] text-blue-200 block text-right mt-1 font-medium">${timeStr}</span>
                    </div>
                </div>
            `;
        } else {
            bubbleHtml = `
                <div class="flex items-start gap-2.5 max-w-[85%]">
                    <div class="w-7 h-7 rounded-lg bg-primary text-white flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">
                        <i class="fa-solid fa-user-shield"></i>
                    </div>
                    <div class="p-3 rounded-2xl rounded-tl-none bg-white border border-slate-200 text-slate-800 text-xs shadow-xs leading-relaxed whitespace-pre-line">
                        <div class="font-bold text-[10px] text-primary mb-0.5">${msg.sender_name || 'Admin HR'}</div>
                        ${escapeHtml(msg.message)}
                        <span class="text-[9px] text-slate-400 block text-right mt-1 font-medium">${timeStr}</span>
                    </div>
                </div>
            `;
        }

        container.insertAdjacentHTML('beforeend', bubbleHtml);
    }

    async function pollChatMessages() {
        if (!chatSessionToken) return;

        try {
            const resp = await fetch(`{{ route('auth.chat.poll') }}?session_token=${encodeURIComponent(chatSessionToken)}&last_message_id=${chatLastMessageId}`);
            if (!resp.ok) return;

            const data = await resp.json();
            if (data.success && data.messages && data.messages.length > 0) {
                data.messages.forEach(msg => {
                    appendMessageBubble(msg);
                });
                scrollChatToBottom();
            }

            if (data.status) {
                const badge = document.getElementById('chatRoomStatusBadge');
                if (data.status === 'resolved') {
                    badge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800';
                    badge.innerHTML = '<i class="fa-solid fa-check text-[9px]"></i> Akses Terkirim';
                } else if (data.status === 'replied') {
                    badge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-100 text-blue-800';
                    badge.innerHTML = '<i class="fa-solid fa-reply text-[9px]"></i> Dibalas Admin';
                }
            }
        } catch (e) {
            // Polling silent catch
        }
    }

    async function handleSendChatReply(event) {
        event.preventDefault();
        const input = document.getElementById('chatReplyInput');
        const btn = document.getElementById('btnSendChatReply');
        const text = input.value.trim();

        if (!text || !chatSessionToken) return;

        input.value = '';
        btn.disabled = true;

        try {
            const resp = await fetch("{{ route('auth.chat.send-message') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    session_token: chatSessionToken,
                    message: text
                })
            });

            const data = await resp.json();
            btn.disabled = false;

            if (data.success && data.message) {
                appendMessageBubble(data.message);
                scrollChatToBottom();
            }
        } catch (e) {
            btn.disabled = false;
        }
    }

    function scrollChatToBottom() {
        const container = document.getElementById('chatMessagesList');
        container.scrollTop = container.scrollHeight;
    }

    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    function copyToClipboard(text, label) {
        navigator.clipboard.writeText(text).then(() => {
            alert(`${label} berhasil disalin ke clipboard!`);
        });
    }

    function useCredentialsAndLogin(email, password) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = password;
        closeForgotPasswordChat();
        
        // Focus tombol login
        const btnLogin = document.getElementById('btnSubmitLogin');
        btnLogin.focus();
        btnLogin.classList.add('ring-4', 'ring-emerald-500/50');
        setTimeout(() => btnLogin.classList.remove('ring-4', 'ring-emerald-500/50'), 1500);
    }
    </script>
</body>
</html>
