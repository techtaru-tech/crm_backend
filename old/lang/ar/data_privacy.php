<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Data Privacy page — translation strings
|--------------------------------------------------------------------------
|
| Strings used by the tenant-side Data Privacy page (Filament).  Implements
| GDPR Article 15 (Right of Access) and Article 17 (Right to Erasure) UI.
|
| Buyers can adapt or localise the copy by editing this file or by copying
| it to lang/<locale>/data_privacy.php and translating.
|
| Placeholders use Laravel's :placeholder convention.
|
*/

return [

    'error_no_tenant'             => 'تعذّر تحديد مساحة العمل. يُرجى تسجيل الخروج ثم الدخول مجددًا.',

    // --- Right of Access — Data Export ----------------------------------
    'export_title'                => 'تصدير بياناتي',
    'export_description'          => 'سننشئ ملف ZIP لكل سجل لدينا في هذه المساحة — العملاء المحتملون والنماذج والأتمتة والإعدادات وكل شيء — بصيغة JSON. رابط التحميل صالح لمدة 48 ساعة.',

    'export_building_title'       => 'جارٍ إنشاء التصدير…',
    'export_building_body'        => 'حدّث الصفحة بعد بضع دقائق. قد تستغرق مساحات العمل الكبيرة بعض الوقت.',

    'export_failed_title'         => 'فشل التصدير الأخير.',
    'export_failed_body'          => 'حاول طلب تصدير جديد — إذا فشل مرة أخرى، تواصل مع الدعم.',
    'export_failed_btn'           => 'إعادة المحاولة',

    'export_ready_title'          => '✅ أرشيفك جاهز',
    'export_ready_size'           => 'الحجم: :size',
    'export_ready_expires'        => 'ينتهي :when',
    'export_download_btn'         => 'تحميل ZIP',
    'export_rebuild_btn'          => 'إنشاء تصدير جديد',
    'export_request_btn'          => 'تصدير بياناتي',

    // --- Right to Erasure — Workspace Deletion --------------------------
    'deletion_title'              => 'حذف مساحة العمل',
    'deletion_description'        => 'يحذف مساحة العمل هذه وكل سجل لدينا لها بشكل نهائي. مجدول قبل 30 يومًا حتى يمكنك تغيير رأيك — حتى ذلك الحين، تبقى مساحة العمل نشطة.',

    'deletion_scheduled_title'    => '⏰ الحذف مجدول',
    'deletion_scheduled_on'       => 'في :date',
    'deletion_days_left'          => '(تبقى :count يوم)|(تبقى :count أيام)',
    'deletion_cancel_btn'         => 'إلغاء الحذف',

    'deletion_request_confirm'    => 'هل أنت متأكد تمامًا؟ سيؤدي هذا إلى جدولة حذف نهائي لكل سجل في مساحة العمل هذه. ستحظى بـ 30 يومًا للإلغاء.',
    'deletion_request_btn'        => 'جدولة الحذف',

    'deletion_not_owner'          => 'لا يستطيع جدولة الحذف سوى مالك مساحة العمل. يُرجى التواصل مع المالك إذا كانت مساحة العمل هذه بحاجة إلى الإزالة النهائية.',

    // --- Footer ---------------------------------------------------------
    'gdpr_footer'                 => 'تطبق هذه الضوابط المادتين 15 (حق الوصول) و17 (حق المحو) من GDPR. للحصول على معلومات DPA / المعالجات الفرعية، تواصل مع الدعم.',

    // --- README.txt bundled in the GDPR export ZIP ----------------------
    // Rendered by TenantDataExporter::buildReadme() at request time.
    // :app, :workspace, :timestamp are substituted in PHP, not the
    // translator — we keep them in-template for clarity.
    'readme_title'                => ':app — تصدير البيانات الشخصية',
    'readme_divider'              => '============================================================',
    'readme_workspace'            => 'مساحة العمل: :workspace',
    'readme_generated'            => 'تم الإنشاء: :timestamp',
    'readme_intro'                => "يحتوي هذا الأرشيف على كل سجل لدينا لمساحة العمل،\nمُصدَّر بصيغة JSON لقابلية النقل (يمكن استيراده في أي مكان — CRM،\nجداول بيانات، نصوص مخصصة).",
    'readme_file_map_header'      => 'خريطة الملفات',
    'readme_subdivider'           => '------------------------------------------------------------',
    'readme_row_readme'           => 'README.txt                         هذا الملف',
    'readme_row_tenant'           => 'tenant.json                        ملف مساحة العمل والإعدادات والعلامة التجارية',
    'readme_row_members'          => 'members.json                       أعضاء الفريق + الأدوار',
    'readme_row_leads'            => 'leads.json                         كل عميل محتمل',
    'readme_row_companies'        => 'companies.json                     سجلات الشركات المرتبطة',
    'readme_row_activities'       => 'lead_activities.json               أحداث المسار الزمني',
    'readme_row_notes'            => 'lead_notes.json                    الملاحظات المرفقة بالعملاء المحتملين',
    'readme_row_tasks'            => 'lead_tasks.json                    المهام',
    'readme_row_messages'         => 'lead_messages.json                 الرسائل الواردة / الصادرة',
    'readme_row_emails'           => 'lead_emails.json                   رسائل البريد الإلكتروني',
    'readme_row_calls'            => 'lead_calls.json                    سجلات المكالمات',
    'readme_row_pipelines'        => 'pipelines.json                     خطوط الأنابيب',
    'readme_row_pipeline_stages'  => 'pipeline_stages.json               المراحل',
    'readme_row_tags'             => 'tags.json                          الوسوم',
    'readme_row_custom_fields'    => 'custom_field_definitions.json      مخطط الحقول المخصصة',
    'readme_row_forms'            => 'forms.json                         النماذج',
    'readme_row_form_submissions' => 'form_submissions.json              عمليات الإرسال',
    'readme_row_landing_pages'    => 'landing_pages.json                 صفحات الهبوط',
    'readme_row_automations'      => 'automations.json                   الأتمتة',
    'readme_row_email_sequences'  => 'email_sequences.json               حملات التنقيط',
    'readme_row_email_templates'  => 'email_templates.json               القوالب',
    'readme_row_products'         => 'products.json                      الكتالوج',
    'readme_row_quotes'           => 'quotes.json                        عروض الأسعار',
    'readme_row_invoices'         => 'invoices.json                      الفواتير',
    'readme_row_meeting_types'    => 'meeting_types.json                 أنواع الاجتماعات القابلة للحجز',
    'readme_row_meeting_bookings' => 'meeting_bookings.json              الاجتماعات المحجوزة',
    'readme_row_integrations'     => 'integrations.json                  التكاملات المتصلة',
    'readme_row_api_keys'         => 'api_keys.json                      مفاتيح API (أسرار محذوفة)',
    'readme_notes_header'         => 'ملاحظات',
    'readme_note_iso8601'         => '- جميع حقول التاريخ والوقت بصيغة ISO-8601.',
    'readme_note_redaction'       => "- يتم حذف أسرار مفاتيح API وأي عمود مُعلَّم سرّي / رمز /\n  api_key / مفتاح.",
    'readme_note_attachments'     => "- يُشار إلى الملفات المرفقة بالمسار فقط — لتنزيل\n  الملفات الفعلية، تواصل مع الدعم.",
    'readme_note_snapshot'        => '- هذا التصدير لقطة في :timestamp.',

];
