<?php

namespace App\Notifications\Concerns;

use App\Mail\LeadNotificationMail;
use App\Models\Tenant;

/**
 * Resolves the Tenant associated with a notifiable so that notification
 * toMail() methods can pass it to LeadNotificationMail::withTenant().
 *
 * All email sending is handled by LeadNotificationMail (a BrandedMailable
 * subclass), which applies the tenant's From address, From name, logo, and
 * colour palette automatically. This trait handles the tenant lookup and
 * the recipient routing.
 */
trait UsesBrandedMail
{
    /**
     * Apply tenant branding AND the recipient to a LeadNotificationMail.
     *
     * Every toMail() here returns a Mailable rather than a MailMessage, and
     * MailChannel::send() hands a Mailable straight to $mailable->send() —
     * it only applies the notifiable's address when a MailMessage is
     * returned. So the Mailable has to carry its own "To" or Symfony throws
     * "An email must have a "To", "Cc", or "Bcc" header." at send time.
     */
    protected function brandedMailFor(LeadNotificationMail $mail, object $notifiable): LeadNotificationMail
    {
        $mail->withTenant($this->resolveNotifiableTenant($notifiable));

        if ($recipient = $this->resolveMailRecipient($notifiable)) {
            $mail->to($recipient);
        }

        return $mail;
    }

    /**
     * Honour a custom routeNotificationForMail() when the notifiable defines
     * one, then fall back to the model itself (Mailable::to() reads its
     * `email` / `name`), so tenant users keep a friendly From-name pairing.
     */
    protected function resolveMailRecipient(object $notifiable): mixed
    {
        if (method_exists($notifiable, 'routeNotificationFor')
            && $route = $notifiable->routeNotificationFor('mail')) {
            return $route;
        }

        return ! empty($notifiable->email) ? $notifiable : null;
    }

    protected function resolveNotifiableTenant(object $notifiable): ?Tenant
    {
        if (method_exists($notifiable, 'tenant') && $notifiable->tenant instanceof Tenant) {
            return $notifiable->tenant;
        }

        if (property_exists($notifiable, 'tenant_id') && $notifiable->tenant_id) {
            return Tenant::find($notifiable->tenant_id);
        }

        if (app()->bound('current_tenant') && app('current_tenant') instanceof Tenant) {
            return app('current_tenant');
        }

        return null;
    }
}
