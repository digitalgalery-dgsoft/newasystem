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
use App\Http\Controllers\WorkPlanChatController;
use App\Http\Controllers\Helpdesk\HelpdeskDashboardController;
use App\Http\Controllers\Helpdesk\HelpdeskTicketController;
use App\Http\Controllers\Helpdesk\HelpdeskDivisionController;
use App\Http\Controllers\Helpdesk\HelpdeskCannedController;
use App\Http\Controllers\Helpdesk\HelpdeskTemplateController;

// ==============================================================
// 1. RUTE PUBLIK (DAPAT DIAKSES GUEST / TANPA LOGIN)
// ==============================================================

// Halaman Depan Web & Landing Page (v3/index.php)
Route::get('/', [HomeController::class, 'index'])->name('home.index');
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/index.php', fn() => redirect()->route('home.index'));

// Autentikasi Pengguna (Login & Logout)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/login.php', fn() => redirect()->route('login'));
Route::get('/refresh-csrf', fn() => response()->json(['token' => csrf_token()]))->name('refresh-csrf');

// Progressive Web App (PWA) Manifest, Service Worker & Offline Routes
Route::get('/manifest.json', function () {
    return response()->file(public_path('manifest.json'), [
        'Content-Type' => 'application/manifest+json; charset=utf-8',
        'Cache-Control' => 'public, max-age=3600',
    ]);
})->name('pwa.manifest');

Route::get('/sw.js', function () {
    return response()->file(public_path('sw.js'), [
        'Content-Type' => 'application/javascript; charset=utf-8',
        'Service-Worker-Allowed' => '/',
        'Cache-Control' => 'no-cache, no-store, must-revalidate',
    ]);
})->name('pwa.sw');

Route::get('/offline', function () {
    return response()->file(public_path('offline.html'), [
        'Content-Type' => 'text/html; charset=utf-8',
    ]);
})->name('pwa.offline');

// Live Chat Bantuan Login & Forgot Password (Publik)
Route::prefix('auth/chat')->name('auth.chat.')->group(function () {
    Route::post('/check-nik', [\App\Http\Controllers\AuthChatController::class, 'checkNik'])->name('check-nik');
    Route::get('/poll', [\App\Http\Controllers\AuthChatController::class, 'poll'])->name('poll');
    Route::post('/send-message', [\App\Http\Controllers\AuthChatController::class, 'sendMessage'])->name('send-message');
});

// Portal Lowongan Kerja, Detail & Form Apply Pelamar (Publik)
Route::get('/job', [PublicJobController::class, 'index'])->name('job.public');
Route::get('/job/{id}', [PublicJobController::class, 'show'])->whereNumber('id')->name('job.detail');
Route::get('/job/{id}/apply', [PublicJobController::class, 'applyForm'])->whereNumber('id')->name('job.apply');
Route::post('/job/{id}/apply', [PublicJobController::class, 'submitApply'])->whereNumber('id')->name('job.apply.submit');
Route::get('/job.php', fn() => redirect()->route('job.public'));
Route::get('/job_detail.php', fn(\Illuminate\Http\Request $r) => redirect()->route('job.detail', $r->query('id', 1)));
Route::get('/job_apply.php', fn(\Illuminate\Http\Request $r) => redirect()->route('job.apply', $r->query('id', 1)));

// Portal Persetujuan Client / Prinsiple via Secret Token (Publik 20+ Karakter)
Route::get('/approval/{token}', [PrincipleApprovalController::class, 'show'])
    ->where('token', '^[A-Za-z0-9]{20,}$')
    ->name('principle.approval');
Route::post('/approval/{token}/submit', [PrincipleApprovalController::class, 'submit'])
    ->where('token', '^[A-Za-z0-9]{20,}$')
    ->name('principle.approval.submit');

// Modul CBT & Tes Online Pelamar (Terproteksi Sesi Kandidat)
Route::prefix('cbt')->name('cbt.')->group(function () {
    Route::get('/login', [CbtController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [CbtController::class, 'login'])->name('login.post');
    Route::match(['get', 'post'], '/logout', [CbtController::class, 'logout'])->name('logout');

    Route::middleware(['candidate.auth'])->group(function () {
        Route::get('/', [CbtController::class, 'dashboard'])->name('dashboard');
        Route::get('/dashboard', [CbtController::class, 'dashboard'])->name('dashboard.alias');
        Route::get('/profile', [CbtController::class, 'profile'])->name('profile');
        Route::post('/profile', [CbtController::class, 'updateProfile'])->name('profile.update');
        Route::post('/experience', [CbtController::class, 'storeExperience'])->name('experience.store');
        Route::delete('/experience/{id}', [CbtController::class, 'destroyExperience'])->name('experience.destroy');
        Route::get('/kepribadian', [CbtController::class, 'kepribadian'])->name('kepribadian');
        Route::post('/kepribadian', [CbtController::class, 'submitKepribadian'])->name('kepribadian.submit');
        Route::get('/kepribadian/result', [CbtController::class, 'kepribadianResult'])->name('kepribadian.result');
        Route::get('/matematika', [CbtController::class, 'matematika'])->name('matematika');
        Route::post('/matematika', [CbtController::class, 'submitMatematika'])->name('matematika.submit');
        Route::get('/matematika/result', [CbtController::class, 'matematikaResult'])->name('matematika.result');
        Route::get('/komputer', [CbtController::class, 'komputer'])->name('komputer');
        Route::post('/komputer', [CbtController::class, 'submitKomputer'])->name('komputer.submit');
    });
});
Route::get('/cbt.php', fn() => redirect()->route('cbt.login'));
Route::get('/tesonline', fn() => redirect()->route('cbt.login'));
Route::get('/testonline', fn() => redirect()->route('cbt.login'));
Route::get('/awalmath', fn() => redirect()->route('cbt.matematika'));
Route::get('/awalmath.php', fn() => redirect()->route('cbt.matematika'));
Route::get('/soal.php', fn() => redirect()->route('cbt.matematika'));
Route::get('/soaltes.php', fn() => redirect()->route('cbt.matematika'));
Route::get('/soalpsikotes.php', fn() => redirect()->route('cbt.kepribadian'));
Route::get('/soalkomputer.php', fn() => redirect()->route('cbt.komputer'));

// Modul Instalasi Sistem
Route::middleware([\App\Http\Middleware\RedirectIfInstalled::class])->group(function () {
    Route::get('/install', [\App\Http\Controllers\InstallController::class, 'index'])->name('install.index');
    Route::post('/install', [\App\Http\Controllers\InstallController::class, 'process'])->name('install.process');
});

// ==============================================================
// 1.1 BERKAS LAMPIRAN & DOKUMEN TERPROTEKSI (WAJIB LOGIN)
// ==============================================================
Route::get('/lampiran/{filename}', [\App\Http\Controllers\AttachmentController::class, 'showLampiran'])->where('filename', '.*')->name('lampiran.show');
Route::get('/refcekfile/{filename}', [\App\Http\Controllers\AttachmentController::class, 'showRefcek'])->where('filename', '.*')->name('refcekfile.show');
Route::get('/approval/{filename}', [\App\Http\Controllers\AttachmentController::class, 'showApproval'])->where('filename', '.*')->name('approval.show');
Route::get('/prinsiple/ttdfileprinsiple/{filename}', [\App\Http\Controllers\AttachmentController::class, 'showTtdPrinciple'])->where('filename', '.*')->name('prinsiple.ttd.show');

// Deployment Webhook (Terproteksi Token Query Rahasia)
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

// Cron Jobs
Route::get('/cron_ai_analyzer.php', function() {
    require public_path('cron_ai_analyzer.php');
});
Route::get('/v3/cron_ai_analyzer.php', function() {
    require public_path('cron_ai_analyzer.php');
});


// ==============================================================
// 2. RUTE TERPROTEKSI AUTENTIKASI (HANYA BISA DIAKSES JIKA USER LOGIN)
// ==============================================================
Route::middleware(['auth'])->group(function () {

    // --- FITUR HUB (LAUNCHER PORTAL) ---
    Route::get('/fitur', [FeatureController::class, 'index'])->name('fitur.index');

    // --- MANAJEMEN PROFIL & SWITCH USER ---
    Route::get('/profile', [\App\Http\Controllers\UserProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [\App\Http\Controllers\UserProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [\App\Http\Controllers\UserProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile/avatar', [\App\Http\Controllers\UserProfileController::class, 'destroyAvatar'])->name('profile.avatar.destroy');
    Route::match(['get', 'post'], '/switch-back', [EmployeeController::class, 'switchBack'])->name('user.switch-back');
    Route::match(['get', 'post'], '/karyawan/switch-back', [EmployeeController::class, 'switchBack']);
    Route::match(['get', 'post'], '/master/karyawan/switch-back', [EmployeeController::class, 'switchBack']);

    // --- FITUR REKRUTMEN (INTERVIEW UTAMA) ---
    Route::get('/interview', [InterviewController::class, 'index'])->name('interview.index');
    Route::get('/interview/{id}', [InterviewController::class, 'show'])->name('interview.show');
    Route::post('/interview/{id}/approval', [InterviewController::class, 'storePrincipleApproval'])->name('interview.principleApproval');
    Route::post('/interview/{id}/inhouse-approval', [InterviewController::class, 'storeInhouseApproval'])->name('interview.inhouse_approval');
    Route::post('/interview/{id}/assess', [InterviewController::class, 'storeAssessment'])->name('interview.assess');
    Route::post('/interview/{id}/refcek', [InterviewController::class, 'storeRefcek'])->name('interview.refcek');
    Route::post('/interview/{id}/kompt', [InterviewController::class, 'storeComputerTest'])->name('interview.kompt');
    Route::post('/interview/{id}/remidi', [InterviewController::class, 'setRemidi'])->name('interview.remidi');
    Route::post('/interview/{id}/archive', [InterviewController::class, 'archive'])->name('interview.archive');
    Route::post('/interview/{id}/unarchive', [InterviewController::class, 'unarchive'])->name('interview.unarchive');
    Route::post('/interview/bulk-archive', [InterviewController::class, 'bulkArchive'])->name('interview.bulk_archive');
    Route::post('/interview/bulk-unarchive', [InterviewController::class, 'bulkUnarchive'])->name('interview.bulk_unarchive');
    Route::post('/interview/{id}/edit-principle', [InterviewController::class, 'editPrinciple'])->name('interview.editPrinciple');
    Route::post('/interview/{id}/ganti-area', [InterviewController::class, 'gantiArea'])->name('interview.ganti-area');
    Route::post('/interview/{id}/alihkan', [InterviewController::class, 'alihkanAS'])->name('interview.alihkan');
    Route::post('/interview/sync-odoo', [InterviewController::class, 'syncOdoo'])->name('interview.sync_odoo');
    Route::post('/interview/{id}/sync-single-odoo', [InterviewController::class, 'syncSingleOdoo'])->name('interview.sync_single_odoo');

    // Submodul Interview Walkin
    Route::get('/walkinterview', [InterviewController::class, 'walkInterview'])->name('interview.walk');
    Route::get('/walkinterview/create', [InterviewController::class, 'createWalkInterview'])->name('interview.walk.create');
    Route::get('/walkinterview/register', [InterviewController::class, 'createWalkInterview'])->name('interview.walk.register');
    Route::post('/walkinterview', [InterviewController::class, 'storeWalkInterview'])->name('interview.walk.store');
    Route::get('/walkinterview/export', [InterviewController::class, 'exportWalkInterview'])->name('interview.walk.export');
    Route::get('/interviewdone', [InterviewController::class, 'done'])->name('interview.done');
    Route::get('/interviewarsip', [InterviewController::class, 'arsip'])->name('interview.arsip');

    // Import Kandidat Walkin
    Route::get('/interview/import/template', [CandidateImportController::class, 'downloadTemplate'])->name('interview.import.template');
    Route::post('/interview/import/upload', [CandidateImportController::class, 'upload'])->name('interview.import.upload');
    Route::get('/interview/import/stream', [CandidateImportController::class, 'stream'])->name('interview.import.stream');
    Route::get('/importcalontest', fn() => redirect()->route('interview.index', ['open_import' => 1]));
    Route::get('/importkandidatint.php', fn() => redirect()->route('interview.index', ['open_import' => 1]));

    // Tarik Kandidat Odoo via NIK
    Route::match(['get', 'post'], '/interview/odoo/lookup-nik', [CandidateImportController::class, 'lookupOdooByNik'])->name('interview.odoo.lookup_nik');
    Route::match(['get', 'post'], '/interview/odoo/import-nik', [CandidateImportController::class, 'importOdooByNik'])->name('interview.odoo.import_nik');

    // Export Interview & Download PDF / Cetak
    Route::get('/export/interview', [InterviewController::class, 'exportInterview'])->name('interview.export');

    Route::get('/interview/{id}/pdf', [InterviewController::class, 'downloadPdf'])->name('interview.pdf');
    Route::get('/interview/{id}/print', [InterviewController::class, 'downloadPdf'])->name('interview.print');
    Route::get('/printall', fn(\Illuminate\Http\Request $r) => redirect()->route('interview.pdf', $r->query('id', 7)));

    // Modul Menu HR Lainnya
    Route::get('/presensi', fn() => view('interview.placeholder', ['pageTitle' => 'Presensi GPS & Kehadiran Live']))->name('presensi.index');
    Route::get('/cuti', fn() => view('interview.placeholder', ['pageTitle' => 'Pengajuan Cuti & Izin']))->name('cuti.index');
    Route::get('/kpi', fn() => view('interview.placeholder', ['pageTitle' => 'Evaluasi Kinerja & KPI']))->name('kpi.index');

    // --- FITUR KANDIDAT JOB PORTAL ---
    Route::get('/kandidatportal', [KandidatPortalController::class, 'index'])->name('kandidatportal.index');
    Route::get('/kandidat-portal', fn() => redirect()->route('kandidatportal.index'));
    Route::get('/kandidatportal/export', [KandidatPortalController::class, 'exportExcel'])->name('kandidatportal.export');
    Route::post('/kandidatportal/sync-odoo', [KandidatPortalController::class, 'syncOdooRecruitment'])->name('kandidatportal.sync_odoo');
    Route::post('/kandidatportal/{id}/sync-single-odoo', [KandidatPortalController::class, 'syncSingleOdoo'])->name('kandidatportal.sync_single_odoo');
    Route::get('/kandidatportal/ai-live-status', [KandidatPortalController::class, 'aiLiveStatus'])->name('kandidatportal.ai_live_status');
    Route::get('/kandidatportal/ai-queue', [KandidatPortalController::class, 'aiQueueLog'])->name('kandidatportal.ai_queue');
    Route::get('/kandidatportal/ai-queue-data', [KandidatPortalController::class, 'aiQueueData'])->name('kandidatportal.ai_queue_data');
    Route::post('/kandidatportal/ai-queue-trigger', [KandidatPortalController::class, 'aiQueueTriggerProcess'])->name('kandidatportal.ai_queue_trigger');
    Route::get('/kandidatportal/{id}', [KandidatPortalController::class, 'show'])->name('kandidatportal.show');
    Route::post('/kandidatportal/{id}/reset-password', [KandidatPortalController::class, 'resetPassword'])->name('kandidatportal.reset_password');
    Route::post('/kandidatportal/{id}/interview', [KandidatPortalController::class, 'updateInterview'])->name('kandidatportal.interview');
    Route::post('/kandidatportal/{id}/refcek', [KandidatPortalController::class, 'storeRefcek'])->name('kandidatportal.refcek');
    Route::post('/kandidatportal/{id}/kompt', [KandidatPortalController::class, 'storeComputerTest'])->name('kandidatportal.kompt');
    Route::get('/kandidatportal/{id}/cetak-ai', [KandidatPortalController::class, 'cetakAiPdf'])->name('kandidatportal.cetak-ai');
    Route::get('/interview/{id}/cetak-ai', [InterviewController::class, 'cetakAiPdf'])->name('interview.cetak-ai');
    Route::get('/cetak_ai_result.php', fn(\Illuminate\Http\Request $r) => redirect()->route('kandidatportal.cetak-ai', $r->query('id', 64748)));
    Route::post('/kandidatportal/{id}/alihkan', [KandidatPortalController::class, 'alihkanAS'])->name('kandidatportal.alihkan');
    Route::post('/kandidatportal/{id}/ganti-area', [KandidatPortalController::class, 'gantiArea'])->name('kandidatportal.ganti_area');
    Route::post('/kandidatportal/{id}/arsipkan', [KandidatPortalController::class, 'arsipkan'])->name('kandidatportal.arsipkan');
    Route::post('/kandidatportal/{id}/unarchive', [KandidatPortalController::class, 'unarchive'])->name('kandidatportal.unarchive');
    Route::post('/kandidatportal/{id}/attachments', [KandidatPortalController::class, 'uploadAttachments'])->name('kandidatportal.attachments.update');
    Route::post('/kandidatportal/{id}/analyze-cv', [KandidatPortalController::class, 'analyzeCv'])->name('kandidatportal.analyze_cv');

    // --- FITUR KANDIDAT INHOUSE ---
    Route::get('/interviewinhouse', [InterviewInhouseController::class, 'index'])->name('interviewinhouse.index');
    Route::get('/interviewinhouse/{id}', [InterviewInhouseController::class, 'show'])->name('interviewinhouse.show');
    Route::post('/interviewinhouse/{id}/approval', [InterviewInhouseController::class, 'storeApproval'])->name('interviewinhouse.approval');
    Route::get('/interviewinhouse/{id}/berkas', [InterviewInhouseController::class, 'downloadBerkas'])->name('interviewinhouse.berkas');
    Route::get('/interview/inhouse', fn() => redirect()->route('interviewinhouse.index'));
    Route::get('/interview/inhouse/{id}', fn($id) => redirect()->route('interviewinhouse.show', $id));
    Route::get('/hasilinhouse.php', fn(\Illuminate\Http\Request $r) => redirect()->route('interviewinhouse.show', $r->query('id', 7)));

    // --- FITUR AI CANDIDATE RANKING ---
    Route::get('/airanking', [AiRankingController::class, 'index'])->name('airanking.index');
    Route::get('/kandidatportal/ranking', [AiRankingController::class, 'index'])->name('kandidatportal.ranking');
    Route::get('/ai_ranking.php', fn(\Illuminate\Http\Request $r) => redirect()->route('airanking.index', $r->query('job') ? ['job' => $r->query('job')] : []));

    // --- FITUR JOB STATISTIK ---
    Route::get('/job/statistik', [JobStatistikController::class, 'index'])->name('job.statistik');
    Route::get('/job/statistik/export', [JobStatistikController::class, 'export'])->name('job.statistik.export');
    Route::get('/job_stats.php', fn(\Illuminate\Http\Request $r) => redirect()->route('job.statistik', $r->all()));
    Route::get('/export_job_stats.php', fn(\Illuminate\Http\Request $r) => redirect()->route('job.statistik.export', $r->all()));

    // --- FITUR INPUT JOB REQUIREMENT ---
    Route::get('/inputjob', [JobController::class, 'index'])->name('job.input');
    Route::post('/inputjob', [JobController::class, 'store'])->name('job.store');
    Route::post('/inputjob/generate-ai', [JobController::class, 'generateJobAi'])->name('job.generate_ai');
    Route::post('/inputjob/generate-image-prompt', [JobController::class, 'generateImagePrompt'])->name('job.generate_image_prompt');
    Route::delete('/inputjob/{id}', [JobController::class, 'destroy'])->name('job.destroy');
    Route::get('/inputjob/{id}/toggle', [JobController::class, 'toggleStatus'])->name('job.toggle');

    // --- MASTER USER PRINSIPLE ---
    Route::resource('user-prinsiple', App\Http\Controllers\UserPrinsipleController::class)->names('userprinsiple');
    Route::post('user-prinsiple/{id}/send-access', [App\Http\Controllers\UserPrinsipleController::class, 'sendAccess'])->name('userprinsiple.send_access');
    Route::get('/dataprinsiple', fn() => redirect()->route('userprinsiple.index'));
    Route::get('/dataprinsiple.php', fn() => redirect()->route('userprinsiple.index'));

    // --- WORK PLAN & TODOLIST ---
    Route::get('/workplan', [WorkPlanController::class, 'index'])->name('workplan.index');
    Route::get('/workplan/load-more', [WorkPlanController::class, 'loadMore'])->name('workplan.load_more');
    Route::post('/workplan', [WorkPlanController::class, 'store'])->name('workplan.store');
    Route::match(['put', 'post'], '/workplan/{id}', [WorkPlanController::class, 'update'])->name('workplan.update');
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

    // Work Plan Groups Chat (WhatsApp Web style)
    Route::get('/workplan-chat', [WorkPlanChatController::class, 'index'])->name('workplan.chat');
    Route::post('/workplan-chat/groups', [WorkPlanChatController::class, 'storeGroup'])->name('workplan.chat.groups.store');
    Route::post('/workplan-chat/groups/{id}/members', [WorkPlanChatController::class, 'addMembers'])->name('workplan.chat.groups.members');
    Route::get('/workplan-chat/groups/{id}/messages', [WorkPlanChatController::class, 'getMessages'])->name('workplan.chat.messages');
    Route::post('/workplan-chat/groups/{id}/messages', [WorkPlanChatController::class, 'sendMessage'])->name('workplan.chat.messages.send');
    Route::get('/workplan-chat/groups-poll', [WorkPlanChatController::class, 'getGroups'])->name('workplan.chat.groups.poll');
    Route::get('/workplan-chat/notifications/check', [WorkPlanChatController::class, 'checkNotifications'])->name('workplan.chat.notifications.check');
    Route::post('/workplan-chat/notifications/clear', [WorkPlanChatController::class, 'clearNotifications'])->name('workplan.chat.notifications.clear');

    // --- HELPDESK & TICKET PENGGUNA ---
    Route::prefix('helpdesk')->name('helpdesk.')->group(function () {
        Route::get('/', [HelpdeskDashboardController::class, 'index'])->name('index');
        Route::get('/tickets', [HelpdeskTicketController::class, 'index'])->name('tickets.index');
        Route::get('/tickets/create', [HelpdeskTicketController::class, 'create'])->name('tickets.create');
        Route::post('/tickets', [HelpdeskTicketController::class, 'store'])->name('tickets.store');
        Route::get('/tickets/{id}', [HelpdeskTicketController::class, 'show'])->name('tickets.show');
        Route::get('/tickets/{id}/attachment', [HelpdeskTicketController::class, 'downloadAttachment'])->name('tickets.attachment');
        Route::get('/tickets/{ticketId}/replies/{replyId}/attachment', [HelpdeskTicketController::class, 'downloadReplyAttachment'])->name('tickets.reply.attachment');
        Route::post('/tickets/{id}/reply', [HelpdeskTicketController::class, 'reply'])->name('tickets.reply');
        Route::post('/tickets/{id}/claim', [HelpdeskTicketController::class, 'claim'])->name('tickets.claim');
        Route::post('/tickets/{id}/status', [HelpdeskTicketController::class, 'updateStatus'])->name('tickets.status');
        Route::post('/tickets/{id}/close', [HelpdeskTicketController::class, 'closeByUser'])->name('tickets.close');
        Route::post('/tickets/{id}/reopen', [HelpdeskTicketController::class, 'reopenByUser'])->name('tickets.reopen');
    });

    // Alias redirect sistem lama
    Route::get('/helpdesk.php', fn() => redirect()->route('helpdesk.index'));
    Route::get('/ticket.php', fn() => redirect()->route('helpdesk.tickets.index'));
    Route::get('/wp.php', fn() => redirect()->route('workplan.index'));
    Route::get('/wptodo.php', fn() => redirect()->route('workplan.index'));
    Route::get('/todo.php', fn() => redirect()->route('workplan.index'));
    Route::get('/exportwp.php', fn() => redirect()->route('workplan.export'));
    Route::get('/v3/wp.php', fn() => redirect()->route('workplan.index'));
    Route::get('/v3/wptodo.php', fn() => redirect()->route('workplan.index'));
    Route::get('/v3/exportwp.php', fn() => redirect()->route('workplan.export'));
});


// ==============================================================
// 3. RUTE KHUSUS ADMINISTRATOR (HANYA BISA DIAKSES ADMIN)
// ==============================================================
Route::middleware(['admin'])->group(function () {

    // --- MASTER DATA ---
    Route::prefix('master')->name('master.')->group(function () {
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

        // Master Alur Approval Dinamis
        Route::get('/approval-workflow', [\App\Http\Controllers\ApprovalWorkflowController::class, 'index'])->name('approval-workflow.index');
        Route::post('/approval-workflow/step', [\App\Http\Controllers\ApprovalWorkflowController::class, 'storeStep'])->name('approval-workflow.step.store');
        Route::put('/approval-workflow/step/{id}', [\App\Http\Controllers\ApprovalWorkflowController::class, 'updateStep'])->name('approval-workflow.step.update');
        Route::delete('/approval-workflow/step/{id}', [\App\Http\Controllers\ApprovalWorkflowController::class, 'destroyStep'])->name('approval-workflow.step.destroy');
        Route::post('/approval-workflow/reorder', [\App\Http\Controllers\ApprovalWorkflowController::class, 'reorderSteps'])->name('approval-workflow.step.reorder');
        Route::get('/approval-workflow/search-approvers', [\App\Http\Controllers\ApprovalWorkflowController::class, 'searchApprovers'])->name('approval-workflow.search-approvers');
    });

    // --- PENGATURAN AI & WHATSAPP ---
    Route::get('/ai-settings', [AiSettingController::class, 'index'])->name('aisetting.index');
    Route::post('/ai-settings', [AiSettingController::class, 'update'])->name('aisetting.update');
    Route::post('/ai-settings/test-gemini', [AiSettingController::class, 'testGemini'])->name('aisetting.test_gemini');
    Route::post('/ai-settings/test-openrouter', [AiSettingController::class, 'testOpenrouter'])->name('aisetting.test_openrouter');
    Route::post('/ai-settings/test-sumopod', [AiSettingController::class, 'testSumopod'])->name('aisetting.test_sumopod');
    Route::post('/ai-settings/test-wa', [AiSettingController::class, 'testWa'])->name('aisetting.test_wa');
    Route::post('/ai-settings/remove-expired-key', [AiSettingController::class, 'removeExpiredKey'])->name('aisetting.remove_expired_key');
    Route::post('/ai-settings/remove-model', [AiSettingController::class, 'removeModel'])->name('aisetting.remove_model');
    Route::get('/ai_settings.php', fn() => redirect()->route('aisetting.index'));

    // --- INTEGRASI ODOO ERP ---
    Route::prefix('odoo-setting')->name('odoo.setting.')->group(function () {
        Route::get('/', [App\Http\Controllers\OdooSettingController::class, 'index'])->name('index');
        Route::put('/{code}', [App\Http\Controllers\OdooSettingController::class, 'update'])->name('update');
        Route::post('/{code}/test', [App\Http\Controllers\OdooSettingController::class, 'testConnection'])->name('test');
        Route::get('/stream-sync-all', [App\Http\Controllers\OdooSettingController::class, 'streamSyncAll'])->name('stream-sync-all');
        Route::get('/stream-sync-nik', [App\Http\Controllers\OdooSettingController::class, 'streamSyncNik'])->name('stream-sync-nik');
        Route::get('/{code}/stream-sync', [App\Http\Controllers\OdooSettingController::class, 'streamSync'])->name('stream-sync');
        Route::post('/{code}/sync', [App\Http\Controllers\OdooSettingController::class, 'sync'])->name('sync');
        Route::post('/sync-all', [App\Http\Controllers\OdooSettingController::class, 'syncAll'])->name('sync-all');
        Route::match(['GET', 'POST'], '/sync-by-nik', [App\Http\Controllers\OdooSettingController::class, 'syncByNik'])->name('sync-by-nik');
        Route::post('/cleanup-duplicates', [App\Http\Controllers\OdooSettingController::class, 'cleanupDuplicates'])->name('cleanup-duplicates');
    });
    Route::match(['GET', 'POST'], '/sync-by-nik', [App\Http\Controllers\OdooSettingController::class, 'syncByNik']);
    Route::match(['GET', 'POST'], '/master/karyawan/sync-by-nik', [App\Http\Controllers\OdooSettingController::class, 'syncByNik']);
    Route::get('/odoo-sync', fn() => redirect()->route('odoo.setting.index'));
    Route::get('/odoo_setting.php', fn() => redirect()->route('odoo.setting.index'));

    // --- BANTUAN LOGIN ADMIN HELPDESK ---
    Route::prefix('admin/bantuan-login')->name('admin.auth-chat.')->group(function () {
        Route::get('/', [\App\Http\Controllers\AuthChatController::class, 'adminIndex'])->name('index');
        Route::get('/poll', [\App\Http\Controllers\AuthChatController::class, 'adminPoll'])->name('poll');
        Route::post('/{id}/reply', [\App\Http\Controllers\AuthChatController::class, 'adminReply'])->name('reply');
        Route::post('/{id}/send-access', [\App\Http\Controllers\AuthChatController::class, 'adminSendAccess'])->name('send-access');
        Route::post('/{id}/resolve', [\App\Http\Controllers\AuthChatController::class, 'adminResolve'])->name('resolve');
    });

    // --- RBAC & HAK AKSES SISTEM ---
    Route::prefix('setting/rbac')->name('setting.rbac.')->group(function () {
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
    Route::get('/rbac', fn() => redirect()->route('setting.rbac.index'))->name('rbac.index');

    // --- AUDIT TRAIL & LOG AKTIVITAS SISTEM ---
    Route::prefix('activity-logs')->name('activity-logs.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ActivityLogController::class, 'index'])->name('index');
        Route::get('/export/excel', [\App\Http\Controllers\ActivityLogController::class, 'export'])->name('export');
        Route::get('/{id}', [\App\Http\Controllers\ActivityLogController::class, 'show'])->name('show');
    });
    Route::get('/logs', fn() => redirect()->route('activity-logs.index'));
    Route::get('/audit', fn() => redirect()->route('activity-logs.index'));

    // --- HELPDESK KHUSUS ADMINISTRATOR (KANBAN, DIVISI, CANNED, TEMPLATE) ---
    Route::prefix('helpdesk')->name('helpdesk.')->group(function () {
        Route::get('/kanban', [HelpdeskTicketController::class, 'kanban'])->name('kanban');

        Route::get('/divisions', [HelpdeskDivisionController::class, 'index'])->name('divisions.index');
        Route::post('/divisions', [HelpdeskDivisionController::class, 'store'])->name('divisions.store');
        Route::put('/divisions/{id}', [HelpdeskDivisionController::class, 'update'])->name('divisions.update');
        Route::delete('/divisions/{id}', [HelpdeskDivisionController::class, 'destroy'])->name('divisions.destroy');
        Route::post('/divisions/sync-inhouse', [HelpdeskDivisionController::class, 'syncFromInhouse'])->name('divisions.sync_inhouse');
        Route::post('/divisions/{id}/agents', [HelpdeskDivisionController::class, 'addAgent'])->name('divisions.agents.add');
        Route::delete('/divisions/{id}/agents/{userId}', [HelpdeskDivisionController::class, 'removeAgent'])->name('divisions.agents.remove');

        Route::get('/canned', [HelpdeskCannedController::class, 'index'])->name('canned.index');
        Route::post('/canned', [HelpdeskCannedController::class, 'store'])->name('canned.store');
        Route::delete('/canned/{id}', [HelpdeskCannedController::class, 'destroy'])->name('canned.destroy');

        Route::get('/templates', [HelpdeskTemplateController::class, 'index'])->name('templates.index');
        Route::post('/templates', [HelpdeskTemplateController::class, 'store'])->name('templates.store');
        Route::put('/templates/{id}', [HelpdeskTemplateController::class, 'update'])->name('templates.update');
        Route::delete('/templates/{id}', [HelpdeskTemplateController::class, 'destroy'])->name('templates.destroy');
        Route::delete('/templates/{id}/attachment', [HelpdeskTemplateController::class, 'removeAttachment'])->name('templates.attachment.remove');
    });
});


// ==============================================================
// 4. WILDCARD FALLBACK KE ASET V3 LAMA (WAJIB LOGIN)
// ==============================================================
Route::get('/v3/{path}', [\App\Http\Controllers\AttachmentController::class, 'showLegacyV3'])->where('path', '.*')->name('legacy.v3.fallback');
