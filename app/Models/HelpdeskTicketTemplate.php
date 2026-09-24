<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class HelpdeskTicketTemplate extends Model
{
    use HasFactory;

    protected $table = 'helpdesk_ticket_templates';

    protected $fillable = [
        'division_id',
        'title',
        'subject',
        'message',
        'attachment',
        'is_active',
        'order_num',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order_num' => 'integer',
    ];

    /**
     * Divisi yang menaungi template kendala ini (opsional)
     */
    public function division(): BelongsTo
    {
        return $this->belongsTo(HelpdeskDivision::class, 'division_id');
    }

    /**
     * URL Lampiran Dokumen Format Template
     */
    public function getAttachmentUrlAttribute(): ?string
    {
        if (empty($this->attachment)) {
            return null;
        }

        return Storage::url($this->attachment);
    }

    /**
     * Nama Berkas Dokumen Format Template
     */
    public function getAttachmentFilenameAttribute(): ?string
    {
        if (empty($this->attachment)) {
            return null;
        }

        return basename($this->attachment);
    }
}
