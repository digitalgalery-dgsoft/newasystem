<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskComment extends Model
{
    use HasFactory;

    protected $table = 'task_comments';

    protected $fillable = [
        'id',
        'task_id',
        'user_comment',
        'comment_text',
        'comment_date',
        'attachment_url',
    ];

    protected $casts = [
        'comment_date' => 'datetime',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }
}
