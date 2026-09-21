<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Pendaftaran Walk-in Interview - ESA Groups</title>
    
    <!-- Google Fonts: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
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
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        * {
            font-family: 'Outfit', sans-serif;
        }
        body {
            background-color: #f1f5f9;
        }
        /* Custom styling for inputs */
        .form-pill-input {
            background-color: #f1f5f9;
            border-radius: 9999px;
            padding-top: 0.85rem;
            padding-bottom: 0.85rem;
            font-size: 0.875rem;
            color: #1e293b;
            transition: all 0.2s ease;
        }
        .form-pill-input:focus {
            background-color: #ffffff;
            box-shadow: 0 0 0 2px #2563eb;
            outline: none;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between py-6 px-4 sm:px-6">

    <!-- Header Logo & Branding -->
    <header class="w-full max-w-md mx-auto text-center mb-3">
        <div class="inline-flex items-center justify-center gap-2.5 mb-1.5">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-700 to-blue-500 text-white flex items-center justify-center text-lg font-black shadow-md shadow-blue-500/20">
                <i class="fa-solid fa-briefcase"></i>
            </div>
            <div class="text-left">
                <h1 class="text-base font-extrabold text-slate-800 tracking-tight leading-none">ESA GROUPS</h1>
                <p class="text-[10px] text-slate-400 font-medium tracking-wide uppercase mt-0.5">Recruitment & Career Portal</p>
            </div>
        </div>
    </header>

    <!-- Main Card Container (Sesuai Desain Gambar 2 Sistem Lama) -->
    <main class="w-full max-w-md mx-auto">
        
        <!-- Notifikasi Sukses Pendaftaran -->
        @if(session('success'))
        <div class="mb-4 p-5 bg-emerald-500 text-white rounded-3xl shadow-lg shadow-emerald-500/20 text-center animate-fade-in">
            <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center mx-auto mb-2 text-2xl">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <h3 class="font-bold text-base">Pendaftaran Berhasil!</h3>
            <p class="text-xs text-emerald-50 mt-1 leading-relaxed">
                {{ session('success') }}
            </p>
            <div class="mt-3">
                <a href="{{ route('interview.walk.create') }}" class="inline-block px-4 py-1.5 rounded-full bg-white text-emerald-800 text-xs font-bold shadow-xs hover:bg-emerald-50 transition">
                    Daftar Lagi / Kandidat Lain
                </a>
            </div>
        </div>
        @endif

        <!-- Error Validation Alerts -->
        @if(isset($errors) && $errors->any())
        <div class="mb-4 p-4 bg-rose-50 border border-rose-200 rounded-2xl text-rose-800 text-xs space-y-1 shadow-xs">
            <div class="font-bold flex items-center gap-1.5">
                <i class="fa-solid fa-circle-exclamation text-rose-600"></i>
                <span>Mohon periksa kembali formulir Anda:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5 text-[11px] text-rose-700 pl-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Card Form Putih Bersih Sesuai Gambar 2 -->
        <div class="bg-white rounded-3xl shadow-xl p-6 sm:p-8 border border-slate-100">
            
            <!-- Judul Formulir Sesuai Gambar 2 -->
            <div class="text-center mb-6">
                <h2 class="text-base font-semibold text-slate-700 tracking-tight">
                    Isi formulir untuk mendaftar.
                </h2>
            </div>

            <!-- Form Pendaftaran Mandiri -->
            <form action="{{ route('interview.walk.store') }}" method="POST" class="space-y-4">
                @csrf

                <!-- 1. NIK / No. KTP -->
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-regular fa-id-card text-base"></i>
                    </div>
                    <input type="text" 
                           name="nik" 
                           value="{{ old('nik') }}"
                           required 
                           maxlength="16" 
                           minlength="16" 
                           pattern="[0-9]{16}"
                           inputmode="numeric"
                           placeholder="NIK / No. KTP" 
                           class="w-full pl-11 pr-4 form-pill-input border-0 placeholder-slate-400"
                           title="Masukkan 16 digit NIK sesuai KTP">
                </div>

                <!-- 2. Nama Lengkap -->
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-regular fa-user text-base"></i>
                    </div>
                    <input type="text" 
                           name="full_name" 
                           value="{{ old('full_name') }}"
                           required 
                           placeholder="Nama Lengkap" 
                           class="w-full pl-11 pr-4 form-pill-input border-0 placeholder-slate-400">
                </div>

                <!-- 3. Tanggal Lahir (dd/mm/tttt) -->
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-regular fa-calendar text-base"></i>
                    </div>
                    <input type="date" 
                           name="birth_date" 
                           value="{{ old('birth_date') }}"
                           required 
                           class="w-full pl-11 pr-4 form-pill-input border-0 text-slate-700">
                </div>

                <!-- 4. Pendidikan Terakhir -->
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-graduation-cap text-base"></i>
                    </div>
                    <select name="education" required class="w-full pl-11 pr-10 form-pill-input border-0 appearance-none text-slate-700">
                        <option value="" disabled {{ old('education') ? '' : 'selected' }}>Pendidikan Terakhir</option>
                        @foreach($dropdownEducation as $edu)
                            <option value="{{ $edu }}" {{ old('education') == $edu ? 'selected' : '' }}>{{ $edu }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </div>
                </div>

                <!-- 5. Jabatan Dilamar -->
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-briefcase text-base"></i>
                    </div>
                    <select name="applied_job" required class="w-full pl-11 pr-10 form-pill-input border-0 appearance-none text-slate-700">
                        <option value="" disabled {{ old('applied_job') ? '' : 'selected' }}>Jabatan Dilamar</option>
                        @foreach($dropdownJobs as $job)
                            <option value="{{ $job }}" {{ old('applied_job') == $job ? 'selected' : '' }}>{{ $job }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </div>
                </div>

                <!-- 6. Area Interview -->
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-map-location-dot text-base"></i>
                    </div>
                    <select name="area" required class="w-full pl-11 pr-10 form-pill-input border-0 appearance-none text-slate-700">
                        <option value="" disabled {{ old('area') ? '' : 'selected' }}>Area Interview</option>
                        @foreach($dropdownAreas as $area)
                            <option value="{{ $area }}" {{ old('area') == $area ? 'selected' : '' }}>{{ $area }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </div>
                </div>

                <!-- 7. Informasi Lowongan -->
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-bullhorn text-base"></i>
                    </div>
                    <select name="info" required class="w-full pl-11 pr-10 form-pill-input border-0 appearance-none text-slate-700">
                        <option value="" disabled {{ old('info') ? '' : 'selected' }}>Informasi Lowongan</option>
                        @foreach($dropdownInfo as $inf)
                            <option value="{{ $inf }}" {{ old('info') == $inf ? 'selected' : '' }}>{{ $inf }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </div>
                </div>

                <!-- 8. Jenis Undangan Interview -->
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-regular fa-envelope text-base"></i>
                    </div>
                    <select name="undangan" required class="w-full pl-11 pr-10 form-pill-input border-0 appearance-none text-slate-700">
                        <option value="" disabled {{ old('undangan') ? '' : 'selected' }}>Jenis Undangan Interview</option>
                        @foreach($dropdownUndangan as $und)
                            <option value="{{ $und }}" {{ old('undangan') == $und ? 'selected' : '' }}>{{ $und }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </div>
                </div>

                <!-- Tombol Submit: Register (Biru Pill Besar Sesuai Gambar 2) -->
                <div class="pt-3">
                    <button type="submit" class="w-full py-3.5 px-6 rounded-full bg-[#2563eb] hover:bg-blue-700 text-white font-bold text-sm tracking-wide shadow-lg shadow-blue-500/25 active:scale-[0.98] transition cursor-pointer">
                        Register
                    </button>
                </div>
            </form>

        </div>

        <!-- Catatan & Informasi untuk Pelamar -->
        <div class="mt-4 text-center text-xs text-slate-400">
            <p>Pastikan NIK dan data yang dimasukkan sesuai dengan identitas KTP asli Anda.</p>
        </div>
    </main>

    <!-- Footer -->
    <footer class="w-full max-w-md mx-auto text-center mt-6 text-[11px] text-slate-400">
        &copy; {{ date('Y') }} ESA Groups &bull; All rights reserved.
    </footer>

</body>
</html>
