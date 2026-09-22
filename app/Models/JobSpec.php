<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Employee;
use App\Models\User;

class JobSpec extends Model
{
    use HasFactory;

    protected $table = 'job_specs';
    protected $guarded = ['id'];

    protected $casts = [
        'tgl_expired' => 'date',
    ];

    protected static function booted(): void
    {
        static::saving(function ($job) {
            if (!empty($job->job_area) && $job->job_area !== '-') {
                $job->job_region = \App\Models\TbArea::resolveRegion($job->job_area);
            }
        });
    }

    /**
     * Resolusi Region resmi lowongan berdasarkan master tb_area ESA Groups
     */
    public function getRegionAttribute(): string
    {
        if (!empty($this->attributes['job_region']) && $this->attributes['job_region'] !== '-') {
            return $this->attributes['job_region'];
        }

        $area = !empty($this->job_area) ? trim($this->job_area) : null;
        if (!empty($area) && $area !== '-') {
            $reg = \App\Models\TbArea::resolveRegion($area);
            if ($reg !== '-') {
                return $reg;
            }
        }

        return 'Region 1';
    }


    public function getSlugAttribute(): string
    {
        $clean = preg_replace('/[^A-Za-z0-9-]+/', '-', $this->job_title);
        return strtolower(trim(preg_replace('/-+/', '-', $clean), '-'));
    }

    public function getIsExpiredAttribute(): bool
    {
        if (!$this->tgl_expired) {
            return false;
        }
        return $this->tgl_expired->isPast();
    }

    /**
     * Get the matched Employee from Master Karyawan based on created_by (User AS).
     */
    public function getCreatorEmployeeAttribute(): ?Employee
    {
        $creator = trim($this->created_by ?? '');
        if (empty($creator)) {
            return null;
        }

        $clean = strtolower($creator);

        // 1. Match email in Employee
        $emp = Employee::whereRaw('LOWER(TRIM(email)) = ?', [$clean])->first();
        if ($emp) {
            return $emp;
        }

        // 2. Match nama_karyawan in Employee
        $emp = Employee::whereRaw('LOWER(TRIM(nama_karyawan)) = ?', [$clean])->first();
        if ($emp) {
            return $emp;
        }

        // 3. Match NIK in Employee
        $emp = Employee::where('nik', $creator)->first();
        if ($emp) {
            return $emp;
        }

        // 4. Try matching via User account (email, name, or username)
        $user = User::whereRaw('LOWER(TRIM(email)) = ?', [$clean])
            ->orWhereRaw('LOWER(TRIM(name)) = ?', [$clean])
            ->orWhere('username', $creator)
            ->first();

        if ($user) {
            if (!empty($user->email)) {
                $emp = Employee::whereRaw('LOWER(TRIM(email)) = ?', [strtolower(trim($user->email))])->first();
                if ($emp) return $emp;
            }
            if (!empty($user->name)) {
                $emp = Employee::whereRaw('LOWER(TRIM(nama_karyawan)) = ?', [strtolower(trim($user->name))])->first();
                if ($emp) return $emp;
            }
        }

        // 5. Check tb_wa_area_setting locked_by_nama / locked_by_email
        if (str_contains($clean, '@')) {
            $waSetting = \Illuminate\Support\Facades\DB::table('tb_wa_area_setting')
                ->whereRaw('LOWER(TRIM(locked_by_email)) = ?', [$clean])
                ->first();
            if ($waSetting && !empty($waSetting->locked_by_nama)) {
                $emp = Employee::whereRaw('LOWER(TRIM(nama_karyawan)) = ?', [strtolower(trim($waSetting->locked_by_nama))])->first();
                if ($emp) return $emp;
            }
        }

        return null;
    }

    /**
     * Get the display name of the PIC / User AS.
     */
    public function getPicNameAttribute(): string
    {
        $emp = $this->creator_employee;
        if ($emp && !empty($emp->nama_karyawan)) {
            return $emp->nama_karyawan;
        }

        $creator = trim($this->created_by ?? '');
        if (empty($creator)) {
            return 'Tim Rekrutmen ESA';
        }

        if (str_contains($creator, '@')) {
            $user = User::whereRaw('LOWER(TRIM(email)) = ?', [strtolower($creator)])->first();
            if ($user && !empty($user->name)) {
                return $user->name;
            }

            $prefix = explode('@', $creator)[0];
            $name = preg_replace('/[0-9_.-]+/', ' ', $prefix);
            return ucwords(trim($name)) ?: $creator;
        }

        return ucwords(strtolower($creator));
    }

    /**
     * Get the dynamic WhatsApp number formatted for https://wa.me/{number}.
     * Priority:
     * 1. Employee telepon from Master Karyawan
     * 2. User phone if matched
     * 3. Global fallback WA (6283139797309)
     */
    public function getCreatorWhatsappAttribute(): string
    {
        $emp = $this->creator_employee;
        $rawPhone = $emp?->telepon;

        if (empty($rawPhone)) {
            $creator = trim($this->created_by ?? '');
            if (!empty($creator)) {
                $user = User::whereRaw('LOWER(TRIM(email)) = ?', [strtolower($creator)])
                    ->orWhereRaw('LOWER(TRIM(name)) = ?', [strtolower($creator)])
                    ->first();
                $rawPhone = $user?->phone;
            }
        }

        if (!empty($rawPhone)) {
            $digits = preg_replace('/\D/', '', $rawPhone);
            if (!empty($digits)) {
                if (str_starts_with($digits, '0')) {
                    $digits = '62' . substr($digits, 1);
                } elseif (str_starts_with($digits, '8')) {
                    $digits = '62' . $digits;
                }
                if (strlen($digits) >= 10 && str_starts_with($digits, '62')) {
                    return $digits;
                }
            }
        }

        // Default fallback number
        return '6283139797309';
    }

    /**
     * Human-readable formatted WhatsApp number, e.g. +62 812-3456-7890
     */
    public function getCreatorWhatsappFormattedAttribute(): string
    {
        $raw = $this->creator_whatsapp;
        if (str_starts_with($raw, '62') && strlen($raw) >= 11) {
            $prefix = '+62 ';
            $rest = substr($raw, 2);
            return $prefix . substr($rest, 0, 3) . '-' . substr($rest, 3, 4) . '-' . substr($rest, 7);
        }
        return '+' . $raw;
    }

    /**
     * Format content for display in public/portal views.
     * Renders valid HTML if present, or formats plain text / bullets nicely.
     */
    public static function formatRichText(?string $content): string
    {
        if (empty($content)) {
            return '';
        }

        $trimmed = trim($content);
        // Replace literal escape sequences first
        $trimmed = str_replace(["\\r\\n", "\\r", "\\n"], "\n", $trimmed);
        // Replace non-breaking spaces (both &nbsp; and \xc2\xa0)
        $trimmed = str_replace(["\xc2\xa0", '&nbsp;'], ' ', $trimmed);

        // Check if content already contains HTML tags
        if (preg_match('/<[a-z][\s\S]*>/i', $trimmed)) {
            $allowedTags = '<p><br><br/><ul><ol><li><strong><b><em><i><u><span><h3><h4>';
            $cleaned = strip_tags($trimmed, $allowedTags);
            $cleaned = html_entity_decode($cleaned, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            return $cleaned;
        }

        // Check if plain text has bullet points
        $lines = explode("\n", $trimmed);
        $hasBullets = false;
        foreach ($lines as $line) {
            $l = trim($line);
            if (str_starts_with($l, '-') || str_starts_with($l, '*') || str_starts_with($l, '•')) {
                $hasBullets = true;
                break;
            }
        }

        if ($hasBullets) {
            $html = '<ul class="space-y-1.5 list-disc pl-5">';
            foreach ($lines as $line) {
                $l = trim($line);
                if (empty($l)) continue;
                if (preg_match('/^[-*•]\s*(.*)$/u', $l, $m)) {
                    $html .= '<li class="leading-relaxed">' . htmlspecialchars($m[1], ENT_QUOTES, 'UTF-8') . '</li>';
                } else {
                    $html .= '<li class="leading-relaxed">' . htmlspecialchars($l, ENT_QUOTES, 'UTF-8') . '</li>';
                }
            }
            $html .= '</ul>';
            return $html;
        }

        return nl2br(htmlspecialchars($trimmed, ENT_QUOTES, 'UTF-8'));
    }

    /**
     * Convert HTML tags to clean plain text (useful for textarea in edit forms and cards).
     */
    public static function htmlToCleanText(?string $html): string
    {
        if (empty($html)) {
            return '';
        }

        $text = (string)$html;
        $text = str_replace(["\\r\\n", "\\r", "\\n"], "\n", $text);
        $text = str_replace(["\xc2\xa0", '&nbsp;'], ' ', $text);

        if (preg_match('/<[a-z][\s\S]*>/i', $text)) {
            $text = preg_replace('/<li[^>]*>/i', "- ", $text);
            $text = preg_replace('/<\/(li|p|ul|ol|h\d)>/i', "\n", $text);
            $text = preg_replace('/<(br|br\s*\/)>/i', "\n", $text);
            $text = strip_tags($text);
        }

        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace("/\n{3,}/", "\n\n", $text);

        return trim($text);
    }

    /**
     * Parse skills into an array of clean skill items, splitting by newlines, bullets, and commas.
     */
    public function getSkillsArrayAttribute(): array
    {
        if (empty($this->job_skills)) {
            return [];
        }

        $raw = str_replace(["\\r\\n", "\\n", "\\r", "\r\n", "\r"], "\n", $this->job_skills);
        $raw = str_replace(["\xc2\xa0", '&nbsp;'], ' ', $raw);
        $raw = html_entity_decode(strip_tags($raw), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        $parts = preg_split('/[;\n]+/', $raw);
        $skills = [];
        foreach ($parts as $part) {
            $subparts = explode(',', $part);
            foreach ($subparts as $p) {
                $cleaned = trim(preg_replace('/^[-*•\d.)\s]+/u', '', trim($p)));
                $cleaned = trim($cleaned, " \t\n\r\0\x0B.,;");
                if (!empty($cleaned) && mb_strlen($cleaned) > 1) {
                    $skills[] = $cleaned;
                }
            }
        }

        return array_values(array_unique($skills));
    }

    /**
     * Clean comma-separated skills for textarea editing.
     */
    public function getPlainSkillsAttribute(): string
    {
        $skills = $this->skills_array;
        if (empty($skills)) {
            return '';
        }
        return implode(', ', $skills);
    }

    /**
     * One-line summary/snippet of job description for list cards.
     */
    public function getSnippetDescAttribute(): string
    {
        $text = $this->job_desc ?? ($this->kualifikasi ?? '');
        if (empty($text)) {
            return 'Memiliki dedikasi kerja tinggi, mampu beradaptasi dan berkembang bersama perusahaan.';
        }

        $clean = self::htmlToCleanText($text);
        $clean = preg_replace('/\s+/', ' ', $clean);
        return trim($clean);
    }

    public function getFormattedDescAttribute(): string
    {
        return self::formatRichText($this->job_desc);
    }

    public function getFormattedQualsAttribute(): string
    {
        return self::formatRichText($this->job_quals ?? $this->kualifikasi ?? '');
    }

    public function getFormattedExpAttribute(): string
    {
        return self::formatRichText($this->job_exp);
    }

    public function getFormattedAdditionalInfoAttribute(): string
    {
        return self::formatRichText($this->additional_info);
    }

    public function getPlainDescAttribute(): string
    {
        return self::htmlToCleanText($this->job_desc);
    }

    public function getPlainQualsAttribute(): string
    {
        return self::htmlToCleanText($this->job_quals ?? $this->kualifikasi ?? '');
    }

    public function getPlainExpAttribute(): string
    {
        return self::htmlToCleanText($this->job_exp);
    }

    public function getPlainAdditionalInfoAttribute(): string
    {
        return self::htmlToCleanText($this->additional_info);
    }
}