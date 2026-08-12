<?php

namespace App\Services\LeadSources;

use App\Contracts\SourceConnector;
use App\Models\Lead;
use App\Models\LeadSourceConnection;
use App\Models\WebhookLog;
use App\Services\LeadIngestionService;
use Illuminate\Http\Request;

abstract class BaseConnector implements SourceConnector
{
    public function __construct(protected LeadIngestionService $ingestionService) {}

    public function verifyWebhook(Request $request, LeadSourceConnection $connection): bool
    {
        return true;
    }

    public function testConnection(LeadSourceConnection $connection): bool
    {
        return true;
    }

    public function getConnectionFormSchema(): array
    {
        return [];
    }

    /**
     * Translator-first lookup with English fallback. Used by connector
     * subclasses so the CodeCanyon "no hardcoded text" rule is satisfied
     * while keeping the English literals as the canonical source of truth
     * for missing-translation fallback.
     */
    protected static function tx(string $key, string $fallback): string
    {
        $translated = __($key);
        return $translated === $key ? $fallback : (string) $translated;
    }

    /**
     * Build a `type|required|label` schema string with a translator-first
     * label. The label is looked up under
     * `lead_sources_connectors.<connector_slug>.<field>` and falls back to
     * the supplied English literal when no translation exists. Shared
     * helper so every concrete connector's `getConnectionFormSchema()`
     * stays a one-line-per-field array literal.
     */
    protected static function field(
        string $connectorSlug,
        string $field,
        string $type,
        bool $required,
        string $englishLabel
    ): string {
        $label = self::tx(
            'lead_sources_connectors.' . $connectorSlug . '.' . $field,
            $englishLabel
        );

        return $type . '|' . ($required ? 'required' : 'optional') . '|' . $label;
    }

    protected function createLead(array $data, LeadSourceConnection $connection, WebhookLog $log): ?Lead
    {
        return $this->ingestionService->createOrSkipDuplicate($data, $connection, $log);
    }

    protected function parseFullName(string $fullName): array
    {
        $parts = explode(' ', trim($fullName), 2);
        return [
            'first_name' => $parts[0] ?? '',
            'last_name'  => $parts[1] ?? '',
        ];
    }

    protected function normalizePhone(?string $phone): ?string
    {
        if (! $phone) {
            return null;
        }
        $normalized = preg_replace('/[^\d+]/', '', $phone);
        return $normalized ?: null;
    }
}
