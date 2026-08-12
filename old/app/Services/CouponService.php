<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Coupon;
use App\Models\CouponRedemption;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Coupon validation + redemption.
 *
 * validate() is read-only and cheap — call it on every keystroke as
 * the user types a code at checkout, on every form blur, etc.  The
 * return shape is uniform whether the code passes or fails:
 *
 *   ['valid' => bool, 'coupon' => Coupon|null, 'reason' => ?string,
 *    'reason_label' => ?string, 'discount' => float, 'extension_days' => int]
 *
 * redeem() is the side-effect path.  It runs inside a DB transaction:
 *   1. Re-validates (race: another tenant might have just exhausted it)
 *   2. Increments coupons.times_used
 *   3. Inserts a coupon_redemptions row
 *   4. Applies trial extension (if discount_type=trial_extension)
 *   5. Writes an audit log
 *
 * The actual price-side discount is NOT applied here — the gateway
 * adapter (Stripe, Paystack, etc.) takes the discount amount from
 * validate() and creates the gateway-native discount object.  Keeps
 * the engine gateway-agnostic.
 */
class CouponService
{
    /**
     * Reason key → English fallback map.  Kept as-is so existing code
     * that reads the const (legacy seeders, mock fixtures) continues to
     * resolve the keys.  Buyer-facing rendering MUST route through
     * self::reasons() instead so the literal English strings flow
     * through the translator.
     */
    public const REASONS = [
        'not_found'          => 'That code does not exist.',
        'inactive'           => 'This code is no longer active.',
        'expired'            => 'This code has expired.',
        'not_started'        => 'This code is not active yet.',
        'exhausted'          => 'This code has reached its usage limit.',
        'tenant_exhausted'   => 'You have already used this code.',
        'plan_mismatch'      => 'This code does not apply to the chosen plan.',
        'currency_mismatch'  => 'This code is for a different currency.',
        'invalid_for_zero'   => 'This code cannot be applied to a free plan.',
    ];

    /**
     * Translator-resolved reason labels for UI rendering.
     *
     * Mirrors the App\Services\Migration\CrmCsvImporter::labels() pattern:
     * the const holds the canonical fallback map; this method routes each
     * label through __() at call time so the value reflects the active
     * locale instead of being frozen at file-load.
     *
     * @return array<string, string>
     */
    public static function reasons(): array
    {
        return [
            'not_found'          => __('services/coupon.reason_not_found'),
            'inactive'           => __('services/coupon.reason_inactive'),
            'expired'            => __('services/coupon.reason_expired'),
            'not_started'        => __('services/coupon.reason_not_started'),
            'exhausted'          => __('services/coupon.reason_exhausted'),
            'tenant_exhausted'   => __('services/coupon.reason_tenant_exhausted'),
            'plan_mismatch'      => __('services/coupon.reason_plan_mismatch'),
            'currency_mismatch'  => __('services/coupon.reason_currency_mismatch'),
            'invalid_for_zero'   => __('services/coupon.reason_invalid_for_zero'),
        ];
    }

    /**
     * @return array{
     *   valid: bool,
     *   coupon: ?Coupon,
     *   reason: ?string,
     *   reason_label: ?string,
     *   discount: float,
     *   extension_days: int
     * }
     */
    public function validate(
        ?string $code,
        ?Tenant $tenant,
        ?string $planKey = null,
        float $basePrice = 0.0,
        ?string $currency = null,
    ): array {
        $code = $this->normalizeCode($code);
        if ($code === '') {
            return $this->fail('not_found');
        }

        /** @var Coupon|null $coupon */
        $coupon = Coupon::query()->where('code', $code)->first();
        if (! $coupon) return $this->fail('not_found');

        if (! $coupon->is_active) return $this->fail('inactive', $coupon);

        $now = Carbon::now();
        if ($coupon->starts_at && $now->lt($coupon->starts_at)) return $this->fail('not_started', $coupon);
        if ($coupon->ends_at && $now->gt($coupon->ends_at))      return $this->fail('expired', $coupon);

        if (! $coupon->hasUsesLeft()) return $this->fail('exhausted', $coupon);

        if ($tenant && $coupon->usesLeftFor($tenant) <= 0) {
            return $this->fail('tenant_exhausted', $coupon);
        }

        if ($planKey !== null && ! $coupon->isApplicableToPlan($planKey)) {
            return $this->fail('plan_mismatch', $coupon);
        }

        // Price-shaped discounts can't apply to a $0 plan.  Trial
        // extensions are fine on free plans (extends the trial).
        if ($basePrice <= 0 && $coupon->discount_type !== Coupon::TYPE_TRIAL_EXTENSION) {
            return $this->fail('invalid_for_zero', $coupon);
        }

        if ($coupon->discount_type === Coupon::TYPE_FIXED
            && $coupon->currency !== null
            && $currency !== null
            && strtoupper($coupon->currency) !== strtoupper($currency)
        ) {
            return $this->fail('currency_mismatch', $coupon);
        }

        return [
            'valid'          => true,
            'coupon'         => $coupon,
            'reason'         => null,
            'reason_label'   => null,
            'discount'       => $coupon->calculateDiscount($basePrice, $currency),
            'extension_days' => $coupon->trialExtensionDays(),
        ];
    }

    /**
     * Atomic redemption.  Re-validates inside the transaction to
     * avoid the race where two tenants exhaust the last redemption
     * concurrently.  Throws \RuntimeException on validation failure.
     */
    public function redeem(
        Coupon $coupon,
        Tenant $tenant,
        ?string $planKey,
        float $basePrice,
        ?string $currency,
        array $metadata = [],
    ): CouponRedemption {
        return DB::transaction(function () use ($coupon, $tenant, $planKey, $basePrice, $currency, $metadata) {
            // Lock the row so the times_used check is consistent
            // when two concurrent checkouts race for the last seat.
            /** @var Coupon $locked */
            $locked = Coupon::query()
                ->whereKey($coupon->id)
                ->lockForUpdate()
                ->firstOrFail();

            $check = $this->validate($locked->code, $tenant, $planKey, $basePrice, $currency);
            if (! $check['valid']) {
                throw new \RuntimeException(
                    self::reasons()[$check['reason']] ?? __('services/coupon.no_longer_valid'),
                );
            }

            $discountAmount = (float) $check['discount'];
            $extensionDays  = (int)  $check['extension_days'];

            // 1. Counter increment
            $locked->increment('times_used');

            // 2. Audit row
            $redemption = CouponRedemption::create([
                'coupon_id'         => $locked->id,
                'tenant_id'         => $tenant->id,
                'plan_key'          => $planKey,
                'amount_discounted' => $discountAmount,
                'currency'          => $currency ? strtoupper($currency) : null,
                'redeemed_at'       => Carbon::now(),
                'metadata'          => $metadata,
            ]);

            // 3. Trial-extension side effect
            if ($extensionDays > 0) {
                $base = $tenant->trial_ends_at && $tenant->trial_ends_at->isFuture()
                    ? $tenant->trial_ends_at
                    : Carbon::now();
                $tenant->forceFill([
                    'trial_ends_at' => $base->copy()->addDays($extensionDays),
                ])->save();
            }

            // 4. Persistent audit log (separate from the redemption row —
            //    the audit is for the SA "tenant timeline", the redemption
            //    is for the analytics report).
            $this->logAudit($tenant, $locked, $redemption);

            return $redemption;
        });
    }

    protected function fail(string $reason, ?Coupon $coupon = null): array
    {
        return [
            'valid'          => false,
            'coupon'         => $coupon,
            'reason'         => $reason,
            'reason_label'   => self::reasons()[$reason] ?? __('services/coupon.invalid_code'),
            'discount'       => 0.0,
            'extension_days' => 0,
        ];
    }

    protected function normalizeCode(?string $code): string
    {
        return strtoupper(trim((string) $code));
    }

    protected function logAudit(Tenant $tenant, Coupon $coupon, CouponRedemption $redemption): void
    {
        try {
            AuditLog::record(
                action: 'subscription.coupon_redeemed',
                auditable: $tenant,
                oldValues: [],
                newValues: [
                    'tenant_name'       => $tenant->name,
                    'coupon_code'       => $coupon->code,
                    'discount_type'     => $coupon->discount_type,
                    'amount_discounted' => (float) $redemption->amount_discounted,
                    'currency'          => $redemption->currency,
                    'plan_key'          => $redemption->plan_key,
                    'extension_days'    => $coupon->trialExtensionDays(),
                ],
                tags: 'subscription,coupon,billing',
            );
        } catch (\Throwable $e) {
            Log::warning('Failed to write coupon audit log', [
                'tenant_id' => $tenant->id,
                'coupon_id' => $coupon->id,
                'error'     => $e->getMessage(),
            ]);
        }
    }
}
