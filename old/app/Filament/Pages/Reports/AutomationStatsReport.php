<?php

namespace App\Filament\Pages\Reports;

use App\Concerns\HasReportDateRange;
use App\Services\ReportService;
use Filament\Pages\Page;
use Filament\Actions\Action;

class AutomationStatsReport extends Page
{
    use HasReportDateRange;

    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-bolt';
    protected static string|\UnitEnum|null $navigationGroup = 'Reports';
    protected static ?int    $navigationSort  = 5;
    protected string $view = 'filament.pages.reports.automation-stats-report';

    public function mount(): void
    {
        $this->initDateRange();
    }

    public static function getNavigationLabel(): string
    {
        return __('filament/reports.automation_stats_nav');
    }

    public function getTitle(): string
    {
        return __('filament/reports.automation_stats_title');
    }

    public function getTableData(): array
    {
        $tenantId = auth()->user()?->tenant_id;
        if (! $tenantId) {
            return [];
        }
        $svc         = app(ReportService::class);
        [$from, $to] = $svc->dateRange($this->dateRange, $this->dateFrom ?: null, $this->dateTo ?: null);
        return $svc->automationStats($tenantId, $from, $to);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportCsv')
                ->label(__('filament/reports.export_csv'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->url(fn() => route('reports.export.csv', [
                    'report'    => 'automation-stats',
                    'dateRange' => $this->dateRange,
                    'dateFrom'  => $this->dateFrom,
                    'dateTo'    => $this->dateTo,
                ])),

            Action::make('exportPdf')
                ->label(__('filament/reports.export_pdf'))
                ->icon('heroicon-o-document')
                ->color('danger')
                ->url(fn() => route('reports.export.pdf', [
                    'report'    => 'automation-stats',
                    'dateRange' => $this->dateRange,
                    'dateFrom'  => $this->dateFrom,
                    'dateTo'    => $this->dateTo,
                ])),
        ];
    }
}
