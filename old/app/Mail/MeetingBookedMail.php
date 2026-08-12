<?php

namespace App\Mail;

use App\Models\MeetingBooking;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Support\Carbon;

class MeetingBookedMail extends Mailable
{
    public function __construct(
        public MeetingBooking $booking,
        public bool $forHost = false,
    ) {}

    public function envelope(): Envelope
    {
        $name   = $this->booking->meetingType?->name ?? __('mail.meeting_booked_default_name');
        $when   = Carbon::parse($this->booking->starts_at)
            ->setTimezone($this->booking->timezone ?: 'UTC')
            ->translatedFormat('M j, Y g:i A');

        $subject = $this->forHost
            ? __('mail.meeting_booked_subject_host', [
                'name'  => $name,
                'guest' => $this->booking->guest_name,
                'when'  => $when,
            ])
            : __('mail.meeting_booked_subject_guest', [
                'name' => $name,
                'when' => $when,
            ]);

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.meeting.booked',
            with: [
                'booking' => $this->booking,
                'forHost' => $this->forHost,
            ],
        );
    }

    public function attachments(): array
    {
        $ics = $this->buildIcs();
        // Filename prefix routed through the translator so the attachment
        // name in the recipient's inbox respects locale.  Sanitised with
        // Str::slug to guarantee email-client-safe characters even when
        // a translator hands us non-ASCII text.
        $prefix = \Illuminate\Support\Str::slug((string) __('mail.meeting_ics_filename')) ?: 'meeting';
        return [
            Attachment::fromData(fn() => $ics, $prefix . '-' . $this->booking->id . '.ics')
                ->withMime('text/calendar'),
        ];
    }

    protected function buildIcs(): string
    {
        $starts = Carbon::parse($this->booking->starts_at)->utc();
        $ends   = Carbon::parse($this->booking->ends_at)->utc();
        $fmt    = fn(Carbon $d) => $d->format('Ymd\THis\Z');

        // Fix note: RFC 5545 § 3.3.11 requires escaping of `\\`, `;`,
        // `,`, and line breaks in TEXT values. IcsSafety::escapeText
        // handles all four; prior code only stripped \r\n.
        $summary  = \App\Support\IcsSafety::escapeText(
            (string) ($this->booking->meetingType?->name ?? __('mail.meeting_default_name'))
        );
        $desc     = __('mail.meeting_description', [
            'host' => $this->booking->user?->name ?? __('mail.host_default_name'),
            'url'  => (string) $this->booking->reschedule_url,
        ]);
        $desc     = \App\Support\IcsSafety::escapeText((string) $desc);
        $uid      = 'booking-' . $this->booking->id . '@' . parse_url(config('app.url'), PHP_URL_HOST);

        return implode("\r\n", [
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
            'STATUS:CONFIRMED',
            'END:VEVENT',
            'END:VCALENDAR',
            '',
        ]);
    }
}
