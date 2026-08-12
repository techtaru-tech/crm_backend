<?php

namespace App\Integrations\Connectors;

use App\Integrations\BaseConnector;
use App\Models\Lead;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class HubSpotConnector extends BaseConnector
{
    private string $baseUrl = 'https://api.hubapi.com';

    private function headers(): array
    {
        return [
            'Authorization' => 'Bearer ' . ($this->config['access_token'] ?? ''),
            'Content-Type'  => 'application/json',
        ];
    }

    public function testConnection(): bool
    {
        try {
            $res = Http::withHeaders($this->headers())
                ->timeout(10)
                ->get("{$this->baseUrl}/crm/v3/contacts", ['limit' => 1]);
            return $res->successful();
        } catch (\Throwable) { return false; }
    }

    public function pushLead(Lead $lead, array $fieldMapping = [], array $filterConfig = []): array
    {
        if (! $this->leadsFilter($lead, $filterConfig)) return ['skipped' => true];

        $mapped = $fieldMapping ?: [
            ['source_field' => 'email',      'target_field' => 'email'],
            ['source_field' => 'first_name',  'target_field' => 'firstname'],
            ['source_field' => 'last_name',   'target_field' => 'lastname'],
            ['source_field' => 'phone',       'target_field' => 'phone'],
            ['source_field' => 'company',     'target_field' => 'company'],
        ];

        $properties = $this->leadToPayload($lead, $mapped);

        $contactRes = Http::withHeaders($this->headers())
            ->timeout(15)
            ->post("{$this->baseUrl}/crm/v3/contacts", ['properties' => $properties]);

        $contactId = null;

        if ($contactRes->status() === 409) {
            $contactId = $this->findContactByEmail($lead->email);
            if ($contactId) {
                $contactRes = Http::withHeaders($this->headers())
                    ->timeout(15)
                    ->patch("{$this->baseUrl}/crm/v3/contacts/{$contactId}", ['properties' => $properties]);
            }
        } else {
            $contactId = $contactRes->json('id');
        }

        if ($contactId && ! empty($this->config['tags'])) {
            $this->applyTagsToContact($contactId, $this->config['tags']);
        }

        $dealId = null;
        if ($contactId && ($this->config['create_deal'] ?? true)) {
            $dealId = $this->createOrUpdateDeal($lead, $contactId);
        }

        return [
            'status_code' => $contactRes->status(),
            'contact_id'  => $contactId,
            'deal_id'     => $dealId,
            'body'        => $contactRes->json(),
        ];
    }

    private function applyTagsToContact(string $contactId, string $tags): void
    {
        try {
            $tagList = array_filter(array_map('trim', explode(',', $tags)));
            if (empty($tagList)) return;

            Http::withHeaders($this->headers())->timeout(10)
                ->patch("{$this->baseUrl}/crm/v3/contacts/{$contactId}", [
                    'properties' => ['hs_lead_status' => $tagList[0]],
                ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('connector status-sync failed', [
                'provider'   => 'hubspot',
                'contact_id' => $contactId,
                'error'      => $e->getMessage(),
            ]);
        }
    }

    private function createOrUpdateDeal(Lead $lead, string $contactId): ?string
    {
        $name       = trim("{$lead->first_name} {$lead->last_name}") . ' — ' . ($lead->company ?? 'Lead');
        $stageId    = $this->config['deal_stage'] ?? 'appointmentscheduled';
        $pipelineId = $this->config['deal_pipeline'] ?? 'default';

        $dealProperties = [
            'dealname'  => $name,
            'dealstage' => $stageId,
            'pipeline'  => $pipelineId,
            'closedate' => now()->addDays(30)->format('Y-m-d'),
        ];

        $existingDealId = $this->findExistingDeal($contactId);

        if ($existingDealId) {
            Http::withHeaders($this->headers())->timeout(15)
                ->patch("{$this->baseUrl}/crm/v3/objects/deals/{$existingDealId}", ['properties' => $dealProperties]);
            return $existingDealId;
        }

        $res    = Http::withHeaders($this->headers())->timeout(15)
            ->post("{$this->baseUrl}/crm/v3/objects/deals", ['properties' => $dealProperties]);
        $dealId = $res->json('id');

        if ($dealId) {
            Http::withHeaders($this->headers())->timeout(10)
                ->put("{$this->baseUrl}/crm/v3/objects/deals/{$dealId}/associations/contact/{$contactId}/3");
        }

        return $dealId;
    }

    private function findExistingDeal(string $contactId): ?string
    {
        $res     = Http::withHeaders($this->headers())->timeout(10)
            ->get("{$this->baseUrl}/crm/v3/objects/contacts/{$contactId}/associations/deals");
        $results = $res->json('results') ?? [];
        return $results[0]['id'] ?? null;
    }

    private function findContactByEmail(string $email): ?string
    {
        $res = Http::withHeaders($this->headers())
            ->timeout(10)
            ->post("{$this->baseUrl}/crm/v3/objects/contacts/search", [
                'filterGroups' => [[
                    'filters' => [['propertyName' => 'email', 'operator' => 'EQ', 'value' => $email]]
                ]],
                'limit' => 1,
            ]);

        return $res->json('results.0.id');
    }

    /**
     * Fetch available contact properties live from the HubSpot API.
     * Falls back to a static list if the API call fails or no token is configured.
     */
    public function getAvailableFields(): array
    {
        $token = $this->config['access_token'] ?? '';
        if (empty($token)) {
            return $this->staticFields();
        }

        $cacheKey = 'hubspot_fields_' . md5($token);
        return Cache::remember($cacheKey, 600, function () {
            try {
                $res = Http::withHeaders($this->headers())->timeout(15)
                    ->get("{$this->baseUrl}/crm/v3/properties/contact");

                if (! $res->successful()) {
                    return $this->staticFields();
                }

                $fields = [];
                foreach ($res->json('results') ?? [] as $prop) {
                    if (! ($prop['hidden'] ?? false) && ! ($prop['calculated'] ?? false)) {
                        $fields[$prop['name']] = $prop['label'] ?? $prop['name'];
                    }
                }

                return $fields ?: $this->staticFields();
            } catch (\Throwable) {
                return $this->staticFields();
            }
        });
    }

    protected function connectorSlug(): string
    {
        return 'hubspot';
    }

    private function staticFields(): array
    {
        return [
            'email'     => $this->fieldLabel('email', 'Email'),
            'firstname' => $this->fieldLabel('firstname', 'First Name'),
            'lastname'  => $this->fieldLabel('lastname', 'Last Name'),
            'phone'     => $this->fieldLabel('phone', 'Phone'),
            'company'   => $this->fieldLabel('company', 'Company'),
            'website'   => $this->fieldLabel('website', 'Website'),
            'address'   => $this->fieldLabel('address', 'Address'),
            'city'      => $this->fieldLabel('city', 'City'),
            'country'   => $this->fieldLabel('country', 'Country'),
            'jobtitle'  => $this->fieldLabel('jobtitle', 'Job Title'),
        ];
    }
}
