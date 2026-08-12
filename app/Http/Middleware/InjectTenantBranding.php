<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Services\TenantSmtpManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Applies per-tenant runtime settings on every panel request:
 * 1. Applies tenant SMTP config to Laravel's mail runtime config (before request).
 * 2. Injects branding CSS <link>, favicon <link>, and app-name title-replace <script>
 *    into HTML responses (after response).
 *
 * Exits early without modifying the response when no tenant is resolved or when
 * the response is not an HTML page (e.g. JSON, redirects, binary downloads).
 */
class InjectTenantBranding
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = $this->resolveTenant();

        if ($tenant !== null) {
            try {
                app(TenantSmtpManager::class)->applyForTenant($tenant);
            } catch (\Throwable $e) {
                logger()->warning('[Branding] Failed to apply tenant SMTP config: ' . $e->getMessage());
            }
        }

        $response = $next($request);

        if ($tenant === null) {
            return $response;
        }

        return $this->injectBrandingIntoHtml($response, $tenant);
    }

    private function resolveTenant(): ?Tenant
    {
        if (! app()->bound('current_tenant')) {
            return null;
        }

        $resolved = app('current_tenant');

        return $resolved instanceof Tenant ? $resolved : null;
    }

    private function injectBrandingIntoHtml(Response $response, Tenant $tenant): Response
    {
        if (! method_exists($response, 'getContent') || ! method_exists($response, 'setContent')) {
            return $response;
        }

        $contentType = $response->headers->get('Content-Type', '');
        if (! str_contains($contentType, 'text/html')) {
            return $response;
        }

        $content = $response->getContent();
        if (empty($content) || ! str_contains($content, '</head>')) {
            return $response;
        }

        $inject = $this->buildInjectTags($tenant);
        if (empty($inject)) {
            return $response;
        }

        $response->setContent(str_replace('</head>', $inject . '</head>', $content));

        return $response;
    }

    private function buildInjectTags(Tenant $tenant): string
    {
        $inject  = '';
        $cssPath = public_path("tenant/{$tenant->id}/branding.css");

        if (! file_exists($cssPath)) {
            try {
                app(\App\Services\SettingsService::class)->forTenant($tenant)->generateBrandingCss($tenant);
            } catch (\Throwable $e) {
                logger()->warning('[Branding] Failed to generate branding CSS: ' . $e->getMessage());
                return $inject;
            }
        }

        if (file_exists($cssPath)) {
            $cssUrl  = asset("tenant/{$tenant->id}/branding.css") . '?v=' . filemtime($cssPath);
            $inject .= '<link rel="stylesheet" href="' . e($cssUrl) . '">';
        }

        $faviconUrl = $tenant->getBranding('favicon_url', '');
        if ($faviconUrl) {
            $inject .= '<link rel="icon" href="' . e($faviconUrl) . '" />';
        }

        $appName     = $tenant->getAppName();
        $defaultName = config('leadhub.branding.app_name', 'LeadHub');

        if ($appName && $appName !== $defaultName) {
            $escapedDefault = $this->jsStringLiteral($defaultName);
            $escapedNew     = $this->jsStringLiteral($appName);
            $inject .= "<script>document.title = document.title.split({$escapedDefault}).join({$escapedNew});</script>";
        }

        return $inject;
    }

    /**
     * Safely encodes a string as a JavaScript string literal (double-quoted).
     * Uses json_encode with HTML-safe flags so the output is safe for inline
     * <script> blocks and does NOT rely on PHP's preg_quote() — which escapes
     * PHP regex metacharacters, not JavaScript string metacharacters.
     */
    private function jsStringLiteral(string $value): string
    {
        return json_encode($value, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
    }
}
