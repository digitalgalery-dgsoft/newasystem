# Graph Report - newasystem  (2026-09-24)

## Corpus Check
- 286 files · ~377,379 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 19 file(s) not represented in the graph (top: (none) 9, .bat 5, .example 1)

## Summary
- 1498 nodes · 3387 edges · 178 communities (45 shown, 133 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 197 edges (avg confidence: 0.91)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `d710c801`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- OdooEntity
- Candidate
- CbtController
- User
- WorkPlanController
- composer.json
- Employee
- Illuminate\Database\Eloquent\Model
- PasswordResetRequest
- JobStatistikController.php
- InterviewController
- JobSpec
- UserPrinsiple
- ApprovalWorkflowStep
- Illuminate\Database\Eloquent\Relations\BelongsTo
- package.json
- Controller
- AiAnalyzerService
- Principle
- Illuminate\Http\Request
- WorkPlanChatController
- AiSetting
- CbtModuleTest
- .auth
- 🏆 Milestone & Fitur yang Telah Diselesaikan
- Role
- 📜 Riwayat Commit & Pembaruan Kode
- Illuminate\Database\Migrations\Migration
- Illuminate\Support\Facades\Schema
- JobStatistikXlsxExportService
- ImportLegacyInterviewCommand.php
- Panduan Implementasi WhatsApp API Official Coexistence (Coex) di ASystem
- Closure
- InterviewInhouseController
- CandidateXlsxExportService
- AppServiceProvider.php
- DatabaseSeeder.php
- 🚀 Panduan & Prosedur Deployment Server Production ASystem
- Illuminate\Database\Eloquent\Relations\HasMany
- InterviewPdfService
- PersonalityQuestion
- Illuminate\Support\Facades\DB
- AiPdfService
- LegacyAttachmentService
- .index
- bootstrap/app.php
- logging.php
- InstallController
- ExampleTest
- 🚀 Panduan Instalasi & Deployment ASystem Portal
- artisan
- ActivityLogger
- PublicJobController
- HelpdeskDivision
- HelpdeskTicket
- .log
- ActivityLog
- TbArea
- CandidateImportController
- Carbon\Carbon
- UserFactory.php
- ApprovalWorkflowService
- CandidateEvaluationDataService
- console.php
- 27. 🏢 Pengetatan Aturan Tipe Karyawan Inhouse (Hanya 5 Entitas Resmi) & Proteksi Akses Login
- 42. 🛡️ Pemisahan Menu Sync Odoo ke Group 'System Setting' & Implementasi Sistem Manajemen Hak Akses (RBAC) Terpadu (19 September 2026)
- .getMissingProfileFields
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
- helpdesk._kanban_card
- Panduan Pengembangan & Prosedur Update Codebase ASYSTEM
- rules/graphify.md
- workflows/graphify.md
- template_import_kandidat_d4b107a9.md

## God Nodes (most connected - your core abstractions)
1. `Candidate` - 163 edges
2. `User` - 123 edges
3. `Employee` - 102 edges
4. `ActivityLogger` - 86 edges
5. `🏆 Milestone & Fitur yang Telah Diselesaikan` - 73 edges
6. `Principle` - 62 edges
7. `InterviewController` - 43 edges
8. `JobSpec` - 41 edges
9. `📜 Riwayat Commit & Pembaruan Kode` - 40 edges
10. `Controller` - 38 edges

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

## Communities (178 total, 133 thin omitted)

### Community 0 - "OdooEntity"
Cohesion: 0.06
Nodes (25): ImportJobSpecsCommand, ImportOfficialPrinciplesCommand, ImportWpDumpCommand, OdooSyncActiveCommand, OdooSyncCommand, OdooSyncUpdatesResignsCommand, SyncOdooRecruitmentStagesCommand, OdooSettingController (+17 more)

### Community 2 - "CbtController"
Cohesion: 0.15
Nodes (4): CbtController, CbtQuestionService, 38. 🧮 Penyelarasan Soal CBT Matematika dengan Master Soal Sistem Lama & Modul Master Soal Matematika Admin (19 September 2026), 40. 🧠 Penyelarasan Tes Kepribadian CBT dengan 40 Butir Soal Florence Littauer (tb_kepribadian), Modul Master Soal Kepribadian Admin, & Penyesuaian Hasil Dummy ke Sanguinis/Koleris (19 September 2026)

### Community 3 - "User"
Cohesion: 0.11
Nodes (4): SyncInhouseUsersCommand, User, 59. 👥 Resolusi Approver Step Approval: Auto-Sync Karyawan Inhouse Aktif ke Akun Pengguna & Pencarian Multi-Kata (Yohana Teraseptia Seagma) (24 September 2026), 66. 🎨 Kustomisasi Tema Dashboard Personal (Light/Dark Mode, 4 Palet Gelap, Custom Accent Color) & Tampilan Jabatan User (20 September 2026)

### Community 4 - "WorkPlanController"
Cohesion: 0.08
Nodes (12): CheckAstriCommand, SyncAstriWpCommand, WorkPlanController, Task, TaskActivity, TaskComment, TaskNotification, TaskSubtask (+4 more)

### Community 5 - "composer.json"
Cohesion: 0.04
Nodes (48): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+40 more)

### Community 6 - "Employee"
Cohesion: 0.11
Nodes (4): EmployeeController, Employee, 64. 👤 Resolusi Nama Lengkap AS / Rekruter pada Export Excel (.xlsx) Kandidat Portal dari Data Karyawan (20 September 2026), 65. 🏷️ Penambahan Jabatan AS dan Eliminasi Fallback Administrator ESA pada Export Excel (.xlsx) Kandidat Portal (20 September 2026)

### Community 7 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.13
Nodes (6): AiSettingUser, CandidateLog, TaskCategory, WaAreaSetting, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Database\Eloquent\Model

### Community 8 - "PasswordResetRequest"
Cohesion: 0.16
Nodes (4): AuthChatController, PasswordResetChatMessage, PasswordResetRequest, 40. 💬 Fitur Lupa Kata Sandi via Live Chat Administrator & Auto-Sync Odoo Karyawan

### Community 10 - "InterviewController"
Cohesion: 0.10
Nodes (5): InterviewController, Collection, 16. 📂 Pemulihan Visibilitas Data Interview Selesai & Arsip Interview, 25. 🎯 Pembatasan Ketat Data Kandidat Portal & Interview di Dashboard AS (Hanya Kandidat Milik AS Terkait), 55. 🔒 Perbaikan Error Undefined $isAdmin, Isolasi Data Interview Selesai & Arsip per Rekrutor / AS, serta Akses Khusus Kandidat Inhouse 5 Entitas (Approval HRD & Head) (20 September 2026)

### Community 12 - "UserPrinsiple"
Cohesion: 0.23
Nodes (4): UserPrinsipleController, UserPrinsiple, Illuminate\Http\RedirectResponse, Illuminate\View\View

### Community 13 - "ApprovalWorkflowStep"
Cohesion: 0.14
Nodes (3): ApprovalWorkflowController, ApprovalWorkflow, ApprovalWorkflowStep

### Community 14 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.08
Nodes (3): ApprovalWorkflowStepUser, HelpdeskTicketReply, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 15 - "package.json"
Cohesion: 0.09
Nodes (19): devDependencies, axios, concurrently, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite, private (+11 more)

### Community 16 - "Controller"
Cohesion: 0.12
Nodes (9): AiRankingController, Controller, FeatureController, HelpdeskCannedController, HelpdeskDashboardController, HelpdeskCannedResponse, Illuminate\Support\Facades\Auth, Illuminate\Support\Facades\Route (+1 more)

### Community 18 - "Principle"
Cohesion: 0.13
Nodes (5): CandidateController, PrincipleController, Principle, 10. ⚡ Optimasi Kecepatan Loading & Efek Animasi UI/UX Modern di Seluruh Halaman, 36. 📋 Penyelarasan Kolom Export Sesuai Sistem Lama, Link Server Produksi, & Label CV Analisa AI (19 September 2026)

### Community 19 - "Illuminate\Http\Request"
Cohesion: 0.17
Nodes (3): KandidatPortalController, Illuminate\Http\Request, 35. 🧹 Pembersihan Akun Demo Jamil & Pengembalian Data Kandidat ke User Asli

### Community 20 - "WorkPlanChatController"
Cohesion: 0.25
Nodes (5): WorkPlanChatController, WpChatGroup, WpChatGroupMember, WpChatMessage, Illuminate\Database\Eloquent\Relations\HasOne

### Community 21 - "AiSetting"
Cohesion: 0.14
Nodes (3): AiSettingController, AiSetting, 45. 🤖 Integrasi AI OpenRouter, Hierarki Fallback Kuota (Gemini ➔ OpenRouter ➔ Sumopod), & Model Kustom Dinamis

### Community 22 - "CbtModuleTest"
Cohesion: 0.19
Nodes (5): Illuminate\Foundation\Testing\RefreshDatabase, Illuminate\Foundation\Testing\TestCase, CbtModuleTest, ExampleTest, TestCase

### Community 24 - "🏆 Milestone & Fitur yang Telah Diselesaikan"
Cohesion: 0.04
Nodes (47): 10. 🎯 Pemisahan 4 Kategori Kandidat & Penambahan Kolom Jenis Kelamin, 17. 💻 Modernisasi Sinkronisasi Odoo dengan Live Streaming Terminal Console, 1. 🏠 Halaman Beranda / Home (Full-Width Welcome Hero), 22. ✍️ Isolasi & Personalisasi Tanda Tangan Pewawancara (AS), 23. 👥 Pemulihan Master User Prinsiple, Isolasi Data Per Pengguna, & Proteksi Anti-Duplikat Email/No HP, 24. 🔄 Rekonfigurasi Sinkronisasi Odoo ERP & Arsitektur Dual Background Cron Job, 26. 📍 Searchable Dropdown Filter Area & Default Pengurutan Join Date Terbaru (19 September 2026), 28. 👔 Penambahan Field Pimpinan di Form Edit Karyawan & Fitur Bulk Edit Pimpinan Massal (19 September 2026) (+39 more)

### Community 25 - "Role"
Cohesion: 0.17
Nodes (3): RbacController, Permission, Role

### Community 26 - "📜 Riwayat Commit & Pembaruan Kode"
Cohesion: 0.05
Nodes (30): MathQuestionController, MathQuestion, up(), 23. 🧮 Perbaikan Opsi Pilihan Ganda CBT Matematika & Normalisasi Master Soal, 24. 🔄 Penyempurnaan Sinkronisasi NIK Odoo Seluruh Entitas, 26. 👥 Pemisahan 2 Tabel Kandidat Interview (Milik Sendiri & Rekan Se-Area) & Tab Terintegrasi Administrator, 27. 🚶 Modul & Halaman Kandidat Walk-in Interview (`/walkinterview`) Sesuai Sistem Lama, 28. 📝 Formulir Registrasi Walkin Interview Publik, Cascading Master Data (`tb_area` & `tb_kota`), dan Searchable TomSelect (+22 more)

### Community 27 - "Illuminate\Database\Migrations\Migration"
Cohesion: 0.04
Nodes (3): up(), Illuminate\Database\Migrations\Migration, Illuminate\Database\Schema\Blueprint

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

### Community 39 - "Illuminate\Database\Eloquent\Relations\HasMany"
Cohesion: 0.08
Nodes (4): Illuminate\Database\Eloquent\Relations\BelongsToMany, Illuminate\Database\Eloquent\Relations\HasMany, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable

### Community 40 - "InterviewPdfService"
Cohesion: 0.27
Nodes (5): InterviewPdfService, Mpdf, 14. 🏢 Pembersihan Master Data Prinsiple & Logo Entitas Dokumen Interview (18 September 2026), 19. ✍️ Digital Signature AS, Auto-Preload Tanda Tangan, & Dynamic PDF Export, 21. 🛑 Validasi Kriteria Kelulusan Interview, Disable Tab User Prinsiple & Tombol Download Dokumen

### Community 42 - "Illuminate\Support\Facades\DB"
Cohesion: 0.10
Nodes (15): CleanDuplicateCandidatesCommand, FixDummyMathResultsCommand, FixDummyPersonalityResultsCommand, ImportInterviewSqlDumpCommand, PrincipleApprovalController, InterviewAssessment, PrincipleApproval, TestResult (+7 more)

### Community 46 - "bootstrap/app.php"
Cohesion: 0.40
Nodes (3): Illuminate\Foundation\Application, Illuminate\Foundation\Configuration\Exceptions, Illuminate\Foundation\Configuration\Middleware

### Community 47 - "logging.php"
Cohesion: 0.40
Nodes (4): Monolog\Handler\NullHandler, Monolog\Handler\StreamHandler, Monolog\Handler\SyslogUdpHandler, Monolog\Processor\PsrLogMessageProcessor

### Community 50 - "🚀 Panduan Instalasi & Deployment ASystem Portal"
Cohesion: 0.20
Nodes (9): 🔐 Akun Login Bawaan (Default Administrator), 🛠️ Catatan Teknis File Database, METODE 1: cPanel / Shared Hosting (Paling Cepat & Mudah), METODE 2: Linux VPS / Cloud Server (Ubuntu / Debian / AlmaLinux), METODE 3: Windows Server (IIS / XAMPP / Laragon), METODE 4: Web Wizard Installer (Antarmuka Grafis), 🚀 Panduan Instalasi & Deployment ASystem Portal, ⏰ Pengaturan Otomatisasi (Cron Job / Background Worker) (+1 more)

### Community 52 - "ActivityLogger"
Cohesion: 0.12
Nodes (4): UserProfileController, ActivityLogger, ActivityLogXlsxExportService, Illuminate\Validation\Rule

### Community 54 - "HelpdeskDivision"
Cohesion: 0.16
Nodes (3): HelpdeskDivisionController, HelpdeskDivision, HelpdeskDivisionAgent

### Community 56 - "HelpdeskTicket"
Cohesion: 0.20
Nodes (4): HelpdeskTicketController, HelpdeskTicket, HelpdeskTicketLog, HelpdeskWorkplanService

### Community 59 - "ActivityLog"
Cohesion: 0.12
Nodes (3): activity_log(), ActivityLogController, ActivityLog

### Community 63 - "Carbon\Carbon"
Cohesion: 0.15
Nodes (6): Carbon\Carbon, Exception, Illuminate\Support\Facades\Cache, Illuminate\Support\Facades\Http, Illuminate\Support\Facades\Log, ZipArchive

### Community 64 - "UserFactory.php"
Cohesion: 0.47
Nodes (3): UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 69 - "CandidateEvaluationDataService"
Cohesion: 0.33
Nodes (4): CandidateEvaluationDataService, 20. 🎯 Sinkronisasi Evaluasi Nilai CBT & Status Hasil Tes Kandidat di Dashboard Rekruter, 39. 🧮 Penyesuaian Hasil Tes Matematika Dummy Menjadi Nilai B (Grade B - 70%) & Proteksi Data Lama (19 September 2026), 50. 📋 Penyelarasan Menyeluruh Data Riil Evaluasi Kandidat Inhouse (Interview, Refcek, Komputer, Kepribadian, & Matematika)

### Community 70 - "console.php"
Cohesion: 0.50
Nodes (3): Illuminate\Foundation\Inspiring, Illuminate\Support\Facades\Artisan, Illuminate\Support\Facades\Schedule

### Community 72 - "42. 🛡️ Pemisahan Menu Sync Odoo ke Group 'System Setting' & Implementasi Sistem Manajemen Hak Akses (RBAC) Terpadu (19 September 2026)"
Cohesion: 0.25
Nodes (3): 35. 🐛 Perbaikan Filter Export Kandidat Job Portal (19 September 2026), 42. 🛡️ Pemisahan Menu Sync Odoo ke Group 'System Setting' & Implementasi Sistem Manajemen Hak Akses (RBAC) Terpadu (19 September 2026), 43. 🛡️ Role Dinamis (CRUD), Pengaturan Scope Prinsiple Dihandle & Area Cover, serta Penyaringan Data Berdasarkan Role (19 September 2026)

### Community 158 - "ASystem - Support System ESA Groups"
Cohesion: 0.40
Nodes (4): ASystem - Support System ESA Groups, Fitur Utama, Panduan Instalasi & Menjalankan, Persyaratan Sistem

## Knowledge Gaps
- **164 isolated node(s):** `$schema`, `name`, `type`, `description`, `keywords` (+159 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 593 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **133 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Candidate` connect `Candidate` to `OdooEntity`, `CbtController`, `Employee`, `Illuminate\Database\Eloquent\Model`, `JobStatistikController.php`, `JobSpec`, `UserPrinsiple`, `Controller`, `AiAnalyzerService`, `Principle`, `Illuminate\Http\Request`, `CbtModuleTest`, `Role`, `📜 Riwayat Commit & Pembaruan Kode`, `ImportLegacyInterviewCommand.php`, `Closure`, `InterviewInhouseController`, `DatabaseSeeder.php`, `InterviewPdfService`, `Illuminate\Support\Facades\DB`, `AiPdfService`, `PublicJobController`, `TbArea`, `Carbon\Carbon`, `ApprovalWorkflowService`, `CandidateEvaluationDataService`, `42. 🛡️ Pemisahan Menu Sync Odoo ke Group 'System Setting' & Implementasi Sistem Manajemen Hak Akses (RBAC) Terpadu (19 September 2026)`, `.getMissingProfileFields`, `InhouseApproval`?**
  _High betweenness centrality (0.160) - this node is a cross-community bridge._
- **Why does `User` connect `User` to `OdooEntity`, `WorkPlanController`, `Employee`, `Illuminate\Database\Eloquent\Model`, `PasswordResetRequest`, `InterviewController`, `JobSpec`, `UserPrinsiple`, `ApprovalWorkflowStep`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Controller`, `Principle`, `Illuminate\Http\Request`, `.auth`, `Role`, `InterviewInhouseController`, `DatabaseSeeder.php`, `.coversAllAreas`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Support\Facades\DB`, `.index`, `InstallController`, `ActivityLogger`, `HelpdeskDivision`, `HelpdeskTicket`, `.log`, `ActivityLog`, `CandidateImportController`, `Carbon\Carbon`, `UserFactory.php`, `ApprovalWorkflowService`, `42. 🛡️ Pemisahan Menu Sync Odoo ke Group 'System Setting' & Implementasi Sistem Manajemen Hak Akses (RBAC) Terpadu (19 September 2026)`, `InhouseApproval`?**
  _High betweenness centrality (0.073) - this node is a cross-community bridge._
- **Why does `Employee` connect `Employee` to `OdooEntity`, `User`, `WorkPlanController`, `Illuminate\Database\Eloquent\Model`, `PasswordResetRequest`, `JobStatistikController.php`, `InterviewController`, `JobSpec`, `ApprovalWorkflowStep`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Controller`, `Principle`, `Illuminate\Http\Request`, `WorkPlanChatController`, `.auth`, `Role`, `📜 Riwayat Commit & Pembaruan Kode`, `CandidateXlsxExportService`, `Illuminate\Support\Facades\DB`, `.index`, `ActivityLogger`, `.log`, `Carbon\Carbon`, `ApprovalWorkflowService`, `27. 🏢 Pengetatan Aturan Tipe Karyawan Inhouse (Hanya 5 Entitas Resmi) & Proteksi Akses Login`?**
  _High betweenness centrality (0.072) - this node is a cross-community bridge._
- **Are the 5 inferred relationships involving `User` (e.g. with `.getMatchingApproversForCandidate()` and `.getAvatarUrl()`) actually correct?**
  _`User` has 5 INFERRED edges - model-reasoned connections that need verification._
- **Are the 8 inferred relationships involving `Employee` (e.g. with `.getMatchingRuleForCandidate()` and `.matchesCandidate()`) actually correct?**
  _`Employee` has 8 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _164 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `OdooEntity` be split into smaller, more focused modules?**
  _Cohesion score 0.05775803144224197 - nodes in this community are weakly interconnected._