<?php

namespace App\Services\LeadSources;

use App\Models\LeadSourceConnection;
use App\Models\WebhookLog;
use Illuminate\Http\Request;

class PinterestConnector extends BaseConnector
{
    public function verifyWebhook(Request $request, LeadSourceConnection $connection): bool
    {
        $credentials = $connection->credentials ?? [];
        $appSecret   = $credentials['app_secret'] ?? null;

        if (! $appSecret) {
            return true;
        }

        $signature = $request->header('Pinterest-Signature');
        $payload   = $request->getContent();

        if (! $signature) {
            return false;
        }

        $expected = base64_encode(hash_hmac('sha256', $payload, $appSecret, true));
        return hash_equals($expected, $signature);
    }

    public function handleWebhook(Request $request, LeadSourceConnection $connection, WebhookLog $log): array
    {
        $body  = json_decode($request->getContent(), true) ?? [];
        $leads = [];

        $events = $body['events'] ?? [$body];

        foreach ($events as $event) {
            if (($event['event_type'] ?? '') !== 'lead') {
                continue;
            }

            $leadData = $event['lead_data'] ?? $event;
            $fields   = collect($leadData['answers'] ?? $leadData['fields'] ?? [])
                ->keyBy('question_type')
                ->map(fn ($a) => $a['answer'] ?? null)
                ->all();

            $fullName  = $fields['FULL_NAME'] ?? $fields['full_name'] ?? '';
            $nameParts = $this->parseFullName($fullName);

            $lead = $this->createLead([
                'source_id'  => $event['lead_id'] ?? $event['id'] ?? null,
                'first_name' => $fields['FIRST_NAME'] ?? $nameParts['first_name'],
                'last_name'  => $fields['LAST_NAME'] ?? $nameParts['last_name'],
                'email'      => $fields['EMAIL'] ?? null,
                'phone'      => $this->normalizePhone($fields['PHONE_NUMBER'] ?? null),
                'raw_data'   => $event,
                'custom_fields' => array_diff_key($fields, array_flip([
                    'FULL_NAME', 'FIRST_NAME', 'LAST_NAME', 'EMAIL', 'PHONE_NUMBER',
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
            'app_id'        => self::field('pinterest', 'app_id',        'string', true,  'App ID'),
            'app_secret'    => self::field('pinterest', 'app_secret',    'string', true,  'App Secret'),
            'access_token'  => self::field('pinterest', 'access_token',  'string', true,  'Access Token'),
            'ad_account_id' => self::field('pinterest', 'ad_account_id', 'string', false, 'Ad Account ID'),
        ];
    }
}
