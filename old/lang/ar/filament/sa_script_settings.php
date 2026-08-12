<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| SuperAdmin ScriptSettings — سلاسل Filament admin
|------------------------------------------------------------
| الوصول عبر __('filament/sa_script_settings.<key>').
| يمكن للمشترين الترجمة أو التكييف بتحرير هذا الملف أو نسخه إلى
| lang/<locale>/filament/sa_script_settings.php.
*/

return [

    // ----- عنوان الصفحة / التنقل -----
    'title'                            => 'إعدادات السكربت',
    'navigation_label'                 => 'إعدادات السكربت',

    // ----- قسم التقارير والتعريب -----
    'reporting_section_description'    => 'القيم الافتراضية المستخدمة في لوحة الفوترة وصفحة التسعير وأي مكان لم يختر فيه المستأجر قيمته الخاصة. تُخزَّن في حمولة قاعدة بيانات Spatie Laravel Settings — تصمد أمام إعادة كتابة ملف .env وتسري فور الحفظ (لا حاجة لمسح الذاكرة المؤقتة).',
    'reporting_currency_label'         => 'عملة التقارير',
    'reporting_currency_helper'        => 'تُستخدم في أرقام MRR / ARR / ARPU في لوحة الفوترة.',
    'default_timezone_label'           => 'المنطقة الزمنية الافتراضية',
    'date_format_label'                => 'تنسيق التاريخ',
    'default_language_label'           => 'اللغة الافتراضية',
    'default_language_helper'          => 'تحدد لغة الواجهة الافتراضية للزوار الجدد. المرفقة: الإنجليزية، العربية (RTL)، الإسبانية، الهندية.',

    // تسمية الملاذ الأخير عند فراغ config('locales.supported') —
    // لا يزال يلزم خيار Select واحد على الأقل، لذلك نلجأ إلى
    // 'en' => __('locale_fallback_english_native'). تبقى "English" في
    // الإعداد المحلي en نفسه، وذلك بقصد ذاتي مرجعي.
    'locale_fallback_english_native'   => 'English',

    // ----- قسم النسخ الاحتياطي -----
    'backups_section_description'      => 'نسخ احتياطي ليلي تلقائي لقاعدة البيانات والملفات المرفوعة. تُخزَّن النسخ الاحتياطية في storage/app/backups/.',
    'auto_nightly_backup_label'        => 'تمكين النسخ الاحتياطي الليلي التلقائي',
    'auto_nightly_backup_helper'       => 'عند التمكين، يتم إنشاء نسخة احتياطية جديدة كل ليلة في الساعة 02:00 UTC عبر مشغل المهام المجدولة (cron.php).',

    // ----- قسم صفحة الهبوط التسويقية العامة -----
    'landing_section_description'      => 'أي صفحة هبوط يراها الزوار على عنوان URL الجذر (/). يسري التغيير بعد الحفظ — لا حاجة لمسح الذاكرة المؤقتة.',
    'landing_template_label'           => 'قالب صفحة الهبوط',
    'landing_template_helper'          => 'عاين أي متغير عبر /?preview=1 أثناء تسجيل الدخول كمسؤول أعلى. يسري التغيير فور الحفظ — لا حاجة لمسح الذاكرة المؤقتة.',
    'landing_variant_light'            => 'فاتح — بطل تسويقي أبيض مصقول (افتراضي)',
    'landing_variant_warm'             => 'دافئ — خلفية كريمية، عرض serif، إحساس مجلة تحريرية',
    'landing_variant_modern'           => 'حديث — قالب داكن، تدرج بنفسجي/وردي',
    'landing_variant_editorial'        => 'تحريري — أحادي اللون داكن، bento بأسلوب post-Linear',
    'landing_variant_classic'          => 'كلاسيكي — قالب أصلي غني بالمحتوى',

    // ----- قسم البريد الصادر / SMTP -----
    'smtp_section_description'         => 'الإعدادات الافتراضية للبريد على مستوى السكربت المكتوبة في ملف .env الخاص بك (يُشغَّل config:clear عند الحفظ). ترتيب الأولوية وقت الإرسال: (1) SMTP الخاص بالمستأجر من إعدادات البريد ← (2) قيم .env على مستوى السكربت ← (3) محرك "log" الافتراضي الثابت في Laravel. يلجأ المستأجرون الذين يتركون صفحتهم فارغة إلى هذه القيم الافتراضية تلقائياً.',
    'mailer_label'                     => 'Mailer',
    'mailer_helper'                    => 'أبقِ هذا على "Log" أثناء اختبارك. انتقل إلى SMTP بمجرد عمل بيانات الاعتماد أدناه.',
    'mailer_option_smtp'               => 'SMTP',
    'mailer_option_sendmail'           => 'sendmail',
    'mailer_option_log'                => 'Log (التطوير فقط — لا يُرسَل بريد حقيقي)',
    'mailer_option_array'              => 'Array (اختبار — يتجاهل البريد)',
    'smtp_host_label'                  => 'مضيف SMTP',
    'smtp_host_placeholder'            => 'smtp.example.com',
    'smtp_port_label'                  => 'منفذ SMTP',
    'smtp_port_placeholder'            => '587 (STARTTLS)، 465 (SSL)، 25 (غير مشفر)',
    'encryption_label'                 => 'التشفير',
    'encryption_option_tls'            => 'TLS (STARTTLS — موصى به)',
    'encryption_option_ssl'            => 'SSL',
    'encryption_option_none'           => 'بدون',
    'smtp_username_label'              => 'اسم مستخدم SMTP',
    'smtp_password_label'              => 'كلمة مرور SMTP',
    'smtp_password_placeholder'        => 'اتركها دون تغيير للحفاظ على الحالية',
    'from_address_label'               => 'عنوان المُرسِل',
    'from_address_placeholder'         => 'noreply@yourdomain.com',
    'from_name_label'                  => 'اسم المُرسِل',
    'from_name_placeholder'            => 'LeadHub',

    // ----- الإشعارات -----
    'settings_saved_title'             => 'تم حفظ إعدادات السكربت.',
    'settings_saved_body'              => 'تم تحديث القيم الافتراضية للتقارير والبريد. إذا غيّرت Mailer، أرسل بريد اختبار للتأكد من عمله.',
    'no_user_title'                    => 'لا يوجد مستخدم مصادق عليه.',
    'mailer_dry_warning_title'         => 'Mailer الحالي هو ":mailer" — لن يُرسَل بريد حقيقي.',
    'mailer_dry_warning_body'          => 'انتقل إلى SMTP واحفظ قبل الاختبار.',
    'test_email_sent_title'            => 'تم إرسال بريد الاختبار إلى :recipient',
    'test_email_sent_body'             => 'تحقق من صندوق الوارد (والمزعج) في الدقيقة التالية.',
    'test_send_failed_title'           => 'فشل الإرسال: :error',
    'test_send_failed_body'            => 'تحقق مرة أخرى من المضيف والمنفذ والتشفير وبيانات الاعتماد.',

    // ----- نص بريد الاختبار -----
    'test_email_body_intro'            => 'هذه رسالة اختبار من صفحة إعدادات السكربت الخاصة بـ :app.',
    'test_email_body_confirmation'     => 'إذا استلمت هذه الرسالة، فإن بيانات اعتماد SMTP الخاصة بك تعمل.',
    'test_email_body_sent_at'          => 'أُرسِلت في: :timestamp UTC',
    'test_email_subject'               => '[:app] بريد اختبار SMTP',

    // ----- إجراءات الترويسة -----
    'action_send_test'                 => 'إرسال بريد اختبار',
    'action_send_test_tooltip'         => 'يستخدم القيم الموجودة حالياً في النموذج — اختبر أولاً، احفظ إذا نجح.',
    'action_send_test_recipient_label' => 'إرسال بريد اختبار إلى',
    'action_send_test_recipient_helper' => 'أي عنوان — قم بالتجاوز للتحقق من التسليم إلى صندوق الإنتاج قبل اعتماد بيانات الاعتماد.',
    'action_send_test_modal_submit'    => 'إرسال اختبار',
    'action_save_settings'             => 'حفظ الإعدادات',

    // ----- البطل -----
    'page_hero_title'                  => 'القيم الافتراضية الشاملة لنشر LeadHub الخاص بك',
    'hero_eyebrow'                     => 'إعدادات مالك السكربت',
    'hero_subtitle'                    => 'اختر عملة التقارير للوحة الفوترة، والمنطقة الزمنية الافتراضية، واللغة التي يبدأ بها كل مساحة عمل جديدة.',

    // ----- شرح المجدول / cron -----
    'cron_details_summary'             => 'ماذا يشغّل المجدول؟',
    'cron_desc'                        => 'قم بتكوين المجدول البعيد الخاص بك لتنفيذ GET على هذا العنوان كل دقيقة:',

    // ─── عنوان قسم Cron ومقدمته ───
    'cron_section_title'               => 'مجدول المهام في الخلفية (cron)',
    'cron_section_desc_html'           => 'يشغّل LeadHub عدة مهام في الخلفية كل دقيقة — إرسال البريد، والأتمتة، والتقارير المجدولة، وتقييم العملاء المحتملين، وتذكيرات المهام، وسحب صندوق IMAP، وفحوصات دورة حياة الاشتراك، والنسخ الاحتياطية الليلية. تعتمد كلها على محفز cron يطرق <code class="ss-chip">cron.php</code> أو يشغّل <code class="ss-chip">artisan schedule:run</code> مرة كل دقيقة. اختر الخيار الذي يدعمه خادمك.',

    // ─── قائمة تفاصيل المجدول (الفاصل ← الوصف) ───
    'cron_list_every_5_min_label'      => 'كل 5 دقائق',
    'cron_list_every_5_min_desc'       => 'سحب صندوق IMAP، وتذكيرات المهام',
    'cron_list_every_15_min_label'     => 'كل 15 دقيقة',
    'cron_list_every_15_min_desc'      => 'موزِّع خطوات تسلسلات البريد',
    'cron_list_every_hour_label'       => 'كل ساعة',
    'cron_list_every_hour_desc'        => 'أتمتات بدون نشاط، تسليم التقارير المجدولة، تنبيهات المرشحات المحفوظة',
    'cron_list_every_6_hours_label'    => 'كل 6 ساعات',
    'cron_list_every_6_hours_desc'     => 'دورة حياة الاشتراك (انتهاء التجارب، تذكيرات فترة السماح)',
    'cron_list_daily_02_label'         => 'يومياً في 02:00',
    'cron_list_daily_02_desc'          => 'النسخ الاحتياطي لقواعد بيانات المستأجرين (عند التمكين)',
    'cron_list_daily_09_label'         => 'يومياً في 09:00',
    'cron_list_daily_09_desc'          => 'فحص تحديث LeadHub',
    'cron_list_daily_user_label'       => 'يومياً في الساعة التي يحددها المستخدم',
    'cron_list_daily_user_desc'        => 'ملخص الإشعارات',

    // ─── Cron الخيار A: استضافة مشتركة ───
    'cron_option_a_label'              => 'استضافة مشتركة (cPanel / Plesk / DirectAdmin)',
    'cron_option_a_tag'                => 'الأسهل',
    'cron_option_a_desc_html'          => 'في لوحة الاستضافة الخاصة بك، افتح <em>مهام Cron</em> وأضف مهمة تعمل كل دقيقة:',
    'cron_option_a_hint_html'          => 'تحجب بعض الاستضافات المشتركة <code class="ss-chip-light">exec()</code> — استخدم الخيار B بدلاً من ذلك إذا فشل ما سبق صامتاً.',

    // ─── Cron الخيار B: cron قائم على URL ───
    'cron_option_b_label'              => 'cron قائم على URL (cron-job.org، EasyCron، UptimeRobot)',
    'cron_option_b_secret_hint_html'   => 'يجب أن يطابق المعامل <code class="ss-chip-light">secret=</code> قيمة <code class="ss-chip-light">CRON_SECRET</code> في <code class="ss-chip-light">.env</code> — هذا يمنع أي شخص آخر من تشغيل مجدولك.',
    'cron_option_b_warn_html'          => '<strong>تنبيه:</strong> <code class="ss-chip-warn">CRON_SECRET</code> غير معيَّن — محفز URL الخاص بك مفتوح للإنترنت. أضف سراً عشوائياً من 32 حرفاً إلى <code class="ss-chip-warn">.env</code> وألحق <code class="ss-chip-warn">?secret=…</code> بالعنوان أعلاه.',

    // ─── Cron الخيار C: VPS / مخصص (مجدول Laravel الأصلي) ───
    'cron_option_c_label'              => 'VPS / مخصص (مجدول Laravel الأصلي)',
    'cron_option_c_tag'                => 'موصى به لـ VPS',
    'cron_option_c_desc_html'          => 'اتصل عبر SSH كمستخدم الويب الخاص بك ونفّذ <code class="ss-chip-light">crontab -e</code> ثم أضف:',
    'cron_option_c_hint_html'          => 'هذا يتجاوز <code class="ss-chip-light">cron.php</code> ويستخدم مجدول Laravel الأصلي — أقل عبء، الأفضل لـ VPS أو الخوادم المخصصة بقوائم Redis.',

    // ─── زر نسخ Cron + التحقق ───
    'cron_copy'                        => 'نسخ',
    'cron_copied'                      => 'تم النسخ',
    'cron_verify_text_html'            => '<strong>تحقق من عمله:</strong> انتظر دقيقتين بعد حفظ cron، ثم تحقق من صفحة "قائمة الانتظار والعاملون" (داخل أي مساحة عمل مستأجر) — إذا كانت المهام تتدفق، فسيُظهر الاتصال طابعاً زمنياً حديثاً. يمكنك أيضاً تشغيل <code class="ss-chip-success">php artisan schedule:list</code> من SSH لرؤية كل مهمة مسجلة.',
];
