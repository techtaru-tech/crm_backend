<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasRolePermissions;
use App\Filament\Resources\FormResource\Pages;
use App\Models\Form;
use App\Models\FormField;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use UnitEnum;

class FormResource extends Resource
{
    use HasRolePermissions;
    protected static string $permissionPrefix = 'forms';
    protected static ?string $model = Form::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';
    protected static string|UnitEnum|null $navigationGroup = 'Leads';
    protected static ?int $navigationSort = 5;

    public static function getNavigationLabel(): string
    {
        return __('filament/forms.nav_label');
    }

    public static function getModelLabel(): string
    {
        return __('filament/forms.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/forms.plural_model_label');
    }

    public static function getEloquentQuery(): Builder
    {
        $tenantId = \App\Support\TenantContext::currentId();
        return parent::getEloquentQuery()->where('tenant_id', $tenantId);
    }

    public static function form(Schema $schema): Schema
    {
        $tenantId = fn() => \App\Support\TenantContext::currentId();

        return $schema->components([
            Section::make(__('sections.basic_details'))->schema([
                TextInput::make('name')
                    ->label(__('filament/forms.form_name'))
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('slug', \Illuminate\Support\Str::slug($state));
                    }),
                TextInput::make('slug')
                    ->label(__('filament/forms.slug'))
                    ->required()
                    ->maxLength(255)
                    ->helperText(__('filament/forms.slug_help')),
                TextInput::make('title')
                    ->label(__('filament/forms.display_title'))
                    ->maxLength(255),
                Textarea::make('description')
                    ->label(__('filament/forms.description'))
                    ->rows(2),
                TextInput::make('submit_label')
                    ->label(__('filament/forms.submit_button_label'))
                    // Locale-aware default — the migration column carries no
                    // English SQL DEFAULT, so the create form seeds the
                    // tenant-visible "Submit" label through the translator.
                    ->default(__('forms_public.submit'))
                    ->required(),
                Textarea::make('thank_you_message')
                    ->label(__('filament/forms.thank_you_message'))
                    ->rows(2),
                TextInput::make('redirect_url')
                    ->label(__('filament/forms.redirect_url'))
                    ->url()
                    ->nullable(),
                Toggle::make('multi_step')
                    ->label(__('filament/forms.multi_step_form'))
                    ->helperText(__('filament/forms.multi_step_form_help')),
                Toggle::make('active')
                    ->label(__('filament/forms.active'))
                    ->default(true),
            ])->columns(2),

            Section::make(__('sections.appearance'))->schema([
                ColorPicker::make('background_color')
                    ->label(__('filament/forms.background_color')),
                TextInput::make('background_image_url')
                    ->label(__('filament/forms.background_image_url'))
                    ->url()
                    ->nullable(),
                TextInput::make('font_family')
                    ->label(__('filament/forms.google_font_name'))
                    ->placeholder(__('filament/forms.google_font_name_placeholder'))
                    ->nullable(),
                TextInput::make('logo_url')
                    ->label(__('filament/forms.logo_url'))
                    ->url()
                    ->nullable(),
            ])->columns(2)->collapsed(),

            Section::make(__('sections.pipeline_connection'))->schema([
                Select::make('pipeline_id')
                    ->label(__('filament/forms.pipeline'))
                    ->options(fn() => Pipeline::where('tenant_id', $tenantId())->pluck('name', 'id'))
                    ->nullable()
                    ->live(),
                Select::make('pipeline_stage_id')
                    ->label(__('filament/forms.stage'))
                    ->options(function (callable $get) use ($tenantId) {
                        $pid = $get('pipeline_id');
                        if (! $pid) return [];
                        return PipelineStage::where('pipeline_id', $pid)
                            ->where('tenant_id', $tenantId())
                            ->pluck('name', 'id');
                    })
                    ->nullable(),
            ])->columns(2)->collapsed(),

            Section::make(__('sections.spam_protection'))->schema([
                Toggle::make('recaptcha_enabled')
                    ->label(__('filament/forms.enable_recaptcha'))
                    ->live(),
                TextInput::make('recaptcha_site_key')
                    ->label(__('filament/forms.recaptcha_site_key'))
                    ->nullable()
                    ->visible(fn(callable $get) => $get('recaptcha_enabled')),
                TextInput::make('recaptcha_secret_key')
                    ->label(__('filament/forms.recaptcha_secret_key'))
                    ->password()
                    ->nullable()
                    ->visible(fn(callable $get) => $get('recaptcha_enabled')),
            ])->columns(2)->collapsed(),

            Section::make(__('sections.form_fields'))
                ->description(__('filament/forms.fields_section_description'))
                ->schema([
                    Repeater::make('fields')
                        ->relationship('fields', modifyQueryUsing: fn($query) => $query->orderBy('sort_order'))
                        ->label('')
                        ->reorderable('sort_order')
                        ->addActionLabel(__('filament/forms.add_field'))
                        ->deleteAction(
                            // Filament 4 repeater item actions inject
                            // $arguments (the row's UUID lives under the
                            // 'item' key) and $component (the Repeater).
                            // A bare `array $item` parameter is NOT an
                            // injectable — passing it threw "[$item] was
                            // unresolvable" and 500'd the entire form
                            // edit page.  Resolve the row from the
                            // repeater state instead; disable delete
                            // only on the locked GDPR field.
                            fn(\Filament\Actions\Action $action) => $action->disabled(
                                function (array $arguments, $component): bool {
                                    $itemKey = $arguments['item'] ?? null;
                                    if ($itemKey === null) {
                                        return false;
                                    }
                                    $state = $component->getState();
                                    $row   = is_array($state) ? ($state[$itemKey] ?? []) : [];
                                    return (bool) ($row['locked'] ?? false);
                                }
                            )
                        )
                        ->itemLabel(fn(array $state): ?string => ($state['locked'] ?? false)
                            ? '🔒 ' . ($state['label'] ?? __('filament/forms.field_gdpr_consent_default_label')) . ' ' . __('filament/forms.field_gdpr_locked_suffix')
                            : null)
                        ->schema([
                            Select::make('type')
                                ->label(__('filament/forms.field_type'))
                                ->options(FormField::typeLabels())
                                ->required()
                                ->live()
                                ->disabled(fn(callable $get): bool => (bool) $get('locked')),
                            TextInput::make('label')
                                ->label(__('filament/forms.field_label'))
                                ->required()
                                ->disabled(fn(callable $get): bool => (bool) $get('locked')),
                            TextInput::make('placeholder')
                                ->label(__('filament/forms.field_placeholder'))
                                ->nullable()
                                ->disabled(fn(callable $get): bool => (bool) $get('locked')),
                            TextInput::make('field_key')
                                ->label(__('filament/forms.field_key'))
                                ->placeholder(__('filament/forms.field_key_placeholder'))
                                ->helperText(__('filament/forms.field_key_help'))
                                ->nullable()
                                ->disabled(fn(callable $get): bool => (bool) $get('locked')),
                            TextInput::make('step_number')
                                ->label(__('filament/forms.step_number'))
                                ->numeric()
                                ->default(1)
                                ->minValue(1)
                                ->disabled(fn(callable $get): bool => (bool) $get('locked')),
                            Toggle::make('required')
                                ->label(__('filament/forms.required'))
                                ->default(false)
                                ->disabled(fn(callable $get): bool => (bool) $get('locked')),
                            Textarea::make('options')
                                ->label(__('filament/forms.options'))
                                ->visible(fn(callable $get) => in_array($get('type'), ['select', 'multi_checkbox', 'radio']))
                                ->helperText(__('filament/forms.options_help'))
                                ->disabled(fn(callable $get): bool => (bool) $get('locked'))
                                ->dehydrateStateUsing(function ($state) {
                                    if (is_string($state)) {
                                        return array_filter(array_map('trim', explode("\n", $state)));
                                    }
                                    return $state;
                                })
                                ->formatStateUsing(function ($state) {
                                    if (is_array($state)) {
                                        return implode("\n", $state);
                                    }
                                    return $state;
                                }),
                        ])
                        ->columns(3)
                        ->defaultItems(0),
                ]),

            Section::make(__('sections.embed_snippet'))
                ->description(__('filament/forms.embed_section_description'))
                ->schema([
                    Placeholder::make('embed_snippet_display')
                        ->label(__('filament/forms.widget_embed_code'))
                        ->content(function ($record): HtmlString {
                            if (! $record) {
                                return new HtmlString('<em class="text-gray-400">' . e(__('filament/forms.save_form_first_for_snippet')) . '</em>');
                            }
                            // The Copy button uses a declarative data-copy-from
                            // attribute consumed by public/js/views/shared/clipboard.js
                            // — no inline onclick handler, per CodeCanyon's
                            // "no inline scripts" rule.
                            $snippet      = e($record->embed_snippet);
                            $snippetAttr  = e($record->embed_snippet);
                            $publicUrl    = e($record->public_url);
                            $copyLabel    = e(__('filament/forms.copy'));
                            $copiedLabel  = e(__('filament/forms.copied'));
                            $publicUrlEn  = e(__('filament/forms.public_url'));
                            return new HtmlString('
                                <div class="relative">
                                    <pre class="bg-gray-900 text-green-400 rounded-lg p-4 text-xs overflow-x-auto whitespace-pre-wrap break-all font-mono border border-gray-700">' . $snippet . '</pre>
                                    <button
                                        type="button"
                                        class="absolute top-2 right-2 bg-gray-700 hover:bg-gray-600 text-white text-xs px-2 py-1 rounded"
                                        data-copy-from="' . $snippetAttr . '"
                                        data-copied-label="' . $copiedLabel . '"
                                    >' . $copyLabel . '</button>
                                </div>
                                <p class="mt-2 text-sm text-gray-500">
                                    ' . $publicUrlEn . ': <a href="' . $publicUrl . '" target="_blank" class="text-primary-600 underline">' . $publicUrl . '</a>
                                </p>
                            ');
                        }),
                ])
                ->hidden(fn($record) => $record === null)
                ->collapsed(),

            Section::make(__('sections.live_preview'))
                ->description(__('filament/forms.live_preview_description'))
                ->schema([
                    Placeholder::make('live_preview_iframe')
                        ->label('')
                        // Live-preview markup + static styles extracted to a
                        // Blade view + dedicated stylesheet so this file no
                        // longer carries inline style="" attributes or
                        // English string literals (translatable-strings rule).
                        // See:
                        //   resources/views/filament/resources/forms/live-preview.blade.php
                        //   public/css/views/filament/resources/forms/live-preview.css
                        ->content(function ($record): HtmlString {
                            if (! $record) {
                                return new HtmlString('');
                            }
                            return new HtmlString(
                                view('filament.resources.forms.live-preview', [
                                    'record' => $record,
                                ])->render()
                            );
                        }),
                ])
                ->hidden(fn($record) => $record === null)
                ->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('filament/forms.col_form_name'))->searchable()->sortable(),
                TextColumn::make('slug')->label(__('filament/forms.col_slug'))->sortable(),
                IconColumn::make('active')->label(__('filament/forms.col_active'))->boolean(),
                IconColumn::make('multi_step')->label(__('filament/forms.col_multi_step'))->boolean(),
                TextColumn::make('submissions_count')
                    ->label(__('filament/forms.col_submissions'))
                    ->counts('submissions')
                    ->sortable(),
                TextColumn::make('created_at')->label(__('filament/forms.col_created'))->dateTime()->sortable(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc')
            // Empty-state scaffolding for first-time tenants.
            ->emptyStateHeading(__('filament/forms.empty_heading'))
            ->emptyStateDescription(__('filament/forms.empty_description'))
            ->emptyStateIcon('heroicon-o-rectangle-stack')
            ->emptyStateActions([
                \Filament\Actions\Action::make('create_form_empty')
                    ->label(__('filament/forms.create_first_form'))
                    ->icon('heroicon-o-plus')
                    ->url(fn () => static::getUrl('create'))
                    ->button(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'     => Pages\ListForms::route('/'),
            'create'    => Pages\CreateForm::route('/create'),
            'edit'      => Pages\EditForm::route('/{record}/edit'),
            'view'      => Pages\ViewForm::route('/{record}/view'),
            'analytics' => Pages\FormAnalytics::route('/{record}/analytics'),
        ];
    }
}
