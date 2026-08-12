<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Lead;
use App\Models\Tenant;
use App\Models\TenantBenchmarkSnapshot;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Daily snapshotter that captures each tenant's KPIs into
 * tenant_benchmark_snapshots, feeding the network-effect Industry
 * Benchmarks moat.
 *
 * Each daily run writes one row per (tenant, metric) for the rolling
 * 30-day period ending today.  Tenants who opt in (settings.
 * benchmarks.share_metrics=true) get is_shared=true on their rows —
 * those rows are the only ones aggregated into the public median.
 *
 * Wire-up: register in routes/console.php as
 *
 *   Schedule::command('leadhub:capture-benchmarks')->dailyAt('03:30');
 *
 * Idempotent on the same day — checks for an existing snapshot row
 * (tenant_id + metric_key + period_end) before inserting.
 */
class CaptureBenchmarkSnapshots extends Command
{
    protected $signature   = 'leadhub:capture-benchmarks
                              {--dry-run : Print what would happen without writing rows}';
    protected $description = 'Capture daily benchmark KPI snapshots for all active tenants';

    protected bool $dryRun = false;

    public function handle(): int
    {
        $this->dryRun = (bool) $this->option('dry-run');

        if (! Schema::hasTable('tenant_benchmark_snapshots')) {
            $this->warn('tenant_benchmark_snapshots table missing — run migrations.');
            return self::FAILURE;
        }

        $now         = Carbon::now();
        $periodStart = $now->copy()->startOfDay()->subDays(30);
        $periodEnd   = $now->copy()->startOfDay();

        $written = 0;
        $skipped = 0;

        Tenant::query()
            ->where('active', true)
            ->whereNull('deleted_at')
            ->chunk(100, function ($tenants) use (&$written, &$skipped, $periodStart, $periodEnd) {
                foreach ($tenants as $tenant) {
                    try {
                        $written += $this->snapshotTenant($tenant, $periodStart, $periodEnd);
                    } catch (\Throwable $e) {
                        $skipped++;
                        Log::warning('CaptureBenchmarkSnapshots: tenant snapshot failed', [
                            'tenant_id' => $tenant->id,
                            'error'     => $e->getMessage(),
                        ]);
                    }
                }
            });

        $this->info("Done. {$written} rows written, {$skipped} tenants skipped.");
        return self::SUCCESS;
    }

    protected function snapshotTenant(Tenant $tenant, Carbon $periodStart, Carbon $periodEnd): int
    {
        $isShared = (bool) ($tenant->getSetting('benchmarks.share_metrics') ?? false);
        $vertical = $tenant->getSetting('industry_pack.installed') ?? null;

        $metrics = $this->computeMetrics($tenant, $periodStart, $periodEnd);

        $written = 0;
        foreach ($metrics as $key => $value) {
            $exists = TenantBenchmarkSnapshot::query()
                ->where('tenant_id', $tenant->id)
                ->where('metric_key', $key)
                ->whereDate('period_end', $periodEnd)
                ->exists();

            if ($exists) continue;

            $this->line("[{$tenant->id}] {$key}={$value} shared=" . ($isShared ? '1' : '0'));

            if ($this->dryRun) continue;

            TenantBenchmarkSnapshot::create([
                'tenant_id'    => $tenant->id,
                'metric_key'   => $key,
                'value'        => $value,
                'vertical'     => $vertical,
                'period_start' => $periodStart->toDateString(),
                'period_end'   => $periodEnd->toDateString(),
                'is_shared'    => $isShared,
                'captured_at'  => Carbon::now(),
            ]);
            $written++;
        }

        return $written;
    }

    /**
     * @return array<string, float>  metric_key => value
     */
    protected function computeMetrics(Tenant $tenant, Carbon $periodStart, Carbon $periodEnd): array
    {
        $leads = Lead::query()
            ->withoutGlobalScope('tenant')
            ->where('tenant_id', $tenant->id)
            ->whereBetween('created_at', [$periodStart, $periodEnd]);

        $totalLeads = (clone $leads)->count();
        $wonLeads   = (clone $leads)->where('status', 'won')->count();

        $winRate = $totalLeads > 0
            ? round(($wonLeads / $totalLeads) * 100, 2)
            : 0.0;

        $avgDealValue = 0.0;
        if (Schema::hasColumn('leads', 'value') && $wonLeads > 0) {
            $avgDealValue = (float) ((clone $leads)->where('status', 'won')->avg('value') ?: 0.0);
        }

        $teamSize = $tenant->users()->count();

        return [
            TenantBenchmarkSnapshot::METRIC_LEADS_COUNT             => (float) $totalLeads,
            TenantBenchmarkSnapshot::METRIC_LEADS_WON_COUNT         => (float) $wonLeads,
            TenantBenchmarkSnapshot::METRIC_WIN_RATE_PERCENT        => $winRate,
            TenantBenchmarkSnapshot::METRIC_AVG_DEAL_VALUE          => round($avgDealValue, 2),
            TenantBenchmarkSnapshot::METRIC_TEAM_SIZE               => (float) $teamSize,
            TenantBenchmarkSnapshot::METRIC_EMAILS_SENT             => $this->emailsSent($tenant, $periodStart, $periodEnd),
            TenantBenchmarkSnapshot::METRIC_AUTOMATION_RUNS         => $this->automationRuns($tenant, $periodStart, $periodEnd),
            TenantBenchmarkSnapshot::METRIC_RESPONSE_TIME_AVG_MIN   => $this->avgResponseMinutes($tenant, $periodStart, $periodEnd),
        ];
    }

    /**
     * Count of outbound emails recorded in lead_emails for the
     * period.  Falls back to 0 if the table doesn't exist on legacy
     * installs that haven't run the email-tracking migrations yet.
     */
    protected function emailsSent(Tenant $tenant, Carbon $periodStart, Carbon $periodEnd): float
    {
        if (! Schema::hasTable('lead_emails')) {
            return 0.0;
        }

        return (float) \DB::table('lead_emails')
            ->where('tenant_id', $tenant->id)
            ->where('direction', 'outbound')
            ->whereBetween('created_at', [$periodStart, $periodEnd])
            ->count();
    }

    /**
     * Count of automation_runs for this tenant in the period.
     * automation_runs has no tenant_id directly — joins via
     * automations.tenant_id.
     */
    protected function automationRuns(Tenant $tenant, Carbon $periodStart, Carbon $periodEnd): float
    {
        if (! Schema::hasTable('automation_runs') || ! Schema::hasTable('automations')) {
            return 0.0;
        }

        return (float) \DB::table('automation_runs')
            ->join('automations', 'automation_runs.automation_id', '=', 'automations.id')
            ->where('automations.tenant_id', $tenant->id)
            ->whereBetween('automation_runs.created_at', [$periodStart, $periodEnd])
            ->count();
    }

    /**
     * Median-minutes-to-first-outbound-touch for leads created in
     * the period.  We use the average across leads that DID get a
     * response — leads with zero outbound contact are excluded so
     * the metric stays "how fast does the tenant respond when they
     * do" rather than collapsing to infinity for unworked leads.
     */
    protected function avgResponseMinutes(Tenant $tenant, Carbon $periodStart, Carbon $periodEnd): float
    {
        if (! Schema::hasTable('lead_emails')) {
            return 0.0;
        }

        $rows = \DB::table('leads')
            ->select('leads.id as lead_id', 'leads.created_at as lead_created_at')
            ->selectSub(
                \DB::table('lead_emails')
                    ->select('created_at')
                    ->whereColumn('lead_emails.lead_id', 'leads.id')
                    ->where('direction', 'outbound')
                    ->orderBy('created_at', 'asc')
                    ->limit(1),
                'first_response_at'
            )
            ->where('leads.tenant_id', $tenant->id)
            ->whereBetween('leads.created_at', [$periodStart, $periodEnd])
            ->get();

        $diffs = [];
        foreach ($rows as $row) {
            if (! $row->first_response_at || ! $row->lead_created_at) continue;
            $minutes = Carbon::parse($row->first_response_at)
                ->diffInMinutes(Carbon::parse($row->lead_created_at));
            if ($minutes >= 0) $diffs[] = $minutes;
        }

        if (empty($diffs)) return 0.0;

        return (float) round(array_sum($diffs) / count($diffs), 2);
    }
}
