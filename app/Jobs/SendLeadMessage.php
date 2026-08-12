<?php

namespace App\Jobs;

use App\Events\NewLeadMessage;
use App\Models\Lead;
use App\Models\LeadMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendLeadMessage implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $timeout = 60;

    public function __construct(
        public int $leadId,
        public string $channel,
        public string $body,
        public ?int $userId = null,
        public ?string $mediaUrl = null,
    ) {}

    public function handle(): void
    {
        $lead = Lead::with('tenant')->find($this->leadId);
        if (! $lead) {
            Log::warning('SendLeadMessage: lead not found', ['lead_id' => $this->leadId]);
            return;
        }

        $tenant = $lead->tenant;
        if (! $tenant) {
            Log::warning('SendLeadMessage: tenant missing', ['lead_id' => $lead->id]);
            return;
        }

        $settings = $tenant->getSetting('messaging', []);

        $to     = $this->resolveRecipient($lead);
        $from   = $this->resolveSender($tenant, $settings);
        $status = 'failed';
        $externalId = null;
        $errorMessage = null;

        // Refuse to call the provider when the tenant has the channel
        // disabled in their Messaging Settings page. We still persist
        // a failed LeadMessage row so users can see why nothing sent.
        $enabledKey = "{$this->channel}_enabled";
        if (! ($settings[$enabledKey] ?? false)) {
            // Channel label uses the translator-first lookup that the
            // filament/leads.* keys already provide (Wave A). Falls
            // back to ucfirst() if the key is unknown so the message
            // remains intelligible on incomplete translations.
            $channelLabelKey = 'filament/leads.channel_' . $this->channel;
            $channelLabel    = \Illuminate\Support\Facades\Lang::has($channelLabelKey)
                ? (string) __($channelLabelKey)
                : ucfirst($this->channel);

            $errorMessage = (string) __('jobs/send_lead_message.channel_not_enabled', [
                'channel' => $channelLabel,
            ]);
            Log::info('SendLeadMessage skipped: channel disabled', [
                'lead_id' => $lead->id,
                'channel' => $this->channel,
            ]);
        } else {
            try {
                switch ($this->channel) {
                    case 'whatsapp':
                        [$status, $externalId, $errorMessage] = $this->sendWhatsApp($settings, $to, $this->body);
                        break;

                    case 'sms':
                        [$status, $externalId, $errorMessage] = $this->sendSms($settings, $from, $to, $this->body);
                        break;

                    case 'telegram':
                        [$status, $externalId, $errorMessage] = $this->sendTelegram($settings, $to, $this->body);
                        break;

                    case 'viber':
                        [$status, $externalId, $errorMessage] = $this->sendViber($settings, $to, $this->body);
                        break;

                    default:
                        $errorMessage = (string) __('jobs/send_lead_message.unsupported_channel', [
                            'channel' => $this->channel,
                        ]);
                }
            } catch (\Throwable $e) {
                Log::error('SendLeadMessage exception', [
                    'lead_id' => $lead->id,
                    'channel' => $this->channel,
                    'error'   => $e->getMessage(),
                ]);
                $errorMessage = $e->getMessage();
            }
        }

        $message = LeadMessage::create([
            'tenant_id'       => $lead->tenant_id,
            'lead_id'         => $lead->id,
            'user_id'         => $this->userId,
            'channel'         => $this->channel,
            'direction'       => 'outbound',
            'from_identifier' => $from,
            'to_identifier'   => $to,
            'body'            => $this->body,
            'media_url'       => $this->mediaUrl,
            'status'          => $status,
            'external_id'     => $externalId,
            'error_message'   => $errorMessage ? $this->sanitizeError($errorMessage) : null,
        ]);

        try {
            event(new NewLeadMessage($message));
        } catch (\Throwable $e) {
            Log::warning('NewLeadMessage broadcast failed', ['error' => $e->getMessage()]);
        }
    }

    protected function resolveRecipient(Lead $lead): string
    {
        return match ($this->channel) {
            'telegram' => (string) ($lead->custom_fields['telegram_chat_id'] ?? ''),
            'viber'    => (string) ($lead->custom_fields['viber_id'] ?? $lead->phone),
            default    => (string) ($lead->phone_normalized ?? $lead->phone),
        };
    }

    protected function resolveSender(\App\Models\Tenant $tenant, array $settings): string
    {
        return match ($this->channel) {
            'whatsapp' => (string) ($settings['whatsapp_from'] ?? $settings['whatsapp_phone_number'] ?? ''),
            'sms'      => (string) ($settings['twilio_from'] ?? ''),
            'telegram' => (string) ($settings['telegram_bot_username'] ?? $tenant->name),
            'viber'    => (string) ($settings['viber_from'] ?? $tenant->name),
            default    => '',
        };
    }

    /**
     * Sanitize provider error bodies before persisting them.
     *
     * Providers routinely echo the bearer token, account SID, webhook
     * URL (with token) or signed upload URL in their error responses.
     * We redact known-sensitive patterns and cap length so leaks can't
     * reach tenant users via the conversation UI.
     */
    protected function sanitizeError(string $raw): string
    {
        $redacted = preg_replace(
            [
                '/(Bearer\s+)[A-Za-z0-9\._\-]+/i',
                '/(api[_-]?key["\s:=]+)[A-Za-z0-9\._\-]+/i',
                '/(token["\s:=]+)[A-Za-z0-9\._\-]{8,}/i',
                '/AC[a-f0-9]{32}/i',
                '/(x-signature["\s:=]+)[A-Za-z0-9\+\/=]+/i',
            ],
            ['$1[REDACTED]', '$1[REDACTED]', '$1[REDACTED]', '[REDACTED_SID]', '$1[REDACTED]'],
            $raw
        );

        return mb_substr((string) $redacted, 0, 240);
    }

    /**
     * WhatsApp Business API (Meta Graph).
     * @return array{0:string,1:?string,2:?string} [status, external_id, error]
     */
    protected function sendWhatsApp(array $settings, string $to, string $body): array
    {
        $token   = $settings['whatsapp_business_token'] ?? null;
        $phoneId = $settings['whatsapp_phone_number_id'] ?? null;

        if (! $token || ! $phoneId) {
            return ['failed', null, (string) __('jobs/send_lead_message.whatsapp_missing')];
        }

        $response = Http::withToken($token)
            ->acceptJson()
            ->post("https://graph.facebook.com/v18.0/{$phoneId}/messages", [
                'messaging_product' => 'whatsapp',
                'to'                => $to,
                'type'              => 'text',
                'text'              => ['body' => $body],
            ]);

        if ($response->successful()) {
            $externalId = $response->json('messages.0.id');
            return ['sent', $externalId, null];
        }

        return ['failed', null, (string) __('jobs/send_lead_message.whatsapp_api_error', ['body' => $response->body()])];
    }

    /**
     * Twilio SMS.
     * @return array{0:string,1:?string,2:?string}
     */
    protected function sendSms(array $settings, string $from, string $to, string $body): array
    {
        $sid   = $settings['twilio_sid'] ?? null;
        $token = $settings['twilio_token'] ?? null;

        if (! $sid || ! $token || ! $from) {
            return ['failed', null, (string) __('jobs/send_lead_message.twilio_missing')];
        }

        $response = Http::withBasicAuth($sid, $token)
            ->asForm()
            ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                'From' => $from,
                'To'   => $to,
                'Body' => $body,
            ]);

        if ($response->successful()) {
            return ['sent', $response->json('sid'), null];
        }

        return ['failed', null, (string) __('jobs/send_lead_message.twilio_api_error', ['body' => $response->body()])];
    }

    /**
     * Telegram Bot API sendMessage.
     * @return array{0:string,1:?string,2:?string}
     */
    protected function sendTelegram(array $settings, string $to, string $body): array
    {
        $botToken = $settings['telegram_bot_token'] ?? null;

        if (! $botToken || ! $to) {
            return ['failed', null, (string) __('jobs/send_lead_message.telegram_missing')];
        }

        $response = Http::acceptJson()->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
            'chat_id' => $to,
            'text'    => $body,
        ]);

        if ($response->successful() && $response->json('ok')) {
            return ['sent', (string) $response->json('result.message_id'), null];
        }

        return ['failed', null, (string) __('jobs/send_lead_message.telegram_api_error', ['body' => $response->body()])];
    }

    /**
     * Viber placeholder — returns failed when not configured.
     * @return array{0:string,1:?string,2:?string}
     */
    protected function sendViber(array $settings, string $to, string $body): array
    {
        $authToken = $settings['viber_auth_token'] ?? null;
        $sender    = $settings['viber_sender_name'] ?? 'LeadHub';

        if (! $authToken) {
            return ['failed', null, (string) __('jobs/send_lead_message.viber_missing')];
        }

        $response = Http::acceptJson()
            ->withHeaders(['X-Viber-Auth-Token' => $authToken])
            ->post('https://chatapi.viber.com/pa/send_message', [
                'receiver' => $to,
                'type'     => 'text',
                'sender'   => ['name' => $sender],
                'text'     => $body,
            ]);

        if ($response->successful() && ($response->json('status') === 0)) {
            return ['sent', (string) $response->json('message_token'), null];
        }

        return ['failed', null, (string) __('jobs/send_lead_message.viber_api_error', ['body' => $response->body()])];
    }
}
