<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $now = now();

        // 1. Pastikan permission 'view_all_candidates' terdaftar
        DB::table('permissions')->updateOrInsert(
            ['name' => 'view_all_candidates'],
            [
                'display_name' => 'Lihat Seluruh Kandidat Nasional',
                'module' => 'Rekrutmen & Interview',
                'description' => 'Melihat seluruh data pelamar lowongan kerja dan talent pool secara nasional tanpa dibatasi akun rekruter penanggung jawab.',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        $viewAllPerm = DB::table('permissions')->where('name', 'view_all_candidates')->first();

        // 2. Pastikan role 'admin_officer' terdaftar
        DB::table('roles')->updateOrInsert(
            ['name' => 'admin_officer'],
            [
                'display_name' => 'Administrator Talent Pool',
                'description' => 'Akses administrator nasional untuk monitoring pelamar portal, talent pool rekrutmen, perankingan AI, dan ekspor data.',
                'is_system' => false,
                'handle_all_principles' => true,
                'allowed_principles' => null,
                'cover_all_areas' => true,
                'allowed_areas' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        $adminRole = DB::table('roles')->where('name', 'admin')->first();
        $officerRole = DB::table('roles')->where('name', 'admin_officer')->first();

        // 3. Berikan permission 'view_all_candidates' ke admin
        if ($adminRole && $viewAllPerm) {
            DB::table('role_permissions')->updateOrInsert([
                'role_id' => $adminRole->id,
                'permission_id' => $viewAllPerm->id,
            ]);
        }

        // 4. Berikan permission ke admin_officer: view_all_candidates, interview.view, interview.assess, ai.ranking, job.manage, export.data
        if ($officerRole) {
            $permsToAssign = [
                'view_all_candidates',
                'interview.view',
                'interview.assess',
                'ai.ranking',
                'job.manage',
                'export.data',
            ];

            foreach ($permsToAssign as $permName) {
                $p = DB::table('permissions')->where('name', $permName)->first();
                if ($p) {
                    DB::table('role_permissions')->updateOrInsert([
                        'role_id' => $officerRole->id,
                        'permission_id' => $p->id,
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $perm = DB::table('permissions')->where('name', 'view_all_candidates')->first();
        if ($perm) {
            DB::table('role_permissions')->where('permission_id', $perm->id)->delete();
            DB::table('permissions')->where('id', $perm->id)->delete();
        }
    }
};
