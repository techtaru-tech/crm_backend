<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Auto-expires impersonation sessions after a configurable timeout.
 * Also validates that the impersonation state is consistent.
 */
class CheckImpersonation
{
    private const MAX_IMPERSONATION_MINUTES = 60;

    public function handle(Request $request, Closure $next): Response
    {
        if (! session()->has('impersonating_from')) {
            return $next($request);
        }

        $startedAt = session('impersonating_started_at');

        // Auto-expire if too old
        if ($startedAt) {
            $started = Carbon::parse($startedAt);
            if ($started->diffInMinutes(now()) >= self::MAX_IMPERSONATION_MINUTES) {
                return $this->endImpersonation(
                    $request,
                    __('middleware.impersonation.expired', ['minutes' => self::MAX_IMPERSONATION_MINUTES]),
                );
            }
        }

        // Validate that the stored super admin still exists and is still SA
        $superAdminId = session('impersonating_from');
        $superAdmin   = User::find($superAdminId);

        if (! $superAdmin || ! $superAdmin->isSuperAdmin()) {
            return $this->endImpersonation($request, __('middleware.impersonation.invalidated'));
        }

        return $next($request);
    }

    private function endImpersonation(Request $request, string $reason): Response
    {
        $superAdminId = session()->pull('impersonating_from');
        session()->forget(['impersonating_tenant_id', 'impersonating_started_at']);

        Log::info('Impersonation auto-ended', [
            'reason'         => $reason,
            'super_admin_id' => $superAdminId,
            'user_id'        => Auth::id(),
        ]);

        $superAdmin = $superAdminId ? User::find($superAdminId) : null;

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($superAdmin && $superAdmin->isSuperAdmin()) {
            Auth::login($superAdmin);
            return redirect('/super-admin')->with('warning', $reason);
        }

        Auth::logout();
        return redirect('/admin/login')->with('status', $reason);
    }
}
