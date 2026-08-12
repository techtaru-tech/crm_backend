<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ChatbotConfig;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ChatbotConfig>
 */
class ChatbotConfigFactory extends Factory
{
    protected $model = ChatbotConfig::class;

    public function definition(): array
    {
        return [
            'uuid'              => Str::uuid()->toString(),
            'tenant_id'        => Tenant::factory(),
            'enabled'          => true,
            'name'             => 'Support Bot',
            'persona'          => 'A friendly, concise support assistant.',
            'knowledge'        => "Q: Do you offer a free trial?\nA: Yes, 14 days.",
            'greeting'         => 'Hi! How can I help?',
            'primary_color'    => '#4f46e5',
            'daily_message_cap'=> 500,
        ];
    }

    public function disabled(): static
    {
        return $this->state(fn () => ['enabled' => false]);
    }
}
