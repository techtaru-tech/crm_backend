<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasRolePermissions;
use App\Filament\Resources\LandingPageResource\Pages;
use App\Models\Form as FormModel;
use App\Models\LandingPage;
use App\Models\LandingPageSection;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ReplicateAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use UnitEnum;

class LandingPageResource extends Resource
{
    use HasRolePermissions;
    protected static string $permissionPrefix = 'forms';
    protected static ?string $model = LandingPage::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-group';
    protected static string|UnitEnum|null $navigationGroup = 'Tools';
    protected static ?int $navigationSort = 18;

    public static function getNavigationLabel(): string
    {
        return __('filament/landing_pages.nav_label');
    }

    public static function getModelLabel(): string
    {
        return __('filament/landing_pages.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/landing_pages.plural_model_label');
    }

    public static function getEloquentQuery(): Builder
    {
        $tenantId = \App\Support\TenantContext::currentId();
        // Eager-load `tenant` because the Preview Action's url()
        // callback (see ~line 440) reads $record->tenant?->slug per
        // row - without this, listing 50 pages fires 50 extra
        // SELECT-tenants-by-id queries.
        return parent::getEloquentQuery()
            ->where('tenant_id', $tenantId)
            ->with(['tenant']);
    }

    /**
     * Dangerous-field gate.
     *
     * Only super admins or tenant admins (the "admin" spatie role) may edit
     * raw HTML sections, custom CSS, or custom JS — all XSS surfaces that
     * execute on every visitor of this tenant's public landing pages.
     */
    protected static function canEditDangerousFields(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }
        if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return true;
        }
        return method_exists($user, 'hasRole') && $user->hasRole('admin');
    }

    public static function form(Schema $schema): Schema
    {
        $tenantId = fn() => \App\Support\TenantContext::currentId();

        return $schema->components([
            Tabs::make(__('filament/landing_pages.landing_page'))
                ->columnSpanFull()
                ->tabs([

                    Tab::make(__('filament/landing_pages.tab_basics'))
                        ->icon('heroicon-o-identification')
                        ->schema([
                            TextInput::make('name')
                                ->label(__('filament/landing_pages.internal_name'))
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function ($state, callable $set, $get) {
                                    if (empty($get('slug'))) {
                                        $set('slug', Str::slug($state));
                                    }
                                }),
                            TextInput::make('slug')
                                ->label(__('filament/landing_pages.slug'))
                                ->required()
                                ->maxLength(255)
                                ->disabled(fn($record) => $record !== null)
                                ->dehydrated()
                                ->helperText(function () {
                                    // Show the actual live URL pattern for
                                    // the signed-in user's workspace, so
                                    // operators don't have to guess what
                                    // "/{workspace}/{slug}" will resolve
                                    // to.  Falls back to the generic
                                    // placeholder during cold render (no
                                    // auth context).
                                    $ws = auth()->user()?->tenant?->slug;
                                    $base = rtrim(config('app.url'), '/');
                                    return $ws
                                        ? __('filament/landing_pages.slug_help_with_workspace', ['url' => $base . '/' . $ws . '/{slug}'])
                                        : __('filament/landing_pages.slug_help_generic');
                                }),
                            TextInput::make('title')
                                ->label(__('filament/landing_pages.browser_title'))
                                ->maxLength(255),
                            Textarea::make('meta_description')
                                ->label(__('filament/landing_pages.meta_description'))
                                ->rows(2)
                                ->maxLength(300),
                            TextInput::make('og_image_url')
                                ->label(__('filament/landing_pages.og_image_url'))
                                ->url()
                                ->maxLength(500),
                            TextInput::make('favicon_url')
                                ->label(__('filament/landing_pages.favicon_url'))
                                ->url()
                                ->maxLength(500),
                            Select::make('status')
                                ->label(__('filament/landing_pages.status'))
                                ->options([
                                    'draft'     => __('filament/landing_pages.option_status_draft'),
                                    'published' => __('filament/landing_pages.option_status_published'),
                                    'archived'  => __('filament/landing_pages.option_status_archived'),
                                ])
                                ->default('draft')
                                ->required(),
                        ])->columns(2),

                    Tab::make(__('filament/landing_pages.tab_sections'))
                        ->icon('heroicon-o-squares-2x2')
                        ->schema([
                            Repeater::make('sections')
                                ->relationship('sections', modifyQueryUsing: fn($query) => $query->orderBy('sort_order'))
                                ->label('')
                                ->reorderable('sort_order')
                                ->orderColumn('sort_order')
                                ->addActionLabel(__('filament/landing_pages.add_section'))
                                ->itemLabel(fn(array $state): ?string => isset($state['type'])
                                    ? (LandingPageSection::typeLabels()[$state['type']] ?? ucfirst($state['type']))
                                    : __('filament/landing_pages.new_section'))
                                ->collapsible()
                                ->cloneable()
                                ->schema([
                                    Select::make('type')
                                        ->label(__('filament/landing_pages.section_type'))
                                        ->options(LandingPageSection::typeLabels())
                                        ->required()
                                        ->live()
                                        ->default('hero'),

                                    Toggle::make('is_visible')
                                        ->label(__('filament/landing_pages.visible'))
                                        ->default(true),

                                    // ---- HERO ----
                                    TextInput::make('content.eyebrow')
                                        ->label(__('filament/landing_pages.hero_eyebrow'))
                                        ->visible(fn(callable $get) => $get('type') === 'hero'),
                                    TextInput::make('content.headline')
                                        ->label(__('filament/landing_pages.headline'))
                                        ->visible(fn(callable $get) => in_array($get('type'), ['hero', 'features', 'testimonials', 'cta', 'gallery', 'pricing', 'faq', 'form'])),
                                    Textarea::make('content.subheadline')
                                        ->label(__('filament/landing_pages.subheadline'))
                                        ->rows(2)
                                        ->visible(fn(callable $get) => in_array($get('type'), ['hero', 'features', 'pricing', 'form'])),
                                    TextInput::make('content.cta_label')
                                        ->label(__('filament/landing_pages.cta_label'))
                                        ->visible(fn(callable $get) => $get('type') === 'hero'),
                                    TextInput::make('content.cta_url')
                                        ->label(__('filament/landing_pages.cta_url'))
                                        ->visible(fn(callable $get) => $get('type') === 'hero'),
                                    TextInput::make('content.image_url')
                                        ->label(__('filament/landing_pages.image_url'))
                                        ->visible(fn(callable $get) => $get('type') === 'hero'),
                                    Select::make('content.alignment')
                                        ->label(__('filament/landing_pages.alignment'))
                                        ->options([
                                            'left'   => __('filament/landing_pages.option_align_left'),
                                            'center' => __('filament/landing_pages.option_align_center'),
                                        ])
                                        ->default('center')
                                        ->visible(fn(callable $get) => $get('type') === 'hero'),
                                    Select::make('content.background')
                                        ->label(__('filament/landing_pages.background'))
                                        ->options([
                                            'gradient' => __('filament/landing_pages.option_bg_gradient'),
                                            'solid'    => __('filament/landing_pages.option_bg_solid'),
                                            'image'    => __('filament/landing_pages.option_bg_image'),
                                        ])
                                        ->default('gradient')
                                        ->visible(fn(callable $get) => $get('type') === 'hero'),

                                    // ---- FEATURES ----
                                    // Distinct state path per list-type section.  All
                                    // four list repeaters (features/testimonials/gallery/
                                    // faq) used to bind to the SAME `content.items` path,
                                    // which created ambiguous Livewire state bindings —
                                    // clicking "add" on one repeater misfired ("some
                                    // category won't add object like testimonial").  Each
                                    // now owns its own key; the blades read the new key
                                    // with an `items` fallback + a data migration moves
                                    // existing rows over.
                                    Repeater::make('content.feature_items')
                                        ->label(__('filament/landing_pages.feature_items'))
                                        ->schema([
                                            TextInput::make('title')->label(__('filament/landing_pages.feature_title'))->required(),
                                            Textarea::make('body')->label(__('filament/landing_pages.feature_body'))->rows(2),
                                            TextInput::make('icon')->label(__('filament/landing_pages.icon_key_optional')),
                                        ])
                                        ->visible(fn(callable $get) => $get('type') === 'features')
                                        ->defaultItems(3)
                                        ->columns(2),

                                    // ---- FORM ----
                                    Select::make('content.form_id')
                                        ->label(__('filament/landing_pages.form'))
                                        ->options(fn() => FormModel::where('tenant_id', $tenantId())
                                            ->where('active', true)
                                            ->pluck('name', 'id'))
                                        ->searchable()
                                        ->visible(fn(callable $get) => $get('type') === 'form'),
                                    Textarea::make('content.success_message')
                                        ->label(__('filament/landing_pages.success_message'))
                                        ->rows(2)
                                        ->visible(fn(callable $get) => $get('type') === 'form'),

                                    // ---- TESTIMONIALS ----
                                    Repeater::make('content.testimonials')
                                        ->label(__('filament/landing_pages.testimonials'))
                                        ->schema([
                                            Textarea::make('quote')->label(__('filament/landing_pages.testimonial_quote'))->rows(2)->required(),
                                            TextInput::make('author')->label(__('filament/landing_pages.testimonial_author'))->required(),
                                            TextInput::make('role')->label(__('filament/landing_pages.testimonial_role')),
                                            TextInput::make('avatar_url')->label(__('filament/landing_pages.avatar_url'))->url(),
                                        ])
                                        ->visible(fn(callable $get) => $get('type') === 'testimonials')
                                        ->defaultItems(2)
                                        ->columns(2),

                                    // ---- CTA ----
                                    Textarea::make('content.body')
                                        ->label(__('filament/landing_pages.body'))
                                        ->rows(2)
                                        ->visible(fn(callable $get) => $get('type') === 'cta'),
                                    TextInput::make('content.button_label')
                                        ->label(__('filament/landing_pages.button_label'))
                                        ->visible(fn(callable $get) => $get('type') === 'cta'),
                                    TextInput::make('content.button_url')
                                        ->label(__('filament/landing_pages.button_url'))
                                        ->visible(fn(callable $get) => $get('type') === 'cta'),
                                    Select::make('content.background')
                                        ->label(__('filament/landing_pages.background'))
                                        ->options([
                                            'indigo' => __('filament/landing_pages.option_palette_indigo'),
                                            'gray'   => __('filament/landing_pages.option_palette_gray'),
                                            'white'  => __('filament/landing_pages.option_palette_white'),
                                        ])
                                        ->default('indigo')
                                        ->visible(fn(callable $get) => $get('type') === 'cta'),

                                    // ---- GALLERY ----
                                    Repeater::make('content.gallery_items')
                                        ->label(__('filament/landing_pages.images'))
                                        ->schema([
                                            // Image URL is optional — the gallery blade skips
                                            // items with empty image_url, so blank rows (e.g.
                                            // placeholder rows from a template) don't block save.
                                            TextInput::make('image_url')
                                                ->label(__('filament/landing_pages.image_url'))
                                                ->url()
                                                ->placeholder(__('filament/landing_pages.image_url_placeholder')),
                                            TextInput::make('caption')->label(__('filament/landing_pages.caption')),
                                        ])
                                        ->visible(fn(callable $get) => $get('type') === 'gallery')
                                        ->defaultItems(1)
                                        ->columns(2),

                                    // ---- PRICING ----
                                    Repeater::make('content.plans')
                                        ->label(__('filament/landing_pages.plans'))
                                        ->schema([
                                            TextInput::make('name')->label(__('filament/landing_pages.plan_name'))->required(),
                                            TextInput::make('price')->label(__('filament/landing_pages.plan_price'))->required(),
                                            TextInput::make('interval')->label(__('filament/landing_pages.plan_interval'))->default(fn () => __('filament/landing_pages.plan_interval_short_mo')),
                                            Textarea::make('features')
                                                ->label(__('filament/landing_pages.features_one_per_line'))
                                                ->rows(4)
                                                ->dehydrateStateUsing(fn($state) => is_string($state)
                                                    ? array_values(array_filter(array_map('trim', explode("\n", $state))))
                                                    : $state)
                                                ->formatStateUsing(fn($state) => is_array($state)
                                                    ? implode("\n", $state)
                                                    : $state),
                                            TextInput::make('cta_label')->label(__('filament/landing_pages.plan_cta_label'))->default(fn() => __('filament/landing_pages.plan_cta_default')),
                                            TextInput::make('cta_url')->label(__('filament/landing_pages.plan_cta_url')),
                                            Toggle::make('highlight')->label(__('filament/landing_pages.highlight_this_plan')),
                                        ])
                                        ->visible(fn(callable $get) => $get('type') === 'pricing')
                                        ->defaultItems(3)
                                        ->columns(2),

                                    // ---- FAQ ----
                                    Repeater::make('content.faqs')
                                        ->label(__('filament/landing_pages.q_and_a'))
                                        ->schema([
                                            TextInput::make('question')->label(__('filament/landing_pages.faq_question'))->required(),
                                            Textarea::make('answer')->label(__('filament/landing_pages.faq_answer'))->rows(3)->required(),
                                        ])
                                        ->visible(fn(callable $get) => $get('type') === 'faq')
                                        ->defaultItems(3),

                                    // ---- HTML ----
                                    // Admin-only: raw HTML is an XSS vector. The server-side strip
                                    // is best-effort; a tenant admin who can already push <script>
                                    // into Custom JS shouldn't be newly-empowered by this field.
                                    Textarea::make('content.code')
                                        ->label(__('filament/landing_pages.raw_html'))
                                        ->rows(8)
                                        ->helperText(__('filament/landing_pages.raw_html_help'))
                                        ->visible(fn(callable $get) => $get('type') === 'html' && static::canEditDangerousFields()),

                                    Hidden::make('sort_order'),
                                ])
                                ->defaultItems(0),
                        ]),

                    Tab::make(__('filament/landing_pages.tab_appearance'))
                        ->icon('heroicon-o-paint-brush')
                        ->schema([
                            ColorPicker::make('primary_color')
                                ->label(__('filament/landing_pages.primary_color'))
                                ->default('#4f46e5'),
                            ColorPicker::make('background_color')
                                ->label(__('filament/landing_pages.background_color'))
                                ->default('#ffffff'),
                            Select::make('font_family')
                                ->label(__('filament/landing_pages.font_family'))
                                ->options([
                                    'Inter'      => 'Inter',
                                    'Roboto'     => 'Roboto',
                                    'Open Sans'  => 'Open Sans',
                                    'Lato'       => 'Lato',
                                    'Poppins'    => 'Poppins',
                                    'System'     => __('filament/landing_pages.font_system_default'),
                                ])
                                ->default('Inter'),
                        ])->columns(2),

                    Tab::make(__('filament/landing_pages.tab_integration'))
                        ->icon('heroicon-o-link')
                        ->schema([
                            Select::make('form_id')
                                ->label(__('filament/landing_pages.linked_form'))
                                ->options(fn() => FormModel::where('tenant_id', $tenantId())
                                    ->where('active', true)
                                    ->pluck('name', 'id'))
                                ->nullable()
                                ->searchable()
                                ->helperText(__('filament/landing_pages.linked_form_help')),
                            Select::make('pipeline_id')
                                ->label(__('filament/landing_pages.pipeline'))
                                ->options(fn() => Pipeline::where('tenant_id', $tenantId())->pluck('name', 'id'))
                                ->nullable()
                                ->live(),
                            Select::make('pipeline_stage_id')
                                ->label(__('filament/landing_pages.stage'))
                                ->options(function (callable $get) use ($tenantId) {
                                    $pid = $get('pipeline_id');
                                    if (! $pid) return [];
                                    return PipelineStage::where('pipeline_id', $pid)
                                        ->where('tenant_id', $tenantId())
                                        ->pluck('name', 'id');
                                })
                                ->nullable(),
                            TextInput::make('redirect_on_submit')
                                ->label(__('filament/landing_pages.redirect_on_submit'))
                                ->url()
                                ->maxLength(500),
                        ])->columns(2),

                    Tab::make(__('filament/landing_pages.tab_advanced'))
                        ->icon('heroicon-o-code-bracket')
                        // Admin-only tab: custom JS is executable on every
                        // visitor of this tenant's landing pages. A regular
                        // staff user should not be able to bake in tracking
                        // or phishing JS.
                        ->visible(fn () => static::canEditDangerousFields())
                        ->schema([
                            Textarea::make('custom_css')
                                ->label(__('filament/landing_pages.custom_css'))
                                ->rows(8)
                                ->helperText(__('filament/landing_pages.custom_css_help')),
                            Textarea::make('custom_js')
                                ->label(__('filament/landing_pages.custom_js'))
                                ->rows(8)
                                ->helperText(__('filament/landing_pages.custom_js_help')),
                        ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('filament/landing_pages.name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->label(__('filament/landing_pages.slug'))
                    ->copyable()
                    ->copyMessage(__('filament/landing_pages.slug_copied'))
                    ->searchable(),

                BadgeColumn::make('status')
                    ->label(__('filament/landing_pages.status'))
                    ->colors([
                        'gray'    => 'draft',
                        'success' => 'published',
                        'warning' => 'archived',
                    ])
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'draft'     => __('filament/landing_pages.status_draft'),
                        'published' => __('filament/landing_pages.status_published'),
                        'archived'  => __('filament/landing_pages.status_archived'),
                        default     => (string) $state,
                    }),

                TextColumn::make('views_count')
                    ->label(__('filament/landing_pages.views'))
                    ->numeric()
                    ->sortable(),

                TextColumn::make('conversions_count')
                    ->label(__('filament/landing_pages.conversions'))
                    ->numeric()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('filament/landing_pages.created'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Action::make('preview')
                    ->label(__('filament/landing_pages.preview'))
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    // Uses the new /{workspace}/{slug} route; ?t= and
                    // &preview=1 remain on the preview URL so the
                    // staff-preview path in the controller still works
                    // for drafts.
                    ->url(
                        fn (LandingPage $record) => url(
                            '/' . ($record->tenant?->slug ?: 'p') . '/' . $record->slug
                        ) . '?t=' . $record->tenant_id . '&preview=1',
                        shouldOpenInNewTab: true
                    ),

                EditAction::make(),

                ReplicateAction::make()
                    ->label(__('filament/landing_pages.duplicate'))
                    ->icon('heroicon-o-document-duplicate')
                    ->beforeReplicaSaved(function (LandingPage $replica, LandingPage $record) {
                        $replica->name   = $record->name . ' (copy)';
                        $replica->slug   = $record->slug . '-copy-' . Str::random(4);
                        $replica->status = 'draft';
                        $replica->views_count = 0;
                        $replica->conversions_count = 0;
                    })
                    ->afterReplicaSaved(function (LandingPage $replica, LandingPage $record) {
                        foreach ($record->sections()->orderBy('sort_order')->get() as $section) {
                            $replica->sections()->create([
                                'type'       => $section->type,
                                'sort_order' => $section->sort_order,
                                'content'    => $section->content,
                                'is_visible' => $section->is_visible,
                            ]);
                        }
                    }),

                DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListLandingPages::route('/'),
            'create' => Pages\CreateLandingPage::route('/create'),
            'edit'   => Pages\EditLandingPage::route('/{record}/edit'),
        ];
    }
}
