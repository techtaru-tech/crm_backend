<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutomationStep extends Model
{
    protected $fillable = [
        'automation_id', 'type', 'config', 'sort_order',
    ];

    protected $casts = [
        'config' => 'array',
    ];

    public const STEP_TYPES = [
        'condition' => 'Condition',
        'action'    => 'Action',
        'delay'     => 'Delay',
    ];

    public const ACTION_TYPES = [
        'send_email'          => 'Send Email to Lead',
        'notify_users'        => 'Send Internal Notification',
        'assign_lead'         => 'Assign Lead to User',
        'add_tag'             => 'Add Tag',
        'remove_tag'          => 'Remove Tag',
        'move_pipeline'       => 'Move to Pipeline Stage',
        'change_status'       => 'Change Lead Status',
        'send_webhook'        => 'Send Webhook',
        'create_task'         => 'Create Task / Reminder',
        'send_slack'          => 'Send Slack Notification',
        'send_sms'            => 'Send SMS to Lead',
        'enroll_ai_sdr'       => 'Enroll in AI SDR Agent',
    ];

    public const CONDITION_TYPES = [
        'source_is'           => 'Lead Source Is',
        'source_is_not'       => 'Lead Source Is Not',
        'has_tag'             => 'Lead Has Tag',
        'not_has_tag'         => 'Lead Does Not Have Tag',
        'field_equals'        => 'Lead Field Equals',
        'field_contains'      => 'Lead Field Contains',
        'field_is_empty'      => 'Lead Field Is Empty',
        'score_gt'            => 'Lead Score Greater Than',
        'score_lt'            => 'Lead Score Less Than',
        'assigned_to'         => 'Assigned to User',
        'unassigned'          => 'Unassigned',
        'time_of_day'         => 'Time of Day',
        'day_of_week'         => 'Day of Week',
    ];

    /**
     * Translated step-type labels for Filament Select / display.
     * Keys mirror self::STEP_TYPES.
     */
    public static function stepTypeLabels(): array
    {
        return [
            'condition' => __('models/automation_step.step_type_condition'),
            'action'    => __('models/automation_step.step_type_action'),
            'delay'     => __('models/automation_step.step_type_delay'),
        ];
    }

    /**
     * Translated action-type labels for Filament Select / display.
     * Keys mirror self::ACTION_TYPES.
     */
    public static function actionTypeLabels(): array
    {
        return [
            'send_email'    => __('models/automation_step.action_type_send_email'),
            'notify_users'  => __('models/automation_step.action_type_notify_users'),
            'assign_lead'   => __('models/automation_step.action_type_assign_lead'),
            'add_tag'       => __('models/automation_step.action_type_add_tag'),
            'remove_tag'    => __('models/automation_step.action_type_remove_tag'),
            'move_pipeline' => __('models/automation_step.action_type_move_pipeline'),
            'change_status' => __('models/automation_step.action_type_change_status'),
            'send_webhook'  => __('models/automation_step.action_type_send_webhook'),
            'create_task'   => __('models/automation_step.action_type_create_task'),
            'send_slack'    => __('models/automation_step.action_type_send_slack'),
            'send_sms'      => __('models/automation_step.action_type_send_sms'),
            'enroll_ai_sdr' => __('models/automation_step.action_type_enroll_ai_sdr'),
        ];
    }

    /**
     * Translated condition-type labels for Filament Select / display.
     * Keys mirror self::CONDITION_TYPES.
     */
    public static function conditionTypeLabels(): array
    {
        return [
            'source_is'      => __('models/automation_step.condition_type_source_is'),
            'source_is_not'  => __('models/automation_step.condition_type_source_is_not'),
            'has_tag'        => __('models/automation_step.condition_type_has_tag'),
            'not_has_tag'    => __('models/automation_step.condition_type_not_has_tag'),
            'field_equals'   => __('models/automation_step.condition_type_field_equals'),
            'field_contains' => __('models/automation_step.condition_type_field_contains'),
            'field_is_empty' => __('models/automation_step.condition_type_field_is_empty'),
            'score_gt'       => __('models/automation_step.condition_type_score_gt'),
            'score_lt'       => __('models/automation_step.condition_type_score_lt'),
            'assigned_to'    => __('models/automation_step.condition_type_assigned_to'),
            'unassigned'     => __('models/automation_step.condition_type_unassigned'),
            'time_of_day'    => __('models/automation_step.condition_type_time_of_day'),
            'day_of_week'    => __('models/automation_step.condition_type_day_of_week'),
        ];
    }

    public function automation(): BelongsTo
    {
        return $this->belongsTo(Automation::class);
    }
}
