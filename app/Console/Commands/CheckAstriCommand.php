<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Employee;
use App\Models\Task;
use Illuminate\Support\Facades\DB;

class CheckAstriCommand extends Command
{
    protected $signature = 'wp:check-astri';
    protected $description = 'Check Astri Wahyuni user, employee, and task records';

    public function handle()
    {
        $this->info("=== USERS LIKE ASTRI / RAMELAN ===");
        $users = User::where('email', 'LIKE', '%astriramelan%')
            ->orWhere('email', 'LIKE', '%astri%')
            ->orWhere('name', 'LIKE', '%Astri%')
            ->get();
        foreach ($users as $u) {
            $this->line("User ID: {$u->id} | Name: '{$u->name}' | Email: '{$u->email}' | Role: '{$u->role}' | Job: '{$u->job_title}'");
        }

        $this->info("\n=== EMPLOYEES LIKE ASTRI / RAMELAN ===");
        $emps = Employee::where('email', 'LIKE', '%astriramelan%')
            ->orWhere('email', 'LIKE', '%astri%')
            ->orWhere('nama_karyawan', 'LIKE', '%Astri%')
            ->get();
        foreach ($emps as $e) {
            $this->line("Emp ID: {$e->id} | NIK: '{$e->nik}' | Name: '{$e->nama_karyawan}' | Email: '{$e->email}' | Jabatan: '{$e->jabatan}' | Pimpinan: '{$e->pimpinan}'");
        }

        $this->info("\n=== TASKS COUNTS FOR ASTRI ===");
        $oldNameCount = Task::where(function($q) {
            $q->whereRaw('LOWER(TRIM("user")) = ?', ['astri wahyuni'])
              ->orWhereRaw('LOWER(TRIM("assignee")) = ?', ['astri wahyuni'])
              ->orWhereRaw('LOWER(TRIM("delegator")) = ?', ['astri wahyuni']);
        })->count();
        $this->line("Tasks with 'Astri Wahyuni': {$oldNameCount}");

        $newNameCount = Task::where(function($q) {
            $q->whereRaw('LOWER(TRIM("user")) LIKE ?', ['%astri wahyuni%st%'])
              ->orWhereRaw('LOWER(TRIM("assignee")) LIKE ?', ['%astri wahyuni%st%'])
              ->orWhereRaw('LOWER(TRIM("delegator")) LIKE ?', ['%astri wahyuni%st%']);
        })->count();
        $this->line("Tasks with 'ASTRI WAHYUNI,ST': {$newNameCount}");

        $activeOld = Task::where(function($q) {
            $q->whereRaw('LOWER(TRIM("user")) = ?', ['astri wahyuni'])
              ->orWhereRaw('LOWER(TRIM("assignee")) = ?', ['astri wahyuni']);
        })->whereNotIn('status', ['archived'])->select('id', 'title', 'status', 'user', 'assignee')->get();
        $this->info("Active tasks with 'Astri Wahyuni': " . $activeOld->count());
        foreach ($activeOld as $t) {
            $this->line("  [#{$t->id}] ({$t->status}) {$t->title} (User: '{$t->user}', Assignee: '{$t->assignee}')");
        }

        $activeNew = Task::where(function($q) {
            $q->whereRaw('LOWER(TRIM("user")) LIKE ?', ['%astri wahyuni%st%'])
              ->orWhereRaw('LOWER(TRIM("assignee")) LIKE ?', ['%astri wahyuni%st%']);
        })->whereNotIn('status', ['archived'])->select('id', 'title', 'status', 'user', 'assignee')->get();
        $this->info("Active tasks with 'ASTRI WAHYUNI,ST': " . $activeNew->count());
        foreach ($activeNew as $t) {
            $this->line("  [#{$t->id}] ({$t->status}) {$t->title} (User: '{$t->user}', Assignee: '{$t->assignee}')");
        }

        return 0;
    }
}
