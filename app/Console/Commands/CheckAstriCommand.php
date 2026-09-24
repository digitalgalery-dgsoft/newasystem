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
        $this->info("=== EXACT USER FOR astriramelan@gmail.com ===");
        $exactUser = User::where('email', 'astriramelan@gmail.com')->first();
        if ($exactUser) {
            $this->line("User ID: {$exactUser->id} | Name: '{$exactUser->name}' | Email: '{$exactUser->email}' | Role: '{$exactUser->role}' | Job: '{$exactUser->job_title}'");
        } else {
            $this->warn("User astriramelan@gmail.com NOT found!");
        }

        $this->info("\n=== EXACT EMPLOYEE FOR astriramelan@gmail.com or ASTRI WAHYUNI ===");
        $exactEmp = Employee::where('email', 'astriramelan@gmail.com')
            ->orWhereRaw('LOWER(TRIM(nama_karyawan)) LIKE ?', ['%astri wahyuni%'])
            ->get();
        foreach ($exactEmp as $e) {
            $this->line("Emp ID: {$e->id} | NIK: '{$e->nik}' | Name: '{$e->nama_karyawan}' | Email: '{$e->email}' | Jabatan: '{$e->jabatan}' | Pimpinan: '{$e->pimpinan}' | Tipe: '{$e->tipe_karyawan}'");
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
