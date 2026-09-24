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
     * Daftar Berkas Lampiran (Array of paths)
     */
    public function getAttachmentsListAttribute(): array
    {
        if (empty($this->attachment)) return [];

        $decoded = json_decode($this->attachment, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return array_values(array_filter($decoded));
        }

        return [$this->attachment];
    }

    /**
     * Cek apakah tiket memiliki berkas lampiran
     */
    public function getHasAttachmentsAttribute(): bool
    {
        return !empty($this->attachments_list);
    }

    /**
     * Rincian Lengkap Setiap Berkas Lampiran
     */
    public function getAttachmentsDetailsAttribute(): array
    {
        $list = $this->attachments_list;
        $details = [];

        foreach ($list as $index => $path) {
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $filename = basename($path);
            $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg']);
            $isPdf = ($ext === 'pdf');
            $isWord = in_array($ext, ['doc', 'docx']);
            $isExcel = in_array($ext, ['xls', 'xlsx', 'csv']);
            $isPpt = in_array($ext, ['ppt', 'pptx']);
            $isDoc = ($isWord || $isExcel || $isPpt);

            $icon = match(true) {
                $isImage => 'fa-regular fa-image text-blue-500',
                $isPdf => 'fa-solid fa-file-pdf text-rose-500',
                $isWord => 'fa-solid fa-file-word text-blue-600',
                $isExcel => 'fa-solid fa-file-excel text-emerald-600',
                $isPpt => 'fa-solid fa-file-powerpoint text-orange-500',
                default => 'fa-regular fa-file-lines text-slate-500',
            };

            $badgeColor = match(true) {
                $isImage => 'bg-blue-50 text-blue-700 border-blue-200',
                $isPdf => 'bg-rose-50 text-rose-700 border-rose-200',
                $isWord => 'bg-sky-50 text-sky-700 border-sky-200',
                $isExcel => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                $isPpt => 'bg-orange-50 text-orange-700 border-orange-200',
                default => 'bg-slate-50 text-slate-700 border-slate-200',
            };

            $url = $this->id 
                ? route('helpdesk.tickets.attachment', ['id' => $this->id, 'file' => $filename])
                : asset('storage/' . $path);
            $downloadUrl = $this->id 
                ? route('helpdesk.tickets.attachment', ['id' => $this->id, 'file' => $filename, 'download' => 1])
                : asset('storage/' . $path);

            $details[] = [
                'index' => $index,
                'path' => $path,
                'filename' => $filename,
                'ext' => $ext,
                'is_image' => $isImage,
                'is_pdf' => $isPdf,
                'is_word' => $isWord,
                'is_excel' => $isExcel,
                'is_ppt' => $isPpt,
                'is_doc' => $isDoc,
                'icon' => $icon,
                'badge_color' => $badgeColor,
                'url' => $url,
                'download_url' => $downloadUrl,
            ];
        }

        return $details;
    }

    /**
     * URL Berkas Lampiran Utama / Pertama (Backwards Compatibility)
     */
    public function getAttachmentUrlAttribute(): ?string
    {
        $details = $this->attachments_details;
        return !empty($details) ? $details[0]['url'] : null;
    }

    /**
     * Cek apakah berkas lampiran utama adalah gambar (Backwards Compatibility)
     */
    public function getIsImageAttachmentAttribute(): bool
    {
        $details = $this->attachments_details;
        return !empty($details) ? $details[0]['is_image'] : false;
    }

    /**
     * Nama Berkas Lampiran Utama (Backwards Compatibility)
     */
    public function getAttachmentFilenameAttribute(): ?string
    {
        $details = $this->attachments_details;
        return !empty($details) ? $details[0]['filename'] : null;
    }
}
