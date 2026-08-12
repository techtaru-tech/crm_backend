<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| QuoteResource — Filament admin strings
|------------------------------------------------------------
| Accessed via __('filament/quotes.<key>').
*/

return [

    // ----- Model labels -----
    'model_label'        => 'عرض سعر',
    'plural_model_label' => 'عروض الأسعار',

    // ----- Navigation -----
    'nav_label'        => 'عروض الأسعار',
    'tabs_outer'       => 'عرض سعر',

    // ----- Filter labels -----
    'filter_label_status' => 'الحالة',

    // ----- Tabs -----
    'tab_info'         => 'المعلومات',
    'tab_line_items'   => 'البنود',
    'tab_totals'       => 'الإجماليات',

    // ----- Info -----
    'title'            => 'العنوان',
    // Default placeholder pre-filled in the "Title" field when an
    // operator clicks "Create Quote" from a lead view (CreateQuote
    // page sets this via __()).
    'new_quote_default_title' => 'عرض سعر جديد',
    'lead'             => 'العميل المحتمل',
    'company'          => 'الشركة',
    'currency'         => 'العملة',
    'valid_until'      => 'صالح حتى',
    'introduction'     => 'مقدمة',
    'terms'            => 'الشروط والأحكام',

    // ----- Items -----
    'items_description' => 'المنتجات أو الخدمات المضمّنة في هذا العرض.',
    'add_item'         => 'إضافة بند',
    'product'          => 'المنتج',
    'name'             => 'الاسم',
    'unit_price'       => 'سعر الوحدة',
    'discount_percent' => 'نسبة الخصم %',
    'line_total'       => 'إجمالي البند',
    'line_total_placeholder' => 'تلقائي',
    'line_item_default_label' => 'بند جديد',

    // ----- Totals -----
    'subtotal'         => 'المجموع الفرعي',
    'tax_rate'         => 'نسبة الضريبة',
    'tax_amount'       => 'مبلغ الضريبة',
    'additional_discount' => 'خصم إضافي',
    'total'            => 'الإجمالي',

    // ----- Table -----
    'col_number'       => 'الرقم',
    'col_lead'         => 'العميل المحتمل',
    'col_valid_until'  => 'صالح حتى',
    'col_created'      => 'تاريخ الإنشاء',

    // ----- Row actions -----
    'duplicate'        => 'تكرار',
    'send'             => 'إرسال',
    'send_modal_heading' => 'إرسال عرض السعر إلى العميل المحتمل بالبريد',
    'send_modal_description' => 'يرسل رابطًا إلى :recipient لعرض هذا العرض وتوقيعه وقبوله.',
    'send_modal_recipient_fallback' => 'العميل المحتمل',
    'download_pdf'     => 'تحميل PDF',
    'convert_to_invoice' => 'تحويل إلى فاتورة',
    'more'             => 'المزيد',

    // ----- Sub-page actions -----
    'send_for_signature' => 'إرسال للتوقيع',
    'preview'          => 'معاينة',
    'public_link'      => 'الرابط العام',
    'cancel'           => 'إلغاء',

    // ----- Notifications -----
    'notif_duplicated'  => 'تم تكرار عرض السعر.',
    'notif_lead_no_email' => 'العميل المحتمل ليس لديه بريد إلكتروني.',
    'notif_sent'        => 'تم إرسال عرض السعر.',
    'notif_send_failed' => 'فشل الإرسال: :error',
    'notif_invoice_created' => 'تم إنشاء الفاتورة :number.',
    'notif_signature_sent' => 'تم إرسال رابط التوقيع.',
    'notif_signature_failed' => 'فشل: :error',
    'notif_cancelled'   => 'تم إلغاء عرض السعر.',
    'notif_saved'       => 'تم حفظ عرض السعر.',

    // ─── Select options ────────────────────────────────────────────
    'option_status_draft'     => 'مسودة',
    'option_status_sent'      => 'مُرسَل',
    'option_status_accepted'  => 'مقبول',
    'option_status_declined'  => 'مرفوض',
    'option_status_expired'   => 'منتهٍ',
    'option_status_converted' => 'محوَّل',

    // ─── Form field labels ─────────────────────────────────────────
    'field_item_description_label' => 'الوصف',
    'field_item_quantity_label'    => 'الكمية',

    // ─── Table column labels ───────────────────────────────────────
    'col_title'        => 'العنوان',
    'col_total'        => 'الإجمالي',
    'col_status'       => 'الحالة',

    // ─── Status badge labels (table column display) ────────────────
    'status_draft'     => 'مسودة',
    'status_sent'      => 'مُرسَل',
    'status_accepted'  => 'مقبول',
    'status_declined'  => 'مرفوض',
    'status_expired'   => 'منتهٍ',
    'status_converted' => 'محوَّل',

    // ─── Duplicate action: suffix appended to the duplicated title ─
    'duplicate_suffix' => '(نسخة)',

    // ─── Decline reasons (written to quotes.decline_reason column) ─
    'decline_reason_cancelled_by_sender' => 'تم الإلغاء بواسطة المرسل',

];
