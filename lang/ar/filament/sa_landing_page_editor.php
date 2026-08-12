<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| SuperAdmin LandingPageEditor — Filament admin strings
|------------------------------------------------------------
| Accessed via __('filament/sa_landing_page_editor.<key>').
| Buyers translate or adapt by editing this file or copying it
| to lang/<locale>/filament/sa_landing_page_editor.php.
*/

return [

    // ----- Page title -----
    'title'                       => 'محرر صفحة الهبوط',
    'navigation_label'            => 'صفحة الهبوط',
    'tabs_outer'                  => 'الهبوط',
    'tabs_locale'                 => 'اللغة',

    // ----- Hero tab -----
    'tab_hero'                    => 'العنصر الرئيسي',
    'hero_eyebrow'                => 'محتوى التسويق',
    'hero_eyebrow_placeholder'    => 'مثل: جديد — الفوترة متعددة العملات متاحة الآن',
    'hero_headline'               => 'العنوان الرئيسي',
    'hero_headline_placeholder'   => 'مساحة العملاء المحتملين التي يحتاجها فريقك',
    'hero_headline_highlight'     => 'العبارة المميَّزة (متدرج اللون)',
    'hero_headline_highlight_placeholder' => 'بالفعل',
    'hero_subtext'                => 'العنوان الفرعي',
    'hero_subtext_placeholder'    => 'التقط العملاء المحتملين من أي مصدر…',
    'hero_cta_label'              => 'نص زر الدعوة الرئيسي',
    'hero_cta_label_placeholder'  => 'ابدأ تجربتك المجانية',
    'hero_note'                   => 'ملاحظة الثقة (أسفل الزر)',
    'hero_note_placeholder'       => 'بدون بطاقة ائتمان · يمكن الإلغاء في أي وقت',

    // ----- Features tab -----
    'tab_features'                => 'المزايا',
    'features_headline'           => 'عنوان القسم',
    'features_subtext'            => 'نص فرعي للقسم',
    'feature_cards'               => 'بطاقات المزايا',
    'feature_title'               => 'العنوان',
    'feature_body'                => 'النص',

    // ----- Pricing tab -----
    'tab_pricing'                 => 'نبذة عن التسعير',
    'pricing_headline'            => 'عنوان القسم',
    'pricing_subtext'             => 'نص فرعي للقسم',

    // ----- Final CTA tab -----
    'tab_final_cta'               => 'الدعوة النهائية',
    'cta_headline'                => 'العنوان',
    'cta_subtext'                 => 'النص الفرعي',

    // ----- Stats tab -----
    'tab_stats'                   => 'الإحصائيات',
    'stats_blank_helper'          => 'اتركه فارغًا لاستخدام القيمة الافتراضية المقدّمة في ملفات ترجمة التسويق.',
    'stat3_number_helper'         => 'القيمة الافتراضية هي رمز "∞". غيّرها فقط إذا أردت عرض حدّ أقصى فعلي.',
    'stat1_number'                => 'الإحصائية 1 — الرقم',
    'stat1_label'                 => 'الإحصائية 1 — التسمية',
    'stat2_number'                => 'الإحصائية 2 — الرقم',
    'stat2_label'                 => 'الإحصائية 2 — التسمية',
    'stat3_number'                => 'الإحصائية 3 — الرقم',
    'stat3_label'                 => 'الإحصائية 3 — التسمية',
    'stat4_number'                => 'الإحصائية 4 — الرقم',
    'stat4_label'                 => 'الإحصائية 4 — التسمية',

    // ----- Navigation tab -----
    'tab_navigation'              => 'التنقل',
    'built_in_navbar_description' => 'أوقف أي رابط مدمج لا تريده في شريط التنقل التسويقي. تُدار الصفحات الثابتة المُنشأة ضمن النظام ← الصفحات الثابتة هناك.',
    'show_pricing_link'           => 'إظهار رابط «التسعير»',
    'show_docs_link'              => 'إظهار رابط «الوثائق»',
    'show_sign_in_link'           => 'إظهار رابط «تسجيل الدخول»',
    'registration_legal_description' => 'الموقع الذي يجب أن تشير إليه روابط «شروط الخدمة» و«سياسة الخصوصية» في نموذج /register العام. اتركه فارغًا للارتباط بصفحتي /pages/terms و/pages/privacy الثابتتين اللتين ينشئهما seeder عند أول تثبيت.',
    'register_terms_url'          => 'رابط شروط الخدمة',
    'register_terms_url_helper'   => 'نسبي (مثل /pages/terms) أو مطلق (مثل https://legal.example.com/terms).',
    'register_privacy_url'        => 'رابط سياسة الخصوصية',
    'register_privacy_url_helper' => 'نسبي أو مطلق. يفتح في علامة تبويب جديدة من نموذج التسجيل.',

    // ----- Footer tab -----
    'tab_footer'                  => 'تذييل الصفحة',
    'columns_footer_description'  => 'يمكن إخفاء كل عمود بشكل مستقل. يُستخدم التذييل الغني في النسخة الفاتحة (الافتراضية) من صفحة الهبوط؛ تستخدم النسخ الأبسط تذييلًا مدمجًا أبسط تتحكم به هذه المفاتيح أيضًا.',
    'brand_tagline_column'        => 'عمود العلامة + الشعار',
    'product_links'               => 'روابط المنتج (التسعير / الوثائق / تسجيل الدخول)',
    'static_pages_column'         => 'عمود الصفحات الثابتة (الموارد)',
    'static_pages_column_helper'  => 'روابط للصفحات الثابتة التي وضعت لها علامة «إظهار في التذييل» ضمن النظام ← الصفحات الثابتة.',
    'social_icons_description'    => 'املأ رابط أي أيقونة تريد عرضها. اتركه فارغًا لإخفاء تلك الأيقونة. المفتاح الرئيسي أدناه يلغي الثلاث دفعة واحدة.',
    'show_social_icons'           => 'إظهار أي أيقونات اجتماعية على الإطلاق',
    'social_x_url'                => 'رابط X (Twitter)',
    'social_github_url'           => 'رابط GitHub',
    'social_linkedin_url'         => 'رابط LinkedIn',
    'placeholder_x_url'           => 'https://x.com/yourhandle',
    'placeholder_github_url'      => 'https://github.com/youraccount',
    'placeholder_linkedin_url'    => 'https://linkedin.com/company/yourcompany',
    'social_facebook_url'         => 'رابط Facebook',
    'social_instagram_url'        => 'رابط Instagram',
    'social_youtube_url'          => 'رابط YouTube',
    'placeholder_facebook_url'    => 'https://facebook.com/yourpage',
    'placeholder_instagram_url'   => 'https://instagram.com/yourhandle',
    'placeholder_youtube_url'     => 'https://youtube.com/@yourchannel',

    // ----- SEO tab -----
    'tab_seo'                     => 'SEO',
    'seo_section'                 => 'محرك البحث والميتا الاجتماعية',
    'seo_section_description'     => 'يتحكم في عنوان الصفحة ووصف الميتا والكلمات المفتاحية والصورة التي تظهر عند مشاركة صفحتك الرئيسية على وسائل التواصل الاجتماعي. اترك أي حقل فارغًا للإبقاء على القيمة الافتراضية المدمجة.',
    'seo_meta_title'              => 'عنوان الميتا',
    'seo_meta_title_placeholder'  => 'YourApp — نظام CRM ذاتي الاستضافة تملكه',
    'seo_meta_title_helper'       => 'حتى ~60 حرفًا تقريبًا. يُستخدم كعنوان لعلامة تبويب المتصفح وكعنوان عند المشاركة الاجتماعية. فارغ = اسم تطبيقك.',
    'seo_meta_description'        => 'وصف الميتا',
    'seo_meta_description_placeholder' => 'نظام CRM ذاتي الاستضافة مع مصادر للعملاء المحتملين وأتمتة وتعدّد مستأجرين بعلامة بيضاء.',
    'seo_meta_description_helper' => 'حتى ~160 حرفًا تقريبًا. يظهر أسفل عنوانك في نتائج Google وفي معاينات المشاركة الاجتماعية.',
    'seo_meta_keywords'           => 'الكلمات المفتاحية للميتا',
    'seo_meta_keywords_placeholder' => 'crm, self-hosted crm, lead management',
    'seo_meta_keywords_helper'    => 'مفصولة بفواصل. اختيارية — تتجاهلها معظم محركات البحث، لكن بعض الأدلة تستخدمها.',
    'seo_og_image_url'            => 'رابط صورة المشاركة الاجتماعية',
    'seo_og_image_url_placeholder' => 'https://yourdomain.com/og-image.png',
    'seo_og_image_url_helper'     => 'صورة PNG/JPG بقياس 1200x630 تظهر عند مشاركة صفحتك الرئيسية على وسائل التواصل الاجتماعي (Open Graph / بطاقة Twitter). فارغ = شعار علامتك التجارية.',
    'footer_tagline'              => 'الشعار (داخل عمود العلامة)',
    'footer_tagline_placeholder'  => 'نظام CRM ذاتي الاستضافة تملكه بالفعل.',
    'footer_copyright'            => 'سطر حقوق النشر (الشريط السفلي)',
    'footer_copyright_placeholder'=> 'الافتراضي: © :year YourAppName. جميع الحقوق محفوظة.',
    'footer_copyright_helper'     => 'نص عادي أو HTML. اتركه فارغًا للافتراضي.',

    // ----- Translations tab -----
    'tab_translations'            => 'الترجمات',
    'per_language_overrides_description' => 'تُحرَّر الإنجليزية في التبويبات أعلاه. املأ كل لغة غير إنجليزية هنا لعرض نص مترجم للزوار بتلك اللغة. تعود الحقول الفارغة إلى الإنجليزية تلقائيًا.',
    'tr_hero_headline'            => 'عنوان العنصر الرئيسي',
    'tr_hero_headline_highlight'  => 'العبارة المميَّزة في العنصر الرئيسي',
    'tr_hero_subtext'             => 'النص الفرعي للعنصر الرئيسي',
    'tr_hero_cta_label'           => 'نص زر الدعوة في العنصر الرئيسي',
    'tr_hero_note'                => 'ملاحظة العنصر الرئيسي (سطر الثقة)',
    'tr_hero_eyebrow'             => 'تسمية العنصر الرئيسي / الشارة',
    'tr_features_headline'        => 'عنوان المزايا',
    'tr_features_subtext'         => 'النص الفرعي للمزايا',
    'tr_pricing_headline'         => 'عنوان التسعير',
    'tr_pricing_subtext'          => 'النص الفرعي للتسعير',
    'tr_cta_headline'             => 'عنوان الدعوة الختامية',
    'tr_cta_subtext'              => 'النص الفرعي للدعوة الختامية',
    'tr_footer_tagline'           => 'شعار التذييل',

    // ----- Save notification -----
    'saved_title'                 => 'تم حفظ محتوى صفحة الهبوط.',
    'saved_body'                  => 'زر صفحة الهبوط للتحقق من تغييراتك.',

    // ----- Header actions -----
    'preview'                     => 'معاينة صفحة الهبوط',
    'save_changes'                => 'حفظ التغييرات',

    // ----- Hero -----
    'page_hero_title'             => 'أعد صياغة صفحة الهبوط العامة دون لمس الكود',
    'hero_body_html'              => 'يُعرَض كل حقل أدناه مباشرة على صفحة الهبوط العامة في <code class="lpe-code">/</code>. اترك حقلًا فارغًا للعودة إلى النص الافتراضي المدمج، بحيث يظل موقعك مكتملًا حتى أثناء التحرير.',
];
