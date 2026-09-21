# Panduan Implementasi WhatsApp API Official Coexistence (Coex) di ASystem

Dokumen ini berisi panduan teknis, arsitektur, dan referensi implementasi integrasi **WhatsApp Official Cloud API dengan Fitur Coexistence (Coex)** untuk sistem **ASystem Cloud**.

---

## 1. Pendahuluan: Apa itu WhatsApp Coexistence (Coex)?

Secara historis, Meta mengharuskan nomor yang terdaftar di WhatsApp Business API (Cloud API/On-Premise) untuk **melepaskan aplikasi WhatsApp di ponsel pintar (smartphone)**. Hal ini menyulitkan operasional tim HRD/Recruitment yang masih mengandalkan HP fisik untuk memantau status, menelepon kandidat, atau membalas chat secara kasual.

**WhatsApp Coexistence (Coex)** adalah fitur resmi dari Meta (*WhatsApp Business Platform*) yang memungkinkan **1 nomor WhatsApp yang sama** aktif secara bersamaan di:
1. **Aplikasi WhatsApp Business di Smartphone (Android / iOS)**: Tetap dipegang oleh tim operasional/HRD.
2. **Sistem ASystem Cloud (Meta Cloud API / BSP)**: Mengirimkan notifikasi otomatis berbasis sistem (undangan interview, token tes CBT, reminder deadline work plan, dll.).

### Keunggulan Utama Coex:
* **Dual-Access Real-time**: HRD tetap bisa mengetik chat di HP, sementara sistem ASystem otomatis mengirim pesan notifikasi melalui API.
* **Auto Sync (Sinkronisasi Dua Arah)**: Pesan keluar dari sistem ASystem otomatis muncul di WhatsApp HP. Balasan dari kandidat juga masuk ke HP sekaligus dapat diterima webhook ASystem.
* **Zero Ban Risk**: 100% legal, resmi, dan mematuhi regulasi Meta (bebas risiko pemblokiran nomor).
* **Infrastruktur Ringan**: Cukup HTTP REST API standar Laravel (tidak membutuhkan server Node.js terpisah seperti Baileys/WhatsApp.js).

---

## 2. Perbandingan: Coex Official vs Library Unofficial (Baileys / WhatsApp.js)

| Parameter | WhatsApp Coex Official (Meta) | Baileys / WhatsApp.js (Unofficial) |
| :--- | :--- | :--- |
| **Legalitas & Keamanan** | Resmi Meta, **0% risiko banned** | Tidak resmi, melanggar ToS, **risiko banned tinggi** |
| **Kebutuhan Server** | HTTP Request biasa (Laravel bawaan) | Butuh daemon Node.js 24/7 (RAM tinggi, CPU spikes) |
| **Koneksi Sesi** | Selalu aktif via Cloud Token | Sering terputus, sesi expired, butuh scan ulang QR |
| **Penggunaan Smartphone** | HP **tetap aktif & bisa dipakai chat** | Sering konflik jika HP membuka WhatsApp Web lain |
| **Biaya Pesan** | Sesuai tarif resmi percakapan Meta | Gratis, namun berisiko kehilangan nomor bisnis |

---

## 3. Prasyarat & Persiapan

Sebelum memulai integrasi kode di Laravel:
1. **Nomor WhatsApp Business Aktif**: Nomor GSM yang sudah terpasang di aplikasi *WhatsApp Business* pada smartphone.
2. **Meta Business Manager (BM)**: Akun bisnis Facebook/Meta perusahaan yang telah diverifikasi (*Verified Business*).
3. **Penyedia Integrasi (Pilih salah satu)**:
   * **Direct Meta Cloud API (Gratis biaya langganan platform)**: Mendaftar langsung di [Meta for Developers](https://developers.facebook.com/) dan mengaktifkan *Embedded Signup with Coexistence*.
   * **Business Solution Partner (BSP)**: Menggunakan agregator lokal seperti *Qiscus*, *Mekari Qontak*, *SleekFlow*, *TapTalk (OneTalk)*, atau *Wati* jika membutuhkan dashboard percakapan multi-agen dan CS lokal Indonesia.
4. **Pendaftaran Template Pesan**: Meta mewajibkan pesan pembuka (*outbound notification*) menggunakan template terverifikasi (kategori: *Utility* atau *Authentication*).

---

## 4. Arsitektur Integrasi di ASystem (Laravel)

```
┌─────────────────────────────────────────────────────────────┐
│                 Modul ASystem (Trigger)                     │
│  - Jadwal Interview  - Ujian CBT  - Work Plan  - Reset Pwd  │
└──────────────────────────────┬──────────────────────────────┘
                               │
                               │ Dispatch Job
                               ▼
┌─────────────────────────────────────────────────────────────┐
│              Laravel Queue: SendWhatsAppJob                 │
│              (Asinkronus, non-blocking 0 detik)             │
└──────────────────────────────┬──────────────────────────────┘
                               │
                               │ HTTP Client Post
                               ▼
┌─────────────────────────────────────────────────────────────┐
│      App\Services\WhatsAppCoexService (REST API)            │
└──────────────────────────────┬──────────────────────────────┘
                               │
                               │ HTTPS (Payload JSON)
                               ▼
┌─────────────────────────────────────────────────────────────┐
│             Meta WhatsApp Cloud API Endpoint                │
│       https://graph.facebook.com/v20.0/{phone_id}/messages  │
└──────────────────────────────┬──────────────────────────────┘
                               │
               ┌───────────────┴───────────────┐
               ▼                               ▼
┌──────────────────────────────┐ ┌────────────────────────────┐
│      Kandidat / Pengguna     │ │  Aplikasi WhatsApp di HP   │
│       (Penerima Pesan)       │ │     (Tim HRD / Recruiter)  │
└──────────────────────────────┘ └────────────────────────────┘
```

---

## 5. Rencana Implementasi Kode di Laravel

### 5.1. Konfigurasi Environment (`.env`)

```env
# Konfigurasi WhatsApp API Coex
WA_COEX_ENABLED=true
WA_API_VERSION=v20.0
WA_PHONE_NUMBER_ID=your_meta_phone_number_id_here
WA_BUSINESS_ACCOUNT_ID=your_meta_waba_id_here
WA_ACCESS_TOKEN=your_permanent_system_user_token_here
WA_DEFAULT_COUNTRY_CODE=62
```

### 5.2. Konfigurasi Services (`config/services.php`)

```php
'whatsapp' => [
    'enabled' => env('WA_COEX_ENABLED', false),
    'version' => env('WA_API_VERSION', 'v20.0'),
    'phone_id' => env('WA_PHONE_NUMBER_ID'),
    'waba_id' => env('WA_BUSINESS_ACCOUNT_ID'),
    'token' => env('WA_ACCESS_TOKEN'),
    'endpoint' => 'https://graph.facebook.com/' . env('WA_API_VERSION', 'v20.0') . '/' . env('WA_PHONE_NUMBER_ID') . '/messages',
],
```

---

### 5.3. Service Provider: `app/Services/WhatsAppCoexService.php`

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppCoexService
{
    protected string $endpoint;
    protected string $token;
    protected bool $enabled;

    public function __construct()
    {
        $this->enabled = config('services.whatsapp.enabled', false);
        $this->endpoint = config('services.whatsapp.endpoint', '');
        $this->token = config('services.whatsapp.token', '');
    }

    /**
     * Normalisasi nomor telepon ke format internasional (contoh: 081234 -> 6281234)
     */
    public static function formatPhoneNumber(?string $phone): ?string
    {
        if (empty($phone)) return null;

        $clean = preg_replace('/\D/', '', $phone);
        if (str_starts_with($clean, '0')) {
            $clean = '62' . substr($clean, 1);
        } elseif (str_starts_with($clean, '8')) {
            $clean = '62' . $clean;
        }

        return (strlen($clean) >= 10) ? $clean : null;
    }

    /**
     * Kirim pesan template resmi Meta (Undangan Interview, Akses CBT, Reminder)
     */
    public function sendTemplate(string $recipientPhone, string $templateName, array $bodyParameters = [], string $language = 'id'): array
    {
        $phone = self::formatPhoneNumber($recipientPhone);
        if (!$this->enabled || empty($phone)) {
            Log::info("WhatsApp Coex: Pengiriman dilewati untuk {$recipientPhone} (Enabled: " . ($this->enabled ? 'YES' : 'NO') . ")");
            return ['status' => false, 'message' => 'Layanan WA dinonaktifkan atau nomor tidak valid'];
        }

        // Format parameter body template
        $parameters = array_map(function ($val) {
            return ['type' => 'text', 'text' => (string) $val];
        }, $bodyParameters);

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $phone,
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => ['code' => $language],
                'components' => [
                    [
                        'type' => 'body',
                        'parameters' => $parameters,
                    ],
                ],
            ],
        ];

        try {
            $response = Http::withToken($this->token)
                ->timeout(15)
                ->post($this->endpoint, $payload);

            if ($response->successful()) {
                Log::info("WhatsApp Coex Terkirim: Template {$templateName} ke {$phone}", $response->json());
                return ['status' => true, 'data' => $response->json()];
            }

            Log::error("WhatsApp Coex Gagal [HTTP {$response->status()}]: {$response->body()}");
            return ['status' => false, 'error' => $response->json()];
        } catch (\Throwable $e) {
            Log::error("WhatsApp Coex Exception: " . $e->getMessage());
            return ['status' => false, 'error' => $e->getMessage()];
        }
    }
}
```

---

### 5.4. Queue Job: `app/Jobs/SendWhatsAppCoexJob.php`

Job ini memastikan pengiriman pesan berjalan di latar belakang (*background worker*) tanpa memperlambat pengalaman pengguna di UI web:

```php
<?php

namespace App\Jobs;

use App\Services\WhatsAppCoexService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWhatsAppCoexJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 10;

    public function __construct(
        protected string $recipientPhone,
        protected string $templateName,
        protected array $bodyParameters = [],
        protected string $language = 'id'
    ) {}

    public function handle(WhatsAppCoexService $waService): void
    {
        $waService->sendTemplate(
            $this->recipientPhone,
            $this->templateName,
            $this->bodyParameters,
            $this->language
        );
    }
}
```

---

## 6. Contoh Kasus Penggunaan di ASystem

### 6.1. Undangan Interview Kandidat
```php
// Dispatch saat Recruiter menjadwalkan interview
SendWhatsAppCoexJob::dispatch(
    $kandidat->nomor_wa,
    'undangan_interview_karyawan',
    [
        $kandidat->nama_lengkap,        // Parameter {{1}}
        $posisi->nama_posisi,           // Parameter {{2}}
        $jadwal->format('d M Y H:i'),   // Parameter {{3}}
        $lokasiAtauLinkMeet,            // Parameter {{4}}
    ]
);
```

### 6.2. Pengiriman Akses & Token Tes CBT Online
```php
// Dispatch saat token CBT di-generate untuk kandidat
SendWhatsAppCoexJob::dispatch(
    $kandidat->nomor_wa,
    'akses_ujian_cbt_online',
    [
        $kandidat->nama_lengkap,        // Parameter {{1}}
        $tokenUjian,                    // Parameter {{2}}
        url('/cbt/login'),              // Parameter {{3}}
        '24 Jam',                       // Parameter {{4}} Batas pengerjaan
    ]
);
```

### 6.3. Pengingat Tugas Work Plan Karyawan
```php
// Dispatch saat sistem cron mendeteksi deadline tugas hari ini
SendWhatsAppCoexJob::dispatch(
    $karyawan->telepon,
    'reminder_tugas_workplan',
    [
        $karyawan->nama_karyawan,       // Parameter {{1}}
        $tugas->nama_tugas,             // Parameter {{2}}
        $tugas->deadline->format('d M'),// Parameter {{3}}
    ]
);
```

---

## 7. Rekomendasi Alur Pendaftaran Template Meta

Berikut adalah rancangan template standar yang didaftarkan ke Meta Business Manager:

1. **`undangan_interview_karyawan` (Kategori: UTILITY)**:
   > *"Halo {{1}}, selamat! Anda terpilih untuk tahap interview posisi {{2}} di PT Anugrah Talenta Berkarya. Jadwal wawancara Anda adalah {{3}} bertempat di {{4}}. Mohon hadir tepat waktu. Terima kasih."*
2. **`akses_ujian_cbt_online` (Kategori: UTILITY / AUTHENTICATION)**:
   > *"Halo {{1}}, berikut adalah akses Portal CBT Online Anda. Gunakan kode ujian: {{2}} melalui link: {{3}}. Harap selesaikan ujian sebelum {{4}}. Semangat!"*
3. **`reminder_tugas_workplan` (Kategori: UTILITY)**:
   > *"Halo {{1}}, pengingat tugas Work Plan '{{2}}' memiliki tenggat waktu hari ini ({{3}}). Silakan periksa dashboard ASystem untuk memperbarui status pekerjaan Anda."*

---

## 8. Langkah Roadmap Eksekusi Selanjutnya

Jika tim manajemen memutuskan untuk melanjutkan pengaktifan fitur ini:
1. **Langkah 1**: Registrasi WABA (*WhatsApp Business Account*) di Meta Business Manager perusahaan.
2. **Langkah 2**: Ajukan verifikasi bisnis (*Business Verification*) di Meta.
3. **Langkah 3**: Buka portal Meta Developers, hubungkan nomor WhatsApp Business yang ada via alur *Coexistence*.
4. **Langkah 4**: Daftarkan template pesan di dashboard WhatsApp Manager.
5. **Langkah 5**: Masukkan kredensial token ke `.env` server production ASystem.
6. **Langkah 6**: Aktifkan job worker queue di background server (`php artisan queue:work`).
