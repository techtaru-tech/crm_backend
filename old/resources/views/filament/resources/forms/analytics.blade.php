<x-filament-panels::page>
    {{-- Inline CSS because Tailwind utility classes don't JIT-compile
         on shared hosting. Same defensive pattern used elsewhere.

         Note:
         All STATIC styles for this page live in
         public/css/filament/forms-analytics.css. The two `style="width: …%"`
         attributes remaining inline (field-completion bar and step-dropoff
         bar) are DYNAMIC — the bar width is computed per-row from the
         $fieldCompletion / $stepDropoff arrays at render time, so they
         cannot be moved to a stylesheet. --}}
    <link rel="stylesheet" href="{{ asset('css/filament/forms-analytics.css') }}">

    <div class="lh-fa">
        <div class="lh-fa-bc">
            <a href="{{ \App\Filament\Resources\FormResource::getUrl('view', ['record' => $form]) }}">{{ __('filament/forms.analytics_back_to_form') }}</a>
            <span class="sep">/</span>
            <span class="lh-fa-bc-cur">{{ __('filament/forms.analytics_breadcrumb_prefix', ['name' => $form->name]) }}</span>
        </div>

        {{-- Summary cards --}}
        <div class="lh-fa-cards">
            <x-filament::section>
                <div class="lh-fa-card">
                    <div class="lh-fa-num primary">{{ number_format($totalSubmissions) }}</div>
                    <div class="lh-fa-sub">{{ __('filament/forms.analytics_total_submissions') }}</div>
                </div>
            </x-filament::section>
            <x-filament::section>
                <div class="lh-fa-card">
                    <div class="lh-fa-num green">{{ $form->active ? __('filament/forms.analytics_status_active') : __('filament/forms.analytics_status_inactive') }}</div>
                    <div class="lh-fa-sub">{{ __('filament/forms.analytics_form_status') }}</div>
                </div>
            </x-filament::section>
            <x-filament::section>
                <div class="lh-fa-card">
                    <div class="lh-fa-num violet">{{ $form->fields->count() }}</div>
                    <div class="lh-fa-sub">{{ __('filament/forms.analytics_total_fields') }}</div>
                </div>
            </x-filament::section>
        </div>

        <x-filament::section :heading="__('filament/forms.analytics_submissions_30d')">
            <div class="lh-fa-chart">
                <canvas id="submissionsChart" data-chart-label="{{ __('filament/form_analytics.chart_label_submissions') }}"></canvas>
            </div>
        </x-filament::section>

        @if(!empty($fieldCompletion))
        <x-filament::section :heading="__('filament/forms.analytics_field_completion')">
            <div class="lh-fa-rows">
                @foreach($fieldCompletion as $fieldLabel => $pct)
                    <div class="lh-fa-bar-row">
                        <div class="top">
                            <span class="label" title="{{ $fieldLabel }}">{{ $fieldLabel }}</span>
                            <span class="pct">{{ $pct }}%</span>
                        </div>
                        <div class="lh-fa-bar"><div class="fill primary" style="width: {{ max(0, min(100, (int) $pct)) }}%"></div></div>
                    </div>
                @endforeach
            </div>
        </x-filament::section>
        @endif

        @if(!empty($stepDropoff))
        <x-filament::section :heading="__('filament/forms.analytics_step_dropoff')">
            <div class="lh-fa-rows">
                @php $maxCount = max($stepDropoff ?: [1]); @endphp
                @foreach($stepDropoff as $step => $count)
                    <div class="lh-fa-bar-row">
                        <div class="top">
                            <span class="label">{{ __('filament/forms.analytics_step_label', ['n' => $step]) }}</span>
                            <span class="pct">{{ __('filament/forms.analytics_step_reached', ['count' => number_format($count)]) }}</span>
                        </div>
                        <div class="lh-fa-bar"><div class="fill violet" style="width: {{ $maxCount > 0 ? round(($count / $maxCount) * 100) : 0 }}%"></div></div>
                    </div>
                @endforeach
            </div>
        </x-filament::section>
        @endif

        <x-filament::section :heading="__('filament/forms.analytics_embed_snippet')">
            <p class="lh-fa-muted">{{ __('filament/forms.analytics_embed_snippet_intro') }}</p>
            <div class="lh-fa-code">{{ $form->embed_snippet }}</div>
            <p class="lh-fa-muted lh-fa-mt-12">{{ __('filament/forms.analytics_public_form_url') }}
                <a href="{{ $form->public_url }}" target="_blank" rel="noopener noreferrer" class="lh-fa-link">{{ $form->public_url }}</a>
            </p>
        </x-filament::section>
    </div>

    @push('scripts')
    {{-- Chart.js — vendored locally under public/vendor/ (no CDN). --}}
    <script src="{{ asset('vendor/chartjs/chart.umd.min.js') }}"></script>
    {{-- Dynamic submissions-over-time series is published as a JSON
         island the chart initialiser reads from the DOM.  This lets
         the analytics.js init script stay static (no Blade syntax). --}}
    <script type="application/json" id="form-submissions-data">@json([
        'labels' => array_keys($submissionsOverTime),
        'data'   => array_values($submissionsOverTime),
    ])</script>
    <script src="{{ asset('js/views/filament/resources/forms/analytics.js') }}" defer></script>
    @endpush
</x-filament-panels::page>
