<x-filament-panels::page>
    {{--
        Static styles live in public/css/views/filament/pages/marketplace.css.
        Featured-card border color is conveyed via the .mp-card--featured
        modifier class (not inline) — this view contains zero inline styles.
    --}}
    {{-- Filter bar --}}
    <div class="mp-filters lh-mp-filters">
        <input type="text"
               wire:model.live.debounce.300ms="search"
               placeholder="{{ __('filament/marketplace.search_placeholder') }}"
               class="mp-filter-input">

        <select wire:model.live="typeFilter" class="mp-filter-select">
            <option value="">{{ __('filament/marketplace.all_types') }}</option>
            @foreach ($types as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
            @endforeach
        </select>

        <select wire:model.live="categoryFilter" class="mp-filter-select">
            <option value="">{{ __('filament/marketplace.all_categories') }}</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat }}">{{ $cat }}</option>
            @endforeach
        </select>

        <label class="mp-filter-checkbox-label">
            <input type="checkbox" wire:model.live="featuredOnly" class="mp-filter-checkbox">
            {{ __('filament/marketplace.featured_only') }}
        </label>
    </div>

    @if ($total === 0)
        <div class="mp-empty">
            <div class="mp-empty-icon">🛍️</div>
            <h3 class="mp-empty-title">
                @if ($search !== '' || $typeFilter || $categoryFilter || $featuredOnly)
                    {{ __('filament/marketplace.empty_no_matches_title') }}
                @else
                    {{ __('filament/marketplace.empty_no_templates_title') }}
                @endif
            </h3>
            <p class="mp-empty-body">
                @if ($search !== '' || $typeFilter || $categoryFilter || $featuredOnly)
                    {{ __('filament/marketplace.empty_no_matches_body') }}
                @else
                    {{ __('filament/marketplace.empty_no_templates_body') }}
                @endif
            </p>
        </div>
    @else
        <p class="mp-count">
            {{ trans_choice('filament/marketplace.templates_count', $total, ['count' => $total]) }}
        </p>

        <div class="mp-grid">
            @foreach ($templates as $template)
                {{-- border color is dynamic (featured vs neutral) — kept inline --}}
                <div class="mp-card{{ $template->is_featured ? ' mp-card--featured' : '' }}">
                    @if ($template->is_featured)
                        <span class="mp-featured-tag">{{ __('filament/marketplace.featured_tag') }}</span>
                    @endif

                    <div class="mp-card-header-row">
                        <span class="mp-type-pill">
                            {{ $types[$template->type] ?? $template->type }}
                        </span>
                        @if ($template->category)
                            <span class="mp-category-text">{{ $template->category }}</span>
                        @endif
                    </div>

                    <div>
                        <h3 class="mp-title">{{ $template->name }}</h3>
                        @if ($template->description)
                            <p class="mp-description">{{ $template->description }}</p>
                        @endif
                    </div>

                    <div class="mp-card-footer">
                        <div class="mp-owner-block">
                            @if ($template->owner)
                                {{ __('filament/marketplace.by_owner_prefix') }} <strong class="mp-owner-name">{{ $template->owner->name }}</strong>
                            @endif
                            <div class="mp-downloads-line">
                                {{ trans_choice('filament/marketplace.installs_count', (int) $template->downloads, ['count' => (int) $template->downloads]) }}
                                @if ($template->rating_avg > 0)
                                    · ⭐ {{ number_format((float) $template->rating_avg, 1) }}
                                @endif
                            </div>
                        </div>

                        <div class="mp-price-block">
                            @if ($template->isFree())
                                <div class="mp-price-free">{{ __('filament/marketplace.free_price') }}</div>
                            @else
                                <div class="mp-price">
                                    {{ number_format((float) $template->price, 2) }}
                                </div>
                                <div class="mp-price-currency">{{ $template->currency }}</div>
                            @endif
                        </div>
                    </div>

                    <button type="button"
                            wire:click="install({{ $template->id }})"
                            wire:confirm="{{ __('filament/marketplace.confirm_install_template', ['name' => $template->name]) }}"
                            class="mp-install-btn">
                        @if ($template->isFree())
                            {{ __('filament/marketplace.install_free_btn') }}
                        @else
                            {{ __('filament/marketplace.install_paid_btn', ['price' => number_format((float) $template->price, 2), 'currency' => $template->currency]) }}
                        @endif
                    </button>
                </div>
            @endforeach
        </div>
    @endif

    <link rel="stylesheet" href="{{ asset('css/views/filament/pages/marketplace.css') }}">
    <link rel="stylesheet" href="{{ asset('css/filament/marketplace.css') }}">
</x-filament-panels::page>
