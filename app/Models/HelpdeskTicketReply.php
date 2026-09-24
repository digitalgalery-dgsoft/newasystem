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
     * Cek apakah balasan memiliki berkas lampiran
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

            $url = ($this->ticket_id && $this->id)
                ? route('helpdesk.tickets.reply.attachment', [
                    'ticketId' => $this->ticket_id,
                    'replyId' => $this->id,
                    'file' => $filename
                ])
                : asset('storage/' . $path);
            $downloadUrl = ($this->ticket_id && $this->id)
                ? route('helpdesk.tickets.reply.attachment', [
                    'ticketId' => $this->ticket_id,
                    'replyId' => $this->id,
                    'file' => $filename,
                    'download' => 1
                ])
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
     * URL Berkas Lampiran Utama (Backwards Compatibility)
     */
    public function getAttachmentUrlAttribute(): ?string
    {
        $details = $this->attachments_details;
        return !empty($details) ? $details[0]['url'] : null;
    }

    /**
     * Cek apakah berkas lampiran adalah gambar (Backwards Compatibility)
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
