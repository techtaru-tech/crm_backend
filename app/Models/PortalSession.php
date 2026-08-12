<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PortalSession extends Model
{
    protected $fillable = [
        'lead_id', 'session_token', 'last_active_at',
        'expires_at', 'ip_address', 'user_agent',
    ];

    protected $casts = [
        'last_active_at' => 'datetime',
        'expires_at'     => 'datetime',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class)->withoutGlobalScope('tenant');
    }

    public function isValid(): bool
    {
        return $this->expires_at->isFuture();
    }
}
