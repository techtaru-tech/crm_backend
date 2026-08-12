<?php

namespace App\Http\Middleware;

use App\Services\TenantSubscriptionState;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Blocks access to the admin panel when the tenant's trial or
 * subscription has expired. Super admins (even when impersonating)
 * are allowed through.
 *
 * Allowed paths (so the user can still upgrade, log out, or reset password):
 *   - /admin/billing/*
 *   - /admin/logout
 *   - /admin/subscription-required
 *
 * pending_deletion (GDPR Article 17 cool-off) is treated as a special
 * limited-access state — instead of bouncing to subscription-required
 * (which would be wrong because the tenant DOES still have an active
 * subscription, just one scheduled for cancellation), the owner is
 * funnelled to /admin/privacy where the cancel-erasure button lives.
 * Staff members are filtered out earlier by EnforceNotSuspended.
 */
class EnforceSubscription
{
    protected array $allowedPaths = [
        'admin/billing',
        'admin/billing/*',
        'admin/logout',
        'admin/subscription-required',
        'admin/profile',
        'admin/login',
    ];

    /**
     * Paths reachable while the tenant is in pending_deletion.  The
     * cancel-erasure UI lives on /admin/privacy; billing is included
     * so the owner can also still inspect receipts before the
     * workspace is wiped.
     *
     * @var array<int, string>
     */
    protected array $pendingDeletionAllowedPaths = [
        'admin/privacy',
        'admin/privacy/*',
        'admin/billing',
        'admin/billing/*',
        'admin/logout',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user) {
            return $next($request);
        }

        // Super admins always pass (even when impersonating)
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Allow whitelisted paths so user can upgrade/log out
        if ($this->isAllowed($request)) {
            return $next($request);
        }

        $tenant = $user->tenant;

        if (! $tenant) {
            return $next($request); // EnforceTenantScope handles this case
        }

        // GDPR Article 17 cool-off — keep the owner moving through
        // the privacy / billing area so they can cancel.  Anywhere
        // else, redirect them to /admin/privacy (where the cancel
        // button lives) instead of /admin/subscription-required.
        if ($tenant->subscription_status === \App\Enums\SubscriptionStatus::PendingDeletion) {
            if ($this->isPendingDeletionAllowed($request)) {
                return $next($request);
            }
            return redirect('/admin/privacy')->with('reason', 'pending_deletion');
        }

        if (! $tenant->active) {
            return redirect('/admin/subscription-required')->with('reason', 'suspended');
        }

        if ($tenant->canAccessApp()) {
            return $next($request);
        }

        // Derive the redirect reason from the state class, not from
        // raw `subscription_status` strings.  Previously this match
        // bypassed the state machine, so a rename of any status value
        // there would have left this middleware silently falling
        // through to 'inactive'.  Routing every check through
        // TenantSubscriptionState constants keeps both surfaces in
        // lockstep.
        $state  = TenantSubscriptionState::of($tenant)->state();
        $reason = match ($state) {
            TenantSubscriptionState::TRIAL_EXPIRED   => 'trial_expired',
            TenantSubscriptionState::CANCELLED       => 'cancelled',
            TenantSubscriptionState::EXPIRED         => 'expired',
            TenantSubscriptionState::PENDING_PAYMENT => 'pending_payment',
            default                                  => 'inactive',
        };

        return redirect('/admin/subscription-required')->with('reason', $reason);
    }

    private function isAllowed(Request $request): bool
    {
        foreach ($this->allowedPaths as $path) {
            if ($request->is($path)) {
                return true;
            }
        }
        return false;
    }

    private function isPendingDeletionAllowed(Request $request): bool
    {
        foreach ($this->pendingDeletionAllowedPaths as $path) {
            if ($request->is($path)) {
                return true;
            }
        }
        return false;
    }
}
