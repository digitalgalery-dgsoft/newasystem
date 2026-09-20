<?php

namespace App\Services;

use ZipArchive;
use Exception;
use Carbon\Carbon;

class ActivityLogXlsxExportService
{
    /**
     * Generate an executive, styled Microsoft Excel (.xlsx) file for Activity Logs.
     *
     * @param \Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Collection $logs
     * @param array $meta (filters: module, action, user, search, start, end, exporter_name, exporter_jabatan)
     * @return string Path to the temporary .xlsx file
     */
    public static function generateXlsx($logs, array $meta = []): string
    {
        $tempDir = sys_get_temp_dir();
        $tempFile = tempnam($tempDir, 'act_xlsx_') . '.xlsx';

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
        <sheet name="Activity Logs" sheetId="1" r:id="rId1"/>
    </sheets>
</workbook>';
        $zip->addFromString('xl/workbook.xml', $workbook);

        // 5. xl/styles.xml
        $styles = self::buildStylesXml();
        $zip->addFromString('xl/styles.xml', $styles);

        // 6. xl/worksheets/sheet1.xml
        $sheetXml = self::buildSheetXml($logs, $meta);
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
        <!-- 1: Bold 14pt Sapphire Title -->
        <font><b/><sz val="14"/><name val="Segoe UI"/><color rgb="FF0F52BA"/></font>
        <!-- 2: Bold 10pt White Header -->
        <font><b/><sz val="10"/><name val="Segoe UI"/><color rgb="FFFFFFFF"/></font>
        <!-- 3: Subtitle 9pt Italic Slate -->
        <font><i/><sz val="9"/><name val="Segoe UI"/><color rgb="FF64748B"/></font>
        <!-- 4: Bold 10pt Emerald (Create/Login) -->
        <font><b/><sz val="10"/><name val="Segoe UI"/><color rgb="FF059669"/></font>
        <!-- 5: Bold 10pt Rose (Delete/Failed) -->
        <font><b/><sz val="10"/><name val="Segoe UI"/><color rgb="FFE11D48"/></font>
        <!-- 6: Bold 10pt Amber (Update) -->
        <font><b/><sz val="10"/><name val="Segoe UI"/><color rgb="FFD97706"/></font>
        <!-- 7: Bold 10pt Sky (Export/Sync) -->
        <font><b/><sz val="10"/><name val="Segoe UI"/><color rgb="FF0284C7"/></font>
    </fonts>
    <fills count="8">
        <fill><patternFill patternType="none"/></fill>
        <fill><patternFill patternType="gray125"/></fill>
        <!-- 2: Header Dark Sapphire 800 -->
        <fill><patternFill patternType="solid"><fgColor rgb="FF0F52BA"/></patternFill></fill>
        <!-- 3: Zebra Light Slate -->
        <fill><patternFill patternType="solid"><fgColor rgb="FFF8FAFC"/></patternFill></fill>
        <!-- 4: Soft Emerald -->
        <fill><patternFill patternType="solid"><fgColor rgb="FFECFDF5"/></patternFill></fill>
        <!-- 5: Soft Rose -->
        <fill><patternFill patternType="solid"><fgColor rgb="FFFFF1F2"/></patternFill></fill>
        <!-- 6: Soft Amber -->
        <fill><patternFill patternType="solid"><fgColor rgb="FFFFFBEB"/></patternFill></fill>
        <!-- 7: Soft Sky -->
        <fill><patternFill patternType="solid"><fgColor rgb="FFF0F9FF"/></patternFill></fill>
    </fills>
    <borders count="2">
        <border><left/><right/><top/><bottom/><diagonal/></border>
        <!-- 1: Thin border light gray -->
        <border>
            <left style="thin"><color rgb="FFCBD5E1"/></left>
            <right style="thin"><color rgb="FFCBD5E1"/></right>
            <top style="thin"><color rgb="FFCBD5E1"/></top>
            <bottom style="thin"><color rgb="FFCBD5E1"/></bottom>
        </border>
    </borders>
    <cellStyleXfs count="1">
        <xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>
    </cellStyleXfs>
    <cellXfs count="16">
        <!-- 0: Default -->
        <xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>
        <!-- 1: Title Banner (A1) -->
        <xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1"><alignment vertical="center"/></xf>
        <!-- 2: Subtitle Metadata (A2) -->
        <xf numFmtId="0" fontId="3" fillId="0" borderId="0" xfId="0" applyFont="1"><alignment vertical="center"/></xf>
        <!-- 3: Table Header Row -->
        <xf numFmtId="0" fontId="2" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>
        <!-- 4: Data Regular Left -->
        <xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1"><alignment horizontal="left" vertical="center"/></xf>
        <!-- 5: Data Regular Center -->
        <xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1"><alignment horizontal="center" vertical="center"/></xf>
        <!-- 6: Data Zebra Left -->
        <xf numFmtId="0" fontId="0" fillId="3" borderId="1" xfId="0" applyFill="1" applyBorder="1"><alignment horizontal="left" vertical="center"/></xf>
        <!-- 7: Data Zebra Center -->
        <xf numFmtId="0" fontId="0" fillId="3" borderId="1" xfId="0" applyFill="1" applyBorder="1"><alignment horizontal="center" vertical="center"/></xf>
        <!-- 8: Wrap Text Regular Left -->
        <xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1"><alignment horizontal="left" vertical="center" wrapText="1"/></xf>
        <!-- 9: Wrap Text Zebra Left -->
        <xf numFmtId="0" fontId="0" fillId="3" borderId="1" xfId="0" applyFill="1" applyBorder="1"><alignment horizontal="left" vertical="center" wrapText="1"/></xf>
        <!-- 10: Action Create / Success (Regular) -->
        <xf numFmtId="0" fontId="4" fillId="4" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1"><alignment horizontal="center" vertical="center"/></xf>
        <!-- 11: Action Create / Success (Zebra) -->
        <xf numFmtId="0" fontId="4" fillId="4" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1"><alignment horizontal="center" vertical="center"/></xf>
        <!-- 12: Action Delete / Failed (Regular) -->
        <xf numFmtId="0" fontId="5" fillId="5" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1"><alignment horizontal="center" vertical="center"/></xf>
        <!-- 13: Action Delete / Failed (Zebra) -->
        <xf numFmtId="0" fontId="5" fillId="5" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1"><alignment horizontal="center" vertical="center"/></xf>
        <!-- 14: Action Update (Regular) -->
        <xf numFmtId="0" fontId="6" fillId="6" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1"><alignment horizontal="center" vertical="center"/></xf>
        <!-- 15: Action Update (Zebra) -->
        <xf numFmtId="0" fontId="6" fillId="6" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1"><alignment horizontal="center" vertical="center"/></xf>
    </cellXfs>
</styleSheet>';
    }

    /**
     * Build Worksheet XML with headers, metadata, columns, and data rows
     */
    protected static function buildSheetXml($logs, array $meta = []): string
    {
        $nowStr = Carbon::now('Asia/Jakarta')->format('d-m-Y H:i:s') . ' WIB';
        $totalCount = count($logs);

        // Subtitle details
        $exporterName = $meta['exporter_name'] ?? 'Administrator';
        $exporterJabatan = $meta['exporter_jabatan'] ?? 'Administrator';

        $metaText = 'Waktu Unduh: ' . $nowStr . ' | Oleh: ' . $exporterName . ' (' . $exporterJabatan . ') | Total: ' . number_format($totalCount) . ' Rekaman Aktivitas';

        // Column widths definition (8 columns)
        $cols = [
            1 => 6,   // No
            2 => 20,  // Waktu (WIB)
            3 => 30,  // Pengguna / Pelaku
            4 => 22,  // Jabatan
            5 => 16,  // Aksi
            6 => 18,  // Modul
            7 => 50,  // Deskripsi Aktivitas
            8 => 28,  // IP Address & Device
        ];

        $colsXml = '<cols>';
        foreach ($cols as $colIdx => $w) {
            $colsXml .= '<col min="' . $colIdx . '" max="' . $colIdx . '" width="' . $w . '" customWidth="1"/>';
        }
        $colsXml .= '</cols>';

        $headers = [
            'A' => 'No',
            'B' => 'Waktu (WIB)',
            'C' => 'Pengguna / Pelaku',
            'D' => 'Jabatan',
            'E' => 'Aksi',
            'F' => 'Modul',
            'G' => 'Deskripsi Aktivitas',
            'H' => 'IP Address & Device',
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
            <c r="A1" t="inlineStr" s="1"><is><t>' . self::xmlEscape('LAPORAN LOG AKTIVITAS & AUDIT TRAIL ASYSTEM') . '</t></is></c>
        </row>
        <!-- ROW 2: SUBTITLE METADATA -->
        <row r="2" ht="18">
            <c r="A2" t="inlineStr" s="2"><is><t>' . self::xmlEscape($metaText) . '</t></is></c>
        </row>
        <!-- ROW 3: SPACING -->
        <row r="3" ht="8"/>
        <!-- ROW 4: TABLE HEADER -->
        <row r="4" ht="28">';

        foreach ($headers as $colLetter => $title) {
            $xml .= '<c r="' . $colLetter . '4" t="inlineStr" s="3"><is><t>' . self::xmlEscape($title) . '</t></is></c>';
        }

        $xml .= '</row>';

        // Data Rows
        $no = 1;
        $rowNum = 5;

        foreach ($logs as $item) {
            $isZebra = ($no % 2 === 0);

            // Style indices
            $sLeft   = $isZebra ? 6 : 4;
            $sCenter = $isZebra ? 7 : 5;
            $sWrap   = $isZebra ? 9 : 8;

            // Action style
            $actionUpper = strtoupper($item->action ?? '');
            if (in_array($actionUpper, ['CREATE', 'LOGIN', 'RESTORE'])) {
                $sAction = $isZebra ? 11 : 10;
            } elseif (in_array($actionUpper, ['DELETE', 'FAILED', 'ARCHIVE'])) {
                $sAction = $isZebra ? 13 : 12;
            } elseif (in_array($actionUpper, ['UPDATE', 'RESET_PASSWORD', 'PERMISSION_CHANGE'])) {
                $sAction = $isZebra ? 15 : 14;
            } else {
                $sAction = $sCenter;
            }

            $waktuStr = $item->created_at ? $item->created_at->format('Y-m-d H:i:s') : '-';
            $userStr = $item->user_name . ($item->user_email ? " ({$item->user_email})" : '');
            $deviceStr = ($item->ip_address ?? '-') . ' / ' . ($item->device ?? '-');

            $descLen = mb_strlen($item->description ?? '');
            $rowHeight = ($descLen > 50) ? 36 : 24;

            $xml .= '<row r="' . $rowNum . '" ht="' . $rowHeight . '">';
            // A: No
            $xml .= '<c r="A' . $rowNum . '" t="inlineStr" s="' . $sCenter . '"><is><t>' . $no . '</t></is></c>';
            // B: Waktu (WIB)
            $xml .= '<c r="B' . $rowNum . '" t="inlineStr" s="' . $sCenter . '"><is><t>' . self::xmlEscape($waktuStr) . '</t></is></c>';
            // C: Pengguna / Pelaku
            $xml .= '<c r="C' . $rowNum . '" t="inlineStr" s="' . $sLeft . '"><is><t>' . self::xmlEscape($userStr) . '</t></is></c>';
            // D: Jabatan
            $xml .= '<c r="D' . $rowNum . '" t="inlineStr" s="' . $sLeft . '"><is><t>' . self::xmlEscape($item->user_jabatan ?: '-') . '</t></is></c>';
            // E: Aksi
            $xml .= '<c r="E' . $rowNum . '" t="inlineStr" s="' . $sAction . '"><is><t>' . self::xmlEscape($item->action ?: '-') . '</t></is></c>';
            // F: Modul
            $xml .= '<c r="F' . $rowNum . '" t="inlineStr" s="' . $sCenter . '"><is><t>' . self::xmlEscape($item->module ?: '-') . '</t></is></c>';
            // G: Deskripsi Aktivitas
            $xml .= '<c r="G' . $rowNum . '" t="inlineStr" s="' . $sWrap . '"><is><t>' . self::xmlEscape($item->description ?: '-') . '</t></is></c>';
            // H: IP Address & Device
            $xml .= '<c r="H' . $rowNum . '" t="inlineStr" s="' . $sLeft . '"><is><t>' . self::xmlEscape($deviceStr) . '</t></is></c>';

            $xml .= '</row>';

            $no++;
            $rowNum++;
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
