{{-- Note:
     This is a TENANT-CMS section.  The HTML rendered below is supplied
     verbatim by the tenant via the editor — any inline style="" or
     <style> tags inside $content['code'] are user-controlled content,
     not maintained by this codebase.  We intentionally do NOT refactor
     those inline styles out into CSS files because:
       1. We do not own them — they belong to the tenant's page.
       2. Doing so would break the editor contract (WYSIWYG → output).

     Hardening: the prior sanitizer stripped only <script> and
     on*= handlers, which left a wide XSS surface open via <iframe>,
     <object>, <embed>, <link>, <base>, <meta http-equiv>, javascript:
     URI schemes, <style>{expression() / @import url(javascript:)}, and
     nested-tag bypass (e.g. <scr<script>ipt>).  Hardened to a multi-
     layer blocklist that defangs the common public-facing XSS sinks.
     This is STILL not a real HTML purifier (HTMLPurifier or DOMPurify
     equivalent would be the correct long-term fix — a `composer require
     ezyang/htmlpurifier` upgrade is recommended).  The layered defense
     below is defensive against common security audit checklists; a determined attacker may still find encoded-bypass
     paths that only a real parser-based purifier closes.  --}}
@php
    $code = $content['code'] ?? '';
    if (is_string($code) && $code !== '') {
        // Layer 1: strip <script> tags (open + self-closing).  Run twice
        // to defeat nested-tag bypass like <scr<script>ipt>foo</script>.
        for ($pass = 0; $pass < 2; $pass++) {
            $code = preg_replace('#<script\b[^>]*>.*?</script>#is', '', $code);
            $code = preg_replace('#<script\b[^>]*/?>#is', '', $code);
        }

        // Layer 2: strip dangerous tags wholesale (rare in legitimate
        // tenant content; always dangerous when an attacker plants them).
        // Includes <style> (CSS expression()/@import attacks),
        // <iframe>/<frame>/<frameset> (clickjack + XSS),
        // <object>/<embed>/<applet> (plugin XSS),
        // <link>/<base>/<meta> (CSP-relative redirects, base-tag hijack),
        // <form>/<input>/<button> (DOM clobbering, hidden-form phishing),
        // <svg>/<math> (XML-namespace XSS sinks).
        $dangerousTags = '(?:style|iframe|frame|frameset|object|embed|applet|link|base|meta|form|input|button|textarea|select|option|svg|math|template|portal|xmp|plaintext|noembed|noscript)';
        for ($pass = 0; $pass < 2; $pass++) {
            $code = preg_replace('#<' . $dangerousTags . '\b[^>]*>.*?</' . $dangerousTags . '>#is', '', $code);
            $code = preg_replace('#<' . $dangerousTags . '\b[^>]*/?>#is', '', $code);
        }

        // Layer 3: strip event-handler attributes on any tag, all
        // quote styles + unquoted form (e.g. `onclick="..."`,
        // `onerror=alert(1)`).
        $code = preg_replace('#\son[a-z]+\s*=\s*"[^"]*"#i', '', $code);
        $code = preg_replace("#\son[a-z]+\s*=\s*'[^']*'#i", '', $code);
        $code = preg_replace('#\son[a-z]+\s*=\s*[^\s>]+#i', '', $code);

        // Layer 4: defang dangerous URI schemes on URL-bearing attrs.
        // Covers href, src, action, formaction, background, poster,
        // cite, longdesc, srcset, data, manifest, ping, archive.
        $code = preg_replace(
            '#\s+(href|src|action|formaction|background|poster|cite|longdesc|srcset|data|manifest|ping|archive)\s*=\s*("|\')?\s*(?:javascript|vbscript|data|file)\s*:#i',
            ' data-blocked-uri-$1=$2',
            $code
        );
    }
@endphp
<section class="lp-section lp-html">
<div class="lp-wrap">
    {!! $code !!}
</div>
</section>
