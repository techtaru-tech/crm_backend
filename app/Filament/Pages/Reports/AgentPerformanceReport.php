<?php

namespace App\Filament\Pages\Reports;

use App\Filament\Concerns\PageRequiresPermission;

use App\Concerns\HasReportDateRange;
use App\Services\ReportService;
use Filament\Pages\Page;
use Filament\Actions\Action;

class AgentPerformanceReport extends Page
{
    use PageRequiresPermission;

    protected static string $requiredPermission = 'reports.view';

    use HasReportDateRange;

    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-user-circle';
    protected static string|\UnitEnum|null $navigationGroup = 'Reports';
    protected static ?int    $navigationSort  = 4;
    protected string $view = 'filament.pages.reports.agent-performance-report';

    public function mount(): void
    {
        $this->initDateRange();
    }

    public static function getNavigationLabel(): string
    {
        return __('filament/reports.agent_performance_nav');
    }

    public function getTitle(): string
    {
        return __('filament/reports.agent_performance_title');
    }

    public function getTableData(): array
    {
        $tenantId = auth()->user()?->tenant_id;
        if (! $tenantId) {
            return [];
        }
        $svc         = app(ReportService::class);
        [$from, $to] = $svc->dateRange($this->dateRange, $this->dateFrom ?: null, $this->dateTo ?: null);
        return $svc->agentPerformance($tenantId, $from, $to);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportCsv')
                ->label(__('filament/reports.export_csv'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->url(fn() => route('reports.export.csv', [
                    'report'    => 'agent-performance',
                    'dateRange' => $this->dateRange,
                    'dateFrom'  => $this->dateFrom,
                    'dateTo'    => $this->dateTo,
                ])),

            Action::make('exportPdf')
                ->label(__('filament/reports.export_pdf'))
                ->icon('heroicon-o-document')
                ->color('danger')
                ->url(fn() => route('reports.export.pdf', [
                    'report'    => 'agent-performance',
                    'dateRange' => $this->dateRange,
                    'dateFrom'  => $this->dateFrom,
                    'dateTo'    => $this->dateTo,
                ])),
        ];
    }
}
