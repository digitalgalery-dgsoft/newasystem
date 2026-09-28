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
        $response = $next($request);

        $path = trim($request->path(), '/');

        // Halaman yang diizinkan untuk diindeks oleh mesin pencari (hanya info lowongan & landing page publik)
        $isPublicSearchable = (
            $path === '' ||
            $path === 'home' ||
            $path === 'job' ||
            (preg_match('#^job/\d+$#', $path) === 1)
        );

        if (!$isPublicSearchable) {
            // Pasang header X-Robots-Tag paling ketat untuk Googlebot dan search engine lainnya
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow, noarchive, nosnippet, noimageindex');
        }

        return $response;
    }
}
