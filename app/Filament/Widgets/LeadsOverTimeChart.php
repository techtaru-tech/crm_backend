<?php

namespace App\Filament\Widgets;

use App\Services\ReportService;
use Filament\Widgets\ChartWidget;

class LeadsOverTimeChart extends ChartWidget
{
    // Sort 3: after both full-width stats widgets (LeadsStatsOverview=1,
    // RevenueForecast=2) so this half-width chart pairs cleanly with
    // LeadsBySourceChart (sort 4) on the same row.
    protected static ?int    $sort    = 3;
    protected int | string | array $columnSpan = 1;

    public ?string $filter = '30';

    public function getHeading(): ?string
    {
        return __('filament/widgets.leads_over_time_chart_heading');
    }

    protected function getFilters(): ?array
    {
        return [
            '7'          => __('components.rdr_range_last_7_days'),
            '30'         => __('components.rdr_range_last_30_days'),
            '60'         => __('components.rdr_range_last_60_days'),
            '90'         => __('components.rdr_range_last_90_days'),
            'this_month' => __('components.rdr_range_this_month'),
            'last_month' => __('components.rdr_range_last_month'),
        ];
    }

    protected function getData(): array
    {
        $tenantId = auth()->user()?->tenant_id;
        $userId = (int) auth()->id();
        if (! $tenantId) {
            return ['datasets' => [], 'labels' => []];
        }

        $svc    = app(ReportService::class);
        $range  = $this->filter ?? '30';

        [$fromDate, $toDate] = $svc->dateRange($range, null, null);

        // 60 s cache per (tenant, range) — same rationale as LeadStatusChart.
        // The cache key carries the user, not just the tenant.  These figures
        // run through LeadVisibilityScope, so a manager's total is smaller
        // than an admin's — but keyed by tenant alone, whoever loaded the
        // dashboard first had their numbers served to everyone else for the
        // next 60 seconds.  A rep saw counts covering leads they cannot open,
        // and an admin saw a rep's smaller total and thought leads had
        // vanished.  The version bump drops the tenant-wide entries.
        $result = \App\Support\TenantCache::remember(
            $tenantId,
            "widget:leads-over-time:tenant:{$tenantId}:user:{$userId}:range:{$range}:v2",
            60,
            fn () => $svc->leadsOverTime($tenantId, $fromDate, $toDate, 'day'),
        );

        return [
            'datasets' => [
                [
                    'label'           => __('filament/widgets.dataset_leads'),
                    'data'            => $result['data'],
                    'fill'            => true,
                    'borderColor'     => 'rgba(99, 102, 241, 1)',
                    'backgroundColor' => 'rgba(99, 102, 241, 0.15)',
                    'tension'         => 0.3,
                ],
            ],
            'labels' => $result['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
