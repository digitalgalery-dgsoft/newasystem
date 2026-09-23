@extends('layouts.app')

@section('title', 'Pengaturan AI & WhatsApp - ASystem Support System')

@section('content')
<div class="space-y-6">
    <!-- PAGE HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-primary mb-1">
                <i class="fa-solid fa-sliders text-primary"></i>
                <span>Fitur & Layanan &bull; Setting AI & Otomatisasi</span>
            </div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight">Pengaturan AI & WhatsApp Gateway</h1>
            <p class="text-xs text-slate-500 mt-0.5">Konfigurasi API Google Gemini, API Fallback, Device WhatsApp, dan Template Notifikasi Pelamar.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('airanking.index') }}" class="btn-att-secondary text-xs">
                <i class="fa-solid fa-ranking-star text-amber-500"></i>
                <span>AI Ranking</span>
            </a>
            <a href="{{ route('kandidatportal.index') }}" class="btn-att-primary text-xs">
                <i class="fa-solid fa-globe"></i>
                <span>Kandidat Portal</span>
            </a>
        </div>
    </div>

    <!-- METRIC CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Total Dianalisis -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 border border-blue-100 text-primary flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-brain"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Dianalisis AI</p>
                <p class="text-2xl font-black text-slate-800">{{ $totalAnalyzed }}</p>
                <span class="text-[11px] font-semibold text-blue-600">Pelamar Terverifikasi</span>
            </div>
        </div>

        <!-- 2. Kandidat Hijau -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-star"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Kandidat Hijau (≥85%)</p>
                <p class="text-2xl font-black text-emerald-600">{{ $totalHighMatch }}</p>
                <span class="text-[11px] font-semibold text-emerald-600">Sangat Direkomendasikan</span>
            </div>
        </div>

        <!-- 3. Kunci API Gemini Aktif -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-50 border border-purple-100 text-purple-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-key"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Kunci API Gemini</p>
                <p class="text-2xl font-black text-purple-600">{{ $activeKeysCount }} Pool Key</p>
                <span class="text-[11px] font-semibold text-purple-600">Rotasi Otomatis</span>
            </div>
        </div>

        <!-- 4. Status WA Gateway -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-brands fa-whatsapp"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">WhatsApp Gateway</p>
                <p class="text-2xl font-black text-slate-800">Online</p>
                <span class="text-[11px] font-semibold text-emerald-600">Device Terhubung</span>
            </div>
        </div>
    </div>

    <!-- AI FALLBACK EXECUTION FLOW BANNER -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-blue-950 text-white p-5 rounded-2xl shadow-sm border border-indigo-900/60 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-cyan-400">
                <i class="fa-solid fa-route"></i>
                <span>Hierarki Alur Eksekusi AI Analisa CV</span>
            </div>
            <div class="mt-2.5 flex flex-wrap items-center gap-2 sm:gap-2.5 text-xs font-bold">
                <span class="px-3 py-1.5 rounded-xl bg-blue-500/20 text-blue-200 border border-blue-400/30 flex items-center gap-1.5 shadow-xs">
                    <span class="w-5 h-5 rounded-full bg-blue-500 text-white flex items-center justify-center text-[10px] font-black">1</span>
                    <span>API Key Gemini</span>
                </span>
                <i class="fa-solid fa-chevron-right text-slate-500 text-[11px]"></i>
                <span class="px-3 py-1.5 rounded-xl bg-purple-500/20 text-purple-200 border border-purple-400/30 flex items-center gap-1.5 shadow-xs">
                    <span class="w-5 h-5 rounded-full bg-purple-500 text-white flex items-center justify-center text-[10px] font-black">2</span>
                    <span>OpenRouter API</span>
                </span>
                <i class="fa-solid fa-chevron-right text-slate-500 text-[11px]"></i>
                <span class="px-3 py-1.5 rounded-xl bg-amber-500/20 text-amber-200 border border-amber-400/30 flex items-center gap-1.5 shadow-xs">
                    <span class="w-5 h-5 rounded-full bg-amber-500 text-white flex items-center justify-center text-[10px] font-black">3</span>
                    <span>Sumopod Fallback</span>
                </span>
                <i class="fa-solid fa-chevron-right text-slate-500 text-[11px]"></i>
                <span class="px-3 py-1.5 rounded-xl bg-rose-500/25 text-rose-300 border border-rose-400/40 flex items-center gap-1.5 shadow-xs">
                    <i class="fa-solid fa-circle-stop text-rose-400 text-xs"></i>
                    <span>Berhenti Jika Semua Limit</span>
                </span>
            </div>
        </div>
        <div class="text-[11px] text-slate-300 max-w-sm lg:text-right border-t lg:border-t-0 lg:border-l border-indigo-800/60 pt-3 lg:pt-0 lg:pl-5 leading-relaxed">
            <span class="text-cyan-300 font-semibold">Kebijakan Kuota:</span> Jika seluruh API Key Gemini, OpenRouter & Sumopod gagal atau mencapai batas limit, proses analisis AI akan otomatis berhenti dan kandidat berstatus antrean tertunda.
        </div>
    </div>

    <!-- MAIN SETTING FORM -->
    <form action="{{ route('aisetting.update') }}" method="POST" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            <!-- LEFT: GEMINI, OPENROUTER & SUMOPOD CONFIGURATION (7 COLS) -->
            <div class="lg:col-span-7 space-y-6">

                <!-- 1. Google Gemini Configuration -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-5">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-blue-50 text-primary flex items-center justify-center font-bold text-sm">
                                <i class="fa-solid fa-brain"></i>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-sm font-bold text-slate-800">1. Google Gemini AI (Utama)</h3>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-primary">Priority #1</span>
                                </div>
                                <p class="text-xs text-slate-500">Model utama untuk ekstraksi CV dan penilaian kecocokan pelamar</p>
                            </div>
                        </div>
                        <button type="button" onclick="testGeminiConnection()" class="btn-att-secondary text-xs px-3 py-1.5 flex items-center gap-1.5 text-primary border-primary-300 hover:bg-primary-50">
                            <i class="fa-solid fa-bolt text-xs text-amber-500"></i>
                            <span>Test Koneksi</span>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <!-- Model Selection -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-xs font-bold text-slate-700">Model Gemini Aktif</label>
                                <span class="text-[11px] font-medium text-slate-400">{{ count($setting->gemini_models) }} Model Tersedia</span>
                            </div>
                            <select name="gemini_model" id="geminiModelSelect" class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-primary-100 focus:border-primary outline-none">
                                @foreach($setting->gemini_models as $gm)
                                    <option value="{{ $gm }}" {{ ($setting->gemini_model === $gm || (empty($setting->gemini_model) && $loop->first)) ? 'selected' : '' }}>
                                        {{ $gm }} {{ ($gm === 'gemini-2.5-flash') ? '(Rekomendasi Cepat)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Add New Custom Gemini Model -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Tambah Model Gemini Baru (Kustom)</label>
                            <div class="flex items-center gap-2">
                                <input type="text" name="new_gemini_model" placeholder="Ketik nama model baru (misal: gemini-2.0-flash-exp)..." class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2 text-xs font-mono text-slate-800 focus:ring-2 focus:ring-primary-100 focus:border-primary outline-none">
                            </div>
                            <!-- Model Chips List -->
                            <div class="mt-2 flex flex-wrap gap-1.5 items-center">
                                <span class="text-[10px] text-slate-400 font-semibold mr-1">Daftar Pilihan:</span>
                                @foreach($setting->gemini_models as $gm)
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                    <span>{{ $gm }}</span>
                                    @if(count($setting->gemini_models) > 1)
                                    <button type="button" onclick="deleteModel('gemini', '{{ $gm }}')" class="text-slate-400 hover:text-rose-600 ml-0.5" title="Hapus model dari daftar">
                                        <i class="fa-solid fa-xmark text-[9px]"></i>
                                    </button>
                                    @endif
                                </span>
                                @endforeach
                            </div>
                        </div>

                        <!-- Gemini API Keys Pool -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-xs font-bold text-slate-700">Daftar API Key Gemini (1 Baris = 1 Key)</label>
                                <span class="text-[11px] font-bold text-slate-500">{{ count($setting->keys_list) }} Kunci Aktif Terdaftar</span>
                            </div>
                            <textarea name="gemini_keys" rows="3" class="w-full font-mono bg-slate-50 border border-slate-300 rounded-xl p-3 text-xs text-slate-800 focus:ring-2 focus:ring-primary-100 focus:border-primary outline-none" placeholder="Masukkan satu Google Gemini API Key per baris...">{{ $setting->gemini_keys }}</textarea>
                            <p class="text-[11px] text-slate-400 mt-1 italic">* Sistem akan otomatis merotasi API Key jika salah satu key terkena limit, dengan jeda istirahat 2 menit.</p>
                        </div>

                        <!-- Add New Single Key -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Tambah Kunci API Baru Cepat</label>
                            <div class="flex items-center gap-2">
                                <input type="text" name="new_gemini_key" placeholder="Tempelkan AIzaSy... baru di sini" class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2 text-xs font-mono text-slate-800 focus:ring-2 focus:ring-primary-100 focus:border-primary outline-none">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 1.1 List Token Gemini Expired / Error -->
                @if(!empty($expiredKeys) && count($expiredKeys) > 0)
                <div class="bg-rose-50/70 rounded-2xl border border-rose-200 shadow-xs p-5 space-y-3">
                    <div class="flex items-center justify-between pb-2.5 border-b border-rose-200/80">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center text-sm font-bold">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-rose-900">List Token Gemini Expired / Error ({{ count($expiredKeys) }})</h4>
                                <p class="text-[11px] text-rose-600">Token berikut mengalami error permanen dan dinonaktifkan otomatis agar tidak mengganggu antrean.</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2 max-h-60 overflow-y-auto pr-1">
                        @foreach($expiredKeys as $exp)
                        @php
                            $expKey = is_array($exp) ? ($exp['key'] ?? '') : $exp;
                            $expErr = is_array($exp) ? ($exp['error'] ?? 'API Key Invalid / Expired') : 'API Key Invalid';
                            $expDate = is_array($exp) ? ($exp['detected_at'] ?? '') : '';
                            $maskedKey = strlen($expKey) > 16 ? substr($expKey, 0, 8) . '...' . substr($expKey, -6) : $expKey;
                        @endphp
                        <div class="bg-white rounded-xl p-3 border border-rose-200/80 flex items-center justify-between gap-3 text-xs">
                            <div class="min-w-0 flex-1 space-y-0.5">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-mono font-bold text-slate-800 text-[11px]">{{ $maskedKey }}</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700 uppercase">Nonaktif</span>
                                    @if(!empty($expDate))
                                    <span class="text-[10px] text-slate-400">{{ $expDate }}</span>
                                    @endif
                                </div>
                                <p class="text-[11px] text-rose-600 truncate font-mono" title="{{ $expErr }}">
                                    <i class="fa-solid fa-circle-xmark mr-1"></i>{{ $expErr }}
                                </p>
                            </div>
                            <button type="button" onclick="deleteExpiredKey('{{ $expKey }}')" class="px-2.5 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 text-[11px] font-bold border border-rose-200 transition-all shrink-0 flex items-center gap-1">
                                <i class="fa-solid fa-trash-can text-xs"></i>
                                <span>Hapus</span>
                            </button>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- 2. OpenRouter AI Configuration (BARU) -->
                <div class="bg-white rounded-2xl border border-purple-200 shadow-sm p-6 space-y-5 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-purple-50 rounded-full blur-2xl -z-10"></div>
                    
                    <div class="flex items-center justify-between pb-3 border-b border-purple-100">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-sm">
                                <i class="fa-solid fa-network-wired"></i>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-sm font-bold text-slate-800">2. OpenRouter AI Gateway</h3>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-700">Fallback #1</span>
                                </div>
                                <p class="text-xs text-slate-500">Cadangan otomatis pertama saat seluruh API Key Gemini mengalami rate limit</p>
                            </div>
                        </div>
                        <button type="button" onclick="testOpenrouterConnection()" class="btn-att-secondary text-xs px-3 py-1.5 flex items-center gap-1.5 text-purple-700 border-purple-300 hover:bg-purple-50">
                            <i class="fa-solid fa-bolt text-xs text-purple-600"></i>
                            <span>Test Koneksi</span>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <!-- OpenRouter API Key -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-xs font-bold text-slate-700">OpenRouter API Key</label>
                                <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                    <i class="fa-solid fa-check mr-1"></i>Bearer Auth Aktif
                                </span>
                            </div>
                            <div class="relative">
                                <input type="password" id="openrouterKeyInput" name="openrouter_key" value="{{ $setting->openrouter_key }}" placeholder="sk-or-v1-..." class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs font-mono text-slate-800 focus:ring-2 focus:ring-purple-100 focus:border-purple-600 outline-none pr-10">
                                <button type="button" onclick="togglePasswordVisibility('openrouterKeyInput', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <!-- OpenRouter Model Selection -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-xs font-bold text-slate-700">Model OpenRouter Aktif</label>
                                <div class="flex items-center gap-1 text-[11px] text-purple-600 font-semibold">
                                    <i class="fa-solid fa-brain"></i>
                                    <span>Reasoning: Enabled</span>
                                </div>
                            </div>
                            <select name="openrouter_model" id="openrouterModelSelect" class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-purple-100 focus:border-purple-600 outline-none">
                                @foreach($setting->openrouter_models as $orm)
                                    <option value="{{ $orm }}" {{ ($setting->openrouter_model === $orm || (empty($setting->openrouter_model) && $orm === 'nvidia/nemotron-3-ultra-550b-a55b:free')) ? 'selected' : '' }}>
                                        {{ $orm }} {{ ($orm === 'nvidia/nemotron-3-ultra-550b-a55b:free') ? '(Default Free Reasoning)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Add New Custom OpenRouter Model -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Tambah Model OpenRouter Baru (Kustom)</label>
                            <input type="text" name="new_openrouter_model" placeholder="Ketik nama model OpenRouter (misal: deepseek/deepseek-r1:free)..." class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2 text-xs font-mono text-slate-800 focus:ring-2 focus:ring-purple-100 focus:border-purple-600 outline-none">
                            
                            <!-- Model Chips List -->
                            <div class="mt-2 flex flex-wrap gap-1.5 items-center">
                                <span class="text-[10px] text-slate-400 font-semibold mr-1">Daftar Model:</span>
                                @foreach($setting->openrouter_models as $orm)
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                    <span>{{ $orm }}</span>
                                    @if(count($setting->openrouter_models) > 1)
                                    <button type="button" onclick="deleteModel('openrouter', '{{ $orm }}')" class="text-purple-400 hover:text-rose-600 ml-0.5" title="Hapus model dari daftar">
                                        <i class="fa-solid fa-xmark text-[9px]"></i>
                                    </button>
                                    @endif
                                </span>
                                @endforeach
                            </div>
                        </div>

                        <div class="p-3 bg-purple-50/60 rounded-xl border border-purple-200/80 text-[11px] text-purple-800 flex items-center justify-between">
                            <span class="flex items-center gap-1.5">
                                <i class="fa-solid fa-shield-halved text-purple-600"></i>
                                <span>Payload cURL otomatis menyertakan parameter <code>"reasoning": {"enabled": true}</code></span>
                            </span>
                            <span class="font-mono font-semibold text-[10px] text-purple-600">openrouter.ai</span>
                        </div>
                    </div>
                </div>

                <!-- 3. Fallback AI Provider (Sumopod / OpenAI) -->
                <div class="bg-white rounded-2xl border border-amber-200 shadow-sm p-6 space-y-5 relative overflow-hidden">
                    <div class="flex items-center justify-between pb-3 border-b border-amber-100">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-sm">
                                <i class="fa-solid fa-shield-halved"></i>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-sm font-bold text-slate-800">3. Sumopod / OpenAI API (Cadangan Akhir)</h3>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-700">Fallback #2</span>
                                </div>
                                <p class="text-xs text-slate-500">Cadangan tahap akhir jika Gemini dan OpenRouter keduanya limit atau gagal</p>
                            </div>
                        </div>
                        <button type="button" onclick="testSumopodConnection()" class="btn-att-secondary text-xs px-3 py-1.5 flex items-center gap-1.5 text-amber-700 border-amber-300 hover:bg-amber-50">
                            <i class="fa-solid fa-bolt text-xs text-amber-600"></i>
                            <span>Test Koneksi</span>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Sumopod / OpenAI API Key</label>
                                <div class="relative">
                                    <input type="password" id="sumopodKeyInput" name="sumopod_key" value="{{ $setting->sumopod_key }}" placeholder="sk-..." class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs font-mono text-slate-800 focus:ring-2 focus:ring-amber-100 focus:border-amber-500 outline-none pr-10">
                                    <button type="button" onclick="togglePasswordVisibility('sumopodKeyInput', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                                        <i class="fa-regular fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Model Sumopod Aktif</label>
                                <select name="sumopod_model" id="sumopodModelSelect" class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-amber-100 focus:border-amber-500 outline-none">
                                    @foreach($setting->sumopod_models as $sm)
                                        <option value="{{ $sm }}" {{ ($setting->sumopod_model === $sm || (empty($setting->sumopod_model) && $sm === 'gpt-4o-mini')) ? 'selected' : '' }}>
                                            {{ $sm }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Add New Custom Sumopod Model -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Tambah Model Sumopod Baru (Kustom)</label>
                            <input type="text" name="new_sumopod_model" placeholder="Ketik nama model Sumopod (misal: claude-3-7-sonnet)..." class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2 text-xs font-mono text-slate-800 focus:ring-2 focus:ring-amber-100 focus:border-amber-500 outline-none">
                            
                            <!-- Model Chips List -->
                            <div class="mt-2 flex flex-wrap gap-1.5 items-center">
                                <span class="text-[10px] text-slate-400 font-semibold mr-1">Daftar Model:</span>
                                @foreach($setting->sumopod_models as $sm)
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                    <span>{{ $sm }}</span>
                                    @if(count($setting->sumopod_models) > 1)
                                    <button type="button" onclick="deleteModel('sumopod', '{{ $sm }}')" class="text-amber-400 hover:text-rose-600 ml-0.5" title="Hapus model dari daftar">
                                        <i class="fa-solid fa-xmark text-[9px]"></i>
                                    </button>
                                    @endif
                                </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- RIGHT: WHATSAPP AUTOMATION & NOTIFICATION (5 COLS) -->
            <div class="lg:col-span-5 space-y-6">
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-5">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm">
                                <i class="fa-brands fa-whatsapp"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-800">WhatsApp Gateway</h3>
                                <p class="text-xs text-slate-500">Notifikasi otomatis akses tes ke nomor pelamar</p>
                            </div>
                        </div>
                        <button type="button" onclick="testWaModal()" class="btn-att-secondary text-xs px-3 py-1.5 flex items-center gap-1.5 text-emerald-700 border-emerald-300 hover:bg-emerald-50">
                            <i class="fa-brands fa-whatsapp text-emerald-600"></i>
                            <span>Test WA</span>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Device ID WhatsApp</label>
                            <input type="text" name="wa_device" value="{{ $setting->wa_device }}" placeholder="DEVICE-ESA-01" class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-primary-100 focus:border-primary outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">API Key WhatsApp</label>
                            <div class="relative">
                                <input type="password" id="waApiKeyInput" name="wa_api_key" value="{{ $setting->wa_api_key }}" placeholder="Token API Gateway" class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2 text-xs font-mono text-slate-800 focus:ring-2 focus:ring-primary-100 focus:border-primary outline-none pr-10">
                                <button type="button" onclick="togglePasswordVisibility('waApiKeyInput', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-xs font-bold text-slate-700">Template Pesan WhatsApp</label>
                                <span class="text-[10px] text-slate-400">Variabel Dinamis</span>
                            </div>
                            <textarea name="wa_template" id="waTemplateTextarea" rows="6" class="w-full bg-white border border-slate-300 rounded-xl p-3 text-xs text-slate-800 focus:ring-2 focus:ring-primary-100 focus:border-primary outline-none leading-relaxed">{{ $setting->wa_template }}</textarea>
                            
                            <!-- Variable Chips -->
                            <div class="mt-2 flex flex-wrap gap-1.5 items-center">
                                <span class="text-[10px] text-slate-400 font-semibold mr-1">Klik untuk sisipkan:</span>
                                <button type="button" onclick="insertVariable('{nama}')" class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300 transition-colors">
                                    {nama}
                                </button>
                                <button type="button" onclick="insertVariable('{job}')" class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300 transition-colors">
                                    {job}
                                </button>
                                <button type="button" onclick="insertVariable('{score}')" class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300 transition-colors">
                                    {score}
                                </button>
                                <button type="button" onclick="insertVariable('{link}')" class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300 transition-colors">
                                    {link}
                                </button>
                            </div>
                        </div>

                        <!-- Checkbox: Gunakan Device Pusat -->
                        <div class="pt-2">
                            <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer">
                                <input type="checkbox" name="wa_use_pusat" value="1" {{ $setting->wa_use_pusat ? 'checked' : '' }} class="rounded text-primary focus:ring-primary">
                                <span>Gunakan Device WhatsApp Pusat untuk seluruh notifikasi</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- SAVE BUTTON BAR -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-2 text-xs text-emerald-700 font-bold bg-emerald-50 border border-emerald-200 px-3 py-1.5 rounded-xl">
                        <i class="fa-solid fa-circle-check text-emerald-600"></i>
                        <span>Pengaturan Global Admin (Pusat)</span>
                    </div>
                    <button type="submit" class="w-full sm:w-auto btn-att-primary text-xs font-bold px-6 py-2.5 flex items-center justify-center gap-2 shadow-md shadow-primary/20">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Simpan Konfigurasi Global</span>
                    </button>
                </div>
            </div>

        </div>
    </form>

    <!-- TABEL 1: SETTING TEMPLATE WA PER AREA (tb_wa_area_setting) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm">
                    <i class="fa-solid fa-map-location-dot"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Template Pesan WhatsApp per Area (tb_wa_area_setting)</h3>
                    <p class="text-xs text-slate-500">Daftar template pesan WA spesifik per cabang/area yang tersimpan di sistem asal (21 Area)</p>
                </div>
            </div>
            <span class="text-xs font-bold text-indigo-700 bg-indigo-50 border border-indigo-200 px-3 py-1 rounded-xl">
                {{ $areaSettings->count() }} Area Terkonfigurasi
            </span>
        </div>

        <div class="overflow-x-auto rounded-xl border border-slate-200">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-bold">
                    <tr>
                        <th class="py-3 px-4 w-12 text-center">No.</th>
                        <th class="py-3 px-4 w-44">Area</th>
                        <th class="py-3 px-4">Template Pesan WhatsApp</th>
                        <th class="py-3 px-4 w-56">Pengunci Template (Rekruter)</th>
                        <th class="py-3 px-4 w-36 text-center">Terakhir Update</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($areaSettings as $idx => $area)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="py-2.5 px-4 text-center font-mono text-slate-400">{{ $idx + 1 }}</td>
                        <td class="py-2.5 px-4 font-bold text-slate-800">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 border border-slate-200">
                                <i class="fa-solid fa-location-dot text-indigo-500 text-[10px]"></i>
                                <span>{{ $area->area }}</span>
                            </span>
                        </td>
                        <td class="py-2.5 px-4 text-slate-600 font-mono text-[11px] max-w-md truncate" title="{{ $area->wa_template }}">
                            {{ !empty($area->wa_template) ? Str::limit($area->wa_template, 120) : '<Belum diisi template khusus>' }}
                        </td>
                        <td class="py-2.5 px-4">
                            <div class="text-slate-800 font-semibold text-xs">{{ $area->locked_by_nama ?: '-' }}</div>
                            <div class="text-[10px] text-slate-400 font-mono">{{ $area->locked_by_email }}</div>
                        </td>
                        <td class="py-2.5 px-4 text-center text-[11px] text-slate-500">
                            {{ $area->updated_at ? $area->updated_at->format('d/m/Y H:i') : '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-6 text-center text-slate-400 italic">Belum ada data template per area di tb_wa_area_setting.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- TABEL 2: SETTING PERANGKAT WA REKRUTER (tb_ai_setting_user) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-sm">
                    <i class="fa-solid fa-mobile-screen"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Daftar Pengaturan WhatsApp Rekruter (tb_ai_setting_user)</h3>
                    <p class="text-xs text-slate-500">Mapping 58 rekruter inhouse dengan konfigurasi area & status device gateway</p>
                </div>
            </div>
            <span class="text-xs font-bold text-purple-700 bg-purple-50 border border-purple-200 px-3 py-1 rounded-xl">
                {{ $userSettings->count() }} Rekruter Terdaftar
            </span>
        </div>

        <div class="overflow-x-auto rounded-xl border border-slate-200">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-bold">
                    <tr>
                        <th class="py-3 px-4 w-12 text-center">No.</th>
                        <th class="py-3 px-4 w-60">Email Rekruter</th>
                        <th class="py-3 px-4 w-40">Area</th>
                        <th class="py-3 px-4 w-48">Device WhatsApp Lokal</th>
                        <th class="py-3 px-4 w-36 text-center">Moda Pengiriman</th>
                        <th class="py-3 px-4">Template Khusus</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($userSettings as $idx => $usr)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="py-2.5 px-4 text-center font-mono text-slate-400">{{ $idx + 1 }}</td>
                        <td class="py-2.5 px-4 font-semibold text-slate-800 font-mono text-[11px]">{{ $usr->email }}</td>
                        <td class="py-2.5 px-4">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                {{ $usr->area ?: '-' }}
                            </span>
                        </td>
                        <td class="py-2.5 px-4 font-mono text-[11px] text-slate-600">
                            {{ $usr->wa_device ?: '<Device Pusat>' }}
                        </td>
                        <td class="py-2.5 px-4 text-center">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <i class="fa-solid fa-globe text-[9px]"></i> Global Pusat
                            </span>
                        </td>
                        <td class="py-2.5 px-4 text-slate-500 font-mono text-[10px] truncate max-w-xs" title="{{ $usr->wa_template }}">
                            {{ !empty($usr->wa_template) ? Str::limit($usr->wa_template, 80) : '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-6 text-center text-slate-400 italic">Belum ada data rekruter di tb_ai_setting_user.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function insertVariable(v) {
    const textarea = document.getElementById('waTemplateTextarea');
    if (textarea) {
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const text = textarea.value;
        textarea.value = text.substring(0, start) + v + text.substring(end);
        textarea.focus();
        textarea.selectionStart = textarea.selectionEnd = start + v.length;
    }
}

function testGeminiConnection() {
    const model = document.getElementById('geminiModelSelect').value;
    Swal.fire({
        title: 'Menguji Koneksi Gemini API...',
        text: 'Menghubungkan ke server Google Gemini AI (' + model + ')',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
            fetch("{{ route('aisetting.test_gemini') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ model: model })
            })
            .then(res => res.json())
            .then(data => {
                Swal.fire({
                    icon: 'success',
                    title: 'Koneksi Berhasil!',
                    text: data.message,
                    confirmButtonColor: '#0F52BA'
                });
            })
            .catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'Koneksi Gagal',
                    text: 'Tidak dapat menghubungi endpoint API.',
                    confirmButtonColor: '#0F52BA'
                });
            });
        }
    });
}

function testWaModal() {
    Swal.fire({
        title: 'Kirim Uji Coba WhatsApp',
        input: 'text',
        inputLabel: 'Masukkan Nomor WhatsApp Tujuan (Format: 08xx / 628xx)',
        inputValue: '081234567890',
        showCancelButton: true,
        confirmButtonText: 'Kirim Pesan Uji Coba',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#059669',
        inputValidator: (value) => {
            if (!value) {
                return 'Nomor WhatsApp tidak boleh kosong!';
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Mengirim Pesan...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                    fetch("{{ route('aisetting.test_wa') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ test_phone: result.value })
                    })
                    .then(res => res.json())
                    .then(data => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Terkirim!',
                            text: data.message,
                            confirmButtonColor: '#059669'
                        });
                    })
                    .catch(err => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Mengirim',
                            text: 'Terjadi kesalahan pada gateway WA.',
                            confirmButtonColor: '#059669'
                        });
                    });
                }
            });
        }
    });
}

function togglePasswordVisibility(inputId, btn) {
    const input = document.getElementById(inputId);
    if (!input) return;
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        if (icon) {
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        }
    } else {
        input.type = 'password';
        if (icon) {
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
}

function testOpenrouterConnection() {
    const key = document.getElementById('openrouterKeyInput') ? document.getElementById('openrouterKeyInput').value : '';
    const model = document.getElementById('openrouterModelSelect') ? document.getElementById('openrouterModelSelect').value : 'nvidia/nemotron-3-ultra-550b-a55b:free';

    Swal.fire({
        title: 'Menguji Koneksi OpenRouter...',
        text: 'Menghubungkan ke API OpenRouter (' + model + ') dengan reasoning enabled...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
            fetch("{{ route('aisetting.test_openrouter') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ key: key, model: model })
            })
            .then(res => res.json().then(data => ({ status: res.status, ok: res.ok, data: data })))
            .then(res => {
                if (res.ok && res.data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Koneksi OpenRouter Berhasil!',
                        html: '<p class="text-sm">' + res.data.message + '</p>' + (res.data.reply ? '<p class="text-xs font-mono text-slate-500 mt-2 bg-slate-100 p-2 rounded">Balasan: ' + res.data.reply + '</p>' : ''),
                        confirmButtonColor: '#7c3aed'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Koneksi OpenRouter Gagal',
                        text: res.data.message || 'Tidak dapat terhubung ke OpenRouter.',
                        confirmButtonColor: '#7c3aed'
                    });
                }
            })
            .catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'Koneksi Gagal',
                    text: 'Terjadi kendala jaringan saat menghubungi OpenRouter.',
                    confirmButtonColor: '#7c3aed'
                });
            });
        }
    });
}

function testSumopodConnection() {
    const key = document.getElementById('sumopodKeyInput') ? document.getElementById('sumopodKeyInput').value : '';
    const model = document.getElementById('sumopodModelSelect') ? document.getElementById('sumopodModelSelect').value : 'gpt-4o-mini';

    Swal.fire({
        title: 'Menguji Koneksi Sumopod...',
        text: 'Menghubungkan ke API Sumopod (' + model + ')...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
            fetch("{{ route('aisetting.test_sumopod') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ key: key, model: model })
            })
            .then(res => res.json().then(data => ({ status: res.status, ok: res.ok, data: data })))
            .then(res => {
                if (res.ok && res.data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Koneksi Sumopod Berhasil!',
                        html: '<p class="text-sm">' + res.data.message + '</p>' + (res.data.reply ? '<p class="text-xs font-mono text-slate-500 mt-2 bg-slate-100 p-2 rounded">Balasan: ' + res.data.reply + '</p>' : ''),
                        confirmButtonColor: '#d97706'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Koneksi Sumopod Gagal',
                        text: res.data.message || 'Tidak dapat terhubung ke Sumopod.',
                        confirmButtonColor: '#d97706'
                    });
                }
            })
            .catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'Koneksi Gagal',
                    text: 'Terjadi kendala jaringan saat menghubungi Sumopod.',
                    confirmButtonColor: '#d97706'
                });
            });
        }
    });
}

function deleteModel(type, model) {
    Swal.fire({
        title: 'Hapus Model dari Daftar?',
        html: `Apakah Anda yakin ingin menghapus model <b class="font-mono text-primary">${model}</b> dari daftar pilihan?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e11d48',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('deleteModelType').value = type;
            document.getElementById('deleteModelName').value = model;
            document.getElementById('deleteModelForm').submit();
        }
    });
}

function deleteExpiredKey(key) {
    Swal.fire({
        title: 'Hapus Token Expired?',
        text: 'Token ini akan dihapus dari daftar expired. Jika sudah diperbaiki di Google Cloud, Anda dapat mendaftarkannya kembali.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e11d48',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('expiredKeyInput').value = key;
            document.getElementById('deleteExpiredKeyForm').submit();
        }
    });
}
</script>

<form id="deleteExpiredKeyForm" action="{{ route('aisetting.remove_expired_key') }}" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="key" id="expiredKeyInput">
</form>

<form id="deleteModelForm" action="{{ route('aisetting.remove_model') }}" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="type" id="deleteModelType">
    <input type="hidden" name="model" id="deleteModelName">
</form>
@endsection

