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