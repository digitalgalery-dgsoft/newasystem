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
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = [
        'parsed_choices',
        'type_badge_label',
    ];

    /**
     * Scope query untuk soal aktif saja
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    /**
     * Helper static untuk parsing string choices dalam format apapun
     * (JSON standar, double-encoded JSON, escaped backslashes, array PHP)
     */
    public static function parseChoices($value): ?array
    {
        if (empty($value)) {
            return null;
        }

        if (is_array($value)) {
            return $value;
        }

        if (!is_string($value)) {
            return null;
        }

        $val = trim($value);

        for ($i = 0; $i < 3; $i++) {
            if (!is_string($val) || $val === '') {
                break;
            }

            // Coba json_decode langsung
            $decoded = json_decode($val, true);
            if (json_last_error() === JSON_ERROR_NONE && !is_null($decoded)) {
                if (is_array($decoded)) {
                    return $decoded;
                }
                if (is_string($decoded)) {
                    $val = $decoded;
                    continue;
                }
            }

            // Jika mengandung backslash escape, bersihkan dan decode kembali
            if (str_contains($val, '\\')) {
                $stripped = stripslashes($val);
                $decoded = json_decode($stripped, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    return $decoded;
                }
                $val = $stripped;
            } else {
                break;
            }
        }

        return is_array($val) ? $val : null;
    }

    /**
     * Accessor atribut choices
     */
    public function getChoicesAttribute($value): ?array
    {
        return self::parseChoices($value);
    }

    /**
     * Accessor untuk pilihan ganda yang dinormalisasi (selalu array)
     */
    public function getParsedChoicesAttribute(): array
    {
        $raw = $this->getRawOriginal('choices') ?? ($this->attributes['choices'] ?? null);
        return self::parseChoices($raw) ?? [];
    }

    /**
     * Mutator atribut choices agar tersimpan bersih sebagai JSON di database
     */
    public function setChoicesAttribute($value): void
    {
        if (is_null($value) || $value === '') {
            $this->attributes['choices'] = null;
        } elseif (is_array($value)) {
            $this->attributes['choices'] = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } elseif (is_string($value)) {
            $parsed = self::parseChoices($value);
            $this->attributes['choices'] = !empty($parsed)
                ? json_encode($parsed, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                : $value;
        } else {
            $this->attributes['choices'] = null;
        }
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
