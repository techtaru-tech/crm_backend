<?php

namespace App\Services\LeadSources;

class YouTubeConnector extends GoogleAdsConnector
{
    public function getConnectionFormSchema(): array
    {
        return [
            'developer_token' => self::field('youtube', 'developer_token', 'string', true,  'Developer Token'),
            'client_id'       => self::field('youtube', 'client_id',       'string', true,  'Client ID'),
            'client_secret'   => self::field('youtube', 'client_secret',   'string', true,  'Client Secret'),
            'refresh_token'   => self::field('youtube', 'refresh_token',   'string', true,  'Refresh Token'),
            'customer_id'     => self::field('youtube', 'customer_id',     'string', true,  'Customer ID'),
            'channel_id'      => self::field('youtube', 'channel_id',      'string', false, 'YouTube Channel ID'),
            'webhook_secret'  => self::field('youtube', 'webhook_secret',  'string', false, 'Webhook Secret'),
        ];
    }
}
