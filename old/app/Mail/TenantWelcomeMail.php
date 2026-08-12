<?php

namespace App\Mail;

use App\Models\Tenant;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * NOT ShouldQueue — user-waiting transactional email, and LeadHub
 * ships to shared hosting where the default QUEUE_CONNECTION is
 * "database" but no worker runs to drain the jobs table.  Sync
 * send guarantees delivery; InvitationMail is sent the same way
 * from InvitationService for the same reason.
 */
class TenantWelcomeMail extends BrandedMailable
{
    use SerializesModels;

    public string $loginUrl;
    public string $userEmail;
    public string $workspaceName;
    public ?string $setupUrl;
    public bool $userSetPassword;

    /**
     * @param Tenant  $tenant
     * @param string  $email
     * @param ?string $setupUrl          Signed password-setup link. When null,
     *                                   the email tells the user to sign in
     *                                   with the password they just chose.
     * @param bool    $userSetPassword   True for self-service registrations.
     */
    public function __construct(Tenant $tenant, string $email, ?string $setupUrl = null, bool $userSetPassword = false)
    {
        $this->userEmail       = $email;
        $this->workspaceName   = $tenant->name;
        $this->loginUrl        = \App\Support\AdminUrl::for('login');
        $this->setupUrl        = $setupUrl;
        $this->userSetPassword = $userSetPassword;

        $this->withTenant($tenant);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('mail.tenant_welcome_subject', ['workspace' => $this->workspaceName]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.tenant-welcome',
        );
    }
}
