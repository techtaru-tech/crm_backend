<x-filament-panels::page>
    {{--
        Static styles live in public/css/views/filament/pages/billing/cancel-subscription.css.
        No dynamic interpolations in this view.
    --}}
    <form wire:submit="cancel" class="cs-form">

        {{-- Reason picker + feedback --}}
        {{ $this->form }}

        {{-- Retention offer (conditional) --}}
        @if ($this->showsRetentionOffer())
            @php $offer = $this->getRetentionOfferCopy(); @endphp
            <div class="cs-retention-offer">
                <div class="cs-retention-row">
                    <div class="cs-retention-icon">
                        💡
                    </div>
                    <div class="cs-retention-body">
                        <h3 class="cs-retention-title">{{ $offer['title'] }}</h3>
                        <p class="cs-retention-text">{{ $offer['body'] }}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Final action row --}}
        <div class="cs-actions">
            <a href="/admin/billing" class="cs-btn-keep">
                {{ __('filament/cancel_subscription.keep_subscription') }}
            </a>

            <button type="submit" class="cs-btn-cancel">
                {{ __('filament/cancel_subscription.cancel_subscription') }}
            </button>
        </div>

        <p class="cs-footer-hint">
            {{ __('filament/cancel_subscription.footer_hint') }}
        </p>
    </form>

    <link rel="stylesheet" href="{{ asset('css/views/filament/pages/billing/cancel-subscription.css') }}">
</x-filament-panels::page>
