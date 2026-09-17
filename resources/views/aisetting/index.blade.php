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

    <!-- MAIN SETTING FORM -->
    <form action="{{ route('aisetting.update') }}" method="POST" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            <!-- LEFT: GEMINI & FALLBACK AI CONFIGURATION (7 COLS) -->
            <div class="lg:col-span-7 space-y-6">
                <!-- 1. Google Gemini Configuration -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-5">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-blue-50 text-primary flex items-center justify-center font-bold text-sm">
                                <i class="fa-solid fa-brain"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-800">Google Gemini AI Configuration</h3>
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
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Model Gemini Aktif</label>
                            <select name="gemini_model" id="geminiModelSelect" class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-primary-100 focus:border-primary outline-none">
                                <option value="gemini-2.5-flash" {{ $setting->gemini_model === 'gemini-2.5-flash' ? 'selected' : '' }}>Google Gemini 2.5 Flash (Sangat Cepat & Direkomendasikan)</option>
                                <option value="gemini-1.5-flash" {{ $setting->gemini_model === 'gemini-1.5-flash' ? 'selected' : '' }}>Google Gemini 1.5 Flash (Cepat & Ringan)</option>
                                <option value="gemini-1.5-pro" {{ $setting->gemini_model === 'gemini-1.5-pro' ? 'selected' : '' }}>Google Gemini 1.5 Pro (Analisis Mendalam & Kompleks)</option>
                            </select>
                        </div>

                        <!-- Gemini API Keys Pool -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-xs font-bold text-slate-700">Daftar API Key Gemini (1 Baris = 1 Key)</label>
                                <span class="text-[11px] font-bold text-slate-500">{{ count($setting->keys_list) }} Kunci Terdaftar</span>
                            </div>
                            <textarea name="gemini_keys" rows="4" class="w-full font-mono bg-slate-50 border border-slate-300 rounded-xl p-3 text-xs text-slate-800 focus:ring-2 focus:ring-primary-100 focus:border-primary outline-none" placeholder="Masukkan satu Google Gemini API Key per baris...">{{ $setting->gemini_keys }}</textarea>
                            <p class="text-[11px] text-slate-400 mt-1 italic">* Sistem akan otomatis merotasi API Key jika kuota harian pada salah satu key telah tercapai.</p>
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

                <!-- 2. Fallback AI Provider (Sumopod / OpenAI) -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4">
                    <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                        <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-800">Fallback Provider (Sumopod / OpenAI)</h3>
                            <p class="text-xs text-slate-500">Cadangan otomatis jika seluruh kuota Google Gemini mengalami limitasi</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Sumopod / OpenAI API Key</label>
                            <input type="password" name="sumopod_key" value="{{ $setting->sumopod_key }}" placeholder="sk-..." class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs font-mono text-slate-800 focus:ring-2 focus:ring-primary-100 focus:border-primary outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Model Cadangan</label>
                            <select name="sumopod_model" class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-primary-100 focus:border-primary outline-none">
                                <option value="gpt-4o-mini" {{ $setting->sumopod_model === 'gpt-4o-mini' ? 'selected' : '' }}>GPT-4o Mini (Hemat & Responsif)</option>
                                <option value="gpt-4o" {{ $setting->sumopod_model === 'gpt-4o' ? 'selected' : '' }}>GPT-4o (Kemampuan Penalaran Tinggi)</option>
                            </select>
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
                            <input type="password" name="wa_api_key" value="{{ $setting->wa_api_key }}" placeholder="Token API Gateway" class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2 text-xs font-mono text-slate-800 focus:ring-2 focus:ring-primary-100 focus:border-primary outline-none">
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
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 flex items-center justify-between">
                    <span class="text-xs text-slate-500">Perubahan akan langsung diterapkan ke seluruh pemrosesan CV daring.</span>
                    <button type="submit" class="btn-att-primary text-xs font-bold px-6 py-2.5 flex items-center gap-2 shadow-md shadow-primary/20">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Simpan Konfigurasi</span>
                    </button>
                </div>
            </div>

        </div>
    </form>
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
</script>
@endsection
