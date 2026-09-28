<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;

class CheckAdminCommand extends Command
{
    protected $signature = 'admin:check {password?} {--set-password=}';
    protected $description = 'Check admin user status and test password';

    public function handle()
    {
        $this->info('=== CHECK ADMIN USER ===');
        $user = User::where('email', 'admin@asystem.co.id')->first();
        if ($user) {
            $this->info("User ID: {$user->id}");
            $this->info("Name: {$user->name}");
            $this->info("Email: {$user->email}");
            $this->info("Role: {$user->role}");
            $this->info("is_active: " . var_export($user->is_active, true));
            $this->info("Password Hash: {$user->password}");

            $newPassword = $this->option('set-password');
            if ($newPassword) {
                $user->password = Hash::make($newPassword);
                $user->is_active = true;
                $user->save();
                $this->info("Password successfully updated to '{$newPassword}'!");
            }

            $testPassword = $this->argument('password');
            if ($testPassword) {
                $matches = Hash::check($testPassword, $user->password);
                $this->info("Testing password '{$testPassword}': " . ($matches ? 'MATCH!' : 'NO MATCH!'));
            }
        } else {
            $this->error('User admin@asystem.co.id NOT FOUND in users table!');
        }

        $employees = Employee::where('email', 'admin@asystem.co.id')->get();
        $this->info("Employees count: " . $employees->count());
        foreach ($employees as $e) {
            $this->info("Emp ID: {$e->id}, Name: {$e->nama_karyawan}, Status: {$e->status}, Tipe: {$e->tipe_karyawan}, Akses: {$e->akses_login}");
        }

        return 0;
    }
}
