<?php

namespace App\Events;

use App\Models\Tenant;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired when a tenant switches plans — upgrade, downgrade, or sidegrade.
 * The listener decides which email template to use based on direction.
 */
class PlanChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Tenant $tenant,
        public string $oldPlan,
        public string $newPlan,
        public string $direction = 'change', // upgrade, downgrade, change
    ) {}
}
