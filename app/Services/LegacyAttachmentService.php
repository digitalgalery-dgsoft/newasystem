<?php

namespace App\Services;

class LegacyAttachmentService
{
    /**
     * Resolve absolute local path for a Refcek attachment.
     * Searches local storage/public first, then downloads and caches from legacy server.
     */
    public static function resolveRefcek(?string $filename): ?string
    {
        if (empty($filename)) {
            return null;
        }

        $baseName = basename(trim($filename));
        if (empty($baseName) || $baseName === '.' || $baseName === '..') {
            return null;
        }

        // 1. Check local candidates
        $localCandidates = [
            public_path('refcekfile/' . $baseName),
            public_path('lampiran/' . $baseName),
            public_path('storage/' . $baseName),
            storage_path('app/public/' . $baseName),
            storage_path('app/public/refcek_proofs/' . $baseName),
            'd:/ASystem/v3/refcekfile/' . $baseName,
            base_path('../v3/refcekfile/' . $baseName),
            'C:/xampp/htdocs/v3/refcekfile/' . $baseName,
        ];

        foreach ($localCandidates as $cand) {
            if (file_exists($cand) && !is_dir($cand) && filesize($cand) > 50) {
                return $cand;
            }
        }

        // 2. Fetch from legacy server
        $remoteUrls = [
            'https://asystem.co.id/v3/refcekfile/' . rawurlencode($baseName),
            'https://asystem.co.id/v3/' . rawurlencode($baseName),
            'https://asystem.co.id/interview/lampiran/' . rawurlencode($baseName),
        ];

        foreach ($remoteUrls as $url) {
            $data = self::fetchRemoteFile($url);
            if ($data !== null) {
                $dir = public_path('refcekfile');
                if (!is_dir($dir)) {
                    @mkdir($dir, 0777, true);
                }
                $savePath = $dir . DIRECTORY_SEPARATOR . $baseName;
                if (@file_put_contents($savePath, $data)) {
                    return $savePath;
                }
            }
        }

        return null;
    }

    /**
     * Resolve absolute local path for an Approval Prinsiple attachment (screenshot / digital signature).
     * Searches local storage/public first, then downloads and caches from legacy server.
     */
    public static function resolveApproval(?string $filename): ?string
    {
        if (empty($filename)) {
            return null;
        }

        $baseName = basename(trim($filename));
        if (empty($baseName) || $baseName === '.' || $baseName === '..') {
            return null;
        }

        $isTtd = str_starts_with($baseName, 'ttd_');

        // 1. Check local candidates
        $localCandidates = [
            public_path('approval/' . $baseName),
            public_path('prinsiple/ttdfileprinsiple/' . $baseName),
            public_path('storage/approvals/' . $baseName),
            public_path('storage/' . $baseName),
            public_path('lampiran/' . $baseName),
            storage_path('app/public/' . $baseName),
            storage_path('app/public/approvals/' . $baseName),
            'd:/ASystem/v3/prinsiple/ttdfileprinsiple/' . $baseName,
            'd:/ASystem/v3/approval/' . $baseName,
            base_path('../v3/prinsiple/ttdfileprinsiple/' . $baseName),
            base_path('../v3/approval/' . $baseName),
            'C:/xampp/htdocs/v3/prinsiple/ttdfileprinsiple/' . $baseName,
        ];

        foreach ($localCandidates as $cand) {
            if (file_exists($cand) && !is_dir($cand) && filesize($cand) > 50) {
                return $cand;
            }
        }

        // 2. Fetch from legacy server
        $remoteUrls = [];
        if ($isTtd) {
            $remoteUrls[] = 'https://asystem.co.id/v3/prinsiple/ttdfileprinsiple/' . rawurlencode($baseName);
            $remoteUrls[] = 'https://asystem.co.id/v3/' . rawurlencode($baseName);
            $remoteUrls[] = 'https://asystem.co.id/v3/approval/' . rawurlencode($baseName);
        } else {
            $remoteUrls[] = 'https://asystem.co.id/v3/approval/' . rawurlencode($baseName);
            $remoteUrls[] = 'https://asystem.co.id/v3/' . rawurlencode($baseName);
            $remoteUrls[] = 'https://asystem.co.id/v3/prinsiple/ttdfileprinsiple/' . rawurlencode($baseName);
        }

        foreach ($remoteUrls as $url) {
            $data = self::fetchRemoteFile($url);
            if ($data !== null) {
                $targetDirName = $isTtd ? 'prinsiple/ttdfileprinsiple' : 'approval';
                $dir = public_path($targetDirName);
                if (!is_dir($dir)) {
                    @mkdir($dir, 0777, true);
                }
                $savePath = $dir . DIRECTORY_SEPARATOR . $baseName;
                if (@file_put_contents($savePath, $data)) {
                    return $savePath;
                }
            }
        }

        return null;
    }

    /**
     * Convert an image file path to a base64 Data URI.
     */
    public static function getImageBase64(?string $localPath): ?string
    {
        if (empty($localPath) || !file_exists($localPath) || is_dir($localPath)) {
            return null;
        }

        $size = filesize($localPath);
        if ($size === false || $size < 50) {
            return null;
        }

        $ext = strtolower(pathinfo($localPath, PATHINFO_EXTENSION));
        $mime = match ($ext) {
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            default => 'image/jpeg',
        };

        $content = @file_get_contents($localPath);
        if ($content === false || empty($content)) {
            return null;
        }

        return 'data:' . $mime . ';base64,' . base64_encode($content);
    }

    /**
     * Helper to fetch remote file content with timeouts and SSL bypass.
     */
    protected static function fetchRemoteFile(string $url): ?string
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 8);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) ASystem/3.0');

        $data = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && is_string($data) && strlen($data) > 100) {
            // Check if it's not an HTML error page
            if (str_starts_with(trim($data), '<!DOCTYPE') || str_starts_with(trim($data), '<html')) {
                return null;
            }
            return $data;
        }

        return null;
    }
}
