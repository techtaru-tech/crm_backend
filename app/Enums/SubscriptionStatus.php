<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Canonical tenant subscription-status vocabulary (H8).
 *
 * The codebase mixed `'cancelled'` (LeadHub) with `'canceled'` (Stripe
 * webhook payloads) before this enum, and gateway drivers wrote bare
 * `$tenant->update(['subscription_status' => 'active'])` strings
 * scattered across 7+ files.  EnforceSubscription middleware could
 * route a freshly-paid tenant to /admin/subscription-required because
 * the gateway had written 'paid' but the middleware checked for
 * 'active'.
 *
 * The companion Tenant::transitionSubscriptionStatus() method is
 * the single audited writer; every gateway driver routes status
 * changes through it.
 */
enum SubscriptionStatus: string
{
    case Trial            = 'trial';
    case TrialExpired     = 'trial_expired';
    case Active           = 'active';
    case PendingPayment   = 'pending_payment';
    case Cancelled        = 'cancelled';
    case Expired          = 'expired';
    case PendingDeletion  = 'pending_deletion';

    public function label(): string
    {
        // Translatable via lang/en/enums/subscription_status.php.
        // EN default matches the original hard-coded values
        // byte-for-byte so existing tests (SubscriptionStateTest,
        // TenantErasureFullFlowTest) and SA dashboard badges
        // continue to see the same strings.
        return (string) __('enums/subscription_status.' . $this->value);
    }

    public function isActiveLike(): bool
    {
        return $this === self::Trial || $this === self::Active;
    }

    public function isTerminated(): bool
    {
        return $this === self::Cancelled
            || $this === self::Expired
            || $this === self::TrialExpired
            || $this === self::PendingDeletion;
    }
}
