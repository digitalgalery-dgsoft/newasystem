<?php

namespace App\Services;

use App\Models\Candidate;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class AiPdfService
{
    /**
     * Generate AI Analysis PDF faithfully following legacy v3/cetak_ai_result.php
     */
    public function generate(Candidate $candidate): string
    {
        $tempDir = storage_path('app/temp-pdf');
        if (!File::exists($tempDir)) {
            File::makeDirectory($tempDir, 0755, true, true);
        }

        $tempFile = $tempDir . '/ai_' . $candidate->id . '_' . uniqid() . '.pdf';
        $cliScript = __DIR__ . '/cli_ai_pdf.php';

        $cmd = 'php ' . escapeshellarg($cliScript) . ' ' . intval($candidate->id) . ' ' . escapeshellarg($tempFile);
        if (function_exists('exec')) {
            @exec($cmd, $output, $returnVar);

            if (isset($returnVar) && $returnVar === 0 && file_exists($tempFile) && filesize($tempFile) > 0) {
                $pdfContent = file_get_contents($tempFile);
                @unlink($tempFile);
                return $pdfContent;
            }
        }

        // Direct rendering
        return $this->renderPdfDirect($candidate);
    }

    /**
     * Generate AI Analysis PDF directly via mPDF
     */
    public function renderPdfDirect(Candidate $candidate): string
    {
        // Require mPDF if not already loaded
        if (!class_exists('\Mpdf\Mpdf')) {
            $possibleAutoloads = [
                'd:/ASystem/v3/vendor/autoload.php',
                base_path('../v3/vendor/autoload.php'),
                'C:/xampp/htdocs/v3/vendor/autoload.php',
            ];
            foreach ($possibleAutoloads as $al) {
                if (file_exists($al)) {
                    require_once $al;
                    break;
                }
            }
        }

        $tempDir = storage_path('app/temp-pdf');
        if (!File::exists($tempDir)) {
            File::makeDirectory($tempDir, 0755, true, true);
        }

        $aiData = $candidate->ai_data;
        $scoreMatch = intval($aiData['evaluation_match_score'] ?? ($candidate->ai_score ?? 0));

        $scoreColorHex = '#0ab39c'; // green
        $scoreBgHex = '#e8f5e9';
        if ($scoreMatch < 60) {
            $scoreColorHex = '#f06548'; // red
            $scoreBgHex = '#fdefea';
        } elseif ($scoreMatch < 85) {
            $scoreColorHex = '#f7b84b'; // yellow
            $scoreBgHex = '#fef4e4';
        }

        // Fetch top other candidates for comparison
        $otherCandidates = Candidate::where('applied_job', $candidate->applied_job)
            ->where('id', '!=', $candidate->id)
            ->whereNotNull('ai_score')
            ->where('ai_score', '>', 0)
            ->orderByDesc('ai_score')
            ->limit(5)
            ->get();

        // Foto Profil
        $fotoHtml = '';
        $photoPath = null;
        if (!empty($candidate->photo_path)) {
            $possiblePhotos = [
                public_path('lampiran/' . $candidate->photo_path),
                public_path($candidate->photo_path),
                'd:/ASystem/interview/lampiran/' . $candidate->photo_path,
                'd:/ASystem/v3/lampiran/' . $candidate->photo_path,
            ];
            foreach ($possiblePhotos as $pp) {
                if (file_exists($pp)) {
                    $photoPath = $pp;
                    break;
                }
            }
        }

        if ($photoPath) {
            $fotoHtml = '<img src="' . htmlspecialchars($photoPath) . '" style="width: 120px; height: 160px; object-fit: cover; border: 2px solid #dee2e6; border-radius: 8px;">';
        } else {
            $fotoHtml = '<div style="width: 120px; height: 160px; background: #f8f9fa; border: 1px dashed #ced4da; border-radius: 8px; text-align: center; color: #adb5bd; line-height: 160px; font-size: 11px;">Pasfoto 3x4</div>';
        }

        $html = '
<style>
    body { font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; font-size: 11px; color: #343a40; line-height: 1.4; }
    h1, h2, h3, h4, h5 { color: #212529; margin-top: 0; font-weight: 600; }
    
    .header-table { width: 100%; border-bottom: 3px solid ' . $scoreColorHex . '; margin-bottom: 15px; padding-bottom: 8px; }
    .header-logo { width: 60%; }
    .header-text { width: 40%; text-align: right; }
    .header-text h2 { margin: 0; color: ' . $scoreColorHex . '; font-size: 18px; text-transform: uppercase; letter-spacing: 1px; }
    .header-text p { margin: 4px 0 0; color: #6c757d; font-size: 10px; }

    /* Profile Section */
    .profile-box { border: 1px solid #e9ecef; border-radius: 8px; padding: 12px; margin-bottom: 15px; background: #ffffff; }
    .section-title { font-size: 12px; font-weight: bold; color: #495057; border-bottom: 2px solid #e9ecef; padding-bottom: 5px; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.5px; }
    
    .profile-table { width: 100%; border-collapse: collapse; }
    .profile-table th { width: 130px; text-align: left; padding: 3px 0; font-weight: 600; color: #6c757d; font-size: 10px; }
    .profile-table td { padding: 3px 0; font-size: 11px; color: #212529; font-weight: 500; }

    /* Grid layout with table columns */
    .mpdf-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
    .mpdf-table td { vertical-align: top; }
    .mpdf-td-box { border: 1px solid #e9ecef; background: #ffffff; border-radius: 8px; padding: 12px; }
    
    .score-container { text-align: center; }
    .score-label { font-size: 10px; text-transform: uppercase; color: #6c757d; font-weight: bold; letter-spacing: 0.5px; display: block; margin-bottom: 4px; }
    .score-value { font-size: 38px; font-weight: 800; color: ' . $scoreColorHex . '; line-height: 1; margin: 4px 0; }
    .score-badge { display: inline-block; font-size: 10px; font-weight: bold; color: ' . $scoreColorHex . '; background: ' . $scoreBgHex . '; padding: 3px 8px; border-radius: 12px; margin-top: 4px; }

    .biodata-title { font-size: 15px; margin-bottom: 8px; color: #2b3a4a; font-weight: bold; }
    .biodata-table { width: 100%; border: none; }
    .biodata-table td.label { width: 85px; color: #6c757d; font-size: 10px; font-weight: 600; padding: 2px 0; border: none; }
    .biodata-table td.value { color: #212529; font-size: 11px; font-weight: 500; padding: 2px 0; border: none; }
    
    .box-title { font-size: 11px; font-weight: 700; margin-bottom: 8px; display: block; padding-bottom: 4px; border-bottom: 1px solid #e9ecef; }
    .box-title.success { color: #0ab39c; border-bottom-color: #0ab39c; }
    .box-title.danger { color: #f06548; border-bottom-color: #f06548; }
    .box-title.primary { color: #3577f1; border-bottom-color: #3577f1; }
    .box-title.info { color: #299cdb; border-bottom-color: #299cdb; }
    
    .list-style { margin: 0; padding-left: 15px; color: #495057; line-height: 1.5; }
    .list-style li { margin-bottom: 6px; text-align: justify; font-size: 10.5px; }
    
    .sub-label { font-size: 9.5px; text-transform: uppercase; color: #888; font-weight: 700; display: block; margin-top: 6px; margin-bottom: 3px; }
    .badge { background: #f1f3f5; color: #495057; border: 1px solid #dee2e6; border-radius: 4px; padding: 3px 6px; font-size: 9.5px; font-weight: 600; margin-right: 4px; margin-bottom: 4px; display: inline-block; }
    
    .comparison-table { width: 100%; border-collapse: collapse; margin-top: 4px; }
    .comparison-table td { padding: 4px 0; border-bottom: 1px solid #f1f3f5; font-size: 10px; }
    .comparison-table tr:last-child td { border-bottom: none; }
    
    .full-box { background: #ffffff; border: 1px solid #e9ecef; border-radius: 8px; padding: 12px; margin-bottom: 12px; }
    
    .alert-box { border-radius: 8px; padding: 10px 14px; margin-bottom: 12px; border-left: 4px solid; }
    .alert-warning { background-color: #fff8e6; border-color: #f7b84b; }
    .alert-warning h4 { color: #d9900f; margin-bottom: 6px; font-size: 12px; }
    .alert-warning p { margin: 0; color: #5a4515; font-size: 10.5px; }
    
    .alert-recommendation { background-color: ' . $scoreBgHex . '; border-color: ' . $scoreColorHex . '; }
    .alert-recommendation h4 { color: ' . $scoreColorHex . '; margin-bottom: 6px; font-size: 12px; }
    .alert-recommendation p { margin: 0; color: #212529; font-weight: 500; font-size: 11px; text-align: justify; }
</style>

<table class="header-table">
    <tr>
        <td class="header-logo">
            <h1 style="color: #2b3a4a; margin: 0; font-size: 20px; letter-spacing: -0.5px;">ASYSTEM HR INTELLIGENCE</h1>
            <span style="color: #878a99; font-size: 10px;">Human Resource AI CV Analysis Report &bull; ESA Groups</span>
        </td>
        <td class="header-text">
            <h2>AI CV Analysis</h2>
            <p>Generated on ' . date('d M Y, H:i') . '</p>
        </td>
    </tr>
</table>

<!-- Profil Kandidat -->
<div class="profile-box">
    <div class="section-title">Profil Kandidat</div>
    <table style="width: 100%; border: none; margin: 0; border-collapse: collapse;">
        <tr>
            <td style="width: 78%; vertical-align: top; padding-right: 12px;">
                <table class="profile-table">
                    <tr><th>No. KTP / NIK</th><td>: ' . htmlspecialchars($candidate->nik) . '</td></tr>
                    <tr><th>Nama Kandidat</th><td>: <strong style="color:#0F52BA;">' . htmlspecialchars($candidate->full_name) . '</strong></td></tr>
                    <tr><th>Alamat KTP</th><td>: ' . htmlspecialchars($candidate->address_ktp ?? '-') . '</td></tr>
                    <tr><th>Usia & Tgl Lahir</th><td>: ' . $candidate->age . ' Tahun (' . ($candidate->formatted_birth_date ?? '-') . ')</td></tr>
                    <tr><th>Pendidikan Terakhir</th><td>: ' . htmlspecialchars($candidate->education ?? '-') . '</td></tr>
                    <tr><th>Kontak / WhatsApp</th><td>: ' . htmlspecialchars($candidate->phone ?? $candidate->whatsapp ?? '-') . '</td></tr>
                    <tr><th>Prinsiple & Area</th><td>: ' . htmlspecialchars($candidate->principle?->name ?? '-') . ' - ' . htmlspecialchars($candidate->area ?? 'JAKARTA') . '</td></tr>
                    <tr><th>Posisi Dilamar</th><td>: <strong>' . htmlspecialchars($candidate->applied_job ?? '-') . '</strong></td></tr>
                    <tr><th>Status Seleksi</th><td>: ' . htmlspecialchars($candidate->status_kandidat ?? $candidate->status ?? 'Proses') . '</td></tr>
                    <tr><th>User Rekruter</th><td>: ' . htmlspecialchars($candidate->user_display_name ?? '-') . '</td></tr>
                    <tr><th>Catatan Tambahan</th><td>: ' . htmlspecialchars($candidate->notes ?? '-') . '</td></tr>
                </table>
            </td>
            <td style="width: 22%; vertical-align: top; text-align: center;">
                ' . $fotoHtml . '
            </td>
        </tr>
    </table>
</div>

<table class="mpdf-table">
    <tr>
        <td style="width: 32%;" class="mpdf-td-box">
            <div class="score-container">
                <span class="score-label">Evaluation Match Score</span>
                <div class="score-value">' . $scoreMatch . '%</div>
                <div class="score-badge">Specification Fit</div>
            </div>
        </td>
        <td style="width: 3%;"></td>
        <td style="width: 65%;" class="mpdf-td-box">
            <span class="score-label" style="margin-bottom: 6px;">Candidate Biodata (AI Extracted)</span>
            <div class="biodata-title">' . htmlspecialchars($aiData['candidate_biodata']['name'] ?? $candidate->full_name) . '</div>
            <table class="biodata-table">
                <tr>
                    <td class="label">Contact</td>
                    <td class="value">: ' . htmlspecialchars($aiData['candidate_biodata']['contact'] ?? ($candidate->phone ?? '-')) . '</td>
                </tr>
                <tr>
                    <td class="label">Education</td>
                    <td class="value">: ' . htmlspecialchars($aiData['candidate_biodata']['education'] ?? ($candidate->education ?? '-')) . '</td>
                </tr>
                <tr>
                    <td class="label">Applied Job</td>
                    <td class="value">: ' . htmlspecialchars($candidate->applied_job ?? '-') . '</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<table class="mpdf-table">
    <tr>
        <td style="width: 48%;" class="mpdf-td-box">
            <span class="box-title success">Core Strengths (Keunggulan Utama)</span>
            <ul class="list-style">';
        foreach ((array)($aiData['core_strengths'] ?? []) as $str) {
            $html .= '<li>' . htmlspecialchars($str) . '</li>';
        }
        if (empty($aiData['core_strengths'])) {
            $html .= '<li style="color:#999; font-style:italic;">Tidak ada data keunggulan teridentifikasi</li>';
        }
        $html .= '  </ul>
        </td>
        <td style="width: 4%;"></td>
        <td style="width: 48%;" class="mpdf-td-box">
            <span class="box-title danger">Weaknesses / Missing Gaps (Kelemahan & Hal Diperhatikan)</span>
            <ul class="list-style">';
        foreach ((array)($aiData['weaknesses'] ?? []) as $weak) {
            $html .= '<li>' . htmlspecialchars($weak) . '</li>';
        }
        if (empty($aiData['weaknesses'])) {
            $html .= '<li style="color:#999; font-style:italic;">Tidak ada catatan kelemahan signifikan</li>';
        }
        $html .= '  </ul>
        </td>
    </tr>
</table>

<table class="mpdf-table">
    <tr>
        <td style="width: 48%;" class="mpdf-td-box">
            <span class="box-title primary">Psychological Traits & Culture Fit</span>
            
            <span class="sub-label">Personality Traits</span>
            <div style="margin-bottom: 8px; line-height: 2;">';
        foreach ((array)($aiData['psychological_traits']['personality'] ?? []) as $trait) {
            $html .= '<span class="badge">' . htmlspecialchars($trait) . '</span>&nbsp;';
        }
        if (empty($aiData['psychological_traits']['personality'])) {
            $html .= '<span style="color:#999; font-size:10px;">-</span>';
        }
        $html .= '  </div>
            
            <span class="sub-label">Work Style</span>
            <div style="margin-bottom: 8px; font-size: 10.5px; text-align: justify; color: #495057;">' . htmlspecialchars($aiData['psychological_traits']['work_style'] ?? '-') . '</div>
            
            <span class="sub-label">Cultural Fit</span>
            <div style="font-size: 10.5px; text-align: justify; color: #495057;">' . htmlspecialchars($aiData['psychological_traits']['cultural_fit'] ?? '-') . '</div>
        </td>
        <td style="width: 4%;"></td>
        <td style="width: 48%;" class="mpdf-td-box">
            <span class="box-title info">Core Skills Evaluation</span>
            <div style="margin-bottom: 12px; line-height: 2;">';
        foreach ((array)($aiData['core_skills'] ?? []) as $skill) {
            $html .= '<span class="badge">' . htmlspecialchars($skill) . '</span>&nbsp;';
        }
        if (empty($aiData['core_skills'])) {
            $html .= '<span style="color:#999; font-size:10px;">-</span>';
        }
        $html .= '  </div>

            <span class="box-title info">Other Candidates Comparison (' . htmlspecialchars($candidate->applied_job ?? '') . ')</span>
            <table class="comparison-table">';
        if ($otherCandidates->count() > 0) {
            foreach ($otherCandidates as $oc) {
                $cColor = '#0ab39c';
                if ($oc->ai_score < 85) $cColor = '#f7b84b';
                if ($oc->ai_score < 60) $cColor = '#f06548';
                $html .= '<tr>
                            <td><strong>' . htmlspecialchars($oc->full_name) . '</strong></td>
                            <td style="text-align: right; color: ' . $cColor . '; font-weight: bold;">' . $oc->ai_score . '% Match</td>
                          </tr>';
            }
        } else {
            $html .= '<tr><td colspan="2" style="text-align: center; color: #adb5bd; font-style: italic;">Belum ada kandidat lain yang dianalisa</td></tr>';
        }
        $html .= '  </table>
        </td>
    </tr>
</table>

<div class="full-box">
    <span class="box-title" style="color: #495057; border-bottom-color: #ced4da;">Work History & Experience</span>
    <ul class="list-style">';
        foreach ((array)($aiData['work_history'] ?? []) as $hist) {
            $html .= '<li>' . htmlspecialchars($hist) . '</li>';
        }
        if (empty($aiData['work_history'])) {
            $html .= '<li style="color:#999; font-style:italic;">Belum memiliki pengalaman kerja (Fresh Graduate)</li>';
        }
        $html .= '</ul>
</div>';

        if (!empty($aiData['data_discrepancy'])) {
            $html .= '
<div class="alert-box alert-warning">
    <h4><i class="fa-solid fa-triangle-exclamation"></i> Data Verification (CV vs Form Input)</h4>
    <p>' . nl2br(htmlspecialchars($aiData['data_discrepancy'])) . '</p>
</div>';
        }

        $html .= '
<div class="alert-box alert-recommendation">
    <h4>Final Recruiter Recommendation</h4>
    <p>' . nl2br(htmlspecialchars($aiData['recommendation'] ?? 'Kandidat dapat dipertimbangkan untuk tahap selanjutnya.')) . '</p>
</div>';

        $mpdf = new \Mpdf\Mpdf([
            'format' => 'A4',
            'margin_top' => 15,
            'margin_bottom' => 15,
            'margin_left' => 15,
            'margin_right' => 15,
            'tempDir' => $tempDir,
        ]);

        $mpdf->SetTitle('AI Analysis - ' . $candidate->full_name);
        $mpdf->showWatermarkText = true;
        $mpdf->WriteHTML('<watermarktext content="CONFIDENTIAL" alpha="0.03" />');
        $mpdf->WriteHTML($html);

        return $mpdf->Output('', 'S');
    }
}
