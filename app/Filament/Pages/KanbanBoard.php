<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\PageRequiresPermission;

use App\Models\Lead;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class KanbanBoard extends Page
{
    use PageRequiresPermission;

    protected static string $requiredPermission = 'pipeline.view';

    public function getView(): string
    {
        return 'filament.pages.kanban-board';
    }

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-view-columns';
    protected static string|UnitEnum|null $navigationGroup = 'Pipeline';
    protected static ?int $navigationSort = 10;

    public static function getNavigationLabel(): string
    {
        return __('filament/kanban_board.nav_label');
    }

    /**
     * Maximum cards rendered per stage column.  Without this cap, a
     * tenant with 20 000 leads in one pipeline would load every row
     * into PHP memory on every loadBoard() call (and loadBoard runs
     * after every drag-to-move) — guaranteed OOM on shared hosting.
     * The blade surfaces "+N more" when the stored total exceeds
     * this, with a link to the regular leads list filtered by stage.
     */
    private const LEADS_PER_STAGE_CAP = 50;

    public int $selectedPipelineId = 0;
    public array $stages = [];
    public array $leadsByStage = [];
    public array $stageTotals = [];

    /**
     * Leads with no pipeline stage (pipeline_stage_id IS NULL) shown in
     * a dedicated "Unassigned" column.  Without this they matched no
     * stage filter and were INVISIBLE on the board — the customer
     * report "Kanban dont show my lead".  $unassignedTotal is the true
     * count (may exceed the rendered cap).
     */
    public array $unassignedLeads = [];
    public int $unassignedTotal = 0;

    public function mount(): void
    {
        $tenantId = \App\Support\TenantContext::currentId();
        $first    = Pipeline::where('tenant_id', $tenantId)->first();
        if ($first) {
            $this->selectedPipelineId = $first->id;
            $this->loadBoard();
        }
    }

    public function loadBoard(): void
    {
        $tenantId = \App\Support\TenantContext::currentId();

        $sourceLabels = config('leadhub.sources', []);

        $stageModels = PipelineStage::where('tenant_id', $tenantId)
            ->where('pipeline_id', $this->selectedPipelineId)
            ->orderBy('sort_order')
            ->get();

        $this->stages = $stageModels->map(fn($s) => [
            'id'      => $s->id,
            'name'    => $s->name,
            'color'   => $s->color ?? '#6366f1',
            'is_won'  => (bool) ($s->is_won ?? false),
            'is_lost' => (bool) ($s->is_lost ?? false),
        ])->toArray();

        // Aggregate counts + deal-value totals per stage in one query.
        // Drives the column-header counts and the "+N more leads"
        // affordance below the rendered card cap.
        $aggregates = Lead::query()
            ->where('tenant_id', $tenantId)
            ->where('pipeline_id', $this->selectedPipelineId)
            ->selectRaw('pipeline_stage_id, COUNT(*) as cnt, COALESCE(SUM(deal_value), 0) as total_val')
            ->groupBy('pipeline_stage_id')
            ->get()
            ->keyBy('pipeline_stage_id');

        $this->leadsByStage = [];
        $this->stageTotals  = [];
        foreach ($stageModels as $stage) {
            $agg = $aggregates->get($stage->id);
            $this->stageTotals[$stage->id] = [
                'count'       => (int) ($agg->cnt ?? 0),
                'total_value' => (float) ($agg->total_val ?? 0),
            ];

            // Per-stage page load — bounded.  N stages × 50 = bounded
            // memory regardless of total leads in the pipeline.
            $stageLeads = Lead::query()
                ->where('tenant_id', $tenantId)
                ->where('pipeline_id', $this->selectedPipelineId)
                ->where('pipeline_stage_id', $stage->id)
                ->with(['tags', 'assignedUser'])
                ->orderByDesc('stage_entered_at')
                ->orderByDesc('id')
                ->limit(self::LEADS_PER_STAGE_CAP)
                ->get();

            $this->leadsByStage[$stage->id] = $stageLeads
                ->map(fn (Lead $l) => $this->mapLeadCard($l, $sourceLabels))
                ->values()
                ->toArray();
        }

        // ── Unassigned column ────────────────────────────────────
        // Leads with no pipeline stage (pipeline_stage_id IS NULL) used
        // to be INVISIBLE — they matched no stage filter, so a tenant
        // who created leads without picking a pipeline + stage saw
        // "Kanban dont show my lead".  Surface them in a dedicated
        // Unassigned column the operator can drag into a real stage
        // (moveCard sets both pipeline_id + pipeline_stage_id from the
        // drop target).  Bounded by the same per-column cap.
        $unassignedQuery = Lead::query()
            ->where('tenant_id', $tenantId)
            ->whereNull('pipeline_stage_id');

        $this->unassignedTotal = (int) $unassignedQuery->count();

        $this->unassignedLeads = (clone $unassignedQuery)
            ->with(['tags', 'assignedUser'])
            ->orderByDesc('id')
            ->limit(self::LEADS_PER_STAGE_CAP)
            ->get()
            ->map(fn (Lead $l) => $this->mapLeadCard($l, $sourceLabels))
            ->values()
            ->toArray();
    }

    /**
     * Map a Lead model to the card-array shape the board blade renders.
     * Shared by both the per-stage columns and the Unassigned column.
     */
    private function mapLeadCard(Lead $l, array $sourceLabels): array
    {
        $daysInStage = 0;
        if ($l->stage_entered_at) {
            $daysInStage = (int) now()->diffInDays($l->stage_entered_at);
        }

        return [
            'id'            => $l->id,
            'name'          => trim("{$l->first_name} {$l->last_name}") ?: '—',
            'email'         => $l->email,
            'view_url'      => \App\Support\AdminUrl::for('leads/' . $l->id . '/view'),
            'is_starred'    => (bool) ($l->is_starred ?? false),
            'source_label'  => (function () use ($l, $sourceLabels): string {
                $key        = (string) ($l->source ?? 'manual');
                $langKey    = 'lead_sources.' . $key;
                $translated = __($langKey);

                // Translator-first: if the key resolves, use the
                // localized label; otherwise fall back to the
                // (English) config value and finally to ucfirst.
                if ($translated !== $langKey) {
                    return (string) $translated;
                }

                return (string) ($sourceLabels[$key] ?? ucfirst($key));
            })(),
            'score'         => (int) ($l->lead_score ?? 0),
            'tags'          => $l->tags instanceof \Illuminate\Support\Collection ? $l->tags->pluck('name')->toArray() : [],
            'days_in_stage' => $daysInStage,
            'assigned_to'   => $l->assignedUser?->name,
            'stage_id'      => $l->pipeline_stage_id,
            'deal_value'    => $l->deal_value !== null ? (float) $l->deal_value : null,
            'deal_currency' => $l->deal_currency ?: 'USD',
        ];
    }

    public function changePipeline(int $pipelineId): void
    {
        $this->selectedPipelineId = $pipelineId;
        $this->loadBoard();
    }

    public function moveCard(int $leadId, int $stageId): void
    {
        $tenantId = \App\Support\TenantContext::currentId();

        $lead = Lead::where('tenant_id', $tenantId)->find($leadId);
        if (! $lead) return;

        $stage = PipelineStage::where('tenant_id', $tenantId)->find($stageId);
        if (! $stage) return;

        $lead->update([
            'pipeline_stage_id' => $stage->id,
            'pipeline_id'       => $stage->pipeline_id,
        ]);

        $this->loadBoard();
    }

    protected function getViewData(): array
    {
        $tenantId  = \App\Support\TenantContext::currentId();
        $pipelines = Pipeline::where('tenant_id', $tenantId)->get();
        return [
            'pipelines' => $pipelines,
        ];
    }
}
