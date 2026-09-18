<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'CBT Online Recruitment | ESA Groups')</title>
    
    <!-- Google Fonts: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Font Awesome 6 Pro & Boxicons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js"></script>

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
                            DEFAULT: '#0F52BA', // Sapphire Blue ESA Groups
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            500: '#3b82f6',
                            600: '#0F52BA',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#0b3275',
                        },
                    }
                }
            }
        }
    </script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            -webkit-tap-highlight-color: transparent;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        .safe-area-bottom {
            padding-bottom: env(safe-area-inset-bottom, 1rem);
        }
    </style>
    @stack('styles')
</head>
<body class="min-h-full flex flex-col antialiased selection:bg-primary-500 selection:text-white pb-20 md:pb-8">
    @include('partials.page-loader')

    <!-- TOP HEADER / NAVBAR -->
    <header class="bg-slate-900 text-white sticky top-0 z-40 shadow-md border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                
                <!-- Brand & Logo -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('cbt.dashboard') }}" class="flex items-center gap-2.5 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-primary-700 via-primary-600 to-blue-400 flex items-center justify-center text-white shadow-lg shadow-blue-500/30 group-hover:scale-105 transition-transform">
                            <i class="fa-solid fa-graduation-cap text-lg"></i>
                        </div>
                        <div>
                            <span class="text-base font-extrabold tracking-wider block leading-tight text-white">ESA CBT PRO</span>
                            <span class="text-[10px] font-semibold text-blue-300 tracking-widest block uppercase">Support System ESA Groups</span>
                        </div>
                    </a>
                </div>

                <!-- User Info & Logout (Desktop) -->
                @if(session()->has('cbt_candidate_id'))
                    @php
                        $activeCandidate = \App\Models\Candidate::find(session('cbt_candidate_id'));
                    @endphp
                    <div class="flex items-center gap-3">
                        <div class="hidden sm:flex flex-col text-right">
                            <span class="text-xs font-bold text-slate-200">{{ session('cbt_candidate_name') }}</span>
                            <span class="text-[10px] text-slate-400 font-mono">NIK: {{ session('cbt_candidate_nik') }}</span>
                        </div>

                        <!-- Candidate Avatar -->
                        <div class="relative">
                            @if($activeCandidate && $activeCandidate->photo_path)
                                <img src="{{ $activeCandidate->photo_url }}" alt="Foto Profil" class="w-10 h-10 rounded-full object-cover ring-2 ring-primary-500 shadow" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode(session('cbt_candidate_name', 'K')) }}&background=0F52BA&color=fff&size=128';">
                            @else
                                <div class="w-10 h-10 rounded-full bg-slate-800 text-slate-300 flex items-center justify-center ring-2 ring-slate-700 font-bold text-sm">
                                    {{ substr(session('cbt_candidate_name', 'K'), 0, 1) }}
                                </div>
                            @endif
                            <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 border-2 border-slate-900 rounded-full"></span>
                        </div>

                        <!-- Desktop Logout Button -->
                        <div class="hidden md:flex items-center ml-2 pl-3 border-l border-slate-800">
                            <a href="{{ route('cbt.logout') }}" onclick="return confirm('Apakah Anda yakin ingin keluar dari sesi tes online ini?')" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-rose-400 hover:text-white hover:bg-rose-600/20 border border-rose-500/30 transition-all flex items-center gap-1.5">
                                <i class="fa-solid fa-right-from-bracket"></i>
                                <span>Keluar</span>
                            </a>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </header>

    <!-- FLASH MESSAGES & ALERTS -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl p-4 flex items-center gap-3 shadow-sm mb-4 animate-fade-in" role="alert">
                <i class="fa-solid fa-circle-check text-emerald-600 text-lg flex-shrink-0"></i>
                <div class="text-xs sm:text-sm font-medium">{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-rose-50 border border-rose-200 text-rose-800 rounded-xl p-4 flex items-center gap-3 shadow-sm mb-4 animate-fade-in" role="alert">
                <i class="fa-solid fa-circle-exclamation text-rose-600 text-lg flex-shrink-0"></i>
                <div class="text-xs sm:text-sm font-medium">{{ session('error') }}</div>
            </div>
        @endif

        @if(session('warning'))
            <div class="bg-amber-50 border border-amber-200 text-amber-800 rounded-xl p-4 flex items-center gap-3 shadow-sm mb-4 animate-fade-in" role="alert">
                <i class="fa-solid fa-triangle-exclamation text-amber-600 text-lg flex-shrink-0"></i>
                <div class="text-xs sm:text-sm font-medium">{{ session('warning') }}</div>
            </div>
        @endif
    </div>

    <!-- MAIN CONTENT AREA -->
    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6 w-full asystem-page-enter">
        @yield('content')
    </main>

    <!-- MOBILE STICKY BOTTOM NAVIGATION BAR -->
    @if(session()->has('cbt_candidate_id'))
        <nav class="fixed bottom-0 left-0 z-50 w-full h-16 bg-white/95 backdrop-blur-md border-t border-slate-200 shadow-[0_-4px_15px_rgba(0,0,0,0.06)] md:hidden">
            <div class="grid h-full max-w-md grid-cols-3 mx-auto font-medium safe-area-bottom">
                
                <!-- Home / Dashboard -->
                <a href="{{ route('cbt.dashboard') }}" class="inline-flex flex-col items-center justify-center px-4 group {{ request()->routeIs('cbt.dashboard') ? 'text-primary' : 'text-slate-500 hover:text-primary' }}">
                    <i class="fa-solid fa-house text-lg mb-1 transition-transform group-hover:scale-110 {{ request()->routeIs('cbt.dashboard') ? 'text-primary' : 'text-slate-400' }}"></i>
                    <span class="text-[11px] font-bold tracking-tight">Beranda</span>
                </a>

                <!-- Profile -->
                <a href="{{ route('cbt.profile') }}" class="inline-flex flex-col items-center justify-center px-4 group {{ request()->routeIs('cbt.profile') ? 'text-primary' : 'text-slate-500 hover:text-primary' }}">
                    <i class="fa-solid fa-user-pen text-lg mb-1 transition-transform group-hover:scale-110 {{ request()->routeIs('cbt.profile') ? 'text-primary' : 'text-slate-400' }}"></i>
                    <span class="text-[11px] font-bold tracking-tight">Profil</span>
                </a>

                <!-- Logout -->
                <a href="{{ route('cbt.logout') }}" onclick="return confirm('Apakah Anda yakin ingin keluar dari sesi tes?')" class="inline-flex flex-col items-center justify-center px-4 text-rose-500 hover:text-rose-700 group">
                    <i class="fa-solid fa-right-from-bracket text-lg mb-1 transition-transform group-hover:scale-110 text-rose-500"></i>
                    <span class="text-[11px] font-bold tracking-tight">Keluar</span>
                </a>

            </div>
        </nav>
    @endif

    <!-- FOOTER (DESKTOP) -->
    <footer class="hidden md:block bg-white border-t border-slate-200 py-4 text-center text-xs text-slate-500 mt-auto">
        <div class="max-w-7xl mx-auto px-4">
            &copy; {{ date('Y') }} <strong>ASystem Portal - ESA Groups</strong>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
