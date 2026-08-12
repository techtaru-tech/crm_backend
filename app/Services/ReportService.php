<?php

namespace App\Services;

use App\Models\Automation;
use App\Models\AutomationRun;
use App\Models\Form;
use App\Models\FormSubmission;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function dateRange(string $range, ?string $from = null, ?string $to = null): array
    {
        return match ($range) {
            '7'           => [now()->subDays(7)->startOfDay(), now()->endOfDay()],
            '30'          => [now()->subDays(30)->startOfDay(), now()->endOfDay()],
            '60'          => [now()->subDays(60)->startOfDay(), now()->endOfDay()],
            '90'          => [now()->subDays(90)->startOfDay(), now()->endOfDay()],
            'this_month'  => [now()->startOfMonth(), now()->endOfDay()],
            'last_month'  => [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()],
            'custom'      => [Carbon::parse($from)->startOfDay(), Carbon::parse($to)->endOfDay()],
            default       => [now()->subDays(30)->startOfDay(), now()->endOfDay()],
        };
    }

    public function previousPeriod(Carbon $from, Carbon $to): array
    {
        $length = $from->diffInSeconds($to);
        return [$from->copy()->subSeconds($length), $from->copy()];
    }

    public function dashboardStats(int $tenantId): array
    {
        $today         = [now()->startOfDay(), now()->endOfDay()];
        $yesterday     = [now()->subDay()->startOfDay(), now()->subDay()->endOfDay()];
        $thisWeek      = [now()->startOfWeek(), now()->endOfDay()];
        $lastWeek      = [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()];
        $month         = [now()->subDays(30)->startOfDay(), now()->endOfDay()];
        $prevMonth     = [now()->subDays(60)->startOfDay(), now()->subDays(30)->endOfDay()];

        $base = Lead::where('tenant_id', $tenantId);

        $total             = (clone $base)->count();
        $prevTotal30       = (clone $base)->whereBetween('created_at', $prevMonth)->count();

        $newToday          = (clone $base)->whereBetween('created_at', $today)->count();
        $newYesterday      = (clone $base)->whereBetween('created_at', $yesterday)->count();

        $newThisWeek       = (clone $base)->whereBetween('created_at', $thisWeek)->count();
        $newLastWeek       = (clone $base)->whereBetween('created_at', $lastWeek)->count();

        $newThisMonth      = (clone $base)->whereBetween('created_at', $month)->count();
        $prevMonth30       = (clone $base)->whereBetween('created_at', $prevMonth)->count();

        // H7: status filters use the typed App\Enums\LeadStatus enum
        // so a future case rename (e.g. dropping 'lost' for 'closed_lost')
        // doesn't silently miss rows here.
        $won      = (clone $base)->where('status', \App\Enums\LeadStatus::Won->value)->count();
        $convRate = $total > 0 ? round($won / $total * 100, 1) : 0;

        $prevWon       = (clone $base)->where('status', \App\Enums\LeadStatus::Won->value)->whereBetween('updated_at', $prevMonth)->count();
        $prevTotalLeads = (clone $base)->whereBetween('created_at', $prevMonth)->count();
        $prevConv       = $prevTotalLeads > 0 ? round($prevWon / $prevTotalLeads * 100, 1) : 0;

        $activePipelineLeads = (clone $base)
            ->whereNotIn('status', \App\Enums\LeadStatus::terminalValues())
            ->whereNotNull('pipeline_stage_id')
            ->count();

        $prevActivePipeline = (clone $base)
            ->whereNotIn('status', \App\Enums\LeadStatus::terminalValues())
            ->whereNotNull('pipeline_stage_id')
            ->where('created_at', '<', now()->subDays(30))
            ->count();

        $avgResponseRaw = DB::select("
            SELECT ROUND(AVG(resp_min), 0) AS avg_min
            FROM (
                SELECT leads.id,
                       TIMESTAMPDIFF(MINUTE, leads.created_at, MIN(la.created_at)) AS resp_min
                FROM leads
                INNER JOIN lead_activities la ON leads.id = la.lead_id
                WHERE leads.tenant_id = ? AND leads.deleted_at IS NULL
                GROUP BY leads.id, leads.created_at
                HAVING resp_min >= 0
            ) t
        ", [$tenantId]);
        $avgResponseMinutes = (int) ($avgResponseRaw[0]->avg_min ?? 0);

        $prevAvgRespRaw = DB::select("
            SELECT ROUND(AVG(resp_min), 0) AS avg_min
            FROM (
                SELECT leads.id,
                       TIMESTAMPDIFF(MINUTE, leads.created_at, MIN(la.created_at)) AS resp_min
                FROM leads
                INNER JOIN lead_activities la ON leads.id = la.lead_id
                WHERE leads.tenant_id = ?
                  AND leads.created_at BETWEEN ? AND ?
                  AND leads.deleted_at IS NULL
                GROUP BY leads.id, leads.created_at
                HAVING resp_min >= 0
            ) t
        ", [$tenantId, $prevMonth[0], $prevMonth[1]]);
        $prevAvgResponse = (int) ($prevAvgRespRaw[0]->avg_min ?? 0);

        return [
            'total_leads'             => $total,
            'prev_total_leads'        => $prevTotal30,

            'new_today'               => $newToday,
            'new_yesterday'           => $newYesterday,

            'new_this_week'           => $newThisWeek,
            'new_last_week'           => $newLastWeek,

            'new_this_month'          => $newThisMonth,
            'prev_month_leads'        => $prevMonth30,

            'conversion_rate'         => $convRate,
            'prev_conversion'         => $prevConv,

            'leads_won'               => $won,
            'active_pipeline_leads'   => $activePipelineLeads,
            'prev_active_pipeline'    => $prevActivePipeline,

            'avg_response_minutes'    => $avgResponseMinutes,
            'prev_avg_response'       => $prevAvgResponse,
        ];
    }

    public function leadsOverTime(int $tenantId, Carbon $from, Carbon $to, string $groupBy = 'day', ?string $source = null): array
    {
        $format = match ($groupBy) {
            'week'  => '%Y-%u',
            'month' => '%Y-%m',
            default => '%Y-%m-%d',
        };

        $query = Lead::where('tenant_id', $tenantId)
            ->whereBetween('created_at', [$from, $to]);

        if ($source) {
            $query->where('source', $source);
        }

        $rows = $query
            ->selectRaw("DATE_FORMAT(created_at, ?) as period, COUNT(*) as total", [$format])
            ->groupBy('period')
            ->orderBy('period')
            ->pluck('total', 'period');

        $periods = $this->fillPeriods($from, $to, $groupBy);
        $data    = [];
        foreach ($periods as $period) {
            $data[] = $rows->get($period, 0);
        }

        return ['labels' => $periods, 'data' => $data];
    }

    public function leadsBySource(int $tenantId, Carbon $from, Carbon $to): array
    {
        return Lead::where('tenant_id', $tenantId)
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('COALESCE(source, "unknown") as source, COUNT(*) as total')
            ->groupBy('source')
            ->orderByDesc('total')
            ->get()
            ->mapWithKeys(fn($r) => [$r->source => (int) $r->total])
            ->all();
    }

    public function pipelineDistribution(int $tenantId, ?int $pipelineId = null, ?Carbon $from = null, ?Carbon $to = null): array
    {
        $query = DB::table('leads')
            ->join('pipeline_stages', 'leads.pipeline_stage_id', '=', 'pipeline_stages.id')
            ->where('leads.tenant_id', $tenantId)
            ->whereNull('leads.deleted_at')
            ->selectRaw('pipeline_stages.name as stage, COUNT(leads.id) as total')
            ->groupBy('pipeline_stages.id', 'pipeline_stages.name', 'pipeline_stages.sort_order')
            ->orderBy('pipeline_stages.sort_order');

        if ($pipelineId) {
            $query->where('leads.pipeline_id', $pipelineId);
        }

        if ($from && $to) {
            $query->whereBetween('leads.created_at', [$from, $to]);
        }

        return $query->get()->mapWithKeys(fn($r) => [$r->stage => (int) $r->total])->all();
    }

    public function sourcePerformance(int $tenantId, Carbon $from, Carbon $to): array
    {
        [$prevFrom, $prevTo] = $this->previousPeriod($from, $to);

        $prevTotals = Lead::where('tenant_id', $tenantId)
            ->whereBetween('created_at', [$prevFrom, $prevTo])
            ->selectRaw('COALESCE(source, "unknown") as source, COUNT(*) as total')
            ->groupBy('source')
            ->pluck('total', 'source');

        return Lead::where('tenant_id', $tenantId)
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('
                COALESCE(source, "unknown") as source,
                COUNT(*) as total_leads,
                SUM(CASE WHEN status = "won" THEN 1 ELSE 0 END) as converted,
                ROUND(SUM(CASE WHEN status = "won" THEN 1 ELSE 0 END) * 100.0 / COUNT(*), 1) as conversion_rate,
                ROUND(AVG(lead_score), 1) as avg_score
            ')
            ->groupBy('source')
            ->orderByDesc('total_leads')
            ->get()
            ->map(function ($row) use ($prevTotals) {
                $prev  = (int) ($prevTotals->get($row->source, 0));
                $trend = $prev > 0
                    ? round(($row->total_leads - $prev) / $prev * 100, 1)
                    : null;
                return [
                    'source'          => $row->source,
                    'total_leads'     => (int) $row->total_leads,
                    'converted'       => (int) $row->converted,
                    'conversion_rate' => (float) $row->conversion_rate,
                    'avg_score'       => (float) $row->avg_score,
                    'prev_total'      => $prev,
                    'trend_pct'       => $trend,
                ];
            })
            ->toArray();
    }

    public function pipelineFunnel(int $tenantId, ?int $pipelineId = null): array
    {
        // Both branches now filter by tenant_id explicitly.  Pipeline
        // is BelongsToTenant so the global scope already filters in an
        // HTTP request, but this service is also called from the SA
        // dashboard where current_tenant may be unset (cross-tenant
        // aggregation context).  Without the explicit filter, the
        // `find($pipelineId)` branch would resolve a foreign tenant's
        // pipeline and leak its stage names to whoever called.
        $pipeline = $pipelineId
            ? Pipeline::where('tenant_id', $tenantId)->with('stages')->whereKey($pipelineId)->first()
            : Pipeline::where('tenant_id', $tenantId)->with('stages')->first();

        if (! $pipeline) {
            return [];
        }

        $stages = $pipeline->stages()->orderBy('sort_order')->get();
        if ($stages->isEmpty()) {
            return [];
        }

        // M-P2: single GROUP BY replaces the N×2 N+1 loop the previous
        // version ran.  At 8 stages that's 1 query instead of 16; at
        // 16 stages 1 instead of 32.  Composite index
        // leads(tenant_id, pipeline_stage_id) from M-P1 covers the
        // WHERE+GROUP BY.  Stages with zero leads still appear in the
        // funnel because we left-join the aggregated rows back onto
        // the stage list in PHP.
        $stageIds = $stages->pluck('id')->all();
        $aggregated = Lead::where('tenant_id', $tenantId)
            ->whereIn('pipeline_stage_id', $stageIds)
            ->whereNull('deleted_at')
            ->selectRaw(
                'pipeline_stage_id,
                 COUNT(*) AS cnt,
                 ROUND(AVG(CASE WHEN stage_entered_at IS NOT NULL THEN TIMESTAMPDIFF(HOUR, stage_entered_at, NOW()) / 24 END), 1) AS avg_days'
            )
            ->groupBy('pipeline_stage_id')
            ->get()
            ->keyBy('pipeline_stage_id');

        $results = [];
        $prev    = null;

        foreach ($stages as $stage) {
            $row     = $aggregated->get($stage->id);
            $count   = $row ? (int) $row->cnt : 0;
            $avgDays = $row ? (float) ($row->avg_days ?? 0) : 0.0;

            $dropOff = $prev !== null && $prev > 0
                ? round((1 - $count / $prev) * 100, 1)
                : null;

            $results[] = [
                'stage'    => $stage->name,
                'count'    => $count,
                'avg_days' => $avgDays,
                'drop_off' => $dropOff,
            ];
            $prev = $count;
        }

        return $results;
    }

    public function agentPerformance(int $tenantId, Carbon $from, Carbon $to): array
    {
        $agents = User::where('tenant_id', $tenantId)->get();
        if ($agents->isEmpty()) {
            return [];
        }

        $agentIds  = $agents->pluck('id')->all();
        $weeksAgo4 = now()->subWeeks(4)->startOfWeek();

        // Five aggregate queries replace the N×5 N+1 loop the previous
        // version ran.  At 20 agents that's 5 queries instead of 100;
        // at 50 agents 5 instead of 250.  Each query GROUPs BY the
        // agent id and the per-agent merge happens in PHP.

        // 1. Leads assigned in the window
        $assignedByAgent = DB::table('leads')
            ->where('tenant_id', $tenantId)
            ->whereIn('assigned_user_id', $agentIds)
            ->whereBetween('created_at', [$from, $to])
            ->whereNull('deleted_at')
            ->selectRaw('assigned_user_id, COUNT(*) as cnt')
            ->groupBy('assigned_user_id')
            ->pluck('cnt', 'assigned_user_id');

        // 2. Wins + avg close days (same row population, both stats
        //    drop out of one GROUP BY).
        $wonAgg = DB::table('leads')
            ->where('tenant_id', $tenantId)
            ->whereIn('assigned_user_id', $agentIds)
            ->where('status', \App\Enums\LeadStatus::Won->value)
            ->whereBetween('updated_at', [$from, $to])
            ->selectRaw('assigned_user_id, COUNT(*) as won, ROUND(AVG(TIMESTAMPDIFF(DAY, created_at, updated_at)), 1) as avg_days')
            ->groupBy('assigned_user_id')
            ->get()
            ->keyBy('assigned_user_id');

        // 3. Activity counts per agent
        $activitiesByAgent = DB::table('lead_activities')
            ->where('tenant_id', $tenantId)
            ->whereIn('user_id', $agentIds)
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('user_id, COUNT(*) as cnt')
            ->groupBy('user_id')
            ->pluck('cnt', 'user_id');

        // 4. Avg first-response minutes per agent — single nested-agg
        //    query.  Outer GROUP BY collapses to per-agent averages.
        $placeholders = implode(',', array_fill(0, count($agentIds), '?'));
        $avgRespRaw = DB::select(
            "SELECT t.assigned_user_id, ROUND(AVG(t.resp_min), 0) AS avg_min
             FROM (
                SELECT leads.assigned_user_id,
                       TIMESTAMPDIFF(MINUTE, leads.created_at, MIN(la.created_at)) AS resp_min
                FROM leads
                INNER JOIN lead_activities la ON leads.id = la.lead_id
                WHERE leads.tenant_id = ?
                  AND leads.assigned_user_id IN ({$placeholders})
                  AND leads.created_at BETWEEN ? AND ?
                  AND leads.deleted_at IS NULL
                GROUP BY leads.id, leads.created_at, leads.assigned_user_id
                HAVING resp_min >= 0
             ) t
             GROUP BY t.assigned_user_id",
            array_merge([$tenantId], $agentIds, [$from, $to]),
        );
        $avgRespByAgent = collect($avgRespRaw)->keyBy('assigned_user_id');

        // 5. 4-week weekly sparkline buckets — single GROUP BY across
        //    all agents, then bucketed in PHP by user_id + YEARWEEK.
        $sparklineRows = DB::table('leads')
            ->where('tenant_id', $tenantId)
            ->whereIn('assigned_user_id', $agentIds)
            ->where('created_at', '>=', $weeksAgo4)
            ->selectRaw('assigned_user_id, YEARWEEK(created_at, 1) as yw, COUNT(*) as cnt')
            ->groupBy('assigned_user_id', 'yw')
            ->get()
            ->groupBy('assigned_user_id');

        $rows = [];
        foreach ($agents as $agent) {
            $assigned   = (int) ($assignedByAgent[$agent->id] ?? 0);
            $won        = (int) ($wonAgg->get($agent->id)->won ?? 0);
            $avgClose   = (float) ($wonAgg->get($agent->id)->avg_days ?? 0);
            $activities = (int) ($activitiesByAgent[$agent->id] ?? 0);
            $avgResp    = (int) ($avgRespByAgent->get($agent->id)->avg_min ?? 0);
            $winRate    = $assigned > 0 ? round($won / $assigned * 100, 1) : 0;

            // Sparkline: project the per-agent weekly-grouped rows
            // into a 4-bucket array, oldest-to-newest.
            $weeklyMap = $sparklineRows->get($agent->id, collect())
                ->pluck('cnt', 'yw');

            $sparkline = [];
            for ($i = 3; $i >= 0; $i--) {
                $yw = (int) now()->subWeeks($i)->format('oW');
                $sparkline[] = (int) ($weeklyMap->get($yw, 0));
            }

            $rows[] = [
                'agent'            => $agent->name,
                'assigned'         => $assigned,
                'won'              => $won,
                'win_rate'         => $winRate,
                'activities'       => $activities,
                'avg_response_min' => $avgResp,
                'avg_close_days'   => $avgClose,
                'sparkline'        => $sparkline,
            ];
        }

        return collect($rows)->sortByDesc('assigned')->values()->all();
    }

    public function automationStats(int $tenantId, Carbon $from, Carbon $to): array
    {
        $automations = Automation::where('tenant_id', $tenantId)
            ->withCount([
                'runs as total_runs'   => fn($q) => $q->whereBetween('started_at', [$from, $to]),
                'runs as success_runs' => fn($q) => $q->whereBetween('started_at', [$from, $to])->where('status', 'success'),
            ])
            ->get();

        if ($automations->isEmpty()) {
            return [];
        }

        // M-P2: single GROUP BY for avg_run_seconds replaces the
        // 1-query-per-automation loop the previous version ran.  At 30
        // automations that's 1 query instead of 30; at 100 automations
        // 1 instead of 100.  Index lead_automations(automation_id,
        // started_at) covers the WHERE.
        $avgByAutomation = DB::table('automation_runs')
            ->whereIn('automation_id', $automations->pluck('id')->all())
            ->whereBetween('started_at', [$from, $to])
            ->whereNotNull('finished_at')
            ->selectRaw('automation_id, ROUND(AVG(TIMESTAMPDIFF(SECOND, started_at, finished_at)), 0) as avg_secs')
            ->groupBy('automation_id')
            ->pluck('avg_secs', 'automation_id');

        return $automations
            ->map(function ($a) use ($avgByAutomation) {
                $successRate = $a->total_runs > 0
                    ? round($a->success_runs / $a->total_runs * 100, 1)
                    : 0;

                return [
                    'name'            => $a->name,
                    'trigger_type'    => $a->trigger_type ?? '—',
                    'enabled'         => $a->enabled,
                    'total_runs'      => $a->total_runs,
                    'success_runs'    => $a->success_runs,
                    'success_rate'    => $successRate,
                    'avg_run_seconds' => (int) ($avgByAutomation->get($a->id) ?? 0),
                ];
            })
            ->sortByDesc('total_runs')
            ->values()
            ->all();
    }

    public function formAnalytics(int $tenantId, Carbon $from, Carbon $to): array
    {
        return Form::where('tenant_id', $tenantId)
            ->withCount([
                'submissions as total_submissions' => fn($q) => $q->whereBetween('created_at', [$from, $to]),
            ])
            ->get()
            ->map(fn($f) => [
                'name'        => $f->name,
                'active'      => $f->active,
                'submissions' => $f->total_submissions,
            ])
            ->sortByDesc('submissions')
            ->values()
            ->all();
    }

    public function formSubmissionTrend(int $tenantId, Carbon $from, Carbon $to): array
    {
        $rows = FormSubmission::where('tenant_id', $tenantId)
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m-%d') as period, COUNT(*) as total")
            ->groupBy('period')
            ->orderBy('period')
            ->pluck('total', 'period');

        $periods = $this->fillPeriods($from, $to, 'day');
        $data    = [];
        foreach ($periods as $period) {
            $data[] = $rows->get($period, 0);
        }

        return ['labels' => $periods, 'data' => $data];
    }

    public function responseTimeDistribution(int $tenantId, Carbon $from, Carbon $to, ?string $source = null, ?int $userId = null): array
    {
        $query = DB::table('leads')
            ->join('lead_activities', 'leads.id', '=', 'lead_activities.lead_id')
            ->where('leads.tenant_id', $tenantId)
            ->whereBetween('leads.created_at', [$from, $to])
            ->whereNull('leads.deleted_at')
            ->selectRaw('
                leads.id,
                TIMESTAMPDIFF(MINUTE, leads.created_at, MIN(lead_activities.created_at)) as response_minutes
            ')
            ->groupBy('leads.id', 'leads.created_at')
            ->having('response_minutes', '>=', 0);

        if ($source) {
            $query->where('leads.source', $source);
        }
        if ($userId) {
            $query->where('leads.assigned_user_id', $userId);
        }

        $rows    = $query->get()->pluck('response_minutes');

        // Histogram buckets keyed by canonical slug. The matching
        // localised label is built in $bucketLabels below so the chart
        // x-axis + breakdown table + CSV/PDF export render in tenant
        // locale instead of showing a raw English "< 5m" / "> 24h".
        $buckets = [
            'under_5m'  => 0,
            '5_15m'     => 0,
            '15_60m'    => 0,
            '1_4h'      => 0,
            '4_24h'     => 0,
            'over_24h'  => 0,
        ];

        foreach ($rows as $minutes) {
            if ($minutes < 5)        $buckets['under_5m']++;
            elseif ($minutes < 15)   $buckets['5_15m']++;
            elseif ($minutes < 60)   $buckets['15_60m']++;
            elseif ($minutes < 240)  $buckets['1_4h']++;
            elseif ($minutes < 1440) $buckets['4_24h']++;
            else                     $buckets['over_24h']++;
        }

        $bucketLabels = [
            'under_5m'  => (string) __('filament/reports.rt_bucket_under_5m'),
            '5_15m'     => (string) __('filament/reports.rt_bucket_5_15m'),
            '15_60m'    => (string) __('filament/reports.rt_bucket_15_60m'),
            '1_4h'      => (string) __('filament/reports.rt_bucket_1_4h'),
            '4_24h'     => (string) __('filament/reports.rt_bucket_4_24h'),
            'over_24h'  => (string) __('filament/reports.rt_bucket_over_24h'),
        ];

        $sorted = $rows->sort()->values();
        $count  = $sorted->count();

        $median = $count > 0
            ? ($count % 2 === 0
                ? ($sorted[$count / 2 - 1] + $sorted[$count / 2]) / 2
                : $sorted[(int) ($count / 2)])
            : 0;

        $p90 = $count > 0 ? $sorted[(int) ($count * 0.9)] : 0;

        return [
            'buckets'       => $buckets,
            'bucket_labels' => $bucketLabels,
            'median'        => round($median),
            'p90'           => round($p90),
            'total'         => $count,
        ];
    }

    private function fillPeriods(Carbon $from, Carbon $to, string $groupBy): array
    {
        $periods = [];
        $current = $from->copy();

        while ($current->lte($to)) {
            $periods[] = match ($groupBy) {
                'week'  => $current->format('Y-W'),
                'month' => $current->format('Y-m'),
                default => $current->format('Y-m-d'),
            };
            match ($groupBy) {
                'week'  => $current->addWeek(),
                'month' => $current->addMonth(),
                default => $current->addDay(),
            };
        }

        return $periods;
    }
}
