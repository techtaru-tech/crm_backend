<?php

namespace App\Enums;

enum LeadSource: string
{
    case Meta        = 'meta';
    case Instagram   = 'instagram';
    case TikTok      = 'tiktok';
    case LinkedIn    = 'linkedin';
    case WhatsApp    = 'whatsapp';
    case Viber       = 'viber';
    case Telegram    = 'telegram';
    case Twitter     = 'twitter';
    case Snapchat    = 'snapchat';
    case Pinterest   = 'pinterest';
    case GoogleAds   = 'google_ads';
    case YouTube     = 'youtube';
    case Microsoft   = 'microsoft_ads';
    case Email       = 'email';
    case Typeform    = 'typeform';
    case JotForm     = 'jotform';
    case Calendly    = 'calendly';
    case WebForm     = 'web_form';
    case Manual      = 'manual';

    public function label(): string
    {
        // All labels resolve through __('enums/lead_source.<value>')
        // so translators can localise the surrounding words
        // ("Leads", "Lead Forms", "Lead Ads", etc.) without
        // touching this file.  The English default in
        // lang/en/enums/lead_source.php matches the original
        // hard-coded values byte-for-byte so existing call sites
        // (Filament Select options, dashboards, API responses)
        // see no behavioural change.
        return (string) __('enums/lead_source.' . $this->value);
    }

    public function icon(): string
    {
        return match($this) {
            self::Meta        => 'heroicon-o-megaphone',
            self::Instagram   => 'heroicon-o-camera',
            self::TikTok      => 'heroicon-o-musical-note',
            self::LinkedIn    => 'heroicon-o-briefcase',
            self::WhatsApp    => 'heroicon-o-chat-bubble-left-right',
            self::Viber       => 'heroicon-o-phone',
            self::Telegram    => 'heroicon-o-paper-airplane',
            self::Twitter     => 'heroicon-o-at-symbol',
            self::Snapchat    => 'heroicon-o-face-smile',
            self::Pinterest   => 'heroicon-o-bookmark',
            self::GoogleAds   => 'heroicon-o-magnifying-glass',
            self::YouTube     => 'heroicon-o-play-circle',
            self::Microsoft   => 'heroicon-o-computer-desktop',
            self::Email       => 'heroicon-o-envelope',
            self::Typeform    => 'heroicon-o-clipboard-document-list',
            self::JotForm     => 'heroicon-o-document-text',
            self::Calendly    => 'heroicon-o-calendar',
            self::WebForm     => 'heroicon-o-globe-alt',
            self::Manual      => 'heroicon-o-pencil',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Meta        => '#1877F2',
            self::Instagram   => '#E1306C',
            self::TikTok      => '#000000',
            self::LinkedIn    => '#0A66C2',
            self::WhatsApp    => '#25D366',
            self::Viber       => '#7360F2',
            self::Telegram    => '#0088CC',
            self::Twitter     => '#1DA1F2',
            self::Snapchat    => '#FFFC00',
            self::Pinterest   => '#E60023',
            self::GoogleAds   => '#4285F4',
            self::YouTube     => '#FF0000',
            self::Microsoft   => '#00A4EF',
            self::Email       => '#6B7280',
            self::Typeform    => '#262627',
            self::JotForm     => '#FF6100',
            self::Calendly    => '#006BFF',
            self::WebForm     => '#4F46E5',
            self::Manual      => '#9CA3AF',
        };
    }

    public function isWebhookBased(): bool
    {
        return ! in_array($this, [self::Email, self::Manual]);
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(
            fn ($case) => [$case->value => $case->label()]
        )->all();
    }
}
