<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Audit row written every time a tenant redeems a coupon.
 * Powers the "uses left" cap and the SA-side coupon analytics
 * (which tenants used which codes, when, for how much).
 */
class CouponRedemption extends Model
{
    use HasFactory;

    protected $fillable = [
        'coupon_id',
        'tenant_id',
        'plan_key',
        'amount_discounted',
        'currency',
        'redeemed_at',
        'metadata',
    ];

    protected $casts = [
        'amount_discounted' => 'decimal:2',
        'redeemed_at'       => 'datetime',
        'metadata'          => 'array',
    ];

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
