<?php

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeadFactory extends Factory
{
    public function definition(): array
    {
        $phone = $this->faker->optional()->phoneNumber();
        return [
            'tenant_id'        => Tenant::factory(),
            'source'           => 'web_form',
            'source_id'        => null,
            'first_name'       => $this->faker->firstName(),
            'last_name'        => $this->faker->lastName(),
            'email'            => $this->faker->unique()->safeEmail(),
            'phone'            => $phone,
            'phone_normalized' => $phone ? preg_replace('/\D/', '', $phone) : null,
            'status'           => 'new',
            'is_duplicate'     => false,
        ];
    }
}
