# Graph Report - newasystem  (2026-09-28)

## Corpus Check
- 298 files · ~402,981 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 19 file(s) not represented in the graph (top: (none) 9, .bat 5, .example 1)

## Summary
- 1537 nodes · 3514 edges · 169 communities (46 shown, 123 thin omitted)
- Extraction: 91% EXTRACTED · 9% INFERRED · 0% AMBIGUOUS · INFERRED: 314 edges (avg confidence: 0.9)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `55602de1`
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
- Carbon\Carbon
- InterviewController
- JobSpec
- ImportLegacyInterviewCommand.php
- ApprovalWorkflowController.php
- HelpdeskTicket
- package.json
- Illuminate\Support\Facades\DB
- AiAnalyzerService
- Principle
- KandidatPortalController
- WorkPlanChatController
- AiSetting
- CbtModuleTest
- .auth
- 🏆 Milestone & Fitur yang Telah Diselesaikan
- Illuminate\Http\Request
- 📜 Riwayat Commit & Pembaruan Kode
- UserPrinsiple
- JobStatistikXlsxExportService
- AppServiceProvider.php
- Panduan Implementasi WhatsApp API Official Coexistence (Coex) di ASystem
- Closure
- InterviewInhouseController
- PasswordResetRequest
- CandidateEvaluationDataService
- DatabaseSeeder.php
- 🚀 Panduan & Prosedur Deployment Server Production ASystem
- HelpdeskCannedResponse
- Illuminate\Database\Eloquent\Relations\HasMany
- InterviewPdfService
- PersonalityQuestion
- .log
- AiPdfService
- PublicJobController
- .index
- bootstrap/app.php
- logging.php
- JobStatistikController
- ExampleTest
- 🚀 Panduan Instalasi & Deployment ASystem Portal
- artisan
- ApprovalWorkflowService
- TbArea
- HelpdeskDivision
- CandidateXlsxExportService
- Role
- .getMissingProfileFields
- App\Http\Controllers\HomeController
- ActivityLog
- 27. 🏢 Pengetatan Aturan Tipe Karyawan Inhouse (Hanya 5 Entitas Resmi) & Proteksi Akses Login
- Illuminate\Database\Eloquent\Relations\BelongsTo
- 2026_09_28_110000_fix_job_portal_terima_candidates_status.php
- console.php
- Illuminate\Support\Facades\Schema
- CandidateImportService.php
- 2026_09_22_123000_sync_tb_area_and_normalize_regions.php
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
6. `Principle` - 60 edges
7. `📜 Riwayat Commit & Pembaruan Kode` - 57 edges
8. `InterviewController` - 43 edges
9. `JobSpec` - 40 edges
10. `OdooSyncService` - 38 edges

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

## Communities (169 total, 123 thin omitted)

### Community 0 - "OdooSyncService"
Cohesion: 0.06
Nodes (25): ImportJobSpecsCommand, ImportOfficialPrinciplesCommand, ImportWpDumpCommand, OdooSyncActiveCommand, OdooSyncCommand, OdooSyncUpdatesResignsCommand, SyncOdooRecruitmentStagesCommand, OdooSettingController (+17 more)

### Community 2 - "CbtController"
Cohesion: 0.15
Nodes (4): CbtController, CbtQuestionService, 38. 🧮 Penyelarasan Soal CBT Matematika dengan Master Soal Sistem Lama & Modul Master Soal Matematika Admin (19 September 2026), 40. 🧠 Penyelarasan Tes Kepribadian CBT dengan 40 Butir Soal Florence Littauer (tb_kepribadian), Modul Master Soal Kepribadian Admin, & Penyesuaian Hasil Dummy ke Sanguinis/Koleris (19 September 2026)

### Community 3 - "User"
Cohesion: 0.06
Nodes (17): SyncInhouseUsersCommand, InstallController, App\Models\User, User, Database\Factories\UserFactory, UserFactory, Employee, Illuminate\Database\Eloquent\Factories\Factory (+9 more)

### Community 4 - "WorkPlanController"
Cohesion: 0.08
Nodes (11): CheckAstriCommand, SyncAstriWpCommand, WorkPlanController, Task, TaskActivity, TaskNotification, TaskSubtask, WorkPlanDaily (+3 more)

### Community 5 - "composer.json"
Cohesion: 0.04
Nodes (48): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+40 more)

### Community 6 - "Employee"
Cohesion: 0.09
Nodes (5): EmployeeController, Employee, 64. 👤 Resolusi Nama Lengkap AS / Rekruter pada Export Excel (.xlsx) Kandidat Portal dari Data Karyawan (20 September 2026), 65. 🏷️ Penambahan Jabatan AS dan Eliminasi Fallback Administrator ESA pada Export Excel (.xlsx) Kandidat Portal (20 September 2026), 69. 📑 Standardisasi & Harmonisasi Kolom Nama AS pada Export Excel Kandidat Portal & Filter Web (25 September 2026)

### Community 7 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.14
Nodes (14): AiSettingUser, CandidateLog, App\Models\JobSpec, App\Models\TaskActivity, App\Models\TaskCategory, TaskCategory, App\Models\TaskComment, TaskComment (+6 more)

### Community 8 - "MathQuestion"
Cohesion: 0.08
Nodes (8): AttachmentController, MathQuestionController, MathQuestion, App\Services\LegacyAttachmentService, LegacyAttachmentService, up(), Symfony\Component\HttpFoundation\BinaryFileResponse, 23. 🧮 Perbaikan Opsi Pilihan Ganda CBT Matematika & Normalisasi Master Soal

### Community 9 - "Carbon\Carbon"
Cohesion: 0.14
Nodes (27): App\Http\Controllers\AiRankingController, App\Http\Controllers\AiSettingController, App\Http\Controllers\AuthController, App\Http\Controllers\Controller, App\Http\Controllers\EmployeeController, App\Http\Controllers\FeatureController, App\Http\Controllers\Helpdesk\HelpdeskCannedController, App\Http\Controllers\Helpdesk\HelpdeskDashboardController (+19 more)

### Community 10 - "InterviewController"
Cohesion: 0.09
Nodes (4): InterviewController, Collection, 16. 📂 Pemulihan Visibilitas Data Interview Selesai & Arsip Interview, 55. 🔒 Perbaikan Error Undefined $isAdmin, Isolasi Data Interview Selesai & Arsip per Rekrutor / AS, serta Akses Khusus Kandidat Inhouse 5 Entitas (Approval HRD & Head) (20 September 2026)

### Community 14 - "HelpdeskTicket"
Cohesion: 0.08
Nodes (8): HelpdeskTicketController, HelpdeskDivisionAgent, HelpdeskTicket, HelpdeskTicketLog, HelpdeskWorkplanService, 62. 🛡️ Diferensiasi Hak Akses Dashboard & Modul Ticketing (User Biasa, User Divisi, Administrator) (24 September 2026), 65. 📎 Dukungan Multiple Lampiran (Gambar, PDF, Office Docs) & Modal Preview Interaktif, 67. 🔒 Fitur Penutupan & Buka Kembali Tiket oleh Pengaju (Requester Self-Close)

### Community 15 - "package.json"
Cohesion: 0.09
Nodes (19): devDependencies, axios, concurrently, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite, private (+11 more)

### Community 16 - "Illuminate\Support\Facades\DB"
Cohesion: 0.11
Nodes (19): CleanDuplicateCandidatesCommand, FixDummyPersonalityResultsCommand, ImportInterviewSqlDumpCommand, App\Http\Controllers\CandidateImportController, App\Http\Controllers\CbtController, App\Http\Controllers\InterviewController, App\Http\Controllers\PrincipleApprovalController, PrincipleApprovalController (+11 more)

### Community 17 - "AiAnalyzerService"
Cohesion: 0.15
Nodes (3): CronAiAnalyzerCommand, AiAnalyzerService, 57. ⚡ Perbaikan Masalah Input Token AI CV Analyzer Melonjak Ekstrem (2.114.589 Token) & Sanitasi Gambar Base64 Job Requirement (24 September 2026)

### Community 18 - "Principle"
Cohesion: 0.12
Nodes (7): CandidateController, App\Http\Controllers\JobStatistikController, App\Http\Controllers\PrincipleController, PrincipleController, Principle, 10. ⚡ Optimasi Kecepatan Loading & Efek Animasi UI/UX Modern di Seluruh Halaman, 36. 📋 Penyelarasan Kolom Export Sesuai Sistem Lama, Link Server Produksi, & Label CV Analisa AI (19 September 2026)

### Community 19 - "KandidatPortalController"
Cohesion: 0.20
Nodes (4): AiPdfService, KandidatPortalController, 35. 🧹 Pembersihan Akun Demo Jamil & Pengembalian Data Kandidat ke User Asli, 75. 🎯 Perbaikan Hak Akses & Pembatasan Tampilan Kandidat Portal Sesuai AS User (28 September 2026)

### Community 20 - "WorkPlanChatController"
Cohesion: 0.23
Nodes (6): App\Http\Controllers\WorkPlanChatController, WorkPlanChatController, WpChatGroup, WpChatGroupMember, WpChatMessage, Illuminate\Database\Eloquent\Relations\HasOne

### Community 21 - "AiSetting"
Cohesion: 0.14
Nodes (3): AiSettingController, AiSetting, 45. 🤖 Integrasi AI OpenRouter, Hierarki Fallback Kuota (Gemini ➔ OpenRouter ➔ Sumopod), & Model Kustom Dinamis

### Community 22 - "CbtModuleTest"
Cohesion: 0.19
Nodes (5): Illuminate\Foundation\Testing\RefreshDatabase, Illuminate\Foundation\Testing\TestCase, CbtModuleTest, ExampleTest, TestCase

### Community 23 - ".auth"
Cohesion: 0.19
Nodes (6): ActivityLogController, AiRankingController, AuthController, Controller, FeatureController, UserProfileController

### Community 24 - "🏆 Milestone & Fitur yang Telah Diselesaikan"
Cohesion: 0.04
Nodes (48): 10. 🎯 Pemisahan 4 Kategori Kandidat & Penambahan Kolom Jenis Kelamin, 17. 💻 Modernisasi Sinkronisasi Odoo dengan Live Streaming Terminal Console, 1. 🏠 Halaman Beranda / Home (Full-Width Welcome Hero), 22. ✍️ Isolasi & Personalisasi Tanda Tangan Pewawancara (AS), 23. 👥 Pemulihan Master User Prinsiple, Isolasi Data Per Pengguna, & Proteksi Anti-Duplikat Email/No HP, 24. 🔄 Rekonfigurasi Sinkronisasi Odoo ERP & Arsitektur Dual Background Cron Job, 26. 📍 Searchable Dropdown Filter Area & Default Pengurutan Join Date Terbaru (19 September 2026), 28. 👔 Penambahan Field Pimpinan di Form Edit Karyawan & Fitur Bulk Edit Pimpinan Massal (19 September 2026) (+40 more)

### Community 25 - "Illuminate\Http\Request"
Cohesion: 0.15
Nodes (5): RbacController, Permission, ActivityLogger, ActivityLogXlsxExportService, Illuminate\Http\Request

### Community 26 - "📜 Riwayat Commit & Pembaruan Kode"
Cohesion: 0.05
Nodes (34): CandidateImportController, CandidateImportService, 24. 🔄 Penyempurnaan Sinkronisasi NIK Odoo Seluruh Entitas, 26. 👥 Pemisahan 2 Tabel Kandidat Interview (Milik Sendiri & Rekan Se-Area) & Tab Terintegrasi Administrator, 27. 🚶 Modul & Halaman Kandidat Walk-in Interview (`/walkinterview`) Sesuai Sistem Lama, 28. 📝 Formulir Registrasi Walkin Interview Publik, Cascading Master Data (`tb_area` & `tb_kota`), dan Searchable TomSelect, 29. 🎯 Filter Ketat Personel Inhouse pada Dropdown Nama AS / Rekrutor Beserta Tampilan Badge Jabatan, 30. 📅 Penyempurnaan Input Tanggal Lahir Profesional dengan Indikator Usia Otomatis & Proteksi Input (+26 more)

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

### Community 33 - "InterviewInhouseController"
Cohesion: 0.23
Nodes (3): InterviewInhouseController, 38. 🏢 Pembatasan Ketat Approval Inhouse Khusus 5 Entitas Resmi & Pemulihan Approval Prinsiple, 9. 🗄️ Migrasi Penuh Database Legacy Interview (`asystemc_interview.sql`) & Evaluasi Dinamis

### Community 34 - "PasswordResetRequest"
Cohesion: 0.19
Nodes (4): AuthChatController, PasswordResetChatMessage, PasswordResetRequest, 40. 💬 Fitur Lupa Kata Sandi via Live Chat Administrator & Auto-Sync Odoo Karyawan

### Community 35 - "CandidateEvaluationDataService"
Cohesion: 0.25
Nodes (5): FixDummyMathResultsCommand, CandidateEvaluationDataService, 20. 🎯 Sinkronisasi Evaluasi Nilai CBT & Status Hasil Tes Kandidat di Dashboard Rekruter, 39. 🧮 Penyesuaian Hasil Tes Matematika Dummy Menjadi Nilai B (Grade B - 70%) & Proteksi Data Lama (19 September 2026), 50. 📋 Penyelarasan Menyeluruh Data Riil Evaluasi Kandidat Inhouse (Interview, Refcek, Komputer, Kepribadian, & Matematika)

### Community 36 - "DatabaseSeeder.php"
Cohesion: 0.27
Nodes (4): CbtCandidateSeeder, DatabaseSeeder, OdooEntitySeeder, Illuminate\Database\Seeder

### Community 37 - "🚀 Panduan & Prosedur Deployment Server Production ASystem"
Cohesion: 0.14
Nodes (13): 🌐 1. Informasi Infrastruktur & Server, ⚡ 2. Cara Cepat Deploy (Metode Utama: 1-Click Remote Runner), 🖥️ 3. Metode Alternatif (Deploy Langsung via SSH Terminal), 📋 4. Standar Operasional Prosedur (SOP) Sebelum & Sesudah Deploy, ⏱️ 5. Layanan Latar Belakang (Cron & Worker) di Production, Eksekusi Deploy:, Mekanisme Kerja Skrip:, Opsi A: Menggunakan Shell Script Resmi (+5 more)

### Community 39 - "Illuminate\Database\Eloquent\Relations\HasMany"
Cohesion: 0.08
Nodes (5): ApprovalWorkflow, ApprovalWorkflowStep, App\Models\OdooEntity, App\Models\Principle, Illuminate\Database\Eloquent\Relations\HasMany

### Community 40 - "InterviewPdfService"
Cohesion: 0.27
Nodes (5): InterviewPdfService, Mpdf, 14. 🏢 Pembersihan Master Data Prinsiple & Logo Entitas Dokumen Interview (18 September 2026), 19. ✍️ Digital Signature AS, Auto-Preload Tanda Tangan, & Dynamic PDF Export, 21. 🛑 Validasi Kriteria Kelulusan Interview, Disable Tab User Prinsiple & Tombol Download Dokumen

### Community 44 - "PublicJobController"
Cohesion: 0.29
Nodes (3): PublicJobController, Controller, 56. 📝 Form Job Apply: Seluruh Field Menjadi Mandatory (Wajib Diisi) & Notifikasi Interaktif Bagian yang Kurang (23 September 2026)

### Community 46 - "bootstrap/app.php"
Cohesion: 0.40
Nodes (3): Illuminate\Foundation\Application, Illuminate\Foundation\Configuration\Exceptions, Illuminate\Foundation\Configuration\Middleware

### Community 47 - "logging.php"
Cohesion: 0.40
Nodes (4): Monolog\Handler\NullHandler, Monolog\Handler\StreamHandler, Monolog\Handler\SyslogUdpHandler, Monolog\Processor\PsrLogMessageProcessor

### Community 50 - "🚀 Panduan Instalasi & Deployment ASystem Portal"
Cohesion: 0.20
Nodes (9): 🔐 Akun Login Bawaan (Default Administrator), 🛠️ Catatan Teknis File Database, METODE 1: cPanel / Shared Hosting (Paling Cepat & Mudah), METODE 2: Linux VPS / Cloud Server (Ubuntu / Debian / AlmaLinux), METODE 3: Windows Server (IIS / XAMPP / Laragon), METODE 4: Web Wizard Installer (Antarmuka Grafis), 🚀 Panduan Instalasi & Deployment ASystem Portal, ⏰ Pengaturan Otomatisasi (Cron Job / Background Worker) (+1 more)

### Community 52 - "ApprovalWorkflowService"
Cohesion: 0.16
Nodes (3): InhouseApproval, ApprovalWorkflowService, Illuminate\Support\Collection

### Community 54 - "HelpdeskDivision"
Cohesion: 0.10
Nodes (5): HelpdeskDivisionController, HelpdeskDivision, Illuminate\Database\Eloquent\Relations\BelongsToMany, 61. 🎫 Implementasi Modul Baru: Helpdesk Ticketing Terintegrasi Otomatis Work Plan (Step Progress) & Master Karyawan Inhouse (24 September 2026), 63. ⚡ Searchable Dropdown Agen Divisi, Template Masalah & Format Laporan Tiket, serta Master Template Laporan

### Community 59 - "ActivityLog"
Cohesion: 0.12
Nodes (3): activity_log(), ActivityLog, 69. 🛡️ Sistem Audit Trail & Log Aktivitas Komprehensif Seluruh Sistem (20 September 2026)

### Community 65 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.11
Nodes (6): ApprovalWorkflowStepUser, HelpdeskTicketReply, App\Models\InterviewAssessment, App\Models\WorkExperience, Illuminate\Database\Eloquent\Relations\BelongsTo, 64. 🖼️ Perbaikan Tampilan Lampiran Gambar Tiket & Balasan Helpdesk

### Community 70 - "console.php"
Cohesion: 0.50
Nodes (3): Illuminate\Foundation\Inspiring, Illuminate\Support\Facades\Artisan, Illuminate\Support\Facades\Schedule

### Community 73 - "Illuminate\Support\Facades\Schema"
Cohesion: 0.03
Nodes (3): Illuminate\Database\Migrations\Migration, Illuminate\Database\Schema\Blueprint, Illuminate\Support\Facades\Schema

### Community 77 - "CandidateImportService.php"
Cohesion: 0.48
Nodes (3): App\Services\WorkPlanXlsxExportService, Exception, ZipArchive

### Community 158 - "ASystem - Support System ESA Groups"
Cohesion: 0.40
Nodes (4): ASystem - Support System ESA Groups, Fitur Utama, Panduan Instalasi & Menjalankan, Persyaratan Sistem

## Knowledge Gaps
- **167 isolated node(s):** `📌 Ringkasan Umum`, `1. 🏠 Halaman Beranda / Home (Full-Width Welcome Hero)`, `3. 👤 Prosedur Login & Autentikasi Karyawan`, `4. 👥 Penyempurnaan Master Karyawan`, `5. 🔄 Integrasi Sinkronisasi Odoo ERP (5 Entitas)` (+162 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 598 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **123 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Candidate` connect `Candidate` to `OdooSyncService`, `CbtController`, `User`, `Employee`, `Illuminate\Database\Eloquent\Model`, `MathQuestion`, `Carbon\Carbon`, `ImportLegacyInterviewCommand.php`, `Illuminate\Support\Facades\DB`, `AiAnalyzerService`, `Principle`, `AiSetting`, `CbtModuleTest`, `.auth`, `Illuminate\Http\Request`, `UserPrinsiple`, `Closure`, `InterviewInhouseController`, `CandidateEvaluationDataService`, `DatabaseSeeder.php`, `InterviewPdfService`, `AiPdfService`, `PublicJobController`, `JobStatistikController`, `ApprovalWorkflowService`, `TbArea`, `.getMissingProfileFields`, `App\Http\Controllers\HomeController`, `2026_09_28_110000_fix_job_portal_terima_candidates_status.php`, `Illuminate\Support\Facades\Schema`, `CandidateImportService.php`?**
  _High betweenness centrality (0.118) - this node is a cross-community bridge._
- **Why does `User` connect `User` to `WorkPlanController`, `Employee`, `Illuminate\Database\Eloquent\Model`, `Carbon\Carbon`, `InterviewController`, `JobSpec`, `ApprovalWorkflowController.php`, `HelpdeskTicket`, `Illuminate\Support\Facades\DB`, `Principle`, `.auth`, `Illuminate\Http\Request`, `📜 Riwayat Commit & Pembaruan Kode`, `UserPrinsiple`, `InterviewInhouseController`, `PasswordResetRequest`, `DatabaseSeeder.php`, `Illuminate\Database\Eloquent\Relations\HasMany`, `.log`, `.index`, `ApprovalWorkflowService`, `HelpdeskDivision`, `Role`, `Illuminate\Support\Facades\Schema`, `CandidateImportService.php`?**
  _High betweenness centrality (0.074) - this node is a cross-community bridge._
- **Why does `🏆 Milestone & Fitur yang Telah Diselesaikan` connect `🏆 Milestone & Fitur yang Telah Diselesaikan` to `OdooSyncService`, `Closure`, `CbtController`, `CandidateEvaluationDataService`, `User`, `InterviewInhouseController`, `Employee`, `InterviewPdfService`, `InterviewController`, `Illuminate\Support\Facades\DB`, `Principle`, `.getMissingProfileFields`, `📜 Riwayat Commit & Pembaruan Kode`, `ActivityLog`, `27. 🏢 Pengetatan Aturan Tipe Karyawan Inhouse (Hanya 5 Entitas Resmi) & Proteksi Akses Login`?**
  _High betweenness centrality (0.056) - this node is a cross-community bridge._
- **Are the 5 inferred relationships involving `Candidate` (e.g. with `.index()` and `.show()`) actually correct?**
  _`Candidate` has 5 INFERRED edges - model-reasoned connections that need verification._
- **Are the 6 inferred relationships involving `User` (e.g. with `.getMatchingApproversForCandidate()` and `.getAvatarUrl()`) actually correct?**
  _`User` has 6 INFERRED edges - model-reasoned connections that need verification._
- **Are the 11 inferred relationships involving `Employee` (e.g. with `.getAiQueueLogPayload()` and `.resolveRecruiterFilterIdentifiers()`) actually correct?**
  _`Employee` has 11 INFERRED edges - model-reasoned connections that need verification._
- **What connects `📌 Ringkasan Umum`, `1. 🏠 Halaman Beranda / Home (Full-Width Welcome Hero)`, `3. 👤 Prosedur Login & Autentikasi Karyawan` to the rest of the system?**
  _167 weakly-connected nodes found - possible documentation gaps or missing edges._