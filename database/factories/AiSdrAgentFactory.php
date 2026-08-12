<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\AiSdrAgent;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AiSdrAgent>
 */
class AiSdrAgentFactory extends Factory
{
    protected $model = AiSdrAgent::class;

    public function definition(): array
    {
        return [
            'tenant_id'                 => Tenant::factory(),
            'name'                      => 'Qualifier '.$this->faker->word(),
            'goal'                      => AiSdrAgent::GOAL_QUALIFY,
            'active'                    => true,
            'channel'                   => AiSdrAgent::CHANNEL_EMAIL,
            'persona'                   => 'a friendly B2B SDR',
            'offer_context'             => 'We sell a multi-tenant CRM for agencies.',
            'max_touches'               => 4,
            'min_hours_between_touches' => 24,
            'respect_business_hours'    => false,
            'assign_to_user_id'         => null,
            'meeting_type_id'           => null,
        ];
    }
}
