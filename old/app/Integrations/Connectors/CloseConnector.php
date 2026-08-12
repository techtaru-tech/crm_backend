<?php

namespace App\Integrations\Connectors;

use App\Integrations\BaseConnector;
use App\Models\Lead;
use Illuminate\Support\Facades\Http;

class CloseConnector extends BaseConnector
{
    private string $baseUrl = 'https://api.close.com/api/v1';

    private function auth(): array
    {
        return [$this->config['api_key'] ?? '', ''];
    }

    public function testConnection(): bool
    {
        try {
            $res = Http::withBasicAuth(...$this->auth())->timeout(10)->get("{$this->baseUrl}/me/");
            return $res->successful();
        } catch (\Throwable) { return false; }
    }

    public function pushLead(Lead $lead, array $fieldMapping = [], array $filterConfig = []): array
    {
        if (! $this->leadsFilter($lead, $filterConfig)) return ['skipped' => true];

        $payload = $this->applyFieldMapping([
            'name'     => trim("{$lead->first_name} {$lead->last_name}"),
            'contacts' => [[
                'name'   => trim("{$lead->first_name} {$lead->last_name}"),
                'emails' => $lead->email ? [['email' => $lead->email, 'type' => 'work']] : [],
                'phones' => $lead->phone ? [['phone' => $lead->phone, 'type' => 'work']] : [],
            ]],
        ], $lead, $fieldMapping);
        $res = Http::withBasicAuth(...$this->auth())->timeout(15)
            ->post("{$this->baseUrl}/lead/", $payload);

        return ['status_code' => $res->status(), 'body' => $res->json()];
    }
}
