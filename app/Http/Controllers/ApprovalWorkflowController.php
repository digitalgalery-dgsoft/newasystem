<?php

namespace App\Http\Controllers;

use App\Models\ApprovalWorkflow;
use App\Models\ApprovalWorkflowStep;
use App\Models\ApprovalWorkflowStepUser;
use App\Models\Employee;
use App\Models\Principle;
use App\Models\User;
use App\Models\TbArea;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApprovalWorkflowController extends Controller
{
    /**
     * Halaman Utama Konfigurasi Alur Approver Dinamis
     */
    public function index(Request $request)
    {
        $module = $request->query('module', 'kandidat_inhouse');

        $workflow = ApprovalWorkflow::with(['steps.stepUsers.user', 'steps.stepUsers.employee'])
            ->where('module', $module)
            ->first();

        if (!$workflow) {
            $workflow = ApprovalWorkflow::create([
                'module' => $module,
                'name' => 'Alur Approval Kandidat Inhouse',
                'description' => 'Alur approval bertingkat dinamis untuk kandidat inhouse 5 entitas resmi perusahaan.',
                'is_active' => true,
            ]);
        }

        // Daftar Entitas Inhouse Resmi
        $entities = [
            'AMK' => 'PT ARINA MULTI KARYA (AMK)',
            'AKP' => 'PT ALVA KARYA PERKASA (AKP)',
            'ATK' => 'PT ANUGRAH TERPERCAYA KERJA (ATK)',
            'ABO' => 'PT ABADI BERKAT ODELIA (ABO)',
            'ATB' => 'PT ANUGRAH TALENTA BERKARYA (ATB)',
        ];

        // Daftar Area Populer dari Master Area
        $areas = [
            'JAKARTA', 'SURABAYA', 'BANDUNG', 'SEMARANG', 'MEDAN', 
            'MAKASSAR', 'DENPASAR', 'PALEMBANG', 'BALIKPAPAN', 'YOGYAKARTA',
            'MALANG', 'BOGOR', 'BEKASI', 'TANGERANG', 'DEPOK'
        ];

        // Daftar Master Prinsiple untuk opsi pemilihan prinsiple spesifik
        // Pastikan seluruh Karyawan Inhouse Aktif dari Master Karyawan telah disinkronkan ke tabel users
        $this->ensureInhouseUsersExist();

        // Daftar Akun Pengguna Aktif untuk Pilihan Approver
        $availableUsers = User::where('is_active', true)
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'email', 'job_title', 'area', 'role']);

        return view('master.approval_workflow.index', compact(
            'workflow',
            'entities',
            'areas',
            'principles',
            'availableUsers'
        ));
    }

    /**
     * Helper untuk mengompilasi aturan dinamis (Area + Prinsiple + Users) dari input request
     */
    private function compileRulesFromRequest(Request $request): ?array
    {
        if ($request->approver_type !== 'user') {
            return null;
        }

        $rawRules = $request->input('rules');
        if (is_string($rawRules)) {
            $rawRules = json_decode($rawRules, true);
        }
        if (empty($rawRules) && $request->filled('rules_json')) {
            $rawRules = json_decode($request->input('rules_json'), true);
        }

        // Fallback jika hanya dikirim via parameter legacy user_ids[]
        if (empty($rawRules) && !empty($request->user_ids)) {
            $userIds = is_array($request->user_ids) ? $request->user_ids : explode(',', (string)$request->user_ids);
            $rawRules = [
                [
                    'id' => 'rule_' . uniqid(),
                    'area' => $request->area_scope ?: 'ALL',
                    'prinsiple' => $request->entity_scope ?: 'ALL',
                    'user_ids' => array_values(array_filter(array_map('intval', $userIds))),
                ]
            ];
        }

        if (empty($rawRules) || !is_array($rawRules)) {
            return null;
        }

        $compiled = [];
        foreach ($rawRules as $r) {
            $uIds = !empty($r['user_ids']) ? (is_array($r['user_ids']) ? $r['user_ids'] : explode(',', (string)$r['user_ids'])) : [];
            $uIds = array_values(array_filter(array_map('intval', $uIds)));

            $users = User::whereIn('id', $uIds)->get(['id', 'name', 'email', 'job_title', 'role']);

            $compiled[] = [
                'id' => $r['id'] ?? ('rule_' . uniqid()),
                'area' => $r['area'] ?? 'ALL',
                'prinsiple' => $r['prinsiple'] ?? 'ALL',
                'user_ids' => $uIds,
                'users' => $users->map(fn($u) => [
                    'id' => $u->id,
                    'name' => $u->name,
                    'email' => $u->email,
                    'job_title' => $u->job_title,
                    'role' => $u->role,
                ])->toArray(),
            ];
        }

        return !empty($compiled) ? $compiled : null;
    }

    /**
     * Simpan Step Approval Baru
     */
    public function storeStep(Request $request)
    {
        $request->validate([
            'workflow_id' => 'required|exists:approval_workflows,id',
            'step_name' => 'required|string|max:255',
            'approver_type' => 'required|in:head,user',
            'step_order' => 'required|integer|min:1',
            'area_scope' => 'nullable|string',
            'entity_scope' => 'nullable|string',
            'description' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $skipIfDireksi = $request->boolean('skip_if_direksi', $request->approver_type === 'head');
            $compiledRules = $this->compileRulesFromRequest($request);

            $step = ApprovalWorkflowStep::create([
                'workflow_id' => $request->workflow_id,
                'step_order' => (int)$request->step_order,
                'step_name' => trim($request->step_name),
                'approver_type' => $request->approver_type,
                'area_scope' => $request->area_scope ?: 'ALL',
                'entity_scope' => $request->entity_scope ?: 'ALL',
                'skip_if_direksi' => $skipIfDireksi,
                'approval_rules' => $compiledRules,
                'description' => $request->description,
            ]);

            // Sinkronisasi ke tabel pivot approval_workflow_step_users
            if ($request->approver_type === 'user' && !empty($compiledRules)) {
                foreach ($compiledRules as $cRule) {
                    $ruleArea = $cRule['area'] ?? 'ALL';
                    $rulePrin = $cRule['prinsiple'] ?? 'ALL';
                    foreach ($cRule['users'] as $u) {
                        ApprovalWorkflowStepUser::create([
                            'step_id' => $step->id,
                            'user_id' => $u['id'],
                            'area' => $ruleArea,
                            'prinsiple' => $rulePrin,
                            'user_name' => $u['name'],
                            'user_email' => $u['email'] ?? null,
                        ]);
                    }
                }
            }

            DB::commit();

            ActivityLogger::log('CREATE', 'Alur Approval', "Menambahkan step approval [{$step->step_name}] urutan ke-{$step->step_order}", $step);

            if ($request->wantsJson()) {
                return response()->json(['status' => 'success', 'message' => 'Step approval berhasil ditambahkan!']);
            }

            return back()->with('success', "Step approval [{$step->step_name}] berhasil ditambahkan!");
        } catch (\Throwable $e) {
            DB::rollBack();
            if ($request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', 'Gagal menambahkan step: ' . $e->getMessage());
        }
    }

    /**
     * Perbarui Step Approval
     */
    public function updateStep(Request $request, $id)
    {
        $step = ApprovalWorkflowStep::findOrFail($id);

        $request->validate([
            'step_name' => 'required|string|max:255',
            'approver_type' => 'required|in:head,user',
            'step_order' => 'required|integer|min:1',
            'area_scope' => 'nullable|string',
            'entity_scope' => 'nullable|string',
            'description' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $skipIfDireksi = $request->has('skip_if_direksi') 
                ? $request->boolean('skip_if_direksi') 
                : ($request->approver_type === 'head');

            $compiledRules = $this->compileRulesFromRequest($request);

            $step->update([
                'step_order' => (int)$request->step_order,
                'step_name' => trim($request->step_name),
                'approver_type' => $request->approver_type,
                'area_scope' => $request->area_scope ?: 'ALL',
                'entity_scope' => $request->entity_scope ?: 'ALL',
                'skip_if_direksi' => $skipIfDireksi,
                'approval_rules' => $compiledRules,
                'description' => $request->description,
            ]);

            // Sinkronisasi Approver Users
            $step->stepUsers()->delete();

            if ($request->approver_type === 'user' && !empty($compiledRules)) {
                foreach ($compiledRules as $cRule) {
                    $ruleArea = $cRule['area'] ?? 'ALL';
                    $rulePrin = $cRule['prinsiple'] ?? 'ALL';
                    foreach ($cRule['users'] as $u) {
                        ApprovalWorkflowStepUser::create([
                            'step_id' => $step->id,
                            'user_id' => $u['id'],
                            'area' => $ruleArea,
                            'prinsiple' => $rulePrin,
                            'user_name' => $u['name'],
                            'user_email' => $u['email'] ?? null,
                        ]);
                    }
                }
            }

            DB::commit();

            ActivityLogger::log('UPDATE', 'Alur Approval', "Memperbarui step approval [{$step->step_name}]", $step);

            if ($request->wantsJson()) {
                return response()->json(['status' => 'success', 'message' => 'Step approval berhasil diperbarui!']);
            }

            return back()->with('success', "Step approval [{$step->step_name}] berhasil diperbarui!");
        } catch (\Throwable $e) {
            DB::rollBack();
            if ($request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', 'Gagal memperbarui step: ' . $e->getMessage());
        }
    }

    /**
     * Hapus Step Approval
     */
    public function destroyStep($id)
    {
        $step = ApprovalWorkflowStep::findOrFail($id);
        $name = $step->step_name;

        DB::beginTransaction();
        try {
            $step->delete();

            // Re-order urutan sisa step agar berurutan kembali
            $remainingSteps = ApprovalWorkflowStep::where('workflow_id', $step->workflow_id)
                ->orderBy('step_order', 'asc')
                ->get();

            foreach ($remainingSteps as $index => $s) {
                $s->update(['step_order' => $index + 1]);
            }

            DB::commit();

            ActivityLogger::log('DELETE', 'Alur Approval', "Menghapus step approval [{$name}]", $step);

            return back()->with('success', "Step approval [{$name}] berhasil dihapus.");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus step: ' . $e->getMessage());
        }
    }

    /**
     * Mengatur ulang urutan step approval (AJAX drag/order)
     */
    public function reorderSteps(Request $request)
    {
        $request->validate([
            'step_ids' => 'required|array',
            'step_ids.*' => 'integer|exists:approval_workflow_steps,id',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->step_ids as $order => $stepId) {
                ApprovalWorkflowStep::where('id', $stepId)->update([
                    'step_order' => $order + 1,
                ]);
            }

            DB::commit();

            return response()->json(['status' => 'success', 'message' => 'Urutan step approval berhasil diperbarui!']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Endpoint API pencarian pengguna untuk approver (autocomplete)
     */
    public function searchApprovers(Request $request)
    {
        $this->ensureInhouseUsersExist();

        $query = trim($request->input('q', ''));
        $words = array_filter(explode(' ', $query));

        $users = User::where('is_active', true)
            ->when(!empty($words), function ($q) use ($words) {
                foreach ($words as $w) {
                    $q->where(function ($sub) use ($w) {
                        $sub->where('name', 'like', "%{$w}%")
                            ->orWhere('email', 'like', "%{$w}%")
                            ->orWhere('job_title', 'like', "%{$w}%")
                            ->orWhere('area', 'like', "%{$w}%");
                    });
                }
            })
            ->orderBy('name', 'asc')
            ->limit(50)
            ->get(['id', 'name', 'email', 'job_title', 'role', 'area']);

        return response()->json($users);
    }

    /**
     * Memastikan seluruh Karyawan Inhouse Aktif dari Master Karyawan telah disinkronkan ke tabel users
     */
    protected function ensureInhouseUsersExist(): void
    {
        $missingInhouse = Employee::where('status', 'Aktiv')
            ->where(function ($q) {
                $q->where('tipe_karyawan', 'Inhouse')
                  ->orWhereRaw('LOWER(TRIM(tipe_karyawan)) = ?', ['inhouse']);
            })
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->whereNotIn('email', User::pluck('email'))
            ->get();

        if ($missingInhouse->isEmpty()) {
            return;
        }

        foreach ($missingInhouse as $emp) {
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

            User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => trim($emp->nama_karyawan),
                    'password' => $emp->password ?: \Illuminate\Support\Facades\Hash::make($emp->default_password ?: 'password'),
                    'role' => $role,
                    'area' => $emp->area,
                    'job_title' => $emp->jabatan,
                    'phone' => $emp->telepon,
                    'is_active' => true,
                ]
            );
        }
    }
}
