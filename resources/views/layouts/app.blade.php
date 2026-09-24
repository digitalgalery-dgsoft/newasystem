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

    <!-- Immediate Theme Application (Zero-Flicker LocalStorage) -->
    <script>
        (function() {
            try {
                var theme = localStorage.getItem('asystem_theme_mode') || 'light';
                var palette = localStorage.getItem('asystem_theme_palette') || 'navy';
                var primary = localStorage.getItem('asystem_primary_color') || '#0F52BA';
                document.documentElement.setAttribute('data-theme', theme);
                document.documentElement.setAttribute('data-palette', palette);
                document.documentElement.style.setProperty('--color-primary', primary);
                if (theme === 'dark') {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            } catch(e) {}
        })();
    </script>

    <!-- Tailwind CSS Play CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            DEFAULT: 'var(--color-primary, #0F52BA)', // Dynamic Custom Primary Color
                            50: '#eef6ff',
                            100: '#d9ebff',
                            200: '#bce0fd',
                            500: '#2563eb',
                            600: 'var(--color-primary, #0F52BA)',
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
    
    <!-- Custom CSS matching Attendance Portal & Multi-Palette Dark Mode -->
    <style>
        :root {
            --color-primary: #0F52BA;
        }

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

        .bg-primary { background-color: var(--color-primary) !important; }
        .text-primary { color: var(--color-primary) !important; }
        .border-primary { border-color: var(--color-primary) !important; }

        .sidebar-item-active {
            background: linear-gradient(135deg, var(--color-primary, #0F52BA) 0%, #2563eb 100%) !important;
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

        /* ============================================================
           DARK MODE COLOR PALETTES (Hitam Pekat, Biru Navy, Dark Grey, Soft Grey)
           ============================================================ */

        /* 1. Hitam Pekat (Pitch Black) */
        html[data-theme="dark"][data-palette="black"] {
            --bg-body: #050505;
            --bg-sidebar: #09090b;
            --bg-topbar: #0c0c0f;
            --bg-card: #121215;
            --bg-card-alt: #18181f;
            --bg-input: #0f0f14;
            --border-color: #26262e;
            --border-subtle: #1c1c24;
            --text-title: #f8fafc;
            --text-body: #cbd5e1;
            --text-muted: #71717a;
        }

        /* 2. Biru Navy (Deep Navy Blue - Default Dark) */
        html[data-theme="dark"][data-palette="navy"],
        html[data-theme="dark"]:not([data-palette]) {
            --bg-body: #070d1e;
            --bg-sidebar: #0b142d;
            --bg-topbar: #0f1c3f;
            --bg-card: #13224d;
            --bg-card-alt: #172a5e;
            --bg-input: #0e1a3d;
            --border-color: #213775;
            --border-subtle: #182a5c;
            --text-title: #f1f5f9;
            --text-body: #cbd5e1;
            --text-muted: #8295b5;
        }

        /* 3. Dark Grey (Charcoal / Carbon Dark) */
        html[data-theme="dark"][data-palette="dark_grey"] {
            --bg-body: #121316;
            --bg-sidebar: #18191e;
            --bg-topbar: #1d1f25;
            --bg-card: #23252d;
            --bg-card-alt: #2a2c36;
            --bg-input: #1b1d24;
            --border-color: #383b48;
            --border-subtle: #2c2e39;
            --text-title: #f1f5f9;
            --text-body: #cbd5e1;
            --text-muted: #8c92a4;
        }

        /* 4. Soft Grey (Titanium / Muted Dark) */
        html[data-theme="dark"][data-palette="soft_grey"] {
            --bg-body: #23252b;
            --bg-sidebar: #2b2d35;
            --bg-topbar: #32353e;
            --bg-card: #393c47;
            --bg-card-alt: #414552;
            --bg-input: #2e313b;
            --border-color: #525666;
            --border-subtle: #434756;
            --text-title: #f8fafc;
            --text-body: #e2e8f0;
            --text-muted: #9fa6b7;
        }

        /* DARK MODE CONTAINER & ELEMENT AUTOMATIC OVERRIDES */
        html[data-theme="dark"] body {
            background-color: var(--bg-body) !important;
            color: var(--text-body) !important;
        }

        html[data-theme="dark"] aside#sidebar {
            background-color: var(--bg-sidebar) !important;
            border-color: var(--border-color) !important;
        }

        html[data-theme="dark"] aside#sidebar .border-b,
        html[data-theme="dark"] aside#sidebar .border-t {
            border-color: var(--border-color) !important;
        }

        html[data-theme="dark"] aside#sidebar .bg-white {
            background-color: var(--bg-card) !important;
            border-color: var(--border-color) !important;
        }

        html[data-theme="dark"] aside#sidebar .bg-slate-50\/50,
        html[data-theme="dark"] aside#sidebar .bg-slate-100 {
            background-color: var(--bg-card-alt) !important;
        }

        html[data-theme="dark"] header.topbar-shadow {
            background-color: var(--bg-topbar) !important;
            border-color: var(--border-color) !important;
        }

        html[data-theme="dark"] .bg-white {
            background-color: var(--bg-card) !important;
            border-color: var(--border-color) !important;
        }

        html[data-theme="dark"] .bg-slate-50,
        html[data-theme="dark"] .bg-slate-50\/20,
        html[data-theme="dark"] .bg-slate-50\/30,
        html[data-theme="dark"] .bg-slate-50\/40,
        html[data-theme="dark"] .bg-slate-50\/50,
        html[data-theme="dark"] .bg-slate-50\/60,
        html[data-theme="dark"] .bg-slate-50\/70,
        html[data-theme="dark"] .bg-slate-50\/75,
        html[data-theme="dark"] .bg-slate-50\/80,
        html[data-theme="dark"] .bg-slate-50\/90,
        html[data-theme="dark"] .bg-slate-100,
        html[data-theme="dark"] .bg-slate-100\/50,
        html[data-theme="dark"] .bg-slate-100\/70,
        html[data-theme="dark"] .bg-slate-100\/80,
        html[data-theme="dark"] .bg-slate-200 {
            background-color: var(--bg-card-alt) !important;
            border-color: var(--border-subtle) !important;
        }

        html[data-theme="dark"] .hover\:bg-white:hover {
            background-color: rgba(255, 255, 255, 0.08) !important;
        }
        html[data-theme="dark"] .hover\:bg-slate-50:hover,
        html[data-theme="dark"] .hover\:bg-slate-50\/50:hover,
        html[data-theme="dark"] .hover\:bg-slate-50\/70:hover,
        html[data-theme="dark"] .hover\:bg-slate-50\/80:hover,
        html[data-theme="dark"] .hover\:bg-slate-100:hover,
        html[data-theme="dark"] .hover\:bg-slate-100\/70:hover {
            background-color: rgba(255, 255, 255, 0.06) !important;
        }

        html[data-theme="dark"] .bg-rose-50,
        html[data-theme="dark"] .bg-rose-50\/80,
        html[data-theme="dark"] .bg-red-50 {
            background-color: rgba(244, 63, 94, 0.12) !important;
            border-color: rgba(244, 63, 94, 0.25) !important;
        }
        html[data-theme="dark"] .text-rose-600,
        html[data-theme="dark"] .text-rose-700,
        html[data-theme="dark"] .text-red-600 {
            color: #fb7185 !important;
        }

        html[data-theme="dark"] .bg-emerald-50,
        html[data-theme="dark"] .bg-emerald-50\/80,
        html[data-theme="dark"] .bg-green-50 {
            background-color: rgba(16, 185, 129, 0.12) !important;
            border-color: rgba(16, 185, 129, 0.25) !important;
        }
        html[data-theme="dark"] .text-emerald-700,
        html[data-theme="dark"] .text-emerald-800,
        html[data-theme="dark"] .text-green-700 {
            color: #34d399 !important;
        }

        html[data-theme="dark"] .bg-blue-50,
        html[data-theme="dark"] .bg-blue-50\/80 {
            background-color: rgba(59, 130, 246, 0.12) !important;
            border-color: rgba(59, 130, 246, 0.25) !important;
        }
        html[data-theme="dark"] .text-blue-700,
        html[data-theme="dark"] .text-blue-800 {
            color: #60a5fa !important;
        }

        html[data-theme="dark"] .bg-purple-50,
        html[data-theme="dark"] .bg-purple-50\/80 {
            background-color: rgba(168, 85, 247, 0.12) !important;
            border-color: rgba(168, 85, 247, 0.25) !important;
        }
        html[data-theme="dark"] .text-purple-700,
        html[data-theme="dark"] .text-purple-800 {
            color: #c084fc !important;
        }

        html[data-theme="dark"] .bg-amber-50,
        html[data-theme="dark"] .bg-amber-50\/80 {
            background-color: rgba(245, 158, 11, 0.12) !important;
            border-color: rgba(245, 158, 11, 0.25) !important;
        }
        html[data-theme="dark"] .text-amber-700,
        html[data-theme="dark"] .text-amber-800 {
            color: #fbbf24 !important;
        }

        html[data-theme="dark"] .border-slate-200,
        html[data-theme="dark"] .border-slate-200\/90,
        html[data-theme="dark"] .border-slate-200\/80,
        html[data-theme="dark"] .border-slate-100,
        html[data-theme="dark"] .border-slate-300 {
            border-color: var(--border-color) !important;
        }

        html[data-theme="dark"] .divide-slate-100 > :not([hidden]) ~ :not([hidden]),
        html[data-theme="dark"] .divide-slate-200 > :not([hidden]) ~ :not([hidden]) {
            border-color: var(--border-subtle) !important;
        }

        html[data-theme="dark"] .text-slate-900,
        html[data-theme="dark"] .text-slate-800 {
            color: var(--text-title) !important;
        }

        html[data-theme="dark"] .text-slate-700,
        html[data-theme="dark"] .text-slate-600 {
            color: var(--text-body) !important;
        }

        html[data-theme="dark"] .text-slate-500,
        html[data-theme="dark"] .text-slate-400 {
            color: var(--text-muted) !important;
        }

        html[data-theme="dark"] input:not([type="checkbox"]):not([type="radio"]):not([type="color"]),
        html[data-theme="dark"] select,
        html[data-theme="dark"] textarea {
            background-color: var(--bg-input) !important;
            border-color: var(--border-color) !important;
            color: var(--text-title) !important;
        }

        html[data-theme="dark"] input::placeholder,
        html[data-theme="dark"] textarea::placeholder {
            color: var(--text-muted) !important;
        }

        html[data-theme="dark"] .table-card,
        html[data-theme="dark"] .page-header-card,
        html[data-theme="dark"] .stat-box {
            background-color: var(--bg-card) !important;
            border-color: var(--border-color) !important;
        }

        html[data-theme="dark"] .custom-table th {
            background-color: var(--bg-card-alt) !important;
            color: var(--text-title) !important;
            border-color: var(--border-color) !important;
        }

        html[data-theme="dark"] .custom-table td {
            color: var(--text-body) !important;
            border-color: var(--border-subtle) !important;
        }

        html[data-theme="dark"] .custom-table tr:hover td {
            background-color: var(--bg-card-alt) !important;
        }

        html[data-theme="dark"] ::-webkit-scrollbar-track {
            background: var(--bg-body);
        }
        html[data-theme="dark"] ::-webkit-scrollbar-thumb {
            background: var(--border-color);
        }
        html[data-theme="dark"] ::-webkit-scrollbar-thumb:hover {
            background: var(--text-muted);
        }

        /* ============================================================
           TAHAPAN REKRUTMEN ODOO ERP MINI-CARDS
           ============================================================ */
        html[data-theme="dark"] .odoo-stat-card {
            background-color: var(--bg-card-alt) !important;
            border-color: var(--border-color) !important;
        }

        html[data-theme="dark"] .odoo-stat-card:hover {
            background-color: rgba(255, 255, 255, 0.08) !important;
            border-color: rgba(255, 255, 255, 0.25) !important;
            transform: translateY(-1px);
        }

        html[data-theme="dark"] .odoo-stat-card .odoo-card-label {
            color: #94a3b8 !important;
        }

        html[data-theme="dark"] .odoo-stat-card:hover .odoo-card-label {
            color: #cbd5e1 !important;
        }

        html[data-theme="dark"] .odoo-stat-card .odoo-card-value {
            color: #ffffff !important;
        }

        /* Active Highlight States in Dark Mode */
        html[data-theme="dark"] .odoo-stat-card-active {
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.4) !important;
        }

        /* 1. Matched / Semua Odoo (Purple) */
        html[data-theme="dark"] .odoo-stat-card.odoo-stat-matched.odoo-stat-card-active {
            background-color: rgba(168, 85, 247, 0.2) !important;
            border-color: #c084fc !important;
            box-shadow: 0 0 14px rgba(168, 85, 247, 0.35) !important;
        }
        html[data-theme="dark"] .odoo-stat-card.odoo-stat-matched.odoo-stat-card-active .odoo-card-label {
            color: #e9d5ff !important;
        }
        html[data-theme="dark"] .odoo-stat-card.odoo-stat-matched.odoo-stat-card-active .odoo-card-value {
            color: #ffffff !important;
        }

        /* 2. Data Pelamar (Blue) */
        html[data-theme="dark"] .odoo-stat-card.odoo-stat-blue.odoo-stat-card-active {
            background-color: rgba(59, 130, 246, 0.2) !important;
            border-color: #60a5fa !important;
            box-shadow: 0 0 14px rgba(59, 130, 246, 0.35) !important;
        }
        html[data-theme="dark"] .odoo-stat-card.odoo-stat-blue.odoo-stat-card-active .odoo-card-label {
            color: #bfdbfe !important;
        }
        html[data-theme="dark"] .odoo-stat-card.odoo-stat-blue.odoo-stat-card-active .odoo-card-value {
            color: #ffffff !important;
        }

        /* 3. Interview (Indigo) */
        html[data-theme="dark"] .odoo-stat-card.odoo-stat-indigo.odoo-stat-card-active {
            background-color: rgba(99, 102, 241, 0.2) !important;
            border-color: #818cf8 !important;
            box-shadow: 0 0 14px rgba(99, 102, 241, 0.35) !important;
        }
        html[data-theme="dark"] .odoo-stat-card.odoo-stat-indigo.odoo-stat-card-active .odoo-card-label {
            color: #c7d2fe !important;
        }
        html[data-theme="dark"] .odoo-stat-card.odoo-stat-indigo.odoo-stat-card-active .odoo-card-value {
            color: #ffffff !important;
        }

        /* 4. Principal (Violet) */
        html[data-theme="dark"] .odoo-stat-card.odoo-stat-violet.odoo-stat-card-active {
            background-color: rgba(139, 92, 246, 0.2) !important;
            border-color: #a78bfa !important;
            box-shadow: 0 0 14px rgba(139, 92, 246, 0.35) !important;
        }
        html[data-theme="dark"] .odoo-stat-card.odoo-stat-violet.odoo-stat-card-active .odoo-card-label {
            color: #ddd6fe !important;
        }
        html[data-theme="dark"] .odoo-stat-card.odoo-stat-violet.odoo-stat-card-active .odoo-card-value {
            color: #ffffff !important;
        }

        /* 5. E-Learning (Sky) */
        html[data-theme="dark"] .odoo-stat-card.odoo-stat-sky.odoo-stat-card-active {
            background-color: rgba(14, 165, 233, 0.2) !important;
            border-color: #38bdf8 !important;
            box-shadow: 0 0 14px rgba(14, 165, 233, 0.35) !important;
        }
        html[data-theme="dark"] .odoo-stat-card.odoo-stat-sky.odoo-stat-card-active .odoo-card-label {
            color: #bae6fd !important;
        }
        html[data-theme="dark"] .odoo-stat-card.odoo-stat-sky.odoo-stat-card-active .odoo-card-value {
            color: #ffffff !important;
        }

        /* 6. PKWT (Amber) */
        html[data-theme="dark"] .odoo-stat-card.odoo-stat-amber.odoo-stat-card-active {
            background-color: rgba(245, 158, 11, 0.2) !important;
            border-color: #fbbf24 !important;
            box-shadow: 0 0 14px rgba(245, 158, 11, 0.35) !important;
        }
        html[data-theme="dark"] .odoo-stat-card.odoo-stat-amber.odoo-stat-card-active .odoo-card-label {
            color: #fef08a !important;
        }
        html[data-theme="dark"] .odoo-stat-card.odoo-stat-amber.odoo-stat-card-active .odoo-card-value {
            color: #ffffff !important;
        }

        /* 7. Joined (Emerald) */
        html[data-theme="dark"] .odoo-stat-card.odoo-stat-emerald.odoo-stat-card-active {
            background-color: rgba(16, 185, 129, 0.2) !important;
            border-color: #34d399 !important;
            box-shadow: 0 0 14px rgba(16, 185, 129, 0.35) !important;
        }
        html[data-theme="dark"] .odoo-stat-card.odoo-stat-emerald.odoo-stat-card-active .odoo-card-label {
            color: #a7f3d0 !important;
        }
        html[data-theme="dark"] .odoo-stat-card.odoo-stat-emerald.odoo-stat-card-active .odoo-card-value {
            color: #ffffff !important;
        }

        /* 8. Belum di Odoo (Slate) */
        html[data-theme="dark"] .odoo-stat-card.odoo-stat-slate.odoo-stat-card-active {
            background-color: rgba(148, 163, 184, 0.2) !important;
            border-color: #cbd5e1 !important;
            box-shadow: 0 0 14px rgba(148, 163, 184, 0.35) !important;
        }
        html[data-theme="dark"] .odoo-stat-card.odoo-stat-slate.odoo-stat-card-active .odoo-card-label {
            color: #f8fafc !important;
        }
        html[data-theme="dark"] .odoo-stat-card.odoo-stat-slate.odoo-stat-card-active .odoo-card-value {
            color: #ffffff !important;
        }

        /* Dark Mode Icon Badges */
        html[data-theme="dark"] .odoo-icon-purple {
            background-color: rgba(168, 85, 247, 0.22) !important;
            color: #d8b4fe !important;
            border: 1px solid rgba(168, 85, 247, 0.35) !important;
        }
        html[data-theme="dark"] .odoo-icon-blue {
            background-color: rgba(59, 130, 246, 0.22) !important;
            color: #93c5fd !important;
            border: 1px solid rgba(59, 130, 246, 0.35) !important;
        }
        html[data-theme="dark"] .odoo-icon-indigo {
            background-color: rgba(99, 102, 241, 0.22) !important;
            color: #a5b4fc !important;
            border: 1px solid rgba(99, 102, 241, 0.35) !important;
        }
        html[data-theme="dark"] .odoo-icon-violet {
            background-color: rgba(139, 92, 246, 0.22) !important;
            color: #c4b5fd !important;
            border: 1px solid rgba(139, 92, 246, 0.35) !important;
        }
        html[data-theme="dark"] .odoo-icon-sky {
            background-color: rgba(14, 165, 233, 0.22) !important;
            color: #7dd3fc !important;
            border: 1px solid rgba(14, 165, 233, 0.35) !important;
        }
        html[data-theme="dark"] .odoo-icon-amber {
            background-color: rgba(245, 158, 11, 0.22) !important;
            color: #fde68a !important;
            border: 1px solid rgba(245, 158, 11, 0.35) !important;
        }
        html[data-theme="dark"] .odoo-icon-emerald {
            background-color: rgba(16, 185, 129, 0.22) !important;
            color: #6ee7b7 !important;
            border: 1px solid rgba(16, 185, 129, 0.35) !important;
        }
        html[data-theme="dark"] .odoo-icon-slate {
            background-color: rgba(148, 163, 184, 0.22) !important;
            color: #cbd5e1 !important;
            border: 1px solid rgba(148, 163, 184, 0.35) !important;
        }

        /* Reset Button */
        html[data-theme="dark"] .odoo-reset-btn {
            background-color: rgba(244, 63, 94, 0.15) !important;
            color: #fda4af !important;
            border: 1px solid rgba(244, 63, 94, 0.35) !important;
        }
        html[data-theme="dark"] .odoo-reset-btn:hover {
            background-color: rgba(244, 63, 94, 0.25) !important;
            color: #ffffff !important;
        }

        /* ============================================================
           JOB STATISTIK TABLES (DARK MODE OPTIMIZATION)
           ============================================================ */
        html[data-theme="dark"] .job-stats-card {
            background-color: var(--bg-card) !important;
            border-color: var(--border-color) !important;
        }

        html[data-theme="dark"] .job-stats-header-area {
            background: linear-gradient(135deg, rgba(8, 145, 178, 0.25), rgba(15, 23, 42, 0.6)) !important;
            border-bottom: 1px solid rgba(8, 145, 178, 0.35) !important;
        }

        html[data-theme="dark"] .job-stats-header-user {
            background: linear-gradient(135deg, rgba(2, 132, 199, 0.25), rgba(15, 23, 42, 0.6)) !important;
            border-bottom: 1px solid rgba(2, 132, 199, 0.35) !important;
        }

        html[data-theme="dark"] .job-stats-header-detail {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.2), rgba(15, 23, 42, 0.6)) !important;
            border-bottom: 1px solid rgba(16, 185, 129, 0.35) !important;
        }

        html[data-theme="dark"] .job-stats-header-odoo {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.25), rgba(15, 23, 42, 0.6)) !important;
            border-bottom: 1px solid rgba(99, 102, 241, 0.35) !important;
        }

        /* Table Column Headers (thead) */
        html[data-theme="dark"] .job-stats-table thead {
            background-color: var(--bg-card-alt) !important;
        }

        html[data-theme="dark"] .job-stats-table thead th {
            color: #cbd5e1 !important;
            border-color: var(--border-color) !important;
            background-color: var(--bg-card-alt) !important;
        }

        /* Specific Thead Highlights in Dark Mode */
        html[data-theme="dark"] .job-stats-table thead th.th-green {
            background-color: rgba(16, 185, 129, 0.18) !important;
            color: #6ee7b7 !important;
            border-bottom: 2px solid #10b981 !important;
        }

        html[data-theme="dark"] .job-stats-table thead th.th-yellow {
            background-color: rgba(245, 158, 11, 0.18) !important;
            color: #fde68a !important;
            border-bottom: 2px solid #f59e0b !important;
        }

        html[data-theme="dark"] .job-stats-table thead th.th-red {
            background-color: rgba(244, 63, 94, 0.18) !important;
            color: #fda4af !important;
            border-bottom: 2px solid #f43f5e !important;
        }

        html[data-theme="dark"] .job-stats-table thead th.th-total {
            background-color: rgba(255, 255, 255, 0.08) !important;
            color: #ffffff !important;
            border-bottom: 2px solid rgba(255, 255, 255, 0.2) !important;
        }

        html[data-theme="dark"] .job-stats-table thead th.th-step-pelamar {
            background-color: rgba(14, 165, 233, 0.18) !important;
            color: #7dd3fc !important;
            border-bottom: 2px solid #0ea5e9 !important;
        }

        html[data-theme="dark"] .job-stats-table thead th.th-step-interview {
            background-color: rgba(99, 102, 241, 0.18) !important;
            color: #a5b4fc !important;
            border-bottom: 2px solid #6366f1 !important;
        }

        html[data-theme="dark"] .job-stats-table thead th.th-step-principal {
            background-color: rgba(168, 85, 247, 0.18) !important;
            color: #d8b4fe !important;
            border-bottom: 2px solid #a855f7 !important;
        }

        html[data-theme="dark"] .job-stats-table thead th.th-step-elearning {
            background-color: rgba(245, 158, 11, 0.18) !important;
            color: #fde68a !important;
            border-bottom: 2px solid #f59e0b !important;
        }

        html[data-theme="dark"] .job-stats-table thead th.th-step-pkwt {
            background-color: rgba(20, 184, 166, 0.18) !important;
            color: #5eead4 !important;
            border-bottom: 2px solid #14b8a6 !important;
        }

        html[data-theme="dark"] .job-stats-table thead th.th-step-joined {
            background-color: rgba(16, 185, 129, 0.25) !important;
            color: #34d399 !important;
            font-weight: 900 !important;
            border-bottom: 2px solid #10b981 !important;
        }

        html[data-theme="dark"] .job-stats-table thead th.th-step-none {
            background-color: rgba(244, 63, 94, 0.18) !important;
            color: #fda4af !important;
            border-bottom: 2px solid #f43f5e !important;
        }

        /* Table Rows & Cells */
        html[data-theme="dark"] .job-stats-table tbody tr {
            border-color: var(--border-subtle) !important;
        }

        html[data-theme="dark"] .job-stats-table tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.05) !important;
        }

        /* RESET SELURUH BACKGROUND CELL TD AGAR TIDAK ADA PITA KABUT VERTIKAL */
        html[data-theme="dark"] .job-stats-table tbody td {
            background-color: transparent !important;
            background: transparent !important;
            border-color: var(--border-subtle) !important;
            color: var(--text-body) !important;
        }

        html[data-theme="dark"] .job-stats-table .stat-text-primary {
            color: #ffffff !important;
        }

        html[data-theme="dark"] .job-stats-table .stat-text-subtle {
            color: #94a3b8 !important;
        }

        html[data-theme="dark"] .job-stats-table .stat-badge-region {
            background-color: rgba(255, 255, 255, 0.08) !important;
            color: #cbd5e1 !important;
            border-color: rgba(255, 255, 255, 0.15) !important;
        }

        /* Job Post Column (Crystal Clear Sky Blue) */
        html[data-theme="dark"] .job-stats-table td.stat-cell-jobpost {
            color: #38bdf8 !important;
            background-color: rgba(56, 189, 248, 0.08) !important;
            font-weight: 800 !important;
        }

        /* Pelamar Column (Crystal Clear Mint Emerald) */
        html[data-theme="dark"] .job-stats-table td.stat-cell-pelamar {
            color: #34d399 !important;
            background-color: rgba(16, 185, 129, 0.08) !important;
            font-weight: 800 !important;
        }

        /* Prinsiple Column */
        html[data-theme="dark"] .job-stats-table td.stat-cell-prinsiple {
            color: #a5b4fc !important;
            font-weight: 700 !important;
        }

        /* Green, Yellow, Red Badges (Tabel 3) */
        html[data-theme="dark"] .job-stats-table .stat-badge-green {
            background-color: rgba(16, 185, 129, 0.22) !important;
            color: #34d399 !important;
            border: 1px solid rgba(16, 185, 129, 0.45) !important;
            box-shadow: 0 0 8px rgba(16, 185, 129, 0.2) !important;
        }

        html[data-theme="dark"] .job-stats-table .stat-badge-yellow {
            background-color: rgba(245, 158, 11, 0.22) !important;
            color: #fbbf24 !important;
            border: 1px solid rgba(245, 158, 11, 0.45) !important;
            box-shadow: 0 0 8px rgba(245, 158, 11, 0.2) !important;
        }

        html[data-theme="dark"] .job-stats-table .stat-badge-red {
            background-color: rgba(244, 63, 94, 0.22) !important;
            color: #fb7185 !important;
            border: 1px solid rgba(244, 63, 94, 0.45) !important;
            box-shadow: 0 0 8px rgba(244, 63, 94, 0.2) !important;
        }

        html[data-theme="dark"] .job-stats-table td.stat-cell-total {
            color: #ffffff !important;
            font-weight: 900 !important;
        }

        /* ============================================================
           STEP ODOO PILL BADGES (LIGHT & DARK MODE)
           ============================================================ */
        .stat-pill-step {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 28px;
            padding: 2px 7px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 800;
            line-height: 1.25;
            transition: all 0.15s ease;
        }

        /* Light Mode Pill Defaults */
        .stat-pill-pelamar { background-color: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd; }
        .stat-pill-interview { background-color: #e0e7ff; color: #4338ca; border: 1px solid #c7d2fe; }
        .stat-pill-principal { background-color: #f3e8ff; color: #7e22ce; border: 1px solid #e9d5ff; }
        .stat-pill-elearning { background-color: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
        .stat-pill-pkwt { background-color: #ccfbf1; color: #0f766e; border: 1px solid #99f6e4; }
        .stat-pill-joined { background-color: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; font-weight: 900; }
        .stat-pill-none { background-color: #ffe4e6; color: #be123c; border: 1px solid #fecdd3; }
        .stat-pill-total { background-color: #0f172a; color: #ffffff; border: 1px solid #334155; }
        .stat-zero-val { color: #cbd5e1; font-weight: 400; font-size: 11px; }

        /* Dark Mode Pill Overrides (Glowing High Contrast) */
        html[data-theme="dark"] .stat-pill-pelamar {
            background-color: rgba(14, 165, 233, 0.22) !important;
            color: #38bdf8 !important;
            border: 1px solid rgba(14, 165, 233, 0.5) !important;
            box-shadow: 0 0 8px rgba(14, 165, 233, 0.25) !important;
        }

        html[data-theme="dark"] .stat-pill-interview {
            background-color: rgba(99, 102, 241, 0.22) !important;
            color: #a5b4fc !important;
            border: 1px solid rgba(99, 102, 241, 0.5) !important;
            box-shadow: 0 0 8px rgba(99, 102, 241, 0.25) !important;
        }

        html[data-theme="dark"] .stat-pill-principal {
            background-color: rgba(168, 85, 247, 0.22) !important;
            color: #d8b4fe !important;
            border: 1px solid rgba(168, 85, 247, 0.5) !important;
            box-shadow: 0 0 8px rgba(168, 85, 247, 0.25) !important;
        }

        html[data-theme="dark"] .stat-pill-elearning {
            background-color: rgba(245, 158, 11, 0.22) !important;
            color: #fde68a !important;
            border: 1px solid rgba(245, 158, 11, 0.5) !important;
            box-shadow: 0 0 8px rgba(245, 158, 11, 0.25) !important;
        }

        html[data-theme="dark"] .stat-pill-pkwt {
            background-color: rgba(20, 184, 166, 0.22) !important;
            color: #5eead4 !important;
            border: 1px solid rgba(20, 184, 166, 0.5) !important;
            box-shadow: 0 0 8px rgba(20, 184, 166, 0.25) !important;
        }

        html[data-theme="dark"] .stat-pill-joined {
            background-color: rgba(16, 185, 129, 0.28) !important;
            color: #34d399 !important;
            border: 1px solid rgba(16, 185, 129, 0.6) !important;
            box-shadow: 0 0 12px rgba(16, 185, 129, 0.35) !important;
            font-weight: 900 !important;
        }

        html[data-theme="dark"] .stat-pill-none {
            background-color: rgba(244, 63, 94, 0.22) !important;
            color: #fda4af !important;
            border: 1px solid rgba(244, 63, 94, 0.5) !important;
            box-shadow: 0 0 8px rgba(244, 63, 94, 0.25) !important;
        }

        html[data-theme="dark"] .stat-pill-total {
            background-color: rgba(255, 255, 255, 0.15) !important;
            color: #ffffff !important;
            border: 1px solid rgba(255, 255, 255, 0.3) !important;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.4) !important;
        }

        html[data-theme="dark"] .stat-zero-val {
            color: #475569 !important;
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
        }

        /* Portal Action Button */
        html[data-theme="dark"] .job-stats-table .stat-btn-portal {
            background-color: rgba(99, 102, 241, 0.2) !important;
            color: #c7d2fe !important;
            border-color: rgba(99, 102, 241, 0.45) !important;
        }
        html[data-theme="dark"] .job-stats-table .stat-btn-portal:hover {
            background-color: rgba(99, 102, 241, 0.35) !important;
            color: #ffffff !important;
        }

        /* Footer & Pagination */
        html[data-theme="dark"] .job-stats-footer {
            background-color: var(--bg-card-alt) !important;
            border-color: var(--border-color) !important;
            color: var(--text-muted) !important;
        }

        html[data-theme="dark"] .job-stats-footer strong,
        html[data-theme="dark"] .job-stats-footer span.text-slate-800 {
            color: #ffffff !important;
        }

        html[data-theme="dark"] .job-stats-footer button {
            background-color: var(--bg-card) !important;
            border-color: var(--border-color) !important;
            color: #cbd5e1 !important;
        }
        html[data-theme="dark"] .job-stats-footer button:hover:not(:disabled) {
            background-color: rgba(255, 255, 255, 0.1) !important;
            color: #ffffff !important;
            border-color: rgba(255, 255, 255, 0.25) !important;
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
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- GROUP 2: MASTER DATA (ADMINISTRATOR ONLY) -->
                @if(Auth::check() && Auth::user()->isAdmin())
                <div>
                    <div class="px-3 text-[11px] font-bold text-slate-400 tracking-wider uppercase mb-1.5 flex items-center justify-between" x-show="!sidebarCollapsed">
                        <span>Master Data</span>
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
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('master.personality.index') }}" 
                               title="Master Soal Kepribadian"
                               class="flex items-center rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('master.personality.*') ? 'sidebar-item-active' : 'text-slate-600 hover:text-primary hover:bg-slate-50' }}"
                                :class="sidebarCollapsed ? 'justify-center px-0 py-2.5' : 'gap-3 px-3.5 py-2.5'">
                                <i class="fa-solid fa-brain text-base w-5 text-center flex-shrink-0 {{ request()->routeIs('master.personality.*') ? 'text-white' : 'text-slate-400' }}"></i>
                                <span x-show="!sidebarCollapsed" class="flex-1 truncate">Soal Kepribadian</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('master.approval-workflow.index') }}" 
                               title="Alur Approver Dinamis"
                               class="flex items-center rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('master.approval-workflow.*') ? 'sidebar-item-active' : 'text-slate-600 hover:text-primary hover:bg-slate-50' }}"
                                :class="sidebarCollapsed ? 'justify-center px-0 py-2.5' : 'gap-3 px-3.5 py-2.5'">
                                <i class="fa-solid fa-diagram-project text-base w-5 text-center flex-shrink-0 {{ request()->routeIs('master.approval-workflow.*') ? 'text-white' : 'text-slate-400' }}"></i>
                                <span x-show="!sidebarCollapsed" class="flex-1 truncate">Alur Approver</span>
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
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('setting.rbac.index') }}" 
                               title="Hak Akses (RBAC)"
                               class="flex items-center rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('setting.rbac.*') || request()->routeIs('rbac.*') ? 'sidebar-item-active' : 'text-slate-600 hover:text-primary hover:bg-slate-50' }}"
                                :class="sidebarCollapsed ? 'justify-center px-0 py-2.5' : 'gap-3 px-3.5 py-2.5'">
                                <i class="fa-solid fa-user-shield text-base w-5 text-center flex-shrink-0 {{ request()->routeIs('setting.rbac.*') || request()->routeIs('rbac.*') ? 'text-white' : 'text-slate-400' }}"></i>
                                <span x-show="!sidebarCollapsed" class="flex-1 truncate">Hak Akses (RBAC)</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('aisetting.index') }}" 
                               title="Setting AI & WA"
                               class="flex items-center rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('aisetting.*') ? 'sidebar-item-active' : 'text-slate-600 hover:text-primary hover:bg-slate-50' }}"
                                :class="sidebarCollapsed ? 'justify-center px-0 py-2.5' : 'gap-3 px-3.5 py-2.5'">
                                <i class="fa-solid fa-sliders text-base w-5 text-center flex-shrink-0 {{ request()->routeIs('aisetting.*') ? 'text-white' : 'text-slate-400' }}"></i>
                                <span x-show="!sidebarCollapsed" class="flex-1 truncate">Setting AI & WA</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.auth-chat.index') }}" 
                               title="Bantuan Login & Reset Kata Sandi"
                               class="flex items-center rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.auth-chat.*') ? 'sidebar-item-active' : 'text-slate-600 hover:text-primary hover:bg-slate-50' }}"
                                :class="sidebarCollapsed ? 'justify-center px-0 py-2.5' : 'gap-3 px-3.5 py-2.5'">
                                <i class="fa-solid fa-headset text-base w-5 text-center flex-shrink-0 {{ request()->routeIs('admin.auth-chat.*') ? 'text-white' : 'text-slate-400' }}"></i>
                                <span x-show="!sidebarCollapsed" class="flex-1 truncate">Bantuan Login</span>
                                @php
                                    $sidebarPendingReset = \App\Models\PasswordResetRequest::where('status', 'pending')->count();
                                @endphp
                                @if($sidebarPendingReset > 0)
                                <span x-show="!sidebarCollapsed" class="text-[10px] bg-rose-500 text-white font-bold px-1.5 py-0.2 rounded-full animate-bounce">{{ $sidebarPendingReset }}</span>
                                @endif
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('activity-logs.index') }}" 
                               title="Log Aktivitas & Audit"
                               class="flex items-center rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('activity-logs.*') ? 'sidebar-item-active' : 'text-slate-600 hover:text-primary hover:bg-slate-50' }}"
                                :class="sidebarCollapsed ? 'justify-center px-0 py-2.5' : 'gap-3 px-3.5 py-2.5'">
                                <i class="fa-solid fa-clock-rotate-left text-base w-5 text-center flex-shrink-0 {{ request()->routeIs('activity-logs.*') ? 'text-white' : 'text-slate-400' }}"></i>
                                <span x-show="!sidebarCollapsed" class="flex-1 truncate">Log Aktivitas</span>
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
                            </a>
                        </li>

                        <!-- GROUPS CHAT -->
                        <li>
                            <a href="{{ route('workplan.chat') }}" 
                               title="Groups Chat"
                               class="flex items-center rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('workplan.chat*') ? 'sidebar-item-active' : 'text-slate-700 hover:text-emerald-600 hover:bg-slate-50' }}"
                                :class="sidebarCollapsed ? 'justify-center px-0 py-2.5' : 'gap-3 px-3.5 py-2.5'">
                                <i class="fa-solid fa-comments text-base w-5 text-center flex-shrink-0 {{ request()->routeIs('workplan.chat*') ? 'text-white' : 'text-emerald-500' }}"></i>
                                <span x-show="!sidebarCollapsed" class="flex-1 truncate">Groups Chat</span>
                            </a>
                        </li>

                        <!-- HELPDESK & TICKETING (COLLAPSIBLE ACCORDION) -->
                        @php
                            $isHelpdeskActive = request()->is('helpdesk*');
                        @endphp
                        <li x-data="{ helpdeskOpen: {{ $isHelpdeskActive ? 'true' : 'false' }} }">
                            <button @click="if(sidebarCollapsed) { toggleSidebar(); helpdeskOpen = true; } else { helpdeskOpen = !helpdeskOpen }" 
                                    type="button"
                                    title="Helpdesk Support"
                                    class="w-full flex items-center rounded-xl text-sm font-semibold transition-all {{ $isHelpdeskActive ? 'bg-amber-50 text-amber-700' : 'text-slate-700 hover:bg-slate-50 hover:text-amber-600' }}"
                                    :class="sidebarCollapsed ? 'justify-center px-0 py-2.5' : 'gap-3 px-3.5 py-2.5'">
                                <i class="fa-solid fa-headset text-base w-5 text-center flex-shrink-0 {{ $isHelpdeskActive ? 'text-amber-600' : 'text-amber-500' }}"></i>
                                <span x-show="!sidebarCollapsed" class="flex-1 text-left truncate">Helpdesk Support</span>
                                <i x-show="!sidebarCollapsed" class="fa-solid fa-chevron-down text-xs text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': helpdeskOpen }"></i>
                            </button>

                            <!-- SUB-MENU ITEMS LIST -->
                            <div x-show="helpdeskOpen && !sidebarCollapsed" x-collapse class="pl-4 pr-1 py-1.5 space-y-1 border-l-2 border-amber-200 ml-4 mt-1">
                                <!-- Dashboard Tiket (Tampil untuk Semua: Admin, Divisi, User Biasa) -->
                                <a href="{{ route('helpdesk.index') }}" 
                                   title="Dashboard Tiket"
                                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-semibold transition-all {{ request()->routeIs('helpdesk.index') ? 'bg-amber-500 text-white shadow-sm' : 'text-slate-600 hover:text-amber-700 hover:bg-amber-50/60' }}">
                                    <i class="fa-solid fa-chart-pie text-[11px] w-4 text-center"></i>
                                    <span>Dashboard Tiket</span>
                                </a>

                                <!-- Buat Tiket Baru (Tampil untuk Semua) -->
                                <a href="{{ route('helpdesk.tickets.create') }}" 
                                   title="Buat Tiket Baru"
                                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-semibold transition-all {{ request()->routeIs('helpdesk.tickets.create') ? 'bg-amber-500 text-white shadow-sm' : 'text-slate-600 hover:text-amber-700 hover:bg-amber-50/60' }}">
                                    <i class="fa-solid fa-circle-plus text-[11px] w-4 text-center"></i>
                                    <span>Buat Tiket Baru</span>
                                </a>

                                <!-- MENU LENGKAP HANYA TAMPIL UNTUK ADMINISTRATOR -->
                                @if(auth()->check() && auth()->user()->isHelpdeskAdmin())
                                <!-- Antrean Tiket -->
                                <a href="{{ route('helpdesk.tickets.index') }}" 
                                   title="Antrean Tiket"
                                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-semibold transition-all {{ request()->routeIs('helpdesk.tickets.index', 'helpdesk.tickets.show') ? 'bg-amber-500 text-white shadow-sm' : 'text-slate-600 hover:text-amber-700 hover:bg-amber-50/60' }}">
                                    <i class="fa-solid fa-inbox text-[11px] w-4 text-center"></i>
                                    <span>Antrean Tiket</span>
                                </a>

                                <!-- Kanban Helpdesk -->
                                <a href="{{ route('helpdesk.kanban') }}" 
                                   title="Kanban Helpdesk"
                                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-semibold transition-all {{ request()->routeIs('helpdesk.kanban') ? 'bg-amber-500 text-white shadow-sm' : 'text-slate-600 hover:text-amber-700 hover:bg-amber-50/60' }}">
                                    <i class="fa-solid fa-table-columns text-[11px] w-4 text-center"></i>
                                    <span>Kanban Helpdesk</span>
                                </a>

                                <!-- Master Divisi & Agen -->
                                <a href="{{ route('helpdesk.divisions.index') }}" 
                                   title="Master Divisi & Agen"
                                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-semibold transition-all {{ request()->routeIs('helpdesk.divisions.*') ? 'bg-amber-500 text-white shadow-sm' : 'text-slate-600 hover:text-amber-700 hover:bg-amber-50/60' }}">
                                    <i class="fa-solid fa-sitemap text-[11px] w-4 text-center"></i>
                                    <span>Master Divisi & Agen</span>
                                </a>

                                <!-- Template Balasan Cepat -->
                                <a href="{{ route('helpdesk.canned.index') }}" 
                                   title="Balasan Cepat"
                                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-semibold transition-all {{ request()->routeIs('helpdesk.canned.*') ? 'bg-amber-500 text-white shadow-sm' : 'text-slate-600 hover:text-amber-700 hover:bg-amber-50/60' }}">
                                    <i class="fa-solid fa-bolt text-[11px] w-4 text-center"></i>
                                    <span>Balasan Cepat</span>
                                </a>

                                <!-- Master Template Laporan -->
                                <a href="{{ route('helpdesk.templates.index') }}" 
                                   title="Master Template Laporan"
                                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-semibold transition-all {{ request()->routeIs('helpdesk.templates.*') ? 'bg-amber-500 text-white shadow-sm' : 'text-slate-600 hover:text-amber-700 hover:bg-amber-50/60' }}">
                                    <i class="fa-solid fa-file-lines text-[11px] w-4 text-center"></i>
                                    <span>Template Laporan</span>
                                </a>
                                @endif
                            </div>
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
                                </a>

                                <!-- Statistik Job & Kandidat -->
                                <a href="{{ route('job.statistik') }}" 
                                   title="Statistik Job & Kandidat"
                                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-semibold transition-all {{ request()->routeIs('job.statistik*') ? 'bg-primary text-white shadow-sm' : 'text-slate-600 hover:text-primary hover:bg-slate-100/70' }}">
                                    <i class="fa-solid fa-chart-pie text-[11px] w-4 text-center"></i>
                                    <span>Statistik Job & Kandidat</span>
                                    @if(request()->routeIs('job.statistik*'))
                                        <span class="w-1.5 h-1.5 rounded-full bg-white ml-auto"></span>
                                    @endif
                                </a>

                                <!-- 3. Kandidat Portal -->
                                <a href="{{ route('kandidatportal.index') }}" 
                                   title="Kandidat Portal"
                                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-semibold transition-all {{ request()->routeIs('kandidatportal.index', 'kandidatportal.show') ? 'bg-primary text-white shadow-sm' : 'text-slate-600 hover:text-primary hover:bg-slate-100/70' }}">
                                    <i class="fa-solid fa-globe text-[11px] w-4 text-center"></i>
                                    <span>Kandidat Portal</span>
                                    @if(request()->routeIs('kandidatportal.index', 'kandidatportal.show'))
                                        <span class="w-1.5 h-1.5 rounded-full bg-white ml-auto"></span>
                                    @endif
                                </a>

                                <!-- 3b. Log Antrean AI -->
                                <a href="{{ route('kandidatportal.ai_queue') }}" 
                                   title="Log Antrean & Hasil Analisa AI"
                                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-semibold transition-all {{ request()->routeIs('kandidatportal.ai_queue*') ? 'bg-primary text-white shadow-sm' : 'text-slate-600 hover:text-primary hover:bg-slate-100/70' }}">
                                    <i class="fa-solid fa-list-check text-[11px] w-4 text-center {{ request()->routeIs('kandidatportal.ai_queue*') ? 'text-white' : 'text-indigo-500' }}"></i>
                                    <span>Log Antrean AI</span>
                                    @if(request()->routeIs('kandidatportal.ai_queue*'))
                                        <span class="w-1.5 h-1.5 rounded-full bg-white ml-auto"></span>
                                    @endif
                                </a>

                                <!-- 4. AI Ranking -->
                                <a href="{{ route('airanking.index') }}" 
                                   title="AI Ranking"
                                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-semibold transition-all {{ request()->routeIs('airanking.*') ? 'bg-primary text-white shadow-sm' : 'text-slate-600 hover:text-primary hover:bg-slate-100/70' }}">
                                    <i class="fa-solid fa-ranking-star text-[11px] w-4 text-center {{ request()->routeIs('airanking.*') ? 'text-white' : 'text-amber-500' }}"></i>
                                    <span>AI Ranking</span>
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
                                    @if(request()->routeIs('userprinsiple.*'))
                                        <span class="w-1.5 h-1.5 rounded-full bg-white ml-auto"></span>
                                    @endif
                                </a>

                                <!-- 6. Kandidat Interview -->
                                <a href="{{ route('interview.index') }}" 
                                   title="Kandidat Interview"
                                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-semibold transition-all {{ (request()->routeIs('interview.*') && !request()->routeIs('interview.walk*')) ? 'bg-primary text-white shadow-sm' : 'text-slate-600 hover:text-primary hover:bg-slate-100/70' }}">
                                    <i class="fa-solid fa-clipboard-user text-[11px] w-4 text-center"></i>
                                    <span>Kandidat Interview</span>
                                    @if(request()->routeIs('interview.*') && !request()->routeIs('interview.walk*'))
                                        <span class="w-1.5 h-1.5 rounded-full bg-white ml-auto"></span>
                                    @endif
                                </a>

                                <!-- 6b. Kandidat Walkin -->
                                <a href="{{ route('interview.walk') }}" 
                                   title="Kandidat Walkin Interview"
                                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-semibold transition-all {{ request()->routeIs('interview.walk*') ? 'bg-primary text-white shadow-sm' : 'text-slate-600 hover:text-primary hover:bg-slate-100/70' }}">
                                    <i class="fa-solid fa-person-walking text-[11px] w-4 text-center"></i>
                                    <span>Kandidat Walkin</span>
                                    @if(request()->routeIs('interview.walk*'))
                                        <span class="w-1.5 h-1.5 rounded-full bg-white ml-auto"></span>
                                    @endif
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
                    $userJabatan = $currentUser ? $currentUser->jabatan_display : 'Administrator';
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
                    <a href="{{ route('profile.index') }}" class="flex items-center gap-2.5 flex-1 min-w-0" title="Klik untuk Buka Profil & Edit Data ({{ $userJabatan }})">
                        <img src="{{ $userAvatarUrl }}" alt="{{ $userName }}" class="w-9 h-9 rounded-lg border border-slate-100 flex-shrink-0 object-cover">
                        <div class="flex-1 min-w-0">
                            <div class="text-xs font-bold text-slate-900 group-hover:text-primary transition-colors truncate">{{ $userName }}</div>
                            <div class="text-[10px] text-slate-500 font-medium truncate flex items-center gap-1" title="{{ $userJabatan }}">
                                <span class="inline-block w-1.5 h-1.5 rounded-full {{ $userRole === 'admin' ? 'bg-blue-500' : ($userRole === 'karyawan_inhouse' ? 'bg-emerald-500' : 'bg-amber-500') }}"></span>
                                <span class="truncate">{{ $userJabatan }}</span>
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
                    <a href="{{ route('profile.index') }}" title="Profil {{ $userName }} ({{ $userJabatan }})">
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

                    <!-- Quick Dark / Light Mode Toggle Button -->
                    <button type="button" 
                            id="quickThemeToggleBtn"
                            onclick="toggleQuickTheme()"
                            class="p-2 text-slate-500 hover:text-primary rounded-xl hover:bg-slate-100 transition-all focus:outline-none"
                            title="Ganti Mode Tampilan (Terang / Gelap)">
                        <i id="themeToggleIcon" class="fa-regular fa-moon text-lg transition-transform duration-300"></i>
                    </button>

                    <!-- Notification Dropdown Component (Alpine.js) -->
                    <div class="relative" x-data="asystemNotifications()" x-init="initNotifications()" @click.outside="isOpen = false">
                        <!-- Notification Bell Button -->
                        <button type="button" 
                                @click="toggleDropdown()"
                                class="relative p-2 text-slate-500 hover:text-slate-800 rounded-xl hover:bg-slate-100 transition-all focus:outline-none"
                                :class="{'text-primary bg-primary-50': isOpen}"
                                title="Notifikasi & Chat Masuk">
                            <i class="fa-regular fa-bell text-lg" :class="{'animate-bounce': unreadTotal > 0 && isRinging}"></i>
                            
                            <!-- Red Badge Counter -->
                            <span x-show="unreadTotal > 0" 
                                  x-transition
                                  class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-rose-600 text-white font-bold text-[10px] flex items-center justify-center shadow-xs border-2 border-white ring-1 ring-rose-500/20"
                                  x-text="unreadTotal > 9 ? '9+' : unreadTotal">
                            </span>
                        </button>

                        <!-- Dropdown Panel -->
                        <div x-show="isOpen" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                             class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-2xl shadow-2xl border border-slate-200 z-50 overflow-hidden divide-y divide-slate-100 text-slate-800"
                             style="display: none;">
                            
                            <!-- Header -->
                            <div class="px-4 py-3 bg-slate-50/90 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-primary-50 text-primary flex items-center justify-center font-bold text-xs">
                                        <i class="fa-regular fa-bell"></i>
                                    </div>
                                    <span class="text-xs font-bold text-slate-800">Pemberitahuan & Chat</span>
                                </div>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full"
                                      :class="unreadTotal > 0 ? 'bg-rose-100 text-rose-700' : 'bg-slate-200 text-slate-600'"
                                      x-text="unreadTotal > 0 ? (unreadTotal + ' pemberitahuan') : 'Semua terbaca'"></span>
                            </div>

                            <!-- Desktop Windows Notification Permission Banner -->
                            <div class="px-4 py-2 border-b border-slate-100 flex items-center justify-between gap-2 text-[11px] bg-slate-50/80">
                                <div class="flex items-center gap-1.5 min-w-0">
                                    <i class="fa-brands fa-windows text-blue-600 text-xs"></i>
                                    <span class="font-medium text-slate-600 truncate">Notifikasi Windows Desktop:</span>
                                </div>
                                <template x-if="desktopPermission === 'granted'">
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                                        <i class="fa-solid fa-circle-check text-[9px]"></i> Aktif
                                    </span>
                                </template>
                                <template x-if="desktopPermission === 'default'">
                                    <button type="button" @click="requestDesktopPermission()" class="text-[10px] font-bold text-blue-700 bg-blue-100 hover:bg-blue-200 px-2.5 py-0.5 rounded-full transition-all flex items-center gap-1 shadow-2xs">
                                        <i class="fa-solid fa-bell text-[9px]"></i> Aktifkan
                                    </button>
                                </template>
                                <template x-if="desktopPermission === 'denied'">
                                    <span class="inline-flex items-center gap-1 text-[10px] font-medium text-slate-400" title="Diblokir di pengaturan peramban">
                                        <i class="fa-solid fa-ban text-[9px]"></i> Dinonaktifkan
                                    </span>
                                </template>
                            </div>

                            <!-- Tab Filter -->
                            <div class="flex items-center border-b border-slate-100 bg-slate-50/60 px-3 pt-1 text-[11px] font-bold gap-1.5 overflow-x-auto">
                                <button type="button" @click="activeNotifTab = 'all'" 
                                        class="pb-1.5 px-1.5 border-b-2 transition-all whitespace-nowrap" 
                                        :class="activeNotifTab === 'all' ? 'border-primary text-primary' : 'border-transparent text-slate-400 hover:text-slate-600'">
                                    Semua (<span x-text="unreadTotal"></span>)
                                </button>
                                <template x-if="unreadTicketsCount > 0 || unreadTickets.length > 0">
                                    <button type="button" @click="activeNotifTab = 'tickets'" 
                                            class="pb-1.5 px-1.5 border-b-2 transition-all flex items-center gap-1 whitespace-nowrap" 
                                            :class="activeNotifTab === 'tickets' ? 'border-amber-600 text-amber-700' : 'border-transparent text-slate-400 hover:text-slate-600'">
                                        <span>Tiket</span>
                                        <span class="px-1 py-0.2 rounded-full bg-amber-100 text-amber-800 text-[9px]" x-text="unreadTicketsCount"></span>
                                    </button>
                                </template>
                                <template x-if="unreadTasksCount > 0 || unreadTasks.length > 0">
                                    <button type="button" @click="activeNotifTab = 'tasks'" 
                                            class="pb-1.5 px-1.5 border-b-2 transition-all flex items-center gap-1 whitespace-nowrap" 
                                            :class="activeNotifTab === 'tasks' ? 'border-indigo-600 text-indigo-700' : 'border-transparent text-slate-400 hover:text-slate-600'">
                                        <span>Work Plan</span>
                                        <span class="px-1 py-0.2 rounded-full bg-indigo-100 text-indigo-800 text-[9px]" x-text="unreadTasksCount"></span>
                                    </button>
                                </template>
                                <template x-if="unreadGroups.length > 0">
                                    <button type="button" @click="activeNotifTab = 'chats'" 
                                            class="pb-1.5 px-1.5 border-b-2 transition-all flex items-center gap-1 whitespace-nowrap" 
                                            :class="activeNotifTab === 'chats' ? 'border-emerald-600 text-emerald-700' : 'border-transparent text-slate-400 hover:text-slate-600'">
                                        <span>Chat</span>
                                        <span class="px-1 py-0.2 rounded-full bg-emerald-100 text-emerald-700 text-[9px]" x-text="unreadGroups.length"></span>
                                    </button>
                                </template>
                                <template x-if="pendingResets.length > 0">
                                    <button type="button" @click="activeNotifTab = 'resets'" 
                                            class="pb-1.5 px-1.5 border-b-2 transition-all flex items-center gap-1 whitespace-nowrap" 
                                            :class="activeNotifTab === 'resets' ? 'border-rose-600 text-rose-700' : 'border-transparent text-slate-400 hover:text-slate-600'">
                                        <span>Reset</span>
                                        <span class="px-1 py-0.2 rounded-full bg-rose-100 text-rose-700 text-[9px]" x-text="pendingResets.length"></span>
                                    </button>
                                </template>
                            </div>

                            <!-- Notification Items List Stream -->
                            <div class="max-h-80 overflow-y-auto divide-y divide-slate-100">

                                <!-- SECTION 1: PERMINTAAN RESET PASSWORD / BANTUAN LOGIN (ADMIN) -->
                                <template x-if="(activeNotifTab === 'all' || activeNotifTab === 'resets') && pendingResets.length > 0">
                                    <div class="divide-y divide-amber-100/60">
                                        <div class="px-3 py-1.5 bg-amber-50/70 border-b border-amber-100/80 text-[10px] font-extrabold text-amber-800 uppercase tracking-wider flex items-center justify-between">
                                            <span class="flex items-center gap-1.5">
                                                <i class="fa-solid fa-key text-[9px] text-amber-600"></i>
                                                Bantuan Login / Reset Password
                                            </span>
                                            <span class="px-1.5 py-0.2 rounded-full bg-rose-100 text-rose-700 font-black text-[9px]" x-text="pendingResets.length + ' PENDING'"></span>
                                        </div>

                                        <template x-for="req in pendingResets" :key="'reset_' + req.id">
                                            <a :href="'{{ route('admin.auth-chat.index') }}?id=' + req.id"
                                               class="flex items-start gap-3 p-3 hover:bg-amber-50/60 transition-colors group relative">
                                                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-amber-500 to-rose-600 text-white flex items-center justify-center text-sm font-bold shadow-xs flex-shrink-0 mt-0.5">
                                                    <i class="fa-solid fa-key"></i>
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <div class="flex items-center justify-between gap-1 mb-0.5">
                                                        <span class="text-xs font-bold text-slate-800 group-hover:text-amber-800 truncate" x-text="req.nama_karyawan"></span>
                                                        <span class="text-[10px] text-slate-400 flex-shrink-0" x-text="req.time || req.created_at_human"></span>
                                                    </div>
                                                    <div class="text-[10px] text-slate-500 font-mono flex items-center gap-1.5">
                                                        <span>NIK: <b class="text-slate-700" x-text="req.nik"></b></span>
                                                        <span class="text-slate-300">•</span>
                                                        <span class="truncate" x-text="req.entitas || 'ESA Groups'"></span>
                                                    </div>
                                                    <p class="text-[11px] text-slate-600 truncate leading-tight mt-1">
                                                        <span class="text-amber-700 font-semibold">Pesan: </span>
                                                        <span x-text="req.request_message || 'Permintaan reset password login.'"></span>
                                                    </p>
                                                </div>
                                                <span class="px-1.5 py-0.5 rounded-full bg-rose-600 text-white font-extrabold text-[9px] flex-shrink-0 shadow-2xs">
                                                    Proses
                                                </span>
                                            </a>
                                        </template>
                                    </div>
                                </template>

                                <!-- SECTION 2: TIKET HELPDESK -->
                                <template x-if="(activeNotifTab === 'all' || activeNotifTab === 'tickets') && unreadTickets.length > 0">
                                    <div class="divide-y divide-amber-100/60">
                                        <div class="px-3 py-1.5 bg-amber-50/60 border-b border-amber-100/80 text-[10px] font-extrabold text-amber-800 uppercase tracking-wider flex items-center justify-between">
                                            <span class="flex items-center gap-1.5">
                                                <i class="fa-solid fa-headset text-[9px] text-amber-600"></i>
                                                Tiket Helpdesk Terbuka
                                            </span>
                                            <span class="px-1.5 py-0.2 rounded-full bg-amber-100 text-amber-800 font-bold text-[9px]" x-text="unreadTicketsCount + ' tiket'"></span>
                                        </div>

                                        <template x-for="tkt in unreadTickets" :key="'tkt_' + tkt.id">
                                            <a :href="tkt.url"
                                               class="flex items-start gap-3 p-3 hover:bg-amber-50/50 transition-colors group">
                                                <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-sm flex-shrink-0 shadow-xs mt-0.5">
                                                    <i class="fa-solid fa-ticket"></i>
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <div class="flex items-center justify-between gap-1 mb-0.5">
                                                        <span class="text-xs font-bold text-slate-800 group-hover:text-amber-700 truncate" x-text="'#' + tkt.ticket_number"></span>
                                                        <span class="text-[10px] text-slate-400 flex-shrink-0" x-text="tkt.time"></span>
                                                    </div>
                                                    <p class="text-[11px] text-slate-700 font-semibold truncate leading-tight" x-text="tkt.subject"></p>
                                                    <div class="text-[10px] text-slate-400 flex items-center gap-1 mt-0.5">
                                                        <span x-text="tkt.creator_name"></span>
                                                        <span>&bull;</span>
                                                        <span class="font-medium text-amber-700" x-text="tkt.division_name"></span>
                                                    </div>
                                                </div>
                                                <span class="px-1.5 py-0.5 rounded-full text-[9px] font-bold uppercase"
                                                      :class="tkt.priority === 'Urgent' || tkt.priority === 'High' ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-600'"
                                                      x-text="tkt.priority"></span>
                                            </a>
                                        </template>
                                    </div>
                                </template>

                                <!-- SECTION 3: TUGAS WORK PLAN -->
                                <template x-if="(activeNotifTab === 'all' || activeNotifTab === 'tasks') && unreadTasks.length > 0">
                                    <div class="divide-y divide-indigo-100/60">
                                        <div class="px-3 py-1.5 bg-indigo-50/60 border-b border-indigo-100/80 text-[10px] font-extrabold text-indigo-800 uppercase tracking-wider flex items-center justify-between">
                                            <span class="flex items-center gap-1.5">
                                                <i class="fa-solid fa-list-check text-[9px] text-indigo-600"></i>
                                                Tugas Work Plan Saya
                                            </span>
                                            <span class="px-1.5 py-0.2 rounded-full bg-indigo-100 text-indigo-800 font-bold text-[9px]" x-text="unreadTasksCount + ' aktif'"></span>
                                        </div>

                                        <template x-for="tsk in unreadTasks" :key="'tsk_' + tsk.id">
                                            <a :href="tsk.url"
                                               class="flex items-start gap-3 p-3 hover:bg-indigo-50/50 transition-colors group">
                                                <div class="w-9 h-9 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-sm flex-shrink-0 shadow-xs mt-0.5">
                                                    <i class="fa-solid fa-thumbtack"></i>
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <div class="flex items-center justify-between gap-1 mb-0.5">
                                                        <span class="text-xs font-bold text-slate-800 group-hover:text-indigo-700 truncate" x-text="tsk.title"></span>
                                                        <span class="text-[10px] font-mono text-slate-400 uppercase" x-text="tsk.status"></span>
                                                    </div>
                                                    <div class="text-[10px] text-slate-500 flex items-center gap-1">
                                                        <span>Dari: <strong class="text-slate-700" x-text="tsk.delegator"></strong></span>
                                                        <template x-if="tsk.due_date">
                                                            <span>&bull; Deadline: <span x-text="tsk.due_date"></span></span>
                                                        </template>
                                                    </div>
                                                </div>
                                                <span class="px-1.5 py-0.5 rounded-full text-[9px] font-bold"
                                                      :class="tsk.priority === 'Urgent' || tsk.priority === 'High' ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-600'"
                                                      x-text="tsk.priority"></span>
                                            </a>
                                        </template>
                                    </div>
                                </template>

                                <!-- SECTION 4: CHAT NOTIFICATION ITEMS LIST (GROUPS CHAT) -->
                                <template x-if="(activeNotifTab === 'all' || activeNotifTab === 'chats') && unreadGroups.length > 0">
                                    <div class="divide-y divide-slate-100">
                                        <div class="px-3 py-1.5 bg-emerald-50/60 border-b border-emerald-100/80 text-[10px] font-extrabold text-emerald-800 uppercase tracking-wider flex items-center justify-between">
                                            <span class="flex items-center gap-1.5">
                                                <i class="fa-solid fa-comments text-[9px] text-emerald-600"></i>
                                                Groups Chat
                                            </span>
                                            <span class="px-1.5 py-0.2 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[9px]" x-text="unreadGroups.length + ' group'"></span>
                                        </div>

                                        <template x-for="grp in unreadGroups" :key="'grp_' + grp.group_id">
                                            <a :href="'{{ url('/workplan-chat') }}?group_id=' + grp.group_id"
                                               class="flex items-start gap-3 p-3 hover:bg-emerald-50/50 transition-colors group">
                                                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white font-bold text-xs flex-shrink-0 shadow-xs mt-0.5"
                                                     :style="'background-color: ' + (grp.avatar_color || '#10b981')">
                                                    <span x-text="grp.initials"></span>
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <div class="flex items-center justify-between gap-1 mb-0.5">
                                                        <span class="text-xs font-bold text-slate-800 group-hover:text-emerald-700 truncate" x-text="grp.group_name"></span>
                                                        <span class="text-[10px] text-slate-400 flex-shrink-0" x-text="grp.last_message ? grp.last_message.time : ''"></span>
                                                    </div>
                                                    <p class="text-[11px] text-slate-600 truncate leading-tight">
                                                        <span class="font-semibold text-slate-800" x-text="grp.last_message ? (grp.last_message.sender + ': ') : ''"></span>
                                                        <span x-text="grp.last_message ? grp.last_message.text : 'Ada pesan baru'"></span>
                                                    </p>
                                                </div>
                                                <span class="px-1.5 py-0.5 rounded-full bg-emerald-600 text-white font-bold text-[10px] flex-shrink-0 shadow-2xs" x-text="grp.unread_count"></span>
                                            </a>
                                        </template>
                                    </div>
                                </template>

                                <!-- State Kosong jika tidak ada notifikasi apapun -->
                                <div x-show="unreadTotal === 0" class="p-6 text-center text-slate-400">
                                    <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2">
                                        <i class="fa-regular fa-bell-slash text-base"></i>
                                    </div>
                                    <div class="text-xs font-bold text-slate-700">Tidak ada notifikasi baru</div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">Semua pesan group, tiket helpdesk, dan tugas workplan sudah diperbarui.</div>
                                </div>
                            </div>

                            <!-- Footer -->
                            <div class="p-2.5 bg-slate-50 flex items-center justify-between border-t border-slate-100 flex-wrap gap-2">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <a href="{{ route('helpdesk.index') }}" class="text-[11px] font-bold text-amber-700 hover:text-amber-800 flex items-center gap-1 px-2 py-1 rounded-lg hover:bg-amber-50 transition-colors">
                                        <i class="fa-solid fa-headset text-xs"></i>
                                        <span>Helpdesk</span>
                                    </a>
                                    <a href="{{ route('workplan.index') }}" class="text-[11px] font-bold text-indigo-700 hover:text-indigo-800 flex items-center gap-1 px-2 py-1 rounded-lg hover:bg-indigo-50 transition-colors">
                                        <i class="fa-solid fa-list-check text-xs"></i>
                                        <span>Work Plan</span>
                                    </a>
                                    <a href="{{ route('workplan.chat') }}" class="text-[11px] font-bold text-emerald-700 hover:text-emerald-800 flex items-center gap-1 px-2 py-1 rounded-lg hover:bg-emerald-50 transition-colors">
                                        <i class="fa-solid fa-comments text-xs"></i>
                                        <span>Chat</span>
                                    </a>
                                </div>
                                <button type="button" @click="isOpen = false" class="text-[11px] font-semibold text-slate-400 hover:text-slate-600 px-2 py-1">
                                    Tutup
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Topbar Profile -->
                    <div class="flex items-center gap-2 pl-2">
                        <a href="{{ route('profile.index') }}" class="flex items-center gap-2 hover:opacity-90 group transition-all" title="Klik untuk Buka Profil Akun ({{ $userJabatan }})">
                            <img src="{{ $userAvatarUrl }}" alt="{{ $userName }}" class="w-8 h-8 rounded-lg border border-slate-200 object-cover group-hover:ring-2 group-hover:ring-primary/40 transition-all">
                            <div class="hidden md:block text-left">
                                <div class="text-xs font-bold text-slate-800 group-hover:text-primary transition-colors leading-none">{{ $userName }}</div>
                                <div class="text-[10px] text-slate-500 leading-none mt-1 font-medium truncate max-w-[140px]" title="{{ $userJabatan }}">{{ $userJabatan }}</div>
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
            const str = String(message ?? '');
            const lower = str.toLowerCase();
            if (lower.includes('ditolak') || lower.includes('hanya delegator') || lower.includes('tidak berhak') || lower.includes('belum memiliki tanda tangan')) {
                icon = 'warning';
                title = 'Perhatian';
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
                confirmButtonText: 'OK, Mengerti',
                customClass: {
                    popup: 'rounded-2xl shadow-2xl border border-slate-200/80',
                    confirmButton: 'rounded-xl font-bold px-6 py-2.5 text-xs shadow-md'
                }
            });
        };

        // Global Confirmation Interceptor for Forms with onsubmit="return confirm(...)"
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (!form || form.tagName !== 'FORM') return;
            if (form.dataset.swalConfirmed === 'true') {
                delete form.dataset.swalConfirmed;
                return;
            }
            const onsubmitAttr = form.getAttribute('onsubmit') || '';
            const confirmMatch = onsubmitAttr.match(/confirm\s*\(\s*(['"`])([\s\S]*?)\1\s*\)/i);
            if (confirmMatch) {
                e.preventDefault();
                e.stopImmediatePropagation();
                const msg = confirmMatch[2].replace(/\\'/g, "'").replace(/\\"/g, '"');
                Swal.fire({
                    title: 'Konfirmasi',
                    html: msg.replace(/\n/g, '<br>'),
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#0F52BA',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: '<i class="fa-solid fa-check mr-1.5"></i> Ya, Lanjutkan',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: {
                        popup: 'rounded-2xl shadow-2xl border border-slate-200/80',
                        confirmButton: 'rounded-xl font-bold px-5 py-2.5 text-xs shadow-md',
                        cancelButton: 'rounded-xl font-semibold px-4 py-2.5 text-xs'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.dataset.swalConfirmed = 'true';
                        form.submit();
                    }
                });
            }
        }, true);

        // Global Confirmation Interceptor for Links / Buttons with onclick="return confirm(...)"
        document.addEventListener('click', function(e) {
            const el = e.target.closest('a[onclick*="confirm"], button[onclick*="confirm"]');
            if (!el) return;
            if (el.dataset.swalConfirmed === 'true') {
                delete el.dataset.swalConfirmed;
                return;
            }
            const onclickAttr = el.getAttribute('onclick') || '';
            const confirmMatch = onclickAttr.match(/confirm\s*\(\s*(['"`])([\s\S]*?)\1\s*\)/i);
            if (confirmMatch) {
                e.preventDefault();
                e.stopImmediatePropagation();
                const msg = confirmMatch[2].replace(/\\'/g, "'").replace(/\\"/g, '"');
                Swal.fire({
                    title: 'Konfirmasi',
                    html: msg.replace(/\n/g, '<br>'),
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#0F52BA',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: '<i class="fa-solid fa-check mr-1.5"></i> Ya, Lanjutkan',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: {
                        popup: 'rounded-2xl shadow-2xl border border-slate-200/80',
                        confirmButton: 'rounded-xl font-bold px-5 py-2.5 text-xs shadow-md',
                        cancelButton: 'rounded-xl font-semibold px-4 py-2.5 text-xs'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        el.dataset.swalConfirmed = 'true';
                        if (el.tagName === 'A' && el.href && !el.href.startsWith('javascript:')) {
                            window.location.href = el.href;
                        } else {
                            el.click();
                        }
                    }
                });
            }
        }, true);

        // Native Windows Desktop Notification Helper Function
        function triggerWindowsNotification(title, body, url = null, icon = null) {
            if (!('Notification' in window)) return;
            if (Notification.permission !== 'granted') return;

            try {
                const defaultIcon = '{{ asset("favicon.ico") }}';
                const notif = new Notification(title, {
                    body: body,
                    icon: icon || defaultIcon,
                    badge: defaultIcon,
                    tag: 'asystem-' + Date.now(),
                    renotify: true
                });

                notif.onclick = function() {
                    window.focus();
                    if (url) {
                        window.location.href = url;
                    }
                    this.close();
                };
            } catch (e) {
                console.warn('Windows notification error:', e);
            }
        }
    </script>
    
    <!-- Global Chat & System Notification System -->
    <script>
    function asystemNotifications() {
        return {
            isOpen: false,
            activeNotifTab: 'all',
            unreadTotal: 0,
            unreadGroups: [],
            unreadTickets: [],
            unreadTicketsCount: 0,
            unreadTasks: [],
            unreadTasksCount: 0,
            pendingResets: [],
            pendingResetsCount: 0,
            notifiedResetIds: new Set(),
            lastChatId: 0,
            lastTicketId: 0,
            lastReplyId: 0,
            lastTaskId: 0,
            desktopPermission: ('Notification' in window) ? Notification.permission : 'denied',
            pollTimer: null,
            isRinging: false,

            initNotifications() {
                // Update permission status
                this.desktopPermission = ('Notification' in window) ? Notification.permission : 'denied';

                // Cek notifikasi pertama kali
                this.fetchNotifications(true);

                // Polling periodik ringan setiap 8-10 detik
                if (!this.pollTimer) {
                    this.pollTimer = setInterval(() => {
                        this.fetchNotifications(false);
                    }, 8500);
                }

                // Listener jika ada event dari halaman chat atau helpdesk
                window.addEventListener('asystem-chat-notif', (e) => {
                    this.fetchNotifications(false);
                });
                window.addEventListener('asystem-ticket-notif', (e) => {
                    this.fetchNotifications(false);
                });
            },

            async requestDesktopPermission() {
                if (!('Notification' in window)) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Tidak Didukung',
                        text: 'Browser Anda tidak mendukung Web Notification API.'
                    });
                    return;
                }

                try {
                    const perm = await Notification.requestPermission();
                    this.desktopPermission = perm;
                    if (perm === 'granted') {
                        triggerWindowsNotification('Notifikasi Windows Aktif', 'Notifikasi desktop untuk ASystem berhasil diaktifkan. Anda akan menerima peringatan langsung di Windows saat ada tiket, balasan, tugas atau pesan baru.');
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Notifikasi Desktop Aktif',
                            text: 'Notifikasi Windows berhasil diaktifkan.',
                            showConfirmButton: false,
                            timer: 3500
                        });
                    }
                } catch (e) {
                    console.error('Error requesting desktop permission:', e);
                }
            },

            toggleDropdown() {
                this.isOpen = !this.isOpen;
                if (this.isOpen) {
                    this.fetchNotifications(false);
                }
            },

            playChime() {
                try {
                    const AudioCtx = window.AudioContext || window.webkitAudioContext;
                    if (!AudioCtx) return;
                    const ctx = new AudioCtx();
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(587.33, ctx.currentTime);
                    osc.frequency.setValueAtTime(880, ctx.currentTime + 0.08);
                    gain.gain.setValueAtTime(0.12, ctx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.35);
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.start();
                    osc.stop(ctx.currentTime + 0.35);
                } catch (e) {}
            },

            showPersistentResetToast(req) {
                if (typeof Swal === 'undefined') return;

                // Jangan munculkan popup jika admin sedang aktif membuka tiket yang sama persis
                const currentUrlParams = new URLSearchParams(window.location.search);
                const currentTicketId = currentUrlParams.get('id');
                if (window.location.pathname.includes('/admin/bantuan-login') && currentTicketId == req.id) {
                    return;
                }

                Swal.fire({
                    toast: true,
                    position: 'bottom-end',
                    showConfirmButton: true,
                    confirmButtonText: '<i class="fa-solid fa-headset mr-1"></i> Buka Bantuan Login',
                    showCloseButton: true,
                    showCancelButton: false,
                    timer: false, // TIDAK AUTO CLOSE (Sesuai Permintaan User!)
                    timerProgressBar: false,
                    iconHtml: `<div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold shadow bg-gradient-to-tr from-amber-500 to-rose-600">
                                 <i class="fa-solid fa-key text-xs"></i>
                               </div>`,
                    customClass: {
                        popup: 'rounded-2xl shadow-2xl border-2 border-amber-400 bg-white/95 backdrop-blur-md cursor-pointer hover:shadow-2xl text-left p-3.5',
                        title: 'text-xs font-extrabold text-slate-800 m-0 text-left',
                        htmlContainer: 'text-xs text-slate-600 m-0 mt-1 text-left',
                        confirmButton: 'px-3 py-1.5 rounded-xl bg-gradient-to-r from-amber-600 to-rose-600 hover:from-amber-700 hover:to-rose-700 text-white font-bold text-xs shadow-sm border-0 transition-all'
                    },
                    title: `<div class="flex items-center justify-between gap-2">
                              <div class="flex items-center gap-1.5 text-amber-800 font-extrabold text-xs">
                                <span class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
                                <span>Permintaan Reset Password</span>
                              </div>
                              <span class="text-[9px] font-black px-1.5 py-0.5 rounded bg-rose-100 text-rose-700 uppercase">Pending</span>
                            </div>`,
                    html: `
                        <div class="mt-2 space-y-1 bg-amber-50/70 p-2.5 rounded-xl border border-amber-200/80 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="font-extrabold text-slate-900 text-xs">${this.escape(req.nama_karyawan)}</span>
                                <span class="text-[10px] font-mono font-bold text-slate-600">NIK: ${this.escape(req.nik)}</span>
                            </div>
                            <div class="text-[11px] text-slate-500 flex items-center gap-1">
                                <i class="fa-solid fa-building text-[9px] text-slate-400"></i>
                                <span>${this.escape(req.entitas || 'ESA Groups')} • ${this.escape(req.tipe_karyawan || 'Karyawan')}</span>
                            </div>
                            <div class="text-[11px] text-slate-600 italic line-clamp-2 mt-1">
                                "${this.escape(req.request_message || 'Mohon bantuan kirim akses password login.')}"
                            </div>
                        </div>
                    `,
                    didOpen: (toast) => {
                        toast.addEventListener('click', (ev) => {
                            if (!ev.target.closest('.swal2-close')) {
                                window.location.href = `{{ route('admin.auth-chat.index') }}?id=${req.id}`;
                            }
                        });
                    }
                });
            },

            async fetchNotifications(isFirstRun = false) {
                @auth
                try {
                    const params = new URLSearchParams({
                        last_chat_id: this.lastChatId,
                        last_ticket_id: this.lastTicketId,
                        last_reply_id: this.lastReplyId,
                        last_task_id: this.lastTaskId
                    });
                    const url = `{{ route('workplan.chat.notifications.check') }}?${params.toString()}`;
                    const res = await fetch(url, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!res.ok) return;
                    const data = await res.json();

                    if (data.success) {
                        this.unreadTotal = data.unread_total || 0;
                        this.unreadGroups = data.unread_groups || [];
                        this.pendingResets = data.pending_resets || [];
                        this.pendingResetsCount = data.pending_resets_count || 0;
                        this.unreadTickets = data.unread_tickets || [];
                        this.unreadTicketsCount = data.unread_tickets_count || 0;
                        this.unreadTasks = data.unread_tasks || [];
                        this.unreadTasksCount = data.unread_tasks_count || 0;

                        let shouldRing = false;

                        // 1. Deteksi Permintaan Reset Password Pending (Khusus Admin)
                        if (this.pendingResets && this.pendingResets.length > 0) {
                            const newResets = this.pendingResets.filter(r => !this.notifiedResetIds.has(r.id));
                            if (newResets.length > 0) {
                                shouldRing = true;
                                newResets.forEach(req => {
                                    this.notifiedResetIds.add(req.id);
                                    this.showPersistentResetToast(req);
                                    triggerWindowsNotification(
                                        'Permintaan Reset Password',
                                        `${req.nama_karyawan} (${req.nik}): ${req.request_message || 'Mohon bantuan akses password login'}`,
                                        `{{ route('admin.auth-chat.index') }}?id=${req.id}`
                                    );
                                });
                            }
                        }

                        // JIKA BUKAN FIRST RUN: PROSES TOAST & WINDOWS NOTIFICATION
                        if (!isFirstRun) {
                            // 2. Chat Group Baru
                            if (data.new_messages && data.new_messages.length > 0) {
                                shouldRing = true;
                                const isChatPage = window.location.pathname.includes('/workplan-chat');
                                data.new_messages.forEach(msg => {
                                    const chatUrl = `{{ url('/workplan-chat') }}?group_id=${msg.group_id}`;
                                    // Windows Desktop Notification
                                    triggerWindowsNotification(
                                        `Chat [${msg.group_name}]`,
                                        `${msg.user_sender}: ${msg.message_text}`,
                                        chatUrl,
                                        msg.sender_avatar
                                    );

                                    // In-system SweetAlert Toast (jika bukan di halaman chat aktif)
                                    if (!isChatPage && typeof Swal !== 'undefined') {
                                        Swal.fire({
                                            toast: true,
                                            position: 'top-end',
                                            showConfirmButton: false,
                                            showCloseButton: true,
                                            timer: 6000,
                                            timerProgressBar: true,
                                            iconHtml: `<div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold shadow-xs bg-emerald-600">
                                                         <i class="fa-solid fa-comments text-xs"></i>
                                                       </div>`,
                                            customClass: {
                                                popup: 'rounded-2xl shadow-2xl border border-emerald-300 bg-white/95 backdrop-blur-md cursor-pointer hover:shadow-2xl text-left',
                                                title: 'text-xs font-extrabold text-slate-800 m-0 text-left',
                                                htmlContainer: 'text-xs text-slate-600 m-0 mt-1 text-left'
                                            },
                                            title: `<div class="flex items-center gap-1.5 text-emerald-800 font-extrabold text-xs">
                                                      <i class="fa-solid fa-users text-[10px] text-emerald-600"></i>
                                                      <span>${this.escape(msg.group_name)}</span>
                                                    </div>`,
                                            html: `
                                                <div class="flex items-start gap-2.5 pt-1">
                                                    <img src="${msg.sender_avatar || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(msg.user_sender) + '&background=059669&color=fff'}" 
                                                         class="w-7 h-7 rounded-full object-cover border border-slate-200 flex-shrink-0 mt-0.5 shadow-2xs">
                                                    <div class="min-w-0 flex-1">
                                                        <div class="text-[11px] font-bold text-slate-800 truncate">${this.escape(msg.user_sender)}</div>
                                                        <div class="text-xs text-slate-600 line-clamp-2 leading-relaxed">${this.escape(msg.message_text)}</div>
                                                    </div>
                                                </div>
                                            `,
                                            didOpen: (toast) => {
                                                toast.addEventListener('click', (ev) => {
                                                    if (!ev.target.closest('.swal2-close')) {
                                                        window.location.href = chatUrl;
                                                    }
                                                });
                                            }
                                        });
                                    }
                                });
                            }

                            // 3. Tiket Helpdesk Baru (Ke semua agent divisi tujuan & admin)
                            if (data.new_tickets && data.new_tickets.length > 0) {
                                shouldRing = true;
                                data.new_tickets.forEach(tkt => {
                                    // Windows Desktop Notification
                                    triggerWindowsNotification(
                                        `Tiket Baru #${tkt.ticket_number} [Divisi ${tkt.division_name}]`,
                                        `${tkt.creator_name}: ${tkt.subject}`,
                                        tkt.url
                                    );

                                    // In-system SweetAlert Toast
                                    if (typeof Swal !== 'undefined') {
                                        Swal.fire({
                                            toast: true,
                                            position: 'top-end',
                                            showConfirmButton: false,
                                            showCloseButton: true,
                                            timer: 8000,
                                            timerProgressBar: true,
                                            iconHtml: `<div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold shadow bg-amber-500">
                                                         <i class="fa-solid fa-ticket text-xs"></i>
                                                       </div>`,
                                            customClass: {
                                                popup: 'rounded-2xl shadow-2xl border border-amber-300 bg-white/95 backdrop-blur-md cursor-pointer hover:shadow-2xl text-left',
                                                title: 'text-xs font-extrabold text-slate-800 m-0 text-left',
                                                htmlContainer: 'text-xs text-slate-600 m-0 mt-1 text-left'
                                            },
                                            title: `<div class="flex items-center gap-1.5 text-amber-800 font-extrabold text-xs">
                                                      <i class="fa-solid fa-ticket text-[10px] text-amber-600"></i>
                                                      <span>Tiket Baru #${this.escape(tkt.ticket_number)}</span>
                                                      <span class="text-[9px] font-black px-1.5 py-0.2 rounded bg-amber-100 text-amber-800 uppercase">${this.escape(tkt.priority)}</span>
                                                    </div>`,
                                            html: `
                                                <div class="pt-1">
                                                    <div class="text-[11px] font-bold text-slate-800 truncate">${this.escape(tkt.subject)}</div>
                                                    <div class="text-[10px] text-slate-500 mt-0.5 flex items-center gap-1">
                                                        <span>${this.escape(tkt.creator_name)}</span>
                                                        <span>&bull;</span>
                                                        <span>Divisi ${this.escape(tkt.division_name)}</span>
                                                    </div>
                                                </div>
                                            `,
                                            didOpen: (toast) => {
                                                toast.addEventListener('click', (ev) => {
                                                    if (!ev.target.closest('.swal2-close')) {
                                                        window.location.href = tkt.url;
                                                    }
                                                });
                                            }
                                        });
                                    }
                                });
                            }

                            // 4. Balasan Tiket (Ticket Reply)
                            if (data.new_replies && data.new_replies.length > 0) {
                                shouldRing = true;
                                data.new_replies.forEach(rep => {
                                    // Windows Desktop Notification
                                    triggerWindowsNotification(
                                        `Balasan Tiket #${rep.ticket_number}`,
                                        `${rep.sender_name}: ${rep.message_snippet}`,
                                        rep.url,
                                        rep.sender_avatar
                                    );

                                    // In-system SweetAlert Toast
                                    if (typeof Swal !== 'undefined') {
                                        Swal.fire({
                                            toast: true,
                                            position: 'top-end',
                                            showConfirmButton: false,
                                            showCloseButton: true,
                                            timer: 7000,
                                            timerProgressBar: true,
                                            iconHtml: `<div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold shadow bg-blue-500">
                                                         <i class="fa-solid fa-reply text-xs"></i>
                                                       </div>`,
                                            customClass: {
                                                popup: 'rounded-2xl shadow-2xl border border-blue-300 bg-white/95 backdrop-blur-md cursor-pointer hover:shadow-2xl text-left',
                                                title: 'text-xs font-extrabold text-slate-800 m-0 text-left',
                                                htmlContainer: 'text-xs text-slate-600 m-0 mt-1 text-left'
                                            },
                                            title: `<div class="flex items-center gap-1.5 text-blue-800 font-extrabold text-xs">
                                                      <i class="fa-solid fa-reply text-[10px] text-blue-600"></i>
                                                      <span>Balasan Tiket #${this.escape(rep.ticket_number)}</span>
                                                    </div>`,
                                            html: `
                                                <div class="pt-1">
                                                    <div class="text-[11px] font-bold text-slate-800">${this.escape(rep.sender_name)}:</div>
                                                    <div class="text-xs text-slate-600 line-clamp-2 mt-0.5">${this.escape(rep.message_snippet)}</div>
                                                </div>
                                            `,
                                            didOpen: (toast) => {
                                                toast.addEventListener('click', (ev) => {
                                                    if (!ev.target.closest('.swal2-close')) {
                                                        window.location.href = rep.url;
                                                    }
                                                });
                                            }
                                        });
                                    }
                                });
                            }

                            // 5. Tugas Work Plan Baru (Workplan Tasks)
                            if (data.new_tasks && data.new_tasks.length > 0) {
                                shouldRing = true;
                                data.new_tasks.forEach(tsk => {
                                    // Windows Desktop Notification
                                    triggerWindowsNotification(
                                        `Tugas Work Plan Baru`,
                                        `${tsk.title} (Dari: ${tsk.delegator})`,
                                        tsk.url
                                    );

                                    // In-system SweetAlert Toast
                                    if (typeof Swal !== 'undefined') {
                                        Swal.fire({
                                            toast: true,
                                            position: 'top-end',
                                            showConfirmButton: false,
                                            showCloseButton: true,
                                            timer: 7000,
                                            timerProgressBar: true,
                                            iconHtml: `<div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold shadow bg-indigo-600">
                                                         <i class="fa-solid fa-list-check text-xs"></i>
                                                       </div>`,
                                            customClass: {
                                                popup: 'rounded-2xl shadow-2xl border border-indigo-300 bg-white/95 backdrop-blur-md cursor-pointer hover:shadow-2xl text-left',
                                                title: 'text-xs font-extrabold text-slate-800 m-0 text-left',
                                                htmlContainer: 'text-xs text-slate-600 m-0 mt-1 text-left'
                                            },
                                            title: `<div class="flex items-center gap-1.5 text-indigo-800 font-extrabold text-xs">
                                                      <i class="fa-solid fa-list-check text-[10px] text-indigo-600"></i>
                                                      <span>Tugas Work Plan Baru</span>
                                                    </div>`,
                                            html: `
                                                <div class="pt-1">
                                                    <div class="text-[11px] font-bold text-slate-800">${this.escape(tsk.title)}</div>
                                                    <div class="text-[10px] text-slate-500 mt-0.5">Dari: ${this.escape(tsk.delegator)}</div>
                                                </div>
                                            `,
                                            didOpen: (toast) => {
                                                toast.addEventListener('click', (ev) => {
                                                    if (!ev.target.closest('.swal2-close')) {
                                                        window.location.href = tsk.url;
                                                    }
                                                });
                                            }
                                        });
                                    }
                                });
                            }
                        }

                        // Bunyikan chime jika ada notifikasi baru
                        if (shouldRing) {
                            this.isRinging = true;
                            this.playChime();
                            setTimeout(() => { this.isRinging = false; }, 3000);
                        }

                        // Simpan latest max IDs
                        if (data.max_id > 0) this.lastChatId = data.max_id;
                        if (data.max_ticket_id > 0) this.lastTicketId = data.max_ticket_id;
                        if (data.max_reply_id > 0) this.lastReplyId = data.max_reply_id;
                        if (data.max_task_id > 0) this.lastTaskId = data.max_task_id;
                    }
                } catch (err) {
                    // Fail gracefully
                }
                @endauth
            },

            escape(str) {
                const div = document.createElement('div');
                div.textContent = str || '';
                return div.innerHTML;
            }
        };
    }
    </script>

    <!-- Dashboard Custom Theme & Dark Mode Manager -->
    <script>
    function updateThemeUI() {
        const currentTheme = localStorage.getItem('asystem_theme_mode') || 'light';
        const icon = document.getElementById('themeToggleIcon');
        const btn = document.getElementById('quickThemeToggleBtn');
        if (icon) {
            if (currentTheme === 'dark') {
                icon.className = 'fa-solid fa-sun text-lg text-amber-400 rotate-180 transition-transform duration-300';
                if (btn) btn.title = 'Ganti ke Mode Terang';
            } else {
                icon.className = 'fa-regular fa-moon text-lg text-slate-500 transition-transform duration-300';
                if (btn) btn.title = 'Ganti ke Mode Gelap';
            }
        }
    }

    function toggleQuickTheme() {
        const currentTheme = localStorage.getItem('asystem_theme_mode') || 'light';
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        const palette = localStorage.getItem('asystem_theme_palette') || 'navy';
        const primary = localStorage.getItem('asystem_primary_color') || '#0F52BA';

        localStorage.setItem('asystem_theme_mode', newTheme);
        document.documentElement.setAttribute('data-theme', newTheme);
        document.documentElement.setAttribute('data-palette', palette);
        document.documentElement.style.setProperty('--color-primary', primary);
        if (newTheme === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
        updateThemeUI();

        // Dispatch custom event for real-time sync with profile page or other listeners
        window.dispatchEvent(new CustomEvent('asystemThemeChanged', { 
            detail: { theme: newTheme, palette: palette, primary: primary } 
        }));
    }

    // Global Searchable Dropdown Helper for Modals & Forms
    function searchableModalSelect(config) {
        return {
            open: false,
            name: config.name,
            placeholder: config.placeholder || '-- Pilih --',
            searchPlaceholder: config.searchPlaceholder || 'Ketik untuk mencari...',
            selectedValue: config.selected !== undefined && config.selected !== null ? String(config.selected) : '',
            searchQuery: '',
            rawOptions: config.options || [],
            required: !!config.required,
            allowCustom: !!config.allowCustom,
            color: config.color || 'primary',

            get parsedOptions() {
                return (this.rawOptions || []).map(opt => {
                    if (typeof opt === 'object' && opt !== null) {
                        return {
                            value: String(opt.value !== undefined ? opt.value : ''),
                            label: String(opt.label || opt.name || opt.value || ''),
                            sublabel: opt.sublabel ? String(opt.sublabel) : ''
                        };
                    }
                    return {
                        value: String(opt),
                        label: String(opt),
                        sublabel: ''
                    };
                });
            },

            get displayLabel() {
                if (!this.selectedValue) return this.placeholder;
                const found = this.parsedOptions.find(o => o.value.toLowerCase() === this.selectedValue.toLowerCase());
                return found ? found.label : this.selectedValue;
            },

            get selectedOption() {
                if (!this.selectedValue) return null;
                return this.parsedOptions.find(o => o.value.toLowerCase() === this.selectedValue.toLowerCase()) || null;
            },

            get filteredOptions() {
                const list = this.parsedOptions;
                if (!this.searchQuery || !this.searchQuery.trim()) {
                    return list.slice(0, 80);
                }
                const q = this.searchQuery.toLowerCase().trim();
                const words = q.split(/\s+/).filter(w => w.length > 0);
                
                const matched = list.filter(opt => {
                    const text = (opt.label + ' ' + opt.sublabel + ' ' + opt.value).toLowerCase();
                    return words.every(word => text.includes(word));
                });

                return matched.slice(0, 80);
            },

            get totalMatches() {
                if (!this.searchQuery || !this.searchQuery.trim()) {
                    return this.parsedOptions.length;
                }
                const q = this.searchQuery.toLowerCase().trim();
                const words = q.split(/\s+/).filter(w => w.length > 0);
                return this.parsedOptions.filter(opt => {
                    const text = (opt.label + ' ' + opt.sublabel + ' ' + opt.value).toLowerCase();
                    return words.every(word => text.includes(word));
                }).length;
            },

            toggle() {
                this.open = !this.open;
                if (this.open) {
                    this.searchQuery = '';
                    this.$nextTick(() => {
                        if (this.$refs.searchInput) {
                            this.$refs.searchInput.focus();
                        }
                    });
                }
            },

            select(val) {
                this.selectedValue = String(val);
                this.open = false;
                this.searchQuery = '';
            },

            clear(e) {
                if (e) e.stopPropagation();
                this.selectedValue = '';
                this.searchQuery = '';
                this.open = false;
            }
        };
    }
    window.searchableModalSelect = searchableModalSelect;

    document.addEventListener('DOMContentLoaded', function() {
        updateThemeUI();
    });
    </script>
    
    @yield('scripts')
    @stack('scripts')
</body>
</html>