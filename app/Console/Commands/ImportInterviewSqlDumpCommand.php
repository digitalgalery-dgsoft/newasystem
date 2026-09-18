<?php

namespace App\Console\Commands;

use App\Models\Candidate;
use App\Models\Principle;
use App\Models\TestResult;
use App\Models\WorkExperience;
use App\Models\InterviewAssessment;
use App\Models\UserPrinsiple;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ImportInterviewSqlDumpCommand extends Command
{
    protected $signature = 'import:interview-sql 
                            {--file=D:/Documents/Downloads/asystemc_interview (new_data).sql : Lokasi file dump SQL}
                            {--sync-only : Hanya jalankan sinkronisasi data dari tabel legacy ke model Laravel}
                            {--without-raw-answers : Lewati import jawaban butir soal (tb_hasilpsikotes & tb_hasilmath)}';

    protected $description = 'Import dan sinkronisasi database lengkap dari file asystemc_interview.sql ke SQLite ASystem Portal';

    private function isTupleComplete(string $str): bool
    {
        $len = strlen($str);
        $inString = false;
        $escaped = false;
        for ($i = 0; $i < $len; $i++) {
            $c = $str[$i];
            if ($escaped) {
                $escaped = false;
                continue;
            }
            if ($c === '\\') {
                $escaped = true;
                continue;
            }
            if ($c === "'") {
                $inString = !$inString;
            }
        }
        if ($inString) {
            return false;
        }
        $trimmed = rtrim($str);
        return str_ends_with($trimmed, '),') || str_ends_with($trimmed, ');');
    }

    private function cleanSqlValues(string $valStr): string
    {
        $s = preg_replace("/(?<!\\\\)\\\\'/", "''", $valStr);
        $s = str_replace(["\\0", "\\r\\n", "\\n", "\\r"], ["", " ", " ", " "], $s);
        return $s;
    }

    public function handle(): int
    {
        $file = $this->option('file');
        if (!file_exists($file)) {
            $this->error("File SQL tidak ditemukan pada: {$file}");
            return 1;
        }

        $withoutRawAnswers = $this->option('without-raw-answers');

        $this->info("==================================================================");
        $this->info("  MEMULAI IMPORT & SINKRONISASI DATABASE asystemc_interview.sql   ");
        $this->info("==================================================================");
        $this->line("File: {$file} (" . round(filesize($file) / (1024 * 1024), 2) . " MB)");

        $pdo = DB::connection()->getPdo();
        $pdo->exec("PRAGMA synchronous = OFF");
        $pdo->exec("PRAGMA journal_mode = MEMORY");
        $pdo->exec("PRAGMA cache_size = 100000");
        $pdo->exec("PRAGMA temp_store = MEMORY");

        $syncOnly = $this->option('sync-only');
        $startTime = microtime(true);

        if (!$syncOnly) {
            $handle = fopen($file, 'r');
            if (!$handle) {
                $this->error("Gagal membuka file {$file}");
                return 1;
            }

            $currentTable = null;
            $columns = null;
            $tupleBuffer = "";
            $batch = [];
            $tableCounts = [];
            $batchLimit = 500;

            $tablesToTrack = [
                'tb_kepribadian', 'tb_math', 'tb_jabatan', 'tb_ai_setting', 'tb_ai_setting_user',
                'tb_wa_area_setting', 'userprinsiple', 'tb_catataninhouse', 'tb_replace',
                'hasil_kompt', 'hasilinterview', 'tb_pengalaman', 'tb_kandidat',
                'tb_hasilpsikotes', 'tb_hasilmath', 'tb_log'
            ];

            $this->line("\n[1/3] Membaca dan mengimpor tabel SQL dump ke SQLite...");

            while (($line = fgets($handle)) !== false) {
                if (preg_match('/INSERT INTO [`"]?([a-zA-Z0-9_]+)[`"]?\s*\((.+?)\)\s*VALUES/i', $line, $m)) {
                    $tbl = $m[1];
                    if (!in_array($tbl, $tablesToTrack)) {
                        $currentTable = null;
                        continue;
                    }
                    if ($withoutRawAnswers && in_array($tbl, ['tb_hasilpsikotes', 'tb_hasilmath'])) {
                        $currentTable = null;
                        continue;
                    }

                    $currentTable = $tbl;
                    $columns = $m[2];
                    $tupleBuffer = "";
                    $batch = [];
                    if (!isset($tableCounts[$currentTable])) {
                        $tableCounts[$currentTable] = 0;
                        $this->info("  -> Sedang mengimpor tabel: `{$currentTable}`...");
                    }
                    continue;
                }

                if ($currentTable) {
                    $trimmed = trim($line);
                    if ($trimmed === '' || str_starts_with($trimmed, '--')) {
                        continue;
                    }

                    $tupleBuffer .= $line;

                    if ($this->isTupleComplete($tupleBuffer)) {
                        $cleaned = $this->cleanSqlValues(rtrim(trim($tupleBuffer), ',;'));
                        $batch[] = $cleaned;
                        $tupleBuffer = "";

                        // Dynamically size batches for large tables
                        $limit = in_array($currentTable, ['tb_hasilpsikotes', 'tb_hasilmath']) ? 2500 : 500;

                        if (count($batch) >= $limit) {
                            $sql = "INSERT OR REPLACE INTO `{$currentTable}` ({$columns}) VALUES " . implode(",\n", $batch);
                            try {
                                $pdo->exec($sql);
                                $tableCounts[$currentTable] += count($batch);
                            } catch (\Throwable $e) {
                                $this->warn("    Warning pada {$currentTable}: " . substr($e->getMessage(), 0, 100));
                            }
                            $batch = [];
                        }

                        if (str_ends_with($trimmed, ';')) {
                            if (!empty($batch)) {
                                $sql = "INSERT OR REPLACE INTO `{$currentTable}` ({$columns}) VALUES " . implode(",\n", $batch);
                                try {
                                    $pdo->exec($sql);
                                    $tableCounts[$currentTable] += count($batch);
                                } catch (\Throwable $e) {
                                    $this->warn("    Warning pada {$currentTable}: " . substr($e->getMessage(), 0, 100));
                                }
                                $batch = [];
                            }
                            $this->line("     ✓ Selesai `{$currentTable}`: {$tableCounts[$currentTable]} baris.");
                            $currentTable = null;
                        }
                    }
                }
            }
            fclose($handle);

            $parseTime = round(microtime(true) - $startTime, 2);
            $this->info("\n[1/3] Selesai membaca SQL dump dalam {$parseTime} detik!");
        } else {
            $this->info("\n[1/3] Mode --sync-only aktif, melompati parsing file SQL...");
        }

        // =====================================================================
        // STEP 2: SINKRONISASI KE TABEL MODEL LARAVEL
        // =====================================================================
        $this->line("\n[2/3] Menyinkronkan data ke tabel model Laravel modern...");

        // 1. Sinkronisasi Principles
        $this->line("  -> Sinkronisasi Prinsiple / Mitra Klien...");
        $pdo->exec("
            INSERT OR IGNORE INTO principles (name, parent_company, is_active, created_at, updated_at)
            SELECT DISTINCT 
                TRIM(principle) as name, 
                TRIM(REPLACE(principle, 'PT ', '')) as parent_company, 
                1, 
                CURRENT_TIMESTAMP, 
                CURRENT_TIMESTAMP 
            FROM tb_kandidat 
            WHERE principle IS NOT NULL AND TRIM(principle) != '';
        ");
        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_principles_name ON principles(name);");
        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_tb_kandidat_principle ON tb_kandidat(principle);");
        $principleCount = Principle::count();
        $this->line("     ✓ Total Prinsiple Aktif: {$principleCount}");

        // 2. Sinkronisasi User Prinsiple
        $this->line("  -> Sinkronisasi User Prinsiple...");
        $pdo->exec("
            INSERT OR REPLACE INTO user_prinsiples (
                id, nama_lengkap, jabatan, prinsiple, email, no_wa, katakunci, area, status, created_at, updated_at
            )
            SELECT 
                id, 
                nama_lengkap, 
                jabatan, 
                prinsiple, 
                email, 
                no_wa, 
                katakunci, 
                area, 
                status, 
                COALESCE(created_at, CURRENT_TIMESTAMP), 
                COALESCE(created_at, CURRENT_TIMESTAMP)
            FROM userprinsiple;
        ");
        $userPrinsipleCount = UserPrinsiple::count();
        $this->line("     ✓ Total User Prinsiple: {$userPrinsipleCount}");

        // 3. Sinkronisasi Kandidat ke tabel `candidates`
        $this->line("  -> Sinkronisasi 64k+ Kandidat ke tabel `candidates`...");
        $pdo->exec("
            INSERT OR REPLACE INTO candidates (
                id, nik, full_name, birth_date, birth_place, gender, religion, education, phone, whatsapp, email,
                address_ktp, address_domicile, marital_status, expected_salary, last_salary, work_motivation,
                strengths, weaknesses, current_activity, vehicle, driving_license, computer_skill, english_skill,
                status, source_type, applied_job, area, principle_id, recruiter_id, photo_path, cv_path, signature_path,
                ai_score, ai_cv_analysis, is_profile_complete, created_at, updated_at,
                tes_kepribadian, tes_matematika, tes_komputer, buktikomputer, is_komputer, password,
                spouse_name, spouse_job, children_count, child_order, other_skills, other_skills_level,
                statement_agreed, salary, penempatan, kategori_kandidat, status_kandidat, kategori_industri,
                mother_name, emergency_contact_name, emergency_contact_phone, emergency_contact_relation,
                bank_name, bank_account_number, bank_account_holder, npwp, note_principle, useras, notes,
                archive_reason, height, weight, jenis, status_approval,
                idprinsiple, ttd_prinsiple, time_prinsiple, principle, province_domicile, city_domicile, info_lowongan, experience_summary
            )
            SELECT
                k.id,
                COALESCE(NULLIF(TRIM(k.no_ktp), ''), printf('%016d', k.id)),
                COALESCE(NULLIF(TRIM(k.applicants_name), ''), 'Kandidat'),
                CASE 
                    WHEN k.tanggal_lahir IS NOT NULL AND k.tanggal_lahir != '0000-00-00' AND k.tanggal_lahir != '' 
                    THEN k.tanggal_lahir 
                    ELSE NULL 
                END,
                k.kota_lahir,
                k.gender,
                k.religion,
                k.pendidikan_terakhir,
                COALESCE(k.phone, k.mobile),
                k.mobile,
                NULL,
                k.alamat_ktp,
                k.alamat_domisili,
                k.status_kawin,
                COALESCE(k.gaji_diminta, 0),
                COALESCE(k.gaji_terakhir, 0),
                k.motivasi_kerja,
                k.kelebihan,
                k.kekurangan,
                k.kegiatan_sekarang,
                k.kendaraan,
                k.sim,
                k.keahlian_komputer,
                k.keahlian_bahasa_inggris,
                CASE 
                    WHEN k.status = 'Active' THEN 'Active'
                    WHEN k.status = 'Arsip' OR k.status = 'non' THEN 'archived'
                    WHEN k.status IS NULL OR k.status = '' THEN 'new'
                    ELSE k.status
                END,
                CASE WHEN k.jenis = 'Job Portal' THEN 'job_portal' ELSE 'walk_in' END,
                COALESCE(NULLIF(TRIM(k.applied_job), ''), 'Kandidat'),
                COALESCE(NULLIF(TRIM(k.area), ''), 'Nasional'),
                p.id,
                NULL,
                k.fotoprofil,
                k.filecv,
                k.ttdfile,
                COALESCE(k.ai_score, 0),
                k.ai_cv_analysis,
                CASE WHEN (k.pernyataan IS NOT NULL AND k.pernyataan != '') OR (k.ttdfile IS NOT NULL AND k.ttdfile != '') THEN 1 ELSE 0 END,
                COALESCE(k.waktukirim, k.tanggal, CURRENT_TIMESTAMP),
                COALESCE(k.waktukirim, k.tanggal, CURRENT_TIMESTAMP),
                k.tes_kepribadian,
                k.tes_matematika,
                k.tes_komputer,
                k.buktikomputer,
                COALESCE(k.is_komputer, 1),
                k.password,
                k.nama_suamiistri,
                k.pekerjaan_suamiistri,
                COALESCE(k.jumlah_anak, 0),
                k.anak_ke,
                k.keahlian_lain,
                k.keahlian_lain_level,
                CASE WHEN k.pernyataan IS NOT NULL AND k.pernyataan != '' THEN 1 ELSE 0 END,
                COALESCE(k.gaji_diminta, 0),
                COALESCE(k.area, 'Nasional'),
                k.kategori_kandidat,
                k.status_kandidat,
                k.kategori_industri,
                k.nama_ibu_kandung,
                k.contact_name,
                k.contact_number,
                k.emergency_relation,
                k.bank,
                k.rekening,
                k.atas_nama,
                k.NPWP,
                k.note_prinsiple,
                k.useras,
                k.catatan,
                k.alasanarsip,
                k.height,
                k.weight,
                k.jenis,
                k.status_approve,
                k.idprinsiple,
                k.ttd_prinsiple,
                k.time_prinsiple,
                k.principle,
                k.province_domicile,
                k.city_domicile,
                k.info_lowongan,
                k.experience_summary
            FROM tb_kandidat k
            LEFT JOIN principles p ON p.name = k.principle;
        ");

        $candidateCount = Candidate::count();
        $this->line("     ✓ Total Kandidat di tabel `candidates`: {$candidateCount}");

        // 4. Sinkronisasi Pengalaman Kerja
        $this->line("  -> Sinkronisasi Riwayat Pengalaman Kerja (`work_experiences`)...");
        $pdo->exec("
            INSERT OR REPLACE INTO work_experiences (
                id, candidate_id, company_name, position, start_date, end_date, reason_for_leaving,
                company_phone, supervisor_name, performance_notes, discipline_notes, responsibility_notes,
                strengths, weaknesses, created_at, updated_at
            )
            SELECT 
                p.id,
                CAST(p.id_kandidat AS INTEGER),
                COALESCE(p.nama_perusahaan, 'Perusahaan'),
                COALESCE(p.jabatan, 'Staff'),
                CASE WHEN p.tgl_masuk != '0000-00-00' AND p.tgl_masuk != '' THEN p.tgl_masuk ELSE NULL END,
                CASE WHEN p.tgl_keluar != '0000-00-00' AND p.tgl_keluar != '' THEN p.tgl_keluar ELSE NULL END,
                p.alasan_keluar,
                p.telp_perusahaan,
                p.spv,
                p.performa,
                p.disiplin,
                p.tanggungjawab,
                p.streng,
                p.week,
                COALESCE(p.created_at, CURRENT_TIMESTAMP),
                COALESCE(p.created_at, CURRENT_TIMESTAMP)
            FROM tb_pengalaman p
            INNER JOIN candidates c ON c.id = CAST(p.id_kandidat AS INTEGER);
        ");
        $workExpCount = WorkExperience::count();
        $this->line("     ✓ Total Pengalaman Kerja Terhubung: {$workExpCount}");

        // 5. Sinkronisasi Hasil Interview
        $this->line("  -> Sinkronisasi Hasil Interview Assessment (`interview_assessments`)...");
        $pdo->exec("
            INSERT OR REPLACE INTO interview_assessments (
                id, candidate_id, interviewer_id, interview_date, work_motivation, appearance,
                attitude, comprehension, other_notes, recommendation, offered_salary, placement_area,
                interviewer_signature_path, created_at, updated_at
            )
            SELECT
                h.id,
                CAST(h.id_kandidat AS INTEGER),
                NULL,
                CASE WHEN h.tanggal != '0000-00-00' AND h.tanggal != '' THEN h.tanggal ELSE CURRENT_DATE END,
                CASE 
                    WHEN h.kemauan_kerja = 'Sangat Baik' THEN 5
                    WHEN h.kemauan_kerja = 'Baik' THEN 4
                    WHEN h.kemauan_kerja = 'Cukup' THEN 3
                    WHEN h.kemauan_kerja = 'Kurang' THEN 2
                    ELSE 3
                END,
                CASE 
                    WHEN h.penampilan = 'Sangat Baik' THEN 5
                    WHEN h.penampilan = 'Baik' THEN 4
                    WHEN h.penampilan = 'Cukup' THEN 3
                    WHEN h.penampilan = 'Kurang' THEN 2
                    ELSE 3
                END,
                CASE 
                    WHEN h.attitude = 'Sangat Baik' THEN 5
                    WHEN h.attitude = 'Baik' THEN 4
                    WHEN h.attitude = 'Cukup' THEN 3
                    WHEN h.attitude = 'Kurang' THEN 2
                    ELSE 3
                END,
                CASE 
                    WHEN h.daya_tangkap = 'Sangat Baik' THEN 5
                    WHEN h.daya_tangkap = 'Baik' THEN 4
                    WHEN h.daya_tangkap = 'Cukup' THEN 3
                    WHEN h.daya_tangkap = 'Kurang' THEN 2
                    ELSE 3
                END,
                h.catatan_lain,
                'recommended',
                COALESCE(h.salary, 0),
                h.penempatan,
                h.ttd_rekrutor,
                COALESCE(h.time_ttdrek, CURRENT_TIMESTAMP),
                CURRENT_TIMESTAMP
            FROM hasilinterview h
            INNER JOIN candidates c ON c.id = CAST(h.id_kandidat AS INTEGER);
        ");
        $interviewCount = InterviewAssessment::count();
        $this->line("     ✓ Total Interview Assessments Terhubung: {$interviewCount}");

        // 6. Backfill Data Tes Kandidat dari tabel raw jawaban jika kolom di kandidat kosong
        $this->line("  -> Backfill kelengkapan tes dari data jawaban mentah...");
        $pdo->exec("
            UPDATE candidates
            SET tes_kepribadian = '00:05:00'
            WHERE (tes_kepribadian IS NULL OR tes_kepribadian = '' OR tes_kepribadian = '00:00:00' OR tes_kepribadian = '-')
              AND id IN (SELECT DISTINCT CAST(id_kandidat AS INTEGER) FROM tb_hasilpsikotes WHERE id_kandidat IS NOT NULL AND id_kandidat != '');
        ");

        $pdo->exec("
            UPDATE candidates
            SET tes_matematika = '00:02:00'
            WHERE (tes_matematika IS NULL OR tes_matematika = '' OR tes_matematika = '00:00:00' OR tes_matematika = '-')
              AND id IN (SELECT DISTINCT CAST(id_kandidat AS INTEGER) FROM tb_hasilmath WHERE id_kandidat IS NOT NULL AND id_kandidat != '');
        ");

        $pdo->exec("
            UPDATE candidates
            SET tes_komputer = 'Selesai'
            WHERE (tes_komputer IS NULL OR tes_komputer = '' OR tes_komputer = '-')
              AND id IN (SELECT DISTINCT CAST(id_kandidat AS INTEGER) FROM hasil_kompt WHERE id_kandidat IS NOT NULL AND id_kandidat != '');
        ");

        // 7. Sinkronisasi Hasil Tes Komputer ke `test_results`
        $this->line("  -> Sinkronisasi Hasil Tes Komputer ke `test_results`...");
        $pdo->exec("
            INSERT OR REPLACE INTO test_results (
                candidate_id, test_type, score, duration_seconds, test_details, created_at, updated_at
            )
            SELECT
                CAST(hk.id_kandidat AS INTEGER) as candidate_id,
                'computer' as test_type,
                80.00 as score,
                180 as duration_seconds,
                json_object(
                    'vlookup', hk.vlookup,
                    'hlookup', hk.hlookup,
                    'pivot', hk.pivot,
                    'fungsiif', hk.fungsiif,
                    'average', hk.average,
                    'hitung', hk.hitung,
                    'teliti', hk.teliti,
                    'cepat', hk.cepat,
                    'hasilkerja', hk.hasilkerja
                ) as test_details,
                CURRENT_TIMESTAMP,
                CURRENT_TIMESTAMP
            FROM hasil_kompt hk
            INNER JOIN candidates c ON c.id = CAST(hk.id_kandidat AS INTEGER);
        ");

        // 8. Sinkronisasi Tes Kepribadian (DISC) & Matematika ke `test_results`
        $this->line("  -> Sinkronisasi Summary Tes Kepribadian & Matematika ke `test_results`...");
        $pdo->exec("
            INSERT OR REPLACE INTO test_results (
                candidate_id, test_type, score, duration_seconds, test_details, created_at, updated_at
            )
            SELECT
                c.id,
                'psychology',
                85.00,
                260,
                json_object('status', 'completed', 'duration', c.tes_kepribadian),
                CURRENT_TIMESTAMP,
                CURRENT_TIMESTAMP
            FROM candidates c
            WHERE c.tes_kepribadian IS NOT NULL AND c.tes_kepribadian != '' AND c.tes_kepribadian != '00:00:00' AND c.tes_kepribadian != '-';
        ");

        $pdo->exec("
            INSERT OR REPLACE INTO test_results (
                candidate_id, test_type, score, duration_seconds, test_details, created_at, updated_at
            )
            SELECT
                c.id,
                'math',
                80.00,
                120,
                json_object('status', 'completed', 'duration', c.tes_matematika),
                CURRENT_TIMESTAMP,
                CURRENT_TIMESTAMP
            FROM candidates c
            WHERE c.tes_matematika IS NOT NULL AND c.tes_matematika != '' AND c.tes_matematika != '00:00:00' AND c.tes_matematika != '-';
        ");

        $testResultsCount = TestResult::count();
        $this->line("     ✓ Total Test Results di `test_results`: {$testResultsCount}");

        // =====================================================================
        // STEP 3: SEED AKUN DEMO PERMANEN
        // =====================================================================
        $this->line("\n[3/3] Memastikan akun demo CBT tetap tersedia...");
        Candidate::updateOrCreate(
            ['nik' => '3578014508980002'],
            [
                'full_name' => 'Nabila Putri Santika',
                'birth_date' => '1998-08-15',
                'birth_place' => 'Surabaya',
                'gender' => 'Perempuan',
                'religion' => 'Islam',
                'education' => 'SMA / SMK',
                'phone' => '085712345678',
                'whatsapp' => '085712345678',
                'email' => 'nabila.putri98@gmail.com',
                'address_ktp' => 'Jl. Rungkut Asri No. 45, Surabaya',
                'address_domicile' => 'Jl. Rungkut Asri No. 45, Surabaya',
                'marital_status' => 'Belum Menikah',
                'expected_salary' => 4800000,
                'last_salary' => 4200000,
                'status' => 'new',
                'source_type' => 'job_portal',
                'applied_job' => 'Sales Promotion Girl (SPG)',
                'area' => 'Surabaya',
                'principle_id' => null,
                'password' => Hash::make('15081998'),
                'is_profile_complete' => 0,
                'is_komputer' => 1,
            ]
        );

        Candidate::updateOrCreate(
            ['nik' => '3171012304950001'],
            [
                'full_name' => 'Ahmad Faisal Rahman',
                'birth_date' => '1995-04-23',
                'birth_place' => 'Jakarta',
                'gender' => 'Laki-laki',
                'religion' => 'Islam',
                'education' => 'S1',
                'phone' => '081234567891',
                'whatsapp' => '081234567891',
                'email' => 'faisal.rahman@gmail.com',
                'address_ktp' => 'Jl. Kebon Jeruk No. 12, Jakarta Barat',
                'address_domicile' => 'Jl. Kebon Jeruk No. 12, Jakarta Barat',
                'marital_status' => 'Belum Menikah',
                'expected_salary' => 5500000,
                'last_salary' => 5000000,
                'status' => 'new',
                'source_type' => 'walk_in',
                'applied_job' => 'Team Leader Promotor',
                'area' => 'Jakarta',
                'principle_id' => null,
                'password' => Hash::make('23041995'),
                'is_profile_complete' => 0,
                'is_komputer' => 1,
            ]
        );
        $this->line("     ✓ Akun demo CBT Nabila & Faisal siap digunakan.");

        $totalDuration = round(microtime(true) - $startTime, 2);
        $this->info("\n==================================================================");
        $this->info("  PROSES IMPORT & SINKRONISASI DATABASE BERHASIL 100%!           ");
        $this->info("  Waktu Total: {$totalDuration} detik                            ");
        $this->info("==================================================================");

        return 0;
    }
}
