<?php

namespace App\Integrations\Connectors;

use App\Integrations\BaseConnector;
use App\Models\Lead;
use Illuminate\Support\Facades\Http;

class CopperConnector extends BaseConnector
{
    private string $baseUrl = 'https://api.copper.com/developer_api/v1';

    private function headers(): array
    {
        return [
            'X-PW-AccessToken' => $this->config['api_key'] ?? '',
            'X-PW-Application' => 'developer_api',
            'X-PW-UserEmail'   => $this->config['user_email'] ?? '',
            'Content-Type'     => 'application/json',
        ];
    }

    public function testConnection(): bool
    {
        try {
            $res = Http::withHeaders($this->headers())->timeout(10)
                ->post("{$this->baseUrl}/leads/search", ['page_size' => 1]);
            return $res->successful();
        } catch (\Throwable) { return false; }
    }

    public function pushLead(Lead $lead, array $fieldMapping = [], array $filterConfig = []): array
    {
        if (! $this->leadsFilter($lead, $filterConfig)) return ['skipped' => true];

        $payload = $this->applyFieldMapping([
            'name'          => trim("{$lead->first_name} {$lead->last_name}"),
            'email'         => ['email' => $lead->email, 'category' => 'work'],
            'phone_numbers' => $lead->phone ? [['number' => $lead->phone, 'category' => 'work']] : [],
            'company_name'  => $lead->company,
        ], $lead, $fieldMapping);
        $res = Http::withHeaders($this->headers())->timeout(15)
            ->post("{$this->baseUrl}/leads", $payload);

        return ['status_code' => $res->status(), 'body' => $res->json()];
    }
}
