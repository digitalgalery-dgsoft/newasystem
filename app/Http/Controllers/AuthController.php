<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Employee;

class AuthController extends Controller
{
    /**
     * Tampilkan Form Login Karyawan / User (Replikasi v3/login.php)
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('fitur.index');
        }

        return view('auth.login');
    }

    /**
     * Proses Login Karyawan / User
     * Rule:
     * - Karyawan Inhouse otomatis memiliki izin login.
     * - Karyawan RateCard hanya bisa login jika diberikan izin akses (akses_login == true).
     * - Default password: ddmmyyyy dari tanggal lahir.
     * - Username: email karyawan.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $email = trim($request->input('email'));
        $inputPassword = $request->input('password');
        $remember = $request->has('remember');

        // 1. Check if an Employee exists with this email
        $employee = Employee::where('email', $email)->first();

        if ($employee) {
            // Check status: Resigned employees cannot log in
            if ($employee->status === 'Resign') {
                return back()
                    ->withInput($request->only('email', 'remember'))
                    ->withErrors([
                        'email' => 'Akun karyawan ini berstatus Resign / Nonaktif dan tidak memiliki akses masuk ke sistem.',
                    ]);
            }

            // Check Access Permission:
            // Inhouse has access; RateCard only if granted (akses_login == true)
            if (!$employee->hasLoginAccess()) {
                return back()
                    ->withInput($request->only('email', 'remember'))
                    ->withErrors([
                        'email' => 'Akses login belum diaktifkan untuk karyawan RateCard ini. Silakan hubungi Admin HR untuk perizinan akses sistem.',
                    ]);
            }

            // Verify Password:
            // Matches default password (ddmmyyyy of tanggal_lahir) OR custom hashed password
            $defaultPassword = $employee->default_password;
            $isPasswordValid = false;

            if ($inputPassword === $defaultPassword) {
                $isPasswordValid = true;
            } elseif (!empty($employee->password) && Hash::check($inputPassword, $employee->password)) {
                $isPasswordValid = true;
            }

            if ($isPasswordValid) {
                // Ensure a User account exists in users table
                $userRole = ($employee->tipe_karyawan === 'Inhouse') ? 'karyawan_inhouse' : 'karyawan_ratecard';
                $user = User::firstOrCreate(
                    ['email' => $employee->email],
                    [
                        'name' => $employee->nama_karyawan,
                        'password' => Hash::make($inputPassword),
                        'role' => $userRole,
                        'area' => $employee->area,
                        'job_title' => $employee->jabatan,
                        'phone' => $employee->telepon,
                        'is_active' => true,
                    ]
                );

                // Update password on user record
                $user->update([
                    'name' => $employee->nama_karyawan,
                    'password' => Hash::make($inputPassword),
                    'role' => $userRole,
                    'is_active' => true,
                ]);

                Auth::login($user, $remember);
                $request->session()->regenerate();

                return redirect()->intended(route('fitur.index'))
                    ->with('success', "Selamat datang kembali, {$employee->nama_karyawan} ({$employee->tipe_karyawan})!");
            }

            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors([
                    'email' => 'Kata sandi tidak sesuai. Default kata sandi adalah tanggal lahir (format: ddmmyyyy).',
                ]);
        }

        // 2. Fallback to standard Admin/Recruiter User authentication
        $credentials = [
            'email' => $email,
            'password' => $inputPassword,
        ];

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();
            return redirect()->intended(route('fitur.index'))
                ->with('success', "Selamat datang kembali, {$user->name}!");
        }

        // 3. Fallback pencocokan alias user (misal: abdurrahman2330@gmail.com -> jamil@asystem.co.id)
        $aliasUser = null;
        if (str_contains($email, 'abdurrahman') || str_contains($email, 'jamil')) {
            $aliasUser = User::where('email', 'jamil@asystem.co.id')->first();
        }
        if ($aliasUser && Hash::check($inputPassword, $aliasUser->password)) {
            Auth::login($aliasUser, $remember);
            $request->session()->regenerate();
            return redirect()->intended(route('fitur.index'))
                ->with('success', "Selamat datang kembali, {$aliasUser->name}!");
        }

        return back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors([
                'email' => 'Email atau kata sandi yang Anda masukkan tidak sesuai.',
            ]);
    }

    /**
     * Proses Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('info', 'Anda telah berhasil keluar dari sistem.');
    }
}
