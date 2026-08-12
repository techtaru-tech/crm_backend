<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use App\Jobs\CheckImapMailboxes;
use App\Jobs\CheckNoActivityAutomations;
use App\Jobs\ProcessAiSdrEnrollments;
use App\Jobs\ProcessEmailSequences;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Cron-task hardening (H13)
|--------------------------------------------------------------------------
| Two guarantees applied to every Schedule::* line below:
|
|  1. ->withoutOverlapping(10) — Laravel's default of 1440 minutes is
|     way too aggressive for fast-firing tasks (->everyFiveMinutes()).
|     A genuinely stuck job would block the slot for a full day.  10
|     minutes lets a slow run finish without blocking the next slot
|     for hours.
|
|  2. ->onFailure($logFailure('<name>')) — without this, scheduled jobs
|     that throw write a stack trace to laravel.log under the generic
|     "Exception in cron" label.  We add a structured warning with the
|     task name so SA dashboards / log aggregators can alert on
|     "schedule task failed" specifically.
|
| The closure factory below is intentionally a small named factory
| rather than inline lambdas — it keeps the schedule list readable as
| one consistent line per task, and makes the log keyword grep-able.
*/

$logFailure = fn (string $name) => function () use ($name) {
    Log::error("schedule task failed: {$name}");
};

Schedule::job(new CheckImapMailboxes())
    ->everyFiveMinutes()
    ->withoutOverlapping(10)
    ->onFailure($logFailure('CheckImapMailboxes'));

Schedule::job(new CheckNoActivityAutomations())
    ->hourly()
    ->withoutOverlapping(10)
    ->onFailure($logFailure('CheckNoActivityAutomations'));

Schedule::job(new ProcessEmailSequences())
    ->everyFifteenMinutes()
    ->withoutOverlapping(10)
    ->onFailure($logFailure('ProcessEmailSequences'));

// AI SDR runner — picks up due enrollments (state=active AND
// next_action_at has arrived) and makes one AI decision / one action per
// enrollment per tick. Mirrors the email-sequence cadence. No-op on the
// public demo (DemoMode short-circuits inside the job).
Schedule::job(new ProcessAiSdrEnrollments())
    ->everyFifteenMinutes()
    ->withoutOverlapping(10)
    ->onFailure($logFailure('ProcessAiSdrEnrollments'));

Schedule::command('notifications:digest')
    ->hourly()
    ->withoutOverlapping(10)
    ->onFailure($logFailure('notifications:digest'));

Schedule::command('tasks:send-reminders')
    ->everyFiveMinutes()
    ->withoutOverlapping(10)
    ->onFailure($logFailure('tasks:send-reminders'));

Schedule::command('leadhub:check-updates')
    ->daily()
    ->withoutOverlapping(10)
    ->onFailure($logFailure('leadhub:check-updates'));

// Nightly licence re-check.  Refreshes the cached status from the
// LeadHub licensing server so a revoked / expired licence transitions
// the install into the grace window (and eventually into the blocked
// state).  Skipped automatically when LEADHUB_DEMO_MODE=true so the
// public preview at theleadhub.app makes no outbound calls.
Schedule::command('leadhub:verify-license', ['--quiet-no-key'])
    ->dailyAt('05:30')
    ->withoutOverlapping(10)
    ->onFailure($logFailure('leadhub:verify-license'));

Schedule::command('reports:deliver')
    ->hourly()
    ->withoutOverlapping(10)
    ->onFailure($logFailure('reports:deliver'));

Schedule::command('filters:send-alerts')
    ->hourly()
    ->withoutOverlapping(10)
    ->onFailure($logFailure('filters:send-alerts'));

Schedule::command('leadhub:subscription-lifecycle')
    ->dailyAt('06:00')
    ->withoutOverlapping(10)
    ->onFailure($logFailure('leadhub:subscription-lifecycle'));

// Recurring invoices / dues: generate invoices from active schedules whose
// next_run_date has arrived, then send one-shot due-date reminders (and flip
// past-due invoices to overdue).  See App\Console\Commands\ProcessRecurringInvoices.
Schedule::command('invoices:process-recurring')
    ->dailyAt('07:00')
    ->withoutOverlapping(10)
    ->onFailure($logFailure('invoices:process-recurring'));

Schedule::command('leadhub:capture-benchmarks')
    ->dailyAt('03:30')
    ->withoutOverlapping(10)
    ->onFailure($logFailure('leadhub:capture-benchmarks'));

Schedule::command('leadhub:sync-calendars')
    ->everyFifteenMinutes()
    ->withoutOverlapping(10)
    ->onFailure($logFailure('leadhub:sync-calendars'));

// GDPR/SOC2 retention: rolling 180-day audit_logs window + 30-day
// login_attempts window + 90-day processed_billing_events window
// (gateway webhook idempotency table — gateways stop retrying long
// before 90 days, so older rows have no value).  Override per
// table via LH_AUDIT_RETENTION_DAYS / LH_LOGIN_ATTEMPT_RETENTION_DAYS
// / LH_BILLING_EVENT_RETENTION_DAYS env vars.
Schedule::command('leadhub:purge-audit-logs')
    ->dailyAt('03:00')
    ->withoutOverlapping(10)
    ->onFailure($logFailure('leadhub:purge-audit-logs'));

// GDPR Article 17 erasure sweeper — hard-deletes tenants whose
// 30-day deletion cool-off has elapsed.  Runs an hour after the
// audit-log purge so there's a quiet window for the cascading
// transaction to complete before the next maintenance task.
Schedule::command('leadhub:execute-erasure')
    ->dailyAt('04:00')
    ->withoutOverlapping(10)
    ->onFailure($logFailure('leadhub:execute-erasure'));

// Nightly automatic backup — guarded by admin toggle in Script Settings.
Schedule::command('leadhub:backup')
    ->dailyAt('02:00')
    ->withoutOverlapping(10)
    ->onFailure($logFailure('leadhub:backup'))
    ->when(function () {
        try {
            return app(\App\Settings\GeneralSettings::class)->auto_nightly_backup
                || config('leadhub.backups.auto_nightly', false);
        } catch (\Throwable) {
            return false;
        }
    });
