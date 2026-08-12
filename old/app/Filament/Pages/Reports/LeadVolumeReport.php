<?php

namespace App\Filament\Pages\Reports;

use App\Concerns\HasReportDateRange;
use App\Services\ReportService;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Livewire\Attributes\Url;

class LeadVolumeReport extends Page
{
    use HasReportDateRange;

    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-chart-bar';
    protected static string|\UnitEnum|null $navigationGroup = 'Reports';
    protected static ?int    $navigationSort  = 1;
    protected string $view = 'filament.pages.reports.lead-volume-report';

    #[Url(as: 'groupby', keep: true)]
    public string $groupBy = 'day';

    #[Url(as: 'source', keep: true)]
    public string $source = '';

    public function mount(): void
    {
        $this->initDateRange();
    }

    public static function getNavigationLabel(): string
    {
        return __('filament/reports.lead_volume_nav');
    }

    public function getTitle(): string
    {
        return __('filament/reports.lead_volume_title');
    }

    public function getChartData(): array
    {
        $tenantId = auth()->user()?->tenant_id;
        if (! $tenantId) {
            return ['labels' => [], 'data' => []];
        }
        $svc         = app(ReportService::class);
        [$from, $to] = $svc->dateRange($this->dateRange, $this->dateFrom ?: null, $this->dateTo ?: null);
        return $svc->leadsOverTime($tenantId, $from, $to, $this->groupBy, $this->source ?: null);
    }

    public function getTableData(): array
    {
        $tenantId = auth()->user()?->tenant_id;
        if (! $tenantId) {
            return [];
        }
        $svc         = app(ReportService::class);
        [$from, $to] = $svc->dateRange($this->dateRange, $this->dateFrom ?: null, $this->dateTo ?: null);
        $result      = $svc->leadsOverTime($tenantId, $from, $to, $this->groupBy, $this->source ?: null);

        return array_map(fn($label, $count) => compact('label', 'count'),
            $result['labels'],
            $result['data']
        );
    }

    public function getTotalLeads(): int
    {
        return (int) array_sum($this->getChartData()['data']);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportCsv')
                ->label(__('filament/reports.export_csv'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->url(fn() => route('reports.export.csv', [
                    'report'    => 'lead-volume',
                    'dateRange' => $this->dateRange,
                    'groupBy'   => $this->groupBy,
                    'source'    => $this->source,
                    'dateFrom'  => $this->dateFrom,
                    'dateTo'    => $this->dateTo,
                ])),

            Action::make('exportPdf')
                ->label(__('filament/reports.export_pdf'))
                ->icon('heroicon-o-document')
                ->color('danger')
                ->url(fn() => route('reports.export.pdf', [
                    'report'    => 'lead-volume',
                    'dateRange' => $this->dateRange,
                    'groupBy'   => $this->groupBy,
                    'source'    => $this->source,
                    'dateFrom'  => $this->dateFrom,
                    'dateTo'    => $this->dateTo,
                ])),
        ];
    }
}
