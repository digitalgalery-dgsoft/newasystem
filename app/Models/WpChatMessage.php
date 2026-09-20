<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WpChatMessage extends Model
{
    protected $table = 'wp_chat_messages';

    protected $fillable = [
        'group_id',
        'user_sender',
        'sender_id',
        'message_text',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(WpChatGroup::class, 'group_id');
    }

    public function getFormattedTimeAttribute(): string
    {
        return $this->created_at ? $this->created_at->format('H:i') : '';
    }

    public function getFormattedDateAttribute(): string
    {
        if (!$this->created_at) return '';
        if ($this->created_at->isToday()) return 'Hari Ini';
        if ($this->created_at->isYesterday()) return 'Kemarin';
        return $this->created_at->translatedFormat('d M Y');
    }
}
