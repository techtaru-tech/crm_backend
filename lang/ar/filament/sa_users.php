<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| SuperAdmin UserResource — Filament admin strings
|------------------------------------------------------------
| Accessed via __('filament/sa_users.<key>').
| Buyers translate or adapt by editing this file or copying it
| to lang/<locale>/filament/sa_users.php.
*/

return [

    // ----- User details form -----
    'primary_workspace'           => 'مساحة العمل الأساسية',
    'super_admin'                 => 'مشرف أعلى',
    'super_admin_helper'          => 'يمنح الوصول إلى لوحة المشرف الأعلى.',

    // ----- Table columns -----
    'workspace'                   => 'مساحة العمل',
    'sa'                          => 'مشرف أعلى',
    'status'                      => 'الحالة',
    'status_active'               => 'نشط',
    'status_suspended'            => 'موقوف',

    // ----- Filters -----
    'filter_role'                 => 'الدور',
    'filter_role_super_admin'     => 'مشرف أعلى',
    'filter_role_regular_user'    => 'مستخدم عادي',

    // ----- Reset password action -----
    'reset_password'              => 'إرسال رابط إعادة التعيين',
    'reset_password_demo_guard'   => 'العرض: لا يمكن إعادة تعيين كلمات مرور المشرفين الأعلى.',
    'reset_password_modal_heading'=> 'إرسال رابط إعادة التعيين إلى :email؟',
    'reset_password_modal_description' => 'صالح لمدة 60 دقيقة.',
    'reset_link_sent_title'       => 'تم إرسال رابط إعادة التعيين',
    'reset_link_sent_body'        => 'تم إرسال البريد إلى :email.',
    'reset_link_failed_title'     => 'تعذر إرسال رابط إعادة التعيين',
    'password_broker_rejected'    => 'رفض وسيط كلمة المرور (:status).',

    // ----- Suspend action -----
    'suspend'                     => 'إيقاف',
    'suspend_demo_guard'          => 'العرض: لا يمكن إيقاف المستخدمين.',
    'suspend_modal_heading'       => 'إيقاف :name؟',
    'suspend_modal_description'   => 'سيفقد الوصول عبر كل لوحة حتى رفع الإيقاف.',
    'user_suspended_title'        => 'تم إيقاف المستخدم',
    'user_suspended_body'         => 'لم يعد :email قادرًا على تسجيل الدخول.',

    // ----- Unsuspend action -----
    'unsuspend'                   => 'رفع الإيقاف',
    'unsuspend_demo_guard'        => 'العرض: لا يمكن رفع إيقاف المستخدمين.',
    'user_reactivated_title'      => 'تم إعادة تفعيل المستخدم',
    'user_reactivated_body'       => 'يمكن لـ :email تسجيل الدخول مجددًا.',

    // ----- Field labels (form + table) -----
    'name'                        => 'الاسم',
    'email'                       => 'البريد الإلكتروني',
    'created_at'                  => 'تاريخ الإنشاء',

    // ----- Model labels -----
    'model_label'                 => 'مستخدم',
    'plural_model_label'          => 'المستخدمون',

];
