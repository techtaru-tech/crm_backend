<?php

declare(strict_types=1);

namespace App\Filament\SuperAdmin\Widgets;

use App\Models\Tenant;
use App\Models\TenantBillingReceipt;
use App\Services\BillingMetricsService;
use App\Support\Currency;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

/**
 * Top-row KPI summary for the Super Admin dashboard.
 *
 * Reads live numbers from Tenants + BillingMetricsService and falls
 * back to safe zeroes if any query throws — the dashboard must keep
 * rendering even if a single metric is broken.  All queries are
 * cached for 5 minutes to avoid pounding the DB on every refresh.
 */
class SaasStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getColumns(): int
    {
        return 4;
    }

    protected function getStats(): array
    {
        // Lock-around-remember prevents the cache stampede that would
        // otherwise hit MySQL when several SA admins refresh the
        // dashboard at the same expiry tick — all of their browsers
        // would race to recompute the closure simultaneously.  First
        // holder warms the cache; the others wait up to 5s and then
        // read the freshly-written value.
        $stats = Cache::lock('sa-dashboard:overview:v1:lock', 10)->block(5, fn () =>
            Cache::remember('sa-dashboard:overview:v1', 300, function (): array {
            /** @var BillingMetricsService $billing */
            $billing = app(BillingMetricsService::class);

            $totalTenants = Tenant::count();
            $activeSubs = Tenant::where('active', true)
                ->where('subscription_status', 'active')
                ->count();
            $signupsThisMonth = Tenant::where('created_at', '>=', now()->startOfMonth())->count();
            $signupsLastMonth = Tenant::whereBetween('created_at', [
                now()->subMonth()->startOfMonth(),
                now()->subMonth()->endOfMonth(),
            ])->count();

            return [
                'total_tenants' => $totalTenants,
                'active_subs' => $activeSubs,
                'mrr' => (float) $billing->computeMrr(),
                'currency' => $billing->reportingCurrency(),
                'signups_month' => $signupsThisMonth,
                'signups_prev' => $signupsLastMonth,
                'paid_collected' => (float) TenantBillingReceipt::where('issued_at', '>=', now()->startOfMonth())
                    ->sum('amount'),
            ];
        }));

        $signupTrend = $this->trendDescription(
            (int) $stats['signups_month'],
            (int) $stats['signups_prev'],
            __('filament/widget_saas_stats.vs_last_month'),
        );

        return [
            Stat::make(__('filament/widget_saas_stats.total_tenants'), number_format((int) $stats['total_tenants']))
                ->description(__('filament/widget_saas_stats.total_tenants_description'))
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary'),

            Stat::make(__('filament/widget_saas_stats.active_subs'), number_format((int) $stats['active_subs']))
                ->description(__('filament/widget_saas_stats.active_subs_description'))
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),

            Stat::make(__('filament/widget_saas_stats.mrr'), Currency::format((float) $stats['mrr'], (string) $stats['currency']))
                ->description(__('filament/widget_saas_stats.mrr_description'))
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('warning'),

            Stat::make(__('filament/widget_saas_stats.signups_month'), number_format((int) $stats['signups_month']))
                ->description($signupTrend)
                ->descriptionIcon($stats['signups_month'] >= $stats['signups_prev']
                    ? 'heroicon-m-arrow-trending-up'
                    : 'heroicon-m-arrow-trending-down')
                ->color($stats['signups_month'] >= $stats['signups_prev'] ? 'success' : 'warning'),
        ];
    }

    private function trendDescription(int $current, int $previous, string $vsLabel): string
    {
        if ($previous === 0 && $current === 0) {
            return __('filament/widget_saas_stats.no_data_for_comparison');
        }
        if ($previous === 0) {
            return __('filament/widget_saas_stats.new_no_prior_data', ['label' => $vsLabel]);
        }
        $pct = round(($current - $previous) / $previous * 100, 1);
        $sign = $pct >= 0 ? '+' : '';

        return __('filament/widget_saas_stats.trend_vs', ['sign' => $sign, 'pct' => (string) $pct, 'label' => $vsLabel]);
    }
}
