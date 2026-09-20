<?php

namespace App\Services;

use ZipArchive;
use Exception;
use Carbon\Carbon;

class WorkPlanXlsxExportService
{
    /**
     * Generate an executive, styled Microsoft Excel (.xlsx) file for Work Plan & ToDoList.
     *
     * @param \Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Collection $tasks
     * @param array $meta (filters: user_filter, smart, status, exporter_name, etc.)
     * @return string Path to the temporary .xlsx file
     */
    public static function generateXlsx($tasks, array $meta = []): string
    {
        $tempDir = sys_get_temp_dir();
        $tempFile = tempnam($tempDir, 'wp_xlsx_') . '.xlsx';

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
        <sheet name="Work Plan &amp; ToDo" sheetId="1" r:id="rId1"/>
    </sheets>
</workbook>';
        $zip->addFromString('xl/workbook.xml', $workbook);

        // 5. xl/styles.xml
        $styles = self::buildStylesXml();
        $zip->addFromString('xl/styles.xml', $styles);

        // 6. xl/worksheets/sheet1.xml
        $sheetXml = self::buildSheetXml($tasks, $meta);
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
    <fonts count="10">
        <!-- 0: Regular 10pt Slate -->
        <font><sz val="10"/><name val="Segoe UI"/><color rgb="FF1E293B"/></font>
        <!-- 1: Bold 14pt Sapphire Title -->
        <font><b/><sz val="14"/><name val="Segoe UI"/><color rgb="FF0F52BA"/></font>
        <!-- 2: Bold 10pt White Header -->
        <font><b/><sz val="10"/><name val="Segoe UI"/><color rgb="FFFFFFFF"/></font>
        <!-- 3: Underline 10pt Blue Link -->
        <font><u/><sz val="10"/><name val="Segoe UI"/><color rgb="FF1D4ED8"/></font>
        <!-- 4: Subtitle 9pt Italic Slate -->
        <font><i/><sz val="9"/><name val="Segoe UI"/><color rgb="FF64748B"/></font>
        <!-- 5: Bold 10pt Emerald (Done) -->
        <font><b/><sz val="10"/><name val="Segoe UI"/><color rgb="FF059669"/></font>
        <!-- 6: Bold 10pt Amber (Medium / Todo) -->
        <font><b/><sz val="10"/><name val="Segoe UI"/><color rgb="FFD97706"/></font>
        <!-- 7: Bold 10pt Rose (High Priority) -->
        <font><b/><sz val="10"/><name val="Segoe UI"/><color rgb="FFE11D48"/></font>
        <!-- 8: Bold 10pt Sky (In Progress) -->
        <font><b/><sz val="10"/><name val="Segoe UI"/><color rgb="FF0284C7"/></font>
        <!-- 9: Bold 10pt Purple (Review) -->
        <font><b/><sz val="10"/><name val="Segoe UI"/><color rgb="FF7C3AED"/></font>
    </fonts>
    <fills count="10">
        <fill><patternFill patternType="none"/></fill>
        <fill><patternFill patternType="gray125"/></fill>
        <!-- 2: Header Dark Slate 800 -->
        <fill><patternFill patternType="solid"><fgColor rgb="FF1E293B"/></patternFill></fill>
        <!-- 3: Zebra Light Slate -->
        <fill><patternFill patternType="solid"><fgColor rgb="FFF8FAFC"/></patternFill></fill>
        <!-- 4: Soft Emerald (Done) -->
        <fill><patternFill patternType="solid"><fgColor rgb="FFECFDF5"/></patternFill></fill>
        <!-- 5: Soft Amber (Medium) -->
        <fill><patternFill patternType="solid"><fgColor rgb="FFFFFBEB"/></patternFill></fill>
        <!-- 6: Soft Rose (High) -->
        <fill><patternFill patternType="solid"><fgColor rgb="FFFFF1F2"/></patternFill></fill>
        <!-- 7: Soft Sky (In Progress) -->
        <fill><patternFill patternType="solid"><fgColor rgb="FFF0F9FF"/></patternFill></fill>
        <!-- 8: Soft Purple (Review) -->
        <fill><patternFill patternType="solid"><fgColor rgb="FFFAF5FF"/></patternFill></fill>
        <!-- 9: Soft Gray -->
        <fill><patternFill patternType="solid"><fgColor rgb="FFF1F5F9"/></patternFill></fill>
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
    <cellXfs count="24">
        <!-- 0: Default -->
        <xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>
        <!-- 1: Title Banner (A1) -->
        <xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1"><alignment vertical="center"/></xf>
        <!-- 2: Subtitle Metadata (A2) -->
        <xf numFmtId="0" fontId="4" fillId="0" borderId="0" xfId="0" applyFont="1"><alignment vertical="center"/></xf>
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
        <!-- 8: Link Regular Center -->
        <xf numFmtId="0" fontId="3" fillId="0" borderId="1" xfId="0" applyFont="1" applyBorder="1"><alignment horizontal="center" vertical="center"/></xf>
        <!-- 9: Link Zebra Center -->
        <xf numFmtId="0" fontId="3" fillId="3" borderId="1" xfId="0" applyFont="1" applyBorder="1"><alignment horizontal="center" vertical="center"/></xf>
        <!-- 10: Wrap Text Regular Left -->
        <xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1"><alignment horizontal="left" vertical="center" wrapText="1"/></xf>
        <!-- 11: Wrap Text Zebra Left -->
        <xf numFmtId="0" fontId="0" fillId="3" borderId="1" xfId="0" applyFill="1" applyBorder="1"><alignment horizontal="left" vertical="center" wrapText="1"/></xf>
        <!-- 12: High Priority (Regular) -->
        <xf numFmtId="0" fontId="7" fillId="6" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1"><alignment horizontal="center" vertical="center"/></xf>
        <!-- 13: High Priority (Zebra) -->
        <xf numFmtId="0" fontId="7" fillId="6" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1"><alignment horizontal="center" vertical="center"/></xf>
        <!-- 14: Medium Priority (Regular) -->
        <xf numFmtId="0" fontId="6" fillId="5" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1"><alignment horizontal="center" vertical="center"/></xf>
        <!-- 15: Medium Priority (Zebra) -->
        <xf numFmtId="0" fontId="6" fillId="5" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1"><alignment horizontal="center" vertical="center"/></xf>
        <!-- 16: Status Done (Regular) -->
        <xf numFmtId="0" fontId="5" fillId="4" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1"><alignment horizontal="center" vertical="center"/></xf>
        <!-- 17: Status Done (Zebra) -->
        <xf numFmtId="0" fontId="5" fillId="4" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1"><alignment horizontal="center" vertical="center"/></xf>
        <!-- 18: Status In Progress (Regular) -->
        <xf numFmtId="0" fontId="8" fillId="7" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1"><alignment horizontal="center" vertical="center"/></xf>
        <!-- 19: Status In Progress (Zebra) -->
        <xf numFmtId="0" fontId="8" fillId="7" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1"><alignment horizontal="center" vertical="center"/></xf>
        <!-- 20: Status Review (Regular) -->
        <xf numFmtId="0" fontId="9" fillId="8" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1"><alignment horizontal="center" vertical="center"/></xf>
        <!-- 21: Status Review (Zebra) -->
        <xf numFmtId="0" fontId="9" fillId="8" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1"><alignment horizontal="center" vertical="center"/></xf>
        <!-- 22: Status Muted (Regular) -->
        <xf numFmtId="0" fontId="4" fillId="9" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1"><alignment horizontal="center" vertical="center"/></xf>
        <!-- 23: Status Muted (Zebra) -->
        <xf numFmtId="0" fontId="4" fillId="9" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1"><alignment horizontal="center" vertical="center"/></xf>
    </cellXfs>
</styleSheet>';
    }

    /**
     * Build Worksheet XML with headers, metadata, columns, and data rows
     */
    protected static function buildSheetXml($tasks, array $meta = []): string
    {
        $nowStr = Carbon::now('Asia/Jakarta')->format('d-m-Y H:i:s') . ' WIB';
        $totalCount = count($tasks);

        // Filter details text
        $filterParts = [];
        if (!empty($meta['status']) && $meta['status'] !== 'all') {
            $filterParts[] = 'Status: ' . strtoupper($meta['status']);
        } else {
            $filterParts[] = 'Status: Semua Status';
        }

        if (!empty($meta['smart'])) {
            $filterParts[] = 'Filter Cepat: ' . ($meta['smart'] === 'high' ? 'Prioritas Tinggi' : ($meta['smart'] === 'overdue' ? 'Tenggat Terlewat' : $meta['smart']));
        }

        if (!empty($meta['user_filter']) && $meta['user_filter'] !== 'all') {
            $filterParts[] = 'Personel: ' . $meta['user_filter'];
        }

        if (!empty($meta['exporter_name'])) {
            $filterParts[] = 'Diexport Oleh: ' . $meta['exporter_name'];
        }

        $metaText = implode(' | ', $filterParts) . ' | Waktu Unduh: ' . $nowStr . ' | Total: ' . number_format($totalCount) . ' Tugas';

        // Column widths definition (13 columns)
        $cols = [
            1  => 6,   // NO
            2  => 12,  // ID TUGAS
            3  => 40,  // JUDUL TUGAS
            4  => 14,  // PRIORITAS
            5  => 16,  // STATUS
            6  => 24,  // PENUGAS (CREATOR)
            7  => 24,  // ASSIGNEE (PENERIMA)
            8  => 24,  // DELEGATOR
            9  => 16,  // TARGET DEADLINE
            10 => 20,  // PROGRESS SUBTASK
            11 => 18,  // TANGGAL INPUT
            12 => 18,  // TANGGAL SELESAI
            13 => 30,  // LINK LAMPIRAN
        ];

        $colsXml = '<cols>';
        foreach ($cols as $colIdx => $w) {
            $colsXml .= '<col min="' . $colIdx . '" max="' . $colIdx . '" width="' . $w . '" customWidth="1"/>';
        }
        $colsXml .= '</cols>';

        $headers = [
            'A' => 'NO',
            'B' => 'ID TUGAS',
            'C' => 'JUDUL TUGAS',
            'D' => 'PRIORITAS',
            'E' => 'STATUS',
            'F' => 'PENUGAS (CREATOR)',
            'G' => 'ASSIGNEE (PENERIMA)',
            'H' => 'DELEGATOR',
            'I' => 'TARGET DEADLINE',
            'J' => 'PROGRESS SUBTASK',
            'K' => 'TANGGAL INPUT',
            'L' => 'TANGGAL SELESAI',
            'M' => 'LINK LAMPIRAN',
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
            <c r="A1" t="inlineStr" s="1"><is><t>' . self::xmlEscape('ASYSTEM - REKAPITULASI WORK PLAN &amp; TO DO LIST') . '</t></is></c>
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

        foreach ($tasks as $task) {
            $isZebra = ($no % 2 === 0);

            // Style indices
            $sLeft   = $isZebra ? 6 : 4;
            $sCenter = $isZebra ? 7 : 5;
            $sWrap   = $isZebra ? 11 : 10;
            $sLink   = $isZebra ? 9 : 8;

            // Subtask progress
            $totalSub = $task->subtasks ? $task->subtasks->count() : 0;
            $compSub = $task->subtasks ? $task->subtasks->where('is_completed', true)->count() : 0;
            $subtaskStr = $totalSub > 0 ? "{$compSub}/{$totalSub} (" . round(($compSub / $totalSub) * 100) . "%)" : "-";

            // Priority Style
            $prioUpper = strtoupper($task->priority ?? '');
            if ($prioUpper === 'HIGH') {
                $sPrio = $isZebra ? 13 : 12;
            } elseif ($prioUpper === 'MEDIUM') {
                $sPrio = $isZebra ? 15 : 14;
            } else {
                $sPrio = $sCenter;
            }

            // Status Style
            $statusUpper = strtoupper($task->status ?? '');
            if ($statusUpper === 'DONE') {
                $sStatus = $isZebra ? 17 : 16;
            } elseif ($statusUpper === 'IN PROGRESS') {
                $sStatus = $isZebra ? 19 : 18;
            } elseif ($statusUpper === 'REVIEW') {
                $sStatus = $isZebra ? 21 : 20;
            } elseif ($statusUpper === 'ARCHIVED') {
                $sStatus = $isZebra ? 23 : 22;
            } else {
                $sStatus = $sCenter;
            }

            // Dates
            $dueDateStr = $task->due_date ? $task->due_date->format('d/m/Y') : '-';
            $inputDateStr = $task->date_input ? $task->date_input->format('d/m/Y H:i') : '-';
            $completedDateStr = $task->date_completed ? $task->date_completed->format('d/m/Y H:i') : '-';

            // Title length determines row height
            $titleLen = mb_strlen($task->title ?? '');
            $rowHeight = ($titleLen > 45) ? 36 : 24;

            $xml .= '<row r="' . $rowNum . '" ht="' . $rowHeight . '">';
            // A: NO
            $xml .= '<c r="A' . $rowNum . '" t="inlineStr" s="' . $sCenter . '"><is><t>' . $no . '</t></is></c>';
            // B: ID TUGAS
            $xml .= '<c r="B' . $rowNum . '" t="inlineStr" s="' . $sCenter . '"><is><t>#' . $task->id . '</t></is></c>';
            // C: JUDUL TUGAS
            $xml .= '<c r="C' . $rowNum . '" t="inlineStr" s="' . $sWrap . '"><is><t>' . self::xmlEscape($task->title) . '</t></is></c>';
            // D: PRIORITAS
            $xml .= '<c r="D' . $rowNum . '" t="inlineStr" s="' . $sPrio . '"><is><t>' . self::xmlEscape($task->priority ?: '-') . '</t></is></c>';
            // E: STATUS
            $xml .= '<c r="E' . $rowNum . '" t="inlineStr" s="' . $sStatus . '"><is><t>' . self::xmlEscape($statusUpper ?: '-') . '</t></is></c>';
            // F: PENUGAS (CREATOR)
            $xml .= '<c r="F' . $rowNum . '" t="inlineStr" s="' . $sLeft . '"><is><t>' . self::xmlEscape($task->user ?: '-') . '</t></is></c>';
            // G: ASSIGNEE (PENERIMA)
            $xml .= '<c r="G' . $rowNum . '" t="inlineStr" s="' . $sLeft . '"><is><t>' . self::xmlEscape($task->assignee ?: '-') . '</t></is></c>';
            // H: DELEGATOR
            $xml .= '<c r="H' . $rowNum . '" t="inlineStr" s="' . $sLeft . '"><is><t>' . self::xmlEscape($task->delegator ?: '-') . '</t></is></c>';
            // I: TARGET DEADLINE
            $xml .= '<c r="I' . $rowNum . '" t="inlineStr" s="' . $sCenter . '"><is><t>' . self::xmlEscape($dueDateStr) . '</t></is></c>';
            // J: PROGRESS SUBTASK
            $xml .= '<c r="J' . $rowNum . '" t="inlineStr" s="' . $sCenter . '"><is><t>' . self::xmlEscape($subtaskStr) . '</t></is></c>';
            // K: TANGGAL INPUT
            $xml .= '<c r="K' . $rowNum . '" t="inlineStr" s="' . $sCenter . '"><is><t>' . self::xmlEscape($inputDateStr) . '</t></is></c>';
            // L: TANGGAL SELESAI
            $xml .= '<c r="L' . $rowNum . '" t="inlineStr" s="' . $sCenter . '"><is><t>' . self::xmlEscape($completedDateStr) . '</t></is></c>';

            // M: LINK LAMPIRAN
            $attUrl = $task->attachment_url;
            if (!empty($attUrl) && trim($attUrl) !== '-') {
                $cleanUrl = trim($attUrl);
                if (!str_starts_with($cleanUrl, 'http://') && !str_starts_with($cleanUrl, 'https://')) {
                    $cleanUrl = 'https://new.asystem.co.id/' . ltrim($cleanUrl, '/');
                }
                $xml .= '<c r="M' . $rowNum . '" s="' . $sLink . '"><f>' . self::xmlEscape('HYPERLINK("' . $cleanUrl . '", "Lihat Lampiran")') . '</f><v>' . self::xmlEscape('Lihat Lampiran') . '</v></c>';
            } else {
                $xml .= '<c r="M' . $rowNum . '" t="inlineStr" s="' . $sCenter . '"><is><t>-</t></is></c>';
            }

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
