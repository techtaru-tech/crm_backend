<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('tenant.{tenantId}.leads', function ($user, $tenantId) {
    return (int) $user->tenant_id === (int) $tenantId;
});

Broadcast::channel('tenant.{tenantId}.messages', function ($user, $tenantId) {
    return (int) $user->tenant_id === (int) $tenantId;
});

Broadcast::channel('user.{userId}.notifications', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
