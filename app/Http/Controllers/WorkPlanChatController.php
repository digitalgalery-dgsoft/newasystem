<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\WpChatGroup;
use App\Models\WpChatGroupMember;
use App\Models\WpChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WorkPlanChatController extends Controller
{
    protected static array $avatarCache = [];

    protected function getCurrentUser()
    {
        return Auth::user();
    }

    protected function getUserOfficialName($user): string
    {
        if (!$user) return 'Guest';
        return trim($user->name ?: ($user->email ?: 'User'));
    }

    /**
     * Resolusi URL avatar foto profil pengirim chat
     */
    public static function getSenderAvatarUrl(?string $senderName): string
    {
        $name = trim($senderName ?? '');
        if (empty($name) || strtolower($name) === 'sistem') {
            return "https://ui-avatars.com/api/?name=Sistem&background=64748b&color=fff&size=128&bold=true";
        }

        $lower = strtolower($name);
        if (isset(self::$avatarCache[$lower])) {
            return self::$avatarCache[$lower];
        }

        // 1. Cek User berdasarkan nama
        $user = \App\Models\User::whereRaw('LOWER(TRIM(name)) = ?', [$lower])->first();
        if ($user && !empty($user->avatar_url)) {
            self::$avatarCache[$lower] = $user->avatar_url;
            return self::$avatarCache[$lower];
        }

        // 2. Cek Employee berdasarkan nama_karyawan
        $employee = Employee::whereRaw('LOWER(TRIM(nama_karyawan)) = ?', [$lower])->first();
        if ($employee && !empty($employee->foto)) {
            $fotoClean = ltrim($employee->foto, '/\\');
            if (file_exists(public_path($fotoClean))) {
                $url = asset($fotoClean);
                self::$avatarCache[$lower] = $url;
                return $url;
            }
            if (file_exists(public_path('uploads/avatars/' . basename($fotoClean)))) {
                $url = asset('uploads/avatars/' . basename($fotoClean));
                self::$avatarCache[$lower] = $url;
                return $url;
            }
            if (file_exists(public_path('lampiran/' . basename($fotoClean)))) {
                $url = asset('lampiran/' . basename($fotoClean));
                self::$avatarCache[$lower] = $url;
                return $url;
            }
        }

        // 3. Fallback ke UI-Avatars dinamis dengan palet warna khusus
        $bgColors = ['0F52BA', '059669', 'D97706', '7C3AED', 'DC2626', '4F46E5', '0891B2', 'C026D3'];
        $hash = abs(crc32($name)) % count($bgColors);
        $bg = $bgColors[$hash];
        $url = "https://ui-avatars.com/api/?name=" . urlencode($name) . "&background={$bg}&color=fff&size=128&bold=true";

        self::$avatarCache[$lower] = $url;
        return $url;
    }

    /**
     * Halaman Utama WhatsApp Groups Chat
     */
    public function index(Request $request)
    {
        $user = $this->getCurrentUser();
        $userName = $this->getUserOfficialName($user);
        $isAdmin = $user && ($user->isAdmin() || $user->role === 'admin');

        // Ambil group tempat user menjadi anggota, atau semua jika admin
        $groupsQuery = WpChatGroup::where('is_active', true)
            ->with(['members', 'latestMessage']);

        if (!$isAdmin) {
            $groupsQuery->whereHas('members', function ($q) use ($userName) {
                $q->where('user_name', $userName)
                  ->orWhere(DB::raw('LOWER(TRIM(user_name))'), strtolower($userName));
            });
        }

        $groups = $groupsQuery->get()->sortByDesc(function ($g) {
            return $g->latestMessage ? $g->latestMessage->created_at : $g->created_at;
        })->values();

        // Tentukan group aktif
        $activeGroupId = $request->query('group_id');
        $activeGroup = null;

        if ($activeGroupId) {
            $activeGroup = $groups->firstWhere('id', (int) $activeGroupId);
            if (!$activeGroup) {
                $activeGroup = WpChatGroup::with('members')->find($activeGroupId);
            }
        }

        if (!$activeGroup && $groups->isNotEmpty()) {
            $activeGroup = $groups->first();
        }

        // Ambil riwayat pesan untuk group aktif
        $messages = collect();
        if ($activeGroup) {
            $messages = $activeGroup->messages()
                ->orderBy('created_at', 'asc')
                ->take(150)
                ->get();

            // Update last_read_at
            WpChatGroupMember::where('group_id', $activeGroup->id)
                ->where('user_name', $userName)
                ->update(['last_read_at' => now()]);
        }

        // Ambil daftar karyawan Inhouse aktif (hanya tipe Inhouse & status Aktiv)
        $inhouseEmployees = Employee::where('status', 'Aktiv')
            ->where(function ($q) {
                $q->where('tipe_karyawan', 'Inhouse')
                  ->orWhere(DB::raw('LOWER(TRIM(tipe_karyawan))'), 'inhouse');
            })
            ->select('id', 'nama_karyawan', 'jabatan', 'area', 'divisi')
            ->orderBy('nama_karyawan')
            ->get();

        // Gabungkan juga User portal aktif agar semua personil yang terdaftar selalu tersedia di dropdown
        $existingNames = $inhouseEmployees->pluck('nama_karyawan')->map(fn($n) => strtolower(trim($n)))->toArray();
        $systemUsers = \App\Models\User::all();
        foreach ($systemUsers as $u) {
            $uName = trim($u->name ?: ($u->email ?: ''));
            if (!empty($uName) && !in_array(strtolower($uName), $existingNames)) {
                $inhouseEmployees->push((object)[
                    'id' => 'u_' . $u->id,
                    'nama_karyawan' => $uName,
                    'jabatan' => $u->role ? ucfirst($u->role) : 'User Portal',
                    'area' => 'Pusat',
                    'divisi' => 'Operasional'
                ]);
                $existingNames[] = strtolower($uName);
            }
        }
        $inhouseEmployees = $inhouseEmployees->sortBy('nama_karyawan')->values();

        // Format JSON payload untuk frontend Alpine.js
        $groupsJson = $groups->map(function ($g) {
            return [
                'id' => $g->id,
                'name' => $g->name,
                'initials' => $g->initials,
                'avatar_color' => $g->avatar_color,
                'member_count' => $g->members->count(),
                'latest_message' => $g->latestMessage ? [
                    'text' => $g->latestMessage->message_text,
                    'sender' => $g->latestMessage->user_sender,
                    'time' => $g->latestMessage->formatted_time,
                ] : null,
            ];
        })->values();

        $messagesJson = $messages->map(function ($m) use ($userName) {
            return [
                'id' => $m->id,
                'user_sender' => $m->user_sender,
                'sender_avatar' => self::getSenderAvatarUrl($m->user_sender),
                'is_me' => (trim(strtolower($m->user_sender)) === trim(strtolower($userName))),
                'is_system' => ($m->user_sender === 'Sistem'),
                'message_text' => $m->message_text,
                'time' => $m->formatted_time,
                'date' => $m->formatted_date,
                'created_at' => $m->created_at ? $m->created_at->toISOString() : '',
            ];
        })->values();

        return view('workplan.chat', compact(
            'user',
            'userName',
            'isAdmin',
            'groups',
            'groupsJson',
            'activeGroup',
            'messages',
            'messagesJson',
            'inhouseEmployees'
        ));
    }

    /**
     * Buat Group Chat Baru
     */
    public function storeGroup(Request $request)
    {
        $user = $this->getCurrentUser();
        $userName = $this->getUserOfficialName($user);

        $request->validate([
            'name' => 'required|string|max:120',
            'description' => 'nullable|string|max:500',
            'members' => 'nullable|array',
        ]);

        $colors = ['#10b981', '#0F52BA', '#8b5cf6', '#f59e0b', '#ec4899', '#06b6d4', '#14b8a6', '#f97316', '#6366f1'];
        $color = $colors[array_rand($colors)];

        $group = WpChatGroup::create([
            'name' => trim($request->name),
            'description' => $request->description ? trim($request->description) : null,
            'avatar_color' => $color,
            'created_by' => $userName,
            'created_by_id' => $user ? $user->id : null,
            'is_active' => true,
        ]);

        // Tambahkan creator sebagai Admin Group
        WpChatGroupMember::create([
            'group_id' => $group->id,
            'user_name' => $userName,
            'user_id' => $user ? $user->id : null,
            'role' => 'admin',
            'joined_at' => now(),
            'last_read_at' => now(),
        ]);

        // Tambahkan anggota terpilih
        if ($request->has('members') && is_array($request->members)) {
            foreach ($request->members as $memberName) {
                $memberName = trim($memberName);
                if (!empty($memberName) && $memberName !== $userName) {
                    WpChatGroupMember::firstOrCreate([
                        'group_id' => $group->id,
                        'user_name' => $memberName,
                    ], [
                        'role' => 'member',
                        'joined_at' => now(),
                    ]);
                }
            }
        }

        // Pesan sistem pembuka group
        WpChatMessage::create([
            'group_id' => $group->id,
            'user_sender' => 'Sistem',
            'message_text' => "Group '{$group->name}' dibuat oleh {$userName}.",
            'created_at' => now(),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'group' => $group, 'redirect' => route('workplan.chat', ['group_id' => $group->id])]);
        }

        return redirect()->route('workplan.chat', ['group_id' => $group->id])
            ->with('success', "Group '{$group->name}' berhasil dibuat!");
    }

    /**
     * Tambahkan Anggota Baru ke Group
     */
    public function addMembers(Request $request, $groupId)
    {
        $user = $this->getCurrentUser();
        $userName = $this->getUserOfficialName($user);
        $group = WpChatGroup::findOrFail($groupId);

        $request->validate([
            'members' => 'required|array|min:1',
        ]);

        $addedNames = [];
        foreach ($request->members as $memberName) {
            $memberName = trim($memberName);
            if (!empty($memberName)) {
                $exists = WpChatGroupMember::where('group_id', $groupId)
                    ->where(function ($q) use ($memberName) {
                        $q->where('user_name', $memberName)
                          ->orWhere(DB::raw('LOWER(TRIM(user_name))'), strtolower($memberName));
                    })->exists();

                if (!$exists) {
                    WpChatGroupMember::create([
                        'group_id' => $groupId,
                        'user_name' => $memberName,
                        'role' => 'member',
                        'joined_at' => now(),
                    ]);
                    $addedNames[] = $memberName;
                }
            }
        }

        if (!empty($addedNames)) {
            WpChatMessage::create([
                'group_id' => $groupId,
                'user_sender' => 'Sistem',
                'message_text' => "{$userName} menambahkan " . implode(', ', $addedNames) . " ke dalam group.",
                'created_at' => now(),
            ]);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => count($addedNames) . ' anggota berhasil ditambahkan ke group.',
                'added' => $addedNames,
            ]);
        }

        return redirect()->route('workplan.chat', ['group_id' => $groupId])
            ->with('success', count($addedNames) . ' anggota berhasil ditambahkan ke group.');
    }

    /**
     * Polling Pesan Real-time (JSON API)
     */
    public function getMessages(Request $request, $groupId)
    {
        $user = $this->getCurrentUser();
        $userName = $this->getUserOfficialName($user);
        $isAdmin = $user && ($user->isAdmin() || $user->role === 'admin');

        $group = WpChatGroup::findOrFail($groupId);

        // Verifikasi membership
        if (!$isAdmin) {
            $isMember = WpChatGroupMember::where('group_id', $groupId)
                ->where(function ($q) use ($userName) {
                    $q->where('user_name', $userName)
                      ->orWhere(DB::raw('LOWER(TRIM(user_name))'), strtolower($userName));
                })->exists();

            if (!$isMember) {
                return response()->json(['success' => false, 'error' => 'Akses ditolak.'], 403);
            }
        }

        $lastId = (int) $request->query('last_id', 0);
        $query = WpChatMessage::where('group_id', $groupId);

        if ($lastId > 0) {
            $query->where('id', '>', $lastId);
        } else {
            $query->take(150);
        }

        $messages = $query->orderBy('id', 'asc')->get()->map(function ($msg) use ($userName) {
            return [
                'id' => $msg->id,
                'user_sender' => $msg->user_sender,
                'sender_avatar' => self::getSenderAvatarUrl($msg->user_sender),
                'is_me' => (trim(strtolower($msg->user_sender)) === trim(strtolower($userName))),
                'is_system' => ($msg->user_sender === 'Sistem'),
                'message_text' => $msg->message_text,
                'time' => $msg->formatted_time,
                'date' => $msg->formatted_date,
                'created_at' => $msg->created_at ? $msg->created_at->toISOString() : '',
            ];
        });

        // Update waktu baca terakhir
        WpChatGroupMember::where('group_id', $groupId)
            ->where('user_name', $userName)
            ->update(['last_read_at' => now()]);

        return response()->json([
            'success' => true,
            'messages' => $messages,
            'member_count' => $group->members()->count(),
        ]);
    }

    /**
     * Kirim Pesan Teks Baru ke Group
     */
    public function sendMessage(Request $request, $groupId)
    {
        $user = $this->getCurrentUser();
        $userName = $this->getUserOfficialName($user);
        $isAdmin = $user && ($user->isAdmin() || $user->role === 'admin');

        $group = WpChatGroup::findOrFail($groupId);

        if (!$isAdmin) {
            $isMember = WpChatGroupMember::where('group_id', $groupId)
                ->where(function ($q) use ($userName) {
                    $q->where('user_name', $userName)
                      ->orWhere(DB::raw('LOWER(TRIM(user_name))'), strtolower($userName));
                })->exists();

            if (!$isMember) {
                return response()->json(['success' => false, 'error' => 'Akses ditolak. Anda bukan anggota group ini.'], 403);
            }
        }

        $request->validate([
            'message_text' => 'required|string|max:5000',
        ]);

        $messageText = trim($request->message_text);

        $msg = WpChatMessage::create([
            'group_id' => $groupId,
            'user_sender' => $userName,
            'sender_id' => $user ? $user->id : null,
            'message_text' => $messageText,
            'created_at' => now(),
        ]);

        $group->touch();

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $msg->id,
                'user_sender' => $msg->user_sender,
                'sender_avatar' => self::getSenderAvatarUrl($msg->user_sender),
                'is_me' => true,
                'is_system' => false,
                'message_text' => $msg->message_text,
                'time' => $msg->formatted_time,
                'date' => $msg->formatted_date,
                'created_at' => $msg->created_at->toISOString(),
            ]
        ]);
    }

    /**
     * Ambil Daftar Group Terkini (Polling Sidebar)
     */
    public function getGroups(Request $request)
    {
        $user = $this->getCurrentUser();
        $userName = $this->getUserOfficialName($user);
        $isAdmin = $user && ($user->isAdmin() || $user->role === 'admin');

        $groupsQuery = WpChatGroup::where('is_active', true)
            ->with(['members', 'latestMessage']);

        if (!$isAdmin) {
            $groupsQuery->whereHas('members', function ($q) use ($userName) {
                $q->where('user_name', $userName)
                  ->orWhere(DB::raw('LOWER(TRIM(user_name))'), strtolower($userName));
            });
        }

        $groups = $groupsQuery->get()->sortByDesc(function ($g) {
            return $g->latestMessage ? $g->latestMessage->created_at : $g->created_at;
        })->values()->map(function ($g) {
            return [
                'id' => $g->id,
                'name' => $g->name,
                'initials' => $g->initials,
                'avatar_color' => $g->avatar_color,
                'member_count' => $g->members->count(),
                'latest_message' => $g->latestMessage ? [
                    'text' => $g->latestMessage->message_text,
                    'sender' => $g->latestMessage->user_sender,
                    'time' => $g->latestMessage->formatted_time,
                ] : null,
            ];
        });

        return response()->json([
            'success' => true,
            'groups' => $groups,
        ]);
    }

    /**
     * Polling Global Notifikasi Chat (untuk Lonceng Navbar & Toast Popup)
     */
    public function checkNotifications(Request $request)
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return response()->json([
                'success' => false,
                'unread_total' => 0,
                'max_id' => 0,
                'new_messages' => [],
                'unread_groups' => [],
            ]);
        }

        $userName = $this->getUserOfficialName($user);
        $isAdmin = ($user->isAdmin() || $user->role === 'admin');

        // Ambil ID group tempat user menjadi anggota
        $memberGroupQuery = WpChatGroupMember::where(function ($q) use ($userName) {
            $q->where('user_name', $userName)
              ->orWhere(DB::raw('LOWER(TRIM(user_name))'), strtolower($userName));
        });
        $userMemberships = $memberGroupQuery->get()->keyBy('group_id');
        $userGroupIds = $userMemberships->keys()->toArray();

        if ($isAdmin) {
            $allActiveGroupIds = WpChatGroup::where('is_active', true)->pluck('id')->toArray();
            $userGroupIds = array_values(array_unique(array_merge($userGroupIds, $allActiveGroupIds)));
        }

        if (empty($userGroupIds)) {
            return response()->json([
                'success' => true,
                'unread_total' => 0,
                'max_id' => 0,
                'new_messages' => [],
                'unread_groups' => [],
            ]);
        }

        $groups = WpChatGroup::whereIn('id', $userGroupIds)
            ->where('is_active', true)
            ->get();

        $unreadTotal = 0;
        $unreadGroups = [];

        foreach ($groups as $grp) {
            $membership = $userMemberships->get($grp->id);
            $lastRead = $membership?->last_read_at;

            $msgQuery = WpChatMessage::where('group_id', $grp->id)
                ->where('user_sender', '!=', 'Sistem')
                ->whereRaw('LOWER(TRIM(user_sender)) != ?', [strtolower(trim($userName))]);

            if ($lastRead) {
                $msgQuery->where('created_at', '>', $lastRead);
            }

            $count = $msgQuery->count();
            if ($count > 0) {
                $unreadTotal += $count;
                $lastMsg = $msgQuery->latest('id')->first();
                $unreadGroups[] = [
                    'group_id' => $grp->id,
                    'group_name' => $grp->name,
                    'avatar_color' => $grp->avatar_color,
                    'initials' => $grp->initials,
                    'unread_count' => $count,
                    'last_message' => $lastMsg ? [
                        'sender' => $lastMsg->user_sender,
                        'text' => \Illuminate\Support\Str::limit($lastMsg->message_text, 65),
                        'time' => $lastMsg->formatted_time,
                    ] : null,
                ];
            }
        }

        // Ambil pesan baru yang masuk untuk memicu Toast
        $newMessages = [];
        $lastChatId = (int) $request->query('last_chat_id', 0);
        if ($lastChatId > 0) {
            $rawNew = WpChatMessage::whereIn('group_id', $userGroupIds)
                ->where('id', '>', $lastChatId)
                ->where('user_sender', '!=', 'Sistem')
                ->whereRaw('LOWER(TRIM(user_sender)) != ?', [strtolower(trim($userName))])
                ->with('group')
                ->orderBy('id', 'asc')
                ->take(10)
                ->get();

            foreach ($rawNew as $m) {
                $newMessages[] = [
                    'id' => $m->id,
                    'group_id' => $m->group_id,
                    'group_name' => $m->group ? $m->group->name : 'Group Chat',
                    'user_sender' => $m->user_sender,
                    'sender_avatar' => self::getSenderAvatarUrl($m->user_sender),
                    'message_text' => \Illuminate\Support\Str::limit($m->message_text, 140),
                    'time' => $m->formatted_time,
                ];
            }
        }

        $currentMaxId = WpChatMessage::whereIn('group_id', $userGroupIds)->max('id') ?: 0;

        return response()->json([
            'success' => true,
            'unread_total' => $unreadTotal,
            'max_id' => $currentMaxId,
            'new_messages' => $newMessages,
            'unread_groups' => $unreadGroups,
        ]);
    }
}
