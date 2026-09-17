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
// MASTER DATA: KARYAWAN & PRINSIPLE
// ==========================================
Route::prefix('master')->name('master.')->group(function () {
    // Master Karyawan
    Route::get('/karyawan', [EmployeeController::class, 'index'])->name('karyawan.index');
    Route::post('/karyawan', [EmployeeController::class, 'store'])->name('karyawan.store');
    Route::put('/karyawan/{id}', [EmployeeController::class, 'update'])->name('karyawan.update');
    Route::get('/karyawan/{id}/resign', [EmployeeController::class, 'resign'])->name('karyawan.resign');
    Route::get('/karyawan/{nik}/switch', [EmployeeController::class, 'switchUser'])->name('karyawan.switch');

    // Master Prinsiple
    Route::get('/prinsiple', [PrincipleController::class, 'index'])->name('prinsiple.index');
    Route::post('/prinsiple', [PrincipleController::class, 'store'])->name('prinsiple.store');
    Route::put('/prinsiple/{id}', [PrincipleController::class, 'update'])->name('prinsiple.update');
    Route::get('/prinsiple/{id}/toggle', [PrincipleController::class, 'toggleStatus'])->name('prinsiple.toggle');
    Route::delete('/prinsiple/{id}', [PrincipleController::class, 'destroy'])->name('prinsiple.destroy');
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

// Submodule Pages
Route::get('/walkinterview', [InterviewController::class, 'walkInterview'])->name('interview.walk');
Route::get('/interviewdone', [InterviewController::class, 'done'])->name('interview.done');
Route::get('/interviewarsip', [InterviewController::class, 'arsip'])->name('interview.arsip');

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
Route::get('/approval/{token}', [PrincipleApprovalController::class, 'show'])->name('principle.approval');
Route::post('/approval/{token}/submit', [PrincipleApprovalController::class, 'submit'])->name('principle.approval.submit');

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
// FITUR KANDIDAT JOB PORTAL (v3/kandidatportal.php & hasilportal.php)
// ==============================================================
Route::get('/kandidatportal', [KandidatPortalController::class, 'index'])->name('kandidatportal.index');
Route::get('/kandidatportal/{id}', [KandidatPortalController::class, 'show'])->name('kandidatportal.show');
Route::post('/kandidatportal/{id}/reset-password', [KandidatPortalController::class, 'resetPassword'])->name('kandidatportal.reset_password');
Route::post('/kandidatportal/{id}/interview', [KandidatPortalController::class, 'updateInterview'])->name('kandidatportal.interview');
Route::post('/kandidatportal/{id}/alihkan', [KandidatPortalController::class, 'alihkanAS'])->name('kandidatportal.alihkan');
Route::post('/kandidatportal/{id}/ganti-area', [KandidatPortalController::class, 'gantiArea'])->name('kandidatportal.ganti_area');
Route::post('/kandidatportal/{id}/arsipkan', [KandidatPortalController::class, 'arsipkan'])->name('kandidatportal.arsipkan');

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
Route::get('/ai-settings', [AiSettingController::class, 'index'])->name('aisetting.index');
Route::post('/ai-settings', [AiSettingController::class, 'update'])->name('aisetting.update');
Route::post('/ai-settings/test-gemini', [AiSettingController::class, 'testGemini'])->name('aisetting.test_gemini');
Route::post('/ai-settings/test-wa', [AiSettingController::class, 'testWa'])->name('aisetting.test_wa');
Route::get('/ai_settings.php', function() { return redirect()->route('aisetting.index'); });

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
Route::prefix('odoo-setting')->name('odoo.setting.')->group(function () {
    Route::get('/', [App\Http\Controllers\OdooSettingController::class, 'index'])->name('index');
    Route::put('/{code}', [App\Http\Controllers\OdooSettingController::class, 'update'])->name('update');
    Route::post('/{code}/test', [App\Http\Controllers\OdooSettingController::class, 'testConnection'])->name('test');
    Route::post('/{code}/sync', [App\Http\Controllers\OdooSettingController::class, 'sync'])->name('sync');
    Route::post('/sync-all', [App\Http\Controllers\OdooSettingController::class, 'syncAll'])->name('sync-all');
    Route::post('/cleanup-duplicates', [App\Http\Controllers\OdooSettingController::class, 'cleanupDuplicates'])->name('cleanup-duplicates');
});
// Legacy shortcut alias
Route::get('/odoo-sync', function() { return redirect()->route('odoo.setting.index'); });
Route::get('/odoo_setting.php', function() { return redirect()->route('odoo.setting.index'); });


// ==============================================================
// MASTER USER PRINSIPLE (v3/dataprinsiple.php)
// ==============================================================
Route::resource('user-prinsiple', App\Http\Controllers\UserPrinsipleController::class)->names('userprinsiple');
Route::post('user-prinsiple/{id}/send-access', [App\Http\Controllers\UserPrinsipleController::class, 'sendAccess'])->name('userprinsiple.send_access');
Route::get('/dataprinsiple', function() { return redirect()->route('userprinsiple.index'); });
Route::get('/dataprinsiple.php', function() { return redirect()->route('userprinsiple.index'); });
