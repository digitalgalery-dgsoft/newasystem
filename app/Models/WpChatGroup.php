<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class WpChatGroup extends Model
{
    protected $table = 'wp_chat_groups';

    protected $fillable = [
        'name',
        'description',
        'avatar_color',
        'created_by',
        'created_by_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function members(): HasMany
    {
        return $this->hasMany(WpChatGroupMember::class, 'group_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(WpChatMessage::class, 'group_id');
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(WpChatMessage::class, 'group_id')->latestOfMany();
    }

    public function getInitialsAttribute(): string
    {
        $words = preg_split('/\s+/', trim($this->name));
        $initials = '';
        foreach (array_slice($words, 0, 2) as $w) {
            if (!empty($w)) {
                $initials .= mb_strtoupper(mb_substr($w, 0, 1));
            }
        }
        return $initials ?: 'GP';
    }
}
