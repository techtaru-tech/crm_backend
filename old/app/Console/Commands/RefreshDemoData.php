<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Lead;
use App\Models\Tenant;
use App\Support\TenantCache;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Pure-date-shift refresher for the live demo at theleadhub.app.
 *
 * Computes how stale the demo data is - delta = now() minus the
 * latest created_at across the demo dataset - and shifts every
 * timestamp column on every demo-tenant-scoped row forward by that
 * delta. Zero deletes, zero inserts. Intervals between events stay
 * intact (a lead created 5 days before its first activity stays 5
 * days before its first activity) but the whole timeline lands
 * "now-relative", so charts always look populated.
 *
 * Self-anchored: the dump owns the anchor. Every restore resets the
 * data to the snapshot moment, every refresh shifts it forward to
 * "now". No external state, no settings table, no coordination with
 * the snapshot script.
 *
 * Strictly scoped to three demo tenant slugs. Production tenants
 * cannot be touched - the WHERE clause on every UPDATE filters by
 * tenant_id IN (demo IDs only) or lead_id IN (demo lead IDs only).
 *
 * Wraps the whole shift in a DB transaction so a per-table failure
 * rolls everything back rather than half-shifting the dataset.
 *
 *   php artisan leadhub:refresh-demo-data        # do it
 *   php artisan leadhub:refresh-demo-data --dry  # report only
 */
class RefreshDemoData extends Command
{
    protected $signature = 'leadhub:refresh-demo-data
                            {--dry : Report what would change without writing}';

    protected $description = 'Delta-shift demo timestamps so dashboards always look fresh';

    /**
     * Slugs of synthetic demo tenants seeded by DemoSeeder /
     * DemoV2Seeder. Anything outside this whitelist is treated as
     * production and is never modified.
     *
     * @var array<int, string>
     */
    private const DEMO_TENANT_SLUGS = [
        'acme-digital',
        'pinnacle-realty',
        'novatech',
    ];

    /**
     * Tables filtered by tenant_id IN (demo IDs).
     *
     * Each value is the list of timestamp columns to shift.
     * Schema::getColumnListing() filters the list at runtime so a
     * column missing from an older install (or one that gets renamed
     * later) does not 500 the SQL.
     *
     * @var array<string, array<int, string>>
     */
    private const TENANT_SCOPED = [
        'leads' => [
            'created_at', 'updated_at',
            'contacted_at', 'consented_at', 'enriched_at',
            'stage_entered_at', 'won_at', 'lost_at',
        ],
        'lead_imports'                => ['created_at', 'updated_at'],
        'automations'                 => ['created_at', 'updated_at'],
        'email_sequences'             => ['created_at', 'updated_at'],
        'email_sequence_steps'        => ['created_at', 'updated_at'],
        'email_sequence_enrollments'  => [
            'created_at', 'updated_at',
            'next_send_at', 'last_sent_at', 'completed_at',
        ],
        'forms'                       => ['created_at', 'updated_at'],
        'form_submissions'            => ['created_at', 'updated_at'],
        'meeting_types'               => ['created_at', 'updated_at'],
        'meeting_bookings'            => [
            'created_at', 'updated_at', 'scheduled_at', 'cancelled_at',
        ],
        'quotes' => [
            'created_at', 'updated_at',
            'valid_until', 'sent_at', 'accepted_at', 'rejected_at',
        ],
        'invoices' => [
            'created_at', 'updated_at',
            'issued_date', 'due_date',
            'sent_at', 'paid_at', 'cancelled_at',
        ],
        'webhook_logs'           => ['created_at', 'updated_at'],
        'outbound_webhooks'      => ['created_at', 'updated_at'],
        'audit_logs'             => ['created_at', 'updated_at'],
        'lead_capture_widgets'   => ['created_at', 'updated_at'],
        'landing_pages'          => ['created_at', 'updated_at'],
        'tenant_billing_receipts' => [
            'created_at', 'updated_at', 'issued_at',
        ],
        'tenants' => [
            'created_at',
            'trial_ends_at', 'subscription_started_at', 'subscription_ends_at',
        ],
        'users' => [
            'created_at', 'updated_at',
            'email_verified_at', 'last_login_at',
        ],
    ];

    /**
     * Tables filtered by lead_id IN (demo lead IDs). The lead
     * tenant_id is the authoritative scope; we just match the lead
     * FK rather than denormalizing the lookup.
     *
     * @var array<string, array<int, string>>
     */
    private const LEAD_SCOPED = [
        'lead_activities' => ['created_at', 'updated_at'],
        'lead_notes'      => ['created_at', 'updated_at'],
        'lead_tasks'      => [
            'created_at', 'updated_at', 'due_at', 'completed_at',
        ],
        'lead_emails' => [
            'created_at', 'updated_at',
            'sent_at', 'delivered_at',
            'opened_at', 'clicked_at',
            'bounced_at', 'replied_at',
        ],
        'lead_messages'    => ['created_at', 'updated_at'],
        'lead_calls'       => [
            'created_at', 'updated_at', 'started_at', 'ended_at',
        ],
        'lead_attachments' => ['created_at', 'updated_at'],
        'lead_duplicates'  => ['created_at', 'updated_at'],
        'automation_runs'  => [
            'created_at', 'updated_at', 'started_at', 'finished_at',
        ],
        'page_views'           => ['created_at', 'last_viewed_at'],
        'integration_sync_logs' => [
            'created_at', 'updated_at', 'started_at', 'finished_at',
        ],
    ];

    public function handle(): int
    {
        $isDry = (bool) $this->option('dry');

        // 1. Resolve demo tenant scope.
        $demoTenantIds = Tenant::query()
            ->withoutGlobalScope('tenant')
            ->whereIn('slug', self::DEMO_TENANT_SLUGS)
            ->pluck('id');

        if ($demoTenantIds->isEmpty()) {
            $this->warn(
                'No demo tenants found (slug in '
                . implode(', ', self::DEMO_TENANT_SLUGS)
                . '). Nothing to refresh.'
            );
            return self::SUCCESS;
        }

        $this->info(
            ($isDry ? '[DRY] ' : '')
            . 'Refreshing ' . $demoTenantIds->count() . ' demo tenant(s).'
        );

        // 2. Compute anchor + delta.
        $anchor = $this->computeAnchor($demoTenantIds);
        if (! $anchor) {
            $this->warn('No demo data found (no created_at to anchor against).');
            return self::SUCCESS;
        }

        $deltaSeconds = (int) $anchor->diffInSeconds(now(), false);

        // Bidirectional. If the anchor is in the past (positive delta)
        // we shift forward to "now". If it is in the future (negative
        // delta - happens when the legacy refresher created rows with
        // setTime(rand(8, 20)) that landed later in the day than the
        // current cron tick), we shift backward to collapse the
        // contamination. DATE_ADD treats a negative INTERVAL as
        // subtraction at the SQL level, so the same code path works
        // for both directions.
        if (abs($deltaSeconds) < 60) {
            $this->info(sprintf(
                'Already current (anchor %ds from now, %s). Nothing to shift.',
                $deltaSeconds,
                $anchor->toDateTimeString(),
            ));
            return self::SUCCESS;
        }

        if ($deltaSeconds < 0) {
            $this->warn(sprintf(
                'Anchor %s is %s in the future - shifting BACKWARD to align '
                . 'the demo timeline to "now".',
                $anchor->toDateTimeString(),
                gmdate('H:i:s', abs($deltaSeconds)),
            ));
        }

        $this->line(sprintf('  Anchor: %s', $anchor->toDateTimeString()));
        $this->line(sprintf(
            '  Delta:  %s seconds (%s%.1f days)',
            number_format($deltaSeconds),
            $deltaSeconds < 0 ? '-' : '',
            abs($deltaSeconds) / 86400,
        ));

        // 3. Resolve demo lead IDs (for through-lead tables).
        $demoLeadIds = Lead::query()
            ->withoutGlobalScope('tenant')
            ->whereIn('tenant_id', $demoTenantIds)
            ->pluck('id');

        // 4. Apply the shift in a single transaction so a per-table
        //    failure rolls the whole thing back.
        $totalRowsShifted = 0;
        DB::transaction(function () use (
            $demoTenantIds,
            $demoLeadIds,
            $deltaSeconds,
            $isDry,
            &$totalRowsShifted,
        ) {
            foreach (self::TENANT_SCOPED as $table => $cols) {
                $totalRowsShifted += $this->shiftTable(
                    $table,
                    $cols,
                    $deltaSeconds,
                    'tenant_id',
                    $demoTenantIds,
                    $isDry,
                );
            }

            if ($demoLeadIds->isNotEmpty()) {
                foreach (self::LEAD_SCOPED as $table => $cols) {
                    $totalRowsShifted += $this->shiftTable(
                        $table,
                        $cols,
                        $deltaSeconds,
                        'lead_id',
                        $demoLeadIds,
                        $isDry,
                    );
                }
            }
        });

        // 5. Cache invalidation - SA dashboard flat keys + per-tenant
        //    TenantCache flush (tagged on redis, no-op on file/db).
        if (! $isDry) {
            $this->bustCaches($demoTenantIds);
        }

        $this->newLine();
        $this->info($isDry ? '[DRY] Would shift:' : 'Shifted:');
        $this->line(sprintf(
            '  %s row(s) across %d table(s)',
            number_format($totalRowsShifted),
            count(self::TENANT_SCOPED) + count(self::LEAD_SCOPED),
        ));

        return self::SUCCESS;
    }

    /**
     * Find the most recent created_at across multiple high-frequency
     * demo-scoped tables. Highest wins. This is the moment we
     * delta-shift forward FROM.
     *
     * Restricting to created_at (not future-dated columns like
     * valid_until or expected_close_date) sidesteps the future
     * outlier becoming the anchor - created_at is by definition the
     * past.
     */
    private function computeAnchor(Collection $demoTenantIds): ?Carbon
    {
        $maxes = [];

        foreach (['lead_activities', 'leads', 'audit_logs', 'tenants'] as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }
            $tableCols = Schema::getColumnListing($table);
            if (! in_array('created_at', $tableCols, true)) {
                continue;
            }
            if (! in_array('tenant_id', $tableCols, true)) {
                continue;
            }

            $max = DB::table($table)
                ->whereIn('tenant_id', $demoTenantIds)
                ->max('created_at');

            if ($max) {
                $maxes[] = $max;
            }
        }

        if (empty($maxes)) {
            return null;
        }

        return Carbon::parse(max($maxes));
    }

    /**
     * Build and execute a single UPDATE that adds $deltaSeconds to
     * every requested timestamp column on rows matching the scope.
     * Returns the affected row count (or projected count in dry mode).
     */
    private function shiftTable(
        string $table,
        array $columns,
        int $deltaSeconds,
        string $scopeColumn,
        Collection $scopeIds,
        bool $isDry,
    ): int {
        if (! Schema::hasTable($table)) {
            return 0;
        }

        $tableCols = Schema::getColumnListing($table);
        if (! in_array($scopeColumn, $tableCols, true)) {
            return 0;
        }

        // Filter to columns that actually exist on this install.
        $validColumns = array_values(array_intersect($columns, $tableCols));
        if (empty($validColumns)) {
            return 0;
        }

        $query = DB::table($table)->whereIn($scopeColumn, $scopeIds);

        if ($isDry) {
            $count = $query->count();
            $this->line(sprintf(
                '  - %s: %d row(s) [%s]',
                $table,
                $count,
                implode(', ', $validColumns),
            ));
            return $count;
        }

        // DATE_ADD on NULL returns NULL, so nullable columns are safe.
        // $deltaSeconds is cast to int by handle() before this is
        // called, so no SQL-injection surface on the interpolation.
        $updates = [];
        foreach ($validColumns as $col) {
            $updates[$col] = DB::raw(
                "DATE_ADD(`{$col}`, INTERVAL {$deltaSeconds} SECOND)"
            );
        }

        $count = $query->update($updates);
        $this->line(sprintf('  - %s: %d row(s) shifted', $table, $count));
        return $count;
    }

    /**
     * Invalidate every dashboard cache that might be holding stale
     * pre-shift values. SA dashboard keys are flat strings;
     * per-tenant widget caches go through TenantCache and are
     * flushed by tag (or NO-OP on file/db cache).
     */
    private function bustCaches(Collection $demoTenantIds): void
    {
        $saKeys = [
            'sa-dashboard:overview:v1',
            'sa-dashboard:secondary-stats:v1',
            'sa-dashboard:mrr-trend:v1',
            'sa-dashboard:signups:v1',
            'sa-dashboard:status-mix:v1',
            'sa-dashboard:source-mix:v1',
            'leadhub.plans.cache.v1',
        ];
        foreach ($saKeys as $key) {
            Cache::forget($key);
        }

        foreach ($demoTenantIds as $tenantId) {
            TenantCache::flushTenant((int) $tenantId);
        }

        $this->line(sprintf(
            '  Caches busted: %d SA key(s) + %d tenant tag(s)',
            count($saKeys),
            $demoTenantIds->count(),
        ));
    }
}
