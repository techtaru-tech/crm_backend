<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One-time magic-link tokens for customer-portal login.
 *
 * Deliberately NOT scoped by BelongsToTenant — we look up by a random
 * 64-char token in public routes where no tenant context exists yet.
 * Tenant isolation is enforced on the resolved lead side.
 */
class PortalAccessToken extends Model
{
    protected $fillable = [
        'lead_id', 'token', 'expires_at', 'used_at', 'ip_address',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at'    => 'datetime',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class)->withoutGlobalScope('tenant');
    }

    public function isValid(): bool
    {
        return $this->used_at === null && $this->expires_at->isFuture();
    }
}
