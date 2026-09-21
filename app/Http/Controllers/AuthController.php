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

        $identifier = trim($request->input('email'));
        $inputPassword = $request->input('password');
        $remember = $request->has('remember');

        // 1. Check if an Employee exists with Email, NIK, or NIP
        // Prioritize: Inhouse / active login access first, then newest ID
        $employees = Employee::where('email', $identifier)
            ->orWhere('nik', $identifier)
            ->orWhere('nip', $identifier)
            ->orderByRaw("CASE WHEN tipe_karyawan = 'Inhouse' THEN 0 ELSE 1 END")
            ->orderByDesc('akses_login')
            ->orderByDesc('id')
            ->get();

        if ($employees->isNotEmpty()) {
            $matchedEmployee = null;
            $hasValidPassword = false;

            foreach ($employees as $empCandidate) {
                if ($empCandidate->status === 'Resign') {
                    continue;
                }

                $defPwd = $empCandidate->default_password;
                if ($inputPassword === $defPwd || (!empty($empCandidate->password) && Hash::check($inputPassword, $empCandidate->password))) {
                    $matchedEmployee = $empCandidate;
                    $hasValidPassword = true;
                    if ($empCandidate->hasLoginAccess()) {
                        break; // Found matching employee with active login access!
                    }
                }
            }

            // If no password match found, pick the most relevant non-resigned employee for error messaging
            if (!$matchedEmployee) {
                $matchedEmployee = $employees->first(fn($e) => $e->status !== 'Resign') ?: $employees->first();
            }

            // Check status: Resigned employees cannot log in
            if ($matchedEmployee->status === 'Resign') {
                return back()
                    ->withInput($request->only('email', 'remember'))
                    ->withErrors([
                        'email' => 'Akun karyawan ini berstatus Resign / Nonaktif dan tidak memiliki akses masuk ke sistem.',
                    ]);
            }

            // Check Access Permission:
            if (!$matchedEmployee->hasLoginAccess()) {
                return back()
                    ->withInput($request->only('email', 'remember'))
                    ->withErrors([
                        'email' => 'Akses login belum diaktifkan untuk karyawan ini. Silakan hubungi Admin HR untuk perizinan akses sistem.',
                    ]);
            }

            if ($hasValidPassword) {
                // Ensure a User account exists in users table
                $userRole = ($matchedEmployee->tipe_karyawan === 'Inhouse') ? 'karyawan_inhouse' : 'karyawan_ratecard';
                $userEmail = !empty($matchedEmployee->email) ? $matchedEmployee->email : ($matchedEmployee->nik . '@asystem.co.id');

                $user = User::where('email', $userEmail)->first();
                if (!$user) {
                    $user = User::create([
                        'name' => $matchedEmployee->nama_karyawan,
                        'email' => $userEmail,
                        'password' => Hash::make($inputPassword),
                        'role' => $userRole,
                        'area' => $matchedEmployee->area,
                        'job_title' => $matchedEmployee->jabatan,
                        'phone' => $matchedEmployee->telepon,
                        'is_active' => true,
                    ]);
                } else {
                    $existingRole = $user->role;
                    $assignedRole = (!empty($existingRole) && !in_array($existingRole, ['karyawan_inhouse', 'karyawan_ratecard'], true))
                        ? $existingRole
                        : ($existingRole ?: $userRole);

                    $user->update([
                        'name' => $matchedEmployee->nama_karyawan,
                        'password' => Hash::make($inputPassword),
                        'role' => $assignedRole,
                        'area' => $matchedEmployee->area ?: $user->area,
                        'job_title' => $matchedEmployee->jabatan ?: $user->job_title,
                        'phone' => $matchedEmployee->telepon ?: $user->phone,
                        'is_active' => true,
                    ]);
                }

                Auth::login($user, $remember);
                $request->session()->regenerate();

                return redirect()->intended(route('fitur.index'))
                    ->with('success', "Selamat datang kembali, {$matchedEmployee->nama_karyawan} ({$matchedEmployee->tipe_karyawan})!");
            }

            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors([
                    'email' => 'Kata sandi tidak sesuai. Default kata sandi adalah tanggal lahir (format: ddmmyyyy).',
                ]);
        }

        // 2. Fallback to standard Admin/Recruiter User authentication
        $credentials = [
            'email' => $identifier,
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
