<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class JobSpec extends Model
{
    use HasFactory;

    protected $table = 'job_specs';
    protected $guarded = ['id'];

    protected $casts = [
        'tgl_expired' => 'date',
    ];

    public function getSkillsArrayAttribute(): array
    {
        if (empty($this->job_skills)) {
            return [];
        }
        return array_values(array_filter(array_map('trim', explode(',', $this->job_skills))));
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
}