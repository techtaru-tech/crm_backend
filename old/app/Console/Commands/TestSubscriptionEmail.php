<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Services\SubscriptionEventService;
use Illuminate\Console\Command;

/**
 * Fires a subscription event so the matching branded email actually
 * gets sent. Useful for validating template rendering on a fresh
 * install without having to simulate a real webhook.
 *
 * Usage:
 *   php artisan leadhub:test-subscription-email activated --tenant=3
 *   php artisan leadhub:test-subscription-email payment-failed --tenant=3
 */
class TestSubscriptionEmail extends Command
{
    protected $signature = 'leadhub:test-subscription-email
        {event : activated|cancelled|payment-failed|plan-changed}
        {--tenant= : Tenant ID (defaults to the first active tenant)}';

    protected $description = 'Fire a subscription event to verify notification emails render and deliver.';

    public function handle(SubscriptionEventService $events): int
    {
        $event = $this->argument('event');

        $tenant = $this->resolveTenant();
        if (! $tenant) {
            $this->error('No tenant found. Pass --tenant=ID.');
            return self::FAILURE;
        }

        $this->info("Firing '{$event}' for tenant #{$tenant->id} ({$tenant->name})");

        match ($event) {
            'activated'      => $events->activated($tenant, $tenant->plan, 'monthly'),
            'cancelled'      => $events->cancelled(
                $tenant,
                $tenant->subscription_ends_at?->format('F j, Y'),
                'Test cancellation',
            ),
            'payment-failed' => $events->paymentFailed($tenant, '49.00', 'USD', 1, now()->addDays(3)->format('F j, Y')),
            'plan-changed'   => $events->planChanged($tenant, $tenant->plan, 'pro'),
            default          => $this->error("Unknown event '{$event}'"),
        };

        $this->info('Event dispatched. If queues are configured, check the queue worker. Otherwise check your mail log.');
        return self::SUCCESS;
    }

    private function resolveTenant(): ?Tenant
    {
        if ($id = $this->option('tenant')) {
            return Tenant::with('owner')->find($id);
        }

        return Tenant::with('owner')->whereNotNull('owner_id')->orderBy('id')->first();
    }
}
