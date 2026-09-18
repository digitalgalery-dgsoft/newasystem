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

    public function getSignatureUrlAttribute(): ?string
    {
        if (!empty($this->signature_path)) {
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
        }

        $defaultSig = 'signatures/user_' . $this->id . '.png';
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($defaultSig)) {
            return \Illuminate\Support\Facades\Storage::disk('public')->url($defaultSig);
        }

        return null;
    }

    public function getSignatureBase64(): ?string
    {
        $path = $this->signature_path;
        if (empty($path)) {
            $defaultSig = 'signatures/user_' . $this->id . '.png';
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($defaultSig)) {
                $path = $defaultSig;
            }
        }

        if (empty($path)) {
            return null;
        }

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
        return !empty($this->signature_path) ||
            \Illuminate\Support\Facades\Storage::disk('public')->exists('signatures/user_' . $this->id . '.png');
    }
}