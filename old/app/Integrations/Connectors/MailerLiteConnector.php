<?php

namespace App\Integrations\Connectors;

use App\Integrations\BaseConnector;
use App\Models\Lead;
use Illuminate\Support\Facades\Http;

class MailerLiteConnector extends BaseConnector
{
    private string $baseUrl = 'https://connect.mailerlite.com/api';

    private function headers(): array
    {
        return ['Authorization' => 'Bearer ' . ($this->config['api_key'] ?? '')];
    }

    public function testConnection(): bool
    {
        try {
            $res = Http::withHeaders($this->headers())->timeout(10)->get("{$this->baseUrl}/subscribers?limit=1");
            return $res->successful();
        } catch (\Throwable) { return false; }
    }

    public function pushLead(Lead $lead, array $fieldMapping = [], array $filterConfig = []): array
    {
        if (! $this->leadsFilter($lead, $filterConfig)) return ['skipped' => true];

        $fields = $this->applyFieldMapping(
            ['name' => $lead->first_name, 'last_name' => $lead->last_name, 'phone' => $lead->phone],
            $lead, $fieldMapping
        );
        $data = ['email' => $lead->email, 'fields' => $fields];

        if (! empty($this->config['group_id'])) {
            $data['groups'] = [$this->config['group_id']];
        }

        $res = Http::withHeaders($this->headers())->timeout(15)
            ->post("{$this->baseUrl}/subscribers", $data);

        return ['status_code' => $res->status(), 'body' => $res->json()];
    }
}
