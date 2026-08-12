<?php

namespace App\Mail;

use App\Models\MeetingBooking;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Carbon;

class MeetingCancelledMail extends Mailable
{
    public function __construct(
        public MeetingBooking $booking,
        public bool $forHost = false,
    ) {}

    public function envelope(): Envelope
    {
        $name = $this->booking->meetingType?->name ?? __('mail.meeting_default_name');
        $when = Carbon::parse($this->booking->starts_at)
            ->setTimezone($this->booking->timezone ?: 'UTC')
            ->translatedFormat('M j, Y g:i A');

        return new Envelope(subject: __('mail.meeting_cancelled_subject', [
            'name' => $name,
            'when' => $when,
        ]));
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.meeting.cancelled',
            with: [
                'booking' => $this->booking,
                'forHost' => $this->forHost,
            ],
        );
    }
}
