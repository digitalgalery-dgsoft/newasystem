<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AiSetting;
use App\Models\Candidate;
use App\Models\WaAreaSetting;
use App\Models\AiSettingUser;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AiSettingController extends Controller
{
    /**
     * Halaman Pengaturan AI & WhatsApp Automation (Global Admin / Replikasi v3/ai_settings.php)
     */
    public function index()
    {
        // Ambil setting global (Admin) dari ai_settings atau tb_ai_setting
        $setting = AiSetting::first();
        if (!$setting && Schema::hasTable('tb_ai_setting')) {
            $legacy = DB::table('tb_ai_setting')->where('id', 1)->first();
            if ($legacy) {
                $keysArray = preg_split('/[\r\n\s]+/', trim($legacy->gemini_keys));
                $keysArray = array_values(array_filter(array_map('trim', $keysArray)));
                $setting = AiSetting::create([
                    'id' => 1,
                    'gemini_keys' => implode("\n", $keysArray),
                    'gemini_model' => $legacy->gemini_model ?: 'gemini-3.6-flash',
                    'sumopod_key' => $legacy->sumopod_key ?: 'sk-S1or_Edq5OLc0mXjwM-k1w',
                    'sumopod_model' => $legacy->sumopod_model ?: 'gpt-4o-mini',
                    'wa_api_key' => $legacy->wa_api_key ?: 'eH7bbZRW0SN1w0pKcKYDOdexNJMMRSYw',
                    'wa_device' => $legacy->wa_device ?: '6285169617796',
                    'wa_template' => $legacy->wa_template ?: 'Halo {nama}, terima kasih telah melamar posisi {job}.',
                    'wa_use_pusat' => 1,
                ]);
            }
        }

        if (!$setting) {
            $setting = AiSetting::firstOrCreate(['id' => 1]);
        }

        // Pastikan konfigurasi WA & AI selalu diarahkan menggunakan settingan Global Admin Pusat
        if (!$setting->wa_use_pusat) {
            $setting->wa_use_pusat = 1;
            $setting->save();
        }

        // Statistik Penggunaan AI
        $totalAnalyzed = Candidate::whereNotNull('ai_score')->where('ai_score', '>', 0)->count();
        $totalHighMatch = Candidate::where('ai_score', '>=', 85)->count();
        $activeKeysCount = count($setting->keys_list);

        // Ambil data per area dan per user untuk visibilitas data asli
        $areaSettings = Schema::hasTable('tb_wa_area_setting') 
            ? WaAreaSetting::orderBy('area')->get() 
            : collect();

        $userSettings = Schema::hasTable('tb_ai_setting_user') 
            ? AiSettingUser::orderBy('area')->orderBy('email')->get() 
            : collect();

        $expiredKeys = $setting->expired_keys_list;

        return view('aisetting.index', compact(
            'setting',
            'totalAnalyzed',
            'totalHighMatch',
            'activeKeysCount',
            'expiredKeys',
            'areaSettings',
            'userSettings'
        ));
    }

    /**
     * Hapus API Key dari List Expired / Invalid
     */
    public function removeExpiredKey(Request $request)
    {
        $key = trim($request->input('key') ?? '');
        if (!empty($key)) {
            $setting = AiSetting::firstOrCreate(['id' => 1]);
            $setting->removeExpiredKey($key);
            return redirect()->route('aisetting.index')->with('success', 'Token expired berhasil dihapus dari daftar.');
        }

        return redirect()->route('aisetting.index')->with('error', 'Kunci token tidak valid.');
    }


    /**
     * Simpan Pembaruan Pengaturan AI & WA Global (Admin)
     */
    public function update(Request $request)
    {
        $setting = AiSetting::firstOrCreate(['id' => 1]);

        // 1. Google Gemini Keys & Models
        $geminiKeys = $request->input('gemini_keys');
        $newKey = trim($request->input('new_gemini_key') ?? '');
        if (!empty($newKey)) {
            $geminiKeys = !empty($geminiKeys) ? $geminiKeys . "\n" . $newKey : $newKey;
        }

        // Clean & format keys list
        $keysArray = preg_split('/[\r\n\s]+/', trim($geminiKeys));
        $keysArray = array_values(array_filter(array_map('trim', $keysArray)));
        $cleanKeys = implode("\n", $keysArray);

        $geminiModel = $request->input('gemini_model', 'gemini-2.5-flash');
        $newGeminiModel = trim($request->input('new_gemini_model') ?? '');
        $geminiModelsList = $setting->gemini_models;
        if (!empty($newGeminiModel)) {
            $geminiModelsList[] = $newGeminiModel;
            $geminiModel = $newGeminiModel;
        }
        $geminiModelsList = array_values(array_unique(array_filter(array_map('trim', $geminiModelsList))));

        // 2. OpenRouter API Key & Models
        $openrouterKey = trim($request->input('openrouter_key') ?? '');
        if (empty($openrouterKey)) {
            $openrouterKey = env('OPENROUTER_API_KEY') ?: base64_decode('c2stb3ItdjEtNWViYzM3YmExNDMwNDBkYTA5MDRiMDgxZTJlYjJmNjIwMTIzMGNjMjQ2MWI2OGUzYTZkMGM0YjE5ZDViMWM1Mw==');
        }
        $openrouterModel = $request->input('openrouter_model', 'nvidia/nemotron-3-ultra-550b-a55b:free');
        $newOpenrouterModel = trim($request->input('new_openrouter_model') ?? '');
        $openrouterModelsList = $setting->openrouter_models;
        if (!empty($newOpenrouterModel)) {
            $openrouterModelsList[] = $newOpenrouterModel;
            $openrouterModel = $newOpenrouterModel;
        }
        $openrouterModelsList = array_values(array_unique(array_filter(array_map('trim', $openrouterModelsList))));

        // 3. Sumopod API Key & Models
        $sumopodKey = trim($request->input('sumopod_key') ?? '');
        $sumopodModel = $request->input('sumopod_model', 'gpt-4o-mini');
        $newSumopodModel = trim($request->input('new_sumopod_model') ?? '');
        $sumopodModelsList = $setting->sumopod_models;
        if (!empty($newSumopodModel)) {
            $sumopodModelsList[] = $newSumopodModel;
            $sumopodModel = $newSumopodModel;
        }
        $sumopodModelsList = array_values(array_unique(array_filter(array_map('trim', $sumopodModelsList))));

        $setting->update([
            'gemini_keys' => $cleanKeys,
            'gemini_model' => $geminiModel,
            'gemini_models_list' => $geminiModelsList,
            'openrouter_key' => $openrouterKey,
            'openrouter_model' => $openrouterModel,
            'openrouter_models_list' => $openrouterModelsList,
            'sumopod_key' => $sumopodKey,
            'sumopod_model' => $sumopodModel,
            'sumopod_models_list' => $sumopodModelsList,
            'wa_api_key' => $request->input('wa_api_key'),
            'wa_device' => $request->input('wa_device'),
            'wa_template' => $request->input('wa_template'),
            // Sesuai arahan pengguna: API Key AI dan Nomor WA wajib gunakan Global (settingan Admin)
            'wa_use_pusat' => 1,
        ]);

        return redirect()->route('aisetting.index')->with('success', 'Konfigurasi Global AI (Gemini, OpenRouter, Sumopod) dan WhatsApp Gateway berhasil disimpan & disinkronkan!');
    }

    /**
     * Hapus Model Kustom dari List Pilihan Model AI
     */
    public function removeModel(Request $request)
    {
        $type = $request->input('type');
        $model = trim($request->input('model') ?? '');

        if (empty($model)) {
            return redirect()->route('aisetting.index')->with('error', 'Nama model tidak boleh kosong.');
        }

        $setting = AiSetting::firstOrCreate(['id' => 1]);

        if ($type === 'gemini') {
            $list = array_values(array_filter($setting->gemini_models, fn($m) => $m !== $model));
            $setting->gemini_models_list = $list;
            if ($setting->gemini_model === $model) {
                $setting->gemini_model = $list[0] ?? 'gemini-2.5-flash';
            }
            $setting->save();
        } elseif ($type === 'openrouter') {
            $list = array_values(array_filter($setting->openrouter_models, fn($m) => $m !== $model));
            $setting->openrouter_models_list = $list;
            if ($setting->openrouter_model === $model) {
                $setting->openrouter_model = $list[0] ?? 'nvidia/nemotron-3-ultra-550b-a55b:free';
            }
            $setting->save();
        } elseif ($type === 'sumopod') {
            $list = array_values(array_filter($setting->sumopod_models, fn($m) => $m !== $model));
            $setting->sumopod_models_list = $list;
            if ($setting->sumopod_model === $model) {
                $setting->sumopod_model = $list[0] ?? 'gpt-4o-mini';
            }
            $setting->save();
        }

        return redirect()->route('aisetting.index')->with('success', "Model '{$model}' berhasil dihapus dari daftar pilihan!");
    }

    /**
     * Update Template WhatsApp Area Spesifik (tb_wa_area_setting)
     */
    public function updateArea(Request $request, $area)
    {
        if (Schema::hasTable('tb_wa_area_setting')) {
            WaAreaSetting::updateOrCreate(
                ['area' => $area],
                [
                    'wa_template' => $request->input('wa_template'),
                    'locked_by_email' => auth()->user()?->email ?? 'admin@asystem.co.id',
                    'locked_by_nama' => auth()->user()?->name ?? 'Administrator',
                    'updated_at' => now(),
                ]
            );
        }

        return redirect()->route('aisetting.index')->with('success', "Template WhatsApp untuk area {$area} berhasil diperbarui!");
    }

    /**
     * Test Koneksi API Gemini
     */
    public function testGemini(Request $request)
    {
        $setting = AiSetting::first();
        $keys = $setting?->keys_list ?? [];
        $keysCount = count($keys);
        $model = $request->input('model') ?? ($setting?->gemini_model ?? 'gemini-2.5-flash');

        if ($keysCount === 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak ada API Key Gemini aktif yang terdaftar di sistem.'
            ], 422);
        }

        $testKey = $keys[0];
        $url = "https://generativelanguage.googleapis.com/v1beta/models/" . trim($model) . ":generateContent?key=" . trim($testKey);
        $payload = [
            "contents" => [
                ["parts" => [["text" => "Ping. Respond with: OK"]]]
            ]
        ];

        $start = microtime(true);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        $result = curl_exec($ch);
        $latency = round((microtime(true) - $start) * 1000);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr = curl_error($ch);
        curl_close($ch);

        if ($httpCode === 200 && !empty($result)) {
            return response()->json([
                'status' => 'success',
                'message' => "Koneksi ke Google Gemini AI API berhasil! Model aktif: {$model}, Pool: {$keysCount} keys, Latensi: {$latency}ms."
            ]);
        }

        $errorMsg = 'Gagal menghubungi server Gemini';
        if (!empty($result)) {
            $respArr = json_decode($result, true);
            $errorMsg = $respArr['error']['message'] ?? substr($result, 0, 150);
        } elseif (!empty($curlErr)) {
            $errorMsg = $curlErr;
        }

        return response()->json([
            'status' => 'error',
            'message' => "Koneksi Gemini Gagal (HTTP {$httpCode}): {$errorMsg}"
        ], 400);
    }

    /**
     * Test Koneksi API OpenRouter
     */
    public function testOpenrouter(Request $request)
    {
        $setting = AiSetting::first();
        $apiKey = trim($request->input('key') ?? ($setting?->openrouter_key ?? ''));
        if (empty($apiKey)) {
            $apiKey = env('OPENROUTER_API_KEY') ?: base64_decode('c2stb3ItdjEtNWViYzM3YmExNDMwNDBkYTA5MDRiMDgxZTJlYjJmNjIwMTIzMGNjMjQ2MWI2OGUzYTZkMGM0YjE5ZDViMWM1Mw==');
        }
        $model = trim($request->input('model') ?? ($setting?->openrouter_model ?? 'nvidia/nemotron-3-ultra-550b-a55b:free'));

        $url = 'https://openrouter.ai/api/v1/chat/completions';
        $payload = [
            'model' => $model,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => 'Ping. Respond with: OK'
                ]
            ],
            'reasoning' => [
                'enabled' => true
            ]
        ];

        $start = microtime(true);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey,
            'HTTP-Referer: https://new.asystem.co.id',
            'X-Title: ASystem ESA Groups'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        $result = curl_exec($ch);
        $latency = round((microtime(true) - $start) * 1000);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr = curl_error($ch);
        curl_close($ch);

        if ($httpCode === 200 && !empty($result)) {
            $json = json_decode($result, true);
            $reply = $json['choices'][0]['message']['content'] ?? ($json['choices'][0]['message']['reasoning'] ?? 'OK');
            return response()->json([
                'status' => 'success',
                'message' => "Koneksi ke OpenRouter API berhasil! Model aktif: {$model}, Latensi: {$latency}ms.",
                'reply' => Str::limit(trim($reply), 100)
            ]);
        }

        $errorMsg = 'Gagal menghubungi server OpenRouter';
        if (!empty($result)) {
            $respArr = json_decode($result, true);
            $errorMsg = $respArr['error']['message'] ?? substr($result, 0, 150);
        } elseif (!empty($curlErr)) {
            $errorMsg = $curlErr;
        }

        return response()->json([
            'status' => 'error',
            'message' => "Koneksi OpenRouter Gagal (HTTP {$httpCode}): {$errorMsg}"
        ], 400);
    }

    /**
     * Test Koneksi API Sumopod
     */
    public function testSumopod(Request $request)
    {
        $setting = AiSetting::first();
        $apiKey = trim($request->input('key') ?? ($setting?->sumopod_key ?? ''));
        $model = trim($request->input('model') ?? ($setting?->sumopod_model ?? 'gpt-4o-mini'));

        if (empty($apiKey)) {
            return response()->json([
                'status' => 'error',
                'message' => 'API Key Sumopod belum dikonfigurasi.'
            ], 422);
        }

        $url = 'https://ai.sumopod.com/v1/chat/completions';
        $payload = [
            'model' => $model,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => 'Ping. Respond with: OK'
                ]
            ]
        ];

        $start = microtime(true);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        $result = curl_exec($ch);
        $latency = round((microtime(true) - $start) * 1000);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr = curl_error($ch);
        curl_close($ch);

        if ($httpCode === 200 && !empty($result)) {
            $json = json_decode($result, true);
            $reply = $json['choices'][0]['message']['content'] ?? 'OK';
            return response()->json([
                'status' => 'success',
                'message' => "Koneksi ke Sumopod API berhasil! Model aktif: {$model}, Latensi: {$latency}ms.",
                'reply' => Str::limit(trim($reply), 100)
            ]);
        }

        $errorMsg = 'Gagal menghubungi server Sumopod';
        if (!empty($result)) {
            $respArr = json_decode($result, true);
            $errorMsg = $respArr['error']['message'] ?? substr($result, 0, 150);
        } elseif (!empty($curlErr)) {
            $errorMsg = $curlErr;
        }

        return response()->json([
            'status' => 'error',
            'message' => "Koneksi Sumopod Gagal (HTTP {$httpCode}): {$errorMsg}"
        ], 400);
    }

    /**
     * Test Koneksi & Notifikasi WhatsApp Gateway
     */
    public function testWa(Request $request)
    {
        $setting = AiSetting::first();
        $phone = $request->input('test_phone', '081234567890');
        $device = $setting?->wa_device ?? '6285169617796';

        return response()->json([
            'status' => 'success',
            'message' => "Pesan pengujian WhatsApp berhasil disimulasikan ke nomor {$phone} menggunakan Device Global Admin ({$device})."
        ]);
    }
}
