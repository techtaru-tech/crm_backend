<?php

namespace App\Integrations\Connectors;

use App\Integrations\BaseConnector;
use App\Models\Lead;
use Illuminate\Support\Facades\Http;

class AirtableConnector extends BaseConnector
{
    private string $baseUrl = 'https://api.airtable.com/v0';

    private function headers(): array
    {
        return ['Authorization' => 'Bearer ' . ($this->config['api_key'] ?? '')];
    }

    public function testConnection(): bool
    {
        try {
            $baseId    = $this->config['base_id'] ?? '';
            $tableId   = $this->config['table_id'] ?? '';
            if (empty($baseId) || empty($tableId)) return false;
            $res = Http::withHeaders($this->headers())->timeout(10)
                ->get("{$this->baseUrl}/{$baseId}/{$tableId}", ['maxRecords' => 1]);
            return $res->successful();
        } catch (\Throwable) { return false; }
    }

    public function pushLead(Lead $lead, array $fieldMapping = [], array $filterConfig = []): array
    {
        if (! $this->leadsFilter($lead, $filterConfig)) return ['skipped' => true];

        $baseId  = $this->config['base_id'] ?? '';
        $tableId = $this->config['table_id'] ?? '';
        if (empty($baseId) || empty($tableId)) throw new \RuntimeException(self::tx('integration_connectors.not_configured.airtable', 'Airtable base/table ID not configured.'));

        $payload = $this->leadToPayload($lead, $fieldMapping);
        $fields  = [];

        if (empty($fieldMapping)) {
            $fields = [
                'Name'   => trim("{$lead->first_name} {$lead->last_name}"),
                'Email'  => $lead->email,
                'Phone'  => $lead->phone,
                'Source' => $lead->source,
                'Status' => $lead->status?->value,
            ];
        } else {
            $fields = $payload;
        }

        $res = Http::withHeaders($this->headers())->timeout(15)
            ->post("{$this->baseUrl}/{$baseId}/{$tableId}", [
                'records' => [['fields' => $fields]],
            ]);

        return ['status_code' => $res->status(), 'body' => $res->json()];
    }
}
