{{--
    Single Kanban lead card.  Extracted from kanban-board.blade.php so
    the per-stage columns AND the "Unassigned" column render identical
    cards from one source.  Expects $lead (the array shape built by
    KanbanBoard::mapLeadCard()).
--}}
<div data-lead-id="{{ $lead['id'] }}" class="kb-card">
    {{-- Card Header --}}
    <div class="kb-card-header">
        <a href="{{ $lead['view_url'] }}">{{ $lead['name'] }}</a>
        @if($lead['is_starred'])
            <svg class="kb-star" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd"/></svg>
        @endif
    </div>

    {{-- Source badge --}}
    <div class="kb-badges">
        <span class="kb-badge kb-badge-source">{{ $lead['source_label'] }}</span>
        @if($lead['score'] > 0)
            <span class="kb-badge {{ $lead['score'] > 50 ? 'kb-badge-score-high' : 'kb-badge-score-low' }}">
                ★ {{ $lead['score'] }}
            </span>
        @endif
    </div>

    {{-- Deal value --}}
    @if(!empty($lead['deal_value']))
        <div class="kb-card-value">
            {{ \App\Support\Currency::format($lead['deal_value'], $lead['deal_currency'] ?? \App\Support\Currency::default()) }}
            <span class="kb-currency">{{ $lead['deal_currency'] }}</span>
        </div>
    @endif

    {{-- Tags --}}
    @if(!empty($lead['tags']))
        <div class="kb-tags">
            @foreach(array_slice($lead['tags'], 0, 3) as $tag)
                <span class="kb-tag">{{ $tag }}</span>
            @endforeach
        </div>
    @endif

    {{-- Footer --}}
    <div class="kb-footer">
        {{-- Localised "Nd" suffix via days_short_suffix so the abbreviation isn't a literal English "d". --}}
        <span>{{ __('filament/kanban_board.days_in_stage', ['days' => __('filament/kanban_board.days_short_suffix', ['count' => $lead['days_in_stage']])]) }}</span>
        @if($lead['assigned_to'])
            <span>{{ $lead['assigned_to'] }}</span>
        @endif
    </div>
</div>
