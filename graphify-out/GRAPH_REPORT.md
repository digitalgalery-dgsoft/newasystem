# Graph Report - newasystem  (2026-10-01)

## Corpus Check
- 331 files · ~464,443 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 19 file(s) not represented in the graph (top: (none) 9, .bat 5, .example 1)

## Summary
- 1575 nodes · 3621 edges · 209 communities (53 shown, 156 thin omitted)
- Extraction: 90% EXTRACTED · 10% INFERRED · 0% AMBIGUOUS · INFERRED: 374 edges (avg confidence: 0.91)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `82d55789`
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
- HelpdeskDivisionAgent
- Illuminate\Http\Request
- JobSpec
- AiSetting
- ApprovalWorkflowController
- HelpdeskTicket
- package.json
- CandidateEvaluationDataService
- AiAnalyzerService
- Principle
- KandidatPortalController
- WorkPlanChatController
- WorkExperience
- CbtModuleTest
- .auth
- 🏆 Milestone & Fitur yang Telah Diselesaikan
- Role
- 📜 Riwayat Commit & Pembaruan Kode
- UserPrinsiple
- Illuminate\Database\Migrations\Migration
- TbArea
- AppServiceProvider.php
- Panduan Implementasi WhatsApp API Official Coexistence (Coex) di ASystem
- Closure
- OdooEntity
- PasswordResetRequest
- HelpdeskTicketController
- DatabaseSeeder.php
- 🚀 Panduan & Prosedur Deployment Server Production ASystem
- CandidateImportController
- Illuminate\Database\Eloquent\Relations\HasMany
- InterviewPdfService
- PersonalityQuestion
- CandidateImportService.php
- AiPdfService.php
- Illuminate\Support\Facades\DB
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- bootstrap/app.php
- logging.php
- Illuminate\Database\Schema\Blueprint
- ExampleTest
- 🚀 Panduan Instalasi & Deployment ASystem Portal
- artisan
- ApprovalWorkflowStep
- Controller
- HelpdeskDivision
- .update
- .getMissingProfileFields
- InterviewAssessment
- ActivityLog
- Database\Factories\UserFactory
- .testResults
- Illuminate\Support\Facades\Schema
- .recruiter
- JobStatistikXlsxExportService
- HelpdeskTicketReply
- .index
- JobController
- CbtQuestionService
- ActivityLogger
- .submitApply
- console.php
- 🚀 Ringkasan Perkembangan & Progress Update ASystem Portal
- ApprovalWorkflowController.php
- .hasCv
- TestResult
- InhouseApproval
- InstallController.php
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
- 2026_09_22_123000_sync_tb_area_and_normalize_regions.php
- 2026_09_28_110000_fix_job_portal_terima_candidates_status.php
- .getInfoLowonganAttribute

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
- `104. 🚀 Penyempurnaan Sinkronisasi Rekrutmen Odoo (Prioritas Rekrutmen Aktif vs Mantan Karyawan Resign) & Form Modal Edit Profil Kandidat Portal & Interview (29 September 2026)` --references--> `Candidate`  [INFERRED]
  UPDATE_PROGRESS.md → app/Models/Candidate.php
- `34. 🗺️ Penyelarasan Penuh Data Region & Area (Job Statistik & Kandidat Portal) Berdasarkan Master `tb_area` (43 Area Resmi ESA Groups)` --references--> `Candidate`  [INFERRED]
  UPDATE_PROGRESS.md → app/Models/Candidate.php

## Import Cycles
- None detected.

## Communities (209 total, 156 thin omitted)

### Community 0 - "OdooSyncService"
Cohesion: 0.06
Nodes (23): CheckAdminCommand, CheckAstriCommand, ImportJobSpecsCommand, ImportLegacyInterviewCommand, ImportOfficialPrinciplesCommand, ImportWpDumpCommand, OdooSyncActiveCommand, OdooSyncCommand (+15 more)

### Community 3 - "User"
Cohesion: 0.10
Nodes (7): SyncInhouseUsersCommand, App\Models\User, User, Employee, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable, 66. 🎨 Kustomisasi Tema Dashboard Personal (Light/Dark Mode, 4 Palet Gelap, Custom Accent Color) & Tampilan Jabatan User (20 September 2026)

### Community 4 - "WorkPlanController"
Cohesion: 0.09
Nodes (10): WorkPlanController, Task, TaskActivity, TaskNotification, TaskSubtask, WorkPlanDaily, WorkPlanXlsxExportService, 32. 📋 Resolusi Work Plan & To Do List Pasca-Migrasi: Pencocokan Nama Case-Insensitive & Akses Data Historis Arsip (Andi Kurniawan Distrianto & 65 User Lainnya) (+2 more)

### Community 5 - "composer.json"
Cohesion: 0.04
Nodes (48): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+40 more)

### Community 6 - "Employee"
Cohesion: 0.07
Nodes (7): Collection, Employee, 27. 🏢 Pengetatan Aturan Tipe Karyawan Inhouse (Hanya 5 Entitas Resmi) & Proteksi Akses Login, 64. 👤 Resolusi Nama Lengkap AS / Rekruter pada Export Excel (.xlsx) Kandidat Portal dari Data Karyawan (20 September 2026), 65. 🏷️ Penambahan Jabatan AS dan Eliminasi Fallback Administrator ESA pada Export Excel (.xlsx) Kandidat Portal (20 September 2026), 69. 📑 Standardisasi & Harmonisasi Kolom Nama AS pada Export Excel Kandidat Portal & Filter Web (25 September 2026), 96. 📜 Implementasi Komprehensif Modul Surat Peringatan (SP 1, SP 2, SP 3) — Workflow Pengajuan, Review HRD, Penomoran Otomatis & Cetak PDF Resmi (29 September 2026)

### Community 7 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.16
Nodes (9): ApprovalWorkflowStepUser, CandidateLog, App\Models\InterviewAssessment, App\Models\TaskCategory, TaskCategory, App\Models\WorkExperience, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Database\Eloquent\Model (+1 more)

### Community 8 - "MathQuestion"
Cohesion: 0.09
Nodes (7): AttachmentController, MathQuestionController, MathQuestion, App\Services\LegacyAttachmentService, LegacyAttachmentService, up(), 23. 🧮 Perbaikan Opsi Pilihan Ganda CBT Matematika & Normalisasi Master Soal

### Community 9 - "HelpdeskDivisionAgent"
Cohesion: 0.18
Nodes (3): HelpdeskCannedResponse, HelpdeskDivisionAgent, HelpdeskTicketLog

### Community 10 - "Illuminate\Http\Request"
Cohesion: 0.15
Nodes (5): InterviewController, Illuminate\Http\Request, 16. 📂 Pemulihan Visibilitas Data Interview Selesai & Arsip Interview, 55. 🔒 Perbaikan Error Undefined $isAdmin, Isolasi Data Interview Selesai & Arsip per Rekrutor / AS, serta Akses Khusus Kandidat Inhouse 5 Entitas (Approval HRD & Head) (20 September 2026), 85. 📊 Standardisasi Hak Ekspor Data Kandidat Nasional untuk Administrator Talent Pool & Pembatasan Ekspor Akun Non-Admin (28 September 2026)

### Community 12 - "AiSetting"
Cohesion: 0.12
Nodes (6): App\Http\Controllers\AiSettingController, AiSettingController, AiSetting, AiSettingUser, WaAreaSetting, 45. 🤖 Integrasi AI OpenRouter, Hierarki Fallback Kuota (Gemini ➔ OpenRouter ➔ Sumopod), & Model Kustom Dinamis

### Community 15 - "package.json"
Cohesion: 0.09
Nodes (19): devDependencies, axios, concurrently, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite, private (+11 more)

### Community 16 - "CandidateEvaluationDataService"
Cohesion: 0.18
Nodes (6): FixDummyMathResultsCommand, FixDummyPersonalityResultsCommand, CandidateEvaluationDataService, 20. 🎯 Sinkronisasi Evaluasi Nilai CBT & Status Hasil Tes Kandidat di Dashboard Rekruter, 39. 🧮 Penyesuaian Hasil Tes Matematika Dummy Menjadi Nilai B (Grade B - 70%) & Proteksi Data Lama (19 September 2026), 50. 📋 Penyelarasan Menyeluruh Data Riil Evaluasi Kandidat Inhouse (Interview, Refcek, Komputer, Kepribadian, & Matematika)

### Community 17 - "AiAnalyzerService"
Cohesion: 0.15
Nodes (4): CronAiAnalyzerCommand, AiAnalyzerService, 57. ⚡ Perbaikan Masalah Input Token AI CV Analyzer Melonjak Ekstrem (2.114.589 Token) & Sanitasi Gambar Base64 Job Requirement (24 September 2026), 89. 🎯 Optimalisasi AI CV Analyzer: Pembacaan Area dari Kota Penempatan Kerja & Guard Khusus Berkas Surat Lamaran Tanpa CV (28 September 2026)

### Community 18 - "Principle"
Cohesion: 0.12
Nodes (9): CandidateController, App\Http\Controllers\JobController, App\Http\Controllers\PrincipleController, PrincipleController, Principle, Illuminate\Support\Facades\Http, 10. ⚡ Optimasi Kecepatan Loading & Efek Animasi UI/UX Modern di Seluruh Halaman, 36. 📋 Penyelarasan Kolom Export Sesuai Sistem Lama, Link Server Produksi, & Label CV Analisa AI (19 September 2026) (+1 more)

### Community 19 - "KandidatPortalController"
Cohesion: 0.13
Nodes (6): AiPdfService, InterviewInhouseController, KandidatPortalController, 35. 🧹 Pembersihan Akun Demo Jamil & Pengembalian Data Kandidat ke User Asli, 75. 🎯 Perbaikan Hak Akses & Pembatasan Tampilan Kandidat Portal Sesuai AS User (28 September 2026), 9. 🗄️ Migrasi Penuh Database Legacy Interview (`asystemc_interview.sql`) & Evaluasi Dinamis

### Community 20 - "WorkPlanChatController"
Cohesion: 0.21
Nodes (7): App\Http\Controllers\WorkPlanChatController, WorkPlanChatController, WpChatGroup, WpChatGroupMember, WpChatMessage, Illuminate\Database\Eloquent\Relations\HasOne, 95. 🔔 Penambahan Tombol 'Clear' pada Dropdown Lonceng Notifikasi Navbar — Pembersihan Badge Notifikasi & Penandaan Telah Dibaca Global (29 September 2026)

### Community 21 - "WorkExperience"
Cohesion: 0.15
Nodes (7): CleanDuplicateCandidatesCommand, PrincipleApprovalController, PrincipleApproval, WorkExperience, 70. 🛡️ Proteksi Integritas Data CBT & Pemulihan Hasil Ujian Kandidat dari Penimpaan Sinkronisasi Odoo (25 September 2026), 71. 🔄 Perbaikan Import Kandidat Interview: Replace Data NIK Eksisting & Reset Menyeluruh Data Tes Online CBT (21 September 2026), 71. 🚫 Validasi Ketat & Alert Proteksi: Blokir Replace Kandidat Aktif pada Tarik Odoo & Import Excel (25 September 2026)

### Community 22 - "CbtModuleTest"
Cohesion: 0.19
Nodes (5): Illuminate\Foundation\Testing\RefreshDatabase, Illuminate\Foundation\Testing\TestCase, CbtModuleTest, ExampleTest, TestCase

### Community 23 - ".auth"
Cohesion: 0.17
Nodes (3): AuthController, HelpdeskCannedController, 69. 🛡️ Sistem Audit Trail & Log Aktivitas Komprehensif Seluruh Sistem (20 September 2026)

### Community 24 - "🏆 Milestone & Fitur yang Telah Diselesaikan"
Cohesion: 0.04
Nodes (47): 10. 🎯 Pemisahan 4 Kategori Kandidat & Penambahan Kolom Jenis Kelamin, 17. 💻 Modernisasi Sinkronisasi Odoo dengan Live Streaming Terminal Console, 1. 🏠 Halaman Beranda / Home (Full-Width Welcome Hero), 22. ✍️ Isolasi & Personalisasi Tanda Tangan Pewawancara (AS), 23. 👥 Pemulihan Master User Prinsiple, Isolasi Data Per Pengguna, & Proteksi Anti-Duplikat Email/No HP, 24. 🔄 Rekonfigurasi Sinkronisasi Odoo ERP & Arsitektur Dual Background Cron Job, 26. 📍 Searchable Dropdown Filter Area & Default Pengurutan Join Date Terbaru (19 September 2026), 28. 👔 Penambahan Field Pimpinan di Form Edit Karyawan & Fitur Bulk Edit Pimpinan Massal (19 September 2026) (+39 more)

### Community 25 - "Role"
Cohesion: 0.17
Nodes (4): RbacController, Permission, Role, 25. 🎯 Pembatasan Ketat Data Kandidat Portal & Interview di Dashboard AS (Hanya Kandidat Milik AS Terkait)

### Community 26 - "📜 Riwayat Commit & Pembaruan Kode"
Cohesion: 0.05
Nodes (39): 100. 📑 Pembaruan Berkas Kop Surat Resmi 5 Entitas dari Direktori Eksternal (`D:\ASystem\KOP ENTITAS`) (29 September 2026), 101. 📅 Sinkronisasi Tanggal Surat Resmi & Masa Berlaku 6 Bulan Sejak Tanggal Rilis / Approval HRD (29 September 2026), 107. 🧮 Penyempurnaan Deduplikasi 10 Butir Soal Tes Matematika & Koreksi Skor Maksimal 100%, 108. 🛡️ Eliminasi Administrator ESA, Standardisasi Nama AS Kanonikal, & Searchable Filter Dropdown Kandidat Portal, 109. 🤝 Auto-Assign Kandidat Status Publik ke User AS Berdasarkan Area Penempatan, 24. 🔄 Penyempurnaan Sinkronisasi NIK Odoo Seluruh Entitas, 26. 👥 Pemisahan 2 Tabel Kandidat Interview (Milik Sendiri & Rekan Se-Area) & Tab Terintegrasi Administrator, 27. 🚶 Modul & Halaman Kandidat Walk-in Interview (`/walkinterview`) Sesuai Sistem Lama (+31 more)

### Community 27 - "UserPrinsiple"
Cohesion: 0.22
Nodes (4): UserPrinsipleController, UserPrinsiple, Illuminate\Http\RedirectResponse, Illuminate\View\View

### Community 29 - "TbArea"
Cohesion: 0.13
Nodes (5): JobStatistikController, TbArea, CandidateXlsxExportService, 105. 📊 Penambahan Filter & Kolom Region, Area, 2nd City / Kota Penempatan, Nama AS, serta Fitur Pengaturan Kolom Dinamis di Kandidat Portal (30 September 2026), 34. 🗺️ Penyelarasan Penuh Data Region & Area (Job Statistik & Kandidat Portal) Berdasarkan Master `tb_area` (43 Area Resmi ESA Groups)

### Community 30 - "AppServiceProvider.php"
Cohesion: 0.22
Nodes (6): AppServiceProvider, Illuminate\Auth\Events\Failed, Illuminate\Auth\Events\Login, Illuminate\Auth\Events\Logout, Illuminate\Support\Facades\Event, Illuminate\Support\ServiceProvider

### Community 31 - "Panduan Implementasi WhatsApp API Official Coexistence (Coex) di ASystem"
Cohesion: 0.11
Nodes (17): 1. Pendahuluan: Apa itu WhatsApp Coexistence (Coex)?, 2. Perbandingan: Coex Official vs Library Unofficial (Baileys / WhatsApp.js), 3. Prasyarat & Persiapan, 4. Arsitektur Integrasi di ASystem (Laravel), 5.1. Konfigurasi Environment (`.env`), 5.2. Konfigurasi Services (`config/services.php`), 5.3. Service Provider: `app/Services/WhatsAppCoexService.php`, 5.4. Queue Job: `app/Jobs/SendWhatsAppCoexJob.php` (+9 more)

### Community 32 - "Closure"
Cohesion: 0.24
Nodes (7): EnsureCandidateAuthenticated, EnsureUserIsAdmin, PreventIndexingMiddleware, RedirectIfInstalled, Closure, Symfony\Component\HttpFoundation\Response, 2. 🔐 Manajemen Hak Akses Role & Proteksi Menu Master Data

### Community 33 - "OdooEntity"
Cohesion: 0.19
Nodes (4): OdooSettingController, OdooEntity, Illuminate\Http\JsonResponse, 73. 🛡️ Penuntasan Error Syntax `unexpected token '<',` pada Sync by NIK & Safe Parsing JSON (21 September 2026)

### Community 34 - "PasswordResetRequest"
Cohesion: 0.18
Nodes (4): AuthChatController, PasswordResetChatMessage, PasswordResetRequest, 40. 💬 Fitur Lupa Kata Sandi via Live Chat Administrator & Auto-Sync Odoo Karyawan

### Community 35 - "HelpdeskTicketController"
Cohesion: 0.23
Nodes (5): HelpdeskTicketController, HelpdeskWorkplanService, 62. 🛡️ Diferensiasi Hak Akses Dashboard & Modul Ticketing (User Biasa, User Divisi, Administrator) (24 September 2026), 65. 📎 Dukungan Multiple Lampiran (Gambar, PDF, Office Docs) & Modal Preview Interaktif, 67. 🔒 Fitur Penutupan & Buka Kembali Tiket oleh Pengaju (Requester Self-Close)

### Community 36 - "DatabaseSeeder.php"
Cohesion: 0.27
Nodes (4): CbtCandidateSeeder, DatabaseSeeder, OdooEntitySeeder, Illuminate\Database\Seeder

### Community 37 - "🚀 Panduan & Prosedur Deployment Server Production ASystem"
Cohesion: 0.14
Nodes (13): 🌐 1. Informasi Infrastruktur & Server, ⚡ 2. Cara Cepat Deploy (Metode Utama: 1-Click Remote Runner), 🖥️ 3. Metode Alternatif (Deploy Langsung via SSH Terminal), 📋 4. Standar Operasional Prosedur (SOP) Sebelum & Sesudah Deploy, ⏱️ 5. Layanan Latar Belakang (Cron & Worker) di Production, Eksekusi Deploy:, Mekanisme Kerja Skrip:, Opsi A: Menggunakan Shell Script Resmi (+5 more)

### Community 38 - "CandidateImportController"
Cohesion: 0.19
Nodes (4): CandidateImportController, CandidateImportService, 73. 🔄 Pembaruan Validasi Import Excel & Tarik NIK: Otomatis Mengarsipkan Data Sebelumnya untuk User/AS yang Sama dan Memblokir User/AS yang Berbeda (26 September 2026), 76. 🛡️ Penguncian Autentikasi Kredensial & Proteksi Menyeluruh Akses Data Kandidat Tanpa Login (28 September 2026)

### Community 40 - "InterviewPdfService"
Cohesion: 0.27
Nodes (5): InterviewPdfService, Mpdf, 14. 🏢 Pembersihan Master Data Prinsiple & Logo Entitas Dokumen Interview (18 September 2026), 19. ✍️ Digital Signature AS, Auto-Preload Tanda Tangan, & Dynamic PDF Export, 21. 🛑 Validasi Kriteria Kelulusan Interview, Disable Tab User Prinsiple & Tombol Download Dokumen

### Community 42 - "CandidateImportService.php"
Cohesion: 0.21
Nodes (5): ActivityLogXlsxExportService, App\Services\WorkPlanXlsxExportService, Exception, SimpleXMLElement, ZipArchive

### Community 44 - "Illuminate\Support\Facades\DB"
Cohesion: 0.16
Nodes (20): App\Http\Controllers\Helpdesk\HelpdeskTicketController, App\Http\Controllers\InterviewInhouseController, App\Http\Controllers\KandidatPortalController, App\Http\Controllers\WorkPlanController, App\Models\Candidate, App\Models\Employee, App\Models\JobSpec, App\Models\OdooEntity (+12 more)

### Community 45 - "Illuminate\Database\Eloquent\Relations\BelongsToMany"
Cohesion: 0.18
Nodes (4): Illuminate\Database\Eloquent\Relations\BelongsToMany, 42. 🛡️ Pemisahan Menu Sync Odoo ke Group 'System Setting' & Implementasi Sistem Manajemen Hak Akses (RBAC) Terpadu (19 September 2026), 43. 🛡️ Role Dinamis (CRUD), Pengaturan Scope Prinsiple Dihandle & Area Cover, serta Penyaringan Data Berdasarkan Role (19 September 2026), 68. 👥 Penyempurnaan Hak Akses Administrator Talent Pool (admin_officer) & Cakupan Scope Nasional Tanpa Pembatasan Rekruter (25 September 2026)

### Community 46 - "bootstrap/app.php"
Cohesion: 0.40
Nodes (3): Illuminate\Foundation\Application, Illuminate\Foundation\Configuration\Exceptions, Illuminate\Foundation\Configuration\Middleware

### Community 47 - "logging.php"
Cohesion: 0.40
Nodes (4): Monolog\Handler\NullHandler, Monolog\Handler\StreamHandler, Monolog\Handler\SyslogUdpHandler, Monolog\Processor\PsrLogMessageProcessor

### Community 50 - "🚀 Panduan Instalasi & Deployment ASystem Portal"
Cohesion: 0.20
Nodes (9): 🔐 Akun Login Bawaan (Default Administrator), 🛠️ Catatan Teknis File Database, METODE 1: cPanel / Shared Hosting (Paling Cepat & Mudah), METODE 2: Linux VPS / Cloud Server (Ubuntu / Debian / AlmaLinux), METODE 3: Windows Server (IIS / XAMPP / Laragon), METODE 4: Web Wizard Installer (Antarmuka Grafis), 🚀 Panduan Instalasi & Deployment ASystem Portal, ⏰ Pengaturan Otomatisasi (Cron Job / Background Worker) (+1 more)

### Community 52 - "ApprovalWorkflowStep"
Cohesion: 0.21
Nodes (4): ApprovalWorkflowStep, ApprovalWorkflowService, Illuminate\Support\Collection, 97. 🏛️ Penyempurnaan Alur Approval Bertingkat Surat Peringatan (Pimpinan Pembuat & HRD Dinamis), Form Fullwidth, Tanggal Otomatis & Kop Surat Entitas PDF (29 September 2026)

### Community 53 - "Controller"
Cohesion: 0.09
Nodes (23): ActivityLogController, App\Http\Controllers\AiRankingController, AiRankingController, App\Http\Controllers\CandidateImportController, App\Http\Controllers\Controller, Controller, App\Http\Controllers\EmployeeController, App\Http\Controllers\FeatureController (+15 more)

### Community 54 - "HelpdeskDivision"
Cohesion: 0.18
Nodes (3): HelpdeskDivisionController, HelpdeskDivision, 63. ⚡ Searchable Dropdown Agen Divisi, Template Masalah & Format Laporan Tiket, serta Master Template Laporan

### Community 58 - "InterviewAssessment"
Cohesion: 0.27
Nodes (3): ImportInterviewSqlDumpCommand, InterviewAssessment, 83. 🎯 Perbaikan Input Hasil Interview Kandidat Portal & Sinkronisasi Tahapan Odoo ERP (Joined) (28 September 2026)

### Community 60 - "Database\Factories\UserFactory"
Cohesion: 0.47
Nodes (4): Database\Factories\UserFactory, UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 65 - "HelpdeskTicketReply"
Cohesion: 0.22
Nodes (5): HelpdeskTicketReply, App\Models\TaskActivity, App\Models\TaskComment, TaskComment, App\Models\TaskNotification

### Community 68 - "CbtQuestionService"
Cohesion: 0.32
Nodes (3): CbtQuestionService, 38. 🧮 Penyelarasan Soal CBT Matematika dengan Master Soal Sistem Lama & Modul Master Soal Matematika Admin (19 September 2026), 40. 🧠 Penyelarasan Tes Kepribadian CBT dengan 40 Butir Soal Florence Littauer (tb_kepribadian), Modul Master Soal Kepribadian Admin, & Penyesuaian Hasil Dummy ke Sanguinis/Koleris (19 September 2026)

### Community 69 - "ActivityLogger"
Cohesion: 0.13
Nodes (10): App\Http\Controllers\CbtController, App\Http\Controllers\InterviewController, UserProfileController, ActivityLogger, OdooRecruitmentSyncService, Illuminate\Support\Facades\File, Illuminate\Validation\Rule, 86. 🛠️ Perbaikan Validasi Tarik / Sinkronisasi NIK Kandidat dari Odoo dan Penyelarasan Duplikat Arsip (28 September 2026) (+2 more)

### Community 70 - ".submitApply"
Cohesion: 0.29
Nodes (3): PublicJobController, Controller, 56. 📝 Form Job Apply: Seluruh Field Menjadi Mandatory (Wajib Diisi) & Notifikasi Interaktif Bagian yang Kurang (23 September 2026)

### Community 71 - "console.php"
Cohesion: 0.50
Nodes (3): Illuminate\Foundation\Inspiring, Illuminate\Support\Facades\Artisan, Illuminate\Support\Facades\Schedule

### Community 72 - "🚀 Ringkasan Perkembangan & Progress Update ASystem Portal"
Cohesion: 0.50
Nodes (3): 🖥️ Panduan Menjalankan Sistem Secara Lokal, 🚀 Ringkasan Perkembangan & Progress Update ASystem Portal, 📌 Ringkasan Umum

### Community 76 - "TestResult"
Cohesion: 0.47
Nodes (3): TestResult, 106. 🧮 Perbaikan Bug Alur Remidi Tes Matematika & Sinkronisasi Status Dashboard CBT Kandidat, 37. 🔄 Penyempurnaan Tombol Send Remidi: Reset Nilai Matematika ke NULL, Icon Silang Merah, Dynamic Increment Tes Ke (Tes Ke-2 dst.), & Re-Test Mandiri CBT (19 September 2026)

### Community 158 - "ASystem - Support System ESA Groups"
Cohesion: 0.40
Nodes (4): ASystem - Support System ESA Groups, Fitur Utama, Panduan Instalasi & Menjalankan, Persyaratan Sistem

## Knowledge Gaps
- **179 isolated node(s):** `axios`, `concurrently`, `laravel-vite-plugin`, `tailwindcss`, `@tailwindcss/vite` (+174 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 608 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **156 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Candidate` connect `Candidate` to `OdooSyncService`, `CbtController`, `Employee`, `Illuminate\Database\Eloquent\Model`, `MathQuestion`, `JobSpec`, `AiSetting`, `CandidateEvaluationDataService`, `AiAnalyzerService`, `Principle`, `KandidatPortalController`, `WorkExperience`, `CbtModuleTest`, `Role`, `UserPrinsiple`, `TbArea`, `Closure`, `DatabaseSeeder.php`, `CandidateImportController`, `InterviewPdfService`, `CandidateImportService.php`, `AiPdfService.php`, `Illuminate\Support\Facades\DB`, `ApprovalWorkflowStep`, `Controller`, `.getMissingProfileFields`, `InterviewAssessment`, `.testResults`, `.recruiter`, `ActivityLogger`, `.submitApply`, `.hasCv`, `TestResult`, `InhouseApproval`, `2026_09_28_110000_fix_job_portal_terima_candidates_status.php`, `.getInfoLowonganAttribute`?**
  _High betweenness centrality (0.171) - this node is a cross-community bridge._
- **Why does `User` connect `User` to `OdooSyncService`, `WorkPlanController`, `Employee`, `Illuminate\Database\Eloquent\Model`, `Illuminate\Http\Request`, `JobSpec`, `ApprovalWorkflowController`, `HelpdeskTicket`, `Principle`, `KandidatPortalController`, `.auth`, `Role`, `UserPrinsiple`, `PasswordResetRequest`, `HelpdeskTicketController`, `DatabaseSeeder.php`, `CandidateImportController`, `Illuminate\Database\Eloquent\Relations\HasMany`, `CandidateImportService.php`, `Illuminate\Support\Facades\DB`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `ApprovalWorkflowStep`, `Controller`, `HelpdeskDivision`, `.update`, `.isAdmin`, `Database\Factories\UserFactory`, `HelpdeskTicketReply`, `.index`, `ActivityLogger`, `ApprovalWorkflowController.php`, `InhouseApproval`, `InstallController.php`?**
  _High betweenness centrality (0.076) - this node is a cross-community bridge._
- **Why does `🏆 Milestone & Fitur yang Telah Diselesaikan` connect `🏆 Milestone & Fitur yang Telah Diselesaikan` to `OdooSyncService`, `Closure`, `OdooEntity`, `User`, `CbtQuestionService`, `Employee`, `InterviewPdfService`, `🚀 Ringkasan Perkembangan & Progress Update ASystem Portal`, `Illuminate\Http\Request`, `TestResult`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `CandidateEvaluationDataService`, `Principle`, `KandidatPortalController`, `WorkExperience`, `.auth`, `.getMissingProfileFields`, `.recruiter`?**
  _High betweenness centrality (0.060) - this node is a cross-community bridge._
- **Are the 7 inferred relationships involving `Candidate` (e.g. with `.index()` and `.show()`) actually correct?**
  _`Candidate` has 7 INFERRED edges - model-reasoned connections that need verification._
- **Are the 9 inferred relationships involving `User` (e.g. with `.handle()` and `.login()`) actually correct?**
  _`User` has 9 INFERRED edges - model-reasoned connections that need verification._
- **Are the 15 inferred relationships involving `Employee` (e.g. with `.handle()` and `.login()`) actually correct?**
  _`Employee` has 15 INFERRED edges - model-reasoned connections that need verification._
- **What connects `axios`, `concurrently`, `laravel-vite-plugin` to the rest of the system?**
  _179 weakly-connected nodes found - possible documentation gaps or missing edges._