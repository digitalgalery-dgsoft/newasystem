# Graph Report - newasystem  (2026-09-28)

## Corpus Check
- 295 files · ~401,078 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 19 file(s) not represented in the graph (top: (none) 9, .bat 5, .example 1)

## Summary
- 1524 nodes · 3469 edges · 197 communities (48 shown, 149 thin omitted)
- Extraction: 92% EXTRACTED · 8% INFERRED · 0% AMBIGUOUS · INFERRED: 284 edges (avg confidence: 0.91)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `34e7732f`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- OdooSyncLog
- Candidate
- Illuminate\Console\Command
- User
- WorkPlanController
- composer.json
- Employee
- Illuminate\Database\Eloquent\Model
- MathQuestion
- web.php
- ActivityLogger
- JobSpec
- UserPrinsiple
- ApprovalWorkflowStep
- HelpdeskTicket
- package.json
- Illuminate\Support\Facades\DB
- AiAnalyzerService
- Principle
- KandidatPortalController
- WorkPlanChatController
- AiSetting
- CbtModuleTest
- Illuminate\Database\Eloquent\Relations\BelongsTo
- 🏆 Milestone & Fitur yang Telah Diselesaikan
- Illuminate\Http\Request
- 📜 Riwayat Commit & Pembaruan Kode
- Illuminate\Database\Schema\Blueprint
- TbArea
- OdooSyncService
- Panduan Implementasi WhatsApp API Official Coexistence (Coex) di ASystem
- Closure
- InterviewInhouseController
- PasswordResetRequest
- AppServiceProvider.php
- DatabaseSeeder.php
- 🚀 Panduan & Prosedur Deployment Server Production ASystem
- Illuminate\Database\Eloquent\Relations\HasMany
- InterviewPdfService
- PersonalityQuestion
- JobController
- AiPdfService
- .auth
- .index
- bootstrap/app.php
- logging.php
- OdooEntity
- ExampleTest
- 🚀 Panduan Instalasi & Deployment ASystem Portal
- artisan
- ApprovalWorkflowService.php
- HelpdeskCannedResponse
- HelpdeskDivision
- Role
- .update
- Illuminate\Support\Facades\Schema
- ActivityLog
- OdooSettingController
- ActivityLogXlsxExportService
- Database\Factories\UserFactory
- PrincipleApproval
- .handle
- InstallController
- console.php
- .canViewAllCandidates
- Illuminate\Database\Migrations\Migration
- .getMissingProfileFields
- .done
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

## God Nodes (most connected - your core abstractions)
1. `Candidate` - 163 edges
2. `User` - 124 edges
3. `Employee` - 93 edges
4. `ActivityLogger` - 86 edges
5. `🏆 Milestone & Fitur yang Telah Diselesaikan` - 73 edges
6. `Principle` - 62 edges
7. `📜 Riwayat Commit & Pembaruan Kode` - 55 edges
8. `InterviewController` - 43 edges
9. `JobSpec` - 41 edges
10. `OdooEntity` - 38 edges

## Surprising Connections (you probably didn't know these)
- `36. 📋 Penyelarasan Kolom Export Sesuai Sistem Lama, Link Server Produksi, & Label CV Analisa AI (19 September 2026)` --references--> `Principle`  [INFERRED]
  UPDATE_PROGRESS.md → app/Models/Principle.php
- `61. 👥 Searchable Multi-Select Dropdown Anggota Tim & Penyesuaian Label "Groups Chat" (20 September 2026)` --references--> `WorkPlanChatController`  [INFERRED]
  UPDATE_PROGRESS.md → app/Http/Controllers/WorkPlanChatController.php
- `62. ⚡ Optimasi Kecepatan Ekstrim Groups Chat & Perbaikan Pembukaan Modal (20 September 2026)` --references--> `WorkPlanChatController`  [INFERRED]
  UPDATE_PROGRESS.md → app/Http/Controllers/WorkPlanChatController.php
- `53. ⚙️ Implementasi Role Akses Approver Dinamis & Workflow Engine Kandidat Inhouse (23 September 2026)` --references--> `Employee`  [INFERRED]
  UPDATE_PROGRESS.md → app/Models/Employee.php
- `34. 🗺️ Penyelarasan Penuh Data Region & Area (Job Statistik & Kandidat Portal) Berdasarkan Master `tb_area` (43 Area Resmi ESA Groups)` --references--> `Candidate`  [INFERRED]
  UPDATE_PROGRESS.md → app/Models/Candidate.php

## Import Cycles
- None detected.

## Communities (197 total, 149 thin omitted)

### Community 0 - "OdooSyncLog"
Cohesion: 0.15
Nodes (8): OdooSyncActiveCommand, OdooSyncCommand, OdooSyncUpdatesResignsCommand, OdooSyncLog, Illuminate\Support\Str, Pdo\Mysql, 39. 🔄 Penyempurnaan Sinkronisasi Odoo: Mutasi Lintas Entitas & Reaktivasi Karyawan Resign, 41. 🔄 Perbaikan Kritis Sinkronisasi Odoo: Penanganan departure_date Terjadwal & Prioritas Entitas Aktif

### Community 2 - "Illuminate\Console\Command"
Cohesion: 0.05
Nodes (21): CheckAstriCommand, FixDummyMathResultsCommand, FixDummyPersonalityResultsCommand, ImportJobSpecsCommand, ImportLegacyInterviewCommand, ImportOfficialPrinciplesCommand, ImportWpDumpCommand, SyncAstriWpCommand (+13 more)

### Community 3 - "User"
Cohesion: 0.12
Nodes (5): User, Employee, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable, 66. 🎨 Kustomisasi Tema Dashboard Personal (Light/Dark Mode, 4 Palet Gelap, Custom Accent Color) & Tampilan Jabatan User (20 September 2026)

### Community 4 - "WorkPlanController"
Cohesion: 0.08
Nodes (10): WorkPlanController, Task, TaskActivity, TaskNotification, TaskSubtask, WorkPlanDaily, HelpdeskWorkplanService, 32. 📋 Resolusi Work Plan & To Do List Pasca-Migrasi: Pencocokan Nama Case-Insensitive & Akses Data Historis Arsip (Andi Kurniawan Distrianto & 65 User Lainnya) (+2 more)

### Community 5 - "composer.json"
Cohesion: 0.04
Nodes (48): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+40 more)

### Community 6 - "Employee"
Cohesion: 0.08
Nodes (6): Collection, Employee, 27. 🏢 Pengetatan Aturan Tipe Karyawan Inhouse (Hanya 5 Entitas Resmi) & Proteksi Akses Login, 64. 👤 Resolusi Nama Lengkap AS / Rekruter pada Export Excel (.xlsx) Kandidat Portal dari Data Karyawan (20 September 2026), 65. 🏷️ Penambahan Jabatan AS dan Eliminasi Fallback Administrator ESA pada Export Excel (.xlsx) Kandidat Portal (20 September 2026), 69. 📑 Standardisasi & Harmonisasi Kolom Nama AS pada Export Excel Kandidat Portal & Filter Web (25 September 2026)

### Community 7 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.15
Nodes (16): App\Http\Controllers\AiSettingController, App\Http\Controllers\WorkPlanController, AiSettingUser, App\Models\Employee, App\Models\TaskActivity, App\Models\TaskCategory, TaskCategory, App\Models\TaskComment (+8 more)

### Community 8 - "MathQuestion"
Cohesion: 0.08
Nodes (8): AttachmentController, MathQuestionController, MathQuestion, App\Services\LegacyAttachmentService, LegacyAttachmentService, Controller, up(), 23. 🧮 Perbaikan Opsi Pilihan Ganda CBT Matematika & Normalisasi Master Soal

### Community 9 - "web.php"
Cohesion: 0.11
Nodes (20): App\Http\Controllers\AiRankingController, App\Http\Controllers\AuthController, App\Http\Controllers\EmployeeController, App\Http\Controllers\FeatureController, App\Http\Controllers\Helpdesk\HelpdeskDivisionController, App\Http\Controllers\Helpdesk\HelpdeskTemplateController, App\Http\Controllers\Helpdesk\HelpdeskTicketController, App\Http\Controllers\HomeController (+12 more)

### Community 11 - "JobSpec"
Cohesion: 0.07
Nodes (5): HomeController, PublicJobController, JobSpec, Illuminate\Support\Facades\Cache, 56. 📝 Form Job Apply: Seluruh Field Menjadi Mandatory (Wajib Diisi) & Notifikasi Interaktif Bagian yang Kurang (23 September 2026)

### Community 12 - "UserPrinsiple"
Cohesion: 0.23
Nodes (3): UserPrinsipleController, UserPrinsiple, Illuminate\Http\RedirectResponse

### Community 13 - "ApprovalWorkflowStep"
Cohesion: 0.19
Nodes (3): ApprovalWorkflowController, ApprovalWorkflowStep, 59. 👥 Resolusi Approver Step Approval: Auto-Sync Karyawan Inhouse Aktif ke Akun Pengguna & Pencarian Multi-Kata (Yohana Teraseptia Seagma) (24 September 2026)

### Community 14 - "HelpdeskTicket"
Cohesion: 0.09
Nodes (9): App\Http\Controllers\Helpdesk\HelpdeskDashboardController, HelpdeskDashboardController, HelpdeskTicketController, HelpdeskDivisionAgent, HelpdeskTicket, HelpdeskTicketLog, 62. 🛡️ Diferensiasi Hak Akses Dashboard & Modul Ticketing (User Biasa, User Divisi, Administrator) (24 September 2026), 65. 📎 Dukungan Multiple Lampiran (Gambar, PDF, Office Docs) & Modal Preview Interaktif (+1 more)

### Community 15 - "package.json"
Cohesion: 0.09
Nodes (19): devDependencies, axios, concurrently, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite, private (+11 more)

### Community 16 - "Illuminate\Support\Facades\DB"
Cohesion: 0.13
Nodes (11): CleanDuplicateCandidatesCommand, App\Http\Controllers\CandidateImportController, App\Http\Controllers\CbtController, App\Http\Controllers\InterviewController, App\Http\Controllers\KandidatPortalController, InterviewAssessment, WorkExperience, OdooRecruitmentSyncService (+3 more)

### Community 17 - "AiAnalyzerService"
Cohesion: 0.15
Nodes (3): CronAiAnalyzerCommand, AiAnalyzerService, 57. ⚡ Perbaikan Masalah Input Token AI CV Analyzer Melonjak Ekstrem (2.114.589 Token) & Sanitasi Gambar Base64 Job Requirement (24 September 2026)

### Community 18 - "Principle"
Cohesion: 0.14
Nodes (7): CandidateController, App\Http\Controllers\JobController, App\Http\Controllers\PrincipleController, PrincipleController, Principle, Illuminate\Support\Facades\Http, 10. ⚡ Optimasi Kecepatan Loading & Efek Animasi UI/UX Modern di Seluruh Halaman

### Community 19 - "KandidatPortalController"
Cohesion: 0.16
Nodes (3): KandidatPortalController, 35. 🧹 Pembersihan Akun Demo Jamil & Pengembalian Data Kandidat ke User Asli, 75. 🎯 Perbaikan Hak Akses & Pembatasan Tampilan Kandidat Portal Sesuai AS User (28 September 2026)

### Community 20 - "WorkPlanChatController"
Cohesion: 0.23
Nodes (6): App\Http\Controllers\WorkPlanChatController, WorkPlanChatController, WpChatGroup, WpChatGroupMember, WpChatMessage, Illuminate\Database\Eloquent\Relations\HasOne

### Community 21 - "AiSetting"
Cohesion: 0.14
Nodes (3): AiSettingController, AiSetting, 45. 🤖 Integrasi AI OpenRouter, Hierarki Fallback Kuota (Gemini ➔ OpenRouter ➔ Sumopod), & Model Kustom Dinamis

### Community 22 - "CbtModuleTest"
Cohesion: 0.19
Nodes (5): Illuminate\Foundation\Testing\RefreshDatabase, Illuminate\Foundation\Testing\TestCase, CbtModuleTest, ExampleTest, TestCase

### Community 23 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.13
Nodes (5): ApprovalWorkflowStepUser, CandidateLog, HelpdeskTicketReply, Illuminate\Database\Eloquent\Relations\BelongsTo, 64. 🖼️ Perbaikan Tampilan Lampiran Gambar Tiket & Balasan Helpdesk

### Community 24 - "🏆 Milestone & Fitur yang Telah Diselesaikan"
Cohesion: 0.04
Nodes (47): 10. 🎯 Pemisahan 4 Kategori Kandidat & Penambahan Kolom Jenis Kelamin, 17. 💻 Modernisasi Sinkronisasi Odoo dengan Live Streaming Terminal Console, 1. 🏠 Halaman Beranda / Home (Full-Width Welcome Hero), 22. ✍️ Isolasi & Personalisasi Tanda Tangan Pewawancara (AS), 23. 👥 Pemulihan Master User Prinsiple, Isolasi Data Per Pengguna, & Proteksi Anti-Duplikat Email/No HP, 24. 🔄 Rekonfigurasi Sinkronisasi Odoo ERP & Arsitektur Dual Background Cron Job, 26. 📍 Searchable Dropdown Filter Area & Default Pengurutan Join Date Terbaru (19 September 2026), 28. 👔 Penambahan Field Pimpinan di Form Edit Karyawan & Fitur Bulk Edit Pimpinan Massal (19 September 2026) (+39 more)

### Community 25 - "Illuminate\Http\Request"
Cohesion: 0.17
Nodes (4): RbacController, Permission, Illuminate\Http\Request, 73. 🛡️ Penuntasan Error Syntax `unexpected token '<',` pada Sync by NIK & Safe Parsing JSON (21 September 2026)

### Community 26 - "📜 Riwayat Commit & Pembaruan Kode"
Cohesion: 0.05
Nodes (34): CandidateImportController, CandidateImportService, 24. 🔄 Penyempurnaan Sinkronisasi NIK Odoo Seluruh Entitas, 26. 👥 Pemisahan 2 Tabel Kandidat Interview (Milik Sendiri & Rekan Se-Area) & Tab Terintegrasi Administrator, 27. 🚶 Modul & Halaman Kandidat Walk-in Interview (`/walkinterview`) Sesuai Sistem Lama, 28. 📝 Formulir Registrasi Walkin Interview Publik, Cascading Master Data (`tb_area` & `tb_kota`), dan Searchable TomSelect, 29. 🎯 Filter Ketat Personel Inhouse pada Dropdown Nama AS / Rekrutor Beserta Tampilan Badge Jabatan, 30. 📅 Penyempurnaan Input Tanggal Lahir Profesional dengan Indikator Usia Otomatis & Proteksi Input (+26 more)

### Community 29 - "TbArea"
Cohesion: 0.06
Nodes (9): JobStatistikController, TbArea, CandidateXlsxExportService, JobStatistikXlsxExportService, App\Services\WorkPlanXlsxExportService, WorkPlanXlsxExportService, Exception, 34. 🗺️ Penyelarasan Penuh Data Region & Area (Job Statistik & Kandidat Portal) Berdasarkan Master `tb_area` (43 Area Resmi ESA Groups) (+1 more)

### Community 30 - "OdooSyncService"
Cohesion: 0.19
Nodes (7): OdooSyncService, SimpleXMLElement, 25. 🔍 Filter Searchable Dropdown Prinsiple/Jabatan, Eksklusi PT BUDGET, & Deduplikasi Master Prinsiple (19 September 2026), 42. 🔒 Proteksi Email & Password Karyawan saat Pembaruan Data dari Odoo ERP, 43. 🧹 Pengecualian Akun Sistem/Dummy (Prefix OD-) dari Sinkronisasi Odoo & Pembersihan Database, 74. ⚡ Optimalisasi Performa Master Karyawan & Perbaikan Logika Sync by NIK (21 September 2026), 74. 📱 Proteksi Nomor Telepon / HP Karyawan dari Penimpaan Sinkronisasi Odoo (28 September 2026)

### Community 31 - "Panduan Implementasi WhatsApp API Official Coexistence (Coex) di ASystem"
Cohesion: 0.11
Nodes (17): 1. Pendahuluan: Apa itu WhatsApp Coexistence (Coex)?, 2. Perbandingan: Coex Official vs Library Unofficial (Baileys / WhatsApp.js), 3. Prasyarat & Persiapan, 4. Arsitektur Integrasi di ASystem (Laravel), 5.1. Konfigurasi Environment (`.env`), 5.2. Konfigurasi Services (`config/services.php`), 5.3. Service Provider: `app/Services/WhatsAppCoexService.php`, 5.4. Queue Job: `app/Jobs/SendWhatsAppCoexJob.php` (+9 more)

### Community 32 - "Closure"
Cohesion: 0.29
Nodes (6): EnsureCandidateAuthenticated, EnsureUserIsAdmin, RedirectIfInstalled, Closure, Symfony\Component\HttpFoundation\Response, 2. 🔐 Manajemen Hak Akses Role & Proteksi Menu Master Data

### Community 33 - "InterviewInhouseController"
Cohesion: 0.24
Nodes (3): InterviewInhouseController, 38. 🏢 Pembatasan Ketat Approval Inhouse Khusus 5 Entitas Resmi & Pemulihan Approval Prinsiple, 9. 🗄️ Migrasi Penuh Database Legacy Interview (`asystemc_interview.sql`) & Evaluasi Dinamis

### Community 34 - "PasswordResetRequest"
Cohesion: 0.19
Nodes (4): AuthChatController, PasswordResetChatMessage, PasswordResetRequest, 40. 💬 Fitur Lupa Kata Sandi via Live Chat Administrator & Auto-Sync Odoo Karyawan

### Community 35 - "AppServiceProvider.php"
Cohesion: 0.18
Nodes (7): AppServiceProvider, Illuminate\Auth\Events\Failed, Illuminate\Auth\Events\Login, Illuminate\Auth\Events\Logout, Illuminate\Support\Facades\Event, Illuminate\Support\ServiceProvider, 69. 🛡️ Sistem Audit Trail & Log Aktivitas Komprehensif Seluruh Sistem (20 September 2026)

### Community 36 - "DatabaseSeeder.php"
Cohesion: 0.27
Nodes (4): CbtCandidateSeeder, DatabaseSeeder, OdooEntitySeeder, Illuminate\Database\Seeder

### Community 37 - "🚀 Panduan & Prosedur Deployment Server Production ASystem"
Cohesion: 0.14
Nodes (13): 🌐 1. Informasi Infrastruktur & Server, ⚡ 2. Cara Cepat Deploy (Metode Utama: 1-Click Remote Runner), 🖥️ 3. Metode Alternatif (Deploy Langsung via SSH Terminal), 📋 4. Standar Operasional Prosedur (SOP) Sebelum & Sesudah Deploy, ⏱️ 5. Layanan Latar Belakang (Cron & Worker) di Production, Eksekusi Deploy:, Mekanisme Kerja Skrip:, Opsi A: Menggunakan Shell Script Resmi (+5 more)

### Community 40 - "InterviewPdfService"
Cohesion: 0.27
Nodes (5): InterviewPdfService, Mpdf, 14. 🏢 Pembersihan Master Data Prinsiple & Logo Entitas Dokumen Interview (18 September 2026), 19. ✍️ Digital Signature AS, Auto-Preload Tanda Tangan, & Dynamic PDF Export, 21. 🛑 Validasi Kriteria Kelulusan Interview, Disable Tab User Prinsiple & Tombol Download Dokumen

### Community 44 - ".auth"
Cohesion: 0.18
Nodes (6): ActivityLogController, AiRankingController, AuthController, Controller, FeatureController, UserProfileController

### Community 46 - "bootstrap/app.php"
Cohesion: 0.40
Nodes (3): Illuminate\Foundation\Application, Illuminate\Foundation\Configuration\Exceptions, Illuminate\Foundation\Configuration\Middleware

### Community 47 - "logging.php"
Cohesion: 0.40
Nodes (4): Monolog\Handler\NullHandler, Monolog\Handler\StreamHandler, Monolog\Handler\SyslogUdpHandler, Monolog\Processor\PsrLogMessageProcessor

### Community 48 - "OdooEntity"
Cohesion: 0.17
Nodes (3): OdooEntity, Illuminate\Support\Facades\Log, self

### Community 50 - "🚀 Panduan Instalasi & Deployment ASystem Portal"
Cohesion: 0.20
Nodes (9): 🔐 Akun Login Bawaan (Default Administrator), 🛠️ Catatan Teknis File Database, METODE 1: cPanel / Shared Hosting (Paling Cepat & Mudah), METODE 2: Linux VPS / Cloud Server (Ubuntu / Debian / AlmaLinux), METODE 3: Windows Server (IIS / XAMPP / Laragon), METODE 4: Web Wizard Installer (Antarmuka Grafis), 🚀 Panduan Instalasi & Deployment ASystem Portal, ⏰ Pengaturan Otomatisasi (Cron Job / Background Worker) (+1 more)

### Community 52 - "ApprovalWorkflowService.php"
Cohesion: 0.14
Nodes (4): ApprovalWorkflow, InhouseApproval, ApprovalWorkflowService, Illuminate\Support\Collection

### Community 53 - "HelpdeskCannedResponse"
Cohesion: 0.29
Nodes (4): App\Http\Controllers\Controller, App\Http\Controllers\Helpdesk\HelpdeskCannedController, HelpdeskCannedController, HelpdeskCannedResponse

### Community 54 - "HelpdeskDivision"
Cohesion: 0.22
Nodes (3): HelpdeskDivisionController, HelpdeskDivision, 63. ⚡ Searchable Dropdown Agen Divisi, Template Masalah & Format Laporan Tiket, serta Master Template Laporan

### Community 60 - "OdooSettingController"
Cohesion: 0.33
Nodes (3): OdooSettingController, Illuminate\Http\JsonResponse, Illuminate\View\View

### Community 64 - "Database\Factories\UserFactory"
Cohesion: 0.47
Nodes (4): Database\Factories\UserFactory, UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 70 - "console.php"
Cohesion: 0.50
Nodes (3): Illuminate\Foundation\Inspiring, Illuminate\Support\Facades\Artisan, Illuminate\Support\Facades\Schedule

### Community 72 - ".canViewAllCandidates"
Cohesion: 0.21
Nodes (5): 25. 🎯 Pembatasan Ketat Data Kandidat Portal & Interview di Dashboard AS (Hanya Kandidat Milik AS Terkait), 35. 🐛 Perbaikan Filter Export Kandidat Job Portal (19 September 2026), 42. 🛡️ Pemisahan Menu Sync Odoo ke Group 'System Setting' & Implementasi Sistem Manajemen Hak Akses (RBAC) Terpadu (19 September 2026), 43. 🛡️ Role Dinamis (CRUD), Pengaturan Scope Prinsiple Dihandle & Area Cover, serta Penyaringan Data Berdasarkan Role (19 September 2026), 68. 👥 Penyempurnaan Hak Akses Administrator Talent Pool (admin_officer) & Cakupan Scope Nasional Tanpa Pembatasan Rekruter (25 September 2026)

### Community 158 - "ASystem - Support System ESA Groups"
Cohesion: 0.40
Nodes (4): ASystem - Support System ESA Groups, Fitur Utama, Panduan Instalasi & Menjalankan, Persyaratan Sistem

## Knowledge Gaps
- **165 isolated node(s):** `axios`, `concurrently`, `laravel-vite-plugin`, `tailwindcss`, `@tailwindcss/vite` (+160 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 594 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **149 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Candidate` connect `Candidate` to `Illuminate\Console\Command`, `Employee`, `Illuminate\Database\Eloquent\Model`, `MathQuestion`, `web.php`, `JobSpec`, `UserPrinsiple`, `Illuminate\Support\Facades\DB`, `AiAnalyzerService`, `Principle`, `KandidatPortalController`, `CbtModuleTest`, `Illuminate\Http\Request`, `📜 Riwayat Commit & Pembaruan Kode`, `TbArea`, `Closure`, `InterviewInhouseController`, `DatabaseSeeder.php`, `InterviewPdfService`, `AiPdfService`, `.auth`, `OdooEntity`, `ApprovalWorkflowService.php`, `PrincipleApproval`, `.handle`, `.canViewAllCandidates`, `.getMissingProfileFields`?**
  _High betweenness centrality (0.151) - this node is a cross-community bridge._
- **Why does `User` connect `User` to `Illuminate\Console\Command`, `WorkPlanController`, `Employee`, `Illuminate\Database\Eloquent\Model`, `web.php`, `ActivityLogger`, `JobSpec`, `ApprovalWorkflowStep`, `HelpdeskTicket`, `Illuminate\Support\Facades\DB`, `Principle`, `KandidatPortalController`, `Illuminate\Http\Request`, `📜 Riwayat Commit & Pembaruan Kode`, `TbArea`, `InterviewInhouseController`, `PasswordResetRequest`, `DatabaseSeeder.php`, `.coversAllAreas`, `Illuminate\Database\Eloquent\Relations\HasMany`, `.auth`, `.index`, `ApprovalWorkflowService.php`, `HelpdeskDivision`, `Role`, `.update`, `OdooSettingController`, `Database\Factories\UserFactory`, `InstallController`, `.canViewAllCandidates`?**
  _High betweenness centrality (0.097) - this node is a cross-community bridge._
- **Why does `Employee` connect `Employee` to `Illuminate\Console\Command`, `WorkPlanController`, `Illuminate\Database\Eloquent\Model`, `web.php`, `ActivityLogger`, `JobSpec`, `ApprovalWorkflowStep`, `Illuminate\Support\Facades\DB`, `Principle`, `KandidatPortalController`, `WorkPlanChatController`, `Illuminate\Http\Request`, `📜 Riwayat Commit & Pembaruan Kode`, `TbArea`, `OdooSyncService`, `PasswordResetRequest`, `JobController`, `.auth`, `.index`, `OdooEntity`, `ApprovalWorkflowService.php`, `.update`, `OdooSettingController`?**
  _High betweenness centrality (0.054) - this node is a cross-community bridge._
- **Are the 6 inferred relationships involving `User` (e.g. with `.getMatchingApproversForCandidate()` and `.getAvatarUrl()`) actually correct?**
  _`User` has 6 INFERRED edges - model-reasoned connections that need verification._
- **Are the 8 inferred relationships involving `Employee` (e.g. with `.getMatchingRuleForCandidate()` and `.matchesCandidate()`) actually correct?**
  _`Employee` has 8 INFERRED edges - model-reasoned connections that need verification._
- **Are the 8 inferred relationships involving `ActivityLogger` (e.g. with `.archive()` and `.destroy()`) actually correct?**
  _`ActivityLogger` has 8 INFERRED edges - model-reasoned connections that need verification._
- **What connects `axios`, `concurrently`, `laravel-vite-plugin` to the rest of the system?**
  _165 weakly-connected nodes found - possible documentation gaps or missing edges._