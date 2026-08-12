<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IntegrationSyncLog extends Model
{
    protected $fillable = [
        'integration_id', 'lead_id', 'event', 'payload', 'response',
        'status', 'attempts', 'retried_at', 'error_message',
    ];

    protected $casts = [
        'payload'    => 'array',
        'response'   => 'array',
        'retried_at' => 'datetime',
        'attempts'   => 'integer',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_SUCCESS = 'success';
    public const STATUS_FAILED  = 'failed';
    public const STATUS_RETRYING = 'retrying';

    public function integration(): BelongsTo
    {
        return $this->belongsTo(Integration::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }
}
