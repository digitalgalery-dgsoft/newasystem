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
        'helpdesk_ticket_id',
    ];

    protected $casts = [
        'due_date' => 'date',
        'date_input' => 'datetime',
        'date_completed' => 'datetime',
        'helpdesk_ticket_id' => 'integer',
    ];

    /**
     * Tiket Helpdesk terkait (jika dibuat otomatis dari respon tiket)
     */
    public function helpdeskTicket()
    {
        return $this->belongsTo(HelpdeskTicket::class, 'helpdesk_ticket_id');
    }

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
     * Cek apakah tugas terlambat (overdue).
     * Tugas dianggap terlambat hanya jika tanggal deadline sudah terlewat (sebelum hari ini).
     */
    public function isOverdue(): bool
    {
        if (in_array($this->status, ['done', 'archived'])) {
            return false;
        }

        if (empty($this->due_date)) {
            return false;
        }

        return Carbon::parse($this->due_date)->startOfDay()->lt(Carbon::today());
    }

    /**
     * Cek apakah deadline tugas adalah hari ini
     */
    public function isDueToday(): bool
    {
        if (in_array($this->status, ['done', 'archived'])) {
            return false;
        }

        if (empty($this->due_date)) {
            return false;
        }

        return Carbon::parse($this->due_date)->isToday();
    }

    /**
     * Persentase progress subtasks
     */
    public function progressPercentage(): int
    {
        $total = isset($this->subtasks_count) ? (int)$this->subtasks_count : ($this->relationLoaded('subtasks') ? $this->subtasks->count() : 0);
        if ($total === 0) {
            return in_array($this->status, ['done', 'archived']) ? 100 : 0;
        }

        $completed = isset($this->completed_subtasks_count) ? (int)$this->completed_subtasks_count : ($this->relationLoaded('subtasks') ? $this->subtasks->where('is_completed', true)->count() : 0);
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

        // Cek foto di User (case-insensitive)
        $lowerName = strtolower($name);
        $user = User::whereRaw('LOWER(TRIM(name)) = ?', [$lowerName])->first();
        if ($user && !empty($user->avatar)) {
            return asset('storage/' . $user->avatar);
        }

        // Cek foto di Employee jika ada (case-insensitive)
        $employee = Employee::whereRaw('LOWER(TRIM(nama_karyawan)) = ?', [$lowerName])->first();
        if ($employee && !empty($employee->foto)) {
            return asset('storage/' . $employee->foto);
        }

        return 'https://ui-avatars.com/api/?background=random&color=fff&name=' . urlencode($name);
    }
}
