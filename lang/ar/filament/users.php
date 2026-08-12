<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — UserResource translation strings
|--------------------------------------------------------------------------
|
| Labels, action copy, modal text and notifications for the Team Members
| (Users) resource at /admin/users.
| Consumed via __('filament/users.<key>').
|
*/

return [

    // ─── Navigation ───────────────────────────────────────────────────
    'nav_label'                         => 'أعضاء الفريق',

    // ─── Model labels (breadcrumbs / page titles) ─────────────────────
    'model_label'                       => 'عضو فريق',
    'plural_model_label'                => 'أعضاء الفريق',

    // ─── Form fields ───────────────────────────────────────────────────
    'name'                              => 'الاسم',
    'email'                             => 'البريد الإلكتروني',
    'password'                          => 'كلمة المرور',
    'avatar'                            => 'الصورة الرمزية',
    'created_at'                        => 'تاريخ الإنشاء',

    // ─── Form ──────────────────────────────────────────────────────────
    'password_helper'                   => 'اتركها فارغة للإبقاء على كلمة المرور الحالية.',
    'role'                              => 'الدور',
    'two_factor_enabled'                => 'المصادقة الثنائية مفعّلة',

    // ─── Table columns ─────────────────────────────────────────────────
    'two_factor_short'                  => '2FA',
    'status'                            => 'الحالة',
    'status_suspended'                  => 'موقوف',
    'status_active'                     => 'نشط',

    // ─── Meeting link action ───────────────────────────────────────────
    'action_meeting_link'               => 'رابط الاجتماع',
    'booking_links_suffix'              => ' روابط الحجز الخاصة بـ',
    'modal_close'                       => 'إغلاق',

    // ─── Password reset action ─────────────────────────────────────────
    'action_send_password_reset'        => 'إرسال إعادة تعيين كلمة المرور',
    'reset_modal_heading_prefix'        => 'إرسال رابط إعادة التعيين إلى ',
    'reset_modal_heading_suffix'        => '؟',
    'reset_modal_description'           => 'سيستلم المستخدم بريدًا يحتوي على رابط لاختيار كلمة مرور جديدة. ينتهي الرابط في 60 دقيقة.',
    'reset_sent_title'                  => 'تم إرسال رابط إعادة التعيين',
    'reset_sent_body_prefix'            => 'تم إرسال البريد إلى ',
    'reset_sent_body_suffix'            => ' — صالح لمدة 60 دقيقة.',
    'reset_failed_title'                => 'تعذر إرسال رابط إعادة التعيين',

    // ─── Suspend action ────────────────────────────────────────────────
    'action_suspend'                    => 'إيقاف',
    'suspend_modal_heading_prefix'      => 'إيقاف ',
    'suspend_modal_heading_suffix'      => '؟',
    'suspend_modal_description'         => 'سيفقد الوصول فورًا. أعد التفعيل في أي وقت بإجراء «رفع الإيقاف».',
    'suspend_notification_title'        => 'تم إيقاف المستخدم',
    'suspend_notification_body_suffix'  => ' لم يعد قادرًا على تسجيل الدخول.',

    // ─── Unsuspend action ──────────────────────────────────────────────
    'action_unsuspend'                  => 'رفع الإيقاف',
    'unsuspend_notification_title'      => 'تم إعادة تفعيل المستخدم',
    'unsuspend_notification_body_suffix' => ' يمكنه تسجيل الدخول مجددًا.',

    // ─── Invite team member ────────────────────────────────────────────
    'invite_action_label'               => 'دعوة عضو فريق',
    'invite_email_label'                => 'البريد الإلكتروني',
    'invite_failed_title'               => 'تعذر إرسال الدعوة',
    'invite_sent_title'                 => 'تم إرسال الدعوة إلى :email',

    // ─── CreateUser notifications ──────────────────────────────────────
    'create_failed_title'               => 'فشل إنشاء المستخدم',
    'create_seat_limit_title'           => 'تم الوصول إلى حد المقاعد',
    'create_email_taken_title'          => 'البريد الإلكتروني مستخدم بالفعل',

    // ─── CreateUser (invitation flow) ──────────────────────────────────
    'create_invite_title'               => 'دعوة عضو فريق',
    'create_invite_heading'             => 'دعوة عضو فريق جديد',
    'create_invite_subheading'          => 'سيستلم بريدًا يحتوي على رابط لتعيين اسمه وكلمة مروره.',
    'create_no_workspace_title'         => 'لا يوجد سياق مساحة عمل',
    'create_no_workspace_body'          => 'تعذر تحديد مساحة عملك. سجّل الخروج ثم الدخول مجددًا، ثم حاول مرة أخرى.',
    'create_invite_sent_title'          => 'تم إرسال الدعوة',
    'create_invite_sent_body'           => 'تم إرسال بريد يحتوي على رابط الإعداد إلى :email.',

    // ─── Select options ────────────────────────────────────────────
    'option_role_manager'               => 'مدير',
    'option_role_member'                => 'عضو',

    // ─── Booking-links modal (per-user list) ───────────────────────
    'booking_links_minutes_suffix'      => 'د',

    // ─── ListUsers subheading (role-permissions banner) ────────────
    // Rendered via resources/views/filament/resources/users/list-subheading.blade.php
    // — see App\Filament\Resources\UserResource\Pages\ListUsers::getSubheading().
    'subheading_role_permissions_title' => 'أذونات الأدوار',
    'subheading_intro'                  => 'مستويان لأعضاء الفريق. كلاهما محصور بالمستأجر — لا يرى البيانات خارج مساحة العمل هذه.',
    'subheading_manager_title'          => 'مدير',
    'subheading_manager_desc'           => 'صلاحيات كاملة على العملاء المحتملين + خطوط الأنابيب + الأتمتة + النماذج. يمكنه دعوة وإيقاف الفريق. لا يمكنه حذف المشرفين.',
    'subheading_member_title'           => 'عضو',
    'subheading_member_desc'            => 'مستخدم قياسي. يعمل مع العملاء المحتملين، يعرض النماذج والتقارير. لا توجد إدارة فريق ولا إعدادات.',
];
