<?php

namespace App\Http\Controllers\Api;

use App\Events\NewLeadMessage;
use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadMessage;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MessagingWebhookController extends Controller
{
    public function handle(Request $request, string $channel, string $tenant, string $token): JsonResponse
    {
        $tenantModel = Tenant::find($tenant);
        if (! $tenantModel) {
            return response()->json(['ok' => false, 'error' => 'tenant not found'], 200);
        }

        // Verify token against tenant's messaging settings
        $settings     = $tenantModel->getSetting('messaging', []);
        $expectedKey  = "{$channel}_webhook_token";
        $expectedToken = $settings[$expectedKey] ?? null;

        if (! $expectedToken || ! hash_equals((string) $expectedToken, (string) $token)) {
            Log::warning('Messaging webhook: token mismatch', [
                'channel' => $channel,
                'tenant'  => $tenant,
            ]);
            return response()->json(['ok' => true], 200);
        }

        // Bind current tenant so BelongsToTenant resolves it
        app()->instance('current_tenant', $tenantModel);

        $parsed = match ($channel) {
            'whatsapp' => $this->parseWhatsApp($request),
            'sms'      => $this->parseTwilioSms($request),
            'telegram' => $this->parseTelegram($request),
            'viber'    => $this->parseViber($request),
            default    => [],
        };

        foreach ($parsed as $entry) {
            $this->storeInbound($tenantModel, $channel, $entry);
        }

        return response()->json(['ok' => true]);
    }

    protected function storeInbound(Tenant $tenant, string $channel, array $entry): void
    {
        $from = (string) ($entry['from'] ?? '');
        $body = $entry['body'] ?? null;
        $to   = (string) ($entry['to'] ?? '');
        $externalId = $entry['external_id'] ?? null;
        $mediaUrl   = $entry['media_url'] ?? null;
        $mediaType  = $entry['media_type'] ?? null;

        if ($from === '') {
            return;
        }

        $lead = $this->findOrCreateLead($tenant, $channel, $from, $entry);
        if (! $lead) {
            return;
        }

        $message = LeadMessage::create([
            'tenant_id'       => $tenant->id,
            'lead_id'         => $lead->id,
            'user_id'         => null,
            'channel'         => $channel,
            'direction'       => 'inbound',
            'from_identifier' => $from,
            'to_identifier'   => $to,
            'body'            => $body,
            'media_url'       => $mediaUrl,
            'media_type'      => $mediaType,
            'status'          => 'delivered',
            'external_id'     => $externalId,
        ]);

        try {
            event(new NewLeadMessage($message));
        } catch (\Throwable $e) {
            Log::warning('NewLeadMessage broadcast failed (inbound)', ['error' => $e->getMessage()]);
        }
    }

    protected function findOrCreateLead(Tenant $tenant, string $channel, string $from, array $entry): ?Lead
    {
        // For telegram, the "from" is the chat_id — store in custom_fields
        if ($channel === 'telegram') {
            // Indexed-column lookup first (migration 2026_05_02_000003
            // added a STORED generated column extracted from
            // custom_fields.telegram_chat_id, indexed for the b-tree).
            // Falls back to whereJsonContains for the brief window
            // between composer install and `php artisan migrate`
            // adding the column — same pattern as the Stripe customer
            // id index from earlier this review cycle.
            $lead = null;
            try {
                $lead = Lead::where('tenant_id', $tenant->id)
                    ->where('telegram_chat_id', (string) $from)
                    ->first();
            } catch (\Throwable) {
                // Generated column not yet present — fall through.
            }
            if (! $lead) {
                $lead = Lead::where('tenant_id', $tenant->id)
                    ->where(function ($q) use ($from) {
                        $q->whereJsonContains('custom_fields->telegram_chat_id', $from)
                          ->orWhereJsonContains('custom_fields->telegram_chat_id', (int) $from);
                    })
                    ->first();
            }

            if (! $lead) {
                $lead = Lead::create([
                    'tenant_id'     => $tenant->id,
                    'source'        => 'telegram',
                    'status'        => 'new',
                    'first_name'    => $entry['first_name'] ?? null,
                    'last_name'     => $entry['last_name'] ?? null,
                    'custom_fields' => ['telegram_chat_id' => $from],
                ]);
            }
            return $lead;
        }

        // For phone-based channels (whatsapp, sms, viber) — match by normalized phone.
        $normalized = preg_replace('/\D/', '', $from) ?: null;
        if (! $normalized) {
            return null;
        }

        $lead = Lead::where('tenant_id', $tenant->id)
            ->where('phone_normalized', $normalized)
            ->first();

        if ($lead) {
            return $lead;
        }

        return Lead::create([
            'tenant_id' => $tenant->id,
            'source'    => $channel === 'whatsapp' ? 'whatsapp' : ($channel === 'viber' ? 'viber' : 'web_form'),
            'status'    => 'new',
            'phone'     => $from,
        ]);
    }

    /**
     * WhatsApp Business API webhook.
     * Body: { entry: [{ changes: [{ value: { messages: [...], contacts: [...] } }] }] }
     */
    protected function parseWhatsApp(Request $request): array
    {
        $out = [];
        foreach ($request->input('entry', []) as $entry) {
            foreach ($entry['changes'] ?? [] as $change) {
                $value = $change['value'] ?? [];
                $contacts = $value['contacts'] ?? [];
                foreach ($value['messages'] ?? [] as $msg) {
                    $contact = $contacts[0] ?? [];
                    $out[] = [
                        'from'        => $msg['from'] ?? '',
                        'to'          => $value['metadata']['display_phone_number'] ?? '',
                        'body'        => $msg['text']['body'] ?? null,
                        'external_id' => $msg['id'] ?? null,
                        'media_url'   => $msg['image']['link'] ?? $msg['video']['link'] ?? $msg['audio']['link'] ?? null,
                        'media_type'  => $msg['type'] ?? null,
                        'first_name'  => $contact['profile']['name'] ?? null,
                    ];
                }
            }
        }
        return $out;
    }

    /**
     * Twilio SMS webhook (form-encoded).
     */
    protected function parseTwilioSms(Request $request): array
    {
        $from = $request->input('From');
        if (! $from) {
            return [];
        }

        return [[
            'from'        => $from,
            'to'          => $request->input('To', ''),
            'body'        => $request->input('Body'),
            'external_id' => $request->input('MessageSid'),
            'media_url'   => $request->input('MediaUrl0'),
            'media_type'  => $request->input('MediaContentType0'),
        ]];
    }

    /**
     * Telegram Bot webhook.
     * Body: { message: { chat: {id, first_name, last_name}, text, ... } }
     */
    protected function parseTelegram(Request $request): array
    {
        $msg = $request->input('message') ?? $request->input('edited_message');
        if (! $msg) {
            return [];
        }

        $chat = $msg['chat'] ?? [];
        return [[
            'from'        => (string) ($chat['id'] ?? ''),
            'to'          => '',
            'body'        => $msg['text'] ?? null,
            'external_id' => (string) ($msg['message_id'] ?? ''),
            'first_name'  => $chat['first_name'] ?? null,
            'last_name'   => $chat['last_name'] ?? null,
        ]];
    }

    /**
     * Viber webhook.
     */
    protected function parseViber(Request $request): array
    {
        if ($request->input('event') !== 'message') {
            return [];
        }

        $sender = $request->input('sender', []);
        $msg    = $request->input('message', []);

        return [[
            'from'        => (string) ($sender['id'] ?? ''),
            'to'          => '',
            'body'        => $msg['text'] ?? null,
            'external_id' => (string) $request->input('message_token', ''),
            'first_name'  => $sender['name'] ?? null,
        ]];
    }
}
