<?php

namespace App\Console\Commands;

use App\Models\Candidate;
use App\Models\TestResult;
use App\Models\InterviewAssessment;
use App\Models\PrincipleApproval;
use App\Models\WorkExperience;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CleanDuplicateCandidatesCommand extends Command
{
    protected $signature = 'candidates:clean-duplicates {--dry-run : Tampilkan daftar duplikasi tanpa menghapus data}';

    protected $description = 'Bersihkan duplikasi data kandidat berdasarkan NIK dan pertahankan 1 record paling mutakhir';

    public function handle(): int
    {
        $this->info("=== MEMULAI PEMBERSIHAN DUPLIKASI DATA KANDIDAT (NIK) ===");
        $isDryRun = (bool)$this->option('dry-run');

        if ($isDryRun) {
            $this->warn("[DRY-RUN] Mode simulasi aktif. Tidak ada data yang akan dihapus.");
        }

        // Cari NIK yang memiliki lebih dari 1 record
        $duplicates = DB::table('candidates')
            ->select('nik', DB::raw('count(*) as total'))
            ->whereNotNull('nik')
            ->where('nik', '!=', '')
            ->groupBy('nik')
            ->havingRaw('count(*) > 1')
            ->get();

        if ($duplicates->isEmpty()) {
            $this->info("Tidak ditemukan duplikasi NIK pada tabel candidates. Database bersih!");
            return 0;
        }

        $this->warn("Ditemukan {$duplicates->count()} NIK dengan record duplikat.");

        $totalRemoved = 0;

        foreach ($duplicates as $item) {
            $nik = $item->nik;
            $candidates = Candidate::where('nik', $nik)->orderBy('id', 'asc')->get();

            if ($candidates->count() <= 1) {
                continue;
            }

            // Pertahankan record terakhir (paling baru)
            $keep = $candidates->last();
            $remove = $candidates->slice(0, $candidates->count() - 1);
            $removeIds = $remove->pluck('id')->all();

            $this->line("NIK: {$nik} | Total: {$candidates->count()} | Simpan ID: {$keep->id} ({$keep->full_name}) | Hapus ID: " . implode(', ', $removeIds));

            if (!$isDryRun) {
                // Hapus relasi yang mengarah ke ID-ID lama yang akan dihapus
                TestResult::whereIn('candidate_id', $removeIds)->delete();
                InterviewAssessment::whereIn('candidate_id', $removeIds)->delete();
                PrincipleApproval::whereIn('candidate_id', $removeIds)->delete();
                WorkExperience::whereIn('candidate_id', $removeIds)->delete();

                if (Schema::hasTable('tb_hasilpsikotes')) {
                    DB::table('tb_hasilpsikotes')->whereIn('id_kandidat', $removeIds)->delete();
                }
                if (Schema::hasTable('tb_hasilmath')) {
                    DB::table('tb_hasilmath')->whereIn('id_kandidat', $removeIds)->delete();
                }
                if (Schema::hasTable('hasil_kompt')) {
                    DB::table('hasil_kompt')->whereIn('id_kandidat', $removeIds)->delete();
                }
                if (Schema::hasTable('hasilinterview')) {
                    DB::table('hasilinterview')->whereIn('id_kandidat', $removeIds)->delete();
                }
                if (Schema::hasTable('tb_pengalaman')) {
                    DB::table('tb_pengalaman')->whereIn('id_kandidat', $removeIds)->delete();
                }

                // Hapus duplikat candidates
                Candidate::whereIn('id', $removeIds)->delete();

                // Bersihkan tb_kandidat jika ada duplikat ID lama
                if (Schema::hasTable('tb_kandidat')) {
                    DB::table('tb_kandidat')->whereIn('id', $removeIds)->delete();
                }
            }

            $totalRemoved += count($removeIds);
        }

        if ($isDryRun) {
            $this->info("[DRY-RUN] Selesai. Total record duplikat yang dapat dibersihkan: {$totalRemoved}.");
        } else {
            $this->info("Pembersihan selesai. Berhasil menghapus {$totalRemoved} record duplikat!");
        }

        return 0;
    }
}
