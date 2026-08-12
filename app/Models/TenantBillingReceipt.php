<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * SaaS-side billing receipt — one row per payment collected from a
 * tenant.  See migration for the full schema rationale.
 *
 * receipt_number is minted post-insert so it can include the
 * auto-incremented id zero-padded (LH-YYYY-NNNNNN).  This keeps
 * numbering monotonic per year without race conditions; gaps
 * appear only if Eloquent inserts are rolled back, which is
 * acceptable for most jurisdictions when paired with audit_logs.
 *
 * For strict EU VAT compliance (gap-free per fiscal year), the
 * operator can replace the post-insert minting with a SELECT FOR
 * UPDATE on a separate sequence table.  We don't ship that by
 * default — it adds lock contention on hot checkout paths.
 */
class TenantBillingReceipt extends Model
{
    protected $table = 'tenant_billing_receipts';

    protected $fillable = [
        'tenant_id',
        'gateway',
        'external_id',
        'plan_key',
        'amount',
        'currency',
        'receipt_number',
        'issued_at',
        'metadata',
    ];

    protected $casts = [
        'amount'    => 'decimal:2',
        'issued_at' => 'datetime',
        'metadata'  => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Idempotent factory called from gateway success hooks.  Returns
     * the existing row if (tenant, gateway, external_id) already
     * matches — webhook retries don't issue duplicate receipts.
     *
     * Best-effort: failures are swallowed and logged so a receipt-
     * write hiccup never blocks a successful subscription activation.
     *
     * Amount sign rules:
     *   - Standard receipts MUST have amount > 0.  Zero / negative
     *     amounts on a normal receipt indicate a caller bug and we
     *     short-circuit rather than persist a misleading row.
     *   - Credit-note receipts (metadata.type === 'credit_note') MUST
     *     have a strictly negative amount — that's how a refund
     *     document reverses the original positive receipt for VAT
     *     reporting.  Callers stamp the link to the original via
     *     metadata (refund_of_charge, refund_of_receipt_number).
     */
    public static function createForCheckout(
        Tenant $tenant,
        string $gateway,
        ?string $externalId,
        string $planKey,
        float $amount,
        string $currency,
        array $metadata = [],
    ): ?self {
        try {
            $isCreditNote = ($metadata['type'] ?? null) === 'credit_note';

            // Standard receipts must be positive; credit notes must
            // be strictly negative.  A zero-amount credit note has
            // no accounting effect and is almost certainly a bug.
            if ($isCreditNote) {
                if ($amount >= 0) return null;
            } else {
                if ($amount <= 0) return null;
            }

            if ($externalId) {
                $existing = static::query()
                    ->where('tenant_id', $tenant->id)
                    ->where('gateway', $gateway)
                    ->where('external_id', $externalId)
                    ->first();
                if ($existing) return $existing;
            }

            $now = now();

            try {
                $receipt = static::create([
                    'tenant_id'      => $tenant->id,
                    'gateway'        => $gateway,
                    'external_id'    => $externalId,
                    'plan_key'       => $planKey,
                    'amount'         => $amount,
                    'currency'       => strtoupper($currency),
                    'receipt_number' => 'LH-' . $now->format('Y') . '-PENDING-' . uniqid(),
                    'issued_at'      => $now,
                    'metadata'       => $metadata,
                ]);
            } catch (\Illuminate\Database\QueryException $e) {
                // SQLSTATE 23000 = integrity constraint violation.
                // Two concurrent webhook workers raced past the
                // existence check above and both attempted to insert
                // the same (tenant, gateway, external_id).  The DB
                // unique index (tbr_dedup_unique) caught the second
                // -- fall back to the row the first one wrote so the
                // caller still gets a deterministic receipt instance.
                if ($externalId && $e->getCode() === '23000') {
                    $existing = static::query()
                        ->where('tenant_id', $tenant->id)
                        ->where('gateway', $gateway)
                        ->where('external_id', $externalId)
                        ->first();
                    if ($existing) return $existing;
                }
                throw $e;
            }

            // Mint the final number once we know the id.  Monotonic
            // id ordering keeps numbers unique without a separate
            // sequence row.  The PENDING placeholder above is unique
            // (uniqid()) so the unique constraint on receipt_number
            // doesn't collide during the insert+update window.
            $receipt->receipt_number = 'LH-' . $now->format('Y') . '-' . str_pad((string) $receipt->id, 6, '0', STR_PAD_LEFT);
            $receipt->save();

            return $receipt;
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('TenantBillingReceipt::createForCheckout failed', [
                'tenant_id' => $tenant->id,
                'gateway'   => $gateway,
                'error'     => $e->getMessage(),
            ]);
            return null;
        }
    }
}
