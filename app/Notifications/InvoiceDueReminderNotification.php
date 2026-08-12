<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent to the invoice owner (creator, or the tenant owner as fallback)
 * when a sent/partial invoice is approaching — or has passed — its due
 * date. Dispatched by the `invoices:process-recurring` command, which
 * de-dupes via invoices.due_reminder_sent_at so each invoice only ever
 * triggers one reminder.
 *
 * Mirrors {@see LeadTaskReminderNotification}: queued, database + mail
 * channels, translator-backed copy.
 */
class InvoiceDueReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Invoice $invoice) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toArray(object $notifiable): array
    {
        $lead = $this->invoice->lead;

        return [
            'type'           => 'invoice_due_reminder',
            'invoice_id'     => $this->invoice->id,
            'invoice_number' => $this->invoice->invoice_number,
            'lead_id'        => $this->invoice->lead_id,
            'lead_name'      => $lead ? trim($lead->first_name . ' ' . $lead->last_name) : null,
            'amount'         => (float) $this->invoice->total,
            'currency'       => $this->invoice->currency,
            'due_date'       => optional($this->invoice->due_date)->toDateString(),
            'url'            => \App\Support\AdminUrl::for('invoices/' . $this->invoice->id),
            'message'        => __('notifications.invoice_due_reminder_db_message', [
                'number' => $this->invoice->invoice_number,
            ]),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $lead = $this->invoice->lead;
        $who  = $lead
            ? trim($lead->first_name . ' ' . $lead->last_name)
            : __('notifications.invoice_due_reminder_customer_fallback');
        $due  = $this->invoice->due_date
            ? $this->invoice->due_date->translatedFormat('M j, Y')
            : __('notifications.invoice_due_reminder_due_no_date');
        $amount = trim(($this->invoice->currency ?: 'USD') . ' ' . number_format((float) $this->invoice->total, 2));

        return (new MailMessage)
            ->subject(__('notifications.invoice_due_reminder_mail_subject', ['number' => $this->invoice->invoice_number]))
            ->greeting(__('notifications.invoice_due_reminder_mail_greeting'))
            ->line(__('notifications.invoice_due_reminder_mail_line_intro'))
            ->line(__('notifications.invoice_due_reminder_mail_line_number', ['number' => $this->invoice->invoice_number]))
            ->line(__('notifications.invoice_due_reminder_mail_line_customer', ['name' => $who]))
            ->line(__('notifications.invoice_due_reminder_mail_line_amount', ['amount' => $amount]))
            ->line(__('notifications.invoice_due_reminder_mail_line_due', ['due' => $due]))
            ->action(
                __('notifications.invoice_due_reminder_btn_view'),
                \App\Support\AdminUrl::for('invoices/' . $this->invoice->id)
            )
            ->line(__('notifications.invoice_due_reminder_mail_line_outro', ['app' => config('app.name', 'LeadHub')]));
    }
}
