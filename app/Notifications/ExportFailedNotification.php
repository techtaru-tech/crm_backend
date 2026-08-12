<?php

namespace App\Notifications;

use App\Mail\LeadNotificationMail;
use App\Notifications\Concerns\UsesBrandedMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ExportFailedNotification extends Notification implements ShouldQueue
{
    use Queueable, UsesBrandedMail;

    public function __construct(private string $errorMessage) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): LeadNotificationMail
    {
        $tenant = $this->resolveNotifiableTenant($notifiable);

        return (new LeadNotificationMail(
            emailSubject: __('notifications.export_failed_mail_subject'),
            headline: __('notifications.export_failed_mail_headline'),
            lines: [
                __('notifications.export_failed_mail_line_intro'),
                __('notifications.export_failed_mail_line_reason', ['reason' => e($this->errorMessage)]),
                __('notifications.export_failed_mail_line_retry'),
            ],
            actionUrl: null,
            actionLabel: null,
        ))->withTenant($tenant);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'    => 'export_failed',
            'message' => __('notifications.export_failed_db_message', ['error' => $this->errorMessage]),
        ];
    }
}
