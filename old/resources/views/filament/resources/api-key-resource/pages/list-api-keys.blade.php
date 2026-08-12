<x-filament-panels::page>
    <link rel="stylesheet" href="{{ asset('css/views/filament/resources/api-key-resource/pages/list-api-keys.css') }}">

    @if($newApiKey)
        <div
            x-data="{ copied: false, key: @js($newApiKey) }"
            class="lak-banner"
        >
            <div class="lak-banner-head">
                <svg class="lak-banner-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
                <span class="lak-banner-title">{{ __('filament/api_keys.banner_title') }}</span>
            </div>

            <p class="lak-banner-msg">
                {{ __('filament/api_keys.banner_msg_lede') }} <strong class="lak-banner-msg-strong">{{ __('filament/api_keys.banner_msg_only_once') }}</strong>
                {{ __('filament/api_keys.banner_msg_store_safe') }}
            </p>

            <div class="lak-banner-row">
                <input
                    type="text"
                    readonly
                    :value="key"
                    class="lak-key-input"
                    x-on:click="$el.select()"
                >
                <button
                    type="button"
                    x-on:click="navigator.clipboard.writeText(key).then(() => { copied = true; setTimeout(() => copied = false, 2500); })"
                    class="lak-copy-btn"
                    x-bind:class="copied ? 'lak-copy-btn-copied' : ''"
                >
                    <template x-if="!copied">
                        <svg class="lak-copy-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75"/>
                        </svg>
                    </template>
                    <template x-if="copied">
                        <svg class="lak-copy-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                        </svg>
                    </template>
                    <span x-text="copied ? @js(__('filament/api_keys.banner_copied_label')) : @js(__('filament/api_keys.banner_copy_button'))"></span>
                </button>
            </div>
        </div>
    @endif

    {{ $this->table }}
</x-filament-panels::page>
