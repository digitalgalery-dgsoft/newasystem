<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Paklaring extends Model
{
    use HasFactory;

    protected $table = 'paklarings';

    protected $guarded = ['id'];

    protected $casts = [
        'tgl_lahir' => 'date',
        'tgl_masuk' => 'date',
        'tgl_keluar' => 'date',
        'tanggaldeposit' => 'date',
        'tgl_kirimsurat' => 'date',
        'waktu_input' => 'datetime',
        'jml_deposit' => 'decimal:2',
        'current_step_order' => 'integer',
        'nomor_urutref' => 'integer',
    ];

    /**
     * Relasi ke riwayat approval bertingkat
     */
    public function approvals(): HasMany
    {
        return $this->hasMany(PaklaringApproval::class, 'paklaring_id')->latest();
    }

    /**
     * Relasi ke pembuat (jika dibuat oleh user login)
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // =========================================================================
    // QUERY SCOPES
    // =========================================================================

    public function scopeWaitingArea($query)
    {
        return $query->where('status_bagian', 'Area')->where('status', 'Proses');
    }

    public function scopeWaitingHrd($query)
    {
        return $query->where('status_bagian', 'HRD')->where('status', 'Proses');
    }

    public function scopeWaitingDb($query)
    {
        return $query->where('status_bagian', 'DB')->where('status', 'Proses');
    }

    public function scopeWaitingBpjs($query)
    {
        return $query->where('status_bagian', 'BPJS')->where('status', 'Proses');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'Selesai');
    }

    public function scopeRejectedOrHold($query)
    {
        return $query->whereIn('status', ['Tolak', 'HOLD']);
    }

    // =========================================================================
    // ACCESSORS & HELPERS
    // =========================================================================

    /**
     * Menghitung lama / durasi sejak permohonan masuk atau tahap berjalan
     */
    public function getDurationStringAttribute(): string
    {
        $startTime = $this->waktu_input ?: $this->created_at;
        if (!$startTime) {
            return '-';
        }

        $endTime = ($this->status === 'Selesai' || $this->status === 'Tolak') 
            ? ($this->updated_at ?: Carbon::now('Asia/Jakarta')) 
            : Carbon::now('Asia/Jakarta');

        $diff = $startTime->diff($endTime);
        $parts = [];
        if ($diff->d > 0) $parts[] = $diff->d . ' Hari';
        if ($diff->h > 0) $parts[] = $diff->h . ' Jam';
        if ($diff->i > 0) $parts[] = $diff->i . ' Menit';

        return empty($parts) ? '< 1 Menit' : implode(', ', $parts);
    }

    /**
     * URL untuk file dokumen lampiran
     */
    public function getFileUrl(?string $filename): ?string
    {
        if (empty($filename)) {
            return null;
        }

        if (str_starts_with($filename, 'http://') || str_starts_with($filename, 'https://')) {
            return $filename;
        }

        // Cek storage publik lokal
        if (Storage::disk('public')->exists('paklaring/' . $filename)) {
            return Storage::disk('public')->url('paklaring/' . $filename);
        }

        if (Storage::disk('public')->exists($filename)) {
            return Storage::disk('public')->url($filename);
        }

        // Fallback file legacy
        return asset('storage/paklaring/' . $filename);
    }

    /**
     * Helper status badge styling
     */
    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'Selesai' => [
                'bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'dot' => 'bg-emerald-500',
                'label' => 'Selesai / Terbit',
                'icon' => 'fa-circle-check',
            ],
            'Tolak' => [
                'bg' => 'bg-rose-50 text-rose-700 border-rose-200',
                'dot' => 'bg-rose-500',
                'label' => 'Ditolak',
                'icon' => 'fa-circle-xmark',
            ],
            'HOLD' => [
                'bg' => 'bg-amber-50 text-amber-700 border-amber-200',
                'dot' => 'bg-amber-500',
                'label' => 'Di-Hold',
                'icon' => 'fa-pause',
            ],
            default => [
                'bg' => 'bg-blue-50 text-blue-700 border-blue-200',
                'dot' => 'bg-blue-500',
                'label' => 'Dalam Proses',
                'icon' => 'fa-hourglass-half',
            ],
        };
    }

    /**
     * Helper status bagian badge styling
     */
    public function getBagianBadgeAttribute(): array
    {
        return match ($this->status_bagian) {
            'Area' => [
                'bg' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                'label' => 'Review Area (AS)',
                'icon' => 'fa-location-dot',
            ],
            'HRD' => [
                'bg' => 'bg-purple-50 text-purple-700 border-purple-200',
                'label' => 'Review HRD',
                'icon' => 'fa-user-tie',
            ],
            'DB' => [
                'bg' => 'bg-cyan-50 text-cyan-700 border-cyan-200',
                'label' => 'Review DB',
                'icon' => 'fa-database',
            ],
            'BPJS' => [
                'bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'label' => 'Review BPJS',
                'icon' => 'fa-shield-halved',
            ],
            'Selesai' => [
                'bg' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                'label' => 'Surat Selesai Terbit',
                'icon' => 'fa-certificate',
            ],
            default => [
                'bg' => 'bg-slate-50 text-slate-700 border-slate-200',
                'label' => $this->status_bagian ?: 'Dalam Proses',
                'icon' => 'fa-circle-info',
            ],
        };
    }

    public function getStatusBagianBadgeAttribute(): array
    {
        return $this->bagian_badge;
    }
}
