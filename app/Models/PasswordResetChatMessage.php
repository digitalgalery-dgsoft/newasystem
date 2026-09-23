<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PasswordResetChatMessage extends Model
{
    use HasFactory;

    protected $table = 'password_reset_chat_messages';

    protected $fillable = [
        'request_id',
        'sender_type',
        'sender_name',
        'message',
        'meta',
        'is_read',
    ];

    protected $casts = [
        'meta'    => 'array',
        'is_read' => 'boolean',
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(PasswordResetRequest::class, 'request_id');
    }
}
