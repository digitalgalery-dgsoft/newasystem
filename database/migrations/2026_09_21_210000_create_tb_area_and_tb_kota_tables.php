<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('tb_area')) {
            Schema::create('tb_area', function (Blueprint $table) {
                $table->integer('kode')->primary();
                $table->integer('kode_area')->nullable();
                $table->string('area', 100)->index();
                $table->string('region', 50)->index();
                $table->integer('hk')->default(25);
                $table->string('singkatan', 20)->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('tb_kota')) {
            Schema::create('tb_kota', function (Blueprint $table) {
                $table->integer('kode')->primary();
                $table->string('kota', 150)->index();
                $table->string('propinsi', 150)->index();
                $table->string('region', 50)->index();
                $table->timestamps();
            });
        }

        // Seed tb_area
        if (DB::table('tb_area')->count() === 0) {
            $areas = array (
  0 => 
  array (
    'kode' => 1,
    'kode_area' => 8477,
    'area' => 'Surabaya',
    'region' => 'Region 4',
    'hk' => 21,
    'singkatan' => 'SBY',
  ),
  1 => 
  array (
    'kode' => 2,
    'kode_area' => 4930,
    'area' => 'Jakarta',
    'region' => 'Region 1',
    'hk' => 21,
    'singkatan' => 'JKT',
  ),
  2 => 
  array (
    'kode' => 3,
    'kode_area' => 7975,
    'area' => 'Bandung',
    'region' => 'Region 2',
    'hk' => 25,
    'singkatan' => 'BDG',
  ),
  3 => 
  array (
    'kode' => 4,
    'kode_area' => 7863,
    'area' => 'Cirebon',
    'region' => 'Region 2',
    'hk' => 25,
    'singkatan' => 'CRB',
  ),
  4 => 
  array (
    'kode' => 5,
    'kode_area' => 1591,
    'area' => 'Jambi',
    'region' => 'Region 7',
    'hk' => 25,
    'singkatan' => 'JMB',
  ),
  5 => 
  array (
    'kode' => 6,
    'kode_area' => 5793,
    'area' => 'Lampung',
    'region' => 'Region 7',
    'hk' => 25,
    'singkatan' => 'LPG',
  ),
  6 => 
  array (
    'kode' => 7,
    'kode_area' => 7682,
    'area' => 'Padang',
    'region' => 'Region 7',
    'hk' => 25,
    'singkatan' => 'PDG',
  ),
  7 => 
  array (
    'kode' => 8,
    'kode_area' => 3396,
    'area' => 'Palembang',
    'region' => 'Region 7',
    'hk' => 25,
    'singkatan' => 'PLB',
  ),
  8 => 
  array (
    'kode' => 9,
    'kode_area' => 1510,
    'area' => 'Pekanbaru',
    'region' => 'Region 7',
    'hk' => 25,
    'singkatan' => 'PKB',
  ),
  9 => 
  array (
    'kode' => 10,
    'kode_area' => 9154,
    'area' => 'Purwokerto',
    'region' => 'Region 3',
    'hk' => 25,
    'singkatan' => 'PWKT',
  ),
  10 => 
  array (
    'kode' => 11,
    'kode_area' => 2296,
    'area' => 'Semarang',
    'region' => 'Region 3',
    'hk' => 25,
    'singkatan' => 'SMG',
  ),
  11 => 
  array (
    'kode' => 12,
    'kode_area' => 2917,
    'area' => 'Solo',
    'region' => 'Region 3',
    'hk' => 25,
    'singkatan' => 'SOLO',
  ),
  12 => 
  array (
    'kode' => 13,
    'kode_area' => 3415,
    'area' => 'Yogyakarta',
    'region' => 'Region 3',
    'hk' => 25,
    'singkatan' => 'YGK',
  ),
  13 => 
  array (
    'kode' => 14,
    'kode_area' => 9779,
    'area' => 'Bojonegoro',
    'region' => 'Region 4',
    'hk' => 25,
    'singkatan' => 'BJN',
  ),
  14 => 
  array (
    'kode' => 15,
    'kode_area' => 5811,
    'area' => 'Buduran',
    'region' => 'Region 4',
    'hk' => 25,
    'singkatan' => 'BDRN',
  ),
  15 => 
  array (
    'kode' => 16,
    'kode_area' => 4546,
    'area' => 'Denpasar',
    'region' => 'Region 4',
    'hk' => 25,
    'singkatan' => 'DPS',
  ),
  16 => 
  array (
    'kode' => 17,
    'kode_area' => 3975,
    'area' => 'Jember',
    'region' => 'Region 4',
    'hk' => 25,
    'singkatan' => 'JMBR',
  ),
  17 => 
  array (
    'kode' => 18,
    'kode_area' => 5059,
    'area' => 'Kediri',
    'region' => 'Region 4',
    'hk' => 25,
    'singkatan' => 'KDR',
  ),
  18 => 
  array (
    'kode' => 19,
    'kode_area' => 5841,
    'area' => 'Kupang',
    'region' => 'Region 4',
    'hk' => 25,
    'singkatan' => 'KPG',
  ),
  19 => 
  array (
    'kode' => 20,
    'kode_area' => 2314,
    'area' => 'Madiun',
    'region' => 'Region 4',
    'hk' => 25,
    'singkatan' => 'MDIUN',
  ),
  20 => 
  array (
    'kode' => 21,
    'kode_area' => 1655,
    'area' => 'Malang',
    'region' => 'Region 4',
    'hk' => 25,
    'singkatan' => 'MLG',
  ),
  21 => 
  array (
    'kode' => 22,
    'kode_area' => 8892,
    'area' => 'Mataram',
    'region' => 'Region 4',
    'hk' => 25,
    'singkatan' => 'MTR',
  ),
  22 => 
  array (
    'kode' => 23,
    'kode_area' => 2151,
    'area' => 'Medaeng',
    'region' => 'Region 4',
    'hk' => 25,
    'singkatan' => 'MDG',
  ),
  23 => 
  array (
    'kode' => 24,
    'kode_area' => 7021,
    'area' => 'Pasuruan',
    'region' => 'Region 4',
    'hk' => 25,
    'singkatan' => 'PSR',
  ),
  24 => 
  array (
    'kode' => 25,
    'kode_area' => 9686,
    'area' => 'Ambon',
    'region' => 'Region 5',
    'hk' => 25,
    'singkatan' => 'AMBN',
  ),
  25 => 
  array (
    'kode' => 26,
    'kode_area' => 9457,
    'area' => 'Makassar',
    'region' => 'Region 5',
    'hk' => 25,
    'singkatan' => 'MKS',
  ),
  26 => 
  array (
    'kode' => 27,
    'kode_area' => 1244,
    'area' => 'Manado',
    'region' => 'Region 5',
    'hk' => 25,
    'singkatan' => 'MND',
  ),
  27 => 
  array (
    'kode' => 28,
    'kode_area' => 5521,
    'area' => 'Palu',
    'region' => 'Region 5',
    'hk' => 25,
    'singkatan' => 'PALU',
  ),
  28 => 
  array (
    'kode' => 29,
    'kode_area' => 7589,
    'area' => 'Papua',
    'region' => 'Region 5',
    'hk' => 25,
    'singkatan' => 'PAPUA',
  ),
  29 => 
  array (
    'kode' => 30,
    'kode_area' => 7161,
    'area' => 'Aceh',
    'region' => 'Region 6',
    'hk' => 25,
    'singkatan' => 'ACEH',
  ),
  30 => 
  array (
    'kode' => 31,
    'kode_area' => 9519,
    'area' => 'Batam',
    'region' => 'Region 6',
    'hk' => 25,
    'singkatan' => 'BTM',
  ),
  31 => 
  array (
    'kode' => 32,
    'kode_area' => 9817,
    'area' => 'Medan',
    'region' => 'Region 6',
    'hk' => 25,
    'singkatan' => 'MDN',
  ),
  32 => 
  array (
    'kode' => 33,
    'kode_area' => 4579,
    'area' => 'Pematang Siantar',
    'region' => 'Region 6',
    'hk' => 25,
    'singkatan' => 'PMS',
  ),
  33 => 
  array (
    'kode' => 34,
    'kode_area' => 4938,
    'area' => 'Balikpapan',
    'region' => 'Region 6',
    'hk' => 25,
    'singkatan' => 'BLP',
  ),
  34 => 
  array (
    'kode' => 35,
    'kode_area' => 5121,
    'area' => 'Banjarmasin',
    'region' => 'Region 6',
    'hk' => 25,
    'singkatan' => 'BJM',
  ),
  35 => 
  array (
    'kode' => 36,
    'kode_area' => 1739,
    'area' => 'Pontianak',
    'region' => 'Region 6',
    'hk' => 25,
    'singkatan' => 'PTN',
  ),
  36 => 
  array (
    'kode' => 37,
    'kode_area' => 7501,
    'area' => 'Samarinda',
    'region' => 'Region 6',
    'hk' => 25,
    'singkatan' => 'SMRD',
  ),
  37 => 
  array (
    'kode' => 38,
    'kode_area' => 6908,
    'area' => 'Kudus',
    'region' => 'Region 3',
    'hk' => 25,
    'singkatan' => 'KDS',
  ),
  38 => 
  array (
    'kode' => 39,
    'kode_area' => 1191,
    'area' => 'TASIKMALAYA',
    'region' => 'Region 2',
    'hk' => 25,
    'singkatan' => 'TSM',
  ),
  39 => 
  array (
    'kode' => 40,
    'kode_area' => 1388,
    'area' => 'Tegal',
    'region' => 'Region 3',
    'hk' => 25,
    'singkatan' => 'TGL',
  ),
  40 => 
  array (
    'kode' => 41,
    'kode_area' => 40,
    'area' => 'Gorontalo',
    'region' => 'Region 5',
    'hk' => 25,
    'singkatan' => 'GRTL',
  ),
  41 => 
  array (
    'kode' => 42,
    'kode_area' => 43,
    'area' => 'Kendari',
    'region' => 'Region 5',
    'hk' => 25,
    'singkatan' => 'KNDR',
  ),
  42 => 
  array (
    'kode' => 43,
    'kode_area' => 47,
    'area' => 'Banyuwangi',
    'region' => 'Region 4',
    'hk' => 25,
    'singkatan' => 'BNYW',
  ),
);
            $now = now();
            foreach ($areas as &$a) {
                $a['created_at'] = $now;
                $a['updated_at'] = $now;
            }
            DB::table('tb_area')->insert($areas);
        }

        // Seed tb_kota
        if (DB::table('tb_kota')->count() === 0) {
            $kotas = array (
  0 => 
  array (
    'kode' => 1,
    'kota' => 'Aceh Barat',
    'propinsi' => 'NAD Aceh',
    'region' => 'Region 6',
  ),
  1 => 
  array (
    'kode' => 2,
    'kota' => 'Aceh Barat Daya',
    'propinsi' => 'NAD Aceh',
    'region' => 'Region 6',
  ),
  2 => 
  array (
    'kode' => 3,
    'kota' => 'Aceh Besar',
    'propinsi' => 'NAD Aceh',
    'region' => 'Region 6',
  ),
  3 => 
  array (
    'kode' => 4,
    'kota' => 'Aceh Jaya',
    'propinsi' => 'NAD Aceh',
    'region' => 'Region 6',
  ),
  4 => 
  array (
    'kode' => 5,
    'kota' => 'Aceh Selatan',
    'propinsi' => 'NAD Aceh',
    'region' => 'Region 6',
  ),
  5 => 
  array (
    'kode' => 6,
    'kota' => 'Aceh Singkil',
    'propinsi' => 'NAD Aceh',
    'region' => 'Region 6',
  ),
  6 => 
  array (
    'kode' => 7,
    'kota' => 'Aceh Tamiang',
    'propinsi' => 'NAD Aceh',
    'region' => 'Region 6',
  ),
  7 => 
  array (
    'kode' => 8,
    'kota' => 'Aceh Tengah',
    'propinsi' => 'NAD Aceh',
    'region' => 'Region 6',
  ),
  8 => 
  array (
    'kode' => 9,
    'kota' => 'Aceh Tenggara',
    'propinsi' => 'NAD Aceh',
    'region' => 'Region 6',
  ),
  9 => 
  array (
    'kode' => 10,
    'kota' => 'Aceh Timur',
    'propinsi' => 'NAD Aceh',
    'region' => 'Region 6',
  ),
  10 => 
  array (
    'kode' => 11,
    'kota' => 'Aceh Utara',
    'propinsi' => 'NAD Aceh',
    'region' => 'Region 6',
  ),
  11 => 
  array (
    'kode' => 12,
    'kota' => 'Bener Meriah',
    'propinsi' => 'NAD Aceh',
    'region' => 'Region 6',
  ),
  12 => 
  array (
    'kode' => 13,
    'kota' => 'Bireuen',
    'propinsi' => 'NAD Aceh',
    'region' => 'Region 6',
  ),
  13 => 
  array (
    'kode' => 14,
    'kota' => 'Gayo Lues',
    'propinsi' => 'NAD Aceh',
    'region' => 'Region 6',
  ),
  14 => 
  array (
    'kode' => 15,
    'kota' => 'Nagan Raya',
    'propinsi' => 'NAD Aceh',
    'region' => 'Region 6',
  ),
  15 => 
  array (
    'kode' => 16,
    'kota' => 'Pidie',
    'propinsi' => 'NAD Aceh',
    'region' => 'Region 6',
  ),
  16 => 
  array (
    'kode' => 17,
    'kota' => 'Pidie Jaya',
    'propinsi' => 'NAD Aceh',
    'region' => 'Region 6',
  ),
  17 => 
  array (
    'kode' => 18,
    'kota' => 'Simeulue',
    'propinsi' => 'NAD Aceh',
    'region' => 'Region 6',
  ),
  18 => 
  array (
    'kode' => 19,
    'kota' => 'Banda Aceh',
    'propinsi' => 'NAD Aceh',
    'region' => 'Region 6',
  ),
  19 => 
  array (
    'kode' => 20,
    'kota' => 'Langsa',
    'propinsi' => 'NAD Aceh',
    'region' => 'Region 6',
  ),
  20 => 
  array (
    'kode' => 21,
    'kota' => 'Lhokseumawe',
    'propinsi' => 'NAD Aceh',
    'region' => 'Region 6',
  ),
  21 => 
  array (
    'kode' => 22,
    'kota' => 'Sabang',
    'propinsi' => 'NAD Aceh',
    'region' => 'Region 6',
  ),
  22 => 
  array (
    'kode' => 23,
    'kota' => 'Subulussalam',
    'propinsi' => 'NAD Aceh',
    'region' => 'Region 6',
  ),
  23 => 
  array (
    'kode' => 24,
    'kota' => 'Asahan',
    'propinsi' => 'Sumatera Utara',
    'region' => 'Region 6',
  ),
  24 => 
  array (
    'kode' => 25,
    'kota' => 'Batubara',
    'propinsi' => 'Sumatera Utara',
    'region' => 'Region 6',
  ),
  25 => 
  array (
    'kode' => 26,
    'kota' => 'Dairi',
    'propinsi' => 'Sumatera Utara',
    'region' => 'Region 6',
  ),
  26 => 
  array (
    'kode' => 27,
    'kota' => 'Deli Serdang',
    'propinsi' => 'Sumatera Utara',
    'region' => 'Region 6',
  ),
  27 => 
  array (
    'kode' => 28,
    'kota' => 'Humbang Hasundutan',
    'propinsi' => 'Sumatera Utara',
    'region' => 'Region 6',
  ),
  28 => 
  array (
    'kode' => 29,
    'kota' => 'Karo',
    'propinsi' => 'Sumatera Utara',
    'region' => 'Region 6',
  ),
  29 => 
  array (
    'kode' => 30,
    'kota' => 'Labuhanbatu',
    'propinsi' => 'Sumatera Utara',
    'region' => 'Region 6',
  ),
  30 => 
  array (
    'kode' => 31,
    'kota' => 'Labuhanbatu Selatan',
    'propinsi' => 'Sumatera Utara',
    'region' => 'Region 6',
  ),
  31 => 
  array (
    'kode' => 32,
    'kota' => 'Labuhanbatu Utara',
    'propinsi' => 'Sumatera Utara',
    'region' => 'Region 6',
  ),
  32 => 
  array (
    'kode' => 33,
    'kota' => 'Langkat',
    'propinsi' => 'Sumatera Utara',
    'region' => 'Region 6',
  ),
  33 => 
  array (
    'kode' => 34,
    'kota' => 'Mandailing Natal',
    'propinsi' => 'Sumatera Utara',
    'region' => 'Region 6',
  ),
  34 => 
  array (
    'kode' => 35,
    'kota' => 'Nias',
    'propinsi' => 'Sumatera Utara',
    'region' => 'Region 6',
  ),
  35 => 
  array (
    'kode' => 36,
    'kota' => 'Nias Barat',
    'propinsi' => 'Sumatera Utara',
    'region' => 'Region 6',
  ),
  36 => 
  array (
    'kode' => 37,
    'kota' => 'Nias Selatan',
    'propinsi' => 'Sumatera Utara',
    'region' => 'Region 6',
  ),
  37 => 
  array (
    'kode' => 38,
    'kota' => 'Nias Utara',
    'propinsi' => 'Sumatera Utara',
    'region' => 'Region 6',
  ),
  38 => 
  array (
    'kode' => 39,
    'kota' => 'Padang Lawas',
    'propinsi' => 'Sumatera Utara',
    'region' => 'Region 6',
  ),
  39 => 
  array (
    'kode' => 40,
    'kota' => 'Padang Lawas Utara',
    'propinsi' => 'Sumatera Utara',
    'region' => 'Region 6',
  ),
  40 => 
  array (
    'kode' => 41,
    'kota' => 'Pakpak Bharat',
    'propinsi' => 'Sumatera Utara',
    'region' => 'Region 6',
  ),
  41 => 
  array (
    'kode' => 42,
    'kota' => 'Samosir',
    'propinsi' => 'Sumatera Utara',
    'region' => 'Region 6',
  ),
  42 => 
  array (
    'kode' => 43,
    'kota' => 'Serdang Bedagai',
    'propinsi' => 'Sumatera Utara',
    'region' => 'Region 6',
  ),
  43 => 
  array (
    'kode' => 44,
    'kota' => 'Simalungun',
    'propinsi' => 'Sumatera Utara',
    'region' => 'Region 6',
  ),
  44 => 
  array (
    'kode' => 45,
    'kota' => 'Tapanuli Selatan',
    'propinsi' => 'Sumatera Utara',
    'region' => 'Region 6',
  ),
  45 => 
  array (
    'kode' => 46,
    'kota' => 'Tapanuli Tengah',
    'propinsi' => 'Sumatera Utara',
    'region' => 'Region 6',
  ),
  46 => 
  array (
    'kode' => 47,
    'kota' => 'Tapanuli Utara',
    'propinsi' => 'Sumatera Utara',
    'region' => 'Region 6',
  ),
  47 => 
  array (
    'kode' => 48,
    'kota' => 'Toba Samosir',
    'propinsi' => 'Sumatera Utara',
    'region' => 'Region 6',
  ),
  48 => 
  array (
    'kode' => 49,
    'kota' => 'Binjai',
    'propinsi' => 'Sumatera Utara',
    'region' => 'Region 6',
  ),
  49 => 
  array (
    'kode' => 50,
    'kota' => 'Gunungsitoli',
    'propinsi' => 'Sumatera Utara',
    'region' => 'Region 6',
  ),
  50 => 
  array (
    'kode' => 51,
    'kota' => 'Medan',
    'propinsi' => 'Sumatera Utara',
    'region' => 'Region 6',
  ),
  51 => 
  array (
    'kode' => 52,
    'kota' => 'Padangsidempuan',
    'propinsi' => 'Sumatera Utara',
    'region' => 'Region 6',
  ),
  52 => 
  array (
    'kode' => 53,
    'kota' => 'Pematangsiantar',
    'propinsi' => 'Sumatera Utara',
    'region' => 'Region 6',
  ),
  53 => 
  array (
    'kode' => 54,
    'kota' => 'Sibolga',
    'propinsi' => 'Sumatera Utara',
    'region' => 'Region 6',
  ),
  54 => 
  array (
    'kode' => 55,
    'kota' => 'Tanjungbalai',
    'propinsi' => 'Sumatera Utara',
    'region' => 'Region 6',
  ),
  55 => 
  array (
    'kode' => 56,
    'kota' => 'Tebing Tinggi',
    'propinsi' => 'Sumatera Utara',
    'region' => 'Region 6',
  ),
  56 => 
  array (
    'kode' => 57,
    'kota' => 'Agam',
    'propinsi' => 'Sumatera Barat',
    'region' => 'Region 7',
  ),
  57 => 
  array (
    'kode' => 58,
    'kota' => 'Dharmasraya',
    'propinsi' => 'Sumatera Barat',
    'region' => 'Region 7',
  ),
  58 => 
  array (
    'kode' => 59,
    'kota' => 'Kepulauan Mentawai',
    'propinsi' => 'Sumatera Barat',
    'region' => 'Region 7',
  ),
  59 => 
  array (
    'kode' => 60,
    'kota' => 'Lima Puluh',
    'propinsi' => 'Sumatera Barat',
    'region' => 'Region 7',
  ),
  60 => 
  array (
    'kode' => 61,
    'kota' => 'Padang Pariaman',
    'propinsi' => 'Sumatera Barat',
    'region' => 'Region 7',
  ),
  61 => 
  array (
    'kode' => 62,
    'kota' => 'Pasaman',
    'propinsi' => 'Sumatera Barat',
    'region' => 'Region 7',
  ),
  62 => 
  array (
    'kode' => 63,
    'kota' => 'Pasaman Barat',
    'propinsi' => 'Sumatera Barat',
    'region' => 'Region 7',
  ),
  63 => 
  array (
    'kode' => 64,
    'kota' => 'Pesisir Selatan',
    'propinsi' => 'Sumatera Barat',
    'region' => 'Region 7',
  ),
  64 => 
  array (
    'kode' => 65,
    'kota' => 'Sijunjung',
    'propinsi' => 'Sumatera Barat',
    'region' => 'Region 7',
  ),
  65 => 
  array (
    'kode' => 66,
    'kota' => 'Solok',
    'propinsi' => 'Sumatera Barat',
    'region' => 'Region 7',
  ),
  66 => 
  array (
    'kode' => 67,
    'kota' => 'Solok Selatan',
    'propinsi' => 'Sumatera Barat',
    'region' => 'Region 7',
  ),
  67 => 
  array (
    'kode' => 68,
    'kota' => 'Tanah Datar',
    'propinsi' => 'Sumatera Barat',
    'region' => 'Region 7',
  ),
  68 => 
  array (
    'kode' => 69,
    'kota' => 'Bukittinggi',
    'propinsi' => 'Sumatera Barat',
    'region' => 'Region 7',
  ),
  69 => 
  array (
    'kode' => 70,
    'kota' => 'Padang',
    'propinsi' => 'Sumatera Barat',
    'region' => 'Region 7',
  ),
  70 => 
  array (
    'kode' => 71,
    'kota' => 'Padangpanjang',
    'propinsi' => 'Sumatera Barat',
    'region' => 'Region 7',
  ),
  71 => 
  array (
    'kode' => 72,
    'kota' => 'Pariaman',
    'propinsi' => 'Sumatera Barat',
    'region' => 'Region 7',
  ),
  72 => 
  array (
    'kode' => 73,
    'kota' => 'Payakumbuh',
    'propinsi' => 'Sumatera Barat',
    'region' => 'Region 7',
  ),
  73 => 
  array (
    'kode' => 74,
    'kota' => 'Sawahlunto',
    'propinsi' => 'Sumatera Barat',
    'region' => 'Region 7',
  ),
  74 => 
  array (
    'kode' => 75,
    'kota' => 'Solok',
    'propinsi' => 'Sumatera Barat',
    'region' => 'Region 7',
  ),
  75 => 
  array (
    'kode' => 76,
    'kota' => 'Banyuasin',
    'propinsi' => 'Sumatera Selatan',
    'region' => 'Region 7',
  ),
  76 => 
  array (
    'kode' => 77,
    'kota' => 'Empat Lawang',
    'propinsi' => 'Sumatera Selatan',
    'region' => 'Region 7',
  ),
  77 => 
  array (
    'kode' => 78,
    'kota' => 'Lahat',
    'propinsi' => 'Sumatera Selatan',
    'region' => 'Region 7',
  ),
  78 => 
  array (
    'kode' => 79,
    'kota' => 'Muara Enim',
    'propinsi' => 'Sumatera Selatan',
    'region' => 'Region 7',
  ),
  79 => 
  array (
    'kode' => 80,
    'kota' => 'Musi Banyuasin',
    'propinsi' => 'Sumatera Selatan',
    'region' => 'Region 7',
  ),
  80 => 
  array (
    'kode' => 81,
    'kota' => 'Musi Rawas',
    'propinsi' => 'Sumatera Selatan',
    'region' => 'Region 7',
  ),
  81 => 
  array (
    'kode' => 82,
    'kota' => 'Musi Rawas Utara',
    'propinsi' => 'Sumatera Selatan',
    'region' => 'Region 7',
  ),
  82 => 
  array (
    'kode' => 83,
    'kota' => 'Ogan Ilir',
    'propinsi' => 'Sumatera Selatan',
    'region' => 'Region 7',
  ),
  83 => 
  array (
    'kode' => 84,
    'kota' => 'Ogan Komering Ilir',
    'propinsi' => 'Sumatera Selatan',
    'region' => 'Region 7',
  ),
  84 => 
  array (
    'kode' => 85,
    'kota' => 'Ogan Komering Ulu',
    'propinsi' => 'Sumatera Selatan',
    'region' => 'Region 7',
  ),
  85 => 
  array (
    'kode' => 86,
    'kota' => 'Ogan Komering Ulu Selatan',
    'propinsi' => 'Sumatera Selatan',
    'region' => 'Region 7',
  ),
  86 => 
  array (
    'kode' => 87,
    'kota' => 'Ogan Komering Ulu Timur',
    'propinsi' => 'Sumatera Selatan',
    'region' => 'Region 7',
  ),
  87 => 
  array (
    'kode' => 88,
    'kota' => 'Penukal Abab Lematang Ilir',
    'propinsi' => 'Sumatera Selatan',
    'region' => 'Region 7',
  ),
  88 => 
  array (
    'kode' => 89,
    'kota' => 'Lubuklinggau',
    'propinsi' => 'Sumatera Selatan',
    'region' => 'Region 7',
  ),
  89 => 
  array (
    'kode' => 90,
    'kota' => 'Pagar Alam',
    'propinsi' => 'Sumatera Selatan',
    'region' => 'Region 7',
  ),
  90 => 
  array (
    'kode' => 91,
    'kota' => 'Palembang',
    'propinsi' => 'Sumatera Selatan',
    'region' => 'Region 7',
  ),
  91 => 
  array (
    'kode' => 92,
    'kota' => 'Prabumulih',
    'propinsi' => 'Sumatera Selatan',
    'region' => 'Region 7',
  ),
  92 => 
  array (
    'kode' => 93,
    'kota' => 'Bengkalis',
    'propinsi' => 'Riau',
    'region' => 'Region 7',
  ),
  93 => 
  array (
    'kode' => 94,
    'kota' => 'Indragiri Hilir',
    'propinsi' => 'Riau',
    'region' => 'Region 7',
  ),
  94 => 
  array (
    'kode' => 95,
    'kota' => 'Indragiri Hulu',
    'propinsi' => 'Riau',
    'region' => 'Region 7',
  ),
  95 => 
  array (
    'kode' => 96,
    'kota' => 'Kampar',
    'propinsi' => 'Riau',
    'region' => 'Region 7',
  ),
  96 => 
  array (
    'kode' => 97,
    'kota' => 'Kepulauan Meranti',
    'propinsi' => 'Riau',
    'region' => 'Region 7',
  ),
  97 => 
  array (
    'kode' => 98,
    'kota' => 'Kuantan Singingi',
    'propinsi' => 'Riau',
    'region' => 'Region 7',
  ),
  98 => 
  array (
    'kode' => 99,
    'kota' => 'Pelalawan',
    'propinsi' => 'Riau',
    'region' => 'Region 7',
  ),
  99 => 
  array (
    'kode' => 100,
    'kota' => 'Rokan Hilir',
    'propinsi' => 'Riau',
    'region' => 'Region 7',
  ),
  100 => 
  array (
    'kode' => 101,
    'kota' => 'Rokan Hulu',
    'propinsi' => 'Riau',
    'region' => 'Region 7',
  ),
  101 => 
  array (
    'kode' => 102,
    'kota' => 'Siak',
    'propinsi' => 'Riau',
    'region' => 'Region 7',
  ),
  102 => 
  array (
    'kode' => 103,
    'kota' => 'Dumai',
    'propinsi' => 'Riau',
    'region' => 'Region 7',
  ),
  103 => 
  array (
    'kode' => 104,
    'kota' => 'Pekanbaru',
    'propinsi' => 'Riau',
    'region' => 'Region 7',
  ),
  104 => 
  array (
    'kode' => 105,
    'kota' => 'Bintan',
    'propinsi' => 'Kepulauan Riau',
    'region' => 'Region 6',
  ),
  105 => 
  array (
    'kode' => 106,
    'kota' => 'Karimun',
    'propinsi' => 'Kepulauan Riau',
    'region' => 'Region 6',
  ),
  106 => 
  array (
    'kode' => 107,
    'kota' => 'Kepulauan Anambas',
    'propinsi' => 'Kepulauan Riau',
    'region' => 'Region 6',
  ),
  107 => 
  array (
    'kode' => 108,
    'kota' => 'Lingga',
    'propinsi' => 'Kepulauan Riau',
    'region' => 'Region 6',
  ),
  108 => 
  array (
    'kode' => 109,
    'kota' => 'Natuna',
    'propinsi' => 'Kepulauan Riau',
    'region' => 'Region 6',
  ),
  109 => 
  array (
    'kode' => 110,
    'kota' => 'Batam',
    'propinsi' => 'Kepulauan Riau',
    'region' => 'Region 6',
  ),
  110 => 
  array (
    'kode' => 111,
    'kota' => 'Tanjung Pinang',
    'propinsi' => 'Kepulauan Riau',
    'region' => 'Region 6',
  ),
  111 => 
  array (
    'kode' => 112,
    'kota' => 'Batanghari',
    'propinsi' => 'Jambi',
    'region' => 'Region 7',
  ),
  112 => 
  array (
    'kode' => 113,
    'kota' => 'Bungo',
    'propinsi' => 'Jambi',
    'region' => 'Region 7',
  ),
  113 => 
  array (
    'kode' => 114,
    'kota' => 'Kerinci',
    'propinsi' => 'Jambi',
    'region' => 'Region 7',
  ),
  114 => 
  array (
    'kode' => 115,
    'kota' => 'Merangin',
    'propinsi' => 'Jambi',
    'region' => 'Region 7',
  ),
  115 => 
  array (
    'kode' => 116,
    'kota' => 'Muaro Jambi',
    'propinsi' => 'Jambi',
    'region' => 'Region 7',
  ),
  116 => 
  array (
    'kode' => 117,
    'kota' => 'Sarolangun',
    'propinsi' => 'Jambi',
    'region' => 'Region 7',
  ),
  117 => 
  array (
    'kode' => 118,
    'kota' => 'Tanjung Jabung Barat',
    'propinsi' => 'Jambi',
    'region' => 'Region 7',
  ),
  118 => 
  array (
    'kode' => 119,
    'kota' => 'Tanjung Jabung Timur',
    'propinsi' => 'Jambi',
    'region' => 'Region 7',
  ),
  119 => 
  array (
    'kode' => 120,
    'kota' => 'Tebo',
    'propinsi' => 'Jambi',
    'region' => 'Region 7',
  ),
  120 => 
  array (
    'kode' => 121,
    'kota' => 'Jambi',
    'propinsi' => 'Jambi',
    'region' => 'Region 7',
  ),
  121 => 
  array (
    'kode' => 122,
    'kota' => 'Sungai Penuh',
    'propinsi' => 'Jambi',
    'region' => 'Region 7',
  ),
  122 => 
  array (
    'kode' => 123,
    'kota' => 'Bengkulu Selatan',
    'propinsi' => 'Bengkulu',
    'region' => 'Region 2',
  ),
  123 => 
  array (
    'kode' => 124,
    'kota' => 'Bengkulu Tengah',
    'propinsi' => 'Bengkulu',
    'region' => 'Region 2',
  ),
  124 => 
  array (
    'kode' => 125,
    'kota' => 'Bengkulu Utara',
    'propinsi' => 'Bengkulu',
    'region' => 'Region 2',
  ),
  125 => 
  array (
    'kode' => 126,
    'kota' => 'Kaur',
    'propinsi' => 'Bengkulu',
    'region' => 'Region 2',
  ),
  126 => 
  array (
    'kode' => 127,
    'kota' => 'Kepahiang',
    'propinsi' => 'Bengkulu',
    'region' => 'Region 2',
  ),
  127 => 
  array (
    'kode' => 128,
    'kota' => 'Lebong',
    'propinsi' => 'Bengkulu',
    'region' => 'Region 2',
  ),
  128 => 
  array (
    'kode' => 129,
    'kota' => 'Mukomuko',
    'propinsi' => 'Bengkulu',
    'region' => 'Region 2',
  ),
  129 => 
  array (
    'kode' => 130,
    'kota' => 'Rejang Lebong',
    'propinsi' => 'Bengkulu',
    'region' => 'Region 2',
  ),
  130 => 
  array (
    'kode' => 131,
    'kota' => 'Seluma',
    'propinsi' => 'Bengkulu',
    'region' => 'Region 2',
  ),
  131 => 
  array (
    'kode' => 132,
    'kota' => 'Bengkulu',
    'propinsi' => 'Bengkulu',
    'region' => 'Region 2',
  ),
  132 => 
  array (
    'kode' => 133,
    'kota' => 'Bangka',
    'propinsi' => 'Bangka Belitung',
    'region' => 'Region 6',
  ),
  133 => 
  array (
    'kode' => 134,
    'kota' => 'Bangka Barat',
    'propinsi' => 'Bangka Belitung',
    'region' => 'Region 6',
  ),
  134 => 
  array (
    'kode' => 135,
    'kota' => 'Bangka Selatan',
    'propinsi' => 'Bangka Belitung',
    'region' => 'Region 6',
  ),
  135 => 
  array (
    'kode' => 136,
    'kota' => 'Bangka Tengah',
    'propinsi' => 'Bangka Belitung',
    'region' => 'Region 6',
  ),
  136 => 
  array (
    'kode' => 137,
    'kota' => 'Belitung',
    'propinsi' => 'Bangka Belitung',
    'region' => 'Region 6',
  ),
  137 => 
  array (
    'kode' => 138,
    'kota' => 'Belitung Timur',
    'propinsi' => 'Bangka Belitung',
    'region' => 'Region 6',
  ),
  138 => 
  array (
    'kode' => 139,
    'kota' => 'Pangkal Pinang',
    'propinsi' => 'Bangka Belitung',
    'region' => 'Region 6',
  ),
  139 => 
  array (
    'kode' => 140,
    'kota' => 'Lampung Tengah',
    'propinsi' => 'Lampung',
    'region' => 'Region 7',
  ),
  140 => 
  array (
    'kode' => 141,
    'kota' => 'Lampung Utara',
    'propinsi' => 'Lampung',
    'region' => 'Region 7',
  ),
  141 => 
  array (
    'kode' => 142,
    'kota' => 'Lampung Selatan',
    'propinsi' => 'Lampung',
    'region' => 'Region 7',
  ),
  142 => 
  array (
    'kode' => 143,
    'kota' => 'Lampung Barat',
    'propinsi' => 'Lampung',
    'region' => 'Region 7',
  ),
  143 => 
  array (
    'kode' => 144,
    'kota' => 'Lampung Timur',
    'propinsi' => 'Lampung',
    'region' => 'Region 7',
  ),
  144 => 
  array (
    'kode' => 145,
    'kota' => 'Mesuji',
    'propinsi' => 'Lampung',
    'region' => 'Region 7',
  ),
  145 => 
  array (
    'kode' => 146,
    'kota' => 'Pesawaran',
    'propinsi' => 'Lampung',
    'region' => 'Region 7',
  ),
  146 => 
  array (
    'kode' => 147,
    'kota' => 'Pesisir Barat',
    'propinsi' => 'Lampung',
    'region' => 'Region 7',
  ),
  147 => 
  array (
    'kode' => 148,
    'kota' => 'Pringsewu',
    'propinsi' => 'Lampung',
    'region' => 'Region 7',
  ),
  148 => 
  array (
    'kode' => 149,
    'kota' => 'Tulang Bawang',
    'propinsi' => 'Lampung',
    'region' => 'Region 7',
  ),
  149 => 
  array (
    'kode' => 150,
    'kota' => 'Tulang Bawang Barat',
    'propinsi' => 'Lampung',
    'region' => 'Region 7',
  ),
  150 => 
  array (
    'kode' => 151,
    'kota' => 'Tanggamus',
    'propinsi' => 'Lampung',
    'region' => 'Region 7',
  ),
  151 => 
  array (
    'kode' => 152,
    'kota' => 'Way Kanan',
    'propinsi' => 'Lampung',
    'region' => 'Region 7',
  ),
  152 => 
  array (
    'kode' => 153,
    'kota' => 'Bandar Lampung',
    'propinsi' => 'Lampung',
    'region' => 'Region 7',
  ),
  153 => 
  array (
    'kode' => 154,
    'kota' => 'Metro',
    'propinsi' => 'Lampung',
    'region' => 'Region 7',
  ),
  154 => 
  array (
    'kode' => 155,
    'kota' => 'Lebak',
    'propinsi' => 'Banten',
    'region' => 'Region 1',
  ),
  155 => 
  array (
    'kode' => 156,
    'kota' => 'Pandeglang',
    'propinsi' => 'Banten',
    'region' => 'Region 1',
  ),
  156 => 
  array (
    'kode' => 157,
    'kota' => 'Serang',
    'propinsi' => 'Banten',
    'region' => 'Region 1',
  ),
  157 => 
  array (
    'kode' => 158,
    'kota' => 'Tangerang',
    'propinsi' => 'Banten',
    'region' => 'Region 1',
  ),
  158 => 
  array (
    'kode' => 159,
    'kota' => 'Cilegon',
    'propinsi' => 'Banten',
    'region' => 'Region 1',
  ),
  159 => 
  array (
    'kode' => 160,
    'kota' => 'Serang',
    'propinsi' => 'Banten',
    'region' => 'Region 1',
  ),
  160 => 
  array (
    'kode' => 161,
    'kota' => 'Tangerang',
    'propinsi' => 'Banten',
    'region' => 'Region 1',
  ),
  161 => 
  array (
    'kode' => 162,
    'kota' => 'Tangerang Selatan',
    'propinsi' => 'Banten',
    'region' => 'Region 1',
  ),
  162 => 
  array (
    'kode' => 163,
    'kota' => 'Bandung',
    'propinsi' => 'Jawa Barat',
    'region' => 'Region 2',
  ),
  163 => 
  array (
    'kode' => 164,
    'kota' => 'Bandung Barat',
    'propinsi' => 'Jawa Barat',
    'region' => 'Region 2',
  ),
  164 => 
  array (
    'kode' => 165,
    'kota' => 'Bekasi',
    'propinsi' => 'Jawa Barat',
    'region' => 'Region 1',
  ),
  165 => 
  array (
    'kode' => 166,
    'kota' => 'Bogor',
    'propinsi' => 'Jawa Barat',
    'region' => 'Region 2',
  ),
  166 => 
  array (
    'kode' => 167,
    'kota' => 'Ciamis',
    'propinsi' => 'Jawa Barat',
    'region' => 'Region 2',
  ),
  167 => 
  array (
    'kode' => 168,
    'kota' => 'Cianjur',
    'propinsi' => 'Jawa Barat',
    'region' => 'Region 2',
  ),
  168 => 
  array (
    'kode' => 169,
    'kota' => 'Cirebon',
    'propinsi' => 'Jawa Barat',
    'region' => 'Region 2',
  ),
  169 => 
  array (
    'kode' => 170,
    'kota' => 'Garut',
    'propinsi' => 'Jawa Barat',
    'region' => 'Region 2',
  ),
  170 => 
  array (
    'kode' => 171,
    'kota' => 'Indramayu',
    'propinsi' => 'Jawa Barat',
    'region' => 'Region 2',
  ),
  171 => 
  array (
    'kode' => 172,
    'kota' => 'Karawang',
    'propinsi' => 'Jawa Barat',
    'region' => 'Region 2',
  ),
  172 => 
  array (
    'kode' => 173,
    'kota' => 'Kuningan',
    'propinsi' => 'Jawa Barat',
    'region' => 'Region 2',
  ),
  173 => 
  array (
    'kode' => 174,
    'kota' => 'Majalengka',
    'propinsi' => 'Jawa Barat',
    'region' => 'Region 2',
  ),
  174 => 
  array (
    'kode' => 175,
    'kota' => 'Pangandaran',
    'propinsi' => 'Jawa Barat',
    'region' => 'Region 2',
  ),
  175 => 
  array (
    'kode' => 176,
    'kota' => 'Purwakarta',
    'propinsi' => 'Jawa Barat',
    'region' => 'Region 2',
  ),
  176 => 
  array (
    'kode' => 177,
    'kota' => 'Subang',
    'propinsi' => 'Jawa Barat',
    'region' => 'Region 2',
  ),
  177 => 
  array (
    'kode' => 178,
    'kota' => 'Sukabumi',
    'propinsi' => 'Jawa Barat',
    'region' => 'Region 2',
  ),
  178 => 
  array (
    'kode' => 179,
    'kota' => 'Sumedang',
    'propinsi' => 'Jawa Barat',
    'region' => 'Region 2',
  ),
  179 => 
  array (
    'kode' => 180,
    'kota' => 'Tasikmalaya',
    'propinsi' => 'Jawa Barat',
    'region' => 'Region 2',
  ),
  180 => 
  array (
    'kode' => 181,
    'kota' => 'Bandung',
    'propinsi' => 'Jawa Barat',
    'region' => 'Region 2',
  ),
  181 => 
  array (
    'kode' => 182,
    'kota' => 'Banjar',
    'propinsi' => 'Jawa Barat',
    'region' => 'Region 2',
  ),
  182 => 
  array (
    'kode' => 184,
    'kota' => 'Bogor',
    'propinsi' => 'Jawa Barat',
    'region' => 'Region 1',
  ),
  183 => 
  array (
    'kode' => 185,
    'kota' => 'Cimahi',
    'propinsi' => 'Jawa Barat',
    'region' => 'Region 2',
  ),
  184 => 
  array (
    'kode' => 186,
    'kota' => 'Cirebon',
    'propinsi' => 'Jawa Barat',
    'region' => 'Region 2',
  ),
  185 => 
  array (
    'kode' => 187,
    'kota' => 'Depok',
    'propinsi' => 'Jawa Barat',
    'region' => 'Region 2',
  ),
  186 => 
  array (
    'kode' => 188,
    'kota' => 'Sukabumi',
    'propinsi' => 'Jawa Barat',
    'region' => 'Region 2',
  ),
  187 => 
  array (
    'kode' => 189,
    'kota' => 'Tasikmalaya',
    'propinsi' => 'Jawa Barat',
    'region' => 'Region 2',
  ),
  188 => 
  array (
    'kode' => 190,
    'kota' => 'Banjarnegara',
    'propinsi' => 'Jawa Tengah',
    'region' => 'Region 3',
  ),
  189 => 
  array (
    'kode' => 191,
    'kota' => 'Banyumas',
    'propinsi' => 'Jawa Tengah',
    'region' => 'Region 3',
  ),
  190 => 
  array (
    'kode' => 192,
    'kota' => 'Batang',
    'propinsi' => 'Jawa Tengah',
    'region' => 'Region 3',
  ),
  191 => 
  array (
    'kode' => 193,
    'kota' => 'Blora',
    'propinsi' => 'Jawa Tengah',
    'region' => 'Region 3',
  ),
  192 => 
  array (
    'kode' => 194,
    'kota' => 'Boyolali',
    'propinsi' => 'Jawa Tengah',
    'region' => 'Region 3',
  ),
  193 => 
  array (
    'kode' => 195,
    'kota' => 'Brebes',
    'propinsi' => 'Jawa Tengah',
    'region' => 'Region 3',
  ),
  194 => 
  array (
    'kode' => 196,
    'kota' => 'Cilacap',
    'propinsi' => 'Jawa Tengah',
    'region' => 'Region 3',
  ),
  195 => 
  array (
    'kode' => 197,
    'kota' => 'Demak',
    'propinsi' => 'Jawa Tengah',
    'region' => 'Region 3',
  ),
  196 => 
  array (
    'kode' => 198,
    'kota' => 'Grobogan',
    'propinsi' => 'Jawa Tengah',
    'region' => 'Region 3',
  ),
  197 => 
  array (
    'kode' => 199,
    'kota' => 'Jepara',
    'propinsi' => 'Jawa Tengah',
    'region' => 'Region 3',
  ),
  198 => 
  array (
    'kode' => 200,
    'kota' => 'Karanganyar',
    'propinsi' => 'Jawa Tengah',
    'region' => 'Region 3',
  ),
  199 => 
  array (
    'kode' => 201,
    'kota' => 'Kebumen',
    'propinsi' => 'Jawa Tengah',
    'region' => 'Region 3',
  ),
  200 => 
  array (
    'kode' => 202,
    'kota' => 'Kendal',
    'propinsi' => 'Jawa Tengah',
    'region' => 'Region 3',
  ),
  201 => 
  array (
    'kode' => 203,
    'kota' => 'Klaten',
    'propinsi' => 'Jawa Tengah',
    'region' => 'Region 3',
  ),
  202 => 
  array (
    'kode' => 204,
    'kota' => 'Kudus',
    'propinsi' => 'Jawa Tengah',
    'region' => 'Region 3',
  ),
  203 => 
  array (
    'kode' => 205,
    'kota' => 'Magelang',
    'propinsi' => 'Jawa Tengah',
    'region' => 'Region 3',
  ),
  204 => 
  array (
    'kode' => 206,
    'kota' => 'Pati',
    'propinsi' => 'Jawa Tengah',
    'region' => 'Region 3',
  ),
  205 => 
  array (
    'kode' => 207,
    'kota' => 'Pekalongan',
    'propinsi' => 'Jawa Tengah',
    'region' => 'Region 3',
  ),
  206 => 
  array (
    'kode' => 208,
    'kota' => 'Pemalang',
    'propinsi' => 'Jawa Tengah',
    'region' => 'Region 3',
  ),
  207 => 
  array (
    'kode' => 209,
    'kota' => 'Purbalingga',
    'propinsi' => 'Jawa Tengah',
    'region' => 'Region 3',
  ),
  208 => 
  array (
    'kode' => 210,
    'kota' => 'Purworejo',
    'propinsi' => 'Jawa Tengah',
    'region' => 'Region 3',
  ),
  209 => 
  array (
    'kode' => 211,
    'kota' => 'Rembang',
    'propinsi' => 'Jawa Tengah',
    'region' => 'Region 3',
  ),
  210 => 
  array (
    'kode' => 212,
    'kota' => 'Semarang',
    'propinsi' => 'Jawa Tengah',
    'region' => 'Region 3',
  ),
  211 => 
  array (
    'kode' => 213,
    'kota' => 'Sragen',
    'propinsi' => 'Jawa Tengah',
    'region' => 'Region 3',
  ),
  212 => 
  array (
    'kode' => 214,
    'kota' => 'Sukoharjo',
    'propinsi' => 'Jawa Tengah',
    'region' => 'Region 3',
  ),
  213 => 
  array (
    'kode' => 215,
    'kota' => 'Tegal',
    'propinsi' => 'Jawa Tengah',
    'region' => 'Region 3',
  ),
  214 => 
  array (
    'kode' => 216,
    'kota' => 'Temanggung',
    'propinsi' => 'Jawa Tengah',
    'region' => 'Region 3',
  ),
  215 => 
  array (
    'kode' => 217,
    'kota' => 'Wonogiri',
    'propinsi' => 'Jawa Tengah',
    'region' => 'Region 3',
  ),
  216 => 
  array (
    'kode' => 218,
    'kota' => 'Wonosobo',
    'propinsi' => 'Jawa Tengah',
    'region' => 'Region 3',
  ),
  217 => 
  array (
    'kode' => 219,
    'kota' => 'Magelang',
    'propinsi' => 'Jawa Tengah',
    'region' => 'Region 3',
  ),
  218 => 
  array (
    'kode' => 220,
    'kota' => 'Pekalongan',
    'propinsi' => 'Jawa Tengah',
    'region' => 'Region 3',
  ),
  219 => 
  array (
    'kode' => 221,
    'kota' => 'Salatiga',
    'propinsi' => 'Jawa Tengah',
    'region' => 'Region 3',
  ),
  220 => 
  array (
    'kode' => 222,
    'kota' => 'Semarang',
    'propinsi' => 'Jawa Tengah',
    'region' => 'Region 3',
  ),
  221 => 
  array (
    'kode' => 223,
    'kota' => 'Surakarta',
    'propinsi' => 'Jawa Tengah',
    'region' => 'Region 3',
  ),
  222 => 
  array (
    'kode' => 224,
    'kota' => 'Tegal',
    'propinsi' => 'Jawa Tengah',
    'region' => 'Region 3',
  ),
  223 => 
  array (
    'kode' => 225,
    'kota' => 'Bangkalan',
    'propinsi' => 'Jawa Timur',
    'region' => 'Region 4',
  ),
  224 => 
  array (
    'kode' => 226,
    'kota' => 'Banyuwangi',
    'propinsi' => 'Jawa Timur',
    'region' => 'Region 4',
  ),
  225 => 
  array (
    'kode' => 227,
    'kota' => 'Blitar',
    'propinsi' => 'Jawa Timur',
    'region' => 'Region 4',
  ),
  226 => 
  array (
    'kode' => 228,
    'kota' => 'Bojonegoro',
    'propinsi' => 'Jawa Timur',
    'region' => 'Region 4',
  ),
  227 => 
  array (
    'kode' => 229,
    'kota' => 'Bondowoso',
    'propinsi' => 'Jawa Timur',
    'region' => 'Region 4',
  ),
  228 => 
  array (
    'kode' => 230,
    'kota' => 'Gresik',
    'propinsi' => 'Jawa Timur',
    'region' => 'Region 4',
  ),
  229 => 
  array (
    'kode' => 231,
    'kota' => 'Jember',
    'propinsi' => 'Jawa Timur',
    'region' => 'Region 4',
  ),
  230 => 
  array (
    'kode' => 232,
    'kota' => 'Jombang',
    'propinsi' => 'Jawa Timur',
    'region' => 'Region 4',
  ),
  231 => 
  array (
    'kode' => 233,
    'kota' => 'Kediri',
    'propinsi' => 'Jawa Timur',
    'region' => 'Region 4',
  ),
  232 => 
  array (
    'kode' => 234,
    'kota' => 'Lamongan',
    'propinsi' => 'Jawa Timur',
    'region' => 'Region 4',
  ),
  233 => 
  array (
    'kode' => 235,
    'kota' => 'Lumajang',
    'propinsi' => 'Jawa Timur',
    'region' => 'Region 4',
  ),
  234 => 
  array (
    'kode' => 236,
    'kota' => 'Madiun',
    'propinsi' => 'Jawa Timur',
    'region' => 'Region 4',
  ),
  235 => 
  array (
    'kode' => 237,
    'kota' => 'Magetan',
    'propinsi' => 'Jawa Timur',
    'region' => 'Region 4',
  ),
  236 => 
  array (
    'kode' => 238,
    'kota' => 'Malang',
    'propinsi' => 'Jawa Timur',
    'region' => 'Region 4',
  ),
  237 => 
  array (
    'kode' => 239,
    'kota' => 'Mojokerto',
    'propinsi' => 'Jawa Timur',
    'region' => 'Region 4',
  ),
  238 => 
  array (
    'kode' => 240,
    'kota' => 'Nganjuk',
    'propinsi' => 'Jawa Timur',
    'region' => 'Region 4',
  ),
  239 => 
  array (
    'kode' => 241,
    'kota' => 'Ngawi',
    'propinsi' => 'Jawa Timur',
    'region' => 'Region 4',
  ),
  240 => 
  array (
    'kode' => 242,
    'kota' => 'Pacitan',
    'propinsi' => 'Jawa Timur',
    'region' => 'Region 4',
  ),
  241 => 
  array (
    'kode' => 243,
    'kota' => 'Pamekasan',
    'propinsi' => 'Jawa Timur',
    'region' => 'Region 4',
  ),
  242 => 
  array (
    'kode' => 244,
    'kota' => 'Pasuruan',
    'propinsi' => 'Jawa Timur',
    'region' => 'Region 4',
  ),
  243 => 
  array (
    'kode' => 245,
    'kota' => 'Ponorogo',
    'propinsi' => 'Jawa Timur',
    'region' => 'Region 4',
  ),
  244 => 
  array (
    'kode' => 246,
    'kota' => 'Probolinggo',
    'propinsi' => 'Jawa Timur',
    'region' => 'Region 4',
  ),
  245 => 
  array (
    'kode' => 247,
    'kota' => 'Sampang',
    'propinsi' => 'Jawa Timur',
    'region' => 'Region 4',
  ),
  246 => 
  array (
    'kode' => 248,
    'kota' => 'Sidoarjo',
    'propinsi' => 'Jawa Timur',
    'region' => 'Region 4',
  ),
  247 => 
  array (
    'kode' => 249,
    'kota' => 'Situbondo',
    'propinsi' => 'Jawa Timur',
    'region' => 'Region 4',
  ),
  248 => 
  array (
    'kode' => 250,
    'kota' => 'Sumenep',
    'propinsi' => 'Jawa Timur',
    'region' => 'Region 4',
  ),
  249 => 
  array (
    'kode' => 251,
    'kota' => 'Trenggalek',
    'propinsi' => 'Jawa Timur',
    'region' => 'Region 4',
  ),
  250 => 
  array (
    'kode' => 252,
    'kota' => 'Tuban',
    'propinsi' => 'Jawa Timur',
    'region' => 'Region 4',
  ),
  251 => 
  array (
    'kode' => 253,
    'kota' => 'Tulungagung',
    'propinsi' => 'Jawa Timur',
    'region' => 'Region 4',
  ),
  252 => 
  array (
    'kode' => 254,
    'kota' => 'Batu',
    'propinsi' => 'Jawa Timur',
    'region' => 'Region 4',
  ),
  253 => 
  array (
    'kode' => 255,
    'kota' => 'Blitar',
    'propinsi' => 'Jawa Timur',
    'region' => 'Region 4',
  ),
  254 => 
  array (
    'kode' => 256,
    'kota' => 'Kediri',
    'propinsi' => 'Jawa Timur',
    'region' => 'Region 4',
  ),
  255 => 
  array (
    'kode' => 257,
    'kota' => 'Madiun',
    'propinsi' => 'Jawa Timur',
    'region' => 'Region 4',
  ),
  256 => 
  array (
    'kode' => 258,
    'kota' => 'Malang',
    'propinsi' => 'Jawa Timur',
    'region' => 'Region 4',
  ),
  257 => 
  array (
    'kode' => 259,
    'kota' => 'Mojokerto',
    'propinsi' => 'Jawa Timur',
    'region' => 'Region 4',
  ),
  258 => 
  array (
    'kode' => 260,
    'kota' => 'Pasuruan',
    'propinsi' => 'Jawa Timur',
    'region' => 'Region 4',
  ),
  259 => 
  array (
    'kode' => 261,
    'kota' => 'Probolinggo',
    'propinsi' => 'Jawa Timur',
    'region' => 'Region 4',
  ),
  260 => 
  array (
    'kode' => 262,
    'kota' => 'Surabaya',
    'propinsi' => 'Jawa Timur',
    'region' => 'Region 4',
  ),
  261 => 
  array (
    'kode' => 263,
    'kota' => 'Jakarta Barat',
    'propinsi' => 'DKI Jakarta',
    'region' => 'Region 1',
  ),
  262 => 
  array (
    'kode' => 264,
    'kota' => 'Jakarta Pusat',
    'propinsi' => 'DKI Jakarta',
    'region' => 'Region 1',
  ),
  263 => 
  array (
    'kode' => 265,
    'kota' => 'Jakarta Selatan',
    'propinsi' => 'DKI Jakarta',
    'region' => 'Region 1',
  ),
  264 => 
  array (
    'kode' => 266,
    'kota' => 'Jakarta Timur',
    'propinsi' => 'DKI Jakarta',
    'region' => 'Region 1',
  ),
  265 => 
  array (
    'kode' => 267,
    'kota' => 'Jakarta Utara',
    'propinsi' => 'DKI Jakarta',
    'region' => 'Region 1',
  ),
  266 => 
  array (
    'kode' => 268,
    'kota' => 'Kepulauan Seribu',
    'propinsi' => 'DKI Jakarta',
    'region' => 'Region 1',
  ),
  267 => 
  array (
    'kode' => 269,
    'kota' => 'Bantul',
    'propinsi' => 'Yogyakarta',
    'region' => 'Region 3',
  ),
  268 => 
  array (
    'kode' => 270,
    'kota' => 'Gunungkidul',
    'propinsi' => 'Yogyakarta',
    'region' => 'Region 3',
  ),
  269 => 
  array (
    'kode' => 271,
    'kota' => 'Kulon Progo',
    'propinsi' => 'Yogyakarta',
    'region' => 'Region 3',
  ),
  270 => 
  array (
    'kode' => 272,
    'kota' => 'Sleman',
    'propinsi' => 'Yogyakarta',
    'region' => 'Region 3',
  ),
  271 => 
  array (
    'kode' => 273,
    'kota' => 'Yogyakarta',
    'propinsi' => 'Yogyakarta',
    'region' => 'Region 3',
  ),
  272 => 
  array (
    'kode' => 274,
    'kota' => 'Badung',
    'propinsi' => 'Bali',
    'region' => 'Region 4',
  ),
  273 => 
  array (
    'kode' => 275,
    'kota' => 'Bangli',
    'propinsi' => 'Bali',
    'region' => 'Region 4',
  ),
  274 => 
  array (
    'kode' => 276,
    'kota' => 'Buleleng',
    'propinsi' => 'Bali',
    'region' => 'Region 4',
  ),
  275 => 
  array (
    'kode' => 277,
    'kota' => 'Gianyar',
    'propinsi' => 'Bali',
    'region' => 'Region 4',
  ),
  276 => 
  array (
    'kode' => 278,
    'kota' => 'Jembrana',
    'propinsi' => 'Bali',
    'region' => 'Region 4',
  ),
  277 => 
  array (
    'kode' => 279,
    'kota' => 'Karangasem',
    'propinsi' => 'Bali',
    'region' => 'Region 4',
  ),
  278 => 
  array (
    'kode' => 280,
    'kota' => 'Klungkung',
    'propinsi' => 'Bali',
    'region' => 'Region 4',
  ),
  279 => 
  array (
    'kode' => 281,
    'kota' => 'Tabanan',
    'propinsi' => 'Bali',
    'region' => 'Region 4',
  ),
  280 => 
  array (
    'kode' => 282,
    'kota' => 'Denpasar',
    'propinsi' => 'Bali',
    'region' => 'Region 4',
  ),
  281 => 
  array (
    'kode' => 283,
    'kota' => 'Bima',
    'propinsi' => 'NTB',
    'region' => 'Region 4',
  ),
  282 => 
  array (
    'kode' => 284,
    'kota' => 'Dompu',
    'propinsi' => 'NTB',
    'region' => 'Region 4',
  ),
  283 => 
  array (
    'kode' => 285,
    'kota' => 'Lombok Barat',
    'propinsi' => 'NTB',
    'region' => 'Region 4',
  ),
  284 => 
  array (
    'kode' => 286,
    'kota' => 'Lombok Tengah',
    'propinsi' => 'NTB',
    'region' => 'Region 4',
  ),
  285 => 
  array (
    'kode' => 287,
    'kota' => 'Lombok Timur',
    'propinsi' => 'NTB',
    'region' => 'Region 4',
  ),
  286 => 
  array (
    'kode' => 288,
    'kota' => 'Lombok Utara',
    'propinsi' => 'NTB',
    'region' => 'Region 4',
  ),
  287 => 
  array (
    'kode' => 289,
    'kota' => 'Sumbawa',
    'propinsi' => 'NTB',
    'region' => 'Region 4',
  ),
  288 => 
  array (
    'kode' => 290,
    'kota' => 'Sumbawa Barat',
    'propinsi' => 'NTB',
    'region' => 'Region 4',
  ),
  289 => 
  array (
    'kode' => 291,
    'kota' => 'Bima',
    'propinsi' => 'NTB',
    'region' => 'Region 4',
  ),
  290 => 
  array (
    'kode' => 292,
    'kota' => 'Mataram',
    'propinsi' => 'NTB',
    'region' => 'Region 4',
  ),
  291 => 
  array (
    'kode' => 293,
    'kota' => 'Alor',
    'propinsi' => 'NTT',
    'region' => 'Region 4',
  ),
  292 => 
  array (
    'kode' => 294,
    'kota' => 'Belu',
    'propinsi' => 'NTT',
    'region' => 'Region 4',
  ),
  293 => 
  array (
    'kode' => 295,
    'kota' => 'Ende',
    'propinsi' => 'NTT',
    'region' => 'Region 4',
  ),
  294 => 
  array (
    'kode' => 296,
    'kota' => 'Flores Timur',
    'propinsi' => 'NTT',
    'region' => 'Region 4',
  ),
  295 => 
  array (
    'kode' => 297,
    'kota' => 'Kupang',
    'propinsi' => 'NTT',
    'region' => 'Region 4',
  ),
  296 => 
  array (
    'kode' => 298,
    'kota' => 'Lembata',
    'propinsi' => 'NTT',
    'region' => 'Region 4',
  ),
  297 => 
  array (
    'kode' => 299,
    'kota' => 'Malaka',
    'propinsi' => 'NTT',
    'region' => 'Region 4',
  ),
  298 => 
  array (
    'kode' => 300,
    'kota' => 'Manggarai',
    'propinsi' => 'NTT',
    'region' => 'Region 4',
  ),
  299 => 
  array (
    'kode' => 301,
    'kota' => 'Manggarai Barat',
    'propinsi' => 'NTT',
    'region' => 'Region 4',
  ),
  300 => 
  array (
    'kode' => 302,
    'kota' => 'Manggarai Timur',
    'propinsi' => 'NTT',
    'region' => 'Region 4',
  ),
  301 => 
  array (
    'kode' => 303,
    'kota' => 'Ngada',
    'propinsi' => 'NTT',
    'region' => 'Region 4',
  ),
  302 => 
  array (
    'kode' => 304,
    'kota' => 'Nagekeo',
    'propinsi' => 'NTT',
    'region' => 'Region 4',
  ),
  303 => 
  array (
    'kode' => 305,
    'kota' => 'Rote Ndao',
    'propinsi' => 'NTT',
    'region' => 'Region 4',
  ),
  304 => 
  array (
    'kode' => 306,
    'kota' => 'Sabu Raijua',
    'propinsi' => 'NTT',
    'region' => 'Region 4',
  ),
  305 => 
  array (
    'kode' => 307,
    'kota' => 'Sikka',
    'propinsi' => 'NTT',
    'region' => 'Region 4',
  ),
  306 => 
  array (
    'kode' => 308,
    'kota' => 'Sumba Barat',
    'propinsi' => 'NTT',
    'region' => 'Region 4',
  ),
  307 => 
  array (
    'kode' => 309,
    'kota' => 'Sumba Barat Daya',
    'propinsi' => 'NTT',
    'region' => 'Region 4',
  ),
  308 => 
  array (
    'kode' => 310,
    'kota' => 'Sumba Tengah',
    'propinsi' => 'NTT',
    'region' => 'Region 4',
  ),
  309 => 
  array (
    'kode' => 311,
    'kota' => 'Sumba Timur',
    'propinsi' => 'NTT',
    'region' => 'Region 4',
  ),
  310 => 
  array (
    'kode' => 312,
    'kota' => 'Timor Tengah Selatan',
    'propinsi' => 'NTT',
    'region' => 'Region 4',
  ),
  311 => 
  array (
    'kode' => 313,
    'kota' => 'Timor Tengah Utara',
    'propinsi' => 'NTT',
    'region' => 'Region 4',
  ),
  312 => 
  array (
    'kode' => 314,
    'kota' => 'Kupang',
    'propinsi' => 'NTT',
    'region' => 'Region 4',
  ),
  313 => 
  array (
    'kode' => 315,
    'kota' => 'Bengkayang',
    'propinsi' => 'Kalimantan Barat',
    'region' => 'Region 6',
  ),
  314 => 
  array (
    'kode' => 316,
    'kota' => 'Kapuas Hulu',
    'propinsi' => 'Kalimantan Barat',
    'region' => 'Region 6',
  ),
  315 => 
  array (
    'kode' => 317,
    'kota' => 'Kayong Utara',
    'propinsi' => 'Kalimantan Barat',
    'region' => 'Region 6',
  ),
  316 => 
  array (
    'kode' => 318,
    'kota' => 'Ketapang',
    'propinsi' => 'Kalimantan Barat',
    'region' => 'Region 6',
  ),
  317 => 
  array (
    'kode' => 319,
    'kota' => 'Kubu Raya',
    'propinsi' => 'Kalimantan Barat',
    'region' => 'Region 6',
  ),
  318 => 
  array (
    'kode' => 320,
    'kota' => 'Landak',
    'propinsi' => 'Kalimantan Barat',
    'region' => 'Region 6',
  ),
  319 => 
  array (
    'kode' => 321,
    'kota' => 'Melawi',
    'propinsi' => 'Kalimantan Barat',
    'region' => 'Region 6',
  ),
  320 => 
  array (
    'kode' => 322,
    'kota' => 'Mempawah',
    'propinsi' => 'Kalimantan Barat',
    'region' => 'Region 6',
  ),
  321 => 
  array (
    'kode' => 323,
    'kota' => 'Sambas',
    'propinsi' => 'Kalimantan Barat',
    'region' => 'Region 6',
  ),
  322 => 
  array (
    'kode' => 324,
    'kota' => 'Sanggau',
    'propinsi' => 'Kalimantan Barat',
    'region' => 'Region 6',
  ),
  323 => 
  array (
    'kode' => 325,
    'kota' => 'Sekadau',
    'propinsi' => 'Kalimantan Barat',
    'region' => 'Region 6',
  ),
  324 => 
  array (
    'kode' => 326,
    'kota' => 'Sintang',
    'propinsi' => 'Kalimantan Barat',
    'region' => 'Region 6',
  ),
  325 => 
  array (
    'kode' => 327,
    'kota' => 'Pontianak',
    'propinsi' => 'Kalimantan Barat',
    'region' => 'Region 6',
  ),
  326 => 
  array (
    'kode' => 328,
    'kota' => 'Singkawang',
    'propinsi' => 'Kalimantan Barat',
    'region' => 'Region 6',
  ),
  327 => 
  array (
    'kode' => 329,
    'kota' => 'Balangan',
    'propinsi' => 'Kalimantan Selatan',
    'region' => 'Region 6',
  ),
  328 => 
  array (
    'kode' => 330,
    'kota' => 'Banjar',
    'propinsi' => 'Kalimantan Selatan',
    'region' => 'Region 6',
  ),
  329 => 
  array (
    'kode' => 331,
    'kota' => 'Barito Kuala',
    'propinsi' => 'Kalimantan Selatan',
    'region' => 'Region 6',
  ),
  330 => 
  array (
    'kode' => 332,
    'kota' => 'Hulu Sungai Selatan',
    'propinsi' => 'Kalimantan Selatan',
    'region' => 'Region 6',
  ),
  331 => 
  array (
    'kode' => 333,
    'kota' => 'Hulu Sungai Tengah',
    'propinsi' => 'Kalimantan Selatan',
    'region' => 'Region 6',
  ),
  332 => 
  array (
    'kode' => 334,
    'kota' => 'Hulu Sungai Utara',
    'propinsi' => 'Kalimantan Selatan',
    'region' => 'Region 6',
  ),
  333 => 
  array (
    'kode' => 335,
    'kota' => 'baru',
    'propinsi' => 'Kalimantan Selatan',
    'region' => 'Region 6',
  ),
  334 => 
  array (
    'kode' => 336,
    'kota' => 'Tabalong',
    'propinsi' => 'Kalimantan Selatan',
    'region' => 'Region 6',
  ),
  335 => 
  array (
    'kode' => 337,
    'kota' => 'Tanah Bumbu',
    'propinsi' => 'Kalimantan Selatan',
    'region' => 'Region 6',
  ),
  336 => 
  array (
    'kode' => 338,
    'kota' => 'Tanah Laut',
    'propinsi' => 'Kalimantan Selatan',
    'region' => 'Region 6',
  ),
  337 => 
  array (
    'kode' => 339,
    'kota' => 'Tapin',
    'propinsi' => 'Kalimantan Selatan',
    'region' => 'Region 6',
  ),
  338 => 
  array (
    'kode' => 340,
    'kota' => 'Banjarbaru',
    'propinsi' => 'Kalimantan Selatan',
    'region' => 'Region 6',
  ),
  339 => 
  array (
    'kode' => 341,
    'kota' => 'Banjarmasin',
    'propinsi' => 'Kalimantan Selatan',
    'region' => 'Region 6',
  ),
  340 => 
  array (
    'kode' => 342,
    'kota' => 'Barito Selatan',
    'propinsi' => 'Kalimantan Tengah',
    'region' => 'Region 7',
  ),
  341 => 
  array (
    'kode' => 343,
    'kota' => 'Barito Timur',
    'propinsi' => 'Kalimantan Tengah',
    'region' => 'Region 7',
  ),
  342 => 
  array (
    'kode' => 344,
    'kota' => 'Barito Utara',
    'propinsi' => 'Kalimantan Tengah',
    'region' => 'Region 7',
  ),
  343 => 
  array (
    'kode' => 345,
    'kota' => 'Gunung Mas',
    'propinsi' => 'Kalimantan Tengah',
    'region' => 'Region 7',
  ),
  344 => 
  array (
    'kode' => 346,
    'kota' => 'Kapuas',
    'propinsi' => 'Kalimantan Tengah',
    'region' => 'Region 7',
  ),
  345 => 
  array (
    'kode' => 347,
    'kota' => 'Katingan',
    'propinsi' => 'Kalimantan Tengah',
    'region' => 'Region 7',
  ),
  346 => 
  array (
    'kode' => 348,
    'kota' => 'waringin Barat',
    'propinsi' => 'Kalimantan Tengah',
    'region' => 'Region 7',
  ),
  347 => 
  array (
    'kode' => 349,
    'kota' => 'waringin Timur',
    'propinsi' => 'Kalimantan Tengah',
    'region' => 'Region 7',
  ),
  348 => 
  array (
    'kode' => 350,
    'kota' => 'Lamandau',
    'propinsi' => 'Kalimantan Tengah',
    'region' => 'Region 7',
  ),
  349 => 
  array (
    'kode' => 351,
    'kota' => 'Murung Raya',
    'propinsi' => 'Kalimantan Tengah',
    'region' => 'Region 7',
  ),
  350 => 
  array (
    'kode' => 352,
    'kota' => 'Pulang Pisau',
    'propinsi' => 'Kalimantan Tengah',
    'region' => 'Region 7',
  ),
  351 => 
  array (
    'kode' => 353,
    'kota' => 'Sukamara',
    'propinsi' => 'Kalimantan Tengah',
    'region' => 'Region 7',
  ),
  352 => 
  array (
    'kode' => 354,
    'kota' => 'Seruyan',
    'propinsi' => 'Kalimantan Tengah',
    'region' => 'Region 7',
  ),
  353 => 
  array (
    'kode' => 355,
    'kota' => 'Palangka Raya',
    'propinsi' => 'Kalimantan Tengah',
    'region' => 'Region 7',
  ),
  354 => 
  array (
    'kode' => 356,
    'kota' => 'Berau',
    'propinsi' => 'Kalimantan Timur',
    'region' => 'Region 6',
  ),
  355 => 
  array (
    'kode' => 357,
    'kota' => 'Kutai Barat',
    'propinsi' => 'Kalimantan Timur',
    'region' => 'Region 6',
  ),
  356 => 
  array (
    'kode' => 358,
    'kota' => 'Kutai Kartanegara',
    'propinsi' => 'Kalimantan Timur',
    'region' => 'Region 6',
  ),
  357 => 
  array (
    'kode' => 359,
    'kota' => 'Kutai Timur',
    'propinsi' => 'Kalimantan Timur',
    'region' => 'Region 6',
  ),
  358 => 
  array (
    'kode' => 360,
    'kota' => 'Mahakam Ulu',
    'propinsi' => 'Kalimantan Timur',
    'region' => 'Region 6',
  ),
  359 => 
  array (
    'kode' => 361,
    'kota' => 'Paser',
    'propinsi' => 'Kalimantan Timur',
    'region' => 'Region 6',
  ),
  360 => 
  array (
    'kode' => 362,
    'kota' => 'Penajam Paser Utara',
    'propinsi' => 'Kalimantan Timur',
    'region' => 'Region 6',
  ),
  361 => 
  array (
    'kode' => 363,
    'kota' => 'Balikpapan',
    'propinsi' => 'Kalimantan Timur',
    'region' => 'Region 6',
  ),
  362 => 
  array (
    'kode' => 364,
    'kota' => 'Bontang',
    'propinsi' => 'Kalimantan Timur',
    'region' => 'Region 6',
  ),
  363 => 
  array (
    'kode' => 365,
    'kota' => 'Samarinda',
    'propinsi' => 'Kalimantan Timur',
    'region' => 'Region 6',
  ),
  364 => 
  array (
    'kode' => 366,
    'kota' => 'Bulungan',
    'propinsi' => 'Kalimantan Utara',
    'region' => 'Region 7',
  ),
  365 => 
  array (
    'kode' => 367,
    'kota' => 'Malinau',
    'propinsi' => 'Kalimantan Utara',
    'region' => 'Region 7',
  ),
  366 => 
  array (
    'kode' => 368,
    'kota' => 'Nunukan',
    'propinsi' => 'Kalimantan Utara',
    'region' => 'Region 7',
  ),
  367 => 
  array (
    'kode' => 369,
    'kota' => 'Tana Tidung',
    'propinsi' => 'Kalimantan Utara',
    'region' => 'Region 7',
  ),
  368 => 
  array (
    'kode' => 370,
    'kota' => 'Tarakan',
    'propinsi' => 'Kalimantan Utara',
    'region' => 'Region 7',
  ),
  369 => 
  array (
    'kode' => 371,
    'kota' => 'Boalemo',
    'propinsi' => 'Gorontalo',
    'region' => 'Region 5',
  ),
  370 => 
  array (
    'kode' => 372,
    'kota' => 'Bone Bolango',
    'propinsi' => 'Gorontalo',
    'region' => 'Region 5',
  ),
  371 => 
  array (
    'kode' => 373,
    'kota' => 'Gorontalo',
    'propinsi' => 'Gorontalo',
    'region' => 'Region 5',
  ),
  372 => 
  array (
    'kode' => 374,
    'kota' => 'Gorontalo Utara',
    'propinsi' => 'Gorontalo',
    'region' => 'Region 5',
  ),
  373 => 
  array (
    'kode' => 375,
    'kota' => 'Pohuwato',
    'propinsi' => 'Gorontalo',
    'region' => 'Region 5',
  ),
  374 => 
  array (
    'kode' => 376,
    'kota' => 'Gorontalo',
    'propinsi' => 'Gorontalo',
    'region' => 'Region 5',
  ),
  375 => 
  array (
    'kode' => 377,
    'kota' => 'Bantaeng',
    'propinsi' => 'Sulawesi Selatan',
    'region' => 'Region 5',
  ),
  376 => 
  array (
    'kode' => 378,
    'kota' => 'Barru',
    'propinsi' => 'Sulawesi Selatan',
    'region' => 'Region 5',
  ),
  377 => 
  array (
    'kode' => 379,
    'kota' => 'Bone',
    'propinsi' => 'Sulawesi Selatan',
    'region' => 'Region 5',
  ),
  378 => 
  array (
    'kode' => 380,
    'kota' => 'Bulukumba',
    'propinsi' => 'Sulawesi Selatan',
    'region' => 'Region 5',
  ),
  379 => 
  array (
    'kode' => 381,
    'kota' => 'Enrekang',
    'propinsi' => 'Sulawesi Selatan',
    'region' => 'Region 5',
  ),
  380 => 
  array (
    'kode' => 382,
    'kota' => 'Gowa',
    'propinsi' => 'Sulawesi Selatan',
    'region' => 'Region 5',
  ),
  381 => 
  array (
    'kode' => 383,
    'kota' => 'Jeneponto',
    'propinsi' => 'Sulawesi Selatan',
    'region' => 'Region 5',
  ),
  382 => 
  array (
    'kode' => 384,
    'kota' => 'Kepulauan Selayar',
    'propinsi' => 'Sulawesi Selatan',
    'region' => 'Region 5',
  ),
  383 => 
  array (
    'kode' => 385,
    'kota' => 'Luwu',
    'propinsi' => 'Sulawesi Selatan',
    'region' => 'Region 5',
  ),
  384 => 
  array (
    'kode' => 386,
    'kota' => 'Luwu Timur',
    'propinsi' => 'Sulawesi Selatan',
    'region' => 'Region 5',
  ),
  385 => 
  array (
    'kode' => 387,
    'kota' => 'Luwu Utara',
    'propinsi' => 'Sulawesi Selatan',
    'region' => 'Region 5',
  ),
  386 => 
  array (
    'kode' => 388,
    'kota' => 'Maros',
    'propinsi' => 'Sulawesi Selatan',
    'region' => 'Region 5',
  ),
  387 => 
  array (
    'kode' => 389,
    'kota' => 'Pangkajene dan Kepulauan',
    'propinsi' => 'Sulawesi Selatan',
    'region' => 'Region 5',
  ),
  388 => 
  array (
    'kode' => 390,
    'kota' => 'Pinrang',
    'propinsi' => 'Sulawesi Selatan',
    'region' => 'Region 5',
  ),
  389 => 
  array (
    'kode' => 391,
    'kota' => 'Sidenreng Rappang',
    'propinsi' => 'Sulawesi Selatan',
    'region' => 'Region 5',
  ),
  390 => 
  array (
    'kode' => 392,
    'kota' => 'Sinjai',
    'propinsi' => 'Sulawesi Selatan',
    'region' => 'Region 5',
  ),
  391 => 
  array (
    'kode' => 393,
    'kota' => 'Soppeng',
    'propinsi' => 'Sulawesi Selatan',
    'region' => 'Region 5',
  ),
  392 => 
  array (
    'kode' => 394,
    'kota' => 'Takalar',
    'propinsi' => 'Sulawesi Selatan',
    'region' => 'Region 5',
  ),
  393 => 
  array (
    'kode' => 395,
    'kota' => 'Tana Toraja',
    'propinsi' => 'Sulawesi Selatan',
    'region' => 'Region 5',
  ),
  394 => 
  array (
    'kode' => 396,
    'kota' => 'Toraja Utara',
    'propinsi' => 'Sulawesi Selatan',
    'region' => 'Region 5',
  ),
  395 => 
  array (
    'kode' => 397,
    'kota' => 'Wajo',
    'propinsi' => 'Sulawesi Selatan',
    'region' => 'Region 5',
  ),
  396 => 
  array (
    'kode' => 398,
    'kota' => 'Makassar',
    'propinsi' => 'Sulawesi Selatan',
    'region' => 'Region 5',
  ),
  397 => 
  array (
    'kode' => 399,
    'kota' => 'Palopo',
    'propinsi' => 'Sulawesi Selatan',
    'region' => 'Region 5',
  ),
  398 => 
  array (
    'kode' => 400,
    'kota' => 'Parepare',
    'propinsi' => 'Sulawesi Selatan',
    'region' => 'Region 5',
  ),
  399 => 
  array (
    'kode' => 401,
    'kota' => 'Bombana',
    'propinsi' => 'Sulawesi Tenggara',
    'region' => 'Region 5',
  ),
  400 => 
  array (
    'kode' => 402,
    'kota' => 'Buton',
    'propinsi' => 'Sulawesi Tenggara',
    'region' => 'Region 5',
  ),
  401 => 
  array (
    'kode' => 403,
    'kota' => 'Buton Selatan',
    'propinsi' => 'Sulawesi Tenggara',
    'region' => 'Region 5',
  ),
  402 => 
  array (
    'kode' => 404,
    'kota' => 'Buton Tengah',
    'propinsi' => 'Sulawesi Tenggara',
    'region' => 'Region 5',
  ),
  403 => 
  array (
    'kode' => 405,
    'kota' => 'Buton Utara',
    'propinsi' => 'Sulawesi Tenggara',
    'region' => 'Region 5',
  ),
  404 => 
  array (
    'kode' => 406,
    'kota' => 'Kolaka',
    'propinsi' => 'Sulawesi Tenggara',
    'region' => 'Region 5',
  ),
  405 => 
  array (
    'kode' => 407,
    'kota' => 'Kolaka Timur',
    'propinsi' => 'Sulawesi Tenggara',
    'region' => 'Region 5',
  ),
  406 => 
  array (
    'kode' => 408,
    'kota' => 'Kolaka Utara',
    'propinsi' => 'Sulawesi Tenggara',
    'region' => 'Region 5',
  ),
  407 => 
  array (
    'kode' => 409,
    'kota' => 'Konawe',
    'propinsi' => 'Sulawesi Tenggara',
    'region' => 'Region 5',
  ),
  408 => 
  array (
    'kode' => 410,
    'kota' => 'Konawe Kepulauan',
    'propinsi' => 'Sulawesi Tenggara',
    'region' => 'Region 5',
  ),
  409 => 
  array (
    'kode' => 411,
    'kota' => 'Konawe Selatan',
    'propinsi' => 'Sulawesi Tenggara',
    'region' => 'Region 5',
  ),
  410 => 
  array (
    'kode' => 412,
    'kota' => 'Konawe Utara',
    'propinsi' => 'Sulawesi Tenggara',
    'region' => 'Region 5',
  ),
  411 => 
  array (
    'kode' => 413,
    'kota' => 'Muna',
    'propinsi' => 'Sulawesi Tenggara',
    'region' => 'Region 5',
  ),
  412 => 
  array (
    'kode' => 414,
    'kota' => 'Muna Barat',
    'propinsi' => 'Sulawesi Tenggara',
    'region' => 'Region 5',
  ),
  413 => 
  array (
    'kode' => 415,
    'kota' => 'Wakatobi',
    'propinsi' => 'Sulawesi Tenggara',
    'region' => 'Region 5',
  ),
  414 => 
  array (
    'kode' => 416,
    'kota' => 'Bau-Bau',
    'propinsi' => 'Sulawesi Tenggara',
    'region' => 'Region 5',
  ),
  415 => 
  array (
    'kode' => 417,
    'kota' => 'Kendari',
    'propinsi' => 'Sulawesi Tenggara',
    'region' => 'Region 5',
  ),
  416 => 
  array (
    'kode' => 418,
    'kota' => 'Banggai',
    'propinsi' => 'Sulawesi Tengah',
    'region' => 'Region 5',
  ),
  417 => 
  array (
    'kode' => 419,
    'kota' => 'Banggai Kepulauan',
    'propinsi' => 'Sulawesi Tengah',
    'region' => 'Region 5',
  ),
  418 => 
  array (
    'kode' => 420,
    'kota' => 'Banggai Laut',
    'propinsi' => 'Sulawesi Tengah',
    'region' => 'Region 5',
  ),
  419 => 
  array (
    'kode' => 421,
    'kota' => 'Buol',
    'propinsi' => 'Sulawesi Tengah',
    'region' => 'Region 5',
  ),
  420 => 
  array (
    'kode' => 422,
    'kota' => 'Donggala',
    'propinsi' => 'Sulawesi Tengah',
    'region' => 'Region 5',
  ),
  421 => 
  array (
    'kode' => 423,
    'kota' => 'Morowali',
    'propinsi' => 'Sulawesi Tengah',
    'region' => 'Region 5',
  ),
  422 => 
  array (
    'kode' => 424,
    'kota' => 'Morowali Utara',
    'propinsi' => 'Sulawesi Tengah',
    'region' => 'Region 5',
  ),
  423 => 
  array (
    'kode' => 425,
    'kota' => 'Parigi Moutong',
    'propinsi' => 'Sulawesi Tengah',
    'region' => 'Region 5',
  ),
  424 => 
  array (
    'kode' => 426,
    'kota' => 'Poso',
    'propinsi' => 'Sulawesi Tengah',
    'region' => 'Region 5',
  ),
  425 => 
  array (
    'kode' => 427,
    'kota' => 'Sigi',
    'propinsi' => 'Sulawesi Tengah',
    'region' => 'Region 5',
  ),
  426 => 
  array (
    'kode' => 428,
    'kota' => 'Tojo Una-Una',
    'propinsi' => 'Sulawesi Tengah',
    'region' => 'Region 5',
  ),
  427 => 
  array (
    'kode' => 429,
    'kota' => 'Toli-Toli',
    'propinsi' => 'Sulawesi Tengah',
    'region' => 'Region 5',
  ),
  428 => 
  array (
    'kode' => 430,
    'kota' => 'Palu',
    'propinsi' => 'Sulawesi Tengah',
    'region' => 'Region 5',
  ),
  429 => 
  array (
    'kode' => 431,
    'kota' => 'Bolaang Mongondow',
    'propinsi' => 'Sulawesi Utara',
    'region' => 'Region 5',
  ),
  430 => 
  array (
    'kode' => 432,
    'kota' => 'Bolaang Mongondow Selatan',
    'propinsi' => 'Sulawesi Utara',
    'region' => 'Region 5',
  ),
  431 => 
  array (
    'kode' => 433,
    'kota' => 'Bolaang Mongondow Timur',
    'propinsi' => 'Sulawesi Utara',
    'region' => 'Region 5',
  ),
  432 => 
  array (
    'kode' => 434,
    'kota' => 'Bolaang Mongondow Utara',
    'propinsi' => 'Sulawesi Utara',
    'region' => 'Region 5',
  ),
  433 => 
  array (
    'kode' => 435,
    'kota' => 'Kepulauan Sangihe',
    'propinsi' => 'Sulawesi Utara',
    'region' => 'Region 5',
  ),
  434 => 
  array (
    'kode' => 436,
    'kota' => 'Kepulauan Siau Tagulandang Biaro',
    'propinsi' => 'Sulawesi Utara',
    'region' => 'Region 5',
  ),
  435 => 
  array (
    'kode' => 437,
    'kota' => 'Kepulauan Talaud',
    'propinsi' => 'Sulawesi Utara',
    'region' => 'Region 5',
  ),
  436 => 
  array (
    'kode' => 438,
    'kota' => 'Minahasa',
    'propinsi' => 'Sulawesi Utara',
    'region' => 'Region 5',
  ),
  437 => 
  array (
    'kode' => 439,
    'kota' => 'Minahasa Selatan',
    'propinsi' => 'Sulawesi Utara',
    'region' => 'Region 5',
  ),
  438 => 
  array (
    'kode' => 440,
    'kota' => 'Minahasa Tenggara',
    'propinsi' => 'Sulawesi Utara',
    'region' => 'Region 5',
  ),
  439 => 
  array (
    'kode' => 441,
    'kota' => 'Minahasa Utara',
    'propinsi' => 'Sulawesi Utara',
    'region' => 'Region 5',
  ),
  440 => 
  array (
    'kode' => 442,
    'kota' => 'Bitung',
    'propinsi' => 'Sulawesi Utara',
    'region' => 'Region 5',
  ),
  441 => 
  array (
    'kode' => 443,
    'kota' => 'mobagu',
    'propinsi' => 'Sulawesi Utara',
    'region' => 'Region 5',
  ),
  442 => 
  array (
    'kode' => 444,
    'kota' => 'Manado',
    'propinsi' => 'Sulawesi Utara',
    'region' => 'Region 5',
  ),
  443 => 
  array (
    'kode' => 445,
    'kota' => 'Tomohon',
    'propinsi' => 'Sulawesi Utara',
    'region' => 'Region 5',
  ),
  444 => 
  array (
    'kode' => 446,
    'kota' => 'Majene',
    'propinsi' => 'Sulawesi Barat',
    'region' => 'Region 5',
  ),
  445 => 
  array (
    'kode' => 447,
    'kota' => 'Mamasa',
    'propinsi' => 'Sulawesi Barat',
    'region' => 'Region 5',
  ),
  446 => 
  array (
    'kode' => 448,
    'kota' => 'Mamuju',
    'propinsi' => 'Sulawesi Barat',
    'region' => 'Region 5',
  ),
  447 => 
  array (
    'kode' => 449,
    'kota' => 'Mamuju Tengah',
    'propinsi' => 'Sulawesi Barat',
    'region' => 'Region 5',
  ),
  448 => 
  array (
    'kode' => 450,
    'kota' => 'Mamuju Utara',
    'propinsi' => 'Sulawesi Barat',
    'region' => 'Region 5',
  ),
  449 => 
  array (
    'kode' => 451,
    'kota' => 'Polewali Mandar',
    'propinsi' => 'Sulawesi Barat',
    'region' => 'Region 5',
  ),
  450 => 
  array (
    'kode' => 452,
    'kota' => 'Mamuju',
    'propinsi' => 'Sulawesi Barat',
    'region' => 'Region 5',
  ),
  451 => 
  array (
    'kode' => 453,
    'kota' => 'Buru',
    'propinsi' => 'Maluku',
    'region' => 'Region 5',
  ),
  452 => 
  array (
    'kode' => 454,
    'kota' => 'Buru Selatan',
    'propinsi' => 'Maluku',
    'region' => 'Region 5',
  ),
  453 => 
  array (
    'kode' => 455,
    'kota' => 'Kepulauan Aru',
    'propinsi' => 'Maluku',
    'region' => 'Region 5',
  ),
  454 => 
  array (
    'kode' => 456,
    'kota' => 'Maluku Barat Daya',
    'propinsi' => 'Maluku',
    'region' => 'Region 5',
  ),
  455 => 
  array (
    'kode' => 457,
    'kota' => 'Maluku Tengah',
    'propinsi' => 'Maluku',
    'region' => 'Region 5',
  ),
  456 => 
  array (
    'kode' => 458,
    'kota' => 'Maluku Tenggara',
    'propinsi' => 'Maluku',
    'region' => 'Region 5',
  ),
  457 => 
  array (
    'kode' => 459,
    'kota' => 'Maluku Tenggara Barat',
    'propinsi' => 'Maluku',
    'region' => 'Region 5',
  ),
  458 => 
  array (
    'kode' => 460,
    'kota' => 'Seram Bagian Barat',
    'propinsi' => 'Maluku',
    'region' => 'Region 5',
  ),
  459 => 
  array (
    'kode' => 461,
    'kota' => 'Seram Bagian Timur',
    'propinsi' => 'Maluku',
    'region' => 'Region 5',
  ),
  460 => 
  array (
    'kode' => 462,
    'kota' => 'Ambon',
    'propinsi' => 'Maluku',
    'region' => 'Region 5',
  ),
  461 => 
  array (
    'kode' => 463,
    'kota' => 'Tual',
    'propinsi' => 'Maluku',
    'region' => 'Region 5',
  ),
  462 => 
  array (
    'kode' => 464,
    'kota' => 'Halmahera Barat',
    'propinsi' => 'Maluku Utara',
    'region' => 'Region 5',
  ),
  463 => 
  array (
    'kode' => 465,
    'kota' => 'Halmahera Tengah',
    'propinsi' => 'Maluku Utara',
    'region' => 'Region 5',
  ),
  464 => 
  array (
    'kode' => 466,
    'kota' => 'Halmahera Utara',
    'propinsi' => 'Maluku Utara',
    'region' => 'Region 5',
  ),
  465 => 
  array (
    'kode' => 467,
    'kota' => 'Halmahera Selatan',
    'propinsi' => 'Maluku Utara',
    'region' => 'Region 5',
  ),
  466 => 
  array (
    'kode' => 468,
    'kota' => 'Kepulauan Sula',
    'propinsi' => 'Maluku Utara',
    'region' => 'Region 5',
  ),
  467 => 
  array (
    'kode' => 469,
    'kota' => 'Halmahera Timur',
    'propinsi' => 'Maluku Utara',
    'region' => 'Region 5',
  ),
  468 => 
  array (
    'kode' => 470,
    'kota' => 'Pulau Morotai',
    'propinsi' => 'Maluku Utara',
    'region' => 'Region 5',
  ),
  469 => 
  array (
    'kode' => 471,
    'kota' => 'Pulau Taliabu',
    'propinsi' => 'Maluku Utara',
    'region' => 'Region 5',
  ),
  470 => 
  array (
    'kode' => 472,
    'kota' => 'Ternate',
    'propinsi' => 'Maluku Utara',
    'region' => 'Region 5',
  ),
  471 => 
  array (
    'kode' => 473,
    'kota' => 'Tidore Kepulauan',
    'propinsi' => 'Maluku Utara',
    'region' => 'Region 5',
  ),
  472 => 
  array (
    'kode' => 474,
    'kota' => 'Asmat',
    'propinsi' => 'Papua',
    'region' => 'Region 5',
  ),
  473 => 
  array (
    'kode' => 475,
    'kota' => 'Biak Numfor',
    'propinsi' => 'Papua',
    'region' => 'Region 5',
  ),
  474 => 
  array (
    'kode' => 476,
    'kota' => 'Boven Digoel',
    'propinsi' => 'Papua',
    'region' => 'Region 5',
  ),
  475 => 
  array (
    'kode' => 477,
    'kota' => 'Deiyai',
    'propinsi' => 'Papua',
    'region' => 'Region 5',
  ),
  476 => 
  array (
    'kode' => 478,
    'kota' => 'Dogiyai',
    'propinsi' => 'Papua',
    'region' => 'Region 5',
  ),
  477 => 
  array (
    'kode' => 479,
    'kota' => 'Intan Jaya',
    'propinsi' => 'Papua',
    'region' => 'Region 5',
  ),
  478 => 
  array (
    'kode' => 480,
    'kota' => 'Jayapura',
    'propinsi' => 'Papua',
    'region' => 'Region 5',
  ),
  479 => 
  array (
    'kode' => 481,
    'kota' => 'Jayawijaya',
    'propinsi' => 'Papua',
    'region' => 'Region 5',
  ),
  480 => 
  array (
    'kode' => 482,
    'kota' => 'Keerom',
    'propinsi' => 'Papua',
    'region' => 'Region 5',
  ),
  481 => 
  array (
    'kode' => 483,
    'kota' => 'Kepulauan Yapen',
    'propinsi' => 'Papua',
    'region' => 'Region 5',
  ),
  482 => 
  array (
    'kode' => 484,
    'kota' => 'Lanny Jaya',
    'propinsi' => 'Papua',
    'region' => 'Region 5',
  ),
  483 => 
  array (
    'kode' => 485,
    'kota' => 'Mamberamo Raya',
    'propinsi' => 'Papua',
    'region' => 'Region 5',
  ),
  484 => 
  array (
    'kode' => 486,
    'kota' => 'Mamberamo Tengah',
    'propinsi' => 'Papua',
    'region' => 'Region 5',
  ),
  485 => 
  array (
    'kode' => 487,
    'kota' => 'Mappi',
    'propinsi' => 'Papua',
    'region' => 'Region 5',
  ),
  486 => 
  array (
    'kode' => 488,
    'kota' => 'Merauke',
    'propinsi' => 'Papua',
    'region' => 'Region 5',
  ),
  487 => 
  array (
    'kode' => 489,
    'kota' => 'Mimika',
    'propinsi' => 'Papua',
    'region' => 'Region 5',
  ),
  488 => 
  array (
    'kode' => 490,
    'kota' => 'Nabire',
    'propinsi' => 'Papua',
    'region' => 'Region 5',
  ),
  489 => 
  array (
    'kode' => 491,
    'kota' => 'Nduga',
    'propinsi' => 'Papua',
    'region' => 'Region 5',
  ),
  490 => 
  array (
    'kode' => 492,
    'kota' => 'Paniai',
    'propinsi' => 'Papua',
    'region' => 'Region 5',
  ),
  491 => 
  array (
    'kode' => 493,
    'kota' => 'Pegunungan Bintang',
    'propinsi' => 'Papua',
    'region' => 'Region 5',
  ),
  492 => 
  array (
    'kode' => 494,
    'kota' => 'Puncak',
    'propinsi' => 'Papua',
    'region' => 'Region 5',
  ),
  493 => 
  array (
    'kode' => 495,
    'kota' => 'Puncak Jaya',
    'propinsi' => 'Papua',
    'region' => 'Region 5',
  ),
  494 => 
  array (
    'kode' => 496,
    'kota' => 'Sarmi',
    'propinsi' => 'Papua',
    'region' => 'Region 5',
  ),
  495 => 
  array (
    'kode' => 497,
    'kota' => 'Supiori',
    'propinsi' => 'Papua',
    'region' => 'Region 5',
  ),
  496 => 
  array (
    'kode' => 498,
    'kota' => 'Tolikara',
    'propinsi' => 'Papua',
    'region' => 'Region 5',
  ),
  497 => 
  array (
    'kode' => 499,
    'kota' => 'Waropen',
    'propinsi' => 'Papua',
    'region' => 'Region 5',
  ),
  498 => 
  array (
    'kode' => 500,
    'kota' => 'Yahukimo',
    'propinsi' => 'Papua',
    'region' => 'Region 5',
  ),
  499 => 
  array (
    'kode' => 501,
    'kota' => 'Yalimo',
    'propinsi' => 'Papua',
    'region' => 'Region 5',
  ),
  500 => 
  array (
    'kode' => 502,
    'kota' => 'Jayapura',
    'propinsi' => 'Papua',
    'region' => 'Region 5',
  ),
  501 => 
  array (
    'kode' => 503,
    'kota' => 'Fakfak',
    'propinsi' => 'Papua Barat',
    'region' => 'Region 5',
  ),
  502 => 
  array (
    'kode' => 504,
    'kota' => 'Kaimana',
    'propinsi' => 'Papua Barat',
    'region' => 'Region 5',
  ),
  503 => 
  array (
    'kode' => 505,
    'kota' => 'Manokwari',
    'propinsi' => 'Papua Barat',
    'region' => 'Region 5',
  ),
  504 => 
  array (
    'kode' => 506,
    'kota' => 'Manokwari Selatan',
    'propinsi' => 'Papua Barat',
    'region' => 'Region 5',
  ),
  505 => 
  array (
    'kode' => 507,
    'kota' => 'Maybrat',
    'propinsi' => 'Papua Barat',
    'region' => 'Region 5',
  ),
  506 => 
  array (
    'kode' => 508,
    'kota' => 'Pegunungan Arfak',
    'propinsi' => 'Papua Barat',
    'region' => 'Region 5',
  ),
  507 => 
  array (
    'kode' => 509,
    'kota' => 'Raja Ampat',
    'propinsi' => 'Papua Barat',
    'region' => 'Region 5',
  ),
  508 => 
  array (
    'kode' => 510,
    'kota' => 'Sorong',
    'propinsi' => 'Papua Barat',
    'region' => 'Region 5',
  ),
  509 => 
  array (
    'kode' => 511,
    'kota' => 'Sorong Selatan',
    'propinsi' => 'Papua Barat',
    'region' => 'Region 5',
  ),
  510 => 
  array (
    'kode' => 512,
    'kota' => 'Tambrauw',
    'propinsi' => 'Papua Barat',
    'region' => 'Region 5',
  ),
  511 => 
  array (
    'kode' => 513,
    'kota' => 'Teluk Bintuni',
    'propinsi' => 'Papua Barat',
    'region' => 'Region 5',
  ),
  512 => 
  array (
    'kode' => 514,
    'kota' => 'Teluk Wondama',
    'propinsi' => 'Papua Barat',
    'region' => 'Region 5',
  ),
);
            $now = now();
            foreach ($kotas as &$k) {
                $k['created_at'] = $now;
                $k['updated_at'] = $now;
            }
            foreach (array_chunk($kotas, 100) as $chunk) {
                DB::table('tb_kota')->insert($chunk);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_kota');
        Schema::dropIfExists('tb_area');
    }
};