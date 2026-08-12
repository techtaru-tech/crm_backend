<?php

namespace App\Filament\SuperAdmin\Pages;

use App\Settings\LandingContent;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use UnitEnum;

/**
 * Visual editor for the public marketing landing page. Wired to the
 * LandingContent Spatie settings class so every field edited here
 * renders on the /landing route without a deploy or file change.
 */
class LandingPageEditor extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-globe-alt';
    protected string $view = 'filament.super-admin.pages.landing-page-editor';
    protected static ?int $navigationSort = 30;
    protected static string|UnitEnum|null $navigationGroup = 'Marketing';

    public ?array $data = [];

    public static function getNavigationLabel(): string
    {
        return __('filament/sa_landing_page_editor.navigation_label');
    }

    public function getTitle(): string|Htmlable
    {
        return __('filament/sa_landing_page_editor.title');
    }

    public function mount(): void
    {
        $c = app(LandingContent::class);

        $this->form->fill([
            'hero_eyebrow'           => $c->hero_eyebrow,
            'hero_headline'          => $c->hero_headline,
            'hero_headline_highlight' => $c->hero_headline_highlight,
            'hero_subtext'           => $c->hero_subtext,
            'hero_cta_label'         => $c->hero_cta_label,
            'hero_note'              => $c->hero_note,

            'features_headline' => $c->features_headline,
            'features_subtext'  => $c->features_subtext,
            'features'          => $c->features,

            'pricing_headline' => $c->pricing_headline,
            'pricing_subtext'  => $c->pricing_subtext,

            'cta_headline' => $c->cta_headline,
            'cta_subtext'  => $c->cta_subtext,

            // Stats strip overrides — null is the "use the translator
            // default" signal, which the landing-light blade respects
            // via __('marketing.light_statN_n / _l') fallback.
            'stat1_number' => $c->stat1_number,
            'stat1_label'  => $c->stat1_label,
            'stat2_number' => $c->stat2_number,
            'stat2_label'  => $c->stat2_label,
            'stat3_number' => $c->stat3_number,
            'stat3_label'  => $c->stat3_label,
            'stat4_number' => $c->stat4_number,
            'stat4_label'  => $c->stat4_label,

            // Treat null as "true" for toggles when filling the form
            // so existing installs surface as ON the first time the
            // operator visits this page.
            'nav_show_pricing'      => $c->nav_show_pricing      ?? true,
            'nav_show_docs'         => $c->nav_show_docs         ?? true,
            'nav_show_sign_in'      => $c->nav_show_sign_in      ?? true,
            'footer_show_nav_links' => $c->footer_show_nav_links ?? true,
            'footer_tagline'        => $c->footer_tagline,
            'footer_copyright'      => $c->footer_copyright,
            'footer_show_static_pages'  => $c->footer_show_static_pages ?? true,
            'footer_show_brand_column'  => $c->footer_show_brand_column ?? true,
            'footer_show_social'        => $c->footer_show_social       ?? true,
            'footer_social_x_url'       => $c->footer_social_x_url,
            'footer_social_github_url'  => $c->footer_social_github_url,
            'footer_social_linkedin_url'=> $c->footer_social_linkedin_url,
            'footer_social_facebook_url'  => $c->footer_social_facebook_url,
            'footer_social_instagram_url' => $c->footer_social_instagram_url,
            'footer_social_youtube_url'   => $c->footer_social_youtube_url,
            'seo_meta_title'        => $c->seo_meta_title,
            'seo_meta_description'  => $c->seo_meta_description,
            'seo_meta_keywords'     => $c->seo_meta_keywords,
            'seo_og_image_url'      => $c->seo_og_image_url,

            // Surface the translations map as a flat form-state key
            // so Filament can bind it directly to dot-notation inputs.
            'translations'          => $c->translations ?? [],
        ]);
    }

    /**
     * Locales other than English — iterated to build the Translations
     * tab. English always lives in the primary scalar fields above.
     */
    protected function nonEnglishLocales(): array
    {
        return array_filter(
            config('locales.supported', []),
            fn ($meta, $code) => $code !== 'en',
            ARRAY_FILTER_USE_BOTH
        );
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Tabs::make(__('filament/sa_landing_page_editor.tabs_outer'))
                    ->tabs([
                        Tab::make(__('filament/sa_landing_page_editor.tab_hero'))
                            ->icon('heroicon-o-rocket-launch')
                            ->schema([
                                TextInput::make('hero_eyebrow')
                                    ->label(__('filament/sa_landing_page_editor.hero_eyebrow'))
                                    ->placeholder(__('filament/sa_landing_page_editor.hero_eyebrow_placeholder')),
                                TextInput::make('hero_headline')
                                    ->label(__('filament/sa_landing_page_editor.hero_headline'))
                                    ->placeholder(__('filament/sa_landing_page_editor.hero_headline_placeholder')),
                                TextInput::make('hero_headline_highlight')
                                    ->label(__('filament/sa_landing_page_editor.hero_headline_highlight'))
                                    ->placeholder(__('filament/sa_landing_page_editor.hero_headline_highlight_placeholder')),
                                Textarea::make('hero_subtext')
                                    ->label(__('filament/sa_landing_page_editor.hero_subtext'))
                                    ->rows(3)
                                    ->placeholder(__('filament/sa_landing_page_editor.hero_subtext_placeholder')),
                                TextInput::make('hero_cta_label')
                                    ->label(__('filament/sa_landing_page_editor.hero_cta_label'))
                                    ->placeholder(__('filament/sa_landing_page_editor.hero_cta_label_placeholder')),
                                TextInput::make('hero_note')
                                    ->label(__('filament/sa_landing_page_editor.hero_note'))
                                    ->placeholder(__('filament/sa_landing_page_editor.hero_note_placeholder')),
                            ])
                            ->columns(1),

                        Tab::make(__('filament/sa_landing_page_editor.tab_features'))
                            ->icon('heroicon-o-squares-2x2')
                            ->schema([
                                TextInput::make('features_headline')
                                    ->label(__('filament/sa_landing_page_editor.features_headline')),
                                Textarea::make('features_subtext')
                                    ->label(__('filament/sa_landing_page_editor.features_subtext'))
                                    ->rows(2),

                                Repeater::make('features')
                                    ->label(__('filament/sa_landing_page_editor.feature_cards'))
                                    ->schema([
                                        TextInput::make('title')->label(__('filament/sa_landing_page_editor.feature_title'))->required(),
                                        Textarea::make('body')->label(__('filament/sa_landing_page_editor.feature_body'))->rows(2)->required(),
                                    ])
                                    ->columns(1)
                                    ->defaultItems(0)
                                    ->reorderable()
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),
                            ])
                            ->columns(1),

                        Tab::make(__('filament/sa_landing_page_editor.tab_pricing'))
                            ->icon('heroicon-o-tag')
                            ->schema([
                                TextInput::make('pricing_headline')->label(__('filament/sa_landing_page_editor.pricing_headline')),
                                Textarea::make('pricing_subtext')->label(__('filament/sa_landing_page_editor.pricing_subtext'))->rows(2),
                            ])
                            ->columns(1),

                        Tab::make(__('filament/sa_landing_page_editor.tab_final_cta'))
                            ->icon('heroicon-o-megaphone')
                            ->schema([
                                TextInput::make('cta_headline')->label(__('filament/sa_landing_page_editor.cta_headline')),
                                Textarea::make('cta_subtext')->label(__('filament/sa_landing_page_editor.cta_subtext'))->rows(2),
                            ])
                            ->columns(1),

                        Tab::make(__('filament/sa_landing_page_editor.tab_stats'))
                            ->icon('heroicon-o-chart-bar')
                            ->schema([
                                // Each row = one tile in the public stats strip
                                // (4 tiles total).  Leaving a field blank falls
                                // back to the translator key shipped in
                                // lang/<locale>/marketing.php (light_stat[1-4]_n /_l).
                                TextInput::make('stat1_number')
                                    ->label(__('filament/sa_landing_page_editor.stat1_number'))
                                    ->placeholder('19')
                                    ->helperText(__('filament/sa_landing_page_editor.stats_blank_helper')),
                                TextInput::make('stat1_label')
                                    ->label(__('filament/sa_landing_page_editor.stat1_label'))
                                    ->placeholder('Lead Sources'),

                                TextInput::make('stat2_number')
                                    ->label(__('filament/sa_landing_page_editor.stat2_number'))
                                    ->placeholder('90s'),
                                TextInput::make('stat2_label')
                                    ->label(__('filament/sa_landing_page_editor.stat2_label'))
                                    ->placeholder('Install time'),

                                TextInput::make('stat3_number')
                                    ->label(__('filament/sa_landing_page_editor.stat3_number'))
                                    ->placeholder('∞')
                                    ->helperText(__('filament/sa_landing_page_editor.stat3_number_helper')),
                                TextInput::make('stat3_label')
                                    ->label(__('filament/sa_landing_page_editor.stat3_label'))
                                    ->placeholder('Tenants / workspaces'),

                                TextInput::make('stat4_number')
                                    ->label(__('filament/sa_landing_page_editor.stat4_number'))
                                    ->placeholder('0'),
                                TextInput::make('stat4_label')
                                    ->label(__('filament/sa_landing_page_editor.stat4_label'))
                                    ->placeholder('Lock-in — own your DB'),
                            ])
                            ->columns(2),

                        Tab::make(__('filament/sa_landing_page_editor.tab_navigation'))
                            ->icon('heroicon-o-bars-3')
                            ->schema([
                                Section::make(__('sections.built_in_navbar_links'))
                                    ->description(__('filament/sa_landing_page_editor.built_in_navbar_description'))
                                    ->schema([
                                        Toggle::make('nav_show_pricing')->label(__('filament/sa_landing_page_editor.show_pricing_link'))->default(true),
                                        Toggle::make('nav_show_docs')->label(__('filament/sa_landing_page_editor.show_docs_link'))->default(true),
                                        Toggle::make('nav_show_sign_in')->label(__('filament/sa_landing_page_editor.show_sign_in_link'))->default(true),
                                    ])
                                    ->columns(3),

                                Section::make(__('sections.registration_legal_links'))
                                    ->description(__('filament/sa_landing_page_editor.registration_legal_description'))
                                    ->schema([
                                        // Intentionally no ->url() validator: relative paths
                                        // like "/pages/terms" would fail the
                                        // absolute-URL rule.  Free-text so the
                                        // operator can paste an https:// URL or a
                                        // relative path.
                                        TextInput::make('register_terms_url')
                                            ->label(__('filament/sa_landing_page_editor.register_terms_url'))
                                            ->placeholder('/pages/terms')
                                            ->maxLength(500)
                                            ->helperText(__('filament/sa_landing_page_editor.register_terms_url_helper')),
                                        TextInput::make('register_privacy_url')
                                            ->label(__('filament/sa_landing_page_editor.register_privacy_url'))
                                            ->placeholder('/pages/privacy')
                                            ->maxLength(500)
                                            ->helperText(__('filament/sa_landing_page_editor.register_privacy_url_helper')),
                                    ])
                                    ->columns(2),
                            ])
                            ->columns(1),

                        Tab::make(__('filament/sa_landing_page_editor.tab_footer'))
                            ->icon('heroicon-o-hashtag')
                            ->schema([
                                Section::make(__('sections.columns_visible_in_the_rich_footer'))
                                    ->description(__('filament/sa_landing_page_editor.columns_footer_description'))
                                    ->schema([
                                        Toggle::make('footer_show_brand_column')
                                            ->label(__('filament/sa_landing_page_editor.brand_tagline_column'))
                                            ->default(true),
                                        Toggle::make('footer_show_nav_links')
                                            ->label(__('filament/sa_landing_page_editor.product_links'))
                                            ->default(true),
                                        Toggle::make('footer_show_static_pages')
                                            ->label(__('filament/sa_landing_page_editor.static_pages_column'))
                                            ->default(true)
                                            ->helperText(__('filament/sa_landing_page_editor.static_pages_column_helper')),
                                    ])->columns(3),

                                Section::make(__('sections.social_icons'))
                                    ->description(__('filament/sa_landing_page_editor.social_icons_description'))
                                    ->schema([
                                        Toggle::make('footer_show_social')
                                            ->label(__('filament/sa_landing_page_editor.show_social_icons'))
                                            ->default(true)
                                            ->live(),
                                        TextInput::make('footer_social_x_url')
                                            ->label(__('filament/sa_landing_page_editor.social_x_url'))                                            ->placeholder(__('filament/sa_landing_page_editor.placeholder_x_url'))
                                            ->visible(fn ($get) => $get('footer_show_social')),
                                        TextInput::make('footer_social_github_url')
                                            ->label(__('filament/sa_landing_page_editor.social_github_url'))                                            ->placeholder(__('filament/sa_landing_page_editor.placeholder_github_url'))
                                            ->visible(fn ($get) => $get('footer_show_social')),
                                        TextInput::make('footer_social_linkedin_url')
                                            ->label(__('filament/sa_landing_page_editor.social_linkedin_url'))                                            ->placeholder(__('filament/sa_landing_page_editor.placeholder_linkedin_url'))
                                            ->visible(fn ($get) => $get('footer_show_social')),
                                        TextInput::make('footer_social_facebook_url')
                                            ->label(__('filament/sa_landing_page_editor.social_facebook_url'))                                            ->placeholder(__('filament/sa_landing_page_editor.placeholder_facebook_url'))
                                            ->visible(fn ($get) => $get('footer_show_social')),
                                        TextInput::make('footer_social_instagram_url')
                                            ->label(__('filament/sa_landing_page_editor.social_instagram_url'))                                            ->placeholder(__('filament/sa_landing_page_editor.placeholder_instagram_url'))
                                            ->visible(fn ($get) => $get('footer_show_social')),
                                        TextInput::make('footer_social_youtube_url')
                                            ->label(__('filament/sa_landing_page_editor.social_youtube_url'))                                            ->placeholder(__('filament/sa_landing_page_editor.placeholder_youtube_url'))
                                            ->visible(fn ($get) => $get('footer_show_social')),
                                    ])->columns(2),

                                Section::make(__('sections.bottom_bar'))
                                    ->schema([
                                        Textarea::make('footer_tagline')
                                            ->label(__('filament/sa_landing_page_editor.footer_tagline'))
                                            ->rows(2)
                                            ->placeholder(__('filament/sa_landing_page_editor.footer_tagline_placeholder')),
                                        TextInput::make('footer_copyright')
                                            ->label(__('filament/sa_landing_page_editor.footer_copyright'))
                                            ->placeholder(__('filament/sa_landing_page_editor.footer_copyright_placeholder', ['year' => date('Y')]))
                                            ->helperText(__('filament/sa_landing_page_editor.footer_copyright_helper')),
                                    ])->columns(1),
                            ])
                            ->columns(1),

                        Tab::make(__('filament/sa_landing_page_editor.tab_seo'))
                            ->icon('heroicon-o-magnifying-glass')
                            ->schema([
                                Section::make(__('filament/sa_landing_page_editor.seo_section'))
                                    ->description(__('filament/sa_landing_page_editor.seo_section_description'))
                                    ->schema([
                                        TextInput::make('seo_meta_title')
                                            ->label(__('filament/sa_landing_page_editor.seo_meta_title'))
                                            ->maxLength(70)
                                            ->placeholder(__('filament/sa_landing_page_editor.seo_meta_title_placeholder'))
                                            ->helperText(__('filament/sa_landing_page_editor.seo_meta_title_helper')),
                                        Textarea::make('seo_meta_description')
                                            ->label(__('filament/sa_landing_page_editor.seo_meta_description'))
                                            ->rows(3)
                                            ->maxLength(200)
                                            ->placeholder(__('filament/sa_landing_page_editor.seo_meta_description_placeholder'))
                                            ->helperText(__('filament/sa_landing_page_editor.seo_meta_description_helper')),
                                        TextInput::make('seo_meta_keywords')
                                            ->label(__('filament/sa_landing_page_editor.seo_meta_keywords'))
                                            ->placeholder(__('filament/sa_landing_page_editor.seo_meta_keywords_placeholder'))
                                            ->helperText(__('filament/sa_landing_page_editor.seo_meta_keywords_helper')),
                                        TextInput::make('seo_og_image_url')
                                            ->label(__('filament/sa_landing_page_editor.seo_og_image_url'))                                            ->placeholder(__('filament/sa_landing_page_editor.seo_og_image_url_placeholder'))
                                            ->helperText(__('filament/sa_landing_page_editor.seo_og_image_url_helper')),
                                    ])->columns(1),
                            ])
                            ->columns(1),

                        Tab::make(__('filament/sa_landing_page_editor.tab_translations'))
                            ->icon('heroicon-o-language')
                            ->schema([
                                Section::make(__('sections.per_language_overrides'))
                                    ->description(__('filament/sa_landing_page_editor.per_language_overrides_description'))
                                    ->schema([
                                        Tabs::make(__('filament/sa_landing_page_editor.tabs_locale'))
                                            ->tabs(array_map(
                                                fn (array $meta, string $code) => Tab::make(($meta['flag'] ?? '') . ' ' . ($meta['name'] ?? strtoupper($code)))
                                                    ->schema([
                                                        TextInput::make("translations.hero_headline.{$code}")->label(__('filament/sa_landing_page_editor.tr_hero_headline')),
                                                        TextInput::make("translations.hero_headline_highlight.{$code}")->label(__('filament/sa_landing_page_editor.tr_hero_headline_highlight')),
                                                        Textarea::make("translations.hero_subtext.{$code}")->label(__('filament/sa_landing_page_editor.tr_hero_subtext'))->rows(3),
                                                        TextInput::make("translations.hero_cta_label.{$code}")->label(__('filament/sa_landing_page_editor.tr_hero_cta_label')),
                                                        TextInput::make("translations.hero_note.{$code}")->label(__('filament/sa_landing_page_editor.tr_hero_note')),
                                                        TextInput::make("translations.hero_eyebrow.{$code}")->label(__('filament/sa_landing_page_editor.tr_hero_eyebrow')),
                                                        TextInput::make("translations.features_headline.{$code}")->label(__('filament/sa_landing_page_editor.tr_features_headline')),
                                                        Textarea::make("translations.features_subtext.{$code}")->label(__('filament/sa_landing_page_editor.tr_features_subtext'))->rows(2),
                                                        TextInput::make("translations.pricing_headline.{$code}")->label(__('filament/sa_landing_page_editor.tr_pricing_headline')),
                                                        Textarea::make("translations.pricing_subtext.{$code}")->label(__('filament/sa_landing_page_editor.tr_pricing_subtext'))->rows(2),
                                                        TextInput::make("translations.cta_headline.{$code}")->label(__('filament/sa_landing_page_editor.tr_cta_headline')),
                                                        Textarea::make("translations.cta_subtext.{$code}")->label(__('filament/sa_landing_page_editor.tr_cta_subtext'))->rows(2),
                                                        Textarea::make("translations.footer_tagline.{$code}")->label(__('filament/sa_landing_page_editor.tr_footer_tagline'))->rows(2),
                                                    ]),
                                                $this->nonEnglishLocales(),
                                                array_keys($this->nonEnglishLocales()),
                                            )),
                                    ]),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    /** Landing-content fields that hold a URL and accept scheme-less input. */
    private const URL_FIELDS = [
        'footer_social_x_url', 'footer_social_github_url', 'footer_social_linkedin_url',
        'footer_social_facebook_url', 'footer_social_instagram_url', 'footer_social_youtube_url',
        'seo_og_image_url',
    ];

    /**
     * Normalise a user-entered URL so a bare host ("facebook.com/acme") is
     * stored as a usable absolute link. Empty → null; absolute,
     * protocol-relative, root-relative and mailto:/tel: links are kept as-is.
     *
     * Filament's ->url() rule used to reject scheme-less input, and because the
     * whole landing form validates together, one bad URL blocked the ENTIRE
     * save — social icons AND the plain SEO fields. We accept lenient input and
     * normalise it here instead.
     */
    public static function normalizeUrl(?string $url): ?string
    {
        $url = trim((string) $url);
        if ($url === '') {
            return null;
        }

        // An explicit scheme? Allow only safe web schemes and reject everything
        // else (javascript:, data:, vbscript:, file:, …). These fields render
        // straight into <a href> / <meta content> on the public marketing site,
        // so a dangerous scheme is a stored-XSS vector — the same posture the
        // footer-copyright sanitiser already applies. The scheme pattern omits
        // "." so a host:port ("example.com:8080") is treated as a bare host, not
        // a scheme.
        if (preg_match('#^([a-z][a-z0-9+\-]*):#i', $url, $m)) {
            return in_array(strtolower($m[1]), ['http', 'https', 'mailto', 'tel'], true) ? $url : null;
        }

        // Scheme-relative ("//host") and root-relative ("/path") links pass through.
        if (str_starts_with($url, '//') || str_starts_with($url, '/')) {
            return $url;
        }

        // Bare host ("facebook.com/acme") → store as an absolute https link.
        return 'https://' . $url;
    }

    public function save(): void
    {
        // Demo lockdown: prevents casual demo visitors signed in as
        // SA from overwriting the public marketing landing copy /
        // toggling nav/footer visibility / vandalising the demo.
        // No-op in production.
        \App\Support\DemoMode::guard();

        $values = $this->form->getState();

        // Accept scheme-less social / OG-image links and store them absolute.
        foreach (self::URL_FIELDS as $urlField) {
            if (array_key_exists($urlField, $values)) {
                $values[$urlField] = self::normalizeUrl($values[$urlField]);
            }
        }

        $c = app(LandingContent::class);
        foreach ($values as $k => $v) {
            if (property_exists($c, $k)) {
                $c->{$k} = $v;
            }
        }
        $c->save();

        // Static-page nav/footer caches are short-lived (60 s) but we
        // bust them here so the operator sees edits immediately.
        foreach (config('locales.supported', ['en' => []]) as $code => $_) {
            \Illuminate\Support\Facades\Cache::forget('static_pages.nav.' . $code);
            \Illuminate\Support\Facades\Cache::forget('static_pages.footer.' . $code);
        }

        Notification::make()
            ->title(__('filament/sa_landing_page_editor.saved_title'))
            ->body(__('filament/sa_landing_page_editor.saved_body'))
            ->success()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('preview')
                ->label(__('filament/sa_landing_page_editor.preview'))
                ->icon('heroicon-o-eye')
                ->color('gray')
                // `?preview=1` bypasses MarketingController's normal
                // authenticated-user redirect so the super admin can
                // actually SEE the landing page they're editing without
                // being bounced back to /super-admin.
                ->url(url('/') . '?preview=1', shouldOpenInNewTab: true),

            Action::make('save')
                ->label(__('filament/sa_landing_page_editor.save_changes'))
                ->action('save')
                ->color('primary'),
        ];
    }
}
