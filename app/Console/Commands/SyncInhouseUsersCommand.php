<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class SyncInhouseUsersCommand extends Command
{
    protected $signature = 'users:sync-inhouse';
    protected $description = 'Sync active inhouse employees from master karyawan into users table for approval workflows and system access';

    public function handle()
    {
        $this->info("==============================================================================");
        $this->info("🚀 SYNCHRONIZING ACTIVE INHOUSE EMPLOYEES TO USERS TABLE");
        $this->info("==============================================================================");

        $inhouseEmployees = Employee::where('status', 'Aktiv')
            ->where(function ($q) {
                $q->where('tipe_karyawan', 'Inhouse')
                  ->orWhereRaw('LOWER(TRIM(tipe_karyawan)) = ?', ['inhouse']);
            })
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->get();

        $this->info("Found " . $inhouseEmployees->count() . " active inhouse employees with valid email.");

        $created = 0;
        $updated = 0;

        foreach ($inhouseEmployees as $emp) {
            $email = strtolower(trim($emp->email));
            if (empty($email)) continue;

            $role = 'karyawan_inhouse';
            $jobLower = strtolower($emp->jabatan ?? '');
            if (str_contains($jobLower, 'recruiter') || str_contains($jobLower, 'rekrutmen')) {
                $role = 'recruiter';
            } elseif (str_contains($jobLower, 'head hr') || str_contains($jobLower, 'hrd manager') || str_contains($jobLower, 'manager hr')) {
                $role = 'head_hr';
            } elseif (str_contains($jobLower, 'head') || str_contains($jobLower, 'lead') || str_contains($jobLower, 'manager') || str_contains($jobLower, 'spv') || str_contains($jobLower, 'supervisor')) {
                $role = 'head';
            }

            $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

            if (!$user) {
                User::create([
                    'name' => trim($emp->nama_karyawan),
                    'email' => $email,
                    'password' => $emp->password ?: Hash::make($emp->default_password ?: 'password'),
                    'role' => $role,
                    'area' => $emp->area,
                    'job_title' => $emp->jabatan,
                    'phone' => $emp->telepon,
                    'is_active' => true,
                ]);
                $created++;
            } else {
                $updates = [];
                if (empty($user->job_title) && !empty($emp->jabatan)) {
                    $updates['job_title'] = $emp->jabatan;
                }
                if (empty($user->area) && !empty($emp->area)) {
                    $updates['area'] = $emp->area;
                }
                if (!$user->is_active) {
                    $updates['is_active'] = true;
                }
                if (!empty($updates)) {
                    $user->update($updates);
                    $updated++;
                }
            }
        }

        $this->info("Synchronization finished: {$created} users created, {$updated} users updated.");

        // Check Yohana Teraseptia Seagma specifically
        $yohanaUser = User::where('name', 'LIKE', '%Yohana%')
            ->orWhere('email', 'LIKE', '%seagmayohana%')
            ->first();

        if ($yohanaUser) {
            $this->info("\n=== VERIFICATION: YOHANA TERASEPTIA SEAGMA ===");
            $this->info("User ID   : {$yohanaUser->id}");
            $this->info("Name      : {$yohanaUser->name}");
            $this->info("Email     : {$yohanaUser->email}");
            $this->info("Job Title : {$yohanaUser->job_title}");
            $this->info("Area      : {$yohanaUser->area}");
            $this->info("Is Active : " . ($yohanaUser->is_active ? 'YES' : 'NO'));
        } else {
            $this->warn("Yohana user was NOT found after sync!");
        }

        return 0;
    }
}
