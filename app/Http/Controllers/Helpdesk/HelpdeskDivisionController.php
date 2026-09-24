<?php

namespace App\Http\Controllers\Helpdesk;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\HelpdeskDivision;
use App\Models\HelpdeskDivisionAgent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class HelpdeskDivisionController extends Controller
{
    /**
     * Tampilkan Pengaturan Divisi & Daftar Agen Inhouse
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        $divisions = HelpdeskDivision::with(['agents'])
            ->withCount(['tickets', 'divisionAgents'])
            ->orderBy('name', 'asc')
            ->get();

        // Ambil Pengguna Aktif (Karyawan Inhouse) untuk pilihan penugasan agen
        $inhouseUsers = User::where('is_active', true)
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'email', 'job_title', 'area']);

        return view('helpdesk.divisions.index', compact('divisions', 'inhouseUsers', 'user'));
    }

    /**
     * Tambah Divisi Baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:helpdesk_divisions,code',
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
            'color' => 'nullable|string|in:blue,indigo,emerald,amber,rose,purple',
            'wa_group_id' => 'nullable|string|max:100',
            'wa_group_link' => 'nullable|string|max:255',
            'sla_hours' => 'nullable|integer|min:1',
        ]);

        $division = HelpdeskDivision::create([
            'name' => trim($request->input('name')),
            'code' => strtoupper(trim($request->input('code'))),
            'description' => trim($request->input('description')),
            'icon' => $request->input('icon', 'fa-solid fa-headset'),
            'color' => $request->input('color', 'blue'),
            'wa_group_id' => trim($request->input('wa_group_id')),
            'wa_group_link' => trim($request->input('wa_group_link')),
            'sla_hours' => $request->input('sla_hours', 24),
            'is_active' => true,
        ]);

        return redirect()->route('helpdesk.divisions.index')
            ->with('success', "Divisi '{$division->name}' berhasil ditambahkan!");
    }

    /**
     * Perbarui Divisi
     */
    public function update(Request $request, $id)
    {
        $division = HelpdeskDivision::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100',
            'code' => "required|string|max:20|unique:helpdesk_divisions,code,{$id}",
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
            'color' => 'nullable|string|in:blue,indigo,emerald,amber,rose,purple',
            'wa_group_id' => 'nullable|string|max:100',
            'wa_group_link' => 'nullable|string|max:255',
            'sla_hours' => 'nullable|integer|min:1',
            'is_active' => 'nullable|boolean',
        ]);

        $division->update([
            'name' => trim($request->input('name')),
            'code' => strtoupper(trim($request->input('code'))),
            'description' => trim($request->input('description')),
            'icon' => $request->input('icon', $division->icon),
            'color' => $request->input('color', $division->color),
            'wa_group_id' => trim($request->input('wa_group_id')),
            'wa_group_link' => trim($request->input('wa_group_link')),
            'sla_hours' => $request->input('sla_hours', 24),
            'is_active' => $request->has('is_active') ? (bool)$request->input('is_active') : true,
        ]);

        return redirect()->route('helpdesk.divisions.index')
            ->with('success', "Divisi '{$division->name}' berhasil diperbarui!");
    }

    /**
     * Hapus Divisi
     */
    public function destroy($id)
    {
        $division = HelpdeskDivision::findOrFail($id);

        if ($division->tickets()->count() > 0) {
            // Jika ada tiket, nonaktifkan saja untuk menjaga integritas data
            $division->update(['is_active' => false]);
            return redirect()->route('helpdesk.divisions.index')
                ->with('success', "Divisi '{$division->name}' memiliki riwayat tiket, sehingga statusnya diubah menjadi Non-Aktif.");
        }

        $division->delete();

        return redirect()->route('helpdesk.divisions.index')
            ->with('success', "Divisi berhasil dihapus.");
    }

    /**
     * Tambah Agen / Karyawan Inhouse ke Divisi
     */
    public function addAgent(Request $request, $divisionId)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'is_lead' => 'nullable|boolean',
        ]);

        $division = HelpdeskDivision::findOrFail($divisionId);
        $userId = $request->input('user_id');

        HelpdeskDivisionAgent::firstOrCreate(
            ['division_id' => $division->id, 'user_id' => $userId],
            ['is_lead' => (bool)$request->input('is_lead', false), 'is_auto_assign' => true]
        );

        $user = User::find($userId);

        return redirect()->route('helpdesk.divisions.index')
            ->with('success', "Karyawan '{$user->name}' berhasil ditugaskan sebagai agen divisi {$division->name}.");
    }

    /**
     * Hapus Agen dari Divisi
     */
    public function removeAgent($divisionId, $userId)
    {
        HelpdeskDivisionAgent::where('division_id', $divisionId)
            ->where('user_id', $userId)
            ->delete();

        return redirect()->route('helpdesk.divisions.index')
            ->with('success', "Agen berhasil dihapus dari divisi.");
    }

    /**
     * Sinkronisasi Otomatis Divisi Berdasarkan Master Karyawan Inhouse
     */
    public function syncFromInhouse()
    {
        // 1. Template Divisi Standar ESA Groups
        $standardDivisions = [
            [
                'name' => 'IT Support & Sistem ASystem',
                'code' => 'IT',
                'description' => 'Layanan bantuan teknis aplikasi, login akun, perbaikan bug, dan database.',
                'icon' => 'fa-solid fa-laptop-code',
                'color' => 'blue',
                'sla_hours' => 12,
                'keywords' => ['it', 'database', 'system', 'programmer', 'edp'],
            ],
            [
                'name' => 'Operasional (OPS)',
                'code' => 'OPS',
                'description' => 'Kendala operasional lapangan, area kerja, SPK, dan alur operasional cabang.',
                'icon' => 'fa-solid fa-gears',
                'color' => 'indigo',
                'sla_hours' => 24,
                'keywords' => ['ops', 'operasional', 'admin ops', 'ae ops', 'as ops', 'sam ops'],
            ],
            [
                'name' => 'General Affairs (GA)',
                'code' => 'GA',
                'description' => 'Pengadaan fasilitas kantor, perbaikan sarana, inventaris, dan kebersihan/keamanan.',
                'icon' => 'fa-solid fa-building',
                'color' => 'amber',
                'sla_hours' => 24,
                'keywords' => ['ga', 'general affair', 'security', 'driver', 'kurir', 'kebersihan'],
            ],
            [
                'name' => 'HRD & Personalia',
                'code' => 'HRD',
                'description' => 'Administrasi kekaryawanan, absensi, klaim BPJS, kontrak kerja, dan mutasi.',
                'icon' => 'fa-solid fa-users',
                'color' => 'rose',
                'sla_hours' => 24,
                'keywords' => ['hrd', 'personalia', 'hr', 'payroll', 'recruitment', 'recruiter'],
            ],
            [
                'name' => 'Finance, Accounting & Tax',
                'code' => 'FAT',
                'description' => 'Pertanyaan reimbursement, invoice, tagihan operasional, kasbon, dan perpajakan.',
                'icon' => 'fa-solid fa-money-check-dollar',
                'color' => 'emerald',
                'sla_hours' => 48,
                'keywords' => ['tax', 'acc', 'accounting', 'finance', 'budget', 'ar', 'ap', 'kasir'],
            ],
            [
                'name' => 'Legal & Compliance',
                'code' => 'LGL',
                'description' => 'Pemeriksaan perjanjian kerja sama, perizinan, sengketa, dan kepatuhan regulasi.',
                'icon' => 'fa-solid fa-scale-balanced',
                'color' => 'purple',
                'sla_hours' => 48,
                'keywords' => ['legal', 'compliance', 'hukum', 'advokat'],
            ],
        ];

        $createdCount = 0;
        $mappedAgentsCount = 0;

        foreach ($standardDivisions as $std) {
            $keywords = $std['keywords'];
            unset($std['keywords']);

            $division = HelpdeskDivision::firstOrCreate(
                ['code' => $std['code']],
                $std
            );

            if ($division->wasRecentlyCreated) {
                $createdCount++;
            }

            // Petakan Karyawan Inhouse ke Divisi Berdasarkan Jabatan / Divisi di Master Karyawan
            $matchingEmployees = Employee::where('tipe_karyawan', 'Inhouse')
                ->where('status', 'Aktiv')
                ->where(function ($q) use ($keywords) {
                    foreach ($keywords as $kw) {
                        $q->orWhere('jabatan', 'like', "%{$kw}%")
                          ->orWhere('divisi', 'like', "%{$kw}%");
                    }
                })
                ->get();

            foreach ($matchingEmployees as $emp) {
                // Temukan user account
                $user = User::where('email', $emp->email)
                    ->orWhere('nik', $emp->nik)
                    ->first();

                if ($user) {
                    $agent = HelpdeskDivisionAgent::firstOrCreate(
                        ['division_id' => $division->id, 'user_id' => $user->id],
                        ['is_lead' => false, 'is_auto_assign' => true]
                    );
                    if ($agent->wasRecentlyCreated) {
                        $mappedAgentsCount++;
                    }
                }
            }
        }

        return redirect()->route('helpdesk.divisions.index')
            ->with('success', "Sinkronisasi Divisi Inhouse Selesai! {$createdCount} divisi baru dibuat, dan {$mappedAgentsCount} penugasan agen karyawan inhouse berhasil dipetakan.");
    }
}
