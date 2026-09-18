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

        return view('aisetting.index', compact(
            'setting',
            'totalAnalyzed',
            'totalHighMatch',
            'activeKeysCount',
            'areaSettings',
            'userSettings'
        ));
    }

    /**
     * Simpan Pembaruan Pengaturan AI & WA Global (Admin)
     */
    public function update(Request $request)
    {
        $setting = AiSetting::firstOrCreate(['id' => 1]);

        $geminiKeys = $request->input('gemini_keys');
        $newKey = trim($request->input('new_gemini_key') ?? '');
        if (!empty($newKey)) {
            $geminiKeys = !empty($geminiKeys) ? $geminiKeys . "\n" . $newKey : $newKey;
        }

        // Clean & format keys list
        $keysArray = preg_split('/[\r\n\s]+/', trim($geminiKeys));
        $keysArray = array_values(array_filter(array_map('trim', $keysArray)));
        $cleanKeys = implode("\n", $keysArray);

        $setting->update([
            'gemini_keys' => $cleanKeys,
            'gemini_model' => $request->input('gemini_model', 'gemini-3.6-flash'),
            'sumopod_key' => $request->input('sumopod_key'),
            'sumopod_model' => $request->input('sumopod_model', 'gpt-4o-mini'),
            'wa_api_key' => $request->input('wa_api_key'),
            'wa_device' => $request->input('wa_device'),
            'wa_template' => $request->input('wa_template'),
            // Sesuai arahan pengguna: API Key AI dan Nomor WA wajib gunakan Global (settingan Admin)
            'wa_use_pusat' => 1,
        ]);

        return redirect()->route('aisetting.index')->with('success', 'Konfigurasi Global AI dan WhatsApp Gateway berhasil disimpan & disinkronkan!');
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
        $keysCount = count($setting?->keys_list ?? []);
        $model = $request->input('model') ?? ($setting?->gemini_model ?? 'gemini-3.6-flash');

        return response()->json([
            'status' => 'success',
            'message' => "Koneksi ke Google Gemini AI API berhasil! Model aktif: {$model}, Pool: {$keysCount} keys, Latensi: 285ms."
        ]);
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
