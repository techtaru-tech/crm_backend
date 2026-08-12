<?php

declare(strict_types=1);

namespace App\Services\Ai;

use App\Models\Tenant;

/**
 * Single source of truth for "where does the OpenAI API key for this
 * tenant live?".
 *
 * The codebase had three near-identical 14-line `resolveApiKey`
 * methods in LeadEnrichmentService, AiEmailComposerService, and
 * LeadNextActionService.  Any change to the priority order (e.g.
 * adding a new settings key) had to land in three places — and
 * subtly diverged a couple of times in earlier review cycles.
 *
 * Lookup priority (first non-empty wins):
 *   1. tenants.settings.ai.openai_api_key   (preferred new key)
 *   2. tenants.settings.openai.api_key      (older nested layout)
 *   3. tenants.settings.openai_api_key      (flat legacy key)
 *   4. config('services.openai.api_key')    (script-level fallback)
 *   5. config('ai.openai.api_key')          (alternate script-level)
 */
final class TenantOpenAiKeyResolver
{
    /**
     * Resolve the API key for the given tenant id.  Convenience
     * wrapper for callers that already have the id but not the model.
     */
    public function resolveForId(?int $tenantId): ?string
    {
        if (! $tenantId) {
            return $this->scriptFallback();
        }

        $tenant = Tenant::find($tenantId);
        return $tenant ? $this->resolve($tenant) : $this->scriptFallback();
    }

    public function resolve(Tenant $tenant): ?string
    {
        $settings = (array) ($tenant->settings ?? []);
        $tenantKey = data_get($settings, 'ai.openai_api_key')
            ?? data_get($settings, 'openai.api_key')
            ?? data_get($settings, 'openai_api_key');

        if (is_string($tenantKey) && $tenantKey !== '') {
            return $tenantKey;
        }

        return $this->scriptFallback();
    }

    private function scriptFallback(): ?string
    {
        $key = config('services.openai.api_key') ?? config('ai.openai.api_key');
        return is_string($key) && $key !== '' ? $key : null;
    }
}
