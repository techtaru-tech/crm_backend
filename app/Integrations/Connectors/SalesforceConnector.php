<?php

namespace App\Integrations\Connectors;

use App\Integrations\BaseConnector;
use App\Models\Lead;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class SalesforceConnector extends BaseConnector
{
    private function instanceUrl(): string
    {
        return rtrim($this->config['instance_url'] ?? 'https://login.salesforce.com', '/');
    }

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
                ->get("{$this->instanceUrl()}/services/data/v58.0/sobjects/Lead/describe");
            return $res->successful();
        } catch (\Throwable) { return false; }
    }

    public function pushLead(Lead $lead, array $fieldMapping = [], array $filterConfig = []): array
    {
        if (! $this->leadsFilter($lead, $filterConfig)) return ['skipped' => true];

        $object = $this->config['sf_object'] ?? 'Lead';

        $defaultMapping = $object === 'Contact' ? [
            ['source_field' => 'email',      'target_field' => 'Email'],
            ['source_field' => 'first_name',  'target_field' => 'FirstName'],
            ['source_field' => 'last_name',   'target_field' => 'LastName'],
            ['source_field' => 'phone',       'target_field' => 'Phone'],
        ] : [
            ['source_field' => 'email',      'target_field' => 'Email'],
            ['source_field' => 'first_name',  'target_field' => 'FirstName'],
            ['source_field' => 'last_name',   'target_field' => 'LastName'],
            ['source_field' => 'phone',       'target_field' => 'Phone'],
            ['source_field' => 'company',     'target_field' => 'Company'],
        ];

        $mapped = $fieldMapping ?: $defaultMapping;
        $data   = $this->leadToPayload($lead, $mapped);

        if (empty($data['Company']) && $object === 'Lead') {
            $data['Company'] = $lead->company ?: trim("{$lead->first_name} {$lead->last_name}") ?: 'Unknown';
        }
        if (empty($data['LastName'])) {
            $data['LastName'] = $lead->last_name ?: 'Unknown';
        }

        $base = "{$this->instanceUrl()}/services/data/v58.0";

        $existingId = $this->findExistingRecord($object, $lead->email, $base);

        if ($existingId) {
            $res = Http::withHeaders($this->headers())->timeout(15)
                ->patch("{$base}/sobjects/{$object}/{$existingId}", $data);
            $sfId = $existingId;
        } else {
            $res  = Http::withHeaders($this->headers())->timeout(15)
                ->post("{$base}/sobjects/{$object}/", $data);
            $sfId = $res->json('id');
        }

        $opportunityId = null;
        if ($sfId && $object === 'Contact' && ($this->config['create_opportunity'] ?? true)) {
            $opportunityId = $this->createOrUpdateOpportunity($lead, $sfId, $base);
        }

        return [
            'status_code'    => $res->status(),
            'sf_id'          => $sfId,
            'opportunity_id' => $opportunityId,
            'body'           => $res->json(),
        ];
    }

    private function findExistingRecord(string $object, string $email, string $base): ?string
    {
        try {
            $res = Http::withHeaders($this->headers())->timeout(10)
                ->get("{$base}/query", ['q' => "SELECT Id FROM {$object} WHERE Email = '{$email}' LIMIT 1"]);
            return $res->json('records.0.Id');
        } catch (\Throwable) { return null; }
    }

    private function createOrUpdateOpportunity(Lead $lead, string $contactId, string $base): ?string
    {
        $name = trim("{$lead->first_name} {$lead->last_name}") . ' — ' . ($lead->company ?? 'Opportunity');
        $data = [
            'Name'        => $name,
            'StageName'   => $this->config['opportunity_stage'] ?? 'Prospecting',
            'CloseDate'   => now()->addDays(30)->format('Y-m-d'),
            'LeadSource'  => ucfirst($lead->source ?? 'Other'),
        ];

        $existingRes = Http::withHeaders($this->headers())->timeout(10)
            ->get("{$base}/query", [
                'q' => "SELECT Id FROM Opportunity WHERE AccountId IN (SELECT AccountId FROM Contact WHERE Id = '{$contactId}') LIMIT 1"
            ]);
        $existingId = $existingRes->json('records.0.Id');

        if ($existingId) {
            Http::withHeaders($this->headers())->timeout(15)
                ->patch("{$base}/sobjects/Opportunity/{$existingId}", $data);
            return $existingId;
        }

        $res = Http::withHeaders($this->headers())->timeout(15)
            ->post("{$base}/sobjects/Opportunity/", $data);
        $oppId = $res->json('id');

        if ($oppId) {
            Http::withHeaders($this->headers())->timeout(10)
                ->post("{$base}/sobjects/OpportunityContactRole/", [
                    'OpportunityId' => $oppId,
                    'ContactId'     => $contactId,
                    'IsPrimary'     => true,
                ]);
        }

        return $oppId;
    }

    /**
     * Fetch fields live from Salesforce describe endpoint for the configured object.
     * Falls back to a static list if no access token is present.
     */
    public function getAvailableFields(): array
    {
        $token  = $this->config['access_token'] ?? '';
        $object = $this->config['sf_object'] ?? 'Lead';
        if (empty($token)) {
            return $this->staticFields($object);
        }

        $cacheKey = 'sf_fields_' . md5($token . $object);
        return Cache::remember($cacheKey, 600, function () use ($object) {
            try {
                $base = "{$this->instanceUrl()}/services/data/v58.0";
                $res  = Http::withHeaders($this->headers())->timeout(15)
                    ->get("{$base}/sobjects/{$object}/describe");

                if (! $res->successful()) {
                    return $this->staticFields($object);
                }

                $fields = [];
                foreach ($res->json('fields') ?? [] as $f) {
                    if (! ($f['deprecatedAndHidden'] ?? false) && ($f['updateable'] ?? true)) {
                        $fields[$f['name']] = $f['label'] ?? $f['name'];
                    }
                }

                return $fields ?: $this->staticFields($object);
            } catch (\Throwable) {
                return $this->staticFields($object);
            }
        });
    }

    protected function connectorSlug(): string
    {
        return 'salesforce';
    }

    private function staticFields(string $object = 'Lead'): array
    {
        return [
            'Email'       => $this->fieldLabel('email', 'Email'),
            'FirstName'   => $this->fieldLabel('first_name', 'First Name'),
            'LastName'    => $this->fieldLabel('last_name', 'Last Name'),
            'Phone'       => $this->fieldLabel('phone', 'Phone'),
            'Company'     => $this->fieldLabel('company', 'Company'),
            'LeadSource'  => $this->fieldLabel('lead_source', 'Lead Source'),
            'Status'      => $this->fieldLabel('status', 'Status'),
            'MobilePhone' => $this->fieldLabel('mobile_phone', 'Mobile Phone'),
            'Website'     => $this->fieldLabel('website', 'Website'),
            'City'        => $this->fieldLabel('city', 'City'),
            'Country'     => $this->fieldLabel('country', 'Country'),
        ];
    }
}
