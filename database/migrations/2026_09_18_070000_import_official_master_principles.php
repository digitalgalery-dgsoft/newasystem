<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('principles')) {
            return;
        }

        // 1. Add entity column if not exists
        if (!Schema::hasColumn('principles', 'entity')) {
            Schema::table('principles', function (Blueprint $table) {
                $table->string('entity', 20)->nullable()->after('parent_company');
            });
        }

        $driver = DB::connection()->getDriverName();

        // 2. Drop legacy unique constraint on name if exists
        try {
            if ($driver === 'sqlite') {
                DB::statement("DROP INDEX IF EXISTS principles_name_unique");
            } else {
                DB::statement("ALTER TABLE principles DROP INDEX principles_name_unique");
            }
        } catch (\Throwable $e) {
            // Ignore if index does not exist
        }

        // 3. Clear dummy data from principles
        try {
            if ($driver === 'sqlite') {
                DB::statement('DELETE FROM principles');
                DB::statement("DELETE FROM sqlite_sequence WHERE name='principles'");
            } else {
                DB::statement('SET FOREIGN_KEY_CHECKS = 0');
                DB::table('principles')->truncate();
                DB::statement('SET FOREIGN_KEY_CHECKS = 1');
            }
        } catch (\Throwable $e) {
            DB::table('principles')->delete();
        }

        // 4. Insert 157 official principles
        $principlesData = array (
  0 => 
  array (
    'code' => 'PRN-AMK-001',
    'name' => 'CV SINAR SURYA',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  1 => 
  array (
    'code' => 'PRN-AMK-002',
    'name' => 'PT ADYABUANA PERSADA',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  2 => 
  array (
    'code' => 'PRN-AMK-003',
    'name' => 'PT AJE',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  3 => 
  array (
    'code' => 'PRN-AMK-004',
    'name' => 'PT AMERTA INDAH OTSUKA',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  4 => 
  array (
    'code' => 'PRN-AMK-005',
    'name' => 'PT ANUGRAH PHARMINDO LESTARI',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  5 => 
  array (
    'code' => 'PRN-AMK-006',
    'name' => 'PT ARINA MULTI KARYA',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  6 => 
  array (
    'code' => 'PRN-AMK-007',
    'name' => 'PT ARNOTT`S INDONESIA',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  7 => 
  array (
    'code' => 'PRN-AMK-008',
    'name' => 'PT BLACKHAWK NETWORK INDONESIA',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  8 => 
  array (
    'code' => 'PRN-AMK-009',
    'name' => 'PT BUDGET AMK',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  9 => 
  array (
    'code' => 'PRN-AMK-010',
    'name' => 'PT BUKU USAHA DIGITAL',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  10 => 
  array (
    'code' => 'PRN-AMK-011',
    'name' => 'PT CAHAYA FORTUNA SEJATI',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  11 => 
  array (
    'code' => 'PRN-AMK-012',
    'name' => 'PT CERITA KREATIF INDONESIA',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  12 => 
  array (
    'code' => 'PRN-AMK-013',
    'name' => 'PT CEVA LOGISTIK INDONESIA',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  13 => 
  array (
    'code' => 'PRN-AMK-014',
    'name' => 'PT DELIGHT CONNECTION COSMETICS INDONESIA',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  14 => 
  array (
    'code' => 'PRN-AMK-015',
    'name' => 'PT DEXA MEDICA',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  15 => 
  array (
    'code' => 'PRN-AMK-016',
    'name' => 'PT EXXONMOBIL LUBRICANTS INDONESIA',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  16 => 
  array (
    'code' => 'PRN-AMK-017',
    'name' => 'PT GOTO GOJEK TOKOPEDIA TBK',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  17 => 
  array (
    'code' => 'PRN-AMK-018',
    'name' => 'PT GRAHA MITRA GITA LESTARINDO',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  18 => 
  array (
    'code' => 'PRN-AMK-019',
    'name' => 'PT ICI PAINTS INDONESIA',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  19 => 
  array (
    'code' => 'PRN-AMK-020',
    'name' => 'PT JINGXING WEISS INDONESIA',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  20 => 
  array (
    'code' => 'PRN-AMK-021',
    'name' => 'PT JOHNSON HOME HYGIENE PRODUCT',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  21 => 
  array (
    'code' => 'PRN-AMK-022',
    'name' => 'PT KENCANA RITELINDO INDONESIA',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  22 => 
  array (
    'code' => 'PRN-AMK-023',
    'name' => 'PT KINO FACTORY',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  23 => 
  array (
    'code' => 'PRN-AMK-024',
    'name' => 'PT KINO INDONESIA TBK',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  24 => 
  array (
    'code' => 'PRN-AMK-025',
    'name' => 'PT LF Services Indonesia',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  25 => 
  array (
    'code' => 'PRN-AMK-026',
    'name' => 'PT LION WINGS',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  26 => 
  array (
    'code' => 'PRN-AMK-027',
    'name' => 'PT LOREAL INDONESIA',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  27 => 
  array (
    'code' => 'PRN-AMK-028',
    'name' => 'PT MATAHARI DEPARTMENT STORE Tbk',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  28 => 
  array (
    'code' => 'PRN-AMK-029',
    'name' => 'PT MAYINDO TRITUNGGAL',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  29 => 
  array (
    'code' => 'PRN-AMK-030',
    'name' => 'PT MEDIKON PRIMA LABORATORIES',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  30 => 
  array (
    'code' => 'PRN-AMK-031',
    'name' => 'PT NESTLE GEMPOL',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  31 => 
  array (
    'code' => 'PRN-AMK-032',
    'name' => 'PT NESTLE INDONESIA',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  32 => 
  array (
    'code' => 'PRN-AMK-033',
    'name' => 'PT OTSUKA INDONESIA',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  33 => 
  array (
    'code' => 'PRN-AMK-034',
    'name' => 'PT PABRIK MINYAK PERNIAGAAN DAN INDUSTRI IKAN DORANG',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  34 => 
  array (
    'code' => 'PRN-AMK-035',
    'name' => 'PT PHILIPS INDONESIA COMMERCIAL',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  35 => 
  array (
    'code' => 'PRN-AMK-036',
    'name' => 'PT SAMATOR GAS INDUSTRI',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  36 => 
  array (
    'code' => 'PRN-AMK-037',
    'name' => 'PT SAMSUNG ELECTRONICS INDONESIA',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  37 => 
  array (
    'code' => 'PRN-AMK-038',
    'name' => 'PT SANGHIANG PERKASA (AMK)',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  38 => 
  array (
    'code' => 'PRN-AMK-039',
    'name' => 'PT SANGHIANG PERKASA (BR)',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  39 => 
  array (
    'code' => 'PRN-AMK-040',
    'name' => 'PT SANGHIANG PERKASA (Ex Indodaya)',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  40 => 
  array (
    'code' => 'PRN-AMK-041',
    'name' => 'PT SANTOS JAYA ABADI',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  41 => 
  array (
    'code' => 'PRN-AMK-042',
    'name' => 'PT SARINAH',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  42 => 
  array (
    'code' => 'PRN-AMK-043',
    'name' => 'PT SASA HOUSEFOODS INDONESIA',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  43 => 
  array (
    'code' => 'PRN-AMK-044',
    'name' => 'PT SASA INTI',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  44 => 
  array (
    'code' => 'PRN-AMK-045',
    'name' => 'PT SATORIA',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  45 => 
  array (
    'code' => 'PRN-AMK-046',
    'name' => 'PT SAVORIA KREASI RASA',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  46 => 
  array (
    'code' => 'PRN-AMK-047',
    'name' => 'PT SCJ',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  47 => 
  array (
    'code' => 'PRN-AMK-048',
    'name' => 'PT SIDO AGUNG ALUMI',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  48 => 
  array (
    'code' => 'PRN-AMK-049',
    'name' => 'PT SIGNIFY COMMERCIAL INDONESIA',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  49 => 
  array (
    'code' => 'PRN-AMK-050',
    'name' => 'PT SREEYA SEWU INDONESIA',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  50 => 
  array (
    'code' => 'PRN-AMK-051',
    'name' => 'PT SUMBER KARYA SEJATI',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  51 => 
  array (
    'code' => 'PRN-AMK-052',
    'name' => 'PT SUNTORY BEVERAGE AND FOOD INDONESIA',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  52 => 
  array (
    'code' => 'PRN-AMK-053',
    'name' => 'PT TORYS CREATIA INDONESIA',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  53 => 
  array (
    'code' => 'PRN-AMK-054',
    'name' => 'PT TUMBAKMAS NIAGASAKTI',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  54 => 
  array (
    'code' => 'PRN-AMK-055',
    'name' => 'PT UNICHEMCANDI INDONESIA',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  55 => 
  array (
    'code' => 'PRN-AMK-056',
    'name' => 'PT UNILEVER INDONESIA',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  56 => 
  array (
    'code' => 'PRN-AMK-057',
    'name' => 'PT VERSUNI HOMELIFE INDONESIA',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  57 => 
  array (
    'code' => 'PRN-AMK-058',
    'name' => 'PT WINGS SURYA',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  58 => 
  array (
    'code' => 'PRN-AMK-059',
    'name' => 'PT XL AXIATA Tbk',
    'entity' => 'AMK',
    'parent_company' => 'PT ARINA MULTI KARYA',
  ),
  59 => 
  array (
    'code' => 'PRN-AKP-001',
    'name' => 'PT ABC PRESIDENT INDONESIA',
    'entity' => 'AKP',
    'parent_company' => 'PT ALVA KARYA PERKASA',
  ),
  60 => 
  array (
    'code' => 'PRN-AKP-002',
    'name' => 'PT ADYABUANA PERSADA',
    'entity' => 'AKP',
    'parent_company' => 'PT ALVA KARYA PERKASA',
  ),
  61 => 
  array (
    'code' => 'PRN-AKP-003',
    'name' => 'PT AKAR DAYA',
    'entity' => 'AKP',
    'parent_company' => 'PT ALVA KARYA PERKASA',
  ),
  62 => 
  array (
    'code' => 'PRN-AKP-004',
    'name' => 'PT ALLIANCE COSMETICS',
    'entity' => 'AKP',
    'parent_company' => 'PT ALVA KARYA PERKASA',
  ),
  63 => 
  array (
    'code' => 'PRN-AKP-005',
    'name' => 'PT ALVA KARYA PERKASA',
    'entity' => 'AKP',
    'parent_company' => 'PT ALVA KARYA PERKASA',
  ),
  64 => 
  array (
    'code' => 'PRN-AKP-006',
    'name' => 'PT AMERTA INDAH OTSUKA',
    'entity' => 'AKP',
    'parent_company' => 'PT ALVA KARYA PERKASA',
  ),
  65 => 
  array (
    'code' => 'PRN-AKP-007',
    'name' => 'PT BLACKHAWK NETWORK INDONESIA',
    'entity' => 'AKP',
    'parent_company' => 'PT ALVA KARYA PERKASA',
  ),
  66 => 
  array (
    'code' => 'PRN-AKP-008',
    'name' => 'PT BUDGET AKP',
    'entity' => 'AKP',
    'parent_company' => 'PT ALVA KARYA PERKASA',
  ),
  67 => 
  array (
    'code' => 'PRN-AKP-009',
    'name' => 'PT CEMINDO GEMILANG Tbk',
    'entity' => 'AKP',
    'parent_company' => 'PT ALVA KARYA PERKASA',
  ),
  68 => 
  array (
    'code' => 'PRN-AKP-010',
    'name' => 'PT CENTRAL UTAMA INDOWARNA',
    'entity' => 'AKP',
    'parent_company' => 'PT ALVA KARYA PERKASA',
  ),
  69 => 
  array (
    'code' => 'PRN-AKP-011',
    'name' => 'PT CERAH SEMESTA GEMILANG',
    'entity' => 'AKP',
    'parent_company' => 'PT ALVA KARYA PERKASA',
  ),
  70 => 
  array (
    'code' => 'PRN-AKP-012',
    'name' => 'PT COLGATE-PALMOLIVE INDONESIA',
    'entity' => 'AKP',
    'parent_company' => 'PT ALVA KARYA PERKASA',
  ),
  71 => 
  array (
    'code' => 'PRN-AKP-013',
    'name' => 'PT GALDERMA INDONESIA HEALTHCARE',
    'entity' => 'AKP',
    'parent_company' => 'PT ALVA KARYA PERKASA',
  ),
  72 => 
  array (
    'code' => 'PRN-AKP-014',
    'name' => 'PT ICI PAINT ALVA (DGO)',
    'entity' => 'AKP',
    'parent_company' => 'PT ALVA KARYA PERKASA',
  ),
  73 => 
  array (
    'code' => 'PRN-AKP-015',
    'name' => 'PT ICI PAINTS INDONESIA',
    'entity' => 'AKP',
    'parent_company' => 'PT ALVA KARYA PERKASA',
  ),
  74 => 
  array (
    'code' => 'PRN-AKP-016',
    'name' => 'PT ICI PAINT TSM',
    'entity' => 'AKP',
    'parent_company' => 'PT ALVA KARYA PERKASA',
  ),
  75 => 
  array (
    'code' => 'PRN-AKP-017',
    'name' => 'PT INDOCARE CITRAPASIFIC',
    'entity' => 'AKP',
    'parent_company' => 'PT ALVA KARYA PERKASA',
  ),
  76 => 
  array (
    'code' => 'PRN-AKP-018',
    'name' => 'PT INDOFOOD CBP SUKSES MAKMUR TBK',
    'entity' => 'AKP',
    'parent_company' => 'PT ALVA KARYA PERKASA',
  ),
  77 => 
  array (
    'code' => 'PRN-AKP-019',
    'name' => 'PT INTEGRATED HEALTHCARE INDONESIA',
    'entity' => 'AKP',
    'parent_company' => 'PT ALVA KARYA PERKASA',
  ),
  78 => 
  array (
    'code' => 'PRN-AKP-020',
    'name' => 'PT JAPFA FOOD INDONESIA',
    'entity' => 'AKP',
    'parent_company' => 'PT ALVA KARYA PERKASA',
  ),
  79 => 
  array (
    'code' => 'PRN-AKP-021',
    'name' => 'PT JOHNSON HOME HYGIENE PRODUCT',
    'entity' => 'AKP',
    'parent_company' => 'PT ALVA KARYA PERKASA',
  ),
  80 => 
  array (
    'code' => 'PRN-AKP-022',
    'name' => 'PT JOHNSON & JOHNSON',
    'entity' => 'AKP',
    'parent_company' => 'PT ALVA KARYA PERKASA',
  ),
  81 => 
  array (
    'code' => 'PRN-AKP-023',
    'name' => 'PT KONIMEX',
    'entity' => 'AKP',
    'parent_company' => 'PT ALVA KARYA PERKASA',
  ),
  82 => 
  array (
    'code' => 'PRN-AKP-024',
    'name' => 'PT LENOVO INDONESIA',
    'entity' => 'AKP',
    'parent_company' => 'PT ALVA KARYA PERKASA',
  ),
  83 => 
  array (
    'code' => 'PRN-AKP-025',
    'name' => 'PT LION WINGS',
    'entity' => 'AKP',
    'parent_company' => 'PT ALVA KARYA PERKASA',
  ),
  84 => 
  array (
    'code' => 'PRN-AKP-026',
    'name' => 'PT LOTTE INDONESIA',
    'entity' => 'AKP',
    'parent_company' => 'PT ALVA KARYA PERKASA',
  ),
  85 => 
  array (
    'code' => 'PRN-AKP-027',
    'name' => 'PT MARGA NUSANTARA JAYA',
    'entity' => 'AKP',
    'parent_company' => 'PT ALVA KARYA PERKASA',
  ),
  86 => 
  array (
    'code' => 'PRN-AKP-028',
    'name' => 'PT MARKETAMA INDAH',
    'entity' => 'AKP',
    'parent_company' => 'PT ALVA KARYA PERKASA',
  ),
  87 => 
  array (
    'code' => 'PRN-AKP-029',
    'name' => 'PT MEGA ADHITAMA SEJATI',
    'entity' => 'AKP',
    'parent_company' => 'PT ALVA KARYA PERKASA',
  ),
  88 => 
  array (
    'code' => 'PRN-AKP-030',
    'name' => 'PT MOTOROLA MOBILITY INDONESIA',
    'entity' => 'AKP',
    'parent_company' => 'PT ALVA KARYA PERKASA',
  ),
  89 => 
  array (
    'code' => 'PRN-AKP-031',
    'name' => 'PT NUTRICIA INDONESIA SEJAHTERA',
    'entity' => 'AKP',
    'parent_company' => 'PT ALVA KARYA PERKASA',
  ),
  90 => 
  array (
    'code' => 'PRN-AKP-032',
    'name' => 'PT OTSUKA INDONESIA',
    'entity' => 'AKP',
    'parent_company' => 'PT ALVA KARYA PERKASA',
  ),
  91 => 
  array (
    'code' => 'PRN-AKP-033',
    'name' => 'PT REKSA TRANSAKSI SUKSES MAKMUR',
    'entity' => 'AKP',
    'parent_company' => 'PT ALVA KARYA PERKASA',
  ),
  92 => 
  array (
    'code' => 'PRN-AKP-034',
    'name' => 'PT ROSE COSMETICS INTERNATIONAL',
    'entity' => 'AKP',
    'parent_company' => 'PT ALVA KARYA PERKASA',
  ),
  93 => 
  array (
    'code' => 'PRN-AKP-035',
    'name' => 'PT SANTOSA AGRINDO',
    'entity' => 'AKP',
    'parent_company' => 'PT ALVA KARYA PERKASA',
  ),
  94 => 
  array (
    'code' => 'PRN-AKP-036',
    'name' => 'PT SARIHUSADA GENERASI MAHARDHIKA',
    'entity' => 'AKP',
    'parent_company' => 'PT ALVA KARYA PERKASA',
  ),
  95 => 
  array (
    'code' => 'PRN-AKP-037',
    'name' => 'PT SAYAP MAS UTAMA',
    'entity' => 'AKP',
    'parent_company' => 'PT ALVA KARYA PERKASA',
  ),
  96 => 
  array (
    'code' => 'PRN-AKP-038',
    'name' => 'PT SHISEIDO COSMETICS INDONESIA',
    'entity' => 'AKP',
    'parent_company' => 'PT ALVA KARYA PERKASA',
  ),
  97 => 
  array (
    'code' => 'PRN-AKP-039',
    'name' => 'PT SIGNIFY COMMERCIAL INDONESIA',
    'entity' => 'AKP',
    'parent_company' => 'PT ALVA KARYA PERKASA',
  ),
  98 => 
  array (
    'code' => 'PRN-AKP-040',
    'name' => 'PT SINAR ABADI PAPUA',
    'entity' => 'AKP',
    'parent_company' => 'PT ALVA KARYA PERKASA',
  ),
  99 => 
  array (
    'code' => 'PRN-AKP-041',
    'name' => 'PT SINARMITRA FORTUNA KERAMINDO',
    'entity' => 'AKP',
    'parent_company' => 'PT ALVA KARYA PERKASA',
  ),
  100 => 
  array (
    'code' => 'PRN-AKP-042',
    'name' => 'PT SO GOOD FOOD',
    'entity' => 'AKP',
    'parent_company' => 'PT ALVA KARYA PERKASA',
  ),
  101 => 
  array (
    'code' => 'PRN-AKP-043',
    'name' => 'PT SOHO INDUSTRI PHARMASI',
    'entity' => 'AKP',
    'parent_company' => 'PT ALVA KARYA PERKASA',
  ),
  102 => 
  array (
    'code' => 'PRN-AKP-044',
    'name' => 'PT SUN PAPER SOURCE',
    'entity' => 'AKP',
    'parent_company' => 'PT ALVA KARYA PERKASA',
  ),
  103 => 
  array (
    'code' => 'PRN-AKP-045',
    'name' => 'PT SYNNEX METRODATA INDONESIA',
    'entity' => 'AKP',
    'parent_company' => 'PT ALVA KARYA PERKASA',
  ),
  104 => 
  array (
    'code' => 'PRN-AKP-046',
    'name' => 'PT TIGARAKSA SATRIA TBK',
    'entity' => 'AKP',
    'parent_company' => 'PT ALVA KARYA PERKASA',
  ),
  105 => 
  array (
    'code' => 'PRN-AKP-047',
    'name' => 'PT TIRTA INVESTAMA',
    'entity' => 'AKP',
    'parent_company' => 'PT ALVA KARYA PERKASA',
  ),
  106 => 
  array (
    'code' => 'PRN-AKP-048',
    'name' => 'PT TUMBAKMAS NIAGASAKTI',
    'entity' => 'AKP',
    'parent_company' => 'PT ALVA KARYA PERKASA',
  ),
  107 => 
  array (
    'code' => 'PRN-AKP-049',
    'name' => 'PT UNZA VITALIS',
    'entity' => 'AKP',
    'parent_company' => 'PT ALVA KARYA PERKASA',
  ),
  108 => 
  array (
    'code' => 'PRN-AKP-050',
    'name' => 'PT WINGS SURYA',
    'entity' => 'AKP',
    'parent_company' => 'PT ALVA KARYA PERKASA',
  ),
  109 => 
  array (
    'code' => 'PRN-ATK-001',
    'name' => 'PT ABC PRESIDENT INDONESIA',
    'entity' => 'ATK',
    'parent_company' => 'PT ANUGRAH TERPERCAYA KERJA',
  ),
  110 => 
  array (
    'code' => 'PRN-ATK-002',
    'name' => 'PT ANUGRAH TERPERCAYA KERJA',
    'entity' => 'ATK',
    'parent_company' => 'PT ANUGRAH TERPERCAYA KERJA',
  ),
  111 => 
  array (
    'code' => 'PRN-ATK-003',
    'name' => 'PT BEIERSDORF INDONESIA (NON AKTIF)',
    'entity' => 'ATK',
    'parent_company' => 'PT ANUGRAH TERPERCAYA KERJA',
  ),
  112 => 
  array (
    'code' => 'PRN-ATK-004',
    'name' => 'PT BUDGET ATK',
    'entity' => 'ATK',
    'parent_company' => 'PT ANUGRAH TERPERCAYA KERJA',
  ),
  113 => 
  array (
    'code' => 'PRN-ATK-005',
    'name' => 'PT DAESANG AGUNG INDONESIA',
    'entity' => 'ATK',
    'parent_company' => 'PT ANUGRAH TERPERCAYA KERJA',
  ),
  114 => 
  array (
    'code' => 'PRN-ATK-006',
    'name' => 'PT DARYA VARIA (NON AKTIF)',
    'entity' => 'ATK',
    'parent_company' => 'PT ANUGRAH TERPERCAYA KERJA',
  ),
  115 => 
  array (
    'code' => 'PRN-ATK-007',
    'name' => 'PT DELIGHT CONNECTION COSMETICS INDONESIA',
    'entity' => 'ATK',
    'parent_company' => 'PT ANUGRAH TERPERCAYA KERJA',
  ),
  116 => 
  array (
    'code' => 'PRN-ATK-008',
    'name' => 'PT DELTOMED LABORATORIES',
    'entity' => 'ATK',
    'parent_company' => 'PT ANUGRAH TERPERCAYA KERJA',
  ),
  117 => 
  array (
    'code' => 'PRN-ATK-009',
    'name' => 'PT DHARMA PERKASA GEMILANG',
    'entity' => 'ATK',
    'parent_company' => 'PT ANUGRAH TERPERCAYA KERJA',
  ),
  118 => 
  array (
    'code' => 'PRN-ATK-010',
    'name' => 'PT FONTERRA BRANDS MANUFACTURING INDONESIA',
    'entity' => 'ATK',
    'parent_company' => 'PT ANUGRAH TERPERCAYA KERJA',
  ),
  119 => 
  array (
    'code' => 'PRN-ATK-011',
    'name' => 'PT INDOCARE CITRAPASIFIC',
    'entity' => 'ATK',
    'parent_company' => 'PT ANUGRAH TERPERCAYA KERJA',
  ),
  120 => 
  array (
    'code' => 'PRN-ATK-012',
    'name' => 'PT JAYA ERNANDO KARYA',
    'entity' => 'ATK',
    'parent_company' => 'PT ANUGRAH TERPERCAYA KERJA',
  ),
  121 => 
  array (
    'code' => 'PRN-ATK-013',
    'name' => 'PT JEMBATAN AKAR TEKNOLOGI',
    'entity' => 'ATK',
    'parent_company' => 'PT ANUGRAH TERPERCAYA KERJA',
  ),
  122 => 
  array (
    'code' => 'PRN-ATK-014',
    'name' => 'PT KAO INDONESIA',
    'entity' => 'ATK',
    'parent_company' => 'PT ANUGRAH TERPERCAYA KERJA',
  ),
  123 => 
  array (
    'code' => 'PRN-ATK-015',
    'name' => 'PT KEVA COSMETICS INTERNATIONAL',
    'entity' => 'ATK',
    'parent_company' => 'PT ANUGRAH TERPERCAYA KERJA',
  ),
  124 => 
  array (
    'code' => 'PRN-ATK-016',
    'name' => 'PT KONIMEX',
    'entity' => 'ATK',
    'parent_company' => 'PT ANUGRAH TERPERCAYA KERJA',
  ),
  125 => 
  array (
    'code' => 'PRN-ATK-017',
    'name' => 'PT LOREAL INDONESIA',
    'entity' => 'ATK',
    'parent_company' => 'PT ANUGRAH TERPERCAYA KERJA',
  ),
  126 => 
  array (
    'code' => 'PRN-ATK-018',
    'name' => 'PT MARGA NUSANTARA JAYA',
    'entity' => 'ATK',
    'parent_company' => 'PT ANUGRAH TERPERCAYA KERJA',
  ),
  127 => 
  array (
    'code' => 'PRN-ATK-019',
    'name' => 'PT MAY SUN YVAN',
    'entity' => 'ATK',
    'parent_company' => 'PT ANUGRAH TERPERCAYA KERJA',
  ),
  128 => 
  array (
    'code' => 'PRN-ATK-020',
    'name' => 'PT MEDIKON',
    'entity' => 'ATK',
    'parent_company' => 'PT ANUGRAH TERPERCAYA KERJA',
  ),
  129 => 
  array (
    'code' => 'PRN-ATK-021',
    'name' => 'PT MULIAKERAMIK INDAHRAYA',
    'entity' => 'ATK',
    'parent_company' => 'PT ANUGRAH TERPERCAYA KERJA',
  ),
  130 => 
  array (
    'code' => 'PRN-ATK-022',
    'name' => 'PT NIRWANA LESTARI',
    'entity' => 'ATK',
    'parent_company' => 'PT ANUGRAH TERPERCAYA KERJA',
  ),
  131 => 
  array (
    'code' => 'PRN-ATK-023',
    'name' => 'PT ONDA MEGA INTEGRA',
    'entity' => 'ATK',
    'parent_company' => 'PT ANUGRAH TERPERCAYA KERJA',
  ),
  132 => 
  array (
    'code' => 'PRN-ATK-024',
    'name' => 'PT PARAGON TECHNOLOGY AND INNOVATION',
    'entity' => 'ATK',
    'parent_company' => 'PT ANUGRAH TERPERCAYA KERJA',
  ),
  133 => 
  array (
    'code' => 'PRN-ATK-025',
    'name' => 'PT PERFETTI VAN MELLE INDONESIA',
    'entity' => 'ATK',
    'parent_company' => 'PT ANUGRAH TERPERCAYA KERJA',
  ),
  134 => 
  array (
    'code' => 'PRN-ATK-026',
    'name' => 'PT REKSA TRANSAKSI SUKSES MAKMUR (NON AKTIF)',
    'entity' => 'ATK',
    'parent_company' => 'PT ANUGRAH TERPERCAYA KERJA',
  ),
  135 => 
  array (
    'code' => 'PRN-ATK-027',
    'name' => 'PT SAMATOR GAS INDUSTRI',
    'entity' => 'ATK',
    'parent_company' => 'PT ANUGRAH TERPERCAYA KERJA',
  ),
  136 => 
  array (
    'code' => 'PRN-ATK-028',
    'name' => 'PT SANGHIANG PERKASA',
    'entity' => 'ATK',
    'parent_company' => 'PT ANUGRAH TERPERCAYA KERJA',
  ),
  137 => 
  array (
    'code' => 'PRN-ATK-029',
    'name' => 'PT SEMESTA DISTRIBUSI INDONESIA',
    'entity' => 'ATK',
    'parent_company' => 'PT ANUGRAH TERPERCAYA KERJA',
  ),
  138 => 
  array (
    'code' => 'PRN-ATK-030',
    'name' => 'PT SUNTONE WISDOM INDONESIA',
    'entity' => 'ATK',
    'parent_company' => 'PT ANUGRAH TERPERCAYA KERJA',
  ),
  139 => 
  array (
    'code' => 'PRN-ATK-031',
    'name' => 'PT THE MAGNUM ICE CREAM INDONESIA',
    'entity' => 'ATK',
    'parent_company' => 'PT ANUGRAH TERPERCAYA KERJA',
  ),
  140 => 
  array (
    'code' => 'PRN-ATK-032',
    'name' => 'PT UNILEVER INDONESIA',
    'entity' => 'ATK',
    'parent_company' => 'PT ANUGRAH TERPERCAYA KERJA',
  ),
  141 => 
  array (
    'code' => 'PRN-ATK-033',
    'name' => 'PT UNZA VITALIS',
    'entity' => 'ATK',
    'parent_company' => 'PT ANUGRAH TERPERCAYA KERJA',
  ),
  142 => 
  array (
    'code' => 'PRN-ABO-001',
    'name' => 'PT ABADI BERKAT ODELIA',
    'entity' => 'ABO',
    'parent_company' => 'PT ABADI BERKAT ODELIA',
  ),
  143 => 
  array (
    'code' => 'PRN-ABO-002',
    'name' => 'PT FORISA NUSAPERSADA',
    'entity' => 'ABO',
    'parent_company' => 'PT ABADI BERKAT ODELIA',
  ),
  144 => 
  array (
    'code' => 'PRN-ABO-003',
    'name' => 'PT GODREJ INDONESIA',
    'entity' => 'ABO',
    'parent_company' => 'PT ABADI BERKAT ODELIA',
  ),
  145 => 
  array (
    'code' => 'PRN-ABO-004',
    'name' => 'PT LENOVO INDONESIA',
    'entity' => 'ABO',
    'parent_company' => 'PT ABADI BERKAT ODELIA',
  ),
  146 => 
  array (
    'code' => 'PRN-ABO-005',
    'name' => 'PT MAY SUN YVAN',
    'entity' => 'ABO',
    'parent_company' => 'PT ABADI BERKAT ODELIA',
  ),
  147 => 
  array (
    'code' => 'PRN-ABO-006',
    'name' => 'PT SEMESTA DISTRIBUSI INDONESIA',
    'entity' => 'ABO',
    'parent_company' => 'PT ABADI BERKAT ODELIA',
  ),
  148 => 
  array (
    'code' => 'PRN-ABO-007',
    'name' => 'PT SIDO AGUNG ALUMI',
    'entity' => 'ABO',
    'parent_company' => 'PT ABADI BERKAT ODELIA',
  ),
  149 => 
  array (
    'code' => 'PRN-ABO-008',
    'name' => 'PT SUNTONE WISDOM INDONESIA',
    'entity' => 'ABO',
    'parent_company' => 'PT ABADI BERKAT ODELIA',
  ),
  150 => 
  array (
    'code' => 'PRN-ABO-009',
    'name' => 'PT UNITED FAMILY FOOD',
    'entity' => 'ABO',
    'parent_company' => 'PT ABADI BERKAT ODELIA',
  ),
  151 => 
  array (
    'code' => 'PRN-ABO-010',
    'name' => 'PT VINDA INTERNASIONAL INDONESIA',
    'entity' => 'ABO',
    'parent_company' => 'PT ABADI BERKAT ODELIA',
  ),
  152 => 
  array (
    'code' => 'PRN-ATB-001',
    'name' => 'PT ANUGRAH TALENTA BERKARYA',
    'entity' => 'ATB',
    'parent_company' => 'PT ANUGRAH TALENTA BERKARYA',
  ),
  153 => 
  array (
    'code' => 'PRN-ATB-002',
    'name' => 'PT LION WINGS',
    'entity' => 'ATB',
    'parent_company' => 'PT ANUGRAH TALENTA BERKARYA',
  ),
  154 => 
  array (
    'code' => 'PRN-ATB-003',
    'name' => 'PT SAYAP MAS UTAMA',
    'entity' => 'ATB',
    'parent_company' => 'PT ANUGRAH TALENTA BERKARYA',
  ),
  155 => 
  array (
    'code' => 'PRN-ATB-004',
    'name' => 'PT SINAR ABADI PAPUA',
    'entity' => 'ATB',
    'parent_company' => 'PT ANUGRAH TALENTA BERKARYA',
  ),
  156 => 
  array (
    'code' => 'PRN-ATB-005',
    'name' => 'PT WINGS SURYA',
    'entity' => 'ATB',
    'parent_company' => 'PT ANUGRAH TALENTA BERKARYA',
  ),
);

        $now = date('Y-m-d H:i:s');
        $insertRows = [];
        foreach ($principlesData as $p) {
            $insertRows[] = [
                'code' => $p['code'],
                'name' => $p['name'],
                'parent_company' => $p['parent_company'],
                'entity' => $p['entity'],
                'pic_name' => null,
                'pic_email' => null,
                'pic_phone' => null,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Insert in chunks of 50
        foreach (array_chunk($insertRows, 50) as $chunk) {
            DB::table('principles')->insert($chunk);
        }

        // 5. Re-link candidates.principle_id
        if (Schema::hasTable('candidates')) {
            $allNewPrinciples = DB::table('principles')->get();

            foreach ($allNewPrinciples as $np) {
                // Match exact name
                DB::table('candidates')
                    ->where('principle', $np->name)
                    ->update(['principle_id' => $np->id]);

                // Match name with entity tag, e.g. "PT WINGS SURYA (ATB)" -> entity ATB
                $withEntity = $np->name . ' (' . $np->entity . ')';
                DB::table('candidates')
                    ->where('principle', $withEntity)
                    ->update(['principle_id' => $np->id]);
            }

            // Match common aliases / slight naming variations
            $aliases = [
                'PT SAYAP MAS' => 'PT SAYAP MAS UTAMA',
                'PT SAYAP MAS (ATB)' => 'PT SAYAP MAS UTAMA',
                'PT ICI PAINT ALVA' => 'PT ICI PAINTS INDONESIA',
                'PT ICI PAINT ARINA' => 'PT ICI PAINTS INDONESIA',
                'PT VINDA INTERNATIONAL INDONESIA' => 'PT VINDA INTERNASIONAL INDONESIA',
                'PT SANGHIANG PERKASA (AMK)' => 'PT SANGHIANG PERKASA',
                'PT SANGHIANG PERKASA (ATK)' => 'PT SANGHIANG PERKASA',
                'PT SUNTONE WISDOM INDONESIA (ABO)' => 'PT SUNTONE WISDOM INDONESIA',
                'PT LENOVO INDONESIA (ABO)' => 'PT LENOVO INDONESIA',
                'PT MAY SUN YVAN (ABO)' => 'PT MAY SUN YVAN',
                'PT SEMESTA DISTRIBUSI INDONESIA (ABO)' => 'PT SEMESTA DISTRIBUSI INDONESIA',
                'PT DELIGHT CONNECTION COSMETICS INDONESIA (ATK)' => 'PT DELIGHT CONNECTION COSMETICS INDONESIA',
            ];

            foreach ($aliases as $alias => $officialName) {
                $target = DB::table('principles')->where('name', $officialName)->first();
                if ($target) {
                    DB::table('candidates')
                        ->where('principle', $alias)
                        ->whereNull('principle_id')
                        ->update(['principle_id' => $target->id]);
                }
            }
        }

        // 6. Re-link user_prinsiples
        if (Schema::hasTable('user_prinsiples') && Schema::hasColumn('user_prinsiples', 'prinsiple_id')) {
            $allNewPrinciples = DB::table('principles')->get();
            foreach ($allNewPrinciples as $np) {
                DB::table('user_prinsiples')
                    ->where('prinsiple', $np->name)
                    ->update(['prinsiple_id' => $np->id]);

                $withEntity = $np->name . ' (' . $np->entity . ')';
                DB::table('user_prinsiples')
                    ->where('prinsiple', $withEntity)
                    ->update(['prinsiple_id' => $np->id]);
            }
        }
    }

    public function down(): void
    {
        // Keep principles table intact on rollback
    }
};