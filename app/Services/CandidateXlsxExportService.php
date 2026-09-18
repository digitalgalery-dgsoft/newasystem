<?php

namespace App\Services;

use ZipArchive;
use Exception;
use Carbon\Carbon;

class CandidateXlsxExportService
{
    /**
     * Generate an executive, styled Microsoft Excel (.xlsx) file for Job Portal Candidates.
     *
     * @param \Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Collection $candidates
     * @param array $meta (filters: start, end, kategori, status_kandidat, area, recruiter_name, etc.)
     * @return string Path to the temporary .xlsx file
     */
    public static function generateXlsx($candidates, array $meta = []): string
    {
        $tempDir = sys_get_temp_dir();
        $tempFile = tempnam($tempDir, 'cand_xlsx_') . '.xlsx';

        $zip = new ZipArchive();
        if ($zip->open($tempFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new Exception("Gagal membuat temporary file Excel (.xlsx).");
        }

        // 1. [Content_Types].xml
        $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
    <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
    <Default Extension="xml" ContentType="application/xml"/>
    <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
    <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
    <Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
</Types>';
        $zip->addFromString('[Content_Types].xml', $contentTypes);

        // 2. _rels/.rels
        $rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>';
        $zip->addFromString('_rels/.rels', $rels);

        // 3. xl/_rels/workbook.xml.rels
        $wbRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
    <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
</Relationships>';
        $zip->addFromString('xl/_rels/workbook.xml.rels', $wbRels);

        // 4. xl/workbook.xml
        $workbook = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
    <sheets>
        <sheet name="Pelamar Job Portal" sheetId="1" r:id="rId1"/>
    </sheets>
</workbook>';
        $zip->addFromString('xl/workbook.xml', $workbook);

        // 5. xl/styles.xml
        $styles = self::buildStylesXml();
        $zip->addFromString('xl/styles.xml', $styles);

        // 6. xl/worksheets/sheet1.xml
        $sheetXml = self::buildSheetXml($candidates, $meta);
        $zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);

        $zip->close();

        return $tempFile;
    }

    /**
     * Build rich OpenXML StyleSheet
     */
    protected static function buildStylesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
    <fonts count="8">
        <!-- 0: Regular 10pt Slate -->
        <font><sz val="10"/><name val="Segoe UI"/><color rgb="FF1E293B"/></font>
        <!-- 1: Bold 14pt Emerald Title -->
        <font><b/><sz val="14"/><name val="Segoe UI"/><color rgb="FF047857"/></font>
        <!-- 2: Bold 10pt White Header -->
        <font><b/><sz val="10"/><name val="Segoe UI"/><color rgb="FFFFFFFF"/></font>
        <!-- 3: Underline 10pt Blue Link -->
        <font><u/><sz val="10"/><name val="Segoe UI"/><color rgb="FF1D4ED8"/></font>
        <!-- 4: Subtitle 9pt Italic Slate -->
        <font><i/><sz val="9"/><name val="Segoe UI"/><color rgb="FF64748B"/></font>
        <!-- 5: Bold 10pt Green -->
        <font><b/><sz val="10"/><name val="Segoe UI"/><color rgb="FF059669"/></font>
        <!-- 6: Bold 10pt Amber -->
        <font><b/><sz val="10"/><name val="Segoe UI"/><color rgb="FFD97706"/></font>
        <!-- 7: Bold 10pt Rose -->
        <font><b/><sz val="10"/><name val="Segoe UI"/><color rgb="FFE11D48"/></font>
    </fonts>
    <fills count="7">
        <fill><patternFill patternType="none"/></fill>
        <fill><patternFill patternType="gray125"/></fill>
        <!-- 2: Header Dark Emerald -->
        <fill><patternFill patternType="solid"><fgColor rgb="FF065F46"/></patternFill></fill>
        <!-- 3: Zebra Light Slate -->
        <fill><patternFill patternType="solid"><fgColor rgb="FFF8FAFC"/></patternFill></fill>
        <!-- 4: Soft Green Badge -->
        <fill><patternFill patternType="solid"><fgColor rgb="FFECFDF5"/></patternFill></fill>
        <!-- 5: Soft Amber Badge -->
        <fill><patternFill patternType="solid"><fgColor rgb="FFFFFBEB"/></patternFill></fill>
        <!-- 6: Soft Rose Badge -->
        <fill><patternFill patternType="solid"><fgColor rgb="FFFFF1F2"/></patternFill></fill>
    </fills>
    <borders count="2">
        <border><left/><right/><top/><bottom/><diagonal/></border>
        <!-- 1: Thin border light gray -->
        <border>
            <left style="thin"><color rgb="FFE2E8F0"/></left>
            <right style="thin"><color rgb="FFE2E8F0"/></right>
            <top style="thin"><color rgb="FFE2E8F0"/></top>
            <bottom style="thin"><color rgb="FFE2E8F0"/></bottom>
        </border>
    </borders>
    <cellStyleXfs count="1">
        <xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>
    </cellStyleXfs>
    <cellXfs count="18">
        <!-- 0: Default -->
        <xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>
        <!-- 1: Title Banner (A1) -->
        <xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1"><alignment vertical="center"/></xf>
        <!-- 2: Subtitle Metadata (A2) -->
        <xf numFmtId="0" fontId="4" fillId="0" borderId="0" xfId="0" applyFont="1"><alignment vertical="center"/></xf>
        <!-- 3: Table Header Row (A4..R4) -->
        <xf numFmtId="0" fontId="2" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>
        <!-- 4: Data Regular Left -->
        <xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1"><alignment horizontal="left" vertical="center"/></xf>
        <!-- 5: Data Regular Center -->
        <xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1"><alignment horizontal="center" vertical="center"/></xf>
        <!-- 6: Data Zebra Left -->
        <xf numFmtId="0" fontId="0" fillId="3" borderId="1" xfId="0" applyFill="1" applyBorder="1"><alignment horizontal="left" vertical="center"/></xf>
        <!-- 7: Data Zebra Center -->
        <xf numFmtId="0" fontId="0" fillId="3" borderId="1" xfId="0" applyFill="1" applyBorder="1"><alignment horizontal="center" vertical="center"/></xf>
        <!-- 8: Link Regular Center -->
        <xf numFmtId="0" fontId="3" fillId="0" borderId="1" xfId="0" applyFont="1" applyBorder="1"><alignment horizontal="center" vertical="center"/></xf>
        <!-- 9: Link Zebra Center -->
        <xf numFmtId="0" fontId="3" fillId="3" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1"><alignment horizontal="center" vertical="center"/></xf>
        <!-- 10: Wrap Text Regular Left -->
        <xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1"><alignment horizontal="left" vertical="center" wrapText="1"/></xf>
        <!-- 11: Wrap Text Zebra Left -->
        <xf numFmtId="0" fontId="0" fillId="3" borderId="1" xfId="0" applyFill="1" applyBorder="1"><alignment horizontal="left" vertical="center" wrapText="1"/></xf>
        <!-- 12: Category Green Regular Center -->
        <xf numFmtId="0" fontId="5" fillId="4" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1"><alignment horizontal="center" vertical="center"/></xf>
        <!-- 13: Category Green Zebra Center -->
        <xf numFmtId="0" fontId="5" fillId="4" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1"><alignment horizontal="center" vertical="center"/></xf>
        <!-- 14: Category Yellow Regular Center -->
        <xf numFmtId="0" fontId="6" fillId="5" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1"><alignment horizontal="center" vertical="center"/></xf>
        <!-- 15: Category Yellow Zebra Center -->
        <xf numFmtId="0" fontId="6" fillId="5" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1"><alignment horizontal="center" vertical="center"/></xf>
        <!-- 16: Category Red Regular Center -->
        <xf numFmtId="0" fontId="7" fillId="6" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1"><alignment horizontal="center" vertical="center"/></xf>
        <!-- 17: Category Red Zebra Center -->
        <xf numFmtId="0" fontId="7" fillId="6" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1"><alignment horizontal="center" vertical="center"/></xf>
    </cellXfs>
</styleSheet>';
    }

    /**
     * Build Worksheet XML with headers, metadata, columns, and data rows
     */
    protected static function buildSheetXml($candidates, array $meta = []): string
    {
        $nowStr = Carbon::now('Asia/Jakarta')->translatedFormat('d F Y, H:i') . ' WIB';
        $totalCount = count($candidates);

        // Filter details text
        $filterParts = [];
        if (!empty($meta['start']) && !empty($meta['end'])) {
            $filterParts[] = 'Periode Daftar: ' . Carbon::parse($meta['start'])->format('d/m/Y') . ' s/d ' . Carbon::parse($meta['end'])->format('d/m/Y');
        } elseif (!empty($meta['start'])) {
            $filterParts[] = 'Mulai: ' . Carbon::parse($meta['start'])->format('d/m/Y');
        } elseif (!empty($meta['end'])) {
            $filterParts[] = 'Sampai: ' . Carbon::parse($meta['end'])->format('d/m/Y');
        } else {
            $filterParts[] = 'Periode: Semua Tanggal';
        }

        if (!empty($meta['kategori'])) {
            $filterParts[] = 'Kategori AI: ' . $meta['kategori'];
        }
        if (!empty($meta['status_kandidat'])) {
            $filterParts[] = 'Status: ' . $meta['status_kandidat'];
        }
        if (!empty($meta['area'])) {
            $filterParts[] = 'Area: ' . $meta['area'];
        }
        if (!empty($meta['recruiter_name'])) {
            $filterParts[] = 'Rekruter: ' . $meta['recruiter_name'];
        }

        $metaText = 'Diexport pada: ' . $nowStr . ' | ' . implode(' | ', $filterParts) . ' | Total Data: ' . number_format($totalCount) . ' Kandidat';

        // Column widths definition
        $cols = [
            1  => 6,   // NO
            2  => 18,  // TGL DAFTAR
            3  => 22,  // NIK (KTP)
            4  => 28,  // NAMA LENGKAP
            5  => 16,  // JENIS KELAMIN
            6  => 15,  // TGL LAHIR
            7  => 10,  // USIA
            8  => 18,  // PENDIDIKAN
            9  => 20,  // NO WHATSAPP / HP
            10 => 26,  // EMAIL
            11 => 30,  // POSISI DILAMAR
            12 => 20,  // AREA PENEMPATAN
            13 => 45,  // RINGKASAN PENGALAMAN KERJA
            14 => 15,  // KATEGORI AI
            15 => 12,  // AI SCORE
            16 => 16,  // STATUS KANDIDAT
            17 => 24,  // HASIL ANALISIS AI (PDF)
            18 => 24,  // REKRUTER / AS
        ];

        $colsXml = '<cols>';
        foreach ($cols as $colIdx => $w) {
            $colsXml .= '<col min="' . $colIdx . '" max="' . $colIdx . '" width="' . $w . '" customWidth="1"/>';
        }
        $colsXml .= '</cols>';

        // Header column titles
        $headers = [
            'A' => 'NO',
            'B' => 'TANGGAL DAFTAR',
            'C' => 'NIK (KTP)',
            'D' => 'NAMA LENGKAP',
            'E' => 'JENIS KELAMIN',
            'F' => 'TANGGAL LAHIR',
            'G' => 'USIA',
            'H' => 'PENDIDIKAN',
            'I' => 'NO WHATSAPP / HP',
            'J' => 'EMAIL',
            'K' => 'POSISI DILAMAR',
            'L' => 'AREA PENEMPATAN',
            'M' => 'RINGKASAN PENGALAMAN KERJA',
            'N' => 'KATEGORI AI',
            'O' => 'AI SCORE',
            'P' => 'STATUS KANDIDAT',
            'Q' => 'HASIL ANALISIS AI (PDF)',
            'R' => 'REKRUTER / AS',
        ];

        // Start XML
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
    <sheetViews>
        <sheetView tabSelected="1" workbookViewId="0">
            <pane ySplit="4" topLeftCell="A5" activePane="bottomLeft" state="frozen"/>
        </sheetView>
    </sheetViews>
    ' . $colsXml . '
    <sheetData>
        <!-- ROW 1: TITLE BANNER -->
        <row r="1" ht="26">
            <c r="A1" t="inlineStr" s="1"><is><t>' . self::xmlEscape('ASYSTEM - REKAPITULASI DATA PELAMAR JOB PORTAL') . '</t></is></c>
        </row>
        <!-- ROW 2: SUBTITLE METADATA -->
        <row r="2" ht="18">
            <c r="A2" t="inlineStr" s="2"><is><t>' . self::xmlEscape($metaText) . '</t></is></c>
        </row>
        <!-- ROW 3: SPACING -->
        <row r="3" ht="8"/>
        <!-- ROW 4: TABLE HEADER -->
        <row r="4" ht="30">';

        foreach ($headers as $colLetter => $title) {
            $xml .= '<c r="' . $colLetter . '4" t="inlineStr" s="3"><is><t>' . self::xmlEscape($title) . '</t></is></c>';
        }

        $xml .= '</row>';

        // Data Rows
        $rowNum = 5;
        $no = 1;

        $baseUrl = config('app.url', 'https://new.asystem.co.id');
        if (str_ends_with($baseUrl, '/')) {
            $baseUrl = rtrim($baseUrl, '/');
        }

        foreach ($candidates as $c) {
            $isZebra = ($no % 2 === 0);
            $sLeft = $isZebra ? 6 : 4;
            $sCenter = $isZebra ? 7 : 5;
            $sWrap = $isZebra ? 11 : 10;
            $sLink = $isZebra ? 9 : 8;

            // Formatted values
            $tglDaftar = $c->created_at ? $c->created_at->format('d/m/Y H:i') : '-';
            
            // Format NIK: text string with clean numbers
            $nik = trim((string)$c->nik);
            if (empty($nik)) {
                $nik = '-';
            }

            $nama = trim((string)$c->full_name);
            $gender = !empty($c->gender) ? trim($c->gender) : '-';
            $tglLahir = $c->birth_date ? $c->birth_date->format('d/m/Y') : '-';
            $usia = !empty($c->age) ? $c->age . ' Thn' : '-';
            $pendidikan = !empty($c->education) ? trim($c->education) : '-';

            // Phone
            $phone = !empty($c->phone) ? trim($c->phone) : (!empty($c->whatsapp) ? trim($c->whatsapp) : '-');
            $email = !empty($c->email) ? trim($c->email) : '-';

            $posisi = !empty($c->applied_job) ? trim($c->applied_job) : '-';
            $area = !empty($c->area) ? trim($c->area) : '-';

            // Ringkasan Pengalaman Kerja
            $expSummary = trim($c->experience_summary ?? '');
            if (empty($expSummary) && $c->relationLoaded('workExperiences') && $c->workExperiences->count() > 0) {
                $expLines = [];
                foreach ($c->workExperiences as $w) {
                    $comp = trim($w->company_name ?? '');
                    $posExp = trim($w->position ?? '');
                    $line = $comp . ($posExp ? " ({$posExp})" : '');
                    if ($w->start_date) {
                        $line .= ' [' . $w->start_date->format('Y') . ($w->end_date ? ' - ' . $w->end_date->format('Y') : ' - sekarang') . ']';
                    }
                    if (!empty($line)) {
                        $expLines[] = $line;
                    }
                }
                $expSummary = implode(" | ", $expLines);
            }
            if (empty($expSummary)) {
                $expSummary = '-';
            }

            // Kategori AI & Style
            $kategori = !empty($c->kategori_kandidat) ? trim($c->kategori_kandidat) : 'Pending';
            $sKategori = $sCenter;
            if (strcasecmp($kategori, 'Green') === 0) {
                $sKategori = $isZebra ? 13 : 12;
            } elseif (strcasecmp($kategori, 'Yellow') === 0) {
                $sKategori = $isZebra ? 15 : 14;
            } elseif (strcasecmp($kategori, 'Red') === 0) {
                $sKategori = $isZebra ? 17 : 16;
            }

            $aiScore = ($c->ai_score !== null && $c->ai_score > 0) ? $c->ai_score . '%' : 'Pending';
            $statusKandidat = !empty($c->status_kandidat) ? trim($c->status_kandidat) : 'Baru';

            // Link PDF Hasil Analisis AI
            $pdfUrl = $baseUrl . '/kandidatportal/' . $c->id . '/cetak-ai';
            $rekruter = !empty($c->useras) ? trim($c->useras) : '-';

            // Determine row height: taller if experience summary is long
            $rowHeight = (mb_strlen($expSummary) > 60) ? 38 : 24;

            $xml .= '<row r="' . $rowNum . '" ht="' . $rowHeight . '">';
            $xml .= '<c r="A' . $rowNum . '" t="inlineStr" s="' . $sCenter . '"><is><t>' . $no . '</t></is></c>';
            $xml .= '<c r="B' . $rowNum . '" t="inlineStr" s="' . $sCenter . '"><is><t>' . self::xmlEscape($tglDaftar) . '</t></is></c>';
            $xml .= '<c r="C' . $rowNum . '" t="inlineStr" s="' . $sCenter . '"><is><t>' . self::xmlEscape($nik) . '</t></is></c>';
            $xml .= '<c r="D' . $rowNum . '" t="inlineStr" s="' . $sLeft . '"><is><t>' . self::xmlEscape($nama) . '</t></is></c>';
            $xml .= '<c r="E' . $rowNum . '" t="inlineStr" s="' . $sCenter . '"><is><t>' . self::xmlEscape($gender) . '</t></is></c>';
            $xml .= '<c r="F' . $rowNum . '" t="inlineStr" s="' . $sCenter . '"><is><t>' . self::xmlEscape($tglLahir) . '</t></is></c>';
            $xml .= '<c r="G' . $rowNum . '" t="inlineStr" s="' . $sCenter . '"><is><t>' . self::xmlEscape($usia) . '</t></is></c>';
            $xml .= '<c r="H' . $rowNum . '" t="inlineStr" s="' . $sCenter . '"><is><t>' . self::xmlEscape($pendidikan) . '</t></is></c>';
            $xml .= '<c r="I' . $rowNum . '" t="inlineStr" s="' . $sCenter . '"><is><t>' . self::xmlEscape($phone) . '</t></is></c>';
            $xml .= '<c r="J' . $rowNum . '" t="inlineStr" s="' . $sLeft . '"><is><t>' . self::xmlEscape($email) . '</t></is></c>';
            $xml .= '<c r="K' . $rowNum . '" t="inlineStr" s="' . $sLeft . '"><is><t>' . self::xmlEscape($posisi) . '</t></is></c>';
            $xml .= '<c r="L' . $rowNum . '" t="inlineStr" s="' . $sLeft . '"><is><t>' . self::xmlEscape($area) . '</t></is></c>';
            $xml .= '<c r="M' . $rowNum . '" t="inlineStr" s="' . $sWrap . '"><is><t>' . self::xmlEscape($expSummary) . '</t></is></c>';
            $xml .= '<c r="N' . $rowNum . '" t="inlineStr" s="' . $sKategori . '"><is><t>' . self::xmlEscape($kategori) . '</t></is></c>';
            $xml .= '<c r="O' . $rowNum . '" t="inlineStr" s="' . $sCenter . '"><is><t>' . self::xmlEscape($aiScore) . '</t></is></c>';
            $xml .= '<c r="P' . $rowNum . '" t="inlineStr" s="' . $sCenter . '"><is><t>' . self::xmlEscape($statusKandidat) . '</t></is></c>';
            
            // Hyperlink cell to AI Analysis PDF
            $xml .= '<c r="Q' . $rowNum . '" s="' . $sLink . '"><f>' . self::xmlEscape('HYPERLINK("' . $pdfUrl . '", "Lihat PDF AI")') . '</f><v>Lihat PDF AI</v></c>';
            
            $xml .= '<c r="R' . $rowNum . '" t="inlineStr" s="' . $sLeft . '"><is><t>' . self::xmlEscape($rekruter) . '</t></is></c>';
            $xml .= '</row>';

            $rowNum++;
            $no++;
        }

        $xml .= '    </sheetData>
</worksheet>';

        return $xml;
    }

    /**
     * XML string escape helper
     */
    protected static function xmlEscape(?string $str): string
    {
        if ($str === null || $str === '') {
            return '';
        }
        return htmlspecialchars($str, ENT_XML1, 'UTF-8');
    }
}
