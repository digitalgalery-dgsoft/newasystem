<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskNotification extends Model
{
    use HasFactory;

    protected $table = 'task_notifications';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'user_recipient',
        'task_id',
        'notification_type',
        'message',
        'is_read',
        'created_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }
}
