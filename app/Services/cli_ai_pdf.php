<?php

$candidateId = $argv[1] ?? null;
$outputPath = $argv[2] ?? null;

if (!$candidateId || !$outputPath) {
    fwrite(STDERR, "Missing parameters\n");
    exit(1);
}

require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$candidate = \App\Models\Candidate::with(['principle'])->find($candidateId);
if (!$candidate) {
    fwrite(STDERR, "Candidate not found\n");
    exit(1);
}

$service = new \App\Services\AiPdfService();
$pdfBinary = $service->renderPdfDirect($candidate);

file_put_contents($outputPath, $pdfBinary);
exit(0);
