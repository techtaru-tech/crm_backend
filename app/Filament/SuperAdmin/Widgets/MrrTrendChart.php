<?php

declare(strict_types=1);

namespace App\Filament\SuperAdmin\Widgets;

use App\Models\TenantBillingReceipt;
use Carbon\CarbonImmutable;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

/**
 * 12-month revenue trend derived from collected billing receipts.
 *
 * receipts.amount is the source of truth for "money collected" — using
 * tenant.plan would mix in trial/cancelled accounts and over-state MRR.
 * Result is cached for 5 minutes; the dashboard refreshes often and a
 * fresh aggregate query on every paint is wasteful.
 */
class MrrTrendChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 1;

    public function getHeading(): ?string
    {
        // Translator-first with English fallback (matches Currency::label
        // pattern). Resolves at render time so the active locale wins.
        $key   = 'filament/super_admin_widgets.mrr_trend.heading';
        $trans = __($key);
        return is_string($trans) && $trans !== $key
            ? $trans
            : 'Revenue collected (last 12 months)';
    }

    protected function getData(): array
    {
        // Stampede guard — see SaasStatsOverview for rationale.
        return Cache::lock('sa-dashboard:mrr-trend:v1:lock', 10)->block(5, fn () =>
            Cache::remember('sa-dashboard:mrr-trend:v1', 300, function (): array {
            $labels = [];
            $values = [];

            $end = CarbonImmutable::now()->startOfMonth();

            for ($i = 11; $i >= 0; $i--) {
                $month = $end->subMonths($i);
                $nextMonth = $month->addMonth();

                $sum = (float) TenantBillingReceipt::query()
                    ->where('issued_at', '>=', $month)
                    ->where('issued_at', '<', $nextMonth)
                    ->sum('amount');

                $labels[] = $month->translatedFormat('M Y');
                $values[] = round($sum, 2);
            }

            $datasetLabelKey   = 'filament/super_admin_widgets.mrr_trend.dataset_label';
            $datasetLabelTrans = __($datasetLabelKey);
            $datasetLabel      = is_string($datasetLabelTrans) && $datasetLabelTrans !== $datasetLabelKey
                ? $datasetLabelTrans
                : 'Revenue';

            return [
                'datasets' => [
                    [
                        'label' => $datasetLabel,
                        'data' => $values,
                        'fill' => true,
                        'borderColor' => 'rgba(79, 70, 229, 1)',
                        'backgroundColor' => 'rgba(79, 70, 229, 0.18)',
                        'pointBackgroundColor' => 'rgba(245, 158, 11, 1)',
                        'pointBorderColor' => 'rgba(255, 255, 255, 1)',
                        'pointRadius' => 4,
                        'tension' => 0.35,
                    ],
                ],
                'labels' => $labels,
            ];
        }));
    }

    protected function getType(): string
    {
        return 'line';
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
