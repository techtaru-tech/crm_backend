<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| SuperAdmin TenantResource — Filament admin strings
|------------------------------------------------------------
| Accessed via __('filament/sa_tenants.<key>').
| Buyers translate or adapt by editing this file or copying it
| to lang/<locale>/filament/sa_tenants.php.
*/

return [

    // ----- Resource labels -----
    'workspace'                       => 'مساحة عمل',
    'workspaces'                      => 'مساحات العمل',

    // ----- Workspace details form -----
    'reserved_slug_error'             => '«:value» محجوز. اختر مُعرّفًا مختلفًا لمساحة العمل.',
    'workspace_url_helper'            => 'رابط مساحة العمل: :url — يُستخدم أيضًا لصفحات الهبوط العامة. يُملأ تلقائيًا من الاسم.',
    'max_seats_helper'                => 'حد أقصى صارم لعدد أعضاء الفريق.',
    'subscription_status_helper'      => 'يُضبط الإيقاف عبر زر «إيقاف» — وليس هنا. عادةً ما تُضبط حالتا «انتهت الفترة التجريبية» و«منتهٍ» تلقائيًا بواسطة cron دورة الحياة.',
    'trial_ends_at_label'             => 'تنتهي الفترة التجريبية في',
    'trial_ends_at_helper'            => 'متى تنتهي الفترة التجريبية المجانية. يُملأ تلقائيًا من trial_days للخطة عند اختيار خطة.',
    'subscription_ends_at_label'      => 'ينتهي الاشتراك في',
    'subscription_ends_at_helper'     => 'تاريخ الفوترة التالي للاشتراكات النشطة، أو تاريخ انتهاء الوصول للاشتراكات الملغاة/المنتهية.',

    // ----- Subscription status options -----
    'status_trial'                    => 'فترة تجريبية',
    'status_trial_expired'            => 'انتهت الفترة التجريبية',
    'status_active_paid'              => 'نشط (مدفوع)',
    'status_cancelled'                => 'ملغى',
    'status_expired'                  => 'منتهٍ',
    'status_active'                   => 'نشط',
    'status_suspended'                => 'موقوف',
    'status_unknown'                  => 'غير معروف',

    // ----- Tenant admin section -----
    'tenant_admin_description'        => 'المستخدم المشرف الذي سيمتلك ويدير مساحة العمل هذه.',
    'admin_name'                      => 'اسم المشرف',
    'admin_email'                     => 'بريد المشرف',
    'admin_password_mode'             => 'إعداد كلمة المرور',
    'admin_password_mode_email_link'  => 'إرسال رابط إعداد للمشرف بالبريد (موصى به)',
    'admin_password_mode_generate'    => 'توليد تلقائي + عرض هنا',
    'admin_password_mode_manual'      => 'تعيين كلمة المرور الآن',
    'admin_password'                  => 'كلمة المرور',
    'admin_password_helper'           => '10 أحرف على الأقل. أبلغ المشرف بها بشكل آمن.',

    // ----- Table columns -----
    'owner'                           => 'المالك',
    'owner_email'                     => 'بريد المالك',
    'status_column'                   => 'الحالة',
    'seats'                           => 'المقاعد',
    'trial_ends'                      => 'انتهاء الفترة التجريبية',
    'sub_ends'                        => 'انتهاء الاشتراك',

    // ----- Filters -----
    'filter_suspension'               => 'الإيقاف',

    // ----- Suspend action -----
    'suspend'                         => 'إيقاف',
    'suspend_modal_heading'           => 'إيقاف مساحة العمل',
    'suspend_modal_description'       => 'هل تريد إيقاف «:name»؟ سيفقد جميع الأعضاء الوصول عند طلبهم التالي وسيرون صفحة «الاشتراك مطلوب». هذا قابل للتراجع.',
    'suspend_reason_label'            => 'السبب (اختياري، داخلي — لسجل المراجعة)',
    'suspend_demo_guard'              => 'العرض: لا يمكن إيقاف المستأجرين (سيرسل بريد إشعار حقيقي).',
    'suspend_notification_title'      => 'تم إيقاف مساحة العمل «:name»',
    'suspend_notification_body_base'  => 'سيُعاد توجيه الأعضاء عند طلبهم التالي.',
    'suspend_notification_body_owner_notified'  => ' تم إبلاغ المالك على :email.',
    'suspend_notification_body_owner_failed'    => ' فشل إرسال بريد المالك (راجع السجلات).',
    'suspend_notification_body_no_owner'        => ' المستأجر ليس له مالك — لم يُرسَل أي إشعار.',

    // ----- Reactivate action -----
    'reactivate'                      => 'إعادة تفعيل',
    'reactivate_modal_heading'        => 'إعادة تفعيل مساحة العمل',
    'reactivate_modal_description'    => 'هل تريد إعادة تفعيل «:name»؟ سيستعيد الأعضاء الوصول فورًا عند طلبهم التالي.',
    'reactivate_demo_guard'           => 'العرض: لا يمكن إعادة تفعيل المستأجرين.',
    'reactivate_notification_title'   => 'تم إعادة تفعيل مساحة العمل «:name»',

    // ----- Impersonate action -----
    'impersonate'                     => 'انتحال الهوية',
    'impersonate_modal_heading'       => 'انتحال هوية مشرف المستأجر',
    'impersonate_modal_description'   => 'سيتم تسجيل دخولك بصفة «:owner_name» (:owner_email) في مساحة العمل «:tenant_name». ستُنفَّذ جميع الإجراءات كهذا المستخدم. يمكنك العودة إلى المشرف الأعلى في أي وقت عبر الشريط.',
    'impersonate_demo_guard'          => 'العرض: انتحال الهوية معطَّل.',

    // ----- CreateTenant page notifications -----
    'workspace_created'               => 'تم إنشاء مساحة العمل',
    'workspace_created_password_body' => "كلمة المرور لـ :email: \n\n  :password\n\nانسخها الآن — لن تُعرَض مرة أخرى.",
    'workspace_created_manual_body'   => 'كلمة المرور التي اخترتها للمشرف فعّالة. شاركها بأمان مع :email.',
    'workspace_created_email_body'    => 'تم إرسال رابط إعداد إلى :email',
    'workspace_created_email_failed_title' => 'تم إنشاء مساحة العمل لكن فشل البريد',
    'workspace_created_email_failed_body'  => 'تم إنشاء مساحة العمل، لكن لم نتمكن من إرسال بريد الإعداد إلى :email. يمكن للمستخدم استخدام رابط «نسيت كلمة المرور» في صفحة تسجيل الدخول للوصول.',
    'workspace_created_existing_user' => 'تم تعيين المستخدم الحالي :email كمشرف. لم يُرسَل بريد إعداد.',

    // ----- Field labels (form + table) -----
    'name'                            => 'الاسم',
    'slug'                            => 'المُعرّف الكنوي',
    'max_seats'                       => 'الحد الأقصى للمقاعد',
    'plan'                            => 'الخطة',
    'subscription_status'             => 'حالة الاشتراك',
    'created_at'                      => 'تاريخ الإنشاء',

    // ----- Model labels -----
    'model_label'                     => 'مستأجر',
    'plural_model_label'              => 'المستأجرون',

];
