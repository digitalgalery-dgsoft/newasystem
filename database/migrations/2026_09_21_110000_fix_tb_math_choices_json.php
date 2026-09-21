<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\MathQuestion;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('tb_math')) {
            return;
        }

        $standardChoices = [
            4 => [
                'A' => '66,937',
                'B' => '67,250',
                'C' => '64,500',
                'D' => '69,500',
                'E' => '70,500',
            ],
            5 => [
                'A' => '30%',
                'B' => '40%',
                'C' => '50%',
                'D' => '60%',
                'E' => '70%',
            ],
            7 => [
                'A' => '10,000',
                'B' => '9,000',
                'C' => '8,000',
                'D' => '7,000',
                'E' => '6,000',
            ],
            8 => [
                'A' => '1,000,000',
                'B' => '1,200,000',
                'C' => '1,300,000',
                'D' => '1,400,000',
                'E' => '1,500,000',
            ],
        ];

        $rows = DB::table('tb_math')->get();

        foreach ($rows as $row) {
            $parsed = MathQuestion::parseChoices($row->choices);

            if (empty($parsed) && isset($standardChoices[$row->id])) {
                $parsed = $standardChoices[$row->id];
            }

            if (!empty($parsed)) {
                $cleanJson = json_encode($parsed, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                DB::table('tb_math')
                    ->where('id', $row->id)
                    ->update([
                        'question_type' => 'multiple_choice',
                        'choices'       => $cleanJson,
                    ]);
            } elseif (in_array($row->id, [1, 2, 3, 6, 9, 10])) {
                DB::table('tb_math')
                    ->where('id', $row->id)
                    ->update([
                        'question_type' => 'fill_in_the_blank',
                        'choices'       => null,
                    ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak perlu me-revert pembersihan data valid
    }
};
