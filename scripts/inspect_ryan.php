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

$allSurabayaJobs = JobSpec::where('job_area', 'Surabaya')
    ->select('id', 'job_title', 'created_by', 'status')
    ->get();
echo "\n=== ALL SURABAYA JOBS ===\n";
foreach ($allSurabayaJobs as $sj) {
    echo "ID: {$sj->id} | {$sj->job_title} | Creator: {$sj->created_by} | Status: {$sj->status}\n";
}

$candSurabayaUseras = Candidate::where('area', 'Surabaya')
    ->select('useras', DB::raw('count(*) as count'))
    ->groupBy('useras')
    ->orderByDesc('count')
    ->take(15)
    ->get();
echo "\n=== TOP USERAS FOR SURABAYA CANDIDATES ===\n";
foreach ($candSurabayaUseras as $cu) {
    echo "- useras: '{$cu->useras}' => count: {$cu->count}\n";
}



// Check candidates who applied to Ryan's jobs
$ryanJobTitles = JobSpec::where(function($q) use ($user, $identifiers) {
    $q->whereIn(DB::raw('LOWER(TRIM(created_by))'), $identifiers)
      ->orWhereRaw('LOWER(TRIM(created_by)) = ?', ['ryanfitrianurrachman02@gmail.com']);
})->pluck('job_title')->toArray();

echo "\n=== RYAN JOB TITLES ===\n" . json_encode($ryanJobTitles) . "\n";

$candsOnRyanJobs = Candidate::whereIn('applied_job', $ryanJobTitles)
    ->select('useras', 'recruiter_id', 'jenis', DB::raw('count(*) as count'))
    ->groupBy('useras', 'recruiter_id', 'jenis')
    ->get();

echo "\n=== CANDIDATES APPLYING TO RYAN'S JOBS ===\n";
foreach ($candsOnRyanJobs as $crj) {
    echo "- useras: '{$crj->useras}' | recruiter_id: '{$crj->recruiter_id}' | jenis: '{$crj->jenis}' => count: {$crj->count}\n";
}


$cTotal = Candidate::count();
echo "Total candidates in DB: {$cTotal}\n";

// Let's find ANY candidate where useras contains 'surabaya' or 'ryan' or recruiter_id = 88
$allMatches = Candidate::where(function($q) {
    $q->where('useras', 'like', '%ryan%')
      ->orWhere('useras', 'like', '%as.surabaya%')
      ->orWhere('recruiter_id', 88);
})->select('useras', 'recruiter_id', DB::raw('count(*) as count'))->groupBy('useras', 'recruiter_id')->get();

echo "Grouped matches in candidates:\n";
foreach ($allMatches as $m) {
    echo "- useras: '{$m->useras}' | recruiter_id: '{$m->recruiter_id}' | count: {$m->count}\n";
}


$byJenis = Candidate::where(function ($q) use ($user) {
    $q->whereRaw('LOWER(TRIM(useras)) = ?', ['ryanfitrianurrachman02@gmail.com'])
      ->orWhereRaw('LOWER(TRIM(useras)) = ?', ['as.surabaya@arina.co.id'])
      ->orWhere('recruiter_id', $user->id);
})->select('jenis', DB::raw('count(*) as count'))->groupBy('jenis')->get();

echo "\n=== BY JENIS ===\n";
foreach ($byJenis as $bj) {
    echo "- Jenis: '{$bj->jenis}' => {$bj->count}\n";
}

$emptyJenis = Candidate::where('recruiter_id', $user->id)
    ->where(function($q) {
        $q->whereNull('jenis')->orWhere('jenis', '');
    })
    ->select('id', 'full_name', 'applied_job', 'area', 'useras', 'recruiter_id', 'jenis', 'status', 'status_kandidat', 'created_at')
    ->orderBy('id', 'desc')
    ->take(10)
    ->get();

echo "\n=== SAMPLE EMPTY JENIS CANDIDATES ===\n";
foreach ($emptyJenis as $ej) {
    echo "ID: {$ej->id} | {$ej->full_name} | Job: {$ej->applied_job} | Status: {$ej->status} | StatKand: {$ej->status_kandidat} | Created: {$ej->created_at}\n";
}

$jobPortal = Candidate::where('recruiter_id', $user->id)
    ->where('jenis', 'Job Portal')
    ->count();

$interviewCand = Candidate::where('recruiter_id', $user->id)
    ->whereNotIn('status', ['Arsip', 'archived'])
    ->where(function ($q) {
        $q->whereNull('status_kandidat')->orWhere('status_kandidat', '!=', 'Arsip');
    })
    ->where(function ($q) {
        $q->whereNull('jenis')->orWhere('jenis', '');
    })
    ->count();

echo "\n=== SIMULATING KANDIDAT PORTAL QUERY ===\n";
echo "Scope override: " . var_export($user->scope_override, true) . "\n";
echo "Allowed areas: " . json_encode($user->allowed_areas) . "\n";
echo "Allowed principles: " . json_encode($user->allowed_principles) . "\n";
echo "Cover all areas: " . var_export($user->cover_all_areas, true) . "\n";
echo "Handle all principles: " . var_export($user->handle_all_principles, true) . "\n";

$kpQuery = Candidate::where('jenis', 'Job Portal');
$kpQuery->where(function ($q) use ($user, $identifiers) {
    $q->whereIn(DB::raw('LOWER(TRIM(useras))'), $identifiers)
      ->orWhere('recruiter_id', $user->id);
});
echo "Portal count before role scope: " . (clone $kpQuery)->count() . "\n";
$user->applyRoleScopeToCandidates($kpQuery);
echo "Portal count after role scope: " . $kpQuery->count() . "\n";

$distinctAreas = Candidate::where(function ($q) use ($user, $identifiers) {
    $q->whereIn(DB::raw('LOWER(TRIM(useras))'), $identifiers)
      ->orWhere('recruiter_id', $user->id);
})->select('area', DB::raw('count(*) as c'))->groupBy('area')->get();

echo "\n=== CANDIDATE AREAS ===\n";
foreach ($distinctAreas as $da) {
    echo "- Area: '{$da->area}' => {$da->c}\n";
}

$distinctPrinciples = Candidate::where(function ($q) use ($user, $identifiers) {
    $q->whereIn(DB::raw('LOWER(TRIM(useras))'), $identifiers)
      ->orWhere('recruiter_id', $user->id);
})->select('principle', DB::raw('count(*) as c'))->groupBy('principle')->get();

echo "\n=== CANDIDATE PRINCIPLES ===\n";
foreach ($distinctPrinciples as $dp) {
    echo "- Principle: '{$dp->principle}' => {$dp->c}\n";
}


