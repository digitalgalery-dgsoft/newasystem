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
        'avatar',
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

    public function helpdeskTickets(): HasMany
    {
        return $this->hasMany(HelpdeskTicket::class, 'user_id');
    }

    public function helpdeskAssignedTickets(): HasMany
    {
        return $this->hasMany(HelpdeskTicket::class, 'assigned_to');
    }

    public function helpdeskDivisions()
    {
        return $this->belongsToMany(HelpdeskDivision::class, 'helpdesk_division_agents', 'user_id', 'division_id')
            ->withPivot('is_lead', 'is_auto_assign')
            ->withTimestamps();
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isHelpdeskAdmin(): bool
    {
        return $this->role === 'admin' || $this->isAdmin();
    }

    public function isHelpdeskDivisionUser(): bool
    {
        if ($this->isHelpdeskAdmin()) return false;
        return HelpdeskDivisionAgent::where('user_id', $this->id)->exists();
    }

    public function isHelpdeskRegularUser(): bool
    {
        return !$this->isHelpdeskAdmin() && !$this->isHelpdeskDivisionUser();
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
     * Cek apakah user berstatus HRD (Admin, Head HR, Role HRD, atau Jabatan HRD/Manager)
     */
    public function isHrd(): bool
    {
        if ($this->isAdmin() || $this->role === 'admin' || $this->role === 'head_hr' || $this->role === 'hrd') {
            return true;
        }
        $role = strtolower($this->role ?? '');
        if ($role === 'hrd' || $role === 'head_hr') {
            return true;
        }

        $job = strtolower($this->job_title ?? '');
        // Rekruter biasa BUKAN HRD, kecuali memiliki titel Head / Lead / Manager
        if (str_contains($job, 'recruiter') || str_contains($job, 'rekrutmen') || str_contains($job, 'aro')) {
            return str_contains($job, 'head') || str_contains($job, 'lead') || str_contains($job, 'manager');
        }

        return str_contains($job, 'hrd') || str_contains($job, 'hr lead') || str_contains($job, 'hr manager') || str_contains($job, 'head of hr');
    }

    /**
     * Cek apakah user berstatus Head / Pimpinan (Head HR, Supervisor, Manager, Lead, atau terdaftar sebagai Pimpinan di data karyawan)
     */
    public function isHead(): bool
    {
        if ($this->isHrd()) {
            return true;
        }
        if (in_array($this->role, ['head', 'head_hr', 'supervisor', 'area_manager', 'team_leader'])) {
            return true;
        }
        $job = strtolower($this->job_title ?? '');
        if ($this->linked_employee && !empty($this->linked_employee->jabatan)) {
            $job .= ' ' . strtolower($this->linked_employee->jabatan);
        }
        // Staff atau rekruter biasa tanpa titel pimpinan bukan Head
        if (str_contains($job, 'staff') || str_contains($job, 'promotor') || ((str_contains($job, 'recruiter') || str_contains($job, 'rekrutmen')) && !str_contains($job, 'head') && !str_contains($job, 'hod') && !str_contains($job, 'lead') && !str_contains($job, 'manager'))) {
            return false;
        }
        $headKeywords = ['head', 'hod', 'spv', 'supervisor', 'manager', 'lead', 'koordinator', 'pimpinan', 'om ops', 'om '];
        foreach ($headKeywords as $kw) {
            if (str_contains($job, $kw)) {
                return true;
            }
        }
        // Cek apakah tercatat sebagai pimpinan bagi karyawan di database
        if (!empty($this->name)) {
            $isPimpinan = Employee::where('pimpinan', $this->name)
                ->orWhere('pimpinan', 'like', "%{$this->name}%")
                ->exists();
            if ($isPimpinan) {
                return true;
            }
        }
        return false;
    }

    /**
     * Cek apakah user berhak mengakses modul kandidat inhouse (HRD atau Head)
     */
    public function isHrdOrHead(): bool
    {
        return $this->isHrd() || $this->isHead();
    }

    /**
     * Ambil daftar identifier (email / nama) dari rekruter atau karyawan binaan di bawah Head ini
     */
    public function getSubordinateRecruiterIdentifiers(): array
    {
        $names = array_filter([$this->name, $this->email]);
        if (empty($names)) {
            return [];
        }

        $subordinates = Employee::where(function($q) use ($names) {
            foreach ($names as $n) {
                $q->orWhere('pimpinan', $n)->orWhere('pimpinan', 'like', "%{$n}%");
            }
        })->get(['email', 'nama_karyawan']);

        $identifiers = [];
        foreach ($subordinates as $sub) {
            if (!empty($sub->email)) {
                $identifiers[] = strtolower(trim($sub->email));
            }
            if (!empty($sub->nama_karyawan)) {
                $identifiers[] = strtolower(trim($sub->nama_karyawan));
            }
        }

        // Sertakan juga rekruter yang ditugaskan ke user ID ini
        $recruiterUsers = User::where('id', '!=', $this->id)
            ->where(function($q) use ($names) {
                foreach ($names as $n) {
                    $q->orWhere('name', 'like', "%{$n}%");
                }
            })->get(['email', 'name']);
        foreach ($recruiterUsers as $ru) {
            if (!empty($ru->email)) $identifiers[] = strtolower(trim($ru->email));
            if (!empty($ru->name)) $identifiers[] = strtolower(trim($ru->name));
        }

        return array_values(array_unique($identifiers));
    }

    /**
     * Cek apakah user berhak melihat seluruh data kandidat (lintas rekruter / nasional)
     * Mengembalikan true jika user adalah admin, role head_hr/admin_officer,
     * ATAU memiliki izin khusus 'view_all_candidates'.
     */
    public function canViewAllCandidates(): bool
    {
        if ($this->isAdmin() || $this->role === 'admin') {
            return true;
        }

        if (in_array($this->role, ['head_hr', 'admin_officer'])) {
            return true;
        }

        if ($this->hasPermission('view_all_candidates')) {
            return true;
        }

        return false;
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
        if ($this->handle_all_principles !== null) {
            return (bool) $this->handle_all_principles;
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
        if ($this->cover_all_areas !== null) {
            return (bool) $this->cover_all_areas;
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
        if ($this->allowed_principles !== null) {
            $arr = $this->allowed_principles;
            return is_array($arr) ? array_values(array_filter($arr)) : [];
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
        if ($this->allowed_areas !== null) {
            $arr = $this->allowed_areas;
            return is_array($arr) ? array_values(array_filter($arr)) : [];
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

    /**
     * Sinkronkan password ke seluruh data master employee yang terkait dengan user ini
     */
    public function syncPasswordToEmployees(string $hashedPassword): void
    {
        try {
            $employeeIds = collect();

            if (!empty($this->email)) {
                $lowerEmail = strtolower(trim($this->email));
                $byEmail = Employee::whereRaw('LOWER(TRIM(email)) = ?', [$lowerEmail])->pluck('id');
                $employeeIds = $employeeIds->merge($byEmail);

                if (str_contains($lowerEmail, '@')) {
                    $noY = str_replace('y', '', $lowerEmail);
                    $byNoY = Employee::whereRaw("LOWER(REPLACE(email, 'y', '')) = ?", [$noY])->pluck('id');
                    $employeeIds = $employeeIds->merge($byNoY);
                }

                if (str_ends_with($lowerEmail, '@asystem.co.id')) {
                    $nikPart = explode('@', $lowerEmail)[0];
                    $byNik = Employee::where('nik', $nikPart)->pluck('id');
                    $employeeIds = $employeeIds->merge($byNik);
                }
            }

            if (!empty($this->phone)) {
                $cleanPhone = preg_replace('/\D/', '', $this->phone);
                if (strlen($cleanPhone) >= 8) {
                    $byPhone = Employee::whereRaw("REPLACE(REPLACE(telepon, '-', ''), ' ', '') LIKE ?", ['%' . substr($cleanPhone, -8)])->pluck('id');
                    $employeeIds = $employeeIds->merge($byPhone);
                }
            }

            if (!empty($this->linked_employee?->id)) {
                $employeeIds->push($this->linked_employee->id);
            }

            $uniqueIds = $employeeIds->unique()->filter()->values();
            if ($uniqueIds->isNotEmpty()) {
                Employee::whereIn('id', $uniqueIds)->update([
                    'password' => $hashedPassword,
                ]);
            }
        } catch (\Throwable $e) {
            \Log::warning('Sinkronisasi password employee gagal: ' . $e->getMessage());
        }
    }

    /**
     * Mendapatkan data Employee terkait berdasarkan email
     */
    public function getLinkedEmployeeAttribute(): ?Employee
    {
        if (!empty($this->email)) {
            $emp = Employee::whereRaw('LOWER(TRIM(email)) = ?', [strtolower(trim($this->email))])
                ->where('status', 'Aktiv')
                ->orderByRaw("CASE WHEN tipe_karyawan = 'Inhouse' THEN 0 ELSE 1 END")
                ->orderByDesc('akses_login')
                ->orderByDesc('id')
                ->first();
            if ($emp) return $emp;

            $empAny = Employee::whereRaw('LOWER(TRIM(email)) = ?', [strtolower(trim($this->email))])
                ->orderByRaw("CASE WHEN tipe_karyawan = 'Inhouse' THEN 0 ELSE 1 END")
                ->orderByDesc('akses_login')
                ->orderByDesc('id')
                ->first();
            if ($empAny) return $empAny;
        }

        if (!empty($this->name)) {
            $emp = Employee::whereRaw('LOWER(TRIM(nama_karyawan)) = ?', [strtolower(trim($this->name))])
                ->where('status', 'Aktiv')
                ->orderByRaw("CASE WHEN tipe_karyawan = 'Inhouse' THEN 0 ELSE 1 END")
                ->orderByDesc('akses_login')
                ->orderByDesc('id')
                ->first();
            if ($emp) return $emp;
        }

        return null;
    }

    /**
     * Nama Jabatan Pengguna untuk Tampilan Dashboard (Prioritas: Jabatan di Data Karyawan -> Job Title User -> Fallback Role)
     */
    public function getJabatanDisplayAttribute(): string
    {
        // 1. Cek dari linked employee (Data Karyawan)
        $emp = $this->linked_employee;
        if ($emp && !empty($emp->jabatan)) {
            return trim($emp->jabatan);
        }

        // 2. Cek dari field job_title pada tabel users
        if (!empty($this->job_title)) {
            return trim($this->job_title);
        }

        // 3. Fallback jika jabatan belum diisi
        return match ($this->role) {
            'admin' => 'Administrator',
            'karyawan_inhouse' => 'Karyawan Inhouse',
            'karyawan_ratecard' => 'Karyawan RateCard',
            'recruiter' => 'Recruiter',
            'head_hr' => 'Head of HR',
            default => !empty($this->role) ? ucfirst(str_replace('_', ' ', $this->role)) : 'Staff'
        };
    }

    /**
     * URL Foto Avatar Pengguna dengan fallback cerdas
     */
    public function getAvatarUrlAttribute(): string
    {
        if (!empty($this->avatar)) {
            $avatarClean = ltrim($this->avatar, '/\\');
            if (file_exists(public_path($avatarClean))) {
                return asset($avatarClean);
            }
            if (file_exists(public_path('uploads/avatars/' . basename($avatarClean)))) {
                return asset('uploads/avatars/' . basename($avatarClean));
            }
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($avatarClean)) {
                return asset('storage/' . $avatarClean);
            }
        }

        // Cek jika akun terhubung ke data Employee yang memiliki foto
        $emp = $this->linked_employee;
        if ($emp && !empty($emp->foto)) {
            $fotoClean = ltrim($emp->foto, '/\\');
            if (file_exists(public_path($fotoClean))) {
                return asset($fotoClean);
            }
            if (file_exists(public_path('lampiran/' . basename($fotoClean)))) {
                return asset('lampiran/' . basename($fotoClean));
            }
        }

        // Fallback ke UI Avatars dinamis sesuai role
        $color = match ($this->role) {
            'admin' => '0F52BA',
            'karyawan_inhouse' => '059669',
            'karyawan_ratecard' => 'D97706',
            default => '4F46E5'
        };

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name ?? 'User') . "&background={$color}&color=fff&size=256&bold=true";
    }
}