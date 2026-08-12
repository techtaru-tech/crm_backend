<?php

namespace App\Integrations\Connectors;

use App\Integrations\BaseConnector;
use App\Models\Lead;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class NotionConnector extends BaseConnector
{
    private string $baseUrl = 'https://api.notion.com/v1';

    private function headers(): array
    {
        return [
            'Authorization'  => 'Bearer ' . ($this->config['api_key'] ?? ''),
            'Notion-Version' => '2022-06-28',
        ];
    }

    public function testConnection(): bool
    {
        try {
            $res = Http::withHeaders($this->headers())->timeout(10)->get("{$this->baseUrl}/users/me");
            return $res->successful();
        } catch (\Throwable) { return false; }
    }

    public function pushLead(Lead $lead, array $fieldMapping = [], array $filterConfig = []): array
    {
        if (! $this->leadsFilter($lead, $filterConfig)) return ['skipped' => true];

        $dbId = $this->config['database_id'] ?? '';
        if (empty($dbId)) throw new \RuntimeException(self::tx('integration_connectors.not_configured.notion', 'Notion database ID not configured.'));

        $properties = $this->buildProperties($lead, $fieldMapping);

        $res = Http::withHeaders($this->headers())->timeout(15)
            ->post("{$this->baseUrl}/pages", [
                'parent'     => ['database_id' => $dbId],
                'properties' => $properties,
            ]);

        return ['status_code' => $res->status(), 'body' => $res->json()];
    }

    /**
     * Build Notion page properties, applying user-configured property mapping.
     * Users can configure: prop_name (Notion property name), prop_type (title|rich_text|email|phone_number|url|select)
     * plus map source lead fields to Notion properties.
     */
    private function buildProperties(Lead $lead, array $fieldMapping): array
    {
        $propMapping = $this->config['property_mapping'] ?? null;

        if ($propMapping && is_string($propMapping)) {
            $propMapping = json_decode($propMapping, true) ?: null;
        }

        if ($propMapping && is_array($propMapping)) {
            $properties = [];
            foreach ($propMapping as $entry) {
                $notionProp = $entry['notion_prop'] ?? '';
                $propType   = $entry['prop_type'] ?? 'rich_text';
                $sourceField = $entry['source_field'] ?? '';
                $value = $this->resolveLeadField($lead, $sourceField);

                if ($notionProp === '' || $value === null) continue;

                $properties[$notionProp] = $this->formatNotionProp($propType, (string) $value);
            }
            return $properties;
        }

        return [
            'Name'   => ['title' => [['text' => ['content' => trim("{$lead->first_name} {$lead->last_name}")]]]],
            'Email'  => ['email' => $lead->email],
            'Phone'  => ['phone_number' => $lead->phone],
            'Source' => ['rich_text' => [['text' => ['content' => $lead->source ?? '']]]],
            'Status' => ['rich_text' => [['text' => ['content' => $lead->status?->value ?? '']]]],
            'Company'=> ['rich_text' => [['text' => ['content' => $lead->company ?? '']]]],
        ];
    }

    private function resolveLeadField(Lead $lead, string $field): mixed
    {
        return match ($field) {
            'first_name'  => $lead->first_name,
            'last_name'   => $lead->last_name,
            'full_name'   => trim("{$lead->first_name} {$lead->last_name}"),
            'email'       => $lead->email,
            'phone'       => $lead->phone,
            'company'     => $lead->company,
            'source'      => $lead->source,
            'status'      => $lead->status?->value,
            'notes'       => $lead->notes,
            'website'     => $lead->custom_data['website'] ?? null,
            default       => $lead->custom_data[$field] ?? null,
        };
    }

    private function formatNotionProp(string $type, string $value): array
    {
        return match ($type) {
            'title'        => ['title' => [['text' => ['content' => $value]]]],
            'email'        => ['email' => $value],
            'phone_number' => ['phone_number' => $value],
            'url'          => ['url' => $value],
            'select'       => ['select' => ['name' => $value]],
            'number'       => ['number' => is_numeric($value) ? (float) $value : 0],
            default        => ['rich_text' => [['text' => ['content' => $value]]]],
        };
    }

    /**
     * Fetch database schema live to return available Notion properties.
     */
    public function getAvailableFields(): array
    {
        $apiKey = $this->config['api_key'] ?? '';
        $dbId   = $this->config['database_id'] ?? '';

        if (empty($apiKey) || empty($dbId)) {
            return $this->staticFields();
        }

        $cacheKey = 'notion_fields_' . md5($apiKey . $dbId);
        return Cache::remember($cacheKey, 600, function () use ($dbId) {
            try {
                $res = Http::withHeaders($this->headers())->timeout(15)
                    ->get("{$this->baseUrl}/databases/{$dbId}");

                if (! $res->successful()) return $this->staticFields();

                $fields = [];
                foreach ($res->json('properties') ?? [] as $name => $prop) {
                    $fields[$name] = $name . ' (' . ($prop['type'] ?? 'text') . ')';
                }

                return $fields ?: $this->staticFields();
            } catch (\Throwable) {
                return $this->staticFields();
            }
        });
    }

    protected function connectorSlug(): string
    {
        return 'notion';
    }

    private function staticFields(): array
    {
        return [
            'Name'    => $this->fieldLabel('name', 'Name (title)'),
            'Email'   => $this->fieldLabel('email', 'Email (email)'),
            'Phone'   => $this->fieldLabel('phone', 'Phone (phone_number)'),
            'Source'  => $this->fieldLabel('source', 'Source (rich_text)'),
            'Status'  => $this->fieldLabel('status', 'Status (rich_text)'),
            'Company' => $this->fieldLabel('company', 'Company (rich_text)'),
        ];
    }
}
