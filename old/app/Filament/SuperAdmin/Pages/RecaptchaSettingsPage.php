<?php

namespace App\Filament\SuperAdmin\Pages;

use App\Settings\RecaptchaSettings;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use UnitEnum;

/**
 * SA page at /super-admin/recaptcha-settings for configuring
 * Google reCAPTCHA v3 protection across login + register forms.
 *
 * Distinct from the per-tenant ScriptSettings page — reCAPTCHA
 * is an install-wide security control, not a tenant preference,
 * so it lives in its own SA page with a single save path.
 */
class RecaptchaSettingsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';
    protected static ?int $navigationSort = 82;
    protected static string|UnitEnum|null $navigationGroup = 'System';
    protected static ?string $navigationLabel = 'reCAPTCHA';
    protected static ?string $slug = 'recaptcha-settings';
    protected string $view = 'filament.super-admin.pages.recaptcha-settings';

    public ?array $data = [];

    public static function getNavigationLabel(): string
    {
        return __('filament/sa_recaptcha.navigation_label');
    }

    public function getTitle(): string|Htmlable
    {
        return __('filament/sa_recaptcha.title');
    }

    public function getMaxContentWidth(): \Filament\Support\Enums\Width|string|null
    {
        return \Filament\Support\Enums\Width::Full;
    }

    public function mount(): void
    {
        $settings = app(RecaptchaSettings::class);

        $this->form->fill([
            'enabled'           => $settings->enabled,
            'site_key'          => $settings->site_key,
            'secret_key'        => $settings->secret_key,
            'guard_register'    => $settings->guard_register,
            'guard_admin_login' => $settings->guard_admin_login,
            'guard_sa_login'    => $settings->guard_sa_login,
            'min_score'         => (string) $settings->min_score,
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->statePath('data')
            ->components([
                Section::make(__('sections.google_recaptcha_v3'))
                    ->description(new \Illuminate\Support\HtmlString(
                        e(__('filament/sa_recaptcha.console_intro_prefix')) . ' '
                        // The .rs-console-link class lives in
                        // public/css/views/filament/super-admin/pages/recaptcha-settings.css
                        // (already loaded by this page's Blade view).
                        . '<a href="https://www.google.com/recaptcha/admin" target="_blank" rel="noopener" class="rs-console-link">'
                        . e(__('filament/sa_recaptcha.console_link_label')) . '</a>. '
                        . e(__('filament/sa_recaptcha.console_intro_suffix', [
                            'host' => parse_url(config('app.url'), PHP_URL_HOST) ?: 'yourdomain.com',
                        ]))
                    ))
                    ->schema([
                        Toggle::make('enabled')
                            ->label(__('filament/sa_recaptcha.master_switch_label'))
                            ->helperText(__('filament/sa_recaptcha.master_switch_helper'))
                            ->live()
                            ->columnSpanFull(),

                        TextInput::make('site_key')
                            ->label(__('filament/sa_recaptcha.site_key_label'))
                            ->helperText(__('filament/sa_recaptcha.site_key_helper'))
                            ->maxLength(80),

                        TextInput::make('secret_key')
                            ->label(__('filament/sa_recaptcha.secret_key_label'))
                            ->helperText(__('filament/sa_recaptcha.secret_key_helper'))
                            ->password()
                            ->revealable()
                            ->maxLength(80),

                        TextInput::make('min_score')
                            ->label(__('filament/sa_recaptcha.min_score_label'))
                            ->helperText(__('filament/sa_recaptcha.min_score_helper'))
                            ->numeric()
                            ->step(0.1)
                            ->minValue(0)
                            ->maxValue(1)
                            ->default(0.5)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make(__('sections.protected_surfaces'))
                    ->description(__('filament/sa_recaptcha.protected_surfaces_description'))
                    ->schema([
                        Toggle::make('guard_register')
                            ->label(__('filament/sa_recaptcha.guard_register_label'))
                            ->helperText(__('filament/sa_recaptcha.guard_register_helper'))
                            ->default(true),

                        Toggle::make('guard_admin_login')
                            ->label(__('filament/sa_recaptcha.guard_admin_login_label'))
                            ->helperText(__('filament/sa_recaptcha.guard_admin_login_helper'))
                            ->default(true),

                        Toggle::make('guard_sa_login')
                            ->label(__('filament/sa_recaptcha.guard_sa_login_label'))
                            ->helperText(__('filament/sa_recaptcha.guard_sa_login_helper'))
                            ->default(true),
                    ])
                    ->columns(3),
            ]);
    }

    public function save(): void
    {
        // Demo lockdown: blocks demo visitors signed in as SA from
        // swapping reCAPTCHA keys (which would break the login + signup
        // forms across the live demo).  No-op in production.
        \App\Support\DemoMode::guard();

        $data = $this->form->getState();

        $settings = app(RecaptchaSettings::class);
        $settings->enabled           = (bool) ($data['enabled'] ?? false);
        $settings->site_key          = filled($data['site_key'] ?? null) ? (string) $data['site_key'] : null;
        $settings->secret_key        = filled($data['secret_key'] ?? null) ? (string) $data['secret_key'] : null;
        $settings->guard_register    = (bool) ($data['guard_register'] ?? true);
        $settings->guard_admin_login = (bool) ($data['guard_admin_login'] ?? true);
        $settings->guard_sa_login    = (bool) ($data['guard_sa_login'] ?? true);
        $settings->min_score         = (float) ($data['min_score'] ?? 0.5);
        $settings->save();

        Notification::make()
            ->title(__('filament/sa_recaptcha.settings_saved'))
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('filament/sa_recaptcha.action_save'))
                ->submit('save'),
        ];
    }
}
