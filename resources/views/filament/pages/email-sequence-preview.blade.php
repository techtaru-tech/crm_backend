@php /** @var \Illuminate\Support\Collection $steps */ @endphp

{{-- Static styles extracted to public/css/views/filament/pages/email-sequence-preview.css
     (was a previously-inline <style> block — moved to satisfy the
     CodeCanyon "no inline styles unless dynamic" rule). --}}
<link rel="stylesheet" href="{{ asset('css/views/filament/pages/email-sequence-preview.css') }}">

<div class="lh-seq-list">
    @forelse($steps as $step)
        <div class="lh-seq-card">
            <div class="lh-seq-head">
                <div class="left">
                    <span class="lh-seq-num">{{ $step['step'] }}</span>
                    <span class="lh-seq-label">{{ __('filament/email_sequences.preview_delay_label') }}</span>
                    <span class="lh-seq-delay">{{ $step['delay'] }}</span>
                </div>
                <span class="lh-seq-meta">{{ __('filament/email_sequences.preview_sample_lead') }}</span>
            </div>
            <div class="lh-seq-body">
                <p class="lh-seq-subj">{{ $step['subject'] }}</p>
                {{-- Body comes from a TinyMCE editor where any tenant
                     admin can paste markup.  Layered defense:
                       1) stripTags allowlist blocks <script>/<style>/<iframe>
                       2) XSS fix: regex-strip event-handler
                          attributes (onclick=, onmouseover=, onerror=, …)
                          on allowlisted tags so a tenant who pastes
                          <a href="#" onclick="fetch(...)">click</a> can't
                          fire XSS on the preview render.
                       3) XSS fix: regex-strip javascript: / vbscript:
                          / data: URI schemes on href/src so
                          <a href="javascript:alert(1)">x</a> is defanged.
                       4) XSS fix: regex-strip CSS expressions
                          (style="expression(...)") on the off-chance an
                          old-IE-targeted payload is pasted.
                     When mews/purifier becomes a dependency, swap the
                     whole chain to Purifier::clean($step['body'], 'tinymce')
                     which handles attribute-level escaping correctly. --}}
                <div class="lh-seq-html">{!!
                    \Illuminate\Support\Str::of($step['body'] ?? '')
                        ->stripTags('<p><br><div><span><strong><b><em><i><u><s><a><h1><h2><h3><h4><h5><h6><ul><ol><li><blockquote><pre><img><table><thead><tbody><tr><td><th>')
                        ->replaceMatches('/\s+on[a-z]+\s*=\s*("[^"]*"|\x27[^\x27]*\x27|[^\s>]+)/i', '')
                        ->replaceMatches('/\s+(?:href|src|action|formaction|background|poster|cite|longdesc)\s*=\s*("|\x27)?\s*(?:javascript|vbscript|data|file)\s*:/i', ' data-blocked-uri=$1')
                        ->replaceMatches('/\s+style\s*=\s*("[^"]*expression\s*\([^"]*"|\x27[^\x27]*expression\s*\([^\x27]*\x27)/i', '')
                !!}</div>
            </div>
        </div>
    @empty
        <p class="lh-seq-empty">{{ __('filament/email_sequences.preview_no_steps') }}</p>
    @endforelse
</div>
