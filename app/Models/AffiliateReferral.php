<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Commission row for an affiliate referral.
 *
 * One row created per successful payment cycle of a referred tenant.
 * Status drives the payout pipeline:
 *
 *   pending   → just-created, not yet eligible for payout
 *   approved  → operator OK'd it (manually or via 30-day auto-approve)
 *   paid      → operator recorded the actual transfer
 *   reversed  → refund / chargeback voided the underlying revenue
 */
class AffiliateReferral extends Model
{
    use HasFactory;

    public const STATUS_PENDING  = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_PAID     = 'paid';
    public const STATUS_REVERSED = 'reversed';

    public const STATUSES = [
        self::STATUS_PENDING  => 'Pending',
        self::STATUS_APPROVED => 'Approved',
        self::STATUS_PAID     => 'Paid',
        self::STATUS_REVERSED => 'Reversed',
    ];

    /**
     * Translated commission-status labels for display.
     * Keys mirror self::STATUSES.
     */
    public static function statusLabels(): array
    {
        return [
            self::STATUS_PENDING  => __('models/affiliate_referral.status_pending'),
            self::STATUS_APPROVED => __('models/affiliate_referral.status_approved'),
            self::STATUS_PAID     => __('models/affiliate_referral.status_paid'),
            self::STATUS_REVERSED => __('models/affiliate_referral.status_reversed'),
        ];
    }

    protected $fillable = [
        'referrer_tenant_id',
        'referred_tenant_id',
        'plan_key',
        'amount_attributed',
        'commission_percent',
        'commission_amount',
        'currency',
        'commission_status',
        'paid_at',
        'metadata',
    ];

    protected $casts = [
        'amount_attributed'  => 'decimal:2',
        'commission_percent' => 'decimal:2',
        'commission_amount'  => 'decimal:2',
        'paid_at'            => 'datetime',
        'metadata'           => 'array',
    ];

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'referrer_tenant_id');
    }

    public function referred(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'referred_tenant_id');
    }

    public function scopeOwed($q)
    {
        return $q->where('commission_status', self::STATUS_APPROVED);
    }

    public function scopePending($q)
    {
        return $q->where('commission_status', self::STATUS_PENDING);
    }

    public function scopePaid($q)
    {
        return $q->where('commission_status', self::STATUS_PAID);
    }
}
