<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class WarningLetterCounter extends Model
{
    use HasFactory;

    protected $table = 'warning_letter_counters';

    protected $fillable = [
        'entity',
        'tahun',
        'last_number',
    ];

    /**
     * Konversi angka bulan ke angka romawi (1 -> I, 9 -> IX, 12 -> XII)
     */
    public static function getRomanMonth(int $month): string
    {
        $romans = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];

        return $romans[$month] ?? 'I';
    }

    /**
     * Konversi tingkat SP ke kode penomoran resmi
     */
    public static function getSpCode(string $tingkatSp): string
    {
        return match (strtolower(trim($tingkatSp))) {
            'sp1' => 'SPI',
            'sp2' => 'SPII',
            'sp3' => 'SPIII',
            default => 'SPI',
        };
    }

    /**
     * Generate nomor surat resmi secara aman (atomic transaction)
     * Format: [No. Urut (3 Digit)]/[Kode SP]/[Entitas]-[Kode Area]/[Bulan Romawi]/[Tahun]
     * Contoh: 001/SPI/AMK-SBY/IX/2026
     */
    public static function generateNextNomorSurat(string $entity, ?string $singkatanArea, string $tingkatSp, ?Carbon $date = null): array
    {
        $date = $date ?: Carbon::now();
        $year = (int) $date->year;
        $month = (int) $date->month;
        $cleanEntity = strtoupper(trim($entity ?: 'AMK'));
        $areaCode = strtoupper(trim($singkatanArea ?: 'PST'));
        $spCode = self::getSpCode($tingkatSp);
        $romanMonth = self::getRomanMonth($month);

        return DB::transaction(function () use ($cleanEntity, $year, $spCode, $areaCode, $romanMonth) {
            $counter = self::where('entity', $cleanEntity)
                ->where('tahun', $year)
                ->lockForUpdate()
                ->first();

            if (!$counter) {
                $counter = self::create([
                    'entity' => $cleanEntity,
                    'tahun' => $year,
                    'last_number' => 0,
                ]);
            }

            $counter->last_number += 1;
            $counter->save();

            $nomorUrut = $counter->last_number;
            $formattedNomor = sprintf('%03d/%s/%s-%s/%s/%d', $nomorUrut, $spCode, $cleanEntity, $areaCode, $romanMonth, $year);

            return [
                'nomor_urut' => $nomorUrut,
                'nomor_surat' => $formattedNomor,
            ];
        });
    }
}
