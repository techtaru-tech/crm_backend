<x-filament-panels::page>
    <link rel="stylesheet" href="{{ asset('css/filament/list-integrations.css') }}">

    <div
        x-data="{
            showModal: @entangle('setupModalOpen'),
            showPasswordFields: {},
            togglePassword(key) {
                this.showPasswordFields[key] = !this.showPasswordFields[key];
            }
        }"
    >
        {{-- Category tabs.  $cat is the canonical English key used as the
            wire-method argument so PHP-side matching stays stable across
            locales; the visible label is routed through the translator via
            the registry's slug map. --}}
        <div class="ig-tabs">
            @foreach(array_keys($this->integrationsByCategory) as $cat)
                @php
                    $categorySlug  = \App\Integrations\IntegrationRegistry::CATEGORY_SLUGS[$cat] ?? 'other';
                    $categoryLabel = __('integrations_registry.categories.' . $categorySlug);
                    $hasTrans      = $categoryLabel !== 'integrations_registry.categories.' . $categorySlug;
                @endphp
                <button
                    wire:click="setActiveCategory('{{ $cat }}')"
                    class="ig-tab {{ $this->activeCategory === $cat ? 'ig-tab-active' : 'ig-tab-inactive' }}"
                >
                    {{ $hasTrans ? $categoryLabel : $cat }}
                    <span class="ig-tab-count">({{ count($this->integrationsByCategory[$cat] ?? []) }})</span>
                </button>
            @endforeach
        </div>

        {{-- Integration cards grid --}}
        @foreach($this->integrationsByCategory as $category => $items)
            @if($this->activeCategory === $category)
                <div class="ig-grid">
                    @foreach($items as $item)
                        @php $integration = $item['integration']; @endphp
                        <div class="ig-card">
                            {{-- Status badge --}}
                            @if($integration)
                                @if($integration['status'] === 'connected')
                                    <span class="ig-status ig-status-connected"><span class="ig-status-dot ig-status-dot-connected"></span> {{ __('filament/integrations.status_connected_label') }}</span>
                                @elseif($integration['status'] === 'error')
                                    <span class="ig-status ig-status-error"><span class="ig-status-dot ig-status-dot-error"></span> {{ __('filament/integrations.status_error_label') }}</span>
                                @else
                                    <span class="ig-status ig-status-inactive"><span class="ig-status-dot ig-status-dot-inactive"></span> {{ __('filament/integrations.status_inactive_label') }}</span>
                                @endif
                            @endif

                            {{-- Icon area --}}
                            <div class="ig-icon-box">
                                <svg class="ig-icon-svg" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.25 6.087c0-.355.186-.676.401-.959.221-.29.349-.634.349-1.003 0-1.036-1.007-1.875-2.25-1.875s-2.25.84-2.25 1.875c0 .369.128.713.349 1.003.215.283.401.604.401.959v0a.64.64 0 01-.657.643 48.39 48.39 0 01-4.163-.3c.186 1.613.293 3.25.315 4.907a.656.656 0 01-.658.663v0c-.355 0-.676-.186-.959-.401a1.647 1.647 0 00-1.003-.349c-1.036 0-1.875 1.007-1.875 2.25s.84 2.25 1.875 2.25c.369 0 .713-.128 1.003-.349.283-.215.604-.401.959-.401v0c.31 0 .555.26.532.57a48.039 48.039 0 01-.642 5.056c1.518.19 3.058.309 4.616.354a.64.64 0 00.657-.643v0c0-.355-.186-.676-.401-.959a1.647 1.647 0 01-.349-1.003c0-1.035 1.008-1.875 2.25-1.875 1.243 0 2.25.84 2.25 1.875 0 .369-.128.713-.349 1.003-.215.283-.401.604-.401.959v0c0 .333.277.599.61.58a48.1 48.1 0 005.427-.63 48.05 48.05 0 00.582-4.717.532.532 0 00-.533-.57v0c-.355 0-.676.186-.959.401-.29.221-.634.349-1.003.349-1.035 0-1.875-1.007-1.875-2.25s.84-2.25 1.875-2.25c.37 0 .713.128 1.003.349.283.215.604.401.959.401v0a.656.656 0 00.658-.663 48.422 48.422 0 00-.37-5.36c-1.886.342-3.81.574-5.766.689a.578.578 0 01-.61-.58v0z"/></svg>
                            </div>

                            {{-- Name + description --}}
                            <div class="ig-card-title">{{ $item['label'] }}</div>
                            <div class="ig-card-desc">{{ $item['description'] }}</div>

                            {{-- Actions --}}
                            @if($integration)
                                <div class="ig-card-actions">
                                    {{-- Enable toggle --}}
                                    <button
                                        wire:click="toggleEnabled({{ $integration['id'] }})"
                                        class="ig-toggle {{ $integration['enabled'] ? 'ig-toggle-on' : 'ig-toggle-off' }}"
                                        title="{{ $integration['enabled'] ? __('filament/integrations.action_disable') : __('filament/integrations.action_enable') }}"
                                    >
                                        <span class="ig-toggle-knob {{ $integration['enabled'] ? 'ig-toggle-knob-on' : 'ig-toggle-knob-off' }}"></span>
                                    </button>

                                    <div class="ig-action-row">
                                        {{-- Test connection --}}
                                        <button wire:click="testConnection({{ $integration['id'] }})" class="ig-action-btn" title="{{ __('filament/integrations.action_test_connection') }}">
                                            <svg class="ig-icon-sm" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.348 14.652a3.75 3.75 0 010-5.304m5.304 0a3.75 3.75 0 010 5.304m-7.425 2.121a6.75 6.75 0 010-9.546m9.546 0a6.75 6.75 0 010 9.546M5.106 18.894c-3.808-3.807-3.808-9.98 0-13.788m13.788 0c3.808 3.807 3.808 9.98 0 13.788M12 12h.008v.008H12V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
                                        </button>
                                        {{-- Edit --}}
                                        <button wire:click="openSetupModal('{{ $item['type'] }}')" class="ig-action-btn" title="{{ __('filament/integrations.action_configure') }}">
                                            <svg class="ig-icon-sm" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 010-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        </button>
                                        {{-- Sync logs --}}
                                        <a href="{{ \App\Filament\Resources\IntegrationResource::getUrl('sync-logs', ['record' => $integration['id']]) }}" class="ig-action-btn" title="{{ __('filament/integrations.action_sync_logs_title') }}">
                                            <svg class="ig-icon-sm" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </a>
                                        {{-- Delete --}}
                                        <button wire:click="deleteIntegration({{ $integration['id'] }})" wire:confirm="{{ __('filament/integrations.confirm_remove_integration') }}" class="ig-action-btn ig-action-btn-danger" title="{{ __('filament/integrations.action_remove') }}">
                                            <svg class="ig-icon-sm" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                        </button>
                                    </div>
                                </div>

                                @if($integration['last_synced_at'] && $integration['last_synced_at'] !== 'Never')
                                    <p class="ig-last-sync">{{ __('filament/integrations.last_sync_prefix', ['time' => $integration['last_synced_at']]) }}</p>
                                @endif
                            @else
                                <button wire:click="openSetupModal('{{ $item['type'] }}')" class="ig-connect-btn">{{ __('filament/integrations.btn_connect') }}</button>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        @endforeach

        {{-- Setup slide-over modal --}}
        <div
            x-show="showModal"
            x-cloak
            class="ig-overlay"
        >
            {{-- Backdrop --}}
            <div
                class="ig-backdrop"
                @click="showModal = false"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
            ></div>

            {{-- Panel --}}
            <div
                class="ig-panel"
                x-transition:enter="transform transition ease-out duration-300"
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transform transition ease-in duration-200"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
            >
                {{-- Header --}}
                <div class="ig-panel-header">
                    <div>
                        <h2 class="ig-panel-title">
                            {{-- Translator-routed brand label + category. The const arrays
                                are slug/match-key sources; UI display goes through the
                                getLabel/getCategoryLabel accessors so non-English locales
                                see localised copy. --}}
                            {{ __('filament/integrations.setup_title_prefix', ['type' => \App\Integrations\IntegrationRegistry::getLabel($this->setupType)]) }}
                        </h2>
                        <p class="ig-panel-subtitle">
                            {{ $this->setupType ? \App\Integrations\IntegrationRegistry::getCategoryLabel($this->setupType) : '' }}
                        </p>
                    </div>
                    <button @click="showModal = false" class="ig-icon-btn-close">
                        <svg class="ig-icon-svg" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Body --}}
                <div class="ig-panel-body">
                    {{-- Display name --}}
                    <div>
                        <label class="ig-label">{{ __('filament/integrations.display_name') }}</label>
                        <input type="text" wire:model="setupName" class="ig-input"/>
                    </div>

                    {{-- Enable toggle --}}
                    <div class="ig-enable-row">
                        <button
                            wire:click="$toggle('setupEnabled')"
                            class="ig-toggle {{ $this->setupEnabled ? 'ig-toggle-on' : 'ig-toggle-off' }}"
                        >
                            <span class="ig-toggle-knob {{ $this->setupEnabled ? 'ig-toggle-knob-on' : 'ig-toggle-knob-off' }}"></span>
                        </button>
                        <span class="ig-enable-label">{{ __('filament/integrations.enable_this_integration') }}</span>
                    </div>

                    {{-- Dynamic config fields --}}
                    @php $configFields = \App\Integrations\IntegrationRegistry::getConfigFields($this->setupType); @endphp

                    @if(count($configFields) > 0)
                        <div class="ig-divider-row">
                            <h3 class="ig-section-title">{{ __('filament/integrations.connection_settings_heading') }}</h3>
                            <div class="ig-col-gap-sm">
                                @foreach($configFields as $field)
                                    <div>
                                        <label class="ig-label">
                                            {{ $field['label'] }}
                                            @if($field['required'] ?? false) <span class="ig-required-asterisk">*</span> @endif
                                        </label>

                                        @if($field['type'] === 'oauth_button')
                                            @php
                                                $oauthProvider    = $field['provider'] ?? $this->setupType;
                                                $oauthConnected   = ($this->setupConfig['oauth_connected'] ?? false);
                                                $oauthRedirectUrl = route('integration.oauth.redirect', ['type' => $oauthProvider])
                                                    . ($this->editingId ? '?integration_id=' . $this->editingId : '');
                                            @endphp
                                            <div class="ig-oauth-row">
                                                @if($oauthConnected)
                                                    <span class="ig-oauth-pill">
                                                        <svg class="ig-oauth-pill-svg" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                        {{ __('filament/integrations.oauth_connected_pill') }}
                                                    </span>
                                                @endif
                                                <a href="{{ $oauthRedirectUrl }}" class="ig-btn-primary ig-oauth-btn-row">
                                                    <svg class="ig-icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                                    {{ $oauthConnected ? __('filament/integrations.oauth_reconnect') : __('filament/integrations.oauth_connect') }}
                                                </a>
                                                <p class="ig-oauth-hint">{{ __('filament/integrations.oauth_hint') }}</p>
                                            </div>
                                        @elseif($field['type'] === 'select')
                                            <select wire:model="setupConfig.{{ $field['key'] }}" class="ig-input">
                                                @foreach($field['options'] ?? [] as $val => $label)
                                                    <option value="{{ $val }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        @elseif($field['type'] === 'textarea')
                                            <textarea wire:model="setupConfig.{{ $field['key'] }}" rows="3" placeholder="{{ $field['placeholder'] ?? '' }}" class="ig-input"></textarea>
                                        @elseif($field['type'] === 'password')
                                            <div class="ig-password-wrap">
                                                <input
                                                    :type="showPasswordFields['{{ $field['key'] }}'] ? 'text' : 'password'"
                                                    wire:model="setupConfig.{{ $field['key'] }}"
                                                    placeholder="{{ $field['placeholder'] ?? '' }}"
                                                    class="ig-input ig-input-with-eye"
                                                />
                                                <button
                                                    type="button"
                                                    @click="togglePassword('{{ $field['key'] }}')"
                                                    class="ig-eye-btn"
                                                >
                                                    <svg class="ig-icon-sm" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                </button>
                                            </div>
                                        @else
                                            <input
                                                type="{{ $field['type'] === 'email' ? 'email' : ($field['type'] === 'url' ? 'url' : 'text') }}"
                                                wire:model="setupConfig.{{ $field['key'] }}"
                                                placeholder="{{ $field['placeholder'] ?? ($field['default'] ?? '') }}"
                                                class="ig-input"
                                            />
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Field Mapping --}}
                    <div class="ig-divider-row">
                        <h3 class="ig-section-title">{{ __('filament/integrations.field_mapping_heading') }} <span class="ig-section-muted">{{ __('filament/integrations.field_optional_suffix') }}</span></h3>
                        <p class="ig-section-desc">{{ __('filament/integrations.field_mapping_desc', ['app' => config('leadhub.branding.app_name', 'LeadHub')]) }}</p>

                        <div class="ig-col-gap-xs">
                            @foreach($this->setupFieldMapping as $idx => $row)
                                <div class="ig-mapping-row">
                                    <select wire:model="setupFieldMapping.{{ $idx }}.source_field" class="ig-mapping-select">
                                        <option value="">{{ __('filament/integrations.select_source_field', ['app' => config('leadhub.branding.app_name', 'LeadHub')]) }}</option>
                                        @foreach([
                                            'first_name' => __('filament/integrations.lh_field_first_name'),
                                            'last_name'  => __('filament/integrations.lh_field_last_name'),
                                            'email'      => __('filament/integrations.lh_field_email'),
                                            'phone'      => __('filament/integrations.lh_field_phone'),
                                            'company'    => __('filament/integrations.lh_field_company'),
                                            'source'     => __('filament/integrations.lh_field_source'),
                                            'status'     => __('filament/integrations.lh_field_status'),
                                            'lead_score' => __('filament/integrations.lh_field_lead_score'),
                                            'address'    => __('filament/integrations.lh_field_address'),
                                            'city'       => __('filament/integrations.lh_field_city'),
                                            'country'    => __('filament/integrations.lh_field_country'),
                                            'notes'      => __('filament/integrations.lh_field_notes'),
                                        ] as $val => $lbl)
                                            <option value="{{ $val }}">{{ $lbl }}</option>
                                        @endforeach
                                    </select>
                                    <span class="ig-arrow-muted">→</span>
                                    @if(count($this->availableTargetFields) > 0)
                                        <select wire:model="setupFieldMapping.{{ $idx }}.target_field" class="ig-mapping-select">
                                            <option value="">{{ __('filament/integrations.select_target_field') }}</option>
                                            @foreach($this->availableTargetFields as $fieldKey => $fieldLabel)
                                                <option value="{{ $fieldKey }}">{{ $fieldLabel }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input type="text" wire:model="setupFieldMapping.{{ $idx }}.target_field" placeholder="{{ __('filament/integrations.target_field_input_placeholder') }}" class="ig-mapping-select"/>
                                    @endif
                                    <button
                                        wire:click="$set('setupFieldMapping', array_values(array_filter($setupFieldMapping, fn($k) => $k !== {{ $idx }}, ARRAY_FILTER_USE_KEY)))"
                                        class="ig-remove-mapping-btn"
                                    >
                                        <svg class="ig-icon-sm" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>

                        <button
                            wire:click="$set('setupFieldMapping', array_merge($setupFieldMapping, [['source_field' => '', 'target_field' => '']]))"
                            class="ig-add-mapping-btn"
                        >
                            {{ __('filament/integrations.btn_add_mapping') }}
                        </button>
                    </div>

                    {{-- Source filter --}}
                    <div class="ig-divider-row">
                        <h3 class="ig-section-title">{{ __('filament/integrations.source_filter_heading') }} <span class="ig-section-muted">{{ __('filament/integrations.field_optional_suffix') }}</span></h3>
                        <p class="ig-section-desc">{{ __('filament/integrations.source_filter_desc') }}</p>
                        <div class="ig-checkbox-wrap">
                            @foreach(\App\Enums\LeadSource::cases() as $src)
                                <label class="ig-checkbox-label">
                                    <input type="checkbox" value="{{ $src->value }}" wire:model="setupFilterSources" class="ig-checkbox-input"/>
                                    {{ $src->label() }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Tag filter --}}
                    @php
                        $tenantId      = auth()->user()?->tenant_id;
                        $tenantTags    = \App\Models\Tag::where('tenant_id', $tenantId)->orderBy('name')->get();
                        $tenantPipelines = \App\Models\Pipeline::where('tenant_id', $tenantId)->orderBy('name')->get();
                    @endphp
                    <div class="ig-divider-row">
                        <h3 class="ig-section-title">{{ __('filament/integrations.tag_filter_heading') }} <span class="ig-section-muted">{{ __('filament/integrations.field_optional_suffix') }}</span></h3>
                        <p class="ig-section-desc">{{ __('filament/integrations.tag_filter_desc') }}</p>
                        <div class="ig-checkbox-wrap">
                            @foreach($tenantTags as $tag)
                                <label class="ig-checkbox-label">
                                    <input type="checkbox" value="{{ $tag->name }}" wire:model="setupFilterTags" class="ig-checkbox-input"/>
                                    {{ $tag->name }}
                                </label>
                            @endforeach
                            @if($tenantTags->isEmpty())
                                <p class="ig-empty-hint">{{ __('filament/integrations.no_tags_created') }}</p>
                            @endif
                        </div>
                    </div>

                    {{-- Pipeline filter --}}
                    <div class="ig-divider-row">
                        <h3 class="ig-section-title">{{ __('filament/integrations.pipeline_filter_heading') }} <span class="ig-section-muted">{{ __('filament/integrations.field_optional_suffix') }}</span></h3>
                        <p class="ig-section-desc">{{ __('filament/integrations.pipeline_filter_desc') }}</p>
                        <div class="ig-checkbox-wrap">
                            @foreach($tenantPipelines as $pipeline)
                                <label class="ig-checkbox-label">
                                    <input type="checkbox" value="{{ $pipeline->id }}" wire:model="setupFilterPipelines" class="ig-checkbox-input"/>
                                    {{ $pipeline->name }}
                                </label>
                            @endforeach
                            @if($tenantPipelines->isEmpty())
                                <p class="ig-empty-hint">{{ __('filament/integrations.no_pipelines_created') }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="ig-panel-footer">
                    <button @click="showModal = false" class="ig-btn-secondary">{{ __('filament/integrations.modal_cancel') }}</button>
                    <button wire:click="saveSetup" class="ig-btn-primary">{{ __('filament/integrations.modal_save_integration') }}</button>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
