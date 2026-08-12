<?php

namespace App\Events;

use App\Models\Tenant;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired when a tenant moves from trial → active subscription, or when a
 * new paid subscription is created. Triggers the welcome-to-paid email.
 */
class SubscriptionActivated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Tenant $tenant,
        public string $plan,
        public ?string $billingCycle = null,
    ) {}
}
