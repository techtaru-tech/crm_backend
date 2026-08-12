<?php

declare(strict_types=1);

return [
    'step_type_condition' => 'शर्त',
    'step_type_action'    => 'क्रिया',
    'step_type_delay'     => 'विलंब',

    'action_type_send_email'    => 'लीड को ईमेल भेजें',
    'action_type_notify_users'  => 'आंतरिक अधिसूचना भेजें',
    'action_type_assign_lead'   => 'उपयोगकर्ता को लीड असाइन करें',
    'action_type_add_tag'       => 'टैग जोड़ें',
    'action_type_remove_tag'    => 'टैग हटाएँ',
    'action_type_move_pipeline' => 'पाइपलाइन चरण में ले जाएँ',
    'action_type_change_status' => 'लीड स्थिति बदलें',
    'action_type_send_webhook'  => 'Webhook भेजें',
    'action_type_create_task'   => 'कार्य / रिमाइंडर बनाएँ',
    'action_type_send_slack'    => 'Slack अधिसूचना भेजें',
    'action_type_send_sms'      => 'लीड को SMS भेजें',

    'condition_type_source_is'      => 'लीड स्रोत है',
    'condition_type_source_is_not'  => 'लीड स्रोत नहीं है',
    'condition_type_has_tag'        => 'लीड में टैग है',
    'condition_type_not_has_tag'    => 'लीड में टैग नहीं है',
    'condition_type_field_equals'   => 'लीड फ़ील्ड बराबर है',
    'condition_type_field_contains' => 'लीड फ़ील्ड में शामिल है',
    'condition_type_field_is_empty' => 'लीड फ़ील्ड खाली है',
    'condition_type_score_gt'       => 'लीड स्कोर से अधिक',
    'condition_type_score_lt'       => 'लीड स्कोर से कम',
    'condition_type_assigned_to'    => 'उपयोगकर्ता को असाइन',
    'condition_type_unassigned'     => 'असाइन नहीं',
    'condition_type_time_of_day'    => 'दिन का समय',
    'condition_type_day_of_week'    => 'सप्ताह का दिन',
];
