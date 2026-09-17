<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Principle extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'parent_company',
        'pic_name',
        'pic_email',
        'pic_phone',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(PrincipleApproval::class);
    }
}