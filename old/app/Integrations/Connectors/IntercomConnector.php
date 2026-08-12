<?php

namespace App\Integrations\Connectors;

use App\Integrations\BaseConnector;
use App\Models\Lead;
use Illuminate\Support\Facades\Http;

class IntercomConnector extends BaseConnector
{
    private string $baseUrl = 'https://api.intercom.io';

    private function headers(): array
    {
        $token = $this->config['access_token'] ?? $this->config['api_key'] ?? '';
        return [
            'Authorization' => 'Bearer ' . $token,
            'Accept'        => 'application/json',
        ];
    }

    public function testConnection(): bool
    {
        try {
            $res = Http::withHeaders($this->headers())->timeout(10)->get("{$this->baseUrl}/me");
            return $res->successful();
        } catch (\Throwable) { return false; }
    }

    public function pushLead(Lead $lead, array $fieldMapping = [], array $filterConfig = []): array
    {
        if (! $this->leadsFilter($lead, $filterConfig)) return ['skipped' => true];

        $res = Http::withHeaders($this->headers())->timeout(15)
            ->post("{$this->baseUrl}/contacts", [
                'role'       => 'lead',
                'email'      => $lead->email,
                'name'       => trim("{$lead->first_name} {$lead->last_name}"),
                'phone'      => $lead->phone,
                'custom_attributes' => [
                    'lead_source' => $lead->source,
                    'lead_score'  => $lead->lead_score,
                ],
            ]);

        return ['status_code' => $res->status(), 'body' => $res->json()];
    }
}
