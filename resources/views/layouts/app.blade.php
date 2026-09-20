<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ASystem - Support System ESA Groups')</title>
    
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
                        navy: {
                            800: '#1e293b',
                            900: '#0f172a',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Custom CSS matching Attendance Portal -->
    <style>
        html {
            zoom: 80%;
            -webkit-text-size-adjust: 100%;
        }
        * {
            font-family: 'Outfit', sans-serif;
        }
        [x-cloak] { display: none !important; }
        
        .topbar-shadow {
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px 0 rgba(0, 0, 0, 0.03);
        }

        .sidebar-item-active {
            background: linear-gradient(135deg, #0F52BA 0%, #2563eb 100%);
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(15, 82, 186, 0.25);
        }
        .sidebar-item-active i {
            color: #ffffff !important;
        }

        .page-header-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            padding: 1.25rem 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }

        .stat-box {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 0.875rem;
            padding: 1.15rem 1.25rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
            transition: all 0.2s ease;
        }
        .stat-box:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px -4px rgba(0,0,0,0.06);
            border-color: #cbd5e1;
        }
        .stat-box-icon {
            width: 46px;
            height: 46px;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .table-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
            overflow: hidden;
        }
        .custom-table th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 700;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0.85rem 1rem;
            border-bottom: 1px solid #e2e8f0;
            white-space: nowrap;
        }
        .custom-table td {
            padding: 0.85rem 1rem;
            font-size: 0.835rem;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        .custom-table tr:hover td {
            background-color: #f8fafc;
        }
        
        .badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.25rem 0.65rem;
            border-radius: 9999px;
            font-size: 0.72rem;
            font-weight: 600;
            letter-spacing: 0.025em;
            border-width: 1px;
        }

        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>
<body class="h-full antialiased text-slate-800 flex flex-col bg-slate-50">
    @include('partials.page-loader')

    <div class="min-h-full flex"
         x-data="{ 
             sidebarCollapsed: localStorage.getItem('asystem_sidebar_collapsed') === 'true',
             toggleSidebar() {
                 this.sidebarCollapsed = !this.sidebarCollapsed;
                 localStorage.setItem('asystem_sidebar_collapsed', this.sidebarCollapsed);
             }
         }">
        <!-- Sidebar -->
        <aside id="sidebar" 
               class="bg-white border-r border-slate-200 flex flex-col flex-shrink-0 transition-all duration-300 z-30 fixed inset-y-0 left-0 lg:static lg:translate-x-0 -translate-x-full"
               :class="sidebarCollapsed ? 'w-20' : 'w-64'">
            <!-- Sidebar Header / Logo -->
            <div class="h-16 flex items-center border-b border-slate-200 bg-white transition-all duration-300"
                 :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-5'">
                <a href="{{ route('fitur.index') }}" class="flex items-center gap-3 min-w-0" :title="sidebarCollapsed ? 'ASYSTEM - Support System ESA Groups' : ''">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-primary-700 via-primary-600 to-blue-500 flex items-center justify-center text-white shadow-md shadow-primary-500/20 flex-shrink-0">
                        <i class="fa-solid fa-fingerprint text-lg"></i>
                    </div>
                    <div x-show="!sidebarCollapsed" class="min-w-0">
                        <div class="text-base font-bold tracking-tight text-slate-900 leading-tight truncate">ASYSTEM</div>
                        <div class="text-[10px] font-semibold tracking-wider text-primary uppercase truncate">Support System ESA Groups</div>
                    </div>
                </a>
                <button @click="toggleSidebar()" 
                        type="button"
                        class="hidden lg:flex items-center justify-center w-7 h-7 text-slate-400 hover:text-primary hover:bg-slate-100 rounded-lg transition-all flex-shrink-0"
                        :title="sidebarCollapsed ? 'Perbesar Menu' : 'Kecilkan Menu'">
                    <i class="fa-solid text-xs" :class="sidebarCollapsed ? 'fa-chevron-right' : 'fa-chevron-left'"></i>
                </button>
                <button id="sidebarToggleBtn" class="text-slate-400 hover:text-slate-600 lg:hidden text-lg">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Sidebar Navigation -->
            <div class="flex-1 overflow-y-auto px-3 py-4 space-y-5">
                
                <!-- GROUP 1: DASHBOARD -->
                <div>
                    <div class="px-3 text-[11px] font-bold text-slate-400 tracking-wider uppercase mb-1.5 flex items-center justify-between" x-show="!sidebarCollapsed">
                        <span>Dashboard</span>
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    </div>
                    <div x-show="sidebarCollapsed" class="w-8 h-px bg-slate-200 mx-auto my-2" x-cloak></div>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('fitur.index') }}" 
                               title="Beranda"
                               class="flex items-center rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('fitur.index') ? 'sidebar-item-active' : 'text-slate-600 hover:text-primary hover:bg-slate-50' }}"
                               :class="sidebarCollapsed ? 'justify-center px-0 py-2.5' : 'gap-3 px-3.5 py-2.5'">
                                <i class="fa-solid fa-house text-base w-5 text-center flex-shrink-0 {{ request()->routeIs('fitur.index') ? 'text-white' : 'text-slate-400' }}"></i>
                                <span x-show="!sidebarCollapsed" class="flex-1 truncate">Beranda</span>
                                <span x-show="!sidebarCollapsed" class="text-[10px] bg-blue-100 text-blue-700 font-bold px-1.5 py-0.5 rounded-md">HOME</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- GROUP 2: MASTER DATA (ADMINISTRATOR ONLY) -->
                @if(Auth::check() && Auth::user()->isAdmin())
                <div>
                    <div class="px-3 text-[11px] font-bold text-slate-400 tracking-wider uppercase mb-1.5 flex items-center justify-between" x-show="!sidebarCollapsed">
                        <span>Master Data</span>
                        <span class="text-[9px] bg-indigo-50 text-indigo-700 font-bold px-1.5 py-0.2 rounded border border-indigo-200">ADMIN</span>
                    </div>
                    <div x-show="sidebarCollapsed" class="w-8 h-px bg-slate-200 mx-auto my-2" x-cloak></div>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('master.karyawan.index') }}" 
                               title="Master Karyawan"
                               class="flex items-center rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('master.karyawan.*') ? 'sidebar-item-active' : 'text-slate-600 hover:text-primary hover:bg-slate-50' }}"
                               :class="sidebarCollapsed ? 'justify-center px-0 py-2.5' : 'gap-3 px-3.5 py-2.5'">
                                <i class="fa-solid fa-users-gear text-base w-5 text-center flex-shrink-0 {{ request()->routeIs('master.karyawan.*') ? 'text-white' : 'text-slate-400' }}"></i>
                                <span x-show="!sidebarCollapsed" class="flex-1 truncate">Master Karyawan</span>
                                <span x-show="!sidebarCollapsed" class="text-[10px] bg-slate-100 text-slate-600 font-semibold px-1.5 py-0.5 rounded-md">Inhouse</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('master.prinsiple.index') }}" 
                               title="Master Prinsiple"
                               class="flex items-center rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('master.prinsiple.*') ? 'sidebar-item-active' : 'text-slate-600 hover:text-primary hover:bg-slate-50' }}"
                               :class="sidebarCollapsed ? 'justify-center px-0 py-2.5' : 'gap-3 px-3.5 py-2.5'">
                                <i class="fa-solid fa-building-shield text-base w-5 text-center flex-shrink-0 {{ request()->routeIs('master.prinsiple.*') ? 'text-white' : 'text-slate-400' }}"></i>
                                <span x-show="!sidebarCollapsed" class="flex-1 truncate">Master Prinsiple</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('master.math.index') }}" 
                               title="Master Soal Matematika"
                               class="flex items-center rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('master.math.*') ? 'sidebar-item-active' : 'text-slate-600 hover:text-primary hover:bg-slate-50' }}"
                               :class="sidebarCollapsed ? 'justify-center px-0 py-2.5' : 'gap-3 px-3.5 py-2.5'">
                                <i class="fa-solid fa-calculator text-base w-5 text-center flex-shrink-0 {{ request()->routeIs('master.math.*') ? 'text-white' : 'text-slate-400' }}"></i>
                                <span x-show="!sidebarCollapsed" class="flex-1 truncate">Soal Matematika</span>
                                <span x-show="!sidebarCollapsed" class="text-[10px] bg-emerald-50 text-emerald-700 font-bold px-1.5 py-0.5 rounded-md border border-emerald-200">CBT</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('master.personality.index') }}" 
                               title="Master Soal Kepribadian"
                               class="flex items-center rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('master.personality.*') ? 'sidebar-item-active' : 'text-slate-600 hover:text-primary hover:bg-slate-50' }}"
                               :class="sidebarCollapsed ? 'justify-center px-0 py-2.5' : 'gap-3 px-3.5 py-2.5'">
                                <i class="fa-solid fa-brain text-base w-5 text-center flex-shrink-0 {{ request()->routeIs('master.personality.*') ? 'text-white' : 'text-slate-400' }}"></i>
                                <span x-show="!sidebarCollapsed" class="flex-1 truncate">Soal Kepribadian</span>
                                <span x-show="!sidebarCollapsed" class="text-[10px] bg-purple-50 text-purple-700 font-bold px-1.5 py-0.5 rounded-md border border-purple-200">DISC</span>
                            </a>
                        </li>
                    </ul>
                </div>
                @endif

                <!-- GROUP: SYSTEM SETTING (ADMINISTRATOR ONLY) -->
                @if(Auth::check() && Auth::user()->isAdmin())
                <div>
                    <div class="px-3 text-[11px] font-bold text-slate-400 tracking-wider uppercase mb-1.5 flex items-center justify-between" x-show="!sidebarCollapsed">
                        <span>System Setting</span>
                        <span class="text-[9px] bg-slate-100 text-slate-700 font-bold px-1.5 py-0.2 rounded border border-slate-200">CONFIG</span>
                    </div>
                    <div x-show="sidebarCollapsed" class="w-8 h-px bg-slate-200 mx-auto my-2" x-cloak></div>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('odoo.setting.index') }}" 
                               title="Setting Sync Odoo"
                               class="flex items-center rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('odoo.setting.*') ? 'sidebar-item-active' : 'text-slate-600 hover:text-primary hover:bg-slate-50' }}"
                               :class="sidebarCollapsed ? 'justify-center px-0 py-2.5' : 'gap-3 px-3.5 py-2.5'">
                                <i class="fa-solid fa-arrows-rotate text-base w-5 text-center flex-shrink-0 {{ request()->routeIs('odoo.setting.*') ? 'text-white' : 'text-slate-400' }}"></i>
                                <span x-show="!sidebarCollapsed" class="flex-1 truncate">Setting Sync Odoo</span>
                                <span x-show="!sidebarCollapsed" class="text-[10px] bg-blue-50 text-blue-700 font-bold px-1.5 py-0.5 rounded-md border border-blue-200">5 Entitas</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('setting.rbac.index') }}" 
                               title="Hak Akses (RBAC)"
                               class="flex items-center rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('setting.rbac.*') || request()->routeIs('rbac.*') ? 'sidebar-item-active' : 'text-slate-600 hover:text-primary hover:bg-slate-50' }}"
                               :class="sidebarCollapsed ? 'justify-center px-0 py-2.5' : 'gap-3 px-3.5 py-2.5'">
                                <i class="fa-solid fa-user-shield text-base w-5 text-center flex-shrink-0 {{ request()->routeIs('setting.rbac.*') || request()->routeIs('rbac.*') ? 'text-white' : 'text-slate-400' }}"></i>
                                <span x-show="!sidebarCollapsed" class="flex-1 truncate">Hak Akses (RBAC)</span>
                                <span x-show="!sidebarCollapsed" class="text-[10px] bg-indigo-50 text-indigo-700 font-bold px-1.5 py-0.5 rounded-md border border-indigo-200">Role & Akses</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('aisetting.index') }}" 
                               title="Setting AI & WA"
                               class="flex items-center rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('aisetting.*') ? 'sidebar-item-active' : 'text-slate-600 hover:text-primary hover:bg-slate-50' }}"
                               :class="sidebarCollapsed ? 'justify-center px-0 py-2.5' : 'gap-3 px-3.5 py-2.5'">
                                <i class="fa-solid fa-sliders text-base w-5 text-center flex-shrink-0 {{ request()->routeIs('aisetting.*') ? 'text-white' : 'text-slate-400' }}"></i>
                                <span x-show="!sidebarCollapsed" class="flex-1 truncate">Setting AI & WA</span>
                                <span x-show="!sidebarCollapsed" class="text-[10px] bg-amber-50 text-amber-700 font-bold px-1.5 py-0.5 rounded-md border border-amber-200">AI</span>
                            </a>
                        </li>
                    </ul>
                </div>
                @endif

                <!-- GROUP 3: BAGIAN FITUR (DENGAN SUB-MENU INTERVIEW) -->
                @php
                    $isInterviewActive = request()->is('interview*') || request()->is('interviewinhouse*') || request()->is('user-prinsiple*') || request()->is('airanking*') || request()->is('ai-settings*') || request()->is('inputjob*') || request()->is('kandidatportal*') || request()->is('job*') || request()->is('interviewdone*') || request()->is('interviewarsip*') || request()->is('inputjob*') || request()->is('kandidatportal*');
                @endphp
                <div x-data="{ interviewOpen: {{ $isInterviewActive ? 'true' : 'false' }} }">
                    <div class="px-3 text-[11px] font-bold text-slate-400 tracking-wider uppercase mb-1.5 flex items-center justify-between" x-show="!sidebarCollapsed">
                        <span>Fitur & Layanan</span>
                        <span class="text-[10px] text-primary font-bold">MODUL</span>
                    </div>
                    <div x-show="sidebarCollapsed" class="w-8 h-px bg-slate-200 mx-auto my-2" x-cloak></div>
                    <ul class="space-y-1">
                        <!-- WORK PLAN & TODOLIST -->
                        <li>
                            <a href="{{ route('workplan.index') }}" 
                               title="Work Plan & ToDo"
                               class="flex items-center rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('workplan.index', 'workplan.daily') ? 'sidebar-item-active' : 'text-slate-700 hover:text-primary hover:bg-slate-50' }}"
                               :class="sidebarCollapsed ? 'justify-center px-0 py-2.5' : 'gap-3 px-3.5 py-2.5'">
                                <i class="fa-solid fa-list-check text-base w-5 text-center flex-shrink-0 {{ request()->routeIs('workplan.index', 'workplan.daily') ? 'text-white' : 'text-indigo-500' }}"></i>
                                <span x-show="!sidebarCollapsed" class="flex-1 truncate">Work Plan & ToDo</span>
                                <span x-show="!sidebarCollapsed" class="text-[9px] {{ request()->routeIs('workplan.index', 'workplan.daily') ? 'bg-white/20 text-white' : 'bg-indigo-100 text-indigo-700' }} font-bold px-1.5 py-0.5 rounded-md">KANBAN</span>
                            </a>
                        </li>

                        <!-- WA GROUPS CHAT -->
                        <li>
                            <a href="{{ route('workplan.chat') }}" 
                               title="WA Groups Chat"
                               class="flex items-center rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('workplan.chat*') ? 'sidebar-item-active' : 'text-slate-700 hover:text-emerald-600 hover:bg-slate-50' }}"
                               :class="sidebarCollapsed ? 'justify-center px-0 py-2.5' : 'gap-3 px-3.5 py-2.5'">
                                <i class="fa-brands fa-whatsapp text-base w-5 text-center flex-shrink-0 {{ request()->routeIs('workplan.chat*') ? 'text-white' : 'text-emerald-500' }}"></i>
                                <span x-show="!sidebarCollapsed" class="flex-1 truncate">WA Groups Chat</span>
                                <span x-show="!sidebarCollapsed" class="text-[9px] {{ request()->routeIs('workplan.chat*') ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-800' }} font-bold px-1.5 py-0.5 rounded-md">LIVE</span>
                            </a>
                        </li>

                        <!-- SUB-MENU INTERVIEW (COLLAPSIBLE ACCORDION) -->
                        <li>
                            <button @click="if(sidebarCollapsed) { toggleSidebar(); interviewOpen = true; } else { interviewOpen = !interviewOpen }" 
                                    type="button"
                                    title="Talent Pool / Rekrutment"
                                    class="w-full flex items-center rounded-xl text-sm font-semibold transition-all {{ $isInterviewActive ? 'bg-primary-50 text-primary' : 'text-slate-700 hover:bg-slate-50 hover:text-primary' }}"
                                    :class="sidebarCollapsed ? 'justify-center px-0 py-2.5' : 'gap-3 px-3.5 py-2.5'">
                                <i class="fa-solid fa-user-tie text-base w-5 text-center flex-shrink-0 {{ $isInterviewActive ? 'text-primary' : 'text-slate-400' }}"></i>
                                <span x-show="!sidebarCollapsed" class="flex-1 text-left truncate">Talent Pool / Rekrutment</span>
                                <i x-show="!sidebarCollapsed" class="fa-solid fa-chevron-down text-xs text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': interviewOpen }"></i>
                            </button>

                            <!-- SUB-MENU ITEMS LIST -->
                            <div x-show="interviewOpen && !sidebarCollapsed" x-collapse class="pl-4 pr-1 py-1.5 space-y-1 border-l-2 border-primary-200 ml-4 mt-1">
                                <!-- 1. Input Job -->
                                <a href="{{ route('job.input') }}" 
                                   title="Input Job"
                                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-semibold transition-all {{ request()->routeIs('job.input') ? 'bg-primary text-white shadow-sm' : 'text-slate-600 hover:text-primary hover:bg-slate-100/70' }}">
                                    <i class="fa-solid fa-briefcase text-[11px] w-4 text-center"></i>
                                    <span>Input Job</span>
                                    @if(request()->routeIs('job.input'))
                                        <span class="w-1.5 h-1.5 rounded-full bg-white ml-auto"></span>
                                    @endif
                                </a>

                                <!-- 2. Portal Lowongan Job -->
                                <a href="{{ route('job.public') }}" target="_blank"
                                   title="Portal Lowongan Job"
                                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-semibold transition-all {{ request()->routeIs('job.public') || request()->routeIs('job.detail') || request()->routeIs('job.apply') ? 'bg-primary text-white shadow-sm' : 'text-slate-600 hover:text-primary hover:bg-slate-100/70' }}">
                                    <i class="fa-solid fa-arrow-up-right-from-square text-[11px] w-4 text-center"></i>
                                    <span>Portal Lowongan Job</span>
                                    <span class="text-[9px] px-1.5 py-0.2 rounded font-bold uppercase bg-slate-100 text-slate-500">
                                        PUB
                                    </span>
                                </a>

                                <!-- Statistik Job & Kandidat -->
                                <a href="{{ route('job.statistik') }}" 
                                   title="Statistik Job & Kandidat"
                                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-semibold transition-all {{ request()->routeIs('job.statistik*') ? 'bg-primary text-white shadow-sm' : 'text-slate-600 hover:text-primary hover:bg-slate-100/70' }}">
                                    <i class="fa-solid fa-chart-pie text-[11px] w-4 text-center"></i>
                                    <span>Statistik Job & Kandidat</span>
                                    <span class="text-[9px] px-1.5 py-0.2 rounded font-bold uppercase {{ request()->routeIs('job.statistik*') ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-700' }}">
                                        STAT
                                    </span>
                                    @if(request()->routeIs('job.statistik*'))
                                        <span class="w-1.5 h-1.5 rounded-full bg-white ml-auto"></span>
                                    @endif
                                </a>

                                <!-- 3. Kandidat Portal -->
                                <a href="{{ route('kandidatportal.index') }}" 
                                   title="Kandidat Portal"
                                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-semibold transition-all {{ request()->routeIs('kandidatportal.*') ? 'bg-primary text-white shadow-sm' : 'text-slate-600 hover:text-primary hover:bg-slate-100/70' }}">
                                    <i class="fa-solid fa-globe text-[11px] w-4 text-center"></i>
                                    <span>Kandidat Portal</span>
                                    @if(request()->routeIs('kandidatportal.*'))
                                        <span class="w-1.5 h-1.5 rounded-full bg-white ml-auto"></span>
                                    @endif
                                </a>

                                <!-- 4. AI Ranking -->
                                <a href="{{ route('airanking.index') }}" 
                                   title="AI Ranking"
                                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-semibold transition-all {{ request()->routeIs('airanking.*') ? 'bg-primary text-white shadow-sm' : 'text-slate-600 hover:text-primary hover:bg-slate-100/70' }}">
                                    <i class="fa-solid fa-ranking-star text-[11px] w-4 text-center {{ request()->routeIs('airanking.*') ? 'text-white' : 'text-amber-500' }}"></i>
                                    <span>AI Ranking</span>
                                    <span class="text-[9px] px-1.5 py-0.2 rounded font-bold uppercase {{ request()->routeIs('airanking.*') ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-700' }}">
                                        AI
                                    </span>
                                    @if(request()->routeIs('airanking.*'))
                                        <span class="w-1.5 h-1.5 rounded-full bg-white ml-auto"></span>
                                    @endif
                                </a>

                                @if(Auth::check() && Auth::user()->isAdmin())
                                <!-- 5. Setting AI (Admin Only) -->
                                <a href="{{ route('aisetting.index') }}" 
                                   title="Setting AI & WA"
                                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-semibold transition-all {{ request()->routeIs('aisetting.*') ? 'bg-primary text-white shadow-sm' : 'text-slate-600 hover:text-primary hover:bg-slate-100/70' }}">
                                    <i class="fa-solid fa-sliders text-[11px] w-4 text-center"></i>
                                    <span>Setting AI & WA</span>
                                    @if(request()->routeIs('aisetting.*'))
                                        <span class="w-1.5 h-1.5 rounded-full bg-white ml-auto"></span>
                                    @endif
                                </a>
                                @endif

                                <!-- Master User Prinsiple -->
                                <a href="{{ route('userprinsiple.index') }}" 
                                   title="Master User Prinsiple"
                                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-semibold transition-all {{ request()->routeIs('userprinsiple.*') ? 'bg-primary text-white shadow-sm' : 'text-slate-600 hover:text-primary hover:bg-slate-100/70' }}">
                                    <i class="fa-solid fa-users-viewfinder text-[11px] w-4 text-center"></i>
                                    <span>Master User Prinsiple</span>
                                    <span class="text-[9px] px-1.5 py-0.2 rounded font-bold uppercase {{ request()->routeIs('userprinsiple.*') ? 'bg-white/20 text-white' : 'bg-blue-100 text-blue-700' }}">
                                        PRIN
                                    </span>
                                    @if(request()->routeIs('userprinsiple.*'))
                                        <span class="w-1.5 h-1.5 rounded-full bg-white ml-auto"></span>
                                    @endif
                                </a>

                                <!-- 6. Kandidat Interview -->
                                <a href="{{ route('interview.index') }}" 
                                   title="Kandidat Interview"
                                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-semibold transition-all {{ request()->routeIs('interview.index') || request()->routeIs('interview.show') ? 'bg-primary text-white shadow-sm' : 'text-slate-600 hover:text-primary hover:bg-slate-100/70' }}">
                                    <i class="fa-solid fa-clipboard-user text-[11px] w-4 text-center"></i>
                                    <span>Kandidat Interview</span>
                                </a>

                                @if(Auth::user() && (Auth::user()->isAdmin() || Auth::user()->isHrdOrHead()))
                                <!-- 7. Kandidat Inhouse -->
                                <a href="{{ route('interviewinhouse.index') }}" 
                                   title="Kandidat Inhouse"
                                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-semibold transition-all {{ request()->routeIs('interviewinhouse.*') ? 'bg-primary text-white shadow-sm' : 'text-slate-600 hover:text-primary hover:bg-slate-100/70' }}">
                                    <i class="fa-solid fa-house-user text-[11px] w-4 text-center"></i>
                                    <span>Kandidat Inhouse</span>
                                    @if(request()->routeIs('interviewinhouse.*'))
                                        <span class="w-1.5 h-1.5 rounded-full bg-white ml-auto"></span>
                                    @endif
                                </a>
                                @endif

                                <!-- 8. Interview Selesai -->
                                <a href="{{ route('interview.done') }}" 
                                   title="Interview Selesai"
                                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-semibold transition-all {{ request()->routeIs('interview.done') ? 'bg-primary text-white shadow-sm' : 'text-slate-600 hover:text-primary hover:bg-slate-100/70' }}">
                                    <i class="fa-solid fa-circle-check text-[11px] w-4 text-center"></i>
                                    <span>Interview Selesai</span>
                                </a>

                                <!-- 9. Arsip Interview -->
                                <a href="{{ route('interview.arsip') }}" 
                                   title="Arsip Interview"
                                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-semibold transition-all {{ request()->routeIs('interview.arsip') ? 'bg-primary text-white shadow-sm' : 'text-slate-600 hover:text-primary hover:bg-slate-100/70' }}">
                                    <i class="fa-solid fa-box-archive text-[11px] w-4 text-center"></i>
                                    <span>Arsip Interview</span>
                                </a>
                            </div>
                        </li>
                    </ul>
                </div>

            </div>

            <!-- Sidebar User Profile Footer -->
            <div class="p-3 border-t border-slate-200 bg-slate-50/50">
                @php
                    $currentUser = Auth::user();
                    $userName = $currentUser ? $currentUser->name : 'Administrator HRD';
                    $userEmail = $currentUser ? ($currentUser->job_title ?: $currentUser->email) : 'admin.pusat@arina.co.id';
                    $userRole = $currentUser ? ($currentUser->role ?? 'admin') : 'admin';
                    $roleLabel = match($userRole) {
                        'admin' => 'Administrator',
                        'karyawan_inhouse' => 'Karyawan Inhouse',
                        'karyawan_ratecard' => 'Karyawan RateCard',
                        'recruiter' => 'Recruiter Team',
                        'head_hr' => 'Head of HR',
                        default => ucfirst(str_replace('_', ' ', $userRole))
                    };
                    $userAvatarUrl = $currentUser ? $currentUser->avatar_url : "https://ui-avatars.com/api/?name=" . urlencode($userName) . "&background=0F52BA&color=fff";
                @endphp
                <!-- Expanded User Profile -->
                <div class="flex items-center gap-2 p-2 rounded-xl bg-white border border-slate-200 shadow-sm hover:border-primary-300 transition-all group" x-show="!sidebarCollapsed">
                    <a href="{{ route('profile.index') }}" class="flex items-center gap-2.5 flex-1 min-w-0" title="Klik untuk Buka Profil & Edit Data">
                        <img src="{{ $userAvatarUrl }}" alt="{{ $userName }}" class="w-9 h-9 rounded-lg border border-slate-100 flex-shrink-0 object-cover">
                        <div class="flex-1 min-w-0">
                            <div class="text-xs font-bold text-slate-900 group-hover:text-primary transition-colors truncate">{{ $userName }}</div>
                            <div class="text-[10px] text-slate-500 font-medium truncate flex items-center gap-1">
                                <span class="inline-block w-1.5 h-1.5 rounded-full {{ $userRole === 'admin' ? 'bg-blue-500' : ($userRole === 'karyawan_inhouse' ? 'bg-emerald-500' : 'bg-amber-500') }}"></span>
                                <span>{{ $roleLabel }}</span>
                            </div>
                        </div>
                    </a>
                    <a href="{{ route('profile.index') }}" title="Edit Profil Saya" class="text-slate-400 hover:text-primary hover:bg-primary-50 p-1.5 rounded-lg transition-all">
                        <i class="fa-solid fa-user-pen text-xs"></i>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline m-0 p-0">
                        @csrf
                        <button type="submit" title="Keluar / Logout" class="text-slate-400 hover:text-rose-600 hover:bg-rose-50 p-1.5 rounded-lg transition-all">
                            <i class="fa-solid fa-arrow-right-from-bracket text-sm"></i>
                        </button>
                    </form>
                </div>
                <!-- Collapsed User Profile -->
                <div class="flex flex-col items-center gap-2 p-1" x-show="sidebarCollapsed" x-cloak>
                    <a href="{{ route('profile.index') }}" title="Profil {{ $userName }} ({{ $roleLabel }})">
                        <img src="{{ $userAvatarUrl }}" alt="{{ $userName }}" class="w-9 h-9 rounded-lg border border-slate-200 shadow-sm object-cover hover:ring-2 hover:ring-primary transition-all">
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline m-0 p-0">
                        @csrf
                        <button type="submit" title="Keluar / Logout" class="text-slate-400 hover:text-rose-600 hover:bg-rose-50 p-1.5 rounded-lg transition-all">
                            <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                        </button>
                    </form>
                </div>

                @if(session()->has('impersonator_id'))
                <!-- Tombol Kembali ke User Asli (Sidebar Footer) -->
                <div class="mt-2.5 pt-2 border-t border-amber-200" x-show="!sidebarCollapsed">
                    <a href="{{ route('user.switch-back') }}" 
                       class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-xl bg-gradient-to-r from-amber-500 via-orange-500 to-amber-600 hover:from-amber-600 hover:to-orange-600 text-white text-xs font-extrabold shadow-sm hover:shadow transition-all"
                       title="Kembali ke Akun Asli: {{ session('impersonator_name', 'Administrator') }}">
                        <i class="fa-solid fa-rotate-left"></i>
                        <span>Kembali ke User Asli</span>
                    </a>
                </div>
                <div class="mt-2 flex justify-center" x-show="sidebarCollapsed" x-cloak>
                    <a href="{{ route('user.switch-back') }}" 
                       class="w-9 h-9 rounded-xl bg-amber-500 hover:bg-amber-600 text-white flex items-center justify-center text-xs shadow-sm transition-all"
                       title="Kembali ke User Asli ({{ session('impersonator_name', 'Administrator') }})">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                </div>
                @endif
            </div>
        </aside>

        <!-- Main Wrapper -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            
            <!-- STICKY BANNER SWITCH USER (SIMULASI AKUN) -->
            @if(session()->has('impersonator_id'))
            <div class="bg-gradient-to-r from-amber-600 via-orange-600 to-amber-700 text-white px-4 py-2.5 shadow-md flex items-center justify-between gap-3 z-30 text-xs font-semibold border-b border-amber-800/60">
                <div class="flex items-center gap-2.5 min-w-0">
                    <span class="w-7 h-7 rounded-lg bg-white/20 flex items-center justify-center flex-shrink-0 text-white animate-pulse">
                        <i class="fa-solid fa-user-gear text-sm"></i>
                    </span>
                    <div class="truncate">
                        <span class="font-bold tracking-wide uppercase text-[10px] bg-amber-950/40 px-2 py-0.5 rounded mr-1.5 border border-amber-400/30">Mode Switch User</span>
                        <span>Anda sedang login sebagai <strong>{{ Auth::user()->name }}</strong> ({{ Auth::user()->job_title ?: (Auth::user()->role === 'admin' ? 'Administrator' : 'Karyawan') }}).</span>
                        <span class="hidden md:inline text-amber-100/90 text-[11px] ml-1">&bull; Akun Asli: <strong>{{ session('impersonator_name', 'Administrator') }}</strong></span>
                    </div>
                </div>
                <a href="{{ route('user.switch-back') }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-white text-orange-700 hover:bg-orange-50 font-extrabold text-xs shadow-sm hover:shadow transition-all flex-shrink-0">
                    <i class="fa-solid fa-arrow-right-from-bracket rotate-180"></i>
                    <span>Kembali ke User Asli</span>
                </a>
            </div>
            @endif

            <!-- Topbar -->
            <header class="h-16 bg-white border-b border-slate-200 topbar-shadow flex items-center justify-between px-6 z-20">
                <div class="flex items-center gap-3">
                    <button id="mobileSidebarToggle" class="text-slate-500 hover:text-slate-800 lg:hidden p-1.5 rounded-lg border border-slate-200">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>
                    <!-- Desktop Minimize/Expand Toggle Button -->
                    <button @click="toggleSidebar()" 
                            type="button"
                            class="hidden lg:inline-flex items-center justify-center w-9 h-9 text-slate-500 hover:text-primary hover:bg-slate-100 rounded-xl transition-all border border-slate-200 shadow-sm" 
                            :title="sidebarCollapsed ? 'Perbesar Side Menu (Expand)' : 'Kecilkan Side Menu (Minimize)'">
                        <i class="fa-solid text-sm" :class="sidebarCollapsed ? 'fa-bars text-primary' : 'fa-bars-staggered'"></i>
                    </button>
                    <div class="hidden sm:flex items-center gap-2 text-xs text-slate-500 font-medium">
                        <span class="inline-flex items-center gap-1 text-slate-700 font-semibold">
                            <i class="fa-solid fa-calendar-day text-primary"></i>
                            {{ date('l, d F Y') }}
                        </span>
                        <span>•</span>
                        <span class="bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded-full border border-emerald-200 font-semibold">
                            <i class="fa-solid fa-circle text-[6px] mr-1"></i> ASystem Cloud Active
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    @if(session()->has('impersonator_id'))
                    <!-- Tombol Kembali ke User Asli di Topbar -->
                    <a href="{{ route('user.switch-back') }}" 
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white text-xs font-bold transition-all shadow-sm shadow-amber-500/20"
                       title="Kembali ke Akun Utama: {{ session('impersonator_name', 'Administrator') }}">
                        <i class="fa-solid fa-rotate-left text-xs"></i>
                        <span class="hidden sm:inline">Kembali ke User Asli</span>
                    </a>
                    @endif

                    <!-- Quick Input Job Button -->
                    <a href="{{ route('job.input') }}" class="hidden sm:inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-primary-50 text-primary text-xs font-semibold hover:bg-primary-100 transition-all border border-primary-200">
                        <i class="fa-solid fa-plus-circle"></i>
                        <span>Input Job</span>
                    </a>

                    <!-- Submenu Interview Quick Access -->
                    <a href="{{ route('interview.index') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 text-xs font-semibold hover:bg-slate-200 transition-all">
                        <i class="fa-solid fa-user-tie text-primary"></i>
                        <span>Kandidat</span>
                    </a>

                    <div class="h-6 w-px bg-slate-200 mx-1"></div>

                    <!-- Notification Button -->
                    <button class="relative p-2 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition-all">
                        <i class="fa-regular fa-bell text-lg"></i>
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-rose-500"></span>
                    </button>

                    <!-- Topbar Profile -->
                    <div class="flex items-center gap-2 pl-2">
                        <a href="{{ route('profile.index') }}" class="flex items-center gap-2 hover:opacity-90 group transition-all" title="Klik untuk Buka Profil Akun">
                            <img src="{{ $userAvatarUrl }}" alt="{{ $userName }}" class="w-8 h-8 rounded-lg border border-slate-200 object-cover group-hover:ring-2 group-hover:ring-primary/40 transition-all">
                            <div class="hidden md:block text-left">
                                <div class="text-xs font-bold text-slate-800 group-hover:text-primary transition-colors leading-none">{{ $userName }}</div>
                                <div class="text-[10px] text-slate-500 leading-none mt-1 font-medium">{{ $roleLabel }}</div>
                            </div>
                        </a>
                        <a href="{{ route('profile.index') }}" title="Edit Profil Saya" class="text-slate-400 hover:text-primary p-1.5 rounded-md hover:bg-slate-100 transition-all">
                            <i class="fa-solid fa-user-gear text-xs"></i>
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline m-0 p-0 ml-1">
                            @csrf
                            <button type="submit" title="Keluar / Logout" class="text-slate-400 hover:text-rose-600 p-1.5 rounded-md hover:bg-slate-100 transition-all">
                                <i class="fa-solid fa-power-off text-xs"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Main Page Content -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 space-y-6 asystem-page-enter">
                <!-- Alerts / Flash Messages -->
                @if(session('success'))
                    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between shadow-sm animate-fade-in">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-emerald-500 text-white flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-check"></i>
                            </div>
                            <div>
                                <span class="font-bold text-sm">Berhasil!</span>
                                <p class="text-xs text-emerald-700 mt-0.5">{!! session('success') !!}</p>
                            </div>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 p-1">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center justify-between shadow-sm animate-fade-in">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-rose-500 text-white flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                            </div>
                            <div>
                                <span class="font-bold text-sm">Gagal Menyimpan!</span>
                                <p class="text-xs text-rose-700 mt-0.5">{!! session('error') !!}</p>
                            </div>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 p-1">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 flex items-center justify-between shadow-sm animate-fade-in">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-amber-500 text-white flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-circle-exclamation"></i>
                            </div>
                            <div>
                                <span class="font-bold text-sm">Perhatian</span>
                                <p class="text-xs text-amber-700 mt-0.5">{!! session('warning') !!}</p>
                            </div>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-amber-500 hover:text-amber-700 p-1">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                @endif

                @if(session('info'))
                    <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 flex items-center justify-between shadow-sm animate-fade-in">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-blue-500 text-white flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-info"></i>
                            </div>
                            <div>
                                <span class="font-bold text-sm">Informasi</span>
                                <p class="text-xs text-blue-700 mt-0.5">{!! session('info') !!}</p>
                            </div>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-blue-500 hover:text-blue-700 p-1">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                @endif

                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-slate-200 px-6 py-3.5 text-xs text-slate-500 flex flex-col sm:flex-row items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                    <span class="font-semibold text-slate-700">ASystem - Support System ESA Groups</span>
                    <span>•</span>
                    <span>Rebuilt on Laravel 12 & Modern UI</span>
                </div>
                <div>
                    <span>&copy; {{ date('Y') }} PT Arina Multikarya. All rights reserved.</span>
                </div>
            </footer>
        </div>
    </div>

    <!-- Mobile Sidebar Toggle Script -->
    <script>
        const sidebar = document.getElementById('sidebar');
        const mobileToggle = document.getElementById('mobileSidebarToggle');
        const sidebarClose = document.getElementById('sidebarToggleBtn');

        if (mobileToggle) {
            mobileToggle.addEventListener('click', () => {
                sidebar.classList.toggle('-translate-x-full');
            });
        }
        if (sidebarClose) {
            sidebarClose.addEventListener('click', () => {
                sidebar.classList.add('-translate-x-full');
            });
        }
    </script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Global SweetAlert replacement for native alert()
        window.alert = function(message) {
            if (typeof Swal === 'undefined') {
                console.warn(message);
                return;
            }
            let icon = 'info';
            let title = 'Pemberitahuan';
            const str = String(message || '');
            const lower = str.toLowerCase();
            if (lower.includes('ditolak') || lower.includes('hanya delegator') || lower.includes('tidak berhak')) {
                icon = 'warning';
                title = 'Akses Ditolak!';
            } else if (lower.includes('gagal') || lower.includes('error') || lower.includes('kesalahan')) {
                icon = 'error';
                title = 'Terjadi Kesalahan';
            } else if (lower.includes('berhasil') || lower.includes('sukses') || lower.includes('tersimpan') || lower.includes('disalin')) {
                icon = 'success';
                title = 'Berhasil!';
            } else if (lower.includes('peringatan') || lower.includes('warning') || lower.includes('perhatian')) {
                icon = 'warning';
                title = 'Perhatian';
            }

            Swal.fire({
                icon: icon,
                title: title,
                html: str.replace(/\n/g, '<br>'),
                confirmButtonColor: '#0F52BA',
                confirmButtonText: 'Mengerti',
                customClass: {
                    popup: 'rounded-2xl shadow-2xl',
                    confirmButton: 'rounded-xl font-bold px-5 py-2.5 text-xs'
                }
            });
        };
    </script>
    
    @yield('scripts')
    @stack('scripts')
</body>
</html>