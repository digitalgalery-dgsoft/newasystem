<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Employee;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Penyelarasan menyeluruh job_specs, candidates, tb_kandidat, dan riwayat interview
     * setelah pengguna karyawan inhouse beralih ke email corporate.
     */
    public function up(): void
    {
        try {
            $users = User::all();

            foreach ($users as $user) {
                $currentEmail = strtolower(trim($user->email ?? ''));
                if (empty($currentEmail)) {
                    continue;
                }

                $aliases = is_array($user->email_aliases) 
                    ? $user->email_aliases 
                    : (is_string($user->email_aliases) ? json_decode($user->email_aliases, true) : []);
                if (!is_array($aliases)) {
                    $aliases = [];
                }

                // Cari data karyawan terhubung jika ada email lama di master employee
                $linkedEmployee = $user->linked_employee ?? null;
                if (!$linkedEmployee && (!empty($user->name) || !empty($currentEmail))) {
                    $linkedEmployee = Employee::where('email', $currentEmail)
                        ->orWhere('nama_karyawan', $user->name)
                        ->first();
                }

                if ($linkedEmployee && !empty($linkedEmployee->email)) {
                    $empEmail = strtolower(trim($linkedEmployee->email));
                    if ($empEmail !== $currentEmail && !in_array($empEmail, $aliases, true)) {
                        $aliases[] = $empEmail;
                    }
                }

                // Normalisasi daftar alias historis
                $cleanAliases = array_values(array_unique(array_filter(array_map('strtolower', array_map('trim', $aliases)))));
                $aliasExcludingCurrent = array_values(array_filter($cleanAliases, fn($a) => $a !== $currentEmail));

                // Simpan alias yang baru jika bertambah
                if (count($cleanAliases) !== (is_array($user->email_aliases) ? count($user->email_aliases) : 0)) {
                    $user->email_aliases = $cleanAliases;
                    $user->saveQuietly();
                }

                // Daftar pencocokan identitas lama (alias email lama + nama user)
                $oldIdentities = $aliasExcludingCurrent;
                if (!empty($user->name) && strlen($user->name) >= 4) {
                    $oldIdentities[] = strtolower(trim($user->name));
                }

                if (!empty($oldIdentities)) {
                    // 1. Selaraskan tabel job_specs (Lowongan Pekerjaan)
                    if (Schema::hasTable('job_specs')) {
                        DB::table('job_specs')
                            ->whereIn(DB::raw('LOWER(TRIM(created_by))'), $oldIdentities)
                            ->update(['created_by' => $user->email]);
                    }

                    // 2. Selaraskan tabel candidates (useras & recruiter_id)
                    if (Schema::hasTable('candidates')) {
                        DB::table('candidates')
                            ->whereIn(DB::raw('LOWER(TRIM(useras))'), $oldIdentities)
                            ->orWhere(function ($q) use ($user, $currentEmail) {
                                $q->where('recruiter_id', $user->id)
                                  ->whereRaw('LOWER(TRIM(useras)) != ?', [$currentEmail]);
                            })
                            ->update([
                                'useras' => $user->email,
                                'recruiter_id' => $user->id,
                            ]);
                    }

                    // 3. Selaraskan tabel legacy tb_kandidat
                    if (Schema::hasTable('tb_kandidat')) {
                        DB::table('tb_kandidat')
                            ->whereIn(DB::raw('LOWER(TRIM(useras))'), $oldIdentities)
                            ->update([
                                'useras' => $user->email,
                                'nama_as' => $user->name,
                            ]);
                    }

                    // 4. Selaraskan riwayat assessment interview jika ada
                    if (Schema::hasTable('interview_assessments')) {
                        DB::table('interview_assessments')
                            ->whereIn(DB::raw('LOWER(TRIM(interviewer))'), $aliasExcludingCurrent)
                            ->update(['interviewer' => $user->email]);
                    }

                    // 5. Selaraskan candidate logs jika ada
                    if (Schema::hasTable('candidate_logs')) {
                        DB::table('candidate_logs')
                            ->whereIn(DB::raw('LOWER(TRIM("user"))'), $aliasExcludingCurrent)
                            ->update(['user' => $user->email]);
                    }
                }

                // Pastikan seluruh kandidat yang sudah memiliki recruiter_id user ini memiliki useras email aktif
                if (Schema::hasTable('candidates')) {
                    DB::table('candidates')
                        ->where('recruiter_id', $user->id)
                        ->where(function ($q) use ($currentEmail) {
                            $q->whereNull('useras')
                              ->orWhereRaw('LOWER(TRIM(useras)) != ?', [$currentEmail]);
                        })
                        ->update(['useras' => $user->email]);
                }
            }

            // 6. Pencocokan tambahan: Selaraskan job_specs yang dibuat memakai email karyawan di master employee
            if (Schema::hasTable('job_specs') && Schema::hasTable('employees')) {
                $unresolvedJobs = DB::table('job_specs')
                    ->select('created_by')
                    ->distinct()
                    ->whereNotNull('created_by')
                    ->where('created_by', '!=', '')
                    ->get();

                foreach ($unresolvedJobs as $uj) {
                    $creatorRaw = strtolower(trim($uj->created_by));
                    if (str_starts_with($creatorRaw, 'admin@') || $creatorRaw === 'administrator') {
                        continue;
                    }

                    // Cek apakah creatorRaw adalah email dari seorang employee yang memiliki akun User
                    $emp = Employee::whereRaw('LOWER(TRIM(email)) = ?', [$creatorRaw])->first();
                    if ($emp && !empty($emp->nama_karyawan)) {
                        $matchedUser = User::whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($emp->nama_karyawan))])
                            ->orWhere('id', $emp->user_id ?? null)
                            ->first();

                        if ($matchedUser && !empty($matchedUser->email) && strtolower(trim($matchedUser->email)) !== $creatorRaw) {
                            $matchedUser->addEmailAlias($creatorRaw);

                            DB::table('job_specs')
                                ->whereRaw('LOWER(TRIM(created_by)) = ?', [$creatorRaw])
                                ->update(['created_by' => $matchedUser->email]);

                            DB::table('candidates')
                                ->whereRaw('LOWER(TRIM(useras)) = ?', [$creatorRaw])
                                ->update([
                                    'useras' => $matchedUser->email,
                                    'recruiter_id' => $matchedUser->id,
                                ]);
                        }
                    }
                }
            }

            Log::info('Migrasi penyelarasan job_specs dan candidates pasca perubahan email corporate selesai.');
        } catch (\Throwable $e) {
            Log::error('Migrasi heal_job_specs_and_candidates error: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No destructive reverse needed
    }
};
