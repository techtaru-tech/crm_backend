<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationPreference extends Model
{
    protected $fillable = ['user_id', 'notification_type', 'channel', 'enabled', 'email_frequency'];

    protected $casts = ['enabled' => 'boolean'];

    public const TYPES = [
        'lead_received'           => 'New Lead Received',
        'lead_assigned'           => 'Lead Assigned to Me',
        'lead_stage_changed'      => 'Lead Moved to Stage',
        'automation_ran'          => 'Automation Triggered',
        'integration_sync_failed' => 'Integration Sync Failed',
        'export_ready'            => 'Export Ready',
        'team_mentioned'          => 'Team Mention in Note',
    ];

    public const CHANNELS = ['in_app', 'email', 'push'];

    /**
     * Translated notification-type labels for display.
     * Keys mirror self::TYPES.
     */
    public static function typeLabels(): array
    {
        return [
            'lead_received'           => __('models/notification_preference.type_lead_received'),
            'lead_assigned'           => __('models/notification_preference.type_lead_assigned'),
            'lead_stage_changed'      => __('models/notification_preference.type_lead_stage_changed'),
            'automation_ran'          => __('models/notification_preference.type_automation_ran'),
            'integration_sync_failed' => __('models/notification_preference.type_integration_sync_failed'),
            'export_ready'            => __('models/notification_preference.type_export_ready'),
            'team_mentioned'          => __('models/notification_preference.type_team_mentioned'),
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function isEnabled(int $userId, string $type, string $channel): bool
    {
        $pref = static::where('user_id', $userId)
            ->where('notification_type', $type)
            ->where('channel', $channel)
            ->first();

        if (! $pref) {
            return true;
        }

        if (! $pref->enabled) {
            return false;
        }

        if ($channel === 'email' && ($pref->email_frequency ?? '') === 'off') {
            return false;
        }

        return true;
    }

    public static function emailFrequency(int $userId, string $type): string
    {
        $pref = static::where('user_id', $userId)
            ->where('notification_type', $type)
            ->where('channel', 'email')
            ->first();

        return $pref ? $pref->email_frequency : 'immediate';
    }
}
