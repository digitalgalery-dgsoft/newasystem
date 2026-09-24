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

        $this->info("\n=== INHOUSE EMPLOYEES EMAIL DUPLICATES ===");
        $inhouse = Employee::where('status', 'Aktiv')
            ->where(function($q) {
                $q->where('tipe_karyawan', 'Inhouse')
                  ->orWhereRaw('LOWER(TRIM(tipe_karyawan)) = ?', ['inhouse']);
            })->get();

        $seen = [];
        $dupes = [];
        foreach ($inhouse as $e) {
            $em = strtolower(trim($e->email ?? ''));
            if (empty($em)) continue;
            if (isset($seen[$em])) {
                $dupes[] = $em;
            } else {
                $seen[$em] = $e->id;
            }
        }
        $this->info("Duplicate emails found: " . count($dupes));
        if (!empty($dupes)) {
            $this->warn("Duplicates: " . implode(', ', array_unique($dupes)));
        }

        $yohana = Employee::where('nama_karyawan', 'LIKE', '%Yohana Teraseptia%')->first();
        if ($yohana) {
            $this->info("Yohana Emp ID: {$yohana->id}, NIK: {$yohana->nik}, Email: '{$yohana->email}', Jabatan: '{$yohana->jabatan}'");
            $yUser = User::whereRaw('LOWER(email) = ?', [strtolower(trim($yohana->email))])->first();
            $this->info("Yohana User: " . ($yUser ? "Found ID {$yUser->id}" : "NOT FOUND"));
        }

        $usersYohana = User::where('name', 'LIKE', '%Yohana%')
            ->orWhere('email', 'LIKE', '%yohana%')
            ->orWhere('name', 'LIKE', '%Teraseptia%')
            ->orWhere('name', 'LIKE', '%seagma%')
            ->get();
        $this->info("Users found: " . $usersYohana->count());
        foreach ($usersYohana as $u) {
            $this->line("User ID: {$u->id} | Name: '{$u->name}' | Email: '{$u->email}' | Role: '{$u->role}' | IsActive: '{$u->is_active}'");
        }

        $activeNew = Task::where(function($q) {
            $q->whereRaw('LOWER(TRIM("user")) LIKE ?', ['%astri wahyuni%st%'])
              ->orWhereRaw('LOWER(TRIM("assignee")) LIKE ?', ['%astri wahyuni%st%']);
        })->whereNotIn('status', ['archived'])->select('id', 'title', 'status', 'user', 'assignee')->get();
        $this->info("\n=== PREVIEW SALIN LAPORAN (WA) FOR ASTRI ===");
        if ($exactUser) {
            \Illuminate\Support\Facades\Auth::login($exactUser);
            $req = \Illuminate\Http\Request::create('/workplan/copy-report', 'GET');
            $ctrl = new \App\Http\Controllers\WorkPlanController();
            $res = $ctrl->copyReport($req);
            $data = $res->getData(true);
            $this->line($data['report']);
            $this->info("Counts returned: " . json_encode($data['counts']));
        }

        return 0;
    }
}
