<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\TbArea;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tambah kolom region pada tabel candidates jika belum ada
        if (Schema::hasTable('candidates') && !Schema::hasColumn('candidates', 'region')) {
            Schema::table('candidates', function (Blueprint $table) {
                $table->string('region', 50)->nullable()->index()->after('area');
            });
        }

        // 2. Tambah kolom job_region pada tabel job_specs jika belum ada
        if (Schema::hasTable('job_specs') && !Schema::hasColumn('job_specs', 'job_region')) {
            Schema::table('job_specs', function (Blueprint $table) {
                $table->string('job_region', 50)->nullable()->index()->after('job_area');
            });
        }

        // 3. Pastikan data tb_area lengkap & mutakhir sesuai tb_area (2).sql
        if (Schema::hasTable('tb_area')) {
            $officialAreas = [
                ['kode' => 1,  'kode_area' => 8477, 'area' => 'Surabaya',         'region' => 'Region 4', 'hk' => 21, 'singkatan' => 'SBY'],
                ['kode' => 2,  'kode_area' => 4930, 'area' => 'Jakarta',          'region' => 'Region 1', 'hk' => 21, 'singkatan' => 'JKT'],
                ['kode' => 3,  'kode_area' => 7975, 'area' => 'Bandung',          'region' => 'Region 2', 'hk' => 25, 'singkatan' => 'BDG'],
                ['kode' => 4,  'kode_area' => 7863, 'area' => 'Cirebon',          'region' => 'Region 2', 'hk' => 25, 'singkatan' => 'CRB'],
                ['kode' => 5,  'kode_area' => 1591, 'area' => 'Jambi',            'region' => 'Region 7', 'hk' => 25, 'singkatan' => 'JMB'],
                ['kode' => 6,  'kode_area' => 5793, 'area' => 'Lampung',          'region' => 'Region 7', 'hk' => 25, 'singkatan' => 'LPG'],
                ['kode' => 7,  'kode_area' => 7682, 'area' => 'Padang',           'region' => 'Region 7', 'hk' => 25, 'singkatan' => 'PDG'],
                ['kode' => 8,  'kode_area' => 3396, 'area' => 'Palembang',        'region' => 'Region 7', 'hk' => 25, 'singkatan' => 'PLB'],
                ['kode' => 9,  'kode_area' => 1510, 'area' => 'Pekanbaru',        'region' => 'Region 7', 'hk' => 25, 'singkatan' => 'PKB'],
                ['kode' => 10, 'kode_area' => 9154, 'area' => 'Purwokerto',       'region' => 'Region 3', 'hk' => 25, 'singkatan' => 'PWKT'],
                ['kode' => 11, 'kode_area' => 2296, 'area' => 'Semarang',         'region' => 'Region 3', 'hk' => 25, 'singkatan' => 'SMG'],
                ['kode' => 12, 'kode_area' => 2917, 'area' => 'Solo',             'region' => 'Region 3', 'hk' => 25, 'singkatan' => 'SOLO'],
                ['kode' => 13, 'kode_area' => 3415, 'area' => 'Yogyakarta',       'region' => 'Region 3', 'hk' => 25, 'singkatan' => 'YGK'],
                ['kode' => 14, 'kode_area' => 9779, 'area' => 'Bojonegoro',       'region' => 'Region 4', 'hk' => 25, 'singkatan' => 'BJN'],
                ['kode' => 15, 'kode_area' => 5811, 'area' => 'Buduran',          'region' => 'Region 4', 'hk' => 25, 'singkatan' => 'BDRN'],
                ['kode' => 16, 'kode_area' => 4546, 'area' => 'Denpasar',         'region' => 'Region 4', 'hk' => 25, 'singkatan' => 'DPS'],
                ['kode' => 17, 'kode_area' => 3975, 'area' => 'Jember',           'region' => 'Region 4', 'hk' => 25, 'singkatan' => 'JMBR'],
                ['kode' => 18, 'kode_area' => 5059, 'area' => 'Kediri',           'region' => 'Region 4', 'hk' => 25, 'singkatan' => 'KDR'],
                ['kode' => 19, 'kode_area' => 5841, 'area' => 'Kupang',           'region' => 'Region 4', 'hk' => 25, 'singkatan' => 'KPG'],
                ['kode' => 20, 'kode_area' => 2314, 'area' => 'Madiun',           'region' => 'Region 4', 'hk' => 25, 'singkatan' => 'MDIUN'],
                ['kode' => 21, 'kode_area' => 1655, 'area' => 'Malang',           'region' => 'Region 4', 'hk' => 25, 'singkatan' => 'MLG'],
                ['kode' => 22, 'kode_area' => 8892, 'area' => 'Mataram',          'region' => 'Region 4', 'hk' => 25, 'singkatan' => 'MTR'],
                ['kode' => 23, 'kode_area' => 2151, 'area' => 'Medaeng',          'region' => 'Region 4', 'hk' => 25, 'singkatan' => 'MDG'],
                ['kode' => 24, 'kode_area' => 7021, 'area' => 'Pasuruan',         'region' => 'Region 4', 'hk' => 25, 'singkatan' => 'PSR'],
                ['kode' => 25, 'kode_area' => 9686, 'area' => 'Ambon',            'region' => 'Region 5', 'hk' => 25, 'singkatan' => 'AMBN'],
                ['kode' => 26, 'kode_area' => 9457, 'area' => 'Makassar',         'region' => 'Region 5', 'hk' => 25, 'singkatan' => 'MKS'],
                ['kode' => 27, 'kode_area' => 1244, 'area' => 'Manado',           'region' => 'Region 5', 'hk' => 25, 'singkatan' => 'MND'],
                ['kode' => 28, 'kode_area' => 5521, 'area' => 'Palu',             'region' => 'Region 5', 'hk' => 25, 'singkatan' => 'PALU'],
                ['kode' => 29, 'kode_area' => 7589, 'area' => 'Papua',            'region' => 'Region 5', 'hk' => 25, 'singkatan' => 'PAPUA'],
                ['kode' => 30, 'kode_area' => 7161, 'area' => 'Aceh',             'region' => 'Region 6', 'hk' => 25, 'singkatan' => 'ACEH'],
                ['kode' => 31, 'kode_area' => 9519, 'area' => 'Batam',            'region' => 'Region 6', 'hk' => 25, 'singkatan' => 'BTM'],
                ['kode' => 32, 'kode_area' => 9817, 'area' => 'Medan',            'region' => 'Region 6', 'hk' => 25, 'singkatan' => 'MDN'],
                ['kode' => 33, 'kode_area' => 4579, 'area' => 'Pematang Siantar', 'region' => 'Region 6', 'hk' => 25, 'singkatan' => 'PMS'],
                ['kode' => 34, 'kode_area' => 4938, 'area' => 'Balikpapan',       'region' => 'Region 6', 'hk' => 25, 'singkatan' => 'BLP'],
                ['kode' => 35, 'kode_area' => 5121, 'area' => 'Banjarmasin',      'region' => 'Region 6', 'hk' => 25, 'singkatan' => 'BJM'],
                ['kode' => 36, 'kode_area' => 1739, 'area' => 'Pontianak',        'region' => 'Region 6', 'hk' => 25, 'singkatan' => 'PTN'],
                ['kode' => 37, 'kode_area' => 7501, 'area' => 'Samarinda',        'region' => 'Region 6', 'hk' => 25, 'singkatan' => 'SMRD'],
                ['kode' => 38, 'kode_area' => 6908, 'area' => 'Kudus',            'region' => 'Region 3', 'hk' => 25, 'singkatan' => 'KDS'],
                ['kode' => 39, 'kode_area' => 1191, 'area' => 'Tasikmalaya',      'region' => 'Region 2', 'hk' => 25, 'singkatan' => 'TSM'],
                ['kode' => 40, 'kode_area' => 1388, 'area' => 'Tegal',            'region' => 'Region 3', 'hk' => 25, 'singkatan' => 'TGL'],
                ['kode' => 41, 'kode_area' => 40,   'area' => 'Gorontalo',        'region' => 'Region 5', 'hk' => 25, 'singkatan' => 'GRTL'],
                ['kode' => 42, 'kode_area' => 43,   'area' => 'Kendari',          'region' => 'Region 5', 'hk' => 25, 'singkatan' => 'KNDR'],
                ['kode' => 43, 'kode_area' => 47,   'area' => 'Banyuwangi',       'region' => 'Region 4', 'hk' => 25, 'singkatan' => 'BNYW'],
            ];

            foreach ($officialAreas as $oa) {
                DB::table('tb_area')->updateOrInsert(
                    ['kode' => $oa['kode']],
                    $oa
                );
            }
        }

        // 4. Normalisasi Kapitalisasi Nama Area pada candidates
        if (Schema::hasTable('candidates')) {
            $capitalFixes = [
                'TASIKMALAYA' => 'Tasikmalaya',
                'CIREBON'     => 'Cirebon',
                'JAKARTA'     => 'Jakarta',
                'MEDAN'       => 'Medan',
                'PONTIANAK'   => 'Pontianak',
                'SAMARINDA'   => 'Samarinda',
            ];

            foreach ($capitalFixes as $bad => $good) {
                DB::table('candidates')->where('area', $bad)->update(['area' => $good]);
            }

            // Normalisasi kandidat dengan area pendaftaran alias
            DB::table('candidates')->whereRaw('LOWER(TRIM(area)) = ?', ['pematangsiantar'])->update(['area' => 'Pematang Siantar']);
            DB::table('candidates')->whereRaw('LOWER(TRIM(area)) = ?', ['banda aceh'])->update(['area' => 'Aceh']);
            DB::table('candidates')->whereIn('area', ['islam', '-'])->update(['area' => 'Jakarta']);

            // 5. Update kolom region pada tabel candidates sesuai master tb_area
            $regionMap = [
                'Surabaya'        => 'Region 4',
                'Jakarta'         => 'Region 1',
                'Bandung'         => 'Region 2',
                'Cirebon'         => 'Region 2',
                'Jambi'           => 'Region 7',
                'Lampung'         => 'Region 7',
                'Padang'          => 'Region 7',
                'Palembang'       => 'Region 7',
                'Pekanbaru'       => 'Region 7',
                'Purwokerto'      => 'Region 3',
                'Semarang'        => 'Region 3',
                'Solo'            => 'Region 3',
                'Yogyakarta'      => 'Region 3',
                'Bojonegoro'      => 'Region 4',
                'Buduran'         => 'Region 4',
                'Denpasar'        => 'Region 4',
                'Jember'          => 'Region 4',
                'Kediri'          => 'Region 4',
                'Kupang'          => 'Region 4',
                'Madiun'          => 'Region 4',
                'Malang'          => 'Region 4',
                'Mataram'         => 'Region 4',
                'Medaeng'         => 'Region 4',
                'Pasuruan'        => 'Region 4',
                'Ambon'           => 'Region 5',
                'Makassar'        => 'Region 5',
                'Manado'          => 'Region 5',
                'Palu'            => 'Region 5',
                'Papua'           => 'Region 5',
                'Aceh'            => 'Region 6',
                'Batam'           => 'Region 6',
                'Medan'           => 'Region 6',
                'Pematang Siantar' => 'Region 6',
                'Balikpapan'      => 'Region 6',
                'Banjarmasin'     => 'Region 6',
                'Pontianak'       => 'Region 6',
                'Samarinda'       => 'Region 6',
                'Kudus'           => 'Region 3',
                'Tasikmalaya'     => 'Region 2',
                'Tegal'           => 'Region 3',
                'Gorontalo'       => 'Region 5',
                'Kendari'         => 'Region 5',
                'Banyuwangi'      => 'Region 4',
            ];

            foreach ($regionMap as $area => $region) {
                DB::table('candidates')
                    ->whereRaw('LOWER(TRIM(area)) = ?', [strtolower($area)])
                    ->update(['region' => $region]);
            }

            // Fallback region untuk kandidat tanpa area atau area Nasional
            DB::table('candidates')
                ->where(function ($q) {
                    $q->whereNull('region')
                      ->orWhere('region', '')
                      ->orWhere('region', '-');
                })
                ->whereNotNull('city_domicile')
                ->where('city_domicile', '!=', '')
                ->chunkById(500, function ($cands) {
                    foreach ($cands as $cand) {
                        $reg = TbArea::resolveRegion($cand->city_domicile);
                        if ($reg !== '-') {
                            DB::table('candidates')->where('id', $cand->id)->update(['region' => $reg]);
                        }
                    }
                });

            // Default terakhir untuk baris yang masih belum memiliki region
            DB::table('candidates')
                ->whereNull('region')
                ->orWhere('region', '')
                ->orWhere('region', '-')
                ->update(['region' => 'Region 1']);
        }

        // 6. Normalisasi job_specs
        if (Schema::hasTable('job_specs')) {
            DB::table('job_specs')->where('job_area', 'TASIKMALAYA')->update(['job_area' => 'Tasikmalaya']);
            DB::table('job_specs')->whereRaw('LOWER(TRIM(job_area)) = ?', ['pematangsiantar'])->update(['job_area' => 'Pematang Siantar']);

            foreach ($regionMap ?? [] as $area => $region) {
                DB::table('job_specs')
                    ->whereRaw('LOWER(TRIM(job_area)) = ?', [strtolower($area)])
                    ->update(['job_region' => $region]);
            }

            DB::table('job_specs')
                ->whereNull('job_region')
                ->orWhere('job_region', '')
                ->orWhere('job_region', '-')
                ->update(['job_region' => 'Region 1']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('candidates') && Schema::hasColumn('candidates', 'region')) {
            Schema::table('candidates', function (Blueprint $table) {
                $table->dropColumn('region');
            });
        }

        if (Schema::hasTable('job_specs') && Schema::hasColumn('job_specs', 'job_region')) {
            Schema::table('job_specs', function (Blueprint $table) {
                $table->dropColumn('job_region');
            });
        }
    }
};
