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
        if ($user->linked_employee && !empty($user->linked_employee->nama_karyawan)) {
            return trim($user->linked_employee->nama_karyawan);
        }
        return trim($user->name ?: ($user->email ?: 'User'));
    }

    /**
     * Dapatkan semua variasi nama / identitas resmi pengguna untuk pencocokan tugas case-insensitive
     */
    protected function getUserCandidateNames($user): array
    {
        if (!$user) return [];
        $rawNames = [];

        if (!empty($user->name)) {
            $rawNames[] = trim($user->name);
        }

        // Cek linked employee jika ada
        if ($user->linked_employee && !empty($user->linked_employee->nama_karyawan)) {
            $rawNames[] = trim($user->linked_employee->nama_karyawan);
        }

        // Cek employee berdasarkan email jika linked_employee belum ter-cache
        if (!empty($user->email)) {
            $rawNames[] = trim($user->email);
            $emp = Employee::whereRaw('LOWER(TRIM(email)) = ?', [strtolower(trim($user->email))])->first();
            if ($emp && !empty($emp->nama_karyawan)) {
                $rawNames[] = trim($emp->nama_karyawan);
            }
        }

        $candidates = [];
        foreach ($rawNames as $name) {
            $name = trim($name);
            if (empty($name)) continue;
            $candidates[] = $name;

            // Variasi jika ada gelar akademik setelah koma (contoh: "ASTRI WAHYUNI,ST" atau "Astri Wahyuni, S.T.")
            if (str_contains($name, ',')) {
                $parts = explode(',', $name);
                $baseName = trim($parts[0]);
                if (!empty($baseName)) {
                    $candidates[] = $baseName;
                    $candidates[] = ucwords(strtolower($baseName));
                    $degreePart = trim($parts[1] ?? '');
                    if (!empty($degreePart)) {
                        $candidates[] = "{$baseName}, {$degreePart}";
                        $candidates[] = "{$baseName},{$degreePart}";
                        $candidates[] = ucwords(strtolower($baseName)) . ", " . strtoupper($degreePart);
                        $candidates[] = ucwords(strtolower($baseName)) . "," . strtoupper($degreePart);
                    }
                }
            }

            // Bersihkan gelar tanpa koma di akhir (misal "Astri Wahyuni ST" atau "Astri Wahyuni SE")
            $cleaned = preg_replace('/\b(ST|S\.T|SE|S\.E|SH|S\.H|SKOM|S\.Kom|MM|M\.M|MBA|M\.B\.A|S\.Pd|SPd|S\.Psi|SPsi)\b/i', '', $name);
            $cleaned = trim(preg_replace('/\s+/', ' ', $cleaned), " ,\t\n\r\0\x0B");
            if (!empty($cleaned) && $cleaned !== $name) {
                $candidates[] = $cleaned;
                $candidates[] = ucwords(strtolower($cleaned));
            }

            // Tambahkan versi Title Case
            $candidates[] = ucwords(strtolower($name));
        }

        // Penyesuaian khusus sinkronisasi akun Astri Wahyuni / astriramelan@gmail.com
        $isAstri = false;
        foreach ($rawNames as $rn) {
            if (stripos($rn, 'astri') !== false && stripos($rn, 'wahyuni') !== false) {
                $isAstri = true;
                break;
            }
        }
        if ($isAstri || (isset($user->email) && strtolower(trim($user->email)) === 'astriramelan@gmail.com')) {
            $candidates[] = 'Astri Wahyuni';
            $candidates[] = 'ASTRI WAHYUNI';
            $candidates[] = 'ASTRI WAHYUNI,ST';
            $candidates[] = 'ASTRI WAHYUNI, ST';
            $candidates[] = 'Astri Wahyuni, ST';
            $candidates[] = 'Astri Wahyuni,ST';
        }

        return array_values(array_unique(array_filter($candidates)));
    }

    /**
     * Tentukan daftar nama bawahan / anggota tim jika pengguna adalah Head / Pimpinan
     */
    protected function getTeamMemberNames($user): array
    {
        if (!$user) return [];
        $userName = $this->getUserOfficialName($user);
        $candidateNames = $this->getUserCandidateNames($user);

        $team = [$userName];
        foreach ($candidateNames as $cn) {
            if (!in_array($cn, $team)) $team[] = $cn;
        }

        // Cari bawahan di tabel employees berdasarkan nama pimpinan atau NIK pimpinan (case-insensitive)
        $lowerPimpinan = array_map('strtolower', $candidateNames);
        $subordinates = Employee::where(function ($q) use ($lowerPimpinan, $user) {
            foreach ($lowerPimpinan as $lp) {
                $q->orWhereRaw('LOWER(TRIM(pimpinan)) = ?', [$lp]);
            }
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
                $ex = trim($ex);
                if (!empty($ex) && !in_array($ex, $team)) {
                    $team[] = $ex;
                }
            }
        }

        return array_values(array_unique(array_filter($team)));
    }

    /**
     * Cek apakah user berhak mengedit atau menghapus tugas (case-insensitive)
     */
    protected function isUserAuthorizedForTask($task, $user): bool
    {
        if (!$user) return false;
        if ($user->isAdmin() || $user->role === 'admin') return true;

        $candidateNames = array_map('strtolower', $this->getUserCandidateNames($user));
        $taskUser = strtolower(trim($task->user ?? ''));
        $taskAssignee = strtolower(trim($task->assignee ?? ''));
        $taskDelegator = strtolower(trim($task->delegator ?? ''));

        return in_array($taskUser, $candidateNames, true)
            || in_array($taskAssignee, $candidateNames, true)
            || in_array($taskDelegator, $candidateNames, true);
    }

    /**
     * Cek apakah user adalah delegator (pimpinan pembuat tugas) untuk approval (case-insensitive)
     */
    protected function isDelegatorForTask($task, $user): bool
    {
        if (!$user) return false;
        if ($user->isAdmin() || $user->role === 'admin') return true;

        $candidateNames = array_map('strtolower', $this->getUserCandidateNames($user));
        $taskDelegator = strtolower(trim($task->delegator ?? ''));

        return in_array($taskDelegator, $candidateNames, true);
    }

    /**
     * Cek apakah user adalah creator atau delegator (case-insensitive)
     */
    protected function isCreatorOrDelegatorForTask($task, $user): bool
    {
        if (!$user) return false;
        if ($user->isAdmin() || $user->role === 'admin') return true;

        $candidateNames = array_map('strtolower', $this->getUserCandidateNames($user));
        $taskUser = strtolower(trim($task->user ?? ''));
        $taskDelegator = strtolower(trim($task->delegator ?? ''));

        return in_array($taskUser, $candidateNames, true)
            || in_array($taskDelegator, $candidateNames, true);
    }

    /**
     * Terapkan filter hak akses hierarkis pada query tasks (case-insensitive)
     */
    protected function applyAccessScope($query, $user, &$isHead = false, &$teamMembers = [])
    {
        $isAdmin = $user && ($user->isAdmin() || $user->role === 'admin');
        if ($isAdmin) {
            return; // Admin dapat melihat seluruh tugas nasional
        }

        $teamMembers = $this->getTeamMemberNames($user);
        $candidateNames = $this->getUserCandidateNames($user);
        $lowerNames = array_values(array_unique(array_map('strtolower', $candidateNames)));
        $isHead = count($teamMembers) > 1 || ($user && method_exists($user, 'isHead') && $user->isHead());

        if ($isHead && count($teamMembers) > 1) {
            $lowerTeam = array_values(array_unique(array_map('strtolower', array_map('trim', $teamMembers))));
            $placeholders = implode(',', array_fill(0, count($lowerTeam), '?'));
            $query->where(function ($q) use ($placeholders, $lowerTeam, $lowerNames, $user) {
                $q->whereRaw("LOWER(TRIM(\"user\")) IN ($placeholders)", $lowerTeam)
                  ->orWhereRaw("LOWER(TRIM(\"assignee\")) IN ($placeholders)", $lowerTeam)
                  ->orWhereRaw("LOWER(TRIM(\"delegator\")) IN ($placeholders)", $lowerTeam);
                foreach ($lowerNames as $lName) {
                    $q->orWhereRaw('LOWER(TRIM("user")) = ?', [$lName])
                      ->orWhereRaw('LOWER(TRIM("assignee")) = ?', [$lName]);
                }
                if (!empty($user->email)) {
                    $q->orWhereRaw('LOWER(TRIM("user")) = ?', [strtolower(trim($user->email))])
                      ->orWhereRaw('LOWER(TRIM("assignee")) = ?', [strtolower(trim($user->email))]);
                }
            });
        } else {
            // Staf / Rekruter / Karyawan biasa: hanya melihat tugas miliknya sendiri (case-insensitive)
            $query->where(function ($q) use ($lowerNames, $user) {
                foreach ($lowerNames as $lName) {
                    $q->orWhereRaw('LOWER(TRIM("user")) = ?', [$lName])
                      ->orWhereRaw('LOWER(TRIM("assignee")) = ?', [$lName])
                      ->orWhereRaw('LOWER(TRIM("delegator")) = ?', [$lName]);
                }
                if (!empty($user->email)) {
                    $q->orWhereRaw('LOWER(TRIM("user")) = ?', [strtolower(trim($user->email))])
                      ->orWhereRaw('LOWER(TRIM("assignee")) = ?', [strtolower(trim($user->email))]);
                }
            });
        }
    }

    /**
     * Query dasar tasks yang sudah difilter sesuai hak akses dan parameter pencarian
     */
    protected function getFilteredTasksQuery(Request $request, $user = null, &$isHead = false, &$teamMembers = [], &$userName = '')
    {
        if (!$user) {
            $user = $this->getCurrentUser();
        }
        $userName = $this->getUserOfficialName($user);

        // Filter parameter dari request
        $search = trim($request->query('search', ''));
        $smartFilter = trim($request->query('smart', 'all')); // all, my, high, overdue
        $filterUser = trim($request->query('user_filter', 'all'));

        // Query Dasar dengan withCount untuk efisiensi memori & performa kilat
        $baseQuery = Task::withCount([
            'subtasks',
            'subtasks as completed_subtasks_count' => function ($q) {
                $q->where('is_completed', true);
            },
            'comments'
        ]);

        $this->applyAccessScope($baseQuery, $user, $isHead, $teamMembers);

        $today = Carbon::today()->toDateString();

        // Terapkan Smart Filter (case-insensitive)
        if ($smartFilter === 'my') {
            $candidateNames = $this->getUserCandidateNames($user);
            $lowerNames = array_values(array_unique(array_map('strtolower', $candidateNames)));
            $baseQuery->where(function ($q) use ($lowerNames) {
                foreach ($lowerNames as $lName) {
                    $q->orWhereRaw('LOWER(TRIM("user")) = ?', [$lName])
                      ->orWhereRaw('LOWER(TRIM("assignee")) = ?', [$lName]);
                }
            });
        } elseif ($smartFilter === 'high') {
            $baseQuery->where('priority', 'High');
        } elseif ($smartFilter === 'overdue') {
            $baseQuery->whereNotIn('status', ['done', 'archived'])
                      ->whereNotNull('due_date')
                      ->where('due_date', '<', $today);
        }

        // Terapkan Filter Karyawan (case-insensitive)
        if (!empty($filterUser) && $filterUser !== 'all') {
            $lowerFilter = strtolower(trim($filterUser));
            $baseQuery->where(function ($q) use ($lowerFilter) {
                $q->whereRaw('LOWER(TRIM("user")) = ?', [$lowerFilter])
                  ->orWhereRaw('LOWER(TRIM("assignee")) = ?', [$lowerFilter])
                  ->orWhereRaw('LOWER(TRIM("delegator")) = ?', [$lowerFilter]);
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

        return $baseQuery;
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
        $isHead = false;
        $teamMembers = [];
        $userName = '';

        // Filter parameter dari request
        $search = trim($request->query('search', ''));
        $smartFilter = trim($request->query('smart', 'all')); // all, my, high, overdue
        $filterUser = trim($request->query('user_filter', 'all'));

        // Query Dasar dengan Scope Hak Akses & withCount
        $baseQuery = $this->getFilteredTasksQuery($request, $user, $isHead, $teamMembers, $userName);

        // Hitung Metrik Statistik Ringkasan Board
        $statsTotal = (clone $baseQuery)->whereNotIn('status', ['archived'])->count();
        $statsTodo = (clone $baseQuery)->where('status', 'todo')->count();
        $statsInProgress = (clone $baseQuery)->where('status', 'inprogress')->count();
        $statsReview = (clone $baseQuery)->where('status', 'review')->count();
        $statsDone = (clone $baseQuery)->where('status', 'done')->count();
        $statsArchived = (clone $baseQuery)->where('status', 'archived')->count();
        
        $today = Carbon::today()->toDateString();
        $statsOverdue = (clone $baseQuery)
            ->whereNotIn('status', ['done', 'archived'])
            ->whereNotNull('due_date')
            ->where('due_date', '<', $today)
            ->count();

        // Ambil Data Kartu per Kolom (Maksimal 25 data awal untuk loading super cepat)
        $perColumn = 25;
        $tasksTodo = (clone $baseQuery)->where('status', 'todo')->orderBy('id', 'desc')->take($perColumn)->get();
        $tasksInProgress = (clone $baseQuery)->where('status', 'inprogress')->orderBy('id', 'desc')->take($perColumn)->get();
        $tasksReview = (clone $baseQuery)->where('status', 'review')->orderBy('id', 'desc')->take($perColumn)->get();
        $tasksDone = (clone $baseQuery)->where('status', 'done')->orderBy('date_completed', 'desc')->orderBy('id', 'desc')->take($perColumn)->get();

        // Filter tanggal khusus arsip
        $archiveDate = trim($request->query('archive_date', ''));
        $archiveDateFrom = trim($request->query('archive_date_from', ''));
        $archiveDateTo = trim($request->query('archive_date_to', ''));

        // Query Dasar Tugas Diarsipkan
        $archiveBaseQuery = (clone $baseQuery)->where('status', 'archived');

        // Daftar tanggal unik yang memiliki arsip tugas (untuk quick-select filter arsip)
        $availableArchiveDates = (clone $archiveBaseQuery)
            ->select(DB::raw('COALESCE(DATE(date_completed), DATE(date_input)) as task_date'), DB::raw('count(*) as total_tasks'))
            ->whereNotNull(DB::raw('COALESCE(DATE(date_completed), DATE(date_input))'))
            ->groupBy('task_date')
            ->orderBy('task_date', 'desc')
            ->get();

        // Terapkan filter tanggal jika dipilih
        $archiveFilteredQuery = clone $archiveBaseQuery;
        if (!empty($archiveDate) && $archiveDate !== 'all') {
            $archiveFilteredQuery->whereRaw('COALESCE(DATE(date_completed), DATE(date_input)) = ?', [$archiveDate]);
        } else {
            if (!empty($archiveDateFrom)) {
                $archiveFilteredQuery->whereRaw('COALESCE(DATE(date_completed), DATE(date_input)) >= ?', [$archiveDateFrom]);
            }
            if (!empty($archiveDateTo)) {
                $archiveFilteredQuery->whereRaw('COALESCE(DATE(date_completed), DATE(date_input)) <= ?', [$archiveDateTo]);
            }
        }

        // Ambil Data Arsip dengan Paginasi 30 data per halaman (diurutkan tanggal terbaru)
        $tasksArchived = $archiveFilteredQuery
            ->orderByRaw('COALESCE(date_completed, date_input) DESC')
            ->orderBy('id', 'desc')
            ->paginate(30)
            ->withQueryString();

        // Grouping data arsip yang diambil per tanggal
        $archivedGrouped = $tasksArchived->getCollection()->groupBy(function ($task) {
            $d = $task->date_completed ?? $task->date_input;
            return $d ? Carbon::parse($d)->toDateString() : 'Tanpa Tanggal';
        });

        // Ambil Daftar Karyawan Unik HANYA Karyawan Inhouse untuk Dropdown Filter
        $inhouseEmployeesQuery = Employee::where('status', 'Aktiv')
            ->where(function ($q) {
                $q->where('tipe_karyawan', 'Inhouse')
                  ->orWhere(DB::raw('LOWER(TRIM(tipe_karyawan))'), 'inhouse');
            });

        $inhouseEmployees = (clone $inhouseEmployeesQuery)
            ->select('id', 'nama_karyawan', 'jabatan_db', 'area', 'divisi', 'entity')
            ->orderBy('nama_karyawan')
            ->get();

        if ($isAdmin) {
            // Ambil semua user dari riwayat tugas (termasuk status archived, baik assignee, user, maupun delegator)
            $taskAssignees = Task::whereNotNull('assignee')->distinct()->pluck('assignee')->filter()->values();
            $taskUsers = Task::whereNotNull('user')->distinct()->pluck('user')->filter()->values();
            $taskDelegators = Task::whereNotNull('delegator')->distinct()->pluck('delegator')->filter()->values();

            $rawNames = $inhouseEmployees->pluck('nama_karyawan')
                ->merge($taskAssignees)
                ->merge($taskUsers)
                ->merge($taskDelegators)
                ->filter()
                ->map(fn($n) => trim($n))
                ->filter(fn($n) => !empty($n));

            // Deduplikasi case-insensitive (utamakan Title Case / Mixed Case jika ada daripada ALL CAPS atau all lowercase)
            $uniqueByName = [];
            foreach ($rawNames as $name) {
                $lower = strtolower($name);
                if (!isset($uniqueByName[$lower])) {
                    $uniqueByName[$lower] = $name;
                } else {
                    $current = $uniqueByName[$lower];
                    $isCurrentAllCaps = (strtoupper($current) === $current && strtolower($current) !== $current);
                    $isCurrentAllLower = (strtolower($current) === $current);
                    $isNewMixed = (strtoupper($name) !== $name && strtolower($name) !== $name);
                    if (($isCurrentAllCaps || $isCurrentAllLower) && $isNewMixed) {
                        $uniqueByName[$lower] = $name;
                    }
                }
            }
            $usersInView = array_values($uniqueByName);
            natcasesort($usersInView);
            $usersInView = array_values($usersInView);

            if (empty($usersInView) && !empty($userName)) {
                $usersInView = [$userName];
            }
        } elseif ($isHead && !empty($teamMembers)) {
            $lowerTeam = array_map('strtolower', array_map('trim', $teamMembers));
            $matchedEmployees = $inhouseEmployees->filter(function ($emp) use ($lowerTeam) {
                return in_array(strtolower(trim($emp->nama_karyawan)), $lowerTeam, true);
            })->pluck('nama_karyawan')->toArray();

            $allTeam = array_merge($matchedEmployees, $teamMembers);
            $uniqueByName = [];
            foreach ($allTeam as $name) {
                $name = trim($name);
                if (empty($name)) continue;
                $lower = strtolower($name);
                if (!isset($uniqueByName[$lower])) {
                    $uniqueByName[$lower] = $name;
                }
            }
            $usersInView = array_values($uniqueByName);
            natcasesort($usersInView);
            $usersInView = array_values($usersInView);

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
            'archivedGrouped',
            'availableArchiveDates',
            'archiveDate',
            'archiveDateFrom',
            'archiveDateTo',
            'statsTotal',
            'statsTodo',
            'statsInProgress',
            'statsReview',
            'statsDone',
            'statsArchived',
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
     * Muat kartu tugas berikutnya pada kolom tertentu (AJAX Load More)
     */
    public function loadMore(Request $request)
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Sesi berakhir.'], 401);
        }

        $column = $request->query('column', 'todo');
        if (!in_array($column, ['todo', 'inprogress', 'review', 'done'])) {
            return response()->json(['success' => false, 'message' => 'Status kolom tidak valid.'], 400);
        }

        $offset = max(0, (int) $request->query('offset', 0));
        $limit = max(1, min(50, (int) $request->query('limit', 25)));

        $baseQuery = $this->getFilteredTasksQuery($request, $user);
        $columnQuery = (clone $baseQuery)->where('status', $column);
        $total = (clone $columnQuery)->count();

        $tasks = (clone $columnQuery)
            ->when($column === 'done', fn($q) => $q->orderBy('date_completed', 'desc'))
            ->orderBy('id', 'desc')
            ->skip($offset)
            ->take($limit)
            ->get();

        $html = '';
        foreach ($tasks as $task) {
            $html .= view('workplan._card', ['task' => $task, 'column' => $column])->render();
        }

        $loadedSoFar = $offset + $tasks->count();
        $hasMore = $loadedSoFar < $total;
        $remaining = max(0, $total - $loadedSoFar);

        return response()->json([
            'success' => true,
            'html' => $html,
            'count' => $tasks->count(),
            'loaded' => $loadedSoFar,
            'total' => $total,
            'has_more' => $hasMore,
            'remaining' => $remaining,
        ]);
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

        // Validasi izin edit: creator, assignee, delegator, atau admin (case-insensitive)
        if (!$this->isUserAuthorizedForTask($task, $user)) {
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
        // HANYA DELEGATOR (Pimpinan pembuat tugas) ATAU ADMIN yang berhak menyetujui tugas menjadi DONE! (case-insensitive)
        if ($oldStatus === 'review' && $newStatus === 'done' && !$this->isDelegatorForTask($task, $user)) {
            return response()->json([
                'success' => false,
                'error' => "Akses ditolak! Hanya Delegator/Pimpinan ({$task->delegator}) atau Administrator yang berhak menyetujui tugas dari status Review menjadi Done."
            ], 403);
        }

        // Update status & waktu
        $task->status = $newStatus;
        if ($newStatus === 'done') {
            $task->date_completed = now();
            // Sinkronisasi otomatis ke tiket Helpdesk jika terhubung
            if (!empty($task->helpdesk_ticket_id)) {
                \App\Services\HelpdeskWorkplanService::syncWorkplanTaskDoneToTicket($task);
            }
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

        // Notifikasi ke Delegator saat tugas dipindahkan ke Review (case-insensitive check)
        $candidateNames = array_map('strtolower', $this->getUserCandidateNames($user));
        $taskDelegatorLower = strtolower(trim($task->delegator ?? ''));
        if ($newStatus === 'review' && !empty($task->delegator) && !in_array($taskDelegatorLower, $candidateNames, true)) {
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

        // Hanya creator, delegator, atau admin yang berhak menghapus tugas (case-insensitive)
        if (!$this->isCreatorOrDelegatorForTask($task, $user)) {
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

        $canEdit = $this->isUserAuthorizedForTask($task, $user);
        $canDelete = $this->isCreatorOrDelegatorForTask($task, $user);
        $canApprove = $this->isDelegatorForTask($task, $user);

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
                'is_due_today' => $task->isDueToday(),
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
        if (!$user) return response()->json(['success' => false, 'notifications' => []]);

        $candidateNames = array_values(array_unique(array_filter(array_map('strtolower', array_map('trim', $this->getUserCandidateNames($user))))));
        $notificationsQuery = TaskNotification::where('is_read', false);
        if (!empty($candidateNames)) {
            $placeholders = implode(',', array_fill(0, count($candidateNames), '?'));
            $notificationsQuery->whereRaw("LOWER(TRIM(user_recipient)) IN ($placeholders)", $candidateNames);
        } else {
            $userName = $this->getUserOfficialName($user);
            $notificationsQuery->where('user_recipient', $userName);
        }

        $notifications = $notificationsQuery
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
        if (!$user) return response()->json(['success' => false], 401);

        $candidateNames = array_values(array_unique(array_filter(array_map('strtolower', array_map('trim', $this->getUserCandidateNames($user))))));
        $notifQuery = TaskNotification::where('id', $id);
        if (!$user->isAdmin() && $user->role !== 'admin') {
            if (!empty($candidateNames)) {
                $placeholders = implode(',', array_fill(0, count($candidateNames), '?'));
                $notifQuery->whereRaw("LOWER(TRIM(user_recipient)) IN ($placeholders)", $candidateNames);
            } else {
                $userName = $this->getUserOfficialName($user);
                $notifQuery->where('user_recipient', $userName);
            }
        }
        $notifQuery->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Salin Laporan Format WhatsApp / Pesan Tim
     */
    public function copyReport(Request $request)
    {
        $user = $this->getCurrentUser();
        $today = Carbon::today()->translatedFormat('l, d F Y');

        $isHead = false;
        $teamMembers = [];
        $officialName = '';
        $baseQuery = $this->getFilteredTasksQuery($request, $user, $isHead, $teamMembers, $officialName);

        // Tentukan nama karyawan yang ditampilkan di header laporan
        $userFilter = trim($request->query('user_filter', 'all'));
        if (!empty($userFilter) && $userFilter !== 'all') {
            $displayName = $userFilter;
        } else {
            $displayName = $officialName ?: $this->getUserOfficialName($user);
        }

        // Ambil data To Do, In Progress, Review persis sesuai query papan Kanban aktif (tanpa pembatasan limit tiruan)
        $todoTasks = (clone $baseQuery)->where('status', 'todo')->orderBy('id', 'desc')->get();
        $inProgressTasks = (clone $baseQuery)->where('status', 'inprogress')->orderBy('id', 'desc')->get();
        $reviewTasks = (clone $baseQuery)->where('status', 'review')->orderBy('id', 'desc')->get();

        // Tugas Selesai: Ambil tugas yang diselesaikan hari ini
        $doneTasks = (clone $baseQuery)->where('status', 'done')
            ->where(function ($q) {
                $q->whereDate('date_completed', Carbon::today())
                  ->orWhere(function ($sub) {
                      $sub->whereNull('date_completed')
                          ->whereDate('updated_at', Carbon::today());
                  });
            })
            ->orderBy('id', 'desc')
            ->get();

        $totalActive = $todoTasks->count() + $inProgressTasks->count() + $reviewTasks->count();

        $text = "🚀 *LAPORAN HARIAN WORK PLAN & TODOLIST*\n";
        $text .= "📅 Tanggal: {$today}\n";
        $text .= "👤 Karyawan: {$displayName}\n";
        $text .= "📊 Total Aktif: {$totalActive} Tugas ({$todoTasks->count()} To Do, {$inProgressTasks->count()} In Progress" . ($reviewTasks->isNotEmpty() ? ", {$reviewTasks->count()} Review" : "") . ")\n";
        $text .= "-----------------------------------------\n\n";

        $text .= "✅ *TUGAS SELESAI HARI INI (DONE: {$doneTasks->count()}):*\n";
        if ($doneTasks->isEmpty()) {
            $text .= "- (Belum ada tugas yang diselesaikan hari ini)\n";
        } else {
            foreach ($doneTasks as $idx => $t) {
                $num = $idx + 1;
                $text .= "{$num}. {$t->title}\n";
            }
        }
        $text .= "\n";

        $text .= "⏳ *SEDANG BERJALAN (IN PROGRESS: {$inProgressTasks->count()}):*\n";
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
            $text .= "📋 *MENUNGGU REVIEW (REVIEW: {$reviewTasks->count()}):*\n";
            foreach ($reviewTasks as $idx => $t) {
                $num = $idx + 1;
                $delegatorInfo = $t->delegator ? " (Delegator: {$t->delegator})" : "";
                $text .= "{$num}. {$t->title}{$delegatorInfo}\n";
            }
            $text .= "\n";
        }

        $text .= "📌 *RENCANA BERIKUTNYA (TO DO: {$todoTasks->count()}):*\n";
        if ($todoTasks->isEmpty()) {
            $text .= "- (Tidak ada)\n";
        } else {
            foreach ($todoTasks as $idx => $t) {
                $num = $idx + 1;
                $dl = $t->due_date ? " [Target: {$t->due_date->format('d/m/Y')}]" : "";
                $text .= "{$num}. {$t->title}{$dl}\n";
            }
        }
        $text .= "\n_Generated via ASystem Work Plan Support System_";

        return response()->json([
            'success' => true,
            'report' => $text,
            'counts' => [
                'total_active' => $totalActive,
                'todo' => $todoTasks->count(),
                'inprogress' => $inProgressTasks->count(),
                'review' => $reviewTasks->count(),
                'done' => $doneTasks->count(),
            ]
        ]);
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

        // Filter (case-insensitive)
        $filterUser = $request->query('user_filter');
        if (!empty($filterUser) && $filterUser !== 'all') {
            $lowerFilter = strtolower(trim($filterUser));
            $query->where(function ($q) use ($lowerFilter) {
                $q->whereRaw('LOWER(TRIM("user")) = ?', [$lowerFilter])
                  ->orWhereRaw('LOWER(TRIM("assignee")) = ?', [$lowerFilter])
                  ->orWhereRaw('LOWER(TRIM("delegator")) = ?', [$lowerFilter]);
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

        // Filter tanggal khusus arsip jika ada
        $archiveDate = trim($request->query('archive_date', ''));
        $archiveDateFrom = trim($request->query('archive_date_from', ''));
        $archiveDateTo = trim($request->query('archive_date_to', ''));
        if (!empty($archiveDate) && $archiveDate !== 'all') {
            $query->whereRaw('COALESCE(DATE(date_completed), DATE(date_input)) = ?', [$archiveDate]);
        } else {
            if (!empty($archiveDateFrom)) {
                $query->whereRaw('COALESCE(DATE(date_completed), DATE(date_input)) >= ?', [$archiveDateFrom]);
            }
            if (!empty($archiveDateTo)) {
                $query->whereRaw('COALESCE(DATE(date_completed), DATE(date_input)) <= ?', [$archiveDateTo]);
            }
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
            $candidateNames = array_values(array_unique(array_filter(array_map('strtolower', array_map('trim', $this->getUserCandidateNames($user))))));
            if (!empty($candidateNames)) {
                $placeholders = implode(',', array_fill(0, count($candidateNames), '?'));
                $query->whereRaw("LOWER(TRIM(\"user\")) IN ($placeholders)", $candidateNames);
            } else {
                $query->where('user', $userName);
            }
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

        $candidateNames = array_map('strtolower', $this->getUserCandidateNames($user));
        $itemUser = strtolower(trim($item->user ?? ''));

        if (!$isAdmin && !in_array($itemUser, $candidateNames, true)) {
            return back()->with('error', 'Akses ditolak! Anda tidak memiliki izin untuk menghapus catatan ini.');
        }

        $item->delete();
        return back()->with('success', 'Catatan kerja harian berhasil dihapus.');
    }
}
