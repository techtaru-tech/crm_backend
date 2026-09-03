<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\PageRequiresPermission;

use App\Concerns\HasReportDateRange;
use App\Models\Lead;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;

class SourceAttributionReport extends Page
{
    use PageRequiresPermission;

    protected static string $requiredPermission = 'reports.view';

    use HasReportDateRange;

    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-chart-bar';
    protected static string|\UnitEnum|null $navigationGroup = 'Reports';
    protected static ?int    $navigationSort  = 5;
    protected string $view = 'filament.pages.source-attribution-report';

    public function mount(): void
    {
        $this->initDateRange();
    }

    public static function getNavigationLabel(): string
    {
        return __('filament/source_attribution_report.nav_label');
    }

    public function getTitle(): string
    {
        return __('filament/source_attribution_report.title');
    }

    public function getTableData(): array
    {
        $tenantId = auth()->user()?->tenant_id;
        if (! $tenantId) {
            return [];
        }

        // Delegate date-range resolution to ReportService so this
        // page picks up any new presets (this_month, last_month, etc.)
        // the rest of the report family already supports without
        // having to maintain a parallel match here.
        [$from, $to] = app(\App\Services\ReportService::class)->dateRange(
            $this->dateRange,
            $this->dateFrom ?: null,
            $this->dateTo   ?: null,
        );

        $rows = Lead::query()
            ->where('tenant_id', $tenantId)
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw("
                COALESCE(utm_source, '(direct)')     as utm_source,
                COALESCE(utm_campaign, '(none)')     as utm_campaign,
                COUNT(*)                             as total_leads,
                SUM(CASE WHEN status = 'converted' OR won_at IS NOT NULL THEN 1 ELSE 0 END) as won_leads,
                COALESCE(SUM(won_value), 0)          as total_won_value
            ")
            ->groupBy('utm_source', 'utm_campaign')
            ->orderByDesc('total_leads')
            ->get();

        return $rows->map(function ($row) {
            $total   = (int) $row->total_leads;
            $won     = (int) $row->won_leads;
            $conv    = $total > 0 ? round($won / $total * 100, 1) : 0.0;
            return [
                'utm_source'      => $row->utm_source,
                'utm_campaign'    => $row->utm_campaign,
                'total_leads'     => $total,
                'won_leads'       => $won,
                'conversion_rate' => $conv,
                'total_won_value' => (float) $row->total_won_value,
            ];
        })->all();
    }
}
