<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TenantFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'     => $this->faker->company(),
            'slug'     => $this->faker->unique()->slug(),
            'domain'   => $this->faker->unique()->domainName(),
            'owner_id' => User::factory(),
            'api_key'  => \Illuminate\Support\Str::random(48),
            'active'   => true,
        ];
    }
}
