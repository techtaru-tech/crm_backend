<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * User-initiated subscription cancellation.
 *
 * The buyer (or workspace owner) hits "Cancel" in the billing portal
 * and lands here.  This service:
 *
 *   1. Records the reason (survey data — drives retention analysis).
 *   2. Computes a graceful access-cutoff date (keep them paid-through
 *      until what they already paid for runs out).
 *   3. Flips the tenant to status='cancelled'.
 *   4. Writes an audit row with the reason + feedback verbatim.
 *   5. Fires the SubscriptionCancelled event so the mailable goes
 *      out and any future analytics listeners can hook in.
 *
 * Idempotent — re-cancelling an already-cancelled tenant is a no-op
 * that returns the existing access-cutoff.
 */
class TenantCancellationService
{
    /**
     * Standard reason taxonomy.  Keep this list aligned with the
     * radio options on the cancel page so reports group cleanly.
     *
     * The literal English values are the canonical fallback map (used
     * by legacy seeders, mock fixtures, and audit-row rendering when
     * the translator isn't booted yet).  UI surfaces MUST route through
     * self::reasons() so labels reflect the active locale.
     */
    public const REASONS = [
        'too_expensive'     => 'Too expensive',
        'missing_feature'   => 'Missing a feature I need',
        'found_alternative' => 'Found a better alternative',
        'not_using'         => 'Not using it enough',
        'going_out'         => 'Going out of business / role change',
        'temporary'         => 'Just need to pause for now',
        'other'             => 'Other',
    ];

    /**
     * Translator-resolved reason labels for UI rendering.
     *
     * Mirrors the App\Services\Migration\CrmCsvImporter::labels() pattern:
     * the const holds the canonical fallback map; this method routes each
     * label through __() at call time so the value reflects the active
     * locale instead of being frozen at file-load.
     *
     * @return array<string, string>
     */
    public static function reasons(): array
    {
        return [
            'too_expensive'     => __('services/tenant_cancellation.reason_too_expensive'),
            'missing_feature'   => __('services/tenant_cancellation.reason_missing_feature'),
            'found_alternative' => __('services/tenant_cancellation.reason_found_alternative'),
            'not_using'         => __('services/tenant_cancellation.reason_not_using'),
            'going_out'         => __('services/tenant_cancellation.reason_going_out'),
            'temporary'         => __('services/tenant_cancellation.reason_temporary'),
            'other'             => __('services/tenant_cancellation.reason_other'),
        ];
    }

    public function __construct(protected SubscriptionEventService $events)
    {
    }

    /**
     * Cancel the tenant's subscription.  Returns the resolved
     * access-cutoff date (when the user actually loses access).
     *
     * @param  string|null  $reason    One of self::REASONS keys
     * @param  string|null  $feedback  Free-text the user typed
     */
    public function cancel(
        Tenant $tenant,
        ?string $reason = null,
        ?string $feedback = null,
        ?User $actor = null,
    ): Carbon {
        $reasonKey   = $this->normalizeReason($reason);
        $feedbackTxt = $this->normalizeFeedback($feedback);

        // Idempotency — already cancelled, just return the cutoff.
        if ($tenant->subscription_status === \App\Enums\SubscriptionStatus::Cancelled) {
            return $tenant->subscription_ends_at ?? Carbon::now();
        }

        $cutoff = $this->resolveAccessCutoff($tenant);

        $tenant->forceFill([
            'subscription_status'  => 'cancelled',
            'subscription_ends_at' => $cutoff,
        ])->save();

        $this->writeAudit($tenant, $reasonKey, $feedbackTxt, $actor);

        try {
            $this->events->cancelled(
                $tenant,
                endsAt: $cutoff->toIso8601String(),
                reason: $reasonKey,
            );
        } catch (\Throwable $e) {
            Log::error('Failed to dispatch SubscriptionCancelled event', [
                'tenant_id' => $tenant->id,
                'error'     => $e->getMessage(),
            ]);
        }

        Log::info('Tenant subscription cancelled by user', [
            'tenant_id' => $tenant->id,
            'reason'    => $reasonKey,
            'actor_id'  => $actor?->id,
            'cutoff'    => $cutoff->toIso8601String(),
        ]);

        return $cutoff;
    }

    /**
     * Pick when the user actually loses access:
     *   - Active paid subscription with subscription_ends_at in the
     *     future → keep them paid-through to that date.
     *   - Active paid with no end date set → 30 days from now.
     *   - Trial → end of trial (no refund / extension).
     *   - Anything else → now.
     */
    protected function resolveAccessCutoff(Tenant $tenant): Carbon
    {
        if ($tenant->subscription_status === \App\Enums\SubscriptionStatus::Active
            && $tenant->subscription_ends_at
            && $tenant->subscription_ends_at->isFuture()
        ) {
            return $tenant->subscription_ends_at;
        }

        if ($tenant->subscription_status === \App\Enums\SubscriptionStatus::Active) {
            return Carbon::now()->addDays(30);
        }

        if ($tenant->subscription_status === \App\Enums\SubscriptionStatus::Trial && $tenant->trial_ends_at) {
            return $tenant->trial_ends_at;
        }

        return Carbon::now();
    }

    protected function normalizeReason(?string $reason): string
    {
        if ($reason === null || $reason === '') {
            return 'unspecified';
        }
        return array_key_exists($reason, self::REASONS) ? $reason : 'other';
    }

    protected function normalizeFeedback(?string $feedback): ?string
    {
        $feedback = $feedback !== null ? trim($feedback) : null;
        return $feedback ?: null;
    }

    protected function writeAudit(Tenant $tenant, string $reason, ?string $feedback, ?User $actor): void
    {
        try {
            AuditLog::record(
                action: 'subscription.cancelled',
                auditable: $tenant,
                oldValues: [],
                newValues: [
                    'tenant_name'  => $tenant->name,
                    'reason'       => $reason,
                    'reason_label' => self::reasons()[$reason] ?? __('services/tenant_cancellation.unspecified'),
                    'feedback'     => $feedback,
                    'by_user_id'   => $actor?->id,
                    'channel'      => 'self_service',
                ],
                tags: 'subscription,cancellation,retention',
            );
        } catch (\Throwable $e) {
            Log::error('Audit write failed for cancellation', [
                'tenant_id' => $tenant->id,
                'error'     => $e->getMessage(),
            ]);
        }
    }
}
