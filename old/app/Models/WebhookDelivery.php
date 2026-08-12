<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\BelongsToTenant;

class WebhookDelivery extends Model
{
    use BelongsToTenant;
    protected $fillable = [
        'webhook_id',
        'tenant_id',
        'event',
        'payload',
        'response_code',
        'response_body',
        'latency_ms',
        'status',
        'attempts',
        'next_retry_at',
    ];

    protected $casts = [
        'payload'       => 'array',
        'next_retry_at' => 'datetime',
    ];

    public const STATUS_PENDING  = 'pending';
    public const STATUS_SUCCESS  = 'success';
    public const STATUS_FAILED   = 'failed';
    public const STATUS_RETRYING = 'retrying';

    public function webhook(): BelongsTo
    {
        return $this->belongsTo(OutboundWebhook::class, 'webhook_id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function isSuccessful(): bool
    {
        return $this->status === self::STATUS_SUCCESS;
    }

    public function canRetry(): bool
    {
        return $this->status === self::STATUS_FAILED && $this->attempts < 5;
    }
}
