<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        {{-- Live preview strip — mirrors the actual email layout
             so the operator sees exactly what the header + footer
             will look like before hitting Save. --}}
        @php
            $style       = $this->data['email_header_style']           ?? 'solid';
            $primary     = $this->data['email_header_color_primary']   ?? '#4f46e5';
            $secondary   = $this->data['email_header_color_secondary'] ?? '#6366f1';
            $angle       = (int) ($this->data['email_header_gradient_angle'] ?? 135);
            $footerBg    = $this->data['email_footer_color']           ?? '#f9fafb';
            $footerText  = $this->data['email_footer_text_color']      ?? '#6b7280';

            $headerBg = $style === 'gradient' && $secondary
                ? "linear-gradient({$angle}deg, {$primary} 0%, {$secondary} 100%)"
                : $primary;
        @endphp

        <div class="eb-preview">
            <div class="eb-preview-head">
                <p class="eb-preview-title">{{ __('filament/sa_email_branding.preview_title') }}</p>
                <p class="eb-preview-sub">{{ __('filament/sa_email_branding.preview_subtitle') }}</p>
            </div>
            <div class="eb-preview-body">
                <div class="eb-email">
                    {{-- Dynamic style retained: header background varies by user's solid/gradient picker. --}}
                    <div class="eb-email-header-pad" style="background:{{ $headerBg }};">
                        <div class="eb-logo-wrap">
                            <div class="eb-logo-square"></div>
                            <span class="eb-logo-name">{{ config('leadhub.branding.app_name', 'LeadHub') }}</span>
                        </div>
                    </div>
                    <div class="eb-email-body">
                        <p class="eb-email-body-p">{{ __('filament/sa_email_branding.preview_sample_greeting') }}</p>
                        <p class="eb-email-body-p-last">{{ __('filament/sa_email_branding.preview_sample_body') }}</p>
                    </div>
                    {{-- Dynamic style retained: footer background + text color come from user pickers. --}}
                    <div class="eb-email-footer-pad" style="background:{{ $footerBg }};color:{{ $footerText }};">
                        <p class="eb-email-footer-name" style="color:{{ $footerText }};">{{ config('leadhub.branding.app_name', 'LeadHub') }}</p>
                        {{ __('filament/sa_email_branding.preview_footer_reason', ['app' => config('leadhub.branding.app_name', 'LeadHub')]) }}
                    </div>
                </div>
            </div>
        </div>

        <div class="eb-actions">
            <x-filament::button type="submit">
                {{ __('filament/sa_email_branding.action_save') }}
            </x-filament::button>
        </div>
    </form>

    <link rel="stylesheet" href="{{ asset('css/views/filament/super-admin/pages/email-branding.css') }}">
</x-filament-panels::page>
