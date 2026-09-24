# Graph Report - newasystem  (2026-09-24)

## Corpus Check
- 265 files · ~363,873 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 19 file(s) not represented in the graph (top: (none) 9, .bat 5, .example 1)

## Summary
- 1404 nodes · 3100 edges · 178 communities (41 shown, 137 thin omitted)
- Extraction: 95% EXTRACTED · 5% INFERRED · 0% AMBIGUOUS · INFERRED: 170 edges (avg confidence: 0.91)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `dde3a5a0`
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
- JobStatistikController.php
- Illuminate\Http\Request
- JobSpec
- UserPrinsiple
- ApprovalWorkflowStep
- Carbon\Carbon
- package.json
- Controller
- AiAnalyzerService
- Principle
- KandidatPortalController
- Illuminate\Database\Eloquent\Relations\HasMany
- AiSetting
- CbtModuleTest
- .auth
- 🏆 Milestone & Fitur yang Telah Diselesaikan
- Role
- 📜 Riwayat Commit & Pembaruan Kode
- Illuminate\Support\Facades\Schema
- Illuminate\Database\Migrations\Migration
- JobStatistikXlsxExportService
- Illuminate\Console\Command
- Panduan Implementasi WhatsApp API Official Coexistence (Coex) di ASystem
- Closure
- InterviewInhouseController
- CandidateXlsxExportService
- AppServiceProvider.php
- DatabaseSeeder.php
- 🚀 Panduan & Prosedur Deployment Server Production ASystem
- Illuminate\Support\Str
- InterviewPdfService
- PersonalityQuestion
- CandidateEvaluationDataService
- AiAnalyzerService.php
- LegacyAttachmentService
- JobController.php
- bootstrap/app.php
- logging.php
- InstallController.php
- ExampleTest
- 🚀 Panduan Instalasi & Deployment ASystem Portal
- artisan
- PublicJobController
- Illuminate\Database\Schema\Blueprint
- ActivityLog
- TbArea
- Illuminate\Support\Facades\DB
- UserFactory.php
- .update
- 25. 🎯 Pembatasan Ketat Data Kandidat Portal & Interview di Dashboard AS (Hanya Kandidat Milik AS Terkait)
- .getMissingProfileFields
- AiSettingController.php
- InhouseApproval
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
- .arsip
- 2026_09_22_123000_sync_tb_area_and_normalize_regions.php
- Panduan Pengembangan & Prosedur Update Codebase ASYSTEM
- TaskComment
- rules/graphify.md
- workflows/graphify.md
- template_import_kandidat_d4b107a9.md

## God Nodes (most connected - your core abstractions)
1. `Candidate` - 163 edges
2. `User` - 108 edges
3. `Employee` - 100 edges
4. `ActivityLogger` - 86 edges
5. `🏆 Milestone & Fitur yang Telah Diselesaikan` - 73 edges
6. `Principle` - 62 edges
7. `InterviewController` - 43 edges
8. `JobSpec` - 41 edges
9. `📜 Riwayat Commit & Pembaruan Kode` - 39 edges
10. `OdooEntity` - 38 edges

## Surprising Connections (you probably didn't know these)
- `61. 👥 Searchable Multi-Select Dropdown Anggota Tim & Penyesuaian Label "Groups Chat" (20 September 2026)` --references--> `WorkPlanChatController`  [INFERRED]
  UPDATE_PROGRESS.md → app/Http/Controllers/WorkPlanChatController.php
- `62. ⚡ Optimasi Kecepatan Ekstrim Groups Chat & Perbaikan Pembukaan Modal (20 September 2026)` --references--> `WorkPlanChatController`  [INFERRED]
  UPDATE_PROGRESS.md → app/Http/Controllers/WorkPlanChatController.php
- `53. ⚙️ Implementasi Role Akses Approver Dinamis & Workflow Engine Kandidat Inhouse (23 September 2026)` --references--> `Employee`  [INFERRED]
  UPDATE_PROGRESS.md → app/Models/Employee.php
- `35. 🧹 Pembersihan Akun Demo Jamil & Pengembalian Data Kandidat ke User Asli` --references--> `CandidateImportController`  [INFERRED]
  UPDATE_PROGRESS.md → app/Http/Controllers/CandidateImportController.php
- `35. 🧹 Pembersihan Akun Demo Jamil & Pengembalian Data Kandidat ke User Asli` --references--> `InterviewController`  [INFERRED]
  UPDATE_PROGRESS.md → app/Http/Controllers/InterviewController.php

## Import Cycles
- None detected.

## Communities (178 total, 137 thin omitted)

### Community 0 - "OdooEntity"
Cohesion: 0.07
Nodes (18): OdooSyncActiveCommand, OdooSyncCommand, OdooSyncUpdatesResignsCommand, OdooSettingController, OdooEntity, OdooSyncService, Illuminate\Http\JsonResponse, Illuminate\Support\Facades\Log (+10 more)

### Community 2 - "WorkExperience"
Cohesion: 0.06
Nodes (15): CleanDuplicateCandidatesCommand, ImportInterviewSqlDumpCommand, CandidateImportController, CbtController, PrincipleApprovalController, InterviewAssessment, PrincipleApproval, TestResult (+7 more)

### Community 3 - "User"
Cohesion: 0.13
Nodes (4): User, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable, 66. 🎨 Kustomisasi Tema Dashboard Personal (Light/Dark Mode, 4 Palet Gelap, Custom Accent Color) & Tampilan Jabatan User (20 September 2026)

### Community 4 - "WorkPlanController"
Cohesion: 0.11
Nodes (7): WorkPlanController, Task, TaskActivity, TaskNotification, WorkPlanXlsxExportService, 32. 📋 Resolusi Work Plan & To Do List Pasca-Migrasi: Pencocokan Nama Case-Insensitive & Akses Data Historis Arsip (Andi Kurniawan Distrianto & 65 User Lainnya), 58. 📋 Penyelarasan Akun Astri Wahyuni (ASTRI WAHYUNI,ST & HOD AR - Surabaya) & Sinkronisasi Presisi Salin Laporan WhatsApp Work Plan (24 September 2026)

### Community 5 - "composer.json"
Cohesion: 0.04
Nodes (48): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+40 more)

### Community 6 - "Employee"
Cohesion: 0.07
Nodes (6): SyncInhouseUsersCommand, Collection, Employee, 27. 🏢 Pengetatan Aturan Tipe Karyawan Inhouse (Hanya 5 Entitas Resmi) & Proteksi Akses Login, 64. 👤 Resolusi Nama Lengkap AS / Rekruter pada Export Excel (.xlsx) Kandidat Portal dari Data Karyawan (20 September 2026), 65. 🏷️ Penambahan Jabatan AS dan Eliminasi Fallback Administrator ESA pada Export Excel (.xlsx) Kandidat Portal (20 September 2026)

### Community 7 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.12
Nodes (9): ApprovalWorkflowStepUser, CandidateLog, OdooSyncLog, TaskCategory, TaskSubtask, WorkPlanDaily, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Database\Eloquent\Model (+1 more)

### Community 8 - "PasswordResetRequest"
Cohesion: 0.17
Nodes (4): AuthChatController, PasswordResetChatMessage, PasswordResetRequest, 40. 💬 Fitur Lupa Kata Sandi via Live Chat Administrator & Auto-Sync Odoo Karyawan

### Community 10 - "Illuminate\Http\Request"
Cohesion: 0.13
Nodes (5): InterviewController, RbacController, ActivityLogger, OdooRecruitmentSyncService, Illuminate\Http\Request

### Community 12 - "UserPrinsiple"
Cohesion: 0.27
Nodes (3): UserPrinsipleController, UserPrinsiple, Illuminate\Http\RedirectResponse

### Community 13 - "ApprovalWorkflowStep"
Cohesion: 0.11
Nodes (6): ApprovalWorkflowController, ApprovalWorkflow, ApprovalWorkflowStep, ApprovalWorkflowService, Illuminate\Support\Collection, 59. 👥 Resolusi Approver Step Approval: Auto-Sync Karyawan Inhouse Aktif ke Akun Pengguna & Pencarian Multi-Kata (Yohana Teraseptia Seagma) (24 September 2026)

### Community 14 - "Carbon\Carbon"
Cohesion: 0.21
Nodes (4): Carbon\Carbon, Illuminate\Support\Facades\Auth, Illuminate\Support\Facades\Hash, Illuminate\Support\Facades\Storage

### Community 15 - "package.json"
Cohesion: 0.09
Nodes (19): devDependencies, axios, concurrently, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite, private (+11 more)

### Community 16 - "Controller"
Cohesion: 0.18
Nodes (5): AiRankingController, Controller, FeatureController, HomeController, Illuminate\Support\Facades\Route

### Community 18 - "Principle"
Cohesion: 0.12
Nodes (5): CandidateController, PrincipleController, Principle, 10. ⚡ Optimasi Kecepatan Loading & Efek Animasi UI/UX Modern di Seluruh Halaman, 36. 📋 Penyelarasan Kolom Export Sesuai Sistem Lama, Link Server Produksi, & Label CV Analisa AI (19 September 2026)

### Community 20 - "Illuminate\Database\Eloquent\Relations\HasMany"
Cohesion: 0.13
Nodes (6): WorkPlanChatController, WpChatGroup, WpChatGroupMember, WpChatMessage, Illuminate\Database\Eloquent\Relations\HasMany, Illuminate\Database\Eloquent\Relations\HasOne

### Community 21 - "AiSetting"
Cohesion: 0.15
Nodes (3): AiSettingController, AiSetting, 45. 🤖 Integrasi AI OpenRouter, Hierarki Fallback Kuota (Gemini ➔ OpenRouter ➔ Sumopod), & Model Kustom Dinamis

### Community 22 - "CbtModuleTest"
Cohesion: 0.19
Nodes (5): Illuminate\Foundation\Testing\RefreshDatabase, Illuminate\Foundation\Testing\TestCase, CbtModuleTest, ExampleTest, TestCase

### Community 23 - ".auth"
Cohesion: 0.14
Nodes (4): AuthController, JobController, UserProfileController, 69. 🛡️ Sistem Audit Trail & Log Aktivitas Komprehensif Seluruh Sistem (20 September 2026)

### Community 24 - "🏆 Milestone & Fitur yang Telah Diselesaikan"
Cohesion: 0.04
Nodes (46): 10. 🎯 Pemisahan 4 Kategori Kandidat & Penambahan Kolom Jenis Kelamin, 17. 💻 Modernisasi Sinkronisasi Odoo dengan Live Streaming Terminal Console, 1. 🏠 Halaman Beranda / Home (Full-Width Welcome Hero), 22. ✍️ Isolasi & Personalisasi Tanda Tangan Pewawancara (AS), 23. 👥 Pemulihan Master User Prinsiple, Isolasi Data Per Pengguna, & Proteksi Anti-Duplikat Email/No HP, 24. 🔄 Rekonfigurasi Sinkronisasi Odoo ERP & Arsitektur Dual Background Cron Job, 26. 📍 Searchable Dropdown Filter Area & Default Pengurutan Join Date Terbaru (19 September 2026), 28. 👔 Penambahan Field Pimpinan di Form Edit Karyawan & Fitur Bulk Edit Pimpinan Massal (19 September 2026) (+38 more)

### Community 25 - "Role"
Cohesion: 0.18
Nodes (4): Permission, Role, Illuminate\Database\Eloquent\Relations\BelongsToMany, 43. 🛡️ Role Dinamis (CRUD), Pengaturan Scope Prinsiple Dihandle & Area Cover, serta Penyaringan Data Berdasarkan Role (19 September 2026)

### Community 26 - "📜 Riwayat Commit & Pembaruan Kode"
Cohesion: 0.05
Nodes (29): MathQuestionController, MathQuestion, 23. 🧮 Perbaikan Opsi Pilihan Ganda CBT Matematika & Normalisasi Master Soal, 24. 🔄 Penyempurnaan Sinkronisasi NIK Odoo Seluruh Entitas, 26. 👥 Pemisahan 2 Tabel Kandidat Interview (Milik Sendiri & Rekan Se-Area) & Tab Terintegrasi Administrator, 27. 🚶 Modul & Halaman Kandidat Walk-in Interview (`/walkinterview`) Sesuai Sistem Lama, 28. 📝 Formulir Registrasi Walkin Interview Publik, Cascading Master Data (`tb_area` & `tb_kota`), dan Searchable TomSelect, 29. 🎯 Filter Ketat Personel Inhouse pada Dropdown Nama AS / Rekrutor Beserta Tampilan Badge Jabatan (+21 more)

### Community 30 - "Illuminate\Console\Command"
Cohesion: 0.11
Nodes (9): CheckAstriCommand, CronAiAnalyzerCommand, ImportJobSpecsCommand, ImportLegacyInterviewCommand, ImportOfficialPrinciplesCommand, ImportWpDumpCommand, SyncAstriWpCommand, SyncOdooRecruitmentStagesCommand (+1 more)

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

### Community 36 - "DatabaseSeeder.php"
Cohesion: 0.27
Nodes (4): CbtCandidateSeeder, DatabaseSeeder, OdooEntitySeeder, Illuminate\Database\Seeder

### Community 37 - "🚀 Panduan & Prosedur Deployment Server Production ASystem"
Cohesion: 0.14
Nodes (13): 🌐 1. Informasi Infrastruktur & Server, ⚡ 2. Cara Cepat Deploy (Metode Utama: 1-Click Remote Runner), 🖥️ 3. Metode Alternatif (Deploy Langsung via SSH Terminal), 📋 4. Standar Operasional Prosedur (SOP) Sebelum & Sesudah Deploy, ⏱️ 5. Layanan Latar Belakang (Cron & Worker) di Production, Eksekusi Deploy:, Mekanisme Kerja Skrip:, Opsi A: Menggunakan Shell Script Resmi (+5 more)

### Community 40 - "InterviewPdfService"
Cohesion: 0.24
Nodes (5): InterviewPdfService, Mpdf, 14. 🏢 Pembersihan Master Data Prinsiple & Logo Entitas Dokumen Interview (18 September 2026), 19. ✍️ Digital Signature AS, Auto-Preload Tanda Tangan, & Dynamic PDF Export, 21. 🛑 Validasi Kriteria Kelulusan Interview, Disable Tab User Prinsiple & Tombol Download Dokumen

### Community 42 - "CandidateEvaluationDataService"
Cohesion: 0.18
Nodes (6): FixDummyMathResultsCommand, FixDummyPersonalityResultsCommand, CandidateEvaluationDataService, 20. 🎯 Sinkronisasi Evaluasi Nilai CBT & Status Hasil Tes Kandidat di Dashboard Rekruter, 39. 🧮 Penyesuaian Hasil Tes Matematika Dummy Menjadi Nilai B (Grade B - 70%) & Proteksi Data Lama (19 September 2026), 50. 📋 Penyelarasan Menyeluruh Data Riil Evaluasi Kandidat Inhouse (Interview, Refcek, Komputer, Kepribadian, & Matematika)

### Community 43 - "AiAnalyzerService.php"
Cohesion: 0.20
Nodes (4): AiPdfService, Illuminate\Support\Facades\Cache, Illuminate\Support\Facades\File, Illuminate\Validation\Rule

### Community 46 - "bootstrap/app.php"
Cohesion: 0.40
Nodes (3): Illuminate\Foundation\Application, Illuminate\Foundation\Configuration\Exceptions, Illuminate\Foundation\Configuration\Middleware

### Community 47 - "logging.php"
Cohesion: 0.40
Nodes (4): Monolog\Handler\NullHandler, Monolog\Handler\StreamHandler, Monolog\Handler\SyslogUdpHandler, Monolog\Processor\PsrLogMessageProcessor

### Community 48 - "InstallController.php"
Cohesion: 0.25
Nodes (4): InstallController, Illuminate\Foundation\Inspiring, Illuminate\Support\Facades\Artisan, Illuminate\Support\Facades\Schedule

### Community 50 - "🚀 Panduan Instalasi & Deployment ASystem Portal"
Cohesion: 0.20
Nodes (9): 🔐 Akun Login Bawaan (Default Administrator), 🛠️ Catatan Teknis File Database, METODE 1: cPanel / Shared Hosting (Paling Cepat & Mudah), METODE 2: Linux VPS / Cloud Server (Ubuntu / Debian / AlmaLinux), METODE 3: Windows Server (IIS / XAMPP / Laragon), METODE 4: Web Wizard Installer (Antarmuka Grafis), 🚀 Panduan Instalasi & Deployment ASystem Portal, ⏰ Pengaturan Otomatisasi (Cron Job / Background Worker) (+1 more)

### Community 59 - "ActivityLog"
Cohesion: 0.08
Nodes (5): activity_log(), ActivityLogController, ActivityLog, ActivityLogXlsxExportService, Throwable

### Community 63 - "Illuminate\Support\Facades\DB"
Cohesion: 0.12
Nodes (5): up(), Exception, Illuminate\Support\Facades\DB, Symfony\Component\HttpFoundation\StreamedResponse, ZipArchive

### Community 64 - "UserFactory.php"
Cohesion: 0.47
Nodes (3): UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 72 - "25. 🎯 Pembatasan Ketat Data Kandidat Portal & Interview di Dashboard AS (Hanya Kandidat Milik AS Terkait)"
Cohesion: 0.29
Nodes (3): 25. 🎯 Pembatasan Ketat Data Kandidat Portal & Interview di Dashboard AS (Hanya Kandidat Milik AS Terkait), 35. 🐛 Perbaikan Filter Export Kandidat Job Portal (19 September 2026), 42. 🛡️ Pemisahan Menu Sync Odoo ke Group 'System Setting' & Implementasi Sistem Manajemen Hak Akses (RBAC) Terpadu (19 September 2026)

### Community 158 - "ASystem - Support System ESA Groups"
Cohesion: 0.40
Nodes (4): ASystem - Support System ESA Groups, Fitur Utama, Panduan Instalasi & Menjalankan, Persyaratan Sistem

## Knowledge Gaps
- **163 isolated node(s):** `$schema`, `name`, `type`, `description`, `keywords` (+158 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 573 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **137 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Candidate` connect `Candidate` to `OdooEntity`, `WorkExperience`, `Employee`, `Illuminate\Database\Eloquent\Model`, `PasswordResetRequest`, `JobStatistikController.php`, `Illuminate\Http\Request`, `UserPrinsiple`, `ApprovalWorkflowStep`, `Carbon\Carbon`, `Controller`, `AiAnalyzerService`, `Principle`, `KandidatPortalController`, `CbtModuleTest`, `Role`, `Illuminate\Console\Command`, `Closure`, `InterviewInhouseController`, `DatabaseSeeder.php`, `InterviewPdfService`, `CandidateEvaluationDataService`, `AiAnalyzerService.php`, `PublicJobController`, `TbArea`, `Illuminate\Support\Facades\DB`, `25. 🎯 Pembatasan Ketat Data Kandidat Portal & Interview di Dashboard AS (Hanya Kandidat Milik AS Terkait)`, `.getMissingProfileFields`, `AiSettingController.php`, `InhouseApproval`?**
  _High betweenness centrality (0.181) - this node is a cross-community bridge._
- **Why does `Employee` connect `Employee` to `OdooEntity`, `User`, `WorkPlanController`, `Illuminate\Database\Eloquent\Model`, `PasswordResetRequest`, `JobStatistikController.php`, `Illuminate\Http\Request`, `JobSpec`, `ApprovalWorkflowStep`, `Carbon\Carbon`, `Controller`, `Principle`, `KandidatPortalController`, `Illuminate\Database\Eloquent\Relations\HasMany`, `.auth`, `Role`, `📜 Riwayat Commit & Pembaruan Kode`, `Illuminate\Console\Command`, `CandidateXlsxExportService`, `AiAnalyzerService.php`, `JobController.php`, `Illuminate\Support\Facades\DB`, `.update`?**
  _High betweenness centrality (0.075) - this node is a cross-community bridge._
- **Why does `User` connect `User` to `WorkExperience`, `WorkPlanController`, `Employee`, `Illuminate\Database\Eloquent\Model`, `PasswordResetRequest`, `Illuminate\Http\Request`, `JobSpec`, `UserPrinsiple`, `ApprovalWorkflowStep`, `Carbon\Carbon`, `Principle`, `KandidatPortalController`, `Illuminate\Database\Eloquent\Relations\HasMany`, `.auth`, `Role`, `Illuminate\Console\Command`, `InterviewInhouseController`, `DatabaseSeeder.php`, `.coversAllAreas`, `AiAnalyzerService.php`, `JobController.php`, `InstallController.php`, `ActivityLog`, `Illuminate\Support\Facades\DB`, `UserFactory.php`, `.update`, `25. 🎯 Pembatasan Ketat Data Kandidat Portal & Interview di Dashboard AS (Hanya Kandidat Milik AS Terkait)`, `InhouseApproval`?**
  _High betweenness centrality (0.074) - this node is a cross-community bridge._
- **Are the 5 inferred relationships involving `User` (e.g. with `.getMatchingApproversForCandidate()` and `.getAvatarUrl()`) actually correct?**
  _`User` has 5 INFERRED edges - model-reasoned connections that need verification._
- **Are the 8 inferred relationships involving `Employee` (e.g. with `.getMatchingRuleForCandidate()` and `.matchesCandidate()`) actually correct?**
  _`Employee` has 8 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _163 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `OdooEntity` be split into smaller, more focused modules?**
  _Cohesion score 0.07005649717514124 - nodes in this community are weakly interconnected._