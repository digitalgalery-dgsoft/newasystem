<?php

namespace App\Services;

use ZipArchive;
use Exception;
use Carbon\Carbon;

class JobStatistikXlsxExportService
{
    /**
     * Generate an executive, styled Microsoft Excel (.xlsx) file with 4 structured sheets.
     *
     * @param array $data Data from JobStatistikController::calculateStatistics
     * @param array $filters Applied filters
     * @return string Path to temporary .xlsx file
     */
    public static function generateXlsx(array $data, array $filters = []): string
    {
        $tempDir = sys_get_temp_dir();
        $tempFile = tempnam($tempDir, 'job_stats_') . '.xlsx';

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
    <Override PartName="/xl/worksheets/sheet2.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
    <Override PartName="/xl/worksheets/sheet3.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
    <Override PartName="/xl/worksheets/sheet4.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
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
    <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet2.xml"/>
    <Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet3.xml"/>
    <Relationship Id="rId4" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet4.xml"/>
    <Relationship Id="rId5" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
</Relationships>';
        $zip->addFromString('xl/_rels/workbook.xml.rels', $wbRels);

        // 4. xl/workbook.xml
        $workbook = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
    <sheets>
        <sheet name="Rekap Step Odoo" sheetId="1" r:id="rId1"/>
        <sheet name="Statistik per Area" sheetId="2" r:id="rId2"/>
        <sheet name="Statistik User &amp; Area" sheetId="3" r:id="rId3"/>
        <sheet name="Detail Lowongan" sheetId="4" r:id="rId4"/>
    </sheets>
</workbook>';
        $zip->addFromString('xl/workbook.xml', $workbook);

        // 5. xl/styles.xml
        $styles = self::buildStylesXml();
        $zip->addFromString('xl/styles.xml', $styles);

        // 6. Worksheets
        $zip->addFromString('xl/worksheets/sheet1.xml', self::buildSheet1OdooXml($data, $filters));
        $zip->addFromString('xl/worksheets/sheet2.xml', self::buildSheet2AreaXml($data, $filters));
        $zip->addFromString('xl/worksheets/sheet3.xml', self::buildSheet3UserAreaXml($data, $filters));
        $zip->addFromString('xl/worksheets/sheet4.xml', self::buildSheet4DetailXml($data, $filters));

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
    <fonts count="10">
        <!-- 0: Regular 10pt Slate -->
        <font><sz val="10"/><name val="Segoe UI"/><color rgb="FF1E293B"/></font>
        <!-- 1: Bold 14pt Indigo Title -->
        <font><b/><sz val="14"/><name val="Segoe UI"/><color rgb="FF1E1B4B"/></font>
        <!-- 2: Bold 10pt White Header -->
        <font><b/><sz val="10"/><name val="Segoe UI"/><color rgb="FFFFFFFF"/></font>
        <!-- 3: Bold 10pt Dark Slate (Total Row) -->
        <font><b/><sz val="10"/><name val="Segoe UI"/><color rgb="FF0F172A"/></font>
        <!-- 4: Subtitle 9pt Italic Muted -->
        <font><i/><sz val="9"/><name val="Segoe UI"/><color rgb="FF64748B"/></font>
        <!-- 5: Bold 10pt Green -->
        <font><b/><sz val="10"/><name val="Segoe UI"/><color rgb="FF065F46"/></font>
        <!-- 6: Bold 10pt Amber -->
        <font><b/><sz val="10"/><name val="Segoe UI"/><color rgb="FF92400E"/></font>
        <!-- 7: Bold 10pt Rose -->
        <font><b/><sz val="10"/><name val="Segoe UI"/><color rgb="FF9F1239"/></font>
        <!-- 8: Bold 12pt Metric Card Value -->
        <font><b/><sz val="12"/><name val="Segoe UI"/><color rgb="FF0F172A"/></font>
        <!-- 9: Bold 8pt Metric Card Label -->
        <font><b/><sz val="8"/><name val="Segoe UI"/><color rgb="FF475569"/></font>
    </fonts>
    <fills count="16">
        <fill><patternFill patternType="none"/></fill>
        <fill><patternFill patternType="gray125"/></fill>
        <!-- 2: Header Indigo (Table 4) -->
        <fill><patternFill patternType="solid"><fgColor rgb="FF312E81"/></patternFill></fill>
        <!-- 3: Header Dark Navy (Table 1) -->
        <fill><patternFill patternType="solid"><fgColor rgb="FF1E293B"/></patternFill></fill>
        <!-- 4: Header Ocean Blue (Table 2) -->
        <fill><patternFill patternType="solid"><fgColor rgb="FF0284C7"/></patternFill></fill>
        <!-- 5: Header Dark Slate (Table 3) -->
        <fill><patternFill patternType="solid"><fgColor rgb="FF0F172A"/></patternFill></fill>
        <!-- 6: Zebra row light slate -->
        <fill><patternFill patternType="solid"><fgColor rgb="FFF8FAFC"/></patternFill></fill>
        <!-- 7: Soft Green (Joined / Green) -->
        <fill><patternFill patternType="solid"><fgColor rgb="FFD1FAE5"/></patternFill></fill>
        <!-- 8: Soft Amber (Yellow / E-Learning) -->
        <fill><patternFill patternType="solid"><fgColor rgb="FFFEF3C7"/></patternFill></fill>
        <!-- 9: Soft Rose (Red / Belum di Odoo) -->
        <fill><patternFill patternType="solid"><fgColor rgb="FFFFE4E6"/></patternFill></fill>
        <!-- 10: Soft Blue (Pelamar) -->
        <fill><patternFill patternType="solid"><fgColor rgb="FFE0F2FE"/></patternFill></fill>
        <!-- 11: Soft Indigo (Interview) -->
        <fill><patternFill patternType="solid"><fgColor rgb="FFE0E7FF"/></patternFill></fill>
        <!-- 12: Soft Purple (Principal) -->
        <fill><patternFill patternType="solid"><fgColor rgb="FFF3E8FF"/></patternFill></fill>
        <!-- 13: Soft Teal (PKWT) -->
        <fill><patternFill patternType="solid"><fgColor rgb="FFCCFBF1"/></patternFill></fill>
        <!-- 14: Metric card bg -->
        <fill><patternFill patternType="solid"><fgColor rgb="FFF1F5F9"/></patternFill></fill>
        <!-- 15: Grand total summary row fill -->
        <fill><patternFill patternType="solid"><fgColor rgb="FFE2E8F0"/></patternFill></fill>
    </fills>
    <borders count="4">
        <border><left/><right/><top/><bottom/><diagonal/></border>
        <!-- 1: Thin border light gray -->
        <border>
            <left style="thin"><color rgb="FFE2E8F0"/></left>
            <right style="thin"><color rgb="FFE2E8F0"/></right>
            <top style="thin"><color rgb="FFE2E8F0"/></top>
            <bottom style="thin"><color rgb="FFE2E8F0"/></bottom>
        </border>
        <!-- 2: Metric card border -->
        <border>
            <left style="thin"><color rgb="FFCBD5E1"/></left>
            <right style="thin"><color rgb="FFCBD5E1"/></right>
            <top style="thin"><color rgb="FFCBD5E1"/></top>
            <bottom style="thin"><color rgb="FFCBD5E1"/></bottom>
        </border>
        <!-- 3: Grand Total Border (thin top, double bottom) -->
        <border>
            <left style="thin"><color rgb="FF94A3B8"/></left>
            <right style="thin"><color rgb="FF94A3B8"/></right>
            <top style="thin"><color rgb="FF475569"/></top>
            <bottom style="double"><color rgb="FF1E293B"/></bottom>
        </border>
    </borders>
    <cellStyleXfs count="1">
        <xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>
    </cellStyleXfs>
    <cellXfs count="25">
        <!-- 0: Default -->
        <xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>
        <!-- 1: Title Banner (A1) -->
        <xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1"><alignment vertical="center"/></xf>
        <!-- 2: Subtitle Metadata (A2) -->
        <xf numFmtId="0" fontId="4" fillId="0" borderId="0" xfId="0" applyFont="1"><alignment vertical="center"/></xf>
        <!-- 3: Table Header Indigo (Sheet 1) -->
        <xf numFmtId="0" fontId="2" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>
        <!-- 4: Table Header Dark Navy (Sheet 2) -->
        <xf numFmtId="0" fontId="2" fillId="3" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>
        <!-- 5: Table Header Ocean Blue (Sheet 3) -->
        <xf numFmtId="0" fontId="2" fillId="4" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>
        <!-- 6: Table Header Dark Slate (Sheet 4) -->
        <xf numFmtId="0" fontId="2" fillId="5" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>
        <!-- 7: Data Regular Left -->
        <xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1"><alignment horizontal="left" vertical="center"/></xf>
        <!-- 8: Data Regular Center -->
        <xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1"><alignment horizontal="center" vertical="center"/></xf>
        <!-- 9: Data Regular Right / Number -->
        <xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1"><alignment horizontal="right" vertical="center"/></xf>
        <!-- 10: Data Zebra Left -->
        <xf numFmtId="0" fontId="0" fillId="6" borderId="1" xfId="0" applyFill="1" applyBorder="1"><alignment horizontal="left" vertical="center"/></xf>
        <!-- 11: Data Zebra Center -->
        <xf numFmtId="0" fontId="0" fillId="6" borderId="1" xfId="0" applyFill="1" applyBorder="1"><alignment horizontal="center" vertical="center"/></xf>
        <!-- 12: Data Zebra Right / Number -->
        <xf numFmtId="0" fontId="0" fillId="6" borderId="1" xfId="0" applyFill="1" applyBorder="1"><alignment horizontal="right" vertical="center"/></xf>
        <!-- 13: Soft Green Center (Joined / Green) -->
        <xf numFmtId="0" fontId="5" fillId="7" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1"><alignment horizontal="center" vertical="center"/></xf>
        <!-- 14: Soft Amber Center (Yellow) -->
        <xf numFmtId="0" fontId="6" fillId="8" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1"><alignment horizontal="center" vertical="center"/></xf>
        <!-- 15: Soft Rose Center (Red / Belum Odoo) -->
        <xf numFmtId="0" fontId="7" fillId="9" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1"><alignment horizontal="center" vertical="center"/></xf>
        <!-- 16: Soft Blue Center (Pelamar) -->
        <xf numFmtId="0" fontId="0" fillId="10" borderId="1" xfId="0" applyFill="1" applyBorder="1"><alignment horizontal="center" vertical="center"/></xf>
        <!-- 17: Soft Indigo Center (Interview) -->
        <xf numFmtId="0" fontId="0" fillId="11" borderId="1" xfId="0" applyFill="1" applyBorder="1"><alignment horizontal="center" vertical="center"/></xf>
        <!-- 18: Soft Purple Center (Principal) -->
        <xf numFmtId="0" fontId="0" fillId="12" borderId="1" xfId="0" applyFill="1" applyBorder="1"><alignment horizontal="center" vertical="center"/></xf>
        <!-- 19: Soft Teal Center (PKWT) -->
        <xf numFmtId="0" fontId="0" fillId="13" borderId="1" xfId="0" applyFill="1" applyBorder="1"><alignment horizontal="center" vertical="center"/></xf>
        <!-- 20: Grand Total Left -->
        <xf numFmtId="0" fontId="3" fillId="15" borderId="3" xfId="0" applyFont="1" applyFill="1" applyBorder="1"><alignment horizontal="left" vertical="center"/></xf>
        <!-- 21: Grand Total Center -->
        <xf numFmtId="0" fontId="3" fillId="15" borderId="3" xfId="0" applyFont="1" applyFill="1" applyBorder="1"><alignment horizontal="center" vertical="center"/></xf>
        <!-- 22: Grand Total Right / Number -->
        <xf numFmtId="0" fontId="3" fillId="15" borderId="3" xfId="0" applyFont="1" applyFill="1" applyBorder="1"><alignment horizontal="right" vertical="center"/></xf>
        <!-- 23: Metric Card Label (A4..D4) -->
        <xf numFmtId="0" fontId="9" fillId="14" borderId="2" xfId="0" applyFont="1" applyFill="1" applyBorder="1"><alignment horizontal="center" vertical="center"/></xf>
        <!-- 24: Metric Card Value (A5..D5) -->
        <xf numFmtId="0" fontId="8" fillId="14" borderId="2" xfId="0" applyFont="1" applyFill="1" applyBorder="1"><alignment horizontal="center" vertical="center"/></xf>
    </cellXfs>
</styleSheet>';
    }

    /**
     * Helper to escape text for XML
     */
    protected static function escapeXml(?string $v): string
    {
        if ($v === null) return '';
        return htmlspecialchars($v, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }

    /**
     * Build Subtitle Metadata string from filters
     */
    protected static function buildSubtitle(array $filters): string
    {
        $parts = [];
        $parts[] = 'Filter Region: ' . (!empty($filters['region']) ? $filters['region'] : 'Semua Region');
        $parts[] = 'Area: ' . (!empty($filters['area']) ? $filters['area'] : 'Semua Area');
        $parts[] = 'Prinsiple: ' . (!empty($filters['prinsiple']) ? $filters['prinsiple'] : 'Semua Prinsiple');
        $parts[] = 'User: ' . (!empty($filters['user']) ? $filters['user'] : 'Semua User');
        $parts[] = 'Info: ' . (!empty($filters['info']) ? $filters['info'] : 'Semua Info');
        $parts[] = 'Waktu Export: ' . Carbon::now('Asia/Jakarta')->format('d/m/Y H:i:s') . ' WIB';

        return implode(' | ', $parts);
    }

    /**
     * Sheet 1: Rekap Step Odoo per Rekrutor / AS (Ranked by Joined)
     */
    protected static function buildSheet1OdooXml(array $data, array $filters): string
    {
        $xml = [];
        $xml[] = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
        $xml[] = '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">';
        
        // Column Widths (13 columns)
        $xml[] = '<cols>';
        $xml[] = '<col min="1" max="1" width="6" customWidth="1"/>';   // No
        $xml[] = '<col min="2" max="2" width="38" customWidth="1"/>';  // Nama Rekrutor / AS
        $xml[] = '<col min="3" max="3" width="30" customWidth="1"/>';  // Email Rekrutor
        $xml[] = '<col min="4" max="4" width="18" customWidth="1"/>';  // Area
        $xml[] = '<col min="5" max="5" width="12" customWidth="1"/>';  // Region
        $xml[] = '<col min="6" max="6" width="12" customWidth="1"/>';  // Total
        $xml[] = '<col min="7" max="7" width="14" customWidth="1"/>';  // 1. Pelamar
        $xml[] = '<col min="8" max="8" width="14" customWidth="1"/>';  // 2. Interview
        $xml[] = '<col min="9" max="9" width="14" customWidth="1"/>';  // 3. Principal
        $xml[] = '<col min="10" max="10" width="14" customWidth="1"/>'; // 4. E-Learning
        $xml[] = '<col min="11" max="11" width="14" customWidth="1"/>'; // 5. PKWT
        $xml[] = '<col min="12" max="12" width="16" customWidth="1"/>'; // 6. Joined (Rank)
        $xml[] = '<col min="13" max="13" width="16" customWidth="1"/>'; // Belum di Odoo
        $xml[] = '</cols>';

        $xml[] = '<sheetData>';

        // Row 1: Title
        $xml[] = '<row r="1" ht="28" customHeight="1">';
        $xml[] = '<c r="A1" s="1" t="inlineStr"><is><t>STATISTIK KANDIDAT PER REKRUTOR / AS BERDASARKAN STEP ODOO ERP</t></is></c>';
        $xml[] = '</row>';

        // Row 2: Subtitle
        $sub = self::buildSubtitle($filters) . ' | Khusus Pelamar Kandidat Portal (jenis = Job Portal)';
        $xml[] = '<row r="2" ht="18" customHeight="1">';
        $xml[] = '<c r="A2" s="2" t="inlineStr"><is><t>' . self::escapeXml($sub) . '</t></is></c>';
        $xml[] = '</row>';

        // Row 4: Metric KPI Headers
        $xml[] = '<row r="4" ht="16" customHeight="1">';
        $xml[] = '<c r="A4" s="23" t="inlineStr"><is><t>TOTAL JOB REQUIREMENT</t></is></c>';
        $xml[] = '<c r="B4" s="23" t="inlineStr"><is><t>TOTAL PELAMAR MASUK</t></is></c>';
        $xml[] = '<c r="C4" s="23" t="inlineStr"><is><t>KANDIDAT GRADE GREEN</t></is></c>';
        $xml[] = '<c r="D4" s="23" t="inlineStr"><is><t>TERDAFTAR DI ODOO ERP</t></is></c>';
        $xml[] = '</row>';

        // Row 5: Metric KPI Values
        $xml[] = '<row r="5" ht="24" customHeight="1">';
        $xml[] = '<c r="A5" s="24"><v>' . (int)($data['totalJobPosts'] ?? 0) . '</v></c>';
        $xml[] = '<c r="B5" s="24"><v>' . (int)($data['totalPelamar'] ?? 0) . '</v></c>';
        $xml[] = '<c r="C5" s="24"><v>' . (int)($data['totalGreen'] ?? 0) . '</v></c>';
        $xml[] = '<c r="D5" s="24"><v>' . (int)($data['totalOdoo'] ?? 0) . '</v></c>';
        $xml[] = '</row>';

        // Row 7: Table Header
        $xml[] = '<row r="7" ht="26" customHeight="1">';
        $headers = [
            'No', 'Nama Rekrutor / AS', 'Email Rekrutor', 'Area', 'Region', 
            'Total', '1. Pelamar', '2. Interview', '3. Principal', '4. E-Learning', '5. PKWT', '6. Joined (Rank)', 'Belum di Odoo'
        ];
        foreach ($headers as $idx => $h) {
            $colLetter = self::colLetter($idx + 1);
            $xml[] = '<c r="' . $colLetter . '7" s="3" t="inlineStr"><is><t>' . $h . '</t></is></c>';
        }
        $xml[] = '</row>';

        // Data Rows
        $rowNum = 8;
        $sumTotal = 0; $sumPelamar = 0; $sumInterview = 0; $sumPrincipal = 0; $sumElearning = 0; $sumPkwt = 0; $sumJoined = 0; $sumBelum = 0;

        foreach ($data['statsOdooRecruiter'] as $idx => $r) {
            $isZebra = ($idx % 2 === 1);
            $sLeft = $isZebra ? 10 : 7;
            $sCenter = $isZebra ? 11 : 8;

            $sumTotal += (int)$r['total'];
            $sumPelamar += (int)$r['data_pelamar'];
            $sumInterview += (int)$r['interview'];
            $sumPrincipal += (int)$r['principal'];
            $sumElearning += (int)$r['elearning'];
            $sumPkwt += (int)$r['pkwt'];
            $sumJoined += (int)$r['joined'];
            $sumBelum += (int)$r['belum_di_odoo'];

            $xml[] = '<row r="' . $rowNum . '" ht="20" customHeight="1">';
            $xml[] = '<c r="A' . $rowNum . '" s="' . $sCenter . '"><v>' . ($idx + 1) . '</v></c>';
            $xml[] = '<c r="B' . $rowNum . '" s="' . $sLeft . '" t="inlineStr"><is><t>' . self::escapeXml($r['user_display']) . '</t></is></c>';
            $xml[] = '<c r="C' . $rowNum . '" s="' . $sLeft . '" t="inlineStr"><is><t>' . self::escapeXml($r['user_email']) . '</t></is></c>';
            $xml[] = '<c r="D' . $rowNum . '" s="' . $sCenter . '" t="inlineStr"><is><t>' . self::escapeXml($r['area']) . '</t></is></c>';
            $xml[] = '<c r="E' . $rowNum . '" s="' . $sCenter . '" t="inlineStr"><is><t>' . self::escapeXml($r['region']) . '</t></is></c>';
            $xml[] = '<c r="F' . $rowNum . '" s="' . $sCenter . '"><v>' . (int)$r['total'] . '</v></c>';
            
            // Step Columns with thematic fills
            $xml[] = '<c r="G' . $rowNum . '" s="' . ($r['data_pelamar'] > 0 ? 16 : $sCenter) . '"><v>' . (int)$r['data_pelamar'] . '</v></c>';
            $xml[] = '<c r="H' . $rowNum . '" s="' . ($r['interview'] > 0 ? 17 : $sCenter) . '"><v>' . (int)$r['interview'] . '</v></c>';
            $xml[] = '<c r="I' . $rowNum . '" s="' . ($r['principal'] > 0 ? 18 : $sCenter) . '"><v>' . (int)$r['principal'] . '</v></c>';
            $xml[] = '<c r="J' . $rowNum . '" s="' . ($r['elearning'] > 0 ? 14 : $sCenter) . '"><v>' . (int)$r['elearning'] . '</v></c>';
            $xml[] = '<c r="K' . $rowNum . '" s="' . ($r['pkwt'] > 0 ? 19 : $sCenter) . '"><v>' . (int)$r['pkwt'] . '</v></c>';
            $xml[] = '<c r="L' . $rowNum . '" s="' . ($r['joined'] > 0 ? 13 : $sCenter) . '"><v>' . (int)$r['joined'] . '</v></c>';
            $xml[] = '<c r="M' . $rowNum . '" s="' . ($r['belum_di_odoo'] > 0 ? 15 : $sCenter) . '"><v>' . (int)$r['belum_di_odoo'] . '</v></c>';

            $xml[] = '</row>';
            $rowNum++;
        }

        // Summary Total Row
        $xml[] = '<row r="' . $rowNum . '" ht="24" customHeight="1">';
        $xml[] = '<c r="A' . $rowNum . '" s="21" t="inlineStr"><is><t>TOTAL</t></is></c>';
        $xml[] = '<c r="B' . $rowNum . '" s="20" t="inlineStr"><is><t>Grand Total Seluruh Rekrutor</t></is></c>';
        $xml[] = '<c r="C' . $rowNum . '" s="21" t="inlineStr"><is><t>-</t></is></c>';
        $xml[] = '<c r="D' . $rowNum . '" s="21" t="inlineStr"><is><t>-</t></is></c>';
        $xml[] = '<c r="E' . $rowNum . '" s="21" t="inlineStr"><is><t>-</t></is></c>';
        $xml[] = '<c r="F' . $rowNum . '" s="21"><v>' . $sumTotal . '</v></c>';
        $xml[] = '<c r="G' . $rowNum . '" s="21"><v>' . $sumPelamar . '</v></c>';
        $xml[] = '<c r="H' . $rowNum . '" s="21"><v>' . $sumInterview . '</v></c>';
        $xml[] = '<c r="I' . $rowNum . '" s="21"><v>' . $sumPrincipal . '</v></c>';
        $xml[] = '<c r="J' . $rowNum . '" s="21"><v>' . $sumElearning . '</v></c>';
        $xml[] = '<c r="K' . $rowNum . '" s="21"><v>' . $sumPkwt . '</v></c>';
        $xml[] = '<c r="L' . $rowNum . '" s="21"><v>' . $sumJoined . '</v></c>';
        $xml[] = '<c r="M' . $rowNum . '" s="21"><v>' . $sumBelum . '</v></c>';
        $xml[] = '</row>';

        $xml[] = '</sheetData>';
        $xml[] = '</worksheet>';

        return implode('', $xml);
    }

    /**
     * Sheet 2: Statistik per Area
     */
    protected static function buildSheet2AreaXml(array $data, array $filters): string
    {
        $xml = [];
        $xml[] = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
        $xml[] = '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">';
        
        $xml[] = '<cols>';
        $xml[] = '<col min="1" max="1" width="6" customWidth="1"/>';   // No
        $xml[] = '<col min="2" max="2" width="16" customWidth="1"/>';  // Region
        $xml[] = '<col min="3" max="3" width="28" customWidth="1"/>';  // Area
        $xml[] = '<col min="4" max="4" width="18" customWidth="1"/>';  // Jumlah Job Post
        $xml[] = '<col min="5" max="5" width="18" customWidth="1"/>';  // Jumlah Pelamar
        $xml[] = '</cols>';

        $xml[] = '<sheetData>';

        // Row 1: Title
        $xml[] = '<row r="1" ht="26" customHeight="1">';
        $xml[] = '<c r="A1" s="1" t="inlineStr"><is><t>STATISTIK JOB REQUIREMENT &amp; PELAMAR PER AREA</t></is></c>';
        $xml[] = '</row>';

        // Row 2: Subtitle
        $sub = self::buildSubtitle($filters);
        $xml[] = '<row r="2" ht="18" customHeight="1">';
        $xml[] = '<c r="A2" s="2" t="inlineStr"><is><t>' . self::escapeXml($sub) . '</t></is></c>';
        $xml[] = '</row>';

        // Row 4: Header
        $xml[] = '<row r="4" ht="24" customHeight="1">';
        $headers = ['No', 'Region', 'Area / Kota', 'Jumlah Job Post', 'Jumlah Pelamar'];
        foreach ($headers as $idx => $h) {
            $col = self::colLetter($idx + 1);
            $xml[] = '<c r="' . $col . '4" s="4" t="inlineStr"><is><t>' . $h . '</t></is></c>';
        }
        $xml[] = '</row>';

        $rowNum = 5;
        $totalJob = 0;
        $totalPel = 0;

        foreach ($data['statsArea'] as $idx => $r) {
            $isZebra = ($idx % 2 === 1);
            $sLeft = $isZebra ? 10 : 7;
            $sCenter = $isZebra ? 11 : 8;

            $totalJob += (int)$r['job_post'];
            $totalPel += (int)$r['pelamar'];

            $xml[] = '<row r="' . $rowNum . '" ht="20" customHeight="1">';
            $xml[] = '<c r="A' . $rowNum . '" s="' . $sCenter . '"><v>' . ($idx + 1) . '</v></c>';
            $xml[] = '<c r="B' . $rowNum . '" s="' . $sCenter . '" t="inlineStr"><is><t>' . self::escapeXml($r['region']) . '</t></is></c>';
            $xml[] = '<c r="C' . $rowNum . '" s="' . $sLeft . '" t="inlineStr"><is><t>' . self::escapeXml($r['area']) . '</t></is></c>';
            $xml[] = '<c r="D' . $rowNum . '" s="' . $sCenter . '"><v>' . (int)$r['job_post'] . '</v></c>';
            $xml[] = '<c r="E' . $rowNum . '" s="' . $sCenter . '"><v>' . (int)$r['pelamar'] . '</v></c>';
            $xml[] = '</row>';
            $rowNum++;
        }

        // Summary Row
        $xml[] = '<row r="' . $rowNum . '" ht="24" customHeight="1">';
        $xml[] = '<c r="A' . $rowNum . '" s="21" t="inlineStr"><is><t>TOTAL</t></is></c>';
        $xml[] = '<c r="B' . $rowNum . '" s="21" t="inlineStr"><is><t>-</t></is></c>';
        $xml[] = '<c r="C' . $rowNum . '" s="20" t="inlineStr"><is><t>Grand Total Seluruh Area</t></is></c>';
        $xml[] = '<c r="D' . $rowNum . '" s="21"><v>' . $totalJob . '</v></c>';
        $xml[] = '<c r="E' . $rowNum . '" s="21"><v>' . $totalPel . '</v></c>';
        $xml[] = '</row>';

        $xml[] = '</sheetData>';
        $xml[] = '</worksheet>';

        return implode('', $xml);
    }

    /**
     * Sheet 3: Statistik per User & Area
     */
    protected static function buildSheet3UserAreaXml(array $data, array $filters): string
    {
        $xml = [];
        $xml[] = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
        $xml[] = '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">';
        
        $xml[] = '<cols>';
        $xml[] = '<col min="1" max="1" width="6" customWidth="1"/>';   // No
        $xml[] = '<col min="2" max="2" width="38" customWidth="1"/>';  // Nama User / Rekrutor
        $xml[] = '<col min="3" max="3" width="16" customWidth="1"/>';  // Region
        $xml[] = '<col min="4" max="4" width="24" customWidth="1"/>';  // Area
        $xml[] = '<col min="5" max="5" width="18" customWidth="1"/>';  // Jumlah Job Post
        $xml[] = '<col min="6" max="6" width="18" customWidth="1"/>';  // Jumlah Pelamar
        $xml[] = '</cols>';

        $xml[] = '<sheetData>';

        // Row 1: Title
        $xml[] = '<row r="1" ht="26" customHeight="1">';
        $xml[] = '<c r="A1" s="1" t="inlineStr"><is><t>STATISTIK JOB REQUIREMENT &amp; PELAMAR PER USER &amp; AREA</t></is></c>';
        $xml[] = '</row>';

        // Row 2: Subtitle
        $sub = self::buildSubtitle($filters);
        $xml[] = '<row r="2" ht="18" customHeight="1">';
        $xml[] = '<c r="A2" s="2" t="inlineStr"><is><t>' . self::escapeXml($sub) . '</t></is></c>';
        $xml[] = '</row>';

        // Row 4: Header
        $xml[] = '<row r="4" ht="24" customHeight="1">';
        $headers = ['No', 'Nama User / Rekrutor', 'Region', 'Area', 'Jumlah Job Post', 'Jumlah Pelamar'];
        foreach ($headers as $idx => $h) {
            $col = self::colLetter($idx + 1);
            $xml[] = '<c r="' . $col . '4" s="5" t="inlineStr"><is><t>' . $h . '</t></is></c>';
        }
        $xml[] = '</row>';

        $rowNum = 5;
        $totalJob = 0;
        $totalPel = 0;

        foreach ($data['statsUserArea'] as $idx => $r) {
            $isZebra = ($idx % 2 === 1);
            $sLeft = $isZebra ? 10 : 7;
            $sCenter = $isZebra ? 11 : 8;

            $totalJob += (int)$r['job_post'];
            $totalPel += (int)$r['pelamar'];

            $xml[] = '<row r="' . $rowNum . '" ht="20" customHeight="1">';
            $xml[] = '<c r="A' . $rowNum . '" s="' . $sCenter . '"><v>' . ($idx + 1) . '</v></c>';
            $xml[] = '<c r="B' . $rowNum . '" s="' . $sLeft . '" t="inlineStr"><is><t>' . self::escapeXml($r['user']) . '</t></is></c>';
            $xml[] = '<c r="C' . $rowNum . '" s="' . $sCenter . '" t="inlineStr"><is><t>' . self::escapeXml($r['region']) . '</t></is></c>';
            $xml[] = '<c r="D' . $rowNum . '" s="' . $sLeft . '" t="inlineStr"><is><t>' . self::escapeXml($r['area']) . '</t></is></c>';
            $xml[] = '<c r="E' . $rowNum . '" s="' . $sCenter . '"><v>' . (int)$r['job_post'] . '</v></c>';
            $xml[] = '<c r="F' . $rowNum . '" s="' . $sCenter . '"><v>' . (int)$r['pelamar'] . '</v></c>';
            $xml[] = '</row>';
            $rowNum++;
        }

        // Summary Row
        $xml[] = '<row r="' . $rowNum . '" ht="24" customHeight="1">';
        $xml[] = '<c r="A' . $rowNum . '" s="21" t="inlineStr"><is><t>TOTAL</t></is></c>';
        $xml[] = '<c r="B' . $rowNum . '" s="20" t="inlineStr"><is><t>Grand Total User &amp; Area</t></is></c>';
        $xml[] = '<c r="C' . $rowNum . '" s="21" t="inlineStr"><is><t>-</t></is></c>';
        $xml[] = '<c r="D' . $rowNum . '" s="21" t="inlineStr"><is><t>-</t></is></c>';
        $xml[] = '<c r="E' . $rowNum . '" s="21"><v>' . $totalJob . '</v></c>';
        $xml[] = '<c r="F' . $rowNum . '" s="21"><v>' . $totalPel . '</v></c>';
        $xml[] = '</row>';

        $xml[] = '</sheetData>';
        $xml[] = '</worksheet>';

        return implode('', $xml);
    }

    /**
     * Sheet 4: Detail Lowongan & Grade Pelamar
     */
    protected static function buildSheet4DetailXml(array $data, array $filters): string
    {
        $xml = [];
        $xml[] = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
        $xml[] = '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">';
        
        $xml[] = '<cols>';
        $xml[] = '<col min="1" max="1" width="6" customWidth="1"/>';   // No
        $xml[] = '<col min="2" max="2" width="34" customWidth="1"/>';  // Nama User
        $xml[] = '<col min="3" max="3" width="14" customWidth="1"/>';  // Region
        $xml[] = '<col min="4" max="4" width="20" customWidth="1"/>';  // Area
        $xml[] = '<col min="5" max="5" width="26" customWidth="1"/>';  // Prinsiple
        $xml[] = '<col min="6" max="6" width="36" customWidth="1"/>';  // Nama Job / Formasi
        $xml[] = '<col min="7" max="7" width="16" customWidth="1"/>';  // Green
        $xml[] = '<col min="8" max="8" width="16" customWidth="1"/>';  // Yellow
        $xml[] = '<col min="9" max="9" width="16" customWidth="1"/>';  // Red
        $xml[] = '<col min="10" max="10" width="16" customWidth="1"/>'; // Total Pelamar
        $xml[] = '</cols>';

        $xml[] = '<sheetData>';

        // Row 1: Title
        $xml[] = '<row r="1" ht="26" customHeight="1">';
        $xml[] = '<c r="A1" s="1" t="inlineStr"><is><t>STATISTIK DETAIL KANDIDAT BERDASARKAN PRINSIPLE &amp; FORMASI JOB</t></is></c>';
        $xml[] = '</row>';

        // Row 2: Subtitle
        $sub = self::buildSubtitle($filters);
        $xml[] = '<row r="2" ht="18" customHeight="1">';
        $xml[] = '<c r="A2" s="2" t="inlineStr"><is><t>' . self::escapeXml($sub) . '</t></is></c>';
        $xml[] = '</row>';

        // Row 4: Header
        $xml[] = '<row r="4" ht="24" customHeight="1">';
        $headers = [
            'No', 'Nama User / Rekrutor', 'Region', 'Area', 'Prinsiple', 
            'Nama Job / Posisi', 'Kandidat Green', 'Kandidat Yellow', 'Kandidat Red', 'Total Pelamar'
        ];
        foreach ($headers as $idx => $h) {
            $col = self::colLetter($idx + 1);
            $xml[] = '<c r="' . $col . '4" s="6" t="inlineStr"><is><t>' . $h . '</t></is></c>';
        }
        $xml[] = '</row>';

        $rowNum = 5;
        $totalGreen = 0; $totalYellow = 0; $totalRed = 0; $totalPel = 0;

        foreach ($data['statsDetail'] as $idx => $r) {
            $isZebra = ($idx % 2 === 1);
            $sLeft = $isZebra ? 10 : 7;
            $sCenter = $isZebra ? 11 : 8;

            $totalGreen += (int)$r['green'];
            $totalYellow += (int)$r['yello'];
            $totalRed += (int)$r['red'];
            $totalPel += (int)$r['total_pelamar'];

            $xml[] = '<row r="' . $rowNum . '" ht="20" customHeight="1">';
            $xml[] = '<c r="A' . $rowNum . '" s="' . $sCenter . '"><v>' . ($idx + 1) . '</v></c>';
            $xml[] = '<c r="B' . $rowNum . '" s="' . $sLeft . '" t="inlineStr"><is><t>' . self::escapeXml($r['user']) . '</t></is></c>';
            $xml[] = '<c r="C' . $rowNum . '" s="' . $sCenter . '" t="inlineStr"><is><t>' . self::escapeXml($r['region']) . '</t></is></c>';
            $xml[] = '<c r="D' . $rowNum . '" s="' . $sLeft . '" t="inlineStr"><is><t>' . self::escapeXml($r['area']) . '</t></is></c>';
            $xml[] = '<c r="E' . $rowNum . '" s="' . $sLeft . '" t="inlineStr"><is><t>' . self::escapeXml($r['prinsiple']) . '</t></is></c>';
            $xml[] = '<c r="F' . $rowNum . '" s="' . $sLeft . '" t="inlineStr"><is><t>' . self::escapeXml($r['job_title']) . '</t></is></c>';
            $xml[] = '<c r="G' . $rowNum . '" s="' . ($r['green'] > 0 ? 13 : $sCenter) . '"><v>' . (int)$r['green'] . '</v></c>';
            $xml[] = '<c r="H' . $rowNum . '" s="' . ($r['yello'] > 0 ? 14 : $sCenter) . '"><v>' . (int)$r['yello'] . '</v></c>';
            $xml[] = '<c r="I' . $rowNum . '" s="' . ($r['red'] > 0 ? 15 : $sCenter) . '"><v>' . (int)$r['red'] . '</v></c>';
            $xml[] = '<c r="J' . $rowNum . '" s="' . $sCenter . '"><v>' . (int)$r['total_pelamar'] . '</v></c>';
            $xml[] = '</row>';
            $rowNum++;
        }

        // Summary Row
        $xml[] = '<row r="' . $rowNum . '" ht="24" customHeight="1">';
        $xml[] = '<c r="A' . $rowNum . '" s="21" t="inlineStr"><is><t>TOTAL</t></is></c>';
        $xml[] = '<c r="B' . $rowNum . '" s="20" t="inlineStr"><is><t>Grand Total Seluruh Formasi</t></is></c>';
        $xml[] = '<c r="C' . $rowNum . '" s="21" t="inlineStr"><is><t>-</t></is></c>';
        $xml[] = '<c r="D' . $rowNum . '" s="21" t="inlineStr"><is><t>-</t></is></c>';
        $xml[] = '<c r="E' . $rowNum . '" s="21" t="inlineStr"><is><t>-</t></is></c>';
        $xml[] = '<c r="F' . $rowNum . '" s="21" t="inlineStr"><is><t>-</t></is></c>';
        $xml[] = '<c r="G' . $rowNum . '" s="21"><v>' . $totalGreen . '</v></c>';
        $xml[] = '<c r="H' . $rowNum . '" s="21"><v>' . $totalYellow . '</v></c>';
        $xml[] = '<c r="I' . $rowNum . '" s="21"><v>' . $totalRed . '</v></c>';
        $xml[] = '<c r="J' . $rowNum . '" s="21"><v>' . $totalPel . '</v></c>';
        $xml[] = '</row>';

        $xml[] = '</sheetData>';
        $xml[] = '</worksheet>';

        return implode('', $xml);
    }

    /**
     * Convert 1-based column number to Excel column letter
     */
    protected static function colLetter(int $colIndex): string
    {
        $letter = '';
        while ($colIndex > 0) {
            $mod = ($colIndex - 1) % 26;
            $letter = chr(65 + $mod) . $letter;
            $colIndex = intval(($colIndex - $mod) / 26);
        }
        return $letter;
    }
}
