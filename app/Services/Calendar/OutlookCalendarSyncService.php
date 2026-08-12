<?php

declare(strict_types=1);

namespace App\Services\Calendar;

use App\Models\CalendarConnection;
use App\Models\MeetingBooking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Outlook Calendar (Microsoft Graph) 2-way sync.
 *
 * Mirrors GoogleCalendarSyncService's contract — same public method
 * signatures so SyncCalendars + MeetingBookingObserver can route to
 * either provider transparently based on the connection's `provider`
 * column.
 *
 * Microsoft Graph API endpoints:
 *   - Token:     https://login.microsoftonline.com/common/oauth2/v2.0/token
 *   - Authorize: https://login.microsoftonline.com/common/oauth2/v2.0/authorize
 *   - List events: GET  /me/events
 *   - Delta sync:  GET  /me/calendar/events/delta  (incremental)
 *   - Insert:      POST /me/events
 *
 * Direct REST — no microsoft-graph SDK dependency.  Keeps the script
 * portable on shared hosting.
 */
class OutlookCalendarSyncService
{
    public const TOKEN_URL     = 'https://login.microsoftonline.com/common/oauth2/v2.0/token';
    public const AUTHORIZE_URL = 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize';
    public const EVENTS_URL    = 'https://graph.microsoft.com/v1.0/me/events';
    public const DELTA_URL     = 'https://graph.microsoft.com/v1.0/me/calendar/events/delta';
    public const USERINFO_URL  = 'https://graph.microsoft.com/v1.0/me';

    public const SCOPES = [
        'offline_access',
        'Calendars.ReadWrite',
        'User.Read',
    ];

    /**
     * @return array{access_token:string, refresh_token:?string, expires_in:int}|null
     */
    public function exchangeAuthCode(string $code, string $redirectUri): ?array
    {
        $clientId     = (string) config('services.microsoft.client_id', env('MICROSOFT_CLIENT_ID', ''));
        $clientSecret = (string) config('services.microsoft.client_secret', env('MICROSOFT_CLIENT_SECRET', ''));

        if ($clientId === '' || $clientSecret === '') {
            Log::warning('OutlookCalendarSyncService: client credentials not configured');
            return null;
        }

        try {
            $response = Http::asForm()
                ->timeout(15)
                ->post(self::TOKEN_URL, [
                    'code'          => $code,
                    'client_id'     => $clientId,
                    'client_secret' => $clientSecret,
                    'redirect_uri'  => $redirectUri,
                    'grant_type'    => 'authorization_code',
                    'scope'         => implode(' ', self::SCOPES),
                ]);

            if (! $response->successful()) {
                Log::warning('OutlookCalendarSyncService::exchangeAuthCode failed', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return null;
            }

            return $response->json();
        } catch (\Throwable $e) {
            Log::error('OutlookCalendarSyncService::exchangeAuthCode exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    public function ensureFreshToken(CalendarConnection $conn): bool
    {
        if (! $conn->isStale()) return true;

        if (empty($conn->refresh_token)) {
            $conn->markNeedsReauth((string) __('services/calendar_sync.no_refresh_token'));
            return false;
        }

        $clientId     = (string) config('services.microsoft.client_id', env('MICROSOFT_CLIENT_ID', ''));
        $clientSecret = (string) config('services.microsoft.client_secret', env('MICROSOFT_CLIENT_SECRET', ''));

        try {
            $response = Http::asForm()
                ->timeout(15)
                ->post(self::TOKEN_URL, [
                    'client_id'     => $clientId,
                    'client_secret' => $clientSecret,
                    'refresh_token' => $conn->refresh_token,
                    'grant_type'    => 'refresh_token',
                    'scope'         => implode(' ', self::SCOPES),
                ]);

            if (! $response->successful()) {
                $conn->markNeedsReauth((string) __('services/calendar_sync.refresh_failed', ['body' => $response->body()]));
                return false;
            }

            $payload = $response->json();
            // Microsoft rotates the refresh_token on every refresh
            // (different from Google) — persist the new one or the
            // next refresh will fail.
            $conn->forceFill([
                'access_token'  => $payload['access_token']  ?? $conn->access_token,
                'refresh_token' => $payload['refresh_token'] ?? $conn->refresh_token,
                'expires_at'    => Carbon::now()->addSeconds((int) ($payload['expires_in'] ?? 3600)),
                'status'        => CalendarConnection::STATUS_ACTIVE,
            ])->save();

            return true;
        } catch (\Throwable $e) {
            $conn->markError((string) __('services/calendar_sync.token_refresh_exception', ['error' => $e->getMessage()]));
            return false;
        }
    }

    public function pushEvent(CalendarConnection $conn, MeetingBooking $booking): ?string
    {
        if (! $this->ensureFreshToken($conn)) return null;

        $title = $booking->meetingType?->name
            ?: ($booking->guest_name ? (string) __('services/calendar_sync.meeting_with_guest', ['guest' => $booking->guest_name]) : null)
            ?: (string) __('services/calendar_sync.meeting_title_fallback');

        $body = [
            'subject' => $title,
            'body'    => [
                'contentType' => 'text',
                'content'     => (string) ($booking->notes ?? ''),
            ],
            'start' => [
                'dateTime' => $booking->starts_at?->format('Y-m-d\TH:i:s'),
                'timeZone' => $booking->timezone ?? 'UTC',
            ],
            'end' => [
                'dateTime' => $booking->ends_at?->format('Y-m-d\TH:i:s'),
                'timeZone' => $booking->timezone ?? 'UTC',
            ],
        ];

        if (! empty($booking->guest_email)) {
            $body['attendees'] = [[
                'emailAddress' => ['address' => $booking->guest_email, 'name' => $booking->guest_name ?? ''],
                'type'         => 'required',
            ]];
        }

        try {
            $response = Http::withToken($conn->access_token)
                ->timeout(15)
                ->post(self::EVENTS_URL, $body);

            if (! $response->successful()) {
                $conn->markError((string) __('services/calendar_sync.push_failed', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]));
                return null;
            }

            return (string) ($response->json('id') ?? '');
        } catch (\Throwable $e) {
            $conn->markError((string) __('services/calendar_sync.push_exception', ['error' => $e->getMessage()]));
            return null;
        }
    }

    /**
     * Microsoft Graph delta sync.  First call fetches all events;
     * subsequent calls return only changes since the last delta link
     * (saved in sync_token).  Provider returns nextLink (paginate)
     * or deltaLink (cursor for next sync) — we follow nextLink to
     * exhaustion before saving deltaLink.
     */
    public function pullEvents(CalendarConnection $conn): int
    {
        if (! $this->ensureFreshToken($conn)) return 0;

        $url = $conn->sync_token ?: self::DELTA_URL;
        $count = 0;
        $nextDeltaLink = null;
        $iter = 0;

        try {
            // Cap iterations to avoid runaway pagination on first sync.
            while ($url && $iter < 20) {
                $iter++;

                $response = Http::withToken($conn->access_token)
                    ->timeout(20)
                    ->get($url);

                // Delta link expired → clear and let next pass do fresh sync
                if ($response->status() === 410) {
                    $conn->forceFill(['sync_token' => null])->save();
                    return 0;
                }

                if (! $response->successful()) {
                    $conn->markError((string) __('services/calendar_sync.pull_failed', ['status' => $response->status()]));
                    return $count;
                }

                $payload = $response->json();
                $events  = (array) ($payload['value'] ?? []);

                foreach ($events as $event) {
                    if ($this->upsertBookingFromEvent($conn, $event)) {
                        $count++;
                    }
                }

                // Microsoft Graph paginates with @odata.nextLink, then
                // emits @odata.deltaLink on the final page.
                $url = $payload['@odata.nextLink'] ?? null;
                if (! $url) {
                    $nextDeltaLink = $payload['@odata.deltaLink'] ?? null;
                    break;
                }
            }

            $conn->markSyncSuccess($nextDeltaLink ?? $conn->sync_token);
            return $count;
        } catch (\Throwable $e) {
            $conn->markError((string) __('services/calendar_sync.pull_exception', ['error' => $e->getMessage()]));
            return $count;
        }
    }

    /**
     * Map Microsoft Graph event → MeetingBooking.  Idempotent on
     * metadata.outlook_event_id.  Cancelled events flip the local
     * booking's status to 'cancelled' rather than deleting.
     */
    protected function upsertBookingFromEvent(CalendarConnection $conn, array $event): bool
    {
        $eventId = $event['id'] ?? null;
        if (! $eventId) return false;

        $start = $event['start']['dateTime'] ?? null;
        $end   = $event['end']['dateTime']   ?? null;
        if (! $start || ! $end) return false;

        try {
            $existing = MeetingBooking::query()
                ->where('tenant_id', $conn->tenant_id)
                ->whereJsonContains('metadata->outlook_event_id', $eventId)
                ->first();

            $eventNotes = trim(
                ($event['subject'] ?? '') . "\n\n" .
                strip_tags((string) ($event['body']['content'] ?? ''))
            );

            $firstAttendee = $event['attendees'][0]['emailAddress'] ?? null;

            $attrs = [
                'tenant_id'   => $conn->tenant_id,
                'user_id'     => $conn->user_id,
                'guest_name'  => $firstAttendee['name']    ?? null,
                'guest_email' => $firstAttendee['address'] ?? null,
                'starts_at'   => Carbon::parse($start),
                'ends_at'     => Carbon::parse($end),
                'timezone'    => $event['start']['timeZone'] ?? ($event['originalStartTimeZone'] ?? 'UTC'),
                'notes'       => $eventNotes !== '' ? $eventNotes : null,
                'status'      => 'confirmed',
                'metadata'    => array_merge(
                    (array) ($existing?->metadata ?? []),
                    ['outlook_event_id' => $eventId, 'outlook_calendar_id' => $conn->calendar_id ?: 'default'],
                ),
            ];

            // Microsoft Graph delta sends @removed objects for cancelled events.
            if (isset($event['@removed']) || ($event['isCancelled'] ?? false) === true) {
                if ($existing) {
                    $attrs['status'] = 'cancelled';
                    $existing->forceFill($attrs)->save();
                }
                return true;
            }

            if ($existing) {
                $existing->forceFill($attrs)->save();
            } else {
                MeetingBooking::create($attrs);
            }
            return true;
        } catch (\Throwable $e) {
            Log::debug('OutlookCalendarSyncService::upsertBookingFromEvent failed', [
                'tenant_id' => $conn->tenant_id,
                'event_id'  => $eventId,
                'error'     => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function authUrl(string $redirectUri, string $stateToken): string
    {
        $clientId = (string) config('services.microsoft.client_id', env('MICROSOFT_CLIENT_ID', ''));

        $params = http_build_query([
            'client_id'     => $clientId,
            'redirect_uri'  => $redirectUri,
            'response_type' => 'code',
            'scope'         => implode(' ', self::SCOPES),
            'response_mode' => 'query',
            'prompt'        => 'consent',
            'state'         => $stateToken,
        ]);

        return self::AUTHORIZE_URL . '?' . $params;
    }
}
