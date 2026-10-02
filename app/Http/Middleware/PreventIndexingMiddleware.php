<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventIndexingMiddleware
{
    /**
     * Handle an incoming request and ensure sensitive pages & candidate data are never indexed by search engines.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $path = trim($request->path(), '/');

        // Halaman yang diizinkan untuk diindeks oleh mesin pencari (hanya info lowongan & landing page publik)
        $isPublicSearchable = (
            $path === '' ||
            $path === 'home' ||
            $path === 'job' ||
            (preg_match('#^job/\d+$#', $path) === 1)
        );

        // Deteksi apakah pemanggil adalah bot perayap mesin pencari (Googlebot, Bingbot, dll.)
        $userAgent = $request->userAgent() ?? '';
        $isSearchBot = (bool)preg_match('/(googlebot|bingbot|slurp|duckduckbot|baiduspider|yandexbot|sogou|exabot)/i', $userAgent);

        // Jika mesin pencari mencoba merayapi URL data internal/kandidat yang bukan publik,
        // segera berikan respons HTTP 410 Gone.
        // Berdasarkan standar resmi Google Search Central:
        // Status 410 Gone adalah sinyal permanen terkuat yang memerintahkan Googlebot
        // untuk segera menghapus (drop/purge) URL tersebut dari indeks hasil pencarian.
        if ($isSearchBot && !$isPublicSearchable) {
            return response("410 Gone: This resource is private and permanently removed from public search indexing.", 410, [
                'X-Robots-Tag'   => 'noindex, nofollow, noarchive, nosnippet, noimageindex',
                'Content-Type'   => 'text/plain; charset=utf-8',
                'Cache-Control'  => 'no-cache, no-store, must-revalidate',
            ]);
        }

        $response = $next($request);

        // HTTP Security Headers Global (Proteksi MIME Sniffing, Clickjacking, dan Referrer Leak)
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        if (!$isPublicSearchable) {
            // Pasang header X-Robots-Tag paling ketat untuk Googlebot dan search engine lainnya
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow, noarchive, nosnippet, noimageindex');
        }

        return $response;
    }
}
