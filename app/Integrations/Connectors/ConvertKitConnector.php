<?php

namespace App\Integrations\Connectors;

use App\Integrations\BaseConnector;
use App\Models\Lead;
use Illuminate\Support\Facades\Http;

class ConvertKitConnector extends BaseConnector
{
    private string $baseUrl = 'https://api.convertkit.com/v3';

    public function testConnection(): bool
    {
        try {
            $apiSecret = $this->config['api_secret'] ?? $this->config['api_key'] ?? '';
            $res = Http::timeout(10)->get("{$this->baseUrl}/account", ['api_secret' => $apiSecret]);
            return $res->successful();
        } catch (\Throwable) { return false; }
    }

    public function pushLead(Lead $lead, array $fieldMapping = [], array $filterConfig = []): array
    {
        if (! $this->leadsFilter($lead, $filterConfig)) return ['skipped' => true];

        $formId = $this->config['form_id'] ?? '';
        if (empty($formId)) throw new \RuntimeException(self::tx('integration_connectors.not_configured.convertkit', 'ConvertKit form ID not configured.'));

        $apiKey = $this->config['api_key'] ?? $this->config['api_secret'] ?? '';

        $payload = $this->applyFieldMapping(
            ['email' => $lead->email, 'first_name' => $lead->first_name],
            $lead, $fieldMapping
        );
        $payload['api_key'] = $apiKey;
        $res = Http::timeout(15)->post("{$this->baseUrl}/forms/{$formId}/subscribe", $payload);

        return ['status_code' => $res->status(), 'body' => $res->json()];
    }
}
