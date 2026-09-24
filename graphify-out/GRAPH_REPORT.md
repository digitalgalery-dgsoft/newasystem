# Graph Report - newasystem  (2026-09-24)

## Corpus Check
- 286 files · ~377,379 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 19 file(s) not represented in the graph (top: (none) 9, .bat 5, .example 1)

## Summary
- 1500 nodes · 3380 edges · 187 communities (46 shown, 141 thin omitted)
- Extraction: 93% EXTRACTED · 7% INFERRED · 0% AMBIGUOUS · INFERRED: 233 edges (avg confidence: 0.9)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `5edf5707`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- OdooEntity
- Candidate
- WorkExperience
- User
- WorkPlanController
- composer.json
- Employee
- Illuminate\Database\Eloquent\Model
- PasswordResetRequest
- App\Http\Controllers\JobStatistikController
- InterviewController
- JobSpec
- UserPrinsiple
- ApprovalWorkflowStep
- MathQuestion
- package.json
- Controller
- AiAnalyzerService
- Principle
- Illuminate\Http\Request
- WorkPlanChatController
- AiSetting
- CbtModuleTest
- Task
- 🏆 Milestone & Fitur yang Telah Diselesaikan
- ActivityLogger
- 📜 Riwayat Commit & Pembaruan Kode
- Illuminate\Database\Schema\Blueprint
- Illuminate\Support\Facades\Schema
- JobStatistikXlsxExportService
- ImportLegacyInterviewCommand.php
- Panduan Implementasi WhatsApp API Official Coexistence (Coex) di ASystem
- Closure
- InterviewInhouseController
- CandidateXlsxExportService
- AppServiceProvider.php
- Illuminate\Database\Seeder
- 🚀 Panduan & Prosedur Deployment Server Production ASystem
- .arsip
- InterviewPdfService
- PersonalityQuestion
- CandidateEvaluationDataService
- AiPdfService
- LegacyAttachmentService
- App\Http\Controllers\JobController
- bootstrap/app.php
- logging.php
- AiAnalyzerService.php
- ExampleTest
- 🚀 Panduan Instalasi & Deployment ASystem Portal
- artisan
- 🚀 Ringkasan Perkembangan & Progress Update ASystem Portal
- PublicJobController
- .auth
- .getUserDisplayNameAttribute
- TaskSubtask
- ActivityLog
- TbArea
- CandidateXlsxExportService.php
- User.php
- Illuminate\Support\Facades\DB
- console.php
- 27. 🏢 Pengetatan Aturan Tipe Karyawan Inhouse (Hanya 5 Entitas Resmi) & Proteksi Akses Login
- Illuminate\Database\Eloquent\Relations\BelongsToMany
- Illuminate\Database\Migrations\Migration
- .getMissingProfileFields
- workplan/index.blade.php
- deploy.sh
- install.sh
- interview/show.blade.php
- kandidatportal/show.blade.php
- partials.page-loader
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
- WorkPlanDaily

## God Nodes (most connected - your core abstractions)
1. `Candidate` - 163 edges
2. `User` - 123 edges
3. `Employee` - 92 edges
4. `ActivityLogger` - 85 edges
5. `🏆 Milestone & Fitur yang Telah Diselesaikan` - 73 edges
6. `Principle` - 62 edges
7. `InterviewController` - 42 edges
8. `JobSpec` - 41 edges
9. `📜 Riwayat Commit & Pembaruan Kode` - 40 edges
10. `OdooEntity` - 38 edges

## Surprising Connections (you probably didn't know these)
- `61. 👥 Searchable Multi-Select Dropdown Anggota Tim & Penyesuaian Label "Groups Chat" (20 September 2026)` --references--> `WorkPlanChatController`  [INFERRED]
  UPDATE_PROGRESS.md → app/Http/Controllers/WorkPlanChatController.php
- `62. ⚡ Optimasi Kecepatan Ekstrim Groups Chat & Perbaikan Pembukaan Modal (20 September 2026)` --references--> `WorkPlanChatController`  [INFERRED]
  UPDATE_PROGRESS.md → app/Http/Controllers/WorkPlanChatController.php
- `53. ⚙️ Implementasi Role Akses Approver Dinamis & Workflow Engine Kandidat Inhouse (23 September 2026)` --references--> `Employee`  [INFERRED]
  UPDATE_PROGRESS.md → app/Models/Employee.php
- `9. 🗄️ Migrasi Penuh Database Legacy Interview (`asystemc_interview.sql`) & Evaluasi Dinamis` --references--> `InterviewController`  [INFERRED]
  UPDATE_PROGRESS.md → app/Http/Controllers/InterviewController.php
- `9. 🗄️ Migrasi Penuh Database Legacy Interview (`asystemc_interview.sql`) & Evaluasi Dinamis` --references--> `KandidatPortalController`  [INFERRED]
  UPDATE_PROGRESS.md → app/Http/Controllers/KandidatPortalController.php

## Import Cycles
- None detected.

## Communities (187 total, 141 thin omitted)

### Community 0 - "OdooEntity"
Cohesion: 0.05
Nodes (25): CheckAstriCommand, ImportJobSpecsCommand, ImportOfficialPrinciplesCommand, ImportWpDumpCommand, OdooSyncActiveCommand, OdooSyncCommand, OdooSyncUpdatesResignsCommand, SyncAstriWpCommand (+17 more)

### Community 2 - "WorkExperience"
Cohesion: 0.06
Nodes (17): CleanDuplicateCandidatesCommand, ImportInterviewSqlDumpCommand, CandidateImportController, App\Http\Controllers\CbtController, CbtController, App\Http\Controllers\PrincipleApprovalController, PrincipleApprovalController, InterviewAssessment (+9 more)

### Community 3 - "User"
Cohesion: 0.09
Nodes (11): SyncInhouseUsersCommand, App\Http\Controllers\AuthController, App\Http\Controllers\EmployeeController, App\Http\Controllers\InterviewController, User, DatabaseSeeder, Employee, Illuminate\Support\Facades\File (+3 more)

### Community 4 - "WorkPlanController"
Cohesion: 0.17
Nodes (6): WorkPlanController, TaskActivity, TaskNotification, Controller, 32. 📋 Resolusi Work Plan & To Do List Pasca-Migrasi: Pencocokan Nama Case-Insensitive & Akses Data Historis Arsip (Andi Kurniawan Distrianto & 65 User Lainnya), 58. 📋 Penyelarasan Akun Astri Wahyuni (ASTRI WAHYUNI,ST & HOD AR - Surabaya) & Sinkronisasi Presisi Salin Laporan WhatsApp Work Plan (24 September 2026)

### Community 5 - "composer.json"
Cohesion: 0.04
Nodes (48): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+40 more)

### Community 6 - "Employee"
Cohesion: 0.09
Nodes (4): EmployeeController, App\Http\Controllers\FeatureController, FeatureController, Employee

### Community 7 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.08
Nodes (12): App\Http\Controllers\AiSettingController, AiSettingUser, CandidateLog, App\Models\Employee, HelpdeskTicketReply, App\Models\TaskCategory, TaskCategory, WaAreaSetting (+4 more)

### Community 8 - "PasswordResetRequest"
Cohesion: 0.18
Nodes (4): AuthChatController, PasswordResetChatMessage, PasswordResetRequest, 40. 💬 Fitur Lupa Kata Sandi via Live Chat Administrator & Auto-Sync Odoo Karyawan

### Community 9 - "App\Http\Controllers\JobStatistikController"
Cohesion: 0.39
Nodes (3): App\Http\Controllers\JobStatistikController, JobStatistikController, Symfony\Component\HttpFoundation\BinaryFileResponse

### Community 10 - "InterviewController"
Cohesion: 0.10
Nodes (3): InterviewController, Collection, 25. 🎯 Pembatasan Ketat Data Kandidat Portal & Interview di Dashboard AS (Hanya Kandidat Milik AS Terkait)

### Community 12 - "UserPrinsiple"
Cohesion: 0.24
Nodes (3): UserPrinsipleController, UserPrinsiple, Illuminate\Http\RedirectResponse

### Community 13 - "ApprovalWorkflowStep"
Cohesion: 0.08
Nodes (8): ApprovalWorkflowController, ApprovalWorkflow, ApprovalWorkflowStep, ApprovalWorkflowStepUser, InhouseApproval, ApprovalWorkflowService, Illuminate\Support\Collection, 59. 👥 Resolusi Approver Step Approval: Auto-Sync Karyawan Inhouse Aktif ke Akun Pengguna & Pencarian Multi-Kata (Yohana Teraseptia Seagma) (24 September 2026)

### Community 14 - "MathQuestion"
Cohesion: 0.15
Nodes (3): MathQuestionController, MathQuestion, 23. 🧮 Perbaikan Opsi Pilihan Ganda CBT Matematika & Normalisasi Master Soal

### Community 15 - "package.json"
Cohesion: 0.09
Nodes (19): devDependencies, axios, concurrently, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite, private (+11 more)

### Community 16 - "Controller"
Cohesion: 0.12
Nodes (6): ActivityLogController, App\Http\Controllers\AiRankingController, AiRankingController, AuthController, Controller, InstallController

### Community 18 - "Principle"
Cohesion: 0.10
Nodes (13): CandidateController, App\Http\Controllers\HomeController, HomeController, App\Http\Controllers\InterviewInhouseController, App\Http\Controllers\KandidatPortalController, App\Http\Controllers\PrincipleController, PrincipleController, App\Http\Controllers\PublicJobController (+5 more)

### Community 19 - "Illuminate\Http\Request"
Cohesion: 0.15
Nodes (4): KandidatPortalController, OdooRecruitmentSyncService, Illuminate\Http\Request, 35. 🧹 Pembersihan Akun Demo Jamil & Pengembalian Data Kandidat ke User Asli

### Community 20 - "WorkPlanChatController"
Cohesion: 0.27
Nodes (6): App\Http\Controllers\WorkPlanChatController, WorkPlanChatController, WpChatGroup, WpChatGroupMember, WpChatMessage, Illuminate\Database\Eloquent\Relations\HasOne

### Community 21 - "AiSetting"
Cohesion: 0.14
Nodes (3): AiSettingController, AiSetting, 45. 🤖 Integrasi AI OpenRouter, Hierarki Fallback Kuota (Gemini ➔ OpenRouter ➔ Sumopod), & Model Kustom Dinamis

### Community 22 - "CbtModuleTest"
Cohesion: 0.19
Nodes (5): Illuminate\Foundation\Testing\RefreshDatabase, Illuminate\Foundation\Testing\TestCase, CbtModuleTest, ExampleTest, TestCase

### Community 23 - "Task"
Cohesion: 0.14
Nodes (5): Task, App\Models\TaskActivity, App\Models\TaskComment, TaskComment, App\Models\TaskNotification

### Community 24 - "🏆 Milestone & Fitur yang Telah Diselesaikan"
Cohesion: 0.04
Nodes (47): 10. 🎯 Pemisahan 4 Kategori Kandidat & Penambahan Kolom Jenis Kelamin, 17. 💻 Modernisasi Sinkronisasi Odoo dengan Live Streaming Terminal Console, 1. 🏠 Halaman Beranda / Home (Full-Width Welcome Hero), 22. ✍️ Isolasi & Personalisasi Tanda Tangan Pewawancara (AS), 23. 👥 Pemulihan Master User Prinsiple, Isolasi Data Per Pengguna, & Proteksi Anti-Duplikat Email/No HP, 24. 🔄 Rekonfigurasi Sinkronisasi Odoo ERP & Arsitektur Dual Background Cron Job, 26. 📍 Searchable Dropdown Filter Area & Default Pengurutan Join Date Terbaru (19 September 2026), 28. 👔 Penambahan Field Pimpinan di Form Edit Karyawan & Fitur Bulk Edit Pimpinan Massal (19 September 2026) (+39 more)

### Community 25 - "ActivityLogger"
Cohesion: 0.17
Nodes (5): RbacController, UserProfileController, Permission, Role, ActivityLogger

### Community 26 - "📜 Riwayat Commit & Pembaruan Kode"
Cohesion: 0.09
Nodes (23): 24. 🔄 Penyempurnaan Sinkronisasi NIK Odoo Seluruh Entitas, 26. 👥 Pemisahan 2 Tabel Kandidat Interview (Milik Sendiri & Rekan Se-Area) & Tab Terintegrasi Administrator, 27. 🚶 Modul & Halaman Kandidat Walk-in Interview (`/walkinterview`) Sesuai Sistem Lama, 28. 📝 Formulir Registrasi Walkin Interview Publik, Cascading Master Data (`tb_area` & `tb_kota`), dan Searchable TomSelect, 29. 🎯 Filter Ketat Personel Inhouse pada Dropdown Nama AS / Rekrutor Beserta Tampilan Badge Jabatan, 30. 📅 Penyempurnaan Input Tanggal Lahir Profesional dengan Indikator Usia Otomatis & Proteksi Input, 31. 📲 QR Code Khusus per Job & Salin Pesan Broadcast WhatsApp dengan Tautan Langsung Detail Lowongan, 33. 📅 Fitur Pengelompokan (Grouping) & Filter Tanggal pada Tugas Diarsipkan (Archive Section) (+15 more)

### Community 28 - "Illuminate\Support\Facades\Schema"
Cohesion: 0.11
Nodes (3): App\Http\Controllers\CandidateImportController, Illuminate\Support\Facades\Schema, Symfony\Component\HttpFoundation\StreamedResponse

### Community 31 - "Panduan Implementasi WhatsApp API Official Coexistence (Coex) di ASystem"
Cohesion: 0.11
Nodes (17): 1. Pendahuluan: Apa itu WhatsApp Coexistence (Coex)?, 2. Perbandingan: Coex Official vs Library Unofficial (Baileys / WhatsApp.js), 3. Prasyarat & Persiapan, 4. Arsitektur Integrasi di ASystem (Laravel), 5.1. Konfigurasi Environment (`.env`), 5.2. Konfigurasi Services (`config/services.php`), 5.3. Service Provider: `app/Services/WhatsAppCoexService.php`, 5.4. Queue Job: `app/Jobs/SendWhatsAppCoexJob.php` (+9 more)

### Community 32 - "Closure"
Cohesion: 0.29
Nodes (6): EnsureCandidateAuthenticated, EnsureUserIsAdmin, RedirectIfInstalled, Closure, Symfony\Component\HttpFoundation\Response, 2. 🔐 Manajemen Hak Akses Role & Proteksi Menu Master Data

### Community 33 - "InterviewInhouseController"
Cohesion: 0.23
Nodes (3): InterviewInhouseController, 38. 🏢 Pembatasan Ketat Approval Inhouse Khusus 5 Entitas Resmi & Pemulihan Approval Prinsiple, 9. 🗄️ Migrasi Penuh Database Legacy Interview (`asystemc_interview.sql`) & Evaluasi Dinamis

### Community 35 - "AppServiceProvider.php"
Cohesion: 0.22
Nodes (6): AppServiceProvider, Illuminate\Auth\Events\Failed, Illuminate\Auth\Events\Login, Illuminate\Auth\Events\Logout, Illuminate\Support\Facades\Event, Illuminate\Support\ServiceProvider

### Community 36 - "Illuminate\Database\Seeder"
Cohesion: 0.38
Nodes (3): CbtCandidateSeeder, OdooEntitySeeder, Illuminate\Database\Seeder

### Community 37 - "🚀 Panduan & Prosedur Deployment Server Production ASystem"
Cohesion: 0.14
Nodes (13): 🌐 1. Informasi Infrastruktur & Server, ⚡ 2. Cara Cepat Deploy (Metode Utama: 1-Click Remote Runner), 🖥️ 3. Metode Alternatif (Deploy Langsung via SSH Terminal), 📋 4. Standar Operasional Prosedur (SOP) Sebelum & Sesudah Deploy, ⏱️ 5. Layanan Latar Belakang (Cron & Worker) di Production, Eksekusi Deploy:, Mekanisme Kerja Skrip:, Opsi A: Menggunakan Shell Script Resmi (+5 more)

### Community 40 - "InterviewPdfService"
Cohesion: 0.24
Nodes (5): InterviewPdfService, Mpdf, 14. 🏢 Pembersihan Master Data Prinsiple & Logo Entitas Dokumen Interview (18 September 2026), 19. ✍️ Digital Signature AS, Auto-Preload Tanda Tangan, & Dynamic PDF Export, 21. 🛑 Validasi Kriteria Kelulusan Interview, Disable Tab User Prinsiple & Tombol Download Dokumen

### Community 42 - "CandidateEvaluationDataService"
Cohesion: 0.18
Nodes (6): FixDummyMathResultsCommand, FixDummyPersonalityResultsCommand, CandidateEvaluationDataService, 20. 🎯 Sinkronisasi Evaluasi Nilai CBT & Status Hasil Tes Kandidat di Dashboard Rekruter, 39. 🧮 Penyesuaian Hasil Tes Matematika Dummy Menjadi Nilai B (Grade B - 70%) & Proteksi Data Lama (19 September 2026), 50. 📋 Penyelarasan Menyeluruh Data Riil Evaluasi Kandidat Inhouse (Interview, Refcek, Komputer, Kepribadian, & Matematika)

### Community 45 - "App\Http\Controllers\JobController"
Cohesion: 0.29
Nodes (3): App\Http\Controllers\JobController, IndonesiaRegionService, Illuminate\Support\Facades\Http

### Community 46 - "bootstrap/app.php"
Cohesion: 0.40
Nodes (3): Illuminate\Foundation\Application, Illuminate\Foundation\Configuration\Exceptions, Illuminate\Foundation\Configuration\Middleware

### Community 47 - "logging.php"
Cohesion: 0.40
Nodes (4): Monolog\Handler\NullHandler, Monolog\Handler\StreamHandler, Monolog\Handler\SyslogUdpHandler, Monolog\Processor\PsrLogMessageProcessor

### Community 50 - "🚀 Panduan Instalasi & Deployment ASystem Portal"
Cohesion: 0.20
Nodes (9): 🔐 Akun Login Bawaan (Default Administrator), 🛠️ Catatan Teknis File Database, METODE 1: cPanel / Shared Hosting (Paling Cepat & Mudah), METODE 2: Linux VPS / Cloud Server (Ubuntu / Debian / AlmaLinux), METODE 3: Windows Server (IIS / XAMPP / Laragon), METODE 4: Web Wizard Installer (Antarmuka Grafis), 🚀 Panduan Instalasi & Deployment ASystem Portal, ⏰ Pengaturan Otomatisasi (Cron Job / Background Worker) (+1 more)

### Community 52 - "🚀 Ringkasan Perkembangan & Progress Update ASystem Portal"
Cohesion: 0.50
Nodes (3): 🖥️ Panduan Menjalankan Sistem Secara Lokal, 🚀 Ringkasan Perkembangan & Progress Update ASystem Portal, 📌 Ringkasan Umum

### Community 54 - ".auth"
Cohesion: 0.05
Nodes (16): App\Http\Controllers\Controller, HelpdeskCannedController, HelpdeskDashboardController, HelpdeskDivisionController, HelpdeskTicketController, JobController, HelpdeskCannedResponse, HelpdeskDivision (+8 more)

### Community 59 - "ActivityLog"
Cohesion: 0.11
Nodes (4): activity_log(), ActivityLog, App\Services\ActivityLogger, Throwable

### Community 63 - "CandidateXlsxExportService.php"
Cohesion: 0.15
Nodes (5): ActivityLogXlsxExportService, App\Services\WorkPlanXlsxExportService, WorkPlanXlsxExportService, Exception, ZipArchive

### Community 64 - "User.php"
Cohesion: 0.28
Nodes (6): Database\Factories\UserFactory, UserFactory, Illuminate\Database\Eloquent\Factories\Factory, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable, static

### Community 67 - "Illuminate\Support\Facades\DB"
Cohesion: 0.08
Nodes (3): up(), Illuminate\Support\Facades\DB, SimpleXMLElement

### Community 70 - "console.php"
Cohesion: 0.50
Nodes (3): Illuminate\Foundation\Inspiring, Illuminate\Support\Facades\Artisan, Illuminate\Support\Facades\Schedule

### Community 72 - "Illuminate\Database\Eloquent\Relations\BelongsToMany"
Cohesion: 0.17
Nodes (4): Illuminate\Database\Eloquent\Relations\BelongsToMany, 35. 🐛 Perbaikan Filter Export Kandidat Job Portal (19 September 2026), 42. 🛡️ Pemisahan Menu Sync Odoo ke Group 'System Setting' & Implementasi Sistem Manajemen Hak Akses (RBAC) Terpadu (19 September 2026), 43. 🛡️ Role Dinamis (CRUD), Pengaturan Scope Prinsiple Dihandle & Area Cover, serta Penyaringan Data Berdasarkan Role (19 September 2026)

### Community 158 - "ASystem - Support System ESA Groups"
Cohesion: 0.40
Nodes (4): ASystem - Support System ESA Groups, Fitur Utama, Panduan Instalasi & Menjalankan, Persyaratan Sistem

## Knowledge Gaps
- **164 isolated node(s):** `📌 Ringkasan Umum`, `1. 🏠 Halaman Beranda / Home (Full-Width Welcome Hero)`, `3. 👤 Prosedur Login & Autentikasi Karyawan`, `4. 👥 Penyempurnaan Master Karyawan`, `5. 🔄 Integrasi Sinkronisasi Odoo ERP (5 Entitas)` (+159 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 594 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **141 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Candidate` connect `Candidate` to `OdooEntity`, `WorkExperience`, `User`, `Illuminate\Database\Eloquent\Model`, `App\Http\Controllers\JobStatistikController`, `UserPrinsiple`, `ApprovalWorkflowStep`, `MathQuestion`, `Controller`, `AiAnalyzerService`, `Principle`, `Illuminate\Http\Request`, `CbtModuleTest`, `ActivityLogger`, `Illuminate\Support\Facades\Schema`, `ImportLegacyInterviewCommand.php`, `Closure`, `InterviewInhouseController`, `Illuminate\Database\Seeder`, `InterviewPdfService`, `CandidateEvaluationDataService`, `AiPdfService`, `AiAnalyzerService.php`, `PublicJobController`, `.getUserDisplayNameAttribute`, `TbArea`, `CandidateXlsxExportService.php`, `Illuminate\Support\Facades\DB`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `.getMissingProfileFields`?**
  _High betweenness centrality (0.141) - this node is a cross-community bridge._
- **Why does `User` connect `User` to `OdooEntity`, `WorkExperience`, `Employee`, `Illuminate\Database\Eloquent\Model`, `PasswordResetRequest`, `InterviewController`, `JobSpec`, `UserPrinsiple`, `ApprovalWorkflowStep`, `Controller`, `Principle`, `Illuminate\Http\Request`, `Task`, `ActivityLogger`, `Illuminate\Support\Facades\Schema`, `InterviewInhouseController`, `.coversAllAreas`, `App\Http\Controllers\JobController`, `.auth`, `.getUserDisplayNameAttribute`, `ActivityLog`, `CandidateXlsxExportService.php`, `User.php`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`?**
  _High betweenness centrality (0.078) - this node is a cross-community bridge._
- **Why does `🏆 Milestone & Fitur yang Telah Diselesaikan` connect `🏆 Milestone & Fitur yang Telah Diselesaikan` to `OdooEntity`, `Closure`, `WorkExperience`, `User`, `InterviewInhouseController`, `.arsip`, `InterviewPdfService`, `27. 🏢 Pengetatan Aturan Tipe Karyawan Inhouse (Hanya 5 Entitas Resmi) & Proteksi Akses Login`, `CandidateEvaluationDataService`, `.getMissingProfileFields`, `Illuminate\Database\Eloquent\Relations\BelongsToMany`, `Principle`, `🚀 Ringkasan Perkembangan & Progress Update ASystem Portal`, `.auth`, `.getUserDisplayNameAttribute`?**
  _High betweenness centrality (0.055) - this node is a cross-community bridge._
- **Are the 5 inferred relationships involving `User` (e.g. with `.getMatchingApproversForCandidate()` and `.getAvatarUrl()`) actually correct?**
  _`User` has 5 INFERRED edges - model-reasoned connections that need verification._
- **Are the 7 inferred relationships involving `Employee` (e.g. with `.getMatchingRuleForCandidate()` and `.matchesCandidate()`) actually correct?**
  _`Employee` has 7 INFERRED edges - model-reasoned connections that need verification._
- **What connects `📌 Ringkasan Umum`, `1. 🏠 Halaman Beranda / Home (Full-Width Welcome Hero)`, `3. 👤 Prosedur Login & Autentikasi Karyawan` to the rest of the system?**
  _164 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `OdooEntity` be split into smaller, more focused modules?**
  _Cohesion score 0.05308641975308642 - nodes in this community are weakly interconnected._