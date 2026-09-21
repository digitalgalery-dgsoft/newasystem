<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     * Hanya pengguna dengan role 'admin' yang dapat mengakses menu dan fitur Master Data.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isJson = $request->expectsJson() || $request->wantsJson() || $request->ajax();

        // 1. Cek apakah sudah login
        if (!Auth::check()) {
            if ($isJson) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi login Anda telah berakhir. Silakan muat ulang (refresh) halaman dan login kembali.'
                ], 200);
            }

            return redirect()->route('login')
                ->with('warning', 'Silakan login terlebih dahulu untuk mengakses menu ini.');
        }

        // 2. Cek apakah memiliki role Administrator
        if (!Auth::user()->isAdmin()) {
            if ($isJson) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses Ditolak! Fitur ini hanya dapat diakses oleh Administrator.'
                ], 200);
            }

            if (session()->has('impersonator_id')) {
                return redirect()->route('fitur.index')
                    ->with('warning', 'Anda sedang dalam mode Switch User (' . Auth::user()->name . '). Halaman Master Data hanya untuk Administrator. Silakan klik tombol "Kembali ke User Asli" di atas untuk kembali.');
            }
            return redirect()->route('fitur.index')
                ->with('error', 'Akses Ditolak! Menu Master Data hanya dapat diakses oleh Administrator.');
        }

        return $next($request);
    }
}
