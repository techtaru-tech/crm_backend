<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| UrlSafetyGuard — SSRF guard exception messages
|--------------------------------------------------------------------------
|
| Translation strings used by UrlSafetyGuard / UnsafeUrlException.
| These messages bubble up to webhook_deliveries.response_body and the
| failed-jobs log, so tenants see them in the UI when their outbound
| URL is rejected. Accessed via __('support/url_safety.<key>').
|
*/

return [
    'url_empty'               => 'URL is empty.',
    'url_missing_scheme_host' => 'URL must include a scheme and host.',
    'scheme_not_allowed'      => 'Only http and https schemes are allowed.',
    'host_not_routable'       => 'Host :host is not a routable destination.',
    'host_unresolvable'       => 'Host :host could not be resolved.',
    'ipv6_not_allowed'        => 'IPv6 destinations are not currently allowed.',
    'ip_unparseable'          => 'Could not parse IP :ip.',
    'ip_in_blocked_range'     => 'IP :ip resolves to a blocked private/loopback/metadata range.',
];
