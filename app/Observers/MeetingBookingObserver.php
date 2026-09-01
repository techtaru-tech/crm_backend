<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\CalendarConnection;
use App\Models\MeetingBooking;
use App\Services\Calendar\GoogleCalendarSyncService;
use App\Services\Calendar\OutlookCalendarSyncService;
use Illuminate\Support\Facades\Log;

/**
 * Auto-push internal meeting bookings to the host's connected
 * external calendar (Google).  Closes the OUTBOUND half of the
 * 2-way calendar sync — pull is handled by SyncCalendars cron.
 *
 * Idempotent — checks for an existing google_event_id in the
 * booking's metadata before pushing, so re-creating an existing
 * booking (rare but possible during imports) doesn't double-book.
 *
 * Failures swallowed — calendar push is a side effect; never let
 * it block local booking creation.
 */
class MeetingBookingObserver
{
    public function __construct(
        protected GoogleCalendarSyncService $google,
        protected OutlookCalendarSyncService $outlook,
    ) {
    }

    public function created(MeetingBooking $booking): void
    {
        $this->pushToCalendar($booking);
        $this->logToLeadTimeline($booking, 'scheduled');
    }

    public function updated(MeetingBooking $booking): void
    {
        // Only re-push when schedule or status actually changed.
        if (! $booking->wasChanged(['starts_at', 'ends_at', 'status'])) {
            return;
        }
        $this->pushToCalendar($booking);

        if ($booking->wasChanged('status') && $booking->status === 'cancelled') {
            $this->logToLeadTimeline($booking, 'cancelled');
        } elseif ($booking->wasChanged(['starts_at', 'ends_at'])) {
            $this->logToLeadTimeline($booking, 'rescheduled');
        }
    }

    /**
     * Mirror the booking onto the lead's activity timeline (spec §10).
     *
     * Bookings can arrive from the public booking page and from calendar
     * sync, i.e. with no authenticated user and no tenant bound — so the lead
     * is resolved without global scopes and the whole thing is wrapped: a
     * timeline entry must never be the reason a booking fails to save.
     */
    protected function logToLeadTimeline(MeetingBooking $booking, string $event): void
    {
        if (! $booking->lead_id) {
            return;
        }

        try {
            $lead = \App\Models\Lead::withoutGlobalScopes()->find($booking->lead_id);
            if (! $lead) {
                return;
            }

            $label = $booking->meetingType?->name
                ?? __('filament/leads.meeting_generic_label');

            $when = $booking->starts_at
                ? $booking->starts_at->format('d M Y, H:i')
                : null;

            app(\App\Services\LeadActivityService::class)
                ->logMeeting($lead, $event, (string) $label, $when, $booking->user_id);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('MeetingBookingObserver: timeline log failed', [
                'booking_id' => $booking->id,
                'event'      => $event,
                'error'      => $e->getMessage(),
            ]);
        }
    }

    /**
     * Resolve the user's preferred calendar provider from
     * users.preferences JSON.  Defaults to Google when unset.
     */
    protected function resolvePreferredProvider(int $userId): string
    {
        try {
            $user = \App\Models\User::find($userId);
            $pref = $user?->preferences['calendar']['preferred_provider'] ?? null;
            if ($pref === CalendarConnection::PROVIDER_OUTLOOK) {
                return CalendarConnection::PROVIDER_OUTLOOK;
            }
        } catch (\Throwable) {
            // Fall through to default
        }
        return CalendarConnection::PROVIDER_GOOGLE;
    }

    protected function pushToCalendar(MeetingBooking $booking): void
    {
        if (! $booking->user_id) return;

        $metadata = (array) ($booking->metadata ?? []);

        // For v1, skip updates if the booking already has an event id
        // for either provider — provider's PATCH flow is a separate
        // code path.  An existing event id means the event is already
        // on the calendar; the user can edit it there directly.
        if (isset($metadata['google_event_id']) || isset($metadata['outlook_event_id'])) {
            return;
        }

        // Resolve the host's preferred provider (set on the user's
        // settings/preferences JSON).  Falls back to Google when
        // unset — same as before this edit, just deterministic.
        $preferred = $this->resolvePreferredProvider($booking->user_id);

        // Try the preferred provider first; if that user doesn't have
        // an active connection of that type, fall through to whatever
        // active connection they DO have.  Calendar push is best-effort
        // — never worth bouncing the booking creation over.
        $conn = CalendarConnection::query()
            ->active()
            ->where('user_id', $booking->user_id)
            ->orderByRaw('CASE WHEN provider = ? THEN 0 ELSE 1 END', [$preferred])
            ->orderBy('id')
            ->first();

        if (! $conn) return;

        try {
            $eventId = $conn->provider === CalendarConnection::PROVIDER_OUTLOOK
                ? $this->outlook->pushEvent($conn, $booking)
                : $this->google->pushEvent($conn, $booking);

            if ($eventId) {
                if ($conn->provider === CalendarConnection::PROVIDER_OUTLOOK) {
                    $metadata['outlook_event_id']    = $eventId;
                    $metadata['outlook_calendar_id'] = $conn->calendar_id ?: 'default';
                } else {
                    $metadata['google_event_id']    = $eventId;
                    $metadata['google_calendar_id'] = $conn->calendar_id ?: 'primary';
                }
                $booking->forceFill(['metadata' => $metadata])->saveQuietly();
            }
        } catch (\Throwable $e) {
            Log::warning('MeetingBookingObserver: push failed', [
                'booking_id' => $booking->id,
                'user_id'    => $booking->user_id,
                'provider'   => $conn->provider,
                'error'      => $e->getMessage(),
            ]);
        }
    }
}
