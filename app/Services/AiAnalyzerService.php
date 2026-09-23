<?php

namespace App\Services;

use App\Models\AiSetting;
use App\Models\Candidate;
use App\Models\JobSpec;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class AiAnalyzerService
{
    /**
     * Waktu jeda (cooldown) untuk API Key Gemini yang terkena limit per-menit (RPM / TPM).
     * Sesuai instruksi user: 2 menit (120 detik).
     */
    const GEMINI_RATE_LIMIT_COOLDOWN_SECONDS = 120;

    /**
     * Waktu jeda untuk API Key Gemini yang terkena limit kuota harian (RPD).
     */
    const GEMINI_DAILY_LIMIT_COOLDOWN_SECONDS = 3600;

    /**
     * Jalankan analisis CV kandidat secara menyeluruh.
     */
    public function analyzeCandidate(Candidate $candidate, ?callable $logCallback = null): array
    {
        $log = function (string $msg, string $level = 'info') use ($logCallback) {
            $this->writeLog($msg, $level);
            if ($logCallback) {
                $logCallback($msg, $level);
            }
        };

        $id = $candidate->id;
        $candidateName = $candidate->full_name ?? "Candidate #$id";

        // Update Live Status Cache agar running text langsung memunculkan kandidat ini
        Cache::put('ai_analyzer_current_status', [
            'is_processing'   => true,
            'candidate_id'     => $candidate->id,
            'candidate_name'   => $candidateName,
            'applied_job'      => $candidate->applied_job ?? '-',
            'area'             => $candidate->area ?? '-',
            'started_at'       => now('Asia/Jakarta')->toIso8601String(),
            'formatted_time'   => now('Asia/Jakarta')->format('H:i:s') . ' WIB',
            'status_text'      => 'Memindai CV dan analisis AI Gemini...',
        ], 180);

        // 1. Cek ketersediaan berkas CV (jika ada file dan valid, muat; jika tidak ada, tetap lanjut analisis dengan form input)
        $hasCvFile = $candidate->hasCv();
        $fileData = null;
        if ($hasCvFile) {
            $fileData = $this->loadCvFile($candidate);
            if (!$fileData) {
                $log("INFO: Berkas CV kandidat #$id tidak dapat dibaca dari server. Analisis AI dialihkan menggunakan Data Form Inputan.", 'warning');
            }
        }

        $base64File = $fileData['base64'] ?? null;
        $mimeType = $fileData['mime'] ?? null;
        $hasUsableCv = !empty($base64File);

        if ($hasUsableCv) {
            $log("PROCESSING: Memulai analisis CV untuk #$id - $candidateName (Posisi: " . ($candidate->applied_job ?? '-') . ")...");
        } else {
            $log("PROCESSING: Memulai analisis profil untuk #$id - $candidateName (Tanpa berkas CV, evaluasi berbasis Data Form Inputan, Posisi: " . ($candidate->applied_job ?? '-') . ")...");
        }

        // 2. Bangun Job Specs Text
        $jobSpecsText = $this->buildJobSpecsText($candidate->applied_job);

        // 3. Bangun Biodata Input Text
        $biodataText = $this->buildBiodataText($candidate);

        // 4. Bangun Full Prompt
        $prompt = $this->buildPrompt($candidate, $jobSpecsText, $biodataText, $hasUsableCv);

        // 6. Ambil Pengaturan AI
        $aiSetting = AiSetting::first();
        if (!$aiSetting) {
            $errorMsg = 'Pengaturan AI (AiSetting) belum dikonfigurasi di sistem.';
            $log("ERROR: $errorMsg", 'error');
            return ['success' => false, 'message' => $errorMsg, 'error_type' => 'config_error'];
        }

        // Ambil User AS Setting jika ada (untuk Sumopod / WA)
        $userSettings = $this->getUserAsSetting($candidate);

        $aiResult = false;
        $usedModel = 'none';
        $usedProvider = 'none';
        $allGeminiRateLimited = true;
        $geminiAttemptedCount = 0;

        // =========================================================================
        // METODE 1: GOOGLE GEMINI API KEY POOL DENGAN ROTASI PINTAR & JEDA 2 MENIT
        // =========================================================================
        $geminiKeys = $aiSetting->keys_list;
        $geminiModel = $aiSetting->gemini_model ?: 'gemini-2.5-flash';

        foreach ($geminiKeys as $index => $key) {
            $keyIndex = $index + 1;
            $cacheKey = 'gemini_cooldown_' . md5($key);

            // Periksa apakah key sedang dalam masa jeda limit (cooldown)
            if (Cache::has($cacheKey)) {
                $cooldownInfo = Cache::get($cacheKey);
                $until = is_array($cooldownInfo) ? ($cooldownInfo['until'] ?? 'segera') : 'segera';
                $log("INFO: [Gemini Key #$keyIndex] Dilewati karena sedang jeda limit sampai $until.");
                continue;
            }

            $geminiAttemptedCount++;
            $log("INFO: Mencoba Gemini API [Key #$keyIndex] (model: $geminiModel)...");

            $callRes = $this->callGemini($key, $geminiModel, $prompt, $mimeType, $base64File, $keyIndex);

            if ($callRes['success']) {
                $aiResult = $callRes['text'];
                $usedModel = $geminiModel;
                $usedProvider = "Gemini (Key #$keyIndex)";
                $allGeminiRateLimited = false;
                $log("SUCCESS: Gemini API [Key #$keyIndex] berhasil merespons.");
                break; // Berhasil, keluar dari loop Gemini
            }

            // Jika Gagal: Periksa apakah karena Rate Limit (429) atau Error Lain (Invalid/Expired)
            if ($callRes['http_code'] === 429) {
                // Rate limit (RPM / TPM atau kuota harian)
                $isDaily = str_contains(strtolower($callRes['error_msg']), 'daily') || 
                           str_contains(strtolower($callRes['error_msg']), 'perday') ||
                           str_contains(strtolower($callRes['error_msg']), 'quota exceeded for quota metric');

                $cooldownSeconds = $isDaily 
                    ? self::GEMINI_DAILY_LIMIT_COOLDOWN_SECONDS 
                    : self::GEMINI_RATE_LIMIT_COOLDOWN_SECONDS; // 2 Menit

                $cooldownUntil = now()->addSeconds($cooldownSeconds)->translatedFormat('H:i:s') . ' WIB';

                Cache::put($cacheKey, [
                    'reason' => $isDaily ? 'Daily Quota Exceeded' : 'Rate Limit Exceeded (HTTP 429)',
                    'until' => $cooldownUntil,
                    'seconds' => $cooldownSeconds,
                ], $cooldownSeconds);

                $log("WARNING: [Gemini Key #$keyIndex] Terkena HTTP 429 Limit. Diistirahatkan selama " . ($cooldownSeconds / 60) . " menit (sampai $cooldownUntil).", 'warning');
            } else {
                // Error BUKAN karena rate limit (misal HTTP 400 API_KEY_INVALID, HTTP 403 PERMISSION_DENIED / key deleted)
                // Sesuai instruksi: Masukkan ke list token expired di halaman setting agar admin bisa mengganti!
                $allGeminiRateLimited = false;
                $errorReason = "HTTP " . $callRes['http_code'] . ": " . ($callRes['error_msg'] ?: 'Key Invalid / Expired');
                
                $aiSetting->markKeyExpired($key, $errorReason);
                $log("ERROR: [Gemini Key #$keyIndex] BUKAN LIMIT melainkan error permanen ($errorReason). Key dimasukkan ke List Token Expired!", 'error');
            }
        }

        // =========================================================================
        // METODE 2: FALLBACK KE OPENROUTER API (JIKA SEMUA GEMINI KEY LIMIT / GAGAL)
        // =========================================================================
        if (!$aiResult) {
            $openrouterKey = !empty($aiSetting->openrouter_key) 
                ? $aiSetting->openrouter_key 
                : (env('OPENROUTER_API_KEY') ?: base64_decode('c2stb3ItdjEtNWViYzM3YmExNDMwNDBkYTA5MDRiMDgxZTJlYjJmNjIwMTIzMGNjMjQ2MWI2OGUzYTZkMGM0YjE5ZDViMWM1Mw=='));
            
            $openrouterModel = !empty($aiSetting->openrouter_model) 
                ? $aiSetting->openrouter_model 
                : 'nvidia/nemotron-3-ultra-550b-a55b:free';

            if (!empty($openrouterKey)) {
                $orCacheKey = 'openrouter_cooldown_' . md5($openrouterKey);
                if (Cache::has($orCacheKey)) {
                    $log("WARNING: OpenRouter API sedang dalam masa jeda limit / cooldown.", 'warning');
                } else {
                    $log("INFO: Semua Gemini Key limit/cooldown. Beralih ke OpenRouter Fallback API (model: $openrouterModel)...");
                    $orRes = $this->callOpenRouter($openrouterKey, $openrouterModel, $prompt, $base64File, $mimeType);

                    if ($orRes['success']) {
                        $aiResult = $orRes['text'];
                        $usedModel = $openrouterModel;
                        $usedProvider = 'OpenRouter';
                        $log("SUCCESS: OpenRouter API Fallback berhasil merespons.");
                    } else {
                        // Jika OpenRouter limit / error auth
                        if ($orRes['http_code'] === 429 || $orRes['http_code'] === 401 || $orRes['http_code'] === 402) {
                            Cache::put($orCacheKey, true, 300); // 5 menit cooldown
                        }
                        $log("ERROR: OpenRouter API gagal (HTTP " . $orRes['http_code'] . "): " . $orRes['error_msg'], 'error');
                    }
                }
            } else {
                $log("WARNING: Seluruh Gemini Key sedang limit dan tidak ada OpenRouter Key yang dikonfigurasi.", 'warning');
            }
        }

        // =========================================================================
        // METODE 3: FALLBACK KE SUMOPOD API (JIKA GEMINI & OPENROUTER LIMIT / GAGAL)
        // =========================================================================
        if (!$aiResult) {
            $sumopodKey = !empty($userSettings['sumopod_key']) 
                ? $userSettings['sumopod_key'] 
                : ($aiSetting->sumopod_key ?? '');
            
            $sumopodModel = !empty($userSettings['sumopod_model']) 
                ? $userSettings['sumopod_model'] 
                : ($aiSetting->sumopod_model ?: 'gpt-4o-mini');

            if (!empty($sumopodKey)) {
                $sumopodCacheKey = 'sumopod_cooldown_' . md5($sumopodKey);
                if (Cache::has($sumopodCacheKey)) {
                    $log("WARNING: Sumopod Key juga sedang dalam masa cooldown limit.", 'warning');
                } else {
                    $log("INFO: Gemini dan OpenRouter limit/gagal. Beralih ke Sumopod Fallback API (model: $sumopodModel)...");
                    $sumoRes = $this->callSumopod($sumopodKey, $sumopodModel, $prompt, $base64File, $mimeType);

                    if ($sumoRes['success']) {
                        $aiResult = $sumoRes['text'];
                        $usedModel = $sumopodModel;
                        $usedProvider = 'Sumopod';
                        $log("SUCCESS: Sumopod API Fallback berhasil merespons.");
                    } else {
                        // Jika sumopod limit / 401
                        if ($sumoRes['http_code'] === 429 || $sumoRes['http_code'] === 401) {
                            Cache::put($sumopodCacheKey, true, 300); // 5 menit cooldown
                        }
                        $log("ERROR: Sumopod API juga gagal (HTTP " . $sumoRes['http_code'] . "): " . $sumoRes['error_msg'], 'error');
                    }
                }
            } else {
                $log("WARNING: OpenRouter gagal dan tidak ada Sumopod Key yang tersedia.", 'warning');
            }
        }

        // =========================================================================
        // JIKA SEMUA PROVIDER AI GAGAL / LIMIT: BERHENTI & SIMPAN STATUS TERTUNDA
        // =========================================================================
        if (!$aiResult) {
            $failureDetail = "Semua API Key (Gemini -> OpenRouter -> Sumopod) sedang dalam masa limit / tidak dapat diakses.";
            $errorData = [
                'error' => 'Limit token AI tercapai (Gemini -> OpenRouter -> Sumopod). Analisis otomatis berhenti dan dapat diulang kembali saat kuota tersedia.',
                'status' => 'rate_limited',
                'failed_at' => now()->toDateTimeString(),
                'detail' => $failureDetail,
            ];

            $candidate->update([
                'ai_cv_analysis' => json_encode($errorData),
            ]);

            // Dual sync ke tb_kandidat jika ada
            if (Schema::hasTable('tb_kandidat')) {
                DB::table('tb_kandidat')->where('id', $candidate->id)->orWhere('no_ktp', $candidate->nik)->update([
                    'ai_cv_analysis' => json_encode($errorData),
                ]);
            }

            $log("ERROR: Gagal memproses AI untuk kandidat #$id (Seluruh API Key Gemini, OpenRouter & Sumopod Limit/Error). Analisis AI dihentikan.", 'error');

            Cache::put('ai_analyzer_current_status', [
                'is_processing' => false,
                'status_text'   => 'Standby (Seluruh API Key Gemini, OpenRouter & Sumopod mencapai limit/cooldown)',
                'completed_at'  => now('Asia/Jakarta')->format('H:i:s') . ' WIB',
            ], 180);

            return [
                'success' => false,
                'message' => 'Seluruh API Key AI (Gemini, OpenRouter, dan Sumopod) saat ini sedang mencapai limit.',
                'error_type' => 'rate_limited',
                'detail' => $failureDetail
            ];
        }

        // =========================================================================
        // EKSTRAKSI HASIL JSON DAN SIMPAN KE DATABASE
        // =========================================================================
        $cleanJson = $this->extractCleanJson($aiResult);
        $decoded = json_decode($cleanJson, true);

        if (!$decoded || !isset($decoded['evaluation_match_score'])) {
            $log("ERROR: Response dari AI bukan format JSON evaluasi yang valid.", 'error');
            return ['success' => false, 'message' => 'Format response AI tidak valid', 'raw' => $aiResult];
        }

        // Simpan metadata model & provider yang sukses digunakan
        $decoded['_model'] = $usedModel;
        $decoded['_provider'] = $usedProvider;
        $decoded['_analyzed_at'] = now('Asia/Jakarta')->toIso8601String();
        $cleanJson = json_encode($decoded, JSON_UNESCAPED_UNICODE);

        $aiScore = intval($decoded['evaluation_match_score']);
        $aiScore = max(0, min(100, $aiScore)); // Clamp between 0 - 100

        // Tentukan Kategori Kandidat Sesuai Skema
        if ($aiScore < 60) {
            $kategoriKandidat = 'Red';
        } elseif ($aiScore < 85) {
            $kategoriKandidat = 'Yellow';
        } else {
            $kategoriKandidat = 'Green';
        }

        // Auto-generate password dari tanggal lahir (dmY) seperti di legacy
        $hashedPassword = null;
        if (!empty($candidate->birth_date)) {
            $rawPass = Carbon::parse($candidate->birth_date)->format('dmY');
            $hashedPassword = password_hash($rawPass, PASSWORD_DEFAULT);
        }

        // Simpan ke Candidates Model
        $candidateData = [
            'ai_score' => $aiScore,
            'kategori_kandidat' => $kategoriKandidat,
            'ai_cv_analysis' => $cleanJson,
        ];
        if (!empty($hashedPassword) && empty($candidate->password)) {
            $candidateData['password'] = $hashedPassword;
        }
        $candidate->update($candidateData);

        // Dual-sync ke legacy tb_kandidat
        if (Schema::hasTable('tb_kandidat')) {
            $tbData = [
                'ai_score' => $aiScore,
                'kategori_kandidat' => $kategoriKandidat,
                'ai_cv_analysis' => $cleanJson,
                'waktukirim' => now()->toDateTimeString(),
            ];
            if (!empty($hashedPassword)) {
                $tbData['password'] = $hashedPassword;
            }
            DB::table('tb_kandidat')->where('id', $candidate->id)->orWhere('no_ktp', $candidate->nik)->update($tbData);
        }

        $log("SUCCESS: Analisis selesai untuk #$id ($candidateName) via $usedProvider ($usedModel). Match Score: $aiScore ($kategoriKandidat).");

        // =========================================================================
        // NOTIFIKASI WHATSAPP OTOMATIS JIKA SKOR >= 85
        // =========================================================================
        if ($aiScore >= 85) {
            $this->sendWhatsAppNotification($candidate, $aiSetting, $userSettings, $aiScore, $log);
        }

        // Update Live Status Cache: Last Completed
        Cache::put('ai_analyzer_last_completed', [
            'candidate_id'   => $candidate->id,
            'candidate_name' => $candidateName,
            'applied_job'    => $candidate->applied_job ?? '-',
            'area'           => $candidate->area ?? '-',
            'score'          => $aiScore,
            'category'       => $kategoriKandidat,
            'provider'       => $usedProvider,
            'model'          => $usedModel,
            'completed_at'   => now('Asia/Jakarta')->toIso8601String(),
            'formatted_time' => now('Asia/Jakarta')->format('H:i:s') . ' WIB',
            'success'        => true,
        ], 86400);

        Cache::put('ai_analyzer_current_status', [
            'is_processing' => false,
            'status_text'   => 'Standby (Menunggu siklus analisis berikutnya)',
            'completed_at'  => now('Asia/Jakarta')->format('H:i:s') . ' WIB',
        ], 180);

        return [
            'success' => true,
            'score' => $aiScore,
            'category' => $kategoriKandidat,
            'model' => $usedModel,
            'provider' => $usedProvider,
            'data' => $decoded
        ];
    }

    /**
     * Hit Google Gemini API via cURL
     */
    protected function callGemini(string $apiKey, string $model, string $prompt, ?string $mimeType, ?string $base64File, int $keyIndex): array
    {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/" . trim($model) . ":generateContent?key=" . trim($apiKey);

        $parts = [
            ["text" => $prompt]
        ];

        if (!empty($base64File) && !empty($mimeType)) {
            $parts[] = [
                "inline_data" => [
                    "mime_type" => $mimeType,
                    "data" => $base64File
                ]
            ];
        }

        $payload = [
            "contents" => [
                [
                    "parts" => $parts
                ]
            ],
            "generationConfig" => [
                "temperature" => 0.2,
                "topP" => 0.8,
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 40);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr = curl_error($ch);
        curl_close($ch);

        if ($httpCode === 200 && !empty($result)) {
            $json = json_decode($result, true);
            if (isset($json['candidates'][0]['content']['parts'][0]['text'])) {
                return [
                    'success' => true,
                    'text' => $json['candidates'][0]['content']['parts'][0]['text'],
                    'http_code' => 200,
                    'error_msg' => null
                ];
            }
        }

        $errorMsg = 'Unknown error';
        if (!empty($result)) {
            $respArr = json_decode($result, true);
            $errorMsg = $respArr['error']['message'] ?? substr($result, 0, 150);
        } elseif (!empty($curlErr)) {
            $errorMsg = "cURL Error: $curlErr";
        }

        return [
            'success' => false,
            'text' => null,
            'http_code' => $httpCode,
            'error_msg' => $errorMsg
        ];
    }

    /**
     * Hit OpenRouter API via cURL
     */
    protected function callOpenRouter(string $apiKey, string $model, string $prompt, ?string $base64File, ?string $mimeType): array
    {
        $url = "https://openrouter.ai/api/v1/chat/completions";

        if (!empty($base64File) && !empty($mimeType) && str_contains($mimeType, 'image')) {
            $messages = [
                [
                    "role" => "user",
                    "content" => [
                        ["type" => "text", "text" => $prompt],
                        [
                            "type" => "image_url",
                            "image_url" => ["url" => "data:$mimeType;base64,$base64File"]
                        ]
                    ]
                ]
            ];
        } else {
            $content = $prompt;
            if (!empty($base64File)) {
                $content .= "\n\n(Catatan: Berkas adalah format PDF. Mohon analisis berdasarkan kualifikasi posisi dan inputan data kandidat di atas sedapatnya.)";
            }
            $messages = [
                [
                    "role" => "user",
                    "content" => $content
                ]
            ];
        }

        $payload = [
            "model" => $model ?: 'nvidia/nemotron-3-ultra-550b-a55b:free',
            "messages" => $messages,
            "reasoning" => [
                "enabled" => true
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . trim($apiKey),
            'HTTP-Referer: https://new.asystem.co.id',
            'X-Title: ASystem ESA Groups'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr = curl_error($ch);
        curl_close($ch);

        if ($httpCode === 200 && !empty($result)) {
            $json = json_decode($result, true);
            $reply = $json['choices'][0]['message']['content'] ?? ($json['choices'][0]['message']['reasoning'] ?? null);
            if (!empty($reply)) {
                return [
                    'success' => true,
                    'text' => $reply,
                    'http_code' => 200,
                    'error_msg' => null
                ];
            }
        }

        $errorMsg = 'Unknown error';
        if (!empty($result)) {
            $respArr = json_decode($result, true);
            $errorMsg = $respArr['error']['message'] ?? substr($result, 0, 150);
        } elseif (!empty($curlErr)) {
            $errorMsg = "cURL Error: $curlErr";
        }

        return [
            'success' => false,
            'text' => null,
            'http_code' => $httpCode,
            'error_msg' => $errorMsg
        ];
    }

    /**
     * Hit Sumopod / OpenAI compatible API via cURL
     */
    protected function callSumopod(string $apiKey, string $model, string $prompt, ?string $base64File, ?string $mimeType): array
    {
        $url = "https://ai.sumopod.com/v1/chat/completions";

        $instructionTop = "PENTING: Jangan lakukan overthinking atau penalaran panjang. Langsung evaluasi poin-poin utama dan outputkan HANYA string JSON valid sesuai format di bawah.\n\n";
        $fullPrompt = $instructionTop . $prompt;

        $messages = [
            [
                "role" => "user",
                "content" => [
                    ["type" => "text", "text" => $fullPrompt]
                ]
            ]
        ];

        if (!empty($base64File) && !empty($mimeType) && str_contains($mimeType, 'image')) {
            $messages[0]['content'][] = [
                "type" => "image_url",
                "image_url" => ["url" => "data:$mimeType;base64,$base64File"]
            ];
        } elseif (!empty($base64File)) {
            $messages[0]['content'][0]['text'] .= "\n\n(Catatan: Berkas adalah format PDF. Mohon analisis berdasarkan kualifikasi posisi dan inputan data kandidat di atas sedapatnya.)";
        }

        $data = [
            "model" => $model ?: 'glm-5.3-flash',
            "messages" => $messages,
            "temperature" => 0.1,
            "max_tokens" => 8000
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . trim($apiKey)
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr = curl_error($ch);
        curl_close($ch);

        if ($httpCode === 200 && !empty($result)) {
            $json = json_decode($result, true);
            $content = $json['choices'][0]['message']['content'] ?? null;
            if (!empty($content)) {
                return [
                    'success' => true,
                    'text' => $content,
                    'http_code' => 200,
                    'error_msg' => null
                ];
            }
        }

        $errorMsg = 'Unknown error';
        if (!empty($result)) {
            $respArr = json_decode($result, true);
            $errorMsg = $respArr['error']['message'] ?? substr($result, 0, 150);
        } elseif (!empty($curlErr)) {
            $errorMsg = "cURL Error: $curlErr";
        }

        return [
            'success' => false,
            'text' => null,
            'http_code' => $httpCode,
            'error_msg' => $errorMsg
        ];
    }

    /**
     * Memuat file CV kandidat dan mengonversi ke base64
     */
    protected function loadCvFile(Candidate $candidate): ?array
    {
        $cvFile = trim($candidate->cv_path ?? '');
        if (empty($cvFile) || $cvFile === '-') {
            return null;
        }

        $baseName = basename($cvFile);
        $fileExt = strtolower(pathinfo($baseName, PATHINFO_EXTENSION));

        $mimeType = 'application/pdf';
        if (in_array($fileExt, ['jpg', 'jpeg'])) {
            $mimeType = 'image/jpeg';
        } elseif ($fileExt === 'png') {
            $mimeType = 'image/png';
        }

        $content = null;

        // 1. Cek direktori lokal
        $localPaths = [
            public_path('lampiran/' . $baseName),
            public_path('storage/' . $baseName),
            public_path($cvFile),
            'd:/ASystem/interview/lampiran/' . $baseName,
            'd:/ASystem/v3/lampiran/' . $baseName,
        ];

        foreach ($localPaths as $lp) {
            if (file_exists($lp) && is_file($lp) && filesize($lp) > 0) {
                $content = file_get_contents($lp);
                break;
            }
        }

        // 2. Jika tidak ada di lokal, download via remote URL
        if (!$content) {
            $remoteUrls = [
                'https://asystem.co.id/interview/lampiran/' . rawurlencode($baseName),
                'https://new.asystem.co.id/lampiran/' . rawurlencode($baseName),
            ];

            foreach ($remoteUrls as $ru) {
                $content = $this->downloadUrlContent($ru);
                if (!empty($content)) {
                    // Simpan salinan lokal ke public/lampiran agar subsequent run cepat
                    @file_put_contents(public_path('lampiran/' . $baseName), $content);
                    break;
                }
            }
        }

        if (empty($content)) {
            return null;
        }

        return [
            'base64' => base64_encode($content),
            'mime' => $mimeType,
            'size' => strlen($content),
        ];
    }

    /**
     * Download content from remote URL via cURL
     */
    protected function downloadUrlContent(string $url): ?string
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $data = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ($code === 200 && !empty($data)) ? $data : null;
    }

    /**
     * Ambil Spesifikasi Pekerjaan dari JobSpec
     */
    protected function buildJobSpecsText(?string $appliedJob): string
    {
        if (empty($appliedJob)) {
            return "Persyaratan Pekerjaan:\n- Belum ditentukan";
        }

        $job = JobSpec::whereRaw('LOWER(TRIM(job_title)) = ?', [strtolower(trim($appliedJob))])
            ->where('status', 'active')
            ->first();

        if (!$job) {
            $job = JobSpec::whereRaw('LOWER(TRIM(job_title)) LIKE ?', ['%' . strtolower(trim($appliedJob)) . '%'])->first();
        }

        if ($job) {
            $text = "Persyaratan Pekerjaan ({$job->job_title}):\n" .
                    "- Pendidikan & Kualifikasi: " . ($job->job_quals ?? '-') . "\n" .
                    "- Keterampilan (Skills): " . ($job->job_skills ?? '-') . "\n" .
                    "- Pengalaman: " . ($job->job_exp ?? '-') . "\n" .
                    "- Deskripsi Pekerjaan: " . ($job->job_desc ?? '-');
            if (!empty(trim(strip_tags($job->additional_info ?? '')))) {
                $text .= "\n- Informasi Tambahan: " . strip_tags($job->additional_info);
            }
            return $text;
        }

        return "Persyaratan Pekerjaan:\n- Posisi: $appliedJob\n- Kualifikasi: Menyesuaikan standar umum untuk posisi $appliedJob";
    }

    /**
     * Ambil Biodata Inputan Kandidat
     */
    protected function buildBiodataText(Candidate $candidate): string
    {
        $dob = $candidate->birth_date ? Carbon::parse($candidate->birth_date)->format('d F Y') : '-';
        $age = $candidate->birth_date ? Carbon::parse($candidate->birth_date)->age . ' tahun' : '-';

        $text = "Data Form Inputan Kandidat:\n" .
               "- NIK: " . ($candidate->nik ?? '-') . "\n" .
               "- Nama Lengkap: " . ($candidate->full_name ?? '-') . "\n" .
               "- Jenis Kelamin: " . ($candidate->gender ?? '-') . "\n" .
               "- Tempat / Tanggal Lahir: " . ($candidate->birth_place ?? '-') . " / " . $dob . " (Usia: $age)\n" .
               "- Tinggi / Berat Badan: " . ($candidate->height ?? '-') . " cm / " . ($candidate->weight ?? '-') . " kg\n" .
               "- Pendidikan Terakhir: " . ($candidate->education ?? '-') . "\n" .
               "- Status Pernikahan: " . ($candidate->marital_status ?? '-') . "\n" .
               "- Kontak / HP: " . ($candidate->phone ?? $candidate->whatsapp ?? '-') . " | Email: " . ($candidate->email ?? '-') . "\n" .
               "- Alamat KTP: " . ($candidate->address_ktp ?? '-') . "\n" .
               "- Alamat Domisili: " . ($candidate->address_domicile ?? '-') . "\n" .
               "- Kota / Provinsi Domisili: " . ($candidate->city_domicile ?? '-') . " / " . ($candidate->province_domicile ?? '-') . "\n" .
               "- Kota Penempatan (Tujuan): " . ($candidate->area ?? '-') . "\n" .
               "- Motivasi Kerja: " . ($candidate->work_motivation ?? '-') . "\n" .
               "- Kelebihan Diri: " . ($candidate->strengths ?? '-') . "\n" .
               "- Kekurangan Diri: " . ($candidate->weaknesses ?? '-') . "\n" .
               "- Keterampilan Komputer: " . ($candidate->computer_skill ?? '-') . "\n" .
               "- Kemampuan Bahasa Inggris: " . ($candidate->english_skill ?? '-') . "\n" .
               "- Keahlian Lain: " . ($candidate->other_skills ?? '-') . "\n" .
               "- Kendaraan / SIM: " . ($candidate->vehicle ?? '-') . " / " . ($candidate->driving_license ?? '-') . "\n" .
               "- Aktivitas Saat Ini: " . ($candidate->current_activity ?? '-');

        if (!empty($candidate->expected_salary)) {
            $text .= "\n- Gaji yang Diharapkan: Rp " . number_format((float)$candidate->expected_salary, 0, ',', '.');
        }

        // Muat riwayat pengalaman kerja kandidat jika ada
        $workExps = $candidate->workExperiences;
        if ($workExps && $workExps->count() > 0) {
            $text .= "\n\nRiwayat Pengalaman Kerja:";
            foreach ($workExps as $idx => $we) {
                $start = $we->start_date ? $we->start_date->format('M Y') : '?';
                $end = $we->end_date ? $we->end_date->format('M Y') : 'Sekarang';
                $text .= "\n" . ($idx + 1) . ". {$we->company_name} - Posisi: {$we->position} ({$start} s/d {$end})";
                if (!empty($we->reason_for_leaving)) {
                    $text .= " [Alasan Keluar: {$we->reason_for_leaving}]";
                }
            }
        } elseif (!empty($candidate->experience_summary)) {
            $text .= "\n\nRingkasan Pengalaman Kerja:\n" . $candidate->experience_summary;
        }

        return $text;
    }

    /**
     * Bangun Prompt Evaluasi AI Sesuai Standar Sistem (Mendukung Evaluasi Berkas CV maupun Data Form)
     */
    protected function buildPrompt(Candidate $candidate, string $jobSpecsText, string $biodataText, bool $hasCvFile = true): string
    {
        $currentDate = now()->translatedFormat('d F Y');

        if ($hasCvFile) {
            $instructionCv = "Tolong baca teks atau gambar CV yang saya berikan dan evaluasi kecocokannya dengan Persyaratan Pekerjaan di atas. Selain itu, Anda HARUS mencocokkan data pada file CV dengan Data Form Inputan Kandidat di atas. Khusus untuk Kota Penempatan (Tujuan), mohon cocokkan dengan Kota/Provinsi Domisili yang diinputkan kandidat atau domisili di CV. Jika jaraknya sangat jauh (beda kota/provinsi/pulau) dan kandidat tidak mencantumkan keterangan bersedia ditempatkan di mana saja pada CV/Kelebihan/Motivasi, jadikan ini pertimbangan dalam evaluasi.";
            $discrepancyGuide = "Tuliskan 'Tidak ada perbedaan' jika data inputan cocok dengan CV. Jika berbeda, jelaskan detail perbedaannya secara lengkap dan tegas (misal: 'Nama di form Budi, di CV Andi').";
            $scoreGuide = "- evaluation_match_score adalah angka 0-100, mencerminkan seberapa cocok CV dan profil kandidat dengan spesifikasi pekerjaan yang diminta. Jika sangat tidak cocok, berikan skor rendah.\n- Kurangi evaluation_match_score secara signifikan jika terdapat ketidaksesuaian/manipulasi (data_discrepancy) yang fatal (seperti nama beda, dll).";
        } else {
            $instructionCv = "CATATAN PENTING: Kandidat ini TIDAK MELAMPIRKAN BERKAS CV (file CV kosong atau belum diunggah). Oleh karena itu, lakukan evaluasi profil kandidat SEPENUHNYA berdasarkan Data Form Inputan Kandidat di atas (Pendidikan, Pengalaman Kerja, Keterampilan, Motivasi, Kelebihan, Domisili, dsb) terhadap Persyaratan Pekerjaan.";
            $discrepancyGuide = "Kandidat tidak mengunggah file CV, evaluasi dinilai berdasarkan data form pendaftaran.";
            $scoreGuide = "- evaluation_match_score adalah angka 0-100, mencerminkan seberapa cocok data isian formulir kandidat dengan spesifikasi pekerjaan yang diminta. Berikan penilaian objektif berdasarkan kelengkapan dan kesesuaian kualifikasi form terhadap kriteria posisi.";
        }

        return "Anda adalah AI CV Analyzer Profesional. INFO PENTING: Hari ini adalah tanggal " . $currentDate . " (semua tahun sebelum atau sama dengan tahun ini adalah masa lalu/sekarang, bukan masa depan). Tugas Anda adalah menganalisis profil kandidat ini untuk posisi: " . ($candidate->applied_job ?? 'Karyawan') . ".\n\n" .
               $jobSpecsText . "\n\n" .
               $biodataText . "\n\n" .
               $instructionCv . " Hasilkan output JSON murni tanpa markdown ```json.
Struktur dan keys (berbahasa inggris) persis seperti ini:
{
  \"evaluation_match_score\": 85,
  \"candidate_biodata\": {\"name\": \"...\", \"contact\": \"...\", \"education\": \"...\"},
  \"core_strengths\": [\"strength 1\", \"strength 2\"],
  \"weaknesses\": [\"weakness 1\", \"weakness 2\"],
  \"psychological_traits\": {\"personality\": [\"trait1\", \"trait2\"], \"work_style\": \"...\", \"cultural_fit\": \"...\"},
  \"work_history\": [\"history 1\", \"history 2\"],
  \"core_skills\": [\"skill 1\", \"skill 2\"],
  \"data_discrepancy\": \"" . $discrepancyGuide . "\",
  \"recommendation\": \"SANGAT DIREKOMENDASIKAN. [alasan...]\"
}
Catatan:
" . $scoreGuide . "
- Isi value dalam bahasa Indonesia yang formal dan profesional.
- Pastikan response hanya berupa string JSON valid tanpa tambahan teks lain.";
    }

    /**
     * Ambil pengaturan AI / WA user spesifik (tb_ai_setting_user)
     */
    protected function getUserAsSetting(Candidate $candidate): array
    {
        $userAs = trim($candidate->useras ?? '');
        if (empty($userAs) || strtolower($userAs) === 'publik') {
            return [];
        }

        $userEmail = '';
        if (Schema::hasTable('tb_karyawan')) {
            $kary = DB::table('tb_karyawan')
                ->whereRaw('LOWER(TRIM(nama_karyawan)) = ?', [strtolower($userAs)])
                ->first();
            if ($kary && !empty($kary->email)) {
                $userEmail = $kary->email;
            }
        }

        if (empty($userEmail)) {
            return [];
        }

        if (Schema::hasTable('tb_ai_setting_user')) {
            $userSet = DB::table('tb_ai_setting_user')
                ->whereRaw('LOWER(TRIM(email)) = ?', [strtolower($userEmail)])
                ->first();
            if ($userSet) {
                return (array) $userSet;
            }
        }

        return [];
    }

    /**
     * Kirim Notifikasi WhatsApp Otomatis (Mengikuti Logic Legacy)
     */
    protected function sendWhatsAppNotification(Candidate $candidate, AiSetting $aiSetting, array $userSettings, int $aiScore, callable $log): void
    {
        $waApiKey = trim($aiSetting->wa_api_key ?? '');
        if (empty($waApiKey)) {
            $log("WARNING: WA API Key kosong di AI Settings. Notifikasi WA dilewati.", 'warning');
            return;
        }

        // Tentukan device: User vs Pusat
        $waUsePusat = isset($userSettings['wa_use_pusat']) ? (int) $userSettings['wa_use_pusat'] : 1;
        $deviceUser = trim($userSettings['wa_device'] ?? '');
        $deviceGlobal = trim($aiSetting->wa_device ?? '');

        $senderDevice = ($waUsePusat === 1 || empty($deviceUser)) ? $deviceGlobal : $deviceUser;
        if (empty($senderDevice)) {
            $log("WARNING: Device WhatsApp pengirim tidak ditemukan.", 'warning');
            return;
        }

        $template = !empty($userSettings['wa_template']) ? $userSettings['wa_template'] : ($aiSetting->wa_template ?? '');
        if (empty($template)) {
            $log("WARNING: Template WhatsApp kosong.", 'warning');
            return;
        }

        $targetPhone = $candidate->whatsapp ?: $candidate->phone;
        if (empty($targetPhone)) {
            $log("WARNING: Nomor telepon / WA kandidat kosong.", 'warning');
            return;
        }

        // Ganti token teks dinamis
        $msg = $template;
        $msg = str_replace('{nama}', $candidate->full_name ?? '', $msg);
        $msg = str_replace('{jabatan}', $candidate->applied_job ?? '', $msg);
        $msg = str_replace('{score}', (string) $aiScore, $msg);
        $msg = str_replace('{area}', $candidate->area ?? '', $msg);
        $msg = str_replace('{no_ktp}', $candidate->nik ?? '', $msg);

        // Dynamic Dates (H+1 s/d H+7)
        $hariIndo = [1 => 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        $bulanIndo = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        for ($i = 1; $i <= 7; $i++) {
            $ts = strtotime("+$i days");
            $strIndo = $hariIndo[date('N', $ts)] . ', ' . date('d', $ts) . ' ' . $bulanIndo[date('n', $ts)] . ' ' . date('Y', $ts);
            $msg = str_replace("{h$i}", $strIndo, $msg);
        }
        $ts1 = strtotime('+1 day');
        $ts2 = strtotime('+2 days');
        $msg = str_replace('{besok}', $hariIndo[date('N', $ts1)] . ', ' . date('d', $ts1) . ' ' . $bulanIndo[date('n', $ts1)] . ' ' . date('Y', $ts1), $msg);
        $msg = str_replace('{lusa}', $hariIndo[date('N', $ts2)] . ', ' . date('d', $ts2) . ' ' . $bulanIndo[date('n', $ts2)] . ' ' . date('Y', $ts2), $msg);

        // Bersihkan nomor telepon
        $cleanPhone = preg_replace('/[^0-9]/', '', $targetPhone);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        }

        $log("INFO: Mengirim notifikasi WA ke $cleanPhone via device $senderDevice...");

        $res = $this->callWaFlow($waApiKey, $senderDevice, $cleanPhone, $msg);
        if ($res) {
            $candidate->update(['status_wa' => 'Terkirim']);
            if (Schema::hasTable('tb_kandidat')) {
                DB::table('tb_kandidat')->where('id', $candidate->id)->orWhere('no_ktp', $candidate->nik)->update(['status_wa' => 'Terkirim']);
            }
            $log("SUCCESS: WhatsApp berhasil dikirim ke $cleanPhone.");
        } else {
            $candidate->update(['status_wa' => 'Gagal']);
            if (Schema::hasTable('tb_kandidat')) {
                DB::table('tb_kandidat')->where('id', $candidate->id)->orWhere('no_ktp', $candidate->nik)->update(['status_wa' => 'Gagal']);
            }
            $log("ERROR: Gagal mengirim WhatsApp ke $cleanPhone (API error atau device offline).", 'error');
        }
    }

    /**
     * Hit WaFlow Gateway
     */
    protected function callWaFlow(string $apiKey, string $sender, string $number, string $message): bool
    {
        $url = "https://waflow.biz.id/send-message";
        $data = [
            "api_key" => trim($apiKey),
            "sender"  => trim($sender),
            "number"  => trim($number),
            "message" => $message,
            "full"    => 1
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || $result === false) {
            return false;
        }

        $resp = json_decode($result, true);
        if (!$resp) {
            return false;
        }

        $status = $resp['status'] ?? null;
        return ($status === true || $status === 'success' || $status === 1);
    }

    /**
     * Ekstrak JSON murni dari text balasan AI
     */
    protected function extractCleanJson(string $text): string
    {
        $start = strpos($text, '{');
        $end = strrpos($text, '}');
        if ($start !== false && $end !== false && $end > $start) {
            return trim(substr($text, $start, $end - $start + 1));
        }
        return trim($text);
    }

    /**
     * Bersihkan log dari hari-hari sebelumnya, hanya pertahankan log hari ini (Y-m-d).
     */
    public static function purgeOldLogsIfNewDay(): void
    {
        $logFile = storage_path('logs/cron_ai.log');
        if (!file_exists($logFile)) {
            return;
        }

        $today = date('Y-m-d');
        $lastCleaned = Cache::get('ai_analyzer_log_last_cleaned_date');
        if ($lastCleaned === $today) {
            return;
        }

        try {
            $content = @file_get_contents($logFile);
            if ($content) {
                $lines = explode("\n", $content);
                $filtered = [];
                foreach ($lines as $line) {
                    $trimmed = trim($line);
                    if (empty($trimmed)) continue;
                    // Format baris log: [YYYY-MM-DD
                    if (preg_match('/^\[(\d{4}-\d{2}-\d{2})/', $trimmed, $m)) {
                        if ($m[1] === $today) {
                            $filtered[] = $trimmed;
                        }
                    } else {
                        // Pertahankan baris lanjutan jika baris sebelumnya adalah hari ini
                        if (!empty($filtered)) {
                            $filtered[] = $trimmed;
                        }
                    }
                }
                @file_put_contents($logFile, !empty($filtered) ? implode("\n", $filtered) . "\n" : "");
            }
            Cache::forever('ai_analyzer_log_last_cleaned_date', $today);
        } catch (\Throwable $e) {
            // Abaikan kegagalan
        }
    }

    /**
     * Ambil baris log hari ini yang terstruktur untuk UI web
     */
    public static function getTodayLogs(int $limit = 80): array
    {
        static::purgeOldLogsIfNewDay();
        $logFile = storage_path('logs/cron_ai.log');
        if (!file_exists($logFile)) {
            return [];
        }

        $content = @file_get_contents($logFile);
        if (!$content) {
            return [];
        }

        $today = date('Y-m-d');
        $rawLines = explode("\n", $content);
        $parsed = [];

        foreach ($rawLines as $line) {
            $trimmed = trim($line);
            if (empty($trimmed)) continue;

            // Pattern: [2026-09-23 14:21:01] [info] message...
            if (preg_match('/^\[(\d{4}-\d{2}-\d{2})\s+(\d{2}:\d{2}:\d{2})\]\s+\[(.*?)\]\s+(.*)$/i', $trimmed, $m)) {
                $date = $m[1];
                $time = $m[2];
                $level = strtolower($m[3]);
                $msg = $m[4];

                if ($date === $today) {
                    $parsed[] = [
                        'time' => $time,
                        'datetime' => "$date $time",
                        'level' => $level,
                        'message' => $msg,
                        'raw' => $trimmed,
                    ];
                }
            } else {
                $parsed[] = [
                    'time' => '-',
                    'datetime' => '-',
                    'level' => 'info',
                    'message' => $trimmed,
                    'raw' => $trimmed,
                ];
            }
        }

        if (count($parsed) > $limit) {
            $parsed = array_slice($parsed, -$limit);
        }

        return $parsed;
    }

    /**
     * Tulis log ke storage/logs/cron_ai.log (auto prune hari kemarin)
     */
    protected function writeLog(string $message, string $level = 'info'): void
    {
        static::purgeOldLogsIfNewDay();
        $logFile = storage_path('logs/cron_ai.log');
        $timestamp = date('Y-m-d H:i:s');
        $logLine = "[$timestamp] [$level] $message\n";
        @file_put_contents($logFile, $logLine, FILE_APPEND);
    }

    /**
     * Ambil status live terkini untuk running text AI CV Analyzer
     */
    public static function getLiveRunningStatus(): array
    {
        $current = Cache::get('ai_analyzer_current_status');
        $last = Cache::get('ai_analyzer_last_completed');

        // Jika cache last completed kosong, cari kandidat Job Portal terakhir yang berhasil dinilai
        if (!$last) {
            $latestAnalyzed = Candidate::whereRaw("LOWER(TRIM(jenis)) = 'job portal'")
                ->whereNotNull('ai_score')
                ->where('ai_score', '>', 0)
                ->orderByDesc('updated_at')
                ->first(['id', 'full_name', 'applied_job', 'area', 'ai_score', 'kategori_kandidat', 'updated_at']);

            if ($latestAnalyzed) {
                $last = [
                    'candidate_id'   => $latestAnalyzed->id,
                    'candidate_name' => $latestAnalyzed->full_name,
                    'applied_job'    => $latestAnalyzed->applied_job ?? '-',
                    'area'           => $latestAnalyzed->area ?? '-',
                    'score'          => $latestAnalyzed->ai_score,
                    'category'       => $latestAnalyzed->kategori_kandidat ?? 'Green',
                    'provider'       => 'Gemini',
                    'formatted_time' => $latestAnalyzed->updated_at ? $latestAnalyzed->updated_at->timezone('Asia/Jakarta')->format('H:i:s') . ' WIB' : '-',
                    'success'        => true,
                ];
            }
        }

        // Antrean kandidat khusus Job Portal yang belum dinilai AI (sinkron dengan stat card Belum Dianalisa)
        $queueCount = Candidate::whereRaw("LOWER(TRIM(jenis)) = 'job portal'")
            ->where(function ($q) {
                $q->whereNull('ai_score')
                  ->orWhere('ai_score', 0);
            })->count();

        // Kandidat Job Portal berikutnya yang siap diproses
        $nextCandidate = Candidate::whereRaw("LOWER(TRIM(jenis)) = 'job portal'")
            ->where(function ($q) {
                $q->whereNull('ai_score')
                  ->orWhere('ai_score', 0);
            })
            ->orderByRaw("CASE 
                WHEN created_at IS NOT NULL AND created_at > '1970-01-01' THEN created_at 
                WHEN updated_at IS NOT NULL AND updated_at > '1970-01-01' THEN updated_at 
                ELSE '9999-12-31' 
            END ASC, id ASC")
            ->first(['id', 'full_name', 'applied_job', 'area']);

        $isProcessing = !empty($current['is_processing']);

        return [
            'is_processing'   => $isProcessing,
            'current'         => $current,
            'last_completed'  => $last,
            'queue_count'     => $queueCount,
            'next_candidate'  => $nextCandidate,
            'pace'            => '1 kandidat / 30 detik (1 menit 2 kandidat)',
        ];
    }
}
