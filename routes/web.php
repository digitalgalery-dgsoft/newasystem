<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InterviewController;
use App\Http\Controllers\PrincipleApprovalController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PrincipleController;
use App\Http\Controllers\FeatureController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\KandidatPortalController;
use App\Http\Controllers\InterviewInhouseController;
use App\Http\Controllers\AiRankingController;
use App\Http\Controllers\AiSettingController;
use App\Http\Controllers\PublicJobController;
use App\Http\Controllers\CbtController;
use App\Http\Controllers\CandidateImportController;
use App\Http\Controllers\JobStatistikController;
use App\Http\Controllers\WorkPlanController;

// ==========================================
// HALAMAN AWAL WEB & LANDING PAGE (v3/index.php)
// ==========================================
Route::get('/', [HomeController::class, 'index'])->name('home.index');
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/index.php', function () { return redirect()->route('home.index'); });

// ==========================================
// AUTENTIKASI LOGIN USER / KARYAWAN (v3/login.php)
// ==========================================
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/login.php', function () { return redirect()->route('login'); });

// ==========================================
// BAGIAN FITUR (FEATURE LAUNCHER HUB)
// ==========================================
Route::get('/fitur', [FeatureController::class, 'index'])->name('fitur.index');

// ==========================================
// MANAJEMEN PROFIL PENGGUNA / KARYAWAN
// ==========================================
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [\App\Http\Controllers\UserProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [\App\Http\Controllers\UserProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [\App\Http\Controllers\UserProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile/avatar', [\App\Http\Controllers\UserProfileController::class, 'destroyAvatar'])->name('profile.avatar.destroy');

    // Switch User: Kembali ke akun user asli (Revert Impersonation)
    Route::match(['get', 'post'], '/switch-back', [EmployeeController::class, 'switchBack'])->name('user.switch-back');
    Route::match(['get', 'post'], '/karyawan/switch-back', [EmployeeController::class, 'switchBack']);
    Route::match(['get', 'post'], '/master/karyawan/switch-back', [EmployeeController::class, 'switchBack']);
});

// ==========================================
// MASTER DATA: KARYAWAN & PRINSIPLE
// ==========================================
Route::middleware(['admin'])->prefix('master')->name('master.')->group(function () {
    // Master Karyawan
    Route::get('/karyawan', [EmployeeController::class, 'index'])->name('karyawan.index');
    Route::post('/karyawan', [EmployeeController::class, 'store'])->name('karyawan.store');
    Route::post('/karyawan/bulk-pimpinan', [EmployeeController::class, 'bulkUpdatePimpinan'])->name('karyawan.bulk-pimpinan');
    Route::put('/karyawan/{id}', [EmployeeController::class, 'update'])->name('karyawan.update');
    Route::get('/karyawan/{id}/resign', [EmployeeController::class, 'resign'])->name('karyawan.resign');
    Route::get('/karyawan/{nik}/switch', [EmployeeController::class, 'switchUser'])->name('karyawan.switch');
    Route::post('/karyawan/{id}/toggle-login', [EmployeeController::class, 'toggleLoginAccess'])->name('karyawan.toggle-login');

    // Master Prinsiple
    Route::get('/prinsiple', [PrincipleController::class, 'index'])->name('prinsiple.index');
    Route::post('/prinsiple', [PrincipleController::class, 'store'])->name('prinsiple.store');
    Route::post('/prinsiple/import-official', [PrincipleController::class, 'reimportOfficial'])->name('prinsiple.reimport');
    Route::put('/prinsiple/{id}', [PrincipleController::class, 'update'])->name('prinsiple.update');
    Route::get('/prinsiple/{id}/toggle', [PrincipleController::class, 'toggleStatus'])->name('prinsiple.toggle');
    Route::delete('/prinsiple/{id}', [PrincipleController::class, 'destroy'])->name('prinsiple.destroy');
    // Master Soal Matematika
    Route::get('/math', [\App\Http\Controllers\MathQuestionController::class, 'index'])->name('math.index');
    Route::post('/math', [\App\Http\Controllers\MathQuestionController::class, 'store'])->name('math.store');
    Route::put('/math/{id}', [\App\Http\Controllers\MathQuestionController::class, 'update'])->name('math.update');
    Route::post('/math/{id}/toggle', [\App\Http\Controllers\MathQuestionController::class, 'toggleStatus'])->name('math.toggle');
    Route::delete('/math/{id}', [\App\Http\Controllers\MathQuestionController::class, 'destroy'])->name('math.destroy');
    // Master Soal Kepribadian (DISC)
    Route::get('/personality', [\App\Http\Controllers\PersonalityQuestionController::class, 'index'])->name('personality.index');
    Route::put('/personality/{id}', [\App\Http\Controllers\PersonalityQuestionController::class, 'update'])->name('personality.update');
});

// ==========================================
// FITUR REKRUTMEN (SUB-MENU INTERVIEW)
// ==========================================
Route::get('/interview', [InterviewController::class, 'index'])->name('interview.index');
Route::get('/interview/{id}', [InterviewController::class, 'show'])->name('interview.show');
Route::post('/interview/{id}/approval', [InterviewController::class, 'storePrincipleApproval'])->name('interview.principleApproval');
Route::post('/interview/{id}/assess', [InterviewController::class, 'storeAssessment'])->name('interview.assess');
Route::post('/interview/{id}/refcek', [InterviewController::class, 'storeRefcek'])->name('interview.refcek');
Route::post('/interview/{id}/kompt', [InterviewController::class, 'storeComputerTest'])->name('interview.kompt');
Route::post('/interview/{id}/remidi', [InterviewController::class, 'setRemidi'])->name('interview.remidi');
Route::post('/interview/{id}/archive', [InterviewController::class, 'archive'])->name('interview.archive');
Route::post('/interview/{id}/edit-principle', [InterviewController::class, 'editPrinciple'])->name('interview.editPrinciple');
Route::post('/interview/{id}/ganti-area', [InterviewController::class, 'gantiArea'])->name('interview.ganti-area');
Route::post('/interview/{id}/alihkan', [InterviewController::class, 'alihkanAS'])->name('interview.alihkan');
Route::post('/interview/sync-odoo', [InterviewController::class, 'syncOdoo'])->name('interview.sync_odoo');
Route::post('/interview/{id}/sync-single-odoo', [InterviewController::class, 'syncSingleOdoo'])->name('interview.sync_single_odoo');

// Submodule Pages
Route::get('/walkinterview', [InterviewController::class, 'walkInterview'])->name('interview.walk');
Route::get('/interviewdone', [InterviewController::class, 'done'])->name('interview.done');
Route::get('/interviewarsip', [InterviewController::class, 'arsip'])->name('interview.arsip');

// Fitur Import Kandidat Walkin (Live Terminal Streaming)
Route::get('/interview/import/template', [CandidateImportController::class, 'downloadTemplate'])->name('interview.import.template');
Route::post('/interview/import/upload', [CandidateImportController::class, 'upload'])->name('interview.import.upload');
Route::get('/interview/import/stream', [CandidateImportController::class, 'stream'])->name('interview.import.stream');
Route::get('/importcalontest', fn() => redirect()->route('interview.index', ['open_import' => 1]));
Route::get('/importkandidatint.php', fn() => redirect()->route('interview.index', ['open_import' => 1]));

// Fitur Tarik Kandidat dari Rekrutmen Odoo via NIK (One-Click Instant Pull)
Route::post('/interview/odoo/lookup-nik', [CandidateImportController::class, 'lookupOdooByNik'])->name('interview.odoo.lookup_nik');
Route::post('/interview/odoo/import-nik', [CandidateImportController::class, 'importOdooByNik'])->name('interview.odoo.import_nik');

// Export Route
Route::get('/export/interview', function () {
    $candidates = \App\Models\Candidate::with('principle')->where('status', 'Active')->get();
    $csvFileName = 'kandidat_interview_' . date('Ymd_His') . '.csv';
    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$csvFileName",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $columns = ['NO', 'NIK', 'NAMA KANDIDAT', 'TANGGAL LAHIR', 'USIA', 'PENDIDIKAN', 'PRINSIPLE', 'JABATAN', 'AREA', 'STATUS'];

    $callback = function() use($candidates, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);
        $no = 1;
        foreach ($candidates as $c) {
            fputcsv($file, [
                $no++,
                $c->nik,
                $c->full_name,
                $c->formatted_birth_date,
                $c->age,
                $c->education,
                $c->principle->name ?? '-',
                $c->applied_job,
                $c->area,
                $c->status,
            ]);
        }
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
})->name('interview.export');

// Placeholders for other features
Route::get('/presensi', function () {
    return view('interview.placeholder', ['pageTitle' => 'Presensi GPS & Kehadiran Live']);
})->name('presensi.index');

Route::get('/cuti', function () {
    return view('interview.placeholder', ['pageTitle' => 'Pengajuan Cuti & Izin']);
})->name('cuti.index');

Route::get('/kpi', function () {
    return view('interview.placeholder', ['pageTitle' => 'Evaluasi Kinerja & KPI']);
})->name('kpi.index');

Route::get('/helpdesk', function () {
    return view('interview.placeholder', ['pageTitle' => 'Helpdesk & Tiket IT Support']);
})->name('helpdesk.index');

// Public Client Approval Portal
Route::get('/approval/{token}', [PrincipleApprovalController::class, 'show'])
    ->where('token', '^[A-Za-z0-9]{20,}$')
    ->name('principle.approval');
Route::post('/approval/{token}/submit', [PrincipleApprovalController::class, 'submit'])
    ->where('token', '^[A-Za-z0-9]{20,}$')
    ->name('principle.approval.submit');

// Download Document PDF (Replikasi v3/printall.php)
Route::get('/interview/{id}/pdf', [InterviewController::class, 'downloadPdf'])->name('interview.pdf');
Route::get('/interview/{id}/print', [InterviewController::class, 'downloadPdf'])->name('interview.print');
Route::get('/printall', function (\Illuminate\Http\Request $request) {
    $id = $request->query('id', 7);
    return redirect()->route('interview.pdf', $id);
});

// ==========================================
// FITUR INPUT JOB REQUIREMENT (v3/inputjob.php)
// ==========================================
Route::get('/inputjob', [JobController::class, 'index'])->name('job.input');
Route::post('/inputjob', [JobController::class, 'store'])->name('job.store');
Route::delete('/inputjob/{id}', [JobController::class, 'destroy'])->name('job.destroy');
Route::get('/inputjob/{id}/toggle', [JobController::class, 'toggleStatus'])->name('job.toggle');

// ==============================================================
// FITUR JOB STATISTIK (v3/job_stats.php & export_job_stats.php)
// ==============================================================
Route::get('/job/statistik', [JobStatistikController::class, 'index'])->name('job.statistik');
Route::get('/job/statistik/export', [JobStatistikController::class, 'export'])->name('job.statistik.export');
Route::get('/job_stats.php', function(\Illuminate\Http\Request $request) {
    return redirect()->route('job.statistik', $request->all());
});
Route::get('/export_job_stats.php', function(\Illuminate\Http\Request $request) {
    return redirect()->route('job.statistik.export', $request->all());
});

// ==============================================================
// FITUR KANDIDAT JOB PORTAL (v3/kandidatportal.php & hasilportal.php)
// ==============================================================
Route::get('/kandidatportal', [KandidatPortalController::class, 'index'])->name('kandidatportal.index');
Route::get('/kandidat-portal', fn() => redirect()->route('kandidatportal.index'));
Route::get('/kandidatportal/export', [KandidatPortalController::class, 'exportExcel'])->name('kandidatportal.export');
Route::post('/kandidatportal/sync-odoo', [KandidatPortalController::class, 'syncOdooRecruitment'])->name('kandidatportal.sync_odoo');
Route::post('/kandidatportal/{id}/sync-single-odoo', [KandidatPortalController::class, 'syncSingleOdoo'])->name('kandidatportal.sync_single_odoo');
Route::get('/kandidatportal/ai-live-status', [KandidatPortalController::class, 'aiLiveStatus'])->name('kandidatportal.ai_live_status');
Route::any('/deploy-webhook', function(\Illuminate\Http\Request $request) {
    $token = $request->query('token') ?? $request->input('token');
    if ($token !== 'dgsoft_rahasia_123') {
        return response('Unauthorized: Token tidak valid.', 403);
    }
    $baseDir = base_path();
    $output = [];
    $output[] = "=== ASYSTEM WEBHOOK DEPLOY ===";
    $output[] = "Waktu: " . date('Y-m-d H:i:s');
    $output[] = "Direktori: " . $baseDir;
    exec("cd {$baseDir} && git config --global --add safe.directory {$baseDir} 2>&1", $output);
    exec("cd {$baseDir} && git pull origin main 2>&1", $output);
    exec("cd {$baseDir} && php artisan optimize:clear 2>&1", $output);
    return response(implode("\n", $output), 200, ['Content-Type' => 'text/plain']);
});
Route::get('/kandidatportal/{id}', [KandidatPortalController::class, 'show'])->name('kandidatportal.show');
Route::post('/kandidatportal/{id}/reset-password', [KandidatPortalController::class, 'resetPassword'])->name('kandidatportal.reset_password');
Route::post('/kandidatportal/{id}/interview', [KandidatPortalController::class, 'updateInterview'])->name('kandidatportal.interview');
Route::post('/kandidatportal/{id}/refcek', [KandidatPortalController::class, 'storeRefcek'])->name('kandidatportal.refcek');
Route::post('/kandidatportal/{id}/kompt', [KandidatPortalController::class, 'storeComputerTest'])->name('kandidatportal.kompt');
Route::get('/kandidatportal/{id}/cetak-ai', [KandidatPortalController::class, 'cetakAiPdf'])->name('kandidatportal.cetak-ai');
Route::get('/interview/{id}/cetak-ai', [InterviewController::class, 'cetakAiPdf'])->name('interview.cetak-ai');
Route::get('/cetak_ai_result.php', function(\Illuminate\Http\Request $request) {
    $id = $request->query('id', 64748);
    return redirect()->route('kandidatportal.cetak-ai', $id);
});
Route::post('/kandidatportal/{id}/alihkan', [KandidatPortalController::class, 'alihkanAS'])->name('kandidatportal.alihkan');
Route::post('/kandidatportal/{id}/ganti-area', [KandidatPortalController::class, 'gantiArea'])->name('kandidatportal.ganti_area');
Route::post('/kandidatportal/{id}/arsipkan', [KandidatPortalController::class, 'arsipkan'])->name('kandidatportal.arsipkan');
Route::post('/kandidatportal/{id}/attachments', [KandidatPortalController::class, 'uploadAttachments'])->name('kandidatportal.attachments.update');
Route::post('/kandidatportal/{id}/analyze-cv', [KandidatPortalController::class, 'analyzeCv'])->name('kandidatportal.analyze_cv');

// Route Fallback Berkas Lampiran (Serve local file if exists, otherwise redirect to legacy server)
Route::get('/lampiran/{filename}', function ($filename) {
    $path = public_path('lampiran/' . $filename);
    if (file_exists($path)) {
        return response()->file($path);
    }
    return redirect('https://asystem.co.id/interview/lampiran/' . rawurlencode($filename), 302);
})->where('filename', '.*');

// ==============================================================
// FITUR KANDIDAT INHOUSE (v3/interviewinhouse.php & hasilinhouse.php)
// ==============================================================
Route::get('/interviewinhouse', [InterviewInhouseController::class, 'index'])->name('interviewinhouse.index');
Route::get('/interviewinhouse/{id}', [InterviewInhouseController::class, 'show'])->name('interviewinhouse.show');
Route::post('/interviewinhouse/{id}/approval', [InterviewInhouseController::class, 'storeApproval'])->name('interviewinhouse.approval');
Route::get('/interviewinhouse/{id}/berkas', [InterviewInhouseController::class, 'downloadBerkas'])->name('interviewinhouse.berkas');

// Fallback legacy link support
Route::get('/interview/inhouse', function() { return redirect()->route('interviewinhouse.index'); });
Route::get('/interview/inhouse/{id}', function($id) { return redirect()->route('interviewinhouse.show', $id); });
Route::get('/hasilinhouse.php', function(\Illuminate\Http\Request $request) {
    $id = $request->query('id', 7);
    return redirect()->route('interviewinhouse.show', $id);
});

// ==============================================================
// FITUR AI CANDIDATE RANKING (v3/ai_ranking.php)
// ==============================================================
Route::get('/airanking', [AiRankingController::class, 'index'])->name('airanking.index');
Route::get('/kandidatportal/ranking', [AiRankingController::class, 'index'])->name('kandidatportal.ranking');
Route::get('/ai_ranking.php', function(\Illuminate\Http\Request $request) {
    $job = $request->query('job');
    return redirect()->route('airanking.index', $job ? ['job' => $job] : []);
});

// ==============================================================
// FITUR PENGATURAN AI & WHATSAPP (v3/ai_settings.php)
// ==============================================================
Route::middleware(['admin'])->group(function () {
    Route::get('/ai-settings', [AiSettingController::class, 'index'])->name('aisetting.index');
    Route::post('/ai-settings', [AiSettingController::class, 'update'])->name('aisetting.update');
    Route::post('/ai-settings/test-gemini', [AiSettingController::class, 'testGemini'])->name('aisetting.test_gemini');
    Route::post('/ai-settings/test-wa', [AiSettingController::class, 'testWa'])->name('aisetting.test_wa');
    Route::post('/ai-settings/remove-expired-key', [AiSettingController::class, 'removeExpiredKey'])->name('aisetting.remove_expired_key');
});
Route::get('/ai_settings.php', function() { return redirect()->route('aisetting.index'); });
Route::get('/cron_ai_analyzer.php', function() {
    require public_path('cron_ai_analyzer.php');
});
Route::get('/v3/cron_ai_analyzer.php', function() {
    require public_path('cron_ai_analyzer.php');
});


// ==============================================================
// FITUR PORTAL LOWONGAN KERJA, DETAIL & APPLY (v3/job.php, job_detail.php, job_apply.php)
// ==============================================================
Route::get('/job', [PublicJobController::class, 'index'])->name('job.public');
Route::get('/job/{id}', [PublicJobController::class, 'show'])->name('job.detail');
Route::get('/job/{id}/apply', [PublicJobController::class, 'applyForm'])->name('job.apply');
Route::post('/job/{id}/apply', [PublicJobController::class, 'submitApply'])->name('job.apply.submit');

// Legacy fallback redirects
Route::get('/job.php', function() { return redirect()->route('job.public'); });
Route::get('/job_detail.php', function(\Illuminate\Http\Request $request) {
    $id = $request->query('id', 1);
    return redirect()->route('job.detail', $id);
});
Route::get('/job_apply.php', function(\Illuminate\Http\Request $request) {
    $id = $request->query('id', 1);
    return redirect()->route('job.apply', $id);
});


// ==============================================================
// INTEGRASI SINKRONISASI ODOO ERP (5 ENTITAS: AMK, AKP, ATK, ABO, ATB)
// ==============================================================
Route::middleware(['admin'])->prefix('odoo-setting')->name('odoo.setting.')->group(function () {
    Route::get('/', [App\Http\Controllers\OdooSettingController::class, 'index'])->name('index');
    Route::put('/{code}', [App\Http\Controllers\OdooSettingController::class, 'update'])->name('update');
    Route::post('/{code}/test', [App\Http\Controllers\OdooSettingController::class, 'testConnection'])->name('test');
    Route::get('/stream-sync-all', [App\Http\Controllers\OdooSettingController::class, 'streamSyncAll'])->name('stream-sync-all');
    Route::get('/stream-sync-nik', [App\Http\Controllers\OdooSettingController::class, 'streamSyncNik'])->name('stream-sync-nik');
    Route::get('/{code}/stream-sync', [App\Http\Controllers\OdooSettingController::class, 'streamSync'])->name('stream-sync');
    Route::post('/{code}/sync', [App\Http\Controllers\OdooSettingController::class, 'sync'])->name('sync');
    Route::post('/sync-all', [App\Http\Controllers\OdooSettingController::class, 'syncAll'])->name('sync-all');
    Route::post('/sync-by-nik', [App\Http\Controllers\OdooSettingController::class, 'syncByNik'])->name('sync-by-nik');
    Route::post('/cleanup-duplicates', [App\Http\Controllers\OdooSettingController::class, 'cleanupDuplicates'])->name('cleanup-duplicates');
});
// Legacy shortcut alias
Route::get('/odoo-sync', function() { return redirect()->route('odoo.setting.index'); });
Route::get('/odoo_setting.php', function() { return redirect()->route('odoo.setting.index'); });

// ==============================================================
// PENGATURAN SISTEM & HAK AKSES (RBAC)
// ==============================================================
Route::middleware(['admin'])->prefix('setting/rbac')->name('setting.rbac.')->group(function () {
    Route::get('/', [\App\Http\Controllers\RbacController::class, 'index'])->name('index');
    Route::post('/matrix', [\App\Http\Controllers\RbacController::class, 'updateRoleMatrix'])->name('matrix.update');
    Route::post('/roles', [\App\Http\Controllers\RbacController::class, 'storeRole'])->name('role.store');
    Route::put('/roles/{id}', [\App\Http\Controllers\RbacController::class, 'updateRole'])->name('role.update');
    Route::delete('/roles/{id}', [\App\Http\Controllers\RbacController::class, 'destroyRole'])->name('role.destroy');
    Route::post('/user', [\App\Http\Controllers\RbacController::class, 'storeUser'])->name('user.store');
    Route::put('/user/{id}', [\App\Http\Controllers\RbacController::class, 'updateUserAccess'])->name('user.update');
    Route::post('/user/{id}/reset-password', [\App\Http\Controllers\RbacController::class, 'resetUserPassword'])->name('user.reset-password');
    Route::get('/search-employees', [\App\Http\Controllers\RbacController::class, 'searchEmployees'])->name('search-employees');
});
// Shortcut aliases
Route::get('/rbac', function() { return redirect()->route('setting.rbac.index'); })->name('rbac.index');


// ==============================================================
// MASTER USER PRINSIPLE (v3/dataprinsiple.php)
// ==============================================================
Route::middleware(['auth'])->group(function () {
    Route::resource('user-prinsiple', App\Http\Controllers\UserPrinsipleController::class)->names('userprinsiple');
    Route::post('user-prinsiple/{id}/send-access', [App\Http\Controllers\UserPrinsipleController::class, 'sendAccess'])->name('userprinsiple.send_access');
});
Route::get('/dataprinsiple', function() { return redirect()->route('userprinsiple.index'); });
Route::get('/dataprinsiple.php', function() { return redirect()->route('userprinsiple.index'); });

// ==============================================================
// MODUL CBT & TEST ONLINE KANDIDAT (Replikasi D:\ASystem\interview)
// ==============================================================
Route::prefix('cbt')->name('cbt.')->group(function () {
    // Autentikasi Peserta CBT
    Route::get('/login', [CbtController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [CbtController::class, 'login'])->name('login.post');
    Route::match(['get', 'post'], '/logout', [CbtController::class, 'logout'])->name('logout');

    // Area Terproteksi Peserta CBT
    Route::middleware(['candidate.auth'])->group(function () {
        // Dashboard
        Route::get('/', [CbtController::class, 'dashboard'])->name('dashboard');
        Route::get('/dashboard', [CbtController::class, 'dashboard'])->name('dashboard.alias');

        // Lengkapi Profil Kandidat
        Route::get('/profile', [CbtController::class, 'profile'])->name('profile');
        Route::post('/profile', [CbtController::class, 'updateProfile'])->name('profile.update');
        Route::post('/experience', [CbtController::class, 'storeExperience'])->name('experience.store');
        Route::delete('/experience/{id}', [CbtController::class, 'destroyExperience'])->name('experience.destroy');

        // 1. Tes Kepribadian (DISC Assessment)
        Route::get('/kepribadian', [CbtController::class, 'kepribadian'])->name('kepribadian');
        Route::post('/kepribadian', [CbtController::class, 'submitKepribadian'])->name('kepribadian.submit');
        Route::get('/kepribadian/result', [CbtController::class, 'kepribadianResult'])->name('kepribadian.result');

        // 2. Tes Matematika & Logika Aritmetika
        Route::get('/matematika', [CbtController::class, 'matematika'])->name('matematika');
        Route::post('/matematika', [CbtController::class, 'submitMatematika'])->name('matematika.submit');
        Route::get('/matematika/result', [CbtController::class, 'matematikaResult'])->name('matematika.result');

        // 3. Tes Komputer & Spreadsheet
        Route::get('/komputer', [CbtController::class, 'komputer'])->name('komputer');
        Route::post('/komputer', [CbtController::class, 'submitKomputer'])->name('komputer.submit');
    });
});

// Shortcut & Legacy Fallback Routes
Route::get('/cbt.php', function() { return redirect()->route('cbt.login'); });
Route::get('/tesonline', function() { return redirect()->route('cbt.login'); });
Route::get('/testonline', function() { return redirect()->route('cbt.login'); });
Route::get('/awalmath', function() { return redirect()->route('cbt.matematika'); });
Route::get('/awalmath.php', function() { return redirect()->route('cbt.matematika'); });
Route::get('/soal.php', function() { return redirect()->route('cbt.matematika'); });
Route::get('/soaltes.php', function() { return redirect()->route('cbt.matematika'); });
Route::get('/soalpsikotes.php', function() { return redirect()->route('cbt.kepribadian'); });
Route::get('/soalkomputer.php', function() { return redirect()->route('cbt.komputer'); });

// ==============================================================
// MODUL INSTALASI SISTEM (Replikasi att-admin-v12)
// ==============================================================
Route::middleware([\App\Http\Middleware\RedirectIfInstalled::class])->group(function () {
    Route::get('/install', [\App\Http\Controllers\InstallController::class, 'index'])->name('install.index');
    Route::post('/install', [\App\Http\Controllers\InstallController::class, 'process'])->name('install.process');
});

// ==============================================================
// SERVING LAMPIRAN & FALLBACK KE SERVER LAMA
// ==============================================================
// 1. Lampiran Profil & CV Kandidat (Fallback: https://asystem.co.id/interview/lampiran/)
Route::get('/lampiran/{filename}', function ($filename) {
    $baseName = basename($filename);
    $localPath = public_path('lampiran/' . $baseName);
    if (file_exists($localPath)) {
        return response()->file($localPath);
    }
    return redirect()->away('https://asystem.co.id/interview/lampiran/' . rawurlencode($baseName));
})->where('filename', '.*')->name('lampiran.show');

// 2. Lampiran Referensi Cek (Fallback: https://asystem.co.id/v3/refcekfile/)
Route::get('/refcekfile/{filename}', function ($filename) {
    $baseName = basename($filename);
    $resolved = \App\Services\LegacyAttachmentService::resolveRefcek($baseName);
    if ($resolved && file_exists($resolved) && !is_dir($resolved)) {
        return response()->file($resolved);
    }
    return redirect()->away('https://asystem.co.id/v3/refcekfile/' . rawurlencode($baseName));
})->where('filename', '.*')->name('refcekfile.show');

// 3. Lampiran Approval Prinsiple (Fallback: https://asystem.co.id/v3/approval/)
Route::get('/approval/{filename}', function ($filename) {
    $baseName = basename($filename);
    $resolved = \App\Services\LegacyAttachmentService::resolveApproval($baseName);
    if ($resolved && file_exists($resolved) && !is_dir($resolved)) {
        return response()->file($resolved);
    }
    if (str_starts_with($baseName, 'ttd_')) {
        return redirect()->away('https://asystem.co.id/v3/prinsiple/ttdfileprinsiple/' . rawurlencode($baseName));
    }
    return redirect()->away('https://asystem.co.id/v3/approval/' . rawurlencode($baseName));
})->where('filename', '.*')->name('approval.show');

// 4. TTD Digital Prinsiple (Fallback: https://asystem.co.id/v3/prinsiple/ttdfileprinsiple/)
Route::get('/prinsiple/ttdfileprinsiple/{filename}', function ($filename) {
    $baseName = basename($filename);
    $resolved = \App\Services\LegacyAttachmentService::resolveApproval($baseName);
    if ($resolved && file_exists($resolved) && !is_dir($resolved)) {
        return response()->file($resolved);
    }
    return redirect()->away('https://asystem.co.id/v3/prinsiple/ttdfileprinsiple/' . rawurlencode($baseName));
})->where('filename', '.*')->name('prinsiple.ttd.show');

// ==========================================
// WORK PLAN & TODOLIST (KANBAN & DAILY ACTIVITY)
// ==========================================
Route::middleware(['auth'])->group(function () {
    Route::get('/workplan', [WorkPlanController::class, 'index'])->name('workplan.index');
    Route::post('/workplan', [WorkPlanController::class, 'store'])->name('workplan.store');
    Route::put('/workplan/{id}', [WorkPlanController::class, 'update'])->name('workplan.update');
    Route::post('/workplan/{id}/move', [WorkPlanController::class, 'moveStatus'])->name('workplan.move');
    Route::post('/workplan/{id}/archive', [WorkPlanController::class, 'archive'])->name('workplan.archive');
    Route::post('/workplan/{id}/unarchive', [WorkPlanController::class, 'unarchive'])->name('workplan.unarchive');
    Route::delete('/workplan/{id}', [WorkPlanController::class, 'destroy'])->name('workplan.destroy');

    Route::get('/workplan/{id}/details', [WorkPlanController::class, 'getDetails'])->name('workplan.details');

    // Subtasks / Checklist
    Route::post('/workplan/{id}/subtasks', [WorkPlanController::class, 'storeSubtask'])->name('workplan.subtasks.store');
    Route::post('/workplan/subtasks/{id}/toggle', [WorkPlanController::class, 'toggleSubtask'])->name('workplan.subtasks.toggle');
    Route::delete('/workplan/subtasks/{id}', [WorkPlanController::class, 'deleteSubtask'])->name('workplan.subtasks.delete');

    // Komentar & Mention
    Route::get('/workplan/{id}/comments', [WorkPlanController::class, 'getComments'])->name('workplan.comments.get');
    Route::post('/workplan/{id}/comments', [WorkPlanController::class, 'storeComment'])->name('workplan.comments.store');

    // Activity Log
    Route::get('/workplan/{id}/activities', [WorkPlanController::class, 'getActivities'])->name('workplan.activities.get');

    // Notifikasi
    Route::get('/workplan-notifications', [WorkPlanController::class, 'getNotifications'])->name('workplan.notifications.get');
    Route::post('/workplan-notifications/{id}/read', [WorkPlanController::class, 'markNotificationRead'])->name('workplan.notifications.read');

    // Alat Bantu Cepat
    Route::get('/workplan/copy-report', [WorkPlanController::class, 'copyReport'])->name('workplan.copy_report');
    Route::get('/workplan/export', [WorkPlanController::class, 'exportExcel'])->name('workplan.export');

    // Daily Work Plan Logs (tb_workplan)
    Route::get('/workplan-daily', [WorkPlanController::class, 'daily'])->name('workplan.daily');
    Route::post('/workplan-daily', [WorkPlanController::class, 'storeDaily'])->name('workplan.daily.store');
    Route::delete('/workplan-daily/{kode}', [WorkPlanController::class, 'destroyDaily'])->name('workplan.daily.destroy');
});

// Redirect sistem lama (wp.php, wptodo.php, todo.php, exportwp.php)
Route::get('/wp.php', fn() => redirect()->route('workplan.index'));
Route::get('/wptodo.php', fn() => redirect()->route('workplan.index'));
Route::get('/todo.php', fn() => redirect()->route('workplan.index'));
Route::get('/exportwp.php', fn() => redirect()->route('workplan.export'));
Route::get('/v3/wp.php', fn() => redirect()->route('workplan.index'));
Route::get('/v3/wptodo.php', fn() => redirect()->route('workplan.index'));
Route::get('/v3/exportwp.php', fn() => redirect()->route('workplan.export'));

// 5. Wildcard Fallback Semua Aset V3 Lama (https://asystem.co.id/v3/{path})
Route::get('/v3/{path}', function ($path) {
    $candidates = [
        public_path('v3/' . $path),
        public_path($path),
        public_path('storage/' . $path),
    ];
    foreach ($candidates as $cand) {
        if (file_exists($cand) && !is_dir($cand)) {
            return response()->file($cand);
        }
    }
    return redirect()->away('https://asystem.co.id/v3/' . $path);
})->where('path', '.*')->name('legacy.v3.fallback');




