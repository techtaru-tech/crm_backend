<?php

namespace App\Services\LeadSources;

use App\Models\LeadSourceConnection;
use App\Models\WebhookLog;
use Illuminate\Http\Request;

class TwitterConnector extends BaseConnector
{
    public function verifyWebhook(Request $request, LeadSourceConnection $connection): bool
    {
        $credentials = $connection->credentials ?? [];
        $secret      = $credentials['consumer_secret'] ?? null;

        if (! $secret) {
            return true;
        }

        $signature = $request->header('X-Twitter-Webhooks-Signature');
        if (! $signature) {
            return false;
        }

        $payload  = $request->getContent();
        $expected = 'sha256=' . base64_encode(hash_hmac('sha256', $payload, $secret, true));

        return hash_equals($expected, $signature);
    }

    public function handleWebhook(Request $request, LeadSourceConnection $connection, WebhookLog $log): array
    {
        $body  = json_decode($request->getContent(), true) ?? [];
        $leads = [];

        if ($request->has('crc_token')) {
            return $leads;
        }

        $dmEvents = $body['direct_message_events'] ?? [];
        $users    = collect($body['users'] ?? [])->keyBy('id')->all();

        foreach ($dmEvents as $event) {
            if (($event['type'] ?? '') !== 'message_create') {
                continue;
            }

            $message   = $event['message_create'] ?? [];
            $senderId  = $message['sender_id'] ?? null;
            $text      = $message['message_data']['text'] ?? '';
            $sender    = $users[$senderId] ?? [];

            if (! $senderId) {
                continue;
            }

            $nameParts = $this->parseFullName($sender['name'] ?? '');

            $lead = $this->createLead([
                'source_id'  => $event['id'] ?? $senderId,
                'first_name' => $nameParts['first_name'],
                'last_name'  => $nameParts['last_name'],
                'email'      => null,
                'phone'      => null,
                'raw_data'   => $event,
                'custom_fields' => [
                    'twitter_handle' => $sender['screen_name'] ?? null,
                    'message'        => $text,
                ],
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
            'consumer_key'    => self::field('twitter', 'consumer_key',    'string', true, 'Consumer Key (API Key)'),
            'consumer_secret' => self::field('twitter', 'consumer_secret', 'string', true, 'Consumer Secret'),
            'access_token'    => self::field('twitter', 'access_token',    'string', true, 'Access Token'),
            'access_secret'   => self::field('twitter', 'access_secret',   'string', true, 'Access Token Secret'),
        ];
    }
}
