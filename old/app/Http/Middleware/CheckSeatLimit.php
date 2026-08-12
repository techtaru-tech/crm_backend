<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Prevents new users from being added once the seat limit is reached.
 * Used on invite/registration routes.
 */
class CheckSeatLimit
{
    public function handle(Request $request, Closure $next): Response
    {
        $user   = $request->user();
        $tenant = $user?->tenant ?? (app()->bound('current_tenant') ? app('current_tenant') : null);

        if ($tenant && $tenant->seat_count >= $tenant->max_seats) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error'   => __('middleware.seat_limit.reached_short'),
                    'message' => __('middleware.seat_limit.reached_short_alt'),
                ], Response::HTTP_FORBIDDEN);
            }

            return redirect()->back()->withErrors([
                'seat_limit' => __('middleware.seat_limit.reached_full', ['max' => $tenant->max_seats]),
            ]);
        }

        return $next($request);
    }
}
