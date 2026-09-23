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
        // 1. Tabel Alur Approval (Approval Workflows)
        if (!Schema::hasTable('approval_workflows')) {
            Schema::create('approval_workflows', function (Blueprint $table) {
                $table->id();
                $table->string('module')->unique()->index(); // e.g. 'kandidat_inhouse'
                $table->string('name');                      // e.g. 'Alur Approval Kandidat Inhouse'
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2. Tabel Tahapan Approval (Approval Workflow Steps)
        if (!Schema::hasTable('approval_workflow_steps')) {
            Schema::create('approval_workflow_steps', function (Blueprint $table) {
                $table->id();
                $table->foreignId('workflow_id')->constrained('approval_workflows')->onDelete('cascade');
                $table->integer('step_order')->default(1);
                $table->string('step_name');
                $table->enum('approver_type', ['head', 'user'])->default('user');
                
                // Kondisi Area: 'ALL', 'JAKARTA', 'OUTSIDE_JAKARTA', atau json array area
                $table->string('area_scope')->default('ALL');
                
                // Kondisi Entitas: 'ALL', atau kode entitas 'AMK', 'AKP', 'ATK', 'ABO', 'ATB', atau json array
                $table->string('entity_scope')->default('ALL');
                
                // Aturan Khusus: Lewati (skip) step ini jika Pimpinan rekruter adalah Direksi / Direktur / BOD
                $table->boolean('skip_if_direksi')->default(false);

                $table->text('description')->nullable();
                $table->timestamps();

                $table->index(['workflow_id', 'step_order']);
            });
        }

        // 3. Tabel Pivot User / Karyawan Approver per Step (Mendukung Multiple Users per Step)
        if (!Schema::hasTable('approval_workflow_step_users')) {
            Schema::create('approval_workflow_step_users', function (Blueprint $table) {
                $table->id();
                $table->foreignId('step_id')->constrained('approval_workflow_steps')->onDelete('cascade');
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
                $table->foreignId('employee_id')->nullable()->constrained('employees')->onDelete('cascade');
                $table->string('user_name');
                $table->string('user_email')->nullable();
                $table->timestamps();

                $table->index('step_id');
            });
        }

        // 4. Tambah kolom pelacakan step ke tabel candidates (jika belum ada)
        if (Schema::hasTable('candidates')) {
            Schema::table('candidates', function (Blueprint $table) {
                if (!Schema::hasColumn('candidates', 'current_approval_step_id')) {
                    $table->unsignedBigInteger('current_approval_step_id')->nullable()->after('status_approval');
                }
                if (!Schema::hasColumn('candidates', 'current_step_order')) {
                    $table->integer('current_step_order')->nullable()->after('current_approval_step_id');
                }
            });
        }

        // 5. Tambah kolom detail step ke tabel inhouse_approvals (jika belum ada)
        if (Schema::hasTable('inhouse_approvals')) {
            Schema::table('inhouse_approvals', function (Blueprint $table) {
                if (!Schema::hasColumn('inhouse_approvals', 'step_id')) {
                    $table->unsignedBigInteger('step_id')->nullable()->after('candidate_id');
                }
                if (!Schema::hasColumn('inhouse_approvals', 'step_order')) {
                    $table->integer('step_order')->nullable()->after('step_id');
                }
                if (!Schema::hasColumn('inhouse_approvals', 'step_name')) {
                    $table->string('step_name')->nullable()->after('step_order');
                }
                if (!Schema::hasColumn('inhouse_approvals', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->nullable()->after('step_name');
                }
            });
        }

        // 6. Seeding Default Workflow: Kandidat Inhouse
        $now = Carbon::now();
        $workflowId = DB::table('approval_workflows')->insertGetId([
            'module' => 'kandidat_inhouse',
            'name' => 'Alur Approval Kandidat Inhouse',
            'description' => 'Alur approval bertingkat dinamis untuk kandidat inhouse 5 entitas resmi perusahaan.',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Step 1: Head Approver (Berlaku Nasional / Semua Area, Skip jika Pimpinan Direksi)
        $step1Id = DB::table('approval_workflow_steps')->insertGetId([
            'workflow_id' => $workflowId,
            'step_order' => 1,
            'step_name' => 'Persetujuan Head Approver',
            'approver_type' => 'head',
            'area_scope' => 'ALL',
            'entity_scope' => 'ALL',
            'skip_if_direksi' => true,
            'description' => 'Persetujuan oleh Head / Pimpinan langsung rekruter sesuai data di Master Karyawan. Dilewati otomatis jika pimpinan adalah Direksi.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Step 2A: HRD Jakarta (Khusus Area Jakarta, Semua Entitas)
        $step2aId = DB::table('approval_workflow_steps')->insertGetId([
            'workflow_id' => $workflowId,
            'step_order' => 2,
            'step_name' => 'Persetujuan HRD Jakarta',
            'approver_type' => 'user',
            'area_scope' => 'JAKARTA',
            'entity_scope' => 'ALL',
            'skip_if_direksi' => false,
            'description' => 'Persetujuan oleh HRD Jakarta untuk kandidat formasi area Jakarta.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Step 2B: HRD Pusat (Khusus Area Selain Jakarta / Luar Jakarta)
        $step2bId = DB::table('approval_workflow_steps')->insertGetId([
            'workflow_id' => $workflowId,
            'step_order' => 2,
            'step_name' => 'Persetujuan HRD Pusat',
            'approver_type' => 'user',
            'area_scope' => 'OUTSIDE_JAKARTA',
            'entity_scope' => 'ALL',
            'skip_if_direksi' => false,
            'description' => 'Persetujuan oleh HRD Pusat untuk kandidat formasi selain Jakarta (daerah / cabang luar Jakarta).',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Cari user HRD untuk dihubungkan ke Step 2A & Step 2B secara default
        $hrdUsers = DB::table('users')
            ->where(function ($q) {
                $q->whereIn('role', ['admin', 'hrd', 'head_hr', 'admin_officer'])
                  ->orWhere('job_title', 'like', '%hr%')
                  ->orWhere('job_title', 'like', '%rekrut%');
            })
            ->limit(10)
            ->get();

        foreach ($hrdUsers as $u) {
            // Assign ke HRD Jakarta jika area Jakarta atau admin
            if (empty($u->area) || str_contains(strtoupper($u->area), 'JAKARTA') || in_array($u->role, ['admin', 'head_hr'])) {
                DB::table('approval_workflow_step_users')->insert([
                    'step_id' => $step2aId,
                    'user_id' => $u->id,
                    'user_name' => $u->name,
                    'user_email' => $u->email,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            // Assign ke HRD Pusat
            DB::table('approval_workflow_step_users')->insert([
                'step_id' => $step2bId,
                'user_id' => $u->id,
                'user_name' => $u->name,
                'user_email' => $u->email,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_workflow_step_users');
        Schema::dropIfExists('approval_workflow_steps');
        Schema::dropIfExists('approval_workflows');

        if (Schema::hasTable('candidates')) {
            Schema::table('candidates', function (Blueprint $table) {
                if (Schema::hasColumn('candidates', 'current_approval_step_id')) {
                    $table->dropColumn('current_approval_step_id');
                }
                if (Schema::hasColumn('candidates', 'current_step_order')) {
                    $table->dropColumn('current_step_order');
                }
            });
        }

        if (Schema::hasTable('inhouse_approvals')) {
            Schema::table('inhouse_approvals', function (Blueprint $table) {
                if (Schema::hasColumn('inhouse_approvals', 'step_id')) {
                    $table->dropColumn(['step_id', 'step_order', 'step_name', 'user_id']);
                }
            });
        }
    }
};
