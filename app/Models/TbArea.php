<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TbArea extends Model
{
    protected $table = 'tb_area';
    protected $primaryKey = 'kode';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'kode',
        'kode_area',
        'area',
        'region',
        'hk',
        'singkatan',
    ];

    /**
     * Master 43 Area Resmi ESA Groups beserta Region & Singkatan (Sesuai tb_area.sql).
     */
    public static function getOfficialAreas(): array
    {
        return [
            1  => ['kode' => 1,  'kode_area' => 8477, 'area' => 'Surabaya',         'region' => 'Region 4', 'hk' => 21, 'singkatan' => 'SBY'],
            2  => ['kode' => 2,  'kode_area' => 4930, 'area' => 'Jakarta',          'region' => 'Region 1', 'hk' => 21, 'singkatan' => 'JKT'],
            3  => ['kode' => 3,  'kode_area' => 7975, 'area' => 'Bandung',          'region' => 'Region 2', 'hk' => 25, 'singkatan' => 'BDG'],
            4  => ['kode' => 4,  'kode_area' => 7863, 'area' => 'Cirebon',          'region' => 'Region 2', 'hk' => 25, 'singkatan' => 'CRB'],
            5  => ['kode' => 5,  'kode_area' => 1591, 'area' => 'Jambi',            'region' => 'Region 7', 'hk' => 25, 'singkatan' => 'JMB'],
            6  => ['kode' => 6,  'kode_area' => 5793, 'area' => 'Lampung',          'region' => 'Region 7', 'hk' => 25, 'singkatan' => 'LPG'],
            7  => ['kode' => 7,  'kode_area' => 7682, 'area' => 'Padang',           'region' => 'Region 7', 'hk' => 25, 'singkatan' => 'PDG'],
            8  => ['kode' => 8,  'kode_area' => 3396, 'area' => 'Palembang',        'region' => 'Region 7', 'hk' => 25, 'singkatan' => 'PLB'],
            9  => ['kode' => 9,  'kode_area' => 1510, 'area' => 'Pekanbaru',        'region' => 'Region 7', 'hk' => 25, 'singkatan' => 'PKB'],
            10 => ['kode' => 10, 'kode_area' => 9154, 'area' => 'Purwokerto',       'region' => 'Region 3', 'hk' => 25, 'singkatan' => 'PWKT'],
            11 => ['kode' => 11, 'kode_area' => 2296, 'area' => 'Semarang',         'region' => 'Region 3', 'hk' => 25, 'singkatan' => 'SMG'],
            12 => ['kode' => 12, 'kode_area' => 2917, 'area' => 'Solo',             'region' => 'Region 3', 'hk' => 25, 'singkatan' => 'SOLO'],
            13 => ['kode' => 13, 'kode_area' => 3415, 'area' => 'Yogyakarta',       'region' => 'Region 3', 'hk' => 25, 'singkatan' => 'YGK'],
            14 => ['kode' => 14, 'kode_area' => 9779, 'area' => 'Bojonegoro',       'region' => 'Region 4', 'hk' => 25, 'singkatan' => 'BJN'],
            15 => ['kode' => 15, 'kode_area' => 5811, 'area' => 'Buduran',          'region' => 'Region 4', 'hk' => 25, 'singkatan' => 'BDRN'],
            16 => ['kode' => 16, 'kode_area' => 4546, 'area' => 'Denpasar',         'region' => 'Region 4', 'hk' => 25, 'singkatan' => 'DPS'],
            17 => ['kode' => 17, 'kode_area' => 3975, 'area' => 'Jember',           'region' => 'Region 4', 'hk' => 25, 'singkatan' => 'JMBR'],
            18 => ['kode' => 18, 'kode_area' => 5059, 'area' => 'Kediri',           'region' => 'Region 4', 'hk' => 25, 'singkatan' => 'KDR'],
            19 => ['kode' => 19, 'kode_area' => 5841, 'area' => 'Kupang',           'region' => 'Region 4', 'hk' => 25, 'singkatan' => 'KPG'],
            20 => ['kode' => 20, 'kode_area' => 2314, 'area' => 'Madiun',           'region' => 'Region 4', 'hk' => 25, 'singkatan' => 'MDIUN'],
            21 => ['kode' => 21, 'kode_area' => 1655, 'area' => 'Malang',           'region' => 'Region 4', 'hk' => 25, 'singkatan' => 'MLG'],
            22 => ['kode' => 22, 'kode_area' => 8892, 'area' => 'Mataram',          'region' => 'Region 4', 'hk' => 25, 'singkatan' => 'MTR'],
            23 => ['kode' => 23, 'kode_area' => 2151, 'area' => 'Medaeng',          'region' => 'Region 4', 'hk' => 25, 'singkatan' => 'MDG'],
            24 => ['kode' => 24, 'kode_area' => 7021, 'area' => 'Pasuruan',         'region' => 'Region 4', 'hk' => 25, 'singkatan' => 'PSR'],
            25 => ['kode' => 25, 'kode_area' => 9686, 'area' => 'Ambon',            'region' => 'Region 5', 'hk' => 25, 'singkatan' => 'AMBN'],
            26 => ['kode' => 26, 'kode_area' => 9457, 'area' => 'Makassar',         'region' => 'Region 5', 'hk' => 25, 'singkatan' => 'MKS'],
            27 => ['kode' => 27, 'kode_area' => 1244, 'area' => 'Manado',           'region' => 'Region 5', 'hk' => 25, 'singkatan' => 'MND'],
            28 => ['kode' => 28, 'kode_area' => 5521, 'area' => 'Palu',             'region' => 'Region 5', 'hk' => 25, 'singkatan' => 'PALU'],
            29 => ['kode' => 29, 'kode_area' => 7589, 'area' => 'Papua',            'region' => 'Region 5', 'hk' => 25, 'singkatan' => 'PAPUA'],
            30 => ['kode' => 30, 'kode_area' => 7161, 'area' => 'Aceh',             'region' => 'Region 6', 'hk' => 25, 'singkatan' => 'ACEH'],
            31 => ['kode' => 31, 'kode_area' => 9519, 'area' => 'Batam',            'region' => 'Region 6', 'hk' => 25, 'singkatan' => 'BTM'],
            32 => ['kode' => 32, 'kode_area' => 9817, 'area' => 'Medan',            'region' => 'Region 6', 'hk' => 25, 'singkatan' => 'MDN'],
            33 => ['kode' => 33, 'kode_area' => 4579, 'area' => 'Pematang Siantar', 'region' => 'Region 6', 'hk' => 25, 'singkatan' => 'PMS'],
            34 => ['kode' => 34, 'kode_area' => 4938, 'area' => 'Balikpapan',       'region' => 'Region 6', 'hk' => 25, 'singkatan' => 'BLP'],
            35 => ['kode' => 35, 'kode_area' => 5121, 'area' => 'Banjarmasin',      'region' => 'Region 6', 'hk' => 25, 'singkatan' => 'BJM'],
            36 => ['kode' => 36, 'kode_area' => 1739, 'area' => 'Pontianak',        'region' => 'Region 6', 'hk' => 25, 'singkatan' => 'PTN'],
            37 => ['kode' => 37, 'kode_area' => 7501, 'area' => 'Samarinda',        'region' => 'Region 6', 'hk' => 25, 'singkatan' => 'SMRD'],
            38 => ['kode' => 38, 'kode_area' => 6908, 'area' => 'Kudus',            'region' => 'Region 3', 'hk' => 25, 'singkatan' => 'KDS'],
            39 => ['kode' => 39, 'kode_area' => 1191, 'area' => 'Tasikmalaya',      'region' => 'Region 2', 'hk' => 25, 'singkatan' => 'TSM'],
            40 => ['kode' => 40, 'kode_area' => 1388, 'area' => 'Tegal',            'region' => 'Region 3', 'hk' => 25, 'singkatan' => 'TGL'],
            41 => ['kode' => 41, 'kode_area' => 40,   'area' => 'Gorontalo',        'region' => 'Region 5', 'hk' => 25, 'singkatan' => 'GRTL'],
            42 => ['kode' => 42, 'kode_area' => 43,   'area' => 'Kendari',          'region' => 'Region 5', 'hk' => 25, 'singkatan' => 'KNDR'],
            43 => ['kode' => 43, 'kode_area' => 47,   'area' => 'Banyuwangi',       'region' => 'Region 4', 'hk' => 25, 'singkatan' => 'BNYW'],
        ];
    }

    /**
     * Peta Lengkap Area / Kota / Singkatan ke Region 1 s/d Region 7.
     */
    public static function getRegionMap(): array
    {
        static $map = null;
        if ($map !== null) {
            return $map;
        }

        $map = [
            // Region 1: Jakarta & Jabodetabek
            'jakarta' => 'Region 1', 'dki jakarta' => 'Region 1', 'jkt' => 'Region 1',
            'jakpus' => 'Region 1', 'jaksel' => 'Region 1', 'jakbar' => 'Region 1', 'jaktim' => 'Region 1', 'jakut' => 'Region 1',
            'bogor' => 'Region 1', 'depok' => 'Region 1', 'tangerang' => 'Region 1', 'tangsel' => 'Region 1', 'bekasi' => 'Region 1',
            'kepulauan seribu' => 'Region 1',

            // Region 2: Bandung, Cirebon, Tasikmalaya (Jawa Barat)
            'bandung' => 'Region 2', 'bdg' => 'Region 2',
            'cirebon' => 'Region 2', 'crb' => 'Region 2',
            'tasikmalaya' => 'Region 2', 'tsm' => 'Region 2', 'tasik' => 'Region 2',
            'sukabumi' => 'Region 2', 'karawang' => 'Region 2', 'subang' => 'Region 2', 'garut' => 'Region 2',
            'purwakarta' => 'Region 2', 'indramayu' => 'Region 2', 'majalengka' => 'Region 2', 'kuningan' => 'Region 2',
            'cianjur' => 'Region 2', 'cimahi' => 'Region 2', 'sumedang' => 'Region 2', 'ciamis' => 'Region 2',
            'banjar' => 'Region 2', 'pangandaran' => 'Region 2',

            // Region 3: Purwokerto, Semarang, Solo, Yogyakarta, Kudus, Tegal (Jateng & DIY)
            'purwokerto' => 'Region 3', 'pwkt' => 'Region 3',
            'semarang' => 'Region 3', 'smg' => 'Region 3',
            'solo' => 'Region 3', 'surakarta' => 'Region 3',
            'yogyakarta' => 'Region 3', 'ygk' => 'Region 3', 'jogja' => 'Region 3', 'diy' => 'Region 3',
            'kudus' => 'Region 3', 'kds' => 'Region 3',
            'tegal' => 'Region 3', 'tgl' => 'Region 3',
            'pekalongan' => 'Region 3', 'magelang' => 'Region 3', 'cilacap' => 'Region 3', 'pati' => 'Region 3',
            'klaten' => 'Region 3', 'salatiga' => 'Region 3', 'brebes' => 'Region 3', 'banyumas' => 'Region 3',
            'kebumen' => 'Region 3', 'purworejo' => 'Region 3', 'wonosobo' => 'Region 3', 'boyolali' => 'Region 3',
            'sukoharjo' => 'Region 3', 'karanganyar' => 'Region 3', 'wonogiri' => 'Region 3', 'sragen' => 'Region 3',
            'grobogan' => 'Region 3', 'blora' => 'Region 3', 'rembang' => 'Region 3', 'jepara' => 'Region 3',
            'demak' => 'Region 3', 'temanggung' => 'Region 3', 'batang' => 'Region 3', 'pemalang' => 'Region 3',
            'sleman' => 'Region 3', 'bantul' => 'Region 3', 'kulon progo' => 'Region 3', 'gunungkidul' => 'Region 3',

            // Region 4: Surabaya, Bojonegoro, Buduran, Denpasar, Jember, Kediri, Kupang, Madiun, Malang, Mataram, Medaeng, Pasuruan, Banyuwangi (Jatim, Bali, Nusa Tenggara)
            'surabaya' => 'Region 4', 'sby' => 'Region 4',
            'bojonegoro' => 'Region 4', 'bjn' => 'Region 4',
            'buduran' => 'Region 4', 'bdrn' => 'Region 4',
            'denpasar' => 'Region 4', 'dps' => 'Region 4', 'bali' => 'Region 4',
            'jember' => 'Region 4', 'jmbr' => 'Region 4',
            'kediri' => 'Region 4', 'kdr' => 'Region 4',
            'kupang' => 'Region 4', 'kpg' => 'Region 4', 'ntt' => 'Region 4',
            'madiun' => 'Region 4', 'mdiun' => 'Region 4',
            'malang' => 'Region 4', 'mlg' => 'Region 4',
            'mataram' => 'Region 4', 'mtr' => 'Region 4', 'lombok' => 'Region 4', 'ntb' => 'Region 4',
            'medaeng' => 'Region 4', 'mdg' => 'Region 4',
            'pasuruan' => 'Region 4', 'psr' => 'Region 4',
            'banyuwangi' => 'Region 4', 'bnyw' => 'Region 4',
            'sidoarjo' => 'Region 4', 'gresik' => 'Region 4', 'mojokerto' => 'Region 4', 'jombang' => 'Region 4',
            'probolinggo' => 'Region 4', 'tuban' => 'Region 4', 'lamongan' => 'Region 4', 'blitar' => 'Region 4',
            'tulungagung' => 'Region 4', 'nganjuk' => 'Region 4', 'trenggalek' => 'Region 4', 'ponorogo' => 'Region 4',
            'pacitan' => 'Region 4', 'magetan' => 'Region 4', 'ngawi' => 'Region 4', 'bangkalan' => 'Region 4',
            'sampang' => 'Region 4', 'pamekasan' => 'Region 4', 'sumenep' => 'Region 4', 'situbondo' => 'Region 4',
            'lumajang' => 'Region 4', 'bondowoso' => 'Region 4', 'sumbawa' => 'Region 4', 'bima' => 'Region 4',
            'flores' => 'Region 4', 'sumba' => 'Region 4', 'alor' => 'Region 4', 'belu' => 'Region 4',

            // Region 5: Ambon, Makassar, Manado, Palu, Papua, Gorontalo, Kendari (Sulawesi, Maluku, Papua)
            'ambon' => 'Region 5', 'ambn' => 'Region 5', 'maluku' => 'Region 5',
            'makassar' => 'Region 5', 'mks' => 'Region 5', 'sulsel' => 'Region 5',
            'manado' => 'Region 5', 'mnd' => 'Region 5', 'sulut' => 'Region 5',
            'palu' => 'Region 5', 'sulteng' => 'Region 5',
            'papua' => 'Region 5', 'jayapura' => 'Region 5', 'sorong' => 'Region 5', 'merauke' => 'Region 5', 'mimika' => 'Region 5',
            'gorontalo' => 'Region 5', 'grtl' => 'Region 5',
            'kendari' => 'Region 5', 'kndr' => 'Region 5', 'sultra' => 'Region 5',
            'mamuju' => 'Region 5', 'sulbar' => 'Region 5', 'bitung' => 'Region 5', 'kotamobagu' => 'Region 5',
            'tomohon' => 'Region 5', 'baubau' => 'Region 5', 'parepare' => 'Region 5', 'palopo' => 'Region 5',
            'maros' => 'Region 5', 'gowa' => 'Region 5',

            // Region 6: Aceh, Batam, Medan, Pematang Siantar, Balikpapan, Banjarmasin, Pontianak, Samarinda (Kalimantan, Sumut, Aceh, Kepri)
            'aceh' => 'Region 6', 'banda aceh' => 'Region 6', 'nad aceh' => 'Region 6',
            'batam' => 'Region 6', 'btm' => 'Region 6', 'kepri' => 'Region 6',
            'medan' => 'Region 6', 'mdn' => 'Region 6', 'sumut' => 'Region 6',
            'pematang siantar' => 'Region 6', 'pematangsiantar' => 'Region 6', 'siantar' => 'Region 6', 'pms' => 'Region 6',
            'balikpapan' => 'Region 6', 'blp' => 'Region 6',
            'banjarmasin' => 'Region 6', 'bjm' => 'Region 6', 'kalsel' => 'Region 6',
            'pontianak' => 'Region 6', 'ptn' => 'Region 6', 'kalbar' => 'Region 6',
            'samarinda' => 'Region 6', 'smrd' => 'Region 6', 'kaltim' => 'Region 6',
            'palangkaraya' => 'Region 6', 'kalteng' => 'Region 6', 'tarakan' => 'Region 6', 'kaltara' => 'Region 6',
            'tanjungpinang' => 'Region 6', 'binjai' => 'Region 6', 'singkawang' => 'Region 6',
            'banjarbaru' => 'Region 6', 'bontang' => 'Region 6', 'kutai' => 'Region 6',

            // Region 7: Jambi, Lampung, Padang, Palembang, Pekanbaru (Sumatera Selatan, Tengah, Barat, Riau)
            'jambi' => 'Region 7', 'jmb' => 'Region 7',
            'lampung' => 'Region 7', 'lpg' => 'Region 7', 'bandar lampung' => 'Region 7',
            'padang' => 'Region 7', 'pdg' => 'Region 7', 'sumbar' => 'Region 7',
            'palembang' => 'Region 7', 'plb' => 'Region 7', 'sumsel' => 'Region 7',
            'pekanbaru' => 'Region 7', 'pkb' => 'Region 7', 'riau' => 'Region 7',
            'bengkulu' => 'Region 7', 'pangkalpinang' => 'Region 7', 'bangka' => 'Region 7', 'belitung' => 'Region 7',
            'prabumulih' => 'Region 7', 'pagar alam' => 'Region 7', 'lubuklinggau' => 'Region 7',
            'metro' => 'Region 7', 'dumai' => 'Region 7', 'bukittinggi' => 'Region 7',
        ];

        return $map;
    }

    /**
     * Kamus nama resmi kanonikal 43 Area.
     */
    public static function getCanonicalAreaName(?string $area): string
    {
        if (empty($area)) {
            return '-';
        }

        $clean = strtolower(trim($area));

        $canonicalMap = [
            'surabaya' => 'Surabaya', 'sby' => 'Surabaya',
            'jakarta' => 'Jakarta', 'jkt' => 'Jakarta', 'dki jakarta' => 'Jakarta',
            'bandung' => 'Bandung', 'bdg' => 'Bandung',
            'cirebon' => 'Cirebon', 'crb' => 'Cirebon',
            'jambi' => 'Jambi', 'jmb' => 'Jambi',
            'lampung' => 'Lampung', 'lpg' => 'Lampung', 'bandar lampung' => 'Lampung',
            'padang' => 'Padang', 'pdg' => 'Padang',
            'palembang' => 'Palembang', 'plb' => 'Palembang',
            'pekanbaru' => 'Pekanbaru', 'pkb' => 'Pekanbaru',
            'purwokerto' => 'Purwokerto', 'pwkt' => 'Purwokerto',
            'semarang' => 'Semarang', 'smg' => 'Semarang',
            'solo' => 'Solo', 'surakarta' => 'Solo',
            'yogyakarta' => 'Yogyakarta', 'ygk' => 'Yogyakarta', 'jogja' => 'Yogyakarta',
            'bojonegoro' => 'Bojonegoro', 'bjn' => 'Bojonegoro',
            'buduran' => 'Buduran', 'bdrn' => 'Buduran',
            'denpasar' => 'Denpasar', 'dps' => 'Denpasar', 'bali' => 'Denpasar',
            'jember' => 'Jember', 'jmbr' => 'Jember',
            'kediri' => 'Kediri', 'kdr' => 'Kediri',
            'kupang' => 'Kupang', 'kpg' => 'Kupang',
            'madiun' => 'Madiun', 'mdiun' => 'Madiun',
            'malang' => 'Malang', 'mlg' => 'Malang',
            'mataram' => 'Mataram', 'mtr' => 'Mataram', 'lombok' => 'Mataram',
            'medaeng' => 'Medaeng', 'mdg' => 'Medaeng',
            'pasuruan' => 'Pasuruan', 'psr' => 'Pasuruan',
            'ambon' => 'Ambon', 'ambn' => 'Ambon',
            'makassar' => 'Makassar', 'mks' => 'Makassar',
            'manado' => 'Manado', 'mnd' => 'Manado',
            'palu' => 'Palu',
            'papua' => 'Papua', 'jayapura' => 'Papua',
            'aceh' => 'Aceh', 'banda aceh' => 'Aceh', 'nad aceh' => 'Aceh',
            'batam' => 'Batam', 'btm' => 'Batam',
            'medan' => 'Medan', 'mdn' => 'Medan',
            'pematang siantar' => 'Pematang Siantar', 'pematangsiantar' => 'Pematang Siantar', 'pms' => 'Pematang Siantar',
            'balikpapan' => 'Balikpapan', 'blp' => 'Balikpapan',
            'banjarmasin' => 'Banjarmasin', 'bjm' => 'Banjarmasin',
            'pontianak' => 'Pontianak', 'ptn' => 'Pontianak',
            'samarinda' => 'Samarinda', 'smrd' => 'Samarinda',
            'kudus' => 'Kudus', 'kds' => 'Kudus',
            'tasikmalaya' => 'Tasikmalaya', 'tsm' => 'Tasikmalaya',
            'tegal' => 'Tegal', 'tgl' => 'Tegal',
            'gorontalo' => 'Gorontalo', 'grtl' => 'Gorontalo',
            'kendari' => 'Kendari', 'kndr' => 'Kendari',
            'banyuwangi' => 'Banyuwangi', 'bnyw' => 'Banyuwangi',
        ];

        if (isset($canonicalMap[$clean])) {
            return $canonicalMap[$clean];
        }

        // Jika diawali / mengandung nama kota resmi
        foreach ($canonicalMap as $k => $v) {
            if ($clean === $k || str_starts_with($clean, $k . ' ') || str_ends_with($clean, ' ' . $k)) {
                return $v;
            }
        }

        return ucwords(strtolower(trim($area)));
    }

    /**
     * Resolusi nama region dari area/kota/singkatan.
     */
    public static function resolveRegion(?string $area): string
    {
        if (empty($area) || trim($area) === '-' || strtolower(trim($area)) === 'null') {
            return '-';
        }

        $clean = strtolower(trim($area));
        $map = self::getRegionMap();

        // 1. Exact match di kamus
        if (isset($map[$clean])) {
            return $map[$clean];
        }

        // 2. Cek apakah ada di database tb_area
        try {
            $dbArea = DB::table('tb_area')
                ->whereRaw('LOWER(area) = ?', [$clean])
                ->orWhereRaw('LOWER(singkatan) = ?', [$clean])
                ->first();
            if ($dbArea && !empty($dbArea->region)) {
                return $dbArea->region;
            }
        } catch (\Throwable $e) {
            // fallback
        }

        // 3. Cek di tb_kota
        try {
            $dbKota = DB::table('tb_kota')
                ->whereRaw('LOWER(kota) = ?', [$clean])
                ->first();
            if ($dbKota && !empty($dbKota->region)) {
                return $dbKota->region;
            }
        } catch (\Throwable $e) {
            // fallback
        }

        // 4. Substring matching cerdas (prioritaskan exact token)
        $tokens = preg_split('/[\s,\-\/]+/', $clean);
        foreach ($tokens as $token) {
            if (isset($map[$token])) {
                return $map[$token];
            }
        }

        foreach ($map as $k => $v) {
            if (str_contains($clean, $k)) {
                return $v;
            }
        }

        return '-';
    }
}
