<?php

declare(strict_types=1);

namespace App\Services\Calendar;

use App\Models\CalendarConnection;
use App\Models\MeetingBooking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Google Calendar 2-way sync.
 *
 * Push: when a MeetingBooking is created internally, post the event
 * to the user's primary Google Calendar.
 *
 * Pull: every 15 min via cron, fetch new/updated/cancelled events
 * since the last syncToken and surface as MeetingBooking rows.
 *
 * Token lifecycle:
 *   - exchangeAuthCode() runs on the OAuth callback to swap an
 *     authorization code for access + refresh tokens
 *   - refreshAccessToken() runs proactively before each API call
 *     when the access token is stale (within 60s of expiry)
 *
 * Direct REST — no google/apiclient SDK dependency.  Keeps the
 * script portable on shared hosting where binary-extension Composer
 * deps can fail.
 */
class GoogleCalendarSyncService
{
    public const TOKEN_URL  = 'https://oauth2.googleapis.com/token';
    public const EVENTS_URL = 'https://www.googleapis.com/calendar/v3/calendars/%s/events';

    public const SCOPES = [
        'https://www.googleapis.com/auth/calendar.events',
        'https://www.googleapis.com/auth/userinfo.email',
    ];

    /**
     * Exchange an authorization code (from the OAuth callback) for
     * access + refresh tokens.  Returns the raw token payload —
     * caller persists the relevant fields onto a CalendarConnection.
     *
     * @return array{access_token:string, refresh_token:?string, expires_in:int, scope:string, token_type:string}|null
     */
    public function exchangeAuthCode(string $code, string $redirectUri): ?array
    {
        $clientId     = (string) config('services.google.client_id', env('GOOGLE_CLIENT_ID', ''));
        $clientSecret = (string) config('services.google.client_secret', env('GOOGLE_CLIENT_SECRET', ''));

        if ($clientId === '' || $clientSecret === '') {
            Log::warning('GoogleCalendarSyncService: client credentials not configured');
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
                ]);

            if (! $response->successful()) {
                Log::warning('GoogleCalendarSyncService::exchangeAuthCode failed', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return null;
            }

            return $response->json();
        } catch (\Throwable $e) {
            Log::error('GoogleCalendarSyncService::exchangeAuthCode exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Ensure the connection's access_token is fresh.  Refreshes if
     * stale (within 60s of expiry).  Marks needs_reauth if the
     * refresh fails (likely revoked).  Returns true when usable.
     */
    public function ensureFreshToken(CalendarConnection $conn): bool
    {
        if (! $conn->isStale()) {
            return true;
        }

        if (empty($conn->refresh_token)) {
            $conn->markNeedsReauth((string) __('services/calendar_sync.no_refresh_token'));
            return false;
        }

        $clientId     = (string) config('services.google.client_id', env('GOOGLE_CLIENT_ID', ''));
        $clientSecret = (string) config('services.google.client_secret', env('GOOGLE_CLIENT_SECRET', ''));

        try {
            $response = Http::asForm()
                ->timeout(15)
                ->post(self::TOKEN_URL, [
                    'client_id'     => $clientId,
                    'client_secret' => $clientSecret,
                    'refresh_token' => $conn->refresh_token,
                    'grant_type'    => 'refresh_token',
                ]);

            if (! $response->successful()) {
                $conn->markNeedsReauth((string) __('services/calendar_sync.refresh_failed', ['body' => $response->body()]));
                return false;
            }

            $payload = $response->json();
            $conn->forceFill([
                'access_token' => $payload['access_token'] ?? $conn->access_token,
                'expires_at'   => Carbon::now()->addSeconds((int) ($payload['expires_in'] ?? 3600)),
                'status'       => CalendarConnection::STATUS_ACTIVE,
            ])->save();

            return true;
        } catch (\Throwable $e) {
            $conn->markError((string) __('services/calendar_sync.token_refresh_exception', ['error' => $e->getMessage()]));
            return false;
        }
    }

    /**
     * Push a MeetingBooking to the user's external calendar.  Returns
     * the created Google event id on success, null on failure.
     */
    public function pushEvent(CalendarConnection $conn, MeetingBooking $booking): ?string
    {
        if (! $this->ensureFreshToken($conn)) return null;

        $calendarId = $conn->calendar_id ?: 'primary';
        $url = sprintf(self::EVENTS_URL, urlencode($calendarId));

        // MeetingBooking schema doesn't have a `title` column — the
        // human-readable name lives on the related MeetingType.  Fall
        // back through the chain so manual bookings (no meeting_type)
        // and imports (no guest_name) still produce sensible labels.
        $title = $booking->meetingType?->name
            ?: ($booking->guest_name ? (string) __('services/calendar_sync.meeting_with_guest', ['guest' => $booking->guest_name]) : null)
            ?: (string) __('services/calendar_sync.meeting_title_fallback');

        $body = [
            'summary'     => $title,
            'description' => (string) ($booking->notes ?? ''),
            'start'       => ['dateTime' => $booking->starts_at?->toIso8601String(), 'timeZone' => $booking->timezone ?? 'UTC'],
            'end'         => ['dateTime' => $booking->ends_at?->toIso8601String(),   'timeZone' => $booking->timezone ?? 'UTC'],
        ];

        $attendeeEmail = $booking->guest_email ?? null;
        if (! empty($attendeeEmail)) {
            $body['attendees'] = [['email' => $attendeeEmail]];
        }

        try {
            $response = Http::withToken($conn->access_token)
                ->timeout(15)
                ->post($url, $body);

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
     * Pull events incrementally using the saved sync_token (Google's
     * incremental-sync cursor).  Returns the count of events
     * processed; 0 means nothing changed since last poll.
     *
     * On 410 GONE (sync_token expired), clears the cursor and
     * triggers a full resync on the next pass.
     */
    public function pullEvents(CalendarConnection $conn): int
    {
        if (! $this->ensureFreshToken($conn)) return 0;

        $calendarId = $conn->calendar_id ?: 'primary';
        $url = sprintf(self::EVENTS_URL, urlencode($calendarId));

        $params = [];
        if (! empty($conn->sync_token)) {
            $params['syncToken'] = $conn->sync_token;
        } else {
            // Fresh sync — only fetch events from now to 90 days out.
            $params['timeMin'] = Carbon::now()->subDays(7)->toRfc3339String();
            $params['timeMax'] = Carbon::now()->addDays(90)->toRfc3339String();
        }

        try {
            $response = Http::withToken($conn->access_token)
                ->timeout(20)
                ->get($url, $params);

            // Sync token expired → clear and let the next pass do a fresh sync.
            if ($response->status() === 410) {
                $conn->forceFill(['sync_token' => null])->save();
                return 0;
            }

            if (! $response->successful()) {
                $conn->markError((string) __('services/calendar_sync.pull_failed', ['status' => $response->status()]));
                return 0;
            }

            $payload = $response->json();
            $events  = (array) ($payload['items'] ?? []);
            $count   = 0;

            foreach ($events as $event) {
                if ($this->upsertBookingFromEvent($conn, $event)) {
                    $count++;
                }
            }

            $conn->markSyncSuccess($payload['nextSyncToken'] ?? null);
            return $count;
        } catch (\Throwable $e) {
            $conn->markError((string) __('services/calendar_sync.pull_exception', ['error' => $e->getMessage()]));
            return 0;
        }
    }

    /**
     * Create or update a MeetingBooking from a Google event payload.
     * Skips events the user cancelled (Google sends `status=cancelled`
     * — we soft-mark the local booking rather than deleting).
     *
     * Idempotent — keyed on metadata.google_event_id.
     */
    protected function upsertBookingFromEvent(CalendarConnection $conn, array $event): bool
    {
        $eventId = $event['id'] ?? null;
        if (! $eventId) return false;

        $start = $event['start']['dateTime'] ?? $event['start']['date'] ?? null;
        $end   = $event['end']['dateTime']   ?? $event['end']['date']   ?? null;
        if (! $start || ! $end) return false;

        try {
            $existing = MeetingBooking::query()
                ->where('tenant_id', $conn->tenant_id)
                ->whereJsonContains('metadata->google_event_id', $eventId)
                ->first();

            // Map Google event → MeetingBooking columns.
            // MeetingBooking has no `title` column — the summary
            // string lives in `notes` alongside any free-text desc
            // so the host sees both in the local timeline.
            $eventNotes = trim(($event['summary'] ?? '') . "\n\n" . ($event['description'] ?? ''));

            $attrs = [
                'tenant_id'   => $conn->tenant_id,
                'user_id'     => $conn->user_id,
                'guest_name'  => $event['attendees'][0]['displayName'] ?? null,
                'guest_email' => $event['attendees'][0]['email']       ?? null,
                'starts_at'   => Carbon::parse($start),
                'ends_at'     => Carbon::parse($end),
                'timezone'    => $event['start']['timeZone'] ?? 'UTC',
                'notes'       => $eventNotes !== '' ? $eventNotes : null,
                'status'      => 'confirmed',
                'metadata'    => array_merge(
                    (array) ($existing?->metadata ?? []),
                    ['google_event_id' => $eventId, 'google_calendar_id' => $conn->calendar_id ?: 'primary'],
                ),
            ];

            // Cancellations from Google → mark cancelled locally
            if (($event['status'] ?? '') === 'cancelled') {
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
            Log::debug('GoogleCalendarSyncService::upsertBookingFromEvent failed', [
                'tenant_id' => $conn->tenant_id,
                'event_id'  => $eventId,
                'error'     => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Build the Google OAuth consent URL the user is redirected to
     * when they click "Connect Google Calendar".
     */
    public function authUrl(string $redirectUri, string $stateToken): string
    {
        $clientId = (string) config('services.google.client_id', env('GOOGLE_CLIENT_ID', ''));

        $params = http_build_query([
            'client_id'     => $clientId,
            'redirect_uri'  => $redirectUri,
            'response_type' => 'code',
            'scope'         => implode(' ', self::SCOPES),
            'access_type'   => 'offline',
            'prompt'        => 'consent',
            'state'         => $stateToken,
        ]);

        return 'https://accounts.google.com/o/oauth2/v2/auth?' . $params;
    }
}
