<x-filament-panels::page>
    {{--
        UI chrome (search box, empty-state, results count, support
        footer) loaded from lang/<locale>/help_center.php so buyers
        can adapt or translate without touching this view.  Article
        titles + bodies still come from HelpCenterPage::articles() —
        moving those is a follow-up so the knowledge-base content
        stays editable in one place.

        Support email is read from config('leadhub.support_email') so
        buyers set it once in config without editing the view.

        Static styles live in public/css/views/filament/pages/help-center.css.
    --}}
    @php
        $__supportEmail = config('leadhub.support_email', 'support@yourdomain.com');
    @endphp
    <div class="hc-container">
        {{-- Search bar --}}
        <div class="hc-search-wrap">
            <input type="text"
                   wire:model.live.debounce.300ms="search"
                   placeholder="{{ __('help_center.search_placeholder') }}"
                   class="hc-search-input">
            <span class="hc-search-icon">🔍</span>
        </div>

        @if ($search_term !== '' && $total_count === 0)
            <div class="hc-no-match">
                <div class="hc-no-match-icon">🤷</div>
                <h3 class="hc-no-match-title">{{ __('help_center.no_match_title') }}</h3>
                <p class="hc-no-match-body">
                    {!! __('help_center.no_match_body', ['term' => '<strong>' . e($search_term) . '</strong>']) !!}
                </p>
            </div>
        @endif

        @if ($search_term !== '' && $total_count > 0)
            <p class="hc-result-count">
                {!! trans_choice('help_center.result_count', $total_count, [
                    'count' => $total_count,
                    'term'  => '<strong>' . e($search_term) . '</strong>',
                ]) !!}
            </p>
        @endif

        @foreach ($grouped as $category => $articles)
            <div class="hc-group">
                <h2 class="hc-group-heading">{{ $category }}</h2>
                <div class="hc-articles">
                    @foreach ($articles as $article)
                        <details class="hc-article">
                            <summary class="hc-article-summary">
                                <span>{{ $article['title'] }}</span>
                                <span class="hc-article-toggle">+</span>
                            </summary>
                            <div class="hc-article-body">
                                {!! nl2br(e($article['body'])) !!}
                                @if (! empty($article['tags']))
                                    <div class="hc-tags">
                                        @foreach ($article['tags'] as $tag)
                                            <span class="hc-tag">#{{ $tag }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </details>
                    @endforeach
                </div>
            </div>
        @endforeach

        {{-- Contact-support fallback --}}
        <div class="hc-support-footer">
            <h3 class="hc-support-title">{{ __('help_center.support_title') }}</h3>
            <p class="hc-support-body">
                {{-- Defense-in-depth: e() on the mailto:
                     substitution.  Today $__supportEmail is from server
                     config (sysadmin-controlled, safe), but if a future
                     change sources it from tenant settings, this lang
                     key renders raw via {!! !!} and the `mailto:`
                     placeholder flows into <a href=":mailto"> — without
                     e() that would let a tenant inject a `javascript:`
                     URL.  e() is idempotent and cheap. --}}
                {!! __('help_center.support_body_html', [
                    'mailto' => 'mailto:' . e($__supportEmail),
                    'email'  => e($__supportEmail),
                ]) !!}
            </p>
        </div>
    </div>

    <link rel="stylesheet" href="{{ asset('css/views/filament/pages/help-center.css') }}">
</x-filament-panels::page>
