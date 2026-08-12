<?php

namespace App\Services;

use App\Models\Tenant;

/**
 * Single source of truth for subscription-state derivation.
 *
 * Before this class, three pieces of code all re-implemented the
 * same state machine:
 *   - Tenant::canAccessApp()
 *   - Tenant::getStatusLabel()
 *   - EnforceSubscription middleware (redirect decisions)
 *
 * Result: the "Unknown" / drift bugs the operator reported earlier
 * in this review cycle.  This service centralises the logic.
 * All three callers now route through it.
 *
 * Usage:
 *     $state = TenantSubscriptionState::of($tenant);
 *     $state->isActive();        // can this tenant access the app?
 *     $state->label();           // human-readable badge
 *     $state->isOnTrial();       // trial + date in future
 *     $state->isTrialExpired();  // trial + date in past
 *
 * ============================================================
 * State machine
 * ============================================================
 *
 * Inputs read from Tenant: { active, subscription_status,
 *                            trial_ends_at, subscription_ends_at }
 *
 *      ON_TRIAL ──── trial_ends_at < now ────▶ TRIAL_EXPIRED
 *         │                                          │
 *         │                                pay/upgrade│
 *         │                                          ▼
 *         │                                   ACTIVE_PAID
 *         │                                          │
 *         │                            sub_ends_at < now
 *         │                                          ▼
 *         │                                      EXPIRED
 *         │                                          │
 *         │                              user cancels│
 *         │                                          ▼
 *         │                                    CANCELLED
 *         │
 *         └─── (any state) SA toggles active=false ──▶ SUSPENDED
 *
 * Allowed-access summary (drives EnforceSubscription middleware):
 *
 *   ON_TRIAL         → full access
 *   ACTIVE_PAID      → full access
 *   PENDING_PAYMENT  → /admin/billing only (manual transfer awaiting SA confirm)
 *   TRIAL_EXPIRED    → /admin/billing only (must subscribe)
 *   EXPIRED          → /admin/billing only (must renew)
 *   CANCELLED        → /admin/billing only (sub ended at user's request)
 *   SUSPENDED        → /admin/subscription-required only (SA lockout)
 *   PENDING_DELETION → /admin/privacy + /admin/billing only (GDPR Art 17 cool-off,
 *                       owner must reach cancel-erasure UI; staff stay locked out)
 *   UNKNOWN          → /admin/subscription-required (data hole)
 *
 * Login itself is always allowed.  The middleware only restricts
 * post-login routing, so users can sign in to upgrade or contact
 * support regardless of state.
 *
 * Stored vs derived TRIAL_EXPIRED:
 *   - DERIVED: subscription_status='trial' AND trial_ends_at < now
 *   - STORED:  subscription_status='trial_expired' (lifecycle cron flips
 *              the column when it transitions a trial that lapsed).
 *   This class handles both representations identically.
 */
class TenantSubscriptionState
{
    public const SUSPENDED        = 'suspended';
    public const ON_TRIAL         = 'trial';
    public const TRIAL_EXPIRED    = 'trial_expired';
    public const ACTIVE_PAID      = 'active';
    public const PENDING_PAYMENT  = 'pending_payment';
    public const CANCELLED        = 'cancelled';
    public const EXPIRED          = 'expired';
    public const PENDING_DELETION = 'pending_deletion';
    public const UNKNOWN          = 'unknown';

    public function __construct(protected Tenant $tenant) {}

    public static function of(Tenant $tenant): self
    {
        return new self($tenant);
    }

    public function state(): string
    {
        $t = $this->tenant;

        if (! $t->active) {
            return self::SUSPENDED;
        }

        if ($t->subscription_status === \App\Enums\SubscriptionStatus::Trial) {
            if ($t->trial_ends_at && $t->trial_ends_at->isFuture()) {
                return self::ON_TRIAL;
            }
            if ($t->trial_ends_at && $t->trial_ends_at->isPast()) {
                return self::TRIAL_EXPIRED;
            }
            // trial with no trial_ends_at → data-integrity hole
            // (migration 2026_04_22 didn't stamp the column
            // when SA-panel Create was run pre-fix).  Return
            // TRIAL — callers gate on isActive(), so the user
            // gets access; the SA can fix the row via Edit.
            return self::ON_TRIAL;
        }

        // The lifecycle cron stores 'trial_expired' in the column
        // when it transitions a lapsed trial.  Without this branch
        // the state would fall through to UNKNOWN — and the badge
        // / middleware behaviour would diverge from a fresh "trial
        // with date in past" tenant.
        if ($t->subscription_status === \App\Enums\SubscriptionStatus::TrialExpired) {
            return self::TRIAL_EXPIRED;
        }

        if ($t->subscription_status === \App\Enums\SubscriptionStatus::Active) {
            if ($t->subscription_ends_at && $t->subscription_ends_at->isPast()) {
                return self::EXPIRED;
            }
            return self::ACTIVE_PAID;
        }

        if ($t->subscription_status === \App\Enums\SubscriptionStatus::Cancelled) {
            return self::CANCELLED;
        }

        if ($t->subscription_status === \App\Enums\SubscriptionStatus::Expired) {
            return self::EXPIRED;
        }

        // Manual gateway parks the tenant here after checkout while
        // the bank transfer is in flight.  Until a Super Admin confirms
        // the transfer the tenant must NOT have paid-feature access,
        // but the badge / middleware shouldn't drop them into the
        // generic "Unknown" data-hole bucket either.
        if ($t->subscription_status === \App\Enums\SubscriptionStatus::PendingPayment) {
            return self::PENDING_PAYMENT;
        }

        // GDPR Article 17 cool-off — workspace owner has scheduled
        // erasure but the 30-day window has not yet elapsed.  The
        // owner must remain able to reach the cancel-erasure UI;
        // EnforceNotSuspended / EnforceSubscription whitelist the
        // billing & privacy paths for this state.
        if ($t->subscription_status === \App\Enums\SubscriptionStatus::PendingDeletion) {
            return self::PENDING_DELETION;
        }

        return self::UNKNOWN;
    }

    public function isActive(): bool
    {
        return in_array($this->state(), [
            self::ON_TRIAL,
            self::ACTIVE_PAID,
        ], true);
    }

    public function isOnTrial(): bool
    {
        return $this->state() === self::ON_TRIAL;
    }

    public function isTrialExpired(): bool
    {
        return $this->state() === self::TRIAL_EXPIRED;
    }

    public function isSuspended(): bool
    {
        return $this->state() === self::SUSPENDED;
    }

    public function isPendingPayment(): bool
    {
        return $this->state() === self::PENDING_PAYMENT;
    }

    public function label(): string
    {
        // Translatable badges via lang/en/billing.php
        // (state_* keys).  EN defaults are byte-identical to the
        // pre-i18n hard-coded strings so SubscriptionStateTest +
        // TenantErasureFullFlowTest assertions on exact label text
        // keep passing.
        return (string) __('billing.state_' . $this->state());
    }
}
