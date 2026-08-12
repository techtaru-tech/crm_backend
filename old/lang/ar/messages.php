<?php

return [
    'app'       => 'التطبيق',
    'dashboard' => 'لوحة التحكم',
    'save'      => 'حفظ',
    'cancel'    => 'إلغاء',
    'delete'    => 'حذف',
    'edit'      => 'تعديل',
    'create'    => 'إنشاء',
    'search'    => 'بحث',
    'loading'   => 'جار التحميل…',
    'yes'       => 'نعم',
    'no'        => 'لا',
    'back'      => 'رجوع',
    'submit'    => 'إرسال',
    'confirm'   => 'تأكيد',
    'success'   => 'تم بنجاح',
    'error'     => 'خطأ',
    'warning'   => 'تحذير',

    'billing_unknown_plan'          => 'خطة غير معروفة [:plan].',
    'calendar_unsupported_provider' => 'مزوّد غير مدعوم: :provider',
    'registration_throttled'        => 'محاولات تسجيل كثيرة جدًا. حاول مرة أخرى بعد :seconds ثانية.',

    // ─── DemoMode guard / abort copy (live demo lockdown) ───
    'demo_mode_title'            => '🛡️ وضع العرض التجريبي',
    'demo_action_disabled_body'  => 'هذا الإجراء معطّل في العرض التجريبي المباشر. احصل على نسختك الخاصة لتفعيل كل الميزات.',
    'demo_get_leadhub'           => 'احصل على :app',
    'demo_action_disabled_short' => 'هذا الإجراء معطّل في العرض التجريبي المباشر.',

    // ─── License-required block screen (EnforceLicense middleware) ───
    'license_required_short'              => 'يلزم الترخيص.',
    'license_required_title'              => ':app — يلزم الترخيص',
    'license_required_heading'            => 'يلزم الترخيص',
    'license_required_lead'               => 'يجب إعادة التحقق من ترخيص :app قبل استخدام لوحة الإدارة مرة أخرى.',
    'license_required_reason_label'       => 'السبب',
    'license_required_step_codecanyon'    => 'سجّل الدخول إلى حسابك على CodeCanyon وافتح قسم التنزيلات.',
    'license_required_step_purchase_code' => 'انسخ رمز الشراء من شهادة الترخيص.',
    'license_required_step_paste_settings' => 'الصقه في المدير الأعلى ← الإعدادات ← الترخيص ثم اضغط على "تحقّق".',
    'license_required_cta_settings'       => 'فتح إعدادات الترخيص',
    'license_required_cta_codecanyon'     => 'الذهاب إلى CodeCanyon',
    'license_required_item_label'         => 'منتج CodeCanyon',

    // ─── Enforce2Fa middleware (JSON 403 for mobile/API) ───
    'two_factor_required' => 'المصادقة الثنائية مطلوبة. يُرجى تفعيل المصادقة الثنائية من إعدادات حسابك.',

    // ─── Billing controller errors ───
    'billing_checkout_failed'    => 'فشل إتمام الدفع.',
    'billing_portal_stripe_only' => 'بوابة العملاء متاحة لـ Stripe فقط.',
    'billing_portal_unavailable' => 'بوابة العملاء غير متاحة. يُرجى إتمام عملية دفع عبر Stripe أولًا أو التواصل مع الدعم.',

    // ─── Calendar OAuth errors ───
    'calendar_connection_not_found' => 'لم يتم العثور على الاتصال.',
    'calendar_oauth_no_session'     => 'يتطلب رد نداء OAuth الخاص بالتقويم جلسة مُصادَقًا عليها.',
    'oauth_state_mismatch'          => 'عدم تطابق حالة OAuth — احتمال محاولة CSRF.',
    'calendar_disconnected_success' => 'تم فصل التقويم.',

    // ─── Invitation errors ───
    'invitation_invalid_or_expired' => 'هذه الدعوة غير صالحة أو منتهية الصلاحية.',

    // ─── Tenant scope errors ───
    'no_tenant_assigned'      => 'حسابك غير مرتبط بأي مساحة عمل.',
    'session_revoked'         => 'تم إلغاء جلستك. يُرجى تسجيل الدخول مرة أخرى.',
    'no_workspace_resolved'   => 'لم يتم تحديد أي مساحة عمل.',
    'no_workspace_found'      => 'لم يتم العثور على مساحة عمل للمضيف :host.',

    // ─── Data export controller (GDPR Art. 20) ───
    'export_link_invalid'      => 'انتهت صلاحية رابط التنزيل أو أنه غير صالح.',
    'export_link_expired'      => 'انتهت صلاحية رابط التنزيل.',
    'export_link_wrong_user'   => 'رابط التنزيل هذا يعود لمستخدم آخر.',
    'export_file_unavailable'  => 'ملف التصدير لم يعد متاحًا. يُرجى طلب تصدير جديد.',

    // ─── Portal (customer dashboard) ───
    'file_type_not_allowed'     => 'نوع الملف غير مسموح به.',
    'portal_magic_link_invalid' => 'رابط تسجيل الدخول هذا غير صالح أو منتهي الصلاحية أو سبق استخدامه. يُرجى طلب رابط جديد.',
    'portal_file_uploaded'      => 'تم رفع الملف.',

    // ─── Impersonation & super admin ───
    'only_super_admins_impersonate'   => 'يمكن للمشرفين العامين فقط انتحال الهوية.',
    'impersonate_no_owner'            => 'مساحة العمل هذه ليس لها مالك لانتحال هويته.',
    'impersonate_already_active'      => 'أنت تنتحل هوية بالفعل. أوقف الجلسة الحالية أولًا.',
    'access_denied_super_admin_only'  => 'تم رفض الوصول. للمشرفين العامين فقط.',
    'signed_in_as_super_admin_info'   => 'لقد سجّلت الدخول كمشرف عام. استخدم إجراء الانتحال على مستأجر للوصول إلى مساحة عمله.',

    // ─── Security middleware ───
    'access_denied_ip_not_whitelisted' => 'تم رفض الوصول: عنوان IP الخاص بك غير مُدرج في القائمة البيضاء لمساحة العمل هذه.',
    'forbidden_generic'                => 'ممنوع.',

    // ─── Lead attachment guard ───
    'attachment_disk_not_allowed' => 'قرص المرفقات غير موجود في قائمة السماح.',

    // ─── Public quote (customer-facing) ───
    'quote_already_accepted'                  => 'تم قبول هذا العرض بالفعل.',
    'quote_already_accepted_cannot_decline'   => 'تم قبول هذا العرض بالفعل ولا يمكن رفضه.',
    'quote_response_recorded'                 => 'تم تسجيل ردّك. شكرًا لك.',

    // ─── Public invoice (customer-facing) ───
    'invoice_already_paid'             => 'تم دفع هذه الفاتورة بالفعل.',
    'invoice_pay_manual_instructions'  => 'يُرجى تحويل المبلغ باستخدام بيانات البنك الواردة في هذه الصفحة. ستُحدَّد فاتورتك كمدفوعة بمجرد أن يقوم المستأجر بتسوية الدفع.',

    // ─── Integration OAuth (CRM/marketing) ───
    'integration_oauth_unavailable'    => 'OAuth غير متاح لـ :type. يُرجى ضبط client_id و client_secret أولًا.',
    'integration_oauth_state_mismatch' => 'عدم تطابق حالة OAuth. يُرجى المحاولة مرة أخرى.',
    'integration_oauth_denied'         => 'تم رفض OAuth: :reason',
    'integration_oauth_no_code'        => 'لم يتم استلام رمز تفويض.',
    'integration_oauth_exchange_failed'=> 'فشل تبادل الرمز: :error',
    'integration_oauth_connected'      => 'تم ربط :label بنجاح عبر OAuth.',

    // ─── Lead-source OAuth connections ───
    'oauth_not_configured_for_source'  => 'OAuth غير مُهيّأ لـ :source. يُرجى إضافة client_id و client_secret أولًا.',
    'oauth_session_expired'            => 'انتهت صلاحية جلسة OAuth. يُرجى المحاولة مرة أخرى.',
    'oauth_state_invalid'              => 'حالة OAuth غير صالحة. يُرجى المحاولة مرة أخرى.',
    'oauth_authorization_denied'       => 'تم رفض تفويض OAuth: :reason',
    'oauth_connection_not_found'       => 'لم يتم العثور على الاتصال أو أن رمز التفويض مفقود.',
    'oauth_token_exchange_failed'      => 'فشل تبادل الرمز: :error',
    'oauth_token_retrieval_failed'     => 'تعذّر الحصول على رموز الوصول.',
    'oauth_connected_success'          => 'تم الاتصال بنجاح عبر OAuth.',

    // ─── Public widget submission ───
    'widget_not_found'         => 'لم يتم العثور على الأداة',
    'widget_submission_failed' => 'فشل الإرسال',

    // ─── Public form (reCAPTCHA) ───
    'recaptcha_token_missing'      => 'رمز reCAPTCHA مفقود.',
    'recaptcha_spam_check_failed'  => 'فشل فحص الرسائل غير المرغوب فيها.',

    // ─── Public booking endpoints ───
    'booking_invalid_datetime'             => 'تاريخ ووقت غير صالحين.',
    'booking_time_advance_notice_failed'   => 'هذا الوقت لم يعد يستوفي شرط الإشعار المسبق.',
    'booking_time_too_far_future'          => 'هذا الوقت بعيد جدًا في المستقبل.',
    'booking_slot_taken'                   => 'تم حجز هذه الفترة للتو. يُرجى اختيار وقت آخر.',

    // ─── InvitationService: MAIL=log warning notification ───
    'email_logged_title' => 'تم تسجيل البريد، لم يتم إرساله',
    'email_logged_body'  => "مُشغّل البريد مضبوط على 'log'، لذا لن يصل بريد الدعوة إلى :email. انسخ هذا الرابط الموقَّع وشاركه يدويًا:\n\n:url",

    // ─── PasswordSetupController: post-setup welcome notification ───
    'password_set_title' => 'تم ضبط كلمة المرور',
    'password_set_body'  => 'مرحبًا بك. حسابك جاهز.',

    // ─── InvitationController: post-accept welcome notification ───
    'welcome_to_app'         => 'مرحبًا بك في :app',
    'joined_workspace_body'  => 'لقد انضممت إلى :workspace. إليك لوحة التحكم الخاصة بك.',
    'workspace_fallback'     => 'مساحة عملك',

    'auth' => [
        'email'     => 'البريد الإلكتروني',
        'password'  => 'كلمة المرور',
        'sign_in'   => 'تسجيل الدخول',
        'sign_out'  => 'تسجيل الخروج',
        'register'  => 'إنشاء حساب',
    ],

    'onboarding' => [
        'subjects' => [
            'day_1'    => 'مرحبًا بك في :workspace — ابدأ بإضافة عميل محتمل',
            'day_3'    => 'كيف تسير الأمور في :workspace؟',
            'day_5'    => '٣ أتمتة يفعّلها كل فريق في الأسبوع الأول',
            'day_7'    => 'مراجعة سريعة: هل يحقق :workspace قيمته؟',
            'fallback' => 'رسالة من نظام إدارة علاقاتك',
        ],
    ],
];
