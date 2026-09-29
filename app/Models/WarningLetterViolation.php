<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WarningLetterViolation extends Model
{
    use HasFactory;

    protected $table = 'warning_letter_violations';

    protected $fillable = [
        'warning_letter_id',
        'tanggal_pelanggaran',
        'pelanggaran',
        'kronologi',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pelanggaran' => 'date',
        ];
    }

    public function warningLetter(): BelongsTo
    {
        return $this->belongsTo(WarningLetter::class, 'warning_letter_id');
    }
}
