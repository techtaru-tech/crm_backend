<?php

namespace App\Filament\Widgets;

use App\Services\ReportService;
use Filament\Widgets\ChartWidget;

class PipelineDistributionChart extends ChartWidget
{
    // Sort 5: pairs with LeadStatusChart (sort 6) on the second row of charts.
    protected static ?int    $sort    = 5;
    protected int | string | array $columnSpan = 1;

    public ?string $filter = '30';

    public function getHeading(): ?string
    {
        return __('filament/widgets.pipeline_distribution_chart_heading');
    }

    protected function getFilters(): ?array
    {
        return [
            'all'        => __('components.rdr_range_all_time'),
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

        $svc   = app(ReportService::class);
        $range = $this->filter ?? 'all';

        $fromDate = null;
        $toDate   = null;
        if ($range !== 'all') {
            [$fromDate, $toDate] = $svc->dateRange($range, null, null);
        }

        // 60 s cache per (tenant, range) — same rationale as LeadStatusChart.
        // The cache key carries the user, not just the tenant.  These figures
        // run through LeadVisibilityScope, so a manager's total is smaller
        // than an admin's — but keyed by tenant alone, whoever loaded the
        // dashboard first had their numbers served to everyone else for the
        // next 60 seconds.  A rep saw counts covering leads they cannot open,
        // and an admin saw a rep's smaller total and thought leads had
        // vanished.  The version bump drops the tenant-wide entries.
        $data = \App\Support\TenantCache::remember(
            $tenantId,
            "widget:pipeline-distribution:tenant:{$tenantId}:user:{$userId}:range:{$range}:v2",
            60,
            fn () => $svc->pipelineDistribution($tenantId, null, $fromDate, $toDate),
        );

        return [
            'datasets' => [
                [
                    'label'           => __('filament/widgets.dataset_leads'),
                    'data'            => array_values($data),
                    'backgroundColor' => 'rgba(99, 102, 241, 0.7)',
                    'borderRadius'    => 4,
                ],
            ],
            'labels' => array_keys($data),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'plugins'   => ['legend' => ['display' => false]],
            'scales'    => [
                'x' => ['beginAtZero' => true, 'grid' => ['display' => false]],
            ],
        ];
    }
}
