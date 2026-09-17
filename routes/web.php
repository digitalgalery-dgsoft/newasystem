<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InterviewController;
use App\Http\Controllers\PrincipleApprovalController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PrincipleController;
use App\Http\Controllers\FeatureController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\KandidatPortalController;

// Root redirect to Fitur Hub
Route::get('/', function () {
    return redirect()->route('fitur.index');
});

Route::get('/home', function () {
    return redirect()->route('fitur.index');
});

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
Route::post('/interview/{id}/alihkan', [InterviewController::class, 'alihkanAS'])->name('interview.alihkan');
Route::post('/interview/{id}/ganti-area', [InterviewController::class, 'gantiArea'])->name('interview.ganti-area');

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