@extends('layouts.app')

@section('title', 'Beranda - ASystem Portal')

@section('content')
@php
    $hour = (int) date('H');
    $greeting = match(true) {
        $hour >= 4 && $hour < 11 => 'Selamat Pagi',
        $hour >= 11 && $hour < 15 => 'Selamat Siang',
        $hour >= 15 && $hour < 18 => 'Selamat Sore',
        default => 'Selamat Malam'
    };

    $currentUser = $user ?? Auth::user();
    $userName = $currentUser ? $currentUser->name : 'Pengguna';
    $userEmail = $currentUser ? $currentUser->email : '-';
    $userRole = $currentUser ? ($currentUser->role ?? 'admin') : 'admin';
    $userJabatan = $currentUser ? $currentUser->jabatan_display : 'Administrator';
    $isAdmin = $currentUser ? $currentUser->isAdmin() : false;

    $roleLabel = match($userRole) {
        'admin' => 'Administrator Sistem',
        'karyawan_inhouse' => 'Karyawan Inhouse',
        'karyawan_ratecard' => 'Karyawan RateCard',
        'recruiter' => 'Recruiter Team',
        'head_hr' => 'Head of HR',
        default => ucfirst(str_replace('_', ' ', $userRole))
    };

    $avatarColor = match($userRole) {
        'admin' => '0F52BA',
        'karyawan_inhouse' => '059669',
        'karyawan_ratecard' => 'D97706',
        default => '6366F1'
    };

    $canAccessRecruitment = $isAdmin || ($currentUser && (
        (method_exists($currentUser, 'isRecruiter') && $currentUser->isRecruiter()) ||
        (method_exists($currentUser, 'isHeadHr') && $currentUser->isHeadHr()) ||
        (method_exists($currentUser, 'isHrd') && $currentUser->isHrd())
    ));
    $canAccessAdmin = $isAdmin;

    // Registry seluruh modul dan shortcut sistem dengan hak akses ketat
    $desktopWidgetRegistry = [
        // --- 1. OPERASIONAL & UNIVERSAL (SEMUA ROLE) ---
        'workplan' => [
            'id' => 'workplan',
            'title' => 'Work Plan & ToDoList',
            'short_title' => 'Work Plan',
            'url' => route('workplan.index'),
            'icon' => 'fa-solid fa-list-check',
            'gradient' => 'from-indigo-600 via-indigo-500 to-blue-500',
            'shadow' => 'shadow-indigo-500/25',
            'category' => 'operasional',
            'category_label' => 'Operasional',
            'desc' => 'Manajemen tugas, Kanban board, dan progres pekerjaan harian.',
            'target' => '_self',
            'role_allowed' => true,
        ],
        'workplan_daily' => [
            'id' => 'workplan_daily',
            'title' => 'Daily Work Plan Logs',
            'short_title' => 'Daily Plan',
            'url' => route('workplan.daily'),
            'icon' => 'fa-solid fa-calendar-check',
            'gradient' => 'from-blue-600 via-sky-500 to-cyan-500',
            'shadow' => 'shadow-blue-500/25',
            'category' => 'operasional',
            'category_label' => 'Operasional',
            'desc' => 'Pencatatan dan rekap pelaporan log kerja per hari.',
            'target' => '_self',
            'role_allowed' => true,
        ],
        'workplan_chat' => [
            'id' => 'workplan_chat',
            'title' => 'Groups Chat Tim',
            'short_title' => 'Groups Chat',
            'url' => route('workplan.chat'),
            'icon' => 'fa-solid fa-comments',
            'gradient' => 'from-emerald-600 via-teal-500 to-green-500',
            'shadow' => 'shadow-emerald-500/25',
            'category' => 'operasional',
            'category_label' => 'Operasional',
            'desc' => 'Komunikasi pesan grup dan diskusi internal tim kerja.',
            'target' => '_self',
            'role_allowed' => true,
        ],
        'helpdesk_dashboard' => [
            'id' => 'helpdesk_dashboard',
            'title' => 'Helpdesk Support',
            'short_title' => 'Helpdesk',
            'url' => route('helpdesk.index'),
            'icon' => 'fa-solid fa-headset',
            'gradient' => 'from-amber-500 via-orange-500 to-amber-600',
            'shadow' => 'shadow-amber-500/25',
            'category' => 'operasional',
            'category_label' => 'Operasional',
            'desc' => 'Pusat pemantauan tiket kendala, keluhan operasional & IT.',
            'target' => '_self',
            'role_allowed' => true,
        ],
        'helpdesk_create' => [
            'id' => 'helpdesk_create',
            'title' => 'Buat Tiket Bantuan',
            'short_title' => 'Buat Tiket',
            'url' => route('helpdesk.tickets.create'),
            'icon' => 'fa-solid fa-circle-plus',
            'gradient' => 'from-orange-500 via-rose-500 to-red-500',
            'shadow' => 'shadow-orange-500/25',
            'category' => 'operasional',
            'category_label' => 'Operasional',
            'desc' => 'Ajukan tiket permohonan bantuan atau perbaikan kendala baru.',
            'target' => '_self',
            'role_allowed' => true,
        ],
        'cbt_portal' => [
            'id' => 'cbt_portal',
            'title' => 'Portal CBT Mandiri',
            'short_title' => 'CBT Online',
            'url' => route('cbt.login'),
            'icon' => 'fa-solid fa-laptop-code',
            'gradient' => 'from-purple-600 via-violet-600 to-indigo-600',
            'shadow' => 'shadow-purple-500/25',
            'category' => 'operasional',
            'category_label' => 'Operasional',
            'desc' => 'Portal tes ujian online matematika dan psikotes kepribadian.',
            'target' => '_blank',
            'role_allowed' => true,
        ],
        'job_portal' => [
            'id' => 'job_portal',
            'title' => 'Portal Lowongan Publik',
            'short_title' => 'Portal Karir',
            'url' => route('job.public'),
            'icon' => 'fa-solid fa-briefcase',
            'gradient' => 'from-sky-600 via-blue-600 to-indigo-600',
            'shadow' => 'shadow-sky-500/25',
            'category' => 'operasional',
            'category_label' => 'Operasional',
            'desc' => 'Lihat portal etalase lowongan kerja publik yang sedang dibuka.',
            'target' => '_blank',
            'role_allowed' => true,
        ],
        'profile' => [
            'id' => 'profile',
            'title' => 'Profil & Akun Pengguna',
            'short_title' => 'Profil Akun',
            'url' => route('profile.index'),
            'icon' => 'fa-solid fa-user-gear',
            'gradient' => 'from-slate-700 via-slate-800 to-slate-900',
            'shadow' => 'shadow-slate-500/25',
            'category' => 'operasional',
            'category_label' => 'Operasional',
            'desc' => 'Kelola biodata, foto profil, dan kata sandi akun.',
            'target' => '_self',
            'role_allowed' => true,
        ],

        // --- 2. REKRUTMEN / TALENT POOL (RECRUITER, HEAD HR, ADMIN) ---
        'interview' => [
            'id' => 'interview',
            'title' => 'Talent Pool Rekrutmen',
            'short_title' => 'Talent Pool',
            'url' => route('interview.index'),
            'icon' => 'fa-solid fa-user-tie',
            'gradient' => 'from-blue-600 via-indigo-600 to-blue-700',
            'shadow' => 'shadow-blue-500/25',
            'category' => 'rekrutmen',
            'category_label' => 'Rekrutmen',
            'desc' => 'Database kandidat pelamar kerja dan penilaian interview.',
            'target' => '_self',
            'role_allowed' => $canAccessRecruitment,
        ],
        'walkinterview' => [
            'id' => 'walkinterview',
            'title' => 'Kandidat Walk-In',
            'short_title' => 'Walk-In',
            'url' => route('interview.walk'),
            'icon' => 'fa-solid fa-person-walking',
            'gradient' => 'from-teal-600 via-emerald-600 to-teal-700',
            'shadow' => 'shadow-teal-500/25',
            'category' => 'rekrutmen',
            'category_label' => 'Rekrutmen',
            'desc' => 'Registrasi dan pendataan pelamar langsung di kantor.',
            'target' => '_self',
            'role_allowed' => $canAccessRecruitment,
        ],
        'kandidatportal' => [
            'id' => 'kandidatportal',
            'title' => 'Kandidat Job Portal',
            'short_title' => 'Job Portal',
            'url' => route('kandidatportal.index'),
            'icon' => 'fa-solid fa-globe',
            'gradient' => 'from-cyan-600 via-blue-600 to-teal-600',
            'shadow' => 'shadow-cyan-500/25',
            'category' => 'rekrutmen',
            'category_label' => 'Rekrutmen',
            'desc' => 'Kelola pelamar yang mendaftar melalui situs karir online.',
            'target' => '_self',
            'role_allowed' => $canAccessRecruitment,
        ],
        'airanking' => [
            'id' => 'airanking',
            'title' => 'AI Candidate Ranking',
            'short_title' => 'AI Ranking',
            'url' => route('airanking.index'),
            'icon' => 'fa-solid fa-ranking-star',
            'gradient' => 'from-violet-600 via-purple-600 to-fuchsia-600',
            'shadow' => 'shadow-violet-500/25',
            'category' => 'rekrutmen',
            'category_label' => 'Rekrutmen',
            'desc' => 'Pemeringkatan kecocokan CV kandidat otomatis dengan AI.',
            'target' => '_self',
            'role_allowed' => $canAccessRecruitment,
        ],
        'job_statistik' => [
            'id' => 'job_statistik',
            'title' => 'Statistik Lowongan & Pelamar',
            'short_title' => 'Statistik Job',
            'url' => route('job.statistik'),
            'icon' => 'fa-solid fa-chart-pie',
            'gradient' => 'from-pink-600 via-rose-500 to-fuchsia-600',
            'shadow' => 'shadow-pink-500/25',
            'category' => 'rekrutmen',
            'category_label' => 'Rekrutmen',
            'desc' => 'Visualisasi data dan grafik statistik pelamar kerja.',
            'target' => '_self',
            'role_allowed' => $canAccessRecruitment,
        ],
        'job_input' => [
            'id' => 'job_input',
            'title' => 'Input Job Requirement',
            'short_title' => 'Input Job',
            'url' => route('job.input'),
            'icon' => 'fa-solid fa-file-circle-plus',
            'gradient' => 'from-emerald-600 via-green-600 to-teal-600',
            'shadow' => 'shadow-emerald-500/25',
            'category' => 'rekrutmen',
            'category_label' => 'Rekrutmen',
            'desc' => 'Buat pembukaan form kualifikasi lowongan posisi baru.',
            'target' => '_self',
            'role_allowed' => $canAccessRecruitment,
        ],
        'userprinsiple' => [
            'id' => 'userprinsiple',
            'title' => 'Master User Prinsiple',
            'short_title' => 'User Client',
            'url' => route('userprinsiple.index'),
            'icon' => 'fa-solid fa-users-viewfinder',
            'gradient' => 'from-sky-600 via-cyan-600 to-blue-600',
            'shadow' => 'shadow-sky-500/25',
            'category' => 'rekrutmen',
            'category_label' => 'Rekrutmen',
            'desc' => 'Pengaturan akses akun persetujuan pelamar bagi mitra/klien.',
            'target' => '_self',
            'role_allowed' => $canAccessRecruitment,
        ],
        'interview_done' => [
            'id' => 'interview_done',
            'title' => 'Kandidat Selesai / Lolos',
            'short_title' => 'Kandidat Lolos',
            'url' => route('interview.done'),
            'icon' => 'fa-solid fa-circle-check',
            'gradient' => 'from-emerald-600 via-emerald-500 to-teal-600',
            'shadow' => 'shadow-emerald-500/25',
            'category' => 'rekrutmen',
            'category_label' => 'Rekrutmen',
            'desc' => 'Rekap daftar kandidat yang telah lolos seluruh tahapan.',
            'target' => '_self',
            'role_allowed' => $canAccessRecruitment,
        ],
        'interview_arsip' => [
            'id' => 'interview_arsip',
            'title' => 'Arsip Data Kandidat',
            'short_title' => 'Arsip Pelamar',
            'url' => route('interview.arsip'),
            'icon' => 'fa-solid fa-box-archive',
            'gradient' => 'from-slate-600 via-zinc-600 to-slate-700',
            'shadow' => 'shadow-slate-500/25',
            'category' => 'rekrutmen',
            'category_label' => 'Rekrutmen',
            'desc' => 'Pusat penyimpanan arsip data kandidat terdahulu.',
            'target' => '_self',
            'role_allowed' => $canAccessRecruitment,
        ],

        // --- 3. MASTER DATA (ADMIN ONLY) ---
        'master_karyawan' => [
            'id' => 'master_karyawan',
            'title' => 'Master Data Karyawan',
            'short_title' => 'Karyawan',
            'url' => route('master.karyawan.index'),
            'icon' => 'fa-solid fa-users-gear',
            'gradient' => 'from-blue-700 via-indigo-700 to-primary-700',
            'shadow' => 'shadow-blue-500/25',
            'category' => 'master',
            'category_label' => 'Master Data',
            'desc' => 'Database induk seluruh pegawai, mutasi, dan akses login.',
            'target' => '_self',
            'role_allowed' => $canAccessAdmin,
        ],
        'master_prinsiple' => [
            'id' => 'master_prinsiple',
            'title' => 'Master Prinsiple / Mitra',
            'short_title' => 'Prinsiple',
            'url' => route('master.prinsiple.index'),
            'icon' => 'fa-solid fa-building-shield',
            'gradient' => 'from-cyan-700 via-blue-700 to-sky-700',
            'shadow' => 'shadow-cyan-500/25',
            'category' => 'master',
            'category_label' => 'Master Data',
            'desc' => 'Daftar perusahaan prinsiple, PIC mitra, dan konfigurasi.',
            'target' => '_self',
            'role_allowed' => $canAccessAdmin,
        ],
        'master_math' => [
            'id' => 'master_math',
            'title' => 'Bank Soal Matematika CBT',
            'short_title' => 'Soal Math',
            'url' => route('master.math.index'),
            'icon' => 'fa-solid fa-calculator',
            'gradient' => 'from-amber-600 via-orange-600 to-yellow-600',
            'shadow' => 'shadow-amber-500/25',
            'category' => 'master',
            'category_label' => 'Master Data',
            'desc' => 'Kelola bank soal tes matematika dasar & kalkulasi numerik.',
            'target' => '_self',
            'role_allowed' => $canAccessAdmin,
        ],
        'master_personality' => [
            'id' => 'master_personality',
            'title' => 'Bank Soal Kepribadian DISC',
            'short_title' => 'Soal DISC',
            'url' => route('master.personality.index'),
            'icon' => 'fa-solid fa-brain',
            'gradient' => 'from-purple-700 via-violet-700 to-fuchsia-700',
            'shadow' => 'shadow-purple-500/25',
            'category' => 'master',
            'category_label' => 'Master Data',
            'desc' => 'Pengaturan pernyataan tes psikotes kepribadian.',
            'target' => '_self',
            'role_allowed' => $canAccessAdmin,
        ],
        'approval_workflow' => [
            'id' => 'approval_workflow',
            'title' => 'Alur Approver Dinamis',
            'short_title' => 'Alur Approver',
            'url' => route('master.approval-workflow.index'),
            'icon' => 'fa-solid fa-diagram-project',
            'gradient' => 'from-rose-600 via-pink-600 to-red-600',
            'shadow' => 'shadow-rose-500/25',
            'category' => 'master',
            'category_label' => 'Master Data',
            'desc' => 'Struktur hierarki urutan verifikasi dan approval berkas.',
            'target' => '_self',
            'role_allowed' => $canAccessAdmin,
        ],

        // --- 4. PENGATURAN SISTEM (ADMIN ONLY) ---
        'odoo_setting' => [
            'id' => 'odoo_setting',
            'title' => 'Sinkronisasi Odoo ERP',
            'short_title' => 'Sync Odoo',
            'url' => route('odoo.setting.index'),
            'icon' => 'fa-solid fa-arrows-rotate',
            'gradient' => 'from-rose-600 via-red-600 to-pink-600',
            'shadow' => 'shadow-rose-500/25',
            'category' => 'system',
            'category_label' => 'Pengaturan',
            'desc' => 'Koneksi XML-RPC dan integrasi database dengan Odoo ERP.',
            'target' => '_self',
            'role_allowed' => $canAccessAdmin,
        ],
        'setting_rbac' => [
            'id' => 'setting_rbac',
            'title' => 'Hak Akses Sistem (RBAC)',
            'short_title' => 'Hak Akses',
            'url' => route('setting.rbac.index'),
            'icon' => 'fa-solid fa-user-shield',
            'gradient' => 'from-indigo-700 via-purple-700 to-slate-800',
            'shadow' => 'shadow-indigo-500/25',
            'category' => 'system',
            'category_label' => 'Pengaturan',
            'desc' => 'Matriks perizinan modul dan pembatasan peran pengguna.',
            'target' => '_self',
            'role_allowed' => $canAccessAdmin,
        ],
        'aisetting' => [
            'id' => 'aisetting',
            'title' => 'Setting AI & WhatsApp Gateway',
            'short_title' => 'Setting AI',
            'url' => route('aisetting.index'),
            'icon' => 'fa-solid fa-sliders',
            'gradient' => 'from-fuchsia-600 via-purple-600 to-pink-600',
            'shadow' => 'shadow-fuchsia-500/25',
            'category' => 'system',
            'category_label' => 'Pengaturan',
            'desc' => 'Konfigurasi Google Gemini, SumoPod, dan WA Blast API.',
            'target' => '_self',
            'role_allowed' => $canAccessAdmin,
        ],
        'admin_auth_chat' => [
            'id' => 'admin_auth_chat',
            'title' => 'Bantuan Login & Reset Sandi',
            'short_title' => 'Bantuan Login',
            'url' => route('admin.auth-chat.index'),
            'icon' => 'fa-solid fa-headset',
            'gradient' => 'from-teal-600 via-emerald-600 to-green-600',
            'shadow' => 'shadow-teal-500/25',
            'category' => 'system',
            'category_label' => 'Pengaturan',
            'desc' => 'Layanan real-time untuk karyawan yang lupa kata sandi.',
            'target' => '_self',
            'role_allowed' => $canAccessAdmin,
        ],
        'activity_logs' => [
            'id' => 'activity_logs',
            'title' => 'Log Aktivitas & Audit Trail',
            'short_title' => 'Audit Log',
            'url' => route('activity-logs.index'),
            'icon' => 'fa-solid fa-clock-rotate-left',
            'gradient' => 'from-slate-700 via-slate-800 to-zinc-800',
            'shadow' => 'shadow-slate-500/25',
            'category' => 'system',
            'category_label' => 'Pengaturan',
            'desc' => 'Rekaman jejak aktivitas user dan histori perubahan data.',
            'target' => '_self',
            'role_allowed' => $canAccessAdmin,
        ],
        'helpdesk_kanban' => [
            'id' => 'helpdesk_kanban',
            'title' => 'Kanban Manajemen Tiket',
            'short_title' => 'Kanban Tiket',
            'url' => route('helpdesk.kanban'),
            'icon' => 'fa-solid fa-table-columns',
            'gradient' => 'from-amber-600 via-yellow-600 to-orange-600',
            'shadow' => 'shadow-amber-500/25',
            'category' => 'system',
            'category_label' => 'Pengaturan',
            'desc' => 'Papan Kanban manajemen penugasan tiket helpdesk agen.',
            'target' => '_self',
            'role_allowed' => $canAccessAdmin,
        ],
    ];

    // Filter modul yang diizinkan untuk peran pengguna yang sedang aktif
    $allowedWidgets = array_filter($desktopWidgetRegistry, fn($item) => $item['role_allowed'] === true);

    // Kategori tab filter untuk modal katalog
    $availableCategories = [
        'all' => 'Semua Modul',
        'operasional' => 'Operasional',
    ];
    if ($canAccessRecruitment) {
        $availableCategories['rekrutmen'] = 'Rekrutmen';
    }
    if ($canAccessAdmin) {
        $availableCategories['master'] = 'Master Data';
        $availableCategories['system'] = 'Pengaturan';
    }

    // Default widget shortcut rekomendasi awal
    $defaultWidgetKeys = match(true) {
        $canAccessAdmin => ['workplan', 'interview', 'kandidatportal', 'master_karyawan', 'odoo_setting', 'helpdesk_dashboard', 'airanking', 'aisetting'],
        $canAccessRecruitment => ['workplan', 'interview', 'kandidatportal', 'airanking', 'job_input', 'helpdesk_dashboard', 'workplan_chat', 'profile'],
        default => ['workplan', 'workplan_daily', 'workplan_chat', 'helpdesk_dashboard', 'helpdesk_create', 'cbt_portal', 'job_portal', 'profile']
    };
@endphp

<div class="space-y-6 w-full">

    <!-- 1. HERO SAMBUTAN UTAMA -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-primary-950 to-primary-900 text-white p-6 sm:p-8 lg:p-8 shadow-xl shadow-primary-950/20 border border-slate-800">
        <!-- Background Ambient Glow & Patterns -->
        <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-primary-500/20 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-80 h-80 rounded-full bg-blue-500/15 blur-3xl pointer-events-none"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-start sm:items-center gap-5">
                <div class="relative flex-shrink-0">
                    <img src="{{ $currentUser ? $currentUser->avatar_url : "https://ui-avatars.com/api/?name=" . urlencode($userName) . "&background=" . $avatarColor . "&color=fff&size=128&bold=true" }}" 
                         alt="{{ $userName }}" 
                         class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl border-2 border-white/20 shadow-lg object-cover ring-4 ring-white/10">
                    <span class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-emerald-500 border-2 border-slate-900 flex items-center justify-center text-[10px]" title="Aktif Online">
                        <i class="fa-solid fa-check text-white"></i>
                    </span>
                </div>
                <div>
                    <div class="flex items-center gap-2.5 flex-wrap mb-1.5">
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold tracking-wide uppercase bg-white/10 text-white border border-white/15 backdrop-blur-sm">
                            {{ $greeting }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold tracking-wide uppercase {{ $isAdmin ? 'bg-indigo-500/25 text-indigo-200 border border-indigo-400/30' : ($userRole === 'karyawan_inhouse' ? 'bg-emerald-500/25 text-emerald-200 border border-emerald-400/30' : 'bg-amber-500/25 text-amber-200 border border-amber-400/30') }}">
                            <i class="fa-solid {{ $isAdmin ? 'fa-shield-halved' : 'fa-id-badge' }} mr-1"></i>
                            {{ $roleLabel }}
                        </span>
                        <a href="{{ route('profile.index') }}" class="px-2.5 py-0.5 rounded-full text-[11px] font-bold tracking-wide bg-amber-400/20 hover:bg-amber-400/30 text-amber-200 border border-amber-300/30 transition-all flex items-center gap-1.5">
                            <i class="fa-solid fa-user-pen text-[10px]"></i>
                            <span>Edit Profil</span>
                        </a>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                        {{ $userName }}
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl leading-relaxed">
                        @if($isAdmin)
                            Selamat datang di pusat kendali <strong>ASystem Cloud Enterprise</strong>. Anda memiliki hak akses penuh sebagai Administrator untuk mengelola Master Data, konfigurasi sinkronisasi, dan seluruh modul operasional.
                        @else
                            Selamat datang di portal layanan <strong>ASystem Cloud</strong> — Support System ESA Groups. Silakan gunakan menu di panel samping untuk mengakses modul dan layanan operasional Anda.
                        @endif
                    </p>
                </div>
            </div>

            <!-- Date & System Status Badge -->
            <div class="flex md:flex-col items-center md:items-end justify-between gap-2 border-t md:border-t-0 border-white/10 pt-4 md:pt-0">
                <div class="text-left md:text-right">
                    <div class="text-[11px] text-slate-400 font-medium uppercase tracking-wider">Tanggal & Waktu</div>
                    <div class="text-sm sm:text-base font-bold text-white flex items-center gap-2 mt-0.5">
                        <i class="fa-solid fa-calendar-day text-primary-400"></i>
                        <span>{{ date('d F Y') }}</span>
                    </div>
                </div>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white/10 border border-white/15 text-xs text-slate-200 backdrop-blur-sm">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="font-semibold">ASystem Cloud Active</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. PAPAN PINTASAN & WIDGET DESKTOP (WINDOWS STYLE) -->
    <div x-data="desktopWidgetManager()" x-init="init()" class="bg-white/90 dark:bg-slate-900/80 backdrop-blur-md rounded-3xl border border-slate-200/90 dark:border-slate-800 shadow-sm p-6 sm:p-7 relative overflow-hidden transition-all">
        
        <!-- Header Widget -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 mb-5 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-blue-700 via-primary-600 to-sky-400 text-white flex items-center justify-center text-xl shadow-lg shadow-blue-500/25 flex-shrink-0 ring-4 ring-blue-50 dark:ring-slate-800">
                    <i class="fa-brands fa-windows"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white">Papan Pintasan Desktop</h2>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-blue-50 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300 border border-blue-200/70 dark:border-blue-800 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                            <span>Windows Desktop Icons</span>
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Akses cepat satu klik ke modul sistem sesuai wewenang peran Anda</p>
                </div>
            </div>

            <!-- Widget Controls & Actions -->
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-xs text-slate-600 dark:text-slate-300 font-semibold px-2.5 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800/80 border border-slate-200/60 dark:border-slate-700 flex items-center gap-1.5">
                    <i class="fa-solid fa-shapes text-primary text-[11px]"></i>
                    <span x-text="activeKeys.length + ' Disematkan'"></span>
                </span>

                <!-- Mode Atur / Edit Toggle -->
                <button type="button" @click="editMode = !editMode" 
                        class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer active:scale-95"
                        :class="editMode ? 'bg-amber-500 hover:bg-amber-600 text-white shadow-sm ring-2 ring-amber-300 dark:ring-amber-700' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200/60 dark:border-slate-700'">
                    <i class="fa-solid text-[11px]" :class="editMode ? 'fa-check' : 'fa-pen-to-square'"></i>
                    <span x-text="editMode ? 'Selesai Atur' : 'Atur Shortcut'"></span>
                </button>

                <!-- Tombol Tambah Shortcut Modal -->
                <button type="button" @click="showAddModal = true"
                        class="px-3.5 py-1.5 rounded-xl bg-gradient-to-r from-blue-700 via-primary-600 to-sky-600 hover:from-blue-600 hover:to-sky-500 text-white text-xs font-bold shadow-md shadow-blue-500/20 transition-all flex items-center gap-1.5 cursor-pointer active:scale-95">
                    <i class="fa-solid fa-plus text-[11px]"></i>
                    <span>Tambah Shortcut</span>
                </button>

                <!-- Reset ke Default -->
                <button type="button" @click="resetDefaults()" title="Kembalikan susunan ke rekomendasi default"
                        class="p-2 rounded-xl text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 border border-transparent hover:border-slate-200 dark:hover:border-slate-700 transition-all text-xs cursor-pointer"
                        :title="'Reset ke pintasan rekomendasi peran ' + '{{ $roleLabel }}'">
                    <i class="fa-solid fa-rotate-left"></i>
                </button>
            </div>
        </div>

        <!-- Banner Info Mode Edit -->
        <div x-show="editMode" 
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 -translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="mb-4 p-3 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/80 text-xs text-amber-900 dark:text-amber-200 flex items-center justify-between gap-3 shadow-xs"
             x-cloak>
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-info text-amber-600 text-sm flex-shrink-0"></i>
                <span><strong>Mode Atur Aktif:</strong> Klik tanda silang <span class="text-rose-600 font-bold">(X)</span> pada icon untuk melepas shortcut dari desktop, atau klik <strong>Selesai Atur</strong> jika sudah.</span>
            </div>
            <button type="button" @click="editMode = false" class="px-2.5 py-1 rounded-lg bg-amber-200 dark:bg-amber-800/80 hover:bg-amber-300 text-amber-900 dark:text-amber-100 font-bold text-[11px] flex-shrink-0">
                Tutup
            </button>
        </div>

        <!-- Desktop Wallpaper / Workspace Area -->
        <div class="rounded-2xl bg-gradient-to-b from-slate-50/80 via-white/50 to-slate-100/60 dark:from-slate-950/40 dark:via-slate-900/30 dark:to-slate-900/60 border border-slate-200/70 dark:border-slate-800/80 p-4 sm:p-6 min-h-[160px] relative">
            
            <!-- Grid of Windows Desktop Icons -->
            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 xl:grid-cols-9 gap-3 sm:gap-4 items-start justify-items-center">
                
                <!-- Active Desktop Shortcut Icons -->
                <template x-for="item in activeWidgets" :key="item.id">
                    <div class="group relative flex flex-col items-center justify-start p-2 sm:p-2.5 rounded-2xl w-[88px] sm:w-[96px] hover:bg-white/95 dark:hover:bg-slate-800/80 hover:shadow-lg hover:border-blue-400/50 dark:hover:border-blue-500/40 border border-transparent transition-all duration-200 cursor-pointer select-none"
                         :class="editMode ? 'ring-2 ring-amber-400/80 bg-white/70 dark:bg-slate-800/50 scale-102' : 'hover:-translate-y-1'">
                        
                        <!-- Delete / Remove Badge Button -->
                        <button type="button" 
                                @click.stop.prevent="removeWidget(item.id)" 
                                class="absolute -top-1 -right-1 z-20 w-5 h-5 rounded-full bg-rose-500 hover:bg-rose-600 text-white flex items-center justify-center text-[10px] shadow-md transition-all cursor-pointer active:scale-90"
                                :class="editMode ? 'opacity-100 scale-100' : 'opacity-0 group-hover:opacity-100 scale-90 group-hover:scale-100'"
                                :title="'Hapus shortcut ' + item.short_title">
                            <i class="fa-solid fa-xmark"></i>
                        </button>

                        <!-- Shortcut Link & Icon -->
                        <a :href="item.url" :target="item.target || '_self'" class="flex flex-col items-center w-full focus:outline-none text-center">
                            <!-- Squircle App Badge -->
                            <div class="relative w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-gradient-to-tr flex items-center justify-center text-white shadow-md transition-transform duration-200 group-hover:scale-110 group-hover:shadow-xl"
                                 :class="[item.gradient, item.shadow]">
                                <i :class="[item.icon, 'text-xl sm:text-2xl drop-shadow-md']"></i>

                                <!-- Little Windows Desktop Shortcut Badge (Curved Arrow in corner) -->
                                <div class="absolute -bottom-1 -left-1 w-4 h-4 rounded-md bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 shadow-2xs flex items-center justify-center pointer-events-none">
                                    <i class="fa-solid fa-share text-[8px] text-blue-600 dark:text-blue-400 -scale-x-100"></i>
                                </div>
                            </div>

                            <!-- Label Shortcut Underneath -->
                            <span class="mt-2 text-[11px] sm:text-xs font-bold text-slate-800 dark:text-slate-200 leading-snug line-clamp-2 w-full break-words group-hover:text-primary transition-colors text-center tracking-tight"
                                  x-text="item.short_title"
                                  :title="item.title"></span>
                        </a>
                    </div>
                </template>

                <!-- Plus / Add Shortcut Placeholder Tile -->
                <div @click="showAddModal = true"
                     class="group flex flex-col items-center justify-start p-2 sm:p-2.5 rounded-2xl w-[88px] sm:w-[96px] hover:bg-white/80 dark:hover:bg-slate-800/80 border border-transparent hover:border-dashed hover:border-primary-400/60 transition-all duration-200 cursor-pointer select-none hover:-translate-y-1"
                     title="Tambah pintasan modul baru ke Desktop">
                    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl border-2 border-dashed border-slate-300 dark:border-slate-700 group-hover:border-primary-500 group-hover:bg-primary-50/50 dark:group-hover:bg-primary-950/30 flex items-center justify-center text-slate-400 group-hover:text-primary-600 transition-all duration-200 group-hover:scale-105">
                        <i class="fa-solid fa-plus text-xl"></i>
                    </div>
                    <span class="mt-2 text-[11px] sm:text-xs font-semibold text-slate-500 dark:text-slate-400 group-hover:text-primary-600 transition-colors text-center">
                        + Tambah
                    </span>
                </div>

            </div>

            <!-- Empty State jika seluruh shortcut dihapus -->
            <div x-show="activeKeys.length === 0" class="py-10 text-center" x-cloak>
                <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 mx-auto flex items-center justify-center text-2xl mb-3">
                    <i class="fa-brands fa-windows"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-800 dark:text-white">Papan Pintasan Masih Kosong</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-sm mx-auto">
                    Anda belum menyematkan shortcut ke Desktop. Klik tombol di bawah untuk memasang rekomendasi default sesuai peran Anda.
                </p>
                <div class="mt-4 flex items-center justify-center gap-2">
                    <button type="button" @click="resetDefaults()" class="px-4 py-2 rounded-xl bg-primary hover:bg-primary-700 text-white text-xs font-bold shadow-md shadow-primary-500/20 transition-all">
                        <i class="fa-solid fa-wand-magic-sparkles mr-1.5"></i>
                        Pasang Rekomendasi Awal
                    </button>
                    <button type="button" @click="showAddModal = true" class="px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-xs font-bold hover:bg-slate-300 dark:hover:bg-slate-700 transition-all">
                        Pilih dari Katalog
                    </button>
                </div>
            </div>

        </div>

        <!-- ============================================================== -->
        <!-- MODAL KATALOG SHORTCUT (TAMBAH / KELOLA PINTASAN DESKTOP) -->
        <!-- ============================================================== -->
        <div x-show="showAddModal" 
             class="fixed inset-0 z-50 overflow-y-auto" 
             style="display: none;" 
             x-cloak
             @keydown.escape.window="showAddModal = false">
            
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-slate-950/65 backdrop-blur-xs transition-opacity" 
                 @click="showAddModal = false"
                 x-show="showAddModal"
                 x-transition:enter="ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"></div>

            <div class="flex min-h-full items-center justify-center p-3 sm:p-4 text-center">
                <div class="w-full max-w-3xl transform overflow-hidden rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-left shadow-2xl transition-all my-8 flex flex-col max-h-[90vh]"
                     x-show="showAddModal"
                     x-transition:enter="ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95">
                    
                    <!-- Modal Header -->
                    <div class="p-5 sm:p-6 border-b border-slate-100 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/90 flex items-start justify-between gap-4">
                        <div class="flex items-center gap-3.5">
                            <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-blue-700 via-primary-600 to-sky-400 text-white flex items-center justify-center text-lg shadow-md flex-shrink-0">
                                <i class="fa-brands fa-windows"></i>
                            </div>
                            <div>
                                <h3 class="text-base sm:text-lg font-black text-slate-900 dark:text-white">Katalog Shortcut Desktop</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                    Pilih modul yang ingin disematkan ke beranda sesuai hak akses <strong>{{ $roleLabel }}</strong>
                                </p>
                            </div>
                        </div>
                        <button type="button" 
                                @click="showAddModal = false" 
                                class="w-8 h-8 rounded-xl bg-slate-200/70 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white flex items-center justify-center text-sm transition-all cursor-pointer">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <!-- Search & Filter Category Bar -->
                    <div class="p-4 sm:px-6 border-b border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900 space-y-3">
                        <!-- Input Search -->
                        <div class="relative">
                            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <input type="text" 
                                   x-model="searchQuery" 
                                   placeholder="Cari modul atau menu (contoh: Work Plan, Interview, CBT, Odoo)..." 
                                   class="w-full pl-9 pr-8 py-2 rounded-xl bg-slate-100 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-xs text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white dark:focus:bg-slate-900 transition-all">
                            <button type="button" 
                                    x-show="searchQuery.length > 0" 
                                    @click="searchQuery = ''" 
                                    class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-xs">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>

                        <!-- Category Filter Pills -->
                        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 scrollbar-none">
                            @foreach($availableCategories as $catKey => $catLabel)
                                <button type="button" 
                                        @click="selectedCategory = '{{ $catKey }}'"
                                        class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap cursor-pointer"
                                        :class="selectedCategory === '{{ $catKey }}' ? 'bg-primary text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700'">
                                    {{ $catLabel }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Modal Body (Grid of Catalog Items) -->
                    <div class="p-4 sm:p-6 overflow-y-auto flex-1 max-h-[52vh] bg-slate-50/50 dark:bg-slate-950/30">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <template x-for="item in filteredCatalog" :key="item.id">
                                <div class="p-3.5 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700 shadow-2xs hover:shadow-md hover:border-blue-400/50 dark:hover:border-blue-500/40 transition-all flex items-start gap-3.5 justify-between">
                                    <div class="flex items-start gap-3 min-w-0">
                                        <!-- App Icon -->
                                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr flex items-center justify-center text-white shadow-md flex-shrink-0"
                                             :class="[item.gradient, item.shadow]">
                                            <i :class="[item.icon, 'text-lg']"></i>
                                        </div>

                                        <!-- Info -->
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <h4 class="text-xs font-bold text-slate-900 dark:text-white truncate" x-text="item.title"></h4>
                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold uppercase bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300" x-text="item.category_label"></span>
                                            </div>
                                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 leading-snug line-clamp-2" x-text="item.desc"></p>
                                        </div>
                                    </div>

                                    <!-- Action Button (Toggle / Add / Remove) -->
                                    <div class="flex-shrink-0 ml-2">
                                        <button type="button" 
                                                @click="toggleWidget(item.id)" 
                                                class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer active:scale-95 group"
                                                :class="isWidgetActive(item.id) ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-300/80 dark:border-emerald-800 hover:bg-rose-50 hover:text-rose-600 hover:border-rose-300' : 'bg-primary hover:bg-primary-700 text-white shadow-xs'">
                                            <template x-if="isWidgetActive(item.id)">
                                                <span class="flex items-center gap-1.5">
                                                    <i class="fa-solid fa-check group-hover:hidden"></i>
                                                    <i class="fa-solid fa-xmark hidden group-hover:inline"></i>
                                                    <span class="group-hover:hidden">Terpasang</span>
                                                    <span class="hidden group-hover:inline">Lepas</span>
                                                </span>
                                            </template>
                                            <template x-if="!isWidgetActive(item.id)">
                                                <span class="flex items-center gap-1">
                                                    <i class="fa-solid fa-plus text-[10px]"></i>
                                                    <span>Pasang</span>
                                                </span>
                                            </template>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Empty Catalog Search Result -->
                        <div x-show="filteredCatalog.length === 0" class="py-12 text-center" x-cloak>
                            <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 mx-auto flex items-center justify-center text-xl mb-2">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </div>
                            <p class="text-xs font-semibold text-slate-700 dark:text-slate-300">Tidak ada modul yang cocok dengan pencarian.</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Coba kata kunci lain atau pilih tab kategori 'Semua Modul'.</p>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="p-4 sm:px-6 border-t border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
                        <div class="text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                            <i class="fa-solid fa-shield-halved text-primary"></i>
                            <span>Daftar modul dibatasi otomatis sesuai wewenang akun Anda.</span>
                        </div>
                        <button type="button" 
                                @click="showAddModal = false" 
                                class="w-full sm:w-auto px-5 py-2 rounded-xl bg-slate-900 dark:bg-slate-800 hover:bg-slate-800 dark:hover:bg-slate-700 text-white font-bold text-xs shadow-sm transition-all cursor-pointer">
                            Selesai & Tutup
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <!-- Floating Feedback Toast -->
        <div x-show="showToast" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-3 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-3 scale-95"
             class="fixed bottom-6 right-6 z-50 px-4 py-2.5 rounded-2xl bg-slate-900/95 dark:bg-slate-800/95 text-white text-xs font-semibold shadow-2xl border border-slate-700/80 backdrop-blur-md flex items-center gap-2.5 pointer-events-none"
             x-cloak>
            <span class="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-[10px]">
                <i class="fa-solid fa-check"></i>
            </span>
            <span x-text="toastMessage"></span>
        </div>

    </div>

    <!-- 3. INFORMASI IDENTITAS & PANDUAN -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- KARTU INFORMASI AKUN / PROFIL (2 KOLOM) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-7 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-primary flex items-center justify-center text-lg">
                            <i class="fa-solid fa-address-card"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-900">Informasi Akun & Pengguna</h2>
                            <p class="text-xs text-slate-500">Detail identitas Anda yang terdaftar pada sistem</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-700">
                        {{ $isAdmin ? 'Admin View' : 'User View' }}
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-y-5 gap-x-6 text-xs">
                    <div class="space-y-1">
                        <div class="text-slate-400 font-semibold uppercase tracking-wider text-[10px]">Nama Lengkap</div>
                        <div class="text-slate-800 font-bold text-sm">{{ $userName }}</div>
                    </div>

                    <div class="space-y-1">
                        <div class="text-slate-400 font-semibold uppercase tracking-wider text-[10px]">Alamat Email</div>
                        <div class="text-slate-800 font-medium flex items-center gap-1.5">
                            <i class="fa-regular fa-envelope text-slate-400"></i>
                            <span>{{ $userEmail }}</span>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <div class="text-slate-400 font-semibold uppercase tracking-wider text-[10px]">Hak Akses / Peran</div>
                        <div>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md font-bold text-[11px] {{ $isAdmin ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                                <i class="fa-solid {{ $isAdmin ? 'fa-shield-halved' : 'fa-user-check' }} text-[10px]"></i>
                                {{ $roleLabel }}
                            </span>
                        </div>
                    </div>

                    @if($employee)
                        <div class="space-y-1">
                            <div class="text-slate-400 font-semibold uppercase tracking-wider text-[10px]">Nomor Induk Karyawan (NIK)</div>
                            <div class="text-slate-800 font-bold font-mono tracking-wider">{{ $employee->nik }}</div>
                        </div>

                        <div class="space-y-1">
                            <div class="text-slate-400 font-semibold uppercase tracking-wider text-[10px]">Jabatan / Posisi</div>
                            <div class="text-slate-800 font-semibold">{{ $employee->jabatan ?: 'Staff Operasional' }}</div>
                        </div>

                        <div class="space-y-1">
                            <div class="text-slate-400 font-semibold uppercase tracking-wider text-[10px]">Penempatan Area</div>
                            <div class="text-slate-800 font-medium flex items-center gap-1.5">
                                <i class="fa-solid fa-location-dot text-slate-400"></i>
                                <span>{{ $employee->area ?: '-' }} {{ $employee->regional ? '('.$employee->regional.')' : '' }}</span>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <div class="text-slate-400 font-semibold uppercase tracking-wider text-[10px]">Prinsiple / Mitra</div>
                            <div class="text-slate-800 font-semibold flex items-center gap-1.5">
                                <i class="fa-solid fa-building text-slate-400"></i>
                                <span>{{ $employee->prinsiple ?: 'PT Arina Multikarya' }}</span>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <div class="text-slate-400 font-semibold uppercase tracking-wider text-[10px]">Tipe & Status Pegawai</div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $employee->tipe_karyawan === 'Inhouse' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                    {{ $employee->tipe_karyawan }}
                                </span>
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    {{ strtoupper($employee->status) }}
                                </span>
                            </div>
                        </div>
                    @else
                        <div class="space-y-1">
                            <div class="text-slate-400 font-semibold uppercase tracking-wider text-[10px]">Jabatan</div>
                            <div class="text-slate-800 font-bold flex items-center gap-1.5">
                                <i class="fa-solid fa-briefcase text-slate-400"></i>
                                <span>{{ $userJabatan }}</span>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <div class="text-slate-400 font-semibold uppercase tracking-wider text-[10px]">Lingkup Sistem</div>
                            <div class="text-slate-800 font-medium">Enterprise Management, Talent Pool & Odoo ERP</div>
                        </div>

                        <div class="space-y-1">
                            <div class="text-slate-400 font-semibold uppercase tracking-wider text-[10px]">Entitas Operasional</div>
                            <div class="text-slate-800 font-medium">ESA Groups (AMK, AKP, ATK, ABO, ATB)</div>
                        </div>

                        <div class="space-y-1">
                            <div class="text-slate-400 font-semibold uppercase tracking-wider text-[10px]">Keamanan & Sesi</div>
                            <div class="text-emerald-700 font-semibold flex items-center gap-1">
                                <i class="fa-solid fa-shield-check"></i> Sesi Login Terproteksi
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500">
                <span class="flex items-center gap-1.5">
                    <i class="fa-solid fa-info-circle text-primary"></i>
                    <span>Informasi profil disinkronkan secara otomatis dari database ASystem.</span>
                </span>
                <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
                    @if(session()->has('impersonator_id'))
                    <a href="{{ route('user.switch-back') }}" class="inline-flex items-center gap-1.5 text-white font-bold py-1.5 px-3 rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 transition-all shadow-sm">
                        <i class="fa-solid fa-rotate-left text-xs"></i>
                        <span>Kembali ke User Asli ({{ session('impersonator_name', 'Administrator') }})</span>
                    </a>
                    @endif
                    <a href="{{ route('profile.index') }}" class="inline-flex items-center gap-1.5 text-primary hover:text-primary-700 font-bold py-1.5 px-3 rounded-xl bg-primary-50 hover:bg-primary-100 transition-all border border-primary-200 shadow-2xs">
                        <i class="fa-solid fa-user-pen text-xs"></i>
                        <span>Edit Profil / Password</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline m-0">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1.5 text-rose-600 hover:text-rose-700 font-semibold py-1.5 px-3 rounded-xl hover:bg-rose-50 transition-all">
                            <i class="fa-solid fa-power-off text-xs"></i>
                            <span>Keluar (Logout)</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- KARTU PANDUAN NAVIGASI (1 KOLOM) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-7 flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-3 pb-4 mb-4 border-b border-slate-100">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-compass"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Panduan Navigasi</h2>
                        <p class="text-xs text-slate-500">Cara menggunakan sistem</p>
                    </div>
                </div>

                <div class="space-y-3.5 text-xs text-slate-600">
                    <div class="flex items-start gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <div class="w-6 h-6 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5">
                            1
                        </div>
                        <div>
                            <div class="font-bold text-slate-800">Menu Navigasi Samping</div>
                            <p class="text-slate-500 mt-0.5 leading-relaxed">
                                Gunakan bilah menu di sebelah kiri untuk berpindah ke modul yang ingin Anda gunakan.
                            </p>
                        </div>
                    </div>

                    @if($isAdmin)
                    <div class="flex items-start gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <div class="w-6 h-6 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5">
                            2
                        </div>
                        <div>
                            <div class="font-bold text-slate-800">Menu Master Data</div>
                            <p class="text-slate-500 mt-0.5 leading-relaxed">
                                Menu Master Data hanya tampil dan dapat dikelola oleh akun dengan akses Administrator.
                            </p>
                        </div>
                    </div>
                    @endif

                    <div class="flex items-start gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <div class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5">
                            {{ $isAdmin ? '3' : '2' }}
                        </div>
                        <div>
                            <div class="font-bold text-slate-800">Bantuan & Kendala</div>
                            <p class="text-slate-500 mt-0.5 leading-relaxed">
                                Jika menemukan kendala akses atau data yang tidak sesuai, hubungi tim IT Support / HRD.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5 p-3 rounded-xl bg-blue-50/70 border border-blue-100 text-[11px] text-blue-800 flex items-center gap-2">
                <i class="fa-solid fa-lock text-blue-600 text-sm flex-shrink-0"></i>
                <span>Pastikan selalu logout setelah selesai jika menggunakan perangkat komputer bersama.</span>
            </div>

            <!-- KARTU PWA WINDOWS APP INSTALL -->
            <div class="mt-4 p-4 rounded-xl bg-gradient-to-br from-blue-50 via-indigo-50 to-sky-50 border border-blue-200/90 flex items-center justify-between gap-3 shadow-xs" id="home-pwa-card">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-700 via-primary to-sky-500 text-white flex items-center justify-center text-lg shadow-md shadow-blue-500/25 flex-shrink-0">
                        <i class="fa-brands fa-windows"></i>
                    </div>
                    <div>
                        <div class="font-bold text-slate-900 text-xs">Pasang ASystem di Windows</div>
                        <p class="text-[11px] text-slate-500 mt-0.5 leading-tight">Buka langsung dari desktop tanpa browser</p>
                    </div>
                </div>
                <button type="button" 
                        onclick="window.installAsystemPwa && window.installAsystemPwa()" 
                        class="py-2 px-3.5 rounded-xl bg-gradient-to-r from-blue-700 via-primary to-sky-600 hover:from-blue-600 hover:to-sky-500 text-white font-bold text-xs shadow-md shadow-blue-500/20 transition-all flex items-center gap-1.5 flex-shrink-0 cursor-pointer active:scale-95">
                    <i class="fa-solid fa-download text-[11px]"></i>
                    <span>Install App</span>
                </button>
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
        var pwaCard = document.getElementById('home-pwa-card');
        if (pwaCard && isStandalone) {
            pwaCard.style.display = 'none';
        }
    });

    /**
     * Windows Desktop Widget Manager (Alpine.js Component)
     * Mengelola shortcut modul beranda berbasis hak akses peran pengguna & persistensi LocalStorage
     */
    function desktopWidgetManager() {
        return {
            availableWidgets: @json($allowedWidgets),
            defaultKeys: @json($defaultWidgetKeys),
            userId: {{ (int) (Auth::id() ?? 0) }},
            activeKeys: [],
            editMode: false,
            showAddModal: false,
            searchQuery: '',
            selectedCategory: 'all',
            toastMessage: '',
            showToast: false,
            _toastTimer: null,

            init() {
                var storageKey = 'asystem_desktop_widgets_' + this.userId;
                var stored = null;
                try {
                    var raw = localStorage.getItem(storageKey);
                    if (raw) {
                        var parsed = JSON.parse(raw);
                        if (Array.isArray(parsed) && parsed.length > 0) {
                            // Filter ketat: Hanya masukkan modul yang diizinkan untuk peran pengguna saat ini
                            var validKeys = parsed.filter(key => Boolean(this.availableWidgets[key]));
                            if (validKeys.length > 0) {
                                stored = validKeys;
                            }
                        }
                    }
                } catch (e) {
                    console.warn('Error reading desktop widgets from localStorage', e);
                }

                if (stored && stored.length > 0) {
                    this.activeKeys = stored;
                } else {
                    this.activeKeys = [...this.defaultKeys];
                    this.save();
                }
            },

            save() {
                try {
                    var storageKey = 'asystem_desktop_widgets_' + this.userId;
                    localStorage.setItem(storageKey, JSON.stringify(this.activeKeys));
                } catch (e) {
                    console.warn('Error saving desktop widgets to localStorage', e);
                }
            },

            get activeWidgets() {
                return this.activeKeys
                    .map(key => this.availableWidgets[key])
                    .filter(Boolean);
            },

            get filteredCatalog() {
                var q = this.searchQuery.toLowerCase().trim();
                var cat = this.selectedCategory;
                var all = Object.values(this.availableWidgets);

                return all.filter(item => {
                    var matchCat = (cat === 'all' || item.category === cat);
                    var matchQuery = !q || 
                        item.title.toLowerCase().includes(q) || 
                        item.short_title.toLowerCase().includes(q) || 
                        (item.desc && item.desc.toLowerCase().includes(q)) ||
                        (item.category_label && item.category_label.toLowerCase().includes(q));
                    return matchCat && matchQuery;
                });
            },

            isWidgetActive(key) {
                return this.activeKeys.includes(key);
            },

            addWidget(key) {
                if (!this.availableWidgets[key]) return;
                if (!this.activeKeys.includes(key)) {
                    this.activeKeys.push(key);
                    this.save();
                    this.notify('"' + this.availableWidgets[key].short_title + '" disematkan ke Desktop!');
                }
            },

            removeWidget(key) {
                var widget = this.availableWidgets[key];
                var title = widget ? widget.short_title : 'Shortcut';
                this.activeKeys = this.activeKeys.filter(k => k !== key);
                this.save();
                this.notify('"' + title + '" dilepas dari Desktop.');
            },

            toggleWidget(key) {
                if (this.isWidgetActive(key)) {
                    this.removeWidget(key);
                } else {
                    this.addWidget(key);
                }
            },

            resetDefaults() {
                if (confirm('Kembalikan susunan pintasan Desktop ke rekomendasi awal peran Anda?')) {
                    this.activeKeys = [...this.defaultKeys];
                    this.save();
                    this.notify('Papan pintasan Desktop berhasil direset ke default.');
                }
            },

            notify(message) {
                this.toastMessage = message;
                this.showToast = true;
                if (this._toastTimer) clearTimeout(this._toastTimer);
                this._toastTimer = setTimeout(() => {
                    this.showToast = false;
                }, 2500);
            }
        };
    }
</script>
@endpush

