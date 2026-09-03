<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use Filament\Widgets\ChartWidget;

class LeadStatusChart extends ChartWidget
{
    // Sort 6: pairs with PipelineDistributionChart (sort 5) on the
    // second row of charts so half-width widgets never sit alone.
    protected static ?int    $sort    = 6;
    protected int | string | array $columnSpan = 1;

    public ?string $filter = '30';

    public function getHeading(): ?string
    {
        return __('filament/widgets.lead_status_chart_heading');
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

        $svc   = app(\App\Services\ReportService::class);
        $range = $this->filter ?? '30';

        [$fromDate, $toDate] = $svc->dateRange($range, null, null);

        // Cache the GROUP BY for 60 s — per (tenant, range) so each
        // filter selection caches independently.  Status counts don't
        // need second-by-second freshness.
        // The cache key carries the user, not just the tenant.  These figures
        // run through LeadVisibilityScope, so a manager's total is smaller
        // than an admin's — but keyed by tenant alone, whoever loaded the
        // dashboard first had their numbers served to everyone else for the
        // next 60 seconds.  A rep saw counts covering leads they cannot open,
        // and an admin saw a rep's smaller total and thought leads had
        // vanished.  The version bump drops the tenant-wide entries.
        $data = \App\Support\TenantCache::remember(
            $tenantId,
            "widget:lead-status-chart:tenant:{$tenantId}:user:{$userId}:range:{$range}:v2",
            60,
            fn () => Lead::where('tenant_id', $tenantId)
                ->whereBetween('created_at', [$fromDate, $toDate])
                ->selectRaw("COALESCE(status, 'new') as status, COUNT(*) as cnt")
                ->groupBy('status')
                ->orderByDesc('cnt')
                ->pluck('cnt', 'status')
                ->toArray(),
        );

        $colorMap = [
            'new'        => 'rgba(34,197,94,0.8)',
            'contacted'  => 'rgba(59,130,246,0.8)',
            'qualified'  => 'rgba(245,158,11,0.8)',
            'won'        => 'rgba(99,102,241,0.8)',
            'lost'       => 'rgba(239,68,68,0.8)',
            'nurturing'  => 'rgba(168,85,247,0.8)',
            'proposal'   => 'rgba(14,165,233,0.8)',
        ];

        $colors = [];
        foreach (array_keys($data) as $status) {
            $colors[] = $colorMap[$status] ?? 'rgba(156,163,175,0.8)';
        }

        // Translator-first labels — canonical enum cases resolve via
        // LeadStatus::label() (lang/<locale>/enums/lead_status.php);
        // unknown / legacy values fall back to ucfirst() so the chart
        // never renders a raw lowercase key.
        $labels = array_map(function (string $s): string {
            $enum = \App\Enums\LeadStatus::tryFrom($s);
            if ($enum) {
                return $enum->label();
            }
            $key        = 'enums/lead_status.' . $s;
            $translated = __($key);
            return is_string($translated) && $translated !== $key
                ? $translated
                : ucfirst($s);
        }, array_keys($data));

        return [
            'datasets' => [
                [
                    'data'            => array_values($data),
                    'backgroundColor' => $colors,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
