<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\OdooEntity;
use App\Models\OdooSyncLog;
use App\Models\Principle;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OdooSyncService
{
    private string $url;
    private string $db;
    private string $username;
    private string $apiKey;
    private ?int $uid = null;

    public function __construct(string $url, string $db, string $username, string $apiKey)
    {
        $this->url      = rtrim(trim($url), '/');
        $this->db       = trim($db);
        $this->username = trim($username);
        $this->apiKey   = trim($apiKey);
    }

    /**
     * Build an Odoo XML-RPC service instance from an OdooEntity.
     */
    public static function fromEntity(OdooEntity $entity): ?self
    {
        if (!$entity->isConfigured()) {
            return null;
        }
        return new self($entity->odoo_url, $entity->odoo_db, $entity->odoo_username, $entity->odoo_api_key);
    }

    /**
     * List all database names available on the Odoo server.
     */
    public static function listDatabases(string $url): array
    {
        $cleanUrl = rtrim(trim($url), '/');
        $service = new self($cleanUrl, '', '', '');
        try {
            $result = $service->xmlRpcCall('/xmlrpc/2/db', 'list', []);
            return is_array($result) ? $result : [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Authenticate with Odoo and return uid.
     */
    public function authenticate(): int
    {
        if ($this->uid !== null) {
            return $this->uid;
        }

        try {
            $response = $this->xmlRpcCall('/xmlrpc/2/common', 'authenticate', [
                $this->db,
                $this->username,
                $this->apiKey,
                [],
            ]);
        } catch (\Throwable $e) {
            $msg = $e->getMessage();
            if (str_contains($msg, 'KeyError') || str_contains($msg, 'registry') || str_contains($msg, 'Fault [1]')) {
                $availableDbs = self::listDatabases($this->url);
                $dbListText = !empty($availableDbs) 
                    ? (' Database terdeteksi di server ini: [' . implode(', ', $availableDbs) . '].') 
                    : '';
                throw new \Exception("Database '{$this->db}' tidak ditemukan di server Odoo ({$this->url})." . $dbListText);
            }
            throw $e;
        }

        if (!$response || !is_int($response)) {
            throw new \Exception('Autentikasi Odoo gagal. Pastikan Database, Username/Email, dan API Key benar.');
        }

        $this->uid = $response;
        return $this->uid;
    }

    /**
     * Test connection and return Odoo server version & UID.
     */
    public function testConnection(): array
    {
        $version = $this->xmlRpcCall('/xmlrpc/2/common', 'version', []);
        $serverVersion = $version['server_version'] ?? 'Unknown';

        $uid = $this->authenticate();

        return [
            'success'        => true,
            'uid'            => $uid,
            'server_version' => $serverVersion,
            'database'       => $this->db,
            'url'            => $this->url,
        ];
    }

    /**
    /**
     * Sync Employees from Odoo for a specific entity.
     * Supports category filtering: 'all', 'inhouse', 'ratecard'.
     * By default, only synchronizes active non-departed employees and SKIPS existing NIKs ($updateExisting = false).
     */
    public function syncEmployees(OdooEntity $entity, ?callable $progressCallback = null, string $category = 'all', bool $updateExisting = false): array
    {
        $log = function(string $type, string $message, ?array $meta = null) use ($progressCallback) {
            if ($progressCallback && is_callable($progressCallback)) {
                call_user_func($progressCallback, $type, $message, $meta);
            }
        };

        $category = strtolower(trim($category ?: 'all'));
        $categoryLabel = match($category) {
            'inhouse'  => 'Inhouse Saja (5 Entitas AMK, AKP, ATK, ABO, ATB)',
            'ratecard' => 'RateCard Saja (Non-Inhouse)',
            default    => 'Semua Kategori (Inhouse & RateCard)',
        };

        $uid = $this->authenticate();
        $batchId = 'SYNC-' . $entity->code . '-' . date('Ymd-His') . '-' . Str::random(4);

        $created   = 0;
        $updated   = 0;
        $resigned  = 0;
        $skipped   = 0;
        $processed = 0;
        $errors    = [];
        $offset    = 0;
        $limit     = 250;
        $batchNum  = 0;

        $log('info', "Memulai sinkronisasi karyawan AKTIF untuk entitas [{$entity->code}] {$entity->name} (Filter: {$categoryLabel})...", [
            'entity'   => $entity->code,
            'category' => $category,
        ]);

        do {
            $batchNum++;
            $log('batch', "Mengambil batch #{$batchNum} dari Odoo (Offset: {$offset}, Limit: {$limit})...", [
                'batch'  => $batchNum,
                'offset' => $offset,
                'limit'  => $limit,
            ]);

            try {
                // Hanya ambil employee aktif dari Odoo
                $records = $this->xmlRpcCall('/xmlrpc/2/object', 'execute_kw', [
                    $this->db, $uid, $this->apiKey,
                    'hr.employee', 'search_read',
                    [[['active', '=', true], ['departure_date', '=', false]]],
                    [
                        'fields' => [
                            'id', 'name', 'registration_number', 'identification_id',
                            'mobile_phone', 'work_email', 'private_email',
                            'department_id', 'job_id', 'principle_id', 'area_id',
                            'first_contract_date', 'active', 'departure_date',
                        ],
                        'context' => ['active_test' => true],
                        'order'   => 'write_date desc, id desc',
                        'limit'   => $limit,
                        'offset'  => $offset,
                    ],
                ]);
            } catch (\Throwable $e) {
                $errMsg = "Gagal mengambil batch #{$batchNum}: " . $e->getMessage();
                $errors[] = $errMsg;
                $log('error', "Gagal pada batch #{$batchNum}: " . $e->getMessage(), ['error' => $errMsg]);
                break;
            }

            $recCount = is_array($records) ? count($records) : 0;
            if ($recCount === 0) {
                $log('info', "Tidak ada data tambahan lagi dari Odoo.");
                break;
            }

            $log('batch_received', "Diterima {$recCount} data karyawan dari Odoo pada batch #{$batchNum}. Memproses record...", [
                'count' => $recCount,
                'batch' => $batchNum,
            ]);

            foreach ($records as $rec) {
                $processed++;
                $odooId = $rec['id'] ?? null;
                $nama = trim((string)($rec['name'] ?? 'Tanpa Nama'));
                $rawNik = trim((string)($rec['identification_id'] ?: $rec['registration_number'] ?: ''));
                $nik = $rawNik ?: ('OD-' . $odooId);

                try {
                    // Filter 1: Hanya Employee Aktif Saja
                    $isActive = (bool)($rec['active'] ?? true);
                    if (!$isActive || !empty($rec['departure_date'])) {
                        $skipped++;
                        $log('item_skip', "⏭️ [{$entity->code}] Lewati ID {$odooId} - {$nama}: Resign/Non-Aktif", [
                            'action'    => 'skipped',
                            'reason'    => 'non_active',
                            'odoo_id'   => $odooId,
                            'name'      => $nama,
                            'processed' => $processed,
                            'created'   => $created,
                            'updated'   => $updated,
                            'skipped'   => $skipped,
                        ]);
                        continue;
                    }

                    // Principle
                    $principleName = is_array($rec['principle_id']) ? $rec['principle_id'][1] : null;

                    // Filter: Abaikan karyawan dengan prinsiple PT BUDGET (AMK, AKP, ATK)
                    if (!empty($principleName) && stripos($principleName, 'BUDGET') !== false) {
                        $skipped++;
                        $log('item_skip', "⏭️ [{$entity->code}] Lewati {$nik} - {$nama}: Prinsiple BUDGET ({$principleName})", [
                            'action'    => 'skipped',
                            'reason'    => 'budget_principle',
                            'nik'       => $nik,
                            'name'      => $nama,
                            'processed' => $processed,
                            'created'   => $created,
                            'updated'   => $updated,
                            'skipped'   => $skipped,
                        ]);
                        continue;
                    }

                    $principleId = null;
                    if (!empty($principleName)) {
                        $p = Principle::firstOrCreate(['name' => $principleName]);
                        $principleId = $p->id;
                    }

                    // Filter 2: Inhouse vs RateCard
                    $tipeKaryawan = Employee::determineTipeKaryawan($principleName);
                    if ($category === 'inhouse' && $tipeKaryawan !== 'Inhouse') {
                        $skipped++;
                        $log('item_skip', "⏭️ [{$entity->code}] Lewati {$nik} - {$nama}: RateCard (Filter: Inhouse)", [
                            'action'    => 'skipped',
                            'reason'    => 'category_mismatch',
                            'nik'       => $nik,
                            'name'      => $nama,
                            'processed' => $processed,
                            'created'   => $created,
                            'updated'   => $updated,
                            'skipped'   => $skipped,
                        ]);
                        continue;
                    }
                    if ($category === 'ratecard' && $tipeKaryawan !== 'RateCard') {
                        $skipped++;
                        $log('item_skip', "⏭️ [{$entity->code}] Lewati {$nik} - {$nama}: Inhouse (Filter: RateCard)", [
                            'action'    => 'skipped',
                            'reason'    => 'category_mismatch',
                            'nik'       => $nik,
                            'name'      => $nama,
                            'processed' => $processed,
                            'created'   => $created,
                            'updated'   => $updated,
                            'skipped'   => $skipped,
                        ]);
                        continue;
                    }

                    $nip = trim((string)($rec['registration_number'] ?: '')) ?: null;
                    $email = $rec['work_email'] ?: ($rec['private_email'] ?: null);
                    $telepon = $rec['mobile_phone'] ?: null;
                    $tanggalJoin = !empty($rec['first_contract_date']) ? $rec['first_contract_date'] : null;

                    $jabatan = is_array($rec['job_id']) ? $rec['job_id'][1] : null;
                    $divisi = is_array($rec['department_id']) ? $rec['department_id'][1] : null;
                    $area = is_array($rec['area_id']) ? $rec['area_id'][1] : null;

                    $status = 'Aktiv';

                    $dataToSave = [
                        'nik'           => $nik,
                        'nip'           => $nip,
                        'nama_karyawan' => $nama,
                        'email'         => $email,
                        'telepon'       => $telepon,
                        'tanggal_join'  => $tanggalJoin,
                        'jabatan'       => $jabatan ?: 'Staff',
                        'divisi'        => $divisi,
                        'principle_id'  => $principleId,
                        'prinsiple'     => $principleName,
                        'tipe_karyawan' => $tipeKaryawan,
                        'area'          => $area ?: 'Pusat',
                        'status'        => $status,
                        'entity'        => $entity->code,
                        'odoo_id'       => $odooId,
                        'last_sync_at'  => now(),
                    ];

                    // 1. Search by NIK if exists
                    $employee = null;
                    if (!empty($rawNik)) {
                        $employee = Employee::where('nik', $rawNik)->first();
                    }

                    // 2. Search by NIP if not found
                    if (!$employee && !empty($nip)) {
                        $employee = Employee::where('nip', $nip)->first();
                    }

                    // 3. Search by odoo_id & entity if not found
                    if (!$employee && !empty($odooId)) {
                        $employee = Employee::where('odoo_id', $odooId)->where('entity', $entity->code)->first();
                    }

                    if ($employee) {
                        // Aturan: NIK yang sudah masuk JANGAN DIUPDATE pada sync active biasa/hourly
                        if (!$updateExisting) {
                            $skipped++;
                            $log('item_skip', "⏭️ [{$entity->code}] Lewati {$nik} - {$nama}: NIK sudah ada di database (tidak diupdate)", [
                                'action'    => 'skipped',
                                'reason'    => 'nik_exists',
                                'nik'       => $nik,
                                'name'      => $nama,
                                'processed' => $processed,
                                'created'   => $created,
                                'updated'   => $updated,
                                'skipped'   => $skipped,
                            ]);
                            continue;
                        }

                        $employee->update($dataToSave);
                        $updated++;
                        $log('item_update', "🔄 [{$entity->code}] #{$processed} {$nik} - {$nama} ({$jabatan} | {$tipeKaryawan}) -> DIPERBARUI", [
                            'action'    => 'updated',
                            'entity'    => $entity->code,
                            'nik'       => $nik,
                            'name'      => $nama,
                            'job'       => $jabatan,
                            'type'      => $tipeKaryawan,
                            'processed' => $processed,
                            'created'   => $created,
                            'updated'   => $updated,
                            'skipped'   => $skipped,
                        ]);
                    } else {
                        Employee::create($dataToSave);
                        $created++;
                        $log('item_create', "👤 [{$entity->code}] #{$processed} {$nik} - {$nama} ({$jabatan} | {$tipeKaryawan}) -> DIBUAT (BARU)", [
                            'action'    => 'created',
                            'entity'    => $entity->code,
                            'nik'       => $nik,
                            'name'      => $nama,
                            'job'       => $jabatan,
                            'type'      => $tipeKaryawan,
                            'processed' => $processed,
                            'created'   => $created,
                            'updated'   => $updated,
                            'skipped'   => $skipped,
                        ]);
                    }

                } catch (\Throwable $e) {
                    $errText = 'Error [ID Odoo: ' . ($rec['id'] ?? '?') . ']: ' . $e->getMessage();
                    $errors[] = $errText;
                    $log('item_error', "❌ [{$entity->code}] Error ID {$odooId} ({$nama}): " . $e->getMessage(), [
                        'action'    => 'error',
                        'error'     => $e->getMessage(),
                        'processed' => $processed,
                        'created'   => $created,
                        'updated'   => $updated,
                        'errors'    => count($errors),
                    ]);
                }
            }

            $offset += $limit;

        } while ($recCount === $limit);

        $totalActive = Employee::where('entity', $entity->code)->where('status', 'Aktiv')->count();
        $syncCounts = [
            'created'  => $created,
            'updated'  => $updated,
            'resigned' => 0,
            'total'    => $totalActive,
            'errors'   => count($errors),
        ];

        $syncStatus = empty($errors) ? 'success' : ($created > 0 || $updated > 0 ? 'partial' : 'failed');
        $syncMessage = "Sync selesai [{$categoryLabel} - Aktif]. Baru: {$created} | Diperbarui: {$updated}" . (count($errors) > 0 ? ' | Error: ' . count($errors) : '');

        // Update entity state
        $entity->update([
            'last_sync_at'     => now(),
            'last_sync_status' => $syncStatus,
            'last_sync_message'=> $syncMessage,
            'sync_counts'      => $syncCounts,
        ]);

        // Create log record
        OdooSyncLog::create([
            'batch_id'             => $batchId,
            'entity_code'          => $entity->code,
            'sync_type'            => 'employee_' . $category,
            'trigger_type'         => 'manual',
            'status'               => $syncStatus,
            'new_count'            => $created,
            'update_count'         => $updated,
            'resign_count'         => 0,
            'total_employee_count' => $totalActive,
            'details'              => [
                'entity_name' => $entity->name,
                'category'    => $category,
                'batch_id'    => $batchId,
                'batches_run' => $batchNum,
                'errors'      => array_slice($errors, 0, 10),
            ],
            'error_message'        => !empty($errors) ? implode('; ', array_slice($errors, 0, 3)) : null,
        ]);

        $log('summary', $syncMessage);

        return [
            'success'   => ($syncStatus !== 'failed'),
            'status'    => $syncStatus,
            'category'  => $category,
            'batch_id'  => $batchId,
            'created'   => $created,
            'updated'   => $updated,
            'resigned'  => 0,
            'total'     => $totalActive,
            'message'   => $syncMessage,
            'errors'    => $errors,
        ];
    }

    /**
     * Midnight sync: Check employee data updates and resignation status from Odoo.
     * Dijalankan setiap tengah malam (00:00).
     *
     * 1. Pengecekan Resign:
     *    Mengambil daftar seluruh karyawan berstatus 'Aktiv' di database lokal untuk entitas ini.
     *    Memeriksa status mereka di Odoo (active == false ATAU departure_date terisi).
     *    Jika di Odoo sudah non-aktif / resign, ubah status di database lokal menjadi 'Resign'.
     *
     * 2. Pengecekan Update Data:
     *    Jika di Odoo masih aktif dan tidak ada departure_date, perbarui data lokal (jabatan, area, divisi, prinsip, dll).
     */
    public function syncUpdatesAndResigns(OdooEntity $entity, ?callable $progressCallback = null): array
    {
        $log = function(string $type, string $message, ?array $meta = null) use ($progressCallback) {
            if ($progressCallback && is_callable($progressCallback)) {
                call_user_func($progressCallback, $type, $message, $meta);
            }
        };

        $uid = $this->authenticate();
        $batchId = 'SYNC-MIDNIGHT-' . $entity->code . '-' . date('Ymd-His') . '-' . Str::random(4);

        $updated   = 0;
        $resigned  = 0;
        $skipped   = 0;
        $processed = 0;
        $errors    = [];

        // Ambil seluruh karyawan berstatus 'Aktiv' di entitas ini
        $activeEmployees = Employee::where('entity', $entity->code)
            ->where('status', 'Aktiv')
            ->orderBy('id')
            ->get();

        $totalLocal = $activeEmployees->count();
        $log('info', "Memulai pemeriksaan tengah malam (Update & Resign) untuk entitas [{$entity->code}] {$entity->name} ({$totalLocal} karyawan lokal aktif)...", [
            'entity' => $entity->code,
            'total'  => $totalLocal,
        ]);

        if ($totalLocal === 0) {
            $log('info', "Tidak ada karyawan aktif lokal yang perlu diperiksa untuk entitas {$entity->code}.");
            return [
                'success'  => true,
                'updated'  => 0,
                'resigned' => 0,
                'total'    => 0,
                'message'  => "Tidak ada karyawan aktif lokal untuk [{$entity->code}].",
                'errors'   => [],
            ];
        }

        // Process in chunks of 200 employees to avoid overloading XML-RPC
        $chunks = $activeEmployees->chunk(200);

        foreach ($chunks as $chunkIndex => $chunk) {
            $chunkNum = $chunkIndex + 1;
            $log('batch', "Memeriksa batch #{$chunkNum} (" . $chunk->count() . " karyawan) ke Odoo...", [
                'chunk' => $chunkNum,
                'count' => $chunk->count(),
            ]);

            $odooIdMap = [];
            $nikMap = [];

            foreach ($chunk as $emp) {
                if (!empty($emp->odoo_id)) {
                    $odooIdMap[$emp->odoo_id] = $emp;
                }
                if (!empty($emp->nik) && !str_starts_with($emp->nik, 'OD-')) {
                    $nikMap[$emp->nik] = $emp;
                }
            }

            $odooIds = array_keys($odooIdMap);
            $niks = array_keys($nikMap);

            $domain = [];
            if (!empty($odooIds) && !empty($niks)) {
                $domain = [
                    '|',
                    ['id', 'in', $odooIds],
                    ['identification_id', 'in', $niks],
                ];
            } elseif (!empty($odooIds)) {
                $domain = [['id', 'in', $odooIds]];
            } elseif (!empty($niks)) {
                $domain = [['identification_id', 'in', $niks]];
            } else {
                continue;
            }

            try {
                $records = $this->xmlRpcCall('/xmlrpc/2/object', 'execute_kw', [
                    $this->db, $uid, $this->apiKey,
                    'hr.employee', 'search_read',
                    [$domain],
                    [
                        'fields' => [
                            'id', 'name', 'registration_number', 'identification_id',
                            'mobile_phone', 'work_email', 'private_email',
                            'department_id', 'job_id', 'principle_id', 'area_id',
                            'first_contract_date', 'active', 'departure_date',
                        ],
                        'context' => ['active_test' => false],
                        'limit'   => count($chunk) * 2,
                    ],
                ]);
            } catch (\Throwable $e) {
                $errMsg = "Gagal memeriksa batch #{$chunkNum}: " . $e->getMessage();
                $errors[] = $errMsg;
                $log('error', $errMsg, ['error' => $errMsg]);
                continue;
            }

            if (is_array($records)) {
                foreach ($records as $rec) {
                    $processed++;
                    $recOdooId = $rec['id'] ?? null;
                    $recNik = trim((string)($rec['identification_id'] ?: $rec['registration_number'] ?: ''));
                    $recName = trim((string)($rec['name'] ?? 'Tanpa Nama'));

                    // Find the local employee
                    $localEmp = null;
                    if ($recOdooId && isset($odooIdMap[$recOdooId])) {
                        $localEmp = $odooIdMap[$recOdooId];
                    } elseif (!empty($recNik) && isset($nikMap[$recNik])) {
                        $localEmp = $nikMap[$recNik];
                    }

                    if (!$localEmp) {
                        continue;
                    }

                    $isActive = (bool)($rec['active'] ?? true);
                    $departureDate = !empty($rec['departure_date']) ? $rec['departure_date'] : null;

                    // 1. Check if Resigned
                    if (!$isActive || !empty($departureDate)) {
                        $localEmp->update([
                            'status'       => 'Resign',
                            'last_sync_at' => now(),
                        ]);
                        $resigned++;
                        $log('item_resign', "🚪 [{$entity->code}] #{$processed} {$localEmp->nik} - {$localEmp->nama_karyawan} -> STATUS RESIGN (Odoo: " . ($departureDate ? "Departure {$departureDate}" : "Non-Aktif") . ")", [
                            'action'    => 'resigned',
                            'entity'    => $entity->code,
                            'nik'       => $localEmp->nik,
                            'name'      => $localEmp->nama_karyawan,
                            'processed' => $processed,
                            'updated'   => $updated,
                            'resigned'  => $resigned,
                        ]);
                    } else {
                        // 2. Check Data Updates for active employee
                        $principleName = is_array($rec['principle_id']) ? $rec['principle_id'][1] : null;

                        // Filter: Abaikan karyawan dengan prinsiple PT BUDGET (AMK, AKP, ATK)
                        if (!empty($principleName) && stripos($principleName, 'BUDGET') !== false) {
                            continue;
                        }

                        $principleId = null;
                        if (!empty($principleName)) {
                            $p = Principle::firstOrCreate(['name' => $principleName]);
                            $principleId = $p->id;
                        }

                        $tipeKaryawan = Employee::determineTipeKaryawan($principleName);
                        $nip = trim((string)($rec['registration_number'] ?: '')) ?: null;
                        $email = $rec['work_email'] ?: ($rec['private_email'] ?: null);
                        $telepon = $rec['mobile_phone'] ?: null;
                        $tanggalJoin = !empty($rec['first_contract_date']) ? $rec['first_contract_date'] : null;

                        $jabatan = is_array($rec['job_id']) ? $rec['job_id'][1] : null;
                        $divisi = is_array($rec['department_id']) ? $rec['department_id'][1] : null;
                        $area = is_array($rec['area_id']) ? $rec['area_id'][1] : null;

                        $localEmp->update([
                            'nip'           => $nip ?: $localEmp->nip,
                            'nama_karyawan' => $recName,
                            'email'         => $email ?: $localEmp->email,
                            'telepon'       => $telepon ?: $localEmp->telepon,
                            'tanggal_join'  => $tanggalJoin ?: $localEmp->tanggal_join,
                            'jabatan'       => $jabatan ?: $localEmp->jabatan,
                            'divisi'        => $divisi ?: $localEmp->divisi,
                            'principle_id'  => $principleId ?: $localEmp->principle_id,
                            'prinsiple'     => $principleName ?: $localEmp->prinsiple,
                            'tipe_karyawan' => $tipeKaryawan,
                            'area'          => $area ?: $localEmp->area,
                            'status'        => 'Aktiv',
                            'odoo_id'       => $recOdooId,
                            'last_sync_at'  => now(),
                        ]);

                        $updated++;
                        $log('item_update', "🔄 [{$entity->code}] #{$processed} {$localEmp->nik} - {$localEmp->nama_karyawan} ({$jabatan} | {$principleName}) -> DATA DIPERBARUI", [
                            'action'    => 'updated',
                            'entity'    => $entity->code,
                            'nik'       => $localEmp->nik,
                            'name'      => $localEmp->nama_karyawan,
                            'job'       => $jabatan,
                            'processed' => $processed,
                            'updated'   => $updated,
                            'resigned'  => $resigned,
                        ]);
                    }
                }
            }
        }

        $totalActiveNow = Employee::where('entity', $entity->code)->where('status', 'Aktiv')->count();
        $totalResignNow = Employee::where('entity', $entity->code)->where('status', 'Resign')->count();

        $syncStatus = empty($errors) ? 'success' : ($updated > 0 || $resigned > 0 ? 'partial' : 'failed');
        $syncMessage = "Midnight check selesai [{$entity->code}]. Diperbarui: {$updated} | Resign: {$resigned} | Total Aktif Sekarang: {$totalActiveNow}";

        // Update entity
        $entity->update([
            'last_sync_at'      => now(),
            'last_sync_status'  => $syncStatus,
            'last_sync_message' => $syncMessage,
            'sync_counts'       => [
                'created'  => 0,
                'updated'  => $updated,
                'resigned' => $resigned,
                'total'    => $totalActiveNow,
                'errors'   => count($errors),
            ],
        ]);

        // Log to OdooSyncLog
        OdooSyncLog::create([
            'batch_id'             => $batchId,
            'entity_code'          => $entity->code,
            'sync_type'            => 'employee_midnight_updates_resigns',
            'trigger_type'         => 'cron_midnight',
            'status'               => $syncStatus,
            'new_count'            => 0,
            'update_count'         => $updated,
            'resign_count'         => $resigned,
            'total_employee_count' => $totalActiveNow,
            'details'              => [
                'entity_name'     => $entity->name,
                'batch_id'        => $batchId,
                'updated_count'   => $updated,
                'resigned_count'  => $resigned,
                'total_active'    => $totalActiveNow,
                'total_resigned'  => $totalResignNow,
                'errors'          => array_slice($errors, 0, 10),
            ],
            'error_message'        => !empty($errors) ? implode('; ', array_slice($errors, 0, 3)) : null,
        ]);

        $log('summary', $syncMessage);

        return [
            'success'   => ($syncStatus !== 'failed'),
            'status'    => $syncStatus,
            'batch_id'  => $batchId,
            'created'   => 0,
            'updated'   => $updated,
            'resigned'  => $resigned,
            'total'     => $totalActiveNow,
            'message'   => $syncMessage,
            'errors'    => $errors,
        ];
    }

    /**
     * Sync a single employee from Odoo by NIK / identification_id.
     * Useful for verifying or checking 1 employee instantly without bulk syncing.
     */
    public function syncSingleEmployee(OdooEntity $entity, string $nik): array
    {
        $cleanNik = trim($nik);
        if (empty($cleanNik)) {
            throw new \Exception('NIK tidak boleh kosong.');
        }

        $uid = $this->authenticate();

        $domain = [
            '|',
            ['identification_id', '=', $cleanNik],
            ['registration_number', '=', $cleanNik],
        ];

        $fields = [
            'id', 'name', 'registration_number', 'identification_id',
            'mobile_phone', 'work_email', 'private_email',
            'department_id', 'job_id', 'principle_id', 'area_id',
            'first_contract_date', 'active', 'departure_date',
        ];

        $records = $this->xmlRpcCall('/xmlrpc/2/object', 'execute_kw', [
            $this->db, $uid, $this->apiKey,
            'hr.employee', 'search_read',
            [$domain],
            [
                'fields' => $fields,
                'context' => ['active_test' => false],
                'limit'   => 5,
            ],
        ]);

        // Fallback ilike search if exact match returned empty
        if (empty($records)) {
            $domainFallback = [
                '|',
                ['identification_id', 'ilike', $cleanNik],
                ['registration_number', 'ilike', $cleanNik],
            ];
            $records = $this->xmlRpcCall('/xmlrpc/2/object', 'execute_kw', [
                $this->db, $uid, $this->apiKey,
                'hr.employee', 'search_read',
                [$domainFallback],
                [
                    'fields' => $fields,
                    'context' => ['active_test' => false],
                    'limit'   => 5,
                ],
            ]);
        }

        if (empty($records)) {
            return [
                'success' => false,
                'message' => "Karyawan dengan NIK / NIP '{$cleanNik}' tidak ditemukan di server Odoo [{$entity->code}] {$entity->name} (Database: {$entity->odoo_db}).",
                'data'    => null,
            ];
        }

        $rec = $records[0];
        $odooId = $rec['id'];
        $rawNik = trim((string)($rec['identification_id'] ?: $rec['registration_number'] ?: ''));
        $finalNik = $rawNik ?: $cleanNik;
        $nip = trim((string)($rec['registration_number'] ?: '')) ?: null;
        $nama = trim((string)($rec['name'] ?? 'Tanpa Nama'));
        $email = $rec['work_email'] ?: ($rec['private_email'] ?: null);
        $telepon = $rec['mobile_phone'] ?: null;
        $tanggalJoin = !empty($rec['first_contract_date']) ? $rec['first_contract_date'] : null;

        $jabatan = is_array($rec['job_id']) ? $rec['job_id'][1] : null;
        $divisi = is_array($rec['department_id']) ? $rec['department_id'][1] : null;
        $area = is_array($rec['area_id']) ? $rec['area_id'][1] : null;

        $principleName = is_array($rec['principle_id']) ? $rec['principle_id'][1] : null;

        // Filter: Abaikan karyawan dengan prinsiple PT BUDGET (AMK, AKP, ATK)
        if (!empty($principleName) && stripos($principleName, 'BUDGET') !== false) {
            return null;
        }

        $principleId = null;
        if (!empty($principleName)) {
            $p = Principle::firstOrCreate(['name' => $principleName]);
            $principleId = $p->id;
        }

        $isActive = (bool)($rec['active'] ?? true);
        $status = ($isActive && empty($rec['departure_date'])) ? 'Aktiv' : 'Resign';
        $tipeKaryawan = Employee::determineTipeKaryawan($principleName);

        // Search existing employee in database
        $employee = Employee::where('odoo_id', $odooId)->where('entity', $entity->code)->first()
                 ?: Employee::where('nik', $finalNik)->first()
                 ?: (!empty($nip) ? Employee::where('nip', $nip)->first() : null);

        $isNew = false;
        $dataToSave = [
            'nik'           => $finalNik,
            'nip'           => $nip,
            'nama_karyawan' => $nama,
            'email'         => $email,
            'telepon'       => $telepon,
            'tanggal_join'  => $tanggalJoin,
            'jabatan'       => $jabatan ?: 'Staff',
            'divisi'        => $divisi,
            'principle_id'  => $principleId,
            'prinsiple'     => $principleName,
            'tipe_karyawan' => $tipeKaryawan,
            'area'          => $area ?: 'Pusat',
            'status'        => $status,
            'entity'        => $entity->code,
            'odoo_id'       => $odooId,
            'last_sync_at'  => now(),
        ];

        if ($employee) {
            $employee->update($dataToSave);
        } else {
            $employee = Employee::create($dataToSave);
            $isNew = true;
        }

        // Log record
        OdooSyncLog::create([
            'batch_id'             => 'SYNC-NIK-' . $entity->code . '-' . date('Ymd-His'),
            'entity_code'          => $entity->code,
            'sync_type'            => 'employee_single_nik',
            'trigger_type'         => 'manual_nik',
            'status'               => 'success',
            'new_count'            => $isNew ? 1 : 0,
            'update_count'         => $isNew ? 0 : 1,
            'resign_count'         => ($status === 'Resign') ? 1 : 0,
            'total_employee_count' => Employee::where('entity', $entity->code)->where('status', 'Aktiv')->count(),
            'details'              => [
                'nik'           => $finalNik,
                'nama_karyawan' => $nama,
                'odoo_id'       => $odooId,
                'status'        => $status,
                'tipe_karyawan' => $tipeKaryawan,
                'prinsiple'     => $principleName,
            ],
        ]);

        return [
            'success'   => true,
            'action'    => $isNew ? 'created' : 'updated',
            'status'    => $status,
            'is_active' => ($status === 'Aktiv'),
            'message'   => ($isNew ? 'Berhasil menambahkan' : 'Berhasil memperbarui') . " data karyawan [{$nama}] (NIK: {$finalNik}) dari Odoo {$entity->code} ({$tipeKaryawan}).",
            'employee'  => $employee,
            'data'      => $employee->toArray(),
            'odoo_raw'  => [
                'id'            => $odooId,
                'nama'          => $nama,
                'nik'           => $finalNik,
                'nip'           => $nip,
                'jabatan'       => $jabatan,
                'divisi'        => $divisi,
                'prinsiple'     => $principleName,
                'tipe_karyawan' => $tipeKaryawan,
                'area'          => $area,
                'status'        => $status,
                'is_active'     => $isActive,
                'email'         => $email,
                'telepon'       => $telepon,
                'tanggal_join'  => $tanggalJoin,
            ],
        ];
    }

    /**
     * Clean up duplicate employees in database based on NIK.
     */
    public static function cleanupDuplicateEmployees(?callable $progressCallback = null): int
    {
        $log = function(string $type, string $message) use ($progressCallback) {
            if ($progressCallback && is_callable($progressCallback)) {
                call_user_func($progressCallback, $type, $message);
            }
        };

        $log('info', 'Memindai database untuk mendeteksi data NIK ganda...');

        $duplicateNiks = Employee::select('nik')
            ->whereNotNull('nik')
            ->where('nik', '!=', '')
            ->where('nik', 'not like', 'OD-%')
            ->groupBy('nik')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('nik');

        $countGroups = $duplicateNiks->count();
        if ($countGroups === 0) {
            $log('success', 'Database bersih! Tidak ada NIK ganda yang terdeteksi.');
            return 0;
        }

        $log('warning', "Ditemukan {$countGroups} kelompok NIK ganda. Memulai pembersihan...");

        $totalCleaned = 0;

        foreach ($duplicateNiks as $nik) {
            $records = Employee::where('nik', $nik)->orderBy('id')->get();
            if ($records->count() <= 1) {
                continue;
            }

            // Pick primary record:
            // 1. Has photo or active status
            // 2. Has odoo_id
            // 3. Highest ID (most recent)
            $primary = $records->firstWhere('status', 'Aktiv')
                ?: ($records->first(fn($e) => !empty($e->foto))
                ?: ($records->firstWhere('odoo_id', '!=', null) ?: $records->last()));

            $duplicatesToDelete = $records->where('id', '!=', $primary->id);
            foreach ($duplicatesToDelete as $dup) {
                // Merge photo if primary is missing
                if (empty($primary->foto) && !empty($dup->foto)) {
                    $primary->foto = $dup->foto;
                    $primary->save();
                }
                $dup->delete();
                $totalCleaned++;
            }
        }

        $log('success', "Pembersihan selesai! {$totalCleaned} data duplikat berhasil dirapikan.");
        return $totalCleaned;
    }

    /**
     * Low-level cURL XML-RPC call.
     */
    public function xmlRpcCall(string $path, string $method, array $params): mixed
    {
        $endpoint = $this->url . $path;

        // Build XML-RPC request
        $xmlParams = array_map([$this, 'phpToXmlRpc'], $params);
        $xmlBody   = '<?xml version="1.0"?><methodCall><methodName>' . htmlspecialchars($method) . '</methodName><params>';
        foreach ($xmlParams as $param) {
            $xmlBody .= '<param><value>' . $param . '</value></param>';
        }
        $xmlBody .= '</params></methodCall>';

        $ch = curl_init($endpoint);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $xmlBody,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['Content-Type: text/xml', 'Content-Length: ' . strlen($xmlBody)],
            CURLOPT_TIMEOUT        => 120,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $rawResponse = curl_exec($ch);
        $error       = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new \Exception('cURL Error: ' . $error);
        }

        return $this->parseXmlRpcResponse($rawResponse);
    }

    private function phpToXmlRpc(mixed $value): string
    {
        if (is_bool($value)) {
            return '<boolean>' . ($value ? '1' : '0') . '</boolean>';
        }
        if (is_int($value)) {
            return '<int>' . $value . '</int>';
        }
        if (is_float($value)) {
            return '<double>' . $value . '</double>';
        }
        if (is_null($value) || $value === false) {
            return '<boolean>0</boolean>';
        }
        if (is_string($value)) {
            return '<string>' . htmlspecialchars($value) . '</string>';
        }
        if (is_array($value)) {
            if (array_values($value) !== $value) {
                // Struct (Associative)
                $members = '';
                foreach ($value as $k => $v) {
                    $members .= '<member><name>' . htmlspecialchars((string)$k) . '</name><value>' . $this->phpToXmlRpc($v) . '</value></member>';
                }
                return '<struct>' . $members . '</struct>';
            }
            // Array (Indexed)
            $data = '';
            foreach ($value as $v) {
                $data .= '<value>' . $this->phpToXmlRpc($v) . '</value>';
            }
            return '<array><data>' . $data . '</data></array>';
        }
        return '<string>' . htmlspecialchars((string)$value) . '</string>';
    }

    private function parseXmlRpcResponse(string $rawResponse): mixed
    {
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($rawResponse);

        if ($xml === false) {
            throw new \Exception('Gagal mengurai respons XML-RPC dari server Odoo.');
        }

        // Check for fault
        if (isset($xml->fault)) {
            $fault = $this->xmlRpcToPHP($xml->fault->value);
            throw new \Exception('Odoo XML-RPC Fault: ' . ($fault['faultString'] ?? 'Unknown fault'));
        }

        if (isset($xml->params->param->value)) {
            return $this->xmlRpcToPHP($xml->params->param->value);
        }

        return null;
    }

    private function xmlRpcToPHP(\SimpleXMLElement $value): mixed
    {
        if (isset($value->array)) {
            $result = [];
            if (isset($value->array->data->value)) {
                foreach ($value->array->data->value as $item) {
                    $result[] = $this->xmlRpcToPHP($item);
                }
            }
            return $result;
        }

        if (isset($value->struct)) {
            $result = [];
            if (isset($value->struct->member)) {
                foreach ($value->struct->member as $member) {
                    $name = (string)$member->name;
                    $result[$name] = $this->xmlRpcToPHP($member->value);
                }
            }
            return $result;
        }

        if (isset($value->string)) {
            return (string)$value->string;
        }

        if (isset($value->int)) {
            return (int)$value->int;
        }

        if (isset($value->i4)) {
            return (int)$value->i4;
        }

        if (isset($value->boolean)) {
            return ((string)$value->boolean === '1');
        }

        if (isset($value->double)) {
            return (float)$value->double;
        }

        return (string)$value;
    }
}
