<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MathQuestion extends Model
{
    protected $table = 'tb_math';

    protected $fillable = [
        'question_type',
        'question_text',
        'choices',
        'correct_answer',
        'is_active',
    ];

    protected $casts = [
        'choices' => 'array',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope query untuk soal aktif saja
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    /**
     * Accessor untuk pilihan ganda yang dinormalisasi
     */
    public function getParsedChoicesAttribute(): array
    {
        if (empty($this->choices)) {
            return [];
        }

        if (is_array($this->choices)) {
            return $this->choices;
        }

        $decoded = json_decode($this->choices, true);
        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Helper cek apakah tipe pilihan ganda
     */
    public function isMultipleChoice(): bool
    {
        return $this->question_type === 'multiple_choice';
    }

    /**
     * Helper label badge tipe
     */
    public function getTypeBadgeLabelAttribute(): string
    {
        return $this->isMultipleChoice() ? 'Pilihan Ganda' : 'Isian Singkat';
    }
}
