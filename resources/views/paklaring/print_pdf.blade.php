<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Keterangan Kerja - {{ $paklaring->nama_lengkap }} ({{ $paklaring->kode_validasi }})</title>
    
    <!-- Google Fonts: Times New Roman / Serif & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Tinos:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Tinos', 'Times New Roman', Times, serif;
            color: #111827;
            background-color: #f1f5f9;
        }

        .font-sans-ui {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        .document-paper {
            width: 210mm;
            min-height: 297mm;
            padding: 15mm 20mm 15mm 20mm;
            background: #ffffff;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            margin: 0 auto;
            position: relative;
            box-sizing: border-box;
        }

        .header-kop {
            text-align: center;
            border-bottom: 2.5px solid #111827;
            padding-bottom: 8px;
            margin-bottom: 18px;
        }

        .header-kop img {
            max-width: 100%;
            height: auto;
            max-height: 80px;
            display: block;
            margin: 0 auto;
        }

        /* Watermark Kode Validasi Diagonal di Tengah Surat */
        .watermark-container {
            position: absolute;
            top: 52%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100%;
            text-align: center;
            pointer-events: none;
            user-select: none;
            z-index: 0;
            overflow: hidden;
        }

        .watermark-text {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: 68pt;
            font-weight: 800;
            letter-spacing: 0.2em;
            color: rgba(100, 116, 139, 0.08); /* slate-500 dengan transparansi 8% */
            transform: rotate(-35deg);
            white-space: nowrap;
            text-transform: uppercase;
            line-height: 1;
            display: inline-block;
        }

        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .no-print {
                display: none !important;
            }

            .document-paper {
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
                padding: 10mm 15mm 10mm 15mm !important;
                width: 100% !important;
                min-height: auto !important;
                page-break-after: avoid !important;
                page-break-inside: avoid !important;
            }

            .watermark-text {
                color: rgba(71, 85, 105, 0.08) !important;
            }

            @page {
                size: A4 portrait;
                margin: 10mm 15mm 10mm 15mm;
            }
        }
    </style>
</head>
<body class="py-6 sm:py-10">

    <!-- Top Action Bar (Hanya tampil di browser, otomatis hilang saat print) -->
    <div class="max-w-[210mm] mx-auto mb-6 px-4 no-print flex items-center justify-between gap-4 font-sans-ui">
        <a href="{{ route('paklaring.show', $paklaring->id) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white text-slate-700 font-bold text-xs border border-slate-200 hover:bg-slate-50 transition-all shadow-xs">
            <i class="fa-solid fa-arrow-left text-xs"></i>
            <span>Kembali ke Detail</span>
        </a>

        <div class="flex items-center gap-2.5">
            <div class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-800 text-[11px] font-bold border border-emerald-200">
                <i class="fa-solid fa-certificate text-emerald-600"></i>
                <span>Format Resmi Veklaring</span>
            </div>
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md transition-all cursor-pointer">
                <i class="fa-solid fa-print text-xs"></i>
                <span>Cetak / Unduh PDF</span>
            </button>
        </div>
    </div>

    <!-- Lembar Dokumen Resmi A4 -->
    <div class="document-paper flex flex-col justify-between text-[11pt] leading-[1.6]">
        
        <!-- Watermark Kode Validasi Diagonal di Tengah Surat -->
        <div class="watermark-container" aria-hidden="true">
            <div class="watermark-text">{{ $paklaring->kode_validasi }}</div>
        </div>

        <div style="position: relative; z-index: 1;">
            <!-- KOP SURAT RESMI ENTITAS (Selaras dengan Surat Peringatan) -->
            <div class="header-kop">
                @if(!empty($kopBase64))
                    <img src="{{ $kopBase64 }}" alt="Kop Surat Resmi {{ $entityUpper }}">
                @elseif(!empty($kopUrl))
                    <img src="{{ $kopUrl }}" alt="Kop Surat Resmi {{ $entityUpper }}">
                @else
                    <div class="py-2">
                        <h2 class="text-xl font-bold uppercase tracking-wider text-slate-900 m-0">{{ $entityUpper }}</h2>
                        <div class="text-[9pt] text-slate-600 font-sans-ui mt-0.5">
                            Gedung Arina, Jl. Rajawali No. 18 Surabaya &bull; Operational Management ESA Groups
                        </div>
                    </div>
                @endif
            </div>

            <!-- JUDUL SURAT -->
            <div class="text-center my-6">
                <h1 class="text-[14pt] font-bold uppercase underline underline-offset-4 tracking-wider inline-block">
                    SURAT KETERANGAN
                </h1>
                @if(!empty($paklaring->nomor_ref))
                    <div class="text-[9.5pt] font-semibold text-slate-700 mt-1 font-mono tracking-wide">
                        Nomor: {{ $paklaring->nomor_ref }}
                    </div>
                @endif
            </div>

            <!-- BAGIAN PIHAK PERTAMA (Yang bertanda tangan) -->
            <div class="mb-5">
                <p class="mb-2 text-slate-900">
                    Yang bertanda tangan dibawah ini :
                </p>
                <table class="w-full text-[10.5pt] ml-6 sm:ml-8" style="border-collapse: collapse;">
                    <tbody>
                        <tr>
                            <td style="width: 95px; vertical-align: top; padding: 2px 0;">NAMA</td>
                            <td style="width: 15px; vertical-align: top; text-align: center; padding: 2px 0;">:</td>
                            <td style="vertical-align: top; padding: 2px 0;">
                                <strong>Nurul Yuliastuti</strong>
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top; padding: 2px 0;">Alamat</td>
                            <td style="vertical-align: top; text-align: center; padding: 2px 0;">:</td>
                            <td style="vertical-align: top; padding: 2px 0;">
                                Jl.Rajawali No.18 Surabaya
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top; padding: 2px 0;">Jabatan</td>
                            <td style="vertical-align: top; text-align: center; padding: 2px 0;">:</td>
                            <td style="vertical-align: top; padding: 2px 0;">
                                Head HRD
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- BAGIAN PIHAK KEDUA (Karyawan) -->
            <div class="mb-6">
                <p class="mb-2 text-slate-900">
                    Menerangkan dengan sebenarnya bahwa :
                </p>
                <table class="w-full text-[10.5pt] ml-6 sm:ml-8" style="border-collapse: collapse;">
                    <tbody>
                        <tr>
                            <td style="width: 95px; vertical-align: top; padding: 2px 0;">Nama</td>
                            <td style="width: 15px; vertical-align: top; text-align: center; padding: 2px 0;">:</td>
                            <td style="vertical-align: top; padding: 2px 0;">
                                {{ $paklaring->nama_lengkap }}
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top; padding: 2px 0;">NIK</td>
                            <td style="vertical-align: top; text-align: center; padding: 2px 0;">:</td>
                            <td style="vertical-align: top; padding: 2px 0; font-family: monospace;">
                                {{ $paklaring->nik }}
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top; padding: 2px 0;">Alamat</td>
                            <td style="vertical-align: top; text-align: center; padding: 2px 0;">:</td>
                            <td style="vertical-align: top; padding: 2px 0; line-height: 1.45;">
                                {{ $paklaring->alamat ?: '-' }}
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top; padding: 2px 0;">Bagian</td>
                            <td style="vertical-align: top; text-align: center; padding: 2px 0;">:</td>
                            <td style="vertical-align: top; padding: 2px 0; text-transform: uppercase;">
                                {{ $paklaring->jabatan }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- PERNYATAAN RIWAYAT BEKERJA -->
            <div class="mb-5 space-y-3.5 text-slate-900">
                <p>
                    Karyawan diatas Pernah Bekerja di <strong>{{ $entityUpper }}</strong>
                </p>
                <p>
                    Sejak : <strong>{{ $tglMasukStr }} s/d {{ $tglKeluarStr }}</strong>
                </p>
            </div>

            <!-- PARAGRAF PENUTUP -->
            <div class="mb-8 text-slate-900">
                <p>
                    Demikian Surat Keterangan dibuat untuk dapat dipergunakan sebagaimana mestinya
                </p>
            </div>

            <!-- BLOK PENANDATANGAN & VERIFIKASI QR CODE -->
            <div class="mt-8 text-slate-900" style="page-break-inside: avoid;">
                <div class="space-y-0.5 mb-2.5">
                    <p class="text-[10.5pt] m-0">Surabaya, {{ $tanggalTerbitStr }}</p>
                    <p class="text-[10.5pt] m-0">{{ $entityTitle }}</p>
                </div>

                <!-- QR Code & Nama Penandatangan (Persis Posisi Gambar Terlampir) -->
                <div class="space-y-2 mt-3">
                    <div style="width: 125px; height: 125px; padding: 4px; border: 1px solid #cbd5e1; background: #ffffff; border-radius: 4px;">
                        <img src="{{ $qrImageSrc }}" alt="QR Code Verifikasi Keaslian Surat" style="width: 100%; height: 100%; object-fit: contain; display: block;">
                    </div>
                    <div class="pt-1">
                        <strong style="text-decoration: underline; text-underline-offset: 3px; font-size: 11pt; display: inline-block;">
                            Nurul Yuliastuti
                        </strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- FOOTER VERIFIKASI DIGITAL (Halus di bagian bawah kertas) -->
        <div class="pt-5 mt-6 border-t border-slate-200/80 text-[7.5pt] text-slate-400 font-sans-ui" style="position: relative; z-index: 1; page-break-inside: avoid;">
            <div>
                {{ $entityTitle }} Digital Certificate &bull; Kode Validasi: <span class="font-mono font-bold text-slate-600">{{ $paklaring->kode_validasi }}</span>
            </div>
        </div>

    </div>

</body>
</html>
