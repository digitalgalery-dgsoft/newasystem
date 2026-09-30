<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\TbArea;

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

        if (!$hasCandidates || !$hasUsers) {
            return;
        }

        // 1. Kamus Peta Default AS OPS per Area Resmi
        $knownAreaAs = [
            'surabaya'          => ['email' => 'lestari.arina.sby@gmail.com', 'user_id' => 51, 'name' => 'Lestari Meiningsih'],
            'medaeng'           => ['email' => 'lestari.arina.sby@gmail.com', 'user_id' => 51, 'name' => 'Lestari Meiningsih'],
            'sidoarjo'          => ['email' => 'lestari.arina.sby@gmail.com', 'user_id' => 51, 'name' => 'Lestari Meiningsih'],
            'banyuwangi'        => ['email' => 'mochamad.ismail27.mi@gmail.com', 'user_id' => 185, 'name' => 'MOCHAMAD ISMAIL'],
            'denpasar'          => ['email' => 'febrian.rian1302@gmail.com', 'user_id' => 162, 'name' => 'Febriansyah'],
            'mataram'           => ['email' => 'febrian.rian1302@gmail.com', 'user_id' => 162, 'name' => 'Febriansyah'],
            'malang'            => ['email' => 'enfatika94@gmail.com', 'user_id' => 160, 'name' => 'ENFATIKA KARIA NENSATI'],
            'jember'            => ['email' => 'firmanhadi939@gmail.com', 'user_id' => 243, 'name' => 'Firman Hadi saputra'],
            'kediri'            => ['email' => 'adm.kediri@arina.co.id', 'user_id' => 89, 'name' => 'Kartika Sari'],
            'madiun'            => ['email' => 'adm.kediri@arina.co.id', 'user_id' => 89, 'name' => 'Kartika Sari'],
            'bojonegoro'        => ['email' => 'lenywidyawati2212@gmail.com', 'user_id' => 251, 'name' => 'LENY WIDYAWATI'],
            'bandung'           => ['email' => 'christonormandebby@gmail.com', 'user_id' => 211, 'name' => 'Christo Norman Debby Fredrick'],
            'tasikmalaya'       => ['email' => 'itsbey115@gmail.com', 'user_id' => 170, 'name' => 'Iis Aisyah'],
            'cirebon'           => ['email' => 'netione39@gmail.com', 'user_id' => 169, 'name' => 'NETI HERAWATI'],
            'semarang'          => ['email' => 'aliffirmantoro20@gmail.com', 'user_id' => 518, 'name' => 'ALIF MAULANA FIRMANTORO'],
            'kudus'             => ['email' => 'aliffirmantoro20@gmail.com', 'user_id' => 518, 'name' => 'ALIF MAULANA FIRMANTORO'],
            'tegal'             => ['email' => 'aliffirmantoro20@gmail.com', 'user_id' => 518, 'name' => 'ALIF MAULANA FIRMANTORO'],
            'yogyakarta'        => ['email' => 'dennihendra@gmail.com', 'user_id' => 180, 'name' => 'DENNI HENDRA GUNAWAN'],
            'solo'              => ['email' => 'hengkyarea@gmail.com', 'user_id' => 192, 'name' => 'HENGKI ARYA WIBOWO'],
            'purwokerto'        => ['email' => 'maulidavinasabilanisataba@gmail.com', 'user_id' => 182, 'name' => 'MAULIDA VINA SABILA NISA TABA'],
            'medan'             => ['email' => 'marshelwilhelmus22@gmail.com', 'user_id' => 285, 'name' => 'MARSHEL WILHELMUS PINEM'],
            'pekanbaru'         => ['email' => 'junita.simanjuntak.pekanbaru@gmail.com', 'user_id' => 181, 'name' => 'Junita simanjuntak'],
            'jambi'             => ['email' => 'ulyfarida2@gmail.com', 'user_id' => 173, 'name' => 'ULY FARIDA PARAPAT'],
            'lampung'           => ['email' => 'gugunceper525@gmail.com', 'user_id' => 250, 'name' => 'GUNTORO AZIZ'],
            'palembang'         => ['email' => 'siahaanlidya84@gmail.com', 'user_id' => 156, 'name' => 'LIDYA WATI S'],
            'jakarta'           => ['email' => 'imam25ari@gmail.com', 'user_id' => 265, 'name' => 'IMAM ARI PRASETIO'],
            'pasuruan'          => ['email' => 'anggabprayoga91291@gmail.com', 'user_id' => 334, 'name' => 'ANGGA BAGUS PRAYUGO'],
            'samarinda'         => ['email' => 'valen.ringo@gmail.com', 'user_id' => 78, 'name' => 'Valentina Siringo Ringo'],
            'balikpapan'        => ['email' => 'imelpuspahatitobing@gmail.com', 'user_id' => 212, 'name' => 'Imelda Dermawan P Br Tobing'],
            'banjarmasin'       => ['email' => 'monicacantika98@gmail.com', 'user_id' => 195, 'name' => 'Kris Monika'],
            'pontianak'         => ['email' => 'leo.candra.ar@gmail.com', 'user_id' => 167, 'name' => 'LEO CANDRA'],
            'makassar'          => ['email' => 'AIMRAHIM130394@GMAIL.COM', 'user_id' => 311, 'name' => 'ABDURRAHIM'],
            'manado'            => ['email' => 'marwanmangore@gmail.com', 'user_id' => 246, 'name' => 'Marwan Hendra Mangore'],
            'palu'              => ['email' => 'mayaruannn@gmail.com', 'user_id' => 159, 'name' => 'MAYDA LISANA AGUSTINA'],
            'kupang'            => ['email' => 'inaagrefina@gmail.com', 'user_id' => 171, 'name' => 'AGREFINA ISABEL NETTU'],
        ];

        // 2. Query seluruh kandidat dengan status useras Publik / Kosong
        $publikCandidates = DB::table('candidates')
            ->where(function($q) {
                $q->whereIn(DB::raw('LOWER(TRIM(useras))'), ['publik', 'public'])
                  ->orWhereNull('useras')
                  ->orWhere('useras', '');
            })
            ->select('id', 'area', 'penempatan')
            ->get();

        foreach ($publikCandidates as $cand) {
            $rawArea = trim($cand->area ?: ($cand->penempatan ?? ''));
            if (empty($rawArea) || $rawArea === '-' || strtolower($rawArea) === 'indonesia') {
                continue;
            }

            // Dapatkan area kanonikal jika class TbArea tersedia
            $canonicalArea = class_exists(TbArea::class) ? TbArea::getCanonicalAreaName($rawArea) : $rawArea;
            $cleanAreaLower = strtolower(trim($canonicalArea ?: $rawArea));

            $targetEmail = null;
            $targetUserId = null;

            // 1. Cek dari kamus definitif
            if (isset($knownAreaAs[$cleanAreaLower])) {
                $info = $knownAreaAs[$cleanAreaLower];
                $targetEmail = strtolower(trim($info['email']));
                $targetUserId = $info['user_id'];
            }

            // 2. Jika belum ketemu, cari user aktif dengan area terkait
            if (!$targetEmail) {
                $u = DB::table('users')
                    ->whereRaw('LOWER(TRIM(area)) = ?', [$cleanAreaLower])
                    ->where('id', '!=', 1)
                    ->where('email', '!=', 'admin@asystem.co.id')
                    ->where(function($q) {
                        $q->where('job_title', 'like', '%AS OPS%')
                          ->orWhere('role', 'recruiter')
                          ->orWhere('job_title', 'like', '%RECRUIT%');
                    })
                    ->orderBy('id', 'asc')
                    ->first();

                if ($u) {
                    $targetEmail = strtolower(trim($u->email));
                    $targetUserId = $u->id;
                }
            }

            // Update kandidat jika AS ditemukan
            if ($targetEmail) {
                $updateData = ['useras' => $targetEmail];
                if ($targetUserId !== null) {
                    $userExists = DB::table('users')->where('id', $targetUserId)->exists();
                    if ($userExists) {
                        $updateData['recruiter_id'] = $targetUserId;
                    } else {
                        $uByEmail = DB::table('users')->whereRaw('LOWER(TRIM(email)) = ?', [$targetEmail])->first();
                        $updateData['recruiter_id'] = $uByEmail ? $uByEmail->id : null;
                    }
                }

                DB::table('candidates')->where('id', $cand->id)->update($updateData);

                if ($hasTbKandidat && Schema::hasColumn('tb_kandidat', 'nama_as')) {
                    DB::table('tb_kandidat')->where('id', $cand->id)->update([
                        'nama_as' => $targetEmail,
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
        // No rollback needed
    }
};
