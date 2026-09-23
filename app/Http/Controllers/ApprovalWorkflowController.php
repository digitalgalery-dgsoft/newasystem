<?php

namespace App\Http\Controllers;

use App\Models\ApprovalWorkflow;
use App\Models\ApprovalWorkflowStep;
use App\Models\ApprovalWorkflowStepUser;
use App\Models\Employee;
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

        // Daftar Akun Pengguna Aktif untuk Pilihan Approver
        $availableUsers = User::where('is_active', true)
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'email', 'job_title', 'area', 'role']);

        return view('master.approval_workflow.index', compact(
            'workflow',
            'entities',
            'areas',
            'availableUsers'
        ));
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
            'area_scope' => 'required|string',
            'entity_scope' => 'required|string',
            'description' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $skipIfDireksi = $request->boolean('skip_if_direksi', $request->approver_type === 'head');

            $step = ApprovalWorkflowStep::create([
                'workflow_id' => $request->workflow_id,
                'step_order' => (int)$request->step_order,
                'step_name' => trim($request->step_name),
                'approver_type' => $request->approver_type,
                'area_scope' => $request->area_scope,
                'entity_scope' => $request->entity_scope,
                'skip_if_direksi' => $skipIfDireksi,
                'description' => $request->description,
            ]);

            // Jika tipe user: hubungkan user-user yang dipilih (Multiple Users)
            if ($request->approver_type === 'user' && !empty($request->user_ids)) {
                $userIds = is_array($request->user_ids) ? $request->user_ids : explode(',', (string)$request->user_ids);
                $users = User::whereIn('id', array_filter($userIds))->get();

                foreach ($users as $u) {
                    ApprovalWorkflowStepUser::create([
                        'step_id' => $step->id,
                        'user_id' => $u->id,
                        'user_name' => $u->name,
                        'user_email' => $u->email,
                    ]);
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
            'area_scope' => 'required|string',
            'entity_scope' => 'required|string',
            'description' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $skipIfDireksi = $request->has('skip_if_direksi') 
                ? $request->boolean('skip_if_direksi') 
                : ($request->approver_type === 'head');

            $step->update([
                'step_order' => (int)$request->step_order,
                'step_name' => trim($request->step_name),
                'approver_type' => $request->approver_type,
                'area_scope' => $request->area_scope,
                'entity_scope' => $request->entity_scope,
                'skip_if_direksi' => $skipIfDireksi,
                'description' => $request->description,
            ]);

            // Sinkronisasi Approver Users
            if ($request->approver_type === 'user') {
                $step->stepUsers()->delete();

                if (!empty($request->user_ids)) {
                    $userIds = is_array($request->user_ids) ? $request->user_ids : explode(',', (string)$request->user_ids);
                    $users = User::whereIn('id', array_filter($userIds))->get();

                    foreach ($users as $u) {
                        ApprovalWorkflowStepUser::create([
                            'step_id' => $step->id,
                            'user_id' => $u->id,
                            'user_name' => $u->name,
                            'user_email' => $u->email,
                        ]);
                    }
                }
            } else {
                // Jika tipe diubah ke head, kosongkan daftar akun spesifik
                $step->stepUsers()->delete();
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
            DB::commit();

            ActivityLogger::log('DELETE', 'Alur Approval', "Menghapus step approval [{$name}]", $step);

            return back()->with('success', "Step approval [{$name}] berhasil dihapus!");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus step: ' . $e->getMessage());
        }
    }

    /**
     * Ubah Urutan Step (Reorder Up / Down)
     */
    public function reorderSteps(Request $request)
    {
        $request->validate([
            'step_ids' => 'required|array',
            'step_ids.*' => 'integer|exists:approval_workflow_steps,id',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->step_ids as $index => $id) {
                ApprovalWorkflowStep::where('id', $id)->update(['step_order' => $index + 1]);
            }
            DB::commit();

            return response()->json(['status' => 'success', 'message' => 'Urutan step approval berhasil diperbarui!']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * API Pencarian User / Karyawan untuk Select Approver
     */
    public function searchApprovers(Request $request)
    {
        $q = trim($request->query('q', ''));
        if (empty($q)) {
            $users = User::where('is_active', true)->limit(20)->get(['id', 'name', 'email', 'job_title', 'area']);
        } else {
            $users = User::where('is_active', true)
                ->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('job_title', 'like', "%{$q}%")
                        ->orWhere('area', 'like', "%{$q}%");
                })
                ->limit(30)
                ->get(['id', 'name', 'email', 'job_title', 'area']);
        }

        return response()->json($users);
    }
}
