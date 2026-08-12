<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Notify the template owner's tenant admins that someone installed
 * their template from the marketplace.
 *
 * Without this, contributors fly blind — they couldn't see their
 * work being adopted, which is a death-blow for any community
 * marketplace.  With the notification + the existing downloads
 * counter, contributors see real-time installs in their bell-icon
 * dropdown and over time build the social proof that drives more
 * contributions.
 *
 * Database channel only — we deliberately don't email this because
 * a popular template would otherwise spam the contributor's inbox
 * on every install.  The bell-icon UI batches and groups them.
 */
class MarketplaceTemplateInstalledNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly int    $templateId,
        public readonly string $templateName,
        public readonly string $templateType,
        public readonly int    $installerTenantId,
        public readonly string $installerTenantName,
    ) {}

    public function via(mixed $notifiable): array
    {
        return ['database'];
    }

    public function toArray(mixed $notifiable): array
    {
        return [
            'type'                  => 'marketplace.template.installed',
            'template_id'           => $this->templateId,
            'template_name'         => $this->templateName,
            'template_type'         => $this->templateType,
            'installer_tenant_id'   => $this->installerTenantId,
            'installer_tenant_name' => $this->installerTenantName,
            'message'               => __('notifications.marketplace_template_installed_message', [
                'installer' => $this->installerTenantName,
                'template'  => $this->templateName,
                'type'      => $this->templateType,
            ]),
        ];
    }
}
