<?php

namespace App\Integrations\Connectors;

use App\Integrations\BaseConnector;
use App\Models\Lead;
use Illuminate\Support\Facades\Http;

class MoosendConnector extends BaseConnector
{
    private string $baseUrl = 'https://api.moosend.com/v3';

    public function testConnection(): bool
    {
        try {
            $apiKey = $this->config['api_key'] ?? '';
            $res = Http::timeout(10)->get("{$this->baseUrl}/lists.json", ['apikey' => $apiKey]);
            return $res->successful();
        } catch (\Throwable) { return false; }
    }

    public function pushLead(Lead $lead, array $fieldMapping = [], array $filterConfig = []): array
    {
        if (! $this->leadsFilter($lead, $filterConfig)) return ['skipped' => true];

        $apiKey = $this->config['api_key'] ?? '';
        $listId = $this->config['list_id'] ?? '';
        if (empty($listId)) throw new \RuntimeException(self::tx('integration_connectors.not_configured.moosend', 'Moosend list ID not configured.'));

        $payload = $this->applyFieldMapping(
            ['Email' => $lead->email, 'Name' => trim("{$lead->first_name} {$lead->last_name}")],
            $lead, $fieldMapping
        );
        $payload['apikey']       = $apiKey;
        $payload['CustomFields'] = [];
        $res = Http::timeout(15)->post("{$this->baseUrl}/subscribers/{$listId}/subscribe.json", $payload);

        return ['status_code' => $res->status(), 'body' => $res->json()];
    }
}
