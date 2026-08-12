<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnforceTenantScope
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            if (! $user->isSuperAdmin() && ! $user->tenant_id) {
                Auth::logout();
                return redirect()->route('filament.admin.auth.login')
                    ->withErrors(['email' => __('messages.no_tenant_assigned')]);
            }
        }

        return $next($request);
    }
}
