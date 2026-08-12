<?php

namespace App\Notifications\Concerns;

use App\Models\Tenant;

/**
 * Resolves the Tenant associated with a notifiable so that notification
 * toMail() methods can pass it to LeadNotificationMail::withTenant().
 *
 * All email sending is handled by LeadNotificationMail (a BrandedMailable
 * subclass), which applies the tenant's From address, From name, logo, and
 * colour palette automatically. This trait only handles the tenant lookup.
 */
trait UsesBrandedMail
{
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
