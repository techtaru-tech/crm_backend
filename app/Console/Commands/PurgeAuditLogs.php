<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\LoginAttempt;
use App\Models\ProcessedBillingEvent;
use Illuminate\Console\Command;

/**
 * Audit log + login_attempts + processed_billing_events retention.
 *
 * Without bounded retention, a busy install grows audit_logs without
 * limit (every CRUD action on every tenant writes a row).  Most
 * compliance regimes (GDPR, SOC2) demand a defined retention period
 * and an automatic purge that enforces it — keeping forever is its
 * own liability.
 *
 * processed_billing_events is the inbound webhook idempotency table.
 * Every successful Stripe/PayPal/Razorpay/Paystack event records a
 * row.  Gateways stop retrying long before 90 days, so older rows
 * have no value and just inflate the table.  The migration comment
 * promised this purge — this command makes it true.
 *
 * Defaults:
 *   audit_logs:               180 days  (six months — covers most
 *                                        dispute windows; legal-review
 *                                        compromise)
 *   login_attempts:           30 days   (only forensic value; lockout
 *                                        uses a 15-minute window so
 *                                        older rows are just SOC2-grade
 *                                        evidence trail)
 *   processed_billing_events: 90 days   (gateways stop retrying long
 *                                        before; older rows can be
 *                                        safely dropped without re-
 *                                        introducing duplicate-event
 *                                        risk)
 *
 * Override per install via config/leadhub.php:
 *   'audit_log_retention_days'     => env('LH_AUDIT_RETENTION_DAYS', 180)
 *   'login_attempt_retention_days' => env('LH_LOGIN_ATTEMPT_RETENTION_DAYS', 30)
 *   'billing_event_retention_days' => env('LH_BILLING_EVENT_RETENTION_DAYS', 90)
 *
 * Wired in routes/console.php:
 *   Schedule::command('leadhub:purge-audit-logs')->dailyAt('03:00');
 */
class PurgeAuditLogs extends Command
{
    protected $signature = 'leadhub:purge-audit-logs
                            {--days= : Override retention days for audit_logs (default: 180)}
                            {--login-days= : Override retention days for login_attempts (default: 30)}
                            {--billing-events-days= : Override retention days for processed_billing_events (default: 90)}
                            {--dry-run : Count rows that would be deleted without deleting}';

    protected $description = 'Purge audit_logs, login_attempts, and processed_billing_events older than the configured retention windows';

    public function handle(): int
    {
        $auditDays = (int) ($this->option('days')
            ?? config('leadhub.audit_log_retention_days', 180));
        $loginDays = (int) ($this->option('login-days')
            ?? config('leadhub.login_attempt_retention_days', 30));
        $billingEventsDays = (int) ($this->option('billing-events-days')
            ?? config('leadhub.billing_event_retention_days', 90));

        if ($auditDays < 1 || $loginDays < 1 || $billingEventsDays < 1) {
            $this->error('Retention windows must be >= 1 day.');
            return self::FAILURE;
        }

        $auditCutoff = now()->subDays($auditDays);
        $loginCutoff = now()->subDays($loginDays);
        $billingEventsCutoff = now()->subDays($billingEventsDays);

        $auditCount = AuditLog::query()->withoutGlobalScopes()->where('created_at', '<', $auditCutoff)->count();
        $loginCount = LoginAttempt::query()->where('created_at', '<', $loginCutoff)->count();
        $billingEventsCount = ProcessedBillingEvent::query()
            ->where('processed_at', '<', $billingEventsCutoff)->count();

        $this->info(sprintf(
            'Audit logs older than %d days: %d row(s) eligible (cutoff %s)',
            $auditDays,
            $auditCount,
            $auditCutoff->toIso8601String(),
        ));
        $this->info(sprintf(
            'Login attempts older than %d days: %d row(s) eligible (cutoff %s)',
            $loginDays,
            $loginCount,
            $loginCutoff->toIso8601String(),
        ));
        $this->info(sprintf(
            'Processed billing events older than %d days: %d row(s) eligible (cutoff %s)',
            $billingEventsDays,
            $billingEventsCount,
            $billingEventsCutoff->toIso8601String(),
        ));

        if ($this->option('dry-run')) {
            $this->warn('Dry-run mode — no rows deleted.');
            return self::SUCCESS;
        }

        $auditDeleted = 0;
        $loginDeleted = 0;
        $billingEventsDeleted = 0;

        // Chunk-delete so a multi-million-row purge doesn't lock
        // the table for minutes.
        AuditLog::query()->withoutGlobalScopes()->where('created_at', '<', $auditCutoff)
            ->chunkById(1000, function ($chunk) use (&$auditDeleted) {
                $auditDeleted += AuditLog::query()->withoutGlobalScopes()->whereIn('id', $chunk->pluck('id'))->delete();
            });

        LoginAttempt::query()->where('created_at', '<', $loginCutoff)
            ->chunkById(1000, function ($chunk) use (&$loginDeleted) {
                $loginDeleted += LoginAttempt::query()->whereIn('id', $chunk->pluck('id'))->delete();
            });

        ProcessedBillingEvent::query()->where('processed_at', '<', $billingEventsCutoff)
            ->chunkById(1000, function ($chunk) use (&$billingEventsDeleted) {
                $billingEventsDeleted += ProcessedBillingEvent::query()
                    ->whereIn('id', $chunk->pluck('id'))->delete();
            });

        $this->info(sprintf(
            'Deleted %d audit_logs, %d login_attempts, and %d processed_billing_events.',
            $auditDeleted,
            $loginDeleted,
            $billingEventsDeleted,
        ));
        return self::SUCCESS;
    }
}
