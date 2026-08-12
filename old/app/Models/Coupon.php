<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Coupon / promo code for SaaS-side billing.
 *
 * Validation surface lives here as pure model methods (isValidNow,
 * isApplicableToPlan, hasUsesLeft, usesLeftFor).  The actual redeem-
 * with-side-effects path goes through CouponService::redeem() so the
 * counter increment + redemption row write are atomic.
 *
 * Discount shapes:
 *   percent          → discount_value 0..100 applied as %
 *   fixed            → discount_value = currency amount, in `currency`
 *   trial_extension  → discount_value = days added to trial_ends_at
 *                       (no currency dependency)
 */
class Coupon extends Model
{
    use HasFactory, SoftDeletes;

    public const TYPE_PERCENT          = 'percent';
    public const TYPE_FIXED            = 'fixed';
    public const TYPE_TRIAL_EXTENSION  = 'trial_extension';

    public const TYPES = [
        self::TYPE_PERCENT          => 'Percent off',
        self::TYPE_FIXED            => 'Fixed amount off',
        self::TYPE_TRIAL_EXTENSION  => 'Extend trial by N days',
    ];

    /**
     * Translated discount-type labels for Filament Select / display.
     * Keys mirror self::TYPES.
     */
    public static function typeLabels(): array
    {
        return [
            self::TYPE_PERCENT         => __('models/coupon.type_percent'),
            self::TYPE_FIXED           => __('models/coupon.type_fixed'),
            self::TYPE_TRIAL_EXTENSION => __('models/coupon.type_trial_extension'),
        ];
    }

    protected $fillable = [
        'code',
        'description',
        'discount_type',
        'discount_value',
        'currency',
        'max_uses',
        'times_used',
        'max_per_tenant',
        'applies_to_plans',
        'starts_at',
        'ends_at',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'discount_value'   => 'decimal:2',
        'max_uses'         => 'integer',
        'times_used'       => 'integer',
        'max_per_tenant'   => 'integer',
        'applies_to_plans' => 'array',
        'starts_at'        => 'datetime',
        'ends_at'          => 'datetime',
        'is_active'        => 'boolean',
        'metadata'         => 'array',
    ];

    public function redemptions(): HasMany
    {
        return $this->hasMany(CouponRedemption::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Validation predicates
    |--------------------------------------------------------------------------
    */

    public function isValidNow(?Carbon $now = null): bool
    {
        $now ??= Carbon::now();

        if (! $this->is_active) return false;
        if ($this->starts_at && $now->lt($this->starts_at)) return false;
        if ($this->ends_at && $now->gt($this->ends_at)) return false;

        return true;
    }

    public function hasUsesLeft(): bool
    {
        if ($this->max_uses === null) return true;
        return $this->times_used < $this->max_uses;
    }

    public function usesLeftFor(Tenant $tenant): int
    {
        $cap = max(1, $this->max_per_tenant);
        $used = $this->redemptions()->where('tenant_id', $tenant->id)->count();
        return max(0, $cap - $used);
    }

    public function isApplicableToPlan(?string $planKey): bool
    {
        $allowed = $this->applies_to_plans;
        if (empty($allowed)) return true; // null / [] = all plans
        if (! $planKey) return false;
        return in_array($planKey, $allowed, true);
    }

    /*
    |--------------------------------------------------------------------------
    | Discount calculators
    |--------------------------------------------------------------------------
    */

    /**
     * How much would this coupon shave off a base price?  Returns the
     * discount AMOUNT (not the post-discount price), in the same
     * currency as $basePrice.  Trial-extension coupons return 0 here
     * because they don't reduce price — caller must inspect
     * discount_type and apply the trial bonus separately.
     */
    public function calculateDiscount(float $basePrice, ?string $currency = null): float
    {
        if ($basePrice <= 0) return 0.0;

        return match ($this->discount_type) {
            self::TYPE_PERCENT => round($basePrice * (float) $this->discount_value / 100, 2),
            self::TYPE_FIXED   => $this->fixedAppliesTo($currency)
                ? min((float) $this->discount_value, $basePrice)
                : 0.0,
            default            => 0.0,
        };
    }

    /**
     * Days to add to a trial when this is a trial_extension coupon.
     * Returns 0 for non-trial coupons.
     */
    public function trialExtensionDays(): int
    {
        return $this->discount_type === self::TYPE_TRIAL_EXTENSION
            ? max(0, (int) $this->discount_value)
            : 0;
    }

    protected function fixedAppliesTo(?string $currency): bool
    {
        if ($this->currency === null) return true;
        if ($currency === null) return false;
        return strtoupper($this->currency) === strtoupper($currency);
    }

    /*
    |--------------------------------------------------------------------------
    | Query scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function scopeValidNow($q)
    {
        $now = Carbon::now();
        return $q->where('is_active', true)
            ->where(fn ($w) => $w->whereNull('starts_at')->orWhere('starts_at', '<=', $now))
            ->where(fn ($w) => $w->whereNull('ends_at')->orWhere('ends_at', '>=', $now));
    }
}
