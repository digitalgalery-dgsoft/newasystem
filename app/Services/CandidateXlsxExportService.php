<?php

namespace App\Services;

use ZipArchive;
use Exception;
use Carbon\Carbon;
use App\Models\Candidate;
use App\Models\Employee;
use App\Models\TbArea;
use App\Models\User;
use Illuminate\Support\Facades\DB;

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
        $nowStr = Carbon::now('Asia/Jakarta')->format('d-m-Y H:i:s') . ' WIB';
        $totalCount = count($candidates);

        // Filter details text
        $filterParts = [];
        $safeDateFmt = function ($d) {
            if (empty($d)) return '';
            $clean = str_replace('/', '-', trim($d));
            try {
                return Carbon::parse($clean)->format('Y-m-d');
            } catch (\Throwable $e) {
                return $d;
            }
        };

        if (!empty($meta['start']) && !empty($meta['end'])) {
            $filterParts[] = 'Periode: ' . $safeDateFmt($meta['start']) . ' s/d ' . $safeDateFmt($meta['end']);
        } elseif (!empty($meta['start'])) {
            $filterParts[] = 'Mulai: ' . $safeDateFmt($meta['start']);
        } elseif (!empty($meta['end'])) {
            $filterParts[] = 'Sampai: ' . $safeDateFmt($meta['end']);
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
            $filterParts[] = 'Diexport Oleh: ' . $meta['recruiter_name'];
        }

        $metaText = implode(' | ', $filterParts) . ' | Waktu Export: ' . $nowStr . ' | Total Data: ' . number_format($totalCount) . ' Kandidat';

        // Column widths definition (27 columns)
        $cols = [
            1  => 6,   // No
            2  => 20,  // Tanggal
            3  => 22,  // No. KTP
            4  => 28,  // Nama Kandidat
            5  => 16,  // Jenis Kelamin
            6  => 30,  // Alamat KTP
            7  => 35,  // Alamat Domisili
            8  => 14,  // Tgl. Lahir
            9  => 12,  // Height (cm)
            10 => 12,  // Weight (kg)
            11 => 12,  // Religion
            12 => 20,  // Pendidikan Terakhir
            13 => 20,  // Phone / WA
            14 => 18,  // Area
            15 => 14,  // Region
            16 => 22,  // Secondary City
            17 => 32,  // Nama AS
            18 => 35,  // Principle
            19 => 28,  // Applied Job
            20 => 45,  // Ringkasan Pengalaman Kerja
            21 => 18,  // Info Lowongan
            22 => 25,  // Foto Profil
            23 => 25,  // File CV
            24 => 20,  // CV Analisa AI
            25 => 16,  // Status Kandidat
            26 => 18,  // Kategori Kandidat
            27 => 12,  // AI Score
        ];

        $colsXml = '<cols>';
        foreach ($cols as $colIdx => $w) {
            $colsXml .= '<col min="' . $colIdx . '" max="' . $colIdx . '" width="' . $w . '" customWidth="1"/>';
        }
        $colsXml .= '</cols>';

        // Header column titles (27 columns matching legacy export + 3 additions)
        $headers = [
            'A'  => 'No',
            'B'  => 'Tanggal',
            'C'  => 'No. KTP',
            'D'  => 'Nama Kandidat',
            'E'  => 'Jenis Kelamin',
            'F'  => 'Alamat KTP',
            'G'  => 'Alamat Domisili',
            'H'  => 'Tgl. Lahir',
            'I'  => 'Height (cm)',
            'J'  => 'Weight (kg)',
            'K'  => 'Religion',
            'L'  => 'Pendidikan Terakhir',
            'M'  => 'Phone / WA',
            'N'  => 'Area',
            'O'  => 'Region',
            'P'  => 'Secondary City',
            'Q'  => 'Nama AS',
            'R'  => 'Principle',
            'S'  => 'Applied Job',
            'T'  => 'Ringkasan Pengalaman Kerja',
            'U'  => 'Info Lowongan',
            'V'  => 'Foto Profil',
            'W'  => 'File CV',
            'X'  => 'CV Analisa AI',
            'Y'  => 'Status Kandidat',
            'Z'  => 'Kategori Kandidat',
            'AA' => 'AI Score',
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
            <c r="A1" t="inlineStr" s="1"><is><t>' . self::xmlEscape('DATA KANDIDAT JOB PORTAL') . '</t></is></c>
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

        // Area to Region mapping (Mengacu ke Master tb_area 43 Area resmi ESA Groups)
        $regionMap = TbArea::getRegionMap();

        // Production Server Base URL: always prioritize https://new.asystem.co.id
        $baseUrl = !empty($meta['base_url']) ? $meta['base_url'] : null;
        if (empty($baseUrl) || str_contains($baseUrl, 'localhost') || str_contains($baseUrl, '127.0.0.1')) {
            $baseUrl = 'https://new.asystem.co.id';
        }
        $baseUrl = rtrim($baseUrl, '/');

        // Pre-fetch Data Karyawan (Employee) berdasarkan email untuk kolom Nama AS & Jabatan
        $asEmails = [];
        foreach ($candidates as $c) {
            $raw = trim($c->useras ?? '');
            if (!empty($raw) && str_contains($raw, '@')) {
                $asEmails[] = strtolower($raw);
            }
            if ($c->recruiter && !empty($c->recruiter->email)) {
                $asEmails[] = strtolower(trim($c->recruiter->email));
            }
        }
        $asEmails = array_values(array_unique($asEmails));

        $asLookup = [];
        if (!empty($asEmails)) {
            // Ambil Nama Lengkap & Jabatan dari Data Karyawan (Employee) berdasarkan email
            try {
                $emps = Employee::whereIn(DB::raw('LOWER(TRIM(email))'), $asEmails)
                    ->whereNotNull('nama_karyawan')
                    ->where('nama_karyawan', '!=', '')
                    ->orderByRaw("CASE WHEN status = 'Aktiv' THEN 0 ELSE 1 END")
                    ->orderBy('id', 'desc')
                    ->get(['email', 'nama_karyawan', 'jabatan']);
                foreach ($emps as $e) {
                    $k = strtolower(trim($e->email));
                    if (!empty($e->nama_karyawan) && !isset($asLookup[$k])) {
                        $asLookup[$k] = [
                            'name' => trim($e->nama_karyawan),
                            'jabatan' => trim($e->jabatan ?? ''),
                        ];
                    }
                }
            } catch (\Throwable $e) {
                // safeguard
            }
        }

        // Data Rows
        $rowNum = 5;
        $no = 1;

        foreach ($candidates as $c) {
            $isZebra = ($no % 2 === 0);
            $sLeft = $isZebra ? 6 : 4;
            $sCenter = $isZebra ? 7 : 5;
            $sWrap = $isZebra ? 11 : 10;
            $sLink = $isZebra ? 9 : 8;

            // Formatted values
            $tglDaftar = $c->created_at ? $c->created_at->format('Y-m-d H:i:s') : '-';
            
            // Format NIK: text string
            $nik = trim((string)$c->nik);
            if (empty($nik)) {
                $nik = '-';
            }

            $nama = trim((string)$c->full_name);
            $gender = !empty($c->gender) ? trim($c->gender) : '-';
            $alamatKtp = !empty($c->address_ktp) ? trim($c->address_ktp) : '-';
            $alamatDom = !empty($c->address_domicile) ? trim($c->address_domicile) : '-';
            $tglLahir = $c->birth_date ? $c->birth_date->format('Y-m-d') : '-';
            $height = !empty($c->height) ? $c->height : '-';
            $weight = !empty($c->weight) ? $c->weight : '-';
            $religion = !empty($c->religion) ? trim($c->religion) : '';
            $pendidikan = !empty($c->education) ? trim($c->education) : '-';

            // Phone
            $phone = !empty($c->phone) ? trim($c->phone) : (!empty($c->whatsapp) ? trim($c->whatsapp) : '-');
            $area = !empty($c->area) ? trim($c->area) : '-';
            
            // Region & Secondary City
            $cleanArea = (!empty($area) && $area !== '-') ? TbArea::getCanonicalAreaName($area) : '-';
            $region = TbArea::resolveRegion($cleanArea);
            if ($region === '-' && !empty($c->region)) {
                $region = $c->region;
            }
            if ($region === '-') {
                $region = 'Region 1';
            }
            $secCity = !empty($c->city_domicile) ? trim($c->city_domicile) : (!empty($c->penempatan) ? trim($c->penempatan) : $cleanArea);

            // Q: Nama AS & Jabatan (Ambil dari Data Karyawan berdasarkan email)
            $namaAs = '-';
            $rawAs = !empty($c->useras) ? trim($c->useras) : ($c->recruiter ? ($c->recruiter->email ?: $c->recruiter->name) : '');
            if (!empty($rawAs)) {
                $lowerAs = strtolower($rawAs);
                $recEmail = ($c->recruiter && !empty($c->recruiter->email)) ? strtolower(trim($c->recruiter->email)) : null;

                if (isset($asLookup[$lowerAs])) {
                    $emp = $asLookup[$lowerAs];
                    $namaAs = !empty($emp['jabatan']) ? "{$emp['name']} ({$emp['jabatan']})" : $emp['name'];
                } elseif ($recEmail && isset($asLookup[$recEmail])) {
                    $emp = $asLookup[$recEmail];
                    $namaAs = !empty($emp['jabatan']) ? "{$emp['name']} ({$emp['jabatan']})" : $emp['name'];
                } else {
                    // Jika nama tidak ditemukan di Data Karyawan:
                    // Fallback JANGAN dibuat 'Administrator ESA' atau Administrator apapun.
                    // Tampilkan apa adanya email yang tercantum, atau tanda '-' jika kosong/admin text.
                    $isAdminText = (stripos($rawAs, 'administrator') !== false || stripos($rawAs, 'admin esa') !== false);
                    if ($isAdminText) {
                        if ($recEmail) {
                            $namaAs = $recEmail;
                        } elseif (str_contains($rawAs, '@')) {
                            $namaAs = $rawAs;
                        } else {
                            $namaAs = '-';
                        }
                    } elseif (str_contains($rawAs, '@')) {
                        // Tampilkan apa adanya saja berupa email yang tercantum
                        $namaAs = $rawAs;
                    } elseif (!empty($c->recruiter) && !empty($c->recruiter->email)) {
                        $namaAs = trim($c->recruiter->email);
                    } else {
                        $namaAs = ucwords(strtolower($rawAs));
                    }
                }
            }

            // Principle
            $prinName = '-';
            if (!empty($c->principle)) {
                if (is_string($c->principle) && str_starts_with(trim($c->principle), '{')) {
                    $decoded = json_decode($c->principle, true);
                    $prinName = $decoded['name'] ?? $c->principle;
                } else {
                    $prinName = is_string($c->principle) ? $c->principle : ($c->principle->name ?? '-');
                }
            } elseif ($c->principleRelation) {
                $prinName = $c->principleRelation->name;
            }

            $posisi = !empty($c->applied_job) ? trim($c->applied_job) : '-';

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

            // Info Lowongan
            $infoLoker = !empty($c->info_lowongan) ? trim($c->info_lowongan) : (!empty($c->source_type) ? trim($c->source_type) : '-');
            if (strtolower($infoLoker) === 'job_portal') {
                $infoLoker = 'Job Portal';
            }

            // Foto Profil
            $photoFile = trim((string)$c->photo_path);
            $photoUrl = null;
            $photoName = '-';
            if (!empty($photoFile) && $photoFile !== '-') {
                $photoName = basename($photoFile);
                $photoUrl = self::resolveLampiranUrl($photoFile, $baseUrl);
            }

            // File CV
            $cvFile = trim((string)$c->cv_path);
            $cvUrl = null;
            $cvName = '-';
            $hasCv = false;
            if (!empty($cvFile) && $cvFile !== '-') {
                $cvName = basename($cvFile);
                $cvUrl = self::resolveLampiranUrl($cvFile, $baseUrl);
                $hasCv = true;
            }

            // AI Analisis: HANYA DITAMPILKAN JIKA KANDIDAT MEMILIKI BERKAS CV VALID
            $hasAiAnalysis = $hasCv && ($c->ai_score !== null && $c->ai_score > 0);
            $pdfUrl = $hasAiAnalysis ? ($baseUrl . '/kandidatportal/' . $c->id . '/cetak-ai') : null;

            $statusKandidat = !empty($c->status_kandidat) ? trim($c->status_kandidat) : 'Baru';

            // Kategori AI & Style
            $kategori = '-';
            $sKategori = $sCenter;
            if ($hasAiAnalysis && !empty($c->kategori_kandidat)) {
                $kategori = trim($c->kategori_kandidat);
                if (strcasecmp($kategori, 'Green') === 0) {
                    $sKategori = $isZebra ? 13 : 12;
                } elseif (strcasecmp($kategori, 'Yellow') === 0) {
                    $sKategori = $isZebra ? 15 : 14;
                } elseif (strcasecmp($kategori, 'Red') === 0) {
                    $sKategori = $isZebra ? 17 : 16;
                }
            }

            $aiScore = $hasAiAnalysis ? ($c->ai_score . '%') : '-';

            // Determine row height
            $rowHeight = (mb_strlen($expSummary) > 60 || mb_strlen($alamatDom) > 50) ? 38 : 24;

            $xml .= '<row r="' . $rowNum . '" ht="' . $rowHeight . '">';
            // A: No
            $xml .= '<c r="A' . $rowNum . '" t="inlineStr" s="' . $sCenter . '"><is><t>' . $no . '</t></is></c>';
            // B: Tanggal
            $xml .= '<c r="B' . $rowNum . '" t="inlineStr" s="' . $sCenter . '"><is><t>' . self::xmlEscape($tglDaftar) . '</t></is></c>';
            // C: NIK
            $xml .= '<c r="C' . $rowNum . '" t="inlineStr" s="' . $sCenter . '"><is><t>' . self::xmlEscape($nik) . '</t></is></c>';
            // D: Nama Kandidat
            $xml .= '<c r="D' . $rowNum . '" t="inlineStr" s="' . $sLeft . '"><is><t>' . self::xmlEscape($nama) . '</t></is></c>';
            // E: Jenis Kelamin
            $xml .= '<c r="E' . $rowNum . '" t="inlineStr" s="' . $sCenter . '"><is><t>' . self::xmlEscape($gender) . '</t></is></c>';
            // F: Alamat KTP
            $xml .= '<c r="F' . $rowNum . '" t="inlineStr" s="' . $sWrap . '"><is><t>' . self::xmlEscape($alamatKtp) . '</t></is></c>';
            // G: Alamat Domisili
            $xml .= '<c r="G' . $rowNum . '" t="inlineStr" s="' . $sWrap . '"><is><t>' . self::xmlEscape($alamatDom) . '</t></is></c>';
            // H: Tgl. Lahir
            $xml .= '<c r="H' . $rowNum . '" t="inlineStr" s="' . $sCenter . '"><is><t>' . self::xmlEscape($tglLahir) . '</t></is></c>';
            // I: Height (cm)
            $xml .= '<c r="I' . $rowNum . '" t="inlineStr" s="' . $sCenter . '"><is><t>' . self::xmlEscape($height) . '</t></is></c>';
            // J: Weight (kg)
            $xml .= '<c r="J' . $rowNum . '" t="inlineStr" s="' . $sCenter . '"><is><t>' . self::xmlEscape($weight) . '</t></is></c>';
            // K: Religion
            $xml .= '<c r="K' . $rowNum . '" t="inlineStr" s="' . $sCenter . '"><is><t>' . self::xmlEscape($religion) . '</t></is></c>';
            // L: Pendidikan Terakhir
            $xml .= '<c r="L' . $rowNum . '" t="inlineStr" s="' . $sCenter . '"><is><t>' . self::xmlEscape($pendidikan) . '</t></is></c>';
            // M: Phone / WA
            $xml .= '<c r="M' . $rowNum . '" t="inlineStr" s="' . $sCenter . '"><is><t>' . self::xmlEscape($phone) . '</t></is></c>';
            // N: Area
            $xml .= '<c r="N' . $rowNum . '" t="inlineStr" s="' . $sLeft . '"><is><t>' . self::xmlEscape($area) . '</t></is></c>';
            // O: Region
            $xml .= '<c r="O' . $rowNum . '" t="inlineStr" s="' . $sCenter . '"><is><t>' . self::xmlEscape($region) . '</t></is></c>';
            // P: Secondary City
            $xml .= '<c r="P' . $rowNum . '" t="inlineStr" s="' . $sLeft . '"><is><t>' . self::xmlEscape($secCity) . '</t></is></c>';
            // Q: Nama AS
            $xml .= '<c r="Q' . $rowNum . '" t="inlineStr" s="' . $sLeft . '"><is><t>' . self::xmlEscape($namaAs) . '</t></is></c>';
            // R: Principle
            $xml .= '<c r="R' . $rowNum . '" t="inlineStr" s="' . $sLeft . '"><is><t>' . self::xmlEscape($prinName) . '</t></is></c>';
            // S: Applied Job
            $xml .= '<c r="S' . $rowNum . '" t="inlineStr" s="' . $sLeft . '"><is><t>' . self::xmlEscape($posisi) . '</t></is></c>';
            // T: Ringkasan Pengalaman Kerja
            $xml .= '<c r="T' . $rowNum . '" t="inlineStr" s="' . $sWrap . '"><is><t>' . self::xmlEscape($expSummary) . '</t></is></c>';
            // U: Info Lowongan
            $xml .= '<c r="U' . $rowNum . '" t="inlineStr" s="' . $sCenter . '"><is><t>' . self::xmlEscape($infoLoker) . '</t></is></c>';
            
            // V: Foto Profil
            if ($photoUrl) {
                $xml .= '<c r="V' . $rowNum . '" s="' . $sLink . '"><f>' . self::xmlEscape('HYPERLINK("' . $photoUrl . '", "' . $photoName . '")') . '</f><v>' . self::xmlEscape($photoName) . '</v></c>';
            } else {
                $xml .= '<c r="V' . $rowNum . '" t="inlineStr" s="' . $sCenter . '"><is><t>-</t></is></c>';
            }

            // W: File CV
            if ($cvUrl) {
                $xml .= '<c r="W' . $rowNum . '" s="' . $sLink . '"><f>' . self::xmlEscape('HYPERLINK("' . $cvUrl . '", "' . $cvName . '")') . '</f><v>' . self::xmlEscape($cvName) . '</v></c>';
            } else {
                $xml .= '<c r="W' . $rowNum . '" t="inlineStr" s="' . $sCenter . '"><is><t>-</t></is></c>';
            }

            // X: CV Analisa AI (Hyperlink ke Server Production PDF HANYA JIKA ADA CV & ANALISIS AI)
            if ($hasAiAnalysis && $pdfUrl) {
                $xml .= '<c r="X' . $rowNum . '" s="' . $sLink . '"><f>' . self::xmlEscape('HYPERLINK("' . $pdfUrl . '", "CV Analisa AI")') . '</f><v>CV Analisa AI</v></c>';
            } else {
                $xml .= '<c r="X' . $rowNum . '" t="inlineStr" s="' . $sCenter . '"><is><t>-</t></is></c>';
            }

            // Y: Status Kandidat
            $xml .= '<c r="Y' . $rowNum . '" t="inlineStr" s="' . $sCenter . '"><is><t>' . self::xmlEscape($statusKandidat) . '</t></is></c>';
            // Z: Kategori Kandidat
            $xml .= '<c r="Z' . $rowNum . '" t="inlineStr" s="' . $sKategori . '"><is><t>' . self::xmlEscape($kategori) . '</t></is></c>';
            // AA: AI Score
            $xml .= '<c r="AA' . $rowNum . '" t="inlineStr" s="' . $sCenter . '"><is><t>' . self::xmlEscape($aiScore) . '</t></is></c>';

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

    /**
     * Resolusi URL Berkas Lampiran (Foto & CV) dengan fallback cerdas:
     * 1. Jika ada di lokal / new.asystem.co.id (public/lampiran) -> gunakan URL server baru
     * 2. Jika tidak ada di lokal (merupakan data lama hasil impor) -> fallback ke server lama (asystem.co.id/interview/lampiran)
     */
    public static function resolveLampiranUrl(?string $file, string $baseUrl): ?string
    {
        if (empty($file) || trim($file) === '-') {
            return null;
        }
        $file = trim($file);
        $baseName = basename($file);

        // 1. Cek apakah berkas ada di server lokal (new.asystem.co.id)
        if (file_exists(public_path('lampiran/' . $baseName))) {
            return rtrim($baseUrl, '/') . '/lampiran/' . rawurlencode($baseName);
        }
        if (file_exists(public_path('storage/' . $baseName))) {
            return rtrim($baseUrl, '/') . '/storage/' . rawurlencode($baseName);
        }
        if (file_exists(public_path($file))) {
            return rtrim($baseUrl, '/') . '/' . ltrim($file, '/');
        }

        // 2. Fallback ke server lama jika berkas berada di server lama (data impor legacy)
        return 'https://asystem.co.id/interview/lampiran/' . rawurlencode($baseName);
    }
}
