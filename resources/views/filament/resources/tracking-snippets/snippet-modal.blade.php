{{--
    snippet-modal.blade.php

    Content of the "Get Snippet" modal on the Web Tracking
    (TrackingSnippet) resource.  Shows the embed snippet that
    tenants paste into the <head> of their site, plus the
    LeadHubTracker.identify() helper for tying a visitor to a
    known lead after signup.

    Rendered from
    App\Filament\Resources\TrackingSnippetResource::table() via
    ->modalContent(fn ($record) => new HtmlString(view(...)->render()))
    so Filament's modal slot accepts the rendered string.

    All static styles live in
    public/css/views/filament/resources/tracking-snippets/snippet-modal.css —
    inline style="" was previously a CodeCanyon-rule violation.

    Expected vars:
      $record  - App\Models\TrackingSnippet  (has ->embed_snippet)
--}}
<link rel="stylesheet" href="{{ asset('css/views/filament/resources/tracking-snippets/snippet-modal.css') }}">

<div class="lh-tracking-snippet-modal">
    <label class="lh-tracking-snippet-modal__label">
        {{ __('filament/tracking_snippets.snippet_embed_label') }}
    </label>

    <pre class="lh-tracking-snippet-modal__code">{{ $record->embed_snippet }}</pre>

    <p class="lh-tracking-snippet-modal__intro">
        {{ __('filament/tracking_snippets.snippet_identify_intro') }}
    </p>

    <pre class="lh-tracking-snippet-modal__code--inline">LeadHubTracker.identify("user@example.com", "+15551234567");</pre>
</div>
