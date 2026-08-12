<?php

namespace App\Services\LeadSources;

use App\Models\LeadSourceConnection;
use App\Models\WebhookLog;
use Illuminate\Http\Request;

class MicrosoftAdsConnector extends BaseConnector
{
    public function verifyWebhook(Request $request, LeadSourceConnection $connection): bool
    {
        $credentials = $connection->credentials ?? [];
        $secret      = $credentials['webhook_secret'] ?? null;

        if (! $secret) {
            return true;
        }

        $signature = $request->header('X-MSADS-Signature');
        $payload   = $request->getContent();

        if (! $signature) {
            return false;
        }

        $expected = base64_encode(hash_hmac('sha256', $payload, $secret, true));
        return hash_equals($expected, $signature);
    }

    public function handleWebhook(Request $request, LeadSourceConnection $connection, WebhookLog $log): array
    {
        $body  = json_decode($request->getContent(), true) ?? [];
        $leads = [];

        $events = $body['events'] ?? [$body];

        foreach ($events as $event) {
            $leadData = $event['leadData'] ?? $event['lead'] ?? $event;
            $answers  = collect($leadData['answers'] ?? $leadData['fields'] ?? [])
                ->keyBy('name')
                ->map(fn ($a) => $a['value'] ?? null)
                ->all();

            $fullName  = $answers['FullName'] ?? $answers['full_name'] ?? '';
            $nameParts = $this->parseFullName($fullName);

            $lead = $this->createLead([
                'source_id'  => $leadData['id'] ?? $event['id'] ?? null,
                'first_name' => $answers['FirstName'] ?? $nameParts['first_name'],
                'last_name'  => $answers['LastName'] ?? $nameParts['last_name'],
                'email'      => $answers['Email'] ?? $answers['email'] ?? null,
                'phone'      => $this->normalizePhone($answers['PhoneNumber'] ?? $answers['phone'] ?? null),
                'raw_data'   => $event,
                'custom_fields' => array_diff_key($answers, array_flip([
                    'FullName', 'FirstName', 'LastName', 'Email', 'PhoneNumber',
                ])),
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
            'client_id'       => self::field('microsoft_ads', 'client_id',       'string', true,  'Client ID'),
            'client_secret'   => self::field('microsoft_ads', 'client_secret',   'string', true,  'Client Secret'),
            'refresh_token'   => self::field('microsoft_ads', 'refresh_token',   'string', true,  'Refresh Token'),
            'developer_token' => self::field('microsoft_ads', 'developer_token', 'string', true,  'Developer Token'),
            'account_id'      => self::field('microsoft_ads', 'account_id',      'string', true,  'Account ID'),
            'webhook_secret'  => self::field('microsoft_ads', 'webhook_secret',  'string', false, 'Webhook Secret'),
        ];
    }
}
