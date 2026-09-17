<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OdooEntity extends Model
{
    use HasFactory;

    protected $table = 'odoo_entities';

    protected $fillable = [
        'code',
        'name',
        'odoo_url',
        'odoo_db',
        'odoo_username',
        'odoo_api_key',
        'is_active',
        'last_sync_at',
        'last_sync_status',
        'last_sync_message',
        'sync_counts',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_sync_at' => 'datetime',
        'sync_counts' => 'array',
    ];

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'entity', 'code');
    }

    public function syncLogs(): HasMany
    {
        return $this->hasMany(OdooSyncLog::class, 'entity_code', 'code')->orderBy('id', 'desc');
    }

    public function isConfigured(): bool
    {
        return !empty($this->odoo_url) && !empty($this->odoo_db) && !empty($this->odoo_username) && !empty($this->odoo_api_key);
    }

    public function getActiveEmployeesCountAttribute(): int
    {
        return $this->employees()->where('status', 'Aktiv')->count();
    }

    public function getTotalEmployeesCountAttribute(): int
    {
        return $this->employees()->count();
    }

    public function getResignedEmployeesCountAttribute(): int
    {
        return $this->employees()->where('status', 'Resign')->count();
    }
}
