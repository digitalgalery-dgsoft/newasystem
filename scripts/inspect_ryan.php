<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Candidate;
use App\Models\JobSpec;
use App\Http\Controllers\KandidatPortalController;
use Illuminate\Support\Facades\DB;

$user = User::where('email', 'as.surabaya@arina.co.id')
    ->orWhere('email', 'ryanfitrianurrachman02@gmail.com')
    ->first();

if (!$user) {
    echo "USER NOT FOUND\n";
    exit(0);
}

echo "=== USER DATA ===\n";
echo "ID: {$user->id}\n";
echo "Name: {$user->name}\n";
echo "Email: {$user->email}\n";
echo "Role: {$user->role}\n";
echo "Area: {$user->area}\n";
echo "Email Aliases: " . json_encode($user->email_aliases) . "\n";

$identifiers = KandidatPortalController::resolveUserIdentifiers($user);
echo "Resolved Identifiers: " . json_encode($identifiers) . "\n";

$candOld = Candidate::whereRaw('LOWER(TRIM(useras)) = ?', ['ryanfitrianurrachman02@gmail.com'])->count();
$candNew = Candidate::whereRaw('LOWER(TRIM(useras)) = ?', ['as.surabaya@arina.co.id'])->count();
$candRec = Candidate::where('recruiter_id', $user->id)->count();
$candLike = Candidate::whereRaw('LOWER(TRIM(useras)) LIKE ?', ['%ryan%'])->count();

echo "\n=== CANDIDATES COUNT ===\n";
echo "Count with useras = ryanfitrianurrachman02@gmail.com: {$candOld}\n";
echo "Count with useras = as.surabaya@arina.co.id: {$candNew}\n";
echo "Count with recruiter_id = {$user->id}: {$candRec}\n";
echo "Count with useras LIKE %ryan%: {$candLike}\n";

$byJenis = Candidate::where(function ($q) use ($user) {
    $q->whereRaw('LOWER(TRIM(useras)) = ?', ['ryanfitrianurrachman02@gmail.com'])
      ->orWhereRaw('LOWER(TRIM(useras)) = ?', ['as.surabaya@arina.co.id'])
      ->orWhere('recruiter_id', $user->id);
})->select('jenis', DB::raw('count(*) as count'))->groupBy('jenis')->get();

echo "\n=== BY JENIS ===\n";
foreach ($byJenis as $bj) {
    echo "- Jenis: '{$bj->jenis}' => {$bj->count}\n";
}

$jobsOld = JobSpec::whereRaw('LOWER(TRIM(created_by)) = ?', ['ryanfitrianurrachman02@gmail.com'])->count();
$jobsNew = JobSpec::whereRaw('LOWER(TRIM(created_by)) = ?', ['as.surabaya@arina.co.id'])->count();
echo "\n=== JOBS COUNT ===\n";
echo "JobSpec with created_by = ryanfitrianurrachman02@gmail.com: {$jobsOld}\n";
echo "JobSpec with created_by = as.surabaya@arina.co.id: {$jobsNew}\n";
