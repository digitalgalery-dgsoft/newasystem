<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\LegacyAttachmentService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AttachmentController extends Controller
{
    /**
     * Pastikan pengunjung telah login (baik sebagai user sistem ataupun kandidat CBT).
     */
    private function ensureAuthenticated()
    {
        if (!Auth::check() && !session()->has('candidate_id')) {
            if (request()->expectsJson()) {
                abort(401, 'Sesi login diperlukan untuk mengakses berkas lampiran.');
            }
            return redirect()->guest(route('login'))
                ->with('warning', 'Silakan login terlebih dahulu untuk mengakses berkas lampiran.');
        }
        return null;
    }

    private function secureHeaders(): array
    {
        return [
            'Cache-Control' => 'private, no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'X-Robots-Tag' => 'noindex, nofollow, noarchive, nosnippet, noimageindex',
        ];
    }

    /**
     * Tampilkan Berkas Lampiran Kandidat (Foto Profil, CV, Bukti Komputer, Berkas Lamaran)
     * URL: /lampiran/{filename}
     */
    public function showLampiran(Request $request, string $filename)
    {
        if ($redirect = $this->ensureAuthenticated()) {
            return $redirect;
        }

        $baseName = basename($filename);
        if (empty($baseName) || $baseName === '.' || $baseName === '..') {
            abort(404, 'Berkas tidak ditemukan.');
        }

        $candidatePaths = [
            public_path('lampiran/' . $baseName),
            public_path('lampiran/berkas_lamaran/' . $baseName),
            storage_path('app/lampiran/' . $baseName),
            storage_path('app/public/lampiran/' . $baseName),
            public_path('storage/' . $baseName),
        ];

        foreach ($candidatePaths as $path) {
            if (file_exists($path) && !is_dir($path)) {
                return response()->file($path, $this->secureHeaders());
            }
        }

        // Fallback untuk user login jika berkas lama belum tersinkron ke lokal
        return redirect()->away('https://asystem.co.id/interview/lampiran/' . rawurlencode($baseName));
    }

    /**
     * Tampilkan Berkas Bukti Referensi Cek
     * URL: /refcekfile/{filename}
     */
    public function showRefcek(Request $request, string $filename)
    {
        if ($redirect = $this->ensureAuthenticated()) {
            return $redirect;
        }

        $baseName = basename($filename);
        if (empty($baseName) || $baseName === '.' || $baseName === '..') {
            abort(404, 'Berkas tidak ditemukan.');
        }

        $resolved = LegacyAttachmentService::resolveRefcek($baseName);
        if ($resolved && file_exists($resolved) && !is_dir($resolved)) {
            return response()->file($resolved, $this->secureHeaders());
        }

        return redirect()->away('https://asystem.co.id/v3/refcekfile/' . rawurlencode($baseName));
    }

    /**
     * Tampilkan Berkas Approval Prinsiple
     * URL: /approval/{filename}
     */
    public function showApproval(Request $request, string $filename)
    {
        if ($redirect = $this->ensureAuthenticated()) {
            return $redirect;
        }

        $baseName = basename($filename);
        if (empty($baseName) || $baseName === '.' || $baseName === '..') {
            abort(404, 'Berkas tidak ditemukan.');
        }

        $resolved = LegacyAttachmentService::resolveApproval($baseName);
        if ($resolved && file_exists($resolved) && !is_dir($resolved)) {
            return response()->file($resolved, $this->secureHeaders());
        }

        if (str_starts_with($baseName, 'ttd_')) {
            return redirect()->away('https://asystem.co.id/v3/prinsiple/ttdfileprinsiple/' . rawurlencode($baseName));
        }

        return redirect()->away('https://asystem.co.id/v3/approval/' . rawurlencode($baseName));
    }

    /**
     * Tampilkan Tanda Tangan Digital Prinsiple
     * URL: /prinsiple/ttdfileprinsiple/{filename}
     */
    public function showTtdPrinciple(Request $request, string $filename)
    {
        if ($redirect = $this->ensureAuthenticated()) {
            return $redirect;
        }

        $baseName = basename($filename);
        if (empty($baseName) || $baseName === '.' || $baseName === '..') {
            abort(404, 'Berkas tidak ditemukan.');
        }

        $resolved = LegacyAttachmentService::resolveApproval($baseName);
        if ($resolved && file_exists($resolved) && !is_dir($resolved)) {
            return response()->file($resolved, $this->secureHeaders());
        }

        return redirect()->away('https://asystem.co.id/v3/prinsiple/ttdfileprinsiple/' . rawurlencode($baseName));
    }

    /**
     * Fallback Berkas Legacy V3
     * URL: /v3/{path}
     */
    public function showLegacyV3(Request $request, string $path)
    {
        if ($redirect = $this->ensureAuthenticated()) {
            return $redirect;
        }

        $candidates = [
            public_path('v3/' . $path),
            public_path($path),
            storage_path('app/v3/' . $path),
            public_path('storage/' . $path),
        ];

        foreach ($candidates as $cand) {
            if (file_exists($cand) && !is_dir($cand)) {
                return response()->file($cand, $this->secureHeaders());
            }
        }

        return redirect()->away('https://asystem.co.id/v3/' . $path);
    }
}
