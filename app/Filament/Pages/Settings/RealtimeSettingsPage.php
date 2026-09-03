<?php

namespace App\Filament\Pages\Settings;

use App\Filament\Concerns\PageRequiresPermission;

use App\Models\AuditLog;
use App\Services\SettingsService;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class RealtimeSettingsPage extends Page implements HasForms
{
    use PageRequiresPermission;

    protected static string $requiredPermission = 'settings.manage';

    use InteractsWithForms;

    protected static string|\UnitEnum|null $navigationGroup = 'Advanced';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-signal';
    protected static ?int $navigationSort = 26;
    protected string $view = 'filament.pages.settings.realtime-settings-page';

    public ?array $data = [];

    public static function getNavigationLabel(): string
    {
        return __('filament/realtime_settings.navigation_label');
    }

    public function getTitle(): string
    {
        return __('filament/realtime_settings.title');
    }

    public function mount(): void
    {
        $tenant   = auth()->user()?->tenant;
        $settings = $tenant?->settings ?? [];
        $realtime = $settings['realtime'] ?? [];

        $this->form->fill([
            'driver'          => $realtime['driver'] ?? config('broadcasting.default', 'pusher'),
            'pusher_app_id'   => $realtime['pusher_app_id'] ?? config('broadcasting.connections.pusher.app_id', ''),
            'pusher_key'      => $realtime['pusher_key'] ?? config('broadcasting.connections.pusher.key', ''),
            'pusher_secret'   => $realtime['pusher_secret'] ?? '',
            'pusher_cluster'  => $realtime['pusher_cluster'] ?? config('broadcasting.connections.pusher.options.cluster', 'us2'),
            'pusher_host'     => $realtime['pusher_host'] ?? '',
            'pusher_port'     => $realtime['pusher_port'] ?? 443,
            'pusher_scheme'   => $realtime['pusher_scheme'] ?? 'https',
            'enabled'         => $realtime['enabled'] ?? true,
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make(__('sections.broadcast_driver'))
                    ->description(__('filament/realtime_settings.driver_section_description'))
                    ->schema([
                        Toggle::make('enabled')
                            ->label(__('filament/realtime_settings.enable_realtime_label'))
                            ->helperText(__('filament/realtime_settings.enable_realtime_helper'))
                            ->columnSpanFull(),

                        Select::make('driver')
                            ->label(__('filament/realtime_settings.driver_label'))
                            ->options([
                                'pusher' => __('filament/realtime_settings.driver_option_pusher'),
                                'null'   => __('filament/realtime_settings.driver_option_null'),
                            ])
                            ->default('pusher')
                            ->live()
                            ->helperText(__('filament/realtime_settings.driver_helper')),
                    ])->columns(2),

                Section::make(__('sections.pusher_reverb_connection'))
                    ->description(__('filament/realtime_settings.pusher_section_description'))
                    ->visible(fn ($get) => $get('driver') === 'pusher')
                    ->schema([
                        TextInput::make('pusher_app_id')
                            ->label(__('filament/realtime_settings.pusher_app_id_label'))
                            ->required(),

                        TextInput::make('pusher_key')
                            ->label(__('filament/realtime_settings.pusher_key_label'))
                            ->required(),

                        TextInput::make('pusher_secret')
                            ->label(__('filament/realtime_settings.pusher_secret_label'))
                            ->password()
                            ->revealable()
                            ->required(),

                        TextInput::make('pusher_cluster')
                            ->label(__('filament/realtime_settings.pusher_cluster_label'))
                            ->placeholder('us2')
                            ->helperText(__('filament/realtime_settings.pusher_cluster_helper')),

                        TextInput::make('pusher_host')
                            ->label(__('filament/realtime_settings.pusher_host_label'))
                            ->placeholder('ws.example.com')
                            ->helperText(__('filament/realtime_settings.pusher_host_helper')),

                        TextInput::make('pusher_port')
                            ->label(__('filament/realtime_settings.pusher_port_label'))
                            ->numeric()
                            ->default(443),

                        Select::make('pusher_scheme')
                            ->label(__('filament/realtime_settings.pusher_scheme_label'))
                            ->options([
                                'https' => __('filament/realtime_settings.pusher_scheme_https'),
                                'http'  => __('filament/realtime_settings.pusher_scheme_http'),
                            ])
                            ->default('https'),
                    ])->columns(3),

                Section::make(__('sections.status'))
                    ->schema([
                        Placeholder::make('connection_info')
                            ->label('')
                            ->content(__('filament/realtime_settings.status_content')),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $tenant = auth()->user()?->tenant;
        if (! $tenant) {
            Notification::make()->title(__('notifications.no_tenant_found'))->danger()->send();
            return;
        }

        $values = $this->form->getState();
        app(SettingsService::class)->forTenant($tenant)->set('realtime', $values);

        try {
            AuditLog::record(
                action: 'settings.realtime.updated',
                auditable: $tenant,
                oldValues: [],
                newValues: array_intersect_key($values, array_flip(['driver', 'enabled', 'pusher_app_id', 'pusher_key', 'pusher_cluster', 'pusher_host', 'pusher_port', 'pusher_scheme'])),
                tags: 'settings',
            );
        } catch (\Throwable $e) {
            logger()->warning('[Realtime] Failed to write audit log: ' . $e->getMessage());
        }

        // The actual save lives at the SettingsService->set('realtime', $values)
        // call above (line ~137).  The previous Spatie RealtimeSettings
        // class was removed in H15 — it had no migration so app(RealtimeSettings::class)
        // always threw, and the try/catch buried that for years.  All
        // reads now flow through tenants.settings.realtime JSON column.
        Notification::make()->title(__('notifications.realtime_saved'))->success()->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label(__('filament/realtime_settings.action_save'))
                ->action('save')
                ->color('primary'),
        ];
    }
}
