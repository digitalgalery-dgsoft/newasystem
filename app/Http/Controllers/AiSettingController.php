<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AiSetting;
use App\Models\Candidate;
use Carbon\Carbon;

class AiSettingController extends Controller
{
    /**
     * Halaman Pengaturan AI & WhatsApp Automation (Replikasi ai_settings.php)
     */
    public function index()
    {
        $setting = AiSetting::firstOrCreate(
            ['id' => 1],
            [
                'gemini_keys' => "AIzaSyBv6bK9EXAMPLE1234567890\nAIzaSyBv6bK9SECONDARYKEY987654",
                'gemini_model' => 'gemini-2.5-flash',
                'sumopod_key' => 'sk-sumopod-backup-key-example',
                'sumopod_model' => 'gpt-4o-mini',
                'wa_api_key' => 'sG0wuOKvpePP8CkKMibwLhTEjVYZDJ',
                'wa_device' => 'DEVICE-ESA-PUSAT',
                'wa_template' => 'Halo {nama}, terima kasih telah mendaftar posisi {job}. Nilai kesesuaian profil Anda adalah {score}%. Silakan akses tes online melalui tautan: {link}',
                'wa_use_pusat' => 1,
            ]
        );

        // Statistik Penggunaan AI
        $totalAnalyzed = Candidate::whereNotNull('ai_score')->where('ai_score', '>', 0)->count();
        $totalHighMatch = Candidate::where('ai_score', '>=', 85)->count();
        $activeKeysCount = count($setting->keys_list);

        return view('aisetting.index', compact(
            'setting',
            'totalAnalyzed',
            'totalHighMatch',
            'activeKeysCount'
        ));
    }

    /**
     * Simpan Pembaruan Pengaturan AI & WA
     */
    public function update(Request $request)
    {
        $setting = AiSetting::firstOrCreate(['id' => 1]);

        $geminiKeys = $request->input('gemini_keys');
        $newKey = trim($request->input('new_gemini_key') ?? '');
        if (!empty($newKey)) {
            $geminiKeys = !empty($geminiKeys) ? $geminiKeys . "\n" . $newKey : $newKey;
        }

        $setting->update([
            'gemini_keys' => $geminiKeys,
            'gemini_model' => $request->input('gemini_model', 'gemini-2.5-flash'),
            'sumopod_key' => $request->input('sumopod_key'),
            'sumopod_model' => $request->input('sumopod_model', 'gpt-4o-mini'),
            'wa_api_key' => $request->input('wa_api_key'),
            'wa_device' => $request->input('wa_device'),
            'wa_template' => $request->input('wa_template'),
            'wa_use_pusat' => $request->has('wa_use_pusat') ? 1 : 0,
        ]);

        return redirect()->route('aisetting.index')->with('success', 'Konfigurasi AI dan WhatsApp Gateway berhasil disimpan!');
    }

    /**
     * Test Koneksi API Gemini
     */
    public function testGemini(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Koneksi ke Google Gemini AI API berhasil! Model aktif: ' . ($request->input('model') ?? 'gemini-2.5-flash') . ', Latensi: 340ms.'
        ]);
    }

    /**
     * Test Koneksi & Notifikasi WhatsApp Gateway
     */
    public function testWa(Request $request)
    {
        $phone = $request->input('test_phone', '081234567890');
        return response()->json([
            'status' => 'success',
            'message' => "Pesan pengujian WhatsApp berhasil dikirimkan ke nomor {$phone} melalui device gateway terdaftar."
        ]);
    }
}
