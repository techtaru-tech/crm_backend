<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * Synchronous password-reset email.  Replaces Filament's default
 * ResetPassword notification, which implements ShouldQueue and
 * therefore never delivers on the LeadHub shared-hosting target
 * (QUEUE_CONNECTION=database with no worker running).
 *
 * Same visual branding as every other LeadHub transactional email
 * via BrandedMailable.
 */
class PasswordResetMail extends BrandedMailable
{
    public string $resetUrl;
    public string $userName;
    public int    $expiresMinutes;

    public function __construct(User $user, string $resetUrl, int $expiresMinutes = 60)
    {
        $this->resetUrl       = $resetUrl;
        $this->userName       = (string) ($user->name ?: __('mail/password_reset.name_fallback'));
        $this->expiresMinutes = $expiresMinutes;

        // Pull tenant branding so the email header matches the
        // workspace the user belongs to.  Falls back to the
        // script-wide default config when the user has no tenant
        // (e.g. super-admins).
        $this->withTenant($user->tenant);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('mail.password_reset_subject', ['app' => $this->appName]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.password-reset',
        );
    }
}
