<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasRolePermissions;
use App\Filament\Resources\IntegrationResource\Pages;
use App\Integrations\IntegrationRegistry;
use App\Models\Integration;
use App\Models\Tag;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class IntegrationResource extends Resource
{
    use HasRolePermissions;
    protected static string $permissionPrefix = 'integrations';
    protected static ?string $model = Integration::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-puzzle-piece';
    protected static string|\UnitEnum|null $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 30;

    public static function getNavigationLabel(): string
    {
        return __('filament/integrations.nav_label');
    }

    /**
     * Method-form model-label overrides.  Replace the static $modelLabel
     * property so the locale resolves at request time (the translator
     * is not yet bound at class-load time for the active session locale).
     */
    public static function getModelLabel(): string
    {
        return __('filament/integrations.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/integrations.plural_model_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('sections.integration_setup'))->schema([
                Select::make('type')
                    ->label(__('filament/integrations.integration_type'))
                    // Loop the canonical type slugs but use the translator-
                    // routing accessors for display so non-English locales see
                    // localised category + label combos. The const is the
                    // *source of slugs*, not a UI string.
                    ->options(collect(array_keys(IntegrationRegistry::LABELS))->mapWithKeys(fn($type) => [
                        $type => IntegrationRegistry::getCategoryLabel($type) . ': ' . IntegrationRegistry::getLabel($type),
                    ])->toArray())
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn($state, $set) => $set('name', $state ? IntegrationRegistry::getLabel($state) : '')),

                TextInput::make('name')
                    ->label(__('filament/integrations.display_name'))
                    ->required(),

                Toggle::make('enabled')
                    ->label(__('filament/integrations.enable_this_integration'))
                    ->default(false),
            ]),

            Section::make(__('sections.connection_configuration'))->schema([
                \Filament\Forms\Components\ViewField::make('config_notice')
                    ->view('filament.components.integration-config-notice')
                    ->hiddenLabel(),

                TextInput::make('config_webhook_url')
                    ->label(__('filament/integrations.webhook_url'))
                    ->url()
                    ->visible(fn($get) => in_array($get('type'), [
                        'zapier', 'n8n', 'make', 'pabbly', 'activepieces', 'workato',
                        'bitrix24', 'generic_webhook', 'rest_api_push', 'slack', 'microsoft_teams',
                    ]))
                    ->placeholder(__('filament/integrations.webhook_url_placeholder')),

                TextInput::make('config_api_key')
                    ->label(__('filament/integrations.api_key'))
                    ->password()
                    ->revealable()
                    ->visible(fn($get) => in_array($get('type'), [
                        'pipedrive', 'freshsales', 'monday', 'copper', 'streak', 'insightly',
                        'mailchimp', 'activecampaign', 'klaviyo', 'brevo', 'convertkit', 'drip',
                        'getresponse', 'moosend', 'mailerlite', 'notion', 'airtable', 'zendesk',
                        'intercom',
                    ])),

                TextInput::make('config_access_token')
                    ->label(__('filament/integrations.access_token_oauth'))
                    ->password()
                    ->revealable()
                    ->visible(fn($get) => in_array($get('type'), [
                        'hubspot', 'salesforce', 'zoho_crm', 'sugarcrm', 'google_sheets', 'intercom',
                    ])),

                TextInput::make('config_domain')
                    ->label(__('filament/integrations.domain_subdomain'))
                    ->visible(fn($get) => in_array($get('type'), ['freshsales', 'zendesk']))
                    ->placeholder(__('filament/integrations.domain_placeholder')),

                TextInput::make('config_instance_url')
                    ->label(__('filament/integrations.instance_url'))
                    ->url()
                    ->visible(fn($get) => in_array($get('type'), ['salesforce', 'sugarcrm', 'vtiger']))
                    ->placeholder(__('filament/integrations.instance_url_placeholder')),

                TextInput::make('config_list_id')
                    ->label(__('filament/integrations.list_audience_id'))
                    ->visible(fn($get) => in_array($get('type'), [
                        'mailchimp', 'activecampaign', 'klaviyo', 'brevo', 'getresponse', 'moosend', 'mailerlite',
                    ])),

                TextInput::make('config_board_id')
                    ->label(__('filament/integrations.board_id'))
                    ->numeric()
                    ->visible(fn($get) => $get('type') === 'monday'),

                TextInput::make('config_spreadsheet_id')
                    ->label(__('filament/integrations.spreadsheet_id'))
                    ->visible(fn($get) => $get('type') === 'google_sheets'),

                TextInput::make('config_sheet_name')
                    ->label(__('filament/integrations.sheet_name'))
                    ->default('Sheet1')
                    ->visible(fn($get) => $get('type') === 'google_sheets'),

                TextInput::make('config_database_id')
                    ->label(__('filament/integrations.notion_database_id'))
                    ->visible(fn($get) => $get('type') === 'notion'),

                TextInput::make('config_base_id')
                    ->label(__('filament/integrations.airtable_base_id'))
                    ->visible(fn($get) => $get('type') === 'airtable'),

                TextInput::make('config_table_id')
                    ->label(__('filament/integrations.airtable_table_name'))
                    ->visible(fn($get) => $get('type') === 'airtable'),

                TextInput::make('config_account_sid')
                    ->label(__('filament/integrations.account_sid'))
                    ->visible(fn($get) => $get('type') === 'twilio'),

                TextInput::make('config_auth_token')
                    ->label(__('filament/integrations.auth_token'))
                    ->password()
                    ->revealable()
                    ->visible(fn($get) => $get('type') === 'twilio'),

                TextInput::make('config_from_number')
                    ->label(__('filament/integrations.from_number'))
                    ->visible(fn($get) => in_array($get('type'), ['twilio', 'vonage'])),

                TextInput::make('config_api_secret')
                    ->label(__('filament/integrations.api_secret'))
                    ->password()
                    ->revealable()
                    ->visible(fn($get) => $get('type') === 'vonage'),

                TextInput::make('config_user_email')
                    ->label(__('filament/integrations.user_email'))
                    ->email()
                    ->visible(fn($get) => $get('type') === 'copper'),

                TextInput::make('config_pipeline_key')
                    ->label(__('filament/integrations.streak_pipeline_key'))
                    ->visible(fn($get) => $get('type') === 'streak'),

                TextInput::make('config_api_url')
                    ->label(__('filament/integrations.activecampaign_api_url'))
                    ->url()
                    ->visible(fn($get) => $get('type') === 'activecampaign')
                    ->placeholder(__('filament/integrations.activecampaign_api_url_placeholder')),

                TextInput::make('config_form_id')
                    ->label(__('filament/integrations.convertkit_form_id'))
                    ->visible(fn($get) => $get('type') === 'convertkit'),

                TextInput::make('config_account_id')
                    ->label(__('filament/integrations.drip_account_id'))
                    ->visible(fn($get) => $get('type') === 'drip'),

                TextInput::make('config_api_token')
                    ->label(__('filament/integrations.api_token'))
                    ->password()
                    ->revealable()
                    ->visible(fn($get) => $get('type') === 'drip'),

                TextInput::make('config_group_id')
                    ->label(__('filament/integrations.mailerlite_group_id'))
                    ->visible(fn($get) => $get('type') === 'mailerlite'),

                TextInput::make('config_data_center')
                    ->label(__('filament/integrations.mailchimp_data_center'))
                    ->visible(fn($get) => $get('type') === 'mailchimp')
                    ->default('us1'),

                TextInput::make('config_subdomain')
                    ->label(__('filament/integrations.zendesk_subdomain'))
                    ->visible(fn($get) => $get('type') === 'zendesk'),

                TextInput::make('config_email')
                    ->label(__('filament/integrations.zendesk_admin_email'))
                    ->email()
                    ->visible(fn($get) => $get('type') === 'zendesk'),

                TextInput::make('config_api_token_zendesk')
                    ->label(__('filament/integrations.zendesk_api_token'))
                    ->password()
                    ->revealable()
                    ->visible(fn($get) => $get('type') === 'zendesk'),

                Select::make('config_sf_object')
                    ->label(__('filament/integrations.salesforce_object_type'))
                    ->options([
                        'Lead'    => __('filament/integrations.salesforce_object_lead'),
                        'Contact' => __('filament/integrations.salesforce_object_contact'),
                    ])
                    ->default('Lead')
                    ->visible(fn($get) => $get('type') === 'salesforce'),

                Textarea::make('config_sms_template')
                    ->label(__('filament/integrations.sms_template'))
                    ->rows(2)
                    ->placeholder(__('filament/integrations.sms_template_placeholder'))
                    ->visible(fn($get) => in_array($get('type'), ['twilio', 'vonage'])),

                Textarea::make('config_message_template')
                    ->label(__('filament/integrations.slack_message_template'))
                    ->rows(3)
                    ->placeholder(__('filament/integrations.slack_message_template_placeholder'))
                    ->visible(fn($get) => $get('type') === 'slack'),

                Select::make('config_auth_type')
                    ->label(__('filament/integrations.auth_type'))
                    ->options([
                        'none' => __('filament/integrations.auth_type_none'),
                        'bearer' => __('filament/integrations.auth_type_bearer'),
                        'api_key' => __('filament/integrations.auth_type_api_key'),
                        'basic' => __('filament/integrations.auth_type_basic'),
                    ])
                    ->default('none')
                    ->live()
                    ->visible(fn($get) => in_array($get('type'), ['generic_webhook', 'rest_api_push'])),

                TextInput::make('config_auth_value')
                    ->label(__('filament/integrations.auth_value'))
                    ->password()
                    ->revealable()
                    ->visible(fn($get) => in_array($get('type'), ['generic_webhook', 'rest_api_push']) && $get('config_auth_type') !== 'none'),

                Textarea::make('config_body_template')
                    ->label(__('filament/integrations.json_body_template'))
                    ->rows(6)
                    ->placeholder(__('filament/integrations.json_body_template_placeholder'))
                    ->visible(fn($get) => in_array($get('type'), ['generic_webhook', 'rest_api_push'])),
            ]),

            Section::make(__('sections.field_mapping'))->schema([
                Repeater::make('field_mapping')
                    ->label(__('filament/integrations.field_mapping_label'))
                    ->schema([
                        Select::make('source_field')
                            ->label(__('filament/integrations.leadhub_field'))
                            ->options([
                                'first_name' => __('filament/integrations.lh_field_first_name'),
                                'last_name'  => __('filament/integrations.lh_field_last_name'),
                                'email'      => __('filament/integrations.lh_field_email'),
                                'phone'      => __('filament/integrations.lh_field_phone'),
                                'company'    => __('filament/integrations.lh_field_company'),
                                'source'     => __('filament/integrations.lh_field_source'),
                                'status'     => __('filament/integrations.lh_field_status'),
                                'lead_score' => __('filament/integrations.lh_field_lead_score'),
                                'address'    => __('filament/integrations.lh_field_address'),
                                'city'       => __('filament/integrations.lh_field_city'),
                                'country'    => __('filament/integrations.lh_field_country'),
                                'notes'      => __('filament/integrations.lh_field_notes'),
                            ])
                            ->required(),

                        TextInput::make('target_field')
                            ->label(__('filament/integrations.target_field_name'))
                            ->required()
                            ->placeholder(__('filament/integrations.target_field_placeholder')),
                    ])
                    ->columns(2)
                    ->addActionLabel(__('filament/integrations.add_field_mapping'))
                    ->collapsible()
                    ->itemLabel(fn(array $state): string => ($state['source_field'] ?? '?') . ' → ' . ($state['target_field'] ?? '?')),
            ]),

            Section::make(__('sections.filter_leads'))->schema([
                Select::make('filter_config.sources')
                    ->label(__('filament/integrations.filter_sources'))
                    ->multiple()
                    ->options(collect(\App\Enums\LeadSource::cases())->mapWithKeys(fn($e) => [
                        $e->value => $e instanceof \App\Enums\LeadSource ? $e->label() : ucfirst($e->value ?? $e),
                    ])),

                Select::make('filter_config.tags')
                    ->label(__('filament/integrations.filter_tags'))
                    ->multiple()
                    ->options(fn() => Tag::where('tenant_id', Auth::user()?->tenant_id)->pluck('name', 'name')),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('filament/integrations.name'))
                    ->description(fn($record) => IntegrationRegistry::getLabel($record->type))
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('type')
                    ->key('integration_category')
                    ->label(__('filament/integrations.category'))
                    ->formatStateUsing(fn($state) => IntegrationRegistry::getCategoryLabel($state))
                    ->badge()
                    ->sortable()
                    ->color(fn($state) => match (IntegrationRegistry::getCategory($state)) {
                        'CRM'              => 'success',
                        'Email Marketing'  => 'warning',
                        'Automation'       => 'info',
                        'Communication'    => 'danger',
                        'Spreadsheet'      => 'gray',
                        default            => 'gray',
                    }),

                IconColumn::make('enabled')
                    ->label(__('filament/integrations.enabled'))
                    ->boolean()
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('filament/integrations.col_status'))
                    ->badge()
                    ->sortable()
                    ->color(fn($state) => match ($state) {
                        'connected'    => 'success',
                        'disconnected' => 'gray',
                        'error'        => 'danger',
                        default        => 'gray',
                    }),

                TextColumn::make('last_synced_at')
                    ->label(__('filament/integrations.last_sync'))
                    ->since()
                    ->sortable()
                    ->placeholder(__('filament/integrations.last_sync_never')),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('filament/integrations.filter_label_status'))
                    ->options([
                        'connected'    => __('filament/integrations.status_connected'),
                        'disconnected' => __('filament/integrations.status_disconnected'),
                        'error'        => __('filament/integrations.status_error'),
                    ]),
                Tables\Filters\TernaryFilter::make('enabled')
                    ->label(__('filament/integrations.enabled')),
            ])
            ->actions([
                Action::make('test')
                    ->label(__('filament/integrations.action_test'))
                    ->icon('heroicon-o-signal')
                    ->action(function (Integration $record) {
                        try {
                            $connector = \App\Integrations\IntegrationRegistry::make($record->type, $record->getConfig());
                            $ok = $connector->testConnection();
                            $record->update(['status' => $ok ? Integration::STATUS_CONNECTED : Integration::STATUS_ERROR]);
                            if ($ok) {
                                Notification::make()->success()->title(__('filament/integrations.connection_successful'))->send();
                            } else {
                                Notification::make()->danger()->title(__('filament/integrations.connection_failed'))->send();
                            }
                        } catch (\Throwable $e) {
                            Notification::make()->danger()->title(__('filament/integrations.error_prefix') . $e->getMessage())->send();
                        }
                    }),

                Action::make('sync_logs')
                    ->label(__('filament/integrations.action_sync_logs'))
                    ->icon('heroicon-o-clock')
                    ->url(fn(Integration $record) => static::getUrl('sync-logs', ['record' => $record])),

                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkAction::make('enable')
                    ->label(__('filament/integrations.bulk_enable'))
                    ->icon('heroicon-o-check')
                    ->action(fn($records) => $records->each->update(['enabled' => true])),

                BulkAction::make('disable')
                    ->label(__('filament/integrations.bulk_disable'))
                    ->icon('heroicon-o-x-mark')
                    ->action(fn($records) => $records->each->update(['enabled' => false])),

                DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'     => Pages\ListIntegrations::route('/'),
            'create'    => Pages\CreateIntegration::route('/create'),
            'edit'      => Pages\EditIntegration::route('/{record}/edit'),
            'sync-logs' => Pages\IntegrationSyncLogs::route('/{record}/sync-logs'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->where('tenant_id', Auth::user()?->tenant_id);
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        return static::encryptConfigFields($data);
    }

    public static function mutateFormDataBeforeSave(array $data): array
    {
        return static::encryptConfigFields($data);
    }

    private static function encryptConfigFields(array $data): array
    {
        $configFields = collect($data)->filter(fn($v, $k) => str_starts_with($k, 'config_'))->toArray();
        $config = [];
        foreach ($configFields as $key => $value) {
            $configKey = substr($key, 7);
            $config[$configKey] = $value;
            unset($data[$key]);
        }
        if (! empty($config)) {
            $integration = new Integration();
            $integration->setConfig($config);
            $data['config'] = $integration->config;
        }
        $data['tenant_id'] = Auth::user()?->tenant_id;
        return $data;
    }
}
