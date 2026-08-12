@php /** @var \Illuminate\Support\Collection $messages */ @endphp

{{-- Static styles (no Tailwind JIT on shared hosting — see CLAUDE.md).
     Per-row state colour stays inline because it is data-driven. --}}
<link rel="stylesheet" href="{{ asset('css/views/filament/ai-sdr/decision-log.css') }}">

<div class="lh-sdr-log">
    @forelse($messages as $msg)
        <div class="lh-sdr-row">
            <div class="lh-sdr-head">
                <span class="lh-sdr-action lh-sdr-action--{{ $msg->action }}">{{ strtoupper($msg->action) }}</span>
                @if($msg->was_fallback)
                    <span class="lh-sdr-badge">{{ __('filament/ai_sdr.log_fallback') }}</span>
                @endif
                @if($msg->intent_score !== null)
                    <span class="lh-sdr-meta">{{ __('filament/ai_sdr.log_intent') }}: {{ $msg->intent_score }}</span>
                @endif
                <span class="lh-sdr-time">{{ $msg->created_at?->diffForHumans() }}</span>
            </div>

            @if($msg->reason)
                <p class="lh-sdr-reason">{{ $msg->reason }}</p>
            @endif

            @if($msg->subject)
                <p class="lh-sdr-subj">{{ $msg->subject }}</p>
            @endif
            @if($msg->body)
                <p class="lh-sdr-body">{{ \Illuminate\Support\Str::limit(strip_tags($msg->body), 280) }}</p>
            @endif

            @if($msg->model || $msg->cost_usd !== null)
                <div class="lh-sdr-foot">
                    @if($msg->model)<span>{{ $msg->model }}</span>@endif
                    @if($msg->prompt_tokens !== null)<span>{{ $msg->prompt_tokens }}+{{ $msg->completion_tokens }} tok</span>@endif
                    @if($msg->cost_usd !== null)<span>${{ number_format((float) $msg->cost_usd, 6) }}</span>@endif
                </div>
            @endif
        </div>
    @empty
        <p class="lh-sdr-empty">{{ __('filament/ai_sdr.log_empty') }}</p>
    @endforelse
</div>
