<?php

declare(strict_types=1);

use Spatie\LaravelSettings\Migrations\SettingsMigration;

/**
 * Adds three SA-editable lifecycle cadence fields to BillingSettings:
 *
 *   billing.trial_reminder_days
 *     - days BEFORE trial_ends_at on which to email "your trial is
 *       ending in N day(s)".  Default [7, 3, 1] mirrors the previous
 *       hardcoded ProcessSubscriptionLifecycle behaviour.
 *
 *   billing.post_expiry_reminder_days
 *     - days AFTER a trial / subscription expires on which to email
 *       a follow-up nudge (drip campaign).  Default [3, 7, 14] sends
 *       three nudges over two weeks, then stops.  Set to [] to
 *       disable post-expiry drip entirely.
 *
 *   billing.auto_suspend_after_expiry_days
 *     - if > 0, the lifecycle cron flips active=false on expired /
 *       trial_expired tenants this many days after their *_ends_at
 *       date.  Default 0 means "never auto-suspend" — the SA must
 *       suspend manually from the Tenants list.  Setting 30 means
 *       expired tenants get the post-expiry drip for 14 days, then
 *       coast for two more weeks, then auto-suspend.
 */
return new class extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('billing.trial_reminder_days', [7, 3, 1]);
        $this->migrator->add('billing.post_expiry_reminder_days', [3, 7, 14]);
        $this->migrator->add('billing.auto_suspend_after_expiry_days', 0);
    }

    public function down(): void
    {
        $this->migrator->delete('billing.auto_suspend_after_expiry_days');
        $this->migrator->delete('billing.post_expiry_reminder_days');
        $this->migrator->delete('billing.trial_reminder_days');
    }
};
