<x-filament-panels::page>
    {{--
        Static styles live in public/css/views/filament/pages/referrals-page.css.
        No dynamic interpolations.
    --}}
    <link rel="stylesheet" href="{{ asset('css/views/filament/pages/referrals-page.css') }}">

    @php
        $link  = $this->getReferralLink();
        $stats = $this->getStats();
        $tenants = $this->getReferredTenants();
    @endphp

    <div class="rf-page">
        <div class="rf-card">
            <h3 class="rf-section-title">{{ __('filament/referrals.your_referral_link') }}</h3>
            <p class="rf-section-sub">{{ __('filament/referrals.share_subtitle') }}</p>
            <div class="rf-link-row">
                <input id="rf-link" type="text" value="{{ $link }}" readonly>
                <button type="button" x-on:click="const i=document.getElementById('rf-link');i.select();document.execCommand('copy');$el.innerText=@js(__('filament/referrals.copied'))">{{ __('filament/referrals.copy') }}</button>
            </div>
        </div>

        <div class="rf-stat-grid">
            <div class="rf-stat">
                <div class="rf-stat-label">{{ __('filament/referrals.stat_referred') }}</div>
                <div class="rf-stat-value">{{ number_format($stats['referred_count']) }}</div>
            </div>
            <div class="rf-stat">
                <div class="rf-stat-label">{{ __('filament/referrals.stat_on_trial') }}</div>
                <div class="rf-stat-value">{{ number_format($stats['trial_count']) }}</div>
            </div>
            <div class="rf-stat">
                <div class="rf-stat-label">{{ __('filament/referrals.stat_paying') }}</div>
                <div class="rf-stat-value rf-stat-value-accent">{{ number_format($stats['paying_count']) }}</div>
            </div>
        </div>

        <div class="rf-card rf-card-tight">
            <div class="rf-card-header">
                <h3 class="rf-section-title">{{ __('filament/referrals.workspaces_youve_referred') }}</h3>
            </div>
            @if ($tenants->isEmpty())
                <div class="rf-empty">{{ __('filament/referrals.empty_no_referrals') }}</div>
            @else
                <table class="rf-table">
                    <thead><tr><th>{{ __('filament/referrals.col_name') }}</th><th>{{ __('filament/referrals.col_plan') }}</th><th>{{ __('filament/referrals.col_status') }}</th><th>{{ __('filament/referrals.col_signed_up') }}</th></tr></thead>
                    <tbody>
                        @foreach ($tenants as $t)
                            <tr>
                                <td class="name">{{ $t->name }}</td>
                                @php
                                    // Translator-first plan label so the badge respects tenant locale.
                                    $rfPlan      = $t->plan ?? null;
                                    $rfPlanKey   = $rfPlan ? 'filament/referrals.plan_' . $rfPlan : null;
                                    $rfPlanTrans = $rfPlanKey ? __($rfPlanKey) : null;
                                    $rfPlanLabel = $rfPlan
                                        ? (is_string($rfPlanTrans) && $rfPlanTrans !== $rfPlanKey
                                            ? $rfPlanTrans
                                            : ucfirst((string) $rfPlan))
                                        : '—';
                                @endphp
                                <td><span class="rf-badge">{{ $rfPlanLabel }}</span></td>
                                <td class="muted">{{ $t->subscription_status?->label() ?? '—' }}</td>
                                <td class="timestamp">{{ $t->created_at?->diffForHumans() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</x-filament-panels::page>
