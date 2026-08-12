<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| DispatchOutboundWebhook job — DB-persisted response strings
|------------------------------------------------------------
| Used by app/Jobs/DispatchOutboundWebhook.php.
| Strings persist to webhook_deliveries.response_body and
| render in the webhook delivery log "Response" modal.
| Translate at write-time so the message renders in the
| dispatching user's locale.
|
| Accessed via __('jobs/dispatch_outbound_webhook.<key>').
*/

return [
    'blocked_prefix' => 'Blocked: :error',
];
