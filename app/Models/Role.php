<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'is_system',
        'handle_all_principles',
        'allowed_principles',
        'cover_all_areas',
        'allowed_areas',
    ];

    protected $casts = [
        'is_system' => 'boolean',
        'handle_all_principles' => 'boolean',
        'allowed_principles' => 'array',
        'cover_all_areas' => 'boolean',
        'allowed_areas' => 'array',
    ];

    /**
     * Relasi ke permissions yang dimiliki role ini
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }

    /**
     * Relasi ke users yang memiliki role ini
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'role', 'name');
    }

    /**
     * Helper cek apakah role memiliki permission tertentu
     */
    public function hasPermission(string $permissionName): bool
    {
        return $this->permissions->contains('name', $permissionName);
    }

    /**
     * Cek apakah role menangani semua prinsiple
     */
    public function handlesAllPrinciples(): bool
    {
        if ($this->name === 'admin') {
            return true;
        }
        return (bool) ($this->handle_all_principles ?? true);
    }

    /**
     * Cek apakah role meng-cover semua area
     */
    public function coversAllAreas(): bool
    {
        if ($this->name === 'admin') {
            return true;
        }
        return (bool) ($this->cover_all_areas ?? true);
    }

    /**
     * Ambil daftar prinsiple yang boleh diakses oleh role ini
     */
    public function getEffectivePrinciples(): array
    {
        if ($this->handlesAllPrinciples()) {
            return [];
        }
        $arr = $this->allowed_principles;
        return is_array($arr) ? array_values(array_filter($arr)) : [];
    }

    /**
     * Ambil daftar area yang dicover oleh role ini
     */
    public function getEffectiveAreas(): array
    {
        if ($this->coversAllAreas()) {
            return [];
        }
        $arr = $this->allowed_areas;
        return is_array($arr) ? array_values(array_filter($arr)) : [];
    }

    /**
     * Cek apakah role ini berhak mengakses prinsiple tertentu
     */
    public function canAccessPrinciple(?string $principleName): bool
    {
        if ($this->handlesAllPrinciples()) {
            return true;
        }
        if (empty($principleName)) {
            return true;
        }
        $effective = array_map('strtolower', array_map('trim', $this->getEffectivePrinciples()));
        return in_array(strtolower(trim($principleName)), $effective, true);
    }

    /**
     * Cek apakah role ini berhak mengakses area tertentu
     */
    public function canAccessArea(?string $areaName): bool
    {
        if ($this->coversAllAreas()) {
            return true;
        }
        if (empty($areaName)) {
            return true;
        }
        $effective = array_map('strtolower', array_map('trim', $this->getEffectiveAreas()));
        return in_array(strtolower(trim($areaName)), $effective, true);
    }
}
