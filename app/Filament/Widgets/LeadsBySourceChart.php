<?php

namespace App\Filament\Widgets;

use App\Services\ReportService;
use Filament\Widgets\ChartWidget;

class LeadsBySourceChart extends ChartWidget
{
    // Sort 4: pairs with LeadsOverTimeChart (sort 3) on the same row.
    protected static ?int    $sort    = 4;
    protected int | string | array $columnSpan = 1;

    public ?string $filter = '30';

    public function getHeading(): ?string
    {
        return __('filament/widgets.leads_by_source_chart_heading');
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

        $svc   = app(ReportService::class);
        $range = $this->filter ?? '30';

        [$fromDate, $toDate] = $svc->dateRange($range, null, null);

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
            "widget:leads-by-source:tenant:{$tenantId}:user:{$userId}:range:{$range}:v2",
            60,
            fn () => $svc->leadsBySource($tenantId, $fromDate, $toDate),
        );

        $colors = [
            'rgba(99,102,241,0.8)',  'rgba(16,185,129,0.8)',  'rgba(245,158,11,0.8)',
            'rgba(239,68,68,0.8)',   'rgba(59,130,246,0.8)',  'rgba(168,85,247,0.8)',
            'rgba(236,72,153,0.8)',  'rgba(14,165,233,0.8)',  'rgba(234,179,8,0.8)',
            'rgba(132,204,22,0.8)',  'rgba(20,184,166,0.8)',  'rgba(249,115,22,0.8)',
        ];

        // Translator-first labels — canonical enum cases resolve via
        // LeadSource::label() (lang/<locale>/enums/lead_source.php);
        // unknown / legacy values try lang/<locale>/lead_sources.<key>
        // before falling back to a humanised key so the chart never
        // renders a raw lowercase slug like "meta" or "google_ads".
        $labels = array_map(function (string $s): string {
            $enum = \App\Enums\LeadSource::tryFrom($s);
            if ($enum) {
                return $enum->label();
            }
            $key        = 'lead_sources.' . $s;
            $translated = __($key);
            if (is_string($translated) && $translated !== $key) {
                return $translated;
            }
            return ucfirst(str_replace('_', ' ', $s));
        }, array_keys($data));

        return [
            'datasets' => [
                [
                    'data'            => array_values($data),
                    'backgroundColor' => array_slice($colors, 0, count($data)),
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
