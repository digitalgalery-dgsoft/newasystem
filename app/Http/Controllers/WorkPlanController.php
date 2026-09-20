<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\Task;
use App\Models\TaskSubtask;
use App\Models\TaskComment;
use App\Models\TaskActivity;
use App\Models\TaskNotification;
use App\Models\TaskCategory;
use App\Models\WorkPlanDaily;
use App\Models\Employee;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\WorkPlanXlsxExportService;

class WorkPlanController extends Controller
{
    /**
     * Dapatkan user saat ini dari session/auth
     */
    protected function getCurrentUser()
    {
        return Auth::user();
    }

    /**
     * Dapatkan nama resmi pengguna untuk relasi tasks
     */
    protected function getUserOfficialName($user): string
    {
        if (!$user) return 'Guest';
        return trim($user->name ?: ($user->email ?: 'User'));
    }

    /**
     * Tentukan daftar nama bawahan / anggota tim jika pengguna adalah Head / Pimpinan
     */
    protected function getTeamMemberNames($user): array
    {
        if (!$user) return [];
        $userName = $this->getUserOfficialName($user);

        $team = [$userName];

        // Cari bawahan di tabel employees berdasarkan nama pimpinan atau NIK pimpinan
        $subordinates = Employee::where(function ($q) use ($userName, $user) {
            $q->where('pimpinan', $userName)
              ->orWhere(DB::raw('LOWER(TRIM(pimpinan))'), strtolower($userName));
            if (!empty($user->nik)) {
                $q->orWhere('pimpinan', $user->nik);
            }
        })->where('status', 'Aktiv')->pluck('nama_karyawan')->toArray();

        foreach ($subordinates as $sub) {
            $sub = trim($sub);
            if (!empty($sub) && !in_array($sub, $team)) {
                $team[] = $sub;
            }
        }

        // Tambahkan subordinate identifiers dari method User jika ada
        if (method_exists($user, 'getSubordinateRecruiterIdentifiers')) {
            $extra = $user->getSubordinateRecruiterIdentifiers();
            foreach ($extra as $ex) {
                if (!empty($ex) && !in_array($ex, $team)) {
                    $team[] = $ex;
                }
            }
        }

        return array_values(array_unique(array_filter($team)));
    }

    /**
     * Terapkan filter hak akses hierarkis pada query tasks
     */
    protected function applyAccessScope($query, $user, &$isHead = false, &$teamMembers = [])
    {
        $isAdmin = $user && ($user->isAdmin() || $user->role === 'admin');
        if ($isAdmin) {
            return; // Admin dapat melihat seluruh tugas nasional
        }

        $teamMembers = $this->getTeamMemberNames($user);
        $userName = $this->getUserOfficialName($user);
        $isHead = count($teamMembers) > 1 || ($user && method_exists($user, 'isHead') && $user->isHead());

        if ($isHead && count($teamMembers) > 1) {
            $query->where(function ($q) use ($teamMembers, $userName) {
                $q->whereIn('user', $teamMembers)
                  ->orWhereIn('assignee', $teamMembers)
                  ->orWhereIn('delegator', $teamMembers)
                  ->orWhere('user', $userName)
                  ->orWhere('assignee', $userName);
            });
        } else {
            // Staf / Rekruter / Karyawan biasa: hanya melihat tugas miliknya sendiri
            $query->where(function ($q) use ($userName, $user) {
                $q->where('user', $userName)
                  ->orWhere('assignee', $userName)
                  ->orWhere('delegator', $userName);
                if (!empty($user->email)) {
                    $q->orWhere('user', $user->email)
                      ->orWhere('assignee', $user->email);
                }
            });
        }
    }

    /**
     * Halaman Utama Kanban Board Work Plan & ToDoList
     */
    public function index(Request $request)
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return redirect()->route('login');
        }

        $isAdmin = $user->isAdmin() || $user->role === 'admin';
        $userName = $this->getUserOfficialName($user);
        $isHead = false;
        $teamMembers = [];

        // Filter parameter dari request
        $search = trim($request->query('search', ''));
        $smartFilter = trim($request->query('smart', 'all')); // all, my, high, overdue
        $filterUser = trim($request->query('user_filter', 'all'));

        // Query Dasar dengan Scope Hak Akses
        $baseQuery = Task::with(['subtasks', 'comments']);
        $this->applyAccessScope($baseQuery, $user, $isHead, $teamMembers);

        // Hitung Metrik Statistik Ringkasan Board
        $statsTotal = (clone $baseQuery)->whereNotIn('status', ['archived'])->count();
        $statsTodo = (clone $baseQuery)->where('status', 'todo')->count();
        $statsInProgress = (clone $baseQuery)->where('status', 'inprogress')->count();
        $statsReview = (clone $baseQuery)->where('status', 'review')->count();
        $statsDone = (clone $baseQuery)->where('status', 'done')->count();
        
        $today = Carbon::today()->toDateString();
        $statsOverdue = (clone $baseQuery)
            ->whereNotIn('status', ['done', 'archived'])
            ->whereNotNull('due_date')
            ->where('due_date', '<', $today)
            ->count();

        // Terapkan Smart Filter
        if ($smartFilter === 'my') {
            $baseQuery->where(function ($q) use ($userName) {
                $q->where('user', $userName)
                  ->orWhere('assignee', $userName);
            });
        } elseif ($smartFilter === 'high') {
            $baseQuery->where('priority', 'High');
        } elseif ($smartFilter === 'overdue') {
            $baseQuery->whereNotIn('status', ['done', 'archived'])
                      ->whereNotNull('due_date')
                      ->where('due_date', '<', $today);
        }

        // Terapkan Filter Karyawan
        if (!empty($filterUser) && $filterUser !== 'all') {
            $baseQuery->where(function ($q) use ($filterUser) {
                $q->where('user', $filterUser)
                  ->orWhere('assignee', $filterUser)
                  ->orWhere('delegator', $filterUser);
            });
        }

        // Terapkan Pencarian Kata Kunci
        if (!empty($search)) {
            $baseQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('tags', 'like', "%{$search}%")
                  ->orWhere('assignee', 'like', "%{$search}%");
            });
        }

        // Ambil Data Kartu per Kolom
        $tasksTodo = (clone $baseQuery)->where('status', 'todo')->orderBy('id', 'desc')->get();
        $tasksInProgress = (clone $baseQuery)->where('status', 'inprogress')->orderBy('id', 'desc')->get();
        $tasksReview = (clone $baseQuery)->where('status', 'review')->orderBy('id', 'desc')->get();
        $tasksDone = (clone $baseQuery)->where('status', 'done')->orderBy('date_completed', 'desc')->orderBy('id', 'desc')->get();

        // Ambil Data Arsip (15 data terbaru yang diarsipkan)
        $tasksArchived = (clone $baseQuery)->where('status', 'archived')->orderBy('date_completed', 'desc')->orderBy('id', 'desc')->paginate(15);

        // Ambil Daftar Karyawan Unik HANYA Karyawan Inhouse untuk Dropdown Filter
        $inhouseEmployeesQuery = Employee::where('status', 'Aktiv')
            ->where(function ($q) {
                $q->where('tipe_karyawan', 'Inhouse')
                  ->orWhere(DB::raw('LOWER(TRIM(tipe_karyawan))'), 'inhouse')
                  ->orWhereIn('entity', ['AMK', 'AKP', 'ATK', 'ABO', 'ATB'])
                  ->orWhere('prinsiple', 'like', '%ARINA MULTI%')
                  ->orWhere('prinsiple', 'like', '%ALVA KARYA%')
                  ->orWhere('prinsiple', 'like', '%ANUGRAH TERPERCAYA%')
                  ->orWhere('prinsiple', 'like', '%ABADI BERKAT%')
                  ->orWhere('prinsiple', 'like', '%BINTANG OETAMA%')
                  ->orWhere('prinsiple', 'like', '%TALENTA BERKARYA%')
                  ->orWhere('prinsiple', 'like', '%TRI BERKAH%');
            });

        $inhouseEmployees = (clone $inhouseEmployeesQuery)
            ->select('id', 'nama_karyawan', 'jabatan_db', 'area', 'divisi', 'entity')
            ->orderBy('nama_karyawan')
            ->get();

        if ($isAdmin) {
            $usersInView = $inhouseEmployees->pluck('nama_karyawan')->unique()->values()->toArray();
            if (empty($usersInView) && !empty($userName)) {
                $usersInView = [$userName];
            }
        } elseif ($isHead && !empty($teamMembers)) {
            $usersInView = $inhouseEmployees->whereIn('nama_karyawan', $teamMembers)->pluck('nama_karyawan')->unique()->values()->toArray();
            if (empty($usersInView)) {
                $usersInView = $teamMembers;
            }
        } else {
            $usersInView = [$userName];
        }

        // Ambil Daftar Assignee yang Dikelompokkan per Area HANYA dari Inhouse Aktif
        $employeesGrouped = [];
        foreach ($inhouseEmployees as $emp) {
            $area = !empty($emp->area) ? strtoupper(trim($emp->area)) : 'PUSAT / LAINNYA';
            $employeesGrouped[$area][] = $emp;
        }

        $categories = TaskCategory::orderBy('name')->get();

        return view('workplan.index', compact(
            'user',
            'isAdmin',
            'isHead',
            'userName',
            'tasksTodo',
            'tasksInProgress',
            'tasksReview',
            'tasksDone',
            'tasksArchived',
            'statsTotal',
            'statsTodo',
            'statsInProgress',
            'statsReview',
            'statsDone',
            'statsOverdue',
            'search',
            'smartFilter',
            'filterUser',
            'usersInView',
            'employeesGrouped',
            'categories'
        ));
    }

    /**
     * Tambah Tugas Baru (CREATE)
     */
    public function store(Request $request)
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return response()->json(['success' => false, 'error' => 'Sesi kedaluwarsa.'], 401);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'priority' => 'nullable|in:Low,Medium,High',
            'due_date' => 'nullable|date',
            'assignee' => 'nullable|string|max:255',
            'attachment' => 'nullable|file|max:10240', // max 10MB
        ]);

        $userName = $this->getUserOfficialName($user);
        $title = trim($request->title);
        $description = $request->description;
        $priority = $request->priority ?: 'Medium';
        $dueDate = $request->due_date ?: null;
        $assignee = trim($request->assignee ?: $userName);
        $delegator = $userName;

        // Upload attachment jika ada
        $attachmentUrl = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = 'attach_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('attachments/workplan', $filename, 'public');
            $attachmentUrl = asset('storage/' . $path);
        }

        $task = new Task();
        $task->title = $title;
        $task->description = $description;
        $task->attachment_url = $attachmentUrl;
        $task->priority = $priority;
        $task->due_date = $dueDate;
        $task->status = 'todo';
        $task->user = $userName;
        $task->assignee = $assignee;
        $task->delegator = $delegator;
        $task->date_input = now();
        $task->save();

        // Rekam Log Aktivitas
        TaskActivity::create([
            'task_id' => $task->id,
            'user_actor' => $userName,
            'action_type' => 'task_created',
            'detail_new' => $title,
            'created_at' => now(),
        ]);

        // Kirim Notifikasi Penugasan jika ditugaskan ke orang lain
        if ($assignee !== $userName) {
            TaskNotification::create([
                'user_recipient' => $assignee,
                'task_id' => $task->id,
                'notification_type' => 'ASSIGNED',
                'message' => "Anda ditugaskan tugas baru: '{$title}' oleh {$userName}.",
                'is_read' => false,
                'created_at' => now(),
            ]);
        }

        ActivityLogger::crud('CREATE', 'Work Plan', "Menambahkan tugas work plan baru: '{$title}' (Penanggung Jawab: {$assignee})", $task, [], $task->toArray());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'task' => $task, 'message' => 'Tugas berhasil ditambahkan!']);
        }

        return redirect()->route('workplan.index')->with('success', 'Tugas berhasil ditambahkan!');
    }

    /**
     * Perbarui Tugas (UPDATE)
     */
    public function update(Request $request, $id)
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return response()->json(['success' => false, 'error' => 'Sesi kedaluwarsa.'], 401);
        }

        $task = Task::findOrFail($id);
        $userName = $this->getUserOfficialName($user);
        $isAdmin = $user->isAdmin() || $user->role === 'admin';

        // Validasi izin edit: hanya creator, assignee, delegator, atau admin
        if (!$isAdmin && $task->user !== $userName && $task->assignee !== $userName && $task->delegator !== $userName) {
            return response()->json(['success' => false, 'error' => 'Akses ditolak. Anda tidak berhak mengedit tugas ini.'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'priority' => 'nullable|in:Low,Medium,High',
            'due_date' => 'nullable|date',
            'assignee' => 'nullable|string|max:255',
            'attachment' => 'nullable|file|max:10240',
        ]);

        $oldTaskData = $task->toArray();

        $task->title = trim($request->title);
        $task->description = $request->description;
        $task->priority = $request->priority ?: 'Medium';
        $task->due_date = $request->due_date ?: null;
        if (!empty($request->assignee)) {
            $task->assignee = trim($request->assignee);
        }

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = 'attach_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('attachments/workplan', $filename, 'public');
            $task->attachment_url = asset('storage/' . $path);
        }

        $task->save();

        TaskActivity::create([
            'task_id' => $task->id,
            'user_actor' => $userName,
            'action_type' => 'task_edited',
            'detail_new' => $task->title,
            'created_at' => now(),
        ]);

        ActivityLogger::crud('UPDATE', 'Work Plan', "Memperbarui rincian tugas work plan: '{$task->title}'", $task, $oldTaskData, $task->toArray());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'task' => $task, 'message' => 'Tugas berhasil diperbarui!']);
        }

        return redirect()->route('workplan.index')->with('success', 'Tugas berhasil diperbarui!');
    }

    /**
     * Pindahkan Status Tugas (Kanban Drag and Drop / Move)
     */
    public function moveStatus(Request $request, $id)
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return response()->json(['success' => false, 'error' => 'Sesi kedaluwarsa.'], 401);
        }

        $task = Task::findOrFail($id);
        $userName = $this->getUserOfficialName($user);
        $isAdmin = $user->isAdmin() || $user->role === 'admin';

        $oldStatus = $task->status;
        $newStatus = $request->input('status');

        $validStatuses = ['todo', 'inprogress', 'review', 'done', 'archived'];
        if (!in_array($newStatus, $validStatuses)) {
            return response()->json(['success' => false, 'error' => 'Status target tidak valid.'], 422);
        }

        if ($oldStatus === $newStatus) {
            return response()->json(['success' => true, 'message' => 'Status tidak berubah.']);
        }

        // Aturan Hak Akses:
        // Jika status berpindah dari 'review' ke 'done':
        // HANYA DELEGATOR (Pimpinan pembuat tugas) ATAU ADMIN yang berhak menyetujui tugas menjadi DONE!
        if ($oldStatus === 'review' && $newStatus === 'done' && !$isAdmin && $task->delegator !== $userName) {
            return response()->json([
                'success' => false,
                'error' => "Akses ditolak! Hanya Delegator/Pimpinan ({$task->delegator}) atau Administrator yang berhak menyetujui tugas dari status Review menjadi Done."
            ], 403);
        }

        // Update status & waktu
        $task->status = $newStatus;
        if ($newStatus === 'done') {
            $task->date_completed = now();
        } elseif ($oldStatus === 'done' && $newStatus !== 'done') {
            $task->date_completed = null;
        }
        $task->save();

        // Rekam Log Perubahan Status
        TaskActivity::create([
            'task_id' => $task->id,
            'user_actor' => $userName,
            'action_type' => 'status_change',
            'detail_new' => $newStatus,
            'detail_old' => $oldStatus,
            'created_at' => now(),
        ]);

        ActivityLogger::log('UPDATE', 'Work Plan', "Mengubah status tugas '{$task->title}' dari {$oldStatus} ke {$newStatus}", $task, [
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
        ]);

        // Notifikasi ke Delegator saat tugas dipindahkan ke Review
        if ($newStatus === 'review' && !empty($task->delegator) && $task->delegator !== $userName) {
            TaskNotification::create([
                'user_recipient' => $task->delegator,
                'task_id' => $task->id,
                'notification_type' => 'REVIEW_REQUEST',
                'message' => "Tugas '{$task->title}' siap ditinjau. Diserahkan oleh: {$userName}.",
                'is_read' => false,
                'created_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "Status tugas berhasil diubah ke " . ucfirst($newStatus),
            'oldStatus' => $oldStatus,
            'newStatus' => $newStatus,
        ]);
    }

    /**
     * Arsipkan Tugas
     */
    public function archive($id)
    {
        $task = Task::findOrFail($id);
        $userName = $this->getUserOfficialName($this->getCurrentUser());

        $task->status = 'archived';
        if (empty($task->date_completed)) {
            $task->date_completed = now();
        }
        $task->save();

        TaskActivity::create([
            'task_id' => $task->id,
            'user_actor' => $userName,
            'action_type' => 'status_change',
            'detail_new' => 'archived',
            'detail_old' => 'done',
            'created_at' => now(),
        ]);

        ActivityLogger::log('ARCHIVE', 'Work Plan', "Mengarsipkan tugas work plan: '{$task->title}'", $task);

        return back()->with('success', 'Tugas berhasil dipindahkan ke arsip.');
    }

    /**
     * Pulihkan Tugas dari Arsip
     */
    public function unarchive($id)
    {
        $task = Task::findOrFail($id);
        $userName = $this->getUserOfficialName($this->getCurrentUser());

        $task->status = 'done';
        $task->save();

        TaskActivity::create([
            'task_id' => $task->id,
            'user_actor' => $userName,
            'action_type' => 'status_change',
            'detail_new' => 'done',
            'detail_old' => 'archived',
            'created_at' => now(),
        ]);

        ActivityLogger::log('UPDATE', 'Work Plan', "Memulihkan tugas work plan dari arsip: '{$task->title}'", $task);

        return back()->with('success', 'Tugas dipulihkan dari arsip ke status Done.');
    }

    /**
     * Hapus Tugas
     */
    public function destroy($id)
    {
        $user = $this->getCurrentUser();
        $task = Task::findOrFail($id);
        $isAdmin = $user && ($user->isAdmin() || $user->role === 'admin');
        $userName = $this->getUserOfficialName($user);

        if (!$isAdmin && $task->user !== $userName && $task->delegator !== $userName) {
            return back()->with('error', 'Akses ditolak! Anda tidak memiliki izin untuk menghapus tugas ini.');
        }

        $oldTaskData = $task->toArray();

        // Hapus relasi terkait
        $task->subtasks()->delete();
        $task->comments()->delete();
        $task->activities()->delete();
        $task->notifications()->delete();
        $task->delete();

        ActivityLogger::crud('DELETE', 'Work Plan', "Menghapus tugas work plan: '{$oldTaskData['title']}'", $task, $oldTaskData, []);

        return back()->with('success', 'Tugas berhasil dihapus permanen.');
    }

    /**
     * Tambah Subtask (Checklist Item)
     */
    public function storeSubtask(Request $request, $taskId)
    {
        $user = $this->getCurrentUser();
        $request->validate(['subtask_text' => 'required|string|max:500']);

        $task = Task::findOrFail($taskId);
        $userName = $this->getUserOfficialName($user);

        $subtask = TaskSubtask::create([
            'task_id' => $task->id,
            'subtask_text' => trim($request->subtask_text),
            'is_completed' => false,
            'created_at' => now(),
        ]);

        TaskActivity::create([
            'task_id' => $task->id,
            'user_actor' => $userName,
            'action_type' => 'subtask_added',
            'detail_new' => $subtask->subtask_text,
            'created_at' => now(),
        ]);

        return response()->json(['success' => true, 'subtask' => $subtask]);
    }

    /**
     * Toggle Selesai/Belum Subtask
     */
    public function toggleSubtask(Request $request, $id)
    {
        $user = $this->getCurrentUser();
        $subtask = TaskSubtask::findOrFail($id);
        $userName = $this->getUserOfficialName($user);

        $isCompleted = (bool) $request->input('is_completed', !$subtask->is_completed);
        $subtask->is_completed = $isCompleted;
        $subtask->save();

        TaskActivity::create([
            'task_id' => $subtask->task_id,
            'user_actor' => $userName,
            'action_type' => 'subtask_toggle',
            'detail_new' => $isCompleted ? 'completed' : 'uncompleted',
            'detail_old' => $subtask->subtask_text,
            'created_at' => now(),
        ]);

        return response()->json(['success' => true, 'is_completed' => $isCompleted]);
    }

    /**
     * Hapus Subtask
     */
    public function deleteSubtask($id)
    {
        $user = $this->getCurrentUser();
        $subtask = TaskSubtask::findOrFail($id);
        $userName = $this->getUserOfficialName($user);

        TaskActivity::create([
            'task_id' => $subtask->task_id,
            'user_actor' => $userName,
            'action_type' => 'subtask_deleted',
            'detail_new' => $subtask->subtask_text,
            'created_at' => now(),
        ]);

        $subtask->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Ambil Detail Lengkap Tugas beserta Subtasks, Comments, dan Permissions
     */
    public function getDetails($id)
    {
        $user = $this->getCurrentUser();
        $userName = $this->getUserOfficialName($user);
        $isAdmin = $user && ($user->isAdmin() || $user->role === 'admin');

        $task = Task::with(['subtasks' => function ($q) {
            $q->orderBy('id', 'asc');
        }])->findOrFail($id);

        $canEdit = $isAdmin || $task->user === $userName || $task->assignee === $userName || $task->delegator === $userName;
        $canDelete = $isAdmin || $task->user === $userName || $task->delegator === $userName;
        $canApprove = $isAdmin || $task->delegator === $userName;

        return response()->json([
            'success' => true,
            'task' => [
                'id' => $task->id,
                'title' => $task->title,
                'description' => $task->description ?? '',
                'priority' => $task->priority,
                'status' => $task->status,
                'user' => $task->user ?: '-',
                'assignee' => $task->assignee ?: '-',
                'delegator' => $task->delegator ?: '-',
                'due_date' => $task->due_date ? $task->due_date->format('Y-m-d') : null,
                'due_date_formatted' => $task->due_date ? $task->due_date->format('d M Y') : '-',
                'is_overdue' => $task->isOverdue(),
                'attachment_url' => $task->attachment_url,
                'date_input' => $task->date_input ? $task->date_input->format('d M Y H:i') : '-',
                'date_completed' => $task->date_completed ? $task->date_completed->format('d M Y H:i') : '-',
                'progress' => $task->progressPercentage(),
                'subtasks' => $task->subtasks->map(function ($s) {
                    return [
                        'id' => $s->id,
                        'subtask_text' => $s->subtask_text,
                        'is_completed' => (bool) $s->is_completed,
                    ];
                }),
            ],
            'can_edit' => $canEdit,
            'can_delete' => $canDelete,
            'can_approve' => $canApprove,
            'current_user' => $userName,
        ]);
    }

    /**
     * Ambil Komentar Tugas
     */
    public function getComments($taskId)
    {
        $comments = TaskComment::where('task_id', $taskId)
            ->orderBy('comment_date', 'asc')
            ->get()
            ->map(function ($c) {
                return [
                    'id' => $c->id,
                    'user_comment' => $c->user_comment,
                    'comment_text' => $c->comment_text,
                    'comment_date' => $c->comment_date ? $c->comment_date->format('d M Y H:i') : '-',
                    'attachment_url' => $c->attachment_url,
                    'avatar_url' => Task::getAvatarUrl($c->user_comment),
                ];
            });

        return response()->json(['success' => true, 'comments' => $comments]);
    }

    /**
     * Tambah Komentar pada Tugas
     */
    public function storeComment(Request $request, $taskId)
    {
        $user = $this->getCurrentUser();
        $task = Task::findOrFail($taskId);
        $userName = $this->getUserOfficialName($user);

        $request->validate([
            'comment_text' => 'required_without:attachment|string|nullable',
            'attachment' => 'nullable|file|max:10240',
        ]);

        $attachmentUrl = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = 'comment_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('attachments/comments', $filename, 'public');
            $attachmentUrl = asset('storage/' . $path);
        }

        $commentText = trim($request->comment_text ?: '');

        $comment = TaskComment::create([
            'task_id' => $task->id,
            'user_comment' => $userName,
            'comment_text' => $commentText,
            'comment_date' => now(),
            'attachment_url' => $attachmentUrl,
        ]);

        TaskActivity::create([
            'task_id' => $task->id,
            'user_actor' => $userName,
            'action_type' => 'comment_added',
            'detail_new' => substr($commentText, 0, 100),
            'created_at' => now(),
        ]);

        // Cek Mention @Nama
        if (preg_match_all('/@([A-Za-z\s]+)/', $commentText, $matches)) {
            $mentioned = array_unique($matches[1]);
            $validEmployees = Employee::whereIn('nama_karyawan', $mentioned)->pluck('nama_karyawan')->toArray();

            foreach ($validEmployees as $recipient) {
                if ($recipient !== $userName) {
                    TaskNotification::create([
                        'user_recipient' => $recipient,
                        'task_id' => $task->id,
                        'notification_type' => 'MENTION',
                        'message' => "Anda disebut (@{$recipient}) dalam komentar pada tugas #{$task->id} oleh {$userName}.",
                        'is_read' => false,
                        'created_at' => now(),
                    ]);
                }
            }
        }

        return response()->json([
            'success' => true,
            'comment' => [
                'id' => $comment->id,
                'user_comment' => $comment->user_comment,
                'comment_text' => $comment->comment_text,
                'comment_date' => $comment->comment_date->format('d M Y H:i'),
                'attachment_url' => $comment->attachment_url,
                'avatar_url' => Task::getAvatarUrl($comment->user_comment),
            ]
        ]);
    }

    /**
     * Ambil Jejak Riwayat Aktivitas Tugas
     */
    public function getActivities($taskId)
    {
        $activities = TaskActivity::where('task_id', $taskId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($a) {
                return [
                    'id' => $a->id,
                    'user_actor' => $a->user_actor,
                    'action_type' => $a->action_type,
                    'description' => $a->human_description,
                    'created_at' => $a->created_at ? $a->created_at->format('d M Y H:i') : '-',
                    'avatar_url' => Task::getAvatarUrl($a->user_actor),
                ];
            });

        return response()->json(['success' => true, 'activities' => $activities]);
    }

    /**
     * Ambil Notifikasi Pengguna
     */
    public function getNotifications()
    {
        $user = $this->getCurrentUser();
        $userName = $this->getUserOfficialName($user);

        $notifications = TaskNotification::where('user_recipient', $userName)
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($n) {
                return [
                    'id' => $n->id,
                    'task_id' => $n->task_id,
                    'notification_type' => $n->notification_type,
                    'message' => $n->message,
                    'created_at' => $n->created_at ? $n->created_at->diffForHumans() : '-',
                ];
            });

        return response()->json(['success' => true, 'notifications' => $notifications]);
    }

    /**
     * Tandai Notifikasi Dibaca
     */
    public function markNotificationRead($id)
    {
        $user = $this->getCurrentUser();
        $userName = $this->getUserOfficialName($user);

        TaskNotification::where('id', $id)
            ->where('user_recipient', $userName)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Salin Laporan Format WhatsApp / Pesan Tim
     */
    public function copyReport(Request $request)
    {
        $user = $this->getCurrentUser();
        $userName = $this->getUserOfficialName($user);
        $today = Carbon::today()->translatedFormat('l, d F Y');

        $query = Task::query();
        $this->applyAccessScope($query, $user);

        // Hanya tugas user yang bersangkutan jika bukan admin
        $query->where(function ($q) use ($userName) {
            $q->where('user', $userName)
              ->orWhere('assignee', $userName);
        });

        $doneTasks = (clone $query)->where('status', 'done')->whereDate('date_completed', Carbon::today())->get();
        if ($doneTasks->isEmpty()) {
            $doneTasks = (clone $query)->where('status', 'done')->limit(5)->get();
        }

        $inProgressTasks = (clone $query)->where('status', 'inprogress')->get();
        $reviewTasks = (clone $query)->where('status', 'review')->get();
        $todoTasks = (clone $query)->where('status', 'todo')->limit(5)->get();

        $text = "🚀 *LAPORAN HARIAN WORK PLAN & TODOLIST*\n";
        $text .= "📅 Tanggal: {$today}\n";
        $text .= "👤 Karyawan: {$userName}\n";
        $text .= "-----------------------------------------\n\n";

        $text .= "✅ *TUGAS SELESAI (DONE):*\n";
        if ($doneTasks->isEmpty()) {
            $text .= "- (Tidak ada tugas yang diselesaikan hari ini)\n";
        } else {
            foreach ($doneTasks as $idx => $t) {
                $num = $idx + 1;
                $text .= "{$num}. {$t->title}\n";
            }
        }
        $text .= "\n";

        $text .= "⏳ *SEDANG BERJALAN (IN PROGRESS):*\n";
        if ($inProgressTasks->isEmpty()) {
            $text .= "- (Tidak ada)\n";
        } else {
            foreach ($inProgressTasks as $idx => $t) {
                $num = $idx + 1;
                $dl = $t->due_date ? " [Target: {$t->due_date->format('d/m/Y')}]" : "";
                $text .= "{$num}. {$t->title}{$dl}\n";
            }
        }
        $text .= "\n";

        if ($reviewTasks->isNotEmpty()) {
            $text .= "📋 *MENUNGGU REVIEW (REVIEW):*\n";
            foreach ($reviewTasks as $idx => $t) {
                $num = $idx + 1;
                $text .= "{$num}. {$t->title} (Delegator: {$t->delegator})\n";
            }
            $text .= "\n";
        }

        $text .= "📌 *RENCANA BERIKUTNYA (TO DO):*\n";
        if ($todoTasks->isEmpty()) {
            $text .= "- (Tidak ada)\n";
        } else {
            foreach ($todoTasks as $idx => $t) {
                $num = $idx + 1;
                $text .= "{$num}. {$t->title}\n";
            }
        }
        $text .= "\n_Generated via ASystem Work Plan Support System_";

        return response()->json(['success' => true, 'report' => $text]);
    }

    /**
     * Export Excel (.xlsx) Laporan Tugas
     */
    public function exportExcel(Request $request)
    {
        $user = $this->getCurrentUser();
        $isAdmin = $user && ($user->isAdmin() || $user->role === 'admin');

        $query = Task::with(['subtasks']);
        $this->applyAccessScope($query, $user);

        // Filter
        $filterUser = $request->query('user_filter');
        if (!empty($filterUser) && $filterUser !== 'all') {
            $query->where(function ($q) use ($filterUser) {
                $q->where('user', $filterUser)
                  ->orWhere('assignee', $filterUser)
                  ->orWhere('delegator', $filterUser);
            });
        }

        $filterSmart = $request->query('smart');
        $today = Carbon::today()->toDateString();
        if ($filterSmart === 'high') {
            $query->where('priority', 'High');
        } elseif ($filterSmart === 'overdue') {
            $query->whereNotIn('status', ['done', 'archived'])
                  ->whereNotNull('due_date')
                  ->where('due_date', '<', $today);
        }

        $filterStatus = $request->query('status');
        if (!empty($filterStatus) && $filterStatus !== 'all') {
            $query->where('status', $filterStatus);
        }

        $tasks = $query->orderBy('id', 'desc')->get();

        $meta = [
            'user_filter' => $filterUser,
            'smart' => $filterSmart,
            'status' => $filterStatus,
            'exporter_name' => $user ? $this->getUserOfficialName($user) : 'Administrator',
        ];

        $filePath = WorkPlanXlsxExportService::generateXlsx($tasks, $meta);
        $fileName = 'WorkPlan_ToDoList_' . date('Ymd_His') . '.xlsx';

        ActivityLogger::export('Work Plan', "Mengekspor daftar tugas work plan ke Excel (" . count($tasks) . " baris)", [
            'total_rows' => count($tasks),
            'status' => $filterStatus,
            'smart' => $filterSmart,
            'user_filter' => $filterUser,
        ]);

        return response()->download($filePath, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
            'Pragma' => 'public',
        ])->deleteFileAfterSend(true);
    }


    /**
     * Halaman Daily Work Activity Log (tb_workplan)
     */
    public function daily(Request $request)
    {
        $user = $this->getCurrentUser();
        if (!$user) return redirect()->route('login');

        $isAdmin = $user->isAdmin() || $user->role === 'admin';
        $userName = $this->getUserOfficialName($user);

        // Ambil data karyawan user untuk default divisi
        $employee = Employee::where('nama_karyawan', $userName)
            ->orWhere('email', $user->email)
            ->first();
        $userDivisi = $employee ? ($employee->divisi ?: ($employee->area ?: 'Umum')) : 'Umum';

        $query = WorkPlanDaily::query();
        if (!$isAdmin) {
            $query->where('user', $userName);
        }

        $search = trim($request->query('search', ''));
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('aktivitas', 'like', "%{$search}%")
                  ->orWhere('divisi', 'like', "%{$search}%")
                  ->orWhere('kendala', 'like', "%{$search}%")
                  ->orWhere('user', 'like', "%{$search}%");
            });
        }

        $filterDivisi = trim($request->query('divisi', ''));
        if (!empty($filterDivisi) && $filterDivisi !== 'all') {
            $query->where('divisi', $filterDivisi);
        }

        $filterTanggal = trim($request->query('tanggal', ''));
        if (!empty($filterTanggal)) {
            $query->whereDate('tanggal', $filterTanggal);
        }

        $totalLogs = (clone $query)->count();
        $todayLogs = (clone $query)->whereDate('tanggal', Carbon::today())->count();

        $dailyLogs = $query->orderBy('tanggal', 'desc')->orderBy('kode', 'desc')->paginate(20)->withQueryString();

        // Daftar divisi unik untuk opsi filter & form
        $distinctDivisions = WorkPlanDaily::whereNotNull('divisi')
            ->where('divisi', '!=', '')
            ->distinct()
            ->pluck('divisi')
            ->toArray();

        if ($employee && $employee->divisi && !in_array($employee->divisi, $distinctDivisions)) {
            $distinctDivisions[] = $employee->divisi;
        }
        sort($distinctDivisions);

        return view('workplan.daily', compact(
            'user', 
            'isAdmin', 
            'userName', 
            'dailyLogs', 
            'search', 
            'filterDivisi', 
            'filterTanggal', 
            'totalLogs', 
            'todayLogs', 
            'distinctDivisions', 
            'userDivisi'
        ));
    }

    /**
     * Simpan Catatan Harian (tb_workplan)
     */
    public function storeDaily(Request $request)
    {
        $user = $this->getCurrentUser();
        $userName = $this->getUserOfficialName($user);

        $request->validate([
            'tanggal' => 'required|date',
            'aktivitas' => 'required|string|max:500',
            'divisi' => 'required|string|max:100',
            'kendala' => 'nullable|string|max:500',
        ]);

        WorkPlanDaily::create([
            'tanggal' => $request->tanggal,
            'aktivitas' => trim($request->aktivitas),
            'divisi' => trim($request->divisi),
            'kendala' => trim($request->kendala ?: 'Belum ada kendala'),
            'user' => $userName,
            'waktu' => now(),
        ]);

        return redirect()->route('workplan.daily')->with('success', 'Catatan aktivitas kerja harian berhasil disimpan!');
    }

    /**
     * Hapus Catatan Harian (tb_workplan)
     */
    public function destroyDaily($kode)
    {
        $user = $this->getCurrentUser();
        $item = WorkPlanDaily::findOrFail($kode);
        $isAdmin = $user && ($user->isAdmin() || $user->role === 'admin');
        $userName = $this->getUserOfficialName($user);

        if (!$isAdmin && $item->user !== $userName) {
            return back()->with('error', 'Akses ditolak! Anda tidak memiliki izin untuk menghapus catatan ini.');
        }

        $item->delete();
        return back()->with('success', 'Catatan kerja harian berhasil dihapus.');
    }
}
