<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $hasStatusKandidat = Schema::hasColumn('candidates', 'status_kandidat');
        $hasArchiveReason = Schema::hasColumn('candidates', 'archive_reason');
        $hasTbKandidat = Schema::hasTable('tb_kandidat');

        // 1. Khusus untuk kandidat Windy Yanti.anggraeni (NIK: 3577036006950006)
        // Record ID 64648 merupakan walkin unassigned tanpa recruiter yang tertinggal Active
        // padahal data record ID 57892 sudah berstatus Arsip.
        $windyNik = '3577036006950006';
        $windyData = [
            'status' => 'Arsip',
        ];
        if ($hasStatusKandidat) {
            $windyData['status_kandidat'] = 'Arsip';
        }
        if ($hasArchiveReason) {
            $windyData['archive_reason'] = 'Disinkronkan ke Arsip: Data kandidat sudah berstatus Arsip sebelumnya';
        }

        DB::table('candidates')
            ->where('id', 64648)
            ->orWhere(function ($q) use ($windyNik) {
                $q->where('nik', $windyNik)
                  ->where(function ($sub) {
                      $sub->whereNull('useras')
                          ->orWhere('useras', '')
                          ->orWhereIn('useras', ['-', 'publik', 'online', 'unassigned', 'null']);
                  });
            })
            ->update($windyData);

        if ($hasTbKandidat) {
            $tbData = ['status' => 'Arsip'];
            if (Schema::hasColumn('tb_kandidat', 'status_kandidat')) {
                $tbData['status_kandidat'] = 'Arsip';
            }
            if (Schema::hasColumn('tb_kandidat', 'archive_reason')) {
                $tbData['archive_reason'] = 'Disinkronkan ke Arsip: Data kandidat sudah berstatus Arsip sebelumnya';
            }

            DB::table('tb_kandidat')
                ->where('id', 64648)
                ->orWhere('no_ktp', $windyNik)
                ->update($tbData);
        }

        // 2. Pembersihan massal: Semua kandidat yang memiliki NIK dengan record Arsip
        // namun masih memiliki duplikat record unassigned / kosong yang berstatus Active
        $archivedNiks = DB::table('candidates')
            ->select('nik')
            ->whereNotNull('nik')
            ->where('nik', '!=', '')
            ->where(function ($q) {
                $q->where('status', 'Arsip')
                  ->orWhere('status_kandidat', 'Arsip');
            })
            ->distinct()
            ->pluck('nik')
            ->toArray();

        if (!empty($archivedNiks)) {
            // Ambil kandidat aktif unassigned yang NIK-nya sudah pernah diarsipkan
            $unassignedDuplicateQuery = DB::table('candidates')
                ->whereIn('nik', $archivedNiks)
                ->where('status', '!=', 'Arsip')
                ->where(function ($q) {
                    $q->whereNull('useras')
                      ->orWhere('useras', '')
                      ->orWhereIn('useras', ['-', 'publik', 'online', 'unassigned', 'null']);
                })
                ->whereNull('recruiter_id');

            $affectedIds = $unassignedDuplicateQuery->pluck('id')->toArray();

            if (!empty($affectedIds)) {
                $healData = ['status' => 'Arsip'];
                if ($hasStatusKandidat) {
                    $healData['status_kandidat'] = 'Arsip';
                }
                if ($hasArchiveReason) {
                    $healData['archive_reason'] = 'Disinkronkan ke Arsip: Data kandidat sudah berstatus Arsip sebelumnya';
                }

                DB::table('candidates')
                    ->whereIn('id', $affectedIds)
                    ->update($healData);

                if ($hasTbKandidat) {
                    $tbHeal = ['status' => 'Arsip'];
                    if (Schema::hasColumn('tb_kandidat', 'status_kandidat')) {
                        $tbHeal['status_kandidat'] = 'Arsip';
                    }
                    if (Schema::hasColumn('tb_kandidat', 'archive_reason')) {
                        $tbHeal['archive_reason'] = 'Disinkronkan ke Arsip: Data kandidat sudah berstatus Arsip sebelumnya';
                    }

                    DB::table('tb_kandidat')
                        ->whereIn('id', $affectedIds)
                        ->update($tbHeal);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // One-time data heal migration; no rollback needed
    }
};
