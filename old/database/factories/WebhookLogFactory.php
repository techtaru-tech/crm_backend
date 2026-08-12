<?php

namespace Database\Factories;

use App\Models\LeadSourceConnection;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class WebhookLogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tenant_id'            => Tenant::factory(),
            'source_connection_id' => LeadSourceConnection::factory(),
            'source'               => 'web_form',
            'status'               => 'pending',
            'payload'              => json_encode(['test' => true]),
            'headers'              => [],
        ];
    }
}
