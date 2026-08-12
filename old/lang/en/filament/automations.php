<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — AutomationResource translation strings
|--------------------------------------------------------------------------
|
| Labels, helper texts, placeholders, descriptions and bulk-action
| copy for the Automations resource at /admin/automations.
| Consumed via __('filament/automations.<key>').
|
| Keys are snake_case; grouped by comment headers (Basic, Trigger,
| Steps, Conditions, Actions, Delay, Table, Filters, Bulk actions).
|
*/

return [

    // ----- Navigation -----
    'nav_label'           => 'Automations',
    'nav_badge_tooltip'   => 'Automation failures in the last 24 hours',

    // ----- Model labels (breadcrumbs / page titles) -----
    'model_label'         => 'Automation',
    'plural_model_label'  => 'Automations',

    // ----- Repeater itemLabel templates (emoji icons preserved literal) -----
    'item_label_condition' => '🔍 Condition: ',
    'item_label_action'    => '⚡ Action: ',
    'item_label_delay_wait' => '⏱ Wait ',
    'item_label_delay_default_unit' => 'minutes',

    // ─── Basic details ───────────────────────────────────────────────
    'automation_name'                   => 'Automation Name',
    'description'                       => 'Description',
    'active'                            => 'Active',
    'respect_business_hours'            => 'Respect business hours',
    'respect_business_hours_help'       => 'Skip triggers outside the tenant business-hours window.',

    // ─── Trigger section ────────────────────────────────────────────
    'trigger_description'               => 'Define when this automation fires.',
    'trigger_event'                     => 'Trigger Event',

    // ─── Steps section ─────────────────────────────────────────────
    'steps_description'                 => 'Add Conditions (filters), Actions, and Delays. Steps execute top-to-bottom. Drag to reorder.',
    'add_step'                          => 'Add Step',
    'step_type'                         => 'Step Type',

    // ─── Trigger config — per trigger type ─────────────────────────
    'filter_by_sources'                 => 'Filter by Source(s)',
    'filter_by_sources_help'            => 'Leave blank to trigger on all sources.',
    'from_stage'                        => 'From Stage',
    'to_stage'                          => 'To Stage',
    'tag_name'                          => 'Tag Name',
    'score_threshold'                   => 'Score Threshold',
    'crosses'                           => 'Crosses',
    'no_activity_for'                   => 'No Activity for',
    'unit'                              => 'Unit',
    'form_blank_for_any'                => 'Form (leave blank for any)',

    // ─── Condition config ──────────────────────────────────────────
    'condition_type'                    => 'Condition Type',
    'source'                            => 'Source',
    'field_name'                        => 'Field Name',
    'field_name_placeholder'            => 'e.g. email, status',
    'value'                             => 'Value',
    'score'                             => 'Score',
    'user'                              => 'User',
    'time_range'                        => 'Time Range (HH:MM-HH:MM)',
    'time_range_placeholder'            => '09:00-17:00',
    'days'                              => 'Day(s)',
    'days_placeholder'                  => 'Monday,Tuesday',

    // ─── Action config ─────────────────────────────────────────────
    'action'                            => 'Action',
    'email_template'                    => 'Email Template',
    'notify_users'                      => 'Notify Users',
    'notify_assigned_agent'             => 'Also notify assigned agent',
    'custom_message'                    => 'Custom Message',
    'assignment_mode'                   => 'Assignment Mode',
    'users_round_robin_pool'            => 'Users (Round Robin Pool)',
    'target_stage'                      => 'Target Stage',
    'new_status'                        => 'New Status',
    'webhook_url'                       => 'Webhook URL',
    'hmac_secret'                       => 'HMAC Secret (optional)',
    'task_title'                        => 'Task Title',
    'task_title_help'                   => 'Supports {first_name}, {last_name}, {full_name}, {email}, {lead_score}.',
    'due_in_hours'                      => 'Due in Hours (from now)',
    'due_in_hours_help'                 => 'Task will be due this many hours after the automation fires.',
    'priority'                          => 'Priority',
    'assign_task_to'                    => 'Assign Task To',
    'assign_task_to_help'               => "Leave blank to fall back to the lead's assigned user.",
    'slack_webhook_url'                 => 'Slack Webhook URL',
    'slack_message'                     => 'Message (supports {{lead.first_name}} etc.)',
    'sms_message'                       => 'SMS Message',
    'sms_message_help'                  => 'Supports {{first_name}}, {{last_name}}, {{full_name}}, {{email}}, {{company}}',

    // ─── Delay config ──────────────────────────────────────────────
    'wait'                              => 'Wait',

    // ─── Table columns ─────────────────────────────────────────────
    'name'                              => 'Name',
    'trigger'                           => 'Trigger',
    'steps'                             => 'Steps',
    'runs'                              => 'Runs',
    'created'                           => 'Created',

    // ─── Row actions ──────────────────────────────────────────────
    'history'                           => 'History',

    // ─── Bulk actions ─────────────────────────────────────────────
    'enable_selected'                   => 'Enable selected',
    'disable_selected'                  => 'Disable selected',

    // ─── Header actions ───────────────────────────────────────────
    'browse_templates'                  => 'Browse Templates',
    'run_history'                       => 'Run History',
    'back_to_automation'                => 'Back to Automation',

    // ─── Run history page ─────────────────────────────────────────
    'run_history_heading'               => 'Run History — :name',
    'runs_count'                        => ':count runs',
    'no_runs_yet'                       => 'No runs yet. This automation has not been triggered.',
    'col_lead'                          => 'Lead',
    'col_started'                       => 'Started',
    'col_duration'                      => 'Duration',
    'col_status'                        => 'Status',
    'col_steps'                         => 'Steps',
    'btn_hide'                          => 'Hide',
    'btn_show'                          => 'Show',
    'btn_show_steps'                    => ':count step(s)',
    'no_log'                            => 'No log',

    // ─── Select options ────────────────────────────────────────────
    'option_above_threshold'            => 'Above threshold',
    'option_below_threshold'            => 'Below threshold',
    'option_minutes'                    => 'Minutes',
    'option_hours'                      => 'Hours',
    'option_days'                       => 'Days',
    'option_specific_user'              => 'Specific User',
    'option_round_robin'                => 'Round Robin',
    'option_lead_status_new'            => 'New',
    'option_lead_status_contacted'      => 'Contacted',
    'option_lead_status_qualified'      => 'Qualified',
    'option_lead_status_lost'           => 'Lost',
    'option_lead_status_won'            => 'Won',
];
