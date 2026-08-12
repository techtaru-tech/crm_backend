<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * Generic branded notification email for lead-related events.
 * Renders emails/lead-notification.blade.php which extends emails/layout.blade.php.
 */
class LeadNotificationMail extends BrandedMailable
{
    public function __construct(
        public string $emailSubject,
        public string $headline,
        public array $lines,
        public string $actionUrl,
        public string $actionLabel,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->emailSubject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.lead-notification');
    }
}
