<?php

namespace App\Integrations\Connectors;

use App\Integrations\BaseConnector;
use App\Models\Lead;
use Illuminate\Support\Facades\Http;

class BrevoConnector extends BaseConnector
{
    private string $baseUrl = 'https://api.brevo.com/v3';

    private function headers(): array
    {
        return ['api-key' => $this->config['api_key'] ?? ''];
    }

    public function testConnection(): bool
    {
        try {
            $res = Http::withHeaders($this->headers())->timeout(10)->get("{$this->baseUrl}/account");
            return $res->successful();
        } catch (\Throwable) { return false; }
    }

    public function pushLead(Lead $lead, array $fieldMapping = [], array $filterConfig = []): array
    {
        if (! $this->leadsFilter($lead, $filterConfig)) return ['skipped' => true];

        $attributes = $this->applyFieldMapping(
            ['FIRSTNAME' => $lead->first_name, 'LASTNAME' => $lead->last_name, 'SMS' => $lead->phone],
            $lead, $fieldMapping
        );
        $data = [
            'email'         => $lead->email,
            'attributes'    => $attributes,
            'updateEnabled' => true,
        ];

        if (! empty($this->config['list_id'])) {
            $data['listIds'] = [(int) $this->config['list_id']];
        }

        $res = Http::withHeaders($this->headers())->timeout(15)
            ->post("{$this->baseUrl}/contacts", $data);

        return ['status_code' => $res->status(), 'body' => $res->json()];
    }
}
