<?php

declare(strict_types=1);

/*
|-----------------------------------------------------------------
| SuperAdmin BrandingPage - Filament admin strings
|-----------------------------------------------------------------
| Accessed via __('filament/sa_branding.<key>').
*/

return [
    'navigation_label'              => 'العلامة التجارية',
    'title'                         => 'العلامة التجارية',

    'section_identity'              => 'هوية العلامة التجارية',
    'section_identity_description'  => 'الاسم والعناصر المرئية التي تمثّل منصّتك في الصفحة الرئيسية العامة وشاشة تسجيل الدخول ولوحات المستأجرين.',
    'app_name_label'                => 'اسم العلامة التجارية',
    'app_name_helper'               => 'اسم المنتج الذي يظهر في تبويب المتصفح وصفحة تسجيل الدخول وترويسة اللوحة. غيِّره من "LeadHub" إلى اسم علامتك التجارية.',
    'logo_url_label'                => 'رابط الشعار',
    'logo_url_helper'               => 'رابط عام لشعار علامتك التجارية (PNG أو SVG). اتركه فارغًا لاستخدام الشعار الافتراضي.',
    'logo_file_label'               => 'أو ارفع ملف شعار',
    'logo_file_helper'              => 'PNG / SVG / JPG حتى 2 ميجابايت. الرفع يستبدل الرابط أعلاه بالملف المرفوع.',
    'favicon_url_label'             => 'رابط أيقونة المتصفح (Favicon)',
    'favicon_url_helper'            => 'رابط عام لأيقونة بحجم 32x32 (أو أكبر) بصيغة ICO / PNG لتبويب المتصفح.',
    'favicon_file_label'            => 'أو ارفع ملف أيقونة المتصفح',
    'favicon_file_helper'           => 'PNG / ICO / SVG مربع (32x32 أو أكبر). الرفع يستبدل الرابط أعلاه.',
    'footer_text_label'             => 'نص التذييل',
    'footer_text_helper'            => 'شعار قصير أو سطر حقوق النشر يظهر في تذييل الصفحة العامة. لا يُعالَج تنسيق Markdown.',

    'section_colors'                => 'ألوان العلامة التجارية',
    'section_colors_description'    => 'لوحة الألوان المُطبَّقة على الأزرار والروابط واللمسات في الموقع العام ولوحات الإدارة.',
    'primary_color_label'           => 'اللون الأساسي',
    'accent_color_label'            => 'لون التمييز',

    'section_login'                 => 'شاشة تسجيل الدخول',
    'section_login_description'     => 'خصِّص خلفية شاشتي تسجيل الدخول /admin و /super-admin.',
    'login_bg_color_label'          => 'لون خلفية تسجيل الدخول',
    'login_bg_image_label'          => 'رابط صورة خلفية تسجيل الدخول',

    'action_save'                   => 'حفظ العلامة التجارية',
    'saved_title'                   => 'تم حفظ العلامة التجارية',
    'saved_body'                    => 'تغييراتك أصبحت سارية على الموقع العام ولوحات الإدارة.',
    'save_failed_title'             => 'تعذّر حفظ العلامة التجارية',
    'save_failed_body'              => 'فشل الحفظ وتم التراجع. راجِع storage/logs/laravel.log للحصول على تفاصيل الخطأ.',
];
