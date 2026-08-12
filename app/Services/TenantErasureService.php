<?php

declare(strict_types=1);

namespace App\Services;

use App\Mail\TenantErasureRequestedMail;
use App\Models\AuditLog;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use ReflectionClass;
use Throwable;

/**
 * GDPR Article 17 — Right to Erasure.
 *
 * State machine:
 *
 *   IDLE                    deletion_requested_at = null
 *     │ requestErasure()
 *     ▼
 *   PENDING_DELETION        deletion_requested_at = now
 *     │                     subscription_status   = 'pending_deletion'
 *     │
 *     ├─── cancelErasure() ─────▶ IDLE  (status restored)
 *     │
 *     └─── 30 days pass + cron ──▶ executeErasure()
 *                                      │  hard-delete every
 *                                      │  tenant-owned row +
 *                                      │  tenant.forceDelete()
 *                                      ▼
 *                                  GONE
 *
 * `executeErasure()` is destructive and irreversible — it bypasses
 * SoftDeletes via `forceDelete()`.  Wrapped in DB::transaction so a
 * partial failure (FK violation on a child table) rolls back and
 * leaves the tenant in a consistent state for retry.  The audit log
 * is written BEFORE the delete because we're about to nuke the
 * tenant_id (and the audit_logs row is cascaded too).
 *
 * Iterates every Eloquent model that uses the BelongsToTenant trait
 * via reflection so we can't forget a new model when one is added.
 * The list is computed once per process and cached statically.
 */
class TenantErasureService
{
    /** Default cool-off window before the cron actually nukes the tenant. */
    public const COOL_OFF_DAYS = 30;

    /** Subscription status used during the cool-off window. */
    public const STATUS_PENDING_DELETION = 'pending_deletion';

    /** @var array<int, class-string<Model>>|null */
    protected static ?array $tenantOwnedModels = null;

    /**
     * Step 1 — Owner clicks "Delete my workspace".
     *
     * Stamps `deletion_requested_at = now()`, flips the workspace
     * subscription_status to 'pending_deletion' so EnforceSubscription
     * blocks new sign-ins, sends a confirmation email, and audits.
     */
    public function requestErasure(Tenant $tenant, User $requester): void
    {
        $now = Carbon::now();
        $previousStatus = $tenant->subscription_status;

        $tenant->forceFill([
            'deletion_requested_at' => $now,
            'deletion_scheduled_at' => $now->copy()->addDays(self::COOL_OFF_DAYS),
            'subscription_status'   => self::STATUS_PENDING_DELETION,
            // Stash the prior status in settings so cancelErasure
            // can restore it cleanly without guessing.
            'settings'              => array_merge(
                $tenant->settings ?? [],
                ['_pre_deletion_status' => $previousStatus],
            ),
        ])->save();

        $this->sendConfirmationEmail($tenant, $requester);

        $this->audit('tenant.erasure_requested', $tenant, [
            'requester_id'          => $requester->id,
            'requester_email'       => $requester->email,
            'previous_status'       => $previousStatus,
            'cool_off_days'         => self::COOL_OFF_DAYS,
            'scheduled_deletion_at' => $now->copy()->addDays(self::COOL_OFF_DAYS)->toIso8601String(),
        ]);
    }

    /**
     * Step 2a — Owner changes their mind during the 30-day window.
     * Clears the timestamps and restores the prior subscription_status
     * so the workspace becomes accessible again on the next request.
     */
    public function cancelErasure(Tenant $tenant): void
    {
        $previous = data_get($tenant->settings, '_pre_deletion_status', 'active');
        $settings = $tenant->settings ?? [];
        unset($settings['_pre_deletion_status']);

        $tenant->forceFill([
            'deletion_requested_at' => null,
            'deletion_scheduled_at' => null,
            'subscription_status'   => $previous,
            'settings'              => $settings,
        ])->save();

        $this->audit('tenant.erasure_cancelled', $tenant, [
            'restored_status' => $previous,
        ]);
    }

    /**
     * Step 2b — 30 days have elapsed.  Hard-delete the tenant and
     * every record that references it.  Wrapped in DB::transaction
     * so a partial failure (FK constraint, etc.) rolls back cleanly.
     *
     * The audit log row goes in FIRST so we have a record even
     * after the tenant_id stops existing (the audit row itself
     * gets nuked too — but log files / external SIEM forwarders
     * have already received it via the Logger pipeline).
     */
    public function executeErasure(Tenant $tenant): void
    {
        $tenantId   = $tenant->id;
        $tenantName = $tenant->name;
        $tenantSlug = $tenant->slug;

        // Audit BEFORE the delete so the trail survives in the
        // logger pipeline / external forwarders even after the
        // audit_logs row itself is purged with the tenant.
        $this->audit('tenant.erased', $tenant, [
            'tenant_id'             => $tenantId,
            'tenant_name'           => $tenantName,
            'tenant_slug'           => $tenantSlug,
            'deletion_requested_at' => $tenant->deletion_requested_at?->toIso8601String(),
            'executed_at'           => Carbon::now()->toIso8601String(),
        ]);

        DB::transaction(function () use ($tenant, $tenantId) {
            foreach ($this->discoverTenantOwnedModels() as $modelClass) {
                try {
                    $modelClass::query()
                        ->withoutGlobalScopes()
                        ->where('tenant_id', $tenantId)
                        ->forceDelete();
                } catch (Throwable $e) {
                    Log::warning('Tenant erasure: failed to delete model rows', [
                        'tenant_id' => $tenantId,
                        'model'     => $modelClass,
                        'error'     => $e->getMessage(),
                    ]);
                    throw $e;
                }
            }

            // Detach pivot relations the trait does not cover —
            // tenant_users links users to tenants and is hand-rolled.
            DB::table('tenant_users')->where('tenant_id', $tenantId)->delete();

            // GDPR Article 17.3.b — tax law (EU 7-10y, US ~7y) requires
            // the operator to retain receipts.  We scrub PII inline
            // (billing email / business name / billing address / VAT id)
            // from the metadata JSON BEFORE the FK nulls the tenant_id
            // in the next step.  What remains is an anonymous tax
            // record with no link back to the deleted person/business --
            // gateway, amount, currency, receipt_number, plan_key, and
            // issued_at survive for audit.
            $this->anonymizeTenantReceipts($tenantId);

            // Finally: hard-delete the tenant itself, bypassing
            // the SoftDeletes trait it uses.  The FK on
            // tenant_billing_receipts.tenant_id is nullOnDelete so
            // each receipt's tenant_id flips to null at this step
            // -- the row is preserved.
            $tenant->forceDelete();
        });

        Log::info('Tenant erased', [
            'tenant_id'   => $tenantId,
            'tenant_name' => $tenantName,
        ]);
    }

    /**
     * Strip personally-identifiable fields (billing_email, business_name,
     * billing_address, vat_number) from each receipt's metadata JSON
     * before the parent tenant gets hard-deleted.
     *
     * Driver-specific paths:
     *   - MySQL/MariaDB: native JSON_REMOVE() in a single UPDATE.
     *   - PostgreSQL:    `metadata - 'key1' - 'key2' ...` operator.
     *   - SQLite (test): row-by-row PHP-side scrub via Eloquent.
     *
     * Best-effort: any failure here is logged but does not abort the
     * erasure transaction.  The downstream FK is nullOnDelete so the
     * receipt's link to the tenant is severed regardless; a stuck
     * PII field in metadata is far less important than completing
     * the legal-deadline erasure.
     */
    protected function anonymizeTenantReceipts(int $tenantId): void
    {
        try {
            $driver = DB::connection()->getDriverName();

            if ($driver === 'mysql' || $driver === 'mariadb') {
                \App\Models\TenantBillingReceipt::query()
                    ->where('tenant_id', $tenantId)
                    ->update([
                        // tenant_id stays so we can still query
                        // operator-side reports; null'd at the DB
                        // layer when tenant is deleted via the new
                        // nullOnDelete FK.  PII fields scrubbed inline.
                        'metadata' => DB::raw("JSON_REMOVE(metadata, '$.billing_email', '$.business_name', '$.billing_address', '$.vat_number')"),
                    ]);
                return;
            }

            if ($driver === 'pgsql') {
                \App\Models\TenantBillingReceipt::query()
                    ->where('tenant_id', $tenantId)
                    ->update([
                        'metadata' => DB::raw("(metadata - 'billing_email' - 'business_name' - 'billing_address' - 'vat_number')"),
                    ]);
                return;
            }

            // SQLite + everything else: row-by-row scrub.  The cast on
            // TenantBillingReceipt::$metadata is `array`, so unsetting
            // the keys + save() round-trips through json_encode.
            \App\Models\TenantBillingReceipt::query()
                ->where('tenant_id', $tenantId)
                ->each(function (\App\Models\TenantBillingReceipt $receipt) {
                    $meta = $receipt->metadata ?? [];
                    foreach (['billing_email', 'business_name', 'billing_address', 'vat_number'] as $piiKey) {
                        unset($meta[$piiKey]);
                    }
                    $receipt->metadata = $meta;
                    $receipt->save();
                });
        } catch (Throwable $e) {
            Log::warning('Tenant erasure: receipt PII scrub failed', [
                'tenant_id' => $tenantId,
                'error'     => $e->getMessage(),
            ]);
            // Re-throw so the surrounding DB::transaction rolls back
            // on real failures -- a half-scrubbed tenant in the DB is
            // worse than a retry-able transaction abort.
            throw $e;
        }
    }

    /**
     * Scan App\Models for every class using the BelongsToTenant
     * trait.  Cached statically so the reflection cost is paid
     * once per process.
     *
     * @return array<int, class-string<Model>>
     */
    public function discoverTenantOwnedModels(): array
    {
        if (self::$tenantOwnedModels !== null) {
            return self::$tenantOwnedModels;
        }

        $models = [];
        $modelsDir = app_path('Models');
        if (! is_dir($modelsDir)) {
            return self::$tenantOwnedModels = [];
        }

        foreach (glob($modelsDir . '/*.php') ?: [] as $file) {
            $basename = basename($file, '.php');
            $class = 'App\\Models\\' . $basename;

            if (! class_exists($class)) {
                continue;
            }

            try {
                $reflection = new ReflectionClass($class);
                if (! $reflection->isInstantiable()) {
                    continue;
                }
                if (! is_subclass_of($class, Model::class)) {
                    continue;
                }
                if ($this->usesBelongsToTenant($reflection)) {
                    $models[] = $class;
                }
            } catch (Throwable) {
                // Skip classes that fail to reflect — tooling /
                // generated stubs can land here in dev.
            }
        }

        return self::$tenantOwnedModels = $models;
    }

    /**
     * Recursively check the trait stack — `use BelongsToTenant`
     * may sit on a parent class or be re-exposed via a composite
     * trait.
     */
    protected function usesBelongsToTenant(ReflectionClass $class): bool
    {
        $check = $class;
        while ($check) {
            $traits = class_uses($check->getName()) ?: [];
            if (in_array(\App\Models\Concerns\BelongsToTenant::class, $traits, true)) {
                return true;
            }
            foreach ($traits as $trait) {
                if (in_array(\App\Models\Concerns\BelongsToTenant::class, class_uses($trait) ?: [], true)) {
                    return true;
                }
            }
            $check = $check->getParentClass() ?: null;
        }
        return false;
    }

    protected function sendConfirmationEmail(Tenant $tenant, User $requester): void
    {
        try {
            Mail::to($requester->email)->send(
                new TenantErasureRequestedMail(
                    tenant: $tenant,
                    requester: $requester,
                    coolOffDays: self::COOL_OFF_DAYS,
                ),
            );
        } catch (Throwable $e) {
            // Never block the user-facing action on mail failure —
            // the deletion is already scheduled in the DB and the
            // UI shows the countdown banner.
            Log::warning('Tenant erasure confirmation email failed', [
                'tenant_id' => $tenant->id,
                'user_id'   => $requester->id,
                'error'     => $e->getMessage(),
            ]);
        }
    }

    /**
     * Bypass the global-scope-tagged AuditLog model so we can
     * write a row even when the tenant_id is about to disappear.
     */
    protected function audit(string $action, Tenant $tenant, array $newValues = []): void
    {
        try {
            AuditLog::query()->withoutGlobalScopes()->create([
                'tenant_id'      => $tenant->id,
                'user_id'        => auth()->id(),
                'user_name'      => auth()->user()?->name,
                'action'         => $action,
                'auditable_type' => Tenant::class,
                'auditable_id'   => $tenant->id,
                'old_values'     => [],
                'new_values'     => $newValues,
                'ip_address'     => request()->ip(),
                'user_agent'     => request()->userAgent(),
                'url'            => request()->fullUrl(),
                'tags'           => 'gdpr,privacy,article-17',
            ]);
        } catch (Throwable $e) {
            // Audit failures must never block destructive actions —
            // the action itself is the contract.  The Logger
            // facade still gets the entry below.
            Log::warning('TenantErasureService audit write failed', [
                'tenant_id' => $tenant->id,
                'action'    => $action,
                'error'     => $e->getMessage(),
            ]);
        }

        // Always log to the structured logger too — gives the
        // operator a SIEM-friendly trail even when the audit_logs
        // row gets cascaded with the tenant.
        Log::info('GDPR erasure event', array_merge([
            'action'    => $action,
            'tenant_id' => $tenant->id,
        ], $newValues));
    }
}
