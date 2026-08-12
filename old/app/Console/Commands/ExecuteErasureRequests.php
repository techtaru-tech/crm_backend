<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Services\TenantErasureService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Throwable;

/**
 * GDPR Article 17 — daily erasure sweeper.
 *
 * Selects every tenant whose `deletion_requested_at` is older than
 * the cool-off window (30 days) and calls
 * TenantErasureService::executeErasure() on each — hard-deleting the
 * tenant and every tenant-owned row in a single DB transaction.
 *
 * Idempotent: a tenant whose deletion already ran no longer matches
 * the WHERE (the row is gone).  Re-running the command is safe.
 *
 * Use `--dry-run` to print what *would* be deleted without making
 * any changes — essential for verifying the cron before wiring it
 * to a hot prod pipeline, and required by the operator runbook.
 */
class ExecuteErasureRequests extends Command
{
    protected $signature = 'leadhub:execute-erasure {--dry-run : List affected tenants without performing the deletion}';

    protected $description = 'Hard-delete tenants whose GDPR Article 17 erasure cool-off has elapsed';

    public function handle(TenantErasureService $service): int
    {
        $cutoff = Carbon::now()->subDays(TenantErasureService::COOL_OFF_DAYS);

        $tenants = Tenant::query()
            ->withTrashed()  // Tenants table uses SoftDeletes — include those rows.
            ->whereNotNull('deletion_requested_at')
            ->where('deletion_requested_at', '<=', $cutoff)
            ->get();

        if ($tenants->isEmpty()) {
            $this->info(sprintf(
                'No tenants past the %d-day erasure cool-off (cutoff: %s).',
                TenantErasureService::COOL_OFF_DAYS,
                $cutoff->toIso8601String(),
            ));
            return self::SUCCESS;
        }

        $isDryRun = (bool) $this->option('dry-run');

        $this->line(sprintf(
            '%s%d tenant(s) past the cool-off window:',
            $isDryRun ? '[DRY-RUN] ' : '',
            $tenants->count(),
        ));

        $erased = 0;
        $failed = 0;

        foreach ($tenants as $tenant) {
            $this->line(sprintf(
                '  - tenant_id=%d  name="%s"  requested_at=%s',
                $tenant->id,
                $tenant->name,
                $tenant->deletion_requested_at?->toIso8601String() ?? '?',
            ));

            if ($isDryRun) {
                continue;
            }

            try {
                $service->executeErasure($tenant);
                $erased++;
            } catch (Throwable $e) {
                $failed++;
                $this->error(sprintf(
                    '    FAILED to erase tenant %d: %s',
                    $tenant->id,
                    $e->getMessage(),
                ));
                report($e);
            }
        }

        if ($isDryRun) {
            $this->info(sprintf('[DRY-RUN] Would erase %d tenant(s). No data was modified.', $tenants->count()));
            return self::SUCCESS;
        }

        $this->info(sprintf('Erased %d tenant(s); %d failure(s).', $erased, $failed));
        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
