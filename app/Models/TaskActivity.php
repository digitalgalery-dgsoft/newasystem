<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskActivity extends Model
{
    use HasFactory;

    protected $table = 'task_activities';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'task_id',
        'user_actor',
        'action_type',
        'detail_new',
        'detail_old',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    /**
     * Format deskripsi aktivitas yang mudah dibaca manusia
     */
    public function getHumanDescriptionAttribute(): string
    {
        return match ($this->action_type) {
            'status_change' => "Mengubah status dari '" . ucfirst($this->detail_old) . "' ke '" . ucfirst($this->detail_new) . "'",
            'task_edited' => "Memperbarui data tugas",
            'subtask_added' => "Menambahkan checklist: '{$this->detail_new}'",
            'subtask_toggle' => "Menandai checklist '{$this->detail_old}' menjadi " . ($this->detail_new === 'completed' ? 'Selesai' : 'Belum Selesai'),
            'subtask_deleted' => "Menghapus checklist: '{$this->detail_new}'",
            'comment_added' => "Menambahkan komentar baru",
            default => ucfirst(str_replace('_', ' ', $this->action_type)),
        };
    }
}
