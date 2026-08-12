<?php

namespace App\Integrations\Connectors;

use App\Integrations\BaseConnector;
use App\Models\Lead;
use Illuminate\Support\Facades\Http;

class GenericWebhookConnector extends BaseConnector
{
    public function testConnection(): bool
    {
        $url = $this->config['webhook_url'] ?? '';
        if (empty($url)) return false;
        try {
            $res = Http::timeout(10)->post($url, ['_test' => true]);
            return $res->successful();
        } catch (\Throwable) { return false; }
    }

    public function pushLead(Lead $lead, array $fieldMapping = [], array $filterConfig = []): array
    {
        if (! $this->leadsFilter($lead, $filterConfig)) return ['skipped' => true];

        $url = $this->config['webhook_url'] ?? '';
        if (empty($url)) throw new \RuntimeException(self::tx('integration_connectors.not_configured.generic_webhook', 'Webhook URL not configured.'));

        $bodyTemplate = $this->config['body_template'] ?? null;
        $headers      = $this->buildHeaders();

        if ($bodyTemplate) {
            $body = json_decode($this->interpolateTemplate($bodyTemplate, $lead), true)
                ?? ['raw' => $this->interpolateTemplate($bodyTemplate, $lead)];
        } else {
            $body = $this->leadToPayload($lead, $fieldMapping);
        }

        $request = Http::withHeaders($headers)->timeout(15);

        $method = strtolower($this->config['method'] ?? 'post');
        $res = $request->$method($url, $body);

        return ['status_code' => $res->status(), 'body' => $res->json() ?? $res->body()];
    }

    private function buildHeaders(): array
    {
        $headers = ['Content-Type' => 'application/json'];

        $authType = $this->config['auth_type'] ?? 'none';
        switch ($authType) {
            case 'bearer':
                $headers['Authorization'] = 'Bearer ' . ($this->config['auth_value'] ?? '');
                break;
            case 'api_key':
                $headerName = $this->config['auth_header'] ?? 'X-API-Key';
                $headers[$headerName] = $this->config['auth_value'] ?? '';
                break;
            case 'basic':
                $headers['Authorization'] = 'Basic ' . base64_encode($this->config['auth_value'] ?? '');
                break;
        }

        $customHeaders = $this->config['custom_headers'] ?? [];
        if (is_string($customHeaders)) {
            $customHeaders = json_decode($customHeaders, true) ?? [];
        }
        foreach ($customHeaders as $row) {
            if (! empty($row['key'])) {
                $headers[$row['key']] = $row['value'] ?? '';
            }
        }

        return $headers;
    }
}
