<?php

declare(strict_types=1);

namespace App\Filament\SuperAdmin\Widgets;

use App\Services\BillingMetricsService;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

/**
 * Doughnut chart of tenants by subscription_status.
 *
 * Statuses come from BillingMetricsService::statusBreakdown(), which
 * already normalises the canonical set (trial / active / trial_expired
 * / cancelled / expired) and returns 0 for any missing bucket so the
 * legend stays stable across deploys.
 */
class SubscriptionStatusChart extends ChartWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 1;

    public function getHeading(): ?string
    {
        // Translator-first with English fallback (matches Currency::label
        // pattern). Resolves at render time so the active locale wins.
        $key   = 'filament/super_admin_widgets.subscription_status.heading';
        $trans = __($key);
        return is_string($trans) && $trans !== $key
            ? $trans
            : 'Subscription status mix';
    }

    protected function getData(): array
    {
        // Stampede guard — see SaasStatsOverview for rationale.
        return Cache::lock('sa-dashboard:status-mix:v1:lock', 10)->block(5, fn () =>
            Cache::remember('sa-dashboard:status-mix:v1', 300, function (): array {
            /** @var BillingMetricsService $billing */
            $billing = app(BillingMetricsService::class);
            $rows = $billing->statusBreakdown();

            // Drop empty buckets so the donut isn't dominated by 0-slices.
            $rows = array_filter($rows, fn (int $count): bool => $count > 0);

            // Brand-aligned palette (Option B): indigo for the happy
            // path, amber for trial, slate for finished/cancelled.
            $palette = [
                'active' => 'rgba(79, 70, 229, 0.92)',
                'trial' => 'rgba(245, 158, 11, 0.92)',
                'trial_expired' => 'rgba(244, 114, 182, 0.85)',
                'cancelled' => 'rgba(100, 116, 139, 0.85)',
                'expired' => 'rgba(148, 163, 184, 0.75)',
                'suspended' => 'rgba(239, 68, 68, 0.85)',
                'past_due' => 'rgba(217, 119, 6, 0.85)',
            ];

            $labels = [];
            $values = [];
            $colors = [];

            foreach ($rows as $status => $count) {
                // Translator-first with English fallback. The lang file
                // ships entries for the canonical set; unknown future
                // statuses fall through to the ucfirst() form so the
                // chart never renders a raw snake_case key.
                $statusKey  = 'filament/super_admin_widgets.subscription_status.status_' . $status;
                $translated = __($statusKey);
                $labels[]   = is_string($translated) && $translated !== $statusKey
                    ? $translated
                    : ucfirst(str_replace('_', ' ', (string) $status));
                $values[] = (int) $count;
                $colors[] = $palette[$status] ?? 'rgba(156, 163, 175, 0.85)';
            }

            return [
                'datasets' => [
                    [
                        'data' => $values,
                        'backgroundColor' => $colors,
                        'borderColor' => 'rgba(255, 255, 255, 1)',
                        'borderWidth' => 2,
                    ],
                ],
                'labels' => $labels,
            ];
        }));
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => ['boxWidth' => 12, 'usePointStyle' => true],
                ],
            ],
            'cutout' => '62%',
        ];
    }
}
