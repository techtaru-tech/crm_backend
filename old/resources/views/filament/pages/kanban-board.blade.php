<x-filament-panels::page>
    <link rel="stylesheet" href="{{ asset('css/filament/kanban-board.css') }}">

    {{-- Pipeline Selector --}}
    <div class="kb-pills">
        <label class="kb-pill-label">{{ __('filament/kanban_board.pipeline_label') }}</label>
        @foreach($pipelines as $pipeline)
            <button
                wire:click="changePipeline({{ $pipeline->id }})"
                class="kb-pill {{ $selectedPipelineId == $pipeline->id ? 'kb-pill-active' : 'kb-pill-inactive' }}">
                {{ $pipeline->name }}
            </button>
        @endforeach
    </div>

    @if(empty($stages))
        <div class="kb-empty">
            <h3 class="kb-empty-title">{{ __('filament/kanban_board.no_pipeline_configured') }}</h3>
            <p class="kb-empty-body">
                <a href="{{ route('filament.admin.resources.pipelines.create') }}" class="kb-empty-link">{{ __('filament/kanban_board.create_a_pipeline') }}</a> {{ __('filament/kanban_board.to_get_started') }}
            </p>
        </div>
    @else
        {{-- Kanban Board --}}
        <div
            class="kb-board"
            x-data="{
                tenantId: @js((string) auth()->user()?->tenant_id),
                toast: null,
                toastTimer: null,

                showToast(message) {
                    this.toast = message;
                    clearTimeout(this.toastTimer);
                    this.toastTimer = setTimeout(() => { this.toast = null; }, 4000);
                },

                initSortable() {
                    @this.get('stages').forEach(stage => {
                        const el = document.getElementById('stage-' + stage.id);
                        if (!el || el._sortable) return;
                        el._sortable = Sortable.create(el, {
                            group: 'leads',
                            animation: 150,
                            ghostClass: 'opacity-40',
                            onEnd: (evt) => {
                                const leadId = parseInt(evt.item.dataset.leadId);
                                const newStageId = parseInt(evt.to.dataset.stageId);
                                if (leadId && newStageId) {
                                    @this.call('moveCard', leadId, newStageId);
                                }
                            }
                        });
                    });

                    // Unassigned column — pull-only source.  Cards can be
                    // dragged OUT into any stage (which assigns them), but
                    // put:false means you cannot drop a card back IN (we
                    // don't support un-assigning via drag).  Drops into a
                    // stage fire moveCard with that stage's id; the empty
                    // data-stage-id on this column means a stray drop here
                    // parses to NaN and is a no-op.
                    const unassignedEl = document.getElementById('stage-unassigned');
                    if (unassignedEl && !unassignedEl._sortable) {
                        unassignedEl._sortable = Sortable.create(unassignedEl, {
                            group: { name: 'leads', pull: true, put: false },
                            animation: 150,
                            ghostClass: 'opacity-40',
                            onEnd: (evt) => {
                                const leadId = parseInt(evt.item.dataset.leadId);
                                const newStageId = parseInt(evt.to.dataset.stageId);
                                if (leadId && newStageId) {
                                    @this.call('moveCard', leadId, newStageId);
                                }
                            }
                        });
                    }
                },

                initEcho() {
                    if (typeof window.Echo === 'undefined') return;
                    window.Echo.private('tenant.' + this.tenantId + '.leads')
                        .listen('.lead.stage_changed', (e) => {
                            @this.call('loadBoard');
                            const tpl = @js(__('filament/kanban_board.toast_lead_moved'));
                            const defaultName = @js(__('filament/kanban_board.default_lead_name'));
                            const defaultStage = @js(__('filament/kanban_board.default_stage_name'));
                            this.showToast(
                                tpl
                                    .replace(':lead_name', e.lead_name || defaultName)
                                    .replace(':to_stage', e.to_stage || defaultStage)
                            );
                        })
                        .listen('.lead.received', () => {
                            @this.call('loadBoard');
                        });
                }
            }"
            x-init="$nextTick(() => { initSortable(); initEcho(); })"
        >
            {{-- Toast --}}
            <div
                x-show="toast"
                x-transition
                class="kb-toast kb-toast-hidden"
                x-text="toast"
            ></div>

            {{-- Unassigned column: leads with no pipeline stage.  Renders
                 only when there ARE unassigned leads.  Pull-only — drag a
                 card OUT into a real stage to assign it (moveCard sets the
                 pipeline + stage from the drop target). --}}
            @if($unassignedTotal > 0)
                <div class="kb-col kb-col-unassigned">
                    <div class="kb-header">
                        <div class="kb-header-left">
                            {{-- inline style: neutral gray dot marks the "no stage" column --}}
                            <div class="kb-dot" style="background-color: #9ca3af"></div>
                            <h3 class="kb-stage-name">{{ __('filament/kanban_board.unassigned_label') }}</h3>
                        </div>
                        <span class="kb-count">{{ count($unassignedLeads) }}</span>
                    </div>
                    <div class="kb-totals">
                        {{ $unassignedTotal }} {{ __('filament/kanban_board.leads_suffix') }} · <span class="kb-total-value">{{ __('filament/kanban_board.unassigned_hint') }}</span>
                    </div>

                    <div
                        id="stage-unassigned"
                        data-stage-id=""
                        class="kb-stage"
                    >
                        @foreach($unassignedLeads as $lead)
                            @include('filament.pages.partials.kanban-card', ['lead' => $lead])
                        @endforeach
                    </div>
                </div>
            @endif

            @foreach($stages as $stage)
                <div class="kb-col">
                    {{-- Stage Header --}}
                    <div class="kb-header">
                        <div class="kb-header-left">
                            {{-- inline style: dynamic per-stage hex color stored in pipeline_stages.color --}}
                            <div class="kb-dot" style="background-color: {{ $stage['color'] }}"></div>
                            <h3 class="kb-stage-name">{{ $stage['name'] }}</h3>
                            @if($stage['is_won'])
                                <span class="kb-badge kb-badge-won">{{ __('filament/kanban_board.badge_won') }}</span>
                            @elseif($stage['is_lost'])
                                <span class="kb-badge kb-badge-lost">{{ __('filament/kanban_board.badge_lost') }}</span>
                            @endif
                        </div>
                        <span class="kb-count">{{ count($leadsByStage[$stage['id']] ?? []) }}</span>
                    </div>
                    @php $totals = $stageTotals[$stage['id']] ?? ['count' => 0, 'total_value' => 0]; @endphp
                    <div class="kb-totals">
                        {{ $totals['count'] }} {{ __('filament/kanban_board.leads_suffix') }} · <span class="kb-total-value">{{ \App\Support\Currency::format($totals['total_value'], \App\Support\Currency::default()) }}</span>
                    </div>

                    {{-- Stage Column (drop target) --}}
                    <div
                        id="stage-{{ $stage['id'] }}"
                        data-stage-id="{{ $stage['id'] }}"
                        class="kb-stage"
                    >
                        @foreach($leadsByStage[$stage['id']] ?? [] as $lead)
                            @include('filament.pages.partials.kanban-card', ['lead' => $lead])
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Load SortableJS — vendored locally under public/vendor/ (no CDN). --}}
        @push('scripts')
            <script src="{{ asset('vendor/sortablejs/Sortable.min.js') }}"></script>
        @endpush
    @endif
</x-filament-panels::page>
