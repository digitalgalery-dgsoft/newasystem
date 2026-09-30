<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $hasCandidates = Schema::hasTable('candidates');
        $hasTbKandidat = Schema::hasTable('tb_kandidat');
        $hasUsers = Schema::hasTable('users');
        $hasEmployees = Schema::hasTable('employees');

        if (!$hasCandidates && !$hasTbKandidat) {
            return;
        }

        // -------------------------------------------------------------------------
        // 1. Normalisasi Alias Email AS ke Email Resmi Kanonikal & Recruiter ID
        // -------------------------------------------------------------------------
        $asAliasMap = [
            'valenringo@gmail.com' => [
                'canonical_email' => 'valen.ringo@gmail.com',
                'name' => 'Valentina Siringo Ringo',
                'area' => 'Samarinda',
                'user_id' => 78,
            ],
            'marshelwilhelmuss22@gmail.com' => [
                'canonical_email' => 'marshelwilhelmus22@gmail.com',
                'name' => 'Marshel Wilhelmus Pinem',
                'area' => 'Medan',
                'user_id' => 285,
            ],
            'titin.trianingsih333@gmail.com' => [
                'canonical_email' => 'aro.surabaya1@alvakaryaperkasa.co.id',
                'name' => 'Titin Trianingsih',
                'area' => 'Surabaya',
                'user_id' => 37,
            ],
            'lestarialvakaryaperkasa@gmail.com' => [
                'canonical_email' => 'Lestari.arina.sby@gmail.com',
                'name' => 'Lestari Meiningsih',
                'area' => 'Surabaya',
                'user_id' => 51,
            ],
            'arumi.surabaya@gmail.com' => [
                'canonical_email' => 'Lestari.arina.sby@gmail.com',
                'name' => 'Lestari Meiningsih',
                'area' => 'Surabaya',
                'user_id' => 51,
            ],
            'christonormandebby91@gmail.com' => [
                'canonical_email' => 'christonormandebby@gmail.com',
                'name' => 'Christo Norman Debby Fredrick',
                'area' => 'Bandung',
                'user_id' => 211,
            ],
            'krt.kartikasari@gmail.com' => [
                'canonical_email' => 'adm.kediri@arina.co.id',
                'name' => 'Kartika Sari',
                'area' => 'Kediri',
                'user_id' => 89,
            ],
        ];

        foreach ($asAliasMap as $alias => $info) {
            $aliasLower = strtolower(trim($alias));
            $canonicalEmail = strtolower(trim($info['canonical_email']));
            $userId = null;

            if ($hasUsers) {
                // Cari user berdasarkan ID atau Email resmi
                $user = null;
                if (!empty($info['user_id'])) {
                    $user = DB::table('users')->where('id', $info['user_id'])->first();
                }
                if (!$user) {
                    $user = DB::table('users')->whereRaw('LOWER(TRIM(email)) = ?', [$canonicalEmail])->first();
                }
                if (!$user && !empty($info['name'])) {
                    $user = DB::table('users')->whereRaw('LOWER(TRIM(name)) LIKE ?', ['%' . strtolower(trim($info['name'])) . '%'])->first();
                }

                if ($user) {
                    $userId = $user->id;
                    $canonicalEmail = strtolower(trim($user->email));

                    // Simpan alias ke kolom email_aliases pada tabel users
                    if (Schema::hasColumn('users', 'email_aliases')) {
                        $aliases = [];
                        if (!empty($user->email_aliases)) {
                            $dec = json_decode($user->email_aliases, true);
                            if (is_array($dec)) $aliases = $dec;
                        }
                        if (!in_array($aliasLower, $aliases, true)) {
                            $aliases[] = $aliasLower;
                            DB::table('users')->where('id', $user->id)->update([
                                'email_aliases' => json_encode(array_values(array_unique(array_filter($aliases)))),
                            ]);
                        }
                    }
                }
            }

            // Update di tabel candidates
            if ($hasCandidates) {
                $candUpdate = ['useras' => $canonicalEmail];
                if ($userId !== null) {
                    $candUpdate['recruiter_id'] = $userId;
                }
                DB::table('candidates')
                    ->whereRaw('LOWER(TRIM(useras)) = ?', [$aliasLower])
                    ->update($candUpdate);
            }

            // Update di tabel tb_kandidat jika ada
            if ($hasTbKandidat && Schema::hasColumn('tb_kandidat', 'nama_as')) {
                $kandUpdate = ['nama_as' => $canonicalEmail];
                if ($userId !== null && Schema::hasColumn('tb_kandidat', 'recruiter_id')) {
                    $kandUpdate['recruiter_id'] = $userId;
                }
                DB::table('tb_kandidat')
                    ->whereRaw('LOWER(TRIM(nama_as)) = ?', [$aliasLower])
                    ->update($kandUpdate);
            }
        }

        // -------------------------------------------------------------------------
        // 2. Eliminasi Kandidat under "Administrator ESA" / admin@asystem.co.id
        //    Petakan ke User AS sesuai Cabang/Area masing-masing kandidat
        // -------------------------------------------------------------------------
        if ($hasCandidates) {
            $adminCandidates = DB::table('candidates')
                ->where(function ($q) {
                    $q->whereIn(DB::raw('LOWER(TRIM(useras))'), ['admin@asystem.co.id', 'administrator', 'administrator esa'])
                      ->orWhere('recruiter_id', 1);
                })
                ->get();

            foreach ($adminCandidates as $cand) {
                $candArea = trim($cand->area ?: ($cand->penempatan ?? ''));
                $targetAsUser = null;

                if (!empty($candArea) && $candArea !== '-' && strtolower($candArea) !== 'indonesia') {
                    $candAreaLower = strtolower($candArea);

                    // 1. Cek User AS spesifik berdasarkan nama cabang umum
                    if ($candAreaLower === 'semarang') {
                        $targetAsUser = DB::table('users')->where('id', 75)->orWhere('name', 'like', '%Setiawan Budi%')->first();
                    } elseif ($candAreaLower === 'malang') {
                        $targetAsUser = DB::table('users')->where('id', 160)->orWhere('name', 'like', '%Enfatika%')->first();
                    } elseif ($candAreaLower === 'surabaya') {
                        $targetAsUser = DB::table('users')->where('id', 51)->orWhere('name', 'like', '%Lestari Meiningsih%')->first();
                    }

                    // 2. Jika belum ketemu, cari di tabel users berdasarkan Area
                    if (!$targetAsUser && $hasUsers) {
                        $targetAsUser = DB::table('users')
                            ->whereRaw('LOWER(TRIM(area)) = ?', [$candAreaLower])
                            ->where('id', '!=', 1)
                            ->where('email', '!=', 'admin@asystem.co.id')
                            ->where('role', '!=', 'admin')
                            ->where(function($q) {
                                $q->where('job_title', 'like', '%AS OPS%')
                                  ->orWhere('role', 'recruiter')
                                  ->orWhere('job_title', 'like', '%RECRUIT%');
                            })
                            ->orderBy('id', 'asc')
                            ->first();
                    }

                    // 3. Jika belum ketemu, cari di Employee yang memiliki email terdaftar di users
                    if (!$targetAsUser && $hasEmployees && $hasUsers) {
                        $asEmp = DB::table('employees')
                            ->whereRaw('LOWER(TRIM(area)) = ?', [$candAreaLower])
                            ->where(function($q) {
                                $q->where('jabatan', 'like', '%AS OPS%')
                                  ->orWhere('jabatan', 'like', '%RECRUIT%');
                            })
                            ->whereRaw("CASE WHEN status = 'Aktiv' THEN 0 ELSE 1 END = 0")
                            ->first();
                        if ($asEmp && !empty($asEmp->email)) {
                            $targetAsUser = DB::table('users')
                                ->whereRaw('LOWER(TRIM(email)) = ?', [strtolower(trim($asEmp->email))])
                                ->first();
                        }
                    }
                }

                if ($targetAsUser) {
                    DB::table('candidates')->where('id', $cand->id)->update([
                        'useras' => strtolower(trim($targetAsUser->email)),
                        'recruiter_id' => $targetAsUser->id,
                    ]);
                } else {
                    // Jika tidak ditemukan AS cabang, alihkan ke 'Publik' dan lepas recruiter_id admin
                    DB::table('candidates')->where('id', $cand->id)->update([
                        'useras' => 'Publik',
                        'recruiter_id' => null,
                    ]);
                }
            }
        }

        if ($hasTbKandidat && Schema::hasColumn('tb_kandidat', 'nama_as')) {
            DB::table('tb_kandidat')
                ->whereIn(DB::raw('LOWER(TRIM(nama_as))'), ['admin@asystem.co.id', 'administrator', 'administrator esa'])
                ->update(['nama_as' => 'Publik']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback needed for data sanitation
    }
};
