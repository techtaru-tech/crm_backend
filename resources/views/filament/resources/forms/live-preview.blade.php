{{--
    live-preview.blade.php

    Content of the "Live Preview" Section on the FormResource
    edit page.  Renders a browser-chrome wrapper + an iframe that
    loads the public form so editors see exactly what visitors
    see, in real time, without leaving the admin.

    Rendered from
    App\Filament\Resources\FormResource::form() via
    ->content(fn ($record) => new HtmlString(view(...)->render()))
    so Filament's Placeholder slot accepts the rendered string.

    All static styles live in
    public/css/views/filament/resources/forms/live-preview.css —
    inline style="" was previously a CodeCanyon-rule violation.

    Expected vars:
      $record  - App\Models\Form  (has ->public_url)
--}}
<link rel="stylesheet" href="{{ asset('css/views/filament/resources/forms/live-preview.css') }}">

<div class="lh-form-live-preview w-full rounded-xl overflow-hidden border border-gray-200 shadow-sm bg-white">
    <div class="bg-gray-100 px-4 py-2 flex items-center gap-2 border-b border-gray-200">
        <span class="w-3 h-3 rounded-full bg-red-400 inline-block"></span>
        <span class="w-3 h-3 rounded-full bg-yellow-400 inline-block"></span>
        <span class="w-3 h-3 rounded-full bg-green-400 inline-block"></span>
        <span class="ml-2 text-xs text-gray-500 truncate">{{ $record->public_url }}</span>
        <a href="{{ $record->public_url }}" target="_blank" rel="noopener noreferrer" class="ml-auto text-xs text-primary-600 underline">
            {{ __('filament/forms.live_preview_open_in_new_tab') }}
        </a>
    </div>
    <iframe
        src="{{ $record->public_url }}"
        class="w-full lh-form-live-preview__iframe"
        loading="lazy"
        title="{{ __('filament/forms.live_preview_iframe_title') }}"
    ></iframe>
</div>
