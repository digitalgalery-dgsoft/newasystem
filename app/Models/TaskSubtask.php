<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskSubtask extends Model
{
    use HasFactory;

    protected $table = 'task_subtasks';

    protected $fillable = [
        'id',
        'task_id',
        'subtask_text',
        'is_completed',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }
}
