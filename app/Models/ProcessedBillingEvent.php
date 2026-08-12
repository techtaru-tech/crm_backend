<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Inbound billing-webhook idempotency record.  See migration for full
 * rationale.
 *
 * Two static helpers cover every gateway's needs:
 *   - wasProcessed($gateway, $eventId): cheap exists-lookup at the top
 *     of handleWebhook().
 *   - markProcessed($gateway, $eventId, $type): firstOrCreate so two
 *     concurrent retries from the same gateway don't both insert.
 */
class ProcessedBillingEvent extends Model
{
    protected $table = 'processed_billing_events';

    protected $fillable = [
        'gateway',
        'event_id',
        'event_type',
        'processed_at',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    public static function wasProcessed(string $gateway, string $eventId): bool
    {
        if ($eventId === '') return false;

        return static::query()
            ->where('gateway', $gateway)
            ->where('event_id', $eventId)
            ->exists();
    }

    /**
     * Idempotent insert.  Race-safe — if two concurrent retries both
     * call markProcessed() simultaneously, the unique compound
     * constraint on (gateway, event_id) makes the second insert a
     * no-op (firstOrCreate handles it).
     */
    public static function markProcessed(string $gateway, string $eventId, ?string $eventType = null): void
    {
        if ($eventId === '') return;

        try {
            static::firstOrCreate(
                ['gateway' => $gateway, 'event_id' => $eventId],
                ['event_type' => $eventType, 'processed_at' => now()],
            );
        } catch (\Throwable) {
            // Best-effort — losing this row at most causes one duplicate
            // re-process on the next retry, which is exactly the
            // condition the table was designed to prevent but which
            // can't be made worse by failing here.
        }
    }
}
