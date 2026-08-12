<?php

namespace App\Concerns;

use Livewire\Attributes\Url;

/**
 * Shared trait for Filament report pages:
 * - Persists date range selection in URL (Livewire #[Url])
 * - Falls back to session on mount if URL params are absent
 * - Writes to session on every property update
 */
trait HasReportDateRange
{
    #[Url(as: 'range', keep: true)]
    public string $dateRange = '30';

    #[Url(as: 'from', keep: true)]
    public string $dateFrom = '';

    #[Url(as: 'to', keep: true)]
    public string $dateTo = '';

    protected function initDateRange(): void
    {
        $key = 'report_date_range_' . static::class;

        if (! request()->has('range') && ! request()->has('from')) {
            $stored = session($key, []);
            if (! empty($stored['dateRange'])) {
                $this->dateRange = $stored['dateRange'];
                $this->dateFrom  = $stored['dateFrom'] ?? '';
                $this->dateTo    = $stored['dateTo']   ?? '';
            }
        }
    }

    public function updatedDateRange(): void
    {
        $this->persistDateRange();
    }

    public function updatedDateFrom(): void
    {
        $this->persistDateRange();
    }

    public function updatedDateTo(): void
    {
        $this->persistDateRange();
    }

    private function persistDateRange(): void
    {
        session()->put('report_date_range_' . static::class, [
            'dateRange' => $this->dateRange,
            'dateFrom'  => $this->dateFrom,
            'dateTo'    => $this->dateTo,
        ]);
    }
}
