<?php

namespace App\Http\Controllers;

use App\Mail\MeetingBookedMail;
use App\Mail\MeetingCancelledMail;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\MeetingAvailabilityRule;
use App\Models\MeetingBooking;
use App\Models\MeetingType;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class PublicBookingController extends Controller
{
    /**
     * Fetch a MeetingType by user_id + slug. Bypass the tenant global scope since
     * this is a public endpoint (no auth context = fail-closed).
     */
    protected function findMeetingType(string $userId, string $slug): MeetingType
    {
        $user = User::findOrFail((int) $userId);

        $type = MeetingType::withoutGlobalScope('tenant')
            ->where('user_id', $user->id)
            ->where('tenant_id', $user->tenant_id)
            ->where('slug', $slug)
            ->where('is_active', true)
            ->with(['user', 'pipeline', 'pipelineStage'])
            ->first();

        abort_if(! $type, 404);

        return $type;
    }

    public function show(string $userId, string $slug): View|Response
    {
        $meetingType = $this->findMeetingType($userId, $slug);

        return view('public.booking.show', [
            'meetingType' => $meetingType,
            'host'        => $meetingType->user,
        ]);
    }

    /**
     * JSON availability for a given ?date=YYYY-MM-DD. Returns slots [{ start: "HH:MM", iso: "..." }].
     */
    public function availability(string $userId, string $slug, Request $request): JsonResponse
    {
        $meetingType = $this->findMeetingType($userId, $slug);
        $date = $request->query('date');

        $validator = Validator::make(['date' => $date], ['date' => 'required|date_format:Y-m-d']);
        if ($validator->fails()) {
            return response()->json(['slots' => [], 'error' => __('public_booking.invalid_date')], 422);
        }

        $tz         = $request->query('timezone') ?: 'UTC';
        $host       = $meetingType->user;
        $dayCarbon  = Carbon::parse($date, $tz)->startOfDay();
        $dayOfWeek  = (int) $dayCarbon->dayOfWeek;
        $duration   = (int) $meetingType->duration_minutes;
        $buffer     = (int) $meetingType->buffer_minutes;
        $slotStep   = max(15, $duration); // step by duration, min 15 min

        // Gate: future_days_max
        $nowLocal = Carbon::now($tz);
        if ($dayCarbon->lt($nowLocal->copy()->startOfDay())) {
            return response()->json(['slots' => []]);
        }
        if ($dayCarbon->gt($nowLocal->copy()->addDays((int) $meetingType->future_days_max))) {
            return response()->json(['slots' => []]);
        }

        // Advance notice
        $earliestStart = $nowLocal->copy()->addHours((int) $meetingType->advance_notice_hours);

        // Rules for this weekday
        $rules = MeetingAvailabilityRule::where('user_id', $host->id)
            ->where('day_of_week', $dayOfWeek)
            ->orderBy('start_time')
            ->get();

        if ($rules->isEmpty()) {
            return response()->json(['slots' => []]);
        }

        // Existing bookings for that day (in host's scope)
        $rangeStartUtc = $dayCarbon->copy()->utc();
        $rangeEndUtc   = $dayCarbon->copy()->addDay()->utc();

        $existing = MeetingBooking::withoutGlobalScope('tenant')
            ->where('user_id', $host->id)
            ->where('status', 'confirmed')
            ->whereBetween('starts_at', [$rangeStartUtc, $rangeEndUtc])
            ->get(['starts_at', 'ends_at']);

        $slots = [];

        foreach ($rules as $rule) {
            [$sH, $sM] = array_pad(explode(':', (string) $rule->start_time), 2, 0);
            [$eH, $eM] = array_pad(explode(':', (string) $rule->end_time), 2, 0);

            $cursor = $dayCarbon->copy()->setTime((int) $sH, (int) $sM);
            $end    = $dayCarbon->copy()->setTime((int) $eH, (int) $eM);

            while ($cursor->copy()->addMinutes($duration)->lte($end)) {
                $slotStart = $cursor->copy();
                $slotEnd   = $cursor->copy()->addMinutes($duration);

                // Advance notice gate
                if ($slotStart->lt($earliestStart)) {
                    $cursor->addMinutes($slotStep + $buffer);
                    continue;
                }

                // Overlap with existing bookings
                $overlaps = false;
                foreach ($existing as $b) {
                    $bStart = Carbon::parse($b->starts_at)->setTimezone($tz);
                    $bEnd   = Carbon::parse($b->ends_at)->setTimezone($tz);
                    if ($slotStart->lt($bEnd) && $slotEnd->gt($bStart)) {
                        $overlaps = true;
                        break;
                    }
                }
                if (! $overlaps) {
                    // translatedFormat() localizes the AM/PM token ('A') per
                    // current locale (e.g. 'πμ/μμ' for Greek, 'AM/PM' for English).
                    $slots[] = [
                        'label' => $slotStart->translatedFormat('g:i A'),
                        'start' => $slotStart->format('H:i'),
                        'iso'   => $slotStart->toIso8601String(),
                    ];
                }

                $cursor->addMinutes($slotStep + $buffer);
            }
        }

        return response()->json(['slots' => $slots]);
    }

    public function book(string $userId, string $slug, Request $request): JsonResponse
    {
        $meetingType = $this->findMeetingType($userId, $slug);
        $host        = $meetingType->user;

        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:150',
            'email'       => 'required|email|max:255',
            'phone'       => 'nullable|string|max:50',
            'starts_at'   => 'required|date',
            'timezone'    => 'required|string|max:64',
            'notes'       => 'nullable|string|max:2000',
            'utm_source'  => 'nullable|string|max:100',
            'utm_medium'  => 'nullable|string|max:100',
            'utm_campaign'=> 'nullable|string|max:200',
            'utm_content' => 'nullable|string|max:200',
            'utm_term'    => 'nullable|string|max:200',
            '__hp'        => 'nullable|string|max:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Honeypot
        if ($request->filled('__hp')) {
            return response()->json(['success' => true, 'spam' => true]);
        }

        $tz = $request->input('timezone');
        try {
            $starts = Carbon::parse($request->input('starts_at'), $tz)->utc();
        } catch (\Throwable) {
            return response()->json(['success' => false, 'message' => __('messages.booking_invalid_datetime')], 422);
        }

        $nowUtc = Carbon::now('UTC');
        if ($starts->lt($nowUtc->copy()->addHours((int) $meetingType->advance_notice_hours))) {
            return response()->json(['success' => false, 'message' => __('messages.booking_time_advance_notice_failed')], 422);
        }
        if ($starts->gt($nowUtc->copy()->addDays((int) $meetingType->future_days_max))) {
            return response()->json(['success' => false, 'message' => __('messages.booking_time_too_far_future')], 422);
        }

        $ends = $starts->copy()->addMinutes((int) $meetingType->duration_minutes);

        $email = strtolower(trim($request->input('email')));
        $parts     = preg_split('/\s+/', trim((string) $request->input('name')), 2);
        $firstName = $parts[0] ?? '';
        $lastName  = $parts[1] ?? null;

        // Atomic: lock the host's confirmed bookings in the target window,
        // re-check for overlap, then insert the lead + booking in one pass.
        // Prevents two simultaneous requests both passing the conflict
        // check and double-booking the same slot.
        try {
            [$booking, $lead] = \Illuminate\Support\Facades\DB::transaction(function () use (
                $host, $meetingType, $starts, $ends, $email, $firstName, $lastName, $request, $tz
            ) {
                $conflict = MeetingBooking::withoutGlobalScope('tenant')
                    ->where('user_id', $host->id)
                    ->where('status', 'confirmed')
                    ->where('starts_at', '<', $ends)
                    ->where('ends_at', '>', $starts)
                    ->lockForUpdate()
                    ->exists();

                if ($conflict) {
                    throw new \RuntimeException('slot_taken');
                }

                $lead = Lead::withoutGlobalScope('tenant')
                    ->where('tenant_id', $host->tenant_id)
                    ->where('email', $email)
                    ->lockForUpdate()
                    ->first();

                if (! $lead) {
                    $leadData = array_filter([
                        'tenant_id'         => $host->tenant_id,
                        'source'            => 'meeting_booking',
                        'first_name'        => $firstName,
                        'last_name'         => $lastName,
                        'email'             => $email,
                        'phone'             => $request->input('phone'),
                        'status'            => 'new',
                        'pipeline_id'       => $meetingType->pipeline_id,
                        'pipeline_stage_id' => $meetingType->pipeline_stage_id,
                        'utm_source'        => $request->input('utm_source'),
                        'utm_medium'        => $request->input('utm_medium'),
                        'utm_campaign'      => $request->input('utm_campaign'),
                        'utm_content'       => $request->input('utm_content'),
                        'utm_term'          => $request->input('utm_term'),
                    ], fn ($v) => $v !== null && $v !== '');
                    $lead = Lead::create($leadData);
                } elseif ($meetingType->pipeline_id && $meetingType->pipeline_stage_id) {
                    $lead->update([
                        'pipeline_id'       => $meetingType->pipeline_id,
                        'pipeline_stage_id' => $meetingType->pipeline_stage_id,
                    ]);
                }

                $booking = MeetingBooking::create([
                    'tenant_id'       => $host->tenant_id,
                    'meeting_type_id' => $meetingType->id,
                    'user_id'         => $host->id,
                    'lead_id'         => $lead?->id,
                    'guest_name'      => $request->input('name'),
                    'guest_email'     => $email,
                    'guest_phone'     => $request->input('phone'),
                    'starts_at'       => $starts,
                    'ends_at'         => $ends,
                    'timezone'        => $tz,
                    'status'          => 'confirmed',
                    'notes'           => $request->input('notes'),
                    'metadata'        => [
                        'ip'          => $request->ip(),
                        'user_agent'  => substr((string) $request->userAgent(), 0, 500),
                        'utm_source'  => $request->input('utm_source'),
                        'utm_medium'  => $request->input('utm_medium'),
                        'utm_campaign'=> $request->input('utm_campaign'),
                        'utm_content' => $request->input('utm_content'),
                        'utm_term'    => $request->input('utm_term'),
                        'referrer'    => $request->header('Referer'),
                    ],
                ]);

                return [$booking, $lead];
            });
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'slot_taken') {
                return response()->json(['success' => false, 'message' => __('messages.booking_slot_taken')], 409);
            }
            throw $e;
        }

        if ($lead) {
            $guestName    = (string) $request->input('name');
            $meetingTitle = (string) $meetingType->name;
            $when         = $starts->copy()->setTimezone($tz)->translatedFormat('M j, Y g:i A');

            LeadActivity::create([
                'tenant_id'   => $host->tenant_id,
                'lead_id'     => $lead->id,
                'user_id'     => $host->id,
                'type'        => 'meeting_booked',
                'description' => sprintf(
                    '%s booked "%s" on %s',
                    $guestName,
                    $meetingTitle,
                    $when
                ),
                'metadata' => [
                    'booking_id'      => $booking->id,
                    'meeting_type_id' => $meetingType->id,
                    'i18n_key'        => 'lead_activities.booking_made',
                    'i18n_params'     => [
                        'guest'   => $guestName,
                        'meeting' => $meetingTitle,
                        'when'    => $when,
                    ],
                ],
            ]);
        }

        // Send emails (tolerate mail failures in install)
        try {
            Mail::to($email)->send(new MeetingBookedMail($booking));
        } catch (\Throwable $e) {
            report($e);
        }
        try {
            if ($host->email) {
                Mail::to($host->email)->send(new MeetingBookedMail($booking, forHost: true));
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return response()->json([
            'success'     => true,
            'redirect'    => route('book.confirmation', ['token' => $booking->reschedule_token]),
            'booking_id'  => $booking->id,
        ]);
    }

    public function confirmation(string $token): View|Response
    {
        $booking = MeetingBooking::withoutGlobalScope('tenant')
            ->where('view_token', $token)
            ->with(['meetingType.user'])
            ->first();

        abort_if(! $booking, 404);

        return view('public.booking.confirmation', [
            'booking'     => $booking,
            'meetingType' => $booking->meetingType,
            'host'        => $booking->meetingType->user,
        ]);
    }

    public function reschedule(string $token): View|Response
    {
        $booking = MeetingBooking::withoutGlobalScope('tenant')
            ->where('reschedule_token', $token)
            ->with(['meetingType.user'])
            ->first();

        abort_if(! $booking, 404);

        if ($booking->status === 'cancelled') {
            return redirect()->route('book.confirmation', ['token' => $booking->view_token]);
        }

        return view('public.booking.reschedule', [
            'booking'     => $booking,
            'meetingType' => $booking->meetingType,
            'host'        => $booking->meetingType->user,
        ]);
    }

    public function cancel(string $token, Request $request): Response
    {
        $booking = MeetingBooking::withoutGlobalScope('tenant')
            ->where('cancel_token', $token)
            ->first();

        abort_if(! $booking, 404);

        if ($booking->status === 'confirmed') {
            $booking->update([
                'status'              => 'cancelled',
                'cancelled_at'        => now(),
                'cancellation_reason' => $request->input('reason'),
            ]);
            try {
                Mail::to($booking->guest_email)->send(new MeetingCancelledMail($booking));
            } catch (\Throwable $e) {
                report($e);
            }
            try {
                if ($booking->user && $booking->user->email) {
                    Mail::to($booking->user->email)->send(new MeetingCancelledMail($booking, forHost: true));
                }
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return redirect()->route('book.confirmation', ['token' => $token]);
    }

    public function ics(string $token): Response
    {
        $booking = MeetingBooking::withoutGlobalScope('tenant')
            ->where('view_token', $token)
            ->with('meetingType.user')
            ->first();

        abort_if(! $booking, 404);

        $starts = Carbon::parse($booking->starts_at)->utc();
        $ends   = Carbon::parse($booking->ends_at)->utc();
        $fmt    = fn(Carbon $d) => $d->format('Ymd\THis\Z');

        // Fix note: RFC 5545 § 3.3.11 requires escaping of `\\`, `;`,
        // `,`, and line breaks in TEXT values. Prior implementation only
        // stripped \r\n which left `;` / `,` in meeting names (entered
        // verbatim by tenant admins) breaking parsing in strict iCal
        // clients (Apple Calendar, Outlook, Thunderbird). IcsSafety
        // handles all four cases.
        $summary  = \App\Support\IcsSafety::escapeText(
            (string) ($booking->meetingType->name ?? __('public_booking.ical_meeting_fallback'))
        );
        $hostName = $booking->user?->name ?? __('public_booking.ical_host_fallback');
        $desc     = __('public_booking.ical_description', [
            'host' => $hostName,
            'url'  => $booking->reschedule_url,
        ]);
        $desc     = \App\Support\IcsSafety::escapeText((string) $desc);

        $uid = 'booking-' . $booking->id . '@' . parse_url(config('app.url'), PHP_URL_HOST);

        $ics = implode("\r\n", [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Leadhub//Meeting Scheduler//EN',
            'METHOD:PUBLISH',
            'BEGIN:VEVENT',
            'UID:' . $uid,
            'DTSTAMP:' . $fmt(Carbon::now('UTC')),
            'DTSTART:' . $fmt($starts),
            'DTEND:' . $fmt($ends),
            'SUMMARY:' . $summary,
            'DESCRIPTION:' . $desc,
            'STATUS:' . ($booking->status === 'cancelled' ? 'CANCELLED' : 'CONFIRMED'),
            'END:VEVENT',
            'END:VCALENDAR',
            '',
        ]);

        return response($ics, 200, [
            'Content-Type'        => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="meeting-' . $booking->id . '.ics"',
        ]);
    }
}
