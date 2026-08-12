<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Mail\OnboardingDripMail;
use App\Mail\SubscriptionExpiredMail;
use App\Mail\TrialEndingSoonMail;
use App\Mail\TrialExpiredMail;
use App\Mail\WorkspaceSuspendedMail;
use App\Models\AuditLog;
use App\Models\Tenant;
use App\Settings\BillingSettings;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Daily lifecycle worker for tenant subscriptions.
 *
 * Five passes, each idempotent and tracked via flags in
 * tenants.settings.lifecycle.*:
 *
 *   1. processTrialReminders
 *      Sends "your trial ends in N day(s)" emails on the cadence
 *      from BillingSettings.trial_reminder_days (default [7,3,1]).
 *
 *   2. processTrialExpirations
 *      Flips status='trial' tenants whose trial_ends_at is in the
 *      past to status='trial_expired' and sends the transition mail.
 *
 *   3. processSubscriptionExpirations
 *      Flips status='active' tenants whose subscription_ends_at is
 *      in the past to status='expired' and sends the transition mail.
 *
 *   4. processPostExpiryDrip                                (new)
 *      Sends post-expiry "drip" nudges to trial_expired / expired
 *      tenants at the cadence from BillingSettings.
 *      post_expiry_reminder_days (default [3,7,14]).
 *
 *   5. processAutoSuspensions                                (new)
 *      Sets active=false on trial_expired / expired tenants whose
 *      expiry date is older than BillingSettings.
 *      auto_suspend_after_expiry_days (default 0 = never).
 *      Sends a final WorkspaceSuspendedMail and writes an audit log.
 *
 * Safe to run multiple times per day — every pass checks an
 * idempotency flag before sending or transitioning.
 */
class ProcessSubscriptionLifecycle extends Command
{
    protected $signature   = 'leadhub:subscription-lifecycle
                              {--dry-run : Print what would happen without making any changes}';
    protected $description = 'Expire trials/subscriptions, send lifecycle reminders, drip post-expiry nudges, auto-suspend after grace period';

    protected bool $dryRun = false;
    protected BillingSettings $settings;

    public function handle(): int
    {
        $this->dryRun = (bool) $this->option('dry-run');

        // BillingSettings throws SettingDoesNotExist if Spatie's settings
        // table hasn't been seeded (partial install, fresh DB without
        // DatabaseSeeder run yet).  Fall back to a default-constructed
        // BillingSettings + log a warning so the cron run finishes
        // gracefully instead of leaving the operator with an unexplained
        // failed-schedule entry.
        try {
            $this->settings = app(BillingSettings::class);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning(
                'leadhub:subscription-lifecycle: BillingSettings unavailable — using defaults',
                ['error' => $e->getMessage()],
            );
            $this->settings = new BillingSettings();
        }

        if ($this->dryRun) {
            $this->warn('DRY RUN — no changes will be persisted.');
        }

        $this->processOnboardingDrip();
        $this->processTrialReminders();
        $this->processTrialExpirations();
        $this->processSubscriptionExpirations();
        $this->processPostExpiryDrip();
        $this->processAutoSuspensions();

        $this->info('Subscription lifecycle run complete.');
        return self::SUCCESS;
    }

    /**
     * Day-1/3/5/7 onboarding drip for fresh trials.
     *
     * Industry math: a structured onboarding sequence improves
     * trial→paid conversion 25-40%.  Each day has a different
     * frame:
     *   day 1 → "add your first lead in 90 seconds"
     *   day 3 → "common stuck-spots are X, Y, Z"
     *   day 5 → "3 automations every team turns on in week 1"
     *   day 7 → social-proof + soft conversion ask
     *
     * Idempotent via lifecycle.onboarding_day_X_sent flag.  Only
     * fires for status='trial' tenants — once they convert to
     * 'active' the drip stops automatically.
     */
    protected function processOnboardingDrip(): void
    {
        $cadence = [1, 3, 5, 7];
        $now = Carbon::now();

        $tenants = Tenant::query()
            ->where('active', true)
            ->where('subscription_status', 'trial')
            ->whereNotNull('created_at')
            ->where('created_at', '>=', $now->copy()->subDays(8))
            ->get();

        foreach ($tenants as $tenant) {
            $daysSinceCreation = (int) floor($tenant->created_at->diffInHours($now, false) / 24);

            if (! in_array($daysSinceCreation, $cadence, true)) {
                continue;
            }

            $flagKey = "lifecycle.onboarding_day_{$daysSinceCreation}_sent";
            if ($tenant->getSetting($flagKey)) {
                continue;
            }

            $email = $tenant->owner?->email;
            if (! $email) {
                $this->warn("Skipping tenant #{$tenant->id} — no owner email.");
                continue;
            }

            $this->line("[onboarding day {$daysSinceCreation}] #{$tenant->id} {$tenant->name} → {$email}");

            if ($this->dryRun) {
                continue;
            }

            try {
                Mail::to($email)->send(new OnboardingDripMail($tenant, $daysSinceCreation));
                $this->setLifecycleFlag($tenant, $flagKey, true);
            } catch (\Throwable $e) {
                Log::error("[lifecycle] Onboarding drip failed for tenant {$tenant->id} day {$daysSinceCreation}: {$e->getMessage()}");
            }
        }
    }

    /**
     * Pre-expiry trial reminders ("your trial ends in N day(s)").
     */
    protected function processTrialReminders(): void
    {
        $cadence = array_values(array_filter(array_map(
            'intval',
            (array) ($this->settings->trial_reminder_days ?? [])
        ), fn ($d) => $d > 0));

        if (empty($cadence)) {
            return;
        }

        $now = Carbon::now();

        $tenants = Tenant::query()
            ->where('active', true)
            ->where('subscription_status', 'trial')
            ->whereNotNull('trial_ends_at')
            ->where('trial_ends_at', '>', $now)
            ->get();

        foreach ($tenants as $tenant) {
            $daysRemaining = (int) ceil($now->diffInHours($tenant->trial_ends_at, false) / 24);

            if (! in_array($daysRemaining, $cadence, true)) {
                continue;
            }

            $flagKey = "lifecycle.trial_reminder_sent_{$daysRemaining}";
            if ($tenant->getSetting($flagKey)) {
                continue;
            }

            $email = $tenant->owner?->email;
            if (! $email) {
                $this->warn("Skipping tenant #{$tenant->id} — no owner email.");
                continue;
            }

            $this->line("[reminder {$daysRemaining}d] #{$tenant->id} {$tenant->name} → {$email}");

            if (! $this->dryRun) {
                try {
                    Mail::to($email)->send(new TrialEndingSoonMail($tenant, $daysRemaining));
                    $this->setLifecycleFlag($tenant, $flagKey, true);
                } catch (\Throwable $e) {
                    Log::error("[lifecycle] Failed to send trial reminder for tenant {$tenant->id}: {$e->getMessage()}");
                }
            }
        }
    }

    /**
     * Flip expired trials to status='trial_expired' and send the
     * transition email.
     */
    protected function processTrialExpirations(): void
    {
        $now = Carbon::now();

        $tenants = Tenant::query()
            ->where('active', true)
            ->where('subscription_status', 'trial')
            ->whereNotNull('trial_ends_at')
            ->where('trial_ends_at', '<=', $now)
            ->get();

        foreach ($tenants as $tenant) {
            $this->line("[trial expired] #{$tenant->id} {$tenant->name}");

            if ($this->dryRun) {
                continue;
            }

            $tenant->subscription_status = \App\Enums\SubscriptionStatus::TrialExpired;
            $tenant->save();

            $email = $tenant->owner?->email;
            if ($email && ! $tenant->getSetting('lifecycle.trial_expired_sent')) {
                try {
                    Mail::to($email)->send(new TrialExpiredMail($tenant));
                    $this->setLifecycleFlag($tenant, 'lifecycle.trial_expired_sent', true);
                } catch (\Throwable $e) {
                    Log::error("[lifecycle] Failed to send trial-expired mail for tenant {$tenant->id}: {$e->getMessage()}");
                }
            }
        }
    }

    /**
     * Flip lapsed paid subscriptions to status='expired' and send
     * the transition email.
     */
    protected function processSubscriptionExpirations(): void
    {
        $now = Carbon::now();

        $tenants = Tenant::query()
            ->where('active', true)
            ->where('subscription_status', 'active')
            ->whereNotNull('subscription_ends_at')
            ->where('subscription_ends_at', '<=', $now)
            ->get();

        foreach ($tenants as $tenant) {
            $this->line("[subscription expired] #{$tenant->id} {$tenant->name}");

            if ($this->dryRun) {
                continue;
            }

            $tenant->subscription_status = \App\Enums\SubscriptionStatus::Expired;
            $tenant->save();

            $email = $tenant->owner?->email;
            if ($email && ! $tenant->getSetting('lifecycle.subscription_expired_sent')) {
                try {
                    Mail::to($email)->send(new SubscriptionExpiredMail($tenant));
                    $this->setLifecycleFlag($tenant, 'lifecycle.subscription_expired_sent', true);
                } catch (\Throwable $e) {
                    Log::error("[lifecycle] Failed to send subscription-expired mail for tenant {$tenant->id}: {$e->getMessage()}");
                }
            }
        }
    }

    /**
     * Drip post-expiry nudges to tenants that haven't reacted to the
     * transition email.  Reuses the existing TrialExpiredMail /
     * SubscriptionExpiredMail templates — content is identical, just
     * delivered at later cadence points.  If you want different copy
     * per drip step, swap to dedicated mailables here.
     */
    protected function processPostExpiryDrip(): void
    {
        $cadence = array_values(array_filter(array_map(
            'intval',
            (array) ($this->settings->post_expiry_reminder_days ?? [])
        ), fn ($d) => $d > 0));

        if (empty($cadence)) {
            return;
        }

        $now = Carbon::now();

        $tenants = Tenant::query()
            ->where('active', true)
            ->whereIn('subscription_status', ['trial_expired', 'expired'])
            ->get();

        foreach ($tenants as $tenant) {
            $anchor = $this->expiryAnchor($tenant);
            if (! $anchor) {
                continue;
            }

            $daysSince = (int) ceil($now->diffInHours($anchor, true) / 24);
            if ($daysSince < 1) {
                // Anchor is in the future — shouldn't happen given
                // the status filters above, but guard against clock
                // skew.
                continue;
            }
            if (! in_array($daysSince, $cadence, true)) {
                continue;
            }

            $flagKey = "lifecycle.post_expiry_drip_sent_{$daysSince}";
            if ($tenant->getSetting($flagKey)) {
                continue;
            }

            $email = $tenant->owner?->email;
            if (! $email) {
                continue;
            }

            $this->line("[drip {$daysSince}d post-expiry] #{$tenant->id} {$tenant->name} → {$email}");

            if (! $this->dryRun) {
                try {
                    $mail = $tenant->subscription_status === \App\Enums\SubscriptionStatus::TrialExpired
                        ? new TrialExpiredMail($tenant)
                        : new SubscriptionExpiredMail($tenant);
                    Mail::to($email)->send($mail);
                    $this->setLifecycleFlag($tenant, $flagKey, true);
                } catch (\Throwable $e) {
                    Log::error("[lifecycle] Failed to send post-expiry drip for tenant {$tenant->id}: {$e->getMessage()}");
                }
            }
        }
    }

    /**
     * Final pass — flip active=false on tenants whose expiry is older
     * than the configured grace period.  No-op when the threshold is
     * zero (the default — SA must suspend manually).
     */
    protected function processAutoSuspensions(): void
    {
        $threshold = (int) ($this->settings->auto_suspend_after_expiry_days ?? 0);
        if ($threshold <= 0) {
            return;
        }

        $now = Carbon::now();

        $tenants = Tenant::query()
            ->where('active', true)
            ->whereIn('subscription_status', ['trial_expired', 'expired'])
            ->get();

        foreach ($tenants as $tenant) {
            $anchor = $this->expiryAnchor($tenant);
            if (! $anchor) {
                continue;
            }

            $daysSince = (int) ceil($now->diffInHours($anchor, true) / 24);
            if ($daysSince < $threshold) {
                continue;
            }

            $this->line("[auto-suspend {$daysSince}d post-expiry] #{$tenant->id} {$tenant->name}");

            if ($this->dryRun) {
                continue;
            }

            $tenant->forceFill(['active' => false])->save();

            AuditLog::record(
                'tenant.auto_suspended',
                $tenant,
                [],
                [
                    'tenant_name'    => $tenant->name,
                    'days_expired'   => $daysSince,
                    'threshold_days' => $threshold,
                    'by'             => 'cron',
                ],
                'tenant-management',
            );

            Log::info('Tenant auto-suspended', [
                'tenant_id'      => $tenant->id,
                'tenant_name'    => $tenant->name,
                'days_expired'   => $daysSince,
                'threshold_days' => $threshold,
            ]);

            $email = $tenant->owner?->email;
            if ($email && ! $tenant->getSetting('lifecycle.auto_suspend_sent')) {
                try {
                    Mail::to($email)->send(new WorkspaceSuspendedMail($tenant));
                    $this->setLifecycleFlag($tenant, 'lifecycle.auto_suspend_sent', true);
                } catch (\Throwable $e) {
                    Log::error("[lifecycle] Failed to send auto-suspend mail for tenant {$tenant->id}: {$e->getMessage()}");
                }
            }
        }
    }

    /**
     * Pick the right "expiry happened on this date" anchor for a
     * given tenant.  Trial expirations anchor on trial_ends_at;
     * paid-sub expirations anchor on subscription_ends_at.
     */
    protected function expiryAnchor(Tenant $tenant): ?Carbon
    {
        return match ($tenant->subscription_status) {
            \App\Enums\SubscriptionStatus::TrialExpired => $tenant->trial_ends_at,
            \App\Enums\SubscriptionStatus::Expired      => $tenant->subscription_ends_at,
            default                                     => null,
        };
    }

    /**
     * Persist a flag into the tenant's settings JSON column.
     */
    protected function setLifecycleFlag(Tenant $tenant, string $key, mixed $value): void
    {
        $settings = $tenant->settings ?? [];
        data_set($settings, $key, $value);
        $tenant->settings = $settings;
        $tenant->save();
    }
}
