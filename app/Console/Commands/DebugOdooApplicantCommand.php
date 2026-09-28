<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OdooEntity;
use App\Models\Candidate;
use App\Services\OdooSyncService;
use App\Services\OdooRecruitmentSyncService;

class DebugOdooApplicantCommand extends Command
{
    protected $signature = 'odoo:debug-applicant {nik=3528044204980003}';
    protected $description = 'Debug Odoo applicant query and sync';

    public function handle()
    {
        $nik = $this->argument('nik');
        $this->info("=== DEBUG ODOO APPLICANT FOR NIK: {$nik} ===");

        $cand = Candidate::where('nik', $nik)->first();
        if ($cand) {
            $this->info("Candidate ID: {$cand->id}, Name: {$cand->full_name}, Jenis: {$cand->jenis}, Status: {$cand->status}, Status Kandidat: {$cand->status_kandidat}, Created: {$cand->created_at}");
            $this->info("Current Odoo fields: app_id=" . ($cand->odoo_applicant_id ?? 'null') . ", stage=" . ($cand->odoo_stage_name ?? 'null') . ", entity=" . ($cand->odoo_entity ?? 'null'));
        } else {
            $this->error("Candidate with NIK {$nik} not found in database!");
        }

        $entities = OdooEntity::where('is_active', true)->get();
        $this->info("Active entities: " . $entities->count());

        foreach ($entities as $e) {
            $this->info("--- Entity: [{$e->code}] {$e->name} ---");
            $this->info("Host: {$e->odoo_host}, DB: {$e->odoo_db}, User: {$e->odoo_username}");

            if (!$e->isConfigured()) {
                $this->warn("Entity is NOT configured properly.");
                continue;
            }

            try {
                $service = OdooSyncService::fromEntity($e);
                $uid = $service->authenticate();
                $this->info("Authenticated UID: {$uid}");

                // 1. Search by exact no_ktp
                $exact = $service->xmlRpcCall('/xmlrpc/2/object', 'execute_kw', [
                    $e->odoo_db, $uid, $e->odoo_api_key,
                    'hr.applicant', 'search_read',
                    [[
                        ['no_ktp', '=', $nik],
                    ]],
                    [
                        'fields' => ['id', 'name', 'partner_name', 'no_ktp', 'stage_id', 'department_id', 'job_id', 'user_id', 'write_date', 'create_date', 'active'],
                        'context' => ['active_test' => false],
                        'limit' => 5,
                        'order' => 'write_date desc, id desc',
                    ]
                ]);

                $this->info("Search by exact no_ktp results count: " . count($exact));
                foreach ($exact as $a) {
                    $this->info(" * ID: {$a['id']}, Name: {$a['name']}, Partner: {$a['partner_name']}, KTP: " . ($a['no_ktp'] ?? 'null') . ", Stage: " . json_encode($a['stage_id']) . ", Active: " . var_export($a['active'], true) . ", Created: {$a['create_date']}, Write: {$a['write_date']}");
                }

                // 2. Search by ilike no_ktp
                if (empty($exact)) {
                    $ilike = $service->xmlRpcCall('/xmlrpc/2/object', 'execute_kw', [
                        $e->odoo_db, $uid, $e->odoo_api_key,
                        'hr.applicant', 'search_read',
                        [[
                            ['no_ktp', 'ilike', $nik],
                        ]],
                        [
                            'fields' => ['id', 'name', 'partner_name', 'no_ktp', 'stage_id', 'active', 'create_date'],
                            'context' => ['active_test' => false],
                            'limit' => 5
                        ]
                    ]);
                    $this->info("Search by ilike no_ktp count: " . count($ilike));
                    foreach ($ilike as $a) {
                        $this->info(" * ID: {$a['id']}, Name: {$a['name']}, KTP: " . ($a['no_ktp'] ?? 'null'));
                    }
                }

                // 3. Search by name
                if (empty($exact)) {
                    $nameSearch = $service->xmlRpcCall('/xmlrpc/2/object', 'execute_kw', [
                        $e->odoo_db, $uid, $e->odoo_api_key,
                        'hr.applicant', 'search_read',
                        [[
                            '|',
                            ['partner_name', 'ilike', 'Yayuk'],
                            ['name', 'ilike', 'Yayuk']
                        ]],
                        [
                            'fields' => ['id', 'name', 'partner_name', 'no_ktp', 'stage_id', 'active'],
                            'context' => ['active_test' => false],
                            'limit' => 5
                        ]
                    ]);
                    $this->info("Search by name 'Yayuk' count: " . count($nameSearch));
                    foreach ($nameSearch as $a) {
                        $this->info(" * ID: {$a['id']}, Name: {$a['name']}, Partner: {$a['partner_name']}, KTP: " . ($a['no_ktp'] ?? 'null') . ", Stage: " . json_encode($a['stage_id']));
                    }
                }

            } catch (\Throwable $err) {
                $this->error("Error: " . $err->getMessage());
            }
        }

        if ($cand) {
            $this->info("--- Testing OdooRecruitmentSyncService::syncSingleCandidate ---");
            $syncService = app(OdooRecruitmentSyncService::class);
            $res = $syncService->syncSingleCandidate($cand);
            $this->info("Sync result: " . json_encode($res));
            $cand->refresh();
            $this->info("Cand after sync: app_id=" . ($cand->odoo_applicant_id ?? 'null') . ", stage=" . ($cand->odoo_stage_name ?? 'null') . ", status_kandidat=" . ($cand->status_kandidat ?? 'null'));
        }

        return 0;
    }
}
