<?php

declare(strict_types=1);

return [
    'step_type_condition' => 'شرط',
    'step_type_action'    => 'إجراء',
    'step_type_delay'     => 'تأخير',

    'action_type_send_email'    => 'إرسال بريد إلى العميل المحتمل',
    'action_type_notify_users'  => 'إرسال إشعار داخلي',
    'action_type_assign_lead'   => 'تعيين عميل محتمل لمستخدم',
    'action_type_add_tag'       => 'إضافة وسم',
    'action_type_remove_tag'    => 'إزالة وسم',
    'action_type_move_pipeline' => 'النقل إلى مرحلة خط أنابيب',
    'action_type_change_status' => 'تغيير حالة العميل المحتمل',
    'action_type_send_webhook'  => 'إرسال Webhook',
    'action_type_create_task'   => 'إنشاء مهمة / تذكير',
    'action_type_send_slack'    => 'إرسال إشعار Slack',
    'action_type_send_sms'      => 'إرسال SMS إلى العميل المحتمل',

    'condition_type_source_is'      => 'مصدر العميل المحتمل هو',
    'condition_type_source_is_not'  => 'مصدر العميل المحتمل ليس',
    'condition_type_has_tag'        => 'لدى العميل المحتمل وسم',
    'condition_type_not_has_tag'    => 'ليس لدى العميل المحتمل وسم',
    'condition_type_field_equals'   => 'حقل العميل المحتمل يساوي',
    'condition_type_field_contains' => 'حقل العميل المحتمل يحتوي على',
    'condition_type_field_is_empty' => 'حقل العميل المحتمل فارغ',
    'condition_type_score_gt'       => 'نقاط العميل المحتمل أكبر من',
    'condition_type_score_lt'       => 'نقاط العميل المحتمل أقل من',
    'condition_type_assigned_to'    => 'مُعيَّن لمستخدم',
    'condition_type_unassigned'     => 'غير مُعيَّن',
    'condition_type_time_of_day'    => 'وقت اليوم',
    'condition_type_day_of_week'    => 'يوم الأسبوع',
];
