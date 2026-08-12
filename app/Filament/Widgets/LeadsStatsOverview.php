<?php

namespace App\Filament\Widgets;

use App\Services\ReportService;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LeadsStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getColumns(): int
    {
        return 4;
    }

    protected function getStats(): array
    {
        $tenantId = auth()->user()?->tenant_id;
        if (! $tenantId) {
            return [];
        }

        // dashboardStats() fires ~10 aggregate queries against the
        // leads table.  Cache for 60 s per tenant — uncached this is
        // the heaviest widget on the tenant dashboard and re-runs
        // every page load.  60 s is short enough that conversion-rate
        // / new-today numbers stay roughly live and long enough to
        // absorb refresh bursts.
        $d = \App\Support\TenantCache::remember(
            $tenantId,
            "widget:dashboard-stats:tenant:{$tenantId}:v1",
            60,
            fn () => app(ReportService::class)->dashboardStats($tenantId),
        );

        $trendDesc = function (int $current, int $previous, string $vsLabel): string {
            if ($previous === 0 && $current === 0) {
                return __('filament/widget_leads_stats.no_data_for_comparison');
            }
            if ($previous === 0) {
                return __('filament/widget_leads_stats.trend_no_prior', ['label' => $vsLabel]);
            }
            $pct  = round(($current - $previous) / $previous * 100, 1);
            $sign = $pct >= 0 ? '+' : '';
            return __('filament/widget_leads_stats.trend_vs', [
                'sign'  => $sign,
                'pct'   => $pct,
                'label' => $vsLabel,
            ]);
        };

        $trendIcon = fn(int $current, int $previous): string =>
            ($current >= $previous)
                ? 'heroicon-m-arrow-trending-up'
                : 'heroicon-m-arrow-trending-down';

        $trendColor = fn(int $current, int $previous, bool $lowerIsBetter = false): string => match (true) {
            $current === $previous => 'gray',
            ($current > $previous) => $lowerIsBetter ? 'danger' : 'success',
            default                => $lowerIsBetter ? 'success' : 'warning',
        };

        $avgResp      = $d['avg_response_minutes'];
        $prevAvgResp  = $d['prev_avg_response'];
        $avgRespLabel = $avgResp > 0
            ? ($avgResp < 60
                ? $avgResp . __('filament/widget_leads_stats.minute_short')
                : round($avgResp / 60, 1) . __('filament/widget_leads_stats.hour_short'))
            : '—';

        $convChange = $d['conversion_rate'] - $d['prev_conversion'];

        $vsPrev30Days = __('filament/widget_leads_stats.vs_label_prev_30_days');
        $vsYesterday  = __('filament/widget_leads_stats.vs_label_yesterday');
        $vsLastWeek   = __('filament/widget_leads_stats.vs_label_last_week');
        $vsLast30Days = __('filament/widget_leads_stats.vs_label_last_30_days');
        $vsPriorPer   = __('filament/widget_leads_stats.vs_label_prior_period');

        return [
            Stat::make(__('filament/widget_leads_stats.total_leads'), number_format($d['total_leads']))
                ->description($trendDesc($d['new_this_month'], $d['prev_month_leads'], $vsPrev30Days) . ' ' . __('filament/widget_leads_stats.suffix_new_leads'))
                ->descriptionIcon($trendIcon($d['new_this_month'], $d['prev_month_leads']))
                ->color($trendColor($d['new_this_month'], $d['prev_month_leads'])),

            Stat::make(__('filament/widget_leads_stats.new_today'), $d['new_today'])
                ->description($trendDesc($d['new_today'], $d['new_yesterday'], $vsYesterday))
                ->descriptionIcon($trendIcon($d['new_today'], $d['new_yesterday']))
                ->color($trendColor($d['new_today'], $d['new_yesterday'])),

            Stat::make(__('filament/widget_leads_stats.new_this_week'), $d['new_this_week'])
                ->description($trendDesc($d['new_this_week'], $d['new_last_week'], $vsLastWeek))
                ->descriptionIcon($trendIcon($d['new_this_week'], $d['new_last_week']))
                ->color($trendColor($d['new_this_week'], $d['new_last_week'])),

            Stat::make(__('filament/widget_leads_stats.this_month'), $d['new_this_month'])
                ->description($trendDesc($d['new_this_month'], $d['prev_month_leads'], $vsLast30Days))
                ->descriptionIcon($trendIcon($d['new_this_month'], $d['prev_month_leads']))
                ->color($trendColor($d['new_this_month'], $d['prev_month_leads'])),

            Stat::make(__('filament/widget_leads_stats.conversion_rate'), $d['conversion_rate'] . '%')
                ->description(__('filament/widget_leads_stats.conversion_trend', [
                    'sign' => $convChange >= 0 ? '+' : '',
                    'pct'  => $convChange,
                ]))
                ->descriptionIcon($convChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($convChange >= 0 ? 'success' : 'danger'),

            Stat::make(__('filament/widget_leads_stats.active_in_pipeline'), number_format($d['active_pipeline_leads']))
                ->description($trendDesc($d['active_pipeline_leads'], $d['prev_active_pipeline'], $vsPriorPer))
                ->descriptionIcon($trendIcon($d['active_pipeline_leads'], $d['prev_active_pipeline']))
                ->color($trendColor($d['active_pipeline_leads'], $d['prev_active_pipeline'])),

            Stat::make(__('filament/widget_leads_stats.avg_response_time'), $avgRespLabel)
                ->description($prevAvgResp > 0
                    ? $trendDesc($avgResp, $prevAvgResp, $vsPrev30Days) . ' ' . __('filament/widget_leads_stats.suffix_lower_is_better')
                    : __('filament/widget_leads_stats.time_to_first_activity'))
                ->descriptionIcon($trendIcon($avgResp, $prevAvgResp))
                ->color($trendColor($avgResp, $prevAvgResp, lowerIsBetter: true)),

            Stat::make(__('filament/widget_leads_stats.won_deals'), number_format($d['leads_won']))
                ->description($d['total_leads'] > 0
                    ? __('filament/widget_leads_stats.suffix_of_all_leads', [
                        'pct' => number_format($d['leads_won'] / $d['total_leads'] * 100, 1),
                    ])
                    : __('filament/widget_leads_stats.no_leads_yet'))
                ->descriptionIcon('heroicon-m-trophy')
                ->color('success'),
        ];
    }
}
