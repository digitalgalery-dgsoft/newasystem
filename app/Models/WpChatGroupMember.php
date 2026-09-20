<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WpChatGroupMember extends Model
{
    protected $table = 'wp_chat_group_members';

    protected $fillable = [
        'group_id',
        'user_name',
        'user_id',
        'role',
        'joined_at',
        'last_read_at',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'last_read_at' => 'datetime',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(WpChatGroup::class, 'group_id');
    }
}
