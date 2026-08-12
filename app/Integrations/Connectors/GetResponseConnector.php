<?php

namespace App\Integrations\Connectors;

use App\Integrations\BaseConnector;
use App\Models\Lead;
use Illuminate\Support\Facades\Http;

class GetResponseConnector extends BaseConnector
{
    private string $baseUrl = 'https://api.getresponse.com/v3';

    private function headers(): array
    {
        return ['X-Auth-Token' => 'api-key ' . ($this->config['api_key'] ?? '')];
    }

    public function testConnection(): bool
    {
        try {
            $res = Http::withHeaders($this->headers())->timeout(10)->get("{$this->baseUrl}/accounts");
            return $res->successful();
        } catch (\Throwable) { return false; }
    }

    public function pushLead(Lead $lead, array $fieldMapping = [], array $filterConfig = []): array
    {
        if (! $this->leadsFilter($lead, $filterConfig)) return ['skipped' => true];

        $campaignId = $this->config['campaign_id'] ?? '';
        if (empty($campaignId)) throw new \RuntimeException(self::tx('integration_connectors.not_configured.getresponse', 'GetResponse campaign ID not configured.'));

        $payload = $this->applyFieldMapping(
            ['email' => $lead->email, 'name' => trim("{$lead->first_name} {$lead->last_name}")],
            $lead, $fieldMapping
        );
        $payload['campaign'] = ['campaignId' => $campaignId];
        $res = Http::withHeaders($this->headers())->timeout(15)
            ->post("{$this->baseUrl}/contacts", $payload);

        return ['status_code' => $res->status(), 'body' => $res->json()];
    }
}
