<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Models\RecurringInvoice;
use App\Models\Tenant;
use App\Notifications\InvoiceDueReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

/**
 * Daily driver for the Recurring Invoices ("Dues") feature.
 *
 * Two passes, both deliberately running cross-tenant via
 * ->withoutGlobalScope('tenant'):
 *
 *   A) GENERATE — for every active schedule whose next_run_date has
 *      arrived, bind that schedule's tenant into the container (so the
 *      BelongsToTenant creating hooks stamp the correct tenant_id),
 *      materialise an Invoice + line item, then roll the schedule
 *      forward.
 *
 *   B) REMIND + OVERDUE — for every sent/partial invoice that is due
 *      within 3 days (or already past) and hasn't had a reminder yet,
 *      flip past-due invoices to "overdue", notify the owner, and stamp
 *      due_reminder_sent_at so it never re-fires.
 *
 * Tenant safety is paramount: the global tenant scope is bypassed for
 * the cross-tenant reads, but the per-row tenant is always re-bound
 * before any create/notify, and the binding is forgotten between rows
 * so nothing leaks across tenants.
 */
class ProcessRecurringInvoices extends Command
{
    /** How many days ahead of the due date to start reminding. */
    private const REMINDER_LEAD_DAYS = 3;

    protected $signature   = 'invoices:process-recurring';
    protected $description = 'Generate invoices from active recurring schedules and send due-date reminders.';

    public function handle(): int
    {
        $generated = $this->generateDueSchedules();
        $reminders = $this->dispatchDueReminders();

        $this->info(sprintf(
            'Recurring invoices: generated %d invoice(s); sent %d due-date reminder(s).',
            $generated,
            $reminders,
        ));

        return self::SUCCESS;
    }

    /**
     * Pass A — materialise invoices for every schedule that is due.
     */
    private function generateDueSchedules(): int
    {
        $count = 0;

        $schedules = RecurringInvoice::query()
            ->withoutGlobalScope('tenant')
            ->where('active', true)
            ->whereDate('next_run_date', '<=', now()->toDateString())
            ->with('tenant')
            ->get();

        foreach ($schedules as $schedule) {
            try {
                $tenant = $schedule->tenant;
                if (! $tenant) {
                    // Orphaned schedule (tenant soft-deleted): skip rather
                    // than generate an invoice we can't attribute.
                    Log::warning('ProcessRecurringInvoices: schedule has no tenant', [
                        'recurring_invoice_id' => $schedule->id,
                    ]);
                    continue;
                }

                // Bind the tenant so Invoice/InvoiceItem creating hooks
                // stamp the right tenant_id.
                app()->instance('current_tenant', $tenant);

                $schedule->generateInvoice();
                $schedule->advanceSchedule();
                $count++;
            } catch (\Throwable $e) {
                Log::warning('ProcessRecurringInvoices: failed to generate invoice', [
                    'recurring_invoice_id' => $schedule->id,
                    'error'                => $e->getMessage(),
                ]);
            } finally {
                // Never leak one tenant's binding into the next iteration.
                app()->forgetInstance('current_tenant');
            }
        }

        return $count;
    }

    /**
     * Pass B — flip past-due invoices to overdue and send a one-shot
     * reminder to the invoice owner.
     */
    private function dispatchDueReminders(): int
    {
        $count   = 0;
        $today   = now()->toDateString();
        $horizon = now()->addDays(self::REMINDER_LEAD_DAYS)->toDateString();

        $invoices = Invoice::query()
            ->withoutGlobalScope('tenant')
            ->whereIn('status', [Invoice::STATUS_SENT, Invoice::STATUS_PARTIAL])
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<=', $horizon)
            ->whereNull('due_reminder_sent_at')
            ->with(['tenant', 'creator', 'lead'])
            ->get();

        foreach ($invoices as $invoice) {
            try {
                $tenant = $invoice->tenant;
                if (! $tenant) {
                    continue;
                }

                app()->instance('current_tenant', $tenant);

                // Past the due date → mark overdue (quietly; no events).
                if ($invoice->due_date && $invoice->due_date->toDateString() < $today) {
                    $invoice->status = Invoice::STATUS_OVERDUE;
                    $invoice->saveQuietly();
                }

                $notifiable = $this->resolveNotifiable($invoice, $tenant);
                if ($notifiable) {
                    Notification::send($notifiable, new InvoiceDueReminderNotification($invoice));
                }

                $invoice->due_reminder_sent_at = now();
                $invoice->saveQuietly();
                $count++;
            } catch (\Throwable $e) {
                Log::warning('ProcessRecurringInvoices: failed to send due reminder', [
                    'invoice_id' => $invoice->id,
                    'error'      => $e->getMessage(),
                ]);
            } finally {
                app()->forgetInstance('current_tenant');
            }
        }

        return $count;
    }

    /**
     * Who should hear about this invoice? Prefer the user who created it;
     * fall back to the tenant owner (Tenant::owner() → belongsTo User via
     * owner_id). The User model is NOT tenant-scoped, so no
     * withoutGlobalScope juggling is needed here.
     *
     * Returns null only when neither can be resolved — the caller then
     * still stamps the dedupe marker so a notifiable-less invoice isn't
     * retried every day.
     */
    private function resolveNotifiable(Invoice $invoice, Tenant $tenant)
    {
        return $invoice->creator ?: $tenant->owner;
    }
}
