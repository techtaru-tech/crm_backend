@extends('portal._layout')
@section('title', __('portal.page_title_account'))

@section('topbar')
<link rel="stylesheet" href="{{ asset('css/views/portal/dashboard.css') }}">
<div class="topbar">
    <div class="brand">
        <img src="/favicon.svg" alt="">
        {{ $tenant?->name ?? config('leadhub.branding.app_name', 'LeadHub') }}
    </div>
    <form method="POST" action="{{ route('portal.logout') }}" class="pd-topbar-form">
        @csrf
        <button type="submit" class="btn btn-ghost pd-signout-btn">{{ __('portal.sign_out') }}</button>
    </form>
</div>
@endsection

@section('content')
    <div class="card">
        <h1>{{ __('portal.greeting_hi', ['name' => $lead->first_name ?: __('portal.greeting_there')]) }}</h1>
        <p class="pd-greeting-sub">{{ __('portal.greeting_sub') }}</p>
    </div>

    {{-- Pipeline progress --}}
    @if($lead->pipeline && $allStages->isNotEmpty())
        <div class="card">
            <h2>{{ __('portal.where_you_are') }}</h2>
            @php
                $currentIndex = $allStages->search(fn($s) => $s->id === $lead->pipeline_stage_id);
                $total = $allStages->count();
                $pct = $currentIndex === false ? 0 : (int) round((($currentIndex + 1) / $total) * 100);
            @endphp
            <div class="pd-progress-track">
                {{-- NOTE: width is DYNAMIC — driven by $pct percentage --}}
                <div class="pd-progress-fill" style="width:{{ $pct }}%;"></div>
            </div>
            <div class="pd-stages">
                @foreach($allStages as $i => $stage)
                    @php $isCurrent = $stage->id === $lead->pipeline_stage_id; $isPast = $currentIndex !== false && $i < $currentIndex; @endphp
                    <div class="pd-stage">
                        {{-- NOTE: background DYNAMIC — color depends on stage state (current/past/future) --}}
                        <div class="pd-stage-dot" style="background:{{ $isCurrent ? '#4f46e5' : ($isPast ? '#10b981' : '#d1d5db') }};">
                            @if($isPast) ✓ @else {{ $i + 1 }} @endif
                        </div>
                        {{-- NOTE: color + font-weight DYNAMIC — highlight current stage --}}
                        <div class="pd-stage-label" style="color:{{ $isCurrent ? '#4f46e5' : '#6b7280' }};font-weight:{{ $isCurrent ? '600' : '400' }};">{{ $stage->name }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Assigned rep + deal --}}
    @if($lead->assignedUser || $lead->deal_value)
        <div class="card">
            <h2>{{ __('portal.your_account') }}</h2>
            <dl class="pd-meta-grid">
                @if($lead->assignedUser)
                    <div>
                        <dt class="pd-meta-dt">{{ __('portal.your_contact') }}</dt>
                        <dd class="pd-meta-dd">{{ $lead->assignedUser->name }}</dd>
                    </div>
                @endif
                @if($lead->deal_value)
                    <div>
                        <dt class="pd-meta-dt">{{ __('portal.deal_value') }}</dt>
                        <dd class="pd-meta-dd">{{ $lead->deal_currency ?? '$' }} {{ number_format($lead->deal_value, 2) }}</dd>
                    </div>
                @endif
                @if($lead->expected_close_date)
                    <div>
                        <dt class="pd-meta-dt">{{ __('portal.expected_close') }}</dt>
                        {{-- Carbon::toFormattedDateString() returns the
                             English "M j, Y" format ("Apr 17, 2026") and
                             ignores app locale.  translatedFormat() walks
                             the same tokens through the current Symfony
                             translator so non-English buyers see the
                             localised month abbreviation. --}}
                        <dd class="pd-meta-dd">{{ $lead->expected_close_date->translatedFormat('M j, Y') }}</dd>
                    </div>
                @endif
            </dl>
        </div>
    @endif

    {{-- Activity timeline (public-safe only) --}}
    @if($activities->isNotEmpty())
        <div class="card">
            <h2>{{ __('portal.recent_activity') }}</h2>
            <ul class="pd-activity-list">
                @foreach($activities as $a)
                    <li class="pd-activity-item">
                        <div class="pd-activity-marker"></div>
                        <div>
                            @php
                                // Translator-first activity description when no persisted description.
                                // Looks up lead_activities.<type> so the portal feed respects locale,
                                // falls back to the humanised key for unknown future activity types.
                                $actDesc = (string) ($a->description ?? '');
                                if ($actDesc === '' && $a->type) {
                                    $actKey   = 'lead_activities.' . $a->type;
                                    $actTrans = __($actKey);
                                    $actDesc  = is_string($actTrans) && $actTrans !== $actKey
                                        ? $actTrans
                                        : ucfirst(str_replace('_', ' ', (string) $a->type));
                                }
                            @endphp
                            <p class="pd-activity-desc">{{ $actDesc }}</p>
                            <p class="pd-activity-time">{{ $a->created_at->diffForHumans() }}</p>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Documents / upload --}}
    <div class="card">
        <h2>{{ __('portal.documents') }}</h2>
        @if($lead->attachments->isNotEmpty())
            <ul class="pd-doc-list">
                @foreach($lead->attachments as $att)
                    <li class="pd-doc-item">
                        <span>{{ $att->original_name }}</span>
                        <span class="pd-doc-meta">{{ $att->getHumanSize() }} &middot; {{ $att->created_at->diffForHumans() }}</span>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="pd-doc-empty">{{ __('portal.no_documents_yet') }}</p>
        @endif
        <form method="POST" action="{{ route('portal.upload') }}" enctype="multipart/form-data" class="pd-upload-form">
            @csrf
            <label class="pd-upload-label">{{ __('portal.upload_a_file') }}</label>
            <input type="file" name="file" required class="pd-upload-input">
            @error('file')<p class="pd-upload-error">{{ $message }}</p>@enderror
            <button type="submit" class="btn btn-primary">{{ __('portal.upload') }}</button>
            <span class="pd-upload-hint">{{ __('portal.max_upload_size') }}</span>
        </form>
    </div>
@endsection
