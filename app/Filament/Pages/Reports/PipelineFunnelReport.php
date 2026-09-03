<?php

namespace App\Filament\Pages\Reports;

use App\Filament\Concerns\PageRequiresPermission;

use App\Models\Pipeline;
use App\Services\ReportService;
use Filament\Pages\Page;
use Filament\Actions\Action;

class PipelineFunnelReport extends Page
{
    use PageRequiresPermission;

    protected static string $requiredPermission = 'reports.view';

    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-funnel';
    protected static string|\UnitEnum|null $navigationGroup = 'Reports';
    protected static ?int    $navigationSort  = 3;
    protected string $view = 'filament.pages.reports.pipeline-funnel-report';

    public ?string $pipelineId = null;

    public static function getNavigationLabel(): string
    {
        return __('filament/reports.pipeline_funnel_nav');
    }

    public function getTitle(): string
    {
        return __('filament/reports.pipeline_funnel_title');
    }

    public function mount(): void
    {
        $tenantId = auth()->user()?->tenant_id;
        $pipeline = Pipeline::where('tenant_id', $tenantId)->orderBy('id')->first();
        $this->pipelineId = $pipeline?->id;
    }

    public function getPipelines(): array
    {
        $tenantId = auth()->user()?->tenant_id;
        return Pipeline::where('tenant_id', $tenantId)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    public function getFunnelData(): array
    {
        $tenantId = auth()->user()?->tenant_id;
        if (! $tenantId) {
            return [];
        }
        $svc = app(ReportService::class);
        return $svc->pipelineFunnel($tenantId, $this->pipelineId ? (int) $this->pipelineId : null);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportCsv')
                ->label(__('filament/reports.export_csv'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->url(fn() => route('reports.export.csv', ['report' => 'pipeline-funnel', 'pipeline_id' => $this->pipelineId])),

            Action::make('exportPdf')
                ->label(__('filament/reports.export_pdf'))
                ->icon('heroicon-o-document')
                ->color('danger')
                ->url(fn() => route('reports.export.pdf', ['report' => 'pipeline-funnel', 'pipeline_id' => $this->pipelineId])),
        ];
    }
}
