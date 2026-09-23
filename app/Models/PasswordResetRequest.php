<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class PasswordResetRequest extends Model
{
    use HasFactory;

    protected $table = 'password_reset_requests';

    protected $fillable = [
        'ticket_number',
        'session_token',
        'employee_id',
        'nik',
        'nama_karyawan',
        'email',
        'telepon',
        'jabatan',
        'entitas',
        'tipe_karyawan',
        'status',
        'odoo_synced',
        'request_message',
        'resolved_by',
        'access_sent_at',
    ];

    protected $casts = [
        'odoo_synced'    => 'boolean',
        'access_sent_at' => 'datetime',
    ];

    protected $appends = [
        'status_badge',
        'formatted_created_at',
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(PasswordResetChatMessage::class, 'request_id')->orderBy('id');
    }

    public function latestMessage()
    {
        return $this->hasOne(PasswordResetChatMessage::class, 'request_id')->latestOfMany();
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function getFormattedCreatedAtAttribute(): string
    {
        return $this->created_at ? $this->created_at->format('d/m/Y H:i') : '-';
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'resolved' => '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800"><i class="fa-solid fa-circle-check text-[9px]"></i> Selesai (Akses Dikirim)</span>',
            'replied'  => '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800"><i class="fa-solid fa-reply text-[9px]"></i> Dibalas Admin</span>',
            'rejected' => '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800"><i class="fa-solid fa-circle-xmark text-[9px]"></i> Ditolak</span>',
            default    => '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 animate-pulse"><i class="fa-solid fa-clock text-[9px]"></i> Menunggu Balasan</span>',
        };
    }

    public static function generateTicketNumber(): string
    {
        $prefix = 'REQ-' . date('Ymd') . '-';
        $last = static::where('ticket_number', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('ticket_number');

        if ($last) {
            $lastNum = (int) substr($last, -4);
            $nextNum = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNum = '0001';
        }

        return $prefix . $nextNum;
    }
}
