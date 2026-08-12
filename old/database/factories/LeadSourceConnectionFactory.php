<?php

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeadSourceConnectionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tenant_id'   => Tenant::factory(),
            'source'      => 'web_form',
            'name'        => $this->faker->words(3, true),
            'active'      => true,
            'credentials' => [],
            'settings'    => [],
        ];
    }
}
