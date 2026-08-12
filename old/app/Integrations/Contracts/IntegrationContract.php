<?php

namespace App\Integrations\Contracts;

use App\Models\Lead;

interface IntegrationContract
{
    public function connect(array $config): void;

    public function testConnection(): bool;

    public function pushLead(Lead $lead, array $fieldMapping = [], array $filterConfig = []): array;

    public function getAvailableFields(): array;
}
