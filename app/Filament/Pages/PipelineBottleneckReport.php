<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\PageRequiresPermission;

use App\Models\Lead;
use App\Models\PipelineStage;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class PipelineBottleneckReport extends Page
{
    use PageRequiresPermission;

    protected static string $requiredPermission = 'reports.view';

    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-funnel';
    protected static string|\UnitEnum|null $navigationGroup = 'Reports';
    protected static ?int    $navigationSort  = 10;
    protected string $view = 'filament.pages.pipeline-bottleneck-report';

    public static function getNavigationLabel(): string
    {
        return __('filament/pipeline_bottleneck.nav_label');
    }

    public function getTitle(): string
    {
        return __('filament/pipeline_bottleneck.title');
    }

    public function getRows(): array
    {
        $tenantId = auth()->user()?->tenant_id;
        if (! $tenantId) {
            return [];
        }

        $stages = PipelineStage::where('tenant_id', $tenantId)
            ->with('pipeline')
            ->orderBy('pipeline_id')
            ->orderBy('sort_order')
            ->get()
            ->reject(fn ($s) => $s->is_won || $s->is_lost)
            ->values();

        if ($stages->isEmpty()) {
            return [];
        }

        // H17: 2 queries instead of 3×N.  At 5 pipelines × 4 active
        // stages each = 20 stages, that's 60 queries → 2.  Composite
        // index leads(tenant_id, pipeline_stage_id) from M-P1 covers
        // both WHERE clauses.
        $stageIds = $stages->pluck('id')->all();

        // SQLite (test suite) uses julianday math; MySQL uses DATEDIFF.
        $driver   = DB::getDriverName();
        $diffExpr = $driver === 'sqlite'
            ? "CAST(julianday('now') - julianday(COALESCE(stage_entered_at, created_at)) AS REAL)"
            : "DATEDIFF(NOW(), COALESCE(stage_entered_at, created_at))";

        $aggregates = DB::table('leads')
            ->where('tenant_id', $tenantId)
            ->whereIn('pipeline_stage_id', $stageIds)
            ->whereNull('won_at')
            ->whereNull('lost_at')
            ->selectRaw("pipeline_stage_id, COUNT(*) AS cnt, AVG({$diffExpr}) AS avg_days")
            ->groupBy('pipeline_stage_id')
            ->get()
            ->keyBy('pipeline_stage_id');

        // Single overdue-count query joined to pipeline_stages so each
        // stage's own expected_duration_days threshold (with a 7-day
        // fallback) is used in the comparison without a per-stage round-
        // trip.
        $overdueByStage = DB::table('leads as l')
            ->join('pipeline_stages as s', 's.id', '=', 'l.pipeline_stage_id')
            ->where('l.tenant_id', $tenantId)
            ->whereIn('l.pipeline_stage_id', $stageIds)
            ->whereNull('l.won_at')
            ->whereNull('l.lost_at')
            ->whereRaw($driver === 'sqlite'
                ? "CAST(julianday('now') - julianday(COALESCE(l.stage_entered_at, l.created_at)) AS REAL) > COALESCE(s.expected_duration_days, 7)"
                : "DATEDIFF(NOW(), COALESCE(l.stage_entered_at, l.created_at)) > COALESCE(s.expected_duration_days, 7)")
            ->selectRaw('l.pipeline_stage_id AS pipeline_stage_id, COUNT(*) AS cnt')
            ->groupBy('l.pipeline_stage_id')
            ->pluck('cnt', 'pipeline_stage_id');

        $rows = [];
        foreach ($stages as $stage) {
            $agg       = $aggregates->get($stage->id);
            $leadCount = (int) ($agg->cnt ?? 0);
            $avgDays   = (float) ($agg->avg_days ?? 0);
            $overdue   = (int) ($overdueByStage[$stage->id] ?? 0);

            $expected = $stage->expected_duration_days;
            $status   = 'healthy';
            if ($expected && $avgDays > 0) {
                if ($avgDays >= 1.5 * $expected) {
                    $status = 'critical';
                } elseif ($avgDays >= $expected) {
                    $status = 'warning';
                }
            }

            $rows[] = [
                'pipeline'          => $stage->pipeline?->name ?? '—',
                'stage'             => $stage->name,
                'avg_days_in_stage' => round($avgDays, 1),
                'expected_days'     => $expected,
                'lead_count'        => $leadCount,
                'overdue_count'     => $overdue,
                'status'            => $status,
            ];
        }

        return $rows;
    }
}
