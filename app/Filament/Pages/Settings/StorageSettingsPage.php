<?php

namespace App\Filament\Pages\Settings;

use App\Filament\Concerns\PageRequiresPermission;

use App\Services\SettingsService;
use Filament\Actions\Action;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;

class StorageSettingsPage extends Page implements HasForms
{
    use PageRequiresPermission;

    protected static string $requiredPermission = 'settings.manage';

    use InteractsWithForms;

    protected static string|\UnitEnum|null $navigationGroup = 'Advanced';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-circle-stack';
    protected static ?int $navigationSort = 25;
    protected string $view = 'filament.pages.settings.storage-settings-page';

    public static function getNavigationLabel(): string
    {
        return __('filament/storage_settings.nav_label');
    }

    public ?array $data = [];

    public function getTitle(): string
    {
        return __('filament/storage_settings.page_title');
    }

    public function mount(): void
    {
        $tenant   = auth()->user()?->tenant;
        $settings = $tenant?->settings ?? [];
        $storage  = $settings['storage'] ?? [];

        $this->form->fill([
            'disk'          => $storage['disk'] ?? 'local',
            's3_endpoint'   => $storage['s3_endpoint'] ?? '',
            's3_bucket'     => $storage['s3_bucket'] ?? env('AWS_BUCKET', ''),
            's3_key'        => $storage['s3_key'] ?? env('AWS_ACCESS_KEY_ID', ''),
            's3_secret'     => '',
            's3_region'     => $storage['s3_region'] ?? env('AWS_DEFAULT_REGION', 'us-east-1'),
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make(__('sections.storage_driver'))
                    ->schema([
                        Select::make('disk')
                            ->label(__('filament/storage_settings.storage_disk'))
                            ->options([
                                'local'  => __('filament/storage_settings.disk_local'),
                                's3'     => __('filament/storage_settings.disk_s3'),
                            ])
                            ->live()
                            ->required(),
                    ]),

                Section::make(__('sections.s3_configuration'))
                    ->description(__('filament/storage_settings.s3_section_description'))
                    ->visible(fn ($get) => $get('disk') === 's3')
                    ->schema([
                        TextInput::make('s3_endpoint')
                            ->label(__('filament/storage_settings.endpoint_url'))
                            ->placeholder(__('filament/storage_settings.endpoint_placeholder'))
                            ->helperText(__('filament/storage_settings.endpoint_helper')),

                        TextInput::make('s3_bucket')->label(__('filament/storage_settings.bucket_name'))->required(),

                        TextInput::make('s3_region')->label(__('filament/storage_settings.region'))->required(),

                        TextInput::make('s3_key')->label(__('filament/storage_settings.access_key_id')),

                        TextInput::make('s3_secret')
                            ->label(__('filament/storage_settings.secret_access_key'))
                            ->password()
                            ->revealable()
                            ->helperText(__('filament/storage_settings.secret_helper')),
                    ])->columns(2),
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

        $values  = $this->form->getState();
        $current = $tenant->getSetting('storage', []);
        $storage = array_merge($current, [
            'disk'       => $values['disk'],
            's3_endpoint'=> $values['s3_endpoint'] ?? '',
            's3_bucket'  => $values['s3_bucket'] ?? '',
            's3_key'     => $values['s3_key'] ?? '',
            's3_region'  => $values['s3_region'] ?? '',
        ]);

        if (! empty($values['s3_secret'])) {
            $storage['s3_secret'] = encrypt($values['s3_secret']);
        }

        app(SettingsService::class)->forTenant($tenant)->set('storage', $storage);
        Notification::make()->title(__('notifications.storage_saved'))->success()->send();
    }

    public function testStorage(): void
    {
        try {
            $disk = auth()->user()?->tenant?->getSetting('storage.disk', 'local');
            Storage::disk($disk)->put('.test-write', 'ok');
            Storage::disk($disk)->delete('.test-write');
            Notification::make()->title(__('notifications.storage_test_ok'))->success()->send();
        } catch (\Throwable $e) {
            Notification::make()->title(__('notifications.storage_test_failed', ['error' => $e->getMessage()]))->danger()->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('testStorage')
                ->label(__('filament/storage_settings.test_connection'))
                ->icon('heroicon-o-beaker')
                ->action('testStorage')
                ->color('gray'),

            Action::make('save')
                ->label(__('filament/storage_settings.save_settings'))
                ->action('save')
                ->color('primary'),
        ];
    }
}
