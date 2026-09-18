<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ASystem - Career & Integrated Support System ESA Groups')</title>
    
    <!-- Google Fonts: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Font Awesome 6 Pro & Boxicons & Remix Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css">

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
                            DEFAULT: '#0F52BA', // Sapphire Blue
                            50: '#eef6ff',
                            100: '#d9ebff',
                            200: '#bce0fd',
                            500: '#2563eb',
                            600: '#0F52BA',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        },
                        secondary: '#475569',
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
        }
        .btn-att-primary {
            background-color: #0F52BA;
            color: #ffffff;
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: 0.75rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-att-primary:hover {
            background-color: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(15, 82, 186, 0.2);
        }
        .btn-att-secondary {
            background-color: #ffffff;
            color: #334155;
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: 0.75rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-att-secondary:hover {
            background-color: #f8fafc;
            color: #0F52BA;
            border-color: #cbd5e1;
        }
    </style>
</head>
<body class="flex flex-col min-h-screen antialiased text-slate-800" x-data="{ mobileMenuOpen: false }">
    @include('partials.page-loader')

    <!-- PUBLIC TOP BAR -->
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200/80 transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Brand Logo -->
                <a href="{{ route('home.index') }}" class="flex items-center gap-3 group">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-blue-700 via-primary to-indigo-800 text-white flex items-center justify-center font-black text-xl shadow-lg shadow-primary/25 group-hover:scale-105 transition-all">
                        A
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-xl font-black tracking-tight text-slate-900 group-hover:text-primary transition-colors">ASystem</span>
                            <span class="px-1.5 py-0.5 rounded text-[10px] font-black bg-blue-50 text-primary uppercase tracking-wider">Career</span>
                        </div>
                        <p class="text-[11px] font-semibold text-slate-400 -mt-1 tracking-wide">Support System ESA Groups</p>
                    </div>
                </a>

                <!-- Desktop Nav Navigation -->
                <nav class="hidden md:flex items-center gap-8">
                    <a href="{{ route('home.index') }}" class="text-xs font-bold {{ request()->routeIs('home.index') ? 'text-primary' : 'text-slate-600 hover:text-primary' }} transition-colors">
                        Beranda
                    </a>
                    <a href="{{ route('job.public') }}" class="text-xs font-bold {{ request()->is('job*') ? 'text-primary' : 'text-slate-600 hover:text-primary' }} transition-colors flex items-center gap-1.5">
                        <span>Lowongan Kerja</span>
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    </a>
                    <a href="{{ route('home.index') }}#about" class="text-xs font-bold text-slate-600 hover:text-primary transition-colors">
                        Tentang Kami
                    </a>
                    <a href="{{ route('home.index') }}#features" class="text-xs font-bold text-slate-600 hover:text-primary transition-colors">
                        Fitur & Sistem
                    </a>
                    <a href="{{ route('home.index') }}#contact" class="text-xs font-bold text-slate-600 hover:text-primary transition-colors">
                        Kontak
                    </a>
                </nav>

                <!-- Right Action Buttons -->
                <div class="hidden sm:flex items-center gap-3">
                    @auth
                        <a href="{{ route('fitur.index') }}" class="btn-att-secondary text-xs">
                            <i class="fa-solid fa-gauge text-primary"></i>
                            <span>Dashboard</span>
                        </a>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-3 py-2 rounded-xl text-xs font-bold text-slate-500 hover:text-rose-600 hover:bg-rose-50 transition-colors">
                                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('job.public') }}" class="btn-att-primary text-xs">
                            <i class="fa-solid fa-briefcase"></i>
                            <span>Cari Lowongan</span>
                        </a>
                        <a href="https://asystem.co.id/v3/login" target="_blank" class="btn-att-secondary text-xs border border-indigo-200 text-indigo-700 hover:bg-indigo-50">
                            <i class="fa-solid fa-arrow-up-right-from-square text-indigo-500"></i>
                            <span>ASystem V3</span>
                        </a>
                        <a href="{{ route('login') }}" class="btn-att-secondary text-xs">
                            <i class="fa-solid fa-arrow-right-to-bracket text-slate-400"></i>
                            <span>Talent Pool Login</span>
                        </a>
                    @endauth
                </div>

                <!-- Mobile Hamburger Button -->
                <div class="flex md:hidden items-center gap-2">
                    <a href="{{ route('job.public') }}" class="px-3 py-1.5 rounded-xl bg-primary text-white text-xs font-bold">
                        Job
                    </a>
                    <button type="button" @click="mobileMenuOpen = !mobileMenuOpen" class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-lg">
                        <i class="fa-solid" :class="mobileMenuOpen ? 'fa-xmark' : 'fa-bars'"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Drawer -->
        <div x-show="mobileMenuOpen" x-transition class="md:hidden border-t border-slate-100 bg-white px-5 py-4 space-y-3 shadow-lg">
            <a href="{{ route('home.index') }}" class="block text-xs font-bold text-slate-700 py-1.5">Beranda</a>
            <a href="{{ route('job.public') }}" class="block text-xs font-bold text-primary py-1.5">Lowongan Kerja (Aktif)</a>
            <a href="{{ route('home.index') }}#about" class="block text-xs font-bold text-slate-700 py-1.5">Tentang Kami</a>
            <a href="{{ route('home.index') }}#features" class="block text-xs font-bold text-slate-700 py-1.5">Fitur & Layanan</a>
            <a href="{{ route('home.index') }}#contact" class="block text-xs font-bold text-slate-700 py-1.5">Kontak</a>
            <div class="pt-3 border-t border-slate-100 flex flex-col gap-2">
                @auth
                    <a href="{{ route('fitur.index') }}" class="w-full py-2.5 rounded-xl bg-primary text-white text-xs font-bold text-center">Ke Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="w-full py-2.5 rounded-xl bg-primary text-white text-xs font-bold text-center flex items-center justify-center gap-2 shadow-sm">
                        <i class="fa-solid fa-arrow-right-to-bracket"></i>
                        <span>Talent Pool / Rekrutmen - Login Disini</span>
                    </a>
                    <a href="https://asystem.co.id/v3/login" target="_blank" class="w-full py-2.5 rounded-xl bg-indigo-50 text-indigo-700 text-xs font-bold text-center flex items-center justify-center gap-2 border border-indigo-200">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        <span>Fitur Lain - Akses ASystem V3</span>
                    </a>
                    <a href="{{ route('cbt.login') }}" class="w-full py-2.5 rounded-xl bg-slate-100 text-slate-800 text-xs font-bold text-center flex items-center justify-center gap-2">
                        <i class="fa-solid fa-laptop-code text-slate-500"></i>
                        <span>Ujian Online CBT</span>
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- MAIN PAGE CONTENT -->
    <main class="flex-1 asystem-page-enter">
        @yield('content')
    </main>

    <!-- PUBLIC FOOTER -->
    <footer class="bg-slate-900 text-white border-t border-slate-800 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-10">
                <!-- Col 1: Brand Info -->
                <div class="lg:col-span-4 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center font-black text-lg shadow-lg">
                            A
                        </div>
                        <div>
                            <span class="text-xl font-black text-white tracking-tight">ASystem</span>
                            <p class="text-[11px] text-slate-400 -mt-1 font-semibold">Support System ESA Groups</p>
                        </div>
                    </div>
                    <p class="text-xs text-slate-400 leading-relaxed max-w-sm">
                        Sistem manajemen terintegrasi untuk rekrutmen cerdas, administrasi tenaga kerja, evaluasi wawancara terpadu, dan otomatisasi SDM di lingkungan ESA Groups.
                    </p>
                    <div class="flex items-center gap-2.5 pt-1">
                        <a href="https://whatsapp.com/channel/0029VbDqNdb60eBgrCxsnt07" target="_blank" class="w-9 h-9 rounded-xl bg-slate-800 hover:bg-emerald-600 text-slate-300 hover:text-white flex items-center justify-center text-sm transition-all" title="WhatsApp Channel">
                            <i class="fa-brands fa-whatsapp"></i>
                        </a>
                        <a href="#" class="w-9 h-9 rounded-xl bg-slate-800 hover:bg-blue-600 text-slate-300 hover:text-white flex items-center justify-center text-sm transition-all" title="LinkedIn">
                            <i class="fa-brands fa-linkedin-in"></i>
                        </a>
                        <a href="#" class="w-9 h-9 rounded-xl bg-slate-800 hover:bg-sky-500 text-slate-300 hover:text-white flex items-center justify-center text-sm transition-all" title="Instagram">
                            <i class="fa-brands fa-instagram"></i>
                        </a>
                    </div>
                </div>

                <!-- Col 2: Nav Links -->
                <div class="lg:col-span-2 space-y-3">
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider">Navigasi</h4>
                    <ul class="space-y-2 text-xs text-slate-400">
                        <li><a href="{{ route('home.index') }}" class="hover:text-white transition-colors">Beranda</a></li>
                        <li><a href="{{ route('job.public') }}" class="hover:text-white transition-colors">Lowongan Kerja</a></li>
                        <li><a href="{{ route('home.index') }}#about" class="hover:text-white transition-colors">Tentang Kami</a></li>
                        <li><a href="{{ route('home.index') }}#features" class="hover:text-white transition-colors">Layanan & Fitur</a></li>
                        <li><a href="{{ route('home.index') }}#contact" class="hover:text-white transition-colors">Kontak Kami</a></li>
                    </ul>
                </div>

                <!-- Col 3: Portal Karyawan & Sistem -->
                <div class="lg:col-span-3 space-y-3">
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider">Akses Sistem</h4>
                    <ul class="space-y-2 text-xs text-slate-400">
                        <li><a href="{{ route('login') }}" class="hover:text-white transition-colors text-blue-300 font-semibold flex items-center gap-1.5"><i class="fa-solid fa-users-viewfinder text-[11px]"></i> Talent Pool / Rekrutmen - Login</a></li>
                        <li><a href="https://asystem.co.id/v3/login" target="_blank" class="hover:text-white transition-colors text-indigo-300 font-semibold flex items-center gap-1.5"><i class="fa-solid fa-arrow-up-right-from-square text-[11px]"></i> Fitur Lain - Akses ASystem V3</a></li>
                        <li><a href="{{ route('cbt.login') }}" class="hover:text-white transition-colors flex items-center gap-1.5"><i class="fa-solid fa-laptop-code text-[11px]"></i> Ujian CBT Online</a></li>
                        <li><a href="{{ route('kandidatportal.index') }}" class="hover:text-white transition-colors">Portal Verifikasi Pelamar</a></li>
                        <li><a href="{{ route('interview.index') }}" class="hover:text-white transition-colors">Interview Online</a></li>
                    </ul>
                </div>

                <!-- Col 4: Alamat & Kontak -->
                <div class="lg:col-span-3 space-y-3">
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider">Kantor & Bantuan</h4>
                    <div class="text-xs text-slate-400 space-y-2 leading-relaxed">
                        <p class="flex items-start gap-2">
                            <i class="fa-solid fa-location-dot text-rose-400 mt-1 shrink-0"></i>
                            <span>Jl. Rajawali No.18-20, Krembangan Sel., Kec. Krembangan, Surabaya, Jawa Timur 60175</span>
                        </p>
                        <p class="flex items-center gap-2">
                            <i class="fa-brands fa-whatsapp text-emerald-400 shrink-0"></i>
                            <a href="https://wa.me/6283139797309" target="_blank" class="hover:underline text-emerald-400">+62 831-3979-7309</a>
                        </p>
                        <p class="flex items-center gap-2">
                            <i class="fa-regular fa-clock text-sky-400 shrink-0"></i>
                            <span>Senin - Jumat: 08:00 - 17:00 WIB</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Bottom Copyright -->
            <div class="pt-10 mt-10 border-t border-slate-800/80 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
                <p>&copy; {{ date('Y') }} ASystem - Support System ESA Groups. All rights reserved.</p>
                <div class="flex items-center gap-4">
                    <span>Powered by <a href="https://digitalgalery.biz.id" target="_blank" class="text-slate-400 hover:text-white font-semibold">DGSoft</a></span>
                    <span>&bull;</span>
                    <a href="{{ route('login') }}" class="text-slate-400 hover:text-white">Admin & Staff Login</a>
                </div>
            </div>
        </div>
    </footer>

    @yield('scripts')
    @stack('scripts')
</body>
</html>
