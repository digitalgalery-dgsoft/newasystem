<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class ActivityLogger
{
    /**
     * Catat aktivitas umum ke database.
     *
     * @param string $action Jenis aksi (LOGIN, LOGOUT, CREATE, UPDATE, DELETE, EXPORT, SYNC, SWITCH_USER, dll.)
     * @param string $module Nama modul (Auth, Master Karyawan, Kandidat Portal, dll.)
     * @param string $description Penjelasan ringkas aktivitas
     * @param mixed $subject Objek model terkait (opsional)
     * @param array $properties Data tambahan/diff perubahan (opsional)
     * @param User|null $user Pengguna yang melakukan aktivitas (opsional, default: Auth::user())
     * @return ActivityLog|null
     */
    public static function log(
        string $action,
        string $module,
        string $description,
        mixed $subject = null,
        array $properties = [],
        ?User $user = null
    ): ?ActivityLog {
        try {
            $currentUser = $user ?? Auth::user();
            $request = request();

            $subjectType = null;
            $subjectId = null;

            if (is_object($subject)) {
                $subjectType = get_class($subject);
                $subjectId = method_exists($subject,('getKey')) ? (string)$subject->getKey() : ($subject->id ?? null);
            } elseif (is_string($subject) || is_numeric($subject)) {
                $subjectId = (string)$subject;
            }

            // Snapshot identitas user
            $userId = $currentUser?->id;
            $userName = $currentUser?->name ?? 'Guest / Sistem';
            $userEmail = $currentUser?->email;
            $userJabatan = $currentUser?->jabatan_display ?? $currentUser?->job_title ?? ($currentUser ? 'Staff' : 'Sistem');

            return ActivityLog::create([
                'user_id'      => $userId,
                'user_name'    => $userName,
                'user_email'   => $userEmail,
                'user_jabatan' => $userJabatan,
                'action'       => strtoupper(trim($action)),
                'module'       => trim($module),
                'description'  => trim($description),
                'subject_type' => $subjectType,
                'subject_id'   => $subjectId,
                'properties'   => !empty($properties) ? $properties : null,
                'ip_address'   => $request ? $request->ip() : null,
                'user_agent'   => $request ? $request->userAgent() : 'CLI / Background Process',
                'url'          => $request ? substr($request->fullUrl(), 0, 1000) : null,
                'method'       => $request ? $request->method() : 'CLI',
            ]);
        } catch (Throwable $e) {
            // Jangan biarkan kegagalan logging menghentikan alur aplikasi utama
            Log::warning('ActivityLogger failure: ' . $e->getMessage(), [
                'action' => $action,
                'module' => $module,
            ]);
            return null;
        }
    }

    /**
     * Catat aktivitas autentikasi
     */
    public static function auth(string $action, string $description, ?User $user = null): ?ActivityLog
    {
        return self::log($action, 'Auth & Akun', $description, null, [], $user);
    }

    /**
     * Catat aktivitas CRUD data (menyimpan data lama & baru jika ada)
     */
    public static function crud(
        string $action,
        string $module,
        string $description,
        mixed $subject = null,
        array $old = [],
        array $new = []
    ): ?ActivityLog {
        $properties = [];
        if (!empty($old)) {
            $properties['old'] = $old;
        }
        if (!empty($new)) {
            $properties['new'] = $new;
        }

        return self::log($action, $module, $description, $subject, $properties);
    }

    /**
     * Catat aktivitas unduh/export data
     */
    public static function export(string $module, string $description, array $filters = []): ?ActivityLog
    {
        return self::log('EXPORT', $module, $description, null, [
            'filters' => $filters,
            'exported_at' => now()->toIso8601String(),
        ]);
    }

    /**
     * Catat aktivitas sinkronisasi data eksternal (misal: Odoo ERP)
     */
    public static function sync(string $module, string $description, array $meta = []): ?ActivityLog
    {
        return self::log('SYNC', $module, $description, null, $meta);
    }
}
