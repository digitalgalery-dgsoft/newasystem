<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalasi ASystem - Support System ESA Groups</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .glass-panel {
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.4);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-900 via-slate-800 to-indigo-950 py-12 px-4 relative overflow-hidden">
    <!-- Ambient Lighting Background -->
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-primary-500/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-3xl relative z-10" x-data="{ 
        step: 1, 
        maxStep: 3, 
        dbType: 'sqlite',
        isSubmitting: false,
        allReqsOk: {{ $allRequirementsMet ? 'true' : 'false' }}
    }">
        <div class="glass-panel rounded-3xl shadow-2xl overflow-hidden border border-white/20">
            
            <!-- HEADER WITH STEPPER -->
            <div class="bg-gradient-to-r from-blue-700 via-indigo-700 to-primary-700 px-8 py-8 text-white relative">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md text-[11px] font-bold tracking-wider uppercase mb-2 text-blue-100">
                            <i class="fa-solid fa-server text-xs"></i>
                            <span>Production Installer Wizard</span>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-black tracking-tight">Instalasi ASystem Portal</h1>
                        <p class="text-blue-100 text-xs mt-1">Selesaikan 3 langkah mudah untuk menyiapkan sistem di server Anda.</p>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center text-2xl font-black shadow-inner">
                        AS
                    </div>
                </div>

                <!-- Step Indicators -->
                <div class="flex items-center justify-between mt-8 relative max-w-xl mx-auto">
                    <div class="absolute left-0 top-1/2 -translate-y-1/2 w-full h-1 bg-white/20 rounded-full"></div>
                    <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-white rounded-full transition-all duration-500" 
                         :style="'width: ' + ((step - 1) / (maxStep - 1)) * 100 + '%'"></div>
                    
                    <!-- Step 1 Indicator -->
                    <div class="relative flex flex-col items-center gap-1.5 cursor-pointer" @click="step = 1">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-xs transition-all border-2"
                             :class="step >= 1 ? 'bg-white text-indigo-700 border-white shadow-lg' : 'bg-indigo-900 text-blue-200 border-indigo-700'">
                            <span>1</span>
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-blue-100">Prasyarat</span>
                    </div>

                    <!-- Step 2 Indicator -->
                    <div class="relative flex flex-col items-center gap-1.5 cursor-pointer" @click="if(allReqsOk) step = 2">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-xs transition-all border-2"
                             :class="step >= 2 ? 'bg-white text-indigo-700 border-white shadow-lg' : 'bg-indigo-900 text-blue-200 border-indigo-700'">
                            <span>2</span>
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-blue-100">Database</span>
                    </div>

                    <!-- Step 3 Indicator -->
                    <div class="relative flex flex-col items-center gap-1.5 cursor-pointer" @click="if(allReqsOk) step = 3">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-xs transition-all border-2"
                             :class="step >= 3 ? 'bg-white text-indigo-700 border-white shadow-lg' : 'bg-indigo-900 text-blue-200 border-indigo-700'">
                            <span>3</span>
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-blue-100">Admin</span>
                    </div>
                </div>
            </div>

            <!-- FORM BODY -->
            <form action="{{ route('install.process') }}" method="POST" class="p-8 space-y-6" @submit="isSubmitting = true">
                @csrf

                @if(session('error'))
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-start gap-3">
                    <i class="fa-solid fa-triangle-exclamation text-rose-500 text-base mt-0.5"></i>
                    <div>
                        <span class="font-bold block mb-0.5">Terjadi Kesalahan:</span>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
                @endif

                @if($errors->any())
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold">
                    <span class="font-bold block mb-1">Periksa isian formulir:</span>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <!-- ========================================================= -->
                <!-- STEP 1: SYSTEM REQUIREMENTS & APP URL                     -->
                <!-- ========================================================= -->
                <div x-show="step === 1" class="space-y-6">
                    <div>
                        <h3 class="text-base font-black text-slate-800">1. Pemeriksaan Kebutuhan Server</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Pastikan ekstensi PHP dan hak akses berkas telah memenuhi standar sistem.</p>
                    </div>

                    <!-- Requirements Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($requirements as $name => $met)
                        <div class="flex items-center justify-between p-3 rounded-xl border {{ $met ? 'bg-emerald-50/60 border-emerald-200 text-emerald-900' : 'bg-rose-50/60 border-rose-200 text-rose-900' }}">
                            <span class="text-xs font-semibold">{{ $name }}</span>
                            @if($met)
                                <span class="w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center text-xs">
                                    <i class="fa-solid fa-check"></i>
                                </span>
                            @else
                                <span class="w-6 h-6 rounded-full bg-rose-500 text-white flex items-center justify-center text-xs">
                                    <i class="fa-solid fa-xmark"></i>
                                </span>
                            @endif
                        </div>
                        @endforeach
                    </div>

                    <!-- App URL -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Alamat Domain / URL Aplikasi (APP_URL)</label>
                        <input type="url" name="app_url" value="{{ old('app_url', url('/')) }}" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-indigo-500 focus:bg-white outline-none">
                        <span class="text-[11px] text-slate-400 mt-1 block">Contoh: <code>https://asystem.dgsoft.web.id</code> atau <code>http://localhost:8000</code></span>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-slate-100">
                        <button type="button" @click="step = 2" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition-all shadow-md flex items-center gap-2" :disabled="!allReqsOk">
                            <span>Lanjut ke Database</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <!-- ========================================================= -->
                <!-- STEP 2: DATABASE CONFIGURATION                            -->
                <!-- ========================================================= -->
                <div x-show="step === 2" class="space-y-6" style="display: none;">
                    <div>
                        <h3 class="text-base font-black text-slate-800">2. Konfigurasi Basis Data (Database)</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Pilih arsitektur database SQLite (Bawaan Standar) atau MySQL Server.</p>
                    </div>

                    <!-- Database Type Selector -->
                    <div class="grid grid-cols-2 gap-4">
                        <label class="cursor-pointer border-2 rounded-2xl p-4 flex flex-col items-center text-center transition-all"
                               :class="dbType === 'sqlite' ? 'border-indigo-600 bg-indigo-50/50' : 'border-slate-200 hover:border-slate-300'">
                            <input type="radio" name="db_connection" value="sqlite" x-model="dbType" class="hidden">
                            <i class="fa-solid fa-database text-2xl mb-2" :class="dbType === 'sqlite' ? 'text-indigo-600' : 'text-slate-400'"></i>
                            <span class="text-xs font-bold text-slate-800">SQLite (Rekomendasi)</span>
                            <span class="text-[10px] text-slate-400 mt-0.5">Cepat, portabel, tanpa server MySQL terpisah</span>
                        </label>

                        <label class="cursor-pointer border-2 rounded-2xl p-4 flex flex-col items-center text-center transition-all"
                               :class="dbType === 'mysql' ? 'border-indigo-600 bg-indigo-50/50' : 'border-slate-200 hover:border-slate-300'">
                            <input type="radio" name="db_connection" value="mysql" x-model="dbType" class="hidden">
                            <i class="fa-solid fa-server text-2xl mb-2" :class="dbType === 'mysql' ? 'text-indigo-600' : 'text-slate-400'"></i>
                            <span class="text-xs font-bold text-slate-800">MySQL Server</span>
                            <span class="text-[10px] text-slate-400 mt-0.5">Dedicated database server untuk skala multi-cluster</span>
                        </label>
                    </div>

                    <!-- SQLite Config Details -->
                    <div x-show="dbType === 'sqlite'" class="p-4 bg-slate-50 border border-slate-200 rounded-2xl space-y-2 text-xs text-slate-600">
                        <span class="font-bold text-slate-800 block">Nama Database SQLite:</span>
                        <input type="text" name="sqlite_database" value="asystem_interview" class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2 text-xs font-mono font-semibold text-slate-800">
                        <span class="text-[11px] text-slate-400 block">File SQLite akan disimpan di folder <code>database/</code> atau path root aplikasi.</span>
                    </div>

                    <!-- MySQL Config Details -->
                    <div x-show="dbType === 'mysql'" class="space-y-4" style="display: none;">
                        <div class="grid grid-cols-3 gap-3">
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-slate-700 mb-1">Host MySQL</label>
                                <input type="text" name="db_host" value="{{ old('db_host', '127.0.0.1') }}" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs font-mono">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Port</label>
                                <input type="text" name="db_port" value="{{ old('db_port', '3306') }}" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs font-mono">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Database</label>
                            <input type="text" name="db_name" value="{{ old('db_name', 'asystem_portal') }}" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs font-mono">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Username DB</label>
                                <input type="text" name="db_user" value="{{ old('db_user', 'root') }}" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs font-mono">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Password DB</label>
                                <input type="password" name="db_password" placeholder="Kosongkan bila tanpa password" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs font-mono">
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                        <button type="button" @click="step = 1" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-xs font-bold hover:bg-slate-50 transition-all flex items-center gap-2">
                            <i class="fa-solid fa-arrow-left"></i>
                            <span>Kembali</span>
                        </button>
                        <button type="button" @click="step = 3" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition-all shadow-md flex items-center gap-2">
                            <span>Lanjut ke Akun Admin</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <!-- ========================================================= -->
                <!-- STEP 3: ADMINISTRATOR ACCOUNT & EXECUTE                   -->
                <!-- ========================================================= -->
                <div x-show="step === 3" class="space-y-6" style="display: none;">
                    <div>
                        <h3 class="text-base font-black text-slate-800">3. Akun Super Administrator</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Akun ini akan digunakan sebagai Super Admin untuk mengelola seluruh modul dan rekruter.</p>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Lengkap Administrator</label>
                            <input type="text" name="admin_name" value="{{ old('admin_name', 'Administrator HR') }}" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-indigo-500 focus:bg-white outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Email Administrator (Untuk Login)</label>
                            <input type="email" name="admin_email" value="{{ old('admin_email', 'admin@asystem.co.id') }}" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-indigo-500 focus:bg-white outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Kata Sandi (Password)</label>
                            <input type="password" name="admin_password" value="password" required minlength="6" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-xs font-mono text-slate-800 focus:ring-2 focus:ring-indigo-500 focus:bg-white outline-none">
                            <span class="text-[11px] text-slate-400 mt-1 block">Minimal 6 karakter. Default: <code>password</code></span>
                        </div>
                    </div>

                    <!-- Summary Info Box -->
                    <div class="p-4 bg-amber-50 border border-amber-200 rounded-2xl flex items-start gap-3 text-xs text-amber-900">
                        <i class="fa-solid fa-circle-info text-amber-600 text-base mt-0.5"></i>
                        <div>
                            <span class="font-bold block mb-0.5">Informasi Proses Instalasi:</span>
                            <span>Sistem akan memvalidasi koneksi database, mengeksekusi migrasi skema tabel, dan membuat akun Super Admin. Proses ini membutuhkan waktu beberapa detik.</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                        <button type="button" @click="step = 2" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-xs font-bold hover:bg-slate-50 transition-all flex items-center gap-2">
                            <i class="fa-solid fa-arrow-left"></i>
                            <span>Kembali</span>
                        </button>
                        <button type="submit" class="px-8 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black transition-all shadow-lg shadow-emerald-600/30 flex items-center gap-2" :disabled="isSubmitting">
                            <i class="fa-solid fa-rocket" x-show="!isSubmitting"></i>
                            <i class="fa-solid fa-spinner fa-spin" x-show="isSubmitting" style="display: none;"></i>
                            <span x-text="isSubmitting ? 'Memproses Instalasi...' : 'Pasang & Jalankan Aplikasi'"></span>
                        </button>
                    </div>
                </div>

            </form>
        </div>

        <p class="text-center text-xs text-slate-400 mt-6 font-medium">
            &copy; 2026 PT Arina Multikarya - ASystem Support System ESA Groups. Rebuilt on Laravel 12.
        </p>
    </div>
</body>
</html>
