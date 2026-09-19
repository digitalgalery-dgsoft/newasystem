<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonalityQuestion extends Model
{
    protected $table = 'tb_kepribadian';

    public $timestamps = false;

    protected $fillable = [
        'pilihan_a',
        'pilihan_b',
        'pilihan_c',
        'pilihan_d',
    ];

    /**
     * Tipe kelompok soal: Kekuatan (1-20) atau Kelemahan (21-40)
     */
    public function getCategoryAttribute(): string
    {
        return ($this->id <= 20) ? 'Kekuatan Diri (Strengths)' : 'Kelemahan Diri (Weaknesses)';
    }

    /**
     * Helper badge warna kategori
     */
    public function getCategoryBadgeClassAttribute(): string
    {
        return ($this->id <= 20) 
            ? 'bg-emerald-50 text-emerald-700 border-emerald-200' 
            : 'bg-amber-50 text-amber-700 border-amber-200';
    }
}
