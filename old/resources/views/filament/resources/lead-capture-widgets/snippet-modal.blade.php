{{--
    snippet-modal.blade.php

    Content of the "Get Snippet" modal on the Lead Capture
    Widgets resource.  Shows the floating-widget embed script
    that tenants paste into any page of their site.

    Rendered from
    App\Filament\Resources\LeadCaptureWidgetResource::table() via
    ->modalContent(fn ($record) => new HtmlString(view(...)->render()))
    so Filament's modal slot accepts the rendered string.

    All static styles live in
    public/css/views/filament/resources/lead-capture-widgets/snippet-modal.css —
    inline style="" was previously a CodeCanyon-rule violation.

    Expected vars:
      $record  - App\Models\LeadCaptureWidget  (has ->embed_snippet)
--}}
<link rel="stylesheet" href="{{ asset('css/views/filament/resources/lead-capture-widgets/snippet-modal.css') }}">

<div class="lh-lcw-snippet-modal">
    <label class="lh-lcw-snippet-modal__label">
        {{ __('filament/lead_capture_widgets.snippet_label') }}
    </label>

    <div class="lh-lcw-snippet-modal__code-wrap">
        <pre class="lh-lcw-snippet-modal__code">{{ $record->embed_snippet }}</pre>
    </div>
</div>
