<?php

namespace App\Filament\Pages\Settings;

use App\Services\EnvEditor;
use App\Services\LicenseService;
use Filament\Actions\Action;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class LicenseSettingsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\UnitEnum|null $navigationGroup = 'Users & Access';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-key';
    protected static ?int $navigationSort = 28;
    protected string $view = 'filament.pages.settings.license-settings-page';

    public static function getNavigationLabel(): string
    {
        return __('filament/license_settings.nav_label');
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public ?array $data = [];
    public ?\App\Billing\License\LicenseStatus $licenseStatus = null;

    public function getTitle(): string
    {
        return __('filament/license_settings.page_title');
    }

    public function mount(): void
    {
        $this->form->fill([
            'license_key' => config('leadhub.license.key', ''),
        ]);

        $this->licenseStatus = app(LicenseService::class)->statusDto();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make(__('sections.license_key'))
                    ->description(__('filament/license_settings.section_description'))
                    ->schema([
                        TextInput::make('license_key')
                            ->label(__('filament/license_settings.license_key'))
                            ->placeholder(__('filament/license_settings.license_key_placeholder'))
                            ->password()
                            ->revealable()
                            ->helperText(__('filament/license_settings.license_key_helper')),
                    ])->columns(1),
            ])
            ->statePath('data');
    }

    public function verify(): void
    {
        $state = $this->form->getState();
        $code  = trim($state['license_key'] ?? '');

        if (empty($code)) {
            Notification::make()->title(__('notifications.license_required'))->danger()->send();
            return;
        }

        $svc    = app(LicenseService::class);
        $svc->clearCache();
        $result = \App\Billing\License\LicenseStatus::fromArray($svc->validate($code));
        $this->licenseStatus = $result;

        if ($result->isValid()) {
            try {
                $env = app(EnvEditor::class);
                $env->set('LEADHUB_LICENSE_KEY', $code);
            } catch (\Throwable $e) {
                logger()->warning('[License] Could not write to .env: ' . $e->getMessage());
            }

            $msg = $result->buyer
                ? __('filament/license_settings.verified_with_buyer', ['buyer' => $result->buyer])
                : __('filament/license_settings.verified_generic');
            Notification::make()->title($msg)->success()->send();
        } else {
            Notification::make()->title($result->message)->danger()->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('verify')
                ->label(__('filament/license_settings.verify_license'))
                ->icon('heroicon-o-check-badge')
                ->action('verify')
                ->color('primary'),
        ];
    }
}
