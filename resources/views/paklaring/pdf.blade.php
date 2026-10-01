<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Keterangan Kerja - {{ $paklaring->nama_lengkap }} ({{ $paklaring->kode_validasi }})</title>
    <style>
        @page {
            margin-top: 12mm;
            margin-bottom: 18mm;
            margin-left: 20mm;
            margin-right: 20mm;
            footer: html_veklaringFooter;
        }
        body {
            font-family: 'times', 'Times New Roman', serif;
            font-size: 10.5pt;
            color: #111827;
            line-height: 1.5;
        }
        .header-kop {
            text-align: center;
            border-bottom: 2.5px solid #111827;
            padding-bottom: 6px;
            margin-bottom: 14px;
        }
        .header-kop img {
            max-width: 100%;
            height: auto;
            max-height: 80px;
            margin: 0 auto;
        }
        .title-block {
            text-align: center;
            margin-top: 14px;
            margin-bottom: 18px;
        }
        .title-block h1 {
            font-size: 13.5pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin: 0;
            padding: 0;
        }
        .title-block .doc-no {
            font-size: 9.5pt;
            font-weight: bold;
            margin-top: 4px;
            font-family: monospace;
            color: #1f2937;
        }
        .section-text {
            margin-bottom: 8px;
            font-size: 10.5pt;
        }
        .data-table {
            width: 100%;
            margin-left: 18px;
            margin-bottom: 14px;
            border-collapse: collapse;
        }
        .data-table td {
            padding: 2px 0;
            vertical-align: top;
            font-size: 10pt;
            line-height: 1.45;
        }
        .sign-block {
            margin-top: 16px;
            font-size: 10.5pt;
            line-height: 1.4;
            page-break-inside: avoid;
        }
        .footer-cert {
            border-top: 1px solid #cbd5e1;
            padding-top: 5px;
            font-size: 7.5pt;
            color: #64748b;
            font-family: 'Helvetica', 'Arial', sans-serif;
        }
    </style>
</head>
<body>

    <!-- KOP SURAT RESMI ENTITAS (Selaras dengan Surat Peringatan) -->
    <div class="header-kop">
        @if(!empty($kopBase64))
            <img src="{{ $kopBase64 }}" alt="Kop Surat Resmi {{ $entityUpper }}">
        @elseif(!empty($kopUrl))
            <img src="{{ $kopUrl }}" alt="Kop Surat Resmi {{ $entityUpper }}">
        @else
            <div style="padding: 4px 0;">
                <h2 style="font-size: 14pt; font-weight: bold; text-transform: uppercase; margin: 0;">{{ $entityUpper }}</h2>
                <div style="font-size: 8.5pt; color: #475569; margin-top: 2px; font-family: 'Helvetica', 'Arial', sans-serif;">
                    Gedung Arina, Jl. Rajawali No. 18 Surabaya &bull; Operational Management ESA Groups
                </div>
            </div>
        @endif
    </div>

    <!-- JUDUL SURAT -->
    <div class="title-block">
        <h1>SURAT KETERANGAN</h1>
        @if(!empty($paklaring->nomor_ref))
            <div class="doc-no">Nomor: {{ $paklaring->nomor_ref }}</div>
        @endif
    </div>

    <!-- BAGIAN PIHAK PERTAMA (Yang bertanda tangan) -->
    <div class="section-text">
        Yang bertanda tangan dibawah ini :
    </div>
    <table class="data-table">
        <tr>
            <td style="width: 80px;">NAMA</td>
            <td style="width: 15px; text-align: center;">:</td>
            <td><strong>Nurul Yuliastuti</strong></td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td style="text-align: center;">:</td>
            <td>Jl.Rajawali No.18 Surabaya</td>
        </tr>
        <tr>
            <td>Jabatan</td>
            <td style="text-align: center;">:</td>
            <td>Head HRD</td>
        </tr>
    </table>

    <!-- BAGIAN PIHAK KEDUA (Karyawan) -->
    <div class="section-text">
        Menerangkan dengan sebenarnya bahwa :
    </div>
    <table class="data-table">
        <tr>
            <td style="width: 80px;">Nama</td>
            <td style="width: 15px; text-align: center;">:</td>
            <td>{{ $paklaring->nama_lengkap }}</td>
        </tr>
        <tr>
            <td>NIK</td>
            <td style="text-align: center;">:</td>
            <td style="font-family: monospace;">{{ $paklaring->nik }}</td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td style="text-align: center;">:</td>
            <td>{{ $paklaring->alamat ?: '-' }}</td>
        </tr>
        <tr>
            <td>Bagian</td>
            <td style="text-align: center;">:</td>
            <td style="text-transform: uppercase;">{{ $paklaring->jabatan }}</td>
        </tr>
    </table>

    <!-- PERNYATAAN RIWAYAT BEKERJA -->
    <div style="margin-bottom: 12px; font-size: 10.5pt; line-height: 1.5;">
        Karyawan diatas Pernah Bekerja di <strong>{{ $entityUpper }}</strong>
    </div>
    <div style="margin-bottom: 16px; font-size: 10.5pt; line-height: 1.5;">
        Sejak : <strong>{{ $tglMasukStr }} s/d {{ $tglKeluarStr }}</strong>
    </div>

    <!-- PARAGRAF PENUTUP -->
    <div style="margin-bottom: 24px; font-size: 10.5pt; line-height: 1.5;">
        Demikian Surat Keterangan dibuat untuk dapat dipergunakan sebagaimana mestinya
    </div>

    <!-- BLOK PENANDATANGAN & VERIFIKASI QR CODE -->
    <div class="sign-block">
        <div style="margin-bottom: 2px;">Surabaya, {{ $tanggalTerbitStr }}</div>
        <div style="margin-bottom: 8px;">{{ $entityTitle }}</div>

        <!-- QR Code & Nama Penandatangan -->
        <div style="margin-top: 6px; margin-bottom: 4px;">
            <img src="{{ $qrImageSrc }}" alt="QR Code" style="width: 105px; height: 105px; display: block;">
        </div>
        <div style="margin-top: 4px;">
            <strong style="text-decoration: underline; font-size: 11pt;">Nurul Yuliastuti</strong>
        </div>
    </div>

    <!-- FOOTER VERIFIKASI DIGITAL -->
    <htmlpagefooter name="veklaringFooter">
        <div class="footer-cert">
            {{ $entityTitle }} Digital Certificate &bull; Kode Validasi: <strong style="font-family: monospace;">{{ $paklaring->kode_validasi }}</strong>
        </div>
    </htmlpagefooter>
    <sethtmlpagefooter name="veklaringFooter" value="on" />

</body>
</html>
