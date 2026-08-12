<x-filament-panels::page>
    <link rel="stylesheet" href="{{ asset('css/views/filament/pages/settings/domain-settings-page.css') }}">

    @php $appHost = parse_url(config('app.url'), PHP_URL_HOST); @endphp

    {{-- DNS Instructions --}}
    @if($existingDomain)
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 ds-section">
            <div class="ds-header">
                <h3 class="ds-title">{{ __('filament/domain_settings.current_domain_label') }} <code class="ds-code">{{ $existingDomain->domain }}</code></h3>
                @if($existingDomain->isVerified())
                    <span class="ds-pill ds-pill-verified">{{ __('filament/domain_settings.status_verified') }}</span>
                @else
                    <span class="ds-pill ds-pill-pending">{{ __('filament/domain_settings.status_pending') }}</span>
                @endif
            </div>

            <div class="ds-panel">
                <p class="ds-panel-lede">{{ __('filament/domain_settings.dns_record_lede') }}</p>
                <table class="ds-table">
                    <thead>
                        <tr class="ds-table-head">
                            <th>{{ __('filament/domain_settings.col_type') }}</th>
                            <th>{{ __('filament/domain_settings.col_name') }}</th>
                            <th>{{ __('filament/domain_settings.col_value') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="ds-table-row">
                            <td>{{ __('filament/domain_settings.dns_record_type_cname') }}</td>
                            <td>{{ $existingDomain->domain }}</td>
                            <td>{{ $appHost }}</td>
                        </tr>
                    </tbody>
                </table>
                @if($existingDomain->verification_token)
                {{-- XSS fix: $existingDomain->domain is tenant-admin
                     typed input; __() does NOT escape :placeholder
                     substitutions; this paragraph renders raw via {!! !!}
                     because the lang string itself contains trusted
                     <code> wrappers.  e() each user value before
                     substitution so a tenant who saves
                     "<img src=x onerror=…>" as their domain can't fire
                     stored-XSS on this admin page. --}}
                <p class="ds-dns-hint">{!! __('filament/domain_settings.dns_txt_hint_html', ['domain' => e($existingDomain->domain), 'token' => e($existingDomain->verification_token)]) !!}</p>
                @endif
                <p class="ds-dns-hint">{{ __('filament/domain_settings.dns_propagation_hint') }}</p>
            </div>
        </div>
    @endif

    {{-- NOTE: the next style="..." is DYNAMIC — its
         value is computed from $existingDomain at render time, so it
         is acceptable per the "no inline styles unless dynamic" rule. --}}
    <form wire:submit="save" style="{{ $existingDomain ? 'margin-top:1.5rem' : '' }}">
        {{ $this->form }}
        <div class="ds-form-actions">
            <x-filament::button type="submit" color="primary">{{ __('filament/domain_settings.save_domain') }}</x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
