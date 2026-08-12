<?php

declare(strict_types=1);

namespace App\Filament\SuperAdmin\Widgets;

use App\Enums\LeadSource;
use App\Models\Lead;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

/**
 * Top 5 lead-source connectors across all tenants.
 *
 * Aggregates leads.source via GROUP BY — fast even on large tables
 * because most installs index the column.  Falls back to the raw
 * value when the source isn't a known LeadSource enum case (e.g.
 * legacy imports or custom connector keys).
 */
class LeadSourceMixChart extends ChartWidget
{
    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 1;

    public function getHeading(): ?string
    {
        // Translator-first with English fallback (matches Currency::label
        // pattern). Resolves at render time so the active locale wins.
        $key   = 'filament/super_admin_widgets.lead_source_mix.heading';
        $trans = __($key);
        return is_string($trans) && $trans !== $key
            ? $trans
            : 'Top 5 lead sources (all tenants)';
    }

    protected function getData(): array
    {
        // Stampede guard — see SaasStatsOverview for rationale.
        return Cache::lock('sa-dashboard:source-mix:v1:lock', 10)->block(5, fn () =>
            Cache::remember('sa-dashboard:source-mix:v1', 300, function (): array {
            $rows = Lead::query()
                ->selectRaw("COALESCE(NULLIF(source, ''), 'unknown') as source, COUNT(*) as cnt")
                ->groupBy('source')
                ->orderByDesc('cnt')
                ->limit(5)
                ->pluck('cnt', 'source')
                ->toArray();

            $labels = [];
            $values = [];

            foreach ($rows as $source => $count) {
                $enum = LeadSource::tryFrom((string) $source);
                if ($enum) {
                    $labels[] = $enum->label();
                } else {
                    // Translator-first fallback for non-enum legacy sources so the
                    // chart never renders raw English for tenant-custom values.
                    $srcKey   = 'lead_sources.' . strtolower((string) $source);
                    $srcTrans = __($srcKey);
                    $labels[] = is_string($srcTrans) && $srcTrans !== $srcKey
                        ? $srcTrans
                        : ucfirst(str_replace('_', ' ', (string) $source));
                }
                $values[] = (int) $count;
            }

            $datasetLabelKey   = 'filament/super_admin_widgets.lead_source_mix.dataset_label';
            $datasetLabelTrans = __($datasetLabelKey);
            $datasetLabel      = is_string($datasetLabelTrans) && $datasetLabelTrans !== $datasetLabelKey
                ? $datasetLabelTrans
                : 'Leads';

            return [
                'datasets' => [
                    [
                        'label' => $datasetLabel,
                        'data' => $values,
                        'backgroundColor' => 'rgba(79, 70, 229, 0.78)',
                        'borderColor' => 'rgba(79, 70, 229, 1)',
                        'borderRadius' => 6,
                        'borderWidth' => 1,
                    ],
                ],
                'labels' => $labels,
            ];
        }));
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'plugins' => ['legend' => ['display' => false]],
            'scales' => [
                'x' => [
                    'beginAtZero' => true,
                    'ticks' => ['precision' => 0],
                    'grid' => ['color' => 'rgba(148, 163, 184, 0.15)'],
                ],
                'y' => [
                    'grid' => ['display' => false],
                ],
            ],
        ];
    }
}
