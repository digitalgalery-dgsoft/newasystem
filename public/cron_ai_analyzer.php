<?php
// public/cron_ai_analyzer.php
// Wrapper untuk Cronjob via HTTP / Curl / Wget
// Contoh: curl -s "https://new.asystem.co.id/cron_ai_analyzer.php"

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

header('Content-Type: text/plain; charset=utf-8');

$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 3;
$limit = max(1, min(20, $limit));

$candidateId = isset($_GET['candidate_id']) ? intval($_GET['candidate_id']) : null;

$analyzer = app(\App\Services\AiAnalyzerService::class);

echo "=== Cron AI Analyzer HTTP Trigger Started ===\n";
echo "Waktu: " . date('Y-m-d H:i:s') . "\n";

$query = \App\Models\Candidate::where(function ($q) {
    $q->whereNotNull('cv_path')
      ->where('cv_path', '!=', '')
      ->where('cv_path', '!=', '-');
});

if ($candidateId) {
    $query->where('id', $candidateId);
} else {
    $query->where(function ($q) {
        $q->whereNull('ai_score')
          ->orWhere('ai_score', 0);
    })->where(function ($q) {
        $q->whereNull('ai_cv_analysis')
          ->orWhere('ai_cv_analysis', 'not like', '%file_error%');
    })->orderBy('id', 'asc')->limit($limit);

}

$candidates = $query->get();

if ($candidates->isEmpty()) {
    echo "INFO: Tidak ada kandidat skor 0 yang menunggu analisis AI.\n";
    exit;
}

echo "INFO: Ditemukan " . $candidates->count() . " kandidat untuk diproses.\n\n";

$success = 0;
$failed = 0;

foreach ($candidates as $cand) {
    echo "Processing ID #{$cand->id} - {$cand->full_name} ({$cand->applied_job})...\n";
    $res = $analyzer->analyzeCandidate($cand, function ($msg) {
        echo "  $msg\n";
    });

    if ($res['success']) {
        $success++;
        echo "  [OK] Score: {$res['score']} ({$res['category']}) via {$res['provider']}\n\n";
    } else {
        $failed++;
        echo "  [PENDING/FAILED] {$res['message']}\n\n";
    }
}

echo "=== Selesai: Sukses = $success, Tertunda/Limit = $failed ===\n";
