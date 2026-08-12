<?php

namespace App\Services\LeadSources;

use App\Models\LeadSourceConnection;
use App\Models\WebhookLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LinkedInConnector extends BaseConnector
{
    public function verifyWebhook(Request $request, LeadSourceConnection $connection): bool
    {
        $credentials = $connection->credentials ?? [];
        $clientSecret = $credentials['client_secret'] ?? null;

        if (! $clientSecret) {
            return true;
        }

        $signature = $request->header('X-Li-Signature');
        if (! $signature) {
            return false;
        }

        $payload  = $request->getContent();
        $expected = base64_encode(hash_hmac('sha256', $payload, $clientSecret, true));

        return hash_equals($expected, $signature);
    }

    public function handleWebhook(Request $request, LeadSourceConnection $connection, WebhookLog $log): array
    {
        $body  = json_decode($request->getContent(), true) ?? [];
        $leads = [];

        $events = $body['events'] ?? [$body];

        foreach ($events as $event) {
            if (($event['eventType'] ?? '') !== 'LEAD_FORM_RESPONSE') {
                continue;
            }

            $formResponse = $event['data']['formResponse'] ?? [];
            $answers      = collect($formResponse['answers'] ?? [])
                ->keyBy('questionId')
                ->map(fn ($a) => $a['answer'] ?? null)
                ->all();

            $firstName = $formResponse['firstName'] ?? '';
            $lastName  = $formResponse['lastName'] ?? '';

            $lead = $this->createLead([
                'source_id'  => $formResponse['id'] ?? $event['id'] ?? null,
                'first_name' => $firstName,
                'last_name'  => $lastName,
                'email'      => $formResponse['email'] ?? null,
                'phone'      => $this->normalizePhone($formResponse['phone'] ?? null),
                'raw_data'   => $event,
                'custom_fields' => $answers,
            ], $connection, $log);

            if ($lead) {
                $leads[] = $lead;
            }
        }

        return $leads;
    }

    public function getConnectionFormSchema(): array
    {
        return [
            'client_id'       => self::field('linkedin', 'client_id',       'string', true,  'Client ID'),
            'client_secret'   => self::field('linkedin', 'client_secret',   'string', true,  'Client Secret'),
            'access_token'    => self::field('linkedin', 'access_token',    'string', true,  'Access Token'),
            'organization_id' => self::field('linkedin', 'organization_id', 'string', false, 'Organization ID'),
        ];
    }

    public function testConnection(LeadSourceConnection $connection): bool
    {
        $credentials = $connection->credentials ?? [];
        $token       = $credentials['access_token'] ?? null;

        if (! $token) {
            return false;
        }

        $response = Http::withHeaders(['Authorization' => "Bearer {$token}"])
            ->get('https://api.linkedin.com/v2/me');

        return $response->successful();
    }
}
