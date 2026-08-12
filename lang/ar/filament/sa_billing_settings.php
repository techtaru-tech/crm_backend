<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| SuperAdmin BillingSettingsPage — Filament admin strings
|------------------------------------------------------------
*/

return [

    // ----- Page title -----
    'title'                       => 'بوابة الدفع وإعدادات دورة الحياة',
    'navigation_label'            => 'الدفع ودورة الحياة',
    'tabs_outer'                  => 'البوابات',

    // ----- One-time cron setup -----
    'cron_section_heading'        => '⚙️ إعداد الخادم لمرة واحدة',
    'cron_section_description'    => 'لكي تعمل تذكيرات الفترة التجريبية ورسائل انتهاء الصلاحية والنسخ الاحتياطي اليومي فعلياً، يحتاج خادمك إلى إدخال cron واحد. أضفه مرة واحدة وستصبح كل مهمة مجدولة في السكربت نشطة تلقائياً.',
    'cron_setup_step_label'       => 'أضف هذا السطر إلى مهام Cron في لوحة الاستضافة (cPanel / Plesk / DirectAdmin)، أو إلى crontab الخادم:',
    'cron_setup_step_thats_it'    => 'هذا كل شيء — اضبطه مرة واحدة وانساه. السكربت يتولى كل شيء آخر داخلياً (متى ترسَل كل رسالة، متى تنتهي الفترات التجريبية، متى تُؤخذ النسخ الاحتياطية، إلخ).',
    'cron_setup_step_support'     => 'إذا لم تكن متأكداً من كيفية إضافة إدخال cron، فإن دعم مزود الاستضافة الخاص بك يمكنه القيام بذلك نيابة عنك في أقل من دقيقة — فقط أرسل لهم السطر أعلاه.',

    // ----- Trial & lifecycle section -----
    'trial_lifecycle_description' => 'يتحكم في مدة الفترات التجريبية، ومتى تُرسَل التذكيرات قبل / بعد انتهاء الصلاحية، وما إذا كان المستأجرون منتهية صلاحياتهم يُعلَّقون تلقائياً بعد فترة سماح. يقرأ ProcessSubscriptionLifecycle هذه القيم في كل تشغيل cron — دون الحاجة إلى إعادة تشغيل التطبيق.',
    'trial_days_label'            => 'المدة الافتراضية للفترة التجريبية (أيام)',
    'trial_days_suffix'           => 'أيام',
    'trial_days_helper'           => 'تُستخدَم عندما لا يحتوي الخطة المختارة على trial_days خاص بها. لا تزال كل خطة قادرة على تجاوز هذا عبر صفحة الخطط.',
    'trial_reminder_days_label'   => 'إيقاع تذكيرات الفترة التجريبية (أيام قبل انتهاء الفترة)',
    'reminder_placeholder'        => 'اكتب عدد الأيام واضغط Enter',
    'trial_reminder_days_helper'  => 'مثلاً 7، 3، 1 ← رسائل تُرسَل قبل 7 و3 و1 يوم من trial_ends_at. كل عدد صحيح هو تذكير. فارغ = بدون تذكيرات.',
    'post_expiry_reminder_days_label' => 'إيقاع التنقيط بعد الانتهاء (أيام بعد الانتهاء)',
    'post_expiry_reminder_days_helper' => 'مثلاً 3، 7، 14 ← رسائل تنقيط تُرسَل بعد 3 و7 و14 يوماً من الانتهاء لاستعادة المستأجرين المنتهية صلاحياتهم. فارغ = بدون تنقيط.',
    'auto_suspend_after_label'    => 'التعليق التلقائي بعد (أيام بعد الانتهاء)',
    'auto_suspend_after_helper'   => '0 = لا تعليق تلقائي أبداً. وإلا، يتم تحويل المستأجرين منتهية صلاحياتهم إلى active=false بعد هذا العدد من الأيام من تاريخ انتهائهم وتُرسَل لهم رسالة إشعار نهائية.',

    // ----- Enabled gateways section -----
    'enabled_gateways_description'=> 'فقط البوابات التي تحددها هنا (والتي تحتوي على بيانات اعتماد مكتملة أدناه) ستُعرَض على المستأجرين في صفحة التسعير.',
    'field_enabled_gateways_label'=> 'البوابات المُفعَّلة',
    'gateway_stripe'              => 'Stripe (بطاقة)',
    'gateway_paypal'              => 'PayPal',
    'gateway_razorpay'            => 'Razorpay',
    'gateway_paystack'            => 'Paystack',
    'gateway_manual'              => 'تحويل بنكي (يدوي)',

    // ----- Stripe tab -----
    'tab_stripe'                  => 'Stripe',
    'test_mode'                   => 'وضع الاختبار',
    'stripe_publishable_key'      => 'المفتاح العام',
    'stripe_secret_key'           => 'المفتاح السري',
    'webhook_signing_secret'      => 'سر توقيع Webhook',
    'stripe_webhook_helper'       => 'اختياري لكن يُوصى به. وجّه Webhook الخاص بـ Stripe إلى :url',

    // ----- PayPal tab -----
    'tab_paypal'                  => 'PayPal',
    'sandbox_mode'                => 'وضع Sandbox',
    'paypal_client_id'            => 'معرف العميل',
    'paypal_client_secret'        => 'سر العميل',
    'paypal_webhook_id'           => 'معرف Webhook',
    'paypal_webhook_helper'       => 'نقطة نهاية Webhook: :url',

    // ----- Razorpay tab -----
    'tab_razorpay'                => 'Razorpay',
    'razorpay_key_id'             => 'معرف المفتاح',
    'razorpay_key_secret'         => 'سر المفتاح',
    'razorpay_webhook_secret'     => 'سر Webhook',
    'razorpay_webhook_helper'     => 'نقطة نهاية Webhook: :url',

    // ----- Paystack tab -----
    'tab_paystack'                => 'Paystack',
    'paystack_public_key'         => 'المفتاح العام',
    'paystack_secret_key'         => 'المفتاح السري',

    // ----- Manual bank transfer tab -----
    'tab_manual_bank'             => 'تحويل بنكي يدوي',
    'manual_bank_name'            => 'اسم البنك',
    'manual_account_name'         => 'اسم الحساب',
    'manual_account_number'       => 'رقم الحساب',
    'manual_iban'                 => 'IBAN',
    'manual_swift'                => 'SWIFT / BIC',
    'manual_extra_instructions'   => 'تعليمات إضافية',
    'manual_extra_helper'         => 'تظهر في صفحة تعليمات التحويل بعد أن يختار المستأجر هذه البوابة.',

    // ----- Notifications -----
    'settings_saved'              => 'تم حفظ الإعدادات.',
    'no_gateway_configured'       => 'لا توجد بوابة مُفعَّلة ومكتملة الإعداد بعد.',
    'active_gateways'             => 'البوابات النشطة: :labels',
    'stripe_mismatch_title'       => 'عدم تطابق وضع اختبار Stripe',
    'stripe_mismatch_body'        => 'مفتاح «وضع الاختبار» مضبوط على :toggle لكن بادئة المفتاح السري تشير إلى :prefix. يقوم Stripe بالتوجيه عبر بادئة المفتاح، وليس عبر المفتاح — اقلب أحدهما ليتطابق الاثنان.',
    'toggle_on'                   => 'تشغيل',
    'toggle_off'                  => 'إيقاف',

    // ----- Header actions -----
    'save_settings'               => 'حفظ الإعدادات',

    // ----- Hero -----
    'hero_eyebrow'                => 'الفوترة',
    'hero_title'                  => 'تكوين بوابات الدفع',
    'body_intro'                  => 'اطرح بوابة واحدة أو أكثر دفعةً واحدة. يختار كل مستأجر طريقته المفضلة في صفحة التسعير. تغطي Stripe وPayPal معظم حركة المرور العالمية؛ وRazorpay وPaystack ممتازتان للهند وأفريقيا على التوالي؛ ويعمل التحويل البنكي اليدوي في أي مكان وهو مثالي للفوترة المؤسسية.',

    // Affiliate program
    'affiliate_section_heading'     => 'برنامج الإحالة',
    'affiliate_section_description' => 'العمولة المدفوعة للمستأجرين الذين يُحيلون مساحات عمل مدفوعة جديدة. تُطبَّق على كل دفعة متكررة تقوم بها مساحة عمل مُحالة؛ راجِع العمولات وادفعها من الفوترة ← عمولات الإحالة.',
    'affiliate_commission_label'    => 'نسبة العمولة',
    'affiliate_commission_helper'   => 'النسبة المئوية من كل دفعة مُحالة تُسجَّل كعمولة إحالة. اضبطها على 0 لتعطيل برنامج الإحالة.',
];
