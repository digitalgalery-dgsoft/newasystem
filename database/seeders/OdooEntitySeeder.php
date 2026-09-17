<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OdooEntitySeeder extends Seeder
{
    public function run(): void
    {
        $entities = [
            [
                'code' => 'AMK',
                'name' => 'PT Arina Multi Karya',
                'odoo_url' => 'https://odoo.arinamultikarya.com',
                'odoo_db' => 'AMK_LIVE',
                'odoo_username' => '',
                'odoo_api_key' => '',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'AKP',
                'name' => 'PT Alva Karya Perkasa',
                'odoo_url' => 'https://odoo.arinamultikarya.com',
                'odoo_db' => 'AKP_LIVE',
                'odoo_username' => '',
                'odoo_api_key' => '',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'ATK',
                'name' => 'PT Anugrah Terpercaya Kerja',
                'odoo_url' => 'https://odoo.arinamultikarya.com',
                'odoo_db' => 'ATK_LIVE',
                'odoo_username' => '',
                'odoo_api_key' => '',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'ABO',
                'name' => 'PT Arina Bintang Operasional',
                'odoo_url' => 'https://odoo.arinamultikarya.com',
                'odoo_db' => 'ABO_LIVE',
                'odoo_username' => '',
                'odoo_api_key' => '',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'ATB',
                'name' => 'PT Anugrah Tri Berkah',
                'odoo_url' => 'https://odoo.arinamultikarya.com',
                'odoo_db' => 'ATB_LIVE',
                'odoo_username' => '',
                'odoo_api_key' => '',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($entities as $entity) {
            DB::table('odoo_entities')->updateOrInsert(
                ['code' => $entity['code']],
                $entity
            );
        }
    }
}
