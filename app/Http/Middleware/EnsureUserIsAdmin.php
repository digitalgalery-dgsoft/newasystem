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
        // 1. Cek apakah sudah login
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('warning', 'Silakan login terlebih dahulu untuk mengakses menu ini.');
        }

        // 2. Cek apakah memiliki role Administrator
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('fitur.index')
                ->with('error', 'Akses Ditolak! Menu Master Data hanya dapat diakses oleh Administrator.');
        }

        return $next($request);
    }
}
