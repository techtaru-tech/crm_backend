<?php

namespace App\Filament\SuperAdmin\Pages;

use App\Services\EnvEditor;
use App\Settings\GeneralSettings;
use App\Support\Currency;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use UnitEnum;

/**
 * Script-wide defaults for the CodeCanyon deployment: reporting currency,
 * timezone, language, date format. Stored in the Spatie Laravel Settings
 * payload (`general` group) so the same values back the Billing dashboard,
 * pricing page defaults, and future i18n rollout.
 *
 * Distinct from the per-tenant General Settings page under Settings →
 * General — those live in the tenant-scoped admin panel, while this page
 * sits in the Super Admin panel and configures the script owner's global
 * defaults.
 */
class ScriptSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-adjustments-horizontal';
    protected static ?int $navigationSort = 80;
    protected static string|UnitEnum|null $navigationGroup = 'System';
    protected string $view = 'filament.super-admin.pages.script-settings';

    public ?array $data = [];

    public static function getNavigationLabel(): string
    {
        return __('filament/sa_script_settings.navigation_label');
    }

    public function getTitle(): string|Htmlable
    {
        return __('filament/sa_script_settings.title');
    }

    public function mount(): void
    {
        $settings = app(GeneralSettings::class);
        $env      = app(EnvEditor::class);

        // Mail config is read from .env directly so we reflect what
        // Laravel will actually USE (not a shadow DB value that might
        // disagree with the live config).
        $this->form->fill([
            'timezone'             => $settings->timezone,
            'date_format'          => $settings->date_format,
            'language'             => $settings->language,
            'currency'             => $settings->currency,
            'auto_nightly_backup'  => $settings->auto_nightly_backup,
            'landing_variant'      => $env->get('LEADHUB_LANDING_VARIANT') ?? 'classic',

            'mail_mailer'          => $env->get('MAIL_MAILER')       ?? 'log',
            'mail_host'            => $env->get('MAIL_HOST')         ?? '',
            'mail_port'            => $env->get('MAIL_PORT')         ?? '587',
            'mail_username'        => $env->get('MAIL_USERNAME')     ?? '',
            'mail_password'        => $env->get('MAIL_PASSWORD')
                ? str_repeat('•', 12)
                : '',
            'mail_encryption'      => $env->get('MAIL_ENCRYPTION')   ?? 'tls',
            'mail_from_address'    => $env->get('MAIL_FROM_ADDRESS') ?? '',
            'mail_from_name'       => $env->get('MAIL_FROM_NAME')    ?? 'LeadHub',
        ]);
    }

    public function form(Schema $form): Schema
    {
        $timezones = collect(\DateTimeZone::listIdentifiers())
            ->mapWithKeys(fn ($tz) => [$tz => $tz])
            ->all();

        return $form
            ->schema([
                Section::make(__('sections.reporting_localisation'))
                    ->description(__('filament/sa_script_settings.reporting_section_description'))
                    ->schema([
                        Select::make('currency')
                            ->label(__('filament/sa_script_settings.reporting_currency_label'))
                            ->options(Currency::options())
                            ->searchable()
                            ->required()
                            ->helperText(__('filament/sa_script_settings.reporting_currency_helper')),

                        Select::make('timezone')
                            ->label(__('filament/sa_script_settings.default_timezone_label'))
                            ->options($timezones)
                            ->searchable()
                            ->required(),

                        Select::make('date_format')
                            ->label(__('filament/sa_script_settings.date_format_label'))
                            ->options($this->dateFormatOptions())
                            ->required(),

                        Select::make('language')
                            ->label(__('filament/sa_script_settings.default_language_label'))
                            ->options(collect(config('locales.supported', []))
                                ->mapWithKeys(fn ($meta, $code) => [
                                    $code => ($meta['flag'] ?? '') . ' ' . ($meta['name'] ?? strtoupper($code))
                                        . ($meta['rtl'] ?? false ? ' (RTL)' : ''),
                                ])->all() ?: ['en' => __('filament/sa_script_settings.locale_fallback_english_native')])
                            ->required()
                            ->helperText(__('filament/sa_script_settings.default_language_helper')),
                    ])
                    ->columns(2),

                Section::make(__('sections.backups'))
                    ->description(__('filament/sa_script_settings.backups_section_description'))
                    ->schema([
                        Toggle::make('auto_nightly_backup')
                            ->label(__('filament/sa_script_settings.auto_nightly_backup_label'))
                            ->helperText(__('filament/sa_script_settings.auto_nightly_backup_helper')),
                    ]),

                Section::make(__('sections.public_marketing_landing'))
                    ->description(__('filament/sa_script_settings.landing_section_description'))
                    ->icon('heroicon-o-presentation-chart-line')
                    ->schema([
                        Select::make('landing_variant')
                            ->label(__('filament/sa_script_settings.landing_template_label'))
                            ->options([
                                'light'     => __('filament/sa_script_settings.landing_variant_light'),
                                'warm'      => __('filament/sa_script_settings.landing_variant_warm'),
                                'modern'    => __('filament/sa_script_settings.landing_variant_modern'),
                                'editorial' => __('filament/sa_script_settings.landing_variant_editorial'),
                                'classic'   => __('filament/sa_script_settings.landing_variant_classic'),
                            ])
                            ->default('light')
                            ->required()
                            ->helperText(__('filament/sa_script_settings.landing_template_helper')),
                    ]),

                Section::make(__('sections.outbound_email_smtp'))
                    ->description(__('filament/sa_script_settings.smtp_section_description'))
                    ->icon('heroicon-o-envelope')
                    ->schema([
                        Select::make('mail_mailer')
                            ->label(__('filament/sa_script_settings.mailer_label'))
                            ->options([
                                'smtp'     => __('filament/sa_script_settings.mailer_option_smtp'),
                                'sendmail' => __('filament/sa_script_settings.mailer_option_sendmail'),
                                'log'      => __('filament/sa_script_settings.mailer_option_log'),
                                'array'    => __('filament/sa_script_settings.mailer_option_array'),
                            ])
                            ->required()
                            ->live()
                            ->helperText(__('filament/sa_script_settings.mailer_helper')),

                        TextInput::make('mail_host')
                            ->label(__('filament/sa_script_settings.smtp_host_label'))
                            ->placeholder(__('filament/sa_script_settings.smtp_host_placeholder'))
                            ->visible(fn ($get) => $get('mail_mailer') === 'smtp')
                            ->required(fn ($get) => $get('mail_mailer') === 'smtp'),

                        TextInput::make('mail_port')
                            ->label(__('filament/sa_script_settings.smtp_port_label'))
                            ->numeric()
                            ->default(587)
                            ->placeholder(__('filament/sa_script_settings.smtp_port_placeholder'))
                            ->visible(fn ($get) => $get('mail_mailer') === 'smtp')
                            ->required(fn ($get) => $get('mail_mailer') === 'smtp'),

                        Select::make('mail_encryption')
                            ->label(__('filament/sa_script_settings.encryption_label'))
                            ->options([
                                'tls'  => __('filament/sa_script_settings.encryption_option_tls'),
                                'ssl'  => __('filament/sa_script_settings.encryption_option_ssl'),
                                ''     => __('filament/sa_script_settings.encryption_option_none'),
                            ])
                            ->default('tls')
                            ->visible(fn ($get) => $get('mail_mailer') === 'smtp'),

                        TextInput::make('mail_username')
                            ->label(__('filament/sa_script_settings.smtp_username_label'))
                            ->autocomplete('off')
                            ->visible(fn ($get) => $get('mail_mailer') === 'smtp'),

                        TextInput::make('mail_password')
                            ->label(__('filament/sa_script_settings.smtp_password_label'))
                            ->password()
                            // Deliberately NOT revealable — prevents
                            // demo-user and over-the-shoulder credential
                            // disclosure. If the operator needs to verify
                            // the stored value, editing .env directly is
                            // the correct path (they already have shell
                            // access if they installed the script).
                            ->autocomplete('new-password')
                            ->placeholder(__('filament/sa_script_settings.smtp_password_placeholder'))
                            ->visible(fn ($get) => $get('mail_mailer') === 'smtp'),

                        TextInput::make('mail_from_address')
                            ->label(__('filament/sa_script_settings.from_address_label'))
                            ->email()
                            ->placeholder(__('filament/sa_script_settings.from_address_placeholder'))
                            ->columnSpan(1)
                            ->required(),

                        TextInput::make('mail_from_name')
                            ->label(__('filament/sa_script_settings.from_name_label'))
                            ->placeholder(__('filament/sa_script_settings.from_name_placeholder'))
                            ->columnSpan(1)
                            ->required(),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        // Demo lockdown: this page writes to .env (mail credentials,
        // landing variant, ...) — exactly the surface that turned the
        // demo landing "black" when a stranger flipped the landing
        // variant dropdown.  Guard at the top so no .env mutation
        // happens in demo mode.  No-op in production.
        \App\Support\DemoMode::guard();

        $values = $this->form->getState();

        $settings              = app(GeneralSettings::class);
        $settings->currency    = strtoupper($values['currency'] ?? 'USD');
        $settings->timezone    = $values['timezone'] ?? 'UTC';
        $settings->date_format = $values['date_format'] ?? 'Y-m-d';
        $settings->language             = $values['language'] ?? 'en';
        $settings->auto_nightly_backup  = (bool) ($values['auto_nightly_backup'] ?? false);
        $settings->save();

        // Persist Mail section to .env — the masked password stays if
        // the operator didn't type a new one, so we don't accidentally
        // wipe credentials on a non-password-change save.
        $env  = app(EnvEditor::class);
        $mask = str_repeat('•', 12);

        $env->set('LEADHUB_LANDING_VARIANT', (string) ($values['landing_variant'] ?? 'classic'));
        $env->set('MAIL_MAILER',      (string) ($values['mail_mailer']    ?? 'log'));
        $env->set('MAIL_HOST',        (string) ($values['mail_host']      ?? ''));
        $env->set('MAIL_PORT',        (string) ($values['mail_port']      ?? '587'));
        $env->set('MAIL_USERNAME',    (string) ($values['mail_username']  ?? ''));
        $env->set('MAIL_ENCRYPTION',  (string) ($values['mail_encryption'] ?? 'tls'));
        $env->set('MAIL_FROM_ADDRESS',(string) ($values['mail_from_address'] ?? ''));
        $env->set('MAIL_FROM_NAME',   (string) ($values['mail_from_name'] ?? 'LeadHub'));

        $pw = (string) ($values['mail_password'] ?? '');
        if ($pw !== '' && $pw !== $mask) {
            $env->set('MAIL_PASSWORD', $pw);
        }

        // Config is cached at boot — force a reload so the change
        // takes effect on the next request without an ops-level
        // `php artisan config:cache` dance.
        try {
            Artisan::call('config:clear');
        } catch (\Throwable) {
            // If config:clear fails (read-only cache dir etc.) the
            // save still succeeds — the operator just needs to
            // clear caches manually.
        }

        Notification::make()
            ->title(__('filament/sa_script_settings.settings_saved_title'))
            ->body(__('filament/sa_script_settings.settings_saved_body'))
            ->success()
            ->send();
    }

    /**
     * Send a test email to the super admin using the CURRENT script-level
     * mail config. Handy to verify credentials before rolling them out
     * tenant-wide.
     *
     * Uses `config()->set()` at call time so unsaved edits in the form
     * are also testable — the operator can tweak the port, click Send
     * Test, and see the result without committing anything to .env.
     */
    public function sendTestEmail(array $data = []): void
    {
        $user = auth()->user();
        if (! $user) {
            Notification::make()->danger()->title(__('filament/sa_script_settings.no_user_title'))->send();
            return;
        }

        // Recipient from the action form — defaults to the super admin's
        // email, overridable so the operator can verify delivery to
        // a production address (e.g. the mailbox they plan to use
        // in production).
        $recipient = filter_var((string) ($data['recipient'] ?? ''), FILTER_VALIDATE_EMAIL)
            ?: $user->email;

        // Pull the in-form values (not yet saved) so the operator can
        // verify credentials BEFORE persisting them.
        $values = $this->form->getState();
        $mailer = (string) ($values['mail_mailer'] ?? 'log');

        if ($mailer === 'log' || $mailer === 'array') {
            Notification::make()
                ->warning()
                ->title(__('filament/sa_script_settings.mailer_dry_warning_title', ['mailer' => $mailer]))
                ->body(__('filament/sa_script_settings.mailer_dry_warning_body'))
                ->send();
            return;
        }

        // Apply unsaved form values for this single send. We don't
        // write to .env — this is a dry run.
        $current = config('mail');
        config([
            'mail.default'           => $mailer,
            'mail.mailers.smtp.host'       => $values['mail_host']       ?? '',
            'mail.mailers.smtp.port'       => (int) ($values['mail_port'] ?? 587),
            'mail.mailers.smtp.username'   => $values['mail_username']   ?? '',
            // Only swap the password when the operator typed a new one.
            // An unchanged masked field falls through to whatever config
            // already holds (from .env or the currently-cached config).
            'mail.mailers.smtp.password'   => $this->resolveLivePassword($values['mail_password'] ?? ''),
            'mail.mailers.smtp.encryption' => $values['mail_encryption'] ?: null,
            'mail.from.address'            => $values['mail_from_address'] ?? 'noreply@example.com',
            'mail.from.name'               => $values['mail_from_name']    ?? 'LeadHub',
        ]);

        try {
            $appName = config('leadhub.branding.app_name', 'LeadHub');
            $body    = __('filament/sa_script_settings.test_email_body_intro', ['app' => $appName])
                . "\n\n"
                . __('filament/sa_script_settings.test_email_body_confirmation')
                . "\n\n"
                . __('filament/sa_script_settings.test_email_body_sent_at', ['timestamp' => now()->toDateTimeString()]);

            Mail::raw(
                $body,
                function ($msg) use ($recipient, $appName) {
                    $msg->to($recipient)
                        ->subject(__('filament/sa_script_settings.test_email_subject', ['app' => $appName]));
                }
            );

            Notification::make()
                ->success()
                ->title(__('filament/sa_script_settings.test_email_sent_title', ['recipient' => $recipient]))
                ->body(__('filament/sa_script_settings.test_email_sent_body'))
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->danger()
                ->title(__('filament/sa_script_settings.test_send_failed_title', [
                    'error' => \Illuminate\Support\Str::limit($e->getMessage(), 200),
                ]))
                ->body(__('filament/sa_script_settings.test_send_failed_body'))
                ->persistent()
                ->send();
        } finally {
            // Restore the originally-loaded mail config — we only
            // mutated it for the test send.
            config(['mail' => $current]);
        }
    }

    /**
     * Build the date_format Select options with live, locale-aware
     * sample renderings.
     *
     * Carbon's translatedFormat() runs the PHP date format string
     * through the current app locale, so e.g. 'M j, Y' renders as
     * 'Apr 15, 2026' in English but 'Απρ 15, 2026' in Greek and
     * 'أبر 15, 2026' in Arabic — every super-admin sees real
     * sample output for THEIR locale rather than a hard-coded
     * English-only literal.
     *
     * @return array<string, string>
     */
    protected function dateFormatOptions(): array
    {
        $sample  = Carbon::create(2026, 4, 15);
        $formats = ['Y-m-d', 'd/m/Y', 'm/d/Y', 'd-m-Y', 'M j, Y'];

        $options = [];
        foreach ($formats as $format) {
            $options[$format] = $sample->translatedFormat($format);
        }

        return $options;
    }

    /**
     * If the operator left the password field as the masked value
     * (12 bullets) or empty, fall back to whatever mail.mailers.smtp.password
     * config already resolves to. Otherwise use the typed value.
     */
    protected function resolveLivePassword(string $formValue): ?string
    {
        $mask = str_repeat('•', 12);
        if ($formValue === '' || $formValue === $mask) {
            return config('mail.mailers.smtp.password');
        }
        return $formValue;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('sendTest')
                ->label(__('filament/sa_script_settings.action_send_test'))
                ->icon('heroicon-o-paper-airplane')
                ->color('gray')
                ->tooltip(__('filament/sa_script_settings.action_send_test_tooltip'))
                ->form([
                    TextInput::make('recipient')
                        ->label(__('filament/sa_script_settings.action_send_test_recipient_label'))
                        ->email()
                        ->default(fn () => auth()->user()?->email)
                        ->required()
                        ->helperText(__('filament/sa_script_settings.action_send_test_recipient_helper')),
                ])
                ->modalSubmitActionLabel(__('filament/sa_script_settings.action_send_test_modal_submit'))
                ->action(fn (array $data) => $this->sendTestEmail($data)),

            Action::make('save')
                ->label(__('filament/sa_script_settings.action_save_settings'))
                ->action('save')
                ->color('primary'),
        ];
    }
}
