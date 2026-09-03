<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\PageRequiresPermission;

use App\Concerns\HasReportDateRange;
use App\Models\User;
use App\Services\ReportService;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class RevenueByRepReport extends Page
{
    use PageRequiresPermission;

    protected static string $requiredPermission = 'reports.view';

    use HasReportDateRange;

    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-currency-dollar';
    protected static string|\UnitEnum|null $navigationGroup = 'Reports';
    protected static ?int    $navigationSort  = 12;
    protected string $view = 'filament.pages.revenue-by-rep-report';

    public function mount(): void
    {
        $this->initDateRange();
    }

    public static function getNavigationLabel(): string
    {
        return __('filament/revenue_by_rep.nav_label');
    }

    public function getTitle(): string
    {
        return __('filament/revenue_by_rep.title');
    }

    /**
     * Three grouped queries total — was 5 per user (N+1 flagged in review).
     * Uses conditional SUM to pivot won/lost/pipeline counts + values in
     * a single aggregation across all assigned users for the tenant.
     */
    public function getRows(): array
    {
        $tenantId = auth()->user()?->tenant_id;
        if (! $tenantId) {
            return [];
        }

        [$from, $to] = app(ReportService::class)->dateRange(
            $this->dateRange,
            $this->dateFrom ?: null,
            $this->dateTo ?: null,
        );

        $users = User::where('tenant_id', $tenantId)->orderBy('name')->get();
        if ($users->isEmpty()) {
            return [];
        }

        $userIds = $users->pluck('id')->all();

        // Won/lost counts + values by user within the period.
        $closedStats = DB::table('leads')
            ->select('assigned_user_id')
            ->selectRaw("SUM(CASE WHEN won_at BETWEEN ? AND ? THEN 1 ELSE 0 END)            AS won_count", [$from, $to])
            ->selectRaw("SUM(CASE WHEN won_at BETWEEN ? AND ? THEN COALESCE(won_value,0) ELSE 0 END) AS won_value", [$from, $to])
            ->selectRaw("SUM(CASE WHEN lost_at BETWEEN ? AND ? THEN 1 ELSE 0 END)           AS lost_count", [$from, $to])
            ->where('tenant_id', $tenantId)
            ->whereIn('assigned_user_id', $userIds)
            ->groupBy('assigned_user_id')
            ->get()
            ->keyBy('assigned_user_id');

        // Open-pipeline value by user (not bound by date range).
        $pipelineValues = DB::table('leads')
            ->select('assigned_user_id')
            ->selectRaw('SUM(COALESCE(deal_value, 0)) AS pipeline_value')
            ->where('tenant_id', $tenantId)
            ->whereIn('assigned_user_id', $userIds)
            ->whereNull('won_at')
            ->whereNull('lost_at')
            ->groupBy('assigned_user_id')
            ->get()
            ->keyBy('assigned_user_id');

        $rows = $users->map(function (User $user) use ($closedStats, $pipelineValues) {
            $closed   = $closedStats->get($user->id);
            $pipeline = $pipelineValues->get($user->id);

            $wonCount  = (int)   ($closed->won_count  ?? 0);
            $wonValue  = (float) ($closed->won_value  ?? 0);
            $lostCount = (int)   ($closed->lost_count ?? 0);
            $totalClosed = $wonCount + $lostCount;

            return [
                'user_id'        => $user->id,
                'name'           => $user->name,
                'won_count'      => $wonCount,
                'won_value'      => $wonValue,
                'lost_count'     => $lostCount,
                'pipeline_value' => (float) ($pipeline->pipeline_value ?? 0),
                'win_rate'       => $totalClosed > 0 ? round(($wonCount / $totalClosed) * 100, 1) : 0.0,
            ];
        })->sortByDesc('won_value')->values()->all();

        return $rows;
    }

    public function getChartPayload(): array
    {
        $rows = $this->getRows();
        return [
            'labels' => array_column($rows, 'name'),
            'values' => array_map(fn ($r) => round($r['won_value'], 2), $rows),
        ];
    }
}
