<?php

declare(strict_types=1);

namespace App\Services\Messaging;

use App\Models\Lead;
use App\Models\LeadMessage;
use App\Models\Tenant;
use App\Settings\MessagingSettings;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * WhatsApp outbound via Twilio's WhatsApp Business API.
 *
 * Pairs with the existing inbound WhatsAppConnector
 * (App\Services\LeadSources\WhatsAppConnector) to give bi-directional
 * WhatsApp support.  Modern leads (especially LATAM, India, MENA) have
 * low email open rates and high WhatsApp engagement — a CRM that can't
 * outbound to WhatsApp loses to one that can.
 *
 * Design choices:
 *   - Uses Twilio's REST API directly (no twilio/sdk dependency —
 *     keeps the script portable on shared hosting that may have
 *     issues with Composer-installed binary deps)
 *   - Persists every send as a LeadMessage row (existing model)
 *     so the conversation timeline shows outbound + inbound side by
 *     side
 *   - Falls back gracefully when credentials are missing — returns
 *     a structured failure rather than throwing, so SendWhatsAppAction
 *     can record the reason in the automation run log
 *
 * Twilio WhatsApp formatting: phone numbers must be prefixed with
 * `whatsapp:` and use E.164 format (`+1...`).  This service handles
 * both prefixing and E.164 normalization internally.
 */
class WhatsAppMessenger
{
    /**
     * @return array{
     *   sent: bool,
     *   message_sid: ?string,
     *   error: ?string,
     *   lead_message_id: ?int,
     * }
     */
    public function send(?Tenant $tenant, Lead $lead, string $body): array
    {
        $settings = $this->loadSettings();
        if (! $settings) {
            return $this->fail((string) __('services/whatsapp_messenger.credentials_missing'));
        }

        $rawPhone = $lead->phone ?? null;
        if (! $rawPhone) {
            return $this->fail((string) __('services/whatsapp_messenger.no_phone'));
        }

        $to = $this->normalizePhone($rawPhone);
        if (! $to) {
            return $this->fail((string) __('services/whatsapp_messenger.invalid_phone_format', ['phone' => $rawPhone]));
        }

        $body = trim($body);
        if ($body === '') {
            return $this->fail((string) __('services/whatsapp_messenger.empty_body'));
        }

        try {
            $response = Http::asForm()
                ->withBasicAuth($settings['account_sid'], $settings['auth_token'])
                ->timeout(15)
                ->post(
                    "https://api.twilio.com/2010-04-01/Accounts/{$settings['account_sid']}/Messages.json",
                    [
                        'From' => 'whatsapp:' . $settings['whatsapp_from'],
                        'To'   => 'whatsapp:' . $to,
                        'Body' => $body,
                    ],
                );

            if (! $response->successful()) {
                $err = $response->json('message') ?? "HTTP {$response->status()}";
                return $this->fail((string) __('services/whatsapp_messenger.twilio_rejected', ['error' => $err]));
            }

            $sid = $response->json('sid');

            $msg = $this->logOutbound($tenant, $lead, $body, $sid);

            return [
                'sent'            => true,
                'message_sid'     => $sid,
                'error'           => null,
                'lead_message_id' => $msg?->id,
            ];
        } catch (\Throwable $e) {
            Log::error('WhatsAppMessenger::send failed', [
                'tenant_id' => $tenant?->id,
                'lead_id'   => $lead->id,
                'error'     => $e->getMessage(),
            ]);
            return $this->fail($e->getMessage());
        }
    }

    /**
     * Reads Twilio credentials from MessagingSettings (Spatie settings).
     * Returns null if any required field is missing.
     */
    protected function loadSettings(): ?array
    {
        try {
            /** @phpstan-ignore-next-line — settings class may not exist on installs that haven't migrated */
            $s = app(MessagingSettings::class);
            $sid    = $s->twilio_account_sid    ?? null;
            $token  = $s->twilio_auth_token     ?? null;
            $from   = $s->twilio_whatsapp_from  ?? null;

            if (! $sid || ! $token || ! $from) {
                return null;
            }

            return [
                'account_sid'    => $sid,
                'auth_token'     => $token,
                'whatsapp_from'  => $this->normalizePhone($from) ?? $from,
            ];
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Normalize a phone string to E.164 (+ followed by 8-15 digits).
     * Returns null if the input can't be coerced into a plausible
     * E.164 number.
     */
    public function normalizePhone(string $raw): ?string
    {
        $raw = trim($raw);
        $digits = preg_replace('/\D+/', '', $raw) ?? '';

        if (strlen($digits) < 8 || strlen($digits) > 15) {
            return null;
        }

        return '+' . $digits;
    }

    protected function logOutbound(?Tenant $tenant, Lead $lead, string $body, ?string $sid): ?LeadMessage
    {
        if (! $tenant) {
            return null;
        }

        try {
            return LeadMessage::create([
                'tenant_id'  => $tenant->id,
                'lead_id'    => $lead->id,
                'channel'    => 'whatsapp',
                'direction'  => 'outbound',
                'body'       => $body,
                'metadata'   => ['twilio_sid' => $sid],
                'sent_at'    => now(),
            ]);
        } catch (\Throwable $e) {
            // Don't fail the send because the timeline write failed.
            Log::warning('WhatsApp outbound logged but timeline write failed', [
                'tenant_id' => $tenant->id,
                'lead_id'   => $lead->id,
                'sid'       => $sid,
                'error'     => $e->getMessage(),
            ]);
            return null;
        }
    }

    protected function fail(string $reason): array
    {
        return [
            'sent'            => false,
            'message_sid'     => null,
            'error'           => $reason,
            'lead_message_id' => null,
        ];
    }
}
