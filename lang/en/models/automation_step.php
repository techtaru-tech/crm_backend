<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| AutomationStep model — translatable enum labels
|------------------------------------------------------------
| Accessed via __('models/automation_step.<key>').
*/

return [
    // ─── STEP TYPES ────────────────────────────────
    'step_type_condition' => 'Condition',
    'step_type_action'    => 'Action',
    'step_type_delay'     => 'Delay',

    // ─── ACTION TYPES ──────────────────────────────
    'action_type_send_email'    => 'Send Email to Lead',
    'action_type_notify_users'  => 'Send Internal Notification',
    'action_type_assign_lead'   => 'Assign Lead to User',
    'action_type_add_tag'       => 'Add Tag',
    'action_type_remove_tag'    => 'Remove Tag',
    'action_type_move_pipeline' => 'Move to Pipeline Stage',
    'action_type_change_status' => 'Change Lead Status',
    'action_type_send_webhook'  => 'Send Webhook',
    'action_type_create_task'   => 'Create Task / Reminder',
    'action_type_send_slack'    => 'Send Slack Notification',
    'action_type_send_sms'      => 'Send SMS to Lead',
    'action_type_enroll_ai_sdr' => 'Enroll in AI SDR Agent',

    // ─── CONDITION TYPES ───────────────────────────
    'condition_type_source_is'      => 'Lead Source Is',
    'condition_type_source_is_not'  => 'Lead Source Is Not',
    'condition_type_has_tag'        => 'Lead Has Tag',
    'condition_type_not_has_tag'    => 'Lead Does Not Have Tag',
    'condition_type_field_equals'   => 'Lead Field Equals',
    'condition_type_field_contains' => 'Lead Field Contains',
    'condition_type_field_is_empty' => 'Lead Field Is Empty',
    'condition_type_score_gt'       => 'Lead Score Greater Than',
    'condition_type_score_lt'       => 'Lead Score Less Than',
    'condition_type_assigned_to'    => 'Assigned to User',
    'condition_type_unassigned'     => 'Unassigned',
    'condition_type_time_of_day'    => 'Time of Day',
    'condition_type_day_of_week'    => 'Day of Week',
];
