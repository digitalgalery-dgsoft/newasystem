<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Employee;
use App\Models\Task;
use Illuminate\Support\Facades\DB;

class SyncAstriWpCommand extends Command
{
    protected $signature = 'wp:sync-astri-names';
    protected $description = 'Harmonize and synchronize Astri Wahyuni legacy tasks and records to ASTRI WAHYUNI,ST';

    public function handle()
    {
        $this->info("==============================================================================");
        $this->info("🚀 SYNCHRONIZING ASTRI WAHYUNI WORK PLAN TASKS & DATA");
        $this->info("==============================================================================");

        $officialName = 'ASTRI WAHYUNI,ST';
        $legacyName = 'Astri Wahyuni';

        // 1. Update tasks user, assignee, delegator
        $updatedUser = Task::whereRaw('LOWER(TRIM("user")) = ?', [strtolower($legacyName)])
            ->update(['user' => $officialName]);
        
        $updatedAssignee = Task::whereRaw('LOWER(TRIM("assignee")) = ?', [strtolower($legacyName)])
            ->update(['assignee' => $officialName]);

        $updatedDelegator = Task::whereRaw('LOWER(TRIM("delegator")) = ?', [strtolower($legacyName)])
            ->update(['delegator' => $officialName]);

        $this->info("Updated tasks: user ({$updatedUser}), assignee ({$updatedAssignee}), delegator ({$updatedDelegator})");

        // 2. Update task_notifications
        $updatedNotifs = DB::table('task_notifications')
            ->whereRaw('LOWER(TRIM("user_recipient")) = ?', [strtolower($legacyName)])
            ->update(['user_recipient' => $officialName]);
        $this->info("Updated notifications user_recipient: {$updatedNotifs}");

        // 3. Update tb_workplan (daily workplan)
        $updatedDaily = DB::table('tb_workplan')
            ->whereRaw('LOWER(TRIM("user")) = ?', [strtolower($legacyName)])
            ->update(['user' => $officialName]);
        $this->info("Updated daily logs (tb_workplan): {$updatedDaily}");

        // 4. Archive old tasks from September 18 and earlier that were superseded
        $pastTasksIds = [10103, 13382, 13383, 13405, 13407, 13408, 13410, 13412];
        $archivedCount = Task::whereIn('id', $pastTasksIds)
            ->whereNotIn('status', ['archived'])
            ->update([
                'status' => 'archived',
                'date_completed' => DB::raw("COALESCE(date_completed, '2026-09-18')")
            ]);
        $this->info("Archived past active tasks from 18 September ({$archivedCount} tasks)");

        // 5. Check resulting status for ASTRI WAHYUNI,ST
        $activeTasks = Task::where(function ($q) use ($officialName) {
            $q->where('user', $officialName)
              ->orWhere('assignee', $officialName);
        })->whereNotIn('status', ['archived'])->get(['id', 'title', 'status']);

        $archivedTasksCount = Task::where(function ($q) use ($officialName) {
            $q->where('user', $officialName)
              ->orWhere('assignee', $officialName);
        })->where('status', 'archived')->count();

        $this->info("\n=== VERIFICATION RESULT ===");
        $this->info("Total Active Tasks for '{$officialName}': " . $activeTasks->count());
        foreach ($activeTasks as $t) {
            $this->line("  [#{$t->id}] ({$t->status}) {$t->title}");
        }
        $this->info("Total Archived Tasks for '{$officialName}': {$archivedTasksCount}");

        return 0;
    }
}
