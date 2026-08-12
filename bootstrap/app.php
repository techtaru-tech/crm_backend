<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // TrustProxies must run before any middleware that reads
        // $request->ip() (lockout, audit log, SA IP allowlist).
        // Defaults to trusting NOTHING — buyers behind a proxy must
        // opt in via TRUSTED_PROXIES env.  See .env.example.
        $middleware->web(prepend: [
            \App\Http\Middleware\TrustProxies::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\ResolveTenant::class,
            \App\Http\Middleware\TrackUserSession::class,
            \App\Http\Middleware\SetLocale::class,
            // SecurityHeaders adds X-Frame-Options, X-Content-Type-
            // Options, and Referrer-Policy on every web response.
            // The form-widget embeddable surface opts out via per-
            // route ->withoutMiddleware(SecurityHeaders::class).
            \App\Http\Middleware\SecurityHeaders::class,
            // EnforceLoginLockout is INTENTIONALLY NOT in the global
            // web stack any more.  Filament 4 + Livewire submit
            // credentials via /livewire/update, never the panel's
            // /admin/login URL — so a path-based middleware never
            // sees the request body.  All Filament panels enforce
            // the lockout via App\Filament\Auth\Pages\Login (which
            // hooks Livewire's authenticate()).  Keep the
            // 'login.lockout' alias below so any custom non-Filament
            // login route can still opt in explicitly.
        ]);

        $middleware->alias([
            'tenant.scope'       => \App\Http\Middleware\EnforceTenantScope::class,
            'resolve.tenant'     => \App\Http\Middleware\ResolveTenant::class,
            'api.key'            => \App\Http\Middleware\ApiKeyAuthentication::class,
            'portal.auth'        => \App\Http\Middleware\PortalAuth::class,
            'login.lockout'      => \App\Http\Middleware\EnforceLoginLockout::class,
            'sa.ip.allowlist'    => \App\Http\Middleware\EnforceSuperAdminIpAllowlist::class,
            'role'               => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission'         => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, $request) {
            if ($request->is('api/*')) {
                return \App\Http\Responses\ApiErrorResponse::validation($e->errors(), $e->getMessage());
            }
        });

        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return \App\Http\Responses\ApiErrorResponse::unauthorized();
            }
        });

        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, $request) {
            if ($request->is('api/*')) {
                return \App\Http\Responses\ApiErrorResponse::forbidden($e->getMessage());
            }
        });

        $exceptions->render(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, $request) {
            if ($request->is('api/*')) {
                $model = class_basename($e->getModel());
                return \App\Http\Responses\ApiErrorResponse::notFound("{$model} not found.");
            }
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            if ($request->is('api/*')) {
                return \App\Http\Responses\ApiErrorResponse::notFound('Endpoint not found.');
            }
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException $e, $request) {
            if ($request->is('api/*')) {
                return \App\Http\Responses\ApiErrorResponse::make('Method not allowed.', 405, 'METHOD_NOT_ALLOWED');
            }
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException $e, $request) {
            if ($request->is('api/*')) {
                return \App\Http\Responses\ApiErrorResponse::rateLimited();
            }
        });
    })->create();
