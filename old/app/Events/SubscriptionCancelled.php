<?php

namespace App\Events;

use App\Models\Tenant;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired when a tenant cancels their subscription. Access usually
 * continues until `$endsAt`, after which the lifecycle cron takes over.
 */
class SubscriptionCancelled
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Tenant $tenant,
        public ?string $endsAt = null,
        public ?string $reason = null,
    ) {}
}
