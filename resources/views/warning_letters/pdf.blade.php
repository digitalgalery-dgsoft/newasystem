<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Peringatan - {{ $letter->nama_karyawan }}</title>
    <style>
        @page {
            margin-top: 10mm;
            margin-bottom: 22mm;
            margin-left: 18mm;
            margin-right: 18mm;
            footer: html_documentFooter;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10pt;
            color: #1a202c;
            line-height: 1.45;
        }
        .header-kop {
            text-align: center;
            border-bottom: 2.5px solid #1a202c;
            padding-bottom: 8px;
            margin-bottom: 14px;
        }
        .header-kop img {
            max-width: 100%;
            height: auto;
            max-height: 85px;
        }
        .title-block {
            text-align: center;
            margin-bottom: 14px;
        }
        .title-block h1 {
            font-size: 13pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 0;
            padding: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .title-block .doc-no {
            font-size: 9.5pt;
            margin-top: 3px;
            font-weight: bold;
        }
        .section-text {
            margin-bottom: 8px;
            text-align: justify;
        }
        .data-table {
            width: 100%;
            margin-bottom: 10px;
            border-collapse: collapse;
        }
        .data-table td {
            padding: 2.5px 4px;
            vertical-align: top;
            font-size: 9.5pt;
        }
        .pasal-box {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-left: 3px solid #0f52ba;
            padding: 8px 10px;
            margin: 8px 0 12px 0;
            font-size: 9pt;
            line-height: 1.4;
        }
        .violations-table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0 12px 0;
        }
        .violations-table th, .violations-table td {
            border: 1px solid #cbd5e1;
            padding: 5px 7px;
            font-size: 9pt;
            vertical-align: top;
        }
        .violations-table th {
            background-color: #f1f5f9;
            font-weight: bold;
            text-align: left;
        }
        .sign-table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            page-break-inside: avoid;
        }
        .sign-table td {
            width: 33.33%;
            text-align: center;
            vertical-align: top;
            padding: 4px;
            font-size: 9pt;
        }
        .sign-space {
            height: 60px;
        }
        .footer-qr {
            width: 100%;
            border-top: 1px solid #e2e8f0;
            margin-top: 15px;
            padding-top: 6px;
            font-size: 7.5pt;
            color: #64748b;
        }
    </style>
</head>
<body>

    <!-- KOP SURAT RESMI ENTITAS -->
    <div class="header-kop">
        @if($kopBase64)
            <img src="{{ $kopBase64 }}" alt="Kop Surat Resmi {{ $letter->entity_full_name }}">
        @else
            <h2 style="margin:0; font-size:14pt; font-weight:bold;">{{ $letter->entity_full_name }}</h2>
            <div style="font-size:8pt; color:#475569; margin-top:2px;">
                Human Resources Department &bull; Operational Management ESA Groups
            </div>
        @endif
    </div>

    <!-- JUDUL SURAT & NOMOR RESMI -->
    <div class="title-block">
        <h1>SURAT PERINGATAN {{ strtoupper(str_replace('sp', ' ', $letter->tingkat_sp_roman)) }}</h1>
        <div class="doc-no">Nomor: {{ $letter->nomor_surat ?: 'PENDING-APPROVAL' }}</div>
    </div>

    <!-- PEMBUKA -->
    <div class="section-text">
        Surat Peringatan ini diterbitkan oleh Management <strong>{{ $letter->entity_full_name }}</strong> kepada karyawan di bawah ini:
    </div>

    <!-- TABEL IDENTITAS KARYAWAN -->
    <table class="data-table">
        <tr>
            <td style="width: 25%;">Nama Lengkap</td>
            <td style="width: 2%;">:</td>
            <td style="width: 73%;"><strong>{{ $letter->nama_karyawan }}</strong></td>
        </tr>
        <tr>
            <td>Nomor Induk Karyawan (NIK)</td>
            <td>:</td>
            <td>{{ $letter->nik }} @if($letter->nip) / NIP: {{ $letter->nip }} @endif</td>
        </tr>
        <tr>
            <td>Jabatan</td>
            <td>:</td>
            <td>{{ $letter->jabatan ?: '-' }}</td>
        </tr>
        <tr>
            <td>Area Penempatan / Kota</td>
            <td>:</td>
            <td>{{ $letter->area ?: '-' }} ({{ $letter->singkatan_area }})</td>
        </tr>
        <tr>
            <td>Mitra Kerja (Prinsiple)</td>
            <td>:</td>
            <td>{{ $letter->prinsiple ?: 'Internal ESA Groups' }}</td>
        </tr>
    </table>

    <!-- LANDASAN PASAL PERATURAN PERUSAHAAN -->
    <div class="section-text">
        Berdasarkan hasil evaluasi kedisiplinan dan investigasi atas laporan yang masuk, karyawan yang bersangkutan terbukti telah melakukan pelanggaran terhadap ketentuan disiplin kerja sebagaimana diatur dalam:
    </div>

    <div class="pasal-box">
        <strong>Dasar Hukum & Rujukan Ketentuan:</strong><br>
        {{ $letter->pasal_pelanggaran ?: 'Ketentuan Disiplin Kerja dan Tata Tertib Ketenagakerjaan Peraturan Perusahaan.' }}
    </div>

    <!-- BUTIR-BUTIR PELANGGARAN -->
    <div class="section-text">
        Adapun butir-butir pelanggaran yang menjadi dasar penerbitan Surat Peringatan ini adalah sebagai berikut:
    </div>

    <table class="violations-table">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">No</th>
                <th style="width: 18%;">Tgl Kejadian</th>
                <th style="width: 32%;">Uraian Pelanggaran</th>
                <th style="width: 45%;">Kronologi Peristiwa</th>
            </tr>
        </thead>
        <tbody>
            @foreach($letter->violations as $idx => $v)
            <tr>
                <td style="text-align: center; font-weight: bold;">{{ $idx + 1 }}</td>
                <td>{{ $v->tanggal_pelanggaran ? $v->tanggal_pelanggaran->format('d/m/Y') : '-' }}</td>
                <td><strong>{{ $v->pelanggaran }}</strong></td>
                <td>{{ $v->kronologi ?: '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- MASA BERLAKU & KOMITMEN -->
    <div class="section-text">
        Sehubungan dengan hal tersebut di atas, Management memberikan <strong>{{ $letter->tingkat_sp_label }}</strong> kepada yang bersangkutan dengan ketentuan:
    </div>

    <ol style="margin-top: 3px; margin-bottom: 8px; padding-left: 20px; font-size: 9.5pt; text-align: justify;">
        <li style="margin-bottom: 4px;">
            Surat Peringatan ini berlaku selama <strong>6 (enam) bulan</strong> terhitung sejak tanggal <strong>{{ $letter->tanggal_surat ? $letter->tanggal_surat->format('d F Y') : '-' }}</strong> sampai dengan tanggal <strong>{{ $letter->tanggal_expired ? $letter->tanggal_expired->format('d F Y') : '-' }}</strong>.
        </li>
        <li style="margin-bottom: 4px;">
            Selama masa berlakunya Surat Peringatan ini, karyawan yang bersangkutan wajib memperbaiki sikap kerja, mematuhi seluruh tata tertib, dan tidak mengulangi pelanggaran yang sama maupun pelanggaran lainnya.
        </li>
        @if(!empty($letter->tindakan_perbaikan))
        <li style="margin-bottom: 4px;">
            <strong>Tindakan perbaikan:</strong> {{ $letter->tindakan_perbaikan }}
        </li>
        @endif
        <li>
            Apabila dalam masa berlakunya Surat Peringatan ini yang bersangkutan kembali melakukan tindakan pelanggaran, maka Management berhak menjatuhkan sanksi yang lebih berat hingga Pemutusan Hubungan Kerja (PHK) sesuai ketentuan perundang-undangan ketenagakerjaan yang berlaku.
        </li>
    </ol>

    <div class="section-text" style="margin-top: 8px;">
        Demikian Surat Peringatan ini dibuat agar dapat diperhatikan, ditaati, dan dipergunakan sebagaimana mestinya.
    </div>

    <!-- TANGGAL PENETAPAN & TANDA TANGAN 3 PIHAK -->
    <div style="text-align: right; margin-top: 16px; margin-bottom: 10px; font-size: 9pt;">
        Ditetapkan di: {{ $letter->area ?: 'Surabaya' }}, {{ $letter->tanggal_surat ? $letter->tanggal_surat->format('d F Y') : date('d F Y') }}
    </div>

    <table class="sign-table">
        <tr>
            <td style="width: 33.33%;">
                Yang Menerima Peringatan,<br>
                <strong>Karyawan Bersangkutan</strong>
            </td>
            <td style="width: 33.33%;">
                Mengetahui & Mengusulkan,<br>
                <strong>Atasan Langsung / Pimpinan</strong>
            </td>
            <td style="width: 33.33%;">
                Menyetujui & Mengesahkan,<br>
                <strong>HRD Management</strong>
            </td>
        </tr>
        <tr>
            <td style="height: 65px; line-height: 65px;">&nbsp;</td>
            <td style="height: 65px; line-height: 65px;">&nbsp;</td>
            <td style="height: 65px; line-height: 65px;">&nbsp;</td>
        </tr>
        <tr>
            <td>
                <strong style="text-decoration: underline;">{{ $letter->nama_karyawan }}</strong><br>
                <span style="font-size: 8pt; color: #334155;">NIK: {{ $letter->nik }}</span>
            </td>
            <td>
                <strong style="text-decoration: underline;">{{ $letter->headApprover ? $letter->headApprover->name : ($letter->pimpinan_pembuat ?: ($letter->creator ? $letter->creator->name : '( Atasan Langsung )')) }}</strong><br>
                <span style="font-size: 8pt; color: #334155;">{{ $letter->headApprover ? ($letter->headApprover->job_title ?: 'Pimpinan / Atasan') : ($letter->creator ? ($letter->creator->job_title ?: 'Atasan Langsung') : 'Atasan Langsung') }}</span>
            </td>
            <td>
                <strong style="text-decoration: underline;">{{ $letter->approver ? $letter->approver->name : '( HRD Management )' }}</strong><br>
                <span style="font-size: 8pt; color: #334155;">{{ $letter->entity_full_name }}</span>
            </td>
        </tr>
    </table>

    <!-- FOOTER RESMI DOKUMEN (BARCODE & LABEL VERIFIKASI) DILETAKKAN SEBAGAI FOOTER HALAMAN -->
    <htmlpagefooter name="documentFooter">
        <table style="width: 100%; border-top: 1px solid #cbd5e1; padding-top: 4px; font-size: 7pt; color: #64748b;">
            <tr>
                <td style="width: 72%; vertical-align: middle;">
                    <strong style="color: #0f172a; font-size: 7.5pt;">ASystem Digital Verification &bull; {{ $letter->entity_full_name }}</strong><br>
                    Dokumen ini sah dan diterbitkan resmi secara digital melalui ASystem Cloud ESA Groups.<br>
                    No. Seri Digital: {{ md5($letter->id . $letter->nomor_surat . $letter->created_at) }} &bull; Waktu Cetak: {{ date('d/m/Y H:i:s') }}
                </td>
                <td style="width: 28%; text-align: right; vertical-align: middle;">
                    @if(class_exists(\Mpdf\QrCode\QrCode::class))
                        <barcode code="{{ $verifyUrl }}" type="QR" class="barcode" size="0.85" error="M" disableborder="1" />
                    @else
                        <barcode code="{{ $letter->nomor_surat ?: ('ID' . $letter->id) }}" type="C128A" size="0.85" height="0.65" />
                        <div style="font-size: 6.5pt; color: #475569; margin-top: 1px; font-family: monospace;">{{ $letter->nomor_surat }}</div>
                    @endif
                </td>
            </tr>
        </table>
    </htmlpagefooter>
    <sethtmlpagefooter name="documentFooter" value="on" />

</body>
</html>
