# Graph Report - newasystem  (2026-09-28)

## Corpus Check
- 301 files · ~406,812 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 19 file(s) not represented in the graph (top: (none) 9, .bat 5, .example 1)

## Summary
- 1544 nodes · 3530 edges · 177 communities (45 shown, 132 thin omitted)
- Extraction: 91% EXTRACTED · 9% INFERRED · 0% AMBIGUOUS · INFERRED: 321 edges (avg confidence: 0.9)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `61470680`
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
- Illuminate\Support\Facades\DB
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
- JobStatistikXlsxExportService
- AppServiceProvider.php
- Panduan Implementasi WhatsApp API Official Coexistence (Coex) di ASystem
- Closure
- CandidateController
- PasswordResetRequest
- CandidateEvaluationDataService
- DatabaseSeeder.php
- 🚀 Panduan & Prosedur Deployment Server Production ASystem
- JobStatistikController
- Illuminate\Database\Eloquent\Relations\HasMany
- InterviewPdfService
- PersonalityQuestion
- TbArea
- AiPdfService.php
- Illuminate\Support\Facades\Auth
- PublicJobController
- bootstrap/app.php
- logging.php
- ExampleTest
- 🚀 Panduan Instalasi & Deployment ASystem Portal
- artisan
- ApprovalWorkflowStep
- InstallController
- HelpdeskDivision
- Permission
- .coversAllAreas
- .getMissingProfileFields
- .update
- ActivityLog
- Database\Factories\UserFactory
- Illuminate\Database\Eloquent\Relations\BelongsTo
- CandidateXlsxExportService
- .done
- OdooRecruitmentSyncService
- ActivityLogXlsxExportService
- Illuminate\Support\Facades\Schema
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
1. `Candidate` - 142 edges
2. `User` - 121 edges
3. `Employee` - 91 edges
4. `ActivityLogger` - 85 edges
5. `🏆 Milestone & Fitur yang Telah Diselesaikan` - 73 edges
6. `📜 Riwayat Commit & Pembaruan Kode` - 61 edges
7. `Principle` - 60 edges
8. `InterviewController` - 43 edges
9. `JobSpec` - 40 edges
10. `OdooSyncService` - 38 edges

## Surprising Connections (you probably didn't know these)
- `36. 📋 Penyelarasan Kolom Export Sesuai Sistem Lama, Link Server Produksi, & Label CV Analisa AI (19 September 2026)` --references--> `Principle`  [INFERRED]
  UPDATE_PROGRESS.md → app/Models/Principle.php
- `61. 👥 Searchable Multi-Select Dropdown Anggota Tim & Penyesuaian Label "Groups Chat" (20 September 2026)` --references--> `WorkPlanChatController`  [INFERRED]
  UPDATE_PROGRESS.md → app/Http/Controllers/WorkPlanChatController.php
- `62. ⚡ Optimasi Kecepatan Ekstrim Groups Chat & Perbaikan Pembukaan Modal (20 September 2026)` --references--> `WorkPlanChatController`  [INFERRED]
  UPDATE_PROGRESS.md → app/Http/Controllers/WorkPlanChatController.php
- `53. ⚙️ Implementasi Role Akses Approver Dinamis & Workflow Engine Kandidat Inhouse (23 September 2026)` --references--> `Employee`  [INFERRED]
  UPDATE_PROGRESS.md → app/Models/Employee.php
- `9. 🗄️ Migrasi Penuh Database Legacy Interview (`asystemc_interview.sql`) & Evaluasi Dinamis` --references--> `InterviewController`  [INFERRED]
  UPDATE_PROGRESS.md → app/Http/Controllers/InterviewController.php

## Import Cycles
- None detected.

## Communities (177 total, 132 thin omitted)

### Community 0 - "OdooSyncService"
Cohesion: 0.10
Nodes (14): OdooSyncActiveCommand, OdooSyncCommand, OdooSyncUpdatesResignsCommand, OdooSettingController, OdooEntity, OdooSyncLog, OdooSyncService, Illuminate\Http\JsonResponse (+6 more)

### Community 2 - "CbtController"
Cohesion: 0.15
Nodes (4): CbtController, CbtQuestionService, 38. 🧮 Penyelarasan Soal CBT Matematika dengan Master Soal Sistem Lama & Modul Master Soal Matematika Admin (19 September 2026), 40. 🧠 Penyelarasan Tes Kepribadian CBT dengan 40 Butir Soal Florence Littauer (tb_kepribadian), Modul Master Soal Kepribadian Admin, & Penyesuaian Hasil Dummy ke Sanguinis/Koleris (19 September 2026)

### Community 3 - "User"
Cohesion: 0.10
Nodes (7): App\Models\User, User, Employee, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable, 61. 🎫 Implementasi Modul Baru: Helpdesk Ticketing Terintegrasi Otomatis Work Plan (Step Progress) & Master Karyawan Inhouse (24 September 2026), 66. 🎨 Kustomisasi Tema Dashboard Personal (Light/Dark Mode, 4 Palet Gelap, Custom Accent Color) & Tampilan Jabatan User (20 September 2026)

### Community 4 - "WorkPlanController"
Cohesion: 0.09
Nodes (9): WorkPlanController, Task, TaskActivity, TaskNotification, TaskSubtask, WorkPlanDaily, WorkPlanXlsxExportService, 32. 📋 Resolusi Work Plan & To Do List Pasca-Migrasi: Pencocokan Nama Case-Insensitive & Akses Data Historis Arsip (Andi Kurniawan Distrianto & 65 User Lainnya) (+1 more)

### Community 5 - "composer.json"
Cohesion: 0.04
Nodes (48): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+40 more)

### Community 6 - "Employee"
Cohesion: 0.07
Nodes (6): Collection, Employee, 27. 🏢 Pengetatan Aturan Tipe Karyawan Inhouse (Hanya 5 Entitas Resmi) & Proteksi Akses Login, 64. 👤 Resolusi Nama Lengkap AS / Rekruter pada Export Excel (.xlsx) Kandidat Portal dari Data Karyawan (20 September 2026), 65. 🏷️ Penambahan Jabatan AS dan Eliminasi Fallback Administrator ESA pada Export Excel (.xlsx) Kandidat Portal (20 September 2026), 69. 📑 Standardisasi & Harmonisasi Kolom Nama AS pada Export Excel Kandidat Portal & Filter Web (25 September 2026)

### Community 7 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.09
Nodes (28): App\Http\Controllers\AiSettingController, App\Http\Controllers\WorkPlanController, AiSettingUser, App\Models\Candidate, CandidateLog, App\Models\Employee, App\Models\InterviewAssessment, App\Models\JobSpec (+20 more)

### Community 8 - "MathQuestion"
Cohesion: 0.08
Nodes (8): AttachmentController, MathQuestionController, MathQuestion, App\Services\LegacyAttachmentService, LegacyAttachmentService, up(), Symfony\Component\HttpFoundation\BinaryFileResponse, 23. 🧮 Perbaikan Opsi Pilihan Ganda CBT Matematika & Normalisasi Master Soal

### Community 9 - "HelpdeskCannedResponse"
Cohesion: 0.27
Nodes (3): App\Http\Controllers\Helpdesk\HelpdeskCannedController, HelpdeskCannedController, HelpdeskCannedResponse

### Community 10 - "Illuminate\Http\Request"
Cohesion: 0.13
Nodes (4): InterviewController, RbacController, ActivityLogger, Illuminate\Http\Request

### Community 12 - "AiSetting"
Cohesion: 0.14
Nodes (3): AiSettingController, AiSetting, 45. 🤖 Integrasi AI OpenRouter, Hierarki Fallback Kuota (Gemini ➔ OpenRouter ➔ Sumopod), & Model Kustom Dinamis

### Community 14 - "HelpdeskTicket"
Cohesion: 0.09
Nodes (8): App\Http\Controllers\Helpdesk\HelpdeskTicketController, HelpdeskTicketController, HelpdeskTicket, HelpdeskTicketLog, HelpdeskWorkplanService, 62. 🛡️ Diferensiasi Hak Akses Dashboard & Modul Ticketing (User Biasa, User Divisi, Administrator) (24 September 2026), 65. 📎 Dukungan Multiple Lampiran (Gambar, PDF, Office Docs) & Modal Preview Interaktif, 67. 🔒 Fitur Penutupan & Buka Kembali Tiket oleh Pengaju (Requester Self-Close)

### Community 15 - "package.json"
Cohesion: 0.09
Nodes (19): devDependencies, axios, concurrently, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite, private (+11 more)

### Community 16 - "Illuminate\Support\Facades\DB"
Cohesion: 0.09
Nodes (20): CheckAdminCommand, CheckAstriCommand, FixDummyMathResultsCommand, FixDummyPersonalityResultsCommand, ImportJobSpecsCommand, ImportLegacyInterviewCommand, ImportOfficialPrinciplesCommand, ImportWpDumpCommand (+12 more)

### Community 17 - "AiAnalyzerService"
Cohesion: 0.15
Nodes (3): CronAiAnalyzerCommand, AiAnalyzerService, 57. ⚡ Perbaikan Masalah Input Token AI CV Analyzer Melonjak Ekstrem (2.114.589 Token) & Sanitasi Gambar Base64 Job Requirement (24 September 2026)

### Community 18 - "Principle"
Cohesion: 0.09
Nodes (17): App\Http\Controllers\AiRankingController, AiRankingController, Controller, App\Http\Controllers\FeatureController, FeatureController, App\Http\Controllers\Helpdesk\HelpdeskTemplateController, App\Http\Controllers\HomeController, HomeController (+9 more)

### Community 19 - "KandidatPortalController"
Cohesion: 0.08
Nodes (11): AiPdfService, CandidateImportController, InterviewInhouseController, KandidatPortalController, CandidateImportService, 35. 🧹 Pembersihan Akun Demo Jamil & Pengembalian Data Kandidat ke User Asli, 38. 🏢 Pembatasan Ketat Approval Inhouse Khusus 5 Entitas Resmi & Pemulihan Approval Prinsiple, 73. 🔄 Pembaruan Validasi Import Excel & Tarik NIK: Otomatis Mengarsipkan Data Sebelumnya untuk User/AS yang Sama dan Memblokir User/AS yang Berbeda (26 September 2026) (+3 more)

### Community 20 - "WorkPlanChatController"
Cohesion: 0.25
Nodes (6): App\Http\Controllers\WorkPlanChatController, WorkPlanChatController, WpChatGroup, WpChatGroupMember, WpChatMessage, Illuminate\Database\Eloquent\Relations\HasOne

### Community 21 - "WorkExperience"
Cohesion: 0.15
Nodes (12): CleanDuplicateCandidatesCommand, ImportInterviewSqlDumpCommand, App\Http\Controllers\InterviewController, App\Http\Controllers\InterviewInhouseController, InterviewAssessment, PrincipleApproval, TestResult, WorkExperience (+4 more)

### Community 22 - "CbtModuleTest"
Cohesion: 0.19
Nodes (5): Illuminate\Foundation\Testing\RefreshDatabase, Illuminate\Foundation\Testing\TestCase, CbtModuleTest, ExampleTest, TestCase

### Community 23 - ".auth"
Cohesion: 0.11
Nodes (7): AuthController, JobController, UserProfileController, IndonesiaRegionService, Controller, 51. 🔒 Proteksi Autentikasi Modul Input Job Requirement & Kontrol Kepemilikan Akun Rekruter (User AS) (23 September 2026), 69. 🛡️ Sistem Audit Trail & Log Aktivitas Komprehensif Seluruh Sistem (20 September 2026)

### Community 24 - "🏆 Milestone & Fitur yang Telah Diselesaikan"
Cohesion: 0.04
Nodes (48): 10. 🎯 Pemisahan 4 Kategori Kandidat & Penambahan Kolom Jenis Kelamin, 17. 💻 Modernisasi Sinkronisasi Odoo dengan Live Streaming Terminal Console, 1. 🏠 Halaman Beranda / Home (Full-Width Welcome Hero), 22. ✍️ Isolasi & Personalisasi Tanda Tangan Pewawancara (AS), 23. 👥 Pemulihan Master User Prinsiple, Isolasi Data Per Pengguna, & Proteksi Anti-Duplikat Email/No HP, 24. 🔄 Rekonfigurasi Sinkronisasi Odoo ERP & Arsitektur Dual Background Cron Job, 26. 📍 Searchable Dropdown Filter Area & Default Pengurutan Join Date Terbaru (19 September 2026), 28. 👔 Penambahan Field Pimpinan di Form Edit Karyawan & Fitur Bulk Edit Pimpinan Massal (19 September 2026) (+40 more)

### Community 26 - "📜 Riwayat Commit & Pembaruan Kode"
Cohesion: 0.05
Nodes (38): SimpleXMLElement, 24. 🔄 Penyempurnaan Sinkronisasi NIK Odoo Seluruh Entitas, 25. 🔍 Filter Searchable Dropdown Prinsiple/Jabatan, Eksklusi PT BUDGET, & Deduplikasi Master Prinsiple (19 September 2026), 26. 👥 Pemisahan 2 Tabel Kandidat Interview (Milik Sendiri & Rekan Se-Area) & Tab Terintegrasi Administrator, 27. 🚶 Modul & Halaman Kandidat Walk-in Interview (`/walkinterview`) Sesuai Sistem Lama, 28. 📝 Formulir Registrasi Walkin Interview Publik, Cascading Master Data (`tb_area` & `tb_kota`), dan Searchable TomSelect, 29. 🎯 Filter Ketat Personel Inhouse pada Dropdown Nama AS / Rekrutor Beserta Tampilan Badge Jabatan, 30. 📅 Penyempurnaan Input Tanggal Lahir Profesional dengan Indikator Usia Otomatis & Proteksi Input (+30 more)

### Community 27 - "UserPrinsiple"
Cohesion: 0.23
Nodes (4): UserPrinsipleController, UserPrinsiple, Illuminate\Http\RedirectResponse, Illuminate\View\View

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

### Community 40 - "InterviewPdfService"
Cohesion: 0.27
Nodes (5): InterviewPdfService, Mpdf, 14. 🏢 Pembersihan Master Data Prinsiple & Logo Entitas Dokumen Interview (18 September 2026), 19. ✍️ Digital Signature AS, Auto-Preload Tanda Tangan, & Dynamic PDF Export, 21. 🛑 Validasi Kriteria Kelulusan Interview, Disable Tab User Prinsiple & Tombol Download Dokumen

### Community 44 - "Illuminate\Support\Facades\Auth"
Cohesion: 0.33
Nodes (4): App\Http\Controllers\EmployeeController, App\Http\Controllers\Helpdesk\HelpdeskDivisionController, Illuminate\Support\Facades\Auth, Illuminate\Validation\Rule

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
Cohesion: 0.13
Nodes (5): ApprovalWorkflow, ApprovalWorkflowStep, InhouseApproval, ApprovalWorkflowService, Illuminate\Support\Collection

### Community 54 - "HelpdeskDivision"
Cohesion: 0.17
Nodes (7): App\Http\Controllers\Controller, App\Http\Controllers\Helpdesk\HelpdeskDashboardController, HelpdeskDashboardController, HelpdeskDivisionController, HelpdeskDivision, HelpdeskDivisionAgent, 63. ⚡ Searchable Dropdown Agen Divisi, Template Masalah & Format Laporan Tiket, serta Master Template Laporan

### Community 55 - "Permission"
Cohesion: 0.19
Nodes (6): Permission, Illuminate\Database\Eloquent\Relations\BelongsToMany, 35. 🐛 Perbaikan Filter Export Kandidat Job Portal (19 September 2026), 42. 🛡️ Pemisahan Menu Sync Odoo ke Group 'System Setting' & Implementasi Sistem Manajemen Hak Akses (RBAC) Terpadu (19 September 2026), 43. 🛡️ Role Dinamis (CRUD), Pengaturan Scope Prinsiple Dihandle & Area Cover, serta Penyaringan Data Berdasarkan Role (19 September 2026), 68. 👥 Penyempurnaan Hak Akses Administrator Talent Pool (admin_officer) & Cakupan Scope Nasional Tanpa Pembatasan Rekruter (25 September 2026)

### Community 59 - "ActivityLog"
Cohesion: 0.11
Nodes (3): activity_log(), ActivityLogController, ActivityLog

### Community 60 - "Database\Factories\UserFactory"
Cohesion: 0.47
Nodes (4): Database\Factories\UserFactory, UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 65 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.10
Nodes (4): ApprovalWorkflowStepUser, HelpdeskTicketReply, Illuminate\Database\Eloquent\Relations\BelongsTo, 64. 🖼️ Perbaikan Tampilan Lampiran Gambar Tiket & Balasan Helpdesk

### Community 70 - "ActivityLogXlsxExportService"
Cohesion: 0.19
Nodes (4): ActivityLogXlsxExportService, Illuminate\Foundation\Inspiring, Illuminate\Support\Facades\Artisan, Illuminate\Support\Facades\Schedule

### Community 73 - "Illuminate\Support\Facades\Schema"
Cohesion: 0.03
Nodes (5): up(), up(), Illuminate\Database\Migrations\Migration, Illuminate\Database\Schema\Blueprint, Illuminate\Support\Facades\Schema

### Community 158 - "ASystem - Support System ESA Groups"
Cohesion: 0.40
Nodes (4): ASystem - Support System ESA Groups, Fitur Utama, Panduan Instalasi & Menjalankan, Persyaratan Sistem

## Knowledge Gaps
- **170 isolated node(s):** `📌 Ringkasan Umum`, `1. 🏠 Halaman Beranda / Home (Full-Width Welcome Hero)`, `3. 👤 Prosedur Login & Autentikasi Karyawan`, `4. 👥 Penyempurnaan Master Karyawan`, `5. 🔄 Integrasi Sinkronisasi Odoo ERP (5 Entitas)` (+165 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 600 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **132 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Candidate` connect `Candidate` to `OdooSyncService`, `CbtController`, `Employee`, `Illuminate\Database\Eloquent\Model`, `MathQuestion`, `Illuminate\Http\Request`, `Illuminate\Support\Facades\DB`, `AiAnalyzerService`, `Principle`, `KandidatPortalController`, `WorkExperience`, `CbtModuleTest`, `UserPrinsiple`, `Closure`, `CandidateController`, `CandidateEvaluationDataService`, `DatabaseSeeder.php`, `JobStatistikController`, `InterviewPdfService`, `TbArea`, `AiPdfService.php`, `PublicJobController`, `ApprovalWorkflowStep`, `Permission`, `.getMissingProfileFields`, `Illuminate\Support\Facades\Schema`?**
  _High betweenness centrality (0.163) - this node is a cross-community bridge._
- **Why does `User` connect `User` to `OdooSyncService`, `WorkPlanController`, `Employee`, `Illuminate\Database\Eloquent\Model`, `Illuminate\Http\Request`, `JobSpec`, `ApprovalWorkflowController`, `HelpdeskTicket`, `Illuminate\Support\Facades\DB`, `Principle`, `KandidatPortalController`, `WorkExperience`, `.auth`, `UserPrinsiple`, `CandidateController`, `PasswordResetRequest`, `DatabaseSeeder.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Illuminate\Support\Facades\Auth`, `ApprovalWorkflowStep`, `InstallController`, `HelpdeskDivision`, `Permission`, `.coversAllAreas`, `.update`, `ActivityLog`, `Database\Factories\UserFactory`?**
  _High betweenness centrality (0.079) - this node is a cross-community bridge._
- **Why does `🏆 Milestone & Fitur yang Telah Diselesaikan` connect `🏆 Milestone & Fitur yang Telah Diselesaikan` to `Closure`, `CandidateController`, `CbtController`, `.done`, `CandidateEvaluationDataService`, `User`, `Employee`, `OdooSyncService`, `InterviewPdfService`, `KandidatPortalController`, `.auth`, `WorkExperience`, `Permission`, `.getMissingProfileFields`, `📜 Riwayat Commit & Pembaruan Kode`?**
  _High betweenness centrality (0.054) - this node is a cross-community bridge._
- **Are the 5 inferred relationships involving `Candidate` (e.g. with `.index()` and `.show()`) actually correct?**
  _`Candidate` has 5 INFERRED edges - model-reasoned connections that need verification._
- **Are the 8 inferred relationships involving `User` (e.g. with `.handle()` and `.login()`) actually correct?**
  _`User` has 8 INFERRED edges - model-reasoned connections that need verification._
- **Are the 13 inferred relationships involving `Employee` (e.g. with `.handle()` and `.login()`) actually correct?**
  _`Employee` has 13 INFERRED edges - model-reasoned connections that need verification._
- **What connects `📌 Ringkasan Umum`, `1. 🏠 Halaman Beranda / Home (Full-Width Welcome Hero)`, `3. 👤 Prosedur Login & Autentikasi Karyawan` to the rest of the system?**
  _170 weakly-connected nodes found - possible documentation gaps or missing edges._