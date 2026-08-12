<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Filament — LandingPageResource translation strings
|--------------------------------------------------------------------------
|
| Labels, helper texts, placeholders and tab/section copy for the
| Landing Pages resource at /admin/landing-pages.
| Consumed via __('filament/landing_pages.<key>').
|
| Keys are snake_case; grouped by purpose-comment headers.
|
*/

return [

    // ─── Navigation ──────────────────────────────────────────────────
    'nav_label'                         => 'صفحات الهبوط',

    // ─── Model labels (breadcrumbs / page titles) ─────────────────────
    'model_label'                       => 'صفحة هبوط',
    'plural_model_label'                => 'صفحات الهبوط',

    // ─── Top-level tabs ─────────────────────────────────────────────
    'landing_page'                   => 'صفحة هبوط',
    'tab_basics'                     => 'الأساسيات',
    'tab_sections'                   => 'الأقسام',
    'tab_appearance'                 => 'المظهر',
    'tab_integration'                => 'التكامل',
    'tab_advanced'                   => 'متقدم',

    // ─── Basics tab ────────────────────────────────────────────────
    'internal_name'                  => 'الاسم الداخلي',
    'slug'                           => 'المُعرّف الكنوي',
    'slug_help_with_workspace'       => 'يُستخدم في الرابط العام: :url',
    'slug_help_generic'              => 'يُستخدم في الرابط العام: /{workspace}/{slug}',
    'browser_title'                  => 'عنوان المتصفح',
    'meta_description'               => 'وصف الميتا',
    'og_image_url'                   => 'رابط صورة Open Graph',
    'favicon_url'                    => 'رابط Favicon',
    'status'                         => 'الحالة',

    // ─── Sections tab — repeater ───────────────────────────────────
    'add_section'                    => 'إضافة قسم',
    'new_section'                    => 'قسم جديد',
    'section_type'                   => 'نوع القسم',
    'visible'                        => 'مرئي',

    // ─── HERO section ──────────────────────────────────────────────
    'hero_eyebrow'                   => 'تسمية صغيرة (Eyebrow)',
    'headline'                       => 'العنوان الرئيسي',
    'subheadline'                    => 'العنوان الفرعي',
    'cta_label'                      => 'نص زر الدعوة',
    'cta_url'                        => 'رابط الدعوة',
    'image_url'                      => 'رابط الصورة',
    'alignment'                      => 'المحاذاة',
    'background'                     => 'الخلفية',

    // ─── FEATURES section ─────────────────────────────────────────
    'feature_items'                  => 'بنود المزايا',
    'icon_key_optional'              => 'مفتاح الأيقونة (اختياري)',
    'feature_title'                  => 'العنوان',
    'feature_body'                   => 'النص',

    // ─── FORM section ─────────────────────────────────────────────
    'form'                           => 'النموذج',
    'success_message'                => 'رسالة النجاح',

    // ─── TESTIMONIALS section ─────────────────────────────────────
    'testimonials'                   => 'الشهادات',
    'avatar_url'                     => 'رابط الصورة الرمزية',
    'testimonial_quote'              => 'الاقتباس',
    'testimonial_author'             => 'صاحب الشهادة',
    'testimonial_role'               => 'المسمى الوظيفي',

    // ─── CTA section ─────────────────────────────────────────────
    'body'                           => 'النص',
    'button_label'                   => 'نص الزر',
    'button_url'                     => 'رابط الزر',

    // ─── GALLERY section ─────────────────────────────────────────
    'images'                         => 'الصور',
    'image_url_placeholder'          => 'https://example.com/image.jpg',
    'caption'                        => 'التسمية التوضيحية',

    // ─── PRICING section ─────────────────────────────────────────
    'plans'                          => 'الخطط',
    'features_one_per_line'          => 'المزايا (واحدة لكل سطر)',
    'highlight_this_plan'            => 'تمييز هذه الخطة',
    'plan_name'                      => 'الاسم',
    'plan_price'                     => 'السعر',
    'plan_interval'                  => 'الفترة',
    'plan_cta_label'                 => 'نص الدعوة',
    'plan_cta_url'                   => 'رابط الدعوة',
    'plan_cta_default'               => 'اختيار الخطة',

    // ─── FAQ section ─────────────────────────────────────────────
    'q_and_a'                        => 'الأسئلة والأجوبة',
    'faq_question'                   => 'السؤال',
    'faq_answer'                     => 'الإجابة',

    // ─── HTML section ────────────────────────────────────────────
    'raw_html'                       => 'HTML خام',
    'raw_html_help'                  => 'للمشرف فقط. تُحذَف سمات السكربت ومعالجات الأحداث. ضع رموز التتبع في تبويب «متقدم» بدلًا من ذلك.',

    // ─── Appearance tab ──────────────────────────────────────────
    'primary_color'                  => 'اللون الأساسي',
    'background_color'               => 'لون الخلفية',
    'font_family'                    => 'عائلة الخط',

    // ─── Integration tab ─────────────────────────────────────────
    'linked_form'                    => 'النموذج المرتبط',
    'linked_form_help'               => 'يُستخدم لقسم النموذج الافتراضي ومعالج الإرسال الاحتياطي.',
    'pipeline'                       => 'خط الأنابيب',
    'stage'                          => 'المرحلة',
    'redirect_on_submit'             => 'رابط إعادة التوجيه بعد الإرسال',

    // ─── Advanced tab ────────────────────────────────────────────
    'custom_css'                     => 'CSS مخصص',
    'custom_css_help'                => 'يُدرَج داخل <style> قبل جسم الصفحة. للمشرف فقط.',
    'custom_js'                      => 'JS مخصص (رموز التتبع)',
    'custom_js_help'                 => 'يُدرَج قبل </body> مباشرة. للمشرف فقط — يعمل على كل زائر.',

    // ─── Table columns ───────────────────────────────────────────
    'name'                           => 'الاسم',
    'slug_copied'                    => 'تم نسخ المُعرّف الكنوي',
    'views'                          => 'المشاهدات',
    'conversions'                    => 'التحويلات',
    'created'                        => 'تاريخ الإنشاء',

    // ─── Row actions ─────────────────────────────────────────────
    'preview'                        => 'معاينة',
    'duplicate'                      => 'تكرار',

    // ─── Use-Template header action ──────────────────────────────
    'use_template'                   => 'استخدام القالب',
    'template'                       => 'القالب',
    'page_name'                      => 'اسم الصفحة',
    'template_not_found'             => 'القالب غير موجود',
    'template_created_title'         => 'تم إنشاء صفحة الهبوط من القالب',
    'template_created_body'          => 'حرّر الصفحة لتخصيص المحتوى.',

    // ─── Select options ────────────────────────────────────────────
    'option_status_draft'            => 'مسودة',
    'option_status_published'        => 'منشورة',
    'option_status_archived'         => 'مؤرشفة',
    'option_align_left'              => 'يسار',
    'option_align_center'            => 'وسط',
    'option_bg_gradient'             => 'متدرج',
    'option_bg_solid'                => 'لون ثابت',
    'option_bg_image'                => 'صورة',
    'option_palette_indigo'          => 'نيلي',
    'option_palette_gray'            => 'رمادي',
    'option_palette_white'           => 'أبيض',

    // ─── Status badge labels (table column) ────────────────────────
    'status_draft'                   => 'مسودة',
    'status_published'               => 'منشورة',
    'status_archived'                => 'مؤرشفة',

    // ─── Font fallback option ──────────────────────────────────────
    'font_system_default'            => 'افتراضي النظام',

    // ─── Plan interval short suffix (pricing section default) ──────
    'plan_interval_short_mo'         => 'شهر',
];
