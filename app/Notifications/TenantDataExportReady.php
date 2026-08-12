<?php

declare(strict_types=1);

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Database-channel notification for GDPR Article 20 data export.
 *
 * Dropped into the user's bell-icon dropdown when ExportTenantDataJob
 * finishes building their workspace ZIP.  Carries the presigned
 * download URL that DataExportController serves once for 24 hours.
 *
 * Database channel only — the file lives outside email systems and
 * the link is single-use, so SMTP doesn't need to know about it.
 */
class TenantDataExportReady extends Notification
{
    use Queueable;

    public function __construct(
        public readonly string $downloadUrl,
        public readonly Carbon $expiresAt,
        public readonly int    $sizeBytes,
        public readonly string $tenantName,
    ) {}

    public function via(mixed $notifiable): array
    {
        return ['database'];
    }

    public function toArray(mixed $notifiable): array
    {
        return [
            'type'         => 'data_export.ready',
            'message'      => __('notifications.tenant_data_export_ready_message', [
                'name' => $this->tenantName,
            ]),
            'download_url' => $this->downloadUrl,
            'expires_at'   => $this->expiresAt->toIso8601String(),
            'size_bytes'   => $this->sizeBytes,
            'tenant_name'  => $this->tenantName,
        ];
    }
}
