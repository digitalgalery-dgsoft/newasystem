<?php

namespace App\Services;

use App\Models\HelpdeskTicket;
use App\Models\HelpdeskTicketReply;
use App\Models\Task;
use App\Models\TaskActivity;
use App\Models\TaskComment;
use App\Models\TaskNotification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class HelpdeskWorkplanService
{
    /**
     * Memetakan prioritas Helpdesk ke format Work Plan (Low, Medium, High, Urgent)
     */
    public static function mapPriority(?string $priority): string
    {
        $p = strtolower(trim((string)$priority));
        if ($p === 'low' || $p === 'rendah') {
            return 'Low';
        } elseif ($p === 'high' || $p === 'tinggi' || $p === 'darurat') {
            return 'High';
        } elseif ($p === 'urgent') {
            return 'Urgent';
        }
        return 'Medium';
    }

    /**
     * Mencari ID Task Work Plan yang terhubung dengan tiket
     */
    public static function findTaskIdByTicket(HelpdeskTicket $ticket): ?int
    {
        if ($ticket->workplan_task_id) {
            $task = Task::find($ticket->workplan_task_id);
            if ($task) return $task->id;
        }

        // Cari berdasarkan kolom helpdesk_ticket_id
        $task = Task::where('helpdesk_ticket_id', $ticket->id)->first();
        if ($task) return $task->id;

        // Fallback pencarian berdasarkan tags (kompatibilitas WPN lama)
        $tagExact = "Ticket Nomor : #{$ticket->id}";
        $task = Task::where('tags', 'like', "%{$tagExact}%")
            ->orWhere('tags', 'like', "%#{$ticket->ticket_number}%")
            ->orderBy('id', 'desc')
            ->first();

        return $task ? $task->id : null;
    }

    /**
     * Sinkronisasi Otomatis Tiket Helpdesk ke Tugas Work Plan pada Step PROGRESS (In Progress)
     * Dipanggil saat Karyawan / Agen merespon tiket masuk pertama kali atau mengklaim tiket.
     */
    public static function syncTicketToWorkplan(HelpdeskTicket $ticket, User $responder): ?Task
    {
        try {
            $existingTaskId = self::findTaskIdByTicket($ticket);
            $now = Carbon::now();

            $creatorName = $ticket->creator ? $ticket->creator->name : "User #{$ticket->user_id}";
            $creatorEmail = $ticket->creator ? $ticket->creator->email : "-";
            $responderName = $responder->name;
            $divisionName = $ticket->division ? $ticket->division->name : "Helpdesk";

            $priority = self::mapPriority($ticket->priority);
            $dueDate = $ticket->due_date ? Carbon::parse($ticket->due_date)->format('Y-m-d') : null;

            // Jika Task sudah ada sebelumnya
            if ($existingTaskId) {
                $task = Task::find($existingTaskId);
                if ($task) {
                    $taskUpdates = [];
                    // Jika sebelumnya assignee kosong atau belum sesuai responder
                    if (empty($task->assignee) || $task->assignee === 'Helpdesk Agent') {
                        $taskUpdates['assignee'] = $responderName;
                    }
                    // Pastikan selalu berada di step inprogress jika masih todo
                    if ($task->status === 'todo') {
                        $taskUpdates['status'] = 'inprogress';
                    }
                    if (!$task->helpdesk_ticket_id) {
                        $taskUpdates['helpdesk_ticket_id'] = $ticket->id;
                    }

                    if (!empty($taskUpdates)) {
                        $task->update($taskUpdates);
                    }

                    if ($ticket->workplan_task_id !== $task->id) {
                        $ticket->update(['workplan_task_id' => $task->id]);
                    }

                    return $task;
                }
            }

            // Susun Deskripsi Tugas yang Rapi & Informatif
            $taskDescription = "=== TIKET HELPDESK #{$ticket->ticket_number} ===\n" .
                "• Pengaju : {$creatorName} ({$creatorEmail})\n" .
                "• Divisi  : {$divisionName}\n" .
                "• Waktu   : " . ($ticket->created_at ? $ticket->created_at->format('d M Y H:i') : $now->format('d M Y H:i')) . " WIB\n" .
                "• Urgensi : {$priority}\n\n" .
                "--- Deskripsi Kendala ---\n" .
                trim($ticket->description);

            $tagLabel = "Helpdesk, Divisi: {$divisionName}, Ticket Nomor : #{$ticket->id}, #{$ticket->ticket_number}";

            // Dapatkan Next ID jika tabel tasks menggunakan auto-increment manual
            $maxId = Task::max('id') ?? 0;
            $nextTaskId = $maxId + 1;

            // INSERT TUGAS BARU LANGSUNG KE STEP 'inprogress' (Progress Kanban)
            $task = Task::create([
                'id' => $nextTaskId,
                'title' => "[{$ticket->ticket_number}] {$ticket->subject}",
                'description' => $taskDescription,
                'priority' => $priority,
                'tags' => $tagLabel,
                'due_date' => $dueDate,
                'status' => 'inprogress', // <-- Step Progress di Kanban Workplan
                'user' => $creatorName,
                'assignee' => $responderName,
                'delegator' => $creatorName,
                'date_input' => $now,
                'helpdesk_ticket_id' => $ticket->id,
            ]);

            // Update relasi pada tiket
            $ticket->update([
                'workplan_task_id' => $task->id,
                'assigned_to' => $ticket->assigned_to ?: $responder->id,
                'status' => ($ticket->status === 'open') ? 'in_progress' : $ticket->status,
            ]);

            // Catat Log Aktivitas di Work Plan
            try {
                $maxActId = TaskActivity::max('id') ?? 0;
                TaskActivity::create([
                    'id' => $maxActId + 1,
                    'task_id' => $task->id,
                    'user_actor' => $responderName,
                    'action_type' => 'ticket_created',
                    'detail_new' => 'inprogress',
                    'detail_old' => "Dibuat otomatis dari respon Helpdesk Tiket #{$ticket->ticket_number}",
                    'created_at' => $now,
                ]);
            } catch (\Throwable $eAct) {
                Log::warning("Gagal mencatat TaskActivity: " . $eAct->getMessage());
            }

            // Buat Notifikasi Tugas Baru di Work Plan
            try {
                $maxNotifId = TaskNotification::max('id') ?? 0;
                TaskNotification::create([
                    'id' => $maxNotifId + 1,
                    'user_recipient' => $responderName,
                    'task_id' => $task->id,
                    'notification_type' => 'ASSIGNED',
                    'message' => "Anda menangani tiket Helpdesk {$ticket->ticket_number}: '{$ticket->subject}'. Tugas otomatis aktif di kolom Progress.",
                    'is_read' => 0,
                    'created_at' => $now,
                ]);
            } catch (\Throwable $eNotif) {
                Log::warning("Gagal membuat TaskNotification: " . $eNotif->getMessage());
            }

            return $task;

        } catch (\Throwable $e) {
            Log::error("Error syncTicketToWorkplan: " . $e->getMessage(), [
                'ticket_id' => $ticket->id,
                'responder_id' => $responder->id,
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Sinkronisasi Balasan Chat Tiket ke Komentar Tugas Work Plan
     */
    public static function syncReplyToWorkplanComment(HelpdeskTicket $ticket, HelpdeskTicketReply $reply): void
    {
        try {
            $taskId = self::findTaskIdByTicket($ticket);
            if (!$taskId) return;

            $senderName = $reply->user ? $reply->user->name : 'User';
            $prefix = $reply->is_internal ? '[🔒 Catatan Internal Helpdesk]' : '[💬 Balasan Helpdesk]';

            $commentText = "{$prefix} {$senderName}:\n" . $reply->message;
            if (!empty($reply->attachment)) {
                $commentText .= "\n📎 Lampiran: " . asset('storage/' . $reply->attachment);
            }

            $maxCommentId = TaskComment::max('id') ?? 0;
            TaskComment::create([
                'id' => $maxCommentId + 1,
                'task_id' => $taskId,
                'user_name' => $senderName,
                'comment_text' => $commentText,
                'comment_date' => Carbon::now(),
            ]);
        } catch (\Throwable $e) {
            Log::warning("Gagal syncReplyToWorkplanComment: " . $e->getMessage());
        }
    }

    /**
     * Sinkronisasi saat Tiket Helpdesk Berstatus Selesai / Ditutup (Closed / Resolved)
     * Mengubah status tugas di Work Plan menjadi 'done'
     */
    public static function syncTicketClosedToWorkplan(HelpdeskTicket $ticket): void
    {
        try {
            $taskId = self::findTaskIdByTicket($ticket);
            if (!$taskId) return;

            $task = Task::find($taskId);
            if (!$task || in_array($task->status, ['done', 'archived'])) return;

            $now = Carbon::now();
            $oldStatus = $task->status;

            $task->update([
                'status' => 'done',
                'date_completed' => $now,
            ]);

            // Catat Log Aktivitas Selesai
            try {
                $maxActId = TaskActivity::max('id') ?? 0;
                TaskActivity::create([
                    'id' => $maxActId + 1,
                    'task_id' => $task->id,
                    'user_actor' => $ticket->assignedAgent ? $ticket->assignedAgent->name : 'System Helpdesk',
                    'action_type' => 'status_change',
                    'detail_new' => 'done',
                    'detail_old' => "Tiket Helpdesk #{$ticket->ticket_number} Selesai / Closed (Status lama: {$oldStatus})",
                    'created_at' => $now,
                ]);
            } catch (\Throwable $eAct) {}

            // Notifikasi ke Delegator / Pembuat Tugas
            try {
                if (!empty($task->delegator)) {
                    $maxNotifId = TaskNotification::max('id') ?? 0;
                    TaskNotification::create([
                        'id' => $maxNotifId + 1,
                        'user_recipient' => $task->delegator,
                        'task_id' => $task->id,
                        'notification_type' => 'STATUS_DONE',
                        'message' => "Tugas untuk Tiket Helpdesk #{$ticket->ticket_number} telah diselesaikan (Done).",
                        'is_read' => 0,
                        'created_at' => $now,
                    ]);
                }
            } catch (\Throwable $eNotif) {}

        } catch (\Throwable $e) {
            Log::error("Error syncTicketClosedToWorkplan: " . $e->getMessage());
        }
    }

    /**
     * Dua Arah (Two-way): Jika kartu tugas di Work Plan dipindahkan ke 'done', selesaikan tiket helpdesk terkait
     */
    public static function syncWorkplanTaskDoneToTicket(Task $task): void
    {
        try {
            if (!$task->helpdesk_ticket_id) return;

            $ticket = HelpdeskTicket::find($task->helpdesk_ticket_id);
            if (!$ticket || $ticket->isClosed()) return;

            $ticket->update([
                'status' => 'resolved',
                'resolved_at' => Carbon::now(),
            ]);

            // Catat Log Tiket
            \App\Models\HelpdeskTicketLog::create([
                'ticket_id' => $ticket->id,
                'user_id' => auth()->id() ?: $ticket->assigned_to,
                'action' => 'Resolved',
                'details' => "Tiket diselesaikan otomatis melalui Work Plan (Tugas dipindahkan ke Done).",
                'created_at' => Carbon::now(),
            ]);
        } catch (\Throwable $e) {
            Log::warning("Gagal syncWorkplanTaskDoneToTicket: " . $e->getMessage());
        }
    }
}
