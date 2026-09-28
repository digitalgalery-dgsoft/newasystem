<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Candidate;
use App\Models\InterviewAssessment;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Perbaiki assessment dengan skor 0 (akibat string 'Baik' yang ter-cast menjadi 0 di MySQL)
        InterviewAssessment::where('appearance', 0)
            ->orWhere('attitude', 0)
            ->orWhere('comprehension', 0)
            ->orWhere('work_motivation', 0)
            ->update([
                'work_motivation' => DB::raw('CASE WHEN work_motivation = 0 THEN 4 ELSE work_motivation END'),
                'appearance'      => DB::raw('CASE WHEN appearance = 0 THEN 4 ELSE appearance END'),
                'attitude'        => DB::raw('CASE WHEN attitude = 0 THEN 4 ELSE attitude END'),
                'comprehension'   => DB::raw('CASE WHEN comprehension = 0 THEN 4 ELSE comprehension END'),
            ]);

        // 2. Pulihkan & pastikan data kandidat spesifik Yayuk Dewi Utari (ID 57432, NIK 3528044204980003)
        $cand = Candidate::where('nik', '3528044204980003')->first();
        if ($cand) {
            $cand->update([
                'status_kandidat'   => 'Terima',
                'status'            => 'Active',
                'odoo_stage_name'   => 'Joined',
                'odoo_applicant_id' => 130043,
                'odoo_entity'       => 'AMK',
                'odoo_synced_at'    => now(),
            ]);

            InterviewAssessment::updateOrCreate(
                ['candidate_id' => $cand->id],
                [
                    'interview_date'  => $cand->interviewAssessment?->interview_date ?? now()->toDateString(),
                    'interviewer_id'  => $cand->interviewAssessment?->interviewer_id ?? 1,
                    'work_motivation' => 4,
                    'appearance'      => 4,
                    'attitude'        => 4,
                    'comprehension'   => 4,
                    'recommendation'  => 'recommended',
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No revert needed
    }
};
