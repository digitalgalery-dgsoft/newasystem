# Graph Report - newasystem  (2026-09-30)

## Corpus Check
- 327 files · ~457,282 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 19 file(s) not represented in the graph (top: (none) 9, .bat 5, .example 1)

## Summary
- 1574 nodes · 3616 edges · 165 communities (58 shown, 107 thin omitted)
- Extraction: 90% EXTRACTED · 10% INFERRED · 0% AMBIGUOUS · INFERRED: 374 edges (avg confidence: 0.91)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `fb6fc8b3`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- OdooSyncService
- Candidate
- CbtController
- User
- WorkPlanController
- composer.json
- Employee
- Illuminate\Database\Eloquent\Model
- MathQuestion
- HelpdeskCannedResponse
- Illuminate\Http\Request
- JobSpec
- AiSetting
- ApprovalWorkflowController
- HelpdeskTicket
- package.json
- Illuminate\Console\Command
- AiAnalyzerService
- Principle
- KandidatPortalController
- WorkPlanChatController
- TestResult
- CbtModuleTest
- .auth
- 🏆 Milestone & Fitur yang Telah Diselesaikan
- Role
- 📜 Riwayat Commit & Pembaruan Kode
- UserPrinsiple
- Illuminate\Support\Facades\Schema
- TbArea
- AppServiceProvider.php
- Panduan Implementasi WhatsApp API Official Coexistence (Coex) di ASystem
- Closure
- CandidateController
- PasswordResetRequest
- CandidateEvaluationDataService
- DatabaseSeeder.php
- 🚀 Panduan & Prosedur Deployment Server Production ASystem
- CandidateImportController
- Illuminate\Database\Eloquent\Relations\HasMany
- InterviewPdfService
- PersonalityQuestion
- .syncEmployees
- AiPdfService
- Carbon\Carbon
- .canViewAllCandidates
- bootstrap/app.php
- logging.php
- InterviewInhouseController
- ExampleTest
- 🚀 Panduan Instalasi & Deployment ASystem Portal
- artisan
- ApprovalWorkflowStep
- Controller
- HelpdeskDivision
- .update
- .getMissingProfileFields
- .done
- ActivityLog
- Database\Factories\UserFactory
- .testResults
- Illuminate\Support\Facades\DB
- .recruiter
- Illuminate\Database\Eloquent\Relations\BelongsTo
- IndonesiaRegionService
- OdooRecruitmentSyncService
- console.php
- 🚀 Ringkasan Perkembangan & Progress Update ASystem Portal
- .hasCv
- workplan/index.blade.php
- deploy.sh
- install.sh
- interview/show.blade.php
- kandidatportal/show.blade.php
- app.blade.php
- cbt.blade.php
- public.blade.php
- cron_ai_analyzer.sh
- cron_odoo_active_hourly.sh
- cron_odoo_sync.sh
- cron_odoo_updates_resigns_midnight.sh
- ASystem - Support System ESA Groups
- helpdesk._kanban_card
- Panduan Pengembangan & Prosedur Update Codebase ASYSTEM
- rules/graphify.md
- workflows/graphify.md
- template_import_kandidat_d4b107a9.md

## God Nodes (most connected - your core abstractions)
1. `Candidate` - 144 edges
2. `User` - 122 edges
3. `Employee` - 93 edges
4. `📜 Riwayat Commit & Pembaruan Kode` - 88 edges
5. `ActivityLogger` - 87 edges
6. `🏆 Milestone & Fitur yang Telah Diselesaikan` - 73 edges
7. `Principle` - 61 edges
8. `InterviewController` - 45 edges
9. `JobSpec` - 40 edges
10. `OdooSyncService` - 38 edges

## Surprising Connections (you probably didn't know these)
- `61. 👥 Searchable Multi-Select Dropdown Anggota Tim & Penyesuaian Label "Groups Chat" (20 September 2026)` --references--> `WorkPlanChatController`  [INFERRED]
  UPDATE_PROGRESS.md → app/Http/Controllers/WorkPlanChatController.php
- `62. ⚡ Optimasi Kecepatan Ekstrim Groups Chat & Perbaikan Pembukaan Modal (20 September 2026)` --references--> `WorkPlanChatController`  [INFERRED]
  UPDATE_PROGRESS.md → app/Http/Controllers/WorkPlanChatController.php
- `53. ⚙️ Implementasi Role Akses Approver Dinamis & Workflow Engine Kandidat Inhouse (23 September 2026)` --references--> `Employee`  [INFERRED]
  UPDATE_PROGRESS.md → app/Models/Employee.php
- `2. 🔐 Manajemen Hak Akses Role & Proteksi Menu Master Data` --references--> `EnsureUserIsAdmin`  [INFERRED]
  UPDATE_PROGRESS.md → app/Http/Middleware/EnsureUserIsAdmin.php
- `9. 🗄️ Migrasi Penuh Database Legacy Interview (`asystemc_interview.sql`) & Evaluasi Dinamis` --references--> `InterviewController`  [INFERRED]
  UPDATE_PROGRESS.md → app/Http/Controllers/InterviewController.php

## Import Cycles
- None detected.

## Communities (165 total, 107 thin omitted)

### Community 0 - "OdooSyncService"
Cohesion: 0.10
Nodes (13): OdooSyncActiveCommand, OdooSyncCommand, OdooSyncUpdatesResignsCommand, OdooSettingController, OdooEntity, OdooSyncLog, OdooSyncService, Illuminate\Http\JsonResponse (+5 more)

### Community 1 - "Candidate"
Cohesion: 0.04
Nodes (3): Candidate, 110. 🐛 Perbaikan TypeError strcasecmp() pada Dropdown Prinsiple Detail Kandidat, 88. 📢 Filter Jalur Info Lowongan & Tampilan Detail Kandidat Portal (28 September 2026)

### Community 2 - "CbtController"
Cohesion: 0.14
Nodes (5): CbtController, CbtQuestionService, 106. 🧮 Perbaikan Bug Alur Remidi Tes Matematika & Sinkronisasi Status Dashboard CBT Kandidat, 38. 🧮 Penyelarasan Soal CBT Matematika dengan Master Soal Sistem Lama & Modul Master Soal Matematika Admin (19 September 2026), 40. 🧠 Penyelarasan Tes Kepribadian CBT dengan 40 Butir Soal Florence Littauer (tb_kepribadian), Modul Master Soal Kepribadian Admin, & Penyesuaian Hasil Dummy ke Sanguinis/Koleris (19 September 2026)

### Community 3 - "User"
Cohesion: 0.09
Nodes (7): App\Models\User, User, Employee, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable, 61. 🎫 Implementasi Modul Baru: Helpdesk Ticketing Terintegrasi Otomatis Work Plan (Step Progress) & Master Karyawan Inhouse (24 September 2026), 66. 🎨 Kustomisasi Tema Dashboard Personal (Light/Dark Mode, 4 Palet Gelap, Custom Accent Color) & Tampilan Jabatan User (20 September 2026)

### Community 4 - "WorkPlanController"
Cohesion: 0.11
Nodes (9): WorkPlanController, Task, TaskActivity, TaskNotification, TaskSubtask, WorkPlanDaily, 32. 📋 Resolusi Work Plan & To Do List Pasca-Migrasi: Pencocokan Nama Case-Insensitive & Akses Data Historis Arsip (Andi Kurniawan Distrianto & 65 User Lainnya), 58. 📋 Penyelarasan Akun Astri Wahyuni (ASTRI WAHYUNI,ST & HOD AR - Surabaya) & Sinkronisasi Presisi Salin Laporan WhatsApp Work Plan (24 September 2026) (+1 more)

### Community 5 - "composer.json"
Cohesion: 0.04
Nodes (48): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+40 more)

### Community 6 - "Employee"
Cohesion: 0.07
Nodes (7): Collection, Employee, 27. 🏢 Pengetatan Aturan Tipe Karyawan Inhouse (Hanya 5 Entitas Resmi) & Proteksi Akses Login, 64. 👤 Resolusi Nama Lengkap AS / Rekruter pada Export Excel (.xlsx) Kandidat Portal dari Data Karyawan (20 September 2026), 65. 🏷️ Penambahan Jabatan AS dan Eliminasi Fallback Administrator ESA pada Export Excel (.xlsx) Kandidat Portal (20 September 2026), 69. 📑 Standardisasi & Harmonisasi Kolom Nama AS pada Export Excel Kandidat Portal & Filter Web (25 September 2026), 96. 📜 Implementasi Komprehensif Modul Surat Peringatan (SP 1, SP 2, SP 3) — Workflow Pengajuan, Review HRD, Penomoran Otomatis & Cetak PDF Resmi (29 September 2026)

### Community 7 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.15
Nodes (17): App\Http\Controllers\WorkPlanController, AiSettingUser, CandidateLog, App\Models\InterviewAssessment, App\Models\JobSpec, App\Models\TaskActivity, App\Models\TaskCategory, TaskCategory (+9 more)

### Community 8 - "MathQuestion"
Cohesion: 0.09
Nodes (7): AttachmentController, MathQuestionController, MathQuestion, App\Services\LegacyAttachmentService, LegacyAttachmentService, up(), 23. 🧮 Perbaikan Opsi Pilihan Ganda CBT Matematika & Normalisasi Master Soal

### Community 9 - "HelpdeskCannedResponse"
Cohesion: 0.25
Nodes (4): App\Http\Controllers\Controller, App\Http\Controllers\Helpdesk\HelpdeskCannedController, HelpdeskCannedController, HelpdeskCannedResponse

### Community 10 - "Illuminate\Http\Request"
Cohesion: 0.09
Nodes (11): App\Http\Controllers\EmployeeController, App\Http\Controllers\FeatureController, InterviewController, UserProfileController, App\Services\ActivityLogger, ActivityLogger, Illuminate\Http\Request, Illuminate\Support\Facades\Auth (+3 more)

### Community 11 - "JobSpec"
Cohesion: 0.07
Nodes (6): App\Http\Controllers\HomeController, HomeController, PublicJobController, JobSpec, Controller, 56. 📝 Form Job Apply: Seluruh Field Menjadi Mandatory (Wajib Diisi) & Notifikasi Interaktif Bagian yang Kurang (23 September 2026)

### Community 12 - "AiSetting"
Cohesion: 0.14
Nodes (3): AiSettingController, AiSetting, 45. 🤖 Integrasi AI OpenRouter, Hierarki Fallback Kuota (Gemini ➔ OpenRouter ➔ Sumopod), & Model Kustom Dinamis

### Community 14 - "HelpdeskTicket"
Cohesion: 0.09
Nodes (7): HelpdeskTicketController, HelpdeskTicket, HelpdeskTicketLog, HelpdeskWorkplanService, 62. 🛡️ Diferensiasi Hak Akses Dashboard & Modul Ticketing (User Biasa, User Divisi, Administrator) (24 September 2026), 65. 📎 Dukungan Multiple Lampiran (Gambar, PDF, Office Docs) & Modal Preview Interaktif, 67. 🔒 Fitur Penutupan & Buka Kembali Tiket oleh Pengaju (Requester Self-Close)

### Community 15 - "package.json"
Cohesion: 0.09
Nodes (19): devDependencies, axios, concurrently, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite, private (+11 more)

### Community 16 - "Illuminate\Console\Command"
Cohesion: 0.10
Nodes (11): CheckAstriCommand, FixDummyMathResultsCommand, FixDummyPersonalityResultsCommand, ImportJobSpecsCommand, ImportLegacyInterviewCommand, ImportOfficialPrinciplesCommand, ImportWpDumpCommand, SyncAstriWpCommand (+3 more)

### Community 17 - "AiAnalyzerService"
Cohesion: 0.15
Nodes (4): CronAiAnalyzerCommand, AiAnalyzerService, 57. ⚡ Perbaikan Masalah Input Token AI CV Analyzer Melonjak Ekstrem (2.114.589 Token) & Sanitasi Gambar Base64 Job Requirement (24 September 2026), 89. 🎯 Optimalisasi AI CV Analyzer: Pembacaan Area dari Kota Penempatan Kerja & Guard Khusus Berkas Surat Lamaran Tanpa CV (28 September 2026)

### Community 18 - "Principle"
Cohesion: 0.26
Nodes (4): App\Http\Controllers\PrincipleController, PrincipleController, Principle, 36. 📋 Penyelarasan Kolom Export Sesuai Sistem Lama, Link Server Produksi, & Label CV Analisa AI (19 September 2026)

### Community 19 - "KandidatPortalController"
Cohesion: 0.20
Nodes (4): AiPdfService, KandidatPortalController, 108. 🛡️ Eliminasi Administrator ESA, Standardisasi Nama AS Kanonikal, & Searchable Filter Dropdown Kandidat Portal, 75. 🎯 Perbaikan Hak Akses & Pembatasan Tampilan Kandidat Portal Sesuai AS User (28 September 2026)

### Community 20 - "WorkPlanChatController"
Cohesion: 0.21
Nodes (7): App\Http\Controllers\WorkPlanChatController, WorkPlanChatController, WpChatGroup, WpChatGroupMember, WpChatMessage, Illuminate\Database\Eloquent\Relations\HasOne, 95. 🔔 Penambahan Tombol 'Clear' pada Dropdown Lonceng Notifikasi Navbar — Pembersihan Badge Notifikasi & Penandaan Telah Dibaca Global (29 September 2026)

### Community 21 - "TestResult"
Cohesion: 0.14
Nodes (16): CleanDuplicateCandidatesCommand, ImportInterviewSqlDumpCommand, App\Http\Controllers\CandidateImportController, App\Http\Controllers\CbtController, App\Http\Controllers\InterviewController, App\Http\Controllers\InterviewInhouseController, App\Http\Controllers\PrincipleApprovalController, PrincipleApprovalController (+8 more)

### Community 22 - "CbtModuleTest"
Cohesion: 0.19
Nodes (5): Illuminate\Foundation\Testing\RefreshDatabase, Illuminate\Foundation\Testing\TestCase, CbtModuleTest, ExampleTest, TestCase

### Community 23 - ".auth"
Cohesion: 0.21
Nodes (3): AuthController, JobController, 69. 🛡️ Sistem Audit Trail & Log Aktivitas Komprehensif Seluruh Sistem (20 September 2026)

### Community 24 - "🏆 Milestone & Fitur yang Telah Diselesaikan"
Cohesion: 0.04
Nodes (47): 10. 🎯 Pemisahan 4 Kategori Kandidat & Penambahan Kolom Jenis Kelamin, 17. 💻 Modernisasi Sinkronisasi Odoo dengan Live Streaming Terminal Console, 1. 🏠 Halaman Beranda / Home (Full-Width Welcome Hero), 22. ✍️ Isolasi & Personalisasi Tanda Tangan Pewawancara (AS), 23. 👥 Pemulihan Master User Prinsiple, Isolasi Data Per Pengguna, & Proteksi Anti-Duplikat Email/No HP, 24. 🔄 Rekonfigurasi Sinkronisasi Odoo ERP & Arsitektur Dual Background Cron Job, 26. 📍 Searchable Dropdown Filter Area & Default Pengurutan Join Date Terbaru (19 September 2026), 28. 👔 Penambahan Field Pimpinan di Form Edit Karyawan & Fitur Bulk Edit Pimpinan Massal (19 September 2026) (+39 more)

### Community 25 - "Role"
Cohesion: 0.23
Nodes (4): RbacController, Permission, Role, 25. 🎯 Pembatasan Ketat Data Kandidat Portal & Interview di Dashboard AS (Hanya Kandidat Milik AS Terkait)

### Community 26 - "📜 Riwayat Commit & Pembaruan Kode"
Cohesion: 0.05
Nodes (40): 100. 📑 Pembaruan Berkas Kop Surat Resmi 5 Entitas dari Direktori Eksternal (`D:\ASystem\KOP ENTITAS`) (29 September 2026), 101. 📅 Sinkronisasi Tanggal Surat Resmi & Masa Berlaku 6 Bulan Sejak Tanggal Rilis / Approval HRD (29 September 2026), 107. 🧮 Penyempurnaan Deduplikasi 10 Butir Soal Tes Matematika & Koreksi Skor Maksimal 100%, 109. 🤝 Auto-Assign Kandidat Status Publik ke User AS Berdasarkan Area Penempatan, 24. 🔄 Penyempurnaan Sinkronisasi NIK Odoo Seluruh Entitas, 26. 👥 Pemisahan 2 Tabel Kandidat Interview (Milik Sendiri & Rekan Se-Area) & Tab Terintegrasi Administrator, 27. 🚶 Modul & Halaman Kandidat Walk-in Interview (`/walkinterview`) Sesuai Sistem Lama, 28. 📝 Formulir Registrasi Walkin Interview Publik, Cascading Master Data (`tb_area` & `tb_kota`), dan Searchable TomSelect (+32 more)

### Community 27 - "UserPrinsiple"
Cohesion: 0.19
Nodes (4): UserPrinsipleController, UserPrinsiple, Illuminate\Http\RedirectResponse, Illuminate\View\View

### Community 28 - "Illuminate\Support\Facades\Schema"
Cohesion: 0.03
Nodes (5): App\Services\AiAnalyzerService, Illuminate\Database\Migrations\Migration, Illuminate\Database\Schema\Blueprint, Illuminate\Support\Facades\Cache, Illuminate\Support\Facades\Schema

### Community 29 - "TbArea"
Cohesion: 0.05
Nodes (13): App\Http\Controllers\JobStatistikController, JobStatistikController, TbArea, ActivityLogXlsxExportService, CandidateXlsxExportService, JobStatistikXlsxExportService, App\Services\WorkPlanXlsxExportService, WorkPlanXlsxExportService (+5 more)

### Community 30 - "AppServiceProvider.php"
Cohesion: 0.22
Nodes (6): AppServiceProvider, Illuminate\Auth\Events\Failed, Illuminate\Auth\Events\Login, Illuminate\Auth\Events\Logout, Illuminate\Support\Facades\Event, Illuminate\Support\ServiceProvider

### Community 31 - "Panduan Implementasi WhatsApp API Official Coexistence (Coex) di ASystem"
Cohesion: 0.11
Nodes (17): 1. Pendahuluan: Apa itu WhatsApp Coexistence (Coex)?, 2. Perbandingan: Coex Official vs Library Unofficial (Baileys / WhatsApp.js), 3. Prasyarat & Persiapan, 4. Arsitektur Integrasi di ASystem (Laravel), 5.1. Konfigurasi Environment (`.env`), 5.2. Konfigurasi Services (`config/services.php`), 5.3. Service Provider: `app/Services/WhatsAppCoexService.php`, 5.4. Queue Job: `app/Jobs/SendWhatsAppCoexJob.php` (+9 more)

### Community 32 - "Closure"
Cohesion: 0.24
Nodes (7): EnsureCandidateAuthenticated, EnsureUserIsAdmin, PreventIndexingMiddleware, RedirectIfInstalled, Closure, Symfony\Component\HttpFoundation\Response, 2. 🔐 Manajemen Hak Akses Role & Proteksi Menu Master Data

### Community 34 - "PasswordResetRequest"
Cohesion: 0.18
Nodes (4): AuthChatController, PasswordResetChatMessage, PasswordResetRequest, 40. 💬 Fitur Lupa Kata Sandi via Live Chat Administrator & Auto-Sync Odoo Karyawan

### Community 35 - "CandidateEvaluationDataService"
Cohesion: 0.33
Nodes (4): CandidateEvaluationDataService, 20. 🎯 Sinkronisasi Evaluasi Nilai CBT & Status Hasil Tes Kandidat di Dashboard Rekruter, 39. 🧮 Penyesuaian Hasil Tes Matematika Dummy Menjadi Nilai B (Grade B - 70%) & Proteksi Data Lama (19 September 2026), 50. 📋 Penyelarasan Menyeluruh Data Riil Evaluasi Kandidat Inhouse (Interview, Refcek, Komputer, Kepribadian, & Matematika)

### Community 36 - "DatabaseSeeder.php"
Cohesion: 0.27
Nodes (4): CbtCandidateSeeder, DatabaseSeeder, OdooEntitySeeder, Illuminate\Database\Seeder

### Community 37 - "🚀 Panduan & Prosedur Deployment Server Production ASystem"
Cohesion: 0.14
Nodes (13): 🌐 1. Informasi Infrastruktur & Server, ⚡ 2. Cara Cepat Deploy (Metode Utama: 1-Click Remote Runner), 🖥️ 3. Metode Alternatif (Deploy Langsung via SSH Terminal), 📋 4. Standar Operasional Prosedur (SOP) Sebelum & Sesudah Deploy, ⏱️ 5. Layanan Latar Belakang (Cron & Worker) di Production, Eksekusi Deploy:, Mekanisme Kerja Skrip:, Opsi A: Menggunakan Shell Script Resmi (+5 more)

### Community 38 - "CandidateImportController"
Cohesion: 0.19
Nodes (5): CandidateImportController, CandidateImportService, 35. 🧹 Pembersihan Akun Demo Jamil & Pengembalian Data Kandidat ke User Asli, 73. 🔄 Pembaruan Validasi Import Excel & Tarik NIK: Otomatis Mengarsipkan Data Sebelumnya untuk User/AS yang Sama dan Memblokir User/AS yang Berbeda (26 September 2026), 76. 🛡️ Penguncian Autentikasi Kredensial & Proteksi Menyeluruh Akses Data Kandidat Tanpa Login (28 September 2026)

### Community 39 - "Illuminate\Database\Eloquent\Relations\HasMany"
Cohesion: 0.09
Nodes (4): App\Models\OdooEntity, App\Models\Principle, Illuminate\Database\Eloquent\Relations\BelongsToMany, Illuminate\Database\Eloquent\Relations\HasMany

### Community 40 - "InterviewPdfService"
Cohesion: 0.27
Nodes (5): InterviewPdfService, Mpdf, 14. 🏢 Pembersihan Master Data Prinsiple & Logo Entitas Dokumen Interview (18 September 2026), 19. ✍️ Digital Signature AS, Auto-Preload Tanda Tangan, & Dynamic PDF Export, 21. 🛑 Validasi Kriteria Kelulusan Interview, Disable Tab User Prinsiple & Tombol Download Dokumen

### Community 42 - ".syncEmployees"
Cohesion: 0.20
Nodes (7): SimpleXMLElement, 25. 🔍 Filter Searchable Dropdown Prinsiple/Jabatan, Eksklusi PT BUDGET, & Deduplikasi Master Prinsiple (19 September 2026), 41. 🔄 Perbaikan Kritis Sinkronisasi Odoo: Penanganan departure_date Terjadwal & Prioritas Entitas Aktif, 42. 🔒 Proteksi Email & Password Karyawan saat Pembaruan Data dari Odoo ERP, 43. 🧹 Pengecualian Akun Sistem/Dummy (Prefix OD-) dari Sinkronisasi Odoo & Pembersihan Database, 74. ⚡ Optimalisasi Performa Master Karyawan & Perbaikan Logika Sync by NIK (21 September 2026), 74. 📱 Proteksi Nomor Telepon / HP Karyawan dari Penimpaan Sinkronisasi Odoo (28 September 2026)

### Community 44 - "Carbon\Carbon"
Cohesion: 0.11
Nodes (16): CheckAdminCommand, App\Http\Controllers\AiRankingController, App\Http\Controllers\AiSettingController, App\Http\Controllers\Helpdesk\HelpdeskDashboardController, App\Http\Controllers\Helpdesk\HelpdeskTemplateController, App\Http\Controllers\Helpdesk\HelpdeskTicketController, App\Http\Controllers\JobController, App\Models\Candidate (+8 more)

### Community 45 - ".canViewAllCandidates"
Cohesion: 0.28
Nodes (3): 42. 🛡️ Pemisahan Menu Sync Odoo ke Group 'System Setting' & Implementasi Sistem Manajemen Hak Akses (RBAC) Terpadu (19 September 2026), 43. 🛡️ Role Dinamis (CRUD), Pengaturan Scope Prinsiple Dihandle & Area Cover, serta Penyaringan Data Berdasarkan Role (19 September 2026), 68. 👥 Penyempurnaan Hak Akses Administrator Talent Pool (admin_officer) & Cakupan Scope Nasional Tanpa Pembatasan Rekruter (25 September 2026)

### Community 46 - "bootstrap/app.php"
Cohesion: 0.40
Nodes (3): Illuminate\Foundation\Application, Illuminate\Foundation\Configuration\Exceptions, Illuminate\Foundation\Configuration\Middleware

### Community 47 - "logging.php"
Cohesion: 0.40
Nodes (4): Monolog\Handler\NullHandler, Monolog\Handler\StreamHandler, Monolog\Handler\SyslogUdpHandler, Monolog\Processor\PsrLogMessageProcessor

### Community 48 - "InterviewInhouseController"
Cohesion: 0.24
Nodes (3): InterviewInhouseController, 38. 🏢 Pembatasan Ketat Approval Inhouse Khusus 5 Entitas Resmi & Pemulihan Approval Prinsiple, 9. 🗄️ Migrasi Penuh Database Legacy Interview (`asystemc_interview.sql`) & Evaluasi Dinamis

### Community 50 - "🚀 Panduan Instalasi & Deployment ASystem Portal"
Cohesion: 0.20
Nodes (9): 🔐 Akun Login Bawaan (Default Administrator), 🛠️ Catatan Teknis File Database, METODE 1: cPanel / Shared Hosting (Paling Cepat & Mudah), METODE 2: Linux VPS / Cloud Server (Ubuntu / Debian / AlmaLinux), METODE 3: Windows Server (IIS / XAMPP / Laragon), METODE 4: Web Wizard Installer (Antarmuka Grafis), 🚀 Panduan Instalasi & Deployment ASystem Portal, ⏰ Pengaturan Otomatisasi (Cron Job / Background Worker) (+1 more)

### Community 52 - "ApprovalWorkflowStep"
Cohesion: 0.12
Nodes (6): ApprovalWorkflow, ApprovalWorkflowStep, InhouseApproval, ApprovalWorkflowService, Illuminate\Support\Collection, 97. 🏛️ Penyempurnaan Alur Approval Bertingkat Surat Peringatan (Pimpinan Pembuat & HRD Dinamis), Form Fullwidth, Tanggal Otomatis & Kop Surat Entitas PDF (29 September 2026)

### Community 53 - "Controller"
Cohesion: 0.25
Nodes (4): AiRankingController, Controller, FeatureController, InstallController

### Community 54 - "HelpdeskDivision"
Cohesion: 0.14
Nodes (6): HelpdeskDashboardController, App\Http\Controllers\Helpdesk\HelpdeskDivisionController, HelpdeskDivisionController, HelpdeskDivision, HelpdeskDivisionAgent, 63. ⚡ Searchable Dropdown Agen Divisi, Template Masalah & Format Laporan Tiket, serta Master Template Laporan

### Community 58 - ".done"
Cohesion: 0.60
Nodes (3): 16. 📂 Pemulihan Visibilitas Data Interview Selesai & Arsip Interview, 55. 🔒 Perbaikan Error Undefined $isAdmin, Isolasi Data Interview Selesai & Arsip per Rekrutor / AS, serta Akses Khusus Kandidat Inhouse 5 Entitas (Approval HRD & Head) (20 September 2026), 85. 📊 Standardisasi Hak Ekspor Data Kandidat Nasional untuk Administrator Talent Pool & Pembatasan Ekspor Akun Non-Admin (28 September 2026)

### Community 59 - "ActivityLog"
Cohesion: 0.11
Nodes (3): activity_log(), ActivityLogController, ActivityLog

### Community 60 - "Database\Factories\UserFactory"
Cohesion: 0.47
Nodes (4): Database\Factories\UserFactory, UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 62 - "Illuminate\Support\Facades\DB"
Cohesion: 0.05
Nodes (4): App\Models\TbArea, up(), up(), Illuminate\Support\Facades\DB

### Community 65 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.11
Nodes (4): ApprovalWorkflowStepUser, HelpdeskTicketReply, Illuminate\Database\Eloquent\Relations\BelongsTo, 64. 🖼️ Perbaikan Tampilan Lampiran Gambar Tiket & Balasan Helpdesk

### Community 69 - "OdooRecruitmentSyncService"
Cohesion: 0.31
Nodes (4): OdooRecruitmentSyncService, 104. 🚀 Penyempurnaan Sinkronisasi Rekrutmen Odoo (Prioritas Rekrutmen Aktif vs Mantan Karyawan Resign) & Form Modal Edit Profil Kandidat Portal & Interview (29 September 2026), 86. 🛠️ Perbaikan Validasi Tarik / Sinkronisasi NIK Kandidat dari Odoo dan Penyelarasan Duplikat Arsip (28 September 2026), 94. ⚡ Penyempurnaan Metode Import & Tarik NIK Kandidat Interview — Auto Replace Tanpa Notifikasi Blokir AS Lain & Keterangan Arsip Transparan Berbasis User (29 September 2026)

### Community 71 - "console.php"
Cohesion: 0.50
Nodes (3): Illuminate\Foundation\Inspiring, Illuminate\Support\Facades\Artisan, Illuminate\Support\Facades\Schedule

### Community 72 - "🚀 Ringkasan Perkembangan & Progress Update ASystem Portal"
Cohesion: 0.50
Nodes (3): 🖥️ Panduan Menjalankan Sistem Secara Lokal, 🚀 Ringkasan Perkembangan & Progress Update ASystem Portal, 📌 Ringkasan Umum

### Community 158 - "ASystem - Support System ESA Groups"
Cohesion: 0.40
Nodes (4): ASystem - Support System ESA Groups, Fitur Utama, Panduan Instalasi & Menjalankan, Persyaratan Sistem

## Knowledge Gaps
- **179 isolated node(s):** `📌 Ringkasan Umum`, `1. 🏠 Halaman Beranda / Home (Full-Width Welcome Hero)`, `3. 👤 Prosedur Login & Autentikasi Karyawan`, `4. 👥 Penyempurnaan Master Karyawan`, `5. 🔄 Integrasi Sinkronisasi Odoo ERP (5 Entitas)` (+174 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 608 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **107 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Candidate` connect `Candidate` to `CbtController`, `Employee`, `Illuminate\Database\Eloquent\Model`, `MathQuestion`, `JobSpec`, `Illuminate\Console\Command`, `AiAnalyzerService`, `Principle`, `TestResult`, `CbtModuleTest`, `Role`, `UserPrinsiple`, `Illuminate\Support\Facades\Schema`, `TbArea`, `Closure`, `CandidateController`, `CandidateEvaluationDataService`, `DatabaseSeeder.php`, `InterviewPdfService`, `AiPdfService`, `Carbon\Carbon`, `InterviewInhouseController`, `ApprovalWorkflowStep`, `Controller`, `.getMissingProfileFields`, `.testResults`, `.recruiter`, `OdooRecruitmentSyncService`, `.hasCv`?**
  _High betweenness centrality (0.132) - this node is a cross-community bridge._
- **Why does `User` connect `User` to `OdooSyncService`, `WorkPlanController`, `Employee`, `Illuminate\Database\Eloquent\Model`, `Illuminate\Http\Request`, `JobSpec`, `ApprovalWorkflowController`, `HelpdeskTicket`, `Illuminate\Console\Command`, `TestResult`, `.auth`, `Role`, `UserPrinsiple`, `TbArea`, `CandidateController`, `PasswordResetRequest`, `DatabaseSeeder.php`, `CandidateImportController`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Carbon\Carbon`, `.canViewAllCandidates`, `InterviewInhouseController`, `ApprovalWorkflowStep`, `Controller`, `HelpdeskDivision`, `.update`, `.isAdmin`, `ActivityLog`, `Database\Factories\UserFactory`?**
  _High betweenness centrality (0.079) - this node is a cross-community bridge._
- **Why does `🏆 Milestone & Fitur yang Telah Diselesaikan` connect `🏆 Milestone & Fitur yang Telah Diselesaikan` to `Closure`, `CandidateController`, `CbtController`, `CandidateEvaluationDataService`, `User`, `OdooSyncService`, `Employee`, `InterviewPdfService`, `🚀 Ringkasan Perkembangan & Progress Update ASystem Portal`, `.syncEmployees`, `.canViewAllCandidates`, `InterviewInhouseController`, `Principle`, `TestResult`, `.auth`, `.getMissingProfileFields`, `.done`, `.recruiter`?**
  _High betweenness centrality (0.054) - this node is a cross-community bridge._
- **Are the 7 inferred relationships involving `Candidate` (e.g. with `.index()` and `.show()`) actually correct?**
  _`Candidate` has 7 INFERRED edges - model-reasoned connections that need verification._
- **Are the 9 inferred relationships involving `User` (e.g. with `.handle()` and `.login()`) actually correct?**
  _`User` has 9 INFERRED edges - model-reasoned connections that need verification._
- **Are the 15 inferred relationships involving `Employee` (e.g. with `.handle()` and `.login()`) actually correct?**
  _`Employee` has 15 INFERRED edges - model-reasoned connections that need verification._
- **What connects `📌 Ringkasan Umum`, `1. 🏠 Halaman Beranda / Home (Full-Width Welcome Hero)`, `3. 👤 Prosedur Login & Autentikasi Karyawan` to the rest of the system?**
  _179 weakly-connected nodes found - possible documentation gaps or missing edges._