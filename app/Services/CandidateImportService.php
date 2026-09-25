<?php

namespace App\Services;

use App\Models\Candidate;
use App\Models\Principle;
use App\Models\WorkExperience;
use App\Models\TestResult;
use App\Models\InterviewAssessment;
use App\Models\PrincipleApproval;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use ZipArchive;
use SimpleXMLElement;
use Exception;

class CandidateImportService
{
    /**
     * Parse raw .xlsx file directly without external dependencies.
     * Returns an array of rows (each row is an array of column values).
     */
    public static function parseXlsxFile(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new Exception("File tidak ditemukan: {$filePath}");
        }

        $zip = new ZipArchive();
        $openResult = $zip->open($filePath);
        if ($openResult !== true) {
            throw new Exception("Gagal membuka file Excel (ZipArchive error code: {$openResult}). Pastikan format file .xlsx valid.");
        }

        // 1. Read Shared Strings (xl/sharedStrings.xml)
        $sharedStrings = [];
        $stringsIndex = $zip->locateName('xl/sharedStrings.xml');
        if ($stringsIndex !== false) {
            $stringsXml = simplexml_load_string($zip->getFromIndex($stringsIndex));
            if ($stringsXml && isset($stringsXml->si)) {
                foreach ($stringsXml->si as $si) {
                    if (isset($si->t)) {
                        $sharedStrings[] = (string)$si->t;
                    } elseif (isset($si->r)) {
                        $str = '';
                        foreach ($si->r as $r) {
                            $str .= (string)$r->t;
                        }
                        $sharedStrings[] = $str;
                    } else {
                        $sharedStrings[] = '';
                    }
                }
            }
        }

        // 2. Read Sheet1 (xl/worksheets/sheet1.xml)
        $sheetIndex = $zip->locateName('xl/worksheets/sheet1.xml');
        if ($sheetIndex === false) {
            // Fallback: cari worksheet pertama yang tersedia
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $stat = $zip->statIndex($i);
                if (str_starts_with($stat['name'], 'xl/worksheets/sheet') && str_ends_with($stat['name'], '.xml')) {
                    $sheetIndex = $i;
                    break;
                }
            }
        }

        if ($sheetIndex === false) {
            $zip->close();
            throw new Exception("Lembar kerja (worksheet) tidak ditemukan di dalam file Excel.");
        }

        $sheetXml = simplexml_load_string($zip->getFromIndex($sheetIndex));
        $zip->close();

        if (!$sheetXml || !isset($sheetXml->sheetData->row)) {
            return [];
        }

        $rows = [];
        foreach ($sheetXml->sheetData->row as $r) {
            $rowCells = [];
            foreach ($r->c as $c) {
                $cellRef = (string)$c['r'];
                preg_match('/^([A-Z]+)(\d+)$/', $cellRef, $matches);
                if (empty($matches[1])) continue;

                $colLetters = $matches[1];
                $colIdx = 0;
                for ($i = 0; $i < strlen($colLetters); $i++) {
                    $colIdx = $colIdx * 26 + (ord($colLetters[$i]) - ord('A') + 1);
                }
                $colIdx -= 1;

                $type = (string)$c['t'];
                $val = (string)$c->v;

                if ($type === 's') {
                    $val = $sharedStrings[(int)$val] ?? '';
                } elseif ($type === 'inlineStr' && isset($c->is->t)) {
                    $val = (string)$c->is->t;
                }
                $rowCells[$colIdx] = $val;
            }

            $maxCol = !empty($rowCells) ? max(array_keys($rowCells)) : -1;
            $normalizedRow = [];
            for ($i = 0; $i <= $maxCol; $i++) {
                $normalizedRow[$i] = $rowCells[$i] ?? '';
            }
            $rows[] = $normalizedRow;
        }

        return $rows;
    }

    /**
     * Konversi tanggal Excel (serial float/integer atau string) ke format Y-m-d.
     */
    public static function parseExcelDate($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            $floatVal = (float)$value;
            // Tanggal Excel (1900 date system)
            $days = (int)$floatVal;
            if ($days > 60) {
                // Bug tahun kabisat 1900 di Excel: selisih hari ke 1 Jan 1970 adalah 25569
                $timestamp = ($days - 25569) * 86400;
                return gmdate('Y-m-d', $timestamp);
            } elseif ($days > 0) {
                $timestamp = ($days - 25568) * 86400;
                return gmdate('Y-m-d', $timestamp);
            }
        }

        $str = trim((string)$value);
        $ts = strtotime($str);
        if ($ts && $ts > 0) {
            return date('Y-m-d', $ts);
        }

        return null;
    }

    /**
     * Sanitasi string sesuai dengan standar keamanan legacy importkandidatint.php
     */
    public static function sanitizeInput($input): string
    {
        return preg_replace("/[^a-zA-Z0-9\s.,@()\/_\-]/u", "", trim((string)$input));
    }

    /**
     * Jalankan proses import baris demi baris dengan callback event untuk streaming terminal.
     *
     * @param string $filePath Path ke file .xlsx
     * @param string $userEmail Email rekruter penanggung jawab
     * @param int|null $userId ID User penanggung jawab
     * @param callable $onEvent Callback: function(string $type, string $message, ?array $meta = null)
     * @return array Ringkasan hasil import ['total', 'success', 'failed', 'experience_added']
     */
    public function import(string $filePath, string $userEmail, ?int $userId, callable $onEvent): array
    {
        $rows = self::parseXlsxFile($filePath);
        $totalRows = count($rows);

        if ($totalRows <= 1) {
            $onEvent('warning', "File Excel kosong atau hanya berisi baris judul header.");
            return ['total' => 0, 'success' => 0, 'failed' => 0, 'experience_added' => 0];
        }

        // Cache daftar Prinsiple agar lookup cepat
        $principlesMap = Principle::pluck('id', 'name')->mapWithKeys(function ($id, $name) {
            return [strtoupper(trim($name)) => $id];
        })->toArray();

        $lastKtp = null;
        $lastCandidateId = null;
        $lastCandidateName = null;

        $stats = [
            'total' => 0,
            'success' => 0,
            'failed' => 0,
            'experience_added' => 0,
        ];

        $hasTbKandidat = Schema::hasTable('tb_kandidat');
        $hasTbPengalaman = Schema::hasTable('tb_pengalaman');

        $dataRowsCount = $totalRows - 1;
        $onEvent('info', "Memulai pemrosesan {$dataRowsCount} baris data dari file template...", ['total_rows' => $dataRowsCount]);

        foreach ($rows as $index => $row) {
            // Lewati header (index 0)
            if ($index === 0) continue;

            $rowNumber = $index + 1;

            // Ambil kolom sesuai template Odoo hr.applicant.xlsx (31 kolom)
            $colTanggal          = $row[0] ?? '';
            $colNoKtp            = $row[1] ?? '';
            $colApplicantsName   = $row[2] ?? '';
            $colKotaLahir        = $row[3] ?? '';
            $colTanggalLahir     = $row[4] ?? '';
            $colAlamatDomisili   = $row[5] ?? '';
            $colAlamatKtp        = $row[6] ?? '';
            $colHeight           = $row[7] ?? '';
            $colWeight           = $row[8] ?? '';
            $colReligion         = $row[9] ?? '';
            $colPendidikan       = $row[10] ?? '';
            $colPhone            = $row[11] ?? '';
            $colNamaIbu          = $row[12] ?? '';
            $colContactName      = $row[13] ?? '';
            $colContactNumber    = $row[14] ?? '';
            $colEmergencyRel     = $row[15] ?? '';
            $colStatusKawin      = $row[16] ?? '';
            $colSuamiIstri       = $row[17] ?? '';
            $colNamaBank         = $row[18] ?? '';
            $colNoRekening       = $row[19] ?? '';
            $colAtasNama         = $row[20] ?? '';
            $colArea             = $row[21] ?? '';
            $colPrinciple        = $row[22] ?? '';
            $colNpwp             = $row[23] ?? '';
            $colAppliedJob       = $row[24] ?? '';
            $colStage            = $row[25] ?? '';
            $colPerusahaan       = $row[26] ?? '';
            $colTglJoin          = $row[27] ?? '';
            $colTglResign        = $row[28] ?? '';
            $colAlasanKeluar     = $row[29] ?? '';
            $colJabatanLama      = $row[30] ?? '';

            // Bersihkan data
            $rawKtp = trim((string)$colNoKtp);
            $cleanKtp = preg_replace('/\D/', '', $rawKtp);
            $namaPerusahaan = self::sanitizeInput($colPerusahaan);

            // Jika baris benar-benar kosong
            if (empty($cleanKtp) && empty($namaPerusahaan) && empty(trim((string)$colApplicantsName))) {
                continue;
            }

            $stats['total']++;

            // KASUS 1: Baris Kandidat Baru (Memiliki NIK)
            if (!empty($cleanKtp)) {
                // Validasi NIK 16 digit angka
                if (strlen($cleanKtp) !== 16) {
                    $stats['failed']++;
                    $len = strlen($cleanKtp);
                    $onEvent('error', "Baris {$rowNumber}: NIK '{$cleanKtp}' tidak valid ({$len} digit). NIK wajib 16 digit angka.", [
                        'row' => $rowNumber,
                        'nik' => $cleanKtp,
                        'name' => self::sanitizeInput($colApplicantsName)
                    ]);
                    continue;
                }

                $applicantsName = ucwords(strtolower(self::sanitizeInput($colApplicantsName)));
                if (empty($applicantsName)) {
                    $applicantsName = 'Kandidat NIK ' . $cleanKtp;
                }

                $birthDate = self::parseExcelDate($colTanggalLahir);
                $passwordPlain = $birthDate ? date('dmY', strtotime($birthDate)) : '12345678';
                $passwordHashed = bcrypt($passwordPlain);

                $prinName = self::sanitizeInput($colPrinciple);
                $principleId = null;
                if (!empty($prinName)) {
                    $prinUpper = strtoupper($prinName);
                    if (isset($principlesMap[$prinUpper])) {
                        $principleId = $principlesMap[$prinUpper];
                    } else {
                        // Coba pencarian LIKE
                        $found = Principle::where('name', 'like', "%{$prinName}%")->first();
                        if ($found) {
                            $principleId = $found->id;
                            $principlesMap[$prinUpper] = $found->id;
                        }
                    }
                }

                $appliedJob = self::sanitizeInput($colAppliedJob);
                $area = self::sanitizeInput($colArea);
                $phone = preg_replace('/[^0-9]/', '', (string)$colPhone);
                if (str_starts_with($phone, '62')) {
                    $phone = '0' . substr($phone, 2);
                }

                try {
                    // 1. Cek apakah kandidat dengan NIK ini sudah ada di database
                    $existingCandidates = Candidate::where('nik', $cleanKtp)->orderBy('id')->get();
                    $isReplaced = $existingCandidates->isNotEmpty();

                    $candidatePayload = [
                        'nik'                      => $cleanKtp,
                        'full_name'                => $applicantsName,
                        'birth_place'              => self::sanitizeInput($colKotaLahir),
                        'birth_date'               => $birthDate,
                        'address_ktp'              => self::sanitizeInput($colAlamatKtp),
                        'address_domicile'         => self::sanitizeInput($colAlamatDomisili),
                        'height'                   => (int)preg_replace('/\D/', '', (string)$colHeight) ?: null,
                        'weight'                   => (int)preg_replace('/\D/', '', (string)$colWeight) ?: null,
                        'religion'                 => self::sanitizeInput($colReligion),
                        'education'                => self::sanitizeInput($colPendidikan),
                        'phone'                    => $phone,
                        'whatsapp'                 => $phone,
                        'mother_name'              => self::sanitizeInput($colNamaIbu),
                        'emergency_contact_name'   => self::sanitizeInput($colContactName),
                        'emergency_contact_phone'  => self::sanitizeInput($colContactNumber),
                        'emergency_contact_relation'=> self::sanitizeInput($colEmergencyRel),
                        'marital_status'           => self::sanitizeInput($colStatusKawin),
                        'spouse_name'              => self::sanitizeInput($colSuamiIstri),
                        'bank_name'                => self::sanitizeInput($colNamaBank),
                        'bank_account_number'      => self::sanitizeInput($colNoRekening),
                        'bank_account_holder'      => ucwords(strtolower(self::sanitizeInput($colAtasNama))),
                        'area'                     => $area,
                        'penempatan'               => $area,
                        'principle'                => $prinName,
                        'principle_id'             => $principleId,
                        'npwp'                     => self::sanitizeInput($colNpwp),
                        'applied_job'              => $appliedJob ?: 'Kandidat Walkin',
                        'status'                   => 'Active',
                        'jenis'                    => '', // Kosong agar tampil di list interview utama
                        'source_type'              => 'walk_in',
                        'useras'                   => $userEmail,
                        'recruiter_id'             => $userId,
                        'password'                 => $passwordHashed,
                        'odoo_stage_name'          => self::sanitizeInput($colStage) ?: null,
                        'odoo_synced_at'           => !empty(trim((string)$colStage)) ? now() : null,

                        // RESET DATA TEST ONLINE KE AWAL:
                        'tes_kepribadian'          => null,
                        'tes_matematika'           => null,
                        'tes_komputer'             => null,
                        'tes_ke'                   => 1,
                        'buktikomputer'            => null,
                        'is_profile_complete'      => false,
                        'signature_path'           => null,
                        'statement_agreed'         => false,
                        'idprinsiple'              => null,
                        'ttd_prinsiple'            => null,
                        'time_prinsiple'           => null,
                        'note_principle'           => null,
                        'status_approval'          => null,
                        'ai_score'                 => null,
                        'ai_cv_analysis'           => null,
                    ];

                    if ($isReplaced) {
                        $candidate = $existingCandidates->last();
                        $allCandIds = $existingCandidates->pluck('id')->all();

                        // Bersihkan duplikat record jika ada dari import terdahulu, migrasikan child records ke kandidat utama
                        $duplicateIds = array_diff($allCandIds, [$candidate->id]);
                        if (!empty($duplicateIds)) {
                            TestResult::whereIn('candidate_id', $duplicateIds)->update(['candidate_id' => $candidate->id]);
                            WorkExperience::whereIn('candidate_id', $duplicateIds)->update(['candidate_id' => $candidate->id]);
                            Candidate::whereIn('id', $duplicateIds)->delete();
                        }

                        // JANGAN hapus TestResult, tb_hasilpsikotes, tb_hasilmath, hasil_kompt, WorkExperience!
                        // Pertahankan tes online dan tanda tangan yang sudah dikerjakan kandidat
                        if (!empty($candidate->tes_kepribadian)) unset($candidatePayload['tes_kepribadian']);
                        if (!empty($candidate->tes_matematika)) unset($candidatePayload['tes_matematika']);
                        if (!empty($candidate->tes_komputer)) unset($candidatePayload['tes_komputer']);
                        if (!empty($candidate->tes_ke)) unset($candidatePayload['tes_ke']);
                        if (!empty($candidate->buktikomputer)) unset($candidatePayload['buktikomputer']);
                        if (!empty($candidate->signature_path)) unset($candidatePayload['signature_path']);
                        if (!empty($candidate->statement_agreed)) unset($candidatePayload['statement_agreed']);
                        if (!empty($candidate->password)) unset($candidatePayload['password']);
                        if (empty($candidatePayload['education']) && !empty($candidate->education)) unset($candidatePayload['education']);

                        // Replace data kandidat dengan payload yang telah dipreservasi
                        $candidate->fill($candidatePayload);
                        $candidate->save();
                        $candidate->checkProfileCompleteness();
                        $newId = $candidate->id;
                    } else {
                        // Buat kandidat baru
                        $candidate = Candidate::create($candidatePayload);
                        $newId = $candidate->id;
                    }

                    // 2. Simpan / Replace ke tabel legacy tb_kandidat jika tabel tersedia
                    if ($hasTbKandidat) {
                        $tbKandidatData = [
                            'tanggal'             => date('Y-m-d'),
                            'no_ktp'              => $cleanKtp,
                            'applicants_name'     => $applicantsName,
                            'alamat_ktp'          => self::sanitizeInput($colAlamatKtp),
                            'alamat_domisili'     => self::sanitizeInput($colAlamatDomisili),
                            'kota_lahir'          => self::sanitizeInput($colKotaLahir),
                            'tanggal_lahir'       => $birthDate ?: '1970-01-01',
                            'height'              => (string)$colHeight,
                            'weight'              => (string)$colWeight,
                            'religion'            => self::sanitizeInput($colReligion),
                            'pendidikan_terakhir' => self::sanitizeInput($colPendidikan),
                            'phone'               => $phone,
                            'mobile'              => $phone,
                            'nama_ibu_kandung'    => self::sanitizeInput($colNamaIbu),
                            'contact_name'        => self::sanitizeInput($colContactName),
                            'contact_number'      => self::sanitizeInput($colContactNumber),
                            'emergency_relation'  => self::sanitizeInput($colEmergencyRel),
                            'area'                => $area,
                            'principle'           => $prinName,
                            'applied_job'         => $appliedJob,
                            'status_kawin'        => self::sanitizeInput($colStatusKawin),
                            'nama_suamiistri'     => self::sanitizeInput($colSuamiIstri),
                            'bank'                => self::sanitizeInput($colNamaBank),
                            'rekening'            => self::sanitizeInput($colNoRekening),
                            'atas_nama'           => ucwords(strtolower(self::sanitizeInput($colAtasNama))),
                            'NPWP'                => self::sanitizeInput($colNpwp),
                            'password'            => $passwordHashed,
                            'useras'              => $userEmail,
                            'status'              => 'Active',
                            'jenis'               => '',
                            'info'                => 'WhatsApp',
                            'undangan'            => 'WhatsApp',
                            'waktukirim'          => now(),

                            // Nilai default awal tes online pada tabel legacy
                            'tes_kepribadian'     => null,
                            'tes_matematika'      => null,
                            'tes_komputer'        => null,
                            'tes_ke'              => 1,
                            'idprinsiple'         => null,
                            'ttd_prinsiple'       => null,
                        ];

                        $existingTb = DB::table('tb_kandidat')->where('no_ktp', $cleanKtp)->first();
                        if ($existingTb) {
                            if (!empty($existingTb->tes_kepribadian) || !empty($candidate->tes_kepribadian)) {
                                unset($tbKandidatData['tes_kepribadian']);
                            }
                            if (!empty($existingTb->tes_matematika) || !empty($candidate->tes_matematika)) {
                                unset($tbKandidatData['tes_matematika']);
                            }
                            if (!empty($existingTb->tes_komputer) || !empty($candidate->tes_komputer)) {
                                unset($tbKandidatData['tes_komputer']);
                            }
                            if (!empty($existingTb->tes_ke) || !empty($candidate->tes_ke)) {
                                unset($tbKandidatData['tes_ke']);
                            }
                            if (!empty($existingTb->password)) {
                                unset($tbKandidatData['password']);
                            }
                            if (empty($tbKandidatData['pendidikan_terakhir']) && !empty($existingTb->pendidikan_terakhir)) {
                                unset($tbKandidatData['pendidikan_terakhir']);
                            }
                            DB::table('tb_kandidat')->where('no_ktp', $cleanKtp)->update($tbKandidatData);
                        } else {
                            $tbKandidatData['id'] = $newId;
                            try {
                                DB::table('tb_kandidat')->insert($tbKandidatData);
                            } catch (Exception $eTb) {
                                DB::table('tb_kandidat')->where('no_ktp', $cleanKtp)->update($tbKandidatData);
                            }
                        }
                    }

                    $lastKtp = $cleanKtp;
                    $lastCandidateId = $newId;
                    $lastCandidateName = $applicantsName;

                    $stats['success']++;
                    if ($isReplaced) {
                        $onEvent('replace', "Baris {$rowNumber}: Data NIK {$cleanKtp} [{$applicantsName}] berhasil di-REPLACE & hasil tes online di-RESET ke awal.", [
                            'row'  => $rowNumber,
                            'id'   => $newId,
                            'name' => $applicantsName,
                            'nik'  => $cleanKtp,
                        ]);
                    } else {
                        $onEvent('success', "Baris {$rowNumber}: Berhasil import [{$applicantsName}] - NIK: {$cleanKtp} | Posisi: {$appliedJob} | Area: {$area}", [
                            'row'  => $rowNumber,
                            'id'   => $newId,
                            'name' => $applicantsName,
                            'nik'  => $cleanKtp,
                        ]);
                    }

                    // 4. Jika ada data Pengalaman Kerja pada baris ini, masukkan ke database
                    if (!empty($namaPerusahaan)) {
                        $tglJoin = self::parseExcelDate($colTglJoin);
                        $tglResign = self::parseExcelDate($colTglResign);
                        $alasanKeluar = self::sanitizeInput($colAlasanKeluar);
                        $jabatan = self::sanitizeInput($colJabatanLama);

                        WorkExperience::create([
                            'candidate_id'       => $newId,
                            'company_name'       => $namaPerusahaan,
                            'position'           => $jabatan,
                            'start_date'         => $tglJoin,
                            'end_date'           => $tglResign,
                            'reason_for_leaving' => $alasanKeluar,
                        ]);

                        if ($hasTbPengalaman) {
                            try {
                                DB::table('tb_pengalaman')->insert([
                                    'id_kandidat'     => $newId,
                                    'nomor_ktp'       => $cleanKtp,
                                    'nama_perusahaan' => $namaPerusahaan,
                                    'jabatan'         => $jabatan,
                                    'tgl_masuk'       => $tglJoin ?: '1970-01-01',
                                    'tgl_keluar'      => $tglResign ?: '1970-01-01',
                                    'alasan_keluar'   => $alasanKeluar,
                                    'tanggal'         => date('Y-m-d'),
                                ]);
                            } catch (Exception $eExp) {}
                        }

                        $stats['experience_added']++;
                        $onEvent('experience', "  -> Pengalaman kerja ditambahkan: {$namaPerusahaan} ({$jabatan})");
                    }

                } catch (Exception $e) {
                    $stats['failed']++;
                    $onEvent('error', "Baris {$rowNumber}: Gagal menyimpan kandidat ({$applicantsName}): " . $e->getMessage(), [
                        'row' => $rowNumber,
                        'nik' => $cleanKtp,
                        'error' => $e->getMessage(),
                    ]);
                }

            // KASUS 2: Baris Kelanjutan Pengalaman Kerja (NIK kosong, tapi nama perusahaan ada)
            } elseif (!empty($namaPerusahaan) && !empty($lastCandidateId)) {
                try {
                    $tglJoin = self::parseExcelDate($colTglJoin);
                    $tglResign = self::parseExcelDate($colTglResign);
                    $alasanKeluar = self::sanitizeInput($colAlasanKeluar);
                    $jabatan = self::sanitizeInput($colJabatanLama);

                    WorkExperience::create([
                        'candidate_id'       => $lastCandidateId,
                        'company_name'       => $namaPerusahaan,
                        'position'           => $jabatan,
                        'start_date'         => $tglJoin,
                        'end_date'           => $tglResign,
                        'reason_for_leaving' => $alasanKeluar,
                    ]);

                    if ($hasTbPengalaman) {
                        try {
                            DB::table('tb_pengalaman')->insert([
                                'id_kandidat'     => $lastCandidateId,
                                'nomor_ktp'       => $lastKtp,
                                'nama_perusahaan' => $namaPerusahaan,
                                'jabatan'         => $jabatan,
                                'tgl_masuk'       => $tglJoin ?: '1970-01-01',
                                'tgl_keluar'      => $tglResign ?: '1970-01-01',
                                'alasan_keluar'   => $alasanKeluar,
                                'tanggal'         => date('Y-m-d'),
                            ]);
                        } catch (Exception $eExp) {}
                    }

                    $stats['experience_added']++;
                    $onEvent('experience', "Baris {$rowNumber}: Menambahkan riwayat kerja lanjutan untuk [{$lastCandidateName}]: {$namaPerusahaan} ({$jabatan})");

                } catch (Exception $e) {
                    $onEvent('warning', "Baris {$rowNumber}: Gagal menambahkan riwayat kerja lanjutan: " . $e->getMessage());
                }
            }
        }

        $onEvent('complete', "Selesai memproses seluruh file. Total Data: {$stats['total']} | Sukses: {$stats['success']} | Gagal: {$stats['failed']} | Pengalaman: {$stats['experience_added']}", $stats);

        return $stats;
    }
}
