<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Task extends Model
{
    use HasFactory;

    protected $table = 'tasks';

    protected $fillable = [
        'id',
        'title',
        'description',
        'attachment_url',
        'priority',
        'tags',
        'due_date',
        'status',
        'user',
        'assignee',
        'delegator',
        'date_input',
        'date_completed',
    ];

    protected $casts = [
        'due_date' => 'date',
        'date_input' => 'datetime',
        'date_completed' => 'datetime',
    ];

    /**
     * Subtasks (checklist)
     */
    public function subtasks()
    {
        return $this->hasMany(TaskSubtask::class, 'task_id');
    }

    /**
     * Comments
     */
    public function comments()
    {
        return $this->hasMany(TaskComment::class, 'task_id')->orderBy('comment_date', 'desc');
    }

    /**
     * Activities
     */
    public function activities()
    {
        return $this->hasMany(TaskActivity::class, 'task_id')->orderBy('created_at', 'asc');
    }

    /**
     * Notifications
     */
    public function notifications()
    {
        return $this->hasMany(TaskNotification::class, 'task_id');
    }

    /**
     * Cek apakah tugas terlambat (overdue)
     */
    public function isOverdue(): bool
    {
        if (in_array($this->status, ['done', 'archived'])) {
            return false;
        }

        if (empty($this->due_date)) {
            return false;
        }

        return Carbon::parse($this->due_date)->startOfDay()->isPast();
    }

    /**
     * Persentase progress subtasks
     */
    public function progressPercentage(): int
    {
        $total = $this->subtasks->count();
        if ($total === 0) {
            return $this->status === 'done' || $this->status === 'archived' ? 100 : 0;
        }

        $completed = $this->subtasks->where('is_completed', true)->count();
        return (int) round(($completed / $total) * 100);
    }

    /**
     * Helper avatar URL untuk assignee/user
     */
    public static function getAvatarUrl(?string $name): string
    {
        $name = trim($name ?? '');
        if (empty($name)) {
            return 'https://ui-avatars.com/api/?background=6366f1&color=fff&name=User';
        }

        // Cek foto di employee / user jika ada
        $user = User::where('name', $name)->first();
        if ($user && !empty($user->avatar)) {
            return asset('storage/' . $user->avatar);
        }

        return 'https://ui-avatars.com/api/?background=random&color=fff&name=' . urlencode($name);
    }
}
