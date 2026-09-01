<?php

declare(strict_types=1);

/*
 |--------------------------------------------------------------------------
 | سلاسل ترجمة LeadResource (ar)
 |--------------------------------------------------------------------------
 |
 | جميع السلاسل الظاهرة للمستخدم المستخدمة في LeadResource وصفحاته
 | ومديري العلاقات. المفاتيح بصيغة snake_case الإنجليزية وتُحفظ
 | مطابقة لـ lang/en/filament/leads.php؛ تُترجم القيم فقط.
 | يتم الوصول عبر __('filament/leads.<key>').
 |
 */

return [
    // ─── التنقّل ───
    'nav_label'                     => 'جميع العملاء المحتملين',

    // ─── تسميات النموذج ───
    'model_label'                   => 'عميل محتمل',
    'plural_model_label'            => 'العملاء المحتملون',

    // ─── البحث الشامل ───
    'search_result_fallback'        => 'عميل محتمل رقم :id',
    'search_result_email'           => 'البريد الإلكتروني',
    'search_result_source'          => 'المصدر',
    'search_result_status'          => 'الحالة',
    'search_result_score'           => 'النقاط',
    'search_result_score_value'     => ':score نقطة',

    // ─── النموذج: معلومات الاتصال ───
    'first_name'                    => 'الاسم الأول',
    'last_name'                     => 'اسم العائلة',
    'email'                         => 'البريد الإلكتروني',
    'phone'                         => 'الهاتف',
    'company'                       => 'الشركة',
    'company_name'                  => 'اسم الشركة',
    'domain'                        => 'النطاق',
    'industry'                      => 'القطاع',

    // ─── النموذج: تفاصيل العميل المحتمل ───
    'source'                        => 'المصدر',
    'status'                        => 'الحالة',
    'assigned_to'                   => 'مُعيَّن لـ',
    'pipeline'                      => 'خط الأنابيب',
    'stage'                         => 'المرحلة',
    'score'                         => 'النقاط',
    'starred'                       => 'مُمَيَّز',
    'lead_notes'                    => 'الملاحظات',

    // ─── النموذج: الصفقة ───
    'deal_value'                    => 'قيمة الصفقة',
    'currency'                      => 'العملة',
    'expected_close_date'           => 'تاريخ الإغلاق المتوقع',
    'lost_reason'                   => 'سبب الخسارة',

    // ─── النموذج: معلومات إضافية ───
    'source_reference_id'           => 'معرّف مرجع المصدر',
    'last_contacted'                => 'آخر تواصل',

    // ─── النموذج: الإسناد ───
    'attribution_description'       => 'تم التقاطه من النموذج أو الأداة التي أنشأت هذا العميل المحتمل.',
    'utm_source'                    => 'UTM Source',
    'utm_medium'                    => 'UTM Medium',
    'utm_campaign'                  => 'UTM Campaign',
    'utm_content'                   => 'UTM Content',
    'utm_term'                      => 'UTM Term',
    'landing_page'                  => 'صفحة الهبوط',
    'referrer'                      => 'المُحيل',
    'custom_fields_description'     => 'حقول معرّفة من قِبل المستأجر. اضبطها في الإعدادات ← الحقول المخصصة.',

    // ─── أعمدة الجدول ───
    'name'                          => 'الاسم',
    'expected_close'                => 'الإغلاق المتوقع',
    'tags'                          => 'العلامات',
    'assigned'                      => 'مُسنَد',
    'dup'                           => 'مكرر',
    'waiting_on'                    => 'في انتظار',
    'created_at'                    => 'تاريخ الإنشاء',

    // ─── المرشحات ───
    'filter_label_source'           => 'المصدر',
    'filter_label_status'           => 'الحالة',
    'tag'                           => 'العلامة',
    'starred_only'                  => 'المُمَيَّز فقط',
    'not_starred'                   => 'غير مُمَيَّز',
    'duplicates_only'               => 'المكررات فقط',
    'waiting_us'                    => 'نحن (قاموا بالرد)',
    'waiting_them'                  => 'هم (تواصلنا معهم)',
    'waiting_new'                   => 'جديد (لا يوجد تواصل)',
    'created_from'                  => 'أُنشئ من',
    'created_until'                 => 'أُنشئ حتى',
    'min_score'                     => 'الحد الأدنى للنقاط',
    'min_deal_value'                => 'الحد الأدنى لقيمة الصفقة',
    'max_deal_value'                => 'الحد الأقصى لقيمة الصفقة',

    // ─── إجراءات الصف ───
    'tooltip_unstar'                => 'إزالة التمييز',
    'tooltip_star_this_lead'        => 'تمييز هذا العميل المحتمل',
    'tooltip_view_lead'             => 'عرض العميل المحتمل',
    'tooltip_edit'                  => 'تعديل',
    'tooltip_delete'                => 'حذف',
    'view_detail_action_label'      => 'عرض التفاصيل',

    // ─── الإجراءات المجمّعة ───
    'bulk_assign_agent'             => 'إسناد وكيل',
    'bulk_assign_to'                => 'إسناد إلى',
    'bulk_leads_assigned'           => 'تم إسناد العملاء المحتملين.',
    'bulk_change_status'            => 'تغيير الحالة',
    'bulk_status_updated'           => 'تم تحديث الحالة.',
    'bulk_add_tag'                  => 'إضافة علامة',
    'bulk_tag_added'                => 'تمت إضافة العلامة إلى العملاء المحتملين المحددين.',
    'bulk_remove_tag'               => 'إزالة علامة',
    'bulk_tag_removed'              => 'تمت إزالة العلامة من العملاء المحتملين المحددين.',
    'bulk_move_to_stage'            => 'النقل إلى مرحلة',
    'bulk_leads_moved'              => 'تم نقل العملاء المحتملين إلى المرحلة.',
    'bulk_export_csv'               => 'تصدير المحدد (CSV)',
    'bulk_export_queued'            => 'تم وضع التصدير في قائمة الانتظار — سيصل رابط التنزيل قريبًا.',
    'bulk_run_automation'           => 'تشغيل الأتمتة',
    'bulk_select_automation'        => 'اختيار الأتمتة',
    'bulk_enroll_in_sequence'       => 'تسجيل في تسلسل',
    'bulk_sequence'                 => 'التسلسل',
    'bulk_automation_queued'        => 'تم وضع الأتمتة في قائمة الانتظار لـ :count عميل محتمل.',
    'bulk_enrolled_skipped'         => 'تم تسجيل :added عميل محتمل. تم تخطي :skipped مسجل مسبقًا.',

    // ─── الحالة الفارغة ───
    'empty_heading'                 => 'لا يوجد عملاء محتملون بعد',
    'empty_description'             => 'التقط أول عميل محتمل عبر استيراد ملف CSV أو إنشاء نموذج قابل للتضمين أو إضافته يدويًا.',
    'empty_add_lead'                => 'إضافة عميل محتمل',
    'empty_import_csv'              => 'الاستيراد من CSV',
    'empty_build_form'              => 'إنشاء نموذج التقاط',

    // ─── صفحة العرض: إجراءات الترويسة ───
    'add_line_item'                 => 'إضافة بند',
    'product'                       => 'المنتج',
    'item_name'                     => 'اسم البند',
    'unit_price'                    => 'سعر الوحدة',
    'discount_percent'              => 'الخصم %',
    'line_item_added'               => 'تمت إضافة البند.',

    'create_task'                   => 'إنشاء مهمة',
    'task_title'                    => 'عنوان المهمة',
    'description'                   => 'الوصف',
    'due_at'                        => 'تاريخ الاستحقاق',
    'priority'                      => 'الأولوية',
    'reminder_at'                   => 'وقت التذكير',
    'reminder_help'                 => 'افتراضيًا قبل ساعة واحدة من موعد الاستحقاق.',
    'task_created'                  => 'تم إنشاء المهمة.',

    'send_email'                    => 'إرسال بريد إلكتروني',
    'load_template'                 => 'تحميل من قالب',
    'load_template_help'            => 'اختر قالب بريد محفوظًا لملء الموضوع والنص. يمكنك تعديلهما قبل الإرسال.',
    'attachments'                   => 'المرفقات',
    'no_email_address'              => 'لا يوجد عنوان بريد إلكتروني للعميل المحتمل.',
    'email_log_mode_title'          => 'تم وضع البريد في قائمة الانتظار، لكن البريد الصادر في وضع التسجيل.',
    'email_log_mode_body'           => 'لم يتم تكوين SMTP — سيُكتب البريد إلى storage/logs/laravel.log فقط. زر الإعدادات ← البريد لإعداد SMTP وبدء التسليم.',
    'email_queued'                  => 'تم وضع البريد في قائمة انتظار التسليم.',

    'call_lead'                     => 'اتصال',
    'call_modal_heading'            => 'بدء مكالمة',
    'call_modal_description'        => 'هل تريد بدء مكالمة إلى :phone؟ سيرن هاتفك أولًا ثم يتم ربطه بالعميل المحتمل.',
    'call_now'                      => 'الاتصال الآن',
    'call_no_phone'                 => 'لا يحتوي ملفك الشخصي على رقم هاتف — لا يمكن ربط المكالمة.',
    'call_failed_to_start'          => 'تعذّر بدء المكالمة — راجع المراسلة ← إعدادات الصوت.',
    'call_initiated'                => 'تم بدء المكالمة — أجب على هاتفك.',
    'call_failed_prefix'            => 'فشلت المكالمة: ',

    'send_message'                  => 'إرسال رسالة',
    'conversation_count'            => 'المحادثة (:count)',
    'channel'                       => 'القناة',
    'message'                       => 'الرسالة',
    'no_phone_number'               => 'لا يوجد رقم هاتف للعميل المحتمل.',
    'message_queued'                => 'تم وضع الرسالة في قائمة انتظار التسليم.',

    'log_call'                      => 'تسجيل مكالمة',
    'inbound'                       => 'واردة',
    'outbound'                      => 'صادرة',
    'outcome_connected'             => 'متصلة',
    'outcome_voicemail'             => 'بريد صوتي',
    'outcome_no_answer'             => 'لا يوجد رد',
    'outcome_not_interested'        => 'غير مهتم',
    'outcome_callback'              => 'طلب معاودة الاتصال',
    'call_logged'                   => 'تم تسجيل المكالمة.',
    'duration'                      => 'المدة',
    'duration_minutes_suffix'       => 'دقيقة',
    'outcome'                       => 'النتيجة',

    'add_note'                      => 'إضافة ملاحظة',
    'mention_label'                 => 'الإشارة إلى أعضاء الفريق (اكتب @ للبحث)',
    'mention_placeholder'           => 'مثال: @jane',
    'mention_help'                  => 'افصل بين الإشارات المتعددة بفاصلات.',
    'note'                          => 'الملاحظة',
    'note_body_help'                => 'استخدم @name للإشارة إلى أعضاء الفريق.',
    'note_added'                    => 'تمت إضافة الملاحظة.',

    'move_stage'                    => 'نقل المرحلة',
    'lead_moved_to_stage'           => 'تم نقل العميل المحتمل إلى المرحلة الجديدة.',

    'assign'                        => 'إسناد',
    'lead_assigned'                 => 'تم إسناد العميل المحتمل.',

    'enroll_in_sequence'            => 'التسجيل في التسلسل',
    'sequence'                      => 'التسلسل',
    'already_enrolled'              => 'العميل المحتمل مسجَّل بالفعل في هذا التسلسل.',
    'lead_enrolled'                 => 'تم تسجيل العميل المحتمل في التسلسل.',

    'apply_tags'                    => 'العلامات',
    'tags_updated'                  => 'تم تحديث العلامات.',

    'star'                          => 'تمييز',
    'unstar'                        => 'إزالة التمييز',

    'more'                          => 'المزيد',

    'create_quote'                  => 'إنشاء عرض سعر',
    'create_invoice'                => 'إنشاء فاتورة',

    'enrich_with_ai'                => 'الإثراء بالذكاء الاصطناعي',
    're_enrich_with_ai'             => 'إعادة الإثراء بالذكاء الاصطناعي',
    're_enrich_modal_heading'       => 'إعادة إثراء العميل المحتمل',
    're_enrich_modal_description'   => 'تم إثراء هذا العميل المحتمل من قبل. تشغيل الإثراء مرة أخرى سيستبدل بيانات الشركة والقطاع والموقع.',
    'enrich_no_email'               => 'لا يوجد بريد إلكتروني للعميل المحتمل — لا يمكن الإثراء دون عنوان بريد إلكتروني.',
    'enrich_queued'                 => 'تم وضع الإثراء في قائمة الانتظار. ستظهر البيانات قريبًا.',

    'ai_draft_email'                => 'مسوّدة بريد بالذكاء الاصطناعي',
    'email_intent'                  => 'هدف البريد',
    'intent_introduction'           => 'تعارف أوّلي',
    'intent_follow_up'              => 'متابعة',
    'intent_proposal'               => 'مقترح / الخطوات التالية',
    'intent_re_engage'              => 'إعادة جذب عميل بارد',
    'intent_closing'                => 'إغلاق / تأكيد',
    'additional_context'            => 'سياق إضافي (اختياري)',
    'additional_context_placeholder'=> 'مثال: شاهدوا صفحة الأسعار لدينا أمس...',
    'ai_draft_failed'               => 'تعذّر إنشاء المسوّدة. تأكد من تكوين مفتاح OpenAI API.',
    'ai_draft_generated'            => 'تم إنشاء المسوّدة',
    'subject_label'                 => 'الموضوع',

    'merge_lead'                    => 'دمج العميل المحتمل',
    'merge_into_label'              => 'دمج هذا العميل المحتمل في (الاحتفاظ كرئيسي)',
    'merge_into_help'               => 'سيتم الاحتفاظ بالعميل المحتمل الرئيسي. هذا العميل المحتمل سيُؤرشف.',
    'merge_primary_not_found'       => 'العميل المحتمل الرئيسي غير موجود.',
    'merge_success'                 => 'تم دمج العملاء المحتملين. جارٍ التحويل إلى العميل المحتمل الرئيسي…',
    'merge_option_format'           => ':name — :email (تطابق على :field)',
    'no_email'                      => 'لا يوجد بريد إلكتروني',

    'export_data'                   => 'تصدير البيانات',

    'send_portal_link'              => 'إرسال رابط البوابة',
    'portal_link_heading'           => 'إرسال رابط الدخول إلى البوابة',
    'portal_link_description'       => 'يرسل رابطًا سحريًا صالحًا لمدة 30 دقيقة إلى بريد العميل المحتمل ليتمكن من عرض حالة صفقته ورفع المستندات عبر بوابة العملاء.',
    'portal_link_sent_prefix'       => 'تم إرسال رابط البوابة إلى ',
    'portal_link_failed_prefix'     => 'فشل الإرسال: ',

    'gdpr_anonymize'                => 'إخفاء الهوية (GDPR)',
    'gdpr_anonymize_heading'        => 'إخفاء هوية العميل المحتمل',
    'gdpr_anonymize_description'    => 'يستبدل جميع البيانات الشخصية بعناصر نائبة لكنه يحتفظ بسجل العميل المحتمل والإحصائيات المجمّعة (قيمة الصفقة، الحالة، خط الأنابيب، المصدر، العلامات). تُحذف المرفقات والبريد والرسائل والملاحظات ومشاهدات الصفحة. تُحفظ صفوف النشاط مع تجريد البيانات الوصفية.',
    'gdpr_anonymize_confirm'        => 'نعم، إخفاء الهوية',
    'gdpr_anonymized_success'       => 'تم إخفاء هوية العميل المحتمل. تم الحفاظ على الإحصائيات المجمّعة.',

    'gdpr_erase'                    => 'محو (GDPR)',
    'gdpr_erase_heading'            => 'محو العميل المحتمل وفق GDPR',
    'gdpr_erase_description'        => 'يحذف بشكل دائم كل أثر لهذا العميل المحتمل — الأنشطة والملاحظات والمهام والمرفقات ورسائل البريد والرسائل ومشاهدات صفحات الويب وبنود الصفقات والتسجيلات في تسلسلات البريد. لا يمكن التراجع عن ذلك.',
    'gdpr_erase_confirm'            => 'نعم، محو بشكل دائم',
    'gdpr_erase_success'            => 'تم محو بيانات العميل المحتمل بشكل دائم.',

    'attachment_uploaded'           => 'تم رفع المرفق (المرفقات).',
    'attachment_deleted'            => 'تم حذف المرفق.',
    'line_item_removed'             => 'تمت إزالة البند.',

    // ─── صفحة التعديل ───
    'full_detail_view'              => 'عرض كامل التفاصيل',

    // ─── صفحة القائمة ───
    'kanban_board'                  => 'لوحة كانبان',
    'save_filters'                  => 'حفظ المرشحات',
    'view_name'                     => 'اسم العرض',
    'view_name_placeholder'         => 'مثال: عملائي المحتملون الساخنون لهذا الأسبوع',
    'email_alerts'                  => 'أرسل لي بريدًا عند وجود عملاء محتملين جدد مطابقين',
    'email_alerts_help'             => 'فحص كل ساعة — ستتلقى ملخصًا عندما يتطابق عملاء محتملون جدد مع هذا المرشح.',
    'share_with_team'               => 'مشاركة مع الفريق',
    'share_with_team_help'          => 'يمكن لكل أعضاء مساحة العمل تحميل هذا العرض.',
    'filter_view_saved'             => 'تم حفظ عرض المرشحات',
    'filter_view_saved_as'          => 'تم الحفظ باسم «:name».',
    'filter_view_loaded'            => 'تم تحميل «:name»',

    'saved_views'                   => 'العروض المحفوظة',
    'no_saved_views_yet'            => 'لا توجد عروض محفوظة بعد. طبّق المرشحات وانقر «حفظ المرشحات» لإنشاء واحد.',
    'select_saved_view'             => 'اختر عرضًا محفوظًا',
    'saved_view_not_found'          => 'العرض المحفوظ غير موجود.',
    'placeholder_empty_label'       => '',

    'delete_view'                   => 'حذف العرض',
    'no_saved_views_to_delete'      => 'لا توجد عروض محفوظة للحذف.',
    'select_view_to_delete'         => 'اختر العرض المراد حذفه',
    'saved_view_deleted'            => 'تم حذف العرض المحفوظ.',

    'export_current_filters'        => 'تصدير (المرشحات الحالية)',
    'export_queued_with_link'       => 'تم وضع التصدير في قائمة الانتظار — ستحصل على رابط التنزيل قريبًا.',

    'import_from_crm'               => 'الاستيراد من CRM',
    'import_modal_heading'          => 'استيراد عملاء محتملين من CRM آخر',
    'import_modal_description'      => 'حمّل ملف CSV مُصدَّر من HubSpot أو Pipedrive أو Salesforce أو أي ملف CSV عام. يكتشف المورد تلقائيًا عند ضبطه على «الاكتشاف التلقائي».',
    'source_crm'                    => 'CRM المصدر',
    'auto_detect_option'            => 'الاكتشاف التلقائي من رؤوس أعمدة CSV',
    'source_crm_help'               => 'الاكتشاف التلقائي يتعرّف على صف الرؤوس. اختر موردًا محددًا إذا فشل الاكتشاف التلقائي في تنسيقك.',
    'csv_file'                      => 'ملف CSV',
    'csv_file_help'                 => 'حتى 20 ميغابايت. يجب أن يحتوي الصف الأول على رؤوس الأعمدة.',
    'no_workspace_context'          => 'لا يوجد سياق لمساحة العمل — يرجى إعادة التحميل.',
    'no_file_uploaded'              => 'لم يتم رفع أي ملف.',
    'csv_import_complete'           => 'اكتمل استيراد CSV',

    // ─── أسطر نص إشعار استيراد CSV ───
    'import_body_imported_count'    => 'تم استيراد :count عميل محتمل من :vendor.',
    'import_body_duplicate_count'    => 'كان :count صفًا موجودًا بالفعل (تم تخطيه كمكرر).',

    'import_body_skipped_count'     => 'تم تخطي :count صفًا (بدون بريد إلكتروني أو هاتف).',
    'import_body_batch_errors'      => ':count خطأ (أخطاء) في الدفعة — راجع السجلات.',

    // ─── العلاقة: رسائل البريد الإلكتروني ───
    'emails_title'                  => 'رسائل البريد الإلكتروني',
    'from'                          => 'من',
    'body_text'                     => 'المحتوى (نص)',
    'subject'                       => 'الموضوع',
    'sent'                          => 'مُرسَل',
    'opened'                        => 'مفتوح',
    'clicked'                       => 'تم النقر',
    'received'                      => 'مُستلَم',
    'direction'                     => 'الاتجاه',
    'email_modal_default'           => 'بريد إلكتروني',
    'body'                          => 'المحتوى',

    // ─── العلاقة: الرسائل ───
    'messages_title'                => 'الرسائل',
    'channel_whatsapp'              => 'WhatsApp',
    'channel_sms'                   => 'SMS',
    'channel_telegram'              => 'Telegram',
    'channel_viber'                 => 'Viber',
    'status_sent'                   => 'مُرسَل',
    'status_delivered'              => 'تم التسليم',
    'status_read'                   => 'مقروءة',
    'status_failed'                 => 'فشلت',
    'media_url'                     => 'رابط الوسائط',
    'message_modal'                 => 'الرسالة',
    'message_status'                => 'الحالة',
    'sent_at'                       => 'وقت الإرسال',

    // ─── العلاقة: المهام ───
    'tasks_title'                   => 'المهام',
    'due'                           => 'الاستحقاق',
    'done'                          => 'منجزة',
    'mark_complete'                 => 'تحديد كمنجزة',
    'mark_incomplete'               => 'تحديد كغير منجزة',
    'reminder_help_short'           => 'افتراضيًا قبل ساعة واحدة من الاستحقاق.',

    // ─── العلاقة: بنود الصفقة ───
    'line_items_title'              => 'بنود الصفقة',
    'discount'                      => 'الخصم',
    'quantity'                      => 'الكمية',
    'total'                         => 'الإجمالي',

    // ─── العلاقة: تسجيلات التسلسلات ───
    'email_sequences_title'         => 'تسلسلات البريد الإلكتروني',
    'step'                          => 'الخطوة',
    'next_send'                     => 'الإرسال التالي',
    'unenroll'                      => 'إلغاء التسجيل',
    'lead_unenrolled'               => 'تم إلغاء تسجيل العميل المحتمل.',
    'sequence_status'               => 'الحالة',
    'enrolled_at'                   => 'تاريخ التسجيل',
    'unenroll_reason_manual'        => 'تم إلغاء التسجيل يدويًا',
    // Wave BB: الأسباب المحفوظة المكتوبة من LeadObserver (won/converted),
    // ProcessEmailSequences (بيانات مفقودة / لا يوجد بريد), و LeadEmail
    // (رد وارد). تُترجم وقت الكتابة بحيث يطابق العمود اللغة النشطة
    // في لحظة الإدراج.
    'unenroll_reason_converted'     => 'تم تعليم العميل المحتمل كمُحوَّل',
    'unenroll_reason_won'           => 'تم تعليم العميل المحتمل كرابح',
    'unenroll_reason_missing_data'  => 'التسلسل أو العميل المحتمل مفقود',
    'unenroll_reason_no_email'      => 'لا يوجد بريد إلكتروني للعميل المحتمل',
    'unenroll_reason_replied'       => 'العميل المحتمل قد ردّ',
    // Wave BB: العناصر النائبة المكتوبة في LeadActivity.metadata.i18n_params
    // من LeadObserver عندما تكون مرحلة خط الأنابيب القديمة/الجديدة مفقودة
    // (stage_none_placeholder) أو لم يُحدَّد مُسنَد
    // (unassigned_placeholder).
    'stage_none_placeholder'        => 'لا يوجد',
    'unassigned_placeholder'        => 'غير مُسنَد',

    // ─── العلاقة: مشاهدات الصفحات ───
    'web_activity_title'            => 'نشاط الويب',
    'viewed'                        => 'تمت المشاهدة',
    'page_path'                     => 'المسار',
    'page_title'                    => 'العنوان',
    'page_utm_source'               => 'UTM Source',

    // ─── صفحة العرض: العناوين الفرعية وسلاسل wire:confirm ───
    'view_quotes_heading'           => 'عروض الأسعار',
    'view_invoices_heading'         => 'الفواتير',
    'confirm_delete_attachment'     => 'هل أنت متأكد من حذف هذا المرفق؟',
    'confirm_remove_line_item'      => 'إزالة هذا البند؟',

    // ─── صفحة العرض: تسميات Blade وعناوين الأقسام ───
    'section_contact_info'          => 'معلومات الاتصال',
    'section_attachments'           => 'المرفقات',
    'section_line_items'            => 'بنود الصفقة',
    'section_quotes_invoices'       => 'عروض الأسعار والفواتير',
    'section_internal_notes'        => 'الملاحظات الداخلية',
    'section_call_history'          => 'سجل المكالمات',
    'section_web_activity'          => 'نشاط الويب',
    'section_activity_timeline'     => 'الخط الزمني للنشاط',
    'section_custom_fields'         => 'الحقول المخصصة',
    'section_email_sequences'       => 'تسلسلات البريد الإلكتروني',
    'section_ai_coaching'           => 'إرشاد الذكاء الاصطناعي',
    'section_conversations'         => 'المحادثات',
    'view_name_label'               => 'الاسم',
    'view_email_label'              => 'البريد الإلكتروني',
    'view_phone_label'              => 'الهاتف',
    'view_source_label'             => 'المصدر',
    'view_status_label'             => 'الحالة',
    'view_lead_score_label'         => 'نقاط العميل المحتمل',
    'view_pts_unit'                 => '/ 100 نقطة',
    'view_no_name'                  => '(لا يوجد اسم)',
    'view_dash'                     => '—',
    'view_why_this_score'           => 'لماذا هذه النقاط؟',
    'view_default_rule_name'        => 'القاعدة',
    'view_pts_suffix'               => 'نقطة',
    'view_assigned_to_label'        => 'مُعيَّن لـ',
    'view_pipeline_stage_label'     => 'مرحلة خط الأنابيب',
    'view_tags_label'               => 'العلامات',
    'view_company_label'            => 'الشركة',
    'view_job_title_label'          => 'المسمى الوظيفي',
    'view_industry_size_label'      => 'القطاع / الحجم',
    'view_employees_suffix'         => 'موظف',
    'view_country_label'            => 'الدولة',
    'view_linkedin_label'           => 'LinkedIn',
    'view_linkedin_view_profile'    => 'عرض الملف الشخصي ←',
    'view_ai_enriched_label'        => 'مُثرى بالذكاء الاصطناعي',
    'view_created_label'            => 'تاريخ الإنشاء',
    'view_inbox_in'                 => 'وارد',
    'view_inbox_out'                => 'صادر',
    'view_inbox_status_prefix'      => 'الحالة:',

    // ─── عرض العميل المحتمل: تسميات قناة البريد الوارد الموحّد (Pass 22) ───
    // تُستخدم في resources/views/filament/resources/leads/view.blade.php
    // إلى جانب مفاتيح channel_* الموجودة (whatsapp, sms, telegram,
    // viber).  تغطي قيم البريد الإلكتروني والدردشة عبر الويب المنتجة عندما
    // يدمج البريد الوارد صفوف LeadEmail/LeadMessage.
    'channel_email'                 => 'البريد الإلكتروني',
    'channel_webchat'               => 'دردشة الويب',

    // ─── عرض العميل المحتمل: شارة حالة البريد الوارد الموحّد (Pass 22) ───
    // خريطة لإشارات الحالة على مستوى البريد الوارد المحسوبة في
    // leads/view.blade.php (opened/clicked/bounced/sent إلخ) لتُتبع
    // الشارة اللغة النشطة وليس صيغة ucfirst() الخام.
    'inbox_status_opened'           => 'مفتوح',
    'inbox_status_clicked'          => 'تم النقر',
    'inbox_status_bounced'          => 'مرتد',
    'inbox_status_sent'             => 'مُرسَل',
    'inbox_status_delivered'        => 'تم التسليم',
    'inbox_status_read'             => 'مقروءة',
    'inbox_status_failed'           => 'فشلت',
    'inbox_status_pending'          => 'قيد الانتظار',
    'view_open_conversation_view'   => 'فتح عرض المحادثة الكامل',
    'view_step_label'               => 'الخطوة',
    'view_next_label'               => '· التالي :time',
    'view_completed_label'          => '· اكتمل :time',
    'view_no_attachments_yet'       => 'لا توجد مرفقات بعد.',
    'view_attachment_download_title' => 'تنزيل',
    'view_attachment_delete_title'  => 'حذف',
    'view_uploading'                => 'جارٍ الرفع...',
    'view_save_attachments'         => 'حفظ المرفقات',
    'view_table_item'               => 'البند',
    'view_table_qty'                => 'الكمية',
    'view_table_unit_price'         => 'سعر الوحدة',
    'view_table_discount'           => 'الخصم',
    'view_table_total'              => 'الإجمالي',
    'view_table_sku_prefix'         => 'SKU:',
    'view_table_total_label'        => 'الإجمالي:',
    'view_remove_item_title'        => 'إزالة',
    'view_no_line_items'            => 'لا توجد بنود بعد. انقر «إضافة بند» أعلاه لإرفاق منتجات أو خدمات بهذه الصفقة.',
    'view_no_quotes_invoices'       => 'لا توجد عروض أسعار أو فواتير بعد. استخدم إجراءات «المزيد ← إنشاء عرض سعر / إنشاء فاتورة» في الترويسة.',
    'view_invoice_due_label'        => 'استحقاق :date',
    // شارات الحالة للقوائم المدمجة لعروض الأسعار/الفواتير/المكالمات في
    // الشريط الجانبي لعرض العميل المحتمل. البحث أولًا عبر المترجم مع
    // الرجوع إلى ucfirst() للحالات غير المعروفة يبقي امتدادات
    // التعدادات المخصصة مفهومة.
    'view_quote_status_draft'       => 'مسوّدة',
    'view_quote_status_sent'        => 'مُرسَل',
    'view_quote_status_accepted'    => 'مقبول',
    'view_quote_status_declined'    => 'مرفوض',
    'view_quote_status_expired'     => 'منتهي الصلاحية',
    'view_quote_status_converted'   => 'تم التحويل',
    'view_invoice_status_draft'     => 'مسوّدة',
    'view_invoice_status_sent'      => 'مُرسَلة',
    'view_invoice_status_partial'   => 'جزئية',
    'view_invoice_status_overdue'   => 'متأخرة',
    'view_invoice_status_paid'      => 'مدفوعة',
    'view_invoice_status_cancelled' => 'مُلغاة',
    'view_invoice_status_refunded'  => 'مُستردة',
    'view_call_status_completed'    => 'مكتملة',
    'view_call_status_failed'       => 'فشلت',
    'view_call_status_canceled'     => 'مُلغاة',
    'view_call_status_no_answer'    => 'لا يوجد رد',
    'view_call_status_busy'         => 'مشغول',
    'view_note_system'              => 'النظام',
    'view_note_mentioned_label'     => 'المُشار إليهم:',
    'view_no_internal_notes'        => 'لا توجد ملاحظات داخلية بعد.',
    'view_call_agent_default'       => 'الوكيل',
    'view_call_ai_summary'          => 'ملخص الذكاء الاصطناعي',
    'view_utm_prefix'               => 'utm:',
    'view_activity_by'              => 'بواسطة :name',
    'view_no_activity_yet'          => 'لم يُسجَّل أي نشاط بعد.',
    'view_custom_yes'               => 'نعم',
    'view_custom_no'                => 'لا',
    'view_media_attachment'         => '[مرفق وسائط]',

    // ─── عمود «آخر رد بواسطة» للمحادثة (جدول العملاء المحتملين) ──
    'conversation_last_by_us'       => 'نحن',
    'conversation_last_by_them'     => 'هم',
    'conversation_last_by_new'      => 'جديد',

    // ─── تسميات الشارة الاحتياطية (Wave A — formatStateUsing من Filament)
    // تدعم هذه المفاتيح استدعاءات formatStateUsing() على شارات
    // TextColumn في LeadResource ومديري العلاقات لديه.  حيث يرتبط العمود
    // بـ enum مكتوب (مثل App\Enums\LeadStatus)، تأخذ طريقة label() الخاصة
    // بـ enum الأولوية وتُستخدم هذه المفاتيح كاحتياطي للسلسلة الخام
    // لقيم قاعدة البيانات القديمة التي تتجاوز التحويل.

    // حالة العميل المحتمل — تعكس enums/lead_status.php بالإضافة إلى الاسم
    // القديم 'converted' المحفوظ بواسطة docblock للـ enum H7.
    'status_new'                    => 'جديد',
    'status_contacted'              => 'تم التواصل',
    'status_qualified'              => 'مؤهَّل',
    'status_won'                    => 'فائز',
    'status_converted'              => 'تم التحويل',
    'status_lost'                   => 'خاسر',

    // شارات الاتجاه (Messages relation manager).  محايدة من حيث العلامة التجارية.
    'direction_inbound'             => 'وارد',
    'direction_outbound'            => 'صادر',

    // شارات اتجاه البريد — تُحفظ منفصلة عن اتجاه الرسالة ليتمكن المترجمون
    // من تعديل الصياغة حسب القناة دون اقتران.
    'email_direction_inbound'       => 'وارد',
    'email_direction_outbound'      => 'صادر',

    // شارات حالة الرسالة — بادئة مفتاح مختلفة عن 'status_*' العام
    // للحفاظ على ترجمات MessagesRelationManager مستقلة عن
    // مفاتيح حالة العميل المحتمل.
    'message_status_sent'           => 'مُرسَل',
    'message_status_delivered'      => 'تم التسليم',
    'message_status_read'           => 'مقروءة',
    'message_status_failed'         => 'فشلت',

    // شارات أولوية المهمة (TasksRelationManager).
    'task_priority_urgent'          => 'عاجلة',
    'task_priority_high'            => 'عالية',
    'task_priority_normal'          => 'عادية',
    'task_priority_low'             => 'منخفضة',

    // شارات حالة التسجيل في تسلسل البريد الإلكتروني.
    'enrollment_status_active'      => 'نشط',
    'enrollment_status_completed'   => 'مكتمل',
    'enrollment_status_replied'     => 'تم الرد',
    'enrollment_status_unenrolled'  => 'مُلغى التسجيل',

    // تسميات إخفاء هوية GDPR المكتوبة في صفوف قاعدة البيانات أثناء المحو
    // بحيث تطابق العناصر النائبة اللغة النشطة للمشغل في لحظة
    // إخفاء الهوية.
    'gdpr_anonymous'                => 'مجهول',
    'gdpr_task_label'               => 'مهمة رقم :id',

    // Phase 1 lead-funnel fields
    'city' => 'المدينة',
    'assigned_team' => 'الفريق المسؤول',
    'assigned_team_help' => 'الفريق المالك لهذا العميل المحتمل، بغض النظر عن المندوب الحالي.',
    'next_follow_up' => 'المتابعة التالية',
    'meeting_generic_label' => 'اجتماع',
    'section_history' => 'سجل المرحلة والملكية',
    'history_stage' => 'تغييرات المرحلة',
    'history_assignment' => 'تغييرات الملكية',
    'history_none' => 'لا شيء',
    'history_unassigned' => 'غير مُسند',
    'history_system' => 'النظام',
];
