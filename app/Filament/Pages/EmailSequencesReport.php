<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\PageRequiresPermission;

use App\Models\EmailSequence;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class EmailSequencesReport extends Page
{
    use PageRequiresPermission;

    protected static string $requiredPermission = 'reports.view';

    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-envelope';
    protected static string|\UnitEnum|null   $navigationGroup = 'Reports';
    protected static ?int    $navigationSort  = 8;

    protected string $view = 'filament.pages.reports.email-sequences-report';

    public static function getNavigationLabel(): string
    {
        return __('filament/email_sequences_report.nav_label');
    }

    public function getTitle(): string
    {
        return __('filament/email_sequences_report.title');
    }

    /**
     * Aggregate per-sequence stats in 2 queries, regardless of sequence count.
     *
     * 1. One grouped-by-status count over all enrollments for the tenant.
     * 2. One grouped-by-sequence count of outbound lead_emails whose lead
     *    is enrolled in any of those sequences (joined through enrollments).
     */
    public function getRows(): array
    {
        $tenantId = \App\Support\TenantContext::currentId();
        if (! $tenantId) {
            return [];
        }

        $sequences = EmailSequence::where('tenant_id', $tenantId)
            ->orderByDesc('created_at')
            ->get();

        if ($sequences->isEmpty()) {
            return [];
        }

        $sequenceIds = $sequences->pluck('id')->all();

        // Single grouped-count over all enrollments for these sequences.
        // Defense-in-depth: explicit tenant_id filter even though
        // $sequenceIds was tenant-scoped one query above — protects
        // against any future scope drop / data-corruption fix-up.
        $counts = DB::table('email_sequence_enrollments')
            ->selectRaw('sequence_id, status, COUNT(*) AS c')
            ->where('tenant_id', $tenantId)
            ->whereIn('sequence_id', $sequenceIds)
            ->groupBy('sequence_id', 'status')
            ->get()
            ->groupBy('sequence_id');

        // Single grouped-count of outbound emails per sequence — join via enrollments.
        // Same belt-and-braces tenant filter on both sides of the
        // join; lead_emails is BelongsToTenant so the column exists.
        $emailCounts = DB::table('lead_emails AS e')
            ->join('email_sequence_enrollments AS se', 'se.lead_id', '=', 'e.lead_id')
            ->where('e.tenant_id', $tenantId)
            ->where('se.tenant_id', $tenantId)
            ->whereIn('se.sequence_id', $sequenceIds)
            ->where('e.direction', 'outbound')
            ->where('e.tenant_id', $tenantId)
            ->selectRaw('se.sequence_id, COUNT(*) AS sent, SUM(CASE WHEN e.opened_at IS NOT NULL THEN 1 ELSE 0 END) AS opened')
            ->groupBy('se.sequence_id')
            ->get()
            ->keyBy('sequence_id');

        return $sequences->map(function (EmailSequence $sequence) use ($counts, $emailCounts) {
            $statusCounts = collect($counts[$sequence->id] ?? [])->pluck('c', 'status');

            $total     = (int) $statusCounts->sum();
            $enrolled  = (int) ($statusCounts['active']    ?? 0);
            $completed = (int) ($statusCounts['completed'] ?? 0);
            $replied   = (int) ($statusCounts['replied']   ?? 0);

            $emails        = $emailCounts[$sequence->id] ?? null;
            $outboundCount = (int) ($emails->sent   ?? 0);
            $openCount     = (int) ($emails->opened ?? 0);

            return [
                'id'             => $sequence->id,
                'name'           => $sequence->name,
                'status'         => $sequence->status,
                'total'          => $total,
                'enrolled'       => $enrolled,
                'completed'      => $completed,
                'replied'        => $replied,
                'reply_rate'     => $total         > 0 ? round(($replied / $total) * 100, 1)       : 0.0,
                'open_rate'      => $outboundCount > 0 ? round(($openCount / $outboundCount) * 100, 1) : 0.0,
                'outbound_count' => $outboundCount,
            ];
        })->all();
    }
}
