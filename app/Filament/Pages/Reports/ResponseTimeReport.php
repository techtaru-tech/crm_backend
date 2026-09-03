<?php

namespace App\Filament\Pages\Reports;

use App\Filament\Concerns\PageRequiresPermission;

use App\Concerns\HasReportDateRange;
use App\Models\User;
use App\Services\ReportService;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Livewire\Attributes\Url;

class ResponseTimeReport extends Page
{
    use PageRequiresPermission;

    protected static string $requiredPermission = 'reports.view';

    use HasReportDateRange;

    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-clock';
    protected static string|\UnitEnum|null $navigationGroup = 'Reports';
    protected static ?int    $navigationSort  = 7;
    protected string $view = 'filament.pages.reports.response-time-report';

    #[Url(as: 'source', keep: true)]
    public string $source = '';

    #[Url(as: 'agent', keep: true)]
    public ?string $userId = null;

    public function mount(): void
    {
        $this->initDateRange();
    }

    public static function getNavigationLabel(): string
    {
        return __('filament/reports.response_time_nav');
    }

    public function getTitle(): string
    {
        return __('filament/reports.response_time_title');
    }

    public function getAgents(): array
    {
        $tenantId = auth()->user()?->tenant_id;
        return User::where('tenant_id', $tenantId)->orderBy('name')->pluck('name', 'id')->all();
    }

    public function getReportData(): array
    {
        $tenantId = auth()->user()?->tenant_id;
        if (! $tenantId) {
            return ['buckets' => [], 'median' => 0, 'p90' => 0, 'total' => 0];
        }
        $svc         = app(ReportService::class);
        [$from, $to] = $svc->dateRange($this->dateRange, $this->dateFrom ?: null, $this->dateTo ?: null);
        return $svc->responseTimeDistribution(
            $tenantId, $from, $to,
            $this->source ?: null,
            $this->userId ? (int) $this->userId : null
        );
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportCsv')
                ->label(__('filament/reports.export_csv'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->url(fn() => route('reports.export.csv', [
                    'report'    => 'response-time',
                    'dateRange' => $this->dateRange,
                    'dateFrom'  => $this->dateFrom,
                    'dateTo'    => $this->dateTo,
                    'source'    => $this->source,
                    'userId'    => $this->userId,
                ])),

            Action::make('exportPdf')
                ->label(__('filament/reports.export_pdf'))
                ->icon('heroicon-o-document')
                ->color('danger')
                ->url(fn() => route('reports.export.pdf', [
                    'report'    => 'response-time',
                    'dateRange' => $this->dateRange,
                    'dateFrom'  => $this->dateFrom,
                    'dateTo'    => $this->dateTo,
                    'source'    => $this->source,
                    'userId'    => $this->userId,
                ])),
        ];
    }
}
