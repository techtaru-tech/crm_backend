<x-filament-panels::page>
    <link rel="stylesheet" href="{{ asset('css/views/filament/super-admin/pages/landing-page-editor.css') }}">
    <div class="lpe-hero">
        <p class="lpe-eyebrow">{{ __('filament/sa_landing_page_editor.hero_eyebrow') }}</p>
        <h2 class="lpe-title">{{ __('filament/sa_landing_page_editor.page_hero_title') }}</h2>
        <p class="lpe-body">{!! __('filament/sa_landing_page_editor.hero_body_html') !!}</p>
    </div>

    {{ $this->form }}
</x-filament-panels::page>
