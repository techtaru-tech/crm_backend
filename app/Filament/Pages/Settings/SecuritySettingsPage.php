<?php

namespace App\Filament\Pages\Settings;

use App\Models\AuditLog;
use App\Services\TenantSecurityService;
use Filament\Actions\Action;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * Per-tenant security settings page. Each workspace manages its own
 * 2FA enforcement, IP whitelist, session lifetime, and lockout policy
 * — stored in tenants.settings.security.* via TenantSecurityService.
 */
class SecuritySettingsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\UnitEnum|null $navigationGroup = 'Team & Access';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';
    protected static ?int $navigationSort = 22;
    protected string $view = 'filament.pages.settings.security-settings-page';

    public ?array $data = [];

    public static function getNavigationLabel(): string
    {
        return __('filament/security_settings.navigation_label');
    }

    public function getTitle(): string
    {
        return __('filament/security_settings.title');
    }

    public function mount(): void
    {
        $tenant   = auth()->user()?->tenant;
        $security = app(TenantSecurityService::class)->all($tenant);

        $this->form->fill([
            'enforce_2fa'        => (bool) ($security['enforce_2fa'] ?? false),
            'ip_whitelist'       => (array) ($security['ip_whitelist'] ?? []),
            'session_lifetime'   => (int) ($security['session_lifetime'] ?? 120),
            'max_login_attempts' => (int) ($security['max_login_attempts'] ?? 5),
            'lockout_duration'   => (int) ($security['lockout_duration'] ?? 15),
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make(__('sections.authentication'))
                    ->description(__('filament/security_settings.auth_section_description'))
                    ->schema([
                        Toggle::make('enforce_2fa')
                            ->label(__('filament/security_settings.enforce_2fa_label'))
                            ->helperText(new \Illuminate\Support\HtmlString(
                                e(__('filament/security_settings.enforce_2fa_helper_prefix')) . ' ' .
                                '<a href="' . route('filament.admin.pages.two-factor-setup') . '" ' .
                                'class="text-primary-600 underline font-medium">' .
                                e(__('filament/security_settings.enforce_2fa_helper_link')) . '</a>'
                            )),

                        TextInput::make('session_lifetime')
                            ->label(__('filament/security_settings.session_lifetime_label'))
                            ->numeric()
                            ->minValue(15)
                            ->maxValue(43200)
                            ->suffix(__('filament/security_settings.minutes_suffix')),
                    ])->columns(2),

                Section::make(__('sections.rate_limiting_lockout'))
                    ->schema([
                        TextInput::make('max_login_attempts')
                            ->label(__('filament/security_settings.max_login_attempts_label'))
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(20),

                        TextInput::make('lockout_duration')
                            ->label(__('filament/security_settings.lockout_duration_label'))
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(1440)
                            ->suffix(__('filament/security_settings.minutes_suffix')),
                    ])->columns(2),

                Section::make(__('sections.ip_whitelist'))
                    ->description(__('filament/security_settings.ip_whitelist_section_description'))
                    ->schema([
                        TagsInput::make('ip_whitelist')
                            ->label(__('filament/security_settings.ip_whitelist_label'))
                            ->placeholder(__('filament/security_settings.ip_whitelist_placeholder'))
                            ->helperText(__('filament/security_settings.ip_whitelist_helper')),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $tenant = auth()->user()?->tenant;
        if (! $tenant) {
            Notification::make()->title(__('notifications.no_workspace_active'))->danger()->send();
            return;
        }

        $values  = $this->form->getState();
        $service = app(TenantSecurityService::class);
        $old     = $service->all($tenant);

        try {
            $new = $service->save($tenant, $values);

            try {
                AuditLog::record(
                    action: 'settings.security.updated',
                    auditable: $tenant,
                    oldValues: $old,
                    newValues: $new,
                    tags: 'settings,security',
                );
            } catch (\Throwable) {
                // Audit failures shouldn't block the save.
            }

            Notification::make()->title(__('notifications.security_saved'))->success()->send();
        } catch (\Throwable $e) {
            Notification::make()->title(__('notifications.save_failed', ['error' => $e->getMessage()]))->danger()->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label(__('filament/security_settings.action_save'))
                ->action('save')
                ->color('primary'),
        ];
    }
}
