<?php

namespace App\Services\LeadSources;

use App\Models\LeadSourceConnection;
use App\Models\WebhookLog;
use Illuminate\Http\Request;

class TelegramConnector extends BaseConnector
{
    public function verifyWebhook(Request $request, LeadSourceConnection $connection): bool
    {
        $credentials = $connection->credentials ?? [];
        $botToken    = $credentials['bot_token'] ?? null;
        $secretToken = $credentials['webhook_secret'] ?? null;

        if (! $secretToken) {
            return true;
        }

        $incoming = $request->header('X-Telegram-Bot-Api-Secret-Token');
        return $incoming === $secretToken;
    }

    public function handleWebhook(Request $request, LeadSourceConnection $connection, WebhookLog $log): array
    {
        $body     = json_decode($request->getContent(), true) ?? [];
        $leads    = [];
        $keywords = array_map('strtolower', $connection->settings['qualification_keywords'] ?? []);

        $message = $body['message'] ?? $body['channel_post'] ?? null;

        if (! $message) {
            return $leads;
        }

        $from = $message['from'] ?? [];
        $text = $message['text'] ?? '';

        $isStart    = $text === '/start';
        $hasKeyword = false;

        if (! $isStart && ! empty($keywords)) {
            foreach ($keywords as $kw) {
                if (stripos($text, $kw) !== false) {
                    $hasKeyword = true;
                    break;
                }
            }
        }

        if (! $isStart && ! $hasKeyword && ! empty($keywords)) {
            return $leads;
        }

        $lead = $this->createLead([
            'source_id'  => (string) ($from['id'] ?? $message['message_id'] ?? null),
            'first_name' => $from['first_name'] ?? null,
            'last_name'  => $from['last_name'] ?? null,
            'email'      => null,
            'phone'      => null,
            'raw_data'   => $body,
            'custom_fields' => [
                'username' => $from['username'] ?? null,
                'message'  => $text,
            ],
        ], $connection, $log);

        if ($lead) {
            $leads[] = $lead;
        }

        return $leads;
    }

    public function getConnectionFormSchema(): array
    {
        return [
            'bot_token'              => self::field('telegram', 'bot_token',              'string', true,  'Bot Token'),
            'webhook_secret'         => self::field('telegram', 'webhook_secret',         'string', false, 'Webhook Secret'),
            'qualification_keywords' => self::field('telegram', 'qualification_keywords', 'array',  false, 'Qualification Keywords'),
        ];
    }
}
