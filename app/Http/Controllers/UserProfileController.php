<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
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

        $oldEmail = $user->email;

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

        $user->name = trim($request->name);
        $user->email = strtolower(trim($request->email));
        $user->phone = trim($request->phone);
        $user->save();

        // Sinkronkan ke data master employee jika akun ini terhubung
        try {
            $employee = Employee::whereRaw('LOWER(TRIM(email)) = ?', [strtolower(trim($oldEmail))])
                ->orWhere('id', $user->linked_employee?->id)
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

        ActivityLogger::log('UPDATE', 'Profil Pengguna', "Memperbarui data identitas profil: {$user->name} ({$user->email})", $user);

        return redirect()->route('profile.index')->with('success', 'Profil akun Anda berhasil diperbarui.');
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

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Password saat ini yang Anda masukkan salah.',
            ])->withInput();
        }

        $newHashedPassword = Hash::make($request->password);
        $user->password = $newHashedPassword;
        $user->save();

        // Sinkronkan password ke data employee jika ada
        try {
            $employee = $user->linked_employee;
            if ($employee) {
                $employee->password = $newHashedPassword;
                $employee->save();
            }
        } catch (\Throwable $e) {
            \Log::warning('Sinkronisasi password employee gagal: ' . $e->getMessage());
        }

        ActivityLogger::log('UPDATE_PASSWORD', 'Profil Pengguna', "Memperbarui password akun: {$user->name} ({$user->email})", $user);

        return redirect()->route('profile.index')->with('success', 'Password Anda berhasil diperbarui! Gunakan password baru ini saat login kembali.');
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
