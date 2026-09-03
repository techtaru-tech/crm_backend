<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use App\Models\PipelineStage;
use App\Models\Tenant;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RevenueForecastWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected function getColumns(): int
    {
        return 4;
    }

    protected function getStats(): array
    {
        $tenantId = auth()->user()?->tenant_id;
        $userId = (int) auth()->id();
        if (! $tenantId) {
            return [];
        }

        $currency = $this->resolveCurrency($tenantId);
        $symbol   = $this->currencySymbol($currency);

        // Cache the 4 aggregate queries for 60 s — uncached, this widget
        // alone fires 4 full leads-table aggregates on every dashboard
        // load.  Cache key is per-tenant + version so cache busts on
        // any version bump; 60 s is short enough that "won this month"
        // feels live and long enough to absorb dashboard refresh
        // bursts.
        // The cache key carries the user, not just the tenant.  These figures
        // run through LeadVisibilityScope, so a manager's total is smaller
        // than an admin's — but keyed by tenant alone, whoever loaded the
        // dashboard first had their numbers served to everyone else for the
        // next 60 seconds.  A rep saw counts covering leads they cannot open,
        // and an admin saw a rep's smaller total and thought leads had
        // vanished.  The version bump drops the tenant-wide entries.
        $aggregates = \App\Support\TenantCache::remember(
            $tenantId,
            "widget:revenue-forecast:tenant:{$tenantId}:user:{$userId}:v3",
            60,
            function () use ($tenantId): array {
                // H7 fix: post-migration 'converted' was coerced to 'won',
                // so the legacy whereNotIn(['converted','lost']) was
                // including won leads in open-pipeline (counted twice).
                // Use the canonical enum vocabulary.
                $terminalValues = \App\Enums\LeadStatus::terminalValues();

                $openPipeline = (float) Lead::query()
                    ->where('tenant_id', $tenantId)
                    ->whereNotIn('status', $terminalValues)
                    ->sum('deal_value');

                $weighted = (float) Lead::query()
                    ->where('leads.tenant_id', $tenantId)
                    ->whereNotIn('status', $terminalValues)
                    ->whereNotNull('deal_value')
                    ->join('pipeline_stages', 'pipeline_stages.id', '=', 'leads.pipeline_stage_id')
                    ->selectRaw('COALESCE(SUM(leads.deal_value * COALESCE(pipeline_stages.win_probability, 0) / 100), 0) AS weighted_sum')
                    ->value('weighted_sum');

                $wonThisMonth = (float) Lead::query()
                    ->where('tenant_id', $tenantId)
                    ->whereNotNull('won_at')
                    ->whereBetween('won_at', [now()->startOfMonth(), now()->endOfMonth()])
                    ->sum('won_value');

                $avgDeal = (float) Lead::query()
                    ->where('tenant_id', $tenantId)
                    ->whereNotNull('won_at')
                    ->where('won_at', '>=', now()->subDays(90))
                    ->avg('won_value');

                return [
                    'open_pipeline'   => $openPipeline,
                    'weighted'        => $weighted,
                    'won_this_month'  => $wonThisMonth,
                    'avg_deal'        => $avgDeal,
                ];
            },
        );

        $openPipeline = $aggregates['open_pipeline'];
        $weighted     = $aggregates['weighted'];
        $wonThisMonth = $aggregates['won_this_month'];
        $avgDeal      = $aggregates['avg_deal'];

        $fmt = fn(float $n): string => $symbol . number_format($n, 0);

        return [
            Stat::make(__('filament/widget_revenue_forecast.open_pipeline'), $fmt($openPipeline))
                ->description(__('filament/widget_revenue_forecast.open_pipeline_description'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),

            Stat::make(__('filament/widget_revenue_forecast.weighted'), $fmt($weighted))
                ->description(__('filament/widget_revenue_forecast.weighted_description'))
                ->descriptionIcon('heroicon-m-scale')
                ->color('info'),

            Stat::make(__('filament/widget_revenue_forecast.won_this_month'), $fmt($wonThisMonth))
                ->description(now()->translatedFormat('F Y'))
                ->descriptionIcon('heroicon-m-trophy')
                ->color('success'),

            Stat::make(__('filament/widget_revenue_forecast.avg_deal'), $fmt($avgDeal))
                ->description(__('filament/widget_revenue_forecast.avg_deal_description'))
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('warning'),
        ];
    }

    protected function resolveCurrency(int $tenantId): string
    {
        $tenant = Tenant::find($tenantId);
        if ($tenant && is_array($tenant->settings ?? null)) {
            $c = $tenant->settings['default_currency'] ?? $tenant->settings['currency'] ?? null;
            if (is_string($c) && strlen($c) === 3) {
                return strtoupper($c);
            }
        }
        return 'USD';
    }

    protected function currencySymbol(string $code): string
    {
        return match (strtoupper($code)) {
            'USD', 'AUD', 'CAD' => '$',
            'EUR'               => '€',
            'GBP'               => '£',
            'INR'               => '₹',
            'AED'               => 'AED ',
            'SAR'               => 'SAR ',
            'JPY', 'CNY'        => '¥',
            default             => strtoupper($code) . ' ',
        };
    }
}
