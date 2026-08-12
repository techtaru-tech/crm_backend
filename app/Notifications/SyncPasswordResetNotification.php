<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseNotification;

/**
 * Drop-in replacement for Filament\Auth\Notifications\ResetPassword
 * that does NOT implement ShouldQueue.  LeadHub ships to shared
 * hosting where QUEUE_CONNECTION=database is the default but no
 * queue worker runs, so every queued mail sat in the jobs table
 * forever.  This class preserves the `public string $url` shape
 * that Filament's RequestPasswordReset page expects, so the
 * Filament-flow-specific URL override (branded reset link) still
 * works the same way.
 *
 * Wired into the container in AppServiceProvider so Filament's
 * `app(\Filament\Auth\Notifications\ResetPassword::class, ...)`
 * returns an instance of this class instead.
 */
class SyncPasswordResetNotification extends BaseNotification
{
    /**
     * Injected by Filament's RequestPasswordReset after construction.
     * When set, takes precedence over the URL Laravel's
     * ResetPassword::createUrlCallback would produce.
     */
    public string $url = '';

    protected function resetUrl($notifiable): string
    {
        if ($this->url !== '') {
            return $this->url;
        }

        return parent::resetUrl($notifiable);
    }
}
