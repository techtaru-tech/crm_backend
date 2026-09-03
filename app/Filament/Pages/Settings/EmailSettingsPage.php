<?php

namespace App\Filament\Pages\Settings;

use App\Filament\Concerns\PageRequiresPermission;

use App\Services\SettingsService;
use App\Services\TenantSmtpManager;
use Filament\Actions\Action;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Mail;

class EmailSettingsPage extends Page implements HasForms
{
    use PageRequiresPermission;

    protected static string $requiredPermission = 'settings.manage';

    use InteractsWithForms;

    protected static string|\UnitEnum|null $navigationGroup = 'Communications';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-envelope';
    protected static ?int $navigationSort = 21;
    protected string $view = 'filament.pages.settings.email-settings-page';

    public static function getNavigationLabel(): string
    {
        return __('filament/email_settings.nav_label');
    }

    public ?array $data = [];

    public function getTitle(): string
    {
        return __('filament/email_settings.page_title');
    }

    public function mount(): void
    {
        $tenant   = auth()->user()?->tenant;
        $settings = $tenant?->settings ?? [];
        $smtp     = $settings['smtp'] ?? [];

        $this->form->fill([
            'smtp_host'       => $smtp['host'] ?? env('MAIL_HOST', ''),
            'smtp_port'       => $smtp['port'] ?? env('MAIL_PORT', 587),
            'smtp_username'   => $smtp['username'] ?? env('MAIL_USERNAME', ''),
            'smtp_password'   => '',
            'smtp_encryption' => $smtp['encryption'] ?? env('MAIL_ENCRYPTION', 'tls'),
            'from_name'       => $smtp['from_name'] ?? ($tenant?->getBranding('email_sender_name') ?? config('mail.from.name', '')),
            'from_address'    => $smtp['from_address'] ?? ($tenant?->getBranding('email_from') ?? config('mail.from.address', '')),
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make(__('sections.smtp_configuration'))
                    ->schema([
                        TextInput::make('smtp_host')->label(__('filament/email_settings.host'))->required(),
                        TextInput::make('smtp_port')->label(__('filament/email_settings.port'))->numeric()->required(),
                        Select::make('smtp_encryption')
                            ->label(__('filament/email_settings.encryption'))
                            ->options([
                                'tls' => 'TLS',
                                'ssl' => 'SSL',
                                ''    => __('filament/email_settings.option_encryption_none'),
                            ]),
                        TextInput::make('smtp_username')->label(__('filament/email_settings.username')),
                        TextInput::make('smtp_password')
                            ->label(__('filament/email_settings.password'))
                            ->password()
                            ->disabled(\App\Support\DemoMode::locked())
                            ->dehydrated(\App\Support\DemoMode::unlocked())
                            // Deliberately NOT revealable — prevents demo-user
                            // and over-the-shoulder credential disclosure.
                            ->autocomplete('new-password')
                            ->helperText(__('filament/email_settings.password_helper')),
                    ])->columns(2),

                Section::make(__('sections.from_address'))
                    ->schema([
                        TextInput::make('from_name')->label(__('filament/email_settings.from_name'))->required(),
                        TextInput::make('from_address')->label(__('filament/email_settings.from_email'))->email()->required(),
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

        $values = $this->form->getState();

        $current = $tenant->getSetting('smtp', []);
        $smtp = array_merge($current, [
            'host'         => $values['smtp_host'],
            'port'         => $values['smtp_port'],
            'encryption'   => $values['smtp_encryption'],
            'username'     => $values['smtp_username'],
            'from_name'    => $values['from_name'],
            'from_address' => $values['from_address'],
        ]);

        if (! empty($values['smtp_password'])) {
            $smtp['password'] = encrypt($values['smtp_password']);
        }

        app(SettingsService::class)->forTenant($tenant)->set('smtp', $smtp);

        Notification::make()->title(__('notifications.email_saved'))->success()->send();
    }

    public function sendTestEmail(array $data = []): void
    {
        \App\Support\DemoMode::abortIfOn(__('filament/demo_mode.test_email_blocked'));

        $user   = auth()->user();
        $tenant = $user?->tenant;

        // Recipient comes from the action form (defaults to the current
        // user's email when unset). Validate as an email so we fail fast
        // on typos instead of handing garbage to the mailer.
        $recipient = filter_var((string) ($data['recipient'] ?? ''), FILTER_VALIDATE_EMAIL)
            ?: $user?->email;

        if (! $recipient) {
            Notification::make()->danger()->title(__('filament/email_settings.no_recipient'))->send();
            return;
        }

        if ($tenant) {
            app(TenantSmtpManager::class)->applyForTenant($tenant);
        }

        try {
            $appName      = $tenant?->getAppName() ?? config('leadhub.branding.app_name', 'LeadHub');
            $primaryColor = $tenant?->getPrimaryColor() ?? config('leadhub.branding.primary_color', '#4f46e5');
            $logoUrl      = $tenant?->getBranding('logo_url') ?? config('leadhub.branding.logo_url');
            $footerText   = $tenant?->getBranding('footer_text') ?? config('leadhub.branding.footer_text', '');

            Mail::send(
                'emails.test',
                compact('appName', 'primaryColor', 'logoUrl', 'footerText', 'user'),
                function ($msg) use ($recipient, $appName) {
                    $msg->to($recipient)
                        ->subject(__('mail.test_subject', ['app' => $appName]));
                }
            );

            Notification::make()
                ->success()
                ->title(__('filament/email_settings.test_sent_to', ['recipient' => $recipient]))
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->danger()
                ->title(__('filament/email_settings.send_failed', ['error' => \Illuminate\Support\Str::limit($e->getMessage(), 200)]))
                ->persistent()
                ->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('sendTest')
                ->label(__('filament/email_settings.send_test_email'))
                ->icon('heroicon-o-paper-airplane')
                ->color('gray')
                ->form([
                    \Filament\Forms\Components\TextInput::make('recipient')
                        ->label(__('filament/email_settings.recipient'))
                        ->email()
                        ->default(fn () => auth()->user()?->email)
                        ->required()
                        ->helperText(__('filament/email_settings.recipient_helper')),
                ])
                ->modalSubmitActionLabel(__('filament/email_settings.modal_submit'))
                ->action(fn (array $data) => $this->sendTestEmail($data)),

            Action::make('save')
                ->label(__('filament/email_settings.save_settings'))
                ->action('save')
                ->color('primary'),
        ];
    }
}
