<?php

namespace App\Contracts;

use App\Models\LeadSourceConnection;
use App\Models\WebhookLog;
use Illuminate\Http\Request;

interface SourceConnector
{
    public function verifyWebhook(Request $request, LeadSourceConnection $connection): bool;

    public function handleWebhook(Request $request, LeadSourceConnection $connection, WebhookLog $log): array;

    public function getConnectionFormSchema(): array;

    public function testConnection(LeadSourceConnection $connection): bool;
}
