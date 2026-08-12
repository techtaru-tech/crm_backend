<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use UnitEnum;

/**
 * In-app help center / knowledge base at /admin/help.
 *
 * Static FAQ baked into the script for v1 — keeps the install
 * self-contained (no external help-desk service required).  The
 * 18 canonical articles cover the questions every tenant asks in
 * the first week (onboarding, forms, pipelines, automations, email,
 * billing, privacy, team).
 *
 * Future: replace the static array with a help_articles DB table +
 * SA-side WYSIWYG editor when help volume justifies it.
 */
class HelpCenterPage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-question-mark-circle';
    protected static string|UnitEnum|null $navigationGroup = 'Account';
    protected static ?int $navigationSort = 80;
    protected static ?string $slug = 'help';
    protected string $view = 'filament.pages.help-center';

    public string $search = '';

    public static function getNavigationLabel(): string
    {
        return __('help_center_articles.nav_label');
    }

    public function getTitle(): string|Htmlable
    {
        return __('help_center_articles.page_title');
    }

    public function getHeading(): string
    {
        return __('help_center_articles.heading');
    }

    public function getSubheading(): ?string
    {
        return __('help_center_articles.subheading');
    }

    public function getViewData(): array
    {
        $articles = $this->articles();

        if ($this->search !== '') {
            $needle = mb_strtolower($this->search);
            $articles = array_filter($articles, function ($a) use ($needle) {
                return str_contains(mb_strtolower($a['title'] . ' ' . $a['body'] . ' ' . implode(' ', $a['tags'] ?? [])), $needle);
            });
        }

        $grouped = [];
        foreach ($articles as $article) {
            $cat = $article['category'] ?? __('help_center_articles.category.general');
            $grouped[$cat] ??= [];
            $grouped[$cat][] = $article;
        }
        ksort($grouped);

        return [
            'grouped'      => $grouped,
            'total_count'  => count($articles),
            'search_term'  => $this->search,
        ];
    }

    /**
     * The 18 canonical FAQ articles.  Content lives in
     * lang/<locale>/help_center_articles.php so buyers can translate
     * or adapt without touching this class.  Operators override by
     * extending this class or pointing the view at a different array.
     *
     * The map below preserves the legacy article ordering (matches
     * the order shipped in earlier installs) and pairs each entry
     * with its stable lang-key slug + category slug + tag list.
     *
     * @return list<array{category:string, title:string, body:string, tags:list<string>}>
     */
    protected function articles(): array
    {
        // Each inner array holds tag slugs (snake_case) that resolve via
        // __('filament/help_center.tags.<slug>') so translators can adapt
        // tag wording without touching this class.
        $map = [
            ['add_first_lead',       'getting_started', ['leads', 'first_lead', 'onboarding']],
            ['import_csv',           'getting_started', ['import', 'csv', 'migration']],
            ['branding_setup',       'getting_started', ['branding', 'white_label', 'logo']],
            ['embed_form',           'forms',           ['forms', 'embed', 'website']],
            ['prefill_form',         'forms',           ['forms', 'utm', 'prefill']],
            ['pipeline_stages',      'pipelines',       ['pipeline', 'stages', 'forecast']],
            ['pipeline_per_team',    'pipelines',       ['pipeline', 'teams']],
            ['automation_intro',     'automations',     ['automation', 'workflow']],
            ['automation_manual',    'automations',     ['automation', 'manual', 'testing']],
            ['smtp_connect',         'email',           ['smtp', 'email', 'configuration']],
            ['email_deliverability', 'email',           ['email', 'deliverability', 'spam']],
            ['payment_method',       'billing',         ['billing', 'card', 'payment']],
            ['download_invoices',    'billing',         ['billing', 'invoices', 'receipts']],
            ['cancel_subscription',  'billing',         ['cancellation', 'billing']],
            ['export_data',          'privacy',         ['gdpr', 'export', 'data']],
            ['delete_workspace',     'privacy',         ['gdpr', 'deletion']],
            ['invite_member',        'team',            ['team', 'invite', 'members']],
            ['member_vs_manager',    'team',            ['team', 'roles', 'permissions']],
        ];

        $articles = [];
        foreach ($map as [$slug, $categorySlug, $tagSlugs]) {
            $articles[] = [
                'category' => __('help_center_articles.category.' . $categorySlug),
                'title'    => __('help_center_articles.articles.' . $slug . '.title'),
                'body'     => __('help_center_articles.articles.' . $slug . '.body'),
                'tags'     => array_map(
                    static fn (string $tagSlug): string => __('filament/help_center.tags.' . $tagSlug),
                    $tagSlugs,
                ),
            ];
        }

        return $articles;
    }
}
