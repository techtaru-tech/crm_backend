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
    'url_empty'               => 'عنوان URL فارغ.',
    'url_missing_scheme_host' => 'يجب أن يتضمّن عنوان URL مخططًا ومضيفًا.',
    'scheme_not_allowed'      => 'يُسمح فقط بمخططَي http و https.',
    'host_not_routable'       => 'المضيف :host ليس وجهة قابلة للتوجيه.',
    'host_unresolvable'       => 'تعذّر استبيان المضيف :host.',
    'ipv6_not_allowed'        => 'وجهات IPv6 غير مسموح بها حاليًا.',
    'ip_unparseable'          => 'تعذّر تحليل عنوان IP :ip.',
    'ip_in_blocked_range'     => 'يُحلَّل عنوان IP :ip إلى نطاق خاص أو استرجاع أو بيانات تعريف محظور.',
];
