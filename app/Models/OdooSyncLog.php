<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OdooSyncLog extends Model
{
    use HasFactory;

    protected $table = 'odoo_sync_logs';

    protected $fillable = [
        'batch_id',
        'entity_code',
        'sync_type',
        'trigger_type',
        'status',
        'new_count',
        'update_count',
        'resign_count',
        'total_employee_count',
        'details',
        'error_message',
    ];

    protected $casts = [
        'details' => 'array',
        'new_count' => 'integer',
        'update_count' => 'integer',
        'resign_count' => 'integer',
        'total_employee_count' => 'integer',
    ];

    public function entity(): BelongsTo
    {
        return $this->belongsTo(OdooEntity::class, 'entity_code', 'code');
    }
}
