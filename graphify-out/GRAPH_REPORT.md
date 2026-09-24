# Graph Report - newasystem  (2026-09-24)

## Corpus Check
- 265 files · ~363,777 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 19 file(s) not represented in the graph (top: (none) 9, .bat 5, .example 1)

## Summary
- 1225 nodes · 2810 edges · 163 communities (29 shown, 134 thin omitted)
- Extraction: 97% EXTRACTED · 3% INFERRED · 0% AMBIGUOUS · INFERRED: 82 edges (avg confidence: 0.85)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `162706af`
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
- TbArea
- InterviewController
- JobSpec
- Illuminate\Database\Eloquent\Relations\BelongsTo
- Illuminate\Database\Eloquent\Relations\HasMany
- Carbon\Carbon
- package.json
- HomeController.php
- AiAnalyzerService
- Principle
- Illuminate\Http\Request
- WpChatGroup
- AiSetting
- CbtModuleTest
- .auth
- ApprovalWorkflowController
- Role
- Illuminate\Database\Schema\Blueprint
- Illuminate\Database\Migrations\Migration
- JobStatistikXlsxExportService
- CandidateImportController.php
- Permission
- Closure
- InterviewInhouseController
- CandidateXlsxExportService
- AppServiceProvider.php
- Illuminate\Database\Seeder
- WorkExperience
- PrincipleApproval
- InterviewPdfService
- PersonalityQuestion
- FixDummyMathResultsCommand.php
- AiPdfService
- LegacyAttachmentService
- JobController.php
- bootstrap/app.php
- logging.php
- console.php
- ExampleTest
- CbtQuestionService
- artisan
- PublicJobController
- Illuminate\Support\Facades\Schema
- ActivityLogXlsxExportService
- Illuminate\Support\Facades\DB
- App\Models\Employee
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
- 2026_09_22_123000_sync_tb_area_and_normalize_regions.php

## God Nodes (most connected - your core abstractions)
1. `Candidate` - 162 edges
2. `User` - 100 edges
3. `Employee` - 91 edges
4. `ActivityLogger` - 85 edges
5. `Principle` - 60 edges
6. `InterviewController` - 41 edges
7. `JobSpec` - 40 edges
8. `OdooEntity` - 38 edges
9. `OdooSyncService` - 38 edges
10. `WorkPlanController` - 35 edges

## Surprising Connections (you probably didn't know these)
- `up()` --calls--> `MathQuestion`  [EXTRACTED]
  database/migrations/2026_09_21_110000_fix_tb_math_choices_json.php → app/Models/MathQuestion.php
- `up()` --calls--> `TbArea`  [EXTRACTED]
  database/migrations/2026_09_22_123000_sync_tb_area_and_normalize_regions.php → app/Models/TbArea.php
- `OdooSettingController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/OdooSettingController.php → app/Http/Controllers/Controller.php
- `InterviewController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/InterviewController.php → app/Http/Controllers/Controller.php
- `PublicJobController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/PublicJobController.php → app/Http/Controllers/Controller.php

## Import Cycles
- None detected.

## Communities (163 total, 134 thin omitted)

### Community 0 - "OdooEntity"
Cohesion: 0.06
Nodes (19): FixDummyPersonalityResultsCommand, ImportJobSpecsCommand, ImportLegacyInterviewCommand, ImportOfficialPrinciplesCommand, ImportWpDumpCommand, OdooSyncActiveCommand, OdooSyncCommand, OdooSyncUpdatesResignsCommand (+11 more)

### Community 3 - "User"
Cohesion: 0.08
Nodes (8): User, App\Services\ActivityLogger, DatabaseSeeder, Illuminate\Support\Facades\Auth, Illuminate\Support\Facades\File, Illuminate\Support\Facades\Hash, Illuminate\Validation\Rule, Throwable

### Community 4 - "WorkPlanController"
Cohesion: 0.10
Nodes (8): WorkPlanController, Task, TaskActivity, TaskComment, TaskNotification, TaskSubtask, WorkPlanDaily, WorkPlanXlsxExportService

### Community 5 - "composer.json"
Cohesion: 0.04
Nodes (48): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+40 more)

### Community 7 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.18
Nodes (8): App\Models\ApprovalWorkflow, App\Models\ApprovalWorkflowStepUser, CandidateLog, App\Models\Principle, App\Models\Task, TaskCategory, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Database\Eloquent\Model

### Community 8 - "PasswordResetRequest"
Cohesion: 0.08
Nodes (5): AuthChatController, MathQuestionController, MathQuestion, PasswordResetChatMessage, PasswordResetRequest

### Community 9 - "TbArea"
Cohesion: 0.22
Nodes (3): JobStatistikController, TbArea, Symfony\Component\HttpFoundation\BinaryFileResponse

### Community 12 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.13
Nodes (3): ApprovalWorkflowStepUser, UserPrinsiple, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 13 - "Illuminate\Database\Eloquent\Relations\HasMany"
Cohesion: 0.08
Nodes (7): ApprovalWorkflow, App\Models\ApprovalWorkflowStep, ApprovalWorkflowStep, InhouseApproval, ApprovalWorkflowService, Illuminate\Database\Eloquent\Relations\HasMany, Illuminate\Support\Collection

### Community 14 - "Carbon\Carbon"
Cohesion: 0.36
Nodes (4): Carbon\Carbon, Exception, SimpleXMLElement, ZipArchive

### Community 15 - "package.json"
Cohesion: 0.09
Nodes (19): devDependencies, axios, concurrently, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite, private (+11 more)

### Community 17 - "AiAnalyzerService"
Cohesion: 0.14
Nodes (3): CronAiAnalyzerCommand, AiAnalyzerService, Illuminate\Support\Facades\Cache

### Community 18 - "Principle"
Cohesion: 0.15
Nodes (3): CandidateController, PrincipleController, Principle

### Community 19 - "Illuminate\Http\Request"
Cohesion: 0.11
Nodes (4): KandidatPortalController, RbacController, ActivityLogger, Illuminate\Http\Request

### Community 20 - "WpChatGroup"
Cohesion: 0.27
Nodes (5): WorkPlanChatController, WpChatGroup, WpChatGroupMember, WpChatMessage, Illuminate\Database\Eloquent\Relations\HasOne

### Community 21 - "AiSetting"
Cohesion: 0.14
Nodes (4): AiSettingController, AiSetting, AiSettingUser, WaAreaSetting

### Community 22 - "CbtModuleTest"
Cohesion: 0.19
Nodes (5): Illuminate\Foundation\Testing\RefreshDatabase, Illuminate\Foundation\Testing\TestCase, CbtModuleTest, ExampleTest, TestCase

### Community 23 - ".auth"
Cohesion: 0.05
Nodes (13): activity_log(), ActivityLogController, AiRankingController, AuthController, Controller, FeatureController, InstallController, JobController (+5 more)

### Community 30 - "CandidateImportController.php"
Cohesion: 0.17
Nodes (3): CandidateImportController, CandidateImportService, Symfony\Component\HttpFoundation\StreamedResponse

### Community 32 - "Closure"
Cohesion: 0.33
Nodes (5): EnsureCandidateAuthenticated, EnsureUserIsAdmin, RedirectIfInstalled, Closure, Symfony\Component\HttpFoundation\Response

### Community 35 - "AppServiceProvider.php"
Cohesion: 0.22
Nodes (6): AppServiceProvider, Illuminate\Auth\Events\Failed, Illuminate\Auth\Events\Login, Illuminate\Auth\Events\Logout, Illuminate\Support\Facades\Event, Illuminate\Support\ServiceProvider

### Community 36 - "Illuminate\Database\Seeder"
Cohesion: 0.38
Nodes (3): CbtCandidateSeeder, OdooEntitySeeder, Illuminate\Database\Seeder

### Community 37 - "WorkExperience"
Cohesion: 0.14
Nodes (6): CleanDuplicateCandidatesCommand, ImportInterviewSqlDumpCommand, InterviewAssessment, TestResult, WorkExperience, Illuminate\Support\Facades\Storage

### Community 39 - "PrincipleApproval"
Cohesion: 0.16
Nodes (4): PrincipleApprovalController, PrincipleApproval, Illuminate\Support\Str, Pdo\Mysql

### Community 46 - "bootstrap/app.php"
Cohesion: 0.40
Nodes (3): Illuminate\Foundation\Application, Illuminate\Foundation\Configuration\Exceptions, Illuminate\Foundation\Configuration\Middleware

### Community 47 - "logging.php"
Cohesion: 0.40
Nodes (4): Monolog\Handler\NullHandler, Monolog\Handler\StreamHandler, Monolog\Handler\SyslogUdpHandler, Monolog\Processor\PsrLogMessageProcessor

### Community 48 - "console.php"
Cohesion: 0.50
Nodes (3): Illuminate\Foundation\Inspiring, Illuminate\Support\Facades\Artisan, Illuminate\Support\Facades\Schedule

### Community 63 - "Illuminate\Support\Facades\DB"
Cohesion: 0.09
Nodes (3): App\Models\TbArea, up(), Illuminate\Support\Facades\DB

### Community 64 - "App\Models\Employee"
Cohesion: 0.20
Nodes (8): CheckAstriCommand, App\Models\Employee, App\Models\User, UserFactory, Illuminate\Database\Eloquent\Factories\Factory, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable, static

## Knowledge Gaps
- **63 isolated node(s):** `axios`, `concurrently`, `laravel-vite-plugin`, `tailwindcss`, `@tailwindcss/vite` (+58 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 475 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **134 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Candidate` connect `Candidate` to `OdooEntity`, `CbtController`, `User`, `Employee`, `Illuminate\Database\Eloquent\Model`, `PasswordResetRequest`, `TbArea`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Carbon\Carbon`, `HomeController.php`, `AiAnalyzerService`, `Principle`, `Illuminate\Http\Request`, `AiSetting`, `CbtModuleTest`, `.auth`, `CandidateImportController.php`, `Closure`, `InterviewInhouseController`, `Illuminate\Database\Seeder`, `WorkExperience`, `InterviewPdfService`, `FixDummyMathResultsCommand.php`, `AiPdfService`, `PublicJobController`, `.getMissingProfileFields`, `.testResults`?**
  _High betweenness centrality (0.192) - this node is a cross-community bridge._
- **Why does `User` connect `User` to `OdooEntity`, `WorkPlanController`, `Employee`, `Illuminate\Database\Eloquent\Model`, `PasswordResetRequest`, `InterviewController`, `JobSpec`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Carbon\Carbon`, `Principle`, `Illuminate\Http\Request`, `.auth`, `ApprovalWorkflowController`, `CandidateImportController.php`, `InterviewInhouseController`, `WorkExperience`, `.coversAllAreas`, `JobController.php`, `App\Models\Employee`?**
  _High betweenness centrality (0.098) - this node is a cross-community bridge._
- **Why does `ActivityLogger` connect `Illuminate\Http\Request` to `OdooEntity`, `CbtController`, `User`, `WorkPlanController`, `WorkExperience`, `Employee`, `AppServiceProvider.php`, `TbArea`, `InterviewController`, `JobController.php`, `Principle`, `.auth`, `ApprovalWorkflowController`?**
  _High betweenness centrality (0.050) - this node is a cross-community bridge._
- **Are the 7 inferred relationships involving `User` (e.g. with `.handle()` and `.compileRulesFromRequest()`) actually correct?**
  _`User` has 7 INFERRED edges - model-reasoned connections that need verification._
- **Are the 6 inferred relationships involving `Employee` (e.g. with `.handle()` and `.ensureInhouseUsersExist()`) actually correct?**
  _`Employee` has 6 INFERRED edges - model-reasoned connections that need verification._
- **Are the 3 inferred relationships involving `ActivityLogger` (e.g. with `.destroyStep()` and `.storeStep()`) actually correct?**
  _`ActivityLogger` has 3 INFERRED edges - model-reasoned connections that need verification._
- **What connects `axios`, `concurrently`, `laravel-vite-plugin` to the rest of the system?**
  _63 weakly-connected nodes found - possible documentation gaps or missing edges._