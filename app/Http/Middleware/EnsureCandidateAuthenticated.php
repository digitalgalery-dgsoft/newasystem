<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Candidate;

class EnsureCandidateAuthenticated
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $candidateId = session('cbt_candidate_id');

        if (!$candidateId) {
            return redirect()->route('cbt.login')->with('warning', 'Silakan login terlebih dahulu untuk mengakses portal CBT.');
        }

        $candidate = Candidate::find($candidateId);

        if (!$candidate) {
            session()->forget('cbt_candidate_id');
            return redirect()->route('cbt.login')->with('error', 'Sesi akun kandidat tidak ditemukan. Silakan login kembali.');
        }

        // Simpan instance candidate pada request untuk kemudahan akses
        $request->attributes->set('candidate', $candidate);

        return $next($request);
    }
}
