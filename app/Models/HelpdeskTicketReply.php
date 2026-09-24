<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HelpdeskTicketReply extends Model
{
    use HasFactory;

    protected $table = 'helpdesk_ticket_replies';

    protected $fillable = [
        'ticket_id',
        'user_id',
        'message',
        'attachment',
        'is_internal',
    ];

    protected $casts = [
        'is_internal' => 'boolean',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(HelpdeskTicket::class, 'ticket_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
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
        return route('helpdesk.tickets.reply.attachment', [
            'ticketId' => $this->ticket_id,
            'replyId' => $this->id
        ]);
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
