<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\BelongsToTenant;

class WebhookLog extends Model
{
    use HasFactory, BelongsToTenant;
    protected $fillable = [
        'tenant_id',
        'source_connection_id',
        'source',
        'status',
        'headers',
        'payload',
        'error_message',
        'leads_created',
        'ip_address',
        'processed_at',
    ];

    protected $casts = [
        'headers'      => 'array',
        'payload'      => 'array',
        'processed_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function sourceConnection(): BelongsTo
    {
        return $this->belongsTo(LeadSourceConnection::class, 'source_connection_id');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isSuccess(): bool
    {
        return $this->status === 'success';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }
}
