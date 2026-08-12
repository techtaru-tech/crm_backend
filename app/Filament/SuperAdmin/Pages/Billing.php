<?php

namespace App\Filament\SuperAdmin\Pages;

use App\Services\BillingMetricsService;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class Billing extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';
    protected string $view = 'filament.super-admin.pages.billing';
    protected static ?int $navigationSort = 10;

    public static function getNavigationLabel(): string
    {
        return __('filament/sa_billing.nav_label');
    }

    public function getTitle(): string|Htmlable
    {
        return __('filament/sa_billing.title');
    }

    public function getMetrics(): array
    {
        /** @var BillingMetricsService $svc */
        $svc = app(BillingMetricsService::class);

        // Every metric is wrapped so a single failing query (e.g. a
        // plan with a bad currency, a malformed subscription row, or
        // a DB unavailable at boot) doesn't 500 the whole Billing
        // dashboard. Instead the widget shows "—" for the broken
        // metric and the page still renders.
        return [
            'mrr'              => $this->safe(fn () => $svc->computeMrr(), 0.0),
            'arr'              => $this->safe(fn () => $svc->computeArr(), 0.0),
            'paying_tenants'   => $this->safe(fn () => $svc->payingTenants(), 0),
            'arpu'             => $this->safe(fn () => $svc->computeArpu(), 0.0),
            'churn_rate'       => $this->safe(fn () => $svc->churnRate(), 0.0),
            'currency'         => $this->safe(fn () => $svc->reportingCurrency(), 'USD'),
            'status_breakdown' => $this->safe(fn () => $svc->statusBreakdown(), []),
            'revenue_by_plan'  => $this->safe(fn () => $svc->revenueByPlan(), []),
            'tenant_growth'    => $this->safe(fn () => $svc->tenantGrowthByMonth(6), []),
            // Collection fallback (not []) — the blade calls Collection::isEmpty()
            // and iterates with @foreach on this key, so a [] fallback would
            // itself 500 the page if the underlying query fails, defeating the
            // safe() wrapper. Other keys above use array access / @foreach
            // only, which works on plain arrays.
            'recent_events'    => $this->safe(fn () => $svc->recentBillingEvents(8), collect()),
        ];
    }

    /**
     * Run a metric callable and return $fallback if it throws. Logs the
     * error at warning level so the operator can investigate without
     * the UI blowing up.
     */
    private function safe(callable $fn, mixed $fallback): mixed
    {
        try {
            return $fn();
        } catch (\Throwable $e) {
            logger()->warning('[Billing] metric failed', [
                'error' => $e->getMessage(),
            ]);
            return $fallback;
        }
    }
}
