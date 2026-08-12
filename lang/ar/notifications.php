<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| سلاسل إشعارات المسؤول / المسؤول الأعلى (إشعارات Filament)
|--------------------------------------------------------------------------
|
| رسائل ملاحظات قصيرة تُعرض عبر
| Filament\Notifications\Notification::make()->title(__('notifications.X'))
| في صفحات Filament الخاصة بمسؤول المستأجر والمسؤول الأعلى.
|
| يمكن للمشترين ترجمة النص أو تعديله بتحرير هذا الملف، أو نسخه إلى
| lang/<locale>/notifications.php وترجمة القيم.
|
| تستخدم العناصر النائبة اصطلاح :placeholder الخاص بـ Laravel؛ مرّر القيم
| عبر __('notifications.key', ['placeholder' => $value]).
|
*/

return [

    // --- أذونات عامة / غير موجود ----------------------------------------
    'no_tenant_found'                      => 'لم يتم العثور على المستأجر.',
    'no_workspace_active'                  => 'لا توجد مساحة عمل نشطة حاليًا.',
    'no_workspace_context'                 => 'لم يتم العثور على سياق مساحة العمل.',
    'permission_denied'                    => 'تم رفض الإذن',
    'permission_required'                  => 'ليس لديك إذن.',
    'no_remove_permission'                 => 'ليس لديك إذن لإزالة أعضاء الفريق.',
    'no_suspend_permission'                => 'ليس لديك إذن لتعليق أعضاء الفريق.',
    'cannot_remove_self'                   => 'لا يمكنك إزالة نفسك.',
    'cannot_suspend_self'                  => 'لا يمكنك تعليق نفسك.',
    'admin_only_suspend_admin'             => 'يمكن للمسؤول فقط تعليق مسؤول آخر.',
    'user_not_in_workspace'                => 'لم يتم العثور على المستخدم في مساحة العمل هذه.',
    'no_owner_for_workspace'               => 'لم يتم العثور على مالك لمساحة العمل هذه',

    // --- تم حفظ الإعدادات ------------------------------------------------
    'profile_updated'                      => 'تم تحديث الملف الشخصي بنجاح.',
    'password_changed'                     => 'تم تغيير كلمة المرور بنجاح.',
    'branding_saved'                       => 'تم حفظ إعدادات العلامة التجارية.',
    'email_saved'                          => 'تم حفظ إعدادات البريد الإلكتروني.',
    'general_saved'                        => 'تم حفظ الإعدادات العامة.',
    'messaging_saved'                      => 'تم حفظ إعدادات المراسلة.',
    'realtime_saved'                       => 'تم حفظ إعدادات الوقت الفعلي.',
    'security_saved'                       => 'تم حفظ إعدادات الأمان لمساحة العمل هذه.',
    'storage_saved'                        => 'تم حفظ إعدادات التخزين.',
    'availability_saved'                   => 'تم حفظ التوفر',

    // --- إخفاقات عامة في الحفظ / الاتصال (مع :error) --------------------
    'save_failed'                          => 'فشل الحفظ: :error',
    'test_failed_error'                    => 'فشل الاختبار: :error',
    'storage_test_failed'                  => 'فشل اختبار التخزين: :error',
    'storage_test_ok'                      => 'اتصال التخزين يعمل.',

    // --- الجلسات / المصادقة الثنائية ------------------------------------
    'session_revoked'                      => 'تم إلغاء الجلسة.',
    'all_other_sessions_revoked'           => 'تم إلغاء جميع الجلسات الأخرى.',
    'cannot_revoke_current_session'        => 'لا يمكنك إلغاء جلستك الحالية.',
    'twofa_enabled'                        => 'تم تفعيل المصادقة الثنائية!',
    'twofa_disabled'                       => 'تم تعطيل المصادقة الثنائية.',
    'twofa_invalid_code'                   => 'رمز التحقق غير صالح. حاول مرة أخرى.',
    'twofa_recovery_regenerated'           => 'تم إعادة إنشاء رموز الاسترداد.',
    'twofa_generate_secret_first'          => 'الرجاء إنشاء سر أولاً.',

    // --- التهيئة / المسؤول المتنوع --------------------------------------
    'onboarding_revisit_hint'              => 'يمكنك العودة إلى التهيئة في أي وقت من الإعدادات ← الفريق.',
    'preset_loaded'                        => 'تم تحميل الإعداد المسبق. انقر على حفظ للتطبيق.',

    // --- مفاتيح API ------------------------------------------------------
    'api_key_revoked'                      => 'تم إلغاء مفتاح API.',

    // --- الاتصالات / Webhooks -------------------------------------------
    'connection_ok'                        => 'نجح الاتصال!',
    'connection_failed'                    => 'فشل الاتصال!',
    'test_delivered_http'                  => 'تم تسليم الاختبار — HTTP :status',
    'test_failed_http'                     => 'فشل الاختبار — HTTP :status',
    'delivery_requeued'                    => 'تم وضع التسليم في قائمة الانتظار لإعادة المحاولة.',
    'webhook_requeued'                     => 'تم إعادة وضع الـ Webhook في قائمة الانتظار',
    'webhook_source_missing'               => 'لا يمكن إعادة المحاولة: لم يتم العثور على اتصال المصدر',

    // --- الاجتماعات / الحجوزات ------------------------------------------
    'booking_cancelled'                    => 'تم إلغاء الحجز',
    'booking_completed'                    => 'تم وضع علامة كمكتمل',
    'booking_no_show'                      => 'تم وضع علامة كعدم حضور',

    // --- الترخيص ---------------------------------------------------------
    'license_required'                     => 'الرجاء إدخال مفتاح الترخيص.',

    // --- النطاق ----------------------------------------------------------
    'domain_saved'                         => 'تم حفظ النطاق. يتم تشغيل التحقق في الخلفية.',
    'domain_verification_retriggered'      => 'تمت إعادة تشغيل التحقق من النطاق.',
    'domain_already_taken'                 => 'هذا النطاق مسجل بالفعل لمساحة عمل أخرى.',

    // --- المستأجر / حدود المقاعد / انتحال الشخصية ------------------------
    'seat_limit_updated'                   => 'تم تحديث حد المقاعد إلى :limit.',
    'already_impersonating'                => 'أنت تنتحل بالفعل — أوقف الجلسة الحالية أولاً',

    // --- الوحدات (SA) ----------------------------------------------------
    'module_enabled'                       => 'تم تمكين الوحدة: :name',
    'module_disabled'                      => 'تم تعطيل الوحدة: :name',
    'module_deleted'                       => 'تم حذف الوحدة: :name',
    'module_enable_failed'                 => 'فشل التمكين',
    'module_disable_failed'                => 'فشل التعطيل',
    'module_delete_failed'                 => 'فشل الحذف',
    'module_install_failed'                => 'فشل التثبيت',
    'module_caches_cleared'                => 'تم مسح ذاكرات التخزين المؤقت للوحدة.',
    'module_regenerate_failed'             => 'فشل إعادة التوليد',
    'module_upload_zip_first'              => 'الرجاء تحميل ملف zip أولاً.',

    // --- خيار احتياطي عام (سمة SafeAction) ------------------------------
    'something_went_wrong'                 => 'حدث خطأ ما',

    // --- مركز الإشعارات (livewire/notification-center) ------------------
    'panel_title'                          => 'الإشعارات',
    'mark_all_read'                        => 'تعليم الكل كمقروء',
    'aria_label'                           => 'الإشعارات',
    'dismiss'                              => 'تجاهل',
    'empty_state'                          => 'لا توجد إشعارات بعد',
    'load_more'                            => 'تحميل المزيد',

    /*
    |--------------------------------------------------------------------------
    | نسخة الإشعارات الموجهة للمستخدم (app/Notifications/*.php)
    |--------------------------------------------------------------------------
    |
    | السلاسل المعروضة للمستخدم من فئات Notification القابلة للوضع في قائمة
    | الانتظار — أسطر موضوع toMail()، والعناوين، وأسطر النص، وتسميات أزرار
    | الإجراءات، ورسائل toDatabase() المعروضة في القائمة المنسدلة لأيقونة
    | الجرس، وعناوين دفع البث. تستخدم العناصر النائبة اصطلاح :placeholder.
    |
    */

    // --- تسميات الأزرار / الخيارات الاحتياطية المشتركة -------------------
    'btn_view_lead'                        => 'عرض العميل المحتمل',
    'system_fallback'                      => 'النظام',

    // --- ExportFailedNotification ---------------------------------------
    'export_failed_mail_subject'           => 'فشل تصدير العملاء المحتملين',
    'export_failed_mail_headline'          => 'فشل التصدير',
    'export_failed_mail_line_intro'        => 'للأسف لم يكتمل تصدير العملاء المحتملين الخاص بك.',
    'export_failed_mail_line_reason'       => '<strong>السبب:</strong> :reason',
    'export_failed_mail_line_retry'        => 'الرجاء المحاولة مرة أخرى. إذا استمرت المشكلة، اتصل بالمسؤول.',
    'export_failed_db_message'             => 'فشل تصدير العملاء المحتملين: :error',

    // --- IntegrationSyncFailedNotification ------------------------------
    'integration_sync_failed_broadcast_title' => 'فشلت المزامنة لـ :integration على :lead',
    'integration_sync_failed_push_title'   => 'فشلت مزامنة التكامل',
    'integration_sync_failed_mail_subject' => 'فشلت مزامنة التكامل: :integration',
    'integration_sync_failed_mail_headline' => 'فشلت مزامنة التكامل',
    'integration_sync_failed_mail_line_intro' => 'فشلت مزامنة تكامل لأحد عملائك المحتملين.',
    'integration_sync_failed_mail_line_integration' => '<strong>التكامل:</strong> :integration',
    'integration_sync_failed_mail_line_lead' => '<strong>العميل المحتمل:</strong> :name',
    'integration_sync_failed_mail_line_error' => '<strong>الخطأ:</strong> :error',

    // --- LeadAssignedNotification ---------------------------------------
    'lead_assigned_broadcast_title'        => 'تم تعيين العميل المحتمل: :name',
    'lead_assigned_push_title'             => 'تم تعيين عميل محتمل لك',
    'lead_assigned_mail_subject'           => 'تم تعيين عميل محتمل لك',
    'lead_assigned_mail_headline'          => 'تم تعيين عميل محتمل لك',
    'lead_assigned_mail_line_intro'        => 'تم تعيين عميل محتمل لك.',
    'lead_assigned_mail_line_lead'         => '<strong>العميل المحتمل:</strong> :name',
    'lead_assigned_mail_line_assigned_by'  => '<strong>عُيّن بواسطة:</strong> :name',

    // --- LeadAutomationNotification -------------------------------------
    'automation_push_title'                => 'تم تشغيل الأتمتة',
    'automation_mail_subject'              => 'تم تشغيل الأتمتة: :message',
    'automation_mail_headline'             => 'تم تشغيل الأتمتة',
    'automation_mail_line_intro'           => 'تم تشغيل أتمتة لعميل محتمل في خط أنابيبك.',
    'automation_mail_line_lead'            => '<strong>العميل المحتمل:</strong> :name',
    'automation_mail_line_message'         => '<strong>الرسالة:</strong> :message',

    // --- LeadReceivedNotification ---------------------------------------
    'lead_received_broadcast_title'        => 'عميل محتمل جديد: :name',
    'lead_received_push_title'             => 'تم استلام عميل محتمل جديد',
    'lead_received_mail_subject'           => 'عميل محتمل جديد: :name',
    'lead_received_mail_headline'          => 'تم استلام عميل محتمل جديد',
    'lead_received_mail_line_intro'        => 'تم استلام عميل محتمل جديد في خط أنابيبك.',
    'lead_received_mail_line_name'         => '<strong>الاسم:</strong> :name',
    'lead_received_mail_line_email'        => '<strong>البريد الإلكتروني:</strong> :email',
    'lead_received_mail_line_phone'        => '<strong>الهاتف:</strong> :phone',
    'lead_received_mail_line_source'       => '<strong>المصدر:</strong> :source',

    // --- LeadStageChangedNotification -----------------------------------
    'lead_stage_changed_broadcast_title'   => 'تم نقل العميل المحتمل إلى :stage: :name',
    'lead_stage_changed_push_title'        => 'تم تغيير مرحلة العميل المحتمل',
    'lead_stage_changed_push_body'         => 'تم نقل :name إلى :stage',
    'lead_stage_changed_mail_subject'      => 'تم تحديث مرحلة العميل المحتمل',
    'lead_stage_changed_mail_headline'     => 'تم تحديث مرحلة العميل المحتمل',
    'lead_stage_changed_mail_line_intro'   => 'انتقل عميل محتمل إلى مرحلة جديدة في خط الأنابيب.',
    'lead_stage_changed_mail_line_lead'    => '<strong>العميل المحتمل:</strong> :name',
    'lead_stage_changed_mail_line_from'    => '<strong>من:</strong> :stage',
    'lead_stage_changed_mail_line_to'      => '<strong>إلى:</strong> :stage',

    // --- LeadTaskReminderNotification -----------------------------------
    'task_reminder_db_message'             => 'تذكير بمهمة: :title',
    'task_reminder_mail_subject'           => 'تذكير بمهمة: :title',
    'task_reminder_mail_greeting'          => 'تذكير بمهمة',
    'task_reminder_mail_line_intro'        => 'لديك مهمة قادمة:',
    'task_reminder_mail_line_lead'         => 'العميل المحتمل ذو الصلة: :name',
    'task_reminder_mail_line_due'          => 'الاستحقاق: :due',
    'task_reminder_mail_line_priority'     => 'الأولوية: :priority',
    'task_reminder_mail_line_description'  => 'الوصف: :description',
    'task_reminder_mail_line_outro'        => 'شكرًا لاستخدامك :app.',
    'task_reminder_due_no_date'            => 'بدون تاريخ',
    'task_reminder_lead_fallback'          => 'عميل محتمل',
    'task_reminder_priority_normal'        => 'عادية',

    // --- LeadsExportReady -----------------------------------------------
    'export_ready_db_message'              => 'تصدير العملاء المحتملين جاهز للتنزيل.',
    'export_ready_push_title'              => 'التصدير جاهز',
    'export_ready_push_body'               => 'تصدير العملاء المحتملين (:filename) جاهز للتنزيل.',
    'export_ready_mail_subject'            => 'تصديرك جاهز: :filename',
    'export_ready_mail_headline'           => 'التصدير جاهز للتنزيل',
    'export_ready_mail_line_intro'         => 'تم إنشاء تصدير العملاء المحتملين وهو جاهز للتنزيل.',
    'export_ready_mail_line_file'          => '<strong>الملف:</strong> :filename',
    'export_ready_btn_download'            => 'تنزيل التصدير',

    // --- MarketplaceTemplateInstalledNotification -----------------------
    'marketplace_template_installed_message' => 'قام :installer بتثبيت قالبك ":template" (:type).',

    // --- TenantDataExportReady ------------------------------------------
    'tenant_data_export_ready_message'     => 'تصدير بياناتك لـ ":name" جاهز. تنتهي صلاحية رابط التنزيل خلال 24 ساعة.',

    // --- InvoiceDueReminderNotification ---------------------------------
    'invoice_due_reminder_db_message'         => 'الفاتورة :number تستحق قريبًا.',
    'invoice_due_reminder_customer_fallback'  => 'عميلك',
    'invoice_due_reminder_due_no_date'        => 'بدون تاريخ',
    'invoice_due_reminder_mail_subject'       => 'الفاتورة :number تستحق قريبًا',
    'invoice_due_reminder_mail_greeting'      => 'تذكير باستحقاق فاتورة',
    'invoice_due_reminder_mail_line_intro'    => 'إحدى فواتيرك تقترب من تاريخ استحقاقها:',
    'invoice_due_reminder_mail_line_number'   => 'الفاتورة: :number',
    'invoice_due_reminder_mail_line_customer' => 'العميل: :name',
    'invoice_due_reminder_mail_line_amount'   => 'المبلغ: :amount',
    'invoice_due_reminder_mail_line_due'      => 'الاستحقاق: :due',
    'invoice_due_reminder_btn_view'           => 'عرض الفاتورة',
    'invoice_due_reminder_mail_line_outro'    => 'شكرًا لاستخدامك :app.',
];
