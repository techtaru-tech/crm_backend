<?php

namespace App\Services;

use App\Contracts\SourceConnector;
use App\Services\LeadSources\CalendlyConnector;
use App\Services\LeadSources\EmailImapConnector;
use App\Services\LeadSources\GoogleAdsConnector;
use App\Services\LeadSources\InstagramConnector;
use App\Services\LeadSources\JotFormConnector;
use App\Services\LeadSources\LinkedInConnector;
use App\Services\LeadSources\ManualConnector;
use App\Services\LeadSources\MetaConnector;
use App\Services\LeadSources\MicrosoftAdsConnector;
use App\Services\LeadSources\PinterestConnector;
use App\Services\LeadSources\SnapchatConnector;
use App\Services\LeadSources\TelegramConnector;
use App\Services\LeadSources\TikTokConnector;
use App\Services\LeadSources\TwitterConnector;
use App\Services\LeadSources\TypeformConnector;
use App\Services\LeadSources\ViberConnector;
use App\Services\LeadSources\WebFormConnector;
use App\Services\LeadSources\WhatsAppConnector;
use App\Services\LeadSources\YouTubeConnector;
use RuntimeException;

class ConnectorRegistry
{
    protected array $connectors = [];

    public function __construct()
    {
        $this->register('meta',           MetaConnector::class);
        $this->register('instagram',      InstagramConnector::class);
        $this->register('tiktok',         TikTokConnector::class);
        $this->register('linkedin',       LinkedInConnector::class);
        $this->register('whatsapp',       WhatsAppConnector::class);
        $this->register('viber',          ViberConnector::class);
        $this->register('telegram',       TelegramConnector::class);
        $this->register('twitter',        TwitterConnector::class);
        $this->register('snapchat',       SnapchatConnector::class);
        $this->register('pinterest',      PinterestConnector::class);
        $this->register('google_ads',     GoogleAdsConnector::class);
        $this->register('youtube',        YouTubeConnector::class);
        $this->register('microsoft_ads',  MicrosoftAdsConnector::class);
        $this->register('email',          EmailImapConnector::class);
        $this->register('typeform',       TypeformConnector::class);
        $this->register('jotform',        JotFormConnector::class);
        $this->register('calendly',       CalendlyConnector::class);
        $this->register('web_form',       WebFormConnector::class);
        // Generic no-credentials inbound webhook for hand-pushed leads.
        // Without this, resolve('manual') threw and the Test action / webhook
        // ingestion 500'd for any "manual" connection.
        $this->register('manual',         ManualConnector::class);
    }

    public function register(string $source, string $connectorClass): void
    {
        $this->connectors[$source] = $connectorClass;
    }

    public function resolve(string $source): SourceConnector
    {
        if (! isset($this->connectors[$source])) {
            // Translated because LeadSourceConnectionResource calls
            // resolve() inside a Filament action lambda that is NOT
            // wrapped in the SafeAction concern.  If a typo'd source
            // ever sneaks in via custom migration, the buyer otherwise
            // sees the raw English literal in the Filament error toast.
            throw new RuntimeException((string) __('exceptions.connector_not_registered', ['source' => $source]));
        }

        return app($this->connectors[$source]);
    }

    public function all(): array
    {
        return $this->connectors;
    }

    public function has(string $source): bool
    {
        return isset($this->connectors[$source]);
    }
}
