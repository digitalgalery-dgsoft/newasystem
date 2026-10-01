<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Candidate;
use App\Models\TestResult;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            if (!Schema::hasTable('candidates')) {
                return;
            }

            // 1. Ambil kandidat berstatus aktif (bukan arsip) yang memiliki tes_kepribadian terisi
            // tetapi kandidat tersebut BELUM pernah mengerjakan tes kepribadian sama sekali
            // (0 record di TestResult psychology dan 0 record di tb_hasilpsikotes untuk ID kandidat aktif ini)
            $candidatesToClean = Candidate::where(function ($q) {
                    $q->whereNull('status')->orWhereNotIn('status', ['Arsip', 'archived']);
                })
                ->where(function ($q) {
                    $q->whereNull('status_kandidat')->orWhere('status_kandidat', '!=', 'Arsip');
                })
                ->whereNotNull('tes_kepribadian')
                ->where('tes_kepribadian', '!=', '')
                ->where('tes_kepribadian', '!=', '00:00:00')
                ->where('tes_kepribadian', '!=', '-')
                ->get();

            $cleanedCount = 0;
            $cleanedNiks = [];

            foreach ($candidatesToClean as $candidate) {
                // Cek apakah kandidat ini memiliki hasil tes kepribadian miliknya sendiri di TestResult
                $hasOwnCbt = TestResult::where('candidate_id', $candidate->id)
                    ->where('test_type', 'psychology')
                    ->exists();

                // Cek apakah kandidat ini memiliki hasil tes di tabel legacy tb_hasilpsikotes miliknya sendiri
                $hasOwnLegacy = false;
                if (Schema::hasTable('tb_hasilpsikotes')) {
                    $hasOwnLegacy = DB::table('tb_hasilpsikotes')
                        ->where('id_kandidat', $candidate->id)
                        ->exists();
                }

                // Jika kandidat TIDAK memiliki hasil tes sendiri, artinya data tes_kepribadian berasal
                // dari auto-sync / warisan riwayat lamaran arsip lama secara keliru
                if (!$hasOwnCbt && !$hasOwnLegacy) {
                    $candidate->tes_kepribadian = null;
                    $candidate->saveQuietly();
                    $cleanedCount++;

                    if (!empty($candidate->nik)) {
                        $cleanedNiks[] = $candidate->nik;
                    }
                }
            }

            // 2. Bersihkan juga kolom tes_kepribadian pada tb_kandidat untuk NIK kandidat aktif tersebut
            if (Schema::hasTable('tb_kandidat') && !empty($cleanedNiks)) {
                $uniqueNiks = array_values(array_unique(array_filter($cleanedNiks)));
                DB::table('tb_kandidat')
                    ->whereIn('no_ktp', $uniqueNiks)
                    ->where(function ($q) {
                        $q->whereNull('status')->orWhereNotIn('status', ['Arsip', 'archived']);
                    })
                    ->update([
                        'tes_kepribadian' => null,
                    ]);
            }

            Log::info("Migrasi fix_uncompleted_psychology_tests selesai. Total kandidat aktif dibersihkan: {$cleanedCount}");
        } catch (\Throwable $e) {
            Log::error("Migrasi fix_uncompleted_psychology_tests error: " . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No destructive rollback needed
    }
};
