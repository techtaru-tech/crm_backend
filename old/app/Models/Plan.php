<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Subscription plan definition. Replaces the legacy `config/plans.php`
 * array so script owners can create, price, and gate plans entirely
 * from the Super Admin UI without touching files.
 *
 * Limit conventions (JSON `limits` column):
 *   -1 = unlimited
 *    0 = disabled / not included
 *   >0 = hard cap
 */
class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'name',
        'description',
        'price',
        'annual_price',
        'annual_discount_percent',
        'currency',
        'interval',
        'trial_days',
        'is_public',
        'is_active',
        'highlight',
        'sort_order',
        'stripe_price_id',
        'paddle_price_id',
        'razorpay_plan_id',
        'paystack_plan_code',
        'features',
        'limits',
        'meta',
    ];

    protected $casts = [
        'price'                   => 'decimal:2',
        'annual_price'            => 'decimal:2',
        'annual_discount_percent' => 'integer',
        'trial_days'              => 'integer',
        'is_public'               => 'boolean',
        'is_active'               => 'boolean',
        'highlight'               => 'boolean',
        'sort_order'              => 'integer',
        'features'                => 'array',
        'limits'                  => 'array',
        'meta'                    => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | Query scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true)->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('price');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function getLimit(string $key, int $default = 0): int
    {
        return (int) data_get($this->limits ?? [], $key, $default);
    }

    public function hasFeature(string $key): bool
    {
        return (bool) data_get($this->features ?? [], $key, false);
    }

    /**
     * Effective price after applying a coupon, accounting for the
     * billing interval the buyer is checking out under.
     *
     * For interval='year':
     *   - Use annual_price column when set (operator-defined yearly
     *     discount, e.g. Pro $79/mo → $759/year)
     *   - Fall back to price × 12 otherwise
     *
     * Trial-extension coupons are handled at the call site
     * (they bump trial_ends_at, don't reduce price).
     *
     * @param  Coupon|null  $coupon    null returns base price
     * @param  string       $interval  'month' or 'year'
     */
    public function priceAfterCoupon(?Coupon $coupon, string $interval = 'month'): float
    {
        $base = $this->basePriceFor($interval);

        if (! $coupon) {
            return $base;
        }

        $discount = $coupon->calculateDiscount($base, $this->currency);
        return max(0.0, round($base - $discount, 2));
    }

    /**
     * Resolve the base price for a billing interval — used by
     * priceAfterCoupon, ProrationCalculator, and any caller that
     * needs to know "what does this plan cost on the yearly cycle?"
     */
    public function basePriceFor(string $interval = 'month'): float
    {
        if ($interval === 'year') {
            $annual = $this->annual_price !== null ? (float) $this->annual_price : null;
            if ($annual !== null && $annual > 0) {
                return $annual;
            }
            return round((float) $this->price * 12, 2);
        }
        return (float) $this->price;
    }

    /**
     * Return the plan as the same associative array shape legacy code
     * used when plans were defined in config. Lets PlanService keep a
     * single contract with the rest of the app.
     */
    public function toLegacyArray(): array
    {
        return [
            'key'                     => $this->key,
            'name'                    => $this->name,
            'description'             => $this->description,
            'price'                   => (float) $this->price,
            'annual_price'            => $this->annual_price !== null ? (float) $this->annual_price : null,
            'annual_discount_percent' => $this->annual_discount_percent !== null ? (int) $this->annual_discount_percent : null,
            'currency'                => $this->currency,
            'interval'                => $this->interval,
            'trial_days'              => (int) ($this->trial_days ?? 0),
            'is_public'               => (bool) $this->is_public,
            'is_active'               => (bool) $this->is_active,
            'highlight'                => (bool) $this->highlight,
            'sort'                     => $this->sort_order,
            'features'                 => $this->features ?? [],
            'limits'                   => $this->limits ?? [],
            'meta'                     => $this->meta ?? [],
        ];
    }
}
