<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class ActivityLog extends Model
{
    use HasFactory;

    protected $table = 'activity_logs';

    protected $fillable = [
        'user_id',
        'user_name',
        'user_email',
        'user_jabatan',
        'action',
        'module',
        'description',
        'subject_type',
        'subject_id',
        'properties',
        'ip_address',
        'user_agent',
        'url',
        'method',
    ];

    protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi ke Pengguna
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope Filter Modul
     */
    public function scopeFilterModule($query, $module)
    {
        if (!empty($module)) {
            $query->where('module', $module);
        }
        return $query;
    }

    /**
     * Scope Filter Aksi
     */
    public function scopeFilterAction($query, $action)
    {
        if (!empty($action)) {
            $query->where('action', strtoupper($action));
        }
        return $query;
    }

    /**
     * Scope Filter User
     */
    public function scopeFilterUser($query, $userId)
    {
        if (!empty($userId)) {
            $query->where('user_id', $userId);
        }
        return $query;
    }

    /**
     * Scope Pencarian Teks
     */
    public function scopeSearch($query, $keyword)
    {
        if (!empty($keyword)) {
            $keyword = trim($keyword);
            $query->where(function ($q) use ($keyword) {
                $q->where('description', 'like', "%{$keyword}%")
                  ->orWhere('user_name', 'like', "%{$keyword}%")
                  ->orWhere('user_email', 'like', "%{$keyword}%")
                  ->orWhere('ip_address', 'like', "%{$keyword}%")
                  ->orWhere('subject_id', 'like', "%{$keyword}%");
            });
        }
        return $query;
    }

    /**
     * Scope Rentang Tanggal
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        if (!empty($startDate)) {
            $query->whereDate('created_at', '>=', Carbon::parse($startDate)->startOfDay());
        }
        if (!empty($endDate)) {
            $query->whereDate('created_at', '<=', Carbon::parse($endDate)->endOfDay());
        }
        return $query;
    }

    /**
     * Accessor Badge Kelas Warna
     */
    public function getActionBadgeClassAttribute(): string
    {
        return match (strtoupper($this->action)) {
            'LOGIN'        => 'bg-cyan-50 text-cyan-700 border-cyan-200 dark:bg-cyan-950/40 dark:text-cyan-300 dark:border-cyan-800',
            'LOGOUT'       => 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
            'CREATE'       => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800',
            'UPDATE'       => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800',
            'DELETE'       => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800',
            'EXPORT'       => 'bg-green-50 text-green-700 border-green-200 dark:bg-green-950/40 dark:text-green-300 dark:border-green-800',
            'SYNC'         => 'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-950/40 dark:text-purple-300 dark:border-purple-800',
            'SWITCH_USER'  => 'bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-950/40 dark:text-indigo-300 dark:border-indigo-800',
            'PROFILE'      => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800',
            default        => 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
        };
    }

    /**
     * Accessor Ikon Aksi
     */
    public function getActionIconAttribute(): string
    {
        return match (strtoupper($this->action)) {
            'LOGIN'        => 'fa-solid fa-right-to-bracket',
            'LOGOUT'       => 'fa-solid fa-right-from-bracket',
            'CREATE'       => 'fa-solid fa-plus-circle',
            'UPDATE'       => 'fa-solid fa-pen-to-square',
            'DELETE'       => 'fa-solid fa-trash-can',
            'EXPORT'       => 'fa-solid fa-file-excel',
            'SYNC'         => 'fa-solid fa-arrows-rotate',
            'SWITCH_USER'  => 'fa-solid fa-shuffle',
            'PROFILE'      => 'fa-solid fa-user-gear',
            default        => 'fa-solid fa-circle-info',
        };
    }

    /**
     * Accessor Tanggal Terformat (WIB)
     */
    public function getFormattedCreatedAtAttribute(): string
    {
        return $this->created_at ? $this->created_at->translatedFormat('d M Y H:i:s') . ' WIB' : '-';
    }

    /**
     * Accessor Waktu Relatif
     */
    public function getDiffTimeAttribute(): string
    {
        return $this->created_at ? $this->created_at->diffForHumans() : '-';
    }

    /**
     * Accessor Informasi Perangkat / Device Parser Ringan
     */
    public function getDeviceAttribute(): string
    {
        $ua = $this->user_agent;
        if (empty($ua)) {
            return 'Sistem / CLI';
        }

        $browser = 'Browser';
        if (str_contains($ua, 'Edg')) $browser = 'Edge';
        elseif (str_contains($ua, 'Chrome')) $browser = 'Chrome';
        elseif (str_contains($ua, 'Firefox')) $browser = 'Firefox';
        elseif (str_contains($ua, 'Safari')) $browser = 'Safari';
        elseif (str_contains($ua, 'Opera') || str_contains($ua, 'OPR')) $browser = 'Opera';

        $os = 'Device';
        if (str_contains($ua, 'Windows')) $os = 'Windows';
        elseif (str_contains($ua, 'Macintosh') || str_contains($ua, 'Mac OS')) $os = 'macOS';
        elseif (str_contains($ua, 'Linux')) $os = 'Linux';
        elseif (str_contains($ua, 'Android')) $os = 'Android';
        elseif (str_contains($ua, 'iPhone') || str_contains($ua, 'iPad')) $os = 'iOS';

        return "{$browser} ({$os})";
    }
}
