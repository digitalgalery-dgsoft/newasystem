<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class HelpdeskTicket extends Model
{
    use HasFactory;

    protected $table = 'helpdesk_tickets';

    protected $fillable = [
        'ticket_number',
        'user_id',
        'division_id',
        'assigned_to',
        'subject',
        'description',
        'priority',
        'status',
        'category',
        'attachment',
        'due_date',
        'sentiment',
        'workplan_task_id',
        'first_response_at',
        'resolved_at',
        'closed_at',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'first_response_at' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    /**
     * Boot model untuk auto-generate ticket_number unik (TKT-YYYYMMDD-XXXX)
     */
    protected static function booted()
    {
        static::creating(function ($ticket) {
            if (empty($ticket->ticket_number)) {
                $dateStr = date('Ymd');
                $latest = self::whereDate('created_at', Carbon::today())
                    ->orderBy('id', 'desc')
                    ->first();
                $seq = 1;
                if ($latest && preg_match('/TKT-\d{8}-(\d+)/', $latest->ticket_number, $m)) {
                    $seq = (int)$m[1] + 1;
                }
                $ticket->ticket_number = 'TKT-' . $dateStr . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(HelpdeskDivision::class, 'division_id');
    }

    public function assignedAgent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(HelpdeskTicketReply::class, 'ticket_id')->orderBy('created_at', 'asc');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(HelpdeskTicketLog::class, 'ticket_id')->orderBy('created_at', 'desc');
    }

    public function workplanTask(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'workplan_task_id');
    }

    /**
     * Cek apakah tiket sudah selesai / ditutup
     */
    public function isClosed(): bool
    {
        return in_array($this->status, ['resolved', 'closed']);
    }

    /**
     * Cek apakah tiket sudah di-assign ke agen
     */
    public function isAssigned(): bool
    {
        return !empty($this->assigned_to);
    }

    /**
     * Cek apakah user berwenang merespon / mengelola tiket
     */
    public function canBeManagedBy(?User $user): bool
    {
        if (!$user) return false;
        if ($user->isAdmin() || $user->role === 'admin') return true;
        if ($this->assigned_to === $user->id) return true;

        // Cek apakah user terdaftar sebagai agen di divisi tiket ini
        return HelpdeskDivisionAgent::where('division_id', $this->division_id)
            ->where('user_id', $user->id)
            ->exists();
    }

    /**
     * Label & Warna Status
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'open' => 'Menunggu Respon',
            'in_progress' => 'Sedang Diproses',
            'answered' => 'Telah Dijawab',
            'resolved' => 'Terselesaikan',
            'closed' => 'Ditutup',
            default => ucfirst($this->status),
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'open' => 'bg-amber-100 text-amber-800 border-amber-300',
            'in_progress' => 'bg-blue-100 text-blue-800 border-blue-300',
            'answered' => 'bg-purple-100 text-purple-800 border-purple-300',
            'resolved' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            'closed' => 'bg-slate-100 text-slate-700 border-slate-300',
            default => 'bg-gray-100 text-gray-700 border-gray-300',
        };
    }

    /**
     * Label & Warna Prioritas
     */
    public function getPriorityBadgeAttribute(): string
    {
        return match(strtolower($this->priority)) {
            'urgent' => 'bg-rose-100 text-rose-800 border-rose-300 font-bold',
            'high' => 'bg-orange-100 text-orange-800 border-orange-300 font-semibold',
            'low' => 'bg-slate-100 text-slate-600 border-slate-300',
            default => 'bg-sky-100 text-sky-800 border-sky-300',
        };
    }

    /**
     * Cek apakah SLA melewati deadline
     */
    public function isOverdue(): bool
    {
        if ($this->isClosed() || empty($this->due_date)) return false;
        return Carbon::now()->gt($this->due_date);
    }

    /**
     * URL Berkas Lampiran (via Route Streaming Aman)
     */
    public function getAttachmentUrlAttribute(): ?string
    {
        if (!$this->attachment) return null;
        if (str_starts_with($this->attachment, 'http://') || str_starts_with($this->attachment, 'https://')) {
            return $this->attachment;
        }
        return route('helpdesk.tickets.attachment', $this->id);
    }

    /**
     * Cek apakah berkas lampiran adalah gambar
     */
    public function getIsImageAttachmentAttribute(): bool
    {
        if (!$this->attachment) return false;
        $ext = strtolower(pathinfo($this->attachment, PATHINFO_EXTENSION));
        return in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg']);
    }

    /**
     * Nama Berkas Lampiran
     */
    public function getAttachmentFilenameAttribute(): ?string
    {
        if (!$this->attachment) return null;
        return basename($this->attachment);
    }
}
