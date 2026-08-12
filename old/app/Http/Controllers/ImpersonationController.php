<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ImpersonationController extends Controller
{
    /**
     * Start impersonating a tenant's owner.
     * Only super admins can impersonate. POST-only for CSRF protection.
     *
     * Note: This endpoint is intentionally NOT gated by DemoMode.
     * Impersonation is one of the headline features buyers come to the
     * public demo to evaluate, so demo visitors logged in as the demo
     * SA must be able to step into a tenant.  The downstream
     * isSuperAdmin() check still blocks anonymous / non-SA users, and
     * the DemoMode guards on destructive endpoints (deletes, billing,
     * outbound mail, etc.) keep firing inside the impersonated session
     * because DemoMode::isOn() reads a global env flag — it doesn't
     * know or care which user the session is currently logged in as.
     */
    public function start(Request $request, Tenant $tenant): RedirectResponse
    {
        $superAdmin = Auth::user();

        if (! $superAdmin || ! $this->isSuperAdmin($superAdmin)) {
            abort(403, __('messages.only_super_admins_impersonate'));
        }

        if (! $tenant->owner) {
            return back()->with('error', __('messages.impersonate_no_owner'));
        }

        if (session()->has('impersonating_from')) {
            return back()->with('error', __('messages.impersonate_already_active'));
        }

        // Audit log before switching context
        AuditLog::record(
            'impersonation.started',
            $tenant,
            [],
            [
                'super_admin_id'    => $superAdmin->id,
                'super_admin_email' => $superAdmin->email,
                'target_user_id'    => $tenant->owner->id,
                'target_user_email' => $tenant->owner->email,
                'tenant_name'       => $tenant->name,
            ],
            'impersonation'
        );

        Log::info('Impersonation started', [
            'super_admin_id'    => $superAdmin->id,
            'super_admin_email' => $superAdmin->email,
            'tenant_id'         => $tenant->id,
            'tenant_name'       => $tenant->name,
            'target_user_id'    => $tenant->owner->id,
        ]);

        // Clear the old session data, then store impersonation keys
        $impersonationData = [
            'impersonating_from'       => $superAdmin->id,
            'impersonating_tenant_id'  => $tenant->id,
            'impersonating_started_at' => now()->toIso8601String(),
        ];

        // Invalidate old session & regenerate for security
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Log in as tenant owner
        Auth::login($tenant->owner);

        // Restore impersonation tracking in the new session
        foreach ($impersonationData as $key => $value) {
            session()->put($key, $value);
        }

        return redirect('/admin');
    }

    /**
     * Stop impersonating and return to super admin.
     * POST-only for CSRF protection.
     */
    public function stop(Request $request): RedirectResponse
    {
        $superAdminId = session()->get('impersonating_from');
        $tenantId     = session()->get('impersonating_tenant_id');

        if (! $superAdminId) {
            return redirect('/admin');
        }

        $superAdmin = User::find($superAdminId);

        if (! $superAdmin || ! $this->isSuperAdmin($superAdmin)) {
            // Safety: if something went wrong, log out entirely
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('/admin/login');
        }

        // Audit log
        AuditLog::record(
            'impersonation.stopped',
            $tenantId ? Tenant::find($tenantId) : null,
            [
                'impersonated_user_id' => Auth::id(),
            ],
            [
                'super_admin_id'    => $superAdmin->id,
                'super_admin_email' => $superAdmin->email,
            ],
            'impersonation'
        );

        Log::info('Impersonation stopped', [
            'super_admin_id' => $superAdmin->id,
            'tenant_id'      => $tenantId,
        ]);

        // Clean session & regenerate
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Restore super admin session
        Auth::login($superAdmin);

        return redirect('/super-admin');
    }

    /**
     * Consistent super admin check — delegates to the canonical
     * User::isSuperAdmin() helper.
     */
    private function isSuperAdmin(User $user): bool
    {
        return $user->isSuperAdmin();
    }
}
