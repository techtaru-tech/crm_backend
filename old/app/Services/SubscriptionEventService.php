<?php

namespace App\Services;

use App\Events\PaymentFailed;
use App\Events\PlanChanged;
use App\Events\SubscriptionActivated;
use App\Events\SubscriptionCancelled;
use App\Models\Tenant;

/**
 * Thin facade the billing code calls to fire subscription events.
 * Concentrating dispatch in one place means future listeners (slack
 * webhooks, analytics, SA digests) can be wired up without hunting
 * through every controller and webhook handler.
 */
class SubscriptionEventService
{
    public function activated(Tenant $tenant, string $plan, ?string $billingCycle = null): void
    {
        event(new SubscriptionActivated($tenant, $plan, $billingCycle));
    }

    public function cancelled(Tenant $tenant, ?string $endsAt = null, ?string $reason = null): void
    {
        event(new SubscriptionCancelled($tenant, $endsAt, $reason));
    }

    public function paymentFailed(
        Tenant $tenant,
        ?string $amount = null,
        ?string $currency = null,
        int $retryAttempt = 1,
        ?string $nextRetryAt = null,
    ): void {
        event(new PaymentFailed($tenant, $amount, $currency, $retryAttempt, $nextRetryAt));
    }

    public function planChanged(Tenant $tenant, string $oldPlan, string $newPlan): void
    {
        $oldRank = $this->planRank($oldPlan);
        $newRank = $this->planRank($newPlan);

        $direction = match (true) {
            $newRank > $oldRank => 'upgrade',
            $newRank < $oldRank => 'downgrade',
            default             => 'change',
        };

        event(new PlanChanged($tenant, $oldPlan, $newPlan, $direction));
    }

    /**
     * Read the plan's sort rank from PlanService — the canonical
     * source that honours both config-seeded plans AND DB-defined
     * custom plans the buyer creates via PlanResource.  Reading
     * `config('plans.plans')` directly here previously meant any
     * custom plan added in the UI was rank-0 → every upgrade /
     * downgrade involving a custom plan was misclassified as
     * 'change', breaking analytics + win-back emails.
     *
     * Falls back to config on PlanService failure (early-boot
     * paths during install where the plans table isn't seeded yet).
     */
    private function planRank(string $key): int
    {
        try {
            $plans = app(\App\Services\PlanService::class)->getAllPlans();
            $plan  = is_iterable($plans) ? collect($plans)->firstWhere('key', $key) : null;
            if ($plan && isset($plan['sort'])) {
                return (int) $plan['sort'];
            }
        } catch (\Throwable) {
            // PlanService unavailable — fall through to config.
        }

        $plans = (array) config('plans.plans', []);
        return (int) ($plans[$key]['sort'] ?? 0);
    }
}
