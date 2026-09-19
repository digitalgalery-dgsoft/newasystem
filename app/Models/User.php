<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'area',
        'phone',
        'job_title',
        'signature_path',
        'is_active',
        'scope_override',
        'handle_all_principles',
        'allowed_principles',
        'cover_all_areas',
        'allowed_areas',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'scope_override' => 'boolean',
            'handle_all_principles' => 'boolean',
            'allowed_principles' => 'array',
            'cover_all_areas' => 'boolean',
            'allowed_areas' => 'array',
        ];
    }

    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class, 'recruiter_id');
    }

    public function interviewAssessments(): HasMany
    {
        return $this->hasMany(InterviewAssessment::class, 'interviewer_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isRecruiter(): bool
    {
        return in_array($this->role, ['recruiter', 'admin']);
    }

    public function isHeadHr(): bool
    {
        return in_array($this->role, ['head_hr', 'admin']);
    }

    /**
     * Relasi ke role model
     */
    public function roleModel()
    {
        return $this->belongsTo(Role::class, 'role', 'name');
    }

    /**
     * Relasi ke permissions yang di-override langsung pada user
     */
    public function customPermissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permissions')
            ->withPivot('is_granted');
    }

    /**
     * Cek apakah user memiliki izin akses tertentu (RBAC)
     */
    public function hasPermission(string $permissionName): bool
    {
        // 1. Super Administrator selalu memiliki akses ke seluruh modul
        if ($this->isAdmin()) {
            return true;
        }

        // 2. Periksa apakah ada override spesifik pada user_permissions
        $override = $this->customPermissions()->where('name', $permissionName)->first();
        if ($override) {
            return (bool) $override->pivot->is_granted;
        }

        // 3. Periksa izin dari Role yang diemban
        $role = $this->roleModel;
        if ($role && $role->hasPermission($permissionName)) {
            return true;
        }

        return false;
    }

    /**
     * Cek apakah user berhak mengakses semua prinsiple
     */
    public function handlesAllPrinciples(): bool
    {
        if ($this->isAdmin()) {
            return true;
        }
        if ($this->scope_override) {
            return (bool) ($this->handle_all_principles ?? true);
        }
        return $this->roleModel ? $this->roleModel->handlesAllPrinciples() : true;
    }

    /**
     * Cek apakah user berhak meng-cover semua area
     */
    public function coversAllAreas(): bool
    {
        if ($this->isAdmin()) {
            return true;
        }
        if ($this->scope_override) {
            return (bool) ($this->cover_all_areas ?? true);
        }
        return $this->roleModel ? $this->roleModel->coversAllAreas() : true;
    }

    /**
     * Ambil daftar prinsiple efektif yang dihandle user ini
     */
    public function getEffectivePrinciples(): array
    {
        if ($this->handlesAllPrinciples()) {
            return [];
        }
        if ($this->scope_override) {
            $arr = $this->allowed_principles;
            return is_array($arr) ? array_values(array_filter($arr)) : [];
        }
        return $this->roleModel ? $this->roleModel->getEffectivePrinciples() : [];
    }

    /**
     * Ambil daftar area efektif yang dicover user ini
     */
    public function getEffectiveAreas(): array
    {
        if ($this->coversAllAreas()) {
            return [];
        }
        if ($this->scope_override) {
            $arr = $this->allowed_areas;
            return is_array($arr) ? array_values(array_filter($arr)) : [];
        }
        return $this->roleModel ? $this->roleModel->getEffectiveAreas() : [];
    }

    /**
     * Cek apakah user dapat mengakses prinsiple tertentu
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
     * Cek apakah user dapat mengakses area tertentu
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

    /**
     * Terapkan scope filter role/user ke query Employee
     */
    public function applyRoleScopeToEmployees($query)
    {
        if ($this->isAdmin()) {
            return $query;
        }

        if (!$this->handlesAllPrinciples()) {
            $principles = $this->getEffectivePrinciples();
            if (empty($principles)) {
                $query->whereRaw('1 = 0');
            } else {
                $query->whereIn('prinsiple', $principles);
            }
        }

        if (!$this->coversAllAreas()) {
            $areas = $this->getEffectiveAreas();
            if (empty($areas)) {
                $query->whereRaw('1 = 0');
            } else {
                $query->whereIn('area', $areas);
            }
        }

        return $query;
    }

    /**
     * Terapkan scope filter role/user ke query Candidate
     */
    public function applyRoleScopeToCandidates($query)
    {
        if ($this->isAdmin()) {
            return $query;
        }

        if (!$this->handlesAllPrinciples()) {
            $principles = $this->getEffectivePrinciples();
            if (empty($principles)) {
                $query->whereRaw('1 = 0');
            } else {
                $query->where(function ($q) use ($principles) {
                    $q->whereIn('principle', $principles)
                      ->orWhereIn('idprinsiple', $principles);
                });
            }
        }

        if (!$this->coversAllAreas()) {
            $areas = $this->getEffectiveAreas();
            if (empty($areas)) {
                $query->whereRaw('1 = 0');
            } else {
                $query->whereIn('area', $areas);
            }
        }

        return $query;
    }

    public function getSignatureUrlAttribute(): ?string
    {
        if (empty($this->signature_path)) {
            return null;
        }

        if (str_starts_with($this->signature_path, 'data:image')) {
            return $this->signature_path;
        }

        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($this->signature_path)) {
            return \Illuminate\Support\Facades\Storage::disk('public')->url($this->signature_path);
        }

        if (file_exists(public_path($this->signature_path))) {
            return asset($this->signature_path);
        }

        if (file_exists(public_path('uploads/ttd/' . $this->signature_path))) {
            return asset('uploads/ttd/' . $this->signature_path);
        }

        return null;
    }

    public function getSignatureBase64(): ?string
    {
        if (empty($this->signature_path)) {
            return null;
        }

        $path = $this->signature_path;

        if (str_starts_with($path, 'data:image')) {
            return $path;
        }

        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
            $content = \Illuminate\Support\Facades\Storage::disk('public')->get($path);
            return 'data:image/png;base64,' . base64_encode($content);
        }

        if (file_exists(public_path($path))) {
            $content = file_get_contents(public_path($path));
            return 'data:image/png;base64,' . base64_encode($content);
        }

        if (file_exists(public_path('uploads/ttd/' . $path))) {
            $content = file_get_contents(public_path('uploads/ttd/' . $path));
            return 'data:image/png;base64,' . base64_encode($content);
        }

        return null;
    }

    public function hasSavedSignature(): bool
    {
        if (empty($this->signature_path)) {
            return false;
        }

        return \Illuminate\Support\Facades\Storage::disk('public')->exists($this->signature_path) ||
            file_exists(public_path($this->signature_path));
    }
}