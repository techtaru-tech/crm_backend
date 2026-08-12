<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| TeamSettingsPage — Filament tenant strings
|------------------------------------------------------------
*/

return [
    'title'                          => 'إدارة الفريق والمقاعد',
    'navigation_label'               => 'الفريق والمقاعد',

    // Notifications - member ops
    'member_removed'                 => 'تمت إزالة :name من مساحة العمل هذه.',
    'member_suspended_title'         => 'تم تعليق :name.',
    'member_suspended_body'          => 'لم يعد بإمكانه تسجيل الدخول.',
    'member_reactivated'             => 'تمت إعادة تفعيل :name.',

    // Reset password
    'reset_link_sent_title'          => 'تم إرسال رابط إعادة التعيين',
    'reset_link_sent_body'           => 'سيتلقى :email رسالة بريد إلكتروني خلال دقيقة. الرابط صالح لمدة 60 دقيقة.',
    'reset_link_failed_title'        => 'تعذّر إرسال رابط إعادة التعيين',

    // Invite action
    'action_invite'                  => 'دعوة عضو في الفريق',
    'invite_email_label'             => 'البريد الإلكتروني',
    'invite_email_placeholder'       => 'teammate@company.com',
    'invite_role_label'              => 'الدور',
    'invite_role_manager'            => 'مدير',
    'invite_role_member'             => 'عضو',
    'invite_failed_title'            => 'تعذّر إرسال الدعوة',
    'invite_sent_title'              => 'تم إرسال الدعوة',
    'invite_sent_body'               => 'تم الإرسال إلى :email.',

    // Adjust seats action
    'action_adjust_seats'            => 'تعديل حد المقاعد',
    'max_seats_label'                => 'الحد الأقصى للمقاعد',

    // ─── Blade view — role permissions banner ─────────────────────────
    'role_permissions_title'         => 'صلاحيات الأدوار',
    'role_permissions_lede'          => 'مستويان لأعضاء الفريق. كلاهما مقيد بالمستأجر — لا يريان بيانات خارج مساحة العمل هذه.',
    'role_card_manager_title'        => 'مدير',
    'role_card_manager_desc'         => 'وصول كامل إلى العملاء المحتملين وخطوط الأنابيب والأتمتة والنماذج. يمكنه دعوة الفريق وتعليقه. لا يمكنه حذف المسؤولين.',
    'role_card_member_title'         => 'عضو',
    'role_card_member_desc'          => 'مستخدم قياسي. يعمل مع العملاء المحتملين، ويعرض النماذج والتقارير. لا توجد إدارة فريق ولا إعدادات.',

    // ─── Blade view — seat usage card ─────────────────────────────────
    'seat_usage_title'               => 'استخدام المقاعد',
    'seat_usage_subtitle'            => 'مستخدم :used من :max مقاعد',
    'seat_count_suffix'              => 'متاحة',
    'seat_plan_prefix'               => 'الخطة:',
    'seat_limit_msg'                 => 'تم الوصول إلى حد المقاعد. علِّق أو احذف عضواً — أو رقّ خطتك — لدعوة مستخدمين جدد.',

    // ─── Blade view — members table ───────────────────────────────────
    'members_title'                  => 'أعضاء الفريق',
    'col_name'                       => 'الاسم',
    'col_email'                      => 'البريد الإلكتروني',
    'col_role'                       => 'الدور',
    'col_status'                     => 'الحالة',
    'col_actions'                    => 'الإجراءات',
    'row_self_suffix'                => '(أنت)',
    'status_suspended'               => 'مُعلَّق',
    'status_active'                  => 'نشط',
    'action_reset_password'          => 'إعادة تعيين كلمة المرور',
    'action_reset_password_title'    => 'إرسال بريد إعادة تعيين كلمة المرور',
    'action_unsuspend'               => 'إلغاء التعليق',
    'action_suspend'                 => 'تعليق',
    'action_remove'                  => 'إزالة',
    'empty_no_members_html'          => 'لا يوجد أعضاء فريق بعد.  استخدم الإجراء <strong>دعوة عضو في الفريق</strong> أعلاه للبدء.',

    // ─── Blade view — wire:confirm prompts ────────────────────────────
    'confirm_reset_password'         => 'إرسال رابط إعادة تعيين كلمة المرور إلى :email؟',
    'confirm_suspend'                => 'تعليق :name؟  سيفقد الوصول حتى إلغاء التعليق.',
    'confirm_remove'                 => 'إزالة :name من مساحة العمل هذه؟',

    // ─── Role badges (Pass 22) ────────────────────────────────────────
    'role_admin'                     => 'مسؤول',
    'role_owner'                     => 'المالك',
    'role_manager'                   => 'مدير',
    'role_member'                    => 'عضو',
    'role_user'                      => 'مستخدم',

    // ─── Role change (inline editor) ──────────────────────────────────
    'change_role_label'              => 'تغيير الدور',
    'role_changed'                   => 'تم تغيير دور :name إلى :role.',
    'role_invalid'                   => 'دور غير صالح.',
    'role_cannot_change_self'        => 'لا يمكنك تغيير دورك الخاص.',
    'role_admin_only'                => 'يمكن للمسؤول وحده تغيير دور مسؤول آخر.',
    'role_admin_not_editable'        => 'تُدار أدوار المسؤولين من قِبل مالك مساحة العمل، وليس من هنا.',

    // ─── Seat-card plan labels (Pass 22) ──────────────────────────────
    'plan_free'                      => 'مجاني',
    'plan_starter'                   => 'مبتدئ',
    'plan_pro'                       => 'احترافي',
    'plan_business'                  => 'أعمال',
    'plan_enterprise'                => 'مؤسسات',
    'plan_trial'                     => 'تجريبي',
];
