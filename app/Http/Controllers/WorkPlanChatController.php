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
}
