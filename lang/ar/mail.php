<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| سطور موضوع البريد والنسخ المشتركة (Arabic)
|------------------------------------------------------------
| يتم الوصول إليها عبر __('mail.<key>', [...]).
|
| المفاتيح بصيغة snake_case، مجمعة حسب Mailable / ملف Blade.
| جميع السلاسل النصية المرئية للمستخدم في
| resources/views/emails/* و app/Mail/*::envelope() تمر من
| هنا للحفاظ على امتثال البند 1 من CodeCanyon
| (بدون نص مرئي مكتوب بشكل ثابت).
*/

return [

    // ─── Shared layout (resources/views/emails/layout.blade.php) ──
    'layout_default_title'        => 'LeadHub',
    'layout_preheader_fallback'   => 'إشعار من :app',
    'layout_footer_default'       => 'لقد تلقيت هذا البريد الإلكتروني لأنك مستخدم في :app.',

    // ─── Meeting booked (resources/views/emails/meeting/booked.blade.php) ──
    'meeting_booked_subject_host'        => 'حجز جديد: :name مع :guest في :when',
    'meeting_booked_subject_guest'       => 'تم تأكيد اجتماعك: :name في :when',
    'meeting_booked_default_name'        => 'اجتماع',
    'meeting_booked_title'               => 'تم تأكيد الاجتماع',
    'meeting_booked_heading_host'        => 'تم استلام حجز جديد',
    'meeting_booked_heading_guest'       => 'تم تأكيد اجتماعك',
    'meeting_booked_label_when'          => 'الموعد',
    'meeting_booked_label_guest'         => 'الضيف',
    'meeting_booked_label_phone'         => 'الهاتف',
    'meeting_booked_label_host'          => 'المضيف',
    'meeting_booked_label_location'      => 'المكان',
    'meeting_booked_label_notes'         => 'ملاحظات',
    'meeting_booked_location_google_meet' => 'Google Meet (سيتم إرسال الرابط لاحقًا)',
    'meeting_booked_location_zoom'       => 'Zoom (سيتم إرسال الرابط لاحقًا)',
    'meeting_booked_location_phone'      => 'مكالمة هاتفية',
    'meeting_booked_location_in_person'  => 'لقاء شخصي',
    'meeting_booked_location_default'    => 'التفاصيل أدناه',
    'meeting_booked_btn_reschedule'      => 'إعادة الجدولة',
    'meeting_booked_btn_cancel'          => 'إلغاء',
    'meeting_booked_ics_note'            => 'دعوة التقويم (.ics) مرفقة — افتحها لإضافتها إلى تقويمك.',

    // ─── Meeting cancelled (resources/views/emails/meeting/cancelled.blade.php) ──
    'meeting_cancelled_subject'   => 'تم إلغاء الاجتماع: :name في :when',
    'meeting_cancelled_default_name' => 'اجتماع',
    'meeting_cancelled_title'     => 'تم إلغاء الاجتماع',
    'meeting_cancelled_body'      => 'الاجتماع المجدول أصلًا في :when (:tz) قد تم إلغاؤه.',
    'meeting_cancelled_reason'    => 'السبب:',
    'meeting_cancelled_book_again_intro' => 'هل تحتاج إلى موعد آخر؟',
    'meeting_cancelled_book_again_link'  => 'احجز مرة أخرى',

    // ─── Portal magic link (resources/views/emails/portal-magic-link.blade.php) ──
    'portal_magic_link_subject'   => 'رابط تسجيل الدخول إلى بوابة :app',
    'portal_magic_link_greeting'  => 'مرحبًا :name،',
    'portal_magic_link_default_name' => 'عزيزي/عزيزتي',
    'portal_magic_link_body'      => 'إليك رابط تسجيل الدخول الآمن. انقر على الزر أدناه للوصول إلى حسابك. هذا الرابط صالح لمدة 30 دقيقة ويمكن استخدامه مرة واحدة فقط.',
    'portal_magic_link_button'    => 'تسجيل الدخول',
    'portal_magic_link_ignore'    => 'إذا لم تطلب هذا الرابط، يمكنك تجاهل هذا البريد الإلكتروني بأمان تام.',
    'portal_magic_link_fallback'  => 'الرابط لا يعمل؟ الصقه في متصفحك:',

    // ─── Tenant welcome (resources/views/emails/tenant-welcome.blade.php) ──
    'tenant_welcome_subject'      => 'مساحة عمل :workspace جاهزة',
    'tenant_welcome_hello'        => 'مرحبًا،',
    'tenant_welcome_intro'        => 'مساحة عمل :workspace جاهزة على :app.',
    'tenant_welcome_user_set_password' => 'يمكنك تسجيل الدخول في أي وقت باستخدام البريد الإلكتروني وكلمة المرور التي اخترتها أثناء التسجيل.',
    'tenant_welcome_admin_created'     => 'أنشأ المسؤول مساحة العمل هذه لك. استخدم الزر أدناه لتعيين كلمة مرور وتسجيل الدخول لأول مرة.',
    'tenant_welcome_workspace_label'   => 'مساحة العمل:',
    'tenant_welcome_email_label'       => 'البريد الإلكتروني:',
    'tenant_welcome_button_set_password' => 'تعيين كلمة المرور وتسجيل الدخول',
    'tenant_welcome_button_login'        => 'تسجيل الدخول إلى مساحة العمل',
    'tenant_welcome_setup_expires'       => 'رابط الإعداد هذا صالح لمدة 60 دقيقة. إذا انتهت صلاحيته، استخدم رابط',
    'tenant_welcome_forgot_password'     => '"نسيت كلمة المرور"',
    'tenant_welcome_setup_expires_suffix' => 'في صفحة تسجيل الدخول.',
    'tenant_welcome_ignore'              => 'إذا لم تكن تتوقع هذا البريد، يمكنك تجاهله بأمان تام.',

    // ─── Invitation (resources/views/emails/invitation.blade.php) ──
    'invitation_subject'          => 'لقد تمت دعوتك إلى :workspace على :app',
    'invitation_default_inviter' => 'أحد أعضاء الفريق',
    'invitation_hello'           => 'مرحبًا،',
    'invitation_body'            => ':inviter قد دعاك للانضمام إلى :workspace على :app بصفة :role.',
    'invitation_button'          => 'قبول الدعوة',
    'invitation_expiry'          => 'تنتهي صلاحية هذه الدعوة خلال 7 أيام.',
    'invitation_ignore'          => 'إذا لم تكن تتوقع هذه الدعوة، يمكنك تجاهل هذا البريد بأمان تام.',

    // ─── Password reset (resources/views/emails/password-reset.blade.php) ──
    'password_reset_subject'     => 'إعادة تعيين كلمة مرور :app',
    'password_reset_default_name' => 'عزيزي/عزيزتي',
    'password_reset_greeting'    => 'مرحبًا :name،',
    'password_reset_intro'       => 'تلقينا طلبًا لإعادة تعيين كلمة المرور لحسابك على :app. انقر على الزر أدناه لاختيار كلمة مرور جديدة.',
    'password_reset_button'      => 'إعادة تعيين كلمة المرور',
    'password_reset_expires'     => 'تنتهي صلاحية هذا الرابط خلال :minutes دقيقة. إذا لم تطلب إعادة تعيين كلمة المرور، يمكنك تجاهل هذا البريد بأمان — ستبقى كلمة المرور كما هي.',
    'password_reset_fallback'    => 'إذا لم يعمل الزر أعلاه، الصق هذا الرابط في متصفحك:',

    // ─── Payment failed (resources/views/emails/payment-failed.blade.php) ──
    'payment_failed_subject'     => 'إجراء مطلوب: فشل الدفع لـ :workspace',
    'payment_failed_heading'     => 'فشل الدفع',
    'payment_failed_attempt'     => 'المحاولة :attempt — يرجى تحديث طريقة الدفع.',
    'payment_failed_greeting'    => 'مرحبًا،',
    'payment_failed_body'        => 'حاولنا خصم المبلغ من البطاقة المسجلة لاشتراك :workspace الخاص بك ولم تتم عملية الدفع بنجاح.',
    'payment_failed_amount_label'      => 'المبلغ المستحق',
    'payment_failed_next_retry_label'  => 'إعادة المحاولة التلقائية التالية',
    'payment_failed_cta_body'    => 'لتجنب انقطاع الخدمة، يرجى تحديث طريقة الدفع في أقرب وقت ممكن. سنعيد محاولة الخصم تلقائيًا بعد تحديث البطاقة.',
    'payment_failed_button'      => 'تحديث طريقة الدفع',
    'payment_failed_help'        => 'الأسباب الشائعة لفشل الخصم: انتهاء صلاحية البطاقة، أو رصيد غير كافٍ، أو حجب من البنك لمكافحة الاحتيال. إذا كنت تحتاج إلى مساعدة، رد على هذا البريد.',

    // ─── Plan changed (resources/views/emails/plan-changed.blade.php) ──
    'plan_changed_subject_upgrade'   => 'تمت ترقيتك إلى :plan',
    'plan_changed_subject_downgrade' => 'تم تغيير خطتك إلى :plan',
    'plan_changed_subject_default'   => 'تم تحديث خطتك إلى :plan',
    'plan_changed_heading_upgrade'   => 'أنت الآن على خطة :plan',
    'plan_changed_heading_downgrade' => 'تم تحديث الخطة إلى :plan',
    'plan_changed_heading_default'   => 'تم تحديث الخطة إلى :plan',
    'plan_changed_greeting'      => 'مرحبًا،',
    'plan_changed_body'          => 'تم تحديث خطتك لـ :workspace على :app.',
    'plan_changed_previous_label' => 'الخطة السابقة',
    'plan_changed_new_label'     => 'الخطة الجديدة',
    'plan_changed_upgrade_note'  => 'الميزات الجديدة والحدود الأعلى مفعلة بالفعل عبر مساحة عملك. سجل الدخول في أي وقت للاستفادة منها.',
    'plan_changed_downgrade_note' => 'خطتك الجديدة نشطة فورًا. قد لا تكون بعض ميزات خطتك السابقة متاحة بعد الآن — راجع صفحة الفوترة للتفاصيل.',
    'plan_changed_button'        => 'عرض لوحة الفوترة',

    // ─── Plan slug labels (Pass 22) ────────────────────────────────────
    // Used by plan-changed.blade.php to translate the old/new plan slug
    // shown in the previous-plan / new-plan rows. Unknown future plans
    // fall back to ucfirst() in the view.
    'plan_value_free'            => 'مجاني',
    'plan_value_starter'         => 'مبتدئ',
    'plan_value_pro'             => 'احترافي',
    'plan_value_business'        => 'أعمال',
    'plan_value_enterprise'      => 'مؤسسي',
    'plan_value_trial'           => 'تجريبي',

    // ─── Billing cycle labels (Pass 22) ────────────────────────────────
    // Used by subscription-activated.blade.php to translate the cycle
    // slug (monthly|yearly) interpolated into subscription_activated_billing_cycle.
    'billing_cycle_monthly'      => 'شهري',
    'billing_cycle_yearly'       => 'سنوي',
    'billing_cycle_quarterly'    => 'ربع سنوي',

    // ─── Subscription activated (resources/views/emails/subscription-activated.blade.php) ──
    'subscription_activated_subject' => 'أهلًا بك في :plan — كل شيء جاهز',
    'subscription_activated_heading' => 'أهلًا بك في :plan 🎉',
    'subscription_activated_greeting' => 'مرحبًا،',
    'subscription_activated_body' => 'اشتراكك لـ :workspace على :app نشط الآن. كل ما بنيته أثناء فترة التجربة يبقى محفوظًا — العملاء المحتملون، خطوط الأنابيب، الأتمتة، التكاملات.',
    'subscription_activated_billing_cycle' => 'دورة الفوترة: :cycle.',
    'subscription_activated_button' => 'عرض لوحة الفوترة',
    'subscription_activated_footer' => 'إذا كانت لديك أي أسئلة حول خطتك، رد على هذا البريد وسنتولى الأمر.',

    // ─── Subscription cancelled (resources/views/emails/subscription-cancelled.blade.php) ──
    'subscription_cancelled_subject' => 'تم إلغاء اشتراك :workspace',
    'subscription_cancelled_heading' => 'تم إلغاء اشتراكك',
    'subscription_cancelled_greeting' => 'مرحبًا،',
    'subscription_cancelled_intro'   => 'قمنا بإلغاء اشتراكك لـ :workspace على :app.',
    'subscription_cancelled_ends_at' => 'ستحتفظ بوصول كامل حتى :date. بعد ذلك، سيتم إيقاف مساحة العمل مؤقتًا وستحتاج إلى إعادة التفعيل للاستمرار في استخدامها.',
    'subscription_cancelled_immediate' => 'تم إيقاف الوصول مؤقتًا بأثر فوري.',
    'subscription_cancelled_data_safe' => 'بياناتك — العملاء المحتملون، خطوط الأنابيب، الأتمتة — تبقى آمنة على خوادمنا. إذا غيّرت رأيك خلال 90 يومًا، يمكنك إعادة التفعيل بنقرة واحدة والمتابعة من حيث توقفت.',
    'subscription_cancelled_reason'   => 'السبب المسجل: :reason',
    'subscription_cancelled_button'   => 'إعادة تفعيل الاشتراك',
    'subscription_cancelled_footer'   => 'يؤسفنا رحيلك. إذا كان هناك ما كان يمكننا فعله بشكل أفضل، رد على هذا البريد وأخبرنا.',

    // ─── Subscription expired (resources/views/emails/subscription-expired.blade.php) ──
    'subscription_expired_subject' => 'انتهت صلاحية اشتراك :workspace',
    'subscription_expired_heading' => 'انتهت صلاحية اشتراكك',
    'subscription_expired_greeting' => 'مرحبًا،',
    'subscription_expired_body'    => 'انتهت صلاحية اشتراكك لـ :workspace على :app. تم إيقاف الوصول إلى لوحة الإدارة مؤقتًا، لكن بياناتك لا تزال هنا في انتظارك.',
    'subscription_expired_reactivate' => 'أعد التفعيل عندما تكون مستعدًا للمتابعة من حيث توقفت.',
    'subscription_expired_button'  => 'إعادة تفعيل الاشتراك',
    'subscription_expired_footer'  => 'هل لديك أسئلة حول الفوترة؟ ببساطة رد على هذا البريد.',

    // ─── Trial ending soon (resources/views/emails/trial-ending-soon.blade.php) ──
    'trial_ending_soon_subject_tomorrow' => 'فترة تجربة :workspace تنتهي غدًا',
    'trial_ending_soon_subject_days'     => 'فترة تجربة :workspace تنتهي خلال :days يومًا',
    'trial_ending_soon_heading_one'  => 'تنتهي فترة تجربتك خلال :days يوم',
    'trial_ending_soon_heading_other' => 'تنتهي فترة تجربتك خلال :days أيام',
    'trial_ending_soon_greeting'    => 'مرحبًا،',
    'trial_ending_soon_body'        => 'تذكير ودي — تنتهي فترة التجربة المجانية لـ :workspace على :app في :ends_at. قم بالترقية الآن للحفاظ على جميع عملائك المحتملين وخطوط الأنابيب والأتمتة دون انقطاع.',
    'trial_ending_soon_after'       => 'بعد انتهاء فترة التجربة، سيتم إيقاف الوصول إلى لوحة الإدارة مؤقتًا حتى تختار خطة. لن يتم حذف أي من بياناتك.',
    'trial_ending_soon_button'      => 'اختر خطتك',
    'trial_ending_soon_footer'      => 'لديك أسئلة؟ رد على هذا البريد وسنساعدك في اختيار الخطة المناسبة.',

    // ─── Trial expired (resources/views/emails/trial-expired.blade.php) ──
    'trial_expired_subject' => 'انتهت فترة تجربة :workspace',
    'trial_expired_heading' => 'انتهت فترة تجربتك',
    'trial_expired_greeting' => 'مرحبًا،',
    'trial_expired_body'   => 'انتهت فترة التجربة المجانية لـ :workspace على :app. الوصول إلى لوحة الإدارة متوقف حتى تختار خطة — لكن لا تقلق، جميع عملائك المحتملين والنماذج والإعدادات آمنة.',
    'trial_expired_pick_plan' => 'اختر خطة عندما تكون مستعدًا وستعود إلى الوصول الكامل في ثوانٍ.',
    'trial_expired_button' => 'إعادة تفعيل مساحة العمل',
    'trial_expired_footer' => 'هل تحتاج إلى مساعدة في الاختيار؟ رد على هذا البريد — يسعدنا مساعدتك.',

    // ─── Workspace suspended (resources/views/emails/workspace-suspended.blade.php) ──
    'workspace_suspended_subject' => 'تم تعليق مساحة عمل :workspace',
    'workspace_suspended_heading' => 'تم تعليق مساحة عملك',
    'workspace_suspended_greeting' => 'مرحبًا،',
    'workspace_suspended_body'    => 'تم تعليق مساحة عمل :workspace على :app بعد فترة طويلة من عدم النشاط في أعقاب انتهاء اشتراكك. تم تسجيل خروج جميع الأعضاء من لوحة الإدارة.',
    'workspace_suspended_data_safe' => 'بياناتك آمنة — العملاء المحتملون والنماذج والأتمتة والإعدادات كلها محفوظة. إعادة التفعيل تبعد نقرة واحدة: اختر خطة وسيعود فريقك في ثوانٍ.',
    'workspace_suspended_button'  => 'إعادة تفعيل مساحة العمل',
    'workspace_suspended_footer'  => 'إذا بدا هذا خطأ أو تحتاج إلى مساعدة للعودة، ببساطة رد على هذا البريد وسنحل المشكلة.',

    // ─── Tenant erasure requested (resources/views/emails/tenant-erasure-requested.blade.php) ──
    'tenant_erasure_requested_subject' => 'سيتم حذف مساحة عمل :workspace خلال :days يومًا',
    'tenant_erasure_requested_heading' => 'تم جدولة حذف مساحة العمل',
    'tenant_erasure_requested_greeting' => 'مرحبًا :name،',
    'tenant_erasure_requested_intro'   => 'تلقينا طلبك لحذف مساحة عمل :workspace على :app. ستُمحى بياناتك بشكل دائم — كل عميل محتمل، نموذج، أتمتة، تكامل، وإعداد — خلال :days يومًا. لا يمكن التراجع عن هذا الإجراء بعد انتهاء فترة التهدئة.',
    'tenant_erasure_requested_window'  => 'خلال نافذة :days يومًا، مساحة عملك معلقة — تسجيل الدخول محظور، لكن جميع السجلات محفوظة سليمة في حال غيّرت رأيك. يمكنك إلغاء الحذف في أي وقت قبل انتهاء النافذة من صفحة الخصوصية والبيانات.',
    'tenant_erasure_requested_button'  => 'إلغاء الحذف',
    'tenant_erasure_requested_footer'  => 'لم تطلب هذا؟ انقر على "إلغاء الحذف" أعلاه فورًا واتصل بالدعم — سنقفل مساحة العمل ونحقق في الأمر. هذه الرسالة تستوفي التزامات الإشعار الخاصة بنا بموجب المادة 17 من اللائحة العامة لحماية البيانات (الحق في المحو).',

    // ─── Test email (resources/views/emails/test.blade.php) ──
    'test_subject'    => 'بريد اختبار — :app',
    'test_heading'    => 'اختبار إعدادات البريد الإلكتروني',
    'test_greeting'   => 'مرحبًا :name،',
    'test_body'       => 'هذا بريد اختبار من :app. إذا تلقيت هذه الرسالة، فإن إعدادات بريدك الإلكتروني مكونة بشكل صحيح.',
    'test_continued'  => 'يمكنك الآن إرسال رسائل بريد إلكتروني تحمل علامتك التجارية من مساحة عملك.',
    'test_button'     => 'فتح لوحة التحكم',

    // ─── Invoice send (app/Filament/Resources/InvoiceResource.php) ──
    'invoice_send_subject' => 'فاتورة :number',
    'invoice_send_body'    => "مرحبًا :name،\n\nالفاتورة :number جاهزة: :url\n\nشكرًا لك.",

    // ─── Quote send (app/Filament/Resources/QuoteResource.php) ──
    'quote_send_subject'   => 'عرض سعر :number',
    'quote_send_body'      => "مرحبًا :name،\n\nعرض السعر الخاص بك جاهز: :url\n\nمع التحية،",

    // ─── Quote send for signature (app/Filament/Resources/QuoteResource/Pages/ViewQuote.php) ──
    'quote_send_review_subject' => 'عرض سعر :number — يرجى المراجعة',
    'quote_send_review_body'    => "مرحبًا :name،\n\nعرض السعر الخاص بك جاهز للمراجعة والتوقيع:\n:url\n\nشكرًا لك.",

    // ─── Notification digest (app/Console/Commands/SendNotificationDigest.php) ──
    'digest_subject'                  => 'ملخص إشعارات :app الخاص بك — :datetime',
    'digest_heading'                  => 'ملخص إشعارات :app',
    'digest_intro_lede'               => 'مرحبًا :name، إليك ما فاتك في الساعة الماضية',
    'digest_col_type'                 => 'النوع',
    'digest_col_details'              => 'التفاصيل',
    'digest_col_when'                 => 'الوقت',
    'digest_view_button'              => 'العرض في :app',
    'digest_footer_explainer'         => 'لقد تلقيت هذا لأنك ضبطت الإشعارات على ملخص بالساعة.',
    'digest_manage_preferences_link'  => 'إدارة التفضيلات',
    'digest_fallback_message'         => 'إشعار',

    // ─── Meeting ICS fallbacks (app/Mail/MeetingBookedMail.php, MeetingCancelledMail.php) ──
    'meeting_default_name'   => 'اجتماع',
    'host_default_name'      => 'المضيف',
    'meeting_description'    => 'اجتماع مع :host. لإعادة الجدولة أو الإلغاء: :url',
    // Filename of the .ics attachment buyer sees in their email client.  Use a
    // safe-slug form (no spaces or punctuation other than dash) so all email
    // clients accept the filename unmodified.  Pass-33 i18n fix — without this
    // the English literal "meeting-" prefix leaked into non-EN buyer inboxes.
    'meeting_ics_filename'   => 'meeting',

    // ─── Onboarding drip series (app/Mail/OnboardingDripMail.php) ──
    'drip_day_1_heading'  => 'أهلًا بك على متن السفينة',
    'drip_day_1_body'     => "يسعدنا انضمامك إلينا. أسرع طريقة لمعرفة ما إذا كان نظام إدارة علاقات العملاء هذا يناسب سير عملك هي إضافة عميل محتمل واحد ومتابعته من البريد الوارد حتى الفوز.\n\nيستغرق الأمر حوالي 90 ثانية. انقر أدناه ولنبدأ.",
    'drip_day_1_cta'      => 'إضافة أول عميل محتمل',

    'drip_day_3_heading'  => 'كيف تجده حتى الآن؟',
    'drip_day_3_body'     => "مر يومان. معظم الفرق تتعثر في أحد هذه الأمور:\n\n• إعداد مراحل خط الأنابيب الصحيحة → الإعدادات → خطوط الأنابيب\n• ربط بريدهم الإلكتروني الحالي → الإعدادات → البريد الإلكتروني\n• استيراد العملاء المحتملين من جدول بيانات → العملاء المحتملون → استيراد\n\nإذا واجهت أيًا من هذه (أو شيء آخر)، رد على هذا البريد — نقرأ كل رد.",
    'drip_day_3_cta'      => 'فتح لوحة التحكم',

    'drip_day_5_heading'  => 'الأتمتات الثلاث التي يفعّلها كل فريق في الأسبوع الأول',
    'drip_day_5_body'     => "معظم أنظمة إدارة علاقات العملاء سلبية — يبقى العملاء المحتملون هناك حتى يلاحظهم شخص ما. هذه الأتمتات الثلاث تقوم بالملاحظة نيابة عنك:\n\n1. تعيين العملاء المحتملين الجدد تلقائيًا بالتناوب حتى لا يضيع شيء\n2. إعلام Slack بشأن العملاء المحتملين الواعدين حتى لا يضطر المندوبون إلى التحديث\n3. إعادة التواصل مع العملاء المحتملين الباردين بعد 7 أيام برسالة لطيفة\n\nالثلاثة جميعها تستغرق أقل من 5 دقائق للإعداد.",
    'drip_day_5_cta'      => 'تصفح الأتمتات',

    'drip_day_7_heading'  => 'بعد أسبوع — فحص سريع',
    'drip_day_7_body'     => "كيف يسير الأمر؟\n\nإذا كان نظام إدارة علاقات العملاء يدفع تكلفته بنفسه بالفعل (لقد أضفت عملاء محتملين، فريقك يستخدمه، تغلق صفقات كنت ستخسرها): ممتاز — تتحول فترة التجربة الخاصة بك تلقائيًا إلى خطة مدفوعة عند انتهائها، دون الحاجة إلى أي إجراء.\n\nإذا كنت لا تزال متردداً: رد على هذا البريد بما ينقصك. لقد أطلقنا 14 ميزة بناءً على ملاحظات الإلغاء خلال الأشهر الستة الماضية.",
    'drip_day_7_cta'      => 'عرض الخطط',

    'drip_default_heading' => 'ملاحظة من نظام إدارة علاقات العملاء',
    'drip_default_body'    => 'نأمل أن تجد كل شيء مفيدًا حتى الآن.',
    'drip_default_cta'     => 'فتح لوحة التحكم',

];
