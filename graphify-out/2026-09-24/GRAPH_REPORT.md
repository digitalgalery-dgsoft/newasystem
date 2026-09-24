# Graph Report - newasystem  (2026-09-24)

## Corpus Check
- 264 files · ~362,966 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 19 file(s) not represented in the graph (top: (none) 9, .bat 5, .example 1)

## Summary
- 1223 nodes · 2805 edges · 162 communities (33 shown, 129 thin omitted)
- Extraction: 98% EXTRACTED · 2% INFERRED · 0% AMBIGUOUS · INFERRED: 66 edges (avg confidence: 0.85)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `08e39033`
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
- TbArea
- Illuminate\Http\Request
- JobSpec
- Illuminate\Database\Eloquent\Relations\BelongsTo
- ApprovalWorkflowStep
- Illuminate\Support\Facades\DB
- package.json
- Controller
- AiAnalyzerService
- ActivityLogger
- KandidatPortalController
- Illuminate\Database\Eloquent\Relations\HasMany
- AiSetting
- CbtModuleTest
- ActivityLog
- OdooSyncLog
- Role
- Illuminate\Support\Facades\Schema
- Illuminate\Database\Schema\Blueprint
- Illuminate\Database\Migrations\Migration
- UserPrinsiple
- CandidateImportController.php
- OdooSyncService
- Closure
- InterviewInhouseController
- JobController
- AppServiceProvider.php
- DatabaseSeeder.php
- Illuminate\Console\Command
- PrincipleApproval
- InterviewPdfService
- PersonalityQuestion
- FixDummyMathResultsCommand.php
- AiPdfService
- LegacyAttachmentService
- JobController.php
- bootstrap/app.php
- logging.php
- InstallController.php
- ExampleTest
- ImportLegacyInterviewCommand.php
- artisan
- Carbon\Carbon
- .auth
- UserFactory.php
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
- 2026_09_21_110000_fix_tb_math_choices_json.php

## God Nodes (most connected - your core abstractions)
1. `Candidate` - 162 edges
2. `User` - 100 edges
3. `Employee` - 91 edges
4. `ActivityLogger` - 86 edges
5. `Principle` - 61 edges
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

## Communities (162 total, 129 thin omitted)

### Community 0 - "OdooEntity"
Cohesion: 0.18
Nodes (3): OdooSettingController, OdooEntity, Illuminate\Http\JsonResponse

### Community 2 - "WorkExperience"
Cohesion: 0.07
Nodes (9): CleanDuplicateCandidatesCommand, FixDummyPersonalityResultsCommand, ImportInterviewSqlDumpCommand, CandidateImportController, CbtController, TestResult, WorkExperience, CandidateImportService (+1 more)

### Community 3 - "User"
Cohesion: 0.13
Nodes (4): App\Models\User, User, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable

### Community 4 - "WorkPlanController"
Cohesion: 0.10
Nodes (8): WorkPlanController, Task, TaskActivity, TaskComment, TaskNotification, TaskSubtask, WorkPlanDaily, WorkPlanXlsxExportService

### Community 5 - "composer.json"
Cohesion: 0.04
Nodes (48): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+40 more)

### Community 6 - "Employee"
Cohesion: 0.07
Nodes (3): EmployeeController, Collection, Employee

### Community 7 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.19
Nodes (4): CandidateLog, TaskCategory, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Database\Eloquent\Model

### Community 8 - "PasswordResetRequest"
Cohesion: 0.08
Nodes (5): AuthChatController, MathQuestionController, MathQuestion, PasswordResetChatMessage, PasswordResetRequest

### Community 9 - "TbArea"
Cohesion: 0.08
Nodes (5): JobStatistikController, TbArea, CandidateXlsxExportService, JobStatistikXlsxExportService, Symfony\Component\HttpFoundation\BinaryFileResponse

### Community 11 - "JobSpec"
Cohesion: 0.08
Nodes (3): PublicJobController, JobSpec, Illuminate\Support\Facades\Storage

### Community 12 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.10
Nodes (3): ApprovalWorkflowStepUser, InterviewAssessment, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 13 - "ApprovalWorkflowStep"
Cohesion: 0.10
Nodes (6): ApprovalWorkflowController, ApprovalWorkflow, ApprovalWorkflowStep, InhouseApproval, ApprovalWorkflowService, Illuminate\Support\Collection

### Community 14 - "Illuminate\Support\Facades\DB"
Cohesion: 0.08
Nodes (5): up(), Exception, Illuminate\Support\Facades\DB, SimpleXMLElement, ZipArchive

### Community 15 - "package.json"
Cohesion: 0.09
Nodes (19): devDependencies, axios, concurrently, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite, private (+11 more)

### Community 16 - "Controller"
Cohesion: 0.18
Nodes (5): AiRankingController, Controller, FeatureController, HomeController, Illuminate\Support\Facades\Route

### Community 18 - "ActivityLogger"
Cohesion: 0.14
Nodes (4): PrincipleController, Principle, ActivityLogger, OdooRecruitmentSyncService

### Community 20 - "Illuminate\Database\Eloquent\Relations\HasMany"
Cohesion: 0.15
Nodes (6): WorkPlanChatController, WpChatGroup, WpChatGroupMember, WpChatMessage, Illuminate\Database\Eloquent\Relations\HasMany, Illuminate\Database\Eloquent\Relations\HasOne

### Community 21 - "AiSetting"
Cohesion: 0.12
Nodes (4): AiSettingController, AiSetting, AiSettingUser, WaAreaSetting

### Community 22 - "CbtModuleTest"
Cohesion: 0.19
Nodes (5): Illuminate\Foundation\Testing\RefreshDatabase, Illuminate\Foundation\Testing\TestCase, CbtModuleTest, ExampleTest, TestCase

### Community 23 - "ActivityLog"
Cohesion: 0.09
Nodes (4): activity_log(), ActivityLogController, ActivityLog, ActivityLogXlsxExportService

### Community 24 - "OdooSyncLog"
Cohesion: 0.18
Nodes (6): OdooSyncActiveCommand, OdooSyncCommand, OdooSyncUpdatesResignsCommand, OdooSyncLog, Illuminate\Support\Str, Pdo\Mysql

### Community 25 - "Role"
Cohesion: 0.13
Nodes (4): RbacController, Permission, Role, Illuminate\Database\Eloquent\Relations\BelongsToMany

### Community 29 - "UserPrinsiple"
Cohesion: 0.28
Nodes (4): UserPrinsipleController, UserPrinsiple, Illuminate\Http\RedirectResponse, Illuminate\View\View

### Community 30 - "CandidateImportController.php"
Cohesion: 0.16
Nodes (8): Illuminate\Support\Facades\Auth, Illuminate\Support\Facades\Cache, Illuminate\Support\Facades\File, Illuminate\Support\Facades\Hash, Illuminate\Support\Facades\Log, Illuminate\Validation\Rule, Symfony\Component\HttpFoundation\StreamedResponse, Throwable

### Community 32 - "Closure"
Cohesion: 0.33
Nodes (5): EnsureCandidateAuthenticated, EnsureUserIsAdmin, RedirectIfInstalled, Closure, Symfony\Component\HttpFoundation\Response

### Community 35 - "AppServiceProvider.php"
Cohesion: 0.22
Nodes (6): AppServiceProvider, Illuminate\Auth\Events\Failed, Illuminate\Auth\Events\Login, Illuminate\Auth\Events\Logout, Illuminate\Support\Facades\Event, Illuminate\Support\ServiceProvider

### Community 36 - "DatabaseSeeder.php"
Cohesion: 0.27
Nodes (4): CbtCandidateSeeder, DatabaseSeeder, OdooEntitySeeder, Illuminate\Database\Seeder

### Community 37 - "Illuminate\Console\Command"
Cohesion: 0.19
Nodes (5): ImportJobSpecsCommand, ImportOfficialPrinciplesCommand, ImportWpDumpCommand, SyncOdooRecruitmentStagesCommand, Illuminate\Console\Command

### Community 46 - "bootstrap/app.php"
Cohesion: 0.40
Nodes (3): Illuminate\Foundation\Application, Illuminate\Foundation\Configuration\Exceptions, Illuminate\Foundation\Configuration\Middleware

### Community 47 - "logging.php"
Cohesion: 0.40
Nodes (4): Monolog\Handler\NullHandler, Monolog\Handler\StreamHandler, Monolog\Handler\SyslogUdpHandler, Monolog\Processor\PsrLogMessageProcessor

### Community 48 - "InstallController.php"
Cohesion: 0.25
Nodes (4): InstallController, Illuminate\Foundation\Inspiring, Illuminate\Support\Facades\Artisan, Illuminate\Support\Facades\Schedule

### Community 53 - "Carbon\Carbon"
Cohesion: 0.29
Nodes (4): CheckAstriCommand, App\Models\Employee, App\Models\Task, Carbon\Carbon

### Community 64 - "UserFactory.php"
Cohesion: 0.47
Nodes (3): UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

## Knowledge Gaps
- **63 isolated node(s):** `axios`, `concurrently`, `laravel-vite-plugin`, `tailwindcss`, `@tailwindcss/vite` (+58 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 474 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **129 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Candidate` connect `Candidate` to `OdooEntity`, `WorkExperience`, `Employee`, `Illuminate\Database\Eloquent\Model`, `PasswordResetRequest`, `TbArea`, `JobSpec`, `ApprovalWorkflowStep`, `Illuminate\Support\Facades\DB`, `Controller`, `AiAnalyzerService`, `ActivityLogger`, `KandidatPortalController`, `AiSetting`, `CbtModuleTest`, `Role`, `UserPrinsiple`, `CandidateImportController.php`, `Closure`, `InterviewInhouseController`, `DatabaseSeeder.php`, `Illuminate\Console\Command`, `InterviewPdfService`, `FixDummyMathResultsCommand.php`, `AiPdfService`, `ImportLegacyInterviewCommand.php`, `Carbon\Carbon`, `.syncSingleCandidate`, `.getMissingProfileFields`, `.testResults`?**
  _High betweenness centrality (0.161) - this node is a cross-community bridge._
- **Why does `User` connect `User` to `WorkExperience`, `WorkPlanController`, `Employee`, `Illuminate\Database\Eloquent\Model`, `PasswordResetRequest`, `Illuminate\Http\Request`, `JobSpec`, `ApprovalWorkflowStep`, `Illuminate\Support\Facades\DB`, `ActivityLogger`, `KandidatPortalController`, `Illuminate\Database\Eloquent\Relations\HasMany`, `ActivityLog`, `Role`, `UserPrinsiple`, `CandidateImportController.php`, `InterviewInhouseController`, `JobController`, `DatabaseSeeder.php`, `.coversAllAreas`, `JobController.php`, `InstallController.php`, `Carbon\Carbon`, `.auth`, `UserFactory.php`?**
  _High betweenness centrality (0.099) - this node is a cross-community bridge._
- **Why does `Employee` connect `Employee` to `OdooEntity`, `User`, `WorkPlanController`, `Illuminate\Database\Eloquent\Model`, `PasswordResetRequest`, `TbArea`, `Illuminate\Http\Request`, `JobSpec`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `ApprovalWorkflowStep`, `Illuminate\Support\Facades\DB`, `Controller`, `ActivityLogger`, `KandidatPortalController`, `Illuminate\Database\Eloquent\Relations\HasMany`, `Role`, `CandidateImportController.php`, `OdooSyncService`, `JobController.php`, `Carbon\Carbon`, `.auth`?**
  _High betweenness centrality (0.059) - this node is a cross-community bridge._
- **Are the 3 inferred relationships involving `User` (e.g. with `.handle()` and `.getMatchingApproversForCandidate()`) actually correct?**
  _`User` has 3 INFERRED edges - model-reasoned connections that need verification._
- **Are the 5 inferred relationships involving `Employee` (e.g. with `.handle()` and `.getMatchingRuleForCandidate()`) actually correct?**
  _`Employee` has 5 INFERRED edges - model-reasoned connections that need verification._
- **What connects `axios`, `concurrently`, `laravel-vite-plugin` to the rest of the system?**
  _63 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Candidate` be split into smaller, more focused modules?**
  _Cohesion score 0.038461538461538464 - nodes in this community are weakly interconnected._