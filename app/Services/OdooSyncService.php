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
     * Sync Employees from Odoo for a specific entity.
     * Supports category filtering: 'all', 'inhouse', 'ratecard'.
     * Only synchronizes active employees.
     */
    public function syncEmployees(OdooEntity $entity, ?callable $progressCallback = null, string $category = 'all'): array
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
        $errors    = [];
        $offset    = 0;
        $limit     = 250;
        $batchNum  = 0;

        $log('info', "Memulai sinkronisasi karyawan AKTIF untuk entitas [{$entity->code}] {$entity->name} (Filter: {$categoryLabel})...");

        do {
            $batchNum++;
            $log('batch', "Mengambil batch #{$batchNum} (Offset: {$offset}, Limit: {$limit})...");

            try {
                // Hanya ambil employee aktif dari Odoo
                $records = $this->xmlRpcCall('/xmlrpc/2/object', 'execute_kw', [
                    $this->db, $uid, $this->apiKey,
                    'hr.employee', 'search_read',
                    [[['active', '=', true]]],
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
                $errors[] = "Gagal mengambil batch #{$batchNum}: " . $e->getMessage();
                $log('error', "Gagal pada batch #{$batchNum}: " . $e->getMessage());
                break;
            }

            $recCount = is_array($records) ? count($records) : 0;
            if ($recCount === 0) {
                break;
            }

            $log('info', "Diterima {$recCount} data karyawan dari Odoo. Memfilter aktif & kategori...");

            foreach ($records as $rec) {
                try {
                    // Filter 1: Hanya Employee Aktif Saja
                    $isActive = (bool)($rec['active'] ?? true);
                    if (!$isActive || !empty($rec['departure_date'])) {
                        continue; // Lewati karyawan resign/non-aktif
                    }

                    // Principle
                    $principleName = is_array($rec['principle_id']) ? $rec['principle_id'][1] : null;
                    $principleId = null;
                    if (!empty($principleName)) {
                        $p = Principle::firstOrCreate(['name' => $principleName]);
                        $principleId = $p->id;
                    }

                    // Filter 2: Inhouse vs RateCard
                    $tipeKaryawan = Employee::determineTipeKaryawan($principleName);
                    if ($category === 'inhouse' && $tipeKaryawan !== 'Inhouse') {
                        continue; // Lewati karena bukan inhouse
                    }
                    if ($category === 'ratecard' && $tipeKaryawan !== 'RateCard') {
                        continue; // Lewati karena inhouse
                    }

                    $odooId = $rec['id'];
                    $rawNik = trim((string)($rec['identification_id'] ?: $rec['registration_number'] ?: ''));
                    $nik = $rawNik ?: ('OD-' . $odooId);
                    $nip = trim((string)($rec['registration_number'] ?: '')) ?: null;
                    $nama = trim((string)($rec['name'] ?? 'Tanpa Nama'));
                    $email = $rec['work_email'] ?: ($rec['private_email'] ?: null);
                    $telepon = $rec['mobile_phone'] ?: null;
                    $tanggalJoin = !empty($rec['first_contract_date']) ? $rec['first_contract_date'] : null;

                    $jabatan = is_array($rec['job_id']) ? $rec['job_id'][1] : null;
                    $divisi = is_array($rec['department_id']) ? $rec['department_id'][1] : null;
                    $area = is_array($rec['area_id']) ? $rec['area_id'][1] : null;

                    $status = 'Aktiv';

                    // 1. Search by odoo_id & entity first
                    $employee = Employee::where('odoo_id', $odooId)->where('entity', $entity->code)->first();

                    // 2. Search by NIK if not found
                    if (!$employee && !empty($rawNik)) {
                        $employee = Employee::where('nik', $rawNik)->first();
                    }

                    // 3. Search by NIP if not found
                    if (!$employee && !empty($nip)) {
                        $employee = Employee::where('nip', $nip)->first();
                    }

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

                    if ($employee) {
                        $employee->update($dataToSave);
                        $updated++;
                    } else {
                        Employee::create($dataToSave);
                        $created++;
                    }

                } catch (\Throwable $e) {
                    $errors[] = 'Error [ID Odoo: ' . ($rec['id'] ?? '?') . ']: ' . $e->getMessage();
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
    private function xmlRpcCall(string $path, string $method, array $params): mixed
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
