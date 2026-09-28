<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tambah kolom email_aliases pada tabel users jika belum ada
        if (!Schema::hasColumn('users', 'email_aliases')) {
            Schema::table('users', function (Blueprint $table) {
                $table->json('email_aliases')->nullable()->after('email');
            });
        }

        $hasTbKandidat = Schema::hasTable('tb_kandidat');

        // 2. Daftar akun AS yang baru saja mengganti email di profile dan kandidatnya tertinggal
        $knownReplacements = [
            // User 211: Christo Norman Debby Fredrick
            [
                'user_id'   => 211,
                'new_email' => 'christonormandebby@gmail.com',
                'old_email' => 'christonormandebby91@gmail.com',
                'name'      => 'Christo Norman Debby Fredrick',
            ],
            // User 89: KARTIKA SARI
            [
                'user_id'   => 89,
                'new_email' => 'adm.kediri@arina.co.id',
                'old_email' => 'krt.kartikasari@gmail.com',
                'name'      => 'KARTIKA SARI',
            ],
            // User 172: CANNY AMERILYSE CAESAR
            [
                'user_id'   => 172,
                'new_email' => 'cannyamerilysecaesar@gmail.com',
                'old_email' => 'camerilyse@outlook.com',
                'name'      => 'CANNY AMERILYSE CAESAR',
            ],
            // User 215: Ary Ervina
            [
                'user_id'   => 215,
                'new_email' => 'ervinaary7@gmail.com',
                'old_email' => 'ervinaary@gmail.com',
                'name'      => 'Ary Ervina',
            ],
            // User 182: MAULIDA VINA SABILA NISA TABA
            [
                'user_id'   => 182,
                'new_email' => 'maulidavinasabilanisataba@gmail.com',
                'old_email' => 'maulid12991@gmail.com',
                'name'      => 'MAULIDA VINA SABILA NISA TABA',
            ],
            // User 123: Tytho Viandhika Pratama
            [
                'user_id'   => 123,
                'new_email' => 'viandhikatytho@gmail.com',
                'old_email' => 'viandhikatytho123@gmail.com',
                'name'      => 'Tytho Viandhika Pratama',
            ],
        ];

        foreach ($knownReplacements as $rep) {
            $user = DB::table('users')->where('id', $rep['user_id'])->first();
            if (!$user) {
                $user = DB::table('users')->whereRaw('LOWER(TRIM(email)) = ?', [strtolower(trim($rep['new_email']))])->first();
            }

            $userId = $user ? $user->id : null;
            $newEmail = $user ? strtolower(trim($user->email)) : strtolower(trim($rep['new_email']));
            $oldEmail = strtolower(trim($rep['old_email']));

            // Simpan alias ke user jika user ditemukan di database
            if ($user) {
                $existingAliases = [];
                if (!empty($user->email_aliases)) {
                    $decoded = json_decode($user->email_aliases, true);
                    if (is_array($decoded)) {
                        $existingAliases = $decoded;
                    }
                }
                if (!in_array($oldEmail, $existingAliases, true)) {
                    $existingAliases[] = $oldEmail;
                    DB::table('users')->where('id', $user->id)->update([
                        'email_aliases' => json_encode(array_values(array_unique(array_filter($existingAliases)))),
                    ]);
                }
            }

            // Update kandidat di tabel candidates
            $candUpdate = ['useras' => $newEmail];
            if ($userId !== null) {
                $candUpdate['recruiter_id'] = $userId;
            }

            DB::table('candidates')
                ->whereRaw('LOWER(TRIM(useras)) = ?', [$oldEmail])
                ->update($candUpdate);

            if (!empty($rep['name'])) {
                DB::table('candidates')
                    ->whereRaw('LOWER(TRIM(useras)) = ?', [strtolower(trim($rep['name']))])
                    ->update($candUpdate);
            }

            // Update tb_kandidat
            if ($hasTbKandidat) {
                DB::table('tb_kandidat')
                    ->whereRaw('LOWER(TRIM(useras)) = ?', [$oldEmail])
                    ->update([
                        'useras' => $newEmail,
                    ]);
            }
        }

        // 3. Selaraskan seluruh kandidat yang memiliki useras sesuai email user aktif agar memiliki recruiter_id
        $users = DB::table('users')->select('id', 'name', 'email')->get();
        foreach ($users as $u) {
            $uEmail = strtolower(trim($u->email));
            $uName = strtolower(trim($u->name));

            if (!empty($uEmail)) {
                DB::table('candidates')
                    ->whereRaw('LOWER(TRIM(useras)) = ?', [$uEmail])
                    ->whereNull('recruiter_id')
                    ->update(['recruiter_id' => $u->id]);
            }

            if (!empty($uName) && strlen($uName) >= 4) {
                DB::table('candidates')
                    ->whereRaw('LOWER(TRIM(useras)) = ?', [$uName])
                    ->update([
                        'useras' => $u->email,
                        'recruiter_id' => $u->id,
                    ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'email_aliases')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('email_aliases');
            });
        }
    }
};
