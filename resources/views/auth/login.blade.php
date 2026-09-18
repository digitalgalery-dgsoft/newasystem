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
                        Gunakan <strong>Email Karyawan</strong> terdaftar dengan kata sandi default <strong>Tanggal Lahir (DDMMYYYY)</strong>. Karyawan RateCard harus memiliki izin aktif dari Admin HR.
                    </p>
                </div>
            </div>

            <!-- Form -->
            <form action="{{ route('login.post') }}" method="POST" class="space-y-4" id="loginForm">
                @csrf

                <!-- Email Input -->
                <div class="space-y-1.5">
                    <label for="email" class="text-xs font-bold text-slate-700 block">Email Pengguna</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-regular fa-envelope text-xs"></i>
                        </div>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="karyawan@arina.co.id atau admin@asystem.co.id" required autofocus class="w-full pl-9 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                    </div>
                </div>

                <!-- Password Input -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label for="password" class="text-xs font-bold text-slate-700">Kata Sandi</label>
                        <a href="https://wa.me/6283139797309?text=Halo%20Admin,%20saya%20lupa%20kata%20sandi%20login%20ASystem" target="_blank" class="text-[11px] font-semibold text-primary hover:underline">
                            Lupa kata sandi?
                        </a>
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
    </script>
</body>
</html>
