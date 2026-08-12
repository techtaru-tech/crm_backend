<?php

namespace App\Integrations\Connectors;

use App\Integrations\BaseConnector;
use App\Models\Lead;
use Illuminate\Support\Facades\Http;

class MailchimpConnector extends BaseConnector
{
    private function baseUrl(): string
    {
        $dc = $this->config['data_center'] ?? 'us1';
        return "https://{$dc}.api.mailchimp.com/3.0";
    }

    private function headers(): array
    {
        return ['Authorization' => 'Basic ' . base64_encode('anystring:' . ($this->config['api_key'] ?? ''))];
    }

    public function testConnection(): bool
    {
        try {
            $res = Http::withHeaders($this->headers())->timeout(10)->get("{$this->baseUrl()}/ping");
            return $res->successful();
        } catch (\Throwable) { return false; }
    }

    public function pushLead(Lead $lead, array $fieldMapping = [], array $filterConfig = []): array
    {
        if (! $this->leadsFilter($lead, $filterConfig)) return ['skipped' => true];

        $listId = $this->config['list_id'] ?? '';
        if (empty($listId)) throw new \RuntimeException(self::tx('integration_connectors.not_configured.mailchimp', 'Mailchimp list ID not configured.'));

        $emailHash  = md5(strtolower($lead->email ?? ''));
        $mergeFields = $this->applyFieldMapping(
            ['FNAME' => $lead->first_name, 'LNAME' => $lead->last_name, 'PHONE' => $lead->phone],
            $lead,
            $fieldMapping
        );

        $tags = [];
        if (! empty($this->config['tags'])) {
            $tags = array_values(array_filter(array_map('trim', explode(',', $this->config['tags']))));
        }

        $payload = [
            'email_address' => $lead->email,
            'status_if_new' => 'subscribed',
            'merge_fields'  => $mergeFields,
        ];

        if (! empty($tags)) {
            $payload['tags'] = $tags;
        }

        $res = Http::withHeaders($this->headers())->timeout(15)
            ->put("{$this->baseUrl()}/lists/{$listId}/members/{$emailHash}", $payload);

        if (! empty($tags) && $res->successful()) {
            $this->applyTagsToMember($listId, $emailHash, $tags);
        }

        return ['status_code' => $res->status(), 'body' => $res->json()];
    }

    private function applyTagsToMember(string $listId, string $emailHash, array $tags): void
    {
        try {
            $tagPayload = array_map(fn($t) => ['name' => $t, 'status' => 'active'], $tags);
            Http::withHeaders($this->headers())->timeout(10)
                ->post("{$this->baseUrl()}/lists/{$listId}/members/{$emailHash}/tags", ['tags' => $tagPayload]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('connector tag-sync failed', [
                'provider' => 'mailchimp',
                'list_id'  => $listId,
                'error'    => $e->getMessage(),
            ]);
        }
    }

    protected function connectorSlug(): string
    {
        return 'mailchimp';
    }

    public function getAvailableFields(): array
    {
        return [
            'FNAME'   => $this->fieldLabel('first_name', 'First Name'),
            'LNAME'   => $this->fieldLabel('last_name', 'Last Name'),
            'PHONE'   => $this->fieldLabel('phone', 'Phone'),
            'COMPANY' => $this->fieldLabel('company', 'Company'),
        ];
    }
}
