<?php

namespace App\Console\Commands;

use App\Models\Candidate;
use App\Models\Principle;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportLegacyInterviewCommand extends Command
{
    protected $signature = 'import:legacy-interview 
                            {--host=localhost : MySQL Host}
                            {--db=u1638368_interview : MySQL Database Name}
                            {--user=root : MySQL Username}
                            {--password= : MySQL Password}
                            {--limit=100 : Jumlah record yang diimport}';

    protected $description = 'Import dan sanitasi data kandidat & interview dari database MySQL lama ke PostgreSQL baru';

    public function handle()
    {
        $this->info("=== MEMULAI IMPORT DATA LEGACY INTERVIEW ===");

        $host = $this->option('host');
        $dbName = $this->option('db');
        $user = $this->option('user');
        $pass = $this->option('password');
        $limit = intval($this->option('limit'));

        $this->line("Menghubungkan ke MySQL {$host} / {$dbName}...");

        try {
            $pdo = new \PDO("mysql:host={$host};dbname={$dbName};charset=utf8mb4", $user, $pass, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            ]);
        } catch (\Exception $e) {
            $this->error("Gagal terhubung ke MySQL: " . $e->getMessage());
            return 1;
        }

        $this->info("Koneksi MySQL berhasil!");

        // Ambil data kandidat lama
        $stmt = $pdo->prepare("SELECT * FROM tb_kandidat WHERE no_ktp != '' ORDER BY id DESC LIMIT :lim");
        $stmt->bindValue(':lim', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $candidates = $stmt->fetchAll();

        $this->info("Ditemukan " . count($candidates) . " kandidat dari MySQL. Memproses import ke PostgreSQL...");

        $imported = 0;
        $skipped = 0;

        foreach ($candidates as $row) {
            $nik = trim($row['no_ktp']);
            if (strlen($nik) < 16) {
                $nik = str_pad($nik, 16, '0', STR_PAD_LEFT);
            }

            // Normalisasi tanggal lahir
            $birthDate = null;
            if (!empty($row['tanggal_lahir']) && $row['tanggal_lahir'] !== '0000-00-00') {
                $birthDate = $row['tanggal_lahir'];
            }

            // Cari atau buat principle
            $principleId = null;
            if (!empty($row['principle'])) {
                $pName = trim(str_replace('PT ', '', $row['principle']));
                $principle = Principle::firstOrCreate(['name' => $row['principle']], [
                    'parent_company' => $pName,
                ]);
                $principleId = $principle->id;
            }

            // Pemetaan status lama ke status baru
            $status = 'new';
            if ($row['status'] === 'Active') {
                if (!empty($row['status_approve']) && $row['status_approve'] === 'Yes') {
                    $status = 'passed';
                } elseif (!empty($row['idprinsiple'])) {
                    $status = 'review_principle';
                } else {
                    $status = 'interview_process';
                }
            } elseif ($row['status'] === 'non' || $row['status'] === 'Arsip') {
                $status = 'archived';
            }

            try {
                $cand = Candidate::updateOrCreate(
                    ['nik' => $nik],
                    [
                        'full_name' => trim($row['applicants_name'] ?? 'Kandidat'),
                        'birth_date' => $birthDate,
                        'birth_place' => $row['kota_lahir'] ?? null,
                        'gender' => $row['jeniskelamin'] ?? null,
                        'religion' => $row['religion'] ?? null,
                        'education' => $row['pendidikan_terakhir'] ?? null,
                        'phone' => $row['mobile'] ?? $row['phone'] ?? null,
                        'whatsapp' => $row['mobile'] ?? null,
                        'email' => $row['email'] ?? null,
                        'address_ktp' => $row['alamat_ktp'] ?? null,
                        'address_domicile' => $row['alamat_domisili'] ?? null,
                        'marital_status' => $row['status_kawin'] ?? null,
                        'expected_salary' => floatval($row['gaji_diminta'] ?? 0),
                        'last_salary' => floatval($row['gaji_terakhir'] ?? 0),
                        'work_motivation' => $row['motivasi_kerja'] ?? null,
                        'strengths' => $row['kelebihan'] ?? null,
                        'weaknesses' => $row['kekurangan'] ?? null,
                        'status' => $status,
                        'source_type' => ($row['jenis'] === 'Job Portal') ? 'job_portal' : 'walk_in',
                        'applied_job' => $row['applied_job'] ?? 'Kandidat',
                        'area' => $row['area'] ?? 'Nasional',
                        'principle_id' => $principleId,
                        'ai_score' => floatval($row['ai_score'] ?? 0),
                        'photo_path' => $row['fotoprofil'] ?? null,
                        'cv_path' => $row['filecv'] ?? null,
                    ]
                );

                $imported++;
            } catch (\Exception $e) {
                $skipped++;
            }
        }

        $this->info("Import selesai!");
        $this->line("• Berhasil diimport ke PostgreSQL: {$imported} kandidat");
        $this->line("• Dilewati / gagal: {$skipped} kandidat");

        return 0;
    }
}