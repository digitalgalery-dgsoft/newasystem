<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class HelpdeskDivision extends Model
{
    use HasFactory;

    protected $table = 'helpdesk_divisions';

    protected $fillable = [
        'name',
        'code',
        'description',
        'icon',
        'color',
        'wa_group_id',
        'wa_group_link',
        'sla_hours',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sla_hours' => 'integer',
    ];

    /**
     * Tiket yang masuk ke divisi ini
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(HelpdeskTicket::class, 'division_id');
    }

    /**
     * Petugas / Agen di divisi ini
     */
    public function divisionAgents(): HasMany
    {
        return $this->hasMany(HelpdeskDivisionAgent::class, 'division_id');
    }

    /**
     * Users yang menjadi agen divisi
     */
    public function agents(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'helpdesk_division_agents', 'division_id', 'user_id')
            ->withPivot('is_lead', 'is_auto_assign')
            ->withTimestamps();
    }

    /**
     * Template balasan cepat untuk divisi ini
     */
    public function cannedResponses(): HasMany
    {
        return $this->hasMany(HelpdeskCannedResponse::class, 'division_id');
    }

    /**
     * Helper badge warna Tailwind
     */
    public function getColorBadgeAttribute(): string
    {
        return match($this->color) {
            'indigo' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
            'emerald' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'amber' => 'bg-amber-50 text-amber-700 border-amber-200',
            'rose' => 'bg-rose-50 text-rose-700 border-rose-200',
            'purple' => 'bg-purple-50 text-purple-700 border-purple-200',
            default => 'bg-blue-50 text-blue-700 border-blue-200',
        };
    }
}
