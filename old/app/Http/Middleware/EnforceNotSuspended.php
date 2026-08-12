<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Boot a suspended user's session the moment they touch the
 * panel.  Without this, a user suspended mid-session kept
 * working until their cookie expired — the login-time gate only
 * protected fresh sign-in attempts.
 *
 * Wired on both Filament panels via their authMiddleware stack.
 */
class EnforceNotSuspended
{
    /**
     * Paths the workspace OWNER may still reach while the tenant is
     * in pending_deletion.  Staff members are still bounced — only
     * the owner needs the cancel-erasure UI.  Without this whitelist
     * the cool-off banner the erasure flow advertises is unreachable
     * because the owner gets logged out the instant they sign in,
     * and the workspace deletes in 30 days regardless of intent.
     *
     * @var array<int, string>
     */
    protected array $ownerPendingDeletionAllowedPaths = [
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

        // Per-user suspension (admin clicked "Suspend" on Team & Seats).
        $isUserSuspended = method_exists($user, 'isSuspended') && $user->isSuspended();

        // Tenant-level "pending_deletion" — workspace owner started a
        // GDPR Article 17 erasure request.  During the 30-day cool-off
        // every member must be locked out so nobody (including the
        // owner) can keep adding personal data we're about to nuke.
        // Super-admins still pass so support can investigate.
        $tenantPendingDeletion = false;
        $tenant = $user->tenant ?? null;
        if (! ($user->is_super_admin ?? false)) {
            if ($tenant && $tenant->subscription_status === \App\Enums\SubscriptionStatus::PendingDeletion) {
                $tenantPendingDeletion = true;
            }
        }

        // OWNER ESCAPE HATCH: the workspace owner must be able to
        // reach the cancel-erasure UI during the cool-off window.
        // Only the owner — staff members keep the lockout so we
        // honour the "no new personal data" guarantee.
        if (
            $tenantPendingDeletion
            && $tenant
            && (int) ($tenant->owner_id ?? 0) === (int) $user->id
            && $this->isOwnerPendingDeletionAllowed($request)
        ) {
            return $next($request);
        }

        if ($isUserSuspended || $tenantPendingDeletion) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Preserve the suspension reason as a flash so the
            // login page can show a branded banner on the next
            // render without us leaking the user's identity.
            $request->session()->flash('suspended', true);
            if ($tenantPendingDeletion) {
                $request->session()->flash('pending_deletion', true);
            }

            // /admin/login and /super-admin/login are the two
            // login paths; send them to whichever their original
            // request was closer to.  Default to /admin/login.
            $loginPath = str_starts_with($request->path(), 'super-admin')
                ? '/super-admin/login'
                : '/admin/login';

            $message = $tenantPendingDeletion
                ? __('middleware.not_suspended.scheduled_for_deletion')
                : __('middleware.not_suspended.suspended');

            return redirect($loginPath)->withErrors([
                'email' => $message,
            ]);
        }

        return $next($request);
    }

    /**
     * Match the request path against the owner-only whitelist.  Uses
     * Request::is() so glob patterns ("admin/billing/*") behave the
     * same as Laravel's route helpers elsewhere in the stack.
     */
    protected function isOwnerPendingDeletionAllowed(Request $request): bool
    {
        foreach ($this->ownerPendingDeletionAllowedPaths as $path) {
            if ($request->is($path)) {
                return true;
            }
        }
        return false;
    }
}
