<x-filament-panels::page>
    <div class="ss-hero">
        <p class="ss-hero-eyebrow">{{ __('filament/sa_script_settings.hero_eyebrow') }}</p>
        <h2 class="ss-hero-title">{{ __('filament/sa_script_settings.page_hero_title') }}</h2>
        <p class="ss-hero-sub">{{ __('filament/sa_script_settings.hero_subtitle') }}</p>
    </div>

    {{ $this->form }}

    {{-- ── Cron setup guide (SuperAdmin / service operator only) ────── --}}
    @php
        $cronPath    = rtrim(base_path(), '/') . '/public/cron.php';
        $cronUrl     = rtrim(config('app.url'), '/') . '/cron.php';
        $cronSecret  = config('leadhub.cron_secret') ?: env('CRON_SECRET');
        $phpBinary   = PHP_BINARY ?: '/usr/bin/php';
    @endphp

    <div class="fi-section ss-section">
        <div class="ss-section-head">
            <div class="ss-section-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="ss-section-text">
                <h3 class="ss-section-title">{{ __('filament/sa_script_settings.cron_section_title') }}</h3>
                <p class="ss-section-desc">
                    {!! __('filament/sa_script_settings.cron_section_desc_html') !!}
                </p>
            </div>
        </div>

        <details class="ss-details">
            <summary class="ss-details-summary">{{ __('filament/sa_script_settings.cron_details_summary') }}</summary>
            <ul class="ss-details-list">
                <li><strong>{{ __('filament/sa_script_settings.cron_list_every_5_min_label') }}</strong> — {{ __('filament/sa_script_settings.cron_list_every_5_min_desc') }}</li>
                <li><strong>{{ __('filament/sa_script_settings.cron_list_every_15_min_label') }}</strong> — {{ __('filament/sa_script_settings.cron_list_every_15_min_desc') }}</li>
                <li><strong>{{ __('filament/sa_script_settings.cron_list_every_hour_label') }}</strong> — {{ __('filament/sa_script_settings.cron_list_every_hour_desc') }}</li>
                <li><strong>{{ __('filament/sa_script_settings.cron_list_every_6_hours_label') }}</strong> — {{ __('filament/sa_script_settings.cron_list_every_6_hours_desc') }}</li>
                <li><strong>{{ __('filament/sa_script_settings.cron_list_daily_02_label') }}</strong> — {{ __('filament/sa_script_settings.cron_list_daily_02_desc') }}</li>
                <li><strong>{{ __('filament/sa_script_settings.cron_list_daily_09_label') }}</strong> — {{ __('filament/sa_script_settings.cron_list_daily_09_desc') }}</li>
                <li><strong>{{ __('filament/sa_script_settings.cron_list_daily_user_label') }}</strong> — {{ __('filament/sa_script_settings.cron_list_daily_user_desc') }}</li>
            </ul>
        </details>

        <div class="ss-cron-block">
            <div class="ss-cron-head">
                <span class="ss-cron-badge">A</span>
                <strong class="ss-cron-label">{{ __('filament/sa_script_settings.cron_option_a_label') }}</strong>
                <span class="ss-cron-tag ss-cron-tag-easy">{{ __('filament/sa_script_settings.cron_option_a_tag') }}</span>
            </div>
            <p class="ss-cron-desc">{!! __('filament/sa_script_settings.cron_option_a_desc_html') !!}</p>
            <div class="ss-snippet-wrap">
                <code id="cron-shared" class="ss-snippet">* * * * * {{ $phpBinary }} {{ $cronPath }} >/dev/null 2>&amp;1</code>
                <button type="button" x-on:click="const el=document.getElementById('cron-shared');navigator.clipboard.writeText(el.innerText);$el.innerText=@js(__('filament/sa_script_settings.cron_copied'));setTimeout(()=>$el.innerText=@js(__('filament/sa_script_settings.cron_copy')),1500)" class="ss-copy-btn">{{ __('filament/sa_script_settings.cron_copy') }}</button>
            </div>
            <p class="ss-hint">{!! __('filament/sa_script_settings.cron_option_a_hint_html') !!}</p>
        </div>

        <div class="ss-cron-block">
            <div class="ss-cron-head">
                <span class="ss-cron-badge">B</span>
                <strong class="ss-cron-label">{{ __('filament/sa_script_settings.cron_option_b_label') }}</strong>
            </div>
            <p class="ss-cron-desc">{{ __('filament/sa_script_settings.cron_desc') }}</p>
            <div class="ss-snippet-wrap">
                <code id="cron-url" class="ss-snippet-break">{{ $cronUrl }}@if($cronSecret)?secret={{ $cronSecret }}@endif</code>
                <button type="button" x-on:click="const el=document.getElementById('cron-url');navigator.clipboard.writeText(el.innerText);$el.innerText=@js(__('filament/sa_script_settings.cron_copied'));setTimeout(()=>$el.innerText=@js(__('filament/sa_script_settings.cron_copy')),1500)" class="ss-copy-btn">{{ __('filament/sa_script_settings.cron_copy') }}</button>
            </div>
            @if($cronSecret)
                <p class="ss-hint">{!! __('filament/sa_script_settings.cron_option_b_secret_hint_html') !!}</p>
            @else
                <p class="ss-warn">{!! __('filament/sa_script_settings.cron_option_b_warn_html') !!}</p>
            @endif
        </div>

        <div class="ss-cron-block-last">
            <div class="ss-cron-head">
                <span class="ss-cron-badge">C</span>
                <strong class="ss-cron-label">{{ __('filament/sa_script_settings.cron_option_c_label') }}</strong>
                <span class="ss-cron-tag ss-cron-tag-rec">{{ __('filament/sa_script_settings.cron_option_c_tag') }}</span>
            </div>
            <p class="ss-cron-desc">{!! __('filament/sa_script_settings.cron_option_c_desc_html') !!}</p>
            <div class="ss-snippet-wrap">
                <code id="cron-native" class="ss-snippet">* * * * * cd {{ rtrim(base_path(), '/') }} &amp;&amp; {{ $phpBinary }} artisan schedule:run >> /dev/null 2>&amp;1</code>
                <button type="button" x-on:click="const el=document.getElementById('cron-native');navigator.clipboard.writeText(el.innerText);$el.innerText=@js(__('filament/sa_script_settings.cron_copied'));setTimeout(()=>$el.innerText=@js(__('filament/sa_script_settings.cron_copy')),1500)" class="ss-copy-btn">{{ __('filament/sa_script_settings.cron_copy') }}</button>
            </div>
            <p class="ss-hint">{!! __('filament/sa_script_settings.cron_option_c_hint_html') !!}</p>
        </div>

        <div class="ss-verify">
            <p class="ss-verify-text">{!! __('filament/sa_script_settings.cron_verify_text_html') !!}</p>
        </div>
    </div>

    <link rel="stylesheet" href="{{ asset('css/views/filament/super-admin/pages/script-settings.css') }}">
</x-filament-panels::page>
