<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use App\Models\TaskActivity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AutoArchiveDoneTasksCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workplan:auto-archive-done {--dry-run : Only show tasks that would be archived without modifying}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-archive Work Plan tasks in Done step whose completion date is before today';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today()->toDateString();
        $isDryRun = $this->option('dry-run');

        $this->info("==============================================================================");
        $this->info("📦 AUTO-ARCHIVE WORK PLAN DONE TASKS (COMPLETION DATE < {$today})");
        $this->info("==============================================================================");

        $query = Task::where('status', 'done')
            ->where(function ($q) use ($today) {
                $q->where(function ($sub) use ($today) {
                    $sub->whereNotNull('date_completed')
                        ->whereDate('date_completed', '<', $today);
                })->orWhere(function ($sub) use ($today) {
                    $sub->whereNull('date_completed')
                        ->where(function ($fallback) use ($today) {
                            $fallback->whereNotNull('updated_at')->whereDate('updated_at', '<', $today)
                                     ->orWhere(function ($fb2) use ($today) {
                                         $fb2->whereNull('updated_at')
                                             ->whereNotNull('date_input')
                                             ->whereDate('date_input', '<', $today);
                                     });
                        });
                });
            });

        $totalCount = $query->count();
        $this->info("Ditemukan {$totalCount} tugas berstatus Done dengan tanggal selesai sebelum hari ini.");

        if ($totalCount === 0) {
            $this->info("Tidak ada tugas Done yang perlu diarsipkan.");
            return 0;
        }

        if ($isDryRun) {
            $this->warn("[DRY-RUN] Daftar tugas yang akan diarsipkan:");
            $tasks = $query->take(20)->get(['id', 'title', 'assignee', 'date_completed', 'updated_at', 'date_input']);
            foreach ($tasks as $t) {
                $date = $t->date_completed ?? $t->updated_at ?? $t->date_input;
                $this->line("- Task #{$t->id}: {$t->title} (Assignee: {$t->assignee}, Selesai: {$date})");
            }
            if ($totalCount > 20) {
                $this->line("... dan " . ($totalCount - 20) . " tugas lainnya.");
            }
            return 0;
        }

        $archivedCount = Task::autoArchivePastDoneTasks();

        $this->info("✅ Berhasil mengarsipkan {$archivedCount} tugas berstatus Done secara otomatis.");
        Log::info("WorkPlan Auto-Archive: Berhasil mengarsipkan {$archivedCount} tugas Done dengan tanggal selesai < {$today}.");

        return 0;
    }
}
