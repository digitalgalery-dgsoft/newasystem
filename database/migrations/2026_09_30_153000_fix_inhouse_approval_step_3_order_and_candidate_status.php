<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $now = Carbon::now('Asia/Jakarta');

        // 1. Sinkronisasi urutan step di approval_workflow_steps
        // Cari workflow kandidat inhouse
        $inhouseWf = DB::table('approval_workflows')->where('module', 'kandidat_inhouse')->first();
        $wfId = $inhouseWf ? $inhouseWf->id : 1;

        // a. Pastikan Step 1: Head Approver -> step_order = 1
        DB::table('approval_workflow_steps')
            ->where('workflow_id', $wfId)
            ->where(function ($q) {
                $q->where('approver_type', 'head')
                  ->orWhere('step_name', 'like', '%Head%');
            })
            ->update([
                'step_order' => 1,
                'updated_at' => $now,
            ]);

        // b. Pastikan Step 2: HRD Jakarta -> step_order = 2
        $stepHrdJakarta = DB::table('approval_workflow_steps')
            ->where('workflow_id', $wfId)
            ->where('step_name', 'like', '%Jakarta%')
            ->first();

        if ($stepHrdJakarta) {
            DB::table('approval_workflow_steps')
                ->where('id', $stepHrdJakarta->id)
                ->update([
                    'step_order' => 2,
                    'area_scope' => 'JAKARTA',
                    'updated_at' => $now,
                ]);

            // Jika rules di lokal masih null, sinkronkan 5 aturan dinamis Jakarta
            if (empty($stepHrdJakarta->approval_rules)) {
                $jakartaRules = [
                    [
                        'id' => 'rule_1790171557920',
                        'area' => 'JAKARTA',
                        'prinsiple' => 'AKP',
                        'user_ids' => [13],
                        'users' => [
                            ['id' => 13, 'name' => 'URIYANTO', 'email' => 'urie001@gmail.com', 'job_title' => 'HRD - Jakarta', 'role' => 'karyawan_inhouse']
                        ]
                    ],
                    [
                        'id' => 'rule_1790171583799_e7c',
                        'area' => 'JAKARTA',
                        'prinsiple' => 'AMK',
                        'user_ids' => [563],
                        'users' => [
                            ['id' => 563, 'name' => 'YOHANA TERASEPTIA SEAGMA', 'email' => 'seagmayohana@gmail.com', 'job_title' => 'ADMIN OPS - Jakarta', 'role' => 'karyawan_inhouse']
                        ]
                    ],
                    [
                        'id' => 'rule_1790219627706_zew',
                        'area' => 'JAKARTA',
                        'prinsiple' => 'ATK',
                        'user_ids' => [413],
                        'users' => [
                            ['id' => 413, 'name' => 'YUNITA MATONDANG', 'email' => 'yunitondang1@gmail.com', 'job_title' => 'ADMIN HRD - Jakarta', 'role' => 'karyawan_inhouse']
                        ]
                    ],
                    [
                        'id' => 'rule_1790219682723_syx',
                        'area' => 'JAKARTA',
                        'prinsiple' => 'ABO',
                        'user_ids' => [108],
                        'users' => [
                            ['id' => 108, 'name' => 'MUHAMMAD SAFIQ', 'email' => 'safiqleo@gmail.com', 'job_title' => 'ADMIN HRD DAN FINANCE - Jakarta', 'role' => 'karyawan_inhouse']
                        ]
                    ],
                    [
                        'id' => 'rule_1790219708987_bsi',
                        'area' => 'JAKARTA',
                        'prinsiple' => 'ATB',
                        'user_ids' => [563],
                        'users' => [
                            ['id' => 563, 'name' => 'YOHANA TERASEPTIA SEAGMA', 'email' => 'seagmayohana@gmail.com', 'job_title' => 'ADMIN OPS - Jakarta', 'role' => 'karyawan_inhouse']
                        ]
                    ]
                ];

                DB::table('approval_workflow_steps')
                    ->where('id', $stepHrdJakarta->id)
                    ->update([
                        'approval_rules' => json_encode($jakartaRules),
                    ]);

                foreach ($jakartaRules as $jRule) {
                    foreach ($jRule['users'] as $u) {
                        $actualUid = $u['id'];
                        $userExists = DB::table('users')->where('id', $u['id'])->exists();
                        if (!$userExists) {
                            $byEmail = DB::table('users')->where('email', $u['email'])->first();
                            if ($byEmail) {
                                $actualUid = $byEmail->id;
                            } else {
                                try {
                                    DB::table('users')->insert([
                                        'id' => $u['id'],
                                        'name' => $u['name'],
                                        'email' => $u['email'],
                                        'password' => bcrypt('password'),
                                        'role' => $u['role'] ?? 'karyawan_inhouse',
                                        'job_title' => $u['job_title'] ?? 'HRD',
                                        'is_active' => 1,
                                        'created_at' => $now,
                                        'updated_at' => $now,
                                    ]);
                                } catch (\Throwable $e) {
                                    $actualUid = DB::table('users')->insertGetId([
                                        'name' => $u['name'],
                                        'email' => $u['email'],
                                        'password' => bcrypt('password'),
                                        'role' => $u['role'] ?? 'karyawan_inhouse',
                                        'job_title' => $u['job_title'] ?? 'HRD',
                                        'is_active' => 1,
                                        'created_at' => $now,
                                        'updated_at' => $now,
                                    ]);
                                }
                            }
                        }

                        try {
                            DB::table('approval_workflow_step_users')->updateOrInsert(
                                [
                                    'step_id' => $stepHrdJakarta->id,
                                    'user_id' => $actualUid,
                                    'area' => $jRule['area'],
                                    'prinsiple' => $jRule['prinsiple'],
                                ],
                                [
                                    'user_name' => $u['name'],
                                    'user_email' => $u['email'],
                                    'updated_at' => $now,
                                    'created_at' => $now,
                                ]
                            );
                        } catch (\Throwable $e) {}
                    }
                }
            }
        }

        // c. Pastikan Step 3: HRD Pusat -> step_order = 3
        $stepHrdPusat = DB::table('approval_workflow_steps')
            ->where('workflow_id', $wfId)
            ->where(function ($q) {
                $q->where('step_name', 'like', '%Pusat%')
                  ->orWhere('id', 3);
            })
            ->first();

        $pusatRules = [
            [
                'id' => 'rule_1790172005278',
                'area' => 'ALL',
                'prinsiple' => 'ALL',
                'user_ids' => [284],
                'users' => [
                    ['id' => 284, 'name' => 'NURUL YULIASTUTI, SH.', 'email' => 'nurulyuliastuti19@gmail.com', 'job_title' => 'HOD HRD - Surabaya', 'role' => 'karyawan_inhouse']
                ]
            ]
        ];

        if ($stepHrdPusat) {
            $updateData = [
                'step_order' => 3,
                'area_scope' => 'ALL',
                'entity_scope' => 'ALL',
                'description' => 'Persetujuan oleh HRD Pusat untuk kandidat inhouse seluruh area.',
                'updated_at' => $now,
            ];
            if (empty($stepHrdPusat->approval_rules)) {
                $updateData['approval_rules'] = json_encode($pusatRules);
            }

            DB::table('approval_workflow_steps')
                ->where('id', $stepHrdPusat->id)
                ->update($updateData);

            $pusatStepId = $stepHrdPusat->id;
        } else {
            $pusatStepId = DB::table('approval_workflow_steps')->insertGetId([
                'workflow_id' => $wfId,
                'step_order' => 3,
                'step_name' => 'Persetujuan HRD Pusat',
                'approver_type' => 'user',
                'area_scope' => 'ALL',
                'entity_scope' => 'ALL',
                'skip_if_direksi' => false,
                'approval_rules' => json_encode($pusatRules),
                'description' => 'Persetujuan oleh HRD Pusat untuk kandidat inhouse seluruh area.',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // Sinkronisasi step_user untuk HRD Pusat
        $nurulUid = 284;
        $nurulExists = DB::table('users')->where('id', 284)->exists();
        if (!$nurulExists) {
            $byEmail = DB::table('users')->where('email', 'nurulyuliastuti19@gmail.com')->first();
            if ($byEmail) {
                $nurulUid = $byEmail->id;
            } else {
                try {
                    DB::table('users')->insert([
                        'id' => 284,
                        'name' => 'NURUL YULIASTUTI, SH.',
                        'email' => 'nurulyuliastuti19@gmail.com',
                        'password' => bcrypt('password'),
                        'role' => 'karyawan_inhouse',
                        'job_title' => 'HOD HRD - Surabaya',
                        'is_active' => 1,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                } catch (\Throwable $e) {
                    $nurulUid = DB::table('users')->insertGetId([
                        'name' => 'NURUL YULIASTUTI, SH.',
                        'email' => 'nurulyuliastuti19@gmail.com',
                        'password' => bcrypt('password'),
                        'role' => 'karyawan_inhouse',
                        'job_title' => 'HOD HRD - Surabaya',
                        'is_active' => 1,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }

        try {
            DB::table('approval_workflow_step_users')->updateOrInsert(
                [
                    'step_id' => $pusatStepId,
                    'user_id' => $nurulUid,
                    'area' => 'ALL',
                    'prinsiple' => 'ALL',
                ],
                [
                    'user_name' => 'NURUL YULIASTUTI, SH.',
                    'user_email' => 'nurulyuliastuti19@gmail.com',
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        } catch (\Throwable $e) {}

        // 2. Sinkronisasi Data Approval Log di inhouse_approvals
        // Candidate 63433 (Azanuddin Fakhrozi):
        // Record 1: David Oscar Sahala G Sibuea -> Step 1 (Head)
        DB::table('inhouse_approvals')
            ->where('candidate_id', 63433)
            ->where('nama_approver', 'like', '%DAVID OSCAR%')
            ->update([
                'step_id' => 1,
                'step_order' => 1,
                'step_name' => 'Persetujuan Head Approver',
            ]);

        // Record 2: Uriyanto -> Step 2 (HRD Jakarta)
        DB::table('inhouse_approvals')
            ->where('candidate_id', 63433)
            ->where('nama_approver', 'like', '%URIYANTO%')
            ->update([
                'step_id' => 2,
                'step_order' => 2,
                'step_name' => 'Persetujuan HRD Jakarta',
                'user_id' => 13,
            ]);

        // General update untuk approval log lama yang step_id nya masih null
        DB::table('inhouse_approvals')
            ->whereNull('step_id')
            ->where(function ($q) {
                $q->where('nama_approver', 'like', '%URIYANTO%')
                  ->orWhere('jabatan_approver', 'like', '%Jakarta%');
            })
            ->update([
                'step_id' => 2,
                'step_order' => 2,
                'step_name' => 'Persetujuan HRD Jakarta',
                'user_id' => 13,
            ]);

        DB::table('inhouse_approvals')
            ->whereNull('step_id')
            ->where(function ($q) {
                $q->where('nama_approver', 'like', '%NURUL%')
                  ->orWhere('jabatan_approver', 'like', '%Pusat%')
                  ->orWhere('jabatan_approver', 'like', '%Surabaya%');
            })
            ->update([
                'step_id' => $pusatStepId,
                'step_order' => 3,
                'step_name' => 'Persetujuan HRD Pusat',
                'user_id' => 284,
            ]);

        DB::table('inhouse_approvals')
            ->whereNull('step_id')
            ->update([
                'step_id' => 1,
                'step_order' => 1,
                'step_name' => 'Persetujuan Head Approver',
            ]);

        // 3. Perbaikan Status Kandidat Azanuddin Fakhrozi (ID: 63433)
        // Telah melewati Approval Head (Step 1) dan HRD Jakarta (Step 2)
        // Status seharusnya: Menunggu Persetujuan HRD Pusat (Step 3)
        DB::table('candidates')
            ->where('id', 63433)
            ->update([
                'status_approval' => 'Review Persetujuan HRD Pusat',
                'current_approval_step_id' => $pusatStepId,
                'current_step_order' => 3,
                'time_prinsiple' => null,
                'ttd_prinsiple' => null,
                'updated_at' => $now,
            ]);

        // 4. Sinkronisasi current_approval_step_id untuk kandidat inhouse lainnya
        $headStep = DB::table('approval_workflow_steps')->where('workflow_id', $wfId)->where('step_order', 1)->first();
        if ($headStep) {
            DB::table('candidates')
                ->where('is_inhouse', 1)
                ->whereIn('status_approval', ['Review Head', 'Review Persetujuan Head Approver'])
                ->whereNull('current_approval_step_id')
                ->update([
                    'current_approval_step_id' => $headStep->id,
                    'current_step_order' => 1,
                    'status_approval' => 'Review Persetujuan Head Approver',
                ]);
        }

        // Kandidat dengan status Review HRD
        $jktStep = DB::table('approval_workflow_steps')->where('workflow_id', $wfId)->where('step_order', 2)->first();
        if ($jktStep) {
            DB::table('candidates')
                ->where('is_inhouse', 1)
                ->where('status_approval', 'Review HRD')
                ->where(function ($q) {
                    $q->where('area', 'like', '%Jakarta%')
                      ->orWhere('penempatan', 'like', '%Jakarta%');
                })
                ->whereNull('current_approval_step_id')
                ->update([
                    'current_approval_step_id' => $jktStep->id,
                    'current_step_order' => 2,
                    'status_approval' => 'Review Persetujuan HRD Jakarta',
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert candidate 63433 if needed
    }
};
