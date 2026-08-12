<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| BillingPortal — Filament tenant strings (ar)
|------------------------------------------------------------
| Accessed via __('filament/billing_portal.<key>').
*/

return [
    'title'                          => 'الفوترة والاشتراك',
    'heading'                        => 'الفوترة والاشتراك',
    'navigation_label'               => 'الفوترة',

    // Subscription state subheadings
    'subheading_on_trial'            => 'أنت في فترة تجريبية مجانية. يمكنك الترقية في أي وقت — تُنقل بياناتك معك.',
    'subheading_active_paid'         => 'اشتراكك نشط.',
    'subheading_trial_expired'       => 'انتهت فترتك التجريبية. اختر خطة للمتابعة.',
    'subheading_expired'             => 'انقضى اشتراكك. جدّده لاستعادة الوصول الكامل.',
    'subheading_cancelled'           => 'تم إلغاء اشتراكك. يمكنك إعادة تنشيطه في أي وقت.',
    'subheading_suspended'           => 'تم تعليق مساحة العمل هذه. تواصل مع الدعم.',

    // View data defaults
    'state_unknown_label'            => 'غير معروف',

    // Deletion flow
    'type_delete_title'              => 'اكتب DELETE للتأكيد.',
    'type_delete_body'               => 'حذف مساحة العمل عملية مدمّرة. اكتب كلمة DELETE في حقل التأكيد للمتابعة.',
    'no_workspace_title'             => 'لا يوجد سياق لمساحة العمل.',
    'auth_required_title'            => 'المصادقة مطلوبة.',
    'owner_only_title'               => 'للمالك فقط.',
    'owner_only_body'                => 'يمكن لمالك مساحة العمل فقط جدولة الحذف.',
    'password_mismatch_title'        => 'كلمة المرور غير متطابقة.',
    'password_mismatch_body'         => 'أعد إدخال كلمة مرور حسابك لتأكيد حذف مساحة العمل.',
    'totp_mismatch_title'            => 'رمز المصادقة الثنائية غير متطابق.',
    'totp_mismatch_body'             => 'أدخل رمزاً جديداً مكوّناً من 6 أرقام من تطبيق المصادقة.',
    'deletion_scheduled_title'       => 'تمت جدولة حذف مساحة العمل',
    'deletion_scheduled_body'        => 'ستُحذف مساحة العمل هذه نهائياً خلال :days يوماً. لديك حتى ذلك الوقت لإلغاء الحذف من هذه الصفحة أو من صفحة الخصوصية والبيانات.',
    'deletion_cancelled_title'       => 'تم إلغاء الحذف',
    'deletion_cancelled_body'        => 'ستظل مساحة عملك نشطة.',

    // Billing details save
    'review_details_title'           => 'يرجى مراجعة بيانات الفوترة.',
    'review_details_default_body'    => 'بعض الحقول غير صالحة.',
    'billing_country_regex'          => 'يجب أن تكون الدولة رمز ISO-3166-1 alpha-2 (مثل US، DE، GB).',
    'no_changes_title'               => 'لا توجد تغييرات للحفظ.',
    'billing_details_saved_title'    => 'تم حفظ بيانات الفوترة.',
    'billing_details_saved_body'     => 'ستظهر هذه في كل إيصال مستقبلي.',

    // Subscription event descriptors
    'event_trial_ends'               => 'انتهاء الفترة التجريبية',
    'event_next_renewal'             => 'التجديد التالي',
    'event_trial_ended'              => 'انتهت الفترة التجريبية',
    'event_subscription_ended'       => 'انتهى الاشتراك',
    'event_access_ends'              => 'انتهاء الوصول',

    // Gateway labels
    'gateway_stripe'                 => 'Stripe',
    'gateway_paypal'                 => 'PayPal',
    'gateway_razorpay'               => 'Razorpay',
    'gateway_paystack'               => 'Paystack',
    'gateway_manual'                 => 'تحويل بنكي',

    // ─── Blade view — billing portal page ─────────────────────────────
    'error_no_workspace'             => 'لم نتمكّن من تحديد مساحة عملك. يرجى تسجيل الخروج ثم تسجيل الدخول مجدداً.',
    'cta_choose_plan'                => 'اختر خطة',
    'section_current_plan'           => 'الخطة الحالية',
    'price_free'                     => 'مجاني',
    'seat_team_seats'                => 'مقاعد الفريق',
    'seat_limit_reached'             => 'لقد وصلت إلى الحد الأقصى للمقاعد. قم بالترقية لدعوة المزيد من الأعضاء.',
    'features_whats_included'        => 'ما هو مُتضمَّن',

    // ─── Feature labels (Pass 22) ─────────────────────────────────────
    'feature_integrations'           => 'التكاملات',
    'feature_automations'            => 'الأتمتة',
    'feature_api_access'             => 'الوصول إلى API',
    'feature_custom_domain'          => 'نطاق مخصص',
    'feature_white_label'            => 'علامة بيضاء',
    'feature_webhooks_outbound'      => 'Webhooks صادرة',
    'feature_reports_advanced'       => 'تقارير متقدمة',
    'feature_priority_support'       => 'دعم بأولوية',
    'feature_marketplace_install'    => 'تثبيتات من السوق',
    'feature_team_collaboration'     => 'التعاون الجماعي',
    'feature_unlimited_leads'        => 'عملاء محتملون غير محدودين',
    'feature_sso'                    => 'تسجيل دخول موحّد',
    'no_plan_information'            => 'لا تتوفر معلومات الخطة.',
    'section_manage_subscription'    => 'إدارة الاشتراك',
    'gateway_paying_via_prefix'      => 'الدفع عبر',
    'action_change_plan'             => 'تغيير الخطة',
    'action_update_payment_method'   => 'تحديث طريقة الدفع والفواتير',
    'action_cancel_subscription'     => 'إلغاء الاشتراك',
    'support_hint'                   => 'هل تحتاج إلى مساعدة؟ تواصل مع الدعم — سنتولّى تغييرات الفوترة نيابةً عنك خلال 24 ساعة.',
    'section_recent_activity'        => 'النشاط الأخير',
    'event_subscription_activated'   => 'تم تنشيط الاشتراك',
    'event_subscription_cancelled'   => 'تم إلغاء الاشتراك',
    'event_payment_failed'           => 'فشل الدفع',
    'event_plan_changed'             => 'تم تغيير الخطة',
    'event_notification_sent'        => 'تم إرسال إشعار',
    'event_workspace_suspended'      => 'تم تعليق مساحة العمل',
    'event_workspace_reactivated'    => 'تم إعادة تفعيل مساحة العمل',
    'event_auto_suspended'           => 'تعليق تلقائي (بعد انتهاء الصلاحية)',
    'section_available_plans'        => 'الخطط المتاحة',
    'toggle_monthly'                 => 'شهري',
    'toggle_annual'                  => 'سنوي',
    'toggle_annual_save_badge'       => 'وفّر حتى 20%',
    'plan_tag_recommended'           => 'موصى به',
    'plan_tag_current'               => 'الحالي',
    'price_suffix_per_month'         => '/شهر',
    'price_suffix_per_year'          => '/سنة',
    'plan_save_vs_monthly'           => 'وفّر :pct% مقارنةً بالشهري',
    'preview_upgrade_strong'         => 'بدّل اليوم، وادفع الفرق فقط:',
    'preview_charge_now_label'       => 'الفاتورة الآن:',
    'preview_credit_applied_label'   => 'الرصيد المُطبَّق:',
    'preview_prorated_days_one'      => ':count يوم من الخطة الحالية',
    'preview_prorated_days_other'    => ':count يوم من الخطة الحالية',
    'preview_downgrade_strong'       => 'تخفيض مع رصيد:',
    'preview_account_credit_label'   => 'رصيد الحساب:',
    'preview_applied_next_invoice'   => 'سيُطبَّق على فاتورتك التالية تلقائياً.',
    'plan_action_switch'             => 'تبديل',
    'plan_action_switch_annual'      => 'تبديل (سنوي)',
    'plan_active_label'              => 'نشط',
    'section_billing_details'        => 'بيانات الفوترة',
    'billing_details_hint'           => 'تُستخدَم في كل ملف PDF للإيصال. تتطلبها فرق الحسابات الدائنة / المشتريات في معظم الولايات القضائية.',
    'form_business_name_label'       => 'الاسم التجاري المسجَّل',
    'form_vat_number_label'          => 'رقم ضريبة القيمة المضافة / GST',
    'form_country_label'             => 'الدولة (ISO-3166-1 alpha-2)',
    'form_country_placeholder'       => 'US',
    'form_billing_address_label'     => 'عنوان الفوترة',
    'form_billing_email_label'       => 'بريد الفوترة الإلكتروني (صندوق الحسابات الدائنة / المحاسبة)',
    'form_billing_email_placeholder' => 'ap@example.com',
    'form_save_button'               => 'حفظ بيانات الفوترة',

    // ─── Connector + interval fallback ───
    'of_connector'                   => 'من',
    'interval_month_fallback'        => 'شهر',

    // ─── Interval labels (slug→localized) ─────────────────────────────
    'interval_month'                 => 'شهر',
    'interval_year'                  => 'سنة',
    'interval_week'                  => 'أسبوع',
    'interval_day'                   => 'يوم',
];
