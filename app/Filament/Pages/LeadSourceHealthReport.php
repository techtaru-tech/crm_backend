<?php

namespace App\Filament\Pages;

use App\Filament\Resources\LeadSourceConnectionResource;
use App\Models\LeadSourceConnection;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class LeadSourceHealthReport extends Page
{
    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-signal';
    protected static string|\UnitEnum|null $navigationGroup = 'Integrations';
    protected static ?int    $navigationSort  = 20;
    protected string $view = 'filament.pages.lead-source-health-report';

    public static function getNavigationLabel(): string
    {
        return __('filament/lead_source_health.nav_label');
    }

    public function getTitle(): string
    {
        return __('filament/lead_source_health.title');
    }

    /**
     * Aggregate health stats in 2 queries regardless of connector count
     * (was 5 queries per connector — N+1 flagged in the strict review).
     */
    public function getRows(): array
    {
        $tenantId = auth()->user()?->tenant_id;
        if (! $tenantId) {
            return [];
        }

        $connections = LeadSourceConnection::where('tenant_id', $tenantId)
            ->orderBy('name')
            ->get();

        if ($connections->isEmpty()) {
            return [];
        }

        $now  = now();
        $ids  = $connections->pluck('id')->all();
        $d1   = $now->copy()->subDay();
        $d7   = $now->copy()->subDays(7);
        $d30  = $now->copy()->subDays(30);

        // One grouped aggregation over leads.
        $leadCounts = DB::table('leads')
            ->select('source_connection_id')
            ->selectRaw("SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) AS c24h", [$d1])
            ->selectRaw("SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) AS c7d",  [$d7])
            ->selectRaw("SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) AS c30d", [$d30])
            ->where('tenant_id', $tenantId)
            ->whereIn('source_connection_id', $ids)
            ->groupBy('source_connection_id')
            ->get()
            ->keyBy('source_connection_id');

        // One grouped aggregation over webhook_logs.
        $webhookStats = DB::table('webhook_logs')
            ->select('source_connection_id')
            ->selectRaw('COUNT(*) AS total')
            ->selectRaw("SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) AS ok")
            ->where('tenant_id', $tenantId)
            ->whereIn('source_connection_id', $ids)
            ->where('created_at', '>=', $d7)
            ->groupBy('source_connection_id')
            ->get()
            ->keyBy('source_connection_id');

        return $connections->map(function (LeadSourceConnection $c) use ($leadCounts, $webhookStats) {
            $leads = $leadCounts->get($c->id);
            $hooks = $webhookStats->get($c->id);

            $total = (int) ($hooks->total ?? 0);
            $ok    = (int) ($hooks->ok    ?? 0);

            return [
                'id'               => $c->id,
                'name'             => $c->name,
                'source'           => $c->source,
                'source_label'     => $c->source_label,
                'status'           => $c->status ?: 'unknown',
                'last_received_at' => $c->last_received_at,
                'leads_24h'        => (int) ($leads->c24h ?? 0),
                'leads_7d'         => (int) ($leads->c7d  ?? 0),
                'leads_30d'        => (int) ($leads->c30d ?? 0),
                'success_rate'     => $total > 0 ? round(($ok / $total) * 100, 1) : null,
                'last_error'       => $c->last_error,
            ];
        })->all();
    }

    public function reconnectUrl(int $id): string
    {
        return LeadSourceConnectionResource::getUrl('edit', ['record' => $id]);
    }
}
