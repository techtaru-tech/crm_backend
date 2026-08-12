<?php

namespace App\Mail;

use App\Models\Invitation;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class InvitationMail extends BrandedMailable
{
    public string $acceptUrl;
    public string $inviterName;
    public string $workspaceName;
    public string $roleName;

    public function __construct(Invitation $invitation, string $signedUrl)
    {
        $this->acceptUrl     = $signedUrl;
        $this->inviterName   = $invitation->invitedBy?->name ?? __('mail/invitation.inviter_fallback');
        $this->workspaceName = $invitation->tenant->name;
        $this->roleName      = self::roleLabel($invitation->role);

        $this->withTenant($invitation->tenant);
    }

    /**
     * Translator-first role label. Falls back to ucfirst() of the raw
     * role key when no translation row exists (e.g. custom roles
     * introduced after the bundled lang files were generated).
     */
    protected static function roleLabel(?string $role): string
    {
        $role = (string) $role;
        if ($role === '') {
            return '';
        }

        $key        = 'invitation_accept.role_' . strtolower($role);
        $translated = __($key);

        // Laravel returns the key itself when a translation is missing.
        if ($translated !== $key) {
            return (string) $translated;
        }

        return ucfirst($role);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('mail.invitation_subject', [
                'workspace' => $this->workspaceName,
                'app'       => $this->appName,
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invitation',
        );
    }
}
