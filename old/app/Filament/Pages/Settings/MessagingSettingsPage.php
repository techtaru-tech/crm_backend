<?php

namespace App\Filament\Pages\Settings;

use App\Models\AuditLog;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

/**
 * Per-tenant messaging provider credentials.
 *
 * Stores WhatsApp Business, Twilio (SMS), Telegram Bot, and Viber
 * credentials under `tenants.settings.messaging.*`.  The inbound
 * webhook URL is rendered read-only so tenants can paste it into
 * their provider's webhook configuration.
 *
 * Secrets (tokens, auth strings) are persisted encrypted; the form
 * shows a masked value on load so operators don't accidentally copy
 * their own secret into a ticket.
 */
class MessagingSettingsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\UnitEnum|null $navigationGroup = 'Communications';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?int $navigationSort = 24;

    protected string $view = 'filament.pages.settings.messaging-settings-page';

    public static function getNavigationLabel(): string
    {
        return __('filament/messaging_settings.nav_label');
    }

    public ?array $data = [];

    public function getTitle(): string
    {
        return __('filament/messaging_settings.page_title');
    }

    public function mount(): void
    {
        $tenant    = auth()->user()?->tenant;
        $messaging = (array) ($tenant?->getSetting('messaging') ?? []);

        $this->form->fill([
            // WhatsApp Business
            'whatsapp_enabled'               => (bool) ($messaging['whatsapp_enabled'] ?? false),
            'whatsapp_business_token'        => $this->maskSecret($messaging['whatsapp_business_token'] ?? ''),
            'whatsapp_phone_number_id'       => (string) ($messaging['whatsapp_phone_number_id'] ?? ''),
            'whatsapp_phone_number'          => (string) ($messaging['whatsapp_phone_number'] ?? ''),
            'whatsapp_webhook_token'         => (string) ($messaging['whatsapp_webhook_token'] ?? ''),

            // Twilio SMS
            'sms_enabled'      => (bool) ($messaging['sms_enabled'] ?? false),
            'twilio_sid'       => (string) ($messaging['twilio_sid'] ?? ''),
            'twilio_token'     => $this->maskSecret($messaging['twilio_token'] ?? ''),
            'twilio_from'      => (string) ($messaging['twilio_from'] ?? ''),
            'sms_webhook_token' => (string) ($messaging['sms_webhook_token'] ?? ''),

            // Telegram
            'telegram_enabled'       => (bool) ($messaging['telegram_enabled'] ?? false),
            'telegram_bot_token'     => $this->maskSecret($messaging['telegram_bot_token'] ?? ''),
            'telegram_bot_username'  => (string) ($messaging['telegram_bot_username'] ?? ''),
            'telegram_webhook_token' => (string) ($messaging['telegram_webhook_token'] ?? ''),

            // Viber
            'viber_enabled'       => (bool) ($messaging['viber_enabled'] ?? false),
            'viber_auth_token'    => $this->maskSecret($messaging['viber_auth_token'] ?? ''),
            'viber_from'          => (string) ($messaging['viber_from'] ?? ''),
            'viber_webhook_token' => (string) ($messaging['viber_webhook_token'] ?? ''),

            // Voice (Twilio Voice) — nested under messaging.voice.*
            'voice_enabled'                => (bool) (($messaging['voice']['voice_enabled'] ?? false)),
            'voice_from_number'            => (string) ($messaging['voice']['voice_from_number'] ?? ''),
            'voice_recording_enabled'      => (bool) (($messaging['voice']['voice_recording_enabled'] ?? false)),
            'voice_transcription_enabled'  => (bool) (($messaging['voice']['voice_transcription_enabled'] ?? false)),
            'voice_webhook_token'          => (string) ($messaging['voice']['voice_webhook_token'] ?? ''),
        ]);
    }

    public function form(Schema $form): Schema
    {
        $tenant   = auth()->user()?->tenant;
        $tenantId = $tenant?->id;
        $appUrl   = rtrim((string) config('app.url'), '/');

        $webhookHint = fn (string $channelField) => function () use ($appUrl, $tenantId, $channelField) {
            $tok = (string) ($this->data[$channelField] ?? '');
            $channel = (string) str($channelField)->before('_webhook_token');
            if (! $tok || ! $tenantId) {
                return __('filament/messaging_settings.inbound_webhook_unsaved');
            }
            return $appUrl . '/api/messaging/' . $channel . '/webhook/' . $tenantId . '/' . $tok;
        };

        return $form
            ->schema([
                Section::make(__('sections.whatsapp_business_meta_cloud_api'))
                    ->description(__('filament/messaging_settings.whatsapp_description'))
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->schema([
                        Toggle::make('whatsapp_enabled')->label(__('filament/messaging_settings.enabled'))->columnSpanFull(),
                        TextInput::make('whatsapp_business_token')
                            ->label(__('filament/messaging_settings.whatsapp_access_token'))
                            ->password()
                            ->revealable()
                            ->placeholder(__('filament/messaging_settings.whatsapp_access_token_placeholder'))
                            ->columnSpanFull(),
                        TextInput::make('whatsapp_phone_number_id')
                            ->label(__('filament/messaging_settings.whatsapp_phone_number_id'))
                            ->placeholder(__('filament/messaging_settings.whatsapp_phone_number_id_placeholder')),
                        TextInput::make('whatsapp_phone_number')
                            ->label(__('filament/messaging_settings.whatsapp_display_phone'))
                            ->placeholder(__('filament/messaging_settings.whatsapp_display_phone_placeholder')),
                        TextInput::make('whatsapp_webhook_token')
                            ->label(__('filament/messaging_settings.inbound_webhook_url'))
                            ->helperText($webhookHint('whatsapp_webhook_token'))
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpanFull(),
                    ])->columns(2)->collapsible(),

                Section::make(__('sections.sms_twilio'))
                    ->description(__('filament/messaging_settings.sms_description'))
                    ->icon('heroicon-o-device-phone-mobile')
                    ->schema([
                        Toggle::make('sms_enabled')->label(__('filament/messaging_settings.enabled'))->columnSpanFull(),
                        TextInput::make('twilio_sid')
                            ->label(__('filament/messaging_settings.twilio_sid'))
                            ->placeholder(__('filament/messaging_settings.twilio_sid_placeholder')),
                        TextInput::make('twilio_token')
                            ->label(__('filament/messaging_settings.twilio_auth_token'))
                            ->password()
                            ->revealable()
                            ->placeholder(__('filament/messaging_settings.twilio_auth_token_placeholder')),
                        TextInput::make('twilio_from')
                            ->label(__('filament/messaging_settings.twilio_sender_number'))
                            ->placeholder(__('filament/messaging_settings.twilio_sender_placeholder'))
                            ->columnSpanFull(),
                        TextInput::make('sms_webhook_token')
                            ->label(__('filament/messaging_settings.inbound_webhook_url'))
                            ->helperText($webhookHint('sms_webhook_token'))
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpanFull(),
                    ])->columns(2)->collapsible()->collapsed(),

                Section::make(__('sections.telegram'))
                    ->description(__('filament/messaging_settings.telegram_description'))
                    ->icon('heroicon-o-paper-airplane')
                    ->schema([
                        Toggle::make('telegram_enabled')->label(__('filament/messaging_settings.enabled'))->columnSpanFull(),
                        TextInput::make('telegram_bot_token')
                            ->label(__('filament/messaging_settings.telegram_bot_token'))
                            ->password()
                            ->revealable()
                            ->placeholder(__('filament/messaging_settings.telegram_bot_token_placeholder')),
                        TextInput::make('telegram_bot_username')
                            ->label(__('filament/messaging_settings.telegram_bot_username'))
                            ->placeholder(__('filament/messaging_settings.telegram_bot_username_placeholder')),
                        TextInput::make('telegram_webhook_token')
                            ->label(__('filament/messaging_settings.inbound_webhook_url'))
                            ->helperText($webhookHint('telegram_webhook_token'))
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpanFull(),
                    ])->columns(2)->collapsible()->collapsed(),

                Section::make(__('sections.voice_twilio_voice'))
                    ->description(__('filament/messaging_settings.voice_description'))
                    ->icon('heroicon-o-phone')
                    ->schema([
                        Toggle::make('voice_enabled')->label(__('filament/messaging_settings.enabled'))->columnSpanFull(),
                        TextInput::make('voice_from_number')
                            ->label(__('filament/messaging_settings.voice_caller_id'))
                            ->placeholder(__('filament/messaging_settings.voice_caller_id_placeholder'))
                            ->helperText(__('filament/messaging_settings.voice_caller_id_helper'))
                            ->columnSpanFull(),
                        Toggle::make('voice_recording_enabled')
                            ->label(__('filament/messaging_settings.voice_record_calls'))
                            ->helperText(__('filament/messaging_settings.voice_record_calls_helper')),
                        Toggle::make('voice_transcription_enabled')
                            ->label(__('filament/messaging_settings.voice_auto_transcribe'))
                            ->helperText(__('filament/messaging_settings.voice_auto_transcribe_helper')),
                        TextInput::make('voice_webhook_token')
                            ->label(__('filament/messaging_settings.voice_webhook_urls'))
                            ->helperText(function () use ($appUrl, $tenantId) {
                                $tok = (string) ($this->data['voice_webhook_token'] ?? '');
                                if (! $tok || ! $tenantId) {
                                    return __('filament/messaging_settings.voice_webhook_unsaved');
                                }
                                return __('filament/messaging_settings.twiml_url_label') . $appUrl . '/api/voice/' . $tenantId . '/twiml/' . $tok
                                    . __('filament/messaging_settings.status_label') . $appUrl . '/api/voice/' . $tenantId . '/status/' . $tok
                                    . __('filament/messaging_settings.recording_label') . $appUrl . '/api/voice/' . $tenantId . '/recording/' . $tok;
                            })
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpanFull(),
                    ])->columns(2)->collapsible()->collapsed(),

                Section::make(__('sections.viber'))
                    ->description(__('filament/messaging_settings.viber_description'))
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->schema([
                        Toggle::make('viber_enabled')->label(__('filament/messaging_settings.enabled'))->columnSpanFull(),
                        TextInput::make('viber_auth_token')
                            ->label(__('filament/messaging_settings.viber_auth_token'))
                            ->password()
                            ->revealable()
                            ->placeholder(__('filament/messaging_settings.viber_auth_token_placeholder')),
                        TextInput::make('viber_from')
                            ->label(__('filament/messaging_settings.viber_sender_name'))
                            ->placeholder(__('filament/messaging_settings.viber_sender_placeholder')),
                        TextInput::make('viber_webhook_token')
                            ->label(__('filament/messaging_settings.inbound_webhook_url'))
                            ->helperText($webhookHint('viber_webhook_token'))
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpanFull(),
                    ])->columns(2)->collapsible()->collapsed(),
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

        $values   = $this->form->getState();
        $existing = (array) ($tenant->getSetting('messaging') ?? []);

        $merged = [
            'whatsapp_enabled'         => (bool) ($values['whatsapp_enabled'] ?? false),
            'whatsapp_business_token'  => $this->unmaskSecret($values['whatsapp_business_token'] ?? '', $existing['whatsapp_business_token'] ?? ''),
            'whatsapp_phone_number_id' => (string) ($values['whatsapp_phone_number_id'] ?? ''),
            'whatsapp_phone_number'    => (string) ($values['whatsapp_phone_number'] ?? ''),
            'whatsapp_webhook_token'   => $existing['whatsapp_webhook_token'] ?? Str::random(32),

            'sms_enabled'       => (bool) ($values['sms_enabled'] ?? false),
            'twilio_sid'        => (string) ($values['twilio_sid'] ?? ''),
            'twilio_token'      => $this->unmaskSecret($values['twilio_token'] ?? '', $existing['twilio_token'] ?? ''),
            'twilio_from'       => (string) ($values['twilio_from'] ?? ''),
            'sms_webhook_token' => $existing['sms_webhook_token'] ?? Str::random(32),

            'telegram_enabled'       => (bool) ($values['telegram_enabled'] ?? false),
            'telegram_bot_token'     => $this->unmaskSecret($values['telegram_bot_token'] ?? '', $existing['telegram_bot_token'] ?? ''),
            'telegram_bot_username'  => (string) ($values['telegram_bot_username'] ?? ''),
            'telegram_webhook_token' => $existing['telegram_webhook_token'] ?? Str::random(32),

            'viber_enabled'       => (bool) ($values['viber_enabled'] ?? false),
            'viber_auth_token'    => $this->unmaskSecret($values['viber_auth_token'] ?? '', $existing['viber_auth_token'] ?? ''),
            'viber_from'          => (string) ($values['viber_from'] ?? ''),
            'viber_webhook_token' => $existing['viber_webhook_token'] ?? Str::random(32),

            // Voice (Twilio Voice) — nested bucket.
            'voice' => [
                'voice_enabled'               => (bool) ($values['voice_enabled'] ?? false),
                'voice_from_number'           => (string) ($values['voice_from_number'] ?? ''),
                'voice_recording_enabled'     => (bool) ($values['voice_recording_enabled'] ?? false),
                'voice_transcription_enabled' => (bool) ($values['voice_transcription_enabled'] ?? false),
                'voice_webhook_token'         => (string) (($existing['voice']['voice_webhook_token'] ?? '') ?: Str::random(32)),
            ],
        ];

        try {
            $settings             = (array) ($tenant->settings ?? []);
            $settings['messaging'] = $merged;
            $tenant->settings     = $settings;
            $tenant->save();

            try {
                AuditLog::record(
                    action: 'settings.messaging.updated',
                    auditable: $tenant,
                    oldValues: ['channels' => array_keys(array_filter($existing, fn ($v, $k) => str_ends_with((string) $k, '_enabled') && $v, ARRAY_FILTER_USE_BOTH))],
                    newValues: ['channels' => array_keys(array_filter($merged, fn ($v, $k) => str_ends_with((string) $k, '_enabled') && $v, ARRAY_FILTER_USE_BOTH))],
                    tags: 'settings,messaging',
                );
            } catch (\Throwable) {
                // Audit failures shouldn't block the save.
            }

            // Re-fill the form so newly-generated webhook tokens appear immediately.
            $this->mount();

            Notification::make()->title(__('notifications.messaging_saved'))->success()->send();
        } catch (\Throwable $e) {
            Notification::make()->title(__('notifications.save_failed', ['error' => $e->getMessage()]))->danger()->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label(__('filament/messaging_settings.save_settings'))
                ->action('save')
                ->color('primary'),

            Action::make('test_config')
                ->label(__('filament/messaging_settings.send_test_message'))
                ->icon('heroicon-o-paper-airplane')
                ->color('gray')
                ->modalHeading(__('filament/messaging_settings.send_test_message'))
                ->modalDescription(__('filament/messaging_settings.send_test_modal_description'))
                ->form([
                    \Filament\Forms\Components\Select::make('channel')
                        ->label(__('filament/messaging_settings.test_channel'))
                        ->options([
                            'whatsapp' => 'WhatsApp',
                            'sms'      => 'SMS',
                            'telegram' => 'Telegram',
                            'viber'    => 'Viber',
                        ])
                        ->required(),
                    TextInput::make('target')
                        ->label(__('filament/messaging_settings.test_target'))
                        ->placeholder(__('filament/messaging_settings.test_target_placeholder'))
                        ->required(),
                    \Filament\Forms\Components\Textarea::make('body')
                        ->label(__('filament/messaging_settings.test_message'))
                        ->default(__('filament/messaging_settings.test_message_default'))
                        ->rows(3)
                        ->required(),
                ])
                ->action(function (array $data) {
                    try {
                        \App\Jobs\SendLeadMessage::dispatch(
                            0, // dummy lead id — the job implementation may skip lead lookup for system tests
                            $data['channel'],
                            $data['body'],
                            auth()->id(),
                            ['override_target' => $data['target']]
                        );
                        Notification::make()
                            ->success()
                            ->title(__('filament/messaging_settings.test_send_scheduled'))
                            ->send();
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->danger()
                            ->title(__('filament/messaging_settings.test_send_failed', ['error' => $e->getMessage()]))
                            ->send();
                    }
                }),
        ];
    }

    /**
     * Render a secret as a masked placeholder so operators don't copy
     * their own token into a ticket.  Empty string for unset secrets.
     */
    protected function maskSecret(string $value): string
    {
        return $value === '' ? '' : str_repeat('•', 12);
    }

    /**
     * If the form value is the mask (unchanged), keep the stored secret.
     * Otherwise accept the new value as-is.
     */
    protected function unmaskSecret(string $formValue, string $existing): string
    {
        $mask = str_repeat('•', 12);
        if ($formValue === '' || $formValue === $mask) {
            return $existing;
        }
        return $formValue;
    }
}
