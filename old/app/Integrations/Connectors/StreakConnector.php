<?php

namespace App\Integrations\Connectors;

use App\Integrations\BaseConnector;
use App\Models\Lead;
use Illuminate\Support\Facades\Http;

class StreakConnector extends BaseConnector
{
    private string $baseUrl = 'https://www.streak.com/api/v2';

    private function headers(): array
    {
        return ['Authorization' => 'Basic ' . base64_encode($this->config['api_key'] ?? '')];
    }

    public function testConnection(): bool
    {
        try {
            $res = Http::withHeaders($this->headers())->timeout(10)->get("{$this->baseUrl}/user");
            return $res->successful();
        } catch (\Throwable) { return false; }
    }

    public function pushLead(Lead $lead, array $fieldMapping = [], array $filterConfig = []): array
    {
        if (! $this->leadsFilter($lead, $filterConfig)) return ['skipped' => true];

        $pipelineKey = $this->config['pipeline_key'] ?? '';
        if (empty($pipelineKey)) throw new \RuntimeException(self::tx('integration_connectors.not_configured.streak', 'Streak pipeline key not configured.'));

        $payload = $this->applyFieldMapping(
            ['name' => trim("{$lead->first_name} {$lead->last_name}")],
            $lead, $fieldMapping
        );
        $res = Http::withHeaders($this->headers())->timeout(15)
            ->post("https://www.streak.com/api/v1/pipelines/{$pipelineKey}/boxes", $payload);

        return ['status_code' => $res->status(), 'body' => $res->json()];
    }
}
