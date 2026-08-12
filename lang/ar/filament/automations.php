<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — سلاسل ترجمة AutomationResource
|--------------------------------------------------------------------------
|
| التسميات والنصوص المساعدة والعناصر النائبة والأوصاف ونصوص الإجراءات
| الجماعية لمورد الأتمتة على /admin/automations.
| يُستهلك عبر __('filament/automations.<key>').
|
*/

return [

    // ----- التنقل -----
    'nav_label'           => 'الأتمتة',
    'nav_badge_tooltip'   => 'إخفاقات الأتمتة خلال آخر 24 ساعة',

    // ----- تسميات النموذج (مسار التنقل / عناوين الصفحات) -----
    'model_label'         => 'أتمتة',
    'plural_model_label'  => 'الأتمتة',

    // ----- قوالب itemLabel للمكرر (رموز الإيموجي محفوظة حرفياً) -----
    'item_label_condition' => '🔍 شرط: ',
    'item_label_action'    => '⚡ إجراء: ',
    'item_label_delay_wait' => '⏱ الانتظار ',
    'item_label_delay_default_unit' => 'دقائق',

    // ─── التفاصيل الأساسية ─────────────────────────────────────────
    'automation_name'                   => 'اسم الأتمتة',
    'description'                       => 'الوصف',
    'active'                            => 'نشطة',
    'respect_business_hours'            => 'احترام ساعات العمل',
    'respect_business_hours_help'       => 'تخطّي المحفزات خارج نافذة ساعات عمل المستأجر.',

    // ─── قسم المحفز ────────────────────────────────────────────────
    'trigger_description'               => 'حدّد متى تنطلق هذه الأتمتة.',
    'trigger_event'                     => 'حدث المحفز',

    // ─── قسم الخطوات ──────────────────────────────────────────────
    'steps_description'                 => 'أضف شروطاً (مرشحات) وإجراءات وتأخيرات. تُنفَّذ الخطوات من الأعلى إلى الأسفل. اسحب لإعادة الترتيب.',
    'add_step'                          => 'إضافة خطوة',
    'step_type'                         => 'نوع الخطوة',

    // ─── إعدادات المحفز — لكل نوع ──────────────────────────────────
    'filter_by_sources'                 => 'التصفية حسب المصدر/المصادر',
    'filter_by_sources_help'            => 'اترك الحقل فارغاً ليشمل جميع المصادر.',
    'from_stage'                        => 'من المرحلة',
    'to_stage'                          => 'إلى المرحلة',
    'tag_name'                          => 'اسم الوسم',
    'score_threshold'                   => 'حد النقاط',
    'crosses'                           => 'يتجاوز',
    'no_activity_for'                   => 'بدون نشاط لمدة',
    'unit'                              => 'الوحدة',
    'form_blank_for_any'                => 'نموذج (اتركه فارغاً لأي نموذج)',

    // ─── إعدادات الشرط ────────────────────────────────────────────
    'condition_type'                    => 'نوع الشرط',
    'source'                            => 'المصدر',
    'field_name'                        => 'اسم الحقل',
    'field_name_placeholder'            => 'مثال: email، status',
    'value'                             => 'القيمة',
    'score'                             => 'النقاط',
    'user'                              => 'المستخدم',
    'time_range'                        => 'النطاق الزمني (HH:MM-HH:MM)',
    'time_range_placeholder'            => '09:00-17:00',
    'days'                              => 'الأيام',
    'days_placeholder'                  => 'الإثنين،الثلاثاء',

    // ─── إعدادات الإجراء ──────────────────────────────────────────
    'action'                            => 'الإجراء',
    'email_template'                    => 'قالب البريد الإلكتروني',
    'notify_users'                      => 'إخطار المستخدمين',
    'notify_assigned_agent'             => 'إخطار الوكيل المعيَّن أيضاً',
    'custom_message'                    => 'رسالة مخصصة',
    'assignment_mode'                   => 'وضع التعيين',
    'users_round_robin_pool'            => 'المستخدمون (مجمع التناوب الدوري)',
    'target_stage'                      => 'المرحلة المستهدفة',
    'new_status'                        => 'الحالة الجديدة',
    'webhook_url'                       => 'عنوان Webhook URL',
    'hmac_secret'                       => 'سر HMAC (اختياري)',
    'task_title'                        => 'عنوان المهمة',
    'task_title_help'                   => 'يدعم {first_name}، {last_name}، {full_name}، {email}، {lead_score}.',
    'due_in_hours'                      => 'الاستحقاق بعد ساعات (من الآن)',
    'due_in_hours_help'                 => 'ستستحق المهمة بعد هذا العدد من الساعات من انطلاق الأتمتة.',
    'priority'                          => 'الأولوية',
    'assign_task_to'                    => 'تعيين المهمة إلى',
    'assign_task_to_help'               => 'اترك الحقل فارغاً للرجوع إلى المستخدم المعيَّن للعميل المحتمل.',
    'slack_webhook_url'                 => 'عنوان Slack Webhook URL',
    'slack_message'                     => 'الرسالة (تدعم {{lead.first_name}} وغيرها)',
    'sms_message'                       => 'رسالة SMS',
    'sms_message_help'                  => 'تدعم {{first_name}}، {{last_name}}، {{full_name}}، {{email}}، {{company}}',

    // ─── إعدادات التأخير ──────────────────────────────────────────
    'wait'                              => 'الانتظار',

    // ─── أعمدة الجدول ─────────────────────────────────────────────
    'name'                              => 'الاسم',
    'trigger'                           => 'المحفز',
    'steps'                             => 'الخطوات',
    'runs'                              => 'التشغيلات',
    'created'                           => 'تاريخ الإنشاء',

    // ─── إجراءات الصف ─────────────────────────────────────────────
    'history'                           => 'السجل',

    // ─── الإجراءات الجماعية ───────────────────────────────────────
    'enable_selected'                   => 'تفعيل المحدد',
    'disable_selected'                  => 'تعطيل المحدد',

    // ─── إجراءات الترويسة ────────────────────────────────────────
    'browse_templates'                  => 'تصفح القوالب',
    'run_history'                       => 'سجل التشغيل',
    'back_to_automation'                => 'العودة إلى الأتمتة',

    // ─── صفحة سجل التشغيل ────────────────────────────────────────
    'run_history_heading'               => 'سجل التشغيل — :name',
    'runs_count'                        => ':count تشغيل',
    'no_runs_yet'                       => 'لا توجد تشغيلات بعد. لم يتم تشغيل هذه الأتمتة.',
    'col_lead'                          => 'العميل المحتمل',
    'col_started'                       => 'البداية',
    'col_duration'                      => 'المدة',
    'col_status'                        => 'الحالة',
    'col_steps'                         => 'الخطوات',
    'btn_hide'                          => 'إخفاء',
    'btn_show'                          => 'عرض',
    'btn_show_steps'                    => ':count خطوة',
    'no_log'                            => 'لا يوجد سجل',

    // ─── خيارات القائمة المنسدلة ──────────────────────────────────
    'option_above_threshold'            => 'فوق الحد',
    'option_below_threshold'            => 'دون الحد',
    'option_minutes'                    => 'دقائق',
    'option_hours'                      => 'ساعات',
    'option_days'                       => 'أيام',
    'option_specific_user'              => 'مستخدم محدد',
    'option_round_robin'                => 'تناوب دوري',
    'option_lead_status_new'            => 'جديد',
    'option_lead_status_contacted'      => 'تم الاتصال به',
    'option_lead_status_qualified'      => 'مؤهَّل',
    'option_lead_status_lost'           => 'مفقود',
    'option_lead_status_won'            => 'مكتسب',
];
