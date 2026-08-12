<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Plan;
use App\Models\Tenant;
use Carbon\Carbon;

/**
 * Plan upgrade / downgrade proration math.
 *
 * Without this service, a tenant on Starter ($29/mo) who upgrades to
 * Pro ($79/mo) on day 15 of their billing cycle has three bad UX
 * options:
 *   - charged $79 immediately (loses ~$14 of unused Starter time)
 *   - charged nothing now (loses revenue this month)
 *   - has to wait until renewal (friction kills upgrades)
 *
 * Industry-standard fix: calculate the unused portion of the current
 * plan as a credit, charge the prorated portion of the new plan.
 *
 * Returns a uniform shape regardless of upgrade or downgrade:
 *   {
 *     credit:           amount of unused current-plan time (>=0)
 *     charge:           amount due for new plan to end of cycle (>=0)
 *     net:              credit - charge (negative = collect now,
 *                                        positive = leave as account credit)
 *     direction:        upgrade | downgrade | same
 *     prorated_days:    days remaining in current cycle
 *     cycle_days:       30 monthly, 365 yearly, etc.
 *   }
 *
 * The actual collection happens gateway-side (Stripe issues an
 * `invoiceitem`, Paystack adjusts the next charge).  This service is
 * gateway-agnostic — produces the contract data.
 */
class ProrationCalculator
{
    public const DIRECTION_UPGRADE   = 'upgrade';
    public const DIRECTION_DOWNGRADE = 'downgrade';
    public const DIRECTION_SAME      = 'same';

    /**
     * @return array{
     *   credit: float,
     *   charge: float,
     *   net: float,
     *   direction: string,
     *   prorated_days: int,
     *   cycle_days: int,
     *   currency: ?string,
     * }
     */
    public function calculate(Tenant $tenant, Plan $currentPlan, Plan $newPlan, ?Carbon $now = null): array
    {
        $now ??= Carbon::now();

        $currentPrice = (float) $currentPlan->price;
        $newPrice     = (float) $newPlan->price;

        $interval         = $currentPlan->interval ?? 'month';
        $cycleDaysApprox  = $this->cycleDaysApprox($interval);
        $cycleEnd         = $this->cycleEndFor($tenant, $now, $cycleDaysApprox);
        $cycleStart       = $this->cycleStartFor($cycleEnd, $interval, $tenant, $now);

        // Real cycle length so Feb (28/29 days), 31-day months, and
        // leap years all prorate correctly.  Falls back to the
        // approximation if the cycle span ends up <= 0 (e.g. clock
        // skew or bad data).
        $cycleDays = max(1, (int) round($cycleStart->diffInDays($cycleEnd, false)));
        if ($cycleDays <= 0) {
            $cycleDays = $cycleDaysApprox;
        }

        $remaining = max(0, (int) round($now->diffInDays($cycleEnd, false)));

        $direction = match (true) {
            $newPrice > $currentPrice => self::DIRECTION_UPGRADE,
            $newPrice < $currentPrice => self::DIRECTION_DOWNGRADE,
            default                   => self::DIRECTION_SAME,
        };

        $credit = $cycleDays > 0 ? round($currentPrice * $remaining / $cycleDays, 2) : 0.0;
        $charge = $cycleDays > 0 ? round($newPrice     * $remaining / $cycleDays, 2) : 0.0;

        return [
            'credit'        => $credit,
            'charge'        => $charge,
            'net'           => round($credit - $charge, 2),
            'direction'     => $direction,
            'prorated_days' => $remaining,
            'cycle_days'    => $cycleDays,
            'currency'      => $newPlan->currency ?? $currentPlan->currency,
        ];
    }

    /**
     * Approximate cycle length used for fallback walking and as a
     * safety floor.  Real day counts are derived per-cycle in
     * calculate() using subMonth/subYear arithmetic so February and
     * leap years prorate correctly.
     */
    protected function cycleDaysApprox(?string $interval): int
    {
        return match ($interval) {
            'year'  => 365,
            'week'  => 7,
            'day'   => 1,
            default => 30,
        };
    }

    /**
     * End of the current billing cycle for this tenant.  Prefers
     * subscription_ends_at when set; otherwise estimates from
     * created_at by walking forward by cycle_days until we land in
     * the future.
     */
    protected function cycleEndFor(Tenant $tenant, Carbon $now, int $cycleDays): Carbon
    {
        if ($tenant->subscription_ends_at && $tenant->subscription_ends_at->isFuture()) {
            return $tenant->subscription_ends_at;
        }

        // Walk forward by real calendar units (addMonth/addYear) so the
        // cycle end matches the cycle start computed in cycleStartFor()
        // — Feb cycles span 28/29 days, Jan/Mar 31, etc.  Walking by
        // fixed 30-day chunks drifts a few days per year and corrupts
        // the proration math near month boundaries.
        $cursor = $tenant->created_at?->copy() ?? $now->copy();
        while ($cursor->lte($now)) {
            $cursor = match ($cycleDays) {
                365 => $cursor->copy()->addYear(),
                7   => $cursor->copy()->addWeek(),
                1   => $cursor->copy()->addDay(),
                default => $cursor->copy()->addMonth(),
            };
        }
        return $cursor;
    }

    /**
     * Start of the cycle ending at $cycleEnd.  Uses real calendar
     * arithmetic (subMonth/subYear) so a March 1 → Feb 1 cycle is
     * correctly 28 days, not 30.  Pins to created_at (or now) when
     * the computed start would be in the future.
     */
    protected function cycleStartFor(Carbon $cycleEnd, ?string $interval, Tenant $tenant, Carbon $now): Carbon
    {
        $start = match ($interval) {
            'year'  => $cycleEnd->copy()->subYear(),
            'week'  => $cycleEnd->copy()->subWeek(),
            'day'   => $cycleEnd->copy()->subDay(),
            default => $cycleEnd->copy()->subMonth(),
        };

        if ($start->isAfter($now)) {
            $created = $tenant->created_at;
            $start = ($created instanceof Carbon) ? $created->copy() : $now->copy();
        }

        return $start;
    }
}
