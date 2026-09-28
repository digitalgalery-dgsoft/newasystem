<!DOCTYPE html>
<html lang="id" class="min-h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sesi Halaman Berakhir (419) - ASystem ESA Groups</title>
    
    <!-- Google Fonts: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Font Awesome 6 Pro -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

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
                            DEFAULT: '#0F52BA',
                            50: '#eef6ff',
                            600: '#0F52BA',
                            700: '#1d4ed8',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-screen font-sans antialiased text-slate-800 bg-gradient-to-br from-slate-950 via-blue-950 to-slate-900 flex flex-col justify-center items-center py-10 px-4 relative overflow-x-hidden">

    <!-- Ambient Glowing Orbs -->
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-amber-500/15 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-blue-600/20 rounded-full blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-md relative z-10 my-auto text-center space-y-6">
        
        <!-- Error Card -->
        <div class="bg-white rounded-3xl p-8 sm:p-10 shadow-2xl border border-slate-200/20 space-y-6 text-center">
            
            <div class="w-16 h-16 rounded-2xl bg-amber-50 border border-amber-200 text-amber-600 flex items-center justify-center text-3xl mx-auto shadow-inner">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>

            <div class="space-y-2">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                    <span>Status 419 • Sesi Kedaluwarsa</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Sesi Halaman Telah Berakhir</h1>
                <p class="text-xs text-slate-500 leading-relaxed max-w-sm mx-auto">
                    Demi keamanan akun dan pembaruan sistem terkini, sesi formulir Anda telah di-refresh. Silakan masuk kembali untuk melanjutkan aktivitas Anda.
                </p>
            </div>

            <div class="pt-2 space-y-3">
                <a href="{{ route('login') }}" class="w-full py-3.5 px-4 rounded-xl bg-primary hover:bg-primary-700 text-white font-bold text-xs sm:text-sm shadow-lg shadow-primary/25 hover:shadow-xl hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i>
                    <span>Masuk ke Akun Kembali</span>
                </a>
                
                <button type="button" onclick="window.location.reload()" class="w-full py-3 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors flex items-center justify-center gap-2">
                    <i class="fa-solid fa-rotate"></i>
                    <span>Muat Ulang Halaman</span>
                </button>
            </div>

            <div class="pt-4 border-t border-slate-100">
                <p class="text-[11px] text-slate-400">
                    Mengalihkan otomatis ke login dalam <span id="countdown" class="font-bold text-primary">5</span> detik...
                </p>
            </div>

        </div>

        <p class="text-xs text-slate-400">
            &copy; {{ date('Y') }} ASystem Support System • ESA Groups
        </p>
    </div>

    <script>
        let seconds = 5;
        const countEl = document.getElementById('countdown');
        const timer = setInterval(() => {
            seconds--;
            if (countEl) countEl.innerText = seconds;
            if (seconds <= 0) {
                clearInterval(timer);
                window.location.href = "{{ route('login') }}";
            }
        }, 1000);
    </script>
</body>
</html>
