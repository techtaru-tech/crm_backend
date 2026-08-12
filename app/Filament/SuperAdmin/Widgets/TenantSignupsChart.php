<?php

declare(strict_types=1);

namespace App\Filament\SuperAdmin\Widgets;

use App\Models\Tenant;
use Carbon\CarbonImmutable;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

/**
 * 12-month bar chart of new tenant signups (created_at), grouped by
 * calendar month.  Pairs visually with the MRR trend chart.  We
 * deliberately bucket month-by-month in PHP rather than via SQL
 * GROUP BY date_format(...) so the widget remains driver-agnostic
 * (MySQL / PostgreSQL / SQLite).
 */
class TenantSignupsChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 1;

    public function getHeading(): ?string
    {
        // Translator-first with English fallback (matches the pattern in
        // App\Support\Currency::label()). Using getHeading() instead of
        // the $heading property so the title resolves at render time and
        // honours the active locale on every paint.
        $key   = 'filament/super_admin_widgets.tenant_signups.heading';
        $trans = __($key);
        return is_string($trans) && $trans !== $key
            ? $trans
            : 'New tenant signups (last 12 months)';
    }

    protected function getData(): array
    {
        // Stampede guard — see SaasStatsOverview for rationale.
        return Cache::lock('sa-dashboard:signups:v1:lock', 10)->block(5, fn () =>
            Cache::remember('sa-dashboard:signups:v1', 300, function (): array {
            $labels = [];
            $values = [];

            $end = CarbonImmutable::now()->startOfMonth();

            for ($i = 11; $i >= 0; $i--) {
                $month = $end->subMonths($i);
                $nextMonth = $month->addMonth();

                $count = Tenant::query()
                    ->where('created_at', '>=', $month)
                    ->where('created_at', '<', $nextMonth)
                    ->count();

                $labels[] = $month->translatedFormat('M Y');
                $values[] = $count;
            }

            $datasetLabelKey   = 'filament/super_admin_widgets.tenant_signups.dataset_label';
            $datasetLabelTrans = __($datasetLabelKey);
            $datasetLabel      = is_string($datasetLabelTrans) && $datasetLabelTrans !== $datasetLabelKey
                ? $datasetLabelTrans
                : 'Signups';

            return [
                'datasets' => [
                    [
                        'label' => $datasetLabel,
                        'data' => $values,
                        'backgroundColor' => 'rgba(245, 158, 11, 0.78)',
                        'borderColor' => 'rgba(245, 158, 11, 1)',
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
            'plugins' => [
                'legend' => ['display' => false],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => ['precision' => 0],
                    'grid' => ['color' => 'rgba(148, 163, 184, 0.15)'],
                ],
                'x' => [
                    'grid' => ['display' => false],
                ],
            ],
        ];
    }
}
