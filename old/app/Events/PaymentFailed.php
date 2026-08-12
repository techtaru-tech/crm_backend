<?php

namespace App\Events;

use App\Models\Tenant;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired when a scheduled payment attempt fails — usually off the back of
 * a webhook from Stripe/Paddle. Prompts the workspace owner to update
 * their payment method before the grace period runs out.
 */
class PaymentFailed
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Tenant $tenant,
        public ?string $amount = null,
        public ?string $currency = null,
        public int $retryAttempt = 1,
        public ?string $nextRetryAt = null,
    ) {}
}
