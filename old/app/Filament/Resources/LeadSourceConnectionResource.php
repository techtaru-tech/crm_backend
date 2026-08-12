<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasRolePermissions;
use App\Enums\LeadSource;
use App\Filament\Resources\LeadSourceConnectionResource\Pages;
use App\Models\LeadSourceConnection;
use App\Services\ConnectorRegistry;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text as SchemaText;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class LeadSourceConnectionResource extends Resource
{
    use HasRolePermissions;
    protected static string $permissionPrefix = 'integrations';
    protected static ?string $model = LeadSourceConnection::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-puzzle-piece';
    protected static string|UnitEnum|null $navigationGroup = 'Integrations';
    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('filament/lead_source_connections.nav_label');
    }

    /**
     * Method-form model-label overrides.  Replace the static $modelLabel
     * property so the locale resolves at request time (the translator
     * is not yet bound at class-load time for the active session locale).
     */
    public static function getModelLabel(): string
    {
        return __('filament/lead_source_connections.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/lead_source_connections.plural_model_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('sections.connection_details'))->schema([
                TextInput::make('name')
                    ->label(__('filament/lead_source_connections.connection_name'))
                    ->required()
                    ->maxLength(100)
                    ->helperText(__('filament/lead_source_connections.connection_name_helper')),
                Select::make('source')
                    ->label(__('filament/lead_source_connections.source'))
                    ->options(LeadSource::options())
                    ->required()
                    ->disabledOn('edit')
                    ->live(),
                Toggle::make('active')
                    ->label(__('filament/lead_source_connections.active'))
                    ->default(true),
            ])->columns(2),

            Section::make(__('sections.webhook_url'))
                ->schema([
                    TextInput::make('webhook_url')
                        ->label(__('filament/lead_source_connections.your_webhook_url'))
                        ->disabled()
                        ->dehydrated(false)
                        ->copyable()
                        // webhook_url is a model ACCESSOR (not a real column, not in
                        // $appends), so it's absent from the record's attributesToArray()
                        // that Filament fills the EDIT form with — and ->default() only
                        // fires on CREATE, never on edit. So the field rendered blank even
                        // though the table column (which reads the accessor directly) shows
                        // it. Hydrate the disabled field explicitly from the accessor so the
                        // URL shows (and is copyable) on edit.
                        ->afterStateHydrated(fn (TextInput $component, ?LeadSourceConnection $record) => $component->state(
                            $record?->webhook_url ?? __('filament/lead_source_connections.webhook_url_placeholder_default')
                        ))
                        ->columnSpanFull()
                        ->helperText(__('filament/lead_source_connections.webhook_url_helper')),
                ])
                ->visibleOn('edit'),

            Section::make(__('sections.oauth_authorization'))
                ->description(__('filament/lead_source_connections.oauth_description'))
                ->schema([
                    SchemaText::make(__('filament/lead_source_connections.oauth_instruction_text')),
                ])
                ->visible(fn (callable $get): bool => in_array($get('source'), [
                    'meta', 'instagram', 'tiktok', 'linkedin', 'google_ads', 'microsoft_ads',
                ])),

            Section::make(__('sections.api_credentials'))
                ->description(__('filament/lead_source_connections.credentials_description'))
                ->schema(function (callable $get): array {
                    $source = $get('source');

                    if (! $source) {
                        return [
                            SchemaText::make(__('filament/lead_source_connections.credentials_select_source')),
                        ];
                    }

                    try {
                        $registry  = app(ConnectorRegistry::class);
                        $connector = $registry->resolve($source);
                        $schema    = $connector->getConnectionFormSchema();
                    } catch (\Throwable $e) {
                        return [];
                    }

                    if (empty($schema)) {
                        return [
                            SchemaText::make(__('filament/lead_source_connections.credentials_none_required')),
                        ];
                    }

                    return collect($schema)
                        ->map(function (string $definition, string $fieldName): TextInput {
                            $parts    = explode('|', $definition);
                            $type     = $parts[0] ?? 'string';
                            $required = ($parts[1] ?? '') === 'required';

                            // Translator-first label resolution. Concrete
                            // connector schemas now embed translated labels via
                            // BaseConnector::field(), but legacy / third-party
                            // connectors that omit the third pipe-segment fall
                            // back here: we still try the translator under
                            // `lead_sources_connectors.<field>` before the
                            // historical ucwords(str_replace(...)) form, so
                            // even unfixed connectors render translatable
                            // labels when an entry exists. The English
                            // ucwords-derived string remains the canonical
                            // missing-translation fallback to keep the form
                            // legible if no lang entry is provided.
                            $fallback = ucwords(str_replace('_', ' ', $fieldName));
                            if (isset($parts[2]) && $parts[2] !== '') {
                                $label = $parts[2];
                            } else {
                                $translatorKey = 'lead_sources_connectors.' . $fieldName;
                                $translated    = __($translatorKey);
                                $label         = ($translated === $translatorKey)
                                    ? $fallback
                                    : (string) $translated;
                            }

                            $isSecret = preg_match('/(secret|password|token)/i', $fieldName);

                            $input = TextInput::make("credentials.{$fieldName}")
                                ->label($label)
                                ->required($required);

                            if ($isSecret) {
                                $input->password()->revealable();
                            }

                            return $input;
                        })
                        ->values()
                        ->all();
                })
                ->live(),

            Section::make(__('sections.message_source_settings'))
                ->description(__('filament/lead_source_connections.message_source_description'))
                ->schema([
                    TagsInput::make('settings.qualification_keywords')
                        ->label(__('filament/lead_source_connections.qualification_keywords'))
                        ->helperText(__('filament/lead_source_connections.qualification_keywords_helper'))
                        ->placeholder(__('filament/lead_source_connections.qualification_keywords_placeholder'))
                        ->columnSpanFull(),
                ])
                ->visible(fn (callable $get): bool => in_array($get('source'), [
                    'whatsapp', 'viber', 'telegram', 'instagram',
                ])),

            Section::make(__('sections.meta_page_selection'))
                ->description(__('filament/lead_source_connections.meta_page_description'))
                ->schema([
                    Select::make('credentials.page_id')
                        ->label(__('filament/lead_source_connections.active_page'))
                        ->helperText(__('filament/lead_source_connections.active_page_helper'))
                        ->options(function ($record): array {
                            if (! $record) {
                                return [];
                            }
                            $pages = $record->credentials['available_pages'] ?? [];
                            return collect($pages)->pluck('name', 'id')->all();
                        })
                        ->live()
                        ->afterStateUpdated(function ($state, $record) {
                            if (! $record || ! $state) {
                                return;
                            }
                            $pages = $record->credentials['available_pages'] ?? [];
                            $page  = collect($pages)->firstWhere('id', $state);
                            if ($page) {
                                $credentials                    = $record->credentials ?? [];
                                $credentials['page_access_token'] = $page['access_token'];
                                $credentials['page_name']       = $page['name'];
                                $record->update(['credentials' => $credentials]);
                            }
                        })
                        ->columnSpanFull(),
                ])
                ->visible(fn (callable $get, $record): bool =>
                    in_array($get('source'), ['meta', 'instagram']) &&
                    ! empty($record?->credentials['available_pages'])
                )
                ->visibleOn('edit'),

            Section::make(__('sections.field_mapping'))
                ->description(__('filament/lead_source_connections.field_mapping_description'))
                ->schema([
                    KeyValue::make('settings.field_mapping')
                        ->label(__('filament/lead_source_connections.field_mapping'))
                        ->keyLabel(__('filament/lead_source_connections.source_field_name'))
                        ->valueLabel(__('filament/lead_source_connections.leadhub_field_value'))
                        ->columnSpanFull(),
                ])
                ->visible(fn (callable $get): bool => in_array($get('source'), [
                    'typeform', 'jotform',
                ])),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('filament/lead_source_connections.col_name'))
                    ->searchable()
                    ->weight('medium'),
                TextColumn::make('source')
                    ->label(__('filament/lead_source_connections.col_source'))
                    ->formatStateUsing(fn (string $state) => LeadSource::tryFrom($state)?->label() ?? ucfirst($state))
                    ->badge()
                    ->color('primary'),
                TextColumn::make('status')
                    ->label(__('filament/lead_source_connections.col_status'))
                    ->badge()
                    ->color(fn (string $state) => match($state) {
                        'connected'    => 'success',
                        'disconnected' => 'gray',
                        'error'        => 'danger',
                        default        => 'warning',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'connected'    => __('filament/lead_source_connections.status_connected'),
                        'disconnected' => __('filament/lead_source_connections.status_disconnected'),
                        'error'        => __('filament/lead_source_connections.status_error'),
                        default        => (string) $state,
                    }),
                TextColumn::make('leads_count')
                    ->label(__('filament/lead_source_connections.leads'))
                    ->counts('leads')
                    ->sortable(),
                TextColumn::make('last_received_at')
                    ->label(__('filament/lead_source_connections.last_lead'))
                    ->since()
                    ->sortable(),
                TextColumn::make('webhook_url')
                    ->label(__('filament/lead_source_connections.webhook_url'))
                    ->limit(40)
                    ->copyable()
                    ->tooltip(fn ($record) => $record->webhook_url),
            ])
            ->filters([
                SelectFilter::make('source')
                    ->label(__('filament/lead_source_connections.filter_label_source'))
                    ->options(LeadSource::options()),
                SelectFilter::make('status')
                    ->label(__('filament/lead_source_connections.filter_label_status'))
                    ->options([
                        'connected'    => __('filament/lead_source_connections.status_connected'),
                        'disconnected' => __('filament/lead_source_connections.status_disconnected'),
                        'error'        => __('filament/lead_source_connections.status_error'),
                    ]),
            ])
            ->actions([
                Action::make('oauth')
                    ->label(__('filament/lead_source_connections.action_connect_oauth'))
                    ->icon('heroicon-o-arrow-right-circle')
                    ->color('success')
                    ->url(fn (LeadSourceConnection $record): string => route('oauth.redirect', [
                        'source'       => $record->source,
                        'connectionId' => $record->id,
                    ]))
                    ->openUrlInNewTab(false)
                    ->visible(fn (LeadSourceConnection $record): bool => in_array($record->source, [
                        'meta', 'instagram', 'tiktok', 'linkedin', 'google_ads', 'microsoft_ads',
                    ]))
                    ->tooltip(__('filament/lead_source_connections.action_connect_oauth_tooltip')),
                Action::make('test')
                    ->label(__('filament/lead_source_connections.action_test'))
                    ->icon('heroicon-o-bolt')
                    // Defensive: only offer "Test" for sources that actually
                    // have a registered connector. Every shipped source now
                    // does (incl. "manual"), so Test shows for all of them —
                    // but a stray legacy/custom source value in old data would
                    // otherwise hit the unguarded resolve() below (this closure
                    // is not try/caught, unlike the credentials form section)
                    // and throw "No connector registered for source: …".
                    ->visible(fn (LeadSourceConnection $record): bool => app(ConnectorRegistry::class)->has($record->source))
                    ->action(function (LeadSourceConnection $record) {
                        $registry  = app(ConnectorRegistry::class);
                        $connector = $registry->resolve($record->source);
                        $success   = $connector->testConnection($record);

                        if ($success) {
                            $record->markConnected();
                            Notification::make()->title(__('notifications.connection_ok'))->success()->send();
                        } else {
                            $record->markError(__('filament/lead_source_connections.test_failed_message'));
                            Notification::make()->title(__('notifications.connection_failed'))->danger()->send();
                        }
                    })
                    ->requiresConfirmation(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->emptyStateHeading(__('filament/lead_source_connections.empty_heading'))
            ->emptyStateDescription(__('filament/lead_source_connections.empty_description'))
            ->emptyStateIcon('heroicon-o-puzzle-piece');
    }

    public static function getEloquentQuery(): Builder
    {
        $tenantId = \App\Support\TenantContext::currentId();

        return parent::getEloquentQuery()
            ->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId));
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListLeadSourceConnections::route('/'),
            'create' => Pages\CreateLeadSourceConnection::route('/create'),
            'edit'   => Pages\EditLeadSourceConnection::route('/{record}/edit'),
        ];
    }
}
