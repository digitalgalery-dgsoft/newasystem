<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Employee;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class UserProfileController extends Controller
{
    /**
     * Tampilkan halaman edit profil pengguna / karyawan
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        $employee = $user->linked_employee;

        // Hitung persentase kelengkapan akun
        $completeness = 0;
        if (!empty($user->name)) $completeness += 25;
        if (!empty($user->email)) $completeness += 25;
        if (!empty($user->phone)) $completeness += 25;
        if (!empty($user->avatar) || ($employee && !empty($employee->foto))) $completeness += 25;

        $errors = session('errors') ?: new \Illuminate\Support\ViewErrorBag();

        return view('profile.index', compact('user', 'employee', 'completeness', 'errors'));
    }

    /**
     * Update data identitas dan kontak pengguna
     */
    public function update(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'phone' => 'nullable|string|max:30',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
            'avatar.image' => 'File harus berupa gambar.',
            'avatar.mimes' => 'Format gambar yang diperbolehkan: JPEG, PNG, JPG, atau WEBP.',
            'avatar.max' => 'Ukuran file gambar maksimal 2 MB.',
        ]);

        $oldEmail = strtolower(trim((string)$user->email));
        $newEmail = strtolower(trim((string)$request->email));
        $newName = trim($request->name);
        $emailChanged = (!empty($oldEmail) && $oldEmail !== $newEmail);

        // Khusus Karyawan Inhouse: Wajib menggunakan domain email corporate resmi
        if ($user->isInhouseUser()) {
            $hasCorpDomain = false;
            foreach (User::CORPORATE_EMAIL_DOMAINS as $domain) {
                if (str_ends_with($newEmail, '@' . strtolower(trim($domain)))) {
                    $hasCorpDomain = true;
                    break;
                }
            }

            if (!$hasCorpDomain) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'email' => 'Khusus Karyawan Inhouse wajib menggunakan alamat email corporate resmi (@arina.co.id, @alvakaryaperkasa.co.id, @anugrahterpercayakerja.co.id, @abadiberkatodelia.co.id, @anugrahtalentaberkarya.co.id, atau @asystem.co.id).',
                    ]);
            }
        }

        // Upload foto profil baru jika ada
        if ($request->hasFile('avatar')) {
            $avatarDir = public_path('uploads/avatars');
            if (!File::exists($avatarDir)) {
                File::makeDirectory($avatarDir, 0755, true, true);
            }

            // Hapus file lama jika ada dan tersimpan di folder avatars
            if (!empty($user->avatar)) {
                $oldPath = public_path($user->avatar);
                if (File::exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $file = $request->file('avatar');
            $extension = $file->getClientOriginalExtension() ?: 'jpg';
            $filename = 'avatar_' . $user->id . '_' . time() . '.' . strtolower($extension);
            $file->move($avatarDir, $filename);

            $user->avatar = 'uploads/avatars/' . $filename;
        }

        $user->name = $newName;
        $user->email = $newEmail;
        $user->phone = trim($request->phone);

        // Simpan email lama ke riwayat email_aliases agar tetap terhubung
        if ($emailChanged) {
            $aliases = is_array($user->email_aliases) ? $user->email_aliases : [];
            if (!in_array($oldEmail, $aliases, true)) {
                $aliases[] = $oldEmail;
            }
            $user->email_aliases = array_values(array_unique(array_filter($aliases)));
        }

        $user->save();

        // JIKA EMAIL BERUBAH: Selaraskan seluruh data lowongan dan kandidat AS ini agar tetap terbaca penuh di Input Job, Kandidat Portal & Interview
        if ($emailChanged) {
            try {
                // Kumpulkan seluruh email historis user (oldEmail + semua alias yang pernah dimiliki)
                $allOldEmails = array_values(array_unique(array_filter(array_merge(
                    [$oldEmail],
                    is_array($user->email_aliases) ? $user->email_aliases : []
                ))));
                $allOldEmailsLower = array_map('strtolower', array_map('trim', $allOldEmails));

                // 1. Update data di tabel candidates (useras & recruiter_id)
                Candidate::where(function ($q) use ($allOldEmailsLower, $user) {
                    $q->whereIn(DB::raw('LOWER(TRIM(useras))'), $allOldEmailsLower)
                      ->orWhere('recruiter_id', $user->id);
                })->update([
                    'useras' => $newEmail,
                    'recruiter_id' => $user->id,
                ]);

                // 2. Selaraskan jika ada kandidat yang terdaftar memakai nama user
                if (!empty($user->name) && strlen($user->name) >= 4) {
                    Candidate::whereRaw('LOWER(TRIM(useras)) = ?', [strtolower(trim($user->name))])
                        ->update([
                            'useras' => $newEmail,
                            'recruiter_id' => $user->id,
                        ]);
                }

                // 3. Selaraskan tabel legacy tb_kandidat jika ada
                if (Schema::hasTable('tb_kandidat')) {
                    DB::table('tb_kandidat')
                        ->whereIn(DB::raw('LOWER(TRIM(useras))'), $allOldEmailsLower)
                        ->update([
                            'useras' => $newEmail,
                            'nama_as' => $user->name,
                        ]);
                }

                // 4. Selaraskan lowongan yang diposting oleh akun AS ini (Tabel job_specs & jobs)
                if (Schema::hasTable('job_specs')) {
                    DB::table('job_specs')
                        ->whereIn(DB::raw('LOWER(TRIM(created_by))'), $allOldEmailsLower)
                        ->update(['created_by' => $newEmail]);
                }
                if (Schema::hasTable('jobs')) {
                    try {
                        DB::table('jobs')
                            ->whereIn(DB::raw('LOWER(TRIM(created_by))'), $allOldEmailsLower)
                            ->update(['created_by' => $newEmail]);
                    } catch (\Throwable $e) {}
                }

                // 5. Selaraskan riwayat assessment interview jika ada
                if (Schema::hasTable('interview_assessments')) {
                    DB::table('interview_assessments')
                        ->whereIn(DB::raw('LOWER(TRIM(interviewer))'), $allOldEmailsLower)
                        ->update(['interviewer' => $newEmail]);
                }

                // 6. Selaraskan candidate logs jika ada
                if (Schema::hasTable('candidate_logs')) {
                    DB::table('candidate_logs')
                        ->whereIn(DB::raw('LOWER(TRIM("user"))'), $allOldEmailsLower)
                        ->update(['user' => $newEmail]);
                }
            } catch (\Throwable $e) {
                \Log::error('Sinkronisasi data lowongan & kandidat saat user AS ganti email gagal: ' . $e->getMessage());
            }
        }

        // Sinkronkan ke data master employee jika akun ini terhubung
        try {
            $checkEmails = array_values(array_unique(array_filter(array_merge(
                [$oldEmail, $newEmail],
                is_array($user->email_aliases) ? $user->email_aliases : []
            ))));
            $employee = Employee::whereIn(DB::raw('LOWER(TRIM(email))'), array_map('strtolower', array_map('trim', $checkEmails)))
                ->orWhere('id', $user->linked_employee?->id)
                ->orWhereRaw('LOWER(TRIM(nama_karyawan)) = ?', [strtolower(trim($user->name))])
                ->first();

            if ($employee) {
                $employee->nama_karyawan = $user->name;
                $employee->email = $user->email;
                if (!empty($user->phone)) {
                    $employee->telepon = $user->phone;
                }
                if (!empty($user->avatar)) {
                    $employee->foto = $user->avatar;
                }
                $employee->save();
            }
        } catch (\Throwable $e) {
            // Log notice silently without breaking user profile update
            \Log::warning('Sinkronisasi employee pada edit profile gagal: ' . $e->getMessage());
        }

        ActivityLogger::log('UPDATE', 'Profil Pengguna', "Memperbarui data identitas profil: {$user->name} ({$user->email})" . ($emailChanged ? " (Email diubah dari '{$oldEmail}' ke '{$newEmail}', lowongan & kandidat diselaraskan)" : ""), $user);

        return redirect()->route('profile.index')->with('success', 'Profil akun Anda berhasil diperbarui.' . ($emailChanged ? ' Seluruh data lowongan dan kandidat Anda telah diselaraskan ke email corporate baru.' : ''));
    }

    /**
     * Update password pengguna
     */
    public function updatePassword(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password baru minimal harus 6 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        // Verifikasi password saat ini terhadap user atau data master employee
        $currentPasswordMatches = false;
        if (!empty($user->password) && Hash::check($request->current_password, $user->password)) {
            $currentPasswordMatches = true;
        } else {
            // Cek juga ke data employee terkait (default password ddmmyyyy atau hash employee)
            $empList = Employee::whereRaw('LOWER(TRIM(email)) = ?', [strtolower(trim($user->email))])->get();
            foreach ($empList as $emp) {
                if ($request->current_password === $emp->default_password || (!empty($emp->password) && Hash::check($request->current_password, $emp->password))) {
                    $currentPasswordMatches = true;
                    break;
                }
            }
        }

        if (!$currentPasswordMatches) {
            return back()->withErrors([
                'current_password' => 'Password saat ini yang Anda masukkan salah.',
            ])->withInput();
        }

        $newHashedPassword = Hash::make($request->password);
        $user->password = $newHashedPassword;
        $user->save();

        // Sinkronkan password ke seluruh data employee terkait
        $user->syncPasswordToEmployees($newHashedPassword);

        ActivityLogger::log('UPDATE_PASSWORD', 'Profil Pengguna', "Memperbarui password akun: {$user->name} ({$user->email})", $user);

        return redirect()->route('profile.index')
            ->with('success', 'Password Anda berhasil diperbarui! Gunakan password baru ini saat login kembali.')
            ->with('tab', 'security');
    }

    /**
     * Hapus foto profil dan kembalikan ke avatar inisial
     */
    public function destroyAvatar()
    {
        /** @var User $user */
        $user = Auth::user();

        if (!empty($user->avatar)) {
            $path = public_path($user->avatar);
            if (File::exists($path)) {
                @unlink($path);
            }
            $user->avatar = null;
            $user->save();

            ActivityLogger::log('DELETE', 'Profil Pengguna', "Menghapus foto profil avatar: {$user->name}", $user);
        }

        return redirect()->route('profile.index')->with('success', 'Foto profil berhasil dihapus dan dikembalikan ke avatar bawaan.');
    }
}
