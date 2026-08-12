<?php

namespace App\Services;

use App\Models\Tenant;
use App\Support\Currency;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Computes high-level billing metrics (MRR, ARR, churn, revenue by plan,
 * growth) from the tenants table. Designed as the data backbone of the
 * Super Admin Billing dashboard.
 *
 * Prices are pulled from the DB-backed plans catalogue via PlanService so
 * script owners can edit pricing from the admin UI without redeploy. MRR
 * is reported in the reporting currency configured on the General settings
 * page (defaults to USD); plans priced in a different currency are shown
 * in their own currency on the revenue-by-plan table.
 */
class BillingMetricsService
{
    public function __construct(protected PlanService $plans)
    {
    }

    /**
     * Reporting currency for the aggregate MRR/ARR/ARPU figures.
     * Pulled from `GeneralSettings::$currency` with USD fallback.
     */
    public function reportingCurrency(): string
    {
        return Currency::default();
    }

    /**
     * Total MRR across all active paid subscriptions, expressed in the
     * reporting currency. Plans denominated in other currencies are
     * included at face value (no FX conversion) — acceptable for the
     * single-operator CodeCanyon use case.
     *
     * M-P4: aggregated COUNT(*) GROUP BY plan instead of fetching every
     * tenant row.  At 1000 paying tenants this is ~5 plan rows
     * pulled from the DB instead of 1000 — the SA dashboard widget
     * stops blocking on the Tenant table scan.
     */
    public function computeMrr(): float
    {
        return (float) Tenant::query()
            ->where('active', true)
            ->where('subscription_status', \App\Enums\SubscriptionStatus::Active->value)
            ->selectRaw('plan, COUNT(*) AS cnt')
            ->groupBy('plan')
            ->pluck('cnt', 'plan')
            ->reduce(
                fn (float $carry, $cnt, $plan) => $carry + ((int) $cnt * $this->monthlyPriceFor((string) $plan)),
                0.0,
            );
    }

    public function computeArr(): float
    {
        return $this->computeMrr() * 12;
    }

    /**
     * Paying tenants = subscription_status 'active' AND non-zero price plan.
     *
     * M-P4: same plan-grouped aggregate as computeMrr — sum the cnt for
     * paid plans, ignore the free tier.  Avoids loading every tenant
     * row into memory just to call ->count() on the filtered subset.
     */
    public function payingTenants(): int
    {
        return (int) Tenant::query()
            ->where('active', true)
            ->where('subscription_status', \App\Enums\SubscriptionStatus::Active->value)
            ->selectRaw('plan, COUNT(*) AS cnt')
            ->groupBy('plan')
            ->pluck('cnt', 'plan')
            ->reduce(
                fn (int $carry, $cnt, $plan) => $carry + ($this->monthlyPriceFor((string) $plan) > 0 ? (int) $cnt : 0),
                0,
            );
    }

    /**
     * Average revenue per paying user (ARPU), in whole currency.
     */
    public function computeArpu(): float
    {
        $paying = $this->payingTenants();
        return $paying > 0 ? round($this->computeMrr() / $paying, 2) : 0.0;
    }

    /**
     * Rolling 30-day churn rate as a percentage.
     * churn% = (cancelled or expired in last 30d) / (active+cancelled+expired at t-30d) * 100
     */
    public function churnRate(int $days = 30): float
    {
        $since = Carbon::now()->subDays($days);

        $churned = Tenant::query()
            ->whereIn('subscription_status', [\App\Enums\SubscriptionStatus::Cancelled->value, \App\Enums\SubscriptionStatus::Expired->value])
            ->where('updated_at', '>=', $since)
            ->count();

        // Denominator: everyone who *could* have churned in this window.
        // Use current active paid tenants + churned as a pragmatic proxy.
        $baseline = Tenant::query()
            ->where('active', true)
            ->whereIn('subscription_status', [\App\Enums\SubscriptionStatus::Active->value, \App\Enums\SubscriptionStatus::Cancelled->value, \App\Enums\SubscriptionStatus::Expired->value])
            ->count();

        return $baseline > 0 ? round(($churned / $baseline) * 100, 2) : 0.0;
    }

    /**
     * Distribution of tenants across subscription statuses.
     */
    public function statusBreakdown(): array
    {
        $rows = Tenant::query()
            ->selectRaw('subscription_status, COUNT(*) as cnt')
            ->groupBy('subscription_status')
            ->pluck('cnt', 'subscription_status')
            ->toArray();

        // Ensure every known status is present (even with 0)
        foreach (['trial', 'active', 'trial_expired', 'cancelled', 'expired'] as $status) {
            $rows[$status] ??= 0;
        }

        return $rows;
    }

    /**
     * Revenue and customer count per plan. Returns an array:
     *  [ [key, name, price, currency, customers, mrr, is_free], ... ]
     */
    public function revenueByPlan(): array
    {
        $counts = Tenant::query()
            ->where('active', true)
            ->where('subscription_status', \App\Enums\SubscriptionStatus::Active->value)
            ->selectRaw('plan, COUNT(*) as cnt')
            ->groupBy('plan')
            ->pluck('cnt', 'plan')
            ->toArray();

        $rows = [];
        foreach ($this->plans->getAllPlans() as $key => $def) {
            $price     = (float) ($def['price'] ?? 0);
            $currency  = strtoupper($def['currency'] ?? $this->reportingCurrency());
            $customers = (int) ($counts[$key] ?? 0);
            $rows[]    = [
                'key'       => $key,
                'name'      => $def['name'] ?? ucfirst($key),
                'price'     => $price,
                'currency'  => $currency,
                'customers' => $customers,
                'mrr'       => $price * $customers,
                'is_free'   => $price <= 0,
            ];
        }

        return $rows;
    }

    /**
     * Tenant growth over the last N months for a small line chart.
     * Returns [ ['label' => 'Jan 2026', 'new_tenants' => 4, 'total' => 42], ... ]
     */
    public function tenantGrowthByMonth(int $months = 6): array
    {
        $rows = [];
        $now  = Carbon::now()->startOfMonth();

        for ($i = $months - 1; $i >= 0; $i--) {
            $month       = (clone $now)->subMonths($i);
            $nextMonth   = (clone $month)->addMonth();
            $newCount    = Tenant::whereBetween('created_at', [$month, $nextMonth])->count();
            $totalAtEnd  = Tenant::where('created_at', '<', $nextMonth)->count();

            $rows[] = [
                'label'        => $month->translatedFormat('M Y'),
                'new_tenants'  => $newCount,
                'total'        => $totalAtEnd,
            ];
        }

        return $rows;
    }

    /**
     * Recent events for the activity feed on the Billing page.
     */
    public function recentBillingEvents(int $limit = 8): Collection
    {
        return Tenant::query()
            ->whereIn('subscription_status', [\App\Enums\SubscriptionStatus::Active->value, \App\Enums\SubscriptionStatus::Cancelled->value, \App\Enums\SubscriptionStatus::Expired->value, \App\Enums\SubscriptionStatus::TrialExpired->value])
            ->orderByDesc('updated_at')
            ->limit($limit)
            ->get(['id', 'name', 'plan', 'subscription_status', 'updated_at']);
    }

    /**
     * Resolve the monthly price for a plan key via the cached plan catalogue.
     * Returns a float so fractional prices (e.g. $9.99) survive the math.
     */
    protected function monthlyPriceFor(?string $planKey): float
    {
        if (! $planKey) {
            return 0.0;
        }

        $def = $this->plans->getAllPlans()[$planKey] ?? null;

        return $def ? (float) ($def['price'] ?? 0) : 0.0;
    }
}
