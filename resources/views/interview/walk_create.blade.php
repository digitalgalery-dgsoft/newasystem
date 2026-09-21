<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Register Kandidat - ESA Groups</title>
    
    <!-- Google Fonts: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Font Awesome 6 Pro & Boxicons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css">

    <!-- TomSelect Searchable Dropdown CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">

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
                            900: '#0f172a',
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
            background-color: #e2e8f0;
        }

        /* Pill Inputs Styling matching Old System */
        .form-pill-input {
            background-color: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 9999px;
            padding: 0.85rem 1.25rem 0.85rem 3rem;
            font-size: 0.875rem;
            color: #1e293b;
            transition: all 0.2s ease;
            width: 100%;
        }
        .form-pill-input:focus {
            background-color: #ffffff;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
            outline: none;
        }

        /* Textarea with Rounded 2XL corners */
        .form-textarea-input {
            background-color: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 1.25rem;
            padding: 0.85rem 1.25rem 0.85rem 3rem;
            font-size: 0.875rem;
            color: #1e293b;
            transition: all 0.2s ease;
            width: 100%;
            min-height: 85px;
            resize: vertical;
        }
        .form-textarea-input:focus {
            background-color: #ffffff;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
            outline: none;
        }

        /* TomSelect Custom Styling to match Pill Inputs */
        .ts-wrapper {
            width: 100%;
        }
        .ts-control {
            background-color: #f1f5f9 !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 9999px !important;
            padding: 0.75rem 1.25rem 0.75rem 3rem !important;
            font-size: 0.875rem !important;
            color: #1e293b !important;
            box-shadow: none !important;
            min-height: 48px !important;
            display: flex !important;
            align-items: center !important;
        }
        .ts-control.focus {
            background-color: #ffffff !important;
            border-color: #2563eb !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15) !important;
        }
        .ts-control input {
            font-size: 0.875rem !important;
            color: #1e293b !important;
        }
        .ts-control input::placeholder {
            color: #94a3b8 !important;
        }
        .ts-dropdown {
            border-radius: 1.25rem !important;
            border: 1px solid #cbd5e1 !important;
            box-shadow: 0 12px 30px -4px rgba(15, 23, 42, 0.15) !important;
            padding: 0.5rem !important;
            margin-top: 6px !important;
            z-index: 999 !important;
            max-height: 240px !important;
        }
        .ts-dropdown .option {
            padding: 0.6rem 1rem !important;
            border-radius: 0.75rem !important;
            font-size: 0.875rem !important;
            color: #334155 !important;
            cursor: pointer !important;
            transition: background 0.15s ease !important;
        }
        .ts-dropdown .option.active, .ts-dropdown .option:hover {
            background-color: #eff6ff !important;
            color: #1d4ed8 !important;
            font-weight: 600 !important;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between py-6 px-3 sm:px-6">

    <main class="w-full max-w-lg mx-auto">
        
        <!-- Notifikasi Sukses Pendaftaran -->
        @if(session('success'))
        <div class="mb-5 p-6 bg-gradient-to-br from-emerald-500 to-teal-600 text-white rounded-3xl shadow-xl text-center animate-fade-in">
            <div class="w-14 h-14 rounded-full bg-white/20 flex items-center justify-center mx-auto mb-3 text-3xl shadow-inner">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <h3 class="font-bold text-lg">Pendaftaran Berhasil!</h3>
            <p class="text-xs text-emerald-50 mt-1.5 leading-relaxed">
                {{ session('success') }}
            </p>
            <div class="mt-4 flex items-center justify-center gap-2">
                <a href="{{ route('interview.walk.create') }}" class="px-5 py-2 rounded-full bg-white text-emerald-800 text-xs font-bold shadow-md hover:bg-emerald-50 transition">
                    + Daftar Kandidat Lain
                </a>
                <a href="{{ route('login') }}" class="px-4 py-2 rounded-full bg-emerald-700/60 text-white text-xs font-bold hover:bg-emerald-700 transition">
                    Ke Halaman Login
                </a>
            </div>
        </div>
        @endif

        <!-- Error Validation Alerts -->
        @if(isset($errors) && $errors->any())
        <div class="mb-5 p-5 bg-rose-50 border border-rose-200 rounded-3xl text-rose-800 text-xs space-y-1.5 shadow-sm">
            <div class="font-bold flex items-center gap-2 text-sm text-rose-700">
                <i class="fa-solid fa-circle-exclamation text-base"></i>
                <span>Terdapat kolom formulir yang perlu dilengkapi:</span>
            </div>
            <ul class="list-disc list-inside space-y-1 text-xs text-rose-600 pl-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Main Card Container Sesuai Gambar 1 & Gambar 2 -->
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-slate-200/80">
            
            <!-- Top Header Banner (Sesuai Gambar 1) -->
            <div class="bg-gradient-to-r from-[#0f172a] via-[#1e3a8a] to-[#2563eb] py-6 px-6 text-center text-white relative">
                <h1 class="text-xl sm:text-2xl font-black uppercase tracking-wider leading-tight">
                    FORM REGISTER KANDIDAT
                </h1>
                <p class="text-xs text-blue-100/90 font-medium mt-1">
                    Silahkan Lengkapi Data & Berkas
                </p>
            </div>

            <!-- Form Body -->
            <form action="{{ route('interview.walk.store') }}" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-5" id="formWalkin">
                @csrf

                <!-- Section Foto Profil (Sesuai Gambar 1) -->
                <div class="text-center pt-2 pb-2">
                    <label class="block text-xs font-bold text-slate-700 mb-3 uppercase tracking-wider">
                        Foto Profil
                    </label>
                    
                    <div class="relative inline-block">
                        <!-- Circle Avatar Preview -->
                        <div class="w-28 h-28 sm:w-32 sm:h-32 rounded-full border-4 border-slate-100 shadow-md bg-slate-100 flex items-center justify-center overflow-hidden mx-auto" id="avatarContainer">
                            <img id="avatarPreview" src="" alt="Preview Foto" class="w-full h-full object-cover hidden">
                            <i id="avatarIcon" class="fa-solid fa-camera text-3xl text-slate-400"></i>
                        </div>

                        <!-- Tombol Pilih Foto -->
                        <div class="mt-3">
                            <button type="button" onclick="document.getElementById('photoInput').click()" class="px-5 py-1.5 rounded-full bg-[#334155] hover:bg-[#1e293b] text-white text-xs font-bold shadow-sm transition active:scale-95 cursor-pointer">
                                Pilih Foto
                            </button>
                            <input type="file" name="foto_profil" id="photoInput" accept="image/*" class="hidden" onchange="previewAvatar(this)">
                        </div>
                    </div>
                </div>

                <!-- 1. NIK / No. KTP -->
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 z-10">
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
                           class="form-pill-input"
                           title="Masukkan 16 digit NIK sesuai KTP">
                </div>

                <!-- 2. Nama Lengkap -->
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 z-10">
                        <i class="fa-regular fa-user text-base"></i>
                    </div>
                    <input type="text" 
                           name="full_name" 
                           value="{{ old('full_name') }}"
                           required 
                           placeholder="Nama Lengkap" 
                           class="form-pill-input">
                </div>

                <!-- 3. Tanggal Lahir (Dengan Label Profesional & Deteksi Usia Otomatis) -->
                <div>
                    <label for="birthDateInput" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5 px-3 flex items-center justify-between">
                        <span class="flex items-center gap-1.5">
                            <i class="fa-regular fa-calendar-days text-blue-600"></i>
                            <span>Tanggal Lahir</span>
                            <span class="text-rose-500 font-bold">*</span>
                        </span>
                        <span id="ageBadge" class="hidden text-[11px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 rounded-full transition-all">
                            Usia: <span id="ageText" class="font-bold"></span> th
                        </span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-blue-500 z-10">
                            <i class="fa-regular fa-calendar-check text-base"></i>
                        </div>
                        <input type="date" 
                               id="birthDateInput"
                               name="birth_date" 
                               value="{{ old('birth_date') }}"
                               min="1950-01-01"
                               max="{{ date('Y-m-d', strtotime('-15 years')) }}"
                               onclick="this.showPicker && this.showPicker()"
                               required 
                               class="form-pill-input font-medium text-slate-700 cursor-pointer hover:border-blue-300 transition"
                               title="Pilih tanggal lahir anda">
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-slate-400 mt-1 px-3">
                        <span class="flex items-center gap-1">
                            <i class="fa-solid fa-circle-info text-blue-500 text-[10px]"></i>
                            Format: Hari / Bulan / Tahun
                        </span>
                        <span class="text-slate-400">Min. 17 tahun</span>
                    </div>
                </div>

                <!-- 4. 2-Column: Tinggi (cm) & Berat (kg) (Sesuai Gambar 1) -->
                <div class="grid grid-cols-2 gap-3">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 z-10">
                            <i class="fa-solid fa-ruler-vertical text-base"></i>
                        </div>
                        <input type="number" 
                               name="height" 
                               value="{{ old('height') }}"
                               min="50" 
                               max="250" 
                               placeholder="Tinggi (cm)" 
                               class="form-pill-input">
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 z-10">
                            <i class="fa-solid fa-weight-scale text-base"></i>
                        </div>
                        <input type="number" 
                               name="weight" 
                               value="{{ old('weight') }}"
                               min="20" 
                               max="250" 
                               placeholder="Berat (kg)" 
                               class="form-pill-input">
                    </div>
                </div>

                <!-- 5. Alamat KTP (Sesuai Gambar 1) -->
                <div class="relative">
                    <div class="absolute top-3.5 left-0 pl-4 pointer-events-none text-slate-400 z-10">
                        <i class="fa-solid fa-location-dot text-base"></i>
                    </div>
                    <textarea name="address_ktp" 
                              rows="2" 
                              id="addressKtp"
                              placeholder="Alamat KTP" 
                              class="form-textarea-input">{{ old('address_ktp') }}</textarea>
                </div>

                <!-- 6. Alamat Domisili (Sesuai Gambar 1) -->
                <div class="relative">
                    <div class="absolute top-3.5 left-0 pl-4 pointer-events-none text-slate-400 z-10">
                        <i class="fa-solid fa-location-dot text-base"></i>
                    </div>
                    <textarea name="address_domicile" 
                              rows="2" 
                              id="addressDomicile"
                              placeholder="Alamat Domisili" 
                              class="form-textarea-input">{{ old('address_domicile') }}</textarea>
                    
                    <div class="flex items-center justify-end mt-1 px-2">
                        <button type="button" onclick="copyKtpToDomisili()" class="text-[11px] font-semibold text-blue-600 hover:text-blue-800 transition flex items-center gap-1">
                            <i class="fa-regular fa-copy text-[10px]"></i>
                            <span>Sama dengan alamat KTP</span>
                        </button>
                    </div>
                </div>

                <!-- 7. Nomor WhatsApp (Sesuai Gambar 1) -->
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 z-10">
                        <i class="fa-brands fa-whatsapp text-lg text-emerald-600"></i>
                    </div>
                    <input type="tel" 
                           name="whatsapp" 
                           value="{{ old('whatsapp') }}"
                           required 
                           placeholder="Nomor WhatsApp" 
                           class="form-pill-input">
                </div>

                <!-- 8. Pendidikan Terakhir (Searchable Dropdown) (Sesuai Gambar 2) -->
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 z-10">
                        <i class="fa-solid fa-graduation-cap text-base"></i>
                    </div>
                    <select name="education" id="selectEducation" required>
                        <option value="" disabled selected>Pendidikan Terakhir</option>
                        @foreach($dropdownEducation as $edu)
                            <option value="{{ $edu }}" {{ old('education') == $edu ? 'selected' : '' }}>{{ $edu }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- 9. Jabatan Dilamar (Searchable Dropdown) (Sesuai Gambar 2) -->
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 z-10">
                        <i class="fa-solid fa-briefcase text-base"></i>
                    </div>
                    <select name="applied_job" id="selectJob" required>
                        <option value="" disabled selected>Jabatan Dilamar</option>
                        @foreach($dropdownJobs as $job)
                            <option value="{{ $job }}" {{ old('applied_job') == $job ? 'selected' : '' }}>{{ $job }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- 10. Area (Searchable Dropdown sesuai tb_area) (Sesuai Gambar 2) -->
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 z-10">
                        <i class="fa-solid fa-map-location-dot text-base"></i>
                    </div>
                    <select name="area" id="selectArea" required>
                        <option value="" disabled selected>Area</option>
                        @foreach($areas as $ar)
                            <option value="{{ $ar->area }}" {{ old('area') == $ar->area ? 'selected' : '' }}>
                                {{ $ar->area }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 11. Kota Asal (Searchable Dropdown sesuai region tb_kota) (Sesuai Gambar 2) -->
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 z-10">
                        <i class="fa-solid fa-city text-base"></i>
                    </div>
                    <select name="kota_asal" id="selectKota">
                        <option value="" disabled selected>Pilih Kota Asal</option>
                        @if(old('kota_asal'))
                            <option value="{{ old('kota_asal') }}" selected>{{ old('kota_asal') }}</option>
                        @endif
                    </select>
                </div>

                <!-- 12. Pilih Nama AS / Rekrutor (Searchable Dropdown karyawan inhouse) (Sesuai Gambar 2) -->
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 z-10">
                        <i class="fa-solid fa-user-tie text-base"></i>
                    </div>
                    <select name="nama_as" id="selectAs">
                        <option value="" disabled selected>Pilih Nama AS</option>
                        @if(old('nama_as'))
                            <option value="{{ old('nama_as') }}" selected>{{ old('nama_as') }}</option>
                        @endif
                    </select>
                </div>

                <!-- 13. Motivasi Kerja (Sesuai Gambar 2) -->
                <div class="relative">
                    <div class="absolute top-3.5 left-0 pl-4 pointer-events-none text-slate-400 z-10">
                        <i class="fa-regular fa-comment-dots text-base"></i>
                    </div>
                    <textarea name="work_motivation" 
                              rows="2" 
                              placeholder="Tuliskan motivasi kerja anda..." 
                              class="form-textarea-input">{{ old('work_motivation') }}</textarea>
                </div>

                <!-- 14. Kelebihan Anda (Sesuai Gambar 2) -->
                <div class="relative">
                    <div class="absolute top-3.5 left-0 pl-4 pointer-events-none text-slate-400 z-10">
                        <i class="fa-solid fa-user-plus text-base"></i>
                    </div>
                    <textarea name="strengths" 
                              rows="2" 
                              placeholder="Tuliskan kelebihan anda..." 
                              class="form-textarea-input">{{ old('strengths') }}</textarea>
                </div>

                <!-- 15. 2-Column: Info Lowongan & Jenis Undangan (Sesuai Gambar 2) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="relative">
                        <select name="info" id="selectInfo">
                            <option value="" disabled selected>Info Lowongan</option>
                            @foreach($dropdownInfo as $inf)
                                <option value="{{ $inf }}" {{ old('info') == $inf ? 'selected' : '' }}>{{ $inf }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="relative">
                        <select name="undangan" id="selectUndangan">
                            <option value="" disabled selected>Jenis Undangan</option>
                            @foreach($dropdownUndangan as $und)
                                <option value="{{ $und }}" {{ old('undangan') == $und ? 'selected' : '' }}>{{ $und }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- 16. Box Upload File CV (Image/PDF) (Sesuai Gambar 2) -->
                <div class="p-4 bg-[#eff6ff] border border-[#bfdbfe] rounded-2xl">
                    <label class="block text-xs font-bold text-[#1e40af] uppercase tracking-wider mb-2.5">
                        UPLOAD FILE CV (IMAGE)
                    </label>
                    <div class="flex items-center gap-3">
                        <button type="button" onclick="document.getElementById('cvInput').click()" class="px-4 py-2 rounded-full bg-[#2563eb] hover:bg-blue-700 text-white text-xs font-bold shadow-sm transition active:scale-95 cursor-pointer">
                            Pilih File
                        </button>
                        <span id="cvFileName" class="text-xs text-slate-500 truncate max-w-[240px]">
                            Tidak ada file yang dipilih
                        </span>
                        <input type="file" name="file_cv" id="cvInput" accept="image/*,.pdf" class="hidden" onchange="updateCvFileName(this)">
                    </div>
                </div>

                <!-- 17. Tombol Submit: Daftar Sekarang (Sesuai Gambar 2) -->
                <div class="pt-2">
                    <button type="submit" class="w-full py-4 px-6 rounded-full bg-[#2563eb] hover:bg-blue-700 text-white font-bold text-sm tracking-wide shadow-xl shadow-blue-500/25 active:scale-[0.98] transition cursor-pointer">
                        Daftar Sekarang
                    </button>
                </div>

                <!-- 18. Link Login Disini (Sesuai Gambar 2) -->
                <div class="text-center pt-2 pb-1">
                    <p class="text-xs text-slate-500 font-medium">
                        Sudah Pernah Register? 
                        <a href="{{ route('login') }}" class="font-bold text-blue-600 hover:text-blue-800 transition">
                            Login Disini
                        </a>
                    </p>
                </div>
            </form>

        </div>

        <!-- Footer -->
        <footer class="w-full text-center mt-6 text-[11px] text-slate-500">
            &copy; {{ date('Y') }} ESA Groups &bull; All rights reserved.
        </footer>
    </main>

    <!-- TomSelect JS -->
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    <!-- Data Master untuk Dependensi Cascading Dropdown -->
    <script>
        const areaRegions = @json($areaRegions);
        const citiesByRegion = @json($citiesByRegion);
        const asListByArea = @json($asListByArea);

        let tsArea, tsKota, tsAs;

        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi TomSelect untuk Area
            tsArea = new TomSelect('#selectArea', {
                create: false,
                placeholder: 'Pilih Area',
                onChange: function(val) {
                    onAreaChanged(val);
                }
            });

            // Inisialisasi TomSelect untuk Kota Asal
            tsKota = new TomSelect('#selectKota', {
                create: false,
                placeholder: 'Pilih Kota Asal'
            });

            // Inisialisasi TomSelect untuk Nama AS (Menampilkan Nama & Jabatan)
            tsAs = new TomSelect('#selectAs', {
                create: false,
                placeholder: 'Pilih Nama AS / Rekrutor',
                searchField: ['name', 'text', 'value', 'jabatan'],
                render: {
                    option: function(data, escape) {
                        const jabBadge = data.jabatan ? `<span class="text-[11px] font-semibold tracking-wide uppercase px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200 shrink-0 ml-2">${escape(data.jabatan)}</span>` : '';
                        return `<div class="flex items-center justify-between py-1.5 px-2 w-full">
                            <span class="font-medium text-slate-800 text-sm">${escape(data.name || data.text)}</span>
                            ${jabBadge}
                        </div>`;
                    },
                    item: function(data, escape) {
                        const jabText = data.jabatan ? ` <span class="text-xs font-semibold text-blue-600">(${escape(data.jabatan)})</span>` : '';
                        return `<div>${escape(data.name || data.text)}${jabText}</div>`;
                    }
                }
            });

            // Inisialisasi Dropdown Lainnya
            new TomSelect('#selectEducation', { create: false, placeholder: 'Pendidikan Terakhir' });
            new TomSelect('#selectJob', { create: false, placeholder: 'Jabatan Dilamar' });
            new TomSelect('#selectInfo', { create: false, placeholder: 'Info Lowongan' });
            new TomSelect('#selectUndangan', { create: false, placeholder: 'Jenis Undangan' });

            // Jika ada old value area (misal setelah submit validasi gagal), trigger cascade
            const currentArea = tsArea.getValue();
            if (currentArea) {
                onAreaChanged(currentArea, "{{ old('kota_asal') }}", "{{ old('nama_as') }}");
            }
        });

        // Hitung Usia Otomatis dari Tanggal Lahir
        const birthInput = document.getElementById('birthDateInput');
        const ageBadge = document.getElementById('ageBadge');

        function calculateAge() {
            if (!birthInput || !birthInput.value) {
                if (ageBadge) ageBadge.classList.add('hidden');
                return;
            }
            const birthDate = new Date(birthInput.value);
            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            const m = today.getMonth() - birthDate.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }

            if (age >= 15 && age <= 65) {
                ageBadge.className = 'text-[11px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 rounded-full flex items-center gap-1 transition-all';
                ageBadge.innerHTML = `<i class="fa-solid fa-circle-check text-[10px]"></i> Usia: <span class="font-bold">${age}</span> th`;
                ageBadge.classList.remove('hidden');
            } else if (age < 15) {
                ageBadge.className = 'text-[11px] font-semibold text-amber-700 bg-amber-50 border border-amber-200 px-2.5 py-0.5 rounded-full flex items-center gap-1 transition-all';
                ageBadge.innerHTML = `<i class="fa-solid fa-triangle-exclamation text-[10px]"></i> Usia ${age} th (Cek tahun lahir)`;
                ageBadge.classList.remove('hidden');
            } else {
                ageBadge.classList.add('hidden');
            }
        }

        if (birthInput) {
            birthInput.addEventListener('change', calculateAge);
            birthInput.addEventListener('input', calculateAge);
            if (birthInput.value) calculateAge();
        }

        // Cascading Dropdown: saat Area dipilih
        function onAreaChanged(selectedArea, prefillKota = null, prefillAs = null) {
            if (!selectedArea) return;

            // 1. Dapatkan Region dari Area yang dipilih
            const region = areaRegions[selectedArea];

            // Update Opsi Kota Asal berdasarkan Region tb_kota
            tsKota.clear();
            tsKota.clearOptions();
            if (region && citiesByRegion[region]) {
                citiesByRegion[region].forEach(city => {
                    tsKota.addOption({ value: city, text: city });
                });
                if (prefillKota) {
                    tsKota.setValue(prefillKota);
                }
                tsKota.refreshOptions(false);
            }

            // 2. Update Opsi Nama AS / Rekrutor berdasarkan Area
            tsAs.clear();
            tsAs.clearOptions();
            const asList = asListByArea[selectedArea] || [
                { name: 'ARO ' + selectedArea.toUpperCase(), jabatan: 'ARO', display: 'ARO ' + selectedArea.toUpperCase() + ' (ARO)' }
            ];
            asList.forEach(item => {
                const val = typeof item === 'object' ? item.name : item;
                const jab = typeof item === 'object' ? (item.jabatan || 'AS') : '';
                const txt = typeof item === 'object' ? item.display : item;
                tsAs.addOption({ value: val, text: txt, name: val, jabatan: jab });
            });
            if (prefillAs) {
                tsAs.setValue(prefillAs);
            }
            tsAs.refreshOptions(false);
        }

        // Preview Foto Profil
        function previewAvatar(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.getElementById('avatarPreview');
                    const icon = document.getElementById('avatarIcon');
                    img.src = e.target.result;
                    img.classList.remove('hidden');
                    icon.classList.add('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Update Label File CV
        function updateCvFileName(input) {
            const label = document.getElementById('cvFileName');
            if (input.files && input.files[0]) {
                label.innerText = input.files[0].name;
                label.classList.remove('text-slate-500');
                label.classList.add('text-blue-700', 'font-medium');
            } else {
                label.innerText = 'Tidak ada file yang dipilih';
                label.classList.remove('text-blue-700', 'font-medium');
                label.classList.add('text-slate-500');
            }
        }

        // Salin Alamat KTP ke Domisili
        function copyKtpToDomisili() {
            const ktp = document.getElementById('addressKtp').value;
            document.getElementById('addressDomicile').value = ktp;
        }
    </script>
</body>
</html>
