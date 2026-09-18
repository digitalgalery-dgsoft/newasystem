<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\CandidateImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Exception;

class CandidateImportController extends Controller
{
    protected CandidateImportService $importService;

    public function __construct(CandidateImportService $importService)
    {
        $this->importService = $importService;
    }

    private function getCurrentUser()
    {
        if (auth()->check()) {
            return auth()->user();
        }

        return User::where('email', 'jamil@asystem.co.id')->first()
            ?? User::where('name', 'like', '%abdur%')->first()
            ?? User::where('email', 'like', '%abdur%')->first()
            ?? User::first();
    }

    /**
     * Unduh Template Excel hr.applicant.xlsx
     */
    public function downloadTemplate()
    {
        $templatePath = public_path('templates/template_import_kandidat.xlsx');

        if (!file_exists($templatePath)) {
            $templatePath = storage_path('app/templates/template_import_kandidat.xlsx');
        }

        if (!file_exists($templatePath)) {
            // Coba salin dari file master jika tersedia di sistem
            $masterSource = 'D:/Documents/Downloads/hr.applicant.xlsx';
            if (file_exists($masterSource)) {
                File::ensureDirectoryExists(dirname($templatePath));
                copy($masterSource, $templatePath);
            }
        }

        if (!file_exists($templatePath)) {
            return back()->with('error', 'File template import belum tersedia di server.');
        }

        return response()->download($templatePath, 'hr.applicant.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Upload file Excel sementara untuk diproses via Terminal Streaming
     */
    public function upload(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|max:30720', // Max 30MB
        ]);

        $file = $request->file('excel_file');
        $extension = strtolower($file->getClientOriginalExtension());

        if ($extension !== 'xlsx') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya file berformat .xlsx yang diperbolehkan!',
            ], 422);
        }

        $tempDir = storage_path('app/temp_imports');
        File::ensureDirectoryExists($tempDir);

        // Hapus file lama yang berusia lebih dari 2 jam
        foreach (glob("{$tempDir}/*") as $oldFile) {
            if (is_file($oldFile) && (time() - filemtime($oldFile) > 7200)) {
                @unlink($oldFile);
            }
        }

        $token = 'cand_imp_' . uniqid() . '_' . time();
        $fileName = "{$token}.xlsx";
        $file->move($tempDir, $fileName);

        return response()->json([
            'success'  => true,
            'token'    => $token,
            'filename' => $file->getClientOriginalName(),
            'stream_url' => route('interview.import.stream', ['token' => $token]),
        ]);
    }

    /**
     * SSE Streaming Terminal Endpoint
     */
    public function stream(Request $request)
    {
        $token = preg_replace('/[^a-zA-Z0-9_-]/', '', (string)$request->query('token'));
        $filePath = storage_path("app/temp_imports/{$token}.xlsx");

        $user = $this->getCurrentUser();
        $userEmail = $user ? $user->email : 'recruitment@asystem.co.id';
        $userId = $user ? $user->id : null;

        return response()->stream(function () use ($filePath, $userEmail, $userId) {
            // Nonaktifkan buffering output PHP & webserver
            if (function_exists('apache_setenv')) {
                @apache_setenv('no-gzip', '1');
            }
            @ini_set('zlib.output_compression', '0');
            @ini_set('implicit_flush', '1');
            while (ob_get_level() > 0) {
                @ob_end_flush();
            }
            @ob_implicit_flush(1);
            set_time_limit(0);

            $sendEvent = function(string $type, string $message, ?array $meta = null) {
                $payload = [
                    'time'    => date('H:i:s'),
                    'type'    => $type,
                    'message' => $message,
                    'meta'    => $meta,
                ];
                echo "data: " . json_encode($payload) . "\n\n";
                if (ob_get_level() > 0) {
                    @ob_flush();
                }
                @flush();
            };

            $sendEvent('init', "Memulai konsol terminal import kandidat walkin interview...");
            $sendEvent('info', "Rekruter penanggung jawab: {$userEmail}");

            if (!file_exists($filePath)) {
                $sendEvent('error', "File batch import tidak ditemukan atau sesi upload telah kadaluarsa.");
                $sendEvent('complete', "Import dibatalkan.", ['total' => 0, 'success' => 0, 'failed' => 0]);
                return;
            }

            try {
                $this->importService->import($filePath, $userEmail, $userId, $sendEvent);
            } catch (Exception $e) {
                $sendEvent('error', "Terjadi kesalahan sistem saat memproses file: " . $e->getMessage());
                $sendEvent('complete', "Import selesai dengan galat fatal.", ['error' => $e->getMessage()]);
            } finally {
                // Bersihkan file sementara
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }

        }, 200, [
            'Content-Type'      => 'text/event-stream',
            'Cache-Control'     => 'no-cache, no-transform',
            'Connection'        => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }
}
